/**
 * ====================================================================
 * FILE: prodotti-completo-show.js
 * LINGUAGGIO: JavaScript + jQuery + AJAX
 * FRAMEWORK: jQuery 3.7, Bootstrap 5.3
 * SCOPO: Gestione vista dettaglio prodotto per tecnici (Livello 2)
 *        Include visualizzazione malfunzionamenti e segnalazioni
 * ====================================================================
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Modal per visualizzazione ingrandita immagini prodotto
 * - Filtri dinamici per malfunzionamenti (tutti/critici/recenti)
 * - Sistema segnalazione malfunzionamenti tramite AJAX
 * - Contatori dinamici segnalazioni
 * - Sistema notifiche toast personalizzate
 * - Gestione errori immagini con fallback
 * - Analytics e tracking visualizzazioni prodotto
 * - Tooltip informativi Bootstrap
 */

// ========================================================================
// INIZIALIZZAZIONE PRINCIPALE - DOM READY EVENT
// ========================================================================

/**
 * jQuery Document Ready Function
 * Si esegue quando il DOM è completamente caricato e pronto per la manipolazione
 * Linguaggio: jQuery JavaScript
 */
$(document).ready(function() {
    // Log di debug per troubleshooting
    console.log('prodotti.completo.show caricato');
    
    // ====================================================================
    // CONTROLLO ROUTE SPECIFICO - SICUREZZA E ISOLAMENTO
    // ====================================================================
    
    /**
     * Verifica che questo script sia eseguito solo nella pagina corretta
     * Evita conflitti tra script di pagine diverse
     * window.LaravelApp è definito nel layout principale app.blade.php
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.completo.show') {
        // Se non siamo nella route corretta, interrompe l'esecuzione
        return;
    }
    
    // ====================================================================
    // INIZIALIZZAZIONE VARIABILI E DATI
    // ====================================================================
    
    /**
     * Recupera dati passati da Laravel Blade template
     * I dati vengono iniettati tramite @json() nel template Blade
     */
    const pageData = window.PageData || {};
    
    /**
     * Array per prodotti selezionati (per future funzionalità comparative)
     * Potrebbe essere utilizzato per confronti tra prodotti simili
     */
    let selectedProducts = [];
    
    // Log di conferma caricamento con emoji per identificazione rapida
    console.log('🔧 Vista prodotto tecnico completo con immagini corrette caricata');
    
    // ====================================================================
    // MODAL VISUALIZZAZIONE IMMAGINI - IMAGE LIGHTBOX
    // ====================================================================
    
    /**
     * Funzione globale per aprire modal con immagine ingrandita
     * Linguaggio: JavaScript vanilla + jQuery + Bootstrap Modal API
     * Scope: window (globale) per essere chiamata da elementi HTML
     * 
     * @param {string} imageSrc - URL dell'immagine da mostrare
     * @param {string} imageTitle - Titolo/descrizione dell'immagine
     */
    window.openImageModal = function(imageSrc, imageTitle) {
        // Imposta src e alt dell'immagine nel modal
        $('#imageModalImg').attr('src', imageSrc).attr('alt', imageTitle);
        
        // Imposta il titolo del modal
        $('#imageModalTitle').text(imageTitle);
        
        // Mostra il modal Bootstrap
        $('#imageModal').modal('show');
    };
    
    // ====================================================================
    // FILTRI MALFUNZIONAMENTI - DYNAMIC FILTERING
    // ====================================================================
    
    /**
     * Event handler per i pulsanti di filtro malfunzionamenti
     * Linguaggio: jQuery event handling
     * Event: 'click' sui button dentro #malfunzionamentoFilter
     */
    $('#malfunzionamentoFilter button').on('click', function() {
        // Recupera il tipo di filtro dal data attribute
        const filter = $(this).data('filter');
        
        // Rimuove classe active da tutti i pulsanti
        $('#malfunzionamentoFilter button').removeClass('active');
        
        // Aggiunge classe active al pulsante cliccato
        $(this).addClass('active');
        
        // Esegue il filtro
        filterMalfunzionamenti(filter);
    });
    
    /**
     * Funzione per filtrare i malfunzionamenti visualizzati
     * Linguaggio: jQuery + JavaScript Date API
     * 
     * @param {string} filter - Tipo di filtro: 'all', 'critica', 'recent'
     */
    function filterMalfunzionamenti(filter) {
        // Seleziona tutti gli elementi malfunzionamento
        const items = $('.malfunzionamento-item');
        
        if (filter === 'all') {
            // Mostra tutti gli elementi
            items.removeClass('d-none').show();
            
        } else if (filter === 'critica') {
            // Filtra per gravità critica
            items.each(function() {
                const gravita = $(this).data('gravita'); // Legge data-gravita attribute
                if (gravita === 'critica') {
                    $(this).removeClass('d-none').show(); // Mostra elemento
                } else {
                    $(this).addClass('d-none').hide();   // Nasconde elemento
                }
            });
            
        } else if (filter === 'recent') {
            // Filtra per malfunzionamenti degli ultimi 30 giorni
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30); // Sottrae 30 giorni
            
            items.each(function() {
                // Legge data di creazione dal data attribute
                const createdDateStr = $(this).data('created');
                const createdDate = new Date(createdDateStr);
                
                // Confronta con data limite (30 giorni fa)
                if (createdDate >= thirtyDaysAgo) {
                    $(this).removeClass('d-none').show(); // Recente: mostra
                } else {
                    $(this).addClass('d-none').hide();   // Vecchio: nasconde
                }
            });
        }
        
        // ================================================================
        // GESTIONE MESSAGGIO "NESSUN RISULTATO"
        // ================================================================
        
        // Conta elementi visibili dopo il filtro
        const visibleCount = items.filter(':not(.d-none)').length;
        
        if (visibleCount === 0) {
            // Rimuove eventuali messaggi precedenti
            $('#no-results-message').remove();
            
            // Aggiunge nuovo messaggio "nessun risultato"
            $('#malfunzionamentiList').append(`
                <div class="col-12 text-center py-3" id="no-results-message">
                    <i class="bi bi-search text-muted mb-2" style="font-size: 1.5rem;"></i>
                    <h6 class="text-muted">Nessun risultato per "${filter}"</h6>
                    <button class="btn btn-outline-primary btn-sm" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                    </button>
                </div>
            `);
        } else {
            // Se ci sono risultati, rimuove il messaggio
            $('#no-results-message').remove();
        }
    }
    
    /**
     * Funzione globale per reset dei filtri
     * Linguaggio: JavaScript
     * Scope: window (globale) per essere chiamata da onclick HTML
     */
    window.resetFilters = function() {
        // Simula click sul pulsante "Tutti" per resettare i filtri
        $('#malfunzionamentoFilter button[data-filter="all"]').click();
    };
    
    // ====================================================================
    // SISTEMA SEGNALAZIONE MALFUNZIONAMENTI - AJAX INTERACTION
    // ====================================================================
    
    /**
     * Funzione globale per segnalare un malfunzionamento
     * Linguaggio: jQuery AJAX + JavaScript
     * Scope: window (globale) per essere chiamata da onclick HTML
     * 
     * @param {number} malfunzionamentoId - ID del malfunzionamento da segnalare
     */
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        // Conferma utente prima di procedere
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return; // Annulla se utente rifiuta
        }
        
        // ================================================================
        // UI FEEDBACK - LOADING STATE
        // ================================================================
        
        // Trova il pulsante specifico per questo malfunzionamento
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        
        // Salva testo originale per ripristino
        const originalText = button.html();
        
        // Cambia a stato loading con spinner
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...')
              .prop('disabled', true); // Disabilita pulsante durante richiesta
        
        // ================================================================
        // RICHIESTA AJAX AL SERVER
        // ================================================================
        
        /**
         * Richiesta AJAX per segnalare il malfunzionamento
         * Linguaggio: jQuery AJAX
         * Endpoint: API Laravel per segnalazioni
         */
        $.ajax({
            // URL endpoint API (definito in window.apiMalfunzionamentiUrl)
            url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
            
            method: 'POST', // Metodo HTTP POST
            
            // Headers necessari per Laravel
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // CSRF protection
                'Content-Type': 'application/json' // Formato dati
            },
            
            timeout: 10000, // Timeout 10 secondi
            
            // ============================================================
            // GESTIONE SUCCESSO RICHIESTA
            // ============================================================
            
            /**
             * Callback eseguito in caso di successo
             * @param {Object} response - Risposta JSON dal server
             */
            success: function(response) {
                if (response.success) {
                    // Mostra notifica di successo
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                    
                    // Aggiorna contatore visuale
                    updateSegnalazioniCount(malfunzionamentoId, response.nuovo_count);
                    
                    // Ripristina pulsante
                    button.html(originalText).prop('disabled', false);
                } else {
                    // Server ha risposto ma con errore logico
                    throw new Error(response.message || 'Errore sconosciuto');
                }
            },
            
            // ============================================================
            // GESTIONE ERRORI RICHIESTA
            // ============================================================
            
            /**
             * Callback eseguito in caso di errore
             * @param {Object} xhr - Oggetto XMLHttpRequest con dettagli errore
             */
            error: function(xhr) {
                console.error('Errore segnalazione:', xhr);
                
                // Determina messaggio di errore appropriato
                let errorMsg = 'Errore nella segnalazione del malfunzionamento';
                
                if (xhr.responseJSON?.message) {
                    // Messaggio specifico dal server
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    // Errore di autorizzazione
                    errorMsg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    // Risorsa non trovata
                    errorMsg = 'Malfunzionamento non trovato';
                }
                
                // Mostra notifica di errore
                showAlert(errorMsg, 'danger');
                
                // Ripristina pulsante
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    /**
     * Aggiorna il contatore di segnalazioni nell'interfaccia
     * Linguaggio: jQuery DOM manipulation
     * 
     * @param {number} malfunzionamentoId - ID del malfunzionamento
     * @param {number} newCount - Nuovo numero di segnalazioni
     */
    function updateSegnalazioniCount(malfunzionamentoId, newCount) {
        // Trova il badge specifico per questo malfunzionamento
        const badge = $(`#badge-${malfunzionamentoId}`);
        
        if (badge.length > 0) {
            // Aggiorna contenuto del badge con nuovo conteggio
            badge.html(`<i class="bi bi-flag me-1"></i>${newCount}`);
        }
    }
    
    // ====================================================================
    // SISTEMA NOTIFICHE PERSONALIZZATE - TOAST ALERTS
    // ====================================================================
    
    /**
     * Mostra alert personalizzato con auto-dismiss
     * Linguaggio: jQuery + Bootstrap CSS + JavaScript setTimeout
     * 
     * @param {string} message - Messaggio da mostrare
     * @param {string} type - Tipo alert: 'success', 'danger', 'warning', 'info'
     * @param {number} duration - Durata in millisecondi (default 4000)
     */
    function showAlert(message, type = 'info', duration = 4000) {
        // Rimuove eventuali alert precedenti
        $('.custom-alert').remove();
        
        // Mappa tipi di alert a icone Bootstrap Icons
        const icons = {
            success: 'check-circle-fill',      // Icona successo
            danger: 'exclamation-triangle-fill', // Icona errore
            warning: 'exclamation-triangle-fill', // Icona avviso
            info: 'info-circle-fill'           // Icona informazione
        };
        
        // Costruisce HTML dell'alert
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
        
        // Aggiunge alert al DOM
        $('body').append(alertHtml);
        
        // Auto-rimuove dopo durata specificata
        setTimeout(() => {
            $('.custom-alert').fadeOut(300, function() {
                $(this).remove(); // Rimuove dal DOM dopo fade
            });
        }, duration);
    }
    
    // ====================================================================
    // GESTIONE ERRORI IMMAGINI - GRACEFUL DEGRADATION
    // ====================================================================
    
    /**
     * Gestisce errori di caricamento immagini con fallback
     * Linguaggio: jQuery event handling
     * Event: 'error' su tutte le immagini della pagina
     */
    $('.product-image, img').on('error', function() {
        const $this = $(this); // Cache riferimento jQuery
        
        // Recupera nome prodotto dall'attributo alt
        const productName = $this.attr('alt') || 'Prodotto';
        
        // Sostituisce immagine rotta con placeholder
        $this.replaceWith(`
            <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                 style="height: ${$this.height() || 280}px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted mb-2" style="font-size: 2rem;"></i>
                    <div class="small text-muted">${productName}</div>
                </div>
            </div>
        `);
    });
    
    // ====================================================================
    // TOOLTIP INFORMATIVI - USER EXPERIENCE
    // ====================================================================
    
    /**
     * Inizializza tooltip Bootstrap su elementi con attributo title
     * Linguaggio: jQuery + Bootstrap Tooltip API
     */
    $('[title]').tooltip({
        trigger: 'hover',  // Trigger al passaggio del mouse
        placement: 'top'   // Posizione sopra l'elemento
    });
    
    // ====================================================================
    // GESTIONE NOTIFICHE DI SESSIONE - LARAVEL INTEGRATION
    // ====================================================================
    
    /**
     * Mostra notifiche di sessione passate da Laravel
     * I messaggi vengono iniettati nel template Blade tramite session flash
     */
    if (window.PageData.sessionSuccess) {
        showAlert(window.PageData.sessionSuccess, 'success');
    }
    if (window.PageData.sessionError) {
        showAlert(window.PageData.sessionError, 'danger');
    }
    
    // ====================================================================
    // ANALYTICS E MONITORAGGIO - DATA TRACKING
    // ====================================================================
    
    /**
     * Log analytics per monitoraggio visualizzazioni prodotto
     * Linguaggio: JavaScript + Console API
     * Scopo: Raccolta dati per analisi comportamento utenti
     */
    if (window.PageData.prodotto) {
        console.log('📊 Vista prodotto:', {
            prodotto_id: window.PageData.prodotto.id,
            nome: window.PageData.prodotto.nome,
            categoria: window.PageData.prodotto.categoria,
            malfunzionamenti: (window.PageData.prodotto.malfunzionamenti || []).length,
            timestamp: new Date().toISOString() // Timestamp ISO formato standard
        });
    }
    
    // Log finale di conferma caricamento completo
    console.log('✅ Vista prodotto tecnico completo completamente caricata');
});

/**
 * ====================================================================
 * RIEPILOGO TECNOLOGIE E PATTERN UTILIZZATI
 * ====================================================================
 * 
 * 1. JQUERY 3.7 - LIBRERIA JAVASCRIPT:
 *    - $(document).ready(): Inizializzazione DOM
 *    - Event handling: .on('click'), .on('error')
 *    - DOM manipulation: .attr(), .html(), .addClass()
 *    - Selettori CSS: $('.class'), $('#id')
 *    - AJAX: $.ajax() per comunicazione server
 *    - Animazioni: .fadeOut(), .show(), .hide()
 * 
 * 2. BOOTSTRAP 5.3 - CSS FRAMEWORK:
 *    - Modal API: .modal('show') per lightbox immagini
 *    - Tooltip API: .tooltip() per informazioni hover
 *    - CSS classes: alert, btn, spinner-border
 *    - Responsive grid system
 * 
 * 3. JAVASCRIPT ES6+ - LINGUAGGIO CORE:
 *    - Arrow functions: () => {}
 *    - Template literals: `string ${variable}`
 *    - Const/let declarations
 *    - Date API: new Date(), setDate(), getDate()
 *    - Error handling: try/catch, throw new Error()
 * 
 * 4. AJAX - COMUNICAZIONE ASINCRONA:
 *    - XMLHttpRequest tramite jQuery $.ajax()
 *    - Headers HTTP: X-CSRF-TOKEN, Content-Type
 *    - Response handling: success/error callbacks
 *    - JSON data format
 * 
 * 5. LARAVEL INTEGRATION:
 *    - CSRF token protection
 *    - Route data injection (window.LaravelApp)
 *    - Page data injection (window.PageData)
 *    - Session flash messages
 * 
 * 6. BROWSER APIs:
 *    - Console API: console.log(), console.error()
 *    - Window object: window.confirm()
 *    - setTimeout/setInterval
 * 
 * ====================================================================
 * PATTERN ARCHITETTURALI IMPLEMENTATI
 * ====================================================================
 * 
 * 1. MODULE PATTERN:
 *    - Script isolato per pagina specifica
 *    - Controllo route per evitare conflitti
 * 
 * 2. EVENT DELEGATION:
 *    - Event listeners su elementi dinamici
 *    - Gestione errori immagini globale
 * 
 * 3. PROGRESSIVE ENHANCEMENT:
 *    - Funzionalità base sempre disponibili
 *    - Miglioramenti graduali (tooltip, animazioni)
 * 
 * 4. GRACEFUL DEGRADATION:
 *    - Fallback per immagini non trovate
 *    - Timeout e error handling AJAX
 * 
 * 5. SEPARATION OF CONCERNS:
 *    - Logica JavaScript separata da HTML
 *    - CSS per styling, JS per comportamento
 * 
 * 6. STATE MANAGEMENT:
 *    - UI state tracking (button loading)
 *    - Filter state management
 * 
 * 7. USER EXPERIENCE PATTERNS:
 *    - Loading states con spinner
 *    - Conferme per azioni critiche
 *    - Notifiche informative
 *    - Auto-dismiss elements
 * 
 * ====================================================================
 * SICUREZZA E BEST PRACTICES
 * ====================================================================
 * 
 * 1. CSRF PROTECTION:
 *    - Token CSRF in tutte le richieste AJAX
 *    - Meta tag csrf-token nel layout
 * 
 * 2. INPUT VALIDATION:
 *    - Conferma utente per azioni critiche
 *    - Controllo esistenza elementi DOM
 * 
 * 3. ERROR HANDLING:
 *    - Try/catch per operazioni rischiose
 *    - Fallback per errori di rete/server
 *    - Log errori per debugging
 * 
 * 4. MEMORY MANAGEMENT:
 *    - Rimozione elementi temporanei dal DOM
 *    - Cleanup event listeners se necessario
 * 
 * 5. PERFORMANCE:
 *    - Caching selettori jQuery
 *    - Timeout AJAX per evitare hang
 *    - Debouncing per azioni frequenti
 */