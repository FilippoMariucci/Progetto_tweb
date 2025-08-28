{{--
    Dashboard Staff - Sistema Assistenza Tecnica
    
    Accessibile solo a utenti con livello_accesso >= 3 (Staff)
    Fornisce gestione completa di malfunzionamenti e soluzioni tecniche
    
    Route: GET /staff/dashboard
    Controller: AuthController@staffDashboard
    Middleware: auth, check.level:3
    
    Funzionalità:
    - Panoramica generale delle attività staff
    - Gestione malfunzionamenti e soluzioni (CRUD completo)
    - Accesso a prodotti assegnati (funzionalità opzionale)
    - Statistiche attività e report performance
    - Monitoraggio problemi critici del sistema
--}}

@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per lo staff --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-person-badge text-warning me-2"></i>
                Pannello Staff Aziendale
            </h1>
            
            {{-- Benvenuto personalizzato per staff --}}
            <div class="alert alert-warning border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-tools display-6 text-warning me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Benvenuto, {{ auth()->user()->nome_completo }}!</h4>
                        <p class="mb-0">
                            <span class="badge bg-warning text-dark">Staff Tecnico Aziendale</span>
                            @if(auth()->user()->specializzazione)
                                <span class="badge bg-light text-dark ms-1">{{ auth()->user()->specializzazione }}</span>
                            @endif
                        </p>
                        <small class="text-muted">
                            Gestione completa malfunzionamenti e soluzioni per tutti i prodotti aziendali
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- === GESTIONE PRINCIPALE STAFF === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-wrench-adjustable me-2"></i>
                        Strumenti Staff
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Gestione malfunzionamenti (principale) --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-tools display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestisci Malfunzionamenti</span>
                            </a>
                        </div>
                        
                        {{-- Prodotti assegnati (funzionalità opzionale) --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-info btn-lg w-100 h-100">
                                <i class="bi bi-box-seam display-6 d-block mb-2"></i>
                                <span class="fw-semibold">I Miei Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Catalogo completo tecnico --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-success btn-lg w-100 h-100">
                                <i class="bi bi-collection display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Tecnico</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche staff --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('staff.statistiche') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Le Mie Stats</span>
                            </a>
                        </div>
                        
                        {{-- Report attività --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('staff.report.attivita') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-file-text display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Report Attività</span>
                            </a>
                        </div>
                        
                        {{-- Ricerca globale malfunzionamenti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-warning btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Cerca Soluzioni</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE STAFF === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Personali
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            {{-- Malfunzionamenti gestiti --}}
                            @if(isset($stats['malfunzionamenti_gestiti']))
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        <i class="bi bi-tools text-warning fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['malfunzionamenti_gestiti'] }}</h4>
                                        <small class="text-muted">Gestiti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Soluzioni create --}}
                            @if(isset($stats['soluzioni_create']))
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        <i class="bi bi-lightbulb text-success fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['soluzioni_create'] }}</h4>
                                        <small class="text-muted">Soluzioni</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Prodotti assegnati --}}
                            @if(isset($stats['prodotti_assegnati']))
                                <div class="col-6">
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        <i class="bi bi-box text-info fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['prodotti_assegnati'] }}</h4>
                                        <small class="text-muted">Assegnati</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Problemi risolti questo mese --}}
                            @if(isset($stats['risolti_mese']))
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-check-circle text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['risolti_mese'] }}</h4>
                                        <small class="text-muted">Questo Mese</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Messaggio quando non ci sono statistiche --}}
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Caricamento statistiche staff...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === RICERCA RAPIDA STAFF === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida Staff
                        <small class="text-muted">(gestione completa malfunzionamenti e soluzioni)</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Ricerca prodotti per gestione --}}
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('prodotti.completo.ricerca') }}" class="d-flex">
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="search" 
                                       placeholder="Cerca prodotti da gestire: lav*, frigo*, condizionatore..."
                                       value="{{ request('search') }}"
                                       id="searchProdottiStaff">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-box-seam"></i>
                                </button>
                            </form>
                            <div class="form-text">
                                Trova prodotti per aggiungere/modificare malfunzionamenti
                            </div>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti per modifica --}}
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}" class="d-flex">
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="q" 
                                       placeholder="Cerca malfunzionamenti: non si accende, perdita..."
                                       value="{{ request('q') }}"
                                       id="searchMalfunzionamentiStaff">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-tools"></i>
                                </button>
                            </form>
                            <div class="form-text">
                                Trova malfunzionamenti esistenti per modifica/gestione
                            </div>
                        </div>
                    </div>
                    
                    {{-- Suggerimenti per staff --}}
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Accesso Staff:</strong>
                            <span class="badge bg-light text-dark me-1">Crea nuovi</span>
                            <span class="badge bg-light text-dark me-1">Modifica esistenti</span>
                            <span class="badge bg-light text-dark me-1">Gestisci soluzioni</span>
                            <span class="badge bg-light text-dark me-1">Report completi</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MALFUNZIONAMENTI PRIORITARI DA GESTIRE === --}}
    @if(isset($malfunzionamenti_prioritari) && $malfunzionamenti_prioritari->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Malfunzionamenti Prioritari - Richiedono Intervento Staff
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($malfunzionamenti_prioritari->take(6) as $malfunzionamento)
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-warning">
                                    <div class="card-body">
                                        <h6 class="card-title text-danger">
                                            {{ $malfunzionamento->titolo }}
                                            <small class="text-muted d-block">{{ $malfunzionamento->prodotto->nome }}</small>
                                        </h6>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-danger">{{ $malfunzionamento->gravita }}</span>
                                            <span class="badge bg-primary">{{ $malfunzionamento->segnalazioni_count }} segnalazioni</span>
                                        </div>
                                        
                                        <p class="card-text small">
                                            <strong>Categoria:</strong> {{ ucfirst(str_replace('_', ' ', $malfunzionamento->prodotto->categoria)) }}
                                        </p>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil me-1"></i>Gestisci Soluzione
                                            </a>
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye me-1"></i>Visualizza Dettagli
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Link per gestire tutti i prioritari --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('staff.malfunzionamenti.index') }}?filter=prioritari" class="btn btn-danger">
                            <i class="bi bi-list me-1"></i>Gestisci Tutti i Prioritari
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === PRODOTTI ASSEGNATI (Funzionalità Opzionale) === --}}
    @if(isset($prodotti_assegnati) && $prodotti_assegnati->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check me-2"></i>
                        I Tuoi Prodotti Assegnati - Gestione Dedicata
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Categoria</th>
                                    <th>Malfunzionamenti</th>
                                    <th>Ultimo Aggiornamento</th>
                                    <th class="text-center">Azioni Staff</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prodotti_assegnati->take(5) as $prodotto)
                                    <tr>
                                        <td>
                                            <strong>{{ $prodotto->nome }}</strong>
                                            @if($prodotto->modello)
                                                <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $prodotto->malfunzionamenti_count ?? 0 }} totali</span>
                                            @if(($prodotto->critici_count ?? 0) > 0)
                                                <span class="badge bg-danger ms-1">{{ $prodotto->critici_count }} critici</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prodotto->ultimo_aggiornamento)
                                                {{ \Carbon\Carbon::parse($prodotto->ultimo_aggiornamento)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('staff.prodotti.assegnati') }}?prodotto={{ $prodotto->id }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Gestisci malfunzionamenti">
                                                    <i class="bi bi-tools"></i>
                                                </a>
                                                <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Aggiungi nuovo malfunzionamento">
                                                    <i class="bi bi-plus-circle"></i>
                                                </a>
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Vista tecnica completa">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-info">
                            <i class="bi bi-list me-1"></i>Vedi Tutti i Prodotti Assegnati
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === ULTIME ATTIVITÀ STAFF === --}}
    @if(isset($ultime_attivita) && $ultime_attivita->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Le Tue Ultime Attività
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Azione</th>
                                    <th>Prodotto/Malfunzionamento</th>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultime_attivita->take(10) as $attivita)
                                    <tr>
                                        <td>
                                            @if($attivita->tipo_azione == 'create')
                                                <span class="badge bg-success">Creato</span>
                                            @elseif($attivita->tipo_azione == 'update')
                                                <span class="badge bg-warning text-dark">Modificato</span>
                                            @elseif($attivita->tipo_azione == 'delete')
                                                <span class="badge bg-danger">Eliminato</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($attivita->tipo_azione) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($attivita->descrizione, 40) }}</strong>
                                            @if($attivita->prodotto_nome)
                                                <br><small class="text-muted">Prodotto: {{ $attivita->prodotto_nome }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $attivita->tipo_oggetto == 'malfunzionamento' ? 'Malfunzionamento' : 'Soluzione' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $attivita->created_at->format('d/m H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === SUPPORTO E LINEE GUIDA STAFF === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-custom border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Linee Guida per Staff
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Crea soluzioni dettagliate e complete per ogni malfunzionamento
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Indica sempre il livello di difficoltà e il tempo stimato
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Utilizza terminologia tecnica precisa e comprensibile
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Assegna la gravità corretta per prioritizzare gli interventi
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Monitora le segnalazioni dei tecnici per migliorare le soluzioni
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-custom border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Supporto Staff
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Responsabile Tecnico:</span>
                        <a href="mailto:responsabile.tecnico@sistemaassistenza.it" class="text-decoration-none">
                            responsabile.tecnico@sistemaassistenza.it
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Interno Staff:</span>
                        <a href="tel:+39071220444" class="text-decoration-none">
                            +39 071 220 4444
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Supervisore:</span>
                        <span class="text-muted">Ext. 4401-4405</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a href="{{ route('contatti') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-headset me-1"></i>Assistenza
                        </a>
                        @if(auth()->user()->livello_accesso >= 4)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm ms-1">
                                <i class="bi bi-gear me-1"></i>Admin Panel
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MESSAGGI CONDIZIONALI === --}}
    @if(!isset($stats) || (isset($stats['malfunzionamenti_gestiti']) && $stats['malfunzionamenti_gestiti'] == 0))
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border border-2 border-warning">
                    <div class="text-center">
                        <i class="bi bi-info-circle display-4 text-warning mb-3"></i>
                        <h4>Benvenuto nel Sistema Staff!</h4>
                        <p class="text-muted mb-3">
                            Come membro dello staff aziendale, hai accesso completo alla gestione di malfunzionamenti e soluzioni.
                            Puoi creare, modificare ed eliminare problemi per tutti i prodotti del catalogo.
                            @if(auth()->user()->livello_accesso >= 4)
                                In qualità di amministratore, puoi anche gestire l'assegnazione dei prodotti ai membri dello staff.
                            @endif
                        </p>
                        {{-- Collegamenti di partenza --}}
                        <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-warning me-2">
                            <i class="bi bi-tools me-2"></i>
                            Inizia Gestione Malfunzionamenti
                        </a>
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-warning me-2">
                            <i class="bi bi-collection me-2"></i>
                            Esplora Catalogo Tecnico
                        </a>
                        @if(isset($stats) && isset($stats['prodotti_assegnati']) && $stats['prodotti_assegnati'] > 0)
                            <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-info">
                                <i class="bi bi-person-check me-2"></i>
                                I Tuoi Prodotti Assegnati
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// =====================================================
// DASHBOARD STAFF - JavaScript Avanzato
// Gestione completa per funzionalità staff aziendale
// =====================================================

