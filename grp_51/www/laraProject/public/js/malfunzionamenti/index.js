

$(document).ready(function() {
    console.log('malfunzionamenti.index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'malfunzionamenti.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('Pagina malfunzionamenti caricata');

    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    // Definisce la funzione globale chiamata dai bottoni onclick (STESSA IMPLEMENTAZIONE DI ricerca.blade.php)
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!malfunzionamentoId) {
            alert('Errore: ID malfunzionamento non valido');
            return;
        }
        
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return;
        }
        
        // Trova il bottone corretto usando l'onclick
        const button = $(`button[onclick="segnalaMalfunzionamento('${malfunzionamentoId}')"]`);
        const originalText = button.html();
        
        // Mostra stato di caricamento
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...').prop('disabled', true);
        
        // Chiamata AJAX per segnalare il malfunzionamento
        $.ajax({
            url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    // Aggiorna il contatore delle segnalazioni nella card
                    button.closest('.card-body')
                          .find('.bi-exclamation-triangle')
                          .parent()
                          .html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    // Cambia il pulsante per mostrare successo
                    button.removeClass('btn-outline-warning')
                          .addClass('btn-success')
                          .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                          .prop('disabled', true)
                          .removeAttr('onclick'); // Rimuove l'onclick handler
                    
                    // Mostra messaggio di successo
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                } else {
                    throw new Error(response.message || 'Errore nella risposta');
                }
            },
            error: function(xhr) {
                console.error('Errore AJAX:', xhr);
                let msg = 'Errore nella segnalazione del malfunzionamento';
                
                // Gestione messaggi di errore specifici
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    msg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    msg = 'Malfunzionamento non trovato';
                }
                
                showAlert(msg, 'danger');
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    // === DISABILITA AUTOCOMPLETE ===
    $('#search').attr({
        'autocomplete': 'off',
        'autocapitalize': 'off',
        'autocorrect': 'off',
        'spellcheck': 'false'
    });
    
    // === TOOLTIP ===
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === AUTO-SUBMIT FILTRI ===
    $('#gravita, #difficolta, #order').on('change', function() {
        $('#filter-form').submit();
    });
    
    // === RICERCA LIVE (DEBOUNCED) ===
    let searchTimeout;
    $('#search').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                $('#filter-form').submit();
            }, 500); // Aspetta 500ms dopo l'ultima digitazione
        }
    });
    
    // === FUNZIONE PER MOSTRARE ALERT ===
    function showAlert(message, type = 'info', duration = 5000) {
        $('.custom-alert').remove(); // Rimuove alert precedenti
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'x-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-rimozione dopo il tempo specificato
        setTimeout(() => {
            $('.custom-alert').fadeOut(() => $('.custom-alert').remove());
        }, duration);
    }
    
    console.log('JavaScript malfunzionamenti inizializzato');
});