{{-- 
    Vista dettaglio prodotto per amministratori - VERSIONE CORRETTA E COMPLETA
    Percorso: resources/views/admin/prodotti/show.blade.php
    Accesso: Solo livello 4 (Amministratori)
    
    CORREZIONI APPLICATE:
    - Modal riassegnazione staff con route corrette
    - Form validation e error handling
    - Gestione immagini con fallback
    - JavaScript per UX migliorata
--}}

@extends('layouts.app')

@section('title', 'Dettaglio Prodotto - ' . $prodotto->nome)

@section('content')
<div class="container-fluid">
    
    {{-- === HEADER CON BREADCRUMB === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Breadcrumb di navigazione --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.prodotti.index') }}" class="text-decoration-none">
                            <i class="bi bi-box me-1"></i>Gestione Prodotti
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $prodotto->nome }}</li>
                </ol>
            </nav>

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

                    {{-- Vista pubblica del prodotto --}}
                    <a href="{{ route('prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-primary" 
                       target="_blank"
                       title="Visualizza come lo vedono gli utenti pubblici">
                        <i class="bi bi-eye me-1"></i>Vista Pubblica
                    </a>
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
            
            {{-- === CARD STAFF ASSEGNATO === --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>Staff Assegnato
                    </h6>
                </div>
                <div class="card-body">
                    @if($prodotto->staffAssegnato)
                        {{-- Staff assegnato presente --}}
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $prodotto->staffAssegnato->nome_completo }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-briefcase me-1"></i>Staff Aziendale (Livello 3)
                                </small>
                            </div>
                        </div>
                        
                        {{-- Pulsanti azione per staff assegnato --}}
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#changeStaffModal">
                                <i class="bi bi-arrow-repeat me-1"></i>Riassegna Staff
                            </button>
                            
                            <form action="{{ route('admin.prodotti.update', $prodotto) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Rimuovere l\'assegnazione corrente?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="staff_assegnato_id" value="">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-person-x me-1"></i>Rimuovi Assegnazione
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- Nessuno staff assegnato --}}
                        <div class="text-center py-4">
                            <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Nessuno staff assegnato</h6>
                            <p class="text-muted small mb-3">
                                Questo prodotto non ha un responsabile tecnico assegnato.
                            </p>
                            
                            <button class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#assignStaffModal">
                                <i class="bi bi-person-plus me-1"></i>Assegna Staff
                            </button>
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

{{-- ========================================= --}}
{{-- === MODALS PER GESTIONE STAFF === --}}
{{-- ========================================= --}}

{{-- Modal per assegnazione nuovo staff --}}
<div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStaffModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i>Assegna Staff al Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Form assegnazione - ROUTE CORRETTA --}}
            <form action="{{ route('admin.prodotti.update', $prodotto) }}" method="POST" id="assignStaffForm">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    {{-- Info prodotto --}}
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Prodotto:</strong> {{ $prodotto->nome }}<br>
                                <small class="text-muted">Modello: {{ $prodotto->modello }}</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Selezione staff --}}
                    <div class="mb-3">
                        <label for="staff_assegnato_id" class="form-label">
                            <i class="bi bi-person me-1"></i>Seleziona Staff da Assegnare:
                        </label>
                        <select name="staff_assegnato_id" 
                                id="staff_assegnato_id" 
                                class="form-select @error('staff_assegnato_id') is-invalid @enderror" 
                                required>
                            <option value="">-- Seleziona uno staff --</option>
                            @php
                                // Recupera lista staff disponibili (livello 3)
                                $staffDisponibili = $staffDisponibili ?? \App\Models\User::where('livello_accesso', '3')
                                    ->select('id', 'nome', 'cognome')
                                    ->orderBy('nome')
                                    ->orderBy('cognome')
                                    ->get();
                            @endphp
                            @forelse($staffDisponibili as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }}
                                    @php
                                        $prodottiAssegnati = \App\Models\Prodotto::where('staff_assegnato_id', $staff->id)->count();
                                    @endphp
                                    ({{ $prodottiAssegnati }} {{ $prodottiAssegnati === 1 ? 'prodotto' : 'prodotti' }} assegnati)
                                </option>
                            @empty
                                <option value="" disabled>Nessuno staff disponibile</option>
                            @endforelse
                        </select>
                        @error('staff_assegnato_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-lightbulb me-1"></i>
                            Lo staff assegnato potrà gestire i malfunzionamenti di questo prodotto.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Annulla
                    </button>
                    <button type="submit" class="btn btn-primary" id="confirmAssignBtn">
                        <i class="bi bi-check me-1"></i>Assegna Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal per riassegnazione staff esistente --}}
<div class="modal fade" id="changeStaffModal" tabindex="-1" aria-labelledby="changeStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStaffModalLabel">
                    <i class="bi bi-arrow-repeat text-warning me-2"></i>Riassegna Staff al Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Form riassegnazione - ROUTE CORRETTA --}}
            <form action="{{ route('admin.prodotti.update', $prodotto) }}" method="POST" id="changeStaffForm">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    {{-- Info prodotto e staff attuale --}}
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                            <div>
                                <strong>Prodotto:</strong> {{ $prodotto->nome }} - {{ $prodotto->modello }}<br>
                                <strong>Staff attualmente assegnato:</strong> 
                                <span class="text-primary">
                                    {{ $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : 'Nessuno' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Selezione nuovo staff --}}
                    <div class="mb-3">
                        <label for="new_staff_assegnato_id" class="form-label">
                            <i class="bi bi-person-gear me-1"></i>Nuovo Staff da Assegnare:
                        </label>
                        <select name="staff_assegnato_id" 
                                id="new_staff_assegnato_id" 
                                class="form-select @error('staff_assegnato_id') is-invalid @enderror">
                            <option value="">-- Rimuovi assegnazione (nessuno staff) --</option>
                            @php
                                $staffDisponibili = $staffDisponibili ?? \App\Models\User::where('livello_accesso', '3')
                                    ->select('id', 'nome', 'cognome')
                                    ->orderBy('nome')
                                    ->orderBy('cognome')
                                    ->get();
                            @endphp
                            @foreach($staffDisponibili as $staff)
                                <option value="{{ $staff->id }}"
                                        {{ $prodotto->staff_assegnato_id == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->nome_completo }}
                                    @if($prodotto->staff_assegnato_id == $staff->id)
                                        (attualmente assegnato)
                                    @else
                                        @php
                                            $prodottiAssegnati = \App\Models\Prodotto::where('staff_assegnato_id', $staff->id)->count();
                                        @endphp
                                        ({{ $prodottiAssegnati }} {{ $prodottiAssegnati === 1 ? 'prodotto' : 'prodotti' }} assegnati)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('staff_assegnato_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Lascia vuoto per rimuovere completamente l'assegnazione del prodotto.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Annulla
                    </button>
                    <button type="submit" class="btn btn-warning" id="confirmChangeBtn">
                        <i class="bi bi-arrow-repeat me-1"></i>Riassegna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal di conferma eliminazione (se necessario) --}}
@if(false) {{-- Attivare se serve funzionalità di eliminazione --}}
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteProductModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Questa azione è irreversibile.
                </div>
                <p>Sei sicuro di voler eliminare definitivamente il prodotto <strong>{{ $prodotto->nome }}</strong>?</p>
                <ul class="text-muted small">
                    <li>Il prodotto verrà rimosso dal catalogo</li>
                    <li>Tutti i malfunzionamenti associati verranno mantenuti per storico</li>
                    <li>Le assegnazioni staff verranno rimosse</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

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

/* === DEBUG PANEL (solo sviluppo) === */
.debug-panel {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 11px;
    z-index: 9998;
    max-width: 300px;
    line-height: 1.3;
}

.debug-panel .debug-title {
    font-weight: bold;
    margin-bottom: 5px;
    color: #ffc107;
}

.debug-panel .debug-item {
    margin-bottom: 2px;
}

.debug-panel .debug-status-ok {
    color: #28a745;
}

