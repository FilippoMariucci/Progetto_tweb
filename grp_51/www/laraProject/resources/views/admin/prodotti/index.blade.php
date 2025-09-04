{{-- 
    VISTA GESTIONE PRODOTTI ADMIN CON STILE CATALOGO TECNICO
    Mantiene tutte le funzionalità admin originali ma con il design del catalogo completo
--}}

@extends('layouts.app')

@section('title', 'Gestione Prodotti')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    
    {{-- Header principale con stile identico al catalogo --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        {{-- Titolo e descrizione --}}
                        <div class="col-lg-8 col-md-7">
                            <h2 class="mb-1 fw-bold">
                                <i class="bi bi-gear-fill me-2"></i>
                                Gestione Prodotti Admin
                            </h2>
                            <p class="mb-0 opacity-90">
                                <span class="badge bg-danger text-white me-2">Amministrazione Completa</span>
                                Controllo totale del catalogo prodotti
                            </p>
                        </div>
                        
                        {{-- Statistiche amministrative nella header --}}
                        <div class="col-lg-4 col-md-5 mt-2 mt-md-0">
                            @if(isset($stats))
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            <div class="h5 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</div>
                                            <small class="opacity-90">Totali</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            <div class="h5 fw-bold mb-0">{{ $stats['con_malfunzionamenti'] ?? 0 }}</div>
                                            <small class="opacity-90">Problemi</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pulsanti azione flottanti --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div class="d-flex flex-column gap-2">
            {{-- Nuovo prodotto --}}
            <a href="{{ route('admin.prodotti.create') }}" 
               class="btn btn-success rounded-circle shadow" 
               style="width: 50px; height: 50px;"
               data-bs-toggle="tooltip" 
               title="Aggiungi Nuovo Prodotto">
                <i class="bi bi-plus" style="font-size: 1.25rem;"></i>
            </a>
            
            {{-- Azioni bulk se ci sono selezioni --}}
            <button class="btn btn-warning rounded-circle shadow d-none" 
                    id="bulkActionsBtn"
                    style="width: 50px; height: 50px;"
                    data-bs-toggle="dropdown"
                    title="Azioni Multiple">
                <i class="bi bi-gear" style="font-size: 1.25rem;"></i>
            </button>
            {{-- Menu dropdown per azioni bulk --}}
<ul class="dropdown-menu" aria-labelledby="bulkActionsBtn">
    <li>
        <button class="dropdown-item" type="button" onclick="selectAllProducts()">
            <i class="bi bi-check-all me-2"></i>Seleziona Tutti
        </button>
    </li>
    <li>
        <button class="dropdown-item" type="button" onclick="deselectAllProducts()">
            <i class="bi bi-x-square me-2"></i>Deseleziona Tutti
        </button>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
        <button class="dropdown-item text-success" type="button" onclick="bulkActivateProducts()">
            <i class="bi bi-check-circle me-2"></i>Attiva Selezionati
        </button>
    </li>
    <li>
        <button class="dropdown-item text-warning" type="button" onclick="bulkDeactivateProducts()">
            <i class="bi bi-x-circle me-2"></i>Disattiva Selezionati
        </button>
    </li>
    <li>
        <button class="dropdown-item text-danger" type="button" onclick="bulkDeleteProducts()">
            <i class="bi bi-trash me-2"></i>Elimina Selezionati
        </button>
    </li>
</ul>
        </div>
    </div>

    {{-- Form di ricerca e filtri con design catalogo --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('admin.prodotti.index') }}" id="filterForm" class="row g-3">

                        {{-- Campo di ricerca avanzata identico al catalogo --}}
                        <div class="col-lg-4 col-md-6">
                            <label for="search" class="form-label fw-semibold text-primary">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Nome, modello, descrizione..."
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Pulisci ricerca">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Supporta ricerche parziali con <code>*</code>
                            </div>
                        </div>

                        {{-- Filtro stato --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="status" class="form-label fw-semibold text-primary">
                                <i class="bi bi-funnel me-1"></i>Stato
                            </label>
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

                        {{-- Staff assegnato --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="staff_id" class="form-label fw-semibold text-primary">
                                <i class="bi bi-person-gear me-1"></i>Staff Assegnato
                            </label>
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

                        {{-- Pulsanti azione --}}
                        <div class="col-lg-3 col-md-12">
                            <label class="form-label d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('admin.prodotti.index') }}" 
                                   class="btn btn-outline-secondary" 
                                   title="Reset filtri">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtri rapidi admin --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge bg-secondary py-2 px-3">
                    <i class="bi bi-funnel me-1"></i>Filtri Admin:
                </span>
                
                <a href="{{ route('admin.prodotti.index') }}" 
                   class="badge {{ !request()->hasAny(['search', 'status', 'staff_id']) ? 'bg-primary' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Tutti i Prodotti
                </a>
                
                <a href="{{ route('admin.prodotti.index') }}?status=attivi" 
                   class="badge {{ request('status') === 'attivi' ? 'bg-success' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Solo Attivi
                </a>
                
                <a href="{{ route('admin.prodotti.index') }}?staff_id=0" 
                   class="badge {{ request('staff_id') === '0' ? 'bg-warning' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Non Assegnati
                </a>
                
                <a href="{{ route('admin.prodotti.index') }}?status=inattivi" 
                   class="badge {{ request('status') === 'inattivi' ? 'bg-danger' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Disattivati
                </a>
            </div>
        </div>
    </div>

    {{-- Statistiche dettagliate --}}
    @if(isset($stats))
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-primary">{{ $stats['total_prodotti'] }} prodotti totali</span>
                    <span class="badge bg-success">{{ $stats['attivi'] ?? 0 }} attivi</span>
                    <span class="badge bg-warning">{{ $stats['inattivi'] ?? 0 }} disattivati</span>
                    <span class="badge bg-danger">{{ $stats['con_malfunzionamenti'] ?? 0 }} con problemi</span>
                    
                    {{-- Filtri applicati --}}
                    @if(request('search'))
                        <span class="badge bg-info">Ricerca: "{{ request('search') }}"</span>
                    @endif
                    @if(request('status'))
                        <span class="badge bg-secondary">Stato: {{ ucfirst(request('status')) }}</span>
                    @endif
                    @if(request('staff_id') === '0')
                        <span class="badge bg-warning">Solo Non Assegnati</span>
                    @elseif(request('staff_id'))
                        <span class="badge bg-info">Staff: {{ $staffMembers->find(request('staff_id'))->nome_completo ?? 'N/A' }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Messaggio risultati ricerca identico al catalogo --}}
    @if(request()->hasAny(['search', 'status', 'staff_id']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm py-2">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Risultati filtrati:</strong>
                                    Trovati <span class="badge bg-primary">{{ $prodotti->total() }}</span> prodotti
                                    @if(request('search'))
                                        per "<em class="text-primary">{{ request('search') }}</em>"
                                    @endif
                                    @if(request('status'))
                                        con stato "<em class="text-primary">{{ request('status') }}</em>"
                                    @endif
                                    @if(request('staff_id'))
                                        {{ request('staff_id') === '0' ? 'non assegnati' : 'per staff selezionato' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end mt-2 mt-lg-0">
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Rimuovi filtri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Griglia prodotti con stile catalogo ma funzionalità admin --}}
    <div class="row g-3 mb-4" id="prodotti-admin-grid">
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                {{-- Card prodotto con design identico al catalogo ma con controlli admin --}}
                <div class="card h-100 shadow-sm border-0 product-card admin-card
                    {{-- Stessi bordi colorati del catalogo --}}
                    @if($prodotto->hasMalfunzionamentiCritici())
                        border-danger-subtle
                    @elseif($prodotto->malfunzionamenti_count > 0)
                        border-warning-subtle
                    @elseif($prodotto->attivo)
                        border-success-subtle
                    @else
                        border-secondary-subtle
                    @endif
                ">
                    
                    {{-- Checkbox di selezione per azioni bulk (admin only) --}}
                    <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                        <input type="checkbox" 
                               class="form-check-input product-checkbox shadow" 
                               value="{{ $prodotto->id }}"
                               style="transform: scale(1.2);">
                    </div>
                    
                    <div class="position-relative overflow-hidden">
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 160px;
    object-fit: contain !important; /* ← Mostra immagine completa */
    object-position: center center;
    background-color: #f8f9fa;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 160px;">
                                <i class="bi bi-box text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-secondary bg-opacity-90 px-2 py-1 mb-1 d-block">
                                <i class="bi bi-tag me-1"></i>{{ $prodotto->categoria_label ?? ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- Indicatori stato admin --}}
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75">
                            <div class="d-flex justify-content-between align-items-center p-2">
                                {{-- Stato attivo/inattivo --}}
                                <div>
                                    @if($prodotto->attivo)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Attivo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Inattivo
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Contatori problemi --}}
                                <div class="d-flex gap-1">
                                    @if($prodotto->malfunzionamenti_count > 0)
                                        <span class="badge bg-warning" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $prodotto->malfunzionamenti_count }} problemi totali">
                                            {{ $prodotto->malfunzionamenti_count }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                        <span class="badge bg-danger" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $prodotto->critici_count }} critici">
                                            {{ $prodotto->critici_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Corpo della card --}}
                    <div class="card-body d-flex flex-column p-3">
                        {{-- Titolo con stato --}}
                        <h6 class="card-title mb-2 fw-bold
                            @if(!$prodotto->attivo)
                                text-muted
                            @elseif($prodotto->hasMalfunzionamentiCritici())
                                text-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                text-warning
                            @else
                                text-primary
                            @endif
                        ">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- Modello e prezzo --}}
                        <div class="row g-1 mb-2 small">
                            @if($prodotto->modello)
                                <div class="col-12">
                                    <span class="text-muted">
                                        <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                                    </span>
                                </div>
                            @endif
                            @if($prodotto->prezzo)
                                <div class="col-12">
                                    <span class="text-success fw-bold">
                                        <i class="bi bi-tag me-1"></i>€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <p class="card-text flex-grow-1 text-muted small">
                            {{ Str::limit($prodotto->descrizione, 80, '...') }}
                        </p>

                        {{-- Informazioni amministrative --}}
                        <div class="row g-1 mb-2 small">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong class="text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                        {{ $prodotto->malfunzionamenti_count ?? 0 }}
                                    </strong>
                                    <br><small class="text-muted">Problemi</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong class="text-muted">
                                        {{ $prodotto->created_at->format('d/m/Y') }}
                                    </strong>
                                    <br><small class="text-muted">Creato</small>
                                </div>
                            </div>
                        </div>

                        {{-- Staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </p>
                        @else
                            <p class="text-warning small mb-2">
                                <i class="bi bi-person-x me-1"></i>
                                Nessun staff assegnato
                            </p>
                        @endif

                        {{-- Pulsanti azione admin --}}
                        <div class="d-grid gap-1">
                            {{-- Visualizza dettagli --}}
                            <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Visualizza
                            </a>
                            
                            {{-- Azioni rapide in dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-1"></i>Azioni
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.prodotti.edit', $prodotto) }}">
                                            <i class="bi bi-pencil me-2"></i>Modifica
                                        </a>
                                    </li>
                                    
                                    @if($prodotto->malfunzionamenti_count > 0)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('malfunzionamenti.index', $prodotto) }}">
                                                <i class="bi bi-tools me-2"></i>Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                                            </a>
                                        </li>
                                    @endif
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    {{-- Toggle stato --}}
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
                                    
                                    {{-- Eliminazione --}}
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
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Stato vuoto --}}
            <div class="col-12">
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
            </div>
        @endforelse
    </div>

    {{-- Paginazione identica al catalogo --}}
    @if($prodotti->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center mb-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
                
                <div class="d-flex justify-content-center">
                    <nav aria-label="Paginazione prodotti">
                        <ul class="pagination pagination-sm mb-0">
                            @if ($prodotti->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                </li>
                            @endif

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

    {{-- Sezione informazioni admin --}}
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h5 class="text-primary mb-2">
                                <i class="bi bi-shield-check me-2"></i>
                                Pannello Amministrazione Prodotti
                            </h5>
                            <p class="mb-3 text-muted">
                                Controllo completo sui prodotti: creazione, modifica, assegnazione staff e gestione malfunzionamenti.
                            </p>
                            
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Dashboard Admin
                                </a>
                                
                                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Nuovo Prodotto
                                </a>
                                
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-people me-1"></i>
                                    Gestione Utenti
                                </a>
                                
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i>
                                    Vista Tecnica
                                </a>
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

{{-- CSS identico al catalogo con aggiunte per funzionalità admin --}}
@push('styles')
<style>
/* === STILI CARD PRODOTTO IDENTICI AL CATALOGO === */

/* Card prodotto base con bordo elegante */
.product-card {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    overflow: hidden;
    /* Bordo sottile per tutte le card */
    border: 1px solid #e9ecef !important;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 2rem rgba(0,0,0,0.15) !important;
    /* Bordo blu al hover */
    border-color: #007bff !important;
}

/* Card con problemi critici - bordo rosso */
.product-card.border-danger-subtle {
    border-left: 4px solid #dc3545 !important;
    border-top: 1px solid #fecaca !important;
    border-right: 1px solid #fecaca !important;
    border-bottom: 1px solid #fecaca !important;
    background-color: #fef7f7;
}

.product-card.border-danger-subtle:hover {
    border-color: #dc3545 !important;
    box-shadow: 0 0.75rem 2rem rgba(220, 53, 69, 0.2) !important;
}

/* Card con problemi non critici - bordo arancione */
.product-card.border-warning-subtle {
    border-left: 4px solid #ffc107 !important;
    border-top: 1px solid #fff3cd !important;
    border-right: 1px solid #fff3cd !important;
    border-bottom: 1px solid #fff3cd !important;
    background-color: #fffbf0;
}

.product-card.border-warning-subtle:hover {
    border-color: #ffc107 !important;
    box-shadow: 0 0.75rem 2rem rgba(255, 193, 7, 0.2) !important;
}

/* Card senza problemi attive - bordo verde */
.product-card.border-success-subtle {
    border-left: 3px solid #28a745 !important;
    border-top: 1px solid #d4edda !important;
    border-right: 1px solid #d4edda !important;
    border-bottom: 1px solid #d4edda !important;
}

.product-card.border-success-subtle:hover {
    border-color: #28a745 !important;
    box-shadow: 0 0.75rem 2rem rgba(40, 167, 69, 0.15) !important;
}

/* Card inattive - bordo grigio */
.product-card.border-secondary-subtle {
    border-left: 3px solid #6c757d !important;
    border-top: 1px solid #e9ecef !important;
    border-right: 1px solid #e9ecef !important;
    border-bottom: 1px solid #e9ecef !important;
    background-color: #f8f9fa;
}

.product-card.border-secondary-subtle:hover {
    border-color: #6c757d !important;
    box-shadow: 0 0.75rem 2rem rgba(108, 117, 125, 0.15) !important;
}

/* === AGGIUNTE SPECIFICHE PER ADMIN === */

/* Stile per le card admin con checkbox */
.admin-card {
    position: relative;
}

/* Checkbox di selezione prodotto */
.product-checkbox {
    background-color: rgba(255, 255, 255, 0.9);
    border: 2px solid #007bff;
    border-radius: 0.25rem;
    backdrop-filter: blur(2px);
}

.product-checkbox:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.product-checkbox:hover {
    border-color: #0056b3;
    background-color: rgba(255, 255, 255, 1);
    transform: scale(1.05);
}

/* Card selezionate per azioni bulk */
.product-card.selected {
    border: 2px solid #007bff !important;
    background-color: #f8f9ff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
    transform: translateY(-2px);
}

/* Pulsanti azione flottanti */
.btn.rounded-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn.rounded-circle:hover {
    transform: scale(1.1) translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.btn.rounded-circle:active {
    transform: scale(0.95) translateY(0px);
}

/* Animazione per pulsanti flottanti */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-3px); }
}

.btn.rounded-circle:not(:hover) {
    animation: float 3s ease-in-out infinite;
}

/* Responsive - checkbox più grandi su mobile */
@media (max-width: 768px) {
    .product-checkbox {
        transform: scale(1.4);
    }
    
    .product-card {
        margin-bottom: 1rem;
    }
    
    .btn.rounded-circle {
        width: 45px !important;
        height: 45px !important;
    }
    
    .btn.rounded-circle i {
        font-size: 1.1rem !important;
    }
}

/* Hover effects per dropdown azioni */
.dropdown-item {
    transition: all 0.15s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 1.25rem;
    transform: translateX(2px);
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da;
    color: #721c24 !important;
}

.dropdown-item.text-success:hover {
    background-color: #d4edda;
    color: #155724 !important;
}

/* Stili per form eliminazione inline */
.dropdown-item form {
    margin: 0;
}

.dropdown-item form button {
    border: none;
    background: none;
    padding: 0.25rem 1rem;
    text-align: left;
    width: 100%;
    color: inherit;
    transition: all 0.15s ease;
}

.dropdown-item form button:hover {
    background: none;
    color: inherit;
    padding-left: 1.25rem;
}

/* Badge stato prodotto */
.badge {
    font-size: 0.7rem;
    font-weight: 500;
    border-radius: 0.35rem;
}

.badge.bg-success {
    background-color: #28a745 !important;
    border: 1px solid #1e7e34;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    border: 1px solid #bd2130;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    border: 1px solid #e0a800;
    color: #212529 !important;
}

/* === PAGINAZIONE IDENTICA AL CATALOGO === */

.pagination {
    margin-bottom: 0 !important;
    justify-content: center !important;
    display: flex !important;
    gap: 4px !important;
}

.pagination .page-item {
    margin: 0 !important;
}

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
    transition: all 0.15s ease;
}

.pagination .page-link:hover {
    color: #495057 !important;
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    text-decoration: none !important;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    color: #fff !important;
    background-color: #007bff !important;
    border-color: #007bff !important;
    font-weight: 500 !important;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    opacity: 0.65 !important;
    cursor: not-allowed !important;
}

/* Frecce di navigazione */
.pagination .page-link:contains('‹'),
.pagination .page-link:contains('›') {
    font-weight: bold;
    font-size: 16px;
}

/* === GRADIENTI E SFONDI === */

/* Card gradient header identico al catalogo */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white;
    border: none;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 1px solid #dee2e6;
}

/* Sfondo per le statistiche nell'header */
.bg-white.bg-opacity-10 {
    background-color: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* === FORM ELEMENTI === */

/* Stili per etichette form */
.form-label.fw-semibold {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Stili per campi input e select */
.form-control,
.form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: all 0.15s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    outline: none;
}

/* Input group con pulsante */
.input-group .btn {
    border-left: none;
}

.input-group .form-control:focus + .btn {
    border-color: #86b7fe;
}

/* === BADGE FILTRI === */

/* Badge per filtri rapidi */
.badge.py-2.px-3 {
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 1rem;
    transition: all 0.15s ease;
}

.badge.py-2.px-3:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Badge con bordo per filtri inattivi */
.badge.bg-light.text-dark.border {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    color: #495057 !important;
}

.badge.bg-light.text-dark.border:hover {
    background-color: #e9ecef !important;
    border-color: #adb5bd !important;
}

/* === ALERT E MESSAGGI === */

/* Alert risultati ricerca */
.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #b6d4dd;
    border-left: 4px solid #0dcaf0;
    color: #0c5460;
    border-radius: 0.375rem;
}

.alert .badge {
    font-size: 0.75rem;
}

/* === TOOLTIP E ACCESSIBILITÀ === */

/* Cursore per elementi con tooltip */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Focus visibile per accessibilità */
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* === ANIMAZIONI === */

