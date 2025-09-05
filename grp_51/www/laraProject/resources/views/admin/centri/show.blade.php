{{-- 
    Vista Admin Centro Assistenza - Versione con JavaScript Separato
    File: resources/views/admin/centri/show.blade.php
    JavaScript: public/js/admin/centri-show.js
--}}

@extends('layouts.app')

@section('title', 'Centro: ' . $centro->nome)

{{-- Nasconde il breadcrumb per dare più spazio --}}
@push('breadcrumb-override')
<style>
.breadcrumb, nav[aria-label="breadcrumb"] {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    {{-- Header principale con info base --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">{{ $centro->nome }}</h1>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $centro->indirizzo }}, {{ $centro->citta }} ({{ $centro->provincia }})
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    {{-- Stato centro e conteggio tecnici --}}
                    @if($centro->tecnici->count() > 0)
                        <span class="badge bg-success fs-6">Centro Attivo</span>
                    @else
                        <span class="badge bg-warning fs-6">Centro Inattivo</span>
                    @endif
                    <div class="mt-1">
                        <small>{{ $centro->tecnici->count() }} Tecnici Assegnati</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezione contatti e azioni rapide in una riga --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Informazioni Contatto</h5>
                </div>
                <div class="card-body">
                    {{-- Mostra contatti se disponibili --}}
                    @if($centro->telefono)
                        <p class="mb-2">
                            <strong>Telefono:</strong>
                            <a href="tel:{{ $centro->telefono }}" class="ms-2">{{ $centro->telefono }}</a>
                        </p>
                    @endif
                    
                    @if($centro->email)
                        <p class="mb-2">
                            <strong>Email:</strong>
                            <a href="mailto:{{ $centro->email }}" class="ms-2">{{ $centro->email }}</a>
                        </p>
                    @endif
                    
                    @if($centro->cap)
                        <p class="mb-0">
                            <strong>CAP:</strong> <span class="ms-2">{{ $centro->cap }}</span>
                        </p>
                    @endif
                    
                    {{-- Se non ci sono contatti --}}
                    @if(!$centro->telefono && !$centro->email)
                        <p class="text-muted mb-0">Nessun contatto disponibile</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Azioni Rapide</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Modifica centro --}}
                        <a href="{{ route('admin.centri.edit', $centro) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Modifica Centro
                        </a>
                        
                        {{-- Visualizza su mappa --}}
                        <button type="button" class="btn btn-success" onclick="GoogleMapsUtil.openMaps('{{ $centro->indirizzo }}, {{ $centro->citta }}, {{ $centro->provincia }}')">
                            <i class="bi bi-map me-1"></i> Visualizza su Maps
                        </button>
                        
                        {{-- Torna alla lista --}}
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Torna alla Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezione tecnici - La più importante --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Tecnici del Centro ({{ $centro->tecnici->count() }})
                    </h4>
                </div>
                <div class="col-auto">
                    {{-- Pulsante per aggiungere tecnico --}}
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                        <i class="bi bi-plus-circle me-1"></i> Aggiungi Tecnico
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if($centro->tecnici->isNotEmpty())
                {{-- Lista tecnici in formato semplice --}}
                <div class="row">
                    @foreach($centro->tecnici as $tecnico)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        {{-- Nome e info tecnico --}}
                                        <h6 class="mb-1">{{ $tecnico->nome_completo }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="bi bi-wrench me-1"></i>
                                            {{ $tecnico->specializzazione ?? 'Specializzazione non specificata' }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-calendar me-1"></i>
                                            Età: {{ $tecnico->eta ?? 'N/A' }} anni
                                        </p>
                                    </div>
                                    <div class="col-auto">
                                        {{-- Azioni sul tecnico --}}
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Visualizza
                                            </a>
                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Modifica
                                            </a>
                                            <form action="{{ route('admin.centri.rimuovi-tecnico', $centro) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Sei sicuro di voler rimuovere &quot;{{ addslashes($tecnico->nome_completo) }}&quot; da questo centro?\n\nIl tecnico rimarrà nel sistema ma non sarà più assegnato a questo centro.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tecnico_id" value="{{ $tecnico->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Rimuovi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Nessun tecnico assegnato --}}
                <div class="text-center py-5">
                    <i class="bi bi-people display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">Nessun Tecnico Assegnato</h5>
                    <p class="text-muted mb-4">Questo centro non ha ancora tecnici assegnati.</p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                        <i class="bi bi-plus-circle me-1"></i> Aggiungi Primo Tecnico
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Altri centri nella stessa provincia (se esistono) --}}
    @if(isset($centriVicini) && $centriVicini->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-geo me-2"></i>
                    Altri Centri in {{ $centro->provincia }} ({{ $centriVicini->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($centriVicini as $centroVicino)
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $centroVicino->nome }}</strong><br>
                                        <small class="text-muted">{{ $centroVicino->citta }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info">{{ $centroVicino->tecnici_count }} tecnici</span>
                                        <a href="{{ route('admin.centri.show', $centroVicino) }}" 
                                           class="btn btn-outline-secondary btn-sm d-block mt-1">
                                            Visualizza
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal per assegnazione tecnico --}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Tecnico al Centro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info centro nel modal --}}
                <div class="alert alert-info">
                    <strong>Centro:</strong> {{ $centro->nome }}<br>
                    <strong>Località:</strong> {{ $centro->indirizzo }}, {{ $centro->citta }}
                </div>
                
                {{-- Form assegnazione --}}
                <form id="formAssegnaTecnico" action="{{ route('admin.centri.assegna-tecnico', $centro) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label required">Seleziona Tecnico</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select" required>
                            <option value="">Caricamento tecnici disponibili...</option>
                        </select>
                        <div class="form-text">
                            Vengono mostrati sia i tecnici non assegnati che quelli trasferibili da altri centri.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="submit" form="formAssegnaTecnico" id="btnAssegnaTecnico" class="btn btn-success" disabled>
                    <i class="bi bi-check-circle me-1"></i> Assegna Tecnico
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- 
===================================================================
CONFIGURAZIONE PER JAVASCRIPT SEPARATO
===================================================================
--}}

{{-- Carica il file JavaScript separato --}}
@push('scripts')
{{-- File JavaScript principale --}}
<script src="{{ asset('js/admin/centri-show.js') }}"></script>

{{-- Configurazione per il modulo JavaScript --}}
<script>
// Configurazione per AdminCentroShow
window.AdminCentroShowConfig = {
    centroId: {{ $centro->id }},
    baseUrl: '{{ url("/") }}',
    csrfToken: '{{ csrf_token() }}',
    debugMode: {{ app()->environment('local') ? 'true' : 'false' }}, // Debug solo in locale
    centroNome: @json($centro->nome),
    centroIndirizzo: @json($centro->indirizzo . ', ' . $centro->citta . ', ' . $centro->provincia)
};

// Log di configurazione per debug
console.log('🔧 Configurazione AdminCentroShow caricata:', window.AdminCentroShowConfig);
</script>
@endpush

{{-- CSS personalizzato --}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI PER VISTA LINEARE === */

/* Miglioramento cards */
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Badge migliorati */
.badge {
    font-size: 0.875em;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}

/* Pulsanti gruppo verticale più compatti */
.btn-group-vertical .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Notifiche temporanee */
.notifica-temp {
    animation: slideInRight 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Miglioramenti per mobile */
@media (max-width: 768px) {
    .btn-group-vertical {
        display: flex;
        flex-direction: row;
        gap: 0.25rem;
    }
    
    .btn-group-vertical .btn {
        flex: 1;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .notifica-temp {
        left: 1rem;
        right: 1rem;
        min-width: auto;
    }
}

/* Label required con asterisco rosso */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Modal migliorato */
.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Stati select */
select:disabled {
    background-color: #f8f9fa;
    opacity: 0.7;
}

/* Miglioramenti accessibilità */
.form-control:focus,
.form-select:focus,
.btn:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: none;
}

/* Loading states */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Animazioni ridotte per accessibilità */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .notifica-temp {
        transition: none !important;
        animation: none !important;
    }
    
    .card:hover {
        transform: none;
    }
}
</style>
@endpush