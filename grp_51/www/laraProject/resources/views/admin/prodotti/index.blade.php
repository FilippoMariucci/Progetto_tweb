{{-- Vista per gestione prodotti Admin - CORREZIONE ELIMINAZIONE --}}
@extends('layouts.app')

@section('title', 'Gestione Prodotti')

@section('content')
<div class="container-fluid mt-4">
    
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
                                            <i class="bi bi-check-circle text-success fs-5" title="Attivo"></i>
                                        @else
                                            <i class="bi bi-x-circle text-danger fs-5" title="Disattivato"></i>
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
                                    
                                    <!-- Azioni - SEZIONE CORRETTA -->
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
                                                    <li>
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
                                                    </li>
                                                @endif
                                                
                                                {{-- CORREZIONE: Form per eliminazione invece di onclick --}}
                                                <li>
                                                    <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Sei sicuro di voler eliminare il prodotto \"{{ $prodotto->nome }}\"?\n\nQuesta azione non può essere annullata.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i>Elimina
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginazione compatta --}}
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

.table td {
    vertical-align: middle;
    white-space: nowrap;
}

.table td:nth-child(3) { /* Colonna Nome */
    white-space: normal;
    min-width: 200px;
    max-width: 250px;
}

.badge {
    font-size: 0.7rem;
    white-space: nowrap;
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

/* NUOVO: Stile per form eliminazione */
.delete-form {
    display: inline !important;
}

.dropdown-item form {
    margin: 0 !important;
}

.dropdown-item button {
    border: none !important;
    background: none !important;
    padding: 0 !important;
    text-align: left !important;
    width: 100% !important;
    color: inherit !important;
}

.dropdown-item button:hover {
    background: none !important;
    color: inherit !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Imposta token CSRF per tutte le richieste AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    console.log('✅ Admin prodotti inizializzato con azioni bulk corrette');
    
    // === GESTIONE SELEZIONE MULTIPLA ===
    
    /**
     * Seleziona/deseleziona tutti i checkbox quando si clicca sul checkbox principale
     */
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateBulkActionsUI();
        console.log(`🔄 Selezionati tutti: ${isChecked}`);
    });
    
    /**
     * Aggiorna stato del checkbox principale quando cambiano quelli individuali
     */
    $(document).on('change', '.product-checkbox', function() {
        const total = $('.product-checkbox').length;
        const checked = $('.product-checkbox:checked').length;
        
        // Imposta stato indeterminato se alcuni sono selezionati
        $('#selectAllCheckbox').prop('indeterminate', checked > 0 && checked < total);
        $('#selectAllCheckbox').prop('checked', checked === total);
        
        updateBulkActionsUI();
        console.log(`🔄 Selezionati: ${checked}/${total}`);
    });
    
    /**
     * Aggiorna la disponibilità delle azioni bulk in base ai prodotti selezionati
     */
    function updateBulkActionsUI() {
        const selectedCount = $('.product-checkbox:checked').length;
        const hasSelection = selectedCount > 0;
        
        // Abilita/disabilita pulsante azioni
        $('#bulkActions').prop('disabled', !hasSelection);
        
        // Mostra conteggio nella dropdown se ci sono selezioni
        if (hasSelection) {
            $('#bulkActions').html(`<i class="bi bi-three-dots-vertical me-1"></i>Azioni (${selectedCount})`);
        } else {
            $('#bulkActions').html('<i class="bi bi-three-dots-vertical me-1"></i>Azioni');
        }
        
        console.log(`🎯 Azioni bulk ${hasSelection ? 'abilitate' : 'disabilitate'}: ${selectedCount} prodotti`);
    }
    
    // === FILTRI E RICERCA ===
    
    /**
     * Cancella il campo di ricerca e risubmit il form
     */
    $('#clearSearch').on('click', function() {
        $('#search').val('');
        $('#filterForm').submit();
    });
    
    /**
     * Sottometti automaticamente il form quando cambiano i filtri
     */
    $('#status, #staff_id').on('change', function() {
        $('#filterForm').submit();
    });
    
    /**
     * Ricerca con debounce per evitare troppe richieste
     */
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                console.log(`🔍 Auto-ricerca: "${searchTerm}"`);
                // Decommentare se si vuole ricerca automatica:
                // $('#filterForm').submit();
            }
        }, 500);
    });
    
    // === AZIONI SUI PRODOTTI ===
    
    /**
     * Conferma il toggle dello stato attivo/inattivo del prodotto
     */
    window.confirmToggleStatus = function(currentStatus) {
        const action = currentStatus ? 'disattivare' : 'attivare';
        const confirmed = confirm(`Sei sicuro di voler ${action} questo prodotto?`);
        
        if (confirmed) {
            console.log(`🔄 Toggle status prodotto: ${currentStatus ? 'disattiva' : 'attiva'}`);
        }
        
        return confirmed;
    };
    
    // === AZIONI BULK CORRETTE ===
    
    /**
     * Seleziona tutti i prodotti visibili nella pagina
     */
    window.selectAll = function() {
        $('.product-checkbox').prop('checked', true);
        $('#selectAllCheckbox').prop('checked', true);
        updateBulkActionsUI();
        console.log('✅ Tutti i prodotti selezionati');
    };
    
    /**
     * Deseleziona tutti i prodotti
     */
    window.deselectAll = function() {
        $('.product-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        $('#selectAllCheckbox').prop('indeterminate', false);
        updateBulkActionsUI();
        console.log('❌ Tutti i prodotti deselezionati');
    };
    
    /**
     * Attiva tutti i prodotti selezionati
     */
    window.bulkActivate = function() {
        const selected = getSelectedProducts();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto per attivarlo');
            return;
        }
        
        const message = selected.length === 1 ? 
            'Attivare il prodotto selezionato?' : 
            `Attivare ${selected.length} prodotti selezionati?`;
            
        if (confirm(message)) {
            console.log(`🟢 Avvio attivazione bulk: ${selected.length} prodotti`);
            executeBulkAction('activate', selected);
        }
    };
    
    /**
     * Disattiva tutti i prodotti selezionati
     */
    window.bulkDeactivate = function() {
        const selected = getSelectedProducts();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto per disattivarlo');
            return;
        }
        
        const message = selected.length === 1 ? 
            'Disattivare il prodotto selezionato?' : 
            `Disattivare ${selected.length} prodotti selezionati?`;
            
        if (confirm(message)) {
            console.log(`🟡 Avvio disattivazione bulk: ${selected.length} prodotti`);
            executeBulkAction('deactivate', selected);
        }
    };
    
    /**
     * Elimina tutti i prodotti selezionati
     */
    window.bulkDelete = function() {
        const selected = getSelectedProducts();
        
        if (selected.length === 0) {
            alert('⚠️ Seleziona almeno un prodotto per eliminarlo');
            return;
        }
        
        const message = selected.length === 1 ? 
            '🗑️ ATTENZIONE: Eliminare definitivamente il prodotto selezionato?\n\nQuesta azione non può essere annullata.' :
            `🗑️ ATTENZIONE: Eliminare definitivamente ${selected.length} prodotti selezionati?\n\nQuesta azione non può essere annullata.`;
            
        if (confirm(message)) {
            console.log(`🔴 Avvio eliminazione bulk: ${selected.length} prodotti`);
            executeBulkAction('delete', selected);
        }
    };
    
    /**
     * Ottiene gli ID dei prodotti attualmente selezionati
     */
    function getSelectedProducts() {
        const selected = $('.product-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        console.log(`📋 Prodotti selezionati:`, selected);
        return selected;
    }
    
    /**
     * FUNZIONE CRITICA: Esegue l'azione bulk sui prodotti selezionati
     */
    function executeBulkAction(action, productIds) {
        if (!productIds || productIds.length === 0) {
            console.error('❌ Nessun prodotto da processare');
            showToast('Errore: nessun prodotto selezionato', 'error');
            return;
        }
        
        console.log(`🚀 Esecuzione azione bulk:`, {
            action: action,
            productIds: productIds,
            count: productIds.length
        });
        
        // Mostra indicatore di caricamento
        showLoadingOverlay(`Esecuzione ${action} su ${productIds.length} prodotti...`);
        
        // Disabilita pulsanti per evitare click multipli
        $('#bulkActions').prop('disabled', true);
        
        // Esegui chiamata AJAX
        $.ajax({
            url: '{{ route("admin.prodotti.bulk-action") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: action,
                products: productIds
            },
            timeout: 30000, // 30 secondi timeout
            success: function(response) {
                console.log('✅ Risposta bulk action:', response);
                
                hideLoadingOverlay();
                
                if (response.success) {
                    const message = response.message || `Azione ${action} completata con successo`;
                    showToast(message, 'success');
                    
                    // Aggiorna la pagina dopo un breve delay per mostrare il messaggio
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                } else {
                    console.error('❌ Errore nella risposta:', response.message);
                    showToast('Errore: ' + (response.message || 'Operazione fallita'), 'error');
                    $('#bulkActions').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Errore AJAX bulk action:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    xhr: xhr
                });
                
                hideLoadingOverlay();
                $('#bulkActions').prop('disabled', false);
                
                let errorMessage = 'Errore di comunicazione con il server';
                
                if (xhr.status === 422) {
                    // Errore di validazione
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = 'Dati non validi: ' + (errorResponse.message || 'Controlla i dati inseriti');
                    } catch (e) {
                        errorMessage = 'Errore di validazione dei dati';
                    }
                } else if (xhr.status === 403) {
                    errorMessage = 'Non hai i permessi per eseguire questa operazione';
                } else if (xhr.status === 500) {
                    errorMessage = 'Errore interno del server. Riprova più tardi.';
                } else if (status === 'timeout') {
                    errorMessage = 'Operazione scaduta. Il server potrebbe essere sovraccarico.';
                } else if (status === 'abort') {
                    errorMessage = 'Operazione annullata';
                } else if (xhr.status === 0) {
                    errorMessage = 'Errore di connessione. Controlla la tua connessione internet.';
                }
                
                showToast(errorMessage, 'error');
            }
        });
    }
    
    // === UTILITY FUNCTIONS ===
    
    /**
     * Mostra overlay di caricamento
     */
    function showLoadingOverlay(message = 'Caricamento...') {
        // Rimuovi overlay esistenti
        $('#loadingOverlay').remove();
        
        const overlay = $(`
            <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); z-index: 9999;">
                <div class="card text-center p-4">
                    <div class="card-body">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="card-title">${message}</h5>
                        <p class="card-text text-muted">Attendere, operazione in corso...</p>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(overlay);
        console.log(`⏳ Loading overlay mostrato: ${message}`);
    }
    
    /**
     * Nasconde overlay di caricamento
     */
    function hideLoadingOverlay() {
        $('#loadingOverlay').fadeOut(300, function() {
            $(this).remove();
        });
        console.log('✅ Loading overlay nascosto');
    }
    
    /**
     * Mostra notifica toast migliorata
     */
    function showToast(message, type = 'success') {
        // Rimuovi toast precedenti
        $('.toast-notification').remove();
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'check-circle-fill',
            'error': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle-fill', 
            'info': 'info-circle-fill'
        }[type] || 'info-circle-fill';
        
        const toast = $(`
            <div class="toast-notification alert ${alertClass} alert-dismissible fade show position-fixed shadow-lg" 
                 style="top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-${icon} me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        
        console.log(`📢 Toast mostrato (${type}): ${message}`);
        
        // Auto-rimuovi dopo 5 secondi per successo, 10 per errori
        const autoHideDelay = type === 'error' ? 10000 : 5000;
        setTimeout(() => {
            toast.fadeOut(500, () => toast.remove());
        }, autoHideDelay);
    }
    
    /**
     * Gestione responsive della tabella
     */
    function handleResponsiveTable() {
        const isSmallScreen = $(window).width() < 768;
        
        if (isSmallScreen) {
            // Su mobile, nascondi alcune colonne meno importanti
            $('.table th:nth-child(4), .table td:nth-child(4)').hide(); // Modello
            $('.table th:nth-child(6), .table td:nth-child(6)').hide(); // Prezzo  
            $('.table th:nth-child(10), .table td:nth-child(10)').hide(); // Data creazione
        } else {
            // Su desktop, mostra tutte le colonne
            $('.table th, .table td').show();
        }
    }
    
    // Esegui responsive check al caricamento e resize
    handleResponsiveTable();
    $(window).on('resize', handleResponsiveTable);
    
    // === AUTO-REFRESH STATISTICHE ===
    
    /**
     * Aggiorna le statistiche via AJAX ogni 5 minuti
     */
    function updateStatsCards() {
        if ($('.product-checkbox:checked').length > 0) {
            // Non aggiornare se ci sono selezioni attive
            return;
        }
        
        $.get('{{ route("api.admin.stats-update") }}')
            .done(function(response) {
                if (response.success && response.stats) {
                    // Aggiorna i contatori nelle card statistiche
                    const stats = response.stats;
                    const statElements = [
                        { key: 'total_prodotti', selector: '.card-stats:eq(0) h3' },
                        { key: 'attivi', selector: '.card-stats:eq(1) h3' },
                        { key: 'inattivi', selector: '.card-stats:eq(2) h3' },
                        { key: 'con_malfunzionamenti', selector: '.card-stats:eq(3) h3' }
                    ];
                    
                    statElements.forEach(({ key, selector }) => {
                        if (stats[key] !== undefined) {
                            $(selector).text(stats[key]);
                        }
                    });
                    
                    console.log('📊 Statistiche aggiornate automaticamente');
                }
            })
            .fail(function() {
                console.log('⚠️ Errore aggiornamento statistiche automatico (normale se route non esiste)');
            });
    }
    
    // Avvia auto-refresh statistiche ogni 5 minuti
    setInterval(updateStatsCards, 300000);
    
    // === TOOLTIP E ACCESSIBILITÀ ===
    
    // Inizializza tooltip se Bootstrap li supporta
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
    }
    
    // Inizializza stato UI
    updateBulkActionsUI();
    
    console.log('🎉 Sistema admin prodotti completamente inizializzato');
});

/**
 * Funzioni globali di utilità per debugging
 */
window.debugBulkActions = function() {
    const selected = $('.product-checkbox:checked').length;
    const total = $('.product-checkbox').length;
    
    console.log('🔍 Debug Bulk Actions:', {
        prodotti_totali: total,
        prodotti_selezionati: selected,
        pulsante_abilitato: !$('#bulkActions').prop('disabled'),
        csrf_token: $('meta[name="csrf-token"]').attr('content'),
        route_bulk_action: '{{ route("admin.prodotti.bulk-action") }}'
    });
};

// Debugging automatico ogni 30 secondi in ambiente di sviluppo
@if(app()->environment('local'))
setInterval(() => {
    if ($('.product-checkbox:checked').length > 0) {
        window.debugBulkActions();
    }
}, 30000);
@endif
</script>
@endpush