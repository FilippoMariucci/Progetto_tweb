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
 * ===============================================
 * CONTROLLER PRINCIPALE PER LA GESTIONE PRODOTTI
 * ===============================================
 * 
 * LINGUAGGIO: PHP con Framework Laravel 12
 * ARCHITETTURA: MVC (Model-View-Controller)
 * 
 * SCOPO DEL CONTROLLER:
 * Gestisce tutte le operazioni CRUD (Create, Read, Update, Delete) sui prodotti
 * del sistema di assistenza tecnica, implementando un sistema di autorizzazioni
 * a 4 livelli e viste multiple per diversi tipi di utenti.
 * 
 * LIVELLI DI ACCESSO IMPLEMENTATI:
 * 
 * LIVELLO 1 - PUBBLICO (Accesso Libero):
 * - Visualizzazione catalogo prodotti base
 * - Schede tecniche prodotti (SENZA malfunzionamenti)
 * - Ricerca testuale con supporto wildcard
 * - Filtri per categoria
 * 
 * LIVELLO 2 - TECNICI (Autenticazione Richiesta):
 * - Catalogo completo CON malfunzionamenti
 * - Conteggi segnalazioni e problemi critici
 * - Ricerca avanzata tecnica
 * - Visualizzazione soluzioni tecniche
 * 
 * LIVELLO 3 - STAFF AZIENDALE:
 * - Gestione prodotti assegnati
 * - Creazione/modifica malfunzionamenti
 * - Filtri per "Miei Prodotti"
 * - Dashboard specializzata
 * 
 * LIVELLO 4 - AMMINISTRATORI:
 * - Gestione completa prodotti (CRUD)
 * - Assegnazione staff ai prodotti
 * - Operazioni bulk (attiva/disattiva/elimina multipli)
 * - Statistiche avanzate e reporting
 * 
 * CARATTERISTICHE TECNICHE:
 * - Sistema categorie unificato e centralizzato
 * - Supporto wildcard nelle ricerche (es. "lav*" per lavatrici)
 * - API REST per integrazioni AJAX/JavaScript
 * - Upload e gestione immagini prodotti
 * - Logging dettagliato per audit e debugging
 * - Gestione errori robusta con try-catch
 * - Paginazione automatica Laravel
 * - Validazione input completa
 * - Soft delete e ripristino prodotti
 * 
 * PATTERN IMPLEMENTATI:
 * - Repository pattern (tramite Eloquent)
 * - Authorization Gates/Policies
 * - Form Request Validation
 * - API Resource Transformation
 * - Service Layer per logica business
 */
class ProdottoController extends Controller
{
    // ================================================
    // SEZIONE 1: METODI PUBBLICI (Livello 1 - Accesso Libero)
    // ================================================

