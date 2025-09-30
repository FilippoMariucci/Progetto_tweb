{{--
    ===================================================================
    STATISTICHE TECNICO - Vista Blade Corretta
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/tecnico/statistiche.blade.php
    
    FUNZIONALITÀ:
    - Layout compatto per tecnici
    - Grafici personalizzati per performance
    - Statistiche personali e di centro
    - Integrazione JavaScript pulita
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    DESCRIZIONE: Template per visualizzare le statistiche personali di un tecnico,
                 inclusi grafici, dati del centro di assistenza e problemi critici
    ===================================================================
--}}

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Estende il layout principale dell'applicazione
    PARAMETRO: 'layouts.app' - percorso del layout base
--}}
@extends('layouts.app')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Definisce il titolo della pagina che apparirà nel tag <title>
    PARAMETRO: stringa del titolo
--}}
@section('title', 'Le mie Statistiche - Tecnico')

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Inizio della sezione contenuto principale della pagina
    SCOPO: Tutto il codice fino a @endsection verrà inserito nella sezione 'content' del layout
--}}
@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap CSS
        FUNZIONE: Container per l'intestazione con titolo e pulsanti di azione
        CLASSE d-flex: layout flexbox
        CLASSE justify-content-between: distribuisce lo spazio tra gli elementi
        CLASSE align-items-center: allinea verticalmente al centro
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                LINGUAGGIO: HTML
                FUNZIONE: Titolo principale della pagina con icona
            --}}
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-primary me-2"></i>
                Le mie Statistiche
            </h2>
            {{-- 
                LINGUAGGIO: Blade Template (PHP)
                FUNZIONE: Visualizza il nome del tecnico autenticato
                OPERATORE ??: null coalescing - restituisce il primo valore non-null
                METODO auth()->user(): recupera l'utente autenticato corrente
                PROPRIETÀ nome_completo/name: campi del modello User
            --}}
            <p class="text-muted small mb-0">{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Tecnico' }}</p>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                LINGUAGGIO: Blade Template + HTML
                FUNZIONE: Link per tornare alla dashboard del tecnico
                HELPER route(): genera URL basato sul nome della route definita in routes/web.php
                PARAMETRO 'tecnico.dashboard': nome della route
            --}}
            <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            {{-- 
                LINGUAGGIO: HTML + JavaScript
                FUNZIONE: Pulsante per aggiornare manualmente le statistiche
                EVENTO onclick: esegue la funzione JavaScript aggiornaStatistiche() al click
            --}}
            <button class="btn btn-primary" onclick="aggiornaStatistiche()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
        </div>
    </div>

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap Grid
        FUNZIONE: Griglia responsive per 4 card con statistiche principali
        CLASSE row: container per il sistema grid di Bootstrap
        CLASSE g-2: gap (spaziatura) di 2 unità tra le colonne
    --}}
    <div class="row g-2 mb-3">
        {{-- 
            CARD 1: PRODOTTI TOTALI
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Visualizza il numero totale di prodotti disponibili
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-box text-primary fs-4"></i>
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il conteggio dei prodotti totali
                        ARRAY $stats: array associativo passato dal controller
                        OPERATORE ??: fornisce valore di default 0 se la chiave non esiste
                        STRUTTURA: $stats['generale']['total_prodotti']
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['generale']['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted">Prodotti Totali</small>
                </div>
            </div>
        </div>
        
        {{-- 
            CARD 2: SOLUZIONI TOTALI
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Visualizza il numero totale di malfunzionamenti/soluzioni
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-tools text-success fs-4"></i>
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il conteggio totale dei malfunzionamenti
                        STRUTTURA: $stats['malfunzionamenti']['totali']
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti']['totali'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni</small>
                </div>
            </div>
        </div>
        
        {{-- 
            CARD 3: PROBLEMI CRITICI
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Visualizza il numero di malfunzionamenti critici
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il conteggio dei malfunzionamenti critici
                        STRUTTURA: $stats['malfunzionamenti']['critici']
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['malfunzionamenti']['critici'] ?? 0 }}</h5>
                    <small class="text-muted">Critici</small>
                </div>
            </div>
        </div>
        
        {{-- 
            CARD 4: CENTRI DI ASSISTENZA
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Visualizza il numero totale di centri di assistenza
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-geo-alt text-info fs-4"></i>
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il conteggio totale dei centri
                        STRUTTURA: $stats['generale']['total_centri']
                    --}}
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['generale']['total_centri'] ?? 0 }}</h5>
                    <small class="text-muted">Centri</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap Grid
        FUNZIONE: Griglia per tre grafici affiancati (Chart.js)
        CLASSE row g-3: row con gap di 3 unità
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            GRAFICO 1: GRAVITÀ MALFUNZIONAMENTI
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Container per grafico a torta della distribuzione per gravità
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Canvas HTML5 per il rendering del grafico Chart.js
                        ID: identificatore univoco per il grafico (usato da JavaScript)
                        ATTRIBUTO height: altezza del canvas in pixel
                    --}}
                    <canvas id="graficoGravita" height="120"></canvas>
                    {{-- Legenda compatta --}}
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Direttiva condizionale per verificare se ci sono dati da visualizzare
                        CONTROLLO isset(): verifica se la variabile esiste e non è null
                        FUNZIONE count(): conta gli elementi nell'array
                    --}}
                    @if(isset($stats['malfunzionamenti']['per_gravita']) && count($stats['malfunzionamenti']['per_gravita']) > 0)
                        <div class="row g-1 mt-2">
                            {{-- 
                                LINGUAGGIO: Blade Template (PHP)
                                FUNZIONE: Loop foreach per iterare su ogni livello di gravità
                                SINTASSI: @foreach(array as chiave => valore)
                                $gravita: chiave (es. 'critica', 'alta', 'media', 'bassa')
                                $count: valore numerico del conteggio
                            --}}
                            @foreach($stats['malfunzionamenti']['per_gravita'] as $gravita => $count)
                                <div class="col-6 small text-center">
                                    {{-- 
                                        LINGUAGGIO: Blade Template (PHP)
                                        FUNZIONE: Switch-case per assegnare badge colorato in base alla gravità
                                        SINTASSI Blade: @switch / @case / @break / @endswitch
                                    --}}
                                    @switch($gravita)
                                        @case('critica')
                                            <span class="badge bg-danger">{{ $count }}</span> Critica
                                            @break
                                        @case('alta')
                                            <span class="badge bg-warning text-dark">{{ $count }}</span> Alta
                                            @break
                                        @case('media')
                                            <span class="badge bg-success">{{ $count }}</span> Media
                                            @break
                                        @case('bassa')
                                            <span class="badge bg-info">{{ $count }}</span> Bassa
                                            @break
                                        @default
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE ucfirst(): converte il primo carattere in maiuscolo
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

        {{-- 
            GRAFICO 2: TREND SETTIMANALE
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Container per grafico a linee dei malfunzionamenti negli ultimi 7 giorni
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up me-1"></i>
                        Ultimi 7 Giorni
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Canvas per il grafico del trend settimanale
                        ID graficoTrend: usato da JavaScript per inizializzare Chart.js
                    --}}
                    <canvas id="graficoTrend" height="120"></canvas>
                    {{-- Info trend --}}
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Verifica condizionale per mostrare il totale settimanale
                        OPERATORE &&: AND logico - entrambe le condizioni devono essere vere
                    --}}
                    @if(isset($stats['trend_settimanale']) && isset($stats['trend_settimanale']['totale_settimana']))
                        <div class="text-center mt-2">
                            <small class="text-success fw-semibold">
                                Totale settimana: {{ $stats['trend_settimanale']['totale_settimana'] }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
            GRAFICO 3: CATEGORIE PRODOTTI
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Container per grafico a barre delle categorie di prodotti
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart me-1"></i>
                        Per Categoria
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Canvas per il grafico delle categorie
                        ID graficoCategorie: usato da JavaScript
                    --}}
                    <canvas id="graficoCategorie" height="120"></canvas>
                    {{-- Dettagli categorie --}}
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Verifica se esistono categorie da visualizzare
                    --}}
                    @if(isset($stats['per_categoria']) && count($stats['per_categoria']) > 0)
                        <div class="row g-1 mt-2">
                            {{-- 
                                LINGUAGGIO: Blade + PHP
                                FUNZIONE array_slice(): estrae le prime 4 categorie dall'array
                                PARAMETRI: (array, offset, length, preserve_keys)
                                - $stats['per_categoria']: array sorgente
                                - 0: parte dall'inizio
                                - 4: prende 4 elementi
                                - true: mantiene le chiavi originali
                            --}}
                            @foreach(array_slice($stats['per_categoria'], 0, 4, true) as $categoria => $count)
                                <div class="col-6 small text-center">
                                    <span class="badge bg-info">{{ $count }}</span>
                                    {{-- 
                                        LINGUAGGIO: Blade + PHP
                                        FUNZIONE ucfirst(): capitalizza la prima lettera
                                    --}}
                                    {{ ucfirst($categoria) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- === INFORMAZIONI LINEARI === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap Grid
        FUNZIONE: Griglia responsive per informazioni centro e profilo tecnico
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            SEZIONE: INFORMAZIONI CENTRO ASSISTENZA
            LINGUAGGIO: Blade Template (PHP) + HTML
            FUNZIONE: Visualizza i dati del centro di assistenza del tecnico
        --}}
        {{-- 
            LINGUAGGIO: Blade Template (PHP)
            FUNZIONE: Direttiva condizionale @if per verificare l'esistenza dei dati del centro
            OPERATORE &&: AND logico - entrambe le condizioni devono essere vere
            CONTROLLO: isset() verifica che la variabile esista e non sia null
                      $stats['centro_assistenza'] verifica che il valore sia truthy (non false, 0, null, ecc.)
        --}}
        @if(isset($stats['centro_assistenza']) && $stats['centro_assistenza'])
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-building me-1"></i>
                        Il mio Centro
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il nome del centro di assistenza
                        OPERATORE ??: null coalescing - restituisce 'N/A' se la chiave non esiste o è null
                        STRUTTURA: accesso a chiave associativa dell'array $stats
                    --}}
                    <h6 class="fw-bold mb-1">{{ $stats['centro_assistenza']['nome'] ?? 'N/A' }}</h6>
                    
                    {{-- 
                        LINGUAGGIO: HTML + Blade
                        FUNZIONE: Visualizza l'indirizzo del centro con icona geolocalizzazione
                    --}}
                    <p class="small mb-1">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $stats['centro_assistenza']['indirizzo'] ?? 'N/A' }}
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP) + HTML
                        FUNZIONE: Visualizza città e provincia del centro
                    --}}
                    <p class="small mb-1">
                        <i class="bi bi-house me-1"></i>
                        {{ $stats['centro_assistenza']['citta'] ?? 'N/A' }} 
                        {{-- 
                            LINGUAGGIO: Blade Template (PHP)
                            FUNZIONE: Direttiva @if per mostrare la provincia solo se esiste
                            PATTERN: controllo condizionale inline
                        --}}
                        @if(isset($stats['centro_assistenza']['provincia']))
                            ({{ $stats['centro_assistenza']['provincia'] }})
                        @endif
                    </p>
                    
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Verifica se il telefono esiste ed è valorizzato prima di visualizzarlo
                        OPERATORE &&: verifica che isset() sia true E che il valore non sia falsy
                    --}}
                    @if(isset($stats['centro_assistenza']['telefono']) && $stats['centro_assistenza']['telefono'])
                    <p class="small mb-0">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $stats['centro_assistenza']['telefono'] }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 
            SEZIONE: PROFILO PERSONALE TECNICO
            LINGUAGGIO: HTML + Blade
            FUNZIONE: Visualizza le informazioni personali del tecnico autenticato
        --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-gear me-1"></i>
                        Profilo Tecnico
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza il nome completo del tecnico
                        METODO auth()->user(): restituisce l'istanza del modello User autenticato
                        OPERATORE ??: catena di null coalescing - prova nome_completo, poi name, poi 'N/A'
                    --}}
                    <p class="small mb-1"><strong>Nome:</strong> {{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'N/A' }}</p>
                    
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Mostra la specializzazione solo se esiste nel profilo utente
                        PATTERN: controllo esistenza e valore truthy prima di visualizzare
                    --}}
                    @if(isset(auth()->user()->specializzazione) && auth()->user()->specializzazione)
                    <p class="small mb-1"><strong>Specializzazione:</strong> {{ auth()->user()->specializzazione }}</p>
                    @endif
                    
                    {{-- 
                        LINGUAGGIO: HTML
                        FUNZIONE: Visualizza il livello di accesso dell'utente (hardcoded per tecnici)
                    --}}
                    <p class="small mb-1"><strong>Livello:</strong> Tecnico (Livello 2)</p>
                    
                    {{-- 
                        LINGUAGGIO: Blade Template (PHP)
                        FUNZIONE: Visualizza la data di registrazione formattata
                        METODO format(): formatta l'oggetto Carbon/DateTime in formato italiano
                        PARAMETRO 'd/m/Y': giorno/mese/anno (es. 15/03/2024)
                        OPERATORE ??: fornisce 'N/A' se la data non esiste
                    --}}
                    <p class="small mb-0"><strong>Attivo dal:</strong> {{ $stats['personali']['data_registrazione']->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- === TABELLA COMPATTA PROBLEMI CRITICI === --}}
    {{-- 
        LINGUAGGIO: HTML + Bootstrap
        FUNZIONE: Tabella responsive per visualizzare i malfunzionamenti critici recenti
    --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-octagon me-1"></i>
                        Problemi Critici Recenti
                    </h6>
                </div>
                <div class="card-body p-0">
                    {{-- 
                        LINGUAGGIO: HTML + Bootstrap
                        FUNZIONE: Wrapper responsive per la tabella (scroll orizzontale su schermi piccoli)
                        CLASSE table-responsive: abilita lo scroll orizzontale su mobile
                    --}}
                    <div class="table-responsive">
                        {{-- 
                            LINGUAGGIO: HTML
                            FUNZIONE: Tabella HTML standard con classi Bootstrap
                            CLASSE table-sm: riduce il padding delle celle
                            CLASSE table-hover: evidenzia le righe al passaggio del mouse
                        --}}
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Problema</th>
                                    <th class="py-2">Gravità</th>
                                    <th class="py-2">Segnalazioni</th>
                                    <th class="py-2">Data</th>
                                    <th class="py-2">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    LINGUAGGIO: Blade Template (PHP)
                                    FUNZIONE: Verifica se esistono malfunzionamenti critici da visualizzare
                                    CONTROLLO isset(): verifica esistenza della variabile
                                    METODO count(): conta gli elementi della collection Eloquent
                                    OPERATORE &&: AND logico
                                --}}
                                @if(isset($stats['critici_recenti']) && $stats['critici_recenti']->count() > 0)
                                    {{-- 
                                        LINGUAGGIO: Blade Template (PHP)
                                        FUNZIONE: Loop foreach per iterare sui malfunzionamenti critici
                                        METODO take(5): metodo Collection Laravel - limita a 5 elementi
                                        VARIABILE $malfunzionamento: istanza del Model Malfunzionamento
                                    --}}
                                    @foreach($stats['critici_recenti']->take(5) as $malfunzionamento)
                                    <tr class="small">
                                        {{-- 
                                            COLONNA PRODOTTO
                                            LINGUAGGIO: Blade + PHP
                                            FUNZIONE: Visualizza nome e modello del prodotto associato
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade Template (PHP)
                                                FUNZIONE: Visualizza il nome del prodotto
                                                RELAZIONE Eloquent: $malfunzionamento->prodotto accede alla relazione belongsTo
                                                OPERATORE ??: fornisce 'N/D' se il prodotto non esiste
                                            --}}
                                            <strong>{{ $malfunzionamento->prodotto->nome ?? 'N/D' }}</strong>
                                            
                                            {{-- 
                                                LINGUAGGIO: Blade Template (PHP)
                                                FUNZIONE: Mostra il modello del prodotto se esiste
                                                PATTERN: controllo esistenza e valore prima di visualizzare
                                            --}}
                                            @if(isset($malfunzionamento->prodotto->modello) && $malfunzionamento->prodotto->modello)
                                                <br><small class="text-muted">{{ $malfunzionamento->prodotto->modello }}</small>
                                            @endif
                                        </td>
                                        
                                        {{-- 
                                            COLONNA PROBLEMA
                                            LINGUAGGIO: Blade + PHP
                                            FUNZIONE: Visualizza titolo e descrizione del malfunzionamento
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE: Helper Str::limit() - tronca la stringa a 30 caratteri
                                                CLASSE Str: helper Laravel per manipolazione stringhe
                                                METODO limit($stringa, $lunghezza, $terminatore = '...')
                                            --}}
                                            <span class="fw-semibold">{{ Str::limit($malfunzionamento->titolo, 30) }}</span>
                                            
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE: Tronca la descrizione a 40 caratteri
                                            --}}
                                            <br><small class="text-muted">{{ Str::limit($malfunzionamento->descrizione, 40) }}</small>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA GRAVITÀ
                                            LINGUAGGIO: Blade + PHP
                                            FUNZIONE: Visualizza badge colorato per il livello di gravità
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE ucfirst(): capitalizza la prima lettera della stringa
                                                PROPRIETÀ $malfunzionamento->gravita: campo del database
                                            --}}
                                            <span class="badge bg-danger">{{ ucfirst($malfunzionamento->gravita) }}</span>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA SEGNALAZIONI
                                            LINGUAGGIO: Blade + PHP
                                            FUNZIONE: Visualizza il numero di segnalazioni ricevute
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade Template (PHP)
                                                FUNZIONE: Visualizza contatore segnalazioni con valore di default 0
                                                OPERATORE ??: null coalescing operator
                                            --}}
                                            <span class="badge bg-warning text-dark">{{ $malfunzionamento->numero_segnalazioni ?? 0 }}</span>
                                        </td>
                                        
                                        {{-- 
                                            COLONNA DATA
                                            LINGUAGGIO: Blade + PHP
                                            FUNZIONE: Visualizza la data di creazione del malfunzionamento
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade + PHP
                                                FUNZIONE: Formatta la data di creazione in formato giorno/mese
                                                PROPRIETÀ created_at: timestamp automatico di Laravel (Carbon instance)
                                                METODO format('d/m'): formatta in giorno/mese (es. 15/03)
                                            --}}
                                            {{ $malfunzionamento->created_at->format('d/m') }}
                                        </td>
                                        
                                        {{-- 
                                            COLONNA AZIONI
                                            LINGUAGGIO: Blade + HTML
                                            FUNZIONE: Pulsante per visualizzare il dettaglio del malfunzionamento
                                        --}}
                                        <td class="py-2">
                                            {{-- 
                                                LINGUAGGIO: Blade Template + HTML
                                                FUNZIONE: Link per visualizzare i dettagli del malfunzionamento
                                                HELPER route(): genera URL per la route nominata
                                                PARAMETRI: ['malfunzionamenti.show', [prodotto_id, malfunzionamento_id]]
                                                - 'malfunzionamenti.show': nome della route definita in routes/web.php
                                                - Array di parametri: [prodotto_id, id] necessari per la route
                                            --}}
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto_id, $malfunzionamento->id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                {{-- 
                                    LINGUAGGIO: Blade Template
                                    FUNZIONE: Blocco @else eseguito se non ci sono malfunzionamenti critici
                                --}}
                                @else
                                    <tr>
                                        {{-- 
                                            LINGUAGGIO: HTML
                                            FUNZIONE: Riga vuota con messaggio quando non ci sono dati
                                            ATTRIBUTO colspan="6": la cella si estende su 6 colonne
                                        --}}
                                        <td colspan="6" class="text-center text-muted py-3 small">
                                            Nessun problema critico recente
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la sezione 'content' iniziata con @section('content')
--}}
@endsection

{{-- === SCRIPTS SECTION === --}}
{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Direttiva @push per aggiungere contenuto alla sezione 'scripts' del layout
    SCOPO: Gli script aggiunti qui vengono inseriti nello stack 'scripts' definito nel layout
    NOTE: Questo consente di posizionare JavaScript specifico della pagina alla fine del body
--}}
@push('scripts')
{{-- 
    LINGUAGGIO: HTML
    FUNZIONE: Include la libreria Chart.js da CDN per creare grafici
    LIBRERIA: Chart.js v3.x - libreria JavaScript per grafici interattivi
    SRC: URL del Content Delivery Network (CDN) per caricare la libreria
--}}
<!-- Chart.js per i grafici -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- 
    LINGUAGGIO: JavaScript
    FUNZIONE: Blocco script inline che prepara i dati per i grafici JavaScript
--}}
<script>
// ===================================================================
// PASSAGGIO DATI DAL CONTROLLER PHP AL JAVASCRIPT
// ===================================================================
// LINGUAGGIO: JavaScript
// FUNZIONE: Trasferisce i dati statistiche dal backend PHP al frontend JavaScript
// SCOPO: Rendere disponibili i dati del controller alle funzioni Chart.js

// LINGUAGGIO: JavaScript
// FUNZIONE console.log(): stampa messaggi nella console del browser per debug
console.log('Inizializzazione dati statistiche tecnico...');

