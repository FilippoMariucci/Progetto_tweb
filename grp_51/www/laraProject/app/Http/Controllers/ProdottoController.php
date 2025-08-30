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
    // METODI PER CATALOGO PUBBLICO (Livello 1)
    // ================================================

   /**
     * Catalogo pubblico - accessibile a tutti senza autenticazione
     * Mostra solo informazioni base dei prodotti, NO malfunzionamenti
     */
    /**
 * Catalogo pubblico - accessibile a tutti senza autenticazione
 * Mostra solo informazioni base dei prodotti, NO malfunzionamenti
 */
public function indexPubblico(Request $request)
{
    // Query base per prodotti attivi
    $query = Prodotto::where('attivo', true);

    // === GESTIONE RICERCA CON WILDCARD ===
    if ($request->filled('search')) {
        $searchTerm = $request->input('search');
        
        // Implementa ricerca con wildcard "*"
        if (str_ends_with($searchTerm, '*')) {
            $searchTerm = rtrim($searchTerm, '*');
            $query->where('descrizione', 'LIKE', $searchTerm . '%');
        } else {
            $query->where('descrizione', 'LIKE', '%' . $searchTerm . '%');
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

    // Categorie per filtri
    $categorie = Prodotto::getCategorie();

    // Statistiche pubbliche (SENZA malfunzionamenti)
    $stats = [
        'total_prodotti' => Prodotto::where('attivo', true)->count(),
        'categorie_count' => count($categorie),
        'version' => 'pubblico' // Flag per la vista
    ];

    // IMPORTANTE: Usa vista specifica per il pubblico
    return view('prodotti.pubblico.index', compact('prodotti', 'categorie', 'stats'));
}

/**
 * Scheda prodotto pubblica - NO malfunzionamenti
 * Solo informazioni tecniche base
 */
public function showPubblico(Prodotto $prodotto)
{
    // Verifica che il prodotto sia attivo
    if (!$prodotto->attivo) {
        abort(404, 'Prodotto non disponibile');
    }

    // Carica solo informazioni base (NO malfunzionamenti)
    $prodotto->load(['staffAssegnato:id,nome,cognome']);

    // IMPORTANTE: Flag per nascondere malfunzionamenti nella vista
    $showMalfunzionamenti = false;
    $isPublicView = true; // Flag aggiuntivo
    
    // Usa vista pubblica specifica
    return view('prodotti.pubblico.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
}

    // ================================================
    // METODI PER CATALOGO COMPLETO (Livello 2+)
    // ================================================

    /**
 * Catalogo completo per tecnici - CON malfunzionamenti
 * Richiede autenticazione e livello 2+
 * AGGIUNTO: Supporto filtro per prodotti assegnati allo staff
 */
 /**
     * Catalogo completo per tecnici - CORRETTO
     */
    public function indexCompleto(Request $request)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici');
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

        // Ricerca avanzata per tecnici
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

        // === AUTOMATICO: Se l'utente è staff, mostra prima i suoi prodotti ===
        if ($user->isStaff() && !$request->filled('staff_filter')) {
            $query->orderByRaw("CASE WHEN staff_assegnato_id = ? THEN 0 ELSE 1 END", [$user->id]);
        }

        // Carica prodotti con conteggio malfunzionamenti
        $prodotti = $query->withCount([
            'malfunzionamenti',
            'malfunzionamenti as critici_count' => function($query) {
                $query->where('gravita', 'critica');
            }
        ])->with('staffAssegnato:id,nome,cognome')
          ->orderBy('nome')
          ->paginate(12);

        // CORREZIONE: Usa il metodo statico per le categorie
        $categorie = Prodotto::getCategorie();

        // Statistiche avanzate per tecnici
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
            'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count()
        ];

        // AGGIUNGI: Statistiche specifiche se l'utente è staff
        if ($user->isStaff()) {
            $stats['miei_prodotti'] = Prodotto::where('staff_assegnato_id', $user->id)
                ->where('attivo', true)
                ->count();
            $stats['mie_soluzioni'] = \Schema::hasColumn('malfunzionamenti', 'creato_da') 
                ? \App\Models\Malfunzionamento::where('creato_da', $user->id)->count() 
                : 0;
        }

        // Determina quale vista usare
        $view = $request->input('staff_filter') === 'my_products' ? 'prodotti.staff.index' : 'prodotti.completo.index';
        
        // Se la vista specifica per staff non esiste, usa quella completa
        if (!view()->exists($view)) {
            $view = 'prodotti.completo.index';
        }

        return view($view, compact('prodotti', 'categorie', 'stats'));
    }

    /**
     * Metodo categoria - CORRETTO
     */
    public function categoria($categoria)
    {
        $prodotti = Prodotto::where('categoria', $categoria)
            ->where('attivo', true)
            ->paginate(12);
        
        // CORREZIONE: Usa il metodo statico per le categorie
        $categorie = Prodotto::getCategorie();

        $stats = [
            'total_prodotti' => $prodotti->total(),
        ];

        return view('prodotti.index', compact('prodotti', 'categorie', 'stats'));
    }

    public function showCompleto(Prodotto $prodotto)
    {
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici');
        }

        if (!$prodotto->attivo) {
            abort(404, 'Prodotto non disponibile');
        }

        // Carica tutte le relazioni inclusi i malfunzionamenti
        $prodotto->load([
            'malfunzionamenti' => function($query) {
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
                      ->orderBy('numero_segnalazioni', 'desc');
            },
            'malfunzionamenti.creatoBy:id,nome,cognome',
            'staffAssegnato:id,nome,cognome'
        ]);
        // Variabile per mostrare malfunzionamenti nella vista
        $showMalfunzionamenti = true;
        return view('prodotti.completo.show', compact('prodotto', 'showMalfunzionamenti'));
    }

    /**
 * Ricerca avanzata nei prodotti per tecnici (Livello 2+)
 * Route: GET /prodotti-completi/ricerca
 * Name: prodotti.completo.ricerca
 * 
 * Supporta ricerca wildcard "*" e filtri avanzati
 * per tecnici e superiori
 */
public function ricercaAvanzata(Request $request)
{
    // Verifica autorizzazioni - solo tecnici e superiori
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

        // Carica prodotti con conteggio malfunzionamenti
        $prodotti = $query->withCount([
            'malfunzionamenti',
            'malfunzionamenti as critici_count' => function($query) {
                $query->where('gravita', 'critica');
            }
        ])->with('staffAssegnato:id,nome,cognome')
          ->orderBy('critici_count', 'desc')
          ->orderBy('malfunzionamenti_count', 'desc')
          ->orderBy('nome', 'asc')
          ->paginate(15)
          ->withQueryString();

        // Statistiche semplici
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
            'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
            'risultati_trovati' => $prodotti->total()
        ];

        // Categorie per filtro
        $categorie = Prodotto::getCategorie();

        // Log per debugging
        Log::info('Ricerca avanzata completata', [
            'search_term' => $request->input('search'),
            'results_count' => $prodotti->total(),
            'user_id' => Auth::id()
        ]);

        // Usa la vista esistente che funziona
        return view('prodotti.completo.index', compact('prodotti', 'stats', 'categorie'));

    } catch (\Exception $e) {
        // Log dell'errore dettagliato
        Log::error('Errore nella ricerca avanzata prodotti', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'search_params' => $request->all(),
            'user_id' => Auth::id()
        ]);

        // Redirect con messaggio di errore specifico
        return redirect()->route('prodotti.completo.index')
            ->with('error', 'Errore durante la ricerca: ' . $e->getMessage());
    }
}

/**
 * API per ricerca avanzata prodotti (AJAX)
 * Supporta le stesse funzionalità della ricerca web ma restituisce JSON
 */
public function apiRicercaAvanzata(Request $request)
{
    // Verifica autorizzazioni
    if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
        return response()->json([
            'success' => false,
            'error' => 'Accesso riservato ai tecnici'
        ], 403);
    }

    try {
        // Riusa la stessa logica della ricerca avanzata
        $query = Prodotto::where('attivo', true);
        
        // Applica gli stessi filtri
        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));
            
            if (str_ends_with($searchTerm, '*')) {
                $baseTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($baseTerm) {
                    $q->where('nome', 'LIKE', $baseTerm . '%')
                      ->orWhere('descrizione', 'LIKE', $baseTerm . '%')
                      ->orWhere('modello', 'LIKE', $baseTerm . '%');
                });
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // Applica altri filtri se presenti
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        if ($request->boolean('has_critici')) {
            $query->whereHas('malfunzionamenti', function($q) {
                $q->where('gravita', 'critica');
            });
        }

        // Carica dati con conteggi
        $prodotti = $query->withCount([
                'malfunzionamenti',
                'malfunzionamenti as critici_count' => function($query) {
                    $query->where('gravita', 'critica');
                }
            ])
            ->with('staffAssegnato:id,nome,cognome')
            ->orderBy('critici_count', 'desc')
            ->limit($request->input('limit', 20))
            ->get();

        // Formatta risultati per API
        $results = $prodotti->map(function($prodotto) {
            return [
                'id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'modello' => $prodotto->modello,
                'categoria' => $prodotto->categoria,
                'descrizione' => \Illuminate\Support\Str::limit($prodotto->descrizione, 100),
                'malfunzionamenti_count' => $prodotto->malfunzionamenti_count,
                'critici_count' => $prodotto->critici_count,
                'staff_assegnato' => $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : null,
                'url' => route('prodotti.completo.show', $prodotto),
                'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                'has_critici' => $prodotto->critici_count > 0,
                'priority_level' => $this->calculatePriorityLevel($prodotto->critici_count, $prodotto->malfunzionamenti_count)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'total' => $results->count(),
            'search_info' => [
                'term' => $request->input('search'),
                'is_wildcard' => str_ends_with($request->input('search', ''), '*'),
                'filters_applied' => $request->only(['categoria', 'has_critici'])
            ],
            'timestamp' => now()->toISOString()
        ]);

    } catch (\Exception $e) {
        Log::error('Errore API ricerca avanzata', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Errore durante la ricerca avanzata'
        ], 500);
    }
}

/**
 * Helper per calcolare il livello di priorità
 */
private function calculatePriorityLevel(int $critici, int $totali): string
{
    if ($critici > 0) {
        return $critici > 3 ? 'critical' : 'high';
    } elseif ($totali > 10) {
        return 'medium';
    } elseif ($totali > 0) {
        return 'low';
    } else {
        return 'none';
    }
}

    // ================================================
    // GESTIONE AMMINISTRATIVA PRODOTTI (Livello 4)
    // ================================================

    /**
     * Lista prodotti per amministratori - CORRETTO con filtro non assegnati
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        $query = Prodotto::query();

        // === FILTRO STATO ===
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'attivi') {
                $query->where('attivo', true);
            } elseif ($status === 'inattivi') {
                $query->where('attivo', false);
            }
        }

        // === FILTRO STAFF ASSEGNATO - CORREZIONE ===
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            
            // DEBUG: Log per capire cosa sta succedendo
            Log::info('Filtro staff_id ricevuto', [
                'staff_id' => $staffId,
                'type' => gettype($staffId)
            ]);
            
            if ($staffId === '0' || $staffId === 0) {
                // CORREZIONE: Prodotti NON assegnati (staff_assegnato_id è NULL)
                $query->whereNull('staff_assegnato_id');
                
                Log::info('Filtro prodotti non assegnati applicato');
                
            } else {
                // Prodotti assegnati a uno staff specifico
                $query->where('staff_assegnato_id', $staffId);
                
                Log::info('Filtro staff specifico applicato', [
                    'staff_id' => $staffId
                ]);
            }
        }

        // === FILTRO RICERCA ===
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // === ORDINAMENTO ===
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // DEBUG: Mostra la query SQL generata
        if (app()->environment('local')) {
            Log::info('Query SQL generata', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'filters' => $request->only(['status', 'staff_id', 'search'])
            ]);
        }

        // === ESECUZIONE QUERY ===
        $prodotti = $query->withCount('malfunzionamenti')
            ->with('staffAssegnato:id,nome,cognome')
            ->paginate(15)
            ->withQueryString();

        // Staff members per filtro
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // === STATISTICHE AGGIORNATE ===
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'attivi' => Prodotto::where('attivo', true)->count(),
            'inattivi' => Prodotto::where('attivo', false)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->count(),
            // AGGIUNTA: Statistica prodotti non assegnati
            'non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(),
        ];

        // DEBUG: Log delle statistiche
        Log::info('Statistiche prodotti calcolate', $stats);

        return view('admin.prodotti.index', compact('prodotti', 'staffMembers', 'stats'));
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
            'malfunzionamenti.creadoDa:id,nome,cognome',
            'malfunzionamenti.modificatoDa:id,nome,cognome',
            'staffAssegnato:id,nome,cognome'
        ]);

        return view('admin.prodotti.show', compact('prodotto'));
    }

    // ================================================
// AGGIUNGI QUESTO METODO AL TUO ProdottoController.php
// ================================================

/**
 * Visualizzazione prodotto per amministratori (vista admin completa)
 * ROUTE: GET /admin/prodotti/{prodotto}
 * NAME: admin.prodotti.show
 * 
 * DIFFERENZA dal metodo show():
 * - Vista specifica per admin con tutte le informazioni
 * - Include statistiche avanzate
 * - Mostra cronologia modifiche
 * - Controlli amministrativi aggiuntivi
 */
public function adminShow(Prodotto $prodotto)
{
    // Verifica autorizzazione amministratore
    if (!Auth::check() || !Auth::user()->canManageProdotti()) {
        abort(403, 'Accesso riservato agli amministratori');
    }

    // Carica tutte le relazioni necessarie per la vista admin
    $prodotto->load([
        // Malfunzionamenti con informazioni complete
        'malfunzionamenti' => function($query) {
            $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
                  ->orderBy('numero_segnalazioni', 'desc')
                  ->orderBy('created_at', 'desc');
        },
        
        // Staff che ha creato/modificato i malfunzionamenti
        'malfunzionamenti.creatoBy:id,nome,cognome,livello_accesso',
        'malfunzionamenti.modificatoBy:id,nome,cognome,livello_accesso',
        
        // Staff attualmente assegnato al prodotto (CORRETTO - senza email)
        'staffAssegnato:id,nome,cognome,livello_accesso,created_at',
    ]);

    // === STATISTICHE AVANZATE PER L'ADMIN ===
    $statistiche = [
        // Conteggi malfunzionamenti per gravità
        'malfunzionamenti_totali' => $prodotto->malfunzionamenti->count(),
        'malfunzionamenti_critici' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
        'malfunzionamenti_alti' => $prodotto->malfunzionamenti->where('gravita', 'alta')->count(),
        'malfunzionamenti_medi' => $prodotto->malfunzionamenti->where('gravita', 'media')->count(),
        'malfunzionamenti_bassi' => $prodotto->malfunzionamenti->where('gravita', 'bassa')->count(),
        
        // Segnalazioni totali
        'segnalazioni_totali' => $prodotto->malfunzionamenti->sum('numero_segnalazioni'),
        
        // Malfunzionamento più segnalato
        'piu_segnalato' => $prodotto->malfunzionamenti->sortByDesc('numero_segnalazioni')->first(),
        
        // Date importanti
        'primo_malfunzionamento' => $prodotto->malfunzionamenti->min('created_at'),
        'ultimo_aggiornamento' => $prodotto->malfunzionamenti->max('updated_at'),
        
        // Staff attività
        'staff_contributor' => $prodotto->malfunzionamenti->pluck('creato_by')->unique()->count(),
    ];

    // === PRODOTTI CORRELATI (stessa categoria o staff) ===
    $prodottiCorrelati = Prodotto::where('id', '!=', $prodotto->id)
        ->where(function($query) use ($prodotto) {
            $query->where('categoria', $prodotto->categoria)
                  ->orWhere('staff_assegnato_id', $prodotto->staff_assegnato_id);
        })
        ->where('attivo', true)
        ->withCount('malfunzionamenti')
        ->orderBy('malfunzionamenti_count', 'desc')
        ->limit(5)
        ->get();

    // === STAFF DISPONIBILI PER RIASSEGNAZIONE (CORRETTO - senza email) ===
    $staffDisponibili = User::where('livello_accesso', '3')
        ->where('id', '!=', $prodotto->staff_assegnato_id) // Esclude lo staff attuale
        ->select('id', 'nome', 'cognome') // ← RIMOSSO 'email'
        ->orderBy('nome')
        ->orderBy('cognome')
        ->get();

    // === METRICHE PERFORMANCE ===
    $metriche = [
        'giorni_dal_lancio' => $prodotto->created_at->diffInDays(now()),
        'media_segnalazioni_per_malfunzionamento' => $statistiche['malfunzionamenti_totali'] > 0 
            ? round($statistiche['segnalazioni_totali'] / $statistiche['malfunzionamenti_totali'], 2) 
            : 0,
        'frequenza_problemi' => $this->calcolaFrequenzaProblemi($prodotto),
        'livello_criticita' => $this->determinaLivelloCriticita($statistiche),
    ];

    // Log dell'accesso admin per audit
    Log::info('Admin visualizza prodotto completo', [
        'prodotto_id' => $prodotto->id,
        'modello' => $prodotto->modello,
        'admin_id' => Auth::id(),
        'admin_name' => Auth::user()->nome_completo,
        'timestamp' => now()
    ]);

    // Restituisce la vista admin specifica
    return view('admin.prodotti.show', compact(
        'prodotto',
        'statistiche', 
        'prodottiCorrelati',
        'staffDisponibili',
        'metriche'
    ));
}
/**
 * Metodo helper per calcolare la frequenza dei problemi
 */
