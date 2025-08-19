{{-- Vista dettaglio prodotto per tecnici (con malfunzionamenti) --}}
@extends('layouts.app')

@section('title', $prodotto->nome . ' - Vista Tecnica')

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB TECNICO === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            @if(auth()->user()->isStaff())
                <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
            @elseif(auth()->user()->isTecnico())
                <li class="breadcrumb-item"><a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('prodotti.completo.index') }}">Catalogo Tecnico</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.categoria', $prodotto->categoria) }}">{{ $prodotto->categoria_label }}</a></li>
            <li class="breadcrumb-item active">{{ $prodotto->nome }}</li>
        </ol>
    </nav>

    <div class="row">
        
        <!-- === COLONNA PRINCIPALE === -->
        <div class="col-lg-8">
            
            <!-- Card Prodotto Principale (Vista Tecnica) -->
            <div class="card card-custom mb-4 {{ $prodotto->hasMalfunzionamentiCritici() ? 'border-danger' : '' }}">
                <div class="row g-0">
                    
                    <!-- Immagine Prodotto -->
                    <div class="col-md-5">
                        <div class="position-relative">
                            <img src="{{ $prodotto->foto_url }}" 
                                 class="img-fluid rounded-start h-100" 
                                 alt="{{ $prodotto->nome }}"
                                 style="object-fit: cover; min-height: 300px;">
                            
                            <!-- Overlay Stato Critico -->
                            @if($prodotto->hasMalfunzionamentiCritici())
                                <div class="position-absolute bottom-0 start-0 end-0 bg-danger bg-opacity-75 text-white text-center py-2">
                                    <strong><i class="bi bi-exclamation-triangle me-1"></i>STATO CRITICO</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informazioni Principali Tecniche -->
                    <div class="col-md-7">
                        <div class="card-body">
                            
                            <!-- Header con Badge Tecnici -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h2 card-title mb-1">{{ $prodotto->nome }}</h1>
                                    <p class="text-muted mb-0">
                                        <strong>Modello:</strong> <code>{{ $prodotto->modello }}</code>
                                    </p>
                                </div>
                                
                                <!-- Badge e Stato Tecnico -->
                                <div class="text-end">
                                    <span class="badge bg-primary mb-2">{{ $prodotto->categoria_label }}</span>
                                    <span class="badge bg-warning text-dark d-block">Vista Tecnica</span>
                                    @if($prodotto->attivo)
                                        <span class="badge bg-success d-block mt-1">Attivo</span>
                                    @else
                                        <span class="badge bg-secondary d-block mt-1">Inattivo</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Descrizione -->
                            <div class="mb-3">
                                <h5>Descrizione Tecnica</h5>
                                <p class="card-text">{{ $prodotto->descrizione }}</p>
                            </div>
                            
                            <!-- Prezzo (se presente) -->
                            @if($prodotto->prezzo)
                                <div class="mb-3">
                                    <h3 class="text-primary">
                                        €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                    </h3>
                                </div>
                            @endif
                            
                            <!-- Staff Assegnato -->
                            @if($prodotto->staffAssegnato)
                                <div class="alert alert-info py-2 mb-3">
                                    <small>
                                        <i class="bi bi-person-badge me-1"></i>
                                        <strong>Gestito da:</strong> {{ $prodotto->staffAssegnato->nome_completo }}
                                        @if(auth()->user()->id === $prodotto->staff_assegnato_id)
                                            <span class="badge bg-success ms-2">Tu</span>
                                        @endif
                                    </small>
                                </div>
                            @elseif(auth()->user()->isAdmin())
                                <div class="alert alert-warning py-2 mb-3">
                                    <small>
                                        <i class="bi bi-person-x me-1"></i>
                                        <strong>Nessun staff assegnato</strong>
                                        <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#assignStaffModal">
                                            Assegna
                                        </button>
                                    </small>
                                </div>
                            @endif
                            
                            <!-- Statistiche Malfunzionamenti Avanzate -->
                            @if($prodotto->totale_malfunzionamenti > 0)
                                <div class="alert border-start border-warning border-4 bg-warning bg-opacity-10">
                                    <h6 class="alert-heading">
                                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                        Analisi Tecnica
                                    </h6>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <strong>{{ $prodotto->totale_malfunzionamenti }}</strong> problemi registrati
                                        </div>
                                        <div class="col-6">
                                            <strong>{{ $prodotto->totale_segnalazioni }}</strong> segnalazioni totali
                                        </div>
                                    </div>
                                    
                                    <!-- Breakdown per Gravità -->
                                    @php
                                        $critici = $prodotto->malfunzionamenti->where('gravita', 'critica')->count();
                                        $alti = $prodotto->malfunzionamenti->where('gravita', 'alta')->count();
                                        $medi = $prodotto->malfunzionamenti->where('gravita', 'media')->count();
                                        $bassi = $prodotto->malfunzionamenti->where('gravita', 'bassa')->count();
                                    @endphp
                                    
                                    <div class="row g-2 small">
                                        @if($critici > 0)
                                            <div class="col-3 text-center">
                                                <span class="badge bg-danger w-100">{{ $critici }} Critici</span>
                                            </div>
                                        @endif
                                        @if($alti > 0)
                                            <div class="col-3 text-center">
                                                <span class="badge bg-warning w-100">{{ $alti }} Alti</span>
                                            </div>
                                        @endif
                                        @if($medi > 0)
                                            <div class="col-3 text-center">
                                                <span class="badge bg-info w-100">{{ $medi }} Medi</span>
                                            </div>
                                        @endif
                                        @if($bassi > 0)
                                            <div class="col-3 text-center">
                                                <span class="badge bg-success w-100">{{ $bassi }} Bassi</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($prodotto->hasMalfunzionamentiCritici())
                                        <div class="alert alert-danger py-2 mt-2 mb-0">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            <strong>PRIORITÀ MASSIMA:</strong> Intervento immediato richiesto
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Stato Ottimale:</strong> Nessun problema registrato
                                </div>
                            @endif
                            
                            <!-- Pulsanti Azione Tecnici -->
                            <div class="d-grid gap-2">
                                @if($prodotto->totale_malfunzionamenti > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                       class="btn btn-{{ $prodotto->hasMalfunzionamentiCritici() ? 'danger' : 'warning' }}">
                                        <i class="bi bi-tools me-2"></i>
                                        Visualizza Tutti i Malfunzionamenti ({{ $prodotto->totale_malfunzionamenti }})
                                    </a>
                                @endif
                                
                                @if(auth()->user()->isStaff())
                                    @if($prodotto->staff_assegnato_id === auth()->id())
                                        <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $prodotto->id]) }}" 
                                           class="btn btn-success">
                                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuova Soluzione
                                        </a>
                                    @endif
                                @endif
                                
                                @if(auth()->user()->isAdmin())
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil me-1"></i>Modifica Prodotto
                                        </a>
                                        <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    data-confirm-delete="Sei sicuro di voler rimuovere {{ $prodotto->nome }}?">
                                                <i class="bi bi-trash me-1"></i>Elimina
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- === SCHEDA TECNICA DETTAGLIATA === -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        Documentazione Tecnica Completa
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Note Tecniche -->
                    <div class="mb-4">
                        <h6><i class="bi bi-gear me-2"></i>Specifiche Tecniche</h6>
                        <div class="bg-light p-3 rounded border-start border-primary border-3">
                            {{ $prodotto->note_tecniche }}
                        </div>
                    </div>
                    
                    <!-- Modalità di Installazione -->
                    <div class="mb-4">
                        <h6><i class="bi bi-tools me-2"></i>Procedure di Installazione</h6>
                        <div class="bg-light p-3 rounded border-start border-success border-3">
                            {!! nl2br(e($prodotto->modalita_installazione)) !!}
                        </div>
                    </div>
                    
                    <!-- Modalità d'Uso -->
                    @if($prodotto->modalita_uso)
                        <div class="mb-0">
                            <h6><i class="bi bi-book me-2"></i>Istruzioni Operative</h6>
                            <div class="bg-light p-3 rounded border-start border-info border-3">
                                {!! nl2br(e($prodotto->modalita_uso)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- === MALFUNZIONAMENTI COMPLETI (Solo per Tecnici) === -->
            <div class="card card-custom mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Database Malfunzionamenti
                        @if($prodotto->malfunzionamenti->count() > 0)
                            <span class="badge bg-warning ms-2">{{ $prodotto->malfunzionamenti->count() }}</span>
                        @endif
                    </h5>
                    
                    @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                        <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $prodotto->id]) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($prodotto->malfunzionamenti->count() > 0)
                        
                        <!-- Filtri Rapidi Malfunzionamenti -->
                        <div class="mb-3">
                            <div class="btn-group btn-group-sm" role="group" id="malfunzionamentoFilter">
                                <button type="button" class="btn btn-outline-secondary active" data-filter="all">
                                    Tutti ({{ $prodotto->malfunzionamenti->count() }})
                                </button>
                                @if($critici > 0)
                                    <button type="button" class="btn btn-outline-danger" data-filter="critica">
                                        Critici ({{ $critici }})
                                    </button>
                                @endif
                                @if($alti > 0)
                                    <button type="button" class="btn btn-outline-warning" data-filter="alta">
                                        Alti ({{ $alti }})
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-info" data-filter="recent">
                                    Recenti
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lista Malfunzionamenti Avanzata -->
                        <div class="row g-3" id="malfunzionamentiList">
                            @foreach($prodotto->malfunzionamenti_ordered as $malfunzionamento)
                                <div class="col-md-6 malfunzionamento-item" data-gravita="{{ $malfunzionamento->gravita }}" data-created="{{ $malfunzionamento->created_at->format('Y-m-d') }}">
                                    <div class="card border-start border-{{ $malfunzionamento->gravita === 'critica' ? 'danger' : ($malfunzionamento->gravita === 'alta' ? 'warning' : 'info') }} border-3">
                                        <div class="card-body py-3">
                                            
                                            <!-- Header Malfunzionamento -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">{{ $malfunzionamento->titolo }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $malfunzionamento->gravita === 'critica' ? 'danger' : ($malfunzionamento->gravita === 'alta' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                    @if($malfunzionamento->gravita === 'critica')
                                                        <span class="badge bg-danger ms-1">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Descrizione -->
                                            <p class="card-text small text-muted mb-2">
                                                {{ Str::limit($malfunzionamento->descrizione, 100) }}
                                            </p>
                                            
                                            <!-- Statistiche Tecniche -->
                                            <div class="row g-2 mb-2 small">
                                                <div class="col-6">
                                                    <i class="bi bi-flag text-warning me-1"></i>
                                                    <strong>{{ $malfunzionamento->numero_segnalazioni }}</strong> segnalazioni
                                                </div>
                                                @if($malfunzionamento->tempo_stimato)
                                                    <div class="col-6">
                                                        <i class="bi bi-clock text-info me-1"></i>
                                                        {{ $malfunzionamento->tempo_formattato }}
                                                    </div>
                                                @endif
                                                @if($malfunzionamento->difficolta)
                                                    <div class="col-6">
                                                        <i class="bi bi-star text-primary me-1"></i>
                                                        {{ ucfirst($malfunzionamento->difficolta) }}
                                                    </div>
                                                @endif
                                                <div class="col-6">
                                                    <i class="bi bi-calendar text-muted me-1"></i>
                                                    {{ $malfunzionamento->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                            
                                            <!-- Azioni -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if($malfunzionamento->creadoDa)
                                                        <small class="text-muted">
                                                            <i class="bi bi-person me-1"></i>{{ $malfunzionamento->creadoDa->nome_completo }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                       class="btn btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Dettagli
                                                    </a>
                                                    @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                                        <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                                           class="btn btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Link per Vedere Tutti -->
                        @if($prodotto->malfunzionamenti->count() > 8)
                            <div class="text-center mt-3">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-warning">
                                    <i class="bi bi-list me-1"></i>
                                    Visualizza Database Completo ({{ $prodotto->malfunzionamenti->count() }} totali)
                                </a>
                            </div>
                        @endif
                        
                    @else
                        <!-- Nessun Malfunzionamento -->
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                            <h5>Nessun Malfunzionamento Registrato</h5>
                            <p class="text-muted">Questo prodotto mantiene uno stato operativo ottimale.</p>
                            
                            @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                <a href="{{ route('staff.malfunzionamenti.create', ['prodotto' => $prodotto->id]) }}" class="btn btn-warning">
                                    <i class="bi bi-plus-circle me-1"></i>Aggiungi Prima Soluzione
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR TECNICA === -->
        <div class="col-lg-4">
            
            <!-- Dashboard Tecnica Rapida -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Dashboard Tecnica
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-6">ID Prodotto:</dt>
                        <dd class="col-sm-6"><code>{{ $prodotto->id }}</code></dd>
                        
                        <dt class="col-sm-6">Categoria:</dt>
                        <dd class="col-sm-6">{{ $prodotto->categoria_label }}</dd>
                        
                        <dt class="col-sm-6">Modello:</dt>
                        <dd class="col-sm-6"><code>{{ $prodotto->modello }}</code></dd>
                        
                        <dt class="col-sm-6">Stato:</dt>
                        <dd class="col-sm-6">
                            @if($prodotto->attivo)
                                <span class="badge bg-success">Attivo</span>
                            @else
                                <span class="badge bg-secondary">Inattivo</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-6">Inserimento:</dt>
                        <dd class="col-sm-6">{{ $prodotto->created_at->format('d/m/Y') }}</dd>
                        
                        <dt class="col-sm-6">Ultimo Aggiorn.:</dt>
                        <dd class="col-sm-6">{{ $prodotto->updated_at->format('d/m/Y') }}</dd>
                        
                        <dt class="col-sm-6">Totale Problemi:</dt>
                        <dd class="col-sm-6">
                            @if($prodotto->totale_malfunzionamenti > 0)
                                <span class="badge bg-warning">{{ $prodotto->totale_malfunzionamenti }}</span>
                            @else
                                <span class="badge bg-success">0</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-6">Critici:</dt>
                        <dd class="col-sm-6">
                            @if($critici > 0)
                                <span class="badge bg-danger">{{ $critici }}</span>
                            @else
                                <span class="badge bg-success">0</span>
                            @endif
                        </dd>
                        
                        @if($prodotto->prezzo)
                            <dt class="col-sm-6">Prezzo:</dt>
                            <dd class="col-sm-6 text-primary fw-bold">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Azioni Rapide Tecniche -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Tecniche
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        <!-- Torna al Catalogo Tecnico -->
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Torna al Catalogo Tecnico
                        </a>
                        
                        <!-- Vedi Altri della Categoria -->
                        <a href="{{ route('prodotti.completo.index') }}?categoria={{ $prodotto->categoria }}" class="btn btn-outline-primary">
                            <i class="bi bi-grid me-1"></i>Altri {{ $prodotto->categoria_label }}
                        </a>
                        
                        <!-- Ricerca Problema Specifico -->
                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="bi bi-search me-1"></i>Cerca Problema Specifico
                        </button>
                        
                        <!-- Segnala Nuovo Problema (Tecnico) -->
                        @if(auth()->user()->isTecnico())
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#reportModal">
                                <i class="bi bi-plus-circle me-1"></i>Segnala Problema
                            </button>
                        @endif
                        
                        <!-- Trova Centro Assistenza -->
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-geo-alt me-1"></i>Trova Centro Assistenza
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Prodotti Correlati Tecnici -->
            @php
                $prodotti_correlati = \App\Models\Prodotto::where('categoria', $prodotto->categoria)
                    ->where('id', '!=', $prodotto->id)
                    ->where('attivo', true)
                    ->withCount(['malfunzionamenti as critici_count' => function($query) {
                        $query->where('gravita', 'critica');
                    }])
                    ->limit(3)
                    ->get();
            @endphp
            
            @if($prodotti_correlati->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-box text-primary me-2"></i>
                            Prodotti Correlati
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($prodotti_correlati as $correlato)
                            <div class="d-flex align-items-center mb-3">
                                <div class="position-relative me-3">
                                    <img src="{{ $correlato->foto_url }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                         alt="{{ $correlato->nome }}">
                                    @if($correlato->critici_count > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-pill" 
                                              style="font-size: 0.6rem;">
                                            {{ $correlato->critici_count }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('prodotti.completo.show', $correlato) }}" class="text-decoration-none">
                                            {{ $correlato->nome }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $correlato->modello }}</small>
                                    @if($correlato->critici_count > 0)
                                        <span class="badge bg-danger ms-2">{{ $correlato->critici_count }} critici</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="d-grid">
                            <a href="{{ route('prodotti.completo.index') }}?categoria={{ $prodotto->categoria }}" 
                               class="btn btn-sm btn-outline-primary">
                                Vedi Tutti i {{ $prodotto->categoria_label }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Supporto Tecnico Avanzato -->
            <div class="card card-custom">
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Supporto specializzato per {{ $prodotto->categoria_label }}.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('centri.index') }}" class="btn btn-sm btn-info">
                            <i class="bi bi-telephone me-1"></i>Contatta Assistenza
                        </a>
                        <a href="{{ route('contatti') }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-envelope me-1"></i>Richiedi Supporto
                        </a>
                        
                        @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                            <hr class="my-2">
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Sei il responsabile tecnico di questo prodotto
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL RICERCA PROBLEMI === -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-search me-2"></i>
                    Ricerca Problema Specifico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="problemSearch" class="form-label">Descrivi il problema che stai riscontrando:</label>
                    <input type="text" class="form-control" id="problemSearch" 
                           placeholder="es: non centrifuga, rumore strano, perdita acqua, display spento...">
                    <div class="form-text">
                        Inserisci parole chiave per trovare soluzioni simili nel database
                    </div>
                </div>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL SEGNALAZIONE PROBLEMA (Tecnici) === -->
@if(auth()->user()->isTecnico())
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Segnala Nuovo Problema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    @csrf
                    <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                    
                    <div class="mb-3">
                        <label for="reportTitle" class="form-label">Titolo del Problema</label>
                        <input type="text" class="form-control" id="reportTitle" name="titolo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reportDescription" class="form-label">Descrizione Dettagliata</label>
                        <textarea class="form-control" id="reportDescription" name="descrizione" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reportGravity" class="form-label">Gravità del Problema</label>
                        <select class="form-select" id="reportGravity" name="gravita" required>
                            <option value="">Seleziona gravità</option>
                            <option value="bassa">Bassa - Problema minore</option>
                            <option value="media">Media - Problema moderato</option>
                            <option value="alta">Alta - Problema significativo</option>
                            <option value="critica">Critica - Richiede intervento immediato</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-warning" id="submitReport">
                    <i class="bi bi-send me-1"></i>Invia Segnalazione
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
/* Stili specifici per vista tecnica */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Card con bordo critico */
.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

/* Overlay per stato critico */
.bg-opacity-75 {
    background-color: rgba(var(--bs-danger-rgb), 0.75) !important;
}

/* Filtri malfunzionamenti */
.malfunzionamento-item {
    transition: all 0.3s ease;
}

.malfunzionamento-item.d-none {
    display: none !important;
}

/* Badge migliorati */
.badge {
    font-size: 0.75rem;
}

/* Bordi colorati per gravità */
.border-start.border-3 {
    border-width: 3px !important;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === INIZIALIZZAZIONE VISTA TECNICA ===
    console.log('Vista tecnica prodotto inizializzata');
    console.log('Prodotto ID:', {{ $prodotto->id }});
    console.log('Malfunzionamenti:', {{ $prodotto->malfunzionamenti->count() }});
    console.log('Critici:', {{ $critici ?? 0 }});
    
    // === FILTRI MALFUNZIONAMENTI ===
    $('#malfunzionamentoFilter button').on('click', function() {
        const filter = $(this).data('filter');
        
        // Aggiorna stato attivo pulsanti
        $('#malfunzionamentoFilter button').removeClass('active');
        $(this).addClass('active');
        
        // Applica filtro
        filterMalfunzionamenti(filter);
    });
    
    function filterMalfunzionamenti(filter) {
        const items = $('.malfunzionamento-item');
        
        if (filter === 'all') {
            items.removeClass('d-none');
        } else if (filter === 'recent') {
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            items.each(function() {
                const createdDate = new Date($(this).data('created'));
                if (createdDate >= thirtyDaysAgo) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        } else {
            items.each(function() {
                const gravita = $(this).data('gravita');
                if (gravita === filter) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        
        // Aggiorna contatore visibili
        const visibleCount = items.not('.d-none').length;
        console.log(`Filtro '${filter}' applicato: ${visibleCount} elementi visibili`);
    }
    
    // === CONFERMA ELIMINAZIONE ===
    $('[data-confirm-delete]').on('click', function(e) {
        e.preventDefault();
        
        const message = $(this).data('confirm-delete');
        const form = $(this).closest('form');
        
        if (confirm(message)) {
            form.submit();
        }
    });
    
    // === RICERCA PROBLEMI (Modal) ===
    let searchTimeout;
    
    $('#problemSearch').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                searchProblems(query);
            }, 300);
        } else {
            $('#searchResults').empty();
        }
    });
    
    function searchProblems(query) {
        $('#searchResults').html(`
            <div class="text-center">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span class="ms-2">Ricerca in corso...</span>
            </div>
        `);
        
        $.get('{{ route("api.malfunzionamenti.search") }}', { 
            q: query,
            prodotto_id: {{ $prodotto->id }}
        })
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<h6 class="mb-3">Problemi Trovati:</h6><div class="list-group">';
                
                response.data.forEach(function(item) {
                    const badgeClass = item.gravita === 'critica' ? 'danger' : 
                                      (item.gravita === 'alta' ? 'warning' : 'info');
                    
                    html += `
                        <a href="${item.url}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${item.titolo}</h6>
                                <div>
                                    <span class="badge bg-${badgeClass}">${item.gravita}</span>
                                    <small class="text-muted ms-2">${item.segnalazioni} segnalazioni</small>
                                </div>
                            </div>
                            <p class="mb-1 small">${item.descrizione_breve}</p>
                            <small class="text-muted">
                                Difficoltà: ${item.difficolta || 'N/A'} | 
                                Tempo stimato: ${item.tempo_formattato || 'N/A'}
                            </small>
                        </a>
                    `;
                });
                
                html += '</div>';
                $('#searchResults').html(html);
                
            } else {
                $('#searchResults').html(`
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Nessun problema simile trovato per "<strong>${query}</strong>".
                        <br><small class="text-muted">Prova con parole chiave diverse o più generiche.</small>
                    </div>
                `);
            }
        })
        .fail(function() {
            $('#searchResults').html(`
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Errore durante la ricerca. Riprova più tardi.
                </div>
            `);
        });
    }
    
    // === SEGNALAZIONE PROBLEMA (Solo Tecnici) ===
    @if(auth()->user()->isTecnico())
        $('#submitReport').on('click', function() {
            const form = $('#reportForm');
            const formData = new FormData(form[0]);
            
            // Disabilita pulsante durante invio
            $(this).prop('disabled', true).html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Invio...');
            
            $.ajax({
                url: '{{ route("api.malfunzionamenti.report") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                if (response.success) {
                    $('#reportModal').modal('hide');
                    form[0].reset();
                    
                    // Mostra messaggio di successo
                    $('body').append(`
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999;">
                            <i class="bi bi-check-circle me-2"></i>
                            Segnalazione inviata con successo!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    
                    // Ricarica pagina dopo 2 secondi per mostrare nuovo problema
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    alert('Errore nell\'invio della segnalazione: ' + response.message);
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMsg = 'Errori di validazione:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMsg += '- ' + response.errors[key][0] + '\n';
                    });
                    alert(errorMsg);
                } else {
                    alert('Errore nell\'invio della segnalazione. Riprova più tardi.');
                }
            })
            .always(function() {
                // Riabilita pulsante
                $('#submitReport').prop('disabled', false).html('<i class="bi bi-send me-1"></i>Invia Segnalazione');
            });
        });
        
        // Reset modal quando viene chiusa
        $('#reportModal').on('hidden.bs.modal', function() {
            $('#reportForm')[0].reset();
            $('#submitReport').prop('disabled', false).html('<i class="bi bi-send me-1"></i>Invia Segnalazione');
        });
    @endif
    
    // === LAZY LOADING IMMAGINI ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // === ANALYTICS TECNICO ===
    console.log('Vista tecnica completa caricata:', {
        prodotto_id: {{ $prodotto->id }},
        nome: '{{ $prodotto->nome }}',
        categoria: '{{ $prodotto->categoria }}',
        malfunzionamenti: {{ $prodotto->totale_malfunzionamenti }},
        critici: {{ $critici ?? 0 }},
        user_level: {{ auth()->user()->livello_accesso }},
        is_assigned: {{ ($prodotto->staff_assegnato_id === auth()->id()) ? 'true' : 'false' }},
        timestamp: new Date().toISOString()
    });
    
    // Traccia tempo di permanenza sulla pagina
    let startTime = Date.now();
    
    $(window).on('beforeunload', function() {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        console.log(`Tempo speso sulla vista tecnica: ${timeSpent} secondi`);
    });
});
</script>
@endpush