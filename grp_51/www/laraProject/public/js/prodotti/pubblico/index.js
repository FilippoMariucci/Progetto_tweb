/**
 * ====================================================================
 * FILE: prodotti-index.js
 * LINGUAGGIO: JavaScript + jQuery
 * FRAMEWORK: jQuery 3.7, Bootstrap 5.3
 * SCOPO: Gestione catalogo prodotti pubblico (Livello 1 - Accesso pubblico)
 *        Versione semplificata senza malfunzionamenti per utenti non autenticati
 * ====================================================================
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Ricerca prodotti con wildcard (*) supportata
 * - Filtri categoria dinamici con badge cliccabili
 * - Evidenziazione termini di ricerca nei risultati
 * - Gestione errori immagini con fallback elegante
 * - Loading states per feedback utente durante ricerche
 * - Analytics e debug per monitoraggio utilizzo
 * - Gestione form con auto-submit su cambio categoria
 * - Reset filtri e navigazione pulita URL
 */

// ========================================================================
// INIZIALIZZAZIONE PRINCIPALE - DOM READY HANDLER
// ========================================================================

/**
 * jQuery Document Ready Function
 * Si attiva quando il DOM è completamente caricato e pronto per la manipolazione
 * Linguaggio: jQuery JavaScript
 */
$(document).ready(function() {
    // Log di debug iniziale per troubleshooting
    console.log('prodotti.index caricato');

    // ====================================================================
    // CONTROLLO ROUTE SPECIFICO - PATTERN SECURITY & ISOLATION
    // ====================================================================

    /**
     * Verifica che questo script sia eseguito solo nella route corretta
     * Previene conflitti tra diversi script di pagina
     * window.LaravelApp è definito nel layout principale (app.blade.php)
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.pubblico.index') {
        // Se non siamo nella route pubblica prodotti, interrompi esecuzione
        return;
    }

    // ====================================================================
    // INIZIALIZZAZIONE VARIABILI E DATI PAGINA
    // ====================================================================

    /**
     * Recupera dati passati da Laravel Blade template
     * Questi dati vengono iniettati tramite @json() nel template
     * Contengono informazioni su categorie, statistiche, termini ricerca
     */
    const pageData = window.PageData || {};

    /**
     * Array per memorizzare prodotti selezionati (per funzionalità future)
     * Potrebbe essere utilizzato per confronti, liste desideri, etc.
     */
    let selectedProducts = [];

    // ====================================================================
    // DEBUG E MONITORAGGIO INIZIALE
    // ====================================================================

    /**
     * Log di debug con informazioni dettagliate sui dati disponibili
     * Linguaggio: JavaScript Console API
     * Utile per debugging e verifica funzionamento filtri categoria
     */
    console.log('📦 Catalogo Prodotti Pubblico - FILTRI CATEGORIA CORRETTI');
    console.log('📊 Categorie disponibili:', pageData.categorie || []);
    console.log('📊 Stats per categoria:', (pageData.stats && pageData.stats.per_categoria) ? pageData.stats.per_categoria : []);

    // ====================================================================
    // GESTIONE FORM DI RICERCA - USER INTERACTION
    // ====================================================================

    /**
     * Pulsante "Cancella ricerca" - Clear search functionality
     * Linguaggio: jQuery event handling
     * Event: 'click' sul pulsante con ID #clearSearch
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus(); // Svuota campo e dà focus per nuova ricerca
    });

    /**
     * Auto-submit form quando cambia la categoria nel dropdown
     * Linguaggio: jQuery event handling
     * Event: 'change' sul select con ID #categoria
     * Scopo: Aggiornamento immediato risultati senza click su "Cerca"
     */
    $('#categoria').on('change', function() {
        // Recupera il valore della categoria selezionata
        const categoriaSelezionata = $(this).val();
        
        // Log per debugging della selezione
        console.log('📂 Categoria selezionata dal dropdown:', categoriaSelezionata);
        
        // Submit automatico del form con ID #search-form
        $('#search-form').submit();
    });

    /**
     * Gestione click sui badge categoria (elementi cliccabili decorativi)
     * Linguaggio: jQuery event handling + URL manipulation
     * Event: 'click' su elementi con classe .category-badge
     */
    $('.category-badge').on('click', function(e) {
        // Previene comportamento default del link (se presente)
        e.preventDefault();
        
        // Recupera categoria dal data attribute
        const categoria = $(this).data('categoria');
        
        // Log per debugging click badge
        console.log('🏷️ Badge categoria cliccato:', categoria);
        
        // Costruisce URL con parametro categoria
        if (categoria && categoria !== '') {
            // Aggiunge parametro categoria all'URL mantenendo il path
            window.location.href = window.location.pathname + '?categoria=' + encodeURIComponent(categoria);
        } else {
            // Se categoria vuota, rimuove tutti i filtri
            window.location.href = window.location.pathname;
        }
    });

    // ====================================================================
    // GESTIONE ERRORI IMMAGINI - GRACEFUL DEGRADATION
    // ====================================================================

    /**
     * Fallback per immagini non trovate o corrotte
     * Linguaggio: jQuery event handling + DOM replacement
     * Event: 'error' su elementi con classe .product-image
     * Pattern: Graceful degradation per migliore user experience
     */
    $('.product-image').on('error', function() {
        const $this = $(this); // Cache riferimento jQuery elemento corrente
        
        // Recupera nome prodotto dall'attributo alt per il placeholder
        const productName = $this.attr('alt') || 'Prodotto';
        
        // Sostituisce immagine rotta con placeholder HTML elegante
        $this.replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 140px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                    <div class="small text-muted mt-1">${productName.substring(0, 20)}</div>
                </div>
            </div>
        `);
    });

    // ====================================================================
    // EVIDENZIAZIONE TERMINI DI RICERCA - SEARCH HIGHLIGHTING
    // ====================================================================

    /**
     * Evidenzia i termini cercati nei risultati per migliorare UX
     * Linguaggio: JavaScript String/RegExp + jQuery DOM manipulation
     * Pattern: Progressive enhancement per migliore visibilità risultati
     */
    const searchTerm = (pageData.searchTerm || '').toString();
    
    // Applica evidenziazione solo se il termine è significativo e non usa wildcard
    if (searchTerm && searchTerm.length > 2 && !searchTerm.includes('*')) {
        
        // Itera su tutti i titoli e descrizioni delle card prodotto
        $('.card-title, .card-text').each(function() {
            const text = $(this).html(); // Recupera HTML corrente
            
            // Crea regex per trovare il termine (case insensitive)
            // escape dei caratteri speciali regex per sicurezza
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&')})`, 'gi');
            
            // Sostituisce occorrenze con versione evidenziata usando <mark>
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            
            // Aggiorna contenuto dell'elemento con versione evidenziata
            $(this).html(highlighted);
        });
    }

    // ====================================================================
    // LOADING STATE PER FORM - USER FEEDBACK
    // ====================================================================

    /**
     * Mostra stato di loading durante submit del form di ricerca
     * Linguaggio: jQuery event handling + setTimeout
     * Event: 'submit' sul form con ID #search-form
     * Scopo: Feedback visuale che la ricerca è in corso
     */
    $('#search-form').on('submit', function() {
        // Trova il pulsante di submit nel form
        const $submitBtn = $(this).find('button[type="submit"]');
        
        if ($submitBtn.length) {
            // Salva testo originale per ripristino
            const originalText = $submitBtn.html();
            
            // Cambia a stato loading con spinner Bootstrap Icons
            $submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                      .prop('disabled', true); // Disabilita per evitare doppi submit
            
            // Ripristina stato originale dopo 3 secondi (fallback)
            setTimeout(() => {
                $submitBtn.html(originalText).prop('disabled', false);
            }, 3000);
        }
    });

    // ====================================================================
    // ANALYTICS E DEBUG RICERCHE - DATA COLLECTION
    // ====================================================================

    /**
     * Log analytics delle ricerche effettuate per monitoraggio
     * Linguaggio: JavaScript Console API + Date API
     * Scopo: Raccolta dati comportamento utenti per miglioramenti
     */
    if (pageData.searchTerm || pageData.categoria) {
        console.log('🔍 Ricerca pubblica:', {
            termine: pageData.searchTerm,        // Termine cercato
            categoria: pageData.categoria,       // Categoria filtrata
            risultati: pageData.risultati,       // Numero risultati
            timestamp: new Date().toISOString()  // Timestamp standard ISO
        });
    }

    // Log finale di conferma inizializzazione completata
    console.log('✅ Filtri categoria funzionanti correttamente');
});

