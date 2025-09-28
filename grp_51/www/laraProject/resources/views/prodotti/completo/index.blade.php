{{-- 
    VISTA CATALOGO PRODOTTI TECNICO COMPLETO - LAYOUT COMPATTO
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Vista ottimizzata con stile compatto, header leggibile e immagini corrette
    LINGUAGGIO: Blade template (PHP con sintassi Blade di Laravel)
    LIVELLO ACCESSO: 2+ (Tecnici e superiori)
    PATH: resources/views/prodotti/completo/index.blade.php
--}}

{{-- 
    EXTENDS: Estende il layout principale dell'applicazione 
    LINGUAGGIO: Blade directive - specifica quale layout utilizzare
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Definisce il titolo della pagina che apparirà nel tag <title>
    LINGUAGGIO: Blade directive - inserisce contenuto in una sezione specifica del layout
--}}
@section('title', 'Catalogo Completo - Tecnici')

{{-- 
    SECTION CONTENT: Sezione principale contenente tutto il corpo della pagina
    LINGUAGGIO: Blade directive - tutto il contenuto HTML/PHP/JS della vista
--}}
@section('content')
<div class="container mt-4">
    
    {{-- === HEADER COMPATTO E LEGGIBILE === 
         DESCRIZIONE: Intestazione della pagina con titolo, breadcrumb e pulsanti azione
         LINGUAGGIO: HTML con Bootstrap CSS classes
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO PRINCIPALE: Mostra nome sezione con icona Bootstrap
                LINGUAGGIO: HTML con classi Bootstrap Icons
            --}}
            <h2 class="mb-1">
                <i class="bi bi-tools text-warning me-2"></i>
                Catalogo Tecnico Completo
            </h2>
            {{-- 
                SOTTOTITOLO: Descrizione breve del livello di accesso
                LINGUAGGIO: HTML con Bootstrap classes per styling
            --}}
            <p class="text-muted small mb-0">
                <span class="badge bg-warning text-dark me-2">Con Malfunzionamenti</span>
                Accesso completo per tecnici e staff
            </p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                PULSANTI AZIONE CONDIZIONALI: Mostrati solo se utente ha permessi
                LINGUAGGIO: Blade conditionals (@if) + Laravel helper functions
            --}}
            {{-- Pulsante nuova soluzione - solo per staff --}}
            @if(auth()->user()->isStaff())
                <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuova Soluzione
                </a>
            @endif
            {{-- Pulsante nuovo prodotto - solo per admin --}}
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Nuovo Prodotto
                </a>
            @endif
            {{-- Pulsante ritorna dashboard - per tutti --}}
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>


    {{-- === STATISTICHE COMPATTE E LEGGIBILI ===
         DESCRIZIONE: Cards con statistiche aggregate sui prodotti
         LINGUAGGIO: Blade conditionals + HTML Bootstrap + PHP isset() function
    --}}
    @if(isset($stats))
        <div class="row g-2 mb-3">
            {{-- Card prodotti totali --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-box text-primary fs-5"></i>
                        {{-- 
                            VISUALIZZAZIONE STATISTICA: Usa null coalescing operator (??) di PHP
                            SCOPO: Evita errori se la chiave array non esiste, mostra 0 come default
                        --}}
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['total_prodotti'] ?? 0 }}</h6>
                        <small class="text-muted">Prodotti Totali</small>
                    </div>
                </div>
            </div>
            {{-- Card prodotti con malfunzionamenti --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['con_malfunzionamenti'] ?? 0 }}</h6>
                        <small class="text-muted">Con Problemi</small>
                    </div>
                </div>
            </div>
            {{-- Card malfunzionamenti critici --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-circle text-danger fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti_critici'] ?? 0 }}</h6>
                        <small class="text-muted">Critici</small>
                    </div>
                </div>
            </div>
            {{-- Card prodotti personali (solo staff) --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-person-check text-success fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">
                            {{-- 
                                LOGICA CONDIZIONALE COMPLESSA: Verifica ruolo utente E esistenza statistica
                                LINGUAGGIO: Blade conditionals annidati + Laravel Auth helpers
                            --}}
                            @if(auth()->user()->isStaff() && isset($stats['miei_prodotti']))
                                {{ $stats['miei_prodotti'] }}
                            @else
                                0
                            @endif
                        </h6>
                        <small class="text-muted">Miei Prodotti</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === FORM RICERCA COMPATTO ===
         DESCRIZIONE: Form per ricerca e filtri prodotti con persistenza parametri
         LINGUAGGIO: HTML form + Blade directives + Laravel helpers
    --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    {{-- 
                        FORM CON METODO GET: Invia parametri via URL query string
                        ACTION: Route Laravel specifica per catalogo completo
                        LINGUAGGIO: HTML form + Laravel route() helper
                    --}}
                    <form method="GET" action="{{ route('prodotti.completo.index') }}" class="row g-3">
                        {{-- Campo ricerca --}}
                        <div class="col-lg-4 col-md-6">
                            <label for="search" class="form-label small fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            <div class="input-group input-group-sm">
                                {{-- 
                                    INPUT RICERCA: Mantiene valore precedente usando request() helper
                                    SCOPO: Persistenza stato form dopo submit
                                    LINGUAGGIO: HTML input + Laravel request() helper
                                --}}
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Nome, modello, descrizione..."
                                       autocomplete="off">
                                {{-- Pulsante clear ricerca --}}
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Filtro categoria --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="categoria" class="form-label small fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Categoria
                            </label>
                            <select name="categoria" id="categoria" class="form-select form-select-sm">
                                <option value="">Tutte</option>
                                {{-- 
                                    LOOP CATEGORIE: Itera array categorie dal controller
                                    LINGUAGGIO: Blade @foreach + PHP array iteration
                                --}}
                                @foreach($categorie as $key => $label)
                                    {{-- 
                                        OPTION SELECTED: Mantiene selezione precedente
                                        LINGUAGGIO: HTML option + PHP ternary operator + Laravel request()
                                    --}}
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro stato prodotto --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="filter" class="form-label small fw-semibold">
                                <i class="bi bi-filter me-1"></i>Filtro
                            </label>
                            <select name="filter" id="filter" class="form-select form-select-sm">
                                <option value="">Tutti</option>
                                {{-- 
                                    OPTIONS PREDEFINITE: Filtri hardcoded per stati prodotto
                                    LINGUAGGIO: HTML options + PHP strict comparison (===)
                                --}}
                                <option value="critici" {{ request('filter') === 'critici' ? 'selected' : '' }}>
                                    Critici
                                </option>
                                <option value="problematici" {{ request('filter') === 'problematici' ? 'selected' : '' }}>
                                    Con Problemi
                                </option>
                                <option value="senza_problemi" {{ request('filter') === 'senza_problemi' ? 'selected' : '' }}>
                                    Senza Problemi
                                </option>
                            </select>
                        </div>

                        {{-- Pulsanti azione form --}}
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                {{-- Pulsante submit ricerca --}}
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- 
                                    LINK RESET: Ritorna alla pagina senza parametri query
                                    LINGUAGGIO: HTML link + Laravel route() helper
                                --}}
                                <a href="{{ route('prodotti.completo.index') }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                        
                        {{-- 
                            HIDDEN FIELDS: Mantiene filtri aggiuntivi durante ricerca
                            SCOPO: Preserva stato filtri staff quando si effettua ricerca
                            LINGUAGGIO: HTML hidden inputs + Blade conditionals
                        --}}
                        @if(request('staff_filter'))
                            <input type="hidden" name="staff_filter" value="{{ request('staff_filter') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === FILTRI STAFF COMPATTI ===
         DESCRIZIONE: Filtri rapidi specifici per utenti staff
         LINGUAGGIO: Blade conditionals + HTML Bootstrap badges
    --}}
    @if(auth()->user()->isStaff())
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">Filtri Staff:</small>
                    
                    {{-- 
                        BADGE FILTRI: Link che funzionano come filtri rapidi
                        SCOPO: Navigazione veloce tra diverse viste per staff
                        LINGUAGGIO: HTML links + PHP negation operator (!) + Laravel request()
                    --}}
                    <a href="{{ route('prodotti.completo.index') }}" 
                       class="badge {{ !request('staff_filter') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                        Tutti i Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                       class="badge {{ request('staff_filter') === 'my_products' ? 'bg-success' : 'bg-light text-dark' }} text-decoration-none">
                        I Miei Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?filter=critici" 
                       class="badge {{ request('filter') === 'critici' ? 'bg-danger' : 'bg-light text-dark' }} text-decoration-none">
                        Solo Critici
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI RICERCA ===
         DESCRIZIONE: Alert informativo sui risultati della ricerca
         LINGUAGGIO: Blade conditionals + HTML + Laravel request() helpers
    --}}
    @if(request('search') || request('categoria') || request('filter'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                            <i class="bi bi-info-circle me-1"></i>
                            {{-- 
                                RISULTATI RICERCA: Mostra numero totale risultati
                                LINGUAGGIO: Blade output + Laravel pagination total() method
                            --}}
                            <strong>Risultati:</strong> {{ $prodotti->total() }} prodotti trovati
                            {{-- 
                                TERMINE RICERCA: Mostra termine ricercato se presente
                                LINGUAGGIO: Blade conditional + HTML em tag
                            --}}
                            @if(request('search'))
                                per "<em>{{ request('search') }}</em>"
                            @endif
                        </div>
                        {{-- Pulsante reset ricerca --}}
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === GRIGLIA PRODOTTI COMPATTA ===
         DESCRIZIONE: Visualizzazione a griglia dei prodotti con card responsive
         LINGUAGGIO: Blade @forelse + HTML Bootstrap grid + PHP logica condizionale
    --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        {{-- 
            FORELSE LOOP: Itera prodotti o mostra messaggio se vuoto
            SCOPO: Gestisce caso array vuoto con @empty
            LINGUAGGIO: Blade @forelse directive (equivale a foreach + if empty)
        --}}
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6">
                {{-- 
                    CARD PRODOTTO CON CLASSI DINAMICHE: Bordi colorati in base allo stato
                    LINGUAGGIO: HTML classes + Blade conditionals + Laravel model methods
                --}}
                <div class="card h-100 shadow-sm border-0 product-card
                    {{-- Bordi colorati per stato --}}
                    @if($prodotto->hasMalfunzionamentiCritici())
                        border-start border-danger border-3
                    @elseif($prodotto->malfunzionamenti_count > 0)
                        border-start border-warning border-3
                    @elseif(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                        border-start border-info border-3
                    @else
                        border-start border-success border-2
                    @endif
                ">
                    
                    {{-- === IMMAGINE CORRETTA ===
                         DESCRIZIONE: Gestione immagine prodotto con fallback
                         LINGUAGGIO: Blade conditionals + Laravel asset() helper
                    --}}
                    <div class="position-relative">
                        {{-- 
                            CONDITIONAL IMAGE: Mostra immagine se esiste, altrimenti placeholder
                            LINGUAGGIO: Blade @if + Laravel asset() + Storage path
                        --}}
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 140px; object-fit: contain; background-color: #f8f9fa;">
                        @else
                            {{-- Placeholder quando immagine non disponibile --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 140px;">
                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        
                        {{-- 
                            BADGE CATEGORIA: Etichetta categoria in overlay
                            LINGUAGGIO: HTML positioning + PHP null coalescing + PHP ucfirst()
                        --}}
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-secondary small">
                                {{ $prodotto->categoria_label ?? ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- 
                            INDICATORI STATO: Badge informativi sullo stato prodotto
                            LINGUAGGIO: Blade conditionals + PHP isset() function
                        --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            {{-- Badge conteggio problemi --}}
                            @if($prodotto->malfunzionamenti_count > 0)
                                <span class="badge bg-warning small mb-1 d-block">
                                    {{ $prodotto->malfunzionamenti_count }} problemi
                                </span>
                            @endif
                            
                            {{-- Badge problemi critici --}}
                            @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                <span class="badge bg-danger small d-block">
                                    {{ $prodotto->critici_count }} critici
                                </span>
                            @endif
                            
                            {{-- Badge prodotto assegnato allo staff loggato --}}
                            @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                <span class="badge bg-success small d-block">
                                    <i class="bi bi-person-check"></i> Mio
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- === CONTENUTO CARD ===
                         DESCRIZIONE: Informazioni testuali del prodotto
                         LINGUAGGIO: HTML + Blade output + Laravel helpers
                    --}}
                    <div class="card-body p-3">
                        {{-- 
                            TITOLO CON COLORE DINAMICO: Cambia colore in base allo stato
                            LINGUAGGIO: HTML h6 + Blade conditionals per classi CSS
                        --}}
                        <h6 class="card-title mb-2 fw-bold
                            @if($prodotto->hasMalfunzionamentiCritici())
                                text-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                text-warning
                            @else
                                text-primary
                            @endif
                        ">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- 
                            MODELLO CONDIZIONALE: Mostra solo se presente
                            LINGUAGGIO: Blade @if + HTML paragraph
                        --}}
                        @if($prodotto->modello)
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                            </p>
                        @endif

                        {{-- 
                            DESCRIZIONE TRONCATA: Limita lunghezza descrizione
                            LINGUAGGIO: Laravel Str::limit() helper + Blade output
                        --}}
                        <p class="card-text small text-muted mb-3">
                            {{ Str::limit($prodotto->descrizione, 60, '...') }}
                        </p>

                        {{-- 
                            STATISTICHE COMPATTE: Layout a due colonne per metriche
                            LINGUAGGIO: Bootstrap grid + Blade conditionals + PHP null coalescing
                        --}}
                        <div class="row g-1 mb-3">
                            {{-- Colonna problemi totali --}}
                            <div class="col-6">
                                <div class="text-center p-2 
                                    @if($prodotto->malfunzionamenti_count > 0) 
                                        bg-warning bg-opacity-10 
                                    @else 
                                        bg-success bg-opacity-10 
                                    @endif 
                                    rounded">
                                    <div class="fw-bold small text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                        {{ $prodotto->malfunzionamenti_count ?? 0 }}
                                    </div>
                                    <small class="text-muted">Problemi</small>
                                </div>
                            </div>
                            {{-- Colonna problemi critici --}}
                            <div class="col-6">
                                <div class="text-center p-2 
                                    @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0) 
                                        bg-danger bg-opacity-10 
                                    @else 
                                        bg-success bg-opacity-10 
                                    @endif 
                                    rounded">
                                    <div class="fw-bold small text-{{ isset($prodotto->critici_count) && $prodotto->critici_count > 0 ? 'danger' : 'success' }}">
                                        {{ $prodotto->critici_count ?? 0 }}
                                    </div>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                        </div>

                        {{-- 
                            STAFF ASSEGNATO: Mostra chi gestisce il prodotto
                            LINGUAGGIO: Blade @if + Laravel relationship + accessor method
                        --}}
                        @if($prodotto->staffAssegnato)
                            <p class="text-muted small mb-3">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </p>
                        @endif

                        {{-- 
                            ALERT CRITICI: Avviso per problemi urgenti
                            LINGUAGGIO: Bootstrap alert + Blade conditional + Model method
                        --}}
                        @if($prodotto->hasMalfunzionamentiCritici())
                            <div class="alert alert-danger py-1 mb-3">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>ATTENZIONE:</strong> Problemi critici
                                </small>
                            </div>
                        @endif

                        {{-- 
                            PULSANTI AZIONE: Link per azioni su prodotto
                            LINGUAGGIO: HTML buttons + Laravel route() helpers + Blade conditionals
                        --}}
                        <div class="d-grid gap-1">
                            {{-- 
                                LINK DETTAGLI: Colore dinamico basato su stato prodotto
                                LINGUAGGIO: HTML link + Laravel route() + Blade conditionals per CSS classes
                            --}}
                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                               class="btn btn-sm
                                    @if($prodotto->hasMalfunzionamentiCritici())
                                        btn-outline-danger
                                    @elseif($prodotto->malfunzionamenti_count > 0)
                                        btn-outline-warning
                                    @else
                                        btn-outline-primary
                                    @endif
                               ">
                                <i class="bi bi-eye me-1"></i>Dettagli Completi
                            </a>
                            
                            {{-- 
                                LINK MALFUNZIONAMENTI: Condizionale - mostra solo se ci sono problemi
                                LINGUAGGIO: Blade @if/@else + Laravel route() + Model method
                            --}}
                            @if($prodotto->malfunzionamenti_count > 0)
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-{{ $prodotto->hasMalfunzionamentiCritici() ? 'danger' : 'warning' }} btn-sm">
                                    <i class="bi bi-tools me-1"></i>
                                    Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                                </a>
                            @else
                                {{-- Messaggio nessun problema --}}
                                <div class="text-center py-1">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Nessun problema segnalato
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- 
                STATO VUOTO: Visualizzazione quando nessun prodotto trovato
                SCOPO: UX - informa utente quando lista è vuota
                LINGUAGGIO: Blade @empty + HTML + Laravel helpers
            --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        {{-- 
                            MESSAGGIO VUOTO CONDIZIONALE: Diverso per ricerca vs catalogo vuoto
                            LINGUAGGIO: Blade @if + Laravel request() helper
                        --}}
                        @if(request('search') || request('categoria') || request('filter'))
                            {{-- Stato: ricerca senza risultati --}}
                            <i class="bi bi-search display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">
                                Prova a modificare i criteri di ricerca o 
                                <a href="{{ route('prodotti.completo.index') }}">visualizza tutti i prodotti</a>
                            </p>
                        @else
                            {{-- Stato: catalogo completamente vuoto --}}
                            <i class="bi bi-box display-1 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto disponibile</h5>
                            <p class="text-muted">Il catalogo è vuoto al momento</p>
                            {{-- Pulsante aggiungi - solo per admin --}}
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-1"></i>Aggiungi Primo Prodotto
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- === PAGINAZIONE COMPATTA ===
         DESCRIZIONE: Controlli navigazione pagine con info numeriche
         LINGUAGGIO: Blade conditionals + Laravel pagination + Bootstrap
    --}}
    @if($prodotti->hasPages())
        <div class="row">
            <div class="col-12">
                {{-- 
                    INFO PAGINAZIONE: Mostra range risultati correnti
                    LINGUAGGIO: HTML + Laravel pagination methods
                --}}
                <div class="text-center mb-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
                
                {{-- 
                    LINKS PAGINAZIONE: Include parametri query nella navigazione
                    SCOPO: Mantiene filtri durante navigazione pagine
                    LINGUAGGIO: Laravel pagination with appends() + Blade include
                --}}
                <div class="d-flex justify-content-center">
                    {{ $prodotti->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif

</div>
{{-- Fine container principale --}}
@endsection

{{-- === PUSH STYLES ===
     DESCRIZIONE: Aggiunge CSS personalizzato specifico per questa vista
     LINGUAGGIO: Blade @push directive + CSS3
     SCOPO: Stili che si applicano solo a questa pagina
--}}
@push('styles')
<style>
/* === STILI COMPATTI CATALOGO TECNICO === 
   LINGUAGGIO: CSS3 con commenti
   SCOPO: Personalizzazione visuale specifica per il catalogo tecnico */

/* Card prodotto base
   SCOPO: Stile base per ogni card prodotto con transizioni fluide */
.product-card {
    transition: all 0.2s ease; /* Transizione CSS per animazioni fluide */
    border-radius: 0.5rem; /* Angoli arrotondati */
    overflow: hidden; /* Nasconde contenuto che deborda */
}

/* Effetto hover su card prodotto
   SCOPO: Feedback visivo quando utente passa mouse sopra card */
.product-card:hover {
    transform: translateY(-2px); /* Solleva card di 2px */
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important; /* Ombra più pronunciata */
}

/* Immagini prodotto CORRETTE 
   SCOPO: Gestione responsive delle immagini prodotto */
.product-image {
    transition: transform 0.3s ease; /* Transizione per zoom */
    padding: 0.5rem; /* Padding per evitare zoom eccessivo */
}

/* Effetto zoom su immagine hover
   SCOPO: Ingrandimento leggero dell'immagine al passaggio mouse */
.product-image:hover {
    transform: scale(1.05); /* Ingrandimento del 5% */
}

/* Badge più compatti
   SCOPO: Riduce dimensione font dei badge per layout compatto */
.badge {
    font-size: 0.7rem; /* Font size ridotto per badge */
}

/* Form controls più piccoli
   SCOPO: Controlli form dimensionati per interfaccia compatta */
.form-select-sm,
.form-control-sm {
    font-size: 0.875rem; /* Font size ridotto per controlli */
}

/* Statistiche compatte
   SCOPO: Riduce padding verticale per cards statistiche */
.card-body.py-2 {
    padding-top: 0.5rem !important; /* Padding top ridotto */
    padding-bottom: 0.5rem !important; /* Padding bottom ridotto */
}

/* Bordi colorati per stato prodotto
   SCOPO: Indicatori visivi dello stato tramite bordi colorati */
.border-start.border-3 {
    border-left-width: 3px !important; /* Bordo sinistro spesso per stati critici */
}

.border-start.border-2 {
    border-left-width: 2px !important; /* Bordo sinistro medio per stati normali */
}

/* Alert compatti
   SCOPO: Riduce padding degli alert per interfaccia compatta */
.alert.py-1 {
    padding-top: 0.25rem !important; /* Padding top molto ridotto */
    padding-bottom: 0.25rem !important; /* Padding bottom molto ridotto */
}

/* === RESPONSIVE DESIGN ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Adattamento layout per dispositivi mobili */

/* Tablet e schermi medi */
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem; /* Padding ridotto per card su tablet */
    }
    
    .product-image {
        height: 120px !important; /* Altezza immagine ridotta per tablet */
    }
    
    .btn-group-sm .btn {
        font-size: 0.8rem; /* Font ridotto per pulsanti piccoli */
        padding: 0.25rem 0.5rem; /* Padding ridotto per pulsanti */
    }
}

/* Smartphone e schermi piccoli */
@media (max-width: 576px) {
    /* Layout header responsivo - cambia da orizzontale a verticale */
    .d-flex.justify-content-between {
        flex-direction: column; /* Stack verticale degli elementi */
        align-items: start !important; /* Allineamento a sinistra */
    }
    
    .btn-group {
        margin-top: 0.5rem; /* Margine superiore per separazione */
        width: 100%; /* Larghezza completa per pulsanti */
    }
    
    .h2 {
        font-size: 1.3rem !important; /* Titolo più piccolo su mobile */
    }
    
    .product-image {
        height: 100px !important; /* Immagini ancora più piccole su mobile */
    }
}

/* === STATI INTERATTIVI ===
   LINGUAGGIO: CSS Pseudo-classes
   SCOPO: Feedback visivo per interazioni utente */

/* Stato disabilitato pulsanti
   SCOPO: Indica visivamente quando pulsante non è cliccabile */
.btn:disabled {
    opacity: 0.6; /* Riduce opacità per indicare disabilitazione */
}

/* === SCROLLBAR PERSONALIZZATA ===
   LINGUAGGIO: CSS Webkit pseudo-elements
   SCOPO: Migliora estetica delle scrollbar su Webkit browsers */

/* Scrollbar principale */
.overflow-auto::-webkit-scrollbar {
    width: 6px; /* Larghezza scrollbar verticale */
    height: 6px; /* Altezza scrollbar orizzontale */
}

/* Track (sfondo) scrollbar */
.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1; /* Colore sfondo track */
    border-radius: 6px; /* Angoli arrotondati */
}

/* Thumb (maniglia) scrollbar */
.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1; /* Colore maniglia */
    border-radius: 6px; /* Angoli arrotondati */
}

/* Hover su thumb scrollbar */
.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8; /* Colore più scuro al hover */
}

/* === EVIDENZIAZIONE RICERCA ===
   LINGUAGGIO: CSS per elementi HTML mark
   SCOPO: Highlight dei termini di ricerca nei risultati */
mark {
    background-color: #fff3cd; /* Sfondo giallo per evidenziazione */
    padding: 0.125rem 0.25rem; /* Padding per leggibilità */
    border-radius: 0.25rem; /* Angoli leggermente arrotondati */
}

/* === ANIMAZIONI GENERALI ===
   LINGUAGGIO: CSS Transitions
   SCOPO: Transizioni fluide per migliorare UX */

/* Transizioni base per elementi interattivi */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out; /* Transizione universale smooth */
}

/* === STATI HOVER MIGLIORATI ===
   LINGUAGGIO: CSS Hover pseudo-class
   SCOPO: Feedback immediato per elementi interattivi */

/* Effetto hover pulsanti - solleva leggermente */
.btn:hover {
    transform: translateY(-1px); /* Solleva pulsante di 1px */
}

