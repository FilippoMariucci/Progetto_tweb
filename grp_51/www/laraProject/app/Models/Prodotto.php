<?php

/*
 * LINGUAGGIO: PHP 8.x con Laravel Framework 12
 * TIPO FILE: Modello Eloquent (ORM di Laravel)
 * DESCRIZIONE: Modello per la gestione dei prodotti nel sistema di assistenza tecnica
 * 
 * Questo modello rappresenta la tabella 'prodotti' nel database e gestisce:
 * - Le relazioni con malfunzionamenti e utenti staff
 * - La ricerca testuale con supporto wildcard
 * - La gestione delle categorie di prodotti
 * - L'upload e gestione delle immagini
 * - Le statistiche sui malfunzionamenti associati
 */

namespace App\Models;

// IMPORT delle classi Laravel necessarie per il funzionamento del modello
use Illuminate\Database\Eloquent\Factories\HasFactory;     // Per il seeding/testing
use Illuminate\Database\Eloquent\Model;                    // Classe base per tutti i modelli Eloquent
use Illuminate\Database\Eloquent\Builder;                  // Per costruire query personalizzate
use Illuminate\Database\Eloquent\Relations\HasMany;       // Relazione 1-a-molti
use Illuminate\Database\Eloquent\Relations\BelongsTo;     // Relazione molti-a-1

/**
 * CLASSE PRINCIPALE DEL MODELLO PRODOTTO
 * 
 * Estende Model di Laravel per utilizzare l'ORM Eloquent
 * Rappresenta un prodotto nel sistema di assistenza tecnica
 */
class Prodotto extends Model
{
    // TRAIT: Abilita l'uso di factory per il seeding e testing
    use HasFactory;

    /**
     * CONFIGURAZIONE TABELLA DATABASE
     * 
     * $table: specifica il nome della tabella nel database
     * Laravel di default usa il plurale del nome classe (prodottos), 
     * qui forziamo l'uso di 'prodotti' (plurale italiano)
     */
    protected $table = 'prodotti';

    /**
     * CONFIGURAZIONE MASS ASSIGNMENT
     * 
     * $fillable: array dei campi che possono essere assegnati in massa
     * Protezione di sicurezza di Laravel per evitare mass assignment attacks
     * Solo questi campi possono essere valorizzati con create() o fill()
     */
    protected $fillable = [
        'nome',                    // Nome del prodotto (es: "Lavatrice")
        'modello',                 // Modello specifico (es: "WM2100")
        'descrizione',             // Descrizione dettagliata del prodotto
        'categoria',               // Categoria di appartenenza (lavatrice, frigorifero, etc)
        'note_tecniche',           // Note tecniche per i tecnici
        'modalita_installazione',  // Istruzioni di installazione
        'modalita_uso',           // Istruzioni d'uso
        'prezzo',                 // Prezzo del prodotto
        'foto',                   // Path dell'immagine del prodotto
        'staff_assegnato_id',     // ID dello staff assegnato (funzionalità opzionale)
        'attivo'                  // Flag booleano per attivazione/disattivazione
    ];

    /**
     * CONFIGURAZIONE CAST AUTOMATICI
     * 
     * $casts: definisce come Laravel deve convertire i dati dal database
     * Automatizza la conversione dei tipi quando si legge/scrive dal DB
     */
    protected $casts = [
        'prezzo' => 'decimal:2',        // Converte in decimal con 2 cifre decimali
        'attivo' => 'boolean',          // Converte 0/1 in true/false
        'created_at' => 'datetime',     // Converte in oggetto Carbon (DateTime esteso)
        'updated_at' => 'datetime'      // Converte in oggetto Carbon (DateTime esteso)
    ];

    // ================================================
    // SEZIONE RELAZIONI ELOQUENT
    // ================================================

    /**
     * RELAZIONE ELOQUENT: Un prodotto ha molti malfunzionamenti
     * 
     * TIPO: One-to-Many (1:N)
     * LINGUAGGIO: Eloquent ORM (parte di Laravel)
     * 
     * @return HasMany Relazione con il modello Malfunzionamento
     * 
     * SPIEGAZIONE:
     * - hasMany() crea una relazione 1-a-molti
     * - Un prodotto può avere 0 o più malfunzionamenti
     * - La foreign key è 'prodotto_id' nella tabella malfunzionamenti
     * - Permette di accedere ai malfunzionamenti con $prodotto->malfunzionamenti
     */
    public function malfunzionamenti(): HasMany
    {
        return $this->hasMany(Malfunzionamento::class, 'prodotto_id');
    }

    /**
     * RELAZIONE ELOQUENT: Un prodotto appartiene a un membro dello staff
     * 
     * TIPO: Many-to-One (N:1) - Relazione opzionale
     * LINGUAGGIO: Eloquent ORM
     * 
     * @return BelongsTo Relazione con il modello User (staff)
     * 
     * SPIEGAZIONE:
     * - belongsTo() crea una relazione molti-a-1
     * - Implementa la funzionalità opzionale di assegnazione prodotti al staff
     * - La foreign key è 'staff_assegnato_id' in questa tabella
     * - Se NULL, il prodotto non è assegnato a nessuno
     */
    public function staffAssegnato(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_assegnato_id');
    }

    // ================================================
    // SEZIONE ACCESSORS (ATTRIBUTI CALCOLATI)
    // ================================================

