<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegue la migrazione per creare la tabella users
     * Gestisce i 4 livelli di utenti: admin, staff, tecnico, pubblico
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Dati comuni a tutti gli utenti
            $table->string('username')->unique(); // Username univoco (no email come richiesto)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Password hashata
            $table->string('nome'); // Nome dell'utente
            $table->string('cognome'); // Cognome dell'utente
            
            // Definisce il livello di accesso: 1=pubblico, 2=tecnico, 3=staff, 4=admin
            $table->enum('livello_accesso', ['1', '2', '3', '4'])->default('1');
            
            // Campi specifici per i tecnici (livello 2)
            $table->date('data_nascita')->nullable(); // Data di nascita del tecnico
            $table->string('specializzazione')->nullable(); // Area di specializzazione
            $table->unsignedBigInteger('centro_assistenza_id')->nullable(); // Riferimento al centro
            
            // Timestamps Laravel per created_at e updated_at
            $table->timestamps();
            $table->rememberToken(); // Token per "ricordami"
            
            // Indice per ottimizzare le query per livello di accesso
            $table->index('livello_accesso');
            // Indice per le ricerche per centro assistenza
            $table->index('centro_assistenza_id');
        });
    }

    /**
     * Annulla la migrazione eliminando la tabella users
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