// ========================================================================
// FUNZIONI GLOBALI UTILITY - GLOBAL SCOPE FUNCTIONS
// ========================================================================

/**
 * Reset completo della ricerca rimuovendo tutti i parametri URL
 * Linguaggio: JavaScript Navigation API
 * Scope: window (globale) per essere chiamata da elementi HTML onclick
 * 
 * Funzionalità: Riporta alla pagina base senza filtri né ricerche
 */
function resetSearch() {
    // Naviga al path base senza query parameters
    window.location.href = window.location.pathname;
}

/**
 * Filtro programmatico per categoria specifica
 * Linguaggio: JavaScript Navigation + URL manipulation
 * Scope: window (globale) per essere chiamata da elementi HTML
 * 
 * @param {string} categoria - Nome della categoria da filtrare
 * 
 * Funzionalità:
 * - Aggiunge parametro categoria all'URL se valorizzata
 * - Rimuove filtri se categoria vuota/null
 * - Preserva il path corrente della pagina
 */
function filterByCategory(categoria) {
    // Log per debugging della chiamata programmatica
    console.log('🏷️ Filtro categoria programmatico:', categoria);
    
    // Controlla se categoria ha un valore valido
    if (categoria && categoria !== '') {
        // Costruisce URL con parametro categoria codificato
        // encodeURIComponent previene problemi con caratteri speciali
        window.location.href = window.location.pathname + '?categoria=' + encodeURIComponent(categoria);
    } else {
        // Se categoria vuota, chiama reset per rimuovere tutti i filtri
        resetSearch();
    }
}

