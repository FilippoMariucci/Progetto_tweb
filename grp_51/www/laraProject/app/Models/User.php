<?php

/*
 * LINGUAGGIO: PHP 8.x con Laravel Framework 12
 * TIPO FILE: Modello Eloquent per autenticazione utenti
 * DESCRIZIONE: Modello base User di Laravel con commenti dettagliati
 * 
 * Questo è il modello standard di Laravel per la gestione degli utenti.
 * Nel tuo progetto dovrebbe essere esteso per gestire i 4 livelli di accesso:
 * - Livello 1: Pubblico (non autenticato) 
 * - Livello 2: Tecnici dei centri di assistenza
 * - Livello 3: Staff aziendale interno
 * - Livello 4: Amministratori del sistema
 */

namespace App\Models;

// IMPORT delle interfacce e classi Laravel necessarie

// use Illuminate\Contracts\Auth\MustVerifyEmail;  // Per verifica email (commentato = non usato)
use Illuminate\Database\Eloquent\Factories\HasFactory;     // Per creare factory (seeding/testing)
use Illuminate\Foundation\Auth\User as Authenticatable;    // Classe base per autenticazione Laravel
use Illuminate\Notifications\Notifiable;                   // Per invio notifiche (email, SMS, etc.)

/**
 * CLASSE PRINCIPALE DEL MODELLO USER
 * 
 * LINGUAGGIO: PHP + Laravel Eloquent ORM
 * ESTENDE: Authenticatable (classe speciale Laravel per autenticazione)
 * 
 * SPIEGAZIONE:
 * - Authenticatable fornisce funzionalità di login/logout automatiche
 * - È diverso da Model normale perché include metodi per password, remember_token, etc.
 * - Laravel usa questo modello per verificare credenziali e gestire sessioni
 */
class User extends Authenticatable
{
    /**
     * TRAITS UTILIZZATI
     * 
     * LINGUAGGIO: PHP Traits
     * 
     * SPIEGAZIONE:
     * - HasFactory: Abilita creazione di factory per seeding e testing
     * - Notifiable: Aggiunge metodi per inviare notifiche (email, database, etc.)
     * - I trait sono come "mix-in": aggiungono metodi alla classe
     * - /** @use: Annotation PHPDoc per specificare il tipo di factory
     */
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * CONFIGURAZIONE MASS ASSIGNMENT
     * 
     * LINGUAGGIO: PHP Array + Laravel Security
     * TIPO: Proprietà protetta della classe
     * 
     * @var list<string> Array di stringhe (PHPDoc type hint)
     * 
     * SPIEGAZIONE:
     * - $fillable protegge da mass assignment attacks
     * - Solo questi campi possono essere assegnati con User::create($data)
     * - Se $data contiene campi non in $fillable, vengono ignorati silenziosamente
     * - Sicurezza: evita che utenti malintenzionati assegnino campi non voluti
     * 
     * ESEMPIO ATTACCO PREVENUTO:
     * - Senza $fillable: User::create(['name' => 'Mario', 'is_admin' => true])
     * - Con $fillable: il campo 'is_admin' viene ignorato
     * 
     * PER IL TUO PROGETTO MANCANO:
     * - 'username' (al posto di 'email')
     * - 'livello_accesso' (2,3,4)
     * - 'nome', 'cognome'
     * - 'data_nascita' (per tecnici)
     * - 'specializzazione' (per tecnici)
     * - 'centro_assistenza_id' (per tecnici)
     */
    protected $fillable = [
        'name',         // Nome utente (nel tuo progetto dovrebbe essere 'nome' e 'cognome' separati)
        'email',        // Email (nel tuo progetto usi 'username' invece)
        'password',     // Password hashata automaticamente
    ];

    /**
     * CONFIGURAZIONE CAMPI NASCOSTI
     * 
     * LINGUAGGIO: PHP Array + Laravel Serialization
     * TIPO: Proprietà protetta della classe
     * 
     * @var list<string> Array di stringhe
     * 
     * SPIEGAZIONE:
     * - $hidden nasconde campi quando il modello viene convertito in JSON/array
     * - Sicurezza: evita che password e token vengano esposti nelle API
     * - Si applica a: toArray(), toJson(), response JSON, etc.
     * - I campi esistono ancora nel modello, ma non vengono serializzati
     * 
     * QUANDO SI APPLICA:
     * - return response()->json($user); // password e remember_token NON inclusi
     * - echo json_encode($user);        // password e remember_token NON inclusi
     * - $array = $user->toArray();      // password e remember_token NON inclusi
     * 
     * SICUREZZA CRITICA:
     * - Senza $hidden, le password verrebbero esposte nelle risposte API
     * - remember_token è usato per "ricordami" e non deve essere pubblico
     */
    protected $hidden = [
        'password',         // Password hashata (MUST essere nascosta)
        'remember_token',   // Token per "ricordami" (MUST essere nascosto)
    ];

