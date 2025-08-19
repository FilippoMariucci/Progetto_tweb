{{-- Vista per catalogo completo tecnici (con malfunzionamenti) --}}
@extends('layouts.app')

@section('title', 'Catalogo Completo - Tecnici')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER CATALOGO TECNICO === -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="bi bi-tools text-warning me-2"></i>
                Catalogo Tecnico Completo
                <span class="badge bg-warning text-dark ms-2">Con Malfunzionamenti</span>
            </h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    @if(auth()->user()->isStaff())
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
                    @elseif(auth()->user()->isTecnico())
                        <li class="breadcrumb-item"><a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a></li>
                    @endif
                    <li class="breadcrumb-item active">Catalogo Completo</li>
                </ol>
            </nav>
            
            <!-- Statistiche rapide per tecnici -->
            @if(isset($stats))
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="badge bg-primary fs-6">{{ $stats['total_prodotti'] }} prodotti totali</span>
                    <span class="badge bg-warning fs-6">{{ $stats['con_malfunzionamenti'] }} con problemi</span>
                    <span class="badge bg-danger fs-6">{{ $stats['malfunzionamenti_critici'] }} critici</span>
                    
                    {{-- Statistiche specifiche staff --}}
                    @if(auth()->user()->isStaff() && isset($stats['miei_prodotti']))
                        <span class="badge bg-success fs-6">{{ $stats['miei_prodotti'] }} miei prodotti</span>
                    @endif
                    
                    @if(request('categoria'))
                        <span class="badge bg-secondary fs-6">Categoria: {{ ucfirst(str_replace('_', ' ', request('categoria'))) }}</span>
                    @endif
                    @if(request('search'))
                        <span class="badge bg-info fs-6">Ricerca: "{{ request('search') }}"</span>
                    @endif
                    @if(request('staff_filter') === 'my_products')
                        <span class="badge bg-success fs-6">Solo Prodotti Assegnati</span>
                    @endif
                </div>
            @endif
            
            <!-- Alert per filtro staff -->
            @if(request('staff_filter') === 'my_products')
                <div class="alert alert-success border-start border-success border-4">
                    <i class="bi bi-person-check me-2"></i>
                    Stai visualizzando solo i <strong>tuoi prodotti assegnati</strong>. 
                    <a href="{{ route('prodotti.completo.index') }}" class="alert-link">Visualizza tutti i prodotti</a>
                </div>
            @endif
        </div>
    </div>

    <!-- === BARRA RICERCA E FILTRI AVANZATA === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <form method="GET" action="{{ route('prodotti.completo.index') }}" class="row g-3">
                        
                        <!-- Ricerca Testuale Avanzata -->
                        <div class="col-md-5">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Avanzata Prodotti
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="es: lavatrice, modello, codice, lav* (ricerca parziale)"
                                   autocomplete="off">
                            <div class="form-text">
                                Cerca in nome, modello, descrizione. Usa <code>*</code> per ricerche parziali
                            </div>
                        </div>
                        
                        <!-- Filtro Categoria -->
                        <div class="col-md-3">
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
                        
                        <!-- Filtri Tecnici Speciali -->
                        <div class="col-md-2">
                            <label for="filter" class="form-label fw-semibold">
                                <i class="bi bi-filter me-1"></i>Filtro Tecnico
                            </label>
                            <select name="filter" id="filter" class="form-select">
                                <option value="">Tutti i prodotti</option>
                                <option value="critici" {{ request('filter') === 'critici' ? 'selected' : '' }}>
                                    Solo Critici
                                </option>
                                <option value="problematici" {{ request('filter') === 'problematici' ? 'selected' : '' }}>
                                    Con Problemi
                                </option>
                                <option value="senza_problemi" {{ request('filter') === 'senza_problemi' ? 'selected' : '' }}>
                                    Senza Problemi
                                </option>
                            </select>
                        </div>
                        
                        <!-- Pulsanti Azione -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                @if(request('search') || request('categoria') || request('filter'))
                                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Filtri Hidden per Staff -->
                        @if(request('staff_filter'))
                            <input type="hidden" name="staff_filter" value="{{ request('staff_filter') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- === FILTRI RAPIDI STAFF === -->
    @if(auth()->user()->isStaff())
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('prodotti.completo.index') }}" 
                       class="btn {{ !request('staff_filter') ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="bi bi-grid me-1"></i>Tutti i Prodotti
                    </a>
                    <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                       class="btn {{ request('staff_filter') === 'my_products' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi bi-person-check me-1"></i>I Miei Prodotti
                    </a>
                    <a href="{{ route('prodotti.completo.index') }}?filter=critici" 
                       class="btn {{ request('filter') === 'critici' ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="bi bi-exclamation-triangle me-1"></i>Solo Critici
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- === GRIGLIA PRODOTTI TECNICA === -->
    <div class="row">
        @forelse($prodotti as $prodotto)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card card-custom h-100 {{ $prodotto->hasMalfunzionamentiCritici() ? 'border-danger' : '' }}">
                    
                    <!-- Immagine Prodotto con Indicatori Tecnici -->
                    <div class="position-relative">
                        <img src="{{ $prodotto->foto_url }}" 
                             class="card-img-top" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 200px; object-fit: cover;">
                        
                        <!-- Badge Categoria -->
                        <span class="position-absolute top-0 start-0 m-2 badge bg-secondary">
                            {{ $prodotto->categoria_label }}
                        </span>
                        
                        <!-- Indicatori Tecnici Avanzati -->
                        <div class="position-absolute top-0 end-0 m-2">
                            @if($prodotto->malfunzionamenti_count > 0)
                                <span class="badge bg-warning mb-1 d-block" data-bs-toggle="tooltip" 
                                      title="{{ $prodotto->malfunzionamenti_count }} malfunzionamenti totali">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ $prodotto->malfunzionamenti_count }}
                                </span>
                            @endif
                            
                            @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                <span class="badge bg-danger mb-1 d-block" data-bs-toggle="tooltip" 
                                      title="{{ $prodotto->critici_count }} malfunzionamenti critici">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $prodotto->critici_count }}
                                </span>
                            @endif
                            
                            <!-- Badge Staff Assegnato -->
                            @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                <span class="badge bg-success d-block" data-bs-toggle="tooltip" title="Prodotto assegnato a te">
                                    <i class="bi bi-person-check"></i>
                                </span>
                            @endif
                        </div>
                        
                        <!-- Indicatore Priorità (solo per critici) -->
                        @if($prodotto->hasMalfunzionamentiCritici())
                            <div class="position-absolute bottom-0 start-0 end-0 bg-danger bg-opacity-75 text-white text-center py-1">
                                <small><i class="bi bi-exclamation-triangle me-1"></i><strong>PRIORITÀ ALTA</strong></small>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Contenuto Card Tecnica -->
                    <div class="card-body d-flex flex-column">
                        
                        <!-- Nome e Modello -->
                        <h5 class="card-title">{{ $prodotto->nome }}</h5>
                        <p class="text-muted mb-2">
                            <small><strong>Modello:</strong> <code>{{ $prodotto->modello }}</code></small>
                        </p>
                        
                        <!-- Descrizione -->
                        <p class="card-text">
                            {{ Str::limit($prodotto->descrizione, 80) }}
                        </p>
                        
                        <!-- Informazioni Tecniche Specifiche -->
                        <div class="mb-3">
                            <div class="row g-2 text-center small">
                                <!-- Malfunzionamenti -->
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded">
                                        <strong class="text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                            {{ $prodotto->malfunzionamenti_count ?? 0 }}
                                        </strong>
                                        <br><small class="text-muted">Problemi</small>
                                    </div>
                                </div>
                                <!-- Critici -->
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded">
                                        <strong class="text-{{ isset($prodotto->critici_count) && $prodotto->critici_count > 0 ? 'danger' : 'success' }}">
                                            {{ $prodotto->critici_count ?? 0 }}
                                        </strong>
                                        <br><small class="text-muted">Critici</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Staff Assegnato (per admin e dettagli) -->
                        @if($prodotto->staffAssegnato)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </p>
                        @elseif(auth()->user()->isAdmin())
                            <p class="text-warning small mb-2">
                                <i class="bi bi-person-x me-1"></i>
                                Nessun staff assegnato
                            </p>
                        @endif
                        
                        <!-- Alert per Problemi Critici -->
                        @if($prodotto->hasMalfunzionamentiCritici())
                            <div class="alert alert-danger py-2 mb-3">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>ATTENZIONE:</strong> Problemi critici rilevati
                                </small>
                            </div>
                        @endif
                        
                        <!-- Pulsanti Azione Tecnici -->
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <!-- Visualizza Dettagli Completi -->
                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>Dettagli Completi
                                </a>
                                
                                <!-- Malfunzionamenti (solo se presenti) -->
                                @if($prodotto->malfunzionamenti_count > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                       class="btn btn-{{ $prodotto->hasMalfunzionamentiCritici() ? 'danger' : 'warning' }} btn-sm">
                                        <i class="bi bi-tools me-1"></i>
                                        Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                                    </a>
                                @endif
                                
                                <!-- Azioni Staff -->
                                @if(auth()->user()->isStaff())
                                    @if($prodotto->staff_assegnato_id === auth()->id())
                                        <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $prodotto->id]) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Soluzione
                                        </a>
                                    @endif
                                @endif
                                
                                <!-- Azioni Admin -->
                                @if(auth()->user()->isAdmin())
                                    <div class="btn-group btn-group-sm w-100" role="group">
                                        <a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-info" data-bs-toggle="modal" 
                                                data-bs-target="#assignModal{{ $prodotto->id }}">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                        <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100" 
                                                    data-confirm-delete="Rimuovere {{ $prodotto->nome }}?">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
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
                        <i class="bi bi-tools display-1 text-muted mb-3"></i>
                        <h3>Nessun prodotto trovato</h3>
                        @if(request('search') || request('categoria') || request('filter') || request('staff_filter'))
                            <p class="text-muted mb-4">
                                @if(request('staff_filter') === 'my_products')
                                    Non hai prodotti assegnati che corrispondono ai filtri applicati.
                                    <br>
                                    <a href="{{ route('prodotti.completo.index') }}" class="alert-link">Visualizza tutti i prodotti</a>
                                @else
                                    Prova a modificare i filtri di ricerca o 
                                    <a href="{{ route('prodotti.completo.index') }}">visualizza tutti i prodotti</a>
                                @endif
                            </p>
                        @else
                            <p class="text-muted mb-4">
                                Il catalogo tecnico è momentaneamente vuoto.
                            </p>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Prodotto
                            </a>
                        @endif
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
                        @if(request('staff_filter') === 'my_products')
                            assegnati
                        @endif
                    </small>
                </div>
            </div>
        </div>
    @endif

    <!-- === PULSANTI AZIONE FLOTTANTI === -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        @if(auth()->user()->isStaff())
            <!-- Pulsante Nuova Soluzione (Staff) -->
            @if($prodotti->count() > 0)
                <button class="btn btn-warning btn-lg rounded-circle shadow me-2" 
                        data-bs-toggle="modal" data-bs-target="#quickSolutionModal"
                        title="Aggiungi Soluzione Rapida">
                    <i class="bi bi-plus-circle fs-3"></i>
                </button>
            @endif
        @endif
        
        @if(auth()->user()->isAdmin())
            <!-- Pulsante Aggiungi Prodotto (Admin) -->
            <a href="{{ route('admin.prodotti.create') }}" 
               class="btn btn-success btn-lg rounded-circle shadow" 
               data-bs-toggle="tooltip" 
               title="Aggiungi Nuovo Prodotto">
                <i class="bi bi-plus fs-3"></i>
            </a>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
