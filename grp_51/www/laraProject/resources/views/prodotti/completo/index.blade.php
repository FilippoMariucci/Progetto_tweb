{{-- 
    VISTA CATALOGO PRODOTTI TECNICO COMPLETO - LAYOUT COMPATTO
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista ottimizzata con stile compatto, header leggibile e immagini corrette
--}}

@extends('layouts.app')

@section('title', 'Catalogo Completo - Tecnici')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER COMPATTO E LEGGIBILE === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-tools text-warning me-2"></i>
                Catalogo Tecnico Completo
            </h2>
            <p class="text-muted small mb-0">
                <span class="badge bg-warning text-dark me-2">Con Malfunzionamenti</span>
                Accesso completo per tecnici e staff
            </p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- Pulsanti azione compatti --}}
            @if(auth()->user()->isStaff())
                <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuova Soluzione
                </a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Nuovo Prodotto
                </a>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>


    {{-- === STATISTICHE COMPATTE E LEGGIBILI === --}}
    @if(isset($stats))
        <div class="row g-2 mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-box text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['total_prodotti'] ?? 0 }}</h6>
                        <small class="text-muted">Prodotti Totali</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['con_malfunzionamenti'] ?? 0 }}</h6>
                        <small class="text-muted">Con Problemi</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-circle text-danger fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti_critici'] ?? 0 }}</h6>
                        <small class="text-muted">Critici</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-person-check text-success fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">
                            @if(auth()->user()->isStaff() && isset($stats['miei_prodotti']))
                                {{ $stats['miei_prodotti'] }}
                            @else
                                0
                            @endif
                        </h6>
                        <small class="text-muted">Miei Prodotti</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === FORM RICERCA COMPATTO === --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('prodotti.completo.index') }}" class="row g-3">
                        {{-- Campo ricerca --}}
                        <div class="col-lg-4 col-md-6">
                            <label for="search" class="form-label small fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Nome, modello, descrizione..."
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Categoria --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="categoria" class="form-label small fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Categoria
                            </label>
                            <select name="categoria" id="categoria" class="form-select form-select-sm">
                                <option value="">Tutte</option>
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="filter" class="form-label small fw-semibold">
                                <i class="bi bi-filter me-1"></i>Filtro
                            </label>
                            <select name="filter" id="filter" class="form-select form-select-sm">
                                <option value="">Tutti</option>
                                <option value="critici" {{ request('filter') === 'critici' ? 'selected' : '' }}>
                                    Critici
                                </option>
                                <option value="problematici" {{ request('filter') === 'problematici' ? 'selected' : '' }}>
                                    Con Problemi
                                </option>
                                <option value="senza_problemi" {{ request('filter') === 'senza_problemi' ? 'selected' : '' }}>
                                    Senza Problemi
                                </option>
                            </select>
                        </div>

                        {{-- Pulsanti --}}
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('prodotti.completo.index') }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                        
                        {{-- Hidden filters --}}
                        @if(request('staff_filter'))
                            <input type="hidden" name="staff_filter" value="{{ request('staff_filter') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === FILTRI STAFF COMPATTI === --}}
    @if(auth()->user()->isStaff())
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">Filtri Staff:</small>
                    
                    <a href="{{ route('prodotti.completo.index') }}" 
                       class="badge {{ !request('staff_filter') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                        Tutti i Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                       class="badge {{ request('staff_filter') === 'my_products' ? 'bg-success' : 'bg-light text-dark' }} text-decoration-none">
                        I Miei Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?filter=critici" 
                       class="badge {{ request('filter') === 'critici' ? 'bg-danger' : 'bg-light text-dark' }} text-decoration-none">
                        Solo Critici
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI RICERCA === --}}
    @if(request('search') || request('categoria') || request('filter'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Risultati:</strong> {{ $prodotti->total() }} prodotti trovati
                            @if(request('search'))
                                per "<em>{{ request('search') }}</em>"
                            @endif
                        </div>
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI COMPATTA === --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 product-card
                    {{-- Bordi colorati per stato --}}
                    @if($prodotto->hasMalfunzionamentiCritici())
                        border-start border-danger border-3
                    @elseif($prodotto->malfunzionamenti_count > 0)
                        border-start border-warning border-3
                    @elseif(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                        border-start border-info border-3
                    @else
                        border-start border-success border-2
                    @endif
                ">
                    
                    {{-- === IMMAGINE CORRETTA === --}}
                    <div class="position-relative">
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 140px; object-fit: contain; background-color: #f8f9fa;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 140px;">
                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-secondary small">
                                {{ $prodotto->categoria_label ?? ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- Indicatori stato --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            @if($prodotto->malfunzionamenti_count > 0)
                                <span class="badge bg-warning small mb-1 d-block">
                                    {{ $prodotto->malfunzionamenti_count }} problemi
                                </span>
                            @endif
                            
                            @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                <span class="badge bg-danger small d-block">
                                    {{ $prodotto->critici_count }} critici
                                </span>
                            @endif
                            
                            @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                <span class="badge bg-success small d-block">
                                    <i class="bi bi-person-check"></i> Mio
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- === CONTENUTO CARD === --}}
                    <div class="card-body p-3">
                        {{-- Titolo --}}
                        <h6 class="card-title mb-2 fw-bold
                            @if($prodotto->hasMalfunzionamentiCritici())
                                text-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                text-warning
                            @else
                                text-primary
                            @endif
                        ">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- Modello --}}
                        @if($prodotto->modello)
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                            </p>
                        @endif

                        {{-- Descrizione breve --}}
                        <p class="card-text small text-muted mb-3">
                            {{ Str::limit($prodotto->descrizione, 60, '...') }}
                        </p>

                        {{-- Statistiche compatte --}}
                        <div class="row g-1 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 
                                    @if($prodotto->malfunzionamenti_count > 0) 
                                        bg-warning bg-opacity-10 
                                    @else 
                                        bg-success bg-opacity-10 
                                    @endif 
                                    rounded">
                                    <div class="fw-bold small text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                        {{ $prodotto->malfunzionamenti_count ?? 0 }}
                                    </div>
                                    <small class="text-muted">Problemi</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 
                                    @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0) 
                                        bg-danger bg-opacity-10 
                                    @else 
                                        bg-success bg-opacity-10 
                                    @endif 
                                    rounded">
                                    <div class="fw-bold small text-{{ isset($prodotto->critici_count) && $prodotto->critici_count > 0 ? 'danger' : 'success' }}">
                                        {{ $prodotto->critici_count ?? 0 }}
                                    </div>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                        </div>

                        {{-- Staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <p class="text-muted small mb-3">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </p>
                        @endif

                        {{-- Alert critici --}}
                        @if($prodotto->hasMalfunzionamentiCritici())
                            <div class="alert alert-danger py-1 mb-3">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>ATTENZIONE:</strong> Problemi critici
                                </small>
                            </div>
                        @endif

                        {{-- Pulsanti azione --}}
                        <div class="d-grid gap-1">
                            {{-- Visualizza dettagli --}}
                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                               class="btn btn-sm
                                    @if($prodotto->hasMalfunzionamentiCritici())
                                        btn-outline-danger
                                    @elseif($prodotto->malfunzionamenti_count > 0)
                                        btn-outline-warning
                                    @else
                                        btn-outline-primary
                                    @endif
                               ">
                                <i class="bi bi-eye me-1"></i>Dettagli Completi
                            </a>
                            
                            {{-- Malfunzionamenti --}}
                            @if($prodotto->malfunzionamenti_count > 0)
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-{{ $prodotto->hasMalfunzionamentiCritici() ? 'danger' : 'warning' }} btn-sm">
                                    <i class="bi bi-tools me-1"></i>
                                    Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                                </a>
                            @else
                                <div class="text-center py-1">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Nessun problema segnalato
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Stato vuoto --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        @if(request('search') || request('categoria') || request('filter'))
                            <i class="bi bi-search display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">
                                Prova a modificare i criteri di ricerca o 
                                <a href="{{ route('prodotti.completo.index') }}">visualizza tutti i prodotti</a>
                            </p>
                        @else
                            <i class="bi bi-box display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto disponibile</h5>
                            <p class="text-muted">Il catalogo è vuoto al momento</p>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-1"></i>Aggiungi Primo Prodotto
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- === PAGINAZIONE COMPATTA === --}}
    @if($prodotti->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $prodotti->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
/* === STILI COMPATTI CATALOGO TECNICO === */

/* Card prodotto base */
.product-card {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

/* Immagini prodotto CORRETTE */
.product-image {
    transition: transform 0.3s ease;
    padding: 0.5rem; /* Aggiunge padding per evitare zoom eccessivo */
}

.product-image:hover {
    transform: scale(1.05);
}

/* Badge più compatti */
.badge {
    font-size: 0.7rem;
}

/* Form controls più piccoli */
.form-select-sm,
.form-control-sm {
    font-size: 0.875rem;
}

/* Statistiche compatte */
.card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

/* Bordi colorati per stato prodotto */
.border-start.border-3 {
    border-left-width: 3px !important;
}

.border-start.border-2 {
    border-left-width: 2px !important;
}

/* Alert compatti */
.alert.py-1 {
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
}

/* Responsive migliorato */
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .product-image {
        height: 120px !important;
    }
    
    .btn-group-sm .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}

@media (max-width: 576px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: start !important;
    }
    
    .btn-group {
        margin-top: 0.5rem;
        width: 100%;
    }
    
    .h2 {
        font-size: 1.3rem !important;
    }
    
    .product-image {
        height: 100px !important;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
}

/* Scrollbar personalizzata */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Evidenziazione ricerca */
mark {
    background-color: #fff3cd;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
}

/* Animazioni leggere */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}

/* Stati hover migliorati */
.btn:hover {
    transform: translateY(-1px);
}

.badge:hover {
    transform: scale(1.05);
}
</style>
@endpush

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

window.PageData.prodottiCount = @json($prodotti->count());
window.PageData.prodottiTotal = @json($prodotti->total());
window.PageData.searchTerm = @json(request('search'));
window.PageData.categoria = @json(request('categoria'));
window.PageData.filtro = @json(request('filter'));
window.PageData.staffFilter = @json(request('staff_filter'));
window.PageData.searchActive = @json(request('search') ? true : false);
window.PageData.filtersActive = @json((request('categoria') || request('filter')) ? true : false);
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionWarning = @json(session('warning'));
// Aggiungi altri dati che potrebbero servire...
</script>
@endpush