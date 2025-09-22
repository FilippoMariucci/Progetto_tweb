/**
 * ===================================================================
 * ADMIN PRODOTTI INDEX - JavaScript per Gestione Prodotti
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/admin/prodotti-index.js
 * Linguaggio: JavaScript con libreria jQuery
 * Framework: Bootstrap 5 per UI/UX
 * 
 * DESCRIZIONE GENERALE:
 * Questo file contiene tutto il codice JavaScript lato client per la gestione
 * dell'interfaccia amministrativa dei prodotti. Gestisce ricerca, filtri,
 * selezione multipla, azioni bulk e interazioni con il server Laravel tramite AJAX.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Gestione ricerca e filtri prodotti con debounce
 * - Selezione multipla prodotti con checkbox
 * - Azioni bulk (attiva, disattiva, elimina prodotti in massa)
 * - Interfaccia responsive con feedback utente
 * - Gestione errori e stati di caricamento
 * - Toast notifications per feedback operazioni
 * - Loading overlay per operazioni lunghe
 * - Keyboard shortcuts per migliorare UX
 * ===================================================================
 */

// ===================================================================
// SEZIONE 1: VARIABILI GLOBALI E CONFIGURAZIONI
// ===================================================================

/**
 * VARIABILI GLOBALI - Mantengono stato dell'applicazione
 */
let selectedProducts = [];    // Array per memorizzare ID prodotti selezionati
let searchTimeout = null;     // Timer per debounce della ricerca (evita troppe chiamate server)
let isLoading = false;        // Flag per prevenire operazioni multiple simultanee

/**
 * CONFIGURAZIONI - Costanti per timing e comportamento UI
 */
const CONFIG = {
    SEARCH_DEBOUNCE_DELAY: 500,    // millisecondi di attesa prima di eseguire ricerca automatica
    LOADING_TIMEOUT: 5000,         // timeout massimo per operazioni di caricamento
    TOAST_AUTO_HIDE_DELAY: 5000,   // tempo auto-hide per notifiche di successo
    ERROR_TOAST_DELAY: 10000       // tempo più lungo per notifiche di errore
};

// ===================================================================
// SEZIONE 2: INIZIALIZZAZIONE PRINCIPALE
// ===================================================================

/**
 * FUNZIONE PRINCIPALE - Si esegue quando il DOM è completamente caricato
 * Utilizza jQuery $(document).ready() per garantire che tutti gli elementi HTML siano pronti
 */
$(document).ready(function() {
    console.log('admin.prodotti.index caricato');
    
    /**
     * CONTROLLO ROUTE - Verifica che siamo nella pagina corretta
     * Previene l'esecuzione del codice in pagine sbagliate
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.index' && !window.location.href.includes('admin/prodotti')) {
        console.log('Route non corretta per admin prodotti');
        return; // Esce dalla funzione se non siamo nella pagina admin prodotti
    }
    
    /**
     * CATENA DI INIZIALIZZAZIONE - Chiama tutte le funzioni di setup
     * L'ordine è importante per evitare dipendenze non risolte
     */
    initializeAdminProducts();    // Inizializza stato base UI
    setupEventListeners();        // Attacca eventi agli elementi DOM
    setupAjaxConfiguration();     // Configura AJAX con token CSRF
    setupTooltips();              // Inizializza tooltip Bootstrap
    setupResponsiveLayout();      // Configura layout responsivo
    
    console.log('Admin prodotti con stile catalogo inizializzato');
});

// ===================================================================
// SEZIONE 3: INIZIALIZZAZIONE SISTEMA
// ===================================================================

/**
 * FUNZIONE: initializeAdminProducts()
 * SCOPO: Inizializza tutti i componenti del sistema admin prodotti
 * LINGUAGGIO: JavaScript puro + jQuery
 * 
 * Questa funzione si occupa di:
 * - Aggiornare l'interfaccia delle azioni bulk
 * - Configurare gestione errori immagini
 * - Evidenziare termini di ricerca
 * - Impostare shortcuts da tastiera
 */
function initializeAdminProducts() {
    console.log('Inizializzazione sistema admin prodotti...');
    
    // Aggiorna UI dei pulsanti di selezione multipla
    updateBulkActionsUI();
    
    // Imposta gestione errori per immagini prodotti
    setupImageErrorHandling();
    
    // Evidenzia termini cercati nei risultati (migliora UX)
    highlightSearchTerms();
    
    // Configura scorciatoie da tastiera
    setupKeyboardShortcuts();
    
    console.log('Sistema admin prodotti inizializzato');
}

/**
 * FUNZIONE: setupAjaxConfiguration()
 * SCOPO: Configura jQuery AJAX per Laravel
 * LINGUAGGIO: jQuery AJAX + Laravel CSRF
 * 
 * Laravel richiede il token CSRF per tutte le richieste POST/PUT/DELETE
 * per prevenire attacchi Cross-Site Request Forgery
 */
