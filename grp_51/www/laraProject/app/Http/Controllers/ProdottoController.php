<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Prodotto;
use App\Models\User;

class ProdottoController extends Controller
{
    // ================================================
    // METODI PER CATALOGO PUBBLICO (Livello 1) - CORRETTI
    // ================================================

    /**
     * Catalogo pubblico - accessibile a tutti senza autenticazione
     * CORREZIONE: Usa vista esistente con flag per nascondere malfunzionamenti
     */
    public function indexPubblico(Request $request)
    {
        // Query base per prodotti attivi
        $query = Prodotto::where('attivo', true);

        // === GESTIONE RICERCA CON WILDCARD ===
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            // Implementa ricerca con wildcard "*" come da specifiche
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

        // === FILTRO PER CATEGORIA ===
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

        // Categorie per filtri - usa metodo corretto
        $categorie = $this->getCategorie();

        // Statistiche pubbliche (SENZA malfunzionamenti)
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'categorie_count' => count($categorie),
            'per_categoria' => $this->getStatsPerCategoria(),
            'version' => 'pubblico' // Flag per la vista
        ];

        // CORREZIONE: Usa vista esistente ma con flag pubblico
        return view('prodotti.pubblico.index', compact('prodotti', 'categorie', 'stats'))
            ->with('isPublicView', true)
            ->with('showMalfunzionamenti', false);
    }

    /**
     * Scheda prodotto pubblica - NO malfunzionamenti
     * CORREZIONE: Usa vista esistente con flag per nascondere malfunzionamenti
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
        
        // CORREZIONE: Usa vista esistente con flag
        return view('prodotti.pubblico.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
    }

    // ================================================
    // METODI PER CATALOGO COMPLETO (Livello 2+) - INVARIATI
    // ================================================

    /**
     * Catalogo completo per tecnici - CON malfunzionamenti
     * Richiede autenticazione e livello 2+
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

        // Filtro per categoria
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

        // Categorie per filtri
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
            $stats['mie_soluzioni'] = \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                ? \App\Models\Malfunzionamento::where('creato_da', $user->id)->count() 
                : 0;
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
    // METODI HELPER PRIVATI - AGGIUNTI
    // ================================================

    /**
     * Ottiene l'elenco delle categorie disponibili
     * Helper per evitare errori se il metodo statico non esiste nel Model
     */
    private function getCategorie(): array
    {
        try {
            // Prova prima il metodo statico del Model
            if (method_exists(Prodotto::class, 'getCategorie')) {
                return Prodotto::getCategorie();
            }
            
            // Fallback: ottieni categorie dalla query
            return Prodotto::where('attivo', true)
                ->select('categoria')
                ->distinct()
                ->orderBy('categoria')
                ->pluck('categoria')
                ->toArray();
                
        } catch (\Exception $e) {
            Log::error('Errore nel recupero categorie', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback con categorie di default per elettrodomestici
            return [
                'elettrodomestici',
                'cucina',
                'lavanderia', 
                'climatizzazione',
                'piccoli_elettrodomestici'
            ];
        }
    }

    /**
     * Calcola statistiche per categoria
     */
    private function getStatsPerCategoria(): array
    {
        try {
            return Prodotto::where('attivo', true)
                ->groupBy('categoria')
                ->selectRaw('categoria, count(*) as count')
                ->pluck('count', 'categoria')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore nel calcolo stats per categoria', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    // ================================================
    // RICERCA AVANZATA PER TECNICI - CORRETTA
    // ================================================

    /**
     * Ricerca avanzata nei prodotti per tecnici (Livello 2+)
     * Route: GET /prodotti-completi/ricerca
     * Name: prodotti.completo.ricerca
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

            // Usa la vista completa esistente con i flag appropriati
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

            // Redirect con messaggio di errore
            return redirect()->route('prodotti.completo.index')
                ->with('error', 'Errore durante la ricerca: ' . $e->getMessage());
        }
    }

    // ================================================
    // METODO CATEGORIA GENERICO - CORRETTO
    // ================================================

    /**
     * Visualizza prodotti per categoria specifica
     * Usabile sia da pubblico che da utenti autenticati
     */
    public function categoria($categoria)
    {
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
        
        $categorie = $this->getCategorie();

        $stats = [
            'total_prodotti' => $prodotti->total(),
            'categoria_corrente' => $categoria,
            'version' => $canViewMalfunctions ? 'completo' : 'pubblico'
        ];

        // Usa vista appropriata
        $view = $canViewMalfunctions ? 'prodotti.completo.index' : 'prodotti.pubblico.index';

        return view($view, compact('prodotti', 'categorie', 'stats'))
            ->with('showMalfunzionamenti', $canViewMalfunctions)
            ->with('isPublicView', !$canViewMalfunctions);
    }

    // ================================================
    // METODI AMMINISTRATIVI - INVARIATI (da mantenere)
    // ================================================

    /**
     * Lista prodotti per amministratori
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

        // Filtro per staff assegnato - CORREZIONE IMPORTANTE
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            
            if ($staffId === '0' || $staffId === 0) {
                // Prodotti NON assegnati
                $query->whereNull('staff_assegnato_id');
            } else {
                // Prodotti assegnati a staff specifico
                $query->where('staff_assegnato_id', $staffId);
            }
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

        // Statistiche admin
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'attivi' => Prodotto::where('attivo', true)->count(),
            'inattivi' => Prodotto::where('attivo', false)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->count(),
            'non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(),
        ];

        return view('admin.prodotti.index', compact('prodotti', 'staffMembers', 'stats'));
    }

    public function adminIndex(Request $request)
{
    // Verifica autorizzazione
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        abort(403, 'Accesso riservato agli amministratori');
    }

    // Se hai già il metodo index(), puoi semplicemente chiamarlo
    return $this->index($request);

    // OPPURE implementa una logica specifica per admin se necessario
}

    // ================================================
    // API ENDPOINTS - CORRETTI
    // ================================================

    /**
     * API per ricerca prodotti AJAX (pubblico)
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

            // Formatta risultati per API
            $results = $prodotti->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'descrizione' => \Illuminate\Support\Str::limit($prodotto->descrizione, 100),
                    'categoria' => $prodotto->categoria,
                    'prezzo' => $prodotto->prezzo ? '€ ' . number_format($prodotto->prezzo, 2, ',', '.') : null,
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'url' => route('prodotti.pubblico.show', $prodotto->id)
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
     * API per lista prodotti pubblici
     */
    public function apiIndexPubblico(Request $request)
    {
        try {
            $query = Prodotto::where('attivo', true);

            // Filtro categoria
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }

            $perPage = min($request->input('per_page', 12), 50);
            
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->paginate($perPage);

            $data = $prodotti->getCollection()->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
                    'prezzo' => $prodotto->prezzo ? '€ ' . number_format($prodotto->prezzo, 2, ',', '.') : null,
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'url' => route('prodotti.pubblico.show', $prodotto->id)
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
     * API per singolo prodotto pubblico
     */
    public function apiShowPubblico($id)
    {
        try {
            $prodotto = Prodotto::where('attivo', true)->findOrFail($id);

            $data = [
                'id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'modello' => $prodotto->modello,
                'descrizione' => $prodotto->descrizione,
                'categoria' => $prodotto->categoria,
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
    // METODI CRUD AMMINISTRATIVI - INVARIATI
    // ================================================

    /**
     * Form creazione nuovo prodotto
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a creare prodotti');
        }

        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        return view('admin.prodotti.create', compact('staffMembers'));
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
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', 'Prodotto "' . $prodotto->nome . '" creato con successo');
    }

    /**
     * Form modifica prodotto
     */
    public function edit(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a modificare prodotti');
        }

        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        return view('admin.prodotti.edit', compact('prodotto', 'staffMembers'));
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

    /**
     * Disattiva prodotto (soft delete)
     */
    public function destroy(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        $prodotto->update(['attivo' => false]);

        Log::info('Prodotto disattivato', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'deactivated_by' => Auth::id()
        ]);

        return redirect()->route('admin.prodotti.index')
            ->with('success', 'Prodotto "' . $prodotto->nome . '" rimosso dal catalogo');
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

            return response()->json([
                'success' => true,
                'message' => "Prodotto '{$prodotto->nome}' {$action} con successo.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Errore toggle status prodotto', [
                'prodotto_id' => $prodotto->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel cambio stato del prodotto.'
            ], 500);
        }
    }

    /**
     * Azioni bulk sui prodotti
     */
    public function bulkAction(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:prodotti,id'
        ]);

        try {
            $productIds = $request->input('products');
            $action = $request->input('action');
            $count = 0;

            switch ($action) {
                case 'activate':
                    $count = Prodotto::whereIn('id', $productIds)
                        ->update(['attivo' => true]);
                    $message = "Attivati {$count} prodotti";
                    break;

                case 'deactivate':
                    $count = Prodotto::whereIn('id', $productIds)
                        ->update(['attivo' => false]);
                    $message = "Disattivati {$count} prodotti";
                    break;

                case 'delete':
                    $count = Prodotto::whereIn('id', $productIds)
                        ->update(['attivo' => false]);
                    $message = "Eliminati {$count} prodotti";
                    break;
            }

            Log::info("Azione bulk sui prodotti", [
                'action' => $action,
                'products_count' => $count,
                'product_ids' => $productIds,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Errore azione bulk prodotti', [
                'action' => $request->input('action'),
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'esecuzione dell\'azione.'
            ], 500);
        }
    }
}