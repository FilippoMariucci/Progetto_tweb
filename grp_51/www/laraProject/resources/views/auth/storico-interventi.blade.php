{{-- 
    ===================================================================
    VISTA STORICO INTERVENTI TECNICI - PARTE 1 - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/auth/storico-interventi.blade.php
    
    DESCRIZIONE:
    Vista completa per visualizzazione cronologia interventi tecnici con immagini ottimizzate.
    Fornisce interfaccia per consultare storico malfunzionamenti risolti con filtri avanzati.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Visualizzazione cronologica interventi tecnici
    - Filtri avanzati per ricerca (periodo, gravità, categoria)
    - Immagini prodotti responsive con fallback
    - Statistiche aggregate per dashboard
    - Paginazione con mantenimento filtri
    - Design responsive e accessibile
    - Badge informativi per categorizzazione visiva
    ===================================================================
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends eredita il layout principale dell'applicazione
--}}
@extends('layouts.app')

{{-- 
    DEFINIZIONE TITOLO PAGINA
    Blade PHP: @section('title') imposta il titolo nel tag <title> HTML
--}}
@section('title', 'Storico Interventi Tecnici')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto che sarà inserito nel layout
--}}
@section('content')
{{-- 
    CONTAINER FLUID PER LAYOUT ESPANSO
    Bootstrap: container-fluid utilizza tutta la larghezza disponibile dello schermo
    Ottimale per tabelle con molte colonne e dati densi
--}}
<div class="container-fluid mt-4">
    
    {{-- === HEADER DELLA PAGINA === 
        HTML: Sezione intestazione con titolo, badge ruolo e navigazione
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{-- 
                        TITOLO PRINCIPALE CON ICONA
                        HTML: h1 con classe h2 per dimensioni ottimizzate
                        Bootstrap Icons: bi-clock-history per tema cronologia
                    --}}
                    <h1 class="h2 mb-1">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Storico Interventi Tecnici
                    </h1>
                    <p class="text-muted mb-0">
                        Cronologia completa degli interventi e soluzioni
                        {{-- 
                            BADGE RUOLO UTENTE DINAMICO
                            Laravel: Controllo condizionale basato su ruolo utente autenticato
                        --}}
                        @if(isset($user))
                            {{-- 
                                CONTROLLI RUOLO CON METODI ELOQUENT
                                Laravel: Metodi personalizzati su User model per controllo autorizzazioni
                                isTecnico(), isStaff(), isAdmin() sono metodi definiti nel model User
                            --}}
                            @if($user->isTecnico())
                                <span class="badge bg-info ms-2">Vista Tecnico</span>
                            @elseif($user->isStaff())
                                <span class="badge bg-warning text-dark ms-2">I Tuoi Prodotti</span>
                            @elseif($user->isAdmin())
                                <span class="badge bg-danger ms-2">Vista Admin</span>
                            @endif
                        @endif
                    </p>
                </div>
                <div>
                    {{-- 
                        NAVIGAZIONE DINAMICA BASATA SU RUOLO
                        Laravel: route() genera URL corretti per dashboard specifiche di ruolo
                        Sistema di autorizzazione: Ogni ruolo ha accesso a dashboard diverse
                    --}}
                    @if(isset($user))
                        @if($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Dashboard Admin
                            </a>
                        @elseif($user->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Dashboard Staff
                            </a>
                        @else
                            <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Dashboard Tecnico
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === 
        HTML: Dashboard KPI con cards colorate per metriche principali
        Laravel: Dati passati dal controller nella variabile $statisticheStorico
    --}}
    @if(isset($statisticheStorico))
        <div class="row mb-4">
            {{-- 
                CARD INTERVENTI TOTALI
                Bootstrap: col-lg-3 per 4 colonne su desktop, col-md-6 per 2 su tablet
            --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-list-check fs-2 me-3"></i>
                            <div>
                                {{-- 
                                    CONTATORE TOTALE INTERVENTI
                                    Laravel: Array associativo $statisticheStorico dal controller
                                    PHP: Null coalescing operator ?? per valore default se chiave mancante
                                --}}
                                <h4 class="mb-0">{{ $statisticheStorico['totale_interventi'] ?? 0 }}</h4>
                                <small>Interventi Totali</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- CARD INTERVENTI OGGI --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-day fs-2 me-3"></i>
                            <div>
                                {{-- 
                                    CONTATORE INTERVENTI ODIERNI
                                    Laravel: Filtro temporale applicato nel controller per data corrente
                                --}}
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_oggi'] ?? 0 }}</h4>
                                <small>Oggi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- CARD INTERVENTI SETTIMANA --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-week fs-2 me-3"></i>
                            <div>
                                {{-- 
                                    CONTATORE INTERVENTI SETTIMANALI
                                    Laravel: Query con whereDate o whereBetween per range temporale
                                --}}
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_settimana'] ?? 0 }}</h4>
                                <small>Settimana</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- CARD INTERVENTI MESE --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-month fs-2 me-3"></i>
                            <div>
                                {{-- CONTATORE INTERVENTI MENSILI --}}
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_mese'] ?? 0 }}</h4>
                                <small>Mese</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === FILTRI DI RICERCA === 
        HTML: Form per filtraggio e ricerca avanzata degli interventi
        UX: Permette agli utenti di trovare interventi specifici rapidamente
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filtri di Ricerca
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        FORM FILTRI CON METHOD GET
                        HTML: GET method per URL condivisibili e bookmark
                        Laravel: action="{{ route('auth.storico-interventi') }}" per self-submit
                    --}}
                    <form method="GET" action="{{ route('auth.storico-interventi') }}">
                        <div class="row g-3">
                            
                            {{-- 
                                CAMPO RICERCA TESTUALE
                                HTML: Input text per ricerca in descrizione o soluzione
                            --}}
                            <div class="col-md-3">
                                <label for="search" class="form-label">Ricerca</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Descrizione o soluzione...">
                            </div>
                            
                            {{-- 
                                SELECT PERIODO TEMPORALE
                                HTML: Options per filtri temporali predefiniti
                                Laravel: request('periodo') mantiene selezione dopo submit
                            --}}
                            <div class="col-md-2">
                                <label for="periodo" class="form-label">Periodo</label>
                                <select name="periodo" id="periodo" class="form-select">
                                    <option value="">Tutti</option>
                                    {{-- 
                                        OPTION CON SELECTED DINAMICO
                                        Blade PHP: Confronto valore corrente per mantenere selezione
                                    --}}
                                    <option value="oggi" {{ request('periodo') == 'oggi' ? 'selected' : '' }}>Oggi</option>
                                    <option value="settimana" {{ request('periodo') == 'settimana' ? 'selected' : '' }}>Settimana</option>
                                    <option value="mese" {{ request('periodo') == 'mese' ? 'selected' : '' }}>Mese</option>
                                    <option value="trimestre" {{ request('periodo') == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                                </select>
                            </div>
                            
                            {{-- 
                                SELECT GRAVITÀ MALFUNZIONAMENTO
                                HTML: Filtro per livello di criticità dell'intervento
                            --}}
                            <div class="col-md-2">
                                <label for="gravita" class="form-label">Gravità</label>
                                <select name="gravita" id="gravita" class="form-select">
                                    <option value="">Tutte</option>
                                    <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>Bassa</option>
                                    <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>Critica</option>
                                </select>
                            </div>
                            
                            {{-- 
                                SELECT CATEGORIA PRODOTTO
                                Laravel: Ciclo dinamico attraverso categorie dal controller
                            --}}
                            <div class="col-md-2">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select name="categoria" id="categoria" class="form-select">
                                    <option value="">Tutte</option>
                                    {{-- 
                                        ITERAZIONE CATEGORIE DINAMICHE
                                        Blade PHP: @foreach attraverso array categorie dal controller
                                    --}}
                                    @if(isset($categorie))
                                        @foreach($categorie as $cat)
                                            <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                                {{-- 
                                                    FORMATTAZIONE CATEGORIA
                                                    PHP: ucfirst() capitalizza, str_replace() pulisce underscore
                                                --}}
                                                {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            {{-- 
                                PULSANTI AZIONE FORM
                                HTML: Submit e reset con feedback visivo per filtri attivi
                            --}}
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="bi bi-search me-1"></i>Cerca
                                    </button>
                                    {{-- 
                                        PULSANTE RESET CONDIZIONALE
                                        Laravel: request()->hasAny() verifica se ci sono filtri attivi
                                        Mostra pulsante reset solo se necessario per UX ottimale
                                    --}}
                                    @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                        <a href="{{ route('auth.storico-interventi') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- === ELENCO INTERVENTI === 
        HTML: Sezione principale con tabella responsive per visualizzazione interventi
        Laravel: Controllo esistenza e conteggio dati collection Eloquent
    --}}
    <div class="row">
        <div class="col-12">
            {{-- 
                CONTROLLO ESISTENZA INTERVENTI
                Laravel: Verifica che collection esista e contenga elementi
                $interventi è una LengthAwarePaginator (collection paginata) dal controller
            --}}
            @if(isset($interventi) && $interventi->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>
                                Elenco Interventi 
                                {{-- 
                                    BADGE CONTEGGIO TOTALE
                                    Laravel: total() restituisce numero totale elementi (non solo pagina corrente)
                                --}}
                                <span class="badge bg-secondary">{{ $interventi->total() }}</span>
                            </h5>
                            
                            {{-- 
                                INFORMAZIONI PAGINAZIONE
                                Laravel: Metodi collection paginata per info navigazione
                            --}}
                            @if($interventi->hasPages())
                                <small class="text-muted">
                                    Pagina {{ $interventi->currentPage() }} di {{ $interventi->lastPage() }}
                                    ({{ $interventi->firstItem() }}-{{ $interventi->lastItem() }} di {{ $interventi->total() }})
                                </small>
                            @endif
                        </div>
                    </div>
                    
                    {{-- 
                        TABELLA RESPONSIVE BOOTSTRAP
                        Bootstrap: table-responsive permette scroll orizzontale su mobile
                        table-hover per feedback visivo su righe
                    --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    {{-- 
                                        HEADER TABELLA CON WIDTH ATTRIBUTES
                                        HTML: width percentuali per controllo layout colonne
                                        scope="col" per accessibilità screen reader
                                    --}}
                                    <th scope="col" width="12%">Data</th>
                                    <th scope="col" width="28%">Prodotto</th>
                                    <th scope="col" width="35%">Problema</th>
                                    <th scope="col" width="12%">Gravità</th>
                                    <th scope="col" width="13%">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    ITERAZIONE ATTRAVERSO INTERVENTI PAGINATI
                                    Laravel: @foreach attraverso collection Eloquent
                                    $intervento è un model con relazioni caricate (eager loading)
                                --}}
                                @foreach($interventi as $intervento)
                                    <tr>
                                        {{-- 
                                            COLONNA DATA COMPATTA
                                            Laravel: Carbon date manipulation per formattazione
                                        --}}
                                        <td>
                                            <div class="small">
                                                {{-- 
                                                    FORMATO DATA GIORNO/MESE/ANNO
                                                    Laravel: $intervento->updated_at è Carbon instance
                                                    format() permette personalizzazione output
                                                --}}
                                                <div class="fw-medium">{{ $intervento->updated_at->format('d/m/Y') }}</div>
                                                {{-- ORA SEPARATA PER LAYOUT COMPATTO --}}
                                                <div class="text-muted">{{ $intervento->updated_at->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA PRODOTTO CON IMMAGINE MIGLIORATA
                                            HTML: Layout complesso con immagine, fallback e badge
                                        --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{-- 
                                                    SEZIONE IMMAGINE PRODOTTO CON FALLBACK
                                                    Laravel: Relazione Eloquent $intervento->prodotto
                                                    Gestione condizionale esistenza foto
                                                --}}
                                                @if($intervento->prodotto->foto)
                                                    <div class="position-relative me-2">
                                                        {{-- 
                                                            IMMAGINE PRINCIPALE PRODOTTO
                                                            Laravel: asset('storage/...') genera URL corretto per file
                                                            HTML: Attributi per ottimizzazione (loading="lazy", onerror)
                                                            CSS: Stili inline per controllo preciso dimensioni
                                                        --}}
                                                        <img src="{{ asset('storage/' . $intervento->prodotto->foto) }}" 
                                                             alt="{{ $intervento->prodotto->nome }}"
                                                             class="rounded shadow-sm product-thumb"
                                                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e9ecef;"
                                                             loading="lazy"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        
                                                        {{-- 
                                                            FALLBACK SE IMMAGINE NON CARICA
                                                            JavaScript: onerror handler mostra questo elemento se img fallisce
                                                            UX: Evita broken image placeholder per migliore aspetto
                                                        --}}
                                                        <div class="bg-light rounded shadow-sm d-none align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px; border: 2px solid #e9ecef;">
                                                            <i class="bi bi-image text-muted" style="font-size: 0.9rem;"></i>
                                                        </div>
                                                        
                                                        {{-- 
                                                            BADGE CATEGORIA SOVRAPPOSTO
                                                            Bootstrap: position-absolute per overlay su immagine
                                                            CSS: translate-middle per centramento perfetto
                                                        --}}
                                                        @if($intervento->prodotto->categoria)
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" 
                                                                  style="font-size: 0.6rem; padding: 0.2rem 0.4rem;"
                                                                  title="{{ ucfirst($intervento->prodotto->categoria) }}">
                                                                {{-- 
                                                                    SWITCH ICONE PER CATEGORIA
                                                                    Blade PHP: @switch per mapping categoria->icona
                                                                    Bootstrap Icons: Icone semantiche per ogni categoria
                                                                --}}
                                                                @switch($intervento->prodotto->categoria)
                                                                    @case('elettrodomestici')
                                                                        <i class="bi bi-lightning"></i>
                                                                        @break
                                                                    @case('informatica')
                                                                        <i class="bi bi-laptop"></i>
                                                                        @break
                                                                    @case('telefonia')
                                                                        <i class="bi bi-phone"></i>
                                                                        @break
                                                                    @case('climatizzazione')
                                                                        <i class="bi bi-snow"></i>
                                                                        @break
                                                                    @case('sicurezza')
                                                                        <i class="bi bi-shield"></i>
                                                                        @break
                                                                    @default
                                                                        <i class="bi bi-gear"></i>
                                                                @endswitch
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    {{-- 
                                                        PLACEHOLDER SE NON C'È IMMAGINE
                                                        HTML: Div con gradiente CSS per aspetto professionale
                                                        Design: Icone categoria specifiche per identificazione visiva
                                                    --}}
                                                    <div class="position-relative me-2">
                                                        <div class="bg-gradient rounded shadow-sm d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px; border: 2px solid #e9ecef; 
                                                                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                                            {{-- 
                                                                ICONA CATEGORIA CON COLORI
                                                                CSS: Colori specifici per ogni categoria per UI consistente
                                                            --}}
                                                            @if($intervento->prodotto->categoria)
                                                                @switch($intervento->prodotto->categoria)
                                                                    @case('elettrodomestici')
                                                                        <i class="bi bi-lightning text-warning" style="font-size: 1.1rem;"></i>
                                                                        @break
                                                                    @case('informatica')
                                                                        <i class="bi bi-laptop text-info" style="font-size: 1.1rem;"></i>
                                                                        @break
                                                                    @case('telefonia')
                                                                        <i class="bi bi-phone text-success" style="font-size: 1.1rem;"></i>
                                                                        @break
                                                                    @case('climatizzazione')
                                                                        <i class="bi bi-snow text-primary" style="font-size: 1.1rem;"></i>
                                                                        @break
                                                                    @case('sicurezza')
                                                                        <i class="bi bi-shield text-danger" style="font-size: 1.1rem;"></i>
                                                                        @break
                                                                    @default
                                                                        <i class="bi bi-gear text-secondary" style="font-size: 1.1rem;"></i>
                                                                @endswitch
                                                            @else
                                                                <i class="bi bi-box text-muted" style="font-size: 1.1rem;"></i>
                                                            @endif
                                                        </div>
                                                        {{-- 
                                                            BADGE CATEGORIA PER PLACEHOLDER
                                                            HTML: Badge testuale quando non c'è immagine
                                                        --}}
                                                        @if($intervento->prodotto->categoria)
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" 
                                                                  style="font-size: 0.6rem; padding: 0.2rem 0.4rem;"
                                                                  title="{{ ucfirst($intervento->prodotto->categoria) }}">
                                                                {{-- 
                                                                    ABBREVIAZIONE CATEGORIA
                                                                    PHP: strtoupper() + substr() per acronimo categoria
                                                                --}}
                                                                {{ strtoupper(substr($intervento->prodotto->categoria, 0, 2)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                {{-- 
                                                    INFORMAZIONI PRODOTTO TESTUALI
                                                    HTML: Layout flex per allineamento ottimale
                                                --}}
                                                <div class="flex-grow-1">
                                                    {{-- NOME PRODOTTO PRINCIPALE --}}
                                                    <div class="fw-medium small">{{ $intervento->prodotto->nome }}</div>
                                                    {{-- 
                                                        METADATI PRODOTTO (MODELLO/CODICE)
                                                        HTML: Flex gap per spaziatura uniforme elementi
                                                    --}}
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($intervento->prodotto->modello)
                                                            <span class="text-muted small">{{ $intervento->prodotto->modello }}</span>
                                                        @endif
                                                        @if($intervento->prodotto->codice)
                                                            <span class="badge bg-light text-dark small" style="font-size: 0.65rem;">
                                                                {{ $intervento->prodotto->codice }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    {{-- 
                                                        INDICATORE STATO PRODOTTO
                                                        Laravel: Controllo condizionale attributo model
                                                    --}}
                                                    @if(isset($intervento->prodotto->attivo))
                                                        <div class="mt-1">
                                                            @if($intervento->prodotto->attivo)
                                                                <span class="badge bg-success-subtle text-success small">
                                                                    <i class="bi bi-check-circle me-1"></i>Attivo
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger-subtle text-danger small">
                                                                    <i class="bi bi-x-circle me-1"></i>Inattivo
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA PROBLEMA E SOLUZIONE
                                            Laravel: Str::limit() helper per troncare testo lungo
                                        --}}
                                        <td>
                                            <div class="small">
                                                {{-- 
                                                    TITOLO INTERVENTO TRONCATO
                                                    Laravel: Str::limit($string, $length) per UX consistente
                                                --}}
                                                <div class="fw-medium mb-1">{{ Str::limit($intervento->titolo ?? 'Intervento', 40) }}</div>
                                                {{-- DESCRIZIONE PROBLEMA --}}
                                                <div class="text-muted">{{ Str::limit($intervento->descrizione, 60) }}</div>
                                                {{-- 
                                                    SOLUZIONE (CONDIZIONALE)
                                                    Laravel: Mostra solo se soluzione esiste
                                                --}}
                                                @if($intervento->soluzione)
                                                    <div class="text-success mt-1">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        {{ Str::limit($intervento->soluzione, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA BADGE GRAVITÀ
                                            PHP: Array associativo per configurazione colori/icone
                                        --}}
                                        <td>
                                            {{-- 
                                                CONFIGURAZIONE GRAVITÀ DINAMICA
                                                PHP: Array associativo con mapping gravità->colore/icona
                                                Questo pattern facilita manutenzione e estensibilità
                                            --}}
                                            @php
                                                $gravitaConfig = [
                                                    'bassa' => ['color' => 'success', 'icon' => 'check-circle'],
                                                    'media' => ['color' => 'warning', 'icon' => 'exclamation-triangle'],
                                                    'alta' => ['color' => 'danger', 'icon' => 'exclamation-diamond'],
                                                    'critica' => ['color' => 'dark', 'icon' => 'x-octagon']
                                                ];
                                                // Fallback per gravità non previste
                                                $config = $gravitaConfig[$intervento->gravita] ?? ['color' => 'secondary', 'icon' => 'circle'];
                                            @endphp
                                            
                                            {{-- 
                                                BADGE GRAVITÀ CON ICONA DINAMICA
                                                Bootstrap: Classi dinamiche basate su configurazione
                                            --}}
                                            <span class="badge bg-{{ $config['color'] }} small">
                                                <i class="bi bi-{{ $config['icon'] }} me-1"></i>
                                                {{ ucfirst($intervento->gravita ?? 'N/D') }}
                                            </span>
                                            
                                            {{-- 
                                                TEMPO STIMATO (CONDIZIONALE)
                                                Laravel: Mostra solo se campo presente nel database
                                            --}}
                                            @if($intervento->tempo_stimato)
                                                <div class="text-muted small mt-1">
                                                    <i class="bi bi-clock me-1"></i>{{ $intervento->tempo_stimato }}min
                                                </div>
                                            @endif
                                        </td>
                                        
                                        {{-- 
                                            COLONNA AZIONI
                                            HTML: Pulsanti verticali per massimizzare spazio
                                        --}}
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm">
                                                {{-- 
                                                    LINK VISUALIZZA DETTAGLI
                                                    Laravel: route() con parametri multipli per nested resource
                                                    Sistema routing: /prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}
                                                --}}
                                                <a href="{{ route('malfunzionamenti.show', [$intervento->prodotto, $intervento]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Dettagli
                                                </a>
                                                
                                                {{-- 
                                                    LINK VISTA PRODOTTO CON AUTORIZZAZIONE
                                                    Laravel: @auth per controllo autenticazione
                                                    Sistema autorizzazioni: Metodi personalizzati su User model
                                                --}}
                                                @auth
                                                    {{-- 
                                                        CONTROLLO PERMESSI UTENTE
                                                        Laravel: canViewMalfunzionamenti() metodo custom su User
                                                        Determina se utente può vedere vista completa o solo pubblica
                                                    --}}
                                                    @if(auth()->user()->canViewMalfunzionamenti())
                                                        <a href="{{ route('prodotti.completo.show', $intervento->prodotto) }}" 
                                                           class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-box me-1"></i>Prodotto
                                                        </a>
                                                    @else
                                                        <a href="{{ route('prodotti.pubblico.show', $intervento->prodotto) }}" 
                                                           class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-box me-1"></i>Prodotto
                                                        </a>
                                                    @endif
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- 
                        PAGINAZIONE CORRETTA CON MANTENIMENTO FILTRI
                        Laravel: Sistema paginazione integrato con query string
                    --}}
                    @if($interventi->hasPages())
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- 
                                    INFORMAZIONI RISULTATI CORRENTI
                                    Laravel: Metodi pagination per info navigazione
                                --}}
                                <div class="text-muted small">
                                    Visualizzazione {{ $interventi->firstItem() }}-{{ $interventi->lastItem() }} 
                                    di {{ $interventi->total() }} risultati
                                </div>
                                
                                {{-- 
                                    LINK PAGINAZIONE CON FILTRI
                                    Laravel: withQueryString() mantiene parametri GET attraverso pagine
                                    Bootstrap: pagination template personalizzato
                                --}}
                                <div>
                                    {{ $interventi->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            @else
                {{-- 
                    STATO VUOTO - NESSUN INTERVENTO TROVATO
                    UX: Messaggio informativo con azioni suggerite
                --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search display-4 text-muted mb-3"></i>
                        <h4 class="text-muted">Nessun intervento trovato</h4>
                        <p class="text-muted mb-4">
                            {{-- 
                                MESSAGGIO DINAMICO BASATO SU FILTRI
                                Laravel: Controllo se ci sono filtri attivi per messaggio contestuale
                            --}}
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                Non ci sono interventi che corrispondono ai filtri selezionati.
                            @else
                                Non sono ancora stati registrati interventi tecnici.
                            @endif
                        </p>
                        
                        {{-- 
                            AZIONI SUGGERITE STATO VUOTO
                            UX: Pulsanti per aiutare utente a trovare contenuti
                        --}}
                        <div class="d-flex justify-content-center gap-3">
                            {{-- PULSANTE RESET FILTRI (CONDIZIONALE) --}}
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                <a href="{{ route('auth.storico-interventi') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                                </a>
                            @endif
                            
                            {{-- LINK CATALOGO TECNICO --}}
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-tools me-1"></i>Catalogo Tecnico
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- === STATISTICHE AGGIUNTIVE === 
        Laravel: Sezione condizionale per analytics avanzati
        Visualizza solo se dati statistici sono disponibili dal controller
    --}}
    @if(isset($statisticheStorico) && isset($statisticheStorico['per_gravita']))
        <div class="row mt-4">
            {{-- 
                GRAFICO DISTRIBUZIONE PER GRAVITÀ
                HTML: Progress bars per visualizzazione percentuali
            --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-pie-chart text-primary me-2"></i>
                            Distribuzione per Gravità
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- 
                            ITERAZIONE STATISTICHE GRAVITÀ
                            Laravel: Loop attraverso array associativo gravità->conteggio
                        --}}
                        @foreach($statisticheStorico['per_gravita'] as $gravita => $count)
                            {{-- 
                                CALCOLO PERCENTUALE DINAMICO
                                PHP: Logica calcolo percentuale con gestione divisione per zero
                            --}}
                            @php
                                $percentage = $statisticheStorico['totale_interventi'] > 0 
                                    ? round(($count / $statisticheStorico['totale_interventi']) * 100, 1) 
                                    : 0;
                                // Match expression per mapping gravità->colore (PHP 8.0+)
                                $color = match($gravita) {
                                    'bassa' => 'success',
                                    'media' => 'warning', 
                                    'alta' => 'danger',
                                    'critica' => 'dark',
                                    default => 'secondary'
                                };
                            @endphp
                            
                            {{-- 
                                PROGRESS BAR PER GRAVITÀ
                                Bootstrap: Progress component con width dinamica
                            --}}
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-medium">{{ ucfirst($gravita) }}</small>
                                    <small class="text-muted">{{ $count }} ({{ $percentage }}%)</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $color }}" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- 
                LISTA PRODOTTI PROBLEMATICI
                Laravel: Sezione condizionale per top prodotti con più problemi
            --}}
            @if(isset($statisticheStorico['prodotti_problematici']))
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Prodotti con Più Problemi
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- 
                                TOP 5 PRODOTTI PROBLEMATICI
                                Laravel: take(5) limita risultati per layout compatto
                            --}}
                            @foreach($statisticheStorico['prodotti_problematici']->take(5) as $prodotto)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        {{-- 
                                            NOME PRODOTTO TRONCATO
                                            Laravel: Str::limit() per controllo lunghezza
                                        --}}
                                        <div class="fw-medium small">{{ Str::limit($prodotto->nome, 25) }}</div>
                                        <span class="badge bg-secondary small">{{ $prodotto->categoria }}</span>
                                    </div>
                                    {{-- 
                                        CONTATORE MALFUNZIONAMENTI
                                        Laravel: malfunzionamenti_count è attributo calcolato da withCount()
                                    --}}
                                    <span class="badge bg-danger">{{ $prodotto->malfunzionamenti_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
{{-- FINE CONTENUTO --}}
@endsection
{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade PHP: @push('styles') aggiunge CSS al layout nell'head
--}}
@push('styles')
<style>
/* === STILI BASE TABELLA === 
   CSS: Personalizzazione componenti Bootstrap per layout ottimizzato
*/

/*
    CELLE TABELLA OTTIMIZZATE
    CSS: Padding e allineamento per densità dati ottimale
*/
.table td {
    padding: 0.5rem 0.75rem;
    vertical-align: middle; /* Allineamento verticale centrale per tutte le righe */
}

/*
    HEADER TABELLA MIGLIORATO
    CSS: Styling testata per migliore leggibilità e gerarchia visiva
*/
.table th {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6; /* Bordo più marcato per separazione */
    font-weight: 600;
    font-size: 0.9rem;
}

/*
    HOVER EFFECT PER RIGHE
    CSS: Feedback visivo su interazione utente
*/
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05); /* Azzurro molto leggero */
}

