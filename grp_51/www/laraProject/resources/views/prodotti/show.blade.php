@extends('layouts.app')

@section('title', $prodotto->nome)

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.index') }}">Catalogo</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.categoria', $prodotto->categoria) }}">{{ $prodotto->categoria_label }}</a></li>
            <li class="breadcrumb-item active">{{ $prodotto->nome }}</li>
        </ol>
    </nav>

    <div class="row">
        
        <!-- === COLONNA PRINCIPALE === -->
        <div class="col-lg-8">
            
            <!-- Card Prodotto Principale -->
            <div class="card card-custom mb-4">
                <div class="row g-0">
                    
                    <!-- Immagine Prodotto -->
                    <div class="col-md-5">
                        <img src="{{ $prodotto->foto_url }}" 
                             class="img-fluid rounded-start h-100" 
                             alt="{{ $prodotto->nome }}"
                             style="object-fit: cover; min-height: 300px;">
                    </div>
                    
                    <!-- Informazioni Principali -->
                    <div class="col-md-7">
                        <div class="card-body">
                            
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h2 card-title mb-1">{{ $prodotto->nome }}</h1>
                                    <p class="text-muted mb-0">
                                        <strong>Modello:</strong> {{ $prodotto->modello }}
                                    </p>
                                </div>
                                
                                <!-- Badge e Stato -->
                                <div class="text-end">
                                    <span class="badge bg-primary mb-2">{{ $prodotto->categoria_label }}</span>
                                    @if($prodotto->attivo)
                                        <span class="badge bg-success d-block">Disponibile</span>
                                    @else
                                        <span class="badge bg-secondary d-block">Non Disponibile</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Descrizione -->
                            <div class="mb-3">
                                <h5>Descrizione</h5>
                                <p class="card-text">{{ $prodotto->descrizione }}</p>
                            </div>
                            
                            <!-- Prezzo -->
                            @if($prodotto->prezzo)
                                <div class="mb-3">
                                    <h3 class="text-primary">
                                        €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                    </h3>
                                </div>
                            @endif
                            
                            <!-- Staff Assegnato (Admin) -->
                            @auth
                                @if(Auth::user()->isAdmin() && $prodotto->staffAssegnato)
                                    <div class="alert alert-info py-2 mb-3">
                                        <small>
                                            <i class="bi bi-person-badge me-1"></i>
                                            <strong>Gestito da:</strong> {{ $prodotto->staffAssegnato->nome_completo }}
                                        </small>
                                    </div>
                                @endif
                            @endauth
                            
                            <!-- Statistiche Malfunzionamenti (Tecnici) -->
                            @if($showMalfunzionamenti && $prodotto->totale_malfunzionamenti > 0)
                                <div class="alert border-start border-warning border-4 bg-warning bg-opacity-10">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                        Informazioni Tecniche
                                    </h6>
                                    <p class="mb-2">
                                        <strong>{{ $prodotto->totale_malfunzionamenti }}</strong> malfunzionamenti registrati<br>
                                        <strong>{{ $prodotto->totale_segnalazioni }}</strong> segnalazioni totali
                                    </p>
                                    @if($prodotto->hasMalfunzionamentiCritici())
                                        <div class="alert alert-danger py-2 mb-0">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            <strong>Attenzione:</strong> Questo prodotto ha malfunzionamenti critici
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Pulsanti Azione -->
                            <div class="d-grid gap-2">
                                @if($showMalfunzionamenti && $prodotto->totale_malfunzionamenti > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-warning">
                                        <i class="bi bi-tools me-2"></i>
                                        Visualizza Malfunzionamenti ({{ $prodotto->totale_malfunzionamenti }})
                                    </a>
                                @endif
                                
                                @can('manageProdotti')
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('prodotti.edit', $prodotto) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil me-1"></i>Modifica
                                        </a>
                                        <form action="{{ route('prodotti.destroy', $prodotto) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    data-confirm-delete="Sei sicuro di voler rimuovere {{ $prodotto->nome }}?">
                                                <i class="bi bi-trash me-1"></i>Elimina
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- === SCHEDA TECNICA === -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        Scheda Tecnica
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Note Tecniche -->
                    <div class="mb-4">
                        <h6><i class="bi bi-gear me-2"></i>Specifiche Tecniche</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $prodotto->note_tecniche }}
                        </div>
                    </div>
                    
                    <!-- Modalità di Installazione -->
                    <div class="mb-4">
                        <h6><i class="bi bi-tools me-2"></i>Installazione</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_installazione)) !!}
                        </div>
                    </div>
                    
                    <!-- Modalità d'Uso (se presente) -->
                    @if($prodotto->modalita_uso)
                        <div class="mb-0">
                            <h6><i class="bi bi-book me-2"></i>Modalità d'Uso</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($prodotto->modalita_uso)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- === MALFUNZIONAMENTI (Solo per Tecnici) === -->
            @if($showMalfunzionamenti)
                <div class="card card-custom mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            Malfunzionamenti Registrati
                        </h5>
                        
                        @can('manageMalfunzionamenti')
                            <a href="{{ route('malfunzionamenti.create', $prodotto) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @if($prodotto->malfunzionamenti->count() > 0)
                            
                            <!-- Lista Malfunzionamenti -->
                            <div class="row g-3">
                                @foreach($prodotto->malfunzionamenti_ordered->take(6) as $malfunzionamento)
                                    <div class="col-md-6">
                                        <div class="card border-start border-{{ $malfunzionamento->gravita === 'critica' ? 'danger' : ($malfunzionamento->gravita === 'alta' ? 'warning' : 'info') }} border-3">
                                            <div class="card-body py-3">
                                                
                                                <!-- Header Malfunzionamento -->
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $malfunzionamento->titolo }}</h6>
                                                    <span class="badge bg-{{ $malfunzionamento->gravita === 'critica' ? 'danger' : ($malfunzionamento->gravita === 'alta' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Descrizione Breve -->
                                                <p class="card-text small text-muted mb-2">
                                                    {{ Str::limit($malfunzionamento->descrizione, 80) }}
                                                </p>
                                                
                                                <!-- Info Aggiuntive -->
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }} segnalazioni
                                                    </small>
                                                    <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Dettagli
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Link per Vedere Tutti -->
                            @if($prodotto->malfunzionamenti->count() > 6)
                                <div class="text-center mt-3">
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-list me-1"></i>
                                        Visualizza Tutti i Malfunzionamenti ({{ $prodotto->malfunzionamenti->count() }})
                                    </a>
                                </div>
                            @endif
                            
                        @else
                            <!-- Nessun Malfunzionamento -->
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                                <h5>Nessun Malfunzionamento Registrato</h5>
                                <p class="text-muted">Questo prodotto non ha malfunzionamenti noti al momento.</p>
                                
                                @can('manageMalfunzionamenti')
                                    <a href="{{ route('malfunzionamenti.create', $prodotto) }}" class="btn btn-warning">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Malfunzionamento
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <!-- === SIDEBAR === -->
        <div class="col-lg-4">
            
            <!-- Informazioni Rapide -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Informazioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Categoria:</dt>
                        <dd class="col-sm-7">{{ $prodotto->categoria_label }}</dd>
                        
                        <dt class="col-sm-5">Modello:</dt>
                        <dd class="col-sm-7"><code>{{ $prodotto->modello }}</code></dd>
                        
                        @if($prodotto->prezzo)
                            <dt class="col-sm-5">Prezzo:</dt>
                            <dd class="col-sm-7 text-primary fw-bold">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</dd>
                        @endif
                        
                        <dt class="col-sm-5">Stato:</dt>
                        <dd class="col-sm-7">
                            @if($prodotto->attivo)
                                <span class="badge bg-success">Attivo</span>
                            @else
                                <span class="badge bg-secondary">Inattivo</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-5">Aggiunto:</dt>
                        <dd class="col-sm-7">{{ $prodotto->created_at->format('d/m/Y') }}</dd>
                        
                        @if($showMalfunzionamenti)
                            <dt class="col-sm-5">Problemi:</dt>
                            <dd class="col-sm-7">
                                @if($prodotto->totale_malfunzionamenti > 0)
                                    <span class="badge bg-warning">{{ $prodotto->totale_malfunzionamenti }}</span>
                                @else
                                    <span class="badge bg-success">0</span>
                                @endif
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Azioni Rapide -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        <!-- Torna al Catalogo -->
                        <a href="{{ route('prodotti.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Torna al Catalogo
                        </a>
                        
                        <!-- Vedi Categoria -->
                        <a href="{{ route('prodotti.categoria', $prodotto->categoria) }}" class="btn btn-outline-primary">
                            <i class="bi bi-grid me-1"></i>Altri {{ $prodotto->categoria_label }}
                        </a>
                        
                        <!-- Trova Centro Assistenza -->
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-geo-alt me-1"></i>Trova Centro Assistenza
                        </a>
                        
                        @auth
                            @if($showMalfunzionamenti)
                                <!-- Ricerca Problemi -->
                                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#searchModal">
                                    <i class="bi bi-search me-1"></i>Cerca Problema Specifico
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Prodotti Correlati -->
            @php
                $prodotti_correlati = \App\Models\Prodotto::where('categoria', $prodotto->categoria)
                    ->where('id', '!=', $prodotto->id)
                    ->where('attivo', true)
                    ->limit(3)
                    ->get();
            @endphp
            
            @if($prodotti_correlati->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-box text-primary me-2"></i>
                            Prodotti Correlati
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($prodotti_correlati as $correlato)
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $correlato->foto_url }}" 
                                     class="me-3 rounded" 
                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                     alt="{{ $correlato->nome }}">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('prodotti.show', $correlato) }}" class="text-decoration-none">
                                            {{ $correlato->nome }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $correlato->modello }}</small>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="d-grid">
                            <a href="{{ route('prodotti.categoria', $prodotto->categoria) }}" class="btn btn-sm btn-outline-primary">
                                Vedi Tutti
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Help e Supporto -->
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle text-info me-2"></i>
                        Bisogno di Aiuto?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Non trovi quello che cerchi? Contatta il nostro supporto tecnico.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('centri.index') }}" class="btn btn-sm btn-info">
                            <i class="bi bi-telephone me-1"></i>Contatta Assistenza
                        </a>
                        <a href="{{ route('contatti') }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-envelope me-1"></i>Scrivi un Messaggio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL RICERCA PROBLEMI (Solo Tecnici) === -->
