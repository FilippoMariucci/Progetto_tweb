<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodotto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Controller per la gestione dei prodotti
 * VERSIONE COMPLETA - Sistema categorie unificato implementato
 * 
 * Gestisce tutte le operazioni CRUD sui prodotti con sistema di categorie coerente
 * tra frontend pubblico, area tecnici, area staff e pannello amministratore
 */
class ProdottoController extends Controller
{
    // ================================================
    // METODI PUBBLICI (Livello 1 - Accesso Libero)
    // ================================================

    /**
     * Catalogo pubblico prodotti (senza malfunzionamenti)
     * Accessibile a tutti senza autenticazione
     */
    public function indexPubblico(Request $request)
{
    // Query base per prodotti attivi
    $query = Prodotto::where('attivo', true);

    // === RICERCA TESTUALE CON WILDCARD ===
    if ($request->filled('search')) {
        $searchTerm = $request->input('search');
        
        // Supporto wildcard * alla fine del termine
        if (str_ends_with($searchTerm, '*')) {
            $searchTerm = rtrim($searchTerm, '*');
            $query->where(function($q) use ($searchTerm) {
                $q->where('descrizione', 'LIKE', $searchTerm . '%')
                  ->orWhere('nome', 'LIKE', $searchTerm . '%')
                  ->orWhere('modello', 'LIKE', $searchTerm . '%');
            });
        } else {
            $query->where(function($q) use ($searchTerm) {
                $q->where('descrizione', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nome', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
            });
        }
    }

    // === FILTRO PER CATEGORIA - CORREZIONE ===
    if ($request->filled('categoria')) {
        $query->where('categoria', $request->input('categoria'));
    }

    // Esecuzione query con paginazione - SOLO campi pubblici
    $prodotti = $query->select([
            'id', 'nome', 'modello', 'descrizione', 
            'categoria', 'prezzo', 'foto'
        ])
        ->orderBy('nome')
        ->paginate(12);

    // === CORREZIONE CATEGORIE ===
    // Ottieni elenco categorie dalla query diretta sui prodotti ATTIVI
    $categorieFromDB = Prodotto::where('attivo', true)
        ->distinct()
        ->whereNotNull('categoria')
        ->orderBy('categoria')
        ->pluck('categoria')
        ->toArray();

    // Crea array finale delle categorie per la vista
    $categorie = $categorieFromDB;

    // === CORREZIONE STATISTICHE PER CATEGORIA ===
    // Calcola conteggi per ogni categoria
    $perCategoriaStats = Prodotto::where('attivo', true)
        ->groupBy('categoria')
        ->selectRaw('categoria, COUNT(*) as count')
        ->pluck('count', 'categoria')
        ->toArray();

    // Statistiche pubbliche (SENZA malfunzionamenti)
    $stats = [
        'total_prodotti' => Prodotto::where('attivo', true)->count(),
        'categorie_count' => count($categorie),
        'per_categoria' => $perCategoriaStats, // AGGIUNTO per i badge
        'version' => 'pubblico'
    ];

    // Log per debugging
    Log::info('Catalogo pubblico caricato', [
        'search_term' => $request->input('search'),
        'categoria_filtro' => $request->input('categoria'),
        'prodotti_totali' => $prodotti->total(),
        'categorie_trovate' => count($categorie),
        'categorie_list' => $categorie,
        'stats_per_categoria' => $perCategoriaStats
    ]);

    return view('prodotti.pubblico.index', compact('prodotti', 'categorie', 'stats'))
        ->with('isPublicView', true)
        ->with('showMalfunzionamenti', false);
}

    /**
     * Scheda prodotto pubblica - NO malfunzionamenti
     */
    public function showPubblico(Prodotto $prodotto)
    {
        // Verifica che il prodotto sia attivo per il pubblico
        if (!$prodotto->attivo) {
            abort(404, 'Prodotto non disponibile');
        }

        // Carica solo informazioni base (NO malfunzionamenti per il pubblico)
        $prodotto->load(['staffAssegnato:id,nome,cognome']);

        // IMPORTANTE: Flag per nascondere malfunzionamenti nella vista esistente
        $showMalfunzionamenti = false;
        $isPublicView = true; 
        
        // Log per debugging
        Log::info('Prodotto visualizzato da utente pubblico', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'ip' => request()->ip()
        ]);
        
        return view('prodotti.pubblico.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
    }

    // ================================================
    // METODI PER CATALOGO COMPLETO (Livello 2+)
    // ================================================

    /**
     * Catalogo completo per tecnici - CON malfunzionamenti
     * CORREZIONE: Usa sistema categorie unificato
     */
    public function indexCompleto(Request $request)
    {
        // Verifica autorizzazione tecnici
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici autorizzati');
        }

        $query = Prodotto::where('attivo', true);
        $user = Auth::user();

        // === FILTRO SPECIALE PER STAFF: SOLO PRODOTTI ASSEGNATI ===
        if ($request->filled('staff_filter') && $request->input('staff_filter') === 'my_products') {
            if ($user->isStaff()) {
                $query->where('staff_assegnato_id', $user->id);
            } else {
                return redirect()->route('prodotti.completo.index')
                    ->with('warning', 'Filtro "Miei Prodotti" disponibile solo per lo staff aziendale');
            }
        }

        // === FILTRO PER PRODOTTI CRITICI ===
        if ($request->filled('filter') && $request->input('filter') === 'critici') {
            $query->whereHas('malfunzionamenti', function($q) {
                $q->where('gravita', 'critica');
            });
        }

        // Ricerca avanzata per tecnici con supporto wildcard
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            if (str_ends_with($searchTerm, '*')) {
                $searchTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', $searchTerm . '%')
                      ->orWhere('nome', 'LIKE', $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', $searchTerm . '%');
                });
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('nome', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // CORREZIONE: Filtro per categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // === SE L'UTENTE È STAFF, mostra prima i suoi prodotti ===
        if ($user->isStaff() && !$request->filled('staff_filter')) {
            $query->orderByRaw("CASE WHEN staff_assegnato_id = ? THEN 0 ELSE 1 END", [$user->id]);
        }

        // Carica prodotti con conteggio malfunzionamenti (FONDAMENTALE per tecnici)
        $prodotti = $query->withCount([
                'malfunzionamenti',
                'malfunzionamenti as critici_count' => function($query) {
                    $query->where('gravita', 'critica');
                }
            ])
            ->with('staffAssegnato:id,nome,cognome')
            ->orderBy('nome')
            ->paginate(12);

        // CORREZIONE: Usa sistema categorie unificato
        $categorie = $this->getCategorie();

        // Statistiche avanzate per tecnici
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
            'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
            'version' => 'completo'
        ];

        // Statistiche aggiuntive per lo staff
        if ($user->isStaff()) {
            $stats['miei_prodotti'] = Prodotto::where('staff_assegnato_id', $user->id)
                ->where('attivo', true)
                ->count();
        }

        // Determina quale vista usare in base al filtro
        $view = $request->input('staff_filter') === 'my_products' ? 'prodotti.staff.index' : 'prodotti.completo.index';
        
        // Fallback alla vista completa se quella specifica non esiste
        if (!view()->exists($view)) {
            $view = 'prodotti.completo.index';
        }

        return view($view, compact('prodotti', 'categorie', 'stats'))
            ->with('showMalfunzionamenti', true)
            ->with('isPublicView', false);
    }

    /**
     * Visualizzazione completa prodotto per tecnici - CON malfunzionamenti
     */
    public function showCompleto(Prodotto $prodotto)
    {
        // Verifica autorizzazione tecnici
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici autorizzati');
        }

        if (!$prodotto->attivo) {
            abort(404, 'Prodotto non disponibile');
        }

        // Carica TUTTE le relazioni inclusi i malfunzionamenti
        $prodotto->load([
            'malfunzionamenti' => function($query) {
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
                      ->orderBy('numero_segnalazioni', 'desc');
            },
            'malfunzionamenti.creatoBy:id,nome,cognome',
            'staffAssegnato:id,nome,cognome'
        ]);

        // Flag per mostrare malfunzionamenti nella vista tecnica
        $showMalfunzionamenti = true;
        $isPublicView = false;

        // Log accesso tecnico
        Log::info('Prodotto visualizzato da tecnico', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'user_id' => Auth::id(),
            'malfunzionamenti_count' => $prodotto->malfunzionamenti->count()
        ]);

        return view('prodotti.completo.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
    }

    // ================================================
    // RICERCA AVANZATA PER TECNICI
    // ================================================

    /**
     * Ricerca avanzata nei prodotti per tecnici (Livello 2+)
     */
    public function ricercaAvanzata(Request $request)
    {
        // Verifica autorizzazioni tecnici
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici e staff');
        }

        try {
            // Query base per prodotti attivi
            $query = Prodotto::where('attivo', true);

            // === RICERCA CON WILDCARD ===
            if ($request->filled('search')) {
                $searchTerm = trim($request->input('search'));
                
                if (str_ends_with($searchTerm, '*')) {
                    // Ricerca wildcard: "lav*" trova lavatrici, lavastoviglie, etc.
                    $baseTerm = rtrim($searchTerm, '*');
                    $query->where(function($q) use ($baseTerm) {
                        $q->where('nome', 'LIKE', $baseTerm . '%')
                          ->orWhere('descrizione', 'LIKE', $baseTerm . '%')
                          ->orWhere('modello', 'LIKE', $baseTerm . '%')
                          ->orWhere('categoria', 'LIKE', $baseTerm . '%');
                    });
                } else {
                    // Ricerca normale full-text
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('categoria', 'LIKE', '%' . $searchTerm . '%');
                    });
                }
            }

            // Filtro per categoria
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }

            // Filtro per problemi critici
            if ($request->boolean('critici_only')) {
                $query->whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                });
            }

            // Carica prodotti con conteggio malfunzionamenti
            $prodotti = $query->withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }
                ])
                ->with('staffAssegnato:id,nome,cognome')
                ->orderBy('critici_count', 'desc')
                ->orderBy('malfunzionamenti_count', 'desc')
                ->orderBy('nome', 'asc')
                ->paginate(15)
                ->withQueryString();

            // Statistiche per la ricerca
            $stats = [
                'total_prodotti' => Prodotto::where('attivo', true)->count(),
                'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
                'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
                'risultati_trovati' => $prodotti->total(),
                'version' => 'ricerca_avanzata'
            ];

            // Categorie per filtro
            $categorie = $this->getCategorie();

            // Log per debugging
            Log::info('Ricerca avanzata completata', [
                'search_term' => $request->input('search'),
                'results_count' => $prodotti->total(),
                'user_id' => Auth::id()
            ]);

            return view('prodotti.completo.index', compact('prodotti', 'stats', 'categorie'))
                ->with('showMalfunzionamenti', true)
                ->with('isPublicView', false)
                ->with('isSearchResults', true);

        } catch (\Exception $e) {
            Log::error('Errore nella ricerca avanzata prodotti', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'search_params' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('prodotti.completo.index')
                ->with('error', 'Errore durante la ricerca: ' . $e->getMessage());
        }
    }

    // ================================================
    // METODI AMMINISTRATIVI - CORREZIONE SISTEMA CATEGORIE
    // ================================================

    /**
     * Lista prodotti per amministratori - CORREZIONE CATEGORIE
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        $query = Prodotto::query();

        // Filtri admin
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'attivi') {
                $query->where('attivo', true);
            } elseif ($status === 'inattivi') {
                $query->where('attivo', false);
            }
        }

        // Filtro per staff assegnato
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            
            if ($staffId === '0' || $staffId === 0) {
                $query->whereNull('staff_assegnato_id');
            } else {
                $query->where('staff_assegnato_id', $staffId);
            }
        }

        // CORREZIONE: Filtro per categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // Filtro ricerca
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Ordinamento
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Esecuzione query admin
        $prodotti = $query->withCount('malfunzionamenti')
            ->with('staffAssegnato:id,nome,cognome')
            ->paginate(15)
            ->withQueryString();

        // Staff members per filtro
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // CORREZIONE: Aggiungi categorie per filtro
        $categorie = $this->getCategorie();

        // Statistiche admin
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'attivi' => Prodotto::where('attivo', true)->count(),
            'inattivi' => Prodotto::where('attivo', false)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->count(),
            'non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(),
        ];

        return view('admin.prodotti.index', compact('prodotti', 'staffMembers', 'stats', 'categorie'));
    }

    /**
     * Visualizza prodotti per categoria specifica - CORREZIONE
     */
    public function categoria($categoria)
    {
        // Verifica che la categoria sia valida usando il sistema unificato
        $categorieDisponibili = $this->getCategorie();
        if (!array_key_exists($categoria, $categorieDisponibili)) {
            abort(404, 'Categoria non trovata');
        }

        // Query base
        $query = Prodotto::where('categoria', $categoria)->where('attivo', true);
        
        // Determina se l'utente può vedere malfunzionamenti
        $canViewMalfunctions = Auth::check() && Auth::user()->canViewMalfunzionamenti();
        
        if ($canViewMalfunctions) {
            // Per tecnici: includi conteggi malfunzionamenti
            $prodotti = $query->withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }
                ])
                ->with('staffAssegnato:id,nome,cognome')
                ->paginate(12);
        } else {
            // Per pubblico: solo dati base
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->paginate(12);
        }
        
        // CORREZIONE: Usa sistema categorie unificato
        $categorie = $categorieDisponibili;

        $stats = [
            'total_prodotti' => $prodotti->total(),
            'categoria_corrente' => $categoria,
            'categoria_label' => $categorieDisponibili[$categoria],
            'version' => $canViewMalfunctions ? 'completo' : 'pubblico'
        ];

        // Usa vista appropriata
        $view = $canViewMalfunctions ? 'prodotti.completo.index' : 'prodotti.pubblico.index';

        return view($view, compact('prodotti', 'categorie', 'stats'))
            ->with('showMalfunzionamenti', $canViewMalfunctions)
            ->with('isPublicView', !$canViewMalfunctions);
    }

    // ================================================
    // METODI CRUD AMMINISTRATIVI - SISTEMA CATEGORIE UNIFICATO
    // ================================================

    /**
     * Form creazione nuovo prodotto - SISTEMA CATEGORIE UNIFICATO
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a creare prodotti');
        }

        // Staff members per assegnazione
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // CORREZIONE: Usa il sistema unificato delle categorie
        $categorie = Prodotto::getCategorieUnifico();

        // Log per debugging
        Log::info('Form creazione prodotto caricato', [
            'admin_id' => Auth::id(),
            'staff_disponibili' => $staffMembers->count(),
            'categorie_disponibili' => count($categorie)
        ]);

        return view('admin.prodotti.create', compact('staffMembers', 'categorie'));
    }

    /**
     * Salva nuovo prodotto
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'modello' => 'required|string|max:255|unique:prodotti',
            'descrizione' => 'required|string',
            'categoria' => 'required|string|max:100',
            'note_tecniche' => 'required|string',
            'modalita_installazione' => 'required|string',
            'modalita_uso' => 'nullable|string',
            'prezzo' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'staff_assegnato_id' => 'nullable|exists:users,id',
        ], [
            'nome.required' => 'Il nome del prodotto è obbligatorio',
            'modello.required' => 'Il modello è obbligatorio',
            'modello.unique' => 'Questo modello esiste già',
            'categoria.required' => 'La categoria è obbligatoria',
            'note_tecniche.required' => 'Le note tecniche sono obbligatorie',
            'modalita_installazione.required' => 'Le modalità di installazione sono obbligatorie',
            'foto.image' => 'Il file deve essere un\'immagine',
            'foto.max' => 'L\'immagine non può superare 2MB',
        ]);

        // Verifica che la categoria sia valida
        $categorieDisponibili = Prodotto::getCategorieUnifico();
        if (!array_key_exists($validated['categoria'], $categorieDisponibili)) {
            return back()->withErrors([
                'categoria' => 'La categoria selezionata non è valida.'
            ])->withInput();
        }

        // Gestione upload foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('prodotti', 'public');
        }

        // Creazione prodotto
        $prodotto = Prodotto::create(array_merge($validated, [
            'attivo' => true
        ]));

        Log::info('Nuovo prodotto creato', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'categoria' => $prodotto->categoria,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', 'Prodotto "' . $prodotto->nome . '" creato con successo');
    }

    /**
     * Form modifica prodotto - SISTEMA CATEGORIE UNIFICATO
     */
    public function edit(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a modificare prodotti');
        }

        // Staff members per assegnazione
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // CORREZIONE: Usa il sistema unificato delle categorie
        $categorie = Prodotto::getCategorieUnifico();

        // Log per debugging
        Log::info('Form modifica prodotto caricato', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'categoria_attuale' => $prodotto->categoria,
            'admin_id' => Auth::id(),
            'categorie_disponibili' => count($categorie)
        ]);

        return view('admin.prodotti.edit', compact('prodotto', 'staffMembers', 'categorie'));
    }

    /**
     * Aggiorna prodotto esistente
     */
    public function update(Request $request, Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'modello' => 'required|string|max:255|unique:prodotti,modello,' . $prodotto->id,
            'descrizione' => 'required|string',
            'categoria' => 'required|string|max:100',
            'note_tecniche' => 'required|string',
            'modalita_installazione' => 'required|string',
            'modalita_uso' => 'nullable|string',
            'prezzo' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'staff_assegnato_id' => 'nullable|exists:users,id',
            'attivo' => 'boolean',
        ]);

        // Verifica che la categoria sia valida
        $categorieDisponibili = Prodotto::getCategorieUnifico();
        if (!array_key_exists($validated['categoria'], $categorieDisponibili)) {
            return back()->withErrors([
                'categoria' => 'La categoria selezionata non è valida.'
            ])->withInput();
        }

        // Gestione upload nuova foto
        if ($request->hasFile('foto')) {
            if ($prodotto->foto) {
                Storage::disk('public')->delete($prodotto->foto);
            }
            $validated['foto'] = $request->file('foto')->store('prodotti', 'public');
        }

        $prodotto->update($validated);

        Log::info('Prodotto aggiornato', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'categoria' => $prodotto->categoria,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', 'Prodotto "' . $prodotto->nome . '" aggiornato con successo');
    }

    /**
     * Visualizza singolo prodotto per admin
     */
    public function show(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        $prodotto->load([
            'malfunzionamenti.creatoBy:id,nome,cognome',
            'malfunzionamenti.modificatoBy:id,nome,cognome',
            'staffAssegnato:id,nome,cognome'
        ]);

        return view('admin.prodotti.show', compact('prodotto'));
    }

    /**
 * CORREZIONE: Metodo destroy nel ProdottoController
 * 
 * PROBLEMA: Il metodo attuale restituisce redirect() invece di JSON per AJAX
 * SOLUZIONE: Detectare se è una richiesta AJAX e restituire risposta appropriata
 */

