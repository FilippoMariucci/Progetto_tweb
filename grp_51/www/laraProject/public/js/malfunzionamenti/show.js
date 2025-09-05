

$(document).ready(function() {
    console.log('malfunzionamenti.show caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'malfunzionamenti.show') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('Pagina dettaglio malfunzionamento caricata');
    
    // === DEBUG INIZIALE ===
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('Pulsanti segnala trovati:', $('.segnala-btn').length);
    console.log('ID malfunzionamento:', $('.segnala-btn').data('malfunzionamento-id'));
    
    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    // Definisce la funzione globale chiamata dai bottoni onclick (STESSA IMPLEMENTAZIONE DI ricerca.blade.php)
    window.segnalaMalfunzionamento = function segnalaMalfunzionamento(malfunzionamentoId) {
    console.log('Funzione segnalaMalfunzionamento chiamata con ID:', malfunzionamentoId);
    
    if (!malfunzionamentoId) {
        alert('Errore: ID malfunzionamento non valido');
        return;
    }
    
    if (!confirm('Confermi di aver riscontrato questo problema?\n\nQuesta segnalazione aiuterà altri tecnici a identificare problemi frequenti.')) {
        return;
    }
    
    // Trova il bottone corretto usando l'onclick
    const button = $(`button[onclick="segnalaMalfunzionamento('${malfunzionamentoId}')"]`);
    
    if (button.length === 0) {
        console.error('Pulsante non trovato per ID:', malfunzionamentoId);
        alert('Errore: Pulsante non trovato');
        return;
    }
    
    const originalText = button.html();
    
    // Mostra stato di caricamento con spinner
    button.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Segnalando...')
          .prop('disabled', true)
          .removeClass('btn-outline-warning')
          .addClass('btn-warning');
    
    console.log('Invio richiesta AJAX per segnalazione...');
    
    // Chiamata AJAX per segnalare il malfunzionamento
    $.ajax({
        url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        timeout: 15000, // 15 secondi timeout
        success: function(response) {
            console.log('Risposta ricevuta:', response);
            
            if (response.success) {
                // Aggiorna il contatore delle segnalazioni nella sidebar
                const counterElement = $('#segnalazioni-counter');
                if (counterElement.length > 0) {
                    counterElement.html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                }
                
                // CAMBIAMENTO PRINCIPALE: Pulsante success molto più visibile
                button.removeClass('btn-warning btn-outline-warning')
                      .addClass('btn-success')
                      .css({
                          'background-color': '#198754',
                          'border-color': '#146c43',
                          'color': '#ffffff',
                          'font-weight': 'bold',
                          'box-shadow': '0 3px 12px rgba(25, 135, 84, 0.4)',
                          'transform': 'translateY(-1px)'
                      })
                      .html('<i class="bi bi-check-circle-fill me-2"></i><strong>Problema Segnalato!</strong>')
                      .prop('disabled', true)
                      .removeAttr('onclick'); // Rimuove l'onclick handler
                
                // Aggiungi animazione pulso per attirare l'attenzione
                button.addClass('pulse-success');
                
                // Rimuovi l'effetto pulso dopo 4 secondi
                setTimeout(() => {
                    button.removeClass('pulse-success');
                }, 4000);
                
                // Mostra messaggio di successo migliorato
                showAlert(`Segnalazione registrata con successo! Totale segnalazioni: ${response.nuovo_count}`, 'success');
                
                console.log('Segnalazione completata con successo');
                
            } else {
                throw new Error(response.message || 'Errore nella risposta del server');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore AJAX completo:', {
                xhr: xhr,
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            let msg = 'Errore nella segnalazione del malfunzionamento';
            
            // Gestione messaggi di errore specifici
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.status === 403) {
                msg = 'Non hai i permessi per effettuare questa segnalazione';
            } else if (xhr.status === 404) {
                msg = 'Malfunzionamento non trovato';
            } else if (xhr.status === 422) {
                msg = 'Dati non validi per la segnalazione';
            } else if (xhr.status === 500) {
                msg = 'Errore interno del server. Riprova più tardi.';
            } else if (status === 'timeout') {
                msg = 'Timeout della richiesta. Controlla la connessione.';
            } else if (xhr.status === 0) {
                msg = 'Errore di connessione. Verifica la tua connessione internet.';
            }
            
            showAlert(msg, 'danger');
            
            // Ripristina il pulsante allo stato originale
            button.removeClass('btn-warning')
                  .addClass('btn-outline-warning')
                  .css({
                      'background-color': '',
                      'border-color': '',
                      'color': '',
                      'font-weight': '',
                      'box-shadow': '',
                      'transform': ''
                  })
                  .html(originalText)
                  .prop('disabled', false);
        }
    });
};

// === FUNZIONE HELPER MIGLIORATA PER ALERT ===
function showAlert(message, type = 'info') {
    // Rimuovi alert esistenti
    $('.alert-floating').remove();
    
    const alertClass = `alert-${type}`;
    const iconClasses = {
        'success': 'check-circle-fill text-success',
        'danger': 'exclamation-triangle-fill text-danger',
        'warning': 'exclamation-triangle-fill text-warning',
        'info': 'info-circle-fill text-info'
    };
    
    const iconClass = iconClasses[type] || iconClasses.info;
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show alert-floating shadow-lg" 
             style="position: fixed; top: 20px; right: 20px; z-index: 1055; min-width: 350px; max-width: 500px; border: none; border-radius: 0.5rem;">
            <div class="d-flex align-items-start">
                <i class="bi bi-${iconClass} me-3 fs-4 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <div class="fw-bold mb-1">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    console.log(`Alert mostrato: ${type} - ${message}`);
    
    // Auto-rimuovi dopo un tempo basato sul tipo
    const autoRemoveDelay = type === 'success' ? 8000 : 6000;
    setTimeout(() => {
        $('.alert-floating').fadeOut(500, function() {
            $(this).remove();
        });
    }, autoRemoveDelay);
}
    
    // === FUNZIONE HELPER PER ALERT ===
    function showAlert(type, message) {
        // Rimuovi alert esistenti
        $('.alert-floating').remove();
        
        const alertClass = `alert-${type}`;
        const iconClass = {
            'success': 'check-circle-fill',
            'danger': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle-fill',
            'info': 'info-circle-fill'
        };
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-floating">
                <i class="bi bi-${iconClass[type] || 'info-circle-fill'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            $('.alert-floating').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // === SMOOTH SCROLLING PER ANCORE ===
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // === TOOLTIP INITIALIZATION ===
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    console.log('JavaScript dettaglio malfunzionamento inizializzato completamente');
});
