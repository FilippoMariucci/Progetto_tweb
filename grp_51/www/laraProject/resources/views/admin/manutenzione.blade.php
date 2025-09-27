{{-- 
    Vista Manutenzione Sistema Admin - Stile Compatto
    File: resources/views/admin/manutenzione.blade.php
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE:
    Vista ottimizzata per amministratori con layout compatto,
    gestione cache, ottimizzazione database e monitoraggio sistema.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Gestione cache (config, route, view, application)
    - Ottimizzazione database
    - Monitoraggio sistema in tempo reale
    - Visualizzazione informazioni sistema
    - Azioni rapide per gestione utenti/prodotti
    - Layout responsive e accessibile
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends indica che questa vista eredita dal layout 'layouts.app'
    Questo significa che il contenuto sarà inserito nella sezione @yield('content') del layout principale
--}}
@extends('layouts.app')

{{-- 
    DEFINIZIONE DEL TITOLO DELLA PAGINA
    Blade PHP: @section('title') definisce il contenuto per la sezione 'title' del layout
    Questo valore viene inserito nel tag <title> della pagina HTML
--}}
@section('title', 'Manutenzione Sistema - Admin')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto principale della pagina
    Tutto il codice fino a @endsection sarà inserito nel layout base
--}}
@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === 
        HTML: Intestazione della pagina con titolo, descrizione e pulsanti di navigazione
        Bootstrap: Utilizza classi di flexbox per layout responsivo
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- Titolo principale con icona Bootstrap Icons --}}
            <h2 class="mb-1">
                <i class="bi bi-tools text-primary me-2"></i>
                Manutenzione Sistema
            </h2>
            {{-- Sottotitolo descrittivo --}}
            <p class="text-muted small mb-0">Gestione cache, database e monitoraggio performance</p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                LINK DI NAVIGAZIONE VERSO DASHBOARD
                Blade PHP: route('admin.dashboard') genera l'URL per la route nominata 'admin.dashboard'
                Questo utilizza il sistema di routing di Laravel per generare URL dinamici
            --}}
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            {{-- 
                PULSANTE AGGIORNA CON JAVASCRIPT
                JavaScript: onclick chiama la funzione aggiornaInfoSistema() definita nel file JS
            --}}
            <button class="btn btn-primary" onclick="aggiornaInfoSistema()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
        </div>
    </div>

    {{-- === MESSAGGI DI FEEDBACK === 
        Blade PHP: Gestione dei messaggi di sessione per feedback all'utente
        Laravel: session() è un helper per accedere ai dati di sessione
    --}}
    {{-- 
        CONTROLLO CONDIZIONALE PER MESSAGGI DI SUCCESSO
        Blade PHP: @if(session('success')) verifica se esiste un messaggio di successo in sessione
        Laravel: Questi messaggi vengono impostati nei controller con redirect()->with('success', 'messaggio')
    --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{-- 
                OUTPUT DEL MESSAGGIO DI SUCCESSO
                Blade PHP: {{ session('success') }} stampa il contenuto escapato del messaggio
                Laravel: L'escape automatico previene attacchi XSS
            --}}
            {{ session('success') }}
            {{-- Pulsante per chiudere l'alert Bootstrap --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 
        GESTIONE ERRORI DI VALIDAZIONE
        Blade PHP: @if($errors->any()) controlla se ci sono errori di validazione
        Laravel: $errors è automaticamente disponibile in tutte le viste e contiene i messaggi di errore
    --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{-- 
                CICLO PER VISUALIZZARE TUTTI GLI ERRORI
                Blade PHP: @foreach itera attraverso tutti gli errori di validazione
                Laravel: $errors->all() restituisce un array di tutti i messaggi di errore
            --}}
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- === INFORMAZIONI SISTEMA COMPATTE === 
        HTML: Griglia Bootstrap per visualizzare le informazioni di sistema
        I dati vengono passati dal controller nella variabile $systemInfo
    --}}
    <div class="row g-2 mb-3">
        {{-- VERSIONE LARAVEL --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-code-square text-primary fs-4"></i>
                    {{-- 
                        VISUALIZZAZIONE VERSIONE LARAVEL
                        Blade PHP: {{ $systemInfo['laravel_version'] ?? 'N/A' }}
                        PHP: L'operatore ?? (null coalescing) restituisce 'N/A' se la chiave non esiste
                    --}}
                    <h6 class="fw-bold mb-0 mt-1">Laravel {{ $systemInfo['laravel_version'] ?? 'N/A' }}</h6>
                    <small class="text-muted">Framework</small>
                </div>
            </div>
        </div>
        
        {{-- VERSIONE PHP --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-server text-success fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1">PHP {{ $systemInfo['php_version'] ?? 'N/A' }}</h6>
                    <small class="text-muted">Server</small>
                </div>
            </div>
        </div>
        
        {{-- VERSIONE DATABASE --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-database text-warning fs-4"></i>
                    <h6 class="fw-bold mb-0 mt-1">{{ $systemInfo['database_version'] ?? 'MySQL' }}</h6>
                    <small class="text-muted">Database</small>
                </div>
            </div>
        </div>
        
        {{-- UTILIZZO MEMORIA --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-memory text-info fs-4"></i>
                    {{-- 
                        CALCOLO MEMORIA IN TEMPO REALE
                        PHP: memory_get_usage(true) ottiene l'uso di memoria in byte
                        PHP: round() arrotonda a 1 decimale, divisione per 1024 converte in MB
                        HTML: id="memory-display" permette aggiornamento via JavaScript
                    --}}
                    <h6 class="fw-bold mb-0 mt-1" id="memory-display">{{ round(memory_get_usage(true) / 1024 / 1024, 1) }}MB</h6>
                    <small class="text-muted">Memoria</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GESTIONE CACHE E SISTEMA === --}}
    <div class="row g-3 mb-3">
        {{-- STATO CACHE - COMPATTO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Stato Cache
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        CONTROLLO ESISTENZA STATO CACHE
                        Blade PHP: @if(isset($cacheStatus)) verifica se la variabile esiste
                        PHP: isset() è una funzione built-in per verificare se una variabile è definita
                    --}}
                    @if(isset($cacheStatus))
                        <div class="row g-1 mb-2">
                            {{-- 
                                ITERAZIONE ATTRAVERSO TIPI DI CACHE
                                Blade PHP: @foreach itera attraverso l'array associativo $cacheStatus
                                PHP: $type => $enabled destruttura chiave e valore dell'array
                            --}}
                            @foreach($cacheStatus as $type => $enabled)
                                <div class="col-6">
                                    <div class="d-flex align-items-center p-2 rounded bg-light">
                                        <div class="me-2">
                                            {{-- 
                                                CONTROLLO CONDIZIONALE STATO CACHE
                                                Blade PHP: @if($enabled) verifica se questo tipo di cache è abilitato
                                                HTML: Mostra icona verde se abilitato, rossa se disabilitato
                                            --}}
                                            @if($enabled)
                                                <i class="bi bi-check-circle text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger"></i>
                                            @endif
                                        </div>
                                        {{-- 
                                            NOME TIPO CACHE FORMATTATO
                                            PHP: ucfirst() capitalizza la prima lettera del tipo di cache
                                        --}}
                                        <small class="fw-semibold">{{ ucfirst($type) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 
                        FORM PER PULIZIA TOTALE CACHE
                        HTML: Form con method POST per invocare l'azione di pulizia
                        Laravel: @csrf genera un token CSRF per proteggere da attacchi cross-site
                    --}}
                    <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}" class="d-grid">
                        @csrf
                        {{-- 
                            INPUT NASCOSTO PER TIPO PULIZIA
                            HTML: Input hidden che specifica il tipo di pulizia da eseguire
                        --}}
                        <input type="hidden" name="type" value="all">
                        {{-- 
                            PULSANTE CON CONFERMA JAVASCRIPT
                            JavaScript: onclick con confirm() mostra dialogo di conferma
                            Il form viene inviato solo se l'utente conferma
                        --}}
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Pulire tutte le cache?')">
                            <i class="bi bi-trash me-1"></i>Pulisci Tutto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- AZIONI CACHE SPECIFICHE --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-gear me-1"></i>
                        Cache Specifiche
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        {{-- 
                            SEZIONE PULSANTI CACHE SPECIFICHE
                            Ogni pulsante invia un form separato per pulire un tipo specifico di cache
                        --}}
                        
                        {{-- CONFIG CACHE --}}
                        <div class="col-6">
                            {{-- 
                                FORM PER PULIZIA CONFIG CACHE
                                Laravel: Pulisce la cache delle configurazioni (config:clear)
                            --}}
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="config">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-gear me-1"></i>Config
                                </button>
                            </form>
                        </div>
                        
                        {{-- ROUTE CACHE --}}
                        <div class="col-6">
                            {{-- 
                                FORM PER PULIZIA ROUTE CACHE
                                Laravel: Pulisce la cache delle rotte (route:clear)
                            --}}
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="route">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-signpost me-1"></i>Route
                                </button>
                            </form>
                        </div>
                        
                        {{-- VIEW CACHE --}}
                        <div class="col-6">
                            {{-- 
                                FORM PER PULIZIA VIEW CACHE
                                Laravel: Pulisce la cache delle viste compilate (view:clear)
                            --}}
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="view">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                            </form>
                        </div>
                        
                        {{-- APPLICATION CACHE --}}
                        <div class="col-6">
                            {{-- 
                                FORM PER PULIZIA APPLICATION CACHE
                                Laravel: Pulisce la cache dell'applicazione (cache:clear)
                            --}}
                            <form method="POST" action="{{ route('admin.manutenzione.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="type" value="application">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-app me-1"></i>App
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MONITORAGGIO SISTEMA --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Stato Sistema
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        AREA STATUS SISTEMA DINAMICA
                        HTML: div con id per aggiornamento JavaScript dinamico
                        Il contenuto viene popolato/aggiornato via AJAX
                    --}}
                    <div id="system-status" class="mb-2">
                        <div class="d-flex justify-content-center">
                            {{-- Spinner Bootstrap durante il caricamento --}}
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <small>Controllo...</small>
                        </div>
                    </div>

                    {{-- 
                        CHECKBOX AUTO-REFRESH
                        HTML: Checkbox per abilitare/disabilitare aggiornamento automatico
                        JavaScript: Viene gestito nel file JS per avviare/fermare timer
                    --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                        <label class="form-check-label small" for="auto-refresh">
                            Auto-refresh (30s)
                        </label>
                    </div>

                    {{-- 
                        PULSANTE CONTROLLO MANUALE
                        HTML: id="manual-check" per binding JavaScript
                        JavaScript: onClick chiamerà funzione per controllo stato sistema
                    --}}
                    <button id="manual-check" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i>Controlla
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- === DATABASE E STORAGE === --}}
    <div class="row g-3 mb-3">
        {{-- MANUTENZIONE DATABASE --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-database me-1"></i>
                        Manutenzione Database
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- Alert informativo sui benefici dell'ottimizzazione --}}
                    <div class="alert alert-info alert-sm py-2 mb-3">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Ottimizzazione DB:</strong> Migliora le performance ottimizzando tutte le tabelle.
                        </small>
                    </div>

                    {{-- 
                        FORM OTTIMIZZAZIONE DATABASE
                        Laravel: Invia richiesta POST per ottimizzare il database
                        MySQL: Esegue OPTIMIZE TABLE su tutte le tabelle
                    --}}
                    <form method="POST" action="{{ route('admin.manutenzione.optimize-db') }}" class="d-grid mb-3">
                        @csrf
                        {{-- 
                            PULSANTE CON CONFERMA JAVASCRIPT
                            JavaScript: confirm() per conferma azione potenzialmente impattante
                        --}}
                        <button type="submit" class="btn btn-info" 
                                onclick="return confirm('Avviare ottimizzazione database?')">
                            <i class="bi bi-lightning-charge me-1"></i>
                            Ottimizza Database
                        </button>
                    </form>

                    {{-- INFO DATABASE COMPATTE --}}
                    <div class="row g-2">
                        <div class="col-6">
                            {{-- STATO CONNESSIONE DATABASE --}}
                            <div class="text-center p-2 bg-light rounded">
                                <small class="fw-semibold">Connessione</small>
                                <div><span class="badge bg-success">Attiva</span></div>
                            </div>
                        </div>
                        <div class="col-6">
                            {{-- 
                                DRIVER DATABASE CONFIGURATO
                                Laravel: config('database.default') legge la configurazione di default
                                Tipicamente 'mysql', 'pgsql', 'sqlite', etc.
                            --}}
                            <div class="text-center p-2 bg-light rounded">
                                <small class="fw-semibold">Driver</small>
                                <div><code class="small">{{ config('database.default') }}</code></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STORAGE E LOG --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-hdd me-1"></i>
                        Storage e Log
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        SEZIONE UTILIZZO STORAGE
                        Blade PHP: Controlli condizionali annidati per gestire dati storage
                    --}}
                    @if(isset($systemInfo['storage_usage']) && is_array($systemInfo['storage_usage']))
                        <div class="mb-3">
                            <small class="fw-semibold">Utilizzo Storage</small>
                            {{-- Gestione errori nell'ottenimento dati storage --}}
                            @if(isset($systemInfo['storage_usage']['error']))
                                <div class="text-danger small">{{ $systemInfo['storage_usage']['error'] }}</div>
                            @else
                                {{-- 
                                    PROGRESS BAR UTILIZZO STORAGE
                                    HTML: Progress bar Bootstrap con codice colore basato su percentuale
                                    PHP: Operatore ?? per valori di default se chiavi non esistono
                                --}}
                                <div class="progress mt-1" style="height: 15px;">
                                    <div class="progress-bar 
                                        @if(($systemInfo['storage_usage']['percentage'] ?? 0) > 80) bg-danger 
                                        @elseif(($systemInfo['storage_usage']['percentage'] ?? 0) > 60) bg-warning 
                                        @else bg-success @endif" 
                                        style="width: {{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%">
                                        <small>{{ $systemInfo['storage_usage']['percentage'] ?? 0 }}%</small>
                                    </div>
                                </div>
                                {{-- Informazioni dettagliate storage --}}
                                <small class="text-muted">
                                    {{ $systemInfo['storage_usage']['used'] ?? 'N/A' }} / 
                                    {{ $systemInfo['storage_usage']['total'] ?? 'N/A' }}
                                </small>
                            @endif
                        </div>
                    @endif

                    {{-- 
                        SEZIONE FILE DI LOG
                        Blade PHP: Controllo esistenza e conteggio file di log
                    --}}
                    @if(isset($systemInfo['log_files']) && count($systemInfo['log_files']) > 0)
                        <div>
                            {{-- 
                                CONTEGGIO FILE LOG
                                PHP: count() per ottenere numero file di log trovati
                            --}}
                            <small class="fw-semibold">File di Log ({{ count($systemInfo['log_files']) }})</small>
                            <div class="row g-1 mt-1">
                                {{-- 
                                    VISUALIZZAZIONE PRIMI 4 FILE LOG
                                    PHP: array_slice() limita a primi 4 elementi per layout compatto
                                    Blade PHP: @foreach per iterare attraverso i file
                                --}}
                                @foreach(array_slice($systemInfo['log_files'], 0, 4) as $logFile)
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            {{-- 
                                                NOME FILE TRONCATO
                                                Laravel: Str::limit() tronca string se supera lunghezza
                                                Migliora la visualizzazione in spazi ristretti
                                            --}}
                                            <small class="fw-semibold d-block">{{ Str::limit($logFile['name'] ?? 'N/A', 15) }}</small>
                                            <small class="text-muted">{{ $logFile['size'] ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === AZIONI RAPIDE === --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 text-center">
                        {{-- 
                            SEZIONE LINK RAPIDI GESTIONE
                            HTML: Grid layout con link alle principali funzioni amministrative
                        --}}

                        {{-- GESTIONE UTENTI --}}
                        <div class="col-md-3">
                            <i class="bi bi-people display-6 text-warning mb-2"></i>
                            <h6 class="fw-semibold">Utenti</h6>
                            <small class="text-muted d-block mb-2">Gestione account</small>
                            {{-- 
                                LINK GESTIONE UTENTI
                                Laravel: route('admin.users.index') genera URL per lista utenti
                            --}}
                            <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-people me-1"></i>Gestisci Utenti
                            </a>
                        </div>

                        {{-- CATALOGO PRODOTTI --}}
                        <div class="col-md-3">
                            <i class="bi bi-box-seam display-6 text-primary mb-2"></i>
                            <h6 class="fw-semibold">Utenti</h6>
                            <small class="text-muted d-block mb-2">Gestione Prodotti</small>
                            {{-- 
                                LINK GESTIONE PRODOTTI
                                Laravel: route('admin.prodotti.index') genera URL per lista prodotti
                            --}}
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-box-seam me-1"></i>Gestisci Prodotti
                            </a>
                        </div>

                        {{-- ASSEGNAZIONI --}}
                        <div class="col-md-3">
                            <i class="bi bi-person-gear display-6 text-primary mb-2"></i>
                            <h6 class="fw-semibold">Assegnazioni</h6>
                            <small class="text-muted d-block mb-2">Prodotti a staff</small>
                            {{-- 
                                LINK GESTIONE ASSEGNAZIONI
                                Laravel: route('admin.assegnazioni.index') per assegnazione prodotti a staff
                            --}}
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-gear me-1"></i>Assegna Prodotto
                            </a>
                        </div>

                        {{-- STATISTICHE --}}
                        <div class="col-md-3">
                            <i class="bi bi-graph-up display-6 text-info mb-2"></i>
                            <h6 class="fw-semibold">Statistiche</h6>
                            <small class="text-muted d-block mb-2">Analytics avanzati</small>
                            {{-- 
                                LINK STATISTICHE
                                Laravel: route('admin.statistiche.index') per visualizzazione analytics
                            --}}
                            <a href="{{ route('admin.statistiche.index') }}" class="btn btn-info btn-sm">
                                <i class="bi bi-graph-up me-1"></i>Statistiche
                            </a>
                        </div>
                    </div>

                    {{-- INFO SICUREZZA --}}
                    <hr class="my-3">
                    <div class="row">
                        {{-- SEZIONE SICUREZZA --}}
                        <div class="col-md-6">
                            <h6 class="small fw-semibold">
                                <i class="bi bi-shield-check me-1 text-success"></i>Sicurezza
                            </h6>
                            {{-- Lista controlli sicurezza attivi --}}
                            <ul class="list-unstyled small mb-0">
                                <li><i class="bi bi-check text-success me-1"></i>Middleware autenticazione attivo</li>
                                <li><i class="bi bi-check text-success me-1"></i>Controlli autorizzazione OK</li>
                                <li><i class="bi bi-check text-success me-1"></i>Log attività tracciati</li>
                            </ul>
                        </div>
                        
                        {{-- SEZIONE BACKUP --}}
                        <div class="col-md-6">
                            <h6 class="small fw-semibold">
                                <i class="bi bi-info-circle me-1 text-info"></i>Backup
                            </h6>
                            {{-- Informazioni stato backup --}}
                            <ul class="list-unstyled small mb-0">
                                <li><i class="bi bi-info text-info me-1"></i>Backup automatico non configurato</li>
                                <li><i class="bi bi-info text-info me-1"></i>Usa export per backup manuali</li>
                                <li><i class="bi bi-info text-info me-1"></i>Log ruotati automaticamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINE SEZIONE CONTENUTO PRINCIPALE --}}
@endsection

{{-- 
    SEZIONE SCRIPT JAVASCRIPT
    Blade PHP: @push('scripts') aggiunge contenuto alla sezione 'scripts' del layout
    Questo codice JavaScript verrà inserito prima della chiusura del tag </body>
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA
    JavaScript: Oggetto globale per contenere dati condivisi tra PHP e JavaScript
    Questo pattern previene conflitti e organizza i dati in un namespace dedicato
*/
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

/*
    SEZIONE DATI CONDIZIONALI
    Blade PHP: @if(isset()) controlla esistenza variabili prima di passarle a JavaScript
    PHP: @json() converte variabili PHP in formato JSON sicuro per JavaScript
    Questo meccanismo permette al JavaScript di accedere ai dati del controller
*/

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
/*
    DATI PRODOTTO SINGOLO
    Laravel: Se la vista ha ricevuto un prodotto specifico dal controller,
    viene convertito in JSON e reso disponibile al JavaScript front-end
*/
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
/*
    COLLEZIONE PRODOTTI
    Laravel: Array/Collection di prodotti convertito in formato JSON
    Utilizzato per popolare select, tabelle dinamiche, autocomplete, etc.
*/
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
/*
    DATI MALFUNZIONAMENTO SINGOLO
    Laravel: Singolo record di malfunzionamento per modifica/visualizzazione
*/
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
/*
    COLLEZIONE MALFUNZIONAMENTI
    Laravel: Lista completa malfunzionamenti per un prodotto specifico
    Utilizzata per popolare dinamicamente interfacce di ricerca e filtri
*/
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
/*
    DATI CENTRO ASSISTENZA SINGOLO
    Laravel: Informazioni specifiche di un centro di assistenza tecnica
*/
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
/*
    COLLEZIONE CENTRI ASSISTENZA
    Laravel: Lista completa centri per popolamento mappe, select, etc.
*/
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
/*
    CATEGORIE PRODOTTI
    Laravel: Elenco categorie per filtri e classificazione prodotti
*/
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
/*
    MEMBRI STAFF TECNICO
    Laravel: Lista membri staff per assegnazioni prodotti e autorizzazioni
*/
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
/*
    STATISTICHE SISTEMA
    Laravel: Dati aggregati per dashboard, grafici e monitoraggio
*/
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
/*
    DATI UTENTE CORRENTE
    Laravel: Informazioni utente autenticato per personalizzazione interfaccia
*/
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
/*
    PATTERN ESTENSIBILE
    Questo sistema permette di aggiungere facilmente nuovi dati
    senza modificare la struttura JavaScript esistente
*/
</script>
@endpush

{{-- 
    SEZIONE STILI CSS
    Blade PHP: @push('styles') aggiunge CSS personalizzati al layout
    Questi stili vengono inseriti nella sezione <head> della pagina
--}}
@push('styles')
<style>
/* === STILI COMPATTI PER MANUTENZIONE ADMIN === */

/*
    LAYOUT GENERALE COMPATTO
    CSS: Stili per ottimizzare lo spazio e migliorare l'usabilità
*/
.container {
    max-width: 1200px;
}

/*
    CARD PIÙ COMPATTE E MODERNE
    CSS: Personalizzazione componenti Bootstrap per design contemporaneo
    Border-radius, transizioni e effetti hover per migliorare UX
*/
.card {
    border-radius: 12px;
    border: none;
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-body {
    font-size: 0.9rem;
    line-height: 1.4;
}

/*
    HEADER COMPATTO
    CSS: Riduzione dimensioni per layout più efficiente
*/
h2 {
    font-size: 1.75rem;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 8px;
}

/*
    STATISTICHE SISTEMA HEADER - STILE CARD PICCOLE
    CSS: Ottimizzazione padding per card informative
*/
.card-body.py-2 {
    padding: 0.75rem !important;
}

/*
    PROGRESS BAR MIGLIORATA
    CSS: Styling personalizzato per indicatori di progresso
*/
.progress {
    height: 15px;
    border-radius: 8px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 15px;
}

/*
    PULSANTI COMPATTI CON STATI
    CSS: Design system per pulsanti con feedback visivo
*/
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/*
    ALERT COMPATTI
    CSS: Riduzione padding per messaggi più discreti
*/
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.alert {
    border-radius: 8px;
    border: none;
}

/*
    BADGE E STATUS INDICATORS
    CSS: Componenti per indicatori di stato
*/
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35em 0.65em;
    border-radius: 6px;
}

/*
    SPINNER LOADING STATES
    CSS: Animazioni di caricamento ottimizzate
*/
.spinner-border-sm {
    width: 0.875rem;
    height: 0.875rem;
    border-width: 0.1em;
}

/*
    FORM CONTROLS COMPATTI
    CSS: Ottimizzazione elementi di form
*/
.form-check {
    font-size: 0.875rem;
}

.form-check-input {
    border-radius: 4px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/*
    ICONE E DISPLAY
    CSS: Sizing coerente per elementi decorativi
*/
.display-6 {
    font-size: 2.5rem;
}

.fs-4 {
    font-size: 1.25rem !important;
}

/*
    GRID SYSTEM OTTIMIZZATO
    CSS: Spaziature personalizzate per layout denso
*/
.row.g-1 > * {
    padding-right: 0.25rem;
    padding-left: 0.25rem;
    margin-bottom: 0.25rem;
}

.row.g-2 > * {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: 0.5rem;
}

.row.g-3 > * {
    padding-right: 0.75rem;
    padding-left: 0.75rem;
    margin-bottom: 0.75rem;
}

/*
    TABELLE RESPONSIVE
    CSS: Ottimizzazione tabelle per dati densi
*/
.table-responsive {
    border-radius: 8px;
    font-size: 0.875rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.5rem;
}

.table td {
    padding: 0.5rem;
    vertical-align: middle;
}

/*
    CODE E ELEMENTI MONOSPACE
    CSS: Styling per codice e dati tecnici
*/
code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

/*
    LISTA COMPATTA
    CSS: Ottimizzazione liste informative
*/
.list-unstyled li {
    padding: 0.125rem 0;
    font-size: 0.875rem;
}

/*
    SEPARATORI
    CSS: Elementi divisori sottili
*/
hr {
    margin: 1rem 0;
    opacity: 0.25;
}

/*
    TOAST NOTIFICATIONS
    CSS: Sistema notifiche sovrapposto
*/
.toast-notification {
    border-radius: 8px;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-width: 300px !important;
}

/*
    SYSTEM STATUS SPECIFICO
    CSS: Styling per area monitoraggio sistema
*/
#system-status {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/*
    RESPONSIVE MIGLIORAMENTI
    CSS: Media queries per ottimizzazione dispositivi
*/
@media (max-width: 992px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    /*
        HEADER RESPONSIVE
        CSS: Layout verticale per schermi piccoli
    */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .btn-group {
        margin-top: 0.5rem;
    }
    
    /*
        CARD PIÙ COMPATTE SU MOBILE
        CSS: Riduzione padding per dispositivi mobili
    */
    .card-body.p-2 {
        padding: 0.5rem !important;
    }
    
    .card-body.p-3 {
        padding: 0.75rem !important;
    }
    
    /*
        FONT SIZES RIDOTTI
        CSS: Tipografia ottimizzata per mobile
    */
    h2 {
        font-size: 1.5rem;
    }
    
    .card-header h6 {
        font-size: 0.875rem;
    }
    
    /*
        GRID MOBILE
        CSS: Spaziature ridotte per layout mobile
    */
    .col-lg-3,
    .col-lg-4,
    .col-lg-6 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    /*
        LAYOUT SUPER COMPATTO PER SMALL SCREENS
        CSS: Ottimizzazione estrema per schermi molto piccoli
    */
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .card {
        margin-bottom: 0.75rem;
    }
    
    .btn.w-100 {
        margin-bottom: 0.25rem;
    }
    
    .small, small {
        font-size: 0.75rem !important;
    }
    
    /*
        RIDUCI PADDING GENERALE
        CSS: Minimizzazione spazi per dispositivi small
    */
    .row.g-3 {
        --bs-gutter-x: 0.5rem;
        --bs-gutter-y: 0.5rem;
    }
}

/*
    DARK MODE SUPPORT
    CSS: Supporto tema scuro automatico
*/
@media (prefers-color-scheme: dark) {
    .bg-light {
        background-color: #212529 !important;
        color: #fff;
    }
    
    .text-muted {
        color: #adb5bd !important;
    }
    
    .card {
        background-color: #2d3748;
        color: #fff;
    }
}

/*
    HIGH CONTRAST SUPPORT
    CSS: Accessibilità per utenti con necessità di alto contrasto
*/
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000;
    }
    
    .btn {
        border-width: 2px;
    }
    
    .badge {
        border: 1px solid;
    }
}

/*
    REDUCED MOTION SUPPORT
    CSS: Accessibilità per utenti sensibili alle animazioni
*/
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    * {
        transition: none !important;
    }
    
    .spinner-border {
        animation: none !important;
    }
}

/*
    PRINT STYLES
    CSS: Ottimizzazione per stampa
*/
@media print {
    .btn,
    .alert,
    .toast-notification,
    #system-status,
    .form-check {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }
    
    .container {
        max-width: none;
        padding: 0;
    }
    
    h2 {
        page-break-after: avoid;
    }
}

/*
    ANIMAZIONI PERSONALIZZATE
    CSS: Keyframes per transizioni fluide
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

.card {
    animation: fadeInUp 0.3s ease-out;
}

/*
    STATI DI CARICAMENTO
    CSS: Feedback visivo durante operazioni asincrone
*/
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading .card {
    position: relative;
}

.loading .card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    z-index: 10;
}

/*
    FOCUS STATES PER ACCESSIBILITÀ
    CSS: Indicatori di focus per navigazione da tastiera
*/
.btn:focus,
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

/*
    CUSTOM SCROLLBAR
    CSS: Scrollbar personalizzata per Webkit browsers
*/
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/*
    UTILITY CLASSES
    CSS: Classi di utilità per uso comune
*/
.fw-semibold {
    font-weight: 600;
}

.rounded-lg {
    border-radius: 12px;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/*
    PERFORMANCE OPTIMIZATIONS
    CSS: Ottimizzazioni per prestazioni rendering
*/
.card,
.btn {
    will-change: transform;
}

/*
    ENSURE PROPER STACKING
    CSS: Gestione z-index per layering corretto
*/
.toast-notification {
    z-index: 1060;
}

.modal {
    z-index: 1050;
}

/*
    CUSTOM PROPERTIES FOR THEMING
    CSS: Variabili CSS per sistema di temi coerente
*/
:root {
    --admin-primary: #0d6efd;
    --admin-success: #198754;
    --admin-warning: #ffc107;
    --admin-danger: #dc3545;
    --admin-info: #0dcaf0;
    --admin-secondary: #6c757d;
    --admin-border-radius: 12px;
    --admin-transition: all 0.2s ease-in-out;
}
</style>
@endpush