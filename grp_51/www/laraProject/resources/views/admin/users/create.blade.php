{{-- Vista per creare nuovo utente (Admin - UserController) --}}
@extends('layouts.app')

@section('title', 'Nuovo Utente')

@section('content')
<div class="container-fluid mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Gestione Utenti</a></li>
            <li class="breadcrumb-item active">Nuovo Utente</li>
        </ol>
    </nav>

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-plus text-success me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Crea Nuovo Utente</h1>
                    <p class="text-muted mb-0">
                        Aggiungi un nuovo utente al sistema di assistenza tecnica
                    </p>
                </div>
            </div>
            
            <div class="alert alert-success border-start border-success border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Nuovo Account:</strong> Compila tutti i campi obbligatori. L'utente riceverà le credenziali per l'accesso.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear text-success me-2"></i>
                        Informazioni Nuovo Utente
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                        @csrf
                        
                        <!-- === DATI ACCOUNT === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-key me-2"></i>Credenziali Account
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">
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
                            <div class="form-text">Username univoco per l'accesso al sistema (senza spazi)</div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
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
                                <div class="form-text">Minimo 8 caratteri</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-1"></i>Conferma Password *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       required
                                       minlength="8"
                                       placeholder="Ripeti la password">
                                <div class="form-text">Ripeti la password</div>
                            </div>
                        </div>
                        
                        <!-- Genera Password Automatica -->
                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-info btn-sm" id="generatePassword">
                                <i class="bi bi-magic me-1"></i>Genera Password Sicura
                            </button>
                        </div>
                        
                        <!-- === DATI PERSONALI === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-person me-2"></i>Informazioni Personali
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Nome e Cognome -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">
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
                                <label for="cognome" class="form-label fw-semibold">
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
                        
                        <!-- Livello Accesso -->
                        <div class="mb-4">
                            <label for="livello_accesso" class="form-label fw-semibold">
                                <i class="bi bi-shield me-1"></i>Livello di Accesso *
                            </label>
                            <select class="form-select @error('livello_accesso') is-invalid @enderror" 
                                    id="livello_accesso" 
                                    name="livello_accesso" 
                                    required>
                                <option value="">Seleziona livello di accesso</option>
                                <option value="2" {{ old('livello_accesso') == '2' ? 'selected' : '' }}>
                                    🔵 Tecnico - Visualizza e consulta soluzioni
                                </option>
                                <option value="3" {{ old('livello_accesso') == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale - Gestisce malfunzionamenti e soluzioni
                                </option>
                                <option value="4" {{ old('livello_accesso') == '4' ? 'selected' : '' }}>
                                    🔴 Amministratore - Controllo completo del sistema
                                </option>
                            </select>
                            <div class="form-text">Determina le funzionalità accessibili all'utente nel sistema</div>
                            @error('livello_accesso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- === DATI TECNICO (condizionali) === -->
                        <div id="dati-tecnico" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-info mb-3">
                                        <i class="bi bi-tools me-2"></i>Informazioni Tecnico
                                        <small class="text-muted">(obbligatorie per tecnici)</small>
                                    </h6>
                                </div>
                            </div>
                            
                            <!-- Data Nascita e Specializzazione -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_nascita" class="form-label fw-semibold">
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
                                    <label for="specializzazione" class="form-label fw-semibold">
                                        <i class="bi bi-star me-1"></i>Specializzazione *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('specializzazione') is-invalid @enderror" 
                                           id="specializzazione" 
                                           name="specializzazione" 
                                           value="{{ old('specializzazione') }}"
                                           placeholder="es: Elettrodomestici, Climatizzatori"
                                           maxlength="255"
                                           list="specializzazioni">
                                    <datalist id="specializzazioni">
                                        <option value="Elettrodomestici">
                                        <option value="Lavatrici e Lavastoviglie">
                                        <option value="Frigoriferi e Freezer">
                                        <option value="Forni e Microonde">
                                        <option value="Climatizzatori">
                                        <option value="Caldaie e Scaldabagni">
                                        <option value="Piccoli Elettrodomestici">
                                        <option value="Aspirapolvere">
                                        <option value="Impianti Elettrici">
                                    </datalist>
                                    @error('specializzazione')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Centro Assistenza -->
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label fw-semibold">
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
                                <div class="form-text">Centro di assistenza di appartenenza del tecnico</div>
                                @error('centro_assistenza_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === RIEPILOGO === -->
                        <div id="riepilogo-utente" class="alert alert-light border" style="display: none;">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle text-success me-2"></i>Riepilogo Nuovo Utente
                            </h6>
                            <div id="riepilogo-content"></div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                <button type="submit" class="btn btn-success" id="createBtn">
                                    <i class="bi bi-person-plus me-1"></i>Crea Utente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR GUIDA === -->
        <div class="col-lg-4">
            
            <!-- Guida Livelli -->
            <div class="card card-custom mb-4">
                <div class="card-header">
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
                            <li>Visualizza prodotti completi</li>
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
            
            <!-- Statistiche Correnti -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>Utenti Attuali
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
                </div>
            </div>
            
            <!-- Suggerimenti -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Suggerimenti
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Username:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Usa formato nome.cognome</li>
                                <li>Solo lettere, numeri e punti</li>
                                <li>Deve essere univoco</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Password sicura:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Almeno 8 caratteri</li>
                                <li>Combinazione lettere/numeri</li>
                                <li>Usa il generatore automatico</li>
                            </ul>
                        </div>
                        
                        <div class="mb-0">
                            <strong>Tecnici:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Centro obbligatorio</li>
                                <li>Specializzazione chiara</li>
                                <li>Data nascita per statistiche</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Centri Disponibili -->
            @if($centri->count() > 0)
                <div class="card card-custom">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt text-info me-2"></i>Centri Disponibili
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            @foreach($centri->take(5) as $centro)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $centro->nome }}</strong>
                                        <div class="text-muted">{{ $centro->citta }}</div>
                                    </div>
                                    <span class="badge bg-secondary">{{ $centro->tecnici()->count() }}</span>
                                </div>
                            @endforeach
                            
                            @if($centri->count() > 5)
                                <div class="text-center mt-2">
                                    <small class="text-muted">... e altri {{ $centri->count() - 5 }} centri</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- === MODAL ANTEPRIMA === -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Nuovo Utente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Contenuto popolato via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Modifica</button>
                <button type="button" class="btn btn-success" id="createFromPreview">
                    <i class="bi bi-person-plus me-1"></i>Conferma Creazione
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
}

.border-start.border-4 {
    border-width: 4px !important;
}

.badge {
    font-size: 0.75rem;
}

/* Password strength indicator */
.password-strength {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-top: 5px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    transition: width 0.3s ease, background-color 0.3s ease;
}

.strength-weak { background-color: #dc3545; }
.strength-medium { background-color: #ffc107; }
.strength-strong { background-color: #28a745; }

/* Preview styling */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #28a745;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #28a745;
    margin-bottom: 0.5rem;
}

/* Focused field styling */
.focused { 
    transform: scale(1.02); 
    transition: transform 0.2s ease; 
}

/* Animation styles */
.password-strength { 
    animation: slideIn 0.3s ease; 
}

@keyframes slideIn { 
    from { opacity: 0; transform: translateY(-10px); } 
    to { opacity: 1; transform: translateY(0); } 
}

.alert { 
    animation: fadeIn 0.5s ease; 
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === GESTIONE CAMPI CONDIZIONALI ===
    
    /**
     * Gestisce la visualizzazione dei campi specifici per i tecnici
     * Quando si seleziona "Tecnico" (livello 2), mostra i campi aggiuntivi
     */
    $('#livello_accesso').on('change', function() {
        const livello = $(this).val();
        const datiTecnico = $('#dati-tecnico');
        
        if (livello === '2') {
            // Mostra i campi con animazione slideDown
            datiTecnico.slideDown();
            
            // Rende obbligatori i campi specifici per i tecnici
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', true);
                
        } else {
            // Nasconde i campi per altri livelli di accesso
            datiTecnico.slideUp();
            
            // Rimuove l'obbligo dai campi tecnico
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', false);
        }
        
        // Aggiorna il riepilogo se visibile
        if ($('#riepilogo-utente').is(':visible')) {
            updatePreview();
        }
    });
    
    // === GESTIONE PASSWORD ===
    
    /**
     * Toggle per mostrare/nascondere la password
     */
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    /**
     * Generatore di password sicure
     */
    $('#generatePassword').on('click', function() {
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        const allChars = lowercase + uppercase + numbers + symbols;
        let password = '';
        
        // Assicura che ci sia almeno un carattere di ogni tipo
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];
        
        // Completa con caratteri casuali fino a 12 caratteri totali
        for (let i = 4; i < 12; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }
        
        // Mescola i caratteri
        password = password.split('').sort(() => Math.random() - 0.5).join('');
        
        // Imposta la password nei campi
        $('#password, #password_confirmation').val(password);
        
        // Mostra la password temporaneamente
        $('#password').attr('type', 'text');
        $('#togglePassword').find('i').removeClass('bi-eye').addClass('bi-eye-slash');
        
        // Feedback visivo
        $(this).removeClass('btn-outline-info').addClass('btn-success');
        $(this).html('<i class="bi bi-check me-1"></i>Password Generata!');
        
        setTimeout(() => {
            $(this).removeClass('btn-success').addClass('btn-outline-info');
            $(this).html('<i class="bi bi-magic me-1"></i>Genera Password Sicura');
        }, 2000);
        
        updatePasswordStrength(password);
    });
    
    /**
     * Validazione password in tempo reale
     */
    $('#password').on('input', function() {
        const password = $(this).val();
        updatePasswordStrength(password);
        
        if ($('#password_confirmation').val() === '') {
            checkPasswordMatch();
        }
    });
    
    $('#password_confirmation').on('input', checkPasswordMatch);
    
    /**
     * Aggiorna l'indicatore di forza password
     */
    function updatePasswordStrength(password) {
        $('.password-strength').remove();
        
        if (password.length === 0) return;
        
        let score = 0;
        
        if (password.length >= 8) score += 25;
        if (password.length >= 12) score += 25;
        if (/[a-z]/.test(password)) score += 12.5;
        if (/[A-Z]/.test(password)) score += 12.5;
        if (/[0-9]/.test(password)) score += 12.5;
        if (/[^A-Za-z0-9]/.test(password)) score += 12.5;
        
        let strengthClass, strengthText;
        if (score < 50) {
            strengthClass = 'strength-weak';
            strengthText = 'Debole';
        } else if (score < 75) {
            strengthClass = 'strength-medium';
            strengthText = 'Media';
        } else {
            strengthClass = 'strength-strong';
            strengthText = 'Forte';
        }
        
        const strengthIndicator = `
            <div class="password-strength">
                <div class="password-strength-bar ${strengthClass}" style="width: ${score}%"></div>
            </div>
            <small class="text-muted">Forza password: <span class="${strengthClass.replace('strength-', 'text-')}">${strengthText}</span></small>
        `;
        
        $('#password').after(strengthIndicator);
    }
    
    /**
     * Controlla corrispondenza password
     */
    function checkPasswordMatch() {
        const password = $('#password').val();
        const confirmation = $('#password_confirmation').val();
        
        if (confirmation.length === 0) return;
        
        if (password === confirmation) {
            $('#password_confirmation').removeClass('is-invalid').addClass('is-valid');
            $('.password-match-feedback').remove();
        } else {
            $('#password_confirmation').removeClass('is-valid').addClass('is-invalid');
            if (!$('.password-match-feedback').length) {
                $('#password_confirmation').after('<div class="invalid-feedback password-match-feedback">Le password non coincidono</div>');
            }
        }
    }
    
    // === GESTIONE ANTEPRIMA ===
    
    /**
     * Mostra l'anteprima del nuovo utente in un modal
     */
    $('#previewBtn').on('click', function() {
        updatePreview();
        $('#previewModal').modal('show');
    });
    
    /**
     * Aggiorna il contenuto dell'anteprima
     */
    function updatePreview() {
        const formData = {
            username: $('#username').val(),
            nome: $('#nome').val(),
            cognome: $('#cognome').val(),
            livello_accesso: $('#livello_accesso').val(),
            data_nascita: $('#data_nascita').val(),
            specializzazione: $('#specializzazione').val(),
            centro_assistenza: $('#centro_assistenza_id option:selected').text()
        };
        
        // Mappa dei livelli di accesso
        const livelli = {
            '2': '🔵 Tecnico',
            '3': '🟡 Staff Aziendale', 
            '4': '🔴 Amministratore'
        };
        
        let previewHtml = `
            <div class="preview-section">
                <div class="preview-title">👤 Informazioni Account</div>
                <div><strong>Username:</strong> ${formData.username || 'Non specificato'}</div>
                <div><strong>Nome Completo:</strong> ${formData.nome} ${formData.cognome}</div>
                <div><strong>Livello Accesso:</strong> ${livelli[formData.livello_accesso] || 'Non selezionato'}</div>
            </div>
        `;
        
        // Sezione tecnico solo se livello 2
        if (formData.livello_accesso === '2') {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">🔧 Informazioni Tecnico</div>
                    <div><strong>Data Nascita:</strong> ${formData.data_nascita ? new Date(formData.data_nascita).toLocaleDateString('it-IT') : 'Non specificata'}</div>
                    <div><strong>Specializzazione:</strong> ${formData.specializzazione || 'Non specificata'}</div>
                    <div><strong>Centro Assistenza:</strong> ${formData.centro_assistenza !== 'Seleziona centro di assistenza' ? formData.centro_assistenza : 'Non selezionato'}</div>
                </div>
            `;
        }
        
        // Aggiunge sezione permessi
        let permessi = [];
        switch(formData.livello_accesso) {
            case '2':
                permessi = ['Visualizza prodotti completi', 'Accede a malfunzionamenti e soluzioni', 'Può segnalare problemi'];
                break;
            case '3':
                permessi = ['Tutte le funzioni Tecnico', 'Crea e modifica soluzioni', 'Gestisce prodotti assegnati'];
                break;
            case '4':
                permessi = ['Controllo completo sistema', 'Gestisce utenti e prodotti', 'Configura centri assistenza'];
                break;
        }
        
        if (permessi.length > 0) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">🛡️ Permessi Utente</div>
                    <ul class="mb-0">
                        ${permessi.map(p => `<li>${p}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        $('#previewContent').html(previewHtml);
        
        // Mostra anche il riepilogo inline
        $('#riepilogo-content').html(previewHtml);
        $('#riepilogo-utente').slideDown();
    }
    
    /**
     * Conferma creazione dal modal di anteprima
     */
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#createUserForm').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    /**
     * Validazione in tempo reale del form
     */
    $('#createUserForm input, #createUserForm select').on('blur change', function() {
        validateField($(this));
    });
    
    /**
     * Valida un singolo campo
     */
    function validateField($field) {
        const value = $field.val().trim();
        const fieldName = $field.attr('name');
        let isValid = true;
        let errorMessage = '';
        
        // Validazioni specifiche per campo
        switch(fieldName) {
            case 'username':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'Username deve essere almeno 3 caratteri';
                } else if (!/^[a-zA-Z0-9._-]+$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Username può contenere solo lettere, numeri, punti, underscore e trattini';
                }
                break;
                
            case 'password':
                if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'Password deve essere almeno 8 caratteri';
                }
                break;
                
            case 'nome':
            case 'cognome':
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Deve essere almeno 2 caratteri';
                }
                break;
                
            case 'data_nascita':
                if ($('#livello_accesso').val() === '2' && !value) {
                    isValid = false;
                    errorMessage = 'Data nascita obbligatoria per i tecnici';
                } else if (value && new Date(value) >= new Date()) {
                    isValid = false;
                    errorMessage = 'Data nascita deve essere nel passato';
                }
                break;
        }
        
        // Applica la validazione visiva
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.siblings('.invalid-feedback.custom-validation').remove();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            
            // Aggiunge messaggio di errore se non esiste
            if (!$field.siblings('.invalid-feedback.custom-validation').length) {
                $field.after(`<div class="invalid-feedback custom-validation">${errorMessage}</div>`);
            } else {
                $field.siblings('.invalid-feedback.custom-validation').text(errorMessage);
            }
        }
        
        return isValid;
    }
    
    /**
     * Validazione completa del form prima dell'invio
     */
    $('#createUserForm').on('submit', function(e) {
        let isFormValid = true;
        
        // Valida tutti i campi obbligatori
        $(this).find('input[required], select[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        // Controlla corrispondenza password
        if ($('#password').val() !== $('#password_confirmation').val()) {
            isFormValid = false;
            $('#password_confirmation').addClass('is-invalid');
        }
        
        // Previene l'invio se ci sono errori
        if (!isFormValid) {
            e.preventDefault();
            
            // Mostra alert di errore
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Errori nel form:</strong> Correggi i campi evidenziati prima di continuare.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#createUserForm').prepend(alertHtml);
            
            // Scroll al primo errore
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });
    
    // === MIGLIORAMENTI UX ===
    
    /**
     * Auto-complete intelligente per username basato su nome e cognome
     */
    $('#nome, #cognome').on('input', function() {
        const nome = $('#nome').val().toLowerCase().trim();
        const cognome = $('#cognome').val().toLowerCase().trim();
        
        if (nome.length > 0 && cognome.length > 0) {
            // Suggerisce username formato nome.cognome
            const suggestedUsername = nome + '.' + cognome;
            
            // Solo se il campo username è vuoto
            if ($('#username').val().trim() === '') {
                $('#username').val(suggestedUsername);
                $('#username').trigger('blur'); // Triggera la validazione
            }
        }
    });
    
    /**
     * Tooltip informativi
     */
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    /**
     * Conferma prima di abbandonare la pagina se ci sono modifiche
     */
    let formChanged = false;
    $('#createUserForm input, #createUserForm select, #createUserForm textarea').on('input change', function() {
        formChanged = true;
    });
    
    $(window).on('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Vuoi davvero uscire?';
            return e.returnValue;
        }
    });
    
    // Rimuove l'avviso quando il form viene inviato
    $('#createUserForm').on('submit', function() {
        formChanged = false;
    });
    
    /**
     * Animazioni e feedback visivi migliorati
     */
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.mb-3').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.mb-3').removeClass('focused');
    });
    
    /**
     * Contatore caratteri per campi con limite
     */
    $('#specializzazione').on('input', function() {
        const maxLength = 255;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        // Rimuove contatori esistenti
        $(this).siblings('.char-counter').remove();
        
        // Aggiunge contatore
        if (currentLength > 0) {
            const counterClass = remaining < 50 ? 'text-warning' : remaining < 20 ? 'text-danger' : 'text-muted';
            $(this).after(`<small class="char-counter ${counterClass}">${remaining} caratteri rimanenti</small>`);
        }
    });
    
    // Inizializza componenti all'avvio
    initializeComponents();
    
    /**
     * Inizializza componenti e impostazioni iniziali
     */
    function initializeComponents() {
        // Nasconde la sezione tecnico inizialmente
        $('#dati-tecnico').hide();
        
        // Nasconde il riepilogo inizialmente
        $('#riepilogo-utente').hide();
        
        // Focus sul primo campo
        $('#username').focus();
        
        console.log('✅ Sistema creazione utente inizializzato correttamente');
    }
});

// === FUNZIONI GLOBALI DI UTILITÀ ===

/**
 * Funzione per validare email (se necessaria in futuro)
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Funzione per formattare numeri di telefono italiani
 */
function formatPhoneNumber(phone) {
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 10) {
        return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
    }
    return phone;
}

/**
 * Debounce per ottimizzare le chiamate AJAX
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush