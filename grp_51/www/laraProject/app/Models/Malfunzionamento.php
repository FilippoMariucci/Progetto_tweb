<?php

namespace App\Models;

// === IMPORTAZIONE DEPENDENCIES LARAVEL ===
use Illuminate\Database\Eloquent\Factories\HasFactory;  // [LARAVEL] - Trait per generazione dati test
use Illuminate\Database\Eloquent\Model;                  // [LARAVEL] - Classe base per modelli Eloquent ORM
use Illuminate\Database\Eloquent\Builder;                // [LARAVEL] - Query Builder per scope personalizzati

/**
 * ======================================================================
 * MODELLO ELOQUENT MALFUNZIONAMENTO - CORE BUSINESS LOGIC DEL SISTEMA
 * ======================================================================
 * 
 * LINGUAGGIO: PHP 8.x con Laravel Eloquent ORM
 * TABELLA DATABASE: malfunzionamenti
 * 
 * SCOPO: Modello centrale del sistema di assistenza tecnica che rappresenta
 *        i problemi/guasti riscontrati nei prodotti dopo la commercializzazione
 *        e le relative soluzioni tecniche per risolverli.
 * 
 * CONTESTO BUSINESS: Il cuore del sistema - ogni volta che un tecnico di un
 *                    centro assistenza incontra un problema, consulta questo
 *                    database per trovare soluzioni già testate e documentate.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * 1. Archiviazione descrizioni dettagliate dei malfunzionamenti
 * 2. Soluzioni step-by-step per la riparazione
 * 3. Classificazione per gravità (critica, alta, media, bassa)
 * 4. Livello di difficoltà per skill tecnico richiesto
 * 5. Tracking frequenza segnalazioni nel tempo
 * 6. Stima tempo riparazione e strumenti necessari
 * 7. Audit trail con utenti che creano/modificano soluzioni
 * 
 * RELAZIONI DATABASE:
 * - N:1 con Prodotto (ogni malfunzionamento appartiene a un prodotto)
 * - N:1 con User (utente che ha creato la soluzione)
 * - N:1 con User (utente che ha modificato per ultimo)
 * 
 * UTILIZZO TIPICO:
 * - Tecnico cerca malfunzionamento per categoria prodotto
 * - Staff aziendale aggiunge nuove soluzioni quando scoperte
 * - Amministratori monitorano trend e statistiche
 * 
 * DESIGN PATTERN: Active Record con Rich Domain Model
 */
class Malfunzionamento extends Model
{
    // === TRAIT E CONFIGURAZIONE BASE ===
    use HasFactory; // Abilita uso di Factory per seeding e testing

    /**
     * =======================================
     * CONFIGURAZIONE TABELLA DATABASE
     * =======================================
     * 
     * LINGUAGGIO: PHP - Proprietà protetta di configurazione Laravel
     * 
     * SCOPO: Specifica nome tabella custom dato che Laravel di default
     *        userebbe "malfunzionamentos" (plurale inglese), ma abbiamo
     *        una tabella con nome italiano personalizzato.
     */
    protected $table = 'malfunzionamenti';

    /**
     * =======================================
     * MASS ASSIGNMENT PROTECTION
     * =======================================
     * 
     * LINGUAGGIO: PHP Array - Configurazione sicurezza Laravel
     * 
     * SCOPO: Definisce campi che possono essere assegnati in massa tramite
     *        create() o fill(). Protegge da Mass Assignment Vulnerability.
     * 
     * SICUREZZA: Solo questi campi possono essere popolati da input utente.
     *            Altri campi (id, timestamps) sono protetti.
     * 
     * BUSINESS FIELDS EXPLAINED:
     * - prodotto_id: FK verso tabella prodotti (quale prodotto ha questo problema)
     * - titolo: Titolo breve del malfunzionamento (es: "Lavatrice non centrifuga")
     * - descrizione: Descrizione dettagliata del problema e sintomi
     * - gravita: Livello criticità (critica|alta|media|bassa)
     * - soluzione: Procedura step-by-step per risolvere il problema
     * - strumenti_necessari: Lista strumenti richiesti per riparazione
     * - tempo_stimato: Minuti stimati per completare riparazione
     * - difficolta: Skill level richiesto (facile|media|difficile|esperto)
     * - numero_segnalazioni: Contatore quante volte è stato segnalato
     * - prima_segnalazione: Data prima volta che è stato segnalato
     * - ultima_segnalazione: Data ultimo aggiornamento segnalazione
     * - creato_da: FK verso User che ha creato questa soluzione
     * - modificato_da: FK verso User che ha modificato per ultimo
     */
    protected $fillable = [
        'prodotto_id',              // Foreign Key verso prodotti
        'titolo',                   // Titolo breve malfunzionamento
        'descrizione',              // Descrizione dettagliata problema
        'gravita',                  // Livello gravità (enum-like)
        'soluzione',                // Procedura risoluzione step-by-step
        'strumenti_necessari',      // CSV strumenti richiesti
        'tempo_stimato',            // Minuti stima riparazione
        'difficolta',               // Livello difficoltà tecnica
        'numero_segnalazioni',      // Counter frequenza problema
        'prima_segnalazione',       // Data prima segnalazione
        'ultima_segnalazione',      // Data ultima segnalazione
        'creato_da',                // FK User creatore
        'modificato_da',            // FK User ultimo modificatore
    ];

    /**
     * =======================================
     * CAST AUTOMATICI TIPI DATO
     * =======================================
     * 
     * LINGUAGGIO: PHP - Metodo Laravel per type casting
     * 
     * SCOPO: Definisce conversioni automatiche tra tipi database e PHP.
     *        Laravel gestisce automaticamente la conversione bidirezionale.
     * 
     * CONVERSIONI SPECIFICHE:
     * - 'date': Converte DATE/DATETIME SQL in oggetti Carbon per PHP
     * - 'integer': Assicura che contatori siano sempre int, non string
     * 
     * VANTAGGI:
     * - $malfunzionamento->prima_segnalazione->format('d/m/Y') funziona automaticamente
     * - $malfunzionamento->numero_segnalazioni + 1 è sempre operazione matematica
     * - Nessuna conversione manuale necessaria nel codice business
     * 
     * @return array - Array associativo campo => tipo
     */
    protected function casts(): array
    {
        return [
            'prima_segnalazione' => 'date',      // Converte in Carbon date object
            'ultima_segnalazione' => 'date',     // Converte in Carbon date object
            'numero_segnalazioni' => 'integer',  // Assicura tipo int per contatori
            'tempo_stimato' => 'integer',        // Assicura tipo int per minuti
        ];
    }

    // ================================================
    // RELAZIONI ELOQUENT - OBJECT RELATIONAL MAPPING
    // ================================================

    /**
     * ==========================================
     * RELAZIONE N:1 - PRODOTTO ASSOCIATO
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent Relations
     * TIPO RELAZIONE: Many-to-One (belongsTo)
     * 
     * SCOPO: Ogni malfunzionamento appartiene a uno specifico prodotto.
     *        Relazione fondamentale per organizzare le soluzioni per tipologia.
     * 
     * BUSINESS LOGIC: Quando un tecnico cerca soluzioni, tipicamente filtra
     *                 prima per prodotto specifico o categoria di prodotti.
     * 
     * FOREIGN KEY: prodotto_id nella tabella malfunzionamenti
     * 
     * UTILIZZO:
     * - $malfunzionamento->prodotto: Ottiene istanza Prodotto associata
     * - $malfunzionamento->prodotto->nome: Nome del prodotto
     * - $malfunzionamento->prodotto->categoria: Categoria per raggruppamenti
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodotto()
    {
        return $this->belongsTo(Prodotto::class);
    }

    /**
     * ==========================================
     * RELAZIONE N:1 - UTENTE CREATORE
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent Relations
     * TIPO RELAZIONE: Many-to-One (belongsTo)
     * 
     * SCOPO: Traccia quale membro dello staff aziendale ha creato questa
     *        soluzione per audit trail e accountability.
     * 
     * BUSINESS LOGIC: Importante per:
     * - Quality assurance (chi ha documentato la soluzione)
     * - Tracking expertise (quali staff creano più soluzioni)
     * - Contatto per chiarimenti su soluzioni complesse
     * 
     * FOREIGN KEY: creato_da nella tabella malfunzionamenti
     * NAMING: creatoBy() invece di creatoDA() per convenzione inglese Laravel
     * 
     * UTILIZZO:
     * - $malfunzionamento->creatoBy: Ottiene User che ha creato
     * - $malfunzionamento->creatoBy->nome_completo: Nome dell'autore
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creatoBy()
    {
        return $this->belongsTo(User::class, 'creato_da');
    }

    /**
     * ==========================================
     * RELAZIONE N:1 - UTENTE ULTIMO MODIFICATORE
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent Relations
     * TIPO RELAZIONE: Many-to-One (belongsTo)
     * 
     * SCOPO: Traccia l'ultimo utente che ha modificato la soluzione
     *        per audit trail delle modifiche nel tempo.
     * 
     * BUSINESS LOGIC: Le soluzioni evolvono nel tempo quando:
     * - Si scoprono metodi più efficaci
     * - Cambiano strumenti disponibili
     * - Si aggiungono note da esperienze sul campo
     * 
     * FOREIGN KEY: modificato_da nella tabella malfunzionamenti
     * 
     * UTILIZZO:
     * - $malfunzionamento->modificatoBy: Ultimo User che ha modificato
     * - Per mostrare "Ultima modifica di X il Y" nell'interfaccia
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modificatoBy()
    {
        return $this->belongsTo(User::class, 'modificato_da');
    }

    // ================================================
    // QUERY SCOPES - METODI PER QUERY OTTIMIZZATE
    // ================================================

    /**
     * ==========================================
     * SCOPE FILTRO PER GRAVITÀ
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes
     * PATTERN: Local Scope con parametro
     * 
     * SCOPO: Filtra malfunzionamenti per livello di gravità specifico.
     *        Permette ricerche rapide per prioritizzare interventi.
     * 
     * VALORI GRAVITÀ: 'critica', 'alta', 'media', 'bassa'
     * 
     * UTILIZZO BUSINESS:
     * - Malfunzionamento::gravita('critica')->get() per emergenze
     * - Dashboard admin per monitorare problemi critici
     * - Filtri nell'interfaccia tecnici
     * 
     * ESEMPIO SQL GENERATO:
     * SELECT * FROM malfunzionamenti WHERE gravita = 'critica'
     * 
     * @param Builder $query - Query builder Laravel da modificare
     * @param string $gravita - Livello gravità da filtrare
     * @return void - Modifica direttamente la query passata
     */
    public function scopeGravita(Builder $query, string $gravita): void
    {
        $query->where('gravita', $gravita);
    }

