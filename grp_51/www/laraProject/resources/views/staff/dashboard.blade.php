{{-- Dashboard Staff Corretta --}}
@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="container mt-4">
    
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
                            Gestisci malfunzionamenti e soluzioni tecniche per tutti i prodotti del catalogo
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
        
        {{-- === ACCESSI RAPIDI STAFF CORRETTI === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Strumenti Staff - Gestione Malfunzionamenti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- CORRETTO: Gestisci Malfunzionamenti (non "Cerca Soluzioni") --}}
                        <div class="col-md-6">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" 
                               class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-tools display-6 mb-2"></i>
                                <span class="fw-semibold">Gestisci Malfunzionamenti</span>
                                <small class="text-muted">CRUD completo problemi e soluzioni</small>
                            </a>
                        </div>
                        
                        {{-- CORRETTO: Catalogo Prodotti (non "Catalogo Tecnico") --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}" 
                               class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-collection display-6 mb-2"></i>
                                <span class="fw-semibold">Catalogo Prodotti</span>
                                <small class="text-muted">Vista completa con malfunzionamenti</small>
                            </a>
                        </div>

                        {{-- Aggiungi Nuova Soluzione --}}
                        <div class="col-md-6">
                            @if($firstProdotto)
                                <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $firstProdotto->id]) }}" 
                                   class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-plus-circle display-6 mb-2"></i>
                                    <span class="fw-semibold">Nuova Soluzione</span>
                                    <small class="text-muted">Aggiungi problema e soluzione</small>
                                </a>
                            @else
                                <div class="btn btn-outline-secondary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center" style="cursor: not-allowed;">
                                    <i class="bi bi-plus-circle display-6 mb-2"></i>
                                    <span class="fw-semibold">Nuova Soluzione</span>
                                    <small class="text-muted">Nessun prodotto disponibile</small>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Le Mie Statistiche --}}
                        <div class="col-md-6">
                            <a href="{{ route('staff.statistiche') }}" 
                               class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-graph-up display-6 mb-2"></i>
                                <span class="fw-semibold">Le Mie Statistiche</span>
                                <small class="text-muted">Performance e attività</small>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE RAPIDE === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Statistiche Rapide
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Prodotti con accesso staff --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <h6 class="mb-0">Prodotti Totali</h6>
                            <small class="text-muted">Catalogo completo</small>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $stats['total_prodotti'] ?? 0 }}</span>
                    </div>

                    {{-- Malfunzionamenti totali --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <h6 class="mb-0">Malfunzionamenti</h6>
                            <small class="text-muted">Sistema totale</small>
                        </div>
                        <span class="badge bg-warning fs-6">{{ $stats['total_malfunzionamenti'] ?? 0 }}</span>
                    </div>

                    {{-- Problemi critici --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <h6 class="mb-0">Problemi Critici</h6>
                            <small class="text-muted">Richiedono attenzione</small>
                        </div>
                        <span class="badge bg-danger fs-6">{{ $stats['malfunzionamenti_critici'] ?? 0 }}</span>
                    </div>

                    {{-- Soluzioni create --}}
                    <div class="d-flex justify-content-between align-items-center mb-0 p-3 bg-light rounded">
                        <div>
                            <h6 class="mb-0">Le Mie Soluzioni</h6>
                            <small class="text-muted">Create da te</small>
                        </div>
                        <span class="badge bg-success fs-6">{{ $stats['soluzioni_create'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === SEZIONE ATTIVITÀ RECENTI === --}}
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Ultime Soluzioni Create
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats['ultime_soluzioni']) && $stats['ultime_soluzioni']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['ultime_soluzioni']->take(5) as $soluzione)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="me-auto">
                                        <h6 class="mb-1">{{ $soluzione->titolo ?? 'Problema senza titolo' }}</h6>
                                        <p class="mb-1 text-muted small">
                                            {{ Str::limit($soluzione->descrizione ?? '', 60) }}
                                        </p>
                                        <small class="text-muted">{{ $soluzione->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-{{ $soluzione->gravita == 'critica' ? 'danger' : 'primary' }} rounded-pill">
                                        {{ ucfirst($soluzione->gravita ?? 'normale') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4 mb-3"></i>
                            <p>Nessuna soluzione creata di recente</p>
                            <small>Le tue soluzioni appariranno qui</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- === AZIONI RAPIDE AGGIUNTIVE === --}}
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        {{-- Report attività --}}
                        <a href="{{ route('staff.report.attivita') }}" class="btn btn-outline-dark">
                            <i class="bi bi-file-text me-2"></i>
                            Genera Report Attività
                        </a>
                        
                        {{-- Ricerca avanzata malfunzionamenti --}}
                        <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>
                            Ricerca Avanzata Problemi
                        </a>
                        
                        {{-- Funzionalità Opzionale: Prodotti Assegnati (se disponibile) --}}
                        @if(\Schema::hasColumn('prodotti', 'staff_assegnato_id'))
                            <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-outline-warning">
                                <i class="bi bi-person-check me-2"></i>
                                I Miei Prodotti Assegnati
                                <span class="badge bg-warning text-dark">Opzionale</span>
                            </a>
                        @endif
                        
                        {{-- Torna alla dashboard generale --}}
                        <hr class="my-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>
                            Dashboard Generale
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MESSAGGIO INFORMATIVO PER FUNZIONALITÀ OPZIONALE === --}}
    @if(!\Schema::hasColumn('prodotti', 'staff_assegnato_id'))
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle me-2"></i>
                        Funzionalità Opzionale Non Attivata
                    </h6>
                    <p class="mb-0">
                        La ripartizione dei prodotti tra membri dello staff non è ancora attivata. 
                        Puoi gestire malfunzionamenti per tutti i prodotti del catalogo.
                        <a href="mailto:admin@sistemaassistenza.it" class="alert-link">
                            Contatta l'amministratore per attivare questa funzionalità.
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- === MESSAGGIO NESSUN PRODOTTO (se applicabile) === --}}
    @if(isset($stats['total_prodotti']) && $stats['total_prodotti'] == 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Nessun Prodotto Disponibile</h6>
                    <p class="mb-3">
                        Non ci sono prodotti nel catalogo su cui lavorare.
                        Contatta l'amministratore per aggiungere prodotti al sistema.
                    </p>
                    <p class="mb-0">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-warning">Torna alla Dashboard</a>
                        <a href="{{ route('prodotti.index') }}" class="btn btn-warning">Esplora Catalogo Pubblico</a>
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
/* Stili per la dashboard staff corretta */
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
    min-height: 130px;
    padding: 1rem;
    text-align: center;
}

.btn-lg.w-100.h-100 small {
    font-size: 0.75rem;
    opacity: 0.8;
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
    border-color: rgba(0,0,0,0.125);
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

.badge.fs-6 {
    font-size: 1rem !important;
}

/* Statistiche cards */
.bg-light.rounded {
    border-left: 4px solid #007bff;
}

.bg-light.rounded:nth-child(2) {
    border-left-color: #ffc107;
}

.bg-light.rounded:nth-child(3) {
    border-left-color: #dc3545;
}

.bg-light.rounded:nth-child(4) {
    border-left-color: #28a745;
}

/* Hover effects per le azioni */
.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Fix alert */
.alert {
    border-width: 1px;
    border-style: solid;
}

.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border-color: #b6effb;
    color: #055160;
}

.alert-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border-color: #ffecb5;
    color: #664d03;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inizializzazione dashboard staff
    console.log('Dashboard staff caricata - versione corretta');
    
    // Tooltip per i pulsanti
    $('[title]').tooltip();
    
    // Animazioni al caricamento
    $('.card-custom').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({opacity: 1}, 300);
    });
    
    // Evidenziazione statistiche critiche
    const critici = {{ $stats['malfunzionamenti_critici'] ?? 0 }};
    if (critici > 0) {
        $('.badge.bg-danger').parent().parent().addClass('border-danger').addClass('border-2');
    }
    
    // Click tracking per analytics (opzionale)
    $('.btn[href]').on('click', function() {
        const action = $(this).find('.fw-semibold').text() || 'Link cliccato';
        console.log('Staff dashboard action:', action);
    });
    
    // Aggiornamento automatico statistiche ogni 5 minuti
    setInterval(function() {
        // Qui puoi aggiungere chiamate AJAX per aggiornare le statistiche
        console.log('Aggiornamento statistiche staff...');
    }, 300000); // 5 minuti
});
</script>
@endpush