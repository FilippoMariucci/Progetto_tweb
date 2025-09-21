<?php

namespace App\Models;

// === IMPORTAZIONE DEPENDENCIES LARAVEL ===
use Illuminate\Database\Eloquent\Factories\HasFactory;  // [LARAVEL] - Trait per generazione test data
use Illuminate\Database\Eloquent\Model;                  // [LARAVEL] - Classe base Eloquent ORM
use Illuminate\Database\Eloquent\Builder;                // [LARAVEL] - Query Builder per scope personalizzati

/**
 * =================================================================
 * MODELLO ELOQUENT CENTRO ASSISTENZA - SISTEMA GESTIONE TECNICI
 * =================================================================
 * 
 * LINGUAGGIO: PHP 8.x con Laravel Eloquent ORM
 * TABELLA DATABASE: centri_assistenza
 * 
 * SCOPO: Modello Eloquent che rappresenta i centri di assistenza tecnica
 *        distribuiti sul territorio. Ogni centro può avere più tecnici
 *        assegnati e fornisce supporto post-vendita per i prodotti.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * 1. Gestione dati geografici (indirizzo, città, provincia, CAP)
 * 2. Informazioni di contatto (telefono, email)
 * 3. Relazioni con tecnici assegnati al centro
 * 4. Scope per ricerche geografiche ottimizzate
 * 5. Accessors per formattazione dati (indirizzo completo, telefono)
 * 6. Metodi helper per statistiche e gestione orari
 * 
 * CONTESTO PROGETTO: Parte del sistema di assistenza tecnica dove
 *                    i tecnici esterni accedono tramite web per
 *                    consultare soluzioni ai malfunzionamenti dei prodotti.
 * 
 * RELAZIONI DATABASE:
 * - 1:N con User (tecnici livello 2) tramite centro_assistenza_id
 * 
 * DESIGN PATTERN: Active Record (Eloquent ORM)
 */
class CentroAssistenza extends Model
{
    // === TRAIT LARAVEL ===
    use HasFactory; // Abilita uso di Factory per seeding e testing

    /**
     * =======================================
     * CONFIGURAZIONE TABELLA DATABASE
     * =======================================
     * 
     * LINGUAGGIO: PHP - Proprietà protette di configurazione Laravel
     * 
     * Specifica il nome della tabella nel database. Laravel di default
     * userebbe "centro_assistenzas" (plurale inglese), ma noi abbiamo
     * una tabella con nome personalizzato italiano.
     */
    protected $table = 'centri_assistenza';

    /**
     * =======================================
     * MASS ASSIGNMENT PROTECTION
     * =======================================
     * 
     * LINGUAGGIO: PHP Array - Configurazione sicurezza Laravel
     * 
     * SCOPO: Lista dei campi che possono essere assegnati in massa tramite
     *        create() o fill(). Protegge da Mass Assignment Vulnerability.
     * 
     * SICUREZZA: Solo questi campi possono essere popolati da input utente.
     *            Altri campi (id, timestamps) sono protetti da modifiche dirette.
     * 
     * CAMPI CONSENTITI:
     * - nome: Nome del centro assistenza
     * - indirizzo: Via e numero civico
     * - citta: Città di ubicazione
     * - provincia: Sigla provincia (2 caratteri)
     * - cap: Codice avviamento postale
     * - telefono: Numero di telefono centro
     * - email: Email di contatto
     */
    protected $fillable = [
        'nome',           // Nome centro (es: "Centro Assistenza Nord Milano")
        'indirizzo',      // Indirizzo (es: "Via Roma 123")
        'citta',          // Città (es: "Milano")
        'provincia',      // Provincia (es: "MI")
        'cap',            // CAP (es: "20100")
        'telefono',       // Telefono (es: "02-12345678")
        'email',          // Email (es: "milano@assistenza.it")
    ];

