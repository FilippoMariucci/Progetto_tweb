{{-- 
    ===================================================================
    HOMEPAGE SISTEMA ASSISTENZA TECNICA
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/home.blade.php
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    DESCRIZIONE: Pagina principale pubblica del sistema di assistenza tecnica
                 Accessibile a tutti gli utenti (autenticati e non)
    
    FUNZIONALITÀ:
    - Hero section con statistiche principali
    - Form di ricerca rapida prodotti con supporto wildcards
    - Informazioni dettagliate sull'azienda
    - Visualizzazione categorie prodotti con contatori
    - Sezioni di accesso differenziate per livelli (Tecnico/Staff/Admin)
    - Elenco centri assistenza principali
    - Orari di servizio
    - Statistiche globali del sistema
    - Call to action per assistenza
    - Sezione vantaggi/certificazioni
    ===================================================================
--}}

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Estende il layout principale dell'applicazione
    PARAMETRO: 'layouts.app' - template di base con navbar, footer, scripts
--}}
@extends('layouts.app')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Definisce il titolo della pagina per il tag <title> del browser
    PARAMETRO: 'Home - Sistema Assistenza Tecnica' - titolo completo
--}}
@section('title', 'Home - Sistema Assistenza Tecnica')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Inizio della sezione contenuto principale
    SCOPO: Tutto il contenuto fino a @endsection verrà inserito nel layout
--}}
@section('content')
{{-- 
    LINGUAGGIO: HTML + Bootstrap
    FUNZIONE: Container fluid per larghezza massima della pagina
    CLASSE container-fluid: usa tutta la larghezza disponibile dello schermo
--}}
<div class="container-fluid">
    
    {{-- === HERO SECTION === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap
        FUNZIONE: Sezione hero principale con sfondo colorato e informazioni chiave
        TAG <section>: elemento semantico HTML5 per sezioni della pagina
        CLASSE hero-section: classe personalizzata definita negli stili @push('styles')
        CLASSE py-5: padding verticale di 5 unità Bootstrap (top e bottom)
        CLASSE mb-5: margin-bottom di 5 unità per spaziare dalla sezione successiva
    --}}
    <section class="hero-section py-5 mb-5">
        {{-- 
            LINGUAGGIO: HTML + Bootstrap
            FUNZIONE: Container standard per centrare e limitare la larghezza
            CLASSE container: larghezza responsive con margini automatici
        --}}
        <div class="container">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia responsive a 2 colonne
                CLASSE row: contenitore del sistema grid Bootstrap
                CLASSE align-items-center: allinea verticalmente al centro gli elementi
            --}}
            <div class="row align-items-center">
                {{-- 
                    COLONNA SINISTRA: TESTO E CALL TO ACTION
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Colonna principale con titolo, descrizione e pulsanti
                    CLASSE col-lg-6: occupa 6/12 colonne su schermi large (50%)
                --}}
                <div class="col-lg-6">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Titolo principale della pagina
                        CLASSE display-4: dimensione grande del titolo (Bootstrap typography)
                        CLASSE fw-bold: font-weight bold
                        CLASSE mb-4: margin-bottom di 4 unità
                    --}}
                    <h1 class="display-4 fw-bold mb-4">
                        Assistenza Tecnica
                        {{-- 
                            LINGUAGGIO: HTML
                            FUNZIONE: Span per colorare una parte del titolo
                            CLASSE text-warning: colore giallo/arancione (Bootstrap)
                        --}}
                        <span class="text-warning">Professionale</span>
                    </h1>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Sottotitolo descrittivo del sistema
                        CLASSE lead: stile Bootstrap per paragrafi introduttivi (font più grande)
                        CLASSE mb-4: margin-bottom di 4 unità
                    --}}
                    <p class="lead mb-4">
                        Sistema completo per la gestione dell'assistenza tecnica sui nostri elettrodomestici. 
                        Accedi a soluzioni rapide per i malfunzionamenti più comuni e trova il centro assistenza più vicino.
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Flexbox
                        FUNZIONE: Container per i pulsanti call-to-action
                        CLASSE d-flex: attiva flexbox
                        CLASSE flex-wrap: permette il wrapping su più righe se necessario
                        CLASSE gap-3: spaziatura di 3 unità tra gli elementi
                        CLASSE mb-4: margin-bottom di 4 unità
                    --}}
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        {{-- 
                            PULSANTE 1: CATALOGO PRODOTTI
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Link al catalogo prodotti pubblico
                            HELPER route(): genera URL per la route 'prodotti.pubblico.index'
                            ROUTE: corrisponde al metodo index() del controller pubblico prodotti
                            CLASSE btn-warning: pulsante giallo (colore principale del brand)
                            CLASSE btn-lg: dimensione grande del pulsante
                        --}}
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-box me-2"></i>Esplora Catalogo
                        </a>
                        
                        {{-- 
                            PULSANTE 2: CENTRI ASSISTENZA
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Link all'elenco dei centri di assistenza
                            HELPER route(): genera URL per 'centri.index'
                            CLASSE btn-outline-light: pulsante con bordo bianco (su sfondo colorato)
                            CLASSE btn-lg: dimensione grande
                        --}}
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-geo-alt me-2"></i>Trova Centro Assistenza
                        </a>
                    </div>
                </div>
                
                {{-- 
                    COLONNA DESTRA: STATISTICHE HERO
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Colonna con icona principale e statistiche in 4 box
                    CLASSE col-lg-6: occupa 6/12 colonne su schermi large (50%)
                    CLASSE text-center: allinea tutto il contenuto al centro
                --}}
                <div class="col-lg-6 text-center">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Icons
                        FUNZIONE: Icona grande degli strumenti per rappresentare l'assistenza
                        CLASSE bi-tools: icona specifica di Bootstrap Icons
                        CLASSE display-1: dimensione massima dell'icona
                        CLASSE text-warning: colore giallo
                        CLASSE mb-4: margin-bottom di 4 unità
                    --}}
                    <i class="bi bi-tools display-1 text-warning mb-4"></i>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Grid
                        FUNZIONE: Griglia per le 4 statistiche principali
                        CLASSE stats-hero: classe personalizzata per styling
                    --}}
                    <div class="row text-center stats-hero">
                        {{-- 
                            STATISTICA 1: PRODOTTI TOTALI
                            LINGUAGGIO: HTML + Bootstrap
                            FUNZIONE: Box con numero prodotti totali
                            CLASSE col-3: occupa 3/12 colonne (25% - 4 box per riga)
                        --}}
                        <div class="col-3">
                            <div class="stat-item">
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP) + HTML
                                    FUNZIONE: Visualizza il numero totale di prodotti
                                    VARIABILE $stats['prodotti_totali']: valore dall'array passato dal controller
                                    OPERATORE ??: null coalescing - fornisce '150+' come fallback
                                    SCOPO: Mostrare dato reale o valore predefinito se non disponibile
                                --}}
                                <h3 class="h1 fw-bold text-light">{{ $stats['prodotti_totali'] ?? '150+' }}</h3>
                                <p class="mb-0 small">Prodotti</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 2: CENTRI TOTALI
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Box con numero centri assistenza totali
                        --}}
                        <div class="col-3">
                            <div class="stat-item">
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero centri assistenza
                                    FALLBACK: '25+' se il dato non esiste
                                --}}
                                <h3 class="h1 fw-bold text-light">{{ $stats['centri_totali'] ?? '25+' }}</h3>
                                <p class="mb-0 small">Centri</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 3: SOLUZIONI TOTALI
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Box con numero soluzioni tecniche disponibili
                        --}}
                        <div class="col-3">
                            <div class="stat-item">
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero soluzioni/malfunzionamenti risolti
                                    FALLBACK: '500+' se il dato non esiste
                                --}}
                                <h3 class="h1 fw-bold text-light">{{ $stats['soluzioni_totali'] ?? '500+' }}</h3>
                                <p class="mb-0 small">Soluzioni</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 4: TECNICI TOTALI
                            LINGUAGGIO: Blade Template + HTML
                            FUNZIONE: Box con numero tecnici specializzati
                        --}}
                        <div class="col-3">
                            <div class="stat-item">
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero tecnici nel sistema
                                    FALLBACK: '50+' se il dato non esiste
                                --}}
                                <h3 class="h1 fw-bold text-light">{{ $stats['tecnici_totali'] ?? '50+' }}</h3>
                                <p class="mb-0 small">Tecnici</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 
        LINGUAGGIO: HTML + Bootstrap
        FUNZIONE: Container standard per il resto del contenuto
        NOTA: Dal qui in poi usiamo container normale per centrare il contenuto
    --}}
    <div class="container">
        
        {{-- === RICERCA RAPIDA === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione per il form di ricerca prodotti
            CLASSE mb-5: margin-bottom per spaziare dalla sezione successiva
        --}}
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card contenitore per il form di ricerca
                CLASSE card-custom: classe personalizzata per stile uniforme
                CLASSE shadow-sm: ombreggiatura leggera
            --}}
            <div class="card card-custom shadow-sm">
                <div class="card-body p-4">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Titolo della sezione ricerca
                        CLASSE h3: dimensione ridotta rispetto a h2
                        CLASSE text-center: centra il testo
                    --}}
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida Prodotti
                    </h2>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Form per la ricerca prodotti
                        ATTRIBUTO action: URL di destinazione del form (route prodotti.pubblico.index)
                        ATTRIBUTO method: metodo HTTP GET (parametri nell'URL)
                        ATTRIBUTO id: identificatore per JavaScript
                        CLASSE row g-3: griglia con gap di 3 unità
                    --}}
                    <form action="{{ route('prodotti.pubblico.index') }}" method="GET" class="row g-3" id="search-form">
                        {{-- 
                            CAMPO 1: TERMINE DI RICERCA
                            LINGUAGGIO: HTML + Bootstrap
                            FUNZIONE: Input di testo per cercare prodotti
                            CLASSE col-md-6: occupa 6/12 colonne su schermi medi
                        --}}
                        <div class="col-md-6">
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Container posizionamento relativo per icona interna
                                CLASSE position-relative: permette posizionamento assoluto dei figli
                            --}}
                            <div class="position-relative">
                                {{-- 
                                    LINGUAGGIO: HTML + Blade
                                    FUNZIONE: Campo input di testo per la ricerca
                                    ATTRIBUTI:
                                    - type="text": tipo di input testuale
                                    - name="search": nome parametro GET inviato al server
                                    - placeholder: testo di suggerimento
                                    - value: valore precompilato se presente nella query string
                                    - id: identificatore per JavaScript
                                    - autocomplete="off": disabilita suggerimenti browser
                                    
                                    HELPER request('search'): recupera il valore del parametro 'search' dalla query string
                                    SCOPO: Mantenere il termine cercato dopo il submit del form
                                --}}
                                <input type="text" 
                                       class="form-control form-control-lg pe-5" 
                                       name="search" 
                                       placeholder="Cerca prodotto (es: lavatrice, lav*)"
                                       value="{{ request('search') }}"
                                       id="search-input"
                                       autocomplete="off">
                                {{-- 
                                    LINGUAGGIO: HTML + Bootstrap
                                    FUNZIONE: Icona di ricerca posizionata all'interno del campo
                                    CLASSE position-absolute: posizionamento assoluto
                                    CLASSE top-50: posiziona al 50% dall'alto
                                    CLASSE end-0: allinea al bordo destro
                                    CLASSE translate-middle-y: centra verticalmente
                                    CLASSE me-3: margin-end (destra) di 3 unità
                                --}}
                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                            </div>
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Testo di aiuto sotto il campo input
                                CLASSE form-text: stile Bootstrap per testi di help
                            --}}
                            <div class="form-text">
                                <i class="bi bi-lightbulb me-1"></i>
                                Usa * alla fine per ricerche parziali (es: "lav*" per lavatrici, lavastoviglie, ecc.)
                            </div>
                        </div>
                        {{-- 
                            CAMPO 2: SELECT CATEGORIA
                            LINGUAGGIO: HTML + Bootstrap
                            FUNZIONE: Dropdown per filtrare per categoria prodotto
                            CLASSE col-md-4: occupa 4/12 colonne su schermi medi
                        --}}
                        <div class="col-md-4">
                            {{-- 
                                LINGUAGGIO: HTML + Blade
                                FUNZIONE: Select dropdown per selezione categoria
                                ATTRIBUTO name="categoria": nome parametro GET
                                ATTRIBUTO id: identificatore per JavaScript
                                CLASSE form-select-lg: dimensione grande del select
                            --}}
                            <select name="categoria" class="form-select form-select-lg" id="categoria-select">
                                {{-- 
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Opzione di default per mostrare tutte le categorie
                                    ATTRIBUTO value="": valore vuoto = nessun filtro
                                --}}
                                <option value="">Tutte le categorie</option>
                                
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Verifica se esistono statistiche categorie da visualizzare
                                    CONTROLLO isset(): verifica esistenza variabile
                                    FUNZIONE count(): conta elementi nell'array
                                    OPERATORE &&: AND logico
                                    VARIABILE $categorie_stats: array con contatori per categoria
                                --}}
                                @if(isset($categorie_stats) && count($categorie_stats) > 0)
                                    {{-- 
                                        LINGUAGGIO: Blade Template (PHP)
                                        FUNZIONE: Loop foreach per generare option per ogni categoria
                                        SINTASSI: @foreach(array as chiave => valore)
                                        $key: chiave categoria (es: 'lavatrice', 'forno')
                                        $info: array associativo con 'label' e 'count'
                                    --}}
                                    @foreach($categorie_stats as $key => $info)
                                        {{-- 
                                            LINGUAGGIO: HTML + Blade
                                            FUNZIONE: Opzione del select per una specifica categoria
                                            ATTRIBUTO value: valore inviato al server
                                            ATTRIBUTO selected: preseleziona l'opzione se corrisponde al filtro attuale
                                            
                                            CONDIZIONE {{ request('categoria') == $key ? 'selected' : '' }}:
                                            - request('categoria'): recupera valore parametro 'categoria' dalla query string
                                            - ==: confronto di uguaglianza
                                            - Operatore ternario ?: restituisce 'selected' se true, stringa vuota se false
                                            - SCOPO: Mantenere la categoria selezionata dopo il submit
                                        --}}
                                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE: Visualizza nome categoria e conteggio prodotti
                                                OPERATORE ??: fornisce label di fallback se non esiste
                                                FUNZIONE ucfirst(): capitalizza prima lettera
                                                ESEMPIO OUTPUT: "Lavatrici (15)" oppure "Forni (8)"
                                            --}}
                                            {{ $info['label'] ?? ucfirst($key) }} ({{ $info['count'] ?? 0 }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        {{-- 
                            CAMPO 3: PULSANTE CERCA
                            LINGUAGGIO: HTML + Bootstrap
                            FUNZIONE: Pulsante submit del form
                            CLASSE col-md-2: occupa 2/12 colonne su schermi medi
                        --}}
                        <div class="col-md-2">
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Pulsante per inviare il form di ricerca
                                ATTRIBUTO type="submit": tipo submit - invia il form
                                CLASSE w-100: width 100% (larghezza completa)
                            --}}
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-1"></i>Cerca
                            </button>
                        </div>
                    </form>
                    
                    {{-- 
                        RISULTATI RICERCA AJAX
                        LINGUAGGIO: HTML
                        FUNZIONE: Container per risultati ricerca dinamica via AJAX
                        ATTRIBUTO id: identificatore per JavaScript
                        ATTRIBUTO style: nascosto di default, mostrato da JavaScript
                        NOTA: I risultati vengono popolati dinamicamente via JavaScript
                    --}}
                    <div id="search-results" class="mt-3" style="display: none;">
                        <div class="search-results-container">
                            {{-- I risultati verranno inseriti qui dal JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === INFORMAZIONI AZIENDA === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione con informazioni dettagliate sull'azienda
        --}}
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia a 2 colonne per info azienda e certificazioni
                CLASSE row g-4: griglia con gap di 4 unità
            --}}
            <div class="row g-4">
                {{-- 
                    COLONNA SINISTRA: INFORMAZIONI AZIENDA
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card principale con dettagli azienda
                    CLASSE col-lg-8: occupa 8/12 colonne su schermi large
                --}}
                <div class="col-lg-8">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap
                        FUNZIONE: Card con altezza 100% per allinearsi con card adiacente
                        CLASSE h-100: height 100%
                    --}}
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body">
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Titolo della sezione azienda
                            --}}
                            <h2 class="h3 mb-4">
                                <i class="bi bi-building text-primary me-2"></i>
                                La Nostra Azienda
                            </h2>
                            
                            {{-- 
                                LINGUAGGIO: HTML + Blade
                                FUNZIONE: Paragrafo introduttivo dell'azienda
                                CLASSE lead: paragrafo in evidenza (Bootstrap)
                            --}}
                            <p class="lead">
                                <strong>TechSupport Pro</strong> è leader nel settore degli elettrodomestici da oltre 
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza anni di esperienza dell'azienda
                                    VARIABILE $stats['anni_esperienza']: dato dal controller
                                    OPERATORE ??: fornisce 30 come fallback
                                --}}
                                <strong>{{ $stats['anni_esperienza'] ?? 30 }} anni</strong>, 
                                con una rete capillare di centri assistenza su tutto il territorio nazionale.
                            </p>
                            
                            {{-- 
                                LINGUAGGIO: HTML + Bootstrap Grid
                                FUNZIONE: Griglia per sede e contatti
                                CLASSE row mb-4: griglia con margin-bottom
                            --}}
                            <div class="row mb-4">
                                {{-- 
                                    SOTTOSEZIONE: SEDE PRINCIPALE
                                    LINGUAGGIO: HTML + Bootstrap
                                    FUNZIONE: Informazioni sulla sede legale
                                    CLASSE col-md-6: metà larghezza su schermi medi
                                --}}
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h5 class="mb-3">
                                            <i class="bi bi-geo-alt text-primary me-2"></i>
                                            Sede Principale
                                        </h5>
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Indirizzo completo della sede
                                            NOTA: Dati statici hardcoded (potrebbero venire da database)
                                        --}}
                                        <p class="mb-1">Via dell'Industria, 123</p>
                                        <p class="mb-1">60121 Ancona (AN)</p>
                                        <p class="mb-3">Italia</p>
                                    </div>
                                </div>
                                
                                {{-- 
                                    SOTTOSEZIONE: CONTATTI
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Informazioni di contatto (telefono, email)
                                --}}
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h5 class="mb-3">
                                            <i class="bi bi-telephone text-primary me-2"></i>
                                            Contatti
                                        </h5>
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Numero di telefono cliccabile
                                            ATTRIBUTO href="tel:+390711234567": protocollo tel per chiamate dirette
                                            CLASSE text-decoration-none: rimuove sottolineatura dal link
                                        --}}
                                        <p class="mb-1">
                                            <strong>Tel:</strong> 
                                            <a href="tel:+390711234567" class="text-decoration-none">+39 071 123 4567</a>
                                        </p>
                                        
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Email info cliccabile
                                            ATTRIBUTO href="mailto:info@techsupportpro.it": protocollo mailto per aprire client email
                                        --}}
                                        <p class="mb-1">
                                            <strong>Email:</strong> 
                                            <a href="mailto:info@techsupportpro.it" class="text-decoration-none">info@techsupportpro.it</a>
                                        </p>
                                        
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Email assistenza cliccabile
                                        --}}
                                        <p class="mb-3">
                                            <strong>Assistenza:</strong> 
                                            <a href="mailto:assistenza@techsupportpro.it" class="text-decoration-none">assistenza@techsupportpro.it</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- 
                                LINGUAGGIO: Blade Template + HTML
                                FUNZIONE: Link per approfondire le informazioni azienda
                                HELPER route(): genera URL per la route 'azienda'
                            --}}
                            <a href="{{ route('azienda') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Scopri di più
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- 
                    COLONNA DESTRA: CERTIFICAZIONI E QUALITÀ
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card con certificazioni e badge qualità
                    CLASSE col-lg-4: occupa 4/12 colonne su schermi large
                --}}
                <div class="col-lg-4">
                    <div class="card card-custom h-100 shadow-sm">
                        {{-- 
                            LINGUAGGIO: HTML
                            FUNZIONE: Corpo card centrato con certificazioni
                            CLASSE text-center: allinea tutto al centro
                        --}}
                        <div class="card-body text-center">
                            <h3 class="h4 mb-4">
                                <i class="bi bi-award text-warning me-2"></i>
                                Certificazioni e Qualità
                            </h3>
                            
                            {{-- 
                                LINGUAGGIO: HTML + Bootstrap Grid
                                FUNZIONE: Griglia 2x2 per 4 badge certificazione
                                CLASSE row g-3: griglia con gap di 3 unità
                            --}}
                            <div class="row g-3">
                                {{-- 
                                    BADGE 1: ISO 9001
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Badge certificazione qualità ISO 9001
                                    CLASSE col-6: metà larghezza (2 badge per riga)
                                --}}
                                <div class="col-6">
                                    {{-- 
                                        LINGUAGGIO: HTML + Bootstrap
                                        FUNZIONE: Container certificazione con padding e sfondo
                                        CLASSE p-3: padding di 3 unità
                                        CLASSE bg-light: sfondo grigio chiaro
                                        CLASSE rounded: bordi arrotondati
                                    --}}
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-shield-check display-6 text-success mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">ISO 9001</p>
                                        <small class="text-muted">Qualità</small>
                                    </div>
                                </div>
                                
                                {{-- 
                                    BADGE 2: ECO-FRIENDLY
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Badge certificazione ambientale
                                --}}
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-leaf display-6 text-success mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">Eco-Friendly</p>
                                        <small class="text-muted">Ambiente</small>
                                    </div>
                                </div>
                                
                                {{-- 
                                    BADGE 3: 5 STELLE
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Badge valutazione clienti
                                --}}
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-star-fill display-6 text-warning mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">5 Stelle</p>
                                        <small class="text-muted">Valutazione</small>
                                    </div>
                                </div>
                                
                                {{-- 
                                    BADGE 4: SUPPORTO 24/7
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Badge supporto continuo
                                --}}
                                <div class="col-6">
                                    <div class="certification-item p-3 bg-light rounded">
                                        <i class="bi bi-headset display-6 text-info mb-2"></i>
                                        <p class="mt-2 mb-0 small fw-bold">24/7</p>
                                        <small class="text-muted">Supporto</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === CATEGORIE PRODOTTI === --}}
        {{-- 
            LINGUAGGIO: Blade Template (PHP)
            FUNZIONE: Verifica se esistono categorie da visualizzare
            CONDIZIONE: isset() e count() > 0
            SCOPO: Mostrare la sezione solo se ci sono categorie disponibili
        --}}
        @if(isset($categorie_stats) && count($categorie_stats) > 0)
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML
                FUNZIONE: Titolo sezione categorie centrato
            --}}
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-grid text-primary me-2"></i>
                Categorie Prodotti
            </h2>
            
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia responsive per le card categorie
                CLASSE row g-4: griglia con gap di 4 unità
            --}}
            <div class="row g-4">
                {{-- 
                    LINGUAGGIO: Blade Template (PHP)
                    FUNZIONE: Loop foreach per iterare su ogni categoria
                    VARIABILI:
                    - $key: chiave della categoria (es. 'lavatrice')
                    - $info: array con 'label' e 'count'
                --}}
                @foreach($categorie_stats as $key => $info)
                    {{-- 
                        LINGUAGGIO: Blade + PHP
                        FUNZIONE: Blocco PHP per preparare i dati della categoria
                        DIRETTIVA @php: permette di eseguire codice PHP puro in Blade
                        SCOPO: Calcolare icona, label e conteggio per ogni categoria
                    --}}
                    @php
                        // LINGUAGGIO: PHP
                        // FUNZIONE: Definisce array associativo per mappare categorie a icone
                        // TIPO: array associativo chiave => valore
                        // SCOPO: Assegnare icone Bootstrap Icons specifiche per ogni tipo di prodotto
                        $icons = [
                            'lavatrice' => 'bi-water',
                            'lavastoviglie' => 'bi-droplet',
                            'forno' => 'bi-fire',
                            'frigorifero' => 'bi-snow',
                            'asciugatrice' => 'bi-wind',
                            'condizionatore' => 'bi-thermometer',
                            'microonde' => 'bi-lightning',
                            'aspirapolvere' => 'bi-fan',
                            'ferro_stiro' => 'bi-iron',
                            'piccoli_elettrodomestici' => 'bi-gear',
                            'elettrodomestici' => 'bi-house',
                            'climatizzazione' => 'bi-thermometer-half',
                            'cucina' => 'bi-cup-hot',
                            'lavanderia' => 'bi-water',
                            'riscaldamento' => 'bi-fire',
                            'altro' => 'bi-tools'
                        ];
                        
                        // LINGUAGGIO: PHP
                        // FUNZIONE: Recupera l'icona per la categoria corrente
                        // OPERATORE ??: null coalescing - usa 'bi-gear' se $key non esiste in $icons
                        $icon = $icons[$key] ?? 'bi-gear';
                        
                        // LINGUAGGIO: PHP
                        // FUNZIONE: Determina il label da visualizzare
                        // OPERATORE ??: usa label dall'array o genera da $key
                        // FUNZIONI:
                        // - ucfirst(): capitalizza prima lettera
                        // - str_replace(): sostituisce underscore con spazi
                        // ESEMPIO: 'ferro_stiro' diventa 'Ferro stiro'
                        $label = $info['label'] ?? ucfirst(str_replace('_', ' ', $key));
                        
                        // LINGUAGGIO: PHP
                        // FUNZIONE: Recupera il conteggio prodotti per la categoria
                        // OPERATORE ??: fornisce 0 come default
                        $count = $info['count'] ?? 0;
                    @endphp
                    
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Verifica se la categoria ha prodotti (count > 0)
                        SCOPO: Mostrare solo categorie con almeno un prodotto
                    --}}
                    @if($count > 0)
                        {{-- 
                            LINGUAGGIO: HTML + Bootstrap
                            FUNZIONE: Colonna responsive per card categoria
                            CLASSI:
                            - col-md-6: 2 colonne su tablet (50%)
                            - col-lg-3: 4 colonne su desktop (25%)
                        --}}
                        <div class="col-md-6 col-lg-3">
                            {{-- 
                                LINGUAGGIO: Blade Template + HTML
                                FUNZIONE: Link cliccabile che avvolge l'intera card
                                HELPER route(): genera URL per route 'prodotti.categoria'
                                PARAMETRO $key: passa la categoria come parametro route
                                ESEMPIO: route('prodotti.categoria', 'lavatrice') 
                                         -> /prodotti/categoria/lavatrice
                                CLASSE text-decoration-none: rimuove sottolineatura dal link
                            --}}
                            <a href="{{ route('prodotti.categoria', $key) }}" class="text-decoration-none">
                                {{-- 
                                    LINGUAGGIO: HTML + Bootstrap
                                    FUNZIONE: Card categoria cliccabile
                                    CLASSE category-card: classe custom per hover effects
                                    CLASSE h-100: altezza 100% per uniformità
                                --}}
                                <div class="card card-custom h-100 text-center category-card">
                                    <div class="card-body">
                                        {{-- 
                                            LINGUAGGIO: HTML + Blade
                                            FUNZIONE: Icona dinamica della categoria
                                            SINTASSI {{ $icon }}: stampa la classe icona calcolata nel blocco @php
                                            CLASSE display-4: dimensione grande
                                        --}}
                                        <i class="bi {{ $icon }} display-4 text-primary mb-3"></i>
                                        
                                        {{-- 
                                            LINGUAGGIO: Blade + HTML
                                            FUNZIONE: Nome della categoria
                                            VARIABILE $label: calcolata nel blocco @php
                                        --}}
                                        <h5 class="card-title">{{ $label }}</h5>
                                        
                                        {{-- 
                                            LINGUAGGIO: Blade + HTML
                                            FUNZIONE: Conteggio prodotti disponibili
                                            VARIABILE $count: numero prodotti in categoria
                                        --}}
                                        <p class="text-muted mb-0">
                                            <strong>{{ $count }}</strong> prodotti disponibili
                                        </p>
                                        
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Badge "Visualizza" per call-to-action
                                        --}}
                                        <div class="mt-3">
                                            <span class="badge bg-primary">Visualizza</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif
        {{-- === ACCESSO PER LIVELLI === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione che mostra le diverse aree di accesso per utenti registrati
            SCOPO: Presentare le funzionalità disponibili per Tecnici, Staff e Admin
        --}}
        <section class="mb-5">
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-people text-primary me-2"></i>
                Accesso per Operatori
            </h2>
            
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia responsive per 3 card (una per livello)
                CLASSE row g-4: griglia con gap di 4 unità
            --}}
            <div class="row g-4">
                
                {{-- === CARD TECNICI === --}}
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card informativa per tecnici specializzati (Livello 2)
                    CLASSE col-md-4: occupa 1/3 della larghezza su schermi medi
                --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        {{-- 
                            LINGUAGGIO: HTML
                            FUNZIONE: Corpo card centrato
                        --}}
                        <div class="card-body text-center">
                            {{-- Icona tecnico --}}
                            <i class="bi bi-person-gear display-4 text-info mb-3"></i>
                            
                            <h5 class="card-title">Tecnici Specializzati</h5>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Descrizione breve del ruolo tecnico
                            --}}
                            <p class="card-text">
                                Accesso completo a malfunzionamenti e soluzioni tecniche per tutti i prodotti del catalogo.
                            </p>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Lista non ordinata delle funzionalità tecnico
                                CLASSE list-unstyled: rimuove pallini della lista
                                CLASSE text-start: allinea testo a sinistra (override del text-center del parent)
                            --}}
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Visualizza tutti i malfunzionamenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Accesso alle soluzioni tecniche
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Segnala nuovi problemi
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Storico interventi personali
                                </li>
                            </ul>
                            
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Direttiva @guest per verificare se l'utente NON è autenticato
                                SCOPO: Mostrare pulsante "Accedi" agli utenti non loggati
                                SISTEMA: Laravel Authentication - verifica sessione utente
                            --}}
                            @guest
                                {{-- 
                                    LINGUAGGIO: Blade Template + HTML
                                    FUNZIONE: Link alla pagina di login per utenti non autenticati
                                    HELPER route('login'): genera URL per la route di login
                                --}}
                                <a href="{{ route('login') }}" class="btn btn-info">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Tecnico
                                </a>
                            {{-- 
                                LINGUAGGIO: Blade Template
                                FUNZIONE: Blocco @else eseguito se l'utente È autenticato
                            --}}
                            @else
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Verifica se l'utente autenticato ha livello >= 2
                                    FACADE Auth::user(): restituisce l'istanza User autenticato
                                    PROPRIETÀ livello_accesso: campo numerico (1-4) che indica il ruolo
                                    CONDIZIONE >= 2: Tecnico (2), Staff (3) o Admin (4)
                                --}}
                                @if(Auth::user()->livello_accesso >= 2)
                                    {{-- 
                                        LINGUAGGIO: Blade Template + HTML
                                        FUNZIONE: Link alla dashboard tecnico per utenti con accesso
                                        HELPER route('tecnico.dashboard'): genera URL dashboard tecnico
                                    --}}
                                    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Tecnico
                                    </a>
                                {{-- 
                                    LINGUAGGIO: Blade Template
                                    FUNZIONE: Altrimenti (utente autenticato ma senza permessi sufficienti)
                                --}}
                                @else
                                    {{-- 
                                        LINGUAGGIO: HTML
                                        FUNZIONE: Pulsante outline per utenti autenticati senza permessi
                                        SCOPO: Suggerire di accedere con account tecnico
                                    --}}
                                    <a href="{{ route('login') }}" class="btn btn-outline-info">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Tecnico
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                {{-- === CARD STAFF === --}}
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card informativa per staff aziendale (Livello 3)
                --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            {{-- Icona staff --}}
                            <i class="bi bi-person-badge display-4 text-warning mb-3"></i>
                            
                            <h5 class="card-title">Staff Aziendale</h5>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Descrizione del ruolo staff
                            --}}
                            <p class="card-text">
                                Gestione completa di malfunzionamenti e soluzioni per i prodotti assegnati al proprio reparto.
                            </p>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Lista funzionalità staff
                                NOTA: Include tutte le funzioni del tecnico + gestione contenuti
                            --}}
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Tutte le funzioni del Tecnico
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Crea nuovi malfunzionamenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Modifica e aggiorna soluzioni
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Statistiche e report attività
                                </li>
                            </ul>
                            
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Verifica stato autenticazione per mostrare pulsante appropriato
                            --}}
                            @guest
                                {{-- Utente NON autenticato: mostra pulsante accesso --}}
                                <a href="{{ route('login') }}" class="btn btn-warning">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Staff
                                </a>
                            @else
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Verifica se utente ha livello >= 3 (Staff o Admin)
                                --}}
                                @if(Auth::user()->livello_accesso >= 3)
                                    {{-- 
                                        LINGUAGGIO: Blade Template + HTML
                                        FUNZIONE: Link alla dashboard staff
                                        HELPER route('staff.dashboard'): URL dashboard staff
                                    --}}
                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-warning">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Staff
                                    </a>
                                @else
                                    {{-- Utente autenticato ma senza permessi staff --}}
                                    <a href="{{ route('login') }}" class="btn btn-outline-warning">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Staff
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                {{-- === CARD AMMINISTRATORI === --}}
                {{-- 
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card informativa per amministratori (Livello 4)
                --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 shadow-sm">
                        <div class="card-body text-center">
                            {{-- Icona admin --}}
                            <i class="bi bi-person-fill-gear display-4 text-danger mb-3"></i>
                            
                            <h5 class="card-title">Amministratori</h5>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Descrizione del ruolo amministratore
                            --}}
                            <p class="card-text">
                                Controllo completo del sistema: gestione utenti, prodotti, centri assistenza e configurazioni avanzate.
                            </p>
                            
                            {{-- 
                                LINGUAGGIO: HTML
                                FUNZIONE: Lista funzionalità amministratore
                                NOTA: Include tutte le funzioni precedenti + gestione sistema
                            --}}
                            <ul class="list-unstyled text-start mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Tutte le funzioni precedenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Gestione completa utenti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Amministrazione prodotti
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Statistiche e manutenzione
                                </li>
                            </ul>
                            
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Verifica stato autenticazione
                            --}}
                            @guest
                                {{-- Utente NON autenticato --}}
                                <a href="{{ route('login') }}" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi come Admin
                                </a>
                            @else
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Verifica se utente ha livello >= 4 (Solo Admin)
                                --}}
                                @if(Auth::user()->livello_accesso >= 4)
                                    {{-- 
                                        LINGUAGGIO: Blade Template + HTML
                                        FUNZIONE: Link alla dashboard amministratore
                                        HELPER route('admin.dashboard'): URL dashboard admin
                                    --}}
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Dashboard Admin
                                    </a>
                                @else
                                    {{-- Utente autenticato ma non admin --}}
                                    <a href="{{ route('login') }}" class="btn btn-outline-danger">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        Accedi come Admin
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- === CENTRI ASSISTENZA === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione con elenco centri assistenza e orari di servizio
        --}}
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia a 2 colonne per centri e orari
            --}}
            <div class="row g-4">
                {{-- 
                    COLONNA SINISTRA: ELENCO CENTRI
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Colonna con lista centri assistenza principali
                    CLASSE col-lg-6: occupa metà larghezza su schermi large
                --}}
                <div class="col-lg-6">
                    <h2 class="h3 mb-4">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Rete Centri Assistenza
                    </h2>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Paragrafo descrittivo della rete centri
                    --}}
                    <p class="lead">
                        I nostri centri di assistenza sono distribuiti su tutto il territorio nazionale 
                        per garantire un servizio rapido e professionale.
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Container per la lista centri
                    --}}
                    <div class="centri-list mb-4">
                        {{-- 
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Verifica se esistono centri principali da visualizzare
                            VARIABILE $centri_principali: Collection Eloquent di centri
                            CONTROLLO isset(): verifica esistenza
                            METODO count(): conta elementi della collection
                        --}}
                        @if(isset($centri_principali) && count($centri_principali) > 0)
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Loop foreach per iterare sui centri
                                VARIABILE $centro: istanza del Model CentroAssistenza
                            --}}
                            @foreach($centri_principali as $centro)
                                {{-- 
                                    LINGUAGGIO: HTML
                                    FUNZIONE: Container per singolo centro
                                --}}
                                <div class="centro-item mb-3">
                                    {{-- 
                                        LINGUAGGIO: HTML + Bootstrap
                                        FUNZIONE: Card centro con bordo colorato sinistro
                                        CLASSE border-start: bordo solo a sinistra
                                        CLASSE border-3: spessore bordo 3 unità
                                    --}}
                                    <div class="card border-start border-primary border-3 shadow-sm">
                                        <div class="card-body py-3">
                                            {{-- 
                                                LINGUAGGIO: Blade + HTML
                                                FUNZIONE: Nome del centro
                                                PROPRIETÀ $centro->nome: campo del Model CentroAssistenza
                                            --}}
                                            <h6 class="card-title mb-1 fw-bold">{{ $centro->nome }}</h6>
                                            
                                            {{-- 
                                                LINGUAGGIO: Blade + HTML
                                                FUNZIONE: Indirizzo completo del centro
                                                OPERATORE ??: fornisce fallback se indirizzo non esiste
                                            --}}
                                            <p class="card-text small text-muted mb-1">
                                                <i class="bi bi-geo-alt me-1 text-primary"></i>
                                                {{ $centro->indirizzo ?? 'Indirizzo non disponibile' }}, 
                                                {{ $centro->citta }} 
                                                {{-- 
                                                    LINGUAGGIO: Blade Template (PHP)
                                                    FUNZIONE: Mostra provincia se disponibile
                                                    PROPRIETÀ $centro->provincia: campo opzionale
                                                --}}
                                                @if($centro->provincia)
                                                    ({{ $centro->provincia }})
                                                @endif
                                            </p>
                                            
                                            {{-- 
                                                LINGUAGGIO: Blade Template (PHP)
                                                FUNZIONE: Mostra telefono se disponibile
                                            --}}
                                            @if($centro->telefono)
                                                {{-- 
                                                    LINGUAGGIO: HTML
                                                    FUNZIONE: Telefono cliccabile
                                                    ATTRIBUTO href="tel:...": protocollo per chiamate dirette
                                                --}}
                                                <p class="card-text small text-muted mb-0">
                                                    <i class="bi bi-telephone me-1 text-primary"></i>
                                                    <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                        {{ $centro->telefono }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        {{-- 
                            LINGUAGGIO: Blade Template
                            FUNZIONE: Blocco @else se non ci sono centri
                        --}}
                        @else
                            {{-- 
                                LINGUAGGIO: HTML + Bootstrap
                                FUNZIONE: Alert informativo quando non ci sono dati
                            --}}
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Informazioni sui centri assistenza in aggiornamento.
                            </div>
                        @endif
                    </div>
                    
                    {{-- 
                        LINGUAGGIO: Blade Template + HTML
                        FUNZIONE: Pulsante per visualizzare tutti i centri
                        HELPER route('centri.index'): genera URL per l'elenco completo
                    --}}
                    <a href="{{ route('centri.index') }}" class="btn btn-primary">
                        <i class="bi bi-geo-alt me-2"></i>Vedi Tutti i Centri
                    </a>
                </div>
                
                {{-- 
                    COLONNA DESTRA: ORARI DI SERVIZIO
                    LINGUAGGIO: HTML + Bootstrap
                    FUNZIONE: Card con orari di tutti i servizi
                --}}
                <div class="col-lg-6">
                    <div class="card card-custom shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-clock text-primary me-2"></i>
                                Orari di Servizio
                            </h4>
                            
                            {{-- 
                                LINGUAGGIO: HTML + Bootstrap Grid
                                FUNZIONE: Griglia 2 colonne per gli orari
                            --}}
                            <div class="row">
                                {{-- 
                                    COLONNA SINISTRA: ASSISTENZA TELEFONICA E DOMICILIO
                                    LINGUAGGIO: HTML
                                --}}
                                <div class="col-6">
                                    {{-- 
                                        SERVIZIO 1: ASSISTENZA TELEFONICA
                                        LINGUAGGIO: HTML
                                        FUNZIONE: Blocco con orari assistenza telefonica
                                    --}}
                                    <div class="service-time mb-4">
                                        <h6 class="fw-bold text-primary">Assistenza Telefonica</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 8:00-18:00
                                        </p>
                                        <p class="mb-3">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: 8:00-13:00
                                        </p>
                                    </div>
                                    
                                    {{-- 
                                        SERVIZIO 2: INTERVENTI A DOMICILIO
                                        LINGUAGGIO: HTML
                                        FUNZIONE: Blocco con orari interventi domicilio
                                    --}}
                                    <div class="service-time">
                                        <h6 class="fw-bold text-primary">Interventi a Domicilio</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 9:00-17:00
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: Su appuntamento
                                        </p>
                                    </div>
                                </div>
                                
                                {{-- 
                                    COLONNA DESTRA: CENTRI E SUPPORTO ONLINE
                                    LINGUAGGIO: HTML
                                --}}
                                <div class="col-6">
                                    {{-- 
                                        SERVIZIO 3: CENTRI ASSISTENZA
                                        LINGUAGGIO: HTML
                                        FUNZIONE: Blocco con orari apertura centri fisici
                                    --}}
                                    <div class="service-time mb-4">
                                        <h6 class="fw-bold text-primary">Centri Assistenza</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Lun-Ven: 8:30-17:30
                                        </p>
                                        <p class="mb-3">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Sab: 8:30-12:30
                                        </p>
                                    </div>
                                    
                                    {{-- 
                                        SERVIZIO 4: SUPPORTO ONLINE
                                        LINGUAGGIO: HTML
                                        FUNZIONE: Blocco per supporto 24/7 tramite portale
                                    --}}
                                    <div class="service-time">
                                        <h6 class="fw-bold text-primary">Supporto Online</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-clock me-1"></i>
                                            24/7 attraverso
                                        </p>
                                        <p class="mb-0">questo portale</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- === STATISTICHE === --}}
        {{-- 
            LINGUAGGIO: Blade Template (PHP)
            FUNZIONE: Verifica se esistono statistiche da visualizzare
            VARIABILE $stats: array con dati statistiche globali
        --}}
        @if(isset($stats) && count($stats) > 0)
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card grande con sfondo blu e statistiche in evidenza
                CLASSE bg-primary: sfondo blu Bootstrap
                CLASSE text-white: testo bianco
                CLASSE shadow-lg: ombreggiatura grande
            --}}
            <div class="card card-custom bg-primary text-white shadow-lg">
                <div class="card-body py-5">
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche del Sistema
                    </h2>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Grid
                        FUNZIONE: Griglia per 4 box statistiche
                        CLASSE row text-center: griglia con contenuto centrato
                    --}}
                    <div class="row text-center">
                        {{-- 
                            STATISTICA 1: PRODOTTI ATTIVI
                            LINGUAGGIO: HTML + Bootstrap
                            CLASSE col-md-3: 1/4 della larghezza su schermi medi
                        --}}
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-box display-4 mb-3"></i>
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero prodotti totali
                                    OPERATORE ??: fornisce 0 come fallback
                                --}}
                                <h3 class="h2 fw-bold">{{ $stats['prodotti_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Prodotti Attivi</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 2: SOLUZIONI DISPONIBILI
                            LINGUAGGIO: Blade + HTML
                        --}}
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-tools display-4 mb-3"></i>
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero soluzioni tecniche
                                --}}
                                <h3 class="h2 fw-bold">{{ $stats['soluzioni_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Soluzioni Disponibili</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 3: CENTRI ASSISTENZA
                            LINGUAGGIO: Blade + HTML
                        --}}
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-geo-alt display-4 mb-3"></i>
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero centri assistenza
                                --}}
                                <h3 class="h2 fw-bold">{{ $stats['centri_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Centri Assistenza</p>
                            </div>
                        </div>
                        
                        {{-- 
                            STATISTICA 4: TECNICI SPECIALIZZATI
                            LINGUAGGIO: Blade + HTML
                        --}}
                        <div class="col-md-3 mb-3">
                            <div class="stat-item">
                                <i class="bi bi-people display-4 mb-3"></i>
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Visualizza numero tecnici
                                --}}
                                <h3 class="h2 fw-bold">{{ $stats['tecnici_totali'] ?? 0 }}</h3>
                                <p class="mb-0">Tecnici Specializzati</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- === CALL TO ACTION === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione finale con invito all'azione
        --}}
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap
                FUNZIONE: Card con sfondo chiaro e call-to-action centrato
            --}}
            <div class="card card-custom bg-light shadow-sm">
                <div class="card-body text-center py-5">
                    <h2 class="h3 mb-4">Hai bisogno di assistenza?</h2>
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Testo descrittivo del servizio
                    --}}
                    <p class="lead mb-4 text-muted">
                        Il nostro team di esperti è sempre pronto ad aiutarti a risolvere 
                        qualsiasi problema con i tuoi elettrodomestici.
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap Flexbox
                        FUNZIONE: Container flexbox per i pulsanti call-to-action
                        CLASSE gap-3: spaziatura tra pulsanti
                    --}}
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        {{-- 
                            PULSANTE 1: CERCA SOLUZIONE
                            LINGUAGGIO: Blade + HTML
                            FUNZIONE: Link al catalogo prodotti pubblico
                        --}}
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-search me-2"></i>Cerca Soluzione
                        </a>
                        
                        {{-- 
                            PULSANTE 2: CONTATTA CENTRO
                            LINGUAGGIO: Blade + HTML
                            FUNZIONE: Link all'elenco centri assistenza
                        --}}
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-telephone me-2"></i>Contatta Centro
                        </a>
                        
                        {{-- 
                            PULSANTE 3: ACCESSO O DASHBOARD
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Pulsante condizionale basato su autenticazione
                        --}}
                        @guest
                            {{-- 
                                LINGUAGGIO: Blade + HTML
                                FUNZIONE: Link al login per utenti non autenticati
                            --}}
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-person me-2"></i>Accedi al Sistema
                            </a>
                        @else
                            {{-- 
                                LINGUAGGIO: Blade + HTML
                                FUNZIONE: Link alla dashboard per utenti autenticati
                                HELPER route('dashboard'): URL dashboard personale
                            --}}
                            <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>Vai alla Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        {{-- === FOOTER INFORMATIVO === --}}
        {{-- 
            LINGUAGGIO: HTML
            FUNZIONE: Sezione finale con 3 vantaggi principali del servizio
        --}}
        <section class="mb-5">
            {{-- 
                LINGUAGGIO: HTML + Bootstrap Grid
                FUNZIONE: Griglia per 3 card vantaggi
            --}}
            <div class="row g-4">
                {{-- 
                    VANTAGGIO 1: GARANZIA ESTESA
                    LINGUAGGIO: HTML + Bootstrap
                --}}
                <div class="col-md-4">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Card trasparente (senza bordo)
                        CLASSE border-0: nessun bordo
                        CLASSE bg-transparent: sfondo trasparente
                    --}}
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check display-4 text-success mb-3"></i>
                            <h5 class="card-title">Garanzia Estesa</h5>
                            <p class="card-text text-muted">
                                Tutti i nostri interventi sono coperti da garanzia estesa 
                                per garantire la massima tranquillità.
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- 
                    VANTAGGIO 2: INTERVENTO RAPIDO
                    LINGUAGGIO: HTML
                --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-charge display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Intervento Rapido</h5>
                            <p class="card-text text-muted">
                                Tempi di intervento ridotti grazie alla nostra rete 
                                capillare di tecnici specializzati.
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- 
                    VANTAGGIO 3: SODDISFAZIONE CLIENTE
                    LINGUAGGIO: HTML
                --}}
                <div class="col-md-4">
                    <div class="card card-custom h-100 border-0 bg-transparent">
                        <div class="card-body text-center">
                            <i class="bi bi-heart display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Soddisfazione Cliente</h5>
                            <p class="card-text text-muted">
                                La soddisfazione dei nostri clienti è la nostra priorità 
                                assoluta, con oltre il 95% di feedback positivi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la sezione 'content'
--}}
@endsection
{{-- === JAVASCRIPT PER FUNZIONALITÀ DINAMICHE === --}}
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Direttiva @push per aggiungere JavaScript allo stack 'scripts' del layout
    SCOPO: Inserire script specifici della homepage alla fine del <body>
--}}
@push('scripts')
<script>
// ===================================================================
// INIZIALIZZAZIONE OGGETTO GLOBALE PageData
// ===================================================================
// LINGUAGGIO: JavaScript
// FUNZIONE: Crea o estende l'oggetto globale window.PageData
// OPERATORE ||: OR logico - se PageData non esiste, crea oggetto vuoto
// SCOPO: Namespace globale per contenere dati della pagina accessibili da altri script
// OGGETTO window: oggetto globale del browser JavaScript
window.PageData = window.PageData || {};

// ===================================================================
// PASSAGGIO DATI DA PHP/BLADE A JAVASCRIPT
// ===================================================================
// LINGUAGGIO: JavaScript + Blade Template
// FUNZIONE: Trasferire dati dal backend PHP al frontend JavaScript
// SCOPO: Rendere disponibili i dati del controller alle funzioni JavaScript client-side

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica condizionale se esiste la variabile $prodotto
// SCOPO: Aggiungere al PageData solo se la variabile è definita nel controller
@if(isset($prodotto))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'oggetto prodotto a window.PageData
// HELPER @json(): converte oggetto/array PHP in formato JSON JavaScript
// PARAMETRO $prodotto: istanza del Model Prodotto o array
// OUTPUT: oggetto JavaScript con tutte le proprietà del prodotto
window.PageData.prodotto = @json($prodotto);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste collection/array prodotti
@if(isset($prodotti))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'array prodotti a window.PageData
// HELPER @json(): converte Collection Eloquent o array PHP in array JavaScript
// PARAMETRO $prodotti: Collection o array di prodotti
// OUTPUT: array JavaScript di oggetti prodotto
window.PageData.prodotti = @json($prodotti);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste singolo malfunzionamento
@if(isset($malfunzionamento))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'oggetto malfunzionamento a window.PageData
// HELPER @json(): serializza l'istanza Model in JSON
// PARAMETRO $malfunzionamento: istanza Model Malfunzionamento
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste collection malfunzionamenti
@if(isset($malfunzionamenti))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'array malfunzionamenti a window.PageData
// HELPER @json(): converte Collection in array JavaScript
// PARAMETRO $malfunzionamenti: Collection di malfunzionamenti
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste singolo centro assistenza
@if(isset($centro))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'oggetto centro a window.PageData
// PARAMETRO $centro: istanza Model CentroAssistenza
window.PageData.centro = @json($centro);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste collection centri
@if(isset($centri))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'array centri a window.PageData
// PARAMETRO $centri: Collection di centri assistenza
window.PageData.centri = @json($centri);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esistono categorie
@if(isset($categorie))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'array categorie a window.PageData
// PARAMETRO $categorie: array o Collection di categorie prodotti
window.PageData.categorie = @json($categorie);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esistono membri staff
@if(isset($staffMembers))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'array staff members a window.PageData
// PARAMETRO $staffMembers: Collection di utenti staff
window.PageData.staffMembers = @json($staffMembers);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esistono statistiche
@if(isset($stats))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge l'oggetto statistiche a window.PageData
// PARAMETRO $stats: array associativo con statistiche del sistema
// ESEMPIO: {prodotti_totali: 150, centri_totali: 25, ...}
window.PageData.stats = @json($stats);
@endif

// LINGUAGGIO: Blade Template (PHP)
// FUNZIONE: Verifica se esiste utente autenticato
@if(isset($user))
// LINGUAGGIO: JavaScript + Blade
// FUNZIONE: Aggiunge i dati utente a window.PageData
// PARAMETRO $user: istanza Model User dell'utente corrente
// NOTA: Attenzione a non esporre dati sensibili (password già esclusa dal Model)
window.PageData.user = @json($user);
@endif

// LINGUAGGIO: JavaScript
// COMMENTO: Indicazione per possibili future estensioni
// SCOPO: Placeholder per aggiungere altri dati necessari in futuro
// Aggiungi altri dati che potrebbero servire...
</script>
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la direttiva @push('scripts')
--}}
@endpush

{{-- 
    NOTA IMPORTANTE:
    Il file continua con @push('styles') che contiene solo CSS.
    Come da requisiti dell'utente, NON vengono aggiunti commenti per gli stili CSS.
    La sezione CSS rimane invariata e senza commenti.
--}}

{{-- === STILI CSS PERSONALIZZATI PER LA HOMEPAGE === --}}
@push('styles')
<style>
/* === HERO SECTION === */
.hero-section {
    background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
    background-size: cover;
    pointer-events: none;
}

.stats-hero .stat-item {
    transition: transform 0.3s ease;
}

.stats-hero .stat-item:hover {
    transform: translateY(-5px);
}

/* === CARD PERSONALIZZATE === */
.card-custom {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-radius: 12px;
}

.card-custom:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

/* === CATEGORY CARDS === */
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
    overflow: hidden;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.category-card .card-body {
    padding: 2rem 1.5rem;
}

.category-card .display-4 {
    transition: transform 0.3s ease;
}

.category-card:hover .display-4 {
    transform: scale(1.1);
}

/* === SEARCH RESULTS === */
.search-results-container {
    max-height: 400px;
    overflow-y: auto;
}

.search-result-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.search-result-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* === CERTIFICATION ITEMS === */
.certification-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.certification-item:hover {
    background-color: #f8f9fa !important;
    transform: scale(1.05);
}

.certification-item .display-6 {
    transition: transform 0.3s ease;
}

.certification-item:hover .display-6 {
    transform: scale(1.1);
}

/* === INFO ITEMS === */
.info-item h5 {
    border-bottom: 2px solid transparent;
    padding-bottom: 0.5rem;
    transition: border-color 0.3s ease;
}

.info-item:hover h5 {
    border-bottom-color: #007bff;
}

/* === SERVICE TIME === */
.service-time {
    padding: 1rem;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.service-time:hover {
    background-color: #f8f9fa;
}

/* === CENTRI LIST === */
.centro-item {
    transition: transform 0.2s ease;
}

.centro-item:hover {
    transform: translateX(5px);
}

/* === ANIMAZIONI === */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease;
}

/* === SPINNER PERSONALIZZATO === */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* === PULSANTI === */
.btn {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 600;
}

/* === BADGE === */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.8rem;
}

/* === ICONE === */
.display-1, .display-4, .display-6 {
    line-height: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* === FORM CONTROLS === */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-control-lg {
    border-radius: 8px;
}

.form-select-lg {
    border-radius: 8px;
}

/* === LINKS === */
a {
    transition: color 0.2s ease;
}

a:hover {
    text-decoration: none !important;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .hero-section .display-4 {
        font-size: 2.5rem;
    }
    
    .hero-section .lead {
        font-size: 1.1rem;
    }
    
    .stats-hero .col-3 {
        margin-bottom: 1rem;
    }
    
    .stats-hero h3 {
        font-size: 1.75rem;
    }
    
    .btn-lg {
        font-size: 0.95rem;
        padding: 0.6rem 1.5rem;
    }
    
    .card-body {
        padding: 1.25rem 1rem;
    }
    
    .category-card .card-body {
        padding: 1.5rem 1rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .display-6 {
        font-size: 1.25rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .hero-section {
        padding: 3rem 0;
    }
    
    .hero-section .display-4 {
        font-size: 2rem;
    }
    
    .hero-section .lead {
        font-size: 1rem;
    }
    
    .stats-hero h3 {
        font-size: 1.5rem;
    }
    
    .card-custom {
        margin-bottom: 1rem;
    }
    
    .search-results-container {
        max-height: 300px;
    }
}

/* === DARK MODE SUPPORT === */
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    .bg-light {
        background-color: #e9ecef !important;
    }
}

/* === PRINT STYLES === */
@media print {
    .hero-section,
    .btn,
    #search-results {
        display: none !important;
    }
    
    .card-custom {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .container-fluid {
        max-width: none;
        padding: 0;
    }
}

/* === HIGH CONTRAST MODE === */
@media (prefers-contrast: high) {
    .btn {
        border-width: 2px;
    }
    
    .card-custom {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid;
    }
}

/* === REDUCED MOTION === */
@media (prefers-reduced-motion: reduce) {
    .card-custom,
    .btn,
    .category-card,
    .certification-item,
    .search-result-item,
    .centro-item,
    .info-item h5,
    .service-time,
    .stats-hero .stat-item {
        transition: none;
    }
    
    .fade-in-up,
    @keyframes fadeInUp {
        animation: none;
    }
}

/* === ACCESSIBILITÀ === */
.visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Focus visible per navigazione da tastiera */
.btn:focus-visible,
.form-control:focus-visible,
.form-select:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}
</style>
@endpush