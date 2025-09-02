{{-- Vista per modificare utente esistente (Admin) --}}
@extends('layouts.app')

@section('title', 'Modifica Utente - ' . $user->nome_completo)

@section('content')
<div class="container mt-4">
    
    

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar-circle bg-warning text-white me-3">
                    {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                </div>
                <div>
                    <h1 class="h2 mb-1">Modifica Utente</h1>
                    <p class="text-muted mb-0">
                        Aggiorna le informazioni di <strong>{{ $user->nome_completo }}</strong>
                    </p>
                </div>
            </div>
            
            @if($user->id === auth()->id())
                <div class="alert alert-warning border-start border-warning border-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione:</strong> Stai modificando il tuo account. Fai attenzione alle modifiche al livello di accesso.
                </div>
            @else
                <div class="alert alert-info border-start border-info border-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Modifica Utente:</strong> Aggiorna i dati dell'utente. I campi obbligatori sono contrassegnati con *.
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear text-warning me-2"></i>
                        Informazioni Utente
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')
                        
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
                                   value="{{ old('username', $user->username) }}"
                                   required 
                                   maxlength="255">
                            <div class="form-text">Username univoco per l'accesso al sistema</div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Nuova Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   minlength="8">
                            <div class="form-text">Lascia vuoto per mantenere la password corrente</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Conferma Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill me-1"></i>Conferma Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   minlength="8">
                            <div class="form-text">Ripeti la nuova password</div>
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
                                       value="{{ old('nome', $user->nome) }}"
                                       required 
                                       maxlength="255">
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
                                       value="{{ old('cognome', $user->cognome) }}"
                                       required 
                                       maxlength="255">
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
                                <option value="">Seleziona livello</option>
                                <option value="2" {{ old('livello_accesso', $user->livello_accesso) == '2' ? 'selected' : '' }}>
                                    🔵 Tecnico - Accesso alle soluzioni
                                </option>
                                <option value="3" {{ old('livello_accesso', $user->livello_accesso) == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale - Gestione malfunzionamenti
                                </option>
                                <option value="4" {{ old('livello_accesso', $user->livello_accesso) == '4' ? 'selected' : '' }}>
                                    🔴 Amministratore - Controllo totale
                                </option>
                            </select>
                            <div class="form-text">Determina le funzionalità accessibili all'utente</div>
                            @error('livello_accesso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- === DATI TECNICO (condizionali) === -->
                        <div id="dati-tecnico" style="display: {{ old('livello_accesso', $user->livello_accesso) == '2' ? 'block' : 'none' }};">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-info mb-3">
                                        <i class="bi bi-tools me-2"></i>Informazioni Tecnico
                                    </h6>
                                </div>
                            </div>
                            
                            <!-- Data Nascita e Specializzazione -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_nascita" class="form-label fw-semibold">
                                        <i class="bi bi-calendar me-1"></i>Data di Nascita
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('data_nascita') is-invalid @enderror" 
                                           id="data_nascita" 
                                           name="data_nascita" 
                                           value="{{ old('data_nascita', $user->data_nascita?->format('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}">
                                    @error('data_nascita')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="specializzazione" class="form-label fw-semibold">
                                        <i class="bi bi-star me-1"></i>Specializzazione
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('specializzazione') is-invalid @enderror" 
                                           id="specializzazione" 
                                           name="specializzazione" 
                                           value="{{ old('specializzazione', $user->specializzazione) }}"
                                           placeholder="es: Elettrodomestici, Climatizzatori"
                                           maxlength="255">
                                    @error('specializzazione')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Centro Assistenza -->
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>Centro di Assistenza
                                </label>
                                <select class="form-select @error('centro_assistenza_id') is-invalid @enderror" 
                                        id="centro_assistenza_id" 
                                        name="centro_assistenza_id">
                                    <option value="">Seleziona centro</option>
                                    @foreach($centri as $centro)
                                        <option value="{{ $centro->id }}" {{ old('centro_assistenza_id', $user->centro_assistenza_id) == $centro->id ? 'selected' : '' }}>
                                            {{ $centro->nome }} - {{ $centro->citta }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Centro di assistenza di appartenenza del tecnico</div>
                                @error('centro_assistenza_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                <button type="submit" class="btn btn-warning" id="updateBtn">
                                    <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR INFORMATIVA === -->
        <div class="col-lg-4">
            
            <!-- Info Utente Corrente -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle text-info me-2"></i>Utente Corrente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-secondary text-white me-3">
                            {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $user->nome_completo }}</h6>
                            <small class="text-muted">{{ $user->username }}</small>
                            <br>
                            <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }}">
                                {{ $user->livello_descrizione }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="small">
                        <p class="mb-2">
                            <strong>Registrato il:</strong> 
                            {{ $user->created_at->format('d/m/Y') }}
                        </p>
                        @if($user->last_login_at)
                            <p class="mb-2">
                                <strong>Ultimo accesso:</strong> 
                                {{ $user->last_login_at->diffForHumans() }}
                            </p>
                        @endif
                        <p class="mb-0">
                            <strong>Ultimo aggiornamento:</strong> 
                            {{ $user->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Guida Livelli Accesso -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check text-primary me-2"></i>Livelli di Accesso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-livello-2 me-2">Tecnico</span>
                            <span>Visualizza soluzioni</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-livello-3 me-2">Staff</span>
                            <span>Gestisce malfunzionamenti</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-livello-4 me-2">Admin</span>
                            <span>Controllo completo</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiche Veloci -->
            @if($user->isStaff())
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up text-success me-2"></i>Statistiche Attuali
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="mb-1">{{ $user->prodottiAssegnati()->count() }}</h5>
                                <small class="text-muted">Prodotti</small>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-1">{{ $user->malfunzionamentiCreati()->count() }}</h5>
                                <small class="text-muted">Soluzioni</small>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($user->isTecnico() && $user->centroAssistenza)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt text-info me-2"></i>Centro Attuale
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-1">{{ $user->centroAssistenza->nome }}</h6>
                        <p class="small text-muted mb-0">{{ $user->centroAssistenza->citta }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Azioni Pericolose -->
            @if($user->id !== auth()->id())
                <div class="card card-custom border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Azioni Pericolose
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-outline-warning btn-sm w-100" 
                                        onclick="return confirm('Resettare la password per {{ $user->nome_completo }}?')">
                                    <i class="bi bi-key me-1"></i>Reset Password
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm w-100" 
                                        onclick="return confirm('ATTENZIONE: Eliminare definitivamente {{ $user->nome_completo }}?\n\nQuesta azione non può essere annullata!')">
                                    <i class="bi bi-trash me-1"></i>Elimina Account
                                </button>
                            </form>
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
                    <i class="bi bi-eye me-2"></i>Anteprima Modifiche
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Contenuto popolato via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-warning" id="updateFromPreview">
                    <i class="bi bi-check-circle me-1"></i>Conferma Modifiche
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

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}

.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
}

.badge-livello {
    font-size: 0.75rem;
}

.badge-livello-4 { background-color: #dc3545; }
.badge-livello-3 { background-color: #ffc107; color: #000; }
.badge-livello-2 { background-color: #0dcaf0; color: #000; }
.badge-livello-1 { background-color: #6c757d; }

/* Preview styling */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #ffc107;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #ffc107;
    margin-bottom: 0.5rem;
}

.highlight-change {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
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
        const original = {
            nome: @json($user->nome),
            cognome: @json($user->cognome),
            username: @json($user->username),
            livello_accesso: @json($user->livello_accesso),
            specializzazione: @json($user->specializzazione),
            data_nascita: @json($user->data_nascita?->format('Y-m-d')),
            centro_assistenza_id: @json($user->centro_assistenza_id)
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
        const originalUsername = @json($user->username);
        
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
</script>
@endpush