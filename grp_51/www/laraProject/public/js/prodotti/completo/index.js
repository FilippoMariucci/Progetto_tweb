/**
 * ====================================================================
 * FILE: prodotti-completo-index.js
 * LINGUAGGIO: JavaScript + jQuery
 * FRAMEWORK: jQuery 3.7, Bootstrap 5.3
 * SCOPO: Gestione interfaccia utente per il catalogo prodotti completo
 *        utilizzato dai tecnici (Livello 2) nel sistema di assistenza
 * ====================================================================
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Gestione form di ricerca con wildcard (*)
 * - Filtri categoria dinamici
 * - Caricamento lazy delle immagini (performance)
 * - Evidenziazione termini di ricerca
 * - Sistema di notifiche toast
 * - Analisi performance e monitoraggio
 * - Gestione errori immagini con fallback
 * - Shortcuts tastiera per ricerca rapida
 * - Sistema tooltip informativi
 * - Animazioni contatori statistiche
 */

// ========================================================================
// INIZIALIZZAZIONE PRINCIPALE - EVENT LISTENER DOM READY
// ========================================================================

/**
 * jQuery Document Ready Function
 * Si esegue quando il DOM è completamente caricato
 * Linguaggio: jQuery JavaScript
 */
$(document).ready(function() {
    // Log di debug - utile per il troubleshooting
    console.log('prodotti.completo.index caricato');
    
    // ====================================================================
    // CONTROLLO ROUTE SPECIFICO - PATTERN SECURITY
    // ====================================================================
    
    /**
     * Verifica che questo script sia eseguito solo nella pagina corretta
     * Previene conflitti tra script di pagine diverse
     * window.LaravelApp è un oggetto globale definito nel layout principale
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.completo.index') {
        // Se non siamo nella route corretta, interrompe l'esecuzione
        return;
    }
    
    // ====================================================================
    // INIZIALIZZAZIONE VARIABILI GLOBALI
    // ====================================================================
    
    /**
     * Recupera dati dalla pagina passati da Laravel Blade tramite window.PageData
     * Questi dati vengono inseriti nel template Blade con @json()
     */
    const pageData = window.PageData || {};
    
    /**
     * Array per memorizzare prodotti selezionati (per future funzionalità)
     * Può essere utilizzato per selezioni multiple, confronti, etc.
     */
    let selectedProducts = [];
    
    // Log di debug con statistiche della pagina
    console.log('📦 Catalogo Tecnico Compatto caricato');
    console.log('📊 Prodotti visualizzati:', window.PageData.prodottiCount);
    
    // ====================================================================
    // GESTIONE FORM DI RICERCA
    // ====================================================================
    
    /**
     * Pulsante "Cancella ricerca" - jQuery Event Handler
     * Linguaggio: jQuery JavaScript
     * Scopo: Pulisce il campo ricerca e gli dà il focus
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    /**
     * Auto-submit form quando cambiano categoria o filtri
     * Linguaggio: jQuery JavaScript
     * Event: 'change' sui select dropdown
     */
    $('#categoria, #filter').on('change', function() {
        // $(this) si riferisce all'elemento che ha scatenato l'evento
        // .closest() trova il primo antenato che corrisponde al selettore
        // .submit() invia il form via HTTP POST/GET
        $(this).closest('form').submit();
    });
    
    // ====================================================================
    // SHORTCUTS TASTIERA - ACCESSIBILITY & UX
    // ====================================================================
    
    /**
     * Gestione scorciatoie da tastiera
     * Linguaggio: JavaScript vanilla + jQuery
     * Event: 'keydown' sul documento
     */
    $(document).on('keydown', function(e) {
        // Ctrl+K o Cmd+K (Mac) per focus rapido sulla ricerca
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault(); // Impedisce comportamento default del browser
            $('#search').focus(); // Sposta focus sul campo ricerca
        }
        // ESC per togliere focus dalla ricerca
        if (e.key === 'Escape') {
            $('#search').blur(); // Rimuove focus
        }
    });
    
    // ====================================================================
    // GESTIONE ERRORI IMMAGINI - GRACEFUL DEGRADATION
    // ====================================================================
    
    /**
     * Fallback per immagini non trovate
     * Linguaggio: jQuery JavaScript
     * Event: 'error' sulle immagini con classe .product-image
     */
    $('.product-image').on('error', function() {
        const $this = $(this); // Cache del riferimento jQuery
        const productName = $this.attr('alt') || 'Prodotto'; // Recupera nome prodotto
        
        // Sostituisce l'immagine rotta con un placeholder HTML
        $this.replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 140px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                    <div class="small text-muted mt-1">${productName}</div>
                </div>
            </div>
        `);
    });
    
    // ====================================================================
    // LAZY LOADING IMMAGINI - PERFORMANCE OPTIMIZATION
    // ====================================================================
    
    /**
     * Intersection Observer API per lazy loading
     * Linguaggio: JavaScript moderno (ES6+)
     * Scopo: Carica immagini solo quando sono visibili nel viewport
     */
    if ('IntersectionObserver' in window) {
        // Crea un observer che monitora quando elementi entrano nel viewport
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Se l'elemento è visibile (intersecting)
                if (entry.isIntersecting) {
                    const img = entry.target;
                    // Se ha attributo data-src, carica l'immagine
                    if (img.dataset.src) {
                        img.src = img.dataset.src; // Carica immagine vera
                        img.classList.remove('lazy'); // Rimuove classe CSS lazy
                        imageObserver.unobserve(img); // Smette di osservare questo elemento
                    }
                }
            });
        });
        
        // Applica l'observer a tutte le immagini lazy
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // ====================================================================
    // EVIDENZIAZIONE TERMINI DI RICERCA - SEARCH HIGHLIGHTING
    // ====================================================================
    
    /**
     * Evidenzia i termini di ricerca nei risultati
     * Linguaggio: jQuery + Regular Expressions JavaScript
     */
    const searchTerm = window.PageData.searchTerm || '';
    if (searchTerm && searchTerm.length > 2 && !searchTerm.includes('*')) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            // Regex per trovare il termine di ricerca (case insensitive)
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&')})`, 'gi');
            // Sostituisce con versione evidenziata usando <mark>
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }
    
    // ====================================================================
    // TOOLTIP BOOTSTRAP - USER EXPERIENCE
    // ====================================================================
    
    /**
     * Inizializza tooltip di Bootstrap 5
     * Linguaggio: jQuery + Bootstrap JavaScript
     */
    $('[data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover', // Mostra al passaggio del mouse
        placement: 'top'  // Posizione sopra l'elemento
    });
    
    // ====================================================================
    // LOADING STATE PER FORM - FEEDBACK UTENTE
    // ====================================================================
    
    /**
     * Mostra stato di caricamento durante submit del form
     * Linguaggio: jQuery JavaScript
     * Event: 'submit' sui form
     */
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        if ($submitBtn.length) {
            const originalText = $submitBtn.html(); // Salva testo originale
            // Cambia testo e disabilita pulsante
            $submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                      .prop('disabled', true);
            
            // Ripristina dopo 3 secondi (fallback)
            setTimeout(() => {
                $submitBtn.html(originalText).prop('disabled', false);
            }, 3000);
        }
    });
    
    // ====================================================================
    // ANALYTICS E MONITORAGGIO - DATA COLLECTION
    // ====================================================================
    
    /**
     * Log analytics delle ricerche effettuate
     * Linguaggio: JavaScript
     * Scopo: Monitoraggio utilizzo per miglioramenti futuri
     */
    if (window.PageData.searchTerm || window.PageData.categoria || window.PageData.filtro) {
        console.log('🔍 Ricerca tecnica effettuata:', {
            termine: window.PageData.searchTerm,
            categoria: window.PageData.categoria,
            filtro: window.PageData.filtro,
            risultati: window.PageData.prodottiTotal,
            staff_filter: window.PageData.staffFilter,
            timestamp: new Date().toISOString()
        });
    }
    
    // ====================================================================
    // AUTO-REFRESH PER PROBLEMI CRITICI - REAL-TIME MONITORING
    // ====================================================================
    
    /**
     * Auto-refresh per problemi critici ogni 5 minuti
     * Linguaggio: JavaScript
     * Utilizza: setInterval API
     */
    if (window.PageData.filtro === 'critici') {
        setInterval(() => {
            console.log('🔄 Auto-refresh per problemi critici');
            // location.reload(); // Decommentare per abilitare refresh automatico
        }, 300000); // 300000ms = 5 minuti
    }
    
    // ====================================================================
    // ANIMAZIONI CONTATORI - VISUAL EFFECTS
    // ====================================================================
    
    /**
     * Anima i contatori numerici con effetto counting
     * Linguaggio: jQuery + jQuery.animate()
     * Timeout per permettere il rendering iniziale
     */
    setTimeout(() => {
        $('.fw-bold').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            const target = parseInt(text);
            
            // Solo per numeri validi e non troppo grandi
            if (!isNaN(target) && target > 0 && target < 100) {
                $counter.text('0'); // Inizia da 0
                
                // Anima da 0 al valore target
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1000,  // 1 secondo
                    easing: 'swing',  // Tipo di easing
                    step: function() {
                        // Ad ogni step dell'animazione
                        $counter.text(Math.ceil(this.counter));
                    },
                    complete: function() {
                        // Al completamento, assicura il valore finale
                        $counter.text(target);
                    }
                });
            }
        });
    }, 300); // 300ms di ritardo iniziale
    
    // ====================================================================
    // EFFETTI HOVER SU CARD PRODOTTI - INTERACTIVITY
    // ====================================================================
    
    /**
     * Effetti hover sulle card dei prodotti
     * Linguaggio: jQuery
     * Events: 'mouseenter' e 'mouseleave'
     */
    $('.product-card').hover(
        function() {
            // Mouse enter: aggiungi ombra
            $(this).addClass('shadow-lg');
        },
        function() {
            // Mouse leave: rimuovi ombra
            $(this).removeClass('shadow-lg');
        }
    );
    
    // ====================================================================
    // CONFERME AZIONI - SAFETY & CONFIRMATION
    // ====================================================================
    
    /**
     * Sistema di conferma per azioni critiche
     * Linguaggio: jQuery JavaScript
     * Utilizza: window.confirm() native API
     */
    $('[data-confirm]').on('click', function(e) {
        const message = $(this).data('confirm') || 'Sei sicuro di voler procedere?';
        if (!confirm(message)) {
            e.preventDefault(); // Annulla l'azione
            return false;
        }
    });
    
    // ====================================================================
    // SISTEMA NOTIFICHE - USER FEEDBACK
    // ====================================================================
    
    /**
     * Gestione notifiche di sessione da Laravel
     * I messaggi vengono passati via window.PageData dal controller Blade
     */
    if (window.PageData.sessionSuccess) {
        showNotification(window.PageData.sessionSuccess, 'success');
    }
    if (window.PageData.sessionError) {
        showNotification(window.PageData.sessionError, 'error');
    }
    if (window.PageData.sessionWarning) {
        showNotification(window.PageData.sessionWarning, 'warning');
    }
    
    /**
     * Funzione per mostrare notifiche toast
     * Linguaggio: jQuery + Bootstrap CSS
     * @param {string} message - Messaggio da mostrare
     * @param {string} type - Tipo: success, error, warning, info
     */
    function showNotification(message, type = 'info') {
        // Mappa tipi a classi CSS Bootstrap
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        // Mappa tipi a icone Bootstrap Icons
        const icon = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-triangle', 
            'info': 'info-circle'
        }[type] || 'info-circle';
        
        // Crea elemento notifica HTML
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;" 
                 role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Aggiunge al DOM
        $('body').append(notification);
        
        // Auto-rimuovi dopo 4 secondi
        setTimeout(() => {
            notification.alert('close');
        }, 4000);
    }
    
    // Rende la funzione disponibile globalmente per altri script
    window.showNotification = showNotification;
    
    // ====================================================================
    // PERFORMANCE MONITORING - METRICS COLLECTION
    // ====================================================================
    
    /**
     * Raccolta metriche di performance
     * Linguaggio: JavaScript
     * Scopo: Monitoraggio prestazioni applicazione
     */
    const performanceData = {
        loadTime: Date.now(),
        totalProducts: window.PageData.prodottiTotal,
        displayedProducts: window.PageData.prodottiCount,
        searchActive: window.PageData.searchActive,
        filtersActive: window.PageData.filtersActive
    };
    
    console.log('📊 Performance Data:', performanceData);
    
    // ====================================================================
    // CLEANUP RISORSE - MEMORY MANAGEMENT
    // ====================================================================
    
    /**
     * Cleanup quando si abbandona la pagina
     * Linguaggio: jQuery JavaScript
     * Event: 'beforeunload' window
     * Scopo: Evitare memory leaks
     */
    $(window).on('beforeunload', function() {
        // Pulisce tooltip per evitare memory leak
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        console.log('🧹 Cleanup completato');
    });
    
    console.log('✅ Catalogo Tecnico Compatto completamente caricato');
});

