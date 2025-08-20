<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CentroAssistenza;
use App\Models\User;

/**
 * Controller completo per la gestione dei Centri di Assistenza
 * 
 * Gestisce:
 * - Visualizzazione pubblica centri (tutti i livelli)
 * - CRUD completo centri (solo admin - livello 4)
 * - Assegnazione/rimozione tecnici (solo admin)
 * - API per chiamate AJAX
 * - Export/Import dati
 * 
 * LIVELLI DI ACCESSO:
 * - Livello 1 (Pubblico): Visualizzazione elenco e dettagli base
 * - Livello 2+ (Tecnici+): Visualizzazione dettagli completi
 * - Livello 4 (Admin): Gestione completa + assegnazione tecnici
 */
class CentroAssistenzaController extends Controller
{
    // ========================================
    // METODI PUBBLICI - Accessibili a tutti
    // ========================================

    /**
     * Mostra l'elenco pubblico dei centri di assistenza
     * Accessibile a tutti gli utenti (anche non autenticati - Livello 1)
     * 
     * @param Request $request Parametri di filtro e ricerca
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Query base per i centri con conteggio tecnici
            $query = CentroAssistenza::withCount('tecnici');

            // === FILTRI DI RICERCA ===
            
            // Filtro per provincia (dropdown)
            if ($request->filled('provincia')) {
                $provincia = strtoupper(trim($request->provincia));
                $query->where('provincia', $provincia);
            }

            // Filtro per città (input text)
            if ($request->filled('citta')) {
                $citta = trim($request->citta);
                $query->where('citta', 'LIKE', "%{$citta}%");
            }

            // Ricerca testuale globale (nome del centro)
            if ($request->filled('search')) {
                $searchTerm = trim($request->search);
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filtro per stato (con/senza tecnici)
            if ($request->filled('stato')) {
                $stato = $request->stato;
                if ($stato === 'attivi') {
                    $query->whereHas('tecnici');
                } elseif ($stato === 'inattivi') {
                    $query->whereDoesntHave('tecnici');
                }
            }

            // === ORDINAMENTO ===
            $sortBy = $request->get('sort', 'nome'); // Default: ordina per nome
            $sortDirection = $request->get('direction', 'asc'); // Default: crescente

            // Campi di ordinamento permessi (sicurezza)
            $allowedSorts = ['nome', 'citta', 'provincia', 'tecnici_count'];
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'nome';
            }

            // Direzione ordinamento permessa
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'asc';
            }

            // Applica ordinamento
            $query->orderBy($sortBy, $sortDirection);

            // === PAGINAZIONE ===
            $centri = $query->paginate(12); // 12 centri per pagina

            // Mantieni i parametri di ricerca nella paginazione
            $centri->appends($request->query());

            // === STATISTICHE PER LA VISTA ===
            $statistiche = [
                'totale_centri' => CentroAssistenza::count(),
                'centri_attivi' => CentroAssistenza::whereHas('tecnici')->count(),
                'centri_inattivi' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
                'province_coperte' => CentroAssistenza::distinct('provincia')->count(),
                'totale_tecnici' => User::where('livello_accesso', 2)->whereNotNull('centro_assistenza_id')->count()
            ];

            // Lista province per il filtro dropdown
            $province = CentroAssistenza::distinct('provincia')
                ->whereNotNull('provincia')
                ->orderBy('provincia')
                ->pluck('provincia');

            return view('centri.index', compact('centri', 'statistiche', 'province'));

        } catch (\Exception $e) {
            // Log dell'errore per debugging
            Log::error('Errore caricamento centri pubblici', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return view('centri.index')
                ->with('error', 'Errore nel caricamento dei centri di assistenza. Riprova più tardi.');
        }
    }

    /**
     * Mostra i dettagli di un singolo centro
     * Accessibile a tutti, ma con informazioni diverse basate sul livello di accesso
     * 
     * @param CentroAssistenza $centro
     * @return \Illuminate\View\View
     */
    public function show(CentroAssistenza $centro)
    {
        try {
            // Carica il centro con le relazioni necessarie
            $centro->load([
                'tecnici' => function($query) {
                    // Ordina i tecnici per cognome e includi solo dati necessari
                    $query->select('id', 'nome', 'cognome', 'specializzazione', 'data_nascita', 'centro_assistenza_id')
                          ->orderBy('cognome')
                          ->orderBy('nome');
                }
            ]);

            // === DATI AGGIUNTIVI PER LA VISTA ===
            
            // Centri nelle vicinanze (stessa provincia, escluso quello corrente)
            $centriVicini = CentroAssistenza::where('id', '!=', $centro->id)
                ->where('provincia', $centro->provincia)
                ->whereHas('tecnici') // Solo centri con tecnici attivi
                ->withCount('tecnici')
                ->orderBy('tecnici_count', 'desc')
                ->limit(3)
                ->get();

            // Statistiche del centro
            $statisticheCentro = [
                'totale_tecnici' => $centro->tecnici->count(),
                'specializzazioni' => $centro->tecnici->groupBy('specializzazione')->map->count(),
                'eta_media' => $centro->tecnici->where('data_nascita', '!=', null)->avg(function($tecnico) {
                    return $tecnico->data_nascita->age ?? 0;
                }),
                'ultimo_aggiornamento' => $centro->updated_at->diffForHumans()
            ];

            // === SCELTA DELLA VISTA BASATA SU AUTORIZZAZIONI ===
            
            // Se è un admin, mostra la vista amministrativa con funzionalità CRUD
            if (Auth::check() && Auth::user()->isAdmin()) {
                return view('admin.centri.show', compact('centro', 'centriVicini', 'statisticheCentro'));
            }

            // Altrimenti mostra la vista pubblica/standard
            return view('centri.show', compact('centro', 'centriVicini', 'statisticheCentro'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('centri.index')
                ->with('error', 'Impossibile visualizzare i dettagli del centro richiesto.');
        }
    }

    // ========================================
    // METODI AMMINISTRATIVI - Solo Admin (Livello 4)
    // ========================================

    /**
     * Dashboard amministrativa per la gestione dei centri
     * Solo per amministratori (livello 4)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function adminIndex(Request $request)
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        try {
            // Query con conteggi e relazioni per la vista admin
            $query = CentroAssistenza::withCount('tecnici')
                ->with(['tecnici' => function($q) {
                    $q->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id');
                }]);

            // === FILTRI AMMINISTRATIVI ===
            
            if ($request->filled('provincia')) {
                $query->where('provincia', strtoupper($request->provincia));
            }

            if ($request->filled('search')) {
                $searchTerm = trim($request->search);
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filtro per centri con/senza tecnici
            if ($request->filled('stato_tecnici')) {
                $stato = $request->stato_tecnici;
                if ($stato === 'con_tecnici') {
                    $query->whereHas('tecnici');
                } elseif ($stato === 'senza_tecnici') {
                    $query->whereDoesntHave('tecnici');
                }
            }

            // Ordinamento per vista admin
            $query->orderBy('nome');

            $centri = $query->paginate(15);
            $centri->appends($request->query());

            // === STATISTICHE AMMINISTRATIVE ===
            $stats = [
                'totale' => CentroAssistenza::count(),
                'con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
                'senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
                'tecnici_non_assegnati' => User::where('livello_accesso', 2)
                    ->whereNull('centro_assistenza_id')->count(),
                'province_attive' => CentroAssistenza::distinct('provincia')->count()
            ];

            // Province per filtro
            $province = CentroAssistenza::distinct('provincia')
                ->whereNotNull('provincia')
                ->orderBy('provincia')
                ->pluck('provincia');

            return view('admin.centri.index', compact('centri', 'stats', 'province'));

        } catch (\Exception $e) {
            Log::error('Errore dashboard admin centri', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', 'Errore nel caricamento della dashboard centri');
        }
    }

    /**
     * Mostra il form per creare un nuovo centro
     * Solo per amministratori
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        return view('admin.centri.create');
    }

    /**
     * Salva un nuovo centro nel database
     * Solo per amministratori
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        // === VALIDAZIONE DATI ===
        $validator = Validator::make($request->all(), [
            'nome' => [
                'required',
                'string',
                'max:255',
                'unique:centri_assistenza,nome' // Nome univoco
            ],
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2', // Codice provincia (es. AN, MI)
            'cap' => 'nullable|string|regex:/^[0-9]{5}$/', // CAP italiano 5 cifre
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:centri_assistenza,email'
        ], [
            // Messaggi di errore personalizzati
            'nome.required' => 'Il nome del centro è obbligatorio',
            'nome.unique' => 'Esiste già un centro con questo nome',
            'indirizzo.required' => 'L\'indirizzo è obbligatorio',
            'citta.required' => 'La città è obbligatoria',
            'provincia.required' => 'La provincia è obbligatoria',
            'provincia.size' => 'La provincia deve essere di 2 caratteri (es. AN)',
            'cap.regex' => 'Il CAP deve essere di 5 cifre',
            'email.email' => 'Formato email non valido',
            'email.unique' => 'Questa email è già utilizzata da un altro centro'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // === CREAZIONE CENTRO ===
            DB::beginTransaction();

            $centro = CentroAssistenza::create([
                'nome' => trim($request->nome),
                'indirizzo' => trim($request->indirizzo),
                'citta' => trim($request->citta),
                'provincia' => strtoupper(trim($request->provincia)), // Sempre maiuscolo
                'cap' => $request->cap ? trim($request->cap) : null,
                'telefono' => $request->telefono ? trim($request->telefono) : null,
                'email' => $request->email ? trim(strtolower($request->email)) : null // Sempre minuscolo
            ]);

            DB::commit();

            // Log dell'operazione
            Log::info('Nuovo centro creato', [
                'centro_id' => $centro->id,
                'centro_nome' => $centro->nome,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', "Centro \"{$centro->nome}\" creato con successo!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Errore creazione centro', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nella creazione del centro. Riprova.');
        }
    }

    /**
     * Mostra il form per modificare un centro esistente
     * Solo per amministratori
     * 
     * @param CentroAssistenza $centro
     * @return \Illuminate\View\View
     */
    public function edit(CentroAssistenza $centro)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        return view('admin.centri.edit', compact('centro'));
    }

    /**
     * Aggiorna i dati di un centro esistente
     * Solo per amministratori
     * 
     * @param Request $request
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CentroAssistenza $centro)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        // === VALIDAZIONE CON ESCLUSIONE CENTRO CORRENTE ===
        $validator = Validator::make($request->all(), [
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('centri_assistenza', 'nome')->ignore($centro->id) // Ignora il centro corrente per l'unicità
            ],
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|regex:/^[0-9]{5}$/',
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('centri_assistenza', 'email')->ignore($centro->id) // Ignora il centro corrente
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // === AGGIORNAMENTO CENTRO ===
            DB::beginTransaction();

            $centro->update([
                'nome' => trim($request->nome),
                'indirizzo' => trim($request->indirizzo),
                'citta' => trim($request->citta),
                'provincia' => strtoupper(trim($request->provincia)),
                'cap' => $request->cap ? trim($request->cap) : null,
                'telefono' => $request->telefono ? trim($request->telefono) : null,
                'email' => $request->email ? trim(strtolower($request->email)) : null,
            ]);

            DB::commit();

            Log::info('Centro aggiornato', [
                'centro_id' => $centro->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', "Centro \"{$centro->nome}\" aggiornato con successo!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Errore aggiornamento centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nell\'aggiornamento del centro');
        }
    }

    /**
     * Elimina un centro dal database
     * Solo per amministratori - con controllo tecnici assegnati
     * 
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CentroAssistenza $centro)
    {
        // Verifica autorizzazione
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        try {
            DB::beginTransaction();

            // === CONTROLLO VINCOLI ===
            // Verifica se ci sono tecnici assegnati a questo centro
            $tecnici = $centro->tecnici()->count();
            if ($tecnici > 0) {
                return redirect()->back()
                    ->with('error', "Impossibile eliminare il centro: ci sono {$tecnici} tecnici assegnati. Riassegnali prima di eliminare il centro.");
            }

            $nomeCentro = $centro->nome;
            $centro->delete();

            DB::commit();

            Log::warning('Centro eliminato', [
                'centro_eliminato' => $nomeCentro,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('admin.centri.index')
                ->with('success', "Centro \"{$nomeCentro}\" eliminato con successo");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Errore eliminazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Errore nell\'eliminazione del centro');
        }
    }

    // ========================================
    // GESTIONE TECNICI NEI CENTRI
    // ========================================

    /**
     * Assegna un tecnico a un centro di assistenza
     * Gestisce sia nuove assegnazioni che riassegnazioni da altri centri
     * Supporta richieste AJAX e form standard
     * 
     * @param Request $request
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
   /**
 * Assegna un tecnico a un centro di assistenza
 * VERSIONE AGGIORNATA con assigned_at
 */
public function assegnaTecnico(Request $request, CentroAssistenza $centro)
{
    // Verifica autorizzazione admin
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        $message = 'Non autorizzato';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }
        
        return redirect()->back()->with('error', $message);
    }

