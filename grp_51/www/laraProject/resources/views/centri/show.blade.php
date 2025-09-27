{{--
    ===================================================================
    VISTA DETTAGLIO CENTRO ASSISTENZA - PARTE 1 - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/centri/show.blade.php
    
    DESCRIZIONE:
    Vista per visualizzazione dettagliata di un singolo centro di assistenza tecnica.
    Accessibile pubblicamente (Livello 1) con informazioni complete per tutti gli utenti.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Visualizzazione informazioni complete centro assistenza
    - Lista dettagliata tecnici specializzati con competenze
    - Informazioni contatto dirette (telefono, email, indirizzo)
    - Integrazione Google Maps per localizzazione
    - Centri vicini nella stessa provincia per confronto
    - Link utili basati su livello autorizzazione utente
    - Sezione informativa sui servizi di assistenza
    - Layout responsive ottimizzato per tutti i dispositivi
    ===================================================================
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends eredita il layout principale dell'applicazione
--}}
@extends('layouts.app')

{{-- 
    TITOLO DINAMICO DELLA PAGINA
    Blade PHP: @section('title') con concatenazione dinamica
    Laravel: $centro->nome accede al model passato dal controller
    SEO: Titolo descrittivo per risultati motori di ricerca
--}}
@section('title', $centro->nome . ' - Centro Assistenza')

