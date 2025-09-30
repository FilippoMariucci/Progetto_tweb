{{--
    ============================================================================
    LINGUAGGIO: Blade (Laravel Template Engine) - PHP + HTML
    ============================================================================
    
    FILE: tecnico-dashboard.blade.php
    PERCORSO: resources/views/tecnico/dashboard.blade.php
    
    DESCRIZIONE:
    Dashboard principale per utenti con ruolo "Tecnico" (livello_accesso >= 2).
    Fornisce accesso completo al catalogo prodotti con visualizzazione di 
    malfunzionamenti e soluzioni tecniche.
    
    ROUTE ASSOCIATA:
    - Nome: tecnico.dashboard
    - Metodo HTTP: GET
    - URL: /tecnico/dashboard
    - Controller: AuthController@tecnicoDashboard
    
    MIDDLEWARE APPLICATI:
    - auth: Verifica autenticazione utente
    - check.level:2: Verifica livello accesso minimo 2 (Tecnico)
    
    FUNZIONALITÀ PRINCIPALI:
    1. Panoramica generale problemi e statistiche sistema
    2. Ricerca rapida prodotti con supporto wildcard (*)
    3. Ricerca malfunzionamenti per descrizione
    4. Accesso schede complete prodotti con malfunzionamenti
    5. Visualizzazione prodotti critici (priorità alta)
    6. Storico malfunzionamenti recenti
    7. Strumenti tecnici rapidi (catalogo, centri, interventi)
    
    VARIABILI RICEVUTE DAL CONTROLLER:
    - $stats: Array con statistiche (total_prodotti, total_malfunzionamenti, etc.)
    - $prodotti_critici: Collection prodotti con malfunzionamenti critici
    - $malfunzionamenti_recenti: Collection ultimi malfunzionamenti segnalati
    
    LAYOUT UTILIZZATO:
    - layouts.app: Layout principale applicazione con navbar e footer
--}}

{{-- 
    DIRETTIVA BLADE: @extends
    LINGUAGGIO: Blade
    
    Estende il layout principale dell'applicazione.
    Eredita struttura HTML, navbar, sidebar e footer da layouts/app.blade.php
--}}
@extends('layouts.app')

{{-- 
    DIRETTIVA BLADE: @section('title')
    LINGUAGGIO: Blade
    
    Definisce il contenuto della sezione 'title' del layout padre.
    Imposta il titolo della pagina che apparirà nel tag <title> dell'HTML.
    Visibile nella tab del browser.
--}}
@section('title', 'Dashboard Tecnico')

{{-- 
    DIRETTIVA BLADE: @section('content')
    LINGUAGGIO: Blade
    
    Inizia la definizione della sezione 'content' del layout.
    Tutto il codice fino a @endsection verrà inserito nel punto 
    dove il layout padre ha @yield('content').
--}}
@section('content')

{{-- 
    ELEMENTO HTML: <div class="container mt-4">
    LINGUAGGIO: HTML + Bootstrap CSS
    
    Container principale Bootstrap per contenuto responsive.
    - container: classe Bootstrap per layout responsive con margini automatici
    - mt-4: margin-top di 1.5rem (spacing Bootstrap)
--}}
<div class="container mt-4">
    
    {{-- 
        ELEMENTO HTML: <div class="row">
        LINGUAGGIO: HTML + Bootstrap CSS
        
        Sistema a griglia Bootstrap per layout responsive.
        Divide lo spazio in 12 colonne virtuali.
    --}}
    <div class="row">
        
        {{-- 
            ELEMENTO HTML: <div class="col-12">
            LINGUAGGIO: HTML + Bootstrap CSS
            
            Colonna che occupa tutte le 12 colonne disponibili (100% larghezza).
            Utilizzata per header e informazioni a tutta larghezza.
        --}}
        <div class="col-12">
            
            {{-- 
                COMMENTO BLADE
                LINGUAGGIO: Blade
                
                I commenti Blade {{-- --}} non vengono renderizzati nel HTML finale,
                a differenza dei commenti HTML <!-- --> che sono visibili nel codice sorgente.
            --}}
            {{-- Header personalizzato per il tecnico --}}
            
            {{-- 
                ELEMENTO HTML: <h1 class="h2 mb-4">
                LINGUAGGIO: HTML + Bootstrap CSS
                
                Heading principale della dashboard.
                - h1: tag semantico HTML per intestazione principale
                - h2: classe Bootstrap per dimensione visiva come h2 (più piccolo di h1)
                - mb-4: margin-bottom di 1.5rem
            --}}
            <h1 class="h2 mb-4">
                {{-- 
                    ELEMENTO HTML: <i class="bi bi-person-gear text-info me-2"></i>
                    LINGUAGGIO: HTML + Bootstrap Icons + Bootstrap CSS
                    
                    Icona Bootstrap Icons (bi bi-person-gear).
                    - bi: prefisso Bootstrap Icons
                    - bi-person-gear: icona specifica (persona con ingranaggio)
                    - text-info: colore testo azzurro Bootstrap
                    - me-2: margin-end (destra in LTR) di 0.5rem
                --}}
                <i class="bi bi-person-gear text-info me-2"></i>
                Pannello Tecnico
            </h1>
            
            {{-- Benvenuto personalizzato per tecnico --}}
            
            {{-- 
                ELEMENTO HTML: <div class="alert alert-info border-start border-info border-4">
                LINGUAGGIO: HTML + Bootstrap CSS
                
                Alert Bootstrap per messaggio di benvenuto.
                - alert: classe base per messaggi di notifica
                - alert-info: stile informativo (sfondo azzurro chiaro)
                - border-start: bordo sul lato sinistro
                - border-info: colore bordo azzurro
                - border-4: spessore bordo 4px
            --}}
            <div class="alert alert-info border-start border-info border-4">
                
                {{-- 
                    ELEMENTO HTML: <div class="d-flex align-items-center">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Container flexbox per allineamento orizzontale.
                    - d-flex: display flex
                    - align-items-center: allinea verticalmente al centro
                --}}
                <div class="d-flex align-items-center">
                    
                    {{-- 
                        ELEMENTO HTML: <i class="bi bi-tools display-6 text-info me-3"></i>
                        LINGUAGGIO: HTML + Bootstrap Icons + Bootstrap CSS
                        
                        Icona strumenti decorativa.
                        - display-6: dimensione heading 6 (grande)
                        - me-3: margin-end di 1rem
                    --}}
                    <i class="bi bi-tools display-6 text-info me-3"></i>
                    
                    <div>
                        {{-- 
                            ELEMENTO HTML: <h4 class="alert-heading mb-1">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Sottotitolo dell'alert.
                            - alert-heading: stile predefinito Bootstrap per heading in alert
                            - mb-1: margin-bottom di 0.25rem
                        --}}
                        <h4 class="alert-heading mb-1">
                            {{-- 
                                DIRETTIVA BLADE: {{ auth()->user()->nome_completo }}
                                LINGUAGGIO: Blade (PHP)
                                
                                OUTPUT ESCAPATO: Stampa nome completo utente autenticato.
                                
                                FUNZIONAMENTO:
                                1. auth(): helper Laravel che restituisce istanza Auth facade
                                2. ->user(): metodo che restituisce utente autenticato corrente
                                3. ->nome_completo: accessor/attributo del modello User
                                4. {{ }}: sintassi Blade per echo escapato (htmlspecialchars)
                                   equivale a: <?php echo e($value); ?>
                                
                                SICUREZZA: Le doppie graffe {{ }} eseguono automaticamente
                                l'escape HTML prevenendo XSS (Cross-Site Scripting).
                            --}}
                            Benvenuto, {{ auth()->user()->nome_completo }}!
                        </h4>
                        
                        <p class="mb-0">
                            {{-- 
                                ELEMENTO HTML: <span class="badge bg-info">Tecnico Specializzato</span>
                                LINGUAGGIO: HTML + Bootstrap CSS
                                
                                Badge per visualizzare ruolo utente.
                                - badge: classe base Bootstrap per etichette
                                - bg-info: background azzurro
                            --}}
                            <span class="badge bg-info">Tecnico Specializzato</span>
                            
                            {{-- 
                                DIRETTIVA BLADE: @if(condizione)
                                LINGUAGGIO: Blade (PHP)
                                
                                Struttura condizionale Blade.
                                Esegue il blocco solo se la condizione è vera.
                                
                                FUNZIONAMENTO:
                                1. Verifica se auth()->user()->centro_assistenza esiste
                                2. Se TRUE: renderizza il contenuto tra @if e @endif
                                3. Se FALSE: salta il blocco
                                
                                EQUIVALENTE PHP:
                                <?php if(auth()->user()->centro_assistenza): ?>
                                    // contenuto
                                <?php endif; ?>
                            --}}
                            @if(auth()->user()->centro_assistenza)
                                {{-- 
                                    ELEMENTO HTML: <span class="badge bg-light text-dark ms-1">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Badge per nome centro assistenza.
                                    - bg-light: background grigio chiaro
                                    - text-dark: testo scuro
                                    - ms-1: margin-start (sinistra in LTR) di 0.25rem
                                --}}
                                <span class="badge bg-light text-dark ms-1">
                                    {{-- 
                                        OUTPUT ESCAPATO: Stampa nome centro assistenza
                                        dell'utente autenticato.
                                    --}}
                                    {{ auth()->user()->centro_assistenza }}
                                </span>
                            {{-- 
                                DIRETTIVA BLADE: @endif
                                LINGUAGGIO: Blade
                                
                                Chiude il blocco condizionale @if.
                            --}}
                            @endif
                        </p>
                        
                        {{-- 
                            ELEMENTO HTML: <small class="text-muted">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Testo piccolo per note aggiuntive.
                            - small: tag HTML per testo ridotto
                            - text-muted: colore grigio tenue Bootstrap
                        --}}
                        <small class="text-muted">
                            Accesso completo al catalogo con malfunzionamenti e soluzioni tecniche
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === RICERCA RAPIDA - POSIZIONATA SOPRA STRUMENTI === --}}
    
    {{-- 
        ELEMENTO HTML: <div class="row mb-4">
        LINGUAGGIO: HTML + Bootstrap CSS
        
        Nuova riga per sezione ricerca rapida.
        - mb-4: margin-bottom di 1.5rem per spaziatura dalla sezione successiva
    --}}
    <div class="row mb-4">
        <div class="col-12">
            
            {{-- 
                ELEMENTO HTML: <div class="card card-custom">
                LINGUAGGIO: HTML + Bootstrap CSS + CSS Personalizzato
                
                Card Bootstrap per contenitore visuale.
                - card: classe base Bootstrap per componente carta
                - card-custom: classe CSS personalizzata (definita in @push('styles'))
            --}}
            <div class="card card-custom">
                
                {{-- 
                    ELEMENTO HTML: <div class="card-header bg-light">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Intestazione della card.
                    - card-header: classe Bootstrap per header della card
                    - bg-light: background grigio molto chiaro
                --}}
                <div class="card-header bg-light">
                    
                    {{-- 
                        ELEMENTO HTML: <h5 class="mb-0">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Titolo sezione ricerca.
                        - h5: heading livello 5
                        - mb-0: margin-bottom zero (rimuove margine inferiore predefinito)
                    --}}
                    <h5 class="mb-0">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida
                        
                        {{-- 
                            ELEMENTO HTML: <small class="text-muted">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Testo piccolo per istruzioni d'uso wildcard.
                            Spiega all'utente come usare il carattere "*" per ricerche parziali.
                        --}}
                        <small class="text-muted">(supporto wildcard "*" - es: "lav*" per lavatrici, lavastoviglie...)</small>
                    </h5>
                </div>
                
                {{-- 
                    ELEMENTO HTML: <div class="card-body">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Corpo della card contenente i form di ricerca.
                --}}
                <div class="card-body">
                    
                    {{-- 
                        ELEMENTO HTML: <div class="row g-3">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Riga con gutter spacing.
                        - g-3: gap di 1rem tra le colonne (gutter)
                    --}}
                    <div class="row g-3">
                        
                        {{-- Ricerca prodotti --}}
                        
                        {{-- 
                            ELEMENTO HTML: <div class="col-md-6">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Colonna responsive.
                            - col-md-6: su schermi medi (≥768px) occupa 6/12 colonne (50%)
                            - su schermi piccoli (<768px) occupa 12/12 colonne (100%)
                        --}}
                        <div class="col-md-6">
                            
                            {{-- 
                                ELEMENTO HTML: <form method="GET" action="{{ route(...) }}" class="d-flex">
                                LINGUAGGIO: HTML + Blade
                                
                                Form per ricerca prodotti.
                                
                                ATTRIBUTI:
                                - method="GET": metodo HTTP GET (parametri in URL query string)
                                - action: URL destinazione form (generato dinamicamente)
                                - d-flex: display flexbox per layout orizzontale
                                
                                DIRETTIVA BLADE: {{ route('prodotti.completo.ricerca') }}
                                Genera URL della route nominata 'prodotti.completo.ricerca'.
                                
                                FUNZIONAMENTO:
                                1. route(): helper Laravel per generare URL da nome route
                                2. Cerca nel file routes/web.php la route con name('prodotti.completo.ricerca')
                                3. Restituisce URL completo (es: http://sito.it/prodotti/ricerca)
                                4. {{ }}: output escapato in Blade
                            --}}
                            <form method="GET" action="{{ route('prodotti.completo.ricerca') }}" class="d-flex">
                                
                                {{-- 
                                    ELEMENTO HTML: <input type="text" ... />
                                    LINGUAGGIO: HTML + Blade
                                    
                                    Campo di input per la ricerca prodotti.
                                    
                                    ATTRIBUTI:
                                    - type="text": campo di testo semplice
                                    - class="form-control me-2": 
                                        * form-control: stile Bootstrap per input
                                        * me-2: margin-end di 0.5rem
                                    - name="search": nome parametro inviato al server (?search=valore)
                                    - placeholder: testo suggerimento quando input vuoto
                                    - value="{{ request('search') }}": valore pre-compilato
                                    - id="searchProdotti": identificatore univoco per JavaScript
                                    
                                    DIRETTIVA BLADE: {{ request('search') }}
                                    Recupera valore parametro 'search' dalla richiesta HTTP corrente.
                                    
                                    FUNZIONAMENTO request():
                                    1. request(): helper Laravel che restituisce istanza Request corrente
                                    2. ->input('search'): metodo alternativo
                                    3. request('search'): shorthand per request()->input('search')
                                    4. Restituisce valore se presente, null se assente
                                    5. Se utente ha cercato "lav*", questo campo mostrerà "lav*"
                                --}}
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="search" 
                                       placeholder="Cerca prodotti: lav*, frigo*, condizionatore..."
                                       value="{{ request('search') }}"
                                       id="searchProdotti">
                                
                                {{-- 
                                    ELEMENTO HTML: <button type="submit" class="btn btn-primary">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Pulsante per inviare il form.
                                    
                                    ATTRIBUTI:
                                    - type="submit": invia il form quando cliccato
                                    - btn: classe base Bootstrap per pulsanti
                                    - btn-primary: stile pulsante blu primario
                                --}}
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                            
                            {{-- 
                                ELEMENTO HTML: <div class="form-text">
                                LINGUAGGIO: HTML + Bootstrap CSS
                                
                                Testo di aiuto per il form.
                                - form-text: classe Bootstrap per testo esplicativo sotto input
                                - Solitamente in grigio e dimensione ridotta
                            --}}
                            <div class="form-text">
                                Usa "*" alla fine per ricerca parziale
                            </div>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        
                        {{-- 
                            ELEMENTO HTML: <div class="col-md-6">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Seconda colonna per ricerca malfunzionamenti.
                            Stessa larghezza della colonna precedente (50% su schermi medi).
                        --}}
                        <div class="col-md-6">
                            
                            {{-- 
                                ELEMENTO HTML: <form method="GET" action="...">
                                LINGUAGGIO: HTML + Blade
                                
                                Form per ricerca malfunzionamenti.
                                Simile al form precedente ma con action diversa.
                                
                                NOTA: name="q" invece di "search" per distinguere
                                i due tipi di ricerca nelle route.
                            --}}
                            <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}" class="d-flex">
                                <input type="text" 
                                       class="form-control me-2" 
                                       name="q" 
                                       placeholder="Cerca problemi: non si accende, perdita..."
                                       value="{{ request('q') }}"
                                       id="searchMalfunzionamenti">
                                
                                {{-- 
                                    ELEMENTO HTML: <button type="submit" class="btn btn-warning">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Pulsante submit con stile warning (giallo).
                                    - btn-warning: stile pulsante giallo/arancione
                                --}}
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-tools"></i>
                                </button>
                            </form>
                            
                            <div class="form-text">
                                Ricerca nella descrizione dei problemi
                            </div>
                        </div>
                    </div>
                    
                    {{-- Suggerimenti ricerca --}}
                    
                    {{-- 
                        ELEMENTO HTML: <div class="mt-3">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Sezione suggerimenti ricerca.
                        - mt-3: margin-top di 1rem per distanziare dai form sopra
                    --}}
                    <div class="mt-3">
                        
                        {{-- 
                            ELEMENTO HTML: <small class="text-muted">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Contenitore per link suggerimenti.
                        --}}
                        <small class="text-muted">
                            <strong>Suggerimenti:</strong>
                            
                            {{-- 
                                ELEMENTO HTML: <a href="..." class="badge ...">
                                LINGUAGGIO: HTML + Bootstrap CSS + Blade
                                
                                Link cliccabile stile badge per ricerca pre-compilata.
                                
                                FUNZIONAMENTO:
                                1. href="{{ route('prodotti.completo.ricerca') }}?search=lav*"
                                   - route(): genera URL base della ricerca prodotti
                                   - ?search=lav*: query string aggiunta manualmente
                                   - Risultato: http://sito.it/prodotti/ricerca?search=lav*
                                2. Quando utente clicca, viene reindirizzato alla ricerca
                                   con parametro search già impostato a "lav*"
                                3. Il controller riceverà $request->input('search') = "lav*"
                                
                                CLASSI CSS:
                                - badge: stile etichetta Bootstrap
                                - bg-light: background grigio chiaro
                                - text-dark: testo scuro
                                - me-1: margin-end di 0.25rem
                            --}}
                            <a href="{{ route('prodotti.completo.ricerca') }}?search=lav*" class="badge bg-light text-dark me-1">lav*</a>
                            <a href="{{ route('prodotti.completo.ricerca') }}?search=frigo*" class="badge bg-light text-dark me-1">frigo*</a>
                            
                            {{-- 
                                Link per ricerca malfunzionamenti.
                                NOTA: usa ?q= invece di ?search= perché il form malfunzionamenti
                                usa name="q" per il campo di ricerca.
                            --}}
                            <a href="{{ route('malfunzionamenti.ricerca') }}?q=non+si+accende" class="badge bg-light text-dark me-1">non si accende</a>
                            <a href="{{ route('malfunzionamenti.ricerca') }}?q=perdita" class="badge bg-light text-dark me-1">perdita</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE: STRUMENTI E STATISTICHE === --}}
    
    {{-- 
        ELEMENTO HTML: <div class="row g-4 mb-4">
        LINGUAGGIO: HTML + Bootstrap CSS
        
        Riga per sezione strumenti tecnici.
        - g-4: gap di 1.5rem tra colonne
        - mb-4: margin-bottom di 1.5rem
    --}}
    <div class="row g-4 mb-4">
        
        {{-- === STRUMENTI TECNICI - LAYOUT LINEARE === --}}
        
        <div class="col-12">
            <div class="card card-custom">
                
                {{-- 
                    ELEMENTO HTML: <div class="card-header bg-info text-white">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Header card con sfondo azzurro e testo bianco.
                    - bg-info: background azzurro Bootstrap
                    - text-white: testo bianco
                --}}
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-wrench-adjustable me-2"></i>
                        Strumenti Tecnici
                    </h5>
                </div>
                
                <div class="card-body">
                    
                    {{-- 
                        ELEMENTO HTML: <div class="row g-3">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Riga con gap di 1rem per contenere pulsanti strumenti.
                    --}}
                    <div class="row g-3">
                        
                        {{-- Catalogo completo --}}
                        
                        {{-- 
                            ELEMENTO HTML: <div class="col-lg-2 col-md-4 col-sm-6">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Colonna responsive multi-breakpoint.
                            
                            FUNZIONAMENTO RESPONSIVE:
                            - col-lg-2: su schermi large (≥992px) occupa 2/12 colonne (16.66%)
                            - col-md-4: su schermi medi (≥768px) occupa 4/12 colonne (33.33%)
                            - col-sm-6: su schermi small (≥576px) occupa 6/12 colonne (50%)
                            - su schermi <576px: occupa 12/12 colonne (100%)
                            
                            RISULTATO:
                            - Desktop (>992px): 6 pulsanti per riga
                            - Tablet (768-991px): 3 pulsanti per riga
                            - Mobile landscape (576-767px): 2 pulsanti per riga
                            - Mobile portrait (<576px): 1 pulsante per riga
                        --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            
                            {{-- 
                                ELEMENTO HTML: <a href="..." class="btn ...">
                                LINGUAGGIO: HTML + Bootstrap CSS + Blade
                                
                                Link stile pulsante per accedere al catalogo completo.
                                
                                ATTRIBUTI/CLASSI:
                                - href="{{ route('prodotti.completo.index') }}": URL generato
                                  dalla route nominata 'prodotti.completo.index'
                                - btn: classe base Bootstrap per pulsanti
                                - btn-info: stile azzurro
                                - btn-lg: dimensione large (più grande)
                                - w-100: width 100% del contenitore padre
                                - h-100: height 100% del contenitore padre
                                
                                NOTA: w-100 e h-100 fanno sì che tutti i pulsanti
                                abbiano la stessa altezza anche se il testo è diverso.
                            --}}
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-info btn-lg w-100 h-100">
                                
                                {{-- 
                                    ELEMENTO HTML: <i class="bi bi-collection display-6 d-block mb-2"></i>
                                    LINGUAGGIO: HTML + Bootstrap CSS + Bootstrap Icons
                                    
                                    Icona decorativa per il pulsante.
                                    
                                    CLASSI:
                                    - bi bi-collection: icona specifica Bootstrap Icons
                                    - display-6: dimensione grande (heading 6 size)
                                    - d-block: display block (va a capo, non inline)
                                    - mb-2: margin-bottom di 0.5rem
                                --}}
                                <i class="bi bi-collection display-6 d-block mb-2"></i>
                                
                                {{-- 
                                    ELEMENTO HTML: <span class="fw-semibold">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Testo del pulsante.
                                    - fw-semibold: font-weight semi-grassetto (600)
                                --}}
                                <span class="fw-semibold">Catalogo Completo</span>
                            </a>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Cerca Soluzioni</span>
                            </a>
                        </div>
                        
                        {{-- Centri assistenza --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('centri.index') }}" class="btn btn-success btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Storico interventi --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            
                            {{-- 
                                LINK: route('tecnico.interventi')
                                
                                Accede alla pagina storico interventi personali del tecnico.
                                Mostra cronologia delle riparazioni effettuate.
                            --}}
                            <a href="{{ route('tecnico.interventi') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-clock-history display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Miei Interventi</span>
                            </a>
                        </div>
                        
                        {{-- Prodotti critici --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            
                            {{-- 
                                LINK: route con query string
                                LINGUAGGIO: HTML + Blade
                                
                                URL con parametro filter nella query string.
                                
                                FUNZIONAMENTO:
                                1. route('prodotti.completo.index'): genera URL base
                                2. ?filter=critici: query string aggiunta manualmente
                                3. Nel controller: $request->input('filter') = 'critici'
                                4. Il controller filtrerà prodotti con malfunzionamenti critici
                            --}}
                            <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-danger btn-lg w-100 h-100">
                                <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Priorità Alta</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche personali --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            
                            {{-- 
                                LINK: route('tecnico.statistiche.view')
                                
                                Accede alla pagina statistiche personali del tecnico.
                                Mostra metriche come: interventi completati, tempo medio, 
                                tasso successo, prodotti più riparati, etc.
                            --}}
                            <a href="{{ route('tecnico.statistiche.view') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Le Mie Stats</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE SISTEMA - LAYOUT LINEARE === --}}
    
    {{-- 
        SEZIONE: Statistiche Sistema
        LINGUAGGIO: HTML + Bootstrap CSS + Blade
        
        Visualizza metriche aggregate del sistema:
        - Totale prodotti in catalogo
        - Totale soluzioni disponibili
        - Problemi critici attivi
        - Centri assistenza attivi
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                
                {{-- === STATISTICHE SISTEMA - STILE COMPATTO COME STAFF === --}}
                
                {{-- 
                    ELEMENTO HTML: <div class="row mb-3 g-2">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Riga per card statistiche compatte.
                    - mb-3: margin-bottom di 1rem
                    - g-2: gap di 0.5rem tra colonne (spacing ridotto)
                --}}
                <div class="row mb-3 g-2">
                    
                    {{-- Card Prodotti Totali --}}
                    
                    {{-- 
                        ELEMENTO HTML: <div class="col-xl-3 col-lg-6">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Colonna responsive per card statistica.
                        
                        BREAKPOINTS:
                        - col-xl-3: su schermi extra-large (≥1200px) occupa 3/12 (25%)
                        - col-lg-6: su schermi large (≥992px) occupa 6/12 (50%)
                        - su schermi <992px: occupa 12/12 (100%)
                        
                        LAYOUT RISULTANTE:
                        - Desktop XL (>1200px): 4 card per riga
                        - Desktop/Tablet (992-1199px): 2 card per riga
                        - Mobile (<992px): 1 card per riga
                    --}}
                    <div class="col-xl-3 col-lg-6">
                        
                        {{-- 
                            ELEMENTO HTML: <div class="card border-0 shadow-sm">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Card Bootstrap con stile personalizzato.
                            - card: classe base Bootstrap
                            - border-0: rimuove bordo predefinito
                            - shadow-sm: ombra piccola per effetto elevazione
                        --}}
                        <div class="card border-0 shadow-sm">
                            
                            {{-- 
                                ELEMENTO HTML: <div class="card-body text-center py-2 px-3">
                                LINGUAGGIO: HTML + Bootstrap CSS
                                
                                Corpo card con padding ridotto e testo centrato.
                                - card-body: classe base Bootstrap per corpo card
                                - text-center: allineamento testo al centro
                                - py-2: padding verticale (top+bottom) di 0.5rem
                                - px-3: padding orizzontale (left+right) di 1rem
                            --}}
                            <div class="card-body text-center py-2 px-3">
                                
                                {{-- 
                                    ELEMENTO HTML: <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                                    LINGUAGGIO: HTML + Bootstrap Icons + Bootstrap CSS
                                    
                                    Icona decorativa per la statistica.
                                    - bi bi-box-seam: icona scatola/pacco
                                    - text-primary: colore blu primario
                                    - fs-3: font-size livello 3 (grande)
                                    - mb-1: margin-bottom di 0.25rem
                                --}}
                                <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                                
                                {{-- 
                                    ELEMENTO HTML: <h5 class="fw-bold mb-0 text-primary">
                                    LINGUAGGIO: HTML + Bootstrap CSS + Blade
                                    
                                    Numero principale della statistica.
                                    - h5: heading livello 5
                                    - fw-bold: font-weight grassetto (700)
                                    - mb-0: margin-bottom zero
                                    - text-primary: colore blu primario
                                --}}
                                <h5 class="fw-bold mb-0 text-primary">
                                    {{-- 
                                        DIRETTIVA BLADE: {{ $stats['total_prodotti'] ?? 0 }}
                                        LINGUAGGIO: Blade (PHP)
                                        
                                        OUTPUT CON NULL COALESCING OPERATOR.
                                        
                                        FUNZIONAMENTO:
                                        1. $stats: array associativo passato dal controller
                                        2. ['total_prodotti']: accede alla chiave 'total_prodotti'
                                        3. ?? 0: operatore PHP null coalescing
                                           - Se $stats['total_prodotti'] esiste e non è null: usa quel valore
                                           - Se non esiste o è null: usa 0 come default
                                        4. {{ }}: output escapato Blade
                                        
                                        ESEMPIO:
                                        - Se $stats = ['total_prodotti' => 150]: stampa "150"
                                        - Se $stats = []: stampa "0"
                                        - Se $stats = ['total_prodotti' => null]: stampa "0"
                                        
                                        EQUIVALENTE PHP:
                                        <?php echo isset($stats['total_prodotti']) && $stats['total_prodotti'] !== null 
                                            ? $stats['total_prodotti'] 
                                            : 0; ?>
                                    --}}
                                    {{ $stats['total_prodotti'] ?? 0 }}
                                </h5>
                                
                                {{-- 
                                    ELEMENTO HTML: <small class="text-muted d-block">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Etichetta descrittiva per la statistica.
                                    - small: tag HTML per testo ridotto
                                    - text-muted: colore grigio tenue
                                    - d-block: display block (va a capo)
                                --}}
                                <small class="text-muted d-block">Prodotti Catalogo</small>
                                
                                {{-- 
                                    ELEMENTO HTML: <small class="badge ...">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Badge informativo aggiuntivo.
                                    - badge: classe base Bootstrap per etichette
                                    - bg-primary: background blu primario
                                    - bg-opacity-10: opacità background al 10% (sfondo molto chiaro)
                                    - text-primary: testo blu primario (più scuro del background)
                                    - mt-1: margin-top di 0.25rem
                                --}}
                                <small class="badge bg-primary bg-opacity-10 text-primary mt-1">
                                    Disponibili
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Card Soluzioni Totali --}}
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-2 px-3">
                                
                                {{-- 
                                    Icona strumenti per rappresentare soluzioni tecniche.
                                    - text-success: colore verde per indicare soluzioni disponibili
                                --}}
                                <i class="bi bi-tools text-success fs-3 mb-1"></i>
                                
                                <h5 class="fw-bold mb-0 text-success">
                                    {{-- 
                                        OUTPUT: Totale malfunzionamenti/soluzioni nel database.
                                        Rappresenta il numero di problemi risolti documentati.
                                    --}}
                                    {{ $stats['total_malfunzionamenti'] ?? 0 }}
                                </h5>
                                
                                <small class="text-muted d-block">Soluzioni Totali</small>
                                
                                <small class="badge bg-success bg-opacity-10 text-success mt-1">
                                    Nel Sistema
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Card Problemi Critici --}}
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-2 px-3">
                                
                                {{-- 
                                    Icona warning per problemi critici.
                                    - text-danger: colore rosso per indicare urgenza
                                --}}
                                <i class="bi bi-exclamation-triangle text-danger fs-3 mb-1"></i>
                                
                                <h5 class="fw-bold mb-0 text-danger">
                                    {{-- 
                                        OUTPUT: Numero malfunzionamenti con gravità 'critica'.
                                        Problemi che richiedono intervento immediato.
                                    --}}
                                    {{ $stats['malfunzionamenti_critici'] ?? 0 }}
                                </h5>
                                
                                <small class="text-muted d-block">Problemi Critici</small>
                                
                                <small class="badge bg-danger bg-opacity-10 text-danger mt-1">
                                    Priorità Alta
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Card Centri Assistenza --}}
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-2 px-3">
                                
                                {{-- 
                                    Icona posizione geografica per centri assistenza.
                                    - text-info: colore azzurro informativo
                                --}}
                                <i class="bi bi-geo-alt text-info fs-3 mb-1"></i>
                                
                                <h5 class="fw-bold mb-0 text-info">
                                    {{-- 
                                        OUTPUT: Numero totale centri assistenza attivi.
                                        Mostra copertura territoriale del servizio.
                                    --}}
                                    {{ $stats['total_centri'] ?? 0 }}
                                </h5>
                                
                                <small class="text-muted d-block">Centri Attivi</small>
                                
                                <small class="badge bg-info bg-opacity-10 text-info mt-1">
                                    Sul Territorio
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI CON PROBLEMI CRITICI === --}}
    
    {{-- 
        DIRETTIVA BLADE: @if(isset(...) && ...->count() > 0)
        LINGUAGGIO: Blade (PHP)
        
        Struttura condizionale complessa.
        
        FUNZIONAMENTO:
        1. isset($prodotti_critici): verifica se variabile è definita
        2. &&: operatore logico AND
        3. $prodotti_critici->count() > 0: verifica se collection non è vuota
        
        DETTAGLIO ->count():
        - $prodotti_critici è una Collection Laravel (Illuminate\Support\Collection)
        - ->count(): metodo che restituisce numero elementi nella collection
        - > 0: verifica che ci sia almeno 1 elemento
        
        NOTA: La sezione viene mostrata SOLO se ci sono prodotti critici.
        Se la collection è vuota o non esiste, tutto il blocco viene saltato.
    --}}
    @if(isset($prodotti_critici) && $prodotti_critici->count() > 0)
    
    <div class="row mt-4">
        <div class="col-12">
            
            {{-- 
                ELEMENTO HTML: <div class="card card-custom border-danger">
                LINGUAGGIO: HTML + Bootstrap CSS
                
                Card con bordo rosso per evidenziare urgenza.
                - border-danger: bordo colore rosso Bootstrap
            --}}
            <div class="card card-custom border-danger">
                
                {{-- 
                    ELEMENTO HTML: <div class="card-header bg-danger text-white">
                    LINGUAGGIO: HTML + Bootstrap CSS
                    
                    Header rosso per attirare l'attenzione.
                    - bg-danger: background rosso
                    - text-white: testo bianco per contrasto
                --}}
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Prodotti con Problemi Critici - Intervento Prioritario
                    </h5>
                </div>
                
                <div class="card-body">
                    
                    {{-- 
                        ELEMENTO HTML: <div class="row g-3">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Griglia per visualizzare card prodotti critici.
                        - g-3: gap di 1rem tra colonne
                    --}}
                    <div class="row g-3">
                        
                        {{-- 
                            DIRETTIVA BLADE: @foreach($collection->take(6) as $item)
                            LINGUAGGIO: Blade (PHP)
                            
                            Loop attraverso collection limitata.
                            
                            FUNZIONAMENTO:
                            1. $prodotti_critici: Collection Laravel di prodotti
                            2. ->take(6): metodo Collection che limita a primi 6 elementi
                               - Se ci sono 10 prodotti critici, prende solo i primi 6
                               - Se ci sono 3 prodotti critici, prende tutti e 3
                            3. as $prodotto: assegna ogni elemento a variabile $prodotto
                            4. Loop itera su ogni prodotto fino a @endforeach
                            
                            EQUIVALENTE PHP:
                            <?php foreach($prodotti_critici->take(6) as $prodotto): ?>
                                // contenuto loop
                            <?php endforeach; ?>
                            
                            NOTA: ->take(6) viene usato per limitare visualizzazione
                            e non appesantire la pagina. Link "Vedi Tutti" mostra resto.
                        --}}
                        @foreach($prodotti_critici->take(6) as $prodotto)
                            
                            {{-- 
                                ELEMENTO HTML: <div class="col-lg-4 col-md-6">
                                LINGUAGGIO: HTML + Bootstrap CSS
                                
                                Colonna responsive per card prodotto.
                                - col-lg-4: su large (≥992px) occupa 4/12 (33.33%) = 3 per riga
                                - col-md-6: su medium (≥768px) occupa 6/12 (50%) = 2 per riga
                                - su <768px: occupa 12/12 (100%) = 1 per riga
                            --}}
                            <div class="col-lg-4 col-md-6">
                                
                                {{-- 
                                    ELEMENTO HTML: <div class="card h-100 border-danger">
                                    LINGUAGGIO: HTML + Bootstrap CSS
                                    
                                    Card per singolo prodotto critico.
                                    - h-100: height 100% (tutte card stessa altezza)
                                    - border-danger: bordo rosso
                                --}}
                                <div class="card h-100 border-danger">
                                    <div class="card-body">
                                        
                                        {{-- 
                                            ELEMENTO HTML: <h6 class="card-title text-danger">
                                            LINGUAGGIO: HTML + Bootstrap CSS + Blade
                                            
                                            Titolo card con nome prodotto.
                                            - card-title: classe Bootstrap per titolo card
                                            - text-danger: testo rosso
                                        --}}
                                        <h6 class="card-title text-danger">
                                            {{-- 
                                                OUTPUT: {{ $prodotto->nome }}
                                                LINGUAGGIO: Blade (PHP)
                                                
                                                Stampa nome del prodotto corrente nel loop.
                                                
                                                FUNZIONAMENTO:
                                                1. $prodotto: variabile del loop @foreach
                                                2. ->nome: accede all'attributo 'nome' del modello Eloquent
                                                3. {{ }}: output escapato
                                                
                                                ESEMPIO:
                                                Se $prodotto->nome = "Lavatrice X500":
                                                Output: Lavatrice X500
                                            --}}
                                            {{ $prodotto->nome }}
                                            
                                            {{-- 
                                                DIRETTIVA BLADE: @if($prodotto->modello)
                                                
                                                Mostra modello solo se esiste.
                                                Verifica che $prodotto->modello non sia null o stringa vuota.
                                            --}}
                                            @if($prodotto->modello)
                                                {{-- 
                                                    ELEMENTO HTML: <small class="text-muted d-block">
                                                    
                                                    Visualizza modello prodotto sotto il nome.
                                                    - d-block: va a capo (nuova riga)
                                                --}}
                                                <small class="text-muted d-block">{{ $prodotto->modello }}</small>
                                            @endif
                                        </h6>
                                        
                                        {{-- 
                                            ELEMENTO HTML: <div class="d-flex justify-content-between ...">
                                            LINGUAGGIO: HTML + Bootstrap CSS
                                            
                                            Container flexbox per badge.
                                            - d-flex: display flex
                                            - justify-content-between: spazio tra elementi ai lati opposti
                                            - align-items-center: allineamento verticale centrato
                                            - mb-2: margin-bottom di 0.5rem
                                        --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            
                                            {{-- 
                                                ELEMENTO HTML: <span class="badge bg-danger">
                                                
                                                Badge che mostra numero malfunzionamenti critici.
                                            --}}
                                            <span class="badge bg-danger">
                                                {{-- 
                                                    OUTPUT: {{ $prodotto->critici_count }} critici
                                                    
                                                    FUNZIONAMENTO:
                                                    1. $prodotto->critici_count: attributo calcolato nel controller
                                                    2. Probabilmente ottenuto tramite:
                                                       ->withCount(['malfunzionamenti as critici_count' => function($q) {
                                                           $q->where('gravita', 'critica');
                                                       }])
                                                    3. Conta solo malfunzionamenti con gravità 'critica'
                                                    
                                                    ESEMPIO OUTPUT: "3 critici"
                                                --}}
                                                {{ $prodotto->critici_count }} critici
                                            </span>
                                            
                                            {{-- 
                                                Badge che mostra totale malfunzionamenti (tutte gravità).
                                                - bg-warning: background giallo/arancione
                                                - text-dark: testo scuro per contrasto
                                            --}}
                                            <span class="badge bg-warning text-dark">
                                                {{-- 
                                                    OUTPUT: {{ $prodotto->malfunzionamenti_count }} totali
                                                    
                                                    FUNZIONAMENTO:
                                                    1. $prodotto->malfunzionamenti_count: conta tutti i malfunzionamenti
                                                    2. Probabilmente ottenuto con ->withCount('malfunzionamenti')
                                                    3. Include critici + alta + media + bassa gravità
                                                --}}
                                                {{ $prodotto->malfunzionamenti_count }} totali
                                            </span>
                                        </div>
                                        
                                        {{-- 
                                            ELEMENTO HTML: <p class="card-text small">
                                            LINGUAGGIO: HTML + Bootstrap CSS
                                            
                                            Paragrafo con informazioni categoria.
                                            - card-text: classe Bootstrap per testo corpo card
                                            - small: dimensione testo ridotta
                                        --}}
                                        <p class="card-text small">
                                            <strong>Categoria:</strong>
                                            {{-- 
                                                HELPER BLADE: ucfirst(str_replace(...))
                                                LINGUAGGIO: Blade (PHP)
                                                
                                                Formattazione categoria per visualizzazione.
                                                
                                                FUNZIONAMENTO:
                                                1. $prodotto->categoria: valore dal database (es: "elettrodomestici_cucina")
                                                2. str_replace('_', ' ', ...): sostituisce underscore con spazi
                                                   "elettrodomestici_cucina" → "elettrodomestici cucina"
                                                3. ucfirst(...): capitalizza prima lettera
                                                   "elettrodomestici cucina" → "Elettrodomestici cucina"
                                                4. {{ }}: output escapato
                                                
                                                ESEMPIO COMPLETO:
                                                Input DB: "elettrodomestici_cucina"
                                                Output HTML: "Elettrodomestici cucina"
                                            --}}
                                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                        </p>
                                        
                                        {{-- 
                                            ELEMENTO HTML: <div class="d-grid gap-2">
                                            LINGUAGGIO: HTML + Bootstrap CSS
                                            
                                            Container grid per pulsanti full-width.
                                            - d-grid: display grid (CSS Grid)
                                            - gap-2: spazio di 0.5rem tra elementi grid
                                        --}}
                                        <div class="d-grid gap-2">
                                            
                                            {{-- 
                                                ELEMENTO HTML: <a href="{{ route(..., $prodotto) }}" ...>
                                                LINGUAGGIO: HTML + Blade
                                                
                                                Link alla pagina dettaglio prodotto.
                                                
                                                FUNZIONAMENTO route() CON PARAMETRO:
                                                1. route('prodotti.completo.show', $prodotto):
                                                   - 'prodotti.completo.show': nome route nel file routes/web.php
                                                   - $prodotto: modello Eloquent passato come parametro
                                                2. Laravel estrae automaticamente la chiave primaria ($prodotto->id)
                                                3. Genera URL: /prodotti/{id} (es: /prodotti/42)
                                                4. Nel controller, Route Model Binding risolve $prodotto
                                                
                                                ESEMPIO:
                                                Se $prodotto->id = 42:
                                                URL generato: http://sito.it/prodotti/42
                                                
                                                CLASSI CSS:
                                                - btn btn-outline-danger: pulsante con bordo rosso
                                                - btn-sm: dimensione small
                                            --}}
                                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                               class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-eye me-1"></i>Vedi Problemi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- 
                            DIRETTIVA BLADE: @endforeach
                            LINGUAGGIO: Blade
                            
                            Chiude il loop @foreach.
                            Tutto il codice tra @foreach e @endforeach viene ripetuto
                            per ogni elemento della collection.
                        --}}
                        @endforeach
                    </div>
                    
                    {{-- Link per vedere tutti i critici --}}
                    
                    {{-- 
                        ELEMENTO HTML: <div class="text-center mt-3">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Container centrato per link.
                        - text-center: allineamento testo al centro
                        - mt-3: margin-top di 1rem
                    --}}
                    <div class="text-center mt-3">
                        
                        {{-- 
                            Link alla pagina catalogo filtrato per prodotti critici.
                            Stesso URL del pulsante "Priorità Alta" nella sezione strumenti.
                            Permette di vedere TUTTI i prodotti critici, non solo i primi 6.
                        --}}
                        <a href="{{ route('prodotti.completo.index') }}?filter=critici" class="btn btn-danger">
                            <i class="bi bi-list me-1"></i>Vedi Tutti i Prodotti Critici
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 
        DIRETTIVA BLADE: @endif
        LINGUAGGIO: Blade
        
        Chiude il blocco @if iniziale.
        Se $prodotti_critici è vuota, tutta la sezione non viene renderizzata.
    --}}
    @endif

    {{-- === MALFUNZIONAMENTI RECENTI === --}}
    
    {{-- 
        DIRETTIVA BLADE: @if(isset(...) && ...->count() > 0)
        LINGUAGGIO: Blade (PHP)
        
        Verifica che esistano malfunzionamenti recenti da mostrare.
        Stessa logica della sezione prodotti critici.
    --}}
    @if(isset($malfunzionamenti_recenti) && $malfunzionamenti_recenti->count() > 0)
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock text-primary me-2"></i>
                        Malfunzionamenti Segnalati Recentemente
                    </h5>
                </div>
                
                <div class="card-body">
                    
                    {{-- 
                        ELEMENTO HTML: <div class="table-responsive">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Wrapper responsive per tabelle.
                        
                        FUNZIONAMENTO:
                        - Su schermi piccoli: aggiunge scroll orizzontale
                        - Su schermi grandi: tabella normale
                        - Previene rottura layout con tabelle larghe
                    --}}
                    <div class="table-responsive">
                        
                        {{-- 
                            ELEMENTO HTML: <table class="table table-hover">
                            LINGUAGGIO: HTML + Bootstrap CSS
                            
                            Tabella Bootstrap con effetto hover.
                            - table: classe base Bootstrap per tabelle
                            - table-hover: evidenzia riga al passaggio mouse
                        --}}
                        <table class="table table-hover">
                            
                            {{-- 
                                ELEMENTO HTML: <thead>
                                LINGUAGGIO: HTML
                                
                                Intestazione tabella (Table Head).
                                Contiene i nomi delle colonne.
                            --}}
                            <thead>
                                
                                {{-- 
                                    ELEMENTO HTML: <tr>
                                    LINGUAGGIO: HTML
                                    
                                    Table Row: riga della tabella.
                                --}}
                                <tr>
                                    {{-- 
                                        ELEMENTO HTML: <th>
                                        LINGUAGGIO: HTML
                                        
                                        Table Header: cella intestazione colonna.
                                        Definisce nome e scopo della colonna.
                                    --}}
                                    <th>Prodotto</th>
                                    <th>Problema</th>
                                    <th>Gravità</th>
                                    <th>Segnalazioni</th>
                                    <th>Ultima Segnalazione</th>
                                    
                                    {{-- 
                                        Colonna azioni con allineamento centrato.
                                        - text-center: allinea contenuto al centro
                                    --}}
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            
                            {{-- 
                                ELEMENTO HTML: <tbody>
                                LINGUAGGIO: HTML
                                
                                Corpo tabella (Table Body).
                                Contiene le righe dati.
                            --}}
                            <tbody>
                                
                                {{-- 
                                    DIRETTIVA BLADE: @foreach(...->take(5) as ...)
                                    {{-- 
    ============================================================================
    PARTE 2: Continuazione del file tecnico-dashboard.blade.php
    ============================================================================
    Questa è la continuazione del file. Incollare dopo la Parte 1.
--}}

                                {{-- 
                                    DIRETTIVA BLADE: @foreach($malfunzionamenti_recenti->take(5) as $malfunzionamento)
                                    LINGUAGGIO: Blade (PHP)
                                    
                                    Loop attraverso ultimi 5 malfunzionamenti recenti.
                                    
                                    FUNZIONAMENTO:
                                    1. $malfunzionamenti_recenti: Collection Laravel
                                    2. ->take(5): limita a primi 5 elementi
                                    3. as $malfunzionamento: variabile loop per ogni elemento
                                    4. Itera fino a @endforeach
                                    
                                    NOTA: Limitato a 5 per non appesantire dashboard.
                                    Link "Vedi Tutti" permette accesso completo.
                                --}}
                                @foreach($malfunzionamenti_recenti->take(5) as $malfunzionamento)
                                    
                                    {{-- 
                                        ELEMENTO HTML: <tr>
                                        LINGUAGGIO: HTML
                                        
                                        Riga tabella per singolo malfunzionamento.
                                        Ogni iterazione del loop crea una nuova riga.
                                    --}}
                                    <tr>
                                        {{-- 
                                            ELEMENTO HTML: <td>
                                            LINGUAGGIO: HTML
                                            
                                            Table Data: cella dati della tabella.
                                        --}}
                                        <td>
                                            {{-- 
                                                ELEMENTO HTML: <strong>
                                                LINGUAGGIO: HTML
                                                
                                                Testo in grassetto per nome prodotto.
                                            --}}
                                            <strong>
                                                {{-- 
                                                    OUTPUT: {{ $malfunzionamento->prodotto->nome }}
                                                    LINGUAGGIO: Blade (PHP)
                                                    
                                                    Accesso relazione Eloquent.
                                                    
                                                    FUNZIONAMENTO:
                                                    1. $malfunzionamento: modello Malfunzionamento corrente
                                                    2. ->prodotto: relazione belongsTo() definita nel modello
                                                       Esempio nel modello Malfunzionamento:
                                                       public function prodotto() {
                                                           return $this->belongsTo(Prodotto::class);
                                                       }
                                                    3. ->nome: attributo del modello Prodotto correlato
                                                    4. {{ }}: output escapato
                                                    
                                                    QUERY SQL SOTTOSTANTE (con eager loading):
                                                    SELECT * FROM malfunzionamenti
                                                    LEFT JOIN prodotti ON malfunzionamenti.prodotto_id = prodotti.id
                                                    
                                                    ESEMPIO:
                                                    Se malfunzionamento appartiene a prodotto "Lavatrice X500":
                                                    Output: Lavatrice X500
                                                --}}
                                                {{ $malfunzionamento->prodotto->nome }}
                                            </strong>
                                            
                                            {{-- 
                                                Mostra modello prodotto se disponibile.
                                            --}}
                                            @if($malfunzionamento->prodotto->modello)
                                                {{-- 
                                                    ELEMENTO HTML: <br>
                                                    LINGUAGGIO: HTML
                                                    
                                                    Line break: va a capo (nuova riga).
                                                --}}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $malfunzionamento->prodotto->modello }}
                                                </small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            {{-- 
                                                HELPER BLADE: Str::limit($string, $limit)
                                                LINGUAGGIO: Blade (PHP)
                                                
                                                Limita lunghezza stringa per visualizzazione.
                                                
                                                FUNZIONAMENTO:
                                                1. Str::limit(): metodo della classe Illuminate\Support\Str
                                                2. Parametri:
                                                   - $malfunzionamento->titolo: stringa da limitare
                                                   - 40: numero massimo caratteri
                                                3. Se stringa > 40 caratteri: tronca e aggiunge "..."
                                                4. Se stringa ≤ 40 caratteri: restituisce invariata
                                                
                                                ESEMPIO:
                                                Input: "Lavatrice non si accende dopo temporale"
                                                Output: "Lavatrice non si accende dopo tempo..."
                                                
                                                Input: "Perdita acqua"
                                                Output: "Perdita acqua"
                                            --}}
                                            {{ Str::limit($malfunzionamento->titolo, 40) }}
                                        </td>
                                        
                                        <td>
                                            {{-- 
                                                DIRETTIVA BLADE: @php ... @endphp
                                                LINGUAGGIO: Blade (PHP)
                                                
                                                Blocco PHP inline in Blade.
                                                Permette esecuzione codice PHP direttamente nel template.
                                                
                                                UTILIZZO:
                                                - Definire variabili temporanee
                                                - Eseguire logica complessa
                                                - Calcoli non fattibili con direttive Blade
                                                
                                                BEST PRACTICE:
                                                - Usare con parsimonia (preferire logica in controller)
                                                - Ideale per mappature semplici come questa
                                            --}}
                                            @php
                                                {{-- 
                                                    ARRAY ASSOCIATIVO PHP
                                                    LINGUAGGIO: PHP
                                                    
                                                    Mappa gravità → colore badge Bootstrap.
                                                    
                                                    STRUTTURA:
                                                    $badges = [
                                                        'chiave' => 'valore',
                                                        ...
                                                    ];
                                                    
                                                    FUNZIONAMENTO:
                                                    1. Definisce array associativo (key => value)
                                                    2. Chiavi: valori gravità dal database
                                                    3. Valori: suffissi classi Bootstrap (bg-{valore})
                                                    
                                                    MAPPING:
                                                    - 'critica' → 'danger' (rosso)
                                                    - 'alta' → 'warning' (giallo/arancione)
                                                    - 'media' → 'info' (azzurro)
                                                    - 'bassa' → 'secondary' (grigio)
                                                --}}
                                                $badges = [
                                                    'critica' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'bassa' => 'secondary'
                                                ];
                                            @endphp
                                            
                                            {{-- 
                                                ELEMENTO HTML: <span class="badge bg-{{ ... }}">
                                                LINGUAGGIO: HTML + Blade
                                                
                                                Badge dinamico con colore basato su gravità.
                                            --}}
                                            <span class="badge bg-{{ $badges[$malfunzionamento->gravita] ?? 'secondary' }}">
                                                {{-- 
                                                    ESPRESSIONE COMPLESSA BLADE
                                                    LINGUAGGIO: Blade (PHP)
                                                    
                                                    {{ $badges[$malfunzionamento->gravita] ?? 'secondary' }}
                                                    
                                                    FUNZIONAMENTO PASSO-PASSO:
                                                    1. $malfunzionamento->gravita: valore dal DB (es: 'critica')
                                                    2. $badges[...]: accesso array associativo
                                                    3. ?? 'secondary': null coalescing operator
                                                       - Se chiave esiste: usa valore corrispondente
                                                       - Se chiave non esiste: usa 'secondary' come default
                                                    4. Risultato inserito in class="badge bg-{risultato}"
                                                    
                                                    ESEMPI:
                                                    - gravita='critica' → bg-danger (rosso)
                                                    - gravita='alta' → bg-warning (giallo)
                                                    - gravita='sconosciuta' → bg-secondary (grigio default)
                                                    
                                                    SICUREZZA:
                                                    Il ?? garantisce che ci sia sempre un valore valido,
                                                    evitando errori se gravità non è nell'array.
                                                --}}
                                                
                                                {{-- 
                                                    HELPER PHP: ucfirst($string)
                                                    LINGUAGGIO: PHP
                                                    
                                                    Capitalizza prima lettera della stringa.
                                                    
                                                    FUNZIONAMENTO:
                                                    1. $malfunzionamento->gravita: valore minuscolo da DB
                                                    2. ucfirst(): funzione PHP nativa
                                                    3. Rende maiuscola solo la prima lettera
                                                    
                                                    ESEMPIO:
                                                    Input: 'critica'
                                                    Output: 'Critica'
                                                    
                                                    NOTA: Non confondere con:
                                                    - strtoupper(): tutto maiuscolo (CRITICA)
                                                    - ucwords(): prima lettera ogni parola (Molto Critica)
                                                --}}
                                                {{ ucfirst($malfunzionamento->gravita) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            {{-- 
                                                ELEMENTO HTML: <span class="badge bg-primary" id="count-{{ ... }}">
                                                LINGUAGGIO: HTML + Blade
                                                
                                                Badge con ID dinamico per aggiornamenti JavaScript.
                                                
                                                ATTRIBUTO ID DINAMICO:
                                                id="count-{{ $malfunzionamento->id }}"
                                                
                                                FUNZIONAMENTO:
                                                1. Blade interpola {{ $malfunzionamento->id }}
                                                2. Genera ID univoco per ogni malfunzionamento
                                                3. Esempio: id="count-42" per malfunzionamento con id=42
                                                
                                                SCOPO:
                                                Permette a JavaScript di targetizzare e aggiornare
                                                il contatore di questo specifico malfunzionamento
                                                quando utente segnala problema (vedi funzione
                                                segnalaMalfunzionamento() in sezione @push('scripts')).
                                            --}}
                                            <span class="badge bg-primary" id="count-{{ $malfunzionamento->id }}">
                                                {{-- 
                                                    OUTPUT: Numero segnalazioni con fallback.
                                                    
                                                    ?? 0: se numero_segnalazioni è null, mostra 0
                                                --}}
                                                {{ $malfunzionamento->numero_segnalazioni ?? 0 }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            {{-- 
                                                DIRETTIVA BLADE: @if($malfunzionamento->ultima_segnalazione)
                                                
                                                Verifica se campo ultima_segnalazione ha valore.
                                                Se null, mostra "N/A" invece di errore.
                                            --}}
                                            @if($malfunzionamento->ultima_segnalazione)
                                                {{-- 
                                                    CLASSE CARBON: \Carbon\Carbon
                                                    LINGUAGGIO: PHP (libreria Carbon)
                                                    
                                                    Carbon è la libreria Laravel per gestione date.
                                                    
                                                    FUNZIONAMENTO:
                                                    1. \Carbon\Carbon::parse(...): parsing stringa data
                                                       - Parametro: stringa data dal DB (es: '2024-03-15 14:30:00')
                                                       - Restituisce oggetto Carbon
                                                    2. ->format('d/m/Y'): formattazione output
                                                       - d: giorno con zero iniziale (01-31)
                                                       - m: mese con zero iniziale (01-12)
                                                       - Y: anno 4 cifre (2024)
                                                    3. {{ }}: output escapato Blade
                                                    
                                                    ESEMPIO COMPLETO:
                                                    Input DB: '2024-03-15 14:30:00'
                                                    Output HTML: '15/03/2024'
                                                    
                                                    NOTA: \Carbon\Carbon con backslash iniziale
                                                    indica classe globale (root namespace).
                                                    Equivale a: use Carbon\Carbon; Carbon::parse(...)
                                                --}}
                                                {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                            @else
                                                {{-- 
                                                    ELEMENTO HTML: <span class="text-muted">N/A</span>
                                                    
                                                    Testo "Non Applicabile" per date mancanti.
                                                    - text-muted: grigio tenue Bootstrap
                                                --}}
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            {{-- 
                                                ELEMENTO HTML: <div class="btn-group btn-group-sm">
                                                LINGUAGGIO: HTML + Bootstrap CSS
                                                
                                                Gruppo pulsanti Bootstrap.
                                                
                                                FUNZIONAMENTO:
                                                - btn-group: raggruppa pulsanti adiacenti
                                                - btn-group-sm: dimensione small per gruppo
                                                - Pulsanti appaiono uniti senza spazi
                                                - Bordi interni condivisi
                                            --}}
                                            <div class="btn-group btn-group-sm">
                                                
                                                {{-- 
                                                    ELEMENTO HTML: <a href="{{ route(...) }}" ...>
                                                    LINGUAGGIO: HTML + Blade
                                                    
                                                    Link a pagina dettaglio malfunzionamento.
                                                    
                                                    ROUTE CON PARAMETRI MULTIPLI:
                                                    route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento])
                                                    
                                                    FUNZIONAMENTO:
                                                    1. route(): helper Laravel
                                                    2. 'malfunzionamenti.show': nome route
                                                    3. Array parametri: [$prodotto, $malfunzionamento]
                                                    4. Laravel sostituisce parametri nella definizione route
                                                    
                                                    ESEMPIO DEFINIZIONE ROUTE (routes/web.php):
                                                    Route::get('/prodotti/{prodotto}/malfunzionamenti/{malfunzionamento}', ...)
                                                        ->name('malfunzionamenti.show');
                                                    
                                                    ESEMPIO GENERAZIONE URL:
                                                    Se prodotto->id=10 e malfunzionamento->id=25:
                                                    URL: /prodotti/10/malfunzionamenti/25
                                                    
                                                    ATTRIBUTO TITLE:
                                                    - title="Visualizza soluzione": tooltip al passaggio mouse
                                                    - Migliora accessibilità e UX
                                                --}}
                                                <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Visualizza soluzione">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                {{-- 
                                                    ELEMENTO HTML: <button type="button" ... onclick="...">
                                                    LINGUAGGIO: HTML + JavaScript
                                                    
                                                    Pulsante per segnalare malfunzionamento.
                                                    
                                                    ATTRIBUTI:
                                                    - type="button": evita submit form (se dentro form)
                                                    - class="btn btn-outline-warning segnala-btn":
                                                      * btn btn-outline-warning: stile Bootstrap
                                                      * segnala-btn: classe custom per JavaScript/CSS
                                                    - onclick="segnalaMalfunzionamento(...)":
                                                      * Evento click chiama funzione JavaScript
                                                      * Funzione definita in sezione @push('scripts')
                                                    - title: tooltip esplicativo
                                                    
                                                    FUNZIONE JAVASCRIPT:
                                                    segnalaMalfunzionamento({{ $malfunzionamento->id }})
                                                    
                                                    FUNZIONAMENTO:
                                                    1. {{ $malfunzionamento->id }}: Blade interpola ID
                                                    2. Esempio: onclick="segnalaMalfunzionamento(42)"
                                                    3. Click chiama funzione JS con ID come parametro
                                                    4. Funzione invia richiesta AJAX per incrementare counter
                                                    5. Aggiorna badge #count-42 senza reload pagina
                                                --}}
                                                <button type="button" 
                                                        class="btn btn-outline-warning segnala-btn"
                                                        onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                        title="Segnala questo problema">
                                                    <i class="bi bi-exclamation-circle me-1"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                {{-- 
                                    DIRETTIVA BLADE: @endforeach
                                    
                                    Chiude loop malfunzionamenti recenti.
                                --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- 
                        Link per accedere a tutti i malfunzionamenti.
                        Reindirizza alla pagina ricerca completa.
                    --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i>Vedi Tutti i Malfunzionamenti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 
        DIRETTIVA BLADE: @endif
        
        Chiude blocco condizionale malfunzionamenti recenti.
    --}}
    @endif

    {{-- === SUPPORTO E GUIDE === --}}
    
    {{-- 
        SEZIONE: Guide e Contatti
        LINGUAGGIO: HTML + Bootstrap CSS
        
        Due colonne con informazioni di supporto per i tecnici.
    --}}
    <div class="row mt-4">
        
        {{-- 
            Colonna sinistra: Guida rapida per tecnici
        --}}
        <div class="col-md-6">
            
            {{-- 
                ELEMENTO HTML: <div class="card card-custom border-info">
                
                Card con bordo azzurro per distinguere contenuto informativo.
                - border-info: bordo azzurro Bootstrap
            --}}
            <div class="card card-custom border-info">
                
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Guida Rapida per Tecnici
                    </h6>
                </div>
                
                <div class="card-body">
                    {{-- 
                        ELEMENTO HTML: <ul class="list-unstyled mb-0">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Lista non ordinata senza bullet points.
                        
                        CLASSI:
                        - list-unstyled: rimuove bullet points e padding sinistro
                        - mb-0: margin-bottom zero (rimuove spazio sotto lista)
                    --}}
                    <ul class="list-unstyled mb-0">
                        
                        {{-- 
                            ELEMENTO HTML: <li class="mb-2">
                            
                            List item con margine inferiore per spaziatura.
                            - mb-2: margin-bottom di 0.5rem tra elementi lista
                        --}}
                        <li class="mb-2">
                            {{-- 
                                Icona checkmark verde per indicare suggerimento/tip.
                            --}}
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Usa la ricerca wildcard con "*" per trovare prodotti simili
                        </li>
                        
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Filtra i malfunzionamenti per gravità e difficoltà
                        </li>
                        
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Segnala i problemi riscontrati per aiutare altri tecnici
                        </li>
                        
                        {{-- 
                            ELEMENTO HTML: <li class="mb-0">
                            
                            Ultimo elemento lista senza margine inferiore.
                            - mb-0: evita spazio extra alla fine della lista
                        --}}
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Consulta lo storico per il track delle tue attività
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- 
            Colonna destra: Informazioni contatto supporto
        --}}
        <div class="col-md-6">
            
            {{-- 
                Card con bordo grigio per informazioni contatto.
            --}}
            <div class="card card-custom border-secondary">
                
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        Supporto e Contatti
                    </h6>
                </div>
                
                <div class="card-body">
                    
                    {{-- 
                        ELEMENTO HTML: <div class="d-flex justify-content-between ...">
                        LINGUAGGIO: HTML + Bootstrap CSS
                        
                        Riga flexbox per informazione contatto.
                        - d-flex: display flex
                        - justify-content-between: spazio tra label e valore
                        - align-items-center: allineamento verticale centrato
                        - mb-2: margine inferiore tra righe
                    --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Email Supporto:</span>
                        
                        {{-- 
                            ELEMENTO HTML: <a href="mailto:..." ...>
                            LINGUAGGIO: HTML
                            
                            Link mailto per aprire client email.
                            
                            FUNZIONAMENTO:
                            1. href="mailto:supporto@sistemaassistenza.it"
                            2. Click apre app email predefinita con destinatario precompilato
                            3. text-decoration-none: rimuove sottolineatura link
                            
                            NOTA: mailto: è protocollo URL per email
                            Sintassi completa: mailto:email@example.com?subject=Oggetto&body=Corpo
                        --}}
                        <a href="mailto:supporto@sistemaassistenza.it" class="text-decoration-none">
                            supporto@sistemaassistenza.it
                        </a>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Telefono:</span>
                        
                        {{-- 
                            ELEMENTO HTML: <a href="tel:..." ...>
                            LINGUAGGIO: HTML
                            
                            Link tel per chiamata telefonica.
                            
                            FUNZIONAMENTO:
                            1. href="tel:+390712204000"
                            2. Su dispositivi mobili: click avvia chiamata
                            3. Su desktop: click apre app telefono (se disponibile)
                            
                            FORMATO NUMERO:
                            - +39: prefisso internazionale Italia
                            - 071: prefisso Ancona
                            - 2204000: numero locale
                            - Formato E.164: +[country][area][number]
                        --}}
                        <a href="tel:+390712204000" class="text-decoration-none">
                            +39 071 220 4000
                        </a>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Orari:</span>
                        <span class="text-muted">Lun-Ven 8:00-18:00</span>
                    </div>
                    
                    {{-- 
                        ELEMENTO HTML: <hr>
                        LINGUAGGIO: HTML
                        
                        Horizontal Rule: linea orizzontale separatrice.
                        Separa visualmente sezioni diverse del contenuto.
                    --}}
                    <hr>
                    
                    {{-- 
                        Sezione centrata con link a pagina contatti completa.
                    --}}
                    <div class="text-center">
                        {{-- 
                            Link alla route 'contatti'.
                            Probabilmente pagina con form contatto o maggiori dettagli.
                        --}}
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-envelope me-1"></i>Contattaci
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    DIRETTIVA BLADE: @endsection
    LINGUAGGIO: Blade
    
    Chiude la sezione 'content' iniziata con @section('content').
    Tutto il contenuto tra @section('content') e @endsection
    viene inserito nel punto @yield('content') del layout padre.
--}}
@endsection

{{-- 
    ============================================================================
    SEZIONE: Scripts JavaScript
    ============================================================================
    
    DIRETTIVA BLADE: @push('scripts')
    LINGUAGGIO: Blade
    
    Stack Blade per aggiungere contenuto a una sezione esistente.
    
    FUNZIONAMENTO:
    1. Nel layout padre (layouts/app.blade.php) c'è @stack('scripts')
    2. Ogni view può fare @push('scripts') per aggiungere JavaScript
    3. Contenuto viene accumulato e inserito dove definito @stack
    4. Permette a view multiple di aggiungere script senza sovrascrivere
    
    DIFFERENZA @section vs @push:
    - @section: sovrascrive contenuto esistente
    - @push: aggiunge (append) al contenuto esistente
    
    USO TIPICO:
    - @push('scripts'): JavaScript specifico della view
    - @push('styles'): CSS specifico della view
    - @push('meta'): meta tag aggiuntivi
--}}
@push('scripts')

{{-- 
    ELEMENTO HTML: <script>
    LINGUAGGIO: HTML
    
    Tag per includere codice JavaScript.
    Tutto tra <script> e </script> è codice JavaScript eseguito dal browser.
--}}
<script>
{{-- 
    ============================================================================
    JAVASCRIPT: Inizializzazione dati pagina
    ============================================================================
    LINGUAGGIO: JavaScript + Blade
--}}

// Inizializza i dati della pagina se non esistono già
{{-- 
    STATEMENT JAVASCRIPT: window.PageData = window.PageData || {};
    LINGUAGGIO: JavaScript
    
    Inizializzazione oggetto globale per dati pagina.
    
    FUNZIONAMENTO:
    1. window.PageData: proprietà dell'oggetto globale window
    2. ||: operatore logico OR
    3. {}: oggetto vuoto
    4. Se PageData esiste già: mantiene valore esistente
    5. Se PageData non esiste: crea oggetto vuoto
    
    PATTERN: "Init if not exists"
    Evita sovrascrivere dati se script eseguito multiple volte.
    
    EQUIVALENTE:
    if (typeof window.PageData === 'undefined') {
        window.PageData = {};
    }
    
    SCOPO:
    - Centralizzare dati della pagina accessibili da JavaScript
    - Evitare variabili globali sparse
    - Namespace per dati Laravel passati a JavaScript
--}}
window.PageData = window.PageData || {};

// Aggiungi dati specifici solo se necessari per questa view

{{-- 
    DIRETTIVA BLADE: @if(isset($prodotto))
    LINGUAGGIO: Blade (in contesto JavaScript)
    
    Condizionale Blade dentro script JavaScript.
    Blade viene processato server-side PRIMA che JavaScript sia inviato al browser.
    
    FUNZIONAMENTO:
    1. Server Laravel valuta @if(isset($prodotto))
    2. Se TRUE: include il codice tra @if e @endif
    3. Se FALSE: rimuove completamente il blocco
    4. Browser riceve solo JavaScript risultante
    
    ESEMPIO OUTPUT BROWSER (se $prodotto esiste):
    window.PageData.prodotto = {"id":42,"nome":"Lavatrice X500",...};
    
    ESEMPIO OUTPUT BROWSER (se $prodotto NON esiste):
    [Nessun codice - completamente rimosso]
--}}
@if(isset($prodotto))
{{-- 
    STATEMENT JAVASCRIPT: window.PageData.prodotto = @json($prodotto);
    LINGUAGGIO: JavaScript + Blade
    
    Passa oggetto PHP a JavaScript in formato JSON.
    
    HELPER BLADE: @json($value)
    
    FUNZIONAMENTO:
    1. @json(): direttiva Blade che converte dati PHP in JSON
    2. $prodotto: modello Eloquent o array PHP
    3. Serializza in formato JSON valido
    4. JavaScript riceve oggetto nativo
    
    ESEMPIO CONVERSIONE:
    PHP (server):
    $prodotto = [
        'id' => 42,
        'nome' => 'Lavatrice X500',
        'prezzo' => 499.99
    ];
    
    OUTPUT JAVASCRIPT (browser):
    window.PageData.prodotto = {
        "id": 42,
        "nome": "Lavatrice X500",
        "prezzo": 499.99
    };
    
    SICUREZZA:
    - @json() esegue automaticamente escape HTML
    - Previene XSS (Cross-Site Scripting)
    - Gestisce correttamente caratteri speciali, apici, etc.
    
    EQUIVALENTE PHP:
    <?php echo json_encode($prodotto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>
    
    ACCESSO DA JAVASCRIPT:
    // Codice JS successivo può usare:
    console.log(window.PageData.prodotto.nome); // "Lavatrice X500"
    let id = PageData.prodotto.id; // 42
--}}
window.PageData.prodotto = @json($prodotto);
@endif

{{-- 
    Pattern ripetuto per ogni variabile da passare a JavaScript.
    Ogni @if verifica esistenza variabile prima di serializzarla.
--}}

@if(isset($prodotti))
{{-- 
    Passa collection/array di prodotti.
    Utile se JavaScript deve iterare su lista prodotti.
    
    NOTA: Collection Laravel viene automaticamente convertita in array JSON.
--}}
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
{{-- 
    Passa singolo malfunzionamento corrente.
--}}
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
{{-- 
    Passa lista malfunzionamenti.
--}}
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
{{-- 
    Passa singolo centro assistenza.
--}}
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
{{-- 
    Passa lista centri assistenza.
--}}
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
{{-- 
    Passa array categorie prodotti.
    Utile per dropdown/filtri dinamici.
--}}
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
{{-- 
    Passa membri staff (per admin/gestione utenti).
--}}
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
{{-- 
    Passa statistiche sistema.
    Già utilizzate nella view, ma rese disponibili anche a JavaScript
    per eventuali grafici o aggiornamenti dinamici.
--}}
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
{{-- 
    Passa dati utente corrente.
    
    NOTA: In questa view probabilmente non necessario perché
    si usa già auth()->user() nel template Blade.
    Incluso per completezza/coerenza con altre view.
--}}
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
{{-- 
    COMMENTO JAVASCRIPT
    LINGUAGGIO: JavaScript
    
    // commento singola riga
    /* commento multilinea */
    
    Questa riga è un placeholder per future aggiunte.
--}}

</script>
{{-- 
    DIRETTIVA BLADE: @endpush
    LINGUAGGIO: Blade
    
    Chiude il blocco @push('scripts').
    Tutto tra @push e @endpush viene aggiunto allo stack 'scripts'.
--}}
@endpush

{{-- 
    ============================================================================
    SEZIONE: Stili CSS Personalizzati
    ============================================================================
    
    DIRETTIVA BLADE: @push('styles')
    LINGUAGGIO: Blade
    
    Stack Blade per aggiungere CSS specifico di questa view.
    Il contenuto viene inserito dove il layout ha @stack('styles'),
    tipicamente nel <head> della pagina.
--}}
@push('styles')

{{-- 
    ELEMENTO HTML: <style>
    LINGUAGGIO: HTML
    
    Tag per definire CSS interno (embedded).
    Tutto tra <style> e </style> è codice CSS interpretato dal browser.
--}}
<style>
{{-- 
    ============================================================================
    CSS: Stili personalizzati per la dashboard tecnico
    ============================================================================
    LINGUAGGIO: CSS (Cascading Style Sheets)
--}}

/* === STILI PER RICERCA MANUALE === */
{{-- 
    COMMENTO CSS
    LINGUAGGIO: CSS
    
    /* commento CSS */
    
    I commenti CSS usano sintassi /* ... */
    Non esiste commento singola riga in CSS puro.
--}}

/* Indicatore di loading per input */
{{-- 
    REGOLA CSS: .loading-input { ... }
    LINGUAGGIO: CSS
    
    Selettore di classe per input durante caricamento.
    
    FUNZIONAMENTO:
    1. .loading-input: selettore classe (dot notation)
    2. Si applica a elementi con class="loading-input"
    3. Aggiunta/rimossa via JavaScript durante AJAX
    
    SCOPO:
    Mostra indicatore visivo quando ricerca è in corso.
--}}
.loading-input {
    {{-- 
        PROPRIETÀ CSS: background-image
        LINGUAGGIO: CSS
        
        Imposta immagine di sfondo.
        
        VALORE: url("data:image/svg+xml,...")
        - data:image/svg+xml: Data URL scheme per SVG inline
        - SVG contiene animazione cerchio pulsante (loading spinner)
        
        DATA URL SCHEME:
        data:[mediatype][;base64],data
        
        VANTAGGI:
        - No richiesta HTTP aggiuntiva
        - Immagine embedded nel CSS
        - Funziona anche offline
        
        SVG CODIFICATO:
        L'SVG contiene elemento <circle> con attributi <animate>
        per creare effetto spinner rotante.
    --}}
    background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%236c757d'%3e%3ccircle cx='10' cy='10' r='1'%3e%3canimate attributeName='r' begin='0s' dur='1.8s' values='1; 4; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.165, 0.84, 0.44, 1; 0.3, 0.61, 0.355, 1' repeatCount='indefinite'/%3e%3canimate attributeName='stroke-opacity' begin='0s' dur='1.8s' values='1; 0; 1' calcMode='spline' keyTimes='0; .5; 1' keySplines='0.3, 0.61, 0.355, 1; 0.165, 0.84, 0.44, 1' repeatCount='indefinite'/%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
    
    {{-- 
        PROPRIETÀ CSS: background-repeat
        
        Controlla ripetizione immagine sfondo.
        
        VALORI:
        - no-repeat: immagine mostrata una sola volta
        - repeat: ripete orizzontalmente e verticalmente
        - repeat-x: ripete solo orizzontalmente
        - repeat-y: ripete solo verticalmente
    --}}
    background-repeat: no-repeat;
    
    {{-- 
        PROPRIETÀ CSS: background-position
        
        Posiziona immagine di sfondo.
        
        VALORE: right 12px center
        - right 12px: 12px dal bordo destro
        - center: centrato verticalmente
        
        SINTASSI:
        background-position: [x] [y];
        
        SCOPO:
        Posiziona spinner a destra nell'input, lasciando spazio per testo.
    --}}
    background-position: right 12px center;
    
    {{-- 
        PROPRIETÀ CSS: background-size
        
        Dimensione immagine di sfondo.
        
        VALORE: 20px
        - Larghezza e altezza 20px (immagine quadrata)
        
        SINTASSI:
        background-size: [width] [height];
        background-size: [size]; (uguale per entrambi)
    --}}
    background-size: 20px;
    
    {{-- 
        PROPRIETÀ CSS: padding-right
        
        Padding interno sul lato destro.
        
        VALORE: 40px
        
        SCOPO:
        Evita che testo input si sovrapponga allo spinner.
        Lascia spazio sufficiente per icona 20px + margini.
    --}}
    padding-right: 40px;
}

/* Animazione per badge aggiornati */
{{-- 
    REGOLA CSS: .badge-updated
    
    Classe applicata via JavaScript a badge dopo aggiornamento.
    Crea effetto visivo per attirare attenzione su valore cambiato.
--}}
.badge-updated {
    {{-- 
        PROPRIETÀ CSS: animation
        
        Shorthand per proprietà animazione CSS.
        
        SINTASSI:
        animation: [name] [duration] [timing-function];
        
        VALORI:
        - badge-pulse: nome animazione (definita con @keyframes)
        - 2s: durata 2 secondi
        - ease-in-out: accelerazione/decelerazione morbida
        
        TIMING FUNCTIONS:
        - linear: velocità costante
        - ease: lento-veloce-lento (default)
        - ease-in: inizia lento
        - ease-out: finisce lento
        - ease-in-out: inizia e finisce lento
    --}}
    animation: badge-pulse 2s ease-in-out;
    
    {{-- 
        PROPRIETÀ CSS: transform
        
        Applica trasformazioni 2D/3D.
        
        FUNZIONE: scale(1.2)
        - Scala elemento al 120% dimensione originale
        - scale(1) = dimensione normale
        - scale(2) = doppia dimensione
        - scale(0.5) = metà dimensione
        
        ALTRE FUNZIONI TRANSFORM:
        - translate(x, y): sposta
        - rotate(deg): ruota
        - skew(deg): inclina
    --}}
    transform: scale(1.2);
}

{{-- 
    DIRETTIVA CSS: @keyframes
    LINGUAGGIO: CSS
    
    Definisce animazione CSS.
    
    SINTASSI:
    @keyframes [nome-animazione] {
        [percentuale] { [proprietà]: [valore]; }
    }
    
    FUNZIONAMENTO:
    1. Definisce stati chiave (keyframes) dell'animazione
    2. Browser interpola automaticamente tra stati
    3. 0% = inizio, 100% = fine
    4. Può usare percentuali intermedie (25%, 50%, etc.)
--}}
@keyframes badge-pulse {
    {{-- 
        KEYFRAME: 0%
        
        Stato iniziale animazione.
    --}}
    0% { 
        {{-- 
            PROPRIETÀ CSS: background-color
            
            Colore di sfondo.
            
            VALORE: #198754
            - Codice esadecimale colore verde
            - # indica hex color
            - 19: componente rosso
            - 87: componente verde
            - 54: componente blu
        --}}
        background-color: #198754;
        
        {{-- 
            Inizia con scala 1.2 (120%)
        --}}
        transform: scale(1.2);
    }
    
    {{-- 
        KEYFRAME: 50%
        
        Stato intermedio (metà animazione).
        Browser interpola automaticamente da 0% a 50% e da 50% a 100%.
    --}}
    50% { 
        {{-- 
            Colore verde più chiaro (#20c997) a metà animazione.
            Crea effetto "pulsazione" di colore.
        --}}
        background-color: #20c997;
        
        {{-- 
            Scala massima 1.3 (130%) a metà animazione.
            Badge "cresce" fino a metà, poi torna a dimensione normale.
        --}}
        transform: scale(1.3);
    }
    
    {{-- 
        KEYFRAME: 100%
        
        Stato finale animazione.
        Ritorna a valori iniziali per loop fluido.
    --}}
    100% { 
        background-color: #198754;
        
        {{-- 
            Ritorna a scala 1 (100% dimensione originale).
        --}}
        transform: scale(1);
    }
}

/* Animazione statistiche in aggiornamento */
{{-- 
    REGOLA CSS: .updating
    
    Classe applicata a elementi durante aggiornamento dati.
    Crea effetto "pulsante" per indicare caricamento.
--}}
.updating {
    {{-- 
        Animazione infinita con nome 'pulse'.
    --}}
    animation: pulse 1s infinite;
    
    {{-- 
        PROPRIETÀ CSS: opacity
        
        Opacità elemento (trasparenza).
        
        VALORE: 0.7
        - 0: completamente trasparente
        - 1: completamente opaco
        - 0.7: 70% opaco (30% trasparente)
    --}}
    opacity: 0.7;
}

{{-- 
    Animazione pulse per effetto caricamento.
    Più semplice di badge-pulse: solo cambia opacità.
--}}
@keyframes pulse {
    0% { opacity: 0.7; }
    50% { opacity: 1; }
    100% { opacity: 0.7; }
}

/* Animazione click pulsanti */
{{-- 
    REGOLA CSS: .btn-clicked
    
    Classe applicata momentaneamente a pulsanti quando cliccati.
    Fornisce feedback visivo immediato dell'interazione.
--}}
.btn-clicked {
    {{-- 
        FUNZIONE TRANSFORM: scale(0.95)
        
        Riduce pulsante al 95% dimensione.
        Simula effetto "premuto" fisicamente.
    --}}
    transform: scale(0.95);
    
    {{-- 
        PROPRIETÀ CSS: transition
        
        Definisce transizione fluida per cambiamenti proprietà.
        
        SINTASSI:
        transition: [property] [duration] [timing-function];
        
        VALORI:
        - transform: proprietà da animare
        - 0.1s: durata 100 millisecondi (veloce)
        - ease: timing function predefinita
        
        FUNZIONAMENTO:
        Quando transform cambia, non cambia istantaneamente
        ma si anima gradualmente in 0.1 secondi.
    --}}
    transition: transform 0.1s ease;
}

/* Tooltip di errore */
{{-- 
    REGOLA CSS: .error-tooltip .tooltip-inner
    LINGUAGGIO: CSS
    
    Selettore discendente (descendant selector).
    
    SINTASSI:
    .parent .child { ... }
    
    SIGNIFICATO:
    Seleziona elementi con classe 'tooltip-inner' che sono
    discendenti (a qualsiasi livello) di elementi con classe 'error-tooltip'.
    
    HTML ESEMPIO:
    <div class="error-tooltip">
        <div class="tooltip">
            <div class="tooltip-inner">Testo</div>  <!-- SELEZIONATO -->
        </div>
    </div>
--}}
.error-tooltip .tooltip-inner {
    {{-- 
        Sfondo rosso per tooltip errore.
        #dc3545 è il colore 'danger' di Bootstrap.
    --}}
    background-color: #dc3545;
    
    {{-- 
        Testo bianco per contrasto.
        #fff è abbreviazione di #ffffff (bianco puro).
    --}}
    color: #fff;
}

/* Form focus migliorato */
{{-- 
    REGOLA CSS: .form-control:focus
    LINGUAGGIO: CSS
    
    Pseudo-classe :focus
    
    FUNZIONAMENTO:
    - Si applica quando elemento ha il focus (è selezionato)
    - Focus si ottiene con: click, tab, focus() JavaScript
    - Attivo mentre utente interagisce con input
    
    UTILIZZO:
    Migliora UX evidenziando campo attivo.
--}}
.form-control:focus {
    {{-- 
        Bordo blu quando input è in focus.
        #0d6efd è il colore 'primary' di Bootstrap.
    --}}
    border-color: #0d6efd;
    
    {{-- 
        PROPRIETÀ CSS: box-shadow
        
        Ombra esterna elemento.
        
        SINTASSI:
        box-shadow: [offset-x] [offset-y] [blur-radius] [spread-radius] [color];
        
        VALORI:
        - 0 0: nessun offset (ombra centrata)
        - 0: nessun blur (bordo netto)
        - 0.25rem: spread radius (espansione ombra)
        - rgba(13, 110, 253, 0.25): colore con opacità 25%
        
        FUNZIONE rgba():
        rgba(red, green, blue, alpha)
        - red: 13
        - green: 110
        - blue: 253
        - alpha: 0.25 (25% opacità)
        
        EFFETTO:
        Crea "alone" blu brillante attorno input focalizzato.
    --}}
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    
    {{-- 
        Ingrandisce leggermente input (102%).
        Attira attenzione su campo attivo.
    --}}
    transform: scale(1.02);
    
    {{-- 
        PROPRIETÀ CSS: transition
        
        Shorthand per multiple transizioni.
        
        VALORE: all 0.3s ease
        - all: tutte le proprietà cambiate
        - 0.3s: durata 300ms
        - ease: timing function morbida
        
        EFFETTO:
        Cambiamenti colore, ombra, scala avvengono gradualmente.
    --}}
    transition: all 0.3s ease;
}

/* Alert personalizzati */
{{-- 
    REGOLA CSS: .alert
    
    Stili per componente alert Bootstrap.
    Sovrascrive/estende stili predefiniti.
--}}
.alert {
    {{-- 
        PROPRIETÀ CSS: border-radius
        
        Arrotonda angoli elemento.
        
        VALORE: 0.5rem
        - rem: unità relativa a root font-size
        - 0.5rem ≈ 8px (se root font-size = 16px)
        
        VALORI POSSIBILI:
        - 0: angoli quadrati
        - 50%: cerchio (se elemento quadrato)
        - px, em, rem: unità assolute/relative
    --}}
    border-radius: 0.5rem;
    
    {{-- 
        PROPRIETÀ CSS: box-shadow
        
        Ombra per effetto "elevazione".
        
        VALORI:
        - 0: offset-x (nessuno spostamento orizzontale)
        - 0.25rem: offset-y (spostamento verso basso)
        - 0.5rem: blur-radius (sfocatura)
        - rgba(0, 0, 0, 0.1): nero semi-trasparente (10% opacità)
        
        EFFETTO:
        Alert appare sollevato dalla pagina.
    --}}
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    
    {{-- 
        Animazione slideInRight all'apparizione alert.
        - 0.5s: durata mezzo secondo
        - ease-out: rallenta verso la fine
    --}}
    animation: slideInRight 0.5s ease-out;
}

{{-- 
    Animazione slideInRight per alert.
    Alert entra scorrendo da destra verso sinistra.
--}}
@keyframes slideInRight {
    {{-- 
        KEYFRAME: from
        
        Alternativa a 0%.
        Stato iniziale animazione.
    --}}
    from {
        {{-- 
            Opacità 0: completamente invisibile.
        --}}
        opacity: 0;
        
        {{-- 
            FUNZIONE TRANSFORM: translateX(100%)
            
            Trasla elemento sull'asse X.
            
            VALORE: 100%
            - Percentuale relativa a larghezza elemento
            - 100% = completamente fuori schermo a destra
            
            ALTRE FUNZIONI TRANSLATE:
            - translateY(): asse verticale
            - translate(x, y): entrambi assi
        --}}
        transform: translateX(100%);
    }
    
    {{-- 
        KEYFRAME: to
        
        Alternativa a 100%.
        Stato finale animazione.
    --}}
    to {
        {{-- 
            Opacità 1: completamente visibile.
        --}}
        opacity: 1;
        
        {{-- 
            translateX(0): posizione normale.
            Alert è completamente entrato nello schermo.
        --}}
        transform: translateX(0);
    }
}

/* Responsive per mobile */
{{-- 
    DIRETTIVA CSS: @media
    LINGUAGGIO: CSS
    
    Media query per design responsive.
    
    SINTASSI:
    @media [type] and ([feature]: [value]) {
        /* regole CSS */
    }
    
    QUERY: (max-width: 768px)
    - Si applica quando larghezza viewport ≤ 768px
    - Tipicamente dispositivi mobile in portrait
    
    BREAKPOINTS COMUNI:
    - 576px: mobile portrait
    - 768px: tablet portrait
    - 992px: tablet landscape / desktop small
    - 1200px: desktop
    - 1400px: desktop large
--}}
@media (max-width: 768px) {
    {{-- 
        Su mobile, riduce dimensione spinner input.
    --}}
    .loading-input {
        background-size: 16px;
        padding-right: 35px;
    }
    
    {{-- 
        REGOLA CSS: .alert
        
        Sovrascrive stili alert su mobile.
        
        NOTA: Più specifico di .alert precedente quando @media attiva.
        CSS usa specificità e ordine per determinare quale regola vince.
    --}}
    .alert {
        {{-- 
            PROPRIETÀ CSS: position
            
            Tipo di posizionamento elemento.
            
            VALORE: static
            - Posizionamento normale (flusso documento)
            - Annulla qualsiasi position: absolute/fixed precedente
            
            !important: forza priorità regola.
            
            VALORI POSITION:
            - static: normale (default)
            - relative: relativo a posizione normale
            - absolute: relativo a parent posizionato
            - fixed: relativo a viewport
            - sticky: misto relative/fixed
        --}}
        position: static !important;
        
        {{-- 
            PROPRIETÀ CSS: margin
            
            Margine esterno elemento.
            
            VALORE: 0.5rem
            - Margine uguale su tutti i lati
            
            SINTASSI:
            margin: [all];
            margin: [vertical] [horizontal];
            margin: [top] [right] [bottom] [left];
        --}}
        margin: 0.5rem;
        
        {{-- 
            PROPRIETÀ CSS: width
            
            Larghezza elemento.
            
            VALORE: auto
            - Larghezza calcolata automaticamente
            - Per elementi block: 100% del contenitore
            - Per elementi inline: larghezza contenuto
            
            !important: annulla eventuali width fisse precedenti.
        --}}
        width: auto !important;
        
        {{-- 
            PROPRIETÀ CSS: max-width
            
            Larghezza massima elemento.
            
            VALORE: none
            - Nessun limite massimo
            - Annulla eventuali max-width precedenti
        --}}
        max-width: none !important;
    }
}
</style>

{{-- 
    DIRETTIVA BLADE: @endpush
    LINGUAGGIO: Blade
    
    Chiude blocco @push('styles').
    Tutto tra @push('styles') e @endpush viene aggiunto allo stack 'styles'.
--}}
@endpush

{{-- 
    ============================================================================
    FINE FILE: tecnico-dashboard.blade.php
    ============================================================================
    
    RIEPILOGO STRUTTURA:
    
    1. DIRETTIVE BLADE:
       - @extends: eredita layout
       - @section: definisce contenuto sezione
       - @if/@foreach: logica condizionale/iterativa
       - @push: aggiunge a stack (scripts/styles)
       - {{ }}: output escapato
       - @json(): serializza PHP→JSON
    
    2. HTML:
       - Struttura semantica (header, section, table, etc.)
       - Bootstrap grid system (container, row, col-*)
       - Form elementi (input, button, select, etc.)
    
    3. BOOTSTRAP CSS:
       - Classi utility (mt-*, mb-*, d-flex, etc.)
       - Componenti (card, alert, badge, btn, etc.)
       - Sistema responsive (col-lg-*, col-md-*, etc.)
    
    4. JAVASCRIPT:
       - Inizializzazione dati pagina (window.PageData)
       - Blade passa dati PHP a JavaScript via @json()
    
    5. CSS PERSONALIZZATO:
       - Animazioni (@keyframes)
       - Effetti hover/focus
       - Media queries responsive
       - Stili specifici componenti
    
    FLUSSO RENDERING:
    1. Laravel processa Blade (server-side)
    2. Genera HTML + CSS + JavaScript
    3. Invia al browser client
    4. Browser renderizza e esegue JavaScript
    
    SICUREZZA:
    - {{ }}: auto-escape HTML (previene XSS)
    - @json(): escape sicuro JSON
    - CSRF token (se presente form POST)
    
    PERFORMANCE:
    - Lazy loading dati (solo se isset)
    - Limitazione risultati (take(5), take(6))
    - CSS/JS inline (nessuna richiesta HTTP extra)
--}}