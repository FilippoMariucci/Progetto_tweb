/**
 * FILE: index.js 
 * LINGUAGGIO: JavaScript (ES6+) con jQuery e AJAX
 * SCOPO: Gestione dell'interfaccia per la visualizzazione e interazione con la lista dei malfunzionamenti
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Bootstrap 5, AJAX
 * FUNZIONALITÀ PRINCIPALI: Segnalazione malfunzionamenti, ricerca live, filtri automatici, notifiche
 * AUTORE: Sistema di assistenza tecnica
 */

/**
 * DOCUMENT READY EVENT - jQuery
 * Si attiva quando il DOM è completamente caricato e pronto per la manipolazione
 * Equivalente a document.addEventListener('DOMContentLoaded', function(){})
 */
$(document).ready(function() {
    
    /**
     * LOG DI CARICAMENTO PER DEBUG
     * console.log: stampa messaggio nella console del browser per verificare il caricamento del file
     */
    console.log('malfunzionamenti.index caricato');
    
    // === CONTROLLO ROUTE ATTIVA ===
    /**
     * VERIFICA DELLA ROUTE CORRENTE
     * window.LaravelApp?.route: variabile globale impostata dal backend Laravel tramite Blade template
     * Operatore ?. (optional chaining): evita errori TypeError se LaravelApp non esiste
     * Se la route non corrisponde a 'malfunzionamenti.index', termina l'esecuzione
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'malfunzionamenti.index') {
        return; // Termina l'esecuzione se non siamo nella pagina corretta
    }
    
    // === VARIABILI GLOBALI DI CONFIGURAZIONE ===
    /**
     * INIZIALIZZAZIONE VARIABILI
     * pageData: oggetto contenente dati della pagina passati dal backend Laravel
     * selectedProducts: array per tracking prodotti selezionati (non utilizzato in questa pagina)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    /**
     * LOG DI CONFERMA CARICAMENTO PAGINA
     * Doppio log per debug e tracciamento del flusso di esecuzione
     */
    console.log('Pagina malfunzionamenti caricata');

    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    /**
     * FUNZIONE GLOBALE PER SEGNALARE MALFUNZIONAMENTI
     * window.segnalaMalfunzionamento: definisce funzione a livello globale
     * Può essere chiamata dai bottoni HTML tramite attributo onclick
     * @param {string|number} malfunzionamentoId - ID del malfunzionamento da segnalare
     */
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        
        /**
         * VALIDAZIONE PARAMETRO ID
         * Verifica che l'ID sia valido prima di procedere
         */
        if (!malfunzionamentoId) {
            alert('Errore: ID malfunzionamento non valido'); // Alert nativo del browser
            return; // Termina funzione se ID non valido
        }
        
        /**
         * CONFERMA UTENTE
         * confirm(): dialog nativo del browser che restituisce boolean
         * Se utente clicca "Annulla", la funzione termina
         */
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return; // Termina se utente annulla
        }
        
        /**
         * SELEZIONE DEL PULSANTE TRAMITE ATTRIBUTO ONCLICK
         * Template literal per creare selettore CSS specifico
         * Cerca il button con onclick che contiene l'ID specifico
         */
        const button = $(`button[onclick="segnalaMalfunzionamento('${malfunzionamentoId}')"]`);
        const originalText = button.html(); // Salva testo originale per ripristino
        
        /**
         * AGGIORNAMENTO UI - STATO DI CARICAMENTO
         * .html(): cambia contenuto HTML del pulsante
         * .prop('disabled', true): disabilita pulsante per prevenire doppi click
         * Spinner Bootstrap per feedback visivo durante operazione AJAX
         */
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...').prop('disabled', true);
        
        /**
         * CHIAMATA AJAX PER SEGNALARE MALFUNZIONAMENTO
         * $.ajax(): metodo jQuery per richieste HTTP asincrone
         */
        $.ajax({
            /**
             * CONFIGURAZIONE RICHIESTA HTTP
             * Template literal per URL dinamico con ID
             * window.apiMalfunzionamentiUrl: variabile globale dal backend Laravel
             */
            url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
            method: 'POST', // Metodo HTTP per creare/modificare risorsa
            
            /**
             * HEADERS HTTP RICHIESTI
             * X-CSRF-TOKEN: token Laravel per protezione CSRF (Cross-Site Request Forgery)
             * Content-Type: specifica formato dati inviati (JSON)
             */
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Legge token da meta tag
                'Content-Type': 'application/json'
            },
            
            timeout: 10000, // Timeout di 10 secondi per evitare attese infinite
            
            /**
             * CALLBACK DI SUCCESSO
             * Si esegue se richiesta HTTP ha successo (status 200-299)
             * @param {Object} response - Risposta JSON dal server Laravel
             */
            success: function(response) {
                /**
                 * VERIFICA RISPOSTA DEL SERVER
                 * response.success: campo booleano nella risposta JSON Laravel
                 */
                if (response.success) {
                    
                    /**
                     * AGGIORNAMENTO CONTATORE SEGNALAZIONI
                     * .closest(): metodo jQuery per trovare antenato più vicino
                     * .find(): cerca discendenti con selettore specificato
                     * Naviga nel DOM per trovare e aggiornare il contatore
                     */
                    button.closest('.card-body')
                          .find('.bi-exclamation-triangle')
                          .parent()
                          .html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    /**
                     * AGGIORNAMENTO VISUAL STATE DEL PULSANTE
                     * Method chaining jQuery per multiple operazioni
                     * Cambia aspetto da "warning" a "success"
                     */
                    button.removeClass('btn-outline-warning')  // Rimuove classe arancione
                          .addClass('btn-success')             // Aggiunge classe verde
                          .html('<i class="bi bi-check-circle me-1"></i>Segnalato') // Cambia testo e icona
                          .prop('disabled', true)             // Mantiene disabilitato
                          .removeAttr('onclick');             // Rimuove handler onclick per prevenire re-click
                    
                    /**
                     * FEEDBACK UTENTE POSITIVO
                     * Mostra notifica di successo con conteggio aggiornato
                     */
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                } else {
                    /**
                     * GESTIONE ERRORE LOGICO
                     * Se server risponde ma con success=false
                     * throw new Error(): forza l'esecuzione del blocco catch
                     */
                    throw new Error(response.message || 'Errore nella risposta');
                }
            },
            
            /**
             * CALLBACK DI ERRORE
             * Si esegue se richiesta HTTP fallisce o viene lanciato errore nel success
             * @param {Object} xhr - Oggetto XMLHttpRequest con dettagli errore
             */
            error: function(xhr) {
                
                /**
                 * LOGGING ERRORE PER DEBUG
                 * console.error: stampa errore nella console con styling rosso
                 */
                console.error('Errore AJAX:', xhr);
                
                let msg = 'Errore nella segnalazione del malfunzionamento'; // Messaggio default
                
                /**
                 * GESTIONE MESSAGGI DI ERRORE SPECIFICI
                 * Interpreta diversi tipi di errore HTTP per fornire feedback appropriato
                 */
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Se server restituisce messaggio JSON specifico
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    // HTTP 403 Forbidden - problema di autorizzazione
                    msg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    // HTTP 404 Not Found - risorsa non trovata
                    msg = 'Malfunzionamento non trovato';
                }
                
                /**
                 * FEEDBACK ERRORE E RIPRISTINO UI
                 * Mostra messaggio di errore e ripristina stato originale del pulsante
                 */
                showAlert(msg, 'danger'); // Notifica rossa di errore
                button.html(originalText).prop('disabled', false); // Ripristina pulsante
            }
        });
    };
    
    // === CONFIGURAZIONE CAMPO DI RICERCA ===
    /**
     * DISABILITAZIONE FUNZIONALITÀ AUTOCOMPLETE BROWSER
     * .attr(): metodo jQuery per impostare attributi HTML
     * Disabilita tutte le funzionalità di auto-completamento per controllo preciso
     */
    $('#search').attr({
        'autocomplete': 'off',      // Disabilita autocomplete del browser
        'autocapitalize': 'off',    // Disabilita auto-capitalizzazione (mobile)
        'autocorrect': 'off',       // Disabilita auto-correzione (mobile)
        'spellcheck': 'false'       // Disabilita controllo ortografico
    });
    
    // === INIZIALIZZAZIONE TOOLTIP BOOTSTRAP ===
    /**
     * ATTIVAZIONE TOOLTIP SU ELEMENTI CON DATA-ATTRIBUTE
     * $('[data-bs-toggle="tooltip"]'): selettore per tutti gli elementi con attributo specifico
     * .tooltip(): metodo Bootstrap per inizializzare tooltip interattivi
     */
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === AUTO-SUBMIT FILTRI ===
    /**
     * EVENT HANDLER PER FILTRI AUTOMATICI
     * Quando utente cambia selezione nei dropdown, invia automaticamente il form
     * .on('change'): attacca event listener per evento change su select
     */
    $('#gravita, #difficolta, #order').on('change', function() {
        /**
         * AUTO-SUBMIT DEL FORM FILTRI
         * $('#filter-form').submit(): metodo jQuery per inviare form programmaticamente
         * Permette filtraggio immediato senza click su pulsante
         */
        $('#filter-form').submit();
    });
    
    // === RICERCA LIVE CON DEBOUNCING ===
    /**
     * VARIABILE PER GESTIRE DEBOUNCING
     * let searchTimeout: variabile per memorizzare ID del timeout
     * Debouncing: tecnica per limitare frequenza di esecuzione di una funzione
     */
    let searchTimeout;
    
    /**
     * EVENT HANDLER PER RICERCA LIVE
     * .on('input'): evento che si scatena ad ogni carattere digitato
     * Più reattivo di 'change' che si attiva solo al blur
     */
    $('#search').on('input', function() {
        
        /**
         * LETTURA E PULIZIA QUERY DI RICERCA
         * $(this).val(): ottiene valore corrente del campo input
         * .trim(): rimuove spazi bianchi iniziali e finali
         */
        const query = $(this).val().trim();
        
        /**
         * CANCELLAZIONE TIMEOUT PRECEDENTE (DEBOUNCING)
         * clearTimeout(): annulla timeout precedente se esiste
         * Previene multiple richieste durante digitazione rapida
         */
        clearTimeout(searchTimeout);
        
        /**
         * RICERCA SOLO CON QUERY SIGNIFICATIVA
         * query.length >= 2: esegue ricerca solo con almeno 2 caratteri
         * Evita troppe richieste per query molto brevi
         */
        if (query.length >= 2) {
            /**
             * IMPOSTAZIONE NUOVO TIMEOUT (DEBOUNCING)
             * setTimeout(): esegue funzione dopo delay specificato
             * 500ms: attende mezzo secondo dopo ultima digitazione prima di cercare
             * Arrow function per mantenere contesto
             */
            searchTimeout = setTimeout(() => {
                $('#filter-form').submit(); // Invia form solo dopo pausa nella digitazione
            }, 500); // Delay di 500 millisecondi
        }
    });
    
    // === FUNZIONE PER MOSTRARE ALERT PERSONALIZZATI ===
    /**
     * SISTEMA DI NOTIFICHE TOAST PERSONALIZZATO
     * @param {string} message - Testo del messaggio da mostrare
     * @param {string} type - Tipo di alert ('info', 'success', 'danger', 'warning')
     * @param {number} duration - Durata visualizzazione in millisecondi (default 5000)
     */
    function showAlert(message, type = 'info', duration = 5000) {
        
        /**
         * RIMOZIONE ALERT PRECEDENTI
         * $('.custom-alert').remove(): rimuove tutte le notifiche esistenti
         * Previene accumulo di multiple notifiche sovrapposte
         */
        $('.custom-alert').remove();
        
        /**
         * CREAZIONE HTML DINAMICO PER ALERT
         * Template literal per creare elemento alert complesso
         * Stili inline per posizionamento fisso in alto a destra
         */
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'x-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        /**
         * INSERIMENTO ALERT NEL DOM
         * $('body').append(): aggiunge alert alla fine del body
         * Position fixed permette di rimanere visibile durante scroll
         */
        $('body').append(alertHtml);
        
        /**
         * AUTO-RIMOZIONE TEMPORIZZATA
         * setTimeout(): programma rimozione automatica dopo durata specificata
         * .fadeOut(): animazione jQuery per nascondere gradualmente
         * Callback function per rimuovere elemento dal DOM dopo animazione
         */
        setTimeout(() => {
            $('.custom-alert').fadeOut(() => $('.custom-alert').remove());
        }, duration);
    }
    
    /**
     * LOG FINALE DI INIZIALIZZAZIONE
     * Conferma che tutti gli event handler e funzionalità sono stati configurati
     */
    console.log('JavaScript malfunzionamenti inizializzato');
});

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, arrow functions, template literals)
 * - jQuery (event handling, DOM manipulation, AJAX, method chaining)
 * - AJAX (XMLHttpRequest per comunicazione asincrona con server)
 * - Bootstrap 5 (alert, tooltip, spinner, classi responsive)
 * - Bootstrap Icons (icone per feedback visivo)
 * - Laravel (CSRF token, route generation, JSON responses)
 * - HTML5 (form attributes, data attributes)
 * - CSS3 (position fixed, z-index per layering)
 * 
 * PATTERN E PRINCIPI UTILIZZATI:
 * - Event-driven programming (risposta a eventi utente)
 * - Debouncing (limitazione frequenza chiamate funzioni)
 * - AJAX communication (comunicazione asincrona client-server)
 * - Progressive enhancement (funzionalità JS senza rompere HTML base)
 * - Error handling (gestione errori HTTP e JavaScript)
 * - User experience (feedback visivo, loading states, notifiche)
 * - Security (CSRF token protection)
 * - Performance optimization (debouncing, cleanup dei timeout)
 * - Separation of concerns (funzioni specializzate per compiti specifici)
 * - Graceful degradation (fallback per errori di rete/server)
 * 
 * FLUSSO DI ESECUZIONE PRINCIPALE:
 * 1. Inizializzazione: verifica route e setup variabili
 * 2. Definizione funzioni globali: segnalaMalfunzionamento per onclick HTML
 * 3. Configurazione UI: disabilita autocomplete, inizializza tooltip
 * 4. Setup event handlers: filtri automatici, ricerca live debounced
 * 5. Sistema notifiche: funzione helper per alert temporanei
 * 6. Logging: conferma inizializzazione completata
 */