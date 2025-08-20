<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdottoController;
use App\Http\Controllers\MalfunzionamentoController;
use App\Http\Controllers\SoluzioneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CentroAssistenzaController;

/*
|--------------------------------------------------------------------------
| Web Routes per il Sistema di Assistenza Tecnica
|--------------------------------------------------------------------------
|
| Qui sono definite tutte le route web per l'applicazione.
| Le route sono organizzate per livello di accesso e funzionalità.
| 
| IMPORTANTE: Le route API devono essere definite PRIMA delle route generiche
| per evitare conflitti di routing.
|
*/

// ===================================================
// ROUTE API PER CHIAMATE AJAX - PRIORITÀ MASSIMA
// ===================================================

Route::prefix('api')->name('api.')->middleware(['web'])->group(function () {
    
    // === API TECNICI (Livello 2+) ===
    Route::middleware(['auth', 'check.level:2'])->group(function () {
        
        // Statistiche tecnico per dashboard
        Route::get('/tecnico/stats', [AuthController::class, 'tecnicoStats'])->name('tecnico.stats');
        
        // Malfunzionamenti per tecnici
        Route::get('/malfunzionamenti', [MalfunzionamentoController::class, 'apiIndex'])->name('malfunzionamenti.api');
        Route::get('/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'apiShow'])->name('malfunzionamenti.api.show');
        
        // Segnalazione malfunzionamento
        Route::post('/malfunzionamenti/{malfunzionamento}/segnala', [MalfunzionamentoController::class, 'apiSegnala'])->name('malfunzionamenti.segnala');
    });
    
    // === API STAFF (Livello 3+) ===
    Route::middleware(['auth', 'check.level:3'])->group(function () {
        
        // Statistiche staff per AJAX
        Route::get('/staff/stats', [AuthController::class, 'staffStats'])->name('staff.stats');
        
        // Prodotti assegnati allo staff
        Route::get('/staff/prodotti', [AuthController::class, 'staffProdotti'])->name('staff.prodotti');

        // Ultime soluzioni create dallo staff
        Route::get('/staff/soluzioni', [AuthController::class, 'staffUltimeSoluzioni'])->name('staff.soluzioni');
    });
    
    // === API AMMINISTRATIVE (Livello 4) ===
    Route::middleware(['auth', 'check.level:4'])->group(function () {

        Route::prefix('admin')->name('admin.')->group(function () {
            
            // === GESTIONE TECNICI VIA API ===
            
            /**
             * API per ottenere TUTTI i tecnici disponibili nel sistema
             * ROUTE: GET /api/admin/tecnici-disponibili
             * CONTROLLER: CentroAssistenzaController@getTecniciDisponibili
             * USATA DA: JavaScript globale per gestione tecnici
             */
            Route::get('/tecnici-disponibili', [CentroAssistenzaController::class, 'getTecniciDisponibili'])
                ->name('tecnici.disponibili');
            
            /**
             * API per ottenere dettagli di un tecnico specifico
             * ROUTE: GET /api/admin/tecnici/{user}
             * CONTROLLER: CentroAssistenzaController@getDettagliTecnico
             * USATA DA: Modal dettagli tecnico, tooltip, ecc.
             */
            Route::get('/tecnici/{user}', [CentroAssistenzaController::class, 'getDettagliTecnico'])
                ->name('tecnici.dettagli');
            
            // === GESTIONE CENTRI VIA API ===
            
            /**
             * API per ottenere tecnici disponibili per UN CENTRO SPECIFICO
             * ROUTE: GET /api/admin/centri/{centro}/tecnici-disponibili
             * CONTROLLER: CentroAssistenzaController@getAvailableTecnici
             * USATA DA: Modal assegnazione tecnico nel centro specifico
             * QUESTA È LA ROUTE CHE IL TUO JAVASCRIPT STA CHIAMANDO!
             */
            Route::get('/centri/{centro}/tecnici-disponibili', [CentroAssistenzaController::class, 'getAvailableTecnici'])
                ->name('centri.tecnici.disponibili');
            
            /**
             * API per ottenere statistiche di un centro
             * ROUTE: GET /api/admin/centri/{centro}/statistiche
             * CONTROLLER: CentroAssistenzaController@getStatisticheCentro
             * USATA DA: Dashboard centro, grafici, metriche real-time
             */
            Route::get('/centri/{centro}/statistiche', [CentroAssistenzaController::class, 'getStatisticheCentro'])
                ->name('centri.statistiche');
            
            /**
             * API per ottenere dettagli completi di un centro
             * ROUTE: GET /api/admin/centri/{centro}/dettagli
             * CONTROLLER: CentroAssistenzaController@getCentroDetails
             * USATA DA: Modal info centro, aggiornamenti dinamici
             */
            Route::get('/centri/{centro}/dettagli', [CentroAssistenzaController::class, 'getCentroDetails'])
                ->name('centri.dettagli');
            
            // === STATISTICHE GENERALI ===
            
            /**
             * API per statistiche dashboard admin
             * ROUTE: GET /api/admin/statistiche
             * CONTROLLER: CentroAssistenzaController@getStatistiche
             * USATA DA: Dashboard admin principale
             */
            Route::get('/statistiche', [CentroAssistenzaController::class, 'getStatistiche'])
                ->name('statistiche');
            
            // === API GENERALI ADMIN ===
            
            /**
             * Statistiche admin per dashboard
             */
            Route::get('/admin/stats', [AuthController::class, 'adminStats'])->name('admin.stats');
            
            /**
             * Attività recenti per admin
             */
            Route::get('/admin/attivita', [AuthController::class, 'adminAttivita'])->name('admin.attivita');
        });
    });
});

// ===================================================
// ROUTE DI DEBUG E TEST (RIMUOVI IN PRODUZIONE)
// ===================================================

/**
 * Route di test per verificare che l'API funzioni
 * TEMPORANEA - Rimuovi dopo aver risolto i problemi
 */
Route::get('/test-api-centro/{centro}', function(\App\Models\CentroAssistenza $centro) {
    return response()->json([
        'success' => true,
        'message' => 'Route API di test funziona!',
        'centro' => [
            'id' => $centro->id,
            'nome' => $centro->nome
        ],
        'user' => [
            'id' => auth()->id(),
            'nome' => auth()->user()->nome_completo ?? 'N/A',
            'livello' => auth()->user()->livello_accesso ?? 'non_autenticato',
            'is_admin' => auth()->check() ? auth()->user()->isAdmin() : false
        ],
        'timestamp' => now()->toISOString()
    ]);
})->middleware(['auth']);

/**
 * Route di test per verificare autenticazione admin
 */
Route::get('/test-admin', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'user_level' => auth()->user()->livello_accesso ?? null,
        'is_admin' => auth()->check() ? auth()->user()->isAdmin() : false,
        'csrf_token' => csrf_token()
    ]);
})->middleware(['auth']);

