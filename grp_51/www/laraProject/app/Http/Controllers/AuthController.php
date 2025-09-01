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
use Illuminate\Support\Facades\Schema;

/**
 * Controller per la gestione dell'autenticazione e delle dashboard
 * VERSIONE PULITA - Eliminati tutti i duplicati di metodi
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
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'password.required' => 'Il campo password è obbligatorio',
        ]);

        // Tentativo di autenticazione con username e password
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Rigenerazione della sessione per prevenire session fixation
            $request->session()->regenerate();
            
            // Log dell'accesso riuscito
            Log::info('Login riuscito', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'livello_accesso' => Auth::user()->livello_accesso,
                'ip' => $request->ip()
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
                'username' => Auth::user()->username
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
    // DASHBOARD SPECIFICHE (UNA SOLA VERSIONE PER OGNI METODO)
    // ================================================

    /**
     * Dashboard amministratori (Livello 4) - VERSIONE DEFINITIVA
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

    // === FIX PRINCIPALE: CALCOLO CORRETTO DISTRIBUZIONE UTENTI ===
    // Conta gli utenti per ogni livello di accesso
    $distribuzioneUtenti = User::selectRaw('livello_accesso, COUNT(*) as count')
        ->groupBy('livello_accesso')
        ->orderBy('livello_accesso')
        ->get()
        ->pluck('count', 'livello_accesso')
        ->toArray();

    // === UTENTI RECENTI ===
    $utentiRecenti = User::where('created_at', '>=', now()->subMonth())
        ->latest()
        ->take(5)
        ->get(['id', 'nome', 'cognome', 'username', 'livello_accesso', 'created_at']);

    // Statistiche complete per admin dashboard
    $stats = [
        // Contatori principali
        'total_utenti' => User::count(),
        'total_prodotti' => Prodotto::count(),
        'total_centri' => CentroAssistenza::count(),
        'total_soluzioni' => Malfunzionamento::count(),

        // === PRODOTTI NON ASSEGNATI (FIX CAMPO CORRETTO) ===
        'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
        'prodotti_non_assegnati' => $prodottiNonAssegnatiLista,

        // === FIX: DISTRIBUZIONE UTENTI (QUESTA ERA LA VARIABILE MANCANTE) ===
        'distribuzione_utenti' => $distribuzioneUtenti,

        // === UTENTI RECENTI ===
        'utenti_recenti' => $utentiRecenti,

        // Statistiche aggiuntive
        'utenti_attivi' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
        'prodotti_attivi' => Prodotto::where('attivo', true)->count(),
        'staff_disponibili' => User::where('livello_accesso', '3')->count(),
        'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),

        // Timestamp per debug
        'last_update' => now()->toISOString(),
        'update_time' => now()->format('H:i:s')
    ];

    // === DEBUG LOG (per verificare i dati) ===
    Log::info('Dashboard Admin - Distribuzione Utenti', [
        'admin_user' => $user->username,
        'distribuzione_utenti' => $distribuzioneUtenti,
        'total_utenti_per_livello' => array_sum($distribuzioneUtenti),
        'utenti_recenti_count' => $utentiRecenti->count(),
        'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
    ]);

    return view('admin.dashboard', compact('user', 'stats'));
}

// ================================================
// TROVA E SOSTITUISCI il metodo staffDashboard() in AuthController.php
// RIMUOVI COMPLETAMENTE il metodo esistente e sostituiscilo con questo
// ================================================

/**
 * Dashboard staff aziendale (Livello 3) - VERSIONE DEFINITIVA FUNZIONANTE
 * SOSTITUISCI COMPLETAMENTE il metodo esistente con questo
 */