.debug-panel .debug-status-error {
    color: #dc3545;
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
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== CONFIGURAZIONE GLOBALE =====
    const config = {
        prodotto: {
            id: {{ $prodotto->id }},
            nome: @json($prodotto->nome),
            attivo: {{ $prodotto->attivo ? 'true' : 'false' }},
            staffAssegnato: @json($prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : null)
        },
        routes: {
            update: @json(route('admin.prodotti.update', $prodotto)),
            toggleStatus: @json(Route::has('admin.prodotti.toggle-status') ? route('admin.prodotti.toggle-status', $prodotto) : ''),
            show: @json(route('admin.prodotti.show', $prodotto))
        },
        debug: {{ config('app.debug') ? 'true' : 'false' }}
    };
    
    // ===== LOGGING E DEBUG =====
    function log(message, type = 'info', data = null) {
        if (config.debug) {
            const timestamp = new Date().toLocaleTimeString();
            const prefix = `[${timestamp}] TechSupport Admin:`;
            
            switch(type) {
                case 'error':
                    console.error(prefix, message, data);
                    break;
                case 'warn':
                    console.warn(prefix, message, data);
                    break;
                case 'success':
                    console.log(`%c${prefix}`, 'color: green; font-weight: bold;', message, data);
                    break;
                default:
                    console.log(prefix, message, data);
            }
        }
    }
    
    log('Inizializzazione pagina dettaglio prodotto', 'info', config.prodotto);
    
    // ===== GESTIONE FORM ASSEGNAZIONE =====
    const assignForm = document.getElementById('assignStaffForm');
    const changeForm = document.getElementById('changeStaffForm');
    
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            const formData = new FormData(this);
            const staffId = formData.get('staff_assegnato_id');
            
            log('Invio form assegnazione staff', 'info', {
                staffId: staffId,
                prodottoId: config.prodotto.id
            });
            
            if (!staffId) {
                e.preventDefault();
                showNotification('error', 'Seleziona uno staff da assegnare al prodotto.');
                return false;
            }
            
            // Disabilita pulsante e mostra loading
            const submitBtn = document.getElementById('confirmAssignBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Assegnando...';
            }
        });
    }
    
    if (changeForm) {
        changeForm.addEventListener('submit', function(e) {
            const formData = new FormData(this);
            const newStaffId = formData.get('staff_assegnato_id');
            
            log('Invio form riassegnazione staff', 'info', {
                newStaffId: newStaffId,
                currentStaff: config.prodotto.staffAssegnato,
                prodottoId: config.prodotto.id
            });
            
            // Conferma se si sta rimuovendo l'assegnazione
            if (!newStaffId && config.prodotto.staffAssegnato) {
                if (!confirm(`Vuoi davvero rimuovere l'assegnazione di ${config.prodotto.staffAssegnato} da questo prodotto?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Disabilita pulsante e mostra loading
            const submitBtn = document.getElementById('confirmChangeBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Riassegnando...';
            }
        });
    }
    
    // ===== GESTIONE TOGGLE STATUS =====
    const toggleForms = document.querySelectorAll('form[action*="toggle-status"]');
    
    toggleForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const isActive = config.prodotto.attivo;
            const action = isActive ? 'disattivare' : 'attivare';
            
            if (!confirmToggleStatus(isActive)) {
                e.preventDefault();
                return false;
            }
            
            log(`Toggle status prodotto: ${action}`, 'info', {
                prodottoId: config.prodotto.id,
                currentStatus: isActive
            });
            
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Attendere...';
            }
        });
    });
    
    // ===== GESTIONE MODALS =====
    const assignModal = document.getElementById('assignStaffModal');
    const changeModal = document.getElementById('changeStaffModal');
    
    if (assignModal) {
        assignModal.addEventListener('shown.bs.modal', function () {
            const staffSelect = document.getElementById('staff_assegnato_id');
            if (staffSelect) {
                staffSelect.focus();
                log('Modal assegnazione staff aperto');
            }
        });
        
        assignModal.addEventListener('hidden.bs.modal', function () {
            resetFormState('assignStaffForm');
        });
    }
    
    if (changeModal) {
        changeModal.addEventListener('shown.bs.modal', function () {
            const staffSelect = document.getElementById('new_staff_assegnato_id');
            if (staffSelect) {
                staffSelect.focus();
                log('Modal riassegnazione staff aperto');
            }
        });
        
        changeModal.addEventListener('hidden.bs.modal', function () {
            resetFormState('changeStaffForm');
        });
    }
    
    // ===== GESTIONE IMMAGINI =====
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this);
        });
        
        img.addEventListener('click', function() {
            // Modalità fullscreen per immagine (opzionale)
            if (this.requestFullscreen) {
                this.requestFullscreen();
            }
        });
    });
    
    // ===== FUNZIONI UTILITY =====
    
    function resetFormState(formId) {
        const form = document.getElementById(formId);
        if (form) {
            // Reset pulsanti
            const submitBtns = form.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                btn.disabled = false;
                if (btn.id === 'confirmAssignBtn') {
                    btn.innerHTML = '<i class="bi bi-check me-1"></i>Assegna Staff';
                } else if (btn.id === 'confirmChangeBtn') {
                    btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Riassegna';
                }
            });
            
            // Reset form
            form.reset();
            
            // Rimuovi classi di errore
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }
    }
    
    // ===== NOTIFICAZIONI =====
    @if(session('success'))
        showNotification('success', @json(session('success')));
        log('Notifica success mostrata', 'success', @json(session('success')));
    @endif
    
    @if(session('error'))
        showNotification('error', @json(session('error')));
        log('Notifica error mostrata', 'error', @json(session('error')));
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showNotification('error', @json($error));
            log('Errore validazione', 'error', @json($error));
        @endforeach
    @endif
});

// ===== FUNZIONI GLOBALI =====

/**
 * Conferma toggle status prodotto
 */
function confirmToggleStatus(isActive) {
    const action = isActive ? 'disattivare' : 'attivare';
    const message = `Sei sicuro di voler ${action} questo prodotto?`;
    
    if (isActive) {
        return confirm(`${message}\n\nSe disattivato, il prodotto non sarà più visibile nel catalogo pubblico.`);
    } else {
        return confirm(`${message}\n\nSe attivato, il prodotto tornerà visibile nel catalogo pubblico.`);
    }
}

/**
 * Gestione errori immagini
 */
function handleImageError(img) {
    const placeholderUrl = @json(asset('images/placeholder-product.png'));
    
    if (img.src !== placeholderUrl) {
        console.warn('🖼️ Errore caricamento immagine:', img.src);
        img.src = placeholderUrl;
        img.onerror = null; // Previeni loop infinito
        
        // Aggiungi classe per styling
        img.classList.add('image-error');
    }
}

/**
 * Copia testo negli appunti
 */
function copyToClipboard(text, successMessage = 'Testo copiato negli appunti!') {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('success', successMessage);
        }).catch(err => {
            console.error('Errore copia clipboard:', err);
            fallbackCopyTextToClipboard(text, successMessage);
        });
    } else {
        fallbackCopyTextToClipboard(text, successMessage);
    }
}

function fallbackCopyTextToClipboard(text, successMessage) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('success', successMessage);
        } else {
            throw new Error('execCommand fallito');
        }
    } catch (err) {
        console.error('Fallback copy fallito:', err);
        showNotification('error', 'Impossibile copiare negli appunti');
    }
    
    document.body.removeChild(textArea);
}

/**
 * Sistema di notificazioni toast
 */
function showNotification(type, message, duration = 5000) {
    // Controlla se Bootstrap Toast è disponibile
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Container per toast
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });
        
        toast.show();
        
        // Rimuovi elemento dopo che è stato nascosto
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    } else {
        // Fallback ad alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Controllo performance pagina (solo debug)
 */
@if(config('app.debug'))
window.addEventListener('load', function() {
    if (performance && performance.timing) {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log(`⏱️ Pagina admin prodotto caricata in: ${loadTime}ms`);
        
        if (loadTime > 3000) {
            console.warn('🐌 Caricamento lento rilevato per pagina admin prodotto');
        }
    }
});

// Monitoraggio memoria JavaScript
if (performance && performance.memory) {
    setInterval(() => {
        const memory = performance.memory;
        const used = Math.round(memory.usedJSHeapSize / 1048576);
        const total = Math.round(memory.totalJSHeapSize / 1048576);
        
        if (used > 100) {
            console.warn(`⚠️ Elevato uso di memoria JavaScript: ${used}MB / ${total}MB`);
        }
    }, 60000); // Ogni minuto
}

// Debug panel per sviluppo
function createDebugPanel() {
    const panel = document.createElement('div');
    panel.className = 'debug-panel';
    panel.innerHTML = `
        <div class="debug-title">🔧 DEBUG PANEL</div>
        <div class="debug-item">Prodotto: <span class="debug-status-ok">${config.prodotto.id}</span></div>
        <div class="debug-item">Staff: <span class="${config.prodotto.staffAssegnato ? 'debug-status-ok' : 'debug-status-error'}">${config.prodotto.staffAssegnato || 'Non assegnato'}</span></div>
        <div class="debug-item">Route Update: <span class="debug-status-ok">OK</span></div>
        <div class="debug-item">Bootstrap: <span class="${typeof bootstrap !== 'undefined' ? 'debug-status-ok' : 'debug-status-error'}">${typeof bootstrap !== 'undefined' ? 'Caricato' : 'Mancante'}</span></div>
        <div class="debug-item">Memory: <span id="memory-usage">--</span></div>
    `;
    
    document.body.appendChild(panel);
    
    // Aggiorna memoria ogni 5 secondi
    if (performance && performance.memory) {
        setInterval(() => {
            const used = Math.round(performance.memory.usedJSHeapSize / 1048576);
            document.getElementById('memory-usage').textContent = `${used}MB`;
        }, 5000);
    }
}

// Mostra debug panel se in modalità debug e URL contiene ?debug=1
if (config.debug && new URLSearchParams(window.location.search).get('debug') === '1') {
    createDebugPanel();
}
@endif

/**
 * Refresh automatico statistiche (opzionale)
 */
function refreshStats() {
    const statsCards = document.querySelectorAll('.card[class*="bg-primary"], .card[class*="bg-danger"], .card[class*="bg-info"], .card[class*="bg-warning"], .card[class*="bg-success"]');
    
    // Aggiungi effetto loading
    statsCards.forEach(card => {
        card.classList.add('loading');
    });
    
    // Simula refresh con timeout (sostituire con chiamata AJAX reale se necessario)
    setTimeout(() => {
        statsCards.forEach(card => {
            card.classList.remove('loading');
        });
        showNotification('success', 'Statistiche aggiornate con successo');
    }, 1500);
}

/**
 * Controllo connettività server
 */
function checkServerConnectivity() {
    fetch(config.routes.show, { 
        method: 'HEAD',
        cache: 'no-cache'
    })
    .then(response => {
        if (response.ok) {
            console.log('✅ Server raggiungibile');
            document.body.classList.remove('server-offline');
        } else {
            console.warn('⚠️ Server risponde ma con errori:', response.status);
        }
    })
    .catch(error => {
        console.error('❌ Server non raggiungibile:', error);
        document.body.classList.add('server-offline');
        showNotification('error', 'Problemi di connessione al server. Alcune funzionalità potrebbero non funzionare.');
    });
}

// Controlla connettività ogni 2 minuti
setInterval(checkServerConnectivity, 2 * 60 * 1000);

/**
 * Gestione keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S per salvare (previeni default)
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        showNotification('info', 'Usa i pulsanti di modifica per salvare le modifiche');
        return false;
    }
    
    // Escape per chiudere modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }
    
    // Alt + E per modificare prodotto
    if (e.altKey && e.key === 'e') {
        e.preventDefault();
        const editBtn = document.querySelector('a[href*="edit"]');
        if (editBtn) {
            editBtn.click();
        }
    }
    
    // Alt + S per assegnare staff (se non già assegnato)
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        if (!config.prodotto.staffAssegnato) {
            const assignBtn = document.querySelector('[data-bs-target="#assignStaffModal"]');
            if (assignBtn) {
                assignBtn.click();
            }
        } else {
            const changeBtn = document.querySelector('[data-bs-target="#changeStaffModal"]');
            if (changeBtn) {
                changeBtn.click();
            }
        }
    }
});

/**
 * Tooltips per keyboard shortcuts
 */
function initKeyboardTooltips() {
    const tooltips = [
        { selector: 'a[href*="edit"]', title: 'Modifica prodotto (Alt+E)' },
        { selector: '[data-bs-target="#assignStaffModal"]', title: 'Assegna staff (Alt+S)' },
        { selector: '[data-bs-target="#changeStaffModal"]', title: 'Riassegna staff (Alt+S)' }
    ];
    
    tooltips.forEach(item => {
        const element = document.querySelector(item.selector);
        if (element && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(element, {
                title: item.title,
                placement: 'top'
            });
        }
    });
}

// Inizializza tooltips
initKeyboardTooltips();

/**
 * Auto-save form data (localStorage fallback)
 */
function setupAutoSave() {
    const forms = document.querySelectorAll('form[id]');
    
    forms.forEach(form => {
        const formId = form.id;
        const inputs = form.querySelectorAll('input, select, textarea');
        
        // Carica dati salvati
        inputs.forEach(input => {
            const savedValue = sessionStorage.getItem(`${formId}_${input.name}`);
            if (savedValue && input.type !== 'hidden') {
                input.value = savedValue;
            }
        });
        
        // Salva ad ogni cambio
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.type !== 'hidden') {
                    sessionStorage.setItem(`${formId}_${this.name}`, this.value);
                }
            });
        });
        
        // Pulisci al submit
        form.addEventListener('submit', function() {
            inputs.forEach(input => {
                sessionStorage.removeItem(`${formId}_${input.name}`);
            });
        });
    });
}

setupAutoSave();

/**
 * Prevenzione perdita dati
 */
let hasUnsavedChanges = false;

document.querySelectorAll('form input, form select, form textarea').forEach(input => {
    if (input.type !== 'hidden') {
        input.addEventListener('change', function() {
            hasUnsavedChanges = true;
        });
    }
});

window.addEventListener('beforeunload', function(e) {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire?';
        return e.returnValue;
    }
});

// Reset flag quando si submittano i form
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        hasUnsavedChanges = false;
    });
});

/**
 * Lazy loading per immagini (se supportato)
 */
if ('loading' in HTMLImageElement.prototype) {
    console.log('✅ Native lazy loading supportato');
} else {
    // Fallback per browser che non supportano lazy loading
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.classList.add('lazy');
            imageObserver.observe(img);
        });
    }
}

/**
 * Accessibilità migliorata
 */
function improveAccessibility() {
    // Aggiungi aria-labels mancanti
    document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])').forEach(btn => {
        const text = btn.textContent.trim();
        if (text) {
            btn.setAttribute('aria-label', text);
        }
    });
    
    // Aggiungi role ai pulsanti che sembrano link
    document.querySelectorAll('button[onclick*="location"]').forEach(btn => {
        btn.setAttribute('role', 'link');
    });
    
    // Miglioramento focus per keyboard navigation
    document.querySelectorAll('.btn, .form-control, .form-select').forEach(el => {
        el.addEventListener('focus', function() {
            this.style.outline = '2px solid #007bff';
            this.style.outlineOffset = '2px';
        });
        
        el.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });
}

improveAccessibility();

// ===== INIZIALIZZAZIONE FINALE =====
console.log('🎉 Pagina admin prodotto completamente inizializzata');

// Notifica ready per altri script
window.dispatchEvent(new CustomEvent('adminProductPageReady', {
    detail: {
        prodotto: config.prodotto,
        routes: config.routes,
        timestamp: new Date().toISOString()
    }
}));
</script>

{{-- Script aggiuntivi per funzionalità avanzate --}}
@if(config('app.env') === 'production')
{{-- Analytics, monitoring, etc. per produzione --}}
<script>
// Tracking eventi amministrazione (sostituire con il tuo sistema di analytics)
if (typeof gtag !== 'undefined') {
    gtag('event', 'admin_product_view', {
        'product_id': {{ $prodotto->id }},
        'product_name': @json($prodotto->nome),
        'user_role': 'admin'
    });
}
</script>
@endif

{{-- Script di sicurezza aggiuntivi --}}
<script>
// Prevenzione XSS nelle notificazioni
function sanitizeMessage(message) {
    const div = document.createElement('div');
    div.textContent = message;
    return div.innerHTML;
}

// Override showNotification per sicurezza
const originalShowNotification = window.showNotification;
window.showNotification = function(type, message, duration = 5000) {
    return originalShowNotification(type, sanitizeMessage(message), duration);
};

// Controllo CSP (Content Security Policy)
document.addEventListener('securitypolicyviolation', function(e) {
    console.error('Violazione CSP rilevata:', e.violatedDirective, e.blockedURI);
});
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
                        Debug Information (Solo Sviluppo)
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
                            <h6>Informazioni Sistema:</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th>Laravel:</th><td>{{ app()->version() }}</td></tr>
                                <tr><th>PHP:</th><td>{{ PHP_VERSION }}</td></tr>
                                <tr><th>Environment:</th><td>{{ app()->environment() }}</td></tr>
                                <tr><th>Debug Mode:</th><td>{{ config('app.debug') ? 'Attivo' : 'Disattivo' }}</td></tr>
                                <tr><th>User ID:</th><td>{{ auth()->id() }}</td></tr>
                                <tr><th>User Livello:</th><td>{{ auth()->user()->livello_accesso }}</td></tr>
                                <tr><th>Timestamp:</th><td>{{ now()->format('Y-m-d H:i:s') }}</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6 class="mt-3">Route Disponibili:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.index') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.index</li>
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.show') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.show</li>
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.edit') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.edit</li>
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.update') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.update</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-{{ Route::has('admin.prodotti.toggle-status') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.prodotti.toggle-status</li>
                                <li><i class="bi bi-{{ Route::has('admin.assegna.prodotto') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.assegna.prodotto</li>
                                <li><i class="bi bi-{{ Route::has('admin.dashboard') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> admin.dashboard</li>
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