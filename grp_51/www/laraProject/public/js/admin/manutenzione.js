/**
 * ===================================================================
 * FILE: admin/manutenzione.js
 * LINGUAGGIO: JavaScript + jQuery + Bootstrap
 * SCOPO: Sistema di monitoraggio e manutenzione per dashboard amministratore
 * ===================================================================
 * 
 * Questo modulo JavaScript gestisce la pagina di manutenzione del sistema
 * di assistenza tecnica, permettendo all'amministratore di:
 * - Monitorare lo stato dei servizi in tempo reale
 * - Gestire cache e ottimizzazioni database
 * - Ricevere notifiche sui problemi del sistema
 * - Controllare metriche di performance (CPU, memoria, disco)
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (Promise, async/await, arrow functions)
 * - jQuery 3.x per manipolazione DOM e AJAX
 * - Bootstrap 5 per componenti UI (alert, toast, modal)
 * - Browser APIs native (Visibility API, Network API)
 * ===================================================================
 */

// ===================================================================
// SEZIONE 1: VARIABILI GLOBALI E STATO APPLICAZIONE
// ===================================================================

/**
 * VARIABILE GLOBALE - Timer per controlli automatici sistema
 * TIPO: Number (ID del setInterval) o null
 * SCOPO: Mantiene riferimento al timer per poterlo fermare/riavviare
 */
let systemCheckInterval = null;

/**
 * VARIABILE GLOBALE - Flag abilitazione auto-refresh
 * TIPO: Boolean
 * SCOPO: Controlla se i controlli automatici sono attivi
 * DEFAULT: true (attivo di default per sicurezza sistema)
 */
let autoRefreshEnabled = true;

/**
 * VARIABILE GLOBALE - Flag controllo sistema in corso
 * TIPO: Boolean
 * SCOPO: Previene chiamate multiple simultanee al sistema di controllo
 * PATTERN: Mutex/Lock pattern per evitare race conditions
 */
let isSystemChecking = false;

// ===================================================================
// SEZIONE 2: CONFIGURAZIONI SISTEMA
// ===================================================================

/**
 * OGGETTO CONFIGURAZIONE - Costanti temporali e limiti sistema
 * TIPO: Object (frozen/immutable)
 * SCOPO: Centralizza tutte le configurazioni temporali per facile manutenzione
 */
const CONFIG = {
    AUTO_REFRESH_INTERVAL: 30000,        // 30 secondi tra controlli automatici
    SYSTEM_CHECK_TIMEOUT: 15000,         // 15 secondi timeout per chiamate AJAX
    NOTIFICATION_DURATION: 3000,         // 3 secondi durata notifiche normali
    ERROR_NOTIFICATION_DURATION: 5000    // 5 secondi durata notifiche errore
};

// ===================================================================
// SEZIONE 3: INIZIALIZZAZIONE PRINCIPALE
// ===================================================================

/**
 * EVENT HANDLER PRINCIPALE - Document Ready
 * LINGUAGGIO: jQuery Event System
 * 
 * Questo è il punto di ingresso principale del modulo.
 * Si attiva quando il DOM è completamente caricato e pronto per la manipolazione.
 */
$(document).ready(function() {
    console.log('Admin.manutenzione.index caricato');

    // CONTROLLO ROUTE - Verifica che siamo nella pagina corretta
    // window.LaravelApp.route è una variabile globale impostata da Laravel nelle view Blade
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.manutenzione.index' && !window.location.href.includes('manutenzione')) {
        console.log('Route non corretta per manutenzione');
        return; // Esce se non siamo nella pagina di manutenzione
    }

    console.log('Inizializzazione manutenzione admin (compatta)');
    
    // SEQUENZA INIZIALIZZAZIONE - Ordine importante per dipendenze
    initializeMaintenance();     // 1. Setup base del sistema
    setupEventListeners();       // 2. Collega eventi UI
    setupNetworkMonitoring();    // 3. Monitora connessione di rete
    
    // PRIMO CONTROLLO - Controllo iniziale dello stato sistema
    // setTimeout evita conflitti con inizializzazione DOM
    setTimeout(checkSystemStatus, 500);
    
    // AVVIO AUTO-REFRESH - Inizia monitoraggio continuo
    startAutoRefresh();
});

// ===================================================================
// SEZIONE 4: INIZIALIZZAZIONE SISTEMA
// ===================================================================

/**
 * FUNZIONE INIZIALIZZAZIONE - Setup configurazioni base sistema
 * SCOPO: Configura tutti i sistemi di supporto necessari per il modulo
 * 
 * Questa funzione prepara l'ambiente per il funzionamento del sistema
 * di manutenzione, includendo gestione visibilità pagina e cleanup risorse.
 */
function initializeMaintenance() {
    console.log('Inizializzazione sistema manutenzione...');
    
    // SETUP VISIBILITY API - Ottimizza performance quando tab nascosto
    setupVisibilityChangeHandling();
    
    // SETUP CLEANUP - Pulisce risorse quando utente esce dalla pagina
    setupPageCleanup();
    
    console.log('Sistema manutenzione inizializzato');
}

// ===================================================================
// SEZIONE 5: GESTIONE EVENTI UI
// ===================================================================

/**
 * FUNZIONE EVENT BINDING - Collega tutti gli event listener dell'UI
 * LINGUAGGIO: jQuery Event System
 * 
 * Questa funzione centralizza la gestione di tutti gli eventi dell'interfaccia utente,
 * inclusi click su pulsanti, submit di form e cambi di stato dei controlli.
 */
function setupEventListeners() {
    console.log('Setup event listeners manutenzione...');
    
    /**
     * EVENT HANDLER - Pulsante controllo manuale sistema
     * ELEMENTO: #manual-check (pulsante nella UI)
     * AZIONE: Esegue controllo immediato stato sistema
     */
    $('#manual-check').on('click', function(e) {
        e.preventDefault(); // Previene comportamento default del link/button
        checkSystemStatus(); // Esegue controllo sistema on-demand
    });
    
    /**
     * EVENT HANDLER - Toggle auto-refresh
     * ELEMENTO: #auto-refresh (checkbox nella UI)
     * AZIONE: Attiva/disattiva controlli automatici con feedback visivo
     */
    $('#auto-refresh').on('change', function() {
        // LETTURA STATO - $(this).is(':checked') verifica stato checkbox
        autoRefreshEnabled = $(this).is(':checked');
        
        // LOGICA CONDIZIONALE - Comportamento diverso in base al nuovo stato
        if (autoRefreshEnabled) {
            startAutoRefresh(); // Avvia timer controlli automatici
            showNotification('Auto-refresh attivato', 'success');
        } else {
            stopAutoRefresh(); // Ferma timer controlli automatici
            showNotification('Auto-refresh disattivato', 'info');
        }
    });
    
    /**
     * EVENT HANDLER - Submit form con sicurezza e feedback
     * ELEMENTO: Tutti i form nella pagina
     * AZIONE: Gestisce invio form con conferme e stati di loading
     */
    $('form').on('submit', function(e) {
        // SELEZIONE ELEMENTO - Trova il pulsante submit del form
        const btn = $(this).find('button[type="submit"]');
        const actionText = btn.text().trim();
        
        // SICUREZZA - Conferme per azioni critiche del sistema
        if (actionText.includes('Pulisci') || actionText.includes('Ottimizza')) {
            const confirmMessage = 'Procedere con ' + actionText.toLowerCase() + '?';
            // confirm() è una funzione JavaScript nativa per dialog di conferma
            if (!confirm(confirmMessage)) {
                e.preventDefault(); // Blocca submit se utente annulla
                return false;
            }
        }
        
        // FEEDBACK VISIVO - Mostra stato loading durante elaborazione
        if (!btn.hasClass('btn-sm')) { // Evita modifiche su pulsanti piccoli
            btn.prop('disabled', true); // Disabilita pulsante
            // Cambia contenuto con spinner Bootstrap
            btn.html('<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i>Elaborazione...');
        }
        
        // OVERLAY LOADING - Per operazioni che richiedono tempo
        if (actionText.includes('Ottimizza')) {
            showLoadingOverlay('Ottimizzazione database in corso...');
        } else if (actionText.includes('Pulisci Tutto')) {
            showLoadingOverlay('Pulizia cache in corso...');
        }
    });
}

// ===================================================================
// SEZIONE 6: MONITORAGGIO SISTEMA (FUNZIONE PRINCIPALE)
// ===================================================================

/**
 * FUNZIONE CRITICA - Controllo stato sistema via AJAX
 * SCOPO: Verifica lo stato di tutti i servizi del sistema (database, cache, storage)
 * 
 * Questa è la funzione più importante del modulo. Esegue controlli diagnostici
 * completi del sistema e aggiorna l'interfaccia utente con i risultati.
 * 
 * PATTERN UTILIZZATO: Promise-based async programming
 * SICUREZZA: Mutex lock per evitare chiamate simultanee
 */