    // === VALIDAZIONE INPUT ===
    $validator = Validator::make($request->all(), [
        'tecnico_id' => [
            'required',
            'integer',
            'exists:users,id',
            Rule::exists('users', 'id')->where('livello_accesso', 2)
        ]
    ], [
        'tecnico_id.required' => 'Seleziona un tecnico',
        'tecnico_id.exists' => 'Tecnico non valido'
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        return redirect()->back()->withErrors($validator);
    }

    try {
        DB::beginTransaction();

        $tecnico = User::findOrFail($request->tecnico_id);
        
        // Verifica che non sia già assegnato a QUESTO centro
        if ($tecnico->centro_assistenza_id == $centro->id) {
            $message = "Il tecnico è già assegnato a questo centro";
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // Salva info centro precedente
        $centroPrecedente = $tecnico->centroAssistenza;
        $centroPrecedenteNome = $centroPrecedente ? $centroPrecedente->nome : 'Nessuno';

        // === ASSEGNAZIONE CON TIMESTAMP ===
        $tecnico->update([
            'centro_assistenza_id' => $centro->id,
            'assigned_at' => now() // AGGIUNTO: timestamp di assegnazione
        ]);

        DB::commit();

        // Log dell'operazione
        Log::info('Tecnico assegnato/riassegnato', [
            'tecnico_id' => $tecnico->id,
            'tecnico_nome' => $tecnico->nome_completo,
            'centro_precedente' => $centroPrecedenteNome,
            'centro_nuovo' => $centro->nome,
            'centro_nuovo_id' => $centro->id,
            'assigned_at' => $tecnico->assigned_at->toISOString(), // AGGIUNTO: log timestamp
            'tipo_operazione' => $centroPrecedente ? 'riassegnazione' : 'prima_assegnazione',
            'assigned_by' => Auth::id()
        ]);

        // Messaggio di successo
        if ($centroPrecedente) {
            $successMessage = "Tecnico \"{$tecnico->nome_completo}\" riassegnato da \"{$centroPrecedenteNome}\" a \"{$centro->nome}\"";
        } else {
            $successMessage = "Tecnico \"{$tecnico->nome_completo}\" assegnato al centro \"{$centro->nome}\"";
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'tecnico' => [
                    'id' => $tecnico->id,
                    'nome_completo' => $tecnico->nome_completo,
                    'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                    'assigned_at' => $tecnico->assigned_at->toISOString() // AGGIUNTO: timestamp nella risposta
                ]
            ]);
        }

        return redirect()->route('admin.centri.show', $centro)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Errore assegnazione tecnico', [
            'centro_id' => $centro->id,
            'tecnico_id' => $request->tecnico_id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id()
        ]);

        $errorMessage = 'Errore nell\'assegnazione del tecnico';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage);
    }
}

    /**
     * Rimuove un tecnico da un centro (lo rende non assegnato)
     * Supporta richieste AJAX e form standard
     * 
     * @param Request $request
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    /**
 * Rimuove un tecnico da un centro
 * VERSIONE AGGIORNATA con assigned_at
 */
