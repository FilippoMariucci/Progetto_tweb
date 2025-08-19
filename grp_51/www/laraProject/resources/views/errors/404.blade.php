{{-- 
    Vista per errore 404 - Pagina non trovata
    Percorso: resources/views/errors/404.blade.php
--}}

@extends('layouts.app')

@section('title', 'Pagina non trovata - 404')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-lg-6 col-md-8">
            <div class="text-center">
                
                {{-- Icona 404 --}}
                <div class="mb-4">
                    <div class="display-1 text-primary mb-3" style="font-size: 8rem; font-weight: bold;">
                        404
                    </div>
                    <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                </div>
                
                {{-- Messaggio principale --}}
                <h1 class="h2 mb-3 text-dark">Pagina non trovata</h1>
                
                <p class="lead text-muted mb-4">
                    Ci dispiace, la pagina che stai cercando non esiste o è stata spostata.
                </p>
                
                {{-- Suggerimenti --}}
                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading">
                        <i class="bi bi-lightbulb me-2"></i>Cosa puoi fare:
                    </h6>
                    <ul class="list-unstyled mb-0 text-start">
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Controlla l'URL digitato</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Torna alla homepage</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Cerca nel catalogo prodotti</li>
                        @auth
                            <li><i class="bi bi-arrow-right text-primary me-2"></i>Vai alla tua dashboard</li>
                        @endauth
                    </ul>
                </div>
                
                {{-- Pulsanti di navigazione --}}
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-door me-2"></i>Torna alla Home
                    </a>
                    
                    <a href="{{ route('prodotti.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-box me-2"></i>Catalogo Prodotti
                    </a>
                </div>
                
                {{-- Link aggiuntivi per utenti autenticati --}}
                @auth
                    <div class="mt-4">
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                            
                            @if(auth()->user()->canViewMalfunzionamenti())
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-tools me-1"></i>Area Tecnica
                                </a>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-gear me-1"></i>Admin
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Link per utenti guest --}}
                    <div class="mt-4">
                        <p class="text-muted">
                            Hai un account? 
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Accedi qui
                            </a>
                        </p>
                    </div>
                @endauth
                
                {{-- Informazioni di contatto --}}
                <div class="mt-5 pt-4 border-top">
                    <h6 class="text-muted mb-3">Hai bisogno di aiuto?</h6>
                    <div class="row g-3 text-center">
                        <div class="col-md-4">
                            <a href="{{ route('centri.index') }}" class="text-decoration-none">
                                <i class="bi bi-geo-alt text-primary d-block mb-1" style="font-size: 1.5rem;"></i>
                                <small class="text-muted">Centri Assistenza</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('contatti') }}" class="text-decoration-none">
                                <i class="bi bi-envelope text-primary d-block mb-1" style="font-size: 1.5rem;"></i>
                                <small class="text-muted">Contattaci</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('azienda') }}" class="text-decoration-none">
                                <i class="bi bi-info-circle text-primary d-block mb-1" style="font-size: 1.5rem;"></i>
                                <small class="text-muted">Chi Siamo</small>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    {{-- Sezione con prodotti in evidenza (solo per guest) --}}
    @guest
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body text-center py-4">
                    <h5 class="card-title">
                        <i class="bi bi-stars text-warning me-2"></i>
                        Prodotti in Evidenza
                    </h5>
                    <p class="card-text text-muted mb-3">
                        Scopri i nostri prodotti più popolari mentre sei qui
                    </p>
                    <a href="{{ route('prodotti.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-right me-1"></i>Esplora il Catalogo
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endguest
</div>
@endsection

{{-- === STILI PERSONALIZZATI === --}}
@push('styles')
<style>
/* Stili per la pagina 404 */
.error-404-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

/* Animazione per l'icona di avviso */
.bi-exclamation-triangle {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Hover effects per i link di aiuto */
.row a:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

/* Responsive per il numero 404 */
@media (max-width: 576px) {
    .display-1 {
        font-size: 5rem !important;
    }
}

/* Stili per i pulsanti */
.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}
</style>
@endpush

{{-- === JAVASCRIPT OPZIONALE === --}}
@push('scripts')
<script>
$(document).ready(function() {
    console.log('Pagina 404 caricata');
    
    // Traccia l'errore 404 per analytics (se necessario)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'exception', {
            'description': 'Error 404: ' + window.location.pathname,
            'fatal': false
        });
    }
    
    // Aggiungi effetto hover ai pulsanti
    $('.btn').hover(
        function() {
            $(this).addClass('shadow');
        },
        function() {
            $(this).removeClass('shadow');
        }
    );
    
    // Auto-focus sul pulsante principale dopo 2 secondi
    setTimeout(function() {
        $('.btn-primary').first().focus();
    }, 2000);
    
    console.log('JavaScript pagina 404 inizializzato');
});
</script>
@endpush