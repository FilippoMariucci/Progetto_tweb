{{-- Dashboard specifica per lo staff aziendale --}}
@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="container mt-4">
    
    {{-- === DEBUG TEMPORANEO (rimuovi in produzione) === --}}
    @if(config('app.debug'))
        <div class="alert alert-info">
            <strong>🐛 DEBUG INFO:</strong>
            <ul>
                <li>Prodotti Lista: {{ isset($stats['prodotti_lista']) ? $stats['prodotti_lista']->count() : 'non definito' }}</li>
                <li>Stats Keys: {{ isset($stats) ? implode(', ', array_keys($stats)) : 'stats non definito' }}</li>
                <li>User: {{ auth()->user()->nome_completo ?? 'N/A' }}</li>
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per lo staff --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-person-badge text-warning me-2"></i>
                Dashboard Staff Aziendale
            </h1>
            
            {{-- Benvenuto personalizzato per staff --}}
            <div class="alert alert-warning border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-badge display-6 text-warning me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">
                            Benvenuto, {{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Staff' }}!
                        </h4>
                        <p class="mb-0">
                            <span class="badge bg-warning text-dark">Staff Tecnico Aziendale</span>
                        </p>
                        <small class="text-muted">
                            Gestisci malfunzionamenti e soluzioni tecniche per i prodotti assegnati
                        </small>
                    </div>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard Staff</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- PHP: Calcola il primo prodotto disponibile --}}
    @php
        $firstProdotto = isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0 
                         ? $stats['prodotti_lista']->first() 
                         : null;
    @endphp

    <div class="row g-4">
        
        {{-- === ACCESSI RAPIDI STAFF === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Strumenti Staff
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Gestione malfunzionamenti --}}
                        <div class="col-md-6">
                            <a href="{{ route('staff.malfunzionamenti.index') }}" 
                               class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-exclamation-triangle-fill display-6 mb-2"></i>
                                <span class="fw-semibold">Gestisci Soluzioni</span>
                            </a>
                        </div>
                        
                        {{-- CORRETTO: Usa rotta catalogo completo per staff --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                               class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-box-seam display-6 mb-2"></i>
                                <span class="fw-semibold">Miei Prodotti</span>
                            </a>
                        </div>

                        {{-- Nuova Soluzione --}}
                        <div class="col-md-6">
                            @if($firstProdotto)
                                <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $firstProdotto->id]) }}" 
                                   class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-plus-circle display-6 mb-2"></i>
                                    <span class="fw-semibold">Nuova Soluzione</span>
                                </a>
                            @else
                                <button class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center" disabled>
                                    <i class="bi bi-plus-circle display-6 mb-2"></i>
                                    <span class="fw-semibold">Nessun prodotto disponibile</span>
                                </button>
                            @endif
                        </div>
                        
                        {{-- Visualizza catalogo completo --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}" 
                               class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-search display-6 mb-2"></i>
                                <span class="fw-semibold">Esplora Catalogo</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE STAFF === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Le Tue Statistiche
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            {{-- Prodotti assegnati --}}
                            @if(isset($stats['prodotti_assegnati']))
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['prodotti_assegnati'] }}</h4>
                                        <small class="text-muted">Prodotti Tuoi</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Soluzioni create --}}
                            @if(isset($stats['soluzioni_create']))
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <i class="bi bi-check-circle text-success fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['soluzioni_create'] }}</h4>
                                        <small class="text-muted">Soluzioni</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Soluzioni critiche --}}
                            @if(isset($stats['soluzioni_critiche']))
                                <div class="col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['soluzioni_critiche'] }}</h4>
                                        <small class="text-muted">Critiche</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Ultima modifica --}}
                            @if(isset($stats['ultima_modifica']))
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <i class="bi bi-clock text-warning fs-1"></i>
                                        <h6 class="mt-2 mb-1 small">{{ $stats['ultima_modifica'] }}</h6>
                                        <small class="text-muted">Ultima Modifica</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted text-center">Statistiche in caricamento...</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI ASSEGNATI === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        I Tuoi Prodotti Assegnati
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['prodotti_lista']->take(5) as $prodotto)
                                <div class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $prodotto->nome }}</h6>
                                            <small class="text-muted">{{ $prodotto->codice ?? $prodotto->modello ?? 'N/A' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">
                                                {{ $prodotto->malfunzionamenti->count() ?? 0 }} soluzioni
                                            </span>
                                            <br>
                                            {{-- CORRETTO: Usa rotta catalogo completo per visualizzare prodotto --}}
                                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                               class="btn btn-outline-primary btn-sm mt-1">
                                                <i class="bi bi-eye"></i> Vedi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($stats['prodotti_lista']->count() > 5)
                            <div class="text-center mt-3">
                                {{-- CORRETTO: Link al catalogo completo con filtro per prodotti assegnati --}}
                                <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                                   class="btn btn-outline-primary">
                                    Vedi tutti i {{ $stats['prodotti_lista']->count() }} prodotti
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-2">Nessun prodotto assegnato</p>
                            <small class="text-muted">Contatta l'amministratore per ottenere l'assegnazione di prodotti</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- === ULTIME SOLUZIONI CREATE === --}}
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        Ultime Soluzioni Create
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats['ultime_soluzioni']) && $stats['ultime_soluzioni']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['ultime_soluzioni']->take(5) as $soluzione)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">
                                                {{ Str::limit($soluzione->titolo, 40) }}
                                            </h6>
                                            <p class="mb-1 small">
                                                <strong>Prodotto:</strong> {{ $soluzione->prodotto->nome ?? 'N/A' }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $soluzione->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $soluzione->gravita == 'critica' ? 'danger' : ($soluzione->gravita == 'alta' ? 'warning' : 'success') }}">
                                                {{ ucfirst($soluzione->gravita ?? 'normale') }}
                                            </span>
                                            {{-- CORRETTO: Link alla soluzione usando rotta staff --}}
                                            @if($soluzione->prodotto)
                                                <br>
                                                <a href="{{ route('staff.malfunzionamenti.show', [$soluzione->prodotto, $soluzione]) }}" class="btn btn-outline-primary btn-sm mt-1">
    <i class="bi bi-eye"></i> Vedi