/*
    BADGE GRAVITÀ CON DIMENSIONI COERENTI
    CSS: Standardizzazione badge per UI consistente
*/
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem; /* Padding bilanciato per leggibilità */
}

/*
    BOTTONI GRUPPO COMPATTI
    CSS: Ottimizzazione spazio per colonna azioni ristretta
*/
.btn-group-vertical .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* === STILI IMMAGINI PRODOTTO === 
   CSS: Sistema completo per gestione immagini con effetti e fallback
*/

/*
    IMMAGINI PRODOTTO CON EFFETTI
    CSS: Transizioni smooth per feedback visivo
*/
.product-thumb {
    transition: all 0.3s ease;
    cursor: pointer; /* Indica interattività */
}

/*
    HOVER EFFECT IMMAGINI
    CSS: Transform e shadow per effetto "lift" moderno
*/
.product-thumb:hover {
    transform: scale(1.1); /* Ingrandimento 10% */
    border-color: #0d6efd !important; /* Bordo blu primario */
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3); /* Ombra blu */
}

/*
    PLACEHOLDER GRADIENTE
    CSS: Styling per contenitori senza immagine
*/
.bg-gradient {
    transition: all 0.3s ease;
}

.bg-gradient:hover {
    transform: scale(1.05); /* Effetto hover più leggero per placeholder */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/*
    BADGE CATEGORIA SOVRAPPOSTI
    CSS: Styling per badge posizionati su immagini
*/
.position-absolute .badge {
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2); /* Ombra per staccare dal background */
}

