{{-- Vista per gestione prodotti Admin --}}
@extends('layouts.app')

@section('title', 'Gestione Prodotti')

@section('content')
<div class="container-fluid mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item active">Gestione Prodotti</li>
        </ol>
    </nav>

    <!-- === HEADER CON STATISTICHE === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill text-primary me-3 fs-2"></i>
                    <div>
                        <h1 class="h2 mb-1">Gestione Prodotti</h1>
                        <p class="text-muted mb-0">
                            Amministrazione completa del catalogo prodotti
                        </p>
                    </div>
                </div>
                
                <!-- Pulsante Nuovo Prodotto -->
                <div>
                    <a href="{{ route('admin.prodotti.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Nuovo Prodotto
                    </a>
                </div>
            </div>
            
            <!-- Statistiche Rapide -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-stats bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-1">{{ $stats['total_prodotti'] ?? 0 }}</h3>
                                    <p class="mb-0">Prodotti Totali</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-box fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-stats bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-1">{{ $stats['attivi'] ?? 0 }}</h3>
                                    <p class="mb-0">Attivi</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-stats bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-1">{{ $stats['inattivi'] ?? 0 }}</h3>
                                    <p class="mb-0">Disattivati</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-x-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-stats bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-1">{{ $stats['con_malfunzionamenti'] ?? 0 }}</h3>
                                    <p class="mb-0">Con Problemi</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === FILTRI E RICERCA === -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filtri e Ricerca
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.prodotti.index') }}" id="filterForm">
                <div class="row">
                    <!-- Ricerca -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label fw-semibold">Ricerca</label>
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="form-control" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome, modello, descrizione...">
                            <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Stato -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label fw-semibold">Stato</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tutti</option>
                            <option value="attivi" {{ request('status') == 'attivi' ? 'selected' : '' }}>
                                ✅ Attivi
                            </option>
                            <option value="inattivi" {{ request('status') == 'inattivi' ? 'selected' : '' }}>
                                ❌ Disattivati
                            </option>
                        </select>
                    </div>
                    
                    <!-- Staff Assegnato -->
                    <div class="col-md-3 mb-3">
                        <label for="staff_id" class="form-label fw-semibold">Staff Assegnato</label>
                        <select name="staff_id" id="staff_id" class="form-select">
                            <option value="">Tutti</option>
                            <option value="0" {{ request('staff_id') === '0' ? 'selected' : '' }}>
                                🚫 Non Assegnati
                            </option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                    👤 {{ $staff->nome }} {{ $staff->cognome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Pulsanti Azione -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Cerca
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filtri Attivi -->
                @if(request()->hasAny(['search', 'status', 'staff_id']))
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-info">Filtri attivi:</span>
                                
                                @if(request('search'))
                                    <span class="badge bg-secondary">
                                        Ricerca: "{{ request('search') }}"
                                        <a href="{{ request()->url() }}" class="text-white ms-1">×</a>
                                    </span>
                                @endif
                                
                                @if(request('status'))
                                    <span class="badge bg-secondary">
                                        Stato: {{ request('status') == 'attivi' ? 'Attivi' : 'Disattivati' }}
                                    </span>
                                @endif
                                
                                @if(request('staff_id'))
                                    <span class="badge bg-secondary">
                                        Staff: {{ request('staff_id') === '0' ? 'Non Assegnati' : $staffMembers->find(request('staff_id'))->nome_completo ?? 'N/A' }}
                                    </span>
                                @endif
                                
                                <a href="{{ route('admin.prodotti.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- === TABELLA PRODOTTI === -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>Elenco Prodotti
                <span class="badge bg-primary ms-2">{{ $prodotti->total() }}</span>
            </h5>
            
            <!-- Azioni Bulk -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="bulkActions" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical me-1"></i>Azioni
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="selectAll()">
                        <i class="bi bi-check-all me-2"></i>Seleziona Tutti
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="deselectAll()">
                        <i class="bi bi-x-square me-2"></i>Deseleziona Tutti
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-success" href="#" onclick="bulkActivate()">
                        <i class="bi bi-check-circle me-2"></i>Attiva Selezionati
                    </a></li>
                    <li><a class="dropdown-item text-warning" href="#" onclick="bulkDeactivate()">
                        <i class="bi bi-x-circle me-2"></i>Disattiva Selezionati
                    </a></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="bulkDelete()">
                        <i class="bi bi-trash me-2"></i>Elimina Selezionati
                    </a></li>
                </ul>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($prodotti->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                </th>
                                <th style="width: 80px;">Foto</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nome', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-white text-decoration-none">
                                        Nome Prodotto
                                        @if(request('sort') == 'nome')
                                            <i class="bi bi-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Modello</th>
                                <th>Categoria</th>
                                <th>Prezzo</th>
                                <th>Stato</th>
                                <th>Staff</th>
                                <th>Problemi</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-white text-decoration-none">
                                        Creato
                                        @if(request('sort') == 'created_at')
                                            <i class="bi bi-arrow-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th style="width: 120px;">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prodotti as $prodotto)
                                <tr class="align-middle">
                                    <!-- Checkbox -->
                                    <td>
                                        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $prodotto->id }}">
                                    </td>
                                    
                                    <!-- Foto -->
                                    <td>
                                        @if($prodotto->foto)
                                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="{{ $prodotto->nome }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Nome -->
                                    <td>
                                        <div>
                                            <strong>{{ $prodotto->nome }}</strong>
                                            @if(!$prodotto->attivo)
                                                <span class="badge bg-warning text-dark ms-1">Disattivato</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            {{ Str::limit($prodotto->descrizione, 60) }}
                                        </small>
                                    </td>
                                    
                                    <!-- Modello -->
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $prodotto->modello }}</code>
                                    </td>
                                    
                                    <!-- Categoria -->
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $prodotto->categoria_label }}
                                        </span>
                                    </td>
                                    
                                    <!-- Prezzo -->
                                    <td>
                                        @if($prodotto->prezzo)
                                            <strong>€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Stato -->
                                    <td>
                                        @if($prodotto->attivo)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Attivo
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Disattivo
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Staff Assegnato -->
                                    <td>
                                        @if($prodotto->staffAssegnato)
                                            <span class="badge bg-primary">
                                                {{ $prodotto->staffAssegnato->nome }} {{ $prodotto->staffAssegnato->cognome }}
                                            </span>
                                        @else
                                            <span class="text-muted">Non assegnato</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Malfunzionamenti -->
                                    <td>
                                        @if($prodotto->malfunzionamenti_count > 0)
                                            <span class="badge bg-warning">
                                                {{ $prodotto->malfunzionamenti_count }} problema{{ $prodotto->malfunzionamenti_count > 1 ? 'i' : '' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Nessuno</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Data Creazione -->
                                    <td>
                                        <small>
                                            {{ $prodotto->created_at->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $prodotto->created_at->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    
                                    <!-- Azioni -->
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.prodotti.show', $prodotto) }}">
                                                        <i class="bi bi-eye me-2"></i>Visualizza
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.prodotti.edit', $prodotto) }}">
                                                        <i class="bi bi-pencil me-2"></i>Modifica
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                {{-- Toggle stato attivo/inattivo --}}
                    @if(Route::has('admin.prodotti.toggle-status'))
    <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
          method="POST" 
          onsubmit="return confirmToggleStatus({{ $prodotto->attivo ? 'true' : 'false' }})">
        @csrf
        <button type="submit" 
                class="dropdown-item {{ $prodotto->attivo ? 'text-danger' : 'text-success' }}">
            <i class="bi bi-{{ $prodotto->attivo ? 'pause' : 'play' }} me-2"></i>
            {{ $prodotto->attivo ? 'Disattiva' : 'Attiva' }}
        </button>
    </form>
@endif
                                                <li>
                                                    <a class="dropdown-item text-danger" 
                                                       href="#" 
                                                       onclick="deleteProduct({{ $prodotto->id }}, '{{ $prodotto->nome }}')">
                                                        <i class="bi bi-trash me-2"></i>Elimina
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
               {{-- Paginazione piccola e allineata --}}
    @if($prodotti->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- Info paginazione a sinistra --}}
                    <small class="text-muted">
                        {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} di {{ $prodotti->total() }}
                    </small>
                    
                    {{-- Controlli paginazione piccoli a destra --}}
                    <nav aria-label="Paginazione prodotti">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous --}}
                            @if ($prodotti->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                </li>
                            @endif

                            {{-- Numeri pagina --}}
                            @foreach ($prodotti->getUrlRange(1, $prodotti->lastPage()) as $page => $url)
                                @if ($page == $prodotti->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($prodotti->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->nextPageUrl() }}">›</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    @endif
            @else
                <!-- Nessun Risultato -->
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun prodotto trovato</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'staff_id']))
                            Prova a modificare i filtri di ricerca
                        @else
                            Non ci sono ancora prodotti nel catalogo
                        @endif
                    </p>
                    <div class="mt-3">
                        @if(request()->hasAny(['search', 'status', 'staff_id']))
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filtri
                            </a>
                        @endif
                        <a href="{{ route('admin.prodotti.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Prodotto
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')

