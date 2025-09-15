<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Malfunzionamento extends Model
{
    protected $table = 'malfunzionamenti';
    use HasFactory;

    /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'prodotto_id',
        'titolo',
        'descrizione',
        'gravita',
        'soluzione',
        'strumenti_necessari',
        'tempo_stimato',
        'difficolta',
        'numero_segnalazioni',
        'prima_segnalazione',
        'ultima_segnalazione',
        'creato_da',
        'modificato_da',
    ];

    /**
     * Cast automatici per i campi
     */
    protected function casts(): array
    {
        return [
            'prima_segnalazione' => 'date',
            'ultima_segnalazione' => 'date',
            'numero_segnalazioni' => 'integer',
            'tempo_stimato' => 'integer',
        ];
    }

    /**
     * Relazione con il prodotto
     * Un malfunzionamento appartiene a un prodotto
     */
    public function prodotto()
    {
        return $this->belongsTo(Prodotto::class);
    }

    /**
     * Relazione con l'utente che ha creato il malfunzionamento
     */
    public function creatoBy()
    {
        return $this->belongsTo(User::class, 'creato_da');
    }

    /**
     * Relazione con l'utente che ha modificato per ultimo il malfunzionamento
     */
    public function modificatoBy()
    {
        return $this->belongsTo(User::class, 'modificato_da');
    }

    // === SCOPE PER QUERY OTTIMIZZATE ===

    /**
     * Scope per filtrare per gravità
     * Uso: Malfunzionamento::gravita('critica')->get()
     */
    public function scopeGravita(Builder $query, string $gravita): void
    {
        $query->where('gravita', $gravita);
    }

    /**
     * Scope per filtrare per difficoltà
     */
    public function scopeDifficolta(Builder $query, string $difficolta): void
    {
        $query->where('difficolta', $difficolta);
    }

    /**
     * Scope per ordinare per gravità (critica prima)
     */
    public function scopeOrdinatoPerGravita(Builder $query): void
    {
        $query->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')");
    }

    /**
     * Scope per ordinare per frequenza (più segnalati prima)
     */
    public function scopeOrdinatoPerFrequenza(Builder $query): void
    {
        $query->orderBy('numero_segnalazioni', 'desc');
    }

    /**
     * Scope per malfunzionamenti recenti
     */
    public function scopeRecenti(Builder $query, int $giorni = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($giorni));
    }

    /**
     * Scope per malfunzionamenti critici
     */
    public function scopeCritici(Builder $query): void
    {
        $query->where('gravita', 'critica');
    }

    /**
     * Scope per malfunzionamenti frequenti (soglia personalizzabile)
     */
    public function scopeFrequenti(Builder $query, int $soglia = 10): void
    {
        $query->where('numero_segnalazioni', '>=', $soglia);
    }

    /**
     * Scope per ricerca full-text
     */
    public function scopeRicerca(Builder $query, string $termine): void
    {
        $query->whereRaw(
            "MATCH(titolo, descrizione) AGAINST(? IN BOOLEAN MODE)", 
            [$termine . '*']
        );
    }

    /**
     * Scope per malfunzionamenti per categoria prodotto
     */
    public function scopePerCategoriaProdotto(Builder $query, string $categoria): void
    {
        $query->whereHas('prodotto', function($q) use ($categoria) {
            $q->where('categoria', $categoria);
        });
    }

    // === METODI HELPER ===

    /**
     * Ottiene l'icona CSS per la gravità
     */
    public function getGravitaIconAttribute(): string
    {
        return match($this->gravita) {
            'critica' => 'bi-exclamation-triangle-fill text-danger',
            'alta' => 'bi-exclamation-triangle text-warning',
            'media' => 'bi-info-circle text-info',
            'bassa' => 'bi-check-circle text-success',
            default => 'bi-question-circle text-secondary'
        };
    }

    /**
     * Ottiene la classe CSS per la gravità
     */
    public function getGravitaClassAttribute(): string
    {
        return match($this->gravita) {
            'critica' => 'text-danger fw-bold',
            'alta' => 'text-warning fw-semibold',
            'media' => 'text-info',
            'bassa' => 'text-success',
            default => 'text-secondary'
        };
    }

    /**
     * Ottiene l'icona per la difficoltà
     */
    public function getDifficoltaIconAttribute(): string
    {
        return match($this->difficolta) {
            'esperto' => 'bi-star-fill text-danger',
            'difficile' => 'bi-star-half text-warning',
            'media' => 'bi-star text-info',
            'facile' => 'bi-circle text-success',
            default => 'bi-question-circle text-secondary'
        };
    }

    /**
     * Formatta il tempo stimato in modo leggibile
     */
    public function getTempoFormatatoAttribute(): string
    {
        if (!$this->tempo_stimato) {
            return 'Non specificato';
        }

        $minuti = $this->tempo_stimato;
        
        if ($minuti < 60) {
            return $minuti . ' minuti';
        }
        
        $ore = floor($minuti / 60);
        $minutiRestanti = $minuti % 60;
        
        $result = $ore . ' ora' . ($ore > 1 ? 'e' : '');
        
        if ($minutiRestanti > 0) {
            $result .= ' e ' . $minutiRestanti . ' minuti';
        }
        
        return $result;
    }

    /**
     * Ottiene la priorità numerica per ordinamento
     */
    public function getPrioritaNumericaAttribute(): int
    {
        $gravitaPunti = match($this->gravita) {
            'critica' => 100,
            'alta' => 75,
            'media' => 50,
            'bassa' => 25,
            default => 0
        };
        
        $frequenzaPunti = min($this->numero_segnalazioni * 2, 50);
        
        return $gravitaPunti + $frequenzaPunti;
    }

    /**
     * Verifica se il malfunzionamento è urgente
     */
    public function isUrgente(): bool
    {
        return $this->gravita === 'critica' || 
               ($this->gravita === 'alta' && $this->numero_segnalazioni >= 10);
    }

    /**
     * Verifica se richiede competenze specialistiche
     */
    public function richiedeEsperto(): bool
    {
        return $this->difficolta === 'esperto' || 
               ($this->difficolta === 'difficile' && $this->tempo_stimato > 90);
    }

    /**
     * Ottiene le statistiche di frequenza
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
     * Calcola il trend delle segnalazioni (crescente, stabile, decrescente)
     */
    private function calcolaTrend(): string
    {
        $ultimoMese = now()->subMonth();
        
        if ($this->ultima_segnalazione->isAfter($ultimoMese)) {
            return 'crescente';
        } elseif ($this->ultima_segnalazione->isAfter(now()->subMonths(3))) {
            return 'stabile';
        } else {
            return 'decrescente';
        }
    }

    /**
     * Ottiene i passaggi della soluzione come array
     */
    public function getPassaggiSoluzioneAttribute(): array
    {
        if (!$this->soluzione) {
            return [];
        }
        
        // Divide la soluzione per numeri o punti elenco
        $passaggi = preg_split('/\n\s*\d+\.\s*|\n\s*[-*]\s*/', $this->soluzione);
        
        // Rimuove elementi vuoti e pulisce
        return array_filter(array_map('trim', $passaggi));
    }

    /**
     * Ottiene gli strumenti necessari come array
     */
    public function getStrumentiArrayAttribute(): array
    {
        if (!$this->strumenti_necessari) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $this->strumenti_necessari)));
    }

    /**
     * Ottiene malfunzionamenti correlati (stesso prodotto o categoria)
     */
    public function getCorrelatiAttribute()
    {
        return self::where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where('prodotto_id', $this->prodotto_id)
                      ->orWhereHas('prodotto', function($q) {
                          $q->where('categoria', $this->prodotto->categoria);
                      });
            })
            ->where('gravita', $this->gravita)
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Metodi statici per statistiche
     */
    public static function getStatisticheGenerali(): array
    {
        return [
            'totale' => self::count(),
            'per_gravita' => self::selectRaw('gravita, COUNT(*) as count')
                ->groupBy('gravita')
                ->pluck('count', 'gravita'),
            'per_difficolta' => self::selectRaw('difficolta, COUNT(*) as count')
                ->groupBy('difficolta')
                ->pluck('count', 'difficolta'),
            'media_segnalazioni' => self::avg('numero_segnalazioni'),
            'tempo_medio_risoluzione' => self::avg('tempo_stimato'),
            'creati_ultimo_mese' => self::where('created_at', '>=', now()->subMonth())->count()
        ];
    }

    /**
     * Ottiene i malfunzionamenti più critici
     */
    public static function getCritici(int $limit = 10)
    {
        return self::critici()
            ->with(['prodotto', 'creatoBy'])
            ->ordinatoPerFrequenza()
            ->limit($limit)
            ->get();
    }

    /**
     * Ottiene i malfunzionamenti più frequenti
     */
    public static function getPiuFrequenti(int $limit = 10)
    {
        return self::ordinatoPerFrequenza()
            ->with(['prodotto'])
            ->limit($limit)
            ->get();
    }
}