// ===================================================
// ROUTE PUBBLICHE (Livello 1 - Tutti gli utenti)
// ===================================================

/**
 * Home Page e Informazioni Generali
 */
Route::get('/', function () {
    return view('welcome');
})->name('home');

/**
 * Pagina About - Informazioni sull'azienda
 */
Route::get('/about', function () {
    return view('about');
})->name('about');

/**
 * Centri di Assistenza - Visualizzazione Pubblica
 * Accessibile a tutti gli utenti (anche non autenticati)
 */
Route::get('/centri', [CentroAssistenzaController::class, 'index'])->name('centri.index');
Route::get('/centri/{centro}', [CentroAssistenzaController::class, 'show'])->name('centri.show');

/**
 * Prodotti - Catalogo Pubblico (senza malfunzionamenti)
 * Accessibile a tutti per visualizzare il catalogo base
 */
Route::get('/prodotti', [ProdottoController::class, 'index'])->name('prodotti.index');
Route::get('/prodotti/{prodotto}', [ProdottoController::class, 'show'])->name('prodotti.show');

// ===================================================
// AUTENTICAZIONE
// ===================================================

/**
 * Route per login/logout
 */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================================
// ROUTE PROTETTE (Solo utenti autenticati)
// ===================================================

Route::middleware(['auth'])->group(function () {

    /**
     * Dashboard principale - reindirizza in base al livello utente
     */
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // ===================================================
    // ROUTE TECNICI (Livello 2+)
    // ===================================================

    Route::middleware(['check.level:2'])->group(function () {
        
        /**
         * Dashboard Tecnico
         */
        Route::get('/tecnico/dashboard', [AuthController::class, 'tecnicoDashboard'])->name('tecnico.dashboard');
        
        /**
         * Malfunzionamenti - Accesso completo per tecnici
         * I tecnici possono vedere tutti i malfunzionamenti e soluzioni
         */
        Route::prefix('malfunzionamenti')->name('malfunzionamenti.')->group(function () {
            Route::get('/', [MalfunzionamentoController::class, 'index'])->name('index');
            Route::get('/{malfunzionamento}', [MalfunzionamentoController::class, 'show'])->name('show');
            Route::get('/prodotto/{prodotto}', [MalfunzionamentoController::class, 'byProdotto'])->name('by-prodotto');
        });
        
        /**
         * Ricerca avanzata malfunzionamenti
         */
        Route::get('/ricerca-malfunzionamenti', [MalfunzionamentoController::class, 'ricerca'])->name('malfunzionamenti.ricerca');
    });

    // ===================================================
    // ROUTE STAFF (Livello 3+)
    // ===================================================

    Route::middleware(['check.level:3'])->group(function () {
        
        /**
         * Dashboard Staff
         */
        Route::get('/staff/dashboard', [AuthController::class, 'staffDashboard'])->name('staff.dashboard');
        
        /**
         * Gestione Malfunzionamenti - CRUD per Staff
         * Lo staff può creare, modificare ed eliminare malfunzionamenti
         */
        Route::prefix('staff')->name('staff.')->group(function () {
            
            // Malfunzionamenti
            Route::resource('malfunzionamenti', MalfunzionamentoController::class, [
                'except' => ['index', 'show'] // index e show sono già pubblici
            ]);
            
            // Soluzioni
            Route::resource('soluzioni', SoluzioneController::class);
            
            // Dashboard staff specifica
            Route::get('/dashboard', [AuthController::class, 'staffDashboard'])->name('dashboard');
        });
        
        /**
         * Prodotti assegnati allo staff (se implementi funzionalità opzionale)
         */
        Route::get('/miei-prodotti', [ProdottoController::class, 'mieiProdotti'])->name('staff.miei-prodotti');
    });

    // ===================================================
    // ROUTE AMMINISTRATORI (Livello 4)
    // ===================================================

    Route::middleware(['check.level:4'])->prefix('admin')->name('admin.')->group(function () {
        
        /**
         * Dashboard Amministratore
         */
        Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('dashboard');
        
        // === GESTIONE UTENTI ===
        
        /**
         * CRUD completo utenti
         * Solo gli admin possono gestire tutti gli utenti del sistema
         */
        Route::resource('users', UserController::class, [
            'names' => [
                'index' => 'users.index',
                'create' => 'users.create',
                'store' => 'users.store',
                'show' => 'users.show',
                'edit' => 'users.edit',
                'update' => 'users.update',
                'destroy' => 'users.destroy'
            ]
        ]);
        
        // === GESTIONE PRODOTTI ===
        
        /**
         * CRUD completo prodotti
         * Solo gli admin possono gestire il catalogo prodotti
         */
        Route::resource('prodotti', ProdottoController::class, [
            'names' => [
                'index' => 'prodotti.index',
                'create' => 'prodotti.create',
                'store' => 'prodotti.store',
                'show' => 'prodotti.show',
                'edit' => 'prodotti.edit',
                'update' => 'prodotti.update',
                'destroy' => 'prodotti.destroy'
            ]
        ]);
        
        // === GESTIONE CENTRI DI ASSISTENZA ===
        
        /**
         * CRUD completo centri di assistenza
         * Solo gli admin possono gestire i centri
         */
        Route::resource('centri', CentroAssistenzaController::class, [
            'names' => [
                'index' => 'centri.index',
                'create' => 'centri.create',
                'store' => 'centri.store',
                'show' => 'centri.show',
                'edit' => 'centri.edit',
                'update' => 'centri.update',
                'destroy' => 'centri.destroy'
            ]
        ]);
        
        // === GESTIONE TECNICI NEI CENTRI ===
        
        /**
         * POST: Assegna tecnico a centro
         * ROUTE: POST /admin/centri/{centro}/assegna-tecnico
         * CONTROLLER: CentroAssistenzaController@assegnaTecnico
         * FORM ACTION: Modal assegnazione tecnico
         */
        Route::post('/centri/{centro}/assegna-tecnico', [CentroAssistenzaController::class, 'assegnaTecnico'])
            ->name('centri.assegna-tecnico');
        
        /**
         * DELETE: Rimuovi tecnico da centro
         * ROUTE: DELETE /admin/centri/{centro}/rimuovi-tecnico
         * CONTROLLER: CentroAssistenzaController@rimuoviTecnico
         * FORM ACTION: Rimozione tecnico via AJAX o form
         */
        Route::delete('/centri/{centro}/rimuovi-tecnico', [CentroAssistenzaController::class, 'rimuoviTecnico'])
            ->name('centri.rimuovi-tecnico');
        
        // === IMPORT/EXPORT CENTRI ===
        
        /**
         * GET: Export centri (CSV/Excel)
         * ROUTE: GET /admin/centri-export
         * CONTROLLER: CentroAssistenzaController@export
         */
        Route::get('/centri-export', [CentroAssistenzaController::class, 'export'])
            ->name('centri.export');
        
        /**
         * POST: Import centri da file
         * ROUTE: POST /admin/centri-import
         * CONTROLLER: CentroAssistenzaController@import
         */
        Route::post('/centri-import', [CentroAssistenzaController::class, 'import'])
            ->name('centri.import');
        
        // === FUNZIONALITÀ OPZIONALI ===
        
        /**
         * Assegnazione prodotti a membri dello staff (funzionalità opzionale)
         */
        Route::get('/assegnazioni', [ProdottoController::class, 'assegnazioni'])->name('assegnazioni');
        Route::post('/assegna-prodotti', [ProdottoController::class, 'assegnaProdotti'])->name('assegna-prodotti');
        Route::delete('/rimuovi-assegnazione/{prodotto}/{user}', [ProdottoController::class, 'rimuoviAssegnazione'])
            ->name('rimuovi-assegnazione');
        
        // === DASHBOARD E STATISTICHE ===
        
        /**
         * Dashboard amministrativa avanzata
         */
        Route::get('/statistiche-avanzate', [AuthController::class, 'statisticheAvanzate'])->name('statistiche.avanzate');
        
        /**
         * Log attività sistema
         */
        Route::get('/log-attivita', [AuthController::class, 'logAttivita'])->name('log.attivita');
        
        /**
         * Configurazioni sistema
         */
        Route::get('/configurazioni', [AuthController::class, 'configurazioni'])->name('configurazioni');
    });
});

