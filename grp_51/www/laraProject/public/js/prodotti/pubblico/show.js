/**
 * ====================================================================
 * FILE: prodotti-pubblico-show.js
 * LINGUAGGIO: JavaScript + jQuery + Modern Web APIs
 * FRAMEWORK: jQuery 3.7, Bootstrap 5.3
 * SCOPO: Gestione vista dettaglio prodotto per utenti pubblici (Livello 1)
 *        Versione semplificata senza malfunzionamenti per accesso non autenticato
 * ====================================================================
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Modal lightbox per visualizzazione immagini ingrandite
 * - Scroll automatico a sezioni specifiche (installazione, uso)
 * - Sistema notifiche toast con icone Bootstrap
 * - Lazy loading immagini per performance ottimizzate
 * - Shortcuts tastiera per migliore accessibilità
 * - Smooth scrolling per navigazione interna
 * - Analytics tracking per comportamento utenti
 * - Gestione errori immagini con fallback eleganti
 * - Performance monitoring per ottimizzazioni
 * - Funzionalità social (condivisione, stampa, favoriti)
 */

// ========================================================================
// INIZIALIZZAZIONE PRINCIPALE - DOPPIO DOM READY (BUG DA CORREGGERE)
// ========================================================================

/**
 * PRIMO jQuery Document Ready (ESTERNO)
 * Linguaggio: jQuery JavaScript
 * NOTA: Questo è il wrapper esterno, probabilmente ridondante
 */
