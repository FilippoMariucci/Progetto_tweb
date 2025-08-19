<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Malfunzionamento;

/**
 * Modello Eloquent che rappresenta la tabella "prodotti" nel database.
 * Ogni istanza di questa classe corrisponde a una riga della tabella "prodotti".
 * Fornisce relazioni, scope e metodi helper per la gestione dei prodotti.
 */
class Prodotto extends Model
{
    // Nome della tabella associata al modello (opzionale se segue la convenzione Laravel)
    protected $table = 'prodotti';
    // Trait per abilitare le factory nei test e seed
    use HasFactory;

    /**
     * Campi che possono essere assegnati in massa
     */
    /**
     * Elenco dei campi che possono essere assegnati in massa tramite create() o fill().
     * Serve a proteggere da mass assignment vulnerability.
     * Questi campi corrispondono alle colonne della tabella "prodotti".
     */
    protected $fillable = [
        'nome',                  // Nome del prodotto
        'modello',               // Modello del prodotto
        'descrizione',           // Descrizione testuale
        'categoria',             // Categoria di appartenenza (es: lavatrice)
        'foto',                  // Nome file immagine associata
        'note_tecniche',         // Note tecniche aggiuntive
        'modalita_installazione',// Modalità di installazione
        'modalita_uso',          // Modalità d'uso
        'prezzo',                // Prezzo del prodotto
        'attivo',                // Stato attivo/disattivo (boolean)
        'staff_assegnato_id',    // ID membro staff assegnato (FK)
    ];

    /**
     * Cast automatici per i campi
     */
    /**
     * Cast automatici per i campi: conversione automatica dei tipi.
     * Esempio: 'prezzo' sarà sempre float con 2 decimali, 'attivo' sarà boolean.
     * Questo permette di lavorare con i tipi corretti senza conversioni manuali.
     */
    protected function casts(): array
    {
        return [
            'prezzo' => 'decimal:2', // Decimale con 2 cifre dopo la virgola
            'attivo' => 'boolean',   // Conversione automatica a booleano (0/1 <-> false/true)
        ];
    }

    /**
     * Relazione con i malfunzionamenti
     * Un prodotto può avere molti malfunzionamenti
     */
    /**
     * Relazione 1:N: un prodotto può avere molti malfunzionamenti.
     * Restituisce la query per tutti i malfunzionamenti associati a questo prodotto.
     * Esempio di uso: $prodotto->malfunzionamenti
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function malfunzionamenti()
    {
        return $this->hasMany(Malfunzionamento::class);
    }

    /**
     * Relazione con lo staff assegnato (funzionalità opzionale)
     * Un prodotto può essere gestito da un membro dello staff
     */
    /**
     * Relazione N:1: un prodotto può essere gestito da un membro dello staff.
     * Restituisce l'utente (staff) assegnato a questo prodotto.
     * Esempio di uso: $prodotto->staffAssegnato
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staffAssegnato()
    {
        return $this->belongsTo(User::class, 'staff_assegnato_id');
    }

    // === SCOPE PER QUERY OTTIMIZZATE ===

    /**
     * Scope per filtrare solo prodotti attivi
     * Uso: Prodotto::attivi()->get()
     */
    /**
     * Scope locale: filtra solo i prodotti attivi.
     * Permette di scrivere Prodotto::attivi()->get() per ottenere solo quelli attivi.
     * @param Builder $query
     */
    public function scopeAttivi(Builder $query): void
    {
        $query->where('attivo', true);
    }

    /**
     * Scope per cercare prodotti per categoria
     * Uso: Prodotto::categoria('lavatrice')->get()
     */
    /**
     * Scope locale: filtra per categoria.
     * Permette di scrivere Prodotto::categoria('lavatrice')->get()
     * @param Builder $query
     * @param string $categoria
     */
    public function scopeCategoria(Builder $query, string $categoria): void
    {
        $query->where('categoria', $categoria);
    }

    /**
     * Scope per ricerca testuale con supporto wildcard
     * Gestisce la ricerca con "*" come ultimo carattere (es: "lav*")
     */
    /**
     * Scope locale: ricerca testuale con supporto wildcard finale (es: "lav*").
     * Se il termine termina con *, ricerca "inizia con" su più campi; altrimenti usa fulltext.
     * Permette ricerche flessibili su nome, descrizione e modello.
     * @param Builder $query
     * @param string $termine
     */
    public function scopeRicerca(Builder $query, string $termine): void
    {
        // Se il termine termina con *, facciamo una ricerca "inizia con"
        if (str_ends_with($termine, '*')) {
            $termine = rtrim($termine, '*'); // Rimuovi l'asterisco
            $query->where(function($q) use ($termine) {
                $q->where('nome', 'LIKE', $termine . '%')
                  ->orWhere('descrizione', 'LIKE', $termine . '%')
                  ->orWhere('modello', 'LIKE', $termine . '%');
            });
        } else {
            // Ricerca fulltext (richiede indice FULLTEXT su questi campi)
            $query->whereRaw(
                "MATCH(nome, descrizione, modello) AGAINST(? IN BOOLEAN MODE)", 
                [$termine . '*']
            );
        }
    }

