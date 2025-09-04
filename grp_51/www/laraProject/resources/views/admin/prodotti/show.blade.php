{{-- 
    Vista dettaglio prodotto per amministratori - VERSIONE SEMPLIFICATA
    Percorso: resources/views/admin/prodotti/show.blade.php
    Accesso: Solo livello 4 (Amministratori)
    
    MODIFICHE APPLICATE:
    - Rimossi pulsanti "Riassegna Staff" e "Rimuovi Assegnazione"
    - Rimosse tutte le modal per gestione staff
    - Mantenute tutte le altre funzionalità
--}}

@extends('layouts.app')

@section('title', 'Dettaglio Prodotto - ' . $prodotto->nome)

@section('content')
<div class="container-fluid">
    
    {{-- === HEADER  === --}}
    <div class="row mb-4">
        <div class="col-12">
            

            {{-- Header principale con titolo e azioni --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-box text-primary me-2"></i>
                        {{ $prodotto->nome }}
                        {{-- Badge stato prodotto --}}
                        @if(!$prodotto->attivo)
                            <span class="badge bg-danger ms-2">INATTIVO</span>
                        @else
                            <span class="badge bg-success ms-2">ATTIVO</span>
                        @endif
                    </h1>
                    {{-- Informazioni base --}}
                    <p class="text-muted mb-0">
                        <strong>Modello:</strong> {{ $prodotto->modello }} • 
                        <strong>Categoria:</strong> {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        @if($prodotto->prezzo)
                            • <strong>Prezzo:</strong> € {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                        @endif
                    </p>
                </div>

                {{-- Pulsanti di azione principale --}}
                <div class="btn-group flex-wrap" role="group" aria-label="Azioni prodotto">
                    {{-- Modifica prodotto --}}
                    <a href="{{ route('admin.prodotti.edit', $prodotto) }}" 
                       class="btn btn-warning"
                       title="Modifica informazioni prodotto">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    
                    {{-- Toggle stato attivo/inattivo --}}
                    @if(Route::has('admin.prodotti.toggle-status'))
                    <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirmToggleStatus({{ $prodotto->attivo ? 'true' : 'false' }})">
                        @csrf
                        <button type="submit" 
                                class="btn {{ $prodotto->attivo ? 'btn-danger' : 'btn-success' }}"
                                title="{{ $prodotto->attivo ? 'Disattiva prodotto' : 'Attiva prodotto' }}">
                            <i class="bi bi-{{ $prodotto->attivo ? 'pause' : 'play' }} me-1"></i>
                            {{ $prodotto->attivo ? 'Disattiva' : 'Attiva' }}
                        </button>
                    </form>
                    @endif

                
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE (se disponibili) === --}}
    @if(isset($statistiche))
    <div class="row mb-4">
        {{-- Malfunzionamenti totali --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $statistiche['malfunzionamenti_totali'] }}</h4>
                            <p class="card-text mb-0">Malfunzionamenti Totali</p>
                        </div>
                        <div>
                            <i class="bi bi-exclamation-triangle display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Problemi critici --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $statistiche['malfunzionamenti_critici'] }}</h4>
                            <p class="card-text mb-0">Problemi Critici</p>
                        </div>
                        <div>
                            <i class="bi bi-exclamation-octagon display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Segnalazioni totali --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $statistiche['segnalazioni_totali'] }}</h4>
                            <p class="card-text mb-0">Segnalazioni Totali</p>
                        </div>
                        <div>
                            <i class="bi bi-flag display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Livello criticità --}}
        <div class="col-md-3 mb-3">
            @php
                $criticita = $metriche['livello_criticita'] ?? ['livello' => 'N/A', 'colore' => 'secondary'];
            @endphp
            <div class="card bg-{{ $criticita['colore'] }} text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ ucfirst($criticita['livello']) }}</h4>
                            <p class="card-text mb-0">Livello Criticità</p>
                        </div>
                        <div>
                            <i class="bi bi-speedometer2 display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === CONTENUTO PRINCIPALE === --}}
    <div class="row">
        
        {{-- === COLONNA PRINCIPALE - INFORMAZIONI PRODOTTO === --}}
        <div class="col-lg-8">
            
            {{-- Card informazioni generali --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Informazioni Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Foto prodotto (se presente) --}}
                        @if($prodotto->foto)
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                     alt="Foto {{ $prodotto->nome }}"
                                     class="img-fluid rounded shadow-sm product-image"
                                     style="max-height: 250px; object-fit: cover;"
                                     onerror="handleImageError(this)"
                                     loading="lazy">
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-camera me-1"></i>Immagine prodotto
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Tabella dettagli prodotto --}}
                        <div class="col-md-{{ $prodotto->foto ? '8' : '12' }}">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th width="30%" class="text-muted">Nome:</th>
                                        <td class="fw-semibold">{{ $prodotto->nome }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Modello:</th>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $prodotto->modello }}</code>
                                            <button type="button" 
                                                    class="btn btn-link btn-sm p-0 ms-2" 
                                                    onclick="copyToClipboard('{{ $prodotto->modello }}')"
                                                    title="Copia modello">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Categoria:</th>
                                        <td>
                                            <span class="badge bg-secondary fs-6">
                                                {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($prodotto->prezzo)
                                    <tr>
                                        <th class="text-muted">Prezzo:</th>
                                        <td class="fw-bold text-success fs-5">
                                            € {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th class="text-muted">Stato:</th>
                                        <td>
                                            <span class="badge bg-{{ $prodotto->attivo ? 'success' : 'danger' }} fs-6">
                                                <i class="bi bi-{{ $prodotto->attivo ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ $prodotto->attivo ? 'ATTIVO' : 'INATTIVO' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Creato:</th>
                                        <td>
                                            {{ $prodotto->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">
                                                ({{ $prodotto->created_at->diffForHumans() }})
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Aggiornato:</th>
                                        <td>
                                            {{ $prodotto->updated_at->format('d/m/Y H:i') }}
                                            @if($prodotto->updated_at != $prodotto->created_at)
                                                <small class="text-muted">
                                                    ({{ $prodotto->updated_at->diffForHumans() }})
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Descrizione prodotto --}}
                    @if($prodotto->descrizione)
                    <div class="mt-4">
                        <h6 class="text-primary">
                            <i class="bi bi-text-left me-1"></i>Descrizione:
                        </h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $prodotto->descrizione }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Note tecniche --}}
                    @if($prodotto->note_tecniche)
                    <div class="mt-4">
                        <h6 class="text-warning">
                            <i class="bi bi-gear me-1"></i>Note Tecniche:
                        </h6>
                        <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 p-3 rounded">
                            {!! nl2br(e($prodotto->note_tecniche)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Modalità di installazione --}}
                    @if($prodotto->modalita_installazione)
                    <div class="mt-4">
                        <h6 class="text-info">
                            <i class="bi bi-tools me-1"></i>Modalità di Installazione:
                        </h6>
                        <div class="bg-info bg-opacity-10 border border-info border-opacity-25 p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_installazione)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Modalità d'uso --}}
                    @if($prodotto->modalita_uso)
                    <div class="mt-4">
                        <h6 class="text-success">
                            <i class="bi bi-book me-1"></i>Modalità d'Uso:
                        </h6>
                        <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_uso)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- === SEZIONE MALFUNZIONAMENTI === --}}
            @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bug text-warning me-2"></i>Malfunzionamenti e Soluzioni
                    </h5>
                    <span class="badge bg-warning">
                        {{ $prodotto->malfunzionamenti->count() }} 
                        {{ $prodotto->malfunzionamenti->count() === 1 ? 'problema' : 'problemi' }}
                    </span>
                </div>
                <div class="card-body">
                    {{-- Loop malfunzionamenti --}}
                    @foreach($prodotto->malfunzionamenti as $index => $malfunzionamento)
                    <div class="border rounded p-3 mb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        {{-- Header malfunzionamento --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    {{-- Badge gravità con colori appropriati --}}
                                    @php
                                        $gravetaColors = [
                                            'critica' => 'danger',
                                            'alta' => 'warning', 
                                            'media' => 'info',
                                            'bassa' => 'secondary'
                                        ];
                                        $color = $gravetaColors[$malfunzionamento->gravita] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }} me-2">
                                        <i class="bi bi-{{ $malfunzionamento->gravita === 'critica' ? 'exclamation-triangle' : 'info-circle' }} me-1"></i>
                                        {{ ucfirst($malfunzionamento->gravita) }}
                                    </span>
                                    <span class="text-dark">{{ $malfunzionamento->descrizione }}</span>
                                </h6>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">
                                    <i class="bi bi-flag me-1"></i>
                                    {{ $malfunzionamento->numero_segnalazioni }} 
                                    {{ $malfunzionamento->numero_segnalazioni === 1 ? 'segnalazione' : 'segnalazioni' }}
                                </small>
                                <small class="text-muted">
                                    #{{ $index + 1 }}
                                </small>
                            </div>
                        </div>
                        
                        {{-- Soluzione tecnica --}}
                        @if($malfunzionamento->soluzione_tecnica)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-tools text-success me-2"></i>
                                <strong class="text-success">Soluzione Tecnica:</strong>
                            </div>
                            <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-3 rounded">
                                {{ $malfunzionamento->soluzione_tecnica }}
                            </div>
                        </div>
                        @endif

                        {{-- Informazioni aggiuntive --}}
                        <div class="row g-3 text-sm">
                            @if($malfunzionamento->tempo_stimato)
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <strong>Tempo stimato:</strong> {{ $malfunzionamento->tempo_stimato }} minuti
                                </small>
                            </div>
                            @endif
                            
                            @if($malfunzionamento->difficolta)
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-bar-chart me-1"></i>
                                    <strong>Difficoltà:</strong> {{ ucfirst($malfunzionamento->difficolta) }}
                                </small>
                            </div>
                            @endif
                        </div>

                        {{-- Metadati creazione/modifica --}}
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <strong>Creato:</strong> {{ $malfunzionamento->created_at->format('d/m/Y H:i') }}
                                @if($malfunzionamento->creatoBy)
                                    da <strong>{{ $malfunzionamento->creatoBy->nome_completo }}</strong>
                                @endif
                                
                                @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                                    <br>
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    <strong>Aggiornato:</strong> {{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}
                                    @if($malfunzionamento->modificatoBy)
                                        da <strong>{{ $malfunzionamento->modificatoBy->nome_completo }}</strong>
                                    @endif
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            {{-- Nessun malfunzionamento --}}
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                    <h5 class="text-success">Nessun malfunzionamento segnalato</h5>
                    <p class="text-muted">Questo prodotto non ha problemi noti al momento.</p>
                </div>
            </div>
            @endif
        </div>

        {{-- === SIDEBAR DESTRA === --}}
        <div class="col-lg-4">
            
            {{-- === CARD STAFF ASSEGNATO (SOLO VISUALIZZAZIONE) === --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>Staff Assegnato
                    </h6>
                </div>
                <div class="card-body">
                    @if($prodotto->staffAssegnato)
                        {{-- Staff assegnato presente - SOLO VISUALIZZAZIONE --}}
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $prodotto->staffAssegnato->nome_completo }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-briefcase me-1"></i>Staff Aziendale (Livello 3)
                                </small>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Assegnato il: {{ $prodotto->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Nessuno staff assegnato --}}
                        <div class="text-center py-4">
                            <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Nessuno staff assegnato</h6>
                            <p class="text-muted small mb-0">
                                Questo prodotto non ha un responsabile tecnico assegnato.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- === CARD METRICHE PERFORMANCE === --}}
            @if(isset($metriche))
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up text-info me-2"></i>Metriche Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-date me-1"></i>Giorni dal lancio:
                                </small>
                                <span class="badge bg-info">{{ $metriche['giorni_dal_lancio'] }}</span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-bar-chart me-1"></i>Media segnalazioni:
                                </small>
                                <span class="badge bg-warning">{{ $metriche['media_segnalazioni_per_malfunzionamento'] }}</span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-speedometer me-1"></i>Frequenza problemi:
                                </small>
                                @php
                                    $frequenza = $metriche['frequenza_problemi'];
                                    $colorFreq = match($frequenza) {
                                        'Molto Alta' => 'danger',
                                        'Alta' => 'warning', 
                                        'Media' => 'info',
                                        'Bassa' => 'success',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $colorFreq }}">{{ $frequenza }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- === CARD PRODOTTI CORRELATI === --}}
            @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-link-45deg text-secondary me-2"></i>Prodotti Correlati
                        <span class="badge bg-secondary ms-2">{{ $prodottiCorrelati->count() }}</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    @foreach($prodottiCorrelati as $correlato)
                    <div class="d-flex align-items-center p-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        {{-- Miniatura prodotto --}}
                        <div class="me-3">
                            @if($correlato->foto)
                                <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                     alt="{{ $correlato->nome }}"
                                     class="rounded border"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     onerror="this.src='{{ asset('images/placeholder-product.png') }}'; this.onerror=null;">
                            @else
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Info prodotto correlato --}}
                        <div class="flex-grow-1">
                            <a href="{{ route('admin.prodotti.show', $correlato) }}" 
                               class="text-decoration-none">
                                <div class="fw-semibold text-dark small">{{ $correlato->nome }}</div>
                            </a>
                            <div class="text-muted small">
                                <i class="bi bi-bug me-1"></i>
                                {{ $correlato->malfunzionamenti_count ?? 0 }} 
                                {{ ($correlato->malfunzionamenti_count ?? 0) === 1 ? 'problema' : 'problemi' }}
                            </div>
                        </div>
                        
                        {{-- Link rapido --}}
                        <div>
                            <a href="{{ route('admin.prodotti.show', $correlato) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

{{-- ========================================= --}}
{{-- === STILI CSS PERSONALIZZATI === --}}
{{-- ========================================= --}}

@push('styles')
<style>
/* === STILI GENERALI CARD === */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.2s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    font-weight: 600;
}

/* === BADGE PERSONALIZZATI === */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* === TABELLE SENZA BORDI === */
.table-borderless th {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
}

.table-borderless td {
    font-weight: 500;
    color: #212529;
}

/* === IMMAGINI PRODOTTO === */
.product-image {
    transition: transform 0.2s ease-in-out;
    cursor: pointer;
}

.product-image:hover {
    transform: scale(1.05);
}

/* === BOTTONI GRUPPI === */
.btn-group .btn {
    margin: 0.125rem;
}

/* === ANIMAZIONI CARICAMENTO === */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        width: 100%;
    }
    
    .card-body .row .col-md-8,
    .card-body .row .col-md-4 {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .display-6 {
        font-size: 2rem;
    }
    
    .h2 {
        font-size: 1.5rem;
    }
}

/* === STATI INTERATTIVI === */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-select:focus,
.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* === UTILITÀ CUSTOM === */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.border-opacity-25 {
    --bs-border-opacity: 0.25;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

/* === NOTIFICHE TOAST === */
.toast-container {
    z-index: 9999;
}

.toast {
    min-width: 300px;
}

/* === ACCESSIBILITÀ === */
@media (prefers-reduced-motion: reduce) {
    .card,
    .product-image,
    .btn {
        transition: none;
    }
    
    .loading::after {
        animation: none;
    }
}

/* === MODALITÀ SCURA (se implementata) === */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .card-header {
        background-color: #4a5568;
        border-color: #2d3748;
    }
    
    .table-borderless th {
        color: #a0aec0;
    }
    
    .text-muted {
        color: #718096 !important;
    }
}
</style>
@endpush

{{-- ========================================= --}}
{{-- === JAVASCRIPT FUNZIONALITÀ === --}}
{{-- ========================================= --}}

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

{{-- ========================================= --}}
{{-- === DEBUG INFO (solo sviluppo) === --}}
{{-- ========================================= --}}

@if(config('app.debug') && request()->get('debug'))
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning bg-opacity-25">
                    <h6 class="mb-0">
                        <i class="bi bi-bug text-warning me-2"></i>
                        Debug Information - Versione Semplificata
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informazioni Prodotto:</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th>ID:</th><td>{{ $prodotto->id }}</td></tr>
                                <tr><th>Nome:</th><td>{{ $prodotto->nome }}</td></tr>
                                <tr><th>Modello:</th><td>{{ $prodotto->modello }}</td></tr>
                                <tr><th>Attivo:</th><td>{{ $prodotto->attivo ? 'Sì' : 'No' }}</td></tr>
                                <tr><th>Staff ID:</th><td>{{ $prodotto->staff_assegnato_id ?? 'NULL' }}</td></tr>
                                <tr><th>Staff Nome:</th><td>{{ $prodotto->staffAssegnato->nome_completo ?? 'N/A' }}</td></tr>
                                <tr><th>Malfunzionamenti:</th><td>{{ $prodotto->malfunzionamenti ? $prodotto->malfunzionamenti->count() : 0 }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Funzionalità Attive:</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-check-circle text-success"></i> Visualizzazione prodotto</li>
                                <li><i class="bi bi-check-circle text-success"></i> Toggle stato attivo/inattivo</li>
                                <li><i class="bi bi-check-circle text-success"></i> Vista pubblica</li>
                                <li><i class="bi bi-check-circle text-success"></i> Modifica prodotto</li>
                                <li><i class="bi bi-x-circle text-danger"></i> Riassegnazione staff (rimossa)</li>
                                <li><i class="bi bi-x-circle text-danger"></i> Rimozione assegnazione (rimossa)</li>
                            </ul>
                            
                            <h6 class="mt-3">Route Disponibili:</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.show') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.show</li>
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.edit') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.edit</li>
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.toggle-status') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.toggle-status</li>
                                <li><i class="bi bi-{{ Route::has('prodotti.show') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> prodotti.show</li>
                            </ul>
                        </div>
                    </div>
                    
                    @if(isset($statistiche))
                    <h6 class="mt-3">Statistiche Debug:</h6>
                    <pre class="bg-light p-3 rounded small">{{ json_encode($statistiche, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    
                    @if(isset($metriche))
                    <h6 class="mt-3">Metriche Debug:</h6>
                    <pre class="bg-light p-3 rounded small">{{ json_encode($metriche, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif