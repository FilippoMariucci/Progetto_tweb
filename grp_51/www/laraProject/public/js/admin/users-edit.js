
$(document).ready(function() {
    console.log('admin.users.edit caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.edit') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

     // === GESTIONE CAMPI CONDIZIONALI ===
    
    // Mostra/nasconde campi tecnico in base al livello
    $('#livello_accesso').on('change', function() {
        const livello = $(this).val();
        const datiTecnico = $('#dati-tecnico');
        
        if (livello === '2') {
            datiTecnico.slideDown();
            // Rendi obbligatori i campi tecnico
            $('#data_nascita, #specializzazione, #centro_assistenza_id').prop('required', true);
        } else {
            datiTecnico.slideUp();
            // Rimuovi obbligatorietà
            $('#data_nascita, #specializzazione, #centro_assistenza_id').prop('required', false);
        }
    });
    
    // === ANTEPRIMA MODIFICHE ===
    $('#previewBtn').on('click', function() {
        generatePreview();
        $('#previewModal').modal('show');
    });
    
    function generatePreview() {
        // Dati originali
        // Assicurati che window.PageData.user sia valorizzato nel template Blade
        const original = {
            nome: window.PageData?.user?.nome || '',
            cognome: window.PageData?.user?.cognome || '',
            username: window.PageData?.user?.username || '',
            livello_accesso: window.PageData?.user?.livello_accesso || '',
            specializzazione: window.PageData?.user?.specializzazione || '',
            data_nascita: window.PageData?.user?.data_nascita || '',
            centro_assistenza_id: window.PageData?.user?.centro_assistenza_id || ''
        };
        
        // Dati correnti
        const current = {
            nome: $('#nome').val(),
            cognome: $('#cognome').val(),
            username: $('#username').val(),
            livello_accesso: $('#livello_accesso').val(),
            specializzazione: $('#specializzazione').val(),
            data_nascita: $('#data_nascita').val(),
            centro_assistenza_id: $('#centro_assistenza_id').val(),
            password: $('#password').val()
        };
        
        const livelloLabels = {
            '2': '🔵 Tecnico',
            '3': '🟡 Staff',
            '4': '🔴 Amministratore'
        };
        
        function highlightChange(originalValue, currentValue) {
            if (originalValue != currentValue) {
                return `<span class="highlight-change" title="Originale: ${originalValue}">${currentValue}</span>`;
            }
            return currentValue || '<em class="text-muted">Non inserito</em>';
        }
        
        let html = `
            <div class="preview-section">
                <div class="preview-title">Informazioni Account</div>
                <p><strong>Nome:</strong> ${highlightChange(original.nome, current.nome)}</p>
                <p><strong>Cognome:</strong> ${highlightChange(original.cognome, current.cognome)}</p>
                <p><strong>Username:</strong> ${highlightChange(original.username, current.username)}</p>
                <p><strong>Livello:</strong> ${highlightChange(livelloLabels[original.livello_accesso], livelloLabels[current.livello_accesso])}</p>
                ${current.password ? '<p><strong>Password:</strong> <span class="text-success">Nuova password impostata</span></p>' : ''}
            </div>
        `;
        
        if (current.livello_accesso === '2') {
            const centroNome = current.centro_assistenza_id ? 
                $('#centro_assistenza_id option:selected').text() : 'Nessuno';
            const centroOriginale = original.centro_assistenza_id ? 
                $(`#centro_assistenza_id option[value="${original.centro_assistenza_id}"]`).text() : 'Nessuno';
            
            html += `
                <div class="preview-section">
                    <div class="preview-title">Informazioni Tecnico</div>
                    <p><strong>Data Nascita:</strong> ${highlightChange(original.data_nascita, current.data_nascita)}</p>
                    <p><strong>Specializzazione:</strong> ${highlightChange(original.specializzazione, current.specializzazione)}</p>
                    <p><strong>Centro:</strong> ${highlightChange(centroOriginale, centroNome)}</p>
                </div>
            `;
        }
        
        // Conteggio modifiche
        let changesCount = 0;
        Object.keys(original).forEach(key => {
            if (original[key] != current[key] && current[key] !== '') {
                changesCount++;
            }
        });
        
        if (current.password) changesCount++;
        
        if (changesCount > 0) {
            html = `<div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>${changesCount} modifica${changesCount > 1 ? 'he' : ''} rilevata${changesCount > 1 ? 'e' : ''}.</strong>
            </div>` + html;
        } else {
            html = `<div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Nessuna modifica rilevata.
            </div>` + html;
        }
        
        $('#previewContent').html(html);
    }
    
    // Submit dal modal
    $('#updateFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#editUserForm').submit();
    });
    
    // === VALIDAZIONE CLIENT-SIDE ===
    $('#editUserForm').on('submit', function(e) {
        let isValid = true;
        
        // Campi obbligatori base
        const requiredFields = ['nome', 'cognome', 'username', 'livello_accesso'];
        
        requiredFields.forEach(function(field) {
            const element = $(`#${field}`);
            if (!element.val().trim()) {
                element.addClass('is-invalid');
                isValid = false;
            } else {
                element.removeClass('is-invalid');
            }
        });
        
        // Validazione password
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirmation').val();
        
        if (password && password !== passwordConfirm) {
            $('#password, #password_confirmation').addClass('is-invalid');
            isValid = false;
            showAlert('error', 'Le password non coincidono');
        } else {
            $('#password, #password_confirmation').removeClass('is-invalid');
        }
        
        // Validazione campi tecnico
        if ($('#livello_accesso').val() === '2') {
            const requiredTecnico = ['data_nascita', 'specializzazione', 'centro_assistenza_id'];
            requiredTecnico.forEach(function(field) {
                const element = $(`#${field}`);
                if (!element.val()) {
                    element.addClass('is-invalid');
                    isValid = false;
                } else {
                    element.removeClass('is-invalid');
                }
            });
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Scroll al primo errore
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        } else {
            // Disabilita pulsante per evitare doppi submit
            $('#updateBtn').prop('disabled', true).html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Salvando...');
        }
    });
    
    // === VALIDAZIONE REAL-TIME ===
    
    // Verifica username duplicato
    let usernameTimeout;
    $('#username').on('input', function() {
        clearTimeout(usernameTimeout);
        const username = $(this).val();
        const originalUsername = window.PageData?.user?.username || '';
        
        if (username && username !== originalUsername && username.length >= 3) {
            usernameTimeout = setTimeout(() => {
                checkUsernameAvailability(username);
            }, 500);
        }
    });
    
    function checkUsernameAvailability(username) {
        // Simulazione controllo username (da implementare con API)
        // Per ora solo validazione lato client
        if (username.length < 3) {
            $('#username').addClass('is-invalid');
            showAlert('warning', 'Username deve essere di almeno 3 caratteri');
        }
    }
    
    // Conferma password real-time
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirm = $(this).val();
        
        if (password && confirm) {
            if (password === confirm) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('#password').removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $('#password').removeClass('is-valid').addClass('is-invalid');
            }
        }
    });
    
    // === HELPER FUNCTIONS ===
    
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
        
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        // Ctrl+S per salvare
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('#editUserForm').submit();
        }
        
        // Ctrl+P per anteprima
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            $('#previewBtn').click();
        }
        
        // Esc per annullare/chiudere modal
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
    });
    
    // === CONFERME AZIONI PERICOLOSE ===
    $('form[action*="reset-password"] button').on('click', function(e) {
        if (!confirm('Resettare la password per {{ $user->nome_completo }}?\n\nVerrà generata una password temporanea.')) {
            e.preventDefault();
        }
    });
    
    $('form[action*="destroy"] button').on('click', function(e) {
        const confirmText = 'ELIMINA {{ strtoupper($user->username) }}';
        const userInput = prompt(`ATTENZIONE: Stai per eliminare definitivamente l'account di {{ $user->nome_completo }}.\n\nQuesta azione NON può essere annullata!\n\nPer confermare, scrivi esattamente: ${confirmText}`);
        
        if (userInput !== confirmText) {
            e.preventDefault();
            if (userInput !== null) {
                alert('Testo di conferma non corretto. Eliminazione annullata.');
            }
        }
    });
    
    // === SUGGERIMENTI AUTOMATICI ===
    
    // Suggerimenti per specializzazione tecnici
    const specializzazioni = [
        'Elettrodomestici',
        'Climatizzatori',
        'Lavatrici e Lavastoviglie',
        'Frigoriferi e Freezer',
        'Forni e Microonde',
        'Aspirapolvere',
        'Piccoli Elettrodomestici',
        'Caldaie e Scaldabagni',
        'Impianti Elettrici'
    ];
    
    $('#specializzazione').on('focus', function() {
        if (!$(this).val()) {
            $(this).attr('placeholder', 'es: ' + specializzazioni[Math.floor(Math.random() * specializzazioni.length)]);
        }
    });
    
    // === INIZIALIZZAZIONE ===
    
    // Trigger iniziale per mostrare/nascondere campi tecnico
    $('#livello_accesso').trigger('change');
    
    // Mostra info sui campi modificati
    $('input, select, textarea').on('change', function() {
        $(this).addClass('border-warning');
    });
    
    console.log('Form modifica utente inizializzato');
    console.log('Utente in modifica: {{ $user->nome_completo }} ({{ $user->username }})');
});