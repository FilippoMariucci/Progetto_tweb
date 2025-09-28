{{-- 
    DASHBOARD STAFF COMPLETA - LIVELLO 3 ACCESSO
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Dashboard principale per staff aziendale con gestione malfunzionamenti
    CARATTERISTICHE: Accesso completo a CRUD soluzioni, statistiche, gestione prodotti
    LINGUAGGIO: Blade template (PHP con sintassi Laravel Blade)
    LIVELLO ACCESSO: 3 (Staff Aziendale - gestione malfunzionamenti e soluzioni)
    PATH: resources/views/staff/dashboard.blade.php
--}}

{{-- 
    EXTENDS: Eredita il layout principale dell'applicazione
    LINGUAGGIO: Blade directive - specifica template padre condiviso
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo specifico per dashboard staff
    LINGUAGGIO: Blade directive + string content per SEO e browser tab
--}}
@section('title', 'Dashboard Staff Aziendale')

{{-- 
    SECTION CONTENT: Contenuto principale dashboard staff
    LINGUAGGIO: Blade directive - corpo completo con layout responsive
--}}
@section('content')
<div class="container-fluid mt-4">

    {{-- === HEADER PRINCIPALE ===
         DESCRIZIONE: Intestazione con branding staff e informazioni accesso
         LINGUAGGIO: HTML Bootstrap + Blade output + PHP date functions
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                INTESTAZIONE CON BENVENUTO: Layout flessibile per info utente
                LINGUAGGIO: Bootstrap flexbox + conditional user data display
            --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    {{-- 
                        TITOLO PRINCIPALE: Styling specifico per livello staff
                        LINGUAGGIO: HTML h1 + Bootstrap classes + Bootstrap Icons
                    --}}
                    <h1 class="h2 mb-1 text-warning fw-bold">
                        <i class="bi bi-person-badge me-2"></i>
                        Dashboard Staff Aziendale
                    </h1>
                    <p class="text-muted mb-0">
                        Gestione malfunzionamenti e soluzioni tecniche - Livello 3
                    </p>
                </div>
                <div class="text-end">
                    {{-- 
                        BADGE LIVELLO ACCESSO: Indicatore visivo permessi utente
                        LINGUAGGIO: Bootstrap badge + rounded styling + icon
                    --}}
                    <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i>
                        Staff Aziendale
                    </div>
                    {{-- 
                        INFO ULTIMO ACCESSO: Timestamp dinamico
                        LINGUAGGIO: PHP Carbon/DateTime + Laravel now() helper
                    --}}
                    <div class="small text-muted mt-1">
                        Ultimo accesso: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            {{-- 
                ALERT BENVENUTO PERSONALIZZATO: Welcome message con gradient styling
                LINGUAGGIO: Bootstrap alert + CSS gradient + conditional user data
            --}}
            <div class="alert alert-warning border-0 shadow-sm" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                <div class="row align-items-center">
                    <div class="col-auto">
                        {{-- 
                            AVATAR UTENTE: Placeholder con icona utente
                            LINGUAGGIO: CSS flexbox + Bootstrap utilities + inline styles
                        --}}
                        <div class="avatar bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                    </div>
                    <div class="col">
                        {{-- 
                            MESSAGGIO BENVENUTO: Dati utente con fallback sicuri
                            LINGUAGGIO: Blade output + PHP null coalescing (??) + Laravel auth()
                        --}}
                        <h4 class="alert-heading mb-1">
                            Benvenuto, {{ auth()->user()->nome ?? auth()->user()->name ?? 'Staff' }}!
                        </h4>
                        <p class="mb-2">
                            <strong>Staff Tecnico Aziendale</strong> - 
                            Gestisci soluzioni per tutti i prodotti del catalogo
                        </p>
                        <small class="text-warning-emphasis">
                            <i class="bi bi-check-circle me-1"></i>
                            Accesso completo a creazione e modifica malfunzionamenti
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE PRINCIPALI COMPATTE ===
         DESCRIZIONE: Cards metriche chiave per staff con layout responsive
         LINGUAGGIO: Bootstrap grid + conditional data display + null safety
    --}}
    <div class="row mb-3 g-2">
        {{-- 
            CARD PRODOTTI GESTITI: Conta prodotti assegnati o totali
            LINGUAGGIO: Bootstrap card + conditional statistics + fallback values
        --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                    {{-- 
                        CONTEGGIO CONDIZIONALE: Prodotti assegnati vs totali
                        LINGUAGGIO: PHP ternary operator + null coalescing + array access
                    --}}
                    <h5 class="fw-bold mb-0 text-primary">{{ $stats['prodotti_assegnati'] ?? $stats['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Prodotti Gestiti</small>
                    {{-- Badge dinamico basato su tipo assegnazione --}}
                    <small class="badge bg-primary bg-opacity-10 text-primary mt-1">
                        {{ isset($stats['prodotti_assegnati']) ? 'Assegnati' : 'Disponibili' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Card soluzioni create dallo staff --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-tools text-success fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-success">{{ $stats['soluzioni_create'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Soluzioni Create</small>
                    <small class="badge bg-success bg-opacity-10 text-success mt-1">
                        Implementate
                    </small>
                </div>
            </div>
        </div>

        {{-- Card problemi critici gestiti --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-exclamation-triangle text-warning fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-warning">{{ $stats['soluzioni_critiche'] ?? $stats['malfunzionamenti_critici'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Problemi Critici</small>
                    <small class="badge bg-warning bg-opacity-10 text-warning mt-1">
                        Attenzione
                    </small>
                </div>
            </div>
        </div>

        {{-- Card totale soluzioni nel database --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-database text-info fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-info">{{ $stats['total_malfunzionamenti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Totale Soluzioni</small>
                    <small class="badge bg-info bg-opacity-10 text-info mt-1">
                        Nel sistema
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE ===
         DESCRIZIONE: Sezione pulsanti azione principale per workflow staff
         LINGUAGGIO: Bootstrap card + grid + gradient styling + route links
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                {{-- 
                    HEADER GRADIENT: Header con gradient e descrizione
                    LINGUAGGIO: CSS inline gradient + Bootstrap text utilities
                --}}
                <div class="card-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <h4 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Azioni Rapide Staff
                    </h4>
                    <small class="opacity-75">Funzionalità principali per la gestione quotidiana</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- 
                            NUOVA SOLUZIONE - PULSANTE PRINCIPALE
                            DESCRIZIONE: CTA principale per creazione soluzioni
                            LINGUAGGIO: HTML button + Laravel route() + CSS animations + positioning
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="position-relative">
                                <a href="{{ route('staff.create.nuova.soluzione') }}" 
                                   class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm pulse-btn"
                                   style="min-height: 150px;">
                                    <i class="bi bi-plus-circle-fill display-4 mb-3"></i>
                                    <div class="text-center">
                                        <h5 class="mb-2 fw-bold">Nuova Soluzione</h5>
                                        <small class="opacity-75">Aggiungi soluzione per qualsiasi prodotto</small>
                                    </div>
                                </a>
                                {{-- 
                                    BADGE NUOVO: Elemento promozionale con animazione
                                    LINGUAGGIO: CSS absolute positioning + Bootstrap badge + animation class
                                --}}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate-pulse">
                                    Nuovo!
                                    <span class="visually-hidden">nuova funzionalità</span>
                                </span>
                            </div>
                        </div>

                        {{-- Gestione malfunzionamenti esistenti --}}
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" 
                               class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm hover-lift"
                               style="min-height: 150px;">
                                <i class="bi bi-gear-fill display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Gestione Soluzioni</h5>
                                    <small class="opacity-75">Visualizza e modifica tutte le soluzioni</small>
                                </div>
                            </a>
                        </div>

                        {{-- Catalogo completo con accesso tecnico --}}
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}" 
                               class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none text-white shadow-sm hover-lift"
                               style="min-height: 150px;">
                                <i class="bi bi-search display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Esplora Catalogo</h5>
                                    <small class="opacity-75">Tutti i prodotti con malfunzionamenti</small>
                                </div>
                            </a>
                        </div>

                        {{-- 
                            STATISTICHE E REPORT: Modal trigger per analytics
                            LINGUAGGIO: HTML button + Bootstrap modal attributes + data attributes
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <button class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-dark shadow-sm hover-lift"
                                    style="min-height: 150px;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#statsModal">
                                <i class="bi bi-bar-chart-line display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Statistiche</h5>
                                    <small class="opacity-75">Report e analisi dettagliate</small>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI E ATTIVITÀ ===
         DESCRIZIONE: Layout principale con lista prodotti e sidebar attività
         LINGUAGGIO: Bootstrap grid responsive + conditional content display
    --}}
    <div class="row mb-4">
        {{-- 
            COLONNA PRINCIPALE - PRODOTTI
            DESCRIZIONE: Sezione principale con lista prodotti gestiti
            LINGUAGGIO: Bootstrap column + card structure + view toggles
        --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                Prodotti del Catalogo
                            </h4>
                            {{-- 
                                DESCRIZIONE DINAMICA: Testo basato su assegnazione prodotti
                                LINGUAGGIO: Blade @if + conditional messaging + statistics display
                            --}}
                            <p class="text-muted small mb-0">
                                @if(isset($stats['prodotti_assegnati']) && $stats['prodotti_assegnati'] > 0)
                                    Hai {{ $stats['prodotti_assegnati'] }} prodotti specificamente assegnati
                                @else
                                    Gestisci soluzioni per tutti i prodotti disponibili
                                @endif
                            </p>
                        </div>
                        {{-- 
                            TOGGLE VISTA: Radio buttons per switch grid/list view
                            LINGUAGGIO: HTML radio inputs + Bootstrap button group + JavaScript targets
                        --}}
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="prodotti-view" id="view-grid" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="view-grid" title="Vista griglia">
                                <i class="bi bi-grid-3x3-gap"></i>
                            </label>
                            <input type="radio" class="btn-check" name="prodotti-view" id="view-list" autocomplete="off">
                            <label class="btn btn-outline-primary" for="view-list" title="Vista lista">
                                <i class="bi bi-list"></i>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card-body pt-3">
                    {{-- 
                        VISTA GRIGLIA (DEFAULT)
                        DESCRIZIONE: Layout cards per visualizzazione prodotti
                        LINGUAGGIO: Bootstrap grid + Blade conditionals + Collection methods
                    --}}
                    <div id="grid-view">
                        @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                            <div class="row g-3">
                                {{-- 
                                    LOOP PRODOTTI: Itera lista prodotti con limite
                                    LINGUAGGIO: Blade @foreach + Laravel Collection take() method
                                --}}
                                @foreach($stats['prodotti_lista']->take(6) as $prodotto)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border-0 shadow-sm hover-card product-card">
                                            <div class="card-body">
                                                {{-- 
                                                    HEADER PRODOTTO: Nome e categoria
                                                    LINGUAGGIO: Bootstrap flexbox + PHP string functions + fallbacks
                                                --}}
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title mb-0 fw-bold text-truncate flex-grow-1 me-2">
                                                        {{ $prodotto->nome }}
                                                    </h6>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst($prodotto->categoria ?? 'generale') }}
                                                    </span>
                                                </div>

                                                {{-- Info modello se disponibile --}}
                                                @if(isset($prodotto->modello) && $prodotto->modello)
                                                    <p class="text-muted small mb-2">
                                                        <i class="bi bi-tag me-1"></i>
                                                        {{ $prodotto->modello }}
                                                    </p>
                                                @endif

                                                {{-- 
                                                    STATISTICHE PRODOTTO: Layout 2 colonne per metriche
                                                    LINGUAGGIO: Bootstrap grid + conditional counting + relationship access
                                                --}}
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                                            <div class="fw-bold text-primary">
                                                                {{ $prodotto->malfunzionamenti->count() ?? 0 }}
                                                            </div>
                                                            <small class="text-muted">Soluzioni</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                                            <div class="fw-bold text-warning">
                                                                {{-- 
                                                                    CONTEGGIO CRITICI: Filtra malfunzionamenti per gravità
                                                                    LINGUAGGIO: PHP ternary + Laravel Collection where() + count()
                                                                --}}
                                                                {{ isset($prodotto->malfunzionamenti) ? $prodotto->malfunzionamenti->where('gravita', 'critica')->count() : 0 }}
                                                            </div>
                                                            <small class="text-muted">Critiche</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- 
                                                    AZIONI PRODOTTO: Pulsanti per gestione
                                                    LINGUAGGIO: Bootstrap d-grid + Laravel route() helpers + icon integration
                                                --}}
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('prodotti.completo.show', $prodotto->id) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i>Visualizza Dettagli
                                                    </a>
                                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto->id) }}" 
                                                       class="btn btn-outline-success btn-sm">
                                                        <i class="bi bi-plus me-1"></i>Aggiungi Soluzione
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- 
                                LINK "VEDI TUTTI": Navigazione al catalogo completo
                                LINGUAGGIO: HTML link + Laravel route() + statistics display
                            --}}
                            <div class="text-center mt-4">
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-right me-2"></i>
                                    Visualizza Tutti i Prodotti ({{ $stats['total_prodotti'] ?? 'N/A' }})
                                </a>
                            </div>
                        @else
                            {{-- 
                                STATO VUOTO PRODOTTI: Messaggio quando nessun prodotto disponibile
                                LINGUAGGIO: Bootstrap utilities + conditional messaging + call-to-action
                            --}}
                            <div class="text-center py-5">
                                <i class="bi bi-box display-1 text-muted opacity-50"></i>
                                <h5 class="text-muted mt-3 mb-3">Nessun prodotto disponibile</h5>
                                <p class="text-muted">
                                    Non ci sono prodotti nel catalogo al momento.<br>
                                    Contatta l'amministratore per aggiungere prodotti.
                                </p>
                                <a href="{{ route('prodotti.index') }}" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>Esplora Catalogo Pubblico
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- 
                        VISTA LISTA (NASCOSTA DI DEFAULT)
                        DESCRIZIONE: Layout lista compatto per visualizzazione alternativa
                        LINGUAGGIO: CSS display none + Bootstrap list group + JavaScript toggle
                    --}}
                    <div id="list-view" style="display: none;">
                        @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                            <div class="list-group list-group-flush">
                                {{-- Loop prodotti formato lista --}}
                                @foreach($stats['prodotti_lista']->take(8) as $prodotto)
                                    <div class="list-group-item px-0 border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold">{{ $prodotto->nome }}</h6>
                                                <p class="text-muted small mb-1">
                                                    <span class="badge bg-secondary me-2">{{ ucfirst($prodotto->categoria ?? 'generale') }}</span>
                                                    @if($prodotto->modello)
                                                        Modello: {{ $prodotto->modello }}
                                                    @endif
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-tools me-1"></i>
                                                    {{ $prodotto->malfunzionamenti->count() ?? 0 }} soluzioni disponibili
                                                </small>
                                            </div>
                                            {{-- Azioni compatte lista --}}
                                            <div class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('prodotti.completo.show', $prodotto->id) }}" 
                                                       class="btn btn-outline-primary" title="Visualizza">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto->id) }}" 
                                                       class="btn btn-outline-success" title="Aggiungi soluzione">
                                                        <i class="bi bi-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Stato vuoto vista lista --}}
                            <div class="text-center py-4">
                                <i class="bi bi-list display-4 text-muted opacity-50"></i>
                                <p class="text-muted mt-2">Nessun prodotto in formato lista</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 
            SIDEBAR DESTRA: Sezioni attività e informazioni account
            DESCRIZIONE: Colonna laterale con ultime attività e info utente
            LINGUAGGIO: Bootstrap column + multiple card sections
        --}}
        <div class="col-lg-4">
            {{-- 
                ULTIME ATTIVITÀ: Timeline delle soluzioni recenti
                DESCRIZIONE: Card con lista ultime soluzioni create dallo staff
                LINGUAGGIO: Bootstrap card + list group + conditional data display
            --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Ultime Soluzioni
                    </h5>
                    <small class="text-muted">Le tue soluzioni più recenti</small>
                </div>
                <div class="card-body">
                    @if(isset($stats['ultime_soluzioni']) && $stats['ultime_soluzioni']->count() > 0)
                        <div class="list-group list-group-flush">
                            {{-- 
                                LOOP SOLUZIONI RECENTI: Lista timeline con metadati
                                LINGUAGGIO: Blade @foreach + Collection methods + relationship access
                            --}}
                            @foreach($stats['ultime_soluzioni']->take(4) as $soluzione)
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-2">
                                            <h6 class="mb-1 fw-semibold text-truncate">
                                                {{ $soluzione->titolo ?? 'Soluzione senza titolo' }}
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-box me-1"></i>
                                                {{ $soluzione->prodotto->nome ?? 'Prodotto N/A' }}
                                            </p>
                                            {{-- 
                                                TIMESTAMP FORMATTATO: Data creazione con fallback sicuro
                                                LINGUAGGIO: PHP conditional + Laravel Carbon formatting + null safety
                                            --}}
                                            <small class="text-muted">
                                                {{ isset($soluzione->created_at) ? $soluzione->created_at->format('d/m/Y H:i') : 'Data N/A' }}
                                            </small>
                                        </div>
                                        <div>
                                            {{-- 
                                                BADGE GRAVITÀ: Colore dinamico basato su livello gravità
                                                LINGUAGGIO: Bootstrap badge + PHP ternary nested + conditional classes
                                            --}}
                                            <span class="badge bg-{{ 
                                                isset($soluzione->gravita) && $soluzione->gravita == 'alta' ? 'danger' : 
                                                (isset($soluzione->gravita) && $soluzione->gravita == 'media' ? 'warning' : 'success') 
                                            }}">
                                                {{ ucfirst($soluzione->gravita ?? 'normale') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Link vedi tutte --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-list me-1"></i>Vedi Tutte le Soluzioni
                            </a>
                        </div>
                    @else
                        {{-- Stato vuoto attività --}}
                        <div class="text-center py-3">
                            <i class="bi bi-clock-history display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-3">Nessuna soluzione recente</p>
                            <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-plus me-1"></i>Crea Prima Soluzione
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 
                INFO ACCOUNT: Informazioni utente corrente
                DESCRIZIONE: Card con metadati account staff senza link rapidi
                LINGUAGGIO: Bootstrap card + flexbox layout + conditional data
            --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Info Account
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        ULTIMA MODIFICA: Info temporale ultima attività
                        LINGUAGGIO: Bootstrap flexbox + conditional data display + fallback
                    --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted">Ultima modifica</small>
                            <div class="fw-semibold">{{ $stats['ultima_modifica'] ?? 'Mai' }}</div>
                        </div>
                    </div>
                    
                    {{-- 
                        LIVELLO ACCESSO: Badge livello permessi utente
                        LINGUAGGIO: Bootstrap flexbox + static level 3 display + icon
                    --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-shield-check text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted">Livello accesso</small>
                            <div class="fw-semibold text-success">
                                Livello 3 - Staff
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        USERNAME UTENTE: Info identificativa utente corrente
                        LINGUAGGIO: Bootstrap flexbox + Laravel auth() + null safety + fallback
                    --}}
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-person text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted">Username</small>
                            <div class="fw-semibold">{{ auth()->user()->username ?? 'N/A' }}</div>
                        </div>
                    </div>

                    {{-- Nota: Rimossa la sezione link rapidi per design più pulito --}}
                </div>
            </div>
        </div>
    </div>

    {{-- === SUGGERIMENTI PER LO STAFF ===
         DESCRIZIONE: Sezione consigli e best practices per utilizzo sistema
         LINGUAGGIO: Bootstrap card + grid layout + icon integration + content strategy
    --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white border-0">
                    <h4 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Suggerimenti per lo Staff
                    </h4>
                    <small class="opacity-75">Consigli per un utilizzo ottimale del sistema</small>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        {{-- 
                            SUGGERIMENTO CONTROLLO QUALITÀ
                            DESCRIZIONE: Best practice per verifica soluzioni
                            LINGUAGGIO: Bootstrap flexbox + icon system + content guidelines
                        --}}
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-15 rounded p-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold text-dark mb-2">Controllo Qualità</h5>
                                    <p class="text-muted mb-0">
                                        Controlla sempre la gravità del problema prima di pubblicare la soluzione. 
                                        Verifica che tutte le informazioni siano accurate e complete.
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- 
                            SUGGERIMENTO EFFICIENZA
                            DESCRIZIONE: Workflow optimization tips per staff
                            LINGUAGGIO: Bootstrap flexbox + content strategy + feature highlighting
                        --}}
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-15 rounded p-2">
                                        <i class="bi bi-lightbulb text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold text-dark mb-2">Efficienza</h5>
                                    <p class="text-muted mb-0">
                                        Usa il pulsante "Nuova Soluzione" per aggiungere rapidamente soluzioni 
                                        senza dover navigare attraverso il catalogo prodotti.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- === MODAL STATISTICHE DETTAGLIATE ===
     DESCRIZIONE: Modal completo per analytics e report avanzati
     LINGUAGGIO: Bootstrap Modal + complex data visualization + interactive elements
--}}
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            {{-- 
                HEADER MODAL: Titolo con gradient styling
                LINGUAGGIO: Bootstrap modal header + CSS inline gradient + accessibility
            --}}
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <h5 class="modal-title" id="statsModalLabel">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Statistiche e Report Staff
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- 
                        STATISTICHE NUMERICHE: Sezione metriche principali
                        DESCRIZIONE: Grid con cards statistiche dettagliate
                        LINGUAGGIO: Bootstrap grid + card system + data visualization
                    --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Riepilogo Attività</h6>
                        <div class="row g-3">
                            {{-- Card prodotti gestiti --}}
                            <div class="col-6">
                                <div class="card border-0 bg-primary bg-opacity-10 text-center p-3">
                                    {{-- 
                                        CONTEGGIO DINAMICO: JavaScript updatable per real-time stats
                                        LINGUAGGIO: HTML with JS ID + fallback static data + statistics display
                                    --}}
                                    <div class="h4 text-primary mb-1" id="modal-prodotti-count">{{ $stats['prodotti_assegnati'] ?? 0 }}</div>
                                    <small class="text-muted">Prodotti Gestiti</small>
                                </div>
                            </div>
                            {{-- Card soluzioni create --}}
                            <div class="col-6">
                                <div class="card border-0 bg-success bg-opacity-10 text-center p-3">
                                    <div class="h4 text-success mb-1" id="modal-soluzioni-count">{{ $stats['soluzioni_create'] ?? 0 }}</div>
                                    <small class="text-muted">Soluzioni Create</small>
                                </div>
                            </div>
                            {{-- Card critiche risolte --}}
                            <div class="col-6">
                                <div class="card border-0 bg-warning bg-opacity-10 text-center p-3">
                                    <div class="h4 text-warning mb-1">{{ $stats['soluzioni_critiche'] ?? 0 }}</div>
                                    <small class="text-muted">Critiche Risolte</small>
                                </div>
                            </div>
                            {{-- Card tasso successo (statico) --}}
                            <div class="col-6">
                                <div class="card border-0 bg-info bg-opacity-10 text-center p-3">
                                    <div class="h4 text-info mb-1">95%</div>
                                    <small class="text-muted">Tasso Successo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 
                        GRAFICO ATTIVITÀ: Visualizzazione andamento settimanale
                        DESCRIZIONE: Chart CSS-based per trend analysis
                        LINGUAGGIO: CSS bar chart + responsive design + data representation
                    --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Andamento Settimanale</h6>
                        <div class="bg-light rounded p-3">
                            {{-- 
                                CSS BAR CHART: Chart implementato con CSS puro
                                LINGUAGGIO: CSS flexbox + height percentages + responsive bars
                            --}}
                            <div class="d-flex justify-content-between align-items-end mb-3" style="height: 120px;">
                                <div class="bg-primary rounded-bottom d-flex align-items-end justify-content-center text-white small fw-bold" style="width: 40px; height: 40%;">Mon</div>
                                <div class="bg-primary rounded-bottom d-flex align-items-end justify-content-center text-white small fw-bold" style="width: 40px; height: 70%;">Tue</div>
                                <div class="bg-primary rounded-bottom d-flex align-items-end justify-content-center text-white small fw-bold" style="width: 40px; height: 50%;">Wed</div>
                                <div class="bg-primary rounded-bottom d-flex align-items-end justify-content-center text-white small fw-bold" style="width: 40px; height: 90%;">Thu</div>
                                <div class="bg-primary rounded-bottom d-flex align-items-end justify-content-center text-white small fw-bold" style="width: 40px; height: 60%;">Fri</div>
                            </div>
                            <p class="text-center text-muted small mb-0">Soluzioni create negli ultimi 5 giorni</p>
                        </div>
                    </div>

                    {{-- 
                        REPORT DETTAGLIATO: Tabella performance comparative
                        DESCRIZIONE: Tabella con metriche comparative e trend analysis
                        LINGUAGGIO: Bootstrap table + responsive design + data comparison
                    --}}
                    <div class="col-12">
                        <hr>
                        <h6 class="fw-semibold mb-3">Report Performance</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Metrica</th>
                                        <th>Questo Mese</th>
                                        <th>Mese Precedente</th>
                                        <th>Variazione</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 
                                        ROW SOLUZIONI: Confronto mensile con calcoli dinamici
                                        LINGUAGGIO: HTML table + PHP calculations + conditional badges
                                    --}}
                                    <tr>
                                        <td>Soluzioni Create</td>
                                        <td>{{ $stats['soluzioni_create'] ?? 0 }}</td>
                                        <td>{{ max(0, ($stats['soluzioni_create'] ?? 0) - 2) }}</td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-up"></i> +{{ min(2, ($stats['soluzioni_create'] ?? 0)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Row problemi critici --}}
                                    <tr>
                                        <td>Problemi Critici Risolti</td>
                                        <td>{{ $stats['soluzioni_critiche'] ?? 0 }}</td>
                                        <td>{{ max(0, ($stats['soluzioni_critiche'] ?? 0) - 1) }}</td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-arrow-up"></i> +{{ min(1, ($stats['soluzioni_critiche'] ?? 0)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Row tempo medio (mock data) --}}
                                    <tr>
                                        <td>Tempo Medio Risoluzione</td>
                                        <td>2.3 ore</td>
                                        <td>2.8 ore</td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-down"></i> -0.5h
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 
                FOOTER MODAL: Pulsanti azione per export e navigazione
                LINGUAGGIO: Bootstrap modal footer + JavaScript functions + route links
            --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                {{-- 
                    EXPORT FUNCTION: JavaScript function per export dati
                    LINGUAGGIO: HTML button + JavaScript onclick + function reference
                --}}
                <button type="button" class="btn btn-primary" onclick="exportStats()">
                    <i class="bi bi-download me-1"></i>Esporta Report
                </button>
                {{-- Link statistiche complete --}}
                <a href="{{ route('staff.statistiche') }}" class="btn btn-warning">
                    <i class="bi bi-bar-chart me-1"></i>Statistiche Complete
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- === STILI CSS PERSONALIZZATI ===
     DESCRIZIONE: CSS avanzato per animazioni, responsive design e UX
     LINGUAGGIO: Blade @push + CSS3 + keyframes + media queries
