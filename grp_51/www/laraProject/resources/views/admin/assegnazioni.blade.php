{{-- Vista per gestione assegnazioni prodotti a staff --}}
@extends('layouts.app')

@section('title', 'Gestione Assegnazioni Prodotti')

@section('content')
<div class="container mt-4">
    

    <!-- === HEADER === -->
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
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                        <i class="bi bi-collection me-1"></i>Assegnazione Multipla
                    </button>
                </div>
            </div>
            
            <!-- Alert Informativo -->
            <div class="alert alert-info border-start border-info border-4">
                <div class="row">
                    <div class="col-md-8">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Funzionalità Opzionale:</strong> Ogni membro dello staff può gestire un sottoinsieme specifico di prodotti.
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-success">{{ $stats['prodotti_assegnati'] }}</span> Assegnati
                        <span class="badge bg-warning">{{ $stats['prodotti_non_assegnati'] }}</span> Non Assegnati
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === STATISTICHE RAPIDE === -->
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
        <!-- === FILTRI === -->
        <div class="col-lg-3">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.assegnazioni') }}" id="filterForm">
                        
                        <!-- Ricerca -->
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
                        
                        <!-- Staff -->
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
                        
                        <!-- Categoria -->
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
                        
                        <!-- Solo non assegnati -->
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
                            <a href="{{ route('admin.assegnazioni') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- === STAFF OVERVIEW === -->
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
                                    <a href="{{ route('admin.assegnazioni', ['staff_id' => $staff->id]) }}" 
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

        <!-- === LISTA PRODOTTI === -->
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Prodotti 
                        <span class="badge bg-secondary">{{ $prodotti->total() }}</span>
                    </h5>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                            <i class="bi bi-check-all me-1"></i>Seleziona Tutti
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" id="bulkAssignBtn" disabled>
                            <i class="bi bi-person-plus me-1"></i>Assegna Selezionati
                        </button>
                    </div>
                </div>
                <div class="card-body">
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
                                    @foreach($prodotti as $prodotto)
                                        <tr>
                                            <td>
                                                <input type="checkbox" 
                                                       class="form-check-input product-checkbox" 
                                                       value="{{ $prodotto->id }}">
                                            </td>
                                            <td>
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
                                                <span class="badge bg-secondary">
                                                    {{ $prodotto->categoria_label }}
                                                </span>
                                            </td>
                                            <td>
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
                                                <div class="btn-group" role="group">
                                                    <!-- Assegna -->
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm assign-btn"
                                                            data-product-id="{{ $prodotto->id }}"
                                                            data-product-name="{{ $prodotto->nome }}"
                                                            data-current-staff="{{ $prodotto->staff_assegnato_id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#assignModal">
                                                        <i class="bi bi-person-plus"></i>
                                                    </button>
                                                    
                                                    <!-- Visualizza -->
                                                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Rimuovi assegnazione se assegnato -->
                                                    @if($prodotto->staffAssegnato)
                                                        <form action="{{ route('admin.assegna.prodotto') }}" 
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

                        <!-- Paginazione -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Mostrando {{ $prodotti->firstItem() ?? 0 }} - {{ $prodotti->lastItem() ?? 0 }} 
                                    di {{ $prodotti->total() }} prodotti
                                </small>
                            </div>
                            <div>
                                {{ $prodotti->links() }}
                            </div>
                        </div>
                    @else
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
            <form action="{{ route('admin.assegna.prodotto') }}" method="POST">
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
            <form action="{{ route('admin.assegnazione.multipla') }}" method="POST" id="bulkAssignForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Seleziona i prodotti dalla lista e scegli il membro dello staff per l'assegnazione.
                    </div>
                    
                    <!-- Lista prodotti selezionati -->
                    <div class="mb-3">
                        <label class="form-label">Prodotti Selezionati:</label>
                        <div id="selected-products" class="border rounded p-3 bg-light">
                            <em class="text-muted">Nessun prodotto selezionato</em>
                        </div>
                    </div>
                    
                    <!-- Staff selection -->
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
                    
                    <!-- Riepilogo staff -->
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

@push('styles')
<style>
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

.badge {
    font-size: 0.75rem;
}

/* Checkbox styling */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Product selection highlighting */
tr.selected {
    background-color: #e3f2fd !important;
}

/* Staff overview cards */
.bg-light:hover {
    background-color: #e9ecef !important;
    transition: background-color 0.2s ease;
}

/* Selected products styling */
#selected-products .product-item {
    display: inline-block;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    margin: 0.25rem;
    font-size: 0.875rem;
}

