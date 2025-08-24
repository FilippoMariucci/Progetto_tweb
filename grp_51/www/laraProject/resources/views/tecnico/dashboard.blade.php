{{--
    Dashboard Tecnico - Sistema Assistenza Tecnica
    
    Accessibile solo a utenti con livello_accesso >= 2 (Tecnici)
    Fornisce accesso completo a prodotti e malfunzionamenti
    
    Route: GET /tecnico/dashboard
    Controller: AuthController@tecnicoDashboard
    Middleware: auth, check.level:2
    
    Funzionalità:
    - Panoramica generale dei problemi
    - Ricerca rapida prodotti e malfunzionamenti  
    - Accesso a schede complete con malfunzionamenti
    - Storico interventi personali
    - Segnalazione problemi con wildcard search
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

    <div class="row g-4">
        
        {{-- === GESTIONE PRINCIPALE === --}}
        <div class="col-lg-8">
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
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-info btn-lg w-100 h-100">
                                <i class="bi bi-collection display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Completo</span>
                            </a>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Cerca Soluzioni</span>
                            </a>
                        </div>
                        
                        {{-- Centri assistenza --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('centri.index') }}" class="btn btn-success btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Storico interventi --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('tecnico.interventi') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-clock-history display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Miei Interventi</span>
                            </a>
                        </div>
                        
                        {{-- Prodotti critici --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-danger btn-lg w-100 h-100">
                                <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Priorità Alta</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche personali --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('tecnico.statistiche') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Le Mie Stats</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE GENERALI === --}}
        <div class="col-lg-4">
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
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Malfunzionamenti totali --}}
                            @if(isset($stats['total_malfunzionamenti']))
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <i class="bi bi-tools text-warning fs-1"></i>
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

    {{-- === RICERCA RAPIDA === --}}
    <div class="row mt-4">
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
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                        title="Segnala questo problema">
                                                    <i class="bi bi-plus-circle"></i>
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
$(document).ready(function() {
    // === INIZIALIZZAZIONE DASHBOARD TECNICO ===
    console.log('Dashboard Tecnico caricata per: {{ auth()->user()->nome_completo }}');
    
    // === RICERCA CON SUGGERIMENTI AJAX ===
    let searchTimeout;
    
    $('#searchProdotti').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                cercaProdottiAjax(query);
            }, 300);
        } else {
            hideSuggestions();
        }
    });
    
    $('#searchMalfunzionamenti').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                cercaMalfunzionamentiAjax(query);
            }, 500);
        } else {
            hideMalfunzionamentoSuggestions();
        }
    });
    
    function cercaProdottiAjax(query) {
        $.ajax({
            url: '{{ route("api.prodotti.search.tech") }}',
            method: 'GET',
            data: { q: query },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    mostraSuggerimenti(response.data, '#searchProdotti');
                } else {
                    hideSuggestions();
                }
            },
            error: function(xhr) {
                console.error('Errore ricerca AJAX prodotti:', xhr.responseText);
                hideSuggestions();
            }
        });
    }
    
    function cercaMalfunzionamentiAjax(query) {
        $.ajax({
            url: '{{ route("api.malfunzionamenti.search") }}',
            method: 'GET',
            data: { 
                q: query,
                limit: 8
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    mostraMalfunzionamentoSuggestions(response.data, '#searchMalfunzionamenti');
                } else {
                    hideMalfunzionamentoSuggestions();
                }
            },
            error: function(xhr) {
                console.error('Errore ricerca AJAX malfunzionamenti:', xhr.responseText);
                hideMalfunzionamentoSuggestions();
            }
        });
    }
    
    function mostraSuggerimenti(risultati, targetInput) {
        let html = '<div class="list-group position-absolute product-suggestions" style="z-index: 1000; max-height: 400px; overflow-y: auto; width: 100%;">';
        
        risultati.forEach(function(prodotto) {
            const criticiIcon = prodotto.critici_count > 0 ? 
                `<i class="bi bi-exclamation-triangle text-danger ms-1" title="${prodotto.critici_count} problemi critici"></i>` : '';
            
            html += `
                <a href="${prodotto.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${prodotto.nome} ${criticiIcon}</h6>
                            <p class="mb-1 text-muted small">${prodotto.modello || ''}</p>
                            <small class="text-muted">${prodotto.categoria}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">${prodotto.malfunzionamenti_count || 0}</span>
                            ${prodotto.critici_count > 0 ? 
                                `<span class="badge bg-danger ms-1">${prodotto.critici_count}</span>` : ''}
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
    
    function mostraMalfunzionamentoSuggestions(risultati, targetInput) {
        let html = '<div class="list-group position-absolute malfunction-suggestions" style="z-index: 1000; max-height: 350px; overflow-y: auto; width: 100%;">';
        
        risultati.forEach(function(malfunzionamento) {
            const graviColor = {
                'critica': 'danger',
                'alta': 'warning', 
                'media': 'info',
                'bassa': 'secondary'
            };
            
            const badgeColor = graviColor[malfunzionamento.gravita] || 'secondary';
            
            html += `
                <a href="${malfunzionamento.url}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${malfunzionamento.titolo}</h6>
                            <p class="mb-1 text-muted small">${malfunzionamento.descrizione}</p>
                            <small><strong>Prodotto:</strong> ${malfunzionamento.prodotto_nome}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${badgeColor}">${malfunzionamento.gravita}</span>
                            <span class="badge bg-primary ms-1">${malfunzionamento.segnalazioni}</span>
                            ${malfunzionamento.tempo_stimato ? 
                                `<div><small class="text-muted">${malfunzionamento.tempo_stimato} min</small></div>` : ''}
                        </div>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        
        hideMalfunzionamentoSuggestions();
        $(targetInput).parent().addClass('position-relative').append(html);
    }
    
    function hideSuggestions() {
        $('.product-suggestions').remove();
    }
    
    function hideMalfunzionamentoSuggestions() {
        $('.malfunction-suggestions').remove();
    }
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            return;
        }
        
        $.ajax({
            url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // Mostra messaggio di successo
                    showAlert('Segnalazione registrata con successo!', 'success');
                    
                    // Aggiorna il contatore visibile
                    $(`#count-${malfunzionamentoId}`).text(response.nuovo_count);
                } else {
                    showAlert('Errore nella segnalazione: ' + (response.message || 'Errore sconosciuto'), 'danger');
                }
            },
            error: function(xhr) {
                console.error('Errore segnalazione:', xhr.responseText);
                let errorMsg = 'Errore nella segnalazione del malfunzionamento.';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    // Mantieni messaggio di default
                }
                
                showAlert(errorMsg, 'danger');
            }
        });
    };
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Inserisci l'alert all'inizio del container
        $('.container').prepend(alertHtml);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    // === GESTIONE EVENTI ===
    
    // Nascondi suggerimenti quando si clicca fuori
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchProdotti, #searchMalfunzionamenti').length) {
            hideSuggestions();
            hideMalfunzionamentoSuggestions();
        }
    });
    
    // Submit con Enter
    $('#searchProdotti, #searchMalfunzionamenti').on('keypress', function(e) {
        if (e.which === 13) { // Tasto Enter
            hideSuggestions();
            hideMalfunzionamentoSuggestions();
            $(this).closest('form').submit();
        }
    });
    
    // === TOOLTIPS E ANIMAZIONI ===
    
    // Inizializza tooltip Bootstrap
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Effetto hover sulle card statistiche
    $('.card.card-custom').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // === AGGIORNAMENTO STATISTICHE PERIODICO ===
    
    let statsUpdateInterval;
    
    function aggiornaStatistiche() {
        $.ajax({
            url: '{{ route("api.stats.dashboard") }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    // Aggiorna i valori nelle card statistiche
                    const stats = response.data;
                    
                    if (stats.total_prodotti !== undefined) {
                        $('.bg-primary.bg-opacity-10 h4').text(stats.total_prodotti);
                    }
                    if (stats.total_malfunzionamenti !== undefined) {
                        $('.bg-warning.bg-opacity-10 h4').text(stats.total_malfunzionamenti);
                    }
                    if (stats.malfunzionamenti_critici !== undefined) {
                        $('.bg-danger.bg-opacity-10 h4').text(stats.malfunzionamenti_critici);
                    }
                    if (stats.total_centri !== undefined) {
                        $('.bg-success.bg-opacity-10 h4').text(stats.total_centri);
                    }
                }
            },
            error: function(xhr) {
                // Errore silenzioso, non disturbare l'utente
                console.warn('Aggiornamento statistiche fallito:', xhr.status);
            }
        });
    }
    
    // Aggiorna statistiche ogni 10 minuti
    statsUpdateInterval = setInterval(aggiornaStatistiche, 10 * 60 * 1000);
    
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
        
        // Ctrl + H = Vai alla home
        if (e.ctrlKey && e.key === 'h') {
            e.preventDefault();
            window.location.href = '{{ route("dashboard") }}';
        }
    });
    
    // === ANALYTICS E TRACKING ===
    
    // Traccia utilizzo dashboard (per miglioramenti)
    console.log('Dashboard Tecnico Analytics:', {
        user_level: {{ auth()->user()->livello_accesso }},
        has_centro: {{ auth()->user()->centro_assistenza ? 'true' : 'false' }},
        stats: {
            prodotti: {{ $stats['total_prodotti'] ?? 0 }},
            malfunzionamenti: {{ $stats['total_malfunzionamenti'] ?? 0 }},
            critici: {{ $stats['malfunzionamenti_critici'] ?? 0 }}
        },
        timestamp: new Date().toISOString(),
        session_start: true
    });
    
    // === ALERT AUTOMATICI ===
    
    // Avviso se ci sono molti problemi critici
    @if(isset($stats['malfunzionamenti_critici']) && $stats['malfunzionamenti_critici'] > 10)
        setTimeout(function() {
            if (confirm('ATTENZIONE: {{ $stats["malfunzionamenti_critici"] }} problemi critici rilevati nel sistema!\n\nVuoi visualizzare i prodotti con priorità alta?')) {
                window.location.href = '{{ route("prodotti.completo.index") }}?filter=critici';
            }
        }, 3000); // Dopo 3 secondi
    @endif
    
    // === CLEANUP ===
    
    // Pulizia quando si lascia la pagina
    $(window).on('beforeunload', function() {
        if (statsUpdateInterval) {
            clearInterval(statsUpdateInterval);
        }
    });
    
    // === INIZIALIZZAZIONE COMPLETATA ===
    
    console.log('Dashboard Tecnico inizializzata completamente');
    
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
/* === STILI PERSONALIZZATI PER DASHBOARD TECNICO === */