// ========================================================================
// FUNZIONI GLOBALI UTILITY - GLOBAL SCOPE
// ========================================================================

/**
 * Filtra prodotti per categoria via JavaScript (navigazione)
 * Linguaggio: JavaScript vanilla
 * @param {string} categoria - Nome categoria da filtrare
 */
function filterByCategory(categoria) {
    if (categoria) {
        // Costruisce URL con parametro categoria
        window.location.href = `{{ route('prodotti.completo.index') }}?categoria=${categoria}`;
    } else {
        // Se categoria vuota, rimuove filtro
        window.location.href = `{{ route('prodotti.completo.index') }}`;
    }
}

/**
 * Ricerca rapida senza submit manuale
 * Linguaggio: JavaScript + DOM manipulation
 * @param {string} term - Termine di ricerca
 */
function quickSearch(term) {
    if (term.length > 2) {
        $('#search').val(term); // Popola campo
        $('form').submit();     // Invia form
    }
}

/**
 * Toggle filtro per staff (gestione prodotti assegnati)
 * Linguaggio: JavaScript moderno (URL API)
 * @param {string} filter - Tipo filtro da applicare
 */
function toggleStaffFilter(filter) {
    const currentUrl = new URL(window.location.href);
    
    if (filter === 'my_products') {
        // Aggiunge parametro staff_filter
        currentUrl.searchParams.set('staff_filter', 'my_products');
    } else {
        // Rimuove parametro
        currentUrl.searchParams.delete('staff_filter');
    }
    
    window.location.href = currentUrl.toString();
}

/**
 * Reset completo di tutti i filtri applicati
 * Linguaggio: JavaScript
 */
function resetAllFilters() {
    window.location.href = `{{ route('prodotti.completo.index') }}`;
}

/**
 * Evidenzia un prodotto specifico (per link diretti)
 * Linguaggio: jQuery + CSS manipulation + Smooth scroll
 * @param {number} productId - ID del prodotto da evidenziare
 */
function highlightProduct(productId) {
    const $card = $(`.product-card[data-product-id="${productId}"]`);
    if ($card.length) {
        // Aggiunge bordo colorato e ombra
        $card.addClass('border-primary border-3 shadow-lg');
        // Scroll smooth verso l'elemento
        $card[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Rimuove evidenziazione dopo 3 secondi
        setTimeout(() => {
            $card.removeClass('border-primary border-3');
        }, 3000);
    }
}

/**
 * Sistema di tracking analytics (opzionale, per Google Analytics)
 * Linguaggio: JavaScript
 * @param {string} action - Azione da tracciare
 * @param {Object} details - Dettagli aggiuntivi
 */
function trackUsage(action, details = {}) {
    // Se Google Analytics è disponibile
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            ...details,
            page_title: 'Catalogo Tecnico Completo',
            page_location: window.location.href
        });
    }
    
    console.log('📈 Azione tracciata:', action, details);
}

