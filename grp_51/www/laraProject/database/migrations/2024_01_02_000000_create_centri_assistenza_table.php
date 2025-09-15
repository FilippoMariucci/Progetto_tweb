<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabella per i centri di assistenza tecnica
     * Ogni centro ha tecnici associati e informazioni di contatto
     */
    public function up(): void
    {
        Schema::create('centri_assistenza', function (Blueprint $table) {
            $table->id();
            
            // Informazioni principali del centro
            $table->string('nome'); // Nome del centro assistenza
            $table->text('indirizzo'); // Indirizzo completo del centro
            $table->string('citta'); // Città dove si trova
            $table->string('provincia', 2); // Sigla provincia (es: AN, RM)
            $table->string('cap', 5); // Codice avviamento postale
            $table->string('telefono')->nullable(); // Numero di telefono
            $table->string('email')->nullable(); // Email del centro
            
            // Timestamps per tracciare creazione e modifiche
            $table->timestamps();
            
            // Indici per ottimizzare le ricerche geografiche
            $table->index('citta');
            $table->index('provincia');
        });
    }

    /**
     * Elimina la tabella centri_assistenza
     */
    public function down(): void
    {
        Schema::dropIfExists('centri_assistenza');
    }
};