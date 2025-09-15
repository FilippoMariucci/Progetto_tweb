{{-- 
    Vista Gestione Centri Assistenza - Admin - ORDINAMENTO CORRETTO
    Fix: pulsanti ordinamento Nome, Provincia, Tecnici funzionanti
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

    {{-- Lista Centri CON ORDINAMENTO CORRETTO --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list me-2"></i>
                Centri Assistenza
                @if($centri->total() > 0)
                    <span class="badge bg-info">{{ $centri->total() }}</span>
                @endif
            </h5>
            
            {{-- ORDINAMENTO CORRETTO - Link che mantengono filtri e cambiano ordinamento --}}
            <div class="btn-group" role="group">
                {{-- Ordinamento per Nome --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'nome', 
                    'order' => (request('sort') == 'nome' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'nome' ? 'active' : '' }}">
                    <i class="bi bi-sort-alpha-{{ (request('sort') == 'nome' && request('order') == 'desc') ? 'up' : 'down' }} me-1"></i>
                    Nome
                    @if(request('sort') == 'nome')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
                
                {{-- Ordinamento per Provincia --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'provincia', 
                    'order' => (request('sort') == 'provincia' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'provincia' ? 'active' : '' }}">
                    <i class="bi bi-geo me-1"></i>
                    Provincia
                    @if(request('sort') == 'provincia')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
                
                {{-- Ordinamento per Tecnici --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'tecnici', 
                    'order' => (request('sort') == 'tecnici' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'tecnici' ? 'active' : '' }}">
                    <i class="bi bi-people me-1"></i>
                    Tecnici
                    @if(request('sort') == 'tecnici')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if($centri->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                {{-- HEADER CLICCABILI PER ORDINAMENTO --}}
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'nome', 
                                        'order' => (request('sort') == 'nome' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Centro
                                        @if(request('sort') == 'nome')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'provincia', 
                                        'order' => (request('sort') == 'provincia' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Località
                                        @if(request('sort') == 'provincia')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Contatti</th>
                                <th class="text-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'tecnici', 
                                        'order' => (request('sort') == 'tecnici' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Tecnici
                                        @if(request('sort') == 'tecnici')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center">Stato</th>
                                <th width="150">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($centri as $centro)
                                <tr>
                                    {{-- Nome Centro MIGLIORATO --}}
                                    <td>
                                        <div>
                                            <strong class="text-primary">{{ $centro->nome }}</strong>
                                            @if($centro->tecnici_count > 0)
                                                <span class="badge bg-success badge-sm ms-2">Attivo</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $centro->indirizzo }}
                                            </small>
                                        </div>
                                    </td>
                                    
                                    {{-- Località MIGLIORATA --}}
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $centro->citta }}</span>
                                            @if($centro->provincia)
                                                <br>
                                                <span class="badge bg-info text-white">
                                                    {{ strtoupper($centro->provincia) }}
                                                </span>
                                            @endif
                                            @if($centro->cap)
                                                <br>
                                                <small class="text-muted">CAP: {{ $centro->cap }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- Contatti --}}
                                    <td>
                                        @if($centro->telefono)
                                            <div class="mb-1">
                                                <i class="bi bi-telephone me-1 text-primary"></i>
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if($centro->email)
                                            <div>
                                                <i class="bi bi-envelope me-1 text-info"></i>
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{ Str::limit($centro->email, 25) }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if(!$centro->telefono && !$centro->email)
                                            <span class="text-muted">Non disponibili</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Tecnici MIGLIORATO --}}
                                    <td class="text-center">
                                        @if($centro->tecnici_count > 0)
                                            <div>
                                                <span class="badge bg-success fs-5 px-3 py-2">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $centro->tecnici_count }}
                                                </span>
                                                <br>
                                                <small class="text-success fw-bold">Centro Operativo</small>
                                            </div>
                                        @else
                                            <div>
                                                <span class="badge bg-warning text-dark fs-5 px-3 py-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    0
                                                </span>
                                                <br>
                                                <small class="text-warning fw-bold">Senza Tecnici</small>
                                            </div>
                                        @endif
                                        
                                        {{-- Lista tecnici se presenti --}}
                                        @if($centro->tecnici_count > 0 && $centro->tecnici->count() > 0)
                                            <div class="mt-2">
                                                <button class="btn btn-outline-info btn-xs" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#tecnici-{{ $centro->id }}">
                                                    <i class="bi bi-list me-1"></i>Dettagli
                                                </button>
                                                <div class="collapse mt-2" id="tecnici-{{ $centro->id }}">
                                                    <div class="card card-body p-2">
                                                        @foreach($centro->tecnici as $tecnico)
                                                            <div class="small">
                                                                <i class="bi bi-person me-1"></i>
                                                                {{ $tecnico->nome }} {{ $tecnico->cognome }}
                                                                @if($tecnico->specializzazione)
                                                                    <br><span class="text-muted">{{ $tecnico->specializzazione }}</span>
                                                                @endif
                                                            </div>
                                                            @if(!$loop->last)<hr class="my-1">@endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    {{-- Stato --}}
                                    <td class="text-center">
                                        @if($centro->tecnici_count > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Operativo
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle me-1"></i>In Attesa
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- Azioni --}}
                                    
<td>
    <div class="btn-group" role="group">
        {{-- Visualizza --}}
        <a href="{{ route('admin.centri.show', $centro->id) }}" 
           class="btn btn-outline-primary btn-sm" 
           title="Visualizza dettagli centro" 
           target="_blank">
            <i class="bi bi-eye"></i>
        </a>
        
        {{-- Modifica --}}
        <a href="{{ route('admin.centri.edit', $centro->id) }}" 
           class="btn btn-outline-warning btn-sm" 
           title="Modifica centro">
            <i class="bi bi-pencil"></i>
        </a>

        
        
        {{-- Elimina - Form diretto funzionante --}}
        <form action="{{ route('admin.centri.destroy', $centro) }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Sei sicuro di voler eliminare il centro \"{{ $centro->nome }}\"?\n\nQuesta azione eliminerà anche i riferimenti ai tecnici associati e non può essere annullata.')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="btn btn-outline-danger btn-sm" 
                    title="Elimina centro">
                <i class="bi bi-trash"></i>
            </button>
        </form>
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

    {{-- Info Distribuzione Geografica --}}
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
                            
                            {{-- Azioni Rapide --}}
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- MODAL (assicurati che sia presente) --}}
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
                <p class="fw-bold text-danger" id="centro-name">Nome centro</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione:</strong> Questa azione eliminerà anche i riferimenti 
                    ai tecnici associati a questo centro e non può essere annullata.
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

{{-- JavaScript e Stili --}}
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
/* Miglioramenti tabella */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table th a {
    color: #495057 !important;
    text-decoration: none !important;
    display: block;
    padding: 0.5rem 0;
}

.table th a:hover {
    color: #007bff !important;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.05);
}

/* Card miglioramenti */
.card {
    transition: all 0.2s ease-in-out;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Badge personalizzati */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.badge.fs-5 {
    font-size: 1.1rem !important;
    padding: 0.5rem 0.75rem !important;
}

.badge-sm {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

/* Pulsanti ordinamento */
.btn-group .btn.active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
    font-weight: 600;
}

.btn-group .btn:not(.active):hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
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

/* Collapse dettagli tecnici */
.collapse .card-body {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

/* Link contatti */
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

/* Icone colorate */
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

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Animazioni */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-in-out;
}

/* Accessibilità */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
}

/* Print styles */
@media print {
    .btn, .alert, .modal, .collapse {
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

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .alert {
        transition: none;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
}

/* Custom utility classes */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}

.fs-7 {
    font-size: 0.875rem !important;
}

/* Miglioramento spacing */
.g-3 > * {
    margin-bottom: 1rem;
}

/* Badge stati specifici */
.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/* Sticky header */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Focus states migliorati */
.table th a:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Miglioramenti tipografici */
.fw-bold {
    font-weight: 700;
}

.small, small {
    font-size: 0.875rem;
}

/* Hover su righe tabella */
.table tbody tr {
    cursor: pointer;
}

.table tbody tr:hover .btn {
    opacity: 1;
}

.table tbody tr .btn {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}
</style>
@endpush