// ========================================================================
// EVENT LISTENERS GLOBALI - DOCUMENT LEVEL
// ========================================================================

/**
 * Track click sui link dei prodotti per analytics
 * Linguaggio: jQuery event delegation
 * Event delegation: gestisce anche elementi aggiunti dinamicamente
 */
$(document).on('click', '.product-card a', function() {
    const productName = $(this).closest('.product-card').find('.card-title').text();
    trackUsage('product_view', {
        product_name: productName,
        view_type: 'tecnico_completo'
    });
});

/**
 * Track submit form ricerche per analytics
 * Linguaggio: jQuery event delegation
 */
$(document).on('submit', 'form', function() {
    const searchTerm = $('#search').val();
    const categoria = $('#categoria').val();
    
    if (searchTerm || categoria) {
        trackUsage('search_performed', {
            search_term: searchTerm,
            category: categoria,
            results_count: window.PageData.prodottiTotal
        });
    }
});

/**
 * ====================================================================
 * RIEPILOGO TECNOLOGIE UTILIZZATE:
 * ====================================================================
 * 
 * 1. JQUERY 3.7:
 *    - Event handling: .on(), .click(), .submit()
 *    - DOM manipulation: .html(), .text(), .addClass()
 *    - AJAX ready: $(document).ready()
 *    - Selettori CSS: $('.class'), $('#id')
 *    - Animazioni: .animate()
 * 
 * 2. JAVASCRIPT ES6+:
 *    - Arrow functions: () => {}
 *    - Template literals: `string ${variable}`
 *    - Const/let declarations
 *    - Object destructuring
 *    - Modern APIs: IntersectionObserver, URL
 * 
 * 3. BOOTSTRAP 5.3:
 *    - CSS Framework per styling
 *    - JavaScript per tooltip, alert
 *    - Classi responsive e utility
 * 
 * 4. BROWSER APIs:
 *    - Console API per debugging
 *    - Local/Session Storage per persistenza
 *    - Intersection Observer per lazy loading
 *    - Navigation API per routing
 * 
 * 5. LARAVEL INTEGRATION:
 *    - Blade template data injection
 *    - Route generation
 *    - CSRF token management
 *    - Session flash messages
 * 
 * ====================================================================
 * PATTERNS UTILIZZATI:
 * ====================================================================
 * 
 * 1. MODULE PATTERN: Script isolato per pagina specifica
 * 2. EVENT DELEGATION: Gestione eventi anche per elementi dinamici  
 * 3. GRACEFUL DEGRADATION: Fallback per funzionalità non supportate
 * 4. PROGRESSIVE ENHANCEMENT: Miglioramenti incrementali UX
 * 5. SEPARATION OF CONCERNS: Logica separata da presentazione
 * 6. ERROR HANDLING: Gestione errori con fallback
 * 7. PERFORMANCE OPTIMIZATION: Lazy loading, caching selettori
 * 8. ACCESSIBILITY: Shortcuts tastiera, ARIA attributes
 */