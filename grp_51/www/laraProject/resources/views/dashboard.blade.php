{{-- Dashboard generale - Solo per utenti pubblici o come fallback --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                Dashboard Generale
            </h1>
            
            {{-- Benvenuto personalizzato --}}
            <div class="alert alert-info border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle display-6 text-primary me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Benvenuto, {{ $user->nome_completo }}!</h4>
                        <p class="mb-0">
                            Livello di accesso: 
                            <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }} ms-1">
                                {{ $user->livello_descrizione }}
                            </span>
                        </p>
                        @if($user->isTecnico() && $user->centroAssistenza)
                            <small class="text-muted">
                                Centro: {{ $user->centroAssistenza->nome }} - {{ $user->centroAssistenza->citta }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Messaggio di reindirizzamento --}}
            @if($user->livello_accesso >= 2)
                <div class="alert alert-light border-start border-secondary border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="alert-heading mb-1">
                                <i class="bi bi-info-circle me-2"></i>
                                Dashboard Specifica Disponibile
                            </h5>
                            <p class="mb-0">
                                Hai accesso a una dashboard personalizzata per il tuo livello di accesso.
                            </p>
                        </div>
                        <div>
                            @if($user->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                    <i class="bi bi-shield-check me-1"></i>Dashboard Admin
                                </a>
                            @elseif($user->isStaff())
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning">
                                    <i class="bi bi-person-badge me-1"></i>Dashboard Staff
                                </a>
                            @elseif($user->isTecnico())
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info">
                                    <i class="bi bi-person-gear me-1"></i>Dashboard Tecnico
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        
        {{-- === ACCESSI RAPIDI GENERALI === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Accessi Rapidi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Link per tutti gli utenti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('prodotti.index') }}" class="btn btn-outline-primary btn-lg w-100 h-100">
                                <i class="bi bi-box display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Prodotti</span>
                            </a>
                        </div>
                        
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('centri.index') }}" class="btn btn-outline-info btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Link per tecnici e superiori --}}
                        @can('viewMalfunzionamenti')
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('prodotti.index') }}?view=tech" class="btn btn-outline-warning btn-lg w-100 h-100">
                                    <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Malfunzionamenti</span>
                                </a>
                            </div>
                        @endcan
                        
                        {{-- Link alle dashboard specifiche --}}
                        @if($user->isTecnico())
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info btn-lg w-100 h-100">
                                    <i class="bi bi-person-gear display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Tecnico</span>
                                </a>
                            </div>
                        @endif
                        
                        @if($user->isStaff())
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning btn-lg w-100 h-100">
                                    <i class="bi bi-person-badge display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Staff</span>
                                </a>
                            </div>
                        @endif
                        
                        @if($user->isAdmin())
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-lg w-100 h-100">
                                    <i class="bi bi-person-fill-gear display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Dashboard Admin</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE GENERALI === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            @if(isset($stats['total_prodotti']))
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($stats['total_centri']))
                                <div class="col-6">
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        <i class="bi bi-geo-alt text-info fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_centri'] }}</h4>
                                        <small class="text-muted">Centri</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted text-center">Nessuna statistica disponibile</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === INFORMAZIONI GENERALI === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Sistema di Assistenza Tecnica
                    </h5>
                    <p class="text-muted mb-3">
                        Piattaforma per la gestione dell'assistenza tecnica e supporto post-vendita
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('azienda') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-building me-1"></i>Chi Siamo
                        </a>
                        <a href="{{ route('contatti') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-telephone me-1"></i>Contatti
                        </a>
                        <a href="{{ route('documentazione') }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Documentazione
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Dashboard generale caricata per utente livello {{ $user->livello_accesso }}');
    
    // Auto-reindirizzamento per utenti con dashboard specifiche (opzionale)
    @if($user->livello_accesso >= 2)
        // Mostra un toast informativo dopo 3 secondi
        setTimeout(function() {
            const toastMessage = 'Ricorda: hai accesso a una dashboard personalizzata per il tuo ruolo!';
            if (typeof showToast === 'function') {
                showToast(toastMessage, 'info');
            }
        }, 3000);
    @endif
});
</script>
@endpush