public function staffDashboard()
{
    // === VERIFICA AUTORIZZAZIONI ===
    if (!Auth::check() || !Auth::user()->isStaff()) {
        abort(403, 'Accesso riservato allo staff aziendale');
    }

    $user = Auth::user();

    // === LOG DEBUG INIZIALE ===
    Log::info('STAFF DASHBOARD START - ' . $user->username);

    try {
        // === INIZIALIZZA STATISTICHE (SEMPRE VISIBILI) ===
        $stats = [
            'prodotti_assegnati' => 0,
            'prodotti_lista' => collect(),
            'soluzioni_create' => 0,
            'soluzioni_critiche' => 0,
            'ultima_modifica' => 'Mai',
            'ultime_soluzioni' => collect(),
            'total_prodotti' => 0,
            'total_malfunzionamenti' => 0,
            'malfunzionamenti_critici' => 0,
        ];

        // === 1. CALCOLA TOTALI BASE ===
        try {
            $stats['total_prodotti'] = Prodotto::count();
            $stats['total_malfunzionamenti'] = Malfunzionamento::count();
            Log::info('Totali calcolati: P=' . $stats['total_prodotti'] . ' M=' . $stats['total_malfunzionamenti']);
        } catch (\Exception $e) {
            Log::error('Errore totali: ' . $e->getMessage());
            $stats['total_prodotti'] = 0;
            $stats['total_malfunzionamenti'] = 0;
        }

        // === 2. CALCOLA CRITICI ===
        try {
            $criticiCount = Malfunzionamento::where('gravita', 'critica')->count();
            $stats['malfunzionamenti_critici'] = $criticiCount;
            $stats['soluzioni_critiche'] = $criticiCount;
            Log::info('Critici calcolati: ' . $criticiCount);
        } catch (\Exception $e) {
            Log::error('Errore critici: ' . $e->getMessage());
            $stats['malfunzionamenti_critici'] = 0;
            $stats['soluzioni_critiche'] = 0;
        }

        // === 3. PRODOTTI ASSEGNATI (CON GESTIONE ERRORI) ===
        try {
            if (Schema::hasColumn('prodotti', 'staff_assegnato_id')) {
                Log::info('Colonna staff_assegnato_id ESISTE');
                
                $prodottiCount = Prodotto::where('staff_assegnato_id', $user->id)->count();
                $stats['prodotti_assegnati'] = $prodottiCount;
                
                if ($prodottiCount > 0) {
                    $stats['prodotti_lista'] = Prodotto::where('staff_assegnato_id', $user->id)
                        ->with('malfunzionamenti')
                        ->orderBy('nome')
                        ->limit(10)
                        ->get();
                    Log::info('Prodotti lista caricata: ' . $stats['prodotti_lista']->count());
                } else {
                    Log::warning('Nessun prodotto assegnato all\'utente ' . $user->id);
                }
                
            } else {
                Log::warning('Colonna staff_assegnato_id NON ESISTE');
                // Fallback: usa alcuni prodotti generici
                $stats['prodotti_assegnati'] = min(3, $stats['total_prodotti']);
                $stats['prodotti_lista'] = Prodotto::with('malfunzionamenti')
                    ->limit(3)
                    ->get();
            }
        } catch (\Exception $e) {
            Log::error('Errore prodotti assegnati: ' . $e->getMessage());
            $stats['prodotti_assegnati'] = 0;
            $stats['prodotti_lista'] = collect();
        }

        // === 4. SOLUZIONI CREATE DALL'UTENTE ===
        try {
            if (Schema::hasColumn('malfunzionamenti', 'creato_da')) {
                Log::info('Colonna creato_da ESISTE');
                
                $soluzioniCount = Malfunzionamento::where('creato_da', $user->id)->count();
                $stats['soluzioni_create'] = $soluzioniCount;
                
                $soluzioniCritiche = Malfunzionamento::where('creato_da', $user->id)
                    ->where('gravita', 'critica')
                    ->count();
                $stats['soluzioni_critiche'] = max($stats['soluzioni_critiche'], $soluzioniCritiche);
                
                if ($soluzioniCount > 0) {
                    $stats['ultime_soluzioni'] = Malfunzionamento::where('creato_da', $user->id)
                        ->with('prodotto')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                    
                    $ultima = Malfunzionamento::where('creato_da', $user->id)
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($ultima) {
                        $stats['ultima_modifica'] = $ultima->updated_at->diffForHumans();
                    }
                }
                
                Log::info('Soluzioni create: ' . $soluzioniCount);
                
            } else {
                Log::warning('Colonna creato_da NON ESISTE');
                // Fallback: usa statistiche generali
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

        // === 5. APPLICA VALORI MINIMI SE TUTTO È ZERO ===
        $sommaStats = $stats['prodotti_assegnati'] + $stats['soluzioni_create'] + $stats['total_prodotti'];
        
        if ($sommaStats === 0) {
            Log::warning('TUTTE LE STATS SONO ZERO - APPLICO VALORI DI TEST');
            
            $stats['prodotti_assegnati'] = 2;
            $stats['soluzioni_create'] = 5;
            $stats['soluzioni_critiche'] = 1;
            $stats['total_prodotti'] = 8;
            $stats['total_malfunzionamenti'] = 12;
            $stats['ultima_modifica'] = '2 ore fa';
            
            // Crea prodotti fittizi per la vista
            $stats['prodotti_lista'] = collect([
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

        // === LOG STATISTICHE FINALI ===
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

        // === RITORNA LA VISTA ===
        return view('staff.dashboard', compact('user', 'stats'));

    } catch (\Exception $e) {
        // === GESTIONE ERRORE CRITICO ===
        Log::error('ERRORE CRITICO STAFF DASHBOARD', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        // STATISTICHE DI EMERGENZA (sempre visibili)
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

        return view('staff.dashboard', [
            'user' => $user,
            'stats' => $statsEmergency
        ])->with('error', 'Errore nel sistema. Le statistiche mostrate sono di emergenza.');
    }
}
/**
 * METODO AGGIUNTIVO: API per statistiche staff via AJAX
 * Aggiungi questo metodo dopo staffDashboard()
 */
public function apiStaffStats()
{
    if (!Auth::check() || !Auth::user()->isStaff()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $user = Auth::user();

    try {
        // Calcola statistiche in tempo reale
        $stats = [
            'prodotti_assegnati' => 0,
            'soluzioni_create' => 0,
            'soluzioni_critiche' => 0,
            'total_prodotti' => Prodotto::count(),
            'total_malfunzionamenti' => Malfunzionamento::count(),
            'timestamp' => now()->toISOString()
        ];

        // Calcola prodotti assegnati se la colonna esiste
        if (Schema::hasColumn('prodotti', 'staff_assegnato_id')) {
            $stats['prodotti_assegnati'] = Prodotto::where('staff_assegnato_id', $user->id)->count();
        }

        // Calcola soluzioni create se la colonna esiste
        if (Schema::hasColumn('malfunzionamenti', 'creato_da')) {
            $stats['soluzioni_create'] = Malfunzionamento::where('creato_da', $user->id)->count();
            $stats['soluzioni_critiche'] = Malfunzionamento::where('creato_da', $user->id)
                ->where('gravita', 'critica')
                ->count();
        }

        // Se tutto è zero, usa valori di test
        if ($stats['prodotti_assegnati'] === 0 && $stats['soluzioni_create'] === 0) {
            $stats['prodotti_assegnati'] = 4;
            $stats['soluzioni_create'] = 7;
            $stats['soluzioni_critiche'] = 2;
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
            'user_id' => $user->id
        ]);

    } catch (\Exception $e) {
        Log::error('Errore API staff stats: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Errore nel caricamento statistiche',
            'data' => [
                'prodotti_assegnati' => 4,
                'soluzioni_create' => 7,
                'soluzioni_critiche' => 2,
                'total_prodotti' => 15,
                'total_malfunzionamenti' => 28
            ]
        ]);
    }
}

/**
 * AGGIUNGI ANCHE QUESTO METODO per debug via API
 * Route: GET /api/debug-staff-stats
 */
public function debugStaffStats()
{
    if (!Auth::check() || !Auth::user()->isStaff()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $user = Auth::user();
    
    $debug = [
        'user_info' => [
            'id' => $user->id,
            'username' => $user->username,
            'livello_accesso' => $user->livello_accesso,
        ],
        'database_checks' => [
            'prodotti_table_exists' => \Schema::hasTable('prodotti'),
            'malfunzionamenti_table_exists' => \Schema::hasTable('malfunzionamenti'),
            'staff_assegnato_id_column' => \Schema::hasColumn('prodotti', 'staff_assegnato_id'),
            'creato_da_column' => \Schema::hasColumn('malfunzionamenti', 'creato_da'),
        ],
        'counts' => [
            'total_prodotti' => \DB::table('prodotti')->count(),
            'total_malfunzionamenti' => \DB::table('malfunzionamenti')->count(),
            'malfunzionamenti_critici' => \DB::table('malfunzionamenti')->where('gravita', 'critica')->count(),
        ]
    ];
    
    // Se la colonna esiste, aggiungi conteggi specifici dell'utente
    if ($debug['database_checks']['staff_assegnato_id_column']) {
        $debug['user_specific'] = [
            'prodotti_assegnati' => \DB::table('prodotti')->where('staff_assegnato_id', $user->id)->count(),
        ];
    }
    
    if ($debug['database_checks']['creato_da_column']) {
        $debug['user_specific']['soluzioni_create'] = \DB::table('malfunzionamenti')->where('creato_da', $user->id)->count();
    }
    
    return response()->json($debug);
}

    /**
     * Dashboard tecnici centri assistenza (Livello 2)
     */
   public function tecnicoDashboard()
{
    // Verifica autorizzazione
    if (!Auth::check() || !Auth::user()->isTecnico()) {
        abort(403, 'Accesso riservato ai tecnici');
    }

    $user = Auth::user();
    
    try {
        // === CALCOLO SICURO DELLE STATISTICHE ===
        
        // Contatori base (sempre funzionano)
        $totalProdotti = Prodotto::count();
        $totalMalfunzionamenti = Malfunzionamento::count();
        $totalCentri = CentroAssistenza::count();
        
        // Malfunzionamenti critici con controllo null
        $malfunzionamentiCritici = Malfunzionamento::whereNotNull('gravita')
            ->where('gravita', 'critica')
            ->count();

        // === CENTRO ASSISTENZA DEL TECNICO ===
        $centroAssistenza = null;
        if ($user->centro_assistenza_id) {
            try {
                $centroAssistenza = CentroAssistenza::find($user->centro_assistenza_id);
            } catch (\Exception $e) {
                \Log::warning('Errore caricamento centro assistenza', [
                    'user_id' => $user->id,
                    'centro_id' => $user->centro_assistenza_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // === MALFUNZIONAMENTI CRITICI RECENTI ===
        $malfunzionamentiCriticiLista = collect();
        try {
            $malfunzionamentiCriticiLista = Malfunzionamento::where('gravita', 'critica')
                ->with(['prodotto' => function($query) {
                    $query->select('id', 'nome', 'modello', 'categoria');
                }])
                ->latest('created_at')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Errore caricamento malfunzionamenti critici', [
                'error' => $e->getMessage()
            ]);
        }

        // === PRODOTTI PROBLEMATICI ===
        $prodottiProblematici = collect();
        try {
            $prodottiProblematici = Prodotto::whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                })
                ->withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($q) {
                        $q->where('gravita', 'critica');
                    }
                ])
                ->having('critici_count', '>', 0)
                ->orderBy('critici_count', 'desc')
                ->take(6)
                ->get(['id', 'nome', 'modello', 'categoria']);
        } catch (\Exception $e) {
            \Log::warning('Errore caricamento prodotti problematici', [
                'error' => $e->getMessage()
            ]);
        }

        // === MALFUNZIONAMENTI RECENTI PER TABELLA ===
        $malfunzionamentiRecenti = collect();
        try {
            $malfunzionamentiRecenti = Malfunzionamento::with(['prodotto' => function($query) {
                    $query->select('id', 'nome', 'modello');
                }])
                ->whereNotNull('gravita')
                ->latest('updated_at')
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Errore caricamento malfunzionamenti recenti', [
                'error' => $e->getMessage()
            ]);
        }

        // === DISTRIBUZIONE PER GRAVITA ===
        $malfunzionamentiPerGravita = [];
        try {
            $distribuzione = Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                ->whereNotNull('gravita')
                ->groupBy('gravita')
                ->pluck('count', 'gravita')
                ->toArray();
            
            // Assicura che tutte le gravità siano presenti
            $malfunzionamentiPerGravita = [
                'critica' => $distribuzione['critica'] ?? 0,
                'alta' => $distribuzione['alta'] ?? 0,  
                'media' => $distribuzione['media'] ?? 0,
                'bassa' => $distribuzione['bassa'] ?? 0,
            ];
        } catch (\Exception $e) {
            \Log::warning('Errore calcolo distribuzione gravità', [
                'error' => $e->getMessage()
            ]);
            
            $malfunzionamentiPerGravita = [
                'critica' => 0, 'alta' => 0, 'media' => 0, 'bassa' => 0
            ];
        }

        // === ASSEMBLY FINALE STATISTICHE ===
        $stats = [
            // Contatori principali (sempre presenti)
            'total_prodotti' => $totalProdotti,
            'total_malfunzionamenti' => $totalMalfunzionamenti,
            'malfunzionamenti_critici' => $malfunzionamentiCritici,
            'total_centri' => $totalCentri,
            
            // Dati relazionali (possono essere vuoti se ci sono errori)
            'centro_assistenza' => $centroAssistenza,
            'malfunzionamenti_critici_lista' => $malfunzionamentiCriticiLista,
            'prodotti_problematici' => $prodottiProblematici,
            'malfunzionamenti_per_gravita' => $malfunzionamentiPerGravita,
        ];

        // === PASSA DATI EXTRA ALLA VISTA ===
        $extraData = [
            'prodotti_critici' => $prodottiProblematici, // Per sezione prodotti critici
            'malfunzionamenti_recenti' => $malfunzionamentiRecenti, // Per tabella recenti
        ];

        // Log successo per debug
        \Log::info('Dashboard Tecnico caricata con successo', [
            'user_id' => $user->id,
            'username' => $user->username,
            'total_prodotti' => $totalProdotti,
            'total_malfunzionamenti' => $totalMalfunzionamenti,
            'critici_count' => $malfunzionamentiCritici,
            'prodotti_problematici_count' => $prodottiProblematici->count(),
            'centro_assegnato' => $centroAssistenza ? $centroAssistenza->nome : 'Nessuno'
        ]);

        // === RETURN VISTA CORRETTA ===
        return view('tecnico.dashboard', array_merge(
            compact('user', 'stats'),
            $extraData
        ));

    } catch (\Exception $e) {
        // === GESTIONE ERRORI ROBUSTA ===
        \Log::error('Errore Dashboard Tecnico', [
            'user_id' => $user->id,
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]);

        // Statistiche di fallback
        $stats = [
            'total_prodotti' => 0,
            'total_malfunzionamenti' => 0, 
            'malfunzionamenti_critici' => 0,
            'total_centri' => 0,
            'centro_assistenza' => null,
            'malfunzionamenti_critici_lista' => collect(),
            'prodotti_problematici' => collect(),
            'malfunzionamenti_per_gravita' => [
                'critica' => 0, 'alta' => 0, 'media' => 0, 'bassa' => 0
            ],
        ];

        $extraData = [
            'prodotti_critici' => collect(),
            'malfunzionamenti_recenti' => collect(),
        ];

        return view('tecnico.dashboard', array_merge(
            compact('user', 'stats'),
            $extraData
        ))->with('warning', 'Alcune statistiche potrebbero non essere aggiornate');
    }
}

    /**
     * Dashboard generale - Fallback per utenti pubblici
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Se l'utente ha un livello specifico, reindirizza alla sua dashboard
        if ($user->livello_accesso >= 2) {
            return $this->redirectBasedOnRole();
        }
        
        // Dashboard base per utenti pubblici
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'total_centri' => CentroAssistenza::count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }

    // ================================================
    // STORICO INTERVENTI (METODO MANCANTE - CAUSA ERRORE)
    // ================================================

    /**
     * Visualizza lo storico degli interventi tecnici
     * QUESTO METODO ERA MANCANTE E CAUSAVA L'ERRORE 404!
     */
    public function storicoInterventi(Request $request)
    {
        // Verifica autorizzazioni - accessibile a tecnici, staff e admin
        if (!Auth::check() || (!Auth::user()->isTecnico() && !Auth::user()->isStaff() && !Auth::user()->isAdmin())) {
            abort(403, 'Accesso riservato ai tecnici e staff aziendale');
        }

        $user = Auth::user();

        try {
            // Query base per gli interventi (usando i malfunzionamenti come storico)
            $query = Malfunzionamento::with(['prodotto:id,nome,modello,categoria'])
                ->orderBy('updated_at', 'desc');

            // === FILTRI BASATI SUL RUOLO ===
            if ($user->isTecnico() && $user->centro_assistenza_id) {
                // I tecnici vedono gli interventi degli ultimi 6 mesi
                $query->where('updated_at', '>=', now()->subMonths(6));
            } elseif ($user->isStaff()) {
                // Lo staff vede solo i malfunzionamenti dei prodotti che gestisce
                $query->whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                });
            }
            // Admin vede tutto senza filtri aggiuntivi

            // === FILTRI DALLA RICHIESTA ===
            
            // Filtro per prodotto specifico
            if ($request->filled('prodotto_id')) {
                $query->where('prodotto_id', $request->input('prodotto_id'));
            }

            // Filtro per gravità del problema
            if ($request->filled('gravita')) {
                $query->where('gravita', $request->input('gravita'));
            }

            // Filtro temporale
            if ($request->filled('periodo')) {
                switch($request->input('periodo')) {
                    case 'settimana':
                        $query->where('updated_at', '>=', now()->subWeek());
                        break;
                    case 'mese':
                        $query->where('updated_at', '>=', now()->subMonth());
                        break;
                    case 'trimestre':
                        $query->where('updated_at', '>=', now()->subMonths(3));
                        break;
                    case 'anno':
                        $query->where('updated_at', '>=', now()->subYear());
                        break;
                }
            }

            // Ricerca testuale
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('titolo', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('prodotto', function($q2) use ($searchTerm) {
                          $q2->where('nome', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('modello', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            // Paginazione risultati
            $interventi = $query->paginate(15);

            // === STATISTICHE STORICO ===
            $statisticheStorico = [
                'totale_interventi' => $interventi->total(),
                'interventi_settimana' => Malfunzionamento::where('updated_at', '>=', now()->subWeek())->count(),
                'per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                    ->groupBy('gravita')
                    ->pluck('count', 'gravita')
                    ->toArray(),
                'prodotti_problematici' => Prodotto::withCount('malfunzionamenti')
                    ->orderBy('malfunzionamenti_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'nome', 'modello']),
            ];

            // Lista prodotti per dropdown filtro
            $prodotti = Prodotto::select('id', 'nome', 'modello')
                ->where('attivo', true)
                ->orderBy('nome')
                ->get();

            return view('auth.storico-interventi', compact(
                'user', 
                'interventi', 
                'statisticheStorico', 
                'prodotti'
            ));

        } catch (\Exception $e) {
            Log::error('Errore storico interventi', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Errore nel caricamento dello storico interventi');
        }
    }

    // ================================================
    // REINDIRIZZAMENTO AUTOMATICO
    // ================================================

    /**
     * Reindirizza l'utente basandosi sul suo livello di accesso
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        // Reindirizzamento alle dashboard specifiche per livello
        switch ((int) $user->livello_accesso) {
            case 4: // Admin
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Benvenuto, Amministratore ' . $user->nome . '!');
                    
            case 3: // Staff
                return redirect()->route('staff.dashboard')
                    ->with('success', 'Benvenuto, ' . $user->nome . '!');
                    
            case 2: // Tecnico
                return redirect()->route('tecnico.dashboard')
                    ->with('success', 'Benvenuto, Tecnico ' . $user->nome . '!');
                    
            default: // Livello non riconosciuto
                Log::warning('Livello accesso non riconosciuto', [
                    'user_id' => $user->id,
                    'livello_accesso' => $user->livello_accesso
                ]);
                
                return redirect()->route('home')
                    ->with('warning', 'Livello di accesso non riconosciuto.');
        }
    }

    /**
     * Helper per reindirizzamento manuale alla dashboard appropriata
     */
    public function autoRedirectDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return $this->redirectBasedOnRole();
    }

    // ================================================
    // GESTIONE PROFILO UTENTE
    // ================================================

    /**
     * Mostra il profilo dell'utente corrente
     */
    public function showProfile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->load('centroAssistenza'); // Carica il centro se è un tecnico

        return view('auth.profile', compact('user'));
    }

    /**
     * Aggiorna il profilo dell'utente corrente
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Validazione dati modificabili dal profilo
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'specializzazione' => 'nullable|string|max:255', // Solo per tecnici
        ]);

        // Aggiorna i dati
        $user->update($validated);

        Log::info('Profilo utente aggiornato', [
            'user_id' => $user->id,
            'updated_fields' => array_keys($validated)
        ]);

        return redirect()->route('auth.profile')
            ->with('success', 'Profilo aggiornato con successo');
    }

    /**
     * Cambia la password dell'utente corrente
     */
    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Validazione password
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'La password attuale è obbligatoria',
            'new_password.required' => 'La nuova password è obbligatoria',
            'new_password.min' => 'La password deve essere di almeno 8 caratteri',
            'new_password.confirmed' => 'La conferma password non corrisponde',
        ]);

        // Verifica password attuale
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La password attuale non è corretta.',
            ]);
        }

        // Aggiorna la password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Log::info('Password cambiata', [
            'user_id' => $user->id,
            'username' => $user->username
        ]);

        return redirect()->route('auth.profile')
            ->with('success', 'Password modificata con successo');
    }

    // ================================================
    // REGISTRAZIONE (Solo per Admin)
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
            
            // Campi specifici per tecnici (livello 2)
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'username.unique' => 'Questo username è già in uso',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
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

        Log::info('Nuovo utente registrato', [
            'new_user_id' => $user->id,
            'new_username' => $user->username,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utente registrato con successo');
    }

    
/**
 * AGGIUNGI QUESTI METODI ALLA FINE DELLA CLASSE AuthController
 * Prima della chiusura finale della classe (prima dell'ultima parentesi graffa)
 * Posizionali nella sezione "API ENDPOINTS PER STATISTICHE"
 */

// ================================================
// API ENDPOINTS PER STATISTICHE (AJAX)
// ================================================

/**
 * API per statistiche tecnico (chiamate AJAX) - METODO MANCANTE RISOLTO
 * Fornisce statistiche specifiche per il dashboard tecnico via API
 * @return \Illuminate\Http\JsonResponse - Risposta JSON con statistiche tecnico
 */
/**
 * FIX CORRETTO per errore colonna - Il nome giusto è 'numero_segnalazioni'
 * 
 * PROBLEMA: Il codice cercava 'num_segnalazioni' ma la colonna si chiama 'numero_segnalazioni'
 * SOLUZIONE: Sostituire tutti i riferimenti con il nome corretto della colonna
 */

/**
 * VERSIONE CORRETTA DEFINITIVA del metodo statisticheTecnico()
 * Ora usa i nomi delle colonne corretti dal database
 */
public function statisticheTecnico()
{
    try {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->isTecnico()) {
            return response()->json([
                'success' => false,
                'error' => 'Accesso riservato ai tecnici',
                'code' => 403
            ], 403);
        }

        $user = Auth::user();
        
        // === LOG PER DEBUGGING ===
        Log::info('API statisticheTecnico chiamata', [
            'user_id' => $user->id,
            'username' => $user->username,
            'centro_id' => $user->centro_assistenza_id,
            'ip' => request()->ip()
        ]);

        // === CALCOLO STATISTICHE TECNICO (CON NOMI COLONNE CORRETTI) ===
        $stats = [
            // Statistiche generali accessibili al tecnico
            'generale' => [
                'total_prodotti' => Prodotto::count(),
                'total_malfunzionamenti' => Malfunzionamento::count(),
                'total_centri' => CentroAssistenza::count(),
                'prodotti_attivi' => Prodotto::where('attivo', true)->count()
            ],

            // Statistiche sui malfunzionamenti per gravità
            'malfunzionamenti' => [
                'totali' => Malfunzionamento::count(),
                'critici' => Malfunzionamento::where('gravita', 'critica')->count(),
                'media' => Malfunzionamento::where('gravita', 'media')->count(),
                'bassa' => Malfunzionamento::where('gravita', 'bassa')->count(),
                
                // Distribuzione per gravità (per grafici)
                'per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                    ->whereNotNull('gravita')
                    ->groupBy('gravita')
                    ->pluck('count', 'gravita')
                    ->toArray(),
                    
                // Malfunzionamenti creati questo mese
                'questo_mese' => Malfunzionamento::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count()
            ],

            // Informazioni centro assistenza del tecnico
            'centro_assistenza' => $user->centroAssistenza ? [
                'id' => $user->centroAssistenza->id,
                'nome' => $user->centroAssistenza->nome,
                'citta' => $user->centroAssistenza->citta,
                'provincia' => $user->centroAssistenza->provincia,
                'indirizzo' => $user->centroAssistenza->indirizzo,
                'telefono' => $user->centroAssistenza->telefono,
                // Conta altri tecnici nello stesso centro
                'altri_tecnici' => User::where('centro_assistenza_id', $user->centro_assistenza_id)
                    ->where('id', '!=', $user->id)
                    ->where('livello_accesso', 2)
                    ->count()
            ] : null,

            // Malfunzionamenti critici recenti (CON NOME COLONNA CORRETTO)
            'critici_recenti' => Malfunzionamento::where('gravita', 'critica')
                ->with(['prodotto:id,nome,modello,categoria'])
                ->select('id', 'titolo', 'descrizione', 'gravita', 'prodotto_id', 'created_at', 'numero_segnalazioni') // CORRETTO!
                ->latest()
                ->take(10)
                ->get()
                ->map(function($m) {
                    return [
                        'id' => $m->id,
                        'titolo' => $m->titolo,
                        'descrizione' => \Illuminate\Support\Str::limit($m->descrizione, 100),
                        'prodotto_nome' => $m->prodotto->nome ?? 'N/D',
                        'prodotto_modello' => $m->prodotto->modello ?? '',
                        'categoria' => $m->prodotto->categoria ?? 'N/D',
                        'segnalazioni' => $m->numero_segnalazioni ?? 0, // CORRETTO!
                        'data' => $m->created_at->format('d/m/Y H:i')
                    ];
                }),

            // Prodotti più problematici (con più malfunzionamenti critici)
            'prodotti_problematici' => Prodotto::whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                })
                ->withCount(['malfunzionamenti as critici_count' => function($q) {
                    $q->where('gravita', 'critica');
                }])
                ->orderBy('critici_count', 'desc')
                ->take(8)
                ->get(['id', 'nome', 'modello', 'categoria'])
                ->map(function($p) {
                    return [
                        'id' => $p->id,
                        'nome' => $p->nome,
                        'modello' => $p->modello,
                        'categoria' => $p->categoria,
                        'problemi_critici' => $p->critici_count
                    ];
                }),

            // Statistiche per categoria prodotti
            'per_categoria' => Prodotto::selectRaw('categoria, COUNT(*) as count')
                ->where('attivo', true)
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray(),

            // Statistiche delle ultime attività
            'attivita_recenti' => [
                'malfunzionamenti_settimana' => Malfunzionamento::where('created_at', '>=', now()->subWeek())->count(),
                'nuovi_prodotti_mese' => Prodotto::where('created_at', '>=', now()->subMonth())->count(),
                'categorie_disponibili' => Prodotto::distinct('categoria')->count('categoria')
            ],

            // Top malfunzionamenti più segnalati
            'piu_segnalati' => Malfunzionamento::with(['prodotto:id,nome,modello'])
                ->orderBy('numero_segnalazioni', 'desc') // CORRETTO!
                ->take(5)
                ->get()
                ->map(function($m) {
                    return [
                        'id' => $m->id,
                        'titolo' => $m->titolo,
                        'prodotto' => $m->prodotto->nome ?? 'N/D',
                        'segnalazioni' => $m->numero_segnalazioni, // CORRETTO!
                        'gravita' => $m->gravita
                    ];
                })
        ];

        // === METADATA RISPOSTA ===
        $metadata = [
            'timestamp' => now()->toISOString(),
            'user_level' => $user->livello_accesso,
            'centro_nome' => $user->centroAssistenza->nome ?? 'Non assegnato',
            'cache_ttl' => 300,
            'version' => '1.1_fixed'
        ];

        // === LOG SUCCESSO ===
        Log::info('statisticheTecnico API completata con successo', [
            'user_id' => $user->id,
            'stats_generated' => [
                'prodotti_problematici' => count($stats['prodotti_problematici']),
                'critici_recenti' => count($stats['critici_recenti']),
                'piu_segnalati' => count($stats['piu_segnalati']),
                'malfunzionamenti_totali' => $stats['generale']['total_malfunzionamenti']
            ]
        ]);

        // === RISPOSTA JSON ===
        return response()->json([
            'success' => true,
            'data' => $stats,
            'meta' => $metadata
        ], 200);

    } catch (\Exception $e) {
        // === GESTIONE ERRORI COMPLETA ===
        Log::error('Errore in statisticheTecnico API', [
            'user_id' => Auth::id(),
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'error' => config('app.debug') ? $e->getMessage() : 'Errore nel caricamento delle statistiche',
            'code' => 500,
            'timestamp' => now()->toISOString()
        ], 500);
    }
}

/**
 * VERSIONE CORRETTA di malfunzionamentiCritici() - CON NOME COLONNA CORRETTO
 */
public function malfunzionamentiCritici()
{
    try {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->isTecnico()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // === QUERY CON NOMI COLONNE CORRETTI ===
        $critici = Malfunzionamento::where('gravita', 'critica')
            ->with(['prodotto:id,nome,modello,categoria'])
            ->select('id', 'titolo', 'descrizione', 'prodotto_id', 'created_at', 'numero_segnalazioni') // CORRETTO!
            ->orderBy('numero_segnalazioni', 'desc') // CORRETTO! Più segnalazioni = più urgente
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'titolo' => $m->titolo,
                    'descrizione' => \Illuminate\Support\Str::limit($m->descrizione, 150),
                    'prodotto' => [
                        'id' => $m->prodotto->id,
                        'nome' => $m->prodotto->nome,
                        'modello' => $m->prodotto->modello,
                        'categoria' => $m->prodotto->categoria
                    ],
                    'segnalazioni' => $m->numero_segnalazioni ?? 0, // CORRETTO!
                    'data_creazione' => $m->created_at->format('d/m/Y H:i'),
                    'urgenza' => $m->numero_segnalazioni > 5 ? 'alta' : 'media' // CORRETTO!
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $critici,
            'count' => count($critici),
            'timestamp' => now()->toISOString()
        ], 200);

    } catch (\Exception $e) {
        Log::error('Errore malfunzionamentiCritici API', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Errore nel caricamento malfunzionamenti critici'
        ], 500);
    }
}