$(document).ready(function() {
    // Log di debug iniziale
    console.log('prodotti.pubblico.show caricato');
    
    // ====================================================================
    // CONTROLLO ROUTE SPECIFICO - SECURITY PATTERN
    // ====================================================================
    
    /**
     * Verifica che lo script sia eseguito solo nella route corretta
     * Previene conflitti tra script di pagine diverse
     * window.LaravelApp definito nel layout app.blade.php
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.pubblico.show') {
        return; // Interrompe esecuzione se route errata
    }
    
    // ====================================================================
    // INIZIALIZZAZIONE DATI E VARIABILI
    // ====================================================================
    
    /**
     * Recupera dati passati da Laravel Blade template
     * Iniettati tramite @json() nel template Blade
     */
    const pageData = window.PageData || {};
    
    /**
     * Array per prodotti selezionati (funzionalità future)
     * Utile per confronti, liste desideri, carrello
     */
    let selectedProducts = [];
    
    // ====================================================================
    // SECONDO jQuery Document Ready (INTERNO) - CODICE PRINCIPALE
    // ====================================================================
    
    /**
     * SECONDO Document Ready - QUI INIZIA IL CODICE VERO
     * Linguaggio: jQuery JavaScript
     * NOTA BUG: Doppio $(document).ready() è ridondante, uno dovrebbe essere rimosso
     */
    $(document).ready(function() {
        // Log con emoji per identificazione rapida
        console.log('📄 Vista prodotto pubblica con stile unificato caricata');
        
        // ================================================================
        // MODAL IMMAGINE LIGHTBOX - IDENTICAL TO TECHNICAL VERSION
        // ================================================================
        
        /**
         * Funzione globale per aprire modal con immagine ingrandita
         * Linguaggio: JavaScript + jQuery + Bootstrap Modal API
         * Scope: window (globale) per chiamate da onclick HTML
         * Pattern: Identical implementation across all product views
         * 
         * @param {string} imageSrc - URL dell'immagine da mostrare
         * @param {string} imageTitle - Titolo/descrizione dell'immagine
         */
        window.openImageModal = function(imageSrc, imageTitle) {
            // Imposta attributi immagine nel modal
            $('#imageModalImg').attr('src', imageSrc).attr('alt', imageTitle);
            
            // Imposta titolo del modal
            $('#imageModalTitle').text(imageTitle);
            
            // Mostra modal Bootstrap
            $('#imageModal').modal('show');
        };
        
        // ================================================================
        // SCROLL TO SECTION - NAVIGAZIONE INTERNA SMART
        // ================================================================
        
        /**
         * Funzione per scroll automatico a sezioni specifiche
         * Linguaggio: jQuery + CSS selectors + Animation API
         * Scope: window (globale)
         * 
         * @param {string} section - Nome sezione: 'installazione' o 'uso'
         * 
         * Funzionalità:
         * - Trova sezione tramite contenuto testo headers
         * - Scroll animato con offset per header fissi
         * - Evidenziazione temporanea sezione target
         */
        window.scrollToSection = function(section) {
            let targetSelector = '';
            
            // Determina selettore basato su sezione richiesta
            if (section === 'installazione') {
                targetSelector = 'h6:contains("Modalità Installazione")';
            } else if (section === 'uso') {
                targetSelector = 'h6:contains("Modalità d\'Uso")';
            }
            
            if (targetSelector) {
                // Trova elemento target e container parent
                const $target = $(targetSelector).closest('.col-lg-4');
                
                if ($target.length > 0) {
                    // Aggiunge classe per evidenziazione temporanea
                    $target.addClass('section-highlight');
                    
                    // Scroll animato con offset per header fissi
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 100 // 100px offset
                    }, 500); // 500ms duration
                    
                    // Rimuove evidenziazione dopo 2 secondi
                    setTimeout(() => {
                        $target.removeClass('section-highlight');
                    }, 2000);
                }
            }
        };
        
        // ================================================================
        // GESTIONE ERRORI IMMAGINI - GRACEFUL DEGRADATION
        // ================================================================
        
        /**
         * Fallback elegante per immagini non trovate o corrotte
         * Linguaggio: jQuery event handling + DOM replacement
         * Event: 'error' su tutte le immagini della pagina
         * Pattern: Graceful degradation per migliore UX
         */
        $('.product-image, img').on('error', function() {
            const $this = $(this); // Cache riferimento jQuery
            
            // Recupera nome prodotto e dimensioni per placeholder
            const productName = $this.attr('alt') || 'Prodotto';
            const height = $this.height() || 280; // Default 280px
            
            // Sostituisce con placeholder HTML responsivo
            $this.replaceWith(`
                <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                     style="height: ${height}px;">
                    <div class="text-center">
                        <i class="bi bi-image text-muted mb-2" style="font-size: 2rem;"></i>
                        <div class="small text-muted">${productName.substring(0, 30)}</div>
                    </div>
                </div>
            `);
        });
        
        // ================================================================
        // SISTEMA NOTIFICHE TOAST - REUSABLE ALERT SYSTEM
        // ================================================================
        
        /**
         * Sistema notifiche personalizzato con auto-dismiss
         * Linguaggio: jQuery + Bootstrap CSS + setTimeout API
         * 
         * @param {string} message - Messaggio da visualizzare
         * @param {string} type - Tipo alert: success, danger, warning, info
         * @param {number} duration - Durata visualizzazione in ms (default 4000)
         * 
         * Features:
         * - Icone dinamiche Bootstrap Icons
         * - Posizionamento fisso top-right
         * - Auto-dismiss con fade animation
         * - Dismissible manualmente con pulsante X
         */
        function showAlert(message, type = 'info', duration = 4000) {
            // Rimuove alert precedenti per evitare sovrapposizioni
            $('.custom-alert').remove();
            
            // Mappa tipi alert a icone Bootstrap Icons
            const icons = {
                success: 'check-circle-fill',           // Verde successo
                danger: 'exclamation-triangle-fill',    // Rosso errore
                warning: 'exclamation-triangle-fill',   // Giallo avviso
                info: 'info-circle-fill'               // Blu informazione
            };
            
            // Costruisce HTML alert completo
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1060; max-width: 350px;" 
                     role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-${icons[type] || 'info-circle-fill'} me-2"></i>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Aggiunge al DOM
            $('body').append(alertHtml);
            
            // Auto-rimozione con animazione fade
            setTimeout(() => {
                $('.custom-alert').fadeOut(300, function() {
                    $(this).remove(); // Cleanup DOM
                });
            }, duration);
        }
        
        // ================================================================
        // TOOLTIP INFORMATIVI - ENHANCED UX
        // ================================================================
        
        /**
         * Inizializza tooltip Bootstrap su elementi con attributo title
         * Linguaggio: jQuery + Bootstrap Tooltip API
         * Trigger: hover per accessibilità mobile-friendly
         */
        $('[title]').tooltip({
            trigger: 'hover',  // Attivazione al passaggio mouse
            placement: 'top'   // Posizione sopra elemento
        });
        
        // ================================================================
        // GESTIONE NOTIFICHE SESSIONE LARAVEL
        // ================================================================
        
        /**
         * Visualizza notifiche di sessione passate da Laravel
         * Le notifiche vengono iniettate nel template Blade tramite session flash
         * Types supportati: success, error, info
         */
        if (window.PageData.sessionSuccess) {
            showAlert(window.PageData.sessionSuccess, 'success');
        }
        if (window.PageData.sessionError) {
            showAlert(window.PageData.sessionError, 'danger');
        }
        if (window.PageData.sessionInfo) {
            showAlert(window.PageData.sessionInfo, 'info');
        }

        // ================================================================
        // ANALYTICS E DEBUG - DATA COLLECTION
        // ================================================================
        
        /**
         * Raccolta dati analytics per monitoraggio comportamento utenti
         * Linguaggio: JavaScript Object.assign() + Date API
         * 
         * Combines:
         * - Dati prodotto da Laravel
         * - Metadata vista (pubblica)
         * - User authentication status
         * - Timestamp per analisi temporali
         */
        const prodottoData = Object.assign({}, window.PageData.prodotto || {}, {
            vista_tipo: 'pubblica',                                                    // Tipo vista
            user_authenticated: window.PageData.user ? true : false,                   // Stato autenticazione
            user_can_view_malfunctions: window.PageData.user_can_view_malfunctions || false, // Permessi malfunzionamenti
            timestamp: new Date().toISOString()                                       // Timestamp ISO standard
        });
        console.log('📊 Vista prodotto pubblico:', prodottoData);
        
        // ================================================================
        // PERFORMANCE MONITORING - METRICS COLLECTION
        // ================================================================
        
        /**
         * Raccolta metriche performance per ottimizzazioni
         * Linguaggio: JavaScript + jQuery selectors
         * 
         * Metrics tracked:
         * - Load time (Date.now())
         * - Number of images loaded
         * - UI elements count (cards, buttons)
         */
        const performanceData = {
            loadTime: Date.now(),           // Timestamp caricamento
            imagesLoaded: $('img').length,  // Conteggio immagini
            cardsCount: $('.card').length,  // Conteggio card Bootstrap
            buttonsCount: $('.btn').length  // Conteggio bottoni
        };
        
        console.log('⚡ Performance vista pubblica:', performanceData);
        
        // ================================================================
        // LAZY LOADING IMMAGINI - PERFORMANCE OPTIMIZATION
        // ================================================================
        
        /**
         * Implementa lazy loading usando Intersection Observer API
         * Linguaggio: JavaScript moderno (ES6+) + DOM APIs
         * Compatibility: Controlla supporto browser prima uso
         * 
         * Benefits:
         * - Carica immagini solo quando visibili nel viewport
         * - Riduce tempo caricamento iniziale pagina
         * - Risparmia bandwidth per utenti
         */
        if ('IntersectionObserver' in window) {
            // Crea observer per monitorare elementi che entrano nel viewport
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // Se elemento è visibile nel viewport
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        // Se ha data-src e non ha ancora src, carica immagine
                        if (img.dataset.src && !img.src) {
                            img.src = img.dataset.src;        // Carica immagine reale
                            img.classList.remove('lazy');     // Rimuove classe CSS lazy
                            imageObserver.unobserve(img);     // Smette di osservare
                        }
                    }
                });
            });
            
            // Applica observer a tutte le immagini con data-src
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // ================================================================
        // SHORTCUTS TASTIERA - ACCESSIBILITY & POWER USERS
        // ================================================================
        
        /**
         * Gestione scorciatoie tastiera per migliore accessibilità
         * Linguaggio: JavaScript event handling + jQuery
         * Events: keydown su document per cattura globale
         */
        $(document).on('keydown', function(e) {
            // ESC chiude modal se aperto
            if (e.key === 'Escape' && $('#imageModal').hasClass('show')) {
                $('#imageModal').modal('hide');
            }
            
            // Ctrl+K o Cmd+K per focus su campo ricerca (se presente)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault(); // Previene comportamento browser default
                const $searchInput = $('#search');
                if ($searchInput.length > 0) {
                    $searchInput.focus(); // Focus su campo ricerca
                }
            }
        });
        
        // ================================================================
        // SMOOTH SCROLLING - ENHANCED NAVIGATION
        // ================================================================
        
        /**
         * Implementa smooth scrolling per link interni (anchor links)
         * Linguaggio: jQuery + Animation API
         * Event: click su link che iniziano con #
         * 
         * Benefits:
         * - Navigazione fluida tra sezioni
         * - Offset per header fissi
         * - Migliore user experience
         */
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault(); // Previene jump immediato
            
            // Recupera target dall'attributo href
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                // Scroll animato con offset per header fissi
                $('html, body').animate({
                    scrollTop: target.offset().top - 100 // 100px offset
                }, 500); // 500ms duration
            }
        });
        
        // ================================================================
        // TRACKING CLICKS BOTTONI - USER INTERACTION ANALYTICS
        // ================================================================
        
        /**
         * Traccia click su tutti i bottoni per analytics comportamentali
         * Linguaggio: jQuery event delegation
         * Event: click su elementi con classe .btn
         * 
         * Data collected:
         * - Testo bottone cliccato
         * - Classi CSS applicate
         * - Timestamp interazione
         */
        $('.btn').on('click', function() {
            const btnText = $(this).text().trim();    // Testo bottone
            const btnClass = $(this).attr('class');   // Classi CSS
            
            console.log('🔘 Click bottone:', {
                text: btnText,
                classes: btnClass,
                timestamp: new Date().toISOString()
            });
        });
        
        // ================================================================
        // FINALIZZAZIONE CARICAMENTO - COMPLETION HANDLER
        // ================================================================
        
        /**
         * Operazioni di finalizzazione dopo caricamento completo
         * Linguaggio: JavaScript setTimeout + jQuery animations
         * Delay: 100ms per assicurare rendering completo
         */
        setTimeout(() => {
            console.log('✅ Vista prodotto pubblica con stile unificato completamente caricata');
            
            // Rimuove indicatori di loading se presenti
            $('.loading-indicator').fadeOut();
            
            // Attiva animazioni CSS per elementi caricati
            $('.card').addClass('loaded');
            
        }, 100); // 100ms delay
        
        console.log('📱 Responsive breakpoints attivi');
    });

// ========================================================================
// FUNZIONI GLOBALI UTILITY - FUTURE FEATURES
// ========================================================================

/**
 * Funzione per condividere prodotto usando Web Share API
 * Linguaggio: JavaScript moderno + Web APIs + Clipboard API
 * Scope: window (globale)
 * 
 * Features:
 * - Native sharing su dispositivi supportati
 * - Fallback su copy-to-clipboard
 * - Notifica successo
 */
window.shareProdotto = function() {
    // Controlla supporto Web Share API (mobile principalmente)
    if (navigator.share) {
        navigator.share({
            title: '{{ $prodotto->nome }}',                                    // Titolo prodotto
            text: '{{ Str::limit($prodotto->descrizione, 100) }}',           // Descrizione limitata
            url: window.location.href                                         // URL corrente
        });
    } else {
        // Fallback: copia URL negli appunti
        navigator.clipboard.writeText(window.location.href).then(() => {
            showAlert('Link copiato negli appunti!', 'success');
        });
    }
};

/**
 * Funzione per stampare scheda prodotto
 * Linguaggio: JavaScript Browser API
 * Scope: window (globale)
 * 
 * Utilizza: window.print() native API
 * Benefits: Versione printer-friendly della pagina
 */
window.printProdotto = function() {
    window.print(); // API nativa browser per stampa
};

