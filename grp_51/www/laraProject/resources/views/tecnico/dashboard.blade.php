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
                <div class="card-body">
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            {{-- Prodotti totali --}}
                            @if(isset($stats['total_prodotti']))
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Malfunzionamenti totali --}}
                            @if(isset($stats['total_malfunzionamenti']))
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <i class="bi bi-tools text-warning fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_malfunzionamenti'] }}</h4>
                                        <small class="text-muted">Soluzioni</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Malfunzionamenti critici --}}
                            @if(isset($stats['malfunzionamenti_critici']))
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['malfunzionamenti_critici'] }}</h4>
                                        <small class="text-muted">Critici</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Centri assistenza --}}
                            @if(isset($stats['total_centri']))
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <i class="bi bi-geo-alt text-success fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_centri'] }}</h4>
                                        <small class="text-muted">Centri</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Messaggio quando non ci sono statistiche --}}
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Caricamento statistiche in corso...</p>
                        </div>
                    @endif
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
// =====================================================
// DASHBOARD TECNICO - JavaScript Semplificato
// Mantiene le funzionalità essenziali della ricerca
// =====================================================

$(document).ready(function() {
    // === CONFIGURAZIONE GLOBALE ===
    console.log('Dashboard Tecnico caricata per: {{ auth()->user()->nome_completo }}');
    
    // URLs corretti per le API (basati sulle route del progetto)
    const API_URLS = {
        // Ricerca prodotti per tecnici (con accesso ai malfunzionamenti)
        prodotti_search: '{{ route("api.prodotti.search.tech") }}',
        
        // Ricerca malfunzionamenti (con filtri per tecnici)
        malfunzionamenti_search: '{{ route("api.malfunzionamenti.search") }}',
        
        // Statistiche dashboard in tempo reale
        stats_dashboard: '{{ route("api.stats.dashboard") }}',
        
        // Endpoint per segnalazione malfunzionamenti
        segnala_base_url: '{{ url("/api/malfunzionamenti") }}'
    };
    
    // Token CSRF per sicurezza nelle richieste AJAX
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    // Configurazione AJAX globale per includere sempre il CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    // === RICERCA CON SUGGERIMENTI AJAX ===
    let searchTimeout;
    
    // Ricerca prodotti con debouncing per evitare troppe chiamate
    $('#searchProdotti').on('input', function() {
        const query = $(this).val().trim();
        
        // Cancella timeout precedente per ottimizzare le performance
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            // Mostra indicatore di caricamento
            $(this).addClass('loading-input');
            
            // Imposta delay di 300ms per ottimizzare le chiamate API
            searchTimeout = setTimeout(() => {
                cercaProdottiAjax(query);
            }, 300);
        } else {
            // Query troppo corta: nascondi suggerimenti
            hideSuggestions();
        }
    });
    
    // Ricerca malfunzionamenti con debouncing più lungo (ricerca più complessa)
    $('#searchMalfunzionamenti').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 3) {
            $(this).addClass('loading-input');
            
            // Delay maggiore per malfunzionamenti (500ms) perché è una ricerca più complessa
            searchTimeout = setTimeout(() => {
                cercaMalfunzionamentiAjax(query);
            }, 500);
        } else {
            hideMalfunzionamentoSuggestions();
        }
    });
    
    // === FUNZIONE RICERCA PRODOTTI AJAX ===
    function cercaProdottiAjax(query) {
        $.ajax({
            url: API_URLS.prodotti_search,
            method: 'GET',
            data: { 
                q: query,
                limit: 10 // Limita risultati per performance
            },
            success: function(response) {
                // Rimuovi indicatore di loading
                $('#searchProdotti').removeClass('loading-input');
                
                if (response.success && response.data && response.data.length > 0) {
                    mostraSuggerimenti(response.data, '#searchProdotti');
                    console.log(`Trovati ${response.data.length} prodotti per "${query}"`);
                } else {
                    hideSuggestions();
                    console.log(`Nessun prodotto trovato per "${query}"`);
                }
            },
            error: function(xhr, status, error) {
                $('#searchProdotti').removeClass('loading-input');
                console.error('Errore ricerca prodotti AJAX:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                // Mostra messaggio di errore user-friendly
                showErrorTooltip('#searchProdotti', 'Errore nella ricerca prodotti');
                hideSuggestions();
            }
        });
    }
    
    // === FUNZIONE RICERCA MALFUNZIONAMENTI AJAX ===
    function cercaMalfunzionamentiAjax(query) {
        $.ajax({
            url: API_URLS.malfunzionamenti_search,
            method: 'GET',
            data: { 
                q: query,
                limit: 8,
                order: 'gravita' // Ordina per gravità (critici prima)
            },
            success: function(response) {
                $('#searchMalfunzionamenti').removeClass('loading-input');
                
                if (response.success && response.data && response.data.length > 0) {
                    mostraMalfunzionamentoSuggestions(response.data, '#searchMalfunzionamenti');
                    console.log(`Trovati ${response.data.length} malfunzionamenti per "${query}"`);
                } else {
                    hideMalfunzionamentoSuggestions();
                    console.log(`Nessun malfunzionamento trovato per "${query}"`);
                }
            },
            error: function(xhr, status, error) {
                $('#searchMalfunzionamenti').removeClass('loading-input');
                console.error('Errore ricerca malfunzionamenti AJAX:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                showErrorTooltip('#searchMalfunzionamenti', 'Errore nella ricerca malfunzionamenti');
                hideMalfunzionamentoSuggestions();
            }
        });
    }
    
    // === MOSTRA SUGGERIMENTI PRODOTTI ===
    function mostraSuggerimenti(risultati, targetInput) {
        let html = '<div class="list-group position-absolute product-suggestions" style="z-index: 1000; max-height: 400px; overflow-y: auto; width: 100%; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 0.375rem;">';
        
        risultati.forEach(function(prodotto) {
            // Icone per indicare se ci sono problemi critici
            const criticiIcon = prodotto.critici_count > 0 ? 
                `<i class="bi bi-exclamation-triangle text-danger ms-1" title="${prodotto.critici_count} problemi critici"></i>` : '';
            
            // Badge per categoria e conteggio malfunzionamenti
            const categoriaBadge = prodotto.categoria ? 
                `<span class="badge bg-light text-dark me-1">${prodotto.categoria}</span>` : '';
            
            const malfunzionamentiBadge = prodotto.malfunzionamenti_count > 0 ? 
                `<span class="badge bg-primary">${prodotto.malfunzionamenti_count}</span>` : '';
            
            const criticiBadge = prodotto.critici_count > 0 ? 
                `<span class="badge bg-danger ms-1">${prodotto.critici_count}</span>` : '';
            
            html += `
                <a href="${prodotto.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                ${prodotto.nome} ${criticiIcon}
                                ${prodotto.modello ? `<small class="text-muted">- ${prodotto.modello}</small>` : ''}
                            </h6>
                            ${prodotto.descrizione ? `<p class="mb-1 text-muted small">${prodotto.descrizione}</p>` : ''}
                            <div>
                                ${categoriaBadge}
                                ${prodotto.prezzo ? `<small class="text-success">${prodotto.prezzo}</small>` : ''}
                            </div>
                        </div>
                        <div class="text-end">
                            ${malfunzionamentiBadge}
                            ${criticiBadge}
                        </div>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        
        // Rimuovi suggerimenti precedenti e mostra nuovi
        hideSuggestions();
        $(targetInput).parent().addClass('position-relative').append(html);
    }
    
    // === MOSTRA SUGGERIMENTI MALFUNZIONAMENTI ===
    function mostraMalfunzionamentoSuggestions(risultati, targetInput) {
        let html = '<div class="list-group position-absolute malfunction-suggestions" style="z-index: 1000; max-height: 350px; overflow-y: auto; width: 100%; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 0.375rem;">';
        
        risultati.forEach(function(malfunzionamento) {
            // Colori badge per gravità del problema
            const graviColor = {
                'critica': 'danger',
                'alta': 'warning', 
                'media': 'info',
                'bassa': 'secondary'
            };
            
            // Colori per difficoltà di risoluzione
            const difficoltaColor = {
                'esperto': 'danger',
                'difficile': 'warning',
                'media': 'info',
                'facile': 'success'
            };
            
            const badgeColor = graviColor[malfunzionamento.gravita] || 'secondary';
            const diffColor = difficoltaColor[malfunzionamento.difficolta] || 'secondary';
            
            html += `
                <a href="${malfunzionamento.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${malfunzionamento.titolo}</h6>
                            <p class="mb-1 text-muted small">${malfunzionamento.descrizione}</p>
                            <small>
                                <strong>Prodotto:</strong> ${malfunzionamento.prodotto_nome}
                                ${malfunzionamento.prodotto_modello ? ` - ${malfunzionamento.prodotto_modello}` : ''}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">
                                <span class="badge bg-${badgeColor}">${malfunzionamento.gravita}</span>
                                <span class="badge bg-${diffColor} ms-1">${malfunzionamento.difficolta}</span>
                            </div>
                            <div>
                                <span class="badge bg-primary" title="Segnalazioni">${malfunzionamento.segnalazioni || 0}</span>
                                ${malfunzionamento.tempo_stimato ? 
                                    `<small class="text-muted ms-1">${malfunzionamento.tempo_stimato} min</small>` : ''}
                            </div>
                        </div>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        
        // Rimuovi suggerimenti precedenti e mostra nuovi
        hideMalfunzionamentoSuggestions();
        $(targetInput).parent().addClass('position-relative').append(html);
    }
    
    // === NASCONDI SUGGERIMENTI ===
    function hideSuggestions() {
        $('.product-suggestions').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    function hideMalfunzionamentoSuggestions() {
        $('.malfunction-suggestions').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    // === TOOLTIP PER ERRORI ===
    function showErrorTooltip(selector, message) {
        const $element = $(selector);
        
        // Rimuovi tooltip esistenti per evitare sovrapposizioni
        $element.tooltip('dispose');
        
        // Aggiungi nuovo tooltip di errore
        $element.tooltip({
            title: message,
            placement: 'bottom',
            trigger: 'manual',
            customClass: 'error-tooltip'
        }).tooltip('show');
        
        // Rimuovi automaticamente dopo 3 secondi
        setTimeout(function() {
            $element.tooltip('dispose');
        }, 3000);
    }
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        // Richiedi conferma dall'utente prima di procedere
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            return;
        }
        
        // Trova il pulsante e disabilitalo temporaneamente per evitare doppi click
        const $button = $(`[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        $button.prop('disabled', true).addClass('loading');
        
        // Esegui la chiamata AJAX per registrare la segnalazione
        $.ajax({
            url: `${API_URLS.segnala_base_url}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            data: JSON.stringify({}), // Body vuoto ma JSON valido per Laravel
            success: function(response) {
                if (response.success) {
                    // Mostra messaggio di successo
                    showAlert('Segnalazione registrata con successo!', 'success');
                    
                    // Aggiorna il contatore visibile nella tabella
                    $(`#count-${malfunzionamentoId}`).text(response.nuovo_count);
                    
                    // Aggiungi animazione di feedback per mostrare l'aggiornamento
                    $(`#count-${malfunzionamentoId}`).addClass('badge-updated');
                    setTimeout(() => {
                        $(`#count-${malfunzionamentoId}`).removeClass('badge-updated');
                    }, 2000);
                    
                    console.log(`Segnalazione registrata per malfunzionamento ${malfunzionamentoId}. Nuovo conteggio: ${response.nuovo_count}`);
                } else {
                    showAlert('Errore nella segnalazione: ' + (response.message || 'Errore sconosciuto'), 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore segnalazione:', {
                    status: xhr.status,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento.';
                
                // Prova a estrarre messaggio di errore dalla risposta
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    // Se la risposta non è JSON valida, usa il messaggio di default
                }
                
                showAlert(errorMsg, 'danger');
            },
            complete: function() {
                // Riabilita il pulsante dopo la completazione (successo o errore)
                $button.prop('disabled', false).removeClass('loading');
            }
        });
    };
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type) {
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Inserisci l'alert nel body per mostrarlo come toast
        $('body').append(alertHtml);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(function() {
            $(`#${alertId}`).fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // === AGGIORNAMENTO STATISTICHE PERIODICO ===
    function aggiornaStatistiche() {
        $.ajax({
            url: API_URLS.stats_dashboard,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Statistiche aggiornate:', response.data);
                    
                    const stats = response.data;
                    
                    // Funzione helper per aggiornare statistiche con animazione
                    function updateStat(selector, newValue) {
                        const $element = $(selector);
                        if ($element.length && $element.text() !== newValue.toString()) {
                            // Aggiungi classe per animazione di aggiornamento
                            $element.addClass('updating');
                            setTimeout(() => {
                                $element.text(newValue).removeClass('updating');
                            }, 300);
                        }
                    }
                    
                    // Aggiorna le statistiche se gli elementi esistono nel DOM
                    if (stats.total_prodotti !== undefined) {
                        updateStat('.stats-prodotti', stats.total_prodotti);
                    }
                    if (stats.total_malfunzionamenti !== undefined) {
                        updateStat('.stats-malfunzionamenti', stats.total_malfunzionamenti);
                    }
                    if (stats.malfunzionamenti_critici !== undefined) {
                        updateStat('.stats-critici', stats.malfunzionamenti_critici);
                    }
                    if (stats.total_centri !== undefined) {
                        updateStat('.stats-centri', stats.total_centri);
                    }
                }
            },
            error: function(xhr) {
                // Errore silenzioso per non disturbare l'esperienza utente
                console.warn('Aggiornamento statistiche fallito:', xhr.status);
            }
        });
    }
    
    // Avvia aggiornamento automatico statistiche ogni 10 minuti
    const statsUpdateInterval = setInterval(aggiornaStatistiche, 10 * 60 * 1000);
    
    // === GESTIONE EVENTI GENERALI ===
    
    // Nascondi suggerimenti quando si clicca fuori dall'area di ricerca
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchProdotti, #searchMalfunzionamenti, .product-suggestions, .malfunction-suggestions').length) {
            hideSuggestions();
            hideMalfunzionamentoSuggestions();
        }
    });
    
    // Gestione navigazione con tastiera nei suggerimenti
    $('#searchProdotti, #searchMalfunzionamenti').on('keydown', function(e) {
        const $suggestions = $(this).parent().find('.product-suggestions, .malfunction-suggestions');
        const $activeItem = $suggestions.find('.list-group-item.active');
        
        switch(e.which) {
            case 13: // Enter
                if ($activeItem.length) {
                    // Se c'è un elemento attivo, naviga ad esso
                    window.location.href = $activeItem.attr('href');
                } else {
                    // Altrimenti, submit del form normalmente
                    hideSuggestions();
                    hideMalfunzionamentoSuggestions();
                    $(this).closest('form').submit();
                }
                break;
                
            case 38: // Freccia su
                e.preventDefault();
                if ($activeItem.length) {
                    $activeItem.removeClass('active').prev().addClass('active');
                } else {
                    $suggestions.find('.list-group-item').last().addClass('active');
                }
                break;
                
            case 40: // Freccia giù
                e.preventDefault();
                if ($activeItem.length) {
                    $activeItem.removeClass('active').next().addClass('active');
                } else {
                    $suggestions.find('.list-group-item').first().addClass('active');
                }
                break;
                
            case 27: // Escape
                hideSuggestions();
                hideMalfunzionamentoSuggestions();
                break;
        }
    });
    
    // === SHORTCUTS DA TASTIERA ===
    $(document).on('keydown', function(e) {
        // Ctrl + F = Focus su ricerca prodotti
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('#searchProdotti').focus().select();
        }
        
        // Ctrl + M = Focus su ricerca malfunzionamenti  
        if (e.ctrlKey && e.key === 'm') {
            e.preventDefault();
            $('#searchMalfunzionamenti').focus().select();
        }
        
        // Ctrl + C = Vai al catalogo completo
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            window.location.href = '{{ route("prodotti.completo.index") }}';
        }
        
        // Ctrl + H = Vai alla dashboard principale
        if (e.ctrlKey && e.key === 'h') {
            e.preventDefault();
            window.location.href = '{{ route("dashboard") }}';
        }
    });
    
    // === INIZIALIZZAZIONE TOOLTIP E ANIMAZIONI ===
    
    // Inizializza tooltip Bootstrap per tutti gli elementi con data-bs-toggle="tooltip"
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Effetto hover migliorato per le card con animazione smooth
    $('.card.card-custom').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // === FUNZIONI DI DEBUG E TESTING ===
    
    // Test delle connessioni API per verificare che tutto funzioni
    function testConnessioniAPI() {
        console.log('🧪 Test delle connessioni API...');
        
        // Test API prodotti
        $.get(API_URLS.prodotti_search + '?q=test')
            .done(() => console.log('✅ API Prodotti: OK'))
            .fail((xhr) => console.log('❌ API Prodotti: ERRORE', xhr.status));
        
        // Test API malfunzionamenti
        $.get(API_URLS.malfunzionamenti_search + '?q=test')
            .done(() => console.log('✅ API Malfunzionamenti: OK'))
            .fail((xhr) => console.log('❌ API Malfunzionamenti: ERRORE', xhr.status));
        
        // Test API statistiche
        $.get(API_URLS.stats_dashboard)
            .done(() => console.log('✅ API Statistiche: OK'))
            .fail((xhr) => console.log('❌ API Statistiche: ERRORE', xhr.status));
    }
    
    // === CLEANUP E FINALIZZAZIONE ===
    
    // Pulizia quando si lascia la pagina per evitare memory leaks
    $(window).on('beforeunload', function() {
        if (typeof statsUpdateInterval !== 'undefined') {
            clearInterval(statsUpdateInterval);
        }
    });
    
    // === INIZIALIZZAZIONE COMPLETATA ===
    console.log('✅ Dashboard Tecnico inizializzata completamente');
    console.log('🔧 URLs API configurati:', API_URLS);
    console.log('🚀 Funzioni disponibili:', {
        'segnalaMalfunzionamento()': 'Segnala un problema riscontrato',
        'testConnessioniAPI()': 'Test delle connessioni API',
        'aggiornaStatistiche()': 'Forza aggiornamento statistiche'
    });
    
    // Esponi funzioni per debugging nella console del browser
    window.dashboardTecnico = {
        testAPI: testConnessioniAPI,
        updateStats: aggiornaStatistiche,
        urls: API_URLS,
        version: '1.0.0'
    };
    
    // Test automatico delle API all'avvio (solo in ambiente di sviluppo)
    @if(app()->environment('local'))
        setTimeout(testConnessioniAPI, 2000);
    @endif
    
    // Messaggio di benvenuto personalizzato (solo al primo accesso giornaliero)
    const oggi = new Date().toDateString();
    const ultimoAccesso = localStorage.getItem('ultimo_accesso_dashboard_tecnico');
    
    if (ultimoAccesso !== oggi) {
        setTimeout(function() {
            showAlert('Benvenuto nella Dashboard Tecnico! Usa Ctrl+F per cercare prodotti e Ctrl+M per malfunzionamenti.', 'info');
            localStorage.setItem('ultimo_accesso_dashboard_tecnico', oggi);
        }, 1500);
    }
});
</script>
@endpush

