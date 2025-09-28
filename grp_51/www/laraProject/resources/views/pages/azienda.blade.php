{{-- 
    Vista per la pagina "Chi Siamo" dell'azienda
    LINGUAGGIO: Blade Template (Laravel) - pagina informativa aziendale
    SCOPO: Presentazione istituzionale dell'azienda con informazioni, storia e servizi
    ACCESSO: Pagina pubblica, accessibile a tutti gli utenti senza autenticazione
    PERCORSO: resources/views/chi-siamo.blade.php (o simile)
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- Titolo statico della pagina per SEO e navigazione --}}
@section('title', 'Chi Siamo')

{{-- Inizio sezione contenuto principale --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">
    
    {{-- 
        SEZIONE HEADER PRINCIPALE
        Layout centralizzato con titolo e introduzione
    --}}
    <div class="row mb-5">
        <div class="col-12 text-center">
            {{-- 
                Titolo principale con display class Bootstrap
                CSS: display-4 per dimensioni importanti
            --}}
            <h1 class="display-4 mb-3">
                <i class="bi bi-building text-primary me-3"></i>
                La Nostra Azienda
            </h1>
            {{-- 
                Testo introduttivo con stile lead
                PHP: Operatore ternario per valori di fallback
                CSS: max-width inline per limitare lunghezza riga
            --}}
            <p class="lead text-muted mx-auto" style="max-width: 800px;">
                Da oltre {{ isset($azienda['anni_esperienza']) ? $azienda['anni_esperienza'] : '20' }} anni leader nel settore dell'assistenza tecnica, 
                offriamo soluzioni innovative e supporto professionale per tutti i nostri prodotti.
            </p>
        </div>
    </div>

    {{-- 
        SEZIONE INFORMAZIONI PRINCIPALI
        Layout a due colonne con card di pari altezza
        Bootstrap: g-4 per gap tra colonne
    --}}
    <div class="row mb-5 g-4">
        {{-- 
            COLONNA 1: MISSIONE E VALORI
            Bootstrap: col-lg-6 per split 50/50 su desktop
        --}}
        <div class="col-lg-6">
            {{-- 
                Card con altezza uniforme
                CSS: h-100 per card di pari altezza, card-custom per stili personalizzati
            --}}
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-target text-success me-2"></i>
                        La Nostra Missione
                    </h3>
                    {{-- 
                        Contenuto dinamico con fallback
                        PHP: ?? operatore null coalescing per testo di default
                    --}}
                    <p class="card-text">
                        {{ $azienda['missione'] ?? 'Fornire assistenza tecnica di eccellenza attraverso un sistema integrato di supporto post-vendita, garantendo soluzioni rapide ed efficaci per tutti i malfunzionamenti dei nostri prodotti. Il nostro obiettivo è assicurare la massima soddisfazione del cliente attraverso competenza tecnica e innovazione continua.' }}
                    </p>
                    
                    {{-- Sezione valori aziendali --}}
                    <h5 class="mt-4 mb-3">I Nostri Valori</h5>
                    {{-- 
                        Lista non ordinata per valori
                        HTML: list-unstyled per rimuovere bullet points
                    --}}
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Eccellenza tecnica:</strong> Competenza e professionalità in ogni intervento</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Innovazione:</strong> Soluzioni all'avanguardia e tecnologie moderne</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Affidabilità:</strong> Supporto costante e tempi di risposta rapidi</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Trasparenza:</strong> Comunicazione chiara e prezzi onesti</li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- 
            COLONNA 2: STATISTICHE E NUMERI
            Metriche aziendali con layout griglia
        --}}
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        I Nostri Numeri
                    </h3>
                    {{-- 
                        Grid 2x2 per statistiche
                        Bootstrap: g-4 per gap, text-center per centraggio
                    --}}
                    <div class="row g-4 text-center">
                        {{-- Statistica 1: Anni esperienza --}}
                        <div class="col-6">
                            {{-- 
                                Container con sfondo colorato trasparente
                                CSS: bg-opacity-10 per trasparenza
                            --}}
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-calendar-check text-primary fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['anni_esperienza'] ?? '20' }}+</h3>
                                <small class="text-muted">Anni di Esperienza</small>
                            </div>
                        </div>
                        {{-- Statistica 2: Clienti serviti --}}
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-people text-success fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['clienti_serviti'] ?? '10000' }}+</h3>
                                <small class="text-muted">Clienti Serviti</small>
                            </div>
                        </div>
                        {{-- Statistica 3: Centri assistenza --}}
                        <div class="col-6">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-geo-alt text-warning fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['centri_assistenza'] ?? '50' }}+</h3>
                                <small class="text-muted">Centri Assistenza</small>
                            </div>
                        </div>
                        {{-- Statistica 4: Tecnici qualificati --}}
                        <div class="col-6">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-tools text-info fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['tecnici_qualificati'] ?? '200' }}+</h3>
                                <small class="text-muted">Tecnici Qualificati</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Sezione certificazioni --}}
                    <div class="mt-4">
                        <h5 class="mb-3">Certificazioni e Riconoscimenti</h5>
                        {{-- 
                            Layout flex per badge responsive
                            Bootstrap: d-flex flex-wrap gap-2
                        --}}
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-success fs-6 py-2 px-3">ISO 9001:2015</span>
                            <span class="badge bg-primary fs-6 py-2 px-3">Certificazione CE</span>
                            <span class="badge bg-info fs-6 py-2 px-3">Partner Tecnico Autorizzato</span>
                            <span class="badge bg-warning fs-6 py-2 px-3">Garanzia Qualità</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE SETTORI DI COMPETENZA
        Griglia di card per i diversi settori serviti
    --}}
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-gear text-primary me-2"></i>
                I Nostri Settori di Competenza
            </h2>
            <div class="row g-4">
                {{-- 
                    Definizione array settori con PHP
                    Blade: @php per logica complessa
                    Array associativo per organizzare dati
                --}}
                @php
                $settori = [
                    [
                        'nome' => 'Elettrodomestici',
                        'icona' => 'bi-house',
                        'descrizione' => 'Assistenza specializzata per lavatrici, lavastoviglie, forni, frigoriferi e piccoli elettrodomestici',
                        'colore' => 'primary'
                    ],
                    [
                        'nome' => 'Attrezzature Industriali',
                        'icona' => 'bi-gear-wide-connected',
                        'descrizione' => 'Manutenzione e riparazione di macchinari industriali e sistemi di produzione',
                        'colore' => 'success'
                    ],
                    [
                        'nome' => 'Sistemi di Comunicazione',
                        'icona' => 'bi-wifi',
                        'descrizione' => 'Supporto per apparecchiature telefoniche, router, sistemi di rete e comunicazione',
                        'colore' => 'info'
                    ],
                    [
                        'nome' => 'Attrezzature Sanitarie',
                        'icona' => 'bi-heart-pulse',
                        'descrizione' => 'Assistenza tecnica per dispositivi medicali e attrezzature sanitarie',
                        'colore' => 'warning'
                    ],
                    [
                        'nome' => 'Informatica',
                        'icona' => 'bi-laptop',
                        'descrizione' => 'Supporto tecnico per computer, server, stampanti e periferiche IT',
                        'colore' => 'secondary'
                    ],
                    [
                        'nome' => 'Automazione',
                        'icona' => 'bi-cpu',
                        'descrizione' => 'Sistemi di controllo automatico, PLC e soluzioni domotiche avanzate',
                        'colore' => 'danger'
                    ]
                ];
                @endphp
                
                {{-- 
                    Iterazione sui settori per generare card
                    Foreach Blade per ogni settore definito
                    Bootstrap: col-md-6 col-lg-4 per layout responsive 3 colonne desktop
                --}}
                @foreach($settori as $settore)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom h-100 text-center">
                            <div class="card-body">
                                {{-- Icona grande per il settore --}}
                                <div class="mb-3">
                                    {{-- 
                                        Icona dinamica con colore del settore
                                        Bootstrap: display-4 per dimensioni grandi
                                    --}}
                                    <i class="bi {{ $settore['icona'] }} text-{{ $settore['colore'] }} display-4"></i>
                                </div>
                                <h5 class="card-title">{{ $settore['nome'] }}</h5>
                                <p class="card-text text-muted">{{ $settore['descrizione'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE STORIA AZIENDALE
        Timeline verticale con eventi storici
    --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h3 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        La Nostra Storia
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Container timeline personalizzato --}}
                    <div class="timeline">
                        {{-- 
                            Definizione eventi storici con calcoli dinamici
                            PHP: Calcoli matematici per anni progressivi
                        --}}
                        @php
                        $storia = [
                            [
                                'anno' => $azienda['anno_fondazione'] ?? '2004',
                                'titolo' => 'Fondazione dell\'Azienda',
                                'descrizione' => 'Nasce la nostra azienda con l\'obiettivo di rivoluzionare il settore dell\'assistenza tecnica post-vendita.'
                            ],
                            [
                                'anno' => ($azienda['anno_fondazione'] ?? 2004) + 5,
                                'titolo' => 'Espansione Nazionale',
                                'descrizione' => 'Apertura dei primi 10 centri di assistenza sul territorio nazionale e raggiungimento di 1000 clienti serviti.'
                            ],
                            [
                                'anno' => ($azienda['anno_fondazione'] ?? 2004) + 10,
                                'titolo' => 'Innovazione Digitale',
                                'descrizione' => 'Lancio della piattaforma web per l\'assistenza tecnica online, permettendo ai tecnici di accedere in tempo reale alle soluzioni.'
                            ],
                            [
                                'anno' => ($azienda['anno_fondazione'] ?? 2004) + 15,
                                'titolo' => 'Certificazioni Qualità',
                                'descrizione' => 'Ottenimento delle principali certificazioni di qualità ISO e riconoscimento come partner tecnico autorizzato.'
                            ],
                            [
                                'anno' => 'Oggi',
                                'titolo' => 'Leadership di Settore',
                                'descrizione' => 'Oltre 50 centri di assistenza, 200+ tecnici qualificati e un sistema integrato all\'avanguardia per il supporto clienti.'
                            ]
                        ];
                        @endphp
                        
                        {{-- 
                            Generazione timeline con eventi alternati
                            $index per determinare posizionamento left/right
                            $loop->index è automatico in foreach Blade
                        --}}
                        @foreach($storia as $index => $evento)
                            <div class="timeline-item {{ $index % 2 == 0 ? 'left' : 'right' }} mb-4">
                                {{-- Marker centrale timeline --}}
                                <div class="timeline-marker">
                                    <div class="timeline-year bg-primary text-white">{{ $evento['anno'] }}</div>
                                </div>
                                {{-- Contenuto evento --}}
                                <div class="timeline-content">
                                    <h5>{{ $evento['titolo'] }}</h5>
                                    <p class="text-muted mb-0">{{ $evento['descrizione'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE TEAM E ORGANIZZAZIONE
        Informazioni sul personale e vantaggi competitivi
    --}}
    <div class="row mb-5">
        {{-- COLONNA 1: Team aziendale --}}
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-people text-success me-2"></i>
                        Il Nostro Team
                    </h3>
                    <p class="card-text">
                        Il nostro successo si basa su un team di professionisti altamente qualificati e appassionati del loro lavoro. 
                        Ogni membro del nostro staff è costantemente aggiornato sulle ultime tecnologie e metodologie di assistenza.
                    </p>
                    
                    {{-- 
                        Ruoli del team con icone e descrizioni
                        Layout verticale per chiarezza gerarchica
                    --}}
                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-shield-check text-danger fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Amministratori</h6>
                                    <small class="text-muted">Gestione sistema e supervisione generale</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-person-badge text-warning fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Staff Aziendale</h6>
                                    <small class="text-muted">Gestione soluzioni e base di conoscenza</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-tools text-info fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Tecnici Qualificati</h6>
                                    <small class="text-muted">Assistenza diretta e interventi sul campo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- COLONNA 2: Vantaggi competitivi --}}
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-award text-warning me-2"></i>
                        Perché Sceglierci
                    </h3>
                    <p class="card-text">
                        La nostra esperienza ventennale nel settore ci ha permesso di sviluppare un sistema di assistenza 
                        unico nel suo genere, che garantisce risultati eccellenti e massima soddisfazione del cliente.
                    </p>
                    
                    {{-- 
                        Grid 2x2 per vantaggi competitivi
                        Layout compatto con icone grandi
                    --}}
                    <div class="mt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-lightning text-warning fs-2"></i>
                                    <h6 class="mt-2 mb-1">Interventi Rapidi</h6>
                                    <small class="text-muted">24-48 ore</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-geo-alt text-info fs-2"></i>
                                    <h6 class="mt-2 mb-1">Copertura Nazionale</h6>
                                    <small class="text-muted">Su tutto il territorio</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-headset text-success fs-2"></i>
                                    <h6 class="mt-2 mb-1">Supporto 24/7</h6>
                                    <small class="text-muted">Sempre disponibili</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 border rounded">
                                    <i class="bi bi-patch-check text-primary fs-2"></i>
                                    <h6 class="mt-2 mb-1">Garanzia Qualità</h6>
                                    <small class="text-muted">Lavoro garantito</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE INFORMAZIONI LEGALI
        Dati societari e contatti ufficiali
    --}}
    <div class="row mb-5">
        <div class="col-12">
            {{-- Card con sfondo grigio per distinguere --}}
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <div class="row">
                        {{-- COLONNA 1: Dati legali --}}
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                Informazioni Legali
                            </h5>
                            {{-- 
                                Lista informazioni legali con fallback
                                HTML: list-unstyled per layout pulito
                            --}}
                            <ul class="list-unstyled">
                                <li><strong>Ragione Sociale:</strong> {{ $azienda['ragione_sociale'] ?? 'TechAssist Solutions S.r.l.' }}</li>
                                <li><strong>P.IVA:</strong> {{ $azienda['partita_iva'] ?? '12345678901' }}</li>
                                <li><strong>Codice Fiscale:</strong> {{ $azienda['codice_fiscale'] ?? '12345678901' }}</li>
                                <li><strong>REA:</strong> {{ $azienda['rea'] ?? 'AN-123456' }}</li>
                                <li><strong>Capitale Sociale:</strong> {{ $azienda['capitale_sociale'] ?? '€ 100.000,00 i.v.' }}</li>
                            </ul>
                        </div>
                        {{-- COLONNA 2: Sede legale --}}
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                Sede Legale
                            </h5>
                            {{-- 
                                Tag address HTML semantico per informazioni contatto
                                Struttura standard indirizzo italiano
                            --}}
                            <address class="mb-3">
                                <strong>{{ $azienda['ragione_sociale'] ?? 'TechAssist Solutions S.r.l.' }}</strong><br>
                                {{ $azienda['indirizzo_sede'] ?? 'Via dell\'Innovazione, 123' }}<br>
                                {{ $azienda['cap_sede'] ?? '60131' }} {{ $azienda['citta_sede'] ?? 'Ancona' }} ({{ $azienda['provincia_sede'] ?? 'AN' }})<br>
                                <i class="bi bi-telephone me-1"></i> {{ $azienda['telefono_sede'] ?? '+39 071 123456' }}<br>
                                {{-- 
                                    Link email con mailto
                                    HTML: mailto: per aprire client email
                                --}}
                                <i class="bi bi-envelope me-1"></i> <a href="mailto:{{ $azienda['email_sede'] ?? 'info@techassist.it' }}">{{ $azienda['email_sede'] ?? 'info@techassist.it' }}</a>
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE CALL TO ACTION
        Invito all'azione per coinvolgere gli utenti
    --}}
    <div class="row">
        <div class="col-12 text-center">
            {{-- Card con sfondo colorato per risaltare --}}
            <div class="card card-custom bg-primary text-white">
                <div class="card-body py-5">
                    <h2 class="mb-4">Inizia Subito con il Nostro Supporto</h2>
                    <p class="lead mb-4">
                        Hai bisogno di assistenza tecnica? Il nostro team è pronto ad aiutarti con competenza e professionalità.
                    </p>
                    {{-- 
                        Pulsanti CTA con routing Laravel
                        Bootstrap: d-flex flex-wrap justify-content-center gap-3
                        Laravel: route() helper per generare URL
                    --}}
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-search me-2"></i>Cerca Soluzioni
                        </a>
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-geo-alt me-2"></i>Trova un Centro
                        </a>
                        <a href="{{ route('contatti') }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-telephone me-2"></i>Contattaci Ora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade: @push('styles') per CSS specifico di questa pagina
--}}
@push('styles')
<style>
/* 
    CSS: STILI BASE PER CARD PERSONALIZZATE
    Design system coerente per tutti gli elementi card
*/
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);  /* Ombra sottile */
    border: 1px solid rgba(0, 0, 0, 0.125);              /* Bordo leggero */
    transition: all 0.2s ease-in-out;                     /* Transizione smooth */
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);       /* Ombra più pronunciata */
    transform: translateY(-2px);                          /* Leggero sollevamento */
}

/* 
    CSS: STILI TIMELINE PERSONALIZZATA
    Timeline verticale con linea centrale e eventi alternati
*/
.timeline {
    position: relative;    /* Per posizionamento elementi figli */
    padding: 0;           /* Rimuove padding default */
}

/* Linea centrale verticale della timeline */
.timeline::before {
    content: '';                              /* Pseudo-elemento vuoto */
    position: absolute;                       /* Posizionamento assoluto */
    left: 50%;                               /* Centro orizzontale */
    top: 0;                                  /* Dall'alto */
    bottom: 0;                               /* Al basso */
    width: 3px;                              /* Spessore linea */
    background: var(--bs-primary);           /* Colore primario Bootstrap */
    transform: translateX(-50%);             /* Centraggio perfetto */
}

/* Elemento singolo della timeline */
.timeline-item {
    position: relative;    /* Per posizionamento marker */
    margin-bottom: 2rem;   /* Spaziatura tra eventi */
}

/* Marker centrale per ogni evento */
.timeline-marker {
    position: absolute;                      /* Posizionamento assoluto */
    left: 50%;                              /* Centro timeline */
    transform: translateX(-50%);            /* Centraggio perfetto */
    z-index: 2;                             /* Sopra la linea */
}

/* Anno nell'elemento marker */
.timeline-year {
    padding: 0.5rem 1rem;     /* Padding interno */
    border-radius: 25px;      /* Bordi molto arrotondati */
    font-weight: bold;        /* Testo in grassetto */
    font-size: 0.9rem;        /* Font leggermente ridotto */
    white-space: nowrap;      /* Evita wrapping testo */
}

/* Contenuto dell'evento timeline */
.timeline-content {
    width: 45%;                                    /* Larghezza evento */
    padding: 1.5rem;                              /* Padding generoso */
    background: white;                            /* Sfondo bianco */
    border-radius: 8px;                           /* Bordi arrotondati */
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);     /* Ombra per elevazione */
    border: 1px solid #e9ecef;                   /* Bordo grigio */
}

/* Posizionamento eventi a sinistra */
.timeline-item.left .timeline-content {
    margin-left: 0;          /* Allinea a sinistra */
    margin-right: auto;      /* Margine automatico a destra */
}

/* Posizionamento eventi a destra */
.timeline-item.right .timeline-content {
    margin-left: auto;       /* Margine automatico a sinistra */
    margin-right: 0;         /* Allinea a destra */
}

/* Stili badge personalizzati */
.badge.fs-6 {
    font-size: 0.875rem !important;      /* Font size custom per badge grandi */
    padding: 0.5rem 0.75rem;             /* Padding generoso per leggibilità */
}

/* 
    CSS RESPONSIVE: ADATTAMENTI MOBILE
    Media query per ottimizzazione timeline su dispositivi piccoli
*/
@media (max-width: 768px) {
    /* Timeline mobile: linea a sinistra invece che centrale */
    .timeline::before {
        left: 20px;                       /* Sposta linea a sinistra */
    }
    
    /* Marker mobile: allineato a sinistra */
    .timeline-marker {
        left: 20px;                       /* Allinea con linea */
    }
    
    /* Contenuto mobile: occupa spazio disponibile */
    .timeline-content {
        width: calc(100% - 60px);         /* Larghezza calcolata */
        margin-left: 60px !important;     /* Margine fisso da sinistra */
        margin-right: 0 !important;       /* Rimuove margine destro */
    }
    
    /* Anno mobile: dimensioni ridotte */
    .timeline-year {
        font-size: 0.8rem;                /* Font più piccolo */
        padding: 0.375rem 0.75rem;        /* Padding ridotto */
    }
}

/* 
    CSS: ANIMAZIONI PER ELEMENTI
    Keyframe animations per migliorare UX al caricamento
*/
@keyframes fadeInUp {
    from {
        opacity: 0;                       /* Inizia invisibile */
        transform: translateY(30px);      /* Parte da sotto */
    }
    to {
        opacity: 1;                       /* Diventa visibile */
        transform: translateY(0);         /* Raggiunge posizione finale */
    }
}

/* Applicazione animazione alle card */
.card-custom {
    animation: fadeInUp 0.6s ease-out;   /* Durata e easing */
}

/* 
    CSS: ANIMAZIONI SCAGLIONATE PER TIMELINE
    Delay progressivo per effetto cascade
*/
.timeline-item:nth-child(1) { animation-delay: 0.1s; }  /* Primo elemento */
.timeline-item:nth-child(2) { animation-delay: 0.2s; }  /* Secondo elemento */
.timeline-item:nth-child(3) { animation-delay: 0.3s; }  /* Terzo elemento */
.timeline-item:nth-child(4) { animation-delay: 0.4s; }  /* Quarto elemento */
.timeline-item:nth-child(5) { animation-delay: 0.5s; }  /* Quinto elemento */
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT
    Blade: @push('scripts') per JavaScript specifico della pagina
    Pattern standard per trasferimento dati PHP→JS
--}}
@push('scripts')
<script>
/*
    JavaScript: Inizializzazione dati globali della pagina
    Pattern singleton per gestione stato applicazione
    Utilizzato per eventuali funzionalità interattive future
*/

// Inizializza oggetto globale se non esiste (pattern safe)
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    @json() garantisce encoding sicuro con escape automatico
    isset() previene errori per variabili non definite dal controller
    
    NOTA: Per la pagina "Chi Siamo" molti di questi dati potrebbero non essere necessari,
    ma il pattern è mantenuto per coerenza con altre viste dell'applicazione
*/

// Dati prodotto (probabilmente non utilizzati in questa vista)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Array prodotti (per eventuale integrazione con catalogo)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento singolo (non pertinente per chi siamo)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti (per eventuali statistiche)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro di assistenza (per informazioni geografiche)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Elenco centri (utilizzabile per mappa interattiva)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti (per sezione competenze)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members (per sezione team)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche aziendali (utilizzabili per dashboard)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente (per personalizzazione esperienza)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    FUNZIONALITÀ JAVASCRIPT POTENZIALI PER QUESTA VISTA:
    
    1. Animazioni scroll-triggered
       - Animazioni che si attivano quando elementi entrano nel viewport
       - Contatori animati per statistiche aziendali
       - Fade-in progressivo per timeline eventi
    
    2. Mappa interattiva centri assistenza
       - Integrazione Google Maps o Leaflet
       - Localizzazione utente per centro più vicino
       - Tooltip informativi per ogni centro
    
    3. Galleria immagini team/uffici
       - Carousel o lightbox per foto aziendali
       - Lazy loading per ottimizzare performance
       - Zoom e navigazione touch-friendly
    
    4. Modulo contatti dinamico
       - Validazione form lato client
       - Invio AJAX senza refresh pagina
       - Feedback utente con notifiche
    
    5. Analytics e tracking
       - Tracking tempo permanenza sezioni
       - Heatmap interazioni utente
       - Conversioni CTA (Call To Action)
    
    ESEMPIO IMPLEMENTAZIONE CONTATORI ANIMATI:
    
    function animateCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.dataset.counter);
                    const duration = 2000; // 2 secondi
                    const step = target / (duration / 16); // 60fps
                    
                    let current = 0;
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.textContent = Math.floor(current);
                    }, 16);
                    
                    observer.unobserve(counter);
                }
            });
        });
        
        counters.forEach(counter => observer.observe(counter));
    }
    
    // Inizializzazione al caricamento DOM
    document.addEventListener('DOMContentLoaded', animateCounters);
    
    INTEGRAZIONE DATI AZIENDALI:
    
    // Accesso dati azienda se passati dal controller
    if (window.PageData.azienda) {
        console.log('Azienda:', window.PageData.azienda.ragione_sociale);
        console.log('Anni esperienza:', window.PageData.azienda.anni_esperienza);
    }
    
    // Utilizzo statistiche per dashboard dinamica
    if (window.PageData.stats) {
        // Aggiorna contatori con dati reali
        // Genera grafici performance
        // Mostra KPI aziendali
    }
    
    PATTERN UTILIZZABILI:
    - Intersection Observer per lazy loading e animazioni
    - Fetch API per caricamento dati dinamici
    - Local Storage per preferenze utente
    - Service Worker per caching risorse
    - Web Components per elementi riutilizzabili
*/

/*
    ESEMPIO GESTIONE FORM CONTATTI (se presente):
    
    function setupContactForm() {
        const form = document.querySelector('#contactForm');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            
            // Stato loading
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Invio...';
            
            try {
                const response = await fetch('/contatti', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showAlert('Messaggio inviato con successo!', 'success');
                    form.reset();
                } else {
                    showAlert(result.message || 'Errore invio messaggio', 'error');
                }
            } catch (error) {
                console.error('Errore:', error);
                showAlert('Errore di connessione. Riprova più tardi.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Invia Messaggio';
            }
        });
    }
    
    // Utility per notifiche
    function showAlert(message, type = 'info') {
        // Implementazione toast/alert personalizzati
        // Auto-dismiss dopo timeout
        // Gestione stack multiple notifiche
    }
    
    document.addEventListener('DOMContentLoaded', setupContactForm);
*/

</script>
@endpush