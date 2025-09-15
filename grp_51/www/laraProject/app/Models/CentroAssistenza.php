<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Modello CentroAssistenza - VERSIONE COMPLETA
 * Include tutti i metodi e accessors necessari per il controller
 */
class CentroAssistenza extends Model
{
    use HasFactory;

    /**
     * Nome della tabella nel database
     */
    protected $table = 'centri_assistenza';

    /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'nome',
        'indirizzo',
        'citta',
        'provincia',
        'cap',
        'telefono',
        'email',
    ];

    /**
     * Cast automatici per i campi
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ================================================
    // RELAZIONI ELOQUENT
    // ================================================

    /**
     * Relazione con i tecnici (livello 2)
     * Un centro può avere molti tecnici assegnati
     */
    public function tecnici()
    {
        return $this->hasMany(User::class, 'centro_assistenza_id')
                    ->where('livello_accesso', '2');
    }

    /**
     * Relazione con tutti gli utenti collegati al centro
     */
    public function utenti()
    {
        return $this->hasMany(User::class, 'centro_assistenza_id');
    }

    // ================================================
    // SCOPE PER QUERY OTTIMIZZATE
    // ================================================

    /**
     * Scope per cercare centri per città
     */
    public function scopeCitta(Builder $query, string $citta): void
    {
        $query->where('citta', 'LIKE', "%{$citta}%");
    }

    /**
     * Scope per cercare centri per provincia
     */
    public function scopeProvincia(Builder $query, string $provincia): void
    {
        $query->where('provincia', strtoupper($provincia));
    }

    /**
     * Scope per ricerca geografica generale
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
     * Scope per centri con tecnici disponibili
     */
    public function scopeConTecnici(Builder $query): void
    {
        $query->whereHas('tecnici');
    }

    /**
     * Scope per ordinamento geografico
     */
    public function scopeOrdinatoGeograficamente(Builder $query): void
    {
        $query->orderBy('provincia')->orderBy('citta')->orderBy('nome');
    }

    // ================================================
    // ACCESSORS (METODI MANCANTI CRITICI!)
    // ================================================

    /**
     * Ottiene l'indirizzo completo formattato
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getIndirizzoCompletoAttribute(): string
    {
        $indirizzo = $this->indirizzo;

        if ($this->cap) {
            $indirizzo .= ', ' . $this->cap;
        }

        $indirizzo .= ' ' . $this->citta;

        if ($this->provincia) {
            $indirizzo .= ' (' . strtoupper($this->provincia) . ')';
        }

        return $indirizzo;
    }

    /**
     * Conta i tecnici assegnati a questo centro
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getNumeroTecniciAttribute(): int
    {
        return $this->tecnici()->count();
    }

    /**
     * Verifica se il centro ha tecnici disponibili
     * QUESTO METODO ERA MANCANTE!
     */
    public function hasTecnici(): bool
    {
        return $this->tecnici()->exists();
    }

    /**
     * Ottiene i tecnici con le loro specializzazioni
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getTecniciConSpecializzazioniAttribute()
    {
        return $this->tecnici()
            ->select('id', 'nome', 'cognome', 'username', 'specializzazione', 'data_nascita', 'centro_assistenza_id')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Formatta il numero di telefono in modo leggibile
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getTelefonoFormatatoAttribute(): string
    {
        if (!$this->telefono) {
            return '';
        }

        // Rimuove spazi e caratteri non numerici tranne +
        $telefono = preg_replace('/[^\d+]/', '', $this->telefono);

        // Formato italiano standard
        if (strlen($telefono) === 10 && !str_starts_with($telefono, '+')) {
            // Es: 0712345678 -> 071 234 5678
            return substr($telefono, 0, 3) . ' ' .
                   substr($telefono, 3, 3) . ' ' .
                   substr($telefono, 6);
        }

        if (str_starts_with($telefono, '+39') && strlen($telefono) === 13) {
            // Es: +390712345678 -> +39 071 234 5678
            return '+39 ' . substr($telefono, 3, 3) . ' ' .
                   substr($telefono, 6, 3) . ' ' .
                   substr($telefono, 9);
        }

        return $this->telefono; // Ritorna originale se non riconosciuto
    }

    /**
     * Controlla se il centro è aperto in base all'orario
     * QUESTO METODO ERA MANCANTE!
     */
    public function isAperto(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = domenica, 1 = lunedì, etc.
        $hour = $now->hour;

        // Orari standard: Lun-Ven 8:30-17:30, Sab 8:30-12:30
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Lun-Ven
            return $hour >= 8 && $hour < 18;
        } elseif ($dayOfWeek === 6) { // Sabato
            return $hour >= 8 && $hour < 13;
        }

        return false; // Domenica chiuso
    }

    /**
     * Ottiene l'orario di apertura formattato
     * QUESTO ACCESSOR ERA MANCANTE!
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
     * Ottiene il link per Google Maps
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        $indirizzo = urlencode($this->indirizzo_completo);
        return "https://www.google.com/maps/search/?api=1&query={$indirizzo}";
    }

    /**
     * Statistiche del centro
     * QUESTO ACCESSOR ERA MANCANTE!
     */
    public function getStatisticheAttribute(): array
    {
        $tecnici = $this->tecnici()->get();

        $specializzazioni = $tecnici->pluck('specializzazione')
            ->filter()
            ->countBy()
            ->toArray();

        return [
            'totale_tecnici' => $tecnici->count(),
            'specializzazioni' => $specializzazioni,
            'eta_media_tecnici' => $tecnici->whereNotNull('data_nascita')
                ->avg(function($tecnico) {
                    return $tecnico->data_nascita ? 
                        now()->diffInYears($tecnico->data_nascita) : null;
                })
        ];
    }

    // ================================================
    // METODI STATICI
    // ================================================

    /**
     * Ottiene le province disponibili (metodo statico)
     * QUESTO METODO ERA MANCANTE!
     */
    public static function getProvinceDisponibili(): array
    {
        return self::distinct()
            ->orderBy('provincia')
            ->pluck('provincia', 'provincia')
            ->toArray();
    }

    /**
     * Ottiene le città per provincia (metodo statico)
     * QUESTO METODO ERA MANCANTE!
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
     * Trova il centro più vicino per provincia/città
     */
    public static function findNearby(string $provincia = null, string $citta = null)
    {
        $query = self::query();

        if ($provincia) {
            $query->where('provincia', $provincia);
        }

        if ($citta) {
            $query->where('citta', 'LIKE', "%{$citta}%");
        }

        return $query->conTecnici()
            ->ordinatoGeograficamente()
            ->first();
    }
}