    /**
     * =======================================
     * CAST AUTOMATICI TIPI DATO
     * =======================================
     * 
     * LINGUAGGIO: PHP - Metodo Laravel per type casting
     * 
     * SCOPO: Define automatiche conversioni di tipo per i campi.
     *        Laravel automaticamente converte i valori dal database
     *        nel tipo PHP specificato e viceversa.
     * 
     * ESEMPI:
     * - 'datetime' converte timestamp SQL in oggetti Carbon per PHP
     * - Facilita operazioni su date senza conversioni manuali
     * 
     * @return array - Array associativo campo => tipo
     */
    protected function casts(): array
    {
        return [
            // Timestamp automatici Laravel convertiti in oggetti Carbon
            'created_at' => 'datetime',  // Data creazione record
            'updated_at' => 'datetime',  // Data ultima modifica record
        ];
    }

    // ================================================
    // RELAZIONI ELOQUENT - OBJECT RELATIONAL MAPPING
    // ================================================

    /**
     * ==========================================
     * RELAZIONE 1:N - TECNICI DEL CENTRO
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent Relations
     * TIPO RELAZIONE: One-to-Many (hasMany)
     * 
     * SCOPO: Definisce la relazione tra un centro assistenza e i suoi tecnici.
     *        Un centro può avere molti tecnici assegnati, ma ogni tecnico
     *        appartiene a un solo centro.
     * 
     * BUSINESS LOGIC: Solo gli utenti con livello_accesso = '2' sono tecnici.
     *                 Il filtro WHERE assicura di recuperare solo i tecnici.
     * 
     * FOREIGN KEY: centro_assistenza_id nella tabella users
     * 
     * UTILIZZO:
     * - $centro->tecnici: Ottiene Collection di tutti i tecnici
     * - $centro->tecnici()->count(): Conta tecnici senza caricarli
     * - $centro->tecnici()->where(...): Aggiunge filtri alla relazione
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tecnici()
    {
        return $this->hasMany(User::class, 'centro_assistenza_id')
                    ->where('livello_accesso', '2');  // Solo tecnici (livello 2)
    }

    /**
     * ==========================================
     * RELAZIONE 1:N - TUTTI GLI UTENTI
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent Relations
     * TIPO RELAZIONE: One-to-Many (hasMany)
     * 
     * SCOPO: Relazione più generica che include TUTTI gli utenti collegati
     *        al centro, indipendentemente dal loro livello di accesso.
     * 
     * DIFFERENZA da tecnici(): Questa relazione non filtra per livello,
     *                         quindi include anche staff o admin eventualmente
     *                         associati al centro.
     * 
     * UTILIZZO: Principalmente per statistiche o gestione completa utenti centro.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utenti()
    {
        return $this->hasMany(User::class, 'centro_assistenza_id');
    }

    // ================================================
    // QUERY SCOPES - METODI PER QUERY OTTIMIZZATE
    // ================================================

    /**
     * ==========================================
     * SCOPE RICERCA PER CITTÀ
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes
     * TIPO: Local Scope (metodo che inizia con 'scope')
     * 
     * SCOPO: Crea scope riutilizzabile per filtrare centri per città.
     *        Permette ricerca parziale case-insensitive.
     * 
     * UTILIZZO:
     * - CentroAssistenza::citta('Milano')->get()
     * - $query->citta('Roma') dentro altri scope
     * 
     * RICERCA: LIKE con wildcard per matching parziale
     *          Es: "Mila" troverà "Milano", "Milanello", etc.
     * 
     * @param Builder $query - Query builder Laravel su cui applicare il filtro
     * @param string $citta - Nome città da cercare (anche parziale)
     * @return void - Modifica direttamente la query passata
     */
    public function scopeCitta(Builder $query, string $citta): void
    {
        $query->where('citta', 'LIKE', "%{$citta}%");
    }