// ===================================================
// ROUTE FALLBACK E GESTIONE ERRORI
// ===================================================

/**
 * Route fallback per pagine non trovate
 * Deve essere l'ULTIMA route definita per non interferire con le altre
 */
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

/*
|--------------------------------------------------------------------------
| RIEPILOGO ROUTE CENTRI ASSISTENZA
|--------------------------------------------------------------------------
|
| ROUTE PUBBLICHE:
| GET  /centri                              -> centri.index (pubblico)
| GET  /centri/{centro}                     -> centri.show (pubblico)
|
| ROUTE ADMIN:
| GET    /admin/centri                      -> admin.centri.index
| GET    /admin/centri/create               -> admin.centri.create
| POST   /admin/centri                      -> admin.centri.store
| GET    /admin/centri/{centro}             -> admin.centri.show
| GET    /admin/centri/{centro}/edit        -> admin.centri.edit
| PUT    /admin/centri/{centro}             -> admin.centri.update
| DELETE /admin/centri/{centro}             -> admin.centri.destroy
|
| GESTIONE TECNICI:
| POST   /admin/centri/{centro}/assegna-tecnico    -> admin.centri.assegna-tecnico
| DELETE /admin/centri/{centro}/rimuovi-tecnico    -> admin.centri.rimuovi-tecnico
|
| API ENDPOINTS CRITICI:
| GET /api/admin/centri/{centro}/tecnici-disponibili   -> api.admin.centri.tecnici.disponibili (QUESTA!)
| GET /api/admin/tecnici-disponibili                   -> api.admin.tecnici.disponibili
| GET /api/admin/tecnici/{user}                        -> api.admin.tecnici.dettagli
| GET /api/admin/centri/{centro}/statistiche           -> api.admin.centri.statistiche
| GET /api/admin/centri/{centro}/dettagli              -> api.admin.centri.dettagli
| GET /api/admin/statistiche                           -> api.admin.statistiche
|
| ROUTE DI TEST:
| GET /test-api-centro/{centro}             -> Test API funzionalità
| GET /test-admin                           -> Test autenticazione admin
|
| IMPORT/EXPORT:
| GET  /admin/centri-export                 -> admin.centri.export
| POST /admin/centri-import                 -> admin.centri.import
|
|--------------------------------------------------------------------------
| COMANDI UTILI:
|--------------------------------------------------------------------------
|
| Visualizza tutte le route:
| php artisan route:list
|
| Visualizza solo route API centri:
| php artisan route:list | grep -E "api.*centri.*tecnici"
|
| Visualizza route specifiche:
| php artisan route:list --name=admin.centri
|
| Pulisci cache route:
| php artisan route:clear
|
| Cache delle route (produzione):
| php artisan route:cache
|
| Test route API:
| curl -H "Accept: application/json" -H "X-Requested-With: XMLHttpRequest" \
|      https://tweban.dii.univpm.it/~grp_51/laraProject/public/api/admin/centri/1/tecnici-disponibili
|
*/