<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabella dei prodotti dell'azienda
     * Contiene il catalogo completo con schede tecniche
     */
    public function up(): void
    {
        Schema::create('prodotti', function (Blueprint $table) {
            $table->id();
            
            // Informazioni base del prodotto
            $table->string('nome'); // Nome commerciale del prodotto
            $table->string('modello')->unique(); // Codice modello univoco
            $table->text('descrizione'); // Descrizione dettagliata per ricerche
            $table->string('categoria'); // Categoria: lavatrice, lavastoviglie, forno, etc.
            
            // Scheda tecnica completa
            $table->string('foto')->nullable(); // Path dell'immagine prodotto
            $table->text('note_tecniche'); // Specifiche tecniche dettagliate
            $table->text('modalita_installazione'); // Istruzioni di installazione
            $table->text('modalita_uso')->nullable(); // Istruzioni d'uso
            
            // Dati commerciali
            $table->decimal('prezzo', 10, 2)->nullable(); // Prezzo di listino
            $table->boolean('attivo')->default(true); // Prodotto ancora in catalogo
            
            // Gestione staff assegnato (funzionalità opzionale)
            $table->unsignedBigInteger('staff_assegnato_id')->nullable();
            
            $table->timestamps();
            
            // Indici per ottimizzare le ricerche
            $table->index('categoria'); // Ricerca per categoria
            $table->index('attivo'); // Filtro prodotti attivi
            $table->index('staff_assegnato_id'); // Prodotti per staff member
            
            // Indice full-text per ricerca nella descrizione (supporta wildcard)
            $table->fullText(['nome', 'descrizione', 'modello']);
        });
    }

    /**
     * Elimina la tabella prodotti
     */
    public function down(): void
    {
        Schema::dropIfExists('prodotti');
    }
};