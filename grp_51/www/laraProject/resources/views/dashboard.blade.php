{{-- 
    ===================================================================
    DASHBOARD GENERALE
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/dashboard.blade.php
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    DESCRIZIONE: Vista della dashboard generale per tutti gli utenti
                 Mostra informazioni di base e link rapidi alle funzionalità
                 Visualizza alert per utenti con dashboard specifiche (tecnici, staff, admin)
    
    FUNZIONALITÀ:
    - Benvenuto personalizzato con informazioni utente
    - Link rapidi alle sezioni principali
    - Statistiche generali del sistema
    - Reindirizzamento suggerito per utenti con ruoli specifici
    ===================================================================
--}}

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Estende il layout principale dell'applicazione
    PARAMETRO: 'layouts.app' - percorso del template di layout base
    SCOPO: Eredita la struttura HTML, header, footer e navbar dal layout principale
--}}
@extends('layouts.app')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Definisce il titolo della pagina per il tag <title> del browser
    PARAMETRO: 'Dashboard' - stringa che apparirà nella scheda del browser
--}}
@section('title', 'Dashboard')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Inizio della sezione contenuto principale
    SCOPO: Tutto il codice fino a @endsection verrà inserito nello slot 'content' del layout
--}}
@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Icons
                FUNZIONE: Titolo principale della pagina con icona
                CLASSE h2: dimensione del titolo (h2 invece di h1 per gerarchia visiva)
            --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                Dashboard Generale
            </h1>
            
            {{-- === BENVENUTO PERSONALIZZATO === --}}
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Alert box con informazioni di benvenuto e dati utente
                CLASSE alert-info: colore azzurro informativo
                CLASSE border-start: bordo sinistro colorato
                CLASSE border-4: spessore del bordo (4 unità Bootstrap)
            --}}
            <div class="alert alert-info border-start border-primary border-4">
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap Flexbox
                    FUNZIONE: Layout flexbox per allineare icona e testo orizzontalmente
                    CLASSE d-flex: attiva flexbox
                    CLASSE align-items-center: allinea verticalmente al centro
                --}}
                <div class="d-flex align-items-center">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Icons
                        FUNZIONE: Icona utente grande a sinistra
                        CLASSE display-6: dimensione grande dell'icona
                        CLASSE me-3: margin-end (destra) di 3 unità
                    --}}
                    <i class="bi bi-person-circle display-6 text-primary me-3"></i>
                    <div>
                        {{-- 
                            LINGUAGGIO: Blade Template (PHP) + HTML
                            FUNZIONE: Saluto personalizzato con nome utente
                            VARIABILE $user: istanza del Model User passata dal controller
                            PROPRIETÀ nome_completo: campo del database con nome e cognome
                            SINTASSI {{ }}: output Blade - stampa il valore escapato (sicuro contro XSS)
                        --}}
                        <h4 class="alert-heading mb-1">Benvenuto, {{ $user->nome_completo }}!</h4>
                        
                        {{-- 
                            LINGUAGGIO: HTML + Blade
                            FUNZIONE: Mostra il livello di accesso dell'utente con badge colorato
                        --}}
                        <p class="mb-0">
                            Livello di accesso: 
                            {{-- 
                                LINGUAGGIO: HTML + Blade
                                FUNZIONE: Badge dinamico che cambia colore in base al livello utente
                                CLASSE badge-livello: classe base per tutti i badge livello
                                CLASSE badge-livello-{{ $user->livello_accesso }}: classe dinamica specifica
                                ESEMPIO: se livello_accesso = 4, diventa "badge-livello-4"
                                PROPRIETÀ livello_accesso: numero intero (1-4) che indica il ruolo
                            --}}
                            <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }} ms-1">
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Mostra la descrizione testuale del livello
                                    PROPRIETÀ livello_descrizione: accessor o attributo del Model User
                                    ESEMPIO OUTPUT: "Tecnico", "Staff", "Amministratore"
                                --}}
                                {{ $user->livello_descrizione }}
                            </span>
                        </p>
                        
                        {{-- 
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Direttiva condizionale per mostrare info centro solo ai tecnici
                            OPERATORE &&: AND logico - entrambe le condizioni devono essere vere
                        --}}
                        @if($user->isTecnico() && $user->centroAssistenza)
                            {{-- 
                                LINGUAGGIO: HTML + Blade
                                FUNZIONE: Mostra nome e città del centro di assistenza del tecnico
                                METODO isTecnico(): metodo del Model User - verifica se livello_accesso == 2
                                RELAZIONE centroAssistenza: relazione Eloquent belongsTo verso CentroAssistenza
                                PROPRIETÀ nome e citta: campi del Model CentroAssistenza
                            --}}
                            <small class="text-muted">
                                Centro: {{ $user->centroAssistenza->nome }} - {{ $user->centroAssistenza->citta }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- === MESSAGGIO DI REINDIRIZZAMENTO === --}}
            {{-- 
                LINGUAGGIO: Blade Template (PHP)
                FUNZIONE: Verifica se l'utente ha livello >= 2 (Tecnico, Staff o Admin)
                CONDIZIONE: $user->livello_accesso >= 2
                SCOPO: Mostrare alert solo agli utenti con dashboard specifiche disponibili
                LIVELLI: 1=Pubblico, 2=Tecnico, 3=Staff, 4=Admin
            --}}
            @if($user->livello_accesso >= 2)
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Alert informativo con link alla dashboard specifica dell'utente
                    CLASSE alert-light: sfondo grigio chiaro
                    CLASSE border-secondary: bordo grigio
                --}}
                <div class="alert alert-light border-start border-secondary border-4">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Flexbox
                        FUNZIONE: Layout flexbox per distribuire contenuto e pulsante
                        CLASSE justify-content-between: spazio tra gli elementi (testo a sinistra, pulsante a destra)
                    --}}
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Titolo dell'alert con icona informativa
                            --}}
                            <h5 class="alert-heading mb-1">
                                <i class="bi bi-info-circle me-2"></i>
                                Dashboard Specifica Disponibile
                            </h5>
                            <p class="mb-0">
                                Hai accesso a una dashboard personalizzata per il tuo livello di accesso.
                            </p>
                        </div>
                        <div>
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Verifica se l'utente è Admin (livello 4)
                                METODO isAdmin(): metodo del Model User
                                RETURN: boolean - true se livello_accesso == 4
                            --}}
                            @if($user->isAdmin())
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard amministratore
                                    HELPER route(): genera URL per la route nominata 'admin.dashboard'
                                    CLASSE btn-danger: pulsante rosso per admin
                                --}}
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                    <i class="bi bi-shield-check me-1"></i>Dashboard Admin
                                </a>
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Altrimenti, verifica se è Staff (livello 3)
                                METODO isStaff(): metodo del Model User
                                RETURN: boolean - true se livello_accesso == 3
                            --}}
                            @elseif($user->isStaff())
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard staff
                                    HELPER route(): genera URL per la route 'staff.dashboard'
                                    CLASSE btn-warning: pulsante giallo/arancione per staff
                                --}}
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning">
                                    <i class="bi bi-person-badge me-1"></i>Dashboard Staff
                                </a>
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Altrimenti, verifica se è Tecnico (livello 2)
                                METODO isTecnico(): metodo del Model User
                                RETURN: boolean - true se livello_accesso == 2
                            --}}
                            @elseif($user->isTecnico())
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard tecnico
                                    HELPER route(): genera URL per la route 'tecnico.dashboard'
                                    CLASSE btn-info: pulsante azzurro per tecnico
                                --}}
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info">
                                    <i class="bi bi-person-gear me-1"></i>Dashboard Tecnico
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- 
        LINGUAGGIO: HTML + Bootstrap Grid
        FUNZIONE: Griglia responsive per sezione accessi rapidi e statistiche
        CLASSE row: container della griglia Bootstrap
        CLASSE g-4: gap (spaziatura) di 4 unità tra le colonne
    --}}
    <div class="row g-4">
        
        {{-- === ACCESSI RAPIDI GENERALI === --}}
        {{-- 
            LINGUAGGIO: HTML + Bootstrap
            FUNZIONE: Colonna principale per i link di accesso rapido
            CLASSE col-lg-8: occupa 8 colonne su 12 su schermi large (66%)
        --}}
        <div class="col-lg-8">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card contenitore per i pulsanti di accesso rapido
                CLASSE card-custom: classe personalizzata definita nei CSS
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Accessi Rapidi
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Grid
                        FUNZIONE: Griglia per organizzare i pulsanti di accesso rapido
                        CLASSE row g-3: griglia con gap di 3 unità
                    --}}
                    <div class="row g-3">
                        
                        {{-- === LINK PER TUTTI GLI UTENTI === --}}
                        {{-- 
                            LINK 1: CATALOGO PRODOTTI
                            LINGUAGGIO: HTML + Bootstrap + Blade
                            FUNZIONE: Pulsante per accedere all'elenco prodotti (accessibile a tutti)
                            CLASSE col-md-6: 6/12 colonne (50%) su schermi medi
                            CLASSE col-lg-4: 4/12 colonne (33%) su schermi large
                        --}}
                        <div class="col-md-6 col-lg-4">
                            {{-- 
                                LINGUAGGIO: Blade Template + HTML
                                FUNZIONE: Link al catalogo prodotti
                                HELPER route(): genera URL per la route 'prodotti.index'
                                ROUTE prodotti.index: corrisponde al metodo index() del ProdottiController
                                CLASSE btn-lg: pulsante grande
                                CLASSE w-100: larghezza 100% (full width)
                                CLASSE h-100: altezza 100% per uniformità con altri pulsanti
                            --}}
                            <a href="{{ route('prodotti.index') }}" class="btn btn-outline-primary btn-lg w-100 h-100">
                                {{-- 
                                    LINGUAGGIO: HTML + Bootstrap Icons
                                    FUNZIONE: Icona box per rappresentare i prodotti
                                    CLASSE display-6: dimensione grande dell'icona
                                    CLASSE d-block: display block per centrare l'icona
                                    CLASSE mb-2: margin-bottom di 2 unità
                                --}}
                                <i class="bi bi-box display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- 
                            LINK 2: CENTRI ASSISTENZA
                            LINGUAGGIO: HTML + Bootstrap + Blade
                            FUNZIONE: Pulsante per visualizzare l'elenco dei centri di assistenza
                        --}}
                        <div class="col-md-6 col-lg-4">
                            {{-- 
                                LINGUAGGIO: Blade Template + HTML
                                FUNZIONE: Link all'elenco centri assistenza
                                HELPER route(): genera URL per la route 'centri.index'
                                ROUTE centri.index: metodo index() del CentriController
                                CLASSE btn-outline-info: pulsante con bordo azzurro
                            --}}
                            <a href="{{ route('centri.index') }}" class="btn btn-outline-info btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- === LINK PER TECNICI E SUPERIORI === --}}
                        {{-- 
                            LINGUAGGIO: Blade Template (PHP) + Laravel Gates/Policies
                            FUNZIONE: Direttiva @can per verificare i permessi utente
                            PARAMETRO 'viewMalfunzionamenti': nome dell'abilità/permission
                            SCOPO: Mostra il link solo se l'utente ha il permesso di visualizzare malfunzionamenti
                            SISTEMA: Laravel Authorization - definito in AuthServiceProvider o Policy
                            PERMESSO: generalmente concesso a utenti con livello >= 2 (Tecnici, Staff, Admin)
                        --}}
                        @can('viewMalfunzionamenti')
                            {{-- 
                                LINK 3: MALFUNZIONAMENTI
                                LINGUAGGIO: HTML + Bootstrap + Blade
                                FUNZIONE: Link ai malfunzionamenti (solo per utenti autorizzati)
                            --}}
                            <div class="col-md-6 col-lg-4">
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla vista tecnica del catalogo prodotti
                                    HELPER route(): genera URL base per 'prodotti.index'
                                    QUERY STRING: ?view=tech - parametro GET per vista tecnica
                                    SCOPO: Mostra i prodotti con focus sui malfunzionamenti
                                --}}
                                <a href="{{ route('prodotti.index') }}?view=tech" class="btn btn-outline-warning btn-lg w-100 h-100">
                                    <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Malfunzionamenti</span>
                                </a>
                            </div>
                        {{-- 
                            LINGUAGGIO: Blade Template
                            FUNZIONE: Chiude il blocco @can
                        --}}
                        @endcan
                        
                        {{-- === LINK ALLE DASHBOARD SPECIFICHE === --}}
                        {{-- 
                            LINK 4: DASHBOARD TECNICO
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Mostra link alla dashboard tecnico solo per utenti con ruolo Tecnico
                            METODO isTecnico(): verifica se $user->livello_accesso == 2
                        --}}
                        @if($user->isTecnico())
                            <div class="col-md-6 col-lg-4">
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard personalizzata per tecnici
                                    HELPER route(): genera URL per 'tecnico.dashboard'
                                    ROUTE: definita in routes/web.php con middleware ['auth', 'tecnico']
                                    CLASSE btn-info: pulsante azzurro per identificare il ruolo tecnico
                                --}}
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info btn-lg w-100 h-100">
                                    <i class="bi bi-person-gear display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Tecnico</span>
                                </a>
                            </div>
                        @endif
                        
                        {{-- 
                            LINK 5: DASHBOARD STAFF
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Mostra link alla dashboard staff solo per membri dello staff
                            METODO isStaff(): verifica se $user->livello_accesso == 3
                        --}}
                        @if($user->isStaff())
                            <div class="col-md-6 col-lg-4">
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard personalizzata per staff tecnico
                                    HELPER route(): genera URL per 'staff.dashboard'
                                    ROUTE: definita con middleware ['auth', 'staff']
                                    CLASSE btn-warning: pulsante giallo/arancione per staff
                                --}}
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning btn-lg w-100 h-100">
                                    <i class="bi bi-person-badge display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Staff</span>
                                </a>
                            </div>
                        @endif
                        
                        {{-- 
                            LINK 6: DASHBOARD ADMIN
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Mostra link alla dashboard admin solo per amministratori
                            METODO isAdmin(): verifica se $user->livello_accesso == 4
                        --}}
                        @if($user->isAdmin())
                            <div class="col-md-6 col-lg-4">
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla dashboard amministratore
                                    HELPER route(): genera URL per 'admin.dashboard'
                                    ROUTE: definita con middleware ['auth', 'admin']
                                    CLASSE btn-danger: pulsante rosso per admin (massima autorità)
                                --}}
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-lg w-100 h-100">
                                    <i class="bi bi-person-fill-gear display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Admin</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE GENERALI === --}}
        {{-- 
            LINGUAGGIO: HTML + Bootstrap
            FUNZIONE: Colonna laterale per le statistiche del sistema
            CLASSE col-lg-4: occupa 4 colonne su 12 su schermi large (33%)
        --}}
        <div class="col-lg-4">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card contenitore per le statistiche
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Verifica se esistono statistiche da visualizzare
                        CONTROLLO isset($stats): verifica che la variabile $stats esista
                        FUNZIONE count($stats): conta gli elementi nell'array
                        OPERATORE &&: AND logico - entrambe le condizioni devono essere vere
                        VARIABILE $stats: array associativo passato dal controller
                    --}}
                    @if(isset($stats) && count($stats) > 0)
                        {{-- 
                            LINGUAGGIO: HTML + Bootstrap Grid
                            FUNZIONE: Griglia per organizzare le card statistiche
                            CLASSE text-center: allinea il testo al centro
                        --}}
                        <div class="row g-3 text-center">
                            {{-- 
                                STATISTICA 1: TOTALE PRODOTTI
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Verifica se esiste la statistica 'total_prodotti'
                                CONTROLLO isset(): verifica esistenza della chiave nell'array
                            --}}
                            @if(isset($stats['total_prodotti']))
                                {{-- 
                                    LINGUAGGIO: HTML + Bootstrap
                                    FUNZIONE: Card per visualizzare il numero totale di prodotti
                                    CLASSE col-6: occupa metà larghezza (6/12 colonne)
                                --}}
                                <div class="col-6">
                                    {{-- 
                                        LINGUAGGIO: HTML + Bootstrap
                                        FUNZIONE: Container con sfondo colorato e bordi arrotondati
                                        CLASSE bg-primary: sfondo blu
                                        CLASSE bg-opacity-10: opacità del 10% (sfondo molto chiaro)
                                        CLASSE rounded: bordi arrotondati
                                    --}}
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        {{-- 
                                            LINGUAGGIO: Blade Template (PHP) + HTML
                                            FUNZIONE: Visualizza il numero totale di prodotti
                                            VARIABILE $stats['total_prodotti']: valore numerico dall'array
                                            SINTASSI {{ }}: output Blade
                                        --}}
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- 
                                STATISTICA 2: TOTALE CENTRI
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Verifica se esiste la statistica 'total_centri'
                            --}}
                            @if(isset($stats['total_centri']))
                                <div class="col-6">
                                    {{-- 
                                        LINGUAGGIO: HTML + Bootstrap
                                        FUNZIONE: Card per visualizzare il numero totale di centri assistenza
                                        CLASSE bg-info: sfondo azzurro
                                    --}}
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        <i class="bi bi-geo-alt text-info fs-1"></i>
                                        {{-- 
                                            LINGUAGGIO: Blade Template (PHP) + HTML
                                            FUNZIONE: Visualizza il numero totale di centri
                                            VARIABILE $stats['total_centri']: valore numerico
                                        --}}
                                        <h4 class="mt-2 mb-1">{{ $stats['total_centri'] }}</h4>
                                        <small class="text-muted">Centri</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    {{-- 
                        LINGUAGGIO: Blade Template
                        FUNZIONE: Blocco @else eseguito se non ci sono statistiche disponibili
                    --}}
                    @else
                        {{-- 
                            LINGUAGGIO: HTML
                            FUNZIONE: Messaggio di fallback quando non ci sono dati
                        --}}
                        <p class="text-muted text-center">Nessuna statistica disponibile</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- === INFORMAZIONI GENERALI === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap
        FUNZIONE: Sezione informativa sul sistema di assistenza tecnica
        CLASSE row mt-4: griglia con margin-top di 4 unità per spaziare dalla sezione precedente
    --}}
    <div class="row mt-4">
        <div class="col-12">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card informativa con sfondo chiaro e contenuto centrato
                CLASSE card-custom: classe personalizzata per stile uniforme
                CLASSE bg-light: sfondo grigio chiaro
            --}}
            <div class="card card-custom bg-light">
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Corpo della card con contenuto centrato
                    CLASSE text-center: allinea tutto il testo al centro
                --}}
                <div class="card-body text-center">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Titolo della sezione informativa
                    --}}
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Sistema di Assistenza Tecnica
                    </h5>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Descrizione breve del sistema
                        CLASSE text-muted: testo grigio
                        CLASSE mb-3: margin-bottom di 3 unità
                    --}}
                    <p class="text-muted mb-3">
                        Piattaforma per la gestione dell'assistenza tecnica e supporto post-vendita
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Flexbox
                        FUNZIONE: Container flexbox per i pulsanti di link esterni
                        CLASSE d-flex: attiva flexbox
                        CLASSE flex-wrap: permette il wrap dei pulsanti su più righe se necessario
                        CLASSE justify-content-center: centra i pulsanti orizzontalmente
                        CLASSE gap-3: spaziatura di 3 unità tra i pulsanti
                    --}}
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        {{-- 
                            LINK 1: CHI SIAMO
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Link alla pagina informazioni azienda
                            HELPER route(): genera URL per la route 'azienda'
                            ROUTE azienda: corrisponde a una route che mostra info sull'azienda
                            CLASSE btn-outline-secondary: pulsante con bordo grigio
                        --}}
                        <a href="{{ route('azienda') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-building me-1"></i>Chi Siamo
                        </a>
                        
                        {{-- 
                            LINK 2: CONTATTI
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Link alla pagina contatti
                            HELPER route(): genera URL per la route 'contatti'
                            ROUTE contatti: mostra informazioni di contatto dell'azienda
                        --}}
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-telephone me-1"></i>Contatti
                        </a>
                        
                        {{-- 
                            LINK 3: DOCUMENTAZIONE
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Link alla documentazione del progetto (PDF)
                            HELPER route(): genera URL per la route 'documentazione'
                            ATTRIBUTO target="_blank": apre il link in una nuova scheda del browser
                            SCOPO: Permette di consultare la documentazione senza lasciare la dashboard
                            CLASSE btn-outline-primary: pulsante con bordo blu per evidenziarlo
                        --}}
                        <a href="{{ route('documentazione') }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Documentazione
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la sezione 'content' iniziata con @section('content')
    SCOPO: Termina il contenuto principale che verrà inserito nel layout
