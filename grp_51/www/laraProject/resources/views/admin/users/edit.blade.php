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