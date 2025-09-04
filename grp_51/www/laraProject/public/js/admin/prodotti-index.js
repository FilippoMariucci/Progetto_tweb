
$(document).ready(function() {
    console.log('admin.prodotti.index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

    // === INIZIALIZZAZIONE ===
    // Configura token CSRF per le richieste AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Inizializza tooltip Bootstrap
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    console.log('🚀 Admin prodotti con stile catalogo inizializzato');
    
    // === GESTIONE FORM RICERCA (identica al catalogo) ===
    
    /**
     * Pulisci campo ricerca e rimetti focus
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
        console.log('🔍 Campo ricerca pulito');
    });
    
    /**
     * Submit automatico quando cambiano filtri
     */
    $('#status, #staff_id').on('change', function() {
        console.log('🔄 Filtro cambiato:', $(this).attr('id'), '=', $(this).val());
        $('#filterForm').submit();
    });
    
    /**
     * Ricerca con debounce per evitare troppe richieste
     * Attualmente disabilitata ma pronta per l'uso
     */
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                console.log(`🔍 Auto-ricerca potenziale: "${searchTerm}"`);
                // Decommentare per abilitare ricerca automatica:
                // $('#filterForm').submit();
            }
        }, 500);
    });
    
    /**
     * Shortcut tastiera per ricerca (Ctrl+K o Cmd+K)
     * Migliora l'accessibilità e la velocità d'uso
     */
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
            console.log('⌨️ Shortcut ricerca attivato');
        }
    });
    
    // === GESTIONE SELEZIONE PRODOTTI PER AZIONI BULK ===
    
    /**
     * Gestione checkbox singoli prodotti
     * Evidenzia visivamente le card selezionate
     */
    $(document).on('change', '.product-checkbox', function() {
        const isChecked = $(this).is(':checked');
        const productId = $(this).val();
        const card = $(this).closest('.product-card');
        
        // Evidenzia visivamente la card selezionata
        if (isChecked) {
            card.addClass('selected');
            console.log(`✅ Prodotto ${productId} selezionato`);
        } else {
            card.removeClass('selected');
            console.log(`❌ Prodotto ${productId} deselezionato`);
        }
        
        updateBulkActionsUI();
    });
    
    /**
     * Aggiorna interfaccia azioni bulk
     * Mostra/nasconde pulsante azioni multiple in base alle selezioni
     */
    function updateBulkActionsUI() {
        const selectedCount = $('.product-checkbox:checked').length;
        const hasSelection = selectedCount > 0;
        const bulkBtn = $('#bulkActionsBtn');
        
        if (hasSelection) {
            bulkBtn.removeClass('d-none').attr('title', `${selectedCount} prodotti selezionati`);
            console.log(`📊 Azioni bulk disponibili: ${selectedCount} prodotti`);
        } else {
            bulkBtn.addClass('d-none');
            console.log('📊 Azioni bulk nascoste: nessuna selezione');
        }
        
        // Aggiorna tooltip se Bootstrap è disponibile
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipInstance = bootstrap.Tooltip.getInstance(bulkBtn[0]);
            if (tooltipInstance) {
                tooltipInstance.dispose();
                new bootstrap.Tooltip(bulkBtn[0]);
            }
        }
    }
    
    // === AZIONI SUI PRODOTTI INDIVIDUALI ===
    
    /**
     * Conferma toggle stato prodotto (attivo/inattivo)
     * Mostra loading durante l'operazione
     */
    window.confirmToggleStatus = function(currentStatus) {
        const action = currentStatus ? 'disattivare' : 'attivare';
        const confirmed = confirm(`Sei sicuro di voler ${action} questo prodotto?`);
        
        if (confirmed) {
            console.log(`🔄 Toggle status confermato: ${action}`);
            showCardLoading();
        }
        
        return confirmed;
    };
    
    /**
     * Mostra loading su card durante operazioni
     * Fornisce feedback visivo immediato all'utente
     */
    function showCardLoading() {
        const loadingOverlay = $(`
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $('.product-card').append(loadingOverlay);
        
        // Rimuovi loading dopo 3 secondi se la pagina non si ricarica
        setTimeout(() => {
            loadingOverlay.remove();
        }, 3000);
    }
    
    // === AZIONI BULK SUI PRODOTTI ===
    
    /**
     * Seleziona tutti i prodotti visibili nella pagina corrente
     */
    window.selectAllProducts = function() {
        $('.product-checkbox').prop('checked', true).trigger('change');
        console.log('✅ Tutti i prodotti selezionati');
    };
    
    /**
     * Deseleziona tutti i prodotti
     */
    window.deselectAllProducts = function() {
        $('.product-checkbox').prop('checked', false).trigger('change');
        console.log('❌ Tutti i prodotti deselezionati');
    };
    
    /**
     * Attiva tutti i prodotti selezionati in bulk
     * Include conferma utente e gestione errori
     */
    window.bulkActivateProducts = function() {
        const selected = getSelectedProductIds();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto da attivare');
            return;
        }
        
        const message = selected.length === 1 ? 
            'Attivare il prodotto selezionato?' : 
            `Attivare ${selected.length} prodotti selezionati?`;
            
        if (confirm(message)) {
            console.log(`🟢 Attivazione bulk confermata: ${selected.length} prodotti`);
            executeBulkAction('activate', selected);
        }
    };
    
    /**
     * Disattiva tutti i prodotti selezionati in bulk
     */
    window.bulkDeactivateProducts = function() {
        const selected = getSelectedProductIds();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto da disattivare');
            return;
        }
        
        const message = selected.length === 1 ? 
            'Disattivare il prodotto selezionato?' : 
            `Disattivare ${selected.length} prodotti selezionati?`;
            
        if (confirm(message)) {
            console.log(`🟡 Disattivazione bulk confermata: ${selected.length} prodotti`);
            executeBulkAction('deactivate', selected);
        }
    };
    
    /**
     * Elimina tutti i prodotti selezionati in bulk
     * Include doppia conferma per operazioni irreversibili
     */
    window.bulkDeleteProducts = function() {
        const selected = getSelectedProductIds();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto da eliminare');
            return;
        }
        
        const message = selected.length === 1 ? 
            '🗑️ ATTENZIONE: Eliminare definitivamente il prodotto selezionato?\n\nQuesta azione non può essere annullata.' :
            `🗑️ ATTENZIONE: Eliminare definitivamente ${selected.length} prodotti selezionati?\n\nQuesta azione non può essere annullata.`;
            
        if (confirm(message)) {
            console.log(`🔴 Eliminazione bulk confermata: ${selected.length} prodotti`);
            executeBulkAction('delete', selected);
        }
    };
    
    /**
     * Ottiene array degli ID dei prodotti attualmente selezionati
     * @returns {number[]} Array di ID prodotti
     */
    function getSelectedProductIds() {
        return $('.product-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
    }
    
    /**
     * FUNZIONE CRITICA: Esegue azione bulk sui prodotti selezionati
     * Gestisce chiamata AJAX con error handling completo
     * 
     * @param {string} action - Tipo di azione (activate, deactivate, delete)
     * @param {number[]} productIds - Array di ID prodotti
     */
    function executeBulkAction(action, productIds) {
        if (!productIds || productIds.length === 0) {
            console.error('❌ Nessun prodotto per azione bulk');
            showToast('Errore: nessun prodotto selezionato', 'error');
            return;
        }
        
        console.log(`🚀 Esecuzione azione bulk: ${action} su ${productIds.length} prodotti`);
        
        // Mostra overlay di caricamento
        showLoadingOverlay(`Esecuzione ${action} su ${productIds.length} prodotti...`);
        
        // Disabilita interfaccia durante operazione
        $('#bulkActionsBtn').prop('disabled', true);
        
        // Chiamata AJAX con gestione errori completa
        $.ajax({
            url: '{{ route("admin.prodotti.bulk-action") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: action,
                products: productIds
            },
            timeout: 30000, // 30 secondi timeout
            success: function(response) {
                console.log('✅ Risposta bulk action:', response);
                hideLoadingOverlay();
                
                if (response.success) {
                    const message = response.message || `Azione ${action} completata con successo`;
                    showToast(message, 'success');
                    
                    // Ricarica la pagina dopo un breve delay per mostrare il messaggio
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    console.error('❌ Errore nella risposta:', response.message);
                    showToast('Errore: ' + (response.message || 'Operazione fallita'), 'error');
                    $('#bulkActionsBtn').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Errore AJAX bulk action:', { status, error, xhr });
                hideLoadingOverlay();
                $('#bulkActionsBtn').prop('disabled', false);
                
                // Gestione errori specifici
                let errorMessage = 'Errore di comunicazione con il server';
                
                if (xhr.status === 422) {
                    // Errore di validazione
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
    
    // === GESTIONE IMMAGINI E UX ===
    
    /**
     * Gestione errori caricamento immagini
     * Sostituisce immagini mancanti con placeholder
     */
    $('.product-image').on('error', function() {
        $(this).replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 160px;">
                <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
            </div>
        `);
    });
    
    /**
     * Loading per form submit
     * Fornisce feedback durante ricerca/filtri
     */
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.html('<i class="bi bi-hourglass-split me-1 loading-spinner"></i>Cercando...')
                  .prop('disabled', true);
        
        // Ripristina dopo timeout se la pagina non cambia
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, 5000);
    });
    
    // === EVIDENZIAZIONE RICERCA ===
    
    /**
     * Evidenzia termini di ricerca nei risultati
     * Migliora la visibilità dei risultati trovati
     */
    const searchTerm = '{{ request("search") }}';
    if (searchTerm && !searchTerm.includes('*') && searchTerm.length > 2) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
            const highlighted = text.replace(regex, '<mark class="bg-warning">$1</mark>');
            $(this).html(highlighted);
        });
        console.log(`🔍 Evidenziati termini di ricerca: "${searchTerm}"`);
    }
    
    /**
     * Escape caratteri speciali per regex sicura
     * Previene errori con caratteri speciali nella ricerca
     */
    function escapeRegex(text) {
        return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
    }
    
    // === UTILITY FUNCTIONS ===
    
    /**
     * Mostra overlay di caricamento fullscreen
     * @param {string} message - Messaggio da mostrare
     */
    function showLoadingOverlay(message = 'Caricamento...') {
        $('#loadingOverlay').remove();
        
        const overlay = $(`
            <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 9999;">
                <div class="card text-center p-4">
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
        console.log(`⏳ Loading overlay: ${message}`);
    }
    
    /**
     * Nasconde overlay di caricamento con animazione
     */
    function hideLoadingOverlay() {
        $('#loadingOverlay').fadeOut(300, function() {
            $(this).remove();
        });
        console.log('✅ Loading overlay nascosto');
    }
    
    /**
     * Mostra notifica toast con diversi tipi di messaggio
     * @param {string} message - Messaggio da mostrare
     * @param {string} type - Tipo: success, error, warning, info
     */
    function showToast(message, type = 'success') {
        // Rimuovi toast precedenti
        $('.toast-notification').remove();
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'check-circle-fill',
            'error': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle-fill', 
            'info': 'info-circle-fill'
        }[type] || 'info-circle-fill';
        
        const toast = $(`
            <div class="toast-notification alert ${alertClass} alert-dismissible fade show position-fixed shadow-lg" 
                 style="top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-${icon} me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        console.log(`📢 Toast mostrato (${type}): ${message}`);
        
        // Auto-rimuovi dopo timeout appropriato
        const autoHideDelay = type === 'error' ? 10000 : 5000;
        setTimeout(() => {
            toast.fadeOut(500, () => toast.remove());
        }, autoHideDelay);
    }
    
    // === GESTIONE RESPONSIVE ===
    
    /**
     * Adatta layout per schermi piccoli
     * Gestisce comportamento responsive dinamico
     */
    function handleResponsiveLayout() {
        const isSmallScreen = $(window).width() < 768;
        
        if (isSmallScreen) {
            // Su mobile, impila i filtri verticalmente
            $('.form-label.d-none.d-lg-block').removeClass('d-none d-lg-block');
            console.log('📱 Layout mobile attivato');
        } else {
            // Su desktop, layout orizzontale
            console.log('🖥️ Layout desktop attivato');
        }
    }
    
    // Esegui controllo responsive al caricamento e resize
    handleResponsiveLayout();
    $(window).on('resize', debounce(handleResponsiveLayout, 250));
    
    /**
     * Funzione debounce per ottimizzare eventi resize
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // === ANALYTICS E DEBUG ===
    
    /**
     * Log statistiche ricerca per analytics
     * Utile per monitorare comportamenti utente
     */
    @if(request('search') || request('status') || request('staff_id'))
        console.log('📊 Ricerca admin:', {
            termine: '{{ request("search") }}',
            status: '{{ request("status") }}',
            staff_id: '{{ request("staff_id") }}',
            risultati: {{ $prodotti->total() }},
            pagina: {{ $prodotti->currentPage() }}
        });
    @endif
    
    /**
     * Debug helper per development
     * Fornisce informazioni utili durante lo sviluppo
     */
    @if(app()->environment('local'))
        window.debugAdminProdotti = function() {
            console.log('🔍 Debug Admin Prodotti:', {
                prodotti_totali: {{ $prodotti->total() }},
                prodotti_in_pagina: {{ $prodotti->count() }},
                prodotti_selezionati: $('.product-checkbox:checked').length,
                filtri_attivi: {
                    search: '{{ request("search") }}' || null,
                    status: '{{ request("status") }}' || null,
                    staff_id: '{{ request("staff_id") }}' || null
                },
                csrf_token: $('meta[name="csrf-token"]').attr('content'),
                bulk_action_route: '{{ route("admin.prodotti.bulk-action") }}'
            });
        };
        
        // Debug automatico ogni minuto in development
        setInterval(window.debugAdminProdotti, 60000);
        
        // Esporta funzioni globali per testing
        window.testFunctions = {
            selectAllProducts,
            deselectAllProducts,
            getSelectedProductIds,
            showToast,
            updateBulkActionsUI
        };
    @endif
    
    // === GESTIONE EVENTI AVANZATA ===
    
    /**
     * Gestione tasti scorciatoia avanzati
     */
    $(document).on('keydown', function(e) {
        // ESC per deselezionare tutto
        if (e.key === 'Escape') {
            if ($('.product-checkbox:checked').length > 0) {
                deselectAllProducts();
                e.preventDefault();
            }
        }
        
        // Ctrl+A per selezionare tutto (solo se focus non è su input)
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !$(e.target).is('input, textarea')) {
            selectAllProducts();
            e.preventDefault();
        }
    });
    
    /**
     * Click fuori dalle card per deselezionare (opzionale)
     */
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.product-card, .btn').length && 
            $('.product-checkbox:checked').length > 0 && 
            e.target.type !== 'checkbox') {
            // Decommentare per abilitare deseleziona cliccando fuori
            // deselectAllProducts();
        }
    });
    
    // === FINALIZZAZIONE ===
    
    // Inizializza stato UI
    updateBulkActionsUI();
    
    // Log completamento inizializzazione
    console.log('🎉 Sistema admin prodotti con stile catalogo completamente inizializzato');
    
    // Performance monitoring in development
    if (typeof performance !== 'undefined') {
        console.log('⚡ Statistiche caricamento:', {
            prodotti_renderizzati: {{ $prodotti->count() }},
            tempo_dom_ready: Math.round(performance.now()) + 'ms',
            memoria_utilizzata: performance.memory ? 
                `${Math.round(performance.memory.usedJSHeapSize / 1024 / 1024)}MB` : 'N/A'
        });
    }
    
    // Pulizia memoria al cambio pagina
    $(window).on('beforeunload', function() {
        $('.toast-notification').remove();
        $('#loadingOverlay').remove();
        console.log('🧹 Pulizia risorse completata');
    });
});

// === FUNZIONI GLOBALI PER COMPATIBILITÀ ===

/**
 * Funzioni esposte globalmente per retrocompatibilità
 * e per l'uso da parte di altri script
 */
window.adminProdottiActions = {
    selectAll: function() { selectAllProducts(); },
    deselectAll: function() { deselectAllProducts(); },
    bulkActivate: function() { bulkActivateProducts(); },
    bulkDeactivate: function() { bulkDeactivateProducts(); },
    bulkDelete: function() { bulkDeleteProducts(); }
};