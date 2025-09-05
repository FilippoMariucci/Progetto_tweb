{{-- 
    Vista per la ricerca globale dei malfunzionamenti
    Accessibile solo a tecnici (livello 2+) e staff (livello 3+)
    Percorso: resources/views/malfunzionamenti/ricerca.blade.php
    
    AGGIORNATA: Include immagini dei prodotti per ogni risultato
--}}

@extends('layouts.app')

@section('title', 'Ricerca Malfunzionamenti')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER COMPATTO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-search text-warning me-2"></i>
                Ricerca Malfunzionamenti
            </h2>
            <p class="text-muted small mb-0">
                Cerca soluzioni ai problemi tecnici in tutto il sistema
            </p>
        </div>
        <div class="btn-group btn-group-sm">
            @if(auth()->user()->isTecnico())
                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @elseif(auth()->user()->isStaff())
                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            @endif
        </div>
    </div>

    {{-- Breadcrumb compatto --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>
            @if(auth()->user()->isTecnico())
                <li class="breadcrumb-item">
                    <a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a>
                </li>
            @elseif(auth()->user()->isStaff())
                <li class="breadcrumb-item">
                    <a href="{{ route('staff.dashboard') }}">Dashboard Staff</a>
                </li>
            @endif
            <li class="breadcrumb-item active">Ricerca Malfunzionamenti</li>
        </ol>
    </nav>

    {{-- === FORM RICERCA COMPATTO === --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filtri di Ricerca
                    </h6>
                </div>
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}" class="row g-3">
                        {{-- Campo ricerca principale --}}
                        <div class="col-lg-4 col-md-6">
                            <label for="q" class="form-label small fw-semibold">Cerca problema:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   id="q" 
                                   name="q" 
                                   placeholder="es: non si accende, perdita acqua..."
                                   value="{{ request('q') }}"
                                   autocomplete="off">
                        </div>
                        
                        {{-- Gravità --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="gravita" class="form-label small fw-semibold">Gravità:</label>
                            <select class="form-select form-select-sm" id="gravita" name="gravita">
                                <option value="">Tutte</option>
                                <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>🔴 Critica</option>
                                <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>🟠 Alta</option>
                                <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>🟡 Media</option>
                                <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>🟢 Bassa</option>
                            </select>
                        </div>
                        
                        {{-- Difficoltà --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="difficolta" class="form-label small fw-semibold">Difficoltà:</label>
                            <select class="form-select form-select-sm" id="difficolta" name="difficolta">
                                <option value="">Tutte</option>
                                <option value="facile" {{ request('difficolta') == 'facile' ? 'selected' : '' }}>✅ Facile</option>
                                <option value="media" {{ request('difficolta') == 'media' ? 'selected' : '' }}>⚠️ Media</option>
                                <option value="difficile" {{ request('difficolta') == 'difficile' ? 'selected' : '' }}>🔧 Difficile</option>
                                <option value="esperto" {{ request('difficolta') == 'esperto' ? 'selected' : '' }}>👨‍🔬 Esperto</option>
                            </select>
                        </div>
                        
                        {{-- Categoria --}}
                        <div class="col-lg-2 col-md-6">
                            <label for="categoria_prodotto" class="form-label small fw-semibold">Categoria:</label>
                            <select class="form-select form-select-sm" id="categoria_prodotto" name="categoria_prodotto">
                                <option value="">Tutte</option>
                                @foreach($categorieProdotti as $valore => $etichetta)
                                    <option value="{{ $valore }}" {{ request('categoria_prodotto') == $valore ? 'selected' : '' }}>
                                        {{ $etichetta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Pulsanti --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-warning btn-sm flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RICERCA COMPATTE === --}}
    @if(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
        <div class="row g-2 mb-3">
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-search text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['totale_trovati'] }}</h6>
                        <small class="text-muted">Risultati Trovati</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['critici'] }}</h6>
                        <small class="text-muted">Critici</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-2">
                        <i class="bi bi-exclamation-circle text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 mt-1">{{ $stats['alta_priorita'] }}</h6>
                        <small class="text-muted">Alta Priorità</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- === RISULTATI CON IMMAGINI === --}}
    <div class="row">
        <div class="col-12">
            @if($malfunzionamenti->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check text-success me-1"></i>
                            Risultati della Ricerca ({{ $malfunzionamenti->total() }} trovati)
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($malfunzionamenti as $malfunzionamento)
                            <div class="border-bottom p-3 hover-light">
                                <div class="row align-items-start">
                                    
                                    {{-- === IMMAGINE PRODOTTO === --}}
                                    <div class="col-lg-2 col-md-3 col-sm-4 mb-3 mb-lg-0">
                                        <div class="position-relative">
                                            @if($malfunzionamento->prodotto->foto)
                                                <img src="{{ asset('storage/' . $malfunzionamento->prodotto->foto) }}" 
                                                     class="img-fluid product-thumb rounded shadow-sm" 
                                                     alt="{{ $malfunzionamento->prodotto->nome }}"
                                                     style="width: 100%; height: 120px; object-fit: contain; background-color: #f8f9fa;">
                                            @else
                                                <div class="product-thumb-placeholder rounded shadow-sm d-flex align-items-center justify-content-center bg-light" 
                                                     style="width: 100%; height: 120px;">
                                                    <div class="text-center">
                                                        <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                        <div class="small text-muted mt-1">{{ Str::limit($malfunzionamento->prodotto->nome, 15) }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            {{-- Badge categoria sul prodotto --}}
                                            <div class="position-absolute top-0 end-0 m-1">
                                                <span class="badge bg-secondary small">
                                                    {{ ucfirst(str_replace('_', ' ', $malfunzionamento->prodotto->categoria)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- === CONTENUTO PRINCIPALE === --}}
                                    <div class="col-lg-7 col-md-6 col-sm-8 mb-3 mb-lg-0">
                                        {{-- Titolo con badge gravità --}}
                                        <h6 class="mb-2 fw-bold">
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="text-decoration-none text-dark">
                                                {{ $malfunzionamento->titolo }}
                                            </a>
                                            
                                            {{-- Badge gravità compatto --}}
                                            @php
                                                $badges = [
                                                    'critica' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'bassa' => 'success'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $badges[$malfunzionamento->gravita] ?? 'secondary' }} small ms-1">
                                                {{ ucfirst($malfunzionamento->gravita) }}
                                            </span>
                                        </h6>
                                        
                                        {{-- Descrizione breve --}}
                                        <p class="text-muted small mb-2">
                                            {{ Str::limit($malfunzionamento->descrizione, 120, '...') }}
                                        </p>
                                        
                                        {{-- Info prodotto compatte --}}
                                        <div class="d-flex align-items-center text-muted small mb-2">
                                            <i class="bi bi-box me-1"></i>
                                            <strong class="me-2">{{ $malfunzionamento->prodotto->nome }}</strong>
                                            @if($malfunzionamento->prodotto->modello)
                                                <span class="me-2">- {{ $malfunzionamento->prodotto->modello }}</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Badge difficoltà e tempo --}}
                                        <div class="d-flex flex-wrap gap-1">
                                            @php
                                                $diffBadges = [
                                                    'facile' => 'success',
                                                    'media' => 'info', 
                                                    'difficile' => 'warning',
                                                    'esperto' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $diffBadges[$malfunzionamento->difficolta] ?? 'secondary' }} small">
                                                {{ ucfirst($malfunzionamento->difficolta) }}
                                            </span>
                                            
                                            @if($malfunzionamento->tempo_stimato)
                                                <span class="badge bg-light text-dark small">
                                                    <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }} min
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- === AZIONI E STATISTICHE === --}}
                                    <div class="col-lg-3 col-md-3 col-12">
                                        {{-- Contatore segnalazioni --}}
                                        <div class="text-center mb-3">
                                            <span class="badge bg-primary" data-segnalazioni-count="{{ $malfunzionamento->id }}">
                                                <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                                            </span>
                                        </div>
                                        
                                        {{-- Pulsanti azione compatti --}}
                                        <div class="d-grid gap-1">
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-warning btn-sm segnala-btn"
                                                    onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                    title="Segnala di aver riscontrato questo problema">
                                                <i class="bi bi-exclamation-circle me-1"></i>Segnala Problema
                                            </button>
                                            
                                            {{-- Link rapido al prodotto --}}
                                            <a href="{{ route('prodotti.completo.show', $malfunzionamento->prodotto) }}" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-box me-1"></i>Vedi Prodotto
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Paginazione compatta --}}
                @if($malfunzionamenti->hasPages())
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="text-center mb-2">
                                <small class="text-muted">
                                    Visualizzati {{ $malfunzionamenti->firstItem() }}-{{ $malfunzionamenti->lastItem() }} 
                                    di {{ $malfunzionamenti->total() }} malfunzionamenti
                                </small>
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $malfunzionamenti->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
                
            @elseif(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
                {{-- Nessun risultato --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-muted opacity-50" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nessun risultato trovato</h5>
                        <p class="text-muted">Prova a modificare i filtri di ricerca o utilizza parole chiave diverse.</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Ricerca
                            </a>
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-collection me-1"></i>Sfoglia Catalogo
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- Stato iniziale con suggerimenti --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-primary opacity-75" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Cerca Malfunzionamenti e Soluzioni</h5>
                        <p class="text-muted mb-4">Utilizza i filtri sopra per trovare rapidamente le soluzioni ai problemi tecnici.</p>
                        
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-start">
                                    <h6 class="fw-semibold mb-3">💡 Suggerimenti per la ricerca:</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Parole chiave specifiche</strong>
                                                    <div class="small text-muted">"non si accende", "perdita", "rumore"</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Combina filtri</strong>
                                                    <div class="small text-muted">Categoria + gravità per risultati precisi</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Emergenze</strong>
                                                    <div class="small text-muted">Parti dalla gravità "Critica"</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong class="small">Filtro categoria</strong>
                                                    <div class="small text-muted">Restringe i risultati per tipo prodotto</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* === STILI PER RICERCA CON IMMAGINI === */

/* Card prodotto base */
.card {
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

/* Immagini prodotto nelle ricerche */
.product-thumb {
    transition: transform 0.3s ease;
    border: 1px solid #e9ecef;
}

.product-thumb:hover {
    transform: scale(1.05);
    border-color: #007bff;
}

.product-thumb-placeholder {
    border: 2px dashed #dee2e6;
    transition: all 0.2s ease;
}

.product-thumb-placeholder:hover {
    border-color: #007bff;
    background-color: #f8f9fa !important;
}

/* Hover effect per risultati */
.hover-light:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Badge più compatti */
.badge.small {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Form controls compatti */
.form-control-sm,
.form-select-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Card body compatto */
.card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card-body.py-3 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}

/* Evidenziazione termini di ricerca */
mark {
    background-color: #fff3cd;
    padding: 0.125em 0.25em;
    border-radius: 0.25rem;
    font-weight: 600;
}

/* Badge gravità con animazioni */
.badge.bg-danger {
    animation: pulse-danger 2s infinite;
}

@keyframes pulse-danger {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

/* Responsive design */
@media (max-width: 768px) {
    .col-lg-2 {
        margin-bottom: 1rem;
    }
    
    .col-lg-3.col-md-3.col-12 {
        border-top: 1px solid #dee2e6;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    
    .product-thumb,
    .product-thumb-placeholder {
        height: 100px !important;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-grid.gap-1 .btn {
        margin-bottom: 0.25rem;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .product-thumb,
    .product-thumb-placeholder {
        height: 80px !important;
    }
    
    .col-lg-2.col-md-3.col-sm-4 {
        flex: 0 0 100px;
        max-width: 100px;
    }
    
    .row.align-items-start {
        align-items: flex-start !important;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-loading::after {
    content: "";
    display: inline-block;
    width: 12px;
    height: 12px;
    margin-left: 6px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #ffc107;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Animazioni */
.btn, .badge, .product-thumb {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Alert personalizzati */
.alert {
    border: none;
    border-radius: 0.5rem;
}

.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
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

/* Focus migliorato */
.form-control:focus,
.form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}
</style>
@endpush


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

// Dati per performance monitoring JS
window.PageData.malfunzionamentiTotal = @json($malfunzionamenti->total());
window.PageData.malfunzionamentiCount = @json($malfunzionamenti->count());
window.PageData.searchActive = @json(request('q') ? true : false);
// Aggiungi altri dati che potrebbero servire...
</script>