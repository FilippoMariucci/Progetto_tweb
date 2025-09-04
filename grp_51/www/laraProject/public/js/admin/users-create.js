

$(document).ready(function() {
    console.log('admin.users.create caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.create') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
     console.log('🚀 Form creazione utente caricato');
    
    var formSubmitted = false;
    
    // === GESTIONE CAMPI CONDIZIONALI ===
    
    $('#livello_accesso').on('change', function() {
        var livello = $(this).val();
        var datiTecnico = $('#dati-tecnico');
        
        if (livello === '2') {
            // Mostra campi tecnico
            datiTecnico.slideDown();
            
            // Solo data_nascita e specializzazione obbligatori
            $('#data_nascita, #specializzazione').attr('required', true);
            
            // Centro SEMPRE opzionale
            $('#centro_assistenza_id').attr('required', false);
            
            console.log('✅ Campi tecnico mostrati');
            
        } else {
            // Nascondi campi
            datiTecnico.slideUp();
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', false);
            
            console.log('📴 Campi tecnico nascosti');
        }
    });
    
    // === GESTIONE PASSWORD ===
    
    // Toggle visibilità password
    $('#togglePassword').on('click', function() {
        var passwordField = $('#password');
        var icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // Generatore password
    $('#generatePassword').on('click', function() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        var password = '';
        
        for (var i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        $('#password, #password_confirmation').val(password);
        
        // Mostra temporaneamente
        $('#password').attr('type', 'text');
        $('#togglePassword').find('i').removeClass('bi-eye').addClass('bi-eye-slash');
        
        // Feedback
        var btn = $(this);
        btn.removeClass('btn-outline-info').addClass('btn-success').text('Generata!');
        
        setTimeout(function() {
            btn.removeClass('btn-success').addClass('btn-outline-info');
            btn.html('<i class="bi bi-magic me-1"></i>Genera Password Sicura');
        }, 1500);
    });
    
    // Verifica corrispondenza password
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();
        var confirmation = $(this).val();
        
        if (confirmation && password !== confirmation) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // === AUTO-COMPLETE USERNAME ===
    
    $('#nome, #cognome').on('input', function() {
        var nome = $('#nome').val().toLowerCase().trim();
        var cognome = $('#cognome').val().toLowerCase().trim();
        
        if (nome && cognome && !$('#username').val()) {
            $('#username').val(nome + '.' + cognome);
        }
    });
    
    // === VALIDAZIONE FORM ===
    
    $('#createUserForm').on('submit', function(e) {
        
        // Evita doppi submit
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }
        
        console.log('📤 Invio form...');
        
        // Rimuovi messaggi di errore precedenti
        $('.alert-danger').remove();
        
        // Controlli minimi
        var hasErrors = false;
        var errors = [];
        
        // Username
        if (!$('#username').val().trim()) {
            hasErrors = true;
            errors.push('Username obbligatorio');
        }
        
        // Password
        var password = $('#password').val();
        var passwordConfirm = $('#password_confirmation').val();
        
        if (!password) {
            hasErrors = true;
            errors.push('Password obbligatoria');
        } else if (password.length < 8) {
            hasErrors = true;
            errors.push('Password troppo corta (minimo 8 caratteri)');
        } else if (password !== passwordConfirm) {
            hasErrors = true;
            errors.push('Le password non coincidono');
        }
        
        // Nome e cognome
        if (!$('#nome').val().trim()) {
            hasErrors = true;
            errors.push('Nome obbligatorio');
        }
        
        if (!$('#cognome').val().trim()) {
            hasErrors = true;
            errors.push('Cognome obbligatorio');
        }
        
        // Livello
        if (!$('#livello_accesso').val()) {
            hasErrors = true;
            errors.push('Seleziona livello di accesso');
        }
        
        // Per tecnici
        if ($('#livello_accesso').val() === '2') {
            if (!$('#data_nascita').val()) {
                hasErrors = true;
                errors.push('Data nascita obbligatoria per tecnici');
            }
            if (!$('#specializzazione').val().trim()) {
                hasErrors = true;
                errors.push('Specializzazione obbligatoria per tecnici');
            }
            // Centro NON è obbligatorio
        }
        
        // Se ci sono errori
        if (hasErrors) {
            e.preventDefault();
            
            // Crea lista errori
            var errorList = '';
            for (var i = 0; i < errors.length; i++) {
                errorList += '<li>' + errors[i] + '</li>';
            }
            
            // Mostra errori
            var alertHtml = '<div class="alert alert-danger alert-dismissible fade show">' +
                '<strong>Correggi questi errori:</strong>' +
                '<ul class="mb-0 mt-2">' + errorList + '</ul>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>';
            
            $('#createUserForm').prepend(alertHtml);
            
            // Scroll in alto
            $('html, body').animate({ scrollTop: 0 }, 300);
            
            console.log('❌ Errori trovati:', errors);
            return false;
        }
        
        // Form valido - procedi
        formSubmitted = true;
        
        // Disabilita pulsante
        var submitBtn = $('#createBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Creazione...');
        
        // Messaggio di caricamento
        var loadingHtml = '<div class="alert alert-info">' +
            '<i class="bi bi-hourglass-split me-2"></i>' +
            '<strong>Creazione in corso...</strong> Non chiudere la pagina.' +
            '</div>';
        
        $('#createUserForm').prepend(loadingHtml);
        
        console.log('✅ Form inviato correttamente');
        
        // Lascia che il form si invii normalmente
        return true;
    });
    
    // === ANTEPRIMA ===
    
    $('#previewBtn').on('click', function() {
        var username = $('#username').val() || 'Non specificato';
        var nome = $('#nome').val() || '';
        var cognome = $('#cognome').val() || '';
        var livello = $('#livello_accesso option:selected').text() || 'Non selezionato';
        
        var previewHtml = '<div class="mb-3">' +
            '<strong>Username:</strong> ' + username + '<br>' +
            '<strong>Nome:</strong> ' + nome + ' ' + cognome + '<br>' +
            '<strong>Livello:</strong> ' + livello;
        
        if ($('#livello_accesso').val() === '2') {
            var dataNascita = $('#data_nascita').val() || 'Non specificata';
            var specializzazione = $('#specializzazione').val() || 'Non specificata';
            var centro = $('#centro_assistenza_id option:selected').text() || 'Nessun centro assegnato';
            
            previewHtml += '<br><strong>Data Nascita:</strong> ' + dataNascita +
                '<br><strong>Specializzazione:</strong> ' + specializzazione +
                '<br><strong>Centro:</strong> ' + centro;
        }
        
        previewHtml += '</div>';
        
        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    });
    
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#createUserForm').submit();
    });
    
    // === INIZIALIZZAZIONE ===
    
    // Nascondi campi tecnico inizialmente
    $('#dati-tecnico').hide();
    
    // Focus iniziale
    $('#username').focus();
    
    console.log('✅ Form inizializzato - Centro assistenza opzionale');
});