$(document).ready(function() {
    console.log('Admin assegnazioni index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.assegnazioni.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
     let selectedProducts = [];
    
    // === GESTIONE SELEZIONE PRODOTTI ===
    
    // Select All checkbox
    $('#checkAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateSelectedProducts();
        updateBulkActions();
    });
    
    // Singola checkbox prodotto
    $('.product-checkbox').on('change', function() {
        updateSelectedProducts();
        updateBulkActions();
        updateSelectAllState();
    });
    
    // Aggiorna stato "Select All"
    function updateSelectAllState() {
        const total = $('.product-checkbox').length;
        const checked = $('.product-checkbox:checked').length;
        
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        $('#checkAll').prop('checked', checked === total && total > 0);
    }
    
    // Aggiorna lista prodotti selezionati
    function updateSelectedProducts() {
        selectedProducts = [];
        $('.product-checkbox:checked').each(function() {
            const productId = $(this).val();
            const productName = $(this).closest('tr').find('h6').text().trim();
            selectedProducts.push({
                id: productId,
                name: productName
            });
            
            // Evidenzia riga selezionata
            $(this).closest('tr').addClass('selected');
        });
        
        // Rimuovi evidenziazione da righe non selezionate
        $('.product-checkbox:not(:checked)').closest('tr').removeClass('selected');
        
        updateSelectedProductsDisplay();
    }
    
    // Aggiorna display prodotti selezionati nel modal
    function updateSelectedProductsDisplay() {
        const container = $('#selected-products');
        
        if (selectedProducts.length === 0) {
            container.html('<em class="text-muted">Nessun prodotto selezionato</em>');
        } else {
            let html = '';
            selectedProducts.forEach(function(product) {
                html += `
                    <span class="product-item">
                        ${product.name}
                        <input type="hidden" name="prodotti[]" value="${product.id}">
                        <button type="button" class="remove-btn" data-product-id="${product.id}">
                            <i class="bi bi-x"></i>
                        </button>
                    </span>
                `;
            });
            container.html(html);
        }
    }
    
    // Rimuovi prodotto dalla selezione nel modal
    $(document).on('click', '.remove-btn', function() {
        const productId = $(this).data('product-id');
        $(`.product-checkbox[value="${productId}"]`).prop('checked', false);
        updateSelectedProducts();
        updateBulkActions();
        updateSelectAllState();
    });
    
    // Aggiorna stato pulsanti azioni multiple
    function updateBulkActions() {
        const hasSelection = selectedProducts.length > 0;
        $('#bulkAssignBtn').prop('disabled', !hasSelection);
        $('#confirmBulkAssign').prop('disabled', !hasSelection);
        
        // Aggiorna contatore nel pulsante
        if (hasSelection) {
            $('#bulkAssignBtn').html(`<i class="bi bi-person-plus me-1"></i>Assegna Selezionati (${selectedProducts.length})`);
        } else {
            $('#bulkAssignBtn').html('<i class="bi bi-person-plus me-1"></i>Assegna Selezionati');
        }
    }
    
    // === GESTIONE MODAL ASSEGNAZIONE SINGOLA ===
    
    $('.assign-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const currentStaff = $(this).data('current-staff');
        
        $('#assign-product-id').val(productId);
        $('#assign-product-name').text(productName);
        $('#assign-staff-id').val(currentStaff || '');
    });
    
    // === GESTIONE MODAL ASSEGNAZIONE MULTIPLA ===
    
    $('#bulkAssignBtn').on('click', function() {
        if (selectedProducts.length === 0) {
            showAlert('warning', 'Seleziona almeno un prodotto per l\'assegnazione multipla');
            return;
        }
        $('#bulkAssignModal').modal('show');
    });
    
    // Abilita pulsante conferma quando staff selezionato
    $('#bulk-staff-id').on('change', function() {
        const hasStaff = $(this).val() !== '';
        const hasProducts = selectedProducts.length > 0;
        $('#confirmBulkAssign').prop('disabled', !(hasStaff && hasProducts));
    });
    
    // === SELECT ALL BUTTON ===
    
    $('#selectAllBtn').on('click', function() {
        const allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length;
        
        if (allChecked) {
            // Deseleziona tutti
            $('.product-checkbox').prop('checked', false);
            $('#checkAll').prop('checked', false);
            $(this).html('<i class="bi bi-check-all me-1"></i>Seleziona Tutti');
        } else {
            // Seleziona tutti
            $('.product-checkbox').prop('checked', true);
            $('#checkAll').prop('checked', true);
            $(this).html('<i class="bi bi-square me-1"></i>Deseleziona Tutti');
        }
        
        updateSelectedProducts();
        updateBulkActions();
    });
    
    // === FILTRI DINAMICI ===
    
    // Auto-submit filtri quando cambiano
    $('#staff_id, #categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Checkbox non_assegnati
    $('#non_assegnati').on('change', function() {
        if ($(this).is(':checked')) {
            $('#staff_id').val('null');
        } else {
            $('#staff_id').val('');
        }
        $(this).closest('form').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    $('#bulkAssignForm').on('submit', function(e) {
        if (selectedProducts.length === 0) {
            e.preventDefault();
            showAlert('error', 'Seleziona almeno un prodotto per l\'assegnazione');
            return false;
        }
        
        // Conferma azione
        const staffName = $('#bulk-staff-id option:selected').text();
        const message = `Confermi l'assegnazione di ${selectedProducts.length} prodotti a ${staffName}?`;
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
    
    // === FUNZIONI HELPER ===
    
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const icon = type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        // Ctrl+A per selezionare tutti
        if (e.ctrlKey && e.key === 'a' && !$(e.target).is('input, textarea')) {
            e.preventDefault();
            $('#selectAllBtn').click();
        }
        
        // Escape per chiudere modal
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
    });
    
    // === INIZIALIZZAZIONE ===
    updateBulkActions();
    updateSelectAllState();
    
    console.log('Gestione assegnazioni inizializzata');
    console.log(`Staff disponibili: ${$('#staff_id option').length - 1}`);
    console.log(`Prodotti totali: {{ $prodotti->total() }}`);
});