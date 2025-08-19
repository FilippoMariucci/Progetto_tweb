{{-- 
    Vista Gestione Centri Assistenza - Admin
    File: resources/views/admin/centri/index.blade.php
    
    Questa vista gestisce la visualizzazione e amministrazione dei centri di assistenza
    Funzionalità opzionale per gestire l'archivio dei centri assistenza esterni
--}}
@extends('layouts.app')

@section('title', 'Gestione Centri Assistenza - Admin')

@section('content')
<div class="container-fluid mt-4">
    {{-- Header della pagina --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-geo-alt text-info me-2"></i>
                        Gestione Centri Assistenza
                    </h1>
                    <p class="text-muted mb-0">
                        Amministra i centri di assistenza tecnica sul territorio
                        <span class="badge bg-info ms-2">Funzionalità Opzionale</span>
                    </p>
                </div>
                <div>
                    {{-- Pulsanti azioni principali --}}
                    <a href="{{ route('admin.centri.create') }}" class="btn btn-info me-2">
                        <i class="bi bi-plus-circle me-1"></i>Nuovo Centro
                    </a>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtri e Ricerca --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Filtri e Ricerca
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.centri.index') }}" class="row g-3">
                {{-- Ricerca per nome --}}
                <div class="col-md-4">
                    <label for="search" class="form-label">Ricerca Centro</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome centro...">
                </div>
                
                {{-- Filtro per provincia --}}
                <div class="col-md-3">
                    <label for="provincia" class="form-label">Provincia</label>
                    <select class="form-select" id="provincia" name="provincia">
                        <option value="">Tutte le province</option>
                        @if(isset($province) && count($province) > 0)
                            @foreach($province as $prov)
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>
                                    {{ strtoupper($prov) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                {{-- Filtro per città --}}
                <div class="col-md-3">
                    <label for="citta" class="form-label">Città</label>
                    <input type="text" class="form-control" id="citta" name="citta" 
                           value="{{ request('citta') }}" placeholder="Nome città...">
                </div>
                
                {{-- Pulsanti filtro --}}
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filtra
                        </button>
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistiche Centri --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-building display-4 text-info mb-2"></i>
                    <h3 class="mb-1">{{ $centri->total() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Centri Totali</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people display-4 text-success mb-2"></i>
                    <h3 class="mb-1">
                        {{ $centri->sum('tecnici_count') ?? 0 }}
                    </h3>
                    <p class="text-muted mb-0">Tecnici Totali</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-check-circle display-4 text-primary mb-2"></i>
                    <h3 class="mb-1">
                        {{ $centri->where('tecnici_count', '>', 0)->count() }}
                    </h3>
                    <p class="text-muted mb-0">Con Tecnici</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-map display-4 text-warning mb-2"></i>
                    <h3 class="mb-1">
                        {{ $centri->pluck('provincia')->unique()->count() }}
                    </h3>
                    <p class="text-muted mb-0">Province Coperte</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista Centri --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list me-2"></i>
                Centri Assistenza
                @if($centri->total() > 0)
                    <span class="badge bg-info">{{ $centri->total() }}</span>
                @endif
            </h5>
            
            {{-- Ordinamento --}}
            <div class="btn-group" role="group">
                <a href="{{ route('admin.centri.index', array_merge(request()->query(), ['sort' => 'nome'])) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'nome' ? 'active' : '' }}">
                    <i class="bi bi-sort-alpha-down me-1"></i>Nome
                </a>
                <a href="{{ route('admin.centri.index', array_merge(request()->query(), ['sort' => 'provincia'])) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'provincia' ? 'active' : '' }}">
                    <i class="bi bi-geo me-1"></i>Provincia
                </a>
                <a href="{{ route('admin.centri.index', array_merge(request()->query(), ['sort' => 'tecnici'])) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'tecnici' ? 'active' : '' }}">
                    <i class="bi bi-people me-1"></i>Tecnici
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if($centri->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Centro</th>
                                <th>Località</th>
                                <th>Contatti</th>
                                <th class="text-center">Tecnici</th>
                                <th class="text-center">Stato</th>
                                <th width="150">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($centri as $centro)
                                <tr>
                                    {{-- Nome Centro --}}
                                    <td>
                                        <div>
                                            <strong>{{ $centro->nome }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $centro->indirizzo }}
                                            </small>
                                        </div>
                                    </td>
                                    
                                    {{-- Località --}}
                                    <td>
                                        <span class="fw-semibold">{{ $centro->citta }}</span>
                                        @if($centro->provincia)
                                            <br>
                                            <span class="badge bg-secondary">{{ strtoupper($centro->provincia) }}</span>
                                        @endif
                                        @if($centro->cap)
                                            <br>
                                            <small class="text-muted">{{ $centro->cap }}</small>
                                        @endif
                                    </td>
                                    
                                    {{-- Contatti --}}
                                    <td>
                                        @if($centro->telefono)
                                            <div class="mb-1">
                                                <i class="bi bi-telephone me-1 text-primary"></i>
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono_formattato ?? $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if($centro->email)
                                            <div>
                                                <i class="bi bi-envelope me-1 text-info"></i>
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{ $centro->email }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if(!$centro->telefono && !$centro->email)
                                            <span class="text-muted">Non disponibili</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Tecnici --}}
                                    <td class="text-center">
                                        @if($centro->tecnici_count > 0)
                                            <span class="badge bg-success fs-6">
                                                {{ $centro->tecnici_count }}
                                            </span>
                                            <br>
                                            <small class="text-success">Attivo</small>
                                        @else
                                            <span class="badge bg-warning text-dark fs-6">0</span>
                                            <br>
                                            <small class="text-warning">Senza tecnici</small>
                                        @endif
                                    </td>
                                    
                                    {{-- Stato --}}
                                    <td class="text-center">
                                        @if($centro->isAperto())
                                            <span class="badge bg-success">
                                                <i class="bi bi-clock me-1"></i>Aperto
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-clock me-1"></i>Chiuso
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- Azioni --}}
                                    <td>
                                        <div class="btn-group" role="group">
                                            {{-- Visualizza --}}
                                            <a href="{{ route('centri.show', $centro->id) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Visualizza dettagli" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            {{-- Modifica --}}
                                            <a href="{{ route('admin.centri.edit', $centro->id) }}" 
                                               class="btn btn-outline-warning btn-sm" 
                                               title="Modifica centro">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            {{-- Elimina --}}
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Elimina centro"
                                                    onclick="confirmDelete({{ $centro->id }}, '{{ $centro->nome }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginazione --}}
                @if($centri->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Visualizzati {{ $centri->firstItem() }}-{{ $centri->lastItem() }} 
                                di {{ $centri->total() }} centri
                            </small>
                        </div>
                        <div>
                            {{ $centri->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
                
            @else
                {{-- Nessun centro trovato --}}
                <div class="text-center py-5">
                    <i class="bi bi-building display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun Centro Trovato</h4>
                    @if(request()->hasAny(['search', 'provincia', 'citta']))
                        <p class="text-muted mb-3">
                            Nessun centro corrisponde ai filtri selezionati.
                        </p>
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Rimuovi Filtri
                        </a>
                    @else
                        <p class="text-muted mb-3">
                            Non ci sono centri di assistenza nel database.
                        </p>
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-info">
                            <i class="bi bi-plus-circle me-1"></i>Crea il Primo Centro
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Mappa Distribuzione (se ci sono centri) --}}
    @if($centri->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-map me-2"></i>
                            Distribuzione Geografica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Distribuzione per Provincia --}}
                            <div class="col-md-6">
                                <h6>Per Provincia</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Provincia</th>
                                                <th class="text-center">Centri</th>
                                                <th class="text-center">Tecnici</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $byProvincia = $centri->groupBy('provincia');
                                            @endphp
                                            @foreach($byProvincia as $provincia => $centri_prov)
                                                <tr>
                                                    <td>
                                                        <strong>{{ strtoupper($provincia) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">{{ $centri_prov->count() }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">{{ $centri_prov->sum('tecnici_count') }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            {{-- Link Utili --}}
                            <div class="col-md-6">
                                <h6>Azioni Rapide</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('centri.index') }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye me-1"></i>Visualizza Lista Pubblica
                                    </a>
                                    
                                    <button class="btn btn-outline-info" onclick="exportCentri()">
                                        <i class="bi bi-download me-1"></i>Esporta Lista Centri
                                    </button>
                                    
                                    <a href="{{ route('admin.centri.create') }}" class="btn btn-info">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo Centro
                                    </a>
                                </div>
                                
                                {{-- Info aggiuntive --}}
                                <div class="mt-3">
                                    <h6>Informazioni</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-info-circle text-info me-1"></i> 
                                            I centri sono visibili pubblicamente
                                        </li>
                                        <li><i class="bi bi-people text-success me-1"></i> 
                                            I tecnici vengono assegnati ai centri
                                        </li>
                                        <li><i class="bi bi-geo-alt text-warning me-1"></i> 
                                            Verifica la copertura geografica
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal per conferma eliminazione --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare il centro assistenza:</p>
                <p class="fw-bold text-danger" id="centro-name"></p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione:</strong> Questa azione eliminerà anche i riferimenti 
                    ai tecnici associati a questo centro.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annulla
                </button>
                <form id="delete-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina Centro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JavaScript per gestione interazioni --}}
@push('scripts')
<script>
/**
 * JavaScript per la gestione centri assistenza
 * Gestisce eliminazione, export e filtri dinamici
 */

$(document).ready(function() {
    console.log('🏢 Inizializzazione gestione centri assistenza');
    
    // Inizializza tooltips
    initializeTooltips();
    
    // Filtri dinamici
    setupDynamicFilters();
});

/**
 * Inizializza i tooltips Bootstrap
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Configura filtri dinamici
 */
function setupDynamicFilters() {
    // Filtro provincia che aggiorna città
    $('#provincia').on('change', function() {
        const provincia = $(this).val();
        
        if (provincia) {
            // Potresti implementare un caricamento dinamico delle città
            console.log('🌍 Provincia selezionata:', provincia);
        }
    });
    
    // Auto-submit del form dopo selezione
    $('#provincia, #citta').on('change', function() {
        $(this).closest('form').submit();
    });
}

/**
 * Conferma eliminazione centro
 */
function confirmDelete(centroId, centroName) {
    // Aggiorna il modal con le informazioni del centro
    $('#centro-name').text(centroName);
    $('#delete-form').attr('action', `/admin/centri/${centroId}`);
    
    // Mostra il modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

/**
 * Esporta lista centri in formato CSV
 */
function exportCentri() {
    // Crea CSV con i dati visibili
    const rows = [];
    
    // Header CSV
    rows.push(['Nome Centro', 'Città', 'Provincia', 'CAP', 'Indirizzo', 'Telefono', 'Email', 'Tecnici']);
    
    // Dati dalle righe della tabella
    $('tbody tr').each(function() {
        const row = [];
        const $tr = $(this);
        
        // Estrae i dati dalle celle
        const nome = $tr.find('td:first strong').text().trim();
        const indirizzo = $tr.find('td:first small').text().replace('📍 ', '').trim();
        const citta = $tr.find('td:nth-child(2) .fw-semibold').text().trim();
        const provincia = $tr.find('td:nth-child(2) .badge').text().trim();
        const telefono = $tr.find('td:nth-child(3) a[href^="tel:"]').text().trim();
        const email = $tr.find('td:nth-child(3) a[href^="mailto:"]').text().trim();
        const tecnici = $tr.find('td:nth-child(4) .badge').text().trim();
        
        rows.push([nome, citta, provincia, '', indirizzo, telefono, email, tecnici]);
    });
    
    // Crea e scarica il file CSV
    const csvContent = rows.map(row => 
        row.map(field => `"${field}"`).join(',')
    ).join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `centri_assistenza_${new Date().toISOString().slice(0,10)}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('File CSV esportato con successo', 'success');
    } else {
        showNotification('Export non supportato dal browser', 'warning');
    }
}

/**
 * Mostra notificazioni toast
 */
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(alert);
    
    setTimeout(() => {
        alert.alert('close');
    }, 4000);
}

/**
 * Aggiorna contatori in tempo reale (opzionale)
 */
function updateCounters() {
    const totalRows = $('tbody tr').length;
    const activeRows = $('tbody tr:has(.badge.bg-success)').length;
    
    console.log(`📊 Centri totali: ${totalRows}, Centri attivi: ${activeRows}`);
}

// Chiama aggiornamento contatori
updateCounters();

console.log('✅ Gestione centri assistenza inizializzata');
</script>

{{-- Stili personalizzati --}}
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

/* Tabella migliorata */
.table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.05);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

/* Badge personalizzati */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.badge.fs-6 {
    font-size: 1rem !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
}

/* Stati centro */
.centro-active {
    border-left: 4px solid #198754;
}

.centro-inactive {
    border-left: 4px solid #ffc107;
}

/* Miglioramenti pulsanti */
.btn-group .btn.active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

/* Icone con colori */
.text-primary {
    color: #0d6efd !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

/* Modal migliorato */
.modal-content {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
}

/* Animazioni */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-in-out;
}

/* Hover effects per azioni */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Miglioramenti accessibilità */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
}

/* Stati vuoti */
.text-center .display-1 {
    opacity: 0.3;
}

/* Print styles */
@media print {
    .btn, .alert, .modal {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .table {
        font-size: 0.8rem;
    }
}

/* Miglioramenti tipografici */
.fw-semibold {
    font-weight: 600;
}

.small, small {
    font-size: 0.875rem;
}

/* Badge per funzionalità opzionale */
.badge.bg-info {
    background-color: #0dcaf0 !important;
}

/* Stili per link contatti */
a[href^="tel:"], 
a[href^="mailto:"] {
    color: inherit;
    text-decoration: none;
}

a[href^="tel:"]:hover, 
a[href^="mailto:"]:hover {
    color: #0d6efd;
    text-decoration: underline;
}

/* Distribuzioni geografiche */
.geografia-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.5rem;
    padding: 1rem;
}

/* Card statistiche */
.stat-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.stat-card:hover {
    transform: scale(1.02);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Filtri attivi */
.filter-active {
    background-color: #e7f3ff;
    border-color: #0d6efd;
}

/* Separatori */
hr {
    margin: 1.5rem 0;
    opacity: 0.3;
}

/* Tooltip personalizzati */
.tooltip {
    font-size: 0.875rem;
}

/* Miglioramenti form */
.form-label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

/* Status indicators */
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

.status-indicator.offline {
    background-color: #6c757d;
}

/* Responsive table improvements */
@media (max-width: 992px) {
    .table-responsive table {
        min-width: 800px;
    }
}

@media (max-width: 576px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .card-body {
        padding: 1rem 0.75rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .table-light {
        background-color: #495057 !important;
        color: #fff !important;
    }
    
    .bg-light {
        background-color: #343a40 !important;
        color: #fff !important;
    }
}

/* High contrast support */
@media (prefers-contrast: high) {
    .btn {
        border-width: 2px;
    }
    
    .card {
        border: 2px solid #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .alert {
        transition: none;
    }
    
    .stat-card:hover {
        transform: none;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
}
</style>
@endpush