{{-- Vista per gestione assegnazioni prodotti a staff senza selezione multipla --}}
@extends('layouts.app')

@section('title', 'Gestione Assegnazioni Prodotti')

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item active">Assegnazioni Prodotti</li>
        </ol>
    </nav>

    <!-- === HEADER === -->
    <div class="row mb-4">
        <link rel="icon" type="image/png" href="favicon.png">
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
                    <!-- Form per applicare i filtri di ricerca e selezione -->
                    <form method="GET" action="{{ route('admin.assegnazioni.index') }}" id="filterForm">
                        
                        <!-- Campo ricerca per nome/modello del prodotto -->
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
                        
                        <!-- Selezione del membro staff per filtro -->
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">
                                <i class="bi bi-person me-1"></i>Membro Staff
                            </label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">Tutti gli staff</option>
                                <option value="null" {{ request('staff_id') === 'null' ? 'selected' : '' }}>
                                    Non Assegnati
                                </option>
                                <!-- Ciclo foreach per popolare la lista dei membri staff -->
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filtro per categoria prodotto -->
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
                        
                        <!-- Checkbox per mostrare solo prodotti non assegnati -->
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
                        
                        <!-- Pulsanti per applicare/reset filtri -->
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

            <!-- === STAFF OVERVIEW === -->
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-info me-2"></i>
                        Staff Overview
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Lista membri staff con conteggio prodotti assegnati -->
                    @forelse($staffMembers as $staff)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                            <div>
                                <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                <small class="text-muted">{{ $staff->username }}</small>
                            </div>
                            <div class="text-end">
                                <!-- Badge con numero di prodotti assegnati -->
                                <span class="badge bg-primary">
                                    {{ $staff->prodottiAssegnati()->count() }}
                                </span>
                                <div>
                                    <!-- Link per filtrare prodotti di questo staff -->
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

        <!-- === LISTA PRODOTTI === -->
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Prodotti 
                        <span class="badge bg-secondary">{{ $prodotti->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($prodotti->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th>Staff Assegnato</th>
                                        <th>Problemi</th>
                                        <th width="200">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Ciclo per ogni prodotto della pagina -->
                                    @foreach($prodotti as $prodotto)
                                        <tr>
                                            <td>
                                                <!-- Informazioni prodotto con immagine -->
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
                                                <!-- Badge categoria prodotto -->
                                                <span class="badge bg-secondary">
                                                    {{ $prodotto->categoria_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Informazioni staff assegnato o stato non assegnato -->
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
                                                <!-- Conteggio malfunzionamenti con criticità -->
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
                                                <!-- Azioni per il prodotto -->
                                                <div class="btn-group" role="group">
                                                    <!-- Pulsante assegna/modifica assegnazione -->
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm assign-btn"
                                                            data-product-id="{{ $prodotto->id }}"
                                                            data-product-name="{{ $prodotto->nome }}"
                                                            data-current-staff="{{ $prodotto->staff_assegnato_id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#assignModal"
                                                            title="Assegna/Modifica Staff">
                                                        <i class="bi bi-person-plus"></i>
                                                    </button>
                                                    
                                                    <!-- Pulsante visualizza dettagli prodotto -->
                                                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Visualizza Prodotto">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Pulsante rimuovi assegnazione (solo se assegnato) -->
                                                    @if($prodotto->staffAssegnato)
                                                        <form action="{{ route('admin.assegnazioni.prodotto') }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                                                            <input type="hidden" name="staff_id" value="">
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Rimuovere l\'assegnazione?')"
                                                                    title="Rimuovi Assegnazione">
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
                        <!-- Messaggio quando non ci sono prodotti -->
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
            <!-- Form per assegnare un prodotto a un membro dello staff -->
            <form action="{{ route('admin.assegnazioni.prodotto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Campo nascosto con ID del prodotto -->
                    <input type="hidden" id="assign-product-id" name="prodotto_id">
                    
                    <!-- Mostra nome del prodotto selezionato -->
                    <div class="mb-3">
                        <label class="form-label">Prodotto:</label>
                        <div class="p-2 bg-light rounded">
                            <strong id="assign-product-name"></strong>
                        </div>
                    </div>
                    
                    <!-- Selezione del membro staff per l'assegnazione -->
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

@endsection

@push('styles')
<style>
/* Stili personalizzati per le card */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Stili per l'intestazione della tabella */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

/* Dimensioni badge */
.badge {
    font-size: 0.75rem;
}

/* Effetto hover per le card dello staff overview */
.bg-light:hover {
    background-color: #e9ecef !important;
    transition: background-color 0.2s ease;
}

/* Stili responsive per dispositivi mobili */
@media (max-width: 768px) {
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};


</script>
@endpush