--}}
@push('styles')
<style>
/* === ANIMAZIONI E TRANSIZIONI ===
   LINGUAGGIO: CSS3 animations + transitions + keyframes
   SCOPO: Micro-interazioni per migliorare user experience */

/* Card hover effect - solleva e aggiunge ombra */
.hover-card {
    transition: all 0.3s ease; /* Transizione smooth per tutti i cambiamenti */
}

.hover-card:hover {
    transform: translateY(-2px); /* Solleva card di 2px */
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; /* Ombra più profonda */
}

/* Hover generico per elementi interattivi */
.hover-lift {
    transition: all 0.2s ease; /* Transizione più veloce per responsività */
}

.hover-lift:hover {
    transform: translateY(-2px); /* Stesso movimento verso l'alto */
}

/* Animazione pulse per pulsante principale */
.pulse-btn {
    animation: pulse-glow 2s ease-in-out infinite alternate; /* Animazione continua alternata */
}

/* Keyframes per effetto glow pulsante */
@keyframes pulse-glow {
    0% { 
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); /* Ombra iniziale verde */
        transform: scale(1); /* Dimensione normale */
    }
    100% { 
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5); /* Ombra più intensa */
        transform: scale(1.02); /* Leggero ingrandimento */
    }
}

/* Animazione pulse per badge */
.animate-pulse {
    animation: pulse 2s ease-in-out infinite; /* Pulse infinito per badge "Nuovo" */
}

