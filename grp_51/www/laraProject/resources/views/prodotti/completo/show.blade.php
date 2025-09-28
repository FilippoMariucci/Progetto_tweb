{{--
    VISTA COMPLETA PRODOTTO CON MALFUNZIONAMENTI - LIVELLO TECNICO
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Vista dettagliata singolo prodotto con layout orizzontale e malfunzionamenti
    LINGUAGGIO: Blade template (PHP con sintassi Laravel Blade)
    LIVELLO ACCESSO: 2+ (Tecnici e superiori con accesso ai malfunzionamenti)
    PATH: resources/views/prodotti/completo/show.blade.php
--}}

{{-- 
    EXTENDS: Eredita il layout base dell'applicazione
    LINGUAGGIO: Blade directive - specifica il template padre
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo dinamico basato sul nome prodotto
    LINGUAGGIO: Blade directive + PHP string concatenation
--}}
@section('title', $prodotto->nome . ' - Dettagli Completi')

{{-- 
    SECTION CONTENT: Contenuto principale della pagina
    LINGUAGGIO: Blade directive - tutto il corpo della vista
--}}
@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- === HEADER COMPATTO ===
         DESCRIZIONE: Intestazione con titolo prodotto e pulsanti azione
         LINGUAGGIO: HTML Bootstrap + Blade conditionals
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO DINAMICO: Nome prodotto dal database
                LINGUAGGIO: Blade output {{ }} con oggetto Eloquent
            --}}
            <h2 class="mb-1">{{ $prodotto->nome }}</h2>
            <p class="text-muted small mb-0">Dettagli tecnici completi</p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                LINK BACK CATALOG: Torna al catalogo completo
                LINGUAGGIO: HTML link + Laravel route() helper
            --}}
            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Catalogo
            </a>
            {{-- 
                PULSANTE MODIFICA CONDIZIONALE: Solo per admin
                LINGUAGGIO: Blade @if + Laravel Auth facade + Model methods
            --}}
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Modifica
                </a>
            @endif
        </div>
    </div>

    {{-- 
        BREADCRUMB NAVIGATION: Navigazione gerarchica contestuale
        DESCRIZIONE: Mostra percorso utente in base al ruolo
        LINGUAGGIO: HTML nav + Blade conditionals + Laravel helpers
    --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            {{-- 
                BREADCRUMB CONDIZIONALE: Dashboard diversa per ruolo utente
                LINGUAGGIO: Blade @if/@elseif + Laravel Auth + Model methods
            --}}
            @if(auth()->user()->isStaff())
                <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
            @elseif(auth()->user()->isTecnico())
                <li class="breadcrumb-item"><a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('prodotti.completo.index') }}">Catalogo Completo</a></li>
            {{-- 
                TITOLO TRONCATO: Limita lunghezza breadcrumb
                LINGUAGGIO: Laravel Str::limit() helper
            --}}
            <li class="breadcrumb-item active">{{ Str::limit($prodotto->nome, 30) }}</li>
        </ol>
    </nav>

    {{-- === ALERT PROBLEMI CRITICI ===
         DESCRIZIONE: Avviso prominente per prodotti con problemi urgenti
         LINGUAGGIO: Blade conditionals + PHP isset() + HTML Bootstrap Alert
    --}}
    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>ATTENZIONE: Problemi Critici</strong> - 
                    Questo prodotto ha <span class="badge bg-white text-danger">{{ $statistiche['malfunzionamenti_critici'] }}</span> 
                    problema/i critico/i che richiedono intervento immediato.
                </div>
                {{-- 
                    LINK ANCHOR: Collegamento interno alla sezione malfunzionamenti
                    LINGUAGGIO: HTML anchor link con ID fragment
                --}}
                <a href="#malfunzionamenti-section" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-down me-1"></i>Vai ai Problemi
                </a>
            </div>
        </div>
    @endif

    {{-- === LAYOUT ORIZZONTALE PRINCIPALE ===
         DESCRIZIONE: Layout responsive a due colonne per desktop
         LINGUAGGIO: Bootstrap Grid System
    --}}
    <div class="row g-4">
        
        {{-- === COLONNA IMMAGINE CORRETTA ===
             DESCRIZIONE: Colonna sinistra con immagine e informazioni tecniche
             LINGUAGGIO: Bootstrap responsive columns
        --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="position-relative">
                    {{-- 
                        GESTIONE IMMAGINE CONDIZIONALE: Con fallback placeholder
                        LINGUAGGIO: Blade @if + Laravel asset() + Storage path
                    --}}
                    @if($prodotto->foto)
                        {{-- 
                            IMMAGINE CORRETTA: Object-fit contain per proporzioni
                            SCOPO: Evita deformazione immagini, mantiene aspect ratio
                            LINGUAGGIO: HTML img + CSS inline + JavaScript onclick
                        --}}
                        <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 280px; object-fit: contain; background-color: #f8f9fa; cursor: pointer; padding: 1rem;"
                             onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                    @else
                        {{-- Placeholder quando immagine non disponibile --}}
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 280px;">
                            <div class="text-center">
                                <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 small">Immagine non disponibile</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- 
                        BADGE CATEGORIA OVERLAY: Etichetta categoria sovrapposta
                        LINGUAGGIO: CSS position absolute + PHP string functions
                    --}}
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                    </div>
                    
                    {{-- 
                        BADGE PREZZO CONDIZIONALE: Mostra prezzo se disponibile
                        LINGUAGGIO: Blade @if + PHP number_format()
                    --}}
                    @if($prodotto->prezzo)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    {{-- 
                        INDICATORE URGENZA: Banner per problemi critici
                        LINGUAGGIO: Blade conditional + CSS positioning
                    --}}
                    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
                        <div class="position-absolute bottom-0 start-0 end-0 bg-danger text-white text-center py-1">
                            <small><i class="bi bi-exclamation-triangle me-1"></i><strong>PRIORITÀ ALTA</strong></small>
                        </div>
                    @endif
                </div>
                
                {{-- 
                    AZIONI IMMAGINE: Controlli per zoom e download
                    LINGUAGGIO: Blade @if + JavaScript functions + HTML download attribute
                --}}
                @if($prodotto->foto)
                    <div class="card-body py-2">
                        <div class="d-flex gap-1">
                            {{-- Pulsante zoom modal --}}
                            <button type="button" class="btn btn-primary btn-sm flex-fill" 
                                    onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                                <i class="bi bi-zoom-in me-1"></i>Ingrandisci
                            </button>
                            {{-- 
                                LINK DOWNLOAD: Download diretto immagine
                                LINGUAGGIO: HTML download attribute + Laravel Str::slug()
                            --}}
                            <a href="{{ asset('storage/' . $prodotto->foto) }}" 
                               download="{{ Str::slug($prodotto->nome) }}.jpg" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- === INFO TECNICHE COMPATTE ===
                 DESCRIZIONE: Card con informazioni tecniche strutturate
                 LINGUAGGIO: Bootstrap card + Blade conditionals
            --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-tools me-1"></i>Info Tecniche
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 text-center">
                        {{-- 
                            DATA CREAZIONE CONDIZIONALE: Mostra quando catalogato
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
                        {{-- 
                            MODELLO CONDIZIONALE: Mostra solo se presente
                            LINGUAGGIO: Blade @if + HTML code tag per formattazione
                        --}}
                        @if($prodotto->modello)
                            <div class="col-12">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Modello</small>
                                    <code class="small">{{ $prodotto->modello }}</code>
                                </div>
                            </div>
                        @endif
                        
                        {{-- 
                            STAFF ASSEGNATO: Mostra chi gestisce il prodotto
                            LINGUAGGIO: Blade @if/@elseif + Laravel relationships + Auth
                        --}}
                        @if($prodotto->staffAssegnato)
                            <div class="col-12">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Staff Assegnato</small>
                                    <span class="badge bg-info small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $prodotto->staffAssegnato->nome_completo }}
                                    </span>
                                </div>
                            </div>
                        @elseif(auth()->user()->isAdmin())
                            {{-- Avviso per admin se nessuno staff assegnato --}}
                            <div class="col-12">
                                <div class="p-2 bg-warning bg-opacity-10 rounded">
                                    <small class="text-warning">
                                        <i class="bi bi-person-x me-1"></i>
                                        Nessun staff assegnato
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- === STATISTICHE PROBLEMI COMPATTE ===
                 DESCRIZIONE: Metriche sui malfunzionamenti se disponibili
                 LINGUAGGIO: Blade nested conditionals + PHP isset()
            --}}
            @if(isset($statistiche) && ($showMalfunzionamenti ?? false))
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-1"></i>Statistiche Problemi
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2">
                            {{-- Grid 2x2 per statistiche --}}
                            <div class="col-6">
                                <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                    <div class="fw-bold text-primary">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</div>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                    <div class="fw-bold text-danger">{{ $statistiche['malfunzionamenti_critici'] ?? 0 }}</div>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                    <div class="fw-bold text-warning">{{ $statistiche['malfunzionamenti_alti'] ?? 0 }}</div>
                                    <small class="text-muted">Alta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                    <div class="fw-bold text-info">{{ $statistiche['totale_segnalazioni'] ?? 0 }}</div>
                                    <small class="text-muted">Segnalazioni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- === COLONNA INFORMAZIONI PRINCIPALE ===
             DESCRIZIONE: Colonna destra con dettagli prodotto e malfunzionamenti
             LINGUAGGIO: Bootstrap responsive column
        --}}
        <div class="col-lg-8 col-md-7">
            
            {{-- 
                HEADER PRODOTTO: Titolo e badge informativi
                LINGUAGGIO: HTML flexbox + Blade conditionals
            --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-2">{{ $prodotto->nome }}</h1>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        {{-- Badge modello se presente --}}
                        @if($prodotto->modello)
                            <span class="badge bg-secondary small">{{ $prodotto->modello }}</span>
                        @endif
                        
                        {{-- Badge staff assegnato troncato --}}
                        @if($prodotto->staffAssegnato)
                            <span class="badge bg-info small">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ Str::limit($prodotto->staffAssegnato->nome_completo, 20) }}
                            </span>
                        @endif
                        
                        {{-- 
                            BADGE STATO DINAMICO: Colore basato su gravità problemi
                            LINGUAGGIO: Blade nested conditionals + CSS Bootstrap classes
                        --}}
                        @if(isset($statistiche))
                            @if($statistiche['malfunzionamenti_critici'] > 0)
                                <span class="badge bg-danger small">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ $statistiche['malfunzionamenti_critici'] }} Critici
                                </span>
                            @elseif($statistiche['totale_malfunzionamenti'] > 0)
                                <span class="badge bg-warning small">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $statistiche['totale_malfunzionamenti'] }} Problemi
                                </span>
                            @else
                                <span class="badge bg-success small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Nessun Problema
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                {{-- Prezzo allineato a destra se presente --}}
                @if($prodotto->prezzo)
                    <div class="text-end">
                        <h4 class="text-success mb-0">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</h4>
                    </div>
                @endif
            </div>
            
            {{-- 
                DESCRIZIONE PRODOTTO: Testo descrittivo se presente
                LINGUAGGIO: Blade @if + HTML paragraph
            --}}
            @if($prodotto->descrizione)
                <div class="mb-3">
                    <p class="text-muted">{{ $prodotto->descrizione }}</p>
                </div>
            @endif
            
            {{-- === SCHEDA TECNICA COMPATTA ===
                 DESCRIZIONE: Informazioni tecniche strutturate in layout responsive
                 LINGUAGGIO: Bootstrap card + responsive grid
            --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i>Scheda Tecnica Completa
                    </h6>
                </div>
                <div class="card-body py-3">
                    
                    {{-- Layout compatto per scheda tecnica --}}
                    <div class="row g-3">
                        
                        {{-- 
                            NOTE TECNICHE: Specifiche tecniche del prodotto
                            LINGUAGGIO: Blade @if + PHP nl2br() + e() helper for XSS protection
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
                            MODALITÀ INSTALLAZIONE: Istruzioni passo-passo
                            LINGUAGGIO: Blade @if + PHP functions per formattazione sicura
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
                            MODALITÀ D'USO: Istruzioni utilizzo prodotto
                            LINGUAGGIO: Blade @if + sicurezza XSS con e() helper
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
                            STATO VUOTO: Messaggio quando scheda tecnica incompleta
                            LINGUAGGIO: Blade complex conditional + PHP negation operators
                        --}}
                        @if(!$prodotto->note_tecniche && !$prodotto->modalita_installazione && !$prodotto->modalita_uso)
                            <div class="col-12 text-center py-3">
                                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">
                                    Scheda tecnica in aggiornamento.
                                    {{-- Pulsante azione solo per admin --}}
                                    @if(auth()->user()->isAdmin())
                                        <br><a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="bi bi-pencil me-1"></i>Completa le informazioni
                                        </a>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === SEZIONE MALFUNZIONAMENTI COMPATTA ===
                 DESCRIZIONE: Elenco problemi con filtri e azioni per tecnici
                 LINGUAGGIO: Blade complex conditionals + JavaScript integration
            --}}
            @if(($showMalfunzionamenti ?? false))
                <div class="card border-0 shadow-sm" id="malfunzionamenti-section">
                    <div class="card-header bg-warning text-dark py-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Malfunzionamenti e Soluzioni Tecniche
                                {{-- Badge conteggio --}}
                                @if(isset($statistiche))
                                    <span class="badge bg-dark ms-1">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</span>
                                @endif
                            </h6>
                            
                            {{-- 
                                AZIONI STAFF: Controlli per gestione malfunzionamenti
                                LINGUAGGIO: Blade conditionals + Laravel relationships + Auth
                            --}}
                            <div class="d-flex gap-1 mt-2 mt-md-0">
                                {{-- Pulsante aggiungi - solo per staff assegnato --}}
                                @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                       class="btn btn-dark btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi
                                    </a>
                                @endif
                                
                                {{-- Link vista completa se ci sono malfunzionamenti --}}
                                @if(($prodotto->malfunzionamenti ?? collect())->count() > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                       class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-list me-1"></i>Vista Completa
                                    </a>
                                @endif
                                
                                {{-- 
                                    FILTRI RAPIDI JAVASCRIPT: Pulsanti per filtrare malfunzionamenti
                                    LINGUAGGIO: HTML button group + JavaScript data attributes
                                --}}
                                <div class="btn-group btn-group-sm" id="malfunzionamentoFilter">
                                    <button type="button" class="btn btn-outline-dark active" data-filter="all">Tutti</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="critica">Critici</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="recent">Recenti</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        
                        {{-- 
                            GRIGLIA MALFUNZIONAMENTI: Layout responsive per problems
                            LINGUAGGIO: Bootstrap grid + Blade @forelse + JavaScript data attributes
                        --}}
                        <div class="row g-3" id="malfunzionamentiList">
                            @forelse($prodotto->malfunzionamenti ?? [] as $malfunzionamento)
                                <div class="col-lg-6 malfunzionamento-item" 
                                     data-gravita="{{ $malfunzionamento->gravita }}" 
                                     data-created="{{ $malfunzionamento->created_at->format('Y-m-d') }}">
                                    
                                    {{-- 
                                        PHP LOGIC BLOCK: Calcolo colori dinamici in base alla gravità
                                        LINGUAGGIO: PHP match expression (PHP 8+) + associative arrays
                                    --}}
                                    @php
                                        // Match expression per determinare colori bordi
                                        $borderColor = match($malfunzionamento->gravita) {
                                            'critica' => 'danger',
                                            'alta' => 'warning', 
                                            'media' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $badgeColor = $borderColor;
                                        
                                        // Array associativo per colori difficoltà
                                        $diffColors = [
                                            'facile' => 'success',
                                            'media' => 'info',
                                            'difficile' => 'warning',
                                            'esperto' => 'danger'
                                        ];
                                    @endphp
                                    
                                    {{-- 
                                        CARD MALFUNZIONAMENTO: Card con bordo colorato dinamico
                                        LINGUAGGIO: HTML Bootstrap + PHP variable interpolation
                                    --}}
                                    <div class="card border-start border-{{ $borderColor }} border-3 h-100">
                                        <div class="card-body py-3">
                                            
                                            {{-- Header malfunzionamento --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold small">{{ $malfunzionamento->titolo }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $badgeColor }} small">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            {{-- 
                                                DESCRIZIONE TRONCATA: Limita lunghezza testo
                                                LINGUAGGIO: Laravel Str::limit() helper
                                            --}}
                                            <p class="text-muted small mb-2">
                                                {{ Str::limit($malfunzionamento->descrizione, 80) }}
                                            </p>
                                            
                                            {{-- 
                                                BADGE INFORMATIVI: Badge per difficoltà e metriche
                                                LINGUAGGIO: Blade conditionals + PHP array access with null coalescing
                                            --}}
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                {{-- Badge difficoltà con colore dinamico --}}
                                                <span class="badge bg-{{ $diffColors[$malfunzionamento->difficolta] ?? 'secondary' }} small">
                                                    {{ ucfirst($malfunzionamento->difficolta) }}
                                                </span>
                                                
                                                {{-- 
                                                    BADGE SEGNALAZIONI: Conteggio segnalazioni se presenti
                                                    LINGUAGGIO: Blade @if + JavaScript ID per aggiornamenti dinamici
                                                --}}
                                                @if($malfunzionamento->numero_segnalazioni)
                                                    <span class="badge bg-primary small" id="badge-{{ $malfunzionamento->id }}">
                                                        <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }}
                                                    </span>
                                                @endif
                                                
                                                {{-- 
                                                    BADGE TEMPO STIMATO: Tempo riparazione se disponibile
                                                    LINGUAGGIO: Blade @if + Bootstrap Icons + concatenazione stringa
                                                --}}
                                                @if($malfunzionamento->tempo_stimato)
                                                    <span class="badge bg-info small">
                                                        <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }}min
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            {{-- 
                                                AZIONI MALFUNZIONAMENTO: Pulsanti per azioni utente
                                                LINGUAGGIO: Bootstrap d-grid + Laravel route() helpers
                                            --}}
                                            <div class="d-grid gap-1">
                                                {{-- 
                                                    LINK SOLUZIONE: Visualizza dettagli completi
                                                    LINGUAGGIO: Laravel route with multiple parameters + dynamic CSS classes
                                                --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-{{ $borderColor }} btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                                </a>
                                                
                                                {{-- Azioni secondarie in layout orizzontale --}}
                                                <div class="d-flex gap-1">
                                                    {{-- 
                                                        PULSANTE SEGNALA: JavaScript function per segnalazione AJAX
                                                        LINGUAGGIO: HTML button + JavaScript onclick + PHP echo per ID
                                                    --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm segnala-btn flex-fill"
                                                            onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                            title="Segnala problema">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Segnala
                                                    </button>
                                                    
                                                    {{-- 
                                                        PULSANTE MODIFICA: Solo per staff con permessi
                                                        LINGUAGGIO: Blade @if + Laravel Auth + Model method + route()
                                                    --}}
                                                    @if(auth()->user()->canManageMalfunzionamenti())
                                                        <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                                           class="btn btn-outline-secondary btn-sm"
                                                           title="Modifica">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- 
                                                INFO CREATORE: Mostra chi ha creato il malfunzionamento (solo staff)
                                                LINGUAGGIO: Blade nested conditionals + Laravel relationships
                                            --}}
                                            @if($malfunzionamento->creatoBy && auth()->user()->isStaff())
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        {{ $malfunzionamento->creatoBy->nome_completo ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                            @empty
                                {{-- 
                                    STATO VUOTO MALFUNZIONAMENTI: Messaggio quando nessun problema
                                    LINGUAGGIO: Blade @empty + HTML centered layout + conditionals
                                --}}
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                                        <h5 class="text-success mt-2">Ottima notizia!</h5>
                                        <p class="text-muted">
                                            Non ci sono malfunzionamenti noti per questo prodotto.
                                        </p>
                                        
                                        {{-- 
                                            PULSANTE PRIMO MALFUNZIONAMENTO: Solo per staff assegnato
                                            LINGUAGGIO: Blade nested conditionals + Laravel relationships + Auth
                                        --}}
                                        @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                            <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                               class="btn btn-outline-warning btn-sm mt-2">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Aggiungi Primo Malfunzionamento
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- 
                            LINK VISUALIZZA TUTTI: Se ci sono molti malfunzionamenti
                            LINGUAGGIO: Blade @if + Laravel Collection count() + route()
                        --}}
                        @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 4)
                            <div class="text-center mt-3">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-list me-1"></i>
                                    Visualizza Tutti ({{ $prodotto->malfunzionamenti->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- === PRODOTTI CORRELATI COMPATTI ===
         DESCRIZIONE: Sezione suggerimenti prodotti nella stessa categoria
         LINGUAGGIO: Blade conditionals + Laravel Collection methods + loops
    --}}
    @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-collection text-info me-1"></i>
                            Prodotti Correlati nella Stessa Categoria
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            {{-- 
                                LOOP PRODOTTI CORRELATI: Limita a 4 elementi
                                LINGUAGGIO: Blade @foreach + Laravel Collection take() method
                            --}}
                            @foreach($prodottiCorrelati->take(4) as $correlato)
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                {{-- 
                                                    IMMAGINE CORRELATA CORRETTA: Thumbnail con object-fit
                                                    LINGUAGGIO: Blade @if/@else + CSS inline + Laravel asset()
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
                                                        LINK PRODOTTO CORRELATO: Link troncato al prodotto
                                                        LINGUAGGIO: HTML link + Laravel route() + Str::limit()
                                                    --}}
                                                    <h6 class="mb-1 small">
                                                        <a href="{{ route('prodotti.completo.show', $correlato) }}" 
                                                           class="text-decoration-none">
                                                            {{ Str::limit($correlato->nome, 25) }}
                                                        </a>
                                                    </h6>
                                                    {{-- Modello o N/A se non presente --}}
                                                    <small class="text-muted d-block mb-1">
                                                        {{ $correlato->modello ?? 'N/A' }}
                                                    </small>
                                                    {{-- 
                                                        BADGE STATO PROBLEMI: Indicatore visivo stato
                                                        LINGUAGGIO: Blade @if/@else + CSS Bootstrap badges
                                                    --}}
                                                    <div class="d-flex gap-1">
                                                        @if($correlato->malfunzionamenti_count > 0)
                                                            <span class="badge bg-warning text-dark small">
                                                                {{ $correlato->malfunzionamenti_count }} problemi
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success small">OK</span>
                                                        @endif
                                                    </div>
                                                </div>
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
</div>

{{-- === MODAL IMMAGINE ===
     DESCRIZIONE: Modal Bootstrap per visualizzazione immagine ingrandita
     LINGUAGGIO: HTML Bootstrap Modal + JavaScript integration
--}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                {{-- 
                    TITOLO MODAL DINAMICO: Viene popolato via JavaScript
                    LINGUAGGIO: HTML h5 + JavaScript DOM manipulation target
                --}}
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                {{-- 
                    IMMAGINE MODAL: Target per JavaScript, src popolato dinamicamente
                    LINGUAGGIO: HTML img + CSS object-fit + JavaScript target
                --}}
                <img id="imageModalImg" src="" alt="" class="img-fluid w-100" style="object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === PUSH STYLES ===
     DESCRIZIONE: CSS personalizzato specifico per questa vista
     LINGUAGGIO: Blade @push directive + CSS3 + Media queries
--}}
@push('styles')
<style>
/* === STILI COMPATTI PER PRODOTTO COMPLETO === 
   LINGUAGGIO: CSS3 con commenti descrittivi
   SCOPO: Personalizzazione visuale per la vista dettaglio prodotto */

/* Card base con transizioni
   SCOPO: Stile uniforme per tutte le card con animazioni fluide */
.card {
    border-radius: 0.5rem; /* Angoli arrotondati */
    transition: all 0.2s ease; /* Transizione smooth per hover effects */
}

/* Effetto hover card
   SCOPO: Feedback visivo quando utente interagisce con le card */
.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important; /* Ombra più pronunciata */
}