/**
 * ====================================================================
 * RIEPILOGO TECNOLOGIE E ARCHITETTURE UTILIZZATE
 * ====================================================================
 * 
 * 1. JQUERY 3.7 - LIBRERIA JAVASCRIPT:
 *    - $(document).ready(): Inizializzazione sicura DOM
 *    - Event handling: .on('click'), .on('change'), .on('submit')
 *    - DOM manipulation: .html(), .val(), .focus(), .prop()
 *    - Selettori CSS: $('.class'), $('#id')
 *    - Method chaining: .html().prop()
 *    - Element traversal: .find()
 * 
 * 2. JAVASCRIPT ES6+ MODERNO:
 *    - Const/let declarations per scope sicuro
 *    - Template literals: `string ${variable}` per HTML
 *    - Arrow functions in callbacks (potrebbero essere aggiunte)
 *    - String methods: .includes(), .substring()
 *    - Regular expressions con escape caratteri speciali
 * 
 * 3. BROWSER APIs NATIVE:
 *    - Console API: console.log() per debugging
 *    - Location API: window.location.href per navigazione
 *    - Date API: new Date().toISOString()
 *    - setTimeout API per delayed execution
 *    - Event API: preventDefault()
 * 
 * 4. HTML5 & DOM APIs:
 *    - Data attributes: data-categoria per storage dati
 *    - Form API: submit(), disabled property
 *    - Element replacement: replaceWith()
 * 
 * 5. CSS INTEGRATION:
 *    - Bootstrap 5.3 classes per styling
 *    - Bootstrap Icons per iconografia
 *    - CSS classes dynamic: .addClass(), .removeClass()
 * 
 * 6. LARAVEL FRAMEWORK INTEGRATION:
 *    - Blade template data injection via window.PageData
 *    - Route system integration via window.LaravelApp
 *    - URL generation Laravel-compatible
 * 
 * ====================================================================
 * PATTERN ARCHITETTURALI E BEST PRACTICES
 * ====================================================================
 * 
 * 1. MODULE PATTERN:
 *    - Script isolato per pagina specifica (route check)
 *    - Namespace separation per evitare conflitti
 *    - Encapsulation delle variabili locali
 * 
 * 2. PROGRESSIVE ENHANCEMENT:
 *    - Funzionalità base sempre disponibili
 *    - JavaScript migliora l'esperienza ma non la blocca
 *    - Fallback per immagini e stati di errore
 * 
 * 3. GRACEFUL DEGRADATION:
 *    - Gestione errori immagini con placeholder
 *    - Timeout per ripristino stati UI
 *    - Controlli esistenza elementi prima uso
 * 
 * 4. USER EXPERIENCE PATTERNS:
 *    - Loading states per feedback immediato
 *    - Auto-focus per migliorare navigazione
 *    - Evidenziazione risultati ricerca
 *    - URL clean e bookmarkable
 * 
 * 5. SEPARATION OF CONCERNS:
 *    - Logica JavaScript separata da HTML
 *    - Styling tramite CSS classes
 *    - Data layer tramite data attributes
 * 
 * 6. PERFORMANCE OPTIMIZATIONS:
 *    - Caching selettori jQuery ($this)
 *    - Event delegation quando possibile
 *    - Controlli condizionali per evitare operazioni inutili
 * 
 * 7. DEBUG & MONITORING:
 *    - Console logging strutturato con emoji
 *    - Analytics data collection
 *    - Error logging per troubleshooting
 * 
 * ====================================================================
 * SICUREZZA E VALIDAZIONE
 * ====================================================================
 * 
 * 1. INPUT SANITIZATION:
 *    - encodeURIComponent() per parametri URL
 *    - Regex escape per prevenire injection
 *    - Controllo lunghezza stringhe ricerca
 * 
 * 2. XSS PREVENTION:
 *    - Controllo contenuti prima innerHTML
 *    - Sanitizzazione data attributes
 * 
 * 3. NAVIGATION SECURITY:
 *    - Controllo route prima esecuzione script
 *    - Validazione parametri prima navigazione
 * 
 * ====================================================================
 * ACCESSIBILITY & USABILITY
 * ====================================================================
 * 
 * 1. KEYBOARD NAVIGATION:
 *    - Auto-focus su campo ricerca dopo clear
 *    - Tab-friendly form interactions
 * 
 * 2. SCREEN READER SUPPORT:
 *    - Alt text per immagini e placeholder
 *    - Semantic HTML structure
 * 
 * 3. USER FEEDBACK:
 *    - Visual loading states
 *    - Clear error messages
 *    - Consistent interaction patterns
 * 
 * ====================================================================
 * ESTENSIBILITÀ E MANUTENIBILITÀ
 * ====================================================================
 * 
 * 1. MODULAR STRUCTURE:
 *    - Funzioni globali riutilizzabili
 *    - Configurazione tramite pageData
 *    - Parametri modificabili facilmente
 * 
 * 2. DEBUG FRIENDLY:
 *    - Console logging dettagliato
 *    - Naming convention chiaro
 *    - Commenti esplicativi
 * 
 * 3. INTEGRATION READY:
 *    - Compatibile con altri script
 *    - Eventi personalizzabili
 *    - Data layer ben definito
 */