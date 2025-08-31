@extends('layouts.app')

@section('title', 'Accedi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <!-- === INFORMAZIONI LIVELLI DI ACCESSO === -->
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Livelli di Accesso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-info me-2">Livello 2</span>
                                <strong>Tecnici</strong>
                            </div>
                            <small class="text-muted">
                                Accesso a malfunzionamenti e soluzioni tecniche
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-warning me-2">Livello 3</span>
                                <strong>Staff Aziendale</strong>
                            </div>
                            <small class="text-muted">
                                Gestione completa di malfunzionamenti e soluzioni
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2">Livello 4</span>
                                <strong>Amministratori</strong>
                            </div>
                            <small class="text-muted">
                                Controllo completo: utenti, prodotti, sistema
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === CREDENZIALI DI TEST === -->
            @if(app()->environment('local'))
                <div class="card card-custom mt-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-tools me-2"></i>
                            Credenziali di Test (Solo Sviluppo)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 text-sm">
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm w-100 fill-credentials" 
                                        data-username="adminadmin" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-gear me-1"></i>
                                    Admin: adminadmin / dNWRdNWR
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-warning btn-sm w-100 fill-credentials" 
                                        data-username="staffstaff" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Staff: staffstaff / dNWRdNWR
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-info btn-sm w-100 fill-credentials" 
                                        data-username="tecntecn" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-wrench me-1"></i>
                                    Tecnico: tecntecn / dNWRdNWR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- === LINK PUBBLICI === -->
            <div class="text-center mt-4">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-house me-1"></i>
                            Torna alla Home
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-box me-1"></i>
                            Catalogo Pubblico
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush === CARD LOGIN === -->
            <div class="card card-custom shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="h3 mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Accesso al Sistema
                    </h2>
                    <p class="mb-0 mt-2 text-white-50">
                        Inserisci le tue credenziali per accedere
                    </p>
                </div>
                
                <div class="card-body p-4">
                    
                    <!-- Form di login -->
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        
                        <!-- Campo Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Username
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autocomplete="username" 
                                   autofocus
                                   placeholder="Inserisci il tuo username">
                            
                            @error('username')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Campo Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Inserisci la tua password">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                        data-bs-toggle="tooltip"
                                        title="Mostra/Nascondi password">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Ricordami -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    <i class="bi bi-bookmark me-1"></i>
                                    Ricordami su questo dispositivo
                                </label>
                            </div>
                        </div>

                        <!-- Pulsante Login -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Accedi
                            </button>
                        </div>
                    </form>
                    
                </div>
                
                <!-- Footer della card -->
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Accesso sicuro protetto da SSL
                    </small>
                </div>
            </div>

            <!--