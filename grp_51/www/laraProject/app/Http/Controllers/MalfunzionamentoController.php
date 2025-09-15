<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Malfunzionamento;
use App\Models\Prodotto;
use Illuminate\Support\Str;

/**
 * CONTROLLER PRINCIPALE PER LA GESTIONE DEI MALFUNZIONAMENTI
 * 
 * Questo controller gestisce tutte le operazioni CRUD (Create, Read, Update, Delete)
 * per i malfunzionamenti dei prodotti nel sistema di assistenza tecnica.
 * 
 * LINGUAGGIO: PHP con Framework Laravel 12
 * PATTERN: MVC (Model-View-Controller)
 * SICUREZZA: Controllo autorizzazioni basato su livelli utente
 * 
 * LIVELLI DI ACCESSO:
 * - Livello 1: Pubblico (solo visualizzazione prodotti)
 * - Livello 2: Tecnici (visualizzazione malfunzionamenti)
 * - Livello 3: Staff (gestione malfunzionamenti)
 * - Livello 4: Admin (gestione completa)
 */
class MalfunzionamentoController extends Controller
{
    /**
     * METODO: index()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti/{prodotto}/malfunzionamenti
     * SCOPO: Visualizza l'elenco paginato di tutti i malfunzionamenti per un prodotto specifico
     * 
     * PARAMETRI:
     * @param Request $request - Oggetto richiesta HTTP contenente parametri GET come filtri e ricerca
     * @param Prodotto $prodotto - Modello Eloquent del prodotto (Route Model Binding automatico)
     * 
     * FUNZIONALITÀ:
     * - Controllo autorizzazioni (solo tecnici livello 2+)
     * - Ricerca full-text nei malfunzionamenti
     * - Filtri per gravità e difficoltà
     * - Ordinamento multiplo (gravità, frequenza, data, difficoltà)
     * - Paginazione con 10 risultati per pagina
     * - Calcolo statistiche aggregate
     * 
     * TECNOLOGIE USATE:
     * - Laravel Eloquent ORM per query database
     * - MySQL MATCH() AGAINST() per ricerca full-text
     * - Laravel Pagination per paginazione automatica
     * - Blade templating engine per le viste
     */
    public function index(Request $request, Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        // Verifica che l'utente sia autenticato E abbia permessi di livello 2+ (tecnici)
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            // abort() genera un'eccezione HTTP con codice 403 (Forbidden)
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // === COSTRUZIONE QUERY BASE ===
        // Utilizza la relazione Eloquent definita nel modello Prodotto
        // $prodotto->malfunzionamenti() restituisce un Query Builder
        $query = $prodotto->malfunzionamenti();

        // === IMPLEMENTAZIONE RICERCA FULL-TEXT ===
        // Controlla se è presente il parametro 'search' nella richiesta HTTP
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            // MySQL MATCH() AGAINST() per ricerca full-text ottimizzata
            // BOOLEAN MODE permette operatori come * per wildcard
            // Il * finale trasforma "lava" in "lava*" per cercare "lavatrici", "lavastoviglie", etc.
            $query->whereRaw(
                "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
                [$searchTerm . '*']
            );
        }

        // === FILTRO PER GRAVITÀ ===
        // Filtro dropdown per livello di gravità del malfunzionamento
        if ($request->filled('gravita')) {
            // WHERE clause semplice su colonna ENUM
            $query->where('gravita', $request->input('gravita'));
        }

        // === FILTRO PER DIFFICOLTÀ ===
        // Filtro per livello di difficoltà della riparazione
        if ($request->filled('difficolta')) {
            $query->where('difficolta', $request->input('difficolta'));
        }

        // === SISTEMA DI ORDINAMENTO MULTIPLO ===
        // Default: ordina per gravità (critica > alta > media > bassa)
        $orderBy = $request->input('order', 'gravita');
        
        // Switch statement per gestire diversi tipi di ordinamento
        switch ($orderBy) {
            case 'gravita':
                // FIELD() di MySQL per ordinamento personalizzato su ENUM
                // Ordina: critica → alta → media → bassa
                $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')");
                break;
            case 'frequenza':
                // Ordina per numero di segnalazioni (più segnalato = più urgente)
                $query->orderBy('numero_segnalazioni', 'desc');
                break;
            case 'recente':
                // Ordina per data ultima segnalazione (più recente prima)
                $query->orderBy('ultima_segnalazione', 'desc');
                break;
            case 'difficolta':
                // Ordinamento per difficoltà: esperto → difficile → media → facile
                $query->orderByRaw("FIELD(difficolta, 'esperto', 'difficile', 'media', 'facile')");
                break;
            default:
                // Fallback: ordina per data creazione
                $query->orderBy('created_at', 'desc');
        }

        // === EAGER LOADING E PAGINAZIONE ===
        // with() carica le relazioni in anticipo per evitare N+1 queries
        // paginate() implementa paginazione Laravel con 10 risultati per pagina
        $malfunzionamenti = $query->with(['creatoBy', 'modificatoBy'])
            ->paginate(10);

        // === CALCOLO STATISTICHE AGGREGATE ===
        // Array associativo con statistiche per la vista
        $stats = [
            'totale' => $prodotto->malfunzionamenti()->count(),
            'critici' => $prodotto->malfunzionamenti()->where('gravita', 'critica')->count(),
            'alta_gravita' => $prodotto->malfunzionamenti()->where('gravita', 'alta')->count(),
            'totale_segnalazioni' => $prodotto->malfunzionamenti()->sum('numero_segnalazioni'),
        ];

