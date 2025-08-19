<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdottoController;
use App\Http\Controllers\MalfunzionamentoController;
use App\Http\Controllers\CentroAssistenzaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Assistenza Tecnica
|--------------------------------------------------------------------------
| Route organizzate per livelli di accesso secondo le specifiche del progetto
| VERSIONE CORRETTA - Fix errori 404 API
*/

// ================================================
// ROUTE PUBBLICHE (Livello 1 - Accesso Libero)
// ================================================

// Homepage e informazioni aziendali
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/azienda', [HomeController::class, 'azienda'])->name('azienda');
Route::get('/contatti', [HomeController::class, 'contatti'])->name('contatti');
Route::post('/contatti/invia', [HomeController::class, 'inviaContatto'])->name('contatti.invia');

// Catalogo prodotti PUBBLICO (senza malfunzionamenti)
Route::get('/prodotti', [ProdottoController::class, 'indexPubblico'])->name('prodotti.index');
Route::get('/prodotti/{prodotto}', [ProdottoController::class, 'showPubblico'])->name('prodotti.show');
Route::get('/prodotti/categoria/{categoria}', [ProdottoController::class, 'categoria'])->name('prodotti.categoria');

// Ricerca prodotti pubblica
Route::get('/prodotti/search', [ProdottoController::class, 'search'])->name('prodotti.search');

// Centri di assistenza (informazioni pubbliche)
Route::get('/centri-assistenza', [CentroAssistenzaController::class, 'index'])->name('centri.index');
Route::get('/centri-assistenza/{centro}', [CentroAssistenzaController::class, 'show'])->name('centri.show');

// Documentazione
Route::get('/documentazione', function () {
    $documentPath = public_path('docs/documentazione_progetto.pdf');
    
    if (file_exists($documentPath)) {
        return response()->file($documentPath);
    } else {
        return view('documentazione.placeholder');
    }
})->name('documentazione');

// ================================================
// AUTENTICAZIONE
// ================================================

// Login e Logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================================================
// API ROUTES (SPOSTATI FUORI DAL MIDDLEWARE GENERALE)
// ================================================

Route::prefix('api')->name('api.')->group(function () {
    
    // === API PUBBLICHE (senza autenticazione) ===
    
    // Ricerca prodotti pubblica
    Route::get('/prodotti/search', [ProdottoController::class, 'apiSearch'])->name('prodotti.search');
    
    // Lista prodotti pubblica (per AJAX)
    Route::get('/prodotti', [ProdottoController::class, 'apiIndex'])->name('prodotti.index');
    
    // Dettagli prodotto singolo (per AJAX)
    Route::get('/prodotti/{prodotto}', [ProdottoController::class, 'apiShow'])->name('prodotti.show');
    
    // Informazioni centri assistenza pubbliche
    Route::get('/centri', [CentroAssistenzaController::class, 'apiIndex'])->name('centri.index');
    Route::get('/centri/search', [CentroAssistenzaController::class, 'apiSearch'])->name('centri.search');
    Route::get('/centri/citta-per-provincia', [CentroAssistenzaController::class, 'apiCittaPerProvincia'])->name('centri.citta-per-provincia');
    
    // Categorie prodotti pubbliche
    Route::get('/categorie', [ProdottoController::class, 'apiCategorie'])->name('categorie.index');
    
    // === API PER UTENTI AUTENTICATI ===
    
    Route::middleware(['auth'])->group(function () {
        
        // Statistiche per dashboard generale
        Route::get('/stats/dashboard', [HomeController::class, 'dashboardStats'])->name('stats.dashboard');
        
        // === API TECNICI (Livello 2+) ===
        Route::middleware(['check.level:2'])->group(function () {
            
            // Ricerca malfunzionamenti (per tecnici)
            Route::get('/malfunzionamenti/search', [MalfunzionamentoController::class, 'apiSearch'])->name('malfunzionamenti.search');
            
            // Malfunzionamenti per prodotto specifico
            Route::get('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'apiByProdotto'])->name('prodotti.malfunzionamenti');
            
            // Prodotti con vista tecnica completa
            Route::get('/prodotti/tech/search', [ProdottoController::class, 'apiSearchTech'])->name('prodotti.search.tech');
            
            // Segnalazione malfunzionamento
            Route::post('/malfunzionamenti/{malfunzionamento}/segnala', [MalfunzionamentoController::class, 'apiSegnala'])->name('malfunzionamenti.segnala');
        });
        
        // === API STAFF (Livello 3+) ===
        Route::middleware(['check.level:3'])->group(function () {
            
            // Statistiche staff per AJAX
            Route::get('/staff/stats', [AuthController::class, 'staffStats'])->name('staff.stats');
            
            // Prodotti assegnati allo staff
            Route::get('/staff/prodotti', [AuthController::class, 'staffProdotti'])->name('staff.prodotti');

            // Ultime soluzioni create dallo staff
            Route::get('/staff/soluzioni', [AuthController::class, 'staffUltimeSoluzioni'])->name('staff.soluzioni');
        });
        
        // === API AMMINISTRATIVE (Livello 4) - VERSIONE CORRETTA ===
        Route::middleware(['check.level:4'])->group(function () {

            // Gruppo admin con prefisso corretto
            Route::prefix('admin')->name('admin.')->group(function () {
                
                // === GESTIONE CENTRI ASSISTENZA VIA API ===
                
                /**
                 * API per ottenere tecnici disponibili per assegnazione
                 * ROUTE: GET /api/admin/tecnici-disponibili
                 * CONTROLLER: CentroAssistenzaController@getTecniciDisponibili
                 */
                Route::get('/tecnici-disponibili', [CentroAssistenzaController::class, 'getTecniciDisponibili'])
                    ->name('tecnici.disponibili');
                
                /**
                 * API per ottenere dettagli di un tecnico specifico
                 * ROUTE: GET /api/admin/tecnici/{user}
                 * CONTROLLER: CentroAssistenzaController@getDettagliTecnico
                 */
                Route::get('/tecnici/{user}', [CentroAssistenzaController::class, 'getDettagliTecnico'])
                    ->name('tecnici.dettagli');
                
                /**
                 * API per ottenere statistiche di un centro
                 * ROUTE: GET /api/admin/centri/{centro}/statistiche
                 * CONTROLLER: CentroAssistenzaController@getStatisticheCentro
                 */
                Route::get('/centri/{centro}/statistiche', [CentroAssistenzaController::class, 'getStatisticheCentro'])
                    ->name('centri.statistiche');
                
                // === METODI ADMIN CONTROLLER CHE DEVI IMPLEMENTARE ===
                
                /**
                 * API per aggiornamento statistiche dashboard admin via AJAX
                 */
                Route::get('/stats-update', [AdminController::class, 'statsUpdate'])
                    ->name('stats.update');
                
                /**
                 * API per controllo stato sistema via AJAX
                 */
                Route::get('/system-status', [AdminController::class, 'systemStatus'])
                    ->name('system.status');
                    
                /**
                 * API per prodotti non assegnati a staff
                 */
                Route::get('/prodotti-non-assegnati', [AdminController::class, 'prodottiNonAssegnati'])
                    ->name('prodotti.non-assegnati');
            });
        });
    });
});

// ================================================
// ROUTE PROTETTE PER UTENTI AUTENTICATI (WEB)
// ================================================

Route::middleware(['auth'])->group(function () {
    
    // Dashboard generale
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // ================================================
    // LIVELLO 2+ (Tecnici e Superiori)
    // ================================================
    
    Route::middleware(['check.level:2'])->group(function () {
        
        // Dashboard tecnico
        Route::get('/tecnico/dashboard', [AuthController::class, 'tecnicoDashboard'])->name('tecnico.dashboard');
        
        // Catalogo prodotti COMPLETO (con malfunzionamenti)
        Route::get('/prodotti-completi', [ProdottoController::class, 'indexCompleto'])->name('prodotti.completo.index');
        Route::get('/prodotti-completi/{prodotto}', [ProdottoController::class, 'showCompleto'])->name('prodotti.completo.show');
        
        // === MALFUNZIONAMENTI PER PRODOTTO SPECIFICO ===
        
        // Lista malfunzionamenti per un prodotto
        Route::get('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'index'])->name('malfunzionamenti.index');
        
        // Dettaglio singolo malfunzionamento 
        Route::get('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'show'])->name('malfunzionamenti.show');
        
        // Incrementa segnalazioni (per tecnici)
        Route::post('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}/segnala', [MalfunzionamentoController::class, 'incrementSegnalazioni'])->name('malfunzionamenti.segnala');
        
        // Ricerca malfunzionamenti
        Route::get('/malfunzionamenti/search', [MalfunzionamentoController::class, 'search'])->name('malfunzionamenti.search');
    });
    
    // ================================================
    // LIVELLO 3+ (Staff Aziendale e Amministratori)
    // ================================================

    Route::middleware(['check.level:3'])->group(function () {

        // === STAFF ROUTES COMPLETE (Livello 3) ===
        Route::prefix('staff')->name('staff.')->group(function () {
            
            // Dashboard staff
            Route::get('/dashboard', [AuthController::class, 'staffDashboard'])->name('dashboard');
            
            // Dashboard/Lista generale malfunzionamenti per staff
            Route::get('/malfunzionamenti', [MalfunzionamentoController::class, 'dashboard'])->name('malfunzionamenti.index');
            
            // Statistiche staff
            Route::get('/statistiche', [StaffController::class, 'statistiche'])->name('statistiche');
            
            // === GESTIONE MALFUNZIONAMENTI PER PRODOTTO SPECIFICO ===
            
            // Crea nuovo malfunzionamento per un prodotto
            Route::get('/prodotti/{prodotto}/malfunzionamenti/create', [MalfunzionamentoController::class, 'create'])->name('malfunzionamenti.create');
            
            // Salva nuovo malfunzionamento
            Route::post('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'store'])->name('malfunzionamenti.store');
            
            // Modifica malfunzionamento esistente
            Route::get('/malfunzionamenti/{malfunzionamento}/edit', [MalfunzionamentoController::class, 'edit'])->name('malfunzionamenti.edit');
            
            // Aggiorna malfunzionamento
            Route::put('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'update'])->name('malfunzionamenti.update');

            // Elimina malfunzionamento
            Route::delete('/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'destroy'])->name('malfunzionamenti.destroy');
            
            // Visualizza dettagli malfunzionamento per staff
            Route::get('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'show'])->name('malfunzionamenti.show');
        });
    });
    
    // ================================================
    // LIVELLO 4 (Solo Amministratori)
    // ================================================
    
    Route::middleware(['check.level:4'])->group(function () {
        
        // Dashboard amministratore
        Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // === GESTIONE PRODOTTI (Admin) ===
        Route::prefix('admin/prodotti')->name('admin.prodotti.')->group(function () {
            Route::get('/', [ProdottoController::class, 'index'])->name('index');
            Route::get('/create', [ProdottoController::class, 'create'])->name('create');
            Route::post('/', [ProdottoController::class, 'store'])->name('store');
            Route::get('/{prodotto}', [ProdottoController::class, 'show'])->name('show');
            Route::get('/{prodotto}/edit', [ProdottoController::class, 'edit'])->name('edit');
            Route::put('/{prodotto}', [ProdottoController::class, 'update'])->name('update');
            Route::delete('/{prodotto}', [ProdottoController::class, 'destroy'])->name('destroy');
            
            // Azioni speciali per prodotti
            Route::post('/{prodotto}/restore', [ProdottoController::class, 'restore'])->name('restore');
            Route::delete('/{prodotto}/force-delete', [ProdottoController::class, 'forceDelete'])->name('force-delete');

            // Azioni AJAX
            Route::post('/{prodotto}/toggle-status', [ProdottoController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-action', [ProdottoController::class, 'bulkAction'])->name('bulk-action');
        });
        
        // === GESTIONE UTENTI ===
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            
            // Azioni speciali per utenti
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });
        
        // === REGISTRAZIONE UTENTI (Admin) ===
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        
        // === GESTIONE CENTRI ASSISTENZA (Funzionalità Opzionale) ===
        Route::prefix('admin/centri')->name('admin.centri.')->group(function () {
            Route::get('/', [CentroAssistenzaController::class, 'adminIndex'])->name('index');
            Route::get('/create', [CentroAssistenzaController::class, 'create'])->name('create');
            Route::post('/', [CentroAssistenzaController::class, 'store'])->name('store');
            Route::get('/{centro}/edit', [CentroAssistenzaController::class, 'edit'])->name('edit');
            Route::put('/{centro}', [CentroAssistenzaController::class, 'update'])->name('update');
            Route::delete('/{centro}', [CentroAssistenzaController::class, 'destroy'])->name('destroy');
            Route::get('/{centro}', [CentroAssistenzaController::class, 'show'])->name('show');
            
            // === GESTIONE TECNICI NEI CENTRI (VIA WEB, NON API) ===
            // IMPORTANTE: Queste sono le route che il JavaScript deve chiamare
            Route::post('/{centro}/assegna-tecnico', [CentroAssistenzaController::class, 'assegnaTecnico'])->name('assegna-tecnico');
            Route::post('/{centro}/rimuovi-tecnico', [CentroAssistenzaController::class, 'rimuoviTecnico'])->name('rimuovi-tecnico');
        });
        
        // === FUNZIONALITÀ AMMINISTRATIVE AVANZATE (AdminController) ===
        Route::prefix('admin')->name('admin.')->group(function () {
            
            // === ASSEGNAZIONI PRODOTTI A STAFF ===
            Route::get('/assegnazioni', [AdminController::class, 'assegnazioni'])->name('assegnazioni');
            Route::post('/assegna-prodotto', [AdminController::class, 'assegnaProdotto'])->name('assegna.prodotto');
            Route::post('/assegnazione-multipla', [AdminController::class, 'assegnazioneMultipla'])->name('assegnazione.multipla');
            
            // === STATISTICHE AVANZATE ===
            Route::get('/statistiche', [AdminController::class, 'statistiche'])->name('statistiche');
            
            // === MANUTENZIONE SISTEMA ===
            Route::get('/manutenzione', [AdminController::class, 'manutenzione'])->name('manutenzione');
            Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear.cache');
            Route::post('/optimize-database', [AdminController::class, 'optimizeDatabase'])->name('optimize.database');
            
            // === EXPORT DATI ===
            Route::get('/export', [AdminController::class, 'export'])->name('export');
            Route::post('/export-all', [AdminController::class, 'exportAll'])->name('export.all');
        });
    });
}); // ← Chiusura corretta del middleware auth

// ================================================
// ROUTE DI TESTING (Solo Ambiente Locale)
// ================================================

if (app()->environment('local')) {
    
    Route::prefix('test')->name('test.')->group(function () {
        
        // Test connessione database
        Route::get('/db', function () {
            try {
                \DB::connection()->getPdo();
                return response()->json([
                    'status' => 'OK',
                    'message' => 'Connessione al database riuscita',
                    'database' => config('database.connections.mysql.database')
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Errore connessione DB: ' . $e->getMessage()
                ], 500);
            }
        })->name('db');
        
        // Test configurazione
        Route::get('/config', function () {
            return response()->json([
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'database' => config('database.default'),
                'session_driver' => config('session.driver'),
            ]);
        })->name('config');
        
        // Test permessi storage
        Route::get('/storage', function () {
            $storagePath = storage_path('logs/laravel.log');
            
            return response()->json([
                'storage_writable' => is_writable(storage_path()),
                'logs_writable' => is_writable(dirname($storagePath)),
                'log_file_exists' => file_exists($storagePath),
            ]);
        })->name('storage');
        
        // Visualizza log (per debugging)
        Route::get('/logs', function () {
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                $logs = file_get_contents($logFile);
                return response($logs)->header('Content-Type', 'text/plain');
            }
            
            return 'File di log non trovato';
        })->name('logs');
        
        // Test route admin per verifica funzionamento
        Route::get('/admin-routes', function () {
            $routes = collect(Route::getRoutes())->filter(function($route) {
                return str_contains($route->getName() ?? '', 'admin.');
            })->map(function($route) {
                return [
                    'name' => $route->getName(),
                    'uri' => $route->uri(),
                    'methods' => $route->methods(),
                    'action' => $route->getActionName()
                ];
            });
            
            return response()->json([
                'admin_routes_count' => $routes->count(),
                'routes' => $routes->values()
            ]);
        })->name('admin-routes');
        
        // Test con middleware applicato
        Route::get('/middleware-test', function () {
            return response()->json([
                'success' => 'Middleware check.level:2 funziona!',
                'user' => Auth::check() ? Auth::user()->nome_completo : 'Non autenticato'
            ]);
        })->middleware(['auth', 'check.level:2'])->name('middleware-test');
        
        // === NUOVO: Test route API admin specifiche ===
        Route::get('/api-admin-test', function () {
            $routes = collect(Route::getRoutes())->filter(function($route) {
                return str_contains($route->getName() ?? '', 'api.admin.');
            })->map(function($route) {
                return [
                    'name' => $route->getName(),
                    'uri' => $route->uri(),
                    'methods' => $route->methods()
                ];
            });
            
            return response()->json([
                'api_admin_routes_count' => $routes->count(),
                'routes' => $routes->values(),
                'test_urls' => [
                    'tecnici_disponibili' => url('/api/admin/tecnici-disponibili'),
                    'statistiche_centro_1' => url('/api/admin/centri/1/statistiche'),
                    'stats_update' => url('/api/admin/stats-update'),
                    'system_status' => url('/api/admin/system-status')
                ]
            ]);
        })->name('api-admin-test');
    });
}

// ================================================
// ROUTE DI FALLBACK
// ================================================

// Gestione 404 personalizzata
Route::fallback(function () {
    return view('errors.404');
});