{{-- 
    File: resources/views/errors/404-authenticated.blade.php
    Descrizione: Pagina 404 personalizzata per utenti autenticati
--}}

@extends('layouts.app')

@section('title', 'Pagina non trovata')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            {{-- Header con icona e messaggio principale --}}
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>
                <h1 class="display-4 text-primary mb-3">404</h1>
                <h2 class="h3 text-muted mb-4">Pagina non trovata</h2>
                <p class="lead text-muted">
                    La pagina che stai cercando non esiste o è stata spostata.
                </p>
                
                {{-- Messaggio personalizzato per utente autenticato --}}
                @if(isset($user))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Ciao <strong>{{ $user->nome_completo }}</strong>, 
                        puoi tornare alla tua dashboard o utilizzare uno dei link qui sotto.
                    </div>
                @endif
            </div>

            {{-- Suggerimenti di navigazione per utenti autenticati --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-compass me-2"></i>
                        Dove vuoi andare?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($suggested_routes))
                            @foreach($suggested_routes as $name => $url)
                                <div class="col-md-6 mb-3">
                                    <a href="{{ $url }}" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="bi bi-{{ $loop->first ? 'speedometer2' : ($loop->last ? 'person' : 'box') }} me-2"></i>
                                        {{ $name }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    {{-- Link aggiuntivi per utenti autenticati --}}
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-house me-2"></i>
                                Homepage
                            </a>
                        </div>
                        
                        @auth
                            @if(Auth::user()->isTecnico())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info w-100">
                                        <i class="bi bi-tools me-2"></i>
                                        Prodotti Tecnici
                                    </a>
                                </div>
                            @endif
                            
                            @if(Auth::user()->isStaff())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-warning w-100">
                                        <i class="bi bi-gear me-2"></i>
                                        Dashboard Staff
                                    </a>
                                </div>
                            @endif
                            
                            @if(Auth::user()->isAdmin())
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-shield me-2"></i>
                                        Admin Panel
                                    </a>
                                </div>
                            @endif
                        @endauth
                        
                        <div class="col-md-4 mb-3">
                            <button onclick="history.back()" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left me-2"></i>
                                Torna Indietro
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informazioni di contatto per supporto --}}
            <div class="text-center mt-5">
                <p class="text-muted">
                    <small>
                        Se pensi che questo sia un errore, 
                        <a href="{{ route('contatti') }}" class="text-decoration-none">contatta l'assistenza</a>
                        o torna alla <a href="{{ route('home') }}" class="text-decoration-none">homepage</a>.
                    </small>
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Script per migliorare l'esperienza utente --}}


{{-- Stili aggiuntivi --}}
<style>
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.alert {
    border-radius: 10px;
    border: none;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem;
    }
}
</style>
@endsection