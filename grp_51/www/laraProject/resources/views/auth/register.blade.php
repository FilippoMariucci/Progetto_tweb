{{-- Vista di registrazione per amministratori --}}
@extends('layouts.app')

@section('title', 'Registrazione Nuovo Utente')

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item active">Registrazione Utente</li>
        </ol>
    </nav>

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-plus text-primary me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Registrazione Nuovo Utente</h1>
                    <p class="text-muted mb-0">
                        Aggiungi un nuovo utente al sistema di assistenza tecnica
                    </p>
                </div>
            </div>
            
            <!-- Alert informativo -->
            <div class="alert alert-info border-start border-primary border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Nota:</strong> Solo gli amministratori possono registrare nuovi utenti nel sistema.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>
                        Dati Nuovo Utente
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('register') }}" method="POST" id="registerForm">
                        @csrf
                        
                        <!-- === CREDENZIALI ACCOUNT === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3 border-bottom pb-2">
                                    <i class="bi bi-key me-2"></i>Credenziali di Accesso
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">
                                <i class="bi bi-at me-1"></i>Username *
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}"
                                   required 
                                   maxlength="255"
                                   placeholder="es: mario.rossi">
                            <div class="form-text">
                                Username univoco per l'accesso. Solo lettere, numeri, punti e underscore.
                            </div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-lock me-1"></i>Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           required
                                           minlength="8"
                                           placeholder="Minimo 8 caratteri">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">
                                    <i class="bi bi-lock-fill me-1"></i>Conferma Password *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       required
                                       minlength="8"
                                       placeholder="Ripeti la password">
                            </div>
                        </div>
                        
                        <!-- === DATI PERSONALI === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3 border-bottom pb-2">
                                    <i class="bi bi-person me-2"></i>Informazioni Personali
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Nome e Cognome -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-bold">
                                    <i class="bi bi-person me-1"></i>Nome *
                                </label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="Mario">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cognome" class="form-label fw-bold">
                                    <i class="bi bi-person-fill me-1"></i>Cognome *
                                </label>
                                <input type="text" 
                                       class="form-control @error('cognome') is-invalid @enderror" 
                                       id="cognome" 
                                       name="cognome" 
                                       value="{{ old('cognome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="Rossi">
                                @error('cognome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Livello di Accesso -->
                        <div class="mb-4">
                            <label for="livello_accesso" class="form-label fw-bold">
                                <i class="bi bi-shield me-1"></i>Livello di Accesso *
                            </label>
                            <select class="form-select @error('livello_accesso') is-invalid @enderror" 
                                    id="livello_accesso" 
                                    name="livello_accesso" 
                                    required>
                                <option value="">Seleziona livello di accesso</option>
                                <option value="2" {{ old('livello_accesso') == '2' ? 'selected' : '' }}>
                                    🔵 Tecnico Centro Assistenza
                                </option>
                                <option value="3" {{ old('livello_accesso') == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale
                                </option>
                                <option value="4" {{ old('livello_accesso') == '4' ? 'selected' : '' }}>
                                    🔴 Amministratore Sistema
                                </option>
                            </select>
                            <div class="form-text">
                                Determina le funzionalità accessibili nel sistema
                            </div>
                            @error('livello_accesso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- === DATI TECNICO (condizionali) === -->
                        <div id="dati-tecnico" class="d-none">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-info mb-3 border-bottom pb-2">
                                        <i class="bi bi-tools me-2"></i>Informazioni Specifiche Tecnico
                                    </h6>
                                </div>
                            </div>
                            
                            <!-- Data Nascita -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_nascita" class="form-label fw-bold">
                                        <i class="bi bi-calendar me-1"></i>Data di Nascita *
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('data_nascita') is-invalid @enderror" 
                                           id="data_nascita" 
                                           name="data_nascita" 
                                           value="{{ old('data_nascita') }}"
                                           max="{{ date('Y-m-d') }}">
                                    @error('data_nascita')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="specializzazione" class="form-label fw-bold">
                                        <i class="bi bi-star me-1"></i>Specializzazione *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('specializzazione') is-invalid @enderror" 
                                           id="specializzazione" 
                                           name="specializzazione" 
                                           value="{{ old('specializzazione') }}"
                                           placeholder="es: Elettrodomestici, Climatizzatori"
                                           maxlength="255">
                                    @error('specializzazione')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Centro Assistenza -->
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label fw-bold">
                                    <i class="bi bi-geo-alt me-1"></i>Centro di Assistenza *
                                </label>
                                <select class="form-select @error('centro_assistenza_id') is-invalid @enderror" 
                                        id="centro_assistenza_id" 
                                        name="centro_assistenza_id">
                                    <option value="">Seleziona centro di assistenza</option>
                                    @foreach($centri as $centro)
                                        <option value="{{ $centro->id }}" {{ old('centro_assistenza_id') == $centro->id ? 'selected' : '' }}>
                                            {{ $centro->nome }} - {{ $centro->citta }} ({{ $centro->provincia }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Centro di assistenza di appartenenza</div>
                                @error('centro_assistenza_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-person-plus me-1"></i>Registra Utente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR INFORMATIVA === -->
        <div class="col-lg-4">
            
            <!-- Guida Livelli -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check text-primary me-2"></i>Livelli di Accesso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">🔵</span>
                            <strong>Tecnico</strong>
                        </div>
                        <ul class="small text-muted mb-0">
                            <li>Visualizza catalogo completo</li>
                            <li>Accede a malfunzionamenti e soluzioni</li>
                            <li>Può segnalare problemi</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2">🟡</span>
                            <strong>Staff Aziendale</strong>
                        </div>
                        <ul class="small text-muted mb-0">
                            <li>Tutte le funzioni Tecnico</li>
                            <li>Crea e modifica soluzioni</li>
                            <li>Gestisce prodotti assegnati</li>
                        </ul>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-danger me-2">🔴</span>
                            <strong>Amministratore</strong>
                        </div>
                        <ul class="small text-muted mb-0">
                            <li>Controllo completo sistema</li>
                            <li>Gestisce utenti e prodotti</li>
                            <li>Configura centri assistenza</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Utenti Attuali -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-people text-success me-2"></i>Utenti Registrati
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-1 text-info">{{ \App\Models\User::where('livello_accesso', '2')->count() }}</h5>
                            <small class="text-muted">Tecnici</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-1 text-warning">{{ \App\Models\User::where('livello_accesso', '3')->count() }}</h5>
                            <small class="text-muted">Staff</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-1 text-danger">{{ \App\Models\User::where('livello_accesso', '4')->count() }}</h5>
                            <small class="text-muted">Admin</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h5 class="mb-1 text-primary">{{ \App\Models\User::count() }}</h5>
                        <small class="text-muted">Totale Utenti</small>
                    </div>
                </div>
            </div>
            
            <!-- Istruzioni -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Istruzioni
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Username:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Deve essere univoco nel sistema</li>
                                <li>Solo lettere, numeri, punti e underscore</li>
                                <li>Suggerito: nome.cognome</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Password:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Minimo 8 caratteri</li>
                                <li>Deve essere confermata</li>
                                <li>L'utente potrà cambiarla al primo accesso</li>
                            </ul>
                        </div>
                        
                        <div class="mb-0">
                            <strong>Tecnici:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Richiedono data di nascita</li>
                                <li>Specializzazione obbligatoria</li>
                                <li>Devono essere assegnati a un centro</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-label.fw-bold {
    color: #495057;
}

.border-bottom {
    border-bottom: 2px solid #e9ecef !important;
}

.badge {
    font-size: 0.75rem;
}

.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === GESTIONE CAMPI CONDIZIONALI PER TECNICI ===
    
    /**
     * Mostra/nasconde i campi specifici per i tecnici
     * quando viene selezionato il livello di accesso "Tecnico"
     */
    $('#livello_accesso').on('change', function() {
        const livello = $(this).val(); // Ottiene il valore del livello selezionato
        const datiTecnico = $('#dati-tecnico'); // Container dei campi tecnico
        
        if (livello === '2') { // Se è selezionato "Tecnico" (livello 2)
            // Mostra i campi con animazione
            datiTecnico.removeClass('d-none').hide().slideDown();
            
            // Rende obbligatori i campi specifici per i tecnici
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', true);
        } else {
            // Nasconde i campi per altri livelli
            datiTecnico.slideUp(function() {
                $(this).addClass('d-none');
            });
            
            // Rimuove l'obbligo dai campi tecnico
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', false);
        }
    });
    
    // === GESTIONE TOGGLE PASSWORD ===
    
    /**
     * Mostra/nasconde la password quando si clicca sull'icona
     */
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password'); // Campo password
        const icon = $(this).find('i'); // Icona del pulsante
        
        // Alterna il tipo di input tra password e text
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // === VALIDAZIONE REAL-TIME ===
    
    /**
     * Controlla la corrispondenza delle password in tempo reale
     */
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (confirmation.length > 0) {
            if (password === confirmation) {
                // Password corrispondenti
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('.password-match-error').remove();
            } else {
                // Password non corrispondenti
                $(this).removeClass('is-valid').addClass('is-invalid');
                if (!$('.password-match-error').length) {
                    $(this).after('<div class="invalid-feedback password-match-error">Le password non coincidono</div>');
                }
            }
        } else {
            // Campo vuoto, rimuove validazione
            $(this).removeClass('is-valid is-invalid');
            $('.password-match-error').remove();
        }
    });
    
    /**
     * Validazione username in tempo reale
     */
    $('#username').on('input', function() {
        const username = $(this).val();
        const regex = /^[a-zA-Z0-9._-]+$/; // Solo caratteri validi
        
        if (username.length > 0) {
            if (regex.test(username) && username.length >= 3) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('.username-error').remove();
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                if (!$('.username-error').length) {
                    $(this).after('<div class="invalid-feedback username-error">Username deve essere almeno 3 caratteri e contenere solo lettere, numeri, punti e underscore</div>');
                }
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
            $('.username-error').remove();
        }
    });
    
    // === AUTO-COMPLETE USERNAME ===
    
    /**
     * Suggerisce automaticamente l'username basato su nome e cognome
     */
    $('#nome, #cognome').on('input', function() {
        const nome = $('#nome').val().toLowerCase().trim();
        const cognome = $('#cognome').val().toLowerCase().trim();
        
        // Solo se entrambi i campi hanno contenuto e username è vuoto
        if (nome.length > 0 && cognome.length > 0 && $('#username').val().trim() === '') {
            const suggestedUsername = nome + '.' + cognome;
            $('#username').val(suggestedUsername);
            $('#username').trigger('input'); // Triggera la validazione
        }
    });
    
    // === VALIDAZIONE FORM FINALE ===
    
    /**
     * Validazione completa prima dell'invio del form
     */
    $('#registerForm').on('submit', function(e) {
        let isValid = true;
        let firstError = null;
        
        // Controlla tutti i campi obbligatori
        $(this).find('input[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('is-invalid');
                if (!firstError) firstError = $(this);
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Controlla corrispondenza password
        const password = $('#password').val();
        const confirmation = $('#password_confirmation').val();
        if (password !== confirmation) {
            isValid = false;
            $('#password_confirmation').addClass('is-invalid');
            if (!firstError) firstError = $('#password_confirmation');
        }
        
        // Se ci sono errori, previene l'invio e mostra il primo errore
        if (!isValid) {
            e.preventDefault();
            
            if (firstError) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
            
            // Mostra alert di errore
            if (!$('.alert-danger').length) {
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Errori nel form:</strong> Compila tutti i campi obbligatori e correggi gli errori evidenziati.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#registerForm').prepend(alertHtml);
            }
        }
    });
    
    // === MIGLIORAMENTI UX ===
    
    /**
     * Focus automatico sul primo campo
     */
    $('#username').focus();
    
    /**
     * Effetti visivi per i campi in focus
     */
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.mb-3').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.mb-3').removeClass('focused');
    });
    
    // CSS dinamico per gli effetti
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .focused { 
                transform: scale(1.01); 
                transition: transform 0.2s ease; 
            }
            .alert { 
                animation: slideInDown 0.5s ease; 
            }
            @keyframes slideInDown { 
                from { opacity: 0; transform: translateY(-20px); } 
                to { opacity: 1; transform: translateY(0); } 
            }
        `)
        .appendTo('head');
});
</script>
@endpush