<style>
/* Stile paginazione compatta identico all'immagine */
nav[aria-label="Paginazione prodotti"] .pagination,
.pagination-compact .pagination,
.pagination {
    margin-bottom: 0 !important;
    justify-content: center !important;
    display: flex !important;
    gap: 4px !important;
}

nav[aria-label="Paginazione prodotti"] .pagination .page-item,
.pagination-compact .pagination .page-item,
.pagination .page-item {
    margin: 0 !important;
}

nav[aria-label="Paginazione prodotti"] .pagination .page-link,
.pagination-compact .pagination .page-link,
.pagination .page-link {
    border: 1px solid #dee2e6 !important;
    border-radius: 6px !important;
    color: #6c757d !important;
    background-color: #fff !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;
    text-decoration: none !important;
    margin: 0 !important;
    min-width: 32px !important;
    height: 32px !important;
    text-align: center !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: none !important;
}

nav[aria-label="Paginazione prodotti"] .pagination .page-link:hover,
.pagination-compact .pagination .page-link:hover,
.pagination .page-link:hover {
    color: #495057 !important;
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    text-decoration: none !important;
    transform: none !important;
}

nav[aria-label="Paginazione prodotti"] .pagination .page-item.active .page-link,
.pagination-compact .pagination .page-item.active .page-link,
.pagination .page-item.active .page-link {
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
    font-weight: 500 !important;
    z-index: 1 !important;
}

