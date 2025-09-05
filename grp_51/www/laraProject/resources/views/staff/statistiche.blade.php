{{--
    Statistiche Staff - Layout Compatto e Lineare
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista ottimizzata per staff con layout compatto, 
    grafici più piccoli e informazioni essenziali
--}}

@extends('layouts.app')

@section('title', 'Le mie Statistiche - Staff')

@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-warning me-2"></i>
                Le mie Statistiche
            </h2>
            <p class="text-muted small mb-0">{{ $user->nome_completo ?? $user->name ?? 'Staff Aziendale' }}</p>
            <small class="text-muted">Periodo: ultimi {{ $periodo }} giorni</small>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- Controlli periodo --}}
            <a href="{{ route('staff.statistiche', ['periodo' => 7]) }}" 
               class="btn btn-outline-warning {{ $periodo == 7 ? 'active' : '' }}">7g</a>
            <a href="{{ route('staff.statistiche', ['periodo' => 30]) }}" 
               class="btn btn-outline-warning {{ $periodo == 30 ? 'active' : '' }}">30g</a>
            <a href="{{ route('staff.statistiche', ['periodo' => 90]) }}" 
               class="btn btn-outline-warning {{ $periodo == 90 ? 'active' : '' }}">90g</a>
            {{-- Azioni --}}
            <button class="btn btn-primary" onclick="aggiornaStatistiche()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Alert di errore se presente --}}
    @if(isset($error))
        <div class="alert alert-warning border-start border-warning border-4 mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $error }}
        </div>
    @endif

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE === --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-tools text-success fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['soluzioni_create'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni Create</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-pencil-square text-info fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['soluzioni_modificate'] ?? 0 }}</h5>
                    <small class="text-muted">Modifiche</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['critiche_risolte'] ?? 0 }}</h5>
                    <small class="text-muted">Critiche Risolte</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-trophy text-warning fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">
                        @if($stats['ranking_posizione'] ?? null)
                            #{{ $stats['ranking_posizione'] }}
                        @else
                            N/A
                        @endif
                    </h5>
                    <small class="text-muted">Ranking</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI === --}}
    <div class="row g-3 mb-3">
        {{-- Attività Periodo - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar3 me-1"></i>
                        Attività {{ $periodo }}g
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- Statistiche del periodo in formato compatto --}}
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <div class="h5 text-success fw-bold mb-0">
                                    {{ $stats['soluzioni_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Nuove</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-info bg-opacity-10 rounded">
                                <div class="h5 text-info fw-bold mb-0">
                                    {{ $stats['modifiche_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Modifiche</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra progresso obiettivo --}}
                    <hr class="my-2">
                    @php
                        $obiettivo = ceil($periodo / 7);
                        $raggiunte = $stats['soluzioni_periodo'] ?? 0;
                        $percentuale = $obiettivo > 0 ? min(100, ($raggiunte / $obiettivo) * 100) : 0;
                    @endphp
                    <div class="mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Obiettivo: {{ $obiettivo }}</small>
                            <small class="text-muted">{{ number_format($percentuale, 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $percentuale }}%" 
                                 role="progressbar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribuzione Gravità - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        {{-- Critiche --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-danger bg-opacity-10 rounded">
                                <div class="bg-danger rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Critiche</small>
                                    <div class="fw-bold small">{{ $stats['critiche_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Alte --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-warning bg-opacity-10 rounded">
                                <div class="bg-warning rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Alte</small>
                                    <div class="fw-bold small">{{ $stats['alte_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Medie --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-info bg-opacity-10 rounded">
                                <div class="bg-info rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Medie</small>
                                    <div class="fw-bold small">{{ $stats['medie_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Basse --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-success bg-opacity-10 rounded">
                                <div class="bg-success rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Basse</small>
                                    <div class="fw-bold small">{{ $stats['basse_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tempo medio risposta --}}
                    @if($stats['tempo_medio_risposta'] ?? null)
                        <hr class="my-2">
                        <div class="text-center">
                            <small class="text-muted">Tempo Medio</small>
                            <div class="fw-bold text-primary">{{ $stats['tempo_medio_risposta'] }}h</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Staff - Compatto --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-gear me-1"></i>
                        Profilo Staff
                    </h6>
                </div>
                <div class="card-body p-3">
                    <p class="small mb-1"><strong>Staff:</strong> {{ $user->nome_completo ?? $user->name ?? 'N/A' }}</p>
                    <p class="small mb-1"><strong>Livello:</strong> Staff Tecnico (Livello 3)</p>
                    <p class="small mb-1"><strong>Su totale:</strong> {{ $stats['totale_staff'] ?? 0 }} staff</p>
                    @if($stats['ranking_posizione'] ?? null)
                        <p class="small mb-0">
                            <strong>Posizione:</strong> 
                            <span class="badge bg-warning text-dark">#{{ $stats['ranking_posizione'] }}</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ANDAMENTO MENSILE COMPATTO === --}}
    @if($attivitaMensile && count($attivitaMensile) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-1"></i>
                            Andamento Mensile (6 mesi)
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        {{-- Grafico barre compatto --}}
                        <div class="row g-1">
                            @foreach($attivitaMensile as $mese)
                                <div class="col-2 text-center">
                                    <div class="mb-1">
                                        @php
                                            $maxValue = max(collect($attivitaMensile)->max('soluzioni_create'), 1);
                                            $altezzaCreate = $mese['soluzioni_create'] > 0 ? (($mese['soluzioni_create'] / $maxValue) * 60) : 0;
                                            $altezzaModificate = $mese['soluzioni_modificate'] > 0 ? (($mese['soluzioni_modificate'] / $maxValue) * 60) : 0;
                                        @endphp
                                        <div style="height: 60px;" class="d-flex flex-column justify-content-end">
                                            @if($mese['soluzioni_create'] > 0)
                                                <div class="bg-success rounded-top mb-1 chart-bar" 
                                                     style="height: {{ $altezzaCreate }}%; min-height: 3px;"
                                                     title="Soluzioni: {{ $mese['soluzioni_create'] }}">
                                                </div>
                                            @endif
                                            @if($mese['soluzioni_modificate'] > 0)
                                                <div class="bg-info rounded-bottom chart-bar" 
                                                     style="height: {{ $altezzaModificate }}%; min-height: 3px;"
                                                     title="Modifiche: {{ $mese['soluzioni_modificate'] }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $mese['mese'] }}</small>
                                    <div class="small">
                                        <span class="badge bg-success">{{ $mese['soluzioni_create'] }}</span>
                                        <span class="badge bg-info">{{ $mese['soluzioni_modificate'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Legenda compatta --}}
                        <hr class="my-2">
                        <div class="row text-center small">
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-success rounded me-1" style="width: 8px; height: 8px;"></div>
                                    <span class="text-muted">Create</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-info rounded me-1" style="width: 8px; height: 8px;"></div>
                                    <span class="text-muted">Modificate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === SEZIONE DETTAGLI LINEARI === --}}
    <div class="row g-3 mb-3">
        {{-- Prodotti per cui hai creato più soluzioni --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-diamond me-1"></i>
                        Prodotti con Più Tue Soluzioni
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2 text-center">Tue Soluzioni</th>
                                    <th class="py-2 text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($prodottiProblematici->count() > 0)
                                    @foreach($prodottiProblematici->take(6) as $prodotto)
                                        <tr class="small">
                                            <td class="py-2">
                                                <strong>{{ $prodotto->nome }}</strong>
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($prodotto->categoria ?? 'generale') }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-center">
                                                <span class="badge bg-warning text-dark">
                                                    {{ $prodotto->soluzioni_mie }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-end">
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
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

        {{-- Ultime Soluzioni --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-1"></i>
                        Ultime Soluzioni
                    </h6>
                </div>
                <div class="card-body p-2">
                    @if($ultimeSoluzioni->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($ultimeSoluzioni->take(5) as $soluzione)
                                <div class="list-group-item px-0 border-0 py-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-1">
                                            <h6 class="mb-1 fw-semibold small text-truncate">
                                                {{ Str::limit($soluzione->titolo, 30) }}
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-box me-1"></i>
                                                {{ Str::limit($soluzione->prodotto->nome, 20) }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $soluzione->created_at->format('d/m H:i') }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ 
                                                $soluzione->gravita == 'critica' ? 'danger' : 
                                                ($soluzione->gravita == 'alta' ? 'warning' : 
                                                ($soluzione->gravita == 'media' ? 'info' : 'success')) 
                                            }}">
                                                {{ substr(ucfirst($soluzione->gravita), 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-2">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" 
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-list me-1"></i>Vedi Tutte
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-plus-circle display-6 text-muted opacity-50"></i>
                            <p class="text-muted small mt-2 mb-0">Nessuna soluzione</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === CATEGORIE COMPATTE === --}}
    @if($soluzioniPerCategoria && $soluzioniPerCategoria->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-tags me-1"></i>
                            Soluzioni per Categoria
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            @foreach($soluzioniPerCategoria->take(6) as $categoria)
                                <div class="col-lg-2 col-md-3 col-4">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h5 text-primary mb-1">{{ $categoria->count }}</div>
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $categoria->categoria ?? 'Generale')) }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

// Dati di sessione per notifiche JS
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
// ...
</script>
@endpush

@push('styles')
<style>
/* === STILI COMPATTI PER STATISTICHE STAFF === */

/* Card più compatte */
.card {
    border-radius: 8px;
    border: none !important;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
}

.card-body {
    font-size: 0.9rem;
}

/* Tabelle più compatte */
.table-sm td, .table-sm th {
    padding: 0.4rem;
    font-size: 0.85rem;
}

/* Grafici più piccoli e responsive */
canvas {
    max-height: 120px !important;
}

/* Badge più piccoli */
.badge {
    font-size: 0.7rem;
}

/* Bottoni più compatti */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

/* Charts compatti */
.chart-bar {
    transition: all 0.2s ease;
}

.chart-bar:hover {
    opacity: 0.8;
    transform: scaleY(1.02);
}

/* Progress bar compatta */
.progress {
    border-radius: 6px;
}

.progress-bar {
    transition: width 0.4s ease;
}

/* Liste compatte */
.list-group-item {
    font-size: 0.85rem;
}

/* Responsive migliorato */
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
}

@media (max-width: 576px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: start !important;
    }
    
    .btn-group {
        margin-top: 0.5rem;
        width: 100%;
    }
    
    .small {
        font-size: 0.75rem !important;
    }
    
    .h5 {
        font-size: 1.1rem !important;
    }
    
    .fs-4 {
        font-size: 1.2rem !important;
    }
}

/* Animazioni leggere */
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

/* Loading spinner */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* Colori personalizzati */
.text-muted {
    color: #6c757d !important;
}

.fw-semibold {
    font-weight: 600;
}

/* Effetti speciali per le statistiche */
.bg-opacity-10 {
    background-color: rgba(var(--bs-success-rgb), 0.1) !important;
}

/* Stile per le icone nelle card */
.fs-4 {
    font-size: 1.25rem !important;
}

/* Hover per le righe della tabella */
.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.025);
}

/* Stile per i grafici a barre semplici */
.chart-bar {
    border-radius: 2px;
}

/* Alert personalizzati */
.alert {
    border-radius: 8px;
    font-size: 0.9rem;
}

/* Breadcrumb se presente */
.breadcrumb {
    font-size: 0.85rem;
    margin-bottom: 0;
}

/* Ottimizzazioni per stampa */
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .chart-bar {
        background-color: #6c757d !important;
    }
}

/* Scrollbar personalizzata per le tabelle */
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

/* Effetto focus migliorato */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Stile per elementi disabilitati */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Animazione per il refresh */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border {
    animation: spin 1s linear infinite;
}

/* Margini consistenti */
.mb-3 {
    margin-bottom: 1rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

/* Stile per link */
a {
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Finali responsive per ultra-piccoli schermi */
@media (max-width: 360px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .card-body {
        padding: 0.5rem;
    }
    
    .btn-group-sm .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endpush