    /**
     * ==========================================
     * SCOPE FILTRO PER DIFFICOLTÀ
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes
     * 
     * SCOPO: Filtra per livello di difficoltà tecnica richiesta.
     *        Utile per assegnare riparazioni basate su skill tecnico.
     * 
     * VALORI DIFFICOLTÀ: 'facile', 'media', 'difficile', 'esperto'
     * 
     * UTILIZZO BUSINESS:
     * - Tecnici junior vedono solo 'facile' e 'media'
     * - Tecnici esperti possono gestire tutto
     * - Sistema di routing automatico basato su competenze
     * 
     * @param Builder $query - Query builder da modificare
     * @param string $difficolta - Livello difficoltà da filtrare
     * @return void
     */
    public function scopeDifficolta(Builder $query, string $difficolta): void
    {
        $query->where('difficolta', $difficolta);
    }

    /**
     * ==========================================
     * SCOPE ORDINAMENTO PER GRAVITÀ
     * ==========================================
     * 
     * LINGUAGGIO: PHP con MySQL FIELD() function
     * 
     * SCOPO: Ordina malfunzionamenti per priorità logica di gravità,
     *        non alfabetica. I problemi critici appaiono sempre per primi.
     * 
     * ALGORITMO: Usa MySQL FIELD() per definire ordine custom:
     *            critica (1°) > alta (2°) > media (3°) > bassa (4°)
     * 
     * SQL GENERATO:
     * ORDER BY FIELD(gravita, 'critica', 'alta', 'media', 'bassa')
     * 
     * UTILIZZO BUSINESS:
     * - Liste malfunzionamenti prioritizzate per urgenza
     * - Dashboard tecnici con problemi critici in evidenza
     * - Report per management ordinati per impatto business
     * 
     * @param Builder $query - Query builder da ordinare
     * @return void
     */
    public function scopeOrdinatoPerGravita(Builder $query): void
    {
        $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')");
    }

    /**
     * ==========================================
     * SCOPE ORDINAMENTO PER FREQUENZA
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Builder orderBy
     * 
     * SCOPO: Ordina malfunzionamenti per numero di segnalazioni ricevute.
     *        I problemi più frequenti appaiono per primi.
     * 
     * BUSINESS LOGIC: Problemi segnalati spesso indicano:
     * - Difetti di progettazione/produzione
     * - Punti critici che necessitano attenzione prioritaria
     * - Opportunità di miglioramento prodotto
     * 
     * UTILIZZO:
     * - Report qualità per identificare pattern ricorrenti
     * - Prioritizzazione R&D per miglioramenti prodotto
     * - KPI per customer satisfaction
     * 
     * @param Builder $query - Query da ordinare
     * @return void
     */
    public function scopeOrdinatoPerFrequenza(Builder $query): void
    {
        $query->orderBy('numero_segnalazioni', 'desc');
    }

    /**
     * ==========================================
     * SCOPE MALFUNZIONAMENTI RECENTI
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Carbon date manipulation
     * 
     * SCOPO: Filtra malfunzionamenti creati negli ultimi N giorni.
     *        Utile per monitoring trend e problemi emergenti.
     * 
     * PARAMETRO FLESSIBILE: Default 30 giorni, personalizzabile
     * 
     * BUSINESS LOGIC:
     * - Monitoring nuovi problemi che emergono
     * - Alert per pattern anomali temporali
     * - Report periodici su trend qualità
     * 
     * UTILIZZO:
     * - Dashboard: "Nuovi problemi questo mese"
     * - Alert: "10 nuovi problemi critici nell'ultima settimana"
     * - Report: Analisi trend settimanali/mensili
     * 
     * @param Builder $query - Query da filtrare
     * @param int $giorni - Numero giorni indietro (default: 30)
     * @return void
     */
    public function scopeRecenti(Builder $query, int $giorni = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($giorni));
    }

    /**
     * ==========================================
     * SCOPE MALFUNZIONAMENTI CRITICI
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Scopes
     * 
     * SCOPO: Shortcut per ottenere solo malfunzionamenti di gravità critica.
     *        Metodo di convenienza per uso frequente.
     * 
     * DESIGN PATTERN: Convenience method che wrappa scope più generico
     *                 per migliorare leggibilità codice business.
     * 
     * EQUIVALENTE A: ->gravita('critica') ma più espressivo
     * 
     * UTILIZZO:
     * - Alert dashboard amministratori
     * - Notification urgenti a team tecnico
     * - Report executive per problemi ad alto impatto
     * 
     * @param Builder $query - Query da filtrare
     * @return void
     */
    public function scopeCritici(Builder $query): void
    {
        $query->where('gravita', 'critica');
    }

    /**
     * ==========================================
     * SCOPE MALFUNZIONAMENTI FREQUENTI
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Query Builder
     * 
     * SCOPO: Filtra malfunzionamenti con segnalazioni sopra una soglia.
     *        Identifica problemi sistemici ricorrenti.
     * 
     * SOGLIA DINAMICA: Default 10 segnalazioni, personalizzabile
     * 
     * BUSINESS INTELLIGENCE:
     * - Identifica difetti di design/produzione
     * - Prioritizza miglioramenti prodotto
     * - Calcola impatto customer satisfaction
     * 
     * UTILIZZO:
     * - Report qualità: "Top 10 problemi più frequenti"
     * - R&D: "Problemi che necessitano redesign"
     * - Management: "Issues ad alto impatto business"
     * 
     * @param Builder $query - Query da filtrare
     * @param int $soglia - Numero minimo segnalazioni (default: 10)
     * @return void
     */
    public function scopeFrequenti(Builder $query, int $soglia = 10): void
    {
        $query->where('numero_segnalazioni', '>=', $soglia);
    }

    /**
     * ==========================================
     * SCOPE RICERCA FULL-TEXT
     * ==========================================
     * 
     * LINGUAGGIO: PHP con MySQL FULLTEXT search
     * 
     * SCOPO: Ricerca avanzata nel titolo e descrizione dei malfunzionamenti
     *        usando indici full-text MySQL per performance ottimali.
     * 
     * PREREQUISITI: Tabella deve avere indice FULLTEXT su (titolo, descrizione)
     * 
     * TECNOLOGIA: MySQL MATCH...AGAINST in BOOLEAN MODE
     * - Supporta operatori booleani (+, -, *)
     * - Automatic stemming e relevance scoring
     * - Performance superiore a LIKE per testi lunghi
     * 
     * WILDCARD: Aggiunge automaticamente '*' per ricerca prefisso
     * 
     * UTILIZZO BUSINESS:
     * - Tecnici cercano: "centrifuga rumore" trova "Centrifuga fa rumore eccessivo"
     * - Ricerca intelligente sintomi correlati
     * - Knowledge base search per supporto
     * 
     * @param Builder $query - Query da filtrare
     * @param string $termine - Termine di ricerca
     * @return void
     */
    public function scopeRicerca(Builder $query, string $termine): void
    {
        $query->whereRaw(
            "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
            [$termine . '*']  // Aggiunge wildcard per ricerca prefisso
        );
    }

    /**
     * ==========================================
     * SCOPE RICERCA PER CATEGORIA PRODOTTO
     * ==========================================
     * 
     * LINGUAGGIO: PHP con Laravel Eloquent whereHas
     * 
     * SCOPO: Filtra malfunzionamenti per categoria di prodotto usando
     *        relazione con tabella prodotti. Ricerca cross-table ottimizzata.
     * 
     * MECCANISMO: whereHas() genera subquery EXISTS per performance.
     *             Più efficiente di join per questo tipo di filtro.
     * 
     * BUSINESS LOGIC: Tecnici specializzati in categoria specifica
     *                 (es: "lavatrici") vedono solo problemi rilevanti.
     * 
     * SQL GENERATO:
     * WHERE EXISTS (SELECT 1 FROM prodotti WHERE prodotti.id = malfunzionamenti.prodotto_id 
     *               AND prodotti.categoria = 'lavatrice')
     * 
     * UTILIZZO:
     * - Filtraggio expertise-based nell'interfaccia tecnici
     * - Report per categoria prodotto
     * - Assignment automatico basato su specializzazione
     * 
     * @param Builder $query - Query da filtrare
     * @param string $categoria - Categoria prodotto (es: 'lavatrice')
     * @return void
     */
    public function scopePerCategoriaProdotto(Builder $query, string $categoria): void
    {
        $query->whereHas('prodotto', function($q) use ($categoria) {
            $q->where('categoria', $categoria);
        });
    }

    // ================================================
    // ACCESSORS - ATTRIBUTI CALCOLATI PER UI/UX
    // ================================================

    /**
     * ==============================================
     * ACCESSOR ICONA CSS PER GRAVITÀ
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Pattern Matching (match expression PHP 8)
     * 
     * SCOPO: Genera automaticamente classe CSS per icona Bootstrap Icons
     *        basata sul livello di gravità del malfunzionamento.
     * 
     * PATTERN MATCH: Usa match() PHP 8 per mapping pulito e performante.
     *                Più efficiente e leggibile di switch/case.
     * 
     * ICON MAPPING:
     * - critica: Triangolo esclamativo rosso (pericolo immediato)
     * - alta: Triangolo esclamativo giallo (attenzione)
     * - media: Cerchio info blu (informativo)
     * - bassa: Check verde (facile)
     * - default: Punto interrogativo grigio (sconosciuto)
     * 
     * FRAMEWORK: Bootstrap Icons + Bootstrap colors
     * 
     * UTILIZZO:
     * - <i class="{{ $malfunzionamento->gravita_icon }}"></i> in Blade
     * - UI consistency automatica senza logica nelle viste
     * - Riconoscimento visuale immediato priorità
     * 
     * @return string - Classi CSS complete per icona
     */
    public function getGravitaIconAttribute(): string
    {
        return match($this->gravita) {
            'critica' => 'bi-exclamation-triangle-fill text-danger',   // Rosso: CRITICO
            'alta' => 'bi-exclamation-triangle text-warning',          // Giallo: ATTENZIONE  
            'media' => 'bi-info-circle text-info',                     // Blu: INFO
            'bassa' => 'bi-check-circle text-success',                 // Verde: OK
            default => 'bi-question-circle text-secondary'             // Grigio: UNKNOWN
        };
    }

    /**
     * ==============================================
     * ACCESSOR CLASSE CSS PER GRAVITÀ
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Pattern Matching
     * 
     * SCOPO: Genera classi CSS per colorazione e formattazione testo
     *        basata su livello gravità. Complementa l'icona.
     * 
     * STYLING LOGIC:
     * - critica: Rosso bold (massima urgenza visuale)
     * - alta: Giallo semi-bold (richiede attenzione)
     * - media: Blu normale (informativo)
     * - bassa: Verde normale (tutto ok)
     * 
     * DESIGN SYSTEM: Usa Bootstrap color utilities per consistency
     * 
     * UTILIZZO:
     * - <span class="{{ $malfunzionamento->gravita_class }}">CRITICA</span>
     * - Highlight automatico priorità nelle liste
     * - Consistent visual hierarchy
     * 
     * @return string - Classi CSS per formattazione testo
     */
    public function getGravitaClassAttribute(): string
    {
        return match($this->gravita) {
            'critica' => 'text-danger fw-bold',      // Rosso grassetto
            'alta' => 'text-warning fw-semibold',    // Giallo semi-grassetto
            'media' => 'text-info',                  // Blu normale
            'bassa' => 'text-success',               // Verde normale
            default => 'text-secondary'              // Grigio normale
        };
    }

    /**
     * ==============================================
     * ACCESSOR ICONA PER DIFFICOLTÀ
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Pattern Matching
     * 
     * SCOPO: Genera icone per rappresentare visualmente il livello
     *        di difficoltà tecnica richiesta per la riparazione.
     * 
     * ICON MAPPING basato su metaphor stelle/difficoltà:
     * - esperto: Stella piena rossa (skill massimo)
     * - difficile: Stella mezza gialla (skill alto)
     * - media: Stella vuota blu (skill medio)
     * - facile: Cerchio verde (skill base)
     * 
     * BUSINESS VALUE: Tecnici possono rapidamente valutare se hanno
     *                 le competenze necessarie per un intervento.
     * 
     * @return string - Classi CSS complete per icona difficoltà
     */
    public function getDifficoltaIconAttribute(): string
    {
        return match($this->difficolta) {
            'esperto' => 'bi-star-fill text-danger',      // Stella piena rossa
            'difficile' => 'bi-star-half text-warning',   // Stella mezza gialla
            'media' => 'bi-star text-info',               // Stella vuota blu
            'facile' => 'bi-circle text-success',         // Cerchio verde
            default => 'bi-question-circle text-secondary' // Unknown
        };
    }

    /**
     * ==============================================
     * ACCESSOR TEMPO FORMATTATO LEGGIBILE
     * ==============================================
     * 
     * LINGUAGGIO: PHP con manipolazione stringhe
     * 
     * SCOPO: Converte tempo stimato da minuti (storage) a formato
     *        leggibile per tecnici (ore e minuti).
     * 
     * ALGORITMO:
     * 1. Se null/0: "Non specificato"
     * 2. Se < 60 min: "X minuti"
     * 3. Se >= 60 min: "X ore e Y minuti" (con plurali corretti)
     * 
     * ESEMPI OUTPUT:
     * - 30 -> "30 minuti"
     * - 60 -> "1 ora"
     * - 90 -> "1 ora e 30 minuti"
     * - 150 -> "2 ore e 30 minuti"
     * 
     * UX VALUE: Tecnici stimano rapidamente durata intervento
     *           per pianificare giornata lavorativa.
     * 
     * @return string - Tempo formattato human-readable
     */
    public function getTempoFormatatoAttribute(): string
    {
        if (!$this->tempo_stimato) {
            return 'Non specificato';
        }

        $minuti = $this->tempo_stimato;
        
        // CASO: Solo minuti (< 1 ora)
        if ($minuti < 60) {
            return $minuti . ' minuti';
        }
        
        // CASO: Ore + minuti
        $ore = floor($minuti / 60);                    // Ore intere
        $minutiRestanti = $minuti % 60;                // Minuti residui
        
        // Costruisce stringa con plurale corretto
        $result = $ore . ' ora' . ($ore > 1 ? 'e' : '');  // "1 ora" vs "2 ore"
        
        // Aggiunge minuti se presenti
        if ($minutiRestanti > 0) {
            $result .= ' e ' . $minutiRestanti . ' minuti';
        }
        
        return $result;
    }

    /**
     * ==============================================
     * ACCESSOR PRIORITÀ NUMERICA PER ORDINAMENTO
     * ==============================================
     * 
     * LINGUAGGIO: PHP con algoritmo di scoring
     * 
     * SCOPO: Calcola punteggio numerico che combina gravità e frequenza
     *        per ordinamento intelligente dei malfunzionamenti.
     * 3. Score Totale: gravità_punti + frequenza_punti
     * 
     * ESEMPI:
     * - Critica con 20 segnalazioni: 100 + 40 = 140 punti
     * - Media con 30 segnalazioni: 50 + 50 = 100 punti
     * - Alta con 5 segnalazioni: 75 + 10 = 85 punti
     * 
     * UTILIZZO: Ordinamento automatico dashboard per priorità combinata
     *           gravità + impatto frequenza.
     * 
     * @return int - Punteggio priorità da 0 a 150
     */
    public function getPrioritaNumericaAttribute(): int
    {
        // CALCOLO PUNTI BASE GRAVITÀ
        $gravitaPunti = match($this->gravita) {
            'critica' => 100,
            'alta' => 75,
            'media' => 50,
            'bassa' => 25,
            default => 0
        };
        
        // CALCOLO BONUS FREQUENZA (cap a 50 punti)
        $frequenzaPunti = min($this->numero_segnalazioni * 2, 50);
        
        return $gravitaPunti + $frequenzaPunti;
    }

    // ================================================
    // METODI HELPER BUSINESS LOGIC
    // ================================================

    /**
     * ==============================================
     * METODO VERIFICA URGENZA
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Metodo helper booleano
     * 
     * SCOPO: Determina se un malfunzionamento richiede intervento urgente
     *        basato su regole business combinate.
     * 
     * LOGICA URGENZA:
     * 1. Qualsiasi problema CRITICO è sempre urgente
     * 2. Problemi ALTI con >= 10 segnalazioni sono urgenti
     * 
     * BUSINESS VALUE:
     * - Alert automatici per problemi che necessitano attenzione immediata
     * - Escalation automatica a manager per issue critiche
     * - SLA tracking per risposta a problemi urgenti
     * 
     * UTILIZZO:
     * - if ($malfunzionamento->isUrgente()) { sendAlert(); }
     * - Dashboard warnings per admin
     * - Email notifications automatiche
     * 
     * @return bool - true se richiede intervento urgente
     */
    public function isUrgente(): bool
    {
        return $this->gravita === 'critica' || 
               ($this->gravita === 'alta' && $this->numero_segnalazioni >= 10);
    }

    /**
     * ==============================================
     * METODO VERIFICA COMPETENZE SPECIALISTICHE
     * ==============================================
     * 
     * LINGUAGGIO: PHP - Metodo helper booleano
     * 
     * SCOPO: Determina se la riparazione richiede tecnico esperto
     *        per assignment intelligente delle riparazioni.
     * 
     * LOGICA EXPERTISE:
     * 1. Difficoltà ESPERTO sempre richiede specialista
     * 2. Difficoltà DIFFICILE + tempo > 90 min richiede specialista
     * 
     * BUSINESS VALUE:
     * - Routing automatico riparazioni a tecnici qualificati
     * - Prevenzione errori da assignment scorretto
     * - Ottimizzazione tempi riparazione
     * 
     * UTILIZZO:
     * - Sistema assignment automatico task
     * - Filtri interface per tecnici junior/senior
     * - Calcolo costi intervento (esperto costa di più)
     * 
     * @return bool - true se serve tecnico esperto
     */
    public function richiedeEsperto(): bool
    {
        return $this->difficolta === 'esperto' || 
               ($this->difficolta === 'difficile' && $this->tempo_stimato > 90);
    }

    /**
     * ==============================================
     * ACCESSOR STATISTICHE FREQUENZA
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Carbon date calculations
     * 
     * SCOPO: Calcola metriche avanzate sulla frequenza di segnalazione
     *        per analisi trend e pattern temporali.
     * 
     * METRICHE CALCOLATE:
     * - Totale segnalazioni (counter assoluto)
     * - Giorni di attività (prima -> ultima segnalazione)
     * - Frequenza media giornaliera (segnalazioni/giorni)
     * - Trend temporale (crescente/stabile/decrescente)
     * 
     * ALGORITMO FREQUENZA:
     * - Se 0 giorni tra prima e ultima: frequenza = 0
     * - Altrimenti: frequenza = totale_segnalazioni / giorni_attivi
     * 
     * BUSINESS INTELLIGENCE:
     * - Identifica problemi in accelerazione
     * - Calcola impact velocity per prioritizzazione
     * - Predice carico futuro supporto tecnico
     * 
     * @return array - Array strutturato con tutte le metriche
     */
    public function getStatisticheFrequenzaAttribute(): array
    {
        $giorniTraPrimaeUltima = $this->prima_segnalazione->diffInDays($this->ultima_segnalazione);
        
        $frequenzaMedia = $giorniTraPrimaeUltima > 0 ? 
            round($this->numero_segnalazioni / $giorniTraPrimaeUltima, 2) : 0;
        
        return [
            'totale_segnalazioni' => $this->numero_segnalazioni,
            'giorni_attivo' => $giorniTraPrimaeUltima,
            'frequenza_giornaliera' => $frequenzaMedia,
            'trend' => $this->calcolaTrend()
        ];
    }

    /**
     * ==============================================
     * METODO PRIVATO CALCOLO TREND
     * ==============================================
     * 
     * LINGUAGGIO: PHP private method con Carbon
     * 
     * SCOPO: Analizza trend temporale delle segnalazioni per classificare
     *        se il problema è in crescita, stabile o in diminuzione.
     * 
     * ALGORITMO TREND:
     * - CRESCENTE: ultima segnalazione nell'ultimo mese
     * - STABILE: ultima segnalazione 1-3 mesi fa
     * - DECRESCENTE: ultima segnalazione > 3 mesi fa
     * 
     * BUSINESS VALUE:
     * - Identifica problemi emergenti (crescenti)
     * - Classifica problemi risolti (decrescenti)
     * - Monitoring effectiveness delle soluzioni implementate
     * 
     * @return string - 'crescente'|'stabile'|'decrescente'
     */
    private function calcolaTrend(): string
    {
        $ultimoMese = now()->subMonth();
        
        if ($this->ultima_segnalazione->isAfter($ultimoMese)) {
            return 'crescente';    // Attivo nell'ultimo mese
        } elseif ($this->ultima_segnalazione->isAfter(now()->subMonths(3))) {
            return 'stabile';      // Attivo 1-3 mesi fa
        } else {
            return 'decrescente';  // Inattivo da > 3 mesi
        }
    }

    /**
     * ==============================================
     * ACCESSOR PASSAGGI SOLUZIONE STRUTTURATI
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Regular Expressions
     * 
     * SCOPO: Converte testo soluzione libero in array di passaggi
     *        strutturati per migliore UX nell'interfaccia tecnici.
     * 
     * PARSING LOGIC:
     * - Divide per numeri "1. 2. 3." o bullet "- *"
     * - Rimuove elementi vuoti e whitespace
     * - Ritorna array pulito di step discreti
     * 
     * ESEMPI INPUT/OUTPUT:
     * INPUT: "1. Spegnere dispositivo\n2. Rimuovere coperchio\n3. Controllare filtro"
     * OUTPUT: ["Spegnere dispositivo", "Rimuovere coperchio", "Controllare filtro"]
     * 
     * UX VALUE: Interface può renderizzare checklist interattiva
     *           invece di blocco testo monolitico.
     * 
     * @return array - Array di passaggi individuali
     */
    public function getPassaggiSoluzioneAttribute(): array
    {
        if (!$this->soluzione) {
            return [];
        }
        
        // REGEX: Split su numeri o bullet points
        $passaggi = preg_split('/\n\s*\d+\.\s*|\n\s*[-*]\s*/', $this->soluzione);
        
        // Pulisce e filtra elementi vuoti
        return array_filter(array_map('trim', $passaggi));
    }

    /**
     * ==============================================
     * ACCESSOR STRUMENTI COME ARRAY
     * ==============================================
     * 
     * LINGUAGGIO: PHP con string manipulation
     * 
     * SCOPO: Converte campo strumenti_necessari da CSV string
     *        ad array per processing programmatico.
     * 
     * PARSING:
     * - Split per virgole
     * - Trim whitespace da ogni elemento
     * - Filtra elementi vuoti
     * 
     * ESEMPI:
     * INPUT: "cacciavite, chiave inglese, multimetro"
     * OUTPUT: ["cacciavite", "chiave inglese", "multimetro"]
     * 
     * UTILIZZO:
     * - Checklist strumenti prima intervento
     * - Inventory check automatico
     * - Costing automatico basato su strumenti richiesti
     * 
     * @return array - Array strumenti individuali
     */
    public function getStrumentiArrayAttribute(): array
    {
        if (!$this->strumenti_necessari) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $this->strumenti_necessari)));
    }

    /**
     * ==============================================
     * ACCESSOR MALFUNZIONAMENTI CORRELATI
     * ==============================================
     * 
     * LINGUAGGIO: PHP con Eloquent ORM complex query
     * 
     * SCOPO: Trova malfunzionamenti simili per suggerimenti intelligenti
     *        e knowledge discovery correlata.
     * 
     * ALGORITMO CORRELAZIONE:
     * 1. Stesso prodotto specifico (massima correlazione)
     * 2. Stessa categoria prodotto (correlazione media)
     * 3. Stessa gravità (problemi simili)
     * 4. Ordinato per frequenza (più rilevanti primi)
     * 5. Limitato a 5 risultati (UX focused)
     * 
     * BUSINESS VALUE:
     * - Suggerimenti "Vedi anche" per tecnici
     * - Pattern discovery problemi correlati
     * - Knowledge transfer tra prodotti simili
     * 
     * QUERY LOGIC: OR condition per prodotto specifico/categoria
     *              AND condition per gravità matching
     * 
     * @return \Illuminate\Database\Eloquent\Collection - Collezione correlati
     */
    public function getCorrelatiAttribute()
    {
        return self::where('id', '!=', $this->id)                    // Esclude se stesso
            ->where(function($query) {                               // GROUP OR conditions
                $query->where('prodotto_id', $this->prodotto_id)     // Stesso prodotto
                      ->orWhereHas('prodotto', function($q) {        // O stessa categoria
                          $q->where('categoria', $this->prodotto->categoria);
                      });
            })
            ->where('gravita', $this->gravita)                      // Stessa gravità
            ->orderBy('numero_segnalazioni', 'desc')                // Più frequenti primi
            ->limit(5)                                               // Top 5 per UX
            ->get();
    }

    // ================================================
    // METODI STATICI - BUSINESS INTELLIGENCE
    // ================================================

    /**
     * ==============================================
     * METODO STATICO STATISTICHE GENERALI
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con Eloquent aggregates
     * 
     * SCOPO: Calcola KPI complessivi del sistema per dashboard
     *        executive e monitoring generale.
     * 
     * METRICHE CALCOLATE:
     * - Totale malfunzionamenti nel sistema
     * - Distribuzione per gravità (pie chart ready)
     * - Distribuzione per difficoltà (skill analysis)
     * - Media segnalazioni (indicator problemi sistemici)
     * - Tempo medio risoluzione (efficiency metric)
     * - Nuovi problemi ultimo mese (trend indicator)
     * 
     * AGGREGATION QUERIES:
     * - COUNT per totali
     * - GROUP BY per distribuzioni
     * - AVG per medie
     * - Date filtering per trend temporali
     * 
     * UTILIZZO:
     * - Dashboard amministratori executive
     * - Report mensili qualità
     * - KPI monitoring alerts
     * 
     * @return array - Array strutturato con tutti i KPI
     */
    public static function getStatisticheGenerali(): array
    {
        return [
            'totale' => self::count(),
            
            // DISTRIBUZIONE GRAVITÀ per pie charts
            'per_gravita' => self::selectRaw('gravita, COUNT(*) as count')
                ->groupBy('gravita')
                ->pluck('count', 'gravita'),
                
            // DISTRIBUZIONE DIFFICOLTÀ per skill analysis
            'per_difficolta' => self::selectRaw('difficolta, COUNT(*) as count')
                ->groupBy('difficolta')
                ->pluck('count', 'difficolta'),
                
            // KPI PERFORMANCE
            'media_segnalazioni' => self::avg('numero_segnalazioni'),
            'tempo_medio_risoluzione' => self::avg('tempo_stimato'),
            
            // TREND INDICATOR
            'creati_ultimo_mese' => self::where('created_at', '>=', now()->subMonth())->count()
        ];
    }

    /**
     * ==============================================
     * METODO STATICO MALFUNZIONAMENTI CRITICI
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con Eager Loading
     * 
     * SCOPO: Ottiene lista prioritizzata dei problemi più critici
     *        per alert management e interventi urgenti.
     * 
     * QUERY OPTIMIZATION:
     * - Usa scope critici() per filtro gravità
     * - Eager load prodotto e creatore (N+1 prevention)
     * - Ordina per frequenza (impact priority)
     * - Limita risultati per performance
     * 
     * BUSINESS USAGE:
     * - Dashboard "Red Alert" per emergenze
     * - Email notifications automatiche
     * - Report escalation per management
     * 
     * @param int $limit - Numero massimo risultati (default: 10)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCritici(int $limit = 10)
    {
        return self::critici()                                      // Solo gravità critica
            ->with(['prodotto', 'creatoBy'])                        // Eager load relazioni
            ->ordinatoPerFrequenza()                                // Più frequenti primi
            ->limit($limit)                                         // Performance limit
            ->get();
    }

    /**
     * ==============================================
     * METODO STATICO MALFUNZIONAMENTI FREQUENTI
     * ==============================================
     * 
     * LINGUAGGIO: PHP Static Method con Eloquent ORM
     * 
     * SCOPO: Identifica problemi più ricorrenti nel sistema per
     *        analisi qualità e prioritizzazione miglioramenti.
     * 
     * BUSINESS INTELLIGENCE:
     * - Top problemi per impact assessment
     * - Candidate per redesign prodotto
     * - ROI analysis per fix development
     * 
     * QUERY STRATEGY:
     * - Ordina per numero_segnalazioni DESC
     * - Include info prodotto per context
     * - Limita per usabilità report
     * 
     * @param int $limit - Numero massimo risultati (default: 10)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPiuFrequenti(int $limit = 10)
    {
        return self::ordinatoPerFrequenza()                         // Più segnalati primi
            ->with(['prodotto'])                                    // Context prodotto
            ->limit($limit)                                         // Top N only
            ->get();
    }
}