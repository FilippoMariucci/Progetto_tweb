<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Prodotto extends Model
{
    use HasFactory;

    protected $table = 'prodotti';

    protected $fillable = [
        'nome',
        'modello', 
        'descrizione',
        'categoria',
        'note_tecniche',
        'modalita_installazione',
        'modalita_uso',
        'prezzo',
        'foto',
        'staff_assegnato_id',
        'attivo'
    ];

    protected $casts = [
        'prezzo' => 'decimal:2',
        'attivo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ================================================
    // RELAZIONI
    // ================================================

    /**
     * Relazione con i malfunzionamenti del prodotto
     */
    public function malfunzionamenti()
    {
        return $this->hasMany(Malfunzionamento::class, 'prodotto_id');
    }

    /**
     * Relazione con lo staff assegnato al prodotto
     */
    public function staffAssegnato()
    {
        return $this->belongsTo(User::class, 'staff_assegnato_id');
    }

    // ================================================
    // ACCESSORS (Attributi Calcolati)
    // ================================================

    /**
     * FIXED: Accessor per URL immagine prodotto
     * Gestisce diversi scenari di storage
     */
    public function getFotoUrlAttribute(): string
    {
        if (!$this->foto) {
            // Immagine placeholder se non presente
            return asset('images/prodotto-placeholder.jpg');
        }

        // === TENTATIVO 1: Storage Laravel standard ===
        $standardPath = asset('storage/' . $this->foto);
        
        // === TENTATIVO 2: Path diretto alla cartella storage ===
        $directPath = asset('storage/app/public/' . $this->foto);
        
        // === TENTATIVO 3: Path verso uploads pubblici ===
        $uploadsPath = asset('uploads/prodotti/' . basename($this->foto));
        
        // === TENTATIVO 4: URL completo con dominio ===
        $fullUrl = url('storage/' . $this->foto);

        // Per debug, logga tutti i path possibili
        \Log::debug('Tentativi path immagine', [
            'foto_field' => $this->foto,
            'standard_path' => $standardPath,
            'direct_path' => $directPath,
            'uploads_path' => $uploadsPath,
            'full_url' => $fullUrl
        ]);

        // Prova prima il path standard
        return $standardPath;
    }

    /**
     * Fallback: URL immagine con path alternativo
     */
    public function getFotoUrlAlternativeAttribute(): string
    {
        if (!$this->foto) {
            return asset('images/no-image.png');
        }

        // Prova path alternativi
        $paths = [
            'storage/' . $this->foto,
            'storage/app/public/' . $this->foto,
            'uploads/prodotti/' . basename($this->foto),
            'images/prodotti/' . basename($this->foto)
        ];

        foreach ($paths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                return asset($path);
            }
        }

        // Se nessun file trovato, restituisce placeholder
        return asset('images/no-image.png');
    }

    /**
     * Nome completo del prodotto (nome + modello)
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->nome . ' ' . $this->modello;
    }

    /**
     * Label formattata per la categoria
     */
    public function getCategoriaLabelAttribute(): string
    {
        $categorie = [
            'elettrodomestici' => 'Elettrodomestici',
            'climatizzazione' => 'Climatizzazione', 
            'cucina' => 'Cucina',
            'lavanderia' => 'Lavanderia',
            'riscaldamento' => 'Riscaldamento',
            'altro' => 'Altro'
        ];

        return $categorie[$this->categoria] ?? ucfirst($this->categoria);
    }

    /**
     * Prezzo formattato in Euro
     */
    public function getPrezzoFormattato(): string
    {
        if (!$this->prezzo) {
            return 'Prezzo non disponibile';
        }

        return '€ ' . number_format($this->prezzo, 2, ',', '.');
    }

    /**
     * Conta i malfunzionamenti totali per questo prodotto
     */
    public function getTotaleMalfunzionamentiAttribute(): int
    {
        return $this->malfunzionamenti()->count();
    }

    /**
     * Conta le segnalazioni totali per tutti i malfunzionamenti
     */
    public function getTotaleSegnalazioniAttribute(): int
    {
        return $this->malfunzionamenti()->sum('numero_segnalazioni') ?? 0;
    }

    /**
     * Ottiene i malfunzionamenti ordinati per gravità e frequenza
     */
    public function getMalfunzionamentiOrdinatiAttribute()
    {
        return $this->malfunzionamenti()
            ->orderByRaw("FIELD(gravita, 'critica', 'alta', 'media', 'bassa')")
            ->orderBy('numero_segnalazioni', 'desc')
            ->get();
    }

    // ================================================
    // SCOPE (Filtri per Query)
    // ================================================

    /**
     * Scope per prodotti attivi
     */
    public function scopeAttivi(Builder $query): void
    {
        $query->where('attivo', true);
    }

    /**
     * Scope per prodotti di una categoria specifica
     */
    public function scopeCategoria(Builder $query, string $categoria): void
    {
        $query->where('categoria', $categoria);
    }

    /**
     * Scope per ricerca testuale
     */
    public function scopeRicerca(Builder $query, string $termine): void
    {
        // Implementa wildcard search
        if (str_ends_with($termine, '*')) {
            $termine = rtrim($termine, '*');
            $query->where(function($q) use ($termine) {
                $q->where('nome', 'LIKE', $termine . '%')
                  ->orWhere('descrizione', 'LIKE', $termine . '%')
                  ->orWhere('modello', 'LIKE', $termine . '%');
            });
        } else {
            $query->where(function($q) use ($termine) {
                $q->where('nome', 'LIKE', '%' . $termine . '%')
                  ->orWhere('descrizione', 'LIKE', '%' . $termine . '%')
                  ->orWhere('modello', 'LIKE', '%' . $termine . '%');
            });
        }
    }

    /**
     * Scope per prodotti assegnati a uno staff specifico
     */
    public function scopeAssegnatiA(Builder $query, int $staffId): void
    {
        $query->where('staff_assegnato_id', $staffId);
    }

    /**
     * Scope per prodotti non assegnati
     */
    public function scopeNonAssegnati(Builder $query): void
    {
        $query->whereNull('staff_assegnato_id');
    }

    // ================================================
    // METODI STATICI
    // ================================================

    /**
     * Ottiene tutte le categorie disponibili
     */
    public static function getCategorie(): array
    {
        return [
            'elettrodomestici' => 'Elettrodomestici',
            'climatizzazione' => 'Climatizzazione',
            'cucina' => 'Cucina', 
            'lavanderia' => 'Lavanderia',
            'riscaldamento' => 'Riscaldamento',
            'altro' => 'Altro'
        ];
    }

    /**
     * Ottiene le categorie con conteggio prodotti
     */
    public static function getCategorieConConteggio(): array
    {
        try {
            $categorie = self::getCategorie();
            
            // Conta i prodotti per categoria
            $prodottiPerCategoria = self::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray();

            // Unisci con le etichette
            $result = [];
            foreach ($categorie as $key => $label) {
                $result[$key] = [
                    'label' => $label,
                    'count' => $prodottiPerCategoria[$key] ?? 0
                ];
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Errore nel calcolo categorie con conteggio', [
                'error' => $e->getMessage()
            ]);

            // Fallback
            return array_map(function($label) {
                return ['label' => $label, 'count' => 0];
            }, self::getCategorie());
        }
    }

    /**
     * Ricerca prodotti con supporto wildcard
     */
    public static function ricercaAvanzata(string $termine, string $categoria = null): Builder
    {
        $query = self::where('attivo', true);

        // Applica ricerca testuale
        $query->ricerca($termine);

        // Filtra per categoria se specificata
        if ($categoria) {
            $query->categoria($categoria);
        }

        return $query;
    }

    // ================================================
    // METODI PER GESTIONE FILE
    // ================================================

    /**
     * Salva l'immagine del prodotto in modo sicuro
     */
    public function salvaImmagine($file): string
    {
        try {
            // Genera nome file univoco
            $filename = time() . '_' . $this->id . '.' . $file->getClientOriginalExtension();
            
            // Prova diversi metodi di storage
            $methods = [
                'storage_public' => function() use ($file, $filename) {
                    return $file->storeAs('prodotti', $filename, 'public');
                },
                'public_uploads' => function() use ($file, $filename) {
                    $path = public_path('uploads/prodotti');
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    $file->move($path, $filename);
                    return 'uploads/prodotti/' . $filename;
                },
                'public_images' => function() use ($file, $filename) {
                    $path = public_path('images/prodotti');
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    $file->move($path, $filename);
                    return 'images/prodotti/' . $filename;
                }
            ];

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
                    continue;
                }
            }

            throw new \Exception('Tutti i metodi di storage sono falliti');

        } catch (\Exception $e) {
            \Log::error('Errore nel salvataggio immagine', [
                'error' => $e->getMessage(),
                'prodotto_id' => $this->id
            ]);
            
            throw $e;
        }
    }

    /**
     * Elimina l'immagine del prodotto
     */
    public function eliminaImmagine(): bool
    {
        if (!$this->foto) {
            return true;
        }

        try {
            $paths = [
                storage_path('app/public/' . $this->foto),
                public_path('storage/' . $this->foto),
                public_path($this->foto)
            ];

            $deleted = false;
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    unlink($path);
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
    // METODI PER DEBUGGING
    // ================================================

    /**
     * Debug: verifica esistenza file immagine
     */
    public function verificaImmagine(): array
    {
        if (!$this->foto) {
            return ['exists' => false, 'reason' => 'No image set'];
        }

        $paths = [
            'storage_public' => storage_path('app/public/' . $this->foto),
            'public_storage' => public_path('storage/' . $this->foto),
            'public_direct' => public_path($this->foto),
            'public_uploads' => public_path('uploads/prodotti/' . basename($this->foto)),
            'public_images' => public_path('images/prodotti/' . basename($this->foto))
        ];

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
}