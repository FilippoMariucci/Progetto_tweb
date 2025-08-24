{{--
    Vista completa per prodotto con malfunzionamenti (per tecnici)
    Percorso: resources/views/prodotti/completo/show.blade.php
    Accessibile solo a utenti con livello_accesso >= 2
--}}

@extends('layouts.app')

@section('title', $prodotto->nome . ' - Dettagli Completi')

@section('content')
<div class="container mt-4">
    
    {{-- === BREADCRUMB === --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                @if(auth()->user()->isTecnico())
                    <a href="{{ route('tecnico.dashboard') }}">Dashboard Tecnico</a>
                @elseif(auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}">Dashboard Staff</a>
                @else
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endif
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('prodotti.completo.index') }}">Catalogo Completo</a>
            </li>
            <li class="breadcrumb-item active">{{ $prodotto->nome }}</li>
        </ol>
    </nav>

    {{-- === HEADER PRODOTTO === --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-3">
                <i class="bi bi-box text-primary me-2"></i>
                {{ $prodotto->nome }}
                @if($prodotto->modello)
                    <small class="text-muted">- {{ $prodotto->modello }}</small>
                @endif
            </h1>
            
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</span>
                
                @if($prodotto->prezzo)
                    <span class="badge bg-success">€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}</span>
                @endif
                
                {{-- Badge staff assegnato --}}
                @if($prodotto->staffAssegnato)
                    <span class="badge bg-info">
                        <i class="bi bi-person-badge me-1"></i>
                        Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                    </span>
                @endif
            </div>
            
            {{-- Alert per problemi critici --}}
            @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
                <div class="alert alert-danger border-start border-danger border-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">ATTENZIONE: Problemi Critici</h5>
                            <p class="mb-0">
                                Questo prodotto ha <strong>{{ $statistiche['malfunzionamenti_critici'] }}</strong> 
                                problema/i critico/i che richiedono intervento immediato.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Immagine prodotto --}}
        <div class="col-md-4 text-center">
            @if($prodotto->foto)
                <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                     alt="{{ $prodotto->nome }}" 
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 200px;">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                     style="height: 200px; width: 100%;">
                    <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- === INFORMAZIONI PRODOTTO === --}}
        <div class="col-lg-8">
            
            {{-- Descrizione --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Descrizione Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $prodotto->descrizione }}</p>
                </div>
            </div>

            {{-- Note tecniche --}}
            @if($prodotto->note_tecniche)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-gear text-warning me-2"></i>
                            Note Tecniche
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{!! nl2br(e($prodotto->note_tecniche)) !!}</p>
                    </div>
                </div>
            @endif

            {{-- Modalità installazione --}}
            @if($prodotto->modalita_installazione)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-tools text-success me-2"></i>
                            Modalità di Installazione
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{!! nl2br(e($prodotto->modalita_installazione)) !!}</p>
                    </div>
                </div>
            @endif

            {{-- Modalità d'uso --}}
            @if($prodotto->modalita_uso)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-book text-info me-2"></i>
                            Modalità d'Uso
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{!! nl2br(e($prodotto->modalita_uso)) !!}</p>
                    </div>
                </div>
            @endif

            {{-- === SEZIONE MALFUNZIONAMENTI === --}}
            @if($showMalfunzionamenti ?? false)
                <div class="card card-custom">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Malfunzionamenti e Soluzioni
                                @if(isset($statistiche))
                                    <span class="badge bg-dark ms-2">{{ $statistiche['totale_malfunzionamenti'] }}</span>
                                @endif
                            </h5>
                            
                            {{-- Solo per staff: aggiungi nuovo --}}
                            @if(auth()->user()->canManageMalfunzionamenti())
                                <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                   class="btn btn-dark btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        
                        {{-- Lista malfunzionamenti --}}
                        <div class="row g-3" id="malfunzionamentiList">
                            @forelse($prodotto->malfunzionamenti ?? [] as $malfunzionamento)
                                <div class="col-md-6 malfunzionamento-item" 
                                     data-gravita="{{ $malfunzionamento->gravita }}" 
                                     data-created="{{ $malfunzionamento->created_at->format('Y-m-d') }}">
                                    
                                    @php
                                        $borderColor = match($malfunzionamento->gravita) {
                                            'critica' => 'danger',
                                            'alta' => 'warning',
                                            'media' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $badgeColor = match($malfunzionamento->gravita) {
                                            'critica' => 'danger',
                                            'alta' => 'warning',
                                            'media' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $diffColors = [
                                            'facile' => 'success',
                                            'media' => 'info',
                                            'difficile' => 'warning',
                                            'esperto' => 'danger'
                                        ];
                                    @endphp
                                    
                                    <div class="card border-start border-{{ $borderColor }} border-3">
                                        <div class="card-body py-3">
                                            
                                            {{-- Header malfunzionamento --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">{{ $malfunzionamento->titolo }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $badgeColor }}">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                    
                                                    <span class="badge bg-{{ $diffColors[$malfunzionamento->difficolta] ?? 'secondary' }} ms-1">
                                                        {{ ucfirst($malfunzionamento->difficolta) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            {{-- Descrizione --}}
                                            <p class="text-muted small mb-2">
                                                {{ Str::limit($malfunzionamento->descrizione, 100) }}
                                            </p>
                                            
                                            {{-- Statistiche --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="text-muted small">
                                                    @if($malfunzionamento->numero_segnalazioni)
                                                        <span class="badge bg-primary me-1" id="badge-{{ $malfunzionamento->id }}">
                                                            <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }} segnalazioni
                                                        </span>
                                                    @endif
                                                    
                                                    @if($malfunzionamento->tempo_stimato)
                                                        <span class="badge bg-info me-1">
                                                            <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }} min
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Data ultima segnalazione --}}
                                                @if($malfunzionamento->ultima_segnalazione)
                                                    <small class="text-muted">
                                                        Ultima: {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                            
                                            {{-- Azioni --}}
                                            <div class="d-flex gap-2">
                                                {{-- Visualizza soluzione --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                                </a>
                                                
                                                {{-- Segnala problema --}}
                                                <button type="button" 
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                        title="Segnala di aver riscontrato questo problema">
                                                    <i class="bi bi-plus-circle me-1"></i>Segnala
                                                </button>
                                                
                                                {{-- Solo per staff: modifica/elimina --}}
                                                @if(auth()->user()->canManageMalfunzionamenti())
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                                           class="btn btn-outline-warning btn-sm"
                                                           title="Modifica malfunzionamento">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <form method="POST" 
                                                              action="{{ route('staff.malfunzionamenti.destroy', $malfunzionamento) }}" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Confermi l\'eliminazione di questo malfunzionamento?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    title="Elimina malfunzionamento">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Info creatore (per staff) --}}
                                            @if($malfunzionamento->creatoBy && auth()->user()->isStaff())
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        Creato da: {{ $malfunzionamento->creatoBy->nome_completo ?? 'N/A' }}
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
                                        <i class="bi bi-check-circle-fill text-success display-1"></i>
                                        <h4 class="text-success mt-3">Ottima notizia!</h4>
                                        <p class="text-muted">
                                            Non ci sono malfunzionamenti noti per questo prodotto.
                                        </p>
                                        
                                        {{-- Solo per staff: aggiungi primo malfunzionamento --}}
                                        @if(auth()->user()->canManageMalfunzionamenti())
                                            <div class="mt-3">
                                                <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                                   class="btn btn-outline-warning">
                                                    <i class="bi bi-plus-circle me-1"></i>
                                                    Aggiungi Primo Malfunzionamento
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Link per vedere tutti --}}
                        @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 6)
                            <div class="text-center mt-4">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="bi bi-list me-1"></i>
                                    Visualizza Tutti i Malfunzionamenti ({{ $prodotto->malfunzionamenti->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- === SIDEBAR INFORMAZIONI === --}}
        <div class="col-lg-4">
            
            {{-- Statistiche malfunzionamenti --}}
            @if(isset($statistiche) && $showMalfunzionamenti)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            Statistiche Malfunzionamenti
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="bi bi-list text-primary fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $statistiche['totale_malfunzionamenti'] }}</h4>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $statistiche['malfunzionamenti_critici'] }}</h4>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <i class="bi bi-exclamation-circle text-warning fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $statistiche['malfunzionamenti_alti'] }}</h4>
                                    <small class="text-muted">Alta Gravità</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                    <i class="bi bi-flag text-info fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $statistiche['totale_segnalazioni'] }}</h4>
                                    <small class="text-muted">Segnalazioni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Azioni rapide --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Tutti i malfunzionamenti --}}
                        @if($showMalfunzionamenti && $prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 0)
                            <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                               class="btn btn-outline-warning">
                                <i class="bi bi-list me-1"></i>Tutti i Malfunzionamenti
                            </a>
                        @endif
                        
                        {{-- Ricerca globale --}}
                        <a href="{{ route('malfunzionamenti.ricerca') }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Ricerca Globale
                        </a>
                        
                        {{-- Torna al catalogo --}}
                        <a href="{{ route('prodotti.completo.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Torna al Catalogo
                        </a>
                        
                        {{-- Solo per staff: gestione --}}
                        @if(auth()->user()->canManageMalfunzionamenti())
                            <hr>
                            <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                               class="btn btn-warning">
                                <i class="bi bi-plus-circle me-1"></i>Aggiungi Malfunzionamento
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Prodotti correlati --}}
            @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
                <div class="card card-custom">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-collection text-info me-2"></i>
                            Prodotti Correlati
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($prodottiCorrelati as $correlato)
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('prodotti.completo.show', $correlato) }}" 
                                           class="text-decoration-none">
                                            {{ $correlato->nome }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $correlato->malfunzionamenti_count }} problemi noti
                                    </small>
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

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Vista prodotto completo caricata - ID: {{ $prodotto->id }}');
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            return;
        }
        
        // Mostra loading sul pulsante
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...').prop('disabled', true);
        
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
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                    updateSegnalazioniCount(malfunzionamentoId, response.nuovo_count);
                    button.html(originalText).prop('disabled', false);
                } else {
                    throw new Error(response.message || 'Errore sconosciuto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore segnalazione:', {xhr, status, error});
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione.';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato.';
                } else if (xhr.status === 500) {
                    errorMsg = 'Errore interno del server. Riprova più tardi.';
                } else if (status === 'timeout') {
                    errorMsg = 'Timeout della richiesta. Controlla la connessione.';
                }
                
                showAlert(errorMsg, 'danger');
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    // === FUNZIONE AGGIORNA CONTATORE ===
    function updateSegnalazioniCount(malfunzionamentoId, newCount) {
        const badge = $(`#badge-${malfunzionamentoId}`);
        if (badge.length > 0) {
            badge.html(`<i class="bi bi-flag me-1"></i>${newCount} segnalazioni`);
        }
    }
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type = 'info', duration = 5000) {
        $('.custom-alert').remove();
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'x-circle' : 'info-circle'} me-2"></i>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.custom-alert').fadeOut(function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // === TOOLTIPS ===
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === ANALYTICS ===
    console.log('Prodotto completo visualizzato:', {
        prodotto_id: {{ $prodotto->id }},
        nome: '{{ $prodotto->nome }}',
        categoria: '{{ $prodotto->categoria }}',
        malfunzionamenti_count: {{ $prodotto->malfunzionamenti ? $prodotto->malfunzionamenti->count() : 0 }},
        user_level: {{ auth()->user()->livello_accesso }},
        timestamp: new Date().toISOString()
    });
    
    // Traccia tempo di permanenza
    let startTime = Date.now();
    
    $(window).on('beforeunload', function() {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        console.log(`Tempo trascorso sulla pagina: ${timeSpent} secondi`);
    });
});
</script>
@endpush

@push('styles')
<style>
/* === STILI PER VISTA PRODOTTO COMPLETO === */

.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.malfunzionamento-item {
    transition: all 0.3s ease;
}

.malfunzionamento-item:hover {
    transform: translateY(-2px);
}

.border-3 {
    border-width: 3px !important;
}

.card.border-start.border-danger {
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.15);
}

.card.border-start.border-warning {
    box-shadow: 0 0.125rem 0.25rem rgba(255, 193, 7, 0.15);
}

.card.border-start.border-info {
    box-shadow: 0 0.125rem 0.25rem rgba(13, 202, 240, 0.15);
}

.badge {
    font-size: 0.75em;
    font-weight: 600;
}

/* Statistiche */
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

/* Loading animation */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Alert personalizzati */
.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
}

/* Hover effects */
.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Responsive design */
@media (max-width: 768px) {
    .malfunzionamento-item .btn-group {
        flex-direction: column;
    }
    
    .malfunzionamento-item .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    .custom-alert {
        position: fixed !important;
        top: 10px !important;
        left: 10px !important;
        right: 10px !important;
        min-width: auto !important;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
    }
}

/* Animazioni */
@keyframes pulse-danger {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.border-danger {
    animation: pulse-danger 2s infinite;
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

/* Miglioramenti accessibilità */
.btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Stili per immagine prodotto */
.img-fluid.rounded.shadow-sm {
    transition: transform 0.3s ease;
}

.img-fluid.rounded.shadow-sm:hover {
    transform: scale(1.05);
}
</style>
@endpush