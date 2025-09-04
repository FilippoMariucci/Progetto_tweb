{{-- 
    Vista specifica per la dashboard amministratore
    File: resources/views/admin/dashboard.blade.php
    
    Questa vista mostra il pannello di controllo completo per gli amministratori (livello 4)
    Include statistiche, gestione utenti, prodotti, centri assistenza e funzionalità avanzate
    RESTYLING: Stile moderno compatto come tecnico e staff
--}}
@extends('layouts.app')

@section('title', 'Dashboard Amministratore')

@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-shield-check text-danger me-2"></i>
                Pannello Amministratore
            </h2>
            <p class="text-muted small mb-0">{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Admin' }} - Controllo completo sistema</p>
        </div>
        <div class="text-end">
            <div class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                <i class="bi bi-person-fill-gear me-1"></i>
                Amministratore
            </div>
            <div class="small text-muted mt-1">
                Ultimo accesso: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- Alert di benvenuto compatto --}}
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-fill-gear fs-4"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="alert-heading mb-1">Benvenuto, {{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Admin' }}!</h5>
                <p class="mb-0">
                    <strong>Amministratore Sistema</strong> - Accesso completo a gestione utenti, prodotti e configurazioni
                </p>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE - STILE UNIFORME === --}}
    <div class="row mb-3 g-2">
        {{-- Card Utenti Totali --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-people text-danger fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-danger">{{ $stats['total_utenti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Utenti Totali</small>
                    <small class="badge bg-danger bg-opacity-10 text-danger mt-1">
                        Registrati
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Prodotti --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-primary">{{ $stats['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Prodotti</small>
                    <small class="badge bg-primary bg-opacity-10 text-primary mt-1">
                        Nel Catalogo
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Centri --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-geo-alt text-info fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-info">{{ $stats['total_centri'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Centri Assistenza</small>
                    <small class="badge bg-info bg-opacity-10 text-info mt-1">
                        Attivi
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Soluzioni --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-tools text-success fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-success">{{ $stats['total_soluzioni'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Soluzioni</small>
                    <small class="badge bg-success bg-opacity-10 text-success mt-1">
                        Disponibili
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- === GESTIONE PRINCIPALE === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <h4 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Gestione Sistema
                    </h4>
                    <small class="opacity-75">Funzionalità amministrative principali</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        {{-- Gestione utenti --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-people display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Utenti</span>
                            </a>
                        </div>
                        
                        {{-- Gestione prodotti --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-box-seam display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Gestione centri --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.centri.index') }}" class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Assegnazione prodotti --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none text-dark shadow-sm">
                                <i class="bi bi-person-gear display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Assegna Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche avanzate --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.statistiche.index') }}" class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Statistiche</span>
                            </a>
                        </div>
                        
                        {{-- Backup/Manutenzione --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.manutenzione.index') }}" class="btn btn-secondary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-tools display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Manutenzione</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === SEZIONE UTENTI E PRODOTTI === --}}
    <div class="row mb-4 g-3">
        {{-- Utenti Recenti --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-plus me-1"></i>
                        Utenti Recenti
                    </h6>
                </div>
                <div class="card-body p-3">
                    @if(isset($stats['utenti_recenti']) && $stats['utenti_recenti']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['utenti_recenti']->take(4) as $utente)
                                <div class="list-group-item px-0 border-0 border-bottom py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold small">{{ $utente->nome_completo ?? $utente->username }}</h6>
                                            <small class="text-muted">{{ $utente->username }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge badge-livello badge-livello-{{ $utente->livello_accesso }}">
                                                Livello {{ $utente->livello_accesso }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-list me-1"></i>Tutti gli Utenti
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-person display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0 small">Nessun utente recente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Prodotti Non Assegnati --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Prodotti Non Assegnati
                        @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                            <span class="badge bg-danger ms-2">{{ $stats['prodotti_non_assegnati_count'] }}</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body p-3" id="prodotti-non-assegnati-container">
                    @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                        <div class="alert alert-warning py-2 mb-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>{{ $stats['prodotti_non_assegnati_count'] }} prodotti</strong> senza staff
                        </div>

                        @if(isset($stats['prodotti_non_assegnati']) && $stats['prodotti_non_assegnati']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($stats['prodotti_non_assegnati']->take(3) as $prodotto)
                                    <div class="list-group-item px-0 border-0 border-bottom py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold small">{{ $prodotto->nome }}</h6>
                                                <small class="text-muted">{{ $prodotto->categoria ?? 'N/A' }}</small>
                                            </div>
                                            <div class="text-end">
                                                <a href="{{ route('admin.assegnazioni.index') }}?prodotto={{ $prodotto->id }}" 
                                                   class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-person-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="text-center mt-2">
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-gear me-1"></i>Gestisci Assegnazioni
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle display-4 text-success opacity-75"></i>
                            <p class="text-success mt-2 mb-0 small">Tutti i prodotti assegnati</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === SEZIONI COMPATTE LATERALI === --}}
    <div class="row mb-4 g-3">
        {{-- Distribuzione Utenti --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Distribuzione Utenti
                    </h6>
                </div>
                <div class="card-body p-3">
                    @if(isset($stats['distribuzione_utenti']) && count($stats['distribuzione_utenti']) > 0)
                        <div class="row g-2 text-center">
                            @foreach($stats['distribuzione_utenti'] as $livello => $count)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light small">
                                        <span class="fw-semibold">
                                            @switch($livello)
                                                @case(2) Tecnici @break
                                                @case(3) Staff @break
                                                @case(4) Admin @break
                                                @default Utenti @break
                                            @endswitch
                                        </span>
                                        <span class="badge badge-livello badge-livello-{{ $livello }}">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-pie-chart display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0 small">Dati non disponibili</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stato Sistema --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-cpu me-1"></i>
                        Stato Sistema
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>Database</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>Laravel</span>
                            <span class="badge bg-info">v{{ app()->version() }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>PHP</span>
                            <span class="badge bg-info">v{{ PHP_VERSION }}</span>
                        </div>
                        @if(isset($stats['ultimo_backup']))
                            <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                                <span>Ultimo Backup</span>
                                <span class="badge bg-warning text-dark">{{ $stats['ultimo_backup'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Azioni Rapide --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                        <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Nuovo Prodotto
                        </a>
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-geo-alt-fill me-1"></i>Nuovo Centro
                        </a>
                        <div class="border-top pt-2 mt-2">
                            <a href="{{ route('admin.export.index') }}" class="btn btn-success btn-sm w-100 mb-1">
                                <i class="bi bi-download me-1"></i>Esporta Dati
                            </a>
                            <a href="{{ route('admin.manutenzione.index') }}" class="btn btn-warning btn-sm w-100">
                                <i class="bi bi-gear me-1"></i>Manutenzione
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === LINK DASHBOARD ALTERNATIVE === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">
                        <i class="bi bi-grid text-secondary me-2"></i>
                        Visualizzazioni Alternative
                    </h6>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Dashboard Generale
                        </a>
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box me-1"></i>Vista Pubblica
                        </a>
                        <a href="{{ route('prodotti.completo.index') }}?view=tech" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-tools me-1"></i>Vista Tecnico
                        </a>
                        <a href="{{ route('documentazione') }}" class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Documentazione
                        </a>
                        <button id="manual-refresh-btn" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            Ultimo aggiornamento: <span id="last-update-time">{{ now()->format('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    Script JavaScript CORRETTO per gestione dinamica della dashboard
    FIX PRINCIPALE: URL corretti per le chiamate API admin
--}}
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


{{-- 
    Stili CSS personalizzati per la dashboard admin
    Questi stili migliorano l'aspetto visivo e l'usabilità
--}}
<style>
/* === STILI UNIFORMI COME TECNICO/STAFF === */

/* Card uniformi con design moderno */
.card {
    border-radius: 12px;
    border: none !important;
    overflow: hidden;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    font-size: 0.9rem;
}

.card-body {
    font-size: 0.9rem;
}

/* Stili per i badge dei livelli utente */
.badge-livello {
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-livello-1 { background-color: #6c757d !important; }
.badge-livello-2 { background-color: #0dcaf0 !important; }
.badge-livello-3 { background-color: #ffc107 !important; color: #000 !important; }
.badge-livello-4 { background-color: #dc3545 !important; }

/* Effetti hover per i pulsanti di gestione */
.btn-lg:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Stili per gli indicatori di stato */
.badge-success { background-color: #198754 !important; }
.badge-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge-danger { background-color: #dc3545 !important; }
.badge-info { background-color: #0dcaf0 !important; }

/* Animazioni per gli aggiornamenti */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.updating {
    animation: pulse 1s infinite;
}

/* Avatar per header */
.avatar {
    width: 50px;
    height: 50px;
}

/* Gradients per header */
.bg-gradient {
    background-size: 200% 200%;
    animation: gradient-shift 10s ease infinite;
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Hover effects per card */
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

/* Badge più piccoli */
.badge {
    font-size: 0.7rem;
}

/* Responsive design per schermi piccoli */
@media (max-width: 768px) {
    .col-lg-2, .col-lg-4, .col-lg-6 {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .display-6 {
        font-size: 2rem !important;
    }
    
    .avatar {
        width: 40px;
        height: 40px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: start !important;
    }
    
    .text-end {
        margin-top: 0.5rem;
    }
}

@media (max-width: 576px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .col-lg-2 {
        flex: 0 0 auto;
        width: 50%; /* Due pulsanti per riga su mobile */
    }
    
    .small {
        font-size: 0.75rem !important;
    }
}

/* Miglioramenti per l'accessibilità */
.btn:focus,
.list-group-item:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 0.125em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Toast personalizzati */
.toast {
    min-width: 300px;
}

/* Stili per le liste di elementi */
.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Miglioramenti tipografici */
.fw-semibold {
    font-weight: 600;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Effetti di transizione fluidi */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}

/* Colori personalizzati uniformi */
.text-muted {
    color: #6c757d !important;
}

/* Scrollbar personalizzata */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1e1e1e;
        color: #ffffff;
    }
    
    .bg-light {
        background-color: #2d2d2d !important;
        color: #ffffff;
    }
    
    .text-muted {
        color: #a0a0a0 !important;
    }
}
</style>