public function rimuoviTecnico(Request $request, CentroAssistenza $centro)
{
    // Verifica autorizzazione admin
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        $message = 'Non autorizzato';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }
        
        return redirect()->back()->with('error', $message);
    }

    // === VALIDAZIONE ===
    $validator = Validator::make($request->all(), [
        'tecnico_id' => [
            'required',
            'integer',
            'exists:users,id'
        ]
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        return redirect()->back()->withErrors($validator);
    }

    try {
        DB::beginTransaction();

        $tecnico = User::findOrFail($request->tecnico_id);

        // Verifica che sia effettivamente assegnato a questo centro
        if ($tecnico->centro_assistenza_id != $centro->id) {
            $message = "Il tecnico non è assegnato a questo centro";
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // === RIMOZIONE CON RESET TIMESTAMP ===
        $tecnico->update([
            'centro_assistenza_id' => null,
            'assigned_at' => null // AGGIUNTO: reset timestamp
        ]);

        DB::commit();

        Log::info('Tecnico rimosso da centro', [
            'tecnico_id' => $tecnico->id,
            'tecnico_nome' => $tecnico->nome_completo,
            'centro_id' => $centro->id,
            'centro_nome' => $centro->nome,
            'removed_at' => now()->toISOString(), // AGGIUNTO: timestamp rimozione
            'removed_by' => Auth::id()
        ]);

        $successMessage = "Tecnico \"{$tecnico->nome_completo}\" rimosso dal centro \"{$centro->nome}\"";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->route('admin.centri.show', $centro)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Errore rimozione tecnico', [
            'centro_id' => $centro->id,
            'tecnico_id' => $request->tecnico_id,
            'error' => $e->getMessage(),
            'admin_id' => Auth::id()
        ]);

        $errorMessage = 'Errore nella rimozione del tecnico';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage);
    }
}

    // ========================================
    // API ENDPOINTS PER AJAX
    // ========================================

    /**
     * API: Lista di TUTTI i tecnici disponibili nel sistema
     * Diverso da getAvailableTecnici che è specifico per un centro
     * ROUTE: GET /api/admin/tecnici-disponibili
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTecniciDisponibili(Request $request)
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false, 
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // === QUERY TUTTI I TECNICI ===
            // Ottiene TUTTI i tecnici del sistema (livello_accesso = 2)
            $query = User::where('livello_accesso', 2) // Solo tecnici
                ->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id', 'data_nascita')
                ->with(['centroAssistenza:id,nome']) // Include info centro attuale
                ->orderBy('cognome')
                ->orderBy('nome');

            // === FILTRO DI RICERCA (opzionale) ===
            if ($request->filled('search')) {
                $searchTerm = trim($request->input('search'));
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('cognome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('specializzazione', 'LIKE', "%{$searchTerm}%");
                });
            }

            // === FILTRO PER STATO ASSEGNAZIONE ===
            if ($request->filled('stato')) {
                $stato = $request->input('stato');
                if ($stato === 'assegnati') {
                    $query->whereNotNull('centro_assistenza_id');
                } elseif ($stato === 'non_assegnati') {
                    $query->whereNull('centro_assistenza_id');
                }
                // Se $stato === 'tutti', non aggiungiamo filtri
            }

            $tecnici = $query->get();

            // === FORMATTAZIONE DATI PER LA RISPOSTA ===
            $tecniciFormatted = $tecnici->map(function($tecnico) {
                return [
                    'id' => $tecnico->id,
                    'nome' => $tecnico->nome,
                    'cognome' => $tecnico->cognome,
                    'nome_completo' => "{$tecnico->nome} {$tecnico->cognome}",
                    'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                    'data_nascita' => $tecnico->data_nascita ? $tecnico->data_nascita->format('d/m/Y') : 'Non specificata',
                    'eta' => $tecnico->data_nascita ? $tecnico->data_nascita->age : null,
                    // Info centro attuale (se presente)
                    'centro_attuale' => $tecnico->centroAssistenza ? [
                        'id' => $tecnico->centroAssistenza->id,
                        'nome' => $tecnico->centroAssistenza->nome,
                        'status' => 'assigned' // Indica che è già assegnato
                    ] : [
                        'status' => 'unassigned' // Non assegnato a nessun centro
                    ],
                    'created_at' => $tecnico->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'tecnici' => $tecniciFormatted,
                'total' => $tecniciFormatted->count(),
                'message' => 'Lista tecnici caricata con successo'
            ]);

        } catch (\Exception $e) {
            // Log dell'errore per debugging
            Log::error('Errore caricamento tecnici disponibili', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei tecnici disponibili'
            ], 500);
        }
    }

    **
 * API: Lista tecnici disponibili per assegnazione a UN CENTRO SPECIFICO
 * Restituisce tecnici non assegnati + tecnici assegnati ad altri centri
 * ROUTE: GET /api/admin/centri/{centro}/tecnici-disponibili
 * 
 * @param Request $request
 * @param CentroAssistenza $centro
 * @return \Illuminate\Http\JsonResponse
 */