    /**
     * ==========================================
     * SCOPE RICERCA PER PROVINCIA
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes
     * 
     * SCOPO: Filtra centri per provincia (sigla a 2 caratteri).
     *        Standardizza input in uppercase per consistenza.
     * 
     * NORMALIZZAZIONE: strtoupper() garantisce che input come 'mi' 
     *                  diventi 'MI' per matching corretto.
     * 
     * UTILIZZO:
     * - CentroAssistenza::provincia('MI')->get()
     * - Ricerca esatta, non parziale (a differenza di città)
     * 
     * @param Builder $query - Query builder per applicare filtro
     * @param string $provincia - Sigla provincia (es: 'mi', 'rm', 'na')
     * @return void
     */
    public function scopeProvincia(Builder $query, string $provincia): void
    {
        $query->where('provincia', strtoupper($provincia));
    }

    /**
     * ==========================================
     * SCOPE RICERCA GEOGRAFICA GENERALE
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes + Closure
     * 
     * SCOPO: Ricerca full-text su tutti i campi geografici del centro.
     *        Permette di trovare un centro cercando qualsiasi informazione
     *        geografica (nome, città, provincia, indirizzo).
     * 
     * LOGICA: Usa OR per cercare il termine in tutti i campi rilevanti.
     *         La closure raggruppa le condizioni OR in parentesi SQL.
     * 
     * QUERY SQL GENERATA:
     * WHERE (nome LIKE '%termine%' OR citta LIKE '%termine%' OR 
     *        provincia LIKE '%termine%' OR indirizzo LIKE '%termine%')
     * 
     * UTILIZZO:
     * - CentroAssistenza::ricercaGeografica('Milano')->get()
     * - Trova centri che contengono "Milano" in qualsiasi campo
     * 
     * @param Builder $query - Query builder base
     * @param string $termine - Termine di ricerca da cercare ovunque
     * @return void
     */
    public function scopeRicercaGeografica(Builder $query, string $termine): void
    {
        $query->where(function($q) use ($termine) {
            $q->where('nome', 'LIKE', "%{$termine}%")
              ->orWhere('citta', 'LIKE', "%{$termine}%")
              ->orWhere('provincia', 'LIKE', "%{$termine}%")
              ->orWhere('indirizzo', 'LIKE', "%{$termine}%");
        });
    }

    /**
     * ==========================================
     * SCOPE CENTRI CON TECNICI DISPONIBILI
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent whereHas
     * 
     * SCOPO: Filtra solo i centri che hanno almeno un tecnico assegnato.
     *        Utile per mostrare centri operativi o calcolare copertura territoriale.
     * 
     * MECCANISMO: whereHas() verifica esistenza relazione senza caricarla.
     *             Più efficiente di with() + filter perché lavora a livello SQL.
     * 
     * QUERY SQL: Genera EXISTS subquery per performance ottimali.
     * 
     * UTILIZZO:
     * - CentroAssistenza::conTecnici()->get()
     * - Ottieni solo centri con staff disponibile
     * 
     * @param Builder $query - Query builder da filtrare
     * @return void
     */
    public function scopeConTecnici(Builder $query): void
    {
        $query->whereHas('tecnici');
    }

    /**
     * ==========================================
     * SCOPE ORDINAMENTO GEOGRAFICO STANDARD
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Builder orderBy
     * 
     * SCOPO: Applica ordinamento geografico standard per liste centri.
     *        Ordine logico: Provincia -> Città -> Nome centro.
     * 
     * LOGICA BUSINESS: Facilita navigazione lista centri raggruppando
     *                  geograficamente le strutture per chiarezza utente.
     * 
     * UTILIZZO:
     * - CentroAssistenza::ordinatoGeograficamente()->get()
     * - Ottieni lista centri ordinata geograficamente
     * 
     * @param Builder $query - Query da ordinare
     * @return void
     */
    public function scopeOrdinatoGeograficamente(Builder $query): void
    {
        $query->orderBy('provincia')->orderBy('citta')->orderBy('nome');
    }

    // ================================================
    // ACCESSORS - ATTRIBUTI CALCOLATI DINAMICAMENTE
    // ================================================

