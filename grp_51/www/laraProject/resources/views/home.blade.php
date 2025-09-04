{{-- 
    Homepage Sistema Assistenza Tecnica
    File: resources/views/home.blade.php
    
    Pagina principale del sistema con:
    - Hero section con statistiche principali
    - Ricerca rapida prodotti
    - Informazioni azienda
    - Categorie prodotti
    - Accesso per operatori
    - Centri assistenza
    - Call to action
--}}
@extends('layouts.app')

@section('title', 'Home - Sistema Assistenza Tecnica')

@section('content')
<div class="container-fluid">
    
    {{-- === HERO SECTION === --}}
    <section class="hero-section py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Assistenza Tecnica
                        <span class="text-warning">Professionale</span>
                    </h1>
                    <p class="lead mb-4">
                        Sistema completo per la gestione dell'assistenza tecnica sui nostri elettrodomestici. 
                        Accedi a soluzioni rapide per i malfunzionamenti più comuni e trova il centro assistenza più vicino.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-box me-2"></i>Esplora Catalogo
                        </a>
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-geo-alt me-2"></i>Trova Centro Assistenza
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="bi bi-tools display-1 text-warning mb-4"></i>
                    <div class="row text-center stats-hero">
                        <div class="col-3">
                            <div class="stat-item">
                                <h3 class="h1 fw-bold text-light">{{ $stats['prodotti_totali'] ?? '150+' }}</h3>
                                <p class="mb-0 small">Prodotti</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <h3 class="h1 fw-bold text-light">{{ $stats['centri_totali'] ?? '25+' }}</h3>
                                <p class="mb-0 small">Centri</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <h3 class="h1 fw-bold text-light">{{ $stats['soluzioni_totali'] ?? '500+' }}</h3>
                                <p class="mb-0 small">Soluzioni</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <h3 class="h1 fw-bold text-light">{{ $stats['tecnici_totali'] ?? '50+' }}</h3>
                                <p class="mb-0 small">Tecnici</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        
        {{-- === RICERCA RAPIDA === --}}
        <section class="mb-5">
            <div class="card card-custom shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida Prodotti
                    </h2>
                    <form action="{{ route('prodotti.pubblico.index') }}" method="GET" class="row g-3" id="search-form">
                        <div class="col-md-6">
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control form-control-lg pe-5" 
                                       name="search" 
                                       placeholder="Cerca prodotto (es: lavatrice, lav*)"
                                       value="{{ request('search') }}"
                                       id="search-input"
                                       autocomplete="off">
                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-lightbulb me-1"></i>
                                Usa * alla fine per ricerche parziali (es: "lav*" per lavatrici, lavastoviglie, ecc.)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="categoria" class="form-select form-select-lg" id="categoria-select">
                                <option value="">Tutte le categorie</option>
                                @if(isset($categorie_stats) && count($categorie_stats) > 0)
                                    @foreach($categorie_stats as $key => $info)
                                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                            {{ $info['label'] ?? ucfirst($key) }} ({{ $info['count'] ?? 0 }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-1"></i>Cerca
                            </button>
                        </div>
                    </form>
                    
                    {{-- Risultati ricerca AJAX --}}
                    <div id="search-results" class="mt-3" style="display: none;">
                        <div class="search-results-container">
                            {{-- I risultati verranno inseriti qui dal JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === INFORMAZIONI AZIENDA === --}}
        <section class="mb-5">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="h3 mb-4">
                                <i class="bi bi-building text-primary me-2"></i>
                                La Nostra Azienda
                            </h2>
                            <p class="lead">
                                <strong>TechSupport Pro</strong> è leader nel settore degli elettrodomestici da oltre 
                                <strong>{{ $stats['anni_esperienza'] ?? 30 }} anni</strong>, 
                                con una rete capillare di centri assistenza su tutto il territorio nazionale.
                            </p>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h5 class="mb-3">
                                            <i class="bi bi-geo-alt text-primary me-2"></i>
                                            Sede Principale
                                        </h5>
                                        <p class="mb-1">Via dell'Industria, 123</p>
                                        <p class="mb-1">60121 Ancona (AN)</p>
                                        <p class="mb-3">Italia</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h5 class="mb-3">
                                            <i class="bi bi-telephone text-primary me-2"></i>
                                            Contatti
                                        </h5>
                                        <p class="mb-1">
                                            <strong>Tel:</strong> 
                                            <a href="tel:+390711234567" class="text-decoration-none">+39 071 123 4567</a>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Email:</strong> 
                                            <a href="mailto:info@techsupportpro.it" class="text-decoration-none">info@techsupportpro.it</a>
                                        </p>
                                        <p class="mb-3">
                                            <strong>Assistenza:</strong> 
                                            <a href="mailto:assistenza@techsupportpro.it" class="text-decoration-none">assistenza@techsupportpro.it</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('azienda') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Scopri di più
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="h4 mb-4">
                                <i class="bi bi-award text-warning me-2"></i>
                                Certificazioni e Qualità
                            </h3>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-shield-check display-6 text-success mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">ISO 9001</p>
                                        <small class="text-muted">Qualità</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-leaf display-6 text-success mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">Eco-Friendly</p>
                                        <small class="text-muted">Ambiente</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-star-fill display-6 text-warning mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">5 Stelle</p>
                                        <small class="text-muted">Valutazione</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-headset display-6 text-info mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">24/7</p>
                                        <small class="text-muted">Supporto</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === CATEGORIE PRODOTTI === --}}
        @if(isset($categorie_stats) && count($categorie_stats) > 0)
        <section class="mb-5">
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-grid text-primary me-2"></i>
                Categorie Prodotti
            </h2>
            <div class="row g-4">
                @foreach($categorie_stats as $key => $info)
                    @php
                        // Mappatura icone per categorie
                        $icons = [
                            'lavatrice' => 'bi-water',
                            'lavastoviglie' => 'bi-droplet',
                            'forno' => 'bi-fire',
                            'frigorifero' => 'bi-snow',
                            'asciugatrice' => 'bi-wind',
                            'condizionatore' => 'bi-thermometer',
                            'microonde' => 'bi-lightning',
                            'aspirapolvere' => 'bi-fan',
                            'ferro_stiro' => 'bi-iron',
                            'piccoli_elettrodomestici' => 'bi-gear',
                            'elettrodomestici' => 'bi-house',
                            'climatizzazione' => 'bi-thermometer-half',
                            'cucina' => 'bi-cup-hot',
                            'lavanderia' => 'bi-water',
                            'riscaldamento' => 'bi-fire',
                            'altro' => 'bi-tools'
                        ];
                        $icon = $icons[$key] ?? 'bi-gear';
                        $label = $info['label'] ?? ucfirst(str_replace('_', ' ', $key));
                        $count = $info['count'] ?? 0;
                    @endphp
                    @if($count > 0)
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('prodotti.categoria', $key) }}" class="text-decoration-none">
                                <div class="card card-custom h-100 text-center category-card">
                                    <div class="card-body">
                                        <i class="bi {{ $icon }} display-4 text-primary mb-3"></i>
                                        <h5 class="card-title">{{ $label }}</h5>
                                        <p class="text-muted mb-0">
                                            <strong>{{ $count }}</strong> prodotti disponibili
                                        </p>
                                        <div class="mt-3">
                                            <span class="badge bg-primary">Visualizza</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        {{-- === ACCESSO PER LIVELLI === --}}
        <section class="mb-5">
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-people text-primary me-2"></i>
                Accesso per Operatori
            </h2>
            <div class="row g-4">
                {{-- Tecnici --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-person-gear display-4 text-info mb-3"></i>
                            <h5 class="card-title">Tecnici Specializzati</h5>
                            <p class="card-text">
                                Accesso completo a malfunzionamenti e soluzioni tecniche per tutti i prodotti del catalogo.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Visualizza tutti i malfunzionamenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Accesso alle soluzioni tecniche
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Segnala nuovi problemi
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Storico interventi personali
                                </li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-info">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Tecnico
                                </a>
                            @else
                                @if(Auth::user()->livello_accesso >= 2)
                                    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Tecnico
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-info">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Tecnico
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                {{-- Staff --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-person-badge display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Staff Aziendale</h5>
                            <p class="card-text">
                                Gestione completa di malfunzionamenti e soluzioni per i prodotti assegnati al proprio reparto.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Tutte le funzioni del Tecnico
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Crea nuovi malfunzionamenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Modifica e aggiorna soluzioni
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Statistiche e report attività
                                </li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-warning">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Staff
                                </a>
                            @else
                                @if(Auth::user()->livello_accesso >= 3)
                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-warning">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Staff
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-warning">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Staff
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                {{-- Amministratori --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-person-fill-gear display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Amministratori</h5>
                            <p class="card-text">
                                Controllo completo del sistema: gestione utenti, prodotti, centri assistenza e configurazioni avanzate.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Tutte le funzioni precedenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Gestione completa utenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Amministrazione prodotti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Statistiche e manutenzione
                                </li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Admin
                                </a>
                            @else
                                @if(Auth::user()->livello_accesso >= 4)
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Admin
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-danger">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Admin
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === CENTRI ASSISTENZA === --}}
        <section class="mb-5">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h2 class="h3 mb-4">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Rete Centri Assistenza
                    </h2>
                    <p class="lead">
                        I nostri centri di assistenza sono distribuiti su tutto il territorio nazionale 
                        per garantire un servizio rapido e professionale.
                    </p>
                    
                    <div class="centri-list mb-4">
                        @if(isset($centri_principali) && count($centri_principali) > 0)
                            @foreach($centri_principali as $centro)
                                <div class="centro-item mb-3">
                                    <div class="card border-start border-primary border-3 shadow-sm">
                                        <div class="card-body py-3">
                                            <h6 class="card-title mb-1 fw-bold">{{ $centro->nome }}</h6>
                                            <p class="card-text small text-muted mb-1">
                                                <i class="bi bi-geo-alt me-1 text-primary"></i>
                                                {{ $centro->indirizzo ?? 'Indirizzo non disponibile' }}, 
                                                {{ $centro->citta }} 
                                                @if($centro->provincia)
                                                    ({{ $centro->provincia }})
                                                @endif
                                            </p>
                                            @if($centro->telefono)
                                                <p class="card-text small text-muted mb-0">
                                                    <i class="bi bi-telephone me-1 text-primary"></i>
                                                    <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                        {{ $centro->telefono }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Informazioni sui centri assistenza in aggiornamento.
                            </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('centri.index') }}" class="btn btn-primary">
                        <i class="bi bi-geo-alt me-2"></i>Vedi Tutti i Centri
                    </a>
                </div>
                
                <div class="col-lg-6">
                    <div class="card card-custom shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-clock text-primary me-2"></i>
                                Orari di Servizio
                            </h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="service-time mb-4">
                                        <h6 class="fw-bold text-primary">Assistenza Telefonica</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 8:00-18:00
                                        </p>
                                        <p class="mb-3">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: 8:00-13:00
                                        </p>
                                    </div>
                                    
                                    <div class="service-time">
                                        <h6 class="fw-bold text-primary">Interventi a Domicilio</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 9:00-17:00
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: Su appuntamento
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="service-time mb-4">
                                        <h6 class="fw-bold text-primary">Centri Assistenza</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 8:30-17:30
                                        </p>
                                        <p class="mb-3">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: 8:30-12:30
                                        </p>
                                    </div>
                                    
                                    <div class="service-time">
                                        <h6 class="fw-bold text-primary">Supporto Online</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-clock me-1"></i>
                                            24/7 attraverso
                                        </p>
                                        <p class="mb-0">questo portale</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === STATISTICHE === --}}
        @if(isset($stats) && count($stats) > 0)
        <section class="mb-5">
            <div class="card card-custom bg-primary text-white shadow-lg">
                <div class="card-body py-5">
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche del Sistema
                    </h2>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-box display-4 mb-3"></i>
                                <h3 class="h2 fw-bold">{{ $stats['prodotti_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Prodotti Attivi</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-tools display-4 mb-3"></i>
                                <h3 class="h2 fw-bold">{{ $stats['soluzioni_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Soluzioni Disponibili</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-geo-alt display-4 mb-3"></i>
                                <h3 class="h2 fw-bold">{{ $stats['centri_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Centri Assistenza</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-people display-4 mb-3"></i>
                                <h3 class="h2 fw-bold">{{ $stats['tecnici_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Tecnici Specializzati</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- === CALL TO ACTION === --}}
        <section class="mb-5">
            <div class="card card-custom bg-light shadow-sm">
                <div class="card-body text-center py-5">
                    <h2 class="h3 mb-4">Hai bisogno di assistenza?</h2>
                    <p class="lead mb-4 text-muted">
                        Il nostro team di esperti è sempre pronto ad aiutarti a risolvere 
                        qualsiasi problema con i tuoi elettrodomestici.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-search me-2"></i>Cerca Soluzione
                        </a>
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-telephone me-2"></i>Contatta Centro
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-person me-2"></i>Accedi al Sistema
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>Vai alla Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        {{-- === FOOTER INFORMATIVO === --}}
        <section class="mb-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check display-4 text-success mb-3"></i>
                            <h5 class="card-title">Garanzia Estesa</h5>
                            <p class="card-text text-muted">
                                Tutti i nostri interventi sono coperti da garanzia estesa 
                                per garantire la massima tranquillità.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-charge display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Intervento Rapido</h5>
                            <p class="card-text text-muted">
                                Tempi di intervento ridotti grazie alla nostra rete 
                                capillare di tecnici specializzati.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-heart display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Soddisfazione Cliente</h5>
                            <p class="card-text text-muted">
                                La soddisfazione dei nostri clienti è la nostra priorità 
                                assoluta, con oltre il 95% di feedback positivi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

{{-- JavaScript per funzionalità dinamiche --}}
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

{{-- Stili CSS personalizzati per la homepage --}}
@push('styles')
<style>
/* === HERO SECTION === */
.hero-section {
    background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
    background-size: cover;
    pointer-events: none;
}

.stats-hero .stat-item {
    transition: transform 0.3s ease;
}

.stats-hero .stat-item:hover {
    transform: translateY(-5px);
}

/* === CARD PERSONALIZZATE === */
.card-custom {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-radius: 12px;
}

.card-custom:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

/* === CATEGORY CARDS === */
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
    overflow: hidden;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.category-card .card-body {
    padding: 2rem 1.5rem;
}

.category-card .display-4 {
    transition: transform 0.3s ease;
}

.category-card:hover .display-4 {
    transform: scale(1.1);
}

/* === SEARCH RESULTS === */
.search-results-container {
    max-height: 400px;
    overflow-y: auto;
}

.search-result-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.search-result-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* === CERTIFICATION ITEMS === */
.certification-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.certification-item:hover {
    background-color: #f8f9fa !important;
    transform: scale(1.05);
}

.certification-item .display-6 {
    transition: transform 0.3s ease;
}

.certification-item:hover .display-6 {
    transform: scale(1.1);
}

/* === INFO ITEMS === */
.info-item h5 {
    border-bottom: 2px solid transparent;
    padding-bottom: 0.5rem;
    transition: border-color 0.3s ease;
}

.info-item:hover h5 {
    border-bottom-color: #007bff;
}

/* === SERVICE TIME === */
.service-time {
    padding: 1rem;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.service-time:hover {
    background-color: #f8f9fa;
}

/* === CENTRI LIST === */
.centro-item {
    transition: transform 0.2s ease;
}

.centro-item:hover {
    transform: translateX(5px);
}

/* === ANIMAZIONI === */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease;
}

/* === SPINNER PERSONALIZZATO === */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* === PULSANTI === */
.btn {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 600;
}

/* === BADGE === */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.8rem;
}

/* === ICONE === */
.display-1, .display-4, .display-6 {
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* === FORM CONTROLS === */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-control-lg {
    border-radius: 8px;
}

.form-select-lg {
    border-radius: 8px;
}

/* === LINKS === */
a {
    transition: color 0.2s ease;
}

a:hover {
    text-decoration: none !important;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .hero-section .display-4 {
        font-size: 2.5rem;
    }
    
    .hero-section .lead {
        font-size: 1.1rem;
    }
    
    .stats-hero .col-3 {
        margin-bottom: 1rem;
    }
    
    .stats-hero h3 {
        font-size: 1.75rem;
    }
    
    .btn-lg {
        font-size: 0.95rem;
        padding: 0.6rem 1.5rem;
    }
    
    .card-body {
        padding: 1.25rem 1rem;
    }
    
    .category-card .card-body {
        padding: 1.5rem 1rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .display-6 {
        font-size: 1.25rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .hero-section {
        padding: 3rem 0;
    }
    
    .hero-section .display-4 {
        font-size: 2rem;
    }
    
    .hero-section .lead {
        font-size: 1rem;
    }
    
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .stats-hero h3 {
        font-size: 1.5rem;
    }
    
    .card-custom {
        margin-bottom: 1rem;
    }
    
    .search-results-container {
        max-height: 300px;
    }
}

/* === DARK MODE SUPPORT === */
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    .bg-light {
        background-color: #e9ecef !important;
    }
}

/* === PRINT STYLES === */
@media print {
    .hero-section,
    .btn,
    #search-results {
        display: none !important;
    }
    
    .card-custom {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .container-fluid {
        max-width: none;
        padding: 0;
    }
}

/* === HIGH CONTRAST MODE === */
@media (prefers-contrast: high) {
    .btn {
        border-width: 2px;
    }
    
    .card-custom {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid;
    }
}

/* === REDUCED MOTION === */
@media (prefers-reduced-motion: reduce) {
    .card-custom,
    .btn,
    .category-card,
    .certification-item,
    .search-result-item,
    .centro-item,
    .info-item h5,
    .service-time,
    .stats-hero .stat-item {
        transition: none;
    }
    
    .fade-in-up,
    @keyframes fadeInUp {
        animation: none;
    }
}

/* === ACCESSIBILITÀ === */
.visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Focus visible per navigazione da tastiera */
.btn:focus-visible,
.form-control:focus-visible,
.form-select:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}
</style>
@endpush