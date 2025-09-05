/**
 * AuthManager - Gestione Autenticazione e Login
 * TechSupport Pro - Gruppo 51
 *
 * Questa classe gestisce tutte le funzionalità di autenticazione lato client:
 * - Gestione form di login
 * - Toggle visibilità password
 * - Helper per credenziali di test
 * - Validazione e shortcut tastiera
 * - Rilevamento CapsLock
 * Tutte le pagine di login fanno riferimento a questa classe per la logica JS.
 */

class AuthManager {
    constructor() {
        this.initializeAuth();
    }

    /**
     * Inizializza gestione autenticazione
     * Avvia setup form, toggle password, helper, validazione e shortcut.
     */
    initializeAuth() {
        this.setupLoginForm();
        this.setupPasswordToggle();
        this.setupCredentialsHelpers();
        this.setupValidation();
        this.setupKeyboardShortcuts();
        this.detectCapsLock();
        
        console.log('🔐 Auth Manager inizializzato');
    }

    /**
     * Gestione form di login
     * Valida i campi, previene doppio submit e mostra spinner.
     */
    setupLoginForm() {
        const loginForm = $('#loginForm');
        
        if (loginForm.length) {
            let formSubmitted = false;
            
            loginForm.on('submit', (e) => {
                const username = $('#username').val().trim();
                const password = $('#password').val();
                
                // Validazione base
                if (!username || !password) {
                    e.preventDefault();
                    showToast('Inserisci username e password', 'warning');
                    return false;
                }
                
                // Previeni doppio submit
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                
                formSubmitted = true;
                this.showLoginSpinner();
                
                // Reset dopo 5 secondi
                setTimeout(() => {
                    formSubmitted = false;
                    this.hideLoginSpinner();
                }, 5000);
            });
        }
    }

    /**
     * Toggle visibilità password
     */
    setupPasswordToggle() {
        $('#togglePassword').on('click', () => {
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
    }

    /**
     * Helper per credenziali di test (solo sviluppo)
     */
    setupCredentialsHelpers() {
        $('.fill-credentials').on('click', function() {
            const username = $(this).data('username');
            const password = $(this).data('password');
            
            $('#username').val(username);
            $('#password').val(password);
            
            // Evidenzia i campi
            $('#username, #password').addClass('border-success');
            
            setTimeout(() => {
                $('#username, #password').removeClass('border-success');
            }, 2000);
            
            showToast(`Credenziali inserite: ${username}`, 'info');
        });
    }

    /**
     * Validazione in tempo reale
     */
    setupValidation() {
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
    }

    /**
     * Scorciatoie tastiera
     */
    setupKeyboardShortcuts() {
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
    }

    /**
     * Rileva Caps Lock attivo
     */
    detectCapsLock() {
        $('#password').on('keypress', function(e) {
            const capsLock = e.originalEvent.getModifierState && 
                           e.originalEvent.getModifierState('CapsLock');
            
            if (capsLock) {
                if (!$('#capsLockWarning').length) {
                    $(this).after(`
                        <small id="capsLockWarning" class="text-warning mt-1">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Caps Lock è attivo
                        </small>
                    `);
                }
            } else {
                $('#capsLockWarning').remove();
            }
        });
    }

    /**
     * Mostra spinner durante login
     */
    showLoginSpinner() {
        const loginBtn = $('#loginBtn');
        loginBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Accesso in corso...
        `);
    }

    /**
     * Nasconde spinner login
     */
    hideLoginSpinner() {
        const loginBtn = $('#loginBtn');
        loginBtn.prop('disabled', false).html(`
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Accedi
        `);
    }

    /**
     * Log delle attività di login (per analytics)
     */
    logLoginAttempt(username) {
        const logData = {
            username: username,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            screen: `${screen.width}x${screen.height}`
        };
        
        console.log('Login attempt:', logData);
        
        // In una versione più avanzata, potresti inviare al server
        // $.post('/api/log-login-attempt', logData);
    }
}

// Inizializza quando DOM è pronto
$(document).ready(function() {
    if ($('#loginForm').length || $('.auth-page').length) {
        window.authManager = new AuthManager();
    }
});