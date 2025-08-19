{{-- 
    Vista Statistiche Amministratore - CORRETTA per AdminController
    File: resources/views/admin/statistiche.blade.php
    
    Questa vista funziona perfettamente con il tuo AdminController esistente
    Usa esattamente le variabili che il controller passa: compact('stats', 'distribuzioneUtenti', ...)
--}}
@extends('layouts.app')

@section('title', 'Statistiche Avanzate - Admin')

@section('content')
<div class="container-fluid mt-4">
    {{-- Header della pagina --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Avanzate
                    </h1>
                    <p class="text-muted mb-0">Analisi dettagliate e metriche del sistema</p>
                    <small class="text-muted">
                        Periodo analisi: ultimi {{ $periodo ?? 30 }} giorni
                    </small>
                </div>
                <div>
                    {{-- Pulsanti azioni --}}
                    <div class="btn-group me-2">
                        <a href="{{ route('admin.statistiche', ['periodo' => 7]) }}" 
                           class="btn btn-outline-info {{ ($periodo ?? 30) == 7 ? 'active' : '' }}">
                            7 giorni
                        </a>
                        <a href="{{ route('admin.statistiche', ['periodo' => 30]) }}" 
                           class="btn btn-outline-info {{ ($periodo ?? 30) == 30 ? 'active' : '' }}">
                            30 giorni
                        </a>
                        <a href="{{ route('admin.statistiche', ['periodo' => 90]) }}" 
                           class="btn btn-outline-info {{ ($periodo ?? 30) == 90 ? 'active' : '' }}">
                            90 giorni
                        </a>
                    </div>
                    <button id="refresh-stats" class="btn btn-outline-success me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiche Generali - USA LE VARIABILI DEL TUO CONTROLLER --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people display-4 text-primary mb-3"></i>
                    <h3 class="mb-1" id="total-users">{{ $stats['utenti_totali'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Utenti Totali</p>
                    {{-- Mostra crescita nuovi utenti --}}
                    @if(isset($stats['nuovi_utenti']) && $stats['nuovi_utenti'] > 0)
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            +{{ $stats['nuovi_utenti'] }} nuovi
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-box display-4 text-info mb-3"></i>
                    <h3 class="mb-1" id="total-products">{{ $stats['prodotti_totali'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Prodotti</p>
                    @if(isset($stats['nuovi_prodotti']) && $stats['nuovi_prodotti'] > 0)
                        <small class="text-info">
                            <i class="bi bi-plus-circle"></i>
                            +{{ $stats['nuovi_prodotti'] }} nuovi
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-wrench display-4 text-warning mb-3"></i>
                    <h3 class="mb-1" id="total-malfunctions">{{ $stats['malfunzionamenti_totali'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Malfunzionamenti</p>
                    @if(isset($stats['nuove_soluzioni']) && $stats['nuove_soluzioni'] > 0)
                        <small class="text-warning">
                            <i class="bi bi-plus-circle"></i>
                            +{{ $stats['nuove_soluzioni'] }} nuove soluzioni
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-geo-alt display-4 text-success mb-3"></i>
                    <h3 class="mb-1" id="total-centers">{{ $stats['centri_totali'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Centri Assistenza</p>
                    @if(isset($stats['utenti_attivi']) && $stats['utenti_attivi'] > 0)
                        <small class="text-success">
                            {{ $stats['utenti_attivi'] }} utenti attivi
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Prima riga di grafici --}}
    <div class="row g-4 mb-4">
        {{-- Grafico Distribuzione Utenti --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribuzione Utenti per Livello
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="usersChart" height="300"></canvas>
                    
                    {{-- Legenda con dati reali --}}
                    <div class="mt-3">
                        <div class="row text-center">
                            @if(isset($distribuzioneUtenti) && count($distribuzioneUtenti) > 0)
                                @foreach($distribuzioneUtenti as $livello => $count)
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="badge badge-livello-{{ $livello }} me-2">{{ $count }}</span>
                                            <small>
                                                @switch($livello)
                                                    @case('1') Pubblico @break
                                                    @case('2') Tecnici @break
                                                    @case('3') Staff @break
                                                    @case('4') Admin @break
                                                    @default Livello {{ $livello }} @break
                                                @endswitch
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">Nessun dato disponibile</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafico Prodotti per Categoria --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Prodotti per Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="productsChart" height="300"></canvas>
                    
                    {{-- Dettagli categorie --}}
                    @if(isset($prodottiPerCategoria) && count($prodottiPerCategoria) > 0)
                        <div class="mt-3">
                            <div class="row">
                                @foreach($prodottiPerCategoria as $categoria => $count)
                                    <div class="col-6 col-md-4 mb-1">
                                        <small class="text-muted">
                                            <strong>{{ ucfirst($categoria) }}:</strong> {{ $count }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Seconda riga di grafici --}}
    <div class="row g-4 mb-4">
        {{-- Grafico Malfunzionamenti per Gravità --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Malfunzionamenti per Gravità
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="malfunctionsChart" height="300"></canvas>
                    
                    {{-- Dettagli gravità --}}
                    @if(isset($malfunzionamentiPerGravita) && count($malfunzionamentiPerGravita) > 0)
                        <div class="mt-3">
                            <div class="row">
                                @foreach($malfunzionamentiPerGravita as $gravita => $count)
                                    <div class="col-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            @switch($gravita)
                                                @case('critica')
                                                    <span class="badge bg-danger me-2">{{ $count }}</span>
                                                    <small>Critica</small>
                                                    @break
                                                @case('alta')
                                                    <span class="badge bg-warning me-2">{{ $count }}</span>
                                                    <small>Alta</small>
                                                    @break
                                                @case('media')
                                                    <span class="badge bg-info me-2">{{ $count }}</span>
                                                    <small>Media</small>
                                                    @break
                                                @case('bassa')
                                                    <span class="badge bg-success me-2">{{ $count }}</span>
                                                    <small>Bassa</small>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary me-2">{{ $count }}</span>
                                                    <small>{{ ucfirst($gravita) }}</small>
                                            @endswitch
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grafico Crescita nel Tempo --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Crescita Utenti e Soluzioni
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabelle Dettagliate --}}
    <div class="row g-4 mb-4">
        {{-- Top Prodotti Problematici --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Prodotti con Più Problemi
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($prodottiProblematici) && $prodottiProblematici->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th class="text-center">Problemi</th>
                                        <th class="text-center">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prodottiProblematici->take(10) as $prodotto)
                                        <tr>
                                            <td>
                                                <strong>{{ $prodotto->nome }}</strong>
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($prodotto->categoria)
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($prodotto->categoria) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">
                                                    {{ $prodotto->malfunzionamenti_count }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.prodotti.show', $prodotto->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h5 class="text-success mt-2">Ottimo!</h5>
                            <p class="text-muted">Nessun prodotto con problemi significativi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Staff Più Attivi --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Staff Più Attivi
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($staffAttivi) && $staffAttivi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Staff</th>
                                        <th class="text-center">Soluzioni Create</th>
                                        <th class="text-center">Prodotti Assegnati</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffAttivi->take(10) as $staff)
                                        <tr>
                                            <td>
                                                <strong>{{ $staff->nome_completo }}</strong>
                                                <br><small class="text-muted">{{ $staff->username }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ $staff->malfunzionamenti_creati_count }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if(isset($staff->prodotti_assegnati_count))
                                                    <span class="badge bg-success">
                                                        {{ $staff->prodotti_assegnati_count }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-plus display-1 text-info"></i>
                            <h5 class="text-info mt-2">Nessun Staff Attivo</h5>
                            <p class="text-muted">Nessuna attività registrata nel periodo selezionato</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Metriche Aggiuntive --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Metriche Aggiuntive
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 text-center">
                        {{-- Aggiornamenti Soluzioni --}}
                        <div class="col-md-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-arrow-clockwise text-primary fs-1 mb-2"></i>
                                <h4 class="mb-1">
                                    {{ $stats['soluzioni_aggiornate'] ?? 0 }}
                                </h4>
                                <small class="text-muted">Soluzioni Aggiornate</small>
                                <br><small class="text-muted">Ultimi {{ $periodo ?? 30 }} giorni</small>
                            </div>
                        </div>

                        {{-- Periodo Analisi --}}
                        <div class="col-md-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-calendar3 text-info fs-1 mb-2"></i>
                                <h4 class="mb-1">
                                    {{ $periodo ?? 30 }}
                                </h4>
                                <small class="text-muted">Giorni Analizzati</small>
                                <br><small class="text-muted">Dal {{ now()->subDays($periodo ?? 30)->format('d/m/Y') }}</small>
                            </div>
                        </div>

                        {{-- Ultimo Aggiornamento --}}
                        <div class="col-md-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-clock text-success fs-1 mb-2"></i>
                                <h4 class="mb-1" id="last-update">
                                    {{ now()->format('H:i') }}
                                </h4>
                                <small class="text-muted">Ultimo Aggiornamento</small>
                                <br><small class="text-muted">{{ now()->format('d/m/Y') }}</small>
                            </div>
                        </div>

                        {{-- Database Status --}}
                        <div class="col-md-3">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-database text-warning fs-1 mb-2"></i>
                                <h4 class="mb-1">
                                    <span class="badge bg-success">Online</span>
                                </h4>
                                <small class="text-muted">Stato Database</small>
                                <br><small class="text-muted">Sistema operativo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JavaScript per i grafici e aggiornamenti --}}
@push('scripts')
{{-- Chart.js per i grafici --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
/**
 * JavaScript per la pagina Statistiche Admin
 * Usa i dati reali passati dal controller PHP
 */

// Variabili globali per i grafici
let usersChart, productsChart, malfunctionsChart, growthChart;

// Dati dal controller PHP - CORRETTI
const distribuzioneUtenti = @json($distribuzioneUtenti ?? []);
const prodottiPerCategoria = @json($prodottiPerCategoria ?? []);
const malfunzionamentiPerGravita = @json($malfunzionamentiPerGravita ?? []);
const crescitaUtenti = @json($crescitaUtenti ?? []);
const crescitaSoluzioni = @json($crescitaSoluzioni ?? []);

$(document).ready(function() {
    console.log('📊 Inizializzazione statistiche admin');
    console.log('📊 Dati ricevuti:', {
        distribuzioneUtenti,
        prodottiPerCategoria,
        malfunzionamentiPerGravita
    });
    
    // Inizializza tutti i grafici
    initializeCharts();
    
    // Gestione pulsante refresh
    $('#refresh-stats').on('click', function() {
        refreshAllStats();
    });
});

/**
 * Inizializza tutti i grafici della pagina
 */
function initializeCharts() {
    try {
        // Grafico distribuzione utenti (Pie Chart)
        initUsersChart();
        
        // Grafico prodotti per categoria (Bar Chart)  
        initProductsChart();
        
        // Grafico malfunzionamenti per gravità (Doughnut Chart)
        initMalfunctionsChart();
        
        // Grafico crescita nel tempo (Line Chart)
        initGrowthChart();
        
        console.log('✅ Tutti i grafici inizializzati');
    } catch (error) {
        console.error('❌ Errore inizializzazione grafici:', error);
    }
}

/**
 * Inizializza il grafico distribuzione utenti
 */
function initUsersChart() {
    const ctx = document.getElementById('usersChart');
    if (!ctx) {
        console.warn('Canvas usersChart non trovato');
        return;
    }
    
    const labels = [];
    const values = [];
    const colors = ['#6c757d', '#0dcaf0', '#ffc107', '#dc3545'];
    
    // Prepara i dati per il grafico
    Object.entries(distribuzioneUtenti).forEach(([livello, count], index) => {
        switch(livello) {
            case '1': labels.push('Pubblico'); break;
            case '2': labels.push('Tecnici'); break;
            case '3': labels.push('Staff'); break;
            case '4': labels.push('Admin'); break;
            default: labels.push('Livello ' + livello); break;
        }
        values.push(count);
    });
    
    if (values.length === 0) {
        console.warn('Nessun dato per grafico utenti');
        ctx.getContext('2d').fillText('Nessun dato disponibile', 50, 50);
        return;
    }
    
    usersChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Inizializza il grafico prodotti per categoria
 */
function initProductsChart() {
    const ctx = document.getElementById('productsChart');
    if (!ctx) return;
    
    const labels = Object.keys(prodottiPerCategoria).map(cat => 
        cat.charAt(0).toUpperCase() + cat.slice(1)
    );
    const values = Object.values(prodottiPerCategoria);
    
    if (values.length === 0) {
        console.warn('Nessun dato per grafico prodotti');
        return;
    }
    
    productsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Numero Prodotti',
                data: values,
                backgroundColor: '#0dcaf0',
                borderColor: '#0dcaf0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Inizializza il grafico malfunzionamenti per gravità
 */
function initMalfunctionsChart() {
    const ctx = document.getElementById('malfunctionsChart');
    if (!ctx) return;
    
    const labels = [];
    const values = [];
    const colors = [];
    
    // Ordine specifico per gravità
    const gravitaOrder = ['critica', 'alta', 'media', 'bassa'];
    const gravitaColors = {
        'critica': '#dc3545',
        'alta': '#fd7e14', 
        'media': '#ffc107',
        'bassa': '#198754'
    };
    
    gravitaOrder.forEach(gravita => {
        if (malfunzionamentiPerGravita[gravita]) {
            labels.push(gravita.charAt(0).toUpperCase() + gravita.slice(1));
            values.push(malfunzionamentiPerGravita[gravita]);
            colors.push(gravitaColors[gravita]);
        }
    });
    
    if (values.length === 0) {
        console.warn('Nessun dato per grafico malfunzionamenti');
        return;
    }
    
    malfunctionsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Inizializza il grafico crescita nel tempo
 */
function initGrowthChart() {
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;
    
    // Prepara i dati per il grafico lineare
    const labels = crescitaUtenti.map(item => {
        const date = new Date(item.data);
        return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
    });
    
    const utentiData = crescitaUtenti.map(item => item.count || 0);
    const soluzioniData = crescitaSoluzioni.map(item => item.count || 0);
    
    growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nuovi Utenti',
                data: utentiData,
                borderColor: '#0dcaf0',
                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Nuove Soluzioni',
                data: soluzioniData,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

/**
 * Aggiorna tutte le statistiche tramite AJAX
 */
function refreshAllStats() {
    const btn = $('#refresh-stats');
    btn.prop('disabled', true);
    btn.html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiornamento...');
    
    // Usa la route corretta del tuo AdminController
    $.ajax({
        url: '{{ route("admin.stats.update") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Aggiorna i contatori
                updateCounters(response.stats);
                
                showNotification('Statistiche aggiornate con successo', 'success');
                
                // Aggiorna timestamp
                $('#last-update').text(new Date().toLocaleTimeString('it-IT', {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
            } else {
                showNotification('Errore nell\'aggiornamento: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore AJAX:', error);
            showNotification('Errore durante l\'aggiornamento delle statistiche', 'danger');
        },
        complete: function() {
            btn.prop('disabled', false);
            btn.html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiorna');
        }
    });
}

/**
 * Aggiorna i contatori numerici
 */
function updateCounters(stats) {
    if (stats.total_utenti !== undefined) {
        $('#total-users').text(stats.total_utenti);
    }
    if (stats.total_prodotti !== undefined) {
        $('#total-products').text(stats.total_prodotti);
    }
    if (stats.total_soluzioni !== undefined) {
        $('#total-malfunctions').text(stats.total_soluzioni);
    }
    if (stats.total_centri !== undefined) {
        $('#total-centers').text(stats.total_centri);
    }
}

/**
 * Funzione helper per mostrare notificazioni
 */
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
    
    // Auto-rimuovi dopo 4 secondi
    setTimeout(() => {
        alert.alert('close');
    }, 4000);
}

/**
 * Funzione helper per formattare numeri
 */
function formatNumber(num) {
    return new Intl.NumberFormat('it-IT').format(num);
}

/**
 * Controllo se i grafici sono stati inizializzati correttamente
 */
function checkChartsStatus() {
    const charts = {
        'usersChart': usersChart,
        'productsChart': productsChart, 
        'malfunctionsChart': malfunctionsChart,
        'growthChart': growthChart
    };
    
    Object.entries(charts).forEach(([name, chart]) => {
        if (!chart) {
            console.warn(`Grafico ${name} non inizializzato`);
        } else {
            console.log(`✅ Grafico ${name} OK`);
        }
    });
}

// Debug: Verifica stato grafici dopo 2 secondi
setTimeout(checkChartsStatus, 2000);

// Auto-refresh ogni 5 minuti (300000ms)
setInterval(refreshAllStats, 300000);

console.log('✅ Sistema statistiche admin completamente inizializzato');
</script>

{{-- Stili personalizzati per la pagina statistiche --}}
<style>
/* Badge per livelli utente */
.badge-livello-1 { background-color: #6c757d !important; color: white !important; }
.badge-livello-2 { background-color: #0dcaf0 !important; color: white !important; }
.badge-livello-3 { background-color: #ffc107 !important; color: #000 !important; }
.badge-livello-4 { background-color: #dc3545 !important; color: white !important; }

/* Altezza fissa per i grafici */
#usersChart, #productsChart, #malfunctionsChart, #growthChart {
    height: 300px !important;
}

/* Miglioramenti responsivi */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .display-4 {
        font-size: 2rem !important;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
}

/* Animazioni per le card */
.card {
    transition: all 0.2s ease-in-out;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Miglioramenti per le tabelle */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Stili per i badge nelle tabelle */
.table .badge {
    font-size: 0.75rem;
}

/* Miglioramenti per le icone */
.display-1, .display-4, .fs-1 {
    line-height: 1;
}

/* Stili per i pulsanti periodo */
.btn-group .btn.active {
    background-color: #0dcaf0 !important;
    border-color: #0dcaf0 !important;
    color: white !important;
}

/* Stili per le notificazioni */
.alert.position-fixed {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Miglioramenti accessibilità */
.btn:focus, 
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
}

/* Placeholder per grafici vuoti */
canvas:empty::before {
    content: "Caricamento grafico...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
    font-size: 0.875rem;
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

/* Stili per stati vuoti */
.text-center .display-1 {
    opacity: 0.3;
}

/* Transizioni fluide */
.card, .btn, .badge, .alert {
    transition: all 0.2s ease-in-out;
}

/* Fix per chart.js responsive */
.card-body canvas {
    max-height: 300px !important;
}

/* Stili per il periodo selezionato */
.period-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    margin-bottom: 1rem;
}

/* Miglioramenti per mobile */
@media (max-width: 576px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        flex: 1;
        min-width: auto;
    }
    
    h1.h2 {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1rem 0.75rem;
    }
}

/* Stili per indicatori di stato */
.status-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.status-indicator.online {
    background-color: #198754;
}

.status-indicator.warning {
    background-color: #ffc107;
}

.status-indicator.error {
    background-color: #dc3545;
}

/* Debug styles (solo in ambiente di sviluppo) */
@media screen and (max-width: 1px) {
    .debug-info {
        position: fixed;
        top: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
        z-index: 9999;
    }
}
</style>
@endpush