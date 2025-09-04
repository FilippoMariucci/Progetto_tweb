

$(document).ready(function() {
    console.log('admin.centri.index JS caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
     console.log('🏢 Inizializzazione gestione centri assistenza');
    
    // Inizializza tooltips e filtri
    initializeTooltips();
    setupDynamicFilters();
    setupSearchHandler();
    
    // Configura event handlers per eliminazione
    setupDeleteHandlers();
});

/**
 * Configura gli event handlers per l'eliminazione
 */
function setupDeleteHandlers() {
    // Event listener per i pulsanti elimina (se non usano onclick)
    $(document).on('click', '.btn-elimina-centro', function(e) {
        e.preventDefault();
        
        const centroId = $(this).data('centro-id');
        const centroNome = $(this).data('centro-nome');
        
        if (centroId && centroNome) {
            confirmDelete(centroId, centroNome);
        } else {
            console.error('❌ Dati centro mancanti per eliminazione');
        }
    });
    
    // Event listener per la conferma nel modal
    $('#delete-form').on('submit', function(e) {
        // Aggiunge un loading state al pulsante
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-hourglass-split me-1"></i>Eliminazione...');
        
        // Il form viene inviato normalmente
        console.log('🗑️ Invio form eliminazione centro');
        
        // Ripristina il pulsante dopo 3 secondi (fallback)
        setTimeout(() => {
            submitBtn.prop('disabled', false).html(originalText);
        }, 3000);
    });
}

/**
 * Funzione globale per confermare eliminazione centro
 * DEVE essere accessibile dal onclick nei template Blade
 */
window.confirmDelete = function(centroId, centroName) {
    console.log('🗑️ Richiesta eliminazione centro:', centroName, 'ID:', centroId);
    
    try {
        // Aggiorna contenuti modal
        const modalTitle = document.querySelector('#deleteModal .modal-title');
        const centroNameElement = document.getElementById('centro-name');
        const deleteForm = document.getElementById('delete-form');
        
        if (!centroNameElement || !deleteForm) {
            console.error('❌ Elementi modal non trovati');
            // Fallback: conferma diretta
            if (confirm(`Sei sicuro di voler eliminare il centro "${centroName}"?`)) {
                window.location.href = `/admin/centri/${centroId}/delete`;
            }
            return;
        }
        
        // Aggiorna i contenuti del modal
        centroNameElement.textContent = centroName;
        deleteForm.setAttribute('action', `/admin/centri/${centroId}`);
        
        // Mostra il modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
        
        console.log('✅ Modal eliminazione mostrato per centro:', centroName);
        
    } catch (error) {
        console.error('❌ Errore nell\'apertura del modal:', error);
        
        // Fallback: alert browser
        if (confirm(`Errore nel modal. Eliminare comunque il centro "${centroName}"?`)) {
            // Crea un form temporaneo per l'eliminazione
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/centri/${centroId}`;
            
            // Aggiunge CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);
            
            // Aggiunge method DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Aggiunge al DOM e invia
            document.body.appendChild(form);
            form.submit();
        }
    }
};

/**
 * Mostra notifica di successo/errore
 */
function showNotification(message, type = 'success') {
    const alertTypes = {
        'success': 'alert-success',
        'danger': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    
    const alert = $(`
        <div class="alert ${alertTypes[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; min-width: 300px;">
            <i class="bi bi-${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(alert);
    
    // Auto-rimuovi dopo 5 secondi
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}

// === ALTRE FUNZIONI DI SUPPORTO ===

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function setupDynamicFilters() {
    // Filtro provincia
    $('#provincia').on('change', function() {
        console.log('🌍 Provincia selezionata:', $(this).val());
        $(this).closest('form').submit();
    });
    
    // Auto-submit per città
    let cittaTimeout;
    $('#citta').on('input', function() {
        const citta = $(this).val().trim();
        clearTimeout(cittaTimeout);
        
        if (citta.length >= 2 || citta.length === 0) {
            cittaTimeout = setTimeout(() => {
                console.log('🏙️ Filtro città:', citta);
                $(this).closest('form').submit();
            }, 800);
        }
    });
}

function setupSearchHandler() {
    // Ricerca con debounce
    let searchTimeout;
    $('#search').on('input', function() {
        const searchTerm = $(this).val().trim();
        clearTimeout(searchTimeout);
        
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            searchTimeout = setTimeout(() => {
                console.log('🔍 Ricerca:', searchTerm);
                $(this).closest('form').submit();
            }, 600);
        }
    });
}

console.log('✅ Fix pulsante elimina caricato correttamente');