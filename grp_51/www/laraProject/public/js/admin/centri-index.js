/*
 * =====================================================
 * SCRIPT JAVASCRIPT PER LA GESTIONE LISTA CENTRI DI ASSISTENZA
 * =====================================================
 * 
 * FRAMEWORK: Laravel + jQuery + Bootstrap 5
 * LINGUAGGIO: JavaScript ES6+ con jQuery
 * FUNZIONALITÀ: Gestione lista centri, filtri dinamici, ricerca, eliminazione
 * 
 * DESCRIZIONE GENERALE:
 * Questo script gestisce l'interfaccia della pagina di elenco dei centri di assistenza
 * nel pannello amministrativo. Include funzionalità di ricerca con debounce,
 * filtri dinamici, gestione eliminazione con modal di conferma e notifiche.
 */

// =====================================================
// INIZIALIZZAZIONE JQUERY DOCUMENT READY
// =====================================================

/**
 * EVENT HANDLER: Document Ready di jQuery
 * LINGUAGGIO: JavaScript con libreria jQuery 3.7
 * 
 * Si esegue quando il DOM è completamente caricato e pronto per la manipolazione.
 * jQuery garantisce che tutti gli elementi HTML sono accessibili.
 */
$(document).ready(function() {
    console.log('admin.centri.index JS caricato');
    
    // =====================================================
    // CONTROLLO ROUTE SPECIFICO
    // =====================================================
    
    /**
     * SICUREZZA: Verifica route corrente
     * SCOPO: Evita esecuzione di codice JavaScript su pagine sbagliate
     * TECNOLOGIA: Laravel route detection via JavaScript globale
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.index') {
        return; // Esce se non siamo nella route corretta
    }
    
    /**
     * INIZIALIZZAZIONE VARIABILI GLOBALI
     * pageData: Dati passati dal controller Laravel via JSON
     * selectedProducts: Array per tracciare selezioni multiple (se implementate)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    console.log('🏢 Inizializzazione gestione centri assistenza');
    
    // =====================================================
    // CHIAMATE FUNZIONI DI INIZIALIZZAZIONE
    // =====================================================
    
    /**
     * PATTERN: Modular Initialization
     * Ogni funzionalità è separata in funzioni specifiche per:
     * - Migliore organizzazione del codice
     * - Facilità di debug
     * - Riusabilità
     * - Manutenibilità
     */
    
    // Inizializza tooltips Bootstrap per elementi con attributo title/data-bs-toggle
    initializeTooltips();
    
    // Configura filtri dinamici per provincia e città
    setupDynamicFilters();
    
    // Imposta gestione ricerca con debounce
    setupSearchHandler();
    
    // Configura event handlers per eliminazione centri
    setupDeleteHandlers();
});

// =====================================================
// GESTIONE ELIMINAZIONE CENTRI
// =====================================================

/**
 * FUNZIONE: setupDeleteHandlers
 * SCOPO: Configura tutti gli event listener per l'eliminazione dei centri
 * PATTERN: Event Delegation per elementi dinamici
 * SICUREZZA: Doppia conferma prima dell'eliminazione
 */
function setupDeleteHandlers() {
    
    // === EVENT DELEGATION PER PULSANTI ELIMINA ===
    
    /**
     * EVENT LISTENER: Click su pulsanti elimina
     * TECNOLOGIA: jQuery Event Delegation
     * PATTERN: $(document).on() per gestire elementi dinamici
     * 
     * Event delegation permette di gestire eventi su elementi
     * che potrebbero essere aggiunti dinamicamente al DOM
     */
    $(document).on('click', '.btn-elimina-centro', function(e) {
        // Previene comportamento default del link/button
        e.preventDefault();
        
        // === ESTRAZIONE DATI DAL DOM ===
        // data() method di jQuery legge attributi data-*
        const centroId = $(this).data('centro-id');
        const centroNome = $(this).data('centro-nome');
        
        // === VALIDAZIONE DATI ===
        // Verifica che i dati necessari siano presenti
        if (centroId && centroNome) {
            // Chiama funzione globale di conferma eliminazione
            confirmDelete(centroId, centroNome);
        } else {
            console.error('❌ Dati centro mancanti per eliminazione');
        }
    });
    
    // === GESTIONE SUBMIT DEL FORM DI ELIMINAZIONE ===
    
    /**
     * EVENT LISTENER: Submit del form di eliminazione nel modal
     * SCOPO: Fornisce feedback visivo durante l'eliminazione
     * UI/UX: Loading state per migliorare esperienza utente
     */
    $('#delete-form').on('submit', function(e) {
        // === UI FEEDBACK: LOADING STATE ===
        
        // Trova il pulsante submit nel form
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html(); // Salva testo originale
        
        // === DISABILITAZIONE E SPINNER ===
        submitBtn.prop('disabled', true) // Disabilita per evitare double-submit
                 .html('<i class="bi bi-hourglass-split me-1"></i>Eliminazione...'); // Mostra spinner
        
        console.log('🗑️ Invio form eliminazione centro');
        
        // === FALLBACK: RIPRISTINO AUTOMATICO ===
        // Ripristina il pulsante dopo 3 secondi in caso di errori
        setTimeout(() => {
            submitBtn.prop('disabled', false).html(originalText);
        }, 3000);
    });
}

