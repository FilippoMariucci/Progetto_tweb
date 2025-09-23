/**
 * TechSupport Pro - JavaScript Core
 * Sistema di Assistenza Tecnica Online
 * Gruppo 51 - Tecnologie Web 2024/2025
 * 
 * LINGUAGGIO: JavaScript ES6+ con jQuery
 * FRAMEWORK: Integrazione con Laravel (backend) e Bootstrap (frontend)
 * SCOPO: Gestione delle funzionalità client-side del sistema di assistenza tecnica
 */

/**
 * CLASSE PRINCIPALE: TechSupportApp
 * 
 * Questa classe gestisce l'intera applicazione JavaScript lato client.
 * Utilizza il pattern Singleton per garantire una sola istanza globale.
 * 
 * RESPONSABILITÀ:
 * - Inizializzazione componenti Bootstrap
 * - Configurazione sicurezza CSRF per Laravel
 * - Gestione eventi globali dell'applicazione
 * - Utilità per notifiche e interfaccia utente
 */
class TechSupportApp {
    /**
     * COSTRUTTORE della classe TechSupportApp
     * 
     * Viene eseguito automaticamente quando si crea una nuova istanza.
     * Inizializza tutti i componenti principali dell'applicazione.
     * 
     * LINGUAGGIO: JavaScript (metodo di classe ES6)
     * 
     * FLUSSO DI ESECUZIONE:
     * 1. Inizializza configurazioni core
     * 2. Configura token CSRF per sicurezza Laravel
     * 3. Inizializza componenti Bootstrap (tooltip, popover, etc.)
     * 4. Imposta event listeners globali
     */
    constructor() {
        this.initializeCore();        // Configurazioni di base
        this.setupCSRF();            // Sicurezza Laravel
        this.initializeGlobalComponents();  // Componenti Bootstrap
        this.setupGlobalEventListeners();   // Eventi globali
    }

    /**
     * METODO: initializeCore
     * 
     * LINGUAGGIO: JavaScript
     * SCOPO: Inizializza le configurazioni di base dell'applicazione
     * 
     * Definisce oggetto di configurazione con:
     * - toastDuration: durata in millisecondi delle notifiche toast
     * - ajaxTimeout: timeout per richieste AJAX (10 secondi)
     * - searchDebounce: ritardo per ottimizzare ricerche in tempo reale
     * 
     * CONSOLE.LOG: Stampa messaggio di conferma inizializzazione
     */
    initializeCore() {
        console.log('🚀 TechSupport Pro - Sistema inizializzato');
        
        // Oggetto di configurazione globale con parametri dell'applicazione
        this.config = {
            toastDuration: 5000,     // 5 secondi per auto-dismiss delle notifiche
            ajaxTimeout: 10000,      // 10 secondi timeout per chiamate AJAX
            searchDebounce: 300      // 300ms di delay per ottimizzare ricerche
        };
    }

    /**
     * METODO: setupCSRF
     * 
     * LINGUAGGIO: JavaScript + jQuery per manipolazione DOM
     * SCOPO: Configura il token CSRF per la sicurezza Laravel
     * 
     * CSRF (Cross-Site Request Forgery): meccanismo di sicurezza Laravel
     * che previene attacchi dove siti malevoli eseguono azioni non autorizzate
     * per conto dell'utente autenticato.
     * 
     * PROCEDIMENTO:
     * 1. Cerca nel DOM il meta tag con name="csrf-token"
     * 2. Estrae il valore del token usando jQuery attr()
     * 3. Configura $.ajaxSetup() per includere il token in TUTTE le richieste AJAX
     * 4. Imposta timeout globale per le richieste
     * 
     * IMPORTANTE: Senza questo token, Laravel rifiuterà le richieste POST/PUT/DELETE
     */
    setupCSRF() {
        
        // jQuery: cerca il meta tag CSRF nel <head> della pagina
        const token = $('meta[name="csrf-token"]').attr('content');
        
        if (token) {
            // $.ajaxSetup(): configura impostazioni predefinite per TUTTE le chiamate AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token    // Header richiesto da Laravel per validazione
                },
                timeout: this.config.ajaxTimeout  // Timeout globale: 10 secondi
            });
            console.log('✅ CSRF Token configurato');
        } else {
            // Warning se token non trovato (possibile problema di sicurezza)
            console.warn('⚠️ CSRF Token non trovato');
        }
    }

    /**
     * METODO: initializeGlobalComponents
     * 
     * LINGUAGGIO: JavaScript
     * SCOPO: Inizializza tutti i componenti Bootstrap e funzionalità globali UI
     * 
     * Orchestrazione dell'inizializzazione UI:
     * - Tooltip: piccole descrizioni che appaiono al hover
     * - Popover: finestre informative più grandi
     * - Alert auto-dismiss: nasconde automaticamente i messaggi
     * - Smooth scrolling: animazione per scroll verso ancore
     */
    initializeGlobalComponents() {
        // Tooltip Bootstrap: descrizioni al passaggio del mouse
        this.initTooltips();
        
        // Popover Bootstrap: finestre informative
        this.initPopovers();
        
        // Auto-dismiss alerts: nasconde messaggi dopo tempo definito
        this.setupAlertsDismiss();
        
        // Smooth scrolling: animazione per link interni (#anchor)
        this.setupSmoothScrolling();
    }

    /**
     * METODO: initTooltips
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap
     * SCOPO: Inizializza tutti i tooltip presenti nella pagina
     * 
     * BOOTSTRAP TOOLTIP: Componente per mostrare suggerimenti al hover
     * 
     * PROCEDIMENTO:
     * 1. jQuery seleziona tutti gli elementi con attributo data-bs-toggle="tooltip"
     * 2. .each() itera su ogni elemento trovato
     * 3. Per ogni elemento crea una nuova istanza Bootstrap Tooltip
     * 
     * ESEMPIO HTML: <button data-bs-toggle="tooltip" title="Aiuto">?</button>
     */
    initTooltips() {
        $('[data-bs-toggle="tooltip"]').each(function() {
            // 'this' si riferisce all'elemento DOM corrente nell'iterazione
            new bootstrap.Tooltip(this);
        });
    }

    /**
     * METODO: initPopovers
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap
     * SCOPO: Inizializza tutti i popover presenti nella pagina
     * 
     * BOOTSTRAP POPOVER: Componente per finestre informative più grandi dei tooltip
     * Possono contenere titolo, contenuto HTML, e sono cliccabili
     * 
     * PROCEDIMENTO identico ai tooltip ma per elementi con data-bs-toggle="popover"
     * 
     * ESEMPIO HTML: 
     * <button data-bs-toggle="popover" 
     *         data-bs-title="Titolo" 
     *         data-bs-content="Contenuto dettagliato">Info</button>
     */
    initPopovers() {
        $('[data-bs-toggle="popover"]').each(function() {
            // Crea istanza Bootstrap Popover per ogni elemento
            new bootstrap.Popover(this);
        });
    }

    /**
     * METODO: setupAlertsDismiss
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Nasconde automaticamente i messaggi di alert dopo un tempo prestabilito
     * 
     * FUNZIONALITÀ:
     * - setTimeout(): esegue codice dopo ritardo specificato
     * - Seleziona alert che NON hanno classe 'alert-permanent'
     * - fadeOut(): animazione jQuery per dissolvenza
     * 
     * ESEMPIO HTML:
     * <div class="alert alert-success">Messaggio temporaneo</div> ← si nasconde
     * <div class="alert alert-danger alert-permanent">Errore critico</div> ← rimane
     */
    setupAlertsDismiss() {
        setTimeout(() => {
            // Seleziona alert temporanei (non permanenti) e li nasconde con animazione
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, this.config.toastDuration);  // Usa configurazione: 5000ms
    }

    /**
     * METODO: setupSmoothScrolling
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Implementa animazione fluida per scroll verso ancore interne
     * 
     * SMOOTH SCROLLING: Invece del salto immediato, anima lo scroll
     * 
     * PROCEDIMENTO:
     * 1. Seleziona link che iniziano con "#" (ancore interne)
     * 2. .on('click'): Event listener per click
     * 3. Previene comportamento default del link
     * 4. Anima lo scroll verso l'elemento target
     * 
     * ESEMPIO HTML: <a href="#sezione1">Vai a Sezione 1</a>
     */
    setupSmoothScrolling() {
        // Seleziona tutti i link che iniziano con "#"
        $('a[href^="#"]').on('click', function(e) {
            // getAttribute(): metodo DOM nativo per ottenere attributo
            const target = $(this.getAttribute('href'));
            
            if (target.length) {  // Verifica che elemento target esista
                e.preventDefault();  // Previene jump immediato del browser
                
                // animate(): jQuery per animazione fluida
                $('html, body').animate({
                    // .offset().top: posizione verticale elemento - 100px di margine
                    scrollTop: target.offset().top - 100
                }, 500);  // Durata animazione: 500ms
            }
        });
    }

    /**
     * METODO: setupGlobalEventListeners
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Imposta event listeners che funzionano su tutta l'applicazione
     * 
     * EVENT DELEGATION: Usa $(document).on() invece di elementi specifici
     * Vantaggio: funziona anche su elementi aggiunti dinamicamente via AJAX
     * 
     * EVENTI GESTITI:
     * 1. Conferme eliminazione per pulsanti di delete
     * 2. Prevenzione doppio submit dei form
     * 3. Gestione errori AJAX globali
     */
    setupGlobalEventListeners() {
        // Event delegation per conferme eliminazione
        $(document).on('click', '[data-confirm-delete]', this.handleDeleteConfirmation);
        
        // Prevenzione doppio submit form (problema comune nelle web app)
        $(document).on('submit', 'form', this.preventDoubleSubmit);
        
        // Gestione errori AJAX applicazione-wide
        $(document).ajaxError(this.handleAjaxError);
    }

    /**
     * METODO: handleDeleteConfirmation
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Gestisce le conferme prima delle eliminazioni
     * 
     * SECURITY PATTERN: Evita eliminazioni accidentali
     * 
     * PARAMETRI:
     * @param {Event} e - Evento click del browser
     * 
     * PROCEDIMENTO:
     * 1. Previene invio immediato form
     * 2. Legge messaggio custom da attributo data o usa default
     * 3. Mostra confirm() nativo browser
     * 4. Se confermato, invia form di eliminazione
     * 
     * ESEMPIO HTML:
     * <form method="POST" action="/delete/123">
     *   <button data-confirm-delete="Eliminare questo prodotto?">Elimina</button>
     * </form>
     */
    handleDeleteConfirmation(e) {
        e.preventDefault();  // Blocca invio form immediato
        
        // Legge messaggio custom o usa default
        const message = $(this).data('confirm-delete') || 'Sei sicuro di voler eliminare questo elemento?';
        
        // Trova form parent del pulsante
        const form = $(this).closest('form');
        
        // confirm(): dialog nativo browser (ritorna true/false)
        if (confirm(message)) {
            form.submit();  // Procede con eliminazione
        }
        // Se cancella, non succede nulla (form non inviato)
    }

    /**
     * METODO: preventDoubleSubmit
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Previene invio multiplo dello stesso form (problema comune web)
     * 
     * PROBLEMA RISOLTO: Utente clicca più volte "Salva" → record duplicati
     * 
     * PARAMETRI:
     * @param {Event} e - Evento submit del form
     * 
     * MECCANISMO:
     * 1. Controlla flag 'submitted' del form
     * 2. Se già inviato, blocca nuovo invio
     * 3. Imposta flag e disabilita pulsante submit
     * 4. Timer di sicurezza: riabilita dopo 3 secondi (per gestire errori)
     */
    preventDoubleSubmit(e) {
        const form = $(this);  // Form che ha scatenato l'evento
        const submitBtn = form.find('button[type="submit"]');  // Pulsante submit
        
        // Controlla se form già inviato usando jQuery data()
        if (form.data('submitted')) {
            e.preventDefault();  // Blocca nuovo invio
            return false;
        }
        
        // Marca form come inviato
        form.data('submitted', true);
        // Disabilita pulsante per feedback visivo
        submitBtn.prop('disabled', true);
        
        // Timer di sicurezza: riabilita dopo 3 secondi
        // Utile se ci sono errori che impediscono il redirect
        setTimeout(() => {
            form.data('submitted', false);
            submitBtn.prop('disabled', false);
        }, 3000);
    }

    /**
     * METODO: handleAjaxError
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Gestisce errori AJAX in modo centralizzato
     * 
     * ERROR HANDLING: Invece di gestire errori in ogni chiamata AJAX,
     * questo handler globale intercetta TUTTI gli errori AJAX dell'app
     * 
     * PARAMETRI (forniti automaticamente da jQuery):
     * @param {Event} event - Evento errore
     * @param {XMLHttpRequest} jqXHR - Oggetto richiesta AJAX
     * @param {Object} ajaxSettings - Impostazioni chiamata
     * @param {String} thrownError - Errore lanciato
     * 
     * STATUS CODE HTTP GESTITI:
     * - 403: Forbidden (non autorizzato)
     * - 404: Not Found (risorsa non trovata)  
     * - 500: Internal Server Error (errore server)
     */
    handleAjaxError(event, jqXHR, ajaxSettings, thrownError) {
        console.error('Errore AJAX:', thrownError);  // Log per debugging
        
        let message = 'Errore di connessione';  // Messaggio default
        
        // Switch basato su HTTP status code
        if (jqXHR.status === 403) {
            message = 'Non autorizzato';  // Problemi di permessi
        } else if (jqXHR.status === 404) {
            message = 'Risorsa non trovata';  // URL inesistente
        } else if (jqXHR.status === 500) {
            message = 'Errore del server';  // Errore lato server
        }
        
        // Mostra notifica all'utente usando metodo statico
        TechSupportApp.showToast(message, 'danger');
    }

    // === UTILITY STATICHE ===
    // Metodi statici: chiamabili senza istanziare la classe
    // Esempio: TechSupportApp.showToast() invece di app.showToast()

    /**
     * METODO STATICO: showToast
     * 
     * LINGUAGGIO: JavaScript + Bootstrap Toast + jQuery
     * SCOPO: Mostra notifiche toast (messaggi temporanei non invasivi)
     * 
     * BOOTSTRAP TOAST: Componente per notifiche eleganti
     * Appaiono nell'angolo dello schermo e si nascondono automaticamente
     * 
     * PARAMETRI:
     * @param {String} message - Testo da mostrare
     * @param {String} type - Tipo Bootstrap: 'success', 'danger', 'warning', 'info'
     * 
     * PROCEDIMENTO:
     * 1. Genera ID univoco con timestamp
     * 2. Crea HTML del toast con interpolazione template
     * 3. Aggiunge toast al container (o lo crea se non esiste)
     * 4. Inizializza componente Bootstrap Toast
     * 5. Mostra toast e imposta auto-rimozione
     */
    static showToast(message, type = 'info') {
        // ID univoco basato su timestamp
        const toastId = 'toast-' + Date.now();
        
        // Template HTML del toast con interpolazione variabili
        const toast = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Crea container se non esiste (lazy initialization)
        if (!$('#toast-container').length) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        
        // Converte HTML string in oggetto jQuery
        const $toast = $(toast);
        // Aggiunge toast al container
        $('#toast-container').append($toast);
        
        // Inizializza componente Bootstrap Toast
        const toastInstance = new bootstrap.Toast($toast[0]);
        toastInstance.show();  // Mostra toast
        
        // Event listener per pulizia: rimuove elemento quando toast si nasconde
        $toast.on('hidden.bs.toast', function() {
            $(this).remove();  // Pulizia DOM
        });
    }

    /**
     * METODO STATICO: showSpinner
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Spinner
     * SCOPO: Mostra indicatore di caricamento su pulsanti/elementi
     * 
     * UX PATTERN: Feedback visivo durante operazioni asincrone
     * L'utente sa che qualcosa sta succedendo
     * 
     * PARAMETRI:
     * @param {HTMLElement|jQuery|String} element - Elemento su cui mostrare spinner
     * 
     * PROCEDIMENTO:
     * 1. Disabilita elemento (previene click multipli)
     * 2. Salva testo originale in data attribute
     * 3. Sostituisce contenuto con spinner + testo "Caricamento..."
     */
    static showSpinner(element) {
        const $el = $(element);  // Converte in oggetto jQuery
        
        // HTML del spinner Bootstrap
        const spinner = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
        
        $el.prop('disabled', true)  // Disabilita elemento
           .data('original-text', $el.html())  // Salva contenuto originale
           .html(spinner + 'Caricamento...');  // Mostra spinner + testo
    }

    /**
     * METODO STATICO: hideSpinner
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Nasconde spinner e ripristina stato originale elemento
     * 
     * PARAMETRI:
     * @param {HTMLElement|jQuery|String} element - Elemento da ripristinare
     * 
     * PROCEDIMENTO:
     * 1. Recupera testo originale da data attribute
     * 2. Riabilita elemento
     * 3. Ripristina contenuto originale
     */
    static hideSpinner(element) {
        const $el = $(element);
        // Recupera testo salvato da showSpinner()
        const originalText = $el.data('original-text');
        
        if (originalText) {
            $el.prop('disabled', false)  // Riabilita elemento
               .html(originalText);      // Ripristina contenuto
        }
    }

    /**
     * METODO STATICO: formatNumber
     * 
     * LINGUAGGIO: JavaScript (Intl API nativo)
     * SCOPO: Formatta numeri secondo standard italiano
     * 
     * INTL.NUMBERFORMAT: API internazionalizzazione nativa JavaScript
     * Gestisce automaticamente separatori decimali e migliaia per locale
     * 
     * PARAMETRI:
     * @param {Number} num - Numero da formattare
     * @return {String} - Numero formattato (es: 1.234,56)
     * 
     * ESEMPIO:
     * formatNumber(1234.56) → "1.234,56" (formato italiano)
     * formatNumber(1234.56) → "1,234.56" (formato inglese)
     */
    static formatNumber(num) {
        // 'it-IT': locale italiano (punto migliaia, virgola decimali)
        return new Intl.NumberFormat('it-IT').format(num);
    }

    /**
     * METODO STATICO: debounce
     * 
     * LINGUAGGIO: JavaScript (Closures e Timers)
     * SCOPO: Ottimizza funzioni chiamate frequentemente (es. ricerca in tempo reale)
     * 
     * DEBOUNCE PATTERN: Ritarda esecuzione finché non passano N millisecondi
     * dall'ultima chiamata. Previene spam di richieste durante digitazione.
     * 
     * PARAMETRI:
     * @param {Function} func - Funzione da "debounciare"
     * @param {Number} wait - Millisecondi di attesa
     * @param {Boolean} immediate - Se true, esegue subito poi aspetta
     * @return {Function} - Versione debounced della funzione
     * 
     * ESEMPIO USO:
     * const debouncedSearch = debounce(searchFunction, 300);
     * // searchFunction viene eseguita solo 300ms dopo l'ultima chiamata
     * 
     * MECCANISMO (Closure):
     * 1. Variabile 'timeout' mantenuta nel closure
     * 2. Ogni chiamata cancella timeout precedente
     * 3. Imposta nuovo timeout per esecuzione ritardata
     */
    static debounce(func, wait, immediate) {
        let timeout;  // Variabile mantenuta nel closure
        
        // Ritorna nuova funzione che wrappa quella originale
        return function executedFunction(...args) {
            // Funzione da eseguire dopo ritardo
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);  // Esegue se non immediato
            };
            
            const callNow = immediate && !timeout;  // Esegue subito se immediato e primo call
            clearTimeout(timeout);  // Cancella eventuale timeout precedente
            timeout = setTimeout(later, wait);  // Imposta nuovo timeout
            
            if (callNow) func(...args);  // Esecuzione immediata se richiesta
        };
    }
}

// === INIZIALIZZAZIONE GLOBALE ===

/**
 * DOCUMENT READY HANDLER
 * 
 * LINGUAGGIO: jQuery
 * SCOPO: Inizializza applicazione quando DOM è completamente caricato
 * 
 * $(document).ready(): jQuery equivalent di DOMContentLoaded
 * Si esegue quando HTML è parsato (prima del caricamento immagini)
 * 
 * OPERAZIONI:
 * 1. Crea istanza globale TechSupportApp
 * 2. Espone funzioni utili come globali per compatibilità
 * 3. Log di conferma inizializzazione
 */
$(document).ready(function() {
    // Crea istanza globale accessibile come window.techSupportApp
    window.techSupportApp = new TechSupportApp();
    
    // Espone metodi statici come funzioni globali per facilità d'uso
    // Permette di chiamare showToast() invece di TechSupportApp.showToast()
    window.showToast = TechSupportApp.showToast;
    window.showSpinner = TechSupportApp.showSpinner;
    window.hideSpinner = TechSupportApp.hideSpinner;
    window.formatNumber = TechSupportApp.formatNumber;
    window.debounce = TechSupportApp.debounce;
    
    console.log('📱 TechSupport Pro pronto per l\'uso');
});

// === EXPORT PER MODULI ===

/**
 * GLOBAL EXPORT
 * 
 * LINGUAGGIO: JavaScript (Window Object)
 * SCOPO: Rende classe disponibile globalmente per altri script
 * 
 * Permette ad altri file JavaScript di accedere alla classe:
 * const app = new window.TechSupportApp();
 */
window.TechSupportApp = TechSupportApp;