    /**
     * ACCESSOR: URL completo dell'immagine del prodotto
     * 
     * LINGUAGGIO: PHP + Laravel Accessor Pattern
     * TIPO: Attributo virtuale (non esiste nel database)
     * 
     * @return string URL completo dell'immagine o placeholder
     * 
     * SPIEGAZIONE:
     * - Gli accessor in Laravel si definiscono con getNomeAttribute()
     * - Si accede come $prodotto->foto_url (snake_case)
     * - Gestisce diversi scenari di storage per compatibilità
     * - Se non c'è immagine, restituisce un placeholder
     * - asset() genera URL pubblici relativi alla document root
     */
    public function getFotoUrlAttribute(): string
    {
        // Se non c'è immagine salvata, usa placeholder
        if (!$this->foto) {
            return asset('images/prodotto-placeholder.jpg');
        }

        // TENTATIVO 1: Path standard Laravel Storage
        // storage/ è il link simbolico verso storage/app/public
        $standardPath = asset('storage/' . $this->foto);
        
        // TENTATIVO 2: Path diretto alla cartella storage
        // Per casi in cui il symlink non funziona
        $directPath = asset('storage/app/public/' . $this->foto);
        
        // TENTATIVO 3: Path verso uploads pubblici
        // Approccio alternativo con cartella uploads
        $uploadsPath = asset('uploads/prodotti/' . basename($this->foto));
        
        // TENTATIVO 4: URL completo con dominio
        // url() genera URL assoluti invece di relativi
        $fullUrl = url('storage/' . $this->foto);

        // DEBUGGING: Logga tutti i path possibili in modalità development
        if (config('app.debug')) {
            \Log::debug('Tentativi path immagine', [
                'foto_field' => $this->foto,
                'standard_path' => $standardPath,
                'direct_path' => $directPath,
                'uploads_path' => $uploadsPath,
                'full_url' => $fullUrl
            ]);
        }

        // Restituisce il path standard (il più comune)
        return $standardPath;
    }

    /**
     * ACCESSOR ALTERNATIVO: URL immagine con verifica esistenza file
     * 
     * LINGUAGGIO: PHP + Laravel + File System
     * 
     * @return string URL dell'immagine verificando che il file esista
     * 
     * SPIEGAZIONE:
     * - Prova diversi path e verifica l'esistenza fisica del file
     * - public_path() restituisce il path assoluto alla cartella public
     * - file_exists() verifica se il file esiste fisicamente
     * - Più lento ma più affidabile del primo accessor
     */
    public function getFotoUrlAlternativeAttribute(): string
    {
        if (!$this->foto) {
            return asset('images/no-image.png');
        }

        // Array di possibili percorsi dove cercare l'immagine
        $paths = [
            'storage/' . $this->foto,                           // Storage standard
            'storage/app/public/' . $this->foto,                // Storage diretto
            'uploads/prodotti/' . basename($this->foto),        // Uploads custom
            'images/prodotti/' . basename($this->foto)          // Images custom
        ];

        // Itera sui path e restituisce il primo file esistente
        foreach ($paths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                return asset($path);
            }
        }

