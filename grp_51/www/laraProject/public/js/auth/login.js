Esempi per file specifici
admin/assegnazioni-index.js:
javascript/**
 * Admin Assegnazioni Index - Gestione assegnazione prodotti a staff
 * File: /public/js/admin/assegnazioni-index.js
 * Gestisce: selezione multipla prodotti, assegnazione staff, filtri
 * @author Gruppo 51 - Corso Tecnologie Web 2024/2025
 */

$(document).ready(function() {
    console.log('login caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'login') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    $(document).ready(function() {
    
    // === TOGGLE PASSWORD VISIBILITY ===
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const icon = $('#togglePasswordIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // === GESTIONE FORM SUBMIT ===
    $('#loginForm').on('submit', function(e) {
        const loginBtn = $('#loginBtn');
        const username = $('#username').val().trim();
        const password = $('#password').val();
        
        // Validazione base
        if (!username || !password) {
            e.preventDefault();
            showToast('Inserisci username e password', 'warning');
            return;
        }
        
        // Mostra loading
        loginBtn.prop('disabled', true);
        loginBtn.html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Accesso in corso...
        `);
        
        // Il form viene inviato normalmente
        // Il loading viene nascosto automaticamente al redirect o errore
    });
    
    // === RIMUOVI LOADING SE C'È ERRORE ===
    @if($errors->any())
        $('#loginBtn').prop('disabled', false).html(`
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Accedi
        `);
    @endif
    
    // === CREDENZIALI DI TEST (solo in sviluppo) ===
    @if(app()->environment('local'))
        $('.fill-credentials').on('click', function() {
            const username = $(this).data('username');
            const password = $(this).data('password');
            
            $('#username').val(username);
            $('#password').val(password);
            
            // Evidenzia i campi compilati
            $('#username, #password').addClass('border-success');
            
            setTimeout(() => {
                $('#username, #password').removeClass('border-success');
            }, 2000);
            
            showToast(`Credenziali inserite: ${username}`, 'info');
        });
    @endif
    
    // === VALIDAZIONE IN TEMPO REALE ===
    $('#username').on('input', function() {
        const value = $(this).val().trim();
        const field = $(this);
        
        if (value.length < 3) {
            field.removeClass('is-valid').addClass('is-invalid');
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    $('#password').on('input', function() {
        const value = $(this).val();
        const field = $(this);
        
        if (value.length < 6) {
            field.removeClass('is-valid').addClass('is-invalid');
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    // === AUTO-FOCUS E ENTER KEY ===
    $('#username').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            $('#password').focus();
        }
    });
    
    $('#password').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            $('#loginForm').submit();
        }
    });
    
    // === SICUREZZA: PREVIENI MULTIPLE SUBMIT ===
    let formSubmitted = false;
    $('#loginForm').on('submit', function() {
        if (formSubmitted) {
            return false;
        }
        formSubmitted = true;
        
        // Reset dopo 5 secondi in caso di errori
        setTimeout(() => {
            formSubmitted = false;
            $('#loginBtn').prop('disabled', false).html(`
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Accedi
            `);
        }, 5000);
    });
    
    // === CAPS LOCK DETECTION ===
    $('#password').on('keypress', function(e) {
        const capsLock = e.originalEvent.getModifierState && e.originalEvent.getModifierState('CapsLock');
        
        if (capsLock) {
            if (!$('#capsLockWarning').length) {
                $(this).after(`
                    <small id="capsLockWarning" class="text-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Caps Lock è attivo
                    </small>
                `);
            }
        } else {
            $('#capsLockWarning').remove();
        }
    });
    
    // === ANALYTICS E LOGGING (se necessario) ===
    $('#loginForm').on('submit', function() {
        const username = $('#username').val();
        const timestamp = new Date().toISOString();
        
        // Log attempt (no sensitive data)
        console.log('Login attempt', {
            username: username,
            timestamp: timestamp,
            userAgent: navigator.userAgent
        });
    });
    
    console.log('Login form initialized');
});

// === FUNZIONE TOAST ===
function showToast(message, type = 'info') {
    const toast = `
        <div class="toast align-items-center text-bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    if (!$('#toast-container').length) {
        $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    const $toast = $(toast);
    $('#toast-container').append($toast);
    
    const toastInstance = new bootstrap.Toast($toast[0]);
    toastInstance.show();
    
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
});