/* === IMMAGINE PRODOTTO CORRETTA ===
   LINGUAGGIO: CSS3 selectors + properties
   SCOPO: Gestione responsive e animazioni per immagini prodotto */
.product-image {
    transition: transform 0.3s ease; /* Transizione per zoom effect */
    border-radius: 0.375rem; /* Angoli arrotondati per immagine */
}

/* Zoom hover su immagine prodotto
   SCOPO: Leggero ingrandimento al passaggio mouse per feedback */
.product-image:hover {
    transform: scale(1.02); /* Ingrandimento del 2% */
}

/* Badge compatti
   SCOPO: Dimensione ridotta per interfaccia compatta */
.badge.small {
    font-size: 0.7rem; /* Font ridotto */
    padding: 0.25rem 0.5rem; /* Padding compatto */
}

/* === PADDING COMPATTI ===
   LINGUAGGIO: CSS utility classes override
   SCOPO: Controllo preciso degli spazi per layout compatto */

/* Card body padding ridotto - variante py-2 */
.card-body.py-2 {
    padding-top: 0.5rem !important; /* 8px di padding verticale */
    padding-bottom: 0.5rem !important;
}

/* Card body padding medio - variante py-3 */
.card-body.py-3 {
    padding-top: 0.75rem !important; /* 12px di padding verticale */
    padding-bottom: 0.75rem !important;
}

/* Card header compatto */
.card-header.py-2 {
    padding-top: 0.5rem !important; /* Padding ridotto per header */
    padding-bottom: 0.5rem !important;
}

/* === STILI MALFUNZIONAMENTI ===
   LINGUAGGIO: CSS classes specifiche
   SCOPO: Animazioni e effetti per sezione malfunzionamenti */

/* Item malfunzionamento con hover effect */
.malfunzionamento-item {
    transition: all 0.3s ease; /* Transizione per interazioni */
}

/* Solleva item al hover */
.malfunzionamento-item:hover {
    transform: translateY(-2px); /* Solleva di 2px */
}

/* Bordo spesso per gravità */
.border-3 {
    border-width: 3px !important; /* Override Bootstrap per bordi più visibili */
}

/* === OMBREGGIATURE COLORATE PER GRAVITÀ ===
   LINGUAGGIO: CSS box-shadow con rgba colors
   SCOPO: Feedback visivo immediato per livello di gravità problemi */

/* Ombra rossa per problemi critici */
.card.border-start.border-danger {
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.15); /* Ombra rossa soft */
}

/* Ombra gialla per problemi di attenzione */
.card.border-start.border-warning {
    box-shadow: 0 0.125rem 0.25rem rgba(255, 193, 7, 0.15); /* Ombra gialla soft */
}

/* Ombra blu per problemi informativi */
.card.border-start.border-info {
    box-shadow: 0 0.125rem 0.25rem rgba(13, 202, 240, 0.15); /* Ombra blu soft */
}

/* === BACKGROUND OPACITY PERSONALIZZATI ===
   LINGUAGGIO: CSS rgba colors + !important override
   SCOPO: Colori di sfondo semi-trasparenti per statistiche */

.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important; /* Blu 10% opacità */
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important; /* Giallo 10% opacità */
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important; /* Rosso 10% opacità */
}

.bg-info.bg-opacity-10 {
    background-color: rgba(13, 202, 240, 0.1) !important; /* Ciano 10% opacità */
}

.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important; /* Verde 10% opacità */
}

/* === ALERT PERSONALIZZATI ===
   LINGUAGGIO: CSS border-radius + box-shadow
   SCOPO: Migliora aspetto degli alert Bootstrap */
.alert {
    border-radius: 0.5rem; /* Angoli arrotondati */
}

/* Alert con ombra personalizzata */
.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra profonda */
}

/* === RESPONSIVE DESIGN ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Adattamento layout per diversi dispositivi */

/* === TABLET E SCHERMI MEDI (768px e sotto) === */
@media (max-width: 768px) {
    /* Riduce padding container su schermi medi */
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Immagine più piccola su tablet */
    .product-image {
        height: 200px !important;
    }
    
    /* Titoli più piccoli */
    .h3 {
        font-size: 1.3rem;
    }
    
    /* Margine aggiuntivo tra colonne */
    .col-lg-4.col-md-5 {
        margin-bottom: 1rem;
    }
    
    /* Layout verticale per gap-1 su mobile */
    .d-flex.gap-1 {
        flex-direction: column; /* Stack verticale */
        gap: 0.25rem !important; /* Gap ridotto */
    }
    
    /* Pulsanti full width su mobile */
    .d-flex.gap-1 .btn {
        width: 100%;
    }
    
    /* Button group più compatti */
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

/* === SMARTPHONE (576px e sotto) === */
@media (max-width: 576px) {
    /* Padding ancora più ridotto per card */
    .card-body {
        padding: 0.75rem !important;
    }
    
    /* Immagini più piccole su smartphone */
    .product-image {
        height: 180px !important;
        padding: 0.5rem;
    }
    
    /* Riduce gap negative margins per gutter sistema */
    .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    /* Padding ridotto per colonne */
    .row.g-3 > * {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Titoli ancora più piccoli */
    .h2 {
        font-size: 1.2rem;
    }
    
    /* Gap badge ridotto */
    .d-flex.flex-wrap.gap-1 {
        gap: 0.25rem !important;
    }
}

/* === STATI INTERATTIVI ===
   LINGUAGGIO: CSS pseudo-classes
   SCOPO: Feedback visivo per interazioni utente */

/* Stato disabilitato pulsanti */
.btn:disabled {
    opacity: 0.6; /* Riduce opacità */
    cursor: not-allowed; /* Cursore "non permesso" */
}

/* Spinner loading piccolo */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === ANIMAZIONI HOVER ===
   LINGUAGGIO: CSS :hover pseudo-class + transform
   SCOPO: Micro-interazioni per migliorare UX */

/* Solleva pulsanti al hover */
.btn:hover {
    transform: translateY(-1px); /* Solleva di 1px */
}

/* Ingrandisce badge al hover */
.badge:hover {
    transform: scale(1.05); /* Ingrandimento del 5% */
}

/* === FOCUS ACCESSIBILITY ===
   LINGUAGGIO: CSS :focus pseudo-class + box-shadow
   SCOPO: Migliora accessibilità per navigazione keyboard */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); /* Outline focus visibile */
}

