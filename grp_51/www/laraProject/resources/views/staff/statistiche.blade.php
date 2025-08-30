{{-- 
    VISTA STATISTICHE STAFF AZIENDALE
    File: resources/views/staff/statistiche.blade.php
    
    Questa vista mostra le statistiche dettagliate per i membri dello staff aziendale (Livello 3).
    Include andamento mensile, distribuzione per categoria, e azioni rapide per operazioni comuni.
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- Definisce il titolo dinamico della pagina --}}
@section('title', 'Statistiche Staff - ' . ($user->nome_completo ?? $user->name ?? 'Staff'))

{{-- Contenuto principale della pagina --}}
@section('content')
<div class="container-fluid mt-4">
    
    {{-- === SEZIONE HEADER PRINCIPALE === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Header con titolo e controlli --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                {{-- Informazioni principale --}}
                <div>
                    {{-- Titolo della pagina con icona --}}
                    <h1 class="h2 mb-1">
                        <i class="bi bi-graph-up text-warning me-2"></i>
                        Statistiche Staff Aziendale
                    </h1>
                    {{-- Descrizione della pagina --}}
                    <p class="text-muted mb-0">
                        Analisi dettagliate della tua attività di staff tecnico
                    </p>
                    {{-- Informazioni contestuali sul periodo e utente --}}
                    <small class="text-muted">
                        Staff: {{ $user->nome_completo ?? $user->name ?? $user->username }}
                        • Periodo: ultimi {{ $periodo }} giorni
                    </small>
                </div>
                
                {{-- Controlli per cambiare periodo e refresh --}}
                <div>
                    {{-- Gruppo pulsanti per selezionare il periodo di analisi --}}
                    <div class="btn-group me-2">
                        <a href="{{ route('staff.statistiche', ['periodo' => 7]) }}" 
                           class="btn btn-outline-warning {{ $periodo == 7 ? 'active' : '' }}">
                            7 giorni
                        </a>
                        <a href="{{ route('staff.statistiche', ['periodo' => 30]) }}" 
                           class="btn btn-outline-warning {{ $periodo == 30 ? 'active' : '' }}">
                            30 giorni
                        </a>
                        <a href="{{ route('staff.statistiche', ['periodo' => 90]) }}" 
                           class="btn btn-outline-warning {{ $periodo == 90 ? 'active' : '' }}">
                            90 giorni
                        </a>
                    </div>
                    {{-- Pulsante per aggiornare le statistiche --}}
                    <button id="refresh-stats" class="btn btn-outline-success me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
                    </button>
                    {{-- Link per tornare alla dashboard --}}
                    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            {{-- Breadcrumb per la navigazione --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
                    <li class="breadcrumb-item active">Statistiche</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Alert di errore se presente --}}
    @if(isset($error))
        <div class="alert alert-warning border-start border-warning border-4 mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $error }}
        </div>
    @endif

    {{-- === STATISTICHE GENERALI - PRIMO ROW === --}}
    <div class="row g-3 mb-4">
        {{-- Card: Soluzioni Create --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        {{-- Icona della statistica --}}
                        <div class="flex-shrink-0">
                            <i class="bi bi-tools display-4 opacity-75"></i>
                        </div>
                        {{-- Valore e descrizione --}}
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['soluzioni_create'] ?? 0 }}</h3>
                            <small class="text-white-50">Soluzioni Create</small>
                        </div>
                    </div>
                </div>
                {{-- Footer con informazioni aggiuntive --}}
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-check-circle me-1"></i>
                        Totali da sempre
                    </small>
                </div>
            </div>
        </div>

        {{-- Card: Modifiche Apportate --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-pencil-square display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['soluzioni_modificate'] ?? 0 }}</h3>
                            <small class="text-white-50">Soluzioni Modificate</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Aggiornamenti effettuati
                    </small>
                </div>
            </div>
        </div>

        {{-- Card: Problemi Critici Risolti --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h3 class="mb-0 fw-bold">{{ $stats['critiche_risolte'] ?? 0 }}</h3>
                            <small class="text-white-50">Critiche Risolte</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-shield-check me-1"></i>
                        Problemi ad alta priorità
                    </small>
                </div>
            </div>
        </div>

        {{-- Card: Posizione Ranking --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #e83e8c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-trophy display-4 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            {{-- Controllo condizionale per la posizione nel ranking --}}
                            @if($stats['ranking_posizione'] ?? null)
                                <h3 class="mb-0 fw-bold">#{{ $stats['ranking_posizione'] }}</h3>
                                <small class="text-white-50">Posizione Ranking</small>
                            @else
                                <h3 class="mb-0 fw-bold">N/A</h3>
                                <small class="text-white-50">Nessun Ranking</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>
                        <i class="bi bi-people me-1"></i>
                        Su {{ $stats['totale_staff'] ?? 0 }} staff totali
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- === ATTIVITÀ NEL PERIODO SELEZIONATO === --}}
    <div class="row g-3 mb-4">
        {{-- Card: Attività Recente --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        Attività Recente ({{ $periodo }} giorni)
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Statistiche del periodo in formato griglia --}}
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <div class="display-6 text-success fw-bold">
                                    {{ $stats['soluzioni_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Nuove Soluzioni</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <div class="display-6 text-info fw-bold">
                                    {{ $stats['modifiche_periodo'] ?? 0 }}
                                </div>
                                <small class="text-muted">Modifiche Apportate</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra di progresso per obiettivi del periodo --}}
                    <hr>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Obiettivo periodo: {{ ceil($periodo / 7) }} soluzioni</small>
                            {{-- Calcolo della percentuale di completamento dell'obiettivo --}}
                            @php
                                $obiettivo = ceil($periodo / 7);
                                $raggiunte = $stats['soluzioni_periodo'] ?? 0;
                                $percentuale = $obiettivo > 0 ? min(100, ($raggiunte / $obiettivo) * 100) : 0;
                            @endphp
                            <small class="text-muted">{{ number_format($percentuale, 1) }}%</small>
                        </div>
                        {{-- Progress bar animata --}}
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $percentuale }}%" 
                                 role="progressbar" 
                                 aria-valuenow="{{ $percentuale }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Distribuzione per Gravità --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart text-warning me-2"></i>
                        Distribuzione per Gravità
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Griglia con i contatori per tipo di gravità --}}
                    <div class="row g-2">
                        {{-- Problemi critici --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-danger bg-opacity-10 rounded">
                                <div class="bg-danger rounded" style="width: 12px; height: 12px;"></div>
                                <div class="ms-2 flex-grow-1">
                                    <small class="text-muted">Critiche</small>
                                    <div class="fw-bold">{{ $stats['critiche_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Problemi alta priorità --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-warning bg-opacity-10 rounded">
                                <div class="bg-warning rounded" style="width: 12px; height: 12px;"></div>
                                <div class="ms-2 flex-grow-1">
                                    <small class="text-muted">Alte</small>
                                    <div class="fw-bold">{{ $stats['alte_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Problemi medi --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-info bg-opacity-10 rounded">
                                <div class="bg-info rounded" style="width: 12px; height: 12px;"></div>
                                <div class="ms-2 flex-grow-1">
                                    <small class="text-muted">Medie</small>
                                    <div class="fw-bold">{{ $stats['medie_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- Problemi bassa priorità --}}
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-success bg-opacity-10 rounded">
                                <div class="bg-success rounded" style="width: 12px; height: 12px;"></div>
                                <div class="ms-2 flex-grow-1">
                                    <small class="text-muted">Basse</small>
                                    <div class="fw-bold">{{ $stats['basse_risolte'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tempo medio di risposta se disponibile --}}
                    @if($stats['tempo_medio_risposta'] ?? null)
                        <hr>
                        <div class="text-center">
                            <div class="text-muted small mb-1">Tempo Medio Risposta</div>
                            <div class="h5 text-primary mb-0">
                                {{ $stats['tempo_medio_risposta'] }} ore
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === ANDAMENTO MENSILE === --}}
    @if($attivitaMensile && count($attivitaMensile) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            Andamento Mensile (ultimi 6 mesi)
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Grafico a barre semplice realizzato con CSS --}}
                        <div class="row g-2">
                            {{-- Ciclo per ogni mese nell'andamento --}}
                            @foreach($attivitaMensile as $mese)
                                <div class="col-2 text-center">
                                    <div class="mb-2">
                                        {{-- Calcolo delle altezze delle barre proporzionalmente --}}
                                        @php
                                            $maxValue = max(collect($attivitaMensile)->max('soluzioni_create'), 1);
                                            $altezzaCreate = $mese['soluzioni_create'] > 0 ? (($mese['soluzioni_create'] / $maxValue) * 100) : 0;
                                            $altezzaModificate = $mese['soluzioni_modificate'] > 0 ? (($mese['soluzioni_modificate'] / $maxValue) * 100) : 0;
                                        @endphp
                                        {{-- Container per le barre del grafico --}}
                                        <div style="height: 120px;" class="d-flex flex-column justify-content-end">
                                            {{-- Barra per soluzioni create --}}
                                            @if($mese['soluzioni_create'] > 0)
                                                <div class="bg-success rounded-top mb-1 chart-bar" 
                                                     style="height: {{ $altezzaCreate }}%; min-height: 4px;"
                                                     title="Soluzioni create: {{ $mese['soluzioni_create'] }}">
                                                </div>
                                            @endif
                                            {{-- Barra per soluzioni modificate --}}
                                            @if($mese['soluzioni_modificate'] > 0)
                                                <div class="bg-info rounded-bottom chart-bar" 
                                                     style="height: {{ $altezzaModificate }}%; min-height: 4px;"
                                                     title="Soluzioni modificate: {{ $mese['soluzioni_modificate'] }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Etichette del mese --}}
                                    <small class="text-muted">{{ $mese['mese'] }}</small>
                                    {{-- Badge con i valori numerici --}}
                                    <div class="small">
                                        <span class="badge bg-success">{{ $mese['soluzioni_create'] }}</span>
                                        <span class="badge bg-info">{{ $mese['soluzioni_modificate'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Legenda del grafico --}}
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-success rounded me-2" style="width: 12px; height: 12px;"></div>
                                    <small class="text-muted">Soluzioni Create</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-info rounded me-2" style="width: 12px; height: 12px;"></div>
                                    <small class="text-muted">Soluzioni Modificate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === SOLUZIONI PER CATEGORIA === --}}
    @if($soluzioniPerCategoria && $soluzioniPerCategoria->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-tags text-primary me-2"></i>
                            Soluzioni per Categoria Prodotto
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Griglia responsive per le categorie --}}
                        <div class="row g-3">
                            {{-- Mostra solo le prime 6 categorie --}}
                            @foreach($soluzioniPerCategoria->take(6) as $categoria)
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="h4 text-primary mb-1">{{ $categoria->count }}</div>
                                        <small class="text-muted">
                                            {{-- Formatta il nome della categoria --}}
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

    {{-- === DETTAGLI E ANALISI === --}}
    <div class="row g-4 mb-4">
        {{-- Prodotti più problematici --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-diamond text-danger me-2"></i>
                        Prodotti per cui hai creato più soluzioni
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Controlla se ci sono prodotti problematici --}}
                    @if($prodottiProblematici->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th class="text-center">Tue Soluzioni</th>
                                        <th class="text-end">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Mostra fino a 8 prodotti --}}
                                    @foreach($prodottiProblematici->take(8) as $prodotto)
                                        <tr>
                                            <td>
                                                <strong>{{ $prodotto->nome }}</strong>
                                                {{-- Mostra il modello se presente --}}
                                                @if($prodotto->modello)
                                                    <br><small class="text-muted">{{ $prodotto->modello }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($prodotto->categoria ?? 'generale') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">
                                                    {{ $prodotto->soluzioni_mie }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>Visualizza
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Messaggio quando non ci sono prodotti problematici --}}
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h5 class="text-success mt-2">Ottimo lavoro!</h5>
                            <p class="text-muted">Non ci sono prodotti con problemi evidenti al momento</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ultime soluzioni create --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Ultime Soluzioni
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Controlla se ci sono soluzioni recenti --}}
                    @if($ultimeSoluzioni->count() > 0)
                        <div class="list-group list-group-flush">
                            {{-- Mostra le ultime 6 soluzioni --}}
                            @foreach($ultimeSoluzioni->take(6) as $soluzione)
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-2">
                                            {{-- Titolo della soluzione --}}
                                            <h6 class="mb-1 fw-semibold text-truncate">
                                                {{ Str::limit($soluzione->titolo, 40) }}
                                            </h6>
                                            {{-- Nome del prodotto --}}
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-box me-1"></i>
                                                {{ Str::limit($soluzione->prodotto->nome, 25) }}
                                            </p>
                                            {{-- Data di creazione --}}
                                            <small class="text-muted">
                                                {{ $soluzione->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div>
                                            {{-- Badge colorato per la gravità --}}
                                            <span class="badge bg-{{ 
                                                $soluzione->gravita == 'critica' ? 'danger' : 
                                                ($soluzione->gravita == 'alta' ? 'warning' : 
                                                ($soluzione->gravita == 'media' ? 'info' : 'success')) 
                                            }}">
                                                {{ ucfirst($soluzione->gravita) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Link per vedere tutte le soluzioni --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" 
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-list me-1"></i>Vedi Tutte
                            </a>
                        </div>
                    @else
                        {{-- Messaggio quando non ci sono soluzioni --}}
                        <div class="text-center py-3">
                            <i class="bi bi-plus-circle display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0">Nessuna soluzione ancora</p>
                            <a href="{{ route('staff.create.nuova.soluzione') }}" 
                               class="btn btn-success btn-sm mt-2">
                                <i class="bi bi-plus me-1"></i>Crea Prima Soluzione
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

{{-- === SEZIONE STILI CSS PERSONALIZZATI === --}}
@push('styles')
<style>
/* === ANIMAZIONI E TRANSIZIONI === */

/* Animazioni per tutte le cards statistiche */
.card {
    transition: all 0.3s ease; /* Transizione fluida per hover */
    border-radius: 12px; /* Angoli arrotondati */
}

/* Effetto hover per le cards */
.card:hover {
    transform: translateY(-2px); /* Solleva leggermente la card */
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; /* Ombra più pronunciata */
}

/* === GRADIENTI ANIMATI === */

/* Gradienti animati per le cards con background speciale */
.bg-gradient {
    background-size: 200% 200%; /* Dimensione doppia per l'animazione */
    animation: gradient-shift 8s ease infinite; /* Animazione continua */
}

/* Keyframe per l'animazione del gradiente */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* === ELEMENTI DEL GRAFICO === */

/* Stile per le barre del grafico mensile */
.chart-bar {
    transition: all 0.3s ease; /* Transizione per interazioni */
}

/* Effetto hover per le barre del grafico */
.chart-bar:hover {
    opacity: 0.8; /* Leggera trasparenza */
    transform: scaleY(1.05); /* Ingrandimento verticale */
}

/* === RESPONSIVE DESIGN === */

/* Stili per dispositivi mobili (tablet e smartphone) */
@media (max-width: 768px) {
    /* Ridimensiona i pulsanti grandi su mobile */
    .btn-lg {
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }
    
    /* Ridimensiona le icone grandi su mobile */
    .display-6 {
        font-size: 1.5rem !important;
    }
    
    /* Riduce il padding delle card su mobile */
    .card-body {
        padding: 1rem;
    }
}

/* === ELEMENTI UI PERSONALIZZATI === */

/* Stile per i badges (etichette colorate) */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Barra di progresso personalizzata */
.progress {
    border-radius: 10px; /* Angoli arrotondati */
    overflow: hidden; /* Nasconde gli angoli della barra interna */
}

/* Animazione fluida per la barra di progresso */
.progress-bar {
    transition: width 0.6s ease;
}

/* === EFFETTI SPECIALI === */

/* Classe per elementi con ombra leggera */
.shadow-sm-hover {
    transition: box-shadow 0.3s ease;
}

.shadow-sm-hover:hover {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
}

/* Animazione di pulsazione per elementi importanti */
@keyframes pulse-soft {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.pulse-soft {
    animation: pulse-soft 2s ease-in-out infinite;
}

/* === TOOLTIP PERSONALIZZATI === */

/* Stile per i tooltip (elementi con title) */
[title] {
    cursor: help; /* Cambia il cursore per indicare informazioni aggiuntive */
}

/* === COLORI PERSONALIZZATI === */

/* Varianti di colore per i testi */
.text-gradient {
    background: linear-gradient(45deg, #007bff, #6610f2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush

{{-- === SEZIONE JAVASCRIPT FUNZIONALE === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('📊 Statistiche Staff inizializzate');

    // === FUNZIONALITÀ REFRESH STATISTICHE ===
    $('#refresh-stats').on('click', function(e) {
        e.preventDefault(); // Previene il comportamento predefinito del link
        
        const btn = $(this);
        const originalContent = btn.html(); // Salva il contenuto originale del pulsante
        
        // Mostra stato di caricamento
        btn.prop('disabled', true) // Disabilita il pulsante
           .html('<i class="bi bi-hourglass-split me-1"></i>Aggiornamento...'); // Cambia testo
        
        // Simula aggiornamento dati con ricarica pagina dopo 1 secondo
        setTimeout(() => {
            location.reload(); // Ricarica la pagina per aggiornare i dati
        }, 1000);
    });

    // === ANIMAZIONI CONTATORI NUMERICI ===
    function animateCounters() {
        // Seleziona tutti gli elementi con numeri da animare
        $('.display-4, .display-6, .h4').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            const target = parseInt(text.replace(/[^\d]/g, '')); // Estrae solo i numeri
            
            // Anima solo se è un numero valido e ragionevole
            if (!isNaN(target) && target > 0 && target < 1000) {
                $counter.text('0'); // Inizia da 0
                
                // Animazione jQuery per incrementare il numero
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1500, // Durata animazione: 1.5 secondi
                    easing: 'swing', // Tipo di easing per l'animazione
                    step: function() {
                        // Aggiorna il testo durante l'animazione
                        $counter.text(Math.ceil(this.counter));
                    },
                    complete: function() {
                        // Assicura che il valore finale sia corretto
                        $counter.text(target);
                    }
                });
            }
        });
    }

    // Avvia l'animazione dei contatori dopo un breve ritardo
    setTimeout(animateCounters, 500);

    // === TOOLTIP INTERATTIVI ===
    // Inizializza i tooltip di Bootstrap per elementi con attributo title
    $('[title]').tooltip({
        trigger: 'hover', // Mostra solo al passaggio del mouse
        placement: 'top', // Posizione sopra l'elemento
        delay: { show: 500, hide: 100 } // Ritardi per mostrare/nascondere
    });

    // === EFFETTI HOVER PER GRAFICI ===
    $('.chart-bar').hover(
        function() {
            // Al passaggio del mouse: aggiungi ombra
            $(this).addClass('shadow-sm');
        },
        function() {
            // Quando il mouse esce: rimuovi ombra
            $(this).removeClass('shadow-sm');
        }
    );

    // === SISTEMA DI NOTIFICHE ===
    
    // Mostra notifica di successo se presente in sessione
    @if(session('success'))
        showNotification('success', {!! json_encode(session('success')) !!});
    @endif

    // Mostra notifica di errore se presente in sessione
    @if(session('error'))
        showNotification('error', {!! json_encode(session('error')) !!});
    @endif

    /**
     * Funzione per mostrare notifiche dinamiche
     * @param {string} type - Tipo di notifica (success, error, info, warning)
     * @param {string} message - Messaggio da mostrare
     */
    function showNotification(type, message) {
        // Determina la classe CSS e l'icona in base al tipo
        const alertClass = type === 'error' ? 'danger' : type;
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        // Crea l'elemento HTML della notifica
        const notification = $(`
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" 
                 role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Aggiunge la notifica alla pagina
        $('body').append(notification);
        
        // Rimuove automaticamente la notifica dopo 5 secondi
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Rende la funzione disponibile globalmente per uso esterno
    window.showNotification = showNotification;

    // === GESTIONE ERRORI AJAX (se presente) ===
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('Errore AJAX:', {
            url: settings.url,
            status: xhr.status,
            error: thrownError
        });
        
        // Mostra notifica di errore per problemi di connessione
        showNotification('error', 'Errore di connessione. Riprova più tardi.');
    });

    // === OTTIMIZZAZIONI PERFORMANCE ===
    
    // Lazy loading per immagini se presenti
    $('img[data-src]').each(function() {
        const img = $(this);
        img.attr('src', img.data('src')).removeAttr('data-src');
    });

    // Preload delle pagine più comuni per migliorare la navigazione
    const importantLinks = [
        "{{ route('staff.dashboard') }}",
        "{{ route('malfunzionamenti.ricerca') }}",
        "{{ route('prodotti.completo.index') }}"
    ];

    // Precarica i link importanti dopo il caricamento della pagina
    setTimeout(() => {
        importantLinks.forEach(url => {
            $('<link>', {
                rel: 'prefetch',
                href: url
            }).appendTo('head');
        });
    }, 2000);

    // === LOG FINALE E CLEANUP ===
    console.log('✅ Statistiche Staff completamente caricate e funzionali');
    
    // Event listener per cleanup quando si esce dalla pagina
    $(window).on('beforeunload', function() {
        console.log('🔄 Cleanup statistiche staff...');
        // Qui si possono aggiungere operazioni di pulizia se necessarie
    });
});

// === FUNZIONI GLOBALI UTILITY ===

/**
 * Formatta numeri per visualizzazione (es: 1000 -> 1K)
 * @param {number} num - Numero da formattare
 * @return {string} - Numero formattato
 */
function formatNumber(num) {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

/**
 * Calcola la percentuale tra due numeri
 * @param {number} value - Valore corrente
 * @param {number} total - Valore totale
 * @return {number} - Percentuale calcolata
 */
function calculatePercentage(value, total) {
    if (total === 0) return 0;
    return Math.round((value / total) * 100);
}

/**
 * Aggiorna dinamicamente una statistica specifica
 * @param {string} selector - Selettore CSS dell'elemento
 * @param {number} newValue - Nuovo valore da mostrare
 */
function updateStatistic(selector, newValue) {
    const element = $(selector);
    if (element.length) {
        const currentValue = parseInt(element.text()) || 0;
        
        // Anima la transizione al nuovo valore
        $({ value: currentValue }).animate({ value: newValue }, {
            duration: 800,
            step: function() {
                element.text(Math.ceil(this.value));
            }
        });
    }
}
</script>
@endpush