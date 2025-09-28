{{-- 
    Vista per l'elenco dei malfunzionamenti di un prodotto
    LINGUAGGIO: Blade Template (Laravel) - sistema di templating per applicazioni web
    SCOPO: Mostra lista paginata e filtrata dei malfunzionamenti per un prodotto specifico
    ACCESSO: Solo tecnici (livello 2+) e staff (livello 3+) autenticati
    PERCORSO: resources/views/malfunzionamenti/index.blade.php
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- 
    Definisce il titolo statico della pagina
    Appare nel tag <title> del browser
--}}
@section('title', 'Malfunzionamenti - Dashboard')

{{-- Inizio sezione contenuto principale --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">
    
    {{-- 
        SEZIONE HEADER MALFUNZIONAMENTI
        Layout responsive con titolo e azioni principali
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                Header con layout flex per distribuire spazio
                Bootstrap: justify-content-between distribuisce elementi agli estremi
            --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="h2 mb-2">
                        {{-- Icona Bootstrap con colore warning (arancione) --}}
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Malfunzionamenti
                    </h1>
                    <p class="text-muted mb-0">
                        {{-- 
                            Blade: Accesso alle proprietà dell'oggetto $prodotto
                            Mostra nome prodotto e modello se disponibile
                        --}}
                        Problemi noti per: <strong>{{ $prodotto->nome }}</strong>
                        @if($prodotto->modello)
                            - {{ $prodotto->modello }}
                        @endif
                    </p>
                </div>

                {{-- 
                    Pulsante aggiungi nuovo malfunzionamento (solo per staff)
                    Laravel: Sistema di autorizzazione con gate e policy
                --}}
                @auth
                    {{-- 
                        Laravel: Controllo autorizzazione custom
                        auth()->user()->canManageMalfunzionamenti() è un metodo custom nel model User
                    --}}
                    @if(auth()->user()->canManageMalfunzionamenti())
                        <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Nuovo Malfunzionamento
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE STATISTICHE RAPIDE
        Mostra metriche aggregate solo se disponibili dal controller
    --}}
    @if(isset($stats))
    <div class="row mb-4">
        <div class="col-12">
            {{-- Card Bootstrap senza bordi per effetto moderno --}}
            <div class="card bg-light border-0">
                <div class="card-body py-3">
                    {{-- Layout griglia responsive con gap per spaziatura --}}
                    <div class="row g-3">
                        {{-- Statistica 1: Totale malfunzionamenti --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                {{-- Icona con sfondo colorato --}}
                                <div class="bg-primary rounded p-2 me-3">
                                    <i class="bi bi-list-ul text-white"></i>
                                </div>
                                <div>
                                    {{-- 
                                        PHP: ?? operatore null coalescing per valore di default
                                        Se $stats['totale'] è null, usa 0
                                    --}}
                                    <h4 class="mb-0 text-primary">{{ $stats['totale'] ?? 0 }}</h4>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Statistica 2: Malfunzionamenti critici --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded p-2 me-3">
                                    <i class="bi bi-exclamation-circle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-danger">{{ $stats['critici'] ?? 0 }}</h4>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Statistica 3: Alta gravità --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded p-2 me-3">
                                    <i class="bi bi-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-warning">{{ $stats['alta_gravita'] ?? 0 }}</h4>
                                    <small class="text-muted">Alta Gravità</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Statistica 4: Totale segnalazioni --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded p-2 me-3">
                                    <i class="bi bi-graph-up text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-info">{{ $stats['totale_segnalazioni'] ?? 0 }}</h4>
                                    <small class="text-muted">Segnalazioni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 
        SEZIONE FILTRI E RICERCA
        Form per filtrare e cercare tra i malfunzionamenti
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- 
                        Form HTML con metodo GET per mantenere filtri nell'URL
                        Bootstrap: row g-3 per layout griglia con gap
                    --}}
                    <form method="GET" class="row g-3" id="filter-form">
                        
                        {{-- Campo ricerca testuale --}}
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Malfunzionamento
                            </label>
                            {{-- 
                                Input text con attributi per disabilitare autocomplete
                                Laravel: request('search') recupera valore dal query string
                            --}}
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cerca nel titolo o descrizione..."
                                   {{-- Attributi HTML per disabilitare autocomplete browser --}}
                                   autocomplete="off"
                                   autocapitalize="off"
                                   autocorrect="off"
                                   spellcheck="false"
                                   data-form-type="other">
                        </div>
                        
                        {{-- Filtro per gravità --}}
                        <div class="col-md-3">
                            <label for="gravita" class="form-label fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Gravità
                            </label>
                            <select name="gravita" id="gravita" class="form-select">
                                <option value="">Tutte le gravità</option>
                                {{-- 
                                    Opzioni con controllo selected basato su query string
                                    PHP: Operatore ternario per aggiungere selected se corrispondente
                                --}}
                                <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>
                                    🔴 Critica
                                </option>
                                <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>
                                    🟡 Alta
                                </option>
                                <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>
                                    🟢 Media
                                </option>
                                <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>
                                    ⚪ Bassa
                                </option>
                            </select>
                        </div>
                        
                        {{-- Filtro per difficoltà riparazione --}}
                        <div class="col-md-3">
                            <label for="difficolta" class="form-label fw-semibold">
                                <i class="bi bi-tools me-1"></i>Difficoltà
                            </label>
                            <select name="difficolta" id="difficolta" class="form-select">
                                <option value="">Tutte le difficoltà</option>
                                <option value="facile" {{ request('difficolta') == 'facile' ? 'selected' : '' }}>
                                    Facile
                                </option>
                                <option value="media" {{ request('difficolta') == 'media' ? 'selected' : '' }}>
                                    Media
                                </option>
                                <option value="difficile" {{ request('difficolta') == 'difficile' ? 'selected' : '' }}>
                                    Difficile
                                </option>
                                <option value="esperto" {{ request('difficolta') == 'esperto' ? 'selected' : '' }}>
                                    Esperto
                                </option>
                            </select>
                        </div>
                        
                        {{-- Ordinamento risultati --}}
                        <div class="col-md-2">
                            <label for="order" class="form-label fw-semibold">
                                <i class="bi bi-sort-down me-1"></i>Ordina
                            </label>
                            <select name="order" id="order" class="form-select">
                                <option value="gravita" {{ request('order') == 'gravita' ? 'selected' : '' }}>
                                    Gravità
                                </option>
                                <option value="frequenza" {{ request('order') == 'frequenza' ? 'selected' : '' }}>
                                    Frequenza
                                </option>
                                <option value="recente" {{ request('order') == 'recente' ? 'selected' : '' }}>
                                    Più Recente
                                </option>
                                <option value="difficolta" {{ request('order') == 'difficolta' ? 'selected' : '' }}>
                                    Difficoltà
                                </option>
                            </select>
                        </div>
                        
                        {{-- Pulsanti azione form --}}
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                {{-- Pulsante submit per applicare filtri --}}
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i>Applica Filtri
                                </button>
                                {{-- 
                                    Pulsante reset (mostrato solo se ci sono filtri attivi)
                                    Laravel: request()->hasAny() controlla se esistono parametri specifici
                                --}}
                                @if(request()->hasAny(['search', 'gravita', 'difficolta', 'order']))
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE ELENCO MALFUNZIONAMENTI
        Lista principale con card responsive per ogni malfunzionamento
    --}}
    <div class="row">
        <div class="col-12">
            {{-- 
                Condizionale per mostrare lista o messaggio vuoto
                Laravel: count() metodo Collection per contare elementi
            --}}
            @if($malfunzionamenti->count() > 0)
                {{-- Lista malfunzionamenti con layout a griglia --}}
                <div class="row g-4">
                    {{-- 
                        Foreach Blade per iterare sui malfunzionamenti paginati
                        $malfunzionamenti è una LengthAwarePaginator Laravel
                    --}}
                    @foreach($malfunzionamenti as $malfunzionamento)
                        <div class="col-12">
                            {{-- 
                                Card Bootstrap con classi dinamiche per bordi colorati
                                Blade: @switch per logica condizionale complessa
                            --}}
                            <div class="card h-100 malfunzionamento-card 
                                @switch($malfunzionamento->gravita)
                                    @case('critica') border-danger @break
                                    @case('alta') border-warning @break 
                                    @case('media') border-info @break
                                    @default border-light
                                @endswitch
                            ">
                                <div class="card-body">
                                    {{-- Layout interno card con allineamento --}}
                                    <div class="row align-items-start">
                                        
                                        {{-- Badge gravità con colori dinamici --}}
                                        <div class="col-auto">
                                            <span class="badge 
                                                @switch($malfunzionamento->gravita)
                                                    @case('critica') bg-danger @break
                                                    @case('alta') bg-warning text-dark @break
                                                    @case('media') bg-info @break
                                                    @default bg-secondary
                                                @endswitch
                                                fs-6 px-3 py-2">
                                                {{-- Testo badge con emoji per identificazione rapida --}}
                                                @switch($malfunzionamento->gravita)
                                                    @case('critica') 🔴 CRITICA @break
                                                    @case('alta') 🟡 ALTA @break
                                                    @case('media') 🟢 MEDIA @break
                                                    @default ⚪ BASSA
                                                @endswitch
                                            </span>
                                        </div>
                                        
                                        {{-- Contenuto principale della card --}}
                                        <div class="col">
                                            {{-- Header con titolo e metadata --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0">
                                                    {{-- 
                                                        Link al dettaglio malfunzionamento
                                                        Laravel: route() con array di parametri
                                                    --}}
                                                    <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                       class="text-decoration-none">
                                                        {{ $malfunzionamento->titolo }}
                                                    </a>
                                                </h5>
                                                
                                                {{-- Metadata: numero segnalazioni --}}
                                                <div class="text-muted small">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    {{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                                                </div>
                                            </div>
                                            
                                            {{-- 
                                                Descrizione troncata
                                                Laravel: Str::limit() helper per limitare caratteri
                                            --}}
                                            <p class="card-text text-muted mb-3">
                                                {{ Str::limit($malfunzionamento->descrizione, 150) }}
                                            </p>
                                            
                                            {{-- Informazioni tecniche in layout responsive --}}
                                            <div class="row g-2 mb-3">
                                                {{-- Difficoltà riparazione --}}
                                                <div class="col-sm-4">
                                                    <small class="text-muted">
                                                        <i class="bi bi-tools me-1"></i>
                                                        {{-- PHP: ucfirst() capitalizza prima lettera --}}
                                                        Difficoltà: <strong>{{ ucfirst($malfunzionamento->difficolta) }}</strong>
                                                    </small>
                                                </div>
                                                
                                                {{-- Tempo stimato (se disponibile) --}}
                                                @if($malfunzionamento->tempo_stimato)
                                                    <div class="col-sm-4">
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Tempo: <strong>{{ $malfunzionamento->tempo_stimato }} min</strong>
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                {{-- Ultima segnalazione (se disponibile) --}}
                                                @if($malfunzionamento->ultima_segnalazione)
                                                    <div class="col-sm-4">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{-- 
                                                                PHP: Carbon::parse() per parsing date
                                                                format() per formato italiano dd/mm/yyyy
                                                            --}}
                                                            Ultima: {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- 
                                                Pulsanti azione con autorizzazioni differenziate
                                                Layout flex responsive per bottoni
                                            --}}
                                            <div class="d-flex gap-2 flex-wrap">
                                                {{-- Visualizza dettagli (disponibile a tutti) --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Visualizza Soluzione
                                                </a>
                                                
                                                {{-- 
                                                    Segnala problema (per TUTTI gli utenti autenticati di livello 2+)
                                                    JavaScript: onclick per gestire azione asincrona
                                                --}}
                                                @if(auth()->user()->canViewMalfunzionamenti())
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm segnala-btn"
                                                            onclick="segnalaMalfunzionamento('{{ $malfunzionamento->id }}')"
                                                            title="Segnala di aver riscontrato questo problema">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Ho Questo Problema
                                                    </button>
                                                @endif
                                                
                                                {{-- Gestione (solo per staff autorizzato) --}}
                                                @if(auth()->user()->canManageMalfunzionamenti())
                                                    {{-- Link modifica --}}
                                                    <a href="{{ route('staff.malfunzionamenti.edit', [$malfunzionamento]) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-pencil me-1"></i>Modifica
                                                    </a>
                                                    
                                                    {{-- 
                                                        Form eliminazione con conferma JavaScript
                                                        Laravel: @method('DELETE') per RESTful routing
                                                    --}}
                                                    <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Eliminare questo malfunzionamento?')">
                                                            <i class="bi bi-trash me-1"></i>Elimina
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 
                    SEZIONE PAGINAZIONE
                    Laravel: Paginazione automatica con conservazione query string
                --}}
                @if($malfunzionamenti->hasPages())
                    <div class="row mt-4">
                        <div class="col-12">
                            {{-- 
                                Navigazione paginazione semantica
                                Laravel: withQueryString() mantiene filtri nella paginazione
                            --}}
                            <nav aria-label="Paginazione malfunzionamenti">
                                {{ $malfunzionamenti->withQueryString()->links() }}
                            </nav>
                            
                            {{-- 
                                Informazioni paginazione per UX
                                Laravel: firstItem(), lastItem(), total() metodi Paginator
                            --}}
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Visualizzati {{ $malfunzionamenti->firstItem() }}-{{ $malfunzionamenti->lastItem() }} 
                                    di {{ $malfunzionamenti->total() }} malfunzionamenti
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                {{-- 
                    SEZIONE NESSUN RISULTATO
                    Messaggio personalizzato basato su presenza filtri
                --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        {{-- Condizionale per tipo di messaggio vuoto --}}
                        @if(request()->hasAny(['search', 'gravita', 'difficolta']))
                            {{-- Caso: nessun risultato per filtri --}}
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h3 class="text-muted mt-3">Nessun malfunzionamento trovato</h3>
                            <p class="text-muted">
                                Non sono stati trovati malfunzionamenti corrispondenti ai criteri di ricerca.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                                </a>
                            </div>
                        @else
                            {{-- Caso: nessun malfunzionamento per prodotto --}}
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h3 class="text-success mt-3">Ottima notizia!</h3>
                            <p class="text-muted">
                                Non ci sono malfunzionamenti noti per questo prodotto.
                            </p>
                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-1"></i>Torna al Prodotto
                            </a>
                        @endif
                        
                        {{-- Pulsante aggiungi per staff (sempre disponibile) --}}
                        @auth
                            @if(auth()->user()->canManageMalfunzionamenti())
                                <div class="mt-4">
                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Malfunzionamento
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade: @push('styles') aggiunge CSS al layout principale
--}}
@push('styles')
<style>
/* 
    CSS: Stili per le card malfunzionamenti
    Effetti hover per migliorare UX e feedback visivo
*/
.malfunzionamento-card {
    transition: all 0.2s ease-in-out;    /* Transizione smooth per animazioni */
}

.malfunzionamento-card:hover {
    transform: translateY(-2px);                      /* Solleva la card al hover */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);  /* Ombra più pronunciata */
}

/* 
    CSS: Disabilita autocomplete per il campo ricerca
    Commento: Non esiste proprietà CSS 'autocomplete', si gestisce via HTML attributes
*/
#search {
    /* Nessuna proprietà CSS 'autocomplete' - gestito via attributi HTML */
}

/* 
    CSS: Nasconde pulsanti autocomplete WebKit
    Browser specifici (Chrome, Safari) per disabilitare completamente autocomplete
*/
#search::-webkit-contacts-auto-fill-button,
#search::-webkit-credentials-auto-fill-button {
    visibility: hidden;           /* Nasconde i pulsanti */
    display: none !important;     /* Rimuove completamente dallo spazio */
    pointer-events: none;         /* Disabilita interazioni mouse */
}

/* 
    CSS RESPONSIVE: Badge responsive per dispositivi mobili
    Media query per adattare dimensioni su schermi piccoli
*/
@media (max-width: 768px) {
    .badge.fs-6 {
        font-size: 0.75rem !important;      /* Font più piccolo su mobile */
        padding: 0.25rem 0.5rem !important; /* Padding ridotto per spazio */
    }
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT
    Blade: @push('scripts') aggiunge script alla fine della pagina
--}}
@push('scripts')
<script>
/*
    JavaScript: Configurazione URL API per chiamate AJAX
    Utilizzato per funzionalità di segnalazione malfunzionamenti
*/
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";

/*
    JavaScript: Inizializzazione dati globali della pagina
    Pattern standard per condividere dati PHP con JavaScript
*/
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    @json() converte strutture PHP in JSON sicuro per JavaScript
    isset() verifica esistenza per evitare errori undefined
*/

// Dati del prodotto corrente per cui si visualizzano i malfunzionamenti
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Lista di tutti i prodotti (se disponibile nel controller)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Singolo malfunzionamento (se in vista dettaglio)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista paginata dei malfunzionamenti correntemente visualizzati
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati centro di assistenza (se applicabile)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista centri di assistenza (se disponibile)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti per filtri e raggruppamenti
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Membri dello staff per gestione assegnazioni e autorizzazioni
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche aggregate per dashboard e metriche
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente autenticato per autorizzazioni client-side
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    FUNZIONALITÀ JAVASCRIPT ATTESE:
    
    1. segnalaMalfunzionamento(id) - Funzione per segnalare problema via AJAX
       Chiamata POST all'API per incrementare contatore segnalazioni
    
    2. Gestione filtri dinamici e ricerca live
       Auto-submit form o filtri in tempo reale per UX migliorata
    
    3. Validazione form lato client
       Controlli JavaScript prima dell'invio per feedback immediato
    
    4. Interazioni UX avanzate
       - Tooltip informativi sui badge gravità
       - Conferme per azioni distruttive
       - Loading stati per operazioni asincrone
       - Notifiche toast per feedback operazioni
    
    5. Gestione responsive
       - Collapse/expand dettagli su mobile
       - Gestione tocchi e swipe per card
    
    PATTERN DI ACCESSO DATI:
    - window.PageData.prodotto.nome                    (nome prodotto corrente)
    - window.PageData.malfunzionamenti.data           (array malfunzionamenti se LengthAwarePaginator)
    - window.PageData.stats.totale                    (statistiche aggregate)
    - window.PageData.user.can_manage_malfunzionamenti (autorizzazioni utente)
    
    ESEMPIO UTILIZZO:
    function segnalaMalfunzionamento(malfunzionamentoId) {
        // Verifica autorizzazioni
        if (!window.PageData.user || !window.PageData.user.can_view_malfunzionamenti) {
            alert('Non autorizzato');
            return;
        }
        
        // Chiamata AJAX
        fetch(window.apiMalfunzionamentiUrl + '/' + malfunzionamentoId + '/segnala', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            // Aggiorna UI con nuovo conteggio
            // Mostra notifica successo
        })
        .catch(error => {
            // Gestisci errori
            console.error('Errore segnalazione:', error);
        });
    }
    
    INTEGRAZIONE CON LARAVEL:
    - I dati sono sincronizzati al caricamento pagina
    - Le chiamate AJAX utilizzano token CSRF Laravel
    - Le autorizzazioni sono verificabili lato client
    - La paginazione mantiene lo stato dei filtri
*/