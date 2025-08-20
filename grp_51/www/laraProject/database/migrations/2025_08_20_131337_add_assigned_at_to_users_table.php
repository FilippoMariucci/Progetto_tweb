<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration per aggiungere il campo assigned_at alla tabella users
 * 
 * Questo campo traccia quando un tecnico è stato assegnato al centro attuale
 * Si aggiorna ogni volta che il tecnico viene assegnato a un nuovo centro
 * 
 * Comando per creare: php artisan make:migration add_assigned_at_to_users_table
 * Comando per eseguire: php artisan migrate
 */
return new class extends Migration
{
    /**
     * Esegue la migration (aggiunge il campo assigned_at)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            /**
             * Timestamp di quando il tecnico è stato assegnato al centro attuale
             * - Si aggiorna ogni volta che centro_assistenza_id cambia
             * - Utile per sapere da quanto tempo un tecnico lavora in un centro
             * - Nullable perché non tutti gli utenti sono tecnici
             */
            $table->timestamp('assigned_at')->nullable()->after('centro_assistenza_id');
            
            /**
             * Indice per ottimizzare query sui tecnici assegnati di recente
             */
            $table->index('assigned_at', 'idx_assigned_at');
        });
    }

    /**
     * Rollback della migration (rimuove il campo)
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // Rimuove l'indice
            $table->dropIndex('idx_assigned_at');
            
            // Rimuove il campo
            $table->dropColumn('assigned_at');
        });
    }
};