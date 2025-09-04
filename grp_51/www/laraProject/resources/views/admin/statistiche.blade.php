{{--
    Statistiche Admin - Layout Compatto e Lineare
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista ottimizzata per amministratori con layout compatto, 
    grafici più piccoli e informazioni essenziali
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
            <button class="btn btn-primary" onclick="aggiornaStatistiche()">
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
                    @if(isset($stats['nuovi_utenti']) && $stats['nuovi_utenti'] > 0)
                        <br><small class="text-success">+{{ $stats['nuovi_utenti'] }} nuovi</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-box text-info fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['prodotti_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Prodotti</small>
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

    {{-- === ANDAMENTO CRESCITA COMPATTO === --}}
    @if(isset($crescitaUtenti) && count($crescitaUtenti) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-1"></i>
                            Crescita Utenti e Soluzioni
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <canvas id="graficoCrescita" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

// Dati dal controller PHP
const distribuzioneUtenti = @json($distribuzioneUtenti ?? []);
const prodottiPerCategoria = @json($prodottiPerCategoria ?? []);
const malfunzionamentiPerGravita = @json($malfunzionamentiPerGravita ?? []);
const crescitaUtenti = @json($crescitaUtenti ?? []);
const crescitaSoluzioni = @json($crescitaSoluzioni ?? []);

$(document).ready(function() {
    console.log('📊 Statistiche Admin Compatte inizializzate');
    console.log('📊 Dati ricevuti:', {
        distribuzioneUtenti,
        prodottiPerCategoria,
        malfunzionamentiPerGravita
    });
    
    // Inizializza grafici
    initializeCharts();
});

// Grafico Utenti - Compatto
function initUsersChart() {
    const ctx = document.getElementById('graficoUtenti');
    if (!ctx) return;
    
    const labels = [];
    const values = [];
    const colors = ['#6c757d', '#0dcaf0', '#ffc107', '#dc3545'];
    
    Object.entries(distribuzioneUtenti).forEach(([livello, count]) => {
        switch(livello) {
            case '1': labels.push('Pubblico'); break;
            case '2': labels.push('Tecnici'); break;
            case '3': labels.push('Staff'); break;
            case '4': labels.push('Admin'); break;
            default: labels.push('Livello ' + livello); break;
        }
        values.push(count);
    });
    
    if (values.length === 0) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            cutout: '50%'
        }
    });
}

// Grafico Prodotti - Compatto  
function initProductsChart() {
    const ctx = document.getElementById('graficoProdotti');
    if (!ctx) return;
    
    const labels = Object.keys(prodottiPerCategoria).map(cat => 
        cat.charAt(0).toUpperCase() + cat.slice(1)
    );
    const values = Object.values(prodottiPerCategoria);
    
    if (values.length === 0) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: '#0dcaf0',
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
}

// Grafico Gravità - Compatto
function initGravityChart() {
    const ctx = document.getElementById('graficoGravita');
    if (!ctx) return;
    
    const labels = [];
    const values = [];
    const colors = [];
    
    const gravitaOrder = ['critica', 'alta', 'media', 'bassa'];
    const gravitaColors = {
        'critica': '#dc3545',
        'alta': '#ffc107', 
        'media': '#0dcaf0',
        'bassa': '#198754'
    };
    
    gravitaOrder.forEach(gravita => {
        if (malfunzionamentiPerGravita[gravita]) {
            labels.push(gravita.charAt(0).toUpperCase() + gravita.slice(1));
            values.push(malfunzionamentiPerGravita[gravita]);
            colors.push(gravitaColors[gravita]);
        }
    });
    
    if (values.length === 0) return;
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions
        }
    });
}

// Grafico Crescita - Compatto
function initGrowthChart() {
    const ctx = document.getElementById('graficoCrescita');
    if (!ctx) return;
    
    const labels = crescitaUtenti.map(item => {
        const date = new Date(item.data);
        return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
    });
    
    const utentiData = crescitaUtenti.map(item => item.count || 0);
    const soluzioniData = crescitaSoluzioni.map(item => item.count || 0);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Utenti',
                data: utentiData,
                borderColor: '#0dcaf0',
                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                tension: 0.3,
                fill: true,
                borderWidth: 2
            }, {
                label: 'Soluzioni',
                data: soluzioniData,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.3,
                fill: true,
                borderWidth: 2
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
}

function initializeCharts() {
    try {
        initUsersChart();
        initProductsChart();
        initGravityChart();
        initGrowthChart();
        console.log('✅ Tutti i grafici admin inizializzati');
    } catch (error) {
        console.error('❌ Errore inizializzazione grafici:', error);
    }
}

// === FUNZIONI UTILITY ===
function aggiornaStatistiche() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    setTimeout(() => location.reload(), 1000);
}

// === ANIMAZIONI CONTATORI ===
function animateCounters() {
    $('.h5.fw-bold').each(function() {
        const $counter = $(this);
        const text = $counter.text().trim();
        const target = parseInt(text.replace(/[^\d]/g, ''));
        
        if (!isNaN(target) && target > 0 && target < 1000) {
            $counter.text('0');
            
            $({ counter: 0 }).animate({ counter: target }, {
                duration: 1500,
                easing: 'swing',
                step: function() {
                    $counter.text(Math.ceil(this.counter));
                },
                complete: function() {
                    $counter.text(target);
                }
            });
        }
    });
}

setTimeout(animateCounters, 500);

// === NOTIFICHE ===
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(alert);
    setTimeout(() => alert.alert('close'), 4000);
}

// Auto-refresh ogni 10 minuti
setInterval(() => {
    console.log('🔄 Auto-refresh statistiche admin');
}, 600000);

console.log('✅ Statistiche Admin Compatte caricate');
</script>
@endpush

@push('styles')
<style>
/* === STILI COMPATTI PER STATISTICHE ADMIN === */

/* Badge per livelli utente */
.badge-livello-1 { background-color: #6c757d !important; color: white !important; }
.badge-livello-2 { background-color: #0dcaf0 !important; color: white !important; }
.badge-livello-3 { background-color: #ffc107 !important; color: #000 !important; }
.badge-livello-4 { background-color: #dc3545 !important; color: white !important; }

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

/* Liste compatte */
.list-group-item {
    font-size: 0.85rem;
}

/* Progress bar e elementi interattivi */
.progress {
    border-radius: 6px;
}

.progress-bar {
    transition: width 0.4s ease;
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
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

/* Stile per le icone nelle card */
.fs-4 {
    font-size: 1.25rem !important;
}

/* Hover per le righe della tabella */
.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.025);
}

/* Alert personalizzati */
.alert {
    border-radius: 8px;
    font-size: 0.9rem;
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

/* Stili specifici per badge di stato */
.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

/* Stili per le cards con colori specifici */
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.card-header.bg-info {
    background-color: #0dcaf0 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
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
    
    .h2 {
        font-size: 1.3rem !important;
    }
}

/* Stili per tooltip */
.tooltip {
    font-size: 0.8rem;
}

/* Miglioramenti accessibilità */
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

/* Focus visibile per accessibilità */
.btn:focus-visible, 
.form-control:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Transizioni fluide globali */
* {
    box-sizing: border-box;
}

.card, .btn, .badge, .alert, .table-hover tbody tr {
    transition: all 0.2s ease-in-out;
}
</style>
@endpush