        // Se nessun file trovato, placeholder di fallback
        return asset('images/no-image.png');
    }

    /**
     * ACCESSOR: Nome completo del prodotto
     * 
     * LINGUAGGIO: PHP + Laravel
     * 
     * @return string Concatenazione di nome e modello
     * 
     * SPIEGAZIONE:
     * - Combina nome e modello per un identificativo completo
     * - Es: "Lavatrice" + "WM2100" = "Lavatrice WM2100"
     * - Utile per dropdown e visualizzazioni compatte
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->nome . ' ' . $this->modello;
    }

    /**
     * ACCESSOR: Etichetta formattata della categoria
     * 
     * LINGUAGGIO: PHP + Laravel
     * 
     * @return string Nome user-friendly della categoria
     * 
     * SPIEGAZIONE:
     * - Converte la categoria dal formato database a formato leggibile
     * - Usa il sistema unificato delle categorie
     * - Es: "lavatrice" -> "Lavatrici"
     * - Se categoria non mappata, formatta automaticamente (underscore -> spazi, prima lettera maiuscola)
     */
    public function getCategoriaLabelAttribute(): string
    {
        return self::getCategorieUnifico()[$this->categoria] ?? ucfirst(str_replace('_', ' ', $this->categoria));
    }

    /**
     * METODO: Prezzo formattato in Euro
     * 
     * LINGUAGGIO: PHP
     * 
     * @return string Prezzo formattato per visualizzazione
     * 
     * SPIEGAZIONE:
     * - Formatta il prezzo numerico in stringa leggibile
     * - number_format(valore, decimali, sep_decimali, sep_migliaia)
     * - Gestisce il caso di prezzo mancante
     * - Es: 1234.56 -> "€ 1.234,56"
     */
    public function getPrezzoFormattato(): string
    {
        if (!$this->prezzo) {
            return 'Prezzo non disponibile';
        }

        return '€ ' . number_format($this->prezzo, 2, ',', '.');
    }

    /**
     * ACCESSOR: Conteggio totale malfunzionamenti
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return int Numero di malfunzionamenti associati
     * 
     * SPIEGAZIONE:
     * - relationLoaded() verifica se la relazione è già stata caricata in memoria
     * - Se già caricata, conta gli elementi in memoria (più veloce)
     * - Se non caricata, esegue query di conteggio sul database
     * - Ottimizzazione per evitare query duplicate
     */
    public function getTotaleMalfunzionamentiAttribute(): int
    {
        // Controllo ottimizzazione: usa dati in memoria se disponibili
        if ($this->relationLoaded('malfunzionamenti')) {
            return $this->malfunzionamenti->count();
        }
        
        // Altrimenti esegui COUNT(*) sul database
        return $this->malfunzionamenti()->count();
    }

    /**
     * ACCESSOR: Somma totale segnalazioni
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return int Somma di tutte le segnalazioni dei malfunzionamenti
     * 
     * SPIEGAZIONE:
     * - sum('campo') esegue una query SQL SUM() sul database
     * - Somma il campo 'numero_segnalazioni' di tutti i malfunzionamenti
     * - ?? 0 è null coalescing: se sum() restituisce null, usa 0
     * - Utile per statistiche generali del prodotto
     */
    public function getTotaleSegnalazioniAttribute(): int
    {
        return $this->malfunzionamenti()->sum('numero_segnalazioni') ?? 0;
    }

    /**
     * ACCESSOR: Malfunzionamenti ordinati per priorità
     * 
     * LINGUAGGIO: PHP + Eloquent ORM + SQL
     * 
     * @return Collection Malfunzionamenti ordinati per gravità e frequenza
     * 
     * SPIEGAZIONE:
     * - orderByRaw() permette di usare SQL raw per ordinamenti complessi
     * - FIELD() di MySQL ordina secondo un ordine specifico di valori
     * - Prima ordina per gravità (critica, alta, media, bassa)
     * - Poi ordina per numero di segnalazioni (decrescente)
     * - get() esegue la query e restituisce una Collection Laravel
     */
    public function getMalfunzionamentiOrdinatiAttribute()
    {
        return $this->malfunzionamenti()
            ->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
            ->orderBy('numero_segnalazioni', 'desc')
            ->get();
    }

    /**
     * ACCESSOR: Conteggio malfunzionamenti critici
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return int Numero di malfunzionamenti con gravità critica
     * 
     * SPIEGAZIONE:
     * - where('campo', 'valore') aggiunge condizione WHERE alla query
     * - count() esegue un COUNT(*) con le condizioni specificate
     * - Utile per dashboard e badge di allarme
     */
    public function getMalfunzionamentiCriticiCountAttribute(): int
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->count();
    }

    /**
     * ACCESSOR: Conteggio malfunzionamenti di alta gravità
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return int Numero di malfunzionamenti con gravità alta
     */
    public function getMalfunzionamentiAltaCountAttribute(): int
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'alta')
            ->count();
    }

    /**
     * ACCESSOR: Alias per categoria formattata
     * 
     * LINGUAGGIO: PHP + Laravel
     * 
     * @return string Categoria formattata (alias di categoria_label)
     * 
     * SPIEGAZIONE:
     * - Accessor che richiama altro accessor
     * - Fornisce nomenclatura alternativa per lo stesso dato
     * - Utile per compatibilità con vecchio codice
     */
    public function getCategoriaFormattataAttribute(): string
    {
        return $this->categoria_label;
    }

    // ================================================
    // SEZIONE METODI HELPER PER MALFUNZIONAMENTI
    // ================================================

    /**
     * METODO HELPER: Verifica presenza malfunzionamenti critici
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return bool True se esistono malfunzionamenti critici
     * 
     * SPIEGAZIONE:
     * - exists() verifica se esiste almeno un record che soddisfa le condizioni
     * - Più efficiente di count() > 0 perché si ferma al primo match
     * - Utile per badge di allarme e priorità nelle viste
     */
    public function hasMalfunzionamentiCritici(): bool
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->exists();
    }

    /**
     * METODO HELPER: Verifica presenza malfunzionamenti di alta gravità
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return bool True se esistono malfunzionamenti di alta gravità
     */
    public function hasMalfunzionamentiAlta(): bool
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'alta')
            ->exists();
    }

    /**
     * METODO HELPER: Ottieni top 5 malfunzionamenti critici
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return Collection I 5 malfunzionamenti critici più segnalati
     * 
     * SPIEGAZIONE:
     * - Filtra per gravità critica
     * - Ordina per numero segnalazioni (decrescente = più segnalati primi)
     * - limit(5) limita i risultati ai primi 5
     * - Utile per dashboard riepilogative
     */
    public function getMalfunzionamentiCritici()
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * METODO HELPER: Ottieni top 5 malfunzionamenti più frequenti
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return Collection I 5 malfunzionamenti più segnalati (qualsiasi gravità)
     */
    public function getMalfunzionamentiFrequenti()
    {
        return $this->malfunzionamenti()
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * METODO HELPER: Verifica se prodotto è assegnato a staff
     * 
     * LINGUAGGIO: PHP
     * 
     * @return bool True se il prodotto è assegnato a qualcuno
     * 
     * SPIEGAZIONE:
     * - is_null() controlla se il valore è NULL
     * - !is_null() restituisce true se NON è null (quindi assegnato)
     * - Implementa funzionalità opzionale di gestione staff
     */
    public function isAssegnato(): bool
    {
        return !is_null($this->staff_assegnato_id);
    }

    /**
     * METODO HELPER: Statistiche complete del prodotto
     * 
     * LINGUAGGIO: PHP + Laravel
     * 
     * @return array Array associativo con tutte le statistiche
     * 
     * SPIEGAZIONE:
     * - Aggregazione di tutti i dati statistici in un unico array
     * - Usa accessor e metodi helper già definiti
     * - ?-> operator: safe navigation (PHP 8.0+), evita errori se null
     * - ->only() su User estrae solo i campi specificati
     * - Utile per API e dashboard
     */
    public function getStatistiche(): array
    {
        return [
            'totale_malfunzionamenti' => $this->totale_malfunzionamenti,
            'malfunzionamenti_critici' => $this->malfunzionamenti_critici_count,
            'malfunzionamenti_alta' => $this->malfunzionamenti_alta_count,
            'totale_segnalazioni' => $this->totale_segnalazioni,
            'has_problemi_critici' => $this->hasMalfunzionamentiCritici(),
            'has_problemi_alta' => $this->hasMalfunzionamentiAlta(),
            'staff_assegnato' => $this->staffAssegnato?->nome_completo,  // Safe navigation
            'categoria_formattata' => $this->categoria_formattata,
            'attivo' => $this->attivo
        ];
    }

    /**
     * METODO HELPER: Badge di gravità per UI
     * 
     * LINGUAGGIO: PHP
     * 
     * @return array Configurazione badge con classe CSS, testo e icona
     * 
     * SPIEGAZIONE:
     * - Logica a cascata per determinare il livello di gravità
     * - Restituisce array con configurazione per Bootstrap badges
     * - 'class': classe CSS Bootstrap per il colore
     * - 'text': testo da visualizzare
     * - 'icon': classe icona Bootstrap Icons
     * - Ordine: Critico > Alta > Media > OK
     */
    public function getBadgeGravita(): array
    {
        // Priorità 1: Problemi critici (rosso)
        if ($this->hasMalfunzionamentiCritici()) {
            return [
                'class' => 'bg-danger',
                'text' => 'Critico',
                'icon' => 'bi-exclamation-triangle-fill'
            ];
        }
        
        // Priorità 2: Problemi di alta gravità (arancione)
        if ($this->hasMalfunzionamentiAlta()) {
            return [
                'class' => 'bg-warning',
                'text' => 'Alta',
                'icon' => 'bi-exclamation-triangle'
            ];
        }
        
        // Priorità 3: Ha malfunzionamenti ma non gravi (blu)
        if ($this->totale_malfunzionamenti > 0) {
            return [
                'class' => 'bg-info',
                'text' => 'Media',
                'icon' => 'bi-info-circle'
            ];
        }
        
        // Priorità 4: Nessun malfunzionamento (verde)
        return [
            'class' => 'bg-success',
            'text' => 'OK',
            'icon' => 'bi-check-circle'
        ];
    }

    // ================================================
    // SEZIONE SCOPE (FILTRI PER QUERY)
    // ================================================

    /**
     * SCOPE: Filtra solo prodotti attivi
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * TIPO: Query Scope (pattern Laravel)
     * 
     * @param Builder $query Il query builder da modificare
     * @return void Modifica la query per riferimento
     * 
     * SPIEGAZIONE:
     * - Gli scope in Laravel si chiamano scopeNome()
     * - Si usano chiamando ->attivi() sulla query
     * - Builder è l'oggetto che costruisce le query SQL
     * - where('attivo', true) aggiunge WHERE attivo = 1
     * - void significa che non restituisce nulla, modifica $query
     */
    public function scopeAttivi(Builder $query): void
    {
        $query->where('attivo', true);
    }

    /**
     * SCOPE: Filtra per categoria specifica
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @param string $categoria Categoria da filtrare
     * @return void
     * 
     * SPIEGAZIONE:
     * - Scope con parametro per filtrare una categoria specifica
     * - Si usa: Prodotto::categoria('lavatrice')->get()
     * - Aggiunge WHERE categoria = 'valore_parametro'
     */
    public function scopeCategoria(Builder $query, string $categoria): void
    {
        $query->where('categoria', $categoria);
    }

    /**
     * SCOPE: Ricerca testuale nei campi principali
     * 
     * LINGUAGGIO: PHP + Eloquent ORM + SQL
     * 
     * @param Builder $query Query builder
     * @param string $termine Termine di ricerca
     * @return void
     * 
     * SPIEGAZIONE:
     * - Implementa ricerca full-text sui campi nome, descrizione, modello
     * - str_ends_with() (PHP 8.0+) verifica se stringa finisce con carattere
     * - Supporta wildcard "*" solo alla fine del termine
     * - LIKE con % permette matching parziale
     * - where(function()) crea OR group in parentesi
     * - Es: "lav*" cerca tutto che inizia con "lav"
     */
    public function scopeRicerca(Builder $query, string $termine): void
    {
        // Gestione wildcard: se termine finisce con *, cerca per prefisso
        if (str_ends_with($termine, '*')) {
            $termine = rtrim($termine, '*');  // Rimuove * dalla fine
            $query->where(function($q) use ($termine) {
                $q->where('nome', 'LIKE', $termine . '%')
                  ->orWhere('descrizione', 'LIKE', $termine . '%')
                  ->orWhere('modello', 'LIKE', $termine . '%');
            });
        } else {
            // Ricerca normale: termine può essere ovunque nel testo
            $query->where(function($q) use ($termine) {
                $q->where('nome', 'LIKE', '%' . $termine . '%')
                  ->orWhere('descrizione', 'LIKE', '%' . $termine . '%')
                  ->orWhere('modello', 'LIKE', '%' . $termine . '%');
            });
        }
    }

    /**
     * SCOPE: Ricerca con wildcard generico
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @param string $termine Pattern di ricerca con *
     * @return void
     * 
     * SPIEGAZIONE:
     * - Versione semplificata che converte * in % di SQL
     * - str_replace('*', '%', $termine) converte wildcard
     * - Più flessibile: supporta * in qualsiasi posizione
     * - Es: "lav*" -> "lav%", "*rice" -> "%rice", "la*trice" -> "la%trice"
     */
    public function scopeRicercaWildcard(Builder $query, string $termine): void
    {
        // Converte wildcard utente (*) in wildcard SQL (%)
        $pattern = str_replace('*', '%', $termine);
        
        $query->where(function($q) use ($pattern) {
            $q->where('nome', 'LIKE', $pattern)
              ->orWhere('modello', 'LIKE', $pattern)
              ->orWhere('descrizione', 'LIKE', $pattern);
        });
    }

    /**
     * SCOPE: Prodotti assegnati a staff specifico
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @param int $staffId ID dello staff member
     * @return void
     * 
     * SPIEGAZIONE:
     * - Filtra prodotti assegnati a un membro dello staff specifico
     * - Implementa funzionalità opzionale del progetto
     * - WHERE staff_assegnato_id = $staffId
     */
    public function scopeAssegnatiA(Builder $query, int $staffId): void
    {
        $query->where('staff_assegnato_id', $staffId);
    }

    /**
     * SCOPE: Prodotti non assegnati a nessuno
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @return void
     * 
     * SPIEGAZIONE:
     * - Filtra prodotti senza assegnazione staff
     * - whereNull() genera WHERE campo IS NULL
     * - Utile per mostrare prodotti disponibili per assegnazione
     */
    public function scopeNonAssegnati(Builder $query): void
    {
        $query->whereNull('staff_assegnato_id');
    }

    /**
     * SCOPE: Prodotti con malfunzionamenti critici
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @return void
     * 
     * SPIEGAZIONE:
     * - whereHas() filtra modelli che hanno relazioni che soddisfano condizioni
     * - Equivalente a EXISTS (SELECT * FROM malfunzionamenti WHERE...)
     * - La closure function definisce le condizioni sulla relazione
     * - Utile per dashboard di emergenza
     */
    public function scopeConMalfunzionamentiCritici(Builder $query): void
    {
        $query->whereHas('malfunzionamenti', function($q) {
            $q->where('gravita', 'critica');
        });
    }

    /**
     * SCOPE: Prodotti con molti malfunzionamenti
     * 
     * LINGUAGGIO: PHP + Eloquent ORM + SQL
     * 
     * @param Builder $query Query builder
     * @param int $soglia Numero minimo di malfunzionamenti (default 5)
     * @return void
     * 
     * SPIEGAZIONE:
     * - withCount() aggiunge un campo calcolato con COUNT()
     * - having() filtra sui risultati aggregati (come WHERE ma per GROUP BY)
     * - Identifica prodotti problematici con molti malfunzionamenti
     * - Il campo calcolato si chiama {relazione}_count
     */
    public function scopeConMoltiMalfunzionamenti(Builder $query, int $soglia = 5): void
    {
        $query->withCount('malfunzionamenti')
            ->having('malfunzionamenti_count', '>=', $soglia);
    }

    /**
     * SCOPE: Alias per categoria (per compatibilità)
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param Builder $query Query builder
     * @param string $categoria Categoria da filtrare
     * @return void
     * 
     * SPIEGAZIONE:
     * - Alias del scope categoria() per nomenclatura alternativa
     * - Richiama il metodo categoria() già definito
     * - Mantiene compatibilità con codice esistente
     */
    public function scopePerCategoria(Builder $query, string $categoria): void
    {
        $query->where('categoria', $categoria);
    }

    // ================================================
    // SEZIONE METODI STATICI UNIFICATI
    // ================================================

    /**
     * METODO STATICO: Sistema unificato delle categorie
     * 
     * LINGUAGGIO: PHP
     * TIPO: Metodo statico (chiamabile senza istanza)
     * 
     * @return array Mapping categoria_db => etichetta_display
     * 
     * SPIEGAZIONE:
     * - self:: richiama metodi sulla stessa classe (alternativa a static::)
     * - Array associativo che mappa valori DB a etichette user-friendly
     * - Sistema centralizzato: tutte le funzioni categorie usano questo
     * - static significa chiamabile come Prodotto::getCategorieUnifico()
     * - Basato sui dati reali presenti nel database seeder
     */
    public static function getCategorieUnifico(): array
    {
        return [
            // Categorie principali da seeder (valori reali nel database)
            'lavatrice' => 'Lavatrici',
            'lavastoviglie' => 'Lavastoviglie', 
            'frigorifero' => 'Frigoriferi',
            'forno' => 'Forni',
            'asciugatrice' => 'Asciugatrici',
            'piano_cottura' => 'Piani Cottura',
            'cappa' => 'Cappe Aspiranti',
            'microonde' => 'Microonde',
            'condizionatore' => 'Condizionatori',
            'aspirapolvere' => 'Aspirapolvere',
            'ferro_stiro' => 'Ferri da Stiro',
            'macchina_caffe' => 'Macchine Caffè',
            'scaldabagno' => 'Scaldabagni',
            'caldaia' => 'Caldaie',
            
            // Categorie aggiuntive supportate (espandibilità futura)
            'climatizzatori' => 'Climatizzatori',
            'elettrodomestici' => 'Elettrodomestici',
            'informatica' => 'Informatica',
            'industriali' => 'Attrezzature Industriali',
            'comunicazione' => 'Apparati Comunicazione',
            'sanitarie' => 'Attrezzature Sanitarie',
            'altro' => 'Altro'
        ];
    }

    /**
     * METODO STATICO DEPRECATO: Compatibilità retroattiva
     * 
     * LINGUAGGIO: PHP
     * 
     * @deprecated Utilizzare getCategorieUnifico() invece
     * @return array Stesso array del metodo unificato
     * 
     * SPIEGAZIONE:
     * - @deprecated in PHPDoc indica metodo obsoleto
     * - Mantiene funzionamento del vecchio codice
     * - Reindirizza al nuovo metodo unificato
     * - Da rimuovere in versioni future
     */
    public static function getCategorie(): array
    {
        return self::getCategorieUnifico();
    }

    /**
     * METODO STATICO: Categorie con conteggio prodotti
     * 
     * LINGUAGGIO: PHP + Eloquent ORM + SQL
     * 
     * @return array Array [categoria => ['label' => nome, 'count' => numero]]
     * 
     * SPIEGAZIONE:
     * - Query aggregata per contare prodotti per categoria
     * - selectRaw() permette di usare SQL raw nelle SELECT
     * - GROUP BY categoria raggruppa i risultati
     * - pluck('count', 'categoria') crea array [categoria => count]
     * - toArray() converte Collection Laravel in array PHP
     * - try/catch gestisce errori database
     * - \Log::error() registra errori nel log Laravel
     */
    public static function getCategorieConConteggio(): array
    {
        try {
            // Ottiene mapping completo categorie
            $categorieComplete = self::getCategorieUnifico();
            
            // Query di conteggio con GROUP BY
            $prodottiPerCategoria = self::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray();

            // Costruisce risultato solo per categorie con prodotti
            $result = [];
            foreach ($prodottiPerCategoria as $categoria => $count) {
                if ($count > 0) { // Solo categorie popolate
                    $result[$categoria] = [
                        'label' => $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria)),
                        'count' => $count
                    ];
                }
            }

            // Ordinamento alfabetico per consistenza UI
            ksort($result);

            return $result;

        } catch (\Exception $e) {
            // Gestione errori con logging
            \Log::error('Errore nel calcolo categorie con conteggio', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * METODO STATICO: Solo categorie presenti nel database
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @return array Array [categoria_db => etichetta_display]
     * 
     * SPIEGAZIONE:
     * - distinct() evita duplicati nella query
     * - pluck('campo') estrae solo i valori di un campo
     * - toArray() converte in array PHP
     * - asort() ordina per valore mantenendo le chiavi
     * - Più leggero di getCategorieConConteggio() se non serve il count
     */
    public static function getCategorieDisponibili(): array
    {
        try {
            // Estrae categorie uniche presenti nel DB
            $categoriePresenti = self::where('attivo', true)
                ->distinct()
                ->pluck('categoria')
                ->toArray();

            $categorieComplete = self::getCategorieUnifico();
            
            // Costruisce array con solo categorie presenti
            $result = [];
            foreach ($categoriePresenti as $categoria) {
                $result[$categoria] = $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria));
            }

            asort($result); // Ordina per etichetta
            
            return $result;

        } catch (\Exception $e) {
            \Log::error('Errore nel recupero categorie disponibili', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * METODO STATICO: Validazione categoria
     * 
     * LINGUAGGIO: PHP
     * 
     * @param string $categoria Categoria da validare
     * @return bool True se categoria valida
     * 
     * SPIEGAZIONE:
     * - array_key_exists() verifica se chiave esiste in array
     * - Valida che la categoria sia tra quelle supportate
     * - Utile per validazione form e API
     */
    public static function isCategoriaValida(string $categoria): bool
    {
        return array_key_exists($categoria, self::getCategorieUnifico());
    }

    /**
     * METODO STATICO: Ottieni etichetta categoria
     * 
     * LINGUAGGIO: PHP
     * 
     * @param string $categoria Categoria da convertire
     * @return string Etichetta formattata
     * 
     * SPIEGAZIONE:
     * - Converte categoria DB in etichetta display
     * - ?? è null coalescing operator (PHP 7.0+)
     * - Se categoria non mappata, formatta automaticamente
     * - ucfirst() rende maiuscola la prima lettera
     * - str_replace('_', ' ') converte underscore in spazi
     */
    public static function getEtichettaCategoria(string $categoria): string
    {
        return self::getCategorieUnifico()[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria));
    }

    /**
     * METODO STATICO: Ricerca avanzata prodotti
     * 
     * LINGUAGGIO: PHP + Eloquent ORM
     * 
     * @param string $termine Termine di ricerca
     * @param string|null $categoria Categoria opzionale per filtrare
     * @return Builder Query builder configurato (non eseguito)
     * 
     * SPIEGAZIONE:
     * - Combina ricerca testuale e filtro categoria
     * - Restituisce Builder, non risultati: permette ulteriori concatenazioni
     * - Es: ::ricercaAvanzata('lav', 'elettrodomestici')->orderBy('nome')->get()
     * - Validazione categoria prima dell'applicazione del filtro
     */
    public static function ricercaAvanzata(string $termine, string $categoria = null): Builder
    {
        $query = self::where('attivo', true);

        // Applica ricerca testuale con scope
        $query->ricerca($termine);

        // Filtra per categoria se specificata e valida
        if ($categoria && self::isCategoriaValida($categoria)) {
            $query->categoria($categoria);
        }

        return $query;
    }

    // ================================================
    // SEZIONE GESTIONE FILE
    // ================================================

    /**
     * METODO: Salvataggio sicuro immagine prodotto
     * 
     * LINGUAGGIO: PHP + Laravel File Storage
     * 
     * @param \Illuminate\Http\UploadedFile $file File uploadato
     * @return string Path dell'immagine salvata
     * @throws \Exception Se tutti i metodi di storage falliscono
     * 
     * SPIEGAZIONE:
     * - Gestisce upload file con diversi meccanismi di fallback
     * - time() genera timestamp per nomi file unici
     * - getClientOriginalExtension() estrae estensione file originale
     * - try/catch gestisce errori di ogni metodo di storage
     * - mkdir() crea directory se non esistenti (chmod 0755)
     * - move() sposta file dalla posizione temporanea
     * - Logging per debug e monitoraggio
     */
    public function salvaImmagine($file): string
    {
        try {
            // Genera nome file univoco: timestamp_id.estensione
            $filename = time() . '_' . $this->id . '.' . $file->getClientOriginalExtension();
            
            // Array di metodi di storage con fallback
            $methods = [
                // METODO 1: Storage Laravel standard (storage/app/public)
                'storage_public' => function() use ($file, $filename) {
                    return $file->storeAs('prodotti', $filename, 'public');
                },
                // METODO 2: Directory pubblica uploads
                'public_uploads' => function() use ($file, $filename) {
                    $path = public_path('uploads/prodotti');
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);  // Crea directory ricorsivamente
                    }
                    $file->move($path, $filename);
                    return 'uploads/prodotti/' . $filename;
                },
                // METODO 3: Directory pubblica images
                'public_images' => function() use ($file, $filename) {
                    $path = public_path('images/prodotti');
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    $file->move($path, $filename);
                    return 'images/prodotti/' . $filename;
                }
            ];

            // Prova ogni metodo finché uno non funziona
            foreach ($methods as $method => $callback) {
                try {
                    $result = $callback();
                    \Log::info("Immagine salvata con successo", [
                        'method' => $method,
                        'filename' => $filename,
                        'result' => $result
                    ]);
                    return $result;
                } catch (\Exception $e) {
                    \Log::warning("Metodo {$method} fallito", [
                        'error' => $e->getMessage()
                    ]);
                    continue;  // Prova il metodo successivo
                }
            }

            // Se arriviamo qui, tutti i metodi sono falliti
            throw new \Exception('Tutti i metodi di storage sono falliti');

        } catch (\Exception $e) {
            \Log::error('Errore nel salvataggio immagine', [
                'error' => $e->getMessage(),
                'prodotto_id' => $this->id
            ]);
            
            throw $e;  // Rilancia l'eccezione per gestione upstream
        }
    }

    /**
     * METODO: Eliminazione sicura immagine prodotto
     * 
     * LINGUAGGIO: PHP + File System
     * 
     * @return bool True se eliminazione riuscita
     * 
     * SPIEGAZIONE:
     * - Elimina file immagine da tutte le possibili posizioni
     * - storage_path() restituisce path assoluto a storage/
     * - public_path() restituisce path assoluto a public/
     * - unlink() elimina file dal filesystem
     * - Prova tutte le posizioni possibili per sicurezza
     * - Restituisce true se almeno un file è stato eliminato
     */
    public function eliminaImmagine(): bool
    {
        if (!$this->foto) {
            return true;  // Niente da eliminare
        }

        try {
            // Array di possibili percorsi del file
            $paths = [
                storage_path('app/public/' . $this->foto),    // Storage Laravel
                public_path('storage/' . $this->foto),        // Symlink storage
                public_path($this->foto)                      // Path diretto
            ];

            $deleted = false;
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    unlink($path);  // Elimina file
                    $deleted = true;
                    \Log::info("Immagine eliminata", ['path' => $path]);
                }
            }

            return $deleted;

        } catch (\Exception $e) {
            \Log::error('Errore eliminazione immagine', [
                'error' => $e->getMessage(),
                'foto' => $this->foto
            ]);
            return false;
        }
    }

    // ================================================
    // SEZIONE API
    // ================================================

    /**
     * METODO: Serializzazione per API
     * 
     * LINGUAGGIO: PHP + Laravel
     * 
     * @param bool $includeDetails Se includere dettagli completi
     * @return array Rappresentazione array del prodotto
     * 
     * SPIEGAZIONE:
     * - Converte modello in array per risposte API/JSON
     * - $includeDetails controlla livello di dettaglio
     * - ?->toISOString() safe navigation con formattazione ISO8601
     * - ->only() estrae solo campi specificati da modello correlato
     * - Separazione tra dati base e dettagli per ottimizzazione
     */
    public function toApiArray(bool $includeDetails = false): array
    {
        // Dati base sempre inclusi
        $data = [
            'id' => $this->id,
            'nome' => $this->nome,
            'modello' => $this->modello,
            'categoria' => $this->categoria,
            'categoria_label' => $this->categoria_label,
            'descrizione' => $this->descrizione,
            'prezzo' => $this->prezzo,
            'prezzo_formattato' => $this->getPrezzoFormattato(),
            'foto_url' => $this->foto_url,
            'attivo' => $this->attivo,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString()
        ];

        // Dati dettagliati solo se richiesti
        if ($includeDetails) {
            $data['note_tecniche'] = $this->note_tecniche;
            $data['modalita_installazione'] = $this->modalita_installazione;
            $data['modalita_uso'] = $this->modalita_uso;
            $data['statistiche'] = $this->getStatistiche();
            $data['staff_assegnato'] = $this->staffAssegnato?->only(['id', 'nome', 'cognome']);
            $data['badge_gravita'] = $this->getBadgeGravita();
        }

        return $data;
    }

    // ================================================
    // SEZIONE DEBUGGING
    // ================================================

    /**
     * METODO DEBUG: Verifica esistenza file immagine
     * 
     * LINGUAGGIO: PHP + File System
     * 
     * @return array Dettagli su tutti i possibili path dell'immagine
     * 
     * SPIEGAZIONE:
     * - Metodo di debug per risolvere problemi con le immagini
     * - Controlla esistenza file in tutte le posizioni possibili
     * - basename() estrae solo il nome file dal path completo
     * - filesize() restituisce dimensione file in bytes
     * - is_readable() verifica permessi di lettura
     * - Utile per troubleshooting problemi di upload/visualizzazione
     */
    public function verificaImmagine(): array
    {
        if (!$this->foto) {
            return ['exists' => false, 'reason' => 'No image set'];
        }

        // Definisce tutti i possibili percorsi
        $paths = [
            'storage_public' => storage_path('app/public/' . $this->foto),
            'public_storage' => public_path('storage/' . $this->foto),
            'public_direct' => public_path($this->foto),
            'public_uploads' => public_path('uploads/prodotti/' . basename($this->foto)),
            'public_images' => public_path('images/prodotti/' . basename($this->foto))
        ];

        // Controlla ogni percorso
        $results = [];
        foreach ($paths as $key => $path) {
            $results[$key] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => file_exists($path) && is_readable($path),
                'size' => file_exists($path) ? filesize($path) : 0
            ];
        }

        return $results;
    }

    // ================================================
    // SEZIONE EVENTI DEL MODELLO (LIFECYCLE HOOKS)
    // ================================================

    /**
     * METODO STATICO: Configurazione eventi del modello
     * 
     * LINGUAGGIO: PHP + Laravel Eloquent Events
     * 
     * @return void
     * 
     * SPIEGAZIONE:
     * - boot() è un metodo speciale di Laravel per configurare il modello
     * - parent::boot() chiama il metodo boot della classe genitore
     * - static::deleting() registra listener per evento "eliminazione"
     * - static::created() registra listener per evento "creazione"
     * - static::updated() registra listener per evento "aggiornamento"
     * - Eventi utili per logging, pulizia, notifiche, etc.
     * - auth()->id() restituisce ID utente autenticato (se presente)
     * - ?? 'Sistema' è fallback se nessun utente autenticato
     */
    protected static function boot()
    {
        parent::boot();
        
        /**
         * EVENTO: Prima dell'eliminazione del prodotto
         * 
         * SPIEGAZIONE:
         * - Si attiva automaticamente prima di delete()
         * - Logging per audit trail
         * - Conta malfunzionamenti associati
         * - NON elimina automaticamente i malfunzionamenti (preserva storico)
         */
        static::deleting(function ($prodotto) {
            \Log::info('Prodotto in eliminazione', [
                'prodotto_id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'malfunzionamenti_count' => $prodotto->malfunzionamenti()->count()
            ]);

            // NOTA: I malfunzionamenti non vengono eliminati automaticamente
            // per preservare lo storico tecnico. Solo l'admin può decidere
            // se eliminare anche i malfunzionamenti associati
        });
        
        /**
         * EVENTO: Dopo la creazione del prodotto
         * 
         * SPIEGAZIONE:
         * - Si attiva automaticamente dopo create()
         * - Logging per tracciabilità
         * - Registra chi ha creato il prodotto
         */
        static::created(function ($prodotto) {
            \Log::info('Nuovo prodotto creato', [
                'prodotto_id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'categoria' => $prodotto->categoria,
                'created_by' => auth()->id() ?? 'Sistema'
            ]);
        });

        /**
         * EVENTO: Dopo l'aggiornamento del prodotto
         * 
         * SPIEGAZIONE:
         * - Si attiva automaticamente dopo update()
         * - getChanges() restituisce array dei campi modificati
         * - Logging delle modifiche per audit
         * - Traccia chi ha fatto la modifica
         */
        static::updated(function ($prodotto) {
            \Log::info('Prodotto aggiornato', [
                'prodotto_id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'changes' => $prodotto->getChanges(),
                'updated_by' => auth()->id() ?? 'Sistema'
            ]);
        });
    }
}

/*
 * RIEPILOGO FUNZIONALITÀ DEL MODELLO:
 * 
 * 1. RELAZIONI DATABASE:
 *    - hasMany con Malfunzionamento (1:N)
 *    - belongsTo con User/Staff (N:1, opzionale)
 * 
 * 2. RICERCA E FILTRI:
 *    - Ricerca testuale con wildcard (*)
 *    - Filtri per categoria, staff, gravità
 *    - Scope riutilizzabili per query complesse
 * 
 * 3. GESTIONE CATEGORIE:
 *    - Sistema unificato centralizzato
 *    - Mapping DB -> Display labels
 *    - Conteggi e validazioni
 * 
 * 4. GESTIONE IMMAGINI:
 *    - Upload multipaths con fallback
 *    - URL generation per diverse configurazioni
 *    - Eliminazione sicura
 * 
 * 5. STATISTICHE E REPORTING:
 *    - Conteggi malfunzionamenti per gravità
 *    - Badge UI dinamici
 *    - Aggregazioni per dashboard
 * 
 * 6. API SUPPORT:
 *    - Serializzazione configurabile
 *    - Livelli di dettaglio
 *    - Formattazione JSON-friendly
 * 
 * 7. LOGGING E AUDIT:
 *    - Eventi lifecycle completi
 *    - Tracciabilità operazioni
 *    - Debug tools per troubleshooting
 */