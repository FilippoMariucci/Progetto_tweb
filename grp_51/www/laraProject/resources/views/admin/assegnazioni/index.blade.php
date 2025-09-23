{{-- 
/**
 * BLADE TEMPLATE: Gestione Assegnazioni Prodotti - TechSupport Pro Gruppo 51
 * 
 * TECNOLOGIE: Blade (Laravel) + HTML5 + Bootstrap 5 + PHP + JavaScript
 * PATTERN: MVC View Layer + Template Inheritance + Component-Based CSS
 * SCOPO: Vista admin per assegnazione prodotti a staff con filtri e bulk operations
 */
--}}

{{-- BLADE INHERITANCE: Estende layout principale con sezioni specifiche --}}
@extends('layouts.app')
@section('title', 'Gestione Assegnazioni Prodotti')

@section('content')
<div class="container mt-4">
    
    {{-- HEADER SECTION: Bootstrap Flexbox + Icons + Responsive Layout --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-person-gear text-warning me-2"></i>
                        Gestione Assegnazioni Prodotti
                    </h1>
                    <p class="text-muted mb-0">
                        Assegna prodotti ai membri dello staff per la gestione dei malfunzionamenti
                    </p>
                </div>
                <div>
                    {{-- BOOTSTRAP MODAL TRIGGER: data-bs-* attributes --}}
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                        <i class="bi bi-collection me-1"></i>Assegnazione Multipla
                    </button>
                </div>
            </div>
            
            {{-- BOOTSTRAP ALERT: Info component con blade variables --}}
            <div class="alert alert-info border-start border-info border-4">
                <div class="row">
                    <div class="col-md-8">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Funzionalità Opzionale:</strong> Ogni membro dello staff può gestire un sottoinsieme specifico di prodotti.
                    </div>
                    <div class="col-md-4 text-end">
                        {{-- BLADE ECHO: Sicuro output con array access --}}
                        <span class="badge bg-success">{{ $stats['prodotti_assegnati'] }}</span> Assegnati
                        <span class="badge bg-warning">{{ $stats['prodotti_non_assegnati'] }}</span> Non Assegnati
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DASHBOARD CARDS: Bootstrap Grid + Themed Cards + Statistics Display --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-box display-6"></i>
                    <h4 class="mt-2">{{ $stats['totale_prodotti'] }}</h4>
                    <small>Prodotti Totali</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_assegnati'] }}</h4>
                    <small>Assegnati</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_non_assegnati'] }}</h4>
                    <small>Non Assegnati</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h4 class="mt-2">{{ $stats['staff_attivi'] }}</h4>
                    <small>Staff Attivi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- FILTERS SIDEBAR: HTML Forms + Laravel Request Helpers + Form Persistence --}}
        <div class="col-lg-3">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                <div class="card-body">
                    {{-- LARAVEL FORM: GET method + route helper + request persistence --}}
                    <form method="GET" action="{{ route('admin.assegnazioni.index') }}" id="filterForm">
                        
                        <!-- Search Input -->
                        <div class="mb-3">
                            <label for="search" class="form-label">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome o modello prodotto">
                        </div>
                        
                        {{-- BLADE FOREACH: Loop con Laravel Collections + Selected Logic --}}
                        <!-- Staff Filter -->
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">
                                <i class="bi bi-person me-1"></i>Membro Staff
                            </label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">Tutti gli staff</option>
                                <option value="null" {{ request('staff_id') === 'null' ? 'selected' : '' }}>
                                    Non Assegnati
                                </option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">
                                <i class="bi bi-tag me-1"></i>Categoria
                            </label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Tutte le categorie</option>
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- BOOTSTRAP FORM-CHECK: Checkbox styling + Boolean filter --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="non_assegnati" 
                                       name="non_assegnati" 
                                       value="1"
                                       {{ request('non_assegnati') ? 'checked' : '' }}>
                                <label class="form-check-label" for="non_assegnati">
                                    Solo prodotti non assegnati
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Applica Filtri
                            </button>
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ELOQUENT RELATIONSHIPS: @forelse loop + relationship count() --}}
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-info me-2"></i>
                        Staff Overview
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($staffMembers as $staff)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                            <div>
                                <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                <small class="text-muted">{{ $staff->username }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">
                                    {{ $staff->prodottiAssegnati()->count() }}
                                </span>
                                <div>
                                    <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $staff->id]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Nessun membro staff disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- PRODUCTS TABLE: Laravel Pagination + Responsive Table + JavaScript Hooks --}}
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Prodotti 
                        <span class="badge bg-secondary">{{ $prodotti->total() }}</span>
                    </h5>
                    <div>
                        {{-- JAVASCRIPT INTEGRATION: IDs per DOM manipulation --}}
                        <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                            <i class="bi bi-check-all me-1"></i>Seleziona Tutti
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" id="bulkAssignBtn" disabled>
                            <i class="bi bi-person-plus me-1"></i>Assegna Selezionati
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- CONDITIONAL RENDERING: Blade @if per empty state --}}
                    @if($prodotti->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </th>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th>Staff Assegnato</th>
                                        <th>Problemi</th>
                                        <th width="200">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- LARAVEL PAGINATION: Collection iteration con Eloquent Models --}}
                                    @foreach($prodotti as $prodotto)
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       class="form-check-input product-checkbox" 
                                                       value="{{ $prodotto->id }}">
                                            </td>
                                            <td>
                                                {{-- BOOTSTRAP FLEXBOX + Image styling + Eloquent attributes --}}
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $prodotto->foto_url }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $prodotto->nome }}">
                                                    <div>
                                                        <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                                                        <small class="text-muted">{{ $prodotto->modello }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- ELOQUENT ACCESSOR: categoria_label formatting --}}
                                                <span class="badge bg-secondary">
                                                    {{ $prodotto->categoria_label }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- ELOQUENT RELATIONSHIPS: Conditional display con belongsTo --}}
                                                @if($prodotto->staffAssegnato)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-person-check text-success me-2"></i>
                                                        <div>
                                                            <strong>{{ $prodotto->staffAssegnato->nome_completo }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $prodotto->staffAssegnato->username }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Non Assegnato
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- PHP INLINE: Collection methods + statistics calculation --}}
                                                @php
                                                    $problemiCount = $prodotto->malfunzionamenti->count();
                                                    $criticiCount = $prodotto->malfunzionamenti->where('gravita', 'critica')->count();
                                                @endphp
                                                
                                                <div class="text-center">
                                                    @if($problemiCount > 0)
                                                        <span class="badge bg-info">{{ $problemiCount }}</span>
                                                        @if($criticiCount > 0)
                                                            <span class="badge bg-danger">{{ $criticiCount }} critici</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-success">0</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{-- BOOTSTRAP BTN-GROUP + Modal triggers + Laravel Forms --}}
                                                <div class="btn-group" role="group">
                                                    {{-- MODAL DATA ATTRIBUTES: JavaScript data passing --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm assign-btn"
                                                            data-product-id="{{ $prodotto->id }}"
                                                            data-product-name="{{ $prodotto->nome }}"
                                                            data-current-staff="{{ $prodotto->staff_assegnato_id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#assignModal">
                                                        <i class="bi bi-person-plus"></i>
                                                    </button>
                                                    
                                                    {{-- LARAVEL ROUTE + Model Binding --}}
                                                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    {{-- CONDITIONAL FORM: CSRF + Hidden inputs + Confirmation --}}
                                                    @if($prodotto->staffAssegnato)
                                                        <form action="{{ route('admin.assegnazioni.prodotto') }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                                                            <input type="hidden" name="staff_id" value="">
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Rimuovere l\'assegnazione?')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- LARAVEL PAGINATION: Custom pagination con query persistence --}}
                        @if($prodotti->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="text-center mb-2">
                                        <small class="text-muted">
                                            Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                                            di {{ $prodotti->total() }} prodotti
                                        </small>
                                    </div>
                                    
                                    {{-- BOOTSTRAP PAGINATION: Manual pagination controls --}}
                                    <div class="d-flex justify-content-center">
                                        <nav aria-label="Paginazione prodotti">
                                            <ul class="pagination pagination-sm mb-0">
                                                {{-- Previous/Next + Query String Persistence --}}
                                                @if ($prodotti->onFirstPage())
                                                    <li class="page-item disabled">
                                                        <span class="page-link">‹</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                                    </li>
                                                @endif

                                                {{-- Page Numbers Loop --}}
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
                        {{-- EMPTY STATE: UX pattern per no results --}}
                        <div class="text-center py-5">
                            <i class="bi bi-box display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BOOTSTRAP MODALS: Single + Bulk Assignment con Laravel Forms --}}
<!-- === MODAL ASSEGNAZIONE SINGOLA === -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.assegnazioni.prodotto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="assign-product-id" name="prodotto_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Prodotto:</label>
                        <div class="p-2 bg-light rounded">
                            <strong id="assign-product-name"></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assign-staff-id" class="form-label">
                            <i class="bi bi-person me-1"></i>Assegna a Staff:
                        </label>
                        <select class="form-select" id="assign-staff-id" name="staff_id">
                            <option value="">Nessuna assegnazione</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }} ({{ $staff->prodottiAssegnati()->count() }} prodotti)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Seleziona un membro dello staff o lascia vuoto per rimuovere l'assegnazione
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check me-1"></i>Conferma Assegnazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- === MODAL ASSEGNAZIONE MULTIPLA === -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-collection me-2"></i>Assegnazione Multipla
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.assegnazioni.multipla') }}" method="POST" id="bulkAssignForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Seleziona i prodotti dalla lista e scegli il membro dello staff per l'assegnazione.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prodotti Selezionati:</label>
                        <div id="selected-products" class="border rounded p-3 bg-light">
                            <em class="text-muted">Nessun prodotto selezionato</em>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk-staff-id" class="form-label">
                            <i class="bi bi-person me-1"></i>Assegna a Staff:
                        </label>
                        <select class="form-select" id="bulk-staff-id" name="staff_id" required>
                            <option value="">Seleziona membro staff</option>
                            <option value="">-- Rimuovi assegnazione --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }} 
                                    ({{ $staff->prodottiAssegnati()->count() }} prodotti attuali)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- STAFF OVERVIEW GRID: Real-time counters --}}
                    <div class="row">
                        @foreach($staffMembers as $staff)
                            <div class="col-md-6 mb-2">
                                <div class="card card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                            <small class="text-muted">{{ $staff->username }}</small>
                                        </div>
                                        <span class="badge bg-primary">
                                            {{ $staff->prodottiAssegnati()->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning" id="confirmBulkAssign" disabled>
                        <i class="bi bi-check me-1"></i>Conferma Assegnazioni
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- BLADE STACK SYSTEM: Component-specific CSS --}}
@push('styles')
<style>
/* CUSTOM STYLING: Card shadows + transitions + hover effects */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

/* BOOTSTRAP OVERRIDES: Checkbox + selection styling */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

tr.selected {
    background-color: #e3f2fd !important;
}

/* INTERACTIVE EFFECTS: Hover states */
.bg-light:hover {
    background-color: #e9ecef !important;
    transition: background-color 0.2s ease;
}

/* MODAL STYLING: Product items */
#selected-products .product-item {
    display: inline-block;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    margin: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endpush

{{-- PHP TO JAVASCRIPT: Data transfer pattern --}}
@push('scripts')
<script>
// GLOBAL DATA OBJECT: Laravel → JavaScript data transfer
window.PageData = window.PageData || {};

// CONDITIONAL DATA PASSING: Only if exists to avoid JSON errors
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// DATA TYPES: Models→JSON, Collections→Arrays, Arrays→Objects
// INTEGRATION READY: For filtering, modal population, AJAX, bulk operations
</script>
@endpush