/* Effetto hover badge - ingrandisce leggermente */
.badge:hover {
    transform: scale(1.05); /* Ingrandimento del 5% */
}
</style>
@endpush

{{-- === PUSH SCRIPTS ===
     DESCRIZIONE: JavaScript specifico per questa vista
     LINGUAGGIO: Blade @push + JavaScript ES6+
     SCOPO: Interattività e logica client-side
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE DATI PAGINA ===
   LINGUAGGIO: JavaScript ES6
   SCOPO: Prepara oggetto globale con dati per uso JavaScript */

// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

/* === INIEZIONE DATI PHP IN JAVASCRIPT ===
   LINGUAGGIO: Blade @json directive + JavaScript
   SCOPO: Rende disponibili dati PHP lato client per AJAX/interazioni */

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

/* === DATI COMPUTATI ===
   LINGUAGGIO: JavaScript con Blade @json
   SCOPO: Dati derivati utilizzabili da funzioni JavaScript */

window.PageData.prodottiCount = @json($prodotti->count());
window.PageData.prodottiTotal = @json($prodotti->total());
window.PageData.searchTerm = @json(request('search'));
window.PageData.categoria = @json(request('categoria'));
window.PageData.filtro = @json(request('filter'));
window.PageData.staffFilter = @json(request('staff_filter'));
window.PageData.searchActive = @json(request('search') ? true : false);
window.PageData.filtersActive = @json((request('categoria') || request('filter')) ? true : false);

/* === MESSAGGI SESSION ===
   LINGUAGGIO: JavaScript con Laravel session helpers
   SCOPO: Rende disponibili messaggi flash per notifiche JavaScript */

window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionWarning = @json(session('warning'));

// Aggiungi altri dati che potrebbero servire per funzionalità future...

/* === NOTA TECNICA ===
   QUESTO SCRIPT VIENE ESEGUITO DOPO IL CARICAMENTO DELLA PAGINA
   I dati sono disponibili per:
   - Chiamate AJAX
   - Validazioni client-side  
   - Interazioni dinamiche
   - Analytics e tracking
   - Gestione stati interfaccia
*/
</script>
@endpush