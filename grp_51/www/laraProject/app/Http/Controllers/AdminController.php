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
 * Controller dedicato alle funzionalità amministrative avanzate
 * Gestisce assegnazioni, statistiche, manutenzione e export dati
 * CORREZIONE: Fix del calcolo prodotti non assegnati
 */
class AdminController extends Controller
{
    /**
     * Costruttore - verifica che solo gli admin possano accedere
     */
    public function __construct()
    {
        // Middleware per verificare che solo gli admin possano accedere
        $this->middleware(['auth', 'check.level:4']);
    }

    // ================================================
    // GESTIONE ASSEGNAZIONI PRODOTTI A STAFF
    // ================================================

   /**
 * Metodo assegnazioni CORRETTO - AdminController
 * Fix per il problema della variabile $stats non definita
 */
 public function assegnazioni(Request $request)
    {
        // Carica tutti i membri dello staff (livello 3)
        $staffMembers = User::where('livello_accesso', '3')
            ->orderBy('nome')
            ->orderBy('cognome')
            ->get();

        // Query prodotti con possibilità di filtri
        $query = Prodotto::with('staffAssegnato');

        // Filtro per prodotti non assegnati
        if ($request->boolean('non_assegnati')) {
            $query->whereNull('staff_assegnato_id');
        }

        // Filtro per staff specifico
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            if ($staffId === 'null') {
                // Filtra prodotti non assegnati
                $query->whereNull('staff_assegnato_id');
            } else {
                // Filtra prodotti assegnati a staff specifico
                $query->where('staff_assegnato_id', $staffId);
            }
        }

        // Filtro per categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // Ricerca per nome/modello
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('modello', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Carica prodotti con paginazione e conteggio malfunzionamenti
        $prodotti = $query->withCount([
                'malfunzionamenti',
                'malfunzionamenti as critici_count' => function($query) {
                    $query->where('gravita', 'critica');
                }
            ])
            ->paginate(20);

        // Statistiche assegnazioni
        $stats = [
            'totale_prodotti' => Prodotto::count(),
            'prodotti_assegnati' => Prodotto::whereNotNull('staff_assegnato_id')->count(),
            'prodotti_non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(),
            'staff_attivi' => $staffMembers->count(),
        ];

        // Categorie disponibili per filtro
        $categorie = Prodotto::getCategorieUnifico();

        // Log per debug
        Log::info('Pagina assegnazioni caricata', [
            'admin_id' => Auth::id(),
            'filtri_applicati' => $request->only(['search', 'staff_id', 'categoria', 'non_assegnati']),
            'prodotti_trovati' => $prodotti->total(),
            'staff_disponibili' => $staffMembers->count()
        ]);

        return view('admin.assegnazioni', compact(
            'prodotti', 'staffMembers', 'stats', 'categorie'
        ));
    }






/**
 * Assegna un prodotto a un membro dello staff
 */

    /**
     * Assegnazione multipla di prodotti
     */
   public function assegnaProdotto(Request $request)
    {
        // Validazione input
        $request->validate([
            'prodotto_id' => 'required|exists:prodotti,id',
            'staff_id' => 'nullable|exists:users,id',
        ], [
            'prodotto_id.required' => 'ID prodotto non specificato',
            'prodotto_id.exists' => 'Il prodotto selezionato non esiste',
            'staff_id.exists' => 'Il membro dello staff selezionato non esiste'
        ]);

        try {
            // Trova il prodotto
            $prodotto = Prodotto::findOrFail($request->prodotto_id);
            $staffId = $request->staff_id ?: null;

            // Se è specificato uno staff, verifica che sia di livello 3
            if ($staffId) {
                $staff = User::findOrFail($staffId);
                if ($staff->livello_accesso !== '3') {
                    return back()->withErrors([
                        'staff_id' => 'L\'utente selezionato non è un membro dello staff aziendale'
                    ]);
                }
            }

            // Salva lo staff precedente per il log
            $staffPrecedente = $prodotto->staffAssegnato;

            // Aggiorna l'assegnazione
            $prodotto->update(['staff_assegnato_id' => $staffId]);

            // Prepara messaggio di successo
            if ($staffId) {
                $nuovoStaff = User::find($staffId);
                $message = "Prodotto \"{$prodotto->nome}\" assegnato a {$nuovoStaff->nome_completo} con successo";
            } else {
                $message = "Assegnazione rimossa dal prodotto \"{$prodotto->nome}\" con successo";
            }

            // Log dell'operazione per audit
            Log::info('Assegnazione prodotto modificata', [
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'staff_precedente_id' => $staffPrecedente?->id,
                'staff_precedente_nome' => $staffPrecedente?->nome_completo,
                'nuovo_staff_id' => $staffId,
                'nuovo_staff_nome' => $staffId ? $nuovoStaff->nome_completo : null,
                'admin_id' => Auth::id(),
                'admin_username' => Auth::user()->username,
                'timestamp' => now()
            ]);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Log dell'errore
            Log::error('Errore nell\'assegnazione prodotto', [
                'prodotto_id' => $request->prodotto_id,
                'staff_id' => $request->staff_id,
                'error_message' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors([
                'assegnazione' => 'Errore durante l\'assegnazione. Riprova o contatta l\'amministratore.'
            ]);
        }
    }

    // ================================================
    // STATISTICHE AVANZATE
    // ================================================

    /**
     * Pagina statistiche avanzate per amministratori
     */
    public function statisticheGenerali(Request $request)
    {
        // Periodo di analisi (default ultimo mese)
        $periodo = $request->input('periodo', 30);
        $dataInizio = now()->subDays($periodo);

        // === STATISTICHE GENERALI ===
        $stats = [
            // Contatori principali
            'utenti_totali' => User::count(),
            'prodotti_totali' => Prodotto::count(),
            'malfunzionamenti_totali' => Malfunzionamento::count(),
            'centri_totali' => CentroAssistenza::count(),

            // Crescita nel periodo
            'nuovi_utenti' => User::where('created_at', '>=', $dataInizio)->count(),
            'nuovi_prodotti' => Prodotto::where('created_at', '>=', $dataInizio)->count(),
            'nuove_soluzioni' => Malfunzionamento::where('created_at', '>=', $dataInizio)->count(),

            // Attività recente
            'utenti_attivi' => User::where('last_login_at', '>=', $dataInizio)->count(),
            'soluzioni_aggiornate' => Malfunzionamento::where('updated_at', '>=', $dataInizio)->count(),
        ];

        // === DISTRIBUZIONE UTENTI PER LIVELLO ===
        $distribuzioneUtenti = User::selectRaw('livello_accesso, COUNT(*) as count')
            ->groupBy('livello_accesso')
            ->orderBy('livello_accesso')
            ->get()
            ->pluck('count', 'livello_accesso')
            ->toArray();

        // === PRODOTTI PER CATEGORIA ===
        $prodottiPerCategoria = Prodotto::selectRaw('categoria, COUNT(*) as count')
            ->whereNotNull('categoria')
            ->groupBy('categoria')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'categoria')
            ->toArray();

        // === MALFUNZIONAMENTI PER GRAVITÀ ===
        $malfunzionamentiPerGravita = Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
            ->whereNotNull('gravita')
            ->groupBy('gravita')
            ->get()
            ->pluck('count', 'gravita')
            ->toArray();

        // === TOP PRODOTTI CON PIÙ PROBLEMI ===
        $prodottiProblematici = Prodotto::withCount('malfunzionamenti')
            ->having('malfunzionamenti_count', '>', 0)
            ->orderBy('malfunzionamenti_count', 'desc')
            ->limit(10)
            ->get();

        // === STAFF PIÙ ATTIVI ===
        $staffAttivi = User::where('livello_accesso', '3')
            ->withCount(['malfunzionamentiCreati' => function($q) use ($dataInizio) {
                $q->where('created_at', '>=', $dataInizio);
            }])
            ->orderBy('malfunzionamenti_creati_count', 'desc')
            ->limit(10)
            ->get();

        // === CRESCITA NEL TEMPO ===
        $crescitaUtenti = User::selectRaw('DATE(created_at) as data, COUNT(*) as count')
            ->where('created_at', '>=', $dataInizio)
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        $crescitaSoluzioni = Malfunzionamento::selectRaw('DATE(created_at) as data, COUNT(*) as count')
            ->where('created_at', '>=', $dataInizio)
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        return view('admin.statistiche', compact(
            'stats', 'distribuzioneUtenti', 'prodottiPerCategoria',
            'malfunzionamentiPerGravita', 'prodottiProblematici',
            'staffAttivi', 'crescitaUtenti', 'crescitaSoluzioni', 'periodo'
        ));
    }

    // ================================================
    // MANUTENZIONE SISTEMA
    // ================================================

    /**
     * Pagina manutenzione sistema
     */
    public function manutenzione()
    {
        // Informazioni sistema
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'database_version' => $this->getDatabaseVersion(),
            'storage_usage' => $this->getStorageUsage(),
            'log_files' => $this->getLogFiles(),
        ];

        // Stato delle cache
        $cacheStatus = [
            'config' => $this->isCacheEnabled('config'),
            'route' => $this->isCacheEnabled('route'),
            'view' => $this->isCacheEnabled('view'),
        ];

        return view('admin.manutenzione', compact('systemInfo', 'cacheStatus'));
    }

    /**
     * Pulisce le cache dell'applicazione
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'type' => 'required|in:all,config,route,view,application'
        ]);

        $type = $request->input('type');
        $cleared = [];

        try {
            switch ($type) {
                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $cleared = ['application', 'config', 'route', 'view'];
                    break;

                case 'config':
                    Artisan::call('config:clear');
                    $cleared = ['config'];
                    break;

                case 'route':
                    Artisan::call('route:clear');
                    $cleared = ['route'];
                    break;

                case 'view':
                    Artisan::call('view:clear');
                    $cleared = ['view'];
                    break;

                case 'application':
                    Artisan::call('cache:clear');
                    $cleared = ['application'];
                    break;
            }

            Log::info('Cache pulite dall\'admin', [
                'type' => $type,
                'cleared' => $cleared,
                'admin_id' => Auth::id()
            ]);

            return back()->with('success', 'Cache ' . implode(', ', $cleared) . ' pulite con successo');

        } catch (\Exception $e) {
            Log::error('Errore pulizia cache', [
                'type' => $type,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['cache' => 'Errore nella pulizia cache: ' . $e->getMessage()]);
        }
    }

    /**
     * Ottimizza il database
     */
    public function optimizeDatabase()
    {
        try {
            // Ottimizza tutte le tabelle
            $tables = DB::select("SHOW TABLES");
            $optimized = [];

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimized[] = $tableName;
            }

            Log::info('Database ottimizzato dall\'admin', [
                'tables_optimized' => count($optimized),
                'admin_id' => Auth::id()
            ]);

            return back()->with('success', 'Database ottimizzato con successo. Tabelle processate: ' . count($optimized));

        } catch (\Exception $e) {
            Log::error('Errore ottimizzazione database', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['database' => 'Errore nell\'ottimizzazione: ' . $e->getMessage()]);
        }
    }

    // ================================================
    // EXPORT DATI
    // ================================================

    /**
     * Pagina export dati
     */
    public function export()
    {
        // Statistiche sui dati disponibili per export
        $exportStats = [
            'utenti' => User::count(),
            'prodotti' => Prodotto::count(),
            'malfunzionamenti' => Malfunzionamento::count(),
            'centri' => CentroAssistenza::count(),
        ];

        return view('admin.export', compact('exportStats'));
    }

    /**
     * Esporta tutti i dati in formato JSON
     */
    public function exportAll(Request $request)
    {
        $request->validate([
            'format' => 'required|in:json,csv',
            'include_sensitive' => 'boolean'
        ]);

        $includeSensitive = $request->boolean('include_sensitive');
        $format = $request->input('format');

        try {
            // Raccolta dati
            $data = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->nome_completo,
                'include_sensitive' => $includeSensitive,
                'stats' => [
                    'total_users' => User::count(),
                    'total_products' => Prodotto::count(),
                    'total_malfunctions' => Malfunzionamento::count(),
                    'total_centers' => CentroAssistenza::count(),
                ]
            ];

            // Utenti (senza password)
            $userFields = ['id', 'username', 'nome', 'cognome', 'livello_accesso', 'created_at'];
            if ($includeSensitive) {
                $userFields = array_merge($userFields, ['data_nascita', 'specializzazione', 'centro_assistenza_id']);
            }
            $data['users'] = User::select($userFields)->get();

            // Prodotti
            $data['products'] = Prodotto::with('staffAssegnato:id,nome,cognome')->get();

            // Malfunzionamenti
            $data['malfunctions'] = Malfunzionamento::with(['prodotto:id,nome', 'creatoBy:id,nome,cognome'])->get();

            // Centri assistenza
            $data['centers'] = CentroAssistenza::withCount('tecnici')->get();

            $filename = 'sistema_assistenza_export_' . now()->format('Y-m-d_H-i-s');

            if ($format === 'json') {
                return response()->json($data)
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
            } else {
                // Per CSV, esporta solo una tabella alla volta
                return $this->exportToCsv($data['users'], $filename . '_users.csv');
            }

        } catch (\Exception $e) {
            Log::error('Errore export dati', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['export' => 'Errore nell\'export: ' . $e->getMessage()]);
        }
    }

    // ================================================
    // API ENDPOINTS PER DASHBOARD AJAX - CORREZIONE PRINCIPALE
    // ================================================

    /**
     * API per aggiornamento statistiche dashboard admin (AJAX)
     * CORREZIONE: Fix del campo per prodotti non assegnati
     */
    public function statsUpdate()
    {
        try {
            // === CORREZIONE PRINCIPALE ===
            // Conta i prodotti che NON hanno uno staff assegnato
            // ERRORE ERA: 'utente_id' - CORRETTO IN: 'staff_assegnato_id'
            $prodottiNonAssegnatiCount = Prodotto::whereNull('staff_assegnato_id')->count();
            
            // Lista dei prodotti non assegnati per il riquadro dettaglio
            $prodottiNonAssegnatiLista = Prodotto::whereNull('staff_assegnato_id')
                ->select('id', 'nome', 'modello', 'categoria', 'created_at', 'attivo')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $stats = [
                // Contatori principali
                'total_utenti' => User::count(),
                'total_prodotti' => Prodotto::count(),
                'total_centri' => CentroAssistenza::count(),
                'total_soluzioni' => Malfunzionamento::count(),

                // Contatori dinamici
                'utenti_attivi' => User::where('last_login_at', '>=', now()->subDays(30))->count(),

                // === FIX: CAMPO CORRETTO ===
                'prodotti_non_assegnati_count' => $prodottiNonAssegnatiCount,
                'prodotti_non_assegnati' => $prodottiNonAssegnatiLista,

                'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),
                'nuovi_utenti_oggi' => User::whereDate('created_at', today())->count(),

                // Statistiche aggiuntive per la dashboard
                'prodotti_attivi' => Prodotto::where('attivo', true)->count(),
                'prodotti_inattivi' => Prodotto::where('attivo', false)->count(),
                'staff_disponibili' => User::where('livello_accesso', '3')->count(),

                // Distribuzione utenti per livello
                'distribuzione_utenti' => User::selectRaw('livello_accesso, COUNT(*) as count')
                    ->groupBy('livello_accesso')
                    ->pluck('count', 'livello_accesso')
                    ->toArray(),

                // Timestamp aggiornamento
                'last_update' => now()->toISOString(),
                'update_time' => now()->format('H:i:s')
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'message' => 'Statistiche aggiornate con successo'
            ]);

        } catch (\Exception $e) {
            Log::error('Errore aggiornamento statistiche admin dashboard', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento delle statistiche'
            ], 500);
        }
    }

    /**
     * METODO AGGIUNTIVO: Ottieni dettagli prodotti non assegnati (AJAX)
     * Nuovo endpoint per popolare specificamente il riquadro dei prodotti non assegnati
     */
    public function prodottiNonAssegnati()
    {
        try {
            // Prodotti senza staff assegnato
            $prodotti = Prodotto::whereNull('staff_assegnato_id')
                ->select('id', 'nome', 'modello', 'categoria', 'created_at', 'attivo')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($prodotto) {
                    return [
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'modello' => $prodotto->modello,
                        'categoria' => $prodotto->categoria_label,
                        'created_at' => $prodotto->created_at->diffForHumans(),
                        'attivo' => $prodotto->attivo
                    ];
                });

            $count = Prodotto::whereNull('staff_assegnato_id')->count();

            return response()->json([
                'success' => true,
                'count' => $count,
                'prodotti' => $prodotti,
                'message' => $count > 0 ? "Trovati {$count} prodotti non assegnati" : "Tutti i prodotti sono assegnati",
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
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
     * API per stato sistema in tempo reale (AJAX)
     */
    public function systemStatus()
    {
        try {
            // Test connessione database
            $databaseStatus = 'online';
            try {
                DB::connection()->getPdo();
                DB::select('SELECT 1');
            } catch (\Exception $e) {
                $databaseStatus = 'error';
            }

            // Test storage
            $storageWritable = is_writable(storage_path());
            
            // Test cache
            $cacheStatus = 'active';
            try {
                \Cache::put('system_test', 'ok', 10);
                \Cache::get('system_test');
            } catch (\Exception $e) {
                $cacheStatus = 'error';
            }

            $status = [
                'database' => $databaseStatus,
                'storage' => $storageWritable ? 'writable' : 'read-only',
                'cache' => $cacheStatus,
                'last_check' => now()->toISOString(),
                'uptime' => $this->getSystemUptime()
            ];

            // Determina stato generale
            $overallStatus = 'operational';
            if ($status['database'] === 'error' || $status['cache'] === 'error') {
                $overallStatus = 'error';
            } elseif ($status['storage'] === 'read-only') {
                $overallStatus = 'degraded';
            }

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
    // METODI HELPER PRIVATI
    // ================================================

    /**
     * Ottiene la versione del database
     */
    private function getDatabaseVersion(): string
    {
        try {
            $version = DB::select('SELECT VERSION() as version')[0]->version;
            return $version;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Calcola l'utilizzo dello storage
     */
    private function getStorageUsage(): array
    {
        try {
            $storagePath = storage_path();
            $totalSpace = disk_total_space($storagePath);
            $freeSpace = disk_free_space($storagePath);
            $usedSpace = $totalSpace - $freeSpace;

            return [
                'total' => $this->formatBytes($totalSpace),
                'used' => $this->formatBytes($usedSpace),
                'free' => $this->formatBytes($freeSpace),
                'percentage' => round(($usedSpace / $totalSpace) * 100, 2)
            ];
        } catch (\Exception $e) {
            return ['error' => 'Impossibile calcolare utilizzo storage'];
        }
    }

    /**
     * Ottiene informazioni sui file di log
     */
    private function getLogFiles(): array
    {
        try {
            $logPath = storage_path('logs');
            $files = [];

            if (is_dir($logPath)) {
                $logFiles = glob($logPath . '/*.log');
                foreach ($logFiles as $file) {
                    $files[] = [
                        'name' => basename($file),
                        'size' => $this->formatBytes(filesize($file)),
                        'modified' => date('d/m/Y H:i', filemtime($file))
                    ];
                }
            }

            return $files;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Verifica se una cache è abilitata
     */
    private function isCacheEnabled(string $type): bool
    {
        try {
            switch ($type) {
                case 'config':
                   return file_exists(base_path('bootstrap/cache/config.php'));
                case 'route':
                    return file_exists(base_path('bootstrap/cache/routes-v7.php'));
                case 'view':
                    return count(glob(storage_path('framework/views/*.php'))) > 0;
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ottiene informazioni di uptime del sistema
     */
    private function getSystemUptime(): string
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

    /**
     * Formatta i bytes in formato leggibile
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Esporta dati in formato CSV
     */
    private function exportToCsv($data, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]->toArray()));
                
                // Dati
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}