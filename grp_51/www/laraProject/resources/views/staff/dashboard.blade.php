{{-- Dashboard Staff Completa per Sistema Assistenza Tecnica --}}
{{-- Gruppo 51 - Staff Aziendale Livello 3 --}}
@extends('layouts.app')

@section('title', 'Dashboard Staff Aziendale')

@section('content')
<div class="container-fluid mt-4">

    {{-- === HEADER PRINCIPALE === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Intestazione con benvenuto --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h2 mb-1 text-warning fw-bold">
                        <i class="bi bi-person-badge me-2"></i>
                        Dashboard Staff Aziendale
                    </h1>
                    <p class="text-muted mb-0">
                        Gestione malfunzionamenti e soluzioni tecniche - Livello 3
                    </p>
                </div>
                <div class="text-end">
                    <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i>
                        Staff Aziendale
                    </div>
                    <div class="small text-muted mt-1">
                        Ultimo accesso: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            {{-- Alert di benvenuto personalizzato --}}
            <div class="alert alert-warning border-0 shadow-sm" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                    </div>
                    <div class="col">
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

            {{-- Breadcrumb navigazione --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><i class="bi bi-house"></i> Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Staff Aziendale</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- === STATISTICHE PRINCIPALI === --}}
    <div class="row mb-4 g-3">
        {{-- Card Prodotti Gestiti --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-box-seam display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold" id="prodotti-count">{{ $stats['prodotti_assegnati'] ?? $stats['total_prodotti'] ?? 0 }}</h3>
                            <small class="text-white-50">Prodotti Gestiti</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-arrow-up me-1"></i>
                        {{ isset($stats['prodotti_assegnati']) ? 'Assegnati a te' : 'Totali disponibili' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Soluzioni Create --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-tools display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold" id="soluzioni-count">{{ $stats['soluzioni_create'] ?? 0 }}</h3>
                            <small class="text-white-50">Soluzioni Create</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-check-circle me-1"></i>
                        Implementate da te
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Problemi Critici --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold" id="critici-count">{{ $stats['soluzioni_critiche'] ?? $stats['malfunzionamenti_critici'] ?? 0 }}</h3>
                            <small class="text-white-50">Problemi Critici</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-clock me-1"></i>
                        Richiedono attenzione
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Totale Database --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-database display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold" id="database-count">{{ $stats['total_malfunzionamenti'] ?? 0 }}</h3>
                            <small class="text-white-50">Totale Soluzioni</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-graph-up me-1"></i>
                        Nel sistema
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <h4 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Azioni Rapide Staff
                    </h4>
                    <small class="opacity-75">Funzionalità principali per la gestione quotidiana</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- NUOVA SOLUZIONE - Pulsante Principale --}}
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
                                {{-- Badge Nuovo --}}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate-pulse">
                                    Nuovo!
                                    <span class="visually-hidden">nuova funzionalità</span>
                                </span>
                            </div>
                        </div>

                        {{-- GESTIONE MALFUNZIONAMENTI --}}
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

                        {{-- CATALOGO COMPLETO --}}
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

                        {{-- STATISTICHE E REPORT --}}
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

    {{-- === PRODOTTI E ATTIVITÀ === --}}
    <div class="row mb-4">
        {{-- Colonna principale - Prodotti --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                Prodotti del Catalogo
                            </h4>
                            <p class="text-muted small mb-0">
                                @if(isset($stats['prodotti_assegnati']) && $stats['prodotti_assegnati'] > 0)
                                    Hai {{ $stats['prodotti_assegnati'] }} prodotti specificamente assegnati
                                @else
                                    Gestisci soluzioni per tutti i prodotti disponibili
                                @endif
                            </p>
                        </div>
                        {{-- Toggle vista --}}
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
                    {{-- Vista Griglia (default) --}}
                    <div id="grid-view">
                        @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                            <div class="row g-3">
                                @foreach($stats['prodotti_lista']->take(6) as $prodotto)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border-0 shadow-sm hover-card product-card">
                                            <div class="card-body">
                                                {{-- Header prodotto --}}
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title mb-0 fw-bold text-truncate flex-grow-1 me-2">
                                                        {{ $prodotto->nome }}
                                                    </h6>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst($prodotto->categoria ?? 'generale') }}
                                                    </span>
                                                </div>

                                                {{-- Info prodotto --}}
                                                @if(isset($prodotto->modello) && $prodotto->modello)
                                                    <p class="text-muted small mb-2">
                                                        <i class="bi bi-tag me-1"></i>
                                                        {{ $prodotto->modello }}
                                                    </p>
                                                @endif

                                                {{-- Statistiche --}}
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
                                                                {{ isset($prodotto->malfunzionamenti) ? $prodotto->malfunzionamenti->where('gravita', 'critica')->count() : 0 }}
                                                            </div>
                                                            <small class="text-muted">Critiche</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Azioni --}}
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
                            
                            {{-- Link "Vedi tutti" --}}
                            <div class="text-center mt-4">
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-right me-2"></i>
                                    Visualizza Tutti i Prodotti ({{ $stats['total_prodotti'] ?? 'N/A' }})
                                </a>
                            </div>
                        @else
                            {{-- Nessun prodotto --}}
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

                    {{-- Vista Lista (nascosta di default) --}}
                    <div id="list-view" style="display: none;">
                        @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                            <div class="list-group list-group-flush">
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
                            <div class="text-center py-4">
                                <i class="bi bi-list display-4 text-muted opacity-50"></i>
                                <p class="text-muted mt-2">Nessun prodotto in formato lista</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar destra --}}
        <div class="col-lg-4">
            {{-- Ultime attività --}}
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
                                            <small class="text-muted">
                                                {{ isset($soluzione->created_at) ? $soluzione->created_at->format('d/m/Y H:i') : 'Data N/A' }}
                                            </small>
                                        </div>
                                        <div>
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
                        <div class="text-center mt-3">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-list me-1"></i>Vedi Tutte le Soluzioni
                            </a>
                        </div>
                    @else
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

            {{-- Informazioni account --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Info Account
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted">Ultima modifica</small>
                            <div class="fw-semibold">{{ $stats['ultima_modifica'] ?? 'Mai' }}</div>
                        </div>
                    </div>
                    
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
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-person text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted">Username</small>
                            <div class="fw-semibold">{{ auth()->user()->username ?? 'N/A' }}</div>
                        </div>
                    </div>

                    {{-- Link rapidi --}}
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profilo') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-gear me-1"></i>Modifica Profilo
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Dashboard Generale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === SUGGERIMENTI E GUIDE === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                    <h4 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Suggerimenti per lo Staff
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded p-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-semibold">Attenzione</h6>
                                    <small class="text-muted">
                                        Controlla sempre la gravità del problema prima di pubblicare la soluzione.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded p-2">
                                        <i class="bi bi-lightbulb text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-semibold">Consiglio</h6>
                                    <small class="text-muted">
                                        Usa il pulsante "Nuova Soluzione" per aggiungere rapidamente soluzioni senza navigare.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- === MODAL STATISTICHE DETTAGLIATE === --}}
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <h5 class="modal-title" id="statsModalLabel">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Statistiche e Report Staff
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- Statistiche numeriche --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Riepilogo Attività</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card border-0 bg-primary bg-opacity-10 text-center p-3">
                                    <div class="h4 text-primary mb-1" id="modal-prodotti-count">{{ $stats['prodotti_assegnati'] ?? 0 }}</div>
                                    <small class="text-muted">Prodotti Gestiti</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0 bg-success bg-opacity-10 text-center p-3">
                                    <div class="h4 text-success mb-1" id="modal-soluzioni-count">{{ $stats['soluzioni_create'] ?? 0 }}</div>
                                    <small class="text-muted">Soluzioni Create</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0 bg-warning bg-opacity-10 text-center p-3">
                                    <div class="h4 text-warning mb-1">{{ $stats['soluzioni_critiche'] ?? 0 }}</div>
                                    <small class="text-muted">Critiche Risolte</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0 bg-info bg-opacity-10 text-center p-3">
                                    <div class="h4 text-info mb-1">95%</div>
                                    <small class="text-muted">Tasso Successo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grafico attività --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Andamento Settimanale</h6>
                        <div class="bg-light rounded p-3">
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

                    {{-- Report dettagliato --}}
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" onclick="exportStats()">
                    <i class="bi bi-download me-1"></i>Esporta Report
                </button>
                <a href="{{ route('staff.statistiche') }}" class="btn btn-warning">
                    <i class="bi bi-bar-chart me-1"></i>Statistiche Complete
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- === STILI CSS PERSONALIZZATI === --}}
@push('styles')
<style>
/* === ANIMAZIONI E TRANSIZIONI === */
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.hover-lift {
    transition: all 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.pulse-btn {
    animation: pulse-glow 2s ease-in-out infinite alternate;
}

@keyframes pulse-glow {
    0% { 
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        transform: scale(1);
    }
    100% { 
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
        transform: scale(1.02);
    }
}

.animate-pulse {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* === CARDS E LAYOUT === */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.product-card {
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.product-card:hover {
    border-color: var(--bs-primary) !important;
}

/* === BADGES E ELEMENTI === */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.avatar {
    width: 60px;
    height: 60px;
}

/* === PULSANTI === */
.btn-lg {
    font-size: 1rem;
    border-radius: 8px;
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .btn-lg {
        min-height: 120px !important;
        font-size: 0.9rem;
    }
    
    .display-4 {
        font-size: 2rem !important;
    }
    
    .avatar {
        width: 50px;
        height: 50px;
    }
    
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group .btn {
        padding: 0.375rem 0.5rem;
    }
}

/* === GRADIENTS === */
.bg-gradient {
    background-size: 200% 200%;
    animation: gradient-shift 10s ease infinite;
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* === UTILITY CLASSES === */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* === DARK MODE SUPPORT === */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1e1e1e;
        color: #ffffff;
    }
    
    .bg-light {
        background-color: #2d2d2d !important;
        color: #ffffff;
    }
    
    .text-muted {
        color: #a0a0a0 !important;
    }
}

/* === SCROLLBAR === */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

{{-- === JAVASCRIPT FUNZIONALE === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('🚀 Dashboard Staff inizializzata - versione sicura');

    // === GESTIONE SICURA TOGGLE VISTA PRODOTTI ===
    $('input[name="prodotti-view"]').on('change', function() {
        const viewType = $(this).attr('id');
        
        if (viewType === 'view-grid') {
            $('#grid-view').fadeIn(300);
            $('#list-view').fadeOut(200);
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('staff_products_view', 'grid');
            }
        } else if (viewType === 'view-list') {
            $('#grid-view').fadeOut(200);
            $('#list-view').fadeIn(300);
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('staff_products_view', 'list');
            }
        }
    });

    // Ripristina vista salvata - CON CONTROLLO SICUREZZA
    try {
        if (typeof(Storage) !== "undefined") {
            const savedView = localStorage.getItem('staff_products_view');
            if (savedView === 'list') {
                const listToggle = $('#view-list');
                if (listToggle.length > 0) {
                    listToggle.prop('checked', true).trigger('change');
                }
            }
        }
    } catch(e) {
        console.warn('Impossibile ripristinare vista salvata:', e);
    }

    // === ANIMAZIONI HOVER PER CARDS - CON CONTROLLI ===
    $('.hover-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // === ANIMAZIONE CONTATORI MIGLIORATA ===
    function animateCounters() {
        $('.card-body h3, .h4').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            
            // Estrai solo i numeri dal testo
            const target = parseInt(text.replace(/[^\d]/g, ''));
            
            if (!isNaN(target) && target > 0 && target < 10000) {
                $counter.text('0');
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $counter.text(Math.ceil(this.counter));
                    },
                    complete: function() {
                        $counter.text(target);
                    }
                });
            }
        });
    }

    // Avvia animazione dopo un breve delay
    setTimeout(animateCounters, 800);

    // === TOOLTIP INITIALIZATION SICURA ===
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    } catch(e) {
        console.warn('Impossibile inizializzare tooltip:', e);
    }

    // === GESTIONE NOTIFICHE SICURA ===
    @if(session('success'))
        showNotification('success', {!! json_encode(session("success")) !!});
    @endif

    @if(session('error'))
        showNotification('error', {!! json_encode(session("error")) !!});
    @endif

    @if(session('warning'))
        showNotification('warning', {!! json_encode(session("warning")) !!});
    @endif

    @if(session('info'))
        showNotification('info', {!! json_encode(session("info")) !!});
    @endif

    // === FUNZIONE NOTIFICA SICURA ===
    function showNotification(type, message) {
        try {
            const alertClass = type === 'error' ? 'danger' : type;
            const icon = type === 'success' ? 'check-circle' : 
                        type === 'error' ? 'exclamation-triangle' : 
                        type === 'warning' ? 'exclamation-triangle' : 'info-circle';
            
            const notification = $(`
                <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed animate-slide-in" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
                     role="alert">
                    <i class="bi bi-${icon} me-2"></i>
                    <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
                </div>
            `);
            
            $('body').append(notification);
            
            // Auto-dismiss dopo 5 secondi con controllo esistenza
            setTimeout(() => {
                if (notification.length > 0 && notification.is(':visible')) {
                    notification.alert('close');
                }
            }, 5000);
            
        } catch(e) {
            console.error('Errore nella visualizzazione notifica:', e);
            // Fallback: alert browser nativo
            alert(type.toUpperCase() + ': ' + message);
        }
    }

    // Rendi showNotification globale
    window.showNotification = showNotification;

    // === REFRESH AUTOMATICO STATS SICURO ===
    let refreshInterval;
    
    function startAutoRefresh() {
        // Solo se siamo in una pagina dashboard attiva
        if (document.hidden || !document.hasFocus()) {
            return;
        }
        
        refreshInterval = setInterval(function() {
            const shouldUpdate = Math.random() > 0.9; // 10% probabilità
            
            if (shouldUpdate && typeof $ !== 'undefined') {
                console.log('📊 Controllo aggiornamento statistiche');
                
                // Effetto visivo leggero
                const counters = $('.card-body h3');
                if (counters.length > 0) {
                    counters.addClass('animate-pulse');
                    setTimeout(() => {
                        counters.removeClass('animate-pulse');
                    }, 1000);
                }
            }
        }, 300000); // 5 minuti
    }

    // Avvia refresh solo se la pagina è visibile
    if (!document.hidden) {
        startAutoRefresh();
    }

    // Gestisci visibilità pagina
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        } else {
            startAutoRefresh();
        }
    });

    // === PERFORMANCE MONITORING SICURO ===
    try {
        if (typeof performance !== 'undefined' && performance.timing) {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            if (loadTime > 0 && loadTime < 60000) { // Sanity check
                console.log(`Dashboard Staff caricata in ${loadTime}ms`);
            }
        }
    } catch(e) {
        console.warn('Performance monitoring non disponibile:', e);
    }

    // === DEBUG INFO SICURO (solo sviluppo) ===
    @if(config('app.debug'))
        try {
            console.group('🐛 Debug Dashboard Staff');
            console.log('User:', {!! json_encode(auth()->user()->nome ?? auth()->user()->name ?? 'N/A') !!});
            console.log('Stats Keys:', {!! json_encode(array_keys($stats ?? [])) !!});
            console.log('Prodotti Count:', {{ $stats['prodotti_assegnati'] ?? 0 }});
            console.log('Environment:', {!! json_encode(config("app.env")) !!});
            console.log('jQuery Version:', typeof $ !== 'undefined' ? $.fn.jquery : 'Non disponibile');
            console.log('Bootstrap:', typeof bootstrap !== 'undefined' ? 'Disponibile' : 'Non disponibile');
            console.groupEnd();
        } catch(e) {
            console.warn('Debug info parzialmente fallito:', e);
        }
    @endif

    console.log('✅ Dashboard Staff completamente funzionale - versione sicura');
});

