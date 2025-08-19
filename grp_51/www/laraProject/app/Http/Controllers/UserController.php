<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Mostra l'elenco di tutti gli utenti (per admin)
     */
    public function index(Request $request)
    {
        // Query base con relazioni
        $query = User::with('centroAssistenza');

        // Filtro per livello di accesso
        if ($request->filled('livello')) {
            $query->where('livello_accesso', $request->input('livello'));
        }

        // Filtro per centro assistenza (solo tecnici)
        if ($request->filled('centro')) {
            $query->where('centro_assistenza_id', $request->input('centro'));
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

        // Ordinamento
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginazione
        $users = $query->paginate(15);

        // Statistiche per la dashboard
        $stats = [
            'total' => User::count(),
            'admin' => User::where('livello_accesso', '4')->count(),
            'staff' => User::where('livello_accesso', '3')->count(),
            'tecnici' => User::where('livello_accesso', '2')->count(),
            'pubblici' => User::where('livello_accesso', '1')->count(),
        ];

        // Centri per filtro
        $centri = CentroAssistenza::orderBy('nome')->get();

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
     * Salva un nuovo utente nel database
     */
    public function store(Request $request)
    {
        // Validazione completa dei dati
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'livello_accesso' => 'required|in:2,3,4',

            // Campi condizionali per i tecnici (livello 2)
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ], [
            // Messaggi di errore personalizzati
            'username.required' => 'Il campo username è obbligatorio',
            'username.unique' => 'Questo username è già in uso',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
            'livello_accesso.in' => 'Livello di accesso non valido',
            'data_nascita.required_if' => 'La data di nascita è obbligatoria per i tecnici',
            'data_nascita.before' => 'La data di nascita deve essere nel passato',
            'specializzazione.required_if' => 'La specializzazione è obbligatoria per i tecnici',
            'centro_assistenza_id.required_if' => 'Il centro di assistenza è obbligatorio per i tecnici',
            'centro_assistenza_id.exists' => 'Il centro di assistenza selezionato non esiste',
        ]);

        try {
            // Creazione del nuovo utente
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']), // Hash della password
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'livello_accesso' => $validated['livello_accesso'],
                'data_nascita' => $validated['data_nascita'] ?? null,
                'specializzazione' => $validated['specializzazione'] ?? null,
                'centro_assistenza_id' => $validated['centro_assistenza_id'] ?? null,
            ]);

            // Log della creazione utente per audit
            Log::info('Nuovo utente creato dall\'admin', [
                'new_user_id' => $user->id,
                'new_username' => $user->username,
                'new_user_level' => $user->livello_accesso,
                'created_by_admin_id' => Auth::id(),
                'created_by_admin_username' => Auth::user()->username,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "Utente '{$user->username}' creato con successo!");

        } catch (\Exception $e) {
            // Log dell'errore
            Log::error('Errore nella creazione utente', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'form_data' => $request->except('password', 'password_confirmation') // Esclude password dai log
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Errore nella creazione dell\'utente. Riprova.']);
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