private function calcolaFrequenzaProblemi(Prodotto $prodotto): string
{
    $giorni = $prodotto->created_at->diffInDays(now());
    $problemi = $prodotto->malfunzionamenti->count();
    
    if ($giorni == 0 || $problemi == 0) {
        return 'N/A';
    }
    
    $frequenza = $problemi / max($giorni, 1);
    
    if ($frequenza > 1) {
        return 'Molto Alta';
    } elseif ($frequenza > 0.5) {
        return 'Alta';
    } elseif ($frequenza > 0.1) {
        return 'Media';
    } else {
        return 'Bassa';
    }
}

/**
 * Metodo helper per determinare il livello di criticità generale
 */
private function determinaLivelloCriticita(array $statistiche): array
{
    $critici = $statistiche['malfunzionamenti_critici'];
    $totali = $statistiche['malfunzionamenti_totali'];
    
    if ($totali == 0) {
        return ['livello' => 'ok', 'colore' => 'success', 'descrizione' => 'Nessun problema segnalato'];
    }
    
    $percentualeCritici = ($critici / $totali) * 100;
    
    if ($percentualeCritici > 30) {
        return ['livello' => 'critico', 'colore' => 'danger', 'descrizione' => 'Molti problemi critici'];
    } elseif ($percentualeCritici > 10) {
        return ['livello' => 'alto', 'colore' => 'warning', 'descrizione' => 'Alcuni problemi critici'];
    } elseif ($totali > 10) {
        return ['livello' => 'medio', 'colore' => 'info', 'descrizione' => 'Molti problemi non critici'];
    } else {
        return ['livello' => 'basso', 'colore' => 'secondary', 'descrizione' => 'Pochi problemi'];
    }
}

/**
 * Metodo per ottenere la cronologia delle modifiche (da implementare)
 */
private function getCronologiaModifiche(Prodotto $prodotto): \Illuminate\Support\Collection
{
    // Placeholder per future implementazioni di audit trail
    // Potresti usare un package come spatie/laravel-activitylog
    
    return collect([
        [
            'azione' => 'Creazione prodotto',
            'data' => $prodotto->created_at,
            'utente' => 'Sistema', // O chi ha creato se hai il campo
            'dettagli' => 'Prodotto aggiunto al catalogo'
        ],
        [
            'azione' => 'Ultimo aggiornamento',
            'data' => $prodotto->updated_at,
            'utente' => $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : 'Sistema',
            'dettagli' => 'Informazioni prodotto modificate'
        ]
    ]);
}

// ================================================
// AGGIUNGI ANCHE QUESTO METODO SE NON LO HAI GIÀ
// ================================================

/**
 * Lista prodotti per amministratori (nome corretto)
 * ROUTE: GET /admin/prodotti
 * NAME: admin.prodotti.index
 */
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

    /**
     * Form creazione nuovo prodotto
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

        return redirect()->route('prodotti.show', $prodotto)
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
        // Elimina vecchia foto
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

    // CORREZIONE: Redirect alla vista admin invece che pubblica
    return redirect()->route('admin.prodotti.show', $prodotto)
        ->with('success', 'Prodotto "' . $prodotto->nome . '" aggiornato con successo');
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

        return redirect()->route('prodotti.index')
            ->with('success', 'Prodotto "' . $prodotto->nome . '" rimosso dal catalogo');
    }

    // ================================================
    // API E FUNZIONI UTILI
    // ================================================

    /**
     * API per ricerca AJAX
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'type' => 'in:public,complete'
        ]);

        $searchTerm = $request->input('q');
        $type = $request->input('type', 'public');

        $query = Prodotto::where('attivo', true);

        // Implementa wildcard search
        if (str_ends_with($searchTerm, '*')) {
            $searchTerm = rtrim($searchTerm, '*');
            $query->where('descrizione', 'LIKE', $searchTerm . '%');
        } else {
            $query->where('descrizione', 'LIKE', '%' . $searchTerm . '%');
        }

        // Limita risultati per performance
        $prodotti = $query->select('id', 'nome', 'modello', 'categoria', 'foto')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $prodotti->map(function($prodotto) use ($type) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
                    'foto_url' => $prodotto->foto ? asset('storage/' . $prodotto->foto) : null,
                    'url' => $type === 'complete' 
                        ? route('prodotti.completo.show', $prodotto)
                        : route('prodotti.show', $prodotto)
                ];
            }),
            'total' => $prodotti->count()
        ]);
    }

    /**
     * API per statistiche prodotti
     */

