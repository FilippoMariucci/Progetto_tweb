<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Controller per la gestione delle funzionalità riservate allo staff aziendale (Livello 3)
 * 
 * Lo staff può:
 * - Visualizzare dashboard con statistiche
 * - Gestire malfunzionamenti e soluzioni (CRUD completo)
 * - Visualizzare prodotti assegnati
 * - Generare report delle attività
 * - Accedere a statistiche avanzate
 */
class StaffController extends Controller
{
    /**
     * Costruttore del controller
     * Applica middleware di autenticazione e verifica livello staff
     */
    public function __construct()
    {
        // Middleware di autenticazione obbligatorio
        $this->middleware('auth');
        
        // Middleware per verificare livello staff (3+)
        $this->middleware('check.level:3');
    }

    // ================================================
    // DASHBOARD E VISTE PRINCIPALI
    // ================================================

    /**
     * Dashboard principale dello staff con statistiche iniziali
     * I dati dettagliati vengono caricati via AJAX
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Log accesso alla dashboard
        Log::info('Accesso dashboard staff', [
            'user_id' => $user->id,
            'username' => $user->username,
            'timestamp' => now()
        ]);

        // Statistiche basilari per inizializzare la vista
        // I dati dettagliati vengono caricati via AJAX dalle API
        $stats = [
            'prodotti_lista' => collect(), // Vuoto, caricato via AJAX
            'loading' => true // Flag per mostrare spinner di caricamento
        ];

        return view('staff.dashboard', compact('user', 'stats'));
    }

    /**
     * Pagina delle statistiche dettagliate dello staff
     * 
     * @return \Illuminate\View\View
     */
    public function statistiche()
    {
        $user = Auth::user();

        try {
            // Statistiche dettagliate per la pagina dedicata
            $stats = [
                'malfunzionamenti_mese_corrente' => Malfunzionamento::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                
                'soluzioni_ultima_settimana' => Malfunzionamento::where('created_at', '>=', now()->subWeek())
                    ->count(),
                    
                'prodotti_con_problemi' => Prodotto::has('malfunzionamenti')->count(),
                
                'richieste_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),
                
                'trend_mensile' => $this->calcolaTrendMensile(),
                
                'top_categorie_problematiche' => $this->getTopCategorieProblematiche(),
                
                // Statistiche aggiuntive
                'media_soluzioni_per_prodotto' => $this->getMediaSoluzioniPerProdotto(),
                'distribuzione_gravita' => $this->getDistribuzioneGravita()
            ];

            return view('staff.statistiche', compact('user', 'stats'));

        } catch (\Exception $e) {
            Log::error('Errore in statistiche staff', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // Statistiche di fallback
            $stats = [
                'errore' => 'Impossibile caricare alcune statistiche'
            ];

            return view('staff.statistiche', compact('user', 'stats'))
                ->with('error', 'Errore nel caricamento delle statistiche dettagliate');
        }
    }

    /**
     * Report delle attività dello staff
     * 
     * @return \Illuminate\View\View
     */
    public function reportAttivita()
    {
        $user = Auth::user();
        
        try {
            // Dati per il report mensile
            $reportData = [
                'periodo' => now()->format('F Y'),
                'malfunzionamenti_risolti' => Malfunzionamento::whereMonth('updated_at', now()->month)->count(),
                'nuove_soluzioni' => Malfunzionamento::whereMonth('created_at', now()->month)->count(),
                'categorie_attive' => Prodotto::distinct('categoria')->count(),
                'prodotti_aggiornati' => Prodotto::whereMonth('updated_at', now()->month)->count(),
                
                // Dettagli aggiuntivi
                'soluzioni_per_categoria' => $this->getSoluzioniPerCategoria(),
                'andamento_settimanale' => $this->getAndamentoSettimanale(),
                'prodotti_piu_problematici' => $this->getProdottiPiuProblematici(10)
            ];

            return view('staff.report-attivita', compact('user', 'reportData'));

        } catch (\Exception $e) {
            Log::error('Errore in report attività staff', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Errore nella generazione del report');
        }
    }

    // ================================================
    // API METHODS PER CHIAMATE AJAX
    // ================================================

    /**
     * API per le statistiche dello staff (chiamata AJAX)
     * Route: GET /api/staff/stats
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStats()
    {
        try {
            $user = Auth::user();

            // Calcola statistiche reali dal database
            $malfunzionamentiGestiti = Malfunzionamento::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $soluzioniAggiunte = Malfunzionamento::where('created_at', '>=', now()->subDays(30))
                ->count();

            $prodottiSeguiti = Prodotto::count();

            // Calcola richieste urgenti (critica o alta priorità)
            $richiesteUrgenti = Malfunzionamento::where(function($query) {
                $query->where('gravita', 'critica')
                      ->orWhere('gravita', 'alta')
                      ->orWhere('gravita', 'urgente');
            })->count();

            // Log della richiesta API
            Log::info('API Stats Staff richieste', [
                'user_id' => $user->id,
                'stats' => [
                    'malfunzionamenti' => $malfunzionamentiGestiti,
                    'soluzioni' => $soluzioniAggiunte,
                    'prodotti' => $prodottiSeguiti,
                    'urgenti' => $richiesteUrgenti
                ]
            ]);

            return response()->json([
                'success' => true,
                'malfunzionamenti_gestiti' => $malfunzionamentiGestiti,
                'soluzioni_aggiunte' => $soluzioniAggiunte,
                'prodotti_seguiti' => $prodottiSeguiti,
                'richieste_urgenti' => $richiesteUrgenti,
                'timestamp' => now()->toISOString(),
                'user_info' => [
                    'name' => $user->nome_completo,
                    'level' => $user->livello_accesso
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Errore API stats staff', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API per le ultime soluzioni create (chiamata AJAX)
     * Route: GET /api/staff/ultime-soluzioni
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiUltimeSoluzioni(Request $request)
    {
        try {
            $limit = min($request->get('limit', 10), 50); // Massimo 50 risultati

            // Recupera le ultime soluzioni dal database
            $soluzioni = Malfunzionamento::with(['prodotto'])
                ->latest()
                ->take($limit)
                ->get()
                ->map(function($malfunzionamento) {
                    return [
                        'id' => $malfunzionamento->id,
                        'title' => $malfunzionamento->title,
                        'description' => $malfunzionamento->description,
                        'prodotto_nome' => $malfunzionamento->prodotto->nome ?? 'Prodotto sconosciuto',
                        'prodotto_categoria' => $malfunzionamento->prodotto->categoria ?? 'N/A',
                        'prodotto_id' => $malfunzionamento->prodotto_id,
                        'created_at' => $malfunzionamento->created_at->toISOString(),
                        'updated_at' => $malfunzionamento->updated_at->toISOString(),
                        'gravita' => $malfunzionamento->gravita ?? 'normale',
                        'has_solution' => !empty($malfunzionamento->solution)
                    ];
                });

            Log::info('API Ultime soluzioni staff', [
                'user_id' => Auth::id(),
                'limit' => $limit,
                'results_count' => $soluzioni->count()
            ]);

            return response()->json($soluzioni);

        } catch (\Exception $e) {
            Log::error('Errore API ultime soluzioni', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle soluzioni'
            ], 500);
        }
    }

    /**
     * API per i prodotti più problematici (chiamata AJAX)
     * Route: GET /api/staff/malfunzionamenti-prioritari
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiMalfunzionamentiPrioritari()
    {
        try {
            // Recupera prodotti con più malfunzionamenti
            $prodottiProblematici = Prodotto::withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }
                ])
                ->orderBy('malfunzionamenti_count', 'desc')
                ->having('malfunzionamenti_count', '>', 0)
                ->take(5)
                ->get()
                ->map(function($prodotto) {
                    return [
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'categoria' => $prodotto->categoria,
                        'codice' => $prodotto->codice ?? 'N/A',
                        'malfunzionamenti_count' => $prodotto->malfunzionamenti_count,
                        'critici_count' => $prodotto->critici_count ?? 0,
                        'livello_priorita' => $this->calcolaPriorita($prodotto->malfunzionamenti_count, $prodotto->critici_count ?? 0)
                    ];
                });

            return response()->json($prodottiProblematici);

        } catch (\Exception $e) {
            Log::error('Errore API prodotti problematici', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei prodotti problematici'
            ], 500);
        }
    }

    /**
     * API per ottenere i prodotti assegnati allo staff
     * Route: GET /api/staff/prodotti-assegnati
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiProdottiAssegnati()
    {
        try {
            $user = Auth::user();

            // Per ora restituisce tutti i prodotti
            // In futuro implementerai la relazione prodotti assegnati
            $prodotti = Prodotto::with(['malfunzionamenti'])
                ->orderBy('nome')
                ->get()
                ->map(function($prodotto) {
                    return [
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'categoria' => $prodotto->categoria,
                        'codice' => $prodotto->codice ?? 'N/A',
                        'descrizione' => $prodotto->descrizione,
                        'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
                        'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
                        'ultima_modifica' => $prodotto->updated_at->toISOString(),
                        'attivo' => $prodotto->attivo ?? true
                    ];
                });

            return response()->json([
                'success' => true,
                'prodotti' => $prodotti,
                'total' => $prodotti->count(),
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Errore API prodotti assegnati', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei prodotti'
            ], 500);
        }
    }

    /**
     * API per creare un nuovo malfunzionamento via AJAX
     * Route: POST /staff/prodotti/{prodotto}/malfunzionamenti
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $prodottoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStoreMalfunzionamento(Request $request, $prodottoId)
    {
        try {
            // Validazione dei dati
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'solution' => 'required|string|max:2000',
                'gravita' => 'nullable|in:bassa,normale,alta,critica,urgente'
            ]);

            // Verifica esistenza prodotto
            $prodotto = Prodotto::findOrFail($prodottoId);

            // Crea malfunzionamento
            $malfunzionamento = Malfunzionamento::create([
                'prodotto_id' => $prodottoId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'solution' => $validated['solution'],
                'gravita' => $validated['gravita'] ?? 'normale',
                'created_by' => Auth::id(), // Se hai il campo nella tabella
            ]);

            Log::info('Nuovo malfunzionamento creato via API', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'prodotto_id' => $prodottoId,
                'user_id' => Auth::id(),
                'title' => $validated['title']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Malfunzionamento creato con successo',
                'data' => [
                    'id' => $malfunzionamento->id,
                    'title' => $malfunzionamento->title,
                    'prodotto_nome' => $prodotto->nome,
                    'created_at' => $malfunzionamento->created_at->toISOString()
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prodotto non trovato'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Errore creazione malfunzionamento', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'prodotto_id' => $prodottoId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione del malfunzionamento'
            ], 500);
        }
    }

    /**
     * API per esportare report via AJAX
     * Route: POST /api/staff/export-report
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiExportReport()
    {
        try {
            $user = Auth::user();
            
            // Per ora restituisce un placeholder
            // In futuro implementerai la generazione PDF con librerie come DOMPDF
            
            Log::info('Richiesta export report staff', [
                'user_id' => $user->id,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Funzionalità export in sviluppo',
                'download_url' => null,
                'filename' => 'report_staff_' . now()->format('Y-m-d') . '.pdf',
                'note' => 'La generazione PDF sarà implementata nelle prossime versioni'
            ]);

        } catch (\Exception $e) {
            Log::error('Errore export report staff', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'export del report'
            ], 500);
        }
    }

    // ================================================
    // GESTIONE MALFUNZIONAMENTI (CRUD TRADIZIONALE)
    // ================================================

    /**
     * Mostra i malfunzionamenti di un prodotto specifico
     * 
     * @param int $productId - ID del prodotto
     * @return \Illuminate\View\View
     */
    public function showMalfunzionamento($productId)
    {
        try {
            // Trova il prodotto specificato, altrimenti genera errore 404
            $prodotto = Prodotto::findOrFail($productId);
            
            // Recupera tutti i malfunzionamenti associati al prodotto
            $malfunzionamenti = $prodotto->malfunzionamenti()
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Restituisce la vista con il prodotto e i suoi malfunzionamenti
            return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione malfunzionamenti', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore nel caricamento dei malfunzionamenti');
        }
    }

    /**
     * Mostra il form per creare un nuovo malfunzionamento
     * 
     * @param int $productId - ID del prodotto a cui aggiungere il malfunzionamento
     * @return \Illuminate\View\View
     */
    public function createMalfunzionamento($productId)
    {
        try {
            // Trova il prodotto specificato
            $prodotto = Prodotto::findOrFail($productId);
            
            // Lista delle gravità disponibili
            $gravita = [
                'bassa' => 'Bassa',
                'normale' => 'Normale',
                'alta' => 'Alta',
                'critica' => 'Critica',
                'urgente' => 'Urgente'
            ];
            
            // Restituisce la vista del form di creazione
            return view('staff.create_malfunzionamento', compact('prodotto', 'gravita'));

        } catch (\Exception $e) {
            Log::error('Errore form creazione malfunzionamento', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore nel caricamento del form');
        }
    }

    /**
     * Salva un nuovo malfunzionamento nel database
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con i dati del form
     * @param int $productId - ID del prodotto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMalfunction(Request $request, $productId)
    {
        try {
            // Validazione dei dati di input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'solution' => 'required|string|max:2000',
                'gravita' => 'nullable|in:bassa,normale,alta,critica,urgente'
            ]);

            // Crea un nuovo malfunzionamento nel database
            $malfunzionamento = Malfunzionamento::create([
                'prodotto_id' => $productId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'solution' => $validated['solution'],
                'gravita' => $validated['gravita'] ?? 'normale',
                'created_by' => Auth::id(),
            ]);

            Log::info('Nuovo malfunzionamento creato', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'prodotto_id' => $productId,
                'user_id' => Auth::id()
            ]);

            // Reindirizza alla pagina dei malfunzionamenti del prodotto con messaggio di successo
            return redirect()->route('staff.malfunzionamenti', $productId)
                            ->with('success', 'Malfunzionamento aggiunto con successo!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Errore salvataggio malfunzionamento', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore durante il salvataggio')->withInput();
        }
    }

    /**
     * Mostra il form per modificare un malfunzionamento esistente
     * 
     * @param int $id - ID del malfunzionamento da modificare
     * @return \Illuminate\View\View
     */
    public function editMalfunction($id)
    {
        try {
            // Trova il malfunzionamento specificato con il prodotto associato
            $malfunzionamento = Malfunzionamento::with('prodotto')->findOrFail($id);
            
            // Lista delle gravità disponibili
            $gravita = [
                'bassa' => 'Bassa',
                'normale' => 'Normale',
                'alta' => 'Alta',
                'critica' => 'Critica',
                'urgente' => 'Urgente'
            ];
            
            // Restituisce la vista del form di modifica
            return view('staff.edit_malfunzionamento', compact('malfunzionamento', 'gravita'));

        } catch (\Exception $e) {
            Log::error('Errore form modifica malfunzionamento', [
                'error' => $e->getMessage(),
                'malfunzionamento_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore nel caricamento del form di modifica');
        }
    }

    /**
     * Aggiorna un malfunzionamento esistente nel database
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con i nuovi dati
     * @param int $id - ID del malfunzionamento da aggiornare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMalfunction(Request $request, $id)
    {
        try {
            // Validazione dei dati di input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'solution' => 'required|string|max:2000',
                'gravita' => 'nullable|in:bassa,normale,alta,critica,urgente'
            ]);

            // Trova il malfunzionamento da aggiornare
            $malfunzionamento = Malfunzionamento::findOrFail($id);
            
            // Aggiorna i campi con i nuovi valori
            $malfunzionamento->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'solution' => $validated['solution'],
                'gravita' => $validated['gravita'] ?? 'normale',
                'updated_by' => Auth::id(), // Se hai il campo nella tabella
            ]);

            Log::info('Malfunzionamento aggiornato', [
                'malfunzionamento_id' => $id,
                'user_id' => Auth::id()
            ]);

            // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
            return redirect()->route('staff.malfunzionamenti', $malfunzionamento->prodotto_id)
                            ->with('success', 'Malfunzionamento aggiornato con successo!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Errore aggiornamento malfunzionamento', [
                'error' => $e->getMessage(),
                'malfunzionamento_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore durante l\'aggiornamento')->withInput();
        }
    }

    /**
     * Elimina un malfunzionamento dal database
     * 
     * @param int $id - ID del malfunzionamento da eliminare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMalfunction($id)
    {
        try {
            // Trova il malfunzionamento da eliminare
            $malfunzionamento = Malfunzionamento::findOrFail($id);
            $productId = $malfunzionamento->prodotto_id;  // Salva l'ID del prodotto per il redirect

            Log::info('Eliminazione malfunzionamento', [
                'malfunzionamento_id' => $id,
                'prodotto_id' => $productId,
                'title' => $malfunzionamento->title,
                'user_id' => Auth::id()
            ]);

            // Elimina il malfunzionamento dal database
            $malfunzionamento->delete();

            // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
            return redirect()->route('staff.malfunzionamenti', $productId)
                            ->with('success', 'Malfunzionamento eliminato con successo!');

        } catch (\Exception $e) {
            Log::error('Errore eliminazione malfunzionamento', [
                'error' => $e->getMessage(),
                'malfunzionamento_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore durante l\'eliminazione');
        }
    }

    /**
     * Ricerca malfunzionamenti per un prodotto specifico basandosi su un termine di ricerca
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con il termine di ricerca
     * @param int $productId - ID del prodotto
     * @return \Illuminate\View\View
     */
    public function searchMalfunctions(Request $request, $productId)
    {
        try {
            // Trova il prodotto specificato
            $prodotto = Prodotto::findOrFail($productId);
            
            // Ottiene il termine di ricerca dalla richiesta
            $searchTerm = $request->get('search', '');
            
            // Query di base per i malfunzionamenti del prodotto
            $query = $prodotto->malfunzionamenti();
            
            // Se c'è un termine di ricerca, filtra i malfunzionamenti
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('solution', 'like', '%' . $searchTerm . '%');
                });
            }
            
            // Esegue la query con paginazione
            $malfunzionamenti = $query->orderBy('created_at', 'desc')->paginate(10);
            
            // Restituisce la vista con i risultati della ricerca
            return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti', 'searchTerm'));

        } catch (\Exception $e) {
            Log::error('Errore ricerca malfunzionamenti', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'search_term' => $request->get('search'),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Errore durante la ricerca');
        }
    }

    // ================================================
    // FUNZIONALITÀ OPZIONALE: PRODOTTI ASSEGNATI
    // ================================================

    /**
     * Visualizza i prodotti assegnati allo staff corrente
     * (Funzionalità opzionale - da implementare se richiesta)
     * 
     * @return \Illuminate\View\View
     */
    public function prodottiAssegnati()
    {
        $user = Auth::user();

        try {
            // Per ora mostra tutti i prodotti
            // In futuro implementerai la relazione user->prodottiAssegnati
            $prodotti = Prodotto::with(['malfunzionamenti' => function($query) {
                    $query->latest()->take(3); // Ultimi 3 malfunzionamenti per prodotto
                }])
                ->withCount(['malfunzionamenti', 'malfunzionamenti as critici_count' => function($query) {
                    $query->where('gravita', 'critica');
                }])
                ->orderBy('nome')
                ->paginate(12);

            return view('staff.prodotti-assegnati', compact('user', 'prodotti'));

        } catch (\Exception $e) {
            Log::error('Errore prodotti assegnati staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return back()->with('error', 'Errore nel caricamento dei prodotti assegnati');
        }
    }

    // ================================================
    // METODI DI SUPPORTO (PRIVATE)
    // ================================================

    /**
     * Calcola il trend mensile dei malfunzionamenti
     * 
     * @return float
     */
    private function calcolaTrendMensile()
    {
        try {
            $meseCorrente = Malfunzionamento::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $mesePrecedente = Malfunzionamento::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();

            if ($mesePrecedente == 0) {
                return $meseCorrente > 0 ? 100 : 0;
            }

            return round((($meseCorrente - $mesePrecedente) / $mesePrecedente) * 100, 1);

        } catch (\Exception $e) {
            Log::error('Errore calcolo trend mensile', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Ottiene le categorie più problematiche
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getTopCategorieProblematiche()
    {
        try {
            return Prodotto::select('categoria')
                ->withCount('malfunzionamenti')
                ->groupBy('categoria')
                ->orderBy('malfunzionamenti_count', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'categoria' => $item->categoria,
                        'count' => $item->malfunzionamenti_count
                    ];
                });

        } catch (\Exception $e) {
            Log::error('Errore top categorie problematiche', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Calcola la media delle soluzioni per prodotto
     * 
     * @return float
     */
    private function getMediaSoluzioniPerProdotto()
    {
        try {
            $totaleProdotti = Prodotto::count();
            $totaleSoluzioni = Malfunzionamento::count();

            return $totaleProdotti > 0 ? round($totaleSoluzioni / $totaleProdotti, 2) : 0;

        } catch (\Exception $e) {
            Log::error('Errore calcolo media soluzioni', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Ottiene la distribuzione per gravità dei malfunzionamenti
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getDistribuzioneGravita()
    {
        try {
            return Malfunzionamento::select('gravita', DB::raw('COUNT(*) as count'))
                ->groupBy('gravita')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->gravita ?? 'normale' => $item->count];
                });

        } catch (\Exception $e) {
            Log::error('Errore distribuzione gravità', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ottiene le soluzioni per categoria di prodotto
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getSoluzioniPerCategoria()
    {
        try {
            return DB::table('malfunzionamenti')
                ->join('prodotti', 'malfunzionamenti.prodotto_id', '=', 'prodotti.id')
                ->select('prodotti.categoria', DB::raw('COUNT(*) as soluzioni_count'))
                ->groupBy('prodotti.categoria')
                ->orderBy('soluzioni_count', 'desc')
                ->get();

        } catch (\Exception $e) {
            Log::error('Errore soluzioni per categoria', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ottiene l'andamento settimanale dei malfunzionamenti
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getAndamentoSettimanale()
    {
        try {
            $settimane = collect();
            
            for ($i = 6; $i >= 0; $i--) {
                $data = now()->subWeeks($i);
                $count = Malfunzionamento::whereBetween('created_at', [
                    $data->startOfWeek()->copy(),
                    $data->endOfWeek()->copy()
                ])->count();
                
                $settimane->push([
                    'settimana' => $data->format('d/m'),
                    'count' => $count
                ]);
            }

            return $settimane;

        } catch (\Exception $e) {
            Log::error('Errore andamento settimanale', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ottiene i prodotti più problematici
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    private function getProdottiPiuProblematici($limit = 5)
    {
        try {
            return Prodotto::withCount('malfunzionamenti')
                ->orderBy('malfunzionamenti_count', 'desc')
                ->having('malfunzionamenti_count', '>', 0)
                ->take($limit)
                ->get()
                ->map(function($prodotto) {
                    return [
                        'nome' => $prodotto->nome,
                        'categoria' => $prodotto->categoria,
                        'malfunzionamenti_count' => $prodotto->malfunzionamenti_count
                    ];
                });

        } catch (\Exception $e) {
            Log::error('Errore prodotti più problematici', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Calcola la priorità di un prodotto basata sui malfunzionamenti
     * 
     * @param int $totale
     * @param int $critici
     * @return string
     */
    private function calcolaPriorita($totale, $critici)
    {
        if ($critici > 0) {
            return 'critica';
        } elseif ($totale > 10) {
            return 'alta';
        } elseif ($totale > 5) {
            return 'media';
        } else {
            return 'bassa';
        }
    }

    // ================================================
    // METODI AGGIUNTIVI PER FUNZIONALITÀ FUTURE
    // ================================================

    /**
     * Esporta report in formato CSV
     * (Funzionalità futura)
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCSV()
    {
        $user = Auth::user();
        
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=staff_report_' . now()->format('Y-m-d') . '.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Prodotto', 'Categoria', 'Malfunzionamenti', 'Ultima Modifica']);
            
            // Dati
            $prodotti = Prodotto::withCount('malfunzionamenti')->get();
            foreach ($prodotti as $prodotto) {
                fputcsv($file, [
                    $prodotto->nome,
                    $prodotto->categoria,
                    $prodotto->malfunzionamenti_count,
                    $prodotto->updated_at->format('d/m/Y')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ricerca globale nei malfunzionamenti per termine
     * (Funzionalità aggiuntiva)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function ricercaGlobale(Request $request)
    {
        $searchTerm = $request->get('q', '');
        $risultati = collect();

        if (!empty($searchTerm)) {
            $risultati = Malfunzionamento::with('prodotto')
                ->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%')
                ->orWhere('solution', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('prodotto', function($query) use ($searchTerm) {
                    $query->where('nome', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('staff.ricerca-globale', compact('searchTerm', 'risultati'));
    }
}