{{-- 
    META DESCRIPTION PER SEO
    Blade PHP: @section personalizzata per ottimizzazione SEO
    HTML: Meta tag description con dati dinamici centro
    Migliora posizionamento nei risultati di ricerca locali
--}}
@section('meta_description', 'Centro di assistenza ' . $centro->nome . ' a ' . $centro->citta . ' (' . $centro->provincia . '). Contatti, tecnici specializzati e servizi disponibili.')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto inserito nel layout
--}}
@section('content')
{{-- 
    CONTAINER BOOTSTRAP STANDARD
    Bootstrap: container per layout centrato e responsive
--}}
<div class="container mt-4">
    
    {{-- 
        LAYOUT A DUE COLONNE
        Bootstrap: row per contenitore griglia responsive
    --}}
    <div class="row">
        {{-- 
            COLONNA PRINCIPALE DETTAGLI CENTRO
            Bootstrap: col-lg-8 per 66% larghezza su desktop, 100% su mobile
        --}}
        <div class="col-lg-8">
            
            {{-- === HEADER DEL CENTRO === 
                HTML: Card principale con informazioni primarie centro
            --}}
            <div class="card card-custom mb-4">
                {{-- 
                    HEADER CARD CON SFONDO PRIMARIO
                    Bootstrap: bg-primary text-white per branding aziendale
                --}}
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            {{-- 
                                TITOLO PRINCIPALE CENTRO
                                HTML: h1 con classe h3 per dimensioni ottimali
                                Laravel: $centro->nome dal model Eloquent
                            --}}
                            <h1 class="h3 mb-0">
                                <i class="bi bi-building me-2"></i>
                                {{ $centro->nome }}
                            </h1>
                            {{-- 
                                SOTTOTITOLO DESCRITTIVO
                                Bootstrap: opacity-75 per testo secondario
                            --}}
                            <p class="mb-0 opacity-75">
                                Centro di Assistenza Tecnica
                            </p>
                        </div>
                        <div class="col-auto">
                            {{-- 
                                BADGE NUMERO TECNICI CONDIZIONALE
                                Laravel: $centro->tecnici_count da withCount() nel controller
                                UX: Mostra solo se ci sono tecnici disponibili
                            --}}
                            @if($centro->tecnici_count > 0)
                                <span class="badge bg-light text-primary fs-6">
                                    <i class="bi bi-people me-1"></i>
                                    {{-- 
                                        PLURALIZZAZIONE DINAMICA
                                        PHP: Controllo condizionale per grammatica italiana corretta
                                    --}}
                                    {{ $centro->tecnici_count }} Tecnic{{ $centro->tecnici_count > 1 ? 'i' : 'o' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- 
                        GRIGLIA INFORMAZIONI PRINCIPALE
                        Bootstrap: row g-4 per gap uniforme tra colonne
                    --}}
                    <div class="row g-4">
                        {{-- 
                            COLONNA INFORMAZIONI DI CONTATTO
                            Bootstrap: col-md-6 per layout 50/50 su tablet+
                        --}}
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-geo-alt me-2"></i>
                                Informazioni di Contatto
                            </h5>
                            
                            {{-- 
                                SEZIONE INDIRIZZO
                                HTML: Struttura con label e contenuto per accessibilità
                            --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">INDIRIZZO</label>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{-- 
                                        INDIRIZZO CON FALLBACK
                                        PHP: ?? operator per valore default se campo vuoto
                                    --}}
                                    {{ $centro->indirizzo ?? 'Non specificato' }}
                                </p>
                                {{-- 
                                    INFORMAZIONI GEOGRAFICHE DETTAGLIATE
                                    Laravel: Accesso attributi model con controlli condizionali
                                --}}
                                <p class="text-muted small mb-0">
                                    {{ $centro->citta }}
                                    @if($centro->cap)
                                        {{ $centro->cap }}
                                    @endif
                                    @if($centro->provincia)
                                        {{-- 
                                            PROVINCIA IN MAIUSCOLO
                                            PHP: strtoupper() per formattazione standard italiana
                                        --}}
                                        ({{ strtoupper($centro->provincia) }})
                                    @endif
                                </p>
                            </div>
                            
                            {{-- 
                                SEZIONE TELEFONO CONDIZIONALE
                                Laravel: @if verifica esistenza campo nel model
                            --}}
                            @if($centro->telefono)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">TELEFONO</label>
                                    <p class="mb-0">
                                        <i class="bi bi-telephone text-success me-2"></i>
                                        {{-- 
                                            LINK TELEFONO CLICCABILE
                                            HTML: href="tel:" per integrazione app telefono native
                                        --}}
                                        <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                            {{ $centro->telefono }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- 
                                SEZIONE EMAIL CONDIZIONALE
                                Laravel: Controllo esistenza campo email
                            --}}
                            @if($centro->email)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">EMAIL</label>
                                    <p class="mb-0">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        {{-- 
                                            LINK EMAIL CLICCABILE
                                            HTML: href="mailto:" per apertura client email
                                        --}}
                                        <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                            {{ $centro->email }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- 
                            COLONNA ORARI E SERVIZI
                            HTML: Informazioni sui servizi offerti dal centro
                        --}}
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>
                                Servizi e Orari
                            </h5>
                            
                            {{-- 
                                LISTA SERVIZI DISPONIBILI
                                HTML: Lista non ordinata con icone check per checklist visiva
                            --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">SERVIZI DISPONIBILI</label>
                                <ul class="list-unstyled mt-2">
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Diagnostica tecnica</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Riparazione componenti</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Supporto installazione</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Consulenza specialistica</li>
                                </ul>
                            </div>
                            
                            {{-- 
                                INFORMAZIONI ORARI INDICATIVI
                                HTML: Orari standard con note per contatto diretto
                            --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">ORARI INDICATIVI</label>
                                <p class="mb-1">
                                    <i class="bi bi-calendar3 text-primary me-2"></i>
                                    Lunedì - Venerdì: 9:00 - 18:00
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Contattare il centro per confermare gli orari
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        SEZIONE PULSANTI DI AZIONE
                        Bootstrap: Layout flex responsivo per azioni principali
                    --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                {{-- 
                                    PULSANTE CHIAMATA DIRETTA
                                    HTML: href="tel:" per integrazione telefono mobile
                                    Bootstrap: btn-success per azione positiva primaria
                                --}}
                                @if($centro->telefono)
                                    <a href="tel:{{ $centro->telefono }}" 
                                       class="btn btn-success text-white fw-semibold">
                                        <i class="bi bi-telephone-fill me-1"></i>
                                        Chiama Ora
                                    </a>
                                @endif
                                
                                {{-- 
                                    PULSANTE INVIO EMAIL
                                    HTML: href="mailto:" per client email
                                --}}
                                @if($centro->email)
                                    <a href="mailto:{{ $centro->email }}" 
                                       class="btn btn-info text-white fw-semibold">
                                        <i class="bi bi-envelope-fill me-1"></i>
                                        Invia Email
                                    </a>
                                @endif
                                
                                {{-- 
                                    PULSANTE GOOGLE MAPS
                                    PHP: Costruzione URL Google Maps con dati centro
                                    Laravel: urlencode() per encoding sicuro parametri URL
                                --}}
                                @if($centro->indirizzo && $centro->citta)
                                    @php
                                        // Costruzione indirizzo completo per Google Maps
                                        $indirizzoCompleto = urlencode($centro->indirizzo . ', ' . $centro->citta . ($centro->provincia ? ', ' . $centro->provincia : '') . ', Italia');
                                        // URL Google Maps con API di ricerca
                                        $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $indirizzoCompleto;
                                    @endphp
                                    <a href="{{ $mapsUrl }}" 
                                       target="_blank" 
                                       class="btn btn-primary text-white fw-semibold">
                                        <i class="bi bi-map-fill me-1"></i>
                                        Apri Mappa
                                    </a>
                                @endif
                                
                                {{-- 
                                    PULSANTE RITORNO LISTA
                                    Laravel: route('centri.index') per navigazione verso elenco
                                --}}
                                <a href="{{ route('centri.index') }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Torna alla Lista
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- === SEZIONE TECNICI SPECIALIZZATI === 
                Laravel: Sezione condizionale per visualizzazione team tecnico
                UX: Mostra solo se il centro ha tecnici assegnati
            --}}
            @if($centro->tecnici && $centro->tecnici->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-people text-primary me-2"></i>
                            Tecnici Specializzati
                            {{-- 
                                BADGE CONTEGGIO TECNICI
                                Laravel: count() sulla collection tecnici
                            --}}
                            <span class="badge bg-primary ms-2">{{ $centro->tecnici->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- 
                            GRIGLIA TECNICI RESPONSIVE
                            Bootstrap: row g-3 per gap uniforme, layout responsive
                        --}}
                        <div class="row g-3">
                            {{-- 
                                ITERAZIONE ATTRAVERSO TECNICI
                                Laravel: @foreach attraverso collection Eloquent
                                $tecnico è model User con relazione centro
                            --}}
                            @foreach($centro->tecnici as $tecnico)
                                {{-- 
                                    CARD INDIVIDUALE TECNICO
                                    Bootstrap: col-md-6 col-lg-4 per layout adattivo
                                    Mobile: 1 colonna, Tablet: 2 colonne, Desktop: 3 colonne
                                --}}
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded p-3 h-100">
                                        {{-- 
                                            HEADER TECNICO CON AVATAR
                                            HTML: Layout flex per allineamento avatar-info
                                        --}}
                                        <div class="d-flex align-items-center mb-2">
                                            {{-- 
                                                AVATAR CIRCOLARE CON ICONA
                                                CSS: Inline styles per dimensioni precise
                                                Bootstrap: bg-primary per coerenza branding
                                            --}}
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <div>
                                                {{-- 
                                                    NOME COMPLETO TECNICO
                                                    Laravel: Accesso diretto attributi model User
                                                --}}
                                                <h6 class="mb-0">{{ $tecnico->nome }} {{ $tecnico->cognome }}</h6>
                                                {{-- 
                                                    BADGE SPECIALIZZAZIONE CONDIZIONALE
                                                    Laravel: Mostra solo se campo specializzazione esistente
                                                --}}
                                                @if($tecnico->specializzazione)
                                                    <span class="badge bg-light text-dark small">
                                                        {{ $tecnico->specializzazione }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- 
                                            INFORMAZIONI AGGIUNTIVE TECNICO
                                            Laravel: Dati opzionali con controlli condizionali
                                        --}}
                                        {{-- 
                                            ETÀ TECNICO CON CARBON
                                            Laravel: Carbon date manipulation per calcolo età
                                            $tecnico->data_nascita è Carbon instance
                                        --}}
                                        @if($tecnico->data_nascita)
                                            <p class="small text-muted mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Età: {{ $tecnico->data_nascita->age }} anni
                                            </p>
                                        @endif
                                        
                                        {{-- 
                                            SPECIALIZZAZIONE DETTAGLIATA
                                            Laravel: Campo opzionale per competenze specifiche
                                        --}}
                                        @if($tecnico->specializzazione)
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-award me-1"></i>
                                                Specializzazione: {{ $tecnico->specializzazione }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
        
        {{-- === SIDEBAR CON INFORMAZIONI AGGIUNTIVE === 
            Bootstrap: col-lg-4 per 33% larghezza su desktop
            Layout verticale con widget informativi
        --}}
        <div class="col-lg-4">
            
            {{-- === WIDGET MAPPA E POSIZIONE === 
                HTML: Card per informazioni geografiche e contatti rapidi
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Posizione e Contatti
                    </h6>
                </div>
                <div class="card-body">
                    {{-- 
                        INDIRIZZO COMPLETO RIPETUTO
                        HTML: Visualizzazione prominente per sidebar
                    --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt-fill text-primary me-2 mt-1"></i>
                            <div>
                                <strong>{{ $centro->indirizzo ?? 'Indirizzo non disponibile' }}</strong><br>
                                {{ $centro->citta }}
                                @if($centro->cap)
                                    {{ $centro->cap }}
                                @endif
                                <br>
                                @if($centro->provincia)
                                    Provincia di {{ strtoupper($centro->provincia) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        CONTATTI DIRETTI VERTICALI
                        Bootstrap: d-grid gap-2 per layout verticale uniforme
                    --}}
                    <div class="d-grid gap-2">
                        {{-- PULSANTE TELEFONO FULL-WIDTH --}}
                        @if($centro->telefono)
                            <a href="tel:{{ $centro->telefono }}" 
                               class="btn btn-success text-white fw-semibold">
                                <i class="bi bi-telephone-fill me-2"></i>
                                {{ $centro->telefono }}
                            </a>
                        @endif
                        
                        {{-- PULSANTE EMAIL FULL-WIDTH --}}
                        @if($centro->email)
                            <a href="mailto:{{ $centro->email }}" 
                               class="btn btn-info text-white fw-semibold">
                                <i class="bi bi-envelope-fill me-2"></i>
                                {{ $centro->email }}
                            </a>
                        @endif
                        
                        {{-- 
                            PULSANTE GOOGLE MAPS RIPETUTO
                            PHP: Stessa logica costruzione URL della sezione principale
                        --}}
                        @if($centro->indirizzo && $centro->citta)
                            @php
                                $indirizzoCompleto = urlencode($centro->indirizzo . ', ' . $centro->citta . ($centro->provincia ? ', ' . $centro->provincia : '') . ', Italia');
                                $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $indirizzoCompleto;
                            @endphp
                            <a href="{{ $mapsUrl }}" 
                               target="_blank" 
                               class="btn btn-primary text-white fw-semibold">
                                <i class="bi bi-map-fill me-2"></i>
                                Visualizza su Google Maps
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === WIDGET STATISTICHE CENTRO === 
                HTML: Card con metriche aggregate del centro
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistiche Centro
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        {{-- 
                            CONTATORE TECNICI
                            Laravel: tecnici_count da withCount() nel controller
                        --}}
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ $centro->tecnici_count ?? 0 }}</h4>
                                <small class="text-muted">Tecnici</small>
                            </div>
                        </div>
                        {{-- 
                            CONTATORE SPECIALIZZAZIONI UNICHE
                            PHP: Calcolo on-the-fly delle specializzazioni distinct
                            Laravel: Collection methods per elaborazione dati
                        --}}
                        <div class="col-6">
                            @php
                                // Conta specializzazioni uniche tra i tecnici del centro
                                $specializzazioni = $centro->tecnici ? $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count() : 0;
                            @endphp
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">{{ $specializzazioni }}</h4>
                                <small class="text-muted">Specializzazioni</small>
                            </div>
                        </div>
                        {{-- 
                            STATO E DATA ATTIVAZIONE
                            Laravel: created_at è Carbon instance per formatting
                        --}}
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h5 class="text-success mb-1">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Attivo
                                </h5>
                                <small class="text-muted">
                                    Dal {{ $centro->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- === WIDGET CENTRI VICINI === 
                Laravel: Sezione condizionale per centri nella stessa provincia
                UX: Suggerimenti per alternative nella zona
            --}}
            @if(isset($centriVicini) && $centriVicini->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo text-primary me-2"></i>
                            Altri Centri in Provincia di {{ strtoupper($centro->provincia) }}
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- 
                            LISTA CENTRI VICINI
                            Laravel: @foreach attraverso collection $centriVicini
                        --}}
                        @foreach($centriVicini as $centroVicino)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        {{-- 
                                            LINK AL CENTRO VICINO
                                            Laravel: route('centri.show', $centroVicino) con model binding
                                        --}}
                                        <h6 class="mb-1">
                                            <a href="{{ route('centri.show', $centroVicino) }}" 
                                               class="text-decoration-none">
                                                {{ $centroVicino->nome }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $centroVicino->citta }}
                                        </small>
                                    </div>
                                    {{-- 
                                        BADGE CONTEGGIO TECNICI
                                        Laravel: tecnici_count con fallback 0
                                    --}}
                                    <span class="badge bg-light text-dark">
                                        {{ $centroVicino->tecnici_count ?? 0 }} tecnici
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- === WIDGET LINK UTILI === 
                HTML: Card navigazione con link contestuali basati su autorizzazioni
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-primary me-2"></i>
                        Link Utili
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- 
                            LINK RITORNO ELENCO CENTRI
                            Laravel: route('centri.index') per navigazione
                        --}}
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-list me-2"></i>
                            Tutti i Centri
                        </a>
                        
                        {{-- 
                            LINK CATALOGO DINAMICO BASATO SU AUTORIZZAZIONI
                            Laravel: Sistema complesso di controlli livello accesso
                            Auth: Gestione stati autenticato/guest con link appropriati
                        --}}
                        @auth
                            {{-- 
                                UTENTI TECNICI E STAFF (LIVELLI 2-3)
                                Laravel: Auth::user()->livello_accesso per controllo preciso
                            --}}
                            @if(Auth::user()->livello_accesso == 2 || Auth::user()->livello_accesso == 3)
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-tools me-2"></i>
                                    Catalogo Tecnico
                                </a>

                            {{-- AMMINISTRATORI (LIVELLO 4) --}}
                            @elseif(Auth::user()->livello_accesso == 4)
                                <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-shield-lock me-2"></i>
                                    Catalogo Admin
                                </a>

                            {{-- 
                                UTENTI AUTENTICATI CON LIVELLO DIVERSO
                                Laravel: Fallback per livelli non standard
                            --}}
                            @else
                                <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box me-2"></i>
                                    Catalogo Prodotti
                                </a>
                            @endif
                        @else
                            {{-- 
                                UTENTI NON AUTENTICATI
                                Laravel: @guest equivale a !auth()->check()
                                Solo accesso al catalogo pubblico
                            --}}
                            <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box me-2"></i>
                                Catalogo Prodotti
                            </a>
                        @endauth
                        
                        {{-- LINK HOMEPAGE --}}
                        <a href="{{ route('home') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-house me-2"></i>
                            Homepage
                        </a>
                        
                        {{-- 
                            SEZIONE DASHBOARD DINAMICA
                            Laravel: Controlli autorizzazione per accesso dashboard appropriate
                        --}}
                        @guest
                            {{-- 
                                UTENTI NON AUTENTICATI - LINK LOGIN
                                Laravel: @guest per utenti non loggati
                                UX: Invito ad accedere all'area tecnici
                            --}}
                            <hr class="my-2">
                            <a href="{{ route('login') }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-person-lock me-2"></i>
                                Area Tecnici
                            </a>
                        @else
                            {{-- 
                                UTENTI AUTENTICATI - DASHBOARD BASATA SU LIVELLO
                                Laravel: Controlli livello_accesso per routing corretto
                            --}}
                            <hr class="my-2">
                            {{-- AMMINISTRATORI (LIVELLO 4+) --}}
                            @if(Auth::user()->livello_accesso >= 4)
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-gear me-2"></i>
                                    Dashboard Admin
                                </a>
                            {{-- STAFF AZIENDALE (LIVELLO 3+) --}}
                            @elseif(Auth::user()->livello_accesso >= 3)
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Dashboard Staff
                                </a>
                            {{-- TECNICI (LIVELLO 2+) --}}
                            @elseif(Auth::user()->livello_accesso >= 2)
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-person-gear me-2"></i>
                                    Dashboard Tecnico
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    {{-- === SEZIONE INFORMAZIONI AGGIUNTIVE === 
        HTML: Card informativa finale con istruzioni per utenti
        UX: Guida completa su come utilizzare i servizi del centro
    --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sui Servizi di Assistenza
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- 
                            COLONNA PROCEDURA RICHIESTA ASSISTENZA
                            HTML: Lista ordinata per processo step-by-step
                        --}}
                        <div class="col-md-6">
                            <h6 class="text-primary">Come Richiedere Assistenza</h6>
                            <ol class="ps-3">
                                <li><strong>Contatta il centro</strong> telefonicamente o via email</li>
                                <li><strong>Descrivi il problema</strong> in modo dettagliato</li>
                                <li><strong>Fornisci il modello</strong> del prodotto interessato</li>
                                <li><strong>Concorda l'intervento</strong> con il tecnico specializzato</li>
                            </ol>
                        </div>
                        {{-- 
                            COLONNA DOCUMENTAZIONE NECESSARIA
                            HTML: Lista non ordinata con checklist visiva
                        --}}
                        <div class="col-md-6">
                            <h6 class="text-primary">Cosa Portare</h6>
                            <ul class="list-unstyled ps-3">
                                <li><i class="bi bi-check text-success me-2"></i><strong>Documento di identità</strong></li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Scontrino o fattura</strong> di acquisto</li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Documentazione tecnica</strong> del prodotto</li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Eventuali accessori</strong> correlati al problema</li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- 
                        ALERT INFORMATIVO IMPORTANTE
                        Bootstrap: alert-info per messaggio promozionale non invasivo
                        UX: Consiglio pratico per migliorare esperienza utente
                    --}}
                    <div class="alert alert-info mt-3 mb-0">
                        <div class="d-flex">
                            <i class="bi bi-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <strong>Importante:</strong> 
                                Prima di recarti presso il centro, ti consigliamo di contattarlo telefonicamente 
                                per verificare la disponibilità e concordare l'appuntamento più adatto alle tue esigenze.
                            </div>
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
    Permette comunicazione sicura tra backend Laravel e frontend
*/
window.PageData = window.PageData || {};

/*
    SEZIONE DATI CONDIZIONALI STANDARDIZZATA
    Blade PHP: Controlli @if(isset()) per passaggio sicuro dati
    Laravel: @json() per serializzazione sicura PHP->JavaScript
    NOTA: Sistema standardizzato per compatibilità tra tutte le viste dell'applicazione
*/

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
/*
    DATI PRODOTTO SINGOLO
    Laravel: Model Eloquent -> JSON Object
    Uso potenziale: Dettagli prodotto per future interazioni JavaScript
*/
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
/*
    COLLEZIONE PRODOTTI
    Laravel: Collection -> JSON Array
    Uso potenziale: Popolamento select dinamici, autocomplete
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
    Laravel: Collection -> JSON per dashboard o filtri
*/
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
/*
    DATI CENTRO CORRENTE (PRINCIPALE)
    Laravel: Model Centro con relazioni tecnici -> JSON
    Uso: Informazioni centro per interazioni JavaScript (mappe, contatti, etc.)
*/
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
/*
    COLLEZIONE CENTRI
    Laravel: Collection -> JSON per navigazione o confronti
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
    Laravel: Collection staff -> JSON per gestione assegnazioni
*/
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
/*
    STATISTICHE CENTRO
    Laravel: Array statistiche -> JSON per grafici o dashboard dinamica
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
    JavaScript: Questa struttura permette aggiunta facile di nuovi dati
    senza modificare l'architettura JavaScript esistente
    Uso futuro: Integrazioni mappe, chat tecnici, booking appuntamenti
*/
</script>
@endpush

