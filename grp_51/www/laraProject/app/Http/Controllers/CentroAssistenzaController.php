<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\CentroAssistenza;
use App\Models\User;

/**
 * Controller completo per la gestione dei Centri di Assistenza
 * VERSIONE FINALE CORRETTA - Include tutti i metodi API mancanti
 */
class CentroAssistenzaController extends Controller
{
    // ================================================
    // METODI PUBBLICI (Accesso libero - Livello 1)
    // ================================================

    /**
     * Lista centri assistenza per utenti pubblici
     */
    public function index(Request $request)
    {
        try {
            // Query base per i centri
            $query = CentroAssistenza::query();

            // Filtri pubblici
            if ($request->filled('provincia')) {
                $query->where('provincia', strtoupper($request->provincia));
            }

            if ($request->filled('citta')) {
                $query->where('citta', 'LIKE', '%' . $request->citta . '%');
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('provincia', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Carica centri con conteggio tecnici
            $centri = $query->withCount('tecnici')
                ->orderBy('provincia')
                ->orderBy('citta')
                ->orderBy('nome')
                ->paginate(12);

            // Statistiche pubbliche
            $stats = [
                'totale_centri' => CentroAssistenza::count(),
                'centri_con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
                'province_coperte' => CentroAssistenza::distinct('provincia')->count()
            ];

            return view('centri.index', compact('centri', 'stats'));

        } catch (\Exception $e) {
            Log::error('Errore caricamento centri pubblici', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('centri.index')
                ->with('centri', collect())
                ->with('stats', [])
                ->with('error', 'Errore nel caricamento dei centri');
        }
    }

    /**
     * Dettaglio centro assistenza
     */
    public function show(CentroAssistenza $centro)
    {
        try {
            // Carica tecnici associati
            $centro->load(['tecnici' => function($query) {
                $query->select('id', 'nome', 'cognome', 'specializzazione', 'data_nascita', 'centro_assistenza_id')
                      ->orderBy('nome');
            }]);

            // Centri nelle vicinanze
            $centriVicini = CentroAssistenza::where('id', '!=', $centro->id)
                ->where('provincia', $centro->provincia)
                ->whereHas('tecnici')
                ->withCount('tecnici')
                ->limit(3)
                ->get();

            // Verifica se è admin per la vista
            if (Auth::check() && Auth::user()->isAdmin()) {
                return view('admin.centri.show', compact('centro', 'centriVicini'));
            }

            return view('centri.show', compact('centro', 'centriVicini'));

        } catch (\Exception $e) {
            Log::error('Errore visualizzazione centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('centri.index')
                ->with('error', 'Impossibile visualizzare i dettagli del centro');
        }
    }

    // ================================================
    // METODI AMMINISTRATIVI (Solo Admin - Livello 4)
    // ================================================

    /**
     * Dashboard amministrativa centri
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        try {
            $query = CentroAssistenza::withCount('tecnici')
                ->with(['tecnici' => function($q) {
                    $q->select('id', 'nome', 'cognome', 'specializzazione', 'centro_assistenza_id');
                }]);

            // Filtri admin
            if ($request->filled('provincia')) {
                $query->where('provincia', strtoupper($request->provincia));
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nome', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('citta', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($request->filled('stato')) {
                if ($request->stato === 'con_tecnici') {
                    $query->whereHas('tecnici');
                } elseif ($request->stato === 'senza_tecnici') {
                    $query->whereDoesntHave('tecnici');
                }
            }

            $centri = $query->orderBy('provincia')
                ->orderBy('citta')
                ->orderBy('nome')
                ->paginate(15);

            // Statistiche admin
            $stats = [
                'totale' => CentroAssistenza::count(),
                'con_tecnici' => CentroAssistenza::whereHas('tecnici')->count(),
                'senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
                'tecnici_totali' => User::where('livello_accesso', '2')->count(),
                'tecnici_non_assegnati' => User::where('livello_accesso', '2')
                    ->whereNull('centro_assistenza_id')->count()
            ];

            return view('admin.centri.index', compact('centri', 'stats'));

        } catch (\Exception $e) {
            Log::error('Errore dashboard admin centri', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return view('admin.centri.index')
                ->with('error', 'Errore nel caricamento della dashboard');
        }
    }

    /**
     * Form creazione centro
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        return view('admin.centri.create');
    }

    /**
     * Salva nuovo centro
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome',
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|regex:/^\d{5}$/',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:centri_assistenza,email',
        ], [
            'nome.required' => 'Il nome è obbligatorio',
            'nome.unique' => 'Nome già esistente',
            'indirizzo.required' => 'L\'indirizzo è obbligatorio',
            'citta.required' => 'La città è obbligatoria',
            'provincia.required' => 'La provincia è obbligatoria',
            'provincia.size' => 'Provincia deve essere 2 caratteri (es: AN)',
            'cap.regex' => 'CAP deve essere 5 cifre',
            'email.email' => 'Email non valida',
            'email.unique' => 'Email già utilizzata'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $centro = CentroAssistenza::create([
                'nome' => trim($request->nome),
                'indirizzo' => trim($request->indirizzo),
                'citta' => trim($request->citta),
                'provincia' => strtoupper(trim($request->provincia)),
                'cap' => $request->cap ? trim($request->cap) : null,
                'telefono' => $request->telefono ? trim($request->telefono) : null,
                'email' => $request->email ? trim(strtolower($request->email)) : null,
            ]);

            DB::commit();

            Log::info('Centro creato', [
                'centro_id' => $centro->id,
                'nome' => $centro->nome,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.centri.show', $centro)
                ->with('success', "Centro \"{$centro->nome}\" creato con successo!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Errore creazione centro', [
                'error' => $e->getMessage(),
                'data' => $request->except(['_token']),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore nella creazione del centro');
        }
    }

    /**
     * Form modifica centro
     */
    public function edit(CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        return view('admin.centri.edit', compact('centro'));
    }

    /**
     * Aggiorna centro
     */
    public function update(Request $request, CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:centri_assistenza,nome,' . $centro->id,
            'indirizzo' => 'required|string|max:500',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|regex:/^\d{5}$/',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:centri_assistenza,email,' . $centro->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
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
     * Elimina centro
     */
    public function destroy(CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        try {
            DB::beginTransaction();

            // Verifica tecnici assegnati
            $tecnici = $centro->tecnici()->count();
            if ($tecnici > 0) {
                return redirect()->back()
                    ->with('error', "Impossibile eliminare: ci sono {$tecnici} tecnici assegnati");
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

    // ================================================
    // GESTIONE TECNICI NEI CENTRI
    // ================================================

    /**
     * Assegna tecnico a centro (WEB + AJAX)
     */
    public function assegnaTecnico(Request $request, CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $message = 'Non autorizzato';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            
            return redirect()->back()->with('error', $message);
        }

        $validator = Validator::make($request->all(), [
            'tecnico_id' => 'required|exists:users,id',
            'notifica_tecnico' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dati non validi',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $tecnico = User::findOrFail($request->tecnico_id);

            // Verifica che sia un tecnico
            if (!$tecnico->isTecnico()) {
                $message = "L'utente non è un tecnico";
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                
                return redirect()->back()->with('error', $message);
            }

            // Verifica che non sia già assegnato
            if ($tecnico->centro_assistenza_id) {
                $message = "Il tecnico è già assegnato a un altro centro";
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                
                return redirect()->back()->with('error', $message);
            }

            // Assegnazione
            $tecnico->update(['centro_assistenza_id' => $centro->id]);

            DB::commit();

            Log::info('Tecnico assegnato a centro', [
                'tecnico_id' => $tecnico->id,
                'centro_id' => $centro->id,
                'assigned_by' => Auth::id()
            ]);

            $successMessage = "Tecnico \"{$tecnico->nome_completo}\" assegnato al centro \"{$centro->nome}\"";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'tecnico' => [
                        'id' => $tecnico->id,
                        'nome_completo' => $tecnico->nome_completo,
                        'specializzazione' => $tecnico->specializzazione
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
     * Rimuove tecnico da centro (WEB + AJAX)
     */
    public function rimuoviTecnico(Request $request, CentroAssistenza $centro)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $message = 'Non autorizzato';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            
            return redirect()->back()->with('error', $message);
        }

        $validator = Validator::make($request->all(), [
            'tecnico_id' => 'required|exists:users,id',
            'notifica_rimozione' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dati non validi',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $tecnico = User::findOrFail($request->tecnico_id);

            // Verifica che sia assegnato a questo centro
            if ($tecnico->centro_assistenza_id != $centro->id) {
                $message = "Il tecnico non è assegnato a questo centro";
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                
                return redirect()->back()->with('error', $message);
            }

            // Rimozione
            $tecnico->update(['centro_assistenza_id' => null]);

            DB::commit();

            Log::info('Tecnico rimosso da centro', [
                'tecnico_id' => $tecnico->id,
                'centro_id' => $centro->id,
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

    // ================================================
    // API ENDPOINTS (QUESTI ERANO I METODI MANCANTI!)
    // ================================================

    /**
     * API: Lista tecnici disponibili per assegnazione
     * QUESTO METODO MANCAVA NEL TUO CONTROLLER!
     */
    public function getTecniciDisponibili()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
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
     * API: Città per provincia
     */
    public function apiCittaPerProvincia(Request $request)
    {
        $provincia = strtoupper(trim($request->get('provincia', '')));
        
        if (!$provincia) {
            return response()->json([
                'success' => false,
                'message' => 'Provincia non specificata'
            ], 422);
        }

        try {
            $citta = CentroAssistenza::where('provincia', $provincia)
                ->distinct()
                ->orderBy('citta')
                ->pluck('citta')
                ->filter()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'citta' => $citta,
                'provincia' => $provincia,
                'total' => count($citta)
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento città per provincia', [
                'provincia' => $provincia,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento città'
            ], 500);
        }
    }
}success' => false, 
                'message' => 'Non autorizzato'
            ], 403);
        }

        try {
            // Tecnici non assegnati a nessun centro
            $tecnici = User::where('livello_accesso', '2')
                ->whereNull('centro_assistenza_id')
                ->orderBy('nome')
                ->orderBy('cognome')
                ->get(['id', 'nome', 'cognome', 'username', 'specializzazione', 'data_nascita', 'created_at'])
                ->map(function($tecnico) {
                    return [
                        'id' => $tecnico->id,
                        'nome_completo' => $tecnico->nome_completo,
                        'username' => $tecnico->username,
                        'specializzazione' => $tecnico->specializzazione ?? 'Non specificata',
                        'data_nascita' => $tecnico->data_nascita ? $tecnico->data_nascita->format('d/m/Y') : 'Non specificata',
                        'eta' => $tecnico->data_nascita ? $tecnico->data_nascita->age : null,
                        'created_at' => $tecnico->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'tecnici' => $tecnici,
                'total' => $tecnici->count()
            ]);

        } catch (\Exception $e) {
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

    /**
     * API: Dettagli di un tecnico specifico
     * QUESTO METODO MANCAVA NEL TUO CONTROLLER!
     */
    public function getDettagliTecnico(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false, 
                'message' => 'Non autorizzato'
            ], 403);
        }

        if (!$user->isTecnico()) {
            return response()->json([
                'success' => false, 
                'message' => 'L\'utente selezionato non è un tecnico'
            ], 422);
        }

        try {
            $user->load('centroAssistenza');

            $tecnico = [
                'id' => $user->id,
                'nome_completo' => $user->nome_completo,
                'username' => $user->username,
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                'data_nascita' => $user->data_nascita ? $user->data_nascita->format('d/m/Y') : 'Non specificata',
                'eta' => $user->data_nascita ? $user->data_nascita->age . ' anni' : 'N/A',
                'centro_attuale' => $user->centroAssistenza ? $user->centroAssistenza->nome : 'Nessuno',
                'created_at' => $user->created_at->format('d/m/Y H:i')
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
     * API: Statistiche di un centro
     * QUESTO METODO MANCAVA NEL TUO CONTROLLER!
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
            // Tecnici nella provincia
            $tecniciProvincia = User::whereHas('centroAssistenza', function($q) use ($centro) {
                $q->where('provincia', $centro->provincia);
            })->where('livello_accesso', '2')->count();

            $stats = [
                'tecnici_centro' => $centro->numero_tecnici,
                'tecnici_provincia' => $tecniciProvincia,
                'giorni_attivita' => $centro->created_at->diffInDays()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento statistiche centro', [
                'centro_id' => $centro->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento statistiche'
            ], 500);
        }
    }

    /**
     * API: Lista centri (AJAX)
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
                        'indirizzo_completo' => $centro->indirizzo_completo,
                        'telefono' => $centro->telefono,
                        'email' => $centro->email,
                        'numero_tecnici' => $centro->tecnici_count,
                        'ha_tecnici' => $centro->tecnici_count > 0
                    ];
                });

            return response()->json([
                'success' => true,
                'centri' => $centri,
                'total' => $centri->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Errore API lista centri', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel caricamento centri'
            ], 500);
        }
    }

    /**
     * API: Ricerca centri
     */
    public function apiSearch(Request $request)
    {
        $termine = trim($request->get('q', ''));
        
        if (strlen($termine) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Termine di ricerca troppo corto'
            ], 422);
        }

        try {
            $centri = CentroAssistenza::where(function($q) use ($termine) {
                    $q->where('nome', 'LIKE', "%{$termine}%")
                      ->orWhere('citta', 'LIKE', "%{$termine}%")
                      ->orWhere('provincia', 'LIKE', "%{$termine}%")
                      ->orWhere('indirizzo', 'LIKE', "%{$termine}%");
                })
                ->withCount('tecnici')
                ->limit(10)
                ->get(['id', 'nome', 'citta', 'provincia', 'indirizzo'])
                ->map(function($centro) {
                    return [
                        'id' => $centro->id,
                        'nome' => $centro->nome,
                        'citta' => $centro->citta,
                        'provincia' => strtoupper($centro->provincia),
                        'indirizzo_completo' => $centro->indirizzo_completo,
                        'numero_tecnici' => $centro->tecnici_count
                    ];
                });

            return response()->json([
                '