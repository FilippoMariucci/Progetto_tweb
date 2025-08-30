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
     * Ricerca globale nei malfunzionamenti (per tecnici)
     * Route: GET /malfunzionamenti/ricerca
     * Name: malfunzionamenti.ricerca
     */
    public function ricercaGlobale(Request $request)
    {
        // Verifica autorizzazioni - solo tecnici (livello 2+)
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        // Query base per tutti i malfunzionamenti
        $query = Malfunzionamento::query();

        // === RICERCA NEL TITOLO E DESCRIZIONE ===
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            
            // Ricerca full-text o LIKE se full-text non disponibile
            try {
                $query->whereRaw(
                    "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
                    [$searchTerm . '*']
                );
            } catch (\Exception $e) {
                // Fallback a LIKE se MATCH non supportato
                $query->where(function($q) use ($searchTerm) {
                    $q->where('titolo', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descrizione', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // === FILTRI AVANZATI ===
        
        // Filtro per gravità
        if ($request->filled('gravita')) {
            $query->where('gravita', $request->input('gravita'));
        }

        // Filtro per difficoltà
        if ($request->filled('difficolta')) {
            $query->where('difficolta', $request->input('difficolta'));
        }

        // Filtro per categoria prodotto
        if ($request->filled('categoria_prodotto')) {
            $query->whereHas('prodotto', function($q) use ($request) {
                $q->where('categoria', $request->input('categoria_prodotto'));
            });
        }

        // Filtro per prodotto specifico
        if ($request->filled('prodotto_id')) {
            $query->where('prodotto_id', $request->input('prodotto_id'));
        }

        // === ORDINAMENTO ===
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
                $query->orderBy('titolo', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // === ESECUZIONE QUERY ===
        $malfunzionamenti = $query->with([
                'prodotto:id,nome,modello,categoria,foto',
                'creatoBy:id,nome,cognome'
            ])
            ->paginate(15)
            ->withQueryString();

        // === STATISTICHE PER LA VISTA ===
        $stats = [
            'totale_trovati' => $malfunzionamenti->total(),
            'critici' => $query->getQuery()->where('gravita', 'critica')->count(),
            'alta_priorita' => $query->getQuery()->where('gravita', 'alta')->count(),
        ];

        // === DATI PER I FILTRI ===
        
        // Liste per i select dei filtri
        $categorieProdotti = \App\Models\Prodotto::select('categoria')
            ->distinct()
            ->whereNotNull('categoria')
            ->orderBy('categoria')
            ->pluck('categoria')
            ->mapWithKeys(function($categoria) {
                return [$categoria => ucfirst(str_replace('_', ' ', $categoria))];
            });

        // Prodotti per filtro (solo se c'è già una ricerca)
        $prodotti = collect();
        if ($request->filled('q') || $request->filled('categoria_prodotto')) {
            $prodotti = \App\Models\Prodotto::select('id', 'nome', 'modello')
                ->when($request->filled('categoria_prodotto'), function($q) use ($request) {
                    $q->where('categoria', $request->input('categoria_prodotto'));
                })
                ->orderBy('nome')
                ->limit(50)
                ->get();
        }

        // Log della ricerca per analytics
        if ($request->filled('q')) {
            \Log::info('Ricerca globale malfunzionamenti', [
                'search_term' => $request->input('q'),
                'user_id' => Auth::id(),
                'results_count' => $malfunzionamenti->total(),
                'filters' => $request->only(['gravita', 'difficolta', 'categoria_prodotto', 'order'])
            ]);
        }

        return view('malfunzionamenti.ricerca', compact(
            'malfunzionamenti', 
            'stats', 
            'categorieProdotti', 
            'prodotti'
        ));

        
    }

    /**
 * Ricerca malfunzionamenti (alias per ricercaGlobale)
 * Route: GET /malfunzionamenti/ricerca
 * Name: malfunzionamenti.ricerca
 */
public function ricerca(Request $request)
{
    // Semplicemente chiama il metodo ricercaGlobale esistente
    return $this->ricercaGlobale($request);
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
     * Segnala problema per un malfunzionamento (non API)
     * Route: POST /malfunzionamenti/{malfunzionamento}/segnala
     */
    public function segnalaProblema(Request $request, Malfunzionamento $malfunzionamento)
    {
        // Verifica autorizzazioni - solo tecnici (livello 2+)
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
            }
            abort(403, 'Accesso riservato a tecnici e staff');
        }

        try {
            // Incrementa segnalazioni e aggiorna data ultima segnalazione
            $malfunzionamento->increment('numero_segnalazioni');
            $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

            // Log dell'azione per tracciabilità
            \Log::info('Segnalazione malfunzionamento registrata', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'titolo' => $malfunzionamento->titolo,
                'nuovo_count' => $malfunzionamento->numero_segnalazioni,
                'segnalato_da' => Auth::id(),
                'username' => Auth::user()->username,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            // Se richiesta AJAX, restituisci JSON
            if ($request->wantsJson()) {
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

            // Se richiesta normale, redirect con messaggio
            return back()->with('success', 
                'Segnalazione registrata con successo! Totale segnalazioni: ' . $malfunzionamento->numero_segnalazioni
            );

        } catch (\Exception $e) {
            // Log dell'errore per debugging
            \Log::error('Errore durante segnalazione malfunzionamento', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Gestione errori per AJAX
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante la segnalazione. Riprova tra qualche minuto.'
                ], 500);
            }

            // Gestione errori per richiesta normale
            return back()->with('error', 'Errore durante la segnalazione del problema. Riprova tra qualche minuto.');
        }
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
 * Salva nuovo malfunzionamento - VERSIONE CORRETTA
 * Gestisce sia prodotto dalla route che prodotto dal form
 */
public function store(Request $request, Prodotto $prodotto = null)
    {
        // === CONTROLLO AUTORIZZAZIONI ===
        if (!Auth::check() || !Auth::user()->canManageMalfunzionamenti()) {
            abort(403, 'Non autorizzato a creare soluzioni');
        }

        // === GESTIONE PRODOTTO DINAMICA ===
        // Se non abbiamo $prodotto dalla route, lo prendiamo dal form
        if (!$prodotto && $request->has('prodotto_id')) {
            $prodotto = Prodotto::findOrFail($request->prodotto_id);
        }
        
        // Se ancora non abbiamo un prodotto, errore
        if (!$prodotto) {
            return back()
                ->withInput()
                ->withErrors(['prodotto_id' => 'Devi selezionare un prodotto valido']);
        }

        // === VALIDAZIONE COMPLETA ALLINEATA ALLA MIGRATION ===
        $validated = $request->validate([
            // Campi del form
            'prodotto_id' => 'nullable|exists:prodotti,id', // Opzionale se viene dalla route
            'titolo' => 'required|string|min:5|max:255',
            'descrizione' => 'required|string|min:10',
            'gravita' => 'required|in:bassa,media,alta,critica',
            'soluzione' => 'required|string|min:10',
            
            // Campi opzionali ma utili
            'difficolta' => 'nullable|in:facile,media,difficile,esperto',
            'strumenti_necessari' => 'nullable|string|max:500',
            'tempo_stimato' => 'nullable|integer|min:1|max:999',
            'componente_difettoso' => 'nullable|string|max:255',
            'codice_errore' => 'nullable|string|max:50',
            
            // Campi di gestione segnalazioni
            'numero_segnalazioni' => 'nullable|integer|min:1|max:9999',
            'prima_segnalazione' => 'nullable|date|before_or_equal:today',
        ], [
            // Messaggi di errore personalizzati
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

        try {
            // === PREPARAZIONE DATI COMPLETA ===
            $data = [
                // CAMPI OBBLIGATORI DALLA MIGRATION
                'prodotto_id' => $prodotto->id, // Usa sempre il prodotto determinato sopra
                'titolo' => trim($validated['titolo']),
                'descrizione' => trim($validated['descrizione']),
                'gravita' => $validated['gravita'],
                'soluzione' => trim($validated['soluzione']),
                'creato_da' => Auth::id(), // OBBLIGATORIO dalla migration
                
                // CAMPI CON VALORI DEFAULT APPROPRIATI
                'difficolta' => $validated['difficolta'] ?? 'media',
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
     * API per segnalare un malfunzionamento (chiamate AJAX)
     * Route: POST /api/malfunzionamenti/{malfunzionamento}/segnala
     * Name: api.malfunzionamenti.segnala
     */
    public function apiSegnala(Request $request, Malfunzionamento $malfunzionamento)
    {
        // Verifica autorizzazioni
        if (!Auth::check() || !Auth::user()->canViewMalfunzionamenti()) {
            return response()->json([
                'success' => false, 
                'message' => 'Accesso riservato a tecnici e staff'
            ], 403);
        }

        try {
            // Controllo CSRF per sicurezza (Laravel lo fa automaticamente, ma controlliamo)
            if (!$request->hasValidSignature()) {
                // Il CSRF token è già controllato dal middleware, ma aggiungiamo ulteriore sicurezza
                \Log::warning('Tentativo di segnalazione senza token CSRF valido', [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip()
                ]);
            }

            // Incrementa segnalazioni atomicamente
            $vecchioContatore = $malfunzionamento->numero_segnalazioni;
            $malfunzionamento->increment('numero_segnalazioni');
            
            // Aggiorna data ultima segnalazione
            $malfunzionamento->update(['ultima_segnalazione' => now()->toDateString()]);

            // Ricarica il modello per ottenere il nuovo valore
            $malfunzionamento->refresh();

            // Log dettagliato per API
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

            // Risposta JSON completa per aggiornamento interfaccia
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
            // Log dettagliato dell'errore
            \Log::error('Errore API segnalazione malfunzionamento', [
                'malfunzionamento_id' => $malfunzionamento->id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server. La segnalazione non è stata registrata.',
                'error_code' => 'SEGNALA_API_ERROR',
                'timestamp' => now()->toISOString()
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
    public function incrementSegnalazioni(Request $request, Prodotto $prodotto, Malfunzionamento $malfunzionamento)
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