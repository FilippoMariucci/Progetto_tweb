<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\CentroAssistenza;

/**
 * Controller per la gestione degli utenti (CRUD completo)
 * Accessibile solo agli amministratori (livello 4)
 */
class UserController extends Controller
{
    /**
     * Costruttore - verifica che solo gli admin possano accedere
     */
    public function __construct()
    {
        // Middleware per verificare che solo gli admin possano accedere
        $this->middleware(['auth', 'check.level:4']);
    }



/**
 * Mostra l'elenco di tutti gli utenti (per admin) - VERSIONE CORRETTA
 * Fix per filtri e ordinamento che non funzionavano
 * 
 * Sostituisci questo metodo nel tuo UserController.php
 */
public function index(Request $request)
{
    // Query base con relazioni
    $query = User::with('centroAssistenza');

    // === FIX: FILTRI CORRETTI ===
    
    // Filtro per livello di accesso (CORRETTO)
    if ($request->filled('livello_accesso')) {
        $query->where('livello_accesso', $request->input('livello_accesso'));
    }

    // Filtro per centro assistenza (CORRETTO)
    if ($request->filled('centro_assistenza_id')) {
        $query->where('centro_assistenza_id', $request->input('centro_assistenza_id'));
    }

    // Ricerca per nome/cognome/username
    if ($request->filled('search')) {
        $searchTerm = $request->input('search');
        $query->where(function($q) use ($searchTerm) {
            $q->where('nome', 'LIKE', "%{$searchTerm}%")
              ->orWhere('cognome', 'LIKE', "%{$searchTerm}%")
              ->orWhere('username', 'LIKE', "%{$searchTerm}%");
        });
    }

    // === FIX: FILTRO DATA REGISTRAZIONE ===
    if ($request->filled('data_registrazione')) {
        $periodo = $request->input('data_registrazione');
        
        switch ($periodo) {
            case 'oggi':
                $query->whereDate('created_at', today());
                break;
            case 'settimana':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'mese':
                $query->where('created_at', '>=', now()->subMonth());
                break;
        }
    }

    // === FIX: ORDINAMENTO CORRETTO ===
    $sort = $request->input('sort', 'created_at');
    
    // Gestisce ordinamento crescente/decrescente dal parametro sort
    if (str_starts_with($sort, '-')) {
        $sortField = substr($sort, 1);
        $sortDirection = 'desc';
    } else {
        $sortField = $sort;
        $sortDirection = 'asc';
    }
    
    // Validazione campi ordinamento
    $allowedSorts = ['nome', 'cognome', 'username', 'created_at', 'livello_accesso', 'last_login_at'];
    if (!in_array($sortField, $allowedSorts)) {
        $sortField = 'created_at';
        $sortDirection = 'desc';
    }

    $query->orderBy($sortField, $sortDirection);

    // === PAGINAZIONE ===
    $users = $query->paginate(15)->withQueryString(); // Importante: withQueryString() mantiene i filtri

    // === STATISTICHE CORRETTE ===
    $stats = [
        'totale' => User::count(),
        'admin' => User::where('livello_accesso', '4')->count(),
        'staff' => User::where('livello_accesso', '3')->count(),
        'tecnici' => User::where('livello_accesso', '2')->count(),
    ];

    // Centri per filtro
    $centri = CentroAssistenza::orderBy('nome')->get();

    // Log per debug
    Log::info('Caricamento gestione utenti', [
        'total_users' => $users->total(),
        'filtri_applicati' => [
            'search' => $request->input('search'),
            'livello_accesso' => $request->input('livello_accesso'),
            'centro_assistenza_id' => $request->input('centro_assistenza_id'),
            'data_registrazione' => $request->input('data_registrazione'),
            'sort' => $sort,
        ],
        'admin_id' => Auth::id()
    ]);

    return view('admin.users.index', compact('users', 'stats', 'centri'));
}

    /**
     * Mostra il form per creare un nuovo utente
     */
    public function create()
    {
        // Carica i centri assistenza per il form (per i tecnici)
        $centri = CentroAssistenza::orderBy('nome')->get();

        return view('admin.users.create', compact('centri'));
    }

  /**
 * Metodo store SEMPLIFICATO per UserController
 * Risolve il problema del mancato redirect
 */
public function store(Request $request)
{
    // Verifica autorizzazione
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        abort(403, 'Non autorizzato');
    }

