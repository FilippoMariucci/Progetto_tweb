<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\CentroAssistenza;
use Carbon\Carbon;

/**
 * CONTROLLER AMMINISTRATIVO AVANZATO - LINGUAGGIO: PHP con Laravel Framework
 * 
 * Questo controller gestisce tutte le funzionalità avanzate riservate agli amministratori (livello 4)
 * del sistema di assistenza tecnica. Include:
 * - Assegnazione prodotti al personale staff (livello 3)
 * - Statistiche avanzate del sistema
 * - Manutenzione e ottimizzazione del sistema
 * - Export dei dati in vari formati
 * - API endpoints per aggiornamenti AJAX in tempo reale
 * 
 * ARCHITETTURA MVC: Questo è il "Controller" che gestisce la logica di business
 * MIDDLEWARE: Verifica autorizzazioni con 'check.level:4' (solo amministratori)
 * DATABASE: Utilizza Eloquent ORM di Laravel per interagire con MySQL
 */
class AdminController extends Controller
{
    /**
     * COSTRUTTORE - LINGUAGGIO: PHP
     * 
     * Viene eseguito automaticamente quando viene istanziato il controller.
     * Applica il middleware di sicurezza per verificare che solo gli utenti
     * autenticati con livello di accesso 4 (amministratori) possano accedere
     * a TUTTE le funzioni di questo controller.
     * 
     * MIDDLEWARE CHAIN:
     * 1. 'auth' -> Verifica che l'utente sia autenticato (logged in)
     * 2. 'check.level:4' -> Verifica che il livello_accesso dell'utente sia 4
     * 
     * Se i controlli falliscono, Laravel reindirizza automaticamente
     * alla pagina di login o restituisce errore 403 Forbidden
     */
    public function __construct()
    {
        // Applica due middleware in sequenza per la sicurezza
        $this->middleware(['auth', 'check.level:4']);
    }

    // ================================================
    // SEZIONE 1: GESTIONE ASSEGNAZIONI PRODOTTI A STAFF
    // ================================================

    /**
     * METODO ASSEGNAZIONI - LINGUAGGIO: PHP con Eloquent ORM
     * 
     * Mostra la pagina principale per gestire l'assegnazione dei prodotti 
     * ai membri dello staff aziendale (livello 3).
     * 
     * FUNZIONALITÀ:
     * - Visualizza tutti i prodotti con filtri avanzati
     * - Permette di cercare per nome/modello prodotto
     * - Filtra per categoria, staff assegnato, prodotti non assegnati
     * - Paginazione automatica (20 prodotti per pagina)
     * - Statistiche di riepilogo delle assegnazioni
     * 
     * PARAMETRI REQUEST (via GET):
     * - search: termine di ricerca per nome/modello
     * - staff_id: filtra per staff specifico ('null' per non assegnati)
     * - categoria: filtra per categoria prodotto
     * - non_assegnati: booleano per mostrare solo prodotti senza staff
     * 
     * @param Request $request Oggetto che contiene tutti i parametri della richiesta HTTP
     * @return \Illuminate\View\View Ritorna la vista 'admin.assegnazioni' con i dati
     */
    public function assegnazioni(Request $request)
    {
        // STEP 1: Carica tutti i membri dello staff (livello 3) ordinati alfabeticamente
        // ELOQUENT: where() crea una query SQL con WHERE livello_accesso = '3'
        // orderBy() aggiunge ORDER BY nome, cognome alla query
        // get() esegue la query e ritorna una Collection di modelli User
        $staffMembers = User::where('livello_accesso', '3')
            ->orderBy('nome')
            ->orderBy('cognome')
            ->get();

        // STEP 2: Inizializza query builder per prodotti con relazione staff
        // ELOQUENT: with() esegue un "eager loading" della relazione staffAssegnato
        // Questo evita il problema N+1 queries caricando subito i dati dello staff
        $query = Prodotto::with('staffAssegnato');

        // STEP 3: Applica filtri basati sui parametri della richiesta
        
        // FILTRO: Prodotti non assegnati
        // boolean() converte il parametro in booleano (true/false)
        if ($request->boolean('non_assegnati')) {
            // whereNull() aggiunge WHERE staff_assegnato_id IS NULL
            $query->whereNull('staff_assegnato_id');
        }

        // FILTRO: Staff specifico
        // filled() verifica che il parametro non sia vuoto o null
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            if ($staffId === 'null') {
                // Filtra prodotti non assegnati (quando si seleziona "Non assegnati" nel dropdown)
                $query->whereNull('staff_assegnato_id');
            } else {
                // Filtra prodotti assegnati a uno staff specifico
                // where() aggiunge WHERE staff_assegnato_id = $staffId
                $query->where('staff_assegnato_id', $staffId);
            }
        }

        // FILTRO: Categoria prodotto
        if ($request->filled('categoria')) {
            // Filtra per categoria specifica
            $query->where('categoria', $request->input('categoria'));
        }

        // FILTRO: Ricerca testuale per nome/modello
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // CLOSURE QUERY: where(function) crea una sotto-query con parentesi
            // LIKE operator con % per ricerca parziale (SQL: WHERE nome LIKE '%term%')
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('modello', 'LIKE', "%{$searchTerm}%");
            });
        }

        // STEP 4: Esegue la query con conteggi aggiuntivi e paginazione
        // withCount() aggiunge campi conteggio senza caricare tutti i record correlati
        // malfunzionamenti_count: conta tutti i malfunzionamenti del prodotto
        // critici_count: conta solo malfunzionamenti con gravita = 'critica'
        // paginate(20): divide i risultati in pagine da 20 elementi
        $prodotti = $query->withCount([
                'malfunzionamenti',
                'malfunzionamenti as critici_count' => function($query) {
                    $query->where('gravita', 'critica');
                }
            ])
            ->paginate(20);

        // STEP 5: Calcola statistiche per il dashboard
        // Ogni count() esegue una query SELECT COUNT(*) ottimizzata
        $stats = [
            'totale_prodotti' => Prodotto::count(),                              // Totale prodotti nel sistema
            'prodotti_assegnati' => Prodotto::whereNotNull('staff_assegnato_id')->count(),  // Prodotti con staff
            'prodotti_non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(), // Prodotti senza staff
            'staff_attivi' => $staffMembers->count(),                            // Numero membri staff disponibili
        ];

        // STEP 6: Carica categorie uniche per il filtro dropdown
        // getCategorieUnifico() è un metodo personalizzato nel model Prodotto
        // che ritorna array delle categorie presenti nel database
        $categorie = Prodotto::getCategorieUnifico();

        // STEP 7: Logging per debug e audit
        // Log::info() scrive nel file di log di Laravel (storage/logs/laravel.log)
        // Utile per tracciare le azioni degli amministratori e debug
        Log::info('Pagina assegnazioni caricata', [
            'admin_id' => Auth::id(),                                           // ID dell'admin che ha fatto la richiesta
            'filtri_applicati' => $request->only(['search', 'staff_id', 'categoria', 'non_assegnati']), // Parametri utilizzati
            'prodotti_trovati' => $prodotti->total(),                          // Numero totale risultati
            'staff_disponibili' => $staffMembers->count()                      // Numero staff disponibili
        ]);

        // STEP 8: Ritorna la vista con tutti i dati necessari
        // compact() crea un array associativo con le variabili specificate
        // Laravel passa questi dati alla vista Blade 'admin.assegnazioni'
        return view('admin.assegnazioni', compact(
            'prodotti', 'staffMembers', 'stats', 'categorie'
        ));
    }

    /**
     * METODO ASSEGNA PRODOTTO - LINGUAGGIO: PHP con Validazione Laravel
     * 
     * Gestisce l'assegnazione di un singolo prodotto a un membro dello staff.
     * Può anche rimuovere un'assegnazione esistente se staff_id è null.
     * 
     * PROCESSO:
     * 1. Valida i dati in input
     * 2. Verifica che lo staff sia di livello 3
     * 3. Aggiorna il database
     * 4. Registra l'operazione nei log
     * 5. Ritorna messaggio di successo o errore
     * 
     * @param Request $request Contiene prodotto_id e staff_id (opzionale)
     * @return \Illuminate\Http\RedirectResponse Redirect alla pagina precedente con messaggio
     */
    public function assegnaProdotto(Request $request)
    {
        // STEP 1: Validazione input con regole Laravel
        // validate() controlla automaticamente i dati e ritorna errori se non validi
        $request->validate([
            'prodotto_id' => 'required|exists:prodotti,id',      // Obbligatorio e deve esistere nella tabella prodotti
            'staff_id' => 'nullable|exists:users,id',            // Opzionale ma se presente deve esistere in users
        ], [
            // Messaggi di errore personalizzati in italiano
            'prodotto_id.required' => 'ID prodotto non specificato',
            'prodotto_id.exists' => 'Il prodotto selezionato non esiste',
            'staff_id.exists' => 'Il membro dello staff selezionato non esiste'
        ]);

        // STEP 2: Gestione con try-catch per errori imprevisti
        try {
            // Trova il prodotto usando findOrFail (lancia eccezione se non trovato)
            $prodotto = Prodotto::findOrFail($request->prodotto_id);
            
            // Se staff_id è vuoto o null, usiamo null per rimuovere l'assegnazione
            $staffId = $request->staff_id ?: null;

            // STEP 3: Verifica aggiuntiva del livello staff
            if ($staffId) {
                $staff = User::findOrFail($staffId);
                // Controllo di sicurezza: solo utenti livello 3 possono essere assegnati
                if ($staff->livello_accesso !== '3') {
                    return back()->withErrors([
                        'staff_id' => 'L\'utente selezionato non è un membro dello staff aziendale'
                    ]);
                }
            }

            // STEP 4: Salva i dati precedenti per il log di audit
            $staffPrecedente = $prodotto->staffAssegnato;

            // STEP 5: Aggiorna il database
            // update() esegue SQL: UPDATE prodotti SET staff_assegnato_id = ? WHERE id = ?
            $prodotto->update(['staff_assegnato_id' => $staffId]);

            // STEP 6: Prepara messaggio di successo personalizzato
            if ($staffId) {
                $nuovoStaff = User::find($staffId);
                // nome_completo è un accessor nel model User che concatena nome + cognome
                $message = "Prodotto \"{$prodotto->nome}\" assegnato a {$nuovoStaff->nome_completo} con successo";
            } else {
                $message = "Assegnazione rimossa dal prodotto \"{$prodotto->nome}\" con successo";
            }

            // STEP 7: Log completo dell'operazione per audit trail
            // Importante per tracciare chi ha fatto cosa e quando
            Log::info('Assegnazione prodotto modificata', [
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'staff_precedente_id' => $staffPrecedente?->id,          // Operatore null-safe (?->)
                'staff_precedente_nome' => $staffPrecedente?->nome_completo,
                'nuovo_staff_id' => $staffId,
                'nuovo_staff_nome' => $staffId ? $nuovoStaff->nome_completo : null,
                'admin_id' => Auth::id(),                                // Chi ha fatto la modifica
                'admin_username' => Auth::user()->username,
                'timestamp' => now()                                     // Timestamp preciso dell'operazione
            ]);

            // STEP 8: Redirect con messaggio di successo
            // back() ritorna alla pagina precedente, with() passa dati alla sessione
            return back()->with('success', $message);

        } catch (\Exception $e) {
            // STEP 9: Gestione errori con logging
            Log::error('Errore nell\'assegnazione prodotto', [
                'prodotto_id' => $request->prodotto_id,
                'staff_id' => $request->staff_id,
                'error_message' => $e->getMessage(),                    // Messaggio dell'eccezione
                'admin_id' => Auth::id()
            ]);

            // Ritorna errore user-friendly senza esporre dettagli tecnici
            return back()->withErrors([
                'assegnazione' => 'Errore durante l\'assegnazione. Riprova o contatta l\'amministratore.'
            ]);
        }
    }

    // ================================================
    // SEZIONE 2: STATISTICHE AVANZATE DEL SISTEMA
    // ================================================

    /**
     * METODO STATISTICHE GENERALI - LINGUAGGIO: PHP con Query Aggregate
     * 
     * Genera statistiche complete del sistema per il dashboard amministrativo.
     * Include contatori, crescita nel tempo, distribuzione dati e top performers.
     * 
     * CARATTERISTICHE:
     * - Periodo di analisi configurabile (default 30 giorni)
     * - Query aggregate ottimizzate per performance
     * - Dati per grafici e dashboard
     * - Informazioni su trend e crescita
     * 
     * @param Request $request Parametro 'periodo' per definire giorni di analisi
     * @return \Illuminate\View\View Vista con tutte le statistiche elaborate
     */
    public function statisticheGenerali(Request $request)
    {
        // STEP 1: Configurazione periodo di analisi
        // input() con valore default se parametro non presente
        $periodo = $request->input('periodo', 30);
        // Carbon::now() crea oggetto data corrente, subDays() sottrae giorni
        $dataInizio = now()->subDays($periodo);

        // STEP 2: Statistiche generali con contatori base
        // Ogni count() esegue SELECT COUNT(*) ottimizzato
        $stats = [
            // === CONTATORI PRINCIPALI ===
            'utenti_totali' => User::count(),                              // Tutti gli utenti registrati
            'prodotti_totali' => Prodotto::count(),                        // Tutti i prodotti in catalogo
            'malfunzionamenti_totali' => Malfunzionamento::count(),        // Tutte le soluzioni tecniche
            'centri_totali' => CentroAssistenza::count(),                  // Centri assistenza registrati

            // === CRESCITA NEL PERIODO ===
            // where() con confronto date: WHERE created_at >= ?
            'nuovi_utenti' => User::where('created_at', '>=', $dataInizio)->count(),
            'nuovi_prodotti' => Prodotto::where('created_at', '>=', $dataInizio)->count(),
            'nuove_soluzioni' => Malfunzionamento::where('created_at', '>=', $dataInizio)->count(),

            // === ATTIVITÀ RECENTE ===
            // last_login_at è un campo che traccia l'ultimo accesso utente
            'utenti_attivi' => User::where('last_login_at', '>=', $dataInizio)->count(),
            // updated_at traccia quando una soluzione è stata modificata
            'soluzioni_aggiornate' => Malfunzionamento::where('updated_at', '>=', $dataInizio)->count(),
        ];

        // STEP 3: Distribuzione utenti per livello con GROUP BY
        // selectRaw() permette di scrivere SQL personalizzato
        // GROUP BY livello_accesso raggruppa per livello di accesso
        // pluck() crea array associativo [livello => count]
        $distribuzioneUtenti = User::selectRaw('livello_accesso, COUNT(*) as count')
            ->groupBy('livello_accesso')
            ->orderBy('livello_accesso')
            ->get()
            ->pluck('count', 'livello_accesso')        // Converte in array [livello => numero]
            ->toArray();

        // STEP 4: Prodotti raggruppati per categoria
        // whereNotNull() esclude categorie vuote
        // ORDER BY count DESC ordina per frequenza
        $prodottiPerCategoria = Prodotto::selectRaw('categoria, COUNT(*) as count')
            ->whereNotNull('categoria')
            ->groupBy('categoria')
            ->orderBy('count', 'desc')                 // Categorie più popolose prima
            ->get()
            ->pluck('count', 'categoria')
            ->toArray();

        // STEP 5: Malfunzionamenti raggruppati per gravità
        // Importante per identificare problemi critici
        $malfunzionamentiPerGravita = Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
            ->whereNotNull('gravita')
            ->groupBy('gravita')
            ->get()
            ->pluck('count', 'gravita')
            ->toArray();

        // STEP 6: Top 10 prodotti più problematici
        // withCount() aggiunge campo malfunzionamenti_count senza caricare i record
        // having() filtra dopo GROUP BY (SQL: HAVING malfunzionamenti_count > 0)
        $prodottiProblematici = Prodotto::withCount('malfunzionamenti')
            ->having('malfunzionamenti_count', '>', 0)
            ->orderBy('malfunzionamenti_count', 'desc')
            ->limit(10)                                // Solo i primi 10
            ->get();

        // STEP 7: Staff più attivi nel periodo
        // Conta quante soluzioni ha creato ogni membro staff nel periodo
        $staffAttivi = User::where('livello_accesso', '3')
            ->withCount(['malfunzionamentiCreati' => function($q) use ($dataInizio) {
                $q->where('created_at', '>=', $dataInizio);
            }])
            ->orderBy('malfunzionamenti_creati_count', 'desc')
            ->limit(10)
            ->get();

        // STEP 8: Dati per grafici di crescita temporale
        // DATE() estrae solo la parte data da datetime
        // Questi dati vengono usati per creare grafici nell'interfaccia
        $crescitaUtenti = User::selectRaw('DATE(created_at) as data, COUNT(*) as count')
            ->where('created_at', '>=', $dataInizio)
            ->groupBy('data')
            ->orderBy('data')                          // Ordine cronologico
            ->get();

        $crescitaSoluzioni = Malfunzionamento::selectRaw('DATE(created_at) as data, COUNT(*) as count')
            ->where('created_at', '>=', $dataInizio)
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // STEP 9: Ritorna vista con tutti i dati elaborati
        // compact() crea array con tutte le variabili per la vista
        return view('admin.statistiche', compact(
            'stats', 'distribuzioneUtenti', 'prodottiPerCategoria',
            'malfunzionamentiPerGravita', 'prodottiProblematici',
            'staffAttivi', 'crescitaUtenti', 'crescitaSoluzioni', 'periodo'
        ));
    }

    // ================================================
    // SEZIONE 3: MANUTENZIONE E OTTIMIZZAZIONE SISTEMA
    // ================================================

    /**
     * METODO MANUTENZIONE - LINGUAGGIO: PHP con System Functions
     * 
     * Mostra la pagina di manutenzione sistema con informazioni tecniche
     * e strumenti per la gestione del sistema.
     * 
     * INFORMAZIONI MOSTRATE:
     * - Versioni software (Laravel, PHP, Server)
     * - Stato delle cache
     * - Utilizzo storage
     * - File di log disponibili
     * - Connessione database
     * 
     * @return \Illuminate\View\View Vista manutenzione con info sistema
     */
    public function manutenzione()
    {
        // STEP 1: Raccolta informazioni di sistema
        // Ogni metodo helper raccoglie dati specifici del sistema
        $systemInfo = [
            'laravel_version' => app()->version(),                         // Versione Laravel framework
            'php_version' => PHP_VERSION,                                  // Versione PHP del server
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',    // Software server web (Apache/Nginx)
            'database_version' => $this->getDatabaseVersion(),             // Versione MySQL/MariaDB
            'storage_usage' => $this->getStorageUsage(),                   // Spazio disco utilizzato
            'log_files' => $this->getLogFiles(),                          // Lista file di log disponibili
        ];

        // STEP 2: Verifica stato cache di Laravel
        // Laravel ha diversi tipi di cache che possono essere attivate/disattivate
        $cacheStatus = [
            'config' => $this->isCacheEnabled('config'),                  // Cache configurazioni
            'route' => $this->isCacheEnabled('route'),                    // Cache rotte
            'view' => $this->isCacheEnabled('view'),                      // Cache viste Blade
        ];

        // STEP 3: Ritorna vista con tutte le informazioni
        return view('admin.manutenzione', compact('systemInfo', 'cacheStatus'));
    }

    /**
     * METODO CLEAR CACHE - LINGUAGGIO: PHP con Artisan Commands
     * 
     * Pulisce le cache di Laravel usando i comandi Artisan.
     * Diversi tipi di cache possono essere puliti individualmente o tutti insieme.
     * 
     * TIPI DI CACHE:
     * - application: Cache generale dell'applicazione
     * - config: Cache file di configurazione
     * - route: Cache delle rotte
     * - view: Cache dei template Blade
     * 
     * @param Request $request Parametro 'type' specifica quale cache pulire
     * @return \Illuminate\Http\RedirectResponse Redirect con messaggio risultato
     */
    public function clearCache(Request $request)
    {
        // STEP 1: Validazione tipo di cache da pulire
        $request->validate([
            'type' => 'required|in:all,config,route,view,application'
        ]);

        $type = $request->input('type');
        $cleared = [];  // Array per tracciare cosa è stato pulito

        // STEP 2: Esecuzione comandi con gestione errori
        try {
            // Switch per diversi tipi di pulizia cache
            switch ($type) {
                case 'all':
                    // Pulisce tutte le cache in sequenza
                    Artisan::call('cache:clear');        // Cache applicazione
                    Artisan::call('config:clear');       // Cache configurazioni
                    Artisan::call('route:clear');        // Cache rotte
                    Artisan::call('view:clear');         // Cache viste
                    $cleared = ['application', 'config', 'route', 'view'];
                    break;

                case 'config':
                    // Solo cache configurazioni
                    Artisan::call('config:clear');
                    $cleared = ['config'];
                    break;

                case 'route':
                    // Solo cache rotte
                    Artisan::call('route:clear');
                    $cleared = ['route'];
                    break;

                case 'view':
                    // Solo cache template Blade
                    Artisan::call('view:clear');
                    $cleared = ['view'];
                    break;

                case 'application':
                    // Solo cache generale applicazione
                    Artisan::call('cache:clear');
                    $cleared = ['application'];
                    break;
            }

            // STEP 3: Log dell'operazione per audit
            Log::info('Cache pulite dall\'admin', [
                'type' => $type,
                'cleared' => $cleared,
                'admin_id' => Auth::id()
            ]);

            // STEP 4: Messaggio di successo con dettagli
            return back()->with('success', 'Cache ' . implode(', ', $cleared) . ' pulite con successo');

        } catch (\Exception $e) {
            // STEP 5: Gestione errori durante pulizia
            Log::error('Errore pulizia cache', [
                'type' => $type,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['cache' => 'Errore nella pulizia cache: ' . $e->getMessage()]);
        }
    }

    /**
     * METODO OPTIMIZE DATABASE - LINGUAGGIO: PHP con Raw SQL
     * 
     * Ottimizza tutte le tabelle del database MySQL per migliorare le performance.
     * Utilizza il comando SQL OPTIMIZE TABLE per ogni tabella.
     * 
     * PROCESSO:
     * 1. Trova tutte le tabelle del database
     * 2. Esegue OPTIMIZE TABLE per ognuna
     * 3. Traccia risultati nel log
     * 4. Ritorna sommario dell'operazione
     * 
     * @return \Illuminate\Http\RedirectResponse Redirect con risultato operazione
     */
    public function optimizeDatabase()
    {
        try {
            // STEP 1: Recupera lista di tutte le tabelle del database
            // SHOW TABLES è un comando MySQL che lista tutte le tabelle
            $tables = DB::select("SHOW TABLES");
            $optimized = [];  // Array per tracciare tabelle ottimizzate

            // STEP 2: Ottimizza ogni tabella individualmente
            foreach ($tables as $table) {
                // array_values() estrae il primo valore dell'oggetto result
                $tableName = array_values((array) $table)[0];
                
                // OPTIMIZE TABLE riorganizza i dati e ricostruisce gli indici
                // Migliora performance per tabelle con molti INSERT/UPDATE/DELETE
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimized[] = $tableName;
            }

            // STEP 3: Log dell'operazione completa
            Log::info('Database ottimizzato dall\'admin', [
                'tables_optimized' => count($optimized),
                'admin_id' => Auth::id()
            ]);

            // STEP 4: Messaggio di successo con statistiche
            return back()->with('success', 'Database ottimizzato con successo. Tabelle processate: ' . count($optimized));

        } catch (\Exception $e) {
            // STEP 5: Gestione errori durante ottimizzazione
            Log::error('Errore ottimizzazione database', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['database' => 'Errore nell\'ottimizzazione: ' . $e->getMessage()]);
        }
    }

    // ================================================
    // SEZIONE 4: EXPORT DATI DEL SISTEMA
    // ================================================

    /**
     * METODO EXPORT - LINGUAGGIO: PHP
     * 
     * Mostra la pagina per l'export dei dati del sistema.
     * Presenta statistiche sui dati disponibili e opzioni di export.
     * 
     * @return \Illuminate\View\View Vista export con statistiche dati
     */
    public function export()
    {
        // Statistiche rapide sui dati disponibili per export
        // Aiuta l'admin a capire la quantità di dati che esporterà
        $exportStats = [
            'utenti' => User::count(),
            'prodotti' => Prodotto::count(),
            'malfunzionamenti' => Malfunzionamento::count(),
            'centri' => CentroAssistenza::count(),
        ];

        return view('admin.export', compact('exportStats'));
    }

    /**
     * METODO EXPORT ALL - LINGUAGGIO: PHP con JSON/CSV Generation
     * 
     * Esporta tutti i dati del sistema in formato JSON o CSV.
     * Include opzione per dati sensibili e metadati dell'export.
     * 
     * FORMATI SUPPORTATI:
     * - JSON: Struttura completa con tutte le relazioni
     * - CSV: Export semplificato (una tabella alla volta)
     * 
     * OPZIONI:
     * - include_sensitive: Include dati sensibili come date di nascita
     * - format: json o csv
     * 
     * @param Request $request Parametri: format, include_sensitive
     * @return \Symfony\Component\HttpFoundation\Response File download o errore
     */
    public function exportAll(Request $request)
    {
        // STEP 1: Validazione parametri di input
        $request->validate([
            'format' => 'required|in:json,csv',                    // Solo JSON o CSV accettati
            'include_sensitive' => 'boolean'                       // Flag per dati sensibili
        ]);

        $includeSensitive = $request->boolean('include_sensitive');
        $format = $request->input('format');

        try {
            // STEP 2: Preparazione metadati export
            // Informazioni utili per tracciare l'export e la sua provenienza
            $data = [
                'exported_at' => now()->toISOString(),             // Timestamp ISO 8601
                'exported_by' => Auth::user()->nome_completo,      // Chi ha fatto l'export
                'include_sensitive' => $includeSensitive,          // Se include dati sensibili
                'stats' => [
                    // Statistiche rapide per validare completezza export
                    'total_users' => User::count(),
                    'total_products' => Prodotto::count(),
                    'total_malfunctions' => Malfunzionamento::count(),
                    'total_centers' => CentroAssistenza::count(),
                ]
            ];

            // STEP 3: Export utenti (senza password per sicurezza)
            // select() limita i campi per proteggere dati sensibili
            $userFields = ['id', 'username', 'nome', 'cognome', 'livello_accesso', 'created_at'];
            if ($includeSensitive) {
                // Aggiunge campi sensibili solo se richiesto esplicitamente
                $userFields = array_merge($userFields, ['data_nascita', 'specializzazione', 'centro_assistenza_id']);
            }
            $data['users'] = User::select($userFields)->get();

            // STEP 4: Export prodotti con relazione staff
            // with() include i dati dello staff assegnato per referenza
            $data['products'] = Prodotto::with('staffAssegnato:id,nome,cognome')->get();

            // STEP 5: Export malfunzionamenti con relazioni
            // Include sia prodotto che utente che ha creato la soluzione
            $data['malfunctions'] = Malfunzionamento::with([
                'prodotto:id,nome',                                 // Solo ID e nome prodotto
                'creatoBy:id,nome,cognome'                         // Solo dati base creatore
            ])->get();

            // STEP 6: Export centri assistenza con conteggio tecnici
            // withCount() aggiunge numero tecnici senza caricare tutti i record
            $data['centers'] = CentroAssistenza::withCount('tecnici')->get();

            // STEP 7: Generazione nome file con timestamp
            $filename = 'sistema_assistenza_export_' . now()->format('Y-m-d_H-i-s');

            // STEP 8: Gestione formato output
            if ($format === 'json') {
                // JSON: Struttura completa con tutte le relazioni
                return response()->json($data)
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
            } else {
                // CSV: Solo una tabella (utenti) per semplicità
                // In produzione si potrebbe creare un ZIP con più CSV
                return $this->exportToCsv($data['users'], $filename . '_users.csv');
            }

        } catch (\Exception $e) {
            // STEP 9: Gestione errori durante export
            Log::error('Errore export dati', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['export' => 'Errore nell\'export: ' . $e->getMessage()]);
        }
    }

    // ================================================
    // SEZIONE 5: API ENDPOINTS PER DASHBOARD AJAX
    // ================================================

    /**
     * METODO STATS UPDATE - LINGUAGGIO: PHP con JSON Response
     * 
     * API endpoint per aggiornamento statistiche dashboard in tempo reale.
     * Utilizzato da JavaScript AJAX per aggiornare dashboard senza reload pagina.
     * 
     * CORREZIONE PRINCIPALE: Fix del campo per prodotti non assegnati
     * ERRORE PRECEDENTE: utilizzava 'utente_id' invece di 'staff_assegnato_id'
     * 
     * RITORNA: JSON con statistiche aggiornate e timestamp
     * 
     * @return \Illuminate\Http\JsonResponse Response JSON per chiamate AJAX
     */
    public function statsUpdate()
    {
        try {
            // STEP 1: CORREZIONE PRINCIPALE - Campo corretto per prodotti non assegnati
            // ERRORE ERA: whereNull('utente_id') 
            // CORRETTO: whereNull('staff_assegnato_id')
            // Questo era il bug principale che causava conteggi errati
            $prodottiNonAssegnatiCount = Prodotto::whereNull('staff_assegnato_id')->count();
            
            // STEP 2: Lista dettagliata prodotti non assegnati per dashboard
            // limit(10) evita di caricare troppi dati nell'AJAX response
            $prodottiNonAssegnatiLista = Prodotto::whereNull('staff_assegnato_id')
                ->select('id', 'nome', 'modello', 'categoria', 'created_at', 'attivo')
                ->orderBy('created_at', 'desc')                   // Più recenti prima
                ->limit(10)
                ->get();

            // STEP 3: Calcolo statistiche complete per dashboard
            $stats = [
                // === CONTATORI PRINCIPALI ===
                'total_utenti' => User::count(),
                'total_prodotti' => Prodotto::count(),
                'total_centri' => CentroAssistenza::count(),
                'total_soluzioni' => Malfunzionamento::count(),

                // === CONTATORI DINAMICI ===
                // Utenti attivi negli ultimi 30 giorni
                'utenti_attivi' => User::where('last_login_at', '>=', now()->subDays(30))->count(),

                // === FIX PRINCIPALE: CAMPO CORRETTO ===
                'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
                'prodotti_non_assegnati' => $prodottiNonAssegnatiLista,

                // Soluzioni critiche che richiedono attenzione
                'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),
                
                // Nuovi utenti registrati oggi
                'nuovi_utenti_oggi' => User::whereDate('created_at', today())->count(),

                // === STATISTICHE AGGIUNTIVE ===
                'prodotti_attivi' => Prodotto::where('attivo', true)->count(),
                'prodotti_inattivi' => Prodotto::where('attivo', false)->count(),
                'staff_disponibili' => User::where('livello_accesso', '3')->count(),

                // === DISTRIBUZIONE UTENTI ===
                // GROUP BY per conteggi per livello accesso
                'distribuzione_utenti' => User::selectRaw('livello_accesso, COUNT(*) as count')
                    ->groupBy('livello_accesso')
                    ->pluck('count', 'livello_accesso')
                    ->toArray(),

                // === TIMESTAMP AGGIORNAMENTO ===
                'last_update' => now()->toISOString(),            // Per verificare freschezza dati
                'update_time' => now()->format('H:i:s')           // Visualizzazione user-friendly
            ];

            // STEP 4: Response JSON strutturata per AJAX
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'message' => 'Statistiche aggiornate con successo'
            ]);

        } catch (\Exception $e) {
            // STEP 5: Gestione errori per AJAX
            Log::error('Errore aggiornamento statistiche admin dashboard', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            // Ritorna errore JSON con status HTTP 500
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento delle statistiche'
            ], 500);
        }
    }

    /**
     * METODO PRODOTTI NON ASSEGNATI - LINGUAGGIO: PHP con JSON Response
     * 
     * Endpoint specifico per dettagli prodotti non assegnati.
     * Utilizzato per popolare widget dashboard dedicato.
     * 
     * FUNZIONALITÀ:
     * - Lista prodotti senza staff assegnato
     * - Dati formattati per visualizzazione frontend
     * - Conteggio totale per badge numerici
     * 
     * @return \Illuminate\Http\JsonResponse Dati JSON per widget dashboard
     */
    public function prodottiNonAssegnati()
    {
        try {
            // STEP 1: Query prodotti senza staff con campi essenziali
            $prodotti = Prodotto::whereNull('staff_assegnato_id')
                ->select('id', 'nome', 'modello', 'categoria', 'created_at', 'attivo')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($prodotto) {
                    // STEP 2: Trasformazione dati per frontend
                    return [
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'modello' => $prodotto->modello,
                        'categoria' => $prodotto->categoria_label,      // Accessor per label leggibile
                        'created_at' => $prodotto->created_at->diffForHumans(), // "2 ore fa", "1 giorno fa"
                        'attivo' => $prodotto->attivo
                    ];
                });

            // STEP 3: Conteggio totale per badge numerici
            $count = Prodotto::whereNull('staff_assegnato_id')->count();

            // STEP 4: Response JSON strutturata
            return response()->json([
                'success' => true,
                'count' => $count,
                'prodotti' => $prodotti,
                'message' => $count > 0 ? "Trovati {$count} prodotti non assegnati" : "Tutti i prodotti sono assegnati",
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            // STEP 5: Gestione errori
            Log::error('Errore caricamento prodotti non assegnati', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei prodotti non assegnati'
            ], 500);
        }
    }

    /**
     * METODO SYSTEM STATUS - LINGUAGGIO: PHP con System Checks
     * 
     * API per monitoraggio stato sistema in tempo reale.
     * Verifica componenti critici e ritorna stato operativo.
     * 
     * COMPONENTI VERIFICATI:
     * - Connessione database
     * - Writability storage
     * - Funzionamento cache
     * - Informazioni server
     * 
     * @return \Illuminate\Http\JsonResponse Stato sistema per monitoring dashboard
     */
    public function systemStatus()
    {
        try {
            // STEP 1: Test connessione database
            $databaseStatus = 'online';
            try {
                // Tenta connessione e query di test
                DB::connection()->getPdo();                       // Test connessione PDO
                DB::select('SELECT 1');                           // Query di test semplice
            } catch (\Exception $e) {
                $databaseStatus = 'error';
            }

            // STEP 2: Test writability storage
            // Verifica che Laravel possa scrivere file (log, cache, uploads)
            $storageWritable = is_writable(storage_path());
            
            // STEP 3: Test funzionamento cache
            $cacheStatus = 'active';
            try {
                // Tenta scrittura e lettura cache
                \Cache::put('system_test', 'ok', 10);             // Scrivi in cache
                \Cache::get('system_test');                       // Leggi dalla cache
            } catch (\Exception $e) {
                $cacheStatus = 'error';
            }

            // STEP 4: Raccolta informazioni sistema
            $status = [
                'database' => $databaseStatus,
                'storage' => $storageWritable ? 'writable' : 'read-only',
                'cache' => $cacheStatus,
                'last_check' => now()->toISOString(),
                'uptime' => $this->getSystemUptime()              // Info uptime server
            ];

            // STEP 5: Determinazione stato generale
            $overallStatus = 'operational';
            if ($status['database'] === 'error' || $status['cache'] === 'error') {
                $overallStatus = 'error';                         // Errori critici
            } elseif ($status['storage'] === 'read-only') {
                $overallStatus = 'degraded';                      // Funzionamento limitato
            }

            // STEP 6: Response completa con dettagli server
            return response()->json([
                'success' => true,
                'status' => $overallStatus,
                'components' => $status,
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                    'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
                ]
            ]);

        } catch (\Exception $e) {
            // STEP 7: Gestione errori critici
            Log::error('Errore controllo stato sistema', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Errore nel controllo dello stato del sistema'
            ], 500);
        }
    }

    // ================================================
    // SEZIONE 6: METODI HELPER PRIVATI
    // ================================================

    /**
     * METODO HELPER: GET DATABASE VERSION - LINGUAGGIO: PHP con Raw SQL
     * 
     * Recupera la versione del database MySQL/MariaDB.
     * Utile per debugging e compatibilità.
     * 
     * @return string Versione database o 'N/A' se errore
     */
    private function getDatabaseVersion(): string
    {
        try {
            // Query SQL diretta per versione database
            $version = DB::select('SELECT VERSION() as version')[0]->version;
            return $version;
        } catch (\Exception $e) {
            return 'N/A';                                         // Fallback se query fallisce
        }
    }

    /**
     * METODO HELPER: GET STORAGE USAGE - LINGUAGGIO: PHP con Filesystem Functions
     * 
     * Calcola utilizzo spazio disco per directory storage di Laravel.
     * Importante per monitorare spazio disponibile per log, cache, uploads.
     * 
     * @return array Array con informazioni spazio disco
     */
    private function getStorageUsage(): array
    {
        try {
            $storagePath = storage_path();                        // Path directory storage Laravel
            
            // Funzioni PHP per informazioni filesystem
            $totalSpace = disk_total_space($storagePath);         // Spazio totale disco
            $freeSpace = disk_free_space($storagePath);           // Spazio libero
            $usedSpace = $totalSpace - $freeSpace;                // Spazio utilizzato

            return [
                'total' => $this->formatBytes($totalSpace),       // Formato leggibile (GB, MB)
                'used' => $this->formatBytes($usedSpace),
                'free' => $this->formatBytes($freeSpace),
                'percentage' => round(($usedSpace / $totalSpace) * 100, 2) // Percentuale uso
            ];
        } catch (\Exception $e) {
            return ['error' => 'Impossibile calcolare utilizzo storage'];
        }
    }

    /**
     * METODO HELPER: GET LOG FILES - LINGUAGGIO: PHP con File System
     * 
     * Scansiona directory log per trovare file disponibili.
     * Mostra dimensioni e date modifica per gestione log.
     * 
     * @return array Array con informazioni file log
     */
    private function getLogFiles(): array
    {
        try {
            $logPath = storage_path('logs');                      // Directory log Laravel
            $files = [];

            if (is_dir($logPath)) {
                // glob() trova tutti i file .log nella directory
                $logFiles = glob($logPath . '/*.log');
                foreach ($logFiles as $file) {
                    $files[] = [
                        'name' => basename($file),                // Nome file senza path
                        'size' => $this->formatBytes(filesize($file)), // Dimensione leggibile
                        'modified' => date('d/m/Y H:i', filemtime($file)) // Data modifica
                    ];
                }
            }

            return $files;
        } catch (\Exception $e) {
            return [];                                           // Array vuoto se errore
        }
    }

    /**
     * METODO HELPER: IS CACHE ENABLED - LINGUAGGIO: PHP con File System
     * 
     * Verifica se specifici tipi di cache Laravel sono attivi.
     * Laravel salva cache come file nel filesystem.
     * 
     * @param string $type Tipo di cache da verificare
     * @return bool True se cache attiva, false altrimenti
     */
    private function isCacheEnabled(string $type): bool
    {
        try {
            switch ($type) {
                case 'config':
                    // Cache config salvata in bootstrap/cache/config.php
                    return file_exists(base_path('bootstrap/cache/config.php'));
                
                case 'route':
                    // Cache rotte salvata in bootstrap/cache/routes-v7.php
                    return file_exists(base_path('bootstrap/cache/routes-v7.php'));
                
                case 'view':
                    // Cache viste Blade salvate in storage/framework/views/
                    return count(glob(storage_path('framework/views/*.php'))) > 0;
                
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * METODO HELPER: GET SYSTEM UPTIME - LINGUAGGIO: PHP con System Functions
     * 
     * Recupera informazioni di uptime/load del sistema server.
     * Utile per monitoraggio performance server.
     * 
     * @return string Informazioni uptime o load average
     */
    private function getSystemUptime(): string
    {
        try {
            // sys_getloadavg() disponibile su sistemi Unix/Linux
            if (function_exists('sys_getloadavg') && $load = sys_getloadavg()) {
                // Load average: [1min, 5min, 15min]
                return "Load average: " . implode(', ', array_map(function($l) {
                    return number_format($l, 2);                 // 2 decimali per readability
                }, $load));
            }
            // Fallback: mostra sistema operativo
            return 'Sistema operativo: ' . PHP_OS;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * METODO HELPER: FORMAT BYTES - LINGUAGGIO: PHP con Math Functions
     * 
     * Converte bytes in formato leggibile (KB, MB, GB, TB).
     * Utilizzato per mostrare dimensioni file e utilizzo storage.
     * 
     * @param int $bytes Numero di bytes da convertire
     * @param int $precision Numero decimali da mostrare
     * @return string Dimensione formattata (es: "1.5 GB")
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];                  // Unità di misura

        // Loop per trovare unità appropriata
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;                                      // Dividi per 1024 per ogni unità
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * METODO HELPER: EXPORT TO CSV - LINGUAGGIO: PHP con File Streaming
     * 
     * Converte dati in formato CSV e li invia come download.
     * Utilizza streaming per evitare problemi di memoria con dataset grandi.
     * 
     * @param mixed $data Dati da esportare (Collection o Array)
     * @param string $filename Nome file per download
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Response stream per download
     */
    private function exportToCsv($data, string $filename)
    {
        // Headers HTTP per download file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Callback function per streaming output
        $callback = function() use ($data) {
            // php://output scrive direttamente nel browser
            $file = fopen('php://output', 'w');
            
            // STEP 1: Scrivi header CSV se ci sono dati
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]->toArray()));  // Header columns
                
                // STEP 2: Scrivi ogni record come riga CSV
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());              // Converte model in array
                }
            }
            
            fclose($file);                                        // Chiudi stream
        };

        // Ritorna StreamedResponse per download immediato
        return response()->stream($callback, 200, $headers);
    }
}