@push('styles')
<style>
/* =====================================================
   DASHBOARD TECNICO - CSS Semplificato
   Layout lineare mantenendo le funzionalità originali
   ===================================================== */

/* === STILI BASE PER DASHBOARD TECNICO === */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* === STILI PER INPUT DI RICERCA === */
.form-control {
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    transform: scale(1.02);
}

/* Indicatore di loading per input durante la ricerca */
.loading-input {
    background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%236c757d'%3e%3ccircle cx='10' cy='10' r='1'%3e%3canimate attributeName='r' begin='0s' dur='1.8s' values='1; 4; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.165, 0.84, 0.44, 1; 0.3, 0.61, 0.355, 1' repeatCount='indefinite'/%3e%3canimate attributeName='stroke-opacity' begin='0s' dur='1.8s' values='1; 0; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.3, 0.61, 0.355, 1; 0.165, 0.84, 0.44, 1' repeatCount='indefinite'/%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
    padding-right: 40px;
}

/* === STILI PER SUGGERIMENTI DI RICERCA === */
.list-group {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    background-color: #fff;
    max-height: 400px;
    overflow-y: auto;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
    cursor: pointer;
}

.list-group-item:hover,
.list-group-item.active {
    background-color: #e3f2fd;
    border-color: #e3f2fd;
    color: #0d47a1;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Scrollbar personalizzata per suggerimenti */
.list-group::-webkit-scrollbar {
    width: 6px;
}

.list-group::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.list-group::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.list-group::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === BADGE E INDICATORI === */
.badge {
    font-size: 0.75em;
    font-weight: 600;
    transition: all 0.2s ease;
}

/* Animazione per badge aggiornati dopo segnalazione */
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

/* Colori personalizzati per badge di gravità */
.badge.bg-danger {
    background-color: #dc3545 !important;
    animation: pulse-danger 2s infinite ease-in-out;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

@keyframes pulse-danger {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

/* === STILI PER STATISTICHE === */
.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
    transition: all 0.3s ease;
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-info.bg-opacity-10 {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

/* Animazione per statistiche in aggiornamento */
.updating {
    animation: pulse 1s infinite;
    opacity: 0.7;
}

@keyframes pulse {
    0% { opacity: 0.7; }
    50% { opacity: 1; }
    100% { opacity: 0.7; }
}

/* === STILI PER PULSANTI === */
.btn {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Effetti hover per pulsanti grandi degli strumenti */
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* === STILI PER CARD HEADER === */
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    position: relative;
}

.card-header.bg-info {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #0dcaf0 0%, #17a2b8 100%);
}

.card-header.bg-danger {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.card-header.bg-light {
    border-bottom: 1px solid #dee2e6;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* === STILI PER ELEMENTI CRITICI === */
.border-danger {
    border-width: 2px !important;
    position: relative;
    overflow: hidden;
}

.border-danger::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #dc3545, #fd7e14);
    border-radius: 0.375rem;
    z-index: -1;
    opacity: 0.1;
    animation: border-glow 3s ease-in-out infinite;
}

@keyframes border-glow {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 0.2; }
}

/* === ALERT PERSONALIZZATI === */
.alert {
    border: none;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
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

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border-left: 4px solid #0dcaf0;
}

.alert-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
    color: #0f5132;
    border-left: 4px solid #198754;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-left: 4px solid #ffc107;
}

/* === TOOLTIP PERSONALIZZATI === */
.tooltip {
    font-size: 0.875rem;
}

.tooltip.show {
    opacity: 1;
}

.tooltip .tooltip-inner {
    background-color: #212529;
    color: #fff;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.2);
}

.error-tooltip .tooltip-inner {
    background-color: #dc3545;
    color: #fff;
}

/* === EVIDENZIAZIONE TERMINI DI RICERCA === */
mark, .highlight {
    background-color: #fff3cd;
    padding: 0.125em 0.25em;
    border-radius: 0.25rem;
    font-weight: 500;
}

/* === ICONE ANIMATE === */
.btn i, .card-header i {
    transition: transform 0.2s ease;
}

.btn:hover i {
    transform: scale(1.1);
}

.card-header i {
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .card-custom {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
    
    .card-body h4 {
        font-size: 1.5rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
        padding-bottom: 2rem;
    }
    
    /* Suggerimenti più piccoli su mobile */
    .list-group {
        max-height: 250px;
        font-size: 0.875rem;
    }
    
    .list-group-item h6 {
        font-size: 1rem;
    }
    
    .list-group-item p {
        font-size: 0.75rem;
    }
    
    /* Alert responsive per mobile */
    .alert {
        position: static !important;
        margin: 0.5rem;
        width: auto !important;
        max-width: none !important;
    }
}

@media (max-width: 576px) {
    /* Layout compatto per schermi molto piccoli */
    .col-lg-2 {
        flex: 0 0 auto;
        width: 50%; /* Due pulsanti per riga su mobile */
    }
    
    .row.g-4 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
    
    /* Nasconde alcuni elementi non essenziali su schermi molto piccoli */
    .badge.bg-light.text-dark {
        display: none;
    }
    
    .form-text {
        font-size: 0.75rem;
    }
}

/* === ACCESSIBILITÀ === */
.btn:focus,
.form-control:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

.list-group-item:focus {
    outline: 2px solid #0d6efd;
    outline-offset: -2px;
}

/* Contrasto migliorato per testi piccoli */
.text-muted {
    color: #6c757d !important;
}

.small, small {
    color: #495057 !important;
}

/* === TRANSIZIONI SMOOTH === */
.card,
.btn,
.form-control,
.badge,
.alert,
.list-group-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* === EFFETTI SPECIALI === */
.card-custom::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #0d6efd, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card-custom:hover::after {
    opacity: 0.3;
}

/* === LOADING STATES === */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #0d6efd;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* === MIGLIORAMENTI VISUAL === */
.shadow-custom {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.shadow-hover:hover {
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2) !important;
    transition: box-shadow 0.3s ease;
}

/* === PRINT STYLES === */
@media print {
    .card-custom,
    .btn,
    .alert {
        box-shadow: none !important;
        background: white !important;
        color: black !important;
    }
    
    .btn {
        border: 1px solid #000 !important;
    }
    
    .product-suggestions,
    .malfunction-suggestions {
        display: none !important;
    }
}
</style>
@endpush