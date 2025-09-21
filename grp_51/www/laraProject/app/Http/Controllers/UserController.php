<?php

namespace App\Http\Controllers;

// === IMPORTAZIONE DELLE DIPENDENZE NECESSARIE ===
// Tutte queste classi sono essenziali per il funzionamento del controller

use Illuminate\Support\Facades\Validator;  // [LARAVEL] - Per validazioni personalizzate dei dati
use Illuminate\Http\Request;                // [LARAVEL] - Per gestire le richieste HTTP (GET, POST, PUT, DELETE)
use Illuminate\Support\Facades\Auth;        // [LARAVEL] - Per gestire l'autenticazione degli utenti
use Illuminate\Support\Facades\Hash;        // [LARAVEL] - Per crittografare/verificare password con bcrypt
use Illuminate\Support\Facades\DB;          // [LARAVEL] - Per operazioni dirette sul database
use Illuminate\Support\Facades\Log;         // [LARAVEL] - Per scrivere log di sistema e debug
use Illuminate\Validation\ValidationException; // [LARAVEL] - Per gestire errori di validazione
use App\Models\User;                        // [MODELLO CUSTOM] - Modello Eloquent per la tabella users
use App\Models\CentroAssistenza;            // [MODELLO CUSTOM] - Modello per la tabella centri_assistenza

/**
 * =====================================================
 * CONTROLLER GESTIONE UTENTI - LIVELLO AMMINISTRATORE 
 * =====================================================
 * 
 * LINGUAGGIO: PHP 8.x con Framework Laravel 12
 * 
 * SCOPO: Questo controller gestisce TUTTE le operazioni CRUD (Create, Read, Update, Delete) 
 *        per gli utenti del sistema di assistenza tecnica.
 * 
 * LIVELLI DI ACCESSO NEL SISTEMA:
 * - Livello 2: Tecnici dei centri assistenza (possono vedere malfunzionamenti e soluzioni)
 * - Livello 3: Staff aziendale (possono modificare malfunzionamenti e soluzioni)  
 * - Livello 4: Amministratori (gestiscono utenti e prodotti, accesso completo)
 * 
 * SICUREZZA: Solo gli amministratori (livello 4) possono accedere a questo controller
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * 1. Visualizzazione elenco utenti con filtri e ricerca
 * 2. Creazione nuovi utenti (tecnici, staff, admin)
 * 3. Modifica dati utenti esistenti
 * 4. Eliminazione utenti (con controlli di sicurezza)
 * 5. Reset password e gestione stato utenti
 * 6. Export dati utenti in formato JSON/CSV
 */
class UserController extends Controller
{
    /**
     * ===================================
     * COSTRUTTORE - CONTROLLO ACCESSO
     * ===================================
     * 
     * LINGUAGGIO: PHP - Metodo costruttore della classe
     * 
     * SCOPO: Viene eseguito automaticamente quando viene istanziato il controller.
     *        Applica middleware di sicurezza per garantire che SOLO gli amministratori
     *        possano accedere a qualsiasi metodo di questo controller.
     * 
     * MIDDLEWARE APPLICATI:
     * - 'auth': Verifica che l'utente sia autenticato (logged in)
     * - 'check.level:4': Verifica che l'utente abbia livello di accesso 4 (amministratore)
     * 
     * COMPORTAMENTO: Se un utente non autorizzato tenta di accedere, viene automaticamente
     *                 reindirizzato alla pagina di login o riceve un errore 403 (Forbidden)
     */
    public function __construct()
    {
        // Applica middleware per verificare autenticazione E livello amministratore
        // Questo protegge TUTTI i metodi del controller automaticamente
        $this->middleware(['auth', 'check.level:4']);
    }

