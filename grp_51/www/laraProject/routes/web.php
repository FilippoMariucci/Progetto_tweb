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
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Assistenza Tecnica
|--------------------------------------------------------------------------
| Route organizzate per livelli di accesso secondo le specifiche del progetto
| Livello 1: Pubblico | Livello 2: Tecnici | Livello 3: Staff | Livello 4: Admin
|
| Password SSH Gruppo 51: dNWR53F3
| Password utenti predefiniti: dNWRdNWR (primi 4 caratteri ripetuti)
*/

// =====================================================
// LIVELLO 1 - ROUTE PUBBLICHE (Accesso senza login)
// =====================================================

// === HOMEPAGE E INFORMAZIONI AZIENDALI ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/azienda', [HomeController::class, 'azienda'])->name('azienda');
Route::get('/contatti', [HomeController::class, 'contatti'])->name('contatti');
Route::post('/contatti/invia', [HomeController::class, 'inviaContatto'])->name('contatti.invia');

// === CATALOGO PRODOTTI PUBBLICO (senza malfunzionamenti) ===
// Visualizzazione lista prodotti per utenti non registrati
Route::get('/catalogo', [ProdottoController::class, 'indexPubblico'])->name('prodotti.pubblico.index');

// Dettaglio singolo prodotto (scheda tecnica senza malfunzionamenti)
Route::get('/prodotti/{prodotto}', [ProdottoController::class, 'showPubblico'])->name('prodotti.pubblico.show');

// Filtraggio prodotti per categoria
Route::get('/prodotti/categoria/{categoria}', [ProdottoController::class, 'categoria'])->name('prodotti.categoria');

// Ricerca prodotti con supporto wildcard (es. "lav*" per lavatrici, lavastoviglie)
Route::get('/ricerca-prodotti', [ProdottoController::class, 'searchPubblico'])->name('prodotti.search');

// === CENTRI DI ASSISTENZA PUBBLICI ===
// Lista di tutti i centri di assistenza sul territorio
Route::get('/centri-assistenza', [CentroAssistenzaController::class, 'index'])->name('centri.index');

// Dettaglio singolo centro assistenza
Route::get('/centri-assistenza/{centro}', [CentroAssistenzaController::class, 'show'])->name('centri.show');

// Ricerca centri per località
Route::get('/centri-assistenza/cerca/{localita?}', [CentroAssistenzaController::class, 'cercaPerLocalita'])->name('centri.cerca');

/*
 * Route per la documentazione del progetto
 * AGGIUNGERE questa route nel file routes/web.php
 */

// === DOCUMENTAZIONE PROGETTO ===
// Route pubblica per accesso alla documentazione PDF
Route::get('/documentazione', function () {
    // Path del file PDF della documentazione
    $documentPath = public_path('docs/documentazione_progetto.pdf');
    
    // Controlla se il file esiste
    if (file_exists($documentPath)) {
        // Restituisce il file PDF per la visualizzazione
        return response()->file($documentPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Documentazione_Gruppo51.pdf"'
        ]);
    } else {
        // Se il file non esiste, mostra errore 404 con messaggio personalizzato
        abort(404, 'Documentazione non trovata. Contattare l\'amministratore.');
    }
})->name('documentazione');

// =====================================================
// SISTEMA DI AUTENTICAZIONE
// =====================================================

// === LOGIN E LOGOUT ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================================================
// API ROUTES (per chiamate AJAX e JavaScript)
// =====================================================