/* Stili per le card personalizzate (coerente con admin/staff) */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Effetti hover per i pulsanti di gestione (coerente con admin) */
.btn-lg:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Stili per gli indicatori di stato */
.badge.bg-success { background-color: #198754 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; }
.badge.bg-primary { background-color: #0d6efd !important; }

/* Badge per livello tecnico */
.badge-livello {
    font-size: 0.75em;
    font-weight: 600;
}

/* Animazioni per le statistiche */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.updating {
    animation: pulse 1s infinite;
}

/* Suggerimenti ricerca con stile coerente */
.list-group {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    background-color: #fff;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group::-webkit-scrollbar {
    width: 6px;
}

.list-group::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.list-group::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.list-group::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Stile per campi ricerca attivi */
#searchProdotti:focus, #searchMalfunzionamenti:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Stile per elementi critici (coerente con admin) */
.border-danger {
    border-width: 2px !important;
    position: relative;
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
}

/* Badge personalizzati per gravità con animazione */
.badge.bg-danger {
    animation: pulse-danger 2s infinite;
}

@keyframes pulse-danger {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Loading states per AJAX */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: "";
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-left: 10px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Evidenziazione termini di ricerca */
mark {
    background-color: #fff3cd;
    padding: 0.125em 0.25em;
    border-radius: 0.25rem;
}

/* Icone animate per feedback */
.btn i {
    transition: transform 0.2s ease;
}

.btn:hover i {
    transform: scale(1.1);
}

/* Stile per alert personalizzati */
.alert {
    border: none;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}

.alert-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
    color: #0f5132;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
    color: #721c24;
}

/* Responsive design per schermi piccoli (coerente con admin/staff) */
@media (max-width: 768px) {
    .col-lg-4, .col-md-6 {
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
    }
}

/* Stile per tooltip personalizzati */
.tooltip.show {
    opacity: 1;
}

.tooltip .tooltip-inner {
    background-color: #212529;
    color: #fff;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

/* Footer spacer per mobile */
@media (max-width: 768px) {
    .container {
        padding-bottom: 2rem;
    }
}

/* Stile per le statistiche nelle card (coerente) */
.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
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

/* Bordi colorati per le card header */
.card-header.bg-info {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header.bg-danger {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header.bg-light {
    border-bottom: 1px solid #dee2e6;
}

/* Miglioramenti accessibilità */
.btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

/* Stile per i suggerimenti nella ricerca */
.badge.bg-light.text-dark:hover {
    background-color: #e9ecef !important;
    cursor: pointer;
}
</style>
@endpush