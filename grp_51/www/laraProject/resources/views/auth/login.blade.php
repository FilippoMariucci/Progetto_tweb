{{--
    ===================================================================
    PAGINA LOGIN - Vista Blade Corretta
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/auth/login.blade.php
    
    FUNZIONALITÀ:
    - Form di login con validazione
    - Credenziali di test per sviluppo
    - Informazioni sui livelli di accesso
    - Interfaccia responsive e accessibile
    ===================================================================
--}}

@extends('layouts.app')

@section('title', 'Accedi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            {{-- === CARD LOGIN === --}}
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
                    
                    {{-- Form di login --}}
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        
                        {{-- Campo Username --}}
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

                        {{-- Campo Password --}}
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

                        {{-- Ricordami --}}
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

                        {{-- Pulsante Login --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Accedi
                            </button>
                        </div>
                    </form>
                    
                </div>
                
                {{-- Footer della card --}}
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Accesso sicuro protetto da SSL
                    </small>
                </div>
            </div>
            
            {{-- === INFORMAZIONI LIVELLI DI ACCESSO === --}}
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

            {{-- === CREDENZIALI DI TEST === --}}
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

            {{-- === LINK PUBBLICI === --}}
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

@push('styles')
<style>
/* === STILI PERSONALIZZATI LOGIN === */

.card-custom {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}

.form-control-lg {
    border-radius: 10px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    transform: translateY(-1px);
}

.btn-lg {
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
    border-radius: 8px;
}

.fill-credentials {
    transition: all 0.2s ease;
}

.fill-credentials:hover {
    transform: scale(1.02);
}

.border-success {
    border-color: #198754 !important;
    animation: pulse-success 1s ease-in-out;
}

@keyframes pulse-success {
    0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
    100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.75) !important;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 2rem 1.5rem;
    }
    
    .col-md-6 {
        margin-bottom: 2rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #2d3748;
        color: #fff;
    }
    
    .card-footer {
        background-color: #1a202c !important;
    }
}

/* Accessibility */
.form-control:focus,
.btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Animation on load */
.card-custom {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush