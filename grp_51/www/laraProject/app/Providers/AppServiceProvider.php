<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Le policy mapping per l'applicazione
     * Associa modelli alle loro policy classes
     */
    protected $policies = [
        // Esempio: 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Registra i servizi di autenticazione/autorizzazione
     */
    public function boot(): void
    {

        // Registra il middleware personalizzato per controllo livelli utente
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('check.user.level', \App\Http\Middleware\CheckUserLevel::class);
        
        // Log per confermare la registrazione (solo in local)
        if ($this->app->environment('local')) {
            Log::info('Middleware check.user.level registrato con successo');
        }
        
        $this->registerPolicies();

        // === GATE PERSONALIZZATI PER I LIVELLI DI ACCESSO ===

        /**
         * Gate per visualizzare malfunzionamenti e soluzioni
         * Livello 2+: Tecnici, Staff, Admin
         */
        Gate::define('viewMalfunzionamenti', function (User $user) {
            return $user->canViewMalfunzionamenti();
        });

        /**
         * Gate per gestire malfunzionamenti (CRUD)
         * Livello 3+: Staff, Admin
         */
        Gate::define('manageMalfunzionamenti', function (User $user) {
            return $user->canManageMalfunzionamenti();
        });

        /**
         * Gate per gestire prodotti (CRUD)
         * Livello 4: Solo Admin
         */
        Gate::define('manageProdotti', function (User $user) {
            return $user->canManageProdotti();
        });

        /**
         * Gate per gestire utenti (CRUD)
         * Livello 4: Solo Admin
         */
        Gate::define('manageUsers', function (User $user) {
            return $user->canManageUsers();
        });

        /**
         * Gate per accedere alle funzioni di amministrazione
         * Livello 4: Solo Admin
         */
        Gate::define('accessAdmin', function (User $user) {
            return $user->isAdmin();
        });

        /**
         * Gate per accedere alle funzioni dello staff
         * Livello 3+: Staff, Admin
         */
        Gate::define('accessStaff', function (User $user) {
            return $user->isStaff() || $user->isAdmin();
        });

        /**
         * Gate per accedere alle funzioni dei tecnici
         * Livello 2+: Tecnici, Staff, Admin
         */
        Gate::define('accessTecnico', function (User $user) {
            return $user->isTecnico() || $user->isStaff() || $user->isAdmin();
        });

        // === GATE SPECIFICI PER FUNZIONALITÀ OPZIONALI ===

        /**
         * Gate per gestire centri di assistenza (funzionalità opzionale)
         * Solo Admin
         */
        Gate::define('manageCentriAssistenza', function (User $user) {
            return $user->isAdmin();
        });

        /**
         * Gate per gestire l'assegnazione dei prodotti allo staff (funzionalità opzionale)
         * Solo Admin può assegnare prodotti ai membri dello staff
         */
        Gate::define('assignProdotti', function (User $user) {
            return $user->isAdmin();
        });

        /**
         * Gate per visualizzare solo i propri prodotti assegnati (per staff)
         * Staff può vedere solo i prodotti a lui assegnati
         */
        Gate::define('viewAssignedProdotti', function (User $user, $prodotto = null) {
            if ($user->isAdmin()) {
                return true; // Admin vede tutto
            }
            
            if ($user->isStaff() && $prodotto) {
                return $prodotto->staff_assegnato_id === $user->id;
            }
            
            return false;
        });

        // === GATE PER AZIONI SPECIFICHE ===

        /**
         * Gate per modificare il proprio profilo
         * Ogni utente può modificare il proprio profilo
         */
        Gate::define('editOwnProfile', function (User $user, User $targetUser) {
            return $user->id === $targetUser->id;
        });

        /**
         * Gate per modificare qualsiasi profilo utente
         * Solo Admin
         */
        Gate::define('editAnyProfile', function (User $user) {
            return $user->isAdmin();
        });

        /**
         * Gate per eliminare utenti
         * Solo Admin, ma non può eliminare se stesso
         */
        Gate::define('deleteUser', function (User $user, User $targetUser) {
            return $user->isAdmin() && $user->id !== $targetUser->id;
        });

        /**
         * Gate per visualizzare informazioni sensibili degli utenti
         * Solo Admin e Staff (per i tecnici del proprio centro)
         */
        Gate::define('viewUserDetails', function (User $user, User $targetUser) {
            if ($user->isAdmin()) {
                return true;
            }
            
            if ($user->isStaff()) {
                return true; // Staff può vedere dettagli di tutti gli utenti
            }
            
            if ($user->isTecnico() && $targetUser->isTecnico()) {
                // Tecnici possono vedere altri tecnici dello stesso centro
                return $user->centro_assistenza_id === $targetUser->centro_assistenza_id;
            }
            
            return $user->id === $targetUser->id; // Può sempre vedere se stesso
        });

        /**
         * Gate per accedere alle statistiche avanzate
         * Staff e Admin
         */
        Gate::define('viewAdvancedStats', function (User $user) {
            return $user->isStaff() || $user->isAdmin();
        });

        /**
         * Gate per esportare dati
         * Solo Admin
         */
        Gate::define('exportData', function (User $user) {
            return $user->isAdmin();
        });

        // === GATE PER OPERAZIONI SUI MALFUNZIONAMENTI ===

        /**
         * Gate per incrementare le segnalazioni di un malfunzionamento
         * Tecnici, Staff e Admin
         */
        Gate::define('reportMalfunzionamento', function (User $user) {
            return $user->canViewMalfunzionamenti();
        });

        /**
         * Gate per modificare un malfunzionamento specifico
         * Staff che lo ha creato o Admin
         */
        Gate::define('editMalfunzionamento', function (User $user, $malfunzionamento) {
            if ($user->isAdmin()) {
                return true;
            }
            
            if ($user->isStaff()) {
                return $malfunzionamento->creato_da === $user->id;
            }
            
            return false;
        });

        // === GATE PER TESTING E DEBUG ===
        
        /**
         * Gate per accedere alle funzioni di debug
         * Solo in ambiente di sviluppo
         */
        Gate::define('accessDebug', function (User $user) {
            return app()->environment('local') && $user->isAdmin();
        });

        // === SUPER ADMIN GATE ===
        
        /**
         * Gate per azioni di super amministratore
         * Per operazioni particolarmente sensibili
         */
        Gate::define('superAdmin', function (User $user) {
            // Controlla se l'utente è l'admin principale (quello creato nel seeder)
            return $user->isAdmin() && $user->username === 'adminadmin';
        });
    }
}