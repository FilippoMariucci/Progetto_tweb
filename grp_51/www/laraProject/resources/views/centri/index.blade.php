{{-- 
    Vista Elenco Centri Assistenza
    File: resources/views/centri/index.blade.php
    
    Questa vista mostra l'elenco dei centri di assistenza tecnica distribuiti sul territorio.
    È accessibile pubblicamente (Livello 1) ma con informazioni complete per utenti autenticati.
    
    Funzionalità:
    - Visualizzazione centri con filtri per provincia e città
    - Ricerca per nome centro, città, indirizzo
    - Informazioni di contatto per ogni centro
    - Layout responsive e user-friendly
--}}

@extends('layouts.app')

@section('title', 'Centri di Assistenza')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER DELLA PAGINA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                Centri di Assistenza Tecnica
            </h1>
            <p class="lead text-muted">
                Trova il centro di assistenza più vicino a te per supporto tecnico professionale
            </p>
        </div>
    </div>

    {{-- === SEZIONE FILTRI E RICERCA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    {{-- Form di ricerca con metodo GET per mantenere i parametri nell'URL --}}
                    <form method="GET" action="{{ route('centri.index') }}" class="row g-3" id="searchForm">
                        
                        {{-- Campo ricerca generale --}}
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome centro, città, indirizzo...">
                        </div>
                        
                        {{-- Filtro per provincia --}}
                        <div class="col-md-3">
                            <label for="provincia" class="form-label fw-semibold">
                                <i class="bi bi-map me-1"></i>Provincia
                            </label>
                            <select name="provincia" id="provincia" class="form-select">
                                <option value="">Tutte le province</option>
                                {{-- Le province dovrebbero essere passate dal controller --}}
                                @php
                                    $province_italiane = [
                                        'AN' => 'Ancona', 'RM' => 'Roma', 'MI' => 'Milano', 'NA' => 'Napoli', 
                                        'TO' => 'Torino', 'FI' => 'Firenze', 'BA' => 'Bari', 'PA' => 'Palermo',
                                        'GE' => 'Genova', 'BO' => 'Bologna', 'VE' => 'Venezia', 'CT' => 'Catania'
                                    ];
                                @endphp
                                @foreach($province_italiane as $sigla => $nome)
                                    <option value="{{ $sigla }}" {{ request('provincia') == $sigla ? 'selected' : '' }}>
                                        {{ $sigla }} - {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filtro per città --}}
                        <div class="col-md-3">
                            <label for="citta" class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i>Città
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="citta" 
                                   name="citta" 
                                   value="{{ request('citta') }}"
                                   placeholder="Nome città">
                        </div>
                        
                        {{-- Pulsanti di azione --}}
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-1">
                                {{-- Pulsante per eseguire la ricerca --}}
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- Pulsante reset visibile solo se ci sono filtri attivi --}}
                                @if(request()->hasAny(['search', 'provincia', 'citta']))
                                    <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm">
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

    {{-- === STATISTICHE RAPIDE === --}}
    {{-- Mostra statistiche solo se disponibili dal controller --}}
    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3">
                    {{-- Numero totale di centri --}}
                    <div class="badge bg-primary fs-6 py-2 px-3">
                        <i class="bi bi-geo-alt me-1"></i>{{ $stats['totale_centri'] ?? 0 }} centri totali
                    </div>
                    {{-- Centri con tecnici disponibili --}}
                    <div class="badge bg-success fs-6 py-2 px-3">
                        <i class="bi bi-people me-1"></i>{{ $stats['centri_con_tecnici'] ?? 0 }} con tecnici disponibili
                    </div>
                    {{-- Badge per filtro provincia attivo --}}
                    @if(request('provincia'))
                        <div class="badge bg-info fs-6 py-2 px-3">
                            <i class="bi bi-filter me-1"></i>Provincia: {{ request('provincia') }}
                        </div>
                    @endif
                    {{-- Numero centri nella provincia selezionata --}}
                    @if(isset($stats['per_provincia']) && request('provincia') && isset($stats['per_provincia'][request('provincia')]))
                        <div class="badge bg-warning fs-6 py-2 px-3">
                            <i class="bi bi-building me-1"></i>{{ $stats['per_provincia'][request('provincia')] }} nella provincia
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- === SEZIONE RISULTATI === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Se ci sono centri da mostrare --}}
            @if(isset($centri) && $centri->count() > 0)
                {{-- Header con conteggio e info paginazione --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Centri Trovati 
                        <span class="badge bg-secondary">{{ $centri->total() }}</span>
                    </h4>
                    
                    {{-- Informazioni sulla paginazione --}}
                    <div class="d-flex align-items-center gap-3">
                        @if($centri->hasPages())
                            <small class="text-muted">
                                Pagina {{ $centri->currentPage() }} di {{ $centri->lastPage() }}
                            </small>
                        @endif
                    </div>
                </div>

                {{-- === GRIGLIA CENTRI DI ASSISTENZA === --}}
                <div class="row g-4">
                    {{-- Loop attraverso tutti i centri paginati --}}
                    @foreach($centri as $centro)
                        <div class="col-md-6 col-lg-4">
                            {{-- Card per ogni centro con altezza uniforme --}}
                            <div class="card card-custom h-100">
                                <div class="card-body">
                                    
                                    {{-- Header del centro con nome e badge --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1">
                                                <i class="bi bi-building text-primary me-2"></i>
                                                {{ $centro->nome }}
                                            </h5>
                                            <div class="mb-2">
                                                {{-- Badge provincia e città --}}
                                                <span class="badge bg-primary">
                                                    {{ $centro->provincia }} - {{ $centro->citta }}
                                                </span>
                                                
                                                {{-- Badge numero tecnici disponibili --}}
                                                @if($centro->tecnici && $centro->tecnici->count() > 0)
                                                    <span class="badge bg-success ms-1">
                                                        <i class="bi bi-people me-1"></i>{{ $centro->tecnici->count() }} tecnici
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning ms-1">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>Senza tecnici
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- === INFORMAZIONI DI CONTATTO === --}}
                                    <div class="mb-3">
                                        {{-- Indirizzo completo --}}
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-medium">{{ $centro->indirizzo }}</div>
                                                <div class="text-muted">{{ $centro->cap }} {{ $centro->citta }} ({{ $centro->provincia }})</div>
                                            </div>
                                        </div>

                                        {{-- Telefono se disponibile --}}
                                        @if($centro->telefono)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono_formattato ?? $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Email se disponibile --}}
                                        @if($centro->email)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{ $centro->email }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- === ELENCO TECNICI DEL CENTRO === --}}
                                    @if($centro->tecnici && $centro->tecnici->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2">
                                                <i class="bi bi-people me-1"></i>
                                                Tecnici Specializzati
                                            </h6>
                                            {{-- Mostra primi 3 tecnici per non appesantire la card --}}
                                            @foreach($centro->tecnici->take(3) as $tecnico)
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="flex-grow-1">
                                                        {{-- Nome completo del tecnico --}}
                                                        <small class="fw-medium">{{ $tecnico->nome_completo ?? $tecnico->nome . ' ' . $tecnico->cognome }}</small>
                                                        {{-- Specializzazione se disponibile --}}
                                                        @if($tecnico->specializzazione)
                                                            <br>
                                                            <small class="text-muted">{{ $tecnico->specializzazione }}</small>
                                                        @endif
                                                    </div>
                                                    {{-- Indicatore stato attivo --}}
                                                    <div class="ms-2">
                                                        @if(method_exists($tecnico, 'isRecentlyActive') && $tecnico->isRecentlyActive())
                                                            <i class="bi bi-circle-fill text-success" title="Attivo di recente"></i>
                                                        @else
                                                            <i class="bi bi-circle text-success" title="Tecnico disponibile"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            {{-- Mostra conteggio tecnici aggiuntivi se presenti --}}
                                            @if($centro->tecnici->count() > 3)
                                                <small class="text-muted">
                                                    E altri {{ $centro->tecnici->count() - 3 }} tecnici...
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Messaggio quando non ci sono tecnici --}}
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                Nessun tecnico assegnato al momento
                                            </small>
                                        </div>
                                    @endif

                                </div>

                                {{-- === FOOTER CARD CON PULSANTI AZIONE === --}}
                                <div class="card-footer bg-light">
                                    <div class="d-flex gap-2">
                                        {{-- Pulsante chiamata telefonica --}}
                                        @if($centro->telefono)
                                            <a href="tel:{{ $centro->telefono }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-telephone me-1"></i>Chiama
                                            </a>
                                        @endif
                                        
                                        {{-- Pulsante invio email --}}
                                        @if($centro->email)
                                            <a href="mailto:{{ $centro->email }}" class="btn btn-outline-info btn-sm flex-fill">
                                                <i class="bi bi-envelope me-1"></i>Email
                                            </a>
                                        @endif
                                        
                                        {{-- Pulsante dettagli centro --}}
                                        <a href="{{ route('centri.show', $centro) }}" class="btn btn-primary btn-sm flex-fill">
                                            <i class="bi bi-eye me-1"></i>Dettagli
                                        </a>
                                        
                                        {{-- Pulsante Google Maps se disponibile --}}
                                        @if(method_exists($centro, 'google_maps_link') && $centro->google_maps_link)
                                            <a href="{{ $centro->google_maps_link }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-map"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- === PAGINAZIONE === --}}
                {{-- La paginazione mantiene i parametri di ricerca attivi --}}
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $centri->withQueryString()->links() }}
                    </div>
                </div>

            @else
                {{-- === MESSAGGIO QUANDO NON CI SONO CENTRI === --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-search display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">Nessun centro di assistenza trovato</h4>
                    <p class="text-muted mb-4">
                        {{-- Messaggio diverso se ci sono filtri attivi o meno --}}
                        @if(request()->hasAny(['search', 'provincia', 'citta']))
                            Prova a modificare i filtri di ricerca o 
                            <a href="{{ route('centri.index') }}" class="text-decoration-none">visualizza tutti i centri</a>
                        @else
                            Non ci sono ancora centri di assistenza disponibili.
                        @endif
                    </p>
                    
                    {{-- Pulsante per aggiungere primo centro (solo per admin) --}}
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Centro
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- === SEZIONE DISTRIBUZIONE PER PROVINCIA === --}}
    {{-- Mostra solo se ci sono dati statistici disponibili --}}
    @if(isset($stats['per_provincia']) && count($stats['per_provincia']) > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-map text-primary me-2"></i>
                        Distribuzione per Provincia
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        {{-- Crea un pulsante per ogni provincia con conteggio --}}
                        @foreach($stats['per_provincia'] as $prov => $count)
                            <div class="col-md-3 col-sm-4 col-6">
                                <a href="{{ route('centri.index', ['provincia' => $prov]) }}" 
                                   class="btn btn-outline-primary btn-sm w-100 d-flex justify-content-between align-items-center">
                                    <span>{{ $prov }}</span>
                                    <span class="badge bg-primary">{{ $count }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === SEZIONE INFORMAZIONI AGGIUNTIVE === --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sui Centri di Assistenza
                    </h5>
                    <div class="row">
                        {{-- Colonna servizi offerti --}}
                        <div class="col-md-6">
                            <h6>Servizi Offerti</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i>Assistenza tecnica specializzata</li>
                                <li><i class="bi bi-check text-success me-2"></i>Riparazione e manutenzione prodotti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Consulenza tecnica professionale</li>
                                <li><i class="bi bi-check text-success me-2"></i>Supporto post-vendita</li>
                            </ul>
                        </div>
                        {{-- Colonna istruzioni contatto --}}
                        <div class="col-md-6">
                            <h6>Come Contattare un Centro</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-telephone text-primary me-2"></i>Chiama direttamente il numero indicato</li>
                                <li><i class="bi bi-envelope text-primary me-2"></i>Invia una email per informazioni</li>
                                <li><i class="bi bi-geo-alt text-primary me-2"></i>Visita il centro presso l'indirizzo mostrato</li>
                                <li><i class="bi bi-eye text-primary me-2"></i>Clicca su "Dettagli" per informazioni complete</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === SEZIONE JAVASCRIPT === --}}
{{-- Push degli script specifici per questa pagina --}}
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

{{-- === SEZIONE CSS PERSONALIZZATO === --}}
{{-- Push degli stili specifici per questa pagina --}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI PER CARD === */
.card-custom {
    /* Ombra sottile di default */
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    /* Transizione smooth per effetti hover */
    transition: box-shadow 0.2s ease-in-out;
}

/* Effetto hover per le card */
.card-custom:hover {
    /* Ombra più pronunciata al passaggio del mouse */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* === STILI PER BADGE === */
.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* === RESPONSIVITÀ MOBILE === */
@media (max-width: 768px) {
    /* Su mobile, i badge vanno a capo */
    .badge {
        margin-bottom: 0.25rem;
        display: inline-block;
    }
    
    /* Footer delle card: pulsanti in colonna su mobile */
    .card-footer .d-flex {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    /* Pulsanti a larghezza piena su mobile */
    .card-footer .btn {
        flex: 1 !important;
    }

    /* Riduzione padding per schermi piccoli */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* === STILI SPECIALI PER DISTRIBUZIONE PROVINCE === */
/* Cambia colore badge al hover per i pulsanti provincia */
.btn-outline-primary:hover .badge {
    background-color: white !important;
    color: var(--bs-primary) !important;
}

/* === STILI PER SEZIONE INFORMAZIONI === */
/* Card informazioni con background diverso */
.bg-light .card-title {
    color: var(--bs-primary);
}

/* === ANIMAZIONI E TRANSIZIONI === */
/* Transizione smooth per tutti i link */
a {
    transition: color 0.2s ease-in-out;
}

/* Effetto pulse per indicatori di stato attivo */
.bi-circle-fill.text-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

/* === STILI PER FORM DI RICERCA === */
/* Migliore allineamento dei label */
.form-label {
    margin-bottom: 0.5rem;
}

/* Stile consistente per gli input */
.form-control:focus,
.form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

/* === STILI PER MESSAGGI DI STATO === */
/* Nessun centro trovato - stile messaggio */
.display-1 {
    font-size: 4rem;
    opacity: 0.6;
}
</style>
@endpush