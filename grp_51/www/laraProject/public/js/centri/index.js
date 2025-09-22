/**
 * JAVASCRIPT PER LISTA CENTRI ASSISTENZA - AREA PUBBLICA
 * 
 * Linguaggio: JavaScript + jQuery
 * Framework: jQuery 3.x per manipolazione DOM e eventi
 * Scopo: Gestione della pagina pubblica di ricerca centri di assistenza
 * 
 * Funzionalità principali:
 * - Validazione form di ricerca con controlli multipli
 * - Ricerca in tempo reale con debouncing (commentata)
 * - Tracking analytics per interazioni utente
 * - Gestione responsive per dispositivi mobili
 * - Tooltip informativi per badge stato
 * - Monitoraggio comunicazioni (telefono/email)
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('centri.index caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     * 
     * NOTA: Route 'centri.index' è PUBBLICA (non richiede autenticazione)
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'centri.index') {
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
    // SEZIONE: DEBUG E LOGGING INIZIALE
    // ===================================================================
    
    // Log di debug per il caricamento della pagina
    console.log('Pagina centri di assistenza caricata');
    
    /**
     * LOG CONTEGGIO CENTRI DA LARAVEL
     * Blade Laravel: {{ isset($centri) ? $centri->count() : 0 }}
     * Verifica esistenza collection e conta elementi
     * isset() PHP verifica se variabile esiste, ->count() metodo Eloquent
     */
    console.log('Centri trovati: {{ isset($centri) ? $centri->count() : 0 }}');
    
    // ===================================================================
    // SEZIONE: GESTIONE FORM DI RICERCA CON VALIDAZIONE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Submit del form di ricerca
     * jQuery: .on('submit') cattura invio del form
     * Implementa validazione lato client prima dell'invio
     */
    $('#searchForm').on('submit', function(e) {
        /**
         * RACCOLTA VALORI DAI CAMPI FORM
         * jQuery: .val() ottiene valore campo
         * .trim() rimuove spazi vuoti all'inizio e fine
         */
        const searchTerm = $('#search').val().trim();      // Campo ricerca libera
        const provincia = $('#provincia').val();           // Select provincia
        const citta = $('#citta').val().trim();           // Campo città
        
        /**
         * VALIDAZIONE: Almeno un criterio obbligatorio
         * JavaScript: && operatore AND logico
         * ! operatore NOT per verificare valori vuoti
         */
        if (!searchTerm && !provincia && !citta) {
            /**
             * jQuery: e.preventDefault() blocca submit del form
             * JavaScript: alert() mostra messaggio nativo browser
             * return false ferma completamente l'esecuzione
             */
            e.preventDefault();
            alert('Inserisci almeno un criterio di ricerca');
            return false;
        }
        
        /**
         * LOG PER DEBUG PARAMETRI RICERCA
         * JavaScript: oggetto per raggruppare parametri logicamente
         * Console.log per verificare dati inviati al server
         */
        console.log('Form sottomesso con parametri:', {
            search: searchTerm,
            provincia: provincia,
            citta: citta
        });
    });
    
    // ===================================================================
    // SEZIONE: DEBUGGING E VALIDAZIONE ELEMENTI DOM
    // ===================================================================
    
    /**
     * VERIFICA PRESENZA ELEMENTI DOM CRITICI
     * jQuery: .length restituisce numero di elementi trovati
     * 0 = elemento non trovato, >0 = elemento presente
     * 
     * UTILITÀ: Debug per verificare che HTML contenga elementi necessari
     */
    console.log('Elementi form presenti:', {
        form: $('#searchForm').length,           // Form principale
        searchInput: $('#search').length,        // Campo ricerca testuale
        provinciaSelect: $('#provincia').length, // Select lista province
        cittaInput: $('#citta').length          // Campo input città
    });
    
    // ===================================================================
    // SEZIONE: RICERCA IN TEMPO REALE CON DEBOUNCING (COMMENTATA)
    // ===================================================================
    
    /**
     * VARIABILE PER DEBOUNCING
     * JavaScript: let per variabile riassegnabile
     * Usata per gestire timer e evitare ricerche eccessive
     */
    let searchTimer;
    
    /**
     * EVENT HANDLER: Ricerca in tempo reale
     * jQuery: .on('input') si attiva ad ogni carattere digitato
     * Implementa debouncing per limitare chiamate server
     */
    $('#search').on('input', function() {
        /**
         * CANCELLAZIONE TIMER PRECEDENTE (DEBOUNCING)
         * JavaScript: clearTimeout() ferma timer attivo
         * Evita ricerca multipla durante digitazione veloce
         */
        clearTimeout(searchTimer);
        const searchTerm = $(this).val(); // Valore corrente campo ricerca
        
        /**
         * SOGLIA MINIMA PER RICERCA
         * Avvia ricerca solo per termini di almeno 3 caratteri
         * Evita ricerche troppo generiche o incomplete
         */
        if (searchTerm.length >= 3) {
            /**
             * TIMER CON DELAY PER DEBOUNCING
             * JavaScript: setTimeout() ritarda esecuzione di 1 secondo
             * Aspetta pausa nella digitazione prima di cercare
             */
            searchTimer = setTimeout(function() {
                console.log('Ricerca per:', searchTerm);
                
                /**
                 * RICERCA AUTOMATICA COMMENTATA
                 * Potrebbe essere abilitata per ricerca live
                 * Attualmente disabilitata per scelta UX
                 */
                // Esempio di ricerca automatica (commentato per ora):
                // $('#searchForm').submit();
            }, 1000); // 1000ms = 1 secondo di delay
        }
    });
    
    // ===================================================================
    // SEZIONE: ANALYTICS E TRACKING INTERAZIONI
    // ===================================================================
    
    /**
     * EVENT HANDLER: Tracking visualizzazione centri
     * jQuery: $('.card') seleziona tutte le card dei centri
     * .on('click') cattura click su qualsiasi card
     * 
     * UTILITÀ: Analytics per monitorare centri più consultati
     */
    $('.card').on('click', function() {
        /**
         * ESTRAZIONE NOME CENTRO DALLA CARD
         * jQuery: $(this) card cliccata
         * .find('.card-title') cerca elemento titolo dentro la card
         * .text().trim() ottiene testo pulito senza spazi
         */
        const centerName = $(this).find('.card-title').text().trim();
        console.log('Centro visualizzato:', centerName);
        
        /**
         * PLACEHOLDER PER ANALYTICS
         * Qui si potrebbero inviare dati a Google Analytics,
         * sistemi di tracking personalizzati, etc.
         */
        // Qui potresti inviare dati analytics
    });
    
    // ===================================================================
    // SEZIONE: TRACKING COMUNICAZIONI
    // ===================================================================
    
    /**
     * EVENT HANDLER: Tracking chiamate telefoniche
     * jQuery: $('a[href^="tel:"]') selettore attributo per link telefono
     * href^="tel:" significa "href che inizia con tel:"
     * 
     * UTILITÀ: Monitorare quante chiamate vengono avviate
     */
    $('a[href^="tel:"]').on('click', function() {
        /**
         * jQuery: $(this).attr('href') ottiene valore attributo href
         * Log del numero chiamato per statistiche
         */
        console.log('Chiamata avviata:', $(this).attr('href'));
    });
    
    /**
     * EVENT HANDLER: Tracking apertura email
     * jQuery: $('a[href^="mailto:"]') selettore per link email
     * href^="mailto:" significa "href che inizia con mailto:"
     * 
     * UTILITÀ: Monitorare interazioni email con centri
     */
    $('a[href^="mailto:"]').on('click', function() {
        /**
         * jQuery: $(this).attr('href') ottiene indirizzo email
         * Log dell'email aperta per statistiche comunicazioni
         */
        console.log('Email aperta:', $(this).attr('href'));
    });
    
    // ===================================================================
    // SEZIONE: TOOLTIP PER INTERFACCIA UTENTE
    // ===================================================================
    
    /**
     * INIZIALIZZAZIONE TOOLTIP BOOTSTRAP
     * jQuery: $('[title]') selettore attributo per elementi con title
     * Bootstrap: .tooltip() inizializza tooltip su hover
     * 
     * UTILITÀ: Badge stato tecnici con informazioni aggiuntive
     * Esempio: <span title="5 tecnici attivi">Badge</span>
     */
    $('[title]').tooltip();
    
    // ===================================================================
    // SEZIONE: GESTIONE RESPONSIVE DESIGN
    // ===================================================================
    
    /**
     * FUNZIONE: Adattamento layout responsive
     * JavaScript: function per logica riutilizzabile
     * Modifica elementi CSS in base alla larghezza schermo
     */
    function handleResponsive() {
        /**
         * jQuery: $(window).width() ottiene larghezza viewport
         * 768px è il breakpoint standard Bootstrap per tablet
         */
        if ($(window).width() < 768) {
            /**
             * LAYOUT MOBILE: Badge più compatti
             * jQuery: .removeClass() rimuove classe, .addClass() aggiunge
             * Bootstrap: fs-6 (font-size 6) -> small (dimensione ridotta)
             */
            $('.badge.fs-6').removeClass('fs-6').addClass('small');
        } else {
            /**
             * LAYOUT DESKTOP: Ripristina dimensioni normali
             * Operazione inversa per schermi più grandi
             */
            $('.badge.small').removeClass('small').addClass('fs-6');
        }
    }
    
    /**
     * INIZIALIZZAZIONE RESPONSIVE
     * Esegue adattamento al caricamento pagina
     */
    handleResponsive();
    
    /**
     * EVENT HANDLER: Adattamento al ridimensionamento
     * jQuery: $(window).resize() cattura cambi dimensione finestra
     * Riesegue adattamento quando utente ridimensiona browser
     */
    $(window).resize(handleResponsive);
});