/* === BREADCRUMB PERSONALIZZATO ===
   LINGUAGGIO: CSS pseudo-element ::before
   SCOPO: Personalizza separatori breadcrumb */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›"; /* Carattere separatore personalizzato */
    color: #6c757d; /* Colore grigio Bootstrap */
}

/* === MODAL IMMAGINE ===
   LINGUAGGIO: CSS per componente modal Bootstrap
   SCOPO: Ottimizza visualizzazione immagine nel modal */
#imageModal .modal-body img {
    max-height: 80vh; /* Limita altezza al 80% viewport */
}

/* === SCROLLBAR PERSONALIZZATA ===
   LINGUAGGIO: CSS webkit pseudo-elements
   SCOPO: Migliora estetica scrollbar su browser Webkit */

/* Dimensioni scrollbar */
.overflow-auto::-webkit-scrollbar {
    width: 6px; /* Larghezza scrollbar verticale */
    height: 6px; /* Altezza scrollbar orizzontale */
}

/* Traccia scrollbar */
.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1; /* Colore background traccia */
    border-radius: 6px; /* Angoli arrotondati */
}

/* Maniglia scrollbar */
.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1; /* Colore maniglia */
    border-radius: 6px; /* Angoli arrotondati */
}

/* Hover maniglia scrollbar */
.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8; /* Colore più scuro al hover */
}
</style>
@endpush

{{-- === PUSH SCRIPTS ===
     DESCRIZIONE: JavaScript specifico per funzionalità dinamiche vista
     LINGUAGGIO: Blade @push + JavaScript ES6+ + Laravel data injection
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE VARIABILI GLOBALI ===
   LINGUAGGIO: JavaScript ES6 const/let
   SCOPO: Definisce endpoint API e oggetti dati per uso JavaScript */

// URL API per chiamate AJAX ai malfunzionamenti
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";

// Inizializza oggetto dati pagina se non esiste
window.PageData = window.PageData || {};

/* === INIEZIONE DATI PHP IN JAVASCRIPT ===
   LINGUAGGIO: Blade @json directive + JavaScript object assignment
   SCOPO: Rende disponibili dati server-side per logica client-side */

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

/* === NOTA TECNICA SULLE FUNZIONI JAVASCRIPT ===
   
   Le seguenti funzioni sono richiamate dal template ma implementate
   nel file JavaScript principale dell'applicazione:
   
   1. openImageModal(src, title) - Apre modal per zoom immagine
   2. segnalaMalfunzionamento(id) - Segnala problema via AJAX  
   3. Filtri malfunzionamenti - Event listeners per bottoni filtro
   
   LINGUAGGIO: JavaScript ES6+ con:
   - Fetch API per chiamate HTTP asincrone
   - DOM manipulation per aggiornamenti dinamici
   - Event delegation per performance
   - Promise/async-await per gestione asincrona
   
   INTEGRAZIONE: I dati in window.PageData vengono utilizzati da:
   - Validazioni client-side
   - Chiamate AJAX con CSRF token
   - Aggiornamenti UI dinamici
   - Analytics e tracking eventi
   - Gestione stati interfaccia
*/

// Aggiungi altri dati che potrebbero servire per estensioni future...
</script>
@endpush