@auth
    @if($showMalfunzionamenti)
        <div class="modal fade" id="searchModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-search me-2"></i>
                            Cerca Problema Specifico
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="problemSearch" class="form-label">Descrivi il problema:</label>
                            <input type="text" class="form-control" id="problemSearch" 
                                   placeholder="es: non centrifuga, rumore, perdita acqua...">
                        </div>
                        <div id="searchResults"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // === CONFERMA ELIMINAZIONE ===
    $('[data-confirm-delete]').on('click', function(e) {
        e.preventDefault();
        
        const message = $(this).data('confirm-delete');
        const form = $(this).closest('form');
        
        if (confirm(message)) {
            form.submit();
        }
    });
    
    // === RICERCA PROBLEMI (Modal) ===
    @auth
        @if($showMalfunzionamenti)
            let searchTimeout;
            
            $('#problemSearch').on('input', function() {
                const query = $(this).val().trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchProblems(query);
                    }, 300);
                } else {
                    $('#searchResults').empty();
                }
            });
            
            function searchProblems(query) {
                $('#searchResults').html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Ricerca in corso...</div>');
                
                $.get('{{ route("api.malfunzionamenti.search", $prodotto) }}', { q: query })
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            let html = '<div class="list-group">';
                            
                            response.data.forEach(function(item) {
                                html += `
                                    <a href="${item.url}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">${item.titolo}</h6>
                                            <span class="badge bg-${item.gravita === 'critica' ? 'danger' : (item.gravita === 'alta' ? 'warning' : 'info')}">${item.gravita}</span>
                                        </div>
                                        <small class="text-muted">${item.segnalazioni} segnalazioni - Difficoltà: ${item.difficolta}</small>
                                    </a>
                                `;
                            });
                            
                            html += '</div>';
                            $('#searchResults').html(html);
                        } else {
                            $('#searchResults').html('<div class="alert alert-info mb-0">Nessun problema trovato per questa ricerca.</div>');
                        }
                    })
                    .fail(function() {
                        $('#searchResults').html('<div class="alert alert-danger mb-0">Errore durante la ricerca.</div>');
                    });
            }
        @endif
    @endauth
    
    // === LAZY LOADING IMMAGINI ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // === ANALYTICS VISUALIZZAZIONE ===
    console.log('Prodotto visualizzato:', {
        id: {{ $prodotto->id }},
        nome: '{{ $prodotto->nome }}',
        categoria: '{{ $prodotto->categoria }}',
        @if($showMalfunzionamenti)
            malfunzionamenti: {{ $prodotto->totale_malfunzionamenti }},
        @endif
        timestamp: new Date().toISOString()
    });
    
    console.log('Dettaglio prodotto inizializzato');
});
</script>
@endpush