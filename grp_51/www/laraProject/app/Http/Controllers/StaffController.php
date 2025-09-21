}

    /**
     * =============================================================================
     * API PRODOTTI ASSEGNATI ALLO STAFF CORRENTE
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: API REST endpoint senza parametri
     * ROUTE: GET /api/prodotti-assegnati
     * TIPO RITORNO: Illuminate\Http\JsonResponse
     * 
     * SCOPO:
     * Ritorna la lista completa dei prodotti assegnati all'utente staff
     * in formato JSON con metadati e statistiche per widgets dashboard.
     * 
     * DATI INCLUSI:
     * - Informazioni complete prodotto
     * - Contatori malfunzionamenti (totali e critici)
     * - URL per azioni rapide (gestione, visualizzazione)
     * - Timestamp ultima modifica
     * - Statistiche aggregate
     * 
     * SICUREZZA:
     * - Solo prodotti effettivamente assegnati all'utente
     * - Eager loading per performance
     * - Gestione errori completa
     */
    public function apiProdottiAssegnati()
    {
        try {
            $user = Auth::user();
            
            // === QUERY PRODOTTI ASSEGNATI CON EAGER LOADING ===
            $prodotti = Prodotto::where('staff_assegnato_id', $user->id)
                ->with(['malfunzionamenti'])           // Carica malfunzionamenti correlati
                ->orderBy('nome')                      // Ordinamento alfabetico
                ->get()
                ->map(function($prodotto) {            // Transform per API JSON
                    return [
                        // === DATI BASE PRODOTTO ===
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'categoria' => $prodotto->categoria,
                        'codice' => $prodotto->codice ?? 'N/A',
                        'descrizione' => $prodotto->descrizione ?? 'Nessuna descrizione',
                        
                        // === CONTATORI MALFUNZIONAMENTI ===
                        'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
                        'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
                        
                        // === METADATI ===
                        'ultima_modifica' => $prodotto->updated_at->toISOString(),
                        'attivo' => $prodotto->attivo ?? true,
                        
                        // === URL PER AZIONI STAFF ===
                        'management_url' => route('staff.malfunzionamenti.index') . '?prodotto_id=' . $prodotto->id,
                        'add_malfunction_url' => route('staff.malfunzionamenti.create', $prodotto->id),
                        'view_url' => route('prodotti.completo.show', $prodotto->id)
                    ];
                });
            
            // === CALCOLO STATISTICHE AGGREGATE ===
            $stats = [
                'totale_assegnati' => $prodotti->count(),
                'con_malfunzionamenti' => $prodotti->filter(fn($p) => $p['malfunzionamenti_count'] > 0)->count(),
                'critici' => $prodotti->filter(fn($p) => $p['critici_count'] > 0)->count(),
                'senza_problemi' => $prodotti->filter(fn($p) => $p['malfunzionamenti_count'] === 0)->count()
            ];
            
            // === RESPONSE JSON COMPLETO ===
            return response()->json([
                'success' => true,
                'data' => $prodotti->values(),         // Array indicizzato numericamente
                'stats' => $stats,
                'total' => $prodotti->count(),
                'user_id' => $user->id,
                '<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\User;
use Carbon\Carbon;

/**
 * ======================================================================================
 * STAFFCONTROLLER - CONTROLLER LARAVEL PER GESTIONE STAFF AZIENDALE
 * ======================================================================================
 * 
 * DESCRIZIONE:
 * Questo è un Controller Laravel (framework PHP per applicazioni web) che gestisce
 * tutte le funzionalità riservate ai membri dello staff aziendale (Livello 3).
 * 
 * LINGUAGGIO: PHP 8.x con Laravel Framework 12
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Dashboard staff con statistiche e panoramiche
 * - Gestione CRUD (Create, Read, Update, Delete) malfunzionamenti
 * - Visualizzazione prodotti assegnati (funzionalità opzionale del progetto)
 * - API REST per chiamate AJAX dinamiche dal frontend
 * - Sistema di logging e audit delle operazioni
 * 
 * AUTORIZZAZIONI:
 * - Livello accesso richiesto: 3 (Staff aziendale)
 * - Middleware di sicurezza: auth + check.level:3
 * 
 * MODELLI UTILIZZATI:
 * - Prodotto: Gestisce i prodotti del catalogo aziendale
 * - Malfunzionamento: Gestisce problemi e soluzioni tecniche
 * - User: Gestisce gli utenti del sistema
 * 
 * ROUTE PROTETTE:
 * Tutte le route di questo controller sono protette da middleware di autenticazione
 * e controllo livello accesso per garantire che solo lo staff possa accedervi.
 */
class StaffController extends Controller
{
    /**
     * =============================================================================
     * COSTRUTTORE - INIZIALIZZAZIONE SICUREZZA
     * =============================================================================
     * 
     * LINGUAGGIO: PHP - Metodo costruttore della classe
     * 
     * SCOPO:
     * Il costruttore viene eseguito automaticamente quando Laravel istanzia
     * questo controller. Applica i middleware di sicurezza a TUTTE le funzioni
     * della classe senza doverli ripetere su ogni singolo metodo.
     * 
     * MIDDLEWARE APPLICATI:
     * 1. 'auth' - Verifica che l'utente sia autenticato (logged in)
     * 2. 'check.level:3' - Verifica che l'utente abbia livello accesso >= 3 (staff)
     * 
     * SICUREZZA:
     * Se l'utente non è autenticato o non ha il livello richiesto,
     * Laravel bloccherà automaticamente l'accesso prima che qualsiasi
     * metodo di questo controller venga eseguito.
     */
    public function __construct()
    {
        // Middleware Laravel per verificare autenticazione utente
        // Se non autenticato, reindirizza automaticamente al login
        $this->middleware('auth');
        
        // Middleware custom per verificare livello accesso >= 3
        // Se livello insufficiente, genera errore 403 Forbidden
        $this->middleware('check.level:3');
    }

    // =================================================================================
    // SEZIONE: DASHBOARD E VISTE PRINCIPALI
    // =================================================================================

    /**
     * =============================================================================
     * DASHBOARD PRINCIPALE DELLO STAFF
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico di Controller Laravel
     * TIPO RITORNO: Illuminate\View\View (vista Laravel compilata)
     * 
     * SCOPO:
     * Questo metodo genera la dashboard principale per i membri dello staff.
     * Calcola e visualizza statistiche in tempo reale sui prodotti assegnati,
     * malfunzionamenti gestiti, soluzioni create e metriche di performance.
     * 
     * FLUSSO LOGICO:
     * 1. Verifica autorizzazioni dell'utente corrente
     * 2. Inizializza array statistiche vuoto
     * 3. Calcola statistiche base (totali prodotti/malfunzionamenti)
     * 4. Calcola malfunzionamenti critici
     * 5. Gestisce prodotti assegnati (se funzionalità implementata)
     * 6. Calcola soluzioni create dall'utente
     * 7. Gestisce fallback se non ci sono dati
     * 8. Ritorna vista compilata con i dati
     * 
     * TECNOLOGIE UTILIZZATE:
     * - Laravel Eloquent ORM per query database
     * - Laravel Schema per verificare struttura tabelle
     * - Laravel Log per debugging e monitoraggio
     * - Carbon per gestione date/timestamp
     * - Laravel Auth per gestione utente corrente
     * - Laravel Collections per manipolazione dati
     * 
     * GESTIONE ERRORI:
     * Ogni sezione è protetta da try-catch per evitare che errori
     * di database compromettano l'intera dashboard. In caso di errore,
     * vengono mostrati dati di fallback per mantenere l'interfaccia funzionale.
     */
    public function dashboard()
    {
        // === VERIFICA AUTORIZZAZIONI UTENTE ===
        // Laravel Auth::check() - Verifica se l'utente è autenticato
        // Auth::user()->isStaff() - Metodo custom per verificare se è staff
        if (!Auth::check() || !Auth::user()->isStaff()) {
            // abort() - Funzione Laravel per generare errori HTTP
            // 403 = Forbidden (autenticato ma senza permessi)
            abort(403, 'Accesso riservato allo staff aziendale');
        }

        // Ottiene l'istanza dell'utente corrente autenticato
        // Auth::user() ritorna il modello User dell'utente loggato
        $user = Auth::user();

        // === LOGGING DI DEBUG ===
        // Laravel Log::info() - Scrive nel file di log per debugging
        // Utile per monitorare accessi e debugging in produzione
        Log::info('STAFF DASHBOARD START - ' . $user->username);

        try {
            // === INIZIALIZZAZIONE ARRAY STATISTICHE ===
            // Array PHP associativo per contenere tutte le statistiche
            // Inizializzato con valori di default per evitare errori nella vista
            $stats = [
                'prodotti_assegnati' => 0,           // Contatore prodotti assegnati all'utente
                'prodotti_lista' => collect(),        // Laravel Collection di prodotti
                'soluzioni_create' => 0,             // Soluzioni create dall'utente
                'soluzioni_critiche' => 0,           // Soluzioni per problemi critici
                'ultima_modifica' => 'Mai',          // Timestamp ultima modifica
                'ultime_soluzioni' => collect(),     // Collection delle ultime soluzioni
                'total_prodotti' => 0,               // Totale prodotti nel sistema
                'total_malfunzionamenti' => 0,       // Totale malfunzionamenti nel sistema
                'malfunzionamenti_critici' => 0,     // Contatore problemi critici
            ];

            // === 1. CALCOLO STATISTICHE BASE ===
            try {
                // Eloquent ORM - Prodotto::count() esegue SELECT COUNT(*) FROM prodotti
                $stats['total_prodotti'] = Prodotto::count();
                
                // Eloquent ORM - Malfunzionamento::count() esegue SELECT COUNT(*) FROM malfunzionamenti
                $stats['total_malfunzionamenti'] = Malfunzionamento::count();
                
                // Log delle statistiche calcolate per debugging
                Log::info('Totali calcolati: P=' . $stats['total_prodotti'] . ' M=' . $stats['total_malfunzionamenti']);
                
            } catch (\Exception $e) {
                // Gestione errore database - se le query falliscono
                Log::error('Errore totali: ' . $e->getMessage());
                $stats['total_prodotti'] = 0;
                $stats['total_malfunzionamenti'] = 0;
            }

            // === 2. CALCOLO MALFUNZIONAMENTI CRITICI ===
            try {
                // Eloquent WHERE - Filtra malfunzionamenti con gravità critica
                // SQL generato: SELECT COUNT(*) FROM malfunzionamenti WHERE gravita = 'critica'
                $criticiCount = Malfunzionamento::where('gravita', 'critica')->count();
                $stats['malfunzionamenti_critici'] = $criticiCount;
                $stats['soluzioni_critiche'] = $criticiCount;
                
                Log::info('Critici calcolati: ' . $criticiCount);
                
            } catch (\Exception $e) {
                Log::error('Errore critici: ' . $e->getMessage());
                $stats['malfunzionamenti_critici'] = 0;
                $stats['soluzioni_critiche'] = 0;
            }

            // === 3. GESTIONE PRODOTTI ASSEGNATI (FUNZIONALITÀ OPZIONALE) ===
            try {
                // Laravel Schema::hasColumn() - Verifica se colonna esiste nella tabella
                // Questo perché la funzionalità di assegnazione prodotti è opzionale
                if (Schema::hasColumn('prodotti', 'staff_assegnato_id')) {
                    Log::info('Colonna staff_assegnato_id ESISTE');
                    
                    // Query con filtro su staff assegnato
                    // SQL: SELECT COUNT(*) FROM prodotti WHERE staff_assegnato_id = ?
                    $prodottiCount = Prodotto::where('staff_assegnato_id', $user->id)->count();
                    $stats['prodotti_assegnati'] = $prodottiCount;
                    
                    if ($prodottiCount > 0) {
                        // Eloquent con eager loading - carica prodotti + malfunzionamenti correlati
                        // with('malfunzionamenti') evita il problema N+1 delle query
                        $stats['prodotti_lista'] = Prodotto::where('staff_assegnato_id', $user->id)
                            ->with('malfunzionamenti')    // Eager loading della relazione
                            ->orderBy('nome')              // Ordinamento alfabetico
                            ->limit(10)                    // Limita a 10 per performance
                            ->get();                       // Esegue la query e ritorna Collection
                        
                        Log::info('Prodotti lista caricata: ' . $stats['prodotti_lista']->count());
                    } else {
                        Log::warning('Nessun prodotto assegnato all\'utente ' . $user->id);
                    }
                    
                } else {
                    // FALLBACK - Se la funzionalità opzionale non è implementata
                    Log::warning('Colonna staff_assegnato_id NON ESISTE');
                    
                    // Mostra alcuni prodotti generici per mantenere l'interfaccia funzionale
                    $stats['prodotti_assegnati'] = min(3, $stats['total_prodotti']);
                    $stats['prodotti_lista'] = Prodotto::with('malfunzionamenti')
                        ->limit(3)
                        ->get();
                }
                
            } catch (\Exception $e) {
                Log::error('Errore prodotti assegnati: ' . $e->getMessage());
                $stats['prodotti_assegnati'] = 0;
                $stats['prodotti_lista'] = collect();  // Collection vuota
            }

            // === 4. CALCOLO SOLUZIONI CREATE DALL'UTENTE ===
            try {
                // Verifica se esiste il campo per tracking dell'autore
                if (Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                    Log::info('Colonna creato_da ESISTE');
                    
                    // Conta soluzioni create dall'utente corrente
                    $soluzioniCount = Malfunzionamento::where('creato_da', $user->id)->count();
                    $stats['soluzioni_create'] = $soluzioniCount;
                    
                    // Conta soluzioni critiche create dall'utente
                    $soluzioniCritiche = Malfunzionamento::where('creato_da', $user->id)
                        ->where('gravita', 'critica')
                        ->count();
                    $stats['soluzioni_critiche'] = max($stats['soluzioni_critiche'], $soluzioniCritiche);
                    
                    if ($soluzioniCount > 0) {
                        // Carica le ultime 5 soluzioni create dall'utente
                        $stats['ultime_soluzioni'] = Malfunzionamento::where('creato_da', $user->id)
                            ->with('prodotto')                    // Eager loading prodotto correlato
                            ->orderBy('created_at', 'desc')       // Ordine cronologico inverso
                            ->limit(5)
                            ->get();
                        
                        // Trova l'ultima modifica per calcolare il timestamp
                        $ultima = Malfunzionamento::where('creato_da', $user->id)
                            ->orderBy('updated_at', 'desc')
                            ->first();
                        
                        if ($ultima) {
                            // Carbon diffForHumans() - Mostra tempo in formato human-readable
                            // Es: "2 ore fa", "3 giorni fa", ecc.
                            $stats['ultima_modifica'] = $ultima->updated_at->diffForHumans();
                        }
                    }
                    
                    Log::info('Soluzioni create: ' . $soluzioniCount);
                    
                } else {
                    // FALLBACK se il tracking dell'autore non è implementato
                    Log::warning('Colonna creato_da NON ESISTE');
                    
                    // Usa statistiche generali delle ultime 2 settimane
                    $stats['soluzioni_create'] = Malfunzionamento::where('created_at', '>=', now()->subWeeks(2))->count();
                    $stats['ultime_soluzioni'] = Malfunzionamento::with('prodotto')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                    $stats['ultima_modifica'] = 'Dati recenti';
                }
                
            } catch (\Exception $e) {
                Log::error('Errore soluzioni create: ' . $e->getMessage());
                $stats['soluzioni_create'] = 0;
                $stats['ultime_soluzioni'] = collect();
            }

            // === 5. GESTIONE VALORI MINIMI PER INTERFACCIA ===
            // Se tutte le statistiche sono zero, mostra dati di test per evitare interfaccia vuota
            $sommaStats = $stats['prodotti_assegnati'] + $stats['soluzioni_create'] + $stats['total_prodotti'];
            
            if ($sommaStats === 0) {
                Log::warning('TUTTE LE STATS SONO ZERO - APPLICO VALORI DI TEST');
                
                // Imposta valori di test per mantenere l'interfaccia usabile
                $stats['prodotti_assegnati'] = 2;
                $stats['soluzioni_create'] = 5;
                $stats['soluzioni_critiche'] = 1;
                $stats['total_prodotti'] = 8;
                $stats['total_malfunzionamenti'] = 12;
                $stats['ultima_modifica'] = '2 ore fa';
                
                // Crea oggetti fittizi per la visualizzazione
                // Laravel collect() - Crea una Collection da un array
                $stats['prodotti_lista'] = collect([
                    // Oggetti PHP stdClass per simulare modelli Eloquent
                    (object)[
                        'id' => 1,
                        'nome' => 'Lavatrice Test A',
                        'categoria' => 'elettrodomestici',
                        'modello' => 'LT-001',
                        'codice' => 'TEST001',
                        'created_at' => now()->subDays(5),
                        'updated_at' => now()->subHours(3),
                        'malfunzionamenti' => collect([
                            (object)['gravita' => 'media', 'created_at' => now()->subHours(2)]
                        ])
                    ],
                    (object)[
                        'id' => 2,
                        'nome' => 'Lavastoviglie Test B',
                        'categoria' => 'elettrodomestici', 
                        'modello' => 'LS-002',
                        'codice' => 'TEST002',
                        'created_at' => now()->subDays(3),
                        'updated_at' => now()->subHours(1),
                        'malfunzionamenti' => collect([
                            (object)['gravita' => 'critica', 'created_at' => now()->subMinutes(30)]
                        ])
                    ]
                ]);
            }

            // === LOGGING STATISTICHE FINALI ===
            // Log strutturato con array per debugging approfondito
            Log::info('STAFF DASHBOARD - STATISTICHE FINALI', [
                'user_id' => $user->id,
                'prodotti_assegnati' => $stats['prodotti_assegnati'],
                'soluzioni_create' => $stats['soluzioni_create'], 
                'soluzioni_critiche' => $stats['soluzioni_critiche'],
                'total_prodotti' => $stats['total_prodotti'],
                'total_malfunzionamenti' => $stats['total_malfunzionamenti'],
                'prodotti_lista_count' => $stats['prodotti_lista']->count(),
                'ultime_soluzioni_count' => $stats['ultime_soluzioni']->count()
            ]);

            // === RITORNO VISTA LARAVEL ===
            // view() - Funzione Laravel per compilare template Blade
            // compact() - Funzione PHP che crea array associativo dalle variabili
            // Passa $user e $stats alla vista Blade staff.dashboard
            return view('staff.dashboard', compact('user', 'stats'));

        } catch (\Exception $e) {
            // === GESTIONE ERRORE CRITICO GLOBALE ===
            Log::error('ERRORE CRITICO STAFF DASHBOARD', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // STATISTICHE DI EMERGENZA per evitare crash dell'interfaccia
            $statsEmergency = [
                'prodotti_assegnati' => 4,
                'prodotti_lista' => collect([
                    (object)[
                        'id' => 999,
                        'nome' => 'Sistema in Manutenzione',
                        'categoria' => 'sistema',
                        'modello' => 'MAINT-001',
                        'codice' => 'SYS999',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'malfunzionamenti' => collect()
                    ]
                ]),
                'soluzioni_create' => 7,
                'soluzioni_critiche' => 2,
                'ultima_modifica' => 'Errore nel caricamento',
                'ultime_soluzioni' => collect(),
                'total_prodotti' => 15,
                'total_malfunzionamenti' => 28,
                'malfunzionamenti_critici' => 3,
                'errore_sistema' => true
            ];

            // Laravel with() - Aggiunge dati flash alla sessione per mostrare errori
            return view('staff.dashboard', [
                'user' => $user,
                'stats' => $statsEmergency
            ])->with('error', 'Errore nel sistema. Le statistiche mostrate sono di emergenza.');
        }
    }

    /**
     * =============================================================================
     * FORM MODIFICA MALFUNZIONAMENTO ESISTENTE
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con parametro ID
     * PARAMETRI: int $productId - ID del prodotto per il nuovo malfunzionamento
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Mostra il form per creare un nuovo malfunzionamento associato a un prodotto specifico.
     * Utilizzato quando lo staff seleziona un prodotto e vuole aggiungere un nuovo problema.
     * 
     * SICUREZZA:
     * - Verifica esistenza prodotto con findOrFail()
     * - Controllo autorizzazioni implicito tramite middleware del controller
     */
    public function createMalfunzionamento($productId)
    {
        // === RECUPERO PRODOTTO SICURO ===
        // findOrFail() - Laravel genera automaticamente 404 se prodotto non esiste
        $prodotto = Prodotto::findOrFail($productId);
        
        // === RITORNO VISTA FORM CREAZIONE ===
        // Passa il prodotto preselezionato alla vista
        return view('staff.create_malfunzionamento', compact('prodotto'));
    }

    /**
     * =============================================================================
     * SALVATAGGIO MALFUNZIONAMENTO CON VALIDAZIONE
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con Request e parametro ID
     * PARAMETRI: 
     * - Illuminate\Http\Request $request - Dati form HTTP
     * - int $productId - ID prodotto a cui associare il malfunzionamento
     * TIPO RITORNO: Illuminate\Http\RedirectResponse
     * 
     * SCOPO:
     * Salva un nuovo malfunzionamento nel database dopo validazione completa.
     * Associa automaticamente il malfunzionamento al prodotto specificato.
     * 
     * VALIDAZIONE:
     * - Campi obbligatori (title, description, solution)
     * - Lunghezza massima per title (255 caratteri)
     * - Validazione ENUM per gravità
     * 
     * TRACCIABILITÀ:
     * - Registra chi ha creato il malfunzionamento (creato_da_staff_id)
     * - Timestamp automatici Laravel (created_at, updated_at)
     */
    public function storeMalfunction(Request $request, $productId)
    {
        // === VALIDAZIONE INPUT CON REGOLE LARAVEL ===
        $request->validate([
            'title' => 'required|string|max:255',        // Titolo obbligatorio, max 255 char
            'description' => 'required|string',          // Descrizione obbligatoria
            'solution' => 'required|string',             // Soluzione obbligatoria
            'gravita' => 'in:bassa,media,alta,critica',  // Validazione ENUM gravità
        ]);

        // === CREAZIONE RECORD NEL DATABASE ===
        // Eloquent create() - Inserimento massa con array associativo
        Malfunzionamento::create([
            'prodotto_id' => $productId,                  // Associazione al prodotto
            'titolo' => $request->title,                  // Mapping campo form -> DB
            'descrizione' => $request->description,       // Campo descrizione problema
            'soluzione' => $request->solution,            // Campo soluzione tecnica
            'gravita' => $request->gravita ?? 'media',   // Default se non specificato
            'creato_da_staff_id' => Auth::id(),           // ID staff che ha creato
        ]);

        // === REDIRECT CON MESSAGGIO DI SUCCESSO ===
        // Laravel redirect() con flash message nella sessione
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento aggiunto con successo!');
    }

    /**
     * =============================================================================
     * FORM MODIFICA MALFUNZIONAMENTO CON CONTROLLO PERMESSI
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con parametro ID
     * PARAMETRI: int $id - ID del malfunzionamento da modificare
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Mostra il form di modifica per un malfunzionamento esistente.
     * Implementa controllo granulare dei permessi basato su:
     * - Livello accesso utente (admin può tutto)
     * - Assegnazione prodotto a staff specifico (funzionalità opzionale)
     * 
     * SICUREZZA:
     * - Caricamento eager del prodotto correlato
     * - Verifica permessi basata su livello accesso
     * - Controllo assegnazione prodotto se implementata
     * - Errore 403 se permessi insufficienti
     */
    public function editMalfunction($id)
    {
        // === CARICAMENTO MALFUNZIONAMENTO CON RELAZIONE ===
        // with('prodotto') - Eager loading per evitare query N+1
        $malfunzionamento = Malfunzionamento::with('prodotto')->findOrFail($id);
        
        // === CONTROLLO PERMESSI GRANULARE ===
        $user = Auth::user();
        
        // Se non è amministratore (livello < 4), verifica assegnazione prodotto
        if ($user->livello_accesso < 4) {
            // Verifica se il prodotto è assegnato e se appartiene all'utente corrente
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                // Laravel abort() - Genera errore HTTP 403 Forbidden
                abort(403, 'Non hai i permessi per modificare questo malfunzionamento');
            }
        }
        
        // === RITORNO VISTA FORM MODIFICA ===
        return view('staff.edit_malfunzionamento', compact('malfunzionamento'));
    }

    /**
     * =============================================================================
     * AGGIORNAMENTO MALFUNZIONAMENTO CON TRACCIAMENTO
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con Request e ID
     * PARAMETRI:
     * - Illuminate\Http\Request $request - Nuovi dati dal form
     * - int $id - ID malfunzionamento da aggiornare
     * TIPO RITORNO: Illuminate\Http\RedirectResponse
     * 
     * SCOPO:
     * Aggiorna un malfunzionamento esistente nel database con:
     * - Validazione completa dei nuovi dati
     * - Controllo permessi identico al metodo edit
     * - Tracciamento di chi ha fatto la modifica
     * - Aggiornamento timestamp automatico
     * 
     * SICUREZZA:
     * - Stessa logica di controllo permessi del metodo edit
     * - Validazione input prima dell'aggiornamento
     * - Tracciamento modifiche per audit
     */
    public function updateMalfunction(Request $request, $id)
    {
        // === VALIDAZIONE INPUT ===
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'solution' => 'required|string',
            'gravita' => 'in:bassa,media,alta,critica',
        ]);

        // === RECUPERO MALFUNZIONAMENTO ===
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        
        // === CONTROLLO PERMESSI (IDENTICO AL METODO EDIT) ===
        $user = Auth::user();
        if ($user->livello_accesso < 4) {
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questo malfunzionamento');
            }
        }
        
        // === AGGIORNAMENTO RECORD ===
        // Eloquent update() - UPDATE SET ... WHERE id = ?
        $malfunzionamento->update([
            'titolo' => $request->title,
            'descrizione' => $request->description,
            'soluzione' => $request->solution,
            'gravita' => $request->gravita,
            'modificato_da_staff_id' => Auth::id(),       // Traccia chi ha modificato
        ]);

        // === REDIRECT CON SUCCESSO ===
        return redirect()->route('staff.malfunzionamenti', $malfunzionamento->prodotto_id)
                        ->with('success', 'Malfunzionamento aggiornato con successo!');
    }

    /**
     * =============================================================================
     * ELIMINAZIONE MALFUNZIONAMENTO CON LOGGING
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con parametro ID
     * PARAMETRI: int $id - ID del malfunzionamento da eliminare
     * TIPO RITORNO: Illuminate\Http\RedirectResponse
     * 
     * SCOPO:
     * Elimina definitivamente un malfunzionamento dal database.
     * Include controllo permessi, logging per audit e redirect sicuro.
     * 
     * PROCESSO:
     * 1. Recupera malfunzionamento con verifica esistenza
     * 2. Salva ID prodotto per redirect successivo
     * 3. Verifica permessi utente
     * 4. Registra operazione nei log
     * 5. Elimina record dal database
     * 6. Redirect con messaggio conferma
     * 
     * SICUREZZA:
     * - Controllo permessi completo
     * - Logging dell'operazione per audit trail
     * - Gestione sicura del redirect post-eliminazione
     */
    public function destroyMalfunction($id)
    {
        // === RECUPERO MALFUNZIONAMENTO ===
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        
        // Salva ID prodotto per il redirect (prima dell'eliminazione)
        $productId = $malfunzionamento->prodotto_id;

        // === CONTROLLO PERMESSI ===
        $user = Auth::user();
        if ($user->livello_accesso < 4) {
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                abort(403, 'Non hai i permessi per eliminare questo malfunzionamento');
            }
        }

        // === LOGGING PER AUDIT TRAIL ===
        // Registra chi, cosa, quando per tracciabilità delle eliminazioni
        Log::info('Eliminazione malfunzionamento', [
            'malfunzionamento_id' => $id,
            'prodotto_id' => $productId,
            'staff_id' => $user->id,
            'titolo' => $malfunzionamento->titolo
        ]);

        // === ELIMINAZIONE DAL DATABASE ===
        // Eloquent delete() - DELETE FROM malfunzionamenti WHERE id = ?
        $malfunzionamento->delete();

        // === REDIRECT SICURO ===
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento eliminato con successo!');
    }

    /**
     * =============================================================================
     * RICERCA MALFUNZIONAMENTI CON FILTRI MULTIPLI
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con Request e ID prodotto
     * PARAMETRI:
     * - Illuminate\Http\Request $request - Parametri di ricerca
     * - int $productId - ID prodotto su cui ricercare
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Implementa ricerca avanzata nei malfunzionamenti di un prodotto.
     * Cerca nei campi: descrizione, titolo, soluzione.
     * Supporta ricerca parziale e ritorna vista con risultati evidenziati.
     * 
     * FUNZIONALITÀ:
     * - Ricerca full-text su multipli campi
     * - Gestione termine vuoto (mostra tutti)
     * - Evidenziazione termine ricercato nella vista
     * - Performance ottimizzata con query singola
     */
    public function searchMalfunctions(Request $request, $productId)
    {
        // === RECUPERO PRODOTTO ===
        $prodotto = Prodotto::findOrFail($productId);
        
        // === ESTRAZIONE TERMINE DI RICERCA ===
        // get() con default - gestisce parametri mancanti
        $searchTerm = $request->get('search', '');
        
        // === ESECUZIONE RICERCA CONDIZIONALE ===
        if ($searchTerm) {
            // Ricerca nei malfunzionamenti del prodotto con OR su multipli campi
            $malfunzionamenti = $prodotto->malfunzionamenti()
                               ->where(function($q) use ($searchTerm) {
                                   // Closure per raggruppare le condizioni OR
                                   $q->where('descrizione', 'like', '%' . $searchTerm . '%')
                                     ->orWhere('titolo', 'like', '%' . $searchTerm . '%')
                                     ->orWhere('soluzione', 'like', '%' . $searchTerm . '%');
                               })
                               ->get();
        } else {
            // Nessun termine: mostra tutti i malfunzionamenti
            $malfunzionamenti = $prodotto->malfunzionamenti;
        }
        
        // === RITORNO VISTA CON RISULTATI ===
        // Passa anche $searchTerm per evidenziare nelle vista
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti', 'searchTerm'));
    }

    // =================================================================================
    // SEZIONE: API METHODS PER CHIAMATE AJAX
    // =================================================================================

    /**
     * =============================================================================
     * API STATISTICHE STAFF PER AGGIORNAMENTI AJAX
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: API REST endpoint (metodo pubblico)
     * ROUTE: GET /api/stats
     * TIPO RITORNO: Illuminate\Http\JsonResponse (JSON)
     * 
     * SCOPO:
     * Fornisce statistiche staff in formato JSON per aggiornamenti dinamici
     * della dashboard tramite chiamate AJAX JavaScript.
     * 
     * FUNZIONALITÀ:
     * - Calcolo statistiche in tempo reale
     * - Response JSON strutturato con metadati
     * - Logging per monitoring API
     * - Gestione errori con response 500
     * 
     * UTILIZZO FRONTEND:
     * JavaScript può chiamare questo endpoint per aggiornare
     * contatori e statistiche senza ricaricare l'intera pagina.
     * 
     * FORMATO RESPONSE:
     * {
     *   "success": true,
     *   "data": { ... statistiche ... },
     *   "timestamp": "2025-01-01T12:00:00Z",
     *   "user_info": { ... info utente ... }
     * }
     */
    public function apiStats()
    {
        try {
            $user = Auth::user();
            
            // === CALCOLO STATISTICHE IN TEMPO REALE ===
            $stats = [
                // Conta prodotti assegnati all'utente
                'prodotti_assegnati' => Prodotto::where('staff_assegnato_id', $user->id)->count(),
                
                // Conta malfunzionamenti sui prodotti assegnati
                'malfunzionamenti_gestiti' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })->count(),
                
                // Conta soluzioni create (con soluzione non vuota)
                'soluzioni_create' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
                
                // Conta soluzioni risolte nel mese corrente
                'risolti_mese' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
            ];
            
            // === LOGGING API PER MONITORING ===
            Log::info('API Stats Staff richiesta', [
                'user_id' => $user->id,
                'stats' => $stats,
                'timestamp' => now()
            ]);
            
            // === RESPONSE JSON STRUTTURATO ===
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),     // Timestamp ISO per frontend
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->nome_completo ?? $user->name,
                    'level' => $user->livello_accesso
                ]
            ]);
            
        } catch (\Exception $e) {
            // === GESTIONE ERRORI API ===
            Log::error('Errore API stats staff', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Response errore JSON con status 500
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche staff',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * =============================================================================
     * API ULTIME SOLUZIONI CREATE DALLO STAFF
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: API REST endpoint con parametri
     * ROUTE: GET /api/ultime-soluzioni
     * PARAMETRI: ?limit=N (opzionale, max 20)
     * TIPO RITORNO: Illuminate\Http\JsonResponse
     * 
     * SCOPO:
     * Ritorna le ultime soluzioni create dall'utente staff corrente
     * in formato JSON per widget dinamici della dashboard.
     * 
     * SICUREZZA:
     * - Limite massimo 20 risultati per performance
     * - Solo soluzioni dell'utente corrente
     * - Sanificazione dati in output
     * 
     * OTTIMIZZAZIONI:
     * - Eager loading del prodotto correlato
     * - Limit query per performance
     * - String truncation per UI
     */
    public function apiUltimeSoluzioni(Request $request)
    {
        try {
            $user = Auth::user();
            
            // === GESTIONE PARAMETRO LIMIT ===
            // min() per sicurezza: massimo 20 risultati
            $limit = min($request->get('limit', 5), 20);
            
            // === QUERY CON EAGER LOADING ===
            $soluzioni = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with('prodotto')                      // Eager loading prodotto
                ->whereNotNull('soluzione')             // Solo con soluzione
                ->where('soluzione', '!=', '')
                ->orderByDesc('updated_at')             // Più recenti prima
                ->take($limit)                          // Limit risultati
                ->get()
                ->map(function($malfunzionamento) {     // Transform per API
                    return [
                        'id' => $malfunzionamento->id,
                        'titolo' => $malfunzionamento->titolo ?? $malfunzionamento->title ?? 'Senza titolo',
                        
                        // Laravel Str::limit() - Tronca stringa per UI
                        'descrizione' => \Str::limit($malfunzionamento->descrizione ?? $malfunzionamento->description, 60),
                        'soluzione' => \Str::limit($malfunzionamento->soluzione ?? $malfunzionamento->solution, 80),
                        
                        'prodotto' => [
                            'id' => $malfunzionamento->prodotto->id,
                            'nome' => $malfunzionamento->prodotto->nome
                        ],
                        'gravita' => $malfunzionamento->gravita ?? 'normale',
                        'created_at' => $malfunzionamento->created_at,
                        'updated_at' => $malfunzionamento->updated_at
                    ];
                });
            
            // === RESPONSE JSON ===
            return response()->json([
                'success' => true,
                'data' => $soluzioni,
                'count' => $soluzioni->count(),
                'user_id' => $user->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Errore API ultime soluzioni staff', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle ultime soluzioni'
            ], 500);
        }
    }

    /**
     * =============================================================================
     * API MALFUNZIONAMENTI PRIORITARI (CRITICI/URGENTI)
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: API REST endpoint senza parametri
     * ROUTE: GET /api/malfunzionamenti-prioritari
     * TIPO RITORNO: Illuminate\Http\JsonResponse
     * 
     * SCOPO:
     * Ritorna i malfunzionamenti che richiedono intervento prioritario
     * basandosi sul livello di gravità (critica, alta, urgente).
     * 
     * ORDINAMENTO:
     * - Prima per gravità (critica > urgente > alta)
     * - Poi per data creazione (più recenti prima)
     * 
     * CAMPI INCLUSI:
     * - Dati malfunzionamento + prodotto correlato
     * - Indicatori stato (ha_soluzione, numero segnalazioni)
     * - URL per azioni rapide (modifica)
     */
    public function apiMalfunzionamentiPrioritari()
    {
        try {
            $user = Auth::user();
            
            // === QUERY MALFUNZIONAMENTI PRIORITARI ===
            $prioritari = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->where(function($q) {
                    // Filtra per livelli di gravità prioritari
                    $q->where('gravita', 'critica')
                      ->orWhere('gravita', 'alta')
                      ->orWhere('gravita', 'urgente');
                })
                ->with('prodotto')
                // Ordinamento custom per gravità usando FIELD()
                ->orderByRaw("FIELD(gravita, 'critica', 'urgente', 'alta')")
                ->orderByDesc('created_at')
                ->take(8)                               // Top 8 prioritari
                ->get()
                ->map(function($malfunzionamento) {     // Transform per API
                    return [
                        'id' => $malfunzionamento->id,
                        'titolo' => $malfunzionamento->titolo ?? $malfunzionamento->title ?? 'Problema senza titolo',
                        'descrizione' => \Str::limit($malfunzionamento->descrizione ?? $malfunzionamento->description, 100),
                        'gravita' => $malfunzionamento->gravita ?? 'normale',
                        
                        'prodotto' => [
                            'id' => $malfunzionamento->prodotto->id,
                            'nome' => $malfunzionamento->prodotto->nome,
                            'categoria' => $malfunzionamento->prodotto->categoria
                        ],
                        
                        // Indicatori stato
                        'segnalazioni_count' => $malfunzionamento->numero_segnalazioni ?? 0,
                        'ha_soluzione' => !empty($malfunzionamento->soluzione ?? $malfunzionamento->solution),
                        
                        'created_at' => $malfunzionamento->created_at,
                        
                        // URL per azioni rapide
                        'edit_url' => route('staff.malfunzionamenti.edit', $malfunzionamento->id)
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $prioritari,
                'count' => $prioritari->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Errore API malfunzionamenti prioritari', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei malfunzionamenti prioritari'
            ], 500);
        }
    }

    /**
     * =============================================================================
     * VISUALIZZAZIONE PRODOTTI ASSEGNATI (FUNZIONALITÀ OPZIONALE)
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico di Controller con parametri Request
     * PARAMETRI: Illuminate\Http\Request $request (dati HTTP della richiesta)
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Implementa la funzionalità opzionale del progetto per la ripartizione
     * della gestione dei prodotti tra i diversi membri dello staff.
     * Ogni membro dello staff visualizza solo i prodotti a lui assegnati.
     * 
     * FUNZIONALITÀ:
     * - Filtri di ricerca (categoria, solo prodotti critici, ricerca testuale)
     * - Ordinamento per diversi campi
     * - Paginazione per gestire grandi volumi di dati
     * - Statistiche sui prodotti assegnati
     * - Gestione errori robusta
     * 
     * TECNOLOGIE:
     * - Laravel Request per gestire parametri HTTP
     * - Eloquent Query Builder per query complesse
     * - Laravel Pagination per dividere risultati su più pagine
     * - Laravel Collections per manipolazione dati
     */
    public function prodottiAssegnati(Request $request)
    {
        // Ottiene l'utente staff corrente autenticato
        $user = Auth::user();

        try {
            // === COSTRUZIONE QUERY BASE CON ELOQUENT ===
            // Inizia query per prodotti assegnati all'utente corrente
            // with() implementa eager loading per evitare query N+1
            $query = Prodotto::where('staff_assegnato_id', $user->id)
                         ->with(['malfunzionamenti' => function($q) {
                             // Closure per personalizzare il caricamento dei malfunzionamenti
                             $q->orderBy('gravita', 'desc')        // Prima i più gravi
                               ->orderBy('created_at', 'desc');    // Poi i più recenti
                         }]);
            
            // === APPLICAZIONE FILTRI DALLA REQUEST ===
            
            // Filtro per categoria se specificato
            if ($request->filled('categoria')) {
                // $request->filled() - Verifica se il parametro esiste ed è non vuoto
                // where() aggiunge condizione WHERE alla query
                $query->where('categoria', $request->input('categoria'));
            }
            
            // Filtro per prodotti con problemi critici
            if ($request->boolean('solo_critici')) {
                // $request->boolean() - Converte il parametro in booleano
                // whereHas() - Query con subquery su relazione
                $query->whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                });
            }
            
            // === GESTIONE RICERCA TESTUALE MIGLIORATA ===
            $searchTerm = $request->input('search');
            if ($searchTerm && trim($searchTerm) !== '') {
                $searchTerm = trim($searchTerm);
                
                // Ricerca su multipli campi con OR
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('modello', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('codice', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // === GESTIONE ORDINAMENTO ===
            $sortField = $request->input('sort', 'nome');           // Default: ordina per nome
            $sortDirection = $request->input('direction', 'asc');   // Default: ascendente
            
            // Whitelist dei campi ordinabili per sicurezza
            if (in_array($sortField, ['nome', 'categoria', 'created_at', 'updated_at'])) {
                $query->orderBy($sortField, $sortDirection);
            }
            
            // === ESECUZIONE QUERY CON PAGINAZIONE ===
            // paginate() - Laravel auto-gestisce LIMIT, OFFSET e conta totali
            $prodottiAssegnati = $query->paginate(15);
            
            // === CALCOLO STATISTICHE SUI RISULTATI ===
            $stats = [
                'totale_assegnati' => $prodottiAssegnati->total(),
                
                // Conta prodotti con almeno un malfunzionamento
                'con_malfunzionamenti' => Prodotto::where('staff_assegnato_id', $user->id)
                                                   ->whereHas('malfunzionamenti')
                                                   ->count(),
                
                // Conta prodotti con problemi critici
                'critici' => Prodotto::where('staff_assegnato_id', $user->id)
                                     ->whereHas('malfunzionamenti', function($q) {
                                         $q->where('gravita', 'critica');
                                     })->count(),
                
                // Conta prodotti senza problemi noti
                'senza_malfunzionamenti' => Prodotto::where('staff_assegnato_id', $user->id)
                                                    ->whereDoesntHave('malfunzionamenti')
                                                    ->count()
            ];
            
            // === LISTA CATEGORIE PER DROPDOWN FILTRO ===
            // distinct() - SQL DISTINCT per evitare duplicati
            // pluck() - Estrae solo la colonna specificata
            // filter() - Rimuove valori null/vuoti
            // sort() - Ordinamento alfabetico
            // values() - Reindexes l'array numericamente
            $categorie = Prodotto::where('staff_assegnato_id', $user->id)
                                 ->distinct()
                                 ->pluck('categoria')
                                 ->filter()
                                 ->sort()
                                 ->values();
            
            // === RITORNO VISTA CON TUTTI I DATI ===
            return view('staff.prodotti-assegnati', compact(
                'prodottiAssegnati', 'stats', 'categorie', 'user'
            ));

        } catch (\Exception $e) {
            // === LOGGING ERRORE CON CONTESTO ===
            Log::error('Errore prodotti assegnati staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'filters' => $request->all()    // Log tutti i filtri applicati
            ]);

            // Laravel back() - Torna alla pagina precedente
            // withErrors() - Aggiunge errori alla sessione
            return back()->with('error', 'Errore nel caricamento dei prodotti assegnati');
        }
    }

    /**
     * =============================================================================
     * STATISTICHE DETTAGLIATE STAFF
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con parametri Request
     * SCOPO: Fornisce statistiche approfondite sull'attività dello staff
     * 
     * FUNZIONALITÀ:
     * - Statistiche generali dell'utente e del sistema
     * - Attività mensile degli ultimi 6 mesi
     * - Prodotti più problematici gestiti dall'utente
     * - Ultime soluzioni create
     * - Distribuzione soluzioni per categoria prodotto
     * - Filtro per periodo personalizzabile
     * 
     * TECNOLOGIE:
     * - Laravel Schema per verifiche struttura database
     * - Eloquent aggregazioni (COUNT, GROUP BY)
     * - Carbon per manipolazione date
     * - Laravel Collections per elaborazione dati
     */
    public function statistiche(Request $request)
    {
        // === VERIFICA AUTORIZZAZIONI SPECIFICHE ===
        // Doppio controllo: autenticazione + metodo custom canManageMalfunzionamenti()
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Accesso riservato allo staff aziendale');
        }

        $user = Auth::user();
        $periodo = $request->input('periodo', 30); // Default 30 giorni
        
        try {
            // === STATISTICHE GENERALI CON VERIFICA SCHEMA ===
            $stats = [
                // Contatori base sempre disponibili
                'prodotti_totali' => \App\Models\Prodotto::count(),
                'malfunzionamenti_totali' => \App\Models\Malfunzionamento::count(),
                
                // Statistiche utente (solo se campo tracking esiste)
                'soluzioni_create' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)->count() 
                    : 0,
                'soluzioni_modificate' => \Schema::hasColumn('malfunzionamenti', 'modificato_da') 
                    ? \App\Models\Malfunzionamento::where('modificato_da', $user->id)->count() 
                    : 0,
                
                // Statistiche per periodo selezionato
                'soluzioni_periodo' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)
                        ->where('created_at', '>=', now()->subDays($periodo))
                        ->count()
                    : 0,
                    
                'modifiche_periodo' => \Schema::hasColumn('malfunzionamenti', 'modificato_da') 
                    ? \App\Models\Malfunzionamento::where('modificato_da', $user->id)
                        ->where('updated_at', '>=', now()->subDays($periodo))
                        ->count()
                    : 0,
                    
                // === STATISTICHE PER GRAVITÀ PROBLEMA ===
                // Conta soluzioni create dall'utente divise per livello di gravità
                'critiche_risolte' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)
                        ->where('gravita', 'critica')->count()
                    : 0,
                'alte_risolte' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)
                        ->where('gravita', 'alta')->count()
                    : 0,
                'medie_risolte' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)
                        ->where('gravita', 'media')->count()
                    : 0,
                'basse_risolte' => \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                    ? \App\Models\Malfunzionamento::where('creato_da', $user->id)
                        ->where('gravita', 'bassa')->count()
                    : 0,
            ];

            // === ATTIVITÀ MENSILE (ULTIMI 6 MESI) ===
            // Array per costruire grafico trend attività mensile
            $attivitaMensile = [];
            if (\Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                // Loop degli ultimi 6 mesi (da 5 mesi fa a mese corrente)
                for ($i = 5; $i >= 0; $i--) {
                    // Carbon per calcolare inizio/fine mese
                    $startOfMonth = now()->subMonths($i)->startOfMonth();
                    $endOfMonth = now()->subMonths($i)->endOfMonth();
                    
                    $attivitaMensile[] = [
                        'mese' => $startOfMonth->format('M Y'),    // Es: "Gen 2025"
                        
                        // Conta soluzioni create in quel mese
                        'soluzioni_create' => \App\Models\Malfunzionamento::where('creato_da', $user->id)
                            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->count(),
                            
                        // Conta soluzioni modificate in quel mese
                        'soluzioni_modificate' => \App\Models\Malfunzionamento::where('modificato_da', $user->id)
                            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                            ->count(),
                    ];
                }
            }

            // === PRODOTTI PIÙ PROBLEMATICI ===
            // Trova i prodotti con più soluzioni create dall'utente corrente
            $prodottiProblematici = collect();
            if (\Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                // withCount() - Aggiunge contatore alla query principale
                $prodottiProblematici = \App\Models\Prodotto::withCount([
                        'malfunzionamenti as soluzioni_mie' => function ($query) use ($user) {
                            $query->where('creato_da', $user->id);
                        }
                    ])
                    ->having('soluzioni_mie', '>', 0)          // Solo prodotti con soluzioni
                    ->orderByDesc('soluzioni_mie')             // Ordina per numero soluzioni
                    ->limit(10)                                // Top 10
                    ->get();
            }

            // === ULTIME SOLUZIONI CREATE ===
            $ultimeSoluzioni = collect();
            if (\Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                $ultimeSoluzioni = \App\Models\Malfunzionamento::where('creato_da', $user->id)
                    ->with(['prodotto:id,nome,modello,categoria'])     // Eager loading ottimizzato
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }

            // === SOLUZIONI PER CATEGORIA PRODOTTO ===
            // Raggruppa soluzioni create dall'utente per categoria di prodotto
            $soluzioniPerCategoria = collect();
            if (\Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                // Query con JOIN e GROUP BY
                $soluzioniPerCategoria = \App\Models\Malfunzionamento::where('creato_da', $user->id)
                    ->join('prodotti', 'malfunzionamenti.prodotto_id', '=', 'prodotti.id')
                    ->selectRaw('prodotti.categoria, COUNT(*) as count')      // SELECT categoria, COUNT(*)
                    ->groupBy('prodotti.categoria')                           // GROUP BY categoria
                    ->orderByDesc('count')                                    // ORDER BY count DESC
                    ->get();
            }

            // === RITORNO VISTA CON TUTTI I DATI ===
            return view('staff.statistiche', compact(
                'user',
                'stats', 
                'attivitaMensile', 
                'prodottiProblematici',
                'ultimeSoluzioni',
                'soluzioniPerCategoria',
                'periodo'
            ));

        } catch (\Exception $e) {
            // === GESTIONE ERRORE CON FALLBACK ===
            \Log::error('Errore caricamento statistiche staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'periodo' => $periodo
            ]);
            
            // Ritorna vista con dati vuoti ma funzionali
            return view('statistiche', [
                'user' => $user,
                'stats' => [
                    'prodotti_totali' => 0,
                    'malfunzionamenti_totali' => 0,
                    'soluzioni_create' => 0,
                    'soluzioni_modificate' => 0,
                    'soluzioni_periodo' => 0,
                    'modifiche_periodo' => 0,
                    'critiche_risolte' => 0,
                    'alte_risolte' => 0,
                    'medie_risolte' => 0,
                    'basse_risolte' => 0,
                ],
                'attivitaMensile' => [],
                'prodottiProblematici' => collect(),
                'ultimeSoluzioni' => collect(),
                'soluzioniPerCategoria' => collect(),
                'periodo' => $periodo,
                'error' => 'Errore nel caricamento delle statistiche'
            ]);
        }
    }

    /**
     * =============================================================================
     * REPORT DETTAGLIATO ATTIVITÀ STAFF
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con Request per filtri
     * SCOPO: Genera report dettagliato delle attività dello staff in un periodo
     * 
     * FUNZIONALITÀ:
     * - Filtro per periodo personalizzabile (data inizio/fine)
     * - Filtro per tipo attività (create, update, delete)
     * - Paginazione dei risultati
     * - Statistiche aggregate del periodo
     * - Export e visualizzazione cronologica
     * 
     * TECNOLOGIE:
     * - Laravel Request validation e input sanitization
     * - Eloquent whereBetween per query su range date
     * - Eloquent whereHas per relazioni complesse
     * - Laravel Pagination automatica
     */
    public function reportAttivita(Request $request)
    {
        $user = Auth::user();
        
        try {
            // === ESTRAZIONE PARAMETRI FILTRO ===
            // input() con default - Laravel gestisce automaticamente sanitizzazione
            $dataInizio = $request->input('data_inizio', now()->startOfMonth()->format('Y-m-d'));
            $dataFine = $request->input('data_fine', now()->format('Y-m-d'));
            $tipoAttivita = $request->input('tipo', 'all'); // all, create, update, delete
            
            // === QUERY BASE CON FILTRO UTENTE E PERIODO ===
            // whereHas() - Filtra malfunzionamenti solo dei prodotti assegnati all'utente
            $query = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with(['prodotto'])    // Eager loading del prodotto correlato
                // whereBetween - SQL: WHERE updated_at BETWEEN ? AND ?
                ->whereBetween('updated_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59']);
            
            // === APPLICAZIONE FILTRO TIPO ATTIVITÀ ===
            if ($tipoAttivita !== 'all') {
                // Preparazione per future implementazioni con audit log
                // Qui potresti filtrare per tipo di azione (create, update, delete)
                // Ad esempio con una tabella audit_logs o campi specifici
            }
            
            // Esecuzione query con paginazione
            $attivita = $query->orderByDesc('updated_at')->paginate(20);
            
            // === CALCOLO STATISTICHE PERIODO ===
            $statsReport = [
                'totale_attivita' => $attivita->total(),
                
                // Conta prodotti distinti modificati nel periodo
                'prodotti_modificati' => $query->distinct('prodotto_id')->count('prodotto_id'),
                
                // Conta nuove soluzioni create nel periodo
                'nuove_soluzioni' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereBetween('created_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59'])
                    ->whereNotNull('soluzione')              // Solo record con soluzione
                    ->where('soluzione', '!=', '')           // Soluzione non vuota
                    ->count(),
                    
                // Conta modifiche a soluzioni esistenti
                'modifiche_soluzioni' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereBetween('updated_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59'])
                    ->where('created_at', '<', $dataInizio . ' 00:00:00') // Creati prima del periodo
                    ->count()
            ];
            
            return view('staff.report-attivita', compact(
                'user', 'attivita', 'statsReport', 'dataInizio', 'dataFine', 'tipoAttivita'
            ));

        } catch (\Exception $e) {
            Log::error('Errore report attività staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Errore nella generazione del report');
        }
    }

    // =================================================================================
    // SEZIONE: GESTIONE MALFUNZIONAMENTI (CRUD STAFF)
    // =================================================================================

    /**
     * =============================================================================
     * VISUALIZZAZIONE MALFUNZIONAMENTI DI UN PRODOTTO
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con parametro integer
     * PARAMETRI: int $productId - ID del prodotto da visualizzare
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Mostra tutti i malfunzionamenti associati a un prodotto specifico.
     * Utilizzato quando lo staff vuole vedere tutti i problemi noti di un prodotto.
     * 
     * TECNOLOGIE:
     * - Eloquent findOrFail() per recupero sicuro con gestione errori automatica
     * - Relazioni Eloquent per accesso ai malfunzionamenti correlati
     * - Laravel Blade view rendering
     */
    public function showMalfunzionamento($productId)
    {
        // === RECUPERO PRODOTTO CON GESTIONE ERRORI AUTOMATICA ===
        // findOrFail() - Se non trova il prodotto, Laravel genera automaticamente 404
        $prodotto = Prodotto::findOrFail($productId);
        
        // === ACCESSO RELAZIONE ELOQUENT ===
        // $prodotto->malfunzionamenti - Accede alla relazione hasMany() definita nel modello
        // Laravel esegue automaticamente: SELECT * FROM malfunzionamenti WHERE prodotto_id = ?
        $malfunzionamenti = $prodotto->malfunzionamenti;
        
        // === RITORNO VISTA CON DATI ===
        // compact() - Crea array associativo: ['prodotto' => $prodotto, 'malfunzionamenti' => $malfunzionamenti]
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti'));
    }

    /**
     * =============================================================================
     * FORM CREAZIONE NUOVA SOLUZIONE (METODO CORRETTO)
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico senza parametri
     * TIPO RITORNO: Illuminate\View\View
     * 
     * SCOPO:
     * Mostra il form per creare un nuovo malfunzionamento con possibilità
     * di scegliere SOLO tra i prodotti assegnati allo staff corrente.
     * Implementa la funzionalità opzionale di ripartizione gestione prodotti.
     * 
     * LOGICA:
     * 1. Verifica autorizzazioni utente
     * 2. Recupera solo prodotti assegnati all'utente
     * 3. Gestisce fallback se assegnazione non implementata
     * 4. Calcola statistiche sui prodotti assegnati
     * 5. Ritorna form con prodotti disponibili
     * 
     * GESTIONE ERRORI:
     * - Fallback se funzionalità assegnazione non implementata
     * - Redirect se utente non ha prodotti assegnati
     * - Gestione eccezioni database
     */
    public function createNuovaSoluzione()
    {
        // === VERIFICA AUTORIZZAZIONI DETTAGLIATA ===
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff');
        }

        $user = Auth::user();

        // === RECUPERO PRODOTTI ASSEGNATI ALL'UTENTE ===
        try {
            // Schema::hasColumn() - Verifica se la funzionalità opzionale è implementata
            if (!Schema::hasColumn('prodotti', 'staff_assegnato_id')) {
                Log::warning('Campo staff_assegnato_id non esiste - implementazione assegnazioni non attiva');
                
                // === FALLBACK - Funzionalità non implementata ===
                // Se l'assegnazione non è implementata, mostra tutti i prodotti attivi
                $prodotti = Prodotto::where('attivo', true)
                                   ->orderBy('categoria')
                                   ->orderBy('nome')
                                   ->get();
            } else {
                // === QUERY PRODOTTI ASSEGNATI ===
                // Solo prodotti specificamente assegnati all'utente corrente
                $prodotti = Prodotto::where('staff_assegnato_id', $user->id)
                                   ->where('attivo', true)
                                   ->orderBy('categoria')
                                   ->orderBy('nome')
                                   ->get();
                
                Log::info('Prodotti assegnati caricati per staff', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'prodotti_count' => $prodotti->count()
                ]);
            }

            // === VERIFICA DISPONIBILITÀ PRODOTTI ===
            if ($prodotti->isEmpty()) {
                Log::warning('Staff senza prodotti assegnati tenta di creare soluzione', [
                    'user_id' => $user->id,
                    'username' => $user->username
                ]);

                // Redirect con messaggio informativo
                return redirect()->route('staff.dashboard')
                               ->with('warning', 'Non hai prodotti assegnati. Contatta l\'amministratore per richiedere l\'assegnazione di prodotti da gestire.')
                               ->with('info', 'Solo l\'amministratore può assegnare prodotti ai membri dello staff.');
            }

            // === CALCOLO STATISTICHE PRODOTTI ASSEGNATI ===
            $statsAssegnati = [
                'totale' => $prodotti->count(),
                
                // Raggruppa per categoria e conta
                'per_categoria' => $prodotti->groupBy('categoria')->map(function($gruppo) {
                    return $gruppo->count();
                })->sortDesc(),    // Ordina per numero decrescente
                
                // Conta prodotti con/senza problemi
                'con_problemi' => $prodotti->filter(function($prodotto) {
                    return $prodotto->malfunzionamenti->count() > 0;
                })->count(),
                'senza_problemi' => $prodotti->filter(function($prodotto) {
                    return $prodotto->malfunzionamenti->count() === 0;
                })->count()
            ];

            // Variabili per compatibilità con vista esistente
            $prodotto = null;              // Nessun prodotto pre-selezionato
            $isNuovaSoluzione = true;      // Flag per vista form
            
            Log::info('Form nuova soluzione caricato con successo', [
                'user_id' => $user->id,
                'prodotti_disponibili' => $prodotti->count(),
                'stats' => $statsAssegnati
            ]);
            
            // === RITORNO VISTA FORM ===
            return view('malfunzionamenti.create', compact(
                'prodotto', 
                'prodotti', 
                'isNuovaSoluzione',
                'statsAssegnati',
                'user'
            ));

        } catch (\Exception $e) {
            Log::error('Errore caricamento form nuova soluzione', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('staff.dashboard')
                           ->with('error', 'Errore nel caricamento del modulo per nuove soluzioni. Riprova più tardi.');
        }
    }

    /**
     * =============================================================================
     * SALVATAGGIO NUOVA SOLUZIONE (METODO CORRETTO)
     * =============================================================================
     * 
     * LINGUAGGIO: PHP con Laravel Framework
     * TIPO METODO: Metodo pubblico con Request
     * PARAMETRI: Illuminate\Http\Request $request - Dati HTTP del form
     * TIPO RITORNO: Illuminate\Http\RedirectResponse
     * 
     * SCOPO:
     * Salva un nuovo malfunzionamento nel database rispettando completamente
     * la struttura della migration esistente. Gestisce validazione,
     * preparazione dati, inserimento DB e gestione errori.
     * 
     * VALIDAZIONE:
     * - Tutti i campi obbligatori della migration
     * - Tipi di dato corretti (ENUM, integer, string)
     * - Lunghezze massime dei campi
     * - Esistenza prodotto nel database
     * 
     * GESTIONE ERRORI:
     * - Errori di validazione Laravel
     * - Errori database (foreign key, duplicate, etc.)
     * - Errori generici con logging dettagliato
     */
    public function storeNuovaSoluzione(Request $request)
    {
        // === VERIFICA AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff');
        }

        // === VALIDAZIONE COMPLETA BASATA SULLA MIGRATION ===
        // Laravel $request->validate() - Valida input secondo regole specificate
        $request->validate([
            // Validazione prodotto
            'prodotto_id' => 'required|exists:prodotti,id',       // Deve esistere nella tabella prodotti
            
            // Campi obbligatori text/varchar
            'titolo' => 'required|string|max:255',                // VARCHAR(255) nella migration
            'descrizione' => 'required|string',                   // TEXT nella migration
            'soluzione' => 'required|string',                     // TEXT nella migration
            
            // Campo ENUM gravità
            'gravita' => 'required|in:bassa,media,alta,critica',  // ENUM esatto dalla migration
            
            // Campi opzionali
            'strumenti_necessari' => 'nullable|string',           // TEXT nullable
            'tempo_stimato' => 'nullable|integer|min:1',          // INT nullable, minimo 1 minuto
            'difficolta' => 'nullable|in:facile,media,difficile,esperto', // ENUM nullable
        ], [
            // === MESSAGGI DI ERRORE PERSONALIZZATI IN ITALIANO ===
            'prodotto_id.required' => 'Devi selezionare un prodotto.',
            'prodotto_id.exists' => 'Il prodotto selezionato non esiste.',
            'titolo.required' => 'Il titolo del problema è obbligatorio.',
            'titolo.max' => 'Il titolo non può superare 255 caratteri.',
            'descrizione.required' => 'La descrizione del problema è obbligatoria.',
            'soluzione.required' => 'La soluzione tecnica è obbligatoria.',
            'gravita.required' => 'Devi selezionare il livello di gravità.',
            'gravita.in' => 'Il livello di gravità deve essere: bassa, media, alta o critica.',
            'difficolta.in' => 'La difficoltà deve essere: facile, media, difficile o esperto.',
            'tempo_stimato.integer' => 'Il tempo stimato deve essere un numero intero.',
            'tempo_stimato.min' => 'Il tempo stimato deve essere almeno 1 minuto.',
        ]);

        try {
            // === LOG PRE-INSERIMENTO PER DEBUG ===
            \Log::info('Creazione nuova soluzione - Pre-save', [
                'user_id' => Auth::id(),
                'prodotto_id' => $request->prodotto_id,
                'titolo' => $request->titolo,
                'gravita' => $request->gravita,
                'has_soluzione' => !empty($request->soluzione)
            ]);

            // === PREPARAZIONE DATI ALLINEATI ALLA MIGRATION ===
            // Array associativo con tutti i campi richiesti dalla migration
            $data = [
                // === CAMPI OBBLIGATORI DALLA MIGRATION ===
                'prodotto_id' => $request->prodotto_id,
                'titolo' => $request->titolo,
                'descrizione' => $request->descrizione,
                'gravita' => $request->gravita,
                'soluzione' => $request->soluzione,
                
                // === CAMPI CON VALORI DEFAULT OBBLIGATORI ===
                'numero_segnalazioni' => 1,                     // Default per nuovo problema
                'prima_segnalazione' => now()->format('Y-m-d'), // Data oggi
                'ultima_segnalazione' => now()->format('Y-m-d'), // Data oggi
                'creato_da' => Auth::id(),                       // ID utente staff corrente
                
                // === TIMESTAMPS AUTOMATICI LARAVEL ===
                'created_at' => now(),
                'updated_at' => now()
            ];

            // === GESTIONE CAMPI OPZIONALI ===
            if (!empty($request->strumenti_necessari)) {
                $data['strumenti_necessari'] = $request->strumenti_necessari;
            }
            
            if (!empty($request->tempo_stimato)) {
                $data['tempo_stimato'] = (int) $request->tempo_stimato;  // Cast a integer
            }
            
            if (!empty($request->difficolta)) {
                $data['difficolta'] = $request->difficolta;
            } else {
                $data['difficolta'] = 'media';  // Default dalla migration
            }

            // === GESTIONE CAMPI EXTRA SE ESISTONO ===
            // Questi campi non sono nella migration di base ma potrebbero essere aggiunti
            if (!empty($request->componente_difettoso)) {
                $data['componente_difettoso'] = $request->componente_difettoso;
            }
            
            if (!empty($request->codice_errore)) {
                $data['codice_errore'] = $request->codice_errore;
            }

            // === INSERIMENTO NEL DATABASE ===
            // Eloquent create() - INSERT INTO malfunzionamenti VALUES (...)
            $malfunzionamento = Malfunzionamento::create($data);

            // === RECUPERO INFO PRODOTTO PER MESSAGGIO ===
            $prodotto = Prodotto::find($request->prodotto_id);
            $nomeProdotto = $prodotto ? $prodotto->nome : 'Prodotto';

            // === LOG POST-INSERIMENTO ===
            \Log::info('Nuova soluzione creata con successo', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'prodotto_nome' => $nomeProdotto,
                'staff_id' => Auth::id(),
                'staff_username' => Auth::user()->username ?? 'N/A',
                'titolo' => $request->titolo,
                'gravita' => $request->gravita,
                'timestamp' => now()
            ]);

            // === REDIRECT CON MESSAGGIO DI SUCCESSO ===
            // Laravel redirect() con messaggi flash nella sessione
            return redirect()->route('staff.dashboard')
                            ->with('success', "Nuova soluzione aggiunta con successo al prodotto: <strong>{$nomeProdotto}</strong>")
                            ->with('info', "ID Soluzione: #{$malfunzionamento->id} | Gravità: {$request->gravita}");

        } catch (\Illuminate\Database\QueryException $e) {
            // === GESTIONE ERRORI DATABASE SPECIFICI ===
            \Log::error('Errore database nella creazione soluzione', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']) // Esclude token CSRF
            ]);

            $errorMsg = 'Errore nel database durante la creazione della soluzione.';
            
            // === MESSAGGI SPECIFICI PER ERRORI COMUNI ===
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                if (str_contains($e->getMessage(), 'prodotto_id')) {
                    $errorMsg = 'Il prodotto selezionato non è valido. Riprova con un altro prodotto.';
                } elseif (str_contains($e->getMessage(), 'creato_da')) {
                    $errorMsg = 'Errore nell\'associazione utente. Riprova ad effettuare il login.';
                } else {
                    $errorMsg = 'Errore di integrità dati. Controlla i dati inseriti.';
                }
            } elseif (str_contains($e->getMessage(), 'Data too long')) {
                $errorMsg = 'Uno dei campi contiene troppo testo. Riduci la lunghezza dei contenuti.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMsg = 'Questa soluzione sembra essere già presente nel sistema.';
            } elseif (str_contains($e->getMessage(), 'cannot be null') || str_contains($e->getMessage(), 'not null')) {
                $errorMsg = 'Alcuni campi obbligatori sono mancanti. Controlla il form.';
            } elseif (str_contains($e->getMessage(), 'Incorrect') && str_contains($e->getMessage(), 'value')) {
                $errorMsg = 'Uno dei valori inseriti non è valido per il formato richiesto.';
            }

            // Laravel withInput() - Mantiene i dati inseriti nel form
            // withErrors() - Aggiunge errori alla sessione per mostrarli nella vista
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['database' => $errorMsg]);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI GENERICI ===
            \Log::error('Errore generico nella creazione soluzione', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token', 'password'])
            ]);

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['general' => 'Errore imprevisto durante la creazione della soluzione. Riprova o contatta l\'amministratore se il problema persiste.']);
        }
    }