$(document).ready(function() {
    // === CONFIGURAZIONE GLOBALE STAFF ===
    console.log('Dashboard Staff caricata per: {{ auth()->user()->nome_completo }}');
    
    // URLs per API staff (basate sulle route corrette)
    const STAFF_API_URLS = {
        // API specifiche per staff
        stats_staff: '{{ route("api.stats") }}',
        ultime_soluzioni: '{{ route("api.ultime-soluzioni") }}',
        malfunzionamenti_prioritari: '{{ route("api.malfunzionamenti-prioritari") }}',
        prodotti_assegnati: '{{ route("api.prodotti-assegnati") }}',
        
        // API condivise con tecnici
        prodotti_search: '{{ route("api.prodotti.search.tech") }}',
        malfunzionamenti_search: '{{ route("api.malfunzionamenti.search") }}',
        
        // Gestione malfunzionamenti (CRUD staff)
        malfunzionamenti_crud_base: '{{ url("/staff/malfunzionamenti") }}'
    };
    
    // Token CSRF per tutte le richieste
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    // Configurazione AJAX con autenticazione
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    // === RICERCA AVANZATA STAFF ===
    let staffSearchTimeout;
    
    // Ricerca prodotti per gestione staff
    $('#searchProdottiStaff').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(staffSearchTimeout);
        
        if (query.length >= 2) {
            $(this).addClass('loading-input');
            
            staffSearchTimeout = setTimeout(() => {
                cercaProdottiStaff(query);
            }, 300);
        } else {
            hideStaffSuggestions();
        }
    });
    
    // Ricerca malfunzionamenti per gestione/modifica
    $('#searchMalfunzionamentiStaff').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(staffSearchTimeout);
        
        if (query.length >= 3) {
            $(this).addClass('loading-input');
            
            staffSearchTimeout = setTimeout(() => {
                cercaMalfunzionamentiStaff(query);
            }, 400);
        } else {
            hideStaffMalfunzionamentoSuggestions();
        }
    });
    
    // === FUNZIONI RICERCA STAFF ===
    function cercaProdottiStaff(query) {
        $.ajax({
            url: STAFF_API_URLS.prodotti_search,
            method: 'GET',
            data: { 
                q: query,
                limit: 8,
                staff_mode: true // Flag per modalità staff
            },
            success: function(response) {
                $('#searchProdottiStaff').removeClass('loading-input');
                
                if (response.success && response.data && response.data.length > 0) {
                    mostraStaffProdottiSuggestions(response.data, '#searchProdottiStaff');
                    console.log(`Staff: Trovati ${response.data.length} prodotti per "${query}"`);
                } else {
                    hideStaffSuggestions();
                }
            },
            error: function(xhr, status, error) {
                $('#searchProdottiStaff').removeClass('loading-input');
                console.error('Errore ricerca prodotti staff:', error);
                showStaffAlert('Errore nella ricerca prodotti', 'warning');
                hideStaffSuggestions();
            }
        });
    }
    
    function cercaMalfunzionamentiStaff(query) {
        $.ajax({
            url: STAFF_API_URLS.malfunzionamenti_search,
            method: 'GET',
            data: { 
                q: query,
                limit: 6,
                staff_access: true, // Accesso completo staff
                include_management: true // Include azioni di gestione
            },
            success: function(response) {
                $('#searchMalfunzionamentiStaff').removeClass('loading-input');
                
                if (response.success && response.data && response.data.length > 0) {
                    mostraStaffMalfunzionamentoSuggestions(response.data, '#searchMalfunzionamentiStaff');
                    console.log(`Staff: Trovati ${response.data.length} malfunzionamenti per "${query}"`);
                } else {
                    hideStaffMalfunzionamentoSuggestions();
                }
            },
            error: function(xhr, status, error) {
                $('#searchMalfunzionamentiStaff').removeClass('loading-input');
                console.error('Errore ricerca malfunzionamenti staff:', error);
                showStaffAlert('Errore nella ricerca malfunzionamenti', 'warning');
                hideStaffMalfunzionamentoSuggestions();
            }
        });
    }
    
    // === SUGGERIMENTI PRODOTTI STAFF ===
    function mostraStaffProdottiSuggestions(risultati, targetInput) {
        let html = '<div class="list-group position-absolute staff-product-suggestions" style="z-index: 1000; max-height: 350px; overflow-y: auto; width: 100%; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 0.375rem;">';
        
        risultati.forEach(function(prodotto) {
            // Indicatori per staff: quanti malfunzionamenti gestisce
            const gestioneInfo = prodotto.is_assigned_to_user ? 
                '<i class="bi bi-person-check text-success ms-1" title="Assegnato a te"></i>' : '';
            
            const malfunzionamentiInfo = prodotto.malfunzionamenti_count > 0 ? 
                `<span class="badge bg-primary me-1">${prodotto.malfunzionamenti_count}</span>` : 
                '<span class="badge bg-secondary me-1">0</span>';
            
            const prioritaInfo = prodotto.critici_count > 0 ? 
                `<span class="badge bg-danger">${prodotto.critici_count} critici</span>` : 
                '<span class="badge bg-success">OK</span>';
            
            html += `
                <div class="list-group-item list-group-item-action staff-suggestion">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                ${prodotto.nome} ${gestioneInfo}
                                ${prodotto.modello ? `<small class="text-muted">- ${prodotto.modello}</small>` : ''}
                            </h6>
                            ${prodotto.descrizione ? `<p class="mb-1 text-muted small">${prodotto.descrizione}</p>` : ''}
                            <div class="d-flex gap-2 mt-2">
                                <a href="${prodotto.management_url || '#'}" class="btn btn-warning btn-xs">
                                    <i class="bi bi-tools me-1"></i>Gestisci
                                </a>
                                <a href="${prodotto.add_malfunction_url || '#'}" class="btn btn-success btn-xs">
                                    <i class="bi bi-plus me-1"></i>Nuovo
                                </a>
                                <a href="${prodotto.url}" class="btn btn-info btn-xs">
                                    <i class="bi bi-eye me-1"></i>Vedi
                                </a>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">${malfunzionamentiInfo}</div>
                            <div>${prioritaInfo}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        hideStaffSuggestions();
        $(targetInput).parent().addClass('position-relative').append(html);
    }
    
    // === SUGGERIMENTI MALFUNZIONAMENTI STAFF ===
    function mostraStaffMalfunzionamentoSuggestions(risultati, targetInput) {
        let html = '<div class="list-group position-absolute staff-malfunction-suggestions" style="z-index: 1000; max-height: 350px; overflow-y: auto; width: 100%; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 0.375rem;">';
        
        risultati.forEach(function(malfunzionamento) {
            const graviColor = {
                'critica': 'danger',
                'alta': 'warning', 
                'media': 'info',
                'bassa': 'secondary'
            };
            
            const badgeColor = graviColor[malfunzionamento.gravita] || 'secondary';
            
            // Informazioni per staff: chi l'ha creato, quando modificato
            const staffInfo = malfunzionamento.created_by_user ? 
                `<small class="text-muted">Creato da: ${malfunzionamento.created_by_user}</small>` : '';
            
            const lastUpdate = malfunzionamento.updated_at ? 
                `<small class="text-muted ms-2">Agg: ${malfunzionamento.updated_at}</small>` : '';
            
            html += `
                <div class="list-group-item list-group-item-action staff-suggestion">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${malfunzionamento.titolo}</h6>
                            <p class="mb-1 text-muted small">${malfunzionamento.descrizione}</p>
                            <small><strong>Prodotto:</strong> ${malfunzionamento.prodotto_nome}</small>
                            <div>${staffInfo} ${lastUpdate}</div>
                            <div class="d-flex gap-2 mt-2">
                                <a href="${malfunzionamento.edit_url || '#'}" class="btn btn-warning btn-xs">
                                    <i class="bi bi-pencil me-1"></i>Modifica
                                </a>
                                <a href="${malfunzionamento.url}" class="btn btn-info btn-xs">
                                    <i class="bi bi-eye me-1"></i>Visualizza
                                </a>
                                <button onclick="eliminaMalfunzionamento(${malfunzionamento.id})" class="btn btn-danger btn-xs">
                                    <i class="bi bi-trash me-1"></i>Elimina
                                </button>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">
                                <span class="badge bg-${badgeColor}">${malfunzionamento.gravita}</span>
                            </div>
                            <div>
                                <span class="badge bg-primary">${malfunzionamento.segnalazioni || 0}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        hideStaffMalfunzionamentoSuggestions();
        $(targetInput).parent().addClass('position-relative').append(html);
    }
    
    // === NASCONDI SUGGERIMENTI STAFF ===
    function hideStaffSuggestions() {
        $('.staff-product-suggestions').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    function hideStaffMalfunzionamentoSuggestions() {
        $('.staff-malfunction-suggestions').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    // === GESTIONE MALFUNZIONAMENTI STAFF ===
    
    // Funzione per eliminare malfunzionamento (solo staff)
    window.eliminaMalfunzionamento = function(malfunzionamentoId) {
        if (!confirm('ATTENZIONE: Eliminerai definitivamente questo malfunzionamento e la sua soluzione. Questa azione non può essere annullata. Continuare?')) {
            return;
        }
        
        const $button = $(`[onclick="eliminaMalfunzionamento(${malfunzionamentoId})"]`);
        $button.prop('disabled', true).addClass('loading');
        
        $.ajax({
            url: `${STAFF_API_URLS.malfunzionamenti_crud_base}/${malfunzionamentoId}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    showStaffAlert('Malfunzionamento eliminato con successo', 'success');
                    
                    // Rimuovi elemento dalla vista
                    $button.closest('.staff-suggestion, tr').fadeOut(500, function() {
                        $(this).remove();
                    });
                    
                    // Aggiorna statistiche
                    aggiornaStatisticheStaff();
                } else {
                    showStaffAlert('Errore nell\'eliminazione: ' + (response.message || 'Errore sconosciuto'), 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore eliminazione malfunzionamento:', xhr.responseText);
                
                let errorMsg = 'Errore nell\'eliminazione del malfunzionamento.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                
                showStaffAlert(errorMsg, 'danger');
            },
            complete: function() {
                $button.prop('disabled', false).removeClass('loading');
            }
        });
    };
    
    // === AGGIORNAMENTO STATISTICHE STAFF ===
    function aggiornaStatisticheStaff() {
        $.ajax({
            url: STAFF_API_URLS.stats_staff,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Statistiche staff aggiornate:', response.data);
                    
                    const stats = response.data;
                    
                    // Aggiorna contatori con animazione
                    function updateStaffStat(className, newValue) {
                        $(`.${className}`).each(function() {
                            const $el = $(this);
                            if ($el.text() !== newValue.toString()) {
                                $el.addClass('updating');
                                setTimeout(() => {
                                    $el.text(newValue).removeClass('updating');
                                }, 300);
                            }
                        });
                    }
                    
                    if (stats.malfunzionamenti_gestiti !== undefined) {
                        updateStaffStat('stats-gestiti', stats.malfunzionamenti_gestiti);
                    }
                    if (stats.soluzioni_create !== undefined) {
                        updateStaffStat('stats-soluzioni', stats.soluzioni_create);
                    }
                    if (stats.prodotti_assegnati !== undefined) {
                        updateStaffStat('stats-assegnati', stats.prodotti_assegnati);
                    }
                    if (stats.risolti_mese !== undefined) {
                        updateStaffStat('stats-mese', stats.risolti_mese);
                    }
                }
            },
            error: function(xhr) {
                console.warn('Aggiornamento statistiche staff fallito:', xhr.status);
            }
        });
    }
    
    // === CARICAMENTO CONTENUTI DINAMICI ===
    function caricaMalfunzionamentiPrioritari() {
        $.ajax({
            url: STAFF_API_URLS.malfunzionamenti_prioritari,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    aggiornaMalfunzionamentiPrioritari(response.data);
                }
            },
            error: function(xhr) {
                console.warn('Caricamento malfunzionamenti prioritari fallito');
            }
        });
    }
    
    function caricaProdottiAssegnati() {
        $.ajax({
            url: STAFF_API_URLS.prodotti_assegnati,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    aggiornaProdottiAssegnati(response.data);
                }
            },
            error: function(xhr) {
                console.warn('Caricamento prodotti assegnati fallito');
            }
        });
    }
    
    function caricaUltimeSoluzioni() {
        $.ajax({
            url: STAFF_API_URLS.ultime_soluzioni,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    aggiornaUltimeSoluzioni(response.data);
                }
            },
            error: function(xhr) {
                console.warn('Caricamento ultime soluzioni fallito');
            }
        });
    }
    
    // === FUNZIONI AGGIORNAMENTO UI ===
    function aggiornaMalfunzionamentiPrioritari(data) {
        // Implementa aggiornamento della sezione malfunzionamenti prioritari
        console.log('Aggiornamento malfunzionamenti prioritari:', data.length);
    }
    
    function aggiornaProdottiAssegnati(data) {
        // Implementa aggiornamento della tabella prodotti assegnati
        console.log('Aggiornamento prodotti assegnati:', data.length);
    }
    
    function aggiornaUltimeSoluzioni(data) {
        // Implementa aggiornamento della sezione ultime attività
        console.log('Aggiornamento ultime soluzioni:', data.length);
    }
    
    // === ALERT PERSONALIZZATI STAFF ===
    function showStaffAlert(message, type) {
        const alertId = 'staff-alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show staff-alert" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 450px; min-width: 300px;">
                <strong>Staff:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-remove dopo 6 secondi per messaggi staff
        setTimeout(function() {
            $(`#${alertId}`).fadeOut(500, function() {
                $(this).remove();
            });
        }, 6000);
    }
    
    // === SHORTCUTS STAFF ===
    $(document).on('keydown', function(e) {
        if (!$('input, textarea, select').is(':focus')) {
            switch(e.key) {
                case '1':
                    if (e.ctrlKey && e.shiftKey) {
                        e.preventDefault();
                        window.location.href = '{{ route("staff.malfunzionamenti.index") }}';
                    }
                    break;
                case '2':
                    if (e.ctrlKey && e.shiftKey) {
                        e.preventDefault();
                        window.location.href = '{{ route("staff.prodotti.assegnati") }}';
                    }
                    break;
                case '3':
                    if (e.ctrlKey && e.shiftKey) {
                        e.preventDefault();
                        window.location.href = '{{ route("staff.statistiche") }}';
                    }
                    break;
                case 's':
                    if (e.ctrlKey && e.altKey) {
                        e.preventDefault();
                        $('#searchProdottiStaff').focus().select();
                    }
                    break;
                case 'm':
                    if (e.ctrlKey && e.altKey) {
                        e.preventDefault();
                        $('#searchMalfunzionamentiStaff').focus().select();
                    }
                    break;
                case 'r':
                    if (e.ctrlKey && e.shiftKey) {
                        e.preventDefault();
                        aggiornaStatisticheStaff();
                        showStaffAlert('Statistiche aggiornate manualmente', 'info');
                    }
                    break;
            }
        }
    });
    
    // === GESTIONE EVENTI GENERALI ===
    
    // Nascondi suggerimenti quando si clicca fuori
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchProdottiStaff, #searchMalfunzionamentiStaff, .staff-product-suggestions, .staff-malfunction-suggestions').length) {
            hideStaffSuggestions();
            hideStaffMalfunzionamentoSuggestions();
        }
    });
    
    // Navigazione con tastiera nei suggerimenti
    $('#searchProdottiStaff, #searchMalfunzionamentiStaff').on('keydown', function(e) {
        const $suggestions = $(this).parent().find('.staff-product-suggestions, .staff-malfunction-suggestions');
        const $activeItem = $suggestions.find('.staff-suggestion.active');
        
        switch(e.which) {
            case 13: // Enter
                if ($activeItem.length) {
                    const $firstLink = $activeItem.find('a').first();
                    if ($firstLink.length) {
                        window.location.href = $firstLink.attr('href');
                    }
                } else {
                    $(this).closest('form').submit();
                }
                break;
                
            case 38: // Freccia su
                e.preventDefault();
                if ($activeItem.length) {
                    $activeItem.removeClass('active').prev('.staff-suggestion').addClass('active');
                } else {
                    $suggestions.find('.staff-suggestion').last().addClass('active');
                }
                break;
                
            case 40: // Freccia giù
                e.preventDefault();
                if ($activeItem.length) {
                    $activeItem.removeClass('active').next('.staff-suggestion').addClass('active');
                } else {
                    $suggestions.find('.staff-suggestion').first().addClass('active');
                }
                break;
                
            case 27: // Escape
                hideStaffSuggestions();
                hideStaffMalfunzionamentoSuggestions();
                break;
        }
    });
    
    // === AGGIORNAMENTI AUTOMATICI ===
    
    // Aggiorna statistiche ogni 2 minuti
    const statsInterval = setInterval(aggiornaStatisticheStaff, 2 * 60 * 1000);
    
    // Ricarica contenuti dinamici ogni 5 minuti
    const contentInterval = setInterval(function() {
        caricaMalfunzionamentiPrioritari();
        caricaProdottiAssegnati();
        caricaUltimeSoluzioni();
    }, 5 * 60 * 1000);
    
    // === TOOLTIP E ANIMAZIONI ===
    
    // Inizializza tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Hover effects per card
    $('.card.card-custom').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-3px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // === CLEANUP ===
    $(window).on('beforeunload', function() {
        if (typeof statsInterval !== 'undefined') {
            clearInterval(statsInterval);
        }
        if (typeof contentInterval !== 'undefined') {
            clearInterval(contentInterval);
        }
    });
    
    // === INIZIALIZZAZIONE COMPLETATA ===
    console.log('✅ Dashboard Staff inizializzata completamente');
    console.log('🔧 URLs API Staff configurati:', STAFF_API_URLS);
    console.log('⌨️ Shortcuts disponibili:', {
        'Ctrl+Shift+1': 'Gestione malfunzionamenti',
        'Ctrl+Shift+2': 'Prodotti assegnati',
        'Ctrl+Shift+3': 'Statistiche',
        'Ctrl+Alt+S': 'Focus ricerca prodotti',
        'Ctrl+Alt+M': 'Focus ricerca malfunzionamenti',
        'Ctrl+Shift+R': 'Aggiorna statistiche'
    });
    
    // Esponi funzioni per debugging
    window.dashboardStaff = {
        updateStats: aggiornaStatisticheStaff,
        loadPriorityItems: caricaMalfunzionamentiPrioritari,
        loadAssignedProducts: caricaProdottiAssegnati,
        loadRecentSolutions: caricaUltimeSoluzioni,
        urls: STAFF_API_URLS,
        version: '2.0.0'
    };
    
    // Messaggio di benvenuto staff (solo primo accesso giornaliero)
    const oggi = new Date().toDateString();
    const ultimoAccessoStaff = localStorage.getItem('ultimo_accesso_dashboard_staff');
    
    if (ultimoAccessoStaff !== oggi) {
        setTimeout(function() {
            showStaffAlert('Benvenuto nella Dashboard Staff! Usa Ctrl+Shift+shortcuts per navigazione rapida.', 'info');
            localStorage.setItem('ultimo_accesso_dashboard_staff', oggi);
        }, 2000);
    }
    
    // Caricamento iniziale contenuti dinamici
    setTimeout(function() {
        aggiornaStatisticheStaff();
        caricaMalfunzionamentiPrioritari();
        caricaProdottiAssegnati();
        caricaUltimeSoluzioni();
    }, 1000);
});
</script>
@endpush

