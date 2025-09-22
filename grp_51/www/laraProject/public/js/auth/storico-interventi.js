/**
 * JAVASCRIPT PER STORICO INTERVENTI - AREA TECNICO
 * 
 * Linguaggio: JavaScript + jQuery
 * Framework: jQuery 3.x per manipolazione DOM e plugin personalizzati
 * Scopo: Gestione della vista storico interventi per utenti tecnici
 * 
 * Funzionalità principali:
 * - Filtri automatici per periodo, gravità e categoria
 * - Evidenziazione termini di ricerca nelle tabelle
 * - Tooltip informativi per elementi troncati
 * - Plugin personalizzato highlight per testo
 * - Integrazione con route Laravel per controllo pagina
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY (PRIMO LIVELLO)
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('storico.interventi caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     * 
     * NOTA: Route è 'tecnico.interventi' - area specifica per utenti tecnici
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'tecnico.interventi') {
        return; // Esce dalla funzione se non è la route corretta
    }
    
    /**
     * INIZIALIZZAZIONE DATI PAGINA
     * window.PageData è un oggetto globale popolato da Laravel con dati della pagina
     * Operatore || fornisce valore di default se PageData non esiste
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (uso futuro)
    
    // ===================================================================
    // SEZIONE: SECONDO DOCUMENT READY ANNIDATO (PATTERN NON OTTIMALE)
    // ===================================================================
    
    /**
     * EVENTO JQUERY ANNIDATO - DOCUMENT READY (SECONDO LIVELLO)
     * 
     * NOTA TECNICA: Questo è un anti-pattern JavaScript
     * Non è necessario annidare $(document).ready() dentro un altro
     * Il DOM è già pronto quando il primo ready si attiva
     * 
     * MIGLIORE PRATICA: Tutto il codice dovrebbe stare nel primo ready
     */
    $(document).ready(function() {
        // Log di debug per inizializzazione specifica storico
        console.log('Storico Interventi inizializzato');
        
        // ===================================================================
        // SEZIONE: TOOLTIP BOOTSTRAP
        // ===================================================================
        
        /**
         * INIZIALIZZAZIONE TOOLTIP BOOTSTRAP
         * jQuery: [data-bs-toggle="tooltip"] selettore attributo Bootstrap 5
         * Bootstrap: .tooltip() inizializza tooltip su hover
         * 
         * Uso: per elementi troncati che mostrano testo completo su hover
         * Esempio HTML: <span data-bs-toggle="tooltip" title="Testo completo">Testo tronc...</span>
         */
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // ===================================================================
        // SEZIONE: FILTRI AUTOMATICI (COMMENTATI)
        // ===================================================================
        
        /**
         * EVENT HANDLER: Auto-submit form per filtri
         * jQuery: selettore multiplo per campi filtro
         * .change() si attiva quando utente modifica selezione
         * 
         * Campi gestiti:
         * - #periodo: Filtra per periodo temporale (es: ultima settimana, mese)
         * - #gravita: Filtra per livello di gravità intervento (bassa, media, alta)
         * - #categoria: Filtra per categoria problema (elettrico, meccanico, ecc.)
         */
        $('#periodo, #gravita, #categoria').change(function() {
            /**
             * AUTO-SUBMIT COMMENTATO PER SCELTA DESIGN
             * jQuery: $(this).closest('form').submit() invierebbe form automaticamente
             * 
             * RATIONALE: Auto-submit può essere invasivo per UX
             * Utente potrebbe voler selezionare più filtri prima di applicare
             * Alternativa: pulsante "Applica Filtri" esplicito
             */
            // Uncomment per auto-submit: $(this).closest('form').submit();
        });
        
        // ===================================================================
        // SEZIONE: EVIDENZIAZIONE TERMINI RICERCA
        // ===================================================================
        
        /**
         * RECUPERO TERMINE RICERCA DA LARAVEL
         * Blade Laravel: {{ request("search") }} ottiene parametro GET 'search'
         * JavaScript: const per valore immutabile da template
         * 
         * PROCESSO:
         * 1. Laravel riceve richiesta con ?search=termine
         * 2. Blade interpola valore in JavaScript  
         * 3. JavaScript evidenzia termine nella tabella
         */
        const searchTerm = '{{ request("search") }}';
        
        /**
         * APPLICAZIONE EVIDENZIAZIONE CONDIZIONALE
         * JavaScript: if controlla se esiste termine di ricerca
         * jQuery: $('.table tbody') seleziona corpo tabella
         * .highlight() chiama plugin personalizzato definito sotto
         */
        if (searchTerm) {
            $('.table tbody').highlight(searchTerm);
        }
        
        // Log finale di conferma inizializzazione
        console.log('Storico interventi pronto');
    });
    
    // ===================================================================
    // SEZIONE: PLUGIN JQUERY PERSONALIZZATO
    // ===================================================================
    
    /**
     * PLUGIN JQUERY PERSONALIZZATO: highlight
     * jQuery: $.fn.highlight estende jQuery con metodo personalizzato
     * Scopo: Evidenzia occorrenze di testo cercato in elementi HTML
     * 
     * @param {string} text - Testo da evidenziare
     * @returns {jQuery} - Oggetto jQuery per method chaining
     * 
     * UTILIZZO: $('.elemento').highlight('termine');
     */
    $.fn.highlight = function(text) {
        /**
         * jQuery: return this.each() permette method chaining
         * .each() itera su ogni elemento nella selezione jQuery
         * Mantiene contesto jQuery per concatenare altre operazioni
         */
        return this.each(function() {
            /**
             * SOSTITUZIONE REGEX PER EVIDENZIAZIONE
             * $(this).html() ottiene HTML interno dell'elemento
             * .replace() con regex per sostituire occorrenze
             * 
             * REGEX BREAKDOWN:
             * - new RegExp() crea regex dinamica con variabile
             * - '(' + text + ')' gruppo di cattura per testo
             * - 'gi' flag: g=global (tutte occorrenze), i=ignoreCase
             * - '<mark>$1</mark>' sostituzione con tag HTML mark
             * - $1 riferisce al primo gruppo catturato (il testo)
             */
            $(this).html($(this).html().replace(
                new RegExp('(' + text + ')', 'gi'),
                '<mark>$1</mark>'
            ));
        });
    };
});