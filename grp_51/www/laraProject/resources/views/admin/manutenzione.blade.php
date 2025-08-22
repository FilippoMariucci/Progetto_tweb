{{-- 
    Vista Manutenzione Sistema - Admin
    File: resources/views/admin/manutenzione.blade.php
    
    Questa vista gestisce la manutenzione del sistema, pulizia cache, 
    ottimizzazione database e monitoraggio stato sistema
--}}
@extends('layouts.app')

@section('title', 'Manutenzione Sistema - Admin')

@section('content')
<div class="container-fluid mt-4">
    {{-- Header della pagina --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-tools text-secondary me-2"></i>
                        Manutenzione Sistema
                    </h1>
                    <p class="text-muted mb-0">Gestione cache, ottimizzazione database e monitoraggio sistema</p>
                </div>
                <div>
                    {{-- Pulsanti azioni principali --}}
                    <button id="refresh-system-info" class="btn btn-outline-info me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna Info
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Messaggi di successo/errore --}}
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
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- === INFORMAZIONI SISTEMA === --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informazioni Sistema
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($systemInfo))
                        <div class="table-responsive">
                            <table class="table table-sm">
                                {{-- Versione Laravel --}}
                                <tr>
                                    <td><strong>Laravel</strong></td>
                                    <td>
                                        <span class="badge bg-primary">v{{ $systemInfo['laravel_version'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                
                                {{-- Versione PHP --}}
                                <tr>
                                    <td><strong>PHP</strong></td>
                                    <td>
                                        <span class="badge bg-success">v{{ $systemInfo['php_version'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                
                                {{-- Server Web --}}
                                <tr>
                                    <td><strong>Server Web</strong></td>
                                    <td>
                                        <span class="text-muted">{{ $systemInfo['server_software'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                
                                {{-- Versione Database --}}
                                <tr>
                                    <td><strong>Database</strong></td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $systemInfo['database_version'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                
                                {{-- Utilizzo Storage --}}
                                @if(isset($systemInfo['storage_usage']) && is_array($systemInfo['storage_usage']))
                                    <tr>
                                        <td><strong>Storage</strong></td>
                                        <td>
                                            @if(isset($systemInfo['storage_usage']['error']))
                                                <span class="text-danger">{{ $systemInfo['storage_usage']['error'] }}</span>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2">
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar 
                                                                @if(($systemInfo['storage_usage']['percentage'] ?? 0) > 80) bg-danger 
                                                                @elseif(($systemInfo['storage_usage']['percentage'] ?? 0) > 60) bg-warning 
                                                                @else bg-success @endif" 
                                                                role="progressbar" 
                                                                style="width: {{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%">
                                                                {{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $systemInfo['storage_usage']['used'] ?? 'N/A' }} / 
                                                        {{ $systemInfo['storage_usage']['total'] ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        
                        {{-- File di Log --}}
                        @if(isset($systemInfo['log_files']) && count($systemInfo['log_files']) > 0)
                            <h6 class="mt-4 mb-3">
                                <i class="bi bi-file-text me-2"></i>
                                File di Log
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome File</th>
                                            <th>Dimensione</th>
                                            <th>Ultima Modifica</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($systemInfo['log_files'] as $logFile)
                                            <tr>
                                                <td>
                                                    <code>{{ $logFile['name'] ?? 'N/A' }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $logFile['size'] ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $logFile['modified'] ?? 'N/A' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center py-4">Informazioni sistema non disponibili</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- === GESTIONE CACHE === --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Gestione Cache
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Stato delle cache --}}
                    @if(isset($cacheStatus))
                        <h6 class="mb-3">Stato Cache</h6>
                        <div class="row g-2 mb-4">
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
                                        <div class="flex-grow-1">
                                            <small class="fw-semibold">{{ ucfirst($type) }}</small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $enabled ? 'Attiva' : 'Non attiva' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Azioni Cache --}}
                    <h6 class="mb-3">Azioni Cache</h6>
                    <div class="d-grid gap-2">
                        {{-- Pulisci tutte le cache --}}
                        <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="all">
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Pulire tutte le cache? Questo potrebbe rallentare temporaneamente il sistema.')">
                                <i class="bi bi-trash me-1"></i>
                                Pulisci Tutte le Cache
                            </button>
                        </form>

                        {{-- Azioni cache specifiche --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="config">
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-gear me-1"></i>
                                        Config
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="route">
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-signpost me-1"></i>
                                        Route
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="view">
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="type" value="application">
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-app me-1"></i>
                                        App
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === SECONDA RIGA === --}}
    <div class="row g-4 mt-1">
        {{-- === MANUTENZIONE DATABASE === --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-database me-2"></i>
                        Manutenzione Database
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Ottimizzazione Database</strong><br>
                        Questa operazione ottimizza tutte le tabelle del database per migliorare le performance.
                        L'operazione è sicura ma potrebbe richiedere alcuni minuti.
                    </div>

                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('admin.manutenzione.optimize-db') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Avviare l\'ottimizzazione del database? L\'operazione potrebbe richiedere alcuni minuti.')">
                                <i class="bi bi-lightning-charge me-1"></i>
                                Ottimizza Database
                            </button>
                        </form>

                        {{-- Informazioni aggiuntive --}}
                        <div class="mt-3">
                            <h6>Informazioni Database</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Connessione:</strong></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Attiva
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Driver:</strong></td>
                                        <td><code>{{ config('database.default') }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Host:</strong></td>
                                        <td><code>{{ config('database.connections.mysql.host', 'N/A') }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Database:</strong></td>
                                        <td><code>{{ config('database.connections.mysql.database', 'N/A') }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === MONITORAGGIO SISTEMA === --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Monitoraggio Sistema
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Stato servizi in tempo reale --}}
                    <h6 class="mb-3">Stato Servizi</h6>
                    <div id="system-status" class="mb-4">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span>Controllo stato sistema...</span>
                        </div>
                    </div>

                    {{-- Metriche Performance --}}
                    <h6 class="mb-3">Metriche Performance</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-memory text-info display-6"></i>
                                <h6 class="mt-2 mb-1">Memoria</h6>
                                <small class="text-muted" id="memory-usage">
                                    {{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB
                                </small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-clock text-warning display-6"></i>
                                <h6 class="mt-2 mb-1">Uptime</h6>
                                <small class="text-muted">Sistema Online</small>
                            </div>
                        </div>
                    </div>

                    {{-- Controlli automatici --}}
                    <div class="mt-4">
                        <h6 class="mb-3">Controlli Automatici</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                            <label class="form-check-label" for="auto-refresh">
                                Auto-refresh stato sistema (ogni 30 secondi)
                            </label>
                        </div>
                    </div>

                    {{-- Pulsante refresh manuale --}}
                    <div class="d-grid mt-3">
                        <button id="manual-system-check" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Controlla Stato Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI AVANZATE === --}}
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Azioni Avanzate
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Export Dati --}}
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-download display-1 text-success mb-3"></i>
                                <h6>Export Dati</h6>
                                <p class="text-muted small">Esporta tutti i dati del sistema in formato JSON/CSV</p>
                                <a href="{{ route('admin.export.index') }}" class="btn btn-success">
                                    <i class="bi bi-download me-1"></i>Gestisci Export
                                </a>
                            </div>
                        </div>

                        {{-- Statistiche Avanzate --}}
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-graph-up display-1 text-info mb-3"></i>
                                <h6>Statistiche Avanzate</h6>
                                <p class="text-muted small">Visualizza analytics dettagliati e metriche sistema</p>
                                <a href="{{ route('admin.statistiche.index') }}" class="btn btn-info">
                                    <i class="bi bi-graph-up me-1"></i>Vai alle Statistiche
                                </a>
                            </div>
                        </div>

                        {{-- Gestione Utenti --}}
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-people display-1 text-warning mb-3"></i>
                                <h6>Gestione Utenti</h6>
                                <p class="text-muted small">Amministra utenti, permessi e configurazioni account</p>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-warning">
                                    <i class="bi bi-people me-1"></i>Gestisci Utenti
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Informazioni Aggiuntive --}}
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-shield-check me-2 text-success"></i>Sicurezza</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-check text-success me-1"></i> Middleware di autenticazione attivo</li>
                                <li><i class="bi bi-check text-success me-1"></i> Controlli di autorizzazione implementati</li>
                                <li><i class="bi bi-check text-success me-1"></i> Log delle attività amministrative</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-clock-history me-2 text-info"></i>Backup</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-info text-info me-1"></i> Backup automatico non configurato</li>
                                <li><i class="bi bi-info text-info me-1"></i> Utilizzare export dati per backup manuali</li>
                                <li><i class="bi bi-info text-info me-1"></i> File di log ruotati automaticamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JavaScript per monitoraggio sistema e aggiornamenti --}}
@push('scripts')
<script>
/**
 * JavaScript per la pagina Manutenzione Admin
 * Gestisce il monitoraggio sistema e controlli automatici
 */

let systemCheckInterval = null;
let autoRefreshEnabled = true;

$(document).ready(function() {
    console.log('🔧 Inizializzazione pagina manutenzione admin');
    
    // Controllo iniziale stato sistema
    checkSystemStatus();
    
    // Avvia controlli automatici
    startAutoRefresh();
    
    // Event listeners
    setupEventListeners();
});

/**
 * Configura tutti gli event listener
 */
function setupEventListeners() {
    // Pulsante refresh info sistema
    $('#refresh-system-info').on('click', function() {
        window.location.reload();
    });
    
    // Controllo manuale stato sistema
    $('#manual-system-check').on('click', function() {
        checkSystemStatus();
    });
    
    // Toggle auto-refresh
    $('#auto-refresh').on('change', function() {
        autoRefreshEnabled = $(this).is(':checked');
        
        if (autoRefreshEnabled) {
            startAutoRefresh();
            showNotification('Auto-refresh attivato', 'success');
        } else {
            stopAutoRefresh();
            showNotification('Auto-refresh disattivato', 'info');
        }
    });
    
    // Conferma azioni pericolose
    $('form').on('submit', function(e) {
        const actionType = $(this).find('button[type="submit"]').text().trim();
        
        if (actionType.includes('Tutte le Cache') || actionType.includes('Ottimizza Database')) {
            // Già gestito con onclick, ma aggiungiamo controllo extra
            const confirmed = confirm('Sei sicuro di voler procedere con questa operazione?');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        }
    });
}

/**
 * Controlla lo stato del sistema tramite AJAX
 */
function checkSystemStatus() {
    const statusContainer = $('#system-status');
    const button = $('#manual-system-check');
    
    // Mostra loading
    statusContainer.html(`
        <div class="d-flex justify-content-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <span>Controllo stato sistema...</span>
        </div>
    `);
    
    button.prop('disabled', true);
    
    $.ajax({
        url: '{{ route("api.admin.system.status") }}',
        method: 'GET',
        timeout: 10000,
        success: function(response) {
            if (response.success) {
                displaySystemStatus(response);
                updateMemoryUsage(response.server_info);
            } else {
                displaySystemError('Errore nel controllo stato sistema');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore controllo sistema:', error);
            
            if (status === 'timeout') {
                displaySystemError('Timeout nella verifica stato sistema');
            } else {
                displaySystemError('Errore di comunicazione con il server');
            }
        },
        complete: function() {
            button.prop('disabled', false);
        }
    });
}

/**
 * Mostra lo stato del sistema
 */
function displaySystemStatus(response) {
    const statusContainer = $('#system-status');
    const status = response.status;
    const components = response.components;
    
    let statusClass, statusIcon, statusText;
    
    switch(status) {
        case 'operational':
            statusClass = 'success';
            statusIcon = 'check-circle-fill';
            statusText = 'Sistema Operativo';
            break;
        case 'degraded':
            statusClass = 'warning';
            statusIcon = 'exclamation-triangle-fill';
            statusText = 'Prestazioni Ridotte';
            break;
        case 'error':
            statusClass = 'danger';
            statusIcon = 'x-circle-fill';
            statusText = 'Errori Rilevati';
            break;
        default:
            statusClass = 'secondary';
            statusIcon = 'question-circle-fill';
            statusText = 'Stato Sconosciuto';
    }
    
    let html = `
        <div class="text-center mb-3">
            <i class="bi bi-${statusIcon} text-${statusClass} display-4"></i>
            <h6 class="mt-2 text-${statusClass}">${statusText}</h6>
            <small class="text-muted">Ultimo controllo: ${new Date().toLocaleTimeString('it-IT')}</small>
        </div>
    `;
    
    // Dettagli componenti
    if (components) {
        html += '<div class="row g-2">';
        
        Object.entries(components).forEach(([component, state]) => {
            let componentClass, componentIcon;
            
            switch(state) {
                case 'online':
                case 'active':
                case 'writable':
                    componentClass = 'success';
                    componentIcon = 'check-circle';
                    break;
                case 'read-only':
                    componentClass = 'warning';
                    componentIcon = 'exclamation-triangle';
                    break;
                case 'error':
                    componentClass = 'danger';
                    componentIcon = 'x-circle';
                    break;
                default:
                    componentClass = 'secondary';
                    componentIcon = 'question-circle';
            }
            
            html += `
                <div class="col-6">
                    <div class="d-flex align-items-center p-2 rounded bg-light">
                        <i class="bi bi-${componentIcon} text-${componentClass} me-2"></i>
                        <div class="flex-grow-1">
                            <small class="fw-semibold">${component.charAt(0).toUpperCase() + component.slice(1)}</small>
                            <br>
                            <small class="text-${componentClass}">${state}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
    }
    
    statusContainer.html(html);
}

/**
 * Mostra errore nel controllo sistema
 */
function displaySystemError(message) {
    const statusContainer = $('#system-status');
    
    statusContainer.html(`
        <div class="text-center">
            <i class="bi bi-exclamation-triangle text-danger display-4"></i>
            <h6 class="mt-2 text-danger">Errore Controllo Sistema</h6>
            <p class="text-muted small">${message}</p>
            <small class="text-muted">Ultimo tentativo: ${new Date().toLocaleTimeString('it-IT')}</small>
        </div>
    `);
}

/**
 * Aggiorna informazioni uso memoria
 */
function updateMemoryUsage(serverInfo) {
    if (serverInfo && serverInfo.memory_usage) {
        $('#memory-usage').text(serverInfo.memory_usage);
    }
}

/**
 * Avvia controlli automatici
 */
function startAutoRefresh() {
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
    }
    
    if (autoRefreshEnabled) {
        systemCheckInterval = setInterval(function() {
            if (autoRefreshEnabled) {
                checkSystemStatus();
            }
        }, 30000); // Ogni 30 secondi
        
        console.log('✅ Auto-refresh sistema attivato (30s)');
    }
}

/**
 * Ferma controlli automatici
 */
function stopAutoRefresh() {
    if (systemCheckInterval) {
        clearInterval(systemCheckInterval);
        systemCheckInterval = null;
        console.log('⏹️ Auto-refresh sistema fermato');
    }
}

/**
 * Mostra notificazioni
 */
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'info' ? 'alert-info' :
                      type === 'warning' ? 'alert-warning' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'info' ? 'info-circle' :
                 type === 'warning' ? 'exclamation-triangle' : 'x-circle';
    
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
 * Gestisce la disconnessione/riconnessione di rete
 */
$(window).on('online', function() {
    showNotification('Connessione ripristinata', 'success');
    if (autoRefreshEnabled) {
        checkSystemStatus();
    }
});

$(window).on('offline', function() {
    showNotification('Connessione persa', 'warning');
});

/**
 * Cleanup quando si esce dalla pagina
 */
$(window).on('beforeunload', function() {
    stopAutoRefresh();
});

/**
 * Debug: Mostra stato auto-refresh
 */
function debugAutoRefresh() {
    console.log('🔧 Debug Auto-refresh:', {
        enabled: autoRefreshEnabled,
        intervalSet: !!systemCheckInterval,
        checkboxChecked: $('#auto-refresh').is(':checked')
    });
}

// Debug ogni 60 secondi in sviluppo
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    setInterval(debugAutoRefresh, 60000);
}

console.log('✅ Sistema manutenzione admin completamente inizializzato');
</script>

{{-- Stili personalizzati per la pagina manutenzione --}}
<style>
/* Miglioramenti per le card */
.card {
    transition: all 0.2s ease-in-out;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Stili per i badge di stato */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

/* Progress bar personalizzata */
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Stili per le icone display */
.display-1, .display-4, .display-6 {
    line-height: 1;
}

/* Spinner personalizzato */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Tabelle responsive */
.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

/* Code elements */
code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

/* Form checks */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    font-size: 0.875rem;
}

/* Alert personalizzati */
.alert {
    border-radius: 0.5rem;
    border: none;
}

.alert.position-fixed {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Pulsanti con stati */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Miglioramenti responsivi */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .display-1 {
        font-size: 2.5rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 1rem 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .btn.w-100 {
        margin-bottom: 0.5rem;
    }
    
    .row.g-2 > .col-6 {
        margin-bottom: 0.5rem;
    }
    
    h1.h2 {
        font-size: 1.5rem;
    }
    
    .card-header h5 {
        font-size: 1rem;
    }
}

/* Animazioni */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.pulse {
    animation: pulse 2s infinite;
}

/* Stato sistema */
.system-status-operational {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

.system-status-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

.system-status-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}

/* Icone di stato con colori */
.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

/* Hover effects per le azioni */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Loading states */
.loading {
    pointer-events: none;
    opacity: 0.6;
}

.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Focus styles per accessibilità */
.btn:focus,
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Miglioramenti tipografici */
.fw-semibold {
    font-weight: 600;
}

.small, small {
    font-size: 0.875rem;
}

/* Stili per liste */
.list-unstyled li {
    padding: 0.25rem 0;
}

/* Separatori */
hr {
    margin: 1.5rem 0;
    opacity: 0.3;
}

/* Miglioramenti per il layout delle metriche */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

/* Stili per indicatori tempo reale */
.real-time-indicator {
    position: relative;
}

.real-time-indicator::before {
    content: "";
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background-color: #198754;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

/* Sistema di notifiche migliorato */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 350px;
}

/* Dark mode support (opzionale) */
@media (prefers-color-scheme: dark) {
    .bg-light {
        background-color: #212529 !important;
        color: #fff;
    }
    
    .table {
        color: #fff;
    }
    
    .text-muted {
        color: #adb5bd !important;
    }
}

/* Print styles */
@media print {
    .btn, .alert, .position-fixed {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .container-fluid {
        max-width: none;
        padding: 0;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .btn {
        border-width: 2px;
    }
    
    .card {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .alert,
    .progress-bar {
        transition: none;
    }
    
    .pulse,
    .spinner-border,
    @keyframes spin,
    @keyframes pulse {
        animation: none;
    }
}
</style>
@endpush