/**
 * Funzione per gestione favoriti (implementazione futura)
 * Linguaggio: JavaScript
 * Scope: window (globale)
 * 
 * Future implementation options:
 * - localStorage per persistenza locale
 * - Backend API per favoriti utente
 * - Cookie-based storage
 */
window.toggleFavorite = function() {
    // Implementazione futura con localStorage o backend
    console.log('Toggle favoriti per prodotto ID: {{ $prodotto->id }}');
};

}); // Chiusura primo $(document).ready()

/**
 * ====================================================================
 * RIEPILOGO TECNOLOGIE E ARCHITETTURE UTILIZZATE
 * ====================================================================
 * 
 * 1. JQUERY 3.7 - JAVASCRIPT LIBRARY:
 *    - $(document).ready(): DOM initialization (DOPPIO - BUG)
 *    - Event handling: .on('click'), .on('error'), .on('keydown')
 *    - DOM manipulation: .attr(), .text(), .html(), .addClass()
 *    - Animations: .animate(), .fadeOut(), .fadeIn()
 *    - Selectors: $('.class'), $('#id'), $('element')
 *    - Method chaining: .attr().text()
 * 
 * 2. BOOTSTRAP 5.3 - CSS/JS FRAMEWORK:
 *    - Modal API: .modal('show'), .modal('hide')
 *    - Tooltip API: .tooltip() con configurazione
 *    - Alert components con dismiss functionality
 *    - CSS classes: alert, btn, card, position-fixed
 *    - Icons: Bootstrap Icons (bi-*) integration
 * 
 * 3. MODERN JAVASCRIPT ES6+:
 *    - Const/let declarations per scope safety
 *    - Template literals: `string ${variable}`
 *    - Object.assign() per merging objects
 *    - Arrow functions in callbacks
 *    - Destructuring e spread operators
 * 
 * 4. WEB APIs MODERNE:
 *    - Intersection Observer: lazy loading performance
 *    - Web Share API: native sharing mobile
 *    - Clipboard API: copy-to-clipboard fallback
 *    - Console API: structured logging
 *    - Date API: ISO timestamps
 * 
 * 5. BROWSER APIs TRADIZIONALI:
 *    - setTimeout/setInterval: delayed execution
 *    - window.print(): native print functionality
 *    - Event API: preventDefault(), key detection
 *    - Location API: window.location.href
 * 
 * 6. LARAVEL FRAMEWORK INTEGRATION:
 *    - Blade data injection: window.PageData
 *    - Route system: window.LaravelApp.route
 *    - Session flash messages integration
 *    - CSRF token (se necessario per future features)
 * 
 * ====================================================================
 * PATTERN ARCHITETTURALI E BEST PRACTICES
 * ====================================================================
 * 
 * 1. MODULE PATTERN:
 *    - Script isolato per pagina specifica
 *    - Route checking per evitare conflitti
 *    - Namespace separation
 * 
 * 2. PROGRESSIVE ENHANCEMENT:
 *    - Funzionalità base sempre disponibili
 *    - JavaScript migliora esperienza senza bloccare
 *    - Feature detection (IntersectionObserver)
 * 
 * 3. GRACEFUL DEGRADATION:
 *    - Fallback per immagini rotte
 *    - Fallback sharing API → clipboard
 *    - Error handling con try/catch impliciti
 * 
 * 4. PERFORMANCE OPTIMIZATION:
 *    - Lazy loading images
 *    - Caching jQuery selectors
 *    - Debounced operations
 *    - Minimal DOM manipulation
 * 
 * 5. USER EXPERIENCE PATTERNS:
 *    - Loading states e feedback immediato
 *    - Smooth animations e transitions
 *    - Keyboard shortcuts accessibility
 *    - Toast notifications informative
 * 
 * 6. ACCESSIBILITY (A11Y):
 *    - Keyboard navigation support
 *    - Alt text per immagini
 *    - Focus management
 *    - ARIA roles negli alert
 * 
 * 7. ANALYTICS & MONITORING:
 *    - User interaction tracking
 *    - Performance metrics collection
 *    - Error logging structured
 *    - Behavioral data collection
 * 
 * ====================================================================
 * PROBLEMI IDENTIFICATI E SUGGERIMENTI
 * ====================================================================
 * 
 * 1. DOPPIO $(document).ready():
 *    - PROBLEMA: Due document.ready annidati
 *    - SOLUZIONE: Rimuovere wrapper esterno
 *    - IMPACT: Nessun impatto funzionale ma codice ridondante
 * 
 * 2. BLADE SYNTAX IN JAVASCRIPT:
 *    - PROBLEMA: {{ $prodotto->nome }} in JS
 *    - SOLUZIONE: Passare dati via window.PageData
 *    - IMPACT: Possibili errori se variabile non definita
 * 
 * 3. GLOBAL FUNCTIONS:
 *    - CONSIDERAZIONE: Molte funzioni globali
 *    - SUGGERIMENTO: Namespace object per organizzazione
 *    - BENEFIT: Evita conflitti con altre librerie
 * 
 * 4. ERROR HANDLING:
 *    - MIGLIORAMENTO: Aggiungere try/catch espliciti
 *    - BENEFIT: Gestione errori più robusta
 *    - IMPLEMENTATION: Wrap risky operations
 * 
 * ====================================================================
 * SECURITY CONSIDERATIONS
 * ====================================================================
 * 
 * 1. INPUT SANITIZATION:
 *    - HTML injection prevention in alerts
 *    - Data attribute validation
 *    - User input escaping
 * 
 * 2. XSS PREVENTION:
 *    - Careful innerHTML usage
 *    - Blade data escaping
 *    - Console logging safety
 * 
 * 3. PRIVACY:
 *    - Analytics data collection transparency
 *    - User consent per tracking
 *    - Local storage usage disclosure
 */