--}}
@endsection

{{-- === SCRIPTS SECTION === --}}
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Direttiva @push per aggiungere JavaScript alla sezione 'scripts' del layout
    SCOPO: Gli script aggiunti qui vengono inseriti nello stack 'scripts' alla fine del <body>
    VANTAGGIO: Permette a ogni vista di aggiungere script specifici senza modificare il layout
--}}
@push('scripts')
<script>
// ===================================================================
// INIZIALIZZAZIONE DASHBOARD GENERALE
// ===================================================================
// LINGUAGGIO: JavaScript + jQuery
// FUNZIONE: Codice eseguito quando il DOM è completamente caricato

// LINGUAGGIO: JavaScript (jQuery)
// FUNZIONE $(document).ready(): esegue il codice quando il DOM è pronto
// PARAMETRO: funzione anonima che contiene il codice da eseguire
// LIBRERIA: jQuery - deve essere inclusa nel layout principale
// SCOPO: Garantire che tutti gli elementi HTML siano disponibili prima di manipolarli
$(document).ready(function() {
    // LINGUAGGIO: JavaScript
    // FUNZIONE console.log(): stampa un messaggio nella console del browser per debug
    // SCOPO: Verificare che la dashboard si sia caricata correttamente
    // VARIABILE BLADE: {{ $user->livello_accesso }} viene sostituita col valore PHP al rendering
    console.log('Dashboard generale caricata per utente livello {{ $user->livello_accesso }}');
    
    // ===================================================================
    // AUTO-REINDIRIZZAMENTO OPZIONALE
    // ===================================================================
    // LINGUAGGIO: Blade Template (PHP)
    // FUNZIONE: Verifica condizionale per utenti con livello >= 2
    // CONDIZIONE: $user->livello_accesso >= 2 (Tecnico, Staff o Admin)
    // SCOPO: Eseguire codice JavaScript solo per utenti con dashboard specifiche
    @if($user->livello_accesso >= 2)
        // LINGUAGGIO: JavaScript
        // FUNZIONE setTimeout(): esegue una funzione dopo un ritardo specificato
        // PARAMETRI:
        // - Prima parametro: funzione anonima da eseguire
        // - Secondo parametro: 3000 millisecondi (3 secondi) di ritardo
        // SCOPO: Mostrare un messaggio toast dopo 3 secondi dal caricamento
        setTimeout(function() {
            // LINGUAGGIO: JavaScript
            // VARIABILE: stringa contenente il messaggio da visualizzare
            const toastMessage = 'Ricorda: hai accesso a una dashboard personalizzata per il tuo ruolo!';
            
            // LINGUAGGIO: JavaScript
            // FUNZIONE: Verifica se esiste la funzione showToast nel contesto globale
            // OPERATORE typeof: restituisce il tipo della variabile come stringa
            // CONFRONTO: 'function' indica che showToast è una funzione disponibile
            // SCOPO: Evitare errori se la funzione showToast non è definita
            if (typeof showToast === 'function') {
                // LINGUAGGIO: JavaScript
                // FUNZIONE showToast(): funzione personalizzata (definita altrove nell'app)
                // PARAMETRI:
                // - toastMessage: testo del messaggio da mostrare
                // - 'info': tipo di notifica (info, success, warning, error)
                // SCOPO: Mostrare una notifica toast all'utente
                // NOTA: Questa funzione deve essere definita nel layout o in un file JS globale
                showToast(toastMessage, 'info');
            }
        }, 3000);
    {{-- 
        LINGUAGGIO: Blade Template
        FUNZIONE: Chiude il blocco condizionale @if($user->livello_accesso >= 2)
    --}}
    @endif
});
</script>
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la direttiva @push('scripts')
    SCOPO: Termina l'aggiunta di contenuto allo stack 'scripts'
--}}
@endpush