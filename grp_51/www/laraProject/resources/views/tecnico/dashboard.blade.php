{{--
    Dashboard Tecnico - Sistema Assistenza Tecnica
    
    Accessibile solo a utenti con livello_accesso >= 2 (Tecnici)
    Layout semplificato con ricerca sopra strumenti e layout lineare
    
    Route: GET /tecnico/dashboard
    Controller: AuthController@tecnicoDashboard
    Middleware: auth, check.level:2
    
    Funzionalità:
    - Panoramica generale dei problemi
    - Ricerca rapida prodotti e malfunzionamenti sopra strumenti
    - Accesso a schede complete con malfunzionamenti
    - Layout lineare per strumenti e statistiche
--}}

@extends('layouts.app')

@section('title', 'Dashboard Tecnico')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per il tecnico --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-person-gear text-info me-2"></i>
                Pannello Tecnico
            </h1>
            
            {{-- Benvenuto personalizzato per tecnico --}}
            <div class="alert alert-info border-start border-info border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-tools display-6 text-info me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Benvenuto, {{ auth()->user()->nome_completo }}!</h4>
                        <p class="mb-0">
                            <span class="badge bg-info">Tecnico Specializzato</span>
                            @if(auth()->user()->centro_assistenza)
                                <span class="badge bg-light text-dark ms-1">{{ auth()->user()->centro_assistenza }}</span>
                            @endif
                        </p>
                        <small class="text-muted">
                            Accesso completo al catalogo con malfunzionamenti e soluzioni tecniche
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === RICERCA RAPIDA - POSIZIONATA SOPRA STRUMENTI === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida
                        <small class="text-muted">(supporto wildcard "*" - es: "lav*" per lavatrici, lavastoviglie...)</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Ricerca prodotti --}}
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('prodotti.completo.ricerca') }}" class="d-flex">
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="search" 
                                       placeholder="Cerca prodotti: lav*, frigo*, condizionatore..."
                                       value="{{ request('search') }}"
                                       id="searchProdotti">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                            <div class="form-text">
                                Usa "*" alla fine per ricerca parziale
                            </div>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}" class="d-flex">
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="q" 
                                       placeholder="Cerca problemi: non si accende, perdita..."
                                       value="{{ request('q') }}"
                                       id="searchMalfunzionamenti">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-tools"></i>
                                </button>
                            </form>
                            <div class="form-text">
                                Ricerca nella descrizione dei problemi
                            </div>
                        </div>
                    </div>
                    
                    {{-- Suggerimenti ricerca --}}
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Suggerimenti:</strong>
                            <a href="{{ route('prodotti.completo.ricerca') }}?search=lav*" class="badge bg-light text-dark me-1">lav*</a>
                            <a href="{{ route('prodotti.completo.ricerca') }}?search=frigo*" class="badge bg-light text-dark me-1">frigo*</a>
                            <a href="{{ route('malfunzionamenti.ricerca') }}?q=non+si+accende" class="badge bg-light text-dark me-1">non si accende</a>
                            <a href="{{ route('malfunzionamenti.ricerca') }}?q=perdita" class="badge bg-light text-dark me-1">perdita</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE: STRUMENTI E STATISTICHE === --}}
    <div class="row g-4 mb-4">
        
        {{-- === STRUMENTI TECNICI - LAYOUT LINEARE === --}}
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-wrench-adjustable me-2"></i>
                        Strumenti Tecnici
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Catalogo completo --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-info btn-lg w-100 h-100">
                                <i class="bi bi-collection display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Completo</span>
                            </a>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Cerca Soluzioni</span>
                            </a>
                        </div>
                        
                        {{-- Centri assistenza --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('centri.index') }}" class="btn btn-success btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Storico interventi --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('tecnico.interventi') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-clock-history display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Miei Interventi</span>
                            </a>
                        </div>
                        
                        {{-- Prodotti critici --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-danger btn-lg w-100 h-100">
                                <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Priorità Alta</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche personali --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('tecnico.statistiche.view') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Le Mie Stats</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE SISTEMA - LAYOUT LINEARE === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                {{-- === STATISTICHE SISTEMA - STILE COMPATTO COME STAFF === --}}
    <div class="row mb-3 g-2">
        {{-- Card Prodotti Totali --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-primary">{{ $stats['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Prodotti Catalogo</small>
                    <small class="badge bg-primary bg-opacity-10 text-primary mt-1">
                        Disponibili
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Soluzioni Totali --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-tools text-success fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-success">{{ $stats['total_malfunzionamenti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Soluzioni Totali</small>
                    <small class="badge bg-success bg-opacity-10 text-success mt-1">
                        Nel Sistema
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Problemi Critici --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-exclamation-triangle text-danger fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-danger">{{ $stats['malfunzionamenti_critici'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Problemi Critici</small>
                    <small class="badge bg-danger bg-opacity-10 text-danger mt-1">
                        Priorità Alta
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Centri Assistenza --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-geo-alt text-info fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-info">{{ $stats['total_centri'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Centri Attivi</small>
                    <small class="badge bg-info bg-opacity-10 text-info mt-1">
                        Sul Territorio
                    </small>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI CON PROBLEMI CRITICI === --}}
    @if(isset($prodotti_critici) && $prodotti_critici->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Prodotti con Problemi Critici - Intervento Prioritario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($prodotti_critici->take(6) as $prodotto)
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-danger">
                                    <div class="card-body">
                                        <h6 class="card-title text-danger">
                                            {{ $prodotto->nome }}
                                            @if($prodotto->modello)
                                                <small class="text-muted d-block">{{ $prodotto->modello }}</small>
                                            @endif
                                        </h6>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-danger">{{ $prodotto->critici_count }} critici</span>
                                            <span class="badge bg-warning text-dark">{{ $prodotto->malfunzionamenti_count }} totali</span>
                                        </div>
                                        
                                        <p class="card-text small">
                                            <strong>Categoria:</strong> {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                        </p>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                               class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-eye me-1"></i>Vedi Problemi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Link per vedere tutti i critici --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-danger">
                            <i class="bi bi-list me-1"></i>Vedi Tutti i Prodotti Critici
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === MALFUNZIONAMENTI RECENTI === --}}
    @if(isset($malfunzionamenti_recenti) && $malfunzionamenti_recenti->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock text-primary me-2"></i>
                        Malfunzionamenti Segnalati Recentemente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Problema</th>
                                    <th>Gravità</th>
                                    <th>Segnalazioni</th>
                                    <th>Ultima Segnalazione</th>
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($malfunzionamenti_recenti->take(5) as $malfunzionamento)
                                    <tr>
                                        <td>
                                            <strong>{{ $malfunzionamento->prodotto->nome }}</strong>
                                            @if($malfunzionamento->prodotto->modello)
                                                <br><small class="text-muted">{{ $malfunzionamento->prodotto->modello }}</small>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($malfunzionamento->titolo, 40) }}</td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'critica' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'bassa' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $badges[$malfunzionamento->gravita] ?? 'secondary' }}">
                                                {{ ucfirst($malfunzionamento->gravita) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary" id="count-{{ $malfunzionamento->id }}">
                                                {{ $malfunzionamento->numero_segnalazioni ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($malfunzionamento->ultima_segnalazione)
                                                {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Visualizza soluzione">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-warning segnala-btn"
                                                        onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                        title="Segnala questo problema">
                                                    <i class="bi bi-exclamation-circle me-1"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i>Vedi Tutti i Malfunzionamenti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === SUPPORTO E GUIDE === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-custom border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Guida Rapida per Tecnici
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Usa la ricerca wildcard con "*" per trovare prodotti simili
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Filtra i malfunzionamenti per gravità e difficoltà
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Segnala i problemi riscontrati per aiutare altri tecnici
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Consulta lo storico per il track delle tue attività
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-custom border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        Supporto e Contatti
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Email Supporto:</span>
                        <a href="mailto:supporto@sistemaassistenza.it" class="text-decoration-none">
                            supporto@sistemaassistenza.it
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Telefono:</span>
                        <a href="tel:+390712204000" class="text-decoration-none">
                            +39 071 220 4000
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Orari:</span>
                        <span class="text-muted">Lun-Ven 8:00-18:00</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-envelope me-1"></i>Contattaci
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush

@push('styles')
<style>
/* === STILI PER RICERCA MANUALE === */

/* Indicatore di loading per input */
.loading-input {
    background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%236c757d'%3e%3ccircle cx='10' cy='10' r='1'%3e%3canimate attributeName='r' begin='0s' dur='1.8s' values='1; 4; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.165, 0.84, 0.44, 1; 0.3, 0.61, 0.355, 1' repeatCount='indefinite'/%3e%3canimate attributeName='stroke-opacity' begin='0s' dur='1.8s' values='1; 0; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.3, 0.61, 0.355, 1; 0.165, 0.84, 0.44, 1' repeatCount='indefinite'/%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
    padding-right: 40px;
}

/* Animazione per badge aggiornati */
.badge-updated {
    animation: badge-pulse 2s ease-in-out;
    transform: scale(1.2);
}

@keyframes badge-pulse {
    0% { 
        background-color: #198754;
        transform: scale(1.2);
    }
    50% { 
        background-color: #20c997;
        transform: scale(1.3);
    }
    100% { 
        background-color: #198754;
        transform: scale(1);
    }
}

/* Animazione statistiche in aggiornamento */
.updating {
    animation: pulse 1s infinite;
    opacity: 0.7;
}

@keyframes pulse {
    0% { opacity: 0.7; }
    50% { opacity: 1; }
    100% { opacity: 0.7; }
}

/* Animazione click pulsanti */
.btn-clicked {
    transform: scale(0.95);
    transition: transform 0.1s ease;
}

/* Tooltip di errore */
.error-tooltip .tooltip-inner {
    background-color: #dc3545;
    color: #fff;
}

/* Form focus migliorato */
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    transform: scale(1.02);
    transition: all 0.3s ease;
}

/* Alert personalizzati */
.alert {
    border-radius: 0.5rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive per mobile */
@media (max-width: 768px) {
    .loading-input {
        background-size: 16px;
        padding-right: 35px;
    }
    
    .alert {
        position: static !important;
        margin: 0.5rem;
        width: auto !important;
        max-width: none !important;
    }
}
</style>
@endpush