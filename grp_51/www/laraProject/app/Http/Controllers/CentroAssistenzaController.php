
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\CentroAssistenza;
use App\Models\User;
use Carbon\Carbon;

/**
 * CONTROLLER CENTRI DI ASSISTENZA - LINGUAGGIO: PHP con Laravel Framework
 * 
 * Questo controller gestisce TUTTI gli aspetti dei Centri di Assistenza del sistema:
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Vista pubblica centri (accessibile a tutti)
 * - API pubbliche per ricerca e filtraggio centri
 * - Gestione amministrativa completa (solo admin)
 * - Assegnazione/rimozione tecnici ai centri
 * - API avanzate per dashboard amministrative
 * - Statistiche e reporting centri
 * 
 * LIVELLI DI ACCESSO:
 * - Livello 1 (Pubblico): Vista lista centri, dettagli centro
 * - Livello 4 (Admin): CRUD completo, gestione tecnici, statistiche
 * 
 * ARCHITETTURA:
 * - Pattern MVC con Controller che coordina Model e View
 * - API REST per interfacce AJAX
 * - Validazione robusta con Laravel Validator
 * - Logging completo per audit trail
 * - Error handling con transaction DB
 */
class CentroAssistenzaController extends Controller
{
    // ================================================
    // SEZIONE 1: METODI PUBBLICI (LIVELLO 1 - ACCESSO LIBERO)
    // ================================================

    /**
     * METODO INDEX PUBBLICO - LINGUAGGIO: PHP con Eloquent Query Builder
     * 
     * Vista principale per la lista dei centri di assistenza.
     * Accessibile a TUTTI gli utenti senza autenticazione.
     * 
     * FUNZIONALITÀ:
     * - Filtri di ricerca multipli (nome, città, provincia)
     * - Paginazione risultati (12 per pagina)
     * - Conteggio tecnici per centro
     * - Statistiche distribuzione geografica
     * - Ordinamento per provincia/città/nome
     * 
     * ROUTE: GET /centri-assistenza
     * 
     * @param Request $request Parametri filtri dalla query string
     * @return \Illuminate\View\View Vista pubblica centri
     */
    public function index(Request $request)
    {
        try {
            // STEP 1: INIZIALIZZAZIONE QUERY BASE
            // query() crea un nuovo Eloquent Query Builder per CentroAssistenza
            $query = CentroAssistenza::query();

            // STEP 2: APPLICAZIONE FILTRI DINAMICI DALLA RICHIESTA
            
            // FILTRO: Ricerca per termine generico su multipli campi
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                // where() con closure crea una sotto-query con parentesi
                // Questo permette di usare OR tra i campi senza interferire con altri filtri
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")          // Ricerca nel nome centro
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")        // Ricerca nella città
                      ->orWhere('provincia', 'LIKE', "%{$searchTerm}%")    // Ricerca nella provincia
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");   // Ricerca nell'indirizzo
                });
            }

            // FILTRO: Provincia specifica
            if ($request->filled('provincia')) {
                // strtoupper() normalizza la provincia in maiuscolo (standard IT)
                $query->where('provincia', strtoupper($request->provincia));
            }

            // FILTRO: Città specifica (ricerca parziale)
            if ($request->filled('citta')) {
                // LIKE con % permette ricerca parziale case-insensitive
                $query->where('citta', 'LIKE', '%' . $request->citta . '%');
            }

            // STEP 3: ESECUZIONE QUERY CON OTTIMIZZAZIONI
            $centri = $query->withCount('tecnici')                      // Conta tecnici senza caricarli tutti
                ->orderBy('provincia')                                  // Ordinamento primario per provincia
                ->orderBy('citta')                                      // Ordinamento secondario per città
                ->orderBy('nome')                                       // Ordinamento terziario per nome
                ->paginate(12)                                          // Paginazione 12 elementi per pagina
                ->withQueryString();                                    // Mantiene parametri URL nelle pagine

            // STEP 4: RACCOLTA PROVINCE UNICHE PER DROPDOWN FILTRO
            $province = CentroAssistenza::distinct()                    // SELECT DISTINCT provincia
                ->whereNotNull('provincia')                             // Esclude province NULL
                ->orderBy('provincia')                                  // Ordine alfabetico
                ->pluck('provincia')                                    // Estrae solo colonna provincia
                ->map(function($provincia) {
                    return strtoupper($provincia);                      // Normalizza in maiuscolo
                })
                ->unique()                                              // Rimuove eventuali duplicati
                ->values();                                             // Re-indicizza array

            // STEP 5: CALCOLO DISTRIBUZIONE GEOGRAFICA PER STATISTICHE
            // Query aggregate con GROUP BY per contare centri per provincia
            $distribuzioneCentri = CentroAssistenza::select('provincia', DB::raw('COUNT(*) as totale'))
                ->groupBy('provincia')                                  // Raggruppa per provincia
                ->orderBy('totale', 'DESC')                             // Ordina per numero centri discendente
                ->get();

            // STEP 6: CALCOLO STATISTICHE GENERALI DASHBOARD
            $statistiche = [
                'totale_centri' => CentroAssistenza::count(),                           // Conteggio totale centri
                'province_coperte' => $province->count(),                               // Numero province con centri
                'centri_con_tecnici' => CentroAssistenza::has('tecnici')->count(),     // Centri che HANNO tecnici
                'tecnici_totali' => User::where('livello_accesso', '2')                // Tutti i tecnici del sistema
                    ->whereNotNull('centro_assistenza_id')->count()                    // che sono assegnati a un centro
            ];

            // STEP 7: RETURN VISTA CON TUTTI I DATI
            // compact() crea array associativo con variabili per la vista Blade
            return view('centri.index', compact(
                'centri',                   // Risultati paginati per la lista
                'province',                 // Province per dropdown filtro
                'distribuzioneCentri',      // Dati per grafico distribuzione
                'statistiche'               // Statistiche per cards riassuntive
            ));

        } catch (\Exception $e) {
            // STEP 8: GESTIONE ERRORI CON LOGGING DETTAGLIATO
            Log::error('Errore nella vista centri', [
                'error' => $e->getMessage(),                            // Messaggio errore
                'request_data' => $request->all(),                      // Dati richiesta per debug
                'trace' => $e->getTraceAsString()                       // Stack trace completo
            ]);

            // Redirect sicuro alla home con messaggio di errore
            return redirect()->route('home')
                ->with('error', 'Errore nel caricamento dei centri di assistenza');
        }
    }

    /**
     * METODO SHOW PUBBLICO - LINGUAGGIO: PHP con Eager Loading
     * 
     * Vista dettagliata di un singolo centro di assistenza.
     * Mostra informazioni complete del centro e tecnici associati.
     * 
     * FUNZIONALITÀ:
     * - Dettagli completi centro
     * - Lista tecnici del centro con specializzazioni
     * - Centri vicini nella stessa provincia
     * - Informazioni di contatto
     * 
     * ROUTE: GET /centri-assistenza/{centro}
     * 
     * @param CentroAssistenza $centro Model binding automatico Laravel
     * @return \Illuminate\View\View Vista dettaglio centro
     */
    public function show(CentroAssistenza $centro)
    {
        try {
            // STEP 1: EAGER LOADING TECNICI CON OTTIMIZZAZIONE
            // load() esegue una query aggiuntiva per caricare la relazione
            // La closure permette di ottimizzare la query dei tecnici correlati
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
                      ->orderBy('nome');                                // Ordina tecnici alfabeticamente
            }]);

            // STEP 2: TROVA CENTRI VICINI NELLA STESSA PROVINCIA
            $centriVicini = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)                       // Esclude centro corrente
                ->withCount('tecnici')                                  // Conta tecnici per ogni centro
                ->limit(4)                                              // Massimo 4 centri correlati
                ->get();

            // STEP 3: RETURN VISTA DETTAGLIO
            return view('centri.show', compact('centro', 'centriVicini'));

        } catch (\Exception $e) {
            // STEP 4: GESTIONE ERRORI SPECIFICA
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,                             // ID centro che ha causato errore
                'error' => $e->getMessage()
            ]);

            // Redirect alla lista centri con messaggio
            return redirect()->route('centri.index')
                ->with('error', 'Centro non trovato');
        }
    }

    /**
     * METODO ADMIN SHOW - LINGUAGGIO: PHP
     * 
     * Vista dettagliata centro per amministratori.
     * Identica alla vista pubblica ma con template diverso per admin.
     * 
     * @param CentroAssistenza $centro Model binding Laravel
     * @return \Illuminate\View\View Vista admin dettaglio centro
     */
    public function adminShow(CentroAssistenza $centro)
    {
        try {
            // Stessa logica del metodo show() pubblico
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
                      ->orderBy('nome');
            }]);

            $centriVicini = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->withCount('tecnici')
                ->limit(4)
                ->get();

            // DIFFERENZA: Usa template admin invece di pubblico
            return view('admin.centri.show', compact('centro', 'centriVicini'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('centri.index')
                ->with('error', 'Centro non trovato');
        }
    }

    // ================================================
    // SEZIONE 2: API PUBBLICHE (SENZA AUTENTICAZIONE)
    // ================================================

    /**
     * METODO API INDEX - LINGUAGGIO: PHP con JSON Response
     * 
     * API endpoint per lista centri in formato JSON.
     * Utilizzata per caricamenti AJAX dinamici nelle interfacce web.
     * 
     * CARATTERISTICHE:
     * - Response JSON strutturata
     * - Conteggio tecnici per centro
     * - Ordinamento geografico
     * - Error handling per API
     * 
     * ROUTE: GET /api/centri
     * 
     * @return \Illuminate\Http\JsonResponse Lista centri in formato JSON
     */
    public function apiIndex()
    {
        try {
            // STEP 1: QUERY OTTIMIZZATA PER API
            $centri = CentroAssistenza::withCount('tecnici')            // Conta tecnici senza caricarli
                ->orderBy('provincia')                                  // Ordinamento geografico
                ->orderBy('citta')
                ->orderBy('nome')
                ->get(['id', 'nome', 'citta', 'provincia', 'indirizzo', 'telefono', 'email']) // Solo campi necessari
                ->map(function($centro) {
                    // STEP 2: TRASFORMAZIONE DATI PER API
                    return [
                        'id' => $centro->id,
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'provincia' => strtoupper($centro->provincia),      // Normalizzazione provincia
                        'indirizzo_completo' => $centro->indirizzo_completo ?? $centro->indirizzo,
                        'telefono' => $centro->telefono,
                        'email' => $centro->email,
                        'numero_tecnici' => $centro->tecnici_count ?? 0,    // Default 0 se null
                        'ha_tecnici' => ($centro->tecnici_count ?? 0) > 0   // Boolean per frontend
                    ];
                });

            // STEP 3: RESPONSE JSON STRUTTURATA
            return response()->json([
                'success' => true,
                'centri' => $centri,
                'total' => $centri->count()
            ]);

        } catch (\Exception $e) {
            // STEP 4: ERROR HANDLING API
            Log::error('Errore API lista centri', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento centri'
            ], 500);                                                    // HTTP 500 Internal Server Error
        }
    }

    /**
     * METODO API SEARCH - LINGUAGGIO: PHP con Validazione Input
     * 
     * API per ricerca in tempo reale dei centri.
     * Utilizzata per autocompletamento e ricerca dinamica.
     * 
     * FUNZIONALITÀ:
     * - Ricerca multi-campo (nome, città, provincia, indirizzo)
     * - Validazione lunghezza minima termine
     * - Limite risultati per performance
     * - Response ottimizzata per autocompletamento
     * 
     * ROUTE: GET /api/centri/search?q={termine}
     * 
     * @param Request $request Parametro 'q' con termine di ricerca
     * @return \Illuminate\Http\JsonResponse Risultati ricerca in JSON
     */
    public function apiSearch(Request $request)
    {
        // STEP 1: ESTRAZIONE E PULIZIA TERMINE RICERCA
        $termine = trim($request->get('q', ''));
        
        // STEP 2: VALIDAZIONE LUNGHEZZA MINIMA
        if (strlen($termine) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Termine di ricerca troppo corto (minimo 2 caratteri)'
            ], 422);                                                    // HTTP 422 Unprocessable Entity
        }

        try {
            // STEP 3: QUERY DI RICERCA MULTI-CAMPO
            $centri = CentroAssistenza::where(function($q) use ($termine) {
                    // Closure per raggruppare condizioni OR
                    $q->where('nome', 'LIKE', "%{$termine}%")
                      ->orWhere('citta', 'LIKE', "%{$termine}%")
                      ->orWhere('provincia', 'LIKE', "%{$termine}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$termine}%");
                })
                ->withCount('tecnici')                                  // Conta tecnici per risultato
                ->limit(10)                                             // Limita risultati per performance
                ->get(['id', 'nome', 'citta', 'provincia', 'indirizzo'])
                ->map(function($centro) {
                    // STEP 4: FORMATO DATI PER AUTOCOMPLETAMENTO
                    return [
                        'id' => $centro->id,
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'provincia' => strtoupper($centro->provincia),
                        'indirizzo_completo' => $centro->indirizzo_completo ?? $centro->indirizzo,
                        'numero_tecnici' => $centro->tecnici_count ?? 0
                    ];
                });

            // STEP 5: RESPONSE CON METADATI
            return response()->json([
                'success' => true,
                'centri' => $centri,
                'total' => $centri->count(),
                'termine' => $termine                                   // Echo termine per debug frontend
            ]);

        } catch (\Exception $e) {
            // STEP 6: ERROR HANDLING
            Log::error('Errore ricerca centri', [
                'termine' => $termine,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nella ricerca'
            ], 500);
        }
    }

    /**
     * METODO API CITTÀ PER PROVINCIA - LINGUAGGIO: PHP con Distinct Query
     * 
     * API CRITICA che era MANCANTE e causava errori nel sistema!
     * Restituisce lista città disponibili per una provincia specifica.
     * 
     * UTILIZZO:
     * - Popolare dinamicamente select città basate su provincia scelta
     * - Filtri a cascata nei form di ricerca
     * - Validazione coerenza dati geografici
     * 
     * ROUTE: GET /api/centri/citta-per-provincia?provincia={provincia}
     * 
     * @param Request $request Parametro 'provincia' (2 caratteri)
     * @return \Illuminate\Http\JsonResponse Lista città per provincia
     */
    public function apiCittaPerProvincia(Request $request)
    {
        // STEP 1: RECUPERO E NORMALIZZAZIONE PROVINCIA
        $provincia = strtoupper(trim($request->get('provincia', '')));
        
        // STEP 2: VALIDAZIONE PARAMETRO OBBLIGATORIO
        if (!$provincia) {
            return response()->json([
                'success' => false,
                'message' => 'Parametro provincia obbligatorio',
                'esempio' => 'GET /api/centri/citta-per-provincia?provincia=AN'    // Esempio uso
            ], 422);
        }

        try {
            // STEP 3: QUERY DISTINCT PER CITTÀ NELLA PROVINCIA
            $citta = CentroAssistenza::where('provincia', $provincia)
                ->distinct()                                            // SELECT DISTINCT citta
                ->whereNotNull('citta')                                 // Esclude città NULL
                ->where('citta', '!=', '')                              // Esclude città vuote
                ->orderBy('citta')                                      // Ordinamento alfabetico
                ->pluck('citta')                                        // Estrae solo colonna citta
                ->filter()                                              // Rimuove valori falsy
                ->values()                                              // Re-indicizza array numerico
                ->toArray();

            // STEP 4: LOGGING PER DEBUG (UTILE IN SVILUPPO)
            Log::info('API città per provincia richiesta', [
                'provincia' => $provincia,
                'citta_trovate' => count($citta),
                'user_ip' => $request->ip()                             // IP per tracciare usage
            ]);

            // STEP 5: RESPONSE JSON CON METADATI
            return response()->json([
                'success' => true,
                'citta' => $citta,
                'provincia' => $provincia,
                'total' => count($citta),
                'timestamp' => now()->toISOString()                     // Timestamp per cache frontend
            ]);

        } catch (\Exception $e) {
            // STEP 6: ERROR HANDLING CON STACK TRACE
            Log::error('Errore API città per provincia', [
                'provincia' => $provincia,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle città per la provincia ' . $provincia
            ], 500);
        }
    }

    // ================================================
    // SEZIONE 3: METODI AMMINISTRATIVI (LIVELLO 4 - SOLO ADMIN)
    // ================================================

    /**
     * METODO ADMIN INDEX - LINGUAGGIO: PHP con Query Complessa e Filtri
     * 
     * Dashboard amministrativa per gestione completa centri.
     * Include tutti i filtri, ordinamenti e statistiche per admin.
     * 
     * FUNZIONALITÀ AVANZATE:
     * - Filtri multipli (ricerca, provincia, città, stato tecnici)
     * - Ordinamento dinamico configurable via URL
     * - Statistiche complete per cards dashboard
     * - Paginazione con mantenimento filtri
     * - Gestione errori con fallback
     * 
     * ROUTE: GET /admin/centri
     * 
     * @param Request $request Parametri filtri e ordinamento
     * @return \Illuminate\View\View Dashboard admin centri
     */
    public function adminIndex(Request $request)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI RIGOROSA
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        try {
            // STEP 2: QUERY BASE CON RELAZIONI OTTIMIZZATE
            $query = CentroAssistenza::withCount('tecnici')             // Conta tecnici senza caricarli
                ->with(['tecnici' => function($q) {                     // Eager loading tecnici
                    $q->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id');
                }]);

            // STEP 3: APPLICAZIONE FILTRI DINAMICI

            // FILTRO RICERCA TESTUALE
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            // FILTRO PROVINCIA
            if ($request->filled('provincia')) {
                $query->where('provincia', strtoupper($request->provincia));
            }

            // FILTRO CITTÀ
            if ($request->filled('citta')) {
                $query->where('citta', 'LIKE', '%' . $request->citta . '%');
            }

            // FILTRO STATO TECNICI (con/senza tecnici)
            if ($request->filled('stato')) {
                if ($request->stato === 'con_tecnici') {
                    $query->whereHas('tecnici');                       // Ha almeno 1 tecnico
                } elseif ($request->stato === 'senza_tecnici') {
                    $query->whereDoesntHave('tecnici');                // Non ha tecnici
                }
            }

            // STEP 4: GESTIONE ORDINAMENTO DINAMICO
            $sortBy = $request->get('sort', 'nome');                   // Default: ordinamento per nome
            $order = $request->get('order', 'asc');                    // Default: ordine crescente
            
            // STEP 5: VALIDAZIONE PARAMETRI ORDINAMENTO (SICUREZZA)
            $allowedSorts = ['nome', 'provincia', 'tecnici', 'citta', 'created_at'];
            $allowedOrders = ['asc', 'desc'];
            
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'nome';                                       // Fallback sicuro
            }
            
            if (!in_array($order, $allowedOrders)) {
                $order = 'asc';                                         // Fallback sicuro
            }

            // STEP 6: APPLICAZIONE ORDINAMENTO CON SWITCH
            switch ($sortBy) {
                case 'nome':
                    $query->orderBy('nome', $order);
                    break;
                    
                case 'provincia':
                    $query->orderBy('provincia', $order)
                          ->orderBy('citta', $order)                    // Ordinamento secondario
                          ->orderBy('nome', 'asc');                     // Ordinamento terziario
                    break;
                    
                case 'tecnici':
                    $query->orderBy('tecnici_count', $order)
                          ->orderBy('nome', 'asc');                     // Ordinamento secondario
                    break;
                    
                case 'citta':
                    $query->orderBy('citta', $order)
                          ->orderBy('nome', 'asc');
                    break;
                    
                case 'created_at':
                    $query->orderBy('created_at', $order);
                    break;
                    
                default:
                    $query->orderBy('nome', 'asc');                     // Fallback sicuro
                    break;
            }

            // STEP 7: PAGINAZIONE CON MANTENIMENTO PARAMETRI
            $centri = $query->paginate(15)->withQueryString();          // 15 per pagina, mantiene ?search=...

            // STEP 8: PROVINCE PER DROPDOWN FILTRO
            $province = CentroAssistenza::distinct()
                ->whereNotNull('provincia')
                ->where('provincia', '!=', '')
                ->orderBy('provincia')
                ->pluck('provincia')
                ->toArray();

            // STEP 9: CALCOLO STATISTICHE DASHBOARD
            $stats = [
                'totale' => CentroAssistenza::count(),
                'con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
                'senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
                'tecnici_totali' => User::where('livello_accesso', '2')->count(),
                'tecnici_non_assegnati' => User::where('livello_accesso', '2')
                    ->whereNull('centro_assistenza_id')->count()
            ];

            // STEP 10: LOGGING DEBUG (RIMUOVERE IN PRODUZIONE)
            Log::info('Ordinamento centri', [
                'sort' => $sortBy,
                'order' => $order,
                'query_params' => $request->query(),
                'total_results' => $centri->total()
            ]);

            // STEP 11: RETURN VISTA ADMIN
            return view('admin.centri.index', compact('centri', 'stats', 'province'));

        } catch (\Exception $e) {
            // STEP 12: GESTIONE ERRORI CON FALLBACK COMPLETO
            Log::error('Errore dashboard admin centri', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback con dati vuoti per evitare crash dashboard
            return view('admin.centri.index', [
                'centri' => new \Illuminate\Pagination\LengthAwarePaginator(
                    collect(), 0, 15, 1, ['path' => request()->url()]
                ),
                'stats' => [
                    'totale' => 0,
                    'con_tecnici' => 0,
                    'senza_tecnici' => 0,
                    'tecnici_totali' => 0,
                    'tecnici_non_assegnati' => 0
                ],
                'province' => []
            ])->with('error', 'Errore nel caricamento della dashboard centri');
        }
    }

    /**
     * METODO CREATE - LINGUAGGIO: PHP con Data Preparation
     * 
     * Mostra form per creazione nuovo centro di assistenza.
     * Prepara dati necessari per il form (lista province italiane).
     * 
     * ROUTE: GET /admin/centri/create
     * 
     * @return \Illuminate\View\View Form creazione centro
     */
    public function create()
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Accesso non autorizzato');
        }

        // STEP 2: LISTA COMPLETA PROVINCE ITALIANE
        // Array con tutte le 110 province italiane (aggiornato 2024)
        
