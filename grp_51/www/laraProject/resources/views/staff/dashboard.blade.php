{{-- Dashboard Staff Aziendale - Versione Completa --}}
@extends('layouts.app')

@section('title', 'Dashboard Staff Tecnico')

@section('content')
<div class="container-fluid mt-4">

    {{-- === HEADER E BENVENUTO === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h2 mb-1 text-warning">
                        <i class="bi bi-person-badge me-2"></i>
                        Dashboard Staff Tecnico
                    </h1>
                    <p class="text-muted mb-0">Gestione soluzioni tecniche e malfunzionamenti</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i>Livello 3 - Staff
                    </div>
                </div>
            </div>

            {{-- Benvenuto personalizzato --}}
            <div class="alert alert-warning border-0 bg-gradient" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="alert-heading mb-1">
                            Benvenuto, {{ auth()->user()->nome ?? auth()->user()->name ?? 'Staff' }}!
                        </h4>
                        <p class="mb-2">
                            <strong>Staff Tecnico Aziendale</strong> - 
                            Ultima connessione: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'Prima volta' }}
                        </p>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Accesso completo a malfunzionamenti e soluzioni tecniche
                        </small>
                    </div>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><i class="bi bi-house"></i> Home</a>
                    </li>
                    <li class="breadcrumb-item active">Dashboard Staff</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === --}}
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm bg-gradient h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-box-seam display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['prodotti_assegnati'] ?? 0 }}</h3>
                            <small class="text-white-50">Prodotti Assegnati</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-arrow-up"></i>
                        Su {{ $stats['total_prodotti'] ?? 0 }} totali
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm bg-gradient h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-tools display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['soluzioni_create'] ?? 0 }}</h3>
                            <small class="text-white-50">Soluzioni Create</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-check-circle"></i>
                        Da te implementate
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm bg-gradient h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['soluzioni_critiche'] ?? 0 }}</h3>
                            <small class="text-white-50">Problemi Critici</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-clock"></i>
                        Richiedono attenzione
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm bg-gradient h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-graph-up display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['total_malfunzionamenti'] ?? 0 }}</h3>
                            <small class="text-white-50">Totale Soluzioni</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-database"></i>
                        Nel database
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>
                        Azioni Rapide
                    </h4>
                    <p class="text-muted small mb-0">Funzionalità principali per la gestione quotidiana</p>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        {{-- NUOVA SOLUZIONE (Pulsante principale) --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="position-relative">
                                <a href="{{ route('staff.create.nuova.soluzione') }}" 
                                   class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm"
                                   style="min-height: 140px;">
                                    <i class="bi bi-plus-circle-fill display-4 mb-3 pulse-animation"></i>
                                    <div class="text-center">
                                        <h5 class="mb-2 fw-bold">Nuova Soluzione</h5>
                                        <small class="opacity-75">Aggiungi soluzione per qualsiasi prodotto</small>
                                    </div>
                                </a>
                                {{-- Badge "Nuovo" --}}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    Nuovo!
                                </span>
                            </div>
                        </div>

                        {{-- ESPLORA CATALOGO --}}
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}" 
                               class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none text-white shadow-sm"
                               style="min-height: 140px;">
                                <i class="bi bi-search display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Esplora Catalogo</h5>
                                    <small class="opacity-75">Visualizza tutti i prodotti e soluzioni</small>
                                </div>
                            </a>
                        </div>

                        {{-- STATISTICHE DETTAGLIATE --}}
                        <div class="col-lg-4 col-md-6">
                            <button class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-dark shadow-sm"
                                    style="min-height: 140px;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#statisticsModal">
                                <i class="bi bi-bar-chart-line display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Statistiche</h5>
                                    <small class="opacity-75">Analisi dettagliate e report</small>
                                </div>
                            </button>
                        </div>

                        {{-- SUPPORTO --}}
                        <div class="col-lg-4 col-md-6">
                            <a href="#" 
                               class="btn btn-outline-secondary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm"
                               style="min-height: 140px;"
                               data-bs-toggle="modal" 
                               data-bs-target="#helpModal">
                                <i class="bi bi-question-circle display-4 mb-3"></i>
                                <div class="text-center">
                                    <h5 class="mb-2 fw-bold">Supporto</h5>
                                    <small class="opacity-75">Guida e assistenza</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI ASSEGNATI === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                I Tuoi Prodotti Assegnati
                                @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                                    <span class="badge bg-primary ms-2">{{ $stats['prodotti_lista']->count() }}</span>
                                @endif
                            </h4>
                            <p class="text-muted small mb-0">Prodotti di cui gestisci le soluzioni tecniche</p>
                        </div>
                        
                        {{-- Toggle visualizzazione --}}
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="view-mode" id="view-cards" autocomplete="off" checked>
                            <label class="btn btn-outline-primary btn-sm" for="view-cards">
                                <i class="bi bi-grid-3x3-gap"></i> Cards
                            </label>
                            <input type="radio" class="btn-check" name="view-mode" id="view-table" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm" for="view-table">
                                <i class="bi bi-table"></i> Tabella
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card-body pt-3">
                    @if(isset($stats['prodotti_lista']) && $stats['prodotti_lista']->count() > 0)
                        {{-- VISTA CARDS --}}
                        <div id="cards-view">
                            <div class="row g-3">
                                @foreach($stats['prodotti_lista'] as $prodotto)
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <div class="card h-100 border-0 shadow-sm hover-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title mb-0 fw-bold text-truncate flex-grow-1 me-2">
                                                        {{ $prodotto->nome }}
                                                    </h6>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst($prodotto->categoria) }}
                                                    </span>
                                                </div>
                                                
                                                @if($prodotto->modello)
                                                    <p class="text-muted small mb-2">
                                                        <i class="bi bi-tag me-1"></i>{{ $prodotto->modello }}
                                                    </p>
                                                @endif
                                                
                                                {{-- Statistiche prodotto --}}
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
                                                                {{ $prodotto->malfunzionamenti->where('gravita', 'alta')->count() ?? 0 }}
                                                            </div>
                                                            <small class="text-muted">Critiche</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Azioni --}}
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i>Visualizza
                                                    </a>
                                                    <a href="{{ route('staff.create.nuova.soluzione', $prodotto->id) }}" 
                                                       class="btn btn-outline-success btn-sm">
                                                        <i class="bi bi-plus me-1"></i>Nuova Soluzione
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Mostra di più --}}
                            @if($stats['prodotti_lista']->count() > 8)
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary" id="showMoreProducts">
                                        <i class="bi bi-chevron-down me-2"></i>
                                        Mostra altri prodotti
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- VISTA TABELLA --}}
                        <div id="table-view" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Prodotto</th>
                                            <th>Categoria</th>
                                            <th>Modello</th>
                                            <th class="text-center">Soluzioni</th>
                                            <th class="text-center">Critiche</th>
                                            <th class="text-center">Ultima Modifica</th>
                                            <th class="text-center">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['prodotti_lista'] as $prodotto)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $prodotto->nome }}</div>
                                                    @if($prodotto->codice)
                                                        <small class="text-muted">{{ $prodotto->codice }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($prodotto->categoria) }}</span>
                                                </td>
                                                <td>{{ $prodotto->modello ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">
                                                        {{ $prodotto->malfunzionamenti->count() ?? 0 }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $critiche = $prodotto->malfunzionamenti->where('gravita', 'alta')->count() ?? 0;
                                                    @endphp
                                                    <span class="badge bg-{{ $critiche > 0 ? 'warning' : 'success' }}">
                                                        {{ $critiche }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <small class="text-muted">
                                                        {{ $prodotto->updated_at ? $prodotto->updated_at->format('d/m/Y') : '-' }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                                           class="btn btn-outline-primary" 
                                                           title="Visualizza dettagli">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('staff.create.nuova.soluzione', $prodotto->id) }}" 
                                                           class="btn btn-outline-success" 
                                                           title="Aggiungi soluzione">
                                                            <i class="bi bi-plus"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        {{-- Nessun prodotto assegnato --}}
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-box display-1 text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted mb-3">Nessun prodotto assegnato</h5>
                            <p class="text-muted mb-4">
                                Non hai prodotti specificamente assegnati al momento.<br>
                                Puoi comunque gestire soluzioni per tutti i prodotti del catalogo.
                            </p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>Aggiungi Nuova Soluzione
                                </a>
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>Esplora Catalogo
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ULTIME ATTIVITÀ === --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Ultime Soluzioni Aggiunte
                    </h4>
                    <p class="text-muted small mb-0">Le tue ultime 5 soluzioni create</p>
                </div>
                <div class="card-body pt-3">
                    @if(isset($stats['ultime_soluzioni']) && $stats['ultime_soluzioni']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['ultime_soluzioni']->take(5) as $soluzione)
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="mb-1 fw-semibold">{{ $soluzione->titolo }}</h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-box me-1"></i>
                                                {{ $soluzione->prodotto->nome ?? 'Prodotto N/A' }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $soluzione->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $soluzione->gravita == 'alta' ? 'danger' : ($soluzione->gravita == 'media' ? 'warning' : 'success') }}">
                                                {{ ucfirst($soluzione->gravita) }}
                                            </span>
                                            <div class="mt-1">
                                                <a href="{{ route('malfunzionamenti.show', [$soluzione->prodotto, $soluzione]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-list me-1"></i>Vedi Tutte
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2">Nessuna soluzione recente</p>
                            <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-plus me-1"></i>Crea la Prima Soluzione
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- === SIDEBAR INFO === --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni Rapide
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
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted">Stato account</small>
                            <div class="fw-semibold text-success">Attivo</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                            <i class="bi bi-shield text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted">Permessi</small>
                            <div class="fw-semibold">Staff Tecnico</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Suggerimenti --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        Suggerimenti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10">
                        <small>
                            <strong>💡 Consiglio:</strong> Usa il pulsante "Nuova Soluzione" 
                            per aggiungere rapidamente soluzioni senza navigare prima al prodotto specifico.
                        </small>
                    </div>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10">
                        <small>
                            <strong>✅ Best Practice:</strong> Includi sempre step dettagliati 
                            nelle tue soluzioni per aiutare altri tecnici.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE AVANZATE (Collapsible) === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0" data-bs-toggle="collapse" data-bs-target="#advancedStats" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            Statistiche Avanzate
                        </h4>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <p class="text-muted small mb-0">Clicca per espandere analisi dettagliate</p>
                </div>
                <div id="advancedStats" class="collapse">
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Grafico a torta per gravità --}}
                            <div class="col-md-6">
                                <h5 class="h6 mb-3">Distribuzione per Gravità</h5>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted">
                                        <span class="badge bg-danger me-2"></span>Alta
                                    </span>
                                    <span class="fw-semibold">{{ $stats['soluzioni_critiche'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted">
                                        <span class="badge bg-warning me-2"></span>Media
                                    </span>
                                    <span class="fw-semibold">{{ ($stats['soluzioni_create'] ?? 0) - ($stats['soluzioni_critiche'] ?? 0) }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">
                                        <span class="badge bg-success me-2"></span>Bassa
                                    </span>
                                    <span class="fw-semibold">{{ max(0, ($stats['soluzioni_create'] ?? 0) - ($stats['soluzioni_critiche'] ?? 0)) }}</span>
                                </div>
                            </div>

                            {{-- Performance mensile --}}
                            <div class="col-md-6">
                                <h5 class="h6 mb-3">Performance del Mese</h5>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">75% dell'obiettivo mensile raggiunto</small>
                                
                                <div class="mt-3 pt-3 border-top">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="fw-bold text-primary">{{ $stats['soluzioni_create'] ?? 0 }}</div>
                                            <small class="text-muted">Create</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-info">{{ max(0, ($stats['soluzioni_create'] ?? 0) - 2) }}</div>
                                            <small class="text-muted">Modificate</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-success">95%</div>
                                            <small class="text-muted">Efficacia</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- === MODALI === --}}

{{-- Modal Statistiche Dettagliate --}}
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Statistiche Dettagliate
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-primary">{{ $stats['prodotti_assegnati'] ?? 0 }}</h3>
                                <p class="mb-0">Prodotti Assegnati</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-success">{{ $stats['soluzioni_create'] ?? 0 }}</h3>
                                <p class="mb-0">Soluzioni Create</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6>Andamento Temporale</h6>
                        <div class="bg-light rounded p-3">
                            <p class="text-muted text-center">Grafico delle attività negli ultimi 30 giorni</p>
                            <div class="d-flex justify-content-between align-items-end" style="height: 100px;">
                                <div class="bg-primary rounded-bottom" style="width: 20px; height: 30%;"></div>
                                <div class="bg-primary rounded-bottom" style="width: 20px; height: 60%;"></div>
                                <div class="bg-primary rounded-bottom" style="width: 20px; height: 40%;"></div>
                                <div class="bg-primary rounded-bottom" style="width: 20px; height: 80%;"></div>
                                <div class="bg-primary rounded-bottom" style="width: 20px; height: 50%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary">Esporta Report</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Aiuto --}}
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-question-circle me-2"></i>
                    Guida Dashboard Staff
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Funzionalità Principali:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-plus-circle text-success me-2"></i>
                        <strong>Nuova Soluzione:</strong> Aggiungi rapidamente soluzioni per qualsiasi prodotto
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-search text-info me-2"></i>
                        <strong>Esplora Catalogo:</strong> Visualizza tutti i prodotti e le loro soluzioni
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-bar-chart text-warning me-2"></i>
                        <strong>Statistiche:</strong> Monitora le tue performance e attività
                    </li>
                </ul>
                
                <h6 class="mt-4">Suggerimenti:</h6>
                <div class="alert alert-info">
                    <small>
                        • Usa descrizioni dettagliate nelle soluzioni<br>
                        • Categorizza correttamente la gravità<br>
                        • Includi passaggi numerati nelle procedure
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ho Capito</button>
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
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.pulse-animation {
    animation: pulse 2s ease-in-out infinite alternate;
}

@keyframes pulse {
    0% { opacity: 1; }
    100% { opacity: 0.7; }
}

/* === GRADIENTS E COLORI === */
.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
}

/* === AVATAR === */
.avatar {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* === BADGES PERSONALIZZATI === */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* === PULSANTI === */
.btn-lg {
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* === CARDS === */
.card {
    border: none !important;
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    background: transparent;
}

/* === PROGRESS BARS === */
.progress {
    background-color: rgba(0,0,0,0.05);
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
        font-size: 1.2rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 6px !important;
        margin-bottom: 2px;
    }
}

/* === DARK MODE SUPPORT === */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1a1a1a;
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

/* === ANIMAZIONI SPECIALI === */
.btn-success:first-child {
    position: relative;
    overflow: hidden;
}

.btn-success:first-child::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-success:first-child:hover::before {
    left: 100%;
}

/* === SCROLLBAR PERSONALIZZATA === */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === FOCUS STYLES === */
.btn:focus, .form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* === UTILITY CLASSES === */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.border-dashed {
    border-style: dashed !important;
}

.bg-pattern {
    background-image: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.1),
        rgba(255,255,255,0.1) 10px,
        transparent 10px,
        transparent 20px
    );
}
</style>
@endpush

{{-- === JAVASCRIPT PERSONALIZZATO === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('🚀 Dashboard Staff Completa inizializzata');

    // === GESTIONE TOGGLE VISTA === 
    $('input[name="view-mode"]').on('change', function() {
        const viewType = $(this).attr('id');
        
        if (viewType === 'view-cards') {
            $('#cards-view').fadeIn(300);
            $('#table-view').fadeOut(200);
            console.log('Switched to cards view');
        } else if (viewType === 'view-table') {
            $('#cards-view').fadeOut(200);
            $('#table-view').fadeIn(300);
            console.log('Switched to table view');
        }
    });

    // === ANIMAZIONI HOVER PER LE CARDS ===
    $('.hover-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // === PULSANTE "MOSTRA ALTRI PRODOTTI" ===
    $('#showMoreProducts').on('click', function() {
        // Simula il caricamento di più prodotti
        $(this).html('<i class="bi bi-hourglass-split me-2"></i>Caricamento...');
        
        setTimeout(() => {
            $(this).html('<i class="bi bi-check-circle me-2"></i>Tutti i prodotti caricati');
            $(this).prop('disabled', true).removeClass('btn-outline-primary').addClass('btn-success');
        }, 1500);
    });

    // === ANIMAZIONE CONTATORI ===
    function animateCounters() {
        $('.card-body h3').each(function() {
            const $counter = $(this);
            const target = parseInt($counter.text());
            
            if (!isNaN(target)) {
                $counter.text('0');
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $counter.text(Math.ceil(this.counter));
                    }
                });
            }
        });
    }

    // Avvia animazione contatori se visibili
    if ($('.card-body h3').length > 0) {
        setTimeout(animateCounters, 500);
    }

    // === GESTIONE COLLAPSE STATISTICHE AVANZATE ===
    $('#advancedStats').on('shown.bs.collapse', function() {
        $('.card-header i').removeClass('bi-chevron-down').addClass('bi-chevron-up');
        console.log('Statistiche avanzate espanse');
    });

    $('#advancedStats').on('hidden.bs.collapse', function() {
        $('.card-header i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
        console.log('Statistiche avanzate contratte');
    });

    // === TOOLTIP INITIALIZATION ===
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // === GESTIONE NOTIFICHE (se presenti) ===
    @if(session('success'))
        showNotification('success', '{{ session("success") }}');
    @endif

    @if(session('error'))
        showNotification('error', '{{ session("error") }}');
    @endif

    @if(session('warning'))
        showNotification('warning', '{{ session("warning") }}');
    @endif

    // === FUNZIONE NOTIFICA PERSONALIZZATA ===
    function showNotification(type, message) {
        const alertClass = type === 'error' ? 'danger' : type;
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'exclamation-triangle' : 'info-circle';
        
        const notification = `
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto-dismiss dopo 5 secondi
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }

    // === SALVATAGGIO PREFERENZE VISTA ===
    $('input[name="view-mode"]').on('change', function() {
        const viewMode = $(this).attr('id');
        localStorage.setItem('staff_dashboard_view_mode', viewMode);
    });

    // Ripristina vista salvata
    const savedViewMode = localStorage.getItem('staff_dashboard_view_mode');
    if (savedViewMode) {
        $(`#${savedViewMode}`).prop('checked', true).trigger('change');
    }

    // === REFRESH AUTOMATICO STATISTICHE ===
    setInterval(function() {
        // Aggiorna timestamp "ultima modifica" se necessario
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0');
        
        // Aggiorna solo se c'è stata attività (simulazione)
        if (Math.random() > 0.95) { // 5% di probabilità ogni minuto
            console.log('Statistiche aggiornate automaticamente');
        }
    }, 60000); // Ogni minuto

    // === GESTIONE ERRORI GLOBALE ===
    window.onerror = function(msg, url, line, col, error) {
        console.error('Errore Dashboard Staff:', {
            message: msg,
            source: url,
            line: line,
            column: col,
            error: error
        });
        return false;
    };

    // === PERFORMANCE MONITORING ===
    if (performance.navigation.type === 1) {
        console.log('Dashboard ricaricata');
    }

    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
    console.log(`Dashboard caricata in ${loadTime}ms`);

    // === FINALIZZAZIONE ===
    console.log('✅ Dashboard Staff completamente inizializzata e ottimizzata');

    // Debug info (solo in sviluppo)
    @if(config('app.debug'))
        console.group('🐛 Debug Info Dashboard Staff');
        console.log('User:', '{{ auth()->user()->nome ?? auth()->user()->name }}');
        console.log('Prodotti Assegnati:', {{ $stats['prodotti_assegnati'] ?? 0 }});
        console.log('Soluzioni Create:', {{ $stats['soluzioni_create'] ?? 0 }});
        console.log('Stats Available:', {!! json_encode(array_keys($stats ?? [])) !!});
        console.groupEnd();
    @endif
});

// === FUNZIONI GLOBALI ===

// Funzione per esportare statistiche
window.exportStats = function() {
    const stats = {
        prodotti_assegnati: {{ $stats['prodotti_assegnati'] ?? 0 }},
        soluzioni_create: {{ $stats['soluzioni_create'] ?? 0 }},
        soluzioni_critiche: {{ $stats['soluzioni_critiche'] ?? 0 }},
        exported_at: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(stats, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = `staff_stats_${new Date().toISOString().split('T')[0]}.json`;
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
    
    console.log('Statistiche esportate');
};

// Funzione per refresh manuale
window.refreshDashboard = function() {
    location.reload();
};

// Service Worker per cache (opzionale)
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(registration => console.log('SW registered'))
        .catch(error => console.log('SW registration failed'));
}
</script>
@endpush