public function getAvailableTecnici(Request $request, CentroAssistenza $centro)
{
    // Log per debug
    Log::info('API getAvailableTecnici chiamata', [
        'centro_id' => $centro->id,
        'user_id' => Auth::id(),
        'user_level' => Auth::user()->livello_accesso ?? 'non_autenticato'
    ]);

    // Verifica autorizzazione admin
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        Log::warning('Accesso negato API tecnici', [
            'centro_id' => $centro->id,
            'user_id' => Auth::id(),
            'user_level' => Auth::user()->livello_accesso ?? 'non_autenticato'
        ]);
        
        return response()->json([
            'success' => false, 
            'message' => 'Non autorizzato - Solo gli amministratori possono accedere a questa API'
        ], 403);
    }

    try {
        // === QUERY TECNICI DISPONIBILI PER QUESTO CENTRO ===
        $query = User::where('livello_accesso', 2) // Solo tecnici
            ->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
            ->with(['centroAssistenza:id,nome']) // Include info centro attuale
            ->orderBy('cognome')
            ->orderBy('nome');

        // Escludiamo solo i tecnici GIÀ ASSEGNATI A QUESTO SPECIFICO CENTRO
        $query->where(function($q) use ($centro) {
            $q->where('centro_assistenza_id', '!=', $centro->id)
              ->orWhereNull('centro_assistenza_id');
        });

        // === FILTRO DI RICERCA (opzionale) ===
        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('cognome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('specializzazione', 'LIKE', "%{$searchTerm}%");
            });
        }

        $tecnici = $query->get();

        Log::info('Tecnici trovati', [
            'centro_id' => $centro->id,
            'count' => $tecnici->count()
        ]);

        // === FORMATTAZIONE DATI PER LA RISPOSTA ===
        $tecniciFormatted = $tecnici->map(function($tecnico) {
            return [
                'id' => $tecnico->id,
                'nome' => $tecnico->nome,
                'cognome' => $tecnico->cognome,
                'nome_completo' => "{$tecnico->nome} {$tecnico->cognome}",
                'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                // Info centro attuale
                'centro_attuale' => $tecnico->centroAssistenza ? [
                    'id' => $tecnico->centroAssistenza->id,
                    'nome' => $tecnico->centroAssistenza->nome,
                    'status' => 'assigned'
                ] : [
                    'status' => 'unassigned'
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'tecnici' => $tecniciFormatted,
            'total' => $tecniciFormatted->count(),
            'centro' => [
                'id' => $centro->id,
                'nome' => $centro->nome
            ],
            'message' => 'Lista tecnici disponibili caricata con successo'
        ]);

    } catch (\Exception $e) {
        Log::error('Errore API tecnici disponibili', [
            'centro_id' => $centro->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore nel caricamento dei tecnici disponibili',
            'error' => $e->getMessage() // Solo per debug, rimuovi in produzione
        ], 500);
    }
}

    /**
     * API: Dettagli di un tecnico specifico
     * ROUTE: GET /api/admin/tecnici/{user}
     * 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDettagliTecnico(User $user)
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false, 
                'message' => 'Non autorizzato'
            ], 403);
        }

        // Verifica che sia effettivamente un tecnico
        if (!$user->isTecnico()) {
            return response()->json([
                'success' => false, 
                'message' => 'L\'utente selezionato non è un tecnico'
            ], 422);
        }

        try {
            // Carica le relazioni necessarie
            $user->load('centroAssistenza');

            // === FORMATTAZIONE DETTAGLI TECNICO ===
            $tecnico = [
                'id' => $user->id,
                'nome' => $user->nome,
                'cognome' => $user->cognome,
                'nome_completo' => $user->nome_completo,
                'username' => $user->username,
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                'data_nascita' => $user->data_nascita ? $user->data_nascita->format('d/m/Y') : 'Non specificata',
                'eta' => $user->data_nascita ? $user->data_nascita->age . ' anni' : 'N/A',
                'centro_attuale' => [
                    'id' => $user->centroAssistenza ? $user->centroAssistenza->id : null,
                    'nome' => $user->centroAssistenza ? $user->centroAssistenza->nome : 'Nessuno',
                    'status' => $user->centroAssistenza ? 'assigned' : 'unassigned'
                ],
                'account_info' => [
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'last_login' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Mai',
                    'attivo' => true // Puoi aggiungere logica per stato account
                ]
            ];

            return response()->json([
                'success' => true,
                'tecnico' => $tecnico
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento dettagli tecnico', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dettagli tecnico'
            ], 500);
        }
    }

    /**
     * API: Statistiche di un centro specifico
     * ROUTE: GET /api/admin/centri/{centro}/statistiche
     * 
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatisticheCentro(CentroAssistenza $centro)
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false, 
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // === CARICAMENTO DATI CON RELAZIONI ===
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id', 'created_at', 'data_nascita');
            }]);

            // === CALCOLO STATISTICHE ===
            $stats = [
                'centro' => [
                    'id' => $centro->id,
                    'nome' => $centro->nome,
                    'citta' => $centro->citta,
                    'provincia' => $centro->provincia
                ],
                'tecnici' => [
                    'totale' => $centro->tecnici->count(),
                    'per_specializzazione' => $centro->tecnici->groupBy('specializzazione')->map->count(),
                    'ultimo_assegnato' => $centro->tecnici->sortByDesc('created_at')->first() ? 
                        $centro->tecnici->sortByDesc('created_at')->first()->created_at->diffForHumans() : 'Mai',
                    'eta_media' => $centro->tecnici->where('data_nascita', '!=', null)->avg(function($tecnico) {
                        return $tecnico->data_nascita->age ?? 0;
                    })
                ],
                'attivita' => [
                    'data_creazione' => $centro->created_at->format('d/m/Y'),
                    'ultimo_aggiornamento' => $centro->updated_at->diffForHumans(),
                    'giorni_attivita' => $centro->created_at->diffInDays(now())
                ]
            ];

            // === CONFRONTO CON ALTRI CENTRI NELLA STESSA PROVINCIA ===
            $centriStessaProvincia = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->withCount('tecnici')
                ->get();

            if ($centriStessaProvincia->isNotEmpty()) {
                $mediaTecniciProvincia = $centriStessaProvincia->avg('tecnici_count');
                $stats['confronti'] = [
                    'media_tecnici_provincia' => round($mediaTecniciProvincia, 1),
                    'posizione_in_provincia' => $centriStessaProvincia->where('tecnici_count', '>', $centro->tecnici->count())->count() + 1,
                    'totale_centri_provincia' => $centriStessaProvincia->count() + 1
                ];
            }

            return response()->json([
                'success' => true,
                'statistiche' => $stats,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento statistiche centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche del centro'
            ], 500);
        }
    }

    /**
     * API: Informazioni dettagliate di un centro per richieste AJAX
     * Utile per modal o aggiornamenti dinamici
     * ROUTE: GET /api/admin/centri/{centro}/dettagli
     * 
     * @param CentroAssistenza $centro
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCentroDetails(CentroAssistenza $centro)
    {
        try {
            // Carica il centro con tutti i tecnici associati
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
                      ->orderBy('cognome');
            }]);

            // Formatta i dati per la risposta JSON
            $centroData = [
                'id' => $centro->id,
                'nome' => $centro->nome,
                'indirizzo' => $centro->indirizzo,
                'citta' => $centro->citta,
                'provincia' => $centro->provincia,
                'cap' => $centro->cap,
                'telefono' => $centro->telefono,
                'email' => $centro->email,
                'indirizzo_completo' => $centro->indirizzo_completo,
                'tecnici' => $centro->tecnici->map(function($tecnico) {
                    return [
                        'id' => $tecnico->id,
                        'nome_completo' => $tecnico->nome_completo,
                        'specializzazione' => $tecnico->specializzazione ?? 'Non specificata'
                    ];
                }),
                'numero_tecnici' => $centro->tecnici->count(),
                'stato' => $centro->tecnici->count() > 0 ? 'attivo' : 'inattivo'
            ];

            return response()->json([
                'success' => true,
                'centro' => $centroData
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento dettagli centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei dettagli del centro'
            ], 500);
        }
    }

    /**
     * API: Statistiche generali sui centri per dashboard admin
     * Restituisce dati aggregati per grafici e metriche
     * ROUTE: GET /api/admin/statistiche
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistiche()
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // === STATISTICHE GENERALI ===
            $stats = [
                'centri' => [
                    'totale' => CentroAssistenza::count(),
                    'con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
                    'senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count()
                ],
                'tecnici' => [
                    'totale' => User::where('livello_accesso', 2)->count(),
                    'assegnati' => User::where('livello_accesso', 2)->whereNotNull('centro_assistenza_id')->count(),
                    'non_assegnati' => User::where('livello_accesso', 2)->whereNull('centro_assistenza_id')->count()
                ],
                'geographic' => [
                    'province_coperte' => CentroAssistenza::distinct('provincia')->count(),
                    'distribuzione_province' => CentroAssistenza::select('provincia', DB::raw('count(*) as count'))
                        ->groupBy('provincia')
                        ->orderBy('count', 'desc')
                        ->get()
                ]
            ];

            // === TOP 5 CENTRI PER NUMERO TECNICI ===
            $topCentri = CentroAssistenza::withCount('tecnici')
                ->having('tecnici_count', '>', 0)
                ->orderBy('tecnici_count', 'desc')
                ->limit(5)
                ->get(['id', 'nome', 'citta'])
                ->map(function($centro) {
                    return [
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'tecnici_count' => $centro->tecnici_count
                    ];
                });

            return response()->json([
                'success' => true,
                'statistiche' => $stats,
                'top_centri' => $topCentri,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento statistiche centri', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche'
            ], 500);
        }
    }

    // ========================================
    // FUNZIONALITÀ AVANZATE (IMPORT/EXPORT)
    // ========================================

    /**
     * Esporta i dati dei centri in formato CSV
     * Solo per amministratori
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        // Verifica autorizzazione admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        try {
            // Query per l'export
            $centri = CentroAssistenza::with(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'centro_assistenza_id');
            }])->get();

            // Preparazione dati per export
            $dataExport = $centri->map(function($centro) {
                return [
                    'ID' => $centro->id,
                    'Nome Centro' => $centro->nome,
                    'Indirizzo' => $centro->indirizzo,
                    'Città' => $centro->citta,
                    'Provincia' => $centro->provincia,
                    'CAP' => $centro->cap,
                    'Telefono' => $centro->telefono,
                    'Email' => $centro->email,
                    'Numero Tecnici' => $centro->tecnici->count(),
                    'Tecnici' => $centro->tecnici->pluck('nome_completo')->join(', '),
                    'Data Creazione' => $centro->created_at->format('d/m/Y H:i')
                ];
            });

            // Headers per download CSV
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="centri_assistenza_' . date('Y-m-d') . '.csv"',
            ];

            // Generazione CSV
            $callback = function() use ($dataExport) {
                $file = fopen('php://output', 'w');
                
                // BOM per Excel UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header CSV
                if ($dataExport->isNotEmpty()) {
                    fputcsv($file, array_keys($dataExport->first()), ';');
                    
                    // Dati
                    foreach ($dataExport as $row) {
                        fputcsv($file, $row, ';');
                    }
                }
                
                fclose($file);
            };

            Log::info('Export centri generato', [
                'total_records' => $dataExport->count(),
                'admin_id' => Auth::id()
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Errore export centri', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Errore nella generazione dell\'export');
        }
    }
}