nav[aria-label="Paginazione prodotti"] .pagination .page-item.disabled .page-link,
.pagination-compact .pagination .page-item.disabled .page-link,
.pagination .page-item.disabled .page-link {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    opacity: 0.65 !important;
    cursor: not-allowed !important;
}

/* Frecce specifiche - più piccole */
nav[aria-label="Paginazione prodotti"] .pagination .page-link[rel="prev"],
nav[aria-label="Paginazione prodotti"] .pagination .page-link[rel="next"],
.pagination .page-link[rel="prev"],
.pagination .page-link[rel="next"] {
    font-size: 12px !important;
    padding: 6px 10px !important;
    min-width: 32px !important;
}

/* Rimuovi tutti gli stili extra che possono interferire */
nav[aria-label="Paginazione prodotti"] .pagination .page-link:focus,
.pagination .page-link:focus {
    box-shadow: none !important;
    outline: 2px solid #007bff !important;
    outline-offset: 2px !important;
}

/* Su mobile ancora più compatto */
@media (max-width: 768px) {
    nav[aria-label="Paginazione prodotti"] .pagination .page-link,
    .pagination .page-link {
        padding: 4px 8px !important;
        font-size: 12px !important;
        min-width: 28px !important;
        height: 28px !important;
    }
    
    nav[aria-label="Paginazione prodotti"] .pagination,
    .pagination {
        gap: 2px !important;
    }
}

.card-stats {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.card-stats:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.img-thumbnail {
    border: 2px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
}

/* Custom dropdown styling */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-stats h3 {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === GESTIONE SELEZIONE MULTIPLA ===
    
    /**
     * Seleziona/deseleziona tutti i checkbox
     */
    $('#selectAllCheckbox').on('change', function() {
        $('.product-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });
    
    /**
     * Aggiorna stato del checkbox principale quando cambiano quelli individuali
     */
    $('.product-checkbox').on('change', function() {
        const total = $('.product-checkbox').length;
        const checked = $('.product-checkbox:checked').length;
        
        $('#selectAllCheckbox').prop('indeterminate', checked > 0 && checked < total);
        $('#selectAllCheckbox').prop('checked', checked === total);
        
        updateBulkActions();
    });
    
    /**
     * Aggiorna la disponibilità delle azioni bulk
     */
    function updateBulkActions() {
        const selected = $('.product-checkbox:checked').length;
        $('#bulkActions').prop('disabled', selected === 0);
    }
    
    // === FILTRI E RICERCA ===
    
    /**
     * Cancella il campo di ricerca
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('');
        $('#filterForm').submit();
    });
    
    /**
     * Sottometti il form quando cambiano i filtri
     */
    $('#status, #staff_id').on('change', function() {
        $('#filterForm').submit();
    });
    
    /**
     * Ricerca in tempo reale con debounce
     */
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if ($(this).val().length >= 3 || $(this).val().length === 0) {
                // Auto-submit per ricerche di 3+ caratteri o quando svuotato
                // $('#filterForm').submit();
            }
        }, 500);
    });
    
    // === AZIONI SUI PRODOTTI ===
    
    /**
     * Mostra/nasconde stato prodotto
     */
    window.toggleStatus = function(productId, newStatus) {
        const action = newStatus ? 'attivare' : 'disattivare';
        
        if (confirm(`Sei sicuro di voler ${action} questo prodotto?`)) {
            // Simula chiamata AJAX
            $.post(`/admin/prodotti/${productId}/toggle-status`, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: newStatus
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Errore: ' + response.message);
                }
            })
            .fail(function() {
                alert('Errore nella comunicazione con il server');
            });
        }
    };
    
    /**
     * Elimina prodotto
     */
    window.deleteProduct = function(productId, productName) {
        if (confirm(`Sei sicuro di voler eliminare il prodotto "${productName}"?\n\nQuesta azione non può essere annullata.`)) {
            // Crea form per DELETE request
            const form = $('<form>', {
                method: 'POST',
                action: `/admin/prodotti/${productId}`
            });
            
            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: $('meta[name="csrf-token"]').attr('content')
            }));
            
            form.append($('<input>', {
                type: 'hidden',
                name: '_method',
                value: 'DELETE'
            }));
            
            $('body').append(form);
            form.submit();
        }
    };
    
    // === AZIONI BULK ===
    
    /**
     * Seleziona tutti i prodotti
     */
    window.selectAll = function() {
        $('.product-checkbox').prop('checked', true);
        $('#selectAllCheckbox').prop('checked', true);
        updateBulkActions();
    };
    
    /**
     * Deseleziona tutti i prodotti
     */
    window.deselectAll = function() {
        $('.product-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        updateBulkActions();
    };
    
    /**
     * Attiva prodotti selezionati
     */
    window.bulkActivate = function() {
        const selected = getSelectedProducts();
        if (selected.length === 0) {
            alert('Seleziona almeno un prodotto');
            return;
        }
        
        if (confirm(`Attivare ${selected.length} prodotti selezionati?`)) {
            bulkAction('activate', selected);
        }
    };
    
    /**
     * Disattiva prodotti selezionati
     */
    window.bulkDeactivate = function() {
        const selected = getSelectedProducts();
        if (selected.length === 0) {
            alert('Seleziona almeno un prodotto');
            return;
        }
        
        if (confirm(`Disattivare ${selected.length} prodotti selezionati?`)) {
            bulkAction('deactivate', selected);
        }
    };
    
    /**
     * Elimina prodotti selezionati
     */
    window.bulkDelete = function() {
        const selected = getSelectedProducts();
        if (selected.length === 0) {
            alert('Seleziona almeno un prodotto');
            return;
        }
        
        if (confirm(`ATTENZIONE: Eliminare definitivamente ${selected.length} prodotti selezionati?\n\nQuesta azione non può essere annullata.`)) {
            bulkAction('delete', selected);
        }
    };
    
    /**
     * Ottiene gli ID dei prodotti selezionati
     */
    function getSelectedProducts() {
        return $('.product-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
    }
    
    /**
     * Esegue un'azione bulk
     */
    function bulkAction(action, productIds) {
        $.post('/admin/prodotti/bulk-action', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action: action,
            products: productIds
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Errore: ' + response.message);
            }
        })
        .fail(function() {
            alert('Errore nella comunicazione con il server');
        });
    }
    
    // === MIGLIORAMENTI UX ===
    
    /**
     * Tooltip per elementi con data-bs-toggle
     */
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    /**
     * Auto-refresh ogni 5 minuti per mantenere i dati aggiornati
     */
    let autoRefreshInterval;
    
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            // Solo se non ci sono checkbox selezionati
            if ($('.product-checkbox:checked').length === 0) {
                // Refresh silenzioso dei dati
                updateStats();
            }
        }, 300000); // 5 minuti
    }
    
    /**
     * Aggiorna le statistiche via AJAX
     */
    function updateStats() {
        $.get('/api/admin/stats')
            .done(function(response) {
                if (response.success) {
                    // Aggiorna i contatori nelle card
                    $('.card-stats').each(function(index) {
                        const statTypes = ['total_prodotti', 'attivi', 'inattivi', 'con_malfunzionamenti'];
                        if (statTypes[index] && response.stats[statTypes[index]]) {
                            $(this).find('h3').text(response.stats[statTypes[index]]);
                        }
                    });
                }
            })
            .fail(function() {
                console.log('Errore nell\'aggiornamento statistiche');
            });
    }
    
    /**
     * Mostra indicatore di caricamento durante le azioni
     */
    function showLoading() {
        $('body').append(`
            <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); z-index: 9999;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
            </div>
        `);
    }
    
    function hideLoading() {
        $('#loadingOverlay').remove();
    }
    
    /**
     * Gestione responsive della tabella
     */
    function handleResponsiveTable() {
        if ($(window).width() < 768) {
            // Su mobile, nascondi alcune colonne meno importanti
            $('.table th:nth-child(4), .table td:nth-child(4)').hide(); // Modello
            $('.table th:nth-child(6), .table td:nth-child(6)').hide(); // Prezzo
            $('.table th:nth-child(10), .table td:nth-child(10)').hide(); // Data creazione
        } else {
            $('.table th, .table td').show();
        }
    }
    
    // Esegui al caricamento e ridimensionamento finestra
    handleResponsiveTable();
    $(window).resize(handleResponsiveTable);
    
    // Avvia auto-refresh
    startAutoRefresh();
    
    // Inizializza stato bulk actions
    updateBulkActions();
    
    console.log('✅ Dashboard admin prodotti inizializzata');
});

// === FUNZIONI GLOBALI UTILITY ===

/**
 * Formatta numeri per visualizzazione
 */
function formatNumber(num) {
    return new Intl.NumberFormat('it-IT').format(num);
}

/**
 * Formatta valute
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

/**
 * Mostra notifica toast
 */
function showToast(message, type = 'success') {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Rimuovi dopo 5 secondi
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endpush