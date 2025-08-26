@extends('layouts.app')

@section('title', 'Centri di Assistenza')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                Centri di Assistenza Tecnica
            </h1>
            <p class="lead text-muted">
                Trova il centro di assistenza più vicino a te per supporto tecnico professionale
            </p>
        </div>
    </div>

    <!-- === FILTRI E RICERCA === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-body">
                    <form method="GET" action="{{ route('centri.index') }}" class="row g-3">
                        
                        <!-- Ricerca Generale -->
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome centro, città, indirizzo...">
                        </div>
                        
                        <!-- Filtro Provincia -->
                        <div class="col-md-3">
                            <label for="provincia" class="form-label fw-semibold">
                                <i class="bi bi-map me-1"></i>Provincia
                            </label>
                            <select name="provincia" id="provincia" class="form-select">
                                <option value="">Tutte le province</option>
                                @if(isset($province))
                                    @foreach($province as $sigla => $nome)
                                        <option value="{{ $sigla }}" {{ request('provincia') == $sigla ? 'selected' : '' }}>
                                            {{ $sigla }} - {{ $nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <!-- Filtro Città -->
                        <div class="col-md-3">
                            <label for="citta" class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i>Città
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="citta" 
                                   name="citta" 
                                   value="{{ request('citta') }}"
                                   placeholder="Nome città">
                        </div>
                        
                        <!-- Pulsanti -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                @if(request()->hasAny(['search', 'provincia', 'citta']))
                                    <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm">
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

    <!-- === STATISTICHE RAPIDE === -->
    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3">
                    <div class="badge bg-primary fs-6 py-2 px-3">
                        <i class="bi bi-geo-alt me-1"></i>{{ $stats['totale_centri'] ?? 0 }} centri totali
                    </div>
                    <div class="badge bg-success fs-6 py-2 px-3">
                        <i class="bi bi-people me-1"></i>{{ $stats['centri_con_tecnici'] ?? 0 }} con tecnici disponibili
                    </div>
                    @if(request('provincia'))
                        <div class="badge bg-info fs-6 py-2 px-3">
                            <i class="bi bi-filter me-1"></i>Provincia: {{ request('provincia') }}
                        </div>
                    @endif
                    @if(isset($stats['per_provincia']) && request('provincia') && isset($stats['per_provincia'][request('provincia')]))
                        <div class="badge bg-warning fs-6 py-2 px-3">
                            <i class="bi bi-building me-1"></i>{{ $stats['per_provincia'][request('provincia')] }} nella provincia
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- === RISULTATI === -->
    <div class="row mb-4">
        <div class="col-12">
            @if(isset($centri) && $centri->count() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Centri Trovati 
                        <span class="badge bg-secondary">{{ $centri->total() }}</span>
                    </h4>
                    
                    <!-- Info Risultati -->
                    <div class="d-flex align-items-center gap-3">
                        @if($centri->hasPages())
                            <small class="text-muted">
                                Pagina {{ $centri->currentPage() }} di {{ $centri->lastPage() }}
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Griglia Centri di Assistenza -->
                <div class="row g-4">
                    @foreach($centri as $centro)
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-custom h-100">
                                <div class="card-body">
                                    
                                    <!-- Header Centro -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1">
                                                <i class="bi bi-building text-primary me-2"></i>
                                                {{ $centro->nome }}
                                            </h5>
                                            <div class="mb-2">
                                                <span class="badge bg-primary">
                                                    {{ $centro->provincia }} - {{ $centro->citta }}
                                                </span>
                                                
                                                @if($centro->tecnici && $centro->tecnici->count() > 0)
                                                    <span class="badge bg-success ms-1">
                                                        <i class="bi bi-people me-1"></i>{{ $centro->tecnici->count() }} tecnici
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning ms-1">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>Senza tecnici
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informazioni Contatto -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-medium">{{ $centro->indirizzo }}</div>
                                                <div class="text-muted">{{ $centro->cap }} {{ $centro->citta }} ({{ $centro->provincia }})</div>
                                            </div>
                                        </div>

                                        @if($centro->telefono)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono_formattato ?? $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif

                                        @if($centro->email)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{ $centro->email }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Tecnici del Centro -->
                                    @if($centro->tecnici && $centro->tecnici->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2">
                                                <i class="bi bi-people me-1"></i>
                                                Tecnici Specializzati
                                            </h6>
                                            @foreach($centro->tecnici->take(3) as $tecnico)
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="flex-grow-1">
                                                        <small class="fw-medium">{{ $tecnico->nome_completo ?? $tecnico->nome . ' ' . $tecnico->cognome }}</small>
                                                        @if($tecnico->specializzazione)
                                                            <br>
                                                            <small class="text-muted">{{ $tecnico->specializzazione }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="ms-2">
                                                        @if(method_exists($tecnico, 'isRecentlyActive') && $tecnico->isRecentlyActive())
                                                            <i class="bi bi-circle-fill text-success" title="Attivo di recente"></i>
                                                        @else
                                                            <i class="bi bi-circle text-success" title="Tecnico disponibile"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            @if($centro->tecnici->count() > 3)
                                                <small class="text-muted">
                                                    E altri {{ $centro->tecnici->count() - 3 }} tecnici...
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                Nessun tecnico assegnato al momento
                                            </small>
                                        </div>
                                    @endif

                                </div>

                                <!-- Footer Card con Azioni -->
                                <div class="card-footer bg-light">
                                    <div class="d-flex gap-2">
                                        @if($centro->telefono)
                                            <a href="tel:{{ $centro->telefono }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-telephone me-1"></i>Chiama
                                            </a>
                                        @endif
                                        
                                        @if($centro->email)
                                            <a href="mailto:{{ $centro->email }}" class="btn btn-outline-info btn-sm flex-fill">
                                                <i class="bi bi-envelope me-1"></i>Email
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('centri.show', $centro) }}" class="btn btn-primary btn-sm flex-fill">
                                            <i class="bi bi-eye me-1"></i>Dettagli
                                        </a>
                                        
                                        @if(method_exists($centro, 'google_maps_link') && $centro->google_maps_link)
                                            <a href="{{ $centro->google_maps_link }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-map"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginazione -->
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $centri->withQueryString()->links() }}
                    </div>
                </div>

            @else
                <!-- Nessun Centro Trovato -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-search display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">Nessun centro di assistenza trovato</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'provincia', 'citta']))
                            Prova a modificare i filtri di ricerca o 
                            <a href="{{ route('centri.index') }}" class="text-decoration-none">visualizza tutti i centri</a>
                        @else
                            Non ci sono ancora centri di assistenza disponibili.
                        @endif
                    </p>
                    
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Centro
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- === DISTRIBUZIONE PER PROVINCIA === -->
    @if(isset($stats['per_provincia']) && count($stats['per_provincia']) > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-map text-primary me-2"></i>
                        Distribuzione per Provincia
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($stats['per_provincia'] as $prov => $count)
                            <div class="col-md-3 col-sm-4 col-6">
                                <a href="{{ route('centri.index', ['provincia' => $prov]) }}" 
                                   class="btn btn-outline-primary btn-sm w-100 d-flex justify-content-between align-items-center">
                                    <span>{{ $prov }}</span>
                                    <span class="badge bg-primary">{{ $count }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- === INFORMAZIONI AGGIUNTIVE === -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sui Centri di Assistenza
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Servizi Offerti</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i>Assistenza tecnica specializzata</li>
                                <li><i class="bi bi-check text-success me-2"></i>Riparazione e manutenzione prodotti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Consulenza tecnica professionale</li>
                                <li><i class="bi bi-check text-success me-2"></i>Supporto post-vendita</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Come Contattare un Centro</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-telephone text-primary me-2"></i>Chiama direttamente il numero indicato</li>
                                <li><i class="bi bi-envelope text-primary me-2"></i>Invia una email per informazioni</li>
                                <li><i class="bi bi-geo-alt text-primary me-2"></i>Visita il centro presso l'indirizzo mostrato</li>
                                <li><i class="bi bi-eye text-primary me-2"></i>Clicca su "Dettagli" per informazioni complete</li>
                            </ul>
                        </div>
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
    console.log('Pagina centri di assistenza caricata');
    console.log('Centri trovati: {{ isset($centri) ? $centri->count() : 0 }}');
    
    // Gestione ricerca in tempo reale (opzionale)
    let searchTimer;
    $('#search').on('input', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();
        
        if (searchTerm.length >= 3) {
            searchTimer = setTimeout(function() {
                console.log('Ricerca per:', searchTerm);
                // Potresti implementare una ricerca AJAX qui usando le API del controller
                // $.get('{{ route("api.centri.search") }}', {q: searchTerm}, function(data) { ... });
            }, 500);
        }
    });
    
    // Cambia automaticamente le città quando si seleziona una provincia
    $('#provincia').on('change', function() {
        const provincia = $(this).val();
        if (provincia) {
            console.log('Provincia selezionata:', provincia);
            // Potresti caricare le città per la provincia selezionata via AJAX
            // $.get('{{ route("api.centri.citta-provincia") }}', {provincia: provincia}, function(data) { ... });
        }
    });
    
    // Analytics per tracking delle interazioni
    $('.card').on('click', function() {
        const centerName = $(this).find('.card-title').text().trim();
        console.log('Centro visualizzato:', centerName);
    });
    
    // Gestione pulsanti telefono e email
    $('a[href^="tel:"]').on('click', function() {
        console.log('Chiamata avviata:', $(this).attr('href'));
    });
    
    $('a[href^="mailto:"]').on('click', function() {
        console.log('Email aperta:', $(this).attr('href'));
    });
});
</script>
@endpush

@push('styles')
<style>
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: box-shadow 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

@media (max-width: 768px) {
    .badge {
        margin-bottom: 0.25rem;
        display: inline-block;
    }
    
    .card-footer .d-flex {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .card-footer .btn {
        flex: 1 !important;
    }
}

/* Stile per le province nella distribuzione */
.btn-outline-primary:hover .badge {
    background-color: white !important;
    color: var(--bs-primary) !important;
}
</style>
@endpush