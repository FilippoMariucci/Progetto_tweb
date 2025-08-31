{{-- 
    VISTA CATALOGO PRODOTTI PUBBLICO 
    Questa vista mostra il catalogo prodotti per utenti non autenticati (Livello 1)
    Include: ricerca con wildcard, filtri categoria, schede tecniche (senza malfunzionamenti)
--}}

@extends('layouts.app')

@section('title', 'Catalogo Prodotti - ' . config('app.name'))

@section('content')
<div class="container-fluid px-3 px-lg-4">
    
    {{-- Header principale ridimensionato --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        {{-- Titolo e descrizione --}}
                        <div class="col-lg-8 col-md-7">
                            <h2 class="mb-1 fw-bold">
                                <i class="bi bi-box-seam me-2"></i>
                                Catalogo Prodotti
                            </h2>
                            <p class="mb-0 opacity-90">
                                Esplora la nostra gamma completa di prodotti per l'assistenza tecnica
                            </p>
                        </div>
                        
                        {{-- Statistiche compatte --}}
                        <div class="col-lg-4 col-md-5 mt-2 mt-md-0">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                        <div class="h5 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</div>
                                        <small class="opacity-90">Prodotti</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                        <div class="h5 fw-bold mb-0">{{ $stats['categorie_count'] ?? 0 }}</div>
                                        <small class="opacity-90">Categorie</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form di ricerca compatto --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('prodotti.pubblico.index') }}" class="row g-3">

                        {{-- Campo di ricerca principale con wildcard --}}
                        <div class="col-lg-6 col-md-8">
                            <label for="search" class="form-label fw-semibold text-primary">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cerca prodotto (es: lavatrice o lav*)"
                                       autocomplete="off">
                                {{-- Pulsante per pulire la ricerca --}}
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Pulisci ricerca">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            {{-- Suggerimento per l'uso del wildcard --}}
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Usa <code>*</code> per ricerche parziali (es: "lav*" trova lavatrici, lavastoviglie, lavelli...)
                            </div>
                        </div>

                        {{-- Filtro per categoria --}}
                        <div class="col-lg-3 col-md-4">
                            <label for="categoria" class="form-label fw-semibold text-primary">
                                <i class="bi bi-tags me-1"></i>Categoria
                            </label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Tutte le categorie</option>
                                @if(isset($categorie) && count($categorie) > 0)
                                    @foreach($categorie as $cat)
                                        <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Pulsanti di azione --}}
                        <div class="col-lg-3 col-md-12">
                            <label class="form-label d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                {{-- Pulsante cerca --}}
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- Pulsante reset filtri --}}
                                <a href="{{ route('prodotti.pubblico.index') }}" 
                                   class="btn btn-outline-secondary" 
                                   title="Rimuovi tutti i filtri">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                                {{-- Link vista tecnica (solo per utenti autenticati) --}}
                                @auth
                                    @if(Auth::user()->canViewMalfunzionamenti())
                                        <a href="{{ route('prodotti.completo.index') }}" 
                                           class="btn btn-outline-info" 
                                           title="Vista Tecnica Completa (con malfunzionamenti)">
                                            <i class="bi bi-tools"></i>
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtri rapidi per categorie (Pills) --}}
    @if(isset($categorie) && count($categorie) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-secondary py-2 px-3">
                        <i class="bi bi-funnel me-1"></i>Categorie:
                    </span>
                    
                    {{-- Link "Tutte" sempre visibile --}}
                    <a href="{{ route('prodotti.pubblico.index') }}" 
                       class="badge {{ !request('categoria') ? 'bg-primary' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                        Tutte ({{ $stats['total_prodotti'] ?? 0 }})
                    </a>
                    
                    {{-- Links per ogni categoria --}}
                    @foreach($categorie as $cat)
                        <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($cat) }}" 
                           class="badge {{ request('categoria') == $cat ? 'bg-primary' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                            {{-- Conta prodotti per categoria se disponibile --}}
                            @if(isset($stats['per_categoria'][$cat]))
                                ({{ $stats['per_categoria'][$cat] }})
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Messaggio risultati ricerca --}}
    @if(request('search') || request('categoria'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm py-2">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Risultati di ricerca:</strong>
                                    Trovati <span class="badge bg-primary">{{ $prodotti->total() }}</span> prodotti
                                    @if(request('search'))
                                        per "<em class="text-primary">{{ request('search') }}</em>"
                                    @endif
                                    @if(request('categoria'))
                                        nella categoria "<em class="text-primary">{{ request('categoria') }}</em>"
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end mt-2 mt-lg-0">
                            <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Rimuovi filtri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Griglia prodotti principale con dimensioni ridotte --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                {{-- Card prodotto compatta --}}
                <div class="card h-100 shadow-sm border-0 product-card">
                    
                    {{-- Sezione immagine ridimensionata --}}
                    <div class="position-relative overflow-hidden">
                        @if($prodotto->foto)
                            {{-- Immagine prodotto esistente --}}
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 160px; object-fit: cover;">
                        @else
                            {{-- Placeholder per prodotti senza immagine --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 160px;">
                                <i class="bi bi-box text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-primary bg-opacity-90 px-2 py-1">
                                <i class="bi bi-tag me-1"></i>{{ ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- Badge prezzo (se disponibile) --}}
                        @if($prodotto->prezzo)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success bg-opacity-90 px-2 py-1">
                                    <i class="bi bi-currency-euro me-1"></i>{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        
                    </div>

                    {{-- Corpo della card compatto --}}
                    <div class="card-body d-flex flex-column p-3">
                        {{-- Titolo prodotto --}}
                        <h6 class="card-title text-primary mb-2 fw-bold">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- Modello se presente --}}
                        @if($prodotto->modello)
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-gear me-1"></i>Modello: {{ $prodotto->modello }}
                            </p>
                        @endif

                        {{-- Descrizione troncata --}}
                        <p class="card-text flex-grow-1 text-muted small">
                            {{ Str::limit($prodotto->descrizione, 100, '...') }}
                        </p>

                        {{-- Informazioni aggiuntive compatte --}}
                        <div class="row g-1 mb-2 small text-muted">
                            @if($prodotto->codice_prodotto)
                                <div class="col-6">
                                    <i class="bi bi-qr-code me-1"></i>{{ $prodotto->codice_prodotto }}
                                </div>
                            @endif
                            @if($prodotto->anno_produzione)
                                <div class="col-6">
                                    <i class="bi bi-calendar me-1"></i>{{ $prodotto->anno_produzione }}
                                </div>
                            @endif
                        </div>

                        {{-- Pulsante azione principale --}}
                        <div class="d-grid">
                            <a href="{{ route('prodotti.pubblico.show', $prodotto) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>
                                Scheda Tecnica
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Stato vuoto - Nessun prodotto trovato --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-4">
                        <div class="empty-state">
                            <i class="bi bi-box display-4 text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">Nessun prodotto trovato</h4>
                            
                            @if(request('search') || request('categoria'))
                                <p class="text-muted mb-3">
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
                                <p class="text-muted mb-3">
                                    Il catalogo è momentaneamente vuoto. Torna presto per vedere i nostri prodotti!
                                </p>
                                <a href="{{ route('contatti') }}" class="btn btn-primary">
                                    <i class="bi bi-envelope me-1"></i>Contattaci per informazioni
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
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

    {{-- Sezione informazioni compatta --}}
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h5 class="text-primary mb-2">
                                <i class="bi bi-info-circle me-2"></i>
                                Hai bisogno di assistenza tecnica?
                            </h5>
                            <p class="mb-3 text-muted">
                                Sei un tecnico autorizzato? Accedi per visualizzare informazioni complete 
                                sui malfunzionamenti e le relative soluzioni tecniche.
                            </p>
                            
                            {{-- Pulsanti azione --}}
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        <i class="bi bi-person-check me-1"></i>
                                        Accesso Tecnici
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard
                                    </a>
                                @endguest
                                
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Centri Assistenza
                                </a>
                                
                                <a href="{{ route('contatti') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-envelope me-1"></i>
                                    Contattaci
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

{{-- CSS personalizzato per layout compatto --}}
@push('styles')
<style>
/* Stili generali */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Stili per le card prodotto compatte */
.product-card {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 2rem rgba(0,0,0,0.15) !important;
}

.product-image {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

/* Overlay per azioni rapide */
.product-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

/* Miglioramenti form */
.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Stili per i badge */
.badge {
    font-weight: 500;
}

.badge:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .product-card:hover {
        transform: none;
    }
    
    .product-overlay {
        opacity: 1;
        background: rgba(0,0,0,0.6);
    }
    
    /* Su mobile, 2 card per riga */
    .col-xl-3.col-lg-4.col-md-6.col-sm-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Su schermi molto piccoli, 1 card per riga */
    .col-xl-3.col-lg-4.col-md-6.col-sm-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}

/* Stili paginazione compatti inline */
.pagination-compact .pagination {
    margin-bottom: 0;
}

.pagination-compact .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    margin: 0;
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-compact .page-item {
    margin: 0 1px;
}

.pagination-compact .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination-compact .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    text-decoration: none;
}

/* Animazioni sottili */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeInUp 0.4s ease forwards;
}
</style>
@endpush

{{-- JavaScript semplificato --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Inizializzazione
    console.log('Catalogo prodotti caricato - Prodotti:', {{ $prodotti->total() }});
    
    // Gestione form di ricerca
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    // Submit automatico quando cambia la categoria
    $('#categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Shortcut tastiera per ricerca
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
    });
    
    // Gestione errori immagini
    $('.product-image').on('error', function() {
        $(this).replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 160px;">
                <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
            </div>
        `);
    });
    
    // Loading per form submit
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                  .prop('disabled', true);
        
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, 3000);
    });
});
</script>
@endpush