/**
 * Elimina prodotto (supporta sia richieste WEB che AJAX)
 * 
 * Metodo CORRETTO che gestisce entrambi i tipi di richiesta:
 * - AJAX: restituisce JSON response
 * - WEB: restituisce redirect tradizionale
 */

public function destroy(Prodotto $prodotto)
{
    // Verifica autorizzazioni - solo admin possono eliminare prodotti
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        abort(403, 'Non autorizzato ad eliminare prodotti');
    }

    try {
        // Salva informazioni per logging prima dell'eliminazione
        $prodottoNome = $prodotto->nome;
        $prodottoModello = $prodotto->modello;
        $prodottoId = $prodotto->id;

        // === GESTIONE FILE IMMAGINE ===
        // Elimina la foto del prodotto se esiste
        if ($prodotto->foto) {
            Storage::disk('public')->delete($prodotto->foto);
            Log::info('Foto prodotto eliminata', [
                'prodotto_id' => $prodottoId,
                'foto_path' => $prodotto->foto
            ]);
        }

        // === GESTIONE RELAZIONI ===
        // Elimina tutti i malfunzionamenti associati al prodotto
        // IMPORTANTE: Questo elimina anche le soluzioni associate
        $malfunzionamentiCount = $prodotto->malfunzionamenti()->count();
        if ($malfunzionamentiCount > 0) {
            $prodotto->malfunzionamenti()->delete();
            Log::info('Malfunzionamenti del prodotto eliminati', [
                'prodotto_id' => $prodottoId,
                'malfunzionamenti_eliminati' => $malfunzionamentiCount
            ]);
        }

        // === ELIMINAZIONE PRODOTTO ===
        // Elimina definitivamente il prodotto dal database
        $prodotto->delete();

        // Log dell'operazione per audit
        Log::warning('Prodotto eliminato definitivamente', [
            'prodotto_id' => $prodottoId,
            'prodotto_nome' => $prodottoNome,
            'prodotto_modello' => $prodottoModello,
            'deleted_by_admin_id' => Auth::id(),
            'deleted_by_admin_username' => Auth::user()->username,
            'malfunzionamenti_eliminati' => $malfunzionamentiCount
        ]);

        // Redirect con messaggio di successo
        return redirect()->route('admin.prodotti.index')
            ->with('success', "Prodotto \"{$prodottoNome}\" eliminato definitivamente dal sistema.");

    } catch (\Exception $e) {
        // Log dell'errore per debugging
        Log::error('Errore nell\'eliminazione prodotto', [
            'prodotto_id' => $prodotto->id,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'admin_id' => Auth::id()
        ]);

        // Redirect con messaggio di errore
        return back()->withErrors([
            'delete' => 'Errore nell\'eliminazione del prodotto. Riprova o contatta l\'amministratore.'
        ]);
    }
}

