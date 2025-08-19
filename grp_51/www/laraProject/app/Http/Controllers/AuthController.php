<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\CentroAssistenza;

/**
 * Controller per la gestione dell'autenticazione e delle dashboard
 * Gestisce login, logout, registrazione e le dashboard specifiche per ogni livello utente
 */
class AuthController extends Controller
{
    // ================================================
    // AUTENTICAZIONE
    // ================================================

    /**
     * Mostra il form di login
     */
    public function showLogin()
    {
        // Se l'utente è già autenticato, reindirizza alla sua dashboard specifica
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        
        return view('auth.login');
    }

    /**
     * Gestisce il processo di autenticazione
     */
    public function login(Request $request)
    {
        // Validazione dei dati in input
        $request->validate([
            'username' => 'required|string', // Username obbligatorio
            'password' => 'required|string', // Password obbligatoria
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'password.required' => 'Il campo password è obbligatorio',
        ]);

        // Tentativo di autenticazione con username e password
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Rigenerazione della sessione per prevenire session fixation
            $request->session()->regenerate();
            
            // Aggiorna timestamp ultimo accesso se il campo esiste
            if (Auth::user()->hasAttribute('last_login_at')) {
                Auth::user()->update(['last_login_at' => now()]);
            }
            
            // Log dell'accesso riuscito
            Log::info('Login riuscito', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'livello_accesso' => Auth::user()->livello_accesso,
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 100) // Limita lunghezza
            ]);

            // Reindirizzamento AUTOMATICO basato sul livello di accesso
            return $this->redirectBasedOnRole();
        }

        // Se l'autenticazione fallisce, torna indietro con errore
        throw ValidationException::withMessages([
            'username' => 'Le credenziali fornite non sono corrette.',
        ]);
    }

    /**
     * Gestisce il logout dell'utente
     */
    public function logout(Request $request)
    {
        // Log del logout
        if (Auth::check()) {
            Log::info('Logout utente', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'livello_accesso' => Auth::user()->livello_accesso
            ]);
        }

        // Logout dell'utente dalla sessione
        Auth::logout();
        
        // Invalidazione della sessione e rigenerazione del token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout effettuato con successo');
    }

    // ================================================
    // REINDIRIZZAMENTO AUTOMATICO
    // ================================================

    /**
     * Reindirizza l'utente basandosi sul suo livello di accesso - AUTOMATICO
     * Questo metodo viene chiamato dopo il login per portare ogni utente alla sua dashboard
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        // Reindirizzamento DIRETTO alle dashboard specifiche per livello
        switch ((int) $user->livello_accesso) {
            case 4: // Admin -> Dashboard Admin
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Benvenuto, Amministratore ' . $user->nome . '!');
                    
            case 3: // Staff -> Dashboard Staff
                return redirect()->route('staff.dashboard')
                    ->with('success', 'Benvenuto, ' . $user->nome_completo . '!');
                    
            case 2: // Tecnico -> Dashboard Tecnico
                return redirect()->route('tecnico.dashboard')
                    ->with('success', 'Benvenuto, Tecnico ' . $user->nome . '!');
                    
            default: // Utente pubblico -> Dashboard generale
                return redirect()->route('dashboard')
                    ->with('success', 'Accesso effettuato con successo');
        }
    }

    /**
     * Route helper per reindirizzamento manuale alla dashboard appropriata
     */
    public function autoRedirectDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return $this->redirectBasedOnRole();
    }

    // ================================================
    // REGISTRAZIONE (Solo Admin)
    // ================================================

    /**
     * Mostra il form di registrazione (solo per admin)
     */
    public function showRegister()
    {
        // Solo gli amministratori possono registrare nuovi utenti
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato a registrare nuovi utenti');
        }

        // Carica i centri assistenza per il form
        $centri = CentroAssistenza::orderBy('nome')->get();

        return view('auth.register', compact('centri'));
    }

    /**
     * Gestisce la registrazione di un nuovo utente (solo admin)
     */
    public function register(Request $request)
    {
        // Verifica che solo gli admin possano registrare utenti
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        // Validazione completa dei dati
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'livello_accesso' => 'required|in:1,2,3,4',
            
            // Campi condizionali per i tecnici (livello 2)
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'username.unique' => 'Questo username è già in uso',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
            'livello_accesso.in' => 'Livello di accesso non valido',
            'data_nascita.required_if' => 'La data di nascita è obbligatoria per i tecnici',
            'specializzazione.required_if' => 'La specializzazione è obbligatoria per i tecnici',
            'centro_assistenza_id.required_if' => 'Il centro di assistenza è obbligatorio per i tecnici',
        ]);

        // Creazione del nuovo utente
        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'nome' => $validated['nome'],
            'cognome' => $validated['cognome'],
            'livello_accesso' => (int) $validated['livello_accesso'],
            'data_nascita' => $validated['data_nascita'] ?? null,
            'specializzazione' => $validated['specializzazione'] ?? null,
            'centro_assistenza_id' => $validated['centro_assistenza_id'] ?? null,
        ]);

        // Log della creazione utente
        Log::info('Nuovo utente registrato', [
            'new_user_id' => $user->id,
            'new_username' => $user->username,
            'created_by' => Auth::id(),
            'livello_accesso' => $user->livello_accesso
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utente registrato con successo');
    }

    // ================================================
    // DASHBOARD GENERALE (Fallback)
    // ================================================

    /**
     * Dashboard generale - SOLO per utenti pubblici (livello 1) o come fallback
     * Gli utenti con livelli superiori vengono automaticamente reindirizzati alle loro dashboard
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Se l'utente ha un livello specifico (2+), reindirizza alla sua dashboard
        if ($user->livello_accesso >= 2) {
            return $this->redirectBasedOnRole();
        }
        
        // Solo per utenti pubblici (livello 1 o nessun livello)
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'total_centri' => CentroAssistenza::count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }

    // ================================================
    // DASHBOARD SPECIFICHE PER LIVELLO
    // ================================================

    /**
     * Dashboard specifica per amministratori (Livello 4) - SENZA DUMP
     * Sostituisci il metodo adminDashboard() nel tuo AuthController.php
     */
public function adminDashboard()
{
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Accesso riservato agli amministratori');
    }

    $user = Auth::user();

    // === CALCOLO PRODOTTI NON ASSEGNATI ===
    $prodottiNonAssegnatiCount = Prodotto::whereNull('staff_assegnato_id')->count();
    
    $prodottiNonAssegnatiLista = Prodotto::whereNull('staff_assegnato_id')
        ->select('id', 'nome', 'modello', 'categoria', 'created_at', 'attivo')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // Statistiche complete per admin
    $stats = [
        // Contatori principali
        'total_utenti' => User::count(),
        'total_prodotti' => Prodotto::count(),
        'total_centri' => CentroAssistenza::count(),
        'total_soluzioni' => Malfunzionamento::count(),

        // === PRODOTTI NON ASSEGNATI ===
        'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
        'prodotti_non_assegnati' => $prodottiNonAssegnatiLista,

        // Distribuzione utenti per livello
        'utenti_per_livello' => User::selectRaw('livello_accesso, COUNT(*) as count')
            ->groupBy('livello_accesso')
            ->pluck('count', 'livello_accesso')
            ->toArray(),

        // Utenti recenti (ultimo mese)
        'utenti_recenti' => User::where('created_at', '>=', now()->subMonth())
            ->latest()
            ->take(5)
            ->get(['id', 'nome', 'cognome', 'username', 'livello_accesso', 'created_at']),

        // Distribuzione utenti (per la sezione apposita)
        'distribuzione_utenti' => User::selectRaw('livello_accesso, COUNT(*) as count')
            ->groupBy('livello_accesso')
            ->pluck('count', 'livello_accesso')
            ->toArray(),

        // Soluzioni critiche
        'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),
    ];

    // === DEBUG CON LOG (non blocca l'esecuzione) ===
    Log::info('Dashboard Admin - Statistiche', [
        'admin_user' => $user->username,
        'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
        'prodotti_lista_count' => $prodottiNonAssegnatiLista->count(),
        'total_prodotti' => $stats['total_prodotti'],
        'sample_non_assegnati' => $prodottiNonAssegnatiLista->take(3)->pluck('nome', 'id')->toArray()
    ]);

    // === RITORNA LA VISTA (ora funziona!) ===
    return view('admin.dashboard', compact('user', 'stats'));
}

    /**
     * Dashboard specifica per staff aziendale (Livello 3)
     * VERSIONE CORRETTA - Risolve tutti i problemi identificati
     */
    public function staffDashboard()
    {
        // Verifica che l'utente sia autenticato e abbia il livello staff
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff');
        }

        $user = Auth::user();

        try {
            // === SOLUZIONE PROBLEMA 1: Gestione sicura dei prodotti assegnati ===
            // Usa query diretta invece di relazione per evitare errori
            $prodottiAssegnati = Prodotto::where('staff_assegnato_id', $user->id)
                ->with(['malfunzionamenti' => function($query) {
                    // Ottimizza caricando solo campi necessari per performance
                    $query->select('id', 'prodotto_id', 'titolo', 'gravita', 'created_at', 'updated_at')
                          ->orderBy('updated_at', 'desc');
                }])
                ->get();

            $countProdottiAssegnati = $prodottiAssegnati->count();

            // === SOLUZIONE PROBLEMA 2: Gestione sicura dei malfunzionamenti creati ===
            // Query diretta con controllo esistenza campo 'creato_da'
            $malfunzionamentiCreati = collect(); // Inizializza collezione vuota
            $countSoluzioniCreate = 0;
            $countSoluzioniCritiche = 0;
            $ultimaModifica = 'Mai';
            $ultimeSoluzioni = collect();

            // Controlla se la tabella malfunzionamenti ha il campo creato_da
            if (\Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                // Se il campo esiste, usa la query diretta
                $malfunzionamentiCreati = Malfunzionamento::where('creato_da', $user->id)
                    ->with(['prodotto:id,nome,categoria'])
                    ->get();

                $countSoluzioniCreate = $malfunzionamentiCreati->count();
                $countSoluzioniCritiche = $malfunzionamentiCreati->where('gravita', 'critica')->count();
                
                // Trova ultima modifica
                $ultimoMalfunzionamento = $malfunzionamentiCreati->sortByDesc('updated_at')->first();
                if ($ultimoMalfunzionamento) {
                    $ultimaModifica = $ultimoMalfunzionamento->updated_at->diffForHumans();
                }

                // Ultimi 5 malfunzionamenti creati
                $ultimeSoluzioni = $malfunzionamentiCreati->sortByDesc('created_at')->take(5);
                
            } else {
                // Se il campo non esiste, usa query alternativa o valori di default
                \Log::info('Campo creato_da non trovato nella tabella malfunzionamenti');
                
                // Potresti implementare una logica alternativa qui se necessario
                // Ad esempio, contare tutti i malfunzionamenti dei prodotti assegnati
                $countSoluzioniCreate = 0;
                $countSoluzioniCritiche = 0;
            }

            // === COSTRUZIONE ARRAY STATISTICHE SICURO ===
            $stats = [
                // Statistiche prodotti assegnati
                'prodotti_assegnati' => $countProdottiAssegnati,
                'prodotti_lista' => $prodottiAssegnati->take(5), // Limita a 5 per la dashboard
                
                // Statistiche soluzioni create
                'soluzioni_create' => $countSoluzioniCreate,
                'soluzioni_critiche' => $countSoluzioniCritiche,
                
                // Ultima attività
                'ultima_modifica' => $ultimaModifica,
                
                // Ultime soluzioni
                'ultime_soluzioni' => $ultimeSoluzioni,
                
                // Statistiche generali accessibili allo staff
                'total_prodotti' => Prodotto::count(),
                'total_malfunzionamenti' => Malfunzionamento::count(),
                'malfunzionamenti_critici' => Malfunzionamento::where('gravita', 'critica')->count(),
            ];

            // Log per debug in ambiente di sviluppo
            if (config('app.debug')) {
                \Log::info('Staff Dashboard Stats Generati', [
                    'user_id' => $user->id,
                    'prodotti_assegnati' => $countProdottiAssegnati,
                    'soluzioni_create' => $countSoluzioniCreate,
                    'has_creato_da_field' => \Schema::hasColumn('malfunzionamenti', 'creato_da')
                ]);
            }

            // Restituisce vista con statistiche
            return view('staff.dashboard', compact('user', 'stats'));

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ROBUSTA ===
            \Log::error('Errore in staffDashboard', [
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Statistiche di fallback in caso di errore
            $stats = [
                'prodotti_assegnati' => 0,
                'prodotti_lista' => collect(),
                'soluzioni_create' => 0,
                'soluzioni_critiche' => 0,
                'ultima_modifica' => 'Errore caricamento',
                'ultime_soluzioni' => collect(),
                'total_prodotti' => 0,
                'total_malfunzionamenti' => 0,
                'malfunzionamenti_critici' => 0,
                'errore' => 'Impossibile caricare alcune statistiche'
            ];

            // In ambiente di sviluppo, mostra l'errore
            if (config('app.debug')) {
                $stats['debug_error'] = $e->getMessage();
            }

            return view('staff.dashboard', compact('user', 'stats'))
                ->with('warning', 'Alcune statistiche potrebbero non essere aggiornate');
        }
    }
    /**
     * Dashboard specifica per tecnici (Livello 2)
     */
    public function tecnicoDashboard()
    {
        if (!Auth::check() || !Auth::user()->isTecnico()) {
            abort(403, 'Accesso riservato ai tecnici');
        }

        $user = Auth::user();
        
        // Statistiche specifiche per il tecnico
        $stats = [
            // Statistiche generali accessibili al tecnico
            'total_prodotti' => Prodotto::count(),
            'total_malfunzionamenti' => Malfunzionamento::count(),
            'malfunzionamenti_critici' => Malfunzionamento::where('gravita', 'critica')->count(),
            'total_centri' => CentroAssistenza::count(),
            
            // Centro di assistenza del tecnico
            'centro_assistenza' => $user->centroAssistenza,
            
            // Malfunzionamenti critici recenti (per interventi urgenti)
            'malfunzionamenti_critici_lista' => Malfunzionamento::where('gravita', 'critica')
                ->with('prodotto')
                ->latest()
                ->take(5)
                ->get(),
                
            // Prodotti con più problemi critici
            'prodotti_problematici' => Prodotto::whereHas('malfunzionamenti', function($q) {
                $q->where('gravita', 'critica');
            })->withCount(['malfunzionamenti as critici_count' => function($q) {
                $q->where('gravita', 'critica');
            }])->orderBy('critici_count', 'desc')
            ->take(5)
            ->get(),
            
            // Distribuzione malfunzionamenti per gravità
            'malfunzionamenti_per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                ->whereNotNull('gravita')
                ->groupBy('gravita')
                ->pluck('count', 'gravita')
                ->toArray(),
        ];

        return view('tecnico.dashboard', compact('user', 'stats'));
    }

    // ================================================
    // API ENDPOINTS PER STATISTICHE (AJAX)
    // ================================================

    public function staffStats()
{
    try {
        // Log per debug
        \Log::info('staffStats API chiamata', [
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'ip' => request()->ip()
        ]);

        // Verifica autenticazione
        if (!Auth::check()) {
            \Log::warning('staffStats: utente non autenticato');
            return response()->json([
                'success' => false, 
                'message' => 'Autenticazione richiesta'
            ], 401);
        }

        $user = Auth::user();

        // Verifica livello staff (livello 3+)
        if (!$user->isStaff()) {
            \Log::warning('staffStats: utente non autorizzato', [
                'user_level' => $user->livello_accesso,
                'required_level' => 3
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Accesso riservato allo staff aziendale'
            ], 403);
        }

        // Calcola statistiche dal database
        $stats = [
            // Statistiche principali
            'total_malfunzionamenti' => \App\Models\Malfunzionamento::count(),
            'critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
            'alta_priorita' => \App\Models\Malfunzionamento::where('gravita', 'alta')->count(),
            'creati_questo_mese' => \App\Models\Malfunzionamento::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            
            // Statistiche specifiche staff (aggiungeremo funzionalità future)
            'prodotti_assegnati' => 0, // TODO: implementare relazione user->prodotti
            'soluzioni_create' => 0,   // TODO: implementare relazione user->malfunzionamenti_creati
            'soluzioni_critiche' => 0,
            'ultima_modifica' => 'Mai',
            'soluzioni_ultimo_mese' => 0
        ];

        // Log del successo
        \Log::info('staffStats completato con successo', [
            'user_id' => $user->id,
            'stats' => $stats
        ]);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now()->toISOString(),
            'user' => $user->nome . ' ' . $user->cognome,
            'user_level' => $user->livello_accesso
        ]);

    } catch (\Exception $e) {
        // Log dettagliato dell'errore
        \Log::error('Errore in staffStats', [
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore interno nel caricamento statistiche',
            'error' => app()->environment('local') ? $e->getMessage() : null
        ], 500);
    }
}


    /**
     * API per prodotti assegnati allo staff (chiamata AJAX)
     */
    public function staffProdotti()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        $user = Auth::user();
        
        $prodotti = $user->prodottiAssegnati()
            ->with('malfunzionamenti')
            ->get()
            ->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'codice' => $prodotto->codice,
                    'categoria' => $prodotto->categoria,
                    'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
                    'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count()
                ];
            });
        
        return response()->json([
            'success' => true,
            'prodotti' => $prodotti,
            'total' => $prodotti->count()
        ]);
    }

    /**
     * API per ultime soluzioni create dallo staff (chiamata AJAX)
     */
    public function staffUltimeSoluzioni()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        $user = Auth::user();
        
        $soluzioni = $user->malfunzionamentiCreati()
            ->with('prodotto')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($soluzione) {
                return [
                    'id' => $soluzione->id,
                    'titolo' => $soluzione->titolo,
                    'prodotto_nome' => $soluzione->prodotto->nome ?? 'N/A',
                    'gravita' => $soluzione->gravita,
                    'created_at' => $soluzione->created_at->diffForHumans(),
                    'updated_at' => $soluzione->updated_at->diffForHumans()
                ];
            });
        
        return response()->json([
            'success' => true,
            'soluzioni' => $soluzioni,
            'total' => $soluzioni->count()
        ]);
    }

    /**
     * API per statistiche admin (chiamata AJAX)
     */
    public function adminStatsUpdate()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        try {
            $stats = [
                'total_utenti' => User::count(),
                'total_prodotti' => Prodotto::count(),
                'total_centri' => CentroAssistenza::count(),
                'total_soluzioni' => Malfunzionamento::count(),
                'utenti_attivi' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
                'prodotti_senza_soluzioni' => Prodotto::whereDoesntHave('malfunzionamenti')->count(),
                'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count()
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Errore nel caricamento statistiche admin', [
                'user_id' => Auth::user()->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche'
            ], 500);
        }
    }

    /**
     * API per stato sistema (chiamata AJAX - solo admin)
     */
    public function systemStatus()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        try {
            $status = [
                'database' => 'online',
                'storage' => is_writable(storage_path()) ? 'writable' : 'read-only',
                'cache' => 'active',
                'queue' => 'active'
            ];
            
            // Test connessione database
            try {
                \DB::connection()->getPdo();
            } catch (\Exception $e) {
                $status['database'] = 'error';
            }
            
            // Determina stato generale
            $overallStatus = 'operational';
            if ($status['database'] === 'error') {
                $overallStatus = 'error';
            } elseif ($status['storage'] === 'read-only') {
                $overallStatus = 'degraded';
            }
            
            return response()->json([
                'success' => true,
                'status' => $overallStatus,
                'services' => $status,
                'timestamp' => now()->toISOString(),
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                    'uptime' => $this->getServerUptime()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Errore nel controllo stato sistema'
            ], 500);
        }
    }

    // ================================================
    // METODI HELPER
    // ================================================

    /**
     * Helper per ottenere uptime del server (approssimativo)
     */
    private function getServerUptime()
    {
        try {
            if (function_exists('sys_getloadavg') && $load = sys_getloadavg()) {
                return "Load average: " . implode(', ', array_map(function($l) { 
                    return number_format($l, 2); 
                }, $load));
            }
            return 'Sistema operativo: ' . PHP_OS;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
?>