// AGGIUNGI QUESTI METODI AL TUO ProdottoController.php

    // ================================================
    // METODI API PER AJAX (alla fine del controller)
    // ================================================

    /**
     * API per ricerca prodotti AJAX (pubblico)
     * Utilizzata dalle chiamate JavaScript nella pagina prodotti
     */
    public function apiSearch(Request $request)
    {
        try {
            // Validazione input
            $request->validate([
                'q' => 'required|string|min:1|max:100',
            ]);

            $searchTerm = trim($request->input('q'));
            
            // Log per debugging
            Log::info('API Search chiamata', [
                'search_term' => $searchTerm,
                'ip' => $request->ip()
            ]);

            $query = Prodotto::where('attivo', true);

            // Implementa ricerca con wildcard come nelle specifiche
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

            // Esegui query con limit per performance
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->limit(20)
                ->get();

            // Formatta risultati
            $results = $prodotti->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'descrizione' => \Illuminate\Support\Str::limit($prodotto->descrizione, 100),
                    'categoria' => $prodotto->categoria,
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Parametri di ricerca non validi',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Errore in apiSearch', [
                'error' => $e->getMessage(),
                'search_term' => $request->input('q', 'N/A'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore interno del server nella ricerca prodotti',
                'message' => app()->environment('local') ? $e->getMessage() : 'Riprova più tardi'
            ], 500);
        }
    }

    /**
     * API per lista prodotti (pubblico)
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = Prodotto::where('attivo', true);

            // Filtro categoria
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }

            // Paginazione per API
            $perPage = min($request->input('per_page', 12), 50); // Max 50 per volta
            
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->paginate($perPage);

            // Formatta risultati
            $data = $prodotti->getCollection()->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
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
            Log::error('Errore in apiIndex', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento prodotti'
            ], 500);
        }
    }

    /**
     * API per singolo prodotto
     */
    public function apiShow($id)
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
            Log::error('Errore in apiShow', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento prodotto'
            ], 500);
        }
    }

    /**
     * API per categorie prodotti
     */
    public function apiCategorie()
    {
        try {
            $categorie = Prodotto::where('attivo', true)
                ->select('categoria')
                ->groupBy('categoria')
                ->withCount(['products as prodotti_count' => function($query) {
                    $query->where('attivo', true);
                }])
                ->orderBy('categoria')
                ->get()
                ->map(function($item) {
                    return [
                        'nome' => $item->categoria,
                        'prodotti_count' => $item->prodotti_count ?? 0,
                        'url' => route('prodotti.categoria', $item->categoria)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categorie,
                'total' => $categorie->count(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore in apiCategorie', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento categorie'
            ], 500);
        }
    }

    /**
     * API per ricerca tecnica avanzata (Livello 2+)
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

            // Formatta risultati per tecnici
            $results = $prodotti->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
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
     * API per export dati (Admin only)
     */
    public function apiExport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            return response()->json([
                'success' => false,
                'error' => 'Accesso riservato agli amministratori'
            ], 403);
        }

        try {
            $format = $request->input('format', 'json');
            
            $prodotti = Prodotto::with(['staffAssegnato:id,nome,cognome'])
                ->withCount('malfunzionamenti')
                ->get();

            if ($format === 'csv') {
                // Implementa export CSV se necessario
                // Per ora restituisce JSON
            }

            $data = $prodotti->map(function($prodotto) {
                return [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria,
                    'attivo' => $prodotto->attivo,
                    'prezzo' => $prodotto->prezzo,
                    'malfunzionamenti_count' => $prodotto->malfunzionamenti_count,
                    'staff_assegnato' => $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : null,
                    'created_at' => $prodotto->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $data->count(),
                'exported_by' => Auth::user()->nome_completo,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore in apiExport', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nell\'export dati'
            ], 500);
        }
    }

    /**
     * Toggle status prodotto (AJAX)
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
     * Azioni bulk sui prodotti (AJAX)
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
                        ->update(['attivo' => false]); // Soft delete
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