/* Animazione caricamento */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

/* Animazione per evidenziazione ricerca */
@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

mark.bg-warning {
    animation: highlight 2s ease-out;
    padding: 0 0.2em;
    border-radius: 0.25rem;
}

/* Animazione per toast notifications */
.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* === STATI DI CARICAMENTO === */

/* Overlay di caricamento */
#loadingOverlay {
    backdrop-filter: blur(2px);
}

#loadingOverlay .card {
    border: none;
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    border-radius: 1rem;
}

/* Stato loading per pulsanti */
.btn.loading {
    pointer-events: none;
    opacity: 0.6;
    position: relative;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* === RESPONSIVE DESIGN === */

/* Tablet */
@media (max-width: 992px) {
    .form-label.d-none.d-lg-block {
        display: block !important;
        margin-top: 1rem;
    }
    
    .col-lg-4.col-md-6 .input-group {
        margin-bottom: 1rem;
    }
}

/* Mobile */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .card-body.py-3 {
        padding: 1rem !important;
    }
    
    .d-flex.gap-2.justify-content-center.flex-wrap {
        flex-direction: column;
        align-items: stretch;
    }
    
    .d-flex.gap-2.justify-content-center.flex-wrap .btn {
        margin-bottom: 0.5rem;
    }
    
    .badge.py-2.px-3 {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem !important;
    }
}

/* === MIGLIORAMENTI SPECIFICI === */

/* Separatori dropdown */
.dropdown-divider {
    margin: 0.5rem 0;
    opacity: 0.3;
}

/* Icone nei dropdown */
.dropdown-item i {
    width: 16px;
    text-align: center;
}

/* Stato vuoto */
.text-center.py-5 i {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.text-center.py-5:hover i {
    opacity: 0.5;
}

/* Miglioramenti tipografici */
.fw-bold {
    font-weight: 600 !important;
}

.text-muted {
    color: #6c757d !important;
}

/* Ombre personalizzate */
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* === PERSONALIZZAZIONI FINALI === */

/* Scrollbar personalizzata per webkit browsers */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Transizioni globali */
* {
    transition: none;
}

.product-card,
.btn,
.form-control,
.form-select,
.badge,
.dropdown-item {
    transition: all 0.15s ease;
}

/* Anti-aliasing per font più nitidi */
body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Focus ring personalizzato */
:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    border-radius: 0.25rem;
}
</style>
@endpush