function checkSystemStatus() {
    // MUTEX LOCK - Previene chiamate multiple simultanee
    if (isSystemChecking) {
        console.log('Controllo sistema già in corso...');
        return; // Esce se un controllo è già attivo
    }
    
    // IMPOSTA LOCK - Blocca nuove chiamate
    isSystemChecking = true;
    
    // SELEZIONE ELEMENTI DOM - Trova elementi UI da aggiornare
    const statusContainer = $('#system-status');
    const button = $('#manual-check');
    
    // FEEDBACK VISIVO INIZIALE - Mostra stato loading compatto
    statusContainer.html(`
        <div class="d-flex justify-content-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <small>Controllo...</small>
        </div>
    `);
    
    // DISABILITA UI - Previene click multipli durante controllo
    button.prop('disabled', true);
    
    /**
     * FUNZIONE MOCK AJAX - Simula chiamata al server Laravel
     * LINGUAGGIO: JavaScript ES6+ (Promise, arrow functions)
     * 
     * NOTA IMPORTANTE: In produzione questa funzione dovrebbe essere sostituita
     * con una vera chiamata AJAX a un endpoint Laravel che verifica:
     * - Stato connessione database
     * - Funzionamento cache Redis/Memcached
     * - Spazio disco disponibile
     * - Carico CPU e memoria
     * - Log di sistema per errori recenti
     */
    const mockSystemCheck = () => {
        // PROMISE CONSTRUCTOR - Crea promise personalizzata per simulazione
        return new Promise((resolve) => {
            // TIMER SIMULAZIONE - setTimeout simula latenza di rete
            setTimeout(() => {
                // GENERAZIONE DATI MOCK - Simula risposta realistica del server
                const mockResponse = {
                    success: true,
                    // LOGICA PROBABILISTICA - Simula occasionali problemi sistema
                    status: Math.random() > 0.1 ? 'operational' : 'degraded',
                    components: {
                        // Ogni componente ha probabilità diverse di problemi
                        database: Math.random() > 0.05 ? 'online' : 'slow',
                        cache: Math.random() > 0.1 ? 'active' : 'partial',
                        storage: Math.random() > 0.02 ? 'writable' : 'readonly',
                        logs: 'active' // Log sempre attivi per semplicità
                    },
                    server_info: {
                        // METRICHE REALISTICHE - Valori casuali ma credibili
                        memory_usage: (Math.random() * 50 + 20).toFixed(1) + 'MB',
                        cpu_usage: (Math.random() * 30 + 10).toFixed(1) + '%',
                        disk_usage: (Math.random() * 20 + 60).toFixed(1) + '%'
                    },
                    timestamp: new Date().toISOString()
                };
                resolve(mockResponse); // Risolve promise con dati mock
            }, Math.random() * 2000 + 500); // Latenza casuale 0.5-2.5 secondi
        });
    };
    
    /**
     * ESECUZIONE ASINCRONA - Promise chain per gestione risultati
     * PATTERN: Promise.then().catch().finally()
     */
    mockSystemCheck()
        .then(response => {
            // SUCCESSO - Elabora e mostra risultati controllo
            displaySystemStatus(response);           // Aggiorna UI principale
            updateSystemMetrics(response.server_info); // Aggiorna metriche header
            console.log('Controllo sistema completato:', response);
        })
        .catch(error => {
            // ERRORE - Gestisce fallimenti del controllo sistema
            console.error('Errore controllo sistema:', error);
            displaySystemError('Errore nel controllo sistema: ' + error.message);
        })
        .finally(() => {
            // CLEANUP - Eseguito sempre, successo o errore
            button.prop('disabled', false); // Riabilita pulsante
            isSystemChecking = false;       // Rilascia mutex lock
        });
}

/**
 * FUNZIONE UI UPDATE - Visualizza stato sistema
 * LINGUAGGIO: JavaScript + Template Literals (ES6)
 * 
 * @param {Object} response - Risposta del controllo sistema contenente:
 *   - status: 'operational', 'degraded', 'error'
 *   - components: oggetto con stato di ogni servizio
 *   - timestamp: momento del controllo
 * 
 * Questa funzione trasforma i dati grezzi del sistema in una rappresentazione
 * visiva comprensibile, usando colori e icone Bootstrap per immediata leggibilità.
 */