#selected-products .product-item .remove-btn {
    background: none;
    border: none;
    color: #dc3545;
    padding: 0;
    margin-left: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedProducts = [];
    
    // === GESTIONE SELEZIONE PRODOTTI ===
    
    // Select All checkbox
    $('#checkAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateSelectedProducts();
        updateBulkActions();
    });
    
    // Singola checkbox prodotto
    $('.product-checkbox').on('change', function() {
        updateSelectedProducts();
        updateBulkActions();
        updateSelectAllState();
    });
    
    // Aggiorna stato "Select All"
    function updateSelectAllState() {
        const total = $('.product-checkbox').length;
        const checked = $('.product-checkbox:checked').length;
        
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        $('#checkAll').prop('checked', checked === total && total > 0);
    }
    
    // Aggiorna lista prodotti selezionati
    function updateSelectedProducts() {
        selectedProducts = [];
        $('.product-checkbox:checked').each(function() {
            const productId = $(this).val();
            const productName = $(this).closest('tr').find('h6').text().trim();
            selectedProducts.push({
                id: productId,
                name: productName
            });
            
            // Evidenzia riga selezionata
            $(this).closest('tr').addClass('selected');
        });
        
        // Rimuovi evidenziazione da righe non selezionate
        $('.product-checkbox:not(:checked)').closest('tr').removeClass('selected');
        
        updateSelectedProductsDisplay();
    }
    
    // Aggiorna display prodotti selezionati nel modal
    function updateSelectedProductsDisplay() {
        const container = $('#selected-products');
        
        if (selectedProducts.length === 0) {
            container.html('<em class="text-muted">Nessun prodotto selezionato</em>');
        } else {
            let html = '';
            selectedProducts.forEach(function(product) {
                html += `
                    <span class="product-item">
                        ${product.name}
                        <input type="hidden" name="prodotti[]" value="${product.id}">
                        <button type="button" class="remove-btn" data-product-id="${product.id}">
                            <i class="bi bi-x"></i>
                        </button>
                    </span>
                `;
            });
            container.html(html);
        }
    }
    
    // Rimuovi prodotto dalla selezione nel modal
    $(document).on('click', '.remove-btn', function() {
        const productId = $(this).data('product-id');
        $(`.product-checkbox[value="${productId}"]`).prop('checked', false);
        updateSelectedProducts();
        updateBulkActions();
        updateSelectAllState();
    });
    
    // Aggiorna stato pulsanti azioni multiple
    function updateBulkActions() {
        const hasSelection = selectedProducts.length > 0;
        $('#bulkAssignBtn').prop('disabled', !hasSelection);
        $('#confirmBulkAssign').prop('disabled', !hasSelection);
        
        // Aggiorna contatore nel pulsante
        if (hasSelection) {
            $('#bulkAssignBtn').html(`<i class="bi bi-person-plus me-1"></i>Assegna Selezionati (${selectedProducts.length})`);
        } else {
            $('#bulkAssignBtn').html('<i class="bi bi-person-plus me-1"></i>Assegna Selezionati');
        }
    }
    
    // === GESTIONE MODAL ASSEGNAZIONE SINGOLA ===
    
    $('.assign-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const currentStaff = $(this).data('current-staff');
        
        $('#assign-product-id').val(productId);
        $('#assign-product-name').text(productName);
        $('#assign-staff-id').val(currentStaff || '');
    });
    
    // === GESTIONE MODAL ASSEGNAZIONE MULTIPLA ===
    
    $('#bulkAssignBtn').on('click', function() {
        if (selectedProducts.length === 0) {
            showAlert('warning', 'Seleziona almeno un prodotto per l\'assegnazione multipla');
            return;
        }
        $('#bulkAssignModal').modal('show');
    });
    
    // Abilita pulsante conferma quando staff selezionato
    $('#bulk-staff-id').on('change', function() {
        const hasStaff = $(this).val() !== '';
        const hasProducts = selectedProducts.length > 0;
        $('#confirmBulkAssign').prop('disabled', !(hasStaff && hasProducts));
    });
    
    // === SELECT ALL BUTTON ===
    
    $('#selectAllBtn').on('click', function() {
        const allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length;
        
        if (allChecked) {
            // Deseleziona tutti
            $('.product-checkbox').prop('checked', false);
            $('#checkAll').prop('checked', false);
            $(this).html('<i class="bi bi-check-all me-1"></i>Seleziona Tutti');
        } else {
            // Seleziona tutti
            $('.product-checkbox').prop('checked', true);
            $('#checkAll').prop('checked', true);
            $(this).html('<i class="bi bi-square me-1"></i>Deseleziona Tutti');
        }
        
        updateSelectedProducts();
        updateBulkActions();
    });
    
    // === FILTRI DINAMICI ===
    
    // Auto-submit filtri quando cambiano
    $('#staff_id, #categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Checkbox non_assegnati
    $('#non_assegnati').on('change', function() {
        if ($(this).is(':checked')) {
            $('#staff_id').val('null');
        } else {
            $('#staff_id').val('');
        }
        $(this).closest('form').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    $('#bulkAssignForm').on('submit', function(e) {
        if (selectedProducts.length === 0) {
            e.preventDefault();
            showAlert('error', 'Seleziona almeno un prodotto per l\'assegnazione');
            return false;
        }
        
        // Conferma azione
        const staffName = $('#bulk-staff-id option:selected').text();
        const message = `Confermi l'assegnazione di ${selectedProducts.length} prodotti a ${staffName}?`;
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
    
    // === FUNZIONI HELPER ===
    
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const icon = type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        // Ctrl+A per selezionare tutti
        if (e.ctrlKey && e.key === 'a' && !$(e.target).is('input, textarea')) {
            e.preventDefault();
            $('#selectAllBtn').click();
        }
        
        // Escape per chiudere modal
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
    });
    
    // === INIZIALIZZAZIONE ===
    updateBulkActions();
    updateSelectAllState();
    
    console.log('Gestione assegnazioni inizializzata');
    console.log(`Staff disponibili: ${$('#staff_id option').length - 1}`);
    console.log(`Prodotti totali: {{ $prodotti->total() }}`);
});
</script>
@endpush