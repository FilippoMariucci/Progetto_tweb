<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Malfunzionamento;
use App\Models\Prodotto;
use Illuminate\Support\Str;

class MalfunzionamentoController extends Controller
{
    /**
     * Visualizza tutti i malfunzionamenti per un prodotto specifico
     * Accessibile solo a tecnici (livello 2+)
     */
    public function index(Request $request, Prodotto $prodotto)
    {
        // Verifica autorizzazioni
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // Query base per i malfunzionamenti del prodotto
        $query = $prodotto->malfunzionamenti();

        // === RICERCA NEI MALFUNZIONAMENTI ===
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            // Ricerca full-text nella descrizione del malfunzionamento
            $query->whereRaw(
                "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
                [$searchTerm . '*']
            );
        }

        // === FILTRO PER GRAVITÀ ===
        if ($request->filled('gravita')) {
            $query->where('gravita', $request->input('gravita'));
        }

        // === FILTRO PER DIFFICOLTÀ ===
        if ($request->filled('difficolta')) {
            $query->where('difficolta', $request->input('difficolta'));
        }

        // === ORDINAMENTO ===
        $orderBy = $request->input('order', 'gravita'); // Default: ordina per gravità
        
        switch ($orderBy) {
            case 'gravita':
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')");
                break;
            case 'frequenza':
                $query->orderBy('numero_segnalazioni', 'desc');
                break;
            case 'recente':
                $query->orderBy('ultima_segnalazione', 'desc');
                break;
            case 'difficolta':
                $query->orderByRaw("FIELD(difficolta, 'esperto', 'difficile', 'media', 'facile')");
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Carica relazioni e paginazione
        $malfunzionamenti = $query->with(['creatoBy', 'modificatoBy'])
            ->paginate(10);

        // Statistiche per il prodotto
        $stats = [
            'totale' => $prodotto->malfunzionamenti()->count(),
            'critici' => $prodotto->malfunzionamenti()->where('gravita', 'critica')->count(),
            'alta_gravita' => $prodotto->malfunzionamenti()->where('gravita', 'alta')->count(),
            'totale_segnalazioni' => $prodotto->malfunzionamenti()->sum('numero_segnalazioni'),
        ];

        return view('malfunzionamenti.index', compact('prodotto', 'malfunzionamenti', 'stats'));
    }

    /**
     * Visualizza un singolo malfunzionamento con soluzione completa
     */
    public function show(Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        // Verifica che il malfunzionamento appartenga al prodotto
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404, 'Malfunzionamento non trovato per questo prodotto');
        }

        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // Carica le relazioni
        $malfunzionamento->load(['creatoBy', 'modificatoBy', 'prodotto']);

        // Malfunzionamenti correlati (stessa gravità o categoria prodotto)
        $correlati = Malfunzionamento::where('id', '!=', $malfunzionamento->id)
            ->where(function($query) use ($malfunzionamento) {
                $query->where('gravita', $malfunzionamento->gravita)
                      ->orWhereHas('prodotto', function($q) use ($malfunzionamento) {
                          $q->where('categoria', $malfunzionamento->prodotto->categoria);
                      });
            })
            ->with('prodotto')
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(5)
            ->get();

        return view('malfunzionamenti.show', compact('prodotto', 'malfunzionamento', 'correlati'));
    }

    /**
     * Mostra form per creare nuovo malfunzionamento (solo staff)
     */
    public function create(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Solo lo staff può creare malfunzionamenti');
        }

        return view('malfunzionamenti.create', compact('prodotto'));
    }

    /**
     * Salva nuovo malfunzionamento
     */
    public function store(Request $request, Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        // Validazione completa
        $validated = $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'required|string',
            'gravita' => 'required|in:bassa,media,alta,critica',
            'soluzione' => 'required|string',
            'strumenti_necessari' => 'nullable|string',
            'tempo_stimato' => 'nullable|integer|min:1|max:999',
            'difficolta' => 'required|in:facile,media,difficile,esperto',
            'numero_segnalazioni' => 'nullable|integer|min:1',
            'prima_segnalazione' => 'nullable|date|before_or_equal:today',
        ], [
            'titolo.required' => 'Il titolo è obbligatorio',
            'descrizione.required' => 'La descrizione del problema è obbligatoria',
            'soluzione.required' => 'La soluzione è obbligatoria',
            'gravita.in' => 'Gravità non valida',
            'difficolta.in' => 'Difficoltà non valida',
            'tempo_stimato.max' => 'Il tempo stimato non può superare 999 minuti',
        ]);

        // Dati aggiuntivi
        $validated['prodotto_id'] = $prodotto->id;
        $validated['creato_da'] = Auth::id();
        $validated['numero_segnalazioni'] = $validated['numero_segnalazioni'] ?? 1;
        $validated['prima_segnalazione'] = $validated['prima_segnalazione'] ?? now()->toDateString();
        $validated['ultima_segnalazione'] = now()->toDateString();

        // Creazione malfunzionamento
        $malfunzionamento = Malfunzionamento::create($validated);

        \Log::info('Nuovo malfunzionamento creato', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'prodotto_id' => $prodotto->id,
            'gravita' => $malfunzionamento->gravita,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('malfunzionamenti.show', [$prodotto, $malfunzionamento])
            ->with('success', 'Malfunzionamento aggiunto con successo');
    }

   public function edit(Malfunzionamento $malfunzionamento)
{
    $prodotto = $malfunzionamento->prodotto;

    if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
        abort(403, 'Solo lo staff può modificare malfunzionamenti');
    }

    return view('malfunzionamenti.edit', compact('prodotto', 'malfunzionamento'));
}

    /**
     * Aggiorna malfunzionamento esistente
     */
    public function update(Request $request, Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        $validated = $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'required|string',
            'gravita' => 'required|in:bassa,media,alta,critica',
            'soluzione' => 'required|string',
            'strumenti_necessari' => 'nullable|string',
            'tempo_stimato' => 'nullable|integer|min:1|max:999',
            'difficolta' => 'required|in:facile,media,difficile,esperto',
            'numero_segnalazioni' => 'nullable|integer|min:1',
            'prima_segnalazione' => 'nullable|date|before_or_equal:today',
            'ultima_segnalazione' => 'nullable|date|before_or_equal:today',
        ]);

        // Aggiorna il modificatore
        $validated['modificato_da'] = Auth::id();

        $malfunzionamento->update($validated);

        \Log::info('Malfunzionamento aggiornato', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'prodotto_id' => $prodotto->id,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('malfunzionamenti.show', [$prodotto, $malfunzionamento])
            ->with('success', 'Malfunzionamento aggiornato con successo');
    }

    /**
     * Elimina malfunzionamento
     */
    public function destroy(Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        $titolo = $malfunzionamento->titolo;
        $malfunzionamento->delete();

        \Log::info('Malfunzionamento eliminato', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'titolo' => $titolo,
            'prodotto_id' => $prodotto->id,
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('malfunzionamenti.index', $prodotto)
            ->with('success', 'Malfunzionamento eliminato con successo');
    }

   /**
 * API per ricerca malfunzionamenti (AJAX)
 * Utilizzata dalla dashboard per ricerca avanzata
 */
public function apiSearch(Request $request)
{
    // Verifica autorizzazioni
    if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
        return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
    }
    
    // Validazione input
    $request->validate([
        'q' => 'nullable|string|min:2|max:100',
        'gravita' => 'nullable|in:bassa,media,alta,critica',
        'difficolta' => 'nullable|in:facile,media,difficile,esperto',
        'order' => 'nullable|in:gravita,frequenza,recente,difficolta',
        'limit' => 'nullable|integer|min:1|max:50'
    ]);
    
    try {
        $searchTerm = $request->input('q');
        $gravita = $request->input('gravita');
        $difficolta = $request->input('difficolta');
        $order = $request->input('order', 'gravita');
        $limit = $request->input('limit', 20);
        
        // Query base
        $query = Malfunzionamento::query();
        
        // Ricerca full-text se presente termine
        if ($searchTerm) {
            $query->ricerca($searchTerm);
        }
        
        // Filtri
        if ($gravita) {
            $query->where('gravita', $gravita);
        }
        
        if ($difficolta) {
            $query->where('difficolta', $difficolta);
        }
        
        // Ordinamento
        switch ($order) {
            case 'gravita':
                $query->ordinatoPerGravita();
                break;
            case 'frequenza':
                $query->ordinatoPerFrequenza();
                break;
            case 'recente':
                $query->orderBy('ultima_segnalazione', 'desc');
                break;
            case 'difficolta':
                $query->orderByRaw("FIELD(difficolta, 'esperto', 'difficile', 'media', 'facile')");
                break;
            default:
                $query->ordinatoPerGravita();
        }
        
        // Esegui query con relazioni
        $malfunzionamenti = $query->with(['prodotto:id,nome,modello,categoria'])
            ->select('id', 'prodotto_id', 'titolo', 'descrizione', 'gravita', 'difficolta', 'numero_segnalazioni', 'tempo_stimato')
            ->limit($limit)
            ->get();
        
        // Trasforma risultati per JSON
        $results = $malfunzionamenti->map(function($malfunzionamento) {
            return [
                'id' => $malfunzionamento->id,
                'titolo' => $malfunzionamento->titolo,
                'descrizione' => Str::limit($malfunzionamento->descrizione, 100),
                'gravita' => $malfunzionamento->gravita,
                'difficolta' => $malfunzionamento->difficolta,
                'segnalazioni' => $malfunzionamento->numero_segnalazioni ?? 0,
                'tempo_stimato' => $malfunzionamento->tempo_stimato,
                'prodotto_nome' => $malfunzionamento->prodotto->nome,
                'prodotto_modello' => $malfunzionamento->prodotto->modello,
                'url' => route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento])
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $results,
            'total' => $results->count(),
            'filters' => [
                'search' => $searchTerm,
                'gravita' => $gravita,
                'difficolta' => $difficolta,
                'order' => $order
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Errore API ricerca malfunzionamenti', [
            'error' => $e->getMessage(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Errore durante la ricerca'
        ], 500);
    }
}

/**
 * API per malfunzionamenti di un prodotto specifico (AJAX)
 */
public function apiByProdotto(Request $request, Prodotto $prodotto)
{
    if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
        return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
    }
    
    try {
        $query = $prodotto->malfunzionamenti();
        
        // Filtri opzionali
        if ($request->filled('gravita')) {
            $query->where('gravita', $request->input('gravita'));
        }
        
        if ($request->filled('difficolta')) {
            $query->where('difficolta', $request->input('difficolta'));
        }
        
        // Ordinamento (default per gravità)
        $order = $request->input('order', 'gravita');
        switch ($order) {
            case 'frequenza':
                $query->ordinatoPerFrequenza();
                break;
            case 'recente':
                $query->orderBy('updated_at', 'desc');
                break;
            default:
                $query->ordinatoPerGravita();
        }
        
        $malfunzionamenti = $query->select('id', 'titolo', 'descrizione', 'gravita', 'difficolta', 'numero_segnalazioni', 'tempo_stimato')
            ->limit(20)
            ->get();
        
        $data = $malfunzionamenti->map(function($m) use ($prodotto) {
            return [
                'id' => $m->id,
                'titolo' => $m->titolo,
                'descrizione' => Str::limit($m->descrizione, 80),
                'gravita' => $m->gravita,
                'difficolta' => $m->difficolta,
                'segnalazioni' => $m->numero_segnalazioni ?? 0,
                'tempo_stimato' => $m->tempo_stimato,
                'url' => route('malfunzionamenti.show', [$prodotto, $m])
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'prodotto' => [
                'id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'modello' => $prodotto->modello
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Errore API malfunzionamenti prodotto', [
            'prodotto_id' => $prodotto->id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Errore nel caricamento dei malfunzionamenti'
        ], 500);
    }
}

/**
 * API per segnalare un malfunzionamento (AJAX)
 * Permette ai tecnici di incrementare il contatore segnalazioni
 */
public function apiSegnala(Request $request, Malfunzionamento $malfunzionamento)
{
    if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
        return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
    }
    
    try {
        // Incrementa segnalazioni e aggiorna data
        $malfunzionamento->increment('numero_segnalazioni');
        $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);
        
        // Log dell'azione
        \Log::info('Segnalazione malfunzionamento via API', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'nuovo_count' => $malfunzionamento->numero_segnalazioni,
            'segnalato_da' => Auth::id(),
            'ip' => $request->ip()
        ]);
        
        return response()->json([
            'success' => true,
            'nuovo_count' => $malfunzionamento->numero_segnalazioni,
            'message' => 'Segnalazione registrata con successo',
            'data' => [
                'id' => $malfunzionamento->id,
                'titolo' => $malfunzionamento->titolo,
                'segnalazioni' => $malfunzionamento->numero_segnalazioni,
                'ultima_segnalazione' => $malfunzionamento->ultima_segnalazione
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Errore API segnalazione malfunzionamento', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Errore durante la segnalazione'
        ], 500);
    }
}

