{{-- 
    Vista per l'elenco dei malfunzionamenti di un prodotto
    Accessibile solo a tecnici (livello 2+) e staff (livello 3+)
    Percorso: resources/views/malfunzionamenti/index.blade.php
--}}

@extends('layouts.app')

@section('title', 'Malfunzionamenti - Dashboard')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER MALFUNZIONAMENTI === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('prodotti.index') }}" class="text-decoration-none">
                            <i class="bi bi-box me-1"></i>Catalogo
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                            {{ $prodotto->nome }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Malfunzionamenti</li>
                </ol>
            </nav>

            {{-- Titolo principale --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Malfunzionamenti
                    </h1>
                    <p class="text-muted mb-0">
                        Problemi noti per: <strong>{{ $prodotto->nome }}</strong>
                        @if($prodotto->modello)
                            - {{ $prodotto->modello }}
                        @endif
                    </p>
                </div>

                {{-- Pulsante aggiungi (solo per staff) --}}
                @auth
                    @if(auth()->user()->canManageMalfunzionamenti())
                        <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" class="btn btn-primary">

                            <i class="bi bi-plus-circle me-1"></i>Nuovo Malfunzionamento
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === --}}
    @if(isset($stats))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded p-2 me-3">
                                    <i class="bi bi-list-ul text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-primary">{{ $stats['totale'] ?? 0 }}</h4>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded p-2 me-3">
                                    <i class="bi bi-exclamation-circle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-danger">{{ $stats['critici'] ?? 0 }}</h4>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded p-2 me-3">
                                    <i class="bi bi-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-warning">{{ $stats['alta_gravita'] ?? 0 }}</h4>
                                    <small class="text-muted">Alta Gravità</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded p-2 me-3">
                                    <i class="bi bi-graph-up text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-info">{{ $stats['totale_segnalazioni'] ?? 0 }}</h4>
                                    <small class="text-muted">Segnalazioni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === FILTRI E RICERCA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3" id="filter-form">
                        
                        {{-- Campo ricerca --}}
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca Malfunzionamento
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cerca nel titolo o descrizione..."
                                   {{-- Disabilita autocomplete --}}
                                   autocomplete="off"
                                   autocapitalize="off"
                                   autocorrect="off"
                                   spellcheck="false"
                                   data-form-type="other">
                        </div>
                        
                        {{-- Filtro gravità --}}
                        <div class="col-md-3">
                            <label for="gravita" class="form-label fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Gravità
                            </label>
                            <select name="gravita" id="gravita" class="form-select">
                                <option value="">Tutte le gravità</option>
                                <option value="critica" {{ request('gravita') == 'critica' ? 'selected' : '' }}>
                                    🔴 Critica
                                </option>
                                <option value="alta" {{ request('gravita') == 'alta' ? 'selected' : '' }}>
                                    🟡 Alta
                                </option>
                                <option value="media" {{ request('gravita') == 'media' ? 'selected' : '' }}>
                                    🟢 Media
                                </option>
                                <option value="bassa" {{ request('gravita') == 'bassa' ? 'selected' : '' }}>
                                    ⚪ Bassa
                                </option>
                            </select>
                        </div>
                        
                        {{-- Filtro difficoltà --}}
                        <div class="col-md-3">
                            <label for="difficolta" class="form-label fw-semibold">
                                <i class="bi bi-tools me-1"></i>Difficoltà
                            </label>
                            <select name="difficolta" id="difficolta" class="form-select">
                                <option value="">Tutte le difficoltà</option>
                                <option value="facile" {{ request('difficolta') == 'facile' ? 'selected' : '' }}>
                                    Facile
                                </option>
                                <option value="media" {{ request('difficolta') == 'media' ? 'selected' : '' }}>
                                    Media
                                </option>
                                <option value="difficile" {{ request('difficolta') == 'difficile' ? 'selected' : '' }}>
                                    Difficile
                                </option>
                                <option value="esperto" {{ request('difficolta') == 'esperto' ? 'selected' : '' }}>
                                    Esperto
                                </option>
                            </select>
                        </div>
                        
                        {{-- Ordinamento --}}
                        <div class="col-md-2">
                            <label for="order" class="form-label fw-semibold">
                                <i class="bi bi-sort-down me-1"></i>Ordina
                            </label>
                            <select name="order" id="order" class="form-select">
                                <option value="gravita" {{ request('order') == 'gravita' ? 'selected' : '' }}>
                                    Gravità
                                </option>
                                <option value="frequenza" {{ request('order') == 'frequenza' ? 'selected' : '' }}>
                                    Frequenza
                                </option>
                                <option value="recente" {{ request('order') == 'recente' ? 'selected' : '' }}>
                                    Più Recente
                                </option>
                                <option value="difficolta" {{ request('order') == 'difficolta' ? 'selected' : '' }}>
                                    Difficoltà
                                </option>
                            </select>
                        </div>
                        
                        {{-- Pulsanti --}}
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i>Applica Filtri
                                </button>
                                @if(request()->hasAny(['search', 'gravita', 'difficolta', 'order']))
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-secondary">
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

    {{-- === ELENCO MALFUNZIONAMENTI === --}}
    <div class="row">
        <div class="col-12">
            @if($malfunzionamenti->count() > 0)
                {{-- Lista malfunzionamenti --}}
                <div class="row g-4">
                    @foreach($malfunzionamenti as $malfunzionamento)
                        <div class="col-12">
                            <div class="card h-100 malfunzionamento-card 
                                @switch($malfunzionamento->gravita)
                                    @case('critica') border-danger @break
                                    @case('alta') border-warning @break 
                                    @case('media') border-info @break
                                    @default border-light
                                @endswitch
                            ">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        
                                        {{-- Badge gravità --}}
                                        <div class="col-auto">
                                            <span class="badge 
                                                @switch($malfunzionamento->gravita)
                                                    @case('critica') bg-danger @break
                                                    @case('alta') bg-warning text-dark @break
                                                    @case('media') bg-info @break
                                                    @default bg-secondary
                                                @endswitch
                                                fs-6 px-3 py-2">
                                                @switch($malfunzionamento->gravita)
                                                    @case('critica') 🔴 CRITICA @break
                                                    @case('alta') 🟡 ALTA @break
                                                    @case('media') 🟢 MEDIA @break
                                                    @default ⚪ BASSA
                                                @endswitch
                                            </span>
                                        </div>
                                        
                                        {{-- Contenuto principale --}}
                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0">
                                                    <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                       class="text-decoration-none">
                                                        {{ $malfunzionamento->titolo }}
                                                    </a>
                                                </h5>
                                                
                                                {{-- Metadata --}}
                                                <div class="text-muted small">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    {{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                                                </div>
                                            </div>
                                            
                                            {{-- Descrizione --}}
                                            <p class="card-text text-muted mb-3">
                                                {{ Str::limit($malfunzionamento->descrizione, 150) }}
                                            </p>
                                            
                                            {{-- Informazioni tecniche --}}
                                            <div class="row g-2 mb-3">
                                                <div class="col-sm-4">
                                                    <small class="text-muted">
                                                        <i class="bi bi-tools me-1"></i>
                                                        Difficoltà: <strong>{{ ucfirst($malfunzionamento->difficolta) }}</strong>
                                                    </small>
                                                </div>
                                                
                                                @if($malfunzionamento->tempo_stimato)
                                                    <div class="col-sm-4">
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Tempo: <strong>{{ $malfunzionamento->tempo_stimato }} min</strong>
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                @if($malfunzionamento->ultima_segnalazione)
                                                    <div class="col-sm-4">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            Ultima: {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Pulsanti azione --}}
                                            <div class="d-flex gap-2 flex-wrap">
                                                {{-- Visualizza dettagli --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Visualizza Soluzione
                                                </a>
                                                
                                                {{-- Segnala (per tecnici) --}}
                                                @if(auth()->user()->canViewMalfunzionamenti() && !auth()->user()->canManageMalfunzionamenti())
                                                    {{-- Segnala problema --}}
                                                <button type="button" 
                                                        class="btn btn-outline-warning segnala-btn"
                                                        onclick="segnalaMalfunzionamento('{{ $malfunzionamento->id }}')"
                                                        title="Segnala di aver riscontrato questo problema">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Ho Questo Problema
                                                </button>
                                                @endif
                                                
                                                {{-- Gestione (per staff) --}}
                                                @if(auth()->user()->canManageMalfunzionamenti())
                                                    <a href="{{ route('staff.malfunzionamenti.edit', [$prodotto, $malfunzionamento]) }}" class="btn btn-outline-secondary btn-sm">

                                                        <i class="bi bi-pencil me-1"></i>Modifica
                                                    </a>
                                                    
                                                    <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Eliminare questo malfunzionamento?')">
                                                            <i class="bi bi-trash me-1"></i>Elimina
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginazione --}}
                @if($malfunzionamenti->hasPages())
                    <div class="row mt-4">
                        <div class="col-12">
                            <nav aria-label="Paginazione malfunzionamenti">
                                {{ $malfunzionamenti->withQueryString()->links() }}
                            </nav>
                            
                            {{-- Info paginazione --}}
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Visualizzati {{ $malfunzionamenti->firstItem() }}-{{ $malfunzionamenti->lastItem() }} 
                                    di {{ $malfunzionamenti->total() }} malfunzionamenti
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                {{-- Nessun malfunzionamento trovato --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        @if(request()->hasAny(['search', 'gravita', 'difficolta']))
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h3 class="text-muted mt-3">Nessun malfunzionamento trovato</h3>
                            <p class="text-muted">
                                Non sono stati trovati malfunzionamenti corrispondenti ai criteri di ricerca.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                                </a>
                            </div>
                        @else
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h3 class="text-success mt-3">Ottima notizia!</h3>
                            <p class="text-muted">
                                Non ci sono malfunzionamenti noti per questo prodotto.
                            </p>
                            <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-1"></i>Torna al Prodotto
                            </a>
                        @endif
                        
                        {{-- Pulsante aggiungi per staff --}}
                        @auth
                            @if(auth()->user()->canManageMalfunzionamenti())
                                <div class="mt-4">
                                    <a href="{{ route('malfunzionamenti.create', $prodotto) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Malfunzionamento
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- === SEZIONE STILI === --}}
@push('styles')
<style>
/* Stili per le card malfunzionamenti */
.malfunzionamento-card {
    transition: all 0.2s ease-in-out;
}

.malfunzionamento-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Disabilita autocomplete per il campo ricerca */
#search {
    /* Nessuna proprietà CSS 'autocomplete' */
}

#search::-webkit-contacts-auto-fill-button,
#search::-webkit-credentials-auto-fill-button {
    visibility: hidden;
    display: none !important;
    pointer-events: none;
}

/* Badge responsive */
@media (max-width: 768px) {
    .badge.fs-6 {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
    }
}
</style>
@endpush

{{-- === SEZIONE JAVASCRIPT === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('Pagina malfunzionamenti caricata');

    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    // Definisce la funzione globale chiamata dai bottoni onclick
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!malfunzionamentoId) {
            alert('Errore: ID malfunzionamento non valido');
            return;
        }
        
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return;
        }
        
        // Trova il bottone e mostra loading
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...').prop('disabled', true);
        
        // Chiamata AJAX per segnalare il malfunzionamento
        $.ajax({
           url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    // Aggiorna il contatore delle segnalazioni
                    $(`[data-segnalazioni-count="${malfunzionamentoId}"]`)
                        .html(`<i class="bi bi-flag me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    // Cambia il pulsante per mostrare successo
                    button.removeClass('btn-outline-success')
                          .addClass('btn-success')
                          .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                          .prop('disabled', true);
                    
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                } else {
                    throw new Error(response.message || 'Errore nella risposta');
                }
            },
            error: function(xhr) {
                console.error('Errore AJAX:', xhr);
                let msg = 'Errore nella segnalazione del malfunzionamento';
                
                // Gestione messaggi di errore specifici
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    msg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    msg = 'Malfunzionamento non trovato';
                }
                
                showAlert(msg, 'danger');
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    // === DISABILITA AUTOCOMPLETE ===
    $('#search').attr({
        'autocomplete': 'off',
        'autocapitalize': 'off',
        'autocorrect': 'off',
        'spellcheck': 'false'
    });
    
    // === TOOLTIP ===
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === AUTO-SUBMIT FILTRI ===
    $('#gravita, #difficolta, #order').on('change', function() {
        $('#filter-form').submit();
    });
    
    // === SEGNALAZIONE MALFUNZIONAMENTO (AJAX) ===
    $('.segnala-btn').on('click', function() {
        const btn = $(this);
        const malfunzionamentoId = btn.data('malfunzionamento-id');
        
        if (!confirm('Vuoi segnalare di aver riscontrato questo problema?')) {
            return;
        }
        
        // Disabilita pulsante durante richiesta
        btn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Invio...');
        
        $.ajax({
            url: `/api/malfunzionamenti/${malfunzionamentoId}/segnala`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Aggiorna contatore segnalazioni
                    btn.closest('.card-body')
                       .find('.bi-exclamation-triangle')
                       .parent()
                       .html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    // Cambia pulsante
                    btn.removeClass('btn-outline-warning')
                       .addClass('btn-outline-success')
                       .html('<i class="bi bi-check me-1"></i>Segnalato')
                       .prop('disabled', true);
                    
                    // Mostra messaggio successo
                    showAlert('success', response.message || 'Segnalazione registrata');
                }
            },
            error: function(xhr) {
                console.error('Errore segnalazione:', xhr);
                showAlert('danger', 'Errore durante la segnalazione');
                
                // Riabilita pulsante
                btn.prop('disabled', false)
                   .html('<i class="bi bi-exclamation me-1"></i>Ho Questo Problema');
            }
        });
    });
    
    // === RICERCA LIVE (DEBOUNCED) ===
    let searchTimeout;
    $('#search').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                $('#filter-form').submit();
            }, 500); // Aspetta 500ms dopo l'ultima digitazione
        }
    });
    
    // === FUNZIONE HELPER PER ALERT ===
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1055; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    console.log('JavaScript malfunzionamenti inizializzato');
});
</script>
@endpush