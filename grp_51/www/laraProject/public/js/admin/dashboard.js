/**
 * FILE: admin.dashboard.js
 * LINGUAGGIO: JavaScript (con libreria jQuery)
 * SCOPO: Gestione dinamica della dashboard amministratore per il sistema di assistenza tecnica
 * 
 * Questo file implementa le funzionalità client-side per la dashboard dell'amministratore,
 * inclusi aggiornamenti automatici delle statistiche, controllo dello stato del sistema
 * e gestione dei prodotti non assegnati.
 */

// EVENTO PRINCIPALE - Si attiva quando il DOM è completamente caricato
$(document).ready(function() {
    console.log('admin.dashboard caricato');
    
    // CONTROLLO ROUTE - Verifica che siamo nella pagina corretta
    // window.LaravelApp.route è una variabile JavaScript globale impostata da Laravel nelle view
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.dashboard') {
        return; // Esce se non siamo nella dashboard admin
    }
    
    // VARIABILI GLOBALI DEL MODULO
    const pageData = window.PageData || {}; // Dati passati dalla view Laravel
    let selectedProducts = []; // Array per gestire prodotti selezionati (per future implementazioni)
    
    console.log('🔧 Dashboard Admin inizializzata');
    
    /**
     * TIMER AUTOMATICO #1 - Aggiornamento statistiche ogni 5 minuti
     * setInterval() è una funzione JavaScript nativa che esegue codice ripetutamente
     * Parametri: funzione da eseguire, intervallo in millisecondi
     */
    setInterval(function() {
        updateAdminStats(); // Chiama la funzione di aggiornamento statistiche
    }, 300000); // 300000ms = 5 minuti
    
    /**
     * TIMER AUTOMATICO #2 - Controllo stato sistema ogni 3 minuti
     * Monitora la salute dei servizi (database, storage, etc.)
     */
    setInterval(function() {
        checkSystemStatus(); // Chiama la funzione di controllo sistema
    }, 180000); // 180000ms = 3 minuti
    
    /**
     * FUNZIONE AJAX - Aggiornamento statistiche dashboard amministratore
     * LINGUAGGIO: JavaScript con jQuery per le chiamate AJAX
     * 
     * Questa funzione esegue una chiamata HTTP GET al server Laravel per ottenere
     * statistiche aggiornate (numero utenti, prodotti, centri assistenza, soluzioni)
     * 
     * AJAX = Asynchronous JavaScript and XML (ora usa JSON)
     * Permette di aggiornare parti della pagina senza ricaricarla completamente
     */
    function updateAdminStats() {
        // $.ajax() è il metodo jQuery per chiamate HTTP asincrone
        $.ajax({
            // URL corretto per l'ambiente del progetto universitario
            // /~grp_51/ è il path del gruppo 51 sul server tweban.dii.univpm.it
            url: '/~grp_51/laraProject/public/api/admin/stats-update',
            method: 'GET', // Metodo HTTP GET per ottenere dati
            dataType: 'json', // Formato risposta atteso: JSON
            
            // CALLBACK DI SUCCESSO - Si esegue se la chiamata HTTP va a buon fine
            success: function(data) {
                // Verifica che la risposta contenga dati validi
                if (data.success && data.stats) {
                    console.log('📊 Statistiche aggiornate', data.stats);
                    
                    // Aggiorna i contatori numerici nelle card Bootstrap
                    updateStatCards(data.stats);
                    
                    // Aggiorna la sezione prodotti non assegnati allo staff
                    updateProdottiNonAssegnati(data.stats);
                    
                    // Aggiorna timestamp ultimo aggiornamento nel footer della dashboard
                    const now = new Date().toLocaleTimeString('it-IT');
                    $('#last-update-time').text(now); // jQuery selector per ID
                    
                    console.log('✅ Statistiche dashboard aggiornate con successo');
                }
            },
            
            // CALLBACK DI ERRORE - Si esegue se la chiamata HTTP fallisce
            error: function(xhr, status, error) {
                console.error('❌ Errore aggiornamento statistiche:', error);
                
                // Gestisce errori solo se non sono 404 di sviluppo
                // Durante lo sviluppo alcune route potrebbero non esistere ancora
                if (xhr.status !== 404) {
                    showUpdateError(); // Mostra notifica di errore all'utente
                }
            }
        });
    }
    
    /**
     * FUNZIONE AJAX - Controllo stato servizi di sistema
     * SCOPO: Verifica che database, storage e altri servizi siano operativi
     * 
     * Questa funzione monitora la salute dell'infrastruttura del sistema
     * per avvisare l'amministratore di eventuali problemi
     */
    function checkSystemStatus() {
        $.ajax({
            // URL per l'API di controllo stato sistema
            url: '/~grp_51/laraProject/public/api/admin/system-status',
            method: 'GET',
            
            // CALLBACK SUCCESSO - Sistema risponde correttamente
            success: function(data) {
                if (data.success) {
                    console.log('🟢 Sistema operativo:', data.status);
                    // Aggiorna gli indicatori visivi dello stato
                    updateSystemStatus(data.components);
                }
            },
            
            // CALLBACK ERRORE - Problemi di comunicazione con il sistema
            error: function(xhr, status, error) {
                // Logga solo errori reali, non 404 di sviluppo
                if (xhr.status !== 404) {
                    console.warn('⚠️ Controllo stato sistema fallito:', error);
                }
            }
        });
    }
    
    /**
     * FUNZIONE DOM MANIPULATION - Aggiorna contatori statistiche
     * LINGUAGGIO: JavaScript + jQuery per manipolazione DOM
     * 
     * @param {Object} stats - Oggetto JavaScript contenente le statistiche dal server Laravel
     * 
     * Questa funzione aggiorna i numeri visualizzati nelle card Bootstrap della dashboard
     * utilizzando i selettori CSS di jQuery per trovare gli elementi HTML
     */
    function updateStatCards(stats) {
        // MAPPATURA DATI - Collega chiavi JSON a selettori CSS
        const statMappings = {
            'total_utenti': '.text-danger h5',    // Selettore CSS per contatore utenti
            'total_prodotti': '.text-primary h5',  // Selettore CSS per contatore prodotti
            'total_centri': '.text-info h5',       // Selettore CSS per contatore centri
            'total_soluzioni': '.text-success h5'  // Selettore CSS per contatore soluzioni
        };
        
        // ITERAZIONE OGGETTO - $.each() è il metodo jQuery per cicli
        $.each(statMappings, function(statKey, selector) {
            // Verifica che il dato esista nell'oggetto stats
            if (stats[statKey] !== undefined) {
                // $(selector) trova l'elemento HTML, .text() ne modifica il contenuto
                $(selector).text(formatNumber(stats[statKey]));
            }
        });
    }
    
    /**
     * FUNZIONE CRITICA - Gestione prodotti non assegnati
     * SCOPO: Questa è la funzione più importante per il controllo qualità del sistema
     * 
     * Nel sistema di assistenza tecnica, ogni prodotto deve essere assegnato a un membro
     * dello staff per la gestione dei malfunzionamenti. Questa funzione monitora e visualizza
     * i prodotti che non hanno ancora un responsabile assegnato.
     * 
     * @param {Object} stats - Statistiche dal controller Laravel contenenti:
     *                        - prodotti_non_assegnati_count: numero totale
     *                        - prodotti_non_assegnati: array di oggetti prodotto
     */
    function updateProdottiNonAssegnati(stats) {
        // SELEZIONE ELEMENTO DOM - Trova il container HTML tramite ID
        const container = $('#prodotti-non-assegnati-container');
        
        // CONTROLLO ESISTENZA - Verifica che l'elemento esista nel DOM
        if (!container.length) {
            console.warn('⚠️ Container prodotti non assegnati non trovato');
            return; // Esce dalla funzione se l'elemento non esiste
        }
        
        // ESTRAZIONE DATI - Ottiene i dati dall'oggetto stats con valori default
        const count = stats.prodotti_non_assegnati_count || 0;
        const prodotti = stats.prodotti_non_assegnati || [];
        
        console.log(`📦 Prodotti non assegnati: ${count}`, prodotti);
        
        // LOGICA CONDIZIONALE - Comportamento diverso in base al numero di prodotti
        if (count > 0) {
            // CASO: CI SONO PRODOTTI NON ASSEGNATI (SITUAZIONE DI ALLERTA)
            
            // Aggiorna o crea il badge di notifica nell'header della card
            const headerBadge = $('.card-header:has(.bi-exclamation-triangle) .badge');
            if (headerBadge.length) {
                // Badge esiste già - aggiorna il numero
                headerBadge.text(count).removeClass('d-none');
            } else {
                // Badge non esiste - lo crea dinamicamente
                $('.card-header:has(.bi-exclamation-triangle) h6').append(`
                    <span class="badge bg-danger ms-2">${count}</span>
                `);
            }
            
            // COSTRUZIONE HTML DINAMICO - Crea il markup per la lista prodotti
            let html = `
                <div class="alert alert-warning py-2 mb-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>${count} prodotti</strong> senza staff
                </div>
            `;
            
            // Se ci sono prodotti specifici da mostrare nella lista
            if (prodotti.length > 0) {
                html += '<div class="list-group list-group-flush">';
                
                // ITERAZIONE ARRAY - Mostra massimo 3 prodotti per non sovraccaricare l'UI
                prodotti.slice(0, 3).forEach(function(prodotto) {
                    // Template HTML per ogni prodotto con pulsante di assegnazione
                    html += `
                        <div class="list-group-item px-0 border-0 border-bottom py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold small">${prodotto.nome}</h6>
                                    <small class="text-muted">${prodotto.categoria || 'N/A'}</small>
                                </div>
                                <div class="text-end">
                                    <a href="/admin/assegnazioni?prodotto=${prodotto.id}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-person-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                
                // Pulsante per andare alla pagina di gestione completa
                html += `
                    <div class="text-center mt-2">
                        <a href="/admin/assegnazioni" 
                           class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-gear me-1"></i>Gestisci Assegnazioni
                        </a>
                    </div>
                `;
            }
            
            // AGGIORNAMENTO DOM - Sostituisce completamente il contenuto del container
            container.html(html);
            
        } else {
            // CASO: TUTTI I PRODOTTI SONO ASSEGNATI (SITUAZIONE OTTIMALE)
            
            // Rimuove il badge di allerta se presente
            $('.card-header:has(.bi-exclamation-triangle) .badge').remove();
            
            // Mostra messaggio di successo con icona
            const successHtml = `
                <div class="text-center py-3">
                    <i class="bi bi-check-circle display-4 text-success opacity-75"></i>
                    <p class="text-success mt-2 mb-0 small">Tutti i prodotti assegnati</p>
                </div>
            `;
            
            container.html(successHtml);
        }
    }
    
    /**
     * FUNZIONE UI UPDATE - Aggiorna indicatori stato sistema
     * LINGUAGGIO: JavaScript + jQuery per manipolazione classi CSS
     * 
     * @param {Object} status - Oggetto con stato dei servizi (es: {database: 'online', storage: 'writable'})
     * 
     * Questa funzione aggiorna visivamente lo stato dei servizi di sistema
     * utilizzando badge colorati Bootstrap per indicare lo stato di salute
     */
    function updateSystemStatus(status) {
        // ITERAZIONE OGGETTO - Per ogni servizio di sistema
        $.each(status, function(component, state) {
            // SELEZIONE COMPLESSA - Trova il badge del servizio specifico
            const badge = $(`.list-group-item:contains("${component}") .badge`);
            
            if (badge.length) {
                // RIMOZIONE CLASSI CSS - Pulisce gli stati precedenti
                badge.removeClass('bg-success bg-warning bg-danger bg-info');
                
                // LOGICA SWITCH - Applica colore in base allo stato
                switch(state) {
                    case 'online':
                    case 'writable':
                    case 'active':
                        // STATO POSITIVO - Verde Bootstrap
                        badge.addClass('bg-success').text('Online');
                        break;
                    case 'read-only':
                        // STATO LIMITATO - Giallo Bootstrap
                        badge.addClass('bg-warning').text('Read-Only');
                        break;
                    case 'error':
                        // STATO ERRORE - Rosso Bootstrap
                        badge.addClass('bg-danger').text('Errore');
                        break;
                    default:
                        // STATO GENERICO - Blu Bootstrap
                        badge.addClass('bg-info').text(state);
                }
            }
        });
    }
    
    /**
     * FUNZIONE UI FEEDBACK - Notifica errore temporanea
     * SCOPO: Informa l'utente quando l'aggiornamento automatico fallisce
     * 
     * Crea un alert Bootstrap temporaneo in posizione fixed per non disturbare
     * il workflow dell'amministratore
     */
    function showUpdateError() {
        // CREAZIONE ELEMENTO DINAMICO - Template HTML per alert Bootstrap
        const indicator = $(`
            <div class="position-fixed top-0 end-0 m-3 alert alert-danger alert-dismissible" style="z-index: 9999;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Errore aggiornamento dati
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // INSERIMENTO DOM - Aggiunge l'alert al body della pagina
        $('body').append(indicator);
        
        // TIMER RIMOZIONE - Auto-rimozione dopo 3 secondi
        setTimeout(function() {
            // ANIMAZIONE FADE - Rimozione graduale con jQuery
            indicator.fadeOut(function() {
                $(this).remove(); // Rimuove completamente dal DOM
            });
        }, 3000);
    }
    
    /**
     * EVENT HANDLER - Gestione pulsante aggiornamento manuale
     * LINGUAGGIO: jQuery Event Handling
     * 
     * Permette all'amministratore di forzare un aggiornamento immediato
     * delle statistiche senza aspettare il timer automatico
     */
    if ($('#manual-refresh-btn').length) {
        // BINDING EVENTO - Collega funzione al click del pulsante
        $('#manual-refresh-btn').on('click', function() {
            // FEEDBACK VISIVO - Cambia aspetto pulsante durante loading
            $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiornamento...');
            $(this).prop('disabled', true); // Disabilita per evitare click multipli
            
            // CHIAMATA FUNZIONE - Esegue aggiornamento con URL corretto
            updateAdminStats();
            
            // RIPRISTINO UI - Torna allo stato normale dopo 2 secondi
            setTimeout(() => {
                $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiorna');
                $(this).prop('disabled', false);
            }, 2000);
        });
    }
    
    console.log('✅ Sistema di aggiornamento dashboard admin attivato');
});

/**
 * SEZIONE FUNZIONI GLOBALI HELPER
 * Queste funzioni sono disponibili in tutto il scope globale JavaScript
 * e possono essere riutilizzate da altri moduli
 */

/**
 * FUNZIONE UTILITY - Formattazione numeri internazionale
 * LINGUAGGIO: JavaScript nativo (ES6+)
 * 
 * @param {number} num - Numero intero o decimale da formattare
 * @returns {string} - Stringa formattata secondo localizzazione italiana
 * 
 * Esempio: formatNumber(1234) → "1.234"
 *          formatNumber(1234567) → "1.234.567"
 */
function formatNumber(num) {
    // Intl.NumberFormat è l'API JavaScript standard per formattazione numeri
    // 'it-IT' specifica la localizzazione italiana (punto come separatore migliaia)
    return new Intl.NumberFormat('it-IT').format(num);
}

/**
 * FUNZIONE UI - Sistema notifiche toast Bootstrap
 * LINGUAGGIO: JavaScript + Bootstrap API
 * 
 * @param {string} message - Testo del messaggio da mostrare
 * @param {string} type - Tipo Bootstrap: 'success', 'warning', 'danger', 'info'
 * 
 * Crea notifiche non invasive nell'angolo superiore destro dello schermo
 * utilizzando il componente Toast di Bootstrap 5
 */
function showNotification(message, type = 'success') {
    // TEMPLATE DINAMICO - Crea HTML per toast Bootstrap
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" 
             role="alert" style="z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    // INSERIMENTO DOM
    $('body').append(toast);
    
    // INIZIALIZZAZIONE BOOTSTRAP - Attiva funzionalità toast
    const bsToast = new bootstrap.Toast(toast[0]); // Usa API Bootstrap nativa
    bsToast.show(); // Mostra il toast con animazione
    
    // CLEANUP AUTOMATICO - Rimuove toast dopo 5 secondi
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

/**
 * FUNZIONE ERROR HANDLING - Gestione centralizzata errori AJAX
 * SCOPO: Standardizza la gestione degli errori HTTP in tutta l'applicazione
 * 
 * @param {Object} xhr - Oggetto XMLHttpRequest dell'errore jQuery
 * @param {string} context - Descrizione del contesto per debugging
 * 
 * Questa funzione analizza il tipo di errore HTTP e mostra messaggi
 * appropriati all'utente, facilitando il troubleshooting
 */
function handleAjaxError(xhr, context) {
    // LOGGING - Registra errore completo per sviluppatori
    console.error(`❌ Errore AJAX in ${context}:`, xhr.responseText);
    
    // MESSAGGIO DEFAULT
    let message = 'Si è verificato un errore durante il caricamento dei dati.';
    
    // ANALISI CODICE HTTP - Personalizza messaggio in base al tipo di errore
    switch(xhr.status) {
        case 403:
            // FORBIDDEN - Problema di autorizzazione
            message = 'Non hai i permessi per accedere a questi dati.';
            break;
        case 404:
            // NOT FOUND - Risorsa inesistente
            message = 'Risorsa non trovata.';
            break;
        case 500:
            // INTERNAL SERVER ERROR - Errore lato server
            message = 'Errore interno del server.';
            break;
        case 0:
            // NETWORK ERROR - Problema di connettività
            message = 'Problemi di connessione. Controlla la tua connessione internet.';
            break;
    }
    
    // NOTIFICA UTENTE - Mostra messaggio con stile di errore
    showNotification(message, 'danger');
}

/**
 * FUNZIONE NETWORK - Test connettività sistema
 * LINGUAGGIO: jQuery AJAX con Promise
 * 
 * Verifica che il server Laravel sia raggiungibile e risponda correttamente
 * utilizzando un endpoint di test leggero
 * 
 * @returns {Promise} - Promise jQuery che risolve con true/false
 */
function checkConnectivity() {
    // CHIAMATA AJAX CON TIMEOUT - Test veloce connettività
    return $.ajax({
        url: '/~grp_51/laraProject/public/api/admin/system-status', // Endpoint esistente
        method: 'GET',
        timeout: 5000 // Timeout di 5 secondi
    }).done(function() {
        // SUCCESSO - Server raggiungibile
        console.log('✅ Connettività OK');
        return true;
    }).fail(function(xhr) {
        // FALLIMENTO - Analizza tipo di errore
        if (xhr.status === 0) {
            // Errore di rete reale
            console.warn('⚠️ Problemi di connettività rilevati');
            showNotification('Problemi di connessione rilevati', 'warning');
        }
        // 404 durante sviluppo è normale, non notificare
        return false;
    });
}

/**
 * EVENT LISTENERS GLOBALI - Gestione stato connessione browser
 * LINGUAGGIO: JavaScript Event API
 * 
 * Questi listener intercettano gli eventi nativi del browser per
 * monitorare lo stato della connessione internet dell'utente
 */

// EVENTO ONLINE - Si attiva quando la connessione viene ripristinata
$(window).on('online', function() {
    showNotification('Connessione ripristinata', 'success');
    updateAdminStats(); // Ricarica dati appena possibile
});

// EVENTO OFFLINE - Si attiva quando la connessione viene persa
$(window).on('offline', function() {
    showNotification('Connessione persa. I dati potrebbero non essere aggiornati.', 'warning');
});

/**
 * FUNZIONE INIZIALIZZAZIONE - Setup configurazioni globali
 * SCOPO: Configura l'ambiente JavaScript per l'intera applicazione admin
 * 
 * Questa funzione viene chiamata una sola volta all'avvio per impostare
 * le configurazioni globali di jQuery, CSRF token e gestione errori
 */
function initAdminDashboard() {
    // CONFIGURAZIONE JQUERY AJAX - Setup globale per tutte le chiamate
    $.ajaxSetup({
        headers: {
            // TOKEN CSRF - Laravel richiede questo header per sicurezza
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // ERROR HANDLER GLOBALE - Gestisce errori di tutte le chiamate AJAX
        error: function(xhr, status, error) {
            // Filtra errori 404 di sviluppo che sono normali
            if (xhr.status !== 404) {
                handleAjaxError(xhr, 'Request generico');
            }
        }
    });
    
    // TEST INIZIALE - Verifica connettività al caricamento pagina
    checkConnectivity();
    
    // LOGGING SVILUPPO - Conferma inizializzazione e route disponibili
    console.log('🚀 Dashboard Admin completamente inizializzata');
    console.log('✅ Route API disponibili:');
    console.log('   - GET /api/admin/stats-update');
    console.log('   - GET /api/admin/system-status');
}

// INIZIALIZZAZIONE AUTOMATICA - Avvia tutto quando il DOM è pronto
$(document).ready(initAdminDashboard);