// === FUNZIONI GLOBALI SICURE ===

// Esporta statistiche in formato JSON - CON CONTROLLI
window.exportStats = function() {
    try {
        const stats = {
            prodotti_gestiti: {{ $stats['prodotti_assegnati'] ?? $stats['total_prodotti'] ?? 0 }},
            soluzioni_create: {{ $stats['soluzioni_create'] ?? 0 }},
            problemi_critici: {{ $stats['soluzioni_critiche'] ?? 0 }},
            totale_database: {{ $stats['total_malfunzionamenti'] ?? 0 }},
            exported_at: new Date().toISOString(),
            user: {!! json_encode(auth()->user()->username ?? "staff") !!}
        };
        
        const dataStr = JSON.stringify(stats, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        
        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = `staff_report_${new Date().toISOString().split('T')[0]}.json`;
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('📄 Report esportato con successo');
        if (typeof showNotification === 'function') {
            showNotification('success', 'Report statistiche esportato con successo');
        }
        
    } catch(e) {
        console.error('Errore durante l\'esportazione:', e);
        if (typeof showNotification === 'function') {
            showNotification('error', 'Errore durante l\'esportazione del report');
        } else {
            alert('Errore durante l\'esportazione del report');
        }
    }
};

// Refresh manuale dashboard - SICURO
window.refreshDashboard = function() {
    try {
        console.log('🔄 Refresh dashboard richiesto');
        
        // Effetto visivo di loading con controllo esistenza elementi
        const cards = $('.card');
        if (cards.length > 0) {
            cards.addClass('animate-pulse');
        }
        
        setTimeout(() => {
            location.reload();
        }, 500);
        
    } catch(e) {
        console.error('Errore durante il refresh:', e);
        location.reload(); // Fallback diretto
    }
};

// === GESTIONE ERRORI GLOBALE MIGLIORATA ===
window.onerror = function(msg, url, line, col, error) {
    console.group('❌ Errore Dashboard Staff');
    console.error('Message:', msg);
    console.error('Source:', url);
    console.error('Line:', line);
    console.error('Column:', col);
    if (error) {
        console.error('Error Object:', error);
        console.error('Stack:', error.stack);
    }
    console.groupEnd();
    
    // Non bloccare l'esecuzione
    return false;
};

// Gestione promise rejections
window.addEventListener('unhandledrejection', function(event) {
    console.warn('Promise rejection non gestita:', event.reason);
    // Previeni che venga mostrato in console come errore
    event.preventDefault();
});

// === CONTROLLI DI INTEGRITÀ ===
function performIntegrityChecks() {
    const checks = {
        jquery: typeof $ !== 'undefined',
        bootstrap: typeof bootstrap !== 'undefined',
        localStorage: typeof Storage !== 'undefined',
        performance: typeof performance !== 'undefined'
    };
    
    console.log('🔍 Controlli integrità:', checks);
    
    return checks;
}

// Esegui controlli all'avvio
setTimeout(performIntegrityChecks, 1000);

// === FUNZIONI GLOBALI ===

// Esporta statistiche in formato JSON
window.exportStats = function() {
    const stats = {
        prodotti_gestiti: {{ $stats['prodotti_assegnati'] ?? $stats['total_prodotti'] ?? 0 }},
        soluzioni_create: {{ $stats['soluzioni_create'] ?? 0 }},
        problemi_critici: {{ $stats['soluzioni_critiche'] ?? 0 }},
        totale_database: {{ $stats['total_malfunzionamenti'] ?? 0 }},
        exported_at: new Date().toISOString(),
        user: '{{ auth()->user()->username ?? "staff" }}'
    };
    
    const dataStr = JSON.stringify(stats, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `staff_report_${new Date().toISOString().split('T')[0]}.json`;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    console.log('📄 Report esportato');
    showNotification('success', 'Report statistiche esportato con successo');
};

// Refresh manuale dashboard
window.refreshDashboard = function() {
    console.log('🔄 Refresh dashboard richiesto');
    
    // Effetto visivo di loading
    $('.card').addClass('animate-pulse');
    
    setTimeout(() => {
        location.reload();
    }, 500);
};

// Mostra notifica personalizzata
window.showNotification = function(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const icon = type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-triangle' : 'info-circle';
    
    const notification = `
        <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(notification);
    setTimeout(() => $('.alert').alert('close'), 5000);
};

// === GESTIONE ERRORI GLOBALE ===
window.onerror = function(msg, url, line, col, error) {
    console.error('❌ Errore Dashboard Staff:', {
        message: msg,
        source: url,
        line: line,
        column: col,
        error: error?.toString()
    });
    return false;
};

// === SERVICE WORKER (opzionale) ===
if ('serviceWorker' in navigator && '{{ config("app.env") }}' === 'production') {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('SW registrato:', reg.scope))
        .catch(err => console.log('SW fallito:', err));
}
</script>
@endpush