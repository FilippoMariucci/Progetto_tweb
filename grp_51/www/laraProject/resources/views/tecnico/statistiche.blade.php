{{--
    ===================================================================
    STATISTICHE TECNICO - Vista Blade Corretta
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/tecnico/statistiche.blade.php
    
    FUNZIONALITÀ:
    - Layout compatto per tecnici
    - Grafici personalizzati per performance
    - Statistiche personali e di centro
    - Integrazione JavaScript pulita
    ===================================================================
--}}

@extends('layouts.app')

@section('title', 'Le mie Statistiche - Tecnico')

@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-primary me-2"></i>
                Le mie Statistiche
            </h2>
            <p class="text-muted small mb-0">{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Tecnico' }}</p>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <button class="btn btn-primary" onclick="aggiornaStatistiche()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE === --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-box text-primary fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['generale']['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted">Prodotti Totali</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-tools text-success fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti']['totali'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti']['critici'] ?? 0 }}</h5>
                    <small class="text-muted">Critici</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-geo-alt text-info fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['generale']['total_centri'] ?? 0 }}</h5>
                    <small class="text-muted">Centri</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI === --}}
    <div class="row g-3 mb-3">
        {{-- Grafico Gravità - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoGravita" height="120"></canvas>
                    {{-- Legenda compatta --}}
                    @if(isset($stats['malfunzionamenti']['per_gravita']) && count($stats['malfunzionamenti']['per_gravita']) > 0)
                        <div class="row g-1 mt-2">
                            @foreach($stats['malfunzionamenti']['per_gravita'] as $gravita => $count)
                                <div class="col-6 small text-center">
                                    @switch($gravita)
                                        @case('critica')
                                            <span class="badge bg-danger">{{ $count }}</span> Critica
                                            @break
                                        @case('alta')
                                            <span class="badge bg-warning text-dark">{{ $count }}</span> Alta
                                            @break
                                        @case('media')
                                            <span class="badge bg-success">{{ $count }}</span> Media
                                            @break
                                        @case('bassa')
                                            <span class="badge bg-info">{{ $count }}</span> Bassa
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

        {{-- Grafico Trend - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up me-1"></i>
                        Ultimi 7 Giorni
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoTrend" height="120"></canvas>
                    {{-- Info trend --}}
                    @if(isset($stats['trend_settimanale']) && isset($stats['trend_settimanale']['totale_settimana']))
                        <div class="text-center mt-2">
                            <small class="text-success fw-semibold">
                                Totale settimana: {{ $stats['trend_settimanale']['totale_settimana'] }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grafico Categorie - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-1"></i>
                        Per Categoria
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="graficoCategorie" height="120"></canvas>
                    {{-- Dettagli categorie --}}
                    @if(isset($stats['per_categoria']) && count($stats['per_categoria']) > 0)
                        <div class="row g-1 mt-2">
                            @foreach(array_slice($stats['per_categoria'], 0, 4, true) as $categoria => $count)
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
    </div>

    {{-- === INFORMAZIONI LINEARI === --}}
    <div class="row g-3 mb-3">
        {{-- Info Centro - Compatto --}}
        @if(isset($stats['centro_assistenza']) && $stats['centro_assistenza'])
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-building me-1"></i>
                        Il mio Centro
                    </h6>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1">{{ $stats['centro_assistenza']['nome'] ?? 'N/A' }}</h6>
                    <p class="small mb-1">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $stats['centro_assistenza']['indirizzo'] ?? 'N/A' }}
                    </p>
                    <p class="small mb-1">
                        <i class="bi bi-house me-1"></i>
                        {{ $stats['centro_assistenza']['citta'] ?? 'N/A' }} 
                        @if(isset($stats['centro_assistenza']['provincia']))
                            ({{ $stats['centro_assistenza']['provincia'] }})
                        @endif
                    </p>
                    @if(isset($stats['centro_assistenza']['telefono']) && $stats['centro_assistenza']['telefono'])
                    <p class="small mb-0">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $stats['centro_assistenza']['telefono'] }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Info Personali - Compatto --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-gear me-1"></i>
                        Profilo Tecnico
                    </h6>
                </div>
                <div class="card-body p-3">
                    <p class="small mb-1"><strong>Nome:</strong> {{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'N/A' }}</p>
                    @if(isset(auth()->user()->specializzazione) && auth()->user()->specializzazione)
                    <p class="small mb-1"><strong>Specializzazione:</strong> {{ auth()->user()->specializzazione }}</p>
                    @endif
                    <p class="small mb-1"><strong>Livello:</strong> Tecnico (Livello 2)</p>
                    <p class="small mb-0"><strong>Attivo dal:</strong> {{ $stats['personali']['data_registrazione']->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- === TABELLA COMPATTA PROBLEMI CRITICI === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-octagon me-1"></i>
                        Problemi Critici Recenti
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Problema</th>
                                    <th class="py-2">Gravità</th>
                                    <th class="py-2">Segnalazioni</th>
                                    <th class="py-2">Data</th>
                                    <th class="py-2">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($stats['critici_recenti']) && $stats['critici_recenti']->count() > 0)
                                    @foreach($stats['critici_recenti']->take(5) as $malfunzionamento)
                                    <tr class="small">
                                        <td class="py-2">
                                            <strong>{{ $malfunzionamento->prodotto->nome ?? 'N/D' }}</strong>
                                            @if(isset($malfunzionamento->prodotto->modello) && $malfunzionamento->prodotto->modello)
                                                <br><small class="text-muted">{{ $malfunzionamento->prodotto->modello }}</small>
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <span class="fw-semibold">{{ Str::limit($malfunzionamento->titolo, 30) }}</span>
                                            <br><small class="text-muted">{{ Str::limit($malfunzionamento->descrizione, 40) }}</small>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-danger">{{ ucfirst($malfunzionamento->gravita) }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-warning text-dark">{{ $malfunzionamento->numero_segnalazioni ?? 0 }}</span>
                                        </td>
                                        <td class="py-2">{{ $malfunzionamento->created_at->format('d/m') }}</td>
                                        <td class="py-2">
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto_id, $malfunzionamento->id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3 small">
                                            Nessun problema critico recente
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ===================================================================
// PASSAGGIO DATI DAL CONTROLLER PHP AL JAVASCRIPT
// ===================================================================
// Dati delle statistiche passati dal controller al JavaScript

console.log('Inizializzazione dati statistiche tecnico...');

// Passa i dati statistiche al JavaScript
window.statsData = {
    // Dati malfunzionamenti per gravità
    malfunzionamenti: {
        per_gravita: @json($stats['malfunzionamenti']['per_gravita'] ?? []),
        totali: {{ $stats['malfunzionamenti']['totali'] ?? 0 }},
        critici: {{ $stats['malfunzionamenti']['critici'] ?? 0 }}
    },
    
    // Trend settimanale
    trend_settimanale: {
        giorni: @json($stats['trend_settimanale']['giorni'] ?? []),
        conteggi: @json($stats['trend_settimanale']['conteggi'] ?? []),
        totale_settimana: {{ $stats['trend_settimanale']['totale_settimana'] ?? 0 }}
    },
    
    // Categorie prodotti
    per_categoria: @json($stats['per_categoria'] ?? []),
    
    // Dati generali
    generale: {
        total_prodotti: {{ $stats['generale']['total_prodotti'] ?? 0 }},
        total_centri: {{ $stats['generale']['total_centri'] ?? 0 }}
    },
    
    // Info centro assistenza
    centro_assistenza: @json($stats['centro_assistenza'] ?? null),
    
    // Dati personali
    personali: {
        nome: '{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Tecnico' }}',
        specializzazione: '{{ auth()->user()->specializzazione ?? 'N/A' }}',
        data_registrazione: '{{ $stats['personali']['data_registrazione']->format('Y-m-d') ?? '' }}'
    }
};

// Debug: mostra i dati ricevuti
console.log('Dati statistiche tecnico ricevuti:', {
    malfunzionamenti_per_gravita: window.statsData.malfunzionamenti.per_gravita,
    trend_giorni: window.statsData.trend_settimanale.giorni,
    categorie: window.statsData.per_categoria
});

// Imposta il route corrente
window.LaravelApp = window.LaravelApp || {};
window.LaravelApp.route = 'tecnico.statistiche.view';

console.log('Dati statistiche tecnico inizializzati correttamente');
</script>

@endpush

{{-- === STYLES SECTION === --}}
@push('styles')
<style>
/* ===================================================================
   STILI COMPATTI PER STATISTICHE TECNICO
   =================================================================== */

/* Layout generale compatto */
.container {
    max-width: 1200px;
}

/* Card più compatte e moderne */
.card {
    border-radius: 8px;
    border: none !important;
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/* Header delle card */
.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
    font-weight: 600;
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
    border-radius: 6px;
}

/* Statistiche header - card piccole */
.card-body.py-2 {
    padding: 0.75rem !important;
}

/* Tabelle più compatte */
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
    border-radius: 4px;
    font-weight: 600;
}

/* Pulsanti compatti */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 992px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    canvas {
        max-height: 100px !important;
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
    
    /* Table responsive */
    .table-responsive {
        font-size: 0.8rem;
    }
    
    /* Grid mobile */
    .col-lg-3,
    .col-lg-4,
    .col-lg-6 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    /* Layout ultra-compatto */
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .card-header h6 {
        font-size: 0.8rem;
    }
    
    .small {
        font-size: 0.75rem !important;
    }
    
    /* Badge e elementi piccoli */
    .badge {
        font-size: 0.65rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
}

/* === ANIMAZIONI E TRANSIZIONI === */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(13, 110, 253, 0.05);
}

/* Spinner loading */
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

/* Focus per accessibilità */
.btn:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Custom scrollbar per tabelle */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === COLORI TEMA === */
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.card-header.bg-success {
    background-color: #198754 !important;
}

.card-header.bg-info {
    background-color: #0dcaf0 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.card-header.bg-danger {
    background-color: #dc3545 !important;
}

.card-header.bg-secondary {
    background-color: #6c757d !important;
}

/* Badge specifici per gravità */
.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
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
    
    .container {
        max-width: none;
        padding: 0;
    }
    
    h2 {
        page-break-after: avoid;
    }
    
    .table {
        font-size: 0.75rem;
    }
}

/* Toast notifications per feedback */
.toast-notification {
    border-radius: 8px;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Performance optimizations */
.card, canvas, .table {
    contain: layout style;
}

/* Custom properties per theming */
:root {
    --tecnico-primary: #0d6efd;
    --tecnico-success: #198754;
    --tecnico-warning: #ffc107;
    --tecnico-danger: #dc3545;
    --tecnico-info: #0dcaf0;
    --tecnico-border-radius: 8px;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Accessibilità migliorata */
.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0,0,0,0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Riduzione movimento per utenti sensibili */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    * {
        transition: none !important;
    }
}

/* Alto contrasto */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid;
    }
}
</style>
@endpush