<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

// IMPORT DEI MODEL NECESSARI
use App\Models\CentroAssistenza;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   /**
     * Campi che possono essere assegnati in massa
     */
    protected $fillable = [
        'nome',
        'cognome',
        'username',
        'email',
        'password',
        'livello_accesso',
        'data_nascita',
        'specializzazione',
        'centro_assistenza_id',
        'assigned_at',  // AGGIUNTO: timestamp assegnazione centro
    ];

     /**
     * Cast automatici per i tipi di dato
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'data_nascita' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'assigned_at' => 'datetime',  // AGGIUNTO: cast per assigned_at
        'last_login_at' => 'datetime',
    ];

    /**
     * Campi che devono essere nascosti nelle serializzazioni
     * La password non deve mai essere esposta nelle API
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast automatici per i campi
     * Laravel convertirà automaticamente i tipi
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Hash automatico della password
            'data_nascita' => 'date',
        ];
    }

    // ================================================
    // RELAZIONI ELOQUENT
    // ================================================

    /**
     * Relazione con il centro di assistenza
     * Un tecnico appartiene a un centro
     */
    public function centroAssistenza()
    {
        return $this->belongsTo(CentroAssistenza::class, 'centro_assistenza_id');
    }

    /**
     * Relazione con i prodotti assegnati (per staff)
     * Funzionalità opzionale: staff gestisce subset di prodotti
     */
    public function prodottiAssegnati()
    {
        return $this->hasMany(Prodotto::class, 'staff_assegnato_id');
    }

    /**
     * Malfunzionamenti creati da questo utente
     */
    public function malfunzionamentiCreati()
    {
        return $this->hasMany(Malfunzionamento::class, 'creato_da');
    }

    /**
     * Malfunzionamenti modificati da questo utente
     */
    public function malfunzionamentiModificati()
    {
        return $this->hasMany(Malfunzionamento::class, 'modificato_da');
    }

    // ================================================
    // METODI PER CONTROLLO AUTORIZZAZIONI
    // ================================================

    /**
     * Verifica se l'utente è un amministratore (livello 4)
     */
    public function isAdmin(): bool
    {
        return $this->livello_accesso === '4';
    }
    
    /**
     * Verifica se l'utente è dello staff aziendale (livello 3)
     */
    public function isStaff(): bool
    {
        return $this->livello_accesso === '3';
    }

    /**
     * Verifica se l'utente è un tecnico (livello 2)
     */
    public function isTecnico(): bool
    {
        return $this->livello_accesso === '2';
    }

    /**
     * Verifica se l'utente può vedere i malfunzionamenti
     * Solo tecnici (livello 2+) possono vedere le soluzioni
     */
    public function canViewMalfunzionamenti(): bool
    {
        return in_array($this->livello_accesso, ['2', '3', '4']);
    }

    /**
     * Verifica se l'utente può modificare malfunzionamenti
     * Solo staff (livello 3+) può gestire le soluzioni
     */
    public function canManageMalfunzionamenti(): bool
    {
        return in_array($this->livello_accesso, ['3', '4']);
    }

    /**
     * Verifica se l'utente può gestire prodotti
     * Solo admin (livello 4) può gestire il catalogo
     */
    public function canManageProdotti(): bool
    {
        return $this->livello_accesso === '4';
    }

    /**
     * Verifica se l'utente può gestire altri utenti
     * Solo admin può creare/modificare/eliminare utenti
     */
    public function canManageUsers(): bool
    {
        return $this->livello_accesso === '4';
    }

    // ================================================
    // ACCESSORS (Attributi Calcolati)
    // ================================================

    /**
     * Ottiene il nome completo dell'utente
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->nome . ' ' . $this->cognome;
    }

    /**
     * Ottiene la descrizione del livello di accesso
     */
    public function getLivelloDescrizioneAttribute(): string
    {
        return match($this->livello_accesso) {
            '1' => 'Utente Pubblico',
            '2' => 'Tecnico',
            '3' => 'Staff Aziendale',
            '4' => 'Amministratore',
            default => 'Non definito'
        };
    }

    /**
     * Ottiene la classe CSS per il badge del ruolo
     */
    public function getRuoloBadgeClassAttribute(): string
    {
        return match($this->livello_accesso) {
            '1' => 'bg-secondary',
            '2' => 'bg-info',
            '3' => 'bg-warning text-dark',
            '4' => 'bg-danger',
            default => 'bg-dark'
        };
    }

    // ================================================
    // METODI UTILI
    // ================================================

    /**
     * Ottiene statistiche personalizzate per l'utente
     * Utilizzato nella dashboard per mostrare dati rilevanti per ogni ruolo
     */
    public function getStats(): array
    {
        $stats = [];

        try {
            if ($this->isAdmin()) {
                // Statistiche per amministratori: panoramica completa del sistema
                $stats = [
                    'total_utenti' => User::count(),
                    'total_prodotti' => Prodotto::count(),
                    'total_malfunzionamenti' => Malfunzionamento::count(),
                    'total_centri' => CentroAssistenza::count(),
                    'utenti_per_livello' => User::selectRaw('livello_accesso, COUNT(*) as count')
                        ->groupBy('livello_accesso')
                        ->pluck('count', 'livello_accesso')
                        ->toArray(),
                ];
            } elseif ($this->isStaff()) {
                // Statistiche per staff: focus sui propri prodotti e malfunzionamenti
                $stats = [
                    'prodotti_assegnati' => $this->prodottiAssegnati()->count(),
                    'malfunzionamenti_creati' => $this->malfunzionamentiCreati()->count(),
                    'malfunzionamenti_modificati' => $this->malfunzionamentiModificati()->count(),
                    'ultimi_prodotti' => $this->prodottiAssegnati()
                        ->latest()
                        ->limit(3)
                        ->get(['id', 'nome', 'categoria']),
                ];
            } elseif ($this->isTecnico()) {
                // Statistiche per tecnici: focus sui malfunzionamenti e centro assistenza
                $stats = [
                    'centro_assistenza' => $this->centroAssistenza?->nome ?? 'Non assegnato',
                    'specializzazione' => $this->specializzazione ?? 'Non specificata',
                    'malfunzionamenti_critici' => Malfunzionamento::where('gravita', 'critica')->count(),
                    'malfunzionamenti_totali' => Malfunzionamento::count(),
                ];
            } else {
                // Statistiche per utenti pubblici: informazioni generali
                $stats = [
                    'prodotti_disponibili' => Prodotto::where('attivo', true)->count(),
                    'centri_assistenza' => CentroAssistenza::count(),
                    'categorie_prodotti' => Prodotto::distinct('categoria')->count('categoria'),
                ];
            }
        } catch (\Exception $e) {
            // Fallback se ci sono errori con i Model
            Log::warning('Errore nel calcolo statistiche utente', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            
            $stats = [
                'errore' => 'Impossibile caricare le statistiche',
                'livello_utente' => $this->livello_descrizione
            ];
        }

        return $stats;
    }

    /**
     * Verifica se l'utente ha un livello minimo di accesso
     */
    public function hasMinimumLevel(int $minLevel): bool
    {
        return (int)$this->livello_accesso >= $minLevel;
    }

    /**
     * Ottiene il colore associato alla gravità per interfacce
     */
    public function getGravitaColor(string $gravita): string
    {
        return match($gravita) {
            'critica' => 'danger',
            'alta' => 'warning',
            'media' => 'info', 
            'bassa' => 'success',
            default => 'secondary'
        };
    }
}