    /**
     * ==============================================
     * ACCESSOR INDIRIZZO COMPLETO FORMATTATO
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Laravel Accessor Pattern
     * PATTERN: getAttribute() automatic per convenzione naming
     * 
     * SCOPO: Crea un attributo virtuale che formatta l'indirizzo completo
     *        combinando tutti i campi geografici in una stringa leggibile.
     * 
     * NAMING CONVENTION: getXxxAttribute() -> $model->xxx
     *                    getIndirizzoCompletoAttribute() -> $centro->indirizzo_completo
     * 
     * FORMATO OUTPUT: "Via Roma 123, 20100 Milano (MI)"
     * 
     * LOGICA:
     * 1. Parte da indirizzo base
     * 2. Aggiunge CAP se presente
     * 3. Aggiunge città sempre
     * 4. Aggiunge provincia in parentesi se presente
     * 
     * UTILIZZO:
     * - $centro->indirizzo_completo (automatico via accessor)
     * - Nelle viste per display indirizzo completo
     * - Per integrazione Google Maps o servizi terzi
     * 
     * @return string - Indirizzo formattato per display
     */
    public function getIndirizzoCompletoAttribute(): string
    {
        // Inizia dall'indirizzo base
        $indirizzo = $this->indirizzo;

        // Aggiunge CAP se disponibile
        if ($this->cap) {
            $indirizzo .= ', ' . $this->cap;
        }

        // Aggiunge sempre la città
        $indirizzo .= ' ' . $this->citta;

        // Aggiunge provincia in parentesi se disponibile
        if ($this->provincia) {
            $indirizzo .= ' (' . strtoupper($this->provincia) . ')';
        }

        return $indirizzo;
    }

    /**
     * ==============================================
     * ACCESSOR CONTEGGIO TECNICI
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Laravel Accessor con Query
     * 
     * SCOPO: Calcola dinamicamente il numero di tecnici assegnati al centro.
     *        Utile per dashboard e statistiche senza dover caricare tutti i tecnici.
     * 
     * PERFORMANCE: Usa count() per ottenere solo il numero senza caricare record.
     * 
     * UTILIZZO:
     * - $centro->numero_tecnici (automatico)
     * - Dashboard amministratori
     * - Statistiche centri assistenza
     * 
     * @return int - Numero tecnici assegnati al centro
     */
    public function getNumeroTecniciAttribute(): int
    {
        return $this->tecnici()->count();
    }

    /**
     * ==============================================
     * METODO VERIFICA PRESENZA TECNICI
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Metodo helper pubblico
     * 
     * SCOPO: Verifica booleana se il centro ha almeno un tecnico.
     *        Più efficiente di numero_tecnici > 0 perché usa exists().
     * 
     * PERFORMANCE: exists() è più veloce di count() per controlli booleani
     *              perché si ferma al primo record trovato.
     * 
     * UTILIZZO:
     * - if ($centro->hasTecnici()) { ... }
     * - Controlli condizionali nelle viste
     * - Validazioni business logic
     * 
     * @return bool - true se centro ha tecnici, false altrimenti
     */
    public function hasTecnici(): bool
    {
        return $this->tecnici()->exists();
    }

    /**
     * ==============================================
     * ACCESSOR TECNICI CON SPECIALIZZAZIONI
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent ORM
     * 
     * SCOPO: Ottiene lista ottimizzata dei tecnici con le loro specializzazioni.
     *        Carica solo i campi necessari per performance e ordina per nome.
     * 
     * OTTIMIZZAZIONE: select() carica solo campi necessari, non tutti i campi User.
     * 
     * UTILIZZO:
     * - $centro->tecnici_con_specializzazioni
     * - Pagine dettaglio centro con lista tecnici
     * - API endpoint per informazioni centro
     * 
     * @return \Illuminate\Database\Eloquent\Collection - Collection di tecnici ottimizzata
     */
    public function getTecniciConSpecializzazioniAttribute()
    {
        return $this->tecnici()
            ->select('id', 'nome', 'cognome', 'username', 'specializzazione', 'data_nascita', 'centro_assistenza_id')
            ->orderBy('nome')
            ->get();
    }