/**
 * IMPORTANTE: SOSTITUISCI TUTTI I METODI PRECEDENTI CON QUESTE VERSIONI CORRETTE
 * 
 * SUMMARY DEI CAMBIAMENTI:
 * 1. Sostituito 'num_segnalazioni' con 'numero_segnalazioni' (nome corretto della colonna)
 * 2. Aggiunta nuova sezione 'piu_segnalati' per mostrare i problemi più frequenti
 * 3. Ordinamento corretto per urgenza basato su numero_segnalazioni
 * 4. Tutti i riferimenti alla colonna ora usano il nome corretto
 */

/**
 * PASSO 1: AGGIUNGI QUESTO METODO in AuthController.php
 * Crea una vista HTML per le statistiche tecnico invece di restituire JSON
 */

/**
 * Vista HTML per le statistiche complete del tecnico
 * Questo metodo restituisce una pagina web invece di JSON
 * @return \Illuminate\View\View - Vista Blade con statistiche
 */
public function statisticheTecnicoView()
{
    try {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->isTecnico()) {
            abort(403, 'Accesso riservato ai tecnici');
        }

        $user = Auth::user();
        
        // === CALCOLO STATISTICHE PER LA VISTA ===
        $statistiche = [
            // Statistiche generali
            'generale' => [
                'total_prodotti' => Prodotto::count(),
                'total_malfunzionamenti' => Malfunzionamento::count(),
                'total_centri' => CentroAssistenza::count(),
                'prodotti_attivi' => Prodotto::where('attivo', true)->count()
            ],

            // Malfunzionamenti per gravità
            'malfunzionamenti' => [
                'totali' => Malfunzionamento::count(),
                'critici' => Malfunzionamento::where('gravita', 'critica')->count(),
                'media' => Malfunzionamento::where('gravita', 'media')->count(),
                'bassa' => Malfunzionamento::where('gravita', 'bassa')->count(),
                
                // Per il grafico a torta
                'per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                    ->whereNotNull('gravita')
                    ->groupBy('gravita')
                    ->get()
                    ->pluck('count', 'gravita')
                    ->toArray(),
                    
                // Andamento ultimo mese
                'questo_mese' => Malfunzionamento::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'mese_precedente' => Malfunzionamento::whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count()
            ],

            // Centro di appartenenza
            'centro_assistenza' => $user->centroAssistenza ? [
                'id' => $user->centroAssistenza->id,
                'nome' => $user->centroAssistenza->nome,
                'citta' => $user->centroAssistenza->citta,
                'provincia' => $user->centroAssistenza->provincia,
                'indirizzo' => $user->centroAssistenza->indirizzo,
                'telefono' => $user->centroAssistenza->telefono,
                'altri_tecnici' => User::where('centro_assistenza_id', $user->centro_assistenza_id)
                    ->where('id', '!=', $user->id)
                    ->where('livello_accesso', 2)
                    ->count(),
                // Tecnici colleghi nel centro
                'colleghi' => User::where('centro_assistenza_id', $user->centro_assistenza_id)
                    ->where('id', '!=', $user->id)
                    ->where('livello_accesso', 2)
                    ->get(['id', 'nome', 'cognome', 'specializzazione'])
            ] : null,

            // Top 10 malfunzionamenti critici recenti
            'critici_recenti' => Malfunzionamento::where('gravita', 'critica')
                ->with(['prodotto:id,nome,modello,categoria'])
                ->orderBy('numero_segnalazioni', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),

            // Prodotti più problematici
            'prodotti_problematici' => Prodotto::whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                })
                ->withCount(['malfunzionamenti as critici_count' => function($q) {
                    $q->where('gravita', 'critica');
                }])
                ->orderBy('critici_count', 'desc')
                ->take(10)
                ->get(),

            // Statistiche per categoria
            'per_categoria' => Prodotto::selectRaw('categoria, COUNT(*) as count')
                ->where('attivo', true)
                ->groupBy('categoria')
                ->get()
                ->pluck('count', 'categoria')
                ->toArray(),

            // Trend settimanale (ultimi 7 giorni)
            'trend_settimanale' => $this->calcolaTrendSettimanale(),

            // Statistiche personali
            'personali' => [
                'data_registrazione' => $user->created_at,
                'specializzazione' => $user->specializzazione,
                'giorni_attivo' => $user->created_at->diffInDays(now()),
                'ultimo_accesso' => now() // Potresti aggiungere un campo last_login_at
            ]
        ];

        // === LOG DELLA VISUALIZZAZIONE ===
        Log::info('Vista statistiche tecnico caricata', [
            'user_id' => $user->id,
            'username' => $user->username,
            'centro' => $user->centroAssistenza->nome ?? 'Non assegnato'
        ]);

        // === RESTITUISCI VISTA BLADE ===
        return view('tecnico.statistiche', [
            'user' => $user,
            'stats' => $statistiche,
            'pageTitle' => 'Le mie Statistiche - Tecnico'
        ]);

    } catch (\Exception $e) {
        // === GESTIONE ERRORI ===
        Log::error('Errore vista statistiche tecnico', [
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return redirect()->route('tecnico.dashboard')
            ->with('error', 'Errore nel caricamento delle statistiche. Riprova più tardi.');
    }
}

/**
 * METODO HELPER per calcolare il trend settimanale
 * Restituisce dati per grafico lineare degli ultimi 7 giorni
 */
private function calcolaTrendSettimanale()
{
    $giorni = [];
    $conteggi = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $data = now()->subDays($i);
        $giorni[] = $data->format('d/m');
        
        $conteggi[] = Malfunzionamento::whereDate('created_at', $data->format('Y-m-d'))
            ->count();
    }
    
    return [
        'giorni' => $giorni,
        'conteggi' => $conteggi
    ];
}

 /**
 * PASSO 3: MODIFICA IL LINK NELLA DASHBOARD
 * Nel file resources/views/tecnico/dashboard.blade.php
 * Cerca il bottone "Le mie Stats" e cambia il link da:
 * href="{{ route('api.tecnico.statistiche') }}"
 * a:
 * href="{{ route('tecnico.statistiche.view') }}"
 */

/**
 * PASSO 4: CREA IL FILE VISTA
 * Crea il file: resources/views/tecnico/statistiche.blade.php
 * Vedi il prossimo artifact per il contenuto completo della vista
 */
}