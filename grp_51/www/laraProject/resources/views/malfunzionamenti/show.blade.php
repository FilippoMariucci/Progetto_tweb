{{-- 
    Vista per visualizzare un singolo malfunzionamento con la sua soluzione completa
    Accessibile solo a tecnici (livello 2+) e staff (livello 3+)
    Percorso: resources/views/malfunzionamenti/show.blade.php
    VERSIONE CORRETTA - Fix duplicazioni e funzionalità segnalazione
--}}

@extends('layouts.app')

@section('title', $malfunzionamento->titolo . ' - Soluzione')

@section('content')
<div class="container mt-4">
    
    {{-- === BREADCRUMB === --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('prodotti.pubblico.index') }}" class="text-decoration-none">
                    <i class="bi bi-box me-1"></i>Catalogo
                </a>
            </li>
            <li class="breadcrumb-item">
                @auth
                    @if(auth()->user()->canViewMalfunzionamenti())
                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="text-decoration-none">
                    @else
                        <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                    @endif
                @else
                    <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                @endauth
                    {{ $prodotto->nome }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="text-decoration-none">
                    Malfunzionamenti
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($malfunzionamento->titolo, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        
        {{-- === COLONNA PRINCIPALE === --}}
        <div class="col-lg-8">
            
            {{-- Card principale del malfunzionamento --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header 
                    @switch($malfunzionamento->gravita)
                        @case('critica') bg-danger text-white @break
                        @case('alta') bg-warning text-dark @break
                        @case('media') bg-info text-white @break
                        @default bg-light text-dark
                    @endswitch
                ">
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Badge gravità --}}
                        <span class="badge 
                            @switch($malfunzionamento->gravita)
                                @case('critica') bg-light text-danger @break
                                @case('alta') bg-light text-warning @break
                                @case('media') bg-light text-info @break
                                @default bg-dark text-light
                            @endswitch
                            fs-6 px-3 py-2">
                            @switch($malfunzionamento->gravita)
                                @case('critica') CRITICA @break
                                @case('alta') ALTA @break
                                @case('media') MEDIA @break
                                @default BASSA
                            @endswitch
                        </span>
                        
                        {{-- Numero segnalazioni - ID per JavaScript --}}
                        <div class="text-end">
                            <span class="badge bg-light text-dark" id="segnalazioni-counter">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Titolo principale --}}
                    <h1 class="h3 card-title mb-3">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        {{ $malfunzionamento->titolo }}
                    </h1>
                    
                    {{-- Descrizione del problema --}}
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="bi bi-info-circle me-2"></i>Descrizione del Problema
                        </h5>
                        <div class="bg-light rounded p-3">
                            <p class="mb-0">{{ $malfunzionamento->descrizione }}</p>
                        </div>
                    </div>
                    
                    {{-- SOLUZIONE TECNICA --}}
                    <div class="mb-4">
                        <h5 class="text-success">
                            <i class="bi bi-tools me-2"></i>Soluzione Tecnica
                        </h5>
                        <div class="border-start border-success border-3 ps-3">
                            @if($malfunzionamento->soluzione)
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    {{-- Rendi la soluzione più leggibile con paragrafi --}}
                                    @php
                                        $soluzione_paragrafi = explode("\n", $malfunzionamento->soluzione);
                                    @endphp
                                    
                                    @foreach($soluzione_paragrafi as $paragrafo)
                                        @if(trim($paragrafo) !== '')
                                            <p class="mb-2">{{ trim($paragrafo) }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Soluzione non ancora disponibile per questo malfunzionamento.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Strumenti necessari --}}
                    @if($malfunzionamento->strumenti_necessari)
                        <div class="mb-4">
                            <h6 class="text-info">
                                <i class="bi bi-tools me-2"></i>Strumenti Necessari
                            </h6>
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <p class="mb-0">{{ $malfunzionamento->strumenti_necessari }}</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Informazioni tecniche dettagliate --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-speedometer text-primary fs-4 d-block mb-1"></i>
                                    <strong>Difficoltà</strong>
                                    <div class="text-muted">{{ ucfirst($malfunzionamento->difficolta) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($malfunzionamento->tempo_stimato)
                            <div class="col-sm-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-clock text-success fs-4 d-block mb-1"></i>
                                        <strong>Tempo Stimato</strong>
                                        <div class="text-muted">{{ $malfunzionamento->tempo_stimato }} minuti</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- === PULSANTI DI AZIONE === --}}
                    <div class="d-flex gap-2 flex-wrap">
                        
                        {{-- Torna all'elenco --}}
                        <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Torna all'Elenco Malfunzionamenti di {{ $prodotto->nome }}
                        </a>
                        
                        {{-- SEGNALA PROBLEMA (per tecnici e staff) --}}
                        @auth
                            @if(auth()->user()->canViewMalfunzionamenti())
        <button type="button" 
                class="btn btn-outline-warning btn-sm segnala-btn"
                onclick="segnalaMalfunzionamento('{{ $malfunzionamento->id }}')"
                title="Segnala di aver riscontrato questo problema">
            <i class="bi bi-exclamation-circle me-1"></i>Ho Questo Problema
        </button>
    @endif
                        @endauth
                        
                        {{-- MODIFICA E ELIMINA (solo per staff) --}}
                        @auth
                            @if(auth()->user()->canManageMalfunzionamenti())
                                <a href="{{ route('staff.malfunzionamenti.edit', [$malfunzionamento]) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Modifica Soluzione
                                </a>
                                
                                <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger">
        <i class="bi bi-trash me-1"></i>Elimina
    </button>
</form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            
        </div>
        
        {{-- === SIDEBAR DESTRA === --}}
        <div class="col-lg-4">
            
            {{-- INFORMAZIONI PRODOTTO --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-box me-2"></i>Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        {{-- Immagine prodotto --}}
                        @if($prodotto->foto)
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="rounded me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;"
                                 alt="{{ $prodotto->nome }}">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                        @endif
                        
                        {{-- Nome e modello --}}
                        <div>
                            <h6 class="mb-1">
                                @auth
                                    @if(auth()->user()->canViewMalfunzionamenti())
                                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="text-decoration-none">
                                            {{ $prodotto->nome }}
                                        </a>
                                    @else
                                        <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                                            {{ $prodotto->nome }}
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                                        {{ $prodotto->nome }}
                                    </a>
                                @endauth
                            </h6>
                            @if($prodotto->modello)
                                <small class="text-muted">Modello: {{ $prodotto->modello }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- Pulsante dettagli prodotto --}}
                    <div class="text-center">
                        @auth
                            @if(auth()->user()->canViewMalfunzionamenti())
                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>Vedi Dettagli Completi
                                    <span class="badge bg-warning text-dark ms-1">Con Malfunzionamenti</span>
                                </a>
                            @else
                                <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>Vedi Dettagli Prodotto
                                    <span class="badge bg-info ms-1">Vista Base</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye me-1"></i>Vedi Dettagli Prodotto
                                <span class="badge bg-secondary ms-1">Pubblico</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            {{-- CRONOLOGIA E METADATA --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Cronologia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        @if($malfunzionamento->prima_segnalazione)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Prima segnalazione:</span>
                                <strong>{{ \Carbon\Carbon::parse($malfunzionamento->prima_segnalazione)->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        
                        @if($malfunzionamento->ultima_segnalazione)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ultima segnalazione:</span>
                                <strong>{{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        
                        @if($malfunzionamento->creatoBy)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Creato da:</span>
                                <strong>{{ $malfunzionamento->creatoBy->nome_completo }}</strong>
                            </div>
                        @endif
                        
                        @if($malfunzionamento->modificatoBy)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Modificato da:</span>
                                <strong>{{ $malfunzionamento->modificatoBy->nome_completo }}</strong>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Aggiornato:</span>
                            <strong>{{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- PROBLEMI CORRELATI --}}
            @if(isset($correlati) && $correlati->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-link-45deg me-2"></i>Problemi Correlati
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($correlati as $correlato)
                            <div class="d-flex align-items-center mb-2 @if(!$loop->last) border-bottom pb-2 @endif">
                                <span class="badge 
                                    @switch($correlato->gravita)
                                        @case('critica') bg-danger @break
                                        @case('alta') bg-warning text-dark @break
                                        @case('media') bg-info @break
                                        @default bg-secondary
                                    @endswitch
                                    me-2">
                                    {{ ucfirst($correlato->gravita) }}
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('malfunzionamenti.show', [$correlato->prodotto, $correlato]) }}" 
                                       class="text-decoration-none small">
                                        {{ Str::limit($correlato->titolo, 40) }}
                                    </a>
                                    <div class="text-muted small">
                                        {{ $correlato->prodotto->nome }}
                                    </div>
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

{{-- === STILI PERSONALIZZATI === --}}
@push('styles')
<style>
/* Stili per la pagina di dettaglio malfunzionamento */
.card {
    transition: all 0.2s ease-in-out;
}

/* Stili per i badge di gravità */
.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* Miglioramenti tipografici */
h1.h3 {
    line-height: 1.3;
}

/* Hover effects per le card */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Stili per il pulsante di segnalazione */
.segnala-btn {
    transition: all 0.3s ease;
}

.segnala-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 > * {
        width: 100% !important;
        margin-bottom: 0.5rem;
    }
    
    .d-flex.gap-2 > *:last-child {
        margin-bottom: 0;
    }
}

/* Stile per alert di successo/errore */
.alert-floating {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
    min-width: 300px;
    max-width: 500px;
}
</style>
@endpush

{{-- === JAVASCRIPT === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('Pagina dettaglio malfunzionamento caricata');
    
    // === DEBUG INIZIALE ===
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('Pulsanti segnala trovati:', $('.segnala-btn').length);
    console.log('ID malfunzionamento:', $('.segnala-btn').data('malfunzionamento-id'));
    
    // === IMPLEMENTAZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    // Definisce la funzione globale chiamata dai bottoni onclick (STESSA IMPLEMENTAZIONE DI ricerca.blade.php)
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!malfunzionamentoId) {
            alert('Errore: ID malfunzionamento non valido');
            return;
        }
        
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return;
        }
        
        // Trova il bottone corretto usando l'onclick
        const button = $(`button[onclick="segnalaMalfunzionamento('${malfunzionamentoId}')"]`);
        const originalText = button.html();
        
        // Mostra stato di caricamento
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
                    // Aggiorna il contatore delle segnalazioni nella card
                    button.closest('.card-body')
                          .find('.bi-exclamation-triangle')
                          .parent()
                          .html(`<i class="bi bi-exclamation-triangle me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    // Cambia il pulsante per mostrare successo
                    button.removeClass('btn-outline-warning')
                          .addClass('btn-success')
                          .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                          .prop('disabled', true)
                          .removeAttr('onclick'); // Rimuove l'onclick handler
                    
                    // Mostra messaggio di successo
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
    
    // === FUNZIONE HELPER PER ALERT ===
    function showAlert(type, message) {
        // Rimuovi alert esistenti
        $('.alert-floating').remove();
        
        const alertClass = `alert-${type}`;
        const iconClass = {
            'success': 'check-circle-fill',
            'danger': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle-fill',
            'info': 'info-circle-fill'
        };
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-floating">
                <i class="bi bi-${iconClass[type] || 'info-circle-fill'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            $('.alert-floating').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // === SMOOTH SCROLLING PER ANCORE ===
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // === TOOLTIP INITIALIZATION ===
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    console.log('JavaScript dettaglio malfunzionamento inizializzato completamente');
});
</script>
@endpush