/**
 * API per esportazione malfunzionamenti (solo admin)
 */
public function apiExport(Request $request)
{
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
    }
    
    try {
        $malfunzionamenti = Malfunzionamento::with(['prodotto:id,nome,categoria', 'creatoBy:id,nome,cognome'])
            ->orderBy('gravita')
            ->orderBy('numero_segnalazioni', 'desc')
            ->get();
        
        $export = $malfunzionamenti->map(function($m) {
            return [
                'id' => $m->id,
                'titolo' => $m->titolo,
                'prodotto' => $m->prodotto->nome,
                'categoria_prodotto' => $m->prodotto->categoria,
                'gravita' => $m->gravita,
                'difficolta' => $m->difficolta,
                'segnalazioni' => $m->numero_segnalazioni ?? 0,
                'tempo_stimato' => $m->tempo_stimato,
                'creato_da' => $m->creatoBy?->nome_completo ?? 'N/A',
                'prima_segnalazione' => $m->prima_segnalazione?->format('d/m/Y'),
                'ultima_segnalazione' => $m->ultima_segnalazione?->format('d/m/Y'),
                'created_at' => $m->created_at->format('d/m/Y H:i')
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $export,
            'filename' => 'malfunzionamenti_export_' . now()->format('Y-m-d_H-i-s') . '.json'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Errore durante l\'esportazione'
        ], 500);
    }
}

    /**
     * Incrementa il numero di segnalazioni per un malfunzionamento
     * Utile quando un tecnico conferma di aver riscontrato lo stesso problema
     */
    public function incrementSegnalazioni(Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        // Incrementa le segnalazioni e aggiorna la data ultima segnalazione
        $malfunzionamento->increment('numero_segnalazioni');
        $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

        \Log::info('Segnalazione malfunzionamento incrementata', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'nuovo_count' => $malfunzionamento->numero_segnalazioni,
            'segnalato_da' => Auth::id()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'message' => 'Segnalazione registrata'
            ]);
        }

        return back()->with('success', 'Segnalazione registrata. Totale: ' . $malfunzionamento->numero_segnalazioni);
    }

    /**
     * Dashboard malfunzionamenti per staff
     * Mostra panoramica generale dei problemi più frequenti
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Accesso riservato allo staff');
        }

        // Statistiche generali
        $stats = [
            'totale_malfunzionamenti' => Malfunzionamento::count(),
            'critici' => Malfunzionamento::where('gravita', 'critica')->count(),
            'alta_priorita' => Malfunzionamento::where('gravita', 'alta')->count(),
            'creati_questo_mese' => Malfunzionamento::whereMonth('created_at', now()->month)->count(),
        ];

        // Malfunzionamenti più frequenti
        $piu_frequenti = Malfunzionamento::with('prodotto')
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(10)
            ->get();

        // Malfunzionamenti critici recenti
        $critici_recenti = Malfunzionamento::where('gravita', 'critica')
            ->with('prodotto')
            ->orderBy('ultima_segnalazione', 'desc')
            ->limit(5)
            ->get();

        // Prodotti con più problemi
        $prodotti_problematici = Prodotto::withCount('malfunzionamenti')
            ->having('malfunzionamenti_count', '>', 0)
            ->orderBy('malfunzionamenti_count', 'desc')
            ->limit(10)
            ->get();

        return view('malfunzionamenti.dashboard', compact(
            'stats', 'piu_frequenti', 'critici_recenti', 'prodotti_problematici'
        ));
    }
}