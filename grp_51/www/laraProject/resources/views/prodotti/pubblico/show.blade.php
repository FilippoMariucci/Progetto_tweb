{{-- 
    VISTA PUBBLICA PRODOTTO SINGOLO - STILE UNIFICATO SENZA MALFUNZIONAMENTI
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Vista dettagliata prodotto per utenti pubblici (Livello 1)
    CARATTERISTICHE: Layout identico alla vista tecnica ma senza sezione malfunzionamenti
    LINGUAGGIO: Blade template (PHP con sintassi Laravel Blade)
    LIVELLO ACCESSO: 1 (Pubblico - solo informazioni base e assistenza)
    PATH: resources/views/prodotti/pubblico/show.blade.php
--}}

{{-- 
    EXTENDS: Eredita il layout base dell'applicazione
    LINGUAGGIO: Blade directive - specifica il template padre comune
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo dinamico specifico per vista pubblica
    LINGUAGGIO: Blade directive + PHP string concatenation + prodotto name
--}}
@section('title', $prodotto->nome . ' - Scheda Tecnica')

{{-- 
    SECTION CONTENT: Contenuto principale vista pubblica prodotto
    LINGUAGGIO: Blade directive - corpo completo senza dati sensibili
--}}
@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- === HEADER COMPATTO UNIFICATO ===
         DESCRIZIONE: Header identico alla vista tecnica ma con testo pubblico
         LINGUAGGIO: HTML Bootstrap + Blade conditionals per upgrade vista
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO PRODOTTO: Nome prodotto dal database
                LINGUAGGIO: Blade output {{ }} con oggetto Eloquent
            --}}
            <h2 class="mb-1">{{ $prodotto->nome }}</h2>
            <p class="text-muted small mb-0">Scheda tecnica pubblica</p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                LINK BACK CATALOG: Torna al catalogo pubblico
                LINGUAGGIO: HTML link + Laravel route() helper per vista pubblica
            --}}
            <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Catalogo
            </a>
            {{-- 
                UPGRADE VISTA CONDIZIONALE: Link vista tecnica se utente autorizzato
                LINGUAGGIO: Blade @auth + Laravel Auth facade + Model methods
            --}}
            @auth
                @if(Auth::user()->canViewMalfunzionamenti())
                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-info">
                        <i class="bi bi-tools"></i> Vista Tecnica
                    </a>
                @endif
            @endauth
        </div>
    </div>

    {{-- 
        BREADCRUMB PUBBLICO: Navigazione gerarchica per vista pubblica
        DESCRIZIONE: Percorso specifico per utenti non autenticati
        LINGUAGGIO: HTML nav + Blade conditionals + Laravel helpers
    --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.pubblico.index') }}">Catalogo Pubblico</a></li>
            {{-- 
                BREADCRUMB CATEGORIA: Link filtro categoria se presente
                LINGUAGGIO: Blade @if + PHP urlencode() + string manipulation
            --}}
            @if($prodotto->categoria)
                <li class="breadcrumb-item">
                    <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($prodotto->categoria) }}">
                        {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                    </a>
                </li>
            @endif
            {{-- Prodotto corrente troncato --}}
            <li class="breadcrumb-item active">{{ Str::limit($prodotto->nome, 30) }}</li>
        </ol>
    </nav>

    {{-- === ALERT TECNICO DISPONIBILE ===
         DESCRIZIONE: Promozione upgrade a vista tecnica per utenti autorizzati
         LINGUAGGIO: Blade @auth + nested conditionals + Bootstrap alert
    --}}
    @auth
        @if(Auth::user()->canViewMalfunzionamenti())
            <div class="alert alert-info border-0 shadow-sm mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Vista Tecnica Disponibile</strong> - 
                        Accedi alla vista completa con malfunzionamenti e soluzioni tecniche.
                    </div>
                    {{-- 
                        CALL-TO-ACTION UPGRADE: Pulsante per vista completa
                        LINGUAGGIO: HTML button + Laravel route() con stesso parametro
                    --}}
                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-tools me-1"></i>Vista Completa
                    </a>
                </div>
            </div>
        @endif
    @endauth

    {{-- === LAYOUT ORIZZONTALE PRINCIPALE UNIFICATO ===
         DESCRIZIONE: Layout identico alla vista tecnica per consistenza UX
         LINGUAGGIO: Bootstrap Grid System responsive
    --}}
    <div class="row g-4">
        
        {{-- === COLONNA IMMAGINE E INFO (stile identico alla vista tecnica) ===
             DESCRIZIONE: Colonna sinistra con immagine e informazioni base
             LINGUAGGIO: Bootstrap responsive columns + card structure
        --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="position-relative">
                    {{-- 
                        GESTIONE IMMAGINE IDENTICA: Stesso comportamento vista tecnica
                        LINGUAGGIO: Blade @if + Laravel asset() + CSS object-fit
                    --}}
                    @if($prodotto->foto)
                        {{-- 
                            IMMAGINE CORRETTA: Object-fit contain per proporzioni + modal JS
                            SCOPO: Mantiene aspect ratio + click per ingrandimento
                            LINGUAGGIO: HTML img + CSS inline + JavaScript onclick function
                        --}}
                        <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 280px; object-fit: contain; background-color: #f8f9fa; cursor: pointer; padding: 1rem;"
                             onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                    @else
                        {{-- Placeholder identico per coerenza visiva --}}
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 280px;">
                            <div class="text-center">
                                <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 small">Immagine non disponibile</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- 
                        BADGE OVERLAY IDENTICI: Stesse posizioni e stili vista tecnica
                        LINGUAGGIO: CSS absolute positioning + Bootstrap badges
                    --}}
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                    </div>
                    
                    {{-- Badge prezzo pubblico --}}
                    @if($prodotto->prezzo)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- 
                    AZIONI IMMAGINE: Stesse funzionalità vista tecnica
                    LINGUAGGIO: Blade @if + JavaScript modal + HTML download
                --}}
                @if($prodotto->foto)
                    <div class="card-body py-2">
                        <div class="d-flex gap-1">
                            {{-- Pulsante zoom identico --}}
                            <button type="button" class="btn btn-primary btn-sm flex-fill" 
                                    onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                                <i class="bi bi-zoom-in me-1"></i>Ingrandisci
                            </button>
                            {{-- Download immagine identico --}}
                            <a href="{{ asset('storage/' . $prodotto->foto) }}" 
                               download="{{ Str::slug($prodotto->nome) }}.jpg" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- === INFO TECNICHE COMPATTE (stile identico) ===
                 DESCRIZIONE: Card informazioni base adattata per pubblico
                 LINGUAGGIO: Bootstrap card + modifiche semantiche per pubblico
            --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        {{-- Icona e titolo adattati per pubblico --}}
                        <i class="bi bi-info-circle me-1"></i>Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 text-center">
                        {{-- 
                            DATA CATALOGAZIONE: Quando prodotto aggiunto al catalogo
                            LINGUAGGIO: Blade @if + Laravel Carbon date formatting
                        --}}
                        @if($prodotto->created_at)
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Catalogato</small>
                                    <strong class="small">{{ $prodotto->created_at->format('d/m/Y') }}</strong>
                                </div>
                            </div>
                        @endif
                        {{-- Categoria formattata --}}
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Categoria</small>
                                <strong class="small">{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                            </div>
                        </div>
                        {{-- Modello se disponibile --}}
                        @if($prodotto->modello)
                            <div class="col-12">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Modello</small>
                                    <code class="small">{{ $prodotto->modello }}</code>
                                </div>
                            </div>
                        @endif
                        
                        {{-- 
                            STAFF REFERENTE: Info pubbliche su referente tecnico
                            LINGUAGGIO: Blade @if + Laravel relationships + public-friendly labels
                        --}}
                        @if($prodotto->staffAssegnato)
                            <div class="col-12">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Referente Tecnico</small>
                                    <span class="badge bg-info small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $prodotto->staffAssegnato->nome_completo }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- === ASSISTENZA TECNICA BOX ===
                 DESCRIZIONE: Card dedicata per promozione servizi assistenza
                 LINGUAGGIO: Bootstrap card + call-to-action per pubblico
            --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-headset me-1"></i>Assistenza Tecnica
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2">
                        {{-- Metriche servizio --}}
                        <div class="col-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <div class="fw-bold text-success">24/7</div>
                                <small class="text-muted">Supporto</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                <div class="fw-bold text-info">Gratuita</div>
                                <small class="text-muted">Consulenza</small>
                            </div>
                        </div>
                        {{-- 
                            AZIONI ASSISTENZA: Links a servizi pubblici
                            LINGUAGGIO: Bootstrap grid + Laravel route() helpers
                        --}}
                        <div class="col-12 mt-2">
                            <div class="d-grid gap-1">
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-geo-alt me-1"></i>Trova Centro Assistenza
                                </a>
                                <a href="{{ route('contatti') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Contatta Supporto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- === COLONNA INFORMAZIONI PRINCIPALE (stile identico) ===
             DESCRIZIONE: Colonna destra con dettagli prodotto pubblici
             LINGUAGGIO: Bootstrap responsive column + content adaptation
        --}}
        <div class="col-lg-8 col-md-7">
            
            {{-- 
                HEADER PRODOTTO: Titolo e badge informativi per pubblico
                LINGUAGGIO: HTML flexbox + Blade conditionals + badge system
            --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-2">{{ $prodotto->nome }}</h1>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        {{-- Badge modello --}}
                        @if($prodotto->modello)
                            <span class="badge bg-secondary small">{{ $prodotto->modello }}</span>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <span class="badge bg-primary small">
                            <i class="bi bi-tag me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                        
                        {{-- Badge referente se presente --}}
                        @if($prodotto->staffAssegnato)
                            <span class="badge bg-info small">
                                <i class="bi bi-person-badge me-1"></i>
                                Ref: {{ Str::limit($prodotto->staffAssegnato->nome_completo, 20) }}
                            </span>
                        @endif
                        
                        {{-- 
                            BADGE VISTA PUBBLICA: Indica livello accesso corrente
                            LINGUAGGIO: Bootstrap badge + visual feedback per utente
                        --}}
                        <span class="badge bg-success small">
                            <i class="bi bi-eye me-1"></i>
                            Vista Pubblica
                        </span>
                    </div>
                </div>
                {{-- Prezzo prominente se disponibile --}}
                @if($prodotto->prezzo)
                    <div class="text-end">
                        <h4 class="text-success mb-0">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</h4>
                    </div>
                @endif
            </div>
            
            {{-- 
                DESCRIZIONE PRODOTTO: Testo descrittivo completo
                LINGUAGGIO: Blade @if + HTML paragraph + safe output
            --}}
            @if($prodotto->descrizione)
                <div class="mb-3">
                    <p class="text-muted">{{ $prodotto->descrizione }}</p>
                </div>
            @endif
            
            {{-- === SCHEDA TECNICA COMPATTA (stile identico) ===
                 DESCRIZIONE: Informazioni tecniche complete per pubblico
                 LINGUAGGIO: Bootstrap card + responsive grid + security
            --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i>Scheda Tecnica Completa
                    </h6>
                </div>
                <div class="card-body py-3">
                    
                    {{-- Layout responsive per scheda tecnica --}}
                    <div class="row g-3">
                        
                        {{-- 
                            NOTE TECNICHE: Specifiche tecniche pubbliche
                            LINGUAGGIO: Blade @if + PHP nl2br() + XSS protection e()
                        --}}
                        @if($prodotto->note_tecniche)
                            <div class="col-lg-4">
                                <h6 class="text-primary small fw-semibold">
                                    <i class="bi bi-gear me-1"></i>Specifiche Tecniche
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-primary border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->note_tecniche)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- 
                            ISTRUZIONI INSTALLAZIONE: Guide pubbliche installazione
                            LINGUAGGIO: Blade @if + secure HTML output + styling
                        --}}
                        @if($prodotto->modalita_installazione)
                            <div class="col-lg-4">
                                <h6 class="text-success small fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Modalità Installazione
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-success border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_installazione)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- 
                            MODALITÀ D'USO: Istruzioni utilizzo per pubblico
                            LINGUAGGIO: Blade @if + XSS protection + formatting
                        --}}
                        @if($prodotto->modalita_uso)
                            <div class="col-lg-4">
                                <h6 class="text-info small fw-semibold">
                                    <i class="bi bi-book me-1"></i>Modalità d'Uso
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-info border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_uso)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- 
                            STATO VUOTO PUBBLICO: Messaggio quando scheda incompleta
                            LINGUAGGIO: Blade complex conditional + call-to-action pubblico
                        --}}
                        @if(!$prodotto->note_tecniche && !$prodotto->modalita_installazione && !$prodotto->modalita_uso)
                            <div class="col-12 text-center py-3">
                                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">
                                    Scheda tecnica in aggiornamento.
                                    {{-- Link contatti per pubblico invece di edit admin --}}
                                    <br><a href="{{ route('contatti') }}" class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="bi bi-envelope me-1"></i>Richiedi informazioni
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === SEZIONE ASSISTENZA TECNICA (invece dei malfunzionamenti) ===
                 DESCRIZIONE: Sostituisce sezione malfunzionamenti con servizi pubblici
                 LINGUAGGIO: Bootstrap card + service grid + conversion funnel
            --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-headset me-1"></i>
                            Supporto e Assistenza Tecnica
                        </h6>
                    </div>
                </div>
                <div class="card-body py-3">
                    
                    {{-- 
                        GRIGLIA SERVIZI ASSISTENZA: Layout servizi per pubblico
                        LINGUAGGIO: Bootstrap grid + service cards + call-to-action
                    --}}
                    <div class="row g-3">
                        
                        {{-- 
                            SUPPORTO TECNICO SPECIALIZZATO: Servizio principale
                            LINGUAGGIO: Bootstrap card + conditional buttons per auth state
                        --}}
                        <div class="col-lg-6">
                            <div class="card border-start border-primary border-3 h-100">
                                <div class="card-body py-3">
                                    <h6 class="card-title mb-2 fw-bold small text-primary">
                                        <i class="bi bi-tools me-1"></i>Supporto Tecnico Specializzato
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        I nostri tecnici qualificati sono pronti ad assisterti per installazione, 
                                        configurazione e risoluzione di eventuali problemi tecnici.
                                    </p>
                                    {{-- Badge servizio --}}
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-primary small">24/7</span>
                                        <span class="badge bg-success small">Gratuito</span>
                                        <span class="badge bg-info small">Specializzato</span>
                                    </div>
                                    {{-- 
                                        AZIONI CONDIZIONALI: Diversi per guest vs authenticated
                                        LINGUAGGIO: Blade @guest/@else + nested conditionals
                                    --}}
                                    <div class="d-grid gap-1">
                                        @guest
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-person-check me-1"></i>Accesso Tecnici
                                            </a>
                                        @else
                                            @if(Auth::user()->canViewMalfunzionamenti())
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-tools me-1"></i>Area Tecnica
                                                </a>
                                            @else
                                                <a href="{{ route('contatti') }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-headset me-1"></i>Richiedi Supporto
                                                </a>
                                            @endif
                                        @endguest
                                        {{-- Link sempre disponibile --}}
                                        <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 
                            DOCUMENTAZIONE E GUIDE: Servizio secondario
                            LINGUAGGIO: Bootstrap card + service description + badge system
                        --}}
                        <div class="col-lg-6">
                            <div class="card border-start border-success border-3 h-100">
                                <div class="card-body py-3">
                                    <h6 class="card-title mb-2 fw-bold small text-success">
                                        <i class="bi bi-book me-1"></i>Guide e Documentazione
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        Accedi alle guide di installazione, manuali d'uso e documentazione tecnica 
                                        per sfruttare al meglio le funzionalità del prodotto.
                                    </p>
                                    {{-- Badge tipi documentazione --}}
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-success small">PDF</span>
                                        <span class="badge bg-warning small">Video Guide</span>
                                        <span class="badge bg-info small">FAQ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        CALL TO ACTION ACCESSO TECNICO: Promozione per guest users
                        LINGUAGGIO: Blade @guest + Bootstrap alert + conversion messaging
                    --}}
                    @guest
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong>Sei un tecnico autorizzato?</strong> 
                                            Accedi per visualizzare informazioni dettagliate sui malfunzionamenti e le relative soluzioni tecniche.
                                        </div>
                                        {{-- Pulsante conversione --}}
                                        <a href="{{ route('login') }}" class="btn btn-info btn-sm ms-2">
                                            <i class="bi bi-person-check me-1"></i>Accedi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI CORRELATI COMPATTI (se disponibili) ===
         DESCRIZIONE: Sezione suggerimenti prodotti simili per pubblico
         LINGUAGGIO: Blade conditionals + Laravel Collection + responsive grid
    --}}
    @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-collection text-info me-1"></i>
                            Altri Prodotti della Categoria "{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}"
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            {{-- 
                                LOOP PRODOTTI CORRELATI: Lista prodotti simili
                                LINGUAGGIO: Blade @foreach + Laravel Collection take() + route linking
                            --}}
                            @foreach($prodottiCorrelati->take(4) as $correlato)
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                {{-- 
                                                    THUMBNAIL PRODOTTO: Immagine piccola prodotto correlato
                                                    LINGUAGGIO: Blade @if/@else + Laravel asset() + CSS object-fit
                                                --}}
                                                @if($correlato->foto)
                                                    <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: contain; background-color: #f8f9fa;">
                                                @else
                                                    {{-- Placeholder per prodotto senza immagine --}}
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-grow-1">
                                                    {{-- 
                                                        LINK PRODOTTO CORRELATO: Nome prodotto troncato linkabile
                                                        LINGUAGGIO: HTML h6 + Laravel route() + Str::limit() helper
                                                    --}}
                                                    <h6 class="mb-1 small">
                                                        <a href="{{ route('prodotti.pubblico.show', $correlato) }}" 
                                                           class="text-decoration-none">
                                                            {{ Str::limit($correlato->nome, 25) }}
                                                        </a>
                                                    </h6>
                                                    {{-- Modello con fallback --}}
                                                    <small class="text-muted d-block mb-1">
                                                        {{ $correlato->modello ?? 'Modello N/A' }}
                                                    </small>
                                                    {{-- 
                                                        BADGE STATO PUBBLICO: Info appropriate per pubblico
                                                        LINGUAGGIO: Blade @if + Bootstrap badges + PHP number_format()
                                                    --}}
                                                    <div class="d-flex gap-1">
                                                        @if($correlato->prezzo)
                                                            <span class="badge bg-success small">
                                                                €{{ number_format($correlato->prezzo, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                        <span class="badge bg-primary small">Disponibile</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- 
                            LINK CATALOGO CATEGORIA: Vedi tutti prodotti categoria
                            LINGUAGGIO: HTML link + Laravel route() + PHP urlencode() + string functions
                        --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($prodotto->categoria) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>
                                Vedi Tutti i Prodotti {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- === MODAL IMMAGINE IDENTICO ===
     DESCRIZIONE: Modal Bootstrap per zoom immagine (identico vista tecnica)
     LINGUAGGIO: HTML Bootstrap Modal + JavaScript integration
--}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                {{-- 
                    TITOLO MODAL DINAMICO: Popolato via JavaScript
                    LINGUAGGIO: HTML h5 + JavaScript DOM target ID
                --}}
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                {{-- 
                    IMMAGINE MODAL: Target JavaScript per src dinamico
                    LINGUAGGIO: HTML img + CSS object-fit + JavaScript manipulation
                --}}
                <img id="imageModalImg" src="" alt="" class="img-fluid w-100" style="object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === PUSH STYLES ===
     DESCRIZIONE: CSS identico alla vista tecnica per consistenza visiva
     LINGUAGGIO: Blade @push + CSS3 + Media queries responsive
--}}
@push('styles')
<style>
/* === STILI IDENTICI ALLA VISTA TECNICA ===
   LINGUAGGIO: CSS3 con proprietà standard
   SCOPO: Mantiene consistenza visiva tra vista pubblica e tecnica */

/* Card base con transizioni
   SCOPO: Comportamento uniforme tra viste pubbliche e tecniche */
.card {
    border-radius: 0.5rem; /* Angoli arrotondati uniformi */
    transition: all 0.2s ease; /* Transizione smooth per hover */
}

/* Hover effect identico per tutte le card */
.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important; /* Ombra consistente */
}

/* === IMMAGINE PRODOTTO CORRETTA ===
   LINGUAGGIO: CSS selectors + properties
   SCOPO: Comportamento immagini identico tra viste */
.product-image {
    transition: transform 0.3s ease; /* Transizione zoom uniforme */
    border-radius: 0.375rem; /* Angoli consistenti */
}

/* Zoom hover identico */
.product-image:hover {
    transform: scale(1.02); /* Stesso ingrandimento vista tecnica */
}

/* Badge compatti uniformi */
.badge.small {
    font-size: 0.7rem; /* Dimensione font consistente */
    padding: 0.25rem 0.5rem; /* Padding uniforme */
}

/* === PADDING COMPATTI IDENTICI ===
   LINGUAGGIO: CSS utility overrides
   SCOPO: Spaziature identiche tra viste per UX uniforme */

/* Card body padding - variante py-2 */
.card-body.py-2 {
    padding-top: 0.5rem !important; /* Stesso padding vista tecnica */
    padding-bottom: 0.5rem !important;
}

/* Card body padding - variante py-3 */
.card-body.py-3 {
    padding-top: 0.75rem !important; /* Consistenza con vista tecnica */
    padding-bottom: 0.75rem !important;
}

/* Card header compatto */
.card-header.py-2 {
    padding-top: 0.5rem !important; /* Header height uniforme */
    padding-bottom: 0.5rem !important;
}