{{-- === SEZIONE CSS PERSONALIZZATO === 
    Blade PHP: @push('styles') aggiunge CSS al layout nell'head
--}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI VISTA DETTAGLIO CENTRO === 
   CSS: Design system specifico per pagina dettaglio centro
*/

/*
    CARD PERSONALIZZATE CON EFFETTI
    CSS: Box-shadow e transizioni per design moderno
*/
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Ombra sottile default */
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.2s ease-in-out; /* Transizione smooth per hover */
}

/*
    EFFETTI HOVER CARD
    CSS: Lift effect con ombra e transform
*/
.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra più pronunciata */
    transform: translateY(-2px); /* Movimento verso l'alto */
}

/* === BADGE PERSONALIZZATI === 
   CSS: Dimensioni ottimizzate per leggibilità
*/
.badge.fs-6 {
    font-size: 0.875rem !important; /* Override Bootstrap */
}

/* === PULSANTI CONTATTO CON TESTO GARANTITO === 
   CSS: Assicura colore testo bianco per tutti gli stati
*/

/*
    PULSANTE SUCCESSO (TELEFONO)
    CSS: Verde per azioni positive/chiamate
*/
.btn.btn-success {
    background: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important; /* Forza testo bianco */
}

/*
    PULSANTE INFO (EMAIL)
    CSS: Azzurro per informazioni/comunicazioni
*/
.btn.btn-info {
    background: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #ffffff !important;
}

/*
    PULSANTE PRIMARY (MAPPE)
    CSS: Blu per azioni principali
*/
.btn.btn-primary {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #ffffff !important;
}

/* === HOVER EFFECTS CON TESTO SEMPRE BIANCO === 
   CSS: Mantiene leggibilità durante interazioni
*/
.btn.btn-success:hover {
    background: #218838 !important; /* Verde più scuro */
    border-color: #1e7e34 !important;
    color: #ffffff !important; /* Forza testo bianco anche su hover */
}

.btn.btn-info:hover {
    background: #138496 !important; /* Azzurro più scuro */
    border-color: #117a8b !important;
    color: #ffffff !important;
}

.btn.btn-primary:hover {
    background: #0069d9 !important; /* Blu più scuro */
    border-color: #0062cc !important;
    color: #ffffff !important;
}

/* === RESPONSIVE DESIGN PER MOBILE === 
   CSS: Ottimizzazioni layout per dispositivi touch
*/
@media (max-width: 768px) {
    /*
        PULSANTI AZIONE MOBILE
        CSS: Layout ottimizzato per touch screen
    */
    .d-flex.flex-wrap.gap-2 {
        justify-content: stretch !important; /* Estendi pulsanti */
    }
    
    .d-flex.flex-wrap.gap-2 > * {
        flex: 1 !important; /* Distribuzione uniforme spazio */
        min-width: 120px; /* Larghezza minima per tap area */
    }
    
    /*
        GRUPPO PULSANTI VERTICALE SU MOBILE
        CSS: Spacing verticale per usabilità mobile
    */
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
}

/* === ANIMAZIONI HOVER PER TECNICI === 
   CSS: Feedback visivo su card tecnici
*/
.border.rounded.p-3:hover {
    background-color: #f8f9fa; /* Background leggero su hover */
    transition: background-color 0.2s ease;
}

/* === ICONE COLORATE PER CONTATTI === 
   CSS: Colori semantici per tipo di contatto
*/
.bi-telephone {
    color: #28a745 !important; /* Verde per telefono */
}

.bi-envelope {
    color: #17a2b8 !important; /* Azzurro per email */
}

.bi-map {
    color: #007bff !important; /* Blu per mappe */
}

/* === ALERT PERSONALIZZATO === 
   CSS: Design gradient per alert informativo
*/
.alert-info {
    background: linear-gradient(135deg, #cce7ff, #e3f2fd); /* Gradient azzurro */
    border: 1px solid #007bff;
    border-radius: 8px;
}

/* === OTTIMIZZAZIONI PERFORMANCE === 
   CSS: GPU acceleration per elementi animati
*/
.card-custom,
.btn {
    will-change: transform, box-shadow; /* Hint per ottimizzazioni browser */
}

/* === ACCESSIBILITÀ === 
   CSS: Miglioramenti per navigazione da tastiera
*/
.btn:focus-visible,
.card:focus-within {
    outline: 2px solid #007bff; /* Outline visibile per focus */
    outline-offset: 2px;
}

/* === PRINT STYLES === 
   CSS: Ottimizzazioni per stampa informazioni centro
*/
@media print {
    .btn,
    .card-footer {
        display: none !important; /* Rimuovi elementi interattivi */
    }
    
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid; /* Evita rottura card tra pagine */
    }
    
    .alert {
        -webkit-print-color-adjust: exact; /* Mantieni colori alert */
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
    
    .border.rounded {
        border-color: #4a5568 !important;
        background-color: #1a202c;
    }
}

/* === HIGH CONTRAST SUPPORT === 
   CSS: Accessibilità per utenti con necessità alto contrasto
*/
@media (prefers-contrast: high) {
    .card-custom,
    .btn {
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
    .btn,
    * {
        transition: none !important; /* Disabilita tutte le transizioni */
    }
}
</style>
@endpush