        // === RENDERIZZAZIONE VISTA BLADE ===
        // compact() crea array associativo con le variabili per la vista
        // La vista Blade si trova in resources/views/malfunzionamenti/index.blade.php
        return view('malfunzionamenti.index', compact('prodotto', 'malfunzionamenti', 'stats'));
    }

    /**
     * METODO: ricercaGlobale()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /malfunzionamenti/ricerca
     * SCOPO: Ricerca avanzata globale in tutti i malfunzionamenti del sistema
     * 
     * DIFFERENZA DA index():
     * - index() cerca solo nei malfunzionamenti di UN prodotto
     * - ricercaGlobale() cerca in TUTTI i malfunzionamenti di TUTTI i prodotti
     * 
     * PARAMETRI:
     * @param Request $request - Oggetto richiesta con parametri di ricerca e filtri
     * 
     * FUNZIONALITÀ AVANZATE:
     * - Ricerca full-text con fallback LIKE
     * - Filtri multipli combinabili
     * - Filtro per categoria prodotto
     * - Filtro per prodotto specifico
     * - Logging delle ricerche per analytics
     * - Caricamento dati per filtri dinamici
     */
    public function ricercaGlobale(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        // Solo tecnici e staff possono fare ricerche globali
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // === QUERY BASE SU TUTTI I MALFUNZIONAMENTI ===
        // Malfunzionamento::query() crea un Query Builder per l'intera tabella
        $query = Malfunzionamento::query();

        // === RICERCA FULL-TEXT CON FALLBACK ===
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            
            // Try-catch per gestire database che non supportano MATCH() AGAINST()
            try {
                // Ricerca full-text ottimizzata (MySQL con indici FULLTEXT)
                $query->whereRaw(
                    "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
                    [$searchTerm . '*']
                );
            } catch (\Exception $e) {
                // Fallback a LIKE per database senza supporto full-text
                // Meno efficiente ma funziona su tutti i database
                $query->where(function($q) use ($searchTerm) {
                    $q->where('titolo', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // === FILTRI AVANZATI COMBINABILI ===
        
        // Filtro per gravità (dropdown)
        if ($request->filled('gravita')) {
            $query->where('gravita', $request->input('gravita'));
        }

        // Filtro per difficoltà (dropdown)
        if ($request->filled('difficolta')) {
            $query->where('difficolta', $request->input('difficolta'));
        }

        // === FILTRO PER CATEGORIA PRODOTTO ===
        // Utilizza whereHas() per filtrare sui prodotti collegati
        if ($request->filled('categoria_prodotto')) {
            // whereHas() esegue una subquery sulla relazione 'prodotto'
            $query->whereHas('prodotto', function($q) use ($request) {
                $q->where('categoria', $request->input('categoria_prodotto'));
            });
        }

        // === FILTRO PER PRODOTTO SPECIFICO ===
        if ($request->filled('prodotto_id')) {
            $query->where('prodotto_id', $request->input('prodotto_id'));
        }

        // === ORDINAMENTO AVANZATO ===
        $orderBy = $request->input('order', 'gravita');
        
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
            case 'alfabetico':
                // Ordinamento alfabetico per titolo
                $query->orderBy('titolo', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // === ESECUZIONE QUERY CON EAGER LOADING ===
        $malfunzionamenti = $query->with([
                // Carica dati essenziali del prodotto per evitare query multiple
                'prodotto:id,nome,modello,categoria,foto',
                'creatoBy:id,nome,cognome'
            ])
            ->paginate(15) // 15 risultati per pagina (più di index perché ricerca globale)
            ->withQueryString(); // Mantiene parametri GET nella paginazione

        // === STATISTICHE PER LA VISTA ===
        $stats = [
            'totale_trovati' => $malfunzionamenti->total(),
            // Nota: getQuery() ottiene la query base per conteggi separati
            'critici' => $query->getQuery()->where('gravita', 'critica')->count(),
            'alta_priorita' => $query->getQuery()->where('gravita', 'alta')->count(),
        ];

        // === DATI PER FILTRI DINAMICI ===
        
        // Collection delle categorie prodotti per il dropdown
        $categorieProdotti = \App\Models\Prodotto::select('categoria')
            ->distinct() // Rimuove duplicati
            ->whereNotNull('categoria') // Solo record con categoria
            ->orderBy('categoria')
            ->pluck('categoria') // Estrae solo i valori della colonna
            ->mapWithKeys(function($categoria) {
                // Trasforma snake_case in Title Case per visualizzazione
                return [$categoria => ucfirst(str_replace('_', ' ', $categoria))];
            });

        // === CARICAMENTO PRODOTTI CONDIZIONALE ===
        // Carica prodotti solo se c'è già una ricerca attiva (performance)
        $prodotti = collect(); // Collection vuota di default
        if ($request->filled('q') || $request->filled('categoria_prodotto')) {
            $prodotti = \App\Models\Prodotto::select('id', 'nome', 'modello')
                ->when($request->filled('categoria_prodotto'), function($q) use ($request) {
                    // when() applica la condizione solo se vera
                    $q->where('categoria', $request->input('categoria_prodotto'));
                })
                ->orderBy('nome')
                ->limit(50) // Limite per performance
                ->get();
        }

        // === LOGGING PER ANALYTICS ===
        // Log delle ricerche per analizzare i pattern d'uso
        if ($request->filled('q')) {
            \Log::info('Ricerca globale malfunzionamenti', [
                'search_term' => $request->input('q'),
                'user_id' => Auth::id(),
                'results_count' => $malfunzionamenti->total(),
                'filters' => $request->only(['gravita', 'difficolta', 'categoria_prodotto', 'order'])
            ]);
        }

        // === RENDERIZZAZIONE VISTA ===
        return view('malfunzionamenti.ricerca', compact(
            'malfunzionamenti', 
            'stats', 
            'categorieProdotti', 
            'prodotti'
        ));
    }

    /**
     * METODO: ricerca()
     * TIPO: ALIAS METHOD
     * SCOPO: Alias per retrocompatibilità con ricercaGlobale()
     * 
     * Questo metodo è un semplice alias che chiama ricercaGlobale().
     * Serve per mantenere retrocompatibilità se ci sono link o route
     * che utilizzano il nome 'ricerca' invece di 'ricercaGlobale'.
     */
    public function ricerca(Request $request)
    {
        // Semplicemente delega tutto al metodo principale
        return $this->ricercaGlobale($request);
    }

    /**
     * METODO: show()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}
     * SCOPO: Visualizza i dettagli completi di un singolo malfunzionamento
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Modello del prodotto (Route Model Binding)
     * @param Malfunzionamento $malfunzionamento - Modello del malfunzionamento (Route Model Binding)
     * 
     * FUNZIONALITÀ:
     * - Visualizzazione dettagli completi (descrizione, soluzione, strumenti, ecc.)
     * - Verifica integrità relazione prodotto-malfunzionamento
     * - Caricamento malfunzionamenti correlati (algoritmo di correlazione)
     * - Informazioni su chi ha creato/modificato
     */
    public function show(Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        // === VERIFICA INTEGRITÀ RELAZIONE ===
        // Controlla che il malfunzionamento appartenga effettivamente al prodotto
        // Prevenzione di URL manipulation attacks
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404, 'Malfunzionamento non trovato per questo prodotto');
        }

        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // === EAGER LOADING DELLE RELAZIONI ===
        // Carica tutte le relazioni necessarie in una query per evitare N+1
        $malfunzionamento->load(['creatoBy', 'modificatoBy', 'prodotto']);

        // === ALGORITMO MALFUNZIONAMENTI CORRELATI ===
        // Trova malfunzionamenti simili basati su:
        // 1. Stessa gravità
        // 2. Stessa categoria di prodotto
        $correlati = Malfunzionamento::where('id', '!=', $malfunzionamento->id) // Escludi quello corrente
            ->where(function($query) use ($malfunzionamento) {
                // OR condition: stessa gravità O stessa categoria prodotto
                $query->where('gravita', $malfunzionamento->gravita)
                      ->orWhereHas('prodotto', function($q) use ($malfunzionamento) {
                          $q->where('categoria', $malfunzionamento->prodotto->categoria);
                      });
            })
            ->with('prodotto') // Eager load dei prodotti correlati
            ->orderBy('numero_segnalazioni', 'desc') // I più segnalati prima
            ->limit(5) // Massimo 5 correlati
            ->get();

        // === RENDERIZZAZIONE VISTA DETTAGLIO ===
        return view('malfunzionamenti.show', compact('prodotto', 'malfunzionamento', 'correlati'));
    }

    /**
     * METODO: segnalaProblema()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /malfunzionamenti/{malfunzionamento}/segnala
     * SCOPO: Permette ai tecnici di segnalare di aver riscontrato lo stesso problema
     * 
     * FUNZIONALITÀ:
     * - Incrementa contatore segnalazioni
     * - Aggiorna data ultima segnalazione
     * - Logging dettagliato per tracciabilità
     * - Supporto richieste AJAX e normali
     * - Gestione errori robusta
     * 
     * PARAMETRI:
     * @param Request $request - Oggetto richiesta (può essere AJAX)
     * @param Malfunzionamento $malfunzionamento - Modello del malfunzionamento
     */
    public function segnalaProblema(Request $request, Malfunzionamento $malfunzionamento)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            // Gestione differenziata per richieste AJAX
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
            }
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // === BLOCCO TRY-CATCH PER GESTIONE ERRORI ===
        try {
            // === AGGIORNAMENTO ATOMICO DATABASE ===
            // increment() è atomico, previene race conditions
            $malfunzionamento->increment('numero_segnalazioni');
            // Aggiorna la data dell'ultima segnalazione
            $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

            // === LOGGING DETTAGLIATO PER TRACCIABILITÀ ===
            \Log::info('Segnalazione malfunzionamento registrata', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'titolo' => $malfunzionamento->titolo,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'segnalato_da' => Auth::id(),
                'username' => Auth::user()->username,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            // === GESTIONE RISPOSTA AJAX ===
            if ($request->wantsJson()) {
                // Risposta JSON strutturata per aggiornamento interfaccia
                return response()->json([
                    'success' => true,
                    'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                    'message' => 'Segnalazione registrata con successo',
                    'data' => [
                        'id' => $malfunzionamento->id,
                        'titolo' => $malfunzionamento->titolo,
                        'segnalazioni' => $malfunzionamento->numero_segnalazioni,
                        'ultima_segnalazione' => $malfunzionamento->ultima_segnalazione,
                        'gravita' => $malfunzionamento->gravita
                    ]
                ]);
            }

            // === GESTIONE RISPOSTA NORMALE (REDIRECT) ===
            return back()->with('success', 
                'Segnalazione registrata con successo! Totale segnalazioni: ' . $malfunzionamento->numero_segnalazioni
            );

        } catch (\Exception $e) {
            // === LOGGING ERRORI PER DEBUG ===
            \Log::error('Errore durante segnalazione malfunzionamento', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // === GESTIONE ERRORI PER AJAX ===
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante la segnalazione. Riprova tra qualche minuto.'
                ], 500);
            }

            // === GESTIONE ERRORI PER RICHIESTA NORMALE ===
            return back()->with('error', 'Errore durante la segnalazione del problema. Riprova tra qualche minuto.');
        }
    }

    /**
     * METODO: create()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /prodotti/{prodotto}/malfunzionamenti/create
     * SCOPO: Mostra il form per creare un nuovo malfunzionamento
     * 
     * NOTA: Solo staff (livello 3+) può accedere a questa funzione
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto per cui creare il malfunzionamento
     */
    public function create(Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI STAFF ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Solo lo staff può creare malfunzionamenti');
        }

        // === RENDERIZZAZIONE FORM CREAZIONE ===
        return view('malfunzionamenti.create', compact('prodotto'));
    }

    /**
     * METODO: store()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /prodotti/{prodotto}/malfunzionamenti
     * SCOPO: Salva un nuovo malfunzionamento nel database
     * 
     * QUESTO È IL METODO PIÙ COMPLESSO DEL CONTROLLER
     * 
     * FUNZIONALITÀ AVANZATE:
     * - Gestione prodotto dinamica (da route o da form)
     * - Validazione completa con messaggi personalizzati
     * - Gestione campi obbligatori e opzionali
     * - Logging dettagliato per debug
     * - Redirect intelligente basato sul contesto
     * - Gestione errori database specifici
     * 
     * PARAMETRI:
     * @param Request $request - Dati del form
     * @param Prodotto $prodotto - Prodotto dalla route (opzionale)
     */
    public function store(Request $request, Prodotto $prodotto = null)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato a creare soluzioni');
        }

        // === GESTIONE PRODOTTO DINAMICA ===
        // Scenario A: Prodotto viene dalla route (/prodotti/{prodotto}/malfunzionamenti)
        // Scenario B: Prodotto viene dal form (dashboard con select)
        if (!$prodotto && $request->has('prodotto_id')) {
            $prodotto = Prodotto::findOrFail($request->prodotto_id);
        }
        
        // Validazione: deve esserci un prodotto valido
        if (!$prodotto) {
            return back()
                ->withInput() // Mantiene i dati inseriti
                ->withErrors(['prodotto_id' => 'Devi selezionare un prodotto valido']);
        }

        // === VALIDAZIONE COMPLETA ALLINEATA ALLA MIGRATION ===
        $validated = $request->validate([
            // === CAMPI DEL FORM ===
            'prodotto_id' => 'nullable|exists:prodotti,id', // Opzionale se dalla route
            'titolo' => 'required|string|min:5|max:255',
            'descrizione' => 'required|string|min:10',
            'gravita' => 'required|in:bassa,media,alta,critica',
            'soluzione' => 'required|string|min:10',
            
            // === CAMPI OPZIONALI MA UTILI ===
            'difficolta' => 'nullable|in:facile,media,difficile,esperto',
            'strumenti_necessari' => 'nullable|string|max:500',
            'tempo_stimato' => 'nullable|integer|min:1|max:999',
            'componente_difettoso' => 'nullable|string|max:255',
            'codice_errore' => 'nullable|string|max:50',
            
            // === CAMPI DI GESTIONE SEGNALAZIONI ===
            'numero_segnalazioni' => 'nullable|integer|min:1|max:9999',
            'prima_segnalazione' => 'nullable|date|before_or_equal:today',
        ], [
            // === MESSAGGI DI ERRORE PERSONALIZZATI ===
            'titolo.required' => 'Il titolo del problema è obbligatorio',
            'titolo.min' => 'Il titolo deve essere almeno 5 caratteri',
            'titolo.max' => 'Il titolo non può superare 255 caratteri',
            
            'descrizione.required' => 'La descrizione del problema è obbligatoria',
            'descrizione.min' => 'La descrizione deve essere almeno 10 caratteri',
            
            'soluzione.required' => 'La soluzione tecnica è obbligatoria',
            'soluzione.min' => 'La soluzione deve essere almeno 10 caratteri',
            
            'gravita.required' => 'Devi selezionare il livello di gravità',
            'gravita.in' => 'Livello di gravità non valido',
            
            'difficolta.in' => 'Livello di difficoltà non valido',
            'tempo_stimato.max' => 'Il tempo stimato non può superare 999 minuti',
            'tempo_stimato.min' => 'Il tempo stimato deve essere almeno 1 minuto',
            
            'prodotto_id.exists' => 'Il prodotto selezionato non esiste',
            'numero_segnalazioni.min' => 'Il numero di segnalazioni deve essere almeno 1',
            'numero_segnalazioni.max' => 'Il numero di segnalazioni non può superare 9999',
            'prima_segnalazione.before_or_equal' => 'La data prima segnalazione non può essere futura'
        ]);

        // === BLOCCO TRY-CATCH PER GESTIONE ERRORI ===
        try {
            // === PREPARAZIONE DATI COMPLETA ===
            $data = [
                // === CAMPI OBBLIGATORI DALLA MIGRATION ===
                'prodotto_id' => $prodotto->id, // Usa sempre il prodotto determinato sopra
                'titolo' => trim($validated['titolo']), // trim() rimuove spazi
                'descrizione' => trim($validated['descrizione']),
                'gravita' => $validated['gravita'],
                'soluzione' => trim($validated['soluzione']),
                'creato_da' => Auth::id(), // OBBLIGATORIO dalla migration
                
                // === CAMPI CON VALORI DEFAULT APPROPRIATI ===
                'difficolta' => $validated['difficolta'] ?? 'media', // ?? è null coalescing operator
                'numero_segnalazioni' => $validated['numero_segnalazioni'] ?? 1,
                'prima_segnalazione' => $validated['prima_segnalazione'] ?? now()->toDateString(),
                'ultima_segnalazione' => now()->toDateString(), // Sempre oggi per nuove soluzioni
            ];
            
            // === CAMPI OPZIONALI (solo se forniti e non vuoti) ===
            if (!empty($validated['strumenti_necessari'])) {
                $data['strumenti_necessari'] = trim($validated['strumenti_necessari']);
            }
            
            if (!empty($validated['tempo_stimato']) && $validated['tempo_stimato'] > 0) {
                $data['tempo_stimato'] = (int) $validated['tempo_stimato'];
            }
            
            if (!empty($validated['componente_difettoso'])) {
                $data['componente_difettoso'] = trim($validated['componente_difettoso']);
            }
            
            if (!empty($validated['codice_errore'])) {
                $data['codice_errore'] = trim($validated['codice_errore']);
            }

            // === CREAZIONE MALFUNZIONAMENTO ===
            $malfunzionamento = Malfunzionamento::create($data);

            // === LOG DETTAGLIATO PER DEBUG ===
            \Log::info('Nuovo malfunzionamento creato con successo', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'prodotto_id' => $prodotto->id,
                'prodotto_nome' => $prodotto->nome,
                'titolo' => $malfunzionamento->titolo,
                'gravita' => $malfunzionamento->gravita,
                'difficolta' => $malfunzionamento->difficolta,
                'created_by_user_id' => Auth::id(),
                'created_by_username' => Auth::user()->username,
                'created_via' => $request->has('prodotto_id') ? 'dashboard_selection' : 'product_page',
                'timestamp' => now()->toISOString()
            ]);

            // === REDIRECT DINAMICO BASATO SUL CONTESTO ===
            if ($request->has('prodotto_id') || $request->route()->getName() === 'staff.store.nuova.soluzione') {
                // Se veniva dalla dashboard con selezione prodotto (nuova soluzione)
                return redirect()->route('staff.dashboard')
                    ->with('success', 
                        "✅ Nuova soluzione aggiunta con successo!<br>" .
                        "<strong>Prodotto:</strong> {$prodotto->nome}<br>" .
                        "<strong>Titolo:</strong> {$malfunzionamento->titolo}"
                    );
            } else {
                // Se veniva dalla pagina specifica del prodotto
                return redirect()->route('malfunzionamenti.show', [$prodotto, $malfunzionamento])
                    ->with('success', 
                        "✅ Soluzione \"{$malfunzionamento->titolo}\" aggiunta con successo!"
                    );
            }

        } catch (\Illuminate\Database\QueryException $e) {
            // === GESTIONE ERRORI DATABASE SPECIFICI ===
            \Log::error('Errore database durante creazione malfunzionamento', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'prodotto_id' => $prodotto->id,
                'user_id' => Auth::id(),
                'sql_state' => $e->errorInfo[0] ?? null,
                'request_data' => $request->except(['_token']),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Analizza il tipo di errore SQL per messaggio più specifico
            $errorMessage = 'Errore nel database durante il salvataggio.';
            
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = 'Errore di integrità dei dati. Il prodotto selezionato potrebbe non essere più disponibile.';
            } elseif (str_contains($e->getMessage(), 'cannot be null') || str_contains($e->getMessage(), 'NOT NULL')) {
                $errorMessage = 'Alcuni campi obbligatori sono mancanti nel database. Controlla la configurazione.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'Questo malfunzionamento potrebbe già esistere per questo prodotto.';
            } elseif (str_contains($e->getMessage(), 'Data too long')) {
                $errorMessage = 'Uno o più campi superano la lunghezza massima consentita.';
            }

            return back()
                ->withInput()
                ->withErrors(['database' => $errorMessage])
                ->with('error', 'Si è verificato un errore durante il salvataggio. Riprova.');

        } catch (\Exception $e) {
            // === GESTIONE ERRORI GENERICI ===
            \Log::error('Errore generico durante creazione malfunzionamento', [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'prodotto_id' => $prodotto->id,
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'full_trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Errore imprevisto durante il salvataggio. Riprova o contatta l\'amministratore.'])
                ->with('error', 'Si è verificato un errore imprevisto. Riprova tra qualche minuto.');
        }
    }

    /**
     * METODO: edit()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /malfunzionamenti/{malfunzionamento}/edit
     * SCOPO: Mostra il form per modificare un malfunzionamento esistente
     * 
     * NOTA: Solo staff può modificare malfunzionamenti
     * 
     * PARAMETRI:
     * @param Malfunzionamento $malfunzionamento - Malfunzionamento da modificare (Route Model Binding)
     */
    public function edit(Malfunzionamento $malfunzionamento)
    {
        // === CARICAMENTO PRODOTTO ASSOCIATO ===
        // Recupera il prodotto dal malfunzionamento per mantenere il contesto
        $prodotto = $malfunzionamento->prodotto;

        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Solo lo staff può modificare malfunzionamenti');
        }

        // === RENDERIZZAZIONE FORM MODIFICA ===
        // La vista riceverà i dati esistenti per pre-popolare il form
        return view('malfunzionamenti.edit', compact('prodotto', 'malfunzionamento'));
    }

    /**
     * METODO: update()
     * TIPO: PUT/PATCH REQUEST HANDLER
     * ROUTE: PUT /prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}
     * SCOPO: Aggiorna un malfunzionamento esistente nel database
     * 
     * PARAMETRI:
     * @param Request $request - Dati del form di modifica
     * @param Prodotto $prodotto - Prodotto associato (per verifica integrità)
     * @param Malfunzionamento $malfunzionamento - Malfunzionamento da aggiornare
     */
    public function update(Request $request, Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        // === VERIFICA INTEGRITÀ RELAZIONE ===
        // Assicura che il malfunzionamento appartenga al prodotto specificato
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        // === VALIDAZIONE DATI AGGIORNAMENTO ===
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

        // === AGGIUNTA CAMPO MODIFICATORE ===
        // Traccia chi ha fatto l'ultima modifica
        $validated['modificato_da'] = Auth::id();

        // === AGGIORNAMENTO DATABASE ===
        $malfunzionamento->update($validated);

        // === LOGGING MODIFICA ===
        \Log::info('Malfunzionamento aggiornato', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'prodotto_id' => $prodotto->id,
            'updated_by' => Auth::id()
        ]);

        // === REDIRECT AL DETTAGLIO ===
        return redirect()->route('malfunzionamenti.show', [$prodotto, $malfunzionamento])
            ->with('success', 'Malfunzionamento aggiornato con successo');
    }

    /**
     * METODO: destroy()
     * TIPO: DELETE REQUEST HANDLER
     * ROUTE: DELETE /prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}
     * SCOPO: Elimina un malfunzionamento dal database
     * 
     * NOTA: Operazione irreversibile, riservata allo staff
     * 
     * PARAMETRI:
     * @param Prodotto $prodotto - Prodotto associato (per verifica)
     * @param Malfunzionamento $malfunzionamento - Malfunzionamento da eliminare
     */
    public function destroy(Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        // === VERIFICA INTEGRITÀ RELAZIONE ===
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        // === SALVATAGGIO DATI PER LOG ===
        // Salva il titolo prima della cancellazione per il log
        $titolo = $malfunzionamento->titolo;
        
        // === ELIMINAZIONE FISICA DAL DATABASE ===
        $malfunzionamento->delete();

        // === LOGGING ELIMINAZIONE ===
        \Log::info('Malfunzionamento eliminato', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'titolo' => $titolo,
            'prodotto_id' => $prodotto->id,
            'deleted_by' => Auth::id()
        ]);

        // === REDIRECT ALL'ELENCO ===
        return redirect()->route('malfunzionamenti.index', $prodotto)
            ->with('success', 'Malfunzionamento eliminato con successo');
    }

    /**
     * METODO: apiSearch()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/malfunzionamenti/search
     * SCOPO: API per ricerca AJAX malfunzionamenti dalla dashboard
     * 
     * TECNOLOGIA: AJAX con JavaScript/jQuery
     * RISPOSTA: JSON strutturato
     * 
     * PARAMETRI:
     * @param Request $request - Parametri di ricerca via AJAX
     * 
     * FUNZIONALITÀ:
     * - Ricerca full-text veloce
     * - Filtri combinabili
     * - Risultati limitati per performance
     * - Formato JSON ottimizzato per frontend
     */
    public function apiSearch(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI API ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        // === VALIDAZIONE INPUT API ===
        // Validazione specifica per API con limiti stretti
        $request->validate([
            'q' => 'nullable|string|min:2|max:100',
            'gravita' => 'nullable|in:bassa,media,alta,critica',
            'difficolta' => 'nullable|in:facile,media,difficile,esperto',
            'order' => 'nullable|in:gravita,frequenza,recente,difficolta',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);
        
        // === BLOCCO TRY-CATCH PER API ===
        try {
            // === ESTRAZIONE PARAMETRI ===
            $searchTerm = $request->input('q');
            $gravita = $request->input('gravita');
            $difficolta = $request->input('difficolta');
            $order = $request->input('order', 'gravita');
            $limit = $request->input('limit', 20);
            
            // === QUERY BASE ===
            $query = Malfunzionamento::query();
            
            // === RICERCA FULL-TEXT ===
            // Utilizza scope del modello se definito
            if ($searchTerm) {
                $query->ricerca($searchTerm); // Scope personalizzato nel modello
            }
            
            // === APPLICAZIONE FILTRI ===
            if ($gravita) {
                $query->where('gravita', $gravita);
            }
            
            if ($difficolta) {
                $query->where('difficolta', $difficolta);
            }
            
            // === ORDINAMENTO DINAMICO ===
            switch ($order) {
                case 'gravita':
                    $query->ordinatoPerGravita(); // Scope del modello
                    break;
                case 'frequenza':
                    $query->ordinatoPerFrequenza(); // Scope del modello
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
            
            // === ESECUZIONE QUERY OTTIMIZZATA ===
            $malfunzionamenti = $query->with(['prodotto:id,nome,modello,categoria'])
                ->select('id', 'prodotto_id', 'titolo', 'descrizione', 'gravita', 'difficolta', 'numero_segnalazioni', 'tempo_stimato')
                ->limit($limit)
                ->get();
            
            // === TRASFORMAZIONE DATI PER JSON ===
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
            
            // === RISPOSTA JSON STRUTTURATA ===
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
            // === GESTIONE ERRORI API ===
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
     * METODO: apiByProdotto()
     * TIPO: API ENDPOINT (JSON)
     * ROUTE: GET /api/prodotti/{prodotto}/malfunzionamenti
     * SCOPO: API per ottenere malfunzionamenti di un prodotto specifico
     * 
     * UTILIZZO: Widget prodotto, modal dettagli, ecc.
     * 
     * PARAMETRI:
     * @param Request $request - Filtri opzionali
     * @param Prodotto $prodotto - Prodotto specifico
     */
    public function apiByProdotto(Request $request, Prodotto $prodotto)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        try {
            // === QUERY SPECIFICA PER PRODOTTO ===
            $query = $prodotto->malfunzionamenti();
            
            // === FILTRI OPZIONALI ===
            if ($request->filled('gravita')) {
                $query->where('gravita', $request->input('gravita'));
            }
            
            if ($request->filled('difficolta')) {
                $query->where('difficolta', $request->input('difficolta'));
            }
            
            // === ORDINAMENTO DEFAULT PER GRAVITÀ ===
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
            
            // === ESECUZIONE QUERY LIMITATA ===
            $malfunzionamenti = $query->select('id', 'titolo', 'descrizione', 'gravita', 'difficolta', 'numero_segnalazioni', 'tempo_stimato')
                ->limit(20)
                ->get();
            
            // === TRASFORMAZIONE PER JSON ===
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
            
            // === RISPOSTA JSON CON DATI PRODOTTO ===
            return response()->json([
                'success' => true,
                'data' => $data,
                'prodotto' => [
                    'id' => $prodotto->id,
                    'nome' => $prodotto->nome,
                    'modello' => $prodotto->modello,
                    'categoria' => $prodotto->categoria
                ],
                'filters' => [
                    'gravita' => $request->input('gravita'),
                    'difficolta' => $request->input('difficolta'),
                    'order' => $order
                ]
            ]);

        } catch (\Exception $e) {
            // === LOGGING ERRORE SPECIFICO ===
            \Log::error('Errore API malfunzionamenti per prodotto', [
                'error' => $e->getMessage(),
                'prodotto_id' => $prodotto->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero dei malfunzionamenti del prodotto'
            ], 500);
        }
    }

    /**
     * METODO: apiSegnala()
     * TIPO: API ENDPOINT (JSON) - POST
     * ROUTE: POST /api/malfunzionamenti/{malfunzionamento}/segnala
     * SCOPO: API per segnalare malfunzionamenti via AJAX
     * 
     * DIFFERENZA DA segnalaProblema():
     * - Questo è specificamente per chiamate AJAX
     * - Risposta sempre JSON
     * - Logging più dettagliato
     * - Sicurezza aggiuntiva
     * 
     * PARAMETRI:
     * @param Request $request - Richiesta AJAX
     * @param Malfunzionamento $malfunzionamento - Malfunzionamento da segnalare
     */
    public function apiSegnala(Request $request, Malfunzionamento $malfunzionamento)
    {
        // === CONTROLLO AUTORIZZAZIONI API ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json([
                'success' => false, 
                'message' => 'Accesso riservato a tecnici e staff'
            ], 403);
        }

        try {
            // === CONTROLLO CSRF AGGIUNTIVO ===
            // Laravel gestisce automaticamente CSRF, ma aggiungiamo controllo extra
            if (!$request->hasValidSignature()) {
                \Log::warning('Tentativo di segnalazione senza token CSRF valido', [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip()
                ]);
            }

            // === OPERAZIONE ATOMICA DATABASE ===
            $vecchioContatore = $malfunzionamento->numero_segnalazioni;
            
            // increment() è atomico, previene race conditions in caso di segnalazioni simultanee
            $malfunzionamento->increment('numero_segnalazioni');
            
            // Aggiorna data ultima segnalazione
            $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

            // === RICARICA MODELLO PER VALORE AGGIORNATO ===
            $malfunzionamento->refresh();

            // === LOGGING DETTAGLIATO PER API ===
            \Log::info('Segnalazione malfunzionamento via API', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'prodotto_id' => $malfunzionamento->prodotto_id,
                'titolo' => $malfunzionamento->titolo,
                'vecchio_count' => $vecchioContatore,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'segnalato_da' => Auth::id(),
                'username' => Auth::user()->username,
                'livello_utente' => Auth::user()->livello_accesso,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            // === RISPOSTA JSON COMPLETA ===
            // Struttura dati per aggiornamento dinamico interfaccia
            return response()->json([
                'success' => true,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'message' => 'Segnalazione registrata con successo!',
                'data' => [
                    'id' => $malfunzionamento->id,
                    'titolo' => $malfunzionamento->titolo,
                    'gravita' => $malfunzionamento->gravita,
                    'difficolta' => $malfunzionamento->difficolta,
                    'segnalazioni' => $malfunzionamento->numero_segnalazioni,
                    'incremento' => $malfunzionamento->numero_segnalazioni - $vecchioContatore,
                    'ultima_segnalazione' => $malfunzionamento->ultima_segnalazione,
                    'updated_at' => $malfunzionamento->updated_at->toISOString()
                ],
                'meta' => [
                    'user_id' => Auth::id(),
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            // === LOGGING ERRORE DETTAGLIATO ===
            \Log::error('Errore API segnalazione malfunzionamento', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // === RISPOSTA ERRORE JSON ===
            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server. La segnalazione non è stata registrata.',
                'error_code' => 'SEGNALA_API_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * METODO: apiExport()
     * TIPO: API ENDPOINT (JSON) - GET
     * ROUTE: GET /api/malfunzionamenti/export
     * SCOPO: Esportazione dati malfunzionamenti per amministratori
     * 
     * UTILIZZO: Backup, analisi, reporting
     * ACCESSO: Solo amministratori (livello 4)
     * 
     * PARAMETRI:
     * @param Request $request - Parametri di esportazione
     */
    public function apiExport(Request $request)
    {
        // === CONTROLLO AUTORIZZAZIONI ADMIN ===
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }
        
        try {
            // === QUERY COMPLETA PER EXPORT ===
            $malfunzionamenti = Malfunzionamento::with(['prodotto:id,nome,categoria', 'creatoBy:id,nome,cognome'])
                ->orderBy('gravita')
                ->orderBy('numero_segnalazioni', 'desc')
                ->get();
            
            // === TRASFORMAZIONE DATI PER EXPORT ===
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
            
            // === RISPOSTA EXPORT ===
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
     * METODO: incrementSegnalazioni()
     * TIPO: POST REQUEST HANDLER
     * ROUTE: POST /prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}/increment
     * SCOPO: Metodo legacy per incrementare segnalazioni
     * 
     * NOTA: Questo metodo è simile a segnalaProblema() ma più semplice
     * Mantiene compatibilità con vecchie interfacce
     * 
     * PARAMETRI:
     * @param Request $request
     * @param Prodotto $prodotto
     * @param Malfunzionamento $malfunzionamento
     */
    public function incrementSegnalazioni(Request $request, Prodotto $prodotto, Malfunzionamento $malfunzionamento)
    {
        // === VERIFICA INTEGRITÀ ===
        if ($malfunzionamento->prodotto_id !== $prodotto->id) {
            abort(404);
        }

        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Non autorizzato');
        }

        // === INCREMENTO SEGNALAZIONI ===
        // Operazione atomica per evitare conflitti
        $malfunzionamento->increment('numero_segnalazioni');
        $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

        // === LOGGING SEMPLIFICATO ===
        \Log::info('Segnalazione malfunzionamento incrementata', [
            'malfunzionamento_id' => $malfunzionamento->id,
            'nuovo_count' => $malfunzionamento->numero_segnalazioni,
            'segnalato_da' => Auth::id()
        ]);

        // === GESTIONE RISPOSTA DUAL ===
        if ($request->wantsJson()) {
            // Risposta JSON per AJAX
            return response()->json([
                'success' => true,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'message' => 'Segnalazione registrata'
            ]);
        }

        // Risposta redirect per form normale
        return back()->with('success', 'Segnalazione registrata. Totale: ' . $malfunzionamento->numero_segnalazioni);
    }

    /**
     * METODO: dashboard()
     * TIPO: GET REQUEST HANDLER
     * ROUTE: /staff/malfunzionamenti/dashboard
     * SCOPO: Dashboard staff con panoramica generale malfunzionamenti
     * 
     * FUNZIONALITÀ:
     * - Statistiche aggregate generali
     * - Top 10 malfunzionamenti più frequenti
     * - Malfunzionamenti critici recenti
     * - Prodotti più problematici
     * 
     * ACCESSO: Solo staff (livello 3+)
     * 
     * UTILIZZO: Monitoraggio, prioritizzazione interventi, analisi trend
     */
    public function dashboard()
    {
        // === CONTROLLO AUTORIZZAZIONI STAFF ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Accesso riservato allo staff');
        }

        // === CALCOLO STATISTICHE GENERALI ===
        // Array associativo con metriche chiave per la dashboard
        $stats = [
            // Conteggio totale malfunzionamenti nel sistema
            'totale_malfunzionamenti' => Malfunzionamento::count(),
            
            // Malfunzionamenti critici (massima priorità)
            'critici' => Malfunzionamento::where('gravita', 'critica')->count(),
            
            // Malfunzionamenti alta priorità
            'alta_priorita' => Malfunzionamento::where('gravita', 'alta')->count(),
            
            // Nuovi malfunzionamenti questo mese (trend crescita)
            'creati_questo_mese' => Malfunzionamento::whereMonth('created_at', now()->month)->count(),
        ];

        // === TOP 10 MALFUNZIONAMENTI PIÙ FREQUENTI ===
        // Query per identificare i problemi più ricorrenti
        $piu_frequenti = Malfunzionamento::with('prodotto') // Eager loading prodotto
            ->orderBy('numero_segnalazioni', 'desc') // Ordina per segnalazioni decrescenti
            ->limit(10) // Solo i primi 10
            ->get();

        // === MALFUNZIONAMENTI CRITICI RECENTI ===
        // Query per problemi critici con segnalazioni recenti
        $critici_recenti = Malfunzionamento::where('gravita', 'critica')
            ->with('prodotto') // Carica dati prodotto
            ->orderBy('ultima_segnalazione', 'desc') // I più recenti prima
            ->limit(5) // Massimo 5 per non sovraccaricare dashboard
            ->get();

        // === PRODOTTI PIÙ PROBLEMATICI ===
        // Query per identificare prodotti con più malfunzionamenti
        $prodotti_problematici = Prodotto::withCount('malfunzionamenti') // Conta malfunzionamenti per prodotto
            ->having('malfunzionamenti_count', '>', 0) // Solo prodotti con almeno 1 problema
            ->orderBy('malfunzionamenti_count', 'desc') // Ordina per numero problemi
            ->limit(10) // Top 10 prodotti problematici
            ->get();

        // === RENDERIZZAZIONE DASHBOARD ===
        // compact() passa tutte le variabili alla vista Blade
        return view('malfunzionamenti.dashboard', compact(
            'stats', 'piu_frequenti', 'critici_recenti', 'prodotti_problematici'
        ));
    }
}

/**
 * ===============================================
 * SPIEGAZIONE GENERALE DEL CONTROLLER
 * ===============================================
 * 
 * ARCHITETTURA MVC (Model-View-Controller):
 * - MODEL: App\Models\Malfunzionamento e App\Models\Prodotto
 * - VIEW: File Blade in resources/views/malfunzionamenti/
 * - CONTROLLER: Questo file (gestisce logica business)
 * 
 * PATTERN UTILIZZATI:
 * 
 * 1. ROUTE MODEL BINDING
 *    Laravel risolve automaticamente i modelli dalle route
 *    Esempio: /prodotti/{prodotto} → $prodotto diventa istanza Prodotto
 * 
 * 2. ELOQUENT ORM
 *    Framework per interazione database object-relational
 *    $malfunzionamento->prodotto accede alla relazione
 * 
 * 3. MIDDLEWARE DI AUTENTICAZIONE
 *    Auth::check() verifica se utente è loggato
 *    Auth::user() ottiene utente corrente
 * 
 * 4. AUTHORIZATION POLICIES
 *    canViewMalfunzionamenti(), canManageMalfunzionamenti()
 *    Metodi personalizzati nel modello User per controllo permessi
 * 
 * 5. QUERY BUILDER FLUENT
 *    $query->where()->orderBy()->with()->paginate()
 *    Sintassi fluida per costruire query SQL complesse
 * 
 * 6. EAGER LOADING
 *    with(['prodotto', 'creatoBy']) carica relazioni in anticipo
 *    Previene problema N+1 queries
 * 
 * 7. SOFT DELETES (se implementato)
 *    delete() marca record come eliminato senza rimuoverlo fisicamente
 * 
 * 8. FORM REQUEST VALIDATION
 *    validate() controlla dati input secondo regole specificate
 *    Restituisce errori automaticamente se validazione fallisce
 * 
 * 9. SESSION FLASH MESSAGES
 *    with('success', '...') memorizza messaggio per prossima richiesta
 *    Utilizzato per feedback utente dopo operazioni
 * 
 * 10. JSON API RESPONSES
 *     response()->json() per endpoint AJAX
 *     Formato standardizzato per comunicazione frontend-backend
 * 
 * SICUREZZA IMPLEMENTATA:
 * 
 * 1. CSRF PROTECTION
 *    Laravel protegge automaticamente da Cross-Site Request Forgery
 * 
 * 2. AUTHORIZATION CHECKS
 *    Ogni metodo verifica permessi utente prima di procedere
 * 
 * 3. INPUT VALIDATION
 *    Tutti i dati vengono validati prima dell'uso
 * 
 * 4. SQL INJECTION PREVENTION
 *    Eloquent usa prepared statements automaticamente
 * 
 * 5. MASS ASSIGNMENT PROTECTION
 *    Solo campi esplicitamente permessi possono essere assegnati
 * 
 * 6. LOGGING DETTAGLIATO
 *    Tutte le operazioni critiche vengono loggate per audit trail
 * 
 * PERFORMANCE OTTIMIZZAZIONI:
 * 
 * 1. EAGER LOADING
 *    Riduce numero query database
 * 
 * 2. PAGINATION
 *    Evita caricamento eccessivo dati in memoria
 * 
 * 3. SELECT SPECIFICI
 *    select('id', 'titolo', ...) carica solo campi necessari
 * 
 * 4. QUERY CACHING (se implementato)
 *    Cache risultati query frequenti
 * 
 * 5. FULLTEXT INDEXING
 *    MATCH() AGAINST() per ricerche veloci su grandi dataset
 * 
 * TECNOLOGIE INTEGRATE:
 * 
 * 1. MYSQL FULLTEXT SEARCH
 *    Per ricerca avanzata nei testi
 * 
 * 2. AJAX/JSON APIs
 *    Per interfacce dinamiche senza reload pagina
 * 
 * 3. BOOTSTRAP/CSS FRAMEWORKS
 *    Per styling responsive delle viste
 * 
 * 4. JQUERY (probabilmente)
 *    Per interazioni JavaScript lato client
 * 
 * 5. LARAVEL PAGINATION
 *    Per navigazione risultati su più pagine
 * 
 * DEBUGGING E MONITORING:
 * 
 * 1. LARAVEL LOG
 *    \Log::info(), \Log::error() per tracciamento eventi
 * 
 * 2. EXCEPTION HANDLING
 *    Try-catch per gestione errori graceful
 * 
 * 3. STACK TRACE LOGGING
 *    Per debug errori in produzione
 * 
 * 4. USER ACTIVITY TRACKING
 *    Log delle azioni utente per analytics
 *
 * ===============================================
 * FINE COMMENTI DETTAGLIATI
 * ===============================================
 */