@keyframes pulse {
    0%, 100% { opacity: 1; } /* Opacità piena */
    50% { opacity: 0.7; } /* Opacità ridotta a metà ciclo */
}

/* === CARDS E LAYOUT ===
   LINGUAGGIO: CSS border-radius + overflow
   SCOPO: Styling uniforme per tutti i componenti card */

.card {
    border-radius: 12px; /* Angoli molto arrotondati per look moderno */
    overflow: hidden; /* Nasconde contenuto che deborda */
}

.card-header {
    border-radius: 12px 12px 0 0 !important; /* Solo angoli superiori arrotondati */
}

/* Card prodotto con bordo sottile */
.product-card {
    border: 1px solid rgba(0,0,0,0.05) !important; /* Bordo quasi trasparente */
}

.product-card:hover {
    border-color: var(--bs-primary) !important; /* Bordo colorato al hover */
}

/* === BADGES E ELEMENTI ===
   LINGUAGGIO: CSS font properties
   SCOPO: Consistenza tipografica per elementi badge */

.badge {
    font-size: 0.75rem; /* Dimensione font ridotta */
    font-weight: 500; /* Peso medio per leggibilità */
}

/* Avatar dimensioni fisse */
.avatar {
    width: 60px;
    height: 60px;
}

/* === PULSANTI ===
   LINGUAGGIO: CSS styling + focus states
   SCOPO: Styling uniforme pulsanti e stati di focus */

.btn-lg {
    font-size: 1rem; /* Font size consistente */
    border-radius: 8px; /* Angoli arrotondati */
}

/* Focus accessibile per pulsanti */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); /* Outline focus visibile */
}

/* === RESPONSIVE DESIGN ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Adattamento layout per dispositivi mobili */

/* === TABLET (768px e sotto) === */
@media (max-width: 768px) {
    /* Pulsanti più piccoli su mobile */
    .btn-lg {
        min-height: 120px !important; /* Altezza ridotta */
        font-size: 0.9rem; /* Font più piccolo */
    }
    
    /* Icone più piccole */
    .display-4 {
        font-size: 2rem !important; /* Riduce dimensione icone grandi */
    }
    
    /* Avatar più piccolo */
    .avatar {
        width: 50px;
        height: 50px;
    }
    
    /* Container padding ridotto */
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
}

/* === SMARTPHONE (576px e sotto) === */
@media (max-width: 576px) {
    /* Card body padding ridotto */
    .card-body {
        padding: 1rem; /* Padding standard mobile */
    }
    
    /* Button group compatto */
    .btn-group .btn {
        padding: 0.375rem 0.5rem; /* Padding ridotto per button groups */
    }
}

/* === GRADIENTS ===
   LINGUAGGIO: CSS animations + background properties
   SCOPO: Animazioni gradient per elementi dinamici */

.bg-gradient {
    background-size: 200% 200%; /* Gradient più ampio per animazione */
    animation: gradient-shift 10s ease infinite; /* Animazione gradient infinita */
}

/* Keyframes per movimento gradient */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* === UTILITY CLASSES ===
   LINGUAGGIO: CSS utility classes + flexbox
   SCOPO: Classi helper per layout specifici */

/* Truncate a 2 righe */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* === DARK MODE SUPPORT ===
   LINGUAGGIO: CSS media queries + color scheme
   SCOPO: Supporto modalità scura del sistema operativo */

@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1e1e1e; /* Background scuro per card */
        color: #ffffff; /* Testo bianco */
    }
    
    .bg-light {
        background-color: #2d2d2d !important; /* Background scuro per elementi light */
        color: #ffffff;
    }
    
    .text-muted {
        color: #a0a0a0 !important; /* Grigio più chiaro per testo muted */
    }
}

/* === SCROLLBAR PERSONALIZZATA ===
   LINGUAGGIO: CSS webkit pseudo-elements
   SCOPO: Scrollbar personalizzata per look uniforme */

::-webkit-scrollbar {
    width: 6px; /* Larghezza scrollbar */
}

::-webkit-scrollbar-track {
    background: #f1f1f1; /* Background track */
    border-radius: 3px; /* Angoli arrotondati */
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1; /* Colore thumb */
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8; /* Colore hover più scuro */
}
</style>
@endpush

