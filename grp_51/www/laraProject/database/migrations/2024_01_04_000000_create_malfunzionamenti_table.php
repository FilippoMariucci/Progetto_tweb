<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabella dei malfunzionamenti
     * Ogni malfunzionamento è associato a un prodotto specifico
     */
    public function up(): void
    {
        Schema::create('malfunzionamenti', function (Blueprint $table) {
            $table->id();
            
            // Collegamento al prodotto
            $table->unsignedBigInteger('prodotto_id');
            
            // Descrizione del problema
            $table->string('titolo'); // Titolo breve del malfunzionamento
            $table->text('descrizione'); // Descrizione dettagliata del problema
            $table->enum('gravita', ['bassa', 'media', 'alta', 'critica'])->default('media');
            
            // Soluzione tecnica associata
            $table->text('soluzione'); // Procedura di risoluzione step-by-step
            $table->text('strumenti_necessari')->nullable(); // Attrezzi richiesti
            $table->integer('tempo_stimato')->nullable(); // Tempo riparazione in minuti
            $table->enum('difficolta', ['facile', 'media', 'difficile', 'esperto'])->default('media');
            
            // Tracking delle segnalazioni
            $table->integer('numero_segnalazioni')->default(1); // Quante volte è stato riscontrato
            $table->date('prima_segnalazione'); // Prima volta che si è verificato
            $table->date('ultima_segnalazione'); // Ultima segnalazione
            
            // Chi ha inserito/modificato la soluzione
            $table->unsignedBigInteger('creato_da'); // Staff member che ha creato
            $table->unsignedBigInteger('modificato_da')->nullable(); // Ultimo che ha modificato
            
            $table->timestamps();
            
            // Chiavi esterne
            $table->foreign('prodotto_id')->references('id')->on('prodotti')->onDelete('cascade');
            $table->foreign('creato_da')->references('id')->on('users');
            $table->foreign('modificato_da')->references('id')->on('users');
            
            // Indici per performance
            $table->index('prodotto_id');
            $table->index('gravita');
            $table->index('numero_segnalazioni');
            
            // Ricerca full-text nelle descrizioni
            $table->fullText(['titolo', 'descrizione']);
        });
    }

    /**
     * Elimina la tabella malfunzionamenti
     */
    public function down(): void
    {
        Schema::dropIfExists('malfunzionamenti');
    }
};