// ===================================================================
// OGGETTO GLOBALE window.statsData
// ===================================================================
// LINGUAGGIO: JavaScript
// FUNZIONE: Crea un oggetto globale che contiene tutti i dati delle statistiche
// SCOPO: Centralizzare i dati in un unico oggetto accessibile da altre funzioni JS
// OGGETTO window: oggetto globale del browser - proprietà aggiunte sono accessibili ovunque

window.statsData = {
    // ===============================================================
    // SEZIONE: DATI MALFUNZIONAMENTI
    // ===============================================================
    // LINGUAGGIO: JavaScript + Blade
    // FUNZIONE: Oggetto contenente statistiche sui malfunzionamenti
    malfunzionamenti: {
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE @json(): helper Blade che converte array PHP in JSON JavaScript
        // PARAMETRO: $stats['malfunzionamenti']['per_gravita'] - array associativo PHP
        // OUTPUT: oggetto JavaScript con coppie chiave-valore (es. {critica: 5, alta: 3})
        per_gravita: @json($stats['malfunzionamenti']['per_gravita'] ?? []),
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Inserisce il valore numerico PHP direttamente nel codice JavaScript
        // SINTASSI {{ }}: output Blade - stampa il valore come numero JavaScript
        // OPERATORE ??: fornisce 0 se il valore non esiste
        totali: {{ $stats['malfunzionamenti']['totali'] ?? 0 }},
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Numero di malfunzionamenti critici
        critici: {{ $stats['malfunzionamenti']['critici'] ?? 0 }}
    },
    
    // ===============================================================
    // SEZIONE: TREND SETTIMANALE
    // ===============================================================
    // LINGUAGGIO: JavaScript + Blade
    // FUNZIONE: Oggetto contenente dati del trend degli ultimi 7 giorni
    trend_settimanale: {
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE @json(): converte array PHP delle etichette giorni in array JavaScript
        // ESEMPIO OUTPUT: ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom']
        giorni: @json($stats['trend_settimanale']['giorni'] ?? []),
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE @json(): converte array PHP dei conteggi in array JavaScript
        // ESEMPIO OUTPUT: [3, 5, 2, 7, 4, 1, 6]
        conteggi: @json($stats['trend_settimanale']['conteggi'] ?? []),
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Totale malfunzionamenti della settimana
        totale_settimana: {{ $stats['trend_settimanale']['totale_settimana'] ?? 0 }}
    },
    
    // ===============================================================
    // SEZIONE: CATEGORIE PRODOTTI
    // ===============================================================
    // LINGUAGGIO: Blade + JavaScript
    // FUNZIONE @json(): converte array associativo categorie in oggetto JavaScript
    // PARAMETRO: $stats['per_categoria'] - array PHP con conteggio per categoria
    // ESEMPIO OUTPUT: {elettrodomestici: 10, sanitari: 5, industriali: 8}
    per_categoria: @json($stats['per_categoria'] ?? []),
    
    // ===============================================================
    // SEZIONE: DATI GENERALI
    // ===============================================================
    // LINGUAGGIO: JavaScript + Blade
    // FUNZIONE: Oggetto con statistiche generali del sistema
    generale: {
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Numero totale di prodotti nel catalogo
        total_prodotti: {{ $stats['generale']['total_prodotti'] ?? 0 }},
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Numero totale di centri di assistenza
        total_centri: {{ $stats['generale']['total_centri'] ?? 0 }}
    },
    
    // ===============================================================
    // SEZIONE: CENTRO ASSISTENZA
    // ===============================================================
    // LINGUAGGIO: Blade + JavaScript
    // FUNZIONE @json(): converte l'intero oggetto centro_assistenza in JSON
    // PARAMETRO: $stats['centro_assistenza'] - può essere null se non disponibile
    // OUTPUT: oggetto JavaScript con proprietà nome, indirizzo, città, ecc. oppure null
    centro_assistenza: @json($stats['centro_assistenza'] ?? null),
    
    // ===============================================================
    // SEZIONE: DATI PERSONALI TECNICO
    // ===============================================================
    // LINGUAGGIO: JavaScript + Blade
    // FUNZIONE: Oggetto con informazioni personali del tecnico autenticato
    personali: {
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Stringa JavaScript con il nome del tecnico
        // SINTASSI '{{ }}': le quotes rendono l'output una stringa JavaScript
        // METODO auth()->user(): recupera l'utente autenticato
        nome: '{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Tecnico' }}',
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Specializzazione del tecnico come stringa JavaScript
        specializzazione: '{{ auth()->user()->specializzazione ?? 'N/A' }}',
        
        // LINGUAGGIO: Blade + JavaScript
        // FUNZIONE: Data di registrazione formattata come stringa ISO
        // METODO format('Y-m-d'): formatta in anno-mese-giorno (es. 2024-03-15)
        data_registrazione: '{{ $stats['personali']['data_registrazione']->format('Y-m-d') ?? '' }}'
    }
};

// ===================================================================
// DEBUG - VERIFICA DATI CARICATI
// ===================================================================
// LINGUAGGIO: JavaScript
// FUNZIONE console.log(): stampa un oggetto strutturato nella console per debug
// SCOPO: Verificare che i dati siano stati passati correttamente da PHP a JavaScript
console.log('Dati statistiche tecnico ricevuti:', {
    malfunzionamenti_per_gravita: window.statsData.malfunzionamenti.per_gravita,
    trend_giorni: window.statsData.trend_settimanale.giorni,
    categorie: window.statsData.per_categoria
});

// ===================================================================
// CONFIGURAZIONE LARAVEL APP
// ===================================================================
// LINGUAGGIO: JavaScript
// FUNZIONE: Inizializza o estende l'oggetto globale LaravelApp
// OPERATORE ||: OR logico - se window.LaravelApp non esiste, crea oggetto vuoto
// SCOPO: Namespace per configurazioni Laravel accessibili da JavaScript
window.LaravelApp = window.LaravelApp || {};

// LINGUAGGIO: JavaScript
// FUNZIONE: Imposta la route corrente per logica condizionale nel codice JS
// PROPRIETÀ route: identifica la pagina corrente
// VALORE: 'tecnico.statistiche.view' - identificatore univoco di questa vista
window.LaravelApp.route = 'tecnico.statistiche.view';

// LINGUAGGIO: JavaScript
// FUNZIONE: Messaggio finale di conferma inizializzazione
console.log('Dati statistiche tecnico inizializzati correttamente');
</script>

{{-- 
    LINGUAGGIO: Blade Template
    FUNZIONE: Chiude la direttiva @push('scripts')
    SCOPO: Termina l'aggiunta di contenuto allo stack 'scripts'
--}}
@endpush

{{-- 
    NOTA IMPORTANTE:
    Il file continua con @push('styles') che contiene solo CSS.
    Come richiesto, NON vengono aggiunti commenti per gli stili CSS.
    Gli stili rimangono invariati senza commenti.
--}}

{{-- === STYLES SECTION === --}}
@push('styles')
<style>
/* ===================================================================
   STILI COMPATTI PER STATISTICHE TECNICO
   =================================================================== */

/* Layout generale compatto */
.container {
    max-width: 1200px;
}

/* Card più compatte e moderne */
.card {
    border-radius: 8px;
    border: none !important;
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/* Header delle card */
.card-header {
    border-radius: 8px 8px 0 0 !important;
    font-size: 0.9rem;
    font-weight: 600;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-body {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Header compatto */
h2 {
    font-size: 1.75rem;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 6px;
}

/* Statistiche header - card piccole */
.card-body.py-2 {
    padding: 0.75rem !important;
}

/* Tabelle più compatte */
.table-sm td, .table-sm th {
    padding: 0.4rem;
    font-size: 0.85rem;
    vertical-align: middle;
}

/* Grafici responsive con altezza fissa */
canvas {
    max-height: 120px !important;
}

/* Badge più piccoli e colorati */
.badge {
    font-size: 0.7rem;
    border-radius: 4px;
    font-weight: 600;
}

/* Pulsanti compatti */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 992px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    canvas {
        max-height: 100px !important;
    }
}

@media (max-width: 768px) {
    /* Header responsive */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .btn-group {
        margin-top: 0.5rem;
    }
    
    /* Card più compatte su mobile */
    .card-body.p-2 {
        padding: 0.5rem !important;
    }
    
    .card-body.p-3 {
        padding: 0.75rem !important;
    }
    
    /* Table responsive */
    .table-responsive {
        font-size: 0.8rem;
    }
    
    /* Grid mobile */
    .col-lg-3,
    .col-lg-4,
    .col-lg-6 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    /* Layout ultra-compatto */
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .card-header h6 {
        font-size: 0.8rem;
    }
    
    .small {
        font-size: 0.75rem !important;
    }
    
    /* Badge e elementi piccoli */
    .badge {
        font-size: 0.65rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
}

/* === ANIMAZIONI E TRANSIZIONI === */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(13, 110, 253, 0.05);
}

/* Spinner loading */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === UTILITÀ === */
.text-muted {
    color: #6c757d !important;
}

.fw-semibold {
    font-weight: 600;
}

/* Focus per accessibilità */
.btn:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Custom scrollbar per tabelle */
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

/* === COLORI TEMA === */
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.card-header.bg-success {
    background-color: #198754 !important;
}

.card-header.bg-info {
    background-color: #0dcaf0 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.card-header.bg-danger {
    background-color: #dc3545 !important;
}

.card-header.bg-secondary {
    background-color: #6c757d !important;
}

/* Badge specifici per gravità */
.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

/* === STAMPA === */
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
        box-shadow: none !important;
    }
    
    .container {
        max-width: none;
        padding: 0;
    }
    
    h2 {
        page-break-after: avoid;
    }
    
    .table {
        font-size: 0.75rem;
    }
}

/* Toast notifications per feedback */
.toast-notification {
    border-radius: 8px;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Performance optimizations */
.card, canvas, .table {
    contain: layout style;
}

/* Custom properties per theming */
:root {
    --tecnico-primary: #0d6efd;
    --tecnico-success: #198754;
    --tecnico-warning: #ffc107;
    --tecnico-danger: #dc3545;
    --tecnico-info: #0dcaf0;
    --tecnico-border-radius: 8px;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Accessibilità migliorata */
.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0,0,0,0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Riduzione movimento per utenti sensibili */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    * {
        transition: none !important;
    }
}

/* Alto contrasto */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid;
    }
}
</style>
@endpush