@push('styles')
<style>
/* =====================================================
   DASHBOARD STAFF - CSS Avanzato
   Stili specifici per funzionalità staff aziendale
   ===================================================== */

/* === STILI BASE STAFF === */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

/* === COLORI TEMA STAFF (Warning/Arancione) === */
.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffc107;
    color: #856404;
}

.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
}

.card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%) !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

/* === STILI RICERCA STAFF === */
.loading-input {
    background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%23ffc107'%3e%3ccircle cx='10' cy='10' r='1'%3e%3canimate attributeName='r' begin='0s' dur='1.8s' values='1; 4; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.165, 0.84, 0.44, 1; 0.3, 0.61, 0.355, 1' repeatCount='indefinite'/%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
    padding-right: 40px;
    border-color: #ffc107;
}

/* === SUGGERIMENTI STAFF === */
.staff-product-suggestions,
.staff-malfunction-suggestions {
    border: 2px solid #ffc107;
    border-radius: 0.5rem;
    box-shadow: 0 0.75rem 1.5rem rgba(255, 193, 7, 0.2);
    background: #fff;
    max-height: 400px;
    overflow-y: auto;
}

.staff-suggestion {
    border: none !important;
    border-bottom: 1px solid #fff3cd !important;
    transition: all 0.2s ease;
    padding: 1rem;
}

.staff-suggestion:hover,
.staff-suggestion.active {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffc107 !important;
    color: #856404;
}

.staff-suggestion:last-child {
    border-bottom: none !important;
}

/* === PULSANTI STAFF === */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.65rem;
    border-radius: 0.25rem;
    font-weight: 500;
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    border-color: #ffc107;
    color: #000;
    font-weight: 600;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ffb300 0%, #ff8f00 100%);
    border-color: #ffb300;
    color: #000;
    transform: translateY(-1px);
}

.btn.loading::after {
    border-top-color: #ffc107;
}

/* === STATISTICHE STAFF === */
.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
    border: 1px solid rgba(255, 193, 7, 0.2);
    transition: all 0.3s ease;
}

.bg-warning.bg-opacity-10:hover {
    background-color: rgba(255, 193, 7, 0.15) !important;
    border-color: rgba(255, 193, 7, 0.3);
}

.updating {
    animation: staff-pulse 1s infinite ease-in-out;
    color: #ffc107 !important;
}

@keyframes staff-pulse {
    0% { opacity: 0.7; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.05); }
    100% { opacity: 0.7; transform: scale(1); }
}

/* === TABELLE STAFF === */
.table-hover tbody tr:hover {
    background-color: rgba(255, 193, 7, 0.1);
    transition: background-color 0.2s ease;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

/* === BADGE STAFF === */
.badge.bg-warning.text-dark {
    background-color: #ffc107 !important;
    color: #000 !important;
    font-weight: 600;
    text-shadow: none;
}

.badge.bg-danger {
    animation: priority-pulse 2s infinite ease-in-out;
}

@keyframes priority-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* === CARD PRIORITARIE === */
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
    background: linear-gradient(45deg, #dc3545, #ffc107, #dc3545);
    border-radius: 0.375rem;
    z-index: -1;
    opacity: 0.1;
    animation: border-glow-staff 3s ease-in-out infinite;
}

@keyframes border-glow-staff {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 0.2; }
}

.border-warning {
    border-color: #ffc107 !important;
    border-width: 2px !important;
}

.border-info {
    border-color: #0dcaf0 !important;
    border-width: 2px !important;
}

/* === HEADER CARD COLORATI === */
.card-header.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header.bg-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #17a2b8 100%) !important;
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header.bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-bottom: 1px solid #dee2e6;
}

/* === ALERT STAFF === */
.staff-alert {
    border-left: 4px solid #ffc107;
    border-radius: 0.5rem;
    animation: slideInFromRight 0.5s ease-out;
    box-shadow: 0 0.5rem 1rem rgba(255, 193, 7, 0.2);
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert-success.staff-alert {
    border-left-color: #198754;
    box-shadow: 0 0.5rem 1rem rgba(25, 135, 84, 0.2);
}

.alert-danger.staff-alert {
    border-left-color: #dc3545;
    box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.2);
}

.alert-info.staff-alert {
    border-left-color: #0dcaf0;
    box-shadow: 0 0.5rem 1rem rgba(13, 202, 240, 0.2);
}

/* === EFFETTI HOVER AVANZATI === */
.card-custom::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #ffc107, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card-custom:hover::after {
    opacity: 0.6;
}

/* === ICONE ANIMATE === */
.btn i {
    transition: transform 0.2s ease;
}

