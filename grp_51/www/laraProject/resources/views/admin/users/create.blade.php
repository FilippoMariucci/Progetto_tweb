{{-- Vista per creare nuovo utente (Admin - UserController) --}}
@extends('layouts.app')

@section('title', 'Nuovo Utente')

@section('content')
<div class="container-fluid mt-4">
    
    

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
                                        <small class="text-muted">(per utenti tecnici)</small>
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
                                           maxlength="255">
                                    @error('specializzazione')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Centro Assistenza - OPZIONALE -->
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>Centro di Assistenza 
                                    <span class="badge bg-secondary ms-2">Opzionale</span>
                                </label>
                                
                                <select class="form-select @error('centro_assistenza_id') is-invalid @enderror"
                                        id="centro_assistenza_id"
                                        name="centro_assistenza_id">
                                    
                                    <option value="">-- Nessun centro assegnato --</option>
                                    
                                    @forelse($centri as $centro)
                                        <option value="{{ $centro->id }}" 
                                                {{ old('centro_assistenza_id') == $centro->id ? 'selected' : '' }}>
                                            🏢 {{ $centro->nome }} - {{ $centro->citta }} ({{ $centro->provincia }})
                                        </option>
                                    @empty
                                        <option value="" disabled>Nessun centro di assistenza disponibile</option>
                                    @endforelse
                                </select>

                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Centro opzionale:</strong> Può essere assegnato ora o successivamente.
                                </div>

                                @error('centro_assistenza_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                            <li>Centro assistenza opzionale</li>
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
                        </ul>
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
                            <strong>Username:</strong> Usa formato nome.cognome
                        </div>
                        <div class="mb-3">
                            <strong>Password:</strong> Usa il generatore automatico
                        </div>
                        <div class="mb-0">
                            <strong>Tecnici:</strong> Centro assistenza opzionale
                        </div>
                    </div>
                </div>
            </div>
            
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
                <div id="previewContent"></div>
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
/* === STILI PER IL FORM DI CREAZIONE UTENTE === */

/* Card personalizzate */
.card-custom {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-radius: 10px;
}

.card-custom:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Form labels */
.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-label i {
    color: #6c757d;
}

/* Bordi colorati */
.border-start.border-4 {
    border-width: 4px !important;
}

/* Badge */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Form controls */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

/* Input groups */
.input-group .btn {
    border-color: #ced4da;
}

.input-group .btn:hover {
    background-color: #f8f9fa;
    border-color: #86b7fe;
}

/* Alert personalizzati */
.alert {
    border-radius: 8px;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
        text-align: center;
    }
    
    .card-custom {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush