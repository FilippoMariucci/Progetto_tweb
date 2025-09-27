{{--
    ===================================================================
    STATISTICHE ADMIN - Vista Blade Corretta - PARTE 1
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/admin/statistiche.blade.php
    
    DESCRIZIONE:
    Vista per la visualizzazione di statistiche avanzate del sistema per amministratori.
    Include grafici interattivi, KPI principali e analisi dettagliate.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Layout compatto per amministratori
    - Grafici Chart.js integrati per visualizzazione dati
    - Statistiche in tempo reale con aggiornamento periodico
    - Design responsive per tutti i dispositivi
    - Filtri per periodo temporale (7, 30, 90 giorni)
    - KPI dashboard con contatori principali
    ===================================================================
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends eredita il layout 'layouts.app'
    Questo significa che questa vista verrà inserita nella sezione @yield('content') del layout principale
--}}
@extends('layouts.app')

{{-- 
    DEFINIZIONE TITOLO PAGINA
    Blade PHP: @section('title') imposta il titolo che apparirà nel tag <title> HTML
    Questo titolo viene utilizzato dal layout base per SEO e identificazione pagina
--}}
@section('title', 'Statistiche Sistema - Admin')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce tutto il contenuto HTML della pagina
    Questo contenuto sarà inserito nel punto @yield('content') del layout base
--}}
@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === 
        HTML: Sezione intestazione con titolo, controlli periodo e azioni
        Bootstrap: Utilizza sistema flexbox per layout responsivo
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO PRINCIPALE CON ICONA
                HTML: Heading h2 con icona Bootstrap Icons
                Bootstrap: Classi per styling (colori, spaziature, tipografia)
            --}}
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-success me-2"></i>
                Statistiche Sistema
            </h2>
            {{-- Sottotitolo descrittivo --}}
            <p class="text-muted small mb-0">Analisi dettagliate del sistema</p>
            {{-- 
                INDICATORE PERIODO ATTUALE
                Blade PHP: {{ $periodo ?? 30 }} utilizza null coalescing operator
                PHP: Se $periodo non è definito o è null, usa valore di default 30
                Questa variabile viene passata dal controller e indica i giorni del periodo di analisi
            --}}
            <small class="text-muted">Periodo: ultimi {{ $periodo ?? 30 }} giorni</small>
        </div>
        
        {{-- 
            GRUPPO CONTROLLI PERIODO E AZIONI
            Bootstrap: btn-group per raggruppare pulsanti correlati
            btn-group-sm per dimensioni compatte
        --}}
        <div class="btn-group btn-group-sm">
            {{-- 
                CONTROLLI PERIODO - PULSANTI FILTRO TEMPORALE
                Questi pulsanti permettono di cambiare il periodo di analisi delle statistiche
            --}}
            
            {{-- 
                FILTRO 7 GIORNI
                Laravel: route('admin.statistiche.index', ['periodo' => 7]) genera URL con parametro
                Blade PHP: Controllo condizionale per classe 'active' se periodo corrente è 7
                HTML: Link che ricarica la pagina con nuovo parametro periodo
            --}}
            <a href="{{ route('admin.statistiche.index', ['periodo' => 7]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 7 ? 'active' : '' }}">7g</a>
            
            {{-- 
                FILTRO 30 GIORNI (DEFAULT)
                Blade PHP: Condizione per evidenziare il pulsante se periodo attuale è 30 giorni
                Questo è il valore di default se nessun periodo è specificato
            --}}
            <a href="{{ route('admin.statistiche.index', ['periodo' => 30]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 30 ? 'active' : '' }}">30g</a>
            
            {{-- FILTRO 90 GIORNI --}}
            <a href="{{ route('admin.statistiche.index', ['periodo' => 90]) }}" 
               class="btn btn-outline-success {{ ($periodo ?? 30) == 90 ? 'active' : '' }}">90g</a>
            
            {{-- 
                PULSANTE AGGIORNA STATISTICHE
                JavaScript: onclick="aggiornaStatistiche(event)" chiama funzione JS
                La funzione aggiornaStatistiche è definita nel file JavaScript esterno
                Permette di ricaricare i dati senza refresh completo della pagina
            --}}
            <button class="btn btn-primary" onclick="aggiornaStatistiche(event)">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
            
            {{-- 
                LINK RITORNO DASHBOARD
                Laravel: route('admin.dashboard') genera URL per dashboard admin
                Fornisce navigazione di ritorno alla pagina principale admin
            --}}
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE === 
        HTML: Griglia Bootstrap per KPI principali del sistema
        Queste card mostrano i numeri chiave del sistema in formato compatto
    --}}
    <div class="row g-2 mb-3">
        {{-- 
            CARD UTENTI TOTALI
            Bootstrap: col-lg-3 col-md-6 per layout responsivo
            Su desktop: 4 colonne, su tablet: 2 colonne, su mobile: 1 colonna
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    {{-- Icona rappresentativa --}}
                    <i class="bi bi-people text-primary fs-4"></i>
                    {{-- 
                        CONTATORE UTENTI TOTALI
                        Blade PHP: {{ $stats['utenti_totali'] ?? 0 }}
                        Laravel: $stats è un array associativo passato dal controller
                        PHP: ?? 0 fornisce valore di default se la chiave non esiste
                        Questo previene errori se il controller non passa il dato
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['utenti_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Utenti Totali</small>
                    {{-- 
                        INDICATORE NUOVI UTENTI (CONDIZIONALE)
                        Blade PHP: @if verifica se esistono nuovi prodotti da mostrare
                        Questo blocco appare solo se ci sono nuovi utenti nel periodo
                    --}}
                    @if(isset($stats['nuovi_prodotti']) && $stats['nuovi_prodotti'] > 0)
                        <br><small class="text-info">+{{ $stats['nuovi_prodotti'] }} nuovi</small>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- CARD SOLUZIONI/MALFUNZIONAMENTI --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    {{-- Icona chiave inglese per rappresentare riparazioni/soluzioni --}}
                    <i class="bi bi-wrench text-warning fs-4"></i>
                    {{-- 
                        CONTATORE MALFUNZIONAMENTI TOTALI
                        Laravel: Dato passato dal controller che conta tutti i malfunzionamenti nel DB
                        Rappresenta il numero totale di problemi registrati nel sistema
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni</small>
                    {{-- 
                        INDICATORE NUOVE SOLUZIONI
                        Mostra quante nuove soluzioni sono state aggiunte nel periodo selezionato
                    --}}
                    @if(isset($stats['nuove_soluzioni']) && $stats['nuove_soluzioni'] > 0)
                        <br><small class="text-warning">+{{ $stats['nuove_soluzioni'] }} nuove</small>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- CARD CENTRI ASSISTENZA --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    {{-- Icona posizione geografica per centri di assistenza --}}
                    <i class="bi bi-geo-alt text-success fs-4"></i>
                    {{-- 
                        CONTATORE CENTRI TOTALI
                        Laravel: Numero totale di centri di assistenza registrati
                        Utile per avere panoramica della copertura territoriale
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['centri_totali'] ?? 0 }}</h5>
                    <small class="text-muted">Centri</small>
                    {{-- 
                        INDICATORE UTENTI ATTIVI NEI CENTRI
                        Mostra quanti utenti sono attualmente attivi nei centri di assistenza
                    --}}
                    @if(isset($stats['utenti_attivi']) && $stats['utenti_attivi'] > 0)
                        <br><small class="text-success">{{ $stats['utenti_attivi'] }} attivi</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI === 
        HTML: Sezione principale con grafici Chart.js per visualizzazione dati
        I grafici sono organizzati in 3 colonne responsive per dashboard compatta
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            GRAFICO UTENTI PER LIVELLO - COMPATTO
            Chart.js: Grafico a torta per distribuzione utenti per livello di accesso
            I dati vengono passati dal controller nella variabile $distribuzioneUtenti
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Utenti per Livello
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        CANVAS PER CHART.JS
                        HTML: Canvas element richiesto da Chart.js per rendering grafici
                        id="graficoUtenti" viene utilizzato da JavaScript per inizializzare il grafico
                        height="120" imposta altezza fissa per layout compatto
                    --}}
                    <canvas id="graficoUtenti" height="120"></canvas>
                    
                    {{-- 
                        LEGENDA COMPATTA
                        HTML: Griglia Bootstrap per mostrare dettagli distribuzione utenti
                        Questa sezione visualizza i dati anche se JavaScript è disabilitato
                    --}}
                    <div class="row g-1 mt-2">
                        {{-- 
                            CONTROLLO ESISTENZA DATI DISTRIBUZIONE
                            Blade PHP: @if verifica che i dati esistano e non siano vuoti
                            PHP: isset() verifica esistenza, count() verifica che array non sia vuoto
                        --}}
                        @if(isset($distribuzioneUtenti) && count($distribuzioneUtenti) > 0)
                            {{-- 
                                ITERAZIONE LIVELLI UTENTE
                                Blade PHP: @foreach itera attraverso array associativo
                                PHP: $livello => $count destruttura chiave e valore
                                Ogni iterazione mostra un livello con il suo conteggio
                            --}}
                            @foreach($distribuzioneUtenti as $livello => $count)
                                <div class="col-6 small text-center">
                                    {{-- 
                                        BADGE CON CLASSE DINAMICA
                                        HTML: Badge colorato basato sul livello utente
                                        CSS: La classe badge-livello-{{ $livello }} è definita nel CSS
                                    --}}
                                    <span class="badge badge-livello-{{ $livello }}">{{ $count }}</span>
                                    {{-- 
                                        SWITCH PER DESCRIZIONE LIVELLO
                                        Blade PHP: @switch traduce codice livello in descrizione leggibile
                                        Mappatura: 1=Pubblico, 2=Tecnici, 3=Staff, 4=Admin
                                    --}}
                                    @switch($livello)
                                        @case('1') Pubblico @break
                                        @case('2') Tecnici @break
                                        @case('3') Staff @break
                                        @case('4') Admin @break
                                        @default Livello {{ $livello }}
                                    @endswitch
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 
            GRAFICO PRODOTTI PER CATEGORIA - COMPATTO
            Chart.js: Grafico a barre per distribuzione prodotti per categoria
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-1"></i>
                        Prodotti per Categoria
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        CANVAS GRAFICO PRODOTTI
                        Chart.js: id="graficoProdotti" per inizializzazione JavaScript
                        I dati vengono dal controller nella variabile $prodottiPerCategoria
                    --}}
                    <canvas id="graficoProdotti" height="120"></canvas>
                    
                    {{-- DETTAGLI COMPATTI CATEGORIE PRODOTTI --}}
                    {{-- 
                        CONTROLLO DATI PRODOTTI PER CATEGORIA
                        Laravel: Verifica esistenza e validità dati dal controller
                    --}}
                    @if(isset($prodottiPerCategoria) && count($prodottiPerCategoria) > 0)
                        <div class="row g-1 mt-2">
                            {{-- 
                                VISUALIZZAZIONE PRIME 4 CATEGORIE
                                PHP: array_slice() limita a prime 4 categorie per spazio
                                true come terzo parametro preserva le chiavi dell'array associativo
                            --}}
                            @foreach(array_slice($prodottiPerCategoria, 0, 4, true) as $categoria => $count)
                                <div class="col-6 small text-center">
                                    <span class="badge bg-info">{{ $count }}</span>
                                    {{-- 
                                        NOME CATEGORIA CAPITALIZZATO
                                        PHP: ucfirst() capitalizza prima lettera del nome categoria
                                        Migliora la presentazione dei dati
                                    --}}
                                    {{ ucfirst($categoria) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
            GRAFICO GRAVITÀ MALFUNZIONAMENTI - COMPATTO
            Chart.js: Grafico per distribuzione malfunzionamenti per livello di gravità
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        CANVAS GRAFICO GRAVITÀ
                        Chart.js: id="graficoGravita" per binding JavaScript
                        Visualizza distribuzione problemi per livello di urgenza
                    --}}
                    <canvas id="graficoGravita" height="120"></canvas>
                    
                    {{-- DETTAGLI GRAVITÀ CON CODICI COLORE --}}
                    {{-- 
                        VERIFICA DATI MALFUNZIONAMENTI PER GRAVITÀ
                        Laravel: $malfunzionamentiPerGravita passato dal controller
                    --}}
                    @if(isset($malfunzionamentiPerGravita) && count($malfunzionamentiPerGravita) > 0)
                        <div class="row g-1 mt-2">
                            {{-- 
                                ITERAZIONE LIVELLI DI GRAVITÀ
                                Ogni livello ha colore specifico per identificazione visiva immediata
                            --}}
                            @foreach($malfunzionamentiPerGravita as $gravita => $count)
                                <div class="col-6 small text-center">
                                    {{-- 
                                        SWITCH COLORI PER GRAVITÀ
                                        Blade PHP: @switch assegna colori bootstrap basati sulla gravità
                                        Codifica colori: Rosso=Critica, Giallo=Alta, Blu=Media, Verde=Bassa
                                    --}}
                                    @switch($gravita)
                                        @case('critica')
                                            <span class="badge bg-danger">{{ $count }}</span> Critica
                                            @break
                                        @case('alta')
                                            <span class="badge bg-warning text-dark">{{ $count }}</span> Alta
                                            @break
                                        @case('media')
                                            <span class="badge bg-info">{{ $count }}</span> Media
                                            @break
                                        @case('bassa')
                                            <span class="badge bg-success">{{ $count }}</span> Bassa
                                            @break
                                        @default
                                            {{-- 
                                                CASO DEFAULT PER GRAVITÀ NON PREVISTE
                                                Gestisce eventuali nuovi livelli di gravità aggiunti
                                            --}}
                                            <span class="badge bg-secondary">{{ $count }}</span> {{ ucfirst($gravita) }}
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ANDAMENTO CRESCITA COMPATTO RIMOSSO === 
        NOTA: Sezione crescita rimossa per richiesta utente
        Questa era una sezione per grafici temporali che mostravano l'andamento nel tempo
        È stata commentata/rimossa per semplificare il layout
    --}}

    {{-- === SEZIONE DETTAGLI LINEARI === 
        HTML: Sezione per dettagli tabellari e liste con informazioni specifiche
        Layout a due colonne: tabella prodotti problematici + lista staff attivi
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            PRODOTTI PROBLEMATICI - TABELLA
            HTML: Tabella responsiva per mostrare prodotti con più malfunzionamenti
            Aiuta identificare prodotti che richiedono più attenzione
        --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Prodotti con Più Problemi
                    </h6>
                </div>
                <div class="card-body p-0">
                    {{-- 
                        TABELLA RESPONSIVE
                        Bootstrap: table-responsive permette scroll orizzontale su mobile
                        table-sm per layout compatto, table-hover per feedback visivo
                    --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2 text-center">Problemi</th>
                                    <th class="py-2 text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    CONTROLLO ESISTENZA PRODOTTI PROBLEMATICI
                                    Laravel: Verifica che collection esista e contenga elementi
                                    $prodottiProblematici è una Collection Eloquent dal controller
                                --}}
                                @if(isset($prodottiProblematici) && $prodottiProblematici->count() > 0)
                                    {{-- 
                                        ITERAZIONE PRIMI 6 PRODOTTI PROBLEMATICI
                                        Laravel: take(6) limita risultati per performance e layout
                                        Ogni prodotto ha relazioni caricate per evitare query N+1
                                    --}}
                                    @foreach($prodottiProblematici->take(6) as $prodotto)
                                        <tr class="small">
                                            {{-- 
                                                COLONNA NOME PRODOTTO
                                                Laravel: $prodotto->nome accede attributo model Eloquent
                                                Mostra anche modello se disponibile
                                            --}}
                                            <td class="py-2">
                                                <strong>{{ $prodotto->nome }}</strong>
                                                {{-- Modello opzionale --}}
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            {{-- 
                                                COLONNA CATEGORIA
                                                Mostra categoria con badge colorato o trattino se mancante
                                            --}}
                                            <td class="py-2">
                                                @if($prodotto->categoria)
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($prodotto->categoria) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- 
                                                COLONNA CONTEGGIO PROBLEMI
                                                Laravel: malfunzionamenti_count è un attributo calcolato
                                                Viene aggiunto dal controller usando withCount() query builder
                                            --}}
                                            <td class="py-2 text-center">
                                                <span class="badge bg-warning text-dark">
                                                    {{ $prodotto->malfunzionamenti_count }}
                                                </span>
                                            </td>
                                            {{-- 
                                                COLONNA AZIONI
                                                Laravel: route() genera URL per visualizzazione dettaglio prodotto
                                            --}}
                                            <td class="py-2 text-end">
                                                <a href="{{ route('admin.prodotti.show', $prodotto->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- 
                                        MESSAGGIO NESSUN DATO
                                        HTML: Riga che occupa tutta la larghezza quando non ci sono dati
                                        Migliora UX mostrando stato vuoto invece di tabella vuota
                                    --}}
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3 small">
                                            Nessun prodotto problematico
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- 
            STAFF ATTIVI - LISTA LATERALE
            HTML: Card con lista staff membri più attivi del sistema
            Layout verticale che si adatta all'altezza della tabella prodotti
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-people me-1"></i>
                        Staff Attivi
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        CONTROLLO ESISTENZA STAFF ATTIVI
                        Laravel: Verifica che collection $staffAttivi esista e contenga dati
                        $staffAttivi è passato dal controller con dati aggregati di attività
                    --}}
                    @if(isset($staffAttivi) && $staffAttivi->count() > 0)
                        {{-- 
                            LISTA BOOTSTRAP PER STAFF
                            Bootstrap: list-group-flush rimuove bordi per integrazione in card
                            Ogni item mostra informazioni staff con metriche di attività
                        --}}
                        <div class="list-group list-group-flush">
                            {{-- 
                                ITERAZIONE PRIMI 5 STAFF ATTIVI
                                Laravel: take(5) limita risultati per ottimizzare spazio
                                Lo staff è ordinato per attività nel controller
                            --}}
                            @foreach($staffAttivi->take(5) as $staff)
                                <div class="list-group-item px-0 border-0 py-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        {{-- 
                                            INFORMAZIONI STAFF - LATO SINISTRO
                                            HTML: Layout flex per nome e username
                                        --}}
                                        <div class="flex-grow-1 me-1">
                                            {{-- 
                                                NOME COMPLETO STAFF
                                                Laravel: $staff->nome_completo può essere un accessor
                                                text-truncate previene overflow su nomi lunghi
                                            --}}
                                            <h6 class="mb-1 fw-semibold small text-truncate">
                                                {{ $staff->nome_completo }}
                                            </h6>
                                            {{-- Username con icona identificativa --}}
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $staff->username }}
                                            </p>
                                        </div>
                                        {{-- 
                                            METRICHE STAFF - LATO DESTRO
                                            HTML: Badge con contatori di attività
                                        --}}
                                        <div class="text-end">
                                            {{-- 
                                                CONTATORE MALFUNZIONAMENTI CREATI
                                                Laravel: malfunzionamenti_creati_count è attributo calcolato
                                                Mostra produttività staff nella creazione soluzioni
                                            --}}
                                            <span class="badge bg-info">
                                                {{ $staff->malfunzionamenti_creati_count }}
                                            </span>
                                            {{-- 
                                                CONTATORE PRODOTTI ASSEGNATI (CONDIZIONALE)
                                                Laravel: Mostra solo se dato disponibile (funzionalità opzionale)
                                                Indica responsabilità staff su prodotti specifici
                                            --}}
                                            @if(isset($staff->prodotti_assegnati_count))
                                                <br><span class="badge bg-success mt-1">
                                                    {{ $staff->prodotti_assegnati_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- 
                            LINK VISUALIZZA TUTTI GLI STAFF
                            Laravel: route() genera URL per pagina completa gestione utenti
                            Permette accesso alla vista dettagliata di tutti gli staff
                        --}}
                        <div class="text-center mt-2">
                            <a href="{{ route('admin.users.index') }}" 
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-list me-1"></i>Vedi Tutti
                            </a>
                        </div>
                    @else
                        {{-- 
                            STATO VUOTO - NESSUN STAFF ATTIVO
                            HTML: Visualizzazione quando non ci sono staff attivi da mostrare
                            Migliora UX evitando spazi vuoti
                        --}}
                        <div class="text-center py-3">
                            <i class="bi bi-person-plus display-6 text-muted opacity-50"></i>
                            <p class="text-muted small mt-2 mb-0">Nessun staff attivo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === SEZIONE DETTAGLI LINEARI === 
        NOTA: Sezione vuota mantenuta per future espansioni
        Potrebbe contenere ulteriori widget o metriche specifiche
    --}}
    <div class="row g-3 mb-3">
        {{-- Spazio riservato per future funzionalità --}}
    </div>

    {{-- === METRICHE AGGIUNTIVE COMPATTE === 
        HTML: Sezione finale con metriche di sistema e informazioni generali
        Layout orizzontale con 4 widget informativi
    --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Metriche Sistema
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        {{-- 
                            WIDGET SOLUZIONI AGGIORNATE
                            Laravel: Mostra numero di soluzioni aggiornate nel periodo
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-arrow-clockwise text-primary fs-4"></i>
                                <div class="h5 text-primary mb-0 mt-1">
                                    {{ $stats['soluzioni_aggiornate'] ?? 0 }}
                                </div>
                                <small class="text-muted">Aggiornate</small>
                            </div>
                        </div>

                        {{-- 
                            WIDGET PERIODO ATTUALE
                            HTML: Mostra il periodo di analisi attualmente selezionato
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-calendar3 text-info fs-4"></i>
                                <div class="h5 text-info mb-0 mt-1">
                                    {{ $periodo ?? 30 }}
                                </div>
                                <small class="text-muted">Giorni</small>
                            </div>
                        </div>

                        {{-- 
                            WIDGET ULTIMO AGGIORNAMENTO
                            PHP: now()->format('H:i') mostra ora corrente formattata
                            Laravel: now() è un helper per Carbon (libreria date/time)
                            JavaScript: id="last-update" permette aggiornamento dinamico via JS
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-clock text-success fs-4"></i>
                                <div class="h5 text-success mb-0 mt-1" id="last-update">
                                    {{ now()->format('H:i') }}
                                </div>
                                <small class="text-muted">Ultimo Update</small>
                            </div>
                        </div>

                        {{-- 
                            WIDGET STATO SISTEMA
                            HTML: Indicatore stato generale del sistema
                            Badge verde "OK" indica sistema funzionante
                        --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-database text-warning fs-4"></i>
                                <div class="h5 text-warning mb-0 mt-1">
                                    <span class="badge bg-success">OK</span>
                                </div>
                                <small class="text-muted">Sistema</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{{-- FINE SEZIONE CONTENUTO --}}
@endsection

{{-- === SCRIPTS SECTION === 
    Blade PHP: @push('scripts') aggiunge JavaScript alla sezione scripts del layout
    Questi script vengono caricati alla fine del body per ottimizzare performance
--}}
@push('scripts')
{{-- 
    INCLUSIONE LIBRERIA CHART.JS
    CDN: Chart.js versione 3.9.1 per rendering grafici interattivi
    JavaScript: Libreria esterna per creazione grafici canvas-based
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
/*
    ===================================================================
    PASSAGGIO DATI DAL CONTROLLER PHP AL JAVASCRIPT
    ===================================================================
    JavaScript: Questo è il punto cruciale per la comunicazione PHP-JS
    I dati vengono serializzati dal PHP e deserializzati dal JavaScript
    per alimentare i grafici Chart.js
*/

console.log('📊 Inizializzazione dati statistiche...');

/*
    SERIALIZZAZIONE DATI PHP -> JAVASCRIPT
    Blade PHP: @json() converte array/oggetti PHP in formato JSON sicuro
    JavaScript: I dati diventano oggetti JavaScript utilizzabili
    Questi dati vengono poi utilizzati dai grafici Chart.js per il rendering
*/

// Passa i dati dal controller PHP alle variabili JavaScript globali
window.distribuzioneUtenti = @json($distribuzioneUtenti ?? []);
window.prodottiPerCategoria = @json($prodottiPerCategoria ?? []);
window.malfunzionamentiPerGravita = @json($malfunzionamentiPerGravita ?? []);
window.crescitaUtenti = @json($crescitaUtenti ?? []);
window.crescitaSoluzioni = @json($crescitaSoluzioni ?? []);

/*
    DEBUG E VALIDAZIONE DATI
    JavaScript: Console logging per verificare che i dati siano passati correttamente
    Utile per debug durante sviluppo e risoluzione problemi
*/
console.log('🔍 Dati ricevuti dal controller:', {
    distribuzioneUtenti: window.distribuzioneUtenti,
    prodottiPerCategoria: window.prodottiPerCategoria,
    malfunzionamentiPerGravita: window.malfunzionamentiPerGravita,
    crescitaUtenti: window.crescitaUtenti,
    crescitaSoluzioni: window.crescitaSoluzioni
});

/*
    VALIDAZIONE DATI NON VUOTI
    JavaScript: Verifica che gli array non siano vuoti prima dell'uso
    Previene errori nei grafici Chart.js se il controller non passa dati
*/
if (Object.keys(window.distribuzioneUtenti).length === 0) {
    console.warn('⚠️ distribuzioneUtenti è vuoto - controllare il controller');
}
if (Object.keys(window.prodottiPerCategoria).length === 0) {
    console.warn('⚠️ prodottiPerCategoria è vuoto - controllare il controller');
}
if (Object.keys(window.malfunzionamentiPerGravita).length === 0) {
    console.warn('⚠️ malfunzionamentiPerGravita è vuoto - controllare il controller');
}

/*
    CONFIGURAZIONE ROUTING PER JAVASCRIPT
    JavaScript: Imposta informazioni di routing per AJAX calls
    Permette al JavaScript di conoscere la route corrente
*/
window.LaravelApp = window.LaravelApp || {};
window.LaravelApp.route = 'admin.statistiche';

console.log('✅ Dati passati correttamente al JavaScript');
</script>

{{-- 
    INCLUSIONE SCRIPT STATISTICHE PERSONALIZZATO
    Laravel: asset() genera URL corretto per file statico
    JavaScript: File dedicato con logica specifica per grafici statistiche
    Questo file contiene le funzioni per inizializzare Chart.js e gestire interazioni
--}}
<script src="{{ asset('js/admin/statistiche.js') }}"></script>
@endpush

{{-- === STYLES SECTION === 
    Blade PHP: @push('styles') aggiunge CSS personalizzati al layout
    Questi stili vengono inseriti nella sezione head per corretta priorità
--}}
@push('styles')
<style>
/* ===================================================================
   STILI COMPATTI PER STATISTICHE ADMIN
   CSS: Definizioni di stile specifiche per la pagina statistiche
   Ottimizzate per layout compatto e visualizzazione dati
   =================================================================== */

/*
    BADGE PERSONALIZZATI PER LIVELLI UTENTE
    CSS: Colori specifici per identificazione visiva livelli di accesso
    Mapping: Grigio=Pubblico, Azzurro=Tecnici, Giallo=Staff, Rosso=Admin
*/
.badge-livello-1 { 
    background-color: #6c757d !important; 
    color: white !important; 
}
.badge-livello-2 { 
    background-color: #0dcaf0 !important; 
    color: white !important; 
}
.badge-livello-3 { 
    background-color: #ffc107 !important; 
    color: #000 !important; 
}
.badge-livello-4 { 
    background-color: #dc3545 !important; 
    color: white !important; 
}

/*
    CARD CON BORDI ARROTONDATI E OMBRE LEGGERE
    CSS: Design system coerente per tutti i componenti card
    Transizioni smooth per feedback visivo su hover
*/
.card {
    border-radius: 8px;
    border: none !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/*
    HEADER DELLE CARD PIÙ COMPATTI
    CSS: Riduzione padding e ottimizzazione tipografia
    Font-weight 600 per migliore leggibilità headers
*/
.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
    font-weight: 600;
}

.card-body {
    font-size: 0.9rem;
}

/*
    TABELLE PIÙ COMPATTE PER LAYOUT RESPONSIVO
    CSS: Ottimizzazione spacing e tipografia per dati tabulari
    Vertical-align middle per allineamento ottimale contenuto
*/
.table-sm td, .table-sm th {
    padding: 0.4rem;
    font-size: 0.85rem;
    vertical-align: middle;
}

/*
    GRAFICI RESPONSIVE CON ALTEZZA FISSA
    CSS: Constraint altezza canvas per layout prevedibile
    Important per override eventuali stili Chart.js
*/
canvas {
    max-height: 120px !important;
}

/*
    BADGE PIÙ PICCOLI E COLORATI
    CSS: Dimensioni ottimizzate per densità informativa
    Border-radius coerente con design system
*/
.badge {
    font-size: 0.7rem;
    border-radius: 6px;
}

/*
    BOTTONI GRUPPO PIÙ COMPATTI
    CSS: Riduzione padding per controlli periodo
    Border-radius per elementi intermedi gruppo
*/
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

/*
    LISTE COMPATTE
    CSS: Ottimizzazione spacing per elementi lista
    Border-radius per integrazione visiva con cards
*/
.list-group-item {
    font-size: 0.85rem;
    border-radius: 4px !important;
}

/*
    PROGRESS BAR CON ANIMAZIONI
    CSS: Styling per eventuali progress indicators
    Transizioni smooth per aggiornamenti valori
*/
.progress {
    border-radius: 6px;
    height: 8px;
}

.progress-bar {
    transition: width 0.6s ease;
}

/* === RESPONSIVE DESIGN === 
   CSS: Media queries per ottimizzazione dispositivi mobili
   Adattamenti specifici per tablet e smartphone
*/
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .col-lg-4, .col-lg-8 {
        margin-bottom: 0.5rem;
    }
    
    .btn-group-sm {
        flex-wrap: wrap;
    }
    
    .btn-group-sm .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    /*
        HEADER RESPONSIVE
        CSS: Stack verticale su mobile per migliore usabilità
    */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .btn-group {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .small {
        font-size: 0.75rem !important;
    }
    
    .h5 {
        font-size: 1.1rem !important;
    }
    
    .fs-4 {
        font-size: 1.2rem !important;
    }
    
    /*
        CONTAINER PIÙ STRETTO SU MOBILE
        CSS: Riduzione padding laterali per massimizzare spazio
    */
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* === ANIMAZIONI E TRANSIZIONI === 
   CSS: Transizioni uniformi per tutti gli elementi interattivi
   Migliora perceived performance e feedback utente
*/
.card, .btn, .badge, .alert {
    transition: all 0.2s ease-in-out;
}

/*
    EFFETTI HOVER
    CSS: Feedback visivo per elementi interattivi
*/
.btn:hover {
    transform: translateY(-1px);
}

.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(13, 110, 253, 0.05);
    transform: scale(1.001);
}

/*
    SPINNER PERSONALIZZATO
    CSS: Dimensioni ottimizzate per layout compatto
*/
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === UTILITÀ === 
   CSS: Classi helper per consistenza design
*/
.text-muted {
    color: #6c757d !important;
}

.fw-semibold {
    font-weight: 600;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

/*
    FOCUS PER ACCESSIBILITÀ
    CSS: Indicatori focus per navigazione da tastiera
    Supporto completo accessibilità WCAG
*/
.btn:focus-visible, 
.form-control:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/*
    SCROLLBAR PERSONALIZZATA
    CSS: Styling scrollbar per componenti table-responsive
    Solo per browser Webkit (Chrome, Safari, Edge)
*/
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* === COLORI TEMA === 
   CSS: Definizioni colori consistenti per headers cards
   Override colori Bootstrap per branding personalizzato
*/
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.card-header.bg-info {
    background-color: #0dcaf0 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.card-header.bg-success {
    background-color: #198754 !important;
}

.card-header.bg-danger {
    background-color: #dc3545 !important;
}

.card-header.bg-secondary {
    background-color: #6c757d !important;
}

.card-header.bg-dark {
    background-color: #212529 !important;
}

/* === OTTIMIZZAZIONI PRESTAZIONI === 
   CSS: Ottimizzazioni per rendering performante
*/
* {
    box-sizing: border-box;
}

/*
    MIGLIORA LE PERFORMANCE DI RENDERING
    CSS: Contain layout per elementi complessi
*/
.card, canvas, .table {
    contain: layout style;
}

/* === STAMPA === 
   CSS: Ottimizzazioni per output stampato
   Rimuove elementi interattivi e ottimizza layout
*/
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
        box-shadow: none !important;
    }
    
    .card-header {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}
</style>
@endpush