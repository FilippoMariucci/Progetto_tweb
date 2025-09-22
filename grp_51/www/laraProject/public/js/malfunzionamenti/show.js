/**
 * FILE: show.js
 * LINGUAGGIO: JavaScript (ES6+) con jQuery e Bootstrap
 * SCOPO: Gestione della pagina di dettaglio di un singolo malfunzionamento con segnalazione avanzata
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Bootstrap 5, CSS3 Animations
 * FUNZIONALITÀ PRINCIPALI: Segnalazione AJAX migliorata, alert avanzati, smooth scrolling, tooltip
 * CARATTERISTICHE: Debug esteso, gestione errori robusta, feedback visivo animato
 * AUTORE: Sistema di assistenza tecnica
 */

/**
 * DOCUMENT READY EVENT - jQuery
 * Entry point che inizializza tutte le funzionalità quando DOM è completamente caricato
 * Equivalente moderno: document.addEventListener('DOMContentLoaded', callback)
 */
$(document).ready(function() {
    
    /**
     * LOG INIZIALE PER DEBUG E TRACCIAMENTO
     * console.log: stampa identificativo del file nella console browser
     */
    console.log('malfunzionamenti.show caricato');
    
    // === CONTROLLO ROUTE ATTIVA PER SICUREZZA ===
    /**
     * VALIDAZIONE ROUTE CORRENTE
     * window.LaravelApp?.route: variabile globale iniettata da Laravel tramite Blade
     * Optional chaining (?.) per sicurezza se oggetto non definito
     * Early return pattern per terminare esecuzione se route errata
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'malfunzionamenti.show') {
        return; // Termina se non siamo nella pagina di dettaglio
    }
    
    // === INIZIALIZZAZIONE VARIABILI GLOBALI ===
    /**
     * SETUP DATI CONDIVISI TRA CLIENT E SERVER
     * window.PageData: oggetto popolato da Laravel con @json() in template Blade
     * selectedProducts: array per tracking (non utilizzato in questa specifica pagina)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    /**
     * LOG CONFERMA CARICAMENTO PAGINA
     * Doppio log per tracciare progression del caricamento
     */
    console.log('Pagina dettaglio malfunzionamento caricata');
    
    // === DEBUG INIZIALE ESTESO ===
    /**
     * LOGGING DETTAGLIATO PER TROUBLESHOOTING
     * Verifica configurazione CSRF, presenza pulsanti, e ID malfunzionamento
     * Essenziale per debug problemi AJAX e selezione elementi DOM
     */
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('Pulsanti segnala trovati:', $('.segnala-btn').length);
    console.log('ID malfunzionamento:', $('.segnala-btn').data('malfunzionamento-id'));
    
    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO AVANZATA ===
    /**
     * FUNZIONE GLOBALE PER SEGNALAZIONI CON DEBUG ESTESO
     * window.segnalaMalfunzionamento: funzione accessibile da onclick HTML attributes
     * Versione migliorata con logging dettagliato e gestione errori avanzata
     * @param {string|number} malfunzionamentoId - ID univoco del malfunzionamento
     */
    window.segnalaMalfunzionamento = function segnalaMalfunzionamento(malfunzionamentoId) {
        /**
         * LOG ENTRY POINT FUNZIONE
         * Traccia ogni chiamata alla funzione con parametri
         */
        console.log('Funzione segnalaMalfunzionamento chiamata con ID:', malfunzionamentoId);
        
        /**
         * VALIDAZIONE PARAMETRO ID
         * Controllo rigoroso dell'input per prevenire chiamate errate
         */
        if (!malfunzionamentoId) {
            alert('Errore: ID malfunzionamento non valido');
            return; // Termina se ID non valido
        }
        
        /**
         * CONFERMA UTENTE CON MESSAGGIO DETTAGLIATO
         * confirm(): dialog nativo con messaggio informativo esteso
         * \n: carattere di nuova riga per formattazione messaggio
         */
        if (!confirm('Confermi di aver riscontrato questo problema?\n\nQuesta segnalazione aiuterà altri tecnici a identificare problemi frequenti.')) {
            return; // Termina se utente annulla
        }
        
        /**
         * SELEZIONE PULSANTE TRAMITE ATTRIBUTO ONCLICK
         * Template literal per costruire selettore CSS specifico per onclick attribute
         * Importante: uso di quotes singole nell'attributo onclick HTML
         */
        const button = $(`button[onclick="segnalaMalfunzionamento('${malfunzionamentoId}')"]`);
        
        /**
         * VALIDAZIONE ESISTENZA PULSANTE NEL DOM
         * .length: proprietà jQuery che restituisce numero elementi trovati
         * Controllo essenziale per prevenire errori su elementi inesistenti
         */
        if (button.length === 0) {
            console.error('Pulsante non trovato per ID:', malfunzionamentoId);
            alert('Errore: Pulsante non trovato');
            return; // Termina se pulsante non trovato
        }
        
        const originalText = button.html(); // Backup testo originale per ripristino
        
        /**
         * AGGIORNAMENTO UI - STATO LOADING AVANZATO
         * Method chaining jQuery per multiple operazioni in sequenza
         * role="status": attributo ARIA per accessibility screen readers
         * Cambio colore da outline-warning a warning per maggiore visibilità
         */
        button.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Segnalando...')
              .prop('disabled', true) // Disabilita per prevenire doppi click
              .removeClass('btn-outline-warning') // Rimuove stile outline
              .addClass('btn-warning'); // Aggiunge stile pieno per evidenziare stato loading
        
        /**
         * LOG PRE-AJAX PER TROUBLESHOOTING
         * Traccia momento di invio richiesta per debug timing
         */
        console.log('Invio richiesta AJAX per segnalazione...');
        
        /**
         * CHIAMATA AJAX AVANZATA CON CONFIGURAZIONE ESTESA
         * $.ajax(): metodo jQuery per XMLHttpRequest con configurazione completa
         */
        $.ajax({
            /**
             * CONFIGURAZIONE URL ENDPOINT
             * Template literal per costruzione URL RESTful dinamico
             * window.apiMalfunzionamentiUrl: base URL definito da Laravel in layout
             */
            url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
            method: 'POST', // HTTP POST per operazioni che modificano stato server
            
            /**
             * HEADERS HTTP COMPLETI PER API REST
             * X-CSRF-TOKEN: token Laravel per protezione CSRF attacks
             * Content-Type: formato payload inviato (JSON)
             * Accept: formato risposta atteso dal server (JSON)
             */
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            
            timeout: 15000, // Timeout esteso a 15 secondi per connessioni lente
            
            /**
             * CALLBACK SUCCESSO CON LOGGING DETTAGLIATO
             * @param {Object} response - Oggetto JSON dal controller Laravel
             * Gestisce tutti i casi di successo con aggiornamento UI completo
             */
            success: function(response) {
                /**
                 * LOG COMPLETO RISPOSTA SERVER
                 * Essenziale per debug struttura response da API
                 */
                console.log('Risposta ricevuta:', response);
                
                /**
                 * VERIFICA SUCCESSO LOGICO APPLICAZIONE
                 * response.success: campo boolean standard nelle response Laravel
                 */
                if (response.success) {
                    
                    /**
                     * AGGIORNAMENTO CONTATORE SEGNALAZIONI
                     * Cerca elemento specifico nella sidebar con ID univoco
                     * Aggiorna contenuto se elemento presente
                     */
                    const counterElement = $('#segnalazioni-counter');
                    if (counterElement.length > 0) {
                        counterElement.html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                    }
                    
                    /**
                     * TRASFORMAZIONE VISUALE PULSANTE - VERSIONE AVANZATA
                     * Rimozione completa classi precedenti e applicazione stile success
                     * .css(): applicazione stili CSS inline per massimo controllo visivo
                     */
                    button.removeClass('btn-warning btn-outline-warning')
                          .addClass('btn-success')
                          .css({
                              'background-color': '#198754',    // Verde Bootstrap success
                              'border-color': '#146c43',       // Bordo più scuro
                              'color': '#ffffff',              // Testo bianco
                              'font-weight': 'bold',           // Testo grassetto
                              'box-shadow': '0 3px 12px rgba(25, 135, 84, 0.4)', // Ombra colorata
                              'transform': 'translateY(-1px)'   // Leggero lift effect
                          })
                          .html('<i class="bi bi-check-circle-fill me-2"></i><strong>Problema Segnalato!</strong>')
                          .prop('disabled', true) // Mantiene disabilitato
                          .removeAttr('onclick'); // Rimuove handler per prevenire re-click
                    
                    /**
                     * AGGIUNTA ANIMAZIONE CSS PERSONALIZZATA
                     * Classe CSS per effetto pulso che attira l'attenzione
                     * Deve essere definita in CSS con @keyframes animation
                     */
                    button.addClass('pulse-success');
                    
                    /**
                     * RIMOZIONE ANIMAZIONE TEMPORIZZATA
                     * setTimeout(): rimuove effetto dopo 4 secondi
                     * Arrow function per mantenere contesto
                     */
                    setTimeout(() => {
                        button.removeClass('pulse-success');
                    }, 4000);
                    
                    /**
                     * NOTIFICA UTENTE CON MESSAGGIO DETTAGLIATO
                     * showAlert(): funzione helper personalizzata
                     * Include conteggio aggiornato per feedback informativo
                     */
                    showAlert(`Segnalazione registrata con successo! Totale segnalazioni: ${response.nuovo_count}`, 'success');
                    
                    /**
                     * LOG SUCCESSO OPERAZIONE
                     * Conferma completamento processo per troubleshooting
                     */
                    console.log('Segnalazione completata con successo');
                    
                } else {
                    /**
                     * GESTIONE ERRORE LOGICO APPLICATIVO
                     * Server risponde HTTP 200 OK ma con success=false
                     * throw new Error(): forza esecuzione del blocco error
                     */
                    throw new Error(response.message || 'Errore nella risposta del server');
                }
            },
            
            /**
             * CALLBACK ERRORE CON GESTIONE COMPLETA
             * @param {Object} xhr - XMLHttpRequest object completo
             * @param {string} status - Stato testuale dell'errore
             * @param {string} error - Messaggio errore
             * Gestisce tutti i tipi di errore con logging e feedback dettagliati
             */
            error: function(xhr, status, error) {
                /**
                 * LOGGING COMPLETO ERRORE PER DEBUG AVANZATO
                 * Oggetto strutturato con tutti i dettagli per troubleshooting
                 */
                console.error('Errore AJAX completo:', {
                    xhr: xhr,                    // Oggetto XMLHttpRequest completo
                    status: status,              // Status testuale (timeout, error, etc.)
                    error: error,                // Messaggio errore JavaScript
                    responseText: xhr.responseText // Response body raw dal server
                });
                
                let msg = 'Errore nella segnalazione del malfunzionamento'; // Messaggio default
                
                /**
                 * INTERPRETAZIONE CODICI HTTP SPECIFICI
                 * Switch logic per fornire messaggi utente appropriati
                 * Copre i casi più comuni di errore nelle API REST
                 */
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Messaggio personalizzato dal server Laravel
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    // HTTP 403 Forbidden - autorizzazioni insufficienti
                    msg = 'Non hai i permessi per effettuare questa segnalazione';
                } else if (xhr.status === 404) {
                    // HTTP 404 Not Found - risorsa non trovata
                    msg = 'Malfunzionamento non trovato';
                } else if (xhr.status === 422) {
                    // HTTP 422 Unprocessable Entity - validazione failed
                    msg = 'Dati non validi per la segnalazione';
                } else if (xhr.status === 500) {
                    // HTTP 500 Internal Server Error - errore server
                    msg = 'Errore interno del server. Riprova più tardi.';
                } else if (status === 'timeout') {
                    // Timeout della richiesta AJAX
                    msg = 'Timeout della richiesta. Controlla la connessione.';
                } else if (xhr.status === 0) {
                    // Status 0 = problemi di rete o CORS
                    msg = 'Errore di connessione. Verifica la tua connessione internet.';
                }
                
                /**
                 * FEEDBACK ERRORE ALL'UTENTE
                 * showAlert(): notifica rossa con messaggio specifico
                 */
                showAlert(msg, 'danger');
                
                /**
                 * RIPRISTINO COMPLETO STATO PULSANTE
                 * Rimuove tutti gli stili custom e ripristina stato originale
                 * .css() con valori vuoti rimuove proprietà inline
                 */
                button.removeClass('btn-warning')
                      .addClass('btn-outline-warning') // Ripristina classe originale
                      .css({
                          'background-color': '',    // Reset colore background
                          'border-color': '',        // Reset colore bordo
                          'color': '',               // Reset colore testo
                          'font-weight': '',         // Reset peso font
                          'box-shadow': '',          // Reset ombra
                          'transform': ''            // Reset trasformazione
                      })
                      .html(originalText)            // Ripristina testo originale
                      .prop('disabled', false);     // Riabilita pulsante
            }
        });
    };

    // === FUNZIONE ALERT AVANZATA CON DESIGN MIGLIORATO ===
    /**
     * SISTEMA NOTIFICHE TOAST PERSONALIZZATO AVANZATO
     * Versione migliorata con design più ricco e configurazioni specifiche
     * @param {string} message - Testo della notifica
     * @param {string} type - Tipo alert (success, danger, warning, info)
     */
    function showAlert(message, type = 'info') {
        
        /**
         * CLEANUP ALERT PRECEDENTI
         * Rimuove notifiche esistenti per evitare sovrapposizioni
         */
        $('.alert-floating').remove();
        
        /**
         * DETERMINAZIONE CLASSE CSS BOOTSTRAP
         * Mapping diretto tipo -> classe per styling coerente
         */
        const alertClass = `alert-${type}`;
        
        /**
         * MAPPING AVANZATO ICONE CON COLORI
         * Oggetto complesso per icone specifiche con classi colore
         * Icone "fill" per maggiore impatto visivo
         */
        const iconClasses = {
            'success': 'check-circle-fill text-success',
            'danger': 'exclamation-triangle-fill text-danger',
            'warning': 'exclamation-triangle-fill text-warning',
            'info': 'info-circle-fill text-info'
        };
        
        const iconClass = iconClasses[type] || iconClasses.info; // Fallback a info
        
        /**
         * TEMPLATE HTML AVANZATO PER ALERT
         * Design ricco con layout flexbox e typography migliorata
         * Stili inline per massimo controllo visuale
         */
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-floating shadow-lg" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 1055; min-width: 350px; max-width: 500px; border: none; border-radius: 0.5rem;">
                <div class="d-flex align-items-start">
                    <i class="bi bi-${iconClass} me-3 fs-4 flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-1">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
                </div>
            </div>
        `;
        
        /**
         * INSERIMENTO ALERT NEL DOM
         * Append al body per posizionamento fixed corretto
         */
        $('body').append(alertHtml);
        
        /**
         * LOG ALERT PER DEBUG
         * Traccia tutti gli alert mostrati per troubleshooting UI
         */
        console.log(`Alert mostrato: ${type} - ${message}`);
        
        /**
         * AUTO-RIMOZIONE TEMPORIZZATA DINAMICA
         * Durata diversa basata sul tipo di messaggio
         * Successo: 8 secondi (più tempo per leggere), altri: 6 secondi
         */
        const autoRemoveDelay = type === 'success' ? 8000 : 6000;
        setTimeout(() => {
            $('.alert-floating').fadeOut(500, function() {
                $(this).remove(); // Cleanup DOM dopo animazione
            });
        }, autoRemoveDelay);
    }
    
    /**
     * NOTA: FUNZIONE SHOWALERT DUPLICATA
     * Il codice originale conteneva una seconda definizione della funzione showAlert
     * più semplice che sovrascriveva quella avanzata. Per chiarezza didattica,
     * ho mantenuto solo la versione avanzata commentata sopra.
     * In produzione, bisognerebbe rimuovere la duplicazione.
     */
    
    // === SMOOTH SCROLLING PER NAVIGAZIONE ANCORE ===
    /**
     * IMPLEMENTAZIONE SCROLL FLUIDO PER LINK INTERNI
     * Event delegation per tutti i link che iniziano con #
     * Migliora UX con transizioni fluide invece di salti bruschi
     */
    $('a[href^="#"]').on('click', function(e) {
        /**
         * PREVENZIONE COMPORTAMENTO DEFAULT
         * preventDefault(): blocca il jump nativo del browser
         */
        e.preventDefault();
        
        /**
         * SELEZIONE TARGET DELL'ANCORA
         * this.hash: parte # dell'URL del link cliccato
         * $(this.hash): selettore jQuery per elemento target
         */
        const target = $(this.hash);
        
        /**
         * ANIMAZIONE SCROLL SE TARGET ESISTE
         * .length: verifica esistenza elemento nel DOM
         */
        if (target.length) {
            /**
             * ANIMAZIONE SCROLL FLUIDA
             * .animate(): metodo jQuery per animazioni CSS
             * scrollTop: proprietà per posizione scroll verticale
             * offset().top - 100: posizione elemento meno offset per header fisso
             * 500: durata animazione in millisecondi
             */
            $('html, body').animate({
                scrollTop: target.offset().top - 100 // Offset per header sticky
            }, 500);
        }
    });
    
    // === INIZIALIZZAZIONE TOOLTIP BOOTSTRAP NATIVA ===
    /**
     * SETUP TOOLTIP CON API BOOTSTRAP 5 NATIVA
     * Approccio moderno senza dipendenze jQuery per tooltip
     */
    
    /**
     * SELEZIONE ELEMENTI CON TOOLTIP
     * document.querySelectorAll(): API DOM nativa per selezione
     * [].slice.call(): converte NodeList in Array per compatibilità
     */
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    
    /**
     * INIZIALIZZAZIONE TOOLTIP SU OGNI ELEMENTO
     * .map(): crea nuovo tooltip Bootstrap per ogni elemento
     * new bootstrap.Tooltip(): costruttore nativo Bootstrap 5
     */
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    /**
     * LOG FINALE CONFERMA INIZIALIZZAZIONE
     * Indica completamento setup di tutte le funzionalità
     */
    console.log('JavaScript dettaglio malfunzionamento inizializzato completamente');
});

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, template literals, arrow functions)
 * - jQuery (DOM manipulation, AJAX, event handling, animations)
 * - Bootstrap 5 (tooltip nativo, alert, classi utility, flexbox)
 * - Bootstrap Icons (iconografia coerente con classi fill)
 * - CSS3 (proprietà inline, transforms, shadows, animations)
 * - HTML5 (data attributes, ARIA roles per accessibility)
 * - Laravel (CSRF protection, Blade templating, JSON responses)
 * - AJAX/XMLHttpRequest (comunicazione asincrona robusta)
 * 
 * PATTERN E PRINCIPI UTILIZZATI:
 * - Event-driven programming (gestione eventi utente)
 * - Progressive enhancement (funzionalità JS che migliorano HTML base)
 * - Graceful degradation (fallback per errori e elementi mancanti)
 * - Comprehensive error handling (gestione dettagliata tutti i casi errore)
 * - User experience optimization (animazioni, feedback immediato, loading states)
 * - Accessibility (ARIA roles, keyboard navigation, screen reader support)
 * - Performance optimization (cleanup DOM, event delegation)
 * - Debug-friendly development (logging esteso per troubleshooting)
 * - Code maintainability (funzioni pure, single responsibility)
 * - Security awareness (CSRF protection, input validation)
 * 
 * CARATTERISTICHE DISTINTIVE DI QUESTO FILE:
 * 1. Debug logging esteso per troubleshooting produzione
 * 2. Gestione errori HTTP completa con messaggi specifici
 * 3. UI feedback avanzato con animazioni CSS e styling custom
 * 4. Alert system ricco con layout flexbox e timing dinamico  
 * 5. Smooth scrolling per migliorare navigazione interna
 * 6. Tooltip Bootstrap 5 nativo senza dipendenze jQuery
 * 7. Validazioni multiple per robustezza (ID, DOM elements, responses)
 * 8. Ripristino completo stato UI in caso di errori
 * 9. CSRF token management per sicurezza API calls
 * 10. Method chaining jQuery per operazioni DOM efficienti
 */