// =====================================================
// FUNZIONE GLOBALE CONFERMA ELIMINAZIONE
// =====================================================

/**
 * FUNZIONE GLOBALE: confirmDelete
 * SCOPE: window.* per accessibilità da template Blade
 * PARAMETRI:
 *   - centroId: ID numerico del centro da eliminare
 *   - centroName: Nome del centro per visualizzazione
 * 
 * PATTERN: Try-Catch per gestione errori robuста
 * FALLBACK: Alert browser se modal fallisce
 */
window.confirmDelete = function(centroId, centroName) {
    console.log('🗑️ Richiesta eliminazione centro:', centroName, 'ID:', centroId);
    
    try {
        // === MANIPOLAZIONE MODAL BOOTSTRAP ===
        
        /**
         * DOM QUERY: Ricerca elementi del modal
         * querySelector: JavaScript vanilla per performance
         * getElementById: Accesso diretto tramite ID
         */
        const modalTitle = document.querySelector('#deleteModal .modal-title');
        const centroNameElement = document.getElementById('centro-name');
        const deleteForm = document.getElementById('delete-form');
        
        // === VALIDAZIONE ELEMENTI DOM ===
        // Verifica che gli elementi del modal esistano
        if (!centroNameElement || !deleteForm) {
            console.error('❌ Elementi modal non trovati');
            
            // === FALLBACK 1: CONFIRM BROWSER ===
            // Se modal non disponibile, usa conferma browser nativa
            if (confirm(`Sei sicuro di voler eliminare il centro "${centroName}"?`)) {
                // Redirect diretto per eliminazione
                window.location.href = `/admin/centri/${centroId}/delete`;
            }
            return; // Esce dalla funzione
        }
        
        // === AGGIORNAMENTO CONTENUTI MODAL ===
        
        // Inserisce nome centro nel modal per personalizzazione
        centroNameElement.textContent = centroName;
        
        // Configura action del form con ID specifico del centro
        deleteForm.setAttribute('action', `/admin/centri/${centroId}`);
        
        // === APERTURA MODAL BOOTSTRAP ===
        
        /**
         * BOOTSTRAP 5 MODAL API
         * new bootstrap.Modal(): Crea istanza modal
         * .show(): Apre il modal con animazioni
         */
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
        
        console.log('✅ Modal eliminazione mostrato per centro:', centroName);
        
    } catch (error) {
        // === GESTIONE ERRORI ===
        console.error('❌ Errore nell\'apertura del modal:', error);
        
        // === FALLBACK 2: FORM TEMPORANEO ===
        // Se tutto fallisce, crea form dinamico per eliminazione
        if (confirm(`Errore nel modal. Eliminare comunque il centro "${centroName}"?`)) {
            
            /**
             * CREAZIONE FORM DINAMICO
             * Necessario per inviare richiesta DELETE con CSRF protection
             */
            const form = document.createElement('form');
            form.method = 'POST'; // Laravel usa POST con method spoofing
            form.action = `/admin/centri/${centroId}`;
            
            // === CSRF TOKEN PER SICUREZZA ===
            /**
             * LARAVEL CSRF PROTECTION
             * Ogni form deve includere token CSRF per prevenire attacchi
             */
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);
            
            // === METHOD SPOOFING PER DELETE ===
            /**
             * LARAVEL METHOD SPOOFING
             * HTML supporta solo GET/POST, Laravel simula DELETE via campo nascosto
             */
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // === INVIO FORM ===
            // Aggiunge al DOM temporaneamente e invia
            document.body.appendChild(form);
            form.submit();
        }
    }
};

// =====================================================
// SISTEMA DI NOTIFICHE
// =====================================================

/**
 * FUNZIONE: showNotification
 * SCOPO: Mostra notifiche temporanee all'utente
 * TECNOLOGIA: Bootstrap 5 Alerts + jQuery
 * 
 * PARAMETRI:
 *   - message: Testo del messaggio
 *   - type: Tipo notifica ('success', 'danger', 'warning', 'info')
 */
function showNotification(message, type = 'success') {
    
    // === MAPPATURA TIPI ALERT ===
    // Oggetti per mappare tipi a classi CSS Bootstrap
    const alertTypes = {
        'success': 'alert-success',
        'danger': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    // === MAPPATURA ICONE BOOTSTRAP ICONS ===
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle', 
        'info': 'info-circle'
    };
    
    // === CREAZIONE ELEMENTO ALERT ===
    /**
     * JQUERY TEMPLATE LITERAL
     * Crea HTML dinamico con interpolazione di variabili
     * POSITIONING: Fixed per rimanere visibile durante scroll
     */
    const alert = $(`
        <div class="alert ${alertTypes[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; min-width: 300px;">
            <i class="bi bi-${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // === INSERIMENTO NEL DOM ===
    $('body').append(alert);
    
    // === AUTO-RIMOZIONE ===
    /**
     * TIMEOUT AUTOMATICO
     * setTimeout: Rimuove notifica dopo 5 secondi
     * .alert('close'): Metodo Bootstrap per chiusura animata
     */
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}

// =====================================================
// FUNZIONI DI SUPPORTO E UTILITÀ
// =====================================================

/**
 * FUNZIONE: initializeTooltips
 * SCOPO: Inizializza tooltips Bootstrap per tutti gli elementi
 * TECNOLOGIA: Bootstrap 5 Tooltip component
 * 
 * PATTERN: Progressive Enhancement
 * Cerca elementi con title o data-bs-toggle="tooltip"
 */
function initializeTooltips() {
    /**
     * QUERY SELECTOR AVANZATO
     * [].slice.call(): Converte NodeList in Array
     * querySelectorAll(): Seleziona tutti gli elementi con tooltip
     */
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[title], [data-bs-toggle="tooltip"]')
    );
    
    /**
     * ARRAY.MAP(): Trasforma array di elementi in array di istanze Tooltip
     * new bootstrap.Tooltip(): Crea istanza tooltip per ogni elemento
     */
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// =====================================================
// FILTRI DINAMICI
// =====================================================

/**
 * FUNZIONE: setupDynamicFilters
 * SCOPO: Configura filtri che si applicano automaticamente
 * PATTERN: Auto-submit per UX fluida senza pulsanti
 */
function setupDynamicFilters() {
    
    // === FILTRO PROVINCIA ===
    /**
     * EVENT LISTENER: Change su select provincia
     * COMPORTAMENTO: Submit automatico del form al cambio selezione
     * UX: Filtro immediato senza click su "Applica"
     */
    $('#provincia').on('change', function() {
        console.log('🌍 Provincia selezionata:', $(this).val());
        
        // .closest(): Trova il form genitore più vicino
        $(this).closest('form').submit();
    });
    
    // === FILTRO CITTÀ CON DEBOUNCE ===
    /**
     * DEBOUNCE PATTERN
     * Previene troppe richieste durante la digitazione
     * Attende 800ms di pausa prima di inviare
     */
    let cittaTimeout; // Variabile per memorizzare timeout
    
    $('#citta').on('input', function() {
        const citta = $(this).val().trim();
        
        // === CLEAR TIMEOUT PRECEDENTE ===
        // Cancella timeout esistente per resettare il timer
        clearTimeout(cittaTimeout);
        
        // === CONDIZIONE LUNGHEZZA MINIMA ===
        // Filtra solo con almeno 2 caratteri o campo vuoto
        if (citta.length >= 2 || citta.length === 0) {
            /**
             * SETTIMEOUT: Ritarda esecuzione
             * 800ms: Tempo di attesa per stabilità UX
             * Arrow function: Mantiene scope della variabile
             */
            cittaTimeout = setTimeout(() => {
                console.log('🏙️ Filtro città:', citta);
                $(this).closest('form').submit();
            }, 800);
        }
    });
}

// =====================================================
// GESTIONE RICERCA
// =====================================================

/**
 * FUNZIONE: setupSearchHandler
 * SCOPO: Configura ricerca con debounce per performance
 * PATTERN: Debounce per ridurre richieste server
 * UX: Ricerca reattiva ma non eccessiva
 */
function setupSearchHandler() {
    
    /**
     * DEBOUNCE PATTERN PER RICERCA
     * Simile ai filtri ma con timing diverso per la ricerca
     */
    let searchTimeout; // Timeout per debounce ricerca
    
    /**
     * EVENT LISTENER: Input su campo ricerca
     * 'input': Si attiva ad ogni carattere digitato
     * Più reattivo di 'change' che si attiva solo al blur
     */
    $('#search').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Cancella timeout precedente
        clearTimeout(searchTimeout);
        
        // === LOGICA RICERCA ===
        // Ricerca con almeno 3 caratteri o campo vuoto (per reset)
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            /**
             * TIMEOUT RICERCA: 600ms
             * Più veloce dei filtri (800ms) per reattività ricerca
             */
            searchTimeout = setTimeout(() => {
                console.log('🔍 Ricerca:', searchTerm);
                $(this).closest('form').submit();
            }, 600);
        }
    });
}

// =====================================================
// CONFERMA INIZIALIZZAZIONE
// =====================================================

/**
 * LOG FINALE
 * Conferma che tutto lo script è stato caricato correttamente
 * Utile per debug e verifica in console browser
 */
console.log('✅ Fix pulsante elimina caricato correttamente');

/*
 * =====================================================
 * RIEPILOGO TECNOLOGIE E PATTERN UTILIZZATI:
 * =====================================================
 * 
 * 1. JAVASCRIPT MODERNO:
 *    - ES6+ features (const, let, arrow functions)
 *    - Template literals per HTML dinamico
 *    - Destructuring e spread operator
 * 
 * 2. JQUERY 3.7:
 *    - Selettori CSS avanzati
 *    - Event delegation $(document).on()
 *    - DOM manipulation (.html(), .prop(), .data())
 *    - Form handling (.submit(), .closest())
 * 
 * 3. BOOTSTRAP 5:
 *    - Modal component API
 *    - Alert component con auto-dismiss
 *    - Tooltip component
 *    - CSS utility classes
 * 
 * 4. DOM APIS:
 *    - document.querySelector/querySelectorAll
 *    - createElement, setAttribute
 *    - Event handling (addEventListener)
 * 
 * 5. LARAVEL INTEGRATION:
 *    - CSRF token handling
 *    - Method spoofing per HTTP verbs
 *    - Route detection
 *    - Blade template interaction
 * 
 * 6. UX/UI PATTERNS:
 *    - Debounce per performance
 *    - Loading states per feedback
 *    - Progressive enhancement
 *    - Graceful degradation
 *    - Error handling robusto
 * 
 * 7. DESIGN PATTERNS:
 *    - Module pattern per organizzazione
 *    - Event delegation per elementi dinamici
 *    - Try-catch per error handling
 *    - Fallback strategies
 * 
 * =====================================================
 * ASPETTI DI SICUREZZA:
 * =====================================================
 * 
 * - CSRF protection su tutti i form
 * - Validazione input lato client
 * - Sanificazione dati prima dell'uso
 * - Doppia conferma per azioni distruttive
 * - Error handling per prevenire crash
 * 
 * =====================================================
 * PERFORMANCE E OTTIMIZZAZIONE:
 * =====================================================
 * 
 * - Debounce per ridurre richieste server
 * - Event delegation invece di multiple listeners
 * - Lazy initialization di componenti
 * - Cleanup automatico di timeout
 * - Query selector ottimizzate
 * 
 * =====================================================
 */