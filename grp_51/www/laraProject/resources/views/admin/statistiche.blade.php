{{--
    ===================================================================
    STATISTICHE ADMIN - Vista Blade Corretta
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/admin/statistiche.blade.php
    
    FUNZIONALITÀ:
    - Layout compatto per amministratori
    - Grafici Chart.js integrati
    - Statistiche in tempo reale
    - Design responsive
    ===================================================================
--}}

@extends('layouts.app')

@section('title', 'Statistiche Sistema - Admin')

@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-success me-2"></i>
                Statistiche Sistema
            </h2>
            <p class="text-muted small mb-0">Analisi dettagliate del sistema</p>
            <small class="text-muted">Periodo: ultimi {{ $periodo ?? 30 }} giorni</small>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- Controlli periodo --}}
            <a href="{{ route('admin.statistiche.index', ['periodo' => 7]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 7 ? 'active' : '' }}">7g</a>
            <a href="{{ route('admin.statistiche.index', ['periodo' => 30]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 30 ? 'active' : '' }}">30g</a>
            <a href="{{ route('admin.statistiche.index', ['periodo' => 90]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 90 ? 'active' : '' }}">90g</a>
            {{-- Azioni --}}
            <button class="btn btn-primary" onclick="aggiornaStatistiche(event)">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE === --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-people text-primary fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['utenti_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Utenti Totali</small>
                    @if(isset($stats['nuovi_prodotti']) && $stats['nuovi_prodotti'] > 0)
                        <br><small class="text-info">+{{ $stats['nuovi_prodotti'] }} nuovi</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-wrench text-warning fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni</small>
                    @if(isset($stats['nuove_soluzioni']) && $stats['nuove_soluzioni'] > 0)
                        <br><small class="text-warning">+{{ $stats['nuove_soluzioni'] }} nuove</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-geo-alt text-success fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['centri_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Centri</small>
                    @if(isset($stats['utenti_attivi']) && $stats['utenti_attivi'] > 0)
                        <br><small class="text-success">{{ $stats['utenti_attivi'] }} attivi</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI === --}}
    <div class="row g-3 mb-3">
        {{-- Grafico Utenti per Livello - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Utenti per Livello
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoUtenti" height="120"></canvas>
                    {{-- Legenda compatta --}}
                    <div class="row g-1 mt-2">
                        @if(isset($distribuzioneUtenti) && count($distribuzioneUtenti) > 0)
                            @foreach($distribuzioneUtenti as $livello => $count)
                                <div class="col-6 small text-center">
                                    <span class="badge badge-livello-{{ $livello }}">{{ $count }}</span>
                                    @switch($livello)
                                        @case('1') Pubblico @break
                                        @case('2') Tecnici @break
                                        @case('3') Staff @break
                                        @case('4') Admin @break
                                        @default Livello {{ $livello }}
                                    @endswitch
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafico Prodotti per Categoria - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-1"></i>
                        Prodotti per Categoria
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoProdotti" height="120"></canvas>
                    {{-- Dettagli compatti --}}
                    @if(isset($prodottiPerCategoria) && count($prodottiPerCategoria) > 0)
                        <div class="row g-1 mt-2">
                            @foreach(array_slice($prodottiPerCategoria, 0, 4, true) as $categoria => $count)
                                <div class="col-6 small text-center">
                                    <span class="badge bg-info">{{ $count }}</span>
                                    {{ ucfirst($categoria) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grafico Gravità - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoGravita" height="120"></canvas>
                    {{-- Dettagli gravità --}}
                    @if(isset($malfunzionamentiPerGravita) && count($malfunzionamentiPerGravita) > 0)
                        <div class="row g-1 mt-2">
                            @foreach($malfunzionamentiPerGravita as $gravita => $count)
                                <div class="col-6 small text-center">
                                    @switch($gravita)
                                        @case('critica')
                                            <span class="badge bg-danger">{{ $count }}</span> Critica
                                            @break
                                        @case('alta')
                                            <span class="badge bg-warning text-dark">{{ $count }}</span> Alta
                                            @break
                                        @case('media')
                                            <span class="badge bg-info">{{ $count }}</span> Media
                                            @break
                                        @case('bassa')
                                            <span class="badge bg-success">{{ $count }}</span> Bassa
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $count }}</span> {{ ucfirst($gravita) }}
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ANDAMENTO CRESCITA COMPATTO RIMOSSO === --}}
    {{-- Sezione crescita rimossa per richiesta utente --}}

    {{-- === SEZIONE DETTAGLI LINEARI === --}}
    <div class="row g-3 mb-3">
        {{-- Prodotti Problematici --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Prodotti con Più Problemi
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2 text-center">Problemi</th>
                                    <th class="py-2 text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($prodottiProblematici) && $prodottiProblematici->count() > 0)
                                    @foreach($prodottiProblematici->take(6) as $prodotto)
                                        <tr class="small">
                                            <td class="py-2">
                                                <strong>{{ $prodotto->nome }}</strong>
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                @if($prodotto->categoria)
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($prodotto->categoria) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="py-2 text-center">
                                                <span class="badge bg-warning text-dark">
                                                    {{ $prodotto->malfunzionamenti_count }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-end">
                                                <a href="{{ route('admin.prodotti.show', $prodotto->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3 small">
                                            Nessun prodotto problematico
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Staff Attivi --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-people me-1"></i>
                        Staff Attivi
                    </h6>
                </div>
                <div class="card-body p-2">
                    @if(isset($staffAttivi) && $staffAttivi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($staffAttivi->take(5) as $staff)
                                <div class="list-group-item px-0 border-0 py-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-1">
                                            <h6 class="mb-1 fw-semibold small text-truncate">
                                                {{ $staff->nome_completo }}
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $staff->username }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">
                                                {{ $staff->malfunzionamenti_creati_count }}
                                            </span>
                                            @if(isset($staff->prodotti_assegnati_count))
                                                <br><span class="badge bg-success mt-1">
                                                    {{ $staff->prodotti_assegnati_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-2">
                            <a href="{{ route('admin.users.index') }}" 
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-list me-1"></i>Vedi Tutti
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-person-plus display-6 text-muted opacity-50"></i>
                            <p class="text-muted small mt-2 mb-0">Nessun staff attivo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === SEZIONE DETTAGLI LINEARI === --}}
    <div class="row g-3 mb-3">
        

       
    </div>

    {{-- === METRICHE AGGIUNTIVE COMPATTE === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Metriche Sistema
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        {{-- Soluzioni Aggiornate --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-arrow-clockwise text-primary fs-4"></i>
                                <div class="h5 text-primary mb-0 mt-1">
                                    {{ $stats['soluzioni_aggiornate'] ?? 0 }}
                                </div>
                                <small class="text-muted">Aggiornate</small>
                            </div>
                        </div>

                        {{-- Periodo --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-calendar3 text-info fs-4"></i>
                                <div class="h5 text-info mb-0 mt-1">
                                    {{ $periodo ?? 30 }}
                                </div>
                                <small class="text-muted">Giorni</small>
                            </div>
                        </div>

                        {{-- Ultimo Aggiornamento --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-clock text-success fs-4"></i>
                                <div class="h5 text-success mb-0 mt-1" id="last-update">
                                    {{ now()->format('H:i') }}
                                </div>
                                <small class="text-muted">Ultimo Update</small>
                            </div>
                        </div>

                        {{-- Stato Sistema --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-database text-warning fs-4"></i>
                                <div class="h5 text-warning mb-0 mt-1">
                                    <span class="badge bg-success">OK</span>
                                </div>
                                <small class="text-muted">Sistema</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- === SCRIPTS SECTION === --}}
@push('scripts')
<!-- Chart.js per i grafici -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// ===================================================================
// PASSAGGIO DATI DAL CONTROLLER PHP AL JAVASCRIPT
// ===================================================================
// Questo è il punto cruciale: i dati devono essere passati correttamente
// dal controller Laravel al JavaScript attraverso la vista Blade

console.log('📊 Inizializzazione dati statistiche...');

// Passa i dati dal controller PHP alle variabili JavaScript globali
// Questi dati vengono poi utilizzati dai grafici Chart.js
window.distribuzioneUtenti = @json($distribuzioneUtenti ?? []);
window.prodottiPerCategoria = @json($prodottiPerCategoria ?? []);
window.malfunzionamentiPerGravita = @json($malfunzionamentiPerGravita ?? []);
window.crescitaUtenti = @json($crescitaUtenti ?? []);
window.crescitaSoluzioni = @json($crescitaSoluzioni ?? []);

// Debug: mostra i dati ricevuti dal controller
console.log('🔍 Dati ricevuti dal controller:', {
    distribuzioneUtenti: window.distribuzioneUtenti,
    prodottiPerCategoria: window.prodottiPerCategoria,
    malfunzionamentiPerGravita: window.malfunzionamentiPerGravita,
    crescitaUtenti: window.crescitaUtenti,
    crescitaSoluzioni: window.crescitaSoluzioni
});

// Verifica che i dati non siano vuoti
if (Object.keys(window.distribuzioneUtenti).length === 0) {
    console.warn('⚠️ distribuzioneUtenti è vuoto - controllare il controller');
}
if (Object.keys(window.prodottiPerCategoria).length === 0) {
    console.warn('⚠️ prodottiPerCategoria è vuoto - controllare il controller');
}
if (Object.keys(window.malfunzionamentiPerGravita).length === 0) {
    console.warn('⚠️ malfunzionamentiPerGravita è vuoto - controllare il controller');
}

// Imposta il route corrente per il JavaScript
window.LaravelApp = window.LaravelApp || {};
window.LaravelApp.route = 'admin.statistiche';

console.log('✅ Dati passati correttamente al JavaScript');
</script>

<!-- Carica il file JavaScript delle statistiche -->
<script src="{{ asset('js/admin/statistiche.js') }}"></script>
@endpush

{{-- === STYLES SECTION === --}}
@push('styles')
<style>
/* ===================================================================
   STILI COMPATTI PER STATISTICHE ADMIN
   =================================================================== */

/* Badge personalizzati per livelli utente */
.badge-livello-1 { 
    background-color: #6c757d !important; 
    color: white !important; 
}
.badge-livello-2 { 
    background-color: #0dcaf0 !important; 
    color: white !important; 
}
.badge-livello-3 { 
    background-color: #ffc107 !important; 
    color: #000 !important; 
}
.badge-livello-4 { 
    background-color: #dc3545 !important; 
    color: white !important; 
}

/* Card con bordi arrotondati e ombre leggere */
.card {
    border-radius: 8px;
    border: none !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/* Header delle card più compatti */
.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
    font-weight: 600;
}

.card-body {
    font-size: 0.9rem;
}

/* Tabelle più compatte per layout responsivo */
.table-sm td, .table-sm th {
    padding: 0.4rem;
    font-size: 0.85rem;
    vertical-align: middle;
}

/* Grafici responsive con altezza fissa */
canvas {
    max-height: 120px !important;
}

/* Badge più piccoli e colorati */
.badge {
    font-size: 0.7rem;
    border-radius: 6px;
}

/* Bottoni gruppo più compatti */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

/* Liste compatte */
.list-group-item {
    font-size: 0.85rem;
    border-radius: 4px !important;
}

/* Progress bar con animazioni */
.progress {
    border-radius: 6px;
    height: 8px;
}

.progress-bar {
    transition: width 0.6s ease;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .col-lg-4, .col-lg-8 {
        margin-bottom: 0.5rem;
    }
    
    .btn-group-sm {
        flex-wrap: wrap;
    }
    
    .btn-group-sm .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    /* Header responsive */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .btn-group {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .small {
        font-size: 0.75rem !important;
    }
    
    .h5 {
        font-size: 1.1rem !important;
    }
    
    .fs-4 {
        font-size: 1.2rem !important;
    }
    
    /* Container più stretto su mobile */
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* === ANIMAZIONI E TRANSIZIONI === */
.card, .btn, .badge, .alert {
    transition: all 0.2s ease-in-out;
}

/* Effetti hover */
.btn:hover {
    transform: translateY(-1px);
}

.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(13, 110, 253, 0.05);
    transform: scale(1.001);
}

/* Spinner personalizzato */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === UTILITÀ === */
.text-muted {
    color: #6c757d !important;
}

.fw-semibold {
    font-weight: 600;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

/* Focus per accessibilità */
.btn:focus-visible, 
.form-control:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Scrollbar personalizzata */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === COLORI TEMA === */
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.card-header.bg-info {
    background-color: #0dcaf0 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.card-header.bg-success {
    background-color: #198754 !important;
}

.card-header.bg-danger {
    background-color: #dc3545 !important;
}

.card-header.bg-secondary {
    background-color: #6c757d !important;
}

.card-header.bg-dark {
    background-color: #212529 !important;
}

/* === OTTIMIZZAZIONI PRESTAZIONI === */
* {
    box-sizing: border-box;
}

/* Migliora le performance di rendering */
.card, canvas, .table {
    contain: layout style;
}

/* === STAMPA === */
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
        box-shadow: none !important;
    }
    
    .card-header {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}
</style>
@endpush