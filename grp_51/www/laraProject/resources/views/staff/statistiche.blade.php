{{--
    STATISTICHE STAFF - LAYOUT COMPATTO E ANALITICO
    Sistema Assistenza Tecnica - Gruppo 51
    
    DESCRIZIONE: Dashboard analytics per staff con visualizzazione dati e metriche performance
    CARATTERISTICHE: Layout compatto, grafici integrati, comparazioni temporali, ranking
    LINGUAGGIO: Blade template (PHP con sintassi Laravel Blade)
    LIVELLO ACCESSO: 3 (Staff Aziendale - analytics personali e team)
    PATH: resources/views/staff/statistiche.blade.php
--}}

{{-- 
    EXTENDS: Eredita il layout base dell'applicazione
    LINGUAGGIO: Blade directive - template inheritance pattern
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo specifico per pagina statistiche staff
    LINGUAGGIO: Blade directive + SEO-friendly page title
--}}
@section('title', 'Le mie Statistiche - Staff')

{{-- 
    SECTION CONTENT: Contenuto principale dashboard analytics
    LINGUAGGIO: Blade directive - corpo completo con data visualization
--}}
@section('content')
<div class="container mt-4">
    {{-- === HEADER COMPATTO ===
         DESCRIZIONE: Intestazione con controlli periodo e azioni rapide
         LINGUAGGIO: Bootstrap flexbox + conditional styling + Laravel helpers
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                TITOLO ANALYTICS: Header con icon e user identification
                LINGUAGGIO: HTML h2 + Bootstrap Icons + Laravel auth helpers
            --}}
            <h2 class="mb-1">
                <i class="bi bi-graph-up text-warning me-2"></i>
                Le mie Statistiche
            </h2>
            {{-- 
                USER INFO: Nome utente con fallback sicuri
                LINGUAGGIO: Blade output + PHP null coalescing + accessor methods
            --}}
            <p class="text-muted small mb-0">{{ $user->nome_completo ?? $user->name ?? 'Staff Aziendale' }}</p>
            <small class="text-muted">Periodo: ultimi {{ $periodo }} giorni</small>
        </div>
        <div class="btn-group btn-group-sm">
            {{-- 
                CONTROLLI PERIODO: Pulsanti toggle per timeframe analysis
                LINGUAGGIO: HTML links + Laravel route() + conditional CSS classes
            --}}
            <a href="{{ route('staff.statistiche', ['periodo' => 7]) }}" 
               class="btn btn-outline-warning {{ $periodo == 7 ? 'active' : '' }}">7g</a>
            <a href="{{ route('staff.statistiche', ['periodo' => 30]) }}" 
               class="btn btn-outline-warning {{ $periodo == 30 ? 'active' : '' }}">30g</a>
            <a href="{{ route('staff.statistiche', ['periodo' => 90]) }}" 
               class="btn btn-outline-warning {{ $periodo == 90 ? 'active' : '' }}">90g</a>
            {{-- 
                AZIONI CONTROLLO: Refresh e navigazione
                LINGUAGGIO: HTML buttons + JavaScript functions + Laravel routes
            --}}
            <button class="btn btn-primary" onclick="aggiornaStatistiche()">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna
            </button>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- 
        ALERT ERRORE: Gestione errori con styling Bootstrap
        LINGUAGGIO: Blade @if + Bootstrap alert + conditional error display
    --}}
    @if(isset($error))
        <div class="alert alert-warning border-start border-warning border-4 mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $error }}
        </div>
    @endif

    {{-- === STATISTICHE COMPATTE - STILE CARD PICCOLE ===
         DESCRIZIONE: KPI cards con metriche principali staff
         LINGUAGGIO: Bootstrap grid + card system + conditional data display
    --}}
    <div class="row g-2 mb-3">
        {{-- 
            CARD SOLUZIONI CREATE: Metrica produttività principale
            LINGUAGGIO: Bootstrap card + icon system + null safety
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-tools text-success fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['soluzioni_create'] ?? 0 }}</h5>
                    <small class="text-muted">Soluzioni Create</small>
                </div>
            </div>
        </div>
        {{-- Card modifiche effettuate --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-pencil-square text-info fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['soluzioni_modificate'] ?? 0 }}</h5>
                    <small class="text-muted">Modifiche</small>
                </div>
            </div>
        </div>
        {{-- Card problemi critici risolti --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">{{ $stats['critiche_risolte'] ?? 0 }}</h5>
                    <small class="text-muted">Critiche Risolte</small>
                </div>
            </div>
        </div>
        {{-- 
            CARD RANKING: Posizione nel team con conditional display
            LINGUAGGIO: Bootstrap card + PHP ternary operator + conditional output
        --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-2">
                    <i class="bi bi-trophy text-warning fs-4"></i>
                    <h5 class="fw-bold mb-0 mt-1">
                        @if($stats['ranking_posizione'] ?? null)
                            #{{ $stats['ranking_posizione'] }}
                        @else
                            N/A
                        @endif
                    </h5>
                    <small class="text-muted">Ranking</small>
                </div>
            </div>
        </div>
    </div>

    {{-- === LAYOUT LINEARE - GRAFICI AFFIANCATI ===
         DESCRIZIONE: Layout responsive con analytics cards in linea
         LINGUAGGIO: Bootstrap grid responsive + data visualization components
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            ATTIVITÀ PERIODO - COMPATTO
            DESCRIZIONE: Card analytics per periodo corrente con goal tracking
            LINGUAGGIO: Bootstrap card + PHP calculations + progress visualization
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar3 me-1"></i>
                        Attività {{ $periodo }}g
                    </h6>
                </div>
                <div class="card-body p-2">
                    {{-- 
                        STATISTICHE PERIODO: Layout 2x2 per metriche periodo
                        LINGUAGGIO: Bootstrap grid + background utilities + data display
                    --}}
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <div class="h5 text-success fw-bold mb-0">
                                    {{ $stats['soluzioni_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Nuove</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-info bg-opacity-10 rounded">
                                <div class="h5 text-info fw-bold mb-0">
                                    {{ $stats['modifiche_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Modifiche</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        BARRA PROGRESSO OBIETTIVO: Goal tracking con calcoli dinamici
                        LINGUAGGIO: PHP @php block + mathematical calculations + Bootstrap progress
                    --}}
                    <hr class="my-2">
                    @php
                        // Calcolo obiettivo basato su periodo (1 soluzione/settimana)
                        $obiettivo = ceil($periodo / 7);
                        $raggiunte = $stats['soluzioni_periodo'] ?? 0;
                        // Calcolo percentuale con limite max 100%
                        $percentuale = $obiettivo > 0 ? min(100, ($raggiunte / $obiettivo) * 100) : 0;
                    @endphp
                    <div class="mb-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Obiettivo: {{ $obiettivo }}</small>
                            <small class="text-muted">{{ number_format($percentuale, 0) }}%</small>
                        </div>
                        {{-- 
                            PROGRESS BAR: Visualizzazione progresso con width dinamico
                            LINGUAGGIO: Bootstrap progress + inline CSS + dynamic percentage
                        --}}
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $percentuale }}%" 
                                 role="progressbar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 
            DISTRIBUZIONE GRAVITÀ - COMPATTO
            DESCRIZIONE: Analytics distribuzione per livello gravità problemi
            LINGUAGGIO: Bootstrap card + color-coded legend + statistics grid
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Per Gravità
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        {{-- 
                            CRITICHE: Legend item con color indicator
                            LINGUAGGIO: Bootstrap flexbox + color indicators + statistics
                        --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-danger bg-opacity-10 rounded">
                                <div class="bg-danger rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Critiche</small>
                                    <div class="fw-bold small">{{ $stats['critiche_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Gravità alta --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-warning bg-opacity-10 rounded">
                                <div class="bg-warning rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Alte</small>
                                    <div class="fw-bold small">{{ $stats['alte_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Gravità media --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-info bg-opacity-10 rounded">
                                <div class="bg-info rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Medie</small>
                                    <div class="fw-bold small">{{ $stats['medie_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Gravità bassa --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-1 bg-success bg-opacity-10 rounded">
                                <div class="bg-success rounded" style="width: 8px; height: 8px;"></div>
                                <div class="ms-1 flex-grow-1">
                                    <small class="text-muted">Basse</small>
                                    <div class="fw-bold small">{{ $stats['basse_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        TEMPO MEDIO RISPOSTA: KPI performance con conditional display
                        LINGUAGGIO: Blade @if + conditional statistics + time metrics
                    --}}
                    @if($stats['tempo_medio_risposta'] ?? null)
                        <hr class="my-2">
                        <div class="text-center">
                            <small class="text-muted">Tempo Medio</small>
                            <div class="fw-bold text-primary">{{ $stats['tempo_medio_risposta'] }}h</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
            INFO STAFF - COMPATTO
            DESCRIZIONE: Card profilo staff con ranking e team info
            LINGUAGGIO: Bootstrap card + user profile data + team positioning
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-gear me-1"></i>
                        Profilo Staff
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        PROFILO DETTAGLI: Informazioni utente e posizionamento team
                        LINGUAGGIO: HTML paragraphs + fallback data + conditional badges
                    --}}
                    <p class="small mb-1"><strong>Staff:</strong> {{ $user->nome_completo ?? $user->name ?? 'N/A' }}</p>
                    <p class="small mb-1"><strong>Livello:</strong> Staff Tecnico (Livello 3)</p>
                    <p class="small mb-1"><strong>Su totale:</strong> {{ $stats['totale_staff'] ?? 0 }} staff</p>
                    @if($stats['ranking_posizione'] ?? null)
                        <p class="small mb-0">
                            <strong>Posizione:</strong> 
                            <span class="badge bg-warning text-dark">#{{ $stats['ranking_posizione'] }}</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ANDAMENTO MENSILE COMPATTO ===
         DESCRIZIONE: Grafico trend mensile con visualizzazione CSS-based
         LINGUAGGIO: Blade @if + Collection methods + CSS chart + data visualization
    --}}
    @if($attivitaMensile && count($attivitaMensile) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-1"></i>
                            Andamento Mensile (6 mesi)
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        {{-- 
                            GRAFICO BARRE COMPATTO: CSS-based chart implementation
                            LINGUAGGIO: PHP calculations + CSS heights + responsive grid
                        --}}
                        <div class="row g-1">
                            @foreach($attivitaMensile as $mese)
                                <div class="col-2 text-center">
                                    <div class="mb-1">
                                        {{-- 
                                            CALCOLI DINAMICI GRAFICO: Altezze barre proporzionali
                                            LINGUAGGIO: PHP @php block + mathematical calculations + Collection max()
                                        --}}
                                        @php
                                            // Trova valore massimo per scaling proporzionale
                                            $maxValue = max(collect($attivitaMensile)->max('soluzioni_create'), 1);
                                            // Calcola altezze relative (max 60px)
                                            $altezzaCreate = $mese['soluzioni_create'] > 0 ? (($mese['soluzioni_create'] / $maxValue) * 60) : 0;
                                            $altezzaModificate = $mese['soluzioni_modificate'] > 0 ? (($mese['soluzioni_modificate'] / $maxValue) * 60) : 0;
                                        @endphp
                                        {{-- 
                                            CONTAINER BARRE: Fixed height container per chart alignment
                                            LINGUAGGIO: CSS flexbox + dynamic height calculation + tooltips
                                        --}}
                                        <div style="height: 60px;" class="d-flex flex-column justify-content-end">
                                            @if($mese['soluzioni_create'] > 0)
                                                <div class="bg-success rounded-top mb-1 chart-bar" 
                                                     style="height: {{ $altezzaCreate }}%; min-height: 3px;"
                                                     title="Soluzioni: {{ $mese['soluzioni_create'] }}">
                                                </div>
                                            @endif
                                            @if($mese['soluzioni_modificate'] > 0)
                                                <div class="bg-info rounded-bottom chart-bar" 
                                                     style="height: {{ $altezzaModificate }}%; min-height: 3px;"
                                                     title="Modifiche: {{ $mese['soluzioni_modificate'] }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Label mese e badges valori --}}
                                    <small class="text-muted">{{ $mese['mese'] }}</small>
                                    <div class="small">
                                        <span class="badge bg-success">{{ $mese['soluzioni_create'] }}</span>
                                        <span class="badge bg-info">{{ $mese['soluzioni_modificate'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- 
                            LEGENDA COMPATTA: Chart legend con color indicators
                            LINGUAGGIO: Bootstrap grid + flexbox + color legend system
                        --}}
                        <hr class="my-2">
                        <div class="row text-center small">
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-success rounded me-1" style="width: 8px; height: 8px;"></div>
                                    <span class="text-muted">Create</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-info rounded me-1" style="width: 8px; height: 8px;"></div>
                                    <span class="text-muted">Modificate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === SEZIONE DETTAGLI LINEARI ===
         DESCRIZIONE: Layout dettagliato con tabelle e liste per drill-down analytics
         LINGUAGGIO: Bootstrap grid + table responsive + list components
    --}}
    <div class="row g-3 mb-3">
        {{-- 
            PRODOTTI PROBLEMATICI: Tabella prodotti con maggiori soluzioni staff
            DESCRIZIONE: Analytics dettagliata su prodotti gestiti dallo staff
            LINGUAGGIO: Bootstrap table + responsive design + Collection methods
        --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-diamond me-1"></i>
                        Prodotti con Più Tue Soluzioni
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="py-2">Prodotto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2 text-center">Tue Soluzioni</th>
                                    <th class="py-2 text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    LOOP PRODOTTI PROBLEMATICI: Itera Collection con limite
                                    LINGUAGGIO: Blade @if + Collection count() + @foreach + take()
                                --}}
                                @if($prodottiProblematici->count() > 0)
                                    @foreach($prodottiProblematici->take(6) as $prodotto)
                                        <tr class="small">
                                            <td class="py-2">
                                                <strong>{{ $prodotto->nome }}</strong>
                                                {{-- Modello condizionale --}}
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($prodotto->categoria ?? 'generale') }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-center">
                                                {{-- 
                                                    BADGE CONTEGGIO: Soluzioni create da questo staff
                                                    LINGUAGGIO: Bootstrap badge + dynamic property access
                                                --}}
                                                <span class="badge bg-warning text-dark">
                                                    {{ $prodotto->soluzioni_mie }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-end">
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- Stato vuoto tabella --}}
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
            ULTIME SOLUZIONI: Timeline attività recenti staff
            DESCRIZIONE: Lista cronologica soluzioni create/modificate
            LINGUAGGIO: Bootstrap card + list group + Collection methods + date formatting
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-1"></i>
                        Ultime Soluzioni
                    </h6>
                </div>
                <div class="card-body p-2">
                    @if($ultimeSoluzioni->count() > 0)
                        <div class="list-group list-group-flush">
                            {{-- 
                                LOOP SOLUZIONI RECENTI: Timeline con metadata e colori gravità
                                LINGUAGGIO: Blade @foreach + Laravel relationship access + date formatting
                            --}}
                            @foreach($ultimeSoluzioni->take(5) as $soluzione)
                                <div class="list-group-item px-0 border-0 py-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-1">
                                            <h6 class="mb-1 fw-semibold small text-truncate">
                                                {{ Str::limit($soluzione->titolo, 30) }}
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-box me-1"></i>
                                                {{ Str::limit($soluzione->prodotto->nome, 20) }}
                                            </p>
                                            {{-- 
                                                TIMESTAMP FORMATTATO: Data/ora in formato compatto
                                                LINGUAGGIO: Laravel Carbon format() method + localized format
                                            --}}
                                            <small class="text-muted">
                                                {{ $soluzione->created_at->format('d/m H:i') }}
                                            </small>
                                        </div>
                                        <div>
                                            {{-- 
                                                BADGE GRAVITÀ: Colore dinamico + abbreviazione
                                                LINGUAGGIO: PHP ternary nested + substr() for abbreviation
                                            --}}
                                            <span class="badge bg-{{ 
                                                $soluzione->gravita == 'critica' ? 'danger' : 
                                                ($soluzione->gravita == 'alta' ? 'warning' : 
                                                ($soluzione->gravita == 'media' ? 'info' : 'success')) 
                                            }}">
                                                {{ substr(ucfirst($soluzione->gravita), 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Link vedi tutte --}}
                        <div class="text-center mt-2">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" 
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-list me-1"></i>Vedi Tutte
                            </a>
                        </div>
                    @else
                        {{-- Stato vuoto soluzioni --}}
                        <div class="text-center py-3">
                            <i class="bi bi-plus-circle display-6 text-muted opacity-50"></i>
                            <p class="text-muted small mt-2 mb-0">Nessuna soluzione</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === CATEGORIE COMPATTE ===
         DESCRIZIONE: Analytics distribuzione soluzioni per categoria prodotto
         LINGUAGGIO: Blade conditional + Collection methods + responsive grid
    --}}
    @if($soluzioniPerCategoria && $soluzioniPerCategoria->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-tags me-1"></i>
                            Soluzioni per Categoria
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            {{-- 
                                LOOP CATEGORIE: Grid responsive con conteggi categoria
                                LINGUAGGIO: Blade @foreach + Collection take() + string manipulation
                            --}}
                            @foreach($soluzioniPerCategoria->take(6) as $categoria)
                                <div class="col-lg-2 col-md-3 col-4">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h5 text-primary mb-1">{{ $categoria->count }}</div>
                                        {{-- 
                                            NOME CATEGORIA FORMATTATO: Capitalizzazione e spazi
                                            LINGUAGGIO: PHP ucfirst() + str_replace() + null coalescing
                                        --}}
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $categoria->categoria ?? 'Generale')) }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

{{-- === PUSH SCRIPTS ===
     DESCRIZIONE: JavaScript per interattività analytics e data injection
     LINGUAGGIO: Blade @push + JavaScript ES6+ + Laravel data integration
--}}
@push('scripts')
<script>
/* === INIZIALIZZAZIONE DATI PAGINA ===
   LINGUAGGIO: JavaScript ES6 object initialization
   SCOPO: Prepara oggetto globale per funzioni analytics e statistiche */

// Inizializza oggetto dati globale se non esiste
window.PageData = window.PageData || {};

/* === INIEZIONE DATI SERVER-SIDE ===
   LINGUAGGIO: Blade @json directive + JavaScript object assignment
   SCOPO: Trasferisce dati PHP sicuri a JavaScript per analytics client-side */

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

/* === DATI STATISTICHE SPECIFICI ===
   LINGUAGGIO: JavaScript object + Blade conditionals + data injection
   SCOPO: Dati specifici per analytics e visualizzazioni grafiche */

// Dati periodo e analytics per JavaScript
window.PageData.periodo = @json($periodo ?? 30);
window.PageData.attivitaMensile = @json($attivitaMensile ?? []);
window.PageData.prodottiProblematici = @json($prodottiProblematici ?? []);
window.PageData.ultimeSoluzioni = @json($ultimeSoluzioni ?? []);
window.PageData.soluzioniPerCategoria = @json($soluzioniPerCategoria ?? []);

// Dati di sessione per notifiche JavaScript
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionWarning = @json(session('warning'));
window.PageData.sessionInfo = @json(session('info'));

/* === FUNZIONI JAVASCRIPT COLLEGATE ===
   
   Le seguenti funzioni sono implementate nel file JavaScript principale:
   
   1. aggiornaStatistiche() - Refresh real-time statistiche
      LINGUAGGIO: JavaScript AJAX + DOM update + animation
      SCOPO: Aggiorna dati statistiche senza reload pagina
   
   2. generaGraficoAndamento() - Rendering grafico trend
      LINGUAGGIO: JavaScript Canvas API o Chart.js integration
      SCOPO: Visualizza andamento temporale con interattività
   
   3. esportaStatistiche() - Export dati in CSV/PDF
      LINGUAGGIO: JavaScript data processing + file generation
      SCOPO: Genera report scaricabili per staff
   
   4. filtraPerPeriodo() - Filtro temporale dinamico
      LINGUAGGIO: JavaScript URL manipulation + history API
      SCOPO: Cambia periodo senza reload completo pagina
   
   5. mostraDettagliProdotto() - Drill-down su prodotti
      LINGUAGGIO: JavaScript modal + AJAX data loading
      SCOPO: Dettagli analytics specifici per prodotto
   
   6. confrontaConTeam() - Comparazione con altri staff
      LINGUAGGIO: JavaScript data visualization + ranking
      SCOPO: Mostra posizionamento relativo nel team
   
   INTEGRAZIONE BACKEND:
   - Endpoint API per aggiornamento real-time statistiche
   - Cache Redis per performance analytics complesse
   - Database queries ottimizzate per aggregazioni
   - Rate limiting per chiamate frequenti
   
   SECURITY:
   - Validation client-side per parametri periodo
   - CSRF token per tutte le chiamate POST
   - Sanitizzazione input per filtri dinamici
   - Access control per dati altri staff members
*/

/* === CONFIGURAZIONE ANALYTICS ===
   LINGUAGGIO: JavaScript object configuration
   SCOPO: Parametri per comportamento analytics e visualizzazioni */

window.PageData.analyticsConfig = {
    refreshInterval: 300000,         // Refresh ogni 5 minuti
    chartAnimationDuration: 800,     // Durata animazioni grafici
    enableRealTimeUpdates: true,     // Real-time updates via websocket
    maxDataPoints: 100,              // Limite punti dati per performance
    enableNotifications: true,       // Notifiche browser per milestone
    exportFormats: ['csv', 'pdf'],   // Formati export supportati
    enableComparisons: true,         // Abilita confronti con team
    cacheTimeout: 600000,           // Cache locale per 10 minuti
    enableDrillDown: true           // Drill-down su analytics dettagliate
};

/* === GOAL TRACKING ===
   LINGUAGGIO: JavaScript object + mathematical calculations
   SCOPO: Tracking obiettivi e milestone per gamification */

window.PageData.goalTracking = {
    soluzioniMensili: 4,            // Obiettivo soluzioni per mese
    tempoMedioTarget: 2,            // Target ore per soluzione
    accuracyTarget: 95,             // Target accuratezza percentuale
    rankingTarget: 3,               // Obiettivo posizione ranking
    enableMilestones: true,         // Abilita notifiche milestone
    enableProgress: true            // Mostra progress verso obiettivi
};

/* === EVENT LISTENERS ===
   LINGUAGGIO: JavaScript DOM events + event delegation
   SCOPO: Gestione interazioni utente con analytics */

document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 Statistiche staff inizializzate');
    
    // Inizializza tooltips per chart elements
    if (typeof initTooltips === 'function') {
        initTooltips();
    }
    
    // Inizializza real-time updates se abilitati
    if (window.PageData.analyticsConfig.enableRealTimeUpdates) {
        if (typeof initRealTimeUpdates === 'function') {
            initRealTimeUpdates();
        }
    }
    
    // Inizializza export functionality
    if (typeof initExportControls === 'function') {
        initExportControls();
    }
    
    // Track page view per analytics
    if (typeof trackAnalyticsView === 'function') {
        trackAnalyticsView({
            pageType: 'staff_statistics',
            userId: window.PageData.user?.id,
            periodo: window.PageData.periodo,
            timestamp: new Date().toISOString()
        });
    }
});

/* === PERFORMANCE MONITORING ===
   LINGUAGGIO: JavaScript Performance API + timing measurements
   SCOPO: Monitoraggio performance caricamento analytics */

// Performance timing per ottimizzazioni
if (window.performance && window.performance.mark) {
    window.performance.mark('statistics-data-loaded');
    
    // Misura tempo rendering grafici
    window.addEventListener('load', function() {
        window.performance.mark('statistics-render-complete');
        window.performance.measure(
            'statistics-total-time',
            'statistics-data-loaded',
            'statistics-render-complete'
        );
    });
}

/* === ERROR HANDLING ===
   LINGUAGGIO: JavaScript error handling + logging
   SCOPO: Gestione errori specifici per analytics */

window.addEventListener('error', function(e) {
    // Log errori analytics per debugging
    if (window.PageData.app && window.PageData.app.debug) {
        console.error('❌ Errore statistiche staff:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            user: window.PageData.user?.id,
            periodo: window.PageData.periodo,
            timestamp: new Date().toISOString()
        });
    }
});

// Dati aggiuntivi per estensioni future...
window.PageData.pageType = 'staff_statistics';
window.PageData.userLevel = 3;
window.PageData.loadTimestamp = Date.now();
window.PageData.analyticsVersion = '2.1.0';
</script>
@endpush

{{-- === PUSH STYLES ===
     DESCRIZIONE: CSS avanzato per analytics compatti e responsive design
     LINGUAGGIO: Blade @push + CSS3 + animations + media queries
--}}
@push('styles')
<style>
/* === STILI COMPATTI PER STATISTICHE STAFF ===
   LINGUAGGIO: CSS3 con proprietà moderne
   SCOPO: Layout ottimizzato per analytics con design compatto */

/* === CARD SYSTEM COMPATTO ===
   LINGUAGGIO: CSS border-radius + shadows
   SCOPO: Componenti card uniformi per analytics dashboard */

/* Card base con styling moderno */
.card {
    border-radius: 8px; /* Angoli moderatamente arrotondati */
    border: none !important; /* Rimuove bordi default Bootstrap */
}

/* Header card con radius coordinato */
.card-header {
    border-radius: 8px 8px 0 0 !important; /* Solo angoli superiori */
    font-size: 0.9rem; /* Font size ridotto per layout compatto */
}

/* Body card con font ridotto */
.card-body {
    font-size: 0.9rem; /* Testo più compatto per analytics */
}

/* === TABELLE COMPATTE ===
   LINGUAGGIO: CSS table styling + responsive design
   SCOPO: Tabelle ottimizzate per dati analytics numerici */

/* Celle tabella con padding ridotto */
.table-sm td, .table-sm th {
    padding: 0.4rem; /* Padding ridotto per densità informazioni */
    font-size: 0.85rem; /* Font ancora più piccolo per tabelle */
}

/* === GRAFICI RESPONSIVE ===
   LINGUAGGIO: CSS max-height + responsive constraints
   SCOPO: Grafici ottimizzati per spazi compatti */

/* Canvas charts con altezza limitata */
canvas {
    max-height: 120px !important; /* Limita altezza per layout compatto */
}

/* === BADGE SYSTEM ===
   LINGUAGGIO: CSS font-size override
   SCOPO: Badge compatti per metriche numeriche */

/* Badge con font ridotto */
.badge {
    font-size: 0.7rem; /* Font size ridotto per layout denso */
}

/* === BUTTON GROUPS COMPATTI ===
   LINGUAGGIO: CSS padding + font-size overrides
   SCOPO: Controlli compatti per filtri periodo */

/* Button group con sizing ridotto */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem; /* Padding ridotto */
    font-size: 0.8rem; /* Font size compatto */
}

/* === CHART BARS CUSTOM ===
   LINGUAGGIO: CSS transitions + transforms
   SCOPO: Animazioni smooth per chart CSS-based */

/* Barre chart con transizioni */
.chart-bar {
    transition: all 0.2s ease; /* Transizione smooth per hover */
}

/* Hover effect su barre chart */
.chart-bar:hover {
    opacity: 0.8; /* Riduce opacità */
    transform: scaleY(1.02); /* Leggero ingrandimento verticale */
}

/* === PROGRESS BARS ===
   LINGUAGGIO: CSS border-radius + transitions
   SCOPO: Progress bars per goal tracking */

/* Progress container con angoli arrotondati */
.progress {
    border-radius: 6px; /* Angoli arrotondati per design moderno */
}

/* Progress bar con animazione width */
.progress-bar {
    transition: width 0.4s ease; /* Animazione smooth per progressi */
}

/* === LIST GROUPS COMPATTI ===
   LINGUAGGIO: CSS font-size override
   SCOPO: Liste dense per timeline attività */

/* List items con font ridotto */
.list-group-item {
    font-size: 0.85rem; /* Font compatto per liste */
}

/* === RESPONSIVE DESIGN ===
   LINGUAGGIO: CSS Media Queries
   SCOPO: Adattamento layout per dispositivi mobili */

/* === TABLET (768px e sotto) === */
@media (max-width: 768px) {
    /* Card body padding ridotto */
    .card-body {
        padding: 0.75rem; /* Padding ridotto per schermi medi */
    }
    
    /* Tabelle responsive con font ridotto */
    .table-responsive {
        font-size: 0.8rem; /* Font ancora più piccolo per mobile */
    }
    
    /* Colonne con margin bottom per stacking */
    .col-lg-4, .col-lg-8 {
        margin-bottom: 0.5rem; /* Spazio tra elementi stacked */
    }
    
    /* Button groups con wrapping */
    .btn-group-sm {
        flex-wrap: wrap; /* Permette wrapping su mobile */
    }
    
    /* Pulsanti con margin per wrapping */
    .btn-group-sm .btn {
        margin-bottom: 0.25rem; /* Spazio tra pulsanti wrapped */
        border-radius: 0.375rem !important; /* Angoli per pulsanti separati */
    }
}

/* === SMARTPHONE (576px e sotto) === */
@media (max-width: 576px) {
    /* Header responsive - stack verticale */
    .d-flex.justify-content-between {
        flex-direction: column; /* Stack verticale per header */
        align-items: start !important; /* Allineamento a sinistra */
    }
    
    /* Button group full width */
    .btn-group {
        margin-top: 0.5rem; /* Spazio superiore */
        width: 100%; /* Larghezza completa */
    }
    
    /* Font sizes ridotti per mobile */
    .small {
        font-size: 0.75rem !important; /* Font molto piccolo */
    }
    
    .h5 {
        font-size: 1.1rem !important; /* Titoli ridotti */
    }
    
    .fs-4 {
        font-size: 1.2rem !important; /* Icone ridotte */
    }
}

/* === ANIMAZIONI E TRANSIZIONI ===
   LINGUAGGIO: CSS transitions + transforms
   SCOPO: Micro-interazioni per migliorare UX */

/* Card con hover effect */
.card {
    transition: transform 0.2s ease; /* Transizione per hover */
}

.card:hover {
    transform: translateY(-1px); /* Leggero sollevamento */
}

/* === LOADING STATES ===
   LINGUAGGIO: CSS spinner + sizing
   SCOPO: Indicatori loading per aggiornamenti real-time */

/* Spinner piccolo per pulsanti */
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* === COLORI PERSONALIZZATI ===
   LINGUAGGIO: CSS color overrides
   SCOPO: Palette colori consistente per analytics */

/* Text muted standardizzato */
.text-muted {
    color: #6c757d !important; /* Grigio Bootstrap standard */
}

/* Font weight semi-bold */
.fw-semibold {
    font-weight: 600; /* Peso intermedio per enfasi */
}

/* === BACKGROUND UTILITIES ===
   LINGUAGGIO: CSS rgba colors + opacity
   SCOPO: Background semi-trasparenti per metriche */

/* Background success con opacity personalizzata */
.bg-opacity-10 {
    background-color: rgba(var(--bs-success-rgb), 0.1) !important;
}

/* === ICON SIZING ===
   LINGUAGGIO: CSS font-size overrides
   SCOPO: Dimensioni icone coordinate con layout compatto */

/* Icone medie per card headers */
.fs-4 {
    font-size: 1.25rem !important; /* Dimensione media per icone */
}

/* === TABLE HOVER EFFECTS ===
   LINGUAGGIO: CSS custom properties + hover states
   SCOPO: Feedback visivo per interazioni tabella */

/* Hover rows con background leggero */
.table-hover tbody tr:hover {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.025); /* Background hover sottile */
}

/* === CHART BARS STYLING ===
   LINGUAGGIO: CSS border-radius per chart elements
   SCOPO: Styling uniforme per chart CSS-based */

/* Barre chart con angoli minimamente arrotondati */
.chart-bar {
    border-radius: 2px; /* Angoli molto sottili */
}

/* === ALERT SYSTEM ===
   LINGUAGGIO: CSS border-radius + font-size
   SCOPO: Alert compatti per notifiche sistema */

/* Alert con styling compatto */
.alert {
    border-radius: 8px; /* Angoli coordinati con card */
    font-size: 0.9rem; /* Font size coordinato */
}

/* === BREADCRUMB STYLING ===
   LINGUAGGIO: CSS font-size + margin
   SCOPO: Breadcrumb compatto se presente */

/* Breadcrumb compatto */
.breadcrumb {
    font-size: 0.85rem; /* Font ridotto */
    margin-bottom: 0; /* Rimuove margin bottom */
}

/* === PRINT OPTIMIZATIONS ===
   LINGUAGGIO: CSS @media print
   SCOPO: Ottimizzazioni per stampa report */

/* Stili per stampa */
@media print {
    /* Nasconde controlli interattivi */
    .btn, .btn-group {
        display: none !important;
    }
    
    /* Card con bordi per stampa */
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid; /* Evita break in mezzo alla card */
    }
    
    /* Chart bars con colore standard per stampa */
    .chart-bar {
        background-color: #6c757d !important;
    }
}

/* === SCROLLBAR PERSONALIZZATA ===
   LINGUAGGIO: CSS webkit pseudo-elements
   SCOPO: Scrollbar personalizzata per tabelle responsive */

/* Scrollbar orizzontale per tabelle */
.table-responsive::-webkit-scrollbar {
    height: 6px; /* Altezza scrollbar orizzontale */
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1; /* Background track */
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1; /* Colore thumb */
    border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8; /* Hover più scuro */
}

/* === FOCUS ACCESSIBILITY ===
   LINGUAGGIO: CSS :focus pseudo-class + box-shadow
   SCOPO: Accessibilità per navigazione keyboard */

/* Focus migliorato per pulsanti */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* === DISABLED STATES ===
   LINGUAGGIO: CSS :disabled pseudo-class
   SCOPO: Stati disabilitati per controlli */

/* Elementi disabilitati */
.btn:disabled {
    opacity: 0.6; /* Riduce opacità */
    cursor: not-allowed; /* Cursore non permesso */
}

/* === KEYFRAME ANIMATIONS ===
   LINGUAGGIO: CSS @keyframes + animation
   SCOPO: Animazioni per refresh e loading */

/* Animazione spin per refresh */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Spinner con animazione */
.spinner-border {
    animation: spin 1s linear infinite;
}

/* === SPACING UTILITIES ===
   LINGUAGGIO: CSS margin overrides
   SCOPO: Spaziature consistenti per layout */

/* Margin bottom consistente */
.mb-3 {
    margin-bottom: 1rem !important;
}

/* Margin top per sezioni */
.mt-4 {
    margin-top: 1.5rem !important;
}

/* === LINK STYLING ===
   LINGUAGGIO: CSS text-decoration + hover
   SCOPO: Link styling coordinato */

/* Link senza decorazione di default */
a {
    text-decoration: none;
}

/* Underline su hover */
a:hover {
    text-decoration: underline;
}

/* === ULTRA-SMALL SCREENS ===
   LINGUAGGIO: CSS media query per schermi molto piccoli
   SCOPO: Supporto per dispositivi con schermi molto ridotti */

/* Dispositivi ultra-piccoli (360px e sotto) */
@media (max-width: 360px) {
    /* Container con padding minimo */
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Card body con padding ridotto */
    .card-body {
        padding: 0.5rem;
    }
    
    /* Pulsanti ultra-compatti */
    .btn-group-sm .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endpush