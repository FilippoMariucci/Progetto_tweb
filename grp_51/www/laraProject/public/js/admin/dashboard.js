

$(document).ready(function() {
    console.log('admin.dashboard caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.dashboard') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    console.log('🔧 Dashboard Admin inizializzata');
    
    // Aggiornamento automatico delle statistiche ogni 5 minuti
    setInterval(function() {
        updateAdminStats();
    }, 300000); // 300000ms = 5 minuti
    
    // Controllo stato sistema ogni 3 minuti
    setInterval(function() {
        checkSystemStatus();
    }, 180000); // 180000ms = 3 minuti
    
    /**
     * Funzione per aggiornare le statistiche della dashboard admin
     * FIX PRINCIPALE: URL corretto per la chiamata AJAX
     */
    function updateAdminStats() {
        $.ajax({
            // *** FIX: URL CORRETTO per le route API admin definite ***
           url: '/~grp_51/laraProject/public/api/admin/stats-update', // Era: "admin/stats-update" (SBAGLIATO)
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Verifica che la risposta sia valida
                if (data.success && data.stats) {
                    console.log('📊 Statistiche aggiornate', data.stats);
                    
                    // Aggiorna i contatori nelle card statistiche
                    updateStatCards(data.stats);
                    
                    // Aggiorna la sezione prodotti non assegnati
                    updateProdottiNonAssegnati(data.stats);
                    
                    // Aggiorna il timestamp dell'ultimo aggiornamento
                    const now = new Date().toLocaleTimeString('it-IT');
                    $('#last-update-time').text(now);
                    
                    console.log('✅ Statistiche dashboard aggiornate con successo');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Errore aggiornamento statistiche:', error);
                
                // Solo mostra errore se non è 404 di sviluppo
                if (xhr.status !== 404) {
                    showUpdateError();
                }
            }
        });
    }
    
    /**
     * Controlla lo stato dei servizi di sistema
     * FIX PRINCIPALE: URL corretto per la chiamata AJAX
     */
    function checkSystemStatus() {
        $.ajax({
            // *** FIX: URL CORRETTO per le route API admin definite ***
            url: '/~grp_51/laraProject/public/api/admin/system-status', // Era: "/admin/system-status" (SBAGLIATO)
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    console.log('🟢 Sistema operativo:', data.status);
                    updateSystemStatus(data.components);
                }
            },
            error: function(xhr, status, error) {
                // Solo logga errori reali, non 404 di sviluppo
                if (xhr.status !== 404) {
                    console.warn('⚠️ Controllo stato sistema fallito:', error);
                }
            }
        });
    }
    
    /**
     * Aggiorna i contatori numerici nelle card delle statistiche
     * @param {Object} stats - Oggetto contenente le statistiche aggiornate
     */
    function updateStatCards(stats) {
        // Mappatura tra chiavi statistiche e selettori CSS
        const statMappings = {
            'total_utenti': '.text-danger h5',
            'total_prodotti': '.text-primary h5', 
            'total_centri': '.text-info h5',
            'total_soluzioni': '.text-success h5'
        };
        
        // Aggiorna ogni contatore se il dato è disponibile
        $.each(statMappings, function(statKey, selector) {
            if (stats[statKey] !== undefined) {
                $(selector).text(formatNumber(stats[statKey]));
            }
        });
    }
    
    /**
     * FUNZIONE PRINCIPALE - Aggiorna la sezione prodotti non assegnati
     * Questa è la funzione più importante per il controllo dei prodotti
     * @param {Object} stats - Statistiche dal server
     */
    function updateProdottiNonAssegnati(stats) {
        // Trova il container della sezione prodotti non assegnati
        const container = $('#prodotti-non-assegnati-container');
        
        if (!container.length) {
            console.warn('⚠️ Container prodotti non assegnati non trovato');
            return;
        }
        
        // Estrae i dati sui prodotti non assegnati
        const count = stats.prodotti_non_assegnati_count || 0;
        const prodotti = stats.prodotti_non_assegnati || [];
        
        console.log(`📦 Prodotti non assegnati: ${count}`, prodotti);
        
        if (count > 0) {
            // CI SONO PRODOTTI NON ASSEGNATI
            
            // Aggiorna il badge nel header se esiste
            const headerBadge = $('.card-header:has(.bi-exclamation-triangle) .badge');
            if (headerBadge.length) {
                headerBadge.text(count).removeClass('d-none');
            } else {
                // Crea il badge se non esiste
                $('.card-header:has(.bi-exclamation-triangle) h6').append(`
                    <span class="badge bg-danger ms-2">${count}</span>
                `);
            }
            
            // Costruisce l'HTML per mostrare la lista dei prodotti
            let html = `
                <div class="alert alert-warning py-2 mb-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>${count} prodotti</strong> senza staff
                </div>
            `;
            
            // Se ci sono prodotti specifici da mostrare
            if (prodotti.length > 0) {
                html += '<div class="list-group list-group-flush">';
                
                // Mostra massimo 3 prodotti
                prodotti.slice(0, 3).forEach(function(prodotto) {
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
                
                html += `
                    <div class="text-center mt-2">
                        <a href="/admin/assegnazioni" 
                           class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-gear me-1"></i>Gestisci Assegnazioni
                        </a>
                    </div>
                `;
            }
            
            // Sostituisce il contenuto del container
            container.html(html);
            
        } else {
            // NESSUN PRODOTTO NON ASSEGNATO - Tutti i prodotti sono assegnati
            
            // Rimuove il badge dal header se presente
            $('.card-header:has(.bi-exclamation-triangle) .badge').remove();
            
            // Mostra messaggio di successo
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
     * Aggiorna gli indicatori dello stato sistema nella dashboard
     * @param {Object} status - Stato dei vari servizi (database, storage, etc)
     */
    function updateSystemStatus(status) {
        // Aggiorna ogni servizio con il suo stato attuale
        $.each(status, function(component, state) {
            const badge = $(`.list-group-item:contains("${component}") .badge`);
            if (badge.length) {
                // Rimuove le classi di stato precedenti
                badge.removeClass('bg-success bg-warning bg-danger bg-info');
                
                // Applica la classe CSS appropriata in base allo stato
                switch(state) {
                    case 'online':
                    case 'writable':
                    case 'active':
                        badge.addClass('bg-success').text('Online');
                        break;
                    case 'read-only':
                        badge.addClass('bg-warning').text('Read-Only');
                        break;
                    case 'error':
                        badge.addClass('bg-danger').text('Errore');
                        break;
                    default:
                        badge.addClass('bg-info').text(state);
                }
            }
        });
    }
    
    /**
     * Mostra un indicatore di errore temporaneo quando l'aggiornamento fallisce
     */
    function showUpdateError() {
        // Crea un alert temporaneo di errore
        const indicator = $(`
            <div class="position-fixed top-0 end-0 m-3 alert alert-danger alert-dismissible" style="z-index: 9999;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Errore aggiornamento dati
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(indicator);
        
        // Rimuove automaticamente l'indicatore dopo 3 secondi
        setTimeout(function() {
            indicator.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Gestione del pulsante di aggiornamento manuale
     * Consente all'admin di aggiornare le statistiche on-demand
     */
    if ($('#manual-refresh-btn').length) {
        $('#manual-refresh-btn').on('click', function() {
            // Cambia il testo del pulsante durante l'aggiornamento
            $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiornamento...');
            $(this).prop('disabled', true);
            
            // Esegue l'aggiornamento via AJAX con URL corretto
            updateAdminStats();
            
            // Ripristina il pulsante dopo 2 secondi
            setTimeout(() => {
                $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiorna');
                $(this).prop('disabled', false);
            }, 2000);
        });
    }
    
    console.log('✅ Sistema di aggiornamento dashboard admin attivato');
});

/**
 * FUNZIONI HELPER GLOBALI
 */

/**
 * Formatta i numeri con separatori di migliaia in formato italiano
 * @param {number} num - Numero da formattare
 * @returns {string} - Numero formattato (es: 1.234)
 */
function formatNumber(num) {
    return new Intl.NumberFormat('it-IT').format(num);
}

/**
 * Mostra notificazioni toast Bootstrap personalizzate
 * @param {string} message - Messaggio da mostrare
 * @param {string} type - Tipo di notifica (success, warning, danger, info)
 */
function showNotification(message, type = 'success') {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" 
             role="alert" style="z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    
    // Inizializza e mostra il toast con Bootstrap
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Rimuove automaticamente il toast dopo 5 secondi
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

/**
 * Gestisce gli errori AJAX in modo centralizzato
 * @param {Object} xhr - Oggetto XMLHttpRequest dell'errore
 * @param {string} context - Contesto dell'errore per il logging
 */
function handleAjaxError(xhr, context) {
    console.error(`❌ Errore AJAX in ${context}:`, xhr.responseText);
    
    let message = 'Si è verificato un errore durante il caricamento dei dati.';
    
    // Personalizza il messaggio in base al codice di stato HTTP
    switch(xhr.status) {
        case 403:
            message = 'Non hai i permessi per accedere a questi dati.';
            break;
        case 404:
            message = 'Risorsa non trovata.';
            break;
        case 500:
            message = 'Errore interno del server.';
            break;
        case 0:
            message = 'Problemi di connessione. Controlla la tua connessione internet.';
            break;
    }
    
    showNotification(message, 'danger');
}

/**
 * Verifica la connettività usando un endpoint che esiste
 * AGGIORNATO: URL corretto per le API admin
 */
function checkConnectivity() {
    return $.ajax({
        url: '/~grp_51/laraProject/public/api/admin/system-status', // URL corretto
        method: 'GET',
        timeout: 5000
    }).done(function() {
        console.log('✅ Connettività OK');
        return true;
    }).fail(function(xhr) {
        // Solo avvisa se è un vero errore di connessione, non un 404
        if (xhr.status === 0) {
            console.warn('⚠️ Problemi di connettività rilevati');
            showNotification('Problemi di connessione rilevati', 'warning');
        }
        return false;
    });
}

// Event listener per gestire disconnessioni di rete
$(window).on('online', function() {
    showNotification('Connessione ripristinata', 'success');
    updateAdminStats(); // Aggiorna i dati quando la connessione torna
});

$(window).on('offline', function() {
    showNotification('Connessione persa. I dati potrebbero non essere aggiornati.', 'warning');
});

/**
 * Funzione di inizializzazione da chiamare quando la pagina è pronta
 * Configura tutti i listener e le impostazioni iniziali
 */
function initAdminDashboard() {
    // Configurazioni globali per AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr, status, error) {
            // Solo gestisce errori reali, non 404 di sviluppo
            if (xhr.status !== 404) {
                handleAjaxError(xhr, 'Request generico');
            }
        }
    });
    
    // Test connettività usando le route implementate
    checkConnectivity();
    
    console.log('🚀 Dashboard Admin completamente inizializzata');
    console.log('✅ Route API disponibili:');
    console.log('   - GET /api/admin/stats-update');
    console.log('   - GET /api/admin/system-status');
}

// Inizializza quando il documento è pronto
$(document).ready(initAdminDashboard);
