{{-- 
    ===================================================================
    VISTA ELENCO CENTRI ASSISTENZA - PARTE 1 - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/centri/index.blade.php
    
    DESCRIZIONE:
    Vista per visualizzazione elenco centri di assistenza tecnica distribuiti sul territorio.
    Accessibile pubblicamente (Livello 1) ma con informazioni complete per utenti autenticati.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Visualizzazione centri con filtri geografici (provincia/città)
    - Ricerca testuale per nome centro, città, indirizzo
    - Informazioni contatto complete per ogni centro
    - Layout responsive con card grid system
    - Elenco tecnici specializzati per centro
    - Statistiche distribuzione geografica
    - Sistema paginazione con mantenimento filtri
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
    Importante per SEO e identificazione pagina nei risultati ricerca
--}}
@section('title', 'Centri di Assistenza')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto inserito nel layout
--}}
@section('content')
{{-- 
    CONTAINER BOOTSTRAP STANDARD
    Bootstrap: container con responsive breakpoints per layout centrato
--}}
<div class="container mt-4">
    
    {{-- === HEADER DELLA PAGINA === 
        HTML: Sezione intestazione con titolo principale e descrizione
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                TITOLO PRINCIPALE CON ICONA
                HTML: h1 con classe h2 per dimensioni ottimizzate
                Bootstrap Icons: bi-geo-alt per tema geografico/posizione
            --}}
            <h1 class="h2 mb-3">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                Centri di Assistenza Tecnica
            </h1>
            {{-- 
                SOTTOTITOLO DESCRITTIVO
                Bootstrap: lead class per testo introduttivo prominente
            --}}
            <p class="lead text-muted">
                Trova il centro di assistenza più vicino a te per supporto tecnico professionale
            </p>
        </div>
    </div>

    {{-- === SEZIONE FILTRI E RICERCA === 
        HTML: Form completo per filtri geografici e ricerca testuale
        UX: Permette agli utenti di trovare centri specifici rapidamente
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    {{-- 
                        FORM RICERCA CON METHOD GET
                        HTML: GET method per URL condivisibili e SEO friendly
                        Laravel: route('centri.index') per self-submit del form
                        Bootstrap: row g-3 per grid con gap uniforme
                    --}}
                    <form method="GET" action="{{ route('centri.index') }}" class="row g-3" id="searchForm">
                        
                        {{-- 
                            CAMPO RICERCA GENERALE
                            HTML: Input text per ricerca full-text
                            UX: Placeholder descrittivo per guidare utente
                        --}}
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            {{-- 
                                INPUT RICERCA CON VALORE PERSISTENTE
                                Laravel: request('search') mantiene valore dopo submit
                                HTML: placeholder per indicazioni uso
                            --}}
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome centro, città, indirizzo...">
                        </div>
                        
                        {{-- 
                            FILTRO PER PROVINCIA
                            HTML: Select con opzioni province italiane principali
                        --}}
                        <div class="col-md-3">
                            <label for="provincia" class="form-label fw-semibold">
                                <i class="bi bi-map me-1"></i>Provincia
                            </label>
                            <select name="provincia" id="provincia" class="form-select">
                                <option value="">Tutte le province</option>
                                {{-- 
                                    ARRAY PROVINCE ITALIANE HARDCODED
                                    PHP: Array associativo sigla=>nome per province principali
                                    NOTA: In produzione dovrebbe venire dal database o config
                                --}}
                                @php
                                    $province_italiane = [
                                        'AN' => 'Ancona', 'RM' => 'Roma', 'MI' => 'Milano', 'NA' => 'Napoli', 
                                        'TO' => 'Torino', 'FI' => 'Firenze', 'BA' => 'Bari', 'PA' => 'Palermo',
                                        'GE' => 'Genova', 'BO' => 'Bologna', 'VE' => 'Venezia', 'CT' => 'Catania'
                                    ];
                                @endphp
                                {{-- 
                                    ITERAZIONE ATTRAVERSO PROVINCE
                                    Blade PHP: @foreach per generare options dinamicamente
                                    Laravel: request('provincia') per mantenere selezione
                                --}}
                                @foreach($province_italiane as $sigla => $nome)
                                    <option value="{{ $sigla }}" {{ request('provincia') == $sigla ? 'selected' : '' }}>
                                        {{ $sigla }} - {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 
                            FILTRO PER CITTÀ
                            HTML: Input text per nome città specifico
                        --}}
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
                        
                        {{-- 
                            PULSANTI DI AZIONE FORM
                            Bootstrap: d-grid per pulsanti full-width
                        --}}
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-1">
                                {{-- 
                                    PULSANTE SUBMIT RICERCA
                                    HTML: type="submit" per invio form
                                --}}
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                {{-- 
                                    PULSANTE RESET CONDIZIONALE
                                    Laravel: request()->hasAny() verifica filtri attivi
                                    Mostra reset solo se necessario per UX pulita
                                --}}
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

    {{-- === STATISTICHE RAPIDE === 
        Laravel: Sezione condizionale per KPI se disponibili dal controller
        UX: Feedback immediato sui risultati e filtri applicati
    --}}
    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-12">
                {{-- 
                    LAYOUT FLEX PER BADGE STATISTICHE
                    Bootstrap: d-flex flex-wrap per disposizione responsive
                --}}
                <div class="d-flex flex-wrap gap-3">
                    {{-- 
                        BADGE NUMERO TOTALE CENTRI
                        Laravel: $stats array associativo dal controller
                        PHP: ?? 0 per fallback se chiave non esiste
                    --}}
                    <div class="badge bg-primary fs-6 py-2 px-3">
                        <i class="bi bi-geo-alt me-1"></i>{{ $stats['totale_centri'] ?? 0 }} centri totali
                    </div>
                    {{-- 
                        BADGE CENTRI CON TECNICI DISPONIBILI
                        Metrica importante per utenti che cercano supporto attivo
                    --}}
                    <div class="badge bg-success fs-6 py-2 px-3">
                        <i class="bi bi-people me-1"></i>{{ $stats['centri_con_tecnici'] ?? 0 }} con tecnici disponibili
                    </div>
                    {{-- 
                        BADGE FILTRO PROVINCIA ATTIVO
                        Laravel: Mostra solo se filtro provincia è applicato
                        UX: Conferma visiva del filtro attivo
                    --}}
                    @if(request('provincia'))
                        <div class="badge bg-info fs-6 py-2 px-3">
                            <i class="bi bi-filter me-1"></i>Provincia: {{ request('provincia') }}
                        </div>
                    @endif
                    {{-- 
                        BADGE CONTEGGIO PROVINCIA SPECIFICA
                        Laravel: Controlli nested per dati statistici strutturati
                        Mostra numero centri nella provincia filtrata
                    --}}
                    @if(isset($stats['per_provincia']) && request('provincia') && isset($stats['per_provincia'][request('provincia')]))
                        <div class="badge bg-warning fs-6 py-2 px-3">
                            <i class="bi bi-building me-1"></i>{{ $stats['per_provincia'][request('provincia')] }} nella provincia
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- === SEZIONE RISULTATI === 
        HTML: Container principale per visualizzazione centri trovati
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                CONTROLLO ESISTENZA E CONTEGGIO CENTRI
                Laravel: isset() + count() per verifica collection valida e non vuota
                $centri è LengthAwarePaginator (collection paginata) dal controller
            --}}
            @if(isset($centri) && $centri->count() > 0)
                {{-- 
                    HEADER RISULTATI CON CONTEGGIO
                    Laravel: total() per numero totale elementi (tutte le pagine)
                    UX: Informazioni chiare su risultati trovati
                --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Centri Trovati 
                        <span class="badge bg-secondary">{{ $centri->total() }}</span>
                    </h4>
                    
                    {{-- 
                        INFORMAZIONI PAGINAZIONE
                        Laravel: Metodi pagination per info navigazione
                    --}}
                    <div class="d-flex align-items-center gap-3">
                        @if($centri->hasPages())
                            <small class="text-muted">
                                Pagina {{ $centri->currentPage() }} di {{ $centri->lastPage() }}
                            </small>
                        @endif
                    </div>
                </div>
                {{-- === GRIGLIA CENTRI DI ASSISTENZA === 
                    Bootstrap: Grid system responsive per layout card organizzato
                --}}
                <div class="row g-4">
                    {{-- 
                        ITERAZIONE ATTRAVERSO CENTRI PAGINATI
                        Laravel: @foreach attraverso collection Eloquent paginata
                        $centro è model con relazioni caricate (eager loading per tecnici)
                    --}}
                    @foreach($centri as $centro)
                        {{-- 
                            COLONNA RESPONSIVE PER CARD CENTRO
                            Bootstrap: col-md-6 col-lg-4 per layout adattivo
                            Mobile: 1 colonna, Tablet: 2 colonne, Desktop: 3 colonne
                        --}}
                        <div class="col-md-6 col-lg-4">
                            {{-- 
                                CARD CENTRO CON ALTEZZA UNIFORME
                                Bootstrap: h-100 per altezza uniforme in griglia
                                CSS: card-custom per styling personalizzato
                            --}}
                            <div class="card card-custom h-100">
                                <div class="card-body">
                                    
                                    {{-- 
                                        HEADER CENTRO CON NOME E BADGE
                                        HTML: Layout flex per allineamento ottimale
                                    --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            {{-- 
                                                NOME CENTRO PRINCIPALE
                                                HTML: h5 con icona edificio per tema
                                                Laravel: $centro->nome dal model Eloquent
                                            --}}
                                            <h5 class="card-title mb-1">
                                                <i class="bi bi-building text-primary me-2"></i>
                                                {{ $centro->nome }}
                                            </h5>
                                            <div class="mb-2">
                                                {{-- 
                                                    BADGE PROVINCIA E CITTÀ
                                                    Bootstrap: badge bg-primary per info geografiche
                                                    Laravel: Accesso diretto attributi model
                                                --}}
                                                <span class="badge bg-primary">
                                                    {{ $centro->provincia }} - {{ $centro->citta }}
                                                </span>
                                                
                                                {{-- 
                                                    BADGE NUMERO TECNICI CONDIZIONALE
                                                    Laravel: Relazione $centro->tecnici con count()
                                                    UX: Colori diversi per stato presenza tecnici
                                                --}}
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

                                    {{-- === INFORMAZIONI DI CONTATTO === 
                                        HTML: Sezione strutturata per dati contatto
                                    --}}
                                    <div class="mb-3">
                                        {{-- 
                                            INDIRIZZO COMPLETO
                                            HTML: Layout flex con icona geografica
                                            UX: Struttura indirizzo su più righe per leggibilità
                                        --}}
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                            <div>
                                                {{-- 
                                                    INDIRIZZO PRINCIPALE
                                                    Laravel: $centro->indirizzo dal database
                                                --}}
                                                <div class="fw-medium">{{ $centro->indirizzo }}</div>
                                                {{-- 
                                                    CAP, CITTÀ E PROVINCIA
                                                    HTML: Formattazione standard indirizzo italiano
                                                --}}
                                                <div class="text-muted">{{ $centro->cap }} {{ $centro->citta }} ({{ $centro->provincia }})</div>
                                            </div>
                                        </div>

                                        {{-- 
                                            TELEFONO CONDIZIONALE
                                            Laravel: @if($centro->telefono) verifica esistenza campo
                                        --}}
                                        @if($centro->telefono)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                {{-- 
                                                    LINK TELEFONO CLICCABILE
                                                    HTML: href="tel:" per apertura app telefono
                                                    Laravel: telefono_formattato accessor o fallback
                                                --}}
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono_formattato ?? $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif

                                        {{-- 
                                            EMAIL CONDIZIONALE
                                            Laravel: Controllo esistenza campo email
                                        --}}
                                        @if($centro->email)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                {{-- 
                                                    LINK EMAIL CLICCABILE
                                                    HTML: href="mailto:" per apertura client email
                                                --}}
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{ $centro->email }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- === ELENCO TECNICI DEL CENTRO === 
                                        Laravel: Sezione condizionale per relazione tecnici
                                    --}}
                                    @if($centro->tecnici && $centro->tecnici->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2">
                                                <i class="bi bi-people me-1"></i>
                                                Tecnici Specializzati
                                            </h6>
                                            {{-- 
                                                PRIMI 3 TECNICI PER SPAZIO
                                                Laravel: take(3) limita risultati per layout compatto
                                                UX: Evita overflow della card
                                            --}}
                                            @foreach($centro->tecnici->take(3) as $tecnico)
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="flex-grow-1">
                                                        {{-- 
                                                            NOME COMPLETO TECNICO
                                                            Laravel: nome_completo accessor o concatenazione
                                                        --}}
                                                        <small class="fw-medium">{{ $tecnico->nome_completo ?? $tecnico->nome . ' ' . $tecnico->cognome }}</small>
                                                        {{-- 
                                                            SPECIALIZZAZIONE CONDIZIONALE
                                                            Laravel: Campo opzionale specializzazione
                                                        --}}
                                                        @if($tecnico->specializzazione)
                                                            <br>
                                                            <small class="text-muted">{{ $tecnico->specializzazione }}</small>
                                                        @endif
                                                    </div>
                                                    {{-- 
                                                        INDICATORE STATO ATTIVO
                                                        Laravel: method_exists() verifica metodo model
                                                        UX: Icone diverse per stato attività recente
                                                    --}}
                                                    <div class="ms-2">
                                                        @if(method_exists($tecnico, 'isRecentlyActive') && $tecnico->isRecentlyActive())
                                                            <i class="bi bi-circle-fill text-success" title="Attivo di recente"></i>
                                                        @else
                                                            <i class="bi bi-circle text-success" title="Tecnico disponibile"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            {{-- 
                                                CONTEGGIO TECNICI AGGIUNTIVI
                                                Laravel: count() per calcolo overflow
                                                UX: Indica presenza tecnici aggiuntivi
                                            --}}
                                            @if($centro->tecnici->count() > 3)
                                                <small class="text-muted">
                                                    E altri {{ $centro->tecnici->count() - 3 }} tecnici...
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        {{-- 
                                            MESSAGGIO NESSUN TECNICO
                                            UX: Feedback quando centro non ha tecnici assegnati
                                        --}}
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                Nessun tecnico assegnato al momento
                                            </small>
                                        </div>
                                    @endif

                                </div>

                                {{-- === FOOTER CARD CON PULSANTI AZIONE === 
                                    Bootstrap: card-footer per sezione azioni separate
                                --}}
                                <div class="card-footer bg-light">
                                    <div class="d-flex gap-2">
                                        {{-- 
                                            PULSANTE CHIAMATA TELEFONICA
                                            HTML: href="tel:" per integrazione nativa mobile
                                            Bootstrap: flex-fill per distribuzione spazio uniforme
                                        --}}
                                        @if($centro->telefono)
                                            <a href="tel:{{ $centro->telefono }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-telephone me-1"></i>Chiama
                                            </a>
                                        @endif
                                        
                                        {{-- 
                                            PULSANTE INVIO EMAIL
                                            HTML: href="mailto:" per client email
                                        --}}
                                        @if($centro->email)
                                            <a href="mailto:{{ $centro->email }}" class="btn btn-outline-info btn-sm flex-fill">
                                                <i class="bi bi-envelope me-1"></i>Email
                                            </a>
                                        @endif
                                        
                                        {{-- 
                                            PULSANTE DETTAGLI CENTRO
                                            Laravel: route('centri.show', $centro) per vista dettaglio
                                            Route model binding: $centro passato automaticamente
                                        --}}
                                        <a href="{{ route('centri.show', $centro) }}" class="btn btn-primary btn-sm flex-fill">
                                            <i class="bi bi-eye me-1"></i>Dettagli
                                        </a>
                                        
                                        {{-- 
                                            PULSANTE GOOGLE MAPS CONDIZIONALE
                                            Laravel: method_exists() verifica metodo accessor
                                            HTML: target="_blank" per apertura nuova finestra
                                        --}}
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

                {{-- === PAGINAZIONE === 
                    Laravel: Sistema paginazione integrato con mantenimento filtri
                --}}
                <div class="row mt-4">
                    <div class="col-12">
                        {{-- 
                            LINK PAGINAZIONE CON QUERY STRING
                            Laravel: withQueryString() preserva parametri GET attraverso pagine
                            UX: Filtri rimangono attivi durante navigazione
                        --}}
                        {{ $centri->withQueryString()->links() }}
                    </div>
                </div>

            @else
                {{-- === MESSAGGIO QUANDO NON CI SONO CENTRI === 
                    UX: Stato vuoto con messaggi contestuali e azioni suggerite
                --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        {{-- Icona grande per stato vuoto --}}
                        <i class="bi bi-search display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">Nessun centro di assistenza trovato</h4>
                    <p class="text-muted mb-4">
                        {{-- 
                            MESSAGGIO DINAMICO BASATO SU FILTRI
                            Laravel: request()->hasAny() per rilevare filtri attivi
                            UX: Messaggi diversi per stato filtrato vs. vuoto
                        --}}
                        @if(request()->hasAny(['search', 'provincia', 'citta']))
                            Prova a modificare i filtri di ricerca o 
                            <a href="{{ route('centri.index') }}" class="text-decoration-none">visualizza tutti i centri</a>
                        @else
                            Non ci sono ancora centri di assistenza disponibili.
                        @endif
                    </p>
                    
                    {{-- 
                        PULSANTE ADMIN PER AGGIUNGERE CENTRO
                        Laravel: auth()->check() + isAdmin() per controllo autorizzazioni
                        Funzionalità: Solo admin può creare nuovi centri
                    --}}
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Centro
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    {{-- === SEZIONE DISTRIBUZIONE PER PROVINCIA === 
        Laravel: Widget condizionale per statistiche geografiche aggregate
        UX: Navigazione rapida per provincia con conteggi
    --}}
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
                        {{-- 
                            ITERAZIONE STATISTICHE PER PROVINCIA
                            Laravel: @foreach attraverso array associativo provincia=>conteggio
                            UX: Grid di pulsanti per navigazione filtrata rapida
                        --}}
                        @foreach($stats['per_provincia'] as $prov => $count)
                            <div class="col-md-3 col-sm-4 col-6">
                                {{-- 
                                    PULSANTE FILTRO PROVINCIA
                                    Laravel: route() con parametro per filtro automatico
                                    Bootstrap: Layout flex per allineamento prov-conteggio
                                --}}
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

    {{-- === SEZIONE INFORMAZIONI AGGIUNTIVE === 
        HTML: Card informativa per guidare utenti sui servizi disponibili
    --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
    {{-- === SEZIONE INFORMAZIONI AGGIUNTIVE === 
        HTML: Card informativa per guidare utenti sui servizi disponibili
    --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sui Centri di Assistenza
                    </h5>
                    <div class="row">
                        {{-- 
                            COLONNA SERVIZI OFFERTI
                            HTML: Lista non ordinata con icone Bootstrap per checklist
                        --}}
                        <div class="col-md-6">
                            <h6>Servizi Offerti</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i>Assistenza tecnica specializzata</li>
                                <li><i class="bi bi-check text-success me-2"></i>Riparazione e manutenzione prodotti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Consulenza tecnica professionale</li>
                                <li><i class="bi bi-check text-success me-2"></i>Supporto post-vendita</li>
                            </ul>
                        </div>
                        {{-- 
                            COLONNA ISTRUZIONI CONTATTO
                            HTML: Guida utente per utilizzo funzionalità contatto
                        --}}
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
{{-- FINE CONTENUTO --}}
@endsection

{{-- === SEZIONE JAVASCRIPT === 
    Blade PHP: @push('scripts') aggiunge JavaScript al layout prima di </body>
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA
    JavaScript: Pattern namespace globale per condivisione dati PHP->JavaScript
    Evita conflitti variabili e organizza dati per modularità
*/
window.PageData = window.PageData || {};

/*
    SEZIONE DATI CONDIZIONALI
    Blade PHP: Controlli @if(isset()) per passaggio sicuro dati
    Laravel: @json() per serializzazione sicura PHP->JavaScript
    NOTA: Sistema standardizzato per compatibilità tra tutte le viste
*/

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
/*
    DATI PRODOTTO SINGOLO
    Laravel: Model Eloquent -> JSON Object
    Uso: Dettagli prodotto per JavaScript interactions
*/
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
/*
    COLLEZIONE PRODOTTI
    Laravel: Collection -> JSON Array
    Uso: Popolamento select, autocomplete, filtri dinamici
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
    Laravel: Collection -> JSON per dashboard/statistiche
*/
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
/*
    DATI CENTRO SINGOLO
    Laravel: Model Centro con relazioni -> JSON
    Uso: Vista dettaglio, modifica, integrazione mappe
*/
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
/*
    COLLEZIONE CENTRI CORRENTE
    Laravel: Collection paginata -> JSON
    Uso: Interazioni JavaScript su lista centri visualizzata
*/
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
/*
    CATEGORIE PRODOTTI
    Laravel: Array -> JSON per filtri dinamici
*/
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
/*
    MEMBRI STAFF
    Laravel: Collection staff -> JSON per assegnazioni
*/
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
/*
    STATISTICHE CENTRI
    Laravel: Array statistiche -> JSON per grafici/analytics
    Uso: Dashboard dinamica, conteggi in tempo reale
*/
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
/*
    DATI UTENTE AUTENTICATO
    Laravel: User model -> JSON per personalizzazione UI
*/
window.PageData.user = @json($user);
@endif

/*
    PATTERN ESTENSIBILE
    JavaScript: Questa struttura permette aggiunta facile nuovi dati
    senza modificare architettura JavaScript esistente
*/
</script>
@endpush

{{-- === SEZIONE CSS PERSONALIZZATO === 
    Blade PHP: @push('styles') aggiunge CSS al layout nell'head
--}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI PER CARD === 
   CSS: Sistema design coerente per componenti card
*/

/*
    CARD PERSONALIZZATE CON OMBRA
    CSS: Box-shadow sottile per profondità visiva
    Transizione smooth per effetti hover
*/
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Ombra sottile default */
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: box-shadow 0.2s ease-in-out; /* Transizione smooth per effetti hover */
}

/*
    EFFETTO HOVER PER CARD
    CSS: Feedback visivo su interazione utente
    Box-shadow più pronunciata per enfasi
*/
.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra più pronunciata al hover */
}

/* === STILI PER BADGE === 
   CSS: Ottimizzazione dimensioni badge per leggibilità
*/
.badge.fs-6 {
    font-size: 0.875rem !important; /* Override Bootstrap per dimensioni ottimali */
}

/* === RESPONSIVITÀ MOBILE === 
   CSS: Media queries per ottimizzazione dispositivi mobili
*/
@media (max-width: 768px) {
    /*
        BADGE RESPONSIVE
        CSS: Layout verticale badge su schermi piccoli
    */
    .badge {
        margin-bottom: 0.25rem;
        display: inline-block; /* Permette wrap naturale */
    }
    
    /*
        FOOTER CARD MOBILE
        CSS: Pulsanti in colonna su mobile per usabilità touch
    */
    .card-footer .d-flex {
        flex-direction: column; /* Stack verticale su mobile */
        gap: 0.5rem !important;
    }
    
    /*
        PULSANTI FULL-WIDTH SU MOBILE
        CSS: Massimizza area touch per accessibilità mobile
    */
    .card-footer .btn {
        flex: 1 !important;
    }

    /*
        RIDUZIONE PADDING CONTAINER
        CSS: Ottimizzazione spazio su schermi piccoli
    */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* === STILI SPECIALI PER DISTRIBUZIONE PROVINCE === 
   CSS: Effetti hover specifici per pulsanti provincia
*/

/*
    CAMBIO COLORE BADGE AL HOVER
    CSS: Inversione colori badge su hover pulsante
    UX: Feedback visivo per elemento interattivo nested
*/
.btn-outline-primary:hover .badge {
    background-color: white !important;
    color: var(--bs-primary) !important;
}

/* === STILI PER SEZIONE INFORMAZIONI === 
   CSS: Personalizzazione card informativa
*/

/*
    TITOLO CARD INFORMAZIONI
    CSS: Colore coerente con design system
*/
.bg-light .card-title {
    color: var(--bs-primary);
}

/* === ANIMAZIONI E TRANSIZIONI === 
   CSS: Sistema animazioni per migliorare UX
*/

/*
    TRANSIZIONE SMOOTH PER LINK
    CSS: Feedback uniforme su tutti i link
*/
a {
    transition: color 0.2s ease-in-out;
}

/*
    EFFETTO PULSE PER INDICATORI ATTIVI
    CSS: Animazione pulse per tecnici recentemente attivi
    Keyframe animation per attirare attenzione su stato importante
*/
.bi-circle-fill.text-success {
    animation: pulse 2s infinite;
}

/*
    KEYFRAMES PULSE ANIMATION
    CSS: Definizione animazione pulsante per indicatori stato
*/
@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5; /* Fade a metà opacità */
    }
    100% {
        opacity: 1; /* Ritorno opacità piena */
    }
}

/* === STILI PER FORM DI RICERCA === 
   CSS: Miglioramenti UX per form filtri
*/

/*
    ALLINEAMENTO LABEL CONSISTENTE
    CSS: Spacing uniforme per elementi form
*/
.form-label {
    margin-bottom: 0.5rem;
}

/*
    STILE FOCUS COERENTE
    CSS: Feedback visivo focus per accessibilità
    Border e box-shadow coordinati con design system
*/
.form-control:focus,
.form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

/* === STILI PER MESSAGGI DI STATO === 
   CSS: Styling per stati vuoti e feedback
*/

/*
    ICONA GRANDE STATO VUOTO
    CSS: Dimensioni e opacità per messaggio "nessun risultato"
*/
.display-1 {
    font-size: 4rem;
    opacity: 0.6; /* Trasparenza per aspetto subdued */
}

/* === OTTIMIZZAZIONI PERFORMANCE === 
   CSS: Will-change per elementi animati
*/
.card-custom {
    will-change: box-shadow; /* Hint per GPU acceleration su hover */
}

/* === ACCESSIBILITÀ === 
   CSS: Miglioramenti per screen reader e navigazione tastiera
*/

/*
    FOCUS VISIBILE
    CSS: Outline personalizzato per focus da tastiera
*/
.btn:focus-visible,
.card:focus-within {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

/* === PRINT STYLES === 
   CSS: Ottimizzazioni per stampa
*/
@media print {
    /*
        NASCONDERE ELEMENTI INTERATTIVI
        CSS: Rimuovi pulsanti e link per stampa pulita
    */
    .btn,
    .card-footer {
        display: none !important;
    }
    
    /*
        BORDI CARD PER STAMPA
        CSS: Bordi visibili per definizione sezioni
    */
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid; /* Evita rottura card tra pagine */
    }
    
    /*
        COLORI STAMPA
        CSS: Preserva colori essenziali per stampa
    */
    .badge {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}

/* === DARK MODE SUPPORT === 
   CSS: Supporto automatico tema scuro
*/
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #2d3748;
        color: #fff;
        border-color: #4a5568;
    }
    
    .bg-light {
        background-color: #1a202c !important;
    }
}

/* === HIGH CONTRAST SUPPORT === 
   CSS: Accessibilità per utenti con necessità alto contrasto
*/
@media (prefers-contrast: high) {
    .card-custom {
        border-width: 2px !important;
    }
    
    .badge {
        border: 1px solid;
    }
}

/* === REDUCED MOTION SUPPORT === 
   CSS: Accessibilità per utenti sensibili alle animazioni
*/
@media (prefers-reduced-motion: reduce) {
    .card-custom,
    a,
    .bi-circle-fill,
    * {
        transition: none !important;
        animation: none !important;
    }
}

/* === CUSTOM PROPERTIES === 
   CSS: Variabili per design system coerente
*/
:root {
    --card-border-radius: 0.375rem;
    --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --transition-duration: 0.2s;
}

/* === UTILITY CLASSES === 
   CSS: Classi helper per uso comune
*/
.fw-medium {
    font-weight: 500;
}

.text-decoration-none:hover {
    text-decoration: underline !important; /* Ripristina underline su hover per accessibilità */
}
</style>
@endpush