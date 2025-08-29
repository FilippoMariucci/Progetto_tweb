<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\User;
use Carbon\Carbon;

/**
 * Controller per le funzionalità specifiche dello staff aziendale (Livello 3)
 * 
 * Lo staff aziendale può:
 * - Gestire malfunzionamenti e soluzioni (CRUD completo)
 * - Visualizzare prodotti assegnati (funzionalità opzionale)
 * - Accedere a statistiche personali e report attività
 * - Utilizzare API per aggiornamenti dinamici via AJAX
 * 
 * Route protette da middleware: auth, check.level:3
 */
class StaffController extends Controller
{
    /**
     * Costruttore - Applica middleware di sicurezza
     */
    public function __construct()
    {
        // Middleware obbligatori per tutte le funzioni del controller
        $this->middleware('auth');
        $this->middleware('check.level:3');
    }

    // ================================================
    // DASHBOARD E VISTE PRINCIPALI
    // ================================================

    /**
     * Dashboard principale dello staff
     * Mostra panoramica generale con statistiche iniziali
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Log accesso dashboard per audit
        Log::info('Accesso dashboard staff', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => request()->ip(),
            'timestamp' => now()
        ]);

        try {
            // Statistiche basilari per inizializzare la vista
            // I dettagli vengono caricati via AJAX per performance migliori
            $stats = [
                'malfunzionamenti_gestiti' => $this->getConteggioBaser('malfunzionamenti_gestiti', $user->id),
                'soluzioni_create' => $this->getConteggioBaser('soluzioni_create', $user->id),
                'prodotti_assegnati' => $this->getConteggioBaser('prodotti_assegnati', $user->id),
                'risolti_mese' => $this->getConteggioBaser('risolti_mese', $user->id),
            ];

            // Carica prodotti assegnati per la vista
            $prodottiAssegnati = Prodotto::where('staff_assegnato_id', $user->id)
                ->with(['malfunzionamenti' => function($q) {
                    $q->orderByDesc('gravita')->orderByDesc('created_at')->take(3);
                }])
                ->orderBy('nome')
                ->take(5)
                ->get();

            // Malfunzionamenti prioritari che richiedono attenzione
            $malfunzionamentiPrioritari = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->where('gravita', 'critica')
                ->with('prodotto')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            // Ultimi malfunzionamenti gestiti dallo staff
            $ultimiMalfunzionamenti = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with('prodotto')
                ->orderByDesc('updated_at')
                ->take(10)
                ->get();

            return view('staff.dashboard', compact(
                'user', 
                'stats', 
                'prodottiAssegnati',
                'malfunzionamentiPrioritari',
                'ultimiMalfunzionamenti'
            ));

        } catch (\Exception $e) {
            Log::error('Errore dashboard staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Vista di fallback con messaggio di errore
            return view('staff.dashboard', [
                'user' => $user,
                'stats' => [],
                'prodottiAssegnati' => collect(),
                'malfunzionamentiPrioritari' => collect(),
                'ultimiMalfunzionamenti' => collect(),
                'error' => 'Errore nel caricamento della dashboard'
            ]);
        }
    }

    /**
     * Visualizza i prodotti assegnati all'utente staff corrente
     * Funzionalità opzionale - implementa ripartizione gestione prodotti
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function prodottiAssegnati(Request $request)
    {
        $user = Auth::user();

        try {
            // Query base per prodotti assegnati all'utente
            $query = Prodotto::where('staff_assegnato_id', $user->id)
                         ->with(['malfunzionamenti' => function($q) {
                             $q->orderBy('gravita', 'desc')
                               ->orderBy('created_at', 'desc');
                         }]);
            
            // Applicazione filtri dalla request
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }
            
            if ($request->boolean('solo_critici')) {
                $query->whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                });
            }
            
            // CORREZIONE: Gestione ricerca migliorata
            $searchTerm = $request->input('search');
            if ($searchTerm && trim($searchTerm) !== '') {
                $searchTerm = trim($searchTerm);
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('modello', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('codice', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Ordinamento
            $sortField = $request->input('sort', 'nome');
            $sortDirection = $request->input('direction', 'asc');
            
            if (in_array($sortField, ['nome', 'categoria', 'created_at', 'updated_at'])) {
                $query->orderBy($sortField, $sortDirection);
            }
            
            $prodottiAssegnati = $query->paginate(15);
            
            // Calcolo statistiche per prodotti assegnati
            $stats = [
                'totale_assegnati' => $prodottiAssegnati->total(),
                'con_malfunzionamenti' => Prodotto::where('staff_assegnato_id', $user->id)
                                                   ->whereHas('malfunzionamenti')
                                                   ->count(),
                'critici' => Prodotto::where('staff_assegnato_id', $user->id)
                                     ->whereHas('malfunzionamenti', function($q) {
                                         $q->where('gravita', 'critica');
                                     })->count(),
                'senza_malfunzionamenti' => Prodotto::where('staff_assegnato_id', $user->id)
                                                    ->whereDoesntHave('malfunzionamenti')
                                                    ->count()
            ];
            
            // Categorie disponibili per filtro dropdown
            $categorie = Prodotto::where('staff_assegnato_id', $user->id)
                                 ->distinct()
                                 ->pluck('categoria')
                                 ->filter()
                                 ->sort()
                                 ->values();
            
            return view('staff.prodotti-assegnati', compact(
                'prodottiAssegnati', 'stats', 'categorie', 'user'
            ));

        } catch (\Exception $e) {
            Log::error('Errore prodotti assegnati staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Errore nel caricamento dei prodotti assegnati');
        }
    }

    /**
     * Statistiche dettagliate per lo staff corrente
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function statistiche(Request $request)
    {
        $user = Auth::user();
        $periodo = $request->input('periodo', 'mese'); // settimana, mese, trimestre, anno
        
        try {
            // Calcolo date per il periodo selezionato
            $dataInizio = match($periodo) {
                'settimana' => now()->startOfWeek(),
                'mese' => now()->startOfMonth(),
                'trimestre' => now()->startOfQuarter(),
                'anno' => now()->startOfYear(),
                default => now()->startOfMonth()
            };
            
            // Statistiche principali del periodo
            $stats = [
                'prodotti_assegnati' => Prodotto::where('staff_assegnato_id', $user->id)->count(),
                
                'malfunzionamenti_gestiti' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->where('updated_at', '>=', $dataInizio)
                    ->count(),
                
                'soluzioni_create' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->where('created_at', '>=', $dataInizio)
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
                
                'malfunzionamenti_per_gravita' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->selectRaw('gravita, COUNT(*) as count')
                    ->groupBy('gravita')
                    ->pluck('count', 'gravita')
                    ->toArray(),
                
                'risolti_mese' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
            ];
            
            // Grafico attività mensile (ultimi 6 mesi)
            $attivitaMensile = [];
            for ($i = 5; $i >= 0; $i--) {
                $mese = now()->copy()->subMonths($i);
                $attivitaMensile[] = [
                    'mese' => $mese->format('M Y'),
                    'soluzioni' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                            $q->where('staff_assegnato_id', $user->id);
                        })
                        ->whereYear('created_at', $mese->year)
                        ->whereMonth('created_at', $mese->month)
                        ->count()
                ];
            }
            
            // Top 5 prodotti più problematici assegnati all'utente
            $prodottiProblematici = Prodotto::where('staff_assegnato_id', $user->id)
                ->withCount(['malfunzionamenti as problemi_totali'])
                ->withCount(['malfunzionamenti as problemi_critici' => function($q) {
                    $q->where('gravita', 'critica');
                }])
                ->having('problemi_totali', '>', 0)
                ->orderByDesc('problemi_critici')
                ->orderByDesc('problemi_totali')
                ->take(5)
                ->get();
            
            return view('staff.statistiche', compact(
                'user', 'stats', 'attivitaMensile', 'prodottiProblematici', 'periodo'
            ));

        } catch (\Exception $e) {
            Log::error('Errore statistiche staff', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'periodo' => $periodo
            ]);

            return view('staff.statistiche', [
                'user' => $user,
                'stats' => [],
                'attivitaMensile' => [],
                'prodottiProblematici' => collect(),
                'periodo' => $periodo,
                'error' => 'Errore nel caricamento delle statistiche'
            ]);
        }
    }

    /**
     * Report dettagliato delle attività dello staff
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function reportAttivita(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Parametri filtro dal form
            $dataInizio = $request->input('data_inizio', now()->startOfMonth()->format('Y-m-d'));
            $dataFine = $request->input('data_fine', now()->format('Y-m-d'));
            $tipoAttivita = $request->input('tipo', 'all'); // all, create, update, delete
            
            // Query base per le attività del periodo
            $query = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with(['prodotto'])
                ->whereBetween('updated_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59']);
            
            // Applicazione filtro per tipo attività se necessario
            if ($tipoAttivita !== 'all') {
                // Per future implementazioni con audit log
                // Qui potresti filtrare per tipo di azione (create, update, delete)
            }
            
            $attivita = $query->orderByDesc('updated_at')->paginate(20);
            
            // Statistiche del periodo selezionato
            $statsReport = [
                'totale_attivita' => $attivita->total(),
                'prodotti_modificati' => $query->distinct('prodotto_id')->count('prodotto_id'),
                'nuove_soluzioni' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereBetween('created_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59'])
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
                'modifiche_soluzioni' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereBetween('updated_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59'])
                    ->where('created_at', '<', $dataInizio . ' 00:00:00') // Modifiche a record esistenti
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

    // ================================================
    // GESTIONE MALFUNZIONAMENTI (CRUD STAFF)
    // ================================================

    /**
     * Mostra i malfunzionamenti di un prodotto specifico
     * 
     * @param int $productId - ID del prodotto
     * @return \Illuminate\View\View
     */
    public function showMalfunzionamento($productId)
    {
        // Trova il prodotto specificato, altrimenti genera errore 404
        $prodotto = Prodotto::findOrFail($productId);
        
        // Recupera tutti i malfunzionamenti associati al prodotto
        $malfunzionamenti = $prodotto->malfunzionamenti;
        
        // Restituisce la vista con il prodotto e i suoi malfunzionamenti
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti'));
    }

    /**
     * NUOVO METODO: Mostra il form per creare un nuovo malfunzionamento
     * con possibilità di scegliere il prodotto dalla dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function createNuovaSoluzione()
    {
        // Verifica che l'utente sia staff e autenticato
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff');
        }

        // Recupera tutti i prodotti disponibili per la selezione
        // Ordina per categoria e nome per facilitare la ricerca
        $prodotti = Prodotto::orderBy('categoria')
                           ->orderBy('nome')
                           ->get();

        // Crea un prodotto vuoto per mantenere compatibilità con la view esistente
        // La view malfunzionamenti.create si aspetta una variabile $prodotto
        $prodotto = null; // Sarà null per indicare che deve essere selezionato
        
        // Passa un flag per indicare che è una "nuova soluzione" dalla dashboard
        $isNuovaSoluzione = true;
        
        // Restituisce la view malfunzionamenti.create con tutti i prodotti
        // e il flag per modificare il comportamento della view
        return view('malfunzionamenti.create', compact('prodotto', 'prodotti', 'isNuovaSoluzione'));
    }

    /**
     * NUOVO METODO: Salva un nuovo malfunzionamento creato dalla dashboard
     * con prodotto selezionato dal dropdown
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con i dati del form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNuovaSoluzione(Request $request)
    {
        // Verifica che l'utente sia staff e autenticato
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff');
        }

        // Validazione dei dati di input
        // Ora include anche la validazione del prodotto selezionato
        $request->validate([
            'prodotto_id' => 'required|exists:prodotti,id',  // Prodotto deve esistere nel DB
            'titolo' => 'required|string|max:255',           // Titolo obbligatorio
            'descrizione' => 'required|string',              // Descrizione obbligatoria
            'soluzione' => 'required|string',                // Soluzione obbligatoria
            'gravita' => 'required|in:bassa,media,alta',     // Gravità deve essere uno dei valori permessi
            'componente_difettoso' => 'nullable|string|max:255', // Componente opzionale
            'codice_errore' => 'nullable|string|max:50',     // Codice errore opzionale
        ]);

        try {
            // Crea un nuovo malfunzionamento nel database
            $malfunzionamento = Malfunzionamento::create([
                'prodotto_id' => $request->prodotto_id,           // ID del prodotto selezionato
                'titolo' => $request->titolo,                     // Titolo del problema
                'descrizione' => $request->descrizione,           // Descrizione dettagliata
                'soluzione' => $request->soluzione,               // Soluzione tecnica
                'gravita' => $request->gravita,                   // Livello di gravità
                'componente_difettoso' => $request->componente_difettoso, // Componente coinvolto
                'codice_errore' => $request->codice_errore,       // Eventuale codice di errore
                'creato_da' => Auth::id(),                        // ID dello staff che ha creato
                'stato' => 'attivo',                              // Imposta come attivo
                'numero_segnalazioni' => 0,                       // Inizializza contatore
                'created_at' => now(),                            // Timestamp creazione
                'updated_at' => now()                             // Timestamp ultima modifica
            ]);

            // Log dell'operazione per debugging (opzionale)
            if (config('app.debug')) {
                \Log::info('Nuova soluzione creata da dashboard staff', [
                    'malfunzionamento_id' => $malfunzionamento->id,
                    'prodotto_id' => $request->prodotto_id,
                    'staff_id' => Auth::id(),
                    'titolo' => $request->titolo
                ]);
            }

            // Reindirizza alla dashboard staff con messaggio di successo
            return redirect()->route('staff.dashboard')
                            ->with('success', 'Nuova soluzione aggiunta con successo al prodotto: ' . 
                                   $malfunzionamento->prodotto->nome);

        } catch (\Exception $e) {
            // Gestione errori: log dell'errore e messaggio utente
            \Log::error('Errore creazione nuova soluzione', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Reindirizza indietro con errore e mantiene i dati inseriti
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Errore durante la creazione della soluzione. Riprova.');
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
        // Trova il prodotto specificato
        $prodotto = Prodotto::findOrFail($productId);
        
        // Restituisce la vista del form di creazione
        return view('staff.create_malfunzionamento', compact('prodotto'));
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
        // Validazione dei dati di input
        $request->validate([
            'title' => 'required|string|max:255',        // Titolo obbligatorio, stringa, max 255 caratteri
            'description' => 'required|string',          // Descrizione obbligatoria
            'solution' => 'required|string',             // Soluzione obbligatoria
            'gravita' => 'in:bassa,media,alta,critica',  // Validazione livello gravità
        ]);

        // Crea un nuovo malfunzionamento nel database
        Malfunzionamento::create([
            'prodotto_id' => $productId,                  // Associa il malfunzionamento al prodotto
            'titolo' => $request->title,                  // Titolo del malfunzionamento
            'descrizione' => $request->description,       // Descrizione del problema
            'soluzione' => $request->solution,            // Soluzione tecnica
            'gravita' => $request->gravita ?? 'media',   // Livello di gravità
            'creato_da_staff_id' => Auth::id(),           // ID staff che ha creato
        ]);

        // Reindirizza alla pagina dei malfunzionamenti del prodotto con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento aggiunto con successo!');
    }

    /**
     * Mostra il form per modificare un malfunzionamento esistente
     * 
     * @param int $id - ID del malfunzionamento da modificare
     * @return \Illuminate\View\View
     */
    public function editMalfunction($id)
    {
        // Trova il malfunzionamento specificato con il prodotto associato
        $malfunzionamento = Malfunzionamento::with('prodotto')->findOrFail($id);
        
        // Verifica che lo staff possa modificare questo malfunzionamento
        // (se implementi la funzionalità opzionale di assegnazione prodotti)
        $user = Auth::user();
        if ($user->livello_accesso < 4) { // Non è admin
            // Controlla se il prodotto è assegnato all'utente corrente
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questo malfunzionamento');
            }
        }
        
        // Restituisce la vista del form di modifica
        return view('staff.edit_malfunzionamento', compact('malfunzionamento'));
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
        // Validazione dei dati di input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'solution' => 'required|string',
            'gravita' => 'in:bassa,media,alta,critica',
        ]);

        // Trova il malfunzionamento da aggiornare
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        
        // Verifica permessi (come sopra)
        $user = Auth::user();
        if ($user->livello_accesso < 4) {
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                abort(403, 'Non hai i permessi per modificare questo malfunzionamento');
            }
        }
        
        // Aggiorna i campi con i nuovi valori
        $malfunzionamento->update([
            'titolo' => $request->title,
            'descrizione' => $request->description,
            'soluzione' => $request->solution,
            'gravita' => $request->gravita,
            'modificato_da_staff_id' => Auth::id(),       // Traccia chi ha modificato
        ]);

        // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $malfunzionamento->prodotto_id)
                        ->with('success', 'Malfunzionamento aggiornato con successo!');
    }

    /**
     * Elimina un malfunzionamento dal database
     * 
     * @param int $id - ID del malfunzionamento da eliminare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMalfunction($id)
    {
        // Trova il malfunzionamento da eliminare
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        $productId = $malfunzionamento->prodotto_id;  // Salva l'ID del prodotto per il redirect

        // Verifica permessi
        $user = Auth::user();
        if ($user->livello_accesso < 4) {
            if ($malfunzionamento->prodotto->staff_assegnato_id && 
                $malfunzionamento->prodotto->staff_assegnato_id !== $user->id) {
                abort(403, 'Non hai i permessi per eliminare questo malfunzionamento');
            }
        }

        // Log dell'eliminazione
        Log::info('Eliminazione malfunzionamento', [
            'malfunzionamento_id' => $id,
            'prodotto_id' => $productId,
            'staff_id' => $user->id,
            'titolo' => $malfunzionamento->titolo
        ]);

        // Elimina il malfunzionamento dal database
        $malfunzionamento->delete();

        // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento eliminato con successo!');
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
        // Trova il prodotto specificato
        $prodotto = Prodotto::findOrFail($productId);
        
        // Ottiene il termine di ricerca dalla richiesta
        $searchTerm = $request->get('search', '');
        
        // Se c'è un termine di ricerca, filtra i malfunzionamenti
        if ($searchTerm) {
            // Cerca nei malfunzionamenti del prodotto quelli che contengono il termine nella descrizione
            $malfunzionamenti = $prodotto->malfunzionamenti()
                                   ->where(function($q) use ($searchTerm) {
                                       $q->where('descrizione', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('titolo', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('soluzione', 'like', '%' . $searchTerm . '%');
                                   })
                                   ->get();
        } else {
            // Se non c'è termine di ricerca, mostra tutti i malfunzionamenti
            $malfunzionamenti = $prodotto->malfunzionamenti;
        }
        
        // Restituisce la vista con i risultati della ricerca
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti', 'searchTerm'));
    }

    // ================================================
    // API METHODS PER CHIAMATE AJAX
    // ================================================

    /**
     * API: Statistiche staff per aggiornamenti AJAX
     * Route: GET /api/stats
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStats()
    {
        try {
            $user = Auth::user();
            
            // Calcolo statistiche in tempo reale
            $stats = [
                'prodotti_assegnati' => Prodotto::where('staff_assegnato_id', $user->id)->count(),
                
                'malfunzionamenti_gestiti' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })->count(),
                
                'soluzioni_create' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
                
                'risolti_mese' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                        $q->where('staff_assegnato_id', $user->id);
                    })
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count(),
            ];
            
            // Log della richiesta API per monitoring
            Log::info('API Stats Staff richiesta', [
                'user_id' => $user->id,
                'stats' => $stats,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->nome_completo ?? $user->name,
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
                'message' => 'Errore nel caricamento delle statistiche staff',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API: Ultime soluzioni create dallo staff corrente
     * Route: GET /api/ultime-soluzioni
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiUltimeSoluzioni(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = min($request->get('limit', 5), 20); // Massimo 20 risultati
            
            $soluzioni = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with('prodotto')
                ->whereNotNull('soluzione')
                ->where('soluzione', '!=', '')
                ->orderByDesc('updated_at')
                ->take($limit)
                ->get()
                ->map(function($malfunzionamento) {
                    return [
                        'id' => $malfunzionamento->id,
                        'titolo' => $malfunzionamento->titolo ?? $malfunzionamento->title ?? 'Senza titolo',
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
     * API: Malfunzionamenti prioritari che richiedono intervento
     * Route: GET /api/malfunzionamenti-prioritari
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiMalfunzionamentiPrioritari()
    {
        try {
            $user = Auth::user();
            
            $prioritari = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->where(function($q) {
                    $q->where('gravita', 'critica')
                      ->orWhere('gravita', 'alta')
                      ->orWhere('gravita', 'urgente');
                })
                ->with('prodotto')
                ->orderByRaw("FIELD(gravita, 'critica', 'urgente', 'alta')")
                ->orderByDesc('created_at')
                ->take(8)
                ->get()
                ->map(function($malfunzionamento) {
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
                        'segnalazioni_count' => $malfunzionamento->numero_segnalazioni ?? 0,
                        'ha_soluzione' => !empty($malfunzionamento->soluzione ?? $malfunzionamento->solution),
                        'created_at' => $malfunzionamento->created_at,
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
     * API: Prodotti assegnati allo staff corrente
     * Route: GET /api/prodotti-assegnati
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiProdottiAssegnati()
    {
        try {
            $user = Auth::user();
            
            // Query solo per prodotti effettivamente assegnati all'utente
            $prodotti = Prodotto::where('staff_assegnato_id', $user->id)
                ->with(['malfunzionamenti'])
                ->orderBy('nome')
                ->get()
                ->map(function($prodotto) {
                    return [
                        'id' => $prodotto->id,
                        'nome' => $prodotto->nome,
                        'categoria' => $prodotto->categoria,
                        'codice' => $prodotto->codice ?? 'N/A',
                        'descrizione' => $prodotto->descrizione ?? 'Nessuna descrizione',
                        'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
                        'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
                        'ultima_modifica' => $prodotto->updated_at->toISOString(),
                        'attivo' => $prodotto->attivo ?? true,
                        // URL per azioni staff
                        'management_url' => route('staff.malfunzionamenti.index') . '?prodotto_id=' . $prodotto->id,
                        'add_malfunction_url' => route('staff.malfunzionamenti.create', $prodotto->id),
                        'view_url' => route('prodotti.completo.show', $prodotto->id)
                    ];
                });
            
            // Statistiche sui prodotti assegnati
            $stats = [
                'totale_assegnati' => $prodotti->count(),
                'con_malfunzionamenti' => $prodotti->filter(fn($p) => $p['malfunzionamenti_count'] > 0)->count(),
                'critici' => $prodotti->filter(fn($p) => $p['critici_count'] > 0)->count(),
                'senza_problemi' => $prodotti->filter(fn($p) => $p['malfunzionamenti_count'] === 0)->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $prodotti->values(), // Array indicizzato numericamente
                'stats' => $stats,
                'total' => $prodotti->count(),
                'user_id' => $user->id,
                'message' => $prodotti->count() > 0 
                    ? "Trovati {$prodotti->count()} prodotti assegnati" 
                    : "Nessun prodotto assegnato a questo utente staff"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Errore API prodotti assegnati', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei prodotti assegnati',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ================================================
    // METODI DI SUPPORTO PRIVATI
    // ================================================

    /**
     * Calcola conteggi base per le statistiche iniziali della dashboard
     * 
     * @param string $tipo
     * @param int $userId
     * @return int
     */
    private function getConteggioBaser($tipo, $userId)
    {
        try {
            switch ($tipo) {
                case 'malfunzionamenti_gestiti':
                    return Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                        $q->where('staff_assegnato_id', $userId);
                    })->count();
                
                case 'soluzioni_create':
                    return Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                        $q->where('staff_assegnato_id', $userId);
                    })
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count();
                
                case 'prodotti_assegnati':
                    return Prodotto::where('staff_assegnato_id', $userId)->count();
                
                case 'risolti_mese':
                    return Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                        $q->where('staff_assegnato_id', $userId);
                    })
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->whereNotNull('soluzione')
                    ->where('soluzione', '!=', '')
                    ->count();
                
                default:
                    return 0;
            }
        } catch (\Exception $e) {
            Log::warning('Errore calcolo conteggio base', [
                'tipo' => $tipo,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Calcola il trend mensile per le statistiche
     * 
     * @return array
     */
    private function calcolaTrendMensile()
    {
        try {
            $user = Auth::user();
            
            $meseCorrente = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $mesePrecedente = Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            
            $trend = $mesePrecedente > 0 
                ? round((($meseCorrente - $mesePrecedente) / $mesePrecedente) * 100, 1)
                : 0;
            
            return [
                'mese_corrente' => $meseCorrente,
                'mese_precedente' => $mesePrecedente,
                'percentuale_variazione' => $trend,
                'direzione' => $trend > 0 ? 'aumento' : ($trend < 0 ? 'diminuzione' : 'stabile')
            ];
            
        } catch (\Exception $e) {
            Log::error('Errore calcolo trend mensile', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return [
                'mese_corrente' => 0,
                'mese_precedente' => 0,
                'percentuale_variazione' => 0,
                'direzione' => 'stabile'
            ];
        }
    }

    /**
     * Verifica se l'utente corrente può gestire il malfunzionamento specificato
     * 
     * @param \App\Models\Malfunzionamento $malfunzionamento
     * @return bool
     */
    private function puoGestireMalfunzionamento($malfunzionamento)
    {
        $user = Auth::user();
        
        // Gli amministratori possono gestire tutto
        if ($user->livello_accesso >= 4) {
            return true;
        }
        
        // Lo staff può gestire solo i prodotti assegnati (se implementata la funzionalità opzionale)
        if ($malfunzionamento->prodotto->staff_assegnato_id) {
            return $malfunzionamento->prodotto->staff_assegnato_id === $user->id;
        }
        
        // Se non c'è assegnazione specifica, tutti gli staff possono gestire
        return true;
    }

    /**
     * Registra un'azione dello staff per audit log
     * 
     * @param string $azione
     * @param \App\Models\Malfunzionamento $malfunzionamento
     * @param array $datiAggiuntivi
     * @return void
     */
    private function logAzioneStaff($azione, $malfunzionamento = null, $datiAggiuntivi = [])
    {
        $logData = [
            'user_id' => Auth::id(),
            'azione' => $azione,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ];
        
        if ($malfunzionamento) {
            $logData['malfunzionamento_id'] = $malfunzionamento->id;
            $logData['prodotto_id'] = $malfunzionamento->prodotto_id;
            $logData['titolo'] = $malfunzionamento->titolo;
        }
        
        if (!empty($datiAggiuntivi)) {
            $logData = array_merge($logData, $datiAggiuntivi);
        }
        
        Log::info('Azione Staff', $logData);
    }

    /**
     * Ottiene le metriche di performance dello staff
     * 
     * @param int $userId
     * @param string $periodo
     * @return array
     */
    private function getMetrichePerformance($userId, $periodo = 'mese')
    {
        $dataInizio = match($periodo) {
            'settimana' => now()->startOfWeek(),
            'mese' => now()->startOfMonth(),
            'trimestre' => now()->startOfQuarter(),
            'anno' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
        
        return [
            'nuovi_malfunzionamenti' => Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                    $q->where('staff_assegnato_id', $userId);
                })
                ->where('created_at', '>=', $dataInizio)
                ->count(),
                
            'soluzioni_completate' => Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                    $q->where('staff_assegnato_id', $userId);
                })
                ->where('updated_at', '>=', $dataInizio)
                ->whereNotNull('soluzione')
                ->where('soluzione', '!=', '')
                ->count(),
                
            'tempo_medio_risoluzione' => $this->calcolaTempoMedioRisoluzione($userId, $dataInizio),
            
            'tasso_risoluzione' => $this->calcolaTassoRisoluzione($userId, $dataInizio)
        ];
    }

    /**
     * Calcola il tempo medio di risoluzione per lo staff
     * 
     * @param int $userId
     * @param \Carbon\Carbon $dataInizio
     * @return float
     */
    private function calcolaTempoMedioRisoluzione($userId, $dataInizio)
    {
        try {
            $malfunzionamenti = Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                    $q->where('staff_assegnato_id', $userId);
                })
                ->where('created_at', '>=', $dataInizio)
                ->whereNotNull('soluzione')
                ->where('soluzione', '!=', '')
                ->get();
            
            if ($malfunzionamenti->count() === 0) {
                return 0;
            }
            
            $tempiTotali = $malfunzionamenti->sum(function($malfunzionamento) {
                return $malfunzionamento->created_at->diffInHours($malfunzionamento->updated_at);
            });
            
            return round($tempiTotali / $malfunzionamenti->count(), 1);
            
        } catch (\Exception $e) {
            Log::error('Errore calcolo tempo medio risoluzione', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return 0;
        }
    }

    /**
     * Calcola il tasso di risoluzione per lo staff
     * 
     * @param int $userId
     * @param \Carbon\Carbon $dataInizio
     * @return float
     */
    private function calcolaTassoRisoluzione($userId, $dataInizio)
    {
        try {
            $totaliMalfunzionamenti = Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                    $q->where('staff_assegnato_id', $userId);
                })
                ->where('created_at', '>=', $dataInizio)
                ->count();
            
            if ($totaliMalfunzionamenti === 0) {
                return 0;
            }
            
            $risolti = Malfunzionamento::whereHas('prodotto', function($q) use ($userId) {
                    $q->where('staff_assegnato_id', $userId);
                })
                ->where('created_at', '>=', $dataInizio)
                ->whereNotNull('soluzione')
                ->where('soluzione', '!=', '')
                ->count();
            
            return round(($risolti / $totaliMalfunzionamenti) * 100, 1);
            
        } catch (\Exception $e) {
            Log::error('Errore calcolo tasso risoluzione', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return 0;
        }
    }

    /**
     * Genera un rapporto CSV delle attività dello staff
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function esportaCsv(Request $request)
    {
        $user = Auth::user();
        $dataInizio = $request->input('data_inizio', now()->startOfMonth()->format('Y-m-d'));
        $dataFine = $request->input('data_fine', now()->format('Y-m-d'));
        
        $filename = "staff_report_{$user->id}_{$dataInizio}_to_{$dataFine}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($user, $dataInizio, $dataFine) {
            $file = fopen('php://output', 'w');
            
            // Intestazioni CSV
            fputcsv($file, [
                'ID Malfunzionamento',
                'Prodotto',
                'Titolo',
                'Descrizione',
                'Gravita',
                'Soluzione',
                'Data Creazione',
                'Data Aggiornamento'
            ]);
            
            // Dati
            Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                })
                ->with('prodotto')
                ->whereBetween('created_at', [$dataInizio . ' 00:00:00', $dataFine . ' 23:59:59'])
                ->chunk(100, function($malfunzionamenti) use ($file) {
                    foreach ($malfunzionamenti as $malfunzionamento) {
                        fputcsv($file, [
                            $malfunzionamento->id,
                            $malfunzionamento->prodotto->nome,
                            $malfunzionamento->titolo,
                            $malfunzionamento->descrizione,
                            $malfunzionamento->gravita,
                            $malfunzionamento->soluzione,
                            $malfunzionamento->created_at->format('d/m/Y H:i'),
                            $malfunzionamento->updated_at->format('d/m/Y H:i')
                        ]);
                    }
                });
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

/* 
|--------------------------------------------------------------------------
| FINE STAFFCONTROLLER - GRUPPO 51
|--------------------------------------------------------------------------
| 
| Controller completo per la gestione delle funzionalità staff aziendale
| Livello di accesso 3 - Gestione completa malfunzionamenti e soluzioni
| 
| Funzionalità implementate:
| ✅ Dashboard con statistiche real-time
| ✅ CRUD completo malfunzionamenti
| ✅ Gestione prodotti assegnati (funzionalità opzionale)
| ✅ API per aggiornamenti AJAX
| ✅ Sistema di logging e audit
| ✅ Report CSV esportabili
| ✅ Metriche di performance
| ✅ Controllo permessi granulare
| ✅ Gestione errori completa
| 
| Middleware applicati:
| - auth: Verifica autenticazione
| - check.level:3: Verifica livello staff (3+)
| 
| Route associate:
| - staff.dashboard: Dashboard principale
| - staff.prodotti.assegnati: Prodotti assegnati
| - staff.statistiche: Statistiche dettagliate
| - staff.report.attivita: Report attività
| - staff.malfunzionamenti.*: CRUD malfunzionamenti
| 
| API endpoints:
| - GET /api/stats: Statistiche staff
| - GET /api/ultime-soluzioni: Ultime soluzioni create
| - GET /api/malfunzionamenti-prioritari: Problemi prioritari
| - GET /api/prodotti-assegnati: Prodotti assegnati all'utente
| 
| Gruppo: 51
| Utente staff predefinito: staffstaff
| Password: dNWRdNWR
| 
*/