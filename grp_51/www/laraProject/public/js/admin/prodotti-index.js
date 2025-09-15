/**
 * ===================================================================
 * ADMIN PRODOTTI INDEX - JavaScript per Gestione Prodotti
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/admin/prodotti-index.js
 * 
 * FUNZIONALITÀ:
 * - Gestione ricerca e filtri prodotti
 * - Selezione multipla e azioni bulk
 * - Interfaccia responsive con feedback utente
 * - Gestione errori e stati di caricamento
 * ===================================================================
 */

// === VARIABILI GLOBALI ===
let selectedProducts = [];
let searchTimeout = null;
let isLoading = false;

// === CONFIGURAZIONI ===
const CONFIG = {
    SEARCH_DEBOUNCE_DELAY: 500, // ms per debounce ricerca
    LOADING_TIMEOUT: 5000, // timeout per operazioni
    TOAST_AUTO_HIDE_DELAY: 5000, // auto-hide per notifiche
    ERROR_TOAST_DELAY: 10000 // notifiche errore più lunghe
};

// === INIZIALIZZAZIONE PRINCIPALE ===
$(document).ready(function() {
    console.log('admin.prodotti.index caricato');
    
    // Verifica route corretta
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.index' && !window.location.href.includes('admin/prodotti')) {
        console.log('Route non corretta per admin prodotti');
        return;
    }
    
    // Inizializza sistema
    initializeAdminProducts();
    setupEventListeners();
    setupAjaxConfiguration();
    setupTooltips();
    setupResponsiveLayout();
    
    console.log('Admin prodotti con stile catalogo inizializzato');
});

// === INIZIALIZZAZIONE SISTEMA ===
function initializeAdminProducts() {
    console.log('Inizializzazione sistema admin prodotti...');
    
    // Inizializza stato UI
    updateBulkActionsUI();
    
    // Gestione errori immagini
    setupImageErrorHandling();
    
    // Evidenzia termini di ricerca se presenti
    highlightSearchTerms();
    
    // Setup keyboard shortcuts
    setupKeyboardShortcuts();
    
    console.log('Sistema admin prodotti inizializzato');
}

// === CONFIGURAZIONE AJAX ===
function setupAjaxConfiguration() {
    // Configura token CSRF per tutte le richieste AJAX
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        console.log('CSRF token configurato per AJAX');
    }
}

// === SETUP TOOLTIP ===
function setupTooltips() {
    // Inizializza tutti i tooltip Bootstrap se disponibili
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
        console.log('Tooltip Bootstrap inizializzati');
    }
}

// === EVENT LISTENERS ===
function setupEventListeners() {
    console.log('Setup event listeners...');
    
    // === GESTIONE FORM RICERCA ===
    
    // Pulisci campo ricerca
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
        console.log('Campo ricerca pulito');
    });
    
    // Submit automatico quando cambiano filtri
    $('#status, #staff_id').on('change', function() {
        console.log('Filtro cambiato:', $(this).attr('id'), '=', $(this).val());
        $('#filterForm').submit();
    });
    
    // Ricerca con debounce
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                console.log('Auto-ricerca potenziale:', searchTerm);
                // Uncomment per abilitare ricerca automatica:
                // $('#filterForm').submit();
            }
        }, CONFIG.SEARCH_DEBOUNCE_DELAY);
    });
    
    // === GESTIONE SELEZIONE PRODOTTI ===
    
    // Checkbox singoli prodotti
    $(document).on('change', '.product-checkbox', function() {
        const isChecked = $(this).is(':checked');
        const productId = $(this).val();
        const card = $(this).closest('.product-card');
        
        // Evidenzia visivamente la card selezionata
        if (isChecked) {
            card.addClass('selected');
            console.log('Prodotto selezionato:', productId);
        } else {
            card.removeClass('selected');
            console.log('Prodotto deselezionato:', productId);
        }
        
        updateBulkActionsUI();
    });
    
    // === GESTIONE FORM SUBMIT ===
    
    // Loading per form submit
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.html('<i class="bi bi-hourglass-split me-1 loading-spinner"></i>Cercando...')
                  .prop('disabled', true);
        
        // Ripristina dopo timeout
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, CONFIG.LOADING_TIMEOUT);
    });
}

// === KEYBOARD SHORTCUTS ===
function setupKeyboardShortcuts() {
    $(document).on('keydown', function(e) {
        // Ctrl+K o Cmd+K per focus ricerca
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
            console.log('Shortcut ricerca attivato');
        }
        
        // ESC per deselezionare tutto
        if (e.key === 'Escape') {
            if ($('.product-checkbox:checked').length > 0) {
                deselectAllProducts();
                e.preventDefault();
            }
        }
        
        // Ctrl+A per selezionare tutto (se non in input)
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !$(e.target).is('input, textarea')) {
            selectAllProducts();
            e.preventDefault();
        }
    });
}

// === GESTIONE SELEZIONE MULTIPLA ===

/**
 * Aggiorna interfaccia azioni bulk
 * Mostra/nasconde pulsanti in base alle selezioni
 */
function updateBulkActionsUI() {
    const selectedCount = $('.product-checkbox:checked').length;
    const hasSelection = selectedCount > 0;
    const bulkBtn = $('#bulkActionsBtn');
    
    if (hasSelection) {
        bulkBtn.removeClass('d-none').attr('title', selectedCount + ' prodotti selezionati');
        console.log('Azioni bulk disponibili:', selectedCount, 'prodotti');
    } else {
        bulkBtn.addClass('d-none');
        console.log('Azioni bulk nascoste: nessuna selezione');
    }
    
    // Aggiorna tooltip se disponibile
    updateTooltip(bulkBtn[0]);
}

/**
 * Seleziona tutti i prodotti visibili
 */
function selectAllProducts() {
    $('.product-checkbox').prop('checked', true).trigger('change');
    console.log('Tutti i prodotti selezionati');
}

/**
 * Deseleziona tutti i prodotti
 */
function deselectAllProducts() {
    $('.product-checkbox').prop('checked', false).trigger('change');
    console.log('Tutti i prodotti deselezionati');
}

/**
 * Ottiene array degli ID prodotti selezionati
 * @returns {number[]} Array di ID prodotti
 */
function getSelectedProductIds() {
    return $('.product-checkbox:checked').map(function() {
        return parseInt($(this).val());
    }).get();
}

// === AZIONI BULK ===

/**
 * Attiva prodotti selezionati in bulk
 */
function bulkActivateProducts() {
    const selected = getSelectedProductIds();
    
    if (selected.length === 0) {
        showToast('Seleziona almeno un prodotto da attivare', 'warning');
        return;
    }
    
    const message = selected.length === 1 ? 
        'Attivare il prodotto selezionato?' : 
        'Attivare ' + selected.length + ' prodotti selezionati?';
        
    if (confirm(message)) {
        console.log('Attivazione bulk confermata:', selected.length, 'prodotti');
        executeBulkAction('activate', selected);
    }
}

/**
 * Disattiva prodotti selezionati in bulk
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
 * Elimina prodotti selezionati in bulk
 */
function bulkDeleteProducts() {
    const selected = getSelectedProductIds();
    
    if (selected.length === 0) {
        showToast('Seleziona almeno un prodotto da eliminare', 'warning');
        return;
    }
    
    const message = selected.length === 1 ? 
        'ATTENZIONE: Eliminare definitivamente il prodotto selezionato?\n\nQuesta azione non può essere annullata.' :
        'ATTENZIONE: Eliminare definitivamente ' + selected.length + ' prodotti selezionati?\n\nQuesta azione non può essere annullata.';
        
    if (confirm(message)) {
        console.log('Eliminazione bulk confermata:', selected.length, 'prodotti');
        executeBulkAction('delete', selected);
    }
}

/**
 * Esegue azione bulk sui prodotti selezionati
 * @param {string} action - Tipo azione (activate, deactivate, delete)
 * @param {number[]} productIds - Array ID prodotti
 */