.btn:hover i {
    transform: scale(1.1) rotate(5deg);
}

.btn-danger:hover i {
    transform: scale(1.1) rotate(-5deg);
}

.card-header i {
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
    transition: transform 0.3s ease;
}

.card:hover .card-header i {
    transform: scale(1.05);
}

/* === SCROLLBAR PERSONALIZZATE === */
.staff-product-suggestions::-webkit-scrollbar,
.staff-malfunction-suggestions::-webkit-scrollbar {
    width: 8px;
}

.staff-product-suggestions::-webkit-scrollbar-track,
.staff-malfunction-suggestions::-webkit-scrollbar-track {
    background: #fff3cd;
    border-radius: 4px;
}

.staff-product-suggestions::-webkit-scrollbar-thumb,
.staff-malfunction-suggestions::-webkit-scrollbar-thumb {
    background: #ffc107;
    border-radius: 4px;
}

.staff-product-suggestions::-webkit-scrollbar-thumb:hover,
.staff-malfunction-suggestions::-webkit-scrollbar-thumb:hover {
    background: #ffb300;
}

/* === STATI LOADING === */
.loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top: 2px solid #ffc107;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === MIGLIORAMENTI TIPOGRAFICI === */
.fw-semibold {
    font-weight: 600;
}

.small, small {
    font-size: 0.875rem;
    color: #6c757d;
}

h4, h5, h6 {
    font-weight: 600;
}

.text-muted {
    color: #6c757d !important;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-custom {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
    }
    
    .display-6 {
        font-size: 1.8rem;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
    
    /* Suggerimenti più compatti su mobile */
    .staff-suggestion {
        padding: 0.75rem;
    }
    
    .staff-suggestion h6 {
        font-size: 0.9rem;
    }
    
    .staff-suggestion .btn-xs {
        font-size: 0.6rem;
        padding: 0.1rem 0.3rem;
    }
    
    /* Alert responsive */
    .staff-alert {
        position: static !important;
        margin: 0.5rem;
        width: auto !important;
        max-width: none !important;
    }
    
    /* Nasconde elementi non essenziali */
    .badge.bg-light.text-dark {
        display: none;
    }
}

@media (max-width: 576px) {
    .col-lg-4, .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .row.g-4 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
    
    .table th, .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg.w-100.h-100 {
        min-height: 80px;
        padding: 0.5rem;
    }
}

/* === ACCESSIBILITÀ === */
.btn:focus,
.form-control:focus {
    outline: 2px solid #ffc107;
    outline-offset: 2px;
}

.staff-suggestion:focus {
    outline: 2px solid #ffc107;
    outline-offset: -2px;
    background-color: rgba(255, 193, 7, 0.15);
}

/* Miglior contrasto per testi */
.text-muted {
    color: #495057 !important;
}

/* === DARK MODE SUPPORT (se implementato) === */
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #212529;
        color: #fff;
    }
    
    .staff-suggestion {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .staff-suggestion:hover {
        background-color: #4a5568;
    }
    
    .table {
        color: #e2e8f0;
    }
    
    .table th {
        background-color: #2d3748;
        border-color: #4a5568;
    }
}

/* === PRINT STYLES === */
@media print {
    .staff-product-suggestions,
    .staff-malfunction-suggestions,
    .staff-alert {
        display: none !important;
    }
    
    .card-custom,
    .btn,
    .badge {
        box-shadow: none !important;
        background: white !important;
        color: black !important;
        border: 1px solid #000 !important;
    }
    
    .btn {
        text-decoration: underline;
    }
    
    .table {
        border-collapse: collapse !important;
    }
    
    .table th,
    .table td {
        border: 1px solid #000 !important;
        background: white !important;
        color: black !important;
    }
}

/* === ANIMAZIONI AVANZATE === */
.card-custom,
.btn,
.form-control,
.badge,
.alert,
.staff-suggestion {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Effetto ondulatorio sui pulsanti */
.btn:active {
    animation: ripple 0.3s ease-out;
}

@keyframes ripple {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
    }
    50% {
        transform: scale(0.98);
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0.2);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

/* === MIGLIORAMENTI VISUAL === */
.shadow-staff {
    box-shadow: 0 0.5rem 1rem rgba(255, 193, 7, 0.15) !important;
}

.border-staff {
    border-color: #ffc107 !important;
}

.bg-staff-light {
    background-color: #fff3cd !important;
}

.text-staff {
    color: #856404 !important;
}

/* === UTILITÀ STAFF === */
.staff-priority {
    position: relative;
}

.staff-priority::before {
    content: '!';
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    animation: pulse 1s infinite;
}

.staff-new {
    position: relative;
}

.staff-new::after {
    content: 'NEW';
    position: absolute;
    top: 0;
    right: 0;
    background: #198754;
    color: white;
    font-size: 0.6rem;
    padding: 0.1rem 0.3rem;
    border-radius: 0.2rem;
    font-weight: bold;
}

/* === LAYOUT MIGLIORAMENTI === */
.sticky-top-custom {
    position: sticky;
    top: 100px;
    z-index: 100;
}

.overflow-hidden-custom {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* === TRANSIZIONI SMOOTH === */
.smooth-transition {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* === FOCUS RING PERSONALIZZATO === */
.focus-ring-staff:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

/* === SEPARATORI === */
hr.staff-divider {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffc107, transparent);
    margin: 2rem 0;
    opacity: 0.3;
}

/* === COMPLETAMENTO CSS === */
.dashboard-staff-loaded {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.5s ease-out;
}

.dashboard-staff-loading {
    opacity: 0;
    transform: translateY(20px);
}
</style>
@endpush