function setupAjaxConfiguration() {
    // Ottiene il token CSRF dal meta tag nell'HTML
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    if (csrfToken) {
        /**
         * $.ajaxSetup() - Configura default per tutte le chiamate AJAX
         * Aggiunge automaticamente l'header X-CSRF-TOKEN a ogni richiesta
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        console.log('CSRF token configurato per AJAX');
    }
}

/**
 * FUNZIONE: setupTooltips()
 * SCOPO: Inizializza tooltip Bootstrap 5
 * LINGUAGGIO: JavaScript + Bootstrap 5 API
 * 
 * I tooltip mostrano informazioni aggiuntive al hover degli elementi
 */
function setupTooltips() {
    // Controlla se Bootstrap è disponibile nell'oggetto globale window
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        // Trova tutti gli elementi con attributo data-bs-toggle="tooltip"
        $('[data-bs-toggle="tooltip"]').each(function() {
            // Crea un'istanza Tooltip per ogni elemento
            new bootstrap.Tooltip(this);
        });
        console.log('Tooltip Bootstrap inizializzati');
    }
}

// ===================================================================
// SEZIONE 4: EVENT LISTENERS - Gestione Eventi UI
// ===================================================================

/**
 * FUNZIONE: setupEventListeners()
 * SCOPO: Configura tutti gli event listener per l'interfaccia utente
 * LINGUAGGIO: jQuery Events + DOM manipulation
 * 
 * Questa funzione è il cuore dell'interattività dell'interfaccia.
 * Gestisce click, input, change, submit di tutti gli elementi UI.
 */
function setupEventListeners() {
    console.log('Setup event listeners...');
    
    // === SOTTOSEZIONE: GESTIONE FORM RICERCA ===
    
    /**
     * EVENT: Click su bottone "Pulisci ricerca"
     * ELEMENTO: #clearSearch (bottone X accanto al campo ricerca)
     * AZIONE: Svuota campo ricerca e mette focus per nuova ricerca
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();  // val('') svuota il campo, focus() attiva cursore
        console.log('Campo ricerca pulito');
    });
    
    /**
     * EVENT: Change su select filtri
     * ELEMENTI: #status, #staff_id (dropdown filtri)
     * AZIONE: Submit automatico del form quando cambiano i filtri
     */
    $('#status, #staff_id').on('change', function() {
        console.log('Filtro cambiato:', $(this).attr('id'), '=', $(this).val());
        $('#filterForm').submit(); // Invia form per aggiornare risultati
    });
    
    /**
     * EVENT: Input nel campo ricerca con DEBOUNCE
     * ELEMENTO: #search (campo testo ricerca)
     * TECNICA: Debouncing per evitare troppe chiamate al server
     * 
     * Il debouncing aspetta che l'utente smetta di digitare per X millisecondi
     * prima di eseguire la ricerca, migliorando performance e UX
     */
    $('#search').on('input', function() {
        clearTimeout(searchTimeout); // Cancella timer precedente
        const searchTerm = $(this).val().trim(); // Ottiene testo pulito
        
        // Imposta nuovo timer
        searchTimeout = setTimeout(() => {
            // Esegue ricerca solo se termini >= 3 caratteri o campo vuoto
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                console.log('Auto-ricerca potenziale:', searchTerm);
                // NOTA: Linea commentata per evitare ricerca automatica
                // Decommentare per abilitare: $('#filterForm').submit();
            }
        }, CONFIG.SEARCH_DEBOUNCE_DELAY);
    });
    
    // === SOTTOSEZIONE: GESTIONE SELEZIONE PRODOTTI ===
    
    /**
     * EVENT: Change sui checkbox prodotti (Event Delegation)
     * ELEMENTI: .product-checkbox (checkbox in ogni card prodotto)
     * TECNICA: Event delegation per gestire elementi aggiunti dinamicamente
     * 
     * $(document).on() invece di $('.product-checkbox').on() permette
     * di gestire anche checkbox aggiunti dopo il caricamento iniziale
     */
    $(document).on('change', '.product-checkbox', function() {
        const isChecked = $(this).is(':checked');    // Stato checkbox (true/false)
        const productId = $(this).val();             // ID prodotto dal value
        const card = $(this).closest('.product-card'); // Card container padre
        
        /**
         * FEEDBACK VISIVO - Aggiunge/rimuove classe CSS per evidenziare selezione
         */
        if (isChecked) {
            card.addClass('selected');  // Aggiunge classe CSS per stile selezione
            console.log('Prodotto selezionato:', productId);
        } else {
            card.removeClass('selected'); // Rimuove classe CSS
            console.log('Prodotto deselezionato:', productId);
        }
        
        // Aggiorna UI dei pulsanti azioni bulk
        updateBulkActionsUI();
    });
    
    // === SOTTOSEZIONE: GESTIONE FORM SUBMIT ===
    
    /**
     * EVENT: Submit form con feedback visivo
     * ELEMENTI: Tutti i form nella pagina
     * AZIONE: Mostra loading spinner durante invio form
     * 
     * Migliora UX mostrando che l'operazione è in corso
     */
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]'); // Trova bottone submit
        const originalText = $submitBtn.html(); // Salva testo originale
        
        // Cambia bottone con spinner e testo "Cercando..."
        $submitBtn.html('<i class="bi bi-hourglass-split me-1 loading-spinner"></i>Cercando...')
                  .prop('disabled', true); // Disabilita per evitare doppi click
        
        // Ripristina stato originale dopo timeout di sicurezza
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, CONFIG.LOADING_TIMEOUT);
    });
}

// ===================================================================
// SEZIONE 5: KEYBOARD SHORTCUTS - Scorciatoie Tastiera
// ===================================================================

/**
 * FUNZIONE: setupKeyboardShortcuts()
 * SCOPO: Implementa scorciatoie da tastiera per power users
 * LINGUAGGIO: JavaScript Events + Key Detection
 * 
 * Migliora produttività per utenti esperti che preferiscono tastiera
 */
function setupKeyboardShortcuts() {
    $(document).on('keydown', function(e) {
        
        /**
         * SHORTCUT: Ctrl+K o Cmd+K per focus ricerca
         * COMPATIBILITÀ: Windows/Linux (Ctrl) e Mac (Cmd)
         * USO: Standard per "ricerca rapida" in molte applicazioni
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault(); // Previene comportamento browser default
            $('#search').focus(); // Attiva campo ricerca
            console.log('Shortcut ricerca attivato');
        }
        
        /**
         * SHORTCUT: ESC per deselezionare tutto
         * USO: Cancella selezioni multiple rapidamente
         */
        if (e.key === 'Escape') {
            if ($('.product-checkbox:checked').length > 0) {
                deselectAllProducts();
                e.preventDefault();
            }
        }
        
        /**
         * SHORTCUT: Ctrl+A per selezionare tutto
         * CONDIZIONE: Non deve essere attivo un input o textarea
         * SICUREZZA: Evita conflitti con selezione testo normale
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !$(e.target).is('input, textarea')) {
            selectAllProducts();
            e.preventDefault();
        }
    });
}

// ===================================================================
// SEZIONE 6: GESTIONE SELEZIONE MULTIPLA
// ===================================================================

/**
 * FUNZIONE: updateBulkActionsUI()
 * SCOPO: Aggiorna interfaccia pulsanti azioni bulk basata su selezioni
 * LINGUAGGIO: jQuery DOM manipulation + CSS classes
 * RETURN: void
 * 
 * Questa funzione controlla quanti prodotti sono selezionati e
 * mostra/nasconde il dropdown delle azioni bulk di conseguenza
 */
function updateBulkActionsUI() {
    const selectedCount = $('.product-checkbox:checked').length; // Conta checkbox selezionati
    const hasSelection = selectedCount > 0; // Boolean: ci sono selezioni?
    const bulkBtn = $('#bulkActionsBtn'); // Riferimento al pulsante dropdown
    
    if (hasSelection) {
        // MOSTRA pulsante azioni bulk
        bulkBtn.removeClass('d-none')  // Rimuove classe Bootstrap "display: none"
               .attr('title', selectedCount + ' prodotti selezionati'); // Tooltip informativo
        console.log('Azioni bulk disponibili:', selectedCount, 'prodotti');
    } else {
        // NASCONDE pulsante azioni bulk
        bulkBtn.addClass('d-none'); // Aggiunge classe Bootstrap "display: none"
        console.log('Azioni bulk nascoste: nessuna selezione');
    }
    
    // Aggiorna tooltip Bootstrap se disponibile
    updateTooltip(bulkBtn[0]); // [0] converte jQuery object in DOM element
}

/**
 * FUNZIONE: selectAllProducts()
 * SCOPO: Seleziona tutti i prodotti visibili nella pagina corrente
 * LINGUAGGIO: jQuery selectors + event triggering
 * RETURN: void
 * 
 * NOTA: Seleziona solo prodotti nella pagina corrente, non tutti nel database
 */
function selectAllProducts() {
    $('.product-checkbox').prop('checked', true)  // Imposta checked=true su tutti checkbox
                          .trigger('change');      // Scatena evento change per aggiornare UI
    console.log('Tutti i prodotti selezionati');
}

/**
 * FUNZIONE: deselectAllProducts()
 * SCOPO: Deseleziona tutti i prodotti
 * LINGUAGGIO: jQuery selectors + event triggering
 * RETURN: void
 */
function deselectAllProducts() {
    $('.product-checkbox').prop('checked', false) // Imposta checked=false
                          .trigger('change');      // Scatena evento change
    console.log('Tutti i prodotti deselezionati');
}

/**
 * FUNZIONE: getSelectedProductIds()
 * SCOPO: Ottiene array degli ID prodotti attualmente selezionati
 * LINGUAGGIO: jQuery selectors + array manipulation
 * RETURN: number[] - Array di ID prodotti (numeri interi)
 * 
 * Utilizza .map() per trasformare collezione jQuery in array JavaScript
 */
function getSelectedProductIds() {
    return $('.product-checkbox:checked').map(function() {
        return parseInt($(this).val()); // Converte valore string in integer
    }).get(); // .get() converte jQuery object in array JavaScript nativo
}

// ===================================================================
// SEZIONE 7: AZIONI BULK - Operazioni Multiple
// ===================================================================

/**
 * FUNZIONE: bulkActivateProducts()
 * SCOPO: Attiva prodotti selezionati in massa
 * LINGUAGGIO: JavaScript + User Confirmation + AJAX
 * RETURN: void
 * 
 * Workflow: Validazione → Conferma utente → Esecuzione AJAX
 */
function bulkActivateProducts() {
    const selected = getSelectedProductIds(); // Ottiene IDs selezionati
    
    // VALIDAZIONE: Controlla che ci siano prodotti selezionati
    if (selected.length === 0) {
        showToast('Seleziona almeno un prodotto da attivare', 'warning');
        return; // Esce dalla funzione se nessuna selezione
    }
    
    // MESSAGGIO PERSONALIZZATO: Singolare vs plurale per UX migliore
    const message = selected.length === 1 ? 
        'Attivare il prodotto selezionato?' : 
        'Attivare ' + selected.length + ' prodotti selezionati?';
    
    // CONFERMA UTENTE: Dialog nativo JavaScript
    if (confirm(message)) {
        console.log('Attivazione bulk confermata:', selected.length, 'prodotti');
        executeBulkAction('activate', selected); // Esegue azione
    }
    // Se utente clicca "Annulla", non succede nulla (no else necessario)
}

/**
 * FUNZIONE: bulkDeactivateProducts()
 * SCOPO: Disattiva prodotti selezionati in massa
 * LINGUAGGIO: JavaScript + User Confirmation + AJAX
 * RETURN: void
 * 
 * Stesso pattern di bulkActivateProducts ma per disattivazione
 */
function bulkDeactivateProducts() {
    const selected = getSelectedProductIds();
    
    if (selected.length === 0) {
        showToast('Seleziona almeno un prodotto da disattivare', 'warning');
        return;
    }
    
    const message = selected.length === 1 ? 
        'Disattivare il prodotto selezionato?' : 
        'Disattivare ' + selected.length + ' prodotti selezionati?';
        
    if (confirm(message)) {
        console.log('Disattivazione bulk confermata:', selected.length, 'prodotti');
        executeBulkAction('deactivate', selected);
    }
}

/**
 * FUNZIONE: bulkDeleteProducts()
 * SCOPO: Elimina definitivamente prodotti selezionati
 * LINGUAGGIO: JavaScript + Strong User Confirmation + AJAX
 * RETURN: void
 * 
 * ATTENZIONE: Operazione irreversibile, richiede conferma forte
 */
function bulkDeleteProducts() {
    const selected = getSelectedProductIds();
    
    if (selected.length === 0) {
        showToast('Seleziona almeno un prodotto da eliminare', 'warning');
        return;
    }
    
    // MESSAGGIO DI AVVERTIMENTO FORTE per operazione irreversibile
    const message = selected.length === 1 ? 
        'ATTENZIONE: Eliminare definitivamente il prodotto selezionato?\n\nQuesta azione non può essere annullata.' :
        'ATTENZIONE: Eliminare definitivamente ' + selected.length + ' prodotti selezionati?\n\nQuesta azione non può essere annullata.';
        
    if (confirm(message)) {
        console.log('Eliminazione bulk confermata:', selected.length, 'prodotti');
        executeBulkAction('delete', selected);
    }
}

/**
 * FUNZIONE: executeBulkAction(action, productIds)
 * SCOPO: Esegue azione bulk sui prodotti tramite chiamata AJAX a Laravel
 * LINGUAGGIO: jQuery AJAX + Laravel Backend + Error Handling
 * PARAMETRI:
 *   - action (string): Tipo azione ('activate', 'deactivate', 'delete')
 *   - productIds (number[]): Array ID prodotti da processare
 * RETURN: void
 * 
 * Questa è la funzione più complessa, gestisce comunicazione client-server
 */
function executeBulkAction(action, productIds) {
    // VALIDAZIONE PARAMETRI
    if (!productIds || productIds.length === 0) {
        console.error('Nessun prodotto per azione bulk');
        showToast('Errore: nessun prodotto selezionato', 'error');
        return;
    }
    
    console.log('Esecuzione azione bulk:', action, 'su', productIds.length, 'prodotti');
    
    // UI FEEDBACK: Mostra loading e disabilita pulsanti
    showLoadingOverlay('Esecuzione ' + action + ' su ' + productIds.length + ' prodotti...');
    $('#bulkActionsBtn').prop('disabled', true);
    
    /**
     * COSTRUZIONE URL: Ottiene route Laravel per bulk actions
     * Preferenza: Route globale definita → Fallback costruzione manuale
     */
    let bulkActionUrl = window.LaravelRoutes?.['admin.prodotti.bulk-action'];
    if (!bulkActionUrl) {
        // FALLBACK: Costruisce URL manualmente se route non disponibile
        const baseUrl = window.location.origin;
        const pathArray = window.location.pathname.split('/');
        const adminIndex = pathArray.indexOf('admin');
        if (adminIndex !== -1) {
            const basePath = pathArray.slice(0, adminIndex + 2).join('/');
            bulkActionUrl = baseUrl + basePath + '/bulk-action';
        } else {
            bulkActionUrl = baseUrl + '/admin/prodotti/bulk-action';
        }
    }
    
    /**
     * CHIAMATA AJAX PRINCIPALE
     * Comunica con Laravel backend per eseguire azione bulk
     */
    $.ajax({
        url: bulkActionUrl,
        type: 'POST',                    // Metodo HTTP
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF Laravel
            action: action,              // Tipo azione da eseguire
            products: productIds         // Array ID prodotti
        },
        timeout: 30000,                  // Timeout 30 secondi per operazioni lunghe
        
        /**
         * SUCCESS HANDLER: Chiamata riuscita (status 200)
         * PARAMETRO response: Risposta JSON dal server Laravel
         */
        success: function(response) {
            console.log('Risposta bulk action:', response);
            hideLoadingOverlay();
            
            if (response.success) {
                // SUCCESSO: Mostra messaggio e ricarica pagina
                const message = response.message || 'Azione ' + action + ' completata con successo';
                showToast(message, 'success');
                
                // RICARICA PAGINA dopo delay per mostrare messaggio
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                // ERRORE APPLICATIVO: Server risponde ma operazione fallita
                console.error('Errore nella risposta:', response.message);
                showToast('Errore: ' + (response.message || 'Operazione fallita'), 'error');
                $('#bulkActionsBtn').prop('disabled', false); // Riabilita pulsanti
            }
        },
        
        /**
         * ERROR HANDLER: Chiamata fallita (network, server error, timeout)
         * PARAMETRI:
         *   - xhr: XMLHttpRequest object con dettagli errore
         *   - status: Tipo errore ('timeout', 'error', 'abort', etc.)
         *   - error: Messaggio errore specifico
         */
        error: function(xhr, status, error) {
            console.error('Errore AJAX bulk action:', { status: status, error: error, xhr: xhr });
            hideLoadingOverlay();
            $('#bulkActionsBtn').prop('disabled', false);
            
            /**
             * GESTIONE ERRORI SPECIFICI per UX migliore
             * Analizza status code HTTP per messaggi utente appropriati
             */
            let errorMessage = 'Errore di comunicazione con il server';
            
            if (xhr.status === 422) {
                errorMessage = 'Dati non validi: controlla i prodotti selezionati';
            } else if (xhr.status === 403) {
                errorMessage = 'Non hai i permessi per eseguire questa operazione';
            } else if (xhr.status === 500) {
                errorMessage = 'Errore interno del server. Riprova più tardi.';
            } else if (status === 'timeout') {
                errorMessage = 'Operazione scaduta. Il server potrebbe essere sovraccarico.';
            } else if (xhr.status === 0) {
                errorMessage = 'Errore di connessione. Controlla la tua connessione internet.';
            }
            
            showToast(errorMessage, 'error');
        }
    });
}

// ===================================================================
// SEZIONE 8: GESTIONE STATO SINGOLO PRODOTTO
// ===================================================================

/**
 * FUNZIONE: confirmToggleStatus(currentStatus)
 * SCOPO: Conferma cambio stato per singolo prodotto
 * LINGUAGGIO: JavaScript + User Confirmation
 * PARAMETRO: currentStatus (boolean) - Stato attuale prodotto
 * RETURN: boolean - True se confermato, false se annullato
 * 
 * Usata dai pulsanti attiva/disattiva nelle card prodotto
 */
function confirmToggleStatus(currentStatus) {
    const action = currentStatus ? 'disattivare' : 'attivare'; // Determina azione opposta
    const confirmed = confirm('Sei sicuro di voler ' + action + ' questo prodotto?');
    
    if (confirmed) {
        console.log('Toggle status confermato:', action);
        showCardLoading(); // Mostra loading visivo sulla card
    }
    
    return confirmed; // Form si sottomette solo se true
}

/**
 * FUNZIONE: showCardLoading()
 * SCOPO: Mostra overlay loading sulle card durante operazioni singole
 * LINGUAGGIO: jQuery DOM manipulation + CSS styling
 * RETURN: void
 * 
 * Crea overlay temporaneo con spinner per feedback visivo
 */
function showCardLoading() {
    // CREA OVERLAY: Elemento temporaneo con spinner Bootstrap
    const loadingOverlay = $(
        '<div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10;">' +
            '<div class="spinner-border text-primary" role="status">' +
                '<span class="visually-hidden">Loading...</span>' +
            '</div>' +
        '</div>'
    );
    
    $('.product-card').append(loadingOverlay); // Aggiunge a tutte le card
    
    // RIMOZIONE AUTOMATICA dopo 3 secondi di sicurezza
    setTimeout(function() {
        loadingOverlay.remove();
    }, 3000);
}

// ===================================================================
// SEZIONE 9: GESTIONE UI E UX - Miglioramenti Interfaccia
// ===================================================================

/**
 * FUNZIONE: setupImageErrorHandling()
 * SCOPO: Gestisce errori caricamento immagini prodotti
 * LINGUAGGIO: jQuery Events + DOM replacement
 * RETURN: void
 * 
 * Sostituisce immagini non trovate con placeholder icona
 */
function setupImageErrorHandling() {
    $('.product-image').on('error', function() {
        // SOSTITUZIONE: Immagine fallita → Placeholder con icona
        $(this).replaceWith(
            '<div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 160px;">' +
                '<i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>' +
            '</div>'
        );
    });
}

/**
 * FUNZIONE: highlightSearchTerms()
 * SCOPO: Evidenzia termini di ricerca nei risultati
 * LINGUAGGIO: JavaScript Regex + jQuery DOM manipulation
 * RETURN: void
 * 
 * Migliora UX mostrando cosa ha matchato la ricerca
 */
function highlightSearchTerms() {
    const searchTerm = getSearchTermFromUrl(); // Ottiene termine dall'URL
    
    // CONDIZIONI: Solo se termine valido e non contiene wildcard
    if (searchTerm && !searchTerm.includes('*') && searchTerm.length > 2) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            // REGEX: Case-insensitive global matching
            const regex = new RegExp('(' + escapeRegex(searchTerm) + ')', 'gi');
            // SOSTITUZIONE: Wrapper con tag <mark> Bootstrap
            const highlighted = text.replace(regex, '<mark class="bg-warning">$1</mark>');
            $(this).html(highlighted);
        });
        console.log('Evidenziati termini di ricerca:', searchTerm);
    }
}

