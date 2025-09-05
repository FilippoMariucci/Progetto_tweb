$(document).ready(function() {
    console.log('Admin assegnazioni index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.assegnazioni.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // === GESTIONE MODAL ASSEGNAZIONE SINGOLA ===
    
    /**
     * Gestisce il click sui pulsanti di assegnazione
     * Popola il modal con i dati del prodotto selezionato
     */
    $('.assign-btn').on('click', function() {
        // Estrae i dati del prodotto dal pulsante cliccato
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const currentStaff = $(this).data('current-staff');
        
        // Popola i campi del modal con i dati estratti
        $('#assign-product-id').val(productId);
        $('#assign-product-name').text(productName);
        $('#assign-staff-id').val(currentStaff || '');
        
        console.log('Modal assegnazione aperto per prodotto:', productName);
    });
    
    // === FILTRI DINAMICI ===
    
    /**
     * Auto-submit del form quando cambiano i filtri dropdown
     * Migliora l'esperienza utente evitando di dover cliccare "Applica"
     */
    $('#staff_id, #categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    /**
     * Gestisce il checkbox "Solo prodotti non assegnati"
     * Quando attivato, seleziona automaticamente "Non Assegnati" nel filtro staff
     */
    $('#non_assegnati').on('change', function() {
        if ($(this).is(':checked')) {
            // Se il checkbox è selezionato, filtra per prodotti non assegnati
            $('#staff_id').val('null');
        } else {
            // Se deselezionato, resetta il filtro staff
            $('#staff_id').val('');
        }
        // Applica automaticamente i filtri
        $(this).closest('form').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    /**
     * Validazione del form di assegnazione prima dell'invio
     */
    $('form[action*="assegna.prodotto"]').on('submit', function(e) {
        const staffId = $(this).find('select[name="staff_id"]').val();
        const productName = $('#assign-product-name').text();
        
        // Se viene selezionato uno staff, chiede conferma
        if (staffId) {
            const staffName = $(this).find('select[name="staff_id"] option:selected').text();
            const message = `Confermi l'assegnazione del prodotto "${productName}" a ${staffName}?`;
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        } else {
            // Se viene rimossa l'assegnazione, chiede conferma
            const message = `Confermi la rimozione dell'assegnazione per "${productName}"?`;
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // === FUNZIONI HELPER ===
    
    /**
     * Mostra alert temporanei per feedback all'utente
     * @param {string} type - Tipo di alert (success, warning, error, info)
     * @param {string} message - Messaggio da mostrare
     */
    function showAlert(type, message) {
        // Determina la classe CSS in base al tipo
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        // Determina l'icona in base al tipo
        const icon = type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        // Crea l'elemento alert
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Aggiunge l'alert al documento
        $('body').append(alert);
        
        // Rimuove automaticamente l'alert dopo 5 secondi
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    
    /**
     * Gestisce le scorciatoie da tastiera
     */
    $(document).on('keydown', function(e) {
        // Escape per chiudere il modal
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
        
        // F5 per ricaricare la pagina (comportamento standard del browser)
        // Ctrl+F per cercare (comportamento standard del browser)
    });
    
    // === INIZIALIZZAZIONE ===
    
    /**
     * Log di inizializzazione per debug
     */
    console.log('Gestione assegnazioni inizializzata');
    console.log(`Staff disponibili: ${$('#staff_id option').length - 2}`); // -2 per "Tutti" e "Non Assegnati"
    console.log(`Prodotti totali: {{ $prodotti->total() ?? 0 }}`);
    
    // Evidenzia il campo di ricerca se è stato utilizzato
    if ($('#search').val()) {
        $('#search').addClass('border-primary');
    }
});