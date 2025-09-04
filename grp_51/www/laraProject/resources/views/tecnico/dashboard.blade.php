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
// =====================================================
// DASHBOARD TECNICO - JavaScript per Ricerca Manuale
// La ricerca avviene solo tramite click del bottone o ENTER
// Nessuna ricerca automatica durante il typing
// =====================================================

$(document).ready(function() {
    // === CONFIGURAZIONE GLOBALE ===
    console.log('Dashboard Tecnico caricata per: {{ auth()->user()->nome_completo }}');
    
    // URLs corretti per le API (basati sulle route del progetto)
    const API_URLS = {
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

    // === DEBUG: Verifica presenza elementi ===
    console.log('Elemento searchProdotti trovato:', $('#searchProdotti').length);
    console.log('Elemento searchMalfunzionamenti trovato:', $('#searchMalfunzionamenti').length);
    console.log('Form prodotti trovato:', $('#searchProdotti').closest('form').length);
    console.log('Form malfunzionamenti trovato:', $('#searchMalfunzionamenti').closest('form').length);

    // === GESTIONE RICERCA PRODOTTI ===
    // Gestione form ricerca prodotti - intercetta SOLO per validazione
    $('#searchProdotti').closest('form').on('submit', function(e) {
        const form = $(this);
        const input = form.find('input[name="search"]');
        const query = input.val().trim();
        
        console.log('Form ricerca prodotti submitted, query:', query);
        
        // Validazione lunghezza minima
        if (query.length < 2) {
            e.preventDefault();
            showAlert('Inserisci almeno 2 caratteri per la ricerca', 'warning');
            input.focus();
            return false;
        }
        
        // Validazione caratteri non consentiti
        const validazione = validaTermineRicerca(query);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // Mostra indicatore di caricamento sul bottone
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input');
        
        console.log('Ricerca prodotti validata, form si submitterà normalmente');
        // Lascio che il form si submitti normalmente
        return true;
    });
    
    // === GESTIONE RICERCA MALFUNZIONAMENTI ===
    // Gestione form ricerca malfunzionamenti - intercetta SOLO per validazione
    $('#searchMalfunzionamenti').closest('form').on('submit', function(e) {
        const form = $(this);
        const input = form.find('input[name="q"]');
        const query = input.val().trim();
        
        console.log('Form ricerca malfunzionamenti submitted, query:', query);
        
        // Validazione lunghezza minima
        if (query.length < 2) {
            e.preventDefault();
            showAlert('Inserisci almeno 2 caratteri per cercare malfunzionamenti', 'warning');
            input.focus();
            return false;
        }
        
        // Validazione caratteri non consentiti
        const validazione = validaTermineRicerca(query, 2);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // Mostra indicatore di caricamento sul bottone
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input');
        
        console.log('Ricerca malfunzionamenti validata, form si submitterà normalmente');
        // Lascio che il form si submitti normalmente
        return true;
    });

    // === RICERCA CON ENTER (manteniamo per usabilità) ===
    
    // Gestione ricerca prodotti con ENTER
    $('#searchProdotti').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca prodotti, query:', query);
            
            // Validazione prima del submit
            const validazione = validaTermineRicerca(query);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            // Mostra indicatore di caricamento
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            // Submit manuale del form
            console.log('Ricerca prodotti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // Gestione ricerca malfunzionamenti con ENTER
    $('#searchMalfunzionamenti').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca malfunzionamenti, query:', query);
            
            // Validazione prima del submit (2 caratteri minimi per malfunzionamenti)
            const validazione = validaTermineRicerca(query, 2);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            // Mostra indicatore di caricamento
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            // Submit manuale del form
            console.log('Ricerca malfunzionamenti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // === VALIDAZIONE RICERCA ===
    // Funzione helper per validare i termini di ricerca
    function validaTermineRicerca(query, lunghezzaMinima = 2) {
        // Controllo lunghezza minima
        if (!query || query.length < lunghezzaMinima) {
            return {
                valido: false,
                messaggio: `Inserisci almeno ${lunghezzaMinima} caratteri per la ricerca`
            };
        }
        
        // Controlli aggiuntivi per caratteri speciali pericolosi
        if (/[<>]/.test(query)) {
            return {
                valido: false,
                messaggio: 'Caratteri non ammessi nella ricerca: < >'
            };
        }
        
        // Controllo lunghezza massima per evitare query troppo lunghe
        if (query.length > 100) {
            return {
                valido: false,
                messaggio: 'Termine di ricerca troppo lungo (max 100 caratteri)'
            };
        }
        
        return { valido: true, messaggio: '' };
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
    
    // === GESTIONE EVENTI GENERALI ===
    
    // Focus automatico sull'input di ricerca quando si arriva alla pagina
    setTimeout(function() {
        $('#searchProdotti').focus();
        console.log('Focus automatico su searchProdotti');
    }, 500);
    
    // Suggerimenti visivi per ricerca con wildcard
    $('#searchProdotti, #searchMalfunzionamenti').on('focus', function() {
        $(this).next('.form-text').addClass('text-primary');
        console.log('Focus su input ricerca:', this.id);
    }).on('blur', function() {
        $(this).next('.form-text').removeClass('text-primary');
        console.log('Blur su input ricerca:', this.id);
    });
    
    // Validazione in tempo reale per caratteri non consentiti
    $('#searchProdotti, #searchMalfunzionamenti').on('input', function() {
        const query = $(this).val();
        const hasInvalidChars = /[<>]/.test(query);
        
        if (hasInvalidChars) {
            $(this).addClass('is-invalid');
            showErrorTooltip(this, 'Caratteri non ammessi: < >');
        } else {
            $(this).removeClass('is-invalid');
            $(this).tooltip('dispose');
        }
    });
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        console.log('Segnalazione richiesta per malfunzionamento ID:', malfunzionamentoId);
        
        if (!malfunzionamentoId) {
            showAlert('Errore: ID malfunzionamento non valido', 'danger');
            return;
        }
        
        // Richiedi conferma dall'utente prima di procedere
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            console.log('Segnalazione annullata dall\'utente');
            return;
        }
        
        // Trova il pulsante e disabilitalo temporaneamente per evitare doppi click
        const $button = $(`[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        if (!$button.length) {
            console.error('Pulsante segnalazione non trovato per ID:', malfunzionamentoId);
            showAlert('Errore: pulsante non trovato', 'danger');
            return;
        }
        
        const originalContent = $button.html();
        $button.prop('disabled', true)
               .removeClass('btn-outline-warning')
               .addClass('btn-secondary')
               .html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...');
        
        // Esegui la chiamata AJAX per registrare la segnalazione
        $.ajax({
            url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify({}), // Body vuoto ma JSON valido per Laravel
            timeout: 10000, // Timeout di 10 secondi
            success: function(response) {
                console.log('Risposta segnalazione:', response);
                
                if (response.success) {
                    // Mostra messaggio di successo
                    showAlert('Segnalazione registrata con successo!', 'success');
                    
                    // Aggiorna il contatore visibile nella tabella
                    const $counter = $(`#count-${malfunzionamentoId}`);
                    if ($counter.length) {
                        const nuovoCount = response.nuovo_count || (parseInt($counter.text()) + 1);
                        $counter.text(nuovoCount);
                        
                        // Aggiungi animazione di feedback per mostrare l'aggiornamento
                        $counter.addClass('badge-updated');
                        setTimeout(() => {
                            $counter.removeClass('badge-updated');
                        }, 2000);
                    }
                    
                    // Cambia il pulsante per mostrare successo
                    $button.removeClass('btn-secondary btn-outline-warning')
                           .addClass('btn-success')
                           .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                           .prop('disabled', true);
                    
                    console.log(`Segnalazione registrata per malfunzionamento ${malfunzionamentoId}. Nuovo conteggio: ${response.nuovo_count}`);
                } else {
                    throw new Error(response.message || 'Errore nella risposta del server');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore segnalazione AJAX:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento';
                
                // Gestione errori specifici
                if (xhr.status === 0) {
                    errorMsg = 'Errore di connessione. Controlla la rete.';
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato';
                } else if (xhr.status === 429) {
                    errorMsg = 'Troppi tentativi. Riprova tra qualche minuto';
                } else if (xhr.status === 500) {
                    errorMsg = 'Errore interno del server';
                } else {
                    // Prova a estrarre messaggio di errore dalla risposta
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        // Se la risposta non è JSON valida, usa il messaggio di default
                        console.warn('Risposta non JSON:', xhr.responseText);
                    }
                }
                
                showAlert(errorMsg, 'danger');
                
                // Ripristina il pulsante originale
                $button.removeClass('btn-secondary')
                       .addClass('btn-outline-warning')
                       .prop('disabled', false)
                       .html(originalContent);
            }
        });
    };
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type = 'info') {
        const alertId = 'alert-' + Date.now();
        const icons = {
            success: 'bi-check-circle',
            danger: 'bi-exclamation-triangle',
            warning: 'bi-exclamation-circle',
            info: 'bi-info-circle'
        };
        
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-sm" 
                 role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; min-width: 300px;">
                <i class="${icons[type] || icons.info} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Inserisci l'alert nel body per mostrarlo come toast
        $('body').append(alertHtml);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(function() {
            const $alert = $(`#${alertId}`);
            if ($alert.length) {
                $alert.fadeOut(500, function() {
                    $(this).remove();
                });
            }
        }, 5000);
    }
    
    // === AGGIORNAMENTO STATISTICHE PERIODICO ===
    function aggiornaStatistiche() {
        console.log('Aggiornamento statistiche...');
        
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
                    // Usa selettori più specifici basati sul contenuto delle card
                    $('h5:contains("' + (stats.total_prodotti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-box-seam').length) {
                            updateStat(this, stats.total_prodotti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_malfunzionamenti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-tools').length) {
                            updateStat(this, stats.total_malfunzionamenti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.malfunzionamenti_critici || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-exclamation-triangle').length) {
                            updateStat(this, stats.malfunzionamenti_critici);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_centri || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-geo-alt').length) {
                            updateStat(this, stats.total_centri);
                        }
                    });
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
    
    // === SHORTCUTS DA TASTIERA ===
    $(document).on('keydown', function(e) {
        // Solo se non siamo già dentro un input per evitare conflitti
        if (!$(e.target).is('input, textarea')) {
            
            // Ctrl + F = Focus su ricerca prodotti
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                $('#searchProdotti').focus().select();
                console.log('Shortcut Ctrl+F: Focus su ricerca prodotti');
            }
            
            // Ctrl + M = Focus su ricerca malfunzionamenti  
            if (e.ctrlKey && e.key === 'm') {
                e.preventDefault();
                $('#searchMalfunzionamenti').focus().select();
                console.log('Shortcut Ctrl+M: Focus su ricerca malfunzionamenti');
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
            
            // Ctrl + S = Focus su segnalazione rapida (se presente)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const $firstSegnalaBtn = $('.segnala-btn').first();
                if ($firstSegnalaBtn.length) {
                    $firstSegnalaBtn.focus();
                    console.log('Shortcut Ctrl+S: Focus su primo pulsante segnalazione');
                }
            }
        }
    });
    
    // === INIZIALIZZAZIONE TOOLTIP E ANIMAZIONI ===
    
    // Inizializza tooltip Bootstrap per tutti gli elementi con data-bs-toggle="tooltip"
    $('[data-bs-toggle="tooltip"]').tooltip();
    console.log('Tooltip inizializzati per', $('[data-bs-toggle="tooltip"]').length, 'elementi');
    
    // Effetto hover migliorato per le card con animazione smooth
    $('.card.card-custom').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // Animazione click per pulsanti
    $('.btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('btn-clicked');
        setTimeout(() => {
            $btn.removeClass('btn-clicked');
        }, 200);
    });
    
    // === FUNZIONI DI DEBUG E TESTING ===
    
    // Test delle connessioni API per verificare che tutto funzioni
    function testConnessioniAPI() {
        console.log('🧪 Test delle connessioni API...');
        
        // Test API statistiche
        $.get(API_URLS.stats_dashboard)
            .done(() => console.log('✅ API Statistiche: OK'))
            .fail((xhr) => console.log('❌ API Statistiche: ERRORE', xhr.status));
    }
    
    // === GESTIONE RESPONSIVE ===
    
    // Adatta layout per dispositivi mobili
    function handleResponsiveLayout() {
        const isMobile = window.innerWidth < 768;
        console.log('Layout responsive:', isMobile ? 'mobile' : 'desktop');
        
        if (isMobile) {
            // Nasconde alcuni elementi non essenziali su mobile
            $('.form-text').addClass('d-none');
            
            // Riduce il padding delle card
            $('.card-body').addClass('p-2');
            
            // Semplifica i tooltip
            $('[data-bs-toggle="tooltip"]').tooltip('disable');
        } else {
            // Ripristina layout desktop
            $('.form-text').removeClass('d-none');
            $('.card-body').removeClass('p-2');
            $('[data-bs-toggle="tooltip"]').tooltip('enable');
        }
    }
    
    // Chiama al caricamento e al resize
    handleResponsiveLayout();
    $(window).on('resize', handleResponsiveLayout);
    
    // === GESTIONE ERRORI GLOBALI ===
    
    // Intercetta errori AJAX globali per logging
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        console.error('Errore AJAX Globale:', {
            url: ajaxSettings.url,
            status: jqXHR.status,
            statusText: jqXHR.statusText,
            responseText: jqXHR.responseText,
            error: thrownError
        });
        
        // Mostra errore solo per chiamate importanti (non statistiche)
        if (!ajaxSettings.url.includes('/stats/')) {
            showAlert('Si è verificato un errore di connessione. Riprova tra qualche momento.', 'danger');
        }
    });
    
    // === CLEANUP E FINALIZZAZIONE ===
    
    // Pulizia quando si lascia la pagina per evitare memory leaks
    $(window).on('beforeunload', function() {
        if (typeof statsUpdateInterval !== 'undefined') {
            clearInterval(statsUpdateInterval);
        }
        
        // Rimuovi tutti i tooltip attivi
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
    });
    
    // === INIZIALIZZAZIONE COMPLETATA ===
    console.log('✅ Dashboard Tecnico inizializzata completamente');
    console.log('🔧 URLs API configurati:', API_URLS);
    console.log('🚀 Funzioni disponibili:', {
        'segnalaMalfunzionamento()': 'Segnala un problema riscontrato',
        'testConnessioniAPI()': 'Test delle connessioni API',
        'aggiornaStatistiche()': 'Forza aggiornamento statistiche',
        'validaTermineRicerca()': 'Valida termine di ricerca'
    });
    
    // Esponi funzioni per debugging nella console del browser
    window.dashboardTecnico = {
        testAPI: testConnessioniAPI,
        updateStats: aggiornaStatistiche,
        showAlert: showAlert,
        validateSearch: validaTermineRicerca,
        urls: API_URLS,
        version: '2.1.0'
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
    
    // Logging delle metriche di utilizzo per analytics
    const sessionStart = Date.now();
    window.addEventListener('beforeunload', function() {
        const sessionDuration = Date.now() - sessionStart;
        console.log('Sessione Dashboard Tecnico:', {
            durata: Math.round(sessionDuration / 1000) + ' secondi',
            utente: '{{ auth()->user()->username }}',
            timestamp: new Date().toISOString()
        });
    });
    
    // === TEST FINALE FUNZIONALITÀ ===
    // Dopo 1 secondo dall'inizializzazione, testa le funzionalità principali
    setTimeout(function() {
        console.log('=== TEST FUNZIONALITÀ DASHBOARD ===');
        
        // Test presenza elementi chiave
        const elementi = {
            'Input ricerca prodotti': $('#searchProdotti').length,
            'Input ricerca malfunzionamenti': $('#searchMalfunzionamenti').length,
            'Bottone ricerca prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').length,
            'Bottone ricerca malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').length,
            'Pulsanti segnalazione': $('.segnala-btn').length,
            'Card statistiche': $('.card.border-0.shadow-sm').length
        };
        
        console.table(elementi);
        
        // Verifica se gli event handler sono stati attaccati
        const eventiAttaccati = {
            'Click bottone prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Click bottone malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Keypress prodotti': $('#searchProdotti').data('events') ? 'SI' : 'NO',
            'Keypress malfunzionamenti': $('#searchMalfunzionamenti').data('events') ? 'SI' : 'NO'
        };
        
        console.table(eventiAttaccati);
        
        console.log('=== FINE TEST FUNZIONALITÀ ===');
    }, 1000);
});
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