    // === VALIDAZIONE ===
    $request->validate([
        'username' => 'required|string|min:3|max:255|unique:users,username',
        'password' => 'required|string|min:8|confirmed',
        'nome' => 'required|string|min:2|max:255',
        'cognome' => 'required|string|min:2|max:255',
        'livello_accesso' => 'required|in:2,3,4',
        
        // Campi tecnico condizionali
        'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
        'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
        
        // Centro SEMPRE opzionale
        'centro_assistenza_id' => 'nullable|exists:centri_assistenza,id',
        
    ], [
        'username.required' => 'Username obbligatorio',
        'username.min' => 'Username troppo corto',
        'username.unique' => 'Username già esistente',
        'password.required' => 'Password obbligatoria',
        'password.min' => 'Password troppo corta (minimo 8 caratteri)',
        'password.confirmed' => 'Le password non coincidono',
        'nome.required' => 'Nome obbligatorio',
        'cognome.required' => 'Cognome obbligatorio',
        'livello_accesso.required' => 'Livello di accesso obbligatorio',
        'data_nascita.required_if' => 'Data nascita obbligatoria per i tecnici',
        'specializzazione.required_if' => 'Specializzazione obbligatoria per i tecnici',
    ]);

    try {
        // === CREAZIONE UTENTE ===
        
        $user = User::create([
            'username' => trim($request->username),
            'password' => Hash::make($request->password),
            'nome' => trim($request->nome),
            'cognome' => trim($request->cognome),
            'livello_accesso' => $request->livello_accesso,
            'data_nascita' => $request->data_nascita ?: null,
            'specializzazione' => $request->specializzazione ? trim($request->specializzazione) : null,
            // Centro opzionale
            'centro_assistenza_id' => $request->filled('centro_assistenza_id') ? $request->centro_assistenza_id : null,
        ]);

        // Log successo
        Log::info('Utente creato', [
            'user_id' => $user->id,
            'username' => $user->username,
            'livello' => $user->livello_accesso,
            'centro_id' => $user->centro_assistenza_id,
            'created_by' => Auth::id()
        ]);

        // === MESSAGGIO DI SUCCESSO ===
        $livelli = ['2' => 'Tecnico', '3' => 'Staff', '4' => 'Amministratore'];
        $livelloNome = $livelli[$user->livello_accesso] ?? 'Utente';
        
        $message = "✅ {$livelloNome} '{$user->username}' ({$user->nome} {$user->cognome}) creato con successo!";
        
        // Informazioni aggiuntive per tecnici
        if ($user->livello_accesso == '2') {
            if ($user->centro_assistenza_id) {
                $centro = CentroAssistenza::find($user->centro_assistenza_id);
                $message .= " Assegnato al centro '{$centro->nome}'.";
            } else {
                $message .= " Centro di assistenza non assegnato.";
            }
        }

        // === REDIRECT DIRETTO ===
        return redirect()->route('admin.users.index')->with('success', $message);

    } catch (\Exception $e) {
        
        Log::error('Errore creazione utente', [
            'error' => $e->getMessage(),
            'admin_id' => Auth::id()
        ]);

        // Torna al form con errore
        return back()
            ->withInput($request->except('password', 'password_confirmation'))
            ->with('error', 'Errore durante la creazione dell\'utente: ' . $e->getMessage());
    }
}

    /**
     * Mostra i dettagli di un utente specifico
     */
    public function show(User $user)
    {
        // Carica le relazioni necessarie
        $user->load(['centroAssistenza', 'prodottiAssegnati', 'malfunzionamentiCreati']);

        // Statistiche dell'utente
        $stats = [];
        
        if ($user->isStaff()) {
            $stats = [
                'prodotti_assegnati' => $user->prodottiAssegnati()->count(),
                'soluzioni_create' => $user->malfunzionamentiCreati()->count(),
                'ultima_attivita' => $user->malfunzionamentiCreati()
                    ->latest('updated_at')
                    ->first()
                    ?->updated_at
                    ?->diffForHumans() ?? 'Mai'
            ];
        } elseif ($user->isTecnico()) {
            $stats = [
                'centro_assistenza' => $user->centroAssistenza?->nome ?? 'Non assegnato',
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                'eta' => $user->data_nascita ? 
                    now()->diffInYears($user->data_nascita) . ' anni' : 'Non specificata'
            ];
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Mostra il form per modificare un utente esistente
     */
    public function edit(User $user)
    {
        // Carica i centri assistenza per il form
        $centri = CentroAssistenza::orderBy('nome')->get();

        return view('admin.users.edit', compact('user', 'centri'));
    }

    /**
     * Aggiorna un utente esistente
     */
    public function update(Request $request, User $user)
    {
        // Validazione con regole dinamiche
        $rules = [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'livello_accesso' => 'required|in:2,3,4',
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ];

        // Se è fornita una nuova password, validala
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules, [
            'username.unique' => 'Questo username è già in uso da un altro utente',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
        ]);

        try {
            // Prepara i dati per l'aggiornamento
            $updateData = [
                'username' => $validated['username'],
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'livello_accesso' => $validated['livello_accesso'],
                'data_nascita' => $validated['data_nascita'] ?? null,
                'specializzazione' => $validated['specializzazione'] ?? null,
                'centro_assistenza_id' => $validated['centro_assistenza_id'] ?? null,
            ];

            // Aggiorna password solo se fornita
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // Salva i dati originali per confronto
            $originalData = $user->toArray();

            // Aggiorna l'utente
            $user->update($updateData);

            // Log delle modifiche per audit
            Log::info('Utente modificato dall\'admin', [
                'user_id' => $user->id,
                'username' => $user->username,
                'modified_by_admin_id' => Auth::id(),
                'original_data' => $originalData,
                'new_data' => $user->fresh()->toArray(),
                'password_changed' => $request->filled('password')
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', "Utente '{$user->username}' aggiornato con successo!");

        } catch (\Exception $e) {
            Log::error('Errore nell\'aggiornamento utente', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Errore nell\'aggiornamento dell\'utente.']);
        }
    }

   
    /**
     * Elimina un utente (soft delete)
     */
    public function destroy(User $user)
    {
        // Previene l'auto-eliminazione
        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'Non puoi eliminare il tuo stesso account.']);
        }

        // Previene l'eliminazione dell'ultimo admin
        if ($user->isAdmin()) {
            $adminCount = User::where('livello_accesso', '4')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['delete' => 'Non puoi eliminare l\'ultimo amministratore del sistema.']);
            }
        }

        try {
            $username = $user->username;
            $userId = $user->id;

            // Soft delete dell'utente
            $user->delete();

            // Log dell'eliminazione
            Log::warning('Utente eliminato dall\'admin', [
                'deleted_user_id' => $userId,
                'deleted_username' => $username,
                'deleted_by_admin_id' => Auth::id(),
                'deleted_by_admin_username' => Auth::user()->username
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "Utente '{$username}' eliminato con successo.");

        } catch (\Exception $e) {
            Log::error('Errore nell\'eliminazione utente', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['delete' => 'Errore nell\'eliminazione dell\'utente.']);
        }
    }

    /**
     * Attiva/disattiva un utente
     */
    public function toggleStatus(User $user)
    {
        // Previene la disattivazione del proprio account
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi disattivare il tuo stesso account.'
            ], 403);
        }

        try {
            // Toggle dello stato (assumendo un campo 'attivo')
            $newStatus = !($user->attivo ?? true);
            $user->update(['attivo' => $newStatus]);

            $action = $newStatus ? 'attivato' : 'disattivato';

            Log::info("Utente {$action} dall'admin", [
                'user_id' => $user->id,
                'username' => $user->username,
                'new_status' => $newStatus,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Utente '{$user->username}' {$action} con successo.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Errore toggle status utente', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel cambio stato dell\'utente.'
            ], 500);
        }
    }

    /**
     * Reset della password di un utente
     */
    public function resetPassword(User $user)
    {
        try {
            // Genera una password temporanea
            $tempPassword = $this->generateTempPassword();
            
            // Aggiorna la password
            $user->update([
                'password' => Hash::make($tempPassword),
                // 'password_reset_required' => true // Se hai questo campo
            ]);

            Log::info('Password resetatta dall\'admin', [
                'user_id' => $user->id,
                'username' => $user->username,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Password resetatta per '{$user->username}'.",
                'temp_password' => $tempPassword
            ]);

        } catch (\Exception $e) {
            Log::error('Errore reset password', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel reset della password.'
            ], 500);
        }
    }

    /**
 * Metodo helper per ottenere statistiche post-creazione
 * Utile per aggiornare dashboard o notifiche
 */