.position-absolute .badge:hover {
    transform: scale(1.1); /* Effetto hover anche sui badge */
}

/*
    BADGE STATO PRODOTTO
    CSS: Varianti Bootstrap personalizzate per stato attivo/inattivo
*/
.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important; /* Verde molto leggero */
    border: 1px solid rgba(25, 135, 84, 0.2);
}

.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1) !important; /* Rosso molto leggero */
    border: 1px solid rgba(220, 53, 69, 0.2);
}

/*
    COLORI TESTO STATO
    CSS: Override Bootstrap per consistenza colori
*/
.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

/*
    ANIMAZIONE CARICAMENTO IMMAGINI
    CSS: Keyframe per transizione smooth al caricamento
*/
@keyframes imageLoad {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.product-thumb {
    animation: imageLoad 0.4s ease-out; /* Applicazione animazione */
}

/*
    TOOLTIP MIGLIORATO
    CSS: Cursor per elementi con tooltip
*/
[data-bs-toggle="tooltip"] {
    cursor: help; /* Cursor esplicativo per tooltip */
}

/* === RESPONSIVE DESIGN === 
   CSS: Media queries per ottimizzazione dispositivi mobili
*/
@media (max-width: 768px) {
    /*
        NASCONDI COLONNE NON ESSENZIALI SU TABLET
        CSS: Display none per colonne secondarie su schermi medi
    */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        display: none; /* Nascondi colonna data su mobile */
    }
    
    .table th:nth-child(4),
    .table td:nth-child(4) {
        display: none; /* Nascondi gravità su mobile */
    }
    
    /*
        BOTTONI AZIONE ORIZZONTALI SU MOBILE
        CSS: Cambio layout da verticale a orizzontale per spazio
    */
    .btn-group-vertical {
        flex-direction: row;
        gap: 0.25rem;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    /*
        IMMAGINI PIÙ PICCOLE SU MOBILE
        CSS: Riduzione dimensioni per ottimizzazione spazio
    */
    .product-thumb,
    .bg-gradient {
        width: 32px !important;
        height: 32px !important;
    }
    
    .position-absolute .badge {
        font-size: 0.55rem !important;
        padding: 0.1rem 0.3rem !important;
    }
    
    /*
        NASCONDI BADGE SECONDARI SU MOBILE
        CSS: Rimozione elementi non essenziali per spazio
    */
    .badge.bg-light {
        display: none;
    }
}

@media (max-width: 576px) {
    /*
        OTTIMIZZAZIONI EXTREME MOBILE
        CSS: Riduzioni massime per schermi molto piccoli
    */
    .product-thumb,
    .bg-gradient {
        width: 28px !important;
        height: 28px !important;
    }
    
    /*
        SEMPLIFICAZIONE UI ESTREMA
        CSS: Rimozione elementi decorativi per funzionalità core
    */
    .position-absolute .badge {
        display: none;
    }
    
    .bg-success-subtle,
    .bg-danger-subtle {
        display: none;
    }
}

/* === PAGINAZIONE BOOTSTRAP PERSONALIZZATA === 
   CSS: Styling custom per navigazione pagine
*/
.pagination {
    margin: 0; /* Rimozione margini default */
}

/*
    LINK PAGINAZIONE
    CSS: Styling coerente con design system
*/
.page-link {
    color: #0d6efd;
    border-color: #dee2e6;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #0a58ca; /* Blu più scuro su hover */
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/*
    PAGINA ATTIVA
    CSS: Evidenziazione pagina corrente
*/
.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/*
    PAGINA DISABILITATA
    CSS: Stato disabled per prima/ultima pagina
*/
.page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* === ANIMAZIONI E EFFETTI === 
   CSS: Sistema animazioni per migliorare perceived performance
*/

/*
    ANIMAZIONE ENTRATA RIGHE
    CSS: Keyframe per fade-in delle righe tabella
*/
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table tbody tr {
    animation: fadeInUp 0.3s ease-out; /* Applicazione su tutte le righe */
}

/*
    PROGRESS BAR PERSONALIZZATE
    CSS: Styling per grafici statistiche
*/
.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.progress-bar {
    transition: width 0.6s ease; /* Animazione smooth per cambi valore */
}

/*
    EFFETTO LOADING SKELETON
    CSS: Animazione placeholder durante caricamento
*/
.image-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* === MIGLIORAMENTI ACCESSIBILITÀ === 
   CSS: Support per navigazione da tastiera e screen reader
*/
.product-thumb:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/*
    FOCUS VISIBILE PER PULSANTI
    CSS: Indicatori focus per accessibility compliance
*/
.btn:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* === PRINT STYLES === 
   CSS: Ottimizzazioni per output stampato
*/
@media print {
    .product-thumb,
    .bg-gradient {
        width: 20px !important;
        height: 20px !important;
        -webkit-print-color-adjust: exact; /* Mantieni colori in stampa */
        color-adjust: exact;
    }
    
    .position-absolute .badge {
        display: none; /* Rimuovi badge overlay per stampa */
    }
    
    .btn-group-vertical {
        display: none; /* Rimuovi pulsanti interattivi */
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid; /* Evita rottura card tra pagine */
    }
}

/* === DARK MODE SUPPORT === 
   CSS: Supporto automatico tema scuro sistema
*/
@media (prefers-color-scheme: dark) {
    .table {
        --bs-table-bg: #212529;
        --bs-table-color: #fff;
    }
    
    .card {
        background-color: #2d3748;
        color: #fff;
    }
    
    .bg-light {
        background-color: #374151 !important;
    }
}

/* === HIGH CONTRAST SUPPORT === 
   CSS: Accessibilità per utenti con necessità alto contrasto
*/
@media (prefers-contrast: high) {
    .product-thumb,
    .bg-gradient {
        border-width: 3px !important; /* Bordi più spessi */
    }
    
    .badge {
        border: 1px solid; /* Bordi aggiuntivi per definizione */
    }
}

/* === REDUCED MOTION SUPPORT === 
   CSS: Accessibilità per utenti sensibili alle animazioni
*/
@media (prefers-reduced-motion: reduce) {
    .product-thumb,
    .bg-gradient,
    .position-absolute .badge,
    * {
        transition: none !important; /* Disabilita tutte le transizioni */
    }
    
    .table tbody tr {
        animation: none !important; /* Disabilita animazioni */
    }
}

/* === CUSTOM PROPERTIES === 
   CSS: Variabili per consistenza design system
*/
:root {
    --product-image-size: 40px;
    --product-image-border: 2px solid #e9ecef;
    --badge-font-size: 0.6rem;
    --transition-duration: 0.3s;
}

/* === UTILITY CLASSES === 
   CSS: Classi helper per uso comune
*/
.fw-medium {
    font-weight: 500;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.rounded {
    border-radius: 0.375rem !important;
}

/* === PERFORMANCE OPTIMIZATIONS === 
   CSS: Ottimizzazioni rendering per elementi animati
*/
.product-thumb,
.bg-gradient {
    will-change: transform; /* Hint per GPU acceleration */
}

/* === SCROLLBAR PERSONALIZZATA === 
   CSS: Styling scrollbar per tabelle responsive (Webkit)
*/
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === STACKING CONTEXT === 
   CSS: Gestione z-index per layering corretto
*/
.position-absolute {
    z-index: 10;
}

.tooltip {
    z-index: 1070;
}

.dropdown-menu {
    z-index: 1000;
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT
    Blade PHP: @push('scripts') aggiunge JavaScript al layout prima di </body>
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE JQUERY DOCUMENT READY
    jQuery: $(document).ready() per esecuzione dopo DOM loaded
*/
$(document).ready(function() {
    console.log('📋 Storico Interventi inizializzato');
    
    /*
        === INIZIALIZZAZIONE TOOLTIP ===
        Bootstrap: Attivazione tooltip per tutti gli elementi con attributo data-bs-toggle
    */
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
        console.log('✅ Tooltip inizializzati');
    }
    
    /*
        === GESTIONE IMMAGINI PRODOTTO ===
        JavaScript: Event handler per errori caricamento immagini
        UX: Fallback automatico a placeholder quando immagine non carica
    */
    $('.product-thumb').on('error', function() {
        console.log('⚠️ Errore caricamento immagine:', $(this).attr('src'));
        $(this).hide(); // Nascondi immagine rotta
        $(this).next('.d-none').removeClass('d-none').addClass('d-flex'); // Mostra fallback
    });
    
    /*
        === CLICK SU IMMAGINI PER ZOOM ===
        JavaScript: Funzionalità opzionale per visualizzazione ingrandita
        UX: Modal con immagine full-size per dettagli migliori
    */
    $('.product-thumb').on('click', function(e) {
        e.stopPropagation(); // Previeni bubble su tr
        const imgSrc = $(this).attr('src');
        const productName = $(this).attr('alt');
        
        // Modal semplice per zoom immagine
        if (imgSrc && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            showImageModal(imgSrc, productName);
        }
    });
    
    /*
        === GESTIONE FORM FILTRI ===
        JavaScript: Event listeners per campi filtro
        NOTA: Auto-submit commentato per evitare troppe richieste
    */
    $('#search, #periodo, #gravita, #categoria').on('change', function() {
        // Auto-submit opzionale (commentato per evitare troppe richieste)
        // $(this).closest('form').submit();
    });
    
    /*
        === GESTIONE PAGINAZIONE ===
        JavaScript: Loading state per link paginazione
        UX: Feedback visivo durante navigazione tra pagine
    */
    $('.pagination .page-link').on('click', function(e) {
        const $link = $(this);
        if (!$link.parent().hasClass('disabled') && !$link.parent().hasClass('active')) {
            $link.html('<span class="spinner-border spinner-border-sm"></span>');
        }
    });
    
    /*
        === ANIMAZIONI CONTATORI ===
        JavaScript: Animazione numerica per statistiche KPI
        jQuery: animate() per transizione smooth dei numeri
    */
    function animateCounters() {
        $('.card h4').each(function() {
            const $counter = $(this);
            const target = parseInt($counter.text()) || 0;
            
            // Anima solo numeri ragionevoli per performance
            if (target > 0 && target <= 100) {
                $counter.text('0');
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1000,
                    step: function() {
                        $counter.text(Math.ceil(this.counter));
                    }
                });
            }
        });
    }
    
    // Avvia animazioni dopo un breve delay per permettere rendering DOM
    setTimeout(animateCounters, 500);
    
    console.log('✅ Storico Interventi JavaScript caricato');
});

/*
    === FUNZIONE MODAL IMMAGINE ===
    JavaScript: Creazione dinamica modal Bootstrap per zoom immagini
    Bootstrap: Modal component per overlay fullscreen
*/
function showImageModal(imgSrc, productName) {
    /*
        TEMPLATE HTML MODAL
        Bootstrap: Modal structure con header, body e close button
    */
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${productName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imgSrc}" alt="${productName}" class="img-fluid rounded" style="max-height: 400px;">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    /*
        GESTIONE MODAL DINAMICO
        jQuery: Rimozione modal esistente e creazione nuovo per evitare conflitti
    */
    $('#imageModal').remove(); // Cleanup modal precedente
    $('body').append(modalHtml); // Append nuovo modal
    
    /*
        INIZIALIZZAZIONE E SHOW MODAL
        Bootstrap: Creazione istanza Modal e visualizzazione
    */
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
    
    /*
        CLEANUP AL CLOSE
        JavaScript: Event listener per pulizia DOM quando modal si chiude
    */
    $('#imageModal').on('hidden.bs.modal', function() {
        $(this).remove(); // Rimuovi dal DOM
    });
}

/*
    === UTILITY FUNCTIONS ===
    JavaScript: Funzioni helper per notifiche e feedback
*/

/*
    FUNZIONE TOAST NOTIFICATION
    Bootstrap: Sistema toast per messaggi temporanei
    Parametri: message (string), type (Bootstrap color variant)
*/
function showToast(message, type = 'info') {
    /*
        TEMPLATE TOAST BOOTSTRAP
        Bootstrap: Toast structure con auto-dismiss
    */
    const toastHtml = `
        <div class="toast align-items-center text-bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    /*
        CREAZIONE CONTAINER TOAST SE NON ESISTE
        HTML: Container posizionato fisso per toast notifications
    */
    if (!$('#toast-container').length) {
        $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    /*
        CREAZIONE E VISUALIZZAZIONE TOAST
        jQuery: Append toast e inizializzazione Bootstrap Toast
    */
    const $toast = $(toastHtml);
    $('#toast-container').append($toast);
    
    const toast = new bootstrap.Toast($toast[0]);
    toast.show();
    
    /*
        AUTO-CLEANUP TOAST
        JavaScript: Rimozione automatica dal DOM dopo dismiss
    */
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

/*
    === ESPORTAZIONE PER DEBUG ===
    JavaScript: Oggetto globale con funzioni utili per debugging
    Window object: Accessibile da console browser per testing
*/
window.storicoInterventi = {
    showImageModal: showImageModal,
    showToast: showToast,
    animateCounters: function() {
        $('.card h4').each(function() {
            const $counter = $(this);
            const target = parseInt($counter.text()) || 0;
            
            if (target > 0 && target <= 100) {
                $counter.text('0');
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1000,
                    step: function() {
                        $counter.text(Math.ceil(this.counter));
                    }
                });
            }
        });
    }
};

/*
    === INIZIALIZZAZIONE DATI PAGINA ===
    JavaScript: Pattern standard per condivisione dati PHP->JavaScript
    Namespace globale per evitare conflitti variabili
*/
window.PageData = window.PageData || {};

/*
    SEZIONE DATI CONDIZIONALI
    Blade PHP: Controlli @if(isset()) per passaggio sicuro dati
    Laravel: @json() per serializzazione sicura PHP->JavaScript
*/

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
/*
    DATI PRODOTTO SINGOLO
    Laravel: Model Eloquent -> JSON Object per JavaScript
*/
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
/*
    COLLEZIONE PRODOTTI
    Laravel: Collection -> JSON Array
*/
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
/*
    DATI MALFUNZIONAMENTO SINGOLO
    Laravel: Model -> JSON per elaborazione client-side
*/
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
/*
    COLLEZIONE MALFUNZIONAMENTI
    Laravel: Collection paginata -> JSON
*/
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
/*
    DATI CENTRO ASSISTENZA
    Laravel: Model con relazioni -> JSON
*/
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
/*
    COLLEZIONE CENTRI ASSISTENZA
    Laravel: Collection -> JSON Array per select/mapping
*/
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
/*
    CATEGORIE PRODOTTI
    Laravel: Array -> JSON per popolamento dinamico select
*/
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
/*
    MEMBRI STAFF
    Laravel: Collection staff -> JSON per dashboard/assegnazioni
*/
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
/*
    STATISTICHE SISTEMA
    Laravel: Array statistiche -> JSON per grafici/dashboard
*/
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
/*
    DATI UTENTE CORRENTE
    Laravel: User model autenticato -> JSON per personalizzazione UI
*/
window.PageData.user = @json($user);
@endif

@if(isset($interventi))
/*
    METADATI INTERVENTI PAGINATI
    Laravel: Informazioni paginazione per JavaScript navigation
*/
window.PageData.interventi = {
    total: {{ $interventi->total() ?? 0 }},
    currentPage: {{ $interventi->currentPage() ?? 1 }},
    hasMorePages: {{ $interventi->hasMorePages() ? 'true' : 'false' }}
};
@endif
</script>
@endpush