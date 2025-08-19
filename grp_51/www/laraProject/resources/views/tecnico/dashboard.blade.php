{{-- Dashboard specifica per i tecnici --}}
@extends('layouts.app')

@section('title', 'Dashboard Tecnico')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per il tecnico --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-person-gear text-info me-2"></i>
                Dashboard Tecnico
            </h1>
            
            {{-- Benvenuto personalizzato per tecnico --}}
            <div class="alert alert-info border-start border-info border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-gear display-6 text-info me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Benvenuto, {{ $user->nome_completo }}!</h4>
                        <p class="mb-0">
                            <span class="badge bg-info">Tecnico Specializzato</span>
                            @if($user->specializzazione)
                                - {{ $user->specializzazione }}
                            @endif
                        </p>
                        {{-- Informazioni centro assistenza --}}
                        @if($user->centroAssistenza)
                            <small class="text-muted">
                                Centro: {{ $user->centroAssistenza->nome }} - {{ $user->centroAssistenza->citta }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard Tecnico</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- === STRUMENTI TECNICO === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-tools me-2"></i>
                        Strumenti Tecnico
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Ricerca rapida soluzioni --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.index') }}?view=tech" class="btn btn-info btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Cerca Soluzioni</span>
                            </a>
                        </div>
                        
                        {{-- Prodotti più problematici --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.index') }}?problematici=1" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Prodotti Critici</span>
                            </a>
                        </div>
                        
                        {{-- Catalogo completo --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.index') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-box display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Altri centri assistenza --}}
                        <div class="col-md-6">
                            <a href="{{ route('centri.index') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE TECNICO === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            {{-- Totale prodotti --}}
                            @if(isset($stats['total_prodotti']))
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Totale soluzioni disponibili --}}
                            @if(isset($stats['total_malfunzionamenti']))
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <i class="bi bi-check-circle text-success fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_malfunzionamenti'] }}</h4>
                                        <small class="text-muted">Soluzioni</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Malfunzionamenti critici --}}
                            @if(isset($stats['malfunzionamenti_critici']))
                                <div class="col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['malfunzionamenti_critici'] }}</h4>
                                        <small class="text-muted">Critici</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Centri assistenza --}}
                            @if(isset($stats['total_centri']))
                                <div class="col-6">
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        <i class="bi bi-geo-alt text-info fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_centri'] }}</h4>
                                        <small class="text-muted">Centri</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted text-center">Statistiche non disponibili</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === INFORMAZIONI CENTRO ASSISTENZA === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Il Tuo Centro Assistenza
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->centroAssistenza)
                        <h6 class="fw-bold">{{ $user->centroAssistenza->nome }}</h6>
                        <p class="mb-1">
                            <i class="bi bi-geo-alt text-muted me-1"></i>
                            {{ $user->centroAssistenza->indirizzo_completo }}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-telephone text-muted me-1"></i>
                            {{ $user->centroAssistenza->telefono_formattato }}
                        </p>
                        @if($user->centroAssistenza->email)
                            <p class="mb-0">
                                <i class="bi bi-envelope text-muted me-1"></i>
                                {{ $user->centroAssistenza->email }}
                            </p>
                        @endif
                        
                        {{-- Informazioni aggiuntive centro --}}
                        <hr class="my-3">
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <h6 class="mb-1">{{ $user->centroAssistenza->provincia ?? 'N/A' }}</h6>
                                    <small class="text-muted">Provincia</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <h6 class="mb-1">{{ $user->centroAssistenza->regione ?? 'N/A' }}</h6>
                                    <small class="text-muted">Regione</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <p class="text-muted mt-2">Nessun centro di assistenza assegnato</p>
                            <small class="text-muted">Contatta l'amministratore per l'assegnazione</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- === PRODOTTI PROBLEMATICI === --}}
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Prodotti con Problemi Critici
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats['prodotti_problematici']) && $stats['prodotti_problematici']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['prodotti_problematici']->take(5) as $prodotto)
                                <div class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $prodotto->nome }}</h6>
                                            <small class="text-muted">{{ $prodotto->codice }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger">
                                                {{ $prodotto->critici_count ?? $prodotto->malfunzionamenti->where('gravita', 'critica')->count() }} critici
                                            </span>
                                            <br>
                                            <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary btn-sm mt-1">
                                                <i class="bi bi-eye me-1"></i>Vedi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($stats['prodotti_problematici']->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('prodotti.index') }}?problematici=1" class="btn btn-outline-warning">
                                    Vedi tutti i {{ $stats['prodotti_problematici']->count() }} prodotti
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <p class="text-success mt-2">Nessun prodotto con problemi critici</p>
                            <small class="text-muted">Ottimo lavoro! Il sistema è in buone condizioni.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === MALFUNZIONAMENTI CRITICI RECENTI === --}}
    @if(isset($stats['malfunzionamenti_critici_lista']) && $stats['malfunzionamenti_critici_lista']->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Problemi Critici Recenti - Intervento Urgente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($stats['malfunzionamenti_critici_lista']->take(6) as $malfunzionamento)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-danger">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title text-danger">{{ Str::limit($malfunzionamento->titolo, 30) }}</h6>
                                                <p class="card-text small">
                                                    <strong>Prodotto:</strong> {{ $malfunzionamento->prodotto->nome ?? 'N/A' }}
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-muted">{{ $malfunzionamento->created_at->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                            <span class="badge bg-danger">CRITICO</span>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    

    {{-- === DISTRIBUZIONE MALFUNZIONAMENTI === --}}
    @if(isset($stats['malfunzionamenti_per_gravita']) && count($stats['malfunzionamenti_per_gravita']) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Distribuzione Problemi per Gravità
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        @foreach($stats['malfunzionamenti_per_gravita'] as $gravita => $count)
                            @php
                                $colors = ['critica' => 'danger', 'alta' => 'warning', 'media' => 'info', 'bassa' => 'success'];
                                $icons = ['critica' => 'exclamation-triangle-fill', 'alta' => 'exclamation-triangle', 'media' => 'info-circle', 'bassa' => 'check-circle'];
                            @endphp
                            <div class="col-md-3">
                                <div class="p-3 bg-{{ $colors[$gravita] ?? 'secondary' }} bg-opacity-10 rounded">
                                    <i class="bi bi-{{ $icons[$gravita] ?? 'circle' }} text-{{ $colors[$gravita] ?? 'secondary' }} fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $count }}</h4>
                                    <small class="text-muted">{{ ucfirst($gravita) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inizializzazione specifica per dashboard tecnico
    console.log('Dashboard tecnico caricata per {{ $user->nome_completo }}');
    
    // Aggiornamento periodico statistiche (ogni 5 minuti)
    setInterval(function() {
        // Qui potresti implementare un aggiornamento AJAX delle statistiche
        console.log('Controllo aggiornamenti statistiche...');
    }, 300000); // 5 minuti
    
    // Gestione ricerca rapida con Enter
    $('input[name="search"], input[name="malfunzionamento"]').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });
    
    // Evidenziazione prodotti critici
    $('.border-danger').hover(
        function() { $(this).addClass('shadow-sm'); },
        function() { $(this).removeClass('shadow-sm'); }
    );
    
    // Alert per problemi critici
    @if(isset($stats['malfunzionamenti_critici']) && $stats['malfunzionamenti_critici'] > 5)
        setTimeout(function() {
            if (typeof showToast === 'function') {
                showToast('Attenzione: {{ $stats["malfunzionamenti_critici"] }} problemi critici richiedono intervento!', 'warning');
            }
        }, 2000);
    @endif
});
</script>
@endpush