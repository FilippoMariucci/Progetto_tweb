{{--
    Statistiche Tecnico - Layout Compatto e Lineare
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista ottimizzata per tecnici con layout compatto, 
    grafici più piccoli e informazioni essenziali
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// === CONFIGURAZIONE GRAFICI COMPATTI ===

// Configurazione comune per tutti i grafici
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false // Nasconde legenda per risparmiare spazio
        }
    },
    elements: {
        point: {
            radius: 3 // Punti più piccoli
        }
    }
};

// Grafico Gravità - Compatto
const ctxGravita = document.getElementById('graficoGravita').getContext('2d');
new Chart(ctxGravita, {
    type: 'doughnut',
    data: {
        labels: [@foreach(($stats['malfunzionamenti']['per_gravita'] ?? []) as $gravita => $count) '{{ ucfirst($gravita) }}', @endforeach],
        datasets: [{
            data: [{{ implode(',', array_values($stats['malfunzionamenti']['per_gravita'] ?? [])) }}],
            backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#17a2b8'],
            borderWidth: 1
        }]
    },
    options: {
        ...commonOptions,
        cutout: '50%'
    }
});

// Grafico Trend - Compatto  
const ctxTrend = document.getElementById('graficoTrend').getContext('2d');
new Chart(ctxTrend, {
    type: 'line',
    data: {
        labels: {!! json_encode(($stats['trend_settimanale']['giorni'] ?? [])) !!},
        datasets: [{
            data: {!! json_encode(($stats['trend_settimanale']['conteggi'] ?? [])) !!},
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.3,
            fill: true,
            borderWidth: 2
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            x: {
                display: false // Nasconde etichette X per risparmiare spazio
            },
            y: {
                beginAtZero: true,
                display: false, // Nasconde etichette Y
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Grafico Categorie - Compatto
const ctxCategorie = document.getElementById('graficoCategorie').getContext('2d');
new Chart(ctxCategorie, {
    type: 'bar',
    data: {
        labels: [@foreach(($stats['per_categoria'] ?? []) as $categoria => $count) '{{ ucfirst($categoria) }}', @endforeach],
        datasets: [{
            data: [{{ implode(',', array_values($stats['per_categoria'] ?? [])) }}],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],
            borderWidth: 1
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            x: {
                display: false
            },
            y: {
                beginAtZero: true,
                display: false,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// === FUNZIONI UTILITY ===
function aggiornaStatistiche() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    setTimeout(() => location.reload(), 1000);
}

// Auto-refresh ogni 10 minuti
setInterval(() => {
    console.log('🔄 Auto-refresh statistiche tecnico');
    // Implementare chiamata AJAX se necessario
}, 600000);

console.log('✅ Statistiche Tecnico - Layout Compatto caricato');
</script>
@endpush

@push('styles')
<style>
/* === STILI COMPATTI PER STATISTICHE TECNICO === */

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

/* Responsive migliorato */
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    canvas {
        max-height: 100px !important;
    }
    
    .col-lg-4, .col-lg-6 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: start !important;
    }
    
    .btn-group {
        margin-top: 0.5rem;
    }
    
    .small {
        font-size: 0.75rem !important;
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
</style>
@endpush