{{-- 
    Vista Storico Interventi Tecnici - PAGINAZIONE CORRETTA
    File: resources/views/auth/storico-interventi.blade.php
    
    CORREZIONE: Paginazione e layout sistemi
--}}

@extends('layouts.app')

@section('title', 'Storico Interventi Tecnici')

@section('content')
<div class="container-fluid mt-4">
    
    {{-- Header della pagina --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Storico Interventi Tecnici
                    </h1>
                    <p class="text-muted mb-0">
                        Cronologia completa degli interventi e soluzioni
                        @if(isset($user))
                            @if($user->isTecnico())
                                <span class="badge bg-info ms-2">Vista Tecnico</span>
                            @elseif($user->isStaff())
                                <span class="badge bg-warning text-dark ms-2">I Tuoi Prodotti</span>
                            @elseif($user->isAdmin())
                                <span class="badge bg-danger ms-2">Vista Admin</span>
                            @endif
                        @endif
                    </p>
                </div>
                <div>
                    @if(isset($user))
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiche rapide --}}
    @if(isset($statisticheStorico))
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-list-check fs-2 me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $statisticheStorico['totale_interventi'] ?? 0 }}</h4>
                                <small>Interventi Totali</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-day fs-2 me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_oggi'] ?? 0 }}</h4>
                                <small>Oggi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-week fs-2 me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_settimana'] ?? 0 }}</h4>
                                <small>Settimana</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-month fs-2 me-3"></i>
                            <div>
                                <h4 class="mb-0">{{ $statisticheStorico['interventi_mese'] ?? 0 }}</h4>
                                <small>Mese</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtri di ricerca --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filtri di Ricerca
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('auth.storico-interventi') }}">
                        <div class="row g-3">
                            
                            {{-- Ricerca testuale --}}
                            <div class="col-md-3">
                                <label for="search" class="form-label">Ricerca</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Descrizione o soluzione...">
                            </div>
                            
                            {{-- Periodo --}}
                            <div class="col-md-2">
                                <label for="periodo" class="form-label">Periodo</label>
                                <select name="periodo" id="periodo" class="form-select">
                                    <option value="">Tutti</option>
                                    <option value="oggi" {{ request('periodo') == 'oggi' ? 'selected' : '' }}>Oggi</option>
                                    <option value="settimana" {{ request('periodo') == 'settimana' ? 'selected' : '' }}>Settimana</option>
                                    <option value="mese" {{ request('periodo') == 'mese' ? 'selected' : '' }}>Mese</option>
                                    <option value="trimestre" {{ request('periodo') == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                                </select>
                            </div>
                            
                            {{-- Gravità --}}
                            <div class="col-md-2">
                                <label for="gravita" class="form-label">Gravità</label>
                                <select name="gravita" id="gravita" class="form-select">
                                    <option value="">Tutte</option>
                                    <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>Bassa</option>
                                    <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>Critica</option>
                                </select>
                            </div>
                            
                            {{-- Categoria --}}
                            <div class="col-md-2">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select name="categoria" id="categoria" class="form-select">
                                    <option value="">Tutte</option>
                                    @if(isset($categorie))
                                        @foreach($categorie as $cat)
                                            <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            {{-- Pulsanti --}}
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="bi bi-search me-1"></i>Cerca
                                    </button>
                                    @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                        <a href="{{ route('auth.storico-interventi') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Elenco interventi --}}
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
                            
                            {{-- Info paginazione corretta --}}
                            @if($interventi->hasPages())
                                <small class="text-muted">
                                    Pagina {{ $interventi->currentPage() }} di {{ $interventi->lastPage() }}
                                    ({{ $interventi->firstItem() }}-{{ $interventi->lastItem() }} di {{ $interventi->total() }})
                                </small>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Tabella responsive --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="12%">Data</th>
                                    <th scope="col" width="28%">Prodotto</th>
                                    <th scope="col" width="35%">Problema</th>
                                    <th scope="col" width="12%">Gravità</th>
                                    <th scope="col" width="13%">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interventi as $intervento)
                                    <tr>
                                        {{-- Data compatta --}}
                                        <td>
                                            <div class="small">
                                                <div class="fw-medium">{{ $intervento->updated_at->format('d/m/Y') }}</div>
                                                <div class="text-muted">{{ $intervento->updated_at->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        
                                        {{-- Prodotto --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($intervento->prodotto->foto)
                                                    <img src="{{ asset('storage/' . $intervento->prodotto->foto) }}" 
                                                         alt="{{ $intervento->prodotto->nome }}"
                                                         class="rounded me-2"
                                                         style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                         style="width: 32px; height: 32px;">
                                                        <i class="bi bi-image text-muted small"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium small">{{ $intervento->prodotto->nome }}</div>
                                                    @if($intervento->prodotto->modello)
                                                        <div class="text-muted small">{{ $intervento->prodotto->modello }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {{-- Problema e soluzione --}}
                                        <td>
                                            <div class="small">
                                                <div class="fw-medium mb-1">{{ Str::limit($intervento->titolo ?? 'Intervento', 40) }}</div>
                                                <div class="text-muted">{{ Str::limit($intervento->descrizione, 60) }}</div>
                                                @if($intervento->soluzione)
                                                    <div class="text-success mt-1">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        {{ Str::limit($intervento->soluzione, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        {{-- Badge gravità --}}
                                        <td>
                                            @php
                                                $gravitaConfig = [
                                                    'bassa' => ['color' => 'success', 'icon' => 'check-circle'],
                                                    'media' => ['color' => 'warning', 'icon' => 'exclamation-triangle'],
                                                    'alta' => ['color' => 'danger', 'icon' => 'exclamation-diamond'],
                                                    'critica' => ['color' => 'dark', 'icon' => 'x-octagon']
                                                ];
                                                $config = $gravitaConfig[$intervento->gravita] ?? ['color' => 'secondary', 'icon' => 'circle'];
                                            @endphp
                                            
                                            <span class="badge bg-{{ $config['color'] }} small">
                                                <i class="bi bi-{{ $config['icon'] }} me-1"></i>
                                                {{ ucfirst($intervento->gravita ?? 'N/D') }}
                                            </span>
                                            
                                            @if($intervento->tempo_stimato)
                                                <div class="text-muted small mt-1">
                                                    <i class="bi bi-clock me-1"></i>{{ $intervento->tempo_stimato }}min
                                                </div>
                                            @endif
                                        </td>
                                        
                                        {{-- Azioni --}}
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm">
                                                {{-- Visualizza dettagli --}}
                                                <a href="{{ route('malfunzionamenti.show', [$intervento->prodotto, $intervento]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Dettagli
                                                </a>
                                                
                                                {{-- Vista prodotto --}}
                                                @auth
                                                    @if(auth()->user()->canViewMalfunzionamenti())
                                                        <a href="{{ route('prodotti.completo.show', $intervento->prodotto) }}" 
                                                           class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-box me-1"></i>Prodotto
                                                        </a>
                                                    @else
                                                        <a href="{{ route('prodotti.pubblico.show', $intervento->prodotto) }}" 
                                                           class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-box me-1"></i>Prodotto
                                                        </a>
                                                    @endif
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- PAGINAZIONE CORRETTA --}}
                    @if($interventi->hasPages())
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- Info risultati --}}
                                <div class="text-muted small">
                                    Visualizzazione {{ $interventi->firstItem() }}-{{ $interventi->lastItem() }} 
                                    di {{ $interventi->total() }} risultati
                                </div>
                                
                                {{-- Link di paginazione che mantengono i filtri --}}
                                <div>
                                    {{ $interventi->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            @else
                {{-- Nessun intervento trovato --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search display-4 text-muted mb-3"></i>
                        <h4 class="text-muted">Nessun intervento trovato</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                Non ci sono interventi che corrispondono ai filtri selezionati.
                            @else
                                Non sono ancora stati registrati interventi tecnici.
                            @endif
                        </p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            @if(request()->hasAny(['search', 'periodo', 'gravita', 'categoria']))
                                <a href="{{ route('auth.storico-interventi') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                                </a>
                            @endif
                            
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-tools me-1"></i>Catalogo Tecnico
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Statistiche aggiuntive (se disponibili) --}}
    @if(isset($statisticheStorico) && isset($statisticheStorico['per_gravita']))
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-pie-chart text-primary me-2"></i>
                            Distribuzione per Gravità
                        </h6>
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
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-medium">{{ ucfirst($gravita) }}</small>
                                    <small class="text-muted">{{ $count }} ({{ $percentage }}%)</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $color }}" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            @if(isset($statisticheStorico['prodotti_problematici']))
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Prodotti con Più Problemi
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($statisticheStorico['prodotti_problematici']->take(5) as $prodotto)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-medium small">{{ Str::limit($prodotto->nome, 25) }}</div>
                                        <span class="badge bg-secondary small">{{ $prodotto->categoria }}</span>
                                    </div>
                                    <span class="badge bg-danger">{{ $prodotto->malfunzionamenti_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

{{-- CSS ottimizzato --}}
@push('styles')
<style>
/* Tabella compatta */
.table td {
    padding: 0.5rem 0.75rem;
    vertical-align: middle;
}

.table th {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Hover effect per righe */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

/* Badge gravità con dimensioni coerenti */
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem;
}

/* Bottoni gruppo compatti */
.btn-group-vertical .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Responsive per mobile */
@media (max-width: 768px) {
    .table th:nth-child(1),
    .table td:nth-child(1) {
        display: none; /* Nascondi colonna data su mobile */
    }
    
    .table th:nth-child(4),
    .table td:nth-child(4) {
        display: none; /* Nascondi gravità su mobile */
    }
    
    .btn-group-vertical {
        flex-direction: row;
        gap: 0.25rem;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}

/* Paginazione Bootstrap custom */
.pagination {
    margin: 0;
}

.page-link {
    color: #0d6efd;
    border-color: #dee2e6;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #0a58ca;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Animazione per nuove righe */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table tbody tr {
    animation: fadeInUp 0.3s ease-out;
}

/* Progress bar personalizzate */
.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endpush

{{-- JavaScript ottimizzato --}}
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