/**
 * OPZIONALE: Metodo per soft delete (disattivazione)
 * Utile se vuoi mantenere anche l'opzione di disattivazione
 */
public function softDestroy(Prodotto $prodotto)
{
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        abort(403, 'Non autorizzato');
    }

    // Disattiva il prodotto invece di eliminarlo
    $prodotto->update(['attivo' => false]);

    Log::info('Prodotto disattivato (soft delete)', [
        'prodotto_id' => $prodotto->id,
        'modello' => $prodotto->modello,
        'deactivated_by' => Auth::id()
    ]);

    return redirect()->route('admin.prodotti.index')
        ->with('success', 'Prodotto "' . $prodotto->nome . '" rimosso dal catalogo (può essere riattivato)');
}



/**
 * METODO AGGIUNTIVO: Conferma eliminazione (per approccio alternativo)
 * 
 * Questo metodo può essere utilizzato per mostrare una pagina di conferma
 * prima dell'eliminazione definitiva (approccio più sicuro)
 */
public function confirmDelete(Prodotto $prodotto)
{
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        abort(403, 'Non autorizzato');
    }

    // Informazioni aggiuntive per la conferma
    $relatedData = [
        'malfunzionamenti_count' => $prodotto->malfunzionamenti()->count(),
        'staff_assegnato' => $prodotto->staffAssegnato,
        'created_at' => $prodotto->created_at,
        'last_modified' => $prodotto->updated_at
    ];

    return view('admin.prodotti.confirm-delete', compact('prodotto', 'relatedData'));
}

/**
 * METODO AGGIUNTIVO: Ripristino prodotto eliminato
 * 
 * Permette di riattivare un prodotto precedentemente disattivato
 */
public function restore(Prodotto $prodotto)
{
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }
        abort(403, 'Non autorizzato');
    }

    try {
        // Riattiva il prodotto
        $prodotto->update(['attivo' => true]);

        Log::info('Prodotto ripristinato', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'restored_by' => Auth::id(),
            'timestamp' => now()
        ]);

        $successMessage = "Prodotto \"{$prodotto->nome}\" ripristinato nel catalogo";

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'status' => 'active',
                    'restored_at' => now()->toISOString()
                ]
            ]);
        }

        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        Log::error('Errore ripristino prodotto', [
            'prodotto_id' => $prodotto->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id()
        ]);

        $errorMessage = 'Errore durante il ripristino del prodotto';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage);
    }
}

/**
 * METODO AGGIUNTIVO: Eliminazione fisica (definitiva)
 * 
 * Per eliminare definitivamente un prodotto dal database
 * ATTENZIONE: Usare con cautela, elimina anche i malfunzionamenti associati
 */
public function forceDelete(Prodotto $prodotto)
{
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }
        abort(403, 'Non autorizzato');
    }

    try {
        DB::beginTransaction();

        // Salva info prima dell'eliminazione
        $prodottoInfo = [
            'id' => $prodotto->id,
            'nome' => $prodotto->nome,
            'modello' => $prodotto->modello
        ];

        // Elimina malfunzionamenti associati
        $malfunzionamentiCount = $prodotto->malfunzionamenti()->count();
        $prodotto->malfunzionamenti()->delete();

        // Elimina foto se esiste
        if ($prodotto->foto) {
            Storage::disk('public')->delete($prodotto->foto);
        }

        // Eliminazione fisica dal database
        $prodotto->delete();

        DB::commit();

        Log::warning('Prodotto eliminato DEFINITIVAMENTE', [
            'prodotto_info' => $prodottoInfo,
            'malfunzionamenti_eliminati' => $malfunzionamentiCount,
            'eliminated_by' => Auth::id(),
            'admin_name' => Auth::user()->nome_completo,
            'timestamp' => now()
        ]);

        $successMessage = "Prodotto \"{$prodottoInfo['nome']}\" eliminato definitivamente dal sistema";

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => [
                    'eliminated_product' => $prodottoInfo,
                    'related_data_removed' => $malfunzionamentiCount
                ]
            ]);
        }

        return redirect()->route('admin.prodotti.index')
            ->with('warning', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Errore eliminazione definitiva prodotto', [
            'prodotto_id' => $prodotto->id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id()
        ]);

        $errorMessage = 'Errore durante l\'eliminazione definitiva';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage);
    }
}

    /**
     * Toggle status prodotto via AJAX
     */
    public function toggleStatus(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            $newStatus = !$prodotto->attivo;
            $prodotto->update(['attivo' => $newStatus]);

            $action = $newStatus ? 'attivato' : 'disattivato';

            Log::info("Prodotto {$action} dall'admin", [
                'prodotto_id' => $prodotto->id,
                'modello' => $prodotto->modello,
                'new_status' => $newStatus,
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('admin.prodotti.index')
            ->with('success', 'Prodotto "' . $prodotto->nome . '" aggiornato con successo');

        } catch (\Exception $e) {
            Log::error('Errore toggle status prodotto', [
                'prodotto_id' => $prodotto->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel cambio stato del prodotto.',
                
            ], 500);
            
        }
    }

    /**
 * METODO CORRETTO: Azioni bulk sui prodotti
 * Gestisce attivazione, disattivazione ed eliminazione multipla
 */
public function bulkAction(Request $request)
{
    // Verifica autorizzazioni admin
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        return response()->json([
            'success' => false,
            'message' => 'Non autorizzato ad eseguire questa azione'
        ], 403);
    }

    // Validazione input
    $validated = $request->validate([
        'action' => 'required|in:activate,deactivate,delete',
        'products' => 'required|array|min:1|max:50', // Limite per sicurezza
        'products.*' => 'required|integer|exists:prodotti,id'
    ], [
        'action.required' => 'Azione non specificata',
        'action.in' => 'Azione non valida',
        'products.required' => 'Nessun prodotto selezionato',
        'products.min' => 'Seleziona almeno un prodotto',
        'products.max' => 'Troppi prodotti selezionati (max 50)',
        'products.*.exists' => 'Uno o più prodotti non esistono'
    ]);

    try {
        $productIds = $validated['products'];
        $action = $validated['action'];
        $count = 0;
        $message = '';

        // Log inizio operazione
        Log::info('Inizio azione bulk sui prodotti', [
            'action' => $action,
            'product_ids' => $productIds,
            'admin_id' => Auth::id(),
            'admin_username' => Auth::user()->username
        ]);

        switch ($action) {
            case 'activate':
                $count = Prodotto::whereIn('id', $productIds)
                    ->where('attivo', false) // Solo quelli disattivati
                    ->update(['attivo' => true]);
                $message = "Attivati {$count} prodotti con successo";
                break;

            case 'deactivate':
                $count = Prodotto::whereIn('id', $productIds)
                    ->where('attivo', true) // Solo quelli attivi
                    ->update(['attivo' => false]);
                $message = "Disattivati {$count} prodotti con successo";
                break;

            case 'delete':
                // Per eliminazione: prima disattiva, poi elimina
                $prodottiDaEliminare = Prodotto::whereIn('id', $productIds)->get();
                
                foreach ($prodottiDaEliminare as $prodotto) {
                    // Elimina eventuali file immagine
                    if ($prodotto->foto && Storage::disk('public')->exists($prodotto->foto)) {
                        Storage::disk('public')->delete($prodotto->foto);
                    }
                    
                    // Elimina malfunzionamenti associati
                    $prodotto->malfunzionamenti()->delete();
                    
                    // Elimina il prodotto
                    $prodotto->delete();
                    $count++;
                }
                
                $message = "Eliminati {$count} prodotti definitivamente";
                break;
        }

        // Log successo operazione
        Log::warning('Azione bulk completata', [
            'action' => $action,
            'products_affected' => $count,
            'total_requested' => count($productIds),
            'admin_id' => Auth::id(),
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => $count,
            'action' => $action,
            'timestamp' => now()->toISOString()
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Dati non validi',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Errore in azione bulk prodotti', [
            'action' => $request->input('action'),
            'product_ids' => $request->input('products', []),
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore durante l\'esecuzione dell\'operazione. Riprova.',
            'error_details' => app()->environment('local') ? $e->getMessage() : null
        ], 500);
    }
}

    // ================================================
    // API ENDPOINTS - SISTEMA CATEGORIE UNIFICATO
    // ================================================

    /**
     * API per ricerca prodotti AJAX (pubblico) - CORREZIONE CATEGORIE
     */
    public function apiSearch(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:1|max:100',
            ]);

            $searchTerm = trim($request->input('q'));
            $query = Prodotto::where('attivo', true);

            // Implementa ricerca con wildcard
            if (str_ends_with($searchTerm, '*')) {
                $searchTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', $searchTerm . '%');
                });
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            // Esegui query con limit
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->limit(20)
                ->get();

            // CORREZIONE: Usa sistema categorie unificato per le etichette
            $categorieLabels = $this->getCategorie();

            // Formatta risultati per API
            $results = $prodotti->map(function($prodotto) use ($categorieLabels) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'descrizione' => \Illuminate\Support\Str::limit($prodotto->descrizione, 100),
                    'categoria' => $prodotto->categoria,
                    'categoria_label' => $categorieLabels[$prodotto->categoria] ?? ucfirst(str_replace('_', ' ', $prodotto->categoria)),
                    'prezzo' => $prodotto->prezzo ? '€ ' . number_format($prodotto->prezzo, 2, ',', '.') : null,
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'url' => route('prodotti.show', $prodotto->id)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $results->count(),
                'search_term' => $request->input('q'),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore in apiSearch', [
                'error' => $e->getMessage(),
                'search_term' => $request->input('q', 'N/A')
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nella ricerca prodotti'
            ], 500);
        }
    }

    /**
     * API per ricerca tecnica (tecnici e staff) - CORREZIONE CATEGORIE
     */
    public function apiSearchTech(Request $request)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json([
                'success' => false,
                'error' => 'Accesso riservato ai tecnici'
            ], 403);
        }

        try {
            $request->validate([
                'q' => 'required|string|min:1|max:100',
            ]);

            $searchTerm = trim($request->input('q'));
            $query = Prodotto::where('attivo', true);

            // Ricerca con wildcard
            if (str_ends_with($searchTerm, '*')) {
                $searchTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', $searchTerm . '%');
                });
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            // Include conteggio malfunzionamenti per vista tecnica
            $prodotti = $query->withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }
                ])
                ->with('staffAssegnato:id,nome,cognome')
                ->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto', 'staff_assegnato_id'
                ])
                ->orderBy('nome')
                ->limit(20)
                ->get();

            // CORREZIONE: Usa sistema categorie unificato per le etichette
            $categorieLabels = $this->getCategorie();

            // Formatta risultati per tecnici
            $results = $prodotti->map(function($prodotto) use ($categorieLabels) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
                    'categoria_label' => $categorieLabels[$prodotto->categoria] ?? ucfirst(str_replace('_', ' ', $prodotto->categoria)),
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'malfunzionamenti_count' => $prodotto->malfunzionamenti_count,
                    'critici_count' => $prodotto->critici_count,
                    'staff_assegnato' => $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : null,
                    'url' => route('prodotti.completo.show', $prodotto->id)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $results->count(),
                'search_term' => $request->input('q'),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore in apiSearchTech', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nella ricerca tecnica'
            ], 500);
        }
    }

    /**
     * API per lista prodotti pubblici - CORREZIONE CATEGORIE
     */
    public function apiIndexPubblico(Request $request)
    {
        try {
            $query = Prodotto::where('attivo', true);

            // Filtro categoria
            if ($request->filled('categoria')) {
                $categoria = $request->input('categoria');
                
                // Verifica che la categoria sia valida
                $categorieDisponibili = $this->getCategorie();
                if (array_key_exists($categoria, $categorieDisponibili)) {
                    $query->where('categoria', $categoria);
                }
            }

            $perPage = min($request->input('per_page', 12), 50);
            
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->paginate($perPage);

            // CORREZIONE: Usa sistema categorie unificato per le etichette
            $categorieLabels = $this->getCategorie();

            $data = $prodotti->getCollection()->map(function($prodotto) use ($categorieLabels) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
                    'categoria_label' => $categorieLabels[$prodotto->categoria] ?? ucfirst(str_replace('_', ' ', $prodotto->categoria)),
                    'prezzo' => $prodotto->prezzo ? '€ ' . number_format($prodotto->prezzo, 2, ',', '.') : null,
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'url' => route('prodotti.show', $prodotto->id)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $prodotti->currentPage(),
                    'last_page' => $prodotti->lastPage(),
                    'per_page' => $prodotti->perPage(),
                    'total' => $prodotti->total()
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore in apiIndexPubblico', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento prodotti'
            ], 500);
        }
    }

    /**
     * API per singolo prodotto pubblico - CORREZIONE CATEGORIE
     */
    public function apiShowPubblico($id)
    {
        try {
            $prodotto = Prodotto::where('attivo', true)->findOrFail($id);

            // CORREZIONE: Usa sistema categorie unificato per l'etichetta
            $categorieLabels = $this->getCategorie();

            $data = [
                'id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'modello' => $prodotto->modello,
                'descrizione' => $prodotto->descrizione,
                'categoria' => $prodotto->categoria,
                'categoria_label' => $categorieLabels[$prodotto->categoria] ?? ucfirst(str_replace('_', ' ', $prodotto->categoria)),
                'note_tecniche' => $prodotto->note_tecniche,
                'modalita_installazione' => $prodotto->modalita_installazione,
                'modalita_uso' => $prodotto->modalita_uso,
                'prezzo' => $prodotto->prezzo ? '€ ' . number_format($prodotto->prezzo, 2, ',', '.') : null,
                'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                'created_at' => $prodotto->created_at->format('d/m/Y'),
                'staff_assegnato' => $prodotto->staffAssegnato ? [
                    'nome' => $prodotto->staffAssegnato->nome_completo
                ] : null
            ];

            // Aggiungi malfunzionamenti solo se l'utente è autorizzato
            if (Auth::check() && Auth::user()->canViewMalfunzionamenti()) {
                $data['malfunzionamenti_count'] = $prodotto->malfunzionamenti()->count();
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Prodotto non trovato'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Errore in apiShowPubblico', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento prodotto'
            ], 500);
        }
    }

    // ================================================
    // METODI HELPER PRIVATI - SISTEMA CATEGORIE UNIFICATO
    // ================================================

    /**
     * CORREZIONE: Ottiene l'elenco delle categorie usando il sistema unificato
     * Questo metodo ora usa sempre il sistema delle categorie definito nel modello Prodotto
     */
    private function getCategorie(): array
    {
        try {
            // Usa SEMPRE il sistema unificato del modello
            return Prodotto::getCategorieUnifico();
            
        } catch (\Exception $e) {
            Log::error('Errore nel recupero categorie unificate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback con categorie di base in caso di errore
            return [
                'lavatrice' => 'Lavatrici',
                'lavastoviglie' => 'Lavastoviglie',
                'frigorifero' => 'Frigoriferi',
                'forno' => 'Forni',
                'altro' => 'Altro'
            ];
        }
    }

    /**
     * NUOVO: Ottiene solo le categorie presenti effettivamente nel database
     * Utile per i filtri dropdown (mostra solo categorie con prodotti)
     */
    private function getCategorieDisponibili(): array
    {
        try {
            return Prodotto::getCategorieDisponibili();
            
        } catch (\Exception $e) {
            Log::error('Errore nel recupero categorie disponibili', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback: ottieni categorie dalla query diretta
            $categoriePresenti = Prodotto::where('attivo', true)
                ->distinct()
                ->pluck('categoria')
                ->toArray();
                
            $categorieComplete = $this->getCategorie();
            
            $result = [];
            foreach ($categoriePresenti as $categoria) {
                $result[$categoria] = $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria));
            }
            
            return $result;
        }
    }

    /**
     * AGGIORNATO: Calcola statistiche per categoria usando sistema unificato
     */
    private function getStatsPerCategoria(): array
    {
        try {
            $stats = Prodotto::where('attivo', true)
                ->groupBy('categoria')
                ->selectRaw('categoria, count(*) as count')
                ->pluck('count', 'categoria')
                ->toArray();
            
            // Aggiungi etichette leggibili
            $categorieComplete = $this->getCategorie();
            $result = [];
            foreach ($stats as $categoria => $count) {
                $result[$categoria] = [
                    'count' => $count,
                    'label' => $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria))
                ];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Errore nel calcolo stats per categoria', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    // ================================================
    // METODI DI COMPATIBILITÀ
    // ================================================

    /**
     * Alias per compatibilità con route esistenti
     */
    public function adminIndex(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Vista admin specifica per prodotti
     */
    public function adminShow(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        $prodotto->load([
            'malfunzionamenti' => function($query) {
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
                      ->orderBy('numero_segnalazioni', 'desc')
                      ->orderBy('created_at', 'desc');
            },
            'malfunzionamenti.creatoBy:id,nome,cognome,livello_accesso',
            'malfunzionamenti.modificatoBy:id,nome,cognome,livello_accesso',
            'staffAssegnato:id,nome,cognome,livello_accesso,created_at',
        ]);

        // Statistiche avanzate per admin
        $statistiche = [
            'malfunzionamenti_totali' => $prodotto->malfunzionamenti->count(),
            'malfunzionamenti_critici' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
            'segnalazioni_totali' => $prodotto->malfunzionamenti->sum('numero_segnalazioni'),
            'piu_segnalato' => $prodotto->malfunzionamenti->sortByDesc('numero_segnalazioni')->first(),
        ];

        // Prodotti correlati
        $prodottiCorrelati = Prodotto::where('id', '!=', $prodotto->id)
            ->where(function($query) use ($prodotto) {
                $query->where('categoria', $prodotto->categoria)
                      ->orWhere('staff_assegnato_id', $prodotto->staff_assegnato_id);
            })
            ->where('attivo', true)
            ->withCount('malfunzionamenti')
            ->limit(5)
            ->get();

        // Staff disponibili per riassegnazione
        $staffDisponibili = User::where('livello_accesso', '3')
            ->where('id', '!=', $prodotto->staff_assegnato_id)
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        return view('admin.prodotti.show', compact(
            'prodotto',
            'statistiche', 
            'prodottiCorrelati',
            'staffDisponibili'
        ));
    }
}