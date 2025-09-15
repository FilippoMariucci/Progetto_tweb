{{-- 
    VISTA CATALOGO PRODOTTI PUBBLICO - FILTRI CATEGORIA CORRETTI
    Sistema Assistenza Tecnica - Gruppo 51
    
    Vista per utenti non autenticati (Livello 1) - CORREZIONE FILTRI
--}}

@extends('layouts.app')

@section('title', 'Catalogo Prodotti - ' . config('app.name'))

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Catalogo Prodotti
            </h2>
            <p class="text-muted small mb-0">
                Esplora la nostra gamma completa di prodotti per l'assistenza tecnica
            </p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- Link vista tecnica per utenti autenticati --}}
            @auth
                @if(Auth::user()->canViewMalfunzionamenti())
                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-tools"></i> Vista Tecnica
                    </a>
                @endif
            @endauth
            <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-geo-alt"></i> Centri Assistenza
            </a>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE === --}}
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
                    <i class="bi bi-tags text-success fs-5"></i>
                    <h6 class="fw-bold mb-0 mt-1">{{ $stats['categorie_count'] ?? 0 }}</h6>
                    <small class="text-muted">Categorie</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-calendar text-info fs-5"></i>
                    <h6 class="fw-bold mb-0 mt-1">{{ date('Y') }}</h6>
                    <small class="text-muted">Catalogo Aggiornato</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-shield-check text-warning fs-5"></i>
                    <h6 class="fw-bold mb-0 mt-1">24/7</h6>
                    <small class="text-muted">Assistenza</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === FORM RICERCA COMPATTO === --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('prodotti.pubblico.index') }}" class="row g-3" id="search-form">
                        {{-- Campo ricerca --}}
                        <div class="col-lg-5 col-md-7">
                            <label for="search" class="form-label small fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cerca prodotto (es: lavatrice o lav*)"
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="form-text small">
                                <strong>Suggerimento:</strong> Usa <code>*</code> per ricerche parziali
                            </div>
                        </div>

                        {{-- === CORREZIONE: Categoria Select === --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="categoria" class="form-label small fw-semibold">
                                <i class="bi bi-tags me-1"></i>Categoria
                            </label>
                            <select class="form-select form-select-sm" id="categoria" name="categoria">
                                <option value="">Tutte</option>
                                {{-- CORREZIONE: Loop attraverso array semplice di categorie --}}
                                @if(isset($categorie) && is_array($categorie) && count($categorie) > 0)
                                    @foreach($categorie as $categoria)
                                        <option value="{{ $categoria }}" 
                                                {{ request('categoria') == $categoria ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $categoria)) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Pulsanti --}}
                        <div class="col-lg-4 col-md-2">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('prodotti.pubblico.index') }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === CORREZIONE: FILTRI CATEGORIE BADGE === --}}
    @if(isset($categorie) && is_array($categorie) && count($categorie) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">Categorie:</small>
                    
                    {{-- Badge "Tutte" --}}
                    <a href="{{ route('prodotti.pubblico.index') }}" 
                       class="badge category-badge {{ !request('categoria') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none"
                       data-categoria="">
                        Tutte ({{ $stats['total_prodotti'] ?? 0 }})
                    </a>
                    
                    {{-- CORREZIONE: Badge per ogni categoria --}}
                    @foreach($categorie as $cat)
                        @php
                            // Ottieni il conteggio dalla stats
                            $count = isset($stats['per_categoria'][$cat]) ? $stats['per_categoria'][$cat] : 0;
                        @endphp
                        <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($cat) }}" 
                           class="badge category-badge {{ request('categoria') == $cat ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none"
                           data-categoria="{{ $cat }}">
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                            <span class="ms-1">({{ $count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI RICERCA === --}}
    @if(request('search') || request('categoria'))
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
                            @if(request('categoria'))
                                nella categoria "<em>{{ ucfirst(str_replace('_', ' ', request('categoria'))) }}</em>"
                            @endif
                        </div>
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI === --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 product-card">
                    
                    {{-- Immagine prodotto --}}
                    <div class="position-relative">
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 140px; object-fit: contain; background-color: #f8f9fa; padding: 0.5rem;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 140px;">
                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-primary small">
                                {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                            </span>
                        </div>

                        {{-- Badge prezzo --}}
                        @if($prodotto->prezzo)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success small">
                                    €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Contenuto card --}}
                    <div class="card-body p-3">
                        <h6 class="card-title text-primary mb-2 fw-bold">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        @if($prodotto->modello)
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                            </p>
                        @endif

                        <p class="card-text text-muted small mb-3">
                            {{ Str::limit($prodotto->descrizione, 80, '...') }}
                        </p>

                        <div class="d-grid">
                            <a href="{{ route('prodotti.pubblico.show', $prodotto) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Scheda Tecnica
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        @if(request('search') || request('categoria'))
                            <i class="bi bi-search display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">
                                Non abbiamo trovato prodotti che corrispondono ai tuoi criteri di ricerca.
                            </p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-1"></i>Vedi tutti i prodotti
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="$('#search').focus()">
                                    <i class="bi bi-search me-1"></i>Nuova ricerca
                                </button>
                            </div>
                        @else
                            <i class="bi bi-box display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Catalogo in aggiornamento</h5>
                            <p class="text-muted">
                                Il catalogo è momentaneamente vuoto. Torna presto per vedere i nostri prodotti!
                            </p>
                            <a href="{{ route('contatti') }}" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i>Contattaci per informazioni
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginazione --}}
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

    {{-- Info assistenza --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h5 class="text-primary mb-2">
                        <i class="bi bi-info-circle me-2"></i>
                        Hai bisogno di assistenza tecnica?
                    </h5>
                    <p class="mb-3 text-muted">
                        Sei un tecnico autorizzato? Accedi per visualizzare informazioni complete 
                        sui malfunzionamenti e le relative soluzioni tecniche.
                    </p>
                    
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i>Accesso Tecnici
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        @endguest
                        
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                        </a>
                        
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope me-1"></i>Contattaci
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Stili per i filtri categoria */
.category-badge {
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.category-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-card {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

.product-image {
    transition: transform 0.3s ease;
    border: 1px solid #e9ecef;
}

.product-image:hover {
    transform: scale(1.02);
    border-color: #007bff;
}

@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .product-image {
        height: 120px !important;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
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

// Dati di ricerca pubblica per JS (usati in index.js)
</script>
<script>
window.PageData.searchTerm = @json(request('search'));
window.PageData.categoria = @json(request('categoria'));
window.PageData.risultati = @json(isset($prodotti) ? $prodotti->total() : 0);
// ...
// Aggiungi altri dati che potrebbero servire...
</script>
@endpush