</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-outline-success">
                                Vedi tutte le soluzioni
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-plus-circle display-1 text-muted"></i>
                            <p class="text-muted mt-2">Nessuna soluzione creata ancora</p>
                            @if($firstProdotto)
                                <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $firstProdotto->id]) }}" 
                                   class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>Crea la prima soluzione
                                </a>
                            @else
                                <p class="text-muted small">Nessun prodotto assegnato per creare soluzioni</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                        
                        {{-- Nuova Soluzione --}}
                        @if($firstProdotto)
                            <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $firstProdotto->id]) }}" 
                               class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>
                                Nuova Soluzione
                            </a>
                        @else
                            <button class="btn btn-success disabled" disabled>
                                <i class="bi bi-plus-circle me-1"></i>
                                Nuova Soluzione (nessun prodotto)
                            </button>
                        @endif
                        
                        {{-- CORRETTI: Link con rotte appropriate per lo staff --}}
                        <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>Prodotti Critici
                        </a>
                        <a href="{{ route('staff.malfunzionamenti.index') }}?filter=recent" class="btn btn-info">
                            <i class="bi bi-clock me-1"></i>Soluzioni Recenti
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>Dashboard Generale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MESSAGGIO SE NESSUN PRODOTTO ASSEGNATO === --}}
    @if(!$firstProdotto)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5 class="alert-heading">
                        <i class="bi bi-info-circle me-2"></i>Nessun prodotto assegnato
                    </h5>
                    <p>Non hai ancora prodotti assegnati per gestire le soluzioni tecniche.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-warning">Torna alla Dashboard</a>
                        {{-- CORRETTO: Link al catalogo pubblico per esplorare --}}
                        <a href="{{ route('prodotti.index') }}" class="btn btn-warning">Esplora Catalogo</a>
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
/* Stili per la dashboard staff */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Fix per i pulsanti grandi */
.btn-lg.w-100.h-100 {
    min-height: 120px;
    padding: 1rem;
}

/* Migliora la spaziatura delle icone nei pulsanti */
.btn .display-6 {
    font-size: 2.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-lg.w-100.h-100 {
        min-height: 100px;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}

/* Fix per list-group */
.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inizializzazione specifica per dashboard staff
    console.log('Dashboard staff caricata');
    
    // Controllo se ci sono prodotti assegnati
    const hasProdotti = {{ $firstProdotto ? 'true' : 'false' }};
    console.log('Ha prodotti assegnati:', hasProdotti);
    
    // Aggiornamento automatico statistiche ogni 2 minuti (solo se ci sono prodotti)
    if (hasProdotti) {
        setInterval(function() {
            updateStaffStats();
        }, 120000); // 2 minuti
    }
    
    // Funzione per aggiornare le statistiche staff via AJAX
    function updateStaffStats() {
        $.ajax({
            url: "{{ route('api.staff.stats') }}",
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success) {
                    console.log('Statistiche staff aggiornate', data.stats);
                    
                    // Aggiorna i contatori nella dashboard se necessario
                    if (data.stats.prodotti_assegnati !== undefined) {
                        // Aggiorna contatore prodotti assegnati se esiste nella pagina
                        $('.prodotti-count').text(data.stats.prodotti_assegnati);
                    }
                    if (data.stats.soluzioni_create !== undefined) {
                        // Aggiorna contatore soluzioni create se esiste nella pagina
                        $('.soluzioni-count').text(data.stats.soluzioni_create);
                    }
                } else {
                    console.warn('Errore API staff stats:', data.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Errore nell\'aggiornamento statistiche staff:', error);
                
                // Se l'errore è 403 (non autorizzato), reindirizza al login
                if (xhr.status === 403) {
                    console.warn('Sessione scaduta, reindirizzo al login');
                    window.location.href = "{{ route('login') }}";
                }
            }
        });
    }
    
    // Evidenziazione elementi con hover
    $('.list-group-item-action').hover(
        function() { $(this).addClass('bg-light'); },
        function() { $(this).removeClass('bg-light'); }
    );
    
    // Tooltip per pulsanti disabilitati
    $('[disabled]').tooltip({
        title: 'Funzione non disponibile - nessun prodotto assegnato',
        placement: 'top'
    });
    
    // Animazioni al caricamento per le card
    $('.card-custom').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({opacity: 1}, 300);
    });
    
    // Conferma prima di azioni importanti
    $('.btn-danger').on('click', function(e) {
        if (!confirm('Sei sicuro di voler procedere con questa azione?')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush