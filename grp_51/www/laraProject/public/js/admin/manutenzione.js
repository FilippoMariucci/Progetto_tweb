/**
 * ===================================================================
 * ADMIN MANUTENZIONE - JavaScript per Gestione Sistema
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/admin/manutenzione.js
 * 
 * FUNZIONALITÀ:
 * - Monitoraggio stato sistema in tempo reale
 * - Gestione cache e ottimizzazioni
 * - Auto-refresh configurabile
 * - Notifiche e feedback utente
 * ===================================================================
 */

// === VARIABILI GLOBALI ===
let systemCheckInterval = null;
let autoRefreshEnabled = true;
let isSystemChecking = false;

// === CONFIGURAZIONI ===
const CONFIG = {
    AUTO_REFRESH_INTERVAL: 30000, // 30 secondi
    SYSTEM_CHECK_TIMEOUT: 15000,  // 15 secondi timeout
    NOTIFICATION_DURATION: 3000,  // 3 secondi per notifiche
    ERROR_NOTIFICATION_DURATION: 5000 // 5 secondi per errori
};

// === INIZIALIZZAZIONE PRINCIPALE ===
$(document).ready(function() {
    console.log('Admin.manutenzione.index caricato');

    // Verifica route corretta
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.manutenzione.index' && !window.location.href.includes('manutenzione')) {
        console.log('Route non corretta per manutenzione');
        return;
    }

    console.log('Inizializzazione manutenzione admin (compatta)');
    
    // Inizializza sistema
    initializeMaintenance();
    setupEventListeners();
    setupNetworkMonitoring();
    
    // Controllo iniziale stato sistema
    setTimeout(checkSystemStatus, 500);
    
    // Avvia auto-refresh
    startAutoRefresh();
});

// === INIZIALIZZAZIONE SISTEMA ===
function initializeMaintenance() {
    console.log('Inizializzazione sistema manutenzione...');
    
    // Setup visibility change handling
    setupVisibilityChangeHandling();
    
    // Setup cleanup on page unload
    setupPageCleanup();
    
    console.log('Sistema manutenzione inizializzato');
}

// === EVENT LISTENERS ===
function setupEventListeners() {
    console.log('Setup event listeners manutenzione...');
    
    // Pulsante controllo manuale sistema
    $('#manual-check').on('click', function(e) {
        e.preventDefault();
        checkSystemStatus();
    });
    
    // Toggle auto-refresh con feedback visivo
    $('#auto-refresh').on('change', function() {
        autoRefreshEnabled = $(this).is(':checked');
        
        if (autoRefreshEnabled) {
            startAutoRefresh();
            showNotification('Auto-refresh attivato', 'success');
        } else {
            stopAutoRefresh();
            showNotification('Auto-refresh disattivato', 'info');
        }
    });
    
    // Gestione submit form con loading
    $('form').on('submit', function(e) {
        const btn = $(this).find('button[type="submit"]');
        const actionText = btn.text().trim();
        
        // Conferme per azioni critiche
        if (actionText.includes('Pulisci') || actionText.includes('Ottimizza')) {
            const confirmMessage = 'Procedere con ' + actionText.toLowerCase() + '?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        }
        
        // Mostra loading durante l'invio
        if (!btn.hasClass('btn-sm')) {
            btn.prop('disabled', true);
            btn.html('<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i>Elaborazione...');
        }
        
        // Mostra loading overlay per azioni importanti
        if (actionText.includes('Ottimizza')) {
            showLoadingOverlay('Ottimizzazione database in corso...');
        } else if (actionText.includes('Pulisci Tutto')) {
            showLoadingOverlay('Pulizia cache in corso...');
        }
    });
}

// === MONITORAGGIO SISTEMA ===

/**
 * Controlla lo stato del sistema via AJAX simulato
 * In produzione sostituire con endpoint reale
 */
function checkSystemStatus() {
    if (isSystemChecking) {
        console.log('Controllo sistema già in corso...');
        return;
    }
    
    isSystemChecking = true;
    const statusContainer = $('#system-status');
    const button = $('#manual-check');
    
    // Mostra stato loading compatto
    statusContainer.html(`
        <div class="d-flex justify-content-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <small>Controllo...</small>
        </div>
    `);
    
    button.prop('disabled', true);
    
    // Simula chiamata AJAX (sostituire con endpoint reale in produzione)
    const mockSystemCheck = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simula risposta sistema con dati realistici
                const mockResponse = {
                    success: true,
                    status: Math.random() > 0.1 ? 'operational' : 'degraded',
                    components: {
                        database: Math.random() > 0.05 ? 'online' : 'slow',
                        cache: Math.random() > 0.1 ? 'active' : 'partial',
                        storage: Math.random() > 0.02 ? 'writable' : 'readonly',
                        logs: 'active'
                    },
                    server_info: {
                        memory_usage: (Math.random() * 50 + 20).toFixed(1) + 'MB',
                        cpu_usage: (Math.random() * 30 + 10).toFixed(1) + '%',
                        disk_usage: (Math.random() * 20 + 60).toFixed(1) + '%'
                    },
                    timestamp: new Date().toISOString()
                };
                resolve(mockResponse);
            }, Math.random() * 2000 + 500); // 0.5-2.5 secondi di latenza simulata
        });
    };
    
    mockSystemCheck()
        .then(response => {
            displaySystemStatus(response);
            updateSystemMetrics(response.server_info);
            console.log('Controllo sistema completato:', response);
        })
        .catch(error => {
            console.error('Errore controllo sistema:', error);
            displaySystemError('Errore nel controllo sistema: ' + error.message);
        })
        .finally(() => {
            button.prop('disabled', false);
            isSystemChecking = false;
        });
}

/**
 * Mostra lo stato del sistema in formato compatto
 */
function displaySystemStatus(response) {
    const statusContainer = $('#system-status');
    const status = response.status;
    
    let statusClass, statusIcon, statusText;
    
    // Determina classe e icona in base allo stato
    switch(status) {
        case 'operational':
            statusClass = 'success';
            statusIcon = 'check-circle-fill';
            statusText = 'Operativo';
            break;
        case 'degraded':
            statusClass = 'warning';
            statusIcon = 'exclamation-triangle-fill';
            statusText = 'Degradato';
            break;
        case 'error':
            statusClass = 'danger';
            statusIcon = 'x-circle-fill';
            statusText = 'Errori';
            break;
        default:
            statusClass = 'secondary';
            statusIcon = 'question-circle-fill';
            statusText = 'Sconosciuto';
    }
    
    // HTML compatto per lo stato
    let html = `
        <div class="text-center mb-2">
            <i class="bi bi-${statusIcon} text-${statusClass} fs-4"></i>
            <div class="fw-semibold text-${statusClass}">${statusText}</div>
            <small class="text-muted">${new Date().toLocaleTimeString('it-IT')}</small>
        </div>
    `;
    
    // Aggiunge dettagli componenti se disponibili
    if (response.components) {
        html += '<div class="row g-1">';
        
        Object.entries(response.components).forEach(([component, state]) => {
            let componentClass = (state === 'online' || state === 'active' || state === 'writable') ? 'success' : 'warning';
            let componentIcon = componentClass === 'success' ? 'check' : 'exclamation-triangle';
            
            html += `
                <div class="col-6">
                    <div class="d-flex align-items-center justify-content-center p-1">
                        <i class="bi bi-${componentIcon} text-${componentClass} me-1"></i>
                        <small class="fw-semibold">${component}</small>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
    }
    
    statusContainer.html(html);
}

/**
 * Mostra errore nel controllo sistema
 */
function displaySystemError(message) {
    const statusContainer = $('#system-status');
    
    statusContainer.html(`
        <div class="text-center">
            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
            <div class="fw-semibold text-danger">Errore</div>
            <small class="text-muted">${message}</small>
        </div>
    `);
}

/**
 * Aggiorna metriche sistema nell'header
 */
function updateSystemMetrics(serverInfo) {
    if (!serverInfo) return;
    
    // Aggiorna memoria se disponibile
    if (serverInfo.memory_usage) {
        const memoryDisplay = $('#memory-display');
        if (memoryDisplay.length) {
            memoryDisplay.text(serverInfo.memory_usage);
        }
    }
    
    // Potresti aggiungere altri indicatori qui
    console.log('Metriche sistema aggiornate:', serverInfo);
}

// === AUTO-REFRESH ===

/**
 * Avvia controlli automatici del sistema
 */
function startAutoRefresh() {
    // Ferma interval esistente
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
    }
    
    if (autoRefreshEnabled) {
        systemCheckInterval = setInterval(function() {
            // Controlla solo se la pagina è visibile e auto-refresh è ancora abilitato
            if (autoRefreshEnabled && document.visibilityState === 'visible') {
                checkSystemStatus();
            }
        }, CONFIG.AUTO_REFRESH_INTERVAL);
        
        console.log('Auto-refresh avviato (ogni', CONFIG.AUTO_REFRESH_INTERVAL / 1000, 'secondi)');
    }
}

/**
 * Ferma controlli automatici
 */
function stopAutoRefresh() {
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
        systemCheckInterval = null;
        console.log('Auto-refresh fermato');
    }
}

// === GESTIONE VISIBILITÀ PAGINA ===

/**
 * Gestisce cambio visibilità pagina per ottimizzare performance
 */
function setupVisibilityChangeHandling() {
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            // Pausa auto-refresh quando pagina non visibile
            if (autoRefreshEnabled) {
                console.log('Auto-refresh in pausa (tab nascosto)');
            }
        } else {
            // Riprendi e fai controllo immediato
            if (autoRefreshEnabled) {
                console.log('Auto-refresh ripreso');
                setTimeout(checkSystemStatus, 1000);
            }
        }
    });
}

// === MONITORAGGIO RETE ===

/**
 * Setup monitoraggio connessione di rete
 */
function setupNetworkMonitoring() {
    $(window).on('online', function() {
        showNotification('Connessione ripristinata', 'success');
        if (autoRefreshEnabled) {
            setTimeout(checkSystemStatus, 1000);
        }
    });

    $(window).on('offline', function() {
        showNotification('Connessione persa', 'warning');
    });
}

// === UTILITIES E UI ===

/**
 * Funzione per aggiornare info sistema (chiamata dal pulsante header)
 */
function aggiornaInfoSistema() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    
    // Mostra loading
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    // Ricarica pagina dopo delay
    setTimeout(function() {
        location.reload();
    }, 1000);
}

/**
 * Mostra overlay di caricamento per operazioni lunghe
 */
function showLoadingOverlay(message) {
    message = message || 'Caricamento...';
    
    // Rimuove overlay esistente
    $('#loadingOverlay').remove();
    
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
    
    $('body').append(overlay);
    console.log('Loading overlay mostrato:', message);
}

/**
 * Nasconde overlay di caricamento
 */
function hideLoadingOverlay() {
    $('#loadingOverlay').fadeOut(300, function() {
        $(this).remove();
    });
    console.log('Loading overlay nascosto');
}

/**
 * Sistema di notifiche toast compatto
 */
function showNotification(message, type) {
    type = type || 'success';
    
    // Rimuove notifiche precedenti
    $('.toast-notification').remove();
    
    const alertClasses = {
        'success': 'alert-success',
        'info': 'alert-info',
        'warning': 'alert-warning',
        'error': 'alert-danger',
        'danger': 'alert-danger'
    };
    
    const icons = {
        'success': 'check-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle',
        'error': 'x-circle',
        'danger': 'x-circle'
    };
    
    const alertClass = alertClasses[type] || 'alert-info';
    const icon = icons[type] || 'info-circle';
    
    // Crea notifica compatta
    const notification = $(`
        <div class="toast-notification alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    $('body').append(notification);
    console.log('Notifica mostrata (' + type + '):', message);
    
    // Auto-rimuovi dopo timeout
    const autoHideDelay = type === 'error' || type === 'danger' ? 
        CONFIG.ERROR_NOTIFICATION_DURATION : 
        CONFIG.NOTIFICATION_DURATION;
        
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, autoHideDelay);
}

// === CLEANUP E GESTIONE MEMORIA ===

/**
 * Setup cleanup quando si esce dalla pagina
 */
function setupPageCleanup() {
    $(window).on('beforeunload', function() {
        stopAutoRefresh();
        $('.toast-notification').remove();
        $('#loadingOverlay').remove();
        console.log('Cleanup pagina manutenzione completato');
    });
}

// === DEBUG E DEVELOPMENT ===

/**
 * Debug per ambiente di sviluppo
 */
function setupDebugMode() {
    // Verifica se siamo in ambiente di sviluppo
    const isDevelopment = window.location.hostname === 'localhost' || 
                         window.location.hostname === '127.0.0.1' ||
                         window.location.hostname.includes('localhost');
    
    if (isDevelopment) {
        // Debug ogni minuto in sviluppo
        setInterval(function() {
            console.log('Debug Manutenzione:', {
                autoRefresh: autoRefreshEnabled,
                interval: !!systemCheckInterval,
                visible: document.visibilityState,
                checking: isSystemChecking,
                timestamp: new Date().toISOString()
            });
        }, 60000);
        
        // Esponi funzioni per testing
        window.maintenanceDebug = {
            checkSystemStatus: checkSystemStatus,
            showNotification: showNotification,
            startAutoRefresh: startAutoRefresh,
            stopAutoRefresh: stopAutoRefresh,
            showLoadingOverlay: showLoadingOverlay,
            hideLoadingOverlay: hideLoadingOverlay
        };
    }
}

// === ESPORTAZIONE FUNZIONI GLOBALI ===

// Espone funzioni globalmente per retrocompatibilità
window.aggiornaInfoSistema = aggiornaInfoSistema;
window.checkSystemStatus = checkSystemStatus;
window.showNotification = showNotification;

// === INIZIALIZZAZIONE DEBUG ===
$(document).ready(function() {
    setupDebugMode();
});

console.log('Sistema manutenzione admin compatto inizializzato');