function executeBulkAction(action, productIds) {
    if (!productIds || productIds.length === 0) {
        console.error('Nessun prodotto per azione bulk');
        showToast('Errore: nessun prodotto selezionato', 'error');
        return;
    }
    
    console.log('Esecuzione azione bulk:', action, 'su', productIds.length, 'prodotti');
    
    // Mostra loading
    showLoadingOverlay('Esecuzione ' + action + ' su ' + productIds.length + ' prodotti...');
    
    // Disabilita interfaccia
    $('#bulkActionsBtn').prop('disabled', true);
    
    // Ottiene route dalla configurazione globale o costruisce URL
    let bulkActionUrl = window.LaravelRoutes?.['admin.prodotti.bulk-action'];
    if (!bulkActionUrl) {
        // Fallback: costruisce URL base
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
    
    // Chiamata AJAX
    $.ajax({
        url: bulkActionUrl,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action: action,
            products: productIds
        },
        timeout: 30000,
        success: function(response) {
            console.log('Risposta bulk action:', response);
            hideLoadingOverlay();
            
            if (response.success) {
                const message = response.message || 'Azione ' + action + ' completata con successo';
                showToast(message, 'success');
                
                // Ricarica pagina dopo delay
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                console.error('Errore nella risposta:', response.message);
                showToast('Errore: ' + (response.message || 'Operazione fallita'), 'error');
                $('#bulkActionsBtn').prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore AJAX bulk action:', { status: status, error: error, xhr: xhr });
            hideLoadingOverlay();
            $('#bulkActionsBtn').prop('disabled', false);
            
            // Gestione errori specifici
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

// === GESTIONE STATO SINGOLO PRODOTTO ===

/**
 * Conferma toggle stato prodotto
 * @param {boolean} currentStatus - Stato attuale del prodotto
 */
function confirmToggleStatus(currentStatus) {
    const action = currentStatus ? 'disattivare' : 'attivare';
    const confirmed = confirm('Sei sicuro di voler ' + action + ' questo prodotto?');
    
    if (confirmed) {
        console.log('Toggle status confermato:', action);
        showCardLoading();
    }
    
    return confirmed;
}

/**
 * Mostra loading su card durante operazioni
 */
function showCardLoading() {
    const loadingOverlay = $('<div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    
    $('.product-card').append(loadingOverlay);
    
    // Rimuovi loading dopo timeout
    setTimeout(function() {
        loadingOverlay.remove();
    }, 3000);
}

// === GESTIONE UI E UX ===

/**
 * Gestione errori caricamento immagini
 */
function setupImageErrorHandling() {
    $('.product-image').on('error', function() {
        $(this).replaceWith('<div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 160px;"><i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i></div>');
    });
}

/**
 * Evidenzia termini di ricerca nei risultati
 */
function highlightSearchTerms() {
    const searchTerm = getSearchTermFromUrl();
    if (searchTerm && !searchTerm.includes('*') && searchTerm.length > 2) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp('(' + escapeRegex(searchTerm) + ')', 'gi');
            const highlighted = text.replace(regex, '<mark class="bg-warning">$1</mark>');
            $(this).html(highlighted);
        });
        console.log('Evidenziati termini di ricerca:', searchTerm);
    }
}

/**
 * Ottiene termine di ricerca dall'URL
 */
function getSearchTermFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search') || '';
}

/**
 * Escape caratteri speciali per regex
 */
function escapeRegex(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
}

// === LOADING E NOTIFICHE ===

/**
 * Mostra overlay di caricamento fullscreen
 * @param {string} message - Messaggio da mostrare
 */
function showLoadingOverlay(message) {
    message = message || 'Caricamento...';
    $('#loadingOverlay').remove();
    
    const overlay = $('<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 9999;"><div class="card text-center p-4"><div class="card-body"><div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><h5 class="card-title">' + message + '</h5><p class="card-text text-muted">Attendere, operazione in corso...</p></div></div></div>');
    
    $('body').append(overlay);
    console.log('Loading overlay:', message);
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
 * Mostra notifica toast
 * @param {string} message - Messaggio
 * @param {string} type - Tipo: success, error, warning, info
 */
function showToast(message, type) {
    type = type || 'success';
    
    // Rimuovi toast precedenti
    $('.toast-notification').remove();
    
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
    
    const toast = $('<div class="toast-notification alert ' + alertClass + ' alert-dismissible fade show position-fixed shadow-lg" style="top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;"><div class="d-flex align-items-center"><i class="bi bi-' + icon + ' me-2 fs-5"></i><div class="flex-grow-1"><strong>' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong><br>' + message + '</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>');
    
    $('body').append(toast);
    console.log('Toast mostrato (' + type + '):', message);
    
    // Auto-rimuovi dopo timeout
    const autoHideDelay = type === 'error' ? CONFIG.ERROR_TOAST_DELAY : CONFIG.TOAST_AUTO_HIDE_DELAY;
    setTimeout(function() {
        toast.fadeOut(500, function() {
            $(this).remove();
        });
    }, autoHideDelay);
}

// === RESPONSIVE LAYOUT ===

/**
 * Setup layout responsive
 */
function setupResponsiveLayout() {
    handleResponsiveLayout();
    $(window).on('resize', debounce(handleResponsiveLayout, 250));
}

/**
 * Gestisce layout per diverse dimensioni schermo
 */
function handleResponsiveLayout() {
    const isSmallScreen = $(window).width() < 768;
    
    if (isSmallScreen) {
        // Su mobile, mostra labels dei filtri
        $('.form-label.d-none.d-lg-block').removeClass('d-none d-lg-block');
        console.log('Layout mobile attivato');
    } else {
        console.log('Layout desktop attivato');
    }
}

/**
 * Debounce function per ottimizzare eventi resize
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction() {
        const later = function() {
            clearTimeout(timeout);
            func.apply(null, arguments);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// === UTILITY FUNCTIONS ===

/**
 * Aggiorna tooltip Bootstrap
 */
function updateTooltip(element) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip && element) {
        const tooltipInstance = bootstrap.Tooltip.getInstance(element);
        if (tooltipInstance) {
            tooltipInstance.dispose();
            new bootstrap.Tooltip(element);
        }
    }
}

/**
 * Log statistiche per analytics
 */
function logSearchStats() {
    const urlParams = new URLSearchParams(window.location.search);
    const stats = {
        termine: urlParams.get('search') || null,
        status: urlParams.get('status') || null,
        staff_id: urlParams.get('staff_id') || null,
        risultati: $('.product-card').length,
        pagina: urlParams.get('page') || 1
    };
    
    if (stats.termine || stats.status || stats.staff_id) {
        console.log('Ricerca admin:', stats);
    }
}

// === ESPORTAZIONE FUNZIONI GLOBALI ===

// Espone funzioni globalmente per retrocompatibilità
window.selectAllProducts = selectAllProducts;
window.deselectAllProducts = deselectAllProducts;
window.bulkActivateProducts = bulkActivateProducts;
window.bulkDeactivateProducts = bulkDeactivateProducts;
window.bulkDeleteProducts = bulkDeleteProducts;
window.confirmToggleStatus = confirmToggleStatus;

// Oggetto per azioni bulk
window.adminProdottiActions = {
    selectAll: selectAllProducts,
    deselectAll: deselectAllProducts,
    bulkActivate: bulkActivateProducts,
    bulkDeactivate: bulkDeactivateProducts,
    bulkDelete: bulkDeleteProducts
};

// === CLEANUP E FINALIZZAZIONE ===

// Cleanup al cambio pagina
$(window).on('beforeunload', function() {
    $('.toast-notification').remove();
    $('#loadingOverlay').remove();
    console.log('Pulizia risorse completata');
});

// Log statistiche ricerca
$(document).ready(function() {
    setTimeout(logSearchStats, 1000);
});

console.log('Admin prodotti JavaScript caricato completamente');