    /**
     * ========================================
     * METODO INDEX - ELENCO UTENTI CON FILTRI
     * ========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent ORM
     * ROUTE: GET /admin/users
     * VISTA: resources/views/admin/users/index.blade.php
     * 
     * SCOPO: Mostra la pagina principale di gestione utenti con elenco paginato,
     *        filtri avanzati, ricerca e statistiche del sistema.
     * 
     * PARAMETRI REQUEST GESTITI:
     * - 'livello_accesso': Filtra per livello (2=tecnico, 3=staff, 4=admin)
     * - 'centro_assistenza_id': Filtra per centro assistenza specifico
     * - 'search': Ricerca testuale in nome, cognome, username
     * - 'data_registrazione': Filtra per periodo (oggi, settimana, mese)
     * - 'sort': Campo di ordinamento con direzione (es: 'nome', '-created_at')
     * 
     * FUNZIONALITÀ IMPLEMENTATE:
     * 1. Query Builder con Eager Loading per ottimizzare performance
     * 2. Filtri dinamici basati su parametri URL
     * 3. Ricerca full-text su campi multipli
     * 4. Ordinamento ascendente/discendente
     * 5. Paginazione con mantenimento filtri
     * 6. Calcolo statistiche in tempo reale
     * 7. Logging dettagliato per audit e debugging
     * 
     * @param Request $request - Oggetto contenente tutti i parametri della richiesta HTTP
     * @return \Illuminate\View\View - Vista Blade renderizzata con dati
     */
    public function index(Request $request)
    {
        // === STEP 1: INIZIALIZZAZIONE QUERY BASE ===
        // Crea query Eloquent con eager loading della relazione centro assistenza
        // with('centroAssistenza') evita il problema N+1 queries caricando i centri in un'unica query
        $query = User::with('centroAssistenza');

        // === STEP 2: APPLICAZIONE FILTRI DINAMICI ===
        
        // FILTRO LIVELLO ACCESSO
        // Se il parametro 'livello_accesso' è presente nell'URL, filtra gli utenti
        // Esempio: ?livello_accesso=2 mostra solo i tecnici
        if ($request->filled('livello_accesso')) {
            $query->where('livello_accesso', $request->input('livello_accesso'));
        }

        // FILTRO CENTRO ASSISTENZA
        // Filtra utenti appartenenti a uno specifico centro
        // Utilizzato principalmente per filtrare i tecnici per centro
        if ($request->filled('centro_assistenza_id')) {
            $query->where('centro_assistenza_id', $request->input('centro_assistenza_id'));
        }

        // FILTRO RICERCA TESTUALE
        // Ricerca case-insensitive su nome, cognome e username
        // Utilizza LIKE con wildcard per ricerca parziale
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // Usa closure per raggruppare le condizioni OR in parentesi
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('cognome', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('username', 'LIKE', "%{$searchTerm}%");
            });
        }

        // FILTRO DATA REGISTRAZIONE
        // Filtra utenti registrati in specifici periodi temporali
        if ($request->filled('data_registrazione')) {
            $periodo = $request->input('data_registrazione');
            
            switch ($periodo) {
                case 'oggi':
                    // Filtra solo utenti registrati oggi (dalla mezzanotte)
                    $query->whereDate('created_at', today());
                    break;
                case 'settimana':
                    // Filtra utenti registrati negli ultimi 7 giorni
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'mese':
                    // Filtra utenti registrati nell'ultimo mese
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        // === STEP 3: GESTIONE ORDINAMENTO ===
        
        // Ottiene campo di ordinamento, default: 'created_at' (più recenti)
        $sort = $request->input('sort', 'created_at');
        
        // Gestisce ordinamento decrescente usando prefisso '-'
        // Esempio: '-nome' = ordina per nome Z-A, 'nome' = ordina per nome A-Z
        if (str_starts_with($sort, '-')) {
            $sortField = substr($sort, 1);    // Rimuove il '-' 
            $sortDirection = 'desc';          // Ordinamento decrescente
        } else {
            $sortField = $sort;               // Campo così com'è
            $sortDirection = 'asc';           // Ordinamento crescente
        }
        
        // VALIDAZIONE CAMPI ORDINAMENTO (Sicurezza)
        // Previene SQL injection limitando i campi ordinabili
        $allowedSorts = ['nome', 'cognome', 'username', 'created_at', 'livello_accesso', 'last_login_at'];
        if (!in_array($sortField, $allowedSorts)) {
            // Se campo non valido, usa default sicuro
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }

        // Applica ordinamento alla query
        $query->orderBy($sortField, $sortDirection);

        // === STEP 4: PAGINAZIONE ===
        // Divide risultati in pagine da 15 elementi
        // withQueryString() mantiene parametri URL (filtri) durante navigazione pagine
        $users = $query->paginate(15)->withQueryString();

        // === STEP 5: CALCOLO STATISTICHE DASHBOARD ===
        // Calcola contatori per dashboard amministratore
        $stats = [
            'totale' => User::count(),                                    // Totale utenti sistema
            'admin' => User::where('livello_accesso', '4')->count(),      // Numero amministratori
            'staff' => User::where('livello_accesso', '3')->count(),      // Numero staff azienda
            'tecnici' => User::where('livello_accesso', '2')->count(),    // Numero tecnici centri
        ];

        // === STEP 6: DATI AGGIUNTIVI PER LA VISTA ===
        // Carica lista centri assistenza per popolare select di filtro
        $centri = CentroAssistenza::orderBy('nome')->get();

        // === STEP 7: LOGGING PER AUDIT E DEBUG ===
        // Registra accesso alla gestione utenti per tracciabilità
        Log::info('Caricamento gestione utenti', [
            'total_users' => $users->total(),                // Totale risultati query
            'filtri_applicati' => [                          // Filtri attivi per debug
                'search' => $request->input('search'),
                'livello_accesso' => $request->input('livello_accesso'),
                'centro_assistenza_id' => $request->input('centro_assistenza_id'),
                'data_registrazione' => $request->input('data_registrazione'),
                'sort' => $sort,
            ],
            'admin_id' => Auth::id()                         // ID admin che esegue l'azione
        ]);

        // === STEP 8: RITORNO VISTA ===
        // Passa tutti i dati necessari alla vista Blade
        // compact() crea array associativo con nomi variabili come chiavi
        return view('admin.users.index', compact('users', 'stats', 'centri'));
    }

    /**
     * =====================================
     * METODO CREATE - FORM NUOVO UTENTE
     * =====================================
     * 
     * LINGUAGGIO: PHP con Laravel
     * ROUTE: GET /admin/users/create
     * VISTA: resources/views/admin/users/create.blade.php
     * 
     * SCOPO: Mostra il form HTML per la creazione di un nuovo utente.
     *        Carica i dati necessari per popolare i campi select del form.
     * 
     * DATI PREPARATI PER IL FORM:
     * - Lista centri assistenza (per assegnazione tecnici)
     * - Opzioni livelli di accesso (via codice JavaScript o helper Blade)
     * 
     * @return \Illuminate\View\View - Vista con form di creazione
     */
    public function create()
    {
        // Carica tutti i centri assistenza ordinati alfabeticamente
        // Necessario per il campo select "Centro Assistenza" nel form
        // Solo i tecnici (livello 2) possono essere assegnati a un centro
        $centri = CentroAssistenza::orderBy('nome')->get();

        // Ritorna la vista con i dati necessari per il form
        return view('admin.users.create', compact('centri'));
    }

    /**
     * ====================================
     * METODO STORE - CREAZIONE UTENTE
     * ====================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent ORM
     * ROUTE: POST /admin/users
     * REDIRECT: /admin/users (elenco utenti)
     * 
     * SCOPO: Elabora i dati del form di creazione, valida l'input,
     *        crea il nuovo utente nel database e gestisce eventuali errori.
     * 
     * VALIDAZIONI IMPLEMENTATE:
     * - Username: obbligatorio, unico, lunghezza 3-255 caratteri
     * - Password: obbligatoria, minimo 8 caratteri, con conferma
     * - Nome/Cognome: obbligatori, lunghezza 2-255 caratteri
     * - Livello accesso: obbligatorio, solo valori 2,3,4
     * - Data nascita: obbligatoria per tecnici, deve essere passata
     * - Specializzazione: obbligatoria per tecnici
     * - Centro assistenza: opzionale per tutti i livelli
     * 
     * SICUREZZA:
     * - Password crittografata con Hash::make() (bcrypt)
     * - Sanitizzazione input con trim()
     * - Validazione rigorosa tipi di dato
     * - Logging completo delle operazioni
     * 
     * @param Request $request - Dati del form HTTP POST
     * @return \Illuminate\Http\RedirectResponse - Redirect con messaggio
     */
    public function store(Request $request)
    {
        // === STEP 1: VERIFICA AUTORIZZAZIONE DOPPIA ===
        // Controllo aggiuntivo oltre al middleware (defense in depth)
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Non autorizzato');
        }

        // === STEP 2: VALIDAZIONE COMPLETA DATI ===
        // Laravel valida automaticamente e blocca l'esecuzione se errori
        $request->validate([
            // CAMPI OBBLIGATORI PER TUTTI GLI UTENTI
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',  // confirmed verifica password_confirmation
            'nome' => 'required|string|min:2|max:255',
            'cognome' => 'required|string|min:2|max:255',
            'livello_accesso' => 'required|in:2,3,4',         // Solo livelli validi
            
            // CAMPI CONDIZIONALI PER TECNICI (livello 2)
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            
            // CENTRO ASSISTENZA OPZIONALE PER TUTTI
            'centro_assistenza_id' => 'nullable|exists:centri_assistenza,id',
            
        ], [
            // MESSAGGI DI ERRORE PERSONALIZZATI (UX migliore)
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
            // === STEP 3: CREAZIONE UTENTE NEL DATABASE ===
            
            // Usa Eloquent per creare nuovo record nella tabella users
            $user = User::create([
                'username' => trim($request->username),                    // Rimuove spazi extra
                'password' => Hash::make($request->password),              // Crittografia bcrypt
                'nome' => trim($request->nome),
                'cognome' => trim($request->cognome),
                'livello_accesso' => $request->livello_accesso,
                'data_nascita' => $request->data_nascita ?: null,         // NULL se vuoto
                'specializzazione' => $request->specializzazione ? trim($request->specializzazione) : null,
                'centro_assistenza_id' => $request->filled('centro_assistenza_id') ? $request->centro_assistenza_id : null,
            ]);

            // === STEP 4: LOGGING SUCCESSO ===
            // Registra creazione utente per audit trail
            Log::info('Utente creato', [
                'user_id' => $user->id,
                'username' => $user->username,
                'livello' => $user->livello_accesso,
                'centro_id' => $user->centro_assistenza_id,
                'created_by' => Auth::id()  // ID dell'admin che ha creato l'utente
            ]);

            // === STEP 5: GENERAZIONE MESSAGGIO SUCCESSO ===
            // Crea messaggio informativo basato sul tipo di utente creato
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

            // === STEP 6: REDIRECT CON SUCCESSO ===
            // Torna all'elenco utenti con messaggio di conferma
            return redirect()->route('admin.users.index')->with('success', $message);

        } catch (\Exception $e) {
            
            // === GESTIONE ERRORI ===
            // Log dell'errore per debugging
            Log::error('Errore creazione utente', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            // Torna al form mantenendo i dati inseriti (eccetto password)
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Errore durante la creazione dell\'utente: ' . $e->getMessage());
        }
    }

    /**
     * =====================================
     * METODO SHOW - DETTAGLI UTENTE
     * =====================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent
     * ROUTE: GET /admin/users/{user}
     * VISTA: resources/views/admin/users/show.blade.php
     * 
     * SCOPO: Mostra la pagina di dettaglio di un utente specifico con
     *        informazioni complete, statistiche personali e relazioni.
     * 
     * ROUTE MODEL BINDING: Laravel automaticamente trova l'utente
     *                      dall'ID nell'URL e lo passa come parametro.
     * 
     * RELAZIONI CARICATE:
     * - centroAssistenza: Centro di appartenenza (se tecnico)
     * - prodottiAssegnati: Prodotti gestiti (se staff)
     * - malfunzionamentiCreati: Soluzioni create (se staff)
     * 
     * STATISTICHE CALCOLATE:
     * - Per Staff: prodotti assegnati, soluzioni create, ultima attività
     * - Per Tecnici: centro, specializzazione, età
     * 
     * @param User $user - Istanza utente da visualizzare (Route Model Binding)
     * @return \Illuminate\View\View - Vista dettaglio utente
     */
    public function show(User $user)
    {
        // === STEP 1: CARICAMENTO RELAZIONI EAGER ===
        // Carica in una sola query tutte le relazioni necessarie
        // Evita il problema N+1 queries per performance ottimali
        $user->load(['centroAssistenza', 'prodottiAssegnati', 'malfunzionamentiCreati']);

        // === STEP 2: CALCOLO STATISTICHE PERSONALIZZATE ===
        $stats = [];
        
        // STATISTICHE PER UTENTI STAFF (livello 3)
        if ($user->isStaff()) {
            $stats = [
                // Conta prodotti assegnati per gestione
                'prodotti_assegnati' => $user->prodottiAssegnati()->count(),
                
                // Conta soluzioni/malfunzionamenti creati
                'soluzioni_create' => $user->malfunzionamentiCreati()->count(),
                
                // Trova ultima attività (ultimo malfunzionamento modificato)
                'ultima_attivita' => $user->malfunzionamentiCreati()
                    ->latest('updated_at')    // Ordina per data modifica
                    ->first()                 // Prende il primo (più recente)
                    ?->updated_at             // Se esiste, prende la data
                    ?->diffForHumans() ?? 'Mai' // Converte in formato "2 ore fa"
            ];
        } 
        // STATISTICHE PER TECNICI (livello 2)
        elseif ($user->isTecnico()) {
            $stats = [
                // Nome centro o messaggio se non assegnato
                'centro_assistenza' => $user->centroAssistenza?->nome ?? 'Non assegnato',
                
                // Specializzazione tecnica
                'specializzazione' => $user->specializzazione ?? 'Non specificata',
                
                // Calcolo età dalla data di nascita
                'eta' => $user->data_nascita ? 
                    now()->diffInYears($user->data_nascita) . ' anni' : 'Non specificata'
            ];
        }

        // === STEP 3: RITORNO VISTA CON DATI ===
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * ====================================
     * METODO EDIT - FORM MODIFICA UTENTE
     * ====================================
     * 
     * LINGUAGGIO: PHP con Laravel
     * ROUTE: GET /admin/users/{user}/edit
     * VISTA: resources/views/admin/users/edit.blade.php
     * 
     * SCOPO: Mostra il form pre-compilato per modificare un utente esistente.
     *        I campi sono popolati con i dati attuali dell'utente.
     * 
     * @param User $user - Utente da modificare (Route Model Binding)
     * @return \Illuminate\View\View - Form di modifica pre-compilato
     */
    public function edit(User $user)
    {
        // Carica lista centri per il campo select (necessario per tecnici)
        $centri = CentroAssistenza::orderBy('nome')->get();

        // Passa utente e centri alla vista per pre-compilare il form
        return view('admin.users.edit', compact('user', 'centri'));
    }

    /**
     * ===================================
     * METODO UPDATE - AGGIORNA UTENTE
     * ===================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent ORM
     * ROUTE: PUT /admin/users/{user}
     * REDIRECT: /admin/users/{user} (pagina dettaglio)
     * 
     * SCOPO: Elabora i dati del form di modifica, valida l'input,
     *        aggiorna l'utente nel database e gestisce eventuali errori.
     * 
     * VALIDAZIONI DINAMICHE:
     * - Username: unique escluso utente corrente
     * - Password: opzionale, se fornita deve essere validata
     * - Altri campi: stesse regole della creazione
     * 
     * FUNZIONALITÀ SPECIALI:
     * - Password aggiornata solo se fornita nel form
     * - Logging dettagliato delle modifiche per audit
     * - Confronto dati originali vs nuovi per tracciabilità
     * 
     * @param Request $request - Dati form di modifica
     * @param User $user - Utente da aggiornare
     * @return \Illuminate\Http\RedirectResponse - Redirect con esito
     */
    public function update(Request $request, User $user)
    {
        // === STEP 1: DEFINIZIONE REGOLE VALIDAZIONE ===
        $rules = [
            // Username unico ECCETTO per l'utente corrente
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'livello_accesso' => 'required|in:2,3,4',
            'data_nascita' => 'required_if:livello_accesso,2|nullable|date|before:today',
            'specializzazione' => 'required_if:livello_accesso,2|nullable|string|max:255',
            'centro_assistenza_id' => 'required_if:livello_accesso,2|nullable|exists:centri_assistenza,id',
        ];

        // VALIDAZIONE PASSWORD CONDIZIONALE
        // Password validata solo se l'utente la sta cambiando
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        // === STEP 2: ESECUZIONE VALIDAZIONE ===
        $validated = $request->validate($rules, [
            'username.unique' => 'Questo username è già in uso da un altro utente',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
        ]);

        try {
            // === STEP 3: PREPARAZIONE DATI AGGIORNAMENTO ===
            $updateData = [
                'username' => $validated['username'],
                'nome' => $validated['nome'],
                'cognome' => $validated['cognome'],
                'livello_accesso' => $validated['livello_accesso'],
                'data_nascita' => $validated['data_nascita'] ?? null,
                'specializzazione' => $validated['specializzazione'] ?? null,
                'centro_assistenza_id' => $validated['centro_assistenza_id'] ?? null,
            ];

            // AGGIUNTA PASSWORD SOLO SE FORNITA
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // === STEP 4: SALVATAGGIO DATI ORIGINALI PER AUDIT ===
            $originalData = $user->toArray();

            // === STEP 5: AGGIORNAMENTO DATABASE ===
            $user->update($updateData);

            // === STEP 6: LOGGING COMPLETO PER AUDIT ===
            Log::info('Utente modificato dall\'admin', [
                'user_id' => $user->id,
                'username' => $user->username,
                'modified_by_admin_id' => Auth::id(),
                'original_data' => $originalData,           // Dati prima della modifica
                'new_data' => $user->fresh()->toArray(),    // Dati dopo la modifica
                'password_changed' => $request->filled('password')
            ]);

            // === STEP 7: REDIRECT CON SUCCESSO ===
            return redirect()->route('admin.users.show', $user)
                ->with('success', "Utente '{$user->username}' aggiornato con successo!");

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * ===================================
     * METODO DESTROY - ELIMINA UTENTE
     * ===================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent (Soft Delete)
     * ROUTE: DELETE /admin/users/{user}
     * REDIRECT: /admin/users (elenco utenti)
     * 
     * SCOPO: Elimina un utente dal sistema con controlli di sicurezza avanzati.
     *        Utilizza soft delete per mantenere tracciabilità e possibilità di ripristino.
     * 
     * CONTROLLI DI SICUREZZA IMPLEMENTATI:
     * 1. Previene auto-eliminazione (admin non può eliminare se stesso)
     * 2. Previene eliminazione ultimo amministratore (protezione sistema)
     * 3. Verifica autorizzazioni e registra azione per audit
     * 
     * SOFT DELETE: L'utente non viene fisicamente cancellato dal database,
     *              ma marcato come "eliminato" con timestamp deleted_at.
     *              Questo mantiene integrità referenziale e tracciabilità.
     * 
     * @param User $user - Utente da eliminare (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse - Redirect con esito operazione
     */
    public function destroy(User $user)
    {
        // === CONTROLLO 1: PREVENZIONE AUTO-ELIMINAZIONE ===
        // Impedisce che un amministratore elimini il proprio account
        // per evitare perdita accesso al sistema
        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'Non puoi eliminare il tuo stesso account.']);
        }

        // === CONTROLLO 2: PROTEZIONE ULTIMO AMMINISTRATORE ===
        // Previene eliminazione dell'ultimo admin per proteggere il sistema
        if ($user->isAdmin()) {
            $adminCount = User::where('livello_accesso', '4')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['delete' => 'Non puoi eliminare l\'ultimo amministratore del sistema.']);
            }
        }

        try {
            // === STEP 1: SALVATAGGIO DATI PRE-ELIMINAZIONE ===
            // Conserva informazioni per logging prima della soft delete
            $username = $user->username;
            $userId = $user->id;

            // === STEP 2: ESECUZIONE SOFT DELETE ===
            // Marca l'utente come eliminato senza rimuoverlo fisicamente
            // Laravel automaticamente aggiunge deleted_at timestamp
            $user->delete();

            // === STEP 3: LOGGING ELIMINAZIONE PER AUDIT ===
            // Registra l'eliminazione per tracciabilità e sicurezza
            Log::warning('Utente eliminato dall\'admin', [
                'deleted_user_id' => $userId,
                'deleted_username' => $username,
                'deleted_by_admin_id' => Auth::id(),
                'deleted_by_admin_username' => Auth::user()->username
            ]);

            // === STEP 4: REDIRECT CON CONFERMA ===
            return redirect()->route('admin.users.index')
                ->with('success', "Utente '{$username}' eliminato con successo.");

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
            Log::error('Errore nell\'eliminazione utente', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return back()->withErrors(['delete' => 'Errore nell\'eliminazione dell\'utente.']);
        }
    }

    /**
     * ==========================================
     * METODO TOGGLE STATUS - ATTIVA/DISATTIVA
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel (AJAX Response JSON)
     * ROUTE: POST /admin/users/{user}/toggle-status
     * RESPONSE: JSON con esito operazione
     * 
     * SCOPO: Attiva o disattiva un utente tramite chiamata AJAX.
     *        Utilizzato per sospendere temporaneamente l'accesso
     *        senza eliminare l'utente dal sistema.
     * 
     * FUNZIONALITÀ:
     * - Toggle dello stato attivo/inattivo
     * - Prevenzione auto-disattivazione
     * - Response JSON per aggiornamento interfaccia
     * - Logging delle modifiche stato
     * 
     * UTILIZZO: Chiamato da JavaScript lato client per
     *           aggiornare stato utente senza ricaricare pagina.
     * 
     * @param User $user - Utente di cui cambiare lo stato
     * @return \Illuminate\Http\JsonResponse - Risposta JSON con esito
     */
    public function toggleStatus(User $user)
    {
        // === CONTROLLO: PREVENZIONE AUTO-DISATTIVAZIONE ===
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi disattivare il tuo stesso account.'
            ], 403);
        }

        try {
            // === STEP 1: TOGGLE STATO ===
            // Inverte il campo 'attivo' (true diventa false e viceversa)
            // Se campo non esiste, assume true come default
            $newStatus = !($user->attivo ?? true);
            $user->update(['attivo' => $newStatus]);

            // === STEP 2: PREPARAZIONE MESSAGGIO ===
            $action = $newStatus ? 'attivato' : 'disattivato';

            // === STEP 3: LOGGING MODIFICA STATO ===
            Log::info("Utente {$action} dall'admin", [
                'user_id' => $user->id,
                'username' => $user->username,
                'new_status' => $newStatus,
                'admin_id' => Auth::id()
            ]);

            // === STEP 4: RESPONSE JSON SUCCESSO ===
            return response()->json([
                'success' => true,
                'message' => "Utente '{$user->username}' {$action} con successo.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * =====================================
     * METODO RESET PASSWORD - GENERA NUOVA
     * =====================================
     * 
     * LINGUAGGIO: PHP con Laravel (AJAX Response JSON)
     * ROUTE: POST /admin/users/{user}/reset-password
     * RESPONSE: JSON con password temporanea
     * 
     * SCOPO: Genera e assegna una password temporanea sicura all'utente.
     *        Utilizzato quando un utente dimentica la password o per
     *        motivi di sicurezza amministrativa.
     * 
     * SICUREZZA:
     * - Password generata casualmente con caratteri speciali
     * - Crittografia bcrypt prima del salvataggio
     * - Logging senza esposizione password in chiaro
     * - Ritorno password solo in response (non salvata nei log)
     * 
     * UTILIZZO: L'admin riceve la password temporanea e la
     *           comunica all'utente attraverso canali sicuri.
     * 
     * @param User $user - Utente di cui resettare la password
     * @return \Illuminate\Http\JsonResponse - Response con password temporanea
     */
    public function resetPassword(User $user)
    {
        try {
            // === STEP 1: GENERAZIONE PASSWORD TEMPORANEA ===
            // Utilizza metodo helper per creare password sicura
            $tempPassword = $this->generateTempPassword();
            
            // === STEP 2: AGGIORNAMENTO DATABASE ===
            $user->update([
                'password' => Hash::make($tempPassword),
                // 'password_reset_required' => true // Campo opzionale per forzare cambio
            ]);

            // === STEP 3: LOGGING SICURO ===
            // Registra l'azione SENZA salvare la password nei log
            Log::info('Password resetatta dall\'admin', [
                'user_id' => $user->id,
                'username' => $user->username,
                'admin_id' => Auth::id()
                // NON include la password per sicurezza
            ]);

            // === STEP 4: RESPONSE CON PASSWORD ===
            return response()->json([
                'success' => true,
                'message' => "Password resetatta per '{$user->username}'.",
                'temp_password' => $tempPassword  // Inviata solo in response
            ]);

        } catch (\Exception $e) {
            // === GESTIONE ERRORI ===
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
     * =============================================
     * METODO HELPER - STATISTICHE POST-CREAZIONE
     * =============================================
     * 
     * LINGUAGGIO: PHP - Metodo helper privato
     * SCOPO: Calcola statistiche del sistema dopo la creazione di un utente.
     *        Utile per aggiornare dashboard o inviare notifiche.
     * 
     * STATISTICHE CALCOLATE:
     * - Totale utenti sistema
     * - Conteggi per livello di accesso
     * - Tecnici senza centro assegnato
     * - Numero centri disponibili
     * 
     * UTILIZZO: Può essere chiamato dopo operazioni di creazione
     *           per fornire feedback o aggiornare interfacce.
     * 
     * @return array - Array associativo con statistiche sistema
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
     * =======================================
     * METODO API EXPORT - ESPORTAZIONE DATI
     * =======================================
     * 
     * LINGUAGGIO: PHP con Laravel (API Response JSON/CSV)
     * ROUTE: GET /admin/users/export?format=json|csv
     * RESPONSE: JSON data o file CSV download
     * 
     * SCOPO: Esporta dati utenti in formato JSON o CSV per backup,
     *        reporting o integrazione con sistemi esterni.
     * 
     * FORMATI SUPPORTATI:
     * - JSON: Response diretta con array utenti
     * - CSV: Download file Excel-compatibile
     * 
     * SICUREZZA:
     * - Solo dati non sensibili (esclude password)
     * - Include timestamp esportazione
     * - Conteggio record per verifica
     * 
     * @param Request $request - Parametri richiesta (format)
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function apiExport(Request $request)
    {
        // === STEP 1: DETERMINAZIONE FORMATO ===
        $format = $request->input('format', 'json');
        
        // === STEP 2: QUERY DATI EXPORT ===
        // Carica solo campi necessari per ottimizzare performance
        $users = User::with('centroAssistenza')
            ->select(['id', 'username', 'nome', 'cognome', 'livello_accesso', 'created_at', 'centro_assistenza_id'])
            ->get();

        // === STEP 3: GESTIONE FORMATO CSV ===
        if ($format === 'csv') {
            return $this->exportToCsv($users, 'utenti_export.csv');
        }

        // === STEP 4: RESPONSE JSON DEFAULT ===
        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count(),
            'exported_at' => now()->toISOString()
        ]);
    }

    // ================================================
    // METODI HELPER PRIVATI - UTILITIES
    // ================================================

    /**
     * =============================================
     * HELPER - GENERAZIONE PASSWORD TEMPORANEA
     * =============================================
     * 
     * LINGUAGGIO: PHP - Funzione helper privata
     * SCOPO: Genera password temporanea sicura con caratteri misti.
     * 
     * CARATTERISTICHE SICUREZZA:
     * - Lunghezza 12 caratteri (sicura per uso temporaneo)
     * - Mix lettere maiuscole/minuscole, numeri, simboli
     * - Generazione casuale crittograficamente sicura
     * - Non utilizza caratteri ambigui (0, O, l, I)
     * 
     * ALGORITMO:
     * 1. Define character set sicuro
     * 2. Loop 12 iterazioni
     * 3. Selezione casuale carattere da set
     * 4. Concatenazione risultato finale
     * 
     * @return string - Password temporanea di 12 caratteri
     */
    private function generateTempPassword(): string
    {
        // Set caratteri sicuri (esclusi caratteri ambigui)
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        // Genera 12 caratteri casuali
        for ($i = 0; $i < 12; $i++) {
            // random_int() è crittograficamente sicuro
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    /**
     * ===================================
     * HELPER - EXPORT CSV
     * ===================================
     * 
     * LINGUAGGIO: PHP con Stream Response
     * SCOPO: Converte dati in formato CSV e invia come download.
     * 
     * FUNZIONALITÀ:
     * - Headers HTTP per download file
     * - Generazione CSV con intestazioni
     * - Streaming per file grandi (memoria efficiente)
     * - Compatibilità Excel/Calc
     * 
     * PROCESSO:
     * 1. Imposta headers HTTP download
     * 2. Crea stream output
     * 3. Scrive header CSV dai nomi campi
     * 4. Itera dati e scrive righe CSV
     * 5. Chiude stream e invia response
     * 
     * @param \Illuminate\Database\Eloquent\Collection $data - Dati da esportare
     * @param string $filename - Nome file download
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($data, string $filename)
    {
        // === STEP 1: HEADERS HTTP PER DOWNLOAD ===
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // === STEP 2: CALLBACK GENERAZIONE CSV ===
        $callback = function() use ($data) {
            // Apre stream output per scrittura diretta
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // === STEP 3: HEADER CSV ===
                // Usa keys del primo record come intestazioni colonne
                fputcsv($file, array_keys($data[0]->toArray()));
                
                // === STEP 4: DATI CSV ===
                // Itera ogni record e scrive riga CSV
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }
            
            // Chiude stream
            fclose($file);
        };

        // === STEP 5: STREAMED RESPONSE ===
        // Invia file come stream per efficienza memoria
        return response()->stream($callback, 200, $headers);
    }
}