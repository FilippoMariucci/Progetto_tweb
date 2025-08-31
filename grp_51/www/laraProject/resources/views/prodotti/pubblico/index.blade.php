{{-- 
    VISTA CATALOGO PRODOTTI PUBBLICO 
    Questa vista mostra il catalogo prodotti per utenti non autenticati (Livello 1)
    Include: ricerca, filtri, paginazione e accesso alle schede tecniche (senza malfunzionamenti)
--}}

@extends('layouts.app')

@section('title', 'Catalogo Prodotti - ' . config('app.name'))

@section('content')
<div class="container-fluid px-3 px-lg-4">
    
    {{-- === HEADER PRINCIPALE CON STATISTICHE === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        {{-- Titolo e descrizione --}}
                        <div class="col-lg-8 col-md-7">
                            <h1 class="h2 mb-2 fw-bold">
                                <i class="bi bi-box-seam me-2"></i>
                                Catalogo Prodotti
                            </h1>
                            <p class="mb-0 opacity-90 lead">
                                Esplora la nostra gamma completa di prodotti per l'assistenza tecnica
                            </p>
                        </div>
                        
                        {{-- Statistiche in card compatte --}}
                        <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                        <div class="h4 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</div>
                                        <small class="opacity-90">Prodotti</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                        <div class="h4 fw-bold mb-0">{{ $stats['categorie_count'] ?? 0 }}</div>
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

    {{-- === SEZIONE FILTRI E RICERCA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    {{-- Form di ricerca con layout responsivo --}}
                    <form method="GET" action="{{ route('prodotti.index') }}" class="row g-3">
                        
                        {{-- Campo di ricerca principale con wildcard --}}
                        <div class="col-lg-6 col-md-8">
                            <label for="search" class="form-label fw-semibold text-primary">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control form-control-lg" 
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
                                💡 <strong>Suggerimento:</strong> Usa <code>*</code> per ricerche parziali (es: "lav*" trova lavatrici, lavastoviglie, lavelli...)
                            </div>
                        </div>

                        {{-- Filtro per categoria --}}
                        <div class="col-lg-3 col-md-4">
                            <label for="categoria" class="form-label fw-semibold text-primary">
                                <i class="bi bi-tags me-1"></i>Categoria
                            </label>
                            <select class="form-select form-select-lg" id="categoria" name="categoria">
                                <option value="">🏷️ Tutte le categorie</option>
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
                                <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- Pulsante reset filtri --}}
                                <a href="{{ route('prodotti.index') }}" 
                                   class="btn btn-outline-secondary btn-lg" 
                                   title="Rimuovi tutti i filtri">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                                {{-- Link vista tecnica (solo per utenti autenticati) --}}
                                @auth
                                    @if(Auth::user()->canViewMalfunzionamenti())
                                        <a href="{{ route('prodotti.completo.index') }}" 
                                           class="btn btn-outline-info btn-lg" 
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

    {{-- === FILTRI RAPIDI PER CATEGORIE (Pills) === --}}
    @if(isset($categorie) && count($categorie) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-secondary fs-6 py-2 px-3">
                        <i class="bi bi-funnel me-1"></i>Categorie:
                    </span>
                    
                    {{-- Link "Tutte" sempre visibile --}}
                    <a href="{{ route('prodotti.index') }}" 
                       class="badge {{ !request('categoria') ? 'bg-primary' : 'bg-light text-dark border' }} fs-6 py-2 px-3 text-decoration-none">
                        Tutte ({{ $stats['total_prodotti'] ?? 0 }})
                    </a>
                    
                    {{-- Links per ogni categoria --}}
                    @foreach($categorie as $cat)
                        <a href="{{ route('prodotti.index') }}?categoria={{ urlencode($cat) }}" 
                           class="badge {{ request('categoria') == $cat ? 'bg-primary' : 'bg-light text-dark border' }} fs-6 py-2 px-3 text-decoration-none">
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

    {{-- === MESSAGGIO RISULTATI RICERCA === --}}
    @if(request('search') || request('categoria'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
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
                            <a href="{{ route('prodotti.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Rimuovi filtri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI PRINCIPALE === --}}
    <div class="row g-4 mb-5" id="prodotti-grid">
        @forelse($prodotti as $prodotto)
            <div class="col-xl-4 col-lg-6 col-md-6">
                {{-- Card prodotto con hover effects --}}
                <div class="card h-100 shadow-sm border-0 product-card">
                    
                    {{-- Sezione immagine con overlay informazioni --}}
                    <div class="position-relative overflow-hidden">
                        @if($prodotto->foto)
                            {{-- Immagine prodotto esistente --}}
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 250px; object-fit: cover;">
                        @else
                            {{-- Placeholder semplice per prodotti senza immagine --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 250px;">
                                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Badge categoria (sempre visibile) --}}
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-primary bg-opacity-90 fs-6 px-3 py-2">
                                <i class="bi bi-tag me-1"></i>{{ ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- Badge prezzo (se disponibile) --}}
                        @if($prodotto->prezzo)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success bg-opacity-90 fs-6 px-3 py-2">
                                    <i class="bi bi-currency-euro me-1"></i>{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        {{-- Overlay con azioni rapide (visibile al hover) --}}
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 product-overlay">
                            <div class="d-flex gap-2">
                                <a href="{{ route('prodotti.show', $prodotto) }}" 
                                   class="btn btn-light btn-sm flex-fill text-center">
                                    <i class="bi bi-eye me-1"></i>Dettagli
                                </a>
                                {{-- Link download scheda tecnica se disponibile --}}
                                @if($prodotto->scheda_tecnica)
                                    <a href="{{ asset('storage/' . $prodotto->scheda_tecnica) }}" 
                                       target="_blank"
                                       class="btn btn-outline-light btn-sm" 
                                       title="Scarica scheda tecnica">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Corpo della card con informazioni prodotto --}}
                    <div class="card-body d-flex flex-column p-4">
                        {{-- Titolo prodotto --}}
                        <h5 class="card-title text-primary mb-2 fw-bold">
                            {{ $prodotto->nome }}
                        </h5>
                        
                        {{-- Modello se presente --}}
                        @if($prodotto->modello)
                            <h6 class="card-subtitle mb-2 text-muted">
                                <i class="bi bi-gear me-1"></i>Modello: {{ $prodotto->modello }}
                            </h6>
                        @endif

                        {{-- Descrizione troncata --}}
                        <p class="card-text flex-grow-1 text-muted">
                            {{ Str::limit($prodotto->descrizione, 140, '...') }}
                        </p>

                        {{-- Informazioni aggiuntive compatte --}}
                        <div class="row g-2 mb-3 small text-muted">
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
                            <a href="{{ route('prodotti.show', $prodotto) }}" 
                               class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-eye me-2"></i>
                                Visualizza Scheda Tecnica
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- === STATO VUOTO - Nessun prodotto trovato === --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-box display-1 text-muted mb-4"></i>
                            <h3 class="text-muted mb-3">Nessun prodotto trovato</h3>
                            
                            @if(request('search') || request('categoria'))
                                <p class="text-muted mb-4 lead">
                                    Non abbiamo trovato prodotti che corrispondono ai tuoi criteri di ricerca.
                                </p>
                                
                                {{-- Suggerimenti per migliorare la ricerca --}}
                                <div class="row justify-content-center mb-4">
                                    <div class="col-lg-8">
                                        <div class="alert alert-light border">
                                            <h6 class="text-primary mb-3">💡 Suggerimenti per trovare quello che cerchi:</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled mb-0 text-start small">
                                                        <li>• Prova termini più generici</li>
                                                        <li>• Usa il carattere * per ricerche parziali</li>
                                                        <li>• Verifica l'ortografia</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled mb-0 text-start small">
                                                        <li>• Prova una categoria diversa</li>
                                                        <li>• Rimuovi alcuni filtri</li>
                                                        <li>• Cerca per modello o codice</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ route('prodotti.index') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-left me-1"></i>Vedi tutti i prodotti
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" onclick="$('#search').focus()">
                                        <i class="bi bi-search me-1"></i>Nuova ricerca
                                    </button>
                                </div>
                            @else
                                <p class="text-muted mb-4 lead">
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

    {{-- === PAGINAZIONE AVANZATA === --}}
    @if($prodotti->hasPages())
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    {{-- Info paginazione --}}
                    <div class="text-muted">
                        Mostrando {{ $prodotti->firstItem() }} - {{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </div>
                    
                    {{-- Links di paginazione --}}
                    <div class="pagination-wrapper">
                        {{ $prodotti->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === SEZIONE INFORMAZIONI E CALL TO ACTION === --}}
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-light border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h4 class="text-primary mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Hai bisogno di assistenza tecnica?
                            </h4>
                            <p class="mb-4 text-muted lead">
                                Sei un tecnico autorizzato? Accedi per visualizzare informazioni complete 
                                sui malfunzionamenti e le relative soluzioni tecniche.
                            </p>
                            
                            {{-- Pulsanti azione --}}
                            <div class="row g-3 justify-content-center">
                                @guest
                                    <div class="col-auto">
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                            <i class="bi bi-person-check me-2"></i>
                                            Accesso Tecnici
                                        </a>
                                    </div>
                                @else
                                    <div class="col-auto">
                                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                            <i class="bi bi-speedometer2 me-2"></i>
                                            Vai alla Dashboard
                                        </a>
                                    </div>
                                @endguest
                                
                                <div class="col-auto">
                                    <a href="{{ route('centri.index') }}" class="btn btn-outline-info btn-lg">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Centri Assistenza
                                    </a>
                                </div>
                                
                                <div class="col-auto">
                                    <a href="{{ route('contatti') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-envelope me-2"></i>
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
</div>
@endsection

{{-- === STILI CSS PERSONALIZZATI === --}}
@push('styles')
<style>
/* === STILI GENERALI PER IL CATALOGO PUBBLICO === */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* === STILI PER LE CARD PRODOTTO === */
.product-card {
    transition: all 0.3s ease;
    border-radius: 0.75rem;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
}

.product-image {
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.08);
}

/* === OVERLAY PER AZIONI RAPIDE === */
.product-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

/* === MIGLIORAMENTI FORM === */
.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-control-lg,
.form-select-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

/* === STILI PER I BADGE === */
.badge {
    font-weight: 500;
    letter-spacing: 0.02em;
}

.badge:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* === EMPTY STATE STYLING === */
.empty-state i {
    opacity: 0.5;
}

/* === RESPONSIVE IMPROVEMENTS === */
@media (max-width: 768px) {
    .product-card:hover {
        transform: none;
    }
    
    .product-overlay {
        opacity: 1;
        background: rgba(0,0,0,0.6);
    }
    
    .col-xl-4.col-lg-6.col-md-6 {
        /* Su mobile, una card per riga */
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card-body {
        padding: 1.25rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .row.g-4 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
}

/* === ANIMAZIONI PERSONALIZZATE === */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeInUp 0.6s ease forwards;
}

.product-card:nth-child(2) { animation-delay: 0.1s; }
.product-card:nth-child(3) { animation-delay: 0.2s; }
.product-card:nth-child(4) { animation-delay: 0.3s; }

/* === STILI PAGINAZIONE === */
.pagination-wrapper .pagination {
    margin-bottom: 0;
}

.page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    margin: 0 2px;
    border-radius: 0.5rem;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}
</style>
@endpush

{{-- === JAVASCRIPT E JQUERY === --}}
@push('scripts')
<script>
$(document).ready(function() {
    // === INIZIALIZZAZIONE COMPONENTI ===
    console.log('🚀 Inizializzazione catalogo prodotti pubblico');
    console.log('📊 Prodotti caricati:', {{ $prodotti->total() }});
    
    // === GESTIONE FORM DI RICERCA ===
    
    // Pulsante per pulire il campo di ricerca
    $('#clearSearch').on('click', function() {
        console.log('🧹 Pulizia campo ricerca');
        $('#search').val('').focus();
        // Opzionale: submit automatico dopo pulizia
        // $(this).closest('form').submit();
    });
    
    // Submit automatico quando cambia la categoria
    $('#categoria').on('change', function() {
        const selectedCategory = $(this).val();
        console.log('🏷️ Categoria selezionata:', selectedCategory || 'Tutte');
        $(this).closest('form').submit();
    });
    
    // === GESTIONE RICERCA AVANZATA ===
    
    let searchTimeout;
    $('#search').on('input', function() {
        const searchTerm = $(this).val();
        
        // Mostra suggerimenti per wildcard se l'utente sta digitando
        if (searchTerm.length > 2 && !searchTerm.includes('*')) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Suggerimento per wildcard
                if (!$('#wildcard-suggestion').length) {
                    $('#search').after(
                        '<div id="wildcard-suggestion" class="position-absolute bg-info text-white p-2 rounded mt-1 small" style="z-index: 1000;">' +
                        '💡 Prova ad aggiungere * alla fine per ricerche più ampie' +
                        '</div>'
                    );
                    
                    // Rimuovi suggerimento dopo 3 secondi
                    setTimeout(function() {
                        $('#wildcard-suggestion').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 3000);
                }
            }, 1000);
        }
    });
    
    // Rimuovi suggerimento quando l'utente clicca altrove
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search, #wildcard-suggestion').length) {
            $('#wildcard-suggestion').remove();
        }
    });
    
    // === KEYBOARD SHORTCUTS ===
    
    // Ctrl/Cmd + K per focussare la ricerca (standard web)
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus().select();
            console.log('⌨️ Shortcut ricerca attivato');
        }
        
        // ESC per pulire la ricerca se è attiva
        if (e.key === 'Escape' && document.activeElement === $('#search')[0]) {
            $('#search').val('').blur();
        }
    });
    
    // === ANALYTICS E TRACKING ===
    
    // Track visualizzazioni prodotti
    $('.product-card a[href*="prodotti"]').on('click', function(e) {
        const $card = $(this).closest('.product-card');
        const prodottoNome = $card.find('.card-title').text().trim();
        const categoria = $card.find('.badge:first').text().trim();
        
        console.log('👁️ Prodotto visualizzato:', {
            nome: prodottoNome,
            categoria: categoria,
            url: $(this).attr('href')
        });
        
        // Qui puoi integrare Google Analytics, Mixpanel, etc.
        // gtag('event', 'view_product', {
        //     product_name: prodottoNome,
        //     product_category: categoria
        // });
    });
    
    // Track ricerche effettuate
    $('form').on('submit', function(e) {
        const searchTerm = $('#search').val();
        const categoria = $('#categoria').val();
        
        if (searchTerm || categoria) {
            console.log('🔍 Ricerca effettuata:', {
                termine: searchTerm,
                categoria: categoria || 'Tutte',
                timestamp: new Date().toISOString()
            });
            
            // Analytics per ricerche
            // gtag('event', 'search', {
            //     search_term: searchTerm,
            //     category: categoria,
            //     results_count: {{ $prodotti->total() }}
            // });
        }
    });
    
    // Track click sui filtri categoria (pills)
    $('a.badge[href*="categoria="]').on('click', function() {
        const categoria = $(this).text().trim();
        console.log('🏷️ Filtro categoria cliccato:', categoria);
    });
    
    // === MIGLIORAMENTI UX E INTERATTIVITA' ===
    
    // Loading spinner per i form
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        // Mostra loading
        $submitBtn.html('<i class="bi bi-hourglass-split me-1 spin"></i>Cercando...')
                  .prop('disabled', true);
        
        // Ripristina stato dopo 3 secondi (fallback)
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, 3000);
    });
    
    // Smooth scroll verso risultati se la pagina è lunga
    if (window.location.search && $(window).scrollTop() > 400) {
        $('html, body').animate({
            scrollTop: $('#prodotti-grid').offset().top - 100
        }, 800, 'easeOutQuart');
    }
    
    // === GESTIONE IMMAGINI ===
    
    // Lazy loading migliorato per le immagini
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        // Osserva tutte le immagini prodotto
        $('.product-image').each(function() {
            imageObserver.observe(this);
        });
    }
    
    // Gestione errori caricamento immagini
    $('.product-image').on('error', function() {
        console.warn('⚠️ Errore caricamento immagine:', $(this).attr('src'));
        
        // Sostituisci con placeholder semplice
        $(this).replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 250px;">
                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
            </div>
        `);
    });
    
    // === ACCESSIBILITY IMPROVEMENTS ===
    
    // Focus management per ricerca
    $('#search').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
    
    // Annunci per screen reader
    const announceResults = () => {
        const resultsCount = {{ $prodotti->total() }};
        if (resultsCount === 0) {
            $('#prodotti-grid').attr('aria-label', 'Nessun prodotto trovato');
        } else {
            $('#prodotti-grid').attr('aria-label', `${resultsCount} prodotti trovati`);
        }
    };
    announceResults();
    
    // === PERFORMANCE OPTIMIZATIONS ===
    
    // Debounce per eventi costosi
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Gestione resize window ottimizzata
    const handleResize = debounce(() => {
        // Ricalcola layout se necessario
        console.log('📱 Window resized, new width:', $(window).width());
        
        // Adatta comportamento hover su mobile
        if ($(window).width() <= 768) {
            $('.product-card').removeClass('hover-enabled');
        } else {
            $('.product-card').addClass('hover-enabled');
        }
    }, 250);
    
    $(window).on('resize', handleResize);
    handleResize(); // Esegui subito
    
    // === GESTIONE STATI DELL'APPLICAZIONE ===
    
    // Salva stato ricerca in sessionStorage per navigazione
    const saveSearchState = () => {
        const state = {
            search: $('#search').val(),
            categoria: $('#categoria').val(),
            timestamp: Date.now()
        };
        
        try {
            sessionStorage.setItem('catalogoSearchState', JSON.stringify(state));
        } catch (e) {
            console.warn('⚠️ Impossibile salvare stato ricerca:', e);
        }
    };
    
    // Salva stato ad ogni submit
    $('form').on('submit', saveSearchState);
    
    // Ripristina stato se torniamo indietro
    const restoreSearchState = () => {
        try {
            const saved = sessionStorage.getItem('catalogoSearchState');
            if (saved) {
                const state = JSON.parse(saved);
                // Ripristina solo se recente (ultimi 10 minuti)
                if (Date.now() - state.timestamp < 10 * 60 * 1000) {
                    $('#search').val(state.search || '');
                    $('#categoria').val(state.categoria || '');
                }
            }
        } catch (e) {
            console.warn('⚠️ Impossibile ripristinare stato ricerca:', e);
        }
    };
    
    // === EASTER EGGS E FEEDBACK UTENTE ===
    
    // Konami Code per sviluppatori
    const konamiCode = [38,38,40,40,37,39,37,39,66,65];
    let konamiIndex = 0;
    
    $(document).on('keydown', function(e) {
        if (e.keyCode === konamiCode[konamiIndex]) {
            konamiIndex++;
            if (konamiIndex === konamiCode.length) {
                console.log('🎮 Konami Code attivato!');
                $('body').addClass('konami-mode');
                alert('🎉 Modalità sviluppatore attivata!');
                konamiIndex = 0;
            }
        } else {
            konamiIndex = 0;
        }
    });
    
    // Feedback visivo per azioni
    const showToast = (message, type = 'info') => {
        const toast = $(`
            <div class="toast-message toast-${type} position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; background: var(--bs-${type}); color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                ${message}
            </div>
        `);
        
        $('body').append(toast);
        toast.fadeIn(300).delay(3000).fadeOut(300, function() {
            $(this).remove();
        });
    };
    
    // === FINALIZZAZIONE E LOG ===
    
    // Log statistiche finali
    console.log('📈 Statistiche pagina:', {
        prodotti_mostrati: $('.product-card').length,
        totale_prodotti: {{ $prodotti->total() }},
        pagina_corrente: {{ $prodotti->currentPage() }},
        ricerca_attiva: Boolean('{{ request("search") }}'),
        categoria_filtrata: '{{ request("categoria") }}' || null,
        viewport_width: $(window).width(),
        user_agent: navigator.userAgent.split(' ').pop()
    });
    
    // Segna caricamento completo
    $('body').addClass('catalogo-ready');
    console.log('✅ Catalogo prodotti pubblico caricato completamente');
    
    // Trigger evento personalizzato per altri script
    $(document).trigger('catalogoReady', {
        prodotti: {{ $prodotti->total() }},
        filtri: {
            search: '{{ request("search") }}',
            categoria: '{{ request("categoria") }}'
        }
    });
});

// === CSS AGGIUNTIVO PER ANIMAZIONI ===
const additionalCSS = `
    <style>
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .focused {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25) !important;
    }
    
    .konami-mode .product-card {
        animation: rainbow 2s ease-in-out infinite alternate;
    }
    
    @keyframes rainbow {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }
    
    .toast-message {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Smooth transitions per tutti gli elementi interattivi */
    .btn, .card, .badge, .form-control, .form-select {
        transition: all 0.2s ease-in-out;
    }
    
    /* Stati di caricamento */
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #ccc;
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }
    </style>
`;

// Inietta CSS aggiuntivo
$('head').append(additionalCSS);
</script>
@endpush