Route::prefix('api')->name('api.')->group(function () {
    
    // === API PUBBLICHE (senza autenticazione richiesta) ===
    
    // Ricerca prodotti via AJAX con supporto wildcard
    Route::get('/prodotti/search', [ProdottoController::class, 'apiSearch'])->name('prodotti.search');
    
    // Lista prodotti per caricamento dinamico
    Route::get('/prodotti', [ProdottoController::class, 'apiIndexPubblico'])->name('prodotti.completo.index');
    
    // Dettagli prodotto singolo per modal o widget
    Route::get('/prodotti/{prodotto}', [ProdottoController::class, 'apiShowPubblico'])->name('prodotti.completo.show');
    
    // API per centri assistenza
    Route::get('/centri', [CentroAssistenzaController::class, 'apiIndex'])->name('centri.index');
    Route::get('/centri/search', [CentroAssistenzaController::class, 'apiSearch'])->name('centri.search');
    
    // Ottenere città per provincia (per form dinamici)
    Route::get('/centri/citta-per-provincia', [CentroAssistenzaController::class, 'apiCittaPerProvincia'])->name('centri.citta-provincia');
    
    // Liste categorie prodotti per filtri dinamici
    Route::get('/categorie', [ProdottoController::class, 'apiCategorie'])->name('categorie.index');
    
    // === API PER UTENTI AUTENTICATI ===
    
    Route::middleware(['auth'])->group(function () {
        
        // Statistiche generali dashboard per AJAX
        Route::get('/stats/dashboard', [HomeController::class, 'dashboardStats'])->name('stats.dashboard');
        
        // === API LIVELLO 2+ (Tecnici e superiori) ===
        Route::middleware(['check.level:2'])->group(function () {
            
             // *** QUESTE SONO LE ROUTE CHE MANCAVANO ***
            
            // Ricerca malfunzionamenti per tecnici
            Route::get('/malfunzionamenti/search', [MalfunzionamentoController::class, 'apiSearch'])
                ->name('malfunzionamenti.search');
            
            // Malfunzionamenti per prodotto specifico
            Route::get('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'apiByProdotto'])
                ->name('prodotti.malfunzionamenti');
            
            // Prodotti con vista tecnica completa (QUESTA ERA FONDAMENTALE)
            Route::get('/prodotti/tech/search', [ProdottoController::class, 'apiSearchTech'])
                ->name('prodotti.search.tech');
            
            // Lista prodotti per tecnici
            Route::get('/prodotti/tech/all', [ProdottoController::class, 'apiIndexTech'])
                ->name('prodotti.tech.all');

            
            // Segnalazione problema per incrementare contatori (QUESTA ERA CRITICA)
            Route::post('/malfunzionamenti/{malfunzionamento}/segnala', [MalfunzionamentoController::class, 'apiSegnala'])
                ->name('malfunzionamenti.segnala');
            
            // Storico interventi del tecnico
            Route::get('/tecnico/storico-interventi', [AuthController::class, 'apiStoricoInterventi'])
                ->name('tecnico.storico');
            
            // Suggerimenti per ricerca rapida
            Route::get('/prodotti/suggestions', [ProdottoController::class, 'apiSuggestions'])
                ->name('prodotti.suggestions');
        });
        
        // === API LIVELLO 3+ (Staff aziendale e amministratori) ===
        Route::middleware(['check.level:3'])->group(function () {
            
            // Statistiche staff per dashboard AJAX
        Route::get('/stats', [StaffController::class, 'apiStats'])->name('stats');
        
        // Ultime soluzioni create dallo staff
        Route::get('/ultime-soluzioni', [StaffController::class, 'apiUltimeSoluzioni'])->name('ultime-soluzioni');
        
        // Malfunzionamenti più segnalati (per priorità interventi)
        Route::get('/malfunzionamenti-prioritari', [StaffController::class, 'apiMalfunzionamentiPrioritari'])->name('malfunzionamenti-prioritari');
        
        // Prodotti assegnati allo staff corrente
        Route::get('/prodotti-assegnati', [StaffController::class, 'apiProdottiAssegnati'])->name('prodotti-assegnati');
        });
        
        // === API LIVELLO 4 (Solo amministratori) ===
        Route::middleware(['check.level:4'])->group(function () {
            
            Route::prefix('admin')->name('admin.')->group(function () {
                
                // === GESTIONE CENTRI ASSISTENZA VIA API ===
                
               // Tutti i tecnici del sistema
                Route::get('/tecnici-disponibili', [CentroAssistenzaController::class, 'getTecniciDisponibili'])
                ->name('tecnici.disponibili');
                // QUESTA ERA LA ROUTE MANCANTE!
                Route::get('/centri/{centro}/tecnici-disponibili', [CentroAssistenzaController::class, 'getAvailableTecnici'])->name('centri.tecnici.disponibili');
                
                // Dettagli tecnico specifico
                Route::get('/tecnici/{user}', [CentroAssistenzaController::class, 'apiDettagliTecnico'])->name('tecnici.dettagli');
                
                // Statistiche di un centro specifico
                Route::get('/centri/{centro}/statistiche', [CentroAssistenzaController::class, 'apiStatisticheCentro'])->name('centri.statistiche');
                
                // === STATISTICHE AMMINISTRATIVE ===
                
                /**
                 * API per aggiornamento statistiche dashboard admin (AJAX)
                 * ENDPOINT: GET /api/admin/stats-update
                 * JavaScript fa chiamata a questo endpoint ogni 5 minuti
                 */
                Route::get('/stats-update', [AdminController::class, 'statsUpdate'])
                    ->name('stats-update');
                
                /**
                 * API per controllo stato sistema (AJAX) 
                 * ENDPOINT: GET /api/admin/system-status
                 * JavaScript fa chiamata a questo endpoint ogni 3 minuti
                 */
                Route::get('/system-status', [AdminController::class, 'systemStatus'])->name('system.status');
                
                // Prodotti non ancora assegnati a membri dello staff
                Route::get('/prodotti-non-assegnati', [AdminController::class, 'apiProdottiNonAssegnati'])->name('prodotti.non-assegnati');
                
                // Utenti inattivi o con problemi
                Route::get('/utenti-problematici', [AdminController::class, 'apiUtentiProblematici'])->name('utenti.problematici');
                
                // Report utilizzo sistema
                Route::get('/report-utilizzo', [AdminController::class, 'apiReportUtilizzo'])->name('report.utilizzo');
            });
        });
    });
});

// =====================================================
// ROUTE PROTETTE PER UTENTI AUTENTICATI
// =====================================================

Route::middleware(['auth'])->group(function () {
    
    // === DASHBOARD GENERALE (tutti gli utenti autenticati) ===
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Profilo utente
    Route::get('/profilo', [AuthController::class, 'profilo'])->name('profilo');
    Route::put('/profilo/aggiorna', [AuthController::class, 'aggiornaProfilo'])->name('profilo.aggiorna');
    Route::post('/profilo/cambia-password', [AuthController::class, 'cambiaPassword'])->name('profilo.password');
    // Storico interventi per tecnici e superiori
Route::get('/storico-interventi', [AuthController::class, 'storicoInterventi'])
    ->name('auth.storico-interventi')
    ->middleware(['auth', 'check.level:2']);
    
    // =====================================================
    // LIVELLO 2+ (Tecnici e superiori)
    // =====================================================
    
    Route::middleware(['check.level:2'])->group(function () {
        
        // === DASHBOARD TECNICO ===
        Route::get('/tecnico/dashboard', [AuthController::class, 'tecnicoDashboard'])->name('tecnico.dashboard');
        // Ricerca avanzata prodotti per tecnici (questa mancava)
    Route::get('/prodotti-completi/ricerca', [ProdottoController::class, 'ricercaAvanzata'])
        ->name('prodotti.completo.ricerca');
    
    // Ricerca globale malfunzionamenti (mancava anche questa)
    Route::get('/malfunzionamenti/ricerca', [MalfunzionamentoController::class, 'ricercaGlobale'])
        ->name('malfunzionamenti.ricerca');
        
        // === CATALOGO PRODOTTI COMPLETO (con malfunzionamenti) ===
        
        // Lista prodotti con accesso ai malfunzionamenti
        Route::get('/prodotti-completi', [ProdottoController::class, 'indexCompleto'])->name('prodotti.completo.index');
        
        // Dettaglio prodotto con malfunzionamenti e soluzioni
        Route::get('/prodotti-completi/{prodotto}', [ProdottoController::class, 'showCompleto'])->name('prodotti.completo.show');
        
        // Ricerca avanzata nei prodotti e malfunzionamenti
        Route::get('/prodotti-completi/ricerca', [ProdottoController::class, 'ricercaAvanzata'])->name('prodotti.completo.ricerca');
        
        // === GESTIONE MALFUNZIONAMENTI ===
        
        // Lista malfunzionamenti per un prodotto specifico
        Route::get('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'index'])->name('malfunzionamenti.index');
        
        // Dettaglio singolo malfunzionamento con soluzione
        Route::get('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'show'])->name('malfunzionamenti.show');
        
        
        
        // Ricerca globale nei malfunzionamenti
        Route::get('/malfunzionamenti/ricerca', [MalfunzionamentoController::class, 'ricercaGlobale'])->name('malfunzionamenti.ricerca');
        
        // === STORICO INTERVENTI TECNICO ===
        Route::get('/tecnico/interventi', [AuthController::class, 'storicoInterventi'])->name('tecnico.interventi');
        Route::get('/tecnico/statistiche', [AuthController::class, 'statisticheTecnico'])->name('tecnico.statistiche');
        Route::get('/tecnico/le-mie-statistiche', [AuthController::class, 'statisticheTecnicoView'])->name('tecnico.statistiche.view');
        Route::get('/malfunzionamenti/ricerca', [MalfunzionamentoController::class, 'ricerca'])->name('malfunzionamenti.ricerca');
    });
    
    // =====================================================
    // LIVELLO 3+ (Staff aziendale e amministratori)
    // =====================================================
    
    Route::middleware(['check.level:3'])->group(function () {
        
        Route::prefix('staff')->name('staff.')->group(function () {
            
            // === DASHBOARD STAFF ===
            Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');

            // === GESTIONE MALFUNZIONAMENTI (CRUD completo) ===
            
            // Dashboard generale malfunzionamenti per staff
            Route::get('/malfunzionamenti', [MalfunzionamentoController::class, 'dashboard']) ->name('malfunzionamenti.index'); // <- attenzione al suffisso "index"
            
            // Creazione nuovo malfunzionamento per prodotto specifico
            Route::get('/prodotti/{prodotto}/malfunzionamenti/create', [MalfunzionamentoController::class, 'create'])->name('malfunzionamenti.create');
            
            // Salvataggio nuovo malfunzionamento
            Route::post('/prodotti/{prodotto}/malfunzionamenti', [MalfunzionamentoController::class, 'store'])->name('malfunzionamenti.store');
            
            // Modifica malfunzionamento esistente
            Route::get('/malfunzionamenti/{malfunzionamento}/edit', [MalfunzionamentoController::class, 'edit'])->name('malfunzionamenti.edit');
            
            // Aggiornamento malfunzionamento
            Route::put('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'update'])->name('malfunzionamenti.update');

       Route::delete('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'destroy'])
    ->name('malfunzionamenti.destroy');
            
            // Visualizzazione dettagliata per staff (con opzioni di modifica)
            Route::get('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', [MalfunzionamentoController::class, 'show'])->name('malfunzionamenti.show');

     /**
     * Route per creare una nuova soluzione dalla dashboard
     * Permette di selezionare il prodotto dal dropdown invece di averlo predefinito
     */
    Route::get('/nuova-soluzione', [StaffController::class, 'createNuovaSoluzione'])->name('create.nuova.soluzione');
    
    /**
     * Route per salvare la nuova soluzione creata dalla dashboard
     * Gestisce il form con la selezione del prodotto
     */
    Route::post('/nuova-soluzione', [StaffController::class, 'storeNuovaSoluzione'])->name('store.nuova.soluzione');
            
            // === GESTIONE PRODOTTI ASSEGNATI (Funzionalità Opzionale) ===
            
            // Solo i prodotti assegnati allo staff corrente
            Route::get('/prodotti-assegnati', [StaffController::class, 'ProdottiAssegnati'])->name('prodotti.assegnati');

            // === STATISTICHE E REPORT STAFF ===
            Route::get('/statistiche', [StaffController::class, 'statistiche'])->name('statistiche');
            Route::get('/report-attivita', [StaffController::class, 'reportAttivita'])->name('report.attivita');
        });
    });
    
    // =====================================================
    // LIVELLO 4 (Solo amministratori)
    // =====================================================
    
    Route::middleware(['check.level:4'])->group(function () {
        
        Route::prefix('admin')->name('admin.')->group(function () {
            
            // === DASHBOARD AMMINISTRATORE ===
            Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('dashboard');
            
            // *** AGGIUNGI QUESTA RIGA ***
        Route::get('/assegnazioni', [AdminController::class, 'assegnazioni'])->name('assegnazioni');
            
            // === GESTIONE PRODOTTI (CRUD completo per admin) ===
            Route::prefix('prodotti')->name('prodotti.')->group(function () {
                
                // Lista tutti i prodotti per amministrazione
                Route::get('/', [ProdottoController::class, 'adminIndex'])->name('index');
                
                // Creazione nuovo prodotto
                Route::get('/create', [ProdottoController::class, 'create'])->name('create');
                Route::post('/', [ProdottoController::class, 'store'])->name('store');
                
                // Visualizzazione singolo prodotto (admin view)
                Route::get('/{prodotto}', [ProdottoController::class, 'adminShow'])->name('show');
                
                // Modifica prodotto esistente
                Route::get('/{prodotto}/edit', [ProdottoController::class, 'edit'])->name('edit');
                Route::put('/{prodotto}', [ProdottoController::class, 'update'])->name('update');
                
                // Eliminazione prodotto (soft delete)
                Route::delete('/{prodotto}', [ProdottoController::class, 'destroy'])->name('destroy');

                
                
                // === AZIONI SPECIALI SUI PRODOTTI ===
                
                // Ripristino prodotto eliminato
                Route::post('/{prodotto}/restore', [ProdottoController::class, 'restore'])->name('restore');
                
                // Eliminazione definitiva
                Route::delete('/{prodotto}/force-delete', [ProdottoController::class, 'forceDelete'])->name('force-delete');
                
                // Cambio stato attivo/inattivo
                Route::post('/{prodotto}/toggle-status', [ProdottoController::class, 'toggleStatus'])->name('toggle-status');
                
                // Azioni multiple (bulk actions)
                Route::post('/bulk-action', [ProdottoController::class, 'bulkAction'])->name('bulk-action');
            });
            
            // === GESTIONE UTENTI (CRUD completo) ===
            Route::prefix('users')->name('users.')->group(function () {
                
                // Lista tutti gli utenti
                Route::get('/', [UserController::class, 'index'])->name('index');
                
                // Creazione nuovo utente
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                
                // Visualizzazione dettagli utente
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                
                // Modifica utente esistente
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                
                // Eliminazione utente
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                
                // === AZIONI SPECIALI SUGLI UTENTI ===
                
                // Attivazione/disattivazione utente
                Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
                
                // Reset password utente
                Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
                
                // Cambio livello accesso utente
                Route::post('/{user}/change-level', [UserController::class, 'changeLevel'])->name('change-level');
            });
            
            // === REGISTRAZIONE UTENTI (solo admin può registrare nuovi utenti) ===
            Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
            Route::post('/register', [AuthController::class, 'register']);
            
            // === GESTIONE CENTRI ASSISTENZA (Funzionalità Opzionale) ===
            Route::prefix('centri')->name('centri.')->group(function () {
                
                 // Lista centri per amministrazione
    Route::get('/', [CentroAssistenzaController::class, 'adminIndex'])->name('index');
    
    // Creazione nuovo centro
    Route::get('/create', [CentroAssistenzaController::class, 'create'])->name('create');
    Route::post('/', [CentroAssistenzaController::class, 'store'])->name('store');
    
    // RIMUOVI LA DUPLICAZIONE - USA SOLO UNA ROUTE
    Route::get('/{centro}', [CentroAssistenzaController::class, 'adminShow'])->name('show');
    
    // Modifica centro esistente
    Route::get('/{centro}/edit', [CentroAssistenzaController::class, 'edit'])->name('edit');
    Route::put('/{centro}', [CentroAssistenzaController::class, 'update'])->name('update');
    
    // Eliminazione centro
    Route::delete('/{centro}', [CentroAssistenzaController::class, 'destroy'])->name('destroy');
    
                
                // === GESTIONE TECNICI NEI CENTRI ===
                
                // Assegnazione tecnico a centro
               Route::post('/{centro}/assegna-tecnico', [CentroAssistenzaController::class, 'assegnaTecnico'])
                ->name('assegna-tecnico');
                
                // Rimozione tecnico da centro
                Route::delete('/{centro}/rimuovi-tecnico', [CentroAssistenzaController::class, 'rimuoviTecnico'])->name('rimuovi-tecnico');
                
                // Gestione multipla tecnici
                Route::post('/{centro}/gestisci-tecnici', [CentroAssistenzaController::class, 'gestisciTecnici'])->name('gestisci-tecnici');
            });
            
            // === FUNZIONALITÀ OPZIONALE: ASSEGNAZIONE PRODOTTI A STAFF ===
            Route::prefix('assegnazioni')->name('assegnazioni.')->group(function () {
                
                // Dashboard assegnazioni prodotti-staff
                Route::get('/', [AdminController::class, 'assegnazioni'])->name('index');
                
                // Assegnazione singolo prodotto a staff
                Route::post('/assegna-prodotto', [AdminController::class, 'assegnaProdotto'])->name('prodotto');
                
                // Assegnazione multipla prodotti
                Route::post('/assegnazione-multipla', [AdminController::class, 'assegnazioneMultipla'])->name('multipla');
                
                // Rimozione assegnazione
                Route::delete('/rimuovi-assegnazione/{assegnazione}', [AdminController::class, 'rimuoviAssegnazione'])->name('rimuovi');
                
                // Vista assegnazioni per singolo staff
                Route::get('/staff/{user}', [AdminController::class, 'assegnazioniStaff'])->name('staff');
            });
            
            // === STATISTICHE AVANZATE E REPORT ===
            Route::prefix('statistiche')->name('statistiche.')->group(function () {
                
                // Dashboard statistiche complete
                Route::get('/', [AdminController::class, 'statisticheGenerali'])->name('index');
                
                // Report utilizzo sistema
                Route::get('/utilizzo-sistema', [AdminController::class, 'reportUtilizzo'])->name('utilizzo');
                
                // Statistiche prodotti
                Route::get('/prodotti', [AdminController::class, 'statisticheProdotti'])->name('prodotti');
                
                // Statistiche malfunzionamenti
                Route::get('/malfunzionamenti', [AdminController::class, 'statisticheMalfunzionamenti'])->name('malfunzionamenti');
                
                // Report performance centri
                Route::get('/centri-assistenza', [AdminController::class, 'reportCentriAssistenza'])->name('centri');
            });
            
            // === MANUTENZIONE SISTEMA ===
            Route::prefix('manutenzione')->name('manutenzione.')->group(function () {
                
                // Dashboard manutenzione
                Route::get('/', [AdminController::class, 'manutenzione'])->name('index');

                // Pulizia cache
                Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
                
                // Ottimizzazione database
                Route::post('/optimize-database', [AdminController::class, 'optimizeDatabase'])->name('optimize-db');
                
                // Backup dati
                Route::post('/backup-data', [AdminController::class, 'backupData'])->name('backup');
                
                // Controllo integrità dati
                Route::post('/check-integrity', [AdminController::class, 'checkIntegrity'])->name('check-integrity');
            });
            
            // === EXPORT E IMPORT DATI ===
            Route::prefix('export')->name('export.')->group(function () {
                
                // Dashboard export
                Route::get('/', [AdminController::class, 'dashboardExport'])->name('index');
                
                // Export completo sistema
                Route::post('/all-data', [AdminController::class, 'exportAll'])->name('all');
                
                // Export specifici
                Route::post('/prodotti', [AdminController::class, 'exportProdotti'])->name('prodotti');
                Route::post('/utenti', [AdminController::class, 'exportUtenti'])->name('utenti');
                Route::post('/malfunzionamenti', [AdminController::class, 'exportMalfunzionamenti'])->name('malfunzionamenti');
                Route::post('/centri', [AdminController::class, 'exportCentri'])->name('centri');
            });
        });
    });
}); // ← CHIUSURA CORRETTA del middleware auth

// =====================================================
// ROUTE DI TESTING E DEBUGGING (solo ambiente locale)
// =====================================================

if (app()->environment('local')) {
    
    Route::prefix('test')->name('test.')->group(function () {
        
        // Test connessione database
        Route::get('/db', function () {
            try {
                DB::connection()->getPdo();
                return response()->json([
                    'status' => 'OK',
                    'message' => 'Connessione al database riuscita',
                    'database' => config('database.connections.mysql.database'),
                    'timestamp' => now()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Errore connessione DB: ' . $e->getMessage()
                ], 500);
            }
        })->name('db');
        
        // Test configurazione Laravel
        Route::get('/config', function () {
            return response()->json([
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'database' => config('database.default'),
                'session_driver' => config('session.driver'),
                'cache_driver' => config('cache.default'),
                'mail_driver' => config('mail.default'),
            ]);
        })->name('config');
        
        // Test permessi storage e logs
        Route::get('/storage', function () {
            $storagePath = storage_path();
            $logPath = storage_path('logs');
            $logFile = storage_path('logs/laravel.log');
            
            return response()->json([
                'storage_writable' => is_writable($storagePath),
                'logs_directory_exists' => is_dir($logPath),
                'logs_writable' => is_writable($logPath),
                'log_file_exists' => file_exists($logFile),
                'storage_path' => $storagePath,
                'log_path' => $logPath
            ]);
        })->name('storage');
        
        // Visualizzazione ultime righe del log
        Route::get('/logs', function () {
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                $lines = file($logFile);
                $lastLines = array_slice($lines, -50); // Ultime 50 righe
                return response(implode('', $lastLines))->header('Content-Type', 'text/plain');
            }
            
            return response('File di log non trovato', 404);
        })->name('logs');
        
        // Test route per livelli accesso
        Route::get('/level-test/{level?}', function ($level = 2) {
            return response()->json([
                'message' => "Test middleware livello $level riuscito!",
                'user' => Auth::check() ? [
                    'id' => Auth::id(),
                    'username' => Auth::user()->username,
                    'livello_accesso' => Auth::user()->livello_accesso,
                    'nome_completo' => Auth::user()->nome_completo
                ] : 'Non autenticato',
                'timestamp' => now()
            ]);
        })->middleware(['auth', "check.level:{level}"])->name('level-test');
        
        // Test route API admin
        Route::get('/api-routes', function () {
            $adminRoutes = collect(Route::getRoutes())->filter(function($route) {
                return str_contains($route->getName() ?? '', 'api.admin.');
            })->map(function($route) {
                return [
                    'name' => $route->getName(),
                    'uri' => $route->uri(),
                    'methods' => $route->methods()
                ];
            });
            
            return response()->json([
                'admin_routes_count' => $adminRoutes->count(),
                'routes' => $adminRoutes->values(),
                'test_urls' => [
                    'api_base' => url('/api'),
                    'tecnici_disponibili' => url('/api/admin/tecnici-disponibili'),
                    'stats_update' => url('/api/admin/stats-update'),
                    'system_status' => url('/api/admin/system-status')
                ]
            ]);
        })->name('api-routes');
        
        // Test seeder utenti predefiniti (tecntecn, staffstaff, adminadmin)
        Route::get('/users-check', function () {
            $users = \App\Models\User::whereIn('username', ['tecntecn', 'staffstaff', 'adminadmin'])->get();
            
            return response()->json([
                'users_found' => $users->count(),
                'required_users' => ['tecntecn', 'staffstaff', 'adminadmin'],
                'users' => $users->map(function($user) {
                    return [
                        'username' => $user->username,
                        'livello_accesso' => $user->livello_accesso,
                        'nome_completo' => $user->nome_completo,
                        'attivo' => $user->attivo ?? true,
                        'created_at' => $user->created_at
                    ];
                }),
                'missing_users' => collect(['tecntecn', 'staffstaff', 'adminadmin'])
                    ->diff($users->pluck('username'))
                    ->values(),
                'password_info' => [
                    'ssh_password' => 'dNWR53F3',
                    'user_password' => 'dNWRdNWR (primi 4 caratteri SSH ripetuti)'
                ]
            ]);
        })->name('users-check');
        
        // Test middleware completo con simulazione accesso
        Route::get('/auth-simulation/{username}', function ($username) {
            $user = \App\Models\User::where('username', $username)->first();
            
            if (!$user) {
                return response()->json(['error' => 'Utente non trovato'], 404);
            }
            
            // Simula permessi senza autenticazione reale
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'livello_accesso' => $user->livello_accesso,
                    'nome_completo' => $user->nome_completo,
                    'email' => $user->email ?? 'N/A'
                ],
                'permissions' => [
                    'can_access_public' => true,
                    'can_access_tech' => $user->livello_accesso >= 2,
                    'can_access_staff' => $user->livello_accesso >= 3,
                    'can_access_admin' => $user->livello_accesso >= 4,
                    'can_manage_products' => $user->livello_accesso >= 4,
                    'can_manage_users' => $user->livello_accesso >= 4,
                    'can_manage_malfunctions' => $user->livello_accesso >= 3
                ],
                'available_routes' => [
                    'public' => [
                        'home' => route('home'),
                        'prodotti' => route('prodotti.index'),
                        'centri' => route('centri.index'),
                        'azienda' => route('azienda'),
                        'contatti' => route('contatti')
                    ],
                    'authenticated' => [
                        'dashboard' => route('dashboard'),
                        'profilo' => route('profilo')
                    ],
                    'tecnico' => $user->livello_accesso >= 2 ? [
                        'tecnico_dashboard' => route('tecnico.dashboard'),
                        'prodotti_completi' => route('prodotti.completo.index'),
                        'ricerca_malfunzionamenti' => route('malfunzionamenti.ricerca')
                    ] : null,
                    'staff' => $user->livello_accesso >= 3 ? [
                        'staff_dashboard' => route('staff.dashboard'),
                        'gestione_malfunzionamenti' => route('staff.malfunzionamenti.dashboard'),
                        'statistiche_staff' => route('staff.statistiche')
                    ] : null,
                    'admin' => $user->livello_accesso >= 4 ? [
                        'admin_dashboard' => route('admin.dashboard'),
                        'gestione_prodotti' => route('admin.prodotti.index'),
                        'gestione_utenti' => route('admin.users.index'),
                        'gestione_centri' => route('admin.centri.index'),
                        'assegnazioni' => route('admin.assegnazioni.index'),
                        'statistiche' => route('admin.statistiche.index')
                    ] : null
                ]
            ]);
        })->name('auth-simulation');
        
        // Test creazione dati di esempio
        Route::get('/seed-sample-data', function () {
            try {
                // Crea alcuni prodotti di esempio se non esistono
                $prodotti = [
                    [
                        'nome' => 'Lavatrice EcoWash 2000',
                        'categoria' => 'elettrodomestici',
                        'descrizione' => 'Lavatrice a basso consumo energetico',
                        'prezzo' => 599.99
                    ],
                    [
                        'nome' => 'Lavastoviglie SmartClean',
                        'categoria' => 'elettrodomestici', 
                        'descrizione' => 'Lavastoviglie intelligente con sensori',
                        'prezzo' => 449.99
                    ]
                ];
                
                $createdProducts = [];
                foreach ($prodotti as $prodottoData) {
                    $prodotto = \App\Models\Prodotto::firstOrCreate(
                        ['nome' => $prodottoData['nome']],
                        $prodottoData
                    );
                    $createdProducts[] = $prodotto->nome;
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dati di esempio creati/verificati',
                    'created_products' => $createdProducts,
                    'total_products' => \App\Models\Prodotto::count(),
                    'total_users' => \App\Models\User::count()
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Errore nella creazione dati: ' . $e->getMessage()
                ], 500);
            }
        })->name('seed-sample');
        
        // Test validazione struttura database
        Route::get('/db-structure', function () {
            try {
                $tables = [];
                
                // Controlla tabelle principali
                $requiredTables = ['users', 'prodotti', 'malfunzionamenti', 'centri_assistenza'];
                
                foreach ($requiredTables as $table) {
                    $exists = DB::getSchemaBuilder()->hasTable($table);
                    $count = $exists ? DB::table($table)->count() : 0;
                    
                    $tables[$table] = [
                        'exists' => $exists,
                        'records_count' => $count
                    ];
                }
                
                return response()->json([
                    'database' => config('database.connections.mysql.database'),
                    'tables' => $tables,
                    'all_tables_exist' => collect($tables)->every(fn($table) => $table['exists']),
                    'timestamp' => now()
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Errore verifica struttura DB: ' . $e->getMessage()
                ], 500);
            }
        })->name('db-structure');
        
        // Test completo del sistema con report dettagliato
        Route::get('/system-check', function () {
            $report = [
                'timestamp' => now(),
                'environment' => app()->environment(),
                'laravel_version' => app()->version(),
            ];
            
            // Test database
            try {
                DB::connection()->getPdo();
                $report['database'] = [
                    'status' => 'OK',
                    'connection' => config('database.connections.mysql.database')
                ];
            } catch (\Exception $e) {
                $report['database'] = [
                    'status' => 'ERROR',
                    'message' => $e->getMessage()
                ];
            }
            
            // Test storage
            $report['storage'] = [
                'writable' => is_writable(storage_path()),
                'logs_exists' => is_dir(storage_path('logs'))
            ];
            
            // Test route principali
            $mainRoutes = [
                'home' => '/',
                'login' => '/login',
                'prodotti' => '/prodotti',
                'centri' => '/centri-assistenza'
            ];
            
            $report['routes'] = [];
            foreach ($mainRoutes as $name => $url) {
                try {
                    $route = route($name);
                    $report['routes'][$name] = 'OK';
                } catch (\Exception $e) {
                    $report['routes'][$name] = 'ERROR: ' . $e->getMessage();
                }
            }
            
            return response()->json($report);
        })->name('system-check');
        
    }); // ← CHIUSURA gruppo test routes
} // ← CHIUSURA if environment local

// =====================================================
// ROUTE DI FALLBACK E GESTIONE ERRORI
// =====================================================

// Route di fallback per gestire 404 personalizzati
Route::fallback(function () {
    // Controlla se l'utente è autenticato per mostrare una pagina 404 appropriata
    if (Auth::check()) {
        return view('errors.404-authenticated', [
            'user' => Auth::user(),
            'suggested_routes' => [
                'Dashboard' => route('dashboard'),
                'Prodotti' => route('prodotti.pubblico.index'),
                'Centri Assistenza' => route('centri.index'),
                'Profilo' => route('profilo')
            ]
        ]);
    } else {
        return view('errors.404-public', [
            'suggested_routes' => [
                'Homepage' => route('home'),
                'Prodotti' => route('prodotti.index'),
                'Centri Assistenza' => route('centri.index'),
                'Azienda' => route('azienda'),
                'Contatti' => route('contatti'),
                'Login' => route('login')
            ]
        ]);
    }
})->name('fallback');

/*
|--------------------------------------------------------------------------
| RIEPILOGO ORGANIZZAZIONE ROUTE - SISTEMA ASSISTENZA TECNICA
|--------------------------------------------------------------------------
|
| === CREDENZIALI GRUPPO 51 ===
| SSH Password: dNWR53F3
| Utenti predefiniti con password: dNWRdNWR
| - tecntecn (Livello 2 - Tecnico)
| - staffstaff (Livello 3 - Staff)  
| - adminadmin (Livello 4 - Admin)
|
| === LIVELLI DI ACCESSO ===
|
| LIVELLO 1 (Pubblico):
| ✓ Homepage, azienda, contatti
| ✓ Catalogo prodotti (senza malfunzionamenti)
| ✓ Lista centri assistenza
| ✓ Ricerca prodotti con wildcard (*) 
| ✓ Documentazione PDF
|
| LIVELLO 2 (Tecnici):
| ✓ Dashboard tecnico
| ✓ Catalogo prodotti completo (con malfunzionamenti)
| ✓ Visualizzazione e ricerca malfunzionamenti
| ✓ Segnalazione problemi
| ✓ Storico interventi personali
|
| LIVELLO 3 (Staff):
| ✓ Dashboard staff
| ✓ CRUD completo malfunzionamenti e soluzioni
| ✓ Gestione prodotti assegnati (opzionale)
| ✓ Statistiche attività staff
|
| LIVELLO 4 (Admin):
| ✓ Dashboard amministratore
| ✓ CRUD completo prodotti (esclusi malfunzionamenti)
| ✓ CRUD completo utenti (tecnici e staff)
| ✓ CRUD completo centri assistenza (opzionale)
| ✓ Assegnazione prodotti a staff (opzionale)
| ✓ Statistiche avanzate e report
| ✓ Manutenzione sistema
| ✓ Export/Import dati
|
| === API ROUTES ===
| ✓ Tutte le funzionalità hanno controparti API per AJAX
| ✓ Organizzate per livello di accesso
| ✓ Utilizzabili da JavaScript per interfacce dinamiche
|
| === FUNZIONALITÀ OPZIONALI IMPLEMENTATE ===
| 1. ✓ Gestione centri assistenza con assegnazione tecnici
| 2. ✓ Ripartizione gestione prodotti tra staff membri
|
| === MIDDLEWARE UTILIZZATI ===
| - auth: Verifica autenticazione utente
| - check.level:X: Verifica livello accesso minimo X
|
| === NAMING CONVENTION ROUTE ===
| - Route pubbliche: nome.azione (es. prodotti.index)
| - Route tecnici: tecnico.nome o nome.completo.azione  
| - Route staff: staff.nome.azione
| - Route admin: admin.nome.azione
| - Route API: api.nome.azione
|
| === TESTING E DEBUG ===
| ✓ Route test per ambiente locale
| ✓ Verifica connessione database
| ✓ Test middleware e permessi
| ✓ Controllo utenti predefiniti
| ✓ Validazione struttura database
| ✓ Report sistema completo
|
*/