private function getPostCreationStats(): array
{
    return [
        'total_users' => User::count(),
        'admin_count' => User::where('livello_accesso', '4')->count(),
        'staff_count' => User::where('livello_accesso', '3')->count(),
        'tecnici_count' => User::where('livello_accesso', '2')->count(),
        'tecnici_without_center' => User::where('livello_accesso', '2')
            ->whereNull('centro_assistenza_id')
            ->count(),
        'centers_available' => CentroAssistenza::count()
    ];
}

    /**
     * Export degli utenti per API
     */
    public function apiExport(Request $request)
    {
        $format = $request->input('format', 'json');
        
        $users = User::with('centroAssistenza')
            ->select(['id', 'username', 'nome', 'cognome', 'livello_accesso', 'created_at', 'centro_assistenza_id'])
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($users, 'utenti_export.csv');
        }

        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count(),
            'exported_at' => now()->toISOString()
        ]);
    }

    // ================================================
    // METODI HELPER PRIVATI
    // ================================================

    /**
     * Genera una password temporanea sicura
     */
    private function generateTempPassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < 12; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    /**
     * Esporta dati in formato CSV
     */
    private function exportToCsv($data, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // Header CSV
                fputcsv($file, array_keys($data[0]->toArray()));
                
                // Dati
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}