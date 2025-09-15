@push('scripts')
<script>
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";
</script>
@endpush
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
                            <i class="bi bi-exclamation-circle me-1"></i>Malfunzionamenti di: {{ $prodotto->nome }}
                        </a>
                        
                        {{-- SEGNALA PROBLEMA (per tecnici e staff) --}}
                        @auth
                            @if(auth()->user()->canViewMalfunzionamenti())
        <button type="button" 
                class="btn btn-outline-warning btn-sm segnala-btn"
                onclick="segnalaMalfunzionamento('{{ $malfunzionamento->id }}')"
                title="Segnala di aver riscontrato questo problema">
            <i class="bi bi-plus-circle me-1"></i>Ho Questo Problema
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
/* === STILI BASE PER PAGINA DETTAGLIO MALFUNZIONAMENTO === */

/* Transizioni fluide per tutte le card */
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

/* === STILI MIGLIORATI PER PULSANTE SUCCESS === */

/* Pulsante success con massima visibilità */
.btn-success {
    background-color: #198754 !important;
    border-color: #146c43 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border-width: 2px !important;
}

.btn-success:hover:not(:disabled) {
    background-color: #157347 !important;
    border-color: #146c43 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4) !important;
}

.btn-success:focus:not(:disabled) {
    background-color: #198754 !important;
    border-color: #146c43 !important;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
}

.btn-success:disabled {
    background-color: #198754 !important;
    border-color: #146c43 !important;
    color: #ffffff !important;
    opacity: 0.95 !important;
    cursor: not-allowed;
}

/* === ANIMAZIONE PULSO PER PULSANTE SUCCESS === */

@keyframes pulse-success {
    0% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0.2);
        transform: scale(1.05);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        transform: scale(1);
    }
}

.pulse-success {
    animation: pulse-success 1.5s ease-in-out 2;
}

/* === STILI PER PULSANTE SEGNALAZIONE === */

.segnala-btn {
    transition: all 0.3s ease;
    border-width: 2px;
    font-weight: 500;
}

.segnala-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

.segnala-btn:focus:not(:disabled) {
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

/* === MIGLIORAMENTI ALERT === */

/* Alert success più visibile */
.alert-success {
    background-color: #d1edda !important;
    border: 1px solid #badbcc !important;
    color: #0f5132 !important;
    font-weight: 500;
    border-radius: 0.5rem;
}

.alert-success .bi {
    color: #198754;
}

/* Alert danger migliorato */
.alert-danger {
    background-color: #f8d7da !important;
    border: 1px solid #f1aeb5 !important;
    color: #721c24 !important;
    font-weight: 500;
    border-radius: 0.5rem;
}

.alert-danger .bi {
    color: #dc3545;
}

/* Stile per alert flottanti */
.alert-floating {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
    min-width: 350px;
    max-width: 500px;
    border: none !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* === CONTRASTO MIGLIORATO PER BADGE === */

/* Badge contatore segnalazioni */
#segnalazioni-counter {
    background-color: #ffffff !important;
    color: #495057 !important;
    border: 2px solid #dee2e6 !important;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
}

/* Badge nella card header */
.card-header .badge.bg-light {
    background-color: #ffffff !important;
    color: #495057 !important;
    border: 2px solid #dee2e6 !important;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

/* === MIGLIORAMENTI RESPONSIVE === */

/* Tablet e mobile */
@media (max-width: 768px) {
    /* Pulsanti in colonna su mobile */
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
    
    /* Pulsante success più grande su mobile */
    .btn-success {
        font-size: 1.1rem;
        padding: 0.75rem 1.25rem;
        min-height: 50px;
    }
    
    .btn-success .bi {
        font-size: 1.2rem;
    }
    
    /* Alert flottanti responsive */
    .alert-floating {
        right: 10px;
        left: 10px;
        min-width: auto;
        max-width: none;
        width: calc(100% - 20px);
    }
}

/* Mobile piccoli */
@media (max-width: 576px) {
    .card-body {
        padding: 1rem 0.75rem;
    }
    
    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .btn-success {
        font-size: 1rem;
        padding: 0.75rem 1rem;
        min-height: 48px;
    }
}

/* === STILI PER SPINNER DI CARICAMENTO === */

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.125em;
}

/* Spinner nel pulsante */
.btn .spinner-border-sm {
    margin-right: 0.5rem;
}

/* === MIGLIORAMENTI ACCESSIBILITÀ === */

/* Focus visibile per elementi interattivi */
.btn:focus,
.btn-close:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Miglioramenti per lettori di schermo */
.visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* === STILI PER STATI DISABLED === */

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.8;
}

/* === MIGLIORAMENTI PERFORMANCE === */

/* Preload per transizioni fluide */
* {
    box-sizing: border-box;
}

/* Ottimizzazione animazioni */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .pulse-success {
        animation: none;
    }
}

/* === STILI AGGIUNTIVI PER MIGLIORARE L'ESPERIENZA === */

/* Card prodotto nella sidebar */
.card-body img {
    object-fit: contain;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

/* Badge gravità con colori migliorati */
.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000000 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000000 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: #ffffff !important;
}

/* === STAMPA === */

@media print {
    .btn,
    .alert-floating {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>
@endpush
{{-- === JAVASCRIPT === --}}
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