    /**
     * METODO: indexPubblico()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /catalogo (pubblica)
     * ACCESSO: Tutti (non richiede autenticazione)
     * 
     * SCOPO:
     * Mostra il catalogo pubblico dei prodotti senza informazioni sui malfunzionamenti.
     * È la vetrina principale per utenti non registrati che vogliono consultare i prodotti.
     * 
     * FUNZIONALITÀ IMPLEMENTATE:
     * - Visualizzazione prodotti attivi con paginazione
     * - Ricerca testuale con supporto wildcard (*)
     * - Filtro per categoria prodotto
     * - Statistiche basic per il frontend
     * - Sistema categorie unificato
     * - Logging per analytics pubbliche
     * 
     * PARAMETRI:
     * @param Request $request - Oggetto richiesta HTTP contenente:
     *   - search: termine di ricerca (supporta wildcard *)
     *   - categoria: filtro per categoria specifica
     *   - page: numero pagina per paginazione
     * 
     * TECNOLOGIE USATE:
     * - Laravel Query Builder per costruzione query dinamiche
     * - Eloquent ORM per interazione database
     * - Laravel Pagination per navigazione risultati
     * - Blade templating per renderizzazione vista
     */
    public function indexPubblico(Request $request)
    {
        // === COSTRUZIONE QUERY BASE ===
        // where('attivo', true) = mostra solo prodotti disponibili al pubblico
        // Nasconde prodotti disattivati o in manutenzione
        $query = Prodotto::where('attivo', true);

        // === IMPLEMENTAZIONE RICERCA TESTUALE CON WILDCARD ===
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            // === GESTIONE WILDCARD SPECIALE ===
            // Se il termine finisce con *, implementa ricerca "inizia con"
            // Esempio: "lav*" trova "lavatrici", "lavastoviglie", "lavelli"
            if (str_ends_with($searchTerm, '*')) {
                // rtrim() rimuove il carattere * dalla fine
                $searchTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($searchTerm) {
                    // LIKE con % solo alla fine = "inizia con"
                    $q->where('descrizione', 'LIKE', $searchTerm . '%')
                      ->orWhere('nome', 'LIKE', $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', $searchTerm . '%');
                });
            } else {
                // === RICERCA NORMALE FULL-TEXT ===
                // LIKE con % su entrambi i lati = "contiene"
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('nome', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // === FILTRO PER CATEGORIA ===
        // Permette di filtrare i prodotti per una categoria specifica
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // === ESECUZIONE QUERY CON SELEZIONE CAMPI PUBBLICI ===
        // select() limita i campi restituiti per performance e sicurezza
        // Non include campi sensibili come costi interni, note staff, etc.
        $prodotti = $query->select([
                'id', 'nome', 'modello', 'descrizione', 
                'categoria', 'prezzo', 'foto'
            ])
            ->orderBy('nome') // Ordinamento alfabetico per UX
            ->paginate(12); // 12 prodotti per pagina (griglia 3x4)

        // === SISTEMA CATEGORIE UNIFICATO ===
        // Ottiene elenco categorie dai prodotti ATTIVI per dropdown filtro
        $categorieFromDB = Prodotto::where('attivo', true)
            ->distinct() // Rimuove duplicati
            ->whereNotNull('categoria') // Solo record con categoria valida
            ->orderBy('categoria') // Ordinamento alfabetico
            ->pluck('categoria') // Estrae solo i valori della colonna categoria
            ->toArray(); // Converte Collection in array PHP

        // Array finale delle categorie per la vista
        $categorie = $categorieFromDB;

        // === CALCOLO STATISTICHE PER CATEGORIA ===
        // Conta quanti prodotti ci sono per ogni categoria (per badge numerici)
        $perCategoriaStats = Prodotto::where('attivo', true)
            ->groupBy('categoria') // Raggruppa per categoria
            ->selectRaw('categoria, COUNT(*) as count') // Conta elementi per gruppo
            ->pluck('count', 'categoria') // Crea array [categoria => conteggio]
            ->toArray();

        // === STATISTICHE PUBBLICHE ===
        // Array con dati aggregati per la vista (NO malfunzionamenti per sicurezza)
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'categorie_count' => count($categorie),
            'per_categoria' => $perCategoriaStats, // Per badge numerici nel frontend
            'version' => 'pubblico' // Flag per identificare tipo vista
        ];

        // === LOGGING PER ANALYTICS ===
        // Log delle visualizzazioni pubbliche per statistiche d'uso
        Log::info('Catalogo pubblico caricato', [
            'search_term' => $request->input('search'),
            'categoria_filtro' => $request->input('categoria'),
            'prodotti_totali' => $prodotti->total(),
            'categorie_trovate' => count($categorie),
            'categorie_list' => $categorie,
            'stats_per_categoria' => $perCategoriaStats,
            'ip_address' => $request->ip(), // Per geolocalizzazione
            'user_agent' => $request->userAgent() // Per analytics dispositivi
        ]);

        // === RENDERIZZAZIONE VISTA BLADE ===
        // view() carica il template Blade e passa i dati
        return view('prodotti.pubblico.index', compact('prodotti', 'categorie', 'stats'))
            ->with('isPublicView', true) // Flag per template condizionale
            ->with('showMalfunzionamenti', false); // Nasconde sezioni tecniche
    }

    /**
     * METODO: showPubblico()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /catalogo/{prodotto} (pubblica)
     * ACCESSO: Tutti (non richiede autenticazione)
     * 
     * SCOPO:
     * Mostra la scheda dettagliata di un singolo prodotto per utenti pubblici.
     * Include tutte le informazioni tecniche BASE ma nasconde malfunzionamenti.
     * 
     * FUNZIONALITÀ:
     * - Visualizzazione completa scheda prodotto pubblica
     * - Controllo attivazione prodotto (404 se disattivo)
     * - Caricamento informazioni staff (solo nome, per trasparenza)
     * - Logging accessi per analytics
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Modello automaticamente risolto da Laravel
     *                            tramite Route Model Binding
     * 
     * SICUREZZA:
     * - Verifica che il prodotto sia attivo (nasconde quelli disattivati)
     * - Non carica informazioni sui malfunzionamenti
     * - Log degli accessi per monitoraggio
     */
    public function showPubblico(Prodotto $prodotto)
    {
        // === CONTROLLO DISPONIBILITÀ PUBBLICA ===
        // Verifica che il prodotto sia attivo e disponibile al pubblico
        if (!$prodotto->attivo) {
            // abort() genera una risposta HTTP 404 con messaggio personalizzato
            abort(404, 'Prodotto non disponibile');
        }

        // === EAGER LOADING DELLE RELAZIONI ===
        // load() carica relazioni in modo efficiente (prevenzione N+1 queries)
        // :id,nome,cognome = limita i campi caricati per performance
        $prodotto->load(['staffAssegnato:id,nome,cognome']);

        // === FLAG PER CONTROLLO TEMPLATE ===
        // Questi flag controllano cosa mostrare nel template Blade condiviso
        $showMalfunzionamenti = false; // NASCONDE sezione malfunzionamenti
        $isPublicView = true; // ATTIVA modalità pubblica nel template
        
        // === LOGGING ACCESSI PUBBLICI ===
        // Traccia visualizzazioni per analytics e monitoraggio prodotti popolari
        Log::info('Prodotto visualizzato da utente pubblico', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'modello' => $prodotto->modello,
            'categoria' => $prodotto->categoria,
            'ip' => request()->ip(), // IP per geolocalizzazione
            'timestamp' => now(), // Timestamp per trend temporali
            'referrer' => request()->header('referer') // Da dove arriva l'utente
        ]);
        
        // === RENDERIZZAZIONE VISTA SPECIFICA PUBBLICA ===
        return view('prodotti.pubblico.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
    }

    // ================================================
    // SEZIONE 2: METODI PER CATALOGO COMPLETO (Livello 2+ - Tecnici)
    // ================================================

    /**
     * METODO: indexCompleto()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti (richiede autenticazione)
     * ACCESSO: Tecnici (livello 2+)
     * 
     * SCOPO:
     * Catalogo completo per tecnici CON informazioni sui malfunzionamenti.
     * Include conteggi problemi, segnalazioni, filtri avanzati e staff assegnato.
     * 
     * DIFFERENZE DA indexPubblico():
     * - Richiede autenticazione e autorizzazione
     * - Mostra conteggi malfunzionamenti e problemi critici
     * - Include informazioni staff assegnato
     * - Filtri avanzati (prodotti critici, prodotti assegnati)
     * - Ordinamento intelligente per staff
     * 
     * FUNZIONALITÀ AVANZATE:
     * - Filtro "Miei Prodotti" per staff
     * - Filtro "Prodotti Critici" per emergenze
     * - Conteggi real-time malfunzionamenti
     * - Prioritizzazione prodotti assegnati
     * 
     * PARAMETRI:
     * @param Request $request - Richiesta con parametri:
     *   - search: ricerca testuale con wildcard
     *   - categoria: filtro categoria
     *   - staff_filter: 'my_products' per filtro staff
     *   - filter: 'critici' per prodotti con problemi critici
     */
    public function indexCompleto(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI TECNICI ===
        // Verifica che l'utente sia autenticato E abbia permessi tecnici (livello 2+)
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici autorizzati');
        }

        // === COSTRUZIONE QUERY BASE ===
        $query = Prodotto::where('attivo', true);
        $user = Auth::user(); // Utente autenticato corrente

        // === FILTRO SPECIALE STAFF: SOLO PRODOTTI ASSEGNATI ===
        // Permette ai membri dello staff di vedere solo i loro prodotti
        if ($request->filled('staff_filter') && $request->input('staff_filter') === 'my_products') {
            if ($user->isStaff()) {
                // where() filtra solo prodotti assegnati all'utente corrente
                $query->where('staff_assegnato_id', $user->id);
            } else {
                // Se non è staff, reindirizza con avviso
                return redirect()->route('prodotti.completo.index')
                    ->with('warning', 'Filtro "Miei Prodotti" disponibile solo per lo staff aziendale');
            }
        }

        // === FILTRO PER PRODOTTI CRITICI ===
        // Mostra solo prodotti che hanno malfunzionamenti critici attivi
        if ($request->filled('filter') && $request->input('filter') === 'critici') {
            // whereHas() filtra per esistenza di relazione con condizione
            $query->whereHas('malfunzionamenti', function($q) {
                $q->where('gravita', 'critica');
            });
        }

        // === RICERCA AVANZATA PER TECNICI ===
        // Stesso sistema wildcard del pubblico ma su più campi
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            if (str_ends_with($searchTerm, '*')) {
                // Ricerca "inizia con"
                $searchTerm = rtrim($searchTerm, '*');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', $searchTerm . '%')
                      ->orWhere('nome', 'LIKE', $searchTerm . '%')
                      ->orWhere('modello', 'LIKE', $searchTerm . '%');
                });
            } else {
                // Ricerca "contiene"
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

        // === ORDINAMENTO INTELLIGENTE PER STAFF ===
        // Se l'utente è staff, mostra prima i prodotti assegnati a lui
        if ($user->isStaff() && !$request->filled('staff_filter')) {
            // CASE WHEN in SQL: prodotti assegnati = 0 (primi), altri = 1 (dopo)
            $query->orderByRaw("CASE WHEN staff_assegnato_id = ? THEN 0 ELSE 1 END", [$user->id]);
        }

        // === CARICAMENTO DATI CON CONTEGGI MALFUNZIONAMENTI ===
        // withCount() aggiunge colonne virtuali con conteggi delle relazioni
        $prodotti = $query->withCount([
                'malfunzionamenti', // Conteggio totale malfunzionamenti
                'malfunzionamenti as critici_count' => function($query) {
                    // Conteggio solo malfunzionamenti critici
                    $query->where('gravita', 'critica');
                }
            ])
            ->with('staffAssegnato:id,nome,cognome') // Eager loading staff
            ->orderBy('nome') // Ordinamento secondario alfabetico
            ->paginate(12); // 12 risultati per pagina

        // === CARICAMENTO CATEGORIE UNIFICATE ===
        $categorie = $this->getCategorie(); // Metodo helper privato

        // === STATISTICHE AVANZATE PER TECNICI ===
        $stats = [
            'total_prodotti' => Prodotto::where('attivo', true)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
            'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
            'version' => 'completo' // Flag per identificare vista tecnica
        ];

        // === STATISTICHE AGGIUNTIVE PER STAFF ===
        if ($user->isStaff()) {
            $stats['miei_prodotti'] = Prodotto::where('staff_assegnato_id', $user->id)
                ->where('attivo', true)
                ->count();
        }

        // === DETERMINAZIONE VISTA DINAMICA ===
        // Usa vista specifica staff se filtro "my_products" attivo
        $view = $request->input('staff_filter') === 'my_products' ? 'prodotti.staff.index' : 'prodotti.completo.index';
        
        // Fallback alla vista completa se quella specifica non esiste
        if (!view()->exists($view)) {
            $view = 'prodotti.completo.index';
        }

        // === RENDERIZZAZIONE VISTA TECNICA ===
        return view($view, compact('prodotti', 'categorie', 'stats'))
            ->with('showMalfunzionamenti', true) // MOSTRA sezione malfunzionamenti
            ->with('isPublicView', false); // DISATTIVA modalità pubblica
    }

    /**
     * METODO: showCompleto()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti/{prodotto}/completo
     * ACCESSO: Tecnici (livello 2+)
     * 
     * SCOPO:
     * Visualizzazione completa di un prodotto per tecnici CON tutti i malfunzionamenti,
     * soluzioni tecniche, segnalazioni e informazioni staff.
     * 
     * DIFFERENZE DA showPubblico():
     * - Include TUTTI i malfunzionamenti con soluzioni
     * - Mostra segnalazioni e priorità
     * - Informazioni su chi ha creato ogni malfunzionamento
     * - Ordinamento intelligente per criticità
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Modello risolto automaticamente
     */
    public function showCompleto(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI TECNICI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici autorizzati');
        }

        // === CONTROLLO DISPONIBILITÀ ===
        if (!$prodotto->attivo) {
            abort(404, 'Prodotto non disponibile');
        }

        // === CARICAMENTO COMPLETO RELAZIONI ===
        $prodotto->load([
            // Carica malfunzionamenti con ordinamento intelligente
            'malfunzionamenti' => function($query) {
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')") // Prima i critici
                      ->orderBy('numero_segnalazioni', 'desc'); // Poi i più segnalati
            },
            // Carica info su chi ha creato ogni malfunzionamento
            'malfunzionamenti.creatoBy:id,nome,cognome',
            // Carica info staff assegnato al prodotto
            'staffAssegnato:id,nome,cognome'
        ]);

        // === FLAG PER VISTA TECNICA COMPLETA ===
        $showMalfunzionamenti = true; // MOSTRA tutti i malfunzionamenti
        $isPublicView = false; // DISATTIVA limitazioni pubbliche

        // === LOGGING ACCESSO TECNICO ===
        Log::info('Prodotto visualizzato da tecnico', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'user_id' => Auth::id(),
            'user_level' => Auth::user()->livello_accesso,
            'malfunzionamenti_count' => $prodotto->malfunzionamenti->count(),
            'critici_count' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count()
        ]);

        // === RENDERIZZAZIONE VISTA TECNICA COMPLETA ===
        return view('prodotti.completo.show', compact('prodotto', 'showMalfunzionamenti', 'isPublicView'));
    }

    // ================================================
    // SEZIONE 3: RICERCA AVANZATA PER TECNICI
    // ================================================

    /**
     * METODO: ricercaAvanzata()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti/ricerca-avanzata
     * ACCESSO: Tecnici (livello 2+)
     * 
     * SCOPO:
     * Sistema di ricerca avanzata specificatamente progettato per tecnici.
     * Include filtri multipli, ordinamento per criticità e risultati ottimizzati.
     * 
     * FUNZIONALITÀ AVANZATE:
     * - Ricerca wildcard su tutti i campi prodotto
     * - Filtro per prodotti con problemi critici
     * - Ordinamento per urgenza (critici primi)
     * - Paginazione estesa (15 risultati)
     * - Statistiche di ricerca real-time
     * 
     * ALGORITMO DI ORDINAMENTO:
     * 1. Prodotti con problemi critici (per urgenza)
     * 2. Prodotti con più malfunzionamenti (per esperienza)
     * 3. Ordine alfabetico (per consistenza)
     * 
     * PARAMETRI:
     * @param Request $request - Parametri di ricerca:
     *   - search: termine con supporto wildcard
     *   - categoria: filtro categoria
     *   - critici_only: boolean per solo prodotti critici
     */
    public function ricercaAvanzata(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato ai tecnici e staff');
        }

        // === BLOCCO TRY-CATCH PER GESTIONE ERRORI ===
        try {
            // === QUERY BASE PER PRODOTTI ATTIVI ===
            $query = Prodotto::where('attivo', true);

            // === RICERCA AVANZATA CON WILDCARD ===
            if ($request->filled('search')) {
                $searchTerm = trim($request->input('search')); // Rimuove spazi
                
                if (str_ends_with($searchTerm, '*')) {
                    // === RICERCA WILDCARD ESTESA ===
                    // "lav*" trova lavatrici, lavastoviglie, lavelli, etc.
                    $baseTerm = rtrim($searchTerm, '*');
                    $query->where(function($q) use ($baseTerm) {
                        $q->where('nome', 'LIKE', $baseTerm . '%')
                          ->orWhere('descrizione', 'LIKE', $baseTerm . '%')
                          ->orWhere('modello', 'LIKE', $baseTerm . '%')
                          ->orWhere('categoria', 'LIKE', $baseTerm . '%'); // Include anche categoria
                    });
                } else {
                    // === RICERCA NORMALE FULL-TEXT ===
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('categoria', 'LIKE', '%' . $searchTerm . '%');
                    });
                }
            }

            // === FILTRO PER CATEGORIA ===
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }

            // === FILTRO SOLO PROBLEMI CRITICI ===
            // boolean() converte string in boolean (checkbox frontend)
            if ($request->boolean('critici_only')) {
                $query->whereHas('malfunzionamenti', function($q) {
                    $q->where('gravita', 'critica');
                });
            }

            // === CARICAMENTO CON CONTEGGI E RELAZIONI ===
            $prodotti = $query->withCount([
                    'malfunzionamenti', // Conteggio totale
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica'); // Solo critici
                    }
                ])
                ->with('staffAssegnato:id,nome,cognome') // Staff info
                // === ORDINAMENTO INTELLIGENTE PER URGENZA ===
                ->orderBy('critici_count', 'desc') // Prima: più problemi critici
                ->orderBy('malfunzionamenti_count', 'desc') // Poi: più problemi totali
                ->orderBy('nome', 'asc') // Infine: alfabetico
                ->paginate(15) // 15 risultati (più di normale per ricerca)
                ->withQueryString(); // Mantiene parametri GET nella paginazione

            // === STATISTICHE DI RICERCA ===
            $stats = [
                'total_prodotti' => Prodotto::where('attivo', true)->count(),
                'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->where('attivo', true)->count(),
                'malfunzionamenti_critici' => \App\Models\Malfunzionamento::where('gravita', 'critica')->count(),
                'risultati_trovati' => $prodotti->total(), // Risultati della ricerca
                'version' => 'ricerca_avanzata'
            ];

            // === CATEGORIE PER FILTRO ===
            $categorie = $this->getCategorie();

            // === LOGGING RICERCA ===
            Log::info('Ricerca avanzata completata', [
                'search_term' => $request->input('search'),
                'categoria_filter' => $request->input('categoria'),
                'critici_only' => $request->boolean('critici_only'),
                'results_count' => $prodotti->total(),
                'user_id' => Auth::id(),
                'user_level' => Auth::user()->livello_accesso
            ]);

            // === RENDERIZZAZIONE RISULTATI ===
            return view('prodotti.completo.index', compact('prodotti', 'stats', 'categorie'))
                ->with('showMalfunzionamenti', true)
                ->with('isPublicView', false)
                ->with('isSearchResults', true); // Flag per template risultati ricerca

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ROBUSTA ===
            Log::error('Errore nella ricerca avanzata prodotti', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'search_params' => $request->all(),
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect con messaggio di errore user-friendly
            return redirect()->route('prodotti.completo.index')
                ->with('error', 'Errore durante la ricerca: ' . $e->getMessage());
        }
    }

    // ================================================
    // SEZIONE 4: METODI AMMINISTRATIVI
    // ================================================

    /**
     * METODO: index()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /admin/prodotti
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Lista completa prodotti per amministratori con funzionalità di gestione avanzata.
     * Include filtri multipli, ordinamento personalizzato e statistiche complete.
     * 
     * FUNZIONALITÀ AMMINISTRATIVE:
     * - Visualizzazione di TUTTI i prodotti (attivi e inattivi)
     * - Filtri per status attivazione
     * - Filtro per staff assegnato
     * - Filtro per categoria
     * - Ordinamento personalizzabile
     * - Statistiche complete del sistema
     * 
     * PARAMETRI:
     * @param Request $request - Parametri amministrativi:
     *   - status: 'attivi', 'inattivi', '' (tutti)
     *   - staff_id: ID staff assegnato, '0' per non assegnati
     *   - categoria: filtro categoria
     *   - search: ricerca testuale
     *   - sort: campo ordinamento
     *   - order: 'asc' o 'desc'
     */
    public function index(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ADMIN ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        // === QUERY BASE ADMIN (TUTTI I PRODOTTI) ===
        // Diversamente dalle altre viste, admin vede anche prodotti disattivati
        $query = Prodotto::query();

        // === FILTRO PER STATUS ATTIVAZIONE ===
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'attivi') {
                $query->where('attivo', true);
            } elseif ($status === 'inattivi') {
                $query->where('attivo', false);
            }
            // Se status non specificato, mostra tutti (attivi + inattivi)
        }

        // === FILTRO PER STAFF ASSEGNATO ===
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            
            // Gestione speciale per "non assegnati"
            if ($staffId === '0' || $staffId === 0) {
                $query->whereNull('staff_assegnato_id');
            } else {
                $query->where('staff_assegnato_id', $staffId);
            }
        }

        // === FILTRO PER CATEGORIA ===
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // === FILTRO RICERCA TESTUALE ===
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('modello', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // === ORDINAMENTO PERSONALIZZABILE ===
        $sortBy = $request->input('sort', 'created_at'); // Default: data creazione
        $sortOrder = $request->input('order', 'desc'); // Default: più recenti prima
        $query->orderBy($sortBy, $sortOrder);

        // === ESECUZIONE QUERY ADMIN ===
        $prodotti = $query->withCount('malfunzionamenti') // Conteggio problemi
            ->with('staffAssegnato:id,nome,cognome') // Info staff
            ->paginate(15) // 15 risultati per pagina admin
            ->withQueryString(); // Mantiene filtri nella paginazione

        // === CARICAMENTO STAFF MEMBERS PER FILTRO ===
        $staffMembers = User::where('livello_accesso', '3') // Solo staff (livello 3)
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // === CATEGORIE PER FILTRO ===
        $categorie = $this->getCategorie();

        // === STATISTICHE COMPLETE ADMIN ===
        $stats = [
            'total_prodotti' => Prodotto::count(), // TUTTI i prodotti
            'attivi' => Prodotto::where('attivo', true)->count(),
            'inattivi' => Prodotto::where('attivo', false)->count(),
            'con_malfunzionamenti' => Prodotto::whereHas('malfunzionamenti')->count(),
            'non_assegnati' => Prodotto::whereNull('staff_assegnato_id')->count(),
        ];

        // === RENDERIZZAZIONE VISTA ADMIN ===
        return view('admin.prodotti.index', compact('prodotti', 'staffMembers', 'stats', 'categorie'));
    }

    /**
     * METODO: categoria()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /categoria/{categoria}
     * ACCESSO: Variabile (pubblico + tecnici)
     * 
     * SCOPO:
     * Visualizza prodotti filtrati per una categoria specifica.
     * La vista cambia in base al livello di autorizzazione dell'utente.
     * 
     * LOGICA ADAPTIVE:
     * - Utenti pubblici: vista base senza malfunzionamenti
     * - Tecnici/Staff: vista completa con malfunzionamenti
     * - Validazione categoria tramite sistema unificato
     * 
     * PARAMETRI:
     * @param string $categoria - Slug della categoria da visualizzare
     */
    public function categoria($categoria)
    {
        // === VALIDAZIONE CATEGORIA ===
        // Verifica che la categoria sia valida usando il sistema unificato
        $categorieDisponibili = $this->getCategorie();
        if (!array_key_exists($categoria, $categorieDisponibili)) {
            abort(404, 'Categoria non trovata');
        }

        // === QUERY BASE PER CATEGORIA ===
        $query = Prodotto::where('categoria', $categoria)->where('attivo', true);
        
        // === DETERMINAZIONE LIVELLO AUTORIZZAZIONE ===
        $canViewMalfunctions = Auth::check() && Auth::user()->canViewMalfunzionamenti();
        
        if ($canViewMalfunctions) {
            // === VISTA TECNICA: Include conteggi malfunzionamenti ===
            $prodotti = $query->withCount([
                    'malfunzionamenti',
                    'malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }
                ])
                ->with('staffAssegnato:id,nome,cognome')
                ->paginate(12);
        } else {
            // === VISTA PUBBLICA: Solo dati base ===
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->paginate(12);
        }
        
        // === CARICAMENTO CATEGORIE ===
        $categorie = $categorieDisponibili;

        // === STATISTICHE PER CATEGORIA ===
        $stats = [
            'total_prodotti' => $prodotti->total(),
            'categoria_corrente' => $categoria,
            'categoria_label' => $categorieDisponibili[$categoria],
            'version' => $canViewMalfunctions ? 'completo' : 'pubblico'
        ];

        // === DETERMINAZIONE VISTA DINAMICA ===
        $view = $canViewMalfunctions ? 'prodotti.completo.index' : 'prodotti.pubblico.index';

        return view($view, compact('prodotti', 'categorie', 'stats'))
            ->with('showMalfunzionamenti', $canViewMalfunctions)
            ->with('isPublicView', !$canViewMalfunctions);
    }

    // ================================================
    // SEZIONE 5: METODI CRUD AMMINISTRATIVI
    // ================================================

    /**
     * METODO: create()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /admin/prodotti/create
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Mostra il form per creare un nuovo prodotto utilizzando il sistema
     * categorie unificato e caricando i membri staff disponibili.
     * 
     * FUNZIONALITÀ:
     * - Form con tutti i campi prodotto
     * - Dropdown categorie dal sistema unificato
     * - Selezione staff per assegnazione
     * - Logging per audit trail
     */
    public function create()
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a creare prodotti');
        }

        // === CARICAMENTO STAFF MEMBERS ===
        // Solo utenti con livello_accesso = 3 (staff)
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // === CARICAMENTO CATEGORIE UNIFICATE ===
        // getCategorieUnifico() è il metodo statico del modello Prodotto
        $categorie = Prodotto::getCategorieUnifico();

        // === LOGGING FORM ACCESSO ===
        Log::info('Form creazione prodotto caricato', [
            'admin_id' => Auth::id(),
            'admin_username' => Auth::user()->username,
            'staff_disponibili' => $staffMembers->count(),
            'categorie_disponibili' => count($categorie),
            'timestamp' => now()
        ]);

        // === RENDERIZZAZIONE FORM ===
        return view('admin.prodotti.create', compact('staffMembers', 'categorie'));
    }

    /**
     * METODO: store()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /admin/prodotti
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Salva un nuovo prodotto nel database dopo validazione completa.
     * Include gestione upload immagini e validazione categoria.
     * 
     * FUNZIONALITÀ:
     * - Validazione completa tutti i campi
     * - Verifica categoria nel sistema unificato
     * - Upload e storage immagine prodotto
     * - Creazione record con flag attivo=true
     * - Logging dettagliato operazione
     * 
     * PARAMETRI:
     * @param Request $request - Dati del form con tutti i campi prodotto
     */
    public function store(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        // === VALIDAZIONE COMPLETA DATI ===
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'modello' => 'required|string|max:255|unique:prodotti', // Modello deve essere unico
            'descrizione' => 'required|string',
            'categoria' => 'required|string|max:100',
            'note_tecniche' => 'required|string',
            'modalita_installazione' => 'required|string',
            'modalita_uso' => 'nullable|string',
            'prezzo' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'staff_assegnato_id' => 'nullable|exists:users,id',
        ], [
            // === MESSAGGI ERRORE PERSONALIZZATI ===
            'nome.required' => 'Il nome del prodotto è obbligatorio',
            'modello.required' => 'Il modello è obbligatorio',
            'modello.unique' => 'Questo modello esiste già',
            'categoria.required' => 'La categoria è obbligatoria',
            'note_tecniche.required' => 'Le note tecniche sono obbligatorie',
            'modalita_installazione.required' => 'Le modalità di installazione sono obbligatorie',
            'foto.image' => 'Il file deve essere un\'immagine',
            'foto.max' => 'L\'immagine non può superare 2MB',
        ]);

        // === VALIDAZIONE CATEGORIA NEL SISTEMA UNIFICATO ===
        $categorieDisponibili = Prodotto::getCategorieUnifico();
        if (!array_key_exists($validated['categoria'], $categorieDisponibili)) {
            return back()->withErrors([
                'categoria' => 'La categoria selezionata non è valida.'
            ])->withInput();
        }

        // === GESTIONE UPLOAD FOTO ===
        if ($request->hasFile('foto')) {
            // store() salva il file nella directory 'prodotti' del disk 'public'
            // Restituisce il path relativo per il database
            $validated['foto'] = $request->file('foto')->store('prodotti', 'public');
        }

        // === CREAZIONE PRODOTTO ===
        $prodotto = Prodotto::create(array_merge($validated, [
            'attivo' => true // Nuovi prodotti sono sempre attivi
        ]));

        // === LOGGING CREAZIONE ===
        Log::info('Nuovo prodotto creato', [
            'prodotto_id' => $prodotto->id,
            'nome' => $prodotto->nome,
            'modello' => $prodotto->modello,
            'categoria' => $prodotto->categoria,
            'created_by' => Auth::id(),
            'created_by_name' => Auth::user()->nome_completo,
            'staff_assegnato' => $prodotto->staff_assegnato_id
        ]);

        // === REDIRECT CON SUCCESSO ===
        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', 'Prodotto "' . $prodotto->nome . '" creato con successo');
    }

    /**
     * METODO: edit()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /admin/prodotti/{prodotto}/edit
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Mostra il form di modifica per un prodotto esistente con dati pre-popolati.
     * Include categorie unificate e staff disponibili.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Modello risolto automaticamente
     */
    public function edit(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato a modificare prodotti');
        }

        // === CARICAMENTO STAFF MEMBERS ===
        $staffMembers = User::where('livello_accesso', '3')
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // === CARICAMENTO CATEGORIE UNIFICATE ===
        $categorie = Prodotto::getCategorieUnifico();

        // === LOGGING ACCESSO MODIFICA ===
        Log::info('Form modifica prodotto caricato', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'categoria_attuale' => $prodotto->categoria,
            'admin_id' => Auth::id(),
            'categorie_disponibili' => count($categorie)
        ]);

        // === RENDERIZZAZIONE FORM MODIFICA ===
        return view('admin.prodotti.edit', compact('prodotto', 'staffMembers', 'categorie'));
    }

    /**
     * METODO: update()
     * TIPO: PUT/PATCH REQUEST HANDLER
     * ROUTE: PUT /admin/prodotti/{prodotto}
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Aggiorna un prodotto esistente nel database dopo validazione.
     * Gestisce upload nuova immagine e sostituzione di quella esistente.
     * 
     * PARAMETRI:
     * @param Request $request - Dati aggiornati del form
     * @param Prodotto $prodotto - Prodotto da aggiornare
     */
    public function update(Request $request, Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        // === VALIDAZIONE CON ESCLUSIONE UNIQUE ===
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            // unique:prodotti,modello,{id} = esclude il prodotto corrente dal controllo unique
            'modello' => 'required|string|max:255|unique:prodotti,modello,' . $prodotto->id,
            'descrizione' => 'required|string',
            'categoria' => 'required|string|max:100',
            'note_tecniche' => 'required|string',
            'modalita_installazione' => 'required|string',
            'modalita_uso' => 'nullable|string',
            'prezzo' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'staff_assegnato_id' => 'nullable|exists:users,id',
            'attivo' => 'boolean', // Checkbox per attivazione/disattivazione
        ]);

        // === VALIDAZIONE CATEGORIA ===
        $categorieDisponibili = Prodotto::getCategorieUnifico();
        if (!array_key_exists($validated['categoria'], $categorieDisponibili)) {
            return back()->withErrors([
                'categoria' => 'La categoria selezionata non è valida.'
            ])->withInput();
        }

        // === GESTIONE SOSTITUZIONE FOTO ===
        if ($request->hasFile('foto')) {
            // Se esiste una foto precedente, eliminala
            if ($prodotto->foto) {
                Storage::disk('public')->delete($prodotto->foto);
            }
            // Salva la nuova foto
            $validated['foto'] = $request->file('foto')->store('prodotti', 'public');
        }

        // === AGGIORNAMENTO DATABASE ===
        $prodotto->update($validated);

        // === LOGGING AGGIORNAMENTO ===
        Log::info('Prodotto aggiornato', [
            'prodotto_id' => $prodotto->id,
            'modello' => $prodotto->modello,
            'categoria' => $prodotto->categoria,
            'updated_by' => Auth::id(),
            'updated_by_name' => Auth::user()->nome_completo,
            'changes' => $prodotto->getChanges() // Laravel traccia i campi modificati
        ]);

        // === REDIRECT CON SUCCESSO ===
        return redirect()->route('admin.prodotti.show', $prodotto)
            ->with('success', 'Prodotto "' . $prodotto->nome . '" aggiornato con successo');
    }

    /**
     * METODO: show()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /admin/prodotti/{prodotto}
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Visualizza tutti i dettagli di un prodotto per amministratori,
     * inclusi malfunzionamenti completi con metadati di creazione/modifica.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da visualizzare
     */
    public function show(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        // === CARICAMENTO COMPLETO RELAZIONI ADMIN ===
        $prodotto->load([
            // Malfunzionamenti con info complete su creator/modifier
            'malfunzionamenti.creatoBy:id,nome,cognome',
            'malfunzionamenti.modificatoBy:id,nome,cognome',
            // Staff assegnato
            'staffAssegnato:id,nome,cognome'
        ]);

        // === RENDERIZZAZIONE VISTA ADMIN ===
        return view('admin.prodotti.show', compact('prodotto'));
    }

    /**
     * METODO: destroy()
     * TIPO: DELETE REQUEST HANDLER
     * ROUTE: DELETE /admin/prodotti/{prodotto}
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Elimina definitivamente un prodotto dal sistema.
     * ATTENZIONE: Elimina anche tutti i malfunzionamenti associati e i file immagine.
     * 
     * FUNZIONALITÀ:
     * - Eliminazione file immagine dal storage
     * - Eliminazione malfunzionamenti associati
     * - Eliminazione record prodotto
     * - Logging completo per audit
     * - Gestione errori robusta
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da eliminare
     */
    public function destroy(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato ad eliminare prodotti');
        }

        // === BLOCCO TRY-CATCH PER GESTIONE ERRORI ===
        try {
            // === SALVATAGGIO DATI PER LOGGING ===
            // Salva informazioni prima dell'eliminazione per audit trail
            $prodottoNome = $prodotto->nome;
            $prodottoModello = $prodotto->modello;
            $prodottoId = $prodotto->id;

            // === GESTIONE FILE IMMAGINE ===
            // Elimina la foto del prodotto se esiste nel storage
            if ($prodotto->foto) {
                Storage::disk('public')->delete($prodotto->foto);
                Log::info('Foto prodotto eliminata', [
                    'prodotto_id' => $prodottoId,
                    'foto_path' => $prodotto->foto
                ]);
            }

            // === GESTIONE RELAZIONI CASCADE ===
            // Elimina tutti i malfunzionamenti associati al prodotto
            // IMPORTANTE: Questo elimina anche le soluzioni tecniche associate
            $malfunzionamentiCount = $prodotto->malfunzionamenti()->count();
            if ($malfunzionamentiCount > 0) {
                $prodotto->malfunzionamenti()->delete();
                Log::info('Malfunzionamenti del prodotto eliminati', [
                    'prodotto_id' => $prodottoId,
                    'malfunzionamenti_eliminati' => $malfunzionamentiCount
                ]);
            }

            // === ELIMINAZIONE PRODOTTO ===
            // Elimina definitivamente il record dal database
            $prodotto->delete();

            // === LOGGING COMPLETO OPERAZIONE ===
            Log::warning('Prodotto eliminato definitivamente', [
                'prodotto_id' => $prodottoId,
                'prodotto_nome' => $prodottoNome,
                'prodotto_modello' => $prodottoModello,
                'deleted_by_admin_id' => Auth::id(),
                'deleted_by_admin_username' => Auth::user()->username,
                'deleted_by_admin_name' => Auth::user()->nome_completo,
                'malfunzionamenti_eliminati' => $malfunzionamentiCount,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);

            // === REDIRECT CON MESSAGGIO SUCCESSO ===
            return redirect()->route('admin.prodotti.index')
                ->with('success', "Prodotto \"{$prodottoNome}\" eliminato definitivamente dal sistema.");

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
            // Log dell'errore per debugging e monitoring
            Log::error('Errore nell\'eliminazione prodotto', [
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
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
     * METODO: softDestroy()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /admin/prodotti/{prodotto}/soft-delete
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Disattiva un prodotto senza eliminarlo (soft delete).
     * Utile per rimuovere temporaneamente prodotti dal catalogo.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da disattivare
     */
    public function softDestroy(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        // === DISATTIVAZIONE PRODOTTO ===
        // Imposta attivo=false invece di eliminare il record
        $prodotto->update(['attivo' => false]);

        // === LOGGING SOFT DELETE ===
        Log::info('Prodotto disattivato (soft delete)', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'modello' => $prodotto->modello,
            'deactivated_by' => Auth::id(),
            'deactivated_by_name' => Auth::user()->nome_completo
        ]);

        // === REDIRECT CON MESSAGGIO ===
        return redirect()->route('admin.prodotti.index')
            ->with('success', 'Prodotto "' . $prodotto->nome . '" rimosso dal catalogo (può essere riattivato)');
    }

    /**
     * METODO: confirmDelete()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /admin/prodotti/{prodotto}/confirm-delete
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Mostra una pagina di conferma prima dell'eliminazione definitiva.
     * Approccio più sicuro per operazioni irreversibili.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da confermare per eliminazione
     */
    public function confirmDelete(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Non autorizzato');
        }

        // === CALCOLO DATI AGGIUNTIVI PER CONFERMA ===
        $relatedData = [
            'malfunzionamenti_count' => $prodotto->malfunzionamenti()->count(),
            'staff_assegnato' => $prodotto->staffAssegnato,
            'created_at' => $prodotto->created_at,
            'last_modified' => $prodotto->updated_at,
            'total_segnalazioni' => $prodotto->malfunzionamenti()->sum('numero_segnalazioni')
        ];

        // === RENDERIZZAZIONE PAGINA CONFERMA ===
        return view('admin.prodotti.confirm-delete', compact('prodotto', 'relatedData'));
    }

    /**
     * METODO: restore()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /admin/prodotti/{prodotto}/restore
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Riattiva un prodotto precedentemente disattivato (soft delete).
     * Supporta sia richieste web che AJAX.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da riattivare
     */
    public function restore(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }
            abort(403, 'Non autorizzato');
        }

        // === BLOCCO TRY-CATCH ===
        try {
            // === RIATTIVAZIONE PRODOTTO ===
            $prodotto->update(['attivo' => true]);

            // === LOGGING RIPRISTINO ===
            Log::info('Prodotto ripristinato', [
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'restored_by' => Auth::id(),
                'restored_by_name' => Auth::user()->nome_completo,
                'timestamp' => now()
            ]);

            $successMessage = "Prodotto \"{$prodotto->nome}\" ripristinato nel catalogo";

            // === GESTIONE RISPOSTA DUAL (WEB + AJAX) ===
            if (request()->expectsJson()) {
                // Risposta JSON per richieste AJAX
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

            // Risposta redirect per richieste web normali
            return redirect()->route('admin.prodotti.show', $prodotto)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * METODO: forceDelete()
     * TIPO: DELETE REQUEST HANDLER
     * ROUTE: DELETE /admin/prodotti/{prodotto}/force
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Eliminazione fisica definitiva con gestione transazioni database.
     * ATTENZIONE: Operazione irreversibile - elimina tutto definitivamente.
     * 
     * CARATTERISTICHE AVANZATE:
     * - Uso di transazioni database per operazioni atomiche
     * - Eliminazione cascata di tutte le relazioni
     * - Gestione file storage
     * - Logging di sicurezza dettagliato
     * - Supporto richieste AJAX
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da eliminare definitivamente
     */
    public function forceDelete(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }
            abort(403, 'Non autorizzato');
        }

        // === BLOCCO TRY-CATCH CON TRANSAZIONI ===
        try {
            // === INIZIO TRANSAZIONE DATABASE ===
            // Assicura che tutte le operazioni vengano completate o annullate insieme
            DB::beginTransaction();

            // === SALVATAGGIO INFO PRE-ELIMINAZIONE ===
            $prodottoInfo = [
                'id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'modello' => $prodotto->modello,
                'categoria' => $prodotto->categoria
            ];

            // === CONTEGGIO RELAZIONI DA ELIMINARE ===
            $malfunzionamentiCount = $prodotto->malfunzionamenti()->count();
            
            // === ELIMINAZIONE MALFUNZIONAMENTI ASSOCIATI ===
            $prodotto->malfunzionamenti()->delete();

            // === ELIMINAZIONE FILE IMMAGINE ===
            if ($prodotto->foto && Storage::disk('public')->exists($prodotto->foto)) {
                Storage::disk('public')->delete($prodotto->foto);
            }

            // === ELIMINAZIONE FISICA DAL DATABASE ===
            $prodotto->delete();

            // === COMMIT TRANSAZIONE ===
            // Se arriviamo qui, tutte le operazioni sono andate a buon fine
            DB::commit();

            // === LOGGING DI SICUREZZA ===
            Log::warning('Prodotto eliminato DEFINITIVAMENTE', [
                'prodotto_info' => $prodottoInfo,
                'malfunzionamenti_eliminati' => $malfunzionamentiCount,
                'eliminated_by' => Auth::id(),
                'admin_name' => Auth::user()->nome_completo,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $successMessage = "Prodotto \"{$prodottoInfo['nome']}\" eliminato definitivamente dal sistema";

            // === GESTIONE RISPOSTA DUAL ===
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
            // === ROLLBACK TRANSAZIONE IN CASO DI ERRORE ===
            DB::rollBack();

            // === LOGGING ERRORE ===
            Log::error('Errore eliminazione definitiva prodotto', [
                'prodotto_id' => $prodotto->id,
                'error' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
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
     * METODO: toggleStatus()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /admin/prodotti/{prodotto}/toggle-status
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Cambia rapidamente lo status attivo/inattivo di un prodotto.
     * Utile per operazioni rapide di attivazione/disattivazione.
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto di cui cambiare lo status
     */
    public function toggleStatus(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // === BLOCCO TRY-CATCH ===
        try {
            // === TOGGLE STATUS ===
            // Inverte lo status corrente (true -> false, false -> true)
            $newStatus = !$prodotto->attivo;
            $prodotto->update(['attivo' => $newStatus]);

            // === DETERMINAZIONE AZIONE ===
            $action = $newStatus ? 'attivato' : 'disattivato';

            // === LOGGING TOGGLE ===
            Log::info("Prodotto {$action} dall'admin", [
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'modello' => $prodotto->modello,
                'new_status' => $newStatus,
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->nome_completo
            ]);

            // === REDIRECT CON MESSAGGIO ===
            return redirect()->route('admin.prodotti.index')
                ->with('success', 'Prodotto "' . $prodotto->nome . '" aggiornato con successo');

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * METODO: bulkAction()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /admin/prodotti/bulk-action
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Gestisce azioni multiple su gruppi di prodotti selezionati.
     * Supporta attivazione, disattivazione ed eliminazione di massa.
     * 
     * FUNZIONALITÀ:
     * - Validazione rigorosa input
     * - Operazioni batch efficienti
     * - Logging dettagliato per audit
     * - Gestione errori granulare
     * - Limite di sicurezza (max 50 prodotti)
     * 
     * PARAMETRI:
     * @param Request $request - Parametri azione bulk:
     *   - action: 'activate', 'deactivate', 'delete'
     *   - products: array di ID prodotti selezionati
     */
    public function bulkAction(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato ad eseguire questa azione'
            ], 403);
        }

        // === VALIDAZIONE INPUT RIGOROSA ===
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'products' => 'required|array|min:1|max:50', // Limite di sicurezza
            'products.*' => 'required|integer|exists:prodotti,id'
        ], [
            // === MESSAGGI ERRORE SPECIFICI ===
            'action.required' => 'Azione non specificata',
            'action.in' => 'Azione non valida',
            'products.required' => 'Nessun prodotto selezionato',
            'products.min' => 'Seleziona almeno un prodotto',
            'products.max' => 'Troppi prodotti selezionati (max 50)',
            'products.*.exists' => 'Uno o più prodotti non esistono'
        ]);

        // === BLOCCO TRY-CATCH PER GESTIONE ERRORI ===
        try {
            $productIds = $validated['products'];
            $action = $validated['action'];
            $count = 0;
            $message = '';

            // === LOGGING INIZIO OPERAZIONE ===
            Log::info('Inizio azione bulk sui prodotti', [
                'action' => $action,
                'product_ids' => $productIds,
                'total_products' => count($productIds),
                'admin_id' => Auth::id(),
                'admin_username' => Auth::user()->username
            ]);

            // === SWITCH PER AZIONI DIVERSE ===
            switch ($action) {
                case 'activate':
                    // === ATTIVAZIONE BULK ===
                    // whereIn() + where() = aggiorna solo quelli attualmente disattivati
                    $count = Prodotto::whereIn('id', $productIds)
                        ->where('attivo', false) // Solo quelli disattivati
                        ->update(['attivo' => true]);
                    $message = "Attivati {$count} prodotti con successo";
                    break;

                case 'deactivate':
                    // === DISATTIVAZIONE BULK ===
                    $count = Prodotto::whereIn('id', $productIds)
                        ->where('attivo', true) // Solo quelli attivi
                        ->update(['attivo' => false]);
                    $message = "Disattivati {$count} prodotti con successo";
                    break;

                case 'delete':
                    // === ELIMINAZIONE BULK ===
                    // Operazione più complessa che richiede gestione file e relazioni
                    $prodottiDaEliminare = Prodotto::whereIn('id', $productIds)->get();
                    
                    foreach ($prodottiDaEliminare as $prodotto) {
                        // === ELIMINAZIONE FILE IMMAGINE ===
                        if ($prodotto->foto && Storage::disk('public')->exists($prodotto->foto)) {
                            Storage::disk('public')->delete($prodotto->foto);
                        }
                        
                        // === ELIMINAZIONE MALFUNZIONAMENTI ===
                        $prodotto->malfunzionamenti()->delete();
                        
                        // === ELIMINAZIONE PRODOTTO ===
                        $prodotto->delete();
                        $count++;
                    }
                    
                    $message = "Eliminati {$count} prodotti definitivamente";
                    break;
            }

            // === LOGGING SUCCESSO OPERAZIONE ===
            Log::warning('Azione bulk completata', [
                'action' => $action,
                'products_affected' => $count,
                'total_requested' => count($productIds),
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->nome_completo,
                'timestamp' => now()
            ]);

            // === RISPOSTA JSON SUCCESSO ===
            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $count,
                'action' => $action,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // === GESTIONE ERRORI VALIDAZIONE ===
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI GENERICI ===
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
    // SEZIONE 6: API ENDPOINTS
    // ================================================

    /**
     * METODO: apiSearch()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/prodotti/search
     * ACCESSO: Pubblico
     * 
     * SCOPO:
     * API pubblica per ricerca prodotti tramite AJAX.
     * Utilizzata da frontend JavaScript per ricerche real-time.
     * 
     * FUNZIONALITÀ:
     * - Ricerca con supporto wildcard
     * - Limite risultati per performance
     * - Formato JSON ottimizzato
     * - Gestione errori graceful
     * - Sistema categorie unificato
     * 
     * PARAMETRI:
     * @param Request $request - Parametri API:
     *   - q: termine di ricerca (required, min 1 char)
     */
    public function apiSearch(Request $request)
    {
        // === BLOCCO TRY-CATCH PER API ===
        try {
            // === VALIDAZIONE INPUT API ===
            $request->validate([
                'q' => 'required|string|min:1|max:100',
            ]);

            $searchTerm = trim($request->input('q'));
            $query = Prodotto::where('attivo', true);

            // === IMPLEMENTA RICERCA CON WILDCARD ===
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

            // === ESECUZIONE QUERY LIMITATA ===
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->limit(20) // Limite per performance API
                ->get();

            // === CARICAMENTO ETICHETTE CATEGORIE ===
            $categorieLabels = $this->getCategorie();

            // === TRASFORMAZIONE DATI PER API ===
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

            // === RISPOSTA JSON STRUTTURATA ===
            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $results->count(),
                'search_term' => $request->input('q'),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI API ===
            Log::error('Errore in apiSearch', [
                'error' => $e->getMessage(),
                'search_term' => $request->input('q', 'N/A'),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nella ricerca prodotti'
            ], 500);
        }
    }

    /**
     * METODO: apiSearchTech()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/prodotti/search-tech
     * ACCESSO: Tecnici (livello 2+)
     * 
     * SCOPO:
     * API ricerca avanzata per tecnici con informazioni sui malfunzionamenti.
     * Include conteggi problemi critici e info staff.
     * 
     * DIFFERENZE DA apiSearch():
     * - Richiede autenticazione
     * - Include dati malfunzionamenti
     * - Mostra info staff assegnato
     * - Conteggi problemi critici
     * 
     * PARAMETRI:
     * @param Request $request - Parametri API ricerca tecnica
     */
    public function apiSearchTech(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONE API ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json([
                'success' => false,
                'error' => 'Accesso riservato ai tecnici'
            ], 403);
        }

        // === BLOCCO TRY-CATCH ===
        try {
            // === VALIDAZIONE INPUT ===
            $request->validate([
                'q' => 'required|string|min:1|max:100',
            ]);

            $searchTerm = trim($request->input('q'));
            $query = Prodotto::where('attivo', true);

            // === RICERCA CON WILDCARD ===
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

            // === CARICAMENTO CON CONTEGGI TECNICI ===
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

            // === ETICHETTE CATEGORIE ===
            $categorieLabels = $this->getCategorie();

            // === TRASFORMAZIONE PER VISTA TECNICA ===
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

            // === RISPOSTA JSON TECNICA ===
            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $results->count(),
                'search_term' => $request->input('q'),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * METODO: apiIndexPubblico()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/prodotti
     * ACCESSO: Pubblico
     * 
     * SCOPO:
     * API per ottenere lista paginata di prodotti pubblici.
     * Supporta filtri e paginazione per frontend dinamici.
     * 
     * FUNZIONALITÀ:
     * - Paginazione configurabile
     * - Filtro per categoria
     * - Formato JSON ottimizzato
     * - Metadata di paginazione
     * 
     * PARAMETRI:
     * @param Request $request - Parametri API:
     *   - categoria: filtro categoria opzionale
     *   - per_page: elementi per pagina (max 50)
     */
    public function apiIndexPubblico(Request $request)
    {
        // === BLOCCO TRY-CATCH ===
        try {
            $query = Prodotto::where('attivo', true);

            // === FILTRO CATEGORIA ===
            if ($request->filled('categoria')) {
                $categoria = $request->input('categoria');
                
                // Verifica validità categoria
                $categorieDisponibili = $this->getCategorie();
                if (array_key_exists($categoria, $categorieDisponibili)) {
                    $query->where('categoria', $categoria);
                }
            }

            // === PAGINAZIONE CONFIGURABILE ===
            $perPage = min($request->input('per_page', 12), 50); // Max 50 per sicurezza
            
            $prodotti = $query->select([
                    'id', 'nome', 'modello', 'descrizione', 
                    'categoria', 'prezzo', 'foto'
                ])
                ->orderBy('nome')
                ->paginate($perPage);

            // === ETICHETTE CATEGORIE ===
            $categorieLabels = $this->getCategorie();

            // === TRASFORMAZIONE COLLEZIONE ===
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

            // === RISPOSTA CON METADATA PAGINAZIONE ===
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
            // === GESTIONE ERRORI ===
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
     * METODO: apiShowPubblico()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/prodotti/{id}
     * ACCESSO: Pubblico
     * 
     * SCOPO:
     * API per ottenere dettagli di un singolo prodotto.
     * Include informazioni complete ma nasconde dati sensibili.
     * 
     * PARAMETRI:
     * @param int $id - ID del prodotto da recuperare
     */
    public function apiShowPubblico($id)
    {
        // === BLOCCO TRY-CATCH ===
        try {
            $prodotto = Prodotto::where('attivo', true)->findOrFail($id);

            // === ETICHETTE CATEGORIE ===
            $categorieLabels = $this->getCategorie();

            // === COSTRUZIONE DATI PUBBLICI ===
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

            // === AGGIUNTA DATI TECNICI SE AUTORIZZATO ===
            if (Auth::check() && Auth::user()->canViewMalfunzionamenti()) {
                $data['malfunzionamenti_count'] = $prodotto->malfunzionamenti()->count();
            }

            // === RISPOSTA JSON ===
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // === GESTIONE PRODOTTO NON TROVATO ===
            return response()->json([
                'success' => false,
                'error' => 'Prodotto non trovato'
            ], 404);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI GENERICI ===
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
    // SEZIONE 7: METODI HELPER PRIVATI - SISTEMA CATEGORIE UNIFICATO
    // ================================================

    /**
     * METODO: getCategorie()
     * TIPO: HELPER PRIVATO
     * SCOPO: Ottiene l'elenco delle categorie usando il sistema unificato
     * 
     * QUESTO È IL CUORE DEL SISTEMA CATEGORIE UNIFICATO:
     * - Usa SEMPRE il metodo statico del modello Prodotto
     * - Garantisce coerenza in tutto il sistema
     * - Fallback di sicurezza in caso di errori
     * - Logging per debugging problemi categorie
     * 
     * RETURN: array associativo [chiave => etichetta]
     * Esempio: ['lavatrice' => 'Lavatrici', 'forno' => 'Forni']
     */
    private function getCategorie(): array
    {
        try {
            // === USA SEMPRE IL SISTEMA UNIFICATO ===
            // getCategorieUnifico() è definito nel modello Prodotto
            // e garantisce coerenza in tutto il sistema
            return Prodotto::getCategorieUnifico();
            
        } catch (\Exception $e) {
            // === LOGGING ERRORE CATEGORIE ===
            Log::error('Errore nel recupero categorie unificate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'called_from' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown'
            ]);
            
            // === FALLBACK CON CATEGORIE DI BASE ===
            // In caso di errore, restituisce categorie hardcoded di base
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
     * METODO: getCategorieDisponibili()
     * TIPO: HELPER PRIVATO
     * SCOPO: Ottiene solo le categorie presenti effettivamente nel database
     * 
     * DIFFERENZA DA getCategorie():
     * - getCategorie() = TUTTE le categorie possibili del sistema
     * - getCategorieDisponibili() = solo categorie con prodotti esistenti
     * 
     * UTILIZZO: Dropdown filtri (mostra solo categorie con prodotti)
     * 
     * RETURN: array associativo delle categorie con prodotti
     */
    private function getCategorieDisponibili(): array
    {
        try {
            // === USA METODO STATICO DEL MODELLO ===
            // Se il modello ha il metodo, usalo per coerenza
            return Prodotto::getCategorieDisponibili();
            
        } catch (\Exception $e) {
            // === LOGGING ERRORE ===
            Log::error('Errore nel recupero categorie disponibili', [
                'error' => $e->getMessage()
            ]);
            
            // === FALLBACK: QUERY DIRETTA ===
            // Se il metodo del modello fallisce, usa query diretta
            $categoriePresenti = Prodotto::where('attivo', true)
                ->distinct() // Rimuove duplicati
                ->whereNotNull('categoria') // Solo record con categoria
                ->pluck('categoria') // Estrae solo i valori categoria
                ->toArray();
                
            // === COMBINA CON ETICHETTE COMPLETE ===
            $categorieComplete = $this->getCategorie();
            
            $result = [];
            foreach ($categoriePresenti as $categoria) {
                // Usa etichetta dal sistema unificato o crea fallback
                $result[$categoria] = $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria));
            }
            
            return $result;
        }
    }

    /**
     * METODO: getStatsPerCategoria()
     * TIPO: HELPER PRIVATO
     * SCOPO: Calcola statistiche per categoria usando sistema unificato
     * 
     * FUNZIONALITÀ:
     * - Conta prodotti per ogni categoria
     * - Aggiunge etichette leggibili
     * - Formato ottimizzato per widget frontend
     * 
     * RETURN: array con struttura:
     * [
     *   'categoria_key' => [
     *     'count' => numero_prodotti,
     *     'label' => 'Etichetta Leggibile'
     *   ]
     * ]
     */
    private function getStatsPerCategoria(): array
    {
        try {
            // === QUERY AGGREGATA PER CONTEGGI ===
            $stats = Prodotto::where('attivo', true)
                ->groupBy('categoria') // Raggruppa per categoria
                ->selectRaw('categoria, count(*) as count') // Conta per gruppo
                ->pluck('count', 'categoria') // Array [categoria => conteggio]
                ->toArray();
            
            // === AGGIUNTA ETICHETTE LEGGIBILI ===
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
            // === GESTIONE ERRORI ===
            Log::error('Errore nel calcolo stats per categoria', [
                'error' => $e->getMessage()
            ]);
            
            return []; // Array vuoto in caso di errore
        }
    }

    // ================================================
    // SEZIONE 8: METODI DI COMPATIBILITÀ E ALIAS
    // ================================================

    /**
     * METODO: adminIndex()
     * TIPO: ALIAS METHOD
     * SCOPO: Alias per compatibilità con route esistenti
     * 
     * Alcuni sistemi potrebbero avere route che chiamano adminIndex
     * invece di index. Questo metodo mantiene la compatibilità.
     * 
     * PARAMETRI:
     * @param Request $request - Passa tutti i parametri al metodo principale
     */
    public function adminIndex(Request $request)
    {
        // Delega tutto al metodo index principale
        return $this->index($request);
    }

    /**
     * METODO: adminShow()
     * TIPO: GET REQUEST HANDLER SPECIALIZZATO
     * ROUTE: /admin/prodotti/{prodotto}/details
     * ACCESSO: Amministratori (livello 4)
     * 
     * SCOPO:
     * Vista admin specializzata con informazioni avanzate e strumenti di gestione.
     * Include statistiche dettagliate, prodotti correlati e opzioni di riassegnazione.
     * 
     * DIFFERENZE DA show():
     * - Statistiche avanzate sui malfunzionamenti
     * - Prodotti correlati per categoria e staff
     * - Lista staff disponibili per riassegnazione
     * - Informazioni complete sui creatori malfunzionamenti
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto da visualizzare in modalità admin avanzata
     */
    public function adminShow(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ADMIN ===
        if (!Auth::check() || !Auth::user()->canManageProdotti()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        // === CARICAMENTO RELAZIONI COMPLETE CON METADATI ===
        $prodotto->load([
            // === MALFUNZIONAMENTI CON ORDINAMENTO INTELLIGENTE ===
            'malfunzionamenti' => function($query) {
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')") // Prima i critici
                      ->orderBy('numero_segnalazioni', 'desc') // Poi i più segnalati
                      ->orderBy('created_at', 'desc'); // Infine i più recenti
            },
            // === INFO COMPLETE SU CHI HA CREATO/MODIFICATO ===
            'malfunzionamenti.creatoBy:id,nome,cognome,livello_accesso',
            'malfunzionamenti.modificatoBy:id,nome,cognome,livello_accesso',
            // === INFO STAFF ASSEGNATO CON METADATI ===
            'staffAssegnato:id,nome,cognome,livello_accesso,created_at',
        ]);

        // === CALCOLO STATISTICHE AVANZATE ===
        $statistiche = [
            'malfunzionamenti_totali' => $prodotto->malfunzionamenti->count(),
            'malfunzionamenti_critici' => $prodotto->malfunzionamenti->where('gravita', 'critica')->count(),
            'malfunzionamenti_alta' => $prodotto->malfunzionamenti->where('gravita', 'alta')->count(),
            'segnalazioni_totali' => $prodotto->malfunzionamenti->sum('numero_segnalazioni'),
            'piu_segnalato' => $prodotto->malfunzionamenti->sortByDesc('numero_segnalazioni')->first(),
            'ultimo_malfunzionamento' => $prodotto->malfunzionamenti->sortByDesc('created_at')->first(),
            'media_segnalazioni' => $prodotto->malfunzionamenti->count() > 0 ? 
                round($prodotto->malfunzionamenti->avg('numero_segnalazioni'), 1) : 0,
        ];

        // === ALGORITMO PRODOTTI CORRELATI ===
        // Trova prodotti simili per analisi comparative
        $prodottiCorrelati = Prodotto::where('id', '!=', $prodotto->id)
            ->where(function($query) use ($prodotto) {
                $query->where('categoria', $prodotto->categoria) // Stessa categoria
                      ->orWhere('staff_assegnato_id', $prodotto->staff_assegnato_id); // Stesso staff
            })
            ->where('attivo', true)
            ->withCount('malfunzionamenti') // Include conteggio problemi
            ->limit(5) // Massimo 5 correlati
            ->get();

        // === STAFF DISPONIBILI PER RIASSEGNAZIONE ===
        // Lista staff escluso quello già assegnato (se presente)
        $staffDisponibili = User::where('livello_accesso', '3') // Solo staff
            ->where('id', '!=', $prodotto->staff_assegnato_id) // Escludi assegnato corrente
            ->select('id', 'nome', 'cognome')
            ->orderBy('nome')
            ->get();

        // === CALCOLO METRICHE TEMPORALI ===
        $metriche = [
            'giorni_online' => $prodotto->created_at->diffInDays(now()),
            'ultimo_aggiornamento' => $prodotto->updated_at->diffForHumans(),
            'frequenza_problemi' => $prodotto->malfunzionamenti->count() > 0 ? 
                round($prodotto->malfunzionamenti->count() / max($prodotto->created_at->diffInMonths(now()), 1), 2) : 0,
        ];

        // === LOGGING ACCESSO ADMIN AVANZATO ===
        Log::info('Vista admin avanzata prodotto caricata', [
            'prodotto_id' => $prodotto->id,
            'prodotto_nome' => $prodotto->nome,
            'admin_id' => Auth::id(),
            'statistiche_calcolate' => array_keys($statistiche),
            'correlati_trovati' => $prodottiCorrelati->count(),
            'staff_disponibili' => $staffDisponibili->count()
        ]);

        // === RENDERIZZAZIONE VISTA ADMIN AVANZATA ===
        return view('admin.prodotti.show', compact(
            'prodotto',           // Prodotto principale con relazioni
            'statistiche',        // Stats dettagliate malfunzionamenti
            'prodottiCorrelati',  // Prodotti simili per comparazione
            'staffDisponibili',   // Staff per riassegnazione
            'metriche'           // Metriche temporali e di performance
        ));
    }
}

/**
 * ===============================================
 * DOCUMENTAZIONE TECNICA COMPLETA DEL CONTROLLER
 * ===============================================
 * 
 * PANORAMICA GENERALE:
 * Questo controller implementa un sistema completo di gestione prodotti
 * per un'applicazione di assistenza tecnica, con 4 livelli di accesso
 * e funzionalità progressive basate sui permessi utente.
 * 
 * ARCHITETTURA MVC IMPLEMENTATA:
 * 
 * MODEL (App\Models\Prodotto):
 * - Gestione dati e relazioni database
 * - Business logic centralizzata
 * - Sistema categorie unificato
 * - Scopes per query comuni
 * 
 * VIEW (Blade Templates):
 * - resources/views/prodotti/pubblico/ (viste pubbliche)
 * - resources/views/prodotti/completo/ (viste tecniche)
 * - resources/views/admin/prodotti/ (viste amministrative)
 * 
 * CONTROLLER (Questo file):
 * - Gestione richieste HTTP
 * - Controllo autorizzazioni
 * - Coordinamento Model-View
 * - Gestione errori e logging
 * 
 * TECNOLOGIE E PATTERN UTILIZZATI:
 * 
 * 1. LARAVEL ELOQUENT ORM:
 *    - Query Builder fluente per costruzione query dinamiche
 *    - Relationships per gestione relazioni database
 *    - Eager Loading per prevenzione N+1 queries
 *    - Model Binding automatico nelle route
 * 
 * 2. AUTHORIZATION SYSTEM:
 *    - Gates e Policies per controllo granulare permessi
 *    - Middleware di autenticazione e autorizzazione
 *    - Livelli di accesso progressivi (1-4)
 * 
 * 3. FORM VALIDATION:
 *    - Request Validation con regole personalizzate
 *    - Messaggi di errore localizzati
 *    - Validazione AJAX per API endpoints
 * 
 * 4. FILE STORAGE:
 *    - Laravel Storage per gestione immagini
 *    - Upload automatico con validazione tipo/dimensione
 *    - Gestione eliminazione file orfani
 * 
 * 5. API DESIGN:
 *    - RESTful endpoints per integrazioni AJAX
 *    - Risposte JSON strutturate
 *    - Gestione errori HTTP appropriati
 * 
 * 6. LOGGING E MONITORING:
 *    - Log dettagliati per audit trail
 *    - Tracking azioni amministrative
 *    - Debug informazioni per troubleshooting
 * 
 * 7. SEARCH FUNCTIONALITY:
 *    - Ricerca full-text con supporto wildcard
 *    - Filtri combinabili multipli
 *    - Paginazione efficiente
 * 
 * 8. SISTEMA CATEGORIE UNIFICATO:
 *    - Definizione centralizzata nel modello
 *    - Coerenza tra tutte le viste
 *    - Fallback di sicurezza per errori
 * 
 * SICUREZZA IMPLEMENTATA:
 * 
 * 1. AUTHENTICATION & AUTHORIZATION:
 *    - Verifica login per funzioni riservate
 *    - Controllo permessi granulare per operazione
 *    - Segregazione dati per livello accesso
 * 
 * 2. INPUT VALIDATION:
 *    - Sanitizzazione e validazione tutti gli input
 *    - Prevenzione SQL Injection via Eloquent
 *    - Controllo dimensioni upload file
 * 
 * 3. CSRF PROTECTION:
 *    - Token CSRF automatici Laravel per form
 *    - Validazione richieste state-changing
 * 
 * 4. AUDIT TRAIL:
 *    - Logging completo operazioni sensibili
 *    - Tracciamento modifiche database
 *    - Informazioni IP e user agent
 * 
 * 5. ERROR HANDLING:
 *    - Gestione graceful errori database
 *    - Messaggi utente user-friendly
 *    - Logging dettagliato per debugging
 * 
 * PERFORMANCE OTTIMIZZAZIONI:
 * 
 * 1. DATABASE QUERIES:
 *    - Eager Loading per prevenire N+1 queries
 *    - Select specifici per ridurre data transfer
 *    - Indici appropriati per ricerche frequenti
 * 
 * 2. PAGINATION:
 *    - Paginazione automatica Laravel
 *    - Limiti configurabili per diversi contesti
 *    - Query string preservation
 * 
 * 3. CACHING STRATEGIES:
 *    - Cache risultati categorie frequenti
 *    - Static data caching dove appropriato
 * 
 * 4. API OPTIMIZATION:
 *    - Limiti rigorosi su risultati API
 *    - Timeout appropriati per operazioni bulk
 *    - Compressione risposte JSON quando possibile
 * 
 * GESTIONE ERRORI ROBUSTA:
 * 
 * 1. TRY-CATCH BLOCKS:
 *    - Wrapping operazioni critiche
 *    - Logging dettagliato stack traces
 *    - Fallback graceful per errori non fatali
 * 
 * 2. DATABASE TRANSACTIONS:
 *    - Operazioni atomiche per consistenza
 *    - Rollback automatico su errori
 *    - Isolamento transazionale appropriato
 * 
 * 3. FILE OPERATIONS:
 *    - Verifica esistenza prima eliminazioni
 *    - Cleanup automatico file orfani
 *    - Gestione errori storage disk
 * 
 * 4. USER FEEDBACK:
 *    - Messaggi specifici per diversi tipi errore
 *    - Evitamento information disclosure
 *    - Suggerimenti azioni correttive
 * 
 * INTEGRAZIONE FRONTEND:
 * 
 * 1. BLADE TEMPLATING:
 *    - Template condivisi per diverse viste
 *    - Component riutilizzabili
 *    - Data binding efficiente
 * 
 * 2. AJAX SUPPORT:
 *    - API endpoints per operazioni dinamiche
 *    - JSON responses strutturate
 *    - Error handling client-side friendly
 * 
 * 3. PROGRESSIVE ENHANCEMENT:
 *    - Funzionalità base senza JavaScript
 *    - Enhancement progressivo con AJAX
 *    - Graceful degradation per old browsers
 * 
 * 4. RESPONSIVE DESIGN:
 *    - Layout adattivi per dispositivi diversi
 *    - Touch-friendly per dispositivi mobile
 *    - Performance ottimizzata per connessioni lente
 * 
 * MANUTENIBILITÀ E ESTENSIBILITÀ:
 * 
 * 1. CLEAN CODE PRINCIPLES:
 *    - Metodi single-responsibility
 *    - Nomi descriptivi e auto-documentanti
 *    - Separazione concerns chiara
 * 
 * 2. DOCUMENTATION:
 *    - Commenti dettagliati per logica complessa
 *    - PHPDoc completo per tutti i metodi
 *    - README e wiki per setup e deployment
 * 
 * 3. TESTING STRATEGY:
 *    - Unit tests per business logic
 *    - Feature tests per user workflows
 *    - API tests per endpoint functionality
 * 
 * 4. DEPENDENCY MANAGEMENT:
 *    - Minimal external dependencies
 *    - Version pinning per stabilità
 *    - Regular security updates
 * 
 * DEPLOYMENT E MONITORING:
 * 
 * 1. ENVIRONMENT CONFIGURATION:
 *    - Separazione config development/production
 *    - Secrets management sicuro
 *    - Feature flags per rollout graduali
 * 
 * 2. MONITORING E ALERTING:
 *    - Health checks applicazione
 *    - Performance metrics collection
 *    - Error rate monitoring e alerting
 * 
 * 3. BACKUP E DISASTER RECOVERY:
 *    - Backup automatici database
 *    - File storage replication
 *    - Procedure restore documentate
 * 
 * 4. SCALABILITY CONSIDERATIONS:
 *    - Database connection pooling
 *    - Load balancing ready
 *    - Horizontal scaling supportato
 * 
 * ===============================================
 * FINE DOCUMENTAZIONE TECNICA COMPLETA
 * ===============================================
 */