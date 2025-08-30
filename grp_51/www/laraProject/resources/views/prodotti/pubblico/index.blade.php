@extends('layouts.app')

@section('title', 'Catalogo Prodotti - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    {{-- === HEADER CATALOGO PUBBLICO === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2">
                                <i class="bi bi-box-seam me-2"></i>
                                Catalogo Prodotti
                            </h1>
                            <p class="mb-0 opacity-75">
                                Esplora la nostra gamma completa di prodotti per l'assistenza tecnica
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            {{-- Statistiche rapide --}}
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <span class="h4 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</span>
                                    <small class="d-block">Prodotti</small>
                                </div>
                                <div>
                                    <span class="h4 fw-bold mb-0">{{ $stats['categorie_count'] ?? 0 }}</span>
                                    <small class="d-block">Categorie</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === FILTRI E RICERCA PUBBLICA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <form method="GET" action="{{ route('prodotti.index') }}" class="row g-3 align-items-end">
                        
                        {{-- Campo ricerca con supporto wildcard --}}
                        <div class="col-md-5">
                            <label for="search" class="form-label fw-semibold">
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
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                💡 Usa <code>*</code> per ricerche parziali: "lav*" trova lavatrici, lavastoviglie, lavelli...
                            </div>
                        </div>

                        {{-- Filtro categoria --}}
                        <div class="col-md-3">
                            <label for="categoria" class="form-label fw-semibold">
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

                        {{-- Pulsanti azione --}}
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('prodotti.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                                {{-- Link per tecnici se autenticati --}}
                                @auth
                                    @if(Auth::user()->canViewMalfunzionamenti())
                                        <a href="{{ route('prodotti.completo.index') }}" 
                                           class="btn btn-outline-info" 
                                           title="Vista Tecnica Completa">
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

    {{-- === FILTRI RAPIDI CATEGORIA === --}}
    @if(isset($categorie) && count($categorie) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-secondary fs-6 py-2 px-3">Categorie:</span>
                    {{-- Mostra tutte se non filtrate --}}
                    <a href="{{ route('prodotti.index') }}" 
                       class="badge {{ !request('categoria') ? 'bg-primary' : 'bg-light text-dark' }} fs-6 py-2 px-3 text-decoration-none">
                        Tutte
                    </a>
                    @foreach($categorie as $cat)
                        <a href="{{ route('prodotti.index') }}?categoria={{ urlencode($cat) }}" 
                           class="badge {{ request('categoria') == $cat ? 'bg-primary' : 'bg-light text-dark' }} fs-6 py-2 px-3 text-decoration-none">
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI RICERCA (se presente) === --}}
    @if(request('search') || request('categoria'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Risultati di ricerca:</strong>
                            Trovati <strong>{{ $prodotti->total() }}</strong> prodotti
                            @if(request('search'))
                                per "<em>{{ request('search') }}</em>"
                            @endif
                            @if(request('categoria'))
                                nella categoria "<em>{{ request('categoria') }}</em>"
                            @endif
                        </div>
                        <a href="{{ route('prodotti.index') }}" class="btn btn-sm btn-outline-info">
                            Rimuovi filtri
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI === --}}
    <div class="row g-4 mb-4">
        @forelse($prodotti as $prodotto)
            <div class="col-lg-4 col-md-6">
                <div class="card card-custom h-100 product-card">
                    {{-- Immagine prodotto --}}
                    <div class="position-relative">
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            {{-- Placeholder se non c'è immagine --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 200px;">
                                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-primary">{{ ucfirst($prodotto->categoria) }}</span>
                        </div>

                        {{-- Badge prezzo (se presente) --}}
                        @if($prodotto->prezzo)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Contenuto card --}}
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary mb-2">
                            {{ $prodotto->nome }}
                        </h5>
                        
                        @if($prodotto->modello)
                            <h6 class="card-subtitle mb-2 text-muted">
                                Modello: {{ $prodotto->modello }}
                            </h6>
                        @endif

                        <p class="card-text flex-grow-1">
                            {{ Str::limit($prodotto->descrizione, 120) }}
                        </p>

                        {{-- Footer card con pulsanti --}}
                        <div class="d-grid">
                            <a href="{{ route('prodotti.show', $prodotto) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>
                                Visualizza Dettagli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Nessun prodotto trovato --}}
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box display-1 text-muted mb-3"></i>
                        <h3>Nessun prodotto trovato</h3>
                        @if(request('search') || request('categoria'))
                            <p class="text-muted mb-4">
                                Non abbiamo trovato prodotti che corrispondono ai tuoi criteri di ricerca.
                                <br>
                                Prova con termini di ricerca diversi o 
                                <a href="{{ route('prodotti.index') }}">visualizza tutti i prodotti</a>.
                            </p>
                        @else
                            <p class="text-muted mb-4">
                                Il nostro catalogo è momentaneamente vuoto.
                                <br>
                                Torna più tardi per vedere i nostri prodotti!
                            </p>
                        @endif
                        
                        {{-- Suggerimenti di ricerca --}}
                        @if(request('search'))
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="alert alert-info">
                                        <h6>💡 Suggerimenti per la ricerca:</h6>
                                        <ul class="list-unstyled mb-0 text-start">
                                            <li>• Prova con termini più generici (es: "lava" invece di "lavatrice")</li>
                                            <li>• Usa il carattere * per ricerche parziali (es: "lav*")</li>
                                            <li>• Controlla l'ortografia</li>
                                            <li>• Prova a cercare per categoria</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- === PAGINAZIONE === --}}
    @if($prodotti->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $prodotti->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    @endif

    {{-- === INFO FOOTER (per utenti pubblici) === --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom bg-light border-0">
                <div class="card-body text-center py-4">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Hai bisogno di assistenza?
                    </h5>
                    <p class="mb-3">
                        Sei un tecnico autorizzato? Accedi per vedere informazioni complete sui malfunzionamenti e le soluzioni tecniche.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i>
                                Accesso Tecnici
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
@endsection

{{-- === STILI PERSONALIZZATI === --}}
@push('styles')
<style>
/* Stili per la vista pubblica del catalogo */
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0,0,0,0.125);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

.product-image {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.card-custom {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

/* Badge personalizzati */
.badge {
    font-weight: 500;
}

/* Form styling improvements */
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    border-color: #80bdff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-card:hover {
        transform: none;
    }
    
    .d-flex.gap-3.justify-content-center.flex-wrap {
        flex-direction: column;
        align-items: stretch;
    }
    
    .d-flex.gap-3.justify-content-center.flex-wrap .btn {
        margin-bottom: 0.5rem;
    }
}

/* Fix per paginazione */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #007bff;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush

{{-- === SCRIPTS JAVASCRIPT === --}}
@push('scripts')
<script>
$(document).ready(function() {
    // === GESTIONE FORM DI RICERCA ===
    
    // Pulsante per pulire la ricerca
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    // Submit form quando cambia la categoria
    $('#categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Suggerimenti di ricerca dinamici (se necessario)
    let searchTimeout;
    $('#search').on('input', function() {
        const searchTerm = $(this).val();
        
        // Mostra suggerimento wildcard se l'utente sta digitando
        if (searchTerm.length > 2 && !searchTerm.includes('*')) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Potresti aggiungere qui suggerimenti dinamici
                console.log('Termine ricerca:', searchTerm);
            }, 500);
        }
    });
    
    // === GESTIONE KEYBOARD SHORTCUTS ===
    
    // Ctrl/Cmd + K per focussare la ricerca
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
    });
    
    // === ANALYTICS E TRACKING (se necessario) ===
    
    // Track clicks sui prodotti
    $('.product-card a').on('click', function() {
        const prodottoNome = $(this).closest('.card').find('.card-title').text().trim();
        console.log('Prodotto visualizzato:', prodottoNome);
        
        // Qui potresti aggiungere Google Analytics o altro tracking
        // gtag('event', 'view_product', { product_name: prodottoNome });
    });
    
    // Track ricerche
    $('form').on('submit', function() {
        const searchTerm = $('#search').val();
        const categoria = $('#categoria').val();
        
        if (searchTerm || categoria) {
            console.log('Ricerca effettuata:', { search: searchTerm, categoria: categoria });
            
            // Tracking ricerca
            // gtag('event', 'search', { search_term: searchTerm, category: categoria });
        }
    });
    
    // === MIGLIORAMENTI UX ===
    
    // Loading spinner per i form
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                .prop('disabled', true);
        
        // Ripristina dopo 3 secondi (fallback)
        setTimeout(function() {
            submitBtn.html(originalText).prop('disabled', false);
        }, 3000);
    });
    
    // Smooth scroll per risultati se la pagina è lunga
    if (window.location.search && $(window).scrollTop() > 300) {
        $('html, body').animate({
            scrollTop: $('.row.g-4.mb-4').offset().top - 100
        }, 500);
    }
    
    console.log('Vista catalogo pubblico caricata - Prodotti trovati:', {{ $prodotti->total() }});
});
</script>
@endpush