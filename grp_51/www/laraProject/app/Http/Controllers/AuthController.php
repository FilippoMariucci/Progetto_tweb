<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use App\Models\CentroAssistenza;

/**
 * Controller per la gestione dell'autenticazione e delle dashboard
 * VERSIONE PULITA - Eliminati tutti i duplicati di metodi
 */
class AuthController extends Controller
{
    // ================================================
    // AUTENTICAZIONE
    // ================================================

    /**
     * Mostra il form di login
     */
    public function showLogin()
    {
        // Se l'utente è già autenticato, reindirizza alla sua dashboard specifica
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        
        return view('auth.login');
    }

    /**
     * Gestisce il processo di autenticazione
     */
    public function login(Request $request)
    {
        // Validazione dei dati in input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'password.required' => 'Il campo password è obbligatorio',
        ]);

        // Tentativo di autenticazione con username e password
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Rigenerazione della sessione per prevenire session fixation
            $request->session()->regenerate();
            
            // Log dell'accesso riuscito
            Log::info('Login riuscito', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'livello_accesso' => Auth::user()->livello_accesso,
                'ip' => $request->ip()
            ]);

            // Reindirizzamento AUTOMATICO basato sul livello di accesso
            return $this->redirectBasedOnRole();
        }

        // Se l'autenticazione fallisce, torna indietro con errore
        throw ValidationException::withMessages([
            'username' => 'Le credenziali fornite non sono corrette.',
        ]);
    }

    /**
     * Gestisce il logout dell'utente
     */
    public function logout(Request $request)
    {
        // Log del logout
        if (Auth::check()) {
            Log::info('Logout utente', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username
            ]);
        }

        // Logout dell'utente dalla sessione
        Auth::logout();
        
        // Invalidazione della sessione e rigenerazione del token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout effettuato con successo');
    }

    // ================================================
    // DASHBOARD SPECIFICHE (UNA SOLA VERSIONE PER OGNI METODO)
    // ================================================

    /**
     * Dashboard amministratori (Livello 4) - VERSIONE DEFINITIVA
     */
    public function adminDashboard()
    {
        // Verifica autorizzazioni
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Accesso riservato agli amministratori');
        }

        $user = Auth::user();

        try {
            // === STATISTICHE COMPLETE PER ADMIN ===
            $stats = [
                // Contatori principali
                'total_utenti' => User::count(),
                'total_prodotti' => Prodotto::count(),
                'total_centri' => CentroAssistenza::count(),
                'total_soluzioni' => Malfunzionamento::count(),

                // Prodotti non assegnati allo staff
                'prodotti_non_assegnati_count' => Prodotto::whereNull('staff_assegnato_id')->count(),
                'prodotti_non_assegnati' => Prodotto::whereNull('staff_assegnato_id')
                    ->select('id', 'nome', 'modello', 'categoria', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),

                // Distribuzione utenti per livello
                'utenti_per_livello' => User::selectRaw('livello_accesso, COUNT(*) as count')
                    ->groupBy('livello_accesso')
                    ->pluck('count', 'livello_accesso')
                    ->toArray(),

                // Utenti registrati di recente
                'utenti_recenti' => User::where('created_at', '>=', now()->subMonth())
                    ->latest()
                    ->take(5)
                    ->get(['id', 'nome', 'cognome', 'username', 'livello_accesso', 'created_at']),

                // Malfunzionamenti critici
                'soluzioni_critiche' => Malfunzionamento::where('gravita', 'critica')->count(),
                
                // Centri senza tecnici
                'centri_senza_tecnici' => CentroAssistenza::whereDoesntHave('tecnici')->count(),
            ];

            return view('admin.dashboard', compact('user', 'stats'));

        } catch (\Exception $e) {
            Log::error('Errore dashboard admin', [
                'admin_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return view('admin.dashboard', compact('user'))
                ->with('error', 'Errore nel caricamento delle statistiche');
        }
    }

    /**
     * Dashboard staff aziendale (Livello 3)
     */
    public function staffDashboard()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Accesso riservato allo staff aziendale');
        }

        $user = Auth::user();

        // Prodotti assegnati al membro dello staff corrente
        $prodottiAssegnati = Prodotto::where('staff_assegnato_id', $user->id)
            ->withCount('malfunzionamenti')
            ->orderBy('nome')
            ->paginate(10);

        // Statistiche specifiche per il membro dello staff
        $stats = [
            'prodotti_gestiti' => $prodottiAssegnati->total(),
            'malfunzionamenti_totali' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                $q->where('staff_assegnato_id', $user->id);
            })->count(),
            'soluzioni_aggiunte_mese' => Malfunzionamento::whereHas('prodotto', function($q) use ($user) {
                $q->where('staff_assegnato_id', $user->id);
            })->where('created_at', '>=', now()->subMonth())->count(),
        ];

        return view('staff.dashboard', compact('user', 'prodottiAssegnati', 'stats'));
    }

    /**
     * Dashboard tecnici centri assistenza (Livello 2)
     */
    public function tecnicoDashboard()
    {
        if (!Auth::check() || !Auth::user()->isTecnico()) {
            abort(403, 'Accesso riservato ai tecnici');
        }

        $user = Auth::user();
        
        // Centro di appartenenza del tecnico
        $centro = $user->centroAssistenza;

        // Prodotti più consultati
        $prodottiConsultati = Prodotto::withCount('malfunzionamenti')
            ->where('attivo', true)
            ->orderBy('malfunzionamenti_count', 'desc')
            ->limit(8)
            ->get();

        // Statistiche specifiche per il tecnico
        $stats = [
            'centro_appartenenza' => $centro ? $centro->nome : 'Non assegnato',
            'prodotti_disponibili' => Prodotto::where('attivo', true)->count(),
            'soluzioni_disponibili' => Malfunzionamento::count(),
            'categorie_prodotti' => Prodotto::distinct()->count('categoria'),
        ];

        return view('tecnico.dashboard', compact('user', 'centro', 'prodottiConsultati', 'stats'));
    }

    /**
     * Dashboard generale - Fallback per utenti pubblici
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Se l'utente ha un livello specifico, reindirizza alla sua dashboard
        if ($user->livello_accesso >= 2) {
            return $this->redirectBasedOnRole();
        }
        
        // Dashboard base per utenti pubblici
        $stats = [
            'total_prodotti' => Prodotto::count(),
            'total_centri' => CentroAssistenza::count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }

    // ================================================
    // STORICO INTERVENTI (METODO MANCANTE - CAUSA ERRORE)
    // ================================================

    /**
     * Visualizza lo storico degli interventi tecnici
     * QUESTO METODO ERA MANCANTE E CAUSAVA L'ERRORE 404!
     */
    public function storicoInterventi(Request $request)
    {
        // Verifica autorizzazioni - accessibile a tecnici, staff e admin
        if (!Auth::check() || (!Auth::user()->isTecnico() && !Auth::user()->isStaff() && !Auth::user()->isAdmin())) {
            abort(403, 'Accesso riservato ai tecnici e staff aziendale');
        }

        $user = Auth::user();

        try {
            // Query base per gli interventi (usando i malfunzionamenti come storico)
            $query = Malfunzionamento::with(['prodotto:id,nome,modello,categoria'])
                ->orderBy('updated_at', 'desc');

            // === FILTRI BASATI SUL RUOLO ===
            if ($user->isTecnico() && $user->centro_assistenza_id) {
                // I tecnici vedono gli interventi degli ultimi 6 mesi
                $query->where('updated_at', '>=', now()->subMonths(6));
            } elseif ($user->isStaff()) {
                // Lo staff vede solo i malfunzionamenti dei prodotti che gestisce
                $query->whereHas('prodotto', function($q) use ($user) {
                    $q->where('staff_assegnato_id', $user->id);
                });
            }
            // Admin vede tutto senza filtri aggiuntivi

            // === FILTRI DALLA RICHIESTA ===
            
            // Filtro per prodotto specifico
            if ($request->filled('prodotto_id')) {
                $query->where('prodotto_id', $request->input('prodotto_id'));
            }

            // Filtro per gravità del problema
            if ($request->filled('gravita')) {
                $query->where('gravita', $request->input('gravita'));
            }

            // Filtro temporale
            if ($request->filled('periodo')) {
                switch($request->input('periodo')) {
                    case 'settimana':
                        $query->where('updated_at', '>=', now()->subWeek());
                        break;
                    case 'mese':
                        $query->where('updated_at', '>=', now()->subMonth());
                        break;
                    case 'trimestre':
                        $query->where('updated_at', '>=', now()->subMonths(3));
                        break;
                    case 'anno':
                        $query->where('updated_at', '>=', now()->subYear());
                        break;
                }
            }

            // Ricerca testuale
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descrizione', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('titolo', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('prodotto', function($q2) use ($searchTerm) {
                          $q2->where('nome', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('modello', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            // Paginazione risultati
            $interventi = $query->paginate(15);

            // === STATISTICHE STORICO ===
            $statisticheStorico = [
                'totale_interventi' => $interventi->total(),
                'interventi_settimana' => Malfunzionamento::where('updated_at', '>=', now()->subWeek())->count(),
                'per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                    ->groupBy('gravita')
                    ->pluck('count', 'gravita')
                    ->toArray(),
                'prodotti_problematici' => Prodotto::withCount('malfunzionamenti')
                    ->orderBy('malfunzionamenti_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'nome', 'modello']),
            ];

            // Lista prodotti per dropdown filtro
            $prodotti = Prodotto::select('id', 'nome', 'modello')
                ->where('attivo', true)
                ->orderBy('nome')
                ->get();

            return view('auth.storico-interventi', compact(
                'user', 
                'interventi', 
                'statisticheStorico', 
                'prodotti'
            ));

        } catch (\Exception $e) {
            Log::error('Errore storico interventi', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Errore nel caricamento dello storico interventi');
        }
    }

    // ================================================
    // REINDIRIZZAMENTO AUTOMATICO
    // ================================================

    /**
     * Reindirizza l'utente basandosi sul suo livello di accesso
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        // Reindirizzamento alle dashboard specifiche per livello
        switch ((int) $user->livello_accesso) {
            case 4: // Admin
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Benvenuto, Amministratore ' . $user->nome . '!');
                    
            case 3: // Staff
                return redirect()->route('staff.dashboard')
                    ->with('success', 'Benvenuto, ' . $user->nome . '!');
                    
            case 2: // Tecnico
                return redirect()->route('tecnico.dashboard')
                    ->with('success', 'Benvenuto, Tecnico ' . $user->nome . '!');
                    
            default: // Livello non riconosciuto
                Log::warning('Livello accesso non riconosciuto', [
                    'user_id' => $user->id,
                    'livello_accesso' => $user->livello_accesso
                ]);
                
                return redirect()->route('home')
                    ->with('warning', 'Livello di accesso non riconosciuto.');
        }
    }

    /**
     * Helper per reindirizzamento manuale alla dashboard appropriata
     */
    public function autoRedirectDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return $this->redirectBasedOnRole();
    }

    // ================================================
    // GESTIONE PROFILO UTENTE
    // ================================================

    /**
     * Mostra il profilo dell'utente corrente
     */
    public function showProfile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->load('centroAssistenza'); // Carica il centro se è un tecnico

        return view('auth.profile', compact('user'));
    }

    /**
     * Aggiorna il profilo dell'utente corrente
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Validazione dati modificabili dal profilo
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'specializzazione' => 'nullable|string|max:255', // Solo per tecnici
        ]);

        // Aggiorna i dati
        $user->update($validated);

        Log::info('Profilo utente aggiornato', [
            'user_id' => $user->id,
            'updated_fields' => array_keys($validated)
        ]);

        return redirect()->route('auth.profile')
            ->with('success', 'Profilo aggiornato con successo');
    }

    /**
     * Cambia la password dell'utente corrente
     */
    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Validazione password
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'La password attuale è obbligatoria',
            'new_password.required' => 'La nuova password è obbligatoria',
            'new_password.min' => 'La password deve essere di almeno 8 caratteri',
            'new_password.confirmed' => 'La conferma password non corrisponde',
        ]);

        // Verifica password attuale
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La password attuale non è corretta.',
            ]);
        }

        // Aggiorna la password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Log::info('Password cambiata', [
            'user_id' => $user->id,
            'username' => $user->username
        ]);

        return redirect()->route('auth.profile')
            ->with('success', 'Password modificata con successo');
    }

    // ================================================
    // REGISTRAZIONE (Solo per Admin)
    // ================================================

    /**
     * Mostra il form di registrazione (solo per admin)
     */
    public function showRegister()
    {
        // Solo gli amministratori possono registrare nuovi utenti
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato a registrare nuovi utenti');
        }

        // Carica i centri assistenza per il form
        $centri = CentroAssistenza::orderBy('nome')->get();

        return view('auth.register', compact('centri'));
    }

    /**
     * Gestisce la registrazione di un nuovo utente (solo admin)
     */
    public function register(Request $request)
    {
        // Verifica che solo gli admin possano registrare utenti
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        // Validazione completa dei dati
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'livello_accesso' => 'required|in:1,2,3,4',
            
            // Campi specifici per tecnici (livello 2)
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ], [
            'username.required' => 'Il campo username è obbligatorio',
            'username.unique' => 'Questo username è già in uso',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
            'data_nascita.required_if' => 'La data di nascita è obbligatoria per i tecnici',
            'specializzazione.required_if' => 'La specializzazione è obbligatoria per i tecnici',
            'centro_assistenza_id.required_if' => 'Il centro di assistenza è obbligatorio per i tecnici',
        ]);

        // Creazione del nuovo utente
        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'nome' => $validated['nome'],
            'cognome' => $validated['cognome'],
            'livello_accesso' => (int) $validated['livello_accesso'],
            'data_nascita' => $validated['data_nascita'] ?? null,
            'specializzazione' => $validated['specializzazione'] ?? null,
            'centro_assistenza_id' => $validated['centro_assistenza_id'] ?? null,
        ]);

        Log::info('Nuovo utente registrato', [
            'new_user_id' => $user->id,
            'new_username' => $user->username,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utente registrato con successo');
    }
}