    /**
     * ==============================================
     * ACCESSOR TELEFONO FORMATTATO
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Regular Expressions
     * 
     * SCOPO: Formatta il numero di telefono in modo leggibile e standardizzato.
     *        Gestisce formati italiani standard per migliorare UX.
     * 
     * FORMATI GESTITI:
     * - 0712345678 -> 071 234 5678 (fisso italiano)
     * - +390712345678 -> +39 071 234 5678 (internazionale)
     * 
     * REGEX: preg_replace('/[^\d+]/', '', $telefono) rimuove tutto tranne cifre e +
     * 
     * ALGORITMO:
     * 1. Pulisce il numero da spazi e caratteri speciali
     * 2. Identifica formato (nazionale/internazionale)
     * 3. Applica formattazione appropriata
     * 4. Fallback al numero originale se non riconosciuto
     * 
     * @return string - Numero di telefono formattato per display
     */
    public function getTelefonoFormatatoAttribute(): string
    {
        if (!$this->telefono) {
            return '';
        }

        // Rimuove spazi e caratteri non numerici tranne il '+'
        $telefono = preg_replace('/[^\d+]/', '', $this->telefono);

        // FORMATO NAZIONALE ITALIANO (10 cifre)
        if (strlen($telefono) === 10 && !str_starts_with($telefono, '+')) {
            // Es: 0712345678 -> 071 234 5678
            return substr($telefono, 0, 3) . ' ' .
                   substr($telefono, 3, 3) . ' ' .
                   substr($telefono, 6);
        }

        // FORMATO INTERNAZIONALE ITALIANO (13 cifre con +39)
        if (str_starts_with($telefono, '+39') && strlen($telefono) === 13) {
            // Es: +390712345678 -> +39 071 234 5678
            return '+39 ' . substr($telefono, 3, 3) . ' ' .
                   substr($telefono, 6, 3) . ' ' .
                   substr($telefono, 9);
        }

        // FALLBACK: Ritorna numero originale se formato non riconosciuto
        return $this->telefono;
    }

    /**
     * ==============================================
     * METODO CONTROLLO STATO APERTURA
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Carbon (date manipulation)
     * 
     * SCOPO: Verifica se il centro è aperto in base a orari standard.
     *        Utile per mostrare stato "Aperto/Chiuso" nell'interfaccia.
     * 
     * ORARI STANDARD DEFINITI:
     * - Lunedì-Venerdì: 8:30-17:30
     * - Sabato: 8:30-12:30  
     * - Domenica: Chiuso
     * 
     * LOGICA:
     * - now()->dayOfWeek: 0=domenica, 1=lunedì, ..., 6=sabato
     * - now()->hour: ora corrente (0-23)
     * 
     * UTILIZZO:
     * - if ($centro->isAperto()) { echo "Aperto"; }
     * - Badge di stato nelle liste centri
     * - API per app mobile tecnici
     * 
     * @return bool - true se centro aperto ora, false se chiuso
     */
    public function isAperto(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = domenica, 1 = lunedì, etc.
        $hour = $now->hour;

        // ORARI FERIALI: Lunedì-Venerdì 8:30-17:30
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { 
            return $hour >= 8 && $hour < 18; // 8:00-17:59 per approssimazione
        } 
        // ORARI SABATO: 8:30-12:30
        elseif ($dayOfWeek === 6) { 
            return $hour >= 8 && $hour < 13; // 8:00-12:59 per approssimazione
        }

        // DOMENICA: Sempre chiuso
        return false;
    }

