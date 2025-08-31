{{-- 
    VISTA CATALOGO PRODOTTI TECNICO COMPLETO
    Stile identico al catalogo pubblico ma con funzionalità tecniche complete
    Include: malfunzionamenti, filtri avanzati, azioni staff/admin
--}}

@extends('layouts.app')

@section('title', 'Catalogo Completo - Tecnici')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    
    {{-- Header principale --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        {{-- Titolo e descrizione --}}
                        <div class="col-lg-8 col-md-7">
                            <h2 class="mb-1 fw-bold">
                                <i class="bi bi-tools me-2"></i>
                                Catalogo Tecnico Completo
                            </h2>
                            <p class="mb-0 opacity-90">
                                <span class="badge bg-warning text-dark me-2">Con Malfunzionamenti</span>
                                Accesso completo per tecnici e staff
                            </p>
                    </div>
            </div>
        </div>
    </div>

    {{-- Pulsanti azione flottanti ridimensionati --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        @if(auth()->user()->isStaff())
            @if($prodotti->count() > 0)
                <button class="btn btn-warning rounded-circle shadow me-2" 
                        style="width: 50px; height: 50px;"
                        data-bs-toggle="modal" data-bs-target="#quickSolutionModal"
                        title="Aggiungi Soluzione Rapida">
                    <i class="bi bi-plus-circle" style="font-size: 1.25rem;"></i>
                </button>
            @endif
        @endif
        
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.prodotti.create') }}" 
               class="btn btn-success rounded-circle shadow" 
               style="width: 50px; height: 50px;"
               data-bs-toggle="tooltip" 
               title="Aggiungi Nuovo Prodotto">
                <i class="bi bi-plus" style="font-size: 1.25rem;"></i>
            </a>
        @endif
    </div>
    {{-- Statistiche tecniche --}}
                        <div class="col-lg-4 col-md-5 mt-2 mt-md-0">
                            @if(isset($stats))
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            <div class="h5 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</div>
                                            <small class="opacity-90">Prodotti</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            <div class="h5 fw-bold mb-0">{{ $stats['malfunzionamenti_critici'] ?? 0 }}</div>
                                            <small class="opacity-90">Critici</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

    {{-- Form di ricerca compatto --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('prodotti.completo.index') }}" class="row g-3">

                        {{-- Campo di ricerca avanzata --}}
                        <div class="col-lg-5 col-md-7">
                            <label for="search" class="form-label fw-semibold text-primary">
                                <i class="bi bi-search me-1"></i>Ricerca Avanzata Prodotti
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="es: lavatrice, modello, codice, lav* (ricerca parziale)"
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Pulisci ricerca">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Cerca in nome, modello, descrizione. Usa <code>*</code> per ricerche parziali
                            </div>
                        </div>

                        {{-- Filtro categoria --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="categoria" class="form-label fw-semibold text-primary">
                                <i class="bi bi-funnel me-1"></i>Categoria
                            </label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="">Tutte le categorie</option>
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro tecnico --}}
                        <div class="col-lg-2 col-md-2">
                            <label for="filter" class="form-label fw-semibold text-primary">
                                <i class="bi bi-filter me-1"></i>Filtro
                            </label>
                            <select name="filter" id="filter" class="form-select">
                                <option value="">Tutti</option>
                                <option value="critici" {{ request('filter') === 'critici' ? 'selected' : '' }}>
                                    Critici
                                </option>
                                <option value="problematici" {{ request('filter') === 'problematici' ? 'selected' : '' }}>
                                    Con Problemi
                                </option>
                                <option value="senza_problemi" {{ request('filter') === 'senza_problemi' ? 'selected' : '' }}>
                                    Senza Problemi
                                </option>
                            </select>
                        </div>

                        {{-- Pulsanti azione --}}
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                <a href="{{ route('prodotti.completo.index') }}" 
                                   class="btn btn-outline-secondary" 
                                   title="Reset filtri">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                        
                        {{-- Filtri nascosti per staff --}}
                        @if(request('staff_filter'))
                            <input type="hidden" name="staff_filter" value="{{ request('staff_filter') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtri rapidi staff --}}
    @if(auth()->user()->isStaff())
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-secondary py-2 px-3">
                        <i class="bi bi-funnel me-1"></i>Filtri Staff:
                    </span>
                    
                    <a href="{{ route('prodotti.completo.index') }}" 
                       class="badge {{ !request('staff_filter') ? 'bg-primary' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                        Tutti i Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?staff_filter=my_products" 
                       class="badge {{ request('staff_filter') === 'my_products' ? 'bg-success' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                        I Miei Prodotti
                    </a>
                    
                    <a href="{{ route('prodotti.completo.index') }}?filter=critici" 
                       class="badge {{ request('filter') === 'critici' ? 'bg-danger' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                        Solo Critici
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Statistiche rapide --}}
    @if(isset($stats))
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-primary">{{ $stats['total_prodotti'] }} prodotti totali</span>
                    <span class="badge bg-warning">{{ $stats['con_malfunzionamenti'] ?? 0 }} con problemi</span>
                    <span class="badge bg-danger">{{ $stats['malfunzionamenti_critici'] ?? 0 }} critici</span>
                    
                    @if(auth()->user()->isStaff() && isset($stats['miei_prodotti']))
                        <span class="badge bg-success">{{ $stats['miei_prodotti'] }} miei prodotti</span>
                    @endif
                    
                    @if(request('categoria'))
                        <span class="badge bg-secondary">Categoria: {{ ucfirst(str_replace('_', ' ', request('categoria'))) }}</span>
                    @endif
                    @if(request('search'))
                        <span class="badge bg-info">Ricerca: "{{ request('search') }}"</span>
                    @endif
                    @if(request('staff_filter') === 'my_products')
                        <span class="badge bg-success">Solo Prodotti Assegnati</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Alert filtro staff --}}
    @if(request('staff_filter') === 'my_products')
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success border-0 shadow-sm py-2">
                    <i class="bi bi-person-check me-2"></i>
                    Stai visualizzando solo i <strong>tuoi prodotti assegnati</strong>. 
                    <a href="{{ route('prodotti.completo.index') }}" class="alert-link">Visualizza tutti i prodotti</a>
                </div>
            </div>
        </div>
    @endif

    {{-- Messaggio risultati ricerca --}}
    @if(request('search') || request('categoria') || request('filter'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm py-2">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Risultati di ricerca:</strong>
                                    Trovati <span class="badge bg-primary">{{ $prodotti->total() }}</span> prodotti
                                    @if(request('search'))
                                        per "<em class="text-primary">{{ request('search') }}</em>"
                                    @endif
                                    @if(request('categoria'))
                                        nella categoria "<em class="text-primary">{{ request('categoria') }}</em>"
                                    @endif
                                    @if(request('filter'))
                                        con filtro "<em class="text-primary">{{ request('filter') }}</em>"
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end mt-2 mt-lg-0">
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Rimuovi filtri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Griglia prodotti con stile pubblico --}}
    <div class="row g-3 mb-4" id="prodotti-grid">
        {{-- Sezione della card prodotto con classi dinamiche per i bordi --}}
@forelse($prodotti as $prodotto)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        {{-- 
            Card prodotto con classi CSS dinamiche per i bordi
            Le classi vengono applicate in base allo stato del prodotto:
            - border-danger-subtle: prodotti con malfunzionamenti critici
            - border-warning-subtle: prodotti con malfunzionamenti non critici
            - border-success-subtle: prodotti senza malfunzionamenti
            - border-info-subtle: prodotti assegnati allo staff corrente
        --}}
        <div class="card h-100 shadow-sm border-0 product-card 
            {{-- Applica classe CSS in base al numero e gravità dei malfunzionamenti --}}
            @if($prodotto->hasMalfunzionamentiCritici())
                border-danger-subtle
            @elseif($prodotto->malfunzionamenti_count > 0)
                border-warning-subtle
            @elseif(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                border-info-subtle
            @else
                border-success-subtle
            @endif
            {{-- Classe aggiuntiva per priorità alta se necessario --}}
            @if(isset($prodotto->critici_count) && $prodotto->critici_count > 2)
                priority-high
            @endif
        ">
            
            {{-- Resto del contenuto della card rimane invariato --}}
            <div class="position-relative overflow-hidden">
                @if($prodotto->foto)
                    <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                         class="card-img-top product-image" 
                         alt="{{ $prodotto->nome }}"
                         style="height: 160px; object-fit: cover;">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                         style="height: 160px;">
                        <i class="bi bi-box text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                @endif
                
                {{-- Badge categoria --}}
                <div class="position-absolute top-0 start-0 m-2">
                    <span class="badge bg-secondary bg-opacity-90 px-2 py-1">
                        <i class="bi bi-tag me-1"></i>{{ $prodotto->categoria_label ?? ucfirst($prodotto->categoria) }}
                    </span>
                </div>

                {{-- Indicatori tecnici --}}
                <div class="position-absolute top-0 end-0 m-2">
                    @if($prodotto->malfunzionamenti_count > 0)
                        <span class="badge bg-warning bg-opacity-90 px-2 py-1 mb-1 d-block" 
                              data-bs-toggle="tooltip" 
                              title="{{ $prodotto->malfunzionamenti_count }} malfunzionamenti totali">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $prodotto->malfunzionamenti_count }}
                        </span>
                    @endif
                    
                    @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                        <span class="badge bg-danger bg-opacity-90 px-2 py-1 mb-1 d-block" 
                              data-bs-toggle="tooltip" 
                              title="{{ $prodotto->critici_count }} malfunzionamenti critici">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $prodotto->critici_count }}
                        </span>
                    @endif
                    
                    @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                        <span class="badge bg-success bg-opacity-90 px-2 py-1 d-block" 
                              data-bs-toggle="tooltip" 
                              title="Prodotto assegnato a te">
                            <i class="bi bi-person-check"></i>
                        </span>
                    @endif
                </div>

                {{-- Indicatore priorità critica con stile aggiornato --}}
                @if($prodotto->hasMalfunzionamentiCritici())
                    <div class="position-absolute bottom-0 start-0 end-0 bg-danger bg-opacity-75 text-white text-center py-1">
                        <small><i class="bi bi-exclamation-triangle me-1"></i><strong>PRIORITÀ ALTA</strong></small>
                    </div>
                @endif
            </div>

            {{-- Corpo della card rimane invariato --}}
            <div class="card-body d-flex flex-column p-3">
                {{-- Titolo con colore dinamico basato sui bordi --}}
                <h6 class="card-title mb-2 fw-bold
                    @if($prodotto->hasMalfunzionamentiCritici())
                        text-danger
                    @elseif($prodotto->malfunzionamenti_count > 0)
                        text-warning
                    @elseif(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                        text-info
                    @else
                        text-primary
                    @endif
                ">
                    {{ $prodotto->nome }}
                </h6>
                
                {{-- Resto del contenuto... --}}
                @if($prodotto->modello)
                    <p class="card-text small text-muted mb-2">
                        <i class="bi bi-gear me-1"></i>Modello: <code>{{ $prodotto->modello }}</code>
                    </p>
                @endif

                <p class="card-text flex-grow-1 text-muted small">
                    {{ Str::limit($prodotto->descrizione, 80, '...') }}
                </p>

                {{-- Informazioni tecniche con indicatori colorati --}}
                <div class="row g-1 mb-2 small">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded
                            @if($prodotto->hasMalfunzionamentiCritici())
                                border border-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                border border-warning
                            @endif
                        ">
                            <strong class="text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                {{ $prodotto->malfunzionamenti_count ?? 0 }}
                            </strong>
                            <br><small class="text-muted">Problemi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded
                            @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                border border-danger
                            @endif
                        ">
                            <strong class="text-{{ isset($prodotto->critici_count) && $prodotto->critici_count > 0 ? 'danger' : 'success' }}">
                                {{ $prodotto->critici_count ?? 0 }}
                            </strong>
                            <br><small class="text-muted">Critici</small>
                        </div>
                    </div>
                </div>

                {{-- Staff assegnato --}}
                @if($prodotto->staffAssegnato)
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person-badge me-1"></i>
                        Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                    </p>
                @elseif(auth()->user()->isAdmin())
                    <p class="text-warning small mb-2">
                        <i class="bi bi-person-x me-1"></i>
                        Nessun staff assegnato
                    </p>
                @endif

                {{-- Alert problemi critici con stile abbinato --}}
                @if($prodotto->hasMalfunzionamentiCritici())
                    <div class="alert alert-danger py-1 mb-2 border-danger">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>ATTENZIONE:</strong> Problemi critici
                        </small>
                    </div>
                @endif

                {{-- Pulsanti azione con stili abbinati ai bordi --}}
                <div class="d-grid gap-1">
                    {{-- Visualizza dettagli con colore dinamico --}}
                    <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                       class="btn btn-sm
                            @if($prodotto->hasMalfunzionamentiCritici())
                                btn-outline-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                btn-outline-warning
                            @elseif(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                btn-outline-info
                            @else
                                btn-outline-primary
                            @endif
                       ">
                        <i class="bi bi-eye me-1"></i>Dettagli Completi
                    </a>
                    
                    {{-- Altri pulsanti rimangono invariati --}}
                    @if($prodotto->malfunzionamenti_count > 0)
                        <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                           class="btn btn-{{ $prodotto->hasMalfunzionamentiCritici() ? 'danger' : 'warning' }} btn-sm">
                            <i class="bi bi-tools me-1"></i>
                            Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                        </a>
                    @endif
                    
                    {{-- Resto dei pulsanti... --}}
                </div>
            </div>
        </div>
    </div>
@empty
    {{-- Stato vuoto rimane invariato --}}
@endforelse
    </div>

    {{-- Paginazione centrata come richiesto --}}
    @if($prodotti->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                {{-- Info paginazione centrata sopra --}}
                <div class="text-center mb-2">
                    <small class="text-muted">
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                        @if(request('staff_filter') === 'my_products')
                            assegnati
                        @endif
                    </small>
                </div>
                
                {{-- Controlli paginazione centrati --}}
                <div class="d-flex justify-content-center">
                    <nav aria-label="Paginazione prodotti">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous --}}
                            @if ($prodotti->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                </li>
                            @endif

                            {{-- Numeri pagina --}}
                            @foreach ($prodotti->getUrlRange(1, $prodotti->lastPage()) as $page => $url)
                                @if ($page == $prodotti->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($prodotti->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->nextPageUrl() }}">›</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    @endif

    {{-- Sezione informazioni compatta --}}
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h5 class="text-primary mb-2">
                                <i class="bi bi-info-circle me-2"></i>
                                Accesso Tecnico Completo
                            </h5>
                            <p class="mb-3 text-muted">
                                Stai visualizzando il catalogo con informazioni complete su malfunzionamenti e soluzioni tecniche.
                            </p>
                            
                            {{-- Pulsanti azione --}}
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Dashboard
                                </a>
                                
                                @if(auth()->user()->isStaff())
                                    <a href="{{ route('staff.create.nuova.soluzione') }}" class="btn btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Nuova Soluzione
                                    </a>
                                @endif
                                
                                <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-eye me-1"></i>
                                    Vista Pubblica
                                </a>
                                
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Centri Assistenza
                                </a>
                            </div>
                        </div>
                    </div>

</div>
@endsection

{{-- CSS personalizzato per layout identico al pubblico --}}
@push('styles')
<style>
/* === STILI CARD PRODOTTO CON BORDI === */

/* Card prodotto base con bordo elegante */
.product-card {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    overflow: hidden;
    /* NUOVO: Bordo sottile per tutte le card */
    border: 1px solid #e9ecef !important;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 2rem rgba(0,0,0,0.15) !important;
    /* NUOVO: Bordo blu al hover */
    border-color: #007bff !important;
}

/* Card con problemi critici - bordo rosso */
.product-card.border-danger-subtle {
    border-left: 4px solid #dc3545 !important;
    border-top: 1px solid #fecaca !important;
    border-right: 1px solid #fecaca !important;
    border-bottom: 1px solid #fecaca !important;
    background-color: #fef7f7;
}

.product-card.border-danger-subtle:hover {
    border-color: #dc3545 !important;
    box-shadow: 0 0.75rem 2rem rgba(220, 53, 69, 0.2) !important;
}

/* Card con problemi non critici - bordo arancione */
.product-card.border-warning-subtle {
    border-left: 4px solid #ffc107 !important;
    border-top: 1px solid #fff3cd !important;
    border-right: 1px solid #fff3cd !important;
    border-bottom: 1px solid #fff3cd !important;
    background-color: #fffbf0;
}

.product-card.border-warning-subtle:hover {
    border-color: #ffc107 !important;
    box-shadow: 0 0.75rem 2rem rgba(255, 193, 7, 0.2) !important;
}

/* Card senza problemi - bordo verde sottile */
.product-card.border-success-subtle {
    border-left: 3px solid #28a745 !important;
    border-top: 1px solid #d4edda !important;
    border-right: 1px solid #d4edda !important;
    border-bottom: 1px solid #d4edda !important;
}

.product-card.border-success-subtle:hover {
    border-color: #28a745 !important;
    box-shadow: 0 0.75rem 2rem rgba(40, 167, 69, 0.15) !important;
}

/* Card assegnata allo staff - bordo viola */
.product-card.border-info-subtle {
    border-left: 3px solid #17a2b8 !important;
    border-top: 1px solid #bee5eb !important;
    border-right: 1px solid #bee5eb !important;
    border-bottom: 1px solid #bee5eb !important;
}

.product-card.border-info-subtle:hover {
    border-color: #17a2b8 !important;
    box-shadow: 0 0.75rem 2rem rgba(23, 162, 184, 0.15) !important;
}

/* Varianti alternative per diversi stati */

/* Stile 1: Bordo completo colorato */
.product-card.full-border-critical {
    border: 2px solid #dc3545 !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
}

.product-card.full-border-warning {
    border: 2px solid #ffc107 !important;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.1);
}

.product-card.full-border-success {
    border: 2px solid #28a745 !important;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
}

/* Stile 2: Bordo superiore colorato */
.product-card.top-border-critical {
    border-top: 4px solid #dc3545 !important;
    border-left: 1px solid #e9ecef !important;
    border-right: 1px solid #e9ecef !important;
    border-bottom: 1px solid #e9ecef !important;
}

.product-card.top-border-warning {
    border-top: 4px solid #ffc107 !important;
    border-left: 1px solid #e9ecef !important;
    border-right: 1px solid #e9ecef !important;
    border-bottom: 1px solid #e9ecef !important;
}

.product-card.top-border-success {
    border-top: 4px solid #28a745 !important;
    border-left: 1px solid #e9ecef !important;
    border-right: 1px solid #e9ecef !important;
    border-bottom: 1px solid #e9ecef !important;
}

/* Stile 3: Bordo con gradiente */
.product-card.gradient-border-critical {
    border: 2px solid transparent !important;
    background: 
        linear-gradient(white, white) padding-box,
        linear-gradient(135deg, #dc3545, #ff6b6b) border-box;
}

.product-card.gradient-border-warning {
    border: 2px solid transparent !important;
    background: 
        linear-gradient(white, white) padding-box,
        linear-gradient(135deg, #ffc107, #ffeb3b) border-box;
}

/* Effetti speciali per card priorità alta */
.product-card.priority-high {
    border: 2px solid #dc3545 !important;
    position: relative;
}

.product-card.priority-high::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #dc3545, #ff6b6b, #dc3545);
    border-radius: 0.5rem;
    z-index: -1;
    animation: priorityPulse 2s ease-in-out infinite;
}

@keyframes priorityPulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

/* Responsive - bordi più sottili su mobile */
@media (max-width: 768px) {
    .product-card {
        border-width: 1px !important;
    }
    
    .product-card.border-danger-subtle,
    .product-card.border-warning-subtle,
    .product-card.border-success-subtle,
    .product-card.border-info-subtle {
        border-left-width: 3px !important;
    }
    
    .product-card.full-border-critical,
    .product-card.full-border-warning,
    .product-card.full-border-success {
        border-width: 1px !important;
    }
    
    .product-card.top-border-critical,
    .product-card.top-border-warning,
    .product-card.top-border-success {
        border-top-width: 3px !important;
    }
}

/* Stili per card normali senza classificazione speciale */
.product-card:not(.border-danger-subtle):not(.border-warning-subtle):not(.border-success-subtle):not(.border-info-subtle) {
    border: 1px solid #dee2e6 !important;
}

.product-card:not(.border-danger-subtle):not(.border-warning-subtle):not(.border-success-subtle):not(.border-info-subtle):hover {
    border-color: #007bff !important;
}

/* Classe utile per evidenziare card selezionate */
.product-card.selected {
    border: 2px solid #007bff !important;
    background-color: #f8f9ff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
}
</style>
@endpush

{{-- JavaScript identico al pubblico con aggiunte tecniche --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Inizializzazione
    console.log('Catalogo tecnico caricato - Prodotti:', {{ $prodotti->total() }});
    
    // Gestione form identica al pubblico
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    // Submit automatico categoria
    $('#categoria, #filter').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Shortcut tastiera
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
    });
    
    // Gestione errori immagini
    $('.product-image').on('error', function() {
        $(this).replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 160px;">
                <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
            </div>
        `);
    });
    
    // Loading per form submit
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                  .prop('disabled', true);
        
        setTimeout(function() {
            $submitBtn.html(originalText).prop('disabled', false);
        }, 3000);
    });
    
    // === FUNZIONALITÀ TECNICHE AGGIUNTIVE ===
    
    // Tooltip per indicatori tecnici
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Conferme eliminazione
    $('[data-confirm-delete]').on('click', function(e) {
        e.preventDefault();
        const message = $(this).data('confirm-delete');
        const form = $(this).closest('form');
        
        if (confirm(message)) {
            form.submit();
        }
    });
    
    // Evidenziazione ricerca
    const searchTerm = '{{ request("search") }}';
    if (searchTerm && !searchTerm.includes('*')) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }
    
    // Analytics tecnico
    @if(request('search'))
        console.log('Ricerca tecnica:', {
            termine: '{{ request("search") }}',
            categoria: '{{ request("categoria") }}',
            filter: '{{ request("filter") }}',
            risultati: {{ $prodotti->total() }}
        });
    @endif
});
</script>
@endpush
                        
                        