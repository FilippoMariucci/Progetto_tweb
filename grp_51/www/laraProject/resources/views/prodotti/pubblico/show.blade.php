{{-- 
    Vista PUBBLICA per scheda prodotto singolo - STILE UNIFICATO
    Path: resources/views/prodotti/pubblico/show.blade.php
    Sistema Assistenza Tecnica - Gruppo 51
    
    Stesso layout compatto della vista tecnica ma SENZA malfunzionamenti
--}}

@extends('layouts.app')

@section('title', $prodotto->nome . ' - Scheda Tecnica')

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- === HEADER COMPATTO UNIFICATO === --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ $prodotto->nome }}</h2>
            <p class="text-muted small mb-0">Scheda tecnica pubblica</p>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Catalogo
            </a>
            @auth
                @if(Auth::user()->canViewMalfunzionamenti())
                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-info">
                        <i class="bi bi-tools"></i> Vista Tecnica
                    </a>
                @endif
            @endauth
        </div>
    </div>

    {{-- Breadcrumb compatto --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.pubblico.index') }}">Catalogo Pubblico</a></li>
            @if($prodotto->categoria)
                <li class="breadcrumb-item">
                    <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($prodotto->categoria) }}">
                        {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($prodotto->nome, 30) }}</li>
        </ol>
    </nav>

    {{-- === ALERT TECNICO DISPONIBILE === --}}
    @auth
        @if(Auth::user()->canViewMalfunzionamenti())
            <div class="alert alert-info border-0 shadow-sm mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Vista Tecnica Disponibile</strong> - 
                        Accedi alla vista completa con malfunzionamenti e soluzioni tecniche.
                    </div>
                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-tools me-1"></i>Vista Completa
                    </a>
                </div>
            </div>
        @endif
    @endauth

    {{-- === LAYOUT ORIZZONTALE PRINCIPALE UNIFICATO === --}}
    <div class="row g-4">
        
        {{-- === COLONNA IMMAGINE E INFO (stile identico alla vista tecnica) === --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="position-relative">
                    @if($prodotto->foto)
                        {{-- IMMAGINE CORRETTA CON OBJECT-FIT CONTAIN --}}
                        <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $prodotto->nome }}"
                             style="height: 280px; object-fit: contain; background-color: #f8f9fa; cursor: pointer; padding: 1rem;"
                             onclick="openImageModal('{{ asset('storage/' . $prodotto->foto) }}', '{{ $prodotto->nome }}')">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 280px;">
                            <div class="text-center">
                                <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 small">Immagine non disponibile</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Badge categoria --}}
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                    </div>
                    
                    {{-- Badge prezzo --}}
                    @if($prodotto->prezzo)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                €{{ number_format($prodotto->prezzo, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Azioni immagine compatte --}}
                @if($prodotto->foto)
                    <div class="card-body py-2">
                        <div class="d-flex gap-1">
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
            
            {{-- === INFO TECNICHE COMPATTE (stile identico) === --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-1"></i>Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 text-center">
                        @if($prodotto->created_at)
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Catalogato</small>
                                    <strong class="small">{{ $prodotto->created_at->format('d/m/Y') }}</strong>
                                </div>
                            </div>
                        @endif
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Categoria</small>
                                <strong class="small">{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                            </div>
                        </div>
                        @if($prodotto->modello)
                            <div class="col-12">
                                <div class="p-2 bg-light rounded">
                                    <small class="text-muted d-block">Modello</small>
                                    <code class="small">{{ $prodotto->modello }}</code>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Staff assegnato (se presente) --}}
                        @if($prodotto->staffAssegnato)
                            <div class="col-12">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <small class="text-muted d-block">Referente Tecnico</small>
                                    <span class="badge bg-info small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $prodotto->staffAssegnato->nome_completo }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- === ASSISTENZA TECNICA BOX === --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-headset me-1"></i>Assistenza Tecnica
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <div class="fw-bold text-success">24/7</div>
                                <small class="text-muted">Supporto</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                                <div class="fw-bold text-info">Gratuita</div>
                                <small class="text-muted">Consulenza</small>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="d-grid gap-1">
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-geo-alt me-1"></i>Trova Centro Assistenza
                                </a>
                                <a href="{{ route('contatti') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Contatta Supporto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- === COLONNA INFORMAZIONI PRINCIPALE (stile identico) === --}}
        <div class="col-lg-8 col-md-7">
            
            {{-- Header prodotto compatto --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-2">{{ $prodotto->nome }}</h1>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($prodotto->modello)
                            <span class="badge bg-secondary small">{{ $prodotto->modello }}</span>
                        @endif
                        
                        {{-- Badge categoria --}}
                        <span class="badge bg-primary small">
                            <i class="bi bi-tag me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        </span>
                        
                        {{-- Badge staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <span class="badge bg-info small">
                                <i class="bi bi-person-badge me-1"></i>
                                Ref: {{ Str::limit($prodotto->staffAssegnato->nome_completo, 20) }}
                            </span>
                        @endif
                        
                        {{-- Badge pubblico --}}
                        <span class="badge bg-success small">
                            <i class="bi bi-eye me-1"></i>
                            Vista Pubblica
                        </span>
                    </div>
                </div>
                @if($prodotto->prezzo)
                    <div class="text-end">
                        <h4 class="text-success mb-0">€{{ number_format($prodotto->prezzo, 2, ',', '.') }}</h4>
                    </div>
                @endif
            </div>
            
            {{-- Descrizione --}}
            @if($prodotto->descrizione)
                <div class="mb-3">
                    <p class="text-muted">{{ $prodotto->descrizione }}</p>
                </div>
            @endif
            
            {{-- === SCHEDA TECNICA COMPATTA (stile identico) === --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i>Scheda Tecnica Completa
                    </h6>
                </div>
                <div class="card-body py-3">
                    
                    {{-- Layout compatto per scheda tecnica --}}
                    <div class="row g-3">
                        
                        {{-- Note tecniche --}}
                        @if($prodotto->note_tecniche)
                            <div class="col-lg-4">
                                <h6 class="text-primary small fw-semibold">
                                    <i class="bi bi-gear me-1"></i>Specifiche Tecniche
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-primary border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->note_tecniche)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Installazione --}}
                        @if($prodotto->modalita_installazione)
                            <div class="col-lg-4">
                                <h6 class="text-success small fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Modalità Installazione
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-success border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_installazione)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Modalità d'uso --}}
                        @if($prodotto->modalita_uso)
                            <div class="col-lg-4">
                                <h6 class="text-info small fw-semibold">
                                    <i class="bi bi-book me-1"></i>Modalità d'Uso
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-info border-3">
                                    <div class="small">
                                        {!! nl2br(e($prodotto->modalita_uso)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Messaggio informazioni mancanti --}}
                        @if(!$prodotto->note_tecniche && !$prodotto->modalita_installazione && !$prodotto->modalita_uso)
                            <div class="col-12 text-center py-3">
                                <i class="bi bi-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">
                                    Scheda tecnica in aggiornamento.
                                    <br><a href="{{ route('contatti') }}" class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="bi bi-envelope me-1"></i>Richiedi informazioni
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === SEZIONE ASSISTENZA TECNICA (invece dei malfunzionamenti) === --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-headset me-1"></i>
                            Supporto e Assistenza Tecnica
                        </h6>
                    </div>
                </div>
                <div class="card-body py-3">
                    
                    {{-- Griglia servizi assistenza --}}
                    <div class="row g-3">
                        
                        {{-- Supporto tecnico --}}
                        <div class="col-lg-6">
                            <div class="card border-start border-primary border-3 h-100">
                                <div class="card-body py-3">
                                    <h6 class="card-title mb-2 fw-bold small text-primary">
                                        <i class="bi bi-tools me-1"></i>Supporto Tecnico Specializzato
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        I nostri tecnici qualificati sono pronti ad assisterti per installazione, 
                                        configurazione e risoluzione di eventuali problemi tecnici.
                                    </p>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-primary small">24/7</span>
                                        <span class="badge bg-success small">Gratuito</span>
                                        <span class="badge bg-info small">Specializzato</span>
                                    </div>
                                    <div class="d-grid gap-1">
                                        @guest
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-person-check me-1"></i>Accesso Tecnici
                                            </a>
                                        @else
                                            @if(Auth::user()->canViewMalfunzionamenti())
                                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-tools me-1"></i>Area Tecnica
                                                </a>
                                            @else
                                                <a href="{{ route('contatti') }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-headset me-1"></i>Richiedi Supporto
                                                </a>
                                            @endif
                                        @endguest
                                        <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Documentazione e guide --}}
                        <div class="col-lg-6">
                            <div class="card border-start border-success border-3 h-100">
                                <div class="card-body py-3">
                                    <h6 class="card-title mb-2 fw-bold small text-success">
                                        <i class="bi bi-book me-1"></i>Guide e Documentazione
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        Accedi alle guide di installazione, manuali d'uso e documentazione tecnica 
                                        per sfruttare al meglio le funzionalità del prodotto.
                                    </p>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-success small">PDF</span>
                                        <span class="badge bg-warning small">Video Guide</span>
                                        <span class="badge bg-info small">FAQ</span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Call to action per accesso tecnico --}}
                    @guest
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong>Sei un tecnico autorizzato?</strong> 
                                            Accedi per visualizzare informazioni dettagliate sui malfunzionamenti e le relative soluzioni tecniche.
                                        </div>
                                        <a href="{{ route('login') }}" class="btn btn-info btn-sm ms-2">
                                            <i class="bi bi-person-check me-1"></i>Accedi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    {{-- === PRODOTTI CORRELATI COMPATTI (se disponibili) === --}}
    @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-collection text-info me-1"></i>
                            Altri Prodotti della Categoria "{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}"
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            @foreach($prodottiCorrelati->take(4) as $correlato)
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @if($correlato->foto)
                                                    <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: contain; background-color: #f8f9fa;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 small">
                                                        <a href="{{ route('prodotti.pubblico.show', $correlato) }}" 
                                                           class="text-decoration-none">
                                                            {{ Str::limit($correlato->nome, 25) }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted d-block mb-1">
                                                        {{ $correlato->modello ?? 'Modello N/A' }}
                                                    </small>
                                                    <div class="d-flex gap-1">
                                                        @if($correlato->prezzo)
                                                            <span class="badge bg-success small">
                                                                €{{ number_format($correlato->prezzo, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                        <span class="badge bg-primary small">Disponibile</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Link catalogo categoria --}}
                        <div class="text-center mt-3">
                            <a href="{{ route('prodotti.pubblico.index') }}?categoria={{ urlencode($prodotto->categoria) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>
                                Vedi Tutti i Prodotti {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- === MODAL IMMAGINE IDENTICO === --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="imageModalImg" src="" alt="" class="img-fluid w-100" style="object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* === STILI IDENTICI ALLA VISTA TECNICA === */

/* Card base */
.card {
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

/* === IMMAGINE PRODOTTO CORRETTA === */
.product-image {
    transition: transform 0.3s ease;
    border-radius: 0.375rem;
}

.product-image:hover {
    transform: scale(1.02);
}

/* Badge più compatti */
.badge.small {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Card body compatti */
.card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card-body.py-3 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}

/* Card header compatti */
.card-header.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.border-3 {
    border-width: 3px !important;
}

/* Background opacity personalizzati */
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

.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

/* Alert personalizzati */
.alert {
    border-radius: 0.5rem;
}

.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Responsive design identico */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .product-image {
        height: 180px !important;
        padding: 0.5rem;
    }
    
    .row.g-3 {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row.g-3 > * {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .h2 {
        font-size: 1.2rem;
    }
    
    .d-flex.flex-wrap.gap-1 {
        gap: 0.25rem !important;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
}

/* Animazioni */
.btn:hover {
    transform: translateY(-1px);
}

.badge:hover {
    transform: scale(1.05);
}

/* Focus migliorato */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

/* Modal immagine */
#imageModal .modal-body img {
    max-height: 80vh;
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

/* Scroll smooth per navigazione interna */
html {
    scroll-behavior: smooth;
}

/* Evidenziazione sezioni quando linkate */
.section-highlight {
    animation: highlight 2s ease-in-out;
}

@keyframes highlight {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    100% { background-color: transparent; }
}
</style>
@endpush

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

// Dati di sessione e permessi per JS pubblico
window.PageData.sessionSuccess = @json(session('success'));
window.PageData.sessionError = @json(session('error'));
window.PageData.sessionInfo = @json(session('info'));
window.PageData.user_can_view_malfunctions = @json(Auth::check() && Auth::user()->canViewMalfunzionamenti());
// ...
</script>
@endpush