    /**
     * ==============================================
     * ACCESSOR ORARI APERTURA FORMATTATI
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Return Array strutturato
     * 
     * SCOPO: Fornisce informazioni complete sugli orari di apertura
     *        in formato strutturato per display nell'interfaccia.
     * 
     * STRUTTURA DATI: Array associativo con tutti gli orari e note.
     * 
     * UTILIZZO:
     * - $centro->orario_apertura['lunedi_venerdi']
     * - Tooltip informativi
     * - Pagine di contatto centri
     * 
     * @return array - Array strutturato con orari completi
     */
    public function getOrarioAperturaAttribute(): array
    {
        return [
            'lunedi_venerdi' => '8:30 - 17:30',
            'sabato' => '8:30 - 12:30',
            'domenica' => 'Chiuso',
            'note' => 'Orari potrebbero variare per festività'
        ];
    }

    /**
     * ==============================================
     * ACCESSOR LINK GOOGLE MAPS
     * ==============================================
     * 
     * LINGUAGGIO: PHP con URL encoding
     * 
     * SCOPO: Genera automaticamente link per aprire Google Maps
     *        con l'indirizzo del centro pre-compilato.
     * 
     * INTEGRAZIONE: Facilita integrazione con servizi mappe
     *               senza dover gestire manualmente gli URL.
     * 
     * URL ENCODING: urlencode() gestisce caratteri speciali nell'indirizzo
     *               per garantire URL validi.
     * 
     * UTILIZZO:
     * - <a href="{{ $centro->google_maps_link }}">Vedi su Maps</a>
     * - Integrazione app mobile
     * - Widget mappe nelle viste
     * 
     * @return string - URL completo per Google Maps
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        $indirizzo = urlencode($this->indirizzo_completo);
        return "https://www.google.com/maps/search/?api=1&query={$indirizzo}";
    }

    /**
     * ==============================================
     * ACCESSOR STATISTICHE CENTRO
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Laravel Collections + Statistical functions
     * 
     * SCOPO: Calcola statistiche complete del centro per dashboard
     *        e reporting amministrativo.
     * 
     * STATISTICHE CALCOLATE:
     * - Totale tecnici assegnati
     * - Distribuzione specializzazioni (conteggio per tipo)
     * - Età media tecnici (calcolata da date di nascita)
     * 
     * COLLECTIONS: Usa metodi Laravel Collection per elaborazione dati:
     * - pluck(): Estrae solo il campo specializzazione
     * - filter(): Rimuove valori null
     * - countBy(): Conta occorrenze per valore
     * - avg(): Calcola media con funzione personalizzata
     * 
     * UTILIZZO:
     * - $centro->statistiche['totale_tecnici']
     * - Dashboard amministratore
     * - Report Excel export
     * 
     * @return array - Array strutturato con tutte le statistiche
     */
    public function getStatisticheAttribute(): array
    {
        // Carica tutti i tecnici del centro
        $tecnici = $this->tecnici()->get();

        // Calcola distribuzione specializzazioni
        $specializzazioni = $tecnici->pluck('specializzazione')   // Estrae solo specializzazioni
            ->filter()                                            // Rimuove valori null/vuoti
            ->countBy()                                           // Conta per valore
            ->toArray();                                          // Converte a array

        return [
            'totale_tecnici' => $tecnici->count(),
            'specializzazioni' => $specializzazioni,
            
            // CALCOLO ETÀ MEDIA AVANZATO
            'eta_media_tecnici' => $tecnici->whereNotNull('data_nascita')  // Solo tecnici con data nascita
                ->avg(function($tecnico) {                                 // Media con calcolo personalizzato
                    return $tecnico->data_nascita ? 
                        now()->diffInYears($tecnico->data_nascita) : null; // Calcola età in anni
                })
        ];
    }

    // ================================================
    // METODI STATICI - UTILITIES A LIVELLO CLASSE
    // ================================================

