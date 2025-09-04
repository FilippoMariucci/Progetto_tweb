@extends('layouts.app')

@section('title', 'Chi Siamo')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER === -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">
                <i class="bi bi-building text-primary me-3"></i>
                La Nostra Azienda
            </h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">
                Da oltre {{ isset($azienda['anni_esperienza']) ? $azienda['anni_esperienza'] : '20' }} anni leader nel settore dell'assistenza tecnica, 
                offriamo soluzioni innovative e supporto professionale per tutti i nostri prodotti.
            </p>
        </div>
    </div>

    <!-- === INFORMAZIONI PRINCIPALI === -->
    <div class="row mb-5 g-4">
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-target text-success me-2"></i>
                        La Nostra Missione
                    </h3>
                    <p class="card-text">
                        {{ $azienda['missione'] ?? 'Fornire assistenza tecnica di eccellenza attraverso un sistema integrato di supporto post-vendita, garantendo soluzioni rapide ed efficaci per tutti i malfunzionamenti dei nostri prodotti. Il nostro obiettivo è assicurare la massima soddisfazione del cliente attraverso competenza tecnica e innovazione continua.' }}
                    </p>
                    
                    <h5 class="mt-4 mb-3">I Nostri Valori</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Eccellenza tecnica:</strong> Competenza e professionalità in ogni intervento</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Innovazione:</strong> Soluzioni all'avanguardia e tecnologie moderne</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Affidabilità:</strong> Supporto costante e tempi di risposta rapidi</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i><strong>Trasparenza:</strong> Comunicazione chiara e prezzi onesti</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        I Nostri Numeri
                    </h3>
                    <div class="row g-4 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-calendar-check text-primary fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['anni_esperienza'] ?? '20' }}+</h3>
                                <small class="text-muted">Anni di Esperienza</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-people text-success fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['clienti_serviti'] ?? '10000' }}+</h3>
                                <small class="text-muted">Clienti Serviti</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-geo-alt text-warning fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['centri_assistenza'] ?? '50' }}+</h3>
                                <small class="text-muted">Centri Assistenza</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-tools text-info fs-1"></i>
                                <h3 class="mt-2 mb-1">{{ $azienda['tecnici_qualificati'] ?? '200' }}+</h3>
                                <small class="text-muted">Tecnici Qualificati</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">Certificazioni e Riconoscimenti</h5>
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

    <!-- === SETTORI DI COMPETENZA === -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-gear text-primary me-2"></i>
                I Nostri Settori di Competenza
            </h2>
            <div class="row g-4">
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
                
                @foreach($settori as $settore)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom h-100 text-center">
                            <div class="card-body">
                                <div class="mb-3">
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

    <!-- === STORIA AZIENDALE === -->
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
                    <div class="timeline">
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
                        
                        @foreach($storia as $index => $evento)
                            <div class="timeline-item {{ $index % 2 == 0 ? 'left' : 'right' }} mb-4">
                                <div class="timeline-marker">
                                    <div class="timeline-year bg-primary text-white">{{ $evento['anno'] }}</div>
                                </div>
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

    <!-- === TEAM E ORGANIZZAZIONE === -->
    <div class="row mb-5">
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

    <!-- === INFORMAZIONI LEGALI === -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                Informazioni Legali
                            </h5>
                            <ul class="list-unstyled">
                                <li><strong>Ragione Sociale:</strong> {{ $azienda['ragione_sociale'] ?? 'TechAssist Solutions S.r.l.' }}</li>
                                <li><strong>P.IVA:</strong> {{ $azienda['partita_iva'] ?? '12345678901' }}</li>
                                <li><strong>Codice Fiscale:</strong> {{ $azienda['codice_fiscale'] ?? '12345678901' }}</li>
                                <li><strong>REA:</strong> {{ $azienda['rea'] ?? 'AN-123456' }}</li>
                                <li><strong>Capitale Sociale:</strong> {{ $azienda['capitale_sociale'] ?? '€ 100.000,00 i.v.' }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                Sede Legale
                            </h5>
                            <address class="mb-3">
                                <strong>{{ $azienda['ragione_sociale'] ?? 'TechAssist Solutions S.r.l.' }}</strong><br>
                                {{ $azienda['indirizzo_sede'] ?? 'Via dell\'Innovazione, 123' }}<br>
                                {{ $azienda['cap_sede'] ?? '60131' }} {{ $azienda['citta_sede'] ?? 'Ancona' }} ({{ $azienda['provincia_sede'] ?? 'AN' }})<br>
                                <i class="bi bi-telephone me-1"></i> {{ $azienda['telefono_sede'] ?? '+39 071 123456' }}<br>
                                <i class="bi bi-envelope me-1"></i> <a href="mailto:{{ $azienda['email_sede'] ?? 'info@techassist.it' }}">{{ $azienda['email_sede'] ?? 'info@techassist.it' }}</a>
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === CALL TO ACTION === -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="card card-custom bg-primary text-white">
                <div class="card-body py-5">
                    <h2 class="mb-4">Inizia Subito con il Nostro Supporto</h2>
                    <p class="lead mb-4">
                        Hai bisogno di assistenza tecnica? Il nostro team è pronto ad aiutarti con competenza e professionalità.
                    </p>
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

@push('styles')
<style>
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.timeline {
    position: relative;
    padding: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--bs-primary);
    transform: translateX(-50%);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
}

.timeline-year {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: bold;
    font-size: 0.9rem;
    white-space: nowrap;
}

.timeline-content {
    width: 45%;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.timeline-item.left .timeline-content {
    margin-left: 0;
    margin-right: auto;
}

.timeline-item.right .timeline-content {
    margin-left: auto;
    margin-right: 0;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
    padding: 0.5rem 0.75rem;
}

@media (max-width: 768px) {
    .timeline::before {
        left: 20px;
    }
    
    .timeline-marker {
        left: 20px;
    }
    
    .timeline-content {
        width: calc(100% - 60px);
        margin-left: 60px !important;
        margin-right: 0 !important;
    }
    
    .timeline-year {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
}

/* Animazioni per gli elementi */
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

.card-custom {
    animation: fadeInUp 0.6s ease-out;
}

.timeline-item:nth-child(1) { animation-delay: 0.1s; }
.timeline-item:nth-child(2) { animation-delay: 0.2s; }
.timeline-item:nth-child(3) { animation-delay: 0.3s; }
.timeline-item:nth-child(4) { animation-delay: 0.4s; }
.timeline-item:nth-child(5) { animation-delay: 0.5s; }
</style>
@endpush

@push('scripts')
<script>
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush
