<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use Illuminate\Support\Facades\Auth;

/**
 * Controller per la gestione delle funzionalità riservate allo staff aziendale (Livello 3)
 * Lo staff può gestire malfunzionamenti e soluzioni dei prodotti
 */
class StaffController extends Controller
{
    /**
     * Costruttore del controller
     * Applica il middleware di autenticazione per verificare che l'utente sia loggato
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostra la dashboard dello staff con i prodotti disponibili
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Recupera tutti i prodotti dal database per mostrarli nella dashboard
        $prodotti = Prodotto::all();

        // Restituisce la vista della dashboard dello staff passando i prodotti
        return view('staff.dashboard', compact('prodotti'));
    }

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
        ]);

        // Crea un nuovo malfunzionamento nel database
        Malfunzionamento::create([
            'prodotto_id' => $productId,                  // Associa il malfunzionamento al prodotto
            'title' => $request->title,                  // Titolo del malfunzionamento
            'description' => $request->description,      // Descrizione del problema
            'solution' => $request->solution,            // Soluzione tecnica
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
        ]);

        // Trova il malfunzionamento da aggiornare
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        
        // Aggiorna i campi con i nuovi valori
        $malfunzionamento->update([
            'title' => $request->title,
            'description' => $request->description,
            'solution' => $request->solution,
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
                                   ->where('description', 'like', '%' . $searchTerm . '%')
                                   ->get();
        } else {
            // Se non c'è termine di ricerca, mostra tutti i malfunzionamenti
            $malfunzionamenti = $prodotto->malfunzionamenti;
        }
        
        // Restituisce la vista con i risultati della ricerca
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti', 'searchTerm'));
    }

    /**
 * API per le statistiche dello staff (chiamata AJAX)
 * Route: GET /api/staff/stats
 */
public function apiStats()
{
    try {
        // Verifica autenticazione e autorizzazione
        if (!Auth::check() || Auth::user()->livello_accesso < 3) {
            return response()->json([
                'success' => false, 
                'message' => 'Accesso riservato allo staff'
            ], 403);
        }

        $user = Auth::user();

        // Calcola statistiche reali dal database
        $malfunzionamentiGestiti = Malfunzionamento::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $soluzioniAggiunte = Malfunzionamento::where('created_at', '>=', now()->subDays(30))
            ->count();

        $prodottiSeguiti = Prodotto::count();

        $richiesteUrgenti = Malfunzionamento::where('gravita', 'critica')
            ->orWhere('gravita', 'alta')
            ->count();

        return response()->json([
            'success' => true,
            'malfunzionamenti_gestiti' => $malfunzionamentiGestiti,
            'soluzioni_aggiunte' => $soluzioniAggiunte,
            'prodotti_seguiti' => $prodottiSeguiti,
            'richieste_urgenti' => $richiesteUrgenti,
            'timestamp' => now()->toISOString()
        ]);

    } catch (\Exception $e) {
        \Log::error('Errore API stats staff', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore nel caricamento delle statistiche'
        ], 500);
    }
}

/**
 * API per le ultime soluzioni create (chiamata AJAX)
 * Route: GET /api/staff/ultime-soluzioni
 */
public function apiUltimeSoluzioni(Request $request)
{
    try {
        // Verifica autorizzazione
        if (!Auth::check() || Auth::user()->livello_accesso < 3) {
            return response()->json(['success' => false], 403);
        }

        $limit = $request->get('limit', 10);

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
                    'created_at' => $malfunzionamento->created_at->toISOString(),
                    'gravita' => $malfunzionamento->gravita ?? 'normale'
                ];
            });

        return response()->json($soluzioni);

    } catch (\Exception $e) {
        \Log::error('Errore API ultime soluzioni', [
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
 */
public function apiMalfunzionamentiPrioritari()
{
    try {
        // Verifica autorizzazione
        if (!Auth::check() || Auth::user()->livello_accesso < 3) {
            return response()->json(['success' => false], 403);
        }

        // Recupera prodotti con più malfunzionamenti
        $prodottiProblematici = Prodotto::withCount('malfunzionamenti')
            ->orderBy('malfunzionamenti_count', 'desc')
            ->having('malfunzionamenti_count', '>', 0)
            ->take(5)
            ->get()
            ->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'categoria' => $prodotto->categoria,
                    'malfunzionamenti_count' => $prodotto->malfunzionamenti_count
                ];
            });

        return response()->json($prodottiProblematici);

    } catch (\Exception $e) {
        \Log::error('Errore API prodotti problematici', [
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
 */
public function apiProdottiAssegnati()
{
    try {
        // Verifica autorizzazione
        if (!Auth::check() || Auth::user()->livello_accesso < 3) {
            return response()->json(['success' => false], 403);
        }

        $user = Auth::user();

        // Se esiste la relazione prodotti assegnati, usala
        // Altrimenti restituisci tutti i prodotti (per ora)
        $prodotti = Prodotto::with(['malfunzionamenti'])
            ->get()
            ->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'categoria' => $prodotto->categoria,
                    'codice' => $prodotto->codice ?? 'N/A',
                    'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
                    'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count()
                ];
            });

        return response()->json([
            'success' => true,
            'prodotti' => $prodotti,
            'total' => $prodotti->count()
        ]);

    } catch (\Exception $e) {
        \Log::error('Errore API prodotti assegnati', [
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
 * Aggiornata la dashboard dello staff per passare solo le statistiche essenziali
 */
public function dashboard()
{
    // Verifica autorizzazione
    if (!Auth::check() || Auth::user()->livello_accesso < 3) {
        abort(403, 'Accesso riservato allo staff aziendale');
    }

    $user = Auth::user();

    // Statistiche basilari per inizializzare la vista
    // I dati dettagliati vengono caricati via AJAX
    $stats = [
        'prodotti_lista' => collect(), // Vuoto, verrà caricato via AJAX
        'loading' => true // Flag per mostrare gli spinner
    ];

    return view('staff.dashboard', compact('user', 'stats'));
}

/**
 * Pagina delle statistiche dettagliate dello staff
 */
public function statistiche()
{
    // Verifica autorizzazione
    if (!Auth::check() || Auth::user()->livello_accesso < 3) {
        abort(403, 'Accesso riservato allo staff aziendale');
    }

    $user = Auth::user();

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
        
        'top_categorie_problematiche' => $this->getTopCategorieProblematiche()
    ];

    return view('staff.statistiche', compact('user', 'stats'));
}

/**
 * Report delle attività dello staff
 */
public function reportAttivita()
{
    // Verifica autorizzazione
    if (!Auth::check() || Auth::user()->livello_accesso < 3) {
        abort(403, 'Accesso riservato allo staff aziendale');
    }

    $user = Auth::user();
    
    // Dati per il report
    $reportData = [
        'periodo' => now()->format('F Y'),
        'malfunzionamenti_risolti' => Malfunzionamento::whereMonth('updated_at', now()->month)->count(),
        'nuove_soluzioni' => Malfunzionamento::whereMonth('created_at', now()->month)->count(),
        'categorie_attive' => Prodotto::distinct('categoria')->count(),
        'prodotti_aggiornati' => Prodotto::whereMonth('updated_at', now()->month)->count()
    ];

    return view('staff.report-attivita', compact('user', 'reportData'));
}

/**
 * Metodo di supporto: calcola il trend mensile
 */
private function calcolaTrendMensile()
{
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
}

/**
 * Metodo di supporto: ottiene le categorie più problematiche
 */
private function getTopCategorieProblematiche()
{
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
}
}