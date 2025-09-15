{{-- 
    Vista Manutenzione Sistema Admin - Stile Compatto
    File: resources/views/admin/manutenzione.blade.php
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista ottimizzata per amministratori con layout compatto,
    gestione cache, ottimizzazione database e monitoraggio sistema
--}}
@extends('layouts.app')

@section('title', 'Manutenzione Sistema - Admin')

@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-tools text-primary me-2"></i>
                Manutenzione Sistema
            </h2>
            <p class="text-muted small mb-0">Gestione cache, database e monitoraggio performance</p>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <button class="btn btn-primary" onclick="aggiornaInfoSistema()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
        </div>
    </div>

    {{-- === MESSAGGI DI FEEDBACK === --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- === INFORMAZIONI SISTEMA COMPATTE === --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-code-square text-primary fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1">Laravel {{ $systemInfo['laravel_version'] ?? 'N/A' }}</h6>
                    <small class="text-muted">Framework</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-server text-success fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1">PHP {{ $systemInfo['php_version'] ?? 'N/A' }}</h6>
                    <small class="text-muted">Server</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-database text-warning fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1">{{ $systemInfo['database_version'] ?? 'MySQL' }}</h6>
                    <small class="text-muted">Database</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-memory text-info fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1" id="memory-display">{{ round(memory_get_usage(true) / 1024 / 1024, 1) }}MB</h6>
                    <small class="text-muted">Memoria</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GESTIONE CACHE E SISTEMA === --}}
    <div class="row g-3 mb-3">
        {{-- Stato Cache - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Stato Cache
                    </h6>
                </div>
                <div class="card-body p-2">
                    @if(isset($cacheStatus))
                        <div class="row g-1 mb-2">
                            @foreach($cacheStatus as $type => $enabled)
                                <div class="col-6">
                                    <div class="d-flex align-items-center p-2 rounded bg-light">
                                        <div class="me-2">
                                            @if($enabled)
                                                <i class="bi bi-check-circle text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger"></i>
                                            @endif
                                        </div>
                                        <small class="fw-semibold">{{ ucfirst($type) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pulsante pulizia totale --}}
                    <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-grid">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Pulire tutte le cache?')">
                            <i class="bi bi-trash me-1"></i>Pulisci Tutto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Azioni Cache Specifiche --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-gear me-1"></i>
                        Cache Specifiche
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        {{-- Config Cache --}}
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="config">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-gear me-1"></i>Config
                                </button>
                            </form>
                        </div>
                        {{-- Route Cache --}}
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="route">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-signpost me-1"></i>Route
                                </button>
                            </form>
                        </div>
                        {{-- View Cache --}}
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="view">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                            </form>
                        </div>
                        {{-- Application Cache --}}
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="application">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-app me-1"></i>App
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monitoraggio Sistema --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Stato Sistema
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div id="system-status" class="mb-2">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <small>Controllo...</small>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                        <label class="form-check-label small" for="auto-refresh">
                            Auto-refresh (30s)
                        </label>
                    </div>

                    <button id="manual-check" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i>Controlla
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- === DATABASE E STORAGE === --}}
    <div class="row g-3 mb-3">
        {{-- Manutenzione Database --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-database me-1"></i>
                        Manutenzione Database
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info alert-sm py-2 mb-3">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Ottimizzazione DB:</strong> Migliora le performance ottimizzando tutte le tabelle.
                        </small>
                    </div>

                    <form method="POST" action="{{ route('admin.manutenzione.optimize-db') }}" class="d-grid mb-3">
                        @csrf
                        <button type="submit" class="btn btn-info" 
                                onclick="return confirm('Avviare ottimizzazione database?')">
                            <i class="bi bi-lightning-charge me-1"></i>
                            Ottimizza Database
                        </button>
                    </form>

                    {{-- Info Database Compatte --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="fw-semibold">Connessione</small>
                                <div><span class="badge bg-success">Attiva</span></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="fw-semibold">Driver</small>
                                <div><code class="small">{{ config('database.default') }}</code></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Storage e Log --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-hdd me-1"></i>
                        Storage e Log
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- Storage Usage --}}
                    @if(isset($systemInfo['storage_usage']) && is_array($systemInfo['storage_usage']))
                        <div class="mb-3">
                            <small class="fw-semibold">Utilizzo Storage</small>
                            @if(isset($systemInfo['storage_usage']['error']))
                                <div class="text-danger small">{{ $systemInfo['storage_usage']['error'] }}</div>
                            @else
                                <div class="progress mt-1" style="height: 15px;">
                                    <div class="progress-bar 
                                        @if(($systemInfo['storage_usage']['percentage'] ?? 0) > 80) bg-danger 
                                        @elseif(($systemInfo['storage_usage']['percentage'] ?? 0) > 60) bg-warning 
                                        @else bg-success @endif" 
                                        style="width: {{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%">
                                        <small>{{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%</small>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $systemInfo['storage_usage']['used'] ?? 'N/A' }} / 
                                    {{ $systemInfo['storage_usage']['total'] ?? 'N/A' }}
                                </small>
                            @endif
                        </div>
                    @endif

                    {{-- File di Log --}}
                    @if(isset($systemInfo['log_files']) && count($systemInfo['log_files']) > 0)
                        <div>
                            <small class="fw-semibold">File di Log ({{ count($systemInfo['log_files']) }})</small>
                            <div class="row g-1 mt-1">
                                @foreach(array_slice($systemInfo['log_files'], 0, 4) as $logFile)
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <small class="fw-semibold d-block">{{ Str::limit($logFile['name'] ?? 'N/A', 15) }}</small>
                                            <small class="text-muted">{{ $logFile['size'] ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 text-center">
                    



                        {{-- Gestione Utenti --}}
                        <div class="col-md-3">
                            <i class="bi bi-people display-6 text-warning mb-2"></i>
                            <h6 class="fw-semibold">Utenti</h6>
                            <small class="text-muted d-block mb-2">Gestione account</small>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-people me-1"></i>Gestisci Utenti
                            </a>
                        </div>

                           {{-- Catalogo Prodotti --}}
                        <div class="col-md-3">
                            <i class="bi bi-box-seam display-6 text-primary mb-2"></i>
                            <h6 class="fw-semibold">Utenti</h6>
                            <small class="text-muted d-block mb-2">Gestione Prodotti</small>
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-box-seam me-1"></i>Gestisci Prodotti
                            </a>
                        </div>

                        {{-- Assegnazioni --}}
                        <div class="col-md-3">
                            <i class="bi bi-person-gear display-6 text-primary mb-2"></i>
                            <h6 class="fw-semibold">Assegnazioni</h6>
                            <small class="text-muted d-block mb-2">Prodotti a staff</small>
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-gear me-1"></i>Assegna Prodotto
                            </a>
                        </div>

                        {{-- Statistiche --}}
                        <div class="col-md-3">
                            <i class="bi bi-graph-up display-6 text-info mb-2"></i>
                            <h6 class="fw-semibold">Statistiche</h6>
                            <small class="text-muted d-block mb-2">Analytics avanzati</small>
                            <a href="{{ route('admin.statistiche.index') }}" class="btn btn-info btn-sm">
                                <i class="bi bi-graph-up me-1"></i>Statistiche
                            </a>
                        </div>
                    </div>



                    {{-- Info Sicurezza --}}
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="small fw-semibold">
                                <i class="bi bi-shield-check me-1 text-success"></i>Sicurezza
                            </h6>
                            <ul class="list-unstyled small mb-0">
                                <li><i class="bi bi-check text-success me-1"></i>Middleware autenticazione attivo</li>
                                <li><i class="bi bi-check text-success me-1"></i>Controlli autorizzazione OK</li>
                                <li><i class="bi bi-check text-success me-1"></i>Log attività tracciati</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="small fw-semibold">
                                <i class="bi bi-info-circle me-1 text-info"></i>Backup
                            </h6>
                            <ul class="list-unstyled small mb-0">
                                <li><i class="bi bi-info text-info me-1"></i>Backup automatico non configurato</li>
                                <li><i class="bi bi-info text-info me-1"></i>Usa export per backup manuali</li>
                                <li><i class="bi bi-info text-info me-1"></i>Log ruotati automaticamente</li>
                            </ul>
                        </div>
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
/* === STILI COMPATTI PER MANUTENZIONE ADMIN === */

/* Layout generale compatto */
.container {
    max-width: 1200px;
}

/* Card più compatte e moderne */
.card {
    border-radius: 12px;
    border: none;
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-body {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Header compatto */
h2 {
    font-size: 1.75rem;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 8px;
}

/* Statistiche sistema header - stile card piccole */
.card-body.py-2 {
    padding: 0.75rem !important;
}

/* Progress bar migliorata */
.progress {
    height: 15px;
    border-radius: 8px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 15px;
}

/* Pulsanti compatti con stati */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Alert compatti */
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.alert {
    border-radius: 8px;
    border: none;
}

/* Badge e status indicators */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35em 0.65em;
    border-radius: 6px;
}

/* Spinner loading states */
.spinner-border-sm {
    width: 0.875rem;
    height: 0.875rem;
    border-width: 0.1em;
}

/* Form controls compatti */
.form-check {
    font-size: 0.875rem;
}

.form-check-input {
    border-radius: 4px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Icone e display */
.display-6 {
    font-size: 2.5rem;
}

.fs-4 {
    font-size: 1.25rem !important;
}

/* Grid system ottimizzato */
.row.g-1 > * {
    padding-right: 0.25rem;
    padding-left: 0.25rem;
    margin-bottom: 0.25rem;
}

.row.g-2 > * {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: 0.5rem;
}

.row.g-3 > * {
    padding-right: 0.75rem;
    padding-left: 0.75rem;
    margin-bottom: 0.75rem;
}

/* Tabelle responsive */
.table-responsive {
    border-radius: 8px;
    font-size: 0.875rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.5rem;
}

.table td {
    padding: 0.5rem;
    vertical-align: middle;
}

/* Code e elementi monospace */
code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

/* Lista compatta */
.list-unstyled li {
    padding: 0.125rem 0;
    font-size: 0.875rem;
}

/* Separatori */
hr {
    margin: 1rem 0;
    opacity: 0.25;
}

/* Toast notifications */
.toast-notification {
    border-radius: 8px;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-width: 300px !important;
}

/* System status specifico */
#system-status {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Responsive miglioramenti */
@media (max-width: 992px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    /* Header responsive */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .btn-group {
        margin-top: 0.5rem;
    }
    
    /* Card più compatte su mobile */
    .card-body.p-2 {
        padding: 0.5rem !important;
    }
    
    .card-body.p-3 {
        padding: 0.75rem !important;
    }
    
    /* Font sizes ridotti */
    h2 {
        font-size: 1.5rem;
    }
    
    .card-header h6 {
        font-size: 0.875rem;
    }
    
    /* Grid mobile */
    .col-lg-3,
    .col-lg-4,
    .col-lg-6 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    /* Layout super compatto per small screens */
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .card {
        margin-bottom: 0.75rem;
    }
    
    .btn.w-100 {
        margin-bottom: 0.25rem;
    }
    
    .small, small {
        font-size: 0.75rem !important;
    }
    
    /* Riduci padding generale */
    .row.g-3 {
        --bs-gutter-x: 0.5rem;
        --bs-gutter-y: 0.5rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .bg-light {
        background-color: #212529 !important;
        color: #fff;
    }
    
    .text-muted {
        color: #adb5bd !important;
    }
    
    .card {
        background-color: #2d3748;
        color: #fff;
    }
}

/* High contrast support */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000;
    }
    
    .btn {
        border-width: 2px;
    }
    
    .badge {
        border: 1px solid;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    * {
        transition: none !important;
    }
    
    .spinner-border {
        animation: none !important;
    }
}

/* Print styles */
@media print {
    .btn,
    .alert,
    .toast-notification,
    #system-status,
    .form-check {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }
    
    .container {
        max-width: none;
        padding: 0;
    }
    
    h2 {
        page-break-after: avoid;
    }
}

/* Animazioni personalizzate */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.3s ease-out;
}

/* Stati di caricamento */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading .card {
    position: relative;
}

.loading .card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    z-index: 10;
}

/* Focus states per accessibilità */
.btn:focus,
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

/* Custom scrollbar */
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

/* Utility classes */
.fw-semibold {
    font-weight: 600;
}

.rounded-lg {
    border-radius: 12px;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Performance optimizations */
.card,
.btn {
    will-change: transform;
}

/* Ensure proper stacking */
.toast-notification {
    z-index: 1060;
}

.modal {
    z-index: 1050;
}

/* Custom properties for theming */
:root {
    --admin-primary: #0d6efd;
    --admin-success: #198754;
    --admin-warning: #ffc107;
    --admin-danger: #dc3545;
    --admin-info: #0dcaf0;
    --admin-secondary: #6c757d;
    --admin-border-radius: 12px;
    --admin-transition: all 0.2s ease-in-out;
}
</style>
@endpush