/**
 * FUNZIONE: getSearchTermFromUrl()
 * SCOPO: Estrae parametro ricerca dall'URL corrente
 * LINGUAGGIO: JavaScript URL API
 * RETURN: string - Termine di ricerca o stringa vuota
 * 
 * Utilizza URLSearchParams per parsing sicuro parametri GET
 */
function getSearchTermFromUrl() {
    const urlParams = new URLSearchParams(window.location.search); // Parsing URL params
    return urlParams.get('search') || ''; // Ritorna valore 'search' o stringa vuota
}

/**
 * FUNZIONE: escapeRegex(text)
 * SCOPO: Escape caratteri speciali per uso sicuro in RegExp
 * LINGUAGGIO: JavaScript Regular Expressions
 * PARAMETRO: text (string) - Testo da rendere sicuro per regex
 * RETURN: string - Testo con caratteri speciali escaped
 * 
 * SICUREZZA: Previene ReDoS (Regular Expression Denial of Service)
 */
function escapeRegex(text) {
    // Escape tutti i caratteri che hanno significato speciale in regex
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\/**
 * FUNZIONE: getSearchTermFromUrl()
 * SCOPO: Estrae parametro ricerca dall'URL corrente
 * LINGUAGGIO: JavaScript URL API
 * RETURN: string');
}

// ===================================================================
// SEZIONE 10: LOADING E NOTIFICHE - Sistema Feedback Utente
// ===================================================================

/**
 * FUNZIONE: showLoadingOverlay(message)
 * SCOPO: Mostra overlay di caricamento fullscreen durante operazioni lunghe
 * LINGUAGGIO: jQuery DOM manipulation + Bootstrap styling
 * PARAMETRO: message (string) - Messaggio da mostrare (default: "Caricamento...")
 * RETURN: void
 * 
 * Blocca interfaccia durante operazioni AJAX per prevenire azioni multiple
 */
function showLoadingOverlay(message) {
    message = message || 'Caricamento...'; // Default se non specificato
    $('#loadingOverlay').remove(); // Rimuove overlay precedenti se esistono
    
    /**
     * CREAZIONE OVERLAY: Elemento fullscreen con z-index alto
     * STILI: Bootstrap classes + inline styles per posizionamento
     */
    const overlay = $(
        '<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 9999;">' +
            '<div class="card text-center p-4">' +
                '<div class="card-body">' +
                    '<div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">' +
                        '<span class="visually-hidden">Loading...</span>' +
                    '</div>' +
                    '<h5 class="card-title">' + message + '</h5>' +
                    '<p class="card-text text-muted">Attendere, operazione in corso...</p>' +
                '</div>' +
            '</div>' +
        '</div>'
    );
    
    $('body').append(overlay); // Aggiunge a fine body per z-index massimo
    console.log('Loading overlay:', message);
}

/**
 * FUNZIONE: hideLoadingOverlay()
 * SCOPO: Nasconde e rimuove overlay di caricamento
 * LINGUAGGIO: jQuery animations + DOM removal
 * RETURN: void
 * 
 * Usa fadeOut per transizione smooth prima della rimozione
 */
function hideLoadingOverlay() {
    $('#loadingOverlay').fadeOut(300, function() {
        $(this).remove(); // Rimuove dal DOM dopo animazione
    });
    console.log('Loading overlay nascosto');
}

/**
 * FUNZIONE: showToast(message, type)
 * SCOPO: Mostra notifiche toast temporanee per feedback operazioni
 * LINGUAGGIO: jQuery + Bootstrap Alert classes + CSS positioning
 * PARAMETRI:
 *   - message (string): Testo notifica da mostrare
 *   - type (string): Tipo notifica ('success', 'error', 'warning', 'info')
 * RETURN: void
 * 
 * Sistema notifiche non-invasive in stile moderno (toast/snackbar)
 */
function showToast(message, type) {
    type = type || 'success'; // Default success se non specificato
    
    // CLEANUP: Rimuove toast precedenti per evitare accumulo
    $('.toast-notification').remove();
    
    /**
     * CONFIGURAZIONE STILI: Mapping tipo → classi Bootstrap + icone
     */
    const alertClasses = {
        'success': 'alert-success',
        'error': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const icons = {
        'success': 'check-circle-fill',
        'error': 'exclamation-triangle-fill',
        'warning': 'exclamation-triangle-fill', 
        'info': 'info-circle-fill'
    };
    
    const alertClass = alertClasses[type] || 'alert-info';
    const icon = icons[type] || 'info-circle-fill';
    
    /**
     * CREAZIONE TOAST: Elemento posizionato fixed in alto a destra
     * STRUTTURA: Alert Bootstrap + icona + testo + pulsante chiudi
     */
    const toast = $(
        '<div class="toast-notification alert ' + alertClass + ' alert-dismissible fade show position-fixed shadow-lg" style="top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;">' +
            '<div class="d-flex align-items-center">' +
                '<i class="bi bi-' + icon + ' me-2 fs-5"></i>' +
                '<div class="flex-grow-1">' +
                    '<strong>' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong><br>' +
                    message +
                '</div>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>' +
        '</div>'
    );
    
    $('body').append(toast); // Aggiunge al body
    console.log('Toast mostrato (' + type + '):', message);
    
    /**
     * AUTO-HIDE: Rimozione automatica dopo timeout configurabile
     * TIMING: Errori restano più a lungo per permettere lettura
     */
    const autoHideDelay = type === 'error' ? CONFIG.ERROR_TOAST_DELAY : CONFIG.TOAST_AUTO_HIDE_DELAY;
    setTimeout(function() {
        toast.fadeOut(500, function() {
            $(this).remove();
        });
    }, autoHideDelay);
}

// ===================================================================
// SEZIONE 11: RESPONSIVE LAYOUT - Adattamento Mobile/Desktop
// ===================================================================

/**
 * FUNZIONE: setupResponsiveLayout()
 * SCOPO: Configura gestione layout responsive
 * LINGUAGGIO: jQuery Events + Window resize handling
 * RETURN: void
 * 
 * Ottimizza interfaccia per diverse dimensioni schermo
 */
function setupResponsiveLayout() {
    handleResponsiveLayout(); // Setup iniziale
    
    // EVENT LISTENER: Resize finestra con debounce per performance
    $(window).on('resize', debounce(handleResponsiveLayout, 250));
}

/**
 * FUNZIONE: handleResponsiveLayout()
 * SCOPO: Gestisce adattamenti UI per dimensioni schermo correnti
 * LINGUAGGIO: jQuery + CSS class manipulation
 * RETURN: void
 * 
 * Modifica comportamento UI basato su breakpoint responsive
 */
function handleResponsiveLayout() {
    const isSmallScreen = $(window).width() < 768; // Breakpoint Bootstrap MD
    
    if (isSmallScreen) {
        /**
         * MOBILE LAYOUT: Mostra labels che sono nascoste su desktop
         * UX: Su mobile serve più contesto visivo per form elements
         */
        $('.form-label.d-none.d-lg-block').removeClass('d-none d-lg-block');
        console.log('Layout mobile attivato');
    } else {
        console.log('Layout desktop attivato');
    }
}

/**
 * FUNZIONE: debounce(func, wait)
 * SCOPO: Implementa debouncing per ottimizzare eventi frequenti (resize)
 * LINGUAGGIO: JavaScript Closures + Timers
 * PARAMETRI:
 *   - func (function): Funzione da eseguire con debounce
 *   - wait (number): Millisecondi di attesa prima esecuzione
 * RETURN: function - Funzione wrappata con debounce
 * 
 * PERFORMANCE: Evita esecuzioni eccessive durante resize continuo
 */
function debounce(func, wait) {
    let timeout; // Closure variable per mantenere riferimento timer
    
    return function executedFunction() {
        /**
         * FUNZIONE RITARDATA: Si esegue dopo wait millisecondi di inattività
         */
        const later = function() {
            clearTimeout(timeout);
            func.apply(null, arguments); // Mantiene contesto e argomenti originali
        };
        
        clearTimeout(timeout); // Cancella timer precedente
        timeout = setTimeout(later, wait); // Imposta nuovo timer
    };
}

// ===================================================================
// SEZIONE 12: UTILITY FUNCTIONS - Funzioni di Supporto
// ===================================================================

/**
 * FUNZIONE: updateTooltip(element)
 * SCOPO: Aggiorna/reinizializza tooltip Bootstrap per elemento specifico
 * LINGUAGGIO: JavaScript + Bootstrap 5 API
 * PARAMETRO: element (DOM Element) - Elemento di cui aggiornare tooltip
 * RETURN: void
 * 
 * NECESSARIO: Dopo modifiche dinamiche attributi tooltip (es. title)
 */
function updateTooltip(element) {
    // Verifica disponibilità Bootstrap e classe Tooltip
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip && element) {
        const tooltipInstance = bootstrap.Tooltip.getInstance(element); // Ottiene istanza esistente
        
        if (tooltipInstance) {
            tooltipInstance.dispose(); // Rimuove istanza precedente
            new bootstrap.Tooltip(element); // Crea nuova istanza con dati aggiornati
        }
    }
}

/**
 * FUNZIONE: logSearchStats()
 * SCOPO: Registra statistiche ricerca per analytics e debugging
 * LINGUAGGIO: JavaScript + URL parsing + Console logging
 * RETURN: void
 * 
 * UTILITÀ: Monitoring utilizzo filtri e risultati per ottimizzazioni future
 */
function logSearchStats() {
    const urlParams = new URLSearchParams(window.location.search);
    
    /**
     * OGGETTO STATS: Raccoglie dati significativi ricerca corrente
     */
    const stats = {
        termine: urlParams.get('search') || null,        // Termine cercato
        status: urlParams.get('status') || null,         // Filtro status
        staff_id: urlParams.get('staff_id') || null,     // Filtro staff
        risultati: $('.product-card').length,           // Numero risultati mostrati
        pagina: urlParams.get('page') || 1              // Pagina corrente
    };
    
    // LOG: Solo se c'è attività di ricerca/filtri
    if (stats.termine || stats.status || stats.staff_id) {
        console.log('Ricerca admin:', stats);
    }
}

// ===================================================================
// SEZIONE 13: ESPORTAZIONE FUNZIONI GLOBALI - Interoperabilità
// ===================================================================

/**
 * ESPORTAZIONE GLOBALE: Rende funzioni accessibili da altri script
 * SCOPO: Retrocompatibilità con codice HTML inline e altri file JS
 * LINGUAGGIO: JavaScript Window Object manipulation
 * 
 * ATTENZIONE: Le funzioni globali possono essere chiamate da:
 * - Eventi onclick inline nell'HTML
 * - Altri file JavaScript
 * - Console browser per debugging
 */

// Funzioni singole per compatibilità HTML onclick
window.selectAllProducts = selectAllProducts;
window.deselectAllProducts = deselectAllProducts;
window.bulkActivateProducts = bulkActivateProducts;
window.bulkDeactivateProducts = bulkDeactivateProducts;
window.bulkDeleteProducts = bulkDeleteProducts;
window.confirmToggleStatus = confirmToggleStatus;

/**
 * OGGETTO NAMESPACE: Raggruppa funzioni correlate per organizzazione migliore
 * VANTAGGIO: Evita inquinamento namespace globale
 */
window.adminProdottiActions = {
    selectAll: selectAllProducts,
    deselectAll: deselectAllProducts,
    bulkActivate: bulkActivateProducts,
    bulkDeactivate: bulkDeactivateProducts,
    bulkDelete: bulkDeleteProducts
};

// ===================================================================
// SEZIONE 14: CLEANUP E FINALIZZAZIONE - Gestione Risorse
// ===================================================================

/**
 * EVENT LISTENER: Cleanup risorse prima di abbandonare pagina
 * SCOPO: Prevenire memory leaks e pulizia elementi temporanei
 * LINGUAGGIO: jQuery Events + DOM cleanup
 * 
 * IMPORTANTE: Si esegue prima di navigazione, refresh, chiusura tab
 */
$(window).on('beforeunload', function() {
    $('.toast-notification').remove(); // Rimuove notifiche attive
    $('#loadingOverlay').remove();      // Rimuove overlay loading
    console.log('Pulizia risorse completata');
});

/**
 * INIZIALIZZAZIONE RITARDATA: Statistics logging dopo caricamento completo
 * SCOPO: Garantisce che DOM sia stabile prima di contare elementi
 * LINGUAGGIO: JavaScript Timers
 */
$(document).ready(function() {
    setTimeout(logSearchStats, 1000); // Ritardo 1 secondo per stabilità
});

/**
 * MESSAGGIO FINALE: Conferma caricamento completo per debugging
 */
console.log('Admin prodotti JavaScript caricato completamente');

// ===================================================================
// FINE DEL FILE
// ===================================================================

/**
 * RIEPILOGO ARCHITETTURA:
 * 
 * 1. INIZIALIZZAZIONE (Sezioni 1-3):
 *    - Configurazione globale e verifica ambiente
 *    - Setup CSRF, tooltip, layout responsive
 * 
 * 2. GESTIONE EVENTI (Sezioni 4-5):
 *    - Event listeners per tutti gli elementi UI
 *    - Keyboard shortcuts per power users
 * 
 * 3. SELEZIONE MULTIPLA (Sezione 6):
 *    - Logica checkbox e stato UI
 *    - Utility per get/set selezioni
 * 
 * 4. AZIONI BULK (Sezione 7):
 *    - Operazioni multiple via AJAX
 *    - Gestione errori comprehensive
 * 
 * 5. UI/UX ENHANCEMENTS (Sezioni 8-11):
 *    - Feedback visivo e notifiche
 *    - Loading states e responsive design
 * 
 * 6. UTILITIES E CLEANUP (Sezioni 12-14):
 *    - Funzioni supporto e gestione risorse
 *    - Esportazione per interoperabilità
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const/let, arrow functions, template literals)
 * - jQuery 3.x per DOM manipulation e AJAX
 * - Bootstrap 5 per UI components e responsività
 * - Laravel CSRF token per sicurezza
 * - Bootstrap Icons per iconografia
 * 
 * PATTERN ARCHITETTURALI:
 * - Module Pattern per organizzazione codice
 * - Event Delegation per elementi dinamici
 * - Debouncing per ottimizzazione performance
 * - Progressive Enhancement per accessibilità
 * - Error Handling comprehensive per robustezza
 */