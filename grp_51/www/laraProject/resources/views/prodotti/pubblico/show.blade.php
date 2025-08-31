{{-- 
    Vista PUBBLICA per scheda prodotto singolo (senza malfunzionamenti)
    Path: resources/views/prodotti/pubblico/show.blade.php
    Layout orizzontale e compatto con colori Bootstrap originali
--}}
@extends('layouts.app')

@section('title', $prodotto->nome . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- Breadcrumb compatto --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('prodotti.pubblico.index') }}">Catalogo</a>
            </li>
            @if($prodotto->categoria)
                <li class="breadcrumb-item">
                    <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($prodotto->categoria) }}">
                        {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $prodotto->nome }}</li>
        </ol>
    </nav>

    {{-- Alert tecnici compatto --}}
    @auth
        @if(Auth::user()->canViewMalfunzionamenti())
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="bi bi-tools me-2"></i>
                <span class="me-auto">Accesso tecnico disponibile - Visualizza malfunzionamenti e soluzioni</span>
                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-info btn-sm">
                    <i class="bi bi-eye me-1"></i>Vista Tecnica
                </a>
            </div>
        @endif
    @endauth

    {{-- Layout orizzontale principale --}}
    <div class="row g-4">
        
        {{-- Colonna immagine più stretta --}}
        <div class="col-md-4">
            <div class="card">
                <div class="position-relative">
                    @if($prodotto->foto)
                        <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                             class="card-img-top" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 300px; object-fit: cover; cursor: pointer;"
                             onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 300px;">
                            <div class="text-center">
                                <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0">Immagine non disponibile</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Badge compatti --}}
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                    </div>
                    
                    @if($prodotto->prezzo)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Azioni immagine --}}
                @if($prodotto->foto)
                    <div class="card-body p-2">
                        <div class="d-flex gap-2">
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
            
            {{-- Info box compatto --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Informazioni
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 text-center">
                        @if($prodotto->created_at)
                            <div class="col-6">
                                <small class="text-muted d-block">Catalogo</small>
                                <strong>{{ $prodotto->created_at->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        <div class="col-6">
                            <small class="text-muted d-block">Categoria</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                        </div>
                        @if($prodotto->codice_prodotto ?? false)
                            <div class="col-12">
                                <small class="text-muted d-block">Codice</small>
                                <code>{{ $prodotto->codice_prodotto }}</code>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Colonna informazioni più ampia --}}
        <div class="col-md-8">
            
            {{-- Header prodotto in linea --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h2 mb-1">{{ $prodotto->nome }}</h1>
                    @if($prodotto->modello)
                        <span class="badge bg-secondary">{{ $prodotto->modello }}</span>
                    @endif
                </div>
                @if($prodotto->prezzo)
                    <div class="text-end">
                        <h3 class="text-success mb-0">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</h3>
                    </div>
                @endif
            </div>
            
            {{-- Descrizione --}}
            @if($prodotto->descrizione)
                <p class="text-muted mb-3">{{ $prodotto->descrizione }}</p>
            @endif
            
            {{-- Scheda tecnica orizzontale --}}
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Scheda Tecnica
                    </h5>
                </div>
                <div class="card-body">
                    
                    {{-- Layout a colonne per scheda tecnica --}}
                    <div class="row g-4">
                        
                        {{-- Note tecniche --}}
                        @if($prodotto->note_tecniche)
                            <div class="col-lg-4">
                                <h6 class="text-primary">
                                    <i class="bi bi-gear me-1"></i>Specifiche
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($prodotto->note_tecniche)) !!}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Installazione --}}
                        @if($prodotto->modalita_installazione)
                            <div class="col-lg-4">
                                <h6 class="text-success">
                                    <i class="bi bi-tools me-1"></i>Installazione
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-success border-3">
                                    {!! nl2br(e($prodotto->modalita_installazione)) !!}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Modalità d'uso --}}
                        @if($prodotto->modalita_uso)
                            <div class="col-lg-4">
                                <h6 class="text-info">
                                    <i class="bi bi-book me-1"></i>Utilizzo
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-info border-3">
                                    {!! nl2br(e($prodotto->modalita_uso)) !!}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Se mancano informazioni --}}
                        @if(!$prodotto->note_tecniche && !$prodotto->modalita_installazione && !$prodotto->modalita_uso)
                            <div class="col-12 text-center py-4">
                                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">
                                    Scheda tecnica in aggiornamento.<br>
                                    <a href="{{ route('contatti') }}">Contatta l'assistenza</a> per maggiori informazioni.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Call to action orizzontale --}}
            <div class="card bg-primary text-white mt-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">
                                <i class="bi bi-question-circle me-2"></i>
                                Hai bisogno di assistenza?
                            </h5>
                            <p class="mb-0">
                                Il nostro team tecnico è a tua disposizione per supporto e risoluzione problemi.
                            </p>
                        </div>
                        <div class="col-md-4 text-end mt-2 mt-md-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-light btn-sm">
                                        <i class="bi bi-person-check me-1"></i>Login
                                    </a>
                                @endguest
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-geo-alt me-1"></i>Centri
                                </a>
                                <a href="{{ route('contatti') }}" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Contatti
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal semplice --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="imageModalImg" src="" alt="" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>
@endsection

{{-- CSS minimo e pulito --}}
@push('styles')
<style>
/* Layout compatto e orizzontale */
.card-img-top {
    transition: transform 0.2s ease;
}

.card-img-top:hover {
    transform: scale(1.02);
}

.card {
    border-radius: 0.375rem;
}

.badge {
    font-size: 0.75rem;
}

/* Responsive per layout orizzontale */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .h2 {
        font-size: 1.5rem;
    }
    
    .card-img-top {
        height: 250px !important;
    }
    
    /* Stack verticale su mobile */
    .col-md-8 .row.align-items-center {
        text-align: center;
    }
    
    .col-md-8 .row .col-md-4 {
        margin-top: 1rem;
    }
}

/* Colori Bootstrap originali mantenuti */
.bg-primary { background-color: #0d6efd !important; }
.bg-success { background-color: #198754 !important; }
.bg-info { background-color: #0dcaf0 !important; }
.bg-secondary { background-color: #6c757d !important; }

.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-info { color: #0dcaf0 !important; }

.border-primary { border-color: #0d6efd !important; }
.border-success { border-color: #198754 !important; }
.border-info { border-color: #0dcaf0 !important; }

/* Hover discreto */
.btn:hover {
    transform: translateY(-1px);
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

{{-- JavaScript essenziale --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('Vista prodotto orizzontale inizializzata');
    
    /**
     * Modal immagine semplice
     */
    window.openImageModal = function(imageSrc, imageTitle) {
        $('#imageModalImg').attr('src', imageSrc);
        $('#imageModalTitle').text(imageTitle);
        $('#imageModal').modal('show');
    };
    
    console.log('Vista prodotto pronta');
});
</script>
@endpush