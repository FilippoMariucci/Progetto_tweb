{{--
    Vista completa per prodotto con malfunzionamenti (per tecnici)
    LAYOUT ORIZZONTALE con immagini corrette
    Percorso: resources/views/prodotti/completo/show.blade.php
    Accessibile solo a utenti con livello_accesso >= 2
--}}

@extends('layouts.app')

@section('title', $prodotto->nome . ' - Dettagli Completi')

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ $prodotto->nome }}</h2>
            <p class="text-muted small mb-0">Dettagli tecnici completi</p>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Catalogo
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Modifica
                </a>
            @endif
        </div>
    </div>

    {{-- Breadcrumb compatto --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            @if(auth()->user()->isStaff())
                <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
            @elseif(auth()->user()->isTecnico())
                <li class="breadcrumb-item"><a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('prodotti.completo.index') }}">Catalogo Completo</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($prodotto->nome, 30) }}</li>
        </ol>
    </nav>

    {{-- === ALERT PROBLEMI CRITICI === --}}
    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>ATTENZIONE: Problemi Critici</strong> - 
                    Questo prodotto ha <span class="badge bg-white text-danger">{{ $statistiche['malfunzionamenti_critici'] }}</span> 
                    problema/i critico/i che richiedono intervento immediato.
                </div>
                <a href="#malfunzionamenti-section" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-down me-1"></i>Vai ai Problemi
                </a>
            </div>
        </div>
    @endif

    {{-- === LAYOUT ORIZZONTALE PRINCIPALE === --}}
    <div class="row g-4">
        
        {{-- === COLONNA IMMAGINE CORRETTA === --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="position-relative">
                    @if($prodotto->foto)
                        {{-- IMMAGINE CORRETTA CON OBJECT-FIT CONTAIN --}}
                        <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 280px; object-fit: contain; background-color: #f8f9fa; cursor: pointer; padding: 1rem;"
                             onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 280px;">
                            <div class="text-center">
                                <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 small">Immagine non disponibile</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Badge categoria --}}
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                    </div>
                    
                    {{-- Badge prezzo --}}
                    @if($prodotto->prezzo)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    {{-- Indicatore problemi critici --}}
                    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
                        <div class="position-absolute bottom-0 start-0 end-0 bg-danger text-white text-center py-1">
                            <small><i class="bi bi-exclamation-triangle me-1"></i><strong>PRIORITÀ ALTA</strong></small>
                        </div>
                    @endif
                </div>
                
                {{-- Azioni immagine compatte --}}
                @if($prodotto->foto)
                    <div class="card-body py-2">
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-primary btn-sm flex-fill" 
                                    onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                                <i class="bi bi-zoom-in me-1"></i>Ingrandisci
                            </button>
                            <a href="{{ asset('storage/' . $prodotto->foto) }}" 
                               download="{{ Str::slug($prodotto->nome) }}.jpg" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- === INFO TECNICHE COMPATTE === --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-tools me-1"></i>Info Tecniche
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 text-center">
                        @if($prodotto->created_at)
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Catalogato</small>
                                    <strong class="small">{{ $prodotto->created_at->format('d/m/Y') }}</strong>
                                </div>
                            </div>
                        @endif
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Categoria</small>
                                <strong class="small">{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                            </div>
                        </div>
                        @if($prodotto->modello)
                            <div class="col-12">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Modello</small>
                                    <code class="small">{{ $prodotto->modello }}</code>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <div class="col-12">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Staff Assegnato</small>
                                    <span class="badge bg-info small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $prodotto->staffAssegnato->nome_completo }}
                                    </span>
                                </div>
                            </div>
                        @elseif(auth()->user()->isAdmin())
                            <div class="col-12">
                                <div class="p-2 bg-warning bg-opacity-10 rounded">
                                    <small class="text-warning">
                                        <i class="bi bi-person-x me-1"></i>
                                        Nessun staff assegnato
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- === STATISTICHE PROBLEMI COMPATTE === --}}
            @if(isset($statistiche) && ($showMalfunzionamenti ?? false))
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-1"></i>Statistiche Problemi
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                    <div class="fw-bold text-primary">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</div>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                    <div class="fw-bold text-danger">{{ $statistiche['malfunzionamenti_critici'] ?? 0 }}</div>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                    <div class="fw-bold text-warning">{{ $statistiche['malfunzionamenti_alti'] ?? 0 }}</div>
                                    <small class="text-muted">Alta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                    <div class="fw-bold text-info">{{ $statistiche['totale_segnalazioni'] ?? 0 }}</div>
                                    <small class="text-muted">Segnalazioni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- === COLONNA INFORMAZIONI PRINCIPALE === --}}
        <div class="col-lg-8 col-md-7">
            
            {{-- Header prodotto compatto --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-2">{{ $prodotto->nome }}</h1>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($prodotto->modello)
                            <span class="badge bg-secondary small">{{ $prodotto->modello }}</span>
                        @endif
                        
                        {{-- Badge staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <span class="badge bg-info small">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ Str::limit($prodotto->staffAssegnato->nome_completo, 20) }}
                            </span>
                        @endif
                        
                        {{-- Badge stato problemi --}}
                        @if(isset($statistiche))
                            @if($statistiche['malfunzionamenti_critici'] > 0)
                                <span class="badge bg-danger small">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ $statistiche['malfunzionamenti_critici'] }} Critici
                                </span>
                            @elseif($statistiche['totale_malfunzionamenti'] > 0)
                                <span class="badge bg-warning small">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $statistiche['totale_malfunzionamenti'] }} Problemi
                                </span>
                            @else
                                <span class="badge bg-success small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Nessun Problema
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                @if($prodotto->prezzo)
                    <div class="text-end">
                        <h4 class="text-success mb-0">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</h4>
                    </div>
                @endif
            </div>
            
            {{-- Descrizione --}}
            @if($prodotto->descrizione)
                <div class="mb-3">
                    <p class="text-muted">{{ $prodotto->descrizione }}</p>
                </div>
            @endif
            
            {{-- === SCHEDA TECNICA COMPATTA === --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i>Scheda Tecnica Completa
                    </h6>
                </div>
                <div class="card-body py-3">
                    
                    {{-- Layout compatto per scheda tecnica --}}
                    <div class="row g-3">
                        
                        {{-- Note tecniche --}}
                        @if($prodotto->note_tecniche)
                            <div class="col-lg-4">
                                <h6 class="text-primary small fw-semibold">
                                    <i class="bi bi-gear me-1"></i>Specifiche Tecniche
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-primary border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->note_tecniche)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Installazione --}}
                        @if($prodotto->modalita_installazione)
                            <div class="col-lg-4">
                                <h6 class="text-success small fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Modalità Installazione
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-success border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_installazione)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Modalità d'uso --}}
                        @if($prodotto->modalita_uso)
                            <div class="col-lg-4">
                                <h6 class="text-info small fw-semibold">
                                    <i class="bi bi-book me-1"></i>Modalità d'Uso
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-info border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_uso)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Messaggio informazioni mancanti --}}
                        @if(!$prodotto->note_tecniche && !$prodotto->modalita_installazione && !$prodotto->modalita_uso)
                            <div class="col-12 text-center py-3">
                                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">
                                    Scheda tecnica in aggiornamento.
                                    @if(auth()->user()->isAdmin())
                                        <br><a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="bi bi-pencil me-1"></i>Completa le informazioni
                                        </a>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === SEZIONE MALFUNZIONAMENTI COMPATTA === --}}
            @if(($showMalfunzionamenti ?? false))
                <div class="card border-0 shadow-sm" id="malfunzionamenti-section">
                    <div class="card-header bg-warning text-dark py-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Malfunzionamenti e Soluzioni Tecniche
                                @if(isset($statistiche))
                                    <span class="badge bg-dark ms-1">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</span>
                                @endif
                            </h6>
                            
                            {{-- Azioni staff compatte --}}
                            <div class="d-flex gap-1 mt-2 mt-md-0">
                                @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                       class="btn btn-dark btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi
                                    </a>
                                @endif
                                
                                @if(($prodotto->malfunzionamenti ?? collect())->count() > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                       class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-list me-1"></i>Vista Completa
                                    </a>
                                @endif
                                
                                {{-- Filtri rapidi compatti --}}
                                <div class="btn-group btn-group-sm" id="malfunzionamentoFilter">
                                    <button type="button" class="btn btn-outline-dark active" data-filter="all">Tutti</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="critica">Critici</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="recent">Recenti</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        
                        {{-- Griglia malfunzionamenti compatta --}}
                        <div class="row g-3" id="malfunzionamentiList">
                            @forelse($prodotto->malfunzionamenti ?? [] as $malfunzionamento)
                                <div class="col-lg-6 malfunzionamento-item" 
                                     data-gravita="{{ $malfunzionamento->gravita }}" 
                                     data-created="{{ $malfunzionamento->created_at->format('Y-m-d') }}">
                                    
                                    @php
                                        $borderColor = match($malfunzionamento->gravita) {
                                            'critica' => 'danger',
                                            'alta' => 'warning', 
                                            'media' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $badgeColor = $borderColor;
                                        
                                        $diffColors = [
                                            'facile' => 'success',
                                            'media' => 'info',
                                            'difficile' => 'warning',
                                            'esperto' => 'danger'
                                        ];
                                    @endphp
                                    
                                    <div class="card border-start border-{{ $borderColor }} border-3 h-100">
                                        <div class="card-body py-3">
                                            
                                            {{-- Header malfunzionamento --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold small">{{ $malfunzionamento->titolo }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $badgeColor }} small">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            {{-- Descrizione --}}
                                            <p class="text-muted small mb-2">
                                                {{ Str::limit($malfunzionamento->descrizione, 80) }}
                                            </p>
                                            
                                            {{-- Badge difficoltà e tempo --}}
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                <span class="badge bg-{{ $diffColors[$malfunzionamento->difficolta] ?? 'secondary' }} small">
                                                    {{ ucfirst($malfunzionamento->difficolta) }}
                                                </span>
                                                
                                                @if($malfunzionamento->numero_segnalazioni)
                                                    <span class="badge bg-primary small" id="badge-{{ $malfunzionamento->id }}">
                                                        <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }}
                                                    </span>
                                                @endif
                                                
                                                @if($malfunzionamento->tempo_stimato)
                                                    <span class="badge bg-info small">
                                                        <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }}min
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            {{-- Azioni compatte --}}
                                            <div class="d-grid gap-1">
                                                {{-- Visualizza soluzione --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-{{ $borderColor }} btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                                </a>
                                                
                                                {{-- Azioni secondarie --}}
                                                <div class="d-flex gap-1">
                                                    {{-- Segnala problema --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm segnala-btn flex-fill"
                                                            onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                            title="Segnala problema">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Segnala
                                                    </button>
                                                    
                                                    {{-- Solo per staff: modifica --}}
                                                    @if(auth()->user()->canManageMalfunzionamenti())
                                                        <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                                           class="btn btn-outline-secondary btn-sm"
                                                           title="Modifica">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Info creatore per staff --}}
                                            @if($malfunzionamento->creatoBy && auth()->user()->isStaff())
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        {{ $malfunzionamento->creatoBy->nome_completo ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                            @empty
                                {{-- Nessun malfunzionamento --}}
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                                        <h5 class="text-success mt-2">Ottima notizia!</h5>
                                        <p class="text-muted">
                                            Non ci sono malfunzionamenti noti per questo prodotto.
                                        </p>
                                        
                                        {{-- Solo per staff: aggiungi primo malfunzionamento --}}
                                        @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                            <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                               class="btn btn-outline-warning btn-sm mt-2">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Aggiungi Primo Malfunzionamento
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Link per vedere tutti --}}
                        @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 4)
                            <div class="text-center mt-3">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-list me-1"></i>
                                    Visualizza Tutti ({{ $prodotto->malfunzionamenti->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- === PRODOTTI CORRELATI COMPATTI === --}}
    @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-collection text-info me-1"></i>
                            Prodotti Correlati nella Stessa Categoria
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            @foreach($prodottiCorrelati->take(4) as $correlato)
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @if($correlato->foto)
                                                    {{-- IMMAGINE CORRELATA CORRETTA --}}
                                                    <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: contain; background-color: #f8f9fa;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 small">
                                                        <a href="{{ route('prodotti.completo.show', $correlato) }}" 
                                                           class="text-decoration-none">
                                                            {{ Str::limit($correlato->nome, 25) }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted d-block mb-1">
                                                        {{ $correlato->modello ?? 'N/A' }}
                                                    </small>
                                                    <div class="d-flex gap-1">
                                                        @if($correlato->malfunzionamenti_count > 0)
                                                            <span class="badge bg-warning text-dark small">
                                                                {{ $correlato->malfunzionamenti_count }} problemi
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success small">OK</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

{{-- === MODAL IMMAGINE === --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="imageModalImg" src="" alt="" class="img-fluid w-100" style="object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* === STILI COMPATTI PER PRODOTTO COMPLETO === */

/* Card base */
.card {
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

/* === IMMAGINE PRODOTTO CORRETTA === */
.product-image {
    transition: transform 0.3s ease;
    border-radius: 0.375rem;
}

.product-image:hover {
    transform: scale(1.02);
}

/* Badge più compatti */
.badge.small {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Card body compatti */
.card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card-body.py-3 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}

/* Card header compatti */
.card-header.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

/* Malfunzionamenti compatti */
.malfunzionamento-item {
    transition: all 0.3s ease;
}

.malfunzionamento-item:hover {
    transform: translateY(-2px);
}

.border-3 {
    border-width: 3px !important;
}

/* Colori per gravità */
.card.border-start.border-danger {
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.15);
}

.card.border-start.border-warning {
    box-shadow: 0 0.125rem 0.25rem rgba(255, 193, 7, 0.15);
}

.card.border-start.border-info {
    box-shadow: 0 0.125rem 0.25rem rgba(13, 202, 240, 0.15);
}

/* Background opacity personalizzati */
.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-info.bg-opacity-10 {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

/* Alert personalizzati */
.alert {
    border-radius: 0.5rem;
}

.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Responsive design */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .product-image {
        height: 200px !important;
    }
    
    .h3 {
        font-size: 1.3rem;
    }
    
    .col-lg-4.col-md-5 {
        margin-bottom: 1rem;
    }
    
    .d-flex.gap-1 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-1 .btn {
        width: 100%;
    }
    
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 0.75rem !important;
    }
    
    .product-image {
        height: 180px !important;
        padding: 0.5rem;
    }
    
    .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row.g-3 > * {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .h2 {
        font-size: 1.2rem;
    }
    
    .d-flex.flex-wrap.gap-1 {
        gap: 0.25rem !important;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* Animazioni */
.btn:hover {
    transform: translateY(-1px);
}

.badge:hover {
    transform: scale(1.05);
}

/* Focus migliorato */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

/* Modal immagine */
#imageModal .modal-body img {
    max-height: 80vh;
}

/* Scrollbar personalizzata */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

@push('scripts')
<script>
// Inizializza i dati della pagina se non esistono già
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";
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