    /**
     * CONFIGURAZIONE CAST AUTOMATICI
     * 
     * LINGUAGGIO: PHP Method + Laravel Casting System
     * TIPO: Metodo protetto che restituisce array di configurazione
     * 
     * @return array<string, string> Array associativo campo => tipo
     * 
     * SPIEGAZIONE:
     * - I cast convertono automaticamente i dati dal/al database
     * - Laravel chiama questo metodo per sapere come convertire i campi
     * - 'datetime': converte stringa DB in oggetto Carbon (DateTime esteso)
     * - 'hashed': hasha automaticamente la password quando assegnata
     * 
     * ESEMPI DI CONVERSIONE AUTOMATICA:
     * 
     * LETTURA DAL DATABASE:
     * - DB: '2025-01-15 10:30:00' → Oggetto Carbon con metodi ->format(), ->diffForHumans(), etc.
     * 
     * SCRITTURA AL DATABASE:
     * - $user->password = 'miapassword123'; → Laravel chiama Hash::make() automaticamente
     * - DB salva: '$2y$10$abcd...' (password hashata con bcrypt)
     * 
     * VANTAGGI:
     * - Non devi ricordare di hashare manualmente le password
     * - Le date sono sempre oggetti Carbon (più facili da manipolare)
     * - Conversioni trasparenti e sicure
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',    // Timestamp verifica email → Carbon object
            'password' => 'hashed',               // Password string → hash bcrypt automatico
        ];
    }

    // ================================================
    // SEZIONE METODI MANCANTI PER IL TUO PROGETTO
    // ================================================
    
    /*
     * NOTA: Il modello attuale è il base di Laravel.
     * Per il tuo progetto di assistenza tecnica dovresti aggiungere:
     * 
     * 1. RELAZIONI ELOQUENT:
     *    - belongsTo(CentroAssistenza::class) per tecnici livello 2
     *    - hasMany(Prodotto::class, 'staff_assegnato_id') per staff livello 3
     *    - hasMany(Malfunzionamento::class, 'creato_da') per tracking modifiche
     * 
     * 2. METODI DI AUTORIZZAZIONE:
     *    - isAdmin(): bool per verificare livello 4
     *    - isStaff(): bool per verificare livello 3  
     *    - isTecnico(): bool per verificare livello 2
     *    - hasLevel(int $level): bool per controllo generico
     * 
     * 3. ACCESSORS AGGIUNTIVI:
     *    - getNomeCompletoAttribute(): string per "Nome Cognome"
     *    - getLivelloNomeAttribute(): string per "Amministratore", "Staff", etc.
     *    - getEtaAttribute(): int se data_nascita presente
     * 
     * 4. SCOPE PER QUERY:
     *    - scopeLevel(Builder $query, int $level) per filtrare per livello
     *    - scopeTecnici(Builder $query) per solo tecnici
     *    - scopeStaff(Builder $query) per solo staff
     *    - scopeAdmin(Builder $query) per solo admin
     * 
     * 5. METODI CONFIGURAZIONE:
     *    - boot() per eventi di creazione/modifica/eliminazione
     *    - getRouteKeyName() se usi username invece di id nelle route
     * 
     * ESEMPIO IMPLEMENTAZIONE METODO AUTORIZZAZIONE:
     * 
     * public function isAdmin(): bool
     * {
     *     return $this->livello_accesso === 4;
     * }
     * 
     * public function isTecnico(): bool  
     * {
     *     return $this->livello_accesso === 2;
     * }
     * 
     * ESEMPIO ACCESSOR NOME COMPLETO:
     * 
     * public function getNomeCompletoAttribute(): string
     * {
     *     return $this->nome . ' ' . $this->cognome;
     * }
     * 
     * ESEMPIO RELAZIONE CENTRO ASSISTENZA:
     * 
     * public function centroAssistenza()
     * {
     *     return $this->belongsTo(CentroAssistenza::class, 'centro_assistenza_id');
     * }
     * 
     * ESEMPIO SCOPE TECNICI:
     * 
     * public function scopeTecnici(Builder $query): void
     * {
     *     $query->where('livello_accesso', 2);
     * }
     * 
     * UTILIZZO SCOPE:
     * - User::tecnici()->get()     // Tutti i tecnici
     * - User::level(4)->first()    // Primo admin trovato
     */
}

/*
 * RIEPILOGO CARATTERISTICHE MODELLO USER STANDARD:
 * 
 * 1. AUTENTICAZIONE INTEGRATA:
 *    - Login/logout automatici tramite Laravel Auth
 *    - Hash password automatico con bcrypt
 *    - Gestione "ricordami" con remember_token
 *    - Reset password integrato
 * 
 * 2. SICUREZZA:
 *    - Mass assignment protection con $fillable
 *    - Campi sensibili nascosti con $hidden
 *    - Password sempre hashate con cast 'hashed'
 * 
 * 3. FACTORY E SEEDING:
 *    - HasFactory per creare dati di test
 *    - Integrazione con database seeder
 * 
 * 4. NOTIFICHE:
 *    - Notifiable per email/SMS automatici
 *    - Reset password via email
 *    - Notifiche custom per eventi sistema
 * 
 * 5. SERIALIZZAZIONE:
 *    - Conversione automatica in JSON per API
 *    - Cast automatici per date e password
 *    - Controllo campi esposti pubblicamente
 * 
 * DIFFERENZE CON MODEL NORMALE:
 * - Estende Authenticatable invece di Model
 * - Include metodi per autenticazione (login, password reset, etc.)
 * - Ha fields speciali: password, remember_token, email_verified_at
 * - Integrato con sistema Auth di Laravel (middleware, guard, etc.)
 * 
 * CONFIGURAZIONE DATABASE ATTESA:
 * - Tabella: users (plurale)
 * - Campi obbligatori: id, email, password, created_at, updated_at
 * - Campi opzionali: name, email_verified_at, remember_token
 * 
 * PER IL TUO PROGETTO DOVRESTI AVERE:
 * - Tabella: users
 * - Campi: id, username, password, nome, cognome, livello_accesso, 
 *          data_nascita, specializzazione, centro_assistenza_id,
 *          created_at, updated_at, remember_token
 */