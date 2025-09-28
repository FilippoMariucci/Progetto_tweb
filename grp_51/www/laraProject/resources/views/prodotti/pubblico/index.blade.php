{{-- 
    VISTA CATALOGO PRODOTTI PUBBLICO - LIVELLO 1 ACCESSO
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Vista per utenti non autenticati con filtri categoria corretti
    LINGUAGGIO: Blade template (PHP con sintassi Laravel Blade)
    LIVELLO ACCESSO: 1 (Pubblico - senza malfunzionamenti)
    PATH: resources/views/prodotti/pubblico/index.blade.php
--}}

{{-- 
    EXTENDS: Eredita il layout base dell'applicazione
    LINGUAGGIO: Blade directive - specifica il template padre
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo dinamico con configurazione app
    LINGUAGGIO: Blade directive + Laravel config() helper + string concatenation
--}}
@section('title', 'Catalogo Prodotti - ' . config('app.name'))

{{-- 
    SECTION CONTENT: Contenuto principale della pagina pubblica
    LINGUAGGIO: Blade directive - corpo completo della vista
--}}
@section('content')
<div class="container mt-4">
    
    {{-- === HEADER COMPATTO ===
         DESCRIZIONE: Intestazione con informazioni di accesso pubblico
         LINGUAGGIO: HTML Bootstrap + Blade conditionals per utenti autenticati
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO PRINCIPALE: Con icona specifica per catalogo pubblico
                LINGUAGGIO: HTML h2 + Bootstrap Icons + Bootstrap spacing classes
            --}}
            <h2 class="mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Catalogo Prodotti
            </h2>
            <p class="text-muted small mb-0">
                Esplora la nostra gamma completa di prodotti per l'assistenza tecnica
            </p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                LINK VISTA TECNICA CONDIZIONALE: Solo per utenti autenticati con permessi
                LINGUAGGIO: Blade @auth + Laravel Auth facade + Model methods
            --}}
            @auth
                @if(Auth::user()->canViewMalfunzionamenti())
                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-tools"></i> Vista Tecnica
                    </a>
                @endif
            @endauth
            {{-- 
                LINK CENTRI ASSISTENZA: Sempre disponibile per il pubblico
                LINGUAGGIO: HTML link + Laravel route() helper
            --}}
            <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-geo-alt"></i> Centri Assistenza
            </a>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE ===
         DESCRIZIONE: Cards con metriche pubbliche (no dati sensibili)
         LINGUAGGIO: Bootstrap grid + Blade output + PHP null coalescing
    --}}
    <div class="row g-2 mb-3">
        {{-- Card prodotti totali --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-box text-primary fs-5"></i>
                    {{-- 
                        CONTEGGIO SICURO: Usa null coalescing per evitare errori
                        LINGUAGGIO: Blade output + PHP null coalescing operator (??)
                    --}}
                    <h6 class="fw-bold mb-0 mt-1">{{ $stats['total_prodotti'] ?? 0 }}</h6>
                    <small class="text-muted">Prodotti Totali</small>
                </div>
            </div>
        </div>
        {{-- Card categorie disponibili --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-tags text-success fs-5"></i>
                    <h6 class="fw-bold mb-0 mt-1">{{ $stats['categorie_count'] ?? 0 }}</h6>
                    <small class="text-muted">Categorie</small>
                </div>
            </div>
        </div>
        {{-- Card anno catalogo --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-calendar text-info fs-5"></i>
                    {{-- 
                        DATA DINAMICA: Anno corrente per mostrare aggiornamento
                        LINGUAGGIO: PHP date() function
                    --}}
                    <h6 class="fw-bold mb-0 mt-1">{{ date('Y') }}</h6>
                    <small class="text-muted">Catalogo Aggiornato</small>
                </div>
            </div>
        </div>
        {{-- Card assistenza --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-shield-check text-warning fs-5"></i>
                    <h6 class="fw-bold mb-0 mt-1">24/7</h6>
                    <small class="text-muted">Assistenza</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === FORM RICERCA COMPATTO ===
         DESCRIZIONE: Form per ricerca prodotti con supporto wildcard
         LINGUAGGIO: HTML form + Laravel helpers + Bootstrap styling
    --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    {{-- 
                        FORM RICERCA: Metodo GET per SEO e bookmarking
                        LINGUAGGIO: HTML form + Laravel route() helper + JavaScript ID
                    --}}
                    <form method="GET" action="{{ route('prodotti.pubblico.index') }}" class="row g-3" id="search-form">
                        {{-- Campo ricerca con suggerimenti --}}
                        <div class="col-lg-5 col-md-7">
                            <label for="search" class="form-label small fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group input-group-sm">
                                {{-- 
                                    INPUT RICERCA: Con persistenza valore e placeholder esplicativo
                                    LINGUAGGIO: HTML input + Laravel request() helper + autocomplete off
                                --}}
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cerca prodotto (es: lavatrice o lav*)"
                                       autocomplete="off">
                                {{-- Pulsante clear ricerca --}}
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            {{-- 
                                SUGGERIMENTO WILDCARD: Help text per utenti
                                LINGUAGGIO: Bootstrap form-text + HTML code tag
                            --}}
                            <div class="form-text small">
                                <strong>Suggerimento:</strong> Usa <code>*</code> per ricerche parziali
                            </div>
                        </div>

                        {{-- === CORREZIONE: Categoria Select ===
                             DESCRIZIONE: Select corretto per array semplice di categorie
                             LINGUAGGIO: HTML select + Blade conditionals + PHP array functions
                        --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="categoria" class="form-label small fw-semibold">
                                <i class="bi bi-tags me-1"></i>Categoria
                            </label>
                            <select class="form-select form-select-sm" id="categoria" name="categoria">
                                <option value="">Tutte</option>
                                {{-- 
                                    LOOP CATEGORIE CORRETTO: Verifica tipo e contenuto array
                                    LINGUAGGIO: Blade @if + PHP isset() + is_array() + count()
                                --}}
                                @if(isset($categorie) && is_array($categorie) && count($categorie) > 0)
                                    {{-- 
                                        FOREACH SEMPLICE: Itera array di stringhe categorie
                                        LINGUAGGIO: Blade @foreach + HTML option + PHP string functions
                                    --}}
                                    @foreach($categorie as $categoria)
                                        <option value="{{ $categoria }}" 
                                                {{ request('categoria') == $categoria ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $categoria)) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Pulsanti azione form --}}
                        <div class="col-lg-4 col-md-2">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                {{-- Pulsante submit --}}
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- Link reset --}}
                                <a href="{{ route('prodotti.pubblico.index') }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === CORREZIONE: FILTRI CATEGORIE BADGE ===
         DESCRIZIONE: Badge interattivi per filtro rapido categorie
         LINGUAGGIO: Blade conditionals + Bootstrap badges + PHP array iteration
    --}}
    @if(isset($categorie) && is_array($categorie) && count($categorie) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">Categorie:</small>
                    
                    {{-- 
                        BADGE "TUTTE": Badge per rimuovere filtro categoria
                        LINGUAGGIO: HTML link + CSS classes condizionali + PHP negation
                    --}}
                    <a href="{{ route('prodotti.pubblico.index') }}" 
                       class="badge category-badge {{ !request('categoria') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none"
                       data-categoria="">
                        Tutte ({{ $stats['total_prodotti'] ?? 0 }})
                    </a>
                    
                    {{-- 
                        LOOP BADGE CATEGORIE: Badge per ogni categoria con conteggio
                        LINGUAGGIO: Blade @foreach + PHP block + array access
                    --}}
                    @foreach($categorie as $cat)
                        {{-- 
                            PHP BLOCK: Calcolo conteggio prodotti per categoria
                            LINGUAGGIO: PHP @php block + array access + ternary operator
                        --}}
                        @php
                            // Ottieni il conteggio dalla stats per questa categoria
                            $count = isset($stats['per_categoria'][$cat]) ? $stats['per_categoria'][$cat] : 0;
                        @endphp
                        {{-- 
                            BADGE CATEGORIA: Link filtro con stato attivo/inattivo
                            LINGUAGGIO: HTML link + PHP urlencode() + CSS condizionali
                        --}}
                        <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($cat) }}" 
                           class="badge category-badge {{ request('categoria') == $cat ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none"
                           data-categoria="{{ $cat }}">
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                            <span class="ms-1">({{ $count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI RICERCA ===
         DESCRIZIONE: Alert informativo sui risultati trovati
         LINGUAGGIO: Blade conditionals + Laravel request() + Bootstrap alert
    --}}
    @if(request('search') || request('categoria'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                            <i class="bi bi-info-circle me-1"></i>
                            {{-- 
                                CONTEGGIO RISULTATI: Mostra numero prodotti trovati
                                LINGUAGGIO: Blade output + Laravel pagination total()
                            --}}
                            <strong>Risultati:</strong> {{ $prodotti->total() }} prodotti trovati
                            {{-- Mostra termine ricerca se presente --}}
                            @if(request('search'))
                                per "<em>{{ request('search') }}</em>"
                            @endif
                            {{-- Mostra categoria se filtrata --}}
                            @if(request('categoria'))
                                nella categoria "<em>{{ ucfirst(str_replace('_', ' ', request('categoria'))) }}</em>"
                            @endif
                        </div>
                        {{-- Pulsante reset ricerca --}}
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI ===
         DESCRIZIONE: Layout responsive per visualizzazione prodotti
         LINGUAGGIO: Bootstrap grid + Blade @forelse + Laravel pagination
    --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        {{-- 
            FORELSE LOOP: Itera prodotti con gestione lista vuota
            LINGUAGGIO: Blade @forelse directive + Collection iteration
        --}}
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6">
                {{-- 
                    CARD PRODOTTO: Card standard senza indicatori tecnici
                    LINGUAGGIO: Bootstrap card + CSS classes custom
                --}}
                <div class="card h-100 shadow-sm border-0 product-card">
                    
                    {{-- 
                        SEZIONE IMMAGINE: Gestione immagine con fallback
                        LINGUAGGIO: CSS positioning + Blade conditionals
                    --}}
                    <div class="position-relative">
                        {{-- 
                            IMMAGINE CONDIZIONALE: Con object-fit per proporzioni corrette
                            LINGUAGGIO: Blade @if + Laravel asset() + CSS inline
                        --}}
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 140px; object-fit: contain; background-color: #f8f9fa; padding: 0.5rem;">
                        @else
                            {{-- Placeholder per prodotto senza immagine --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 140px;">
                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        
                        {{-- 
                            BADGE CATEGORIA OVERLAY: Etichetta categoria sovrapposta
                            LINGUAGGIO: CSS absolute positioning + PHP string functions
                        --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-primary small">
                                {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                            </span>
                        </div>

                        {{-- 
                            BADGE PREZZO: Mostra prezzo se disponibile per il pubblico
                            LINGUAGGIO: Blade @if + PHP number_format() per localizzazione
                        --}}
                        @if($prodotto->prezzo)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success small">
                                    €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- 
                        CONTENUTO CARD: Informazioni prodotto per il pubblico
                        LINGUAGGIO: Bootstrap card body + Blade output
                    --}}
                    <div class="card-body p-3">
                        {{-- 
                            TITOLO PRODOTTO: Nome del prodotto linkabile
                            LINGUAGGIO: HTML h6 + Bootstrap text utilities
                        --}}
                        <h6 class="card-title text-primary mb-2 fw-bold">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- 
                            MODELLO CONDIZIONALE: Mostra modello se presente
                            LINGUAGGIO: Blade @if + Bootstrap text classes
                        --}}
                        @if($prodotto->modello)
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                            </p>
                        @endif

                        {{-- 
                            DESCRIZIONE TRONCATA: Descrizione limitata per layout
                            LINGUAGGIO: Laravel Str::limit() helper + Blade output
                        --}}
                        <p class="card-text text-muted small mb-3">
                            {{ Str::limit($prodotto->descrizione, 80, '...') }}
                        </p>

                        {{-- 
                            PULSANTE AZIONE: Link alla scheda tecnica pubblica
                            LINGUAGGIO: Bootstrap d-grid + Laravel route() con parametro
                        --}}
                        <div class="d-grid">
                            <a href="{{ route('prodotti.pubblico.show', $prodotto) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Scheda Tecnica
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- 
                STATO VUOTO: Gestione quando nessun prodotto trovato
                LINGUAGGIO: Blade @empty + Bootstrap utilities + conditionals
            --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        {{-- 
                            MESSAGGIO VUOTO CONDIZIONALE: Diverso per ricerca vs catalogo vuoto
                            LINGUAGGIO: Blade nested conditionals + Laravel request()
                        --}}
                        @if(request('search') || request('categoria'))
                            {{-- Stato: ricerca senza risultati --}}
                            <i class="bi bi-search display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">
                                Non abbiamo trovato prodotti che corrispondono ai tuoi criteri di ricerca.
                            </p>
                            {{-- 
                                AZIONI SUGGERITE: Pulsanti per proseguire navigazione
                                LINGUAGGIO: Bootstrap button group + JavaScript onclick
                            --}}
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-1"></i>Vedi tutti i prodotti
                                </a>
                                {{-- 
                                    PULSANTE FOCUS: JavaScript per focus su campo ricerca
                                    LINGUAGGIO: HTML button + JavaScript onclick + jQuery
                                --}}
                                <button type="button" class="btn btn-outline-secondary" onclick="$('#search').focus()">
                                    <i class="bi bi-search me-1"></i>Nuova ricerca
                                </button>
                            </div>
                        @else
                            {{-- Stato: catalogo completamente vuoto --}}
                            <i class="bi bi-box display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Catalogo in aggiornamento</h5>
                            <p class="text-muted">
                                Il catalogo è momentaneamente vuoto. Torna presto per vedere i nostri prodotti!
                            </p>
                            {{-- Link contatti per informazioni --}}
                            <a href="{{ route('contatti') }}" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i>Contattaci per informazioni
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 
        PAGINAZIONE: Controlli navigazione pagine se necessari
        LINGUAGGIO: Blade @if + Laravel pagination + Bootstrap styling
    --}}
    @if($prodotti->hasPages())
        <div class="row">
            <div class="col-12">
                {{-- 
                    INFO PAGINAZIONE: Range risultati correnti
                    LINGUAGGIO: Bootstrap text + Laravel pagination methods
                --}}
                <div class="text-center mb-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
                
                {{-- 
                    LINKS PAGINAZIONE: Con mantenimento parametri query
                    LINGUAGGIO: Laravel pagination + Bootstrap template + query preservation
                --}}
                <div class="d-flex justify-content-center">
                    {{ $prodotti->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif

    {{-- === INFO ASSISTENZA ===
         DESCRIZIONE: Sezione informativa per indirizzare utenti verso accesso tecnico
         LINGUAGGIO: Bootstrap card + Blade guest/auth conditionals
    --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h5 class="text-primary mb-2">
                        <i class="bi bi-info-circle me-2"></i>
                        Hai bisogno di assistenza tecnica?
                    </h5>
                    <p class="mb-3 text-muted">
                        Sei un tecnico autorizzato? Accedi per visualizzare informazioni complete 
                        sui malfunzionamenti e le relative soluzioni tecniche.
                    </p>
                    
                    {{-- 
                        PULSANTI CALL-TO-ACTION: Diversi per guest vs utenti autenticati
                        LINGUAGGIO: Bootstrap button group + Blade @guest/@else
                    --}}
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        {{-- 
                            GUEST vs AUTHENTICATED: Pulsanti diversi per stato login
                            LINGUAGGIO: Blade @guest/@else directives
                        --}}
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i>Accesso Tecnici
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        @endguest
                        
                        {{-- Link sempre disponibili --}}
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                        </a>
                        
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope me-1"></i>Contattaci
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === PUSH STYLES ===
     DESCRIZIONE: CSS personalizzato per catalogo pubblico
     LINGUAGGIO: Blade @push + CSS3 + Media queries
--}}
@push('styles')
<style>
/* === STILI PER FILTRI CATEGORIA ===
   LINGUAGGIO: CSS3 selectors e properties
   SCOPO: Styling per badge filtri categoria interattivi */

/* Badge categoria base con transizioni */
.category-badge {
    transition: all 0.2s ease; /* Transizione smooth per hover */
    cursor: pointer; /* Indica interattività */
    border-radius: 0.375rem; /* Angoli arrotondati */
    font-size: 0.75rem; /* Dimensione font compatta */
    padding: 0.375rem 0.75rem; /* Padding equilibrato */
}

/* Effetto hover su badge categoria */
.category-badge:hover {
    transform: translateY(-1px); /* Solleva leggermente */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Ombra soft */
}

/* === STILI CARD PRODOTTO ===
   LINGUAGGIO: CSS3 + Bootstrap overrides
   SCOPO: Styling specifico per card prodotto pubblico */

/* Card prodotto base */
.product-card {
    transition: all 0.2s ease; /* Transizione per animazioni */
    border-radius: 0.5rem; /* Angoli arrotondati */
    overflow: hidden; /* Nasconde overflow per effetti */
}

/* Effetto hover card prodotto */
.product-card:hover {
    transform: translateY(-2px); /* Solleva card */
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important; /* Ombra pronunciata */
}

/* === STILI IMMAGINE PRODOTTO ===
   LINGUAGGIO: CSS transitions + border
   SCOPO: Gestione responsive e interattiva delle immagini */

/* Immagine prodotto con bordo */
.product-image {
    transition: transform 0.3s ease; /* Transizione per zoom */
    border: 1px solid #e9ecef; /* Bordo sottile */
}

/* Hover su immagine prodotto */
.product-image:hover {
    transform: scale(1.02); /* Leggero zoom */
    border-color: #007bff; /* Cambia colore bordo */
}

/* === RESPONSIVE DESIGN ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Adattamento per dispositivi mobili */

/* === TABLET E MOBILE (768px e sotto) === */
@media (max-width: 768px) {
    /* Container con padding ridotto su mobile */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    /* Immagini più piccole su mobile */
    .product-image {
        height: 120px !important;
    }
    
    /* Button group verticale su mobile */
    .d-flex.gap-2 {
        flex-direction: column; /* Stack verticale */
        gap: 0.5rem !important; /* Gap ridotto */
    }
}
</style>
@endpush

{{-- === PUSH SCRIPTS ===
     DESCRIZIONE: JavaScript per interattività catalogo pubblico
     LINGUAGGIO: Blade @push + JavaScript ES6+ + data injection
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE DATI PAGINA ===
   LINGUAGGIO: JavaScript ES6 object initialization
   SCOPO: Prepara oggetto globale per funzioni JavaScript */

// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

/* === INIEZIONE DATI SERVER-SIDE ===
   LINGUAGGIO: Blade @json + JavaScript object assignment
   SCOPO: Rende disponibili dati PHP per uso JavaScript client-side */

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

/* === DATI RICERCA PUBBLICA ===
   LINGUAGGIO: JavaScript object properties + Blade @json
   SCOPO: Dati specifici per ricerca e filtri catalogo pubblico */

// Dati di ricerca pubblica per JavaScript (utilizzati nel file index.js)
window.PageData.searchTerm = @json(request('search'));
window.PageData.categoria = @json(request('categoria'));
window.PageData.risultati = @json(isset($prodotti) ? $prodotti->total() : 0);
window.PageData.catalogoPublico = true; // Flag per identificare vista pubblica
window.PageData.filtersActive = @json((request('search') || request('categoria')) ? true : false);

/* === CONFIGURAZIONE SEARCH ===
   LINGUAGGIO: JavaScript object configuration
   SCOPO: Parametri per funzionalità di ricerca JavaScript */

window.PageData.searchConfig = {
    wildcardSupport: true, // Supporto ricerca con *
    minSearchLength: 2,    // Lunghezza minima termine ricerca
    searchDelay: 300,      // Delay per ricerca automatica (ms)
    placeholder: "Cerca prodotto (es: lavatrice o lav*)",
    clearButtonSelector: "#clearSearch",
    formSelector: "#search-form"
};

/* === FUNZIONI JAVASCRIPT COLLEGATE ===
   
   Le seguenti funzioni sono implementate nel file JavaScript principale:
   
   1. initPublicSearch() - Inizializza ricerca pubblica
      LINGUAGGIO: JavaScript ES6+ con event listeners
      SCOPO: Gestisce ricerca real-time e validazione input
   
   2. handleCategoryFilter() - Gestione filtri categoria
      LINGUAGGIO: JavaScript DOM manipulation + History API
      SCOPO: Filtro categoria senza ricaricamento pagina
   
   3. clearSearchForm() - Reset form ricerca
      LINGUAGGIO: JavaScript form manipulation
      SCOPO: Cancella tutti i filtri e termini ricerca
   
   4. trackPublicSearch() - Analytics ricerca
      LINGUAGGIO: JavaScript con Google Analytics integration
      SCOPO: Traccia comportamento utenti per ottimizzazioni
   
   5. suggestSearch() - Suggerimenti ricerca
      LINGUAGGIO: JavaScript AJAX + debouncing
      SCOPO: Autocompletamento termini ricerca
   
   INTEGRAZIONE CON BACKEND:
   - Le ricerche utilizzano GET parameters per SEO
   - Supporto wildcard (*) gestito lato controller
   - Paginazione preserva filtri applicati
   - Cache risultati per performance
   
   ACCESSIBILITÀ:
   - Keyboard navigation per filtri
   - Screen reader support
   - Focus management
   - High contrast mode support
*/

/* === EVENTI PERSONALIZZATI ===
   LINGUAGGIO: JavaScript Custom Events
   SCOPO: Comunicazione tra componenti JavaScript */

// Evento quando ricerca viene eseguita
document.addEventListener('catalogSearchPerformed', function(e) {
    console.log('🔍 Ricerca eseguita:', e.detail);
    
    // Aggiorna URL senza ricaricamento (se supportato)
    if (window.history && window.history.pushState) {
        const url = new URL(window.location);
        if (e.detail.search) {
            url.searchParams.set('search', e.detail.search);
        } else {
            url.searchParams.delete('search');
        }
        if (e.detail.categoria) {
            url.searchParams.set('categoria', e.detail.categoria);
        } else {
            url.searchParams.delete('categoria');
        }
        window.history.pushState({}, '', url);
    }
});

// Evento quando filtro categoria viene applicato
document.addEventListener('categoryFilterApplied', function(e) {
    console.log('🏷️ Filtro categoria applicato:', e.detail.categoria);
    
    // Scroll to results
    const resultsElement = document.getElementById('prodotti-grid');
    if (resultsElement) {
        resultsElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

/* === DATI ANALYTICS ===
   LINGUAGGIO: JavaScript object per tracking
   SCOPO: Preparazione dati per Google Analytics o sistemi similari */

window.PageData.analytics = {
    pageType: 'catalog_public',
    totalProducts: @json(isset($prodotti) ? $prodotti->total() : 0),
    currentPage: @json(isset($prodotti) ? $prodotti->currentPage() : 1),
    perPage: @json(isset($prodotti) ? $prodotti->perPage() : 12),
    hasSearch: @json(request('search') ? true : false),
    hasFilter: @json(request('categoria') ? true : false),
    userAuthenticated: @json(auth()->check()),
    timestamp: new Date().toISOString()
};

/* === PERFORMANCE MONITORING ===
   LINGUAGGIO: JavaScript Performance API
   SCOPO: Monitoraggio tempi caricamento per ottimizzazioni */

// Marca il tempo di fine caricamento dati
if (window.performance && window.performance.mark) {
    window.performance.mark('catalog-data-loaded');
}

/* === COMPATIBILITÀ BROWSER ===
   LINGUAGGIO: JavaScript feature detection
   SCOPO: Fallback per browser più vecchi */

// Verifica supporto funzionalità moderne
window.PageData.browserSupport = {
    fetch: typeof fetch !== 'undefined',
    promise: typeof Promise !== 'undefined',
    arrow: (function() { try { eval('()=>{}'); return true; } catch(e) { return false; } })(),
    const: (function() { try { eval('const x=1;'); return true; } catch(e) { return false; } })(),
    historyAPI: !!(window.history && window.history.pushState),
    localStorage: (function() { try { localStorage.setItem('test','test'); localStorage.removeItem('test'); return true; } catch(e) { return false; } })()
};

/* === INIZIALIZZAZIONE AUTOMATICA ===
   LINGUAGGIO: JavaScript DOM ready events
   SCOPO: Avvia funzionalità quando DOM è pronto */

// Auto-inizializzazione quando DOM è pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('📦 Catalogo pubblico inizializzato');
    
    // Inizializza ricerca se funzione disponibile
    if (typeof initPublicSearch === 'function') {
        initPublicSearch();
    }
    
    // Inizializza filtri categoria se funzione disponibile
    if (typeof initCategoryFilters === 'function') {
        initCategoryFilters();
    }
    
    // Focus automatico su campo ricerca se richiesto da URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('focus_search')) {
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Evidenzia categoria attiva se selezionata
    const activeCategory = @json(request('categoria'));
    if (activeCategory) {
        const categoryBadges = document.querySelectorAll('[data-categoria="' + activeCategory + '"]');
        categoryBadges.forEach(badge => {
            badge.classList.add('active');
        });
    }
});

/* === GESTIONE ERRORI GLOBALE ===
   LINGUAGGIO: JavaScript error handling
   SCOPO: Cattura errori per debugging e user experience */

// Handler errori globale per questa pagina
window.addEventListener('error', function(e) {
    console.error('❌ Errore in catalogo pubblico:', e.error);
    
    // Log per debugging (solo in development)
    if (window.PageData.app && window.PageData.app.debug) {
        console.group('Dettagli errore catalogo:');
        console.log('File:', e.filename);
        console.log('Linea:', e.linea);
        console.log('Messaggio:', e.message);
        console.log('Stack:', e.error.stack);
        console.groupEnd();
    }
});

// Aggiungi altri dati che potrebbero servire per estensioni future...
window.PageData.version = '1.0.0'; // Versione per cache busting
window.PageData.loadTime = Date.now(); // Timestamp caricamento
</script>
@endpush