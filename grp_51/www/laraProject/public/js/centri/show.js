/**
 * JAVASCRIPT PER DETTAGLIO CENTRO ASSISTENZA - AREA PUBBLICA
 * 
 * Linguaggio: JavaScript + jQuery
 * Framework: jQuery 3.x per manipolazione DOM e animazioni
 * Scopo: Gestione della pagina di dettaglio di un singolo centro assistenza
 * 
 * Funzionalità principali:
 * - Tracking avanzato delle interazioni utente per analytics
 * - Smooth scrolling per navigazione interna fluida
 * - Auto-dismiss automatico per alert temporanei
 * - Monitoraggio comunicazioni (telefono, email, mappe)
 * - Integrazione pronta per Google Analytics/Tag Manager
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('centri.show caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     * 
     * NOTA: Route 'centri.show' è PUBBLICA per dettaglio centro specifico
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'centri.show') {
        return; // Esce dalla funzione se non è la route corretta
    }
    
    /**
     * INIZIALIZZAZIONE DATI PAGINA
     * window.PageData è un oggetto globale popolato da Laravel con dati della pagina
     * Operatore || fornisce valore di default se PageData non esiste
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (uso futuro)
    
    /**
     * LOG CENTRO SPECIFICO
     * Blade Laravel: {{ $centro->nome }} interpola nome centro dal model
     * Utile per debug e identificazione centro nelle analytics
     */
    console.log('Vista centro assistenza caricata:', '{{ $centro->nome }}');
    
    // ===================================================================
    // SEZIONE: TRACKING CHIAMATE TELEFONICHE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Tracking chiamate telefoniche
     * jQuery: $('a[href^="tel:"]') selettore attributo per link telefono
     * href^="tel:" significa "href che inizia con tel:"
     * 
     * BUSINESS VALUE: Monitorare conversioni da web a chiamata
     */
    $('a[href^="tel:"]').on('click', function() {
        /**
         * ESTRAZIONE NUMERO TELEFONO PULITO
         * jQuery: $(this).attr('href') ottiene valore href completo
         * .replace('tel:', '') rimuove prefisso per ottenere solo numero
         */
        const numero = $(this).attr('href').replace('tel:', '');
        console.log('Chiamata avviata verso:', numero);
        
        /**
         * PLACEHOLDER GOOGLE ANALYTICS
         * gtag() è la funzione Google Analytics 4
         * Event tracking per conversioni business-critical
         * 
         * COMMENTO: Implementazione pronta ma disabilitata
         * Per abilitare: decommentare e configurare GA4
         */
        // Potresti inviare un evento analytics qui
        // gtag('event', 'phone_call', {
        //     centro_id: {{ $centro->id }},
        //     centro_nome: '{{ $centro->nome }}',
        //     numero: numero
        // });
    });
    
    // ===================================================================
    // SEZIONE: TRACKING EMAIL
    // ===================================================================
    
    /**
     * EVENT HANDLER: Tracking apertura email
     * jQuery: $('a[href^="mailto:"]') selettore per link email
     * href^="mailto:" significa "href che inizia con mailto:"
     * 
     * BUSINESS VALUE: Monitorare preferenze di comunicazione utenti
     */
    $('a[href^="mailto:"]').on('click', function() {
        /**
         * ESTRAZIONE INDIRIZZO EMAIL PULITO
         * jQuery: $(this).attr('href') ottiene href completo
         * .replace('mailto:', '') rimuove prefisso per email pulita
         */
        const email = $(this).attr('href').replace('mailto:', '');
        console.log('Email aperta verso:', email);
        
        /**
         * PLACEHOLDER GOOGLE ANALYTICS PER EMAIL
         * Event tracking per interazioni email
         * Blade Laravel: {{ $centro->id }} interpola ID centro
         */
        // Analytics per email
        // gtag('event', 'email_click', {
        //     centro_id: {{ $centro->id }},
        //     email: email
        // });
    });
    
    // ===================================================================
    // SEZIONE: TRACKING GOOGLE MAPS
    // ===================================================================
    
    /**
     * EVENT HANDLER: Tracking apertura Google Maps
     * jQuery: $('a[href*="google.com/maps"]') selettore per link mappe
     * href*="testo" significa "href che contiene testo"
     * 
     * BUSINESS VALUE: Monitorare richieste di indicazioni stradali
     */
    $('a[href*="google.com/maps"]').on('click', function() {
        /**
         * LOG APERTURA MAPPA
         * Blade Laravel: {{ $centro->nome }} nome centro per identificazione
         */
        console.log('Mappa aperta per centro:', '{{ $centro->nome }}');
        
        /**
         * PLACEHOLDER ANALYTICS PER MAPPE
         * Event tracking per navigazione geografica
         * Indica interesse forte per visita fisica
         */
        // Analytics per mappe
        // gtag('event', 'map_view', {
        //     centro_id: {{ $centro->id }},
        //     centro_nome: '{{ $centro->nome }}'
        // });
    });
    
    // ===================================================================
    // SEZIONE: SMOOTH SCROLLING PER NAVIGAZIONE INTERNA
    // ===================================================================
    
    /**
     * EVENT HANDLER: Smooth scroll per link interni
     * jQuery: $('a[href^="#"]') selettore per anchor link
     * href^="#" significa "href che inizia con #" (link interno pagina)
     * 
     * UX BENEFIT: Navigazione fluida invece di salti bruschi
     */
    $('a[href^="#"]').on('click', function(e) {
        /**
         * OTTENIMENTO TARGET ELEMENT
         * JavaScript: this.hash ottiene parte # dell'URL
         * jQuery: $(this.hash) seleziona elemento con ID corrispondente
         */
        const target = $(this.hash);
        
        /**
         * VERIFICA ESISTENZA TARGET
         * jQuery: .length verifica se elemento esiste nel DOM
         * Previene errori se anchor link punta a elemento inesistente
         */
        if (target.length) {
            /**
             * BLOCCO COMPORTAMENTO DEFAULT
             * jQuery: e.preventDefault() impedisce salto immediato
             * Permette di implementare animazione personalizzata
             */
            e.preventDefault();
            
            /**
             * ANIMAZIONE SMOOTH SCROLL
             * jQuery: $('html, body') per compatibilità cross-browser
             * .animate() crea transizione fluida
             * 
             * PARAMETRI:
             * - scrollTop: posizione Y finale
             * - target.offset().top: posizione elemento target
             * - -100: offset per header fisso o spazio aggiuntivo
             * - 500: durata animazione in millisecondi
             */
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // ===================================================================
    // SEZIONE: AUTO-DISMISS ALERT TEMPORANEI
    // ===================================================================
    
    /**
     * GESTIONE AUTO-DISMISS ALERT BOOTSTRAP
     * jQuery: $('.alert-dismissible') seleziona alert con pulsante chiudi
     * .each() itera su ogni alert presente nella pagina
     * 
     * UX BENEFIT: Rimozione automatica messaggi temporanei
     */
    $('.alert-dismissible').each(function() {
        /**
         * RIFERIMENTO ALERT CORRENTE
         * jQuery: $(this) alert attuale nel loop each()
         * Salva riferimento per uso nel timeout
         */
        const alert = $(this);
        
        /**
         * TIMER AUTO-DISMISS
         * JavaScript: setTimeout() ritarda esecuzione di 5 secondi
         * Tempo sufficiente per lettura ma non invasivo
         */
        setTimeout(function() {
            /**
             * ANIMAZIONE FADEOUT + RIMOZIONE
             * jQuery: .fadeOut('slow') animazione scomparsa graduale
             * Callback function eseguita al termine fadeout
             * .remove() elimina elemento dal DOM per pulizia
             */
            alert.fadeOut('slow', function() {
                alert.remove(); // Pulizia DOM dopo animazione
            });
        }, 5000); // 5000ms = 5 secondi
    });
});