    /**
     * Scope per prodotti assegnati a uno staff specifico
     */
    /**
     * Scope locale: filtra prodotti assegnati a uno specifico membro dello staff.
     * Permette di scrivere Prodotto::assegnatiA($id)->get()
     * @param Builder $query
     * @param int $staffId
     */
    public function scopeAssegnatiA(Builder $query, int $staffId): void
    {
        $query->where('staff_assegnato_id', $staffId);
    }

    // === METODI HELPER ===

    /**
     * Ottiene il path completo dell'immagine
     */
    /**
     * Accessor: restituisce l'URL completo dell'immagine del prodotto.
     * Se non c'è immagine, restituisce un placeholder.
     * Esempio di uso: $prodotto->foto_url
     * @return string
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/prodotti/' . $this->foto);
        }
        // Immagine placeholder se non presente
        return asset('images/prodotto-placeholder.jpg');
    }

    /**
     * Conta i malfunzionamenti totali per questo prodotto
     */
    /**
     * Accessor: conta il numero totale di malfunzionamenti associati a questo prodotto.
     * Utile per mostrare statistiche rapide.
     * Esempio di uso: $prodotto->totale_malfunzionamenti
     * @return int
     */
    public function getTotaleMalfunzionamentiAttribute(): int
    {
        return $this->malfunzionamenti()->count();
    }

    /**
     * Conta le segnalazioni totali per tutti i malfunzionamenti
     */
    /**
     * Accessor: somma il numero di segnalazioni di tutti i malfunzionamenti del prodotto.
     * Utile per statistiche aggregate.
     * Esempio di uso: $prodotto->totale_segnalazioni
     * @return int
     */
    public function getTotaleSegnalazioniAttribute(): int
    {
        return $this->malfunzionamenti()->sum('numero_segnalazioni');
    }

    /**
     * Ottiene i malfunzionamenti ordinati per gravità e frequenza
     */
    /**
     * Accessor: restituisce i malfunzionamenti ordinati per gravità e frequenza.
     * Prima i più gravi, poi quelli con più segnalazioni.
     * Esempio di uso: $prodotto->malfunzionamenti_ordered
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMalfunzionamentiOrderedAttribute()
    {
        return $this->malfunzionamenti()
            ->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
            ->orderBy('numero_segnalazioni', 'desc')
            ->get();
    }

    /**
     * Verifica se il prodotto ha malfunzionamenti critici
     */
    /**
     * Metodo helper: verifica se il prodotto ha almeno un malfunzionamento critico.
     * Restituisce true se esiste almeno un malfunzionamento "critica".
     * Esempio di uso: $prodotto->hasMalfunzionamentiCritici()
     * @return bool
     */
    public function hasMalfunzionamentiCritici(): bool
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->exists();
    }

    /**
     * Ottiene le categorie disponibili (metodo statico)
     */
    /**
     * Metodo statico: restituisce la lista delle categorie disponibili (chiave => etichetta).
     * Utile per popolare select nei form o validare input.
     * @return array
     */
    public static function getCategorie(): array
    {
        return [
            'lavatrice' => 'Lavatrici',
            'lavastoviglie' => 'Lavastoviglie', 
            'forno' => 'Forni',
            'frigorifero' => 'Frigoriferi',
            'asciugatrice' => 'Asciugatrici',
            'piano_cottura' => 'Piani Cottura',
            'cappa' => 'Cappe Aspiranti',
            'microonde' => 'Microonde',
            'altro' => 'Altri Elettrodomestici'
        ];
    }

    /**
     * Ottiene il nome leggibile della categoria
     */
    /**
     * Accessor: restituisce l'etichetta leggibile della categoria del prodotto.
     * Se la categoria non è tra quelle note, restituisce la stringa capitalizzata.
     * Esempio di uso: $prodotto->categoria_label
     * @return string
     */
    public function getCategoriaLabelAttribute(): string
    {
        $categorie = self::getCategorie();
        return $categorie[$this->categoria] ?? ucfirst($this->categoria);
    }
}