    /**
     * ==============================================
     * METODO STATICO PROVINCE DISPONIBILI
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con Laravel Query Builder
     * 
     * SCOPO: Ottiene lista di tutte le province che hanno centri assistenza.
     *        Utile per popolare select di filtro o dropdown geografici.
     * 
     * QUERY: distinct() evita duplicati, orderBy() ordina alfabeticamente.
     *        pluck('provincia', 'provincia') crea array key-value identici.
     * 
     * OUTPUT: ['MI' => 'MI', 'RM' => 'RM', 'NA' => 'NA', ...]
     * 
     * UTILIZZO STATICO:
     * - CentroAssistenza::getProvinceDisponibili()
     * - Form di ricerca geografica
     * - Dropdown filtri amministrazione
     * 
     * @return array - Array province disponibili
     */
    public static function getProvinceDisponibili(): array
    {
        return self::distinct()
            ->orderBy('provincia')
            ->pluck('provincia', 'provincia')
            ->toArray();
    }

    /**
     * ==============================================
     * METODO STATICO CITTÀ PER PROVINCIA
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con parametrizzazione
     * 
     * SCOPO: Ottiene tutte le città disponibili per una provincia specifica.
     *        Implementa ricerca cascata: prima provincia, poi città.
     * 
     * UTILIZZO TIPICO: Select dinamico JavaScript dove selezione provincia
     *                  aggiorna automaticamente dropdown città.
     * 
     * ESEMPIO:
     * - CentroAssistenza::getCittaPerProvincia('MI')
     * - Ritorna: ['Milano' => 'Milano', 'Monza' => 'Monza', ...]
     * 
     * @param string $provincia - Sigla provincia per filtrare
     * @return array - Array città disponibili in quella provincia
     */
    public static function getCittaPerProvincia(string $provincia): array
    {
        return self::where('provincia', $provincia)
            ->distinct()
            ->orderBy('citta')
            ->pluck('citta', 'citta')
            ->toArray();
    }

    /**
     * ==============================================
     * METODO STATICO RICERCA CENTRO VICINO
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con Query Building condizionale
     * 
     * SCOPO: Trova il centro più appropriato basato su criteri geografici.
     *        Algoritmo di matching progressivo per trovare supporto più vicino.
     * 
     * LOGICA ALGORITMO:
     * 1. Filtra per provincia se specificata
     * 2. Filtra per città se specificata (ricerca parziale)
     * 3. Considera solo centri con tecnici disponibili
     * 4. Ordina geograficamente e prende il primo (più rilevante)
     * 
     * PARAMETRI OPZIONALI: Entrambi i parametri sono nullable per flessibilità:
     * - Solo provincia: trova primo centro nella provincia
     * - Solo città: cerca in tutte le province
     * - Entrambi: ricerca più specifica
     * - Nessuno: primo centro disponibile
     * 
     * BUSINESS LOGIC: Prima priorità ai centri operativi (con tecnici),
     *                 poi ordinamento geografico per risultati consistenti.
     * 
     * UTILIZZO:
     * - CentroAssistenza::findNearby('MI', 'Milano')
     * - Algoritmi di assignment automatico tecnici
     * - Sistema di routing richieste assistenza
     * 
     * @param string|null $provincia - Sigla provincia target (opzionale)
     * @param string|null $citta - Nome città target (opzionale, ricerca parziale)
     * @return CentroAssistenza|null - Centro trovato o null se nessun match
     */
    public static function findNearby(string $provincia = null, string $citta = null)
    {
        // Inizializza query base
        $query = self::query();

        // FILTRO CONDIZIONALE PROVINCIA
        if ($provincia) {
            $query->where('provincia', $provincia);
        }

        // FILTRO CONDIZIONALE CITTÀ (ricerca parziale)
        if ($citta) {
            $query->where('citta', 'LIKE', "%{$citta}%");
        }

        // APPLICA SCOPE COMBINATI E RITORNA PRIMO RISULTATO
        return $query->conTecnici()                    // Solo centri operativi
            ->ordinatoGeograficamente()                // Ordinamento standard
            ->first();                                 // Primo risultato o null
    }
}