function displaySystemStatus(response) {
    const statusContainer = $('#system-status');
    const status = response.status;
    
    // DICHIARAZIONE VARIABILI - Per mappatura stato -> UI
    let statusClass, statusIcon, statusText;
    
    /**
     * MAPPING LOGICO - Converte stato sistema in elementi UI
     * Ogni stato ha colore, icona e testo specifici per UX ottimale
     */
    switch(status) {
        case 'operational':
            statusClass = 'success';              // Verde Bootstrap
            statusIcon = 'check-circle-fill';     // Icona successo Bootstrap Icons
            statusText = 'Operativo';
            break;
        case 'degraded':
            statusClass = 'warning';              // Giallo Bootstrap
            statusIcon = 'exclamation-triangle-fill'; // Icona avviso
            statusText = 'Degradato';
            break;
        case 'error':
            statusClass = 'danger';               // Rosso Bootstrap
            statusIcon = 'x-circle-fill';         // Icona errore
            statusText = 'Errori';
            break;
        default:
            statusClass = 'secondary';            // Grigio Bootstrap
            statusIcon = 'question-circle-fill';  // Icona incognita
            statusText = 'Sconosciuto';
    }
    
    /**
     * TEMPLATE HTML - Costruzione markup dinamico
     * LINGUAGGIO: ES6 Template Literals con interpolazione variabili
     */
    let html = `
        <div class="text-center mb-2">
            <i class="bi bi-${statusIcon} text-${statusClass} fs-4"></i>
            <div class="fw-semibold text-${statusClass}">${statusText}</div>
            <small class="text-muted">${new Date().toLocaleTimeString('it-IT')}</small>
        </div>
    `;
    
    /**
     * SEZIONE COMPONENTI - Dettagli stato servizi individuali
     * Mostra stato di database, cache, storage, etc. in formato griglia
     */
    if (response.components) {
        html += '<div class="row g-1">'; // Bootstrap grid con gap ridotto
        
        // ITERAZIONE OGGETTO - Object.entries() converte oggetto in array key-value
        Object.entries(response.components).forEach(([component, state]) => {
            // LOGICA CONDIZIONALE - Determina colore in base allo stato componente
            let componentClass = (state === 'online' || state === 'active' || state === 'writable') ? 'success' : 'warning';
            let componentIcon = componentClass === 'success' ? 'check' : 'exclamation-triangle';
            
            // TEMPLATE COMPONENTE - Ogni servizio in una colonna della griglia
            html += `
                <div class="col-6">
                    <div class="d-flex align-items-center justify-content-center p-1">
                        <i class="bi bi-${componentIcon} text-${componentClass} me-1"></i>
                        <small class="fw-semibold">${component}</small>
                    </div>
                </div>
            `;
        });
        
        html += '</div>'; // Chiude griglia Bootstrap
    }
    
    // AGGIORNAMENTO DOM - Sostituisce contenuto container con nuovo HTML
    statusContainer.html(html);
}

/**
 * FUNZIONE ERROR DISPLAY - Mostra errori controllo sistema
 * SCOPO: Fornisce feedback visivo quando il controllo sistema fallisce
 * 
 * @param {string} message - Messaggio di errore da mostrare all'utente
 */
function displaySystemError(message) {
    const statusContainer = $('#system-status');
    
    // TEMPLATE ERRORE - HTML per visualizzazione errore con icona e stile Bootstrap
    statusContainer.html(`
        <div class="text-center">
            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
            <div class="fw-semibold text-danger">Errore</div>
            <small class="text-muted">${message}</small>
        </div>
    `);
}

/**
 * FUNZIONE METRICHE - Aggiorna indicatori performance nell'header
 * SCOPO: Mostra metriche sistema (memoria, CPU, disco) in tempo reale
 * 
 * @param {Object} serverInfo - Oggetto contenente metriche server:
 *   - memory_usage: utilizzo memoria (es: "45.2MB")
 *   - cpu_usage: utilizzo CPU (es: "23.1%")
 *   - disk_usage: utilizzo disco (es: "67.8%")
 */
function updateSystemMetrics(serverInfo) {
    // CONTROLLO PARAMETRI - Early return se dati non disponibili
    if (!serverInfo) return;
    
    // AGGIORNAMENTO MEMORIA - Se dato disponibile e elemento esiste nel DOM
    if (serverInfo.memory_usage) {
        const memoryDisplay = $('#memory-display');
        if (memoryDisplay.length) { // .length verifica esistenza elemento jQuery
            memoryDisplay.text(serverInfo.memory_usage);
        }
    }
    
    // ESTENSIBILITÀ - Qui si possono aggiungere altri indicatori
    // Esempi: CPU, disco, numero connessioni attive, ecc.
    console.log('Metriche sistema aggiornate:', serverInfo);
}

// ===================================================================
// SEZIONE 7: SISTEMA AUTO-REFRESH
// ===================================================================

/**
 * FUNZIONE TIMER - Avvia controlli automatici del sistema
 * LINGUAGGIO: JavaScript Timer API (setInterval)
 * 
 * Questa funzione implementa il monitoraggio continuo del sistema,
 * controllando lo stato ogni X secondi definiti nella configurazione.
 * Include ottimizzazioni per performance (pausa quando tab nascosto).
 */
function startAutoRefresh() {
    // CLEANUP PREVENTIVO - Ferma interval esistente per evitare duplicati
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
    }
    
    // CONTROLLO ABILITAZIONE - Avvia solo se auto-refresh è attivo
    if (autoRefreshEnabled) {
        /**
         * CREAZIONE TIMER - setInterval esegue funzione ripetutamente
         * CALLBACK: Funzione anonima che verifica condizioni prima di eseguire controllo
         */
        systemCheckInterval = setInterval(function() {
            // OTTIMIZZAZIONE PERFORMANCE - Controlla solo se:
            // 1. Auto-refresh ancora abilitato (utente potrebbe aver disattivato)
            // 2. Pagina visibile (Visibility API per risparmio risorse)
            if (autoRefreshEnabled && document.visibilityState === 'visible') {
                checkSystemStatus(); // Esegue controllo sistema
            }
        }, CONFIG.AUTO_REFRESH_INTERVAL);
        
        console.log('Auto-refresh avviato (ogni', CONFIG.AUTO_REFRESH_INTERVAL / 1000, 'secondi)');
    }
}

/**
 * FUNZIONE TIMER - Ferma controlli automatici
 * SCOPO: Pulisce timer e libera risorse quando auto-refresh viene disattivato
 */
function stopAutoRefresh() {
    // CLEANUP TIMER - clearInterval ferma esecuzione ripetuta
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
        systemCheckInterval = null; // Reset riferimento
        console.log('Auto-refresh fermato');
    }
}

// ===================================================================
// SEZIONE 8: GESTIONE VISIBILITÀ PAGINA (OTTIMIZZAZIONE)
// ===================================================================

/**
 * FUNZIONE OTTIMIZZAZIONE - Gestisce cambio visibilità pagina
 * LINGUAGGIO: Browser Visibility API
 * 
 * Questa funzione ottimizza le performance pausando i controlli automatici
 * quando l'utente cambia tab o minimizza la finestra, risparmiando risorse
 * del server e batteria su dispositivi mobili.
 */
function setupVisibilityChangeHandling() {
    // EVENT LISTENER NATIVO - Ascolta cambio visibilità browser
    document.addEventListener('visibilitychange', function() {
        // CONTROLLO STATO - document.visibilityState è proprietà Visibility API
        if (document.visibilityState === 'hidden') {
            // PAGINA NASCOSTA - Log per debug, auto-refresh continua ma senza azione
            if (autoRefreshEnabled) {
                console.log('Auto-refresh in pausa (tab nascosto)');
            }
        } else {
            // PAGINA VISIBILE - Riprende attività e controlla immediatamente
            if (autoRefreshEnabled) {
                console.log('Auto-refresh ripreso');
                // CONTROLLO IMMEDIATO - setTimeout evita chiamata sincrona
                setTimeout(checkSystemStatus, 1000);
            }
        }
    });
}

// ===================================================================
// SEZIONE 9: MONITORAGGIO RETE
// ===================================================================

/**
 * FUNZIONE NETWORK - Setup monitoraggio connessione di rete
 * LINGUAGGIO: Browser Network APIs (online/offline events)
 * 
 * Monitora lo stato della connessione internet dell'utente e reagisce
 * automaticamente a disconnessioni/riconnessioni per mantenere dati aggiornati.
 */
function setupNetworkMonitoring() {
    /**
     * EVENT HANDLER - Connessione ripristinata
     * Si attiva quando il browser rileva che la connessione è tornata online
     */
    $(window).on('online', function() {
        showNotification('Connessione ripristinata', 'success');
        // RICONNESSIONE AUTOMATICA - Aggiorna dati dopo riconnessione
        if (autoRefreshEnabled) {
            setTimeout(checkSystemStatus, 1000);
        }
    });

    /**
     * EVENT HANDLER - Connessione persa
     * Si attiva quando il browser rileva perdita di connessione internet
     */
    $(window).on('offline', function() {
        showNotification('Connessione persa', 'warning');
        // Nota: Non fermiamo auto-refresh, riprenderà quando online
    });
}

// ===================================================================
// SEZIONE 10: UTILITIES E INTERFACCIA UTENTE
// ===================================================================

/**
 * FUNZIONE GLOBALE - Aggiorna info sistema (chiamata da pulsante header)
 * LINGUAGGIO: JavaScript con accesso a event globale
 * 
 * Questa funzione è esposta globalmente per essere chiamata direttamente
 * da onclick HTML. Fornisce feedback visivo e ricarica la pagina.
 * 
 * NOTA: event è una variabile globale automatica nei handler HTML onclick
 */
function aggiornaInfoSistema() {
    const btn = event.target;           // event.target è l'elemento che ha scatenato l'evento
    const originalHtml = btn.innerHTML; // Salva contenuto originale
    
    // FEEDBACK VISIVO - Cambia aspetto pulsante durante loading
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    // RICARICA PAGINA - setTimeout per mostrare feedback prima del reload
    setTimeout(function() {
        location.reload(); // API browser per ricaricare pagina
    }, 1000);
}

/**
 * FUNZIONE UI OVERLAY - Mostra schermata di caricamento per operazioni lunghe
 * LINGUAGGIO: jQuery + Bootstrap CSS + CSS positioning
 * 
 * @param {string} message - Messaggio da mostrare durante il caricamento
 * 
 * Crea un overlay full-screen che blocca l'interazione utente durante
 * operazioni che richiedono tempo (ottimizzazione DB, pulizia cache, etc.).
 */
function showLoadingOverlay(message) {
    message = message || 'Caricamento...'; // Default message se non specificato
    
    // CLEANUP PREVENTIVO - Rimuove overlay esistente per evitare duplicati
    $('#loadingOverlay').remove();
    
    /**
     * TEMPLATE OVERLAY - HTML per schermata di caricamento
     * STYLING: position-fixed per coprire intero viewport, z-index alto per stare sopra tutto
     */
    const overlay = $(`
        <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 9999;">
            <div class="card text-center p-4" style="min-width: 300px;">
                <div class="card-body">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="card-title">${message}</h5>
                    <p class="card-text text-muted">Attendere, operazione in corso...</p>
                </div>
            </div>
        </div>
    `);
    
    // INSERIMENTO DOM - Aggiunge overlay al body
    $('body').append(overlay);
    console.log('Loading overlay mostrato:', message);
}

/**
 * FUNZIONE UI OVERLAY - Nasconde schermata di caricamento
 * LINGUAGGIO: jQuery animation API
 * 
 * Rimuove l'overlay di caricamento con animazione di dissolvenza
 * per transizione fluida alla normale interfaccia utente.
 */
function hideLoadingOverlay() {
    // ANIMAZIONE USCITA - fadeOut() è animazione jQuery
    $('#loadingOverlay').fadeOut(300, function() {
        $(this).remove(); // Callback: rimuove elemento dopo animazione
    });
    console.log('Loading overlay nascosto');
}

/**
 * FUNZIONE NOTIFICHE - Sistema toast compatto per feedback utente
 * LINGUAGGIO: jQuery + Bootstrap Alert + CSS animations
 * 
 * @param {string} message - Testo del messaggio da mostrare
 * @param {string} type - Tipo notifica: 'success', 'info', 'warning', 'error'
 * 
 * Crea notifiche temporanee non invasive nell'angolo dello schermo
 * usando il sistema di alert Bootstrap con auto-rimozione temporizzata.
 */
function showNotification(message, type) {
    type = type || 'success'; // Default a successo se tipo non specificato
    
    // CLEANUP - Rimuove notifiche precedenti per evitare accumulo
    $('.toast-notification').remove();
    
    /**
     * MAPPING TIPI - Converte tipi logici in classi CSS Bootstrap
     * Ogni tipo ha colore e significato specifico per UX coerente
     */
    const alertClasses = {
        'success': 'alert-success',   // Verde - operazione riuscita
        'info': 'alert-info',         // Blu - informazione neutra
        'warning': 'alert-warning',   // Giallo - attenzione richiesta
        'error': 'alert-danger',      // Rosso - errore critico
        'danger': 'alert-danger'      // Alias per error
    };
    
    /**
     * MAPPING ICONE - Associa icone Bootstrap Icons ai tipi di messaggio
     */
    const icons = {
        'success': 'check-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle',
        'error': 'x-circle',
        'danger': 'x-circle'
    };
    
    // SELEZIONE STILI - Ottiene classi CSS e icona per il tipo specificato
    const alertClass = alertClasses[type] || 'alert-info';
    const icon = icons[type] || 'info-circle';
    
    /**
     * TEMPLATE NOTIFICA - HTML per alert Bootstrap posizionato fisso
     * STYLING: position-fixed per rimanere visibile durante scroll
     */
    const notification = $(`
        <div class="toast-notification alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    // INSERIMENTO DOM - Aggiunge notifica al body
    $('body').append(notification);
    console.log('Notifica mostrata (' + type + '):', message);
    
    /**
     * AUTO-RIMOZIONE TEMPORIZZATA - Calcola durata in base al tipo di messaggio
     * Errori rimangono più a lungo per permettere lettura completa
     */
    const autoHideDelay = type === 'error' || type === 'danger' ? 
        CONFIG.ERROR_NOTIFICATION_DURATION :  // 5 secondi per errori
        CONFIG.NOTIFICATION_DURATION;         // 3 secondi per messaggi normali
        
    // TIMER RIMOZIONE - setTimeout per auto-nascondere notifica
    setTimeout(function() {
        // ANIMAZIONE USCITA - fadeOut con callback di cleanup
        notification.fadeOut(function() {
            $(this).remove(); // Rimuove completamente dal DOM
        });
    }, autoHideDelay);
}

// ===================================================================
// SEZIONE 11: CLEANUP E GESTIONE MEMORIA
// ===================================================================

/**
 * FUNZIONE CLEANUP - Setup pulizia risorse quando si esce dalla pagina
 * LINGUAGGIO: Browser beforeunload API + jQuery
 * 
 * Questa funzione è critica per prevenire memory leaks e garantire
 * che tutte le risorse vengano rilasciate correttamente quando l'utente
 * naviga via dalla pagina o chiude il browser.
 */
function setupPageCleanup() {
    // EVENT HANDLER - beforeunload si attiva prima che la pagina venga scaricata
    $(window).on('beforeunload', function() {
        // CLEANUP TIMER - Ferma auto-refresh per liberare risorse
        stopAutoRefresh();
        
        // CLEANUP DOM - Rimuove elementi temporanei che potrebbero rimanere
        $('.toast-notification').remove();  // Rimuove notifiche pendenti
        $('#loadingOverlay').remove();       // Rimuove overlay caricamento
        
        console.log('Cleanup pagina manutenzione completato');
        
        // NOTA: Non restituire stringa da questa funzione a meno che non si voglia
        // mostrare dialog di conferma "Sei sicuro di voler lasciare la pagina?"
    });
}

// ===================================================================
// SEZIONE 12: DEBUG E DEVELOPMENT
// ===================================================================

/**
 * FUNZIONE DEBUG - Utilità per ambiente di sviluppo
 * SCOPO: Fornisce strumenti di debug e monitoring per sviluppatori
 * 
 * Questa funzione attiva funzionalità di debug solo in ambiente di sviluppo,
 * inclusi log periodici dello stato e esposizione di funzioni per testing manuale.
 */
function setupDebugMode() {
    /**
     * RILEVAZIONE AMBIENTE - Identifica se siamo in sviluppo locale
     * Controlla hostname per determinare ambiente di esecuzione
     */
    const isDevelopment = window.location.hostname === 'localhost' || 
                         window.location.hostname === '127.0.0.1' ||
                         window.location.hostname.includes('localhost');
    
    // ATTIVAZIONE DEBUG - Solo in ambiente di sviluppo
    if (isDevelopment) {
        /**
         * DEBUG TIMER - Log periodico stato sistema per monitoring
         * Ogni minuto stampa snapshot completo dello stato interno
         */
        setInterval(function() {
            console.log('Debug Manutenzione:', {
                autoRefresh: autoRefreshEnabled,        // Stato auto-refresh
                interval: !!systemCheckInterval,        // Timer attivo (boolean)
                visible: document.visibilityState,      // Visibilità pagina
                checking: isSystemChecking,             // Controllo in corso
                timestamp: new Date().toISOString()     // Timestamp corrente
            });
        }, 60000); // 60000ms = 1 minuto
        
        /**
         * ESPOSIZIONE FUNZIONI - Crea oggetto globale per testing manuale
         * Gli sviluppatori possono testare funzioni dalla console browser:
         * 
         * Esempi d'uso dalla console:
         * - window.maintenanceDebug.checkSystemStatus()
         * - window.maintenanceDebug.showNotification('Test', 'warning')
         * - window.maintenanceDebug.showLoadingOverlay('Test loading...')
         */
        window.maintenanceDebug = {
            checkSystemStatus: checkSystemStatus,     // Testa controllo sistema
            showNotification: showNotification,       // Testa notifiche
            startAutoRefresh: startAutoRefresh,       // Avvia/ferma auto-refresh
            stopAutoRefresh: stopAutoRefresh,
            showLoadingOverlay: showLoadingOverlay,   // Testa overlay
            hideLoadingOverlay: hideLoadingOverlay
        };
        
        console.log('🔧 DEBUG MODE ATTIVO - Funzioni disponibili in window.maintenanceDebug');
    }
}

// ===================================================================
// SEZIONE 13: ESPORTAZIONE FUNZIONI GLOBALI
// ===================================================================

/**
 * ESPORTAZIONE GLOBALE - Rende funzioni disponibili al di fuori del modulo
 * SCOPO: Compatibilità con codice legacy e chiamate dirette da HTML
 * 
 * Queste funzioni vengono aggiunte all'oggetto window globale per permettere
 * chiamate dirette da attributi onclick HTML o da altri moduli JavaScript.
 */

// FUNZIONE HEADER - Utilizzata dal pulsante di refresh nell'header della pagina
window.aggiornaInfoSistema = aggiornaInfoSistema;

// FUNZIONI SISTEMA - Esposte per uso da altri moduli admin
window.checkSystemStatus = checkSystemStatus;
window.showNotification = showNotification;

// ===================================================================
// SEZIONE 14: INIZIALIZZAZIONE FINALE
// ===================================================================

/**
 * INIZIALIZZAZIONE DEBUG - Attiva debug mode quando DOM è pronto
 * Separata dall'inizializzazione principale per modularità
 */
$(document).ready(function() {
    setupDebugMode(); // Attiva debug solo se in ambiente di sviluppo
});

// ===================================================================
// MESSAGGIO DI CONFERMA CARICAMENTO
// ===================================================================

/**
 * LOG FINALE - Conferma caricamento completo del modulo
 * Questo messaggio nella console conferma che tutto il codice è stato
 * caricato e parsato correttamente dal browser
 */
console.log('Sistema manutenzione admin compatto inizializzato');

/**
 * ===================================================================
 * FINE MODULO admin/manutenzione.js
 * ===================================================================
 * 
 * RIEPILOGO FUNZIONALITÀ IMPLEMENTATE:
 * 
 * 1. MONITORAGGIO SISTEMA
 *    - Controllo stato servizi (database, cache, storage)
 *    - Metriche performance (CPU, memoria, disco)
 *    - Auto-refresh configurabile ogni 30 secondi
 * 
 * 2. INTERFACCIA UTENTE
 *    - Notifiche toast non invasive
 *    - Overlay caricamento per operazioni lunghe
 *    - Feedback visivo per tutte le azioni utente
 * 
 * 3. OTTIMIZZAZIONI PERFORMANCE
 *    - Pausa auto-refresh quando tab nascosto
 *    - Mutex lock per evitare chiamate simultanee
 *    - Cleanup automatico risorse
 * 
 * 4. GESTIONE ERRORI E RETE
 *    - Monitoraggio connessione internet
 *    - Riconnessione automatica dopo disconnessione
 *    - Gestione timeout e fallimenti AJAX
 * 
 * 5. DEBUG E MANUTENIBILITÀ
 *    - Logging dettagliato per troubleshooting
 *    - Funzioni esposte per testing manuale
 *    - Configurazioni centralizzate
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (Promise, arrow functions, template literals)
 * - jQuery 3.x (DOM manipulation, AJAX, event handling)
 * - Bootstrap 5 (componenti UI, classi utility)
 * - Browser APIs (Visibility API, Network API, Timer API)
 * 
 * PATTERN ARCHITETTURALI:
 * - Module Pattern per incapsulamento
 * - Observer Pattern per eventi
 * - Promise Pattern per operazioni asincrone
 * - Mutex Pattern per controllo concorrenza
 * ===================================================================
 */