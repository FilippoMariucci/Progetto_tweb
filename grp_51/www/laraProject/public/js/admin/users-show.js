

$(document).ready(function() {
    console.log('admin.users.show caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.show') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

    console.log('Vista dettagli utente inizializzata - senza funzione sospendi');
    
    // === GESTIONE RESET PASSWORD ===
    $('form[action*="reset-password"]').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.text();
        const userName = '{{ $user->nome_completo }}';
        
        if (!confirm(`Resettare la password per ${userName}?\n\nVerrà generata una password temporanea che dovrà essere comunicata all'utente.`)) {
            return;
        }
        
        // Mostra loading
        button.prop('disabled', true)
              .html('<i class="bi bi-hourglass-split me-1"></i>Elaborazione...');
        
        // Invia richiesta AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Mostra alert con password temporanea
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999; min-width: 450px; max-width: 500px;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">Password Resetata con Successo</h6>
                                    <p class="mb-2">${response.message}</p>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong>Password Temporanea:</strong><br>
                                            <code class="bg-light p-2 rounded d-inline-block mt-1" style="font-size: 1.1em; letter-spacing: 1px;">${response.temp_password}</code>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" 
                                                onclick="navigator.clipboard.writeText('${response.temp_password}').then(() => { this.innerHTML='<i class=\\'bi bi-check\\' ></i> Copiato!'; setTimeout(() => { this.innerHTML='<i class=\\'bi bi-clipboard\\'></i> Copia'; }, 2000); })">
                                            <i class="bi bi-clipboard"></i> Copia
                                        </button>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Comunica questa password all'utente. Scadrà al primo login.
                                    </small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    `;
                    $('body').append(alertHtml);
                    
                    // Auto-rimuovi dopo 30 secondi per password sensibili
                    setTimeout(() => {
                        $('.alert').fadeOut(500, function() { $(this).remove(); });
                    }, 30000);
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore reset password:', error);
                showNotification('Errore durante il reset della password. Riprova.', 'danger');
            },
            complete: function() {
                // Ripristina pulsante
                button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // === GESTIONE ELIMINAZIONE UTENTE ===
    $('form[action*="destroy"] button[type="submit"]').on('click', function(e) {
        const form = $(this).closest('form');
        const userName = '{{ $user->nome_completo }}';
        
        // Prima conferma
        const firstConfirm = confirm(`ATTENZIONE: Stai per eliminare l'utente "${userName}".\n\nQuesta azione rimuoverà:\n- L'account utente\n- Tutti i dati associati\n- Le assegnazioni prodotti (se staff)\n- I collegamenti al centro assistenza (se tecnico)\n\nVuoi continuare?`);
        
        if (!firstConfirm) {
            e.preventDefault();
            return false;
        }
        
        // Seconda conferma per sicurezza
        const finalConfirm = confirm(`CONFERMA FINALE: Eliminare definitivamente "${userName}"?\n\nQuesta azione NON PUÒ essere annullata.`);
        
        if (!finalConfirm) {
            e.preventDefault();
            return false;
        }
        
        // Mostra loading se confermato
        const button = $(this);
        button.html('<i class="bi bi-hourglass-split me-1"></i>Eliminazione...')
              .prop('disabled', true);
              
        return true;
    });
    
    // === TOOLTIP ===
    $('[title]').tooltip();
    
    // === GESTIONE ERRORI IMMAGINI PRODOTTI ===
    $('img[alt]').on('error', function() {
        $(this).hide().next('.bg-light').removeClass('d-none').addClass('d-flex');
    });
    
    // === FUNZIONE HELPER PER NOTIFICHE ===
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
                <div class="d-flex align-items-center">
                    <i class="bi ${iconClass} me-2 fs-5"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `);
        
        $('body').append(alert);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => alert.fadeOut(300, () => alert.remove()), 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        // Ctrl+E per modificare utente (solo se permesso)
        if ((e.ctrlKey || e.metaKey) && e.key === 'e' && window.canEditUser) {
            e.preventDefault();
            window.location.href = window.editUserUrl;
        }
        // Ctrl+Backspace per tornare alla lista
        if ((e.ctrlKey || e.metaKey) && e.key === 'Backspace') {
            e.preventDefault();
            window.location.href = window.usersIndexUrl;
        }
    });
    
    // === LOG INFORMAZIONI UTENTE (per debug) ===
    // Per loggare dati utente, passarli dalla view Blade in una variabile JS globale, esempio:
    // <script>
    //   window.userData = @json($user);
    // </script>
    // Poi qui:
    // console.log('Dettagli utente caricati:', window.userData);
    
    console.log('Vista dettagli utente inizializzata - funzionalità sospendi account rimossa');
});