$province = collect([
            'AG', 'AL', 'AN', 'AO', 'AR', 'AP', 'AT', 'AV', 'BA', 'BT', 'BL', 'BN', 'BG', 'BI', 'BO', 'BZ',
            'BS', 'BR', 'CA', 'CL', 'CB', 'CI', 'CE', 'CT', 'CZ', 'CH', 'CO', 'CS', 'CR', 'KR', 'CN', 'EN',
            'FM', 'FE', 'FI', 'FG', 'FC', 'FR', 'GE', 'GO', 'GR', 'IM', 'IS', 'SP', 'AQ', 'LT', 'LE', 'LC',
            'LI', 'LO', 'LU', 'MC', 'MN', 'MS', 'MT', 'VS', 'ME', 'MI', 'MO', 'MB', 'NA', 'NO', 'NU', 'OG',
            'OT', 'OR', 'PD', 'PA', 'PR', 'PV', 'PG', 'PU', 'PE', 'PC', 'PI', 'PT', 'PN', 'PZ', 'PO', 'RG',
            'RA', 'RC', 'RE', 'RI', 'RN', 'RM', 'RO', 'SA', 'SS', 'SV', 'SI', 'SR', 'SO', 'TA', 'TE', 'TR',
            'TO', 'TP', 'TN', 'TV', 'TS', 'UD', 'VA', 'VE', 'VB', 'VC', 'VR', 'VV', 'VI', 'VT'
        ])->sort()->values();                                           // Ordinamento alfabetico

        return view('admin.centri.create', compact('province'));
    }

    /**
     * METODO STORE - LINGUAGGIO: PHP con Database Transaction
     * 
     * Salva nuovo centro di assistenza nel database.
     * Include validazione completa, transaction DB e logging.
     * 
     * PROCESSO:
     * 1. Verifica autorizzazioni admin
     * 2. Validazione dati con regole custom
     * 3. Creazione centro in transaction DB
     * 4. Logging operazione per audit
     * 5. Response JSON o redirect based su richiesta
     * 
     * ROUTE: POST /admin/centri
     * 
     * @param Request $request Dati form nuovo centro
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // STEP 2: VALIDAZIONE COMPLETA DATI CENTRO
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome',        // Nome unico obbligatorio
            'indirizzo' => 'required|string|max:500',                                 // Indirizzo completo
            'citta' => 'required|string|max:100',                                     // Città obbligatoria
            'provincia' => 'required|string|size:2',                                  // Provincia 2 caratteri (AN, RM, MI)
            'cap' => 'required|string|regex:/^[0-9]{5}$/',                            // CAP 5 cifre numeriche
            'telefono' => 'required|string|max:20',                                   // Telefono obbligatorio
            'email' => 'required|email|max:255|unique:centri_assistenza,email'       // Email unica valida
        ], [
            // MESSAGGI DI ERRORE PERSONALIZZATI IN ITALIANO
            'nome.unique' => 'Esiste già un centro con questo nome',
            'email.unique' => 'Email già utilizzata da un altro centro',
            'cap.regex' => 'Il CAP deve essere di 5 cifre',
            'provincia.size' => 'La provincia deve essere di 2 caratteri (es: AN)'
        ]);

        // STEP 3: CONTROLLO VALIDAZIONE
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()                        // Errori specifici per frontend
            ], 422);                                                    // HTTP 422 Unprocessable Entity
        }

        try {
            // STEP 4: INIZIO TRANSACTION DATABASE
            // beginTransaction() garantisce atomicità: tutto ok o rollback completo
            DB::beginTransaction();

            // STEP 5: CREAZIONE NUOVO CENTRO
            $centro = CentroAssistenza::create([
                'nome' => $request->nome,
                'indirizzo' => $request->indirizzo,
                'citta' => $request->citta,
                'provincia' => strtoupper($request->provincia),          // Normalizzazione maiuscolo
                'cap' => $request->cap,
                'telefono' => $request->telefono,
                'email' => $request->email,
            ]);

            // STEP 6: COMMIT TRANSACTION
            DB::commit();

            // STEP 7: LOGGING PER AUDIT TRAIL
            Log::info('Nuovo centro creato', [
                'centro_id' => $centro->id,
                'nome' => $centro->nome,
                'admin_id' => Auth::id()                                 // Chi ha creato il centro
            ]);

            // STEP 8: RESPONSE BASATA SU TIPO RICHIESTA
            if ($request->expectsJson()) {
                // Response per richieste AJAX
                return response()->json([
                    'success' => true,
                    'message' => 'Centro creato con successo',
                    'centro' => $centro,
                    'redirect' => route('admin.centri.show', $centro)
                ]);
            }

            // Redirect per richieste web tradizionali
            return redirect()->route('admin.centri.show', $centro)
                ->with('success', 'Centro creato con successo!');

        } catch (\Exception $e) {
            // STEP 9: ROLLBACK IN CASO DI ERRORE
            DB::rollback();

            // STEP 10: LOGGING ERRORE
            Log::error('Errore creazione centro', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()                                // Dati che hanno causato errore
            ]);

            // STEP 11: RESPONSE ERRORE
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nella creazione del centro'
                ], 500);
            }

            return back()
                ->withInput()                                            // Mantiene dati inseriti
                ->with('error', 'Errore nella creazione del centro');
        }
    }

    /**
     * METODO EDIT - LINGUAGGIO: PHP
     * 
     * Mostra form di modifica per centro esistente.
     * Carica dati centro e lista province per select.
     * 
     * ROUTE: GET /admin/centri/{centro}/edit
     * 
     * @param CentroAssistenza $centro Model binding automatico
     * @return \Illuminate\View\View Form modifica centro
     */
    public function edit(CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Accesso non autorizzato');
        }

        // STEP 2: PROVINCE COMPLETE PER SELECT (STESSO ARRAY DEL CREATE)
        $province = collect([
            'AG', 'AL', 'AN', 'AO', 'AR', 'AP', 'AT', 'AV', 'BA', 'BT', 'BL', 'BN', 'BG', 'BI', 'BO', 'BZ',
            'BS', 'BR', 'CA', 'CL', 'CB', 'CI', 'CE', 'CT', 'CZ', 'CH', 'CO', 'CS', 'CR', 'KR', 'CN', 'EN',
            'FM', 'FE', 'FI', 'FG', 'FC', 'FR', 'GE', 'GO', 'GR', 'IM', 'IS', 'SP', 'AQ', 'LT', 'LE', 'LC',
            'LI', 'LO', 'LU', 'MC', 'MN', 'MS', 'MT', 'VS', 'ME', 'MI', 'MO', 'MB', 'NA', 'NO', 'NU', 'OG',
            'OT', 'OR', 'PD', 'PA', 'PR', 'PV', 'PG', 'PU', 'PE', 'PC', 'PI', 'PT', 'PN', 'PZ', 'PO', 'RG',
            'RA', 'RC', 'RE', 'RI', 'RN', 'RM', 'RO', 'SA', 'SS', 'SV', 'SI', 'SR', 'SO', 'TA', 'TE', 'TR',
            'TO', 'TP', 'TN', 'TV', 'TS', 'UD', 'VA', 'VE', 'VB', 'VC', 'VR', 'VV', 'VI', 'VT'
        ])->sort()->values();

        return view('admin.centri.edit', compact('centro', 'province'));
    }

    /**
     * METODO UPDATE - LINGUAGGIO: PHP con Validazione Condizionale
     * 
     * Aggiorna dati centro esistente.
     * Validazione che esclude il centro corrente dai controlli di unicità.
     * 
     * ROUTE: PUT /admin/centri/{centro}
     * 
     * @param Request $request Dati modificati
     * @param CentroAssistenza $centro Centro da aggiornare
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // STEP 2: VALIDAZIONE CON ECCEZIONE PER CENTRO CORRENTE
        // unique:table,column,except_id esclude il record corrente dal controllo unicità
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome,' . $centro->id,
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'required|string|regex:/^[0-9]{5}$/',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:centri_assistenza,email,' . $centro->id
        ]);

        // STEP 3: CONTROLLO VALIDAZIONE
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // STEP 4: TRANSACTION PER AGGIORNAMENTO
            DB::beginTransaction();

            // STEP 5: AGGIORNAMENTO DATI CENTRO
            $centro->update([
                'nome' => $request->nome,
                'indirizzo' => $request->indirizzo,
                'citta' => $request->citta,
                'provincia' => strtoupper($request->provincia),
                'cap' => $request->cap,
                'telefono' => $request->telefono,
                'email' => $request->email,
            ]);

            DB::commit();

            // STEP 6: LOGGING AGGIORNAMENTO
            Log::info('Centro aggiornato', [
                'centro_id' => $centro->id,
                'nome' => $centro->nome,
                'admin_id' => Auth::id()
            ]);

            // STEP 7: RESPONSE SUCCESSO
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Centro aggiornato con successo',
                    'centro' => $centro
                ]);
            }

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', 'Centro aggiornato con successo!');

        } catch (\Exception $e) {
            // STEP 8: ROLLBACK E ERROR HANDLING
            DB::rollback();

            Log::error('Errore aggiornamento centro', [
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nell\'aggiornamento'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Errore nell\'aggiornamento');
        }
    }

    /**
     * METODO DESTROY - LINGUAGGIO: PHP con Controlli Integrità
     * 
     * Elimina centro di assistenza con controlli di sicurezza.
     * Verifica che non ci siano tecnici assegnati prima dell'eliminazione.
     * 
     * ROUTE: DELETE /admin/centri/{centro}
     * 
     * @param CentroAssistenza $centro Centro da eliminare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        try {
            DB::beginTransaction();

            // STEP 2: CONTROLLO INTEGRITÀ DATI
            // Verifica che il centro non abbia tecnici assegnati
            $tecniciAssegnati = $centro->tecnici()->count();
            
            if ($tecniciAssegnati > 0) {
                return redirect()->back()
                    ->with('error', "Impossibile eliminare il centro: ha {$tecniciAssegnati} tecnici assegnati");
            }

            // STEP 3: BACKUP DATI PER LOG (PRIMA DELL'ELIMINAZIONE)
            $nomeCentro = $centro->nome;
            $centroData = $centro->toArray();                           // Copia completa dati per audit
            
            // STEP 4: ELIMINAZIONE EFFETTIVA
            $centro->delete();

            DB::commit();

            // STEP 5: LOGGING ELIMINAZIONE (WARNING LEVEL PER AUDIT)
            Log::warning('Centro eliminato', [
                'centro_data' => $centroData,                           // Backup completo dati
                'admin_id' => Auth::id()
            ]);

            // STEP 6: REDIRECT CON SUCCESSO
            return redirect()->route('admin.centri.index')
                ->with('success', "Centro \"{$nomeCentro}\" eliminato con successo");

        } catch (\Exception $e) {
            // STEP 7: ROLLBACK E ERROR HANDLING
            DB::rollback();

            Log::error('Errore eliminazione centro', [
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Errore nell\'eliminazione del centro');
        }
    }

    // ================================================
    // SEZIONE 4: GESTIONE TECNICI NEI CENTRI (SOLO ADMIN)
    // ================================================

    /**
     * METODO ASSEGNA TECNICO - LINGUAGGIO: PHP con Business Logic Complessa
     * 
     * Assegna un tecnico a un centro di assistenza.
     * Gestisce sia assegnazioni nuove che trasferimenti tra centri.
     * 
     * FUNZIONALITÀ:
     * - Validazione tecnico esistente e livello corretto
     * - Rilevamento trasferimenti da altri centri
     * - Logging dettagliato per audit trail
     * - Response JSON per interfacce AJAX
     * 
     * ROUTE: POST /admin/centri/{centro}/assegna-tecnico
     * 
     * @param Request $request Contiene tecnico_id
     * @param CentroAssistenza $centro Centro di destinazione
     * @return \Illuminate\Http\JsonResponse Response successo/errore
     */
    public function assegnaTecnico(Request $request, CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // STEP 2: LOGGING TENTATIVO OPERAZIONE
        Log::info('Tentativo assegnazione tecnico', [
            'centro_id' => $centro->id,
            'centro_nome' => $centro->nome,
            'request_data' => $request->all(),
            'admin_id' => Auth::id()
        ]);

        // STEP 3: VALIDAZIONE INPUT
        $validator = Validator::make($request->all(), [
            'tecnico_id' => 'required|integer|exists:users,id'          // Deve esistere nella tabella users
        ], [
            'tecnico_id.required' => 'ID tecnico obbligatorio',
            'tecnico_id.exists' => 'Tecnico non trovato nel sistema'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // STEP 4: RECUPERO E VERIFICA TECNICO
            $tecnico = User::findOrFail($request->tecnico_id);

            // Verifica che sia effettivamente un tecnico (livello accesso 2)
            if ($tecnico->livello_accesso != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'utente selezionato non è un tecnico'
                ], 422);
            }

            // STEP 5: CONTROLLO ASSEGNAZIONE DUPLICATA
            if ($tecnico->centro_assistenza_id == $centro->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Il tecnico è già assegnato a questo centro'
                ], 422);
            }

            // STEP 6: RILEVAMENTO TRASFERIMENTO
            $centroAttuale = null;
            $isTransfer = false;
            
            if ($tecnico->centro_assistenza_id) {
                $centroAttuale = CentroAssistenza::find($tecnico->centro_assistenza_id);
                $isTransfer = true;                             // È un trasferimento, non prima assegnazione
            }

            // STEP 7: ESECUZIONE ASSEGNAZIONE
            $tecnico->centro_assistenza_id = $centro->id;
            $tecnico->save();

            DB::commit();

            // STEP 8: PREPARAZIONE MESSAGGIO DINAMICO
            if ($isTransfer && $centroAttuale) {
                $message = "Tecnico {$tecnico->nome_completo} trasferito da \"{$centroAttuale->nome}\" a \"{$centro->nome}\"";
                
                // STEP 9: LOGGING TRASFERIMENTO
                Log::info('Tecnico trasferito', [
                    'tecnico_id' => $tecnico->id,
                    'tecnico_nome' => $tecnico->nome_completo,
                    'centro_precedente' => $centroAttuale->nome,
                    'centro_nuovo' => $centro->nome,
                    'admin_id' => Auth::id()
                ]);
            } else {
                $message = "Tecnico {$tecnico->nome_completo} assegnato al centro \"{$centro->nome}\"";
                
                // STEP 10: LOGGING PRIMA ASSEGNAZIONE
                Log::info('Tecnico assegnato', [
                    'tecnico_id' => $tecnico->id,
                    'tecnico_nome' => $tecnico->nome_completo,
                    'centro' => $centro->nome,
                    'admin_id' => Auth::id()
                ]);
            }

            // STEP 11: RESPONSE JSON COMPLETA
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_transfer' => $isTransfer,
                'previous_center' => $isTransfer && $centroAttuale ? $centroAttuale->nome : null,
                'tecnico' => [
                    'id' => $tecnico->id,
                    'nome_completo' => $tecnico->nome_completo,
                    'specializzazione' => $tecnico->specializzazione ?? 'Generale'
                ],
                'centro' => [
                    'id' => $centro->id,
                    'nome' => $centro->nome
                ]
            ]);

        } catch (\Exception $e) {
            // STEP 12: ROLLBACK E ERROR HANDLING
            DB::rollback();

            Log::error('Errore assegnazione tecnico', [
                'tecnico_id' => $request->tecnico_id,
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'assegnazione del tecnico',
                'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno del server'
            ], 500);
        }
    }

    /**
     * METODO RIMUOVI TECNICO - LINGUAGGIO: PHP con Multiple Response Types
     * 
     * Rimuove tecnico da centro di assistenza.
     * Supporta sia richieste AJAX che web tradizionali.
     * 
     * FUNZIONALITÀ:
     * - Verifica tecnico appartiene al centro
     * - Rimozione con aggiornamento campo a NULL
     * - Response adattiva per tipo richiesta
     * - Logging completo operazione
     * 
     * ROUTE: POST /admin/centri/{centro}/rimuovi-tecnico
     * 
     * @param Request $request Contiene tecnico_id
     * @param CentroAssistenza $centro Centro da cui rimuovere
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function rimuoviTecnico(Request $request, CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI CON RESPONSE ADATTIVA
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $message = 'Non autorizzato';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // STEP 2: VALIDAZIONE DATI
        $validator = Validator::make($request->all(), [
            'tecnico_id' => 'required|exists:users,id'
        ], [
            'tecnico_id.required' => 'ID tecnico mancante',
            'tecnico_id.exists' => 'Tecnico non trovato nel sistema'
        ]);

        if ($validator->fails()) {
            $message = 'Dati non validi: ' . $validator->errors()->first();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => $message,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            // STEP 3: RECUPERO E VERIFICA TECNICO
            $tecnico = User::findOrFail($request->tecnico_id);

            // Verifica che sia un tecnico
            if ($tecnico->livello_accesso != 2) {
                $message = "L'utente selezionato non è un tecnico";
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                
                return redirect()->back()->with('error', $message);
            }

            // STEP 4: VERIFICA APPARTENENZA AL CENTRO
            if ($tecnico->centro_assistenza_id != $centro->id) {
                $message = "Il tecnico non è assegnato a questo centro";
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                
                return redirect()->back()->with('error', $message);
            }

            // STEP 5: ESECUZIONE RIMOZIONE
            $nomeTecnico = $tecnico->nome_completo;             // Salva nome prima della modifica
            $tecnico->update(['centro_assistenza_id' => null]); // Imposta a NULL = non assegnato

            DB::commit();

            // STEP 6: LOGGING OPERAZIONE PER AUDIT
            Log::info('Tecnico rimosso da centro', [
                'tecnico_id' => $tecnico->id,
                'tecnico_nome' => $nomeTecnico,
                'centro_id' => $centro->id,
                'centro_nome' => $centro->nome,
                'removed_by' => Auth::id(),
                'admin_username' => Auth::user()->username
            ]);

            $successMessage = "Tecnico \"{$nomeTecnico}\" rimosso dal centro \"{$centro->nome}\"";

            // STEP 7: RESPONSE ADATTIVA PER TIPO RICHIESTA
            if ($request->expectsJson()) {
                // Response per richieste AJAX
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'tecnico_id' => $tecnico->id,
                    'tecnico_nome' => $nomeTecnico
                ]);
            }

            // Redirect per richieste web normali
            return redirect()->route('admin.centri.show', $centro)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            // STEP 8: ROLLBACK E ERROR HANDLING
            DB::rollBack();
            
            Log::error('Errore rimozione tecnico da centro', [
                'centro_id' => $centro->id,
                'tecnico_id' => $request->tecnico_id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'admin_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $errorMessage = 'Errore nella rimozione del tecnico dal centro';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    // ================================================
    // SEZIONE 5: API AMMINISTRATIVE AVANZATE (SOLO ADMIN)
    // ================================================

    /**
     * METODO GET TECNICI DISPONIBILI - LINGUAGGIO: PHP con Query Complessa
     * 
     * API per recuperare lista tecnici disponibili per assegnazione.
     * Esclude tecnici già assegnati al centro specifico.
     * 
     * FUNZIONALITÀ:
     * - Filtraggio tecnici per centro target
     * - Separazione tecnici liberi vs trasferibili
     * - Informazioni centro attuale per ogni tecnico
     * - Statistiche riassuntive
     * 
     * ROUTE: GET /api/admin/tecnici-disponibili?centro_id={id}
     * 
     * @param Request $request Parametro centro_id opzionale
     * @return \Illuminate\Http\JsonResponse Lista tecnici disponibili
     */
    public function getTecniciDisponibili(Request $request)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI ADMIN
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // STEP 2: ESTRAZIONE PARAMETRI
            $centroId = $request->get('centro_id');
            
            Log::info('API tecnici disponibili chiamata', [
                'centro_id' => $centroId,
                'admin_id' => Auth::id(),
                'request_url' => $request->fullUrl()
            ]);

            // STEP 3: QUERY BASE TECNICI (LIVELLO ACCESSO 2)
            $query = User::where('livello_accesso', '2')
                ->select([
                    'id', 'nome', 'cognome', 'username', 'specializzazione', 
                    'data_nascita', 'centro_assistenza_id', 'created_at'
                ])
                ->with(['centroAssistenza:id,nome,citta,provincia']);     // Eager loading centro attuale

            // STEP 4: FILTRO PER CENTRO TARGET
            // Se specificato centro_id, escludi tecnici già assegnati a quel centro
            if ($centroId) {
                $query->where(function($q) use ($centroId) {
                    $q->whereNull('centro_assistenza_id')               // Tecnici completamente liberi
                      ->orWhere('centro_assistenza_id', '!=', $centroId); // Trasferibili da altri centri
                });
            }

            // STEP 5: ESECUZIONE QUERY CON ORDINAMENTO
            $tecnici = $query->orderBy('nome')
                ->orderBy('cognome')
                ->get()
                ->map(function($tecnico) {
                    // STEP 6: TRASFORMAZIONE DATI PER API
                    // Determina stato centro attuale
                    $centroAttuale = null;
                    if ($tecnico->centroAssistenza) {
                        $centroAttuale = [
                            'id' => $tecnico->centroAssistenza->id,
                            'nome' => $tecnico->centroAssistenza->nome,
                            'citta' => $tecnico->centroAssistenza->citta,
                            'provincia' => $tecnico->centroAssistenza->provincia,
                            'status' => 'assigned'                      // Tecnico già assegnato
                        ];
                    } else {
                        $centroAttuale = [
                            'status' => 'unassigned'                    // Tecnico libero
                        ];
                    }

                    return [
                        'id' => $tecnico->id,
                        'nome_completo' => trim($tecnico->nome . ' ' . $tecnico->cognome),
                        'username' => $tecnico->username,
                        'specializzazione' => $tecnico->specializzazione ?? 'Generale',
                        'data_nascita' => $tecnico->data_nascita ? $tecnico->data_nascita->format('d/m/Y') : null,
                        'eta' => $tecnico->data_nascita ? $tecnico->data_nascita->age : null,
                        'centro_attuale' => $centroAttuale,
                        'creato_il' => $tecnico->created_at->format('d/m/Y'),
                        'disponibile' => !$tecnico->centro_assistenza_id   // true se libero
                    ];
                });

            // STEP 7: CALCOLO STATISTICHE
            $tecniciLiberi = $tecnici->where('disponibile', true);
            $tecniciAssegnati = $tecnici->where('disponibile', false);

            // STEP 8: RESPONSE JSON CON METADATI
            $response = [
                'success' => true,
                'tecnici' => $tecnici->values(),                        // Re-indicizza array
                'count' => $tecnici->count(),
                'statistiche' => [
                    'totali' => $tecnici->count(),
                    'liberi' => $tecniciLiberi->count(),
                    'trasferibili' => $tecniciAssegnati->count()
                ],
                'centro_id' => $centroId,
                'generato_il' => now()->toISOString()
            ];

            // STEP 9: LOGGING SUCCESSO
            Log::info('Tecnici disponibili caricati con successo', [
                'centro_id' => $centroId,
                'tecnici_trovati' => $tecnici->count(),
                'liberi' => $tecniciLiberi->count(),
                'trasferibili' => $tecniciAssegnati->count()
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            // STEP 10: ERROR HANDLING
            Log::error('Errore caricamento tecnici disponibili', [
                'centro_id' => $request->get('centro_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei tecnici disponibili',
                'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
            ], 500);
        }
    }

    /**
     * METODO GET DETTAGLI TECNICO - LINGUAGGIO: PHP con Data Enrichment
     * 
     * API per informazioni dettagliate su un tecnico specifico.
     * Include relazioni, calcoli età e stato assegnazione.
     * 
     * ROUTE: GET /api/admin/tecnici/{user}
     * 
     * @param User $user Model binding del tecnico
     * @return \Illuminate\Http\JsonResponse Dettagli completi tecnico
     */
    public function getDettagliTecnico(User $user)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // STEP 2: VERIFICA CHE SIA UN TECNICO
        if ($user->livello_accesso != '2') {
            return response()->json([
                'success' => false,
                'message' => 'L\'utente selezionato non è un tecnico'
            ], 422);
        }

        try {
            // STEP 3: CARICA RELAZIONI
            $user->load('centroAssistenza');

            // STEP 4: PREPARA DATI DETTAGLIATI CON ENRICHMENT
            $dettagli = [
                'id' => $user->id,
                'nome_completo' => $user->nome_completo,                // Accessor del model
                'username' => $user->username,
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                'data_nascita' => $user->data_nascita ? $user->data_nascita->format('d/m/Y') : null,
                'eta' => $user->data_nascita ? $user->data_nascita->age : null,     // Calcolo automatico età
                'email' => $user->email,
                'ultimo_accesso' => $user->last_login_at ? 
                    $user->last_login_at->diffForHumans() : 'Mai',     // Formato human-readable
                'registrato_il' => $user->created_at->format('d/m/Y H:i'),
                'centro_assegnato' => $user->centroAssistenza ? [
                    'id' => $user->centroAssistenza->id,
                    'nome' => $user->centroAssistenza->nome,
                    'citta' => $user->centroAssistenza->citta,
                    'provincia' => $user->centroAssistenza->provincia
                ] : null,
                'stato' => $user->centro_assistenza_id ? 'Assegnato' : 'Disponibile'
            ];

            return response()->json([
                'success' => true,
                'tecnico' => $dettagli
            ]);

        } catch (\Exception $e) {
            // STEP 5: ERROR HANDLING
            Log::error('Errore dettagli tecnico', [
                'tecnico_id' => $user->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei dettagli'
            ], 500);
        }
    }

    /**
     * METODO GET STATISTICHE CENTRO - LINGUAGGIO: PHP con Analytics
     * 
     * API per statistiche avanzate di un centro specifico.
     * Include metriche temporali, confronti geografici e analisi tecnici.
     * 
     * ROUTE: GET /api/admin/centri/{centro}/statistiche
     * 
     * @param CentroAssistenza $centro Centro da analizzare
     * @return \Illuminate\Http\JsonResponse Statistiche complete centro
     */
    public function getStatisticheCentro(CentroAssistenza $centro)
    {
        // STEP 1: VERIFICA AUTORIZZAZIONI
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // STEP 2: CARICA RELAZIONI NECESSARIE
            $centro->load('tecnici');

            // STEP 3: CALCOLO STATISTICHE COMPARATIVE
            // Tecnici nella stessa provincia per confronti
            $tecniciProvincia = User::whereHas('centroAssistenza', function($q) use ($centro) {
                $q->where('provincia', $centro->provincia);
            })->where('livello_accesso', '2')->count();

            // Altri centri nella stessa provincia
            $centriStessaProvincia = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->count();

            // STEP 4: CALCOLO METRICHE TEMPORALI
            $giorniAttivita = $centro->created_at->diffInDays(now());
            $settimaneAttivita = max(1, floor($giorniAttivita / 7));

            // STEP 5: ASSEMBLY STATISTICHE COMPLETE
            $stats = [
                // Dati base centro
                'info_centro' => [
                    'nome' => $centro->nome,
                    'citta' => $centro->citta,
                    'provincia' => $centro->provincia,
                    'creato_il' => $centro->created_at->format('d/m/Y')
                ],
                
                // Statistiche tecnici con analisi
                'tecnici' => [
                    'assegnati_centro' => $centro->tecnici->count(),
                    'totale_provincia' => $tecniciProvincia,
                    'media_per_centro_provincia' => $centriStessaProvincia > 0 ? 
                        round($tecniciProvincia / ($centriStessaProvincia + 1), 1) : $centro->tecnici->count(),
                    // Analisi specializzazioni con conteggio
                    'specializzazioni' => $centro->tecnici->whereNotNull('specializzazione')
                        ->pluck('specializzazione')
                        ->countBy()                                     // Conta occorrenze per specializzazione
                        ->toArray()
                ],
                
                // Contesto geografico per benchmarking
                'contesto_geografico' => [
                    'centri_stessa_provincia' => $centriStessaProvincia,
                    'posizione_in_provincia' => CentroAssistenza::where('provincia', $centro->provincia)
                        ->where('created_at', '<=', $centro->created_at)
                        ->count()                                       // Posizione cronologica creazione
                ],
                
                // Metriche temporali di attività
                'metriche_temporali' => [
                    'giorni_attivo' => $giorniAttivita,
                    'settimane_attivo' => $settimaneAttivita,
                    'ultimo_aggiornamento' => $centro->updated_at->diffForHumans()
                ]
            ];

            return response()->json([
                'success' => true,
                'statistiche' => $stats,
                'generato_il' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            // STEP 6: ERROR HANDLING
            Log::error('Errore statistiche centro', [
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche'
            ], 500);
        }
    }

    /**
     * METODO GET AVAILABLE TECNICI - LINGUAGGIO: PHP con Filtering Logic
     * 
     * API specifica per tecnici disponibili per un centro.
     * Utilizzata per popolare interfacce di assegnazione.
     * 
     * ROUTE: GET /api/admin/centri/{centro}/tecnici-disponibili
     * 
     * @param CentroAssistenza $centro Centro target per assegnazione
     * @return \Illuminate\Http\JsonResponse Tecnici disponibili per centro
     */
    public function getAvailableTecnici(CentroAssistenza $centro)
    {
        try {
            // STEP 1: VERIFICA AUTORIZZAZIONI
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }

            // STEP 2: LOGGING RICHIESTA
            Log::info('Caricamento tecnici disponibili per centro', [
                'centro_id' => $centro->id,
                'centro_nome' => $centro->nome,
                'admin_id' => Auth::id()
            ]);

            // STEP 3: QUERY TECNICI CON FILTRO CENTRO
            $query = User::where('livello_accesso', 2)
                ->select([
                    'id', 'nome', 'cognome', 'specializzazione', 
                    'data_nascita', 'centro_assistenza_id'
                ])
                ->with(['centroAssistenza:id,nome,citta,provincia']);

            // STEP 4: ESCLUSIONE TECNICI GIÀ ASSEGNATI AL CENTRO TARGET
            $query->where(function($q) use ($centro) {
                $q->whereNull('centro_assistenza_id')                   // Tecnici completamente liberi
                  ->orWhere('centro_assistenza_id', '!=', $centro->id); // Trasferibili da altri centri
            });

            // STEP 5: ESECUZIONE E TRASFORMAZIONE
            $tecnici = $query->orderBy('nome')
                ->orderBy('cognome')
                ->get()
                ->map(function($tecnico) {
                    return [
                        'id' => $tecnico->id,
                        'nome_completo' => $tecnico->nome_completo,     // Accessor model
                        'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                        'eta' => $tecnico->eta,                         // Accessor model per età
                        'centro_attuale' => $tecnico->centro_assistenza_id ? [
                            'id' => $tecnico->centroAssistenza->id,
                            'nome' => $tecnico->centroAssistenza->nome,
                            'citta' => $tecnico->centroAssistenza->citta,
                            'status' => 'assigned'                      // Tecnico già assegnato
                        ] : [
                            'status' => 'unassigned'                    // Tecnico libero
                        ]
                    ];
                });

            // STEP 6: RESPONSE CON METADATI CENTRO
            return response()->json([
                'success' => true,
                'tecnici' => $tecnici,
                'count' => $tecnici->count(),
                'centro_target' => [
                    'id' => $centro->id,
                    'nome' => $centro->nome,
                    'citta' => $centro->citta
                ]
            ]);

        } catch (\Exception $e) {
            // STEP 7: ERROR HANDLING
            Log::error('Errore caricamento tecnici disponibili per centro', [
                'error' => $e->getMessage(),
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei tecnici disponibili',
                'error' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
            ], 500);
        }
    }

    // ================================================
    // SEZIONE 6: METODI UTILITY E HELPER
    // ================================================

    /**
     * METODO HELPER VALIDAZIONE CENTRO - LINGUAGGIO: PHP con DRY Principle
     * 
     * Centralizza la validazione dati centro per evitare duplicazione codice.
     * Utilizzato sia in create che update con parametri dinamici.
     * 
     * @param \Illuminate\Http\Request $request Dati da validare
     * @param int|null $centroId ID centro per esclusione in update
     * @return \Illuminate\Validation\Validator Validator instance
     */
    private function validazioneCentro($request, $centroId = null)
    {
        // STEP 1: DEFINIZIONE REGOLE VALIDAZIONE
        $rules = [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome' . ($centroId ? ",$centroId" : ''),
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'required|string|regex:/^[0-9]{5}$/',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:centri_assistenza,email' . ($centroId ? ",$centroId" : '')
        ];

        // STEP 2: MESSAGGI ERRORE PERSONALIZZATI
        $messages = [
            'nome.unique' => 'Esiste già un centro con questo nome',
            'email.unique' => 'Email già utilizzata da un altro centro',
            'cap.regex' => 'Il CAP deve essere di 5 cifre numeriche',
            'provincia.size' => 'La provincia deve essere di 2 caratteri (es: AN)',
            'telefono.max' => 'Il numero di telefono è troppo lungo'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * METODO HELPER LOG ATTIVITÀ ADMIN - LINGUAGGIO: PHP con Centralized Logging
     * 
     * Centralizza il logging delle operazioni amministrative.
     * Assicura consistenza nel formato log per audit trail.
     * 
     * @param string $azione Descrizione azione eseguita
     * @param array $dettagli Dettagli aggiuntivi per il log
     * @return void
     */
    private function logAttivitaAdmin($azione, $dettagli = [])
    {
        if (Auth::check()) {
            Log::info("Admin: $azione", array_merge([
                'admin_id' => Auth::id(),
                'admin_username' => Auth::user()->username,
                'timestamp' => now()->toISOString()
            ], $dettagli));
        }
    }

    /**
     * METODO HELPER JSON RESPONSE - LINGUAGGIO: PHP con Standard Response Format
     * 
     * Standardizza il formato delle response JSON API.
     * Assicura consistenza nelle risposte per frontend.
     * 
     * @param bool $success Esito operazione
     * @param string $message Messaggio per utente
     * @param array $data Dati aggiuntivi response
     * @param int $statusCode Codice HTTP status
     * @return \Illuminate\Http\JsonResponse Response standardizzata
     */
    private function jsonResponse($success, $message, $data = [], $statusCode = 200)
    {
        // STEP 1: STRUTTURA BASE RESPONSE
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        // STEP 2: MERGE DATI AGGIUNTIVI
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * METODO HELPER VERIFICA ADMIN - LINGUAGGIO: PHP con Quick Auth Check
     * 
     * Verifica rapida autorizzazioni amministratore.
     * Utilizzabile in metodi che richiedono controllo veloce.
     * 
     * @return bool True se utente è admin autenticato
     */
    private function verificaAdmin()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * METODO HELPER STATISTICHE CENTRO - LINGUAGGIO: PHP con Metrics Calculation
     * 
     * Calcola metriche base per dashboard centro.
     * Include conteggi, date e flag completezza dati.
     * 
     * @param CentroAssistenza $centro Centro da analizzare
     * @return array Metriche calcolate
     */
    private function calcolaStatisticheCentro(CentroAssistenza $centro)
    {
        return [
            'tecnici_assegnati' => $centro->tecnici()->count(),
            'giorni_attivo' => $centro->created_at->diffInDays(now()),
            'ultima_modifica' => $centro->updated_at->diffForHumans(),
            'ha_contatti_completi' => !empty($centro->telefono) && !empty($centro->email)
        ];
    }

    /**
     * METODO HELPER FORMATO INDIRIZZO - LINGUAGGIO: PHP con String Manipulation
     * 
     * Formatta indirizzo completo user-friendly.
     * Concatena componenti indirizzo in formato leggibile.
     * 
     * @param CentroAssistenza $centro Centro con dati indirizzo
     * @return string Indirizzo formattato
     */
    private function formatIndirizzoCompleto($centro)
    {
        $parti = [];
        
        // STEP 1: RACCOLTA COMPONENTI NON VUOTI
        if ($centro->indirizzo) $parti[] = $centro->indirizzo;
        if ($centro->citta) $parti[] = $centro->citta;
        if ($centro->cap) $parti[] = $centro->cap;
        if ($centro->provincia) $parti[] = "({$centro->provincia})";
        
        // STEP 2: CONCATENAZIONE CON SEPARATORI
        return implode(', ', $parti);
    }
}