
$(document).ready(function() {
    console.log('admin.users.index caricato');

    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('Gestione Utenti inizializzata - modalità singola');
    
    // === FILTRI DINAMICI ===
    $('#livello_accesso, #centro_assistenza_id, #data_registrazione').on('change', function() {
        console.log('Applicazione filtro:', $(this).attr('name'), '=', $(this).val());
        $(this).closest('form').submit();
    });
    
    // === RICERCA DINAMICA ===
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        
        if (query.length >= 2 || query.length === 0) {
            searchTimeout = setTimeout(() => {
                console.log('Ricerca per:', query);
                $('#filterForm').submit();
            }, 800);
        }
    });
    
    // === GESTIONE AZIONI UTENTE ===
    
    // Reset password con AJAX
    $('form[action*="reset-password"]').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Resettare la password di questo utente?')) {
            return;
        }
        
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.text();
        
        button.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Elaborazione...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Mostra la password temporanea in un alert visibile
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999; min-width: 400px;">
                            <h6><i class="bi bi-check-circle me-2"></i>Password Resetata</h6>
                            <p>${response.message}</p>
                            <hr>
                            <p class="mb-0">
                                <strong>Password Temporanea:</strong> 
                                <code class="bg-light p-1 rounded">${response.temp_password}</code>
                                <button type="button" class="btn btn-outline-primary btn-sm ms-2" 
                                        onclick="navigator.clipboard.writeText('${response.temp_password}')">
                                    <i class="bi bi-clipboard"></i> Copia
                                </button>
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('body').append(alertHtml);
                    
                    setTimeout(() => $('.alert').alert('close'), 15000);
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function() {
                showNotification('Errore nel reset della password', 'danger');
            },
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Conferma eliminazione
    $('form[action*="destroy"] button[type="submit"]').on('click', function(e) {
        const form = $(this).closest('form');
        const userRow = $(this).closest('tr');
        const userName = userRow.find('h6').first().text().trim();
        
        const confirmed = confirm(`ATTENZIONE: Eliminare l'utente "${userName}"?\n\nQuesta azione non può essere annullata.`);
        
        if (confirmed) {
            userRow.addClass('table-warning');
            userRow.find('td').last().html('<i class="bi bi-hourglass-split"></i> Eliminazione...');
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = "{{ route('admin.users.create') }}";
        }
        
        if (e.key === 'Escape') {
            $('#search').val('').trigger('input');
        }
    });
    
    // === TOOLTIP ===
    $('[title]').tooltip();
    
    // === FUNZIONE HELPER PER NOTIFICHE ===
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        setTimeout(() => alert.alert('close'), 5000);
    }
    
    console.log('Gestione utenti inizializzata - versione senza selezione multipla');
    // Sostituisci il valore 0 con il totale reale degli utenti tramite variabile JS o altro metodo
    console.log('Utenti caricati:', window.PageData?.usersTotal ?? 0);
});