/* Stili specifici per catalogo tecnico */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

/* Badge e indicatori */
.badge {
    font-size: 0.75rem;
}

/* Card con bordo critico */
.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

/* Indicatori priorità */
.bg-opacity-75 {
    background-color: rgba(var(--bs-danger-rgb), 0.75) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Highlights per ricerca */
mark {
    background-color: #fff3cd;
    padding: 0 2px;
    border-radius: 2px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === INIZIALIZZAZIONE CATALOGO TECNICO ===
    console.log('Catalogo tecnico inizializzato');
    console.log('Prodotti caricati:', {{ $prodotti->count() }});
    console.log('Utente livello:', {{ auth()->user()->livello_accesso }});
    
    // === RICERCA DINAMICA MIGLIORATA ===
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
    
    // Suggerimenti prodotti tecnici
    function suggestProducts(query) {
        $.get('{{ route("api.prodotti.search.tech") }}', { q: query })
            .done(function(response) {
                if (response.success && response.data.length > 0) {
                    showTechSuggestions(response.data);
                } else {
                    hideSuggestions();
                }
            })
            .fail(() => hideSuggestions());
    }
    
    function showTechSuggestions(suggestions) {
        let html = '<div class="list-group position-absolute w-100" style="z-index: 1000; top: 100%;">';
        
        suggestions.forEach(function(item) {
            const criticiLabel = item.critici_count > 0 ? 
                `<span class="badge bg-danger ms-1">${item.critici_count} critici</span>` : '';
            
            html += `
                <a href="${item.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="${item.foto_url}" class="me-2 rounded" style="width: 32px; height: 32px; object-fit: cover;">
                            <div>
                                <strong>${item.nome}</strong>
                                <small class="d-block text-muted">${item.modello} - ${item.categoria}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning">${item.malfunzionamenti_count} problemi</span>
                            ${criticiLabel}
                            ${item.staff_assegnato ? '<i class="bi bi-person-check text-success ms-1"></i>' : ''}
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
    
    // === FILTRI AVANZATI ===
    
    // Auto-submit filtri per tecnici
    $('#categoria, #filter').on('change', function() {
        // Auto-submit se necessario (commentato per ora)
        // $(this).closest('form').submit();
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
    
    // === TOOLTIP E UI ===
    
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === EVIDENZIAZIONE RICERCA ===
    
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
    
    // === ANALYTICS TECNICO ===
    
    @if(request('search'))
        console.log('Ricerca tecnica effettuata:', {
            termine: '{{ request("search") }}',
            categoria: '{{ request("categoria") }}',
            filter: '{{ request("filter") }}',
            staff_filter: '{{ request("staff_filter") }}',
            risultati: {{ $prodotti->total() }},
            user_level: {{ auth()->user()->livello_accesso }},
            timestamp: new Date().toISOString()
        });
    @endif
    
    // Statistiche per dashboard
    @if(auth()->user()->isStaff())
        const staffStats = {
            prodotti_totali: {{ $stats['total_prodotti'] ?? 0 }},
            con_malfunzionamenti: {{ $stats['con_malfunzionamenti'] ?? 0 }},
            critici: {{ $stats['malfunzionamenti_critici'] ?? 0 }},
            @if(isset($stats['miei_prodotti']))
                miei_prodotti: {{ $stats['miei_prodotti'] }},
            @endif
        };
        console.log('Statistiche staff:', staffStats);
    @endif
});
</script>
@endpush