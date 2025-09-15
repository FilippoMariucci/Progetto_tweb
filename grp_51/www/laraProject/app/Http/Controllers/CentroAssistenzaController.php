<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\CentroAssistenza;
use App\Models\User;
use Carbon\Carbon;

/**
 * Controller completo per la gestione dei Centri di Assistenza
 * Include TUTTI i metodi Web e API necessari per il funzionamento del sistema
 * 
 * STRUTTURA COMPLETA:
 * 1. Metodi pubblici (Livello 1 - accesso libero)
 * 2. API pubbliche (per AJAX senza autenticazione)
 * 3. Metodi amministrativi (Livello 4)
 * 4. Gestione tecnici nei centri
 * 5. API amministrative avanzate
 * 6. Metodi utility
 */
class CentroAssistenzaController extends Controller
{
    // ================================================
    // SEZIONE 1: METODI PUBBLICI (Livello 1)
    // ================================================

    /**
     * Vista pubblica: Lista centri di assistenza
     * Route: GET /centri-assistenza
     * Accessibile a tutti gli utenti senza autenticazione
     */
    public function index(Request $request)
    {
        try {
            // Query base per recuperare i centri
            $query = CentroAssistenza::query();

            // FILTRO: Ricerca per termine generico
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('provincia', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            // FILTRO: Provincia specifica
            if ($request->filled('provincia')) {
                $query->where('provincia', strtoupper($request->provincia));
            }

            // FILTRO: Città specifica
            if ($request->filled('citta')) {
                $query->where('citta', 'LIKE', '%' . $request->citta . '%');
            }

            // Esecuzione query con paginazione
            $centri = $query->withCount('tecnici') // Conta tecnici per ogni centro
                ->orderBy('provincia')
                ->orderBy('citta')
                ->orderBy('nome')
                ->paginate(12) // 12 centri per pagina
                ->withQueryString(); // Mantiene parametri URL

            // Recupera tutte le province disponibili per il filtro
            $province = CentroAssistenza::distinct()
                ->whereNotNull('provincia')
                ->orderBy('provincia')
                ->pluck('provincia')
                ->map(function($provincia) {
                    return strtoupper($provincia);
                })
                ->unique()
                ->values();

            // Statistiche per la distribuzione geografica
            $distribuzioneCentri = CentroAssistenza::select('provincia', DB::raw('COUNT(*) as totale'))
                ->groupBy('provincia')
                ->orderBy('totale', 'DESC')
                ->get();

            // Statistiche generali per il dashboard
            $statistiche = [
                'totale_centri' => CentroAssistenza::count(),
                'province_coperte' => $province->count(),
                'centri_con_tecnici' => CentroAssistenza::has('tecnici')->count(),
                'tecnici_totali' => User::where('livello_accesso', '2')
                    ->whereNotNull('centro_assistenza_id')->count()
            ];

            return view('centri.index', compact(
                'centri', 
                'province', 
                'distribuzioneCentri', 
                'statistiche'
            ));

        } catch (\Exception $e) {
            // Log dell'errore per debugging
            Log::error('Errore nella vista centri', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect con messaggio di errore
            return redirect()->route('home')
                ->with('error', 'Errore nel caricamento dei centri di assistenza');
        }
    }

    /**
     * Vista pubblica: Dettagli singolo centro
     * Route: GET /centri-assistenza/{centro}
     */
    public function show(CentroAssistenza $centro)
    {
        try {
            // Carica i tecnici associati al centro
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
                      ->orderBy('nome');
            }]);

            // Centri vicini nella stessa provincia
            $centriVicini = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->withCount('tecnici')
                ->limit(4)
                ->get();

            return view('centri.show', compact('centro', 'centriVicini'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('centri.index')
                ->with('error', 'Centro non trovato');
        }
    }

    public function adminShow(CentroAssistenza $centro)
    {
        try {
            // Carica i tecnici associati al centro
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id')
                      ->orderBy('nome');
            }]);

            // Centri vicini nella stessa provincia
            $centriVicini = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->withCount('tecnici')
                ->limit(4)
                ->get();

            return view('admin.centri.show', compact('centro', 'centriVicini'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('centri.index')
                ->with('error', 'Centro non trovato');
        }
    }

    // ================================================
    // SEZIONE 2: API PUBBLICHE (senza autenticazione)
    // ================================================

    /**
     * API: Lista centri assistenza
     * Route: GET /api/centri
     * Utilizzata per caricamenti AJAX dinamici
     */
    public function apiIndex()
    {
        try {
            $centri = CentroAssistenza::withCount('tecnici')
                ->orderBy('provincia')
                ->orderBy('citta')
                ->orderBy('nome')
                ->get(['id', 'nome', 'citta', 'provincia', 'indirizzo', 'telefono', 'email'])
                ->map(function($centro) {
                    return [
                        'id' => $centro->id,
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'provincia' => strtoupper($centro->provincia),
                        'indirizzo_completo' => $centro->indirizzo_completo ?? $centro->indirizzo,
                        'telefono' => $centro->telefono,
                        'email' => $centro->email,
                        'numero_tecnici' => $centro->tecnici_count ?? 0,
                        'ha_tecnici' => ($centro->tecnici_count ?? 0) > 0
                    ];
                });

            return response()->json([
                'success' => true,
                'centri' => $centri,
                'total' => $centri->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore API lista centri', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento centri'
            ], 500);
        }
    }

    /**
     * API: Ricerca centri
     * Route: GET /api/centri/search?q={termine}
     * Ricerca in tempo reale per autocompletamento
     */
    public function apiSearch(Request $request)
    {
        $termine = trim($request->get('q', ''));
        
        // Validazione lunghezza minima termine
        if (strlen($termine) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Termine di ricerca troppo corto (minimo 2 caratteri)'
            ], 422);
        }

        try {
            // Query di ricerca su multipli campi
            $centri = CentroAssistenza::where(function($q) use ($termine) {
                    $q->where('nome', 'LIKE', "%{$termine}%")
                      ->orWhere('citta', 'LIKE', "%{$termine}%")
                      ->orWhere('provincia', 'LIKE', "%{$termine}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$termine}%");
                })
                ->withCount('tecnici')
                ->limit(10) // Limita risultati per performance
                ->get(['id', 'nome', 'citta', 'provincia', 'indirizzo'])
                ->map(function($centro) {
                    return [
                        'id' => $centro->id,
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'provincia' => strtoupper($centro->provincia),
                        'indirizzo_completo' => $centro->indirizzo_completo ?? $centro->indirizzo,
                        'numero_tecnici' => $centro->tecnici_count ?? 0
                    ];
                });

            return response()->json([
                'success' => true,
                'centri' => $centri,
                'total' => $centri->count(),
                'termine' => $termine
            ]);

        } catch (\Exception $e) {
            Log::error('Errore ricerca centri', [
                'termine' => $termine,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nella ricerca'
            ], 500);
        }
    }

    /**
     * API: Città per provincia - METODO CRITICO CHE MANCAVA!
     * Route: GET /api/centri/citta-per-provincia?provincia={provincia}
     * Utilizzata per popolare dinamicamente le select delle città
     */
    public function apiCittaPerProvincia(Request $request)
    {
        // Recupera e normalizza la provincia
        $provincia = strtoupper(trim($request->get('provincia', '')));
        
        // Validazione parametro provincia
        if (!$provincia) {
            return response()->json([
                'success' => false,
                'message' => 'Parametro provincia obbligatorio',
                'esempio' => 'GET /api/centri/citta-per-provincia?provincia=AN'
            ], 422);
        }

        try {
            // Query per recuperare città distinte nella provincia
            $citta = CentroAssistenza::where('provincia', $provincia)
                ->distinct()
                ->whereNotNull('citta')
                ->where('citta', '!=', '')
                ->orderBy('citta')
                ->pluck('citta')
                ->filter() // Rimuove valori falsy
                ->values() // Re-indicizza array
                ->toArray();

            // Log per debugging (utile in sviluppo)
            Log::info('API città per provincia richiesta', [
                'provincia' => $provincia,
                'citta_trovate' => count($citta),
                'user_ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'citta' => $citta,
                'provincia' => $provincia,
                'total' => count($citta),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore API città per provincia', [
                'provincia' => $provincia,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle città per la provincia ' . $provincia
            ], 500);
        }
    }

    // ================================================
    // SEZIONE 3: METODI AMMINISTRATIVI (Livello 4 - Solo Admin)
    // ================================================

    /**
     * Vista amministrativa: Lista centri per gestione
     * Route: GET /admin/centri
     * Solo per amministratori del sistema
     */
    /**
 * Dashboard amministrativa centri - METODO CORRETTO CON TUTTI I FILTRI
 */
public function adminIndex(Request $request)
{
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Accesso riservato agli amministratori');
    }

    try {
        // Query base con conteggio tecnici
        $query = CentroAssistenza::withCount('tecnici')
            ->with(['tecnici' => function($q) {
                $q->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id');
            }]);

        // FILTRO RICERCA
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
            });
        }

        // FILTRO PROVINCIA
        if ($request->filled('provincia')) {
            $query->where('provincia', strtoupper($request->provincia));
        }

        // FILTRO CITTÀ
        if ($request->filled('citta')) {
            $query->where('citta', 'LIKE', '%' . $request->citta . '%');
        }

        // FILTRO STATO (con/senza tecnici)
        if ($request->filled('stato')) {
            if ($request->stato === 'con_tecnici') {
                $query->whereHas('tecnici');
            } elseif ($request->stato === 'senza_tecnici') {
                $query->whereDoesntHave('tecnici');
            }
        }

        // ORDINAMENTO CORRETTO - Gestisce sort e order dai parametri URL
        $sortBy = $request->get('sort', 'nome');  // Default: nome
        $order = $request->get('order', 'asc');   // Default: crescente
        
        // Valida i parametri per sicurezza
        $allowedSorts = ['nome', 'provincia', 'tecnici', 'citta', 'created_at'];
        $allowedOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nome';
        }
        
        if (!in_array($order, $allowedOrders)) {
            $order = 'asc';
        }

        // Applica l'ordinamento in base al parametro sort
        switch ($sortBy) {
            case 'nome':
                $query->orderBy('nome', $order);
                break;
                
            case 'provincia':
                $query->orderBy('provincia', $order)
                      ->orderBy('citta', $order)  // Ordinamento secondario per città
                      ->orderBy('nome', 'asc');   // Ordinamento terziario per nome
                break;
                
            case 'tecnici':
                $query->orderBy('tecnici_count', $order)
                      ->orderBy('nome', 'asc');   // Ordinamento secondario per nome
                break;
                
            case 'citta':
                $query->orderBy('citta', $order)
                      ->orderBy('nome', 'asc');
                break;
                
            case 'created_at':
                $query->orderBy('created_at', $order);
                break;
                
            default:
                $query->orderBy('nome', 'asc');
                break;
        }

        // Paginazione con mantenimento parametri query
        $centri = $query->paginate(15)->withQueryString();

        // Province disponibili per il dropdown filtro
        $province = CentroAssistenza::distinct()
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->orderBy('provincia')
            ->pluck('provincia')
            ->toArray();

        // Statistiche per le card
        $stats = [
            'totale' => CentroAssistenza::count(),
            'con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
            'senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
            'tecnici_totali' => User::where('livello_accesso', '2')->count(),
            'tecnici_non_assegnati' => User::where('livello_accesso', '2')
                ->whereNull('centro_assistenza_id')->count()
        ];

        // Log per debug (rimuovere in produzione)
        Log::info('Ordinamento centri', [
            'sort' => $sortBy,
            'order' => $order,
            'query_params' => $request->query(),
            'total_results' => $centri->total()
        ]);

        // Passa tutti i dati alla vista
        return view('admin.centri.index', compact('centri', 'stats', 'province'));

    } catch (\Exception $e) {
        Log::error('Errore dashboard admin centri', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id(),
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        // Fallback con dati vuoti in caso di errore
        return view('admin.centri.index', [
            'centri' => new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 15, 1, ['path' => request()->url()]
            ),
            'stats' => [
                'totale' => 0,
                'con_tecnici' => 0,
                'senza_tecnici' => 0,
                'tecnici_totali' => 0,
                'tecnici_non_assegnati' => 0
            ],
            'province' => []
        ])->with('error', 'Errore nel caricamento della dashboard centri');
    }
}
    /**
     * Vista amministrativa: Creazione nuovo centro
     * Route: GET /admin/centri/create
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Accesso non autorizzato');
        }

        // Province italiane per select (completa)
        $province = collect([
            'AG', 'AL', 'AN', 'AO', 'AR', 'AP', 'AT', 'AV', 'BA', 'BT', 'BL', 'BN', 'BG', 'BI', 'BO', 'BZ',
            'BS', 'BR', 'CA', 'CL', 'CB', 'CI', 'CE', 'CT', 'CZ', 'CH', 'CO', 'CS', 'CR', 'KR', 'CN', 'EN',
            'FM', 'FE', 'FI', 'FG', 'FC', 'FR', 'GE', 'GO', 'GR', 'IM', 'IS', 'SP', 'AQ', 'LT', 'LE', 'LC',
            'LI', 'LO', 'LU', 'MC', 'MN', 'MS', 'MT', 'VS', 'ME', 'MI', 'MO', 'MB', 'NA', 'NO', 'NU', 'OG',
            'OT', 'OR', 'PD', 'PA', 'PR', 'PV', 'PG', 'PU', 'PE', 'PC', 'PI', 'PT', 'PN', 'PZ', 'PO', 'RG',
            'RA', 'RC', 'RE', 'RI', 'RN', 'RM', 'RO', 'SA', 'SS', 'SV', 'SI', 'SR', 'SO', 'TA', 'TE', 'TR',
            'TO', 'TP', 'TN', 'TV', 'TS', 'UD', 'VA', 'VE', 'VB', 'VC', 'VR', 'VV', 'VI', 'VT'
        ])->sort()->values();

        return view('admin.centri.create', compact('province'));
    }

    /**
     * Salvataggio nuovo centro
     * Route: POST /admin/centri
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // Validazione dati centro
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome',
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'required|string|regex:/^[0-9]{5}$/',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:centri_assistenza,email'
        ], [
            'nome.unique' => 'Esiste già un centro con questo nome',
            'email.unique' => 'Email già utilizzata da un altro centro',
            'cap.regex' => 'Il CAP deve essere di 5 cifre',
            'provincia.size' => 'La provincia deve essere di 2 caratteri (es: AN)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Creazione nuovo centro
            $centro = CentroAssistenza::create([
                'nome' => $request->nome,
                'indirizzo' => $request->indirizzo,
                'citta' => $request->citta,
                'provincia' => strtoupper($request->provincia),
                'cap' => $request->cap,
                'telefono' => $request->telefono,
                'email' => $request->email,
            ]);

            DB::commit();

            Log::info('Nuovo centro creato', [
                'centro_id' => $centro->id,
                'nome' => $centro->nome,
                'admin_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Centro creato con successo',
                    'centro' => $centro,
                    'redirect' => route('admin.centri.show', $centro)
                ]);
            }

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', 'Centro creato con successo!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Errore creazione centro', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nella creazione del centro'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Errore nella creazione del centro');
        }
    }

    /**
     * Vista amministrativa: Modifica centro esistente
     * Route: GET /admin/centri/{centro}/edit
     */
    public function edit(CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Accesso non autorizzato');
        }

        // Province complete per select
        $province = collect([
            'AG', 'AL', 'AN', 'AO', 'AR', 'AP', 'AT', 'AV', 'BA', 'BT', 'BL', 'BN', 'BG', 'BI', 'BO', 'BZ',
            'BS', 'BR', 'CA', 'CL', 'CB', 'CI', 'CE', 'CT', 'CZ', 'CH', 'CO', 'CS', 'CR', 'KR', 'CN', 'EN',
            'FM', 'FE', 'FI', 'FG', 'FC', 'FR', 'GE', 'GO', 'GR', 'IM', 'IS', 'SP', 'AQ', 'LT', 'LE', 'LC',
            'LI', 'LO', 'LU', 'MC', 'MN', 'MS', 'MT', 'VS', 'ME', 'MI', 'MO', 'MB', 'NA', 'NO', 'NU', 'OG',
            'OT', 'OR', 'PD', 'PA', 'PR', 'PV', 'PG', 'PU', 'PE', 'PC', 'PI', 'PT', 'PN', 'PZ', 'PO', 'RG',
            'RA', 'RC', 'RE', 'RI', 'RN', 'RM', 'RO', 'SA', 'SS', 'SV', 'SI', 'SR', 'SO', 'TA', 'TE', 'TR',
            'TO', 'TP', 'TN', 'TV', 'TS', 'UD', 'VA', 'VE', 'VB', 'VC', 'VR', 'VV', 'VI', 'VT'
        ])->sort()->values();

        return view('admin.centri.edit', compact('centro', 'province'));
    }

    /**
     * Aggiornamento centro esistente
     * Route: PUT /admin/centri/{centro}
     */
    public function update(Request $request, CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // Validazione con eccezione per il centro corrente
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome,' . $centro->id,
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'required|string|regex:/^[0-9]{5}$/',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:centri_assistenza,email,' . $centro->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dati non validi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Aggiornamento dati centro
            $centro->update([
                'nome' => $request->nome,
                'indirizzo' => $request->indirizzo,
                'citta' => $request->citta,
                'provincia' => strtoupper($request->provincia),
                'cap' => $request->cap,
                'telefono' => $request->telefono,
                'email' => $request->email,
            ]);

            DB::commit();

            Log::info('Centro aggiornato', [
                'centro_id' => $centro->id,
                'nome' => $centro->nome,
                'admin_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Centro aggiornato con successo',
                    'centro' => $centro
                ]);
            }

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', 'Centro aggiornato con successo!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Errore aggiornamento centro', [
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nell\'aggiornamento'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Errore nell\'aggiornamento');
        }
    }

    /**
     * Eliminazione centro
     * Route: DELETE /admin/centri/{centro}
     */
    public function destroy(CentroAssistenza $centro)
{
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Non autorizzato');
    }

    try {
        DB::beginTransaction();

        // Controlla se il centro ha tecnici assegnati
        $tecniciAssegnati = $centro->tecnici()->count();
        
        if ($tecniciAssegnati > 0) {
            return redirect()->back()
                ->with('error', "Impossibile eliminare il centro: ha {$tecniciAssegnati} tecnici assegnati");
        }

        // Backup dati per log
        $nomeCentro = $centro->nome;
        $centroData = $centro->toArray();
        
        $centro->delete();

        DB::commit();

        Log::warning('Centro eliminato', [
            'centro_data' => $centroData,
            'admin_id' => Auth::id()
        ]);

        return redirect()->route('admin.centri.index')
            ->with('success', "Centro \"{$nomeCentro}\" eliminato con successo");

    } catch (\Exception $e) {
        DB::rollback();

        Log::error('Errore eliminazione centro', [
            'centro_id' => $centro->id,
            'admin_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        return redirect()->back()
            ->with('error', 'Errore nell\'eliminazione del centro');
    }
}

    // ================================================
    // SEZIONE 4: GESTIONE TECNICI NEI CENTRI (Solo Admin)
    // ================================================

    /**
     * Assegna tecnico a centro
     * Route: POST /admin/centri/{centro}/assegna-tecnico
     */
   public function assegnaTecnico(Request $request, CentroAssistenza $centro)
{
    // Verifica autorizzazioni
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Non autorizzato'
        ], 403);
    }

    Log::info('Tentativo assegnazione tecnico', [
        'centro_id' => $centro->id,
        'centro_nome' => $centro->nome,
        'request_data' => $request->all(),
        'admin_id' => Auth::id()
    ]);

    // Validazione input
    $validator = Validator::make($request->all(), [
        'tecnico_id' => 'required|integer|exists:users,id'
    ], [
        'tecnico_id.required' => 'ID tecnico obbligatorio',
        'tecnico_id.exists' => 'Tecnico non trovato nel sistema'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Dati non validi',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $tecnico = User::findOrFail($request->tecnico_id);

        // Verifica che sia effettivamente un tecnico
        if ($tecnico->livello_accesso != 2) {
            return response()->json([
                'success' => false,
                'message' => 'L\'utente selezionato non è un tecnico'
            ], 422);
        }

        // Verifica che non sia già assegnato a QUESTO centro
        if ($tecnico->centro_assistenza_id == $centro->id) {
            return response()->json([
                'success' => false,
                'message' => 'Il tecnico è già assegnato a questo centro'
            ], 422);
        }

        // Controlla se è un trasferimento
        $centroAttuale = null;
        $isTransfer = false;
        
        if ($tecnico->centro_assistenza_id) {
            $centroAttuale = CentroAssistenza::find($tecnico->centro_assistenza_id);
            $isTransfer = true;
        }

        // Esegui l'assegnazione
        $tecnico->centro_assistenza_id = $centro->id;
        $tecnico->save();

        DB::commit();

        // Prepara messaggio di successo
        if ($isTransfer && $centroAttuale) {
            $message = "Tecnico {$tecnico->nome_completo} trasferito da \"{$centroAttuale->nome}\" a \"{$centro->nome}\"";
            
            Log::info('Tecnico trasferito', [
                'tecnico_id' => $tecnico->id,
                'tecnico_nome' => $tecnico->nome_completo,
                'centro_precedente' => $centroAttuale->nome,
                'centro_nuovo' => $centro->nome,
                'admin_id' => Auth::id()
            ]);
        } else {
            $message = "Tecnico {$tecnico->nome_completo} assegnato al centro \"{$centro->nome}\"";
            
            Log::info('Tecnico assegnato', [
                'tecnico_id' => $tecnico->id,
                'tecnico_nome' => $tecnico->nome_completo,
                'centro' => $centro->nome,
                'admin_id' => Auth::id()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_transfer' => $isTransfer,
            'previous_center' => $isTransfer && $centroAttuale ? $centroAttuale->nome : null,
            'tecnico' => [
                'id' => $tecnico->id,
                'nome_completo' => $tecnico->nome_completo,
                'specializzazione' => $tecnico->specializzazione ?? 'Generale'
            ],
            'centro' => [
                'id' => $centro->id,
                'nome' => $centro->nome
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollback();

        Log::error('Errore assegnazione tecnico', [
            'tecnico_id' => $request->tecnico_id,
            'centro_id' => $centro->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore nell\'assegnazione del tecnico',
            'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno del server'
        ], 500);
    }
}


    /**
 * Rimuovi tecnico da centro
 * Route: POST /admin/centri/{centro}/rimuovi-tecnico
 * Method: rimuoviTecnico
 */
public function rimuoviTecnico(Request $request, CentroAssistenza $centro)
{
    // Verifica autorizzazioni admin
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        $message = 'Non autorizzato';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }
        
        return redirect()->back()->with('error', $message);
    }

    // Validazione dati
    $validator = Validator::make($request->all(), [
        'tecnico_id' => 'required|exists:users,id'
    ], [
        'tecnico_id.required' => 'ID tecnico mancante',
        'tecnico_id.exists' => 'Tecnico non trovato nel sistema'
    ]);

    if ($validator->fails()) {
        $message = 'Dati non validi: ' . $validator->errors()->first();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => $message,
                'errors' => $validator->errors()
            ], 422);
        }
        
        return redirect()->back()->withErrors($validator);
    }

    try {
        DB::beginTransaction();

        $tecnico = User::findOrFail($request->tecnico_id);

        // Verifica che sia effettivamente un tecnico
        if ($tecnico->livello_accesso != 2) {
            $message = "L'utente selezionato non è un tecnico";
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // Verifica che sia assegnato a questo centro
        if ($tecnico->centro_assistenza_id != $centro->id) {
            $message = "Il tecnico non è assegnato a questo centro";
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // Esegui la rimozione
        $nomeTecnico = $tecnico->nome_completo;
        $tecnico->update(['centro_assistenza_id' => null]);

        DB::commit();

        // Log dell'operazione per audit
        Log::info('Tecnico rimosso da centro', [
            'tecnico_id' => $tecnico->id,
            'tecnico_nome' => $nomeTecnico,
            'centro_id' => $centro->id,
            'centro_nome' => $centro->nome,
            'removed_by' => Auth::id(),
            'admin_username' => Auth::user()->username
        ]);

        $successMessage = "Tecnico \"{$nomeTecnico}\" rimosso dal centro \"{$centro->nome}\"";

        // Risposta per richieste AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'tecnico_id' => $tecnico->id,
                'tecnico_nome' => $nomeTecnico
            ]);
        }

        // Redirect per richieste web normali
        return redirect()->route('admin.centri.show', $centro)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log dettagliato dell'errore
        Log::error('Errore rimozione tecnico da centro', [
            'centro_id' => $centro->id,
            'tecnico_id' => $request->tecnico_id,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'admin_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $errorMessage = 'Errore nella rimozione del tecnico dal centro';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage);
    }
}
    // ================================================
    // SEZIONE 5: API AMMINISTRATIVE AVANZATE (Solo Admin)
    // ================================================

    /**
     * API: Lista tecnici disponibili per assegnazione
     * Route: GET /api/admin/tecnici-disponibili
     * Utilizzata nelle interfacce di gestione centri
     */
    public function getTecniciDisponibili(Request $request)
{
    // Verifica autorizzazioni admin
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Non autorizzato'
        ], 403);
    }

    try {
        // Estrai centro_id dalla query string
        $centroId = $request->get('centro_id');
        
        Log::info('API tecnici disponibili chiamata', [
            'centro_id' => $centroId,
            'admin_id' => Auth::id(),
            'request_url' => $request->fullUrl()
        ]);

        // Query base per tecnici (livello accesso 2)
        $query = User::where('livello_accesso', '2')
            ->select([
                'id', 
                'nome', 
                'cognome', 
                'username',
                'specializzazione', 
                'data_nascita',
                'centro_assistenza_id',
                'created_at'
            ])
            ->with(['centroAssistenza:id,nome,citta,provincia']);

        // Se specificato centro_id, escludi tecnici già assegnati a quel centro
        if ($centroId) {
            $query->where(function($q) use ($centroId) {
                $q->whereNull('centro_assistenza_id') // Tecnici liberi
                  ->orWhere('centro_assistenza_id', '!=', $centroId); // Trasferibili da altri centri
            });
        }

        $tecnici = $query->orderBy('nome')
            ->orderBy('cognome')
            ->get()
            ->map(function($tecnico) {
                // Determina stato centro attuale
                $centroAttuale = null;
                if ($tecnico->centroAssistenza) {
                    $centroAttuale = [
                        'id' => $tecnico->centroAssistenza->id,
                        'nome' => $tecnico->centroAssistenza->nome,
                        'citta' => $tecnico->centroAssistenza->citta,
                        'provincia' => $tecnico->centroAssistenza->provincia,
                        'status' => 'assigned' // Tecnico già assegnato
                    ];
                } else {
                    $centroAttuale = [
                        'status' => 'unassigned' // Tecnico libero
                    ];
                }

                return [
                    'id' => $tecnico->id,
                    'nome_completo' => trim($tecnico->nome . ' ' . $tecnico->cognome),
                    'username' => $tecnico->username,
                    'specializzazione' => $tecnico->specializzazione ?? 'Generale',
                    'data_nascita' => $tecnico->data_nascita ? $tecnico->data_nascita->format('d/m/Y') : null,
                    'eta' => $tecnico->data_nascita ? $tecnico->data_nascita->age : null,
                    'centro_attuale' => $centroAttuale,
                    'creato_il' => $tecnico->created_at->format('d/m/Y'),
                    'disponibile' => !$tecnico->centro_assistenza_id // true se libero
                ];
            });

        // Separa tecnici liberi da quelli assegnati per statistiche
        $tecniciLiberi = $tecnici->where('disponibile', true);
        $tecniciAssegnati = $tecnici->where('disponibile', false);

        $response = [
            'success' => true,
            'tecnici' => $tecnici->values(), // Re-indicizza array
            'count' => $tecnici->count(),
            'statistiche' => [
                'totali' => $tecnici->count(),
                'liberi' => $tecniciLiberi->count(),
                'trasferibili' => $tecniciAssegnati->count()
            ],
            'centro_id' => $centroId,
            'generato_il' => now()->toISOString()
        ];

        Log::info('Tecnici disponibili caricati con successo', [
            'centro_id' => $centroId,
            'tecnici_trovati' => $tecnici->count(),
            'liberi' => $tecniciLiberi->count(),
            'trasferibili' => $tecniciAssegnati->count()
        ]);

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('Errore caricamento tecnici disponibili', [
            'centro_id' => $request->get('centro_id'),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore nel caricamento dei tecnici disponibili',
            'error_detail' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
        ], 500);
    }
}


    /**
     * API: Dettagli tecnico specifico
     * Route: GET /api/admin/tecnici/{user}
     * Informazioni dettagliate per amministratori
     */
    public function getDettagliTecnico(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        // Verifica che sia un tecnico
        if ($user->livello_accesso != '2') {
            return response()->json([
                'success' => false,
                'message' => 'L\'utente selezionato non è un tecnico'
            ], 422);
        }

        try {
            // Carica relazioni
            $user->load('centroAssistenza');

            // Prepara dati dettagliati
            $dettagli = [
                'id' => $user->id,
                'nome_completo' => $user->nome_completo,
                'username' => $user->username,
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                'data_nascita' => $user->data_nascita ? $user->data_nascita->format('d/m/Y') : null,
                'eta' => $user->data_nascita ? $user->data_nascita->age : null,
                'email' => $user->email,
                'ultimo_accesso' => $user->last_login_at ? 
                    $user->last_login_at->diffForHumans() : 'Mai',
                'registrato_il' => $user->created_at->format('d/m/Y H:i'),
                'centro_assegnato' => $user->centroAssistenza ? [
                    'id' => $user->centroAssistenza->id,
                    'nome' => $user->centroAssistenza->nome,
                    'citta' => $user->centroAssistenza->citta,
                    'provincia' => $user->centroAssistenza->provincia
                ] : null,
                'stato' => $user->centro_assistenza_id ? 'Assegnato' : 'Disponibile'
            ];

            return response()->json([
                'success' => true,
                'tecnico' => $dettagli
            ]);

        } catch (\Exception $e) {
            Log::error('Errore dettagli tecnico', [
                'tecnico_id' => $user->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento dei dettagli'
            ], 500);
        }
    }

    /**
     * API: Statistiche centro specifico
     * Route: GET /api/admin/centri/{centro}/statistiche
     * Dati avanzati per dashboard amministrative
     */
    public function getStatisticheCentro(CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // Carica relazioni necessarie
            $centro->load('tecnici');

            // Calcolo statistiche avanzate
            $tecniciProvincia = User::whereHas('centroAssistenza', function($q) use ($centro) {
                $q->where('provincia', $centro->provincia);
            })->where('livello_accesso', '2')->count();

            $centriStessaProvincia = CentroAssistenza::where('provincia', $centro->provincia)
                ->where('id', '!=', $centro->id)
                ->count();

            // Statistiche temporali
            $giorniAttivita = $centro->created_at->diffInDays(now());
            $settimaneAttivita = max(1, floor($giorniAttivita / 7));

            $stats = [
                // Dati base centro
                'info_centro' => [
                    'nome' => $centro->nome,
                    'citta' => $centro->citta,
                    'provincia' => $centro->provincia,
                    'creato_il' => $centro->created_at->format('d/m/Y')
                ],
                
                // Statistiche tecnici
                'tecnici' => [
                    'assegnati_centro' => $centro->tecnici->count(),
                    'totale_provincia' => $tecniciProvincia,
                    'media_per_centro_provincia' => $centriStessaProvincia > 0 ? 
                        round($tecniciProvincia / ($centriStessaProvincia + 1), 1) : $centro->tecnici->count(),
                    'specializzazioni' => $centro->tecnici->whereNotNull('specializzazione')
                        ->pluck('specializzazione')
                        ->countBy()
                        ->toArray()
                ],
                
                // Contesto geografico
                'contesto_geografico' => [
                    'centri_stessa_provincia' => $centriStessaProvincia,
                    'posizione_in_provincia' => CentroAssistenza::where('provincia', $centro->provincia)
                        ->where('created_at', '<=', $centro->created_at)
                        ->count()
                ],
                
                // Metriche temporali
                'metriche_temporali' => [
                    'giorni_attivo' => $giorniAttivita,
                    'settimane_attivo' => $settimaneAttivita,
                    'ultimo_aggiornamento' => $centro->updated_at->diffForHumans()
                ]
            ];

            return response()->json([
                'success' => true,
                'statistiche' => $stats,
                'generato_il' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore statistiche centro', [
                'centro_id' => $centro->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento delle statistiche'
            ], 500);
        }
    }

    /**
     * API: Tecnici disponibili per un centro specifico
     * Route: GET /api/admin/centri/{centro}/tecnici-disponibili
     * Utilizzata per popolare select di assegnazione
     */
    public function getAvailableTecnici(CentroAssistenza $centro)
{
    try {
        // Verifica autorizzazioni admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }

        Log::info('Caricamento tecnici disponibili per centro', [
            'centro_id' => $centro->id,
            'centro_nome' => $centro->nome,
            'admin_id' => Auth::id()
        ]);

        // Query per tecnici (livello 2)
        $query = User::where('livello_accesso', 2)
            ->select([
                'id', 
                'nome', 
                'cognome', 
                'specializzazione', 
                'data_nascita',
                'centro_assistenza_id'
            ])
            ->with(['centroAssistenza:id,nome,citta,provincia']);

        // Escludi tecnici già assegnati a questo centro specifico
        $query->where(function($q) use ($centro) {
            $q->whereNull('centro_assistenza_id') // Tecnici liberi
              ->orWhere('centro_assistenza_id', '!=', $centro->id); // Trasferibili da altri centri
        });

        $tecnici = $query->orderBy('nome')
            ->orderBy('cognome')
            ->get()
            ->map(function($tecnico) {
                return [
                    'id' => $tecnico->id,
                    'nome_completo' => $tecnico->nome_completo,
                    'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                    'eta' => $tecnico->eta,
                    'centro_attuale' => $tecnico->centro_assistenza_id ? [
                        'id' => $tecnico->centroAssistenza->id,
                        'nome' => $tecnico->centroAssistenza->nome,
                        'citta' => $tecnico->centroAssistenza->citta,
                        'status' => 'assigned' // Tecnico già assegnato
                    ] : [
                        'status' => 'unassigned' // Tecnico libero
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'tecnici' => $tecnici,
            'count' => $tecnici->count(),
            'centro_target' => [
                'id' => $centro->id,
                'nome' => $centro->nome,
                'citta' => $centro->citta
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Errore caricamento tecnici disponibili per centro', [
            'error' => $e->getMessage(),
            'centro_id' => $centro->id,
            'admin_id' => Auth::id(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Errore nel caricamento dei tecnici disponibili',
            'error' => app()->environment('local') ? $e->getMessage() : 'Errore interno'
        ], 500);
    }
}

    // ================================================
    // SEZIONE 6: METODI UTILITY E HELPER
    // ================================================

    /**
     * Metodo helper: Validazione dati centro
     * Utilizzato in create e update per evitare duplicazione codice
     */
    private function validazioneCentro($request, $centroId = null)
    {
        $rules = [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome' . ($centroId ? ",$centroId" : ''),
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'required|string|regex:/^[0-9]{5}$/',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:centri_assistenza,email' . ($centroId ? ",$centroId" : '')
        ];

        $messages = [
            'nome.unique' => 'Esiste già un centro con questo nome',
            'email.unique' => 'Email già utilizzata da un altro centro',
            'cap.regex' => 'Il CAP deve essere di 5 cifre numeriche',
            'provincia.size' => 'La provincia deve essere di 2 caratteri (es: AN)',
            'telefono.max' => 'Il numero di telefono è troppo lungo'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Metodo helper: Log attività amministrative
     * Centralizza il logging delle operazioni admin per audit trail
     */
    private function logAttivitaAdmin($azione, $dettagli = [])
    {
        if (Auth::check()) {
            Log::info("Admin: $azione", array_merge([
                'admin_id' => Auth::id(),
                'admin_username' => Auth::user()->username,
                'timestamp' => now()->toISOString()
            ], $dettagli));
        }
    }

    /**
     * Metodo helper: Formatta response JSON standard
     * Assicura consistenza nelle risposte API
     */
    private function jsonResponse($success, $message, $data = [], $statusCode = 200)
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Metodo helper: Controllo autorizzazioni amministratore
     * Verifica rapida per metodi admin
     */
    private function verificaAdmin()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Metodo helper: Statistiche rapide centro
     * Calcola metriche base per dashboard
     */
    private function calcolaStatisticheCentro(CentroAssistenza $centro)
    {
        return [
            'tecnici_assegnati' => $centro->tecnici()->count(),
            'giorni_attivo' => $centro->created_at->diffInDays(now()),
            'ultima_modifica' => $centro->updated_at->diffForHumans(),
            'ha_contatti_completi' => !empty($centro->telefono) && !empty($centro->email)
        ];
    }

    /**
     * Metodo helper: Formatta indirizzo completo
     * Crea stringa indirizzo user-friendly
     */
    private function formatIndirizzoCompleto($centro)
    {
        $parti = [];
        
        if ($centro->indirizzo) $parti[] = $centro->indirizzo;
        if ($centro->citta) $parti[] = $centro->citta;
        if ($centro->cap) $parti[] = $centro->cap;
        if ($centro->provincia) $parti[] = "({$centro->provincia})";
        
        return implode(', ', $parti);
    }
}