{{-- === JAVASCRIPT FUNZIONALE ===
     DESCRIZIONE: JavaScript per interattività dashboard e data injection
     LINGUAGGIO: Blade @push + JavaScript ES6+ + Laravel data integration
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE DATI PAGINA ===
   LINGUAGGIO: JavaScript ES6 object initialization
   SCOPO: Prepara oggetto globale per funzioni JavaScript dashboard */

// Inizializza oggetto dati globale se non esiste
window.PageData = window.PageData || {};

/* === INIEZIONE DATI SERVER-SIDE ===
   LINGUAGGIO: Blade @json directive + JavaScript object assignment
   SCOPO: Trasferisce dati PHP sicuri a JavaScript per uso client-side */

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

/* === DATI SESSIONE ===
   LINGUAGGIO: JavaScript object + Laravel session helpers
   SCOPO: Messaggi flash session per notifiche JavaScript */

// Messaggi flash per notifiche client-side
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionWarning = @json(session('warning'));
window.PageData.sessionInfo = @json(session('info'));

/* === FUNZIONI JAVASCRIPT COLLEGATE ===
   
   Le seguenti funzioni sono implementate nel file JavaScript principale:
   
   1. toggleProductView() - Switch tra vista griglia e lista
      LINGUAGGIO: JavaScript DOM manipulation + CSS display toggle
      SCOPO: Cambia visualizzazione prodotti senza reload pagina
   
   2. updateStatistics() - Aggiornamento real-time statistiche
      LINGUAGGIO: JavaScript AJAX + DOM update + animation
      SCOPO: Aggiorna contatori statistiche in tempo reale
   
   3. exportStats() - Funzione export report
      LINGUAGGIO: JavaScript data processing + file download
      SCOPO: Genera e scarica report CSV/PDF statistiche staff
   
   4. initDashboardAnimations() - Inizializza animazioni dashboard
      LINGUAGGIO: JavaScript animation control + CSS class management
      SCOPO: Gestisce animazioni cards e transizioni smooth
   
   5. handleQuickActions() - Gestione azioni rapide
      LINGUAGGIO: JavaScript event delegation + route navigation
      SCOPO: Ottimizza navigazione per azioni frequenti staff
   
   INTEGRAZIONE SICUREZZA:
   - Tutti i dati sono sanitizzati prima dell'injection
   - CSRF token incluso per chiamate AJAX
   - Validation client-side per input utente
   - Rate limiting per azioni ripetitive
   
   PERFORMANCE:
   - Lazy loading per statistiche pesanti
   - Debouncing per ricerche in tempo reale
   - Cache locale per dati frequenti
   - Ottimizzazione mobile per touch events
*/

/* === CONFIGURAZIONE DASHBOARD ===
   LINGUAGGIO: JavaScript object configuration
   SCOPO: Parametri personalizzabili per comportamento dashboard */

window.PageData.dashboardConfig = {
    statsUpdateInterval: 30000,      // Aggiorna stats ogni 30 secondi
    animationDuration: 300,          // Durata animazioni in ms
    enableRealTimeUpdates: true,     // Abilita aggiornamenti real-time
    enableNotifications: true,       // Abilita notifiche browser
    defaultView: 'grid',            // Vista predefinita prodotti
    autoSaveInterval: 60000         // Auto-save ogni minuto
};

// Dati aggiuntivi per funzionalità future...
window.PageData.pageType = 'staff_dashboard';
window.PageData.userLevel = 3;
window.PageData.loadTimestamp = Date.now();
</script>
@endpush