/* Bordo spesso per evidenziazione */
.border-3 {
    border-width: 3px !important; /* Stessa larghezza bordi vista tecnica */
}

/* === BACKGROUND OPACITY PERSONALIZZATI ===
   LINGUAGGIO: CSS rgba + !important overrides
   SCOPO: Colori semi-trasparenti uniformi tra viste */

.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important; /* Blu trasparente */
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important; /* Giallo trasparente */
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important; /* Rosso trasparente */
}

.bg-info.bg-opacity-10 {
    background-color: rgba(13, 202, 240, 0.1) !important; /* Ciano trasparente */
}

.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important; /* Verde trasparente */
}

/* === ALERT PERSONALIZZATI ===
   LINGUAGGIO: CSS border-radius
   SCOPO: Styling uniforme alert tra viste */
.alert {
    border-radius: 0.5rem; /* Angoli arrotondati */
}

.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra profonda */
}

/* === RESPONSIVE DESIGN IDENTICO ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Comportamento responsive uniforme tra viste */

/* === TABLET E MOBILE (768px e sotto) === */
@media (max-width: 768px) {
    /* Container padding ridotto identico */
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Dimensione immagine mobile uniforme */
    .product-image {
        height: 180px !important;
        padding: 0.5rem;
    }
    
    /* Grid responsive identica */
    .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row.g-3 > * {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Titoli mobile uniformi */
    .h2 {
        font-size: 1.2rem;
    }
    
    /* Gap badge mobile */
    .d-flex.flex-wrap.gap-1 {
        gap: 0.25rem !important;
    }
}

/* === STATI INTERATTIVI UNIFORMI ===
   LINGUAGGIO: CSS pseudo-classes
   SCOPO: Feedback utente identico tra viste */

/* Stati loading uniformi */
.btn:disabled {
    opacity: 0.6; /* Opacità disabilitato */
    cursor: not-allowed; /* Cursore not-allowed */
}

/* Spinner dimensioni uniformi */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === ANIMAZIONI HOVER IDENTICHE ===
   LINGUAGGIO: CSS transforms + transitions
   SCOPO: Micro-interazioni uniformi */

/* Hover pulsanti identico */
.btn:hover {
    transform: translateY(-1px); /* Solleva pulsante */
}

/* Hover badge identico */
.badge:hover {
    transform: scale(1.05); /* Ingrandisce badge */
}

/* === FOCUS ACCESSIBILITY UNIFORME ===
   LINGUAGGIO: CSS :focus + box-shadow
   SCOPO: Accessibilità keyboard navigation */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); /* Outline focus */
}

/* === BREADCRUMB PERSONALIZZATO IDENTICO ===
   LINGUAGGIO: CSS pseudo-element
   SCOPO: Separatori breadcrumb uniformi */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›"; /* Separatore personalizzato */
    color: #6c757d; /* Colore grigio Bootstrap */
}

/* === MODAL IMMAGINE IDENTICO ===
   LINGUAGGIO: CSS viewport units
   SCOPO: Comportamento modal uniforme */
#imageModal .modal-body img {
    max-height: 80vh; /* Limita altezza viewport */
}

/* === SCROLLBAR PERSONALIZZATA IDENTICA ===
   LINGUAGGIO: CSS webkit pseudo-elements
   SCOPO: Scrollbar uniforme tra viste */

.overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === SMOOTH SCROLLING UNIFORME ===
   LINGUAGGIO: CSS scroll-behavior
   SCOPO: Navigazione fluida tra sezioni */
html {
    scroll-behavior: smooth;
}

/* === EVIDENZIAZIONE SEZIONI ===
   LINGUAGGIO: CSS animations + keyframes
   SCOPO: Feedback quando si accede a sezione specifica */
.section-highlight {
    animation: highlight 2s ease-in-out;
}

@keyframes highlight {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    100% { background-color: transparent; }
}
</style>
@endpush

{{-- === PUSH SCRIPTS ===
     DESCRIZIONE: JavaScript per funzionalità vista pubblica
     LINGUAGGIO: Blade @push + JavaScript ES6+ + data injection
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE DATI PAGINA ===
   LINGUAGGIO: JavaScript ES6 object initialization
   SCOPO: Prepara dati per funzioni JavaScript vista pubblica */

// Inizializza oggetto dati globale
window.PageData = window.PageData || {};

/* === INIEZIONE DATI SERVER-SIDE ===
   LINGUAGGIO: Blade @json + JavaScript assignment
   SCOPO: Trasferisce dati PHP sicuri a JavaScript client-side */

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

/* === DATI SESSIONE E PERMESSI ===
   LINGUAGGIO: JavaScript object + Blade session helpers + Auth
   SCOPO: Informazioni stato utente per logica client-side */

// Messaggi flash session per notifiche JavaScript
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionInfo = @json(session('info'));

// Permessi utente corrente per funzionalità condizionali
window.PageData.user_can_view_malfunctions = @json(Auth::check() && Auth::user()->canViewMalfunzionamenti());
window.PageData.is_authenticated = @json(Auth::check());
window.PageData.user_level = @json(Auth::check() ? Auth::user()->livello_accesso : 1);

/* === CONFIGURAZIONE VISTA PUBBLICA ===
   LINGUAGGIO: JavaScript object configuration
   SCOPO: Parametri specifici per comportamento vista pubblica */

window.PageData.viewConfig = {
    isPublicView: true,              // Flag vista pubblica
    showMalfunctions: false,         // NO malfunzionamenti per pubblico
    enableTechnicalUpgrade: @json(Auth::check() && Auth::user()->canViewMalfunzionamenti()),
    modalImageEnabled: true,         // Modal zoom abilitato
    downloadEnabled: true,           // Download immagine abilitato
    contactsRequired: !@json(Auth::check()) // Indirizza a contatti se guest
};

/* === URLS PER NAVIGAZIONE ===
   LINGUAGGIO: JavaScript object + Laravel route() helpers
   SCOPO: URLs per navigazione JavaScript senza hardcoding */

window.PageData.routes = {
    technicalView: @json(Auth::check() && Auth::user()->canViewMalfunzionamenti() ? route('prodotti.completo.show', $prodotto) : null),
    publicCatalog: @json(route('prodotti.pubblico.index')),
    categoryFilter: @json(route('prodotti.pubblico.index') . '?categoria=' . urlencode($prodotto->categoria)),
    contacts: @json(route('contatti')),
    centers: @json(route('centri.index')),
    login: @json(route('login'))
};

/* === FUNZIONI JAVASCRIPT COLLEGATE ===
   
   Le seguenti funzioni sono implementate nel file JavaScript principale:
   
   1. openImageModal(src, title) - Modal zoom immagine
      LINGUAGGIO: JavaScript DOM manipulation + Bootstrap Modal API
      SCOPO: Visualizza immagine ingrandita in overlay
   
   2. initPublicProductView() - Inizializzazione vista pubblica
      LINGUAGGIO: JavaScript ES6+ con event listeners
      SCOPO: Gestisce interazioni specifiche vista pubblica
   
   3. handleUpgradePrompts() - Gestione promozioni upgrade
      LINGUAGGIO: JavaScript conditional logic + DOM manipulation
      SCOPO: Mostra messaggi upgrade a vista tecnica quando appropriato
   
   4. trackPublicProductView() - Analytics vista prodotto
      LINGUAGGIO: JavaScript con Google Analytics integration
      SCOPO: Traccia visualizzazioni prodotto per ottimizzazioni
   
   5. initImageGallery() - Galleria immagini
      LINGUAGGIO: JavaScript event delegation + CSS transforms
      SCOPO: Gestisce zoom, download e navigazione immagini
   
   INTEGRAZIONE SICUREZZA:
   - Tutti i dati sono sanitizzati lato server prima injection
   - XSS protection tramite Laravel e() helper
   - CSRF token incluso per eventuali chiamate AJAX
   - Rate limiting per azioni pubbliche
   
   UX DIFFERENZIATA:
   - Vista pubblica: focus su informazioni e conversione
   - Call-to-action per upgrade a vista tecnica
   - Percorsi semplificati per contatti e assistenza
   - Nessun dato sensibile o malfunzionamenti
*/

/* === AUTO-INIZIALIZZAZIONE ===
   LINGUAGGIO: JavaScript DOM events + feature detection
   SCOPO: Avvia funzionalità quando DOM ready */

document.addEventListener('DOMContentLoaded', function() {
    console.log('📦 Vista prodotto pubblico inizializzata:', window.PageData.prodotto?.nome);
    
    // Inizializza vista prodotto pubblico se funzione disponibile
    if (typeof initPublicProductView === 'function') {
        initPublicProductView();
    }
    
    // Inizializza modal immagine se funzione disponibile
    if (typeof initImageModal === 'function') {
        initImageModal();
    }
    
    // Gestisce promozioni upgrade se utente autorizzato
    if (window.PageData.viewConfig.enableTechnicalUpgrade) {
        if (typeof handleUpgradePrompts === 'function') {
            handleUpgradePrompts();
        }
    }
    
    // Track vista prodotto per analytics
    if (typeof trackPublicProductView === 'function') {
        trackPublicProductView({
            productId: window.PageData.prodotto?.id,
            productName: window.PageData.prodotto?.nome,
            category: window.PageData.prodotto?.categoria,
            hasPrice: !!window.PageData.prodotto?.prezzo,
            userLevel: window.PageData.user_level
        });
    }
});

/* === GESTIONE ERRORI VISTA PUBBLICA ===
   LINGUAGGIO: JavaScript error handling + logging
   SCOPO: Cattura errori specifici vista pubblica per debugging */

window.addEventListener('error', function(e) {
    // Log errori solo se in development mode
    if (window.PageData.app && window.PageData.app.debug) {
        console.error('❌ Errore vista prodotto pubblico:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            product: window.PageData.prodotto?.nome,
            timestamp: new Date().toISOString()
        });
    }
});

// Dati aggiuntivi per estensioni future...
window.PageData.pageType = 'public_product_detail';
window.PageData.loadTimestamp = Date.now();
</script>
@endpush