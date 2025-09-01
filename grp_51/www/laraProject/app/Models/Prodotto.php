<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Un prodotto può avere molti malfunzionamenti
     */
    public function malfunzionamenti(): HasMany
    {
        return $this->hasMany(Malfunzionamento::class, 'prodotto_id');
    }

    /**
     * Relazione con lo staff assegnato al prodotto
     * Un prodotto può essere assegnato a un membro dello staff (funzionalità opzionale)
     */
    public function staffAssegnato(): BelongsTo
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

        // Per debug, logga tutti i path possibili (solo in development)
        if (config('app.debug')) {
            \Log::debug('Tentativi path immagine', [
                'foto_field' => $this->foto,
                'standard_path' => $standardPath,
                'direct_path' => $directPath,
                'uploads_path' => $uploadsPath,
                'full_url' => $fullUrl
            ]);
        }

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
     * FIXED: Label formattata per la categoria - SISTEMA UNIFICATO
     * Ora usa il mapping unificato delle categorie
     */
    public function getCategoriaLabelAttribute(): string
    {
        return self::getCategorieUnifico()[$this->categoria] ?? ucfirst(str_replace('_', ' ', $this->categoria));
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
        // Se la relazione è già caricata, usa i dati in memoria
        if ($this->relationLoaded('malfunzionamenti')) {
            return $this->malfunzionamenti->count();
        }
        
        // Altrimenti esegui una query di conteggio
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

    /**
     * Ottiene il numero di malfunzionamenti critici
     */
    public function getMalfunzionamentiCriticiCountAttribute(): int
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->count();
    }

    /**
     * Ottiene il numero di malfunzionamenti di alta gravità
     */
    public function getMalfunzionamentiAltaCountAttribute(): int
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'alta')
            ->count();
    }

    /**
     * Categoria formattata per visualizzazione
     */
    public function getCategoriaFormattataAttribute(): string
    {
        return $this->categoria_label;
    }

    // ================================================
    // METODI HELPER PER MALFUNZIONAMENTI
    // ================================================

    /**
     * Controlla se il prodotto ha malfunzionamenti critici
     * Metodo utilizzato nelle viste per mostrare badge di avviso
     */
    public function hasMalfunzionamentiCritici(): bool
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'critica')
            ->exists();
    }

    /**
     * Controlla se il prodotto ha malfunzionamenti di alta gravità
     */
    public function hasMalfunzionamentiAlta(): bool
    {
        return $this->malfunzionamenti()
            ->where('gravita', 'alta')
            ->exists();
    }

    /**
     * Ottiene i malfunzionamenti più critici (massimo 5)
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
     * Ottiene i malfunzionamenti più segnalati (massimo 5)
     */
    public function getMalfunzionamentiFrequenti()
    {
        return $this->malfunzionamenti()
            ->orderBy('numero_segnalazioni', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Controlla se il prodotto è assegnato a uno staff
     */
    public function isAssegnato(): bool
    {
        return !is_null($this->staff_assegnato_id);
    }

    /**
     * Ottiene le statistiche complete del prodotto
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
            'staff_assegnato' => $this->staffAssegnato?->nome_completo,
            'categoria_formattata' => $this->categoria_formattata,
            'attivo' => $this->attivo
        ];
    }

    /**
     * Ottiene il badge di gravità per il prodotto
     * Basato sui malfunzionamenti più gravi presenti
     */
    public function getBadgeGravita(): array
    {
        if ($this->hasMalfunzionamentiCritici()) {
            return [
                'class' => 'bg-danger',
                'text' => 'Critico',
                'icon' => 'bi-exclamation-triangle-fill'
            ];
        }
        
        if ($this->hasMalfunzionamentiAlta()) {
            return [
                'class' => 'bg-warning',
                'text' => 'Alta',
                'icon' => 'bi-exclamation-triangle'
            ];
        }
        
        if ($this->totale_malfunzionamenti > 0) {
            return [
                'class' => 'bg-info',
                'text' => 'Media',
                'icon' => 'bi-info-circle'
            ];
        }
        
        return [
            'class' => 'bg-success',
            'text' => 'OK',
            'icon' => 'bi-check-circle'
        ];
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
     * Scope per ricerca con wildcard (supporta *)
     */
    public function scopeRicercaWildcard(Builder $query, string $termine): void
    {
        // Converte * in % per MySQL LIKE
        $pattern = str_replace('*', '%', $termine);
        
        $query->where(function($q) use ($pattern) {
            $q->where('nome', 'LIKE', $pattern)
              ->orWhere('modello', 'LIKE', $pattern)
              ->orWhere('descrizione', 'LIKE', $pattern);
        });
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

    /**
     * Scope per prodotti con malfunzionamenti critici
     */
    public function scopeConMalfunzionamentiCritici(Builder $query): void
    {
        $query->whereHas('malfunzionamenti', function($q) {
            $q->where('gravita', 'critica');
        });
    }

    /**
     * Scope per prodotti con molti malfunzionamenti
     */
    public function scopeConMoltiMalfunzionamenti(Builder $query, int $soglia = 5): void
    {
        $query->withCount('malfunzionamenti')
            ->having('malfunzionamenti_count', '>=', $soglia);
    }

    /**
     * Scope per prodotti per categoria
     */
    public function scopePerCategoria(Builder $query, string $categoria): void
    {
        $query->where('categoria', $categoria);
    }

    // ================================================
    // METODI STATICI UNIFICATI - SISTEMA PRINCIPALE
    // ================================================

    /**
     * SISTEMA UNIFICATO - Mappa definitiva delle categorie
     * Questo è l'unico metodo che definisce le categorie, tutte le altre funzioni dovranno usarlo
     * Basato sui dati del seeder e sulle categorie presenti nel database
     */
    public static function getCategorieUnifico(): array
    {
        return [
            // Categorie principali da seeder (corrispondono ai valori reali nel database)
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
            
            // Categorie aggiuntive supportate (possono essere create in futuro)
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
     * DEPRECATO - Mantiene compatibilità vecchio codice
     * @deprecated Utilizzare getCategorieUnifico() invece
     */
    public static function getCategorie(): array
    {
        return self::getCategorieUnifico();
    }

    /**
     * Ottiene le categorie presenti effettivamente nel database con conteggio
     * Utilizza il sistema unificato per le etichette
     */
    public static function getCategorieConConteggio(): array
    {
        try {
            // Ottiene le categorie dal sistema unificato
            $categorieComplete = self::getCategorieUnifico();
            
            // Conta i prodotti per categoria presenti nel database
            $prodottiPerCategoria = self::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray();

            // Costruisce il risultato solo per le categorie che hanno prodotti
            $result = [];
            foreach ($prodottiPerCategoria as $categoria => $count) {
                if ($count > 0) { // Solo categorie con prodotti
                    $result[$categoria] = [
                        'label' => $categorieComplete[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria)),
                        'count' => $count
                    ];
                }
            }

            // Ordina per nome categoria per consistenza UI
            ksort($result);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Errore nel calcolo categorie con conteggio', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Ottiene solo le categorie presenti nel database (senza conteggio)
     * Utile per i filtri dropdown
     */
    public static function getCategorieDisponibili(): array
    {
        try {
            $categoriePresenti = self::where('attivo', true)
                ->distinct()
                ->pluck('categoria')
                ->toArray();

            $categorieComplete = self::getCategorieUnifico();
            
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
     * Verifica se una categoria è valida
     */
    public static function isCategoriaValida(string $categoria): bool
    {
        return array_key_exists($categoria, self::getCategorieUnifico());
    }

    /**
     * Ottiene l'etichetta di una categoria
     */
    public static function getEtichettaCategoria(string $categoria): string
    {
        return self::getCategorieUnifico()[$categoria] ?? ucfirst(str_replace('_', ' ', $categoria));
    }

    /**
     * Ricerca prodotti con supporto wildcard
     */
    public static function ricercaAvanzata(string $termine, string $categoria = null): Builder
    {
        $query = self::where('attivo', true);

        // Applica ricerca testuale
        $query->ricerca($termine);

        // Filtra per categoria se specificata e valida
        if ($categoria && self::isCategoriaValida($categoria)) {
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
    // METODI PER L'API
    // ================================================

    /**
     * Trasforma il prodotto per le risposte API
     */
    public function toApiArray(bool $includeDetails = false): array
    {
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

    // ================================================
    // EVENTI DEL MODELLO
    // ================================================

    protected static function boot()
    {
        parent::boot();
        
        // Quando un prodotto viene eliminato, gestisci i malfunzionamenti
        static::deleting(function ($prodotto) {
            // Log dell'eliminazione
            \Log::info('Prodotto in eliminazione', [
                'prodotto_id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'malfunzionamenti_count' => $prodotto->malfunzionamenti()->count()
            ]);

            // Nota: I malfunzionamenti non vengono eliminati automaticamente
            // per preservare lo storico tecnico. Solo l'admin può decidere
            // se eliminare anche i malfunzionamenti associati
        });
        
        // Quando un prodotto viene creato
        static::created(function ($prodotto) {
            \Log::info('Nuovo prodotto creato', [
                'prodotto_id' => $prodotto->id,
                'nome' => $prodotto->nome,
                'categoria' => $prodotto->categoria,
                'created_by' => auth()->id() ?? 'Sistema'
            ]);
        });

        // Quando un prodotto viene aggiornato
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