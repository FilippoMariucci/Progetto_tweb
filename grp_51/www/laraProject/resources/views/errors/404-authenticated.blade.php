{{-- 
    ===================================================================
    PAGINA 404 PERSONALIZZATA PER UTENTI AUTENTICATI - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/errors/404-authenticated.blade.php
    
    DESCRIZIONE:
    Vista personalizzata per errore 404 (Pagina non trovata) specificamente 
    progettata per utenti autenticati nel sistema di assistenza tecnica.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Pagina errore 404 user-friendly per utenti loggati
    - Navigazione personalizzata basata su ruolo utente
    - Suggerimenti di percorsi alternativi dinamici
    - Links diretti a dashboard appropriate per livello accesso
    - Design responsive e accessibile
    - Integrazione con sistema autorizzazioni multi-livello
    - Informazioni contatto per supporto tecnico
    ===================================================================
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends eredita il layout principale dell'applicazione
    La pagina 404 mantiene coerenza con design system generale
--}}
@extends('layouts.app')

{{-- 
    DEFINIZIONE TITOLO PAGINA
    Blade PHP: @section('title') imposta il titolo nel tag <title> HTML
    SEO: Titolo descrittivo per identificazione errore nei motori ricerca
--}}
@section('title', 'Pagina non trovata')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto inserito nel layout
--}}
@section('content')
{{-- 
    CONTAINER BOOTSTRAP CENTRATO
    Bootstrap: container per layout responsive centrato
    CSS: mt-5 per margine superiore adeguato
--}}
<div class="container mt-5">
    <div class="row justify-content-center">
        {{-- 
            COLONNA RESPONSIVE PRINCIPALE
            Bootstrap: col-lg-8 col-md-10 per larghezza ottimale su diversi dispositivi
            Desktop: 66%, Tablet: 83%, Mobile: 100%
        --}}
        <div class="col-lg-8 col-md-10">
            
            {{-- === HEADER CON ICONA E MESSAGGIO PRINCIPALE === 
                HTML: Sezione visiva principale per comunicare l'errore
                UX: Design amichevole per ridurre frustrazione utente
            --}}
            <div class="text-center mb-5">
                <div class="mb-4">
                    {{-- 
                        ICONA AVVISO GRANDE
                        Bootstrap Icons: bi-exclamation-triangle per errore non critico
                        CSS: Inline style per dimensione custom 4rem
                        Design: Colore warning (giallo) per errore recuperabile
                    --}}
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>
                {{-- 
                    CODICE ERRORE 404 PROMINENTE
                    Bootstrap: display-4 per tipografia grande e impattante
                    UX: Numero errore riconoscibile universalmente
                --}}
                <h1 class="display-4 text-primary mb-3">404</h1>
                {{-- 
                    TITOLO DESCRITTIVO ERRORE
                    HTML: h2 con classe h3 per gerarchia visiva appropriata
                --}}
                <h2 class="h3 text-muted mb-4">Pagina non trovata</h2>
                {{-- 
                    SPIEGAZIONE USER-FRIENDLY
                    Bootstrap: lead class per testo introduttivo prominente
                    UX: Linguaggio semplice e non tecnico per tutti gli utenti
                --}}
                <p class="lead text-muted">
                    La pagina che stai cercando non esiste o è stata spostata.
                </p>
                
                {{-- 
                    MESSAGGIO PERSONALIZZATO PER UTENTE AUTENTICATO
                    Laravel: Controllo condizionale esistenza variabile $user
                    UX: Personalizzazione per migliorare esperienza utente loggato
                --}}
                @if(isset($user))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Ciao <strong>{{ $user->nome_completo }}</strong>, 
                        puoi tornare alla tua dashboard o utilizzare uno dei link qui sotto.
                    </div>
                @endif
            </div>

            {{-- === SUGGERIMENTI DI NAVIGAZIONE PER UTENTI AUTENTICATI === 
                HTML: Card con opzioni navigazione personalizzate
                UX: Trasforma errore in opportunità di re-engagement
            --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-compass me-2"></i>
                        Dove vuoi andare?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- 
                            ROUTE SUGGERITE DINAMICHE
                            Laravel: Controllo esistenza array $suggested_routes dal controller
                            Permette personalizzazione suggerimenti basata su contesto errore
                        --}}
                        @if(isset($suggested_routes))
                            {{-- 
                                ITERAZIONE ATTRAVERSO ROUTE SUGGERITE
                                Blade PHP: @foreach attraverso array associativo nome=>URL
                                Laravel: $loop variabile automatica per controllo iterazione
                            --}}
                            @foreach($suggested_routes as $name => $url)
                                <div class="col-md-6 mb-3">
                                    <a href="{{ $url }}" class="btn btn-outline-primary btn-lg w-100">
                                        {{-- 
                                            ICONE DINAMICHE BASATE SU POSIZIONE LOOP
                                            Laravel: $loop->first, $loop->last per controllo posizione
                                            Bootstrap Icons: Icone semantiche per tipo di collegamento
                                        --}}
                                        <i class="bi bi-{{ $loop->first ? 'speedometer2' : ($loop->last ? 'person' : 'box') }} me-2"></i>
                                        {{ $name }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    {{-- === LINK AGGIUNTIVI PER UTENTI AUTENTICATI === 
                        HTML: Sezione separata per link standard sempre disponibili
                    --}}
                    <hr class="my-4">
                    
                    <div class="row">
                        {{-- 
                            LINK HOMEPAGE UNIVERSALE
                            Laravel: route('home') per ritorno alla pagina principale
                            UX: Sempre presente come "rifugio sicuro"
                        --}}
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-house me-2"></i>
                                Homepage
                            </a>
                        </div>
                        
                        {{-- 
                            SEZIONE LINK BASATI SU AUTORIZZAZIONI
                            Laravel: @auth per controllo stato autenticazione
                            Sistema: Links dinamici basati su livello accesso utente
                        --}}
                        @auth
                            {{-- 
                                LINK TECNICI (LIVELLO 2)
                                Laravel: Auth::user()->isTecnico() metodo personalizzato model User
                                Route: Accesso catalogo prodotti tecnico
                            --}}
                            @if(Auth::user()->isTecnico())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info w-100">
                                        <i class="bi bi-tools me-2"></i>
                                        Prodotti Tecnici
                                    </a>
                                </div>
                            @endif
                            
                            {{-- 
                                LINK STAFF (LIVELLO 3)
                                Laravel: isStaff() per controllo ruolo staff aziendale
                                Route: Dashboard staff per gestione avanzata
                            --}}
                            @if(Auth::user()->isStaff())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-gear me-2"></i>
                                        Dashboard Staff
                                    </a>
                                </div>
                            @endif
                            
                            {{-- 
                                LINK AMMINISTRATORI (LIVELLO 4)
                                Laravel: isAdmin() per controllo ruolo amministratore
                                Route: Panel admin per controllo completo sistema
                            --}}
                            @if(Auth::user()->isAdmin())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-shield me-2"></i>
                                        Admin Panel
                                    </a>
                                </div>
                            @endif
                        @endauth
                        
                        {{-- 
                            PULSANTE TORNA INDIETRO
                            JavaScript: history.back() per navigazione browser nativa
                            UX: Permette ritorno alla pagina precedente facilmente
                        --}}
                        <div class="col-md-4 mb-3">
                            <button onclick="history.back()" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left me-2"></i>
                                Torna Indietro
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === INFORMAZIONI DI CONTATTO PER SUPPORTO === 
                HTML: Footer informativo con link assistenza
                UX: Opzione di escalation per problemi persistenti
            --}}
            <div class="text-center mt-5">
                <p class="text-muted">
                    <small>
                        Se pensi che questo sia un errore, 
                        {{-- 
                            LINK CONTATTI
                            Laravel: route('contatti') per pagina supporto
                            UX: Escalation path per problemi tecnici
                        --}}
                        <a href="{{ route('contatti') }}" class="text-decoration-none">contatta l'assistenza</a>
                        o torna alla 
                        {{-- LINK HOMEPAGE ALTERNATIVO --}}
                        <a href="{{ route('home') }}" class="text-decoration-none">homepage</a>.
                    </small>
                </p>
            </div>

        </div>
    </div>
</div>

{{-- === SEZIONE STILI CSS PERSONALIZZATI === 
    CSS: Styling specifico per migliorare UX della pagina errore
--}}
<style>
/*
    TRANSIZIONI SMOOTH PER PULSANTI
    CSS: Transizione uniforme per tutti gli elementi button
    UX: Feedback visivo su interazioni utente
*/
.btn {
    transition: all 0.3s ease;
}

/*
    EFFETTO HOVER PULSANTI
    CSS: Transform e box-shadow per feedback tattile
    UX: Lift effect per indicare interattività
*/
.btn:hover {
    transform: translateY(-2px); /* Movimento verso l'alto */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Ombra per profondità */
}

/*
    CARD CON BORDI ARROTONDATI
    CSS: Design moderno con border-radius pronunciato
*/
.card {
    border: none; /* Rimuove bordo default Bootstrap */
    border-radius: 15px;
}

/*
    HEADER CARD COORDINATO
    CSS: Border-radius che si coordina con card principale
*/
.card-header {
    border-radius: 15px 15px 0 0 !important; /* Solo angoli superiori */
}

/*
    ALERT PERSONALIZZATO
    CSS: Styling coerente per messaggio personalizzato
*/
.alert {
    border-radius: 10px;
    border: none; /* Rimuove bordi per design più pulito */
}

/* === RESPONSIVE DESIGN === 
   CSS: Media queries per ottimizzazione mobile
*/
@media (max-width: 768px) {
    /*
        RIDUZIONE DIMENSIONE DISPLAY SU MOBILE
        CSS: Adatta tipografia grande per schermi piccoli
    */
    .display-4 {
        font-size: 2.5rem; /* Ridotto da default Bootstrap */
    }
    
    /*
        OTTIMIZZAZIONE PULSANTI GRANDI SU MOBILE
        CSS: Riduce padding per migliore usabilità touch
    */
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem;
    }
}

/* === ANIMAZIONI AGGIUNTIVE === 
   CSS: Micro-interazioni per migliorare perceived performance
*/

/*
    ANIMAZIONE ENTRATA ICONA
    CSS: Keyframe animation per icona principale
*/
@keyframes iconBounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.bi-exclamation-triangle {
    animation: iconBounce 2s ease-in-out; /* Applica animazione una volta */
}

/* === ACCESSIBILITÀ === 
   CSS: Miglioramenti per screen reader e navigazione tastiera
*/

/*
    FOCUS VISIBILE PER NAVIGAZIONE TASTIERA
    CSS: Outline personalizzato per elementi focusabili
*/
.btn:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/*
    HIGH CONTRAST SUPPORT
    CSS: Accessibilità per utenti con necessità alto contrasto
*/
@media (prefers-contrast: high) {
    .btn {
        border-width: 2px !important;
    }
    
    .card {
        border: 2px solid #000 !important;
    }
}

/* === REDUCED MOTION SUPPORT === 
   CSS: Accessibilità per utenti sensibili alle animazioni
*/
@media (prefers-reduced-motion: reduce) {
    .btn,
    .bi-exclamation-triangle {
        transition: none !important;
        animation: none !important;
    }
}

/* === DARK MODE SUPPORT === 
   CSS: Supporto automatico tema scuro
*/
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #2d3748;
        color: #fff;
    }
    
    .text-muted {
        color: #a0aec0 !important;
    }
}

/* === PRINT STYLES === 
   CSS: Ottimizzazioni per eventuale stampa
*/
@media print {
    .btn {
        display: none !important; /* Rimuovi elementi interattivi */
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>
{{-- FINE SEZIONE CONTENUTO --}}
@endsection