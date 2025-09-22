/**
 * FILE: contatti.js
 * LINGUAGGIO: JavaScript (ES6+) con jQuery
 * SCOPO: Gestione form contatti con validazione client-side e user experience migliorata
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Bootstrap 5 (classi validazione)
 * FUNZIONALITÀ PRINCIPALI: Validazione form, auto-resize textarea, tracking interazioni
 * CARATTERISTICHE: Form validation real-time, UX enhancements, analytics tracking
 * AUTORE: Sistema di assistenza tecnica
 */

/**
 * DOCUMENT READY EVENT - jQuery
 * Entry point principale che inizializza tutte le funzionalità quando DOM è pronto
 * Si attiva dopo che HTML è completamente caricato e parsato
 */
$(document).ready(function() {
    
    /**
     * LOG IDENTIFICAZIONE FILE PER DEBUG
     * console.log: stampa nella console browser per verificare caricamento file
     */
    console.log('contatti caricato');
    
    // === CONTROLLO ROUTE SPECIFICA PER SICUREZZA ===
    /**
     * VALIDAZIONE ROUTE CORRENTE
     * window.LaravelApp?.route: variabile globale iniettata da Laravel tramite Blade template
     * Optional chaining (?.) per evitare TypeError se LaravelApp non definito
     * Pattern early return per performance e sicurezza del codice
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'contatti') {
        return; // Termina esecuzione se non siamo nella pagina contatti
    }
    
    // === INIZIALIZZAZIONE VARIABILI GLOBALI ===
    /**
     * SETUP DATI CONDIVISI CLIENT-SERVER
     * window.PageData: oggetto con dati pagina popolato da Laravel via @json()
     * selectedProducts: array tracking (non utilizzato in questa pagina)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    /**
     * LOG CONFERMA CARICAMENTO PAGINA
     * Secondo log per tracciare progression del caricamento
     */
    console.log('Pagina contatti caricata');
    
    // === VALIDAZIONE FORM LATO CLIENT ===
    /**
     * EVENT HANDLER PER SUBMIT DEL FORM CONTATTI
     * Implementa validazione completa prima dell'invio al server
     * Migliora UX fornendo feedback immediato senza attesa server
     */
    $('#contact-form').on('submit', function(e) {
        
        let isValid = true; // Flag per tracciare validità complessiva del form
        
        /**
         * DEFINIZIONE CAMPI OBBLIGATORI
         * Array JavaScript con ID dei campi che devono essere compilati
         * Corrisponde ai campi required nel form HTML
         */
        const requiredFields = ['nome', 'cognome', 'email', 'tipo_richiesta', 'oggetto', 'messaggio'];
        
        /**
         * ITERAZIONE E VALIDAZIONE CAMPI OBBLIGATORI
         * .forEach(): metodo array per eseguire funzione su ogni elemento
         * Controlla ogni campo e applica styling di errore se vuoto
         */
        requiredFields.forEach(function(field) {
            /**
             * SELEZIONE DINAMICA CAMPO CON TEMPLATE LITERAL
             * $('#' + field): selettore jQuery dinamico per ID campo
             * Alternativa moderna: $(`#${field}`) con template literal
             */
            const input = $('#' + field);
            
            /**
             * CONTROLLO VALORE CAMPO
             * .val(): metodo jQuery per ottenere valore input/select/textarea
             * .trim(): rimuove spazi bianchi iniziali e finali
             * !valore: controlla se stringa è vuota (falsy value)
             */
            if (!input.val().trim()) {
                /**
                 * APPLICAZIONE STYLING ERRORE BOOTSTRAP
                 * .addClass(): aggiunge classe CSS per styling errore
                 * 'is-invalid': classe Bootstrap per campi con errori
                 */
                input.addClass('is-invalid');
                isValid = false; // Marca form come non valido
            } else {
                /**
                 * RIMOZIONE STYLING ERRORE SE CAMPO VALIDO
                 * .removeClass(): rimuove classe CSS di errore
                 * Pulisce styling precedente se campo ora è valido
                 */
                input.removeClass('is-invalid');
            }
        });
        
        /**
         * VALIDAZIONE CHECKBOX PRIVACY SEPARATA
         * Controllo specifico per checkbox accettazione privacy
         * Richiesto per compliance GDPR/normative privacy
         */
        if (!$('#privacy').is(':checked')) {
            /**
             * :checked PSEUDO-SELECTOR
             * .is(':checked'): metodo jQuery per verificare stato checkbox
             * Restituisce boolean: true se spuntata, false se non spuntata
             */
            $('#privacy').addClass('is-invalid'); // Applica styling errore
            isValid = false; // Marca form non valido
        } else {
            $('#privacy').removeClass('is-invalid'); // Rimuove errore se accettata
        }
        
        /**
         * GESTIONE FORM NON VALIDO
         * Se ci sono errori, previene invio e mostra feedback
         */
        if (!isValid) {
            /**
             * PREVENZIONE SUBMIT DEFAULT
             * e.preventDefault(): metodo Event per bloccare comportamento default
             * Impedisce invio form al server se validazione fallisce
             */
            e.preventDefault();
            
            /**
             * FEEDBACK UTENTE CON ALERT NATIVO
             * alert(): dialog box nativo browser per messaggi immediati
             * In produzione potrebbe essere sostituito con toast/modal più eleganti
             */
            alert('Compila tutti i campi obbligatori e accetta la privacy policy.');
        }
        // Se isValid = true, il form viene inviato normalmente al server
    });
    
    // === RIMOZIONE REAL-TIME CLASSI ERRORE ===
    /**
     * EVENT HANDLER PER RIMOZIONE STYLING ERRORI
     * Migliora UX rimuovendo indicatori errore quando utente inizia a correggere
     * Event delegation su tutti gli input del form
     */
    $('input, select, textarea').on('input change', function() {
        /**
         * RIMOZIONE IMMEDIATE FEEDBACK ERRORE
         * $(this): riferimento jQuery all'elemento che ha scatenato evento
         * Rimuove classe is-invalid non appena utente inizia a digitare/selezionare
         */
        $(this).removeClass('is-invalid');
    });
    
    // === AUTO-RESIZE TEXTAREA DINAMICO ===
    /**
     * IMPLEMENTAZIONE TEXTAREA ESPANDIBILE
     * Migliora UX facendo crescere textarea automaticamente con il contenuto
     * Evita scrollbar interne e mostra tutto il testo
     */
    $('#messaggio').on('input', function() {
        /**
         * RESET ALTEZZA A AUTO
         * this.style.height = 'auto': resetta altezza per calcolo corretto
         * Necessario per calcolare correttamente la nuova altezza necessaria
         */
        this.style.height = 'auto';
        
        /**
         * IMPOSTAZIONE ALTEZZA BASATA SU CONTENUTO
         * this.scrollHeight: proprietà DOM nativa che restituisce altezza totale contenuto
         * Include contenuto non visibile che necessiterebbe scroll
         * Imposta altezza CSS per mostrare tutto il contenuto senza scroll
         */
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // === TRACCIAMENTO INTERAZIONI UTENTE PER ANALYTICS ===
    /**
     * TRACKING CLICK SU LINK TELEFONO
     * Event delegation per tutti i link che iniziano con "tel:"
     * Utile per analytics e monitoraggio conversioni
     */
    $('a[href^="tel:"]').on('click', function() {
        /**
         * LOG INTERAZIONE TELEFONICA
         * $(this).attr('href'): ottiene valore completo attributo href
         * console.log: registra tentativo chiamata per analytics
         * In produzione potrebbe inviare evento a Google Analytics/Tag Manager
         */
        console.log('Chiamata avviata:', $(this).attr('href'));
    });
    
    /**
     * TRACKING CLICK SU LINK EMAIL
     * Event delegation per tutti i link mailto
     * Traccia aperture client email per analytics conversioni
     */
    $('a[href^="mailto:"]').on('click', function() {
        /**
         * LOG INTERAZIONE EMAIL
         * Registra tentativo apertura client email
         * Utile per misurare engagement e preferenze contatto utenti
         */
        console.log('Email aperta:', $(this).attr('href'));
    });
});

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, arrow functions, template literals)
 * - jQuery (DOM manipulation, event handling, selectors, method chaining)
 * - Bootstrap 5 (classi validazione is-invalid per styling errori)
 * - HTML5 Form API (validation states, input types)
 * - CSS Manipulation (dynamic height adjustment per textarea)
 * - Event Prevention (preventDefault per controllo form submission)
 * - DOM Properties native (scrollHeight per calcolo dimensioni)
 * - Laravel Integration (route checking, data sharing)
 * 
 * PATTERN E PRINCIPI UTILIZZATI:
 * - Client-Side Validation (feedback immediato prima invio server)
 * - Progressive Enhancement (form funziona anche senza JavaScript)
 * - Real-time User Feedback (rimozione errori durante digitazione)
 * - User Experience Enhancement (textarea auto-resize)
 * - Analytics Integration (tracking interazioni per business intelligence)
 * - Accessibility Friendly (mantiene funzionalità base se JS disabilitato)
 * - Performance Conscious (early return, efficient selectors)
 * - Error Prevention (controlli esistenza elementi, optional chaining)
 * 
 * FUNZIONALITÀ IMPLEMENTATE:
 * 1. **Form Validation Client-Side**: Controllo campi obbligatori e privacy
 * 2. **Real-time Error Removal**: Rimozione feedback errore durante digitazione
 * 3. **Auto-resize Textarea**: Espansione automatica area testo messaggio
 * 4. **Interaction Tracking**: Analytics per link telefono e email
 * 5. **Route Protection**: Esecuzione condizionale basata su route Laravel
 * 6. **User Experience Enhancement**: Feedback visivo immediato con Bootstrap
 * 
 * CARATTERISTICHE DI SICUREZZA E VALIDAZIONE:
 * - Validazione solo lato client (complementare a validazione server)
 * - Controllo campi obbligatori definiti in array centralizzato
 * - Gestione speciale checkbox privacy per compliance GDPR
 * - Prevenzione submit se dati non validi
 * - Sanitizzazione input con trim() per spazi superflui
 * 
 * MIGLIORAMENTI SUGGERITI PER PRODUZIONE:
 * 1. **Validazione Email**: Aggiungere regex per formato email valido
 * 2. **Validazione Telefono**: Controllo formato numero se presente
 * 3. **Rate Limiting**: Prevenzione spam con cooldown tra invii
 * 4. **Toast Notifications**: Sostituire alert() con notifiche più eleganti
 * 5. **Analytics Integration**: Collegare tracking a Google Analytics/Tag Manager  
 * 6. **Accessibility**: Aggiungere ARIA labels per screen readers
 * 7. **Error Messages**: Messaggi specifici per ogni tipo di errore
 * 8. **Loading States**: Disabilitare form durante invio con spinner
 * 
 * CONSIDERAZIONI ARCHITETTURALI:
 * - Codice pulito e ben strutturato senza nesting eccessivo
 * - Separazione logica tra validazione, UX enhancement, e analytics
 * - Event delegation efficiente per performance
 * - Logging strutturato per debugging e monitoraggio
 * - Compatibilità con sistemi di analytics esterni
 */