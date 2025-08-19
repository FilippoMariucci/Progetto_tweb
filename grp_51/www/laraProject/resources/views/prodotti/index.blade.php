@extends('layouts.app')

@section('title', 'Catalogo Prodotti')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER CATALOGO === -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="bi bi-box text-primary me-2"></i>
                Catalogo Prodotti
                @auth
                    @if(Auth::user()->canViewMalfunzionamenti())
                        <span class="badge bg-info ms-2">Versione Tecnica</span>
                    @endif
                @endauth
            </h1>
            
            <!-- Statistiche rapide -->
            @if(isset($stats))
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="badge bg-primary fs-6">{{ $stats['total_prodotti'] }} prodotti totali</span>
                    @if(request('categoria'))
                        <span class="badge bg-secondary fs-6">Categoria: {{ ucfirst(str_replace('_', ' ', request('categoria'))) }}</span>
                    @endif
                    @if(request('search'))
                        <span class="badge bg-warning fs-6">Ricerca: "{{ request('search') }}"</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- === BARRA RICERCA E FILTRI === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <form method="GET" action="{{ route('prodotti.index') }}" class="row g-3">
                        
                        <!-- Ricerca Testuale -->
                        <div class="col-md-6">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="es: lavatrice, lav* (usa * per ricerca parziale)"
                                   autocomplete="off">
                            <div class="form-text">
                                Usa il carattere <code>*</code> alla fine per ricerche parziali (es: "lav*" trova lavatrici, lavastoviglie, ecc.)
                            </div>
                        </div>
                        
                        <!-- Filtro Categoria -->
                        <div class="col-md-4">
                            <label for="categoria" class="form-label fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Categoria
                            </label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="">Tutte le categorie</option>
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Pulsanti Azione -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                @if(request('search') || request('categoria'))
                                    <a href="{{ route('prodotti.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mantieni parametri nascosti -->
                        @if(request('view'))
                            <input type="hidden" name="view" value="{{ request('view') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- === GRIGLIA PRODOTTI === -->
    <div class="row">
        @forelse($prodotti as $prodotto)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card card-custom h-100">
                    
                    <!-- Immagine Prodotto -->
                    <div class="position-relative">
                        <img src="{{ $prodotto->foto_url }}" 
                             class="card-img-top" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 200px; object-fit: cover;">
                        
                        <!-- Badge Categoria -->
                        <span class="position-absolute top-0 start-0 m-2 badge bg-secondary">
                            {{ $prodotto->categoria_label }}
                        </span>
                        
                        <!-- Indicatori per Tecnici -->
                        @auth
                            @if(Auth::user()->canViewMalfunzionamenti())
                                <div class="position-absolute top-0 end-0 m-2">
                                    @if($prodotto->totale_malfunzionamenti > 0)
                                        <span class="badge bg-warning" data-bs-toggle="tooltip" 
                                              title="{{ $prodotto->totale_malfunzionamenti }} malfunzionamenti">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            {{ $prodotto->totale_malfunzionamenti }}
                                        </span>
                                    @endif
                                    
                                    @if($prodotto->hasMalfunzionamentiCritici())
                                        <span class="badge bg-danger ms-1" data-bs-toggle="tooltip" title="Ha malfunzionamenti critici">
                                            <i class="bi bi-exclamation-circle"></i>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>
                    
                    <!-- Contenuto Card -->
                    <div class="card-body d-flex flex-column">
                        
                        <!-- Nome e Modello -->
                        <h5 class="card-title">{{ $prodotto->nome }}</h5>
                        <p class="text-muted mb-2">
                            <small><strong>Modello:</strong> {{ $prodotto->modello }}</small>
                        </p>
                        
                        <!-- Descrizione -->
                        <p class="card-text">
                            {{ Str::limit($prodotto->descrizione, 100) }}
                        </p>
                        
                        <!-- Prezzo (se presente) -->
                        @if($prodotto->prezzo)
                            <p class="h5 text-primary mb-3">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </p>
                        @endif
                        
                        <!-- Staff Assegnato (per admin) -->
                        @auth
                            @if(Auth::user()->isAdmin() && $prodotto->staffAssegnato)
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Gestito da: {{ $prodotto->staffAssegnato->nome_completo }}
                                </p>
                            @endif
                        @endauth
                        
                        <!-- Statistiche per Tecnici -->
                        @auth
                            @if(Auth::user()->canViewMalfunzionamenti() && $prodotto->totale_segnalazioni > 0)
                                <div class="alert alert-warning py-2 mb-3">
                                    <small>
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $prodotto->totale_segnalazioni }} segnalazioni totali
                                    </small>
                                </div>
                            @endif
                        @endauth
                        
                        <!-- Pulsanti Azione -->
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>Visualizza Dettagli
                                </a>
                                
                                @auth
                                    @if(Auth::user()->canViewMalfunzionamenti() && $prodotto->totale_malfunzionamenti > 0)
                                        <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-tools me-1"></i>Malfunzionamenti ({{ $prodotto->totale_malfunzionamenti }})
                                        </a>
                                    @endif
                                    
                                    @can('manageProdotti')
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            <a href="{{ route('prodotti.edit', $prodotto) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('prodotti.destroy', $prodotto) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger w-100" 
                                                        data-confirm-delete="Sei sicuro di voler rimuovere {{ $prodotto->nome }}?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Nessun Prodotto Trovato -->
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box display-1 text-muted mb-3"></i>
                        <h3>Nessun prodotto trovato</h3>
                        @if(request('search') || request('categoria'))
                            <p class="text-muted mb-4">
                                Prova a modificare i filtri di ricerca o 
                                <a href="{{ route('prodotti.index') }}">visualizza tutti i prodotti</a>
                            </p>
                        @else
                            <p class="text-muted mb-4">
                                Il catalogo è momentaneamente vuoto.
                            </p>
                        @endif
                        
                        @can('manageProdotti')
                            <a href="{{ route('prodotti.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Prodotto
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- === PAGINAZIONE === -->
    @if($prodotti->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $prodotti->appends(request()->query())->links() }}
                </div>
                
                <!-- Info Paginazione -->
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
            </div>
        </div>
    @endif

    <!-- === PULSANTE AGGIUNGI (Admin) === -->
    @can('manageProdotti')
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
            <a href="{{ route('prodotti.create') }}" 
               class="btn btn-success btn-lg rounded-circle shadow" 
               data-bs-toggle="tooltip" 
               title="Aggiungi Nuovo Prodotto">
                <i class="bi bi-plus fs-3"></i>
            </a>
        </div>
    @endcan

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // === RICERCA DINAMICA CON DEBOUNCE ===
    let searchTimeout;
    
    $('#search').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                suggestProducts(query);
            }, 300);
        } else {
            hideSuggestions();
        }
    });
    
    // Suggerimenti prodotti
    function suggestProducts(query) {
        $.get('{{ route("api.prodotti.search") }}', { q: query })
            .done(function(response) {
                if (response.success && response.data.length > 0) {
                    showSuggestions(response.data);
                } else {
                    hideSuggestions();
                }
            })
            .fail(() => hideSuggestions());
    }
    
    function showSuggestions(suggestions) {
        let html = '<div class="list-group position-absolute w-100" style="z-index: 1000; top: 100%;">';
        
        suggestions.forEach(function(item) {
            html += `
                <a href="${item.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center">
                        <img src="${item.foto_url}" class="me-2 rounded" style="width: 32px; height: 32px; object-fit: cover;">
                        <div>
                            <strong>${item.nome}</strong>
                            <small class="d-block text-muted">${item.modello} - ${item.categoria}</small>
                        </div>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        
        $('#search').parent().addClass('position-relative').find('.list-group').remove().end().append(html);
    }
    
    function hideSuggestions() {
        $('.list-group').remove();
    }
    
    // Nascondi suggerimenti quando si clicca fuori
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search').length) {
            hideSuggestions();
        }
    });
    
    // === FILTRI SMART ===
    
    // Aggiorna automaticamente quando cambia categoria
    $('#categoria').on('change', function() {
        if ($(this).val()) {
            // Auto-submit se c'è una categoria selezionata
            // $(this).closest('form').submit();
        }
    });
    
    // === CONFERME ELIMINAZIONE ===
    
    $('[data-confirm-delete]').on('click', function(e) {
        e.preventDefault();
        
        const message = $(this).data('confirm-delete');
        const form = $(this).closest('form');
        
        if (confirm(message)) {
            form.submit();
        }
    });
    
    // === TOOLTIP ===
    
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === EVIDENZIAZIONE RICERCA ===
    
    // Evidenzia i termini di ricerca nei risultati
    const searchTerm = '{{ request("search") }}';
    if (searchTerm && !searchTerm.includes('*')) {
        highlightSearchTerms(searchTerm);
    }
    
    function highlightSearchTerms(term) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${term})`, 'gi');
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }
    
    // === LAZY LOADING IMMAGINI ===
    
    // Implementa lazy loading per le immagini
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // === STATISTICHE RICERCA ===
    
    // Log delle ricerche per analytics (solo se necessario)
    @if(request('search'))
        console.log('Ricerca effettuata:', {
            termine: '{{ request("search") }}',
            categoria: '{{ request("categoria") }}',
            risultati: {{ $prodotti->total() }},
            timestamp: new Date().toISOString()
        });
    @endif
    
    console.log('Catalogo prodotti inizializzato');
    console.log('Prodotti caricati:', {{ $prodotti->count() }});
});
</script>
@endpush