{{-- Vista completa delle statistiche per il tecnico --}}
@extends('layouts.app')

@section('title', 'Le mie Statistiche - Tecnico')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            {{-- Header della pagina --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Le mie Statistiche
                    </h1>
                    <p class="text-muted mb-0">Dashboard completa delle statistiche per {{ $user->nome_completo }}</p>
                </div>
                
                {{-- Pulsanti azione --}}
                <div>
                    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>
                        Torna alla Dashboard
                    </a>
                    <button class="btn btn-primary" onclick="aggiornaStatistiche()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Aggiorna Dati
                    </button>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a></li>
                    <li class="breadcrumb-item active">Le mie Statistiche</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- === SEZIONE STATISTICHE GENERALI === --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="h4 mb-3">
                <i class="bi bi-speedometer2 text-info me-2"></i>
                Panoramica Generale
            </h3>
        </div>
        
        {{-- Card statistiche principali --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam display-4 text-primary mb-2"></i>
                    <h5 class="card-title">Prodotti Totali</h5>
                    <h2 class="text-primary">{{ number_format($stats['generale']['total_prodotti']) }}</h2>
                    <small class="text-muted">
                        {{ number_format($stats['generale']['prodotti_attivi']) }} attivi
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle display-4 text-warning mb-2"></i>
                    <h5 class="card-title">Malfunzionamenti</h5>
                    <h2 class="text-warning">{{ number_format($stats['malfunzionamenti']['totali']) }}</h2>
                    <small class="text-muted">
                        {{ $stats['malfunzionamenti']['critici'] }} critici
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt display-4 text-info mb-2"></i>
                    <h5 class="card-title">Centri Assistenza</h5>
                    <h2 class="text-info">{{ number_format($stats['generale']['total_centri']) }}</h2>
                    <small class="text-muted">sul territorio</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-month display-4 text-success mb-2"></i>
                    <h5 class="card-title">Questo Mese</h5>
                    <h2 class="text-success">{{ number_format($stats['malfunzionamenti']['questo_mese']) }}</h2>
                    <small class="text-muted">
                        @php
                            $differenza = $stats['malfunzionamenti']['questo_mese'] - $stats['malfunzionamenti']['mese_precedente'];
                            $segno = $differenza >= 0 ? '+' : '';
                        @endphp
                        {{ $segno }}{{ $differenza }} dal mese scorso
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- === GRAFICI STATISTICHE === --}}
        <div class="col-lg-8">
            {{-- Grafico distribuzione per gravità --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribuzione Malfunzionamenti per Gravità
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoGravita" height="100"></canvas>
                </div>
            </div>

            {{-- Grafico trend settimanale --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Trend Ultimi 7 Giorni
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoTrend" height="100"></canvas>
                </div>
            </div>

            {{-- Grafico per categorie --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Prodotti per Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoCategorie" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- === SIDEBAR INFO === --}}
        <div class="col-lg-4">
            {{-- Informazioni centro assistenza --}}
            @if($stats['centro_assistenza'])
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Il mio Centro
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ $stats['centro_assistenza']['nome'] }}</h6>
                    <p class="mb-2">
                        <i class="bi bi-geo-alt text-muted me-2"></i>
                        {{ $stats['centro_assistenza']['citta'] }} ({{ $stats['centro_assistenza']['provincia'] }})
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-house text-muted me-2"></i>
                        {{ $stats['centro_assistenza']['indirizzo'] }}
                    </p>
                    @if($stats['centro_assistenza']['telefono'])
                    <p class="mb-2">
                        <i class="bi bi-telephone text-muted me-2"></i>
                        {{ $stats['centro_assistenza']['telefono'] }}
                    </p>
                    @endif
                    
                    @if($stats['centro_assistenza']['altri_tecnici'] > 0)
                    <hr>
                    <h6 class="fw-bold">Colleghi Tecnici ({{ $stats['centro_assistenza']['altri_tecnici'] }})</h6>
                    @foreach($stats['centro_assistenza']['colleghi'] as $collega)
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-circle text-muted me-2"></i>
                        <div>
                            <span class="fw-semibold">{{ $collega->nome }} {{ $collega->cognome }}</span>
                            @if($collega->specializzazione)
                                <br><small class="text-muted">{{ $collega->specializzazione }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif

            {{-- Info personali --}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>
                        Profilo Tecnico
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Nome:</strong> {{ $user->nome_completo }}</p>
                    @if($user->specializzazione)
                    <p><strong>Specializzazione:</strong> {{ $user->specializzazione }}</p>
                    @endif
                    <p><strong>Attivo dal:</strong> {{ $stats['personali']['data_registrazione']->format('d/m/Y') }}</p>
                    <p><strong>Giorni di servizio:</strong> {{ $stats['personali']['giorni_attivo'] }}</p>
                </div>
            </div>

            {{-- Top prodotti problematici --}}
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        Prodotti Più Problematici
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($stats['prodotti_problematici'] as $prodotto)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <span class="fw-semibold">{{ $prodotto->nome }}</span>
                            <br><small class="text-muted">{{ $prodotto->modello }}</small>
                        </div>
                        <span class="badge bg-danger">{{ $prodotto->critici_count }}</span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Nessun prodotto critico al momento</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- === TABELLA MALFUNZIONAMENTI CRITICI === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Malfunzionamenti Critici Recenti (Top 10)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Problema</th>
                                    <th>Gravità</th>
                                    <th>Segnalazioni</th>
                                    <th>Data</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['critici_recenti'] as $malfunzionamento)
                                <tr>
                                    <td>
                                        <strong>{{ $malfunzionamento->prodotto->nome ?? 'N/D' }}</strong>
                                        <br><small class="text-muted">{{ $malfunzionamento->prodotto->modello ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $malfunzionamento->titolo }}</span>
                                        <br><small class="text-muted">{{ Str::limit($malfunzionamento->descrizione, 60) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ ucfirst($malfunzionamento->gravita) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $malfunzionamento->numero_segnalazioni ?? 0 }}</span>
                                    </td>
                                    <td>{{ $malfunzionamento->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto_id, $malfunzionamento->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Dettagli
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Nessun malfunzionamento critico al momento
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- === JAVASCRIPT PER GRAFICI === --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// === CONFIGURAZIONE GRAFICI ===

// Grafico a torta per gravità
const ctxGravita = document.getElementById('graficoGravita').getContext('2d');
const graficoGravita = new Chart(ctxGravita, {
    type: 'doughnut',
    data: {
        labels: [@foreach($stats['malfunzionamenti']['per_gravita'] as $gravita => $count) '{{ ucfirst($gravita) }}', @endforeach],
        datasets: [{
            data: [{{ implode(',', array_values($stats['malfunzionamenti']['per_gravita'])) }}],
            backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#17a2b8'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Grafico lineare per trend settimanale
const ctxTrend = document.getElementById('graficoTrend').getContext('2d');
const graficoTrend = new Chart(ctxTrend, {
    type: 'line',
    data: {
        labels: {!! json_encode($stats['trend_settimanale']['giorni']) !!},
        datasets: [{
            label: 'Nuovi Malfunzionamenti',
            data: {!! json_encode($stats['trend_settimanale']['conteggi']) !!},
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Grafico a barre per categorie
const ctxCategorie = document.getElementById('graficoCategorie').getContext('2d');
const graficoCategorie = new Chart(ctxCategorie, {
    type: 'bar',
    data: {
        labels: [@foreach($stats['per_categoria'] as $categoria => $count) '{{ $categoria }}', @endforeach],
        datasets: [{
            label: 'Numero Prodotti',
            data: [{{ implode(',', array_values($stats['per_categoria'])) }}],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// === FUNZIONI UTILITY ===

function aggiornaStatistiche() {
    // Mostra loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i> Aggiornamento...';
    btn.disabled = true;
    
    // Ricarica la pagina dopo 1 secondo
    setTimeout(function() {
        location.reload();
    }, 1000);
}

// Auto-refresh ogni 5 minuti
setInterval(function() {
    console.log('Auto-refresh statistiche tecnico');
    // Potresti implementare un refresh via AJAX qui
}, 300000); // 5 minuti

console.log('📊 Statistiche Tecnico caricate completamente!');
</script>
@endpush

@push('styles')
<style>
/* Stili personalizzati per la pagina statistiche */
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    border-radius: 8px;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
}

.table th {
    border-top: none;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Responsive per grafici */
@media (max-width: 768px) {
    canvas {
        max-height: 300px !important;
    }
}
</style>
@endpush
@endsection