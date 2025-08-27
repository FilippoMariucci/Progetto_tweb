{{-- 
    Vista per la ricerca globale dei malfunzionamenti
    Accessibile solo a tecnici (livello 2+) e staff (livello 3+)
    Percorso: resources/views/malfunzionamenti/ricerca.blade.php
--}}

@extends('layouts.app')

@section('title', 'Ricerca Malfunzionamenti')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER RICERCA === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if(auth()->user()->isTecnico())
                            <a href="{{ route('tecnico.dashboard') }}" class="text-decoration-none">
                                <i class="bi bi-house me-1"></i>Dashboard Tecnico
                            </a>
                        @elseif(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="text-decoration-none">
                                <i class="bi bi-house me-1"></i>Dashboard Staff
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                <i class="bi bi-house me-1"></i>Dashboard
                            </a>
                        @endif
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ricerca Malfunzionamenti</li>
                </ol>
            </nav>

            {{-- Titolo principale --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-search text-warning me-2"></i>
                        Ricerca Malfunzionamenti
                    </h1>
                    <p class="text-muted mb-0">
                        Cerca soluzioni ai problemi tecnici in tutto il sistema
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- === FORM DI RICERCA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filtri di Ricerca
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('malfunzionamenti.ricerca') }}">
                        <div class="row g-3">
                            
                            {{-- Campo ricerca principale --}}
                            <div class="col-md-6">
                                <label for="q" class="form-label fw-semibold">Cerca problema:</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="q" 
                                       name="q" 
                                       placeholder="Es: non si accende, perdita acqua, rumore strano..."
                                       value="{{ request('q') }}"
                                       autocomplete="off">
                                <div class="form-text">
                                    Ricerca nel titolo e descrizione dei problemi
                                </div>
                            </div>
                            
                            {{-- Filtro gravità --}}
                            <div class="col-md-3">
                                <label for="gravita" class="form-label fw-semibold">Gravità:</label>
                                <select class="form-select" id="gravita" name="gravita">
                                    <option value="">Tutte le gravità</option>
                                    <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>🔴 Critica</option>
                                    <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>🟠 Alta</option>
                                    <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>🟡 Media</option>
                                    <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>🟢 Bassa</option>
                                </select>
                            </div>
                            
                            {{-- Filtro difficoltà --}}
                            <div class="col-md-3">
                                <label for="difficolta" class="form-label fw-semibold">Difficoltà:</label>
                                <select class="form-select" id="difficolta" name="difficolta">
                                    <option value="">Tutte le difficoltà</option>
                                    <option value="facile" {{ request('difficolta') == 'facile' ? 'selected' : '' }}>✅ Facile</option>
                                    <option value="media" {{ request('difficolta') == 'media' ? 'selected' : '' }}>⚠️ Media</option>
                                    <option value="difficile" {{ request('difficolta') == 'difficile' ? 'selected' : '' }}>🔧 Difficile</option>
                                    <option value="esperto" {{ request('difficolta') == 'esperto' ? 'selected' : '' }}>👨‍🔬 Esperto</option>
                                </select>
                            </div>
                            
                            {{-- Filtro categoria prodotto --}}
                            <div class="col-md-4">
                                <label for="categoria_prodotto" class="form-label fw-semibold">Categoria prodotto:</label>
                                <select class="form-select" id="categoria_prodotto" name="categoria_prodotto">
                                    <option value="">Tutte le categorie</option>
                                    @foreach($categorieProdotti as $valore => $etichetta)
                                        <option value="{{ $valore }}" {{ request('categoria_prodotto') == $valore ? 'selected' : '' }}>
                                            {{ $etichetta }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Filtro prodotto specifico --}}
                            <div class="col-md-4">
                                <label for="prodotto_id" class="form-label fw-semibold">Prodotto specifico:</label>
                                <select class="form-select" id="prodotto_id" name="prodotto_id">
                                    <option value="">Tutti i prodotti</option>
                                    @foreach($prodotti as $prodotto)
                                        <option value="{{ $prodotto->id }}" {{ request('prodotto_id') == $prodotto->id ? 'selected' : '' }}>
                                            {{ $prodotto->nome }}
                                            @if($prodotto->modello) - {{ $prodotto->modello }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Ordinamento --}}
                            <div class="col-md-4">
                                <label for="order" class="form-label fw-semibold">Ordina per:</label>
                                <select class="form-select" id="order" name="order">
                                    <option value="gravita" {{ request('order') == 'gravita' ? 'selected' : '' }}>Gravità (critica prima)</option>
                                    <option value="frequenza" {{ request('order') == 'frequenza' ? 'selected' : '' }}>Più segnalati</option>
                                    <option value="recente" {{ request('order') == 'recente' ? 'selected' : '' }}>Più recenti</option>
                                    <option value="difficolta" {{ request('order') == 'difficolta' ? 'selected' : '' }}>Difficoltà</option>
                                    <option value="alfabetico" {{ request('order') == 'alfabetico' ? 'selected' : '' }}>Alfabetico</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Pulsanti azione --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-search me-1"></i>Cerca Soluzioni
                            </button>
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filtri
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RICERCA === --}}
    @if(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom border-info">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-search text-primary fs-1"></i>
                                <h4 class="mt-2 mb-1">{{ $stats['totale_trovati'] }}</h4>
                                <small class="text-muted">Risultati Trovati</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                <h4 class="mt-2 mb-1">{{ $stats['critici'] }}</h4>
                                <small class="text-muted">Problemi Critici</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-exclamation-circle text-warning fs-1"></i>
                                <h4 class="mt-2 mb-1">{{ $stats['alta_priorita'] }}</h4>
                                <small class="text-muted">Alta Priorità</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === RISULTATI RICERCA === --}}
    <div class="row">
        <div class="col-12">
            @if($malfunzionamenti->count() > 0)
                {{-- Lista risultati --}}
                <div class="card card-custom">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check text-success me-2"></i>
                            Risultati della Ricerca ({{ $malfunzionamenti->total() }} trovati)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($malfunzionamenti as $malfunzionamento)
                            <div class="border-bottom p-4 hover-light">
                                <div class="row">
                                    <div class="col-lg-8">
                                        {{-- Titolo e descrizione --}}
                                        <h5 class="mb-2">
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="text-decoration-none text-dark">
                                                {{ $malfunzionamento->titolo }}
                                            </a>
                                            
                                            {{-- Badge gravità --}}
                                            @php
                                                $badges = [
                                                    'critica' => 'danger',
                                                    'alta' => 'warning',
                                                    'media' => 'info',
                                                    'bassa' => 'secondary'
                                                ];
                                                $icons = [
                                                    'critica' => 'exclamation-triangle-fill',
                                                    'alta' => 'exclamation-circle',
                                                    'media' => 'info-circle',
                                                    'bassa' => 'check-circle'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $badges[$malfunzionamento->gravita] ?? 'secondary' }} ms-2">
                                                <i class="bi bi-{{ $icons[$malfunzionamento->gravita] ?? 'circle' }} me-1"></i>
                                                {{ ucfirst($malfunzionamento->gravita) }}
                                            </span>
                                        </h5>
                                        
                                        <p class="text-muted mb-2">{{ Str::limit($malfunzionamento->descrizione, 150) }}</p>
                                        
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-box me-1"></i>
                                            <strong>{{ $malfunzionamento->prodotto->nome }}</strong>
                                            @if($malfunzionamento->prodotto->modello)
                                                - {{ $malfunzionamento->prodotto->modello }}
                                            @endif
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-tag me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $malfunzionamento->prodotto->categoria)) }}
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 text-end">
                                        {{-- Statistiche e azioni --}}
                                        <div class="mb-3">
                                            <span class="badge bg-primary me-1" data-segnalazioni-count="{{ $malfunzionamento->id }}">
                                                <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                                            </span>
                                            
                                            @php
                                                $diffBadges = [
                                                    'facile' => 'success',
                                                    'media' => 'info', 
                                                    'difficile' => 'warning',
                                                    'esperto' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $diffBadges[$malfunzionamento->difficolta] ?? 'secondary' }}">
                                                {{ ucfirst($malfunzionamento->difficolta) }}
                                            </span>
                                            
                                            @if($malfunzionamento->tempo_stimato)
                                                <div class="text-muted small mt-1">
                                                    <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }} min
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]) }}" 
                                               class="btn btn-outline-primary"
                                               title="Visualizza soluzione completa">
                                                <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                            </a>
                                            
                                            <button type="button" 
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                        title="Segnala di aver riscontrato questo problema">
                                                    <i class="bi bi-plus-circle me-1"></i>Segnala
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Paginazione --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $malfunzionamenti->links() }}
                </div>
                
            @elseif(request()->hasAny(['q', 'gravita', 'difficolta', 'categoria_prodotto', 'prodotto_id']))
                {{-- Nessun risultato trovato --}}
                <div class="card card-custom">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Nessun risultato trovato</h4>
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
                {{-- Stato iniziale --}}
                <div class="card card-custom">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-primary" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Cerca Malfunzionamenti e Soluzioni</h4>
                        <p class="text-muted mb-4">Utilizza i filtri sopra per trovare rapidamente le soluzioni ai problemi tecnici.</p>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="text-start">
                                    <h6 class="fw-semibold mb-2">💡 Suggerimenti per la ricerca:</h6>
                                    <ul class="list-unstyled text-muted">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Usa parole chiave specifiche: "non si accende", "perdita", "rumore"</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Combina più filtri per risultati più precisi</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Parti dalla gravità più alta se è un'emergenza</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Filtra per categoria prodotto per restringere i risultati</li>
                                    </ul>
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

@push('scripts')
<script>
$(document).ready(function() {
    console.log('JavaScript caricato');
    
    // Test click segnala con versione semplificata
    $('.segnala-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Bottone cliccato');
        
        const btn = $(this);
        const id = btn.data('malfunzionamento-id');
        
        console.log('ID trovato:', id);
        
        if (!id) {
            alert('ID mancante');
            return;
        }
        
        if (!confirm('Segnalare problema?')) {
            return;
        }
        
        btn.prop('disabled', true).text('Invio...');
        
        $.post({
            url: '/malfunzionamenti/' + id + '/segnala',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Successo:', response);
                if (response.success) {
                    btn.removeClass('btn-outline-warning')
                       .addClass('btn-outline-success')
                       .text('Segnalato')
                       .prop('disabled', true);
                    
                    // Aggiorna contatore
                    $('[data-segnalazioni-count="' + id + '"]')
                        .html('<i class="bi bi-flag me-1"></i>' + response.nuovo_count + ' segnalazioni');
                        
                    alert('Segnalazione registrata!');
                } else {
                    alert('Errore: ' + response.message);
                    btn.prop('disabled', false).text('Segnala');
                }
            },
            error: function(xhr) {
                console.error('Errore:', xhr);
                alert('Errore HTTP ' + xhr.status + ': ' + xhr.statusText);
                btn.prop('disabled', false).text('Segnala');
            }
        });
    });
    
    console.log('Setup completato');
});
</script>
@endpush

@push('styles')
<style>
/* === STILI PER RICERCA MALFUNZIONAMENTI === */

/* Card personalizzate */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Hover effect per risultati */
.hover-light:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Suggerimenti ricerca */
.search-suggestions {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    background-color: #fff;
}

.search-suggestions .list-group-item:hover {
    background-color: #f8f9fa;
}

.search-suggestions::-webkit-scrollbar {
    width: 6px;
}

.search-suggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.search-suggestions::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

/* Focus migliorato per campi */
.form-control:focus,
.form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

/* Badge personalizzati */
.badge.bg-danger {
    animation: pulse-danger 2s infinite;
}

@keyframes pulse-danger {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Evidenziazione termini di ricerca */
mark {
    background-color: #fff3cd;
    padding: 0.125em 0.25em;
    border-radius: 0.25rem;
    font-weight: 600;
}

/* Stili per le statistiche */
.bg-primary.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Responsive design */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .col-lg-4.text-end {
        text-align: left !important;
        margin-top: 1rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Animazioni */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Alert personalizzati */
.alert {
    border: none;
    border-radius: 0.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
    color: #0f5132;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
    color: #721c24;
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

/* Stili per badge gravità/difficoltà */
.badge {
    font-size: 0.75em;
    font-weight: 600;
}

/* Loading states */
.btn-loading {
    opacity: 0.6;
    pointer-events: none;
}

.btn-loading::after {
    content: "";
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-left: 8px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #ffc107;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush