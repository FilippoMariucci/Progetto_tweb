{{-- 
    Vista per la ricerca globale dei malfunzionamenti
    LINGUAGGIO: Blade Template (Laravel) - sistema di templating avanzato
    SCOPO: Interfaccia di ricerca cross-prodotto per malfunzionamenti con filtri avanzati
    ACCESSO: Solo tecnici (livello 2+) e staff (livello 3+) autenticati
    PERCORSO: resources/views/malfunzionamenti/ricerca.blade.php
    
    AGGIORNAMENTO: Include immagini dei prodotti per ogni risultato per UX migliorata
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- Titolo statico della pagina per SEO e tab browser --}}
@section('title', 'Ricerca Malfunzionamenti')

{{-- Inizio sezione contenuto principale --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">
    
    {{-- 
        SEZIONE HEADER COMPATTO
        Layout ottimizzato per spazio con navigazione contestuale
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                {{-- Icona ricerca con colore warning per coerenza visiva --}}
                <i class="bi bi-search text-warning me-2"></i>
                Ricerca Malfunzionamenti
            </h2>
            <p class="text-muted small mb-0">
                Cerca soluzioni ai problemi tecnici in tutto il sistema
            </p>
        </div>
        {{-- 
            Navigazione dinamica basata sul ruolo utente
            Bootstrap: btn-group-sm per pulsanti compatti
        --}}
        <div class="btn-group btn-group-sm">
            {{-- 
                Laravel: Controllo ruoli utente con metodi custom nel model User
                Blade: @if/@elseif per logica condizionale ruoli
            --}}
            @if(auth()->user()->isTecnico())
                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @elseif(auth()->user()->isStaff())
                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @endif
        </div>
    </div>

    {{-- 
        BREADCRUMB DINAMICO PER NAVIGAZIONE
        HTML: <nav> semantico con aria-label per accessibilità
    --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>
            {{-- Breadcrumb condizionale basato su ruolo utente --}}
            @if(auth()->user()->isTecnico())
                <li class="breadcrumb-item">
                    <a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a>
                </li>
            @elseif(auth()->user()->isStaff())
                <li class="breadcrumb-item">
                    <a href="{{ route('staff.dashboard') }}">Dashboard Staff</a>
                </li>
            @endif
            <li class="breadcrumb-item active">Ricerca Malfunzionamenti</li>
        </ol>
    </nav>

    {{-- 
        SEZIONE FORM RICERCA COMPATTO
        Form ottimizzato per ricerca rapida con layout responsive
    --}}
    <div class="row mb-3">
        <div class="col-12">
            {{-- Card senza bordi per design moderno --}}
            <div class="card border-0 shadow-sm">
                {{-- Header card con colore warning per coerenza --}}
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filtri di Ricerca
                    </h6>
                </div>
                <div class="card-body py-3">
                    {{-- 
                        Form GET per mantenere filtri nell'URL (SEO-friendly)
                        Bootstrap: row g-3 per layout griglia con gap
                    --}}
                    <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}" class="row g-3">
                        {{-- 
                            Campo ricerca principale testuale
                            HTML: autocomplete="off" per disabilitare suggerimenti browser
                        --}}
                        <div class="col-lg-4 col-md-6">
                            <label for="q" class="form-label small fw-semibold">Cerca problema:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   id="q" 
                                   name="q" 
                                   placeholder="es: non si accende, perdita acqua..."
                                   value="{{ request('q') }}"
                                   autocomplete="off">
                        </div>
                        
                        {{-- 
                            Filtro gravità con emoji per UX migliorata
                            Laravel: request('gravita') recupera valore dal query string
                        --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="gravita" class="form-label small fw-semibold">Gravità:</label>
                            <select class="form-select form-select-sm" id="gravita" name="gravita">
                                <option value="">Tutte</option>
                                {{-- 
                                    Opzioni con controllo selected dinamico
                                    PHP: Operatore ternario per attributo selected
                                --}}
                                <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>🔴 Critica</option>
                                <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>🟠 Alta</option>
                                <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>🟡 Media</option>
                                <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>🟢 Bassa</option>
                            </select>
                        </div>
                        
                        {{-- Filtro difficoltà riparazione con emoji --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="difficolta" class="form-label small fw-semibold">Difficoltà:</label>
                            <select class="form-select form-select-sm" id="difficolta" name="difficolta">
                                <option value="">Tutte</option>
                                <option value="facile" {{ request('difficolta') == 'facile' ? 'selected' : '' }}>✅ Facile</option>
                                <option value="media" {{ request('difficolta') == 'media' ? 'selected' : '' }}>⚠️ Media</option>
                                <option value="difficile" {{ request('difficolta') == 'difficile' ? 'selected' : '' }}>🔧 Difficile</option>
                                <option value="esperto" {{ request('difficolta') == 'esperto' ? 'selected' : '' }}>👨‍🔬 Esperto</option>
                            </select>
                        </div>
                        
                        {{-- 
                            Filtro categoria prodotto dinamico
                            Laravel: Foreach per iterare array associativo passato dal controller
                        --}}
                        <div class="col-lg-2 col-md-6">
                            <label for="categoria_prodotto" class="form-label small fw-semibold">Categoria:</label>
                            <select class="form-select form-select-sm" id="categoria_prodotto" name="categoria_prodotto">
                                <option value="">Tutte</option>
                                @foreach($categorieProdotti as $valore => $etichetta)
                                    <option value="{{ $valore }}" {{ request('categoria_prodotto') == $valore ? 'selected' : '' }}>
                                        {{ $etichetta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Pulsanti azione form --}}
                        <div class="col-lg-2 col-md-6">
                            {{-- Label nascosta su mobile per allineamento --}}
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                {{-- Pulsante submit principale --}}
                                <button type="submit" class="btn btn-warning btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- Pulsante reset --}}
                                <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE STATISTICHE RICERCA COMPATTE
        Mostrate solo quando ci sono filtri attivi
        Laravel: request()->hasAny() controlla presenza parametri specifici
    --}}
    @if(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
        <div class="row g-2 mb-3">
            {{-- Statistica 1: Totale risultati --}}
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-search text-primary fs-5"></i>
                        {{-- 
                            Accesso array statistiche dal controller
                            PHP: Notazione array per accedere ai valori
                        --}}
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['totale_trovati'] }}</h6>
                        <small class="text-muted">Risultati Trovati</small>
                    </div>
                </div>
            </div>
            {{-- Statistica 2: Malfunzionamenti critici --}}
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['critici'] }}</h6>
                        <small class="text-muted">Critici</small>
                    </div>
                </div>
            </div>
            {{-- Statistica 3: Alta priorità --}}
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-circle text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['alta_priorita'] }}</h6>
                        <small class="text-muted">Alta Priorità</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 
        SEZIONE RISULTATI CON IMMAGINI
        Layout avanzato con immagini prodotto per identificazione rapida
    --}}
    <div class="row">
        <div class="col-12">
            {{-- 
                Condizionale per risultati trovati
                Laravel: count() metodo Collection per contare elementi
            --}}
            @if($malfunzionamenti->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check text-success me-1"></i>
                            {{-- Laravel: total() metodo LengthAwarePaginator --}}
                            Risultati della Ricerca ({{ $malfunzionamenti->total() }} trovati)
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        {{-- 
                            Iterazione sui risultati paginati
                            Foreach Blade per ogni malfunzionamento trovato
                        --}}
                        @foreach($malfunzionamenti as $malfunzionamento)
                            {{-- 
                                Card risultato con hover effect CSS
                                CSS: hover-light classe custom per feedback visivo
                            --}}
                            <div class="border-bottom p-3 hover-light">
                                {{-- Layout responsive a tre colonne --}}
                                <div class="row align-items-start">
                                    
                                    {{-- 
                                        COLONNA 1: IMMAGINE PRODOTTO
                                        Sezione per identificazione visiva rapida
                                    --}}
                                    <div class="col-lg-2 col-md-3 col-sm-4 mb-3 mb-lg-0">
                                        <div class="position-relative">
                                            {{-- 
                                                Condizionale per immagine prodotto
                                                Laravel: Relazione Eloquent prodotto->foto
                                            --}}
                                            @if($malfunzionamento->prodotto->foto)
                                                {{-- 
                                                    Immagine reale del prodotto
                                                    Laravel: asset() helper per URL storage
                                                    CSS: object-fit: contain per mantenere proporzioni
                                                --}}
                                                <img src="{{ asset('storage/' . $malfunzionamento->prodotto->foto) }}" 
                                                     class="img-fluid product-thumb rounded shadow-sm" 
                                                     alt="{{ $malfunzionamento->prodotto->nome }}"
                                                     style="width: 100%; height: 120px; object-fit: contain; background-color: #f8f9fa;">
                                            @else
                                                {{-- 
                                                    Placeholder per prodotti senza immagine
                                                    Bootstrap: d-flex align-items-center justify-content-center per centraggio
                                                --}}
                                                <div class="product-thumb-placeholder rounded shadow-sm d-flex align-items-center justify-content-center bg-light" 
                                                     style="width: 100%; height: 120px;">
                                                    <div class="text-center">
                                                        <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                        {{-- Laravel: Str::limit() helper per troncare testo --}}
                                                        <div class="small text-muted mt-1">{{ Str::limit($malfunzionamento->prodotto->nome, 15) }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            {{-- 
                                                Badge categoria sovrapposto
                                                Bootstrap: position-absolute per posizionamento assoluto
                                                PHP: str_replace() per formattare nome categoria
                                            --}}
                                            <div class="position-absolute top-0 end-0 m-1">
                                                <span class="badge bg-secondary small">
                                                    {{ ucfirst(str_replace('_', ' ', $malfunzionamento->prodotto->categoria)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- 
                                        COLONNA 2: CONTENUTO PRINCIPALE
                                        Informazioni dettagliate del malfunzionamento
                                    --}}
                                    <div class="col-lg-7 col-md-6 col-sm-8 mb-3 mb-lg-0">
                                        {{-- Titolo con badge gravità integrato --}}
                                        <h6 class="mb-2 fw-bold">
                                            {{-- 
                                                Link al dettaglio malfunzionamento
                                                Laravel: route() con parametri array
                                            --}}
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="text-decoration-none text-dark">
                                                {{ $malfunzionamento->titolo }}
                                            </a>
                                            
                                            {{-- 
                                                Badge gravità con colori dinamici
                                                PHP: Array associativo per mapping colori Bootstrap
                                            --}}
                                            @php
                                                $badges = [
                                                    'critica' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'bassa' => 'success'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $badges[$malfunzionamento->gravita] ?? 'secondary' }} small ms-1">
                                                {{ ucfirst($malfunzionamento->gravita) }}
                                            </span>
                                        </h6>
                                        
                                        {{-- 
                                            Descrizione troncata per overview
                                            Laravel: Str::limit() con lunghezza custom e ellipsis
                                        --}}
                                        <p class="text-muted small mb-2">
                                            {{ Str::limit($malfunzionamento->descrizione, 120, '...') }}
                                        </p>
                                        
                                        {{-- Informazioni prodotto compatte --}}
                                        <div class="d-flex align-items-center text-muted small mb-2">
                                            <i class="bi bi-box me-1"></i>
                                            {{-- Accesso relazioni Eloquent annidate --}}
                                            <strong class="me-2">{{ $malfunzionamento->prodotto->nome }}</strong>
                                            @if($malfunzionamento->prodotto->modello)
                                                <span class="me-2">- {{ $malfunzionamento->prodotto->modello }}</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Badge difficoltà e tempo stimato --}}
                                        <div class="d-flex flex-wrap gap-1">
                                            {{-- 
                                                Mapping colori per difficoltà
                                                PHP: Array associativo per gestire colori badge
                                            --}}
                                            @php
                                                $diffBadges = [
                                                    'facile' => 'success',
                                                    'media' => 'info', 
                                                    'difficile' => 'warning',
                                                    'esperto' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $diffBadges[$malfunzionamento->difficolta] ?? 'secondary' }} small">
                                                {{ ucfirst($malfunzionamento->difficolta) }}
                                            </span>
                                            
                                            {{-- Badge tempo solo se disponibile --}}
                                            @if($malfunzionamento->tempo_stimato)
                                                <span class="badge bg-light text-dark small">
                                                    <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }} min
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- 
                                        COLONNA 3: AZIONI E STATISTICHE
                                        Controlli utente e metriche
                                    --}}
                                    <div class="col-lg-3 col-md-3 col-12">
                                        {{-- 
                                            Contatore segnalazioni con data-attribute per JavaScript
                                            HTML: data-* per permettere aggiornamenti AJAX
                                        --}}
                                        <div class="text-center mb-3">
                                            <span class="badge bg-primary" data-segnalazioni-count="{{ $malfunzionamento->id }}">
                                                <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                                            </span>
                                        </div>
                                        
                                        {{-- 
                                            Pulsanti azione in layout verticale
                                            Bootstrap: d-grid per bottoni full-width, gap-1 per spaziatura
                                        --}}
                                        <div class="d-grid gap-1">
                                            {{-- Pulsante principale: visualizza soluzione --}}
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                            </a>
                                            
                                            {{-- 
                                                Pulsante segnalazione con JavaScript
                                                HTML: onclick per gestire chiamata AJAX
                                                title per tooltip informativo
                                            --}}
                                            <button type="button" 
                                                    class="btn btn-outline-warning btn-sm segnala-btn"
                                                    onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                    title="Segnala di aver riscontrato questo problema">
                                                <i class="bi bi-exclamation-circle me-1"></i>Segnala Problema
                                            </button>
                                            
                                            {{-- Link rapido al prodotto correlato --}}
                                            <a href="{{ route('prodotti.completo.show', $malfunzionamento->prodotto) }}" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-box me-1"></i>Vedi Prodotto
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- 
                    SEZIONE PAGINAZIONE COMPATTA
                    Laravel: Paginazione con conservazione query string
                --}}
                @if($malfunzionamenti->hasPages())
                    <div class="row mt-3">
                        <div class="col-12">
                            {{-- Info paginazione per UX --}}
                            <div class="text-center mb-2">
                                <small class="text-muted">
                                    {{-- Laravel: Metodi LengthAwarePaginator per info paginazione --}}
                                    Visualizzati {{ $malfunzionamenti->firstItem() }}-{{ $malfunzionamenti->lastItem() }} 
                                    di {{ $malfunzionamenti->total() }} malfunzionamenti
                                </small>
                            </div>
                            {{-- 
                                Links paginazione con query string preservation
                                Laravel: appends() mantiene parametri GET, links() genera HTML nav
                            --}}
                            <div class="d-flex justify-content-center">
                                {{ $malfunzionamenti->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
                
            {{-- 
                CASO: RICERCA EFFETTUATA MA NESSUN RISULTATO
                Stato di ricerca fallita con suggerimenti UX
            --}}
            @elseif(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        {{-- Icona grande per stato vuoto --}}
                        <i class="bi bi-search text-muted opacity-50" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nessun risultato trovato</h5>
                        <p class="text-muted">Prova a modificare i filtri di ricerca o utilizza parole chiave diverse.</p>
                        
                        {{-- Azioni suggerite per recupero --}}
                        <div class="mt-4">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Ricerca
                            </a>
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-collection me-1"></i>Sfoglia Catalogo
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- 
                    STATO INIZIALE CON SUGGERIMENTI
                    Pagina di ricerca al primo caricamento senza filtri
                --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-primary opacity-75" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Cerca Malfunzionamenti e Soluzioni</h5>
                        <p class="text-muted mb-4">Utilizza i filtri sopra per trovare rapidamente le soluzioni ai problemi tecnici.</p>
                        
                        {{-- Sezione suggerimenti per ottimizzare ricerca --}}
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-start">
                                    <h6 class="fw-semibold mb-3">💡 Suggerimenti per la ricerca:</h6>
                                    {{-- Grid suggerimenti con icone e descrizioni --}}
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Parole chiave specifiche</strong>
                                                    <div class="small text-muted">"non si accende", "perdita", "rumore"</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Combina filtri</strong>
                                                    <div class="small text-muted">Categoria + gravità per risultati precisi</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Emergenze</strong>
                                                    <div class="small text-muted">Parti dalla gravità "Critica"</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Filtro categoria</strong>
                                                    <div class="small text-muted">Restringe i risultati per tipo prodotto</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI AVANZATI
    Blade: @push('styles') aggiunge CSS al layout principale
    CSS ottimizzato per ricerca con immagini e UX moderna
--}}
@push('styles')
<style>
/* 
    CSS: STILI BASE COMPONENTI
    Fondamenta per design system coerente
*/

/* Card base con transizioni smooth */
.card {
    border-radius: 0.5rem;                    /* Bordi arrotondati moderni */
    transition: all 0.2s ease;               /* Transizione per hover effects */
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;  /* Ombra pronunciata al hover */
}

/* 
    CSS: IMMAGINI PRODOTTO NELLE RICERCHE
    Stili specializzati per thumbnail prodotti
*/

/* Immagini reali dei prodotti */
.product-thumb {
    transition: transform 0.3s ease;         /* Animazione smooth per zoom */
    border: 1px solid #e9ecef;             /* Bordo sottile grigio */
}

.product-thumb:hover {
    transform: scale(1.05);                  /* Zoom leggero al hover */
    border-color: #007bff;                  /* Bordo blu al hover */
}

/* Placeholder per prodotti senza immagine */
.product-thumb-placeholder {
    border: 2px dashed #dee2e6;            /* Bordo tratteggiato per distinguere da immagini reali */
    transition: all 0.2s ease;             /* Transizione smooth per effetti hover */
}

.product-thumb-placeholder:hover {
    border-color: #007bff;                  /* Bordo blu al hover */
    background-color: #f8f9fa !important;  /* Sfondo leggermente più scuro */
}

/* 
    CSS: EFFETTI HOVER E INTERAZIONI
    Feedback visivo per migliorare UX
*/

/* Hover effect per risultati di ricerca */
.hover-light:hover {
    background-color: #f8f9fa;             /* Sfondo grigio chiaro al hover */
    transition: background-color 0.2s ease; /* Transizione smooth */
}

/* 
    CSS: BADGE E COMPONENTI COMPATTI
    Ottimizzazione dimensioni per layout denso
*/

/* Badge più compatti per layout mobile-friendly */
.badge.small {
    font-size: 0.7rem;                     /* Font ridotto */
    padding: 0.25rem 0.5rem;              /* Padding compatto */
}

/* 
    CSS: FORM CONTROLS COMPATTI
    Componenti form ottimizzati per spazio
*/
.form-control-sm,
.form-select-sm {
    font-size: 0.875rem;                   /* Font leggermente ridotto */
    padding: 0.375rem 0.75rem;            /* Padding standard Bootstrap small */
}

/* 
    CSS: SPAZIATURA CARD PERSONALIZZATA
    Controllo fine dello spazio interno
*/
.card-body.py-2 {
    padding-top: 0.5rem !important;        /* Padding verticale ridotto */
    padding-bottom: 0.5rem !important;
}

.card-body.py-3 {
    padding-top: 0.75rem !important;       /* Padding verticale medio */
    padding-bottom: 0.75rem !important;
}

/* 
    CSS: EVIDENZIAZIONE TERMINI DI RICERCA
    Highlight per risultati di ricerca (se implementato lato server)
*/
mark {
    background-color: #fff3cd;             /* Sfondo giallo per highlight */
    padding: 0.125em 0.25em;              /* Padding minimo */
    border-radius: 0.25rem;               /* Bordi arrotondati */
    font-weight: 600;                     /* Grassetto per evidenziare */
}

/* 
    CSS: ANIMAZIONI BADGE GRAVITÀ
    Attira attenzione su problemi critici
*/
.badge.bg-danger {
    animation: pulse-danger 2s infinite;   /* Animazione pulsante per criticità */
}

@keyframes pulse-danger {
    0%, 100% { opacity: 1; }              /* Opacità piena */
    50% { opacity: 0.8; }                 /* Opacità ridotta a metà animazione */
}

/* 
    CSS RESPONSIVE: ADATTAMENTI MOBILE
    Media queries per ottimizzazione dispositivi piccoli
*/

/* Tablet e smartphone - max-width: 768px */
@media (max-width: 768px) {
    .col-lg-2 {
        margin-bottom: 1rem;               /* Margine bottom per immagini su mobile */
    }
    
    /* Separatore visivo su mobile per azioni */
    .col-lg-3.col-md-3.col-12 {
        border-top: 1px solid #dee2e6;    /* Bordo superiore */
        padding-top: 1rem;                /* Padding superiore */
        margin-top: 1rem;                 /* Margine superiore */
    }
    
    /* Riduzione altezza immagini su tablet */
    .product-thumb,
    .product-thumb-placeholder {
        height: 100px !important;         /* Altezza ridotta per tablet */
    }
    
    /* Button group verticale su mobile */
    .btn-group {
        flex-direction: column;            /* Stack verticale */
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important; /* Ripristina bordi arrotondati */
        margin-bottom: 0.25rem;            /* Spaziatura tra bottoni */
    }
    
    /* Flex direction column per gap spacing */
    .d-flex.gap-2 {
        flex-direction: column;            /* Colonna su mobile */
    }
    
    /* Grid gap per bottoni */
    .d-grid.gap-1 .btn {
        margin-bottom: 0.25rem;            /* Margine tra bottoni grid */
    }
}

/* Smartphone - max-width: 576px */
@media (max-width: 576px) {
    /* Padding ridotto per card su smartphone */
    .card-body {
        padding: 0.75rem;                 /* Padding compatto */
    }
    
    /* Immagini ancora più piccole su smartphone */
    .product-thumb,
    .product-thumb-placeholder {
        height: 80px !important;          /* Altezza minimale */
    }
    
    /* Colonna immagine fixed width su mobile */
    .col-lg-2.col-md-3.col-sm-4 {
        flex: 0 0 100px;                  /* Larghezza fissa 100px */
        max-width: 100px;                 /* Massima larghezza */
    }
    
    /* Assicura allineamento top su mobile */
    .row.align-items-start {
        align-items: flex-start !important; /* Forza allineamento superiore */
    }
}

/* 
    CSS: STATI DI CARICAMENTO
    Feedback visivo per operazioni asincrone
*/

/* Bottone disabilitato */
.btn:disabled {
    opacity: 0.6;                         /* Opacità ridotta */
    cursor: not-allowed;                  /* Cursore di divieto */
}

/* Spinner di caricamento per bottoni */
.btn-loading::after {
    content: "";                          /* Pseudo-elemento vuoto */
    display: inline-block;                /* Display inline per posizionamento */
    width: 12px;
    height: 12px;
    margin-left: 6px;                     /* Spazio dal testo bottone */
    border: 2px solid #f3f3f3;           /* Bordo grigio chiaro */
    border-top: 2px solid #ffc107;       /* Bordo superiore colorato */
    border-radius: 50%;                   /* Cerchio perfetto */
    animation: spin 1s linear infinite;   /* Animazione rotazione */
}

@keyframes spin {
    0% { transform: rotate(0deg); }       /* Inizio rotazione */
    100% { transform: rotate(360deg); }   /* Fine rotazione completa */
}

/* 
    CSS: TRANSIZIONI GENERALI
    Animazioni coerenti per tutti i componenti
*/
.btn, .badge, .product-thumb {
    transition: all 0.2s ease-in-out;     /* Transizione universale smooth */
}

/* Hover effect per bottoni con movimento */
.btn:hover {
    transform: translateY(-1px);          /* Solleva leggermente il bottone */
}

/* 
    CSS: ALERT PERSONALIZZATI
    Stili per messaggi di sistema
*/
.alert {
    border: none;                         /* Rimuove bordi default */
    border-radius: 0.5rem;               /* Bordi arrotondati moderni */
}

.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra per elevazione */
}

/* 
    CSS: BREADCRUMB PERSONALIZZATO
    Navigazione migliorata
*/
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";                         /* Separatore custom */
    color: #6c757d;                      /* Colore grigio */
}

/* 
    CSS: SCROLLBAR PERSONALIZZATA
    Miglioramento estetico per overflow
*/
.overflow-auto::-webkit-scrollbar {
    width: 6px;                          /* Larghezza scrollbar */
    height: 6px;                         /* Altezza scrollbar orizzontale */
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;                /* Sfondo track */
    border-radius: 6px;                 /* Bordi arrotondati */
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;                /* Colore thumb */
    border-radius: 6px;                 /* Bordi arrotondati */
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;                /* Colore hover thumb */
}

/* 
    CSS: FOCUS MIGLIORATO
    Accessibilità e usabilità keyboard
*/
.form-control:focus,
.form-select:focus {
    border-color: #ffc107;               /* Bordo giallo al focus */
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); /* Ombra focus gialla */
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT AVANZATA
    Blade: @push('scripts') per script fine pagina
    JavaScript per funzionalità ricerca e interattività
--}}
@push('scripts')
<script>
/*
    JavaScript: Configurazione globale API e dati pagina
    Setup per chiamate AJAX e gestione stato applicazione
*/

// URL API per operazioni sui malfunzionamenti (segnalazioni, etc.)
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";

// Inizializzazione oggetto dati globale (pattern singleton)
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    Pattern standard per sincronizzazione backend-frontend
    @json() garantisce encoding sicuro e gestione caratteri speciali
    isset() previene errori per variabili non definite
*/

// Dati prodotto singolo (se in contesto specifico)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Array di tutti i prodotti (per filtri e suggerimenti)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento singolo (se in vista dettaglio)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Collection paginata dei risultati di ricerca
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati centro di assistenza (se applicabile)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Array centri di assistenza per lookup geografici
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti per filtri dinamici
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members per autorizzazioni e assegnazioni
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche aggregate per dashboard e analisi
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente per autorizzazioni client-side
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    Dati specifici per performance monitoring e analytics
    Metriche utili per ottimizzazione UX e tracking comportamento utente
*/
window.PageData.malfunzionamentiTotal = @json($malfunzionamenti->total()); // Totale risultati disponibili
window.PageData.malfunzionamentiCount = @json($malfunzionamenti->count()); // Risultati pagina corrente
window.PageData.searchActive = @json(request('q') ? true : false);         // Flag ricerca attiva

/*
    FUNZIONI JAVASCRIPT ATTESE:
    
    1. segnalaMalfunzionamento(id)
       - Chiamata POST AJAX per incrementare contatore segnalazioni
       - Aggiornamento UI in tempo reale del badge contatore
       - Gestione stati loading e feedback utente
       - Validazione autorizzazioni lato client
    
    2. Ricerca live e filtri dinamici
       - Auto-complete per campo ricerca principale
       - Filtri cascata (categoria → prodotti specifici)
       - Debouncing per ottimizzare performance
       - History API per URL friendly
    
    3. Gestione immagini lazy loading
       - Caricamento asincrono thumbnail prodotti
       - Placeholder animati durante caricamento
       - Fallback per immagini mancanti
       - Ottimizzazione bandwidth mobile
    
    4. Interazioni UX avanzate
       - Tooltip informativi su badge e icone
       - Modal preview per risultati rapidi
       - Keyboard shortcuts per power users
       - Salvataggio preferenze ricerca locali
    
    ESEMPIO IMPLEMENTAZIONE SEGNALAZIONE:
    
    function segnalaMalfunzionamento(malfunzionamentoId) {
        // Validazione autorizzazioni
        if (!window.PageData.user || !window.PageData.user.can_view_malfunzionamenti) {
            showAlert('Non autorizzato a segnalare malfunzionamenti', 'error');
            return;
        }
        
        // Riferimento UI elementi
        const btn = document.querySelector(`button[onclick*="${malfunzionamentoId}"]`);
        const badge = document.querySelector(`[data-segnalazioni-count="${malfunzionamentoId}"]`);
        
        // Stato loading
        btn.disabled = true;
        btn.classList.add('btn-loading');
        
        // Chiamata AJAX con error handling
        fetch(`${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                timestamp: new Date().toISOString(),
                source: 'ricerca_globale'
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            // Aggiornamento UI con nuovo conteggio
            if (badge && data.nuovo_conteggio) {
                badge.innerHTML = `<i class="bi bi-flag me-1"></i>${data.nuovo_conteggio} segnalazioni`;
                badge.classList.add('animate__animated', 'animate__pulse');
            }
            
            // Feedback positivo
            showAlert('Segnalazione registrata con successo', 'success');
            
            // Analytics tracking
            if (window.gtag) {
                gtag('event', 'segnala_malfunzionamento', {
                    'malfunzionamento_id': malfunzionamentoId,
                    'source': 'ricerca_globale'
                });
            }
        })
        .catch(error => {
            console.error('Errore segnalazione:', error);
            showAlert('Errore durante la segnalazione. Riprova più tardi.', 'error');
        })
        .finally(() => {
            // Ripristino stato bottone
            btn.disabled = false;
            btn.classList.remove('btn-loading');
        });
    }
    
    PATTERN DI ACCESSO DATI:
    - window.PageData.stats.totale_trovati         (risultati ricerca corrente)
    - window.PageData.malfunzionamenti.data        (array risultati se Collection)
    - window.PageData.user.ruolo                   (autorizzazioni utente)
    - window.PageData.searchActive                 (flag ricerca in corso)
    
    INTEGRAZIONE LARAVEL:
    - Token CSRF automatico da meta tag
    - URL API configurati via Blade
    - Autorizzazioni sincronizzate con backend
    - Paginazione AJAX con query string preservation
*/

</script>
@endpush