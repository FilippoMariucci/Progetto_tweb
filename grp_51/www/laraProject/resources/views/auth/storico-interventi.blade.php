{{-- 
    Vista Storico Interventi Tecnici
    File: resources/views/auth/storico-interventi.blade.php
    
    Questa vista mostra lo storico completo degli interventi tecnici (malfunzionamenti risolti).
    Accessibile a tecnici (livello 2+), staff aziendale e amministratori.
    
    Funzionalità:
    - Visualizzazione cronologica degli interventi
    - Filtri avanzati per data, gravità, prodotto, categoria
    - Statistiche riassuntive degli interventi
    - Ricerca testuale nei contenuti
--}}

@extends('layouts.app')

@section('title', 'Storico Interventi Tecnici')

@section('content')
<div class="container-fluid mt-4">
    
    {{-- === HEADER DELLA PAGINA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Storico Interventi Tecnici
                    </h1>
                    <p class="text-muted mb-0">
                        Cronologia completa di tutti gli interventi e soluzioni tecniche
                        {{-- Mostra informazioni diverse basate sul ruolo utente --}}
                        @if($user->isTecnico())
                            <span class="badge bg-info ms-2">Vista Tecnico</span>
                        @elseif($user->isStaff())
                            <span class="badge bg-warning text-dark ms-2">I Tuoi Prodotti</span>
                        @elseif($user->isAdmin())
                            <span class="badge bg-danger ms-2">Vista Completa Admin</span>
                        @endif
                    </p>
                </div>
                <div>
                    {{-- Pulsante per tornare alla dashboard --}}
                    @if($user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Dashboard Admin
                        </a>
                    @elseif($user->isStaff())
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Dashboard Staff
                        </a>
                    @else
                        <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Dashboard Tecnico
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === --}}
    @if(isset($statisticheStorico))
        <div class="row mb-4">
            <div class="col-12">
                <div class="row g-3">
                    {{-- Statistiche temporali --}}
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-list-check fs-1 me-3"></i>
                                    <div>
                                        <h3 class="mb-0">{{ $statisticheStorico['totale_interventi'] ?? 0 }}</h3>
                                        <small>Interventi Totali</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-day fs-1 me-3"></i>
                                    <div>
                                        <h3 class="mb-0">{{ $statisticheStorico['interventi_oggi'] ?? 0 }}</h3>
                                        <small>Oggi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-week fs-1 me-3"></i>
                                    <div>
                                        <h3 class="mb-0">{{ $statisticheStorico['interventi_settimana'] ?? 0 }}</h3>
                                        <small>Ultima Settimana</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-month fs-1 me-3"></i>
                                    <div>
                                        <h3 class="mb-0">{{ $statisticheStorico['interventi_mese'] ?? 0 }}</h3>
                                        <small>Ultimo Mese</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === FILTRI AVANZATI === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filtri di Ricerca
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Form filtri con metodo GET per mantenere i parametri nell'URL --}}
                    <form method="GET" action="{{ route('auth.storico-interventi') }}" class="row g-3">
                        
                        {{-- Ricerca testuale --}}
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Testuale
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Descrizione, soluzione, prodotto...">
                        </div>
                        
                        {{-- Filtro periodo --}}
                        <div class="col-md-2">
                            <label for="periodo" class="form-label fw-semibold">
                                <i class="bi bi-calendar3 me-1"></i>Periodo
                            </label>
                            <select name="periodo" id="periodo" class="form-select">
                                <option value="">Tutti</option>
                                <option value="oggi" {{ request('periodo') == 'oggi' ? 'selected' : '' }}>Oggi</option>
                                <option value="settimana" {{ request('periodo') == 'settimana' ? 'selected' : '' }}>Ultima Settimana</option>
                                <option value="mese" {{ request('periodo') == 'mese' ? 'selected' : '' }}>Ultimo Mese</option>
                                <option value="trimestre" {{ request('periodo') == 'trimestre' ? 'selected' : '' }}>Ultimo Trimestre</option>
                                <option value="anno" {{ request('periodo') == 'anno' ? 'selected' : '' }}>Ultimo Anno</option>
                            </select>
                        </div>
                        
                        {{-- Filtro gravità --}}
                        <div class="col-md-2">
                            <label for="gravita" class="form-label fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Gravità
                            </label>
                            <select name="gravita" id="gravita" class="form-select">
                                <option value="">Tutte</option>
                                <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>Bassa</option>
                                <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>Critica</option>
                            </select>
                        </div>
                        
                        {{-- Filtro categoria --}}
                        <div class="col-md-2">
                            <label for="categoria" class="form-label fw-semibold">
                                <i class="bi bi-tags me-1"></i>Categoria
                            </label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="">Tutte</option>
                                @if(isset($categorie))
                                    @foreach($categorie as $cat)
                                        <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        {{-- Pulsanti azioni --}}
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filtra
                                </button>
                                {{-- Pulsante reset se ci sono filtri attivi --}}
                                @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria', 'prodotto_id']))
                                    <a href="{{ route('auth.storico-interventi') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === ELENCO INTERVENTI === --}}
    <div class="row">
        <div class="col-12">
            @if(isset($interventi) && $interventi->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>
                                Elenco Interventi 
                                <span class="badge bg-secondary">{{ $interventi->total() }}</span>
                            </h5>
                            
                            {{-- Info paginazione --}}
                            @if($interventi->hasPages())
                                <small class="text-muted">
                                    Pagina {{ $interventi->currentPage() }} di {{ $interventi->lastPage() }}
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        
                        {{-- === TABELLA INTERVENTI === --}}
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" width="15%">Data/Ora</th>
                                        <th scope="col" width="25%">Prodotto</th>
                                        <th scope="col" width="30%">Problema</th>
                                        <th scope="col" width="15%">Gravità</th>
                                        <th scope="col" width="15%">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop attraverso tutti gli interventi --}}
                                    @foreach($interventi as $intervento)
                                        <tr>
                                            {{-- Data e ora dell'intervento --}}
                                            <td>
                                                <div class="small">
                                                    <div class="fw-medium">
                                                        {{ $intervento->updated_at->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-muted">
                                                        {{ $intervento->updated_at->format('H:i') }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $intervento->updated_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- Informazioni prodotto --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- Immagine prodotto se disponibile --}}
                                                    @if($intervento->prodotto->foto)
                                                        <img src="{{ $intervento->prodotto->foto_url }}" 
                                                             alt="{{ $intervento->prodotto->nome }}"
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    
                                                    <div>
                                                        <div class="fw-medium">{{ $intervento->prodotto->nome }}</div>
                                                        @if($intervento->prodotto->modello)
                                                            <div class="small text-muted">{{ $intervento->prodotto->modello }}</div>
                                                        @endif
                                                        <span class="badge bg-secondary small">{{ $intervento->prodotto->categoria }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- Descrizione problema e soluzione --}}
                                            <td>
                                                <div>
                                                    <div class="fw-medium mb-1">{{ $intervento->titolo ?? 'Intervento Tecnico' }}</div>
                                                    <div class="small text-muted mb-2" style="max-height: 60px; overflow: hidden;">
                                                        {{ Str::limit($intervento->descrizione, 80) }}
                                                    </div>
                                                    {{-- Preview soluzione --}}
                                                    @if($intervento->soluzione)
                                                        <div class="small">
                                                            <i class="bi bi-check-circle text-success me-1"></i>
                                                            <span class="text-success">{{ Str::limit($intervento->soluzione, 50) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            {{-- Badge gravità --}}
                                            <td>
                                                @php
                                                    $gravitaColors = [
                                                        'bassa' => 'success',
                                                        'media' => 'warning',
                                                        'alta' => 'danger',
                                                        'critica' => 'dark'
                                                    ];
                                                    $gravitaIcons = [
                                                        'bassa' => 'check-circle',
                                                        'media' => 'exclamation-triangle',
                                                        'alta' => 'exclamation-diamond',
                                                        'critica' => 'x-octagon'
                                                    ];
                                                @endphp
                                                
                                                <span class="badge bg-{{ $gravitaColors[$intervento->gravita] ?? 'secondary' }} d-flex align-items-center">
                                                    <i class="bi bi-{{ $gravitaIcons[$intervento->gravita] ?? 'circle' }} me-1"></i>
                                                    {{ ucfirst($intervento->gravita ?? 'N/D') }}
                                                </span>
                                                
                                                {{-- Informazioni aggiuntive --}}
                                                @if($intervento->tempo_stimato)
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-clock me-1"></i>{{ $intervento->tempo_stimato }}min
                                                    </div>
                                                @endif
                                            </td>
                                            
                                            {{-- Azioni disponibili --}}
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm" role="group">
                                                    {{-- Visualizza dettagli malfunzionamento --}}
                                                    <a href="{{ route('malfunzionamenti.show', [$intervento->prodotto, $intervento]) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i>Dettagli
                                                    </a>
                                                    
                                                    {{-- Vedi prodotto - ROUTING INTELLIGENTE BASATO SU AUTENTICAZIONE --}}
                                                    @auth
                                                        @if(auth()->user()->canViewMalfunzionamenti())
                                                            {{-- Per tecnici: vista completa con malfunzionamenti --}}
                                                            <a href="{{ route('prodotti.completo.show', $intervento->prodotto) }}" 
                                                               class="btn btn-outline-info btn-sm">
                                                                <i class="bi bi-box me-1"></i>Vista Tecnica
                                                            </a>
                                                        @else
                                                            {{-- Per utenti autenticati ma senza permessi: vista pubblica --}}
                                                            <a href="{{ route('prodotti.show', $intervento->prodotto) }}" 
                                                               class="btn btn-outline-info btn-sm">
                                                                <i class="bi bi-box me-1"></i>Prodotto
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{-- Per utenti non autenticati: vista pubblica --}}
                                                        <a href="{{ route('prodotti.show', $intervento->prodotto) }}" 
                                                           class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-box me-1"></i>Prodotto
                                                        </a>
                                                    @endauth
                                                    
                                                    {{-- Modifica (solo per staff/admin) --}}
                                                    @if(auth()->user()->isStaff() || auth()->user()->isAdmin())
                                                        <a href="{{ route('malfunzionamenti.edit', $intervento) }}" 
                                                           class="btn btn-outline-warning btn-sm">
                                                            <i class="bi bi-pencil me-1"></i>Modifica
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    {{-- === PAGINAZIONE === --}}
                    @if($interventi->hasPages())
                        <div class="card-footer">
                            {{ $interventi->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            @else
                {{-- === NESSUN INTERVENTO TROVATO === --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-search display-1 text-muted"></i>
                        </div>
                        <h4 class="text-muted">Nessun intervento trovato</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                Non ci sono interventi che corrispondono ai filtri selezionati.<br>
                                Prova a modificare i criteri di ricerca.
                            @else
                                Non sono ancora stati registrati interventi tecnici.
                            @endif
                        </p>
                        
                        {{-- Pulsanti azioni --}}
                        <div class="d-flex justify-content-center gap-3">
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                <a href="{{ route('auth.storico-interventi') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                                </a>
                            @endif
                            
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-tools me-1"></i>Vai al Catalogo Tecnico
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- === SEZIONE STATISTICHE AGGIUNTIVE === --}}
    @if(isset($statisticheStorico) && (isset($statisticheStorico['prodotti_problematici']) || isset($statisticheStorico['per_gravita'])))
        <div class="row mt-5">
            
            {{-- === DISTRIBUZIONE PER GRAVITÀ === --}}
            @if(isset($statisticheStorico['per_gravita']) && count($statisticheStorico['per_gravita']) > 0)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-pie-chart text-primary me-2"></i>
                                Distribuzione per Gravità
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($statisticheStorico['per_gravita'] as $gravita => $count)
                                @php
                                    $percentage = $statisticheStorico['totale_interventi'] > 0 
                                        ? round(($count / $statisticheStorico['totale_interventi']) * 100, 1) 
                                        : 0;
                                    $color = match($gravita) {
                                        'bassa' => 'success',
                                        'media' => 'warning', 
                                        'alta' => 'danger',
                                        'critica' => 'dark',
                                        default => 'secondary'
                                    };
                                @endphp
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-medium">{{ ucfirst($gravita) }}</span>
                                        <span class="text-muted">{{ $count }} ({{ $percentage }}%)</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $color }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- === TOP PRODOTTI PROBLEMATICI === --}}
            @if(isset($statisticheStorico['prodotti_problematici']) && $statisticheStorico['prodotti_problematici']->count() > 0)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Prodotti Più Problematici
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($statisticheStorico['prodotti_problematici'] as $prodotto)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $prodotto->nome }}</div>
                                        @if($prodotto->modello)
                                            <div class="small text-muted">{{ $prodotto->modello }}</div>
                                        @endif
                                        <span class="badge bg-secondary small">{{ $prodotto->categoria }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger">{{ $prodotto->malfunzionamenti_count }} problemi</span>
                                        <div class="mt-1">
                                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- === TOP CATEGORIE PROBLEMATICHE === --}}
            @if(isset($statisticheStorico['categorie_problematiche']) && count($statisticheStorico['categorie_problematiche']) > 0)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-tags text-info me-2"></i>
                                Categorie con Più Problemi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($statisticheStorico['categorie_problematiche'] as $categoria => $count)
                                    <div class="col-md-4 col-lg-3">
                                        <a href="{{ route('auth.storico-interventi', ['categoria' => $categoria]) }}" 
                                           class="btn btn-outline-info w-100 d-flex justify-content-between align-items-center">
                                            <span>{{ $categoria }}</span>
                                            <span class="badge bg-info">{{ $count }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- === SEZIONE INFORMAZIONI AGGIUNTIVE === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sullo Storico Interventi
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Come Leggere lo Storico</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-clock text-primary me-2"></i>Data/ora dell'ultimo aggiornamento</li>
                                <li><i class="bi bi-box text-primary me-2"></i>Prodotto interessato dal problema</li>
                                <li><i class="bi bi-exclamation-triangle text-primary me-2"></i>Livello di gravità del malfunzionamento</li>
                                <li><i class="bi bi-check-circle text-primary me-2"></i>Soluzione tecnica applicata</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6>Filtri Disponibili</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-search text-info me-2"></i>Ricerca per testo libero</li>
                                <li><i class="bi bi-calendar3 text-info me-2"></i>Filtro per periodo temporale</li>
                                <li><i class="bi bi-exclamation-triangle text-info me-2"></i>Filtro per livello di gravità</li>
                                <li><i class="bi bi-tags text-info me-2"></i>Filtro per categoria prodotto</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6>Livelli di Gravità</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge bg-success me-2">Bassa</span>Problemi minori, facili da risolvere</li>
                                <li><span class="badge bg-warning me-2">Media</span>Problemi che richiedono competenza tecnica</li>
                                <li><span class="badge bg-danger me-2">Alta</span>Problemi complessi, tempo prolungato</li>
                                <li><span class="badge bg-dark me-2">Critica</span>Problemi che bloccano il funzionamento</li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- Sezione specifica per il ruolo utente --}}
                    @if($user->isTecnico())
                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-info-circle me-2"></i>Informazioni per Tecnici</h6>
                            <p class="mb-0">
                                Come tecnico, puoi consultare lo storico per identificare pattern ricorrenti nei malfunzionamenti
                                e trovare soluzioni già testate per problemi simili. Utilizza i filtri per trovare rapidamente
                                interventi specifici per categoria di prodotto o livello di gravità.
                            </p>
                        </div>
                    @elseif($user->isStaff())
                        <div class="alert alert-warning mt-3">
                            <h6><i class="bi bi-briefcase me-2"></i>Informazioni per Staff</h6>
                            <p class="mb-0">
                                Come membro dello staff, visualizzi solo gli interventi sui prodotti che gestisci.
                                Utilizza questo storico per monitorare la qualità delle soluzioni e identificare
                                prodotti che necessitano di miglioramenti o aggiornamenti della documentazione tecnica.
                            </p>
                        </div>
                    @elseif($user->isAdmin())
                        <div class="alert alert-danger mt-3">
                            <h6><i class="bi bi-shield-check me-2"></i>Vista Amministratore</h6>
                            <p class="mb-0">
                                Come amministratore, hai accesso completo a tutti gli interventi del sistema.
                                Utilizza queste informazioni per analizzare le performance del servizio assistenza,
                                identificare aree di miglioramento e allocare risorse dove necessario.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === SEZIONE JAVASCRIPT === --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Log inizializzazione pagina
    console.log('Storico Interventi caricato per utente {{ $user->username }}');
    console.log('Livello accesso: {{ $user->livello_accesso }}');
    console.log('Interventi trovati: {{ isset($interventi) ? $interventi->count() : 0 }}');
    
    // === GESTIONE FORM FILTRI ===
    const form = $('form');
    const searchInput = $('#search');
    const periodoSelect = $('#periodo');
    const gravitaSelect = $('#gravita');
    const categoriaSelect = $('#categoria');
    
    // Validazione form prima del submit
    form.on('submit', function(e) {
        const hasFilters = searchInput.val().trim() || 
                          periodoSelect.val() || 
                          gravitaSelect.val() || 
                          categoriaSelect.val();
        
        if (!hasFilters) {
            // Se non ci sono filtri, permette la ricerca (mostra tutti)
            console.log('Ricerca senza filtri - mostra tutti gli interventi');
        } else {
            console.log('Ricerca con filtri:', {
                search: searchInput.val(),
                periodo: periodoSelect.val(),
                gravita: gravitaSelect.val(),
                categoria: categoriaSelect.val()
            });
        }
    });
    
    // === RICERCA IN TEMPO REALE (OPZIONALE) ===
    let searchTimer;
    searchInput.on('input', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val().trim();
        
        // Mostra suggerimenti se il termine è abbastanza lungo
        if (searchTerm.length >= 3) {
            searchTimer = setTimeout(function() {
                console.log('Ricerca per:', searchTerm);
                // Qui potresti implementare suggerimenti AJAX
                showSearchSuggestions(searchTerm);
            }, 500);
        } else {
            hideSearchSuggestions();
        }
    });
    
    // === GESTIONE FILTRI VELOCI ===
    
    // Filtro rapido per gravità critica
    $(document).on('keypress', function(e) {
        if (e.ctrlKey && e.which === 99) { // Ctrl+C
            gravitaSelect.val('critica').trigger('change');
            form.submit();
        }
    });
    
    // Filtro rapido per periodo (questa settimana)
    $(document).on('keypress', function(e) {
        if (e.ctrlKey && e.which === 119) { // Ctrl+W
            periodoSelect.val('settimana').trigger('change');
            form.submit();
        }
    });
    
    // === TOOLTIPS E INTERFACCIA ===
    
    // Inizializza tutti i tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Evidenziazione righe critiche
    $('tbody tr').each(function() {
        const gravitiaBadge = $(this).find('.badge');
        if (gravitiaBadge.hasClass('bg-dark') || gravitiaBadge.hasClass('bg-danger')) {
            $(this).addClass('table-warning'); // Evidenzia righe critiche/alte
        }
    });
    
    // === FUNZIONI HELPER ===
    
    // Mostra suggerimenti di ricerca (placeholder per funzionalità futura)
    function showSearchSuggestions(term) {
        // Implementazione futura: mostra dropdown con suggerimenti
        console.log('Suggerimenti per:', term);
    }
    
    // Nascondi suggerimenti
    function hideSearchSuggestions() {
        // Implementazione futura: nascondi dropdown suggerimenti
    }
    
    // === GESTIONE RESPONSIVA ===
    
    // Adatta tabella per dispositivi mobili
    function makeTableResponsive() {
        const table = $('.table');
        const windowWidth = $(window).width();
        
        if (windowWidth < 768) {
            // Su mobile, nascondi alcune colonne meno importanti
            table.find('th:nth-child(4), td:nth-child(4)').hide(); // Nascondi gravità
            table.find('th:nth-child(1), td:nth-child(1)').hide(); // Nascondi data dettagliata
        } else {
            // Su desktop, mostra tutte le colonne
            table.find('th, td').show();
        }
    }
    
    // Esegui al caricamento e al ridimensionamento
    makeTableResponsive();
    $(window).resize(makeTableResponsive);
    
    // === REFRESH AUTOMATICO (OPZIONALE) ===
    
    // Aggiorna automaticamente le statistiche ogni 5 minuti
    @if($user->isTecnico() || $user->isAdmin())
        setInterval(function() {
            console.log('Controllo aggiornamenti statistiche...');
            // Implementazione futura per refresh automatico delle statistiche
            // Potresti creare una route API dedicata per questo scopo
        }, 300000); // 5 minuti
    @endif
    
    // === ANALYTICS E TRACKING ===
    
    // Traccia utilizzo filtri
    $('select, input[type="text"]').on('change', function() {
        const filterName = $(this).attr('name');
        const filterValue = $(this).val();
        console.log('Filtro utilizzato:', filterName, '=', filterValue);
    });
    
    // Traccia click su dettagli interventi
    $('a[href*="/malfunzionamenti/"]').on('click', function() {
        const interventoId = $(this).closest('tr').index();
        console.log('Dettagli intervento visualizzati:', interventoId);
    });
    
    // === NOTIFICAZIONI PER INTERVENTI CRITICI ===
    
    // Mostra alert se ci sono molti interventi critici
    @if(isset($statisticheStorico['per_gravita']['critica']) && $statisticheStorico['per_gravita']['critica'] > 10)
        setTimeout(function() {
            const message = 'Attenzione: {{ $statisticheStorico["per_gravita"]["critica"] }} interventi critici registrati!';
            showAlert(message, 'warning');
        }, 2000);
    @endif
});

// === FUNZIONI GLOBALI ===

/**
 * Mostra un alert personalizzato
 */
function showAlert(message, type = 'info') {
    const alertClass = `alert-${type}`;
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    
    // Rimuovi automaticamente dopo 5 secondi
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

/**
 * Esporta i dati dello storico (funzionalità futura)
 */
function exportData(format = 'csv') {
    console.log('Esportazione dati in formato:', format);
    // Implementazione futura per export CSV/Excel
    alert('Funzionalità di esportazione in sviluppo');
}

/**
 * Stampa la pagina corrente
 */
function printPage() {
    // Nascondi elementi non necessari per la stampa
    $('.btn, .card-footer, .alert').hide();
    
    // Stampa
    window.print();
    
    // Ripristina elementi nascosti
    $('.btn, .card-footer, .alert').show();
}
</script>
@endpush

{{-- === SEZIONE CSS PERSONALIZZATO === --}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI PER STORICO === */

/* Card hover effects */
.card {
    transition: box-shadow 0.2s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

/* === STILI TABELLA === */

/* Righe hover più evidenti */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

/* Celle con contenuto troncato */
.table td {
    max-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Eccezioni per celle che possono andare a capo */
.table td:nth-child(3) { /* Colonna descrizione */
    white-space: normal;
    word-wrap: break-word;
}

/* === STILI BADGE GRAVITÀ === */

/* Animazione pulse per gravità critica */
.badge.bg-dark {
    animation: pulse-critical 2s infinite;
}

@keyframes pulse-critical {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Badge più grandi per migliore leggibilità */
.badge.d-flex {
    padding: 0.5em 0.75em;
}

/* === PROGRESS BAR PERSONALIZZATE === */

/* Animazione per le progress bar */
.progress-bar {
    transition: width 0.8s ease-in-out;
}

/* === STILI RESPONSIVI === */

/* Ottimizzazioni per tablet */
@media (max-width: 992px) {
    .btn-group-vertical .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table td, .table th {
        padding: 0.5rem 0.25rem;
    }
}

/* Ottimizzazioni per mobile */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    /* Nascondi colonne meno importanti */
    .table th:nth-child(1),
    .table td:nth-child(1),
    .table th:nth-child(4), 
    .table td:nth-child(4) {
        display: none;
    }
    
    /* Stack dei filtri in verticale */
    .row.g-3 > .col-md-2,
    .row.g-3 > .col-md-4 {
        margin-bottom: 1rem;
    }
    
    /* Pulsanti azione più compatti */
    .btn-group-vertical {
        flex-direction: row;
        gap: 0.25rem;
    }
    
    /* Card statistiche più compatte */
    .card-body .fs-1 {
        font-size: 2rem !important;
    }
}

/* === STILI STAMPA === */
@media print {
    /* Nascondi elementi non necessari per la stampa */
    .btn, .card-footer, .alert, .breadcrumb {
        display: none !important;
    }
    
    /* Ottimizza colori per stampa */
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background-color: transparent !important;
    }
    
    /* Tabella con bordi per stampa */
    .table th, .table td {
        border: 1px solid #000 !important;
    }
}

/* === ANIMAZIONI PERSONALIZZATE === */

/* Fade-in per nuove righe caricate dinamicamente */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.table tbody tr {
    animation: fadeIn 0.3s ease-in-out;
}

/* Loading stato per form */
.form-control:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

/* === STILI ALERT PERSONALIZZATI === */

/* Alert posizionato in alto a destra */
.alert.position-fixed {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2);
    border: none;
}

/* === STILI ACCESSIBILITY === */

/* Focus visibile per navigazione da tastiera */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
}

/* Contrasto migliorato per testi muted */
.text-muted {
    color: #6c757d !important;
}

/* === UTILITIES PERSONALIZZATE === */

/* Classe helper per testo troncato con tooltip */
.text-truncate-tooltip {
    cursor: help;
}

/* Spaziatura consistente per badge inline */
.badge + .badge {
    margin-left: 0.25rem;
}
</style>
@endpush