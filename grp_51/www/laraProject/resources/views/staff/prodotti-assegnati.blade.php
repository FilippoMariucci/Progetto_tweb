{{--
    Vista Prodotti Assegnati - Staff Aziendale
    
    Funzionalità Opzionale: Ripartizione gestione prodotti tra staff
    Accessibile solo a utenti con livello_accesso >= 3 (Staff)
    
    Route: GET /staff/prodotti-assegnati
    Controller: StaffController@prodottiAssegnati
    Middleware: auth, check.level:3
    
    Funzionalità:
    - Visualizza prodotti assegnati all'utente corrente
    - Filtri per categoria e criticità
    - Ricerca nei prodotti assegnati
    - Azioni di gestione malfunzionamenti
    - Statistiche sui prodotti assegnati
--}}

@extends('layouts.app')

@section('title', 'I Miei Prodotti Assegnati')

@section('content')
<div class="container mt-4">
    
    {{-- === HEADER PAGINA === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-person-check text-info me-2"></i>
                        I Miei Prodotti Assegnati
                    </h1>
                    <p class="text-muted mb-0">
                        Gestisci i prodotti sotto la tua responsabilità (Funzionalità Opzionale)
                    </p>
                </div>
                
                {{-- Azioni rapide --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                    <a href="{{ route('prodotti.completo.index') }}" class="btn btn-warning">
                        <i class="bi bi-collection me-1"></i>Catalogo Completo
                    </a>
                </div>
            </div>
            
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
                    <li class="breadcrumb-item active">Prodotti Assegnati</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === --}}
    @if(isset($stats) && count($stats) > 0)
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-box display-4 mb-2"></i>
                    <h3 class="mb-1">{{ $stats['totale_assegnati'] ?? 0 }}</h3>
                    <small>Prodotti Totali</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle display-4 mb-2"></i>
                    <h3 class="mb-1">{{ $stats['con_malfunzionamenti'] ?? 0 }}</h3>
                    <small>Con Problemi</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-bug display-4 mb-2"></i>
                    <h3 class="mb-1">{{ $stats['critici'] ?? 0 }}</h3>
                    <small>Problemi Critici</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-4 mb-2"></i>
                    <h3 class="mb-1">{{ $stats['senza_malfunzionamenti'] ?? 0 }}</h3>
                    <small>Senza Problemi</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === FILTRI E RICERCA === --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel text-primary me-2"></i>
                Filtra e Cerca
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.prodotti.assegnati') }}" id="filterForm">
                <div class="row g-3">
                    
                    {{-- Ricerca testuale --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label">
                            <i class="bi bi-search me-1"></i>Cerca Prodotto
                        </label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nome, modello, codice...">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Filtro categoria --}}
                    <div class="col-md-3">
                        <label for="categoria" class="form-label">
                            <i class="bi bi-tags me-1"></i>Categoria
                        </label>
                        <select class="form-select" id="categoria" name="categoria">
                            <option value="">Tutte le categorie</option>
                            @if(isset($categorie))
                                @foreach($categorie as $categoria)
                                    <option value="{{ $categoria }}" {{ request('categoria') === $categoria ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $categoria)) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    {{-- Filtro criticità --}}
                    <div class="col-md-3">
                        <label for="solo_critici" class="form-label">
                            <i class="bi bi-exclamation-triangle me-1"></i>Filtro Speciale
                        </label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="solo_critici" 
                                   name="solo_critici" 
                                   value="1"
                                   {{ request('solo_critici') ? 'checked' : '' }}>
                            <label class="form-check-label" for="solo_critici">
                                Solo prodotti con problemi critici
                            </label>
                        </div>
                    </div>
                    
                    {{-- Ordinamento --}}
                    <div class="col-md-2">
                        <label for="sort" class="form-label">
                            <i class="bi bi-sort-down me-1"></i>Ordina per
                        </label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="nome" {{ request('sort') === 'nome' ? 'selected' : '' }}>Nome</option>
                            <option value="categoria" {{ request('sort') === 'categoria' ? 'selected' : '' }}>Categoria</option>
                            <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Ultima Modifica</option>
                        </select>
                        <select class="form-select mt-1" name="direction">
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Crescente</option>
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Decrescente</option>
                        </select>
                    </div>
                    
                </div>
                
                {{-- Pulsanti azioni filtro --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Filtra
                        </button>
                        <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reset Filtri
                        </a>
                        
                        {{-- Info risultati --}}
                        @if(isset($prodottiAssegnati) && $prodottiAssegnati->total() > 0)
                            <span class="ms-3 text-muted">
                                Trovati {{ $prodottiAssegnati->total() }} prodotti
                                @if(request()->hasAny(['search', 'categoria', 'solo_critici']))
                                    <span class="badge bg-info">Filtrati</span>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- === LISTA PRODOTTI ASSEGNATI === --}}
    @if(isset($prodottiAssegnati) && $prodottiAssegnati->count() > 0)
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list me-2"></i>
                    Prodotti Assegnati a {{ auth()->user()->nome_completo ?? auth()->user()->name }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Prodotto</th>
                                <th>Categoria</th>
                                <th class="text-center">Malfunzionamenti</th>
                                <th class="text-center">Stato</th>
                                <th>Ultima Modifica</th>
                                <th class="text-center">Azioni Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prodottiAssegnati as $prodotto)
                                <tr>
                                    {{-- Informazioni prodotto --}}
                                    <td>
                                        <div class="d-flex align-items-start">
                                            {{-- Immagine prodotto (se disponibile) --}}
                                            @if($prodotto->immagine)
                                                <img src="{{ $prodotto->immagine }}" 
                                                     alt="{{ $prodotto->nome }}" 
                                                     class="rounded me-3"
                                                     style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 48px; height: 48px;">
                                                    <i class="bi bi-box text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            {{-- Dettagli prodotto --}}
                                            <div>
                                                <h6 class="mb-1">
                                                    {{ $prodotto->nome }}
                                                    @if($prodotto->codice)
                                                        <small class="text-muted">({{ $prodotto->codice }})</small>
                                                    @endif
                                                </h6>
                                                @if($prodotto->modello)
                                                    <small class="text-muted d-block">{{ $prodotto->modello }}</small>
                                                @endif
                                                @if($prodotto->descrizione)
                                                    <small class="text-muted">
                                                        {{ \Str::limit($prodotto->descrizione, 60) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Categoria --}}
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria ?? 'N/A')) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Malfunzionamenti --}}
                                    <td class="text-center">
                                        @php
                                            $totMalf = $prodotto->malfunzionamenti->count();
                                            $critici = $prodotto->malfunzionamenti->where('gravita', 'critica')->count();
                                        @endphp
                                        
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-primary mb-1">{{ $totMalf }} totali</span>
                                            @if($critici > 0)
                                                <span class="badge bg-danger">{{ $critici }} critici</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- Stato generale --}}
                                    <td class="text-center">
                                        @php
                                            $hasProblemiCritici = $prodotto->malfunzionamenti->where('gravita', 'critica')->count() > 0;
                                            $hasProblemi = $prodotto->malfunzionamenti->count() > 0;
                                        @endphp
                                        
                                        @if($hasProblemiCritici)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Critico
                                            </span>
                                        @elseif($hasProblemi)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-circle me-1"></i>Attenzione
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>OK
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- Ultima modifica --}}
                                    <td>
                                        <small class="text-muted">
                                            {{ $prodotto->updated_at->format('d/m/Y') }}<br>
                                            {{ $prodotto->updated_at->format('H:i') }}
                                        </small>
                                    </td>
                                    
                                    {{-- Azioni Staff --}}
                                    <td class="text-center">
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            {{-- Gestisci malfunzionamenti --}}
                                            <a href="{{ route('staff.malfunzionamenti.index') }}?prodotto_id={{ $prodotto->id }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Gestisci malfunzionamenti">
                                                <i class="bi bi-tools me-1"></i>Gestisci
                                            </a>
                                            
                                            {{-- Aggiungi nuovo malfunzionamento --}}
                                            <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                               class="btn btn-success btn-sm" 
                                               title="Aggiungi nuovo malfunzionamento">
                                                <i class="bi bi-plus-circle me-1"></i>Nuovo
                                            </a>
                                            
                                            {{-- Vista tecnica completa --}}
                                            <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Vista tecnica completa">
                                                <i class="bi bi-eye me-1"></i>Visualizza
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Paginazione --}}
            @if($prodottiAssegnati->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Visualizzando {{ $prodottiAssegnati->firstItem() }}-{{ $prodottiAssegnati->lastItem() }} 
                            di {{ $prodottiAssegnati->total() }} prodotti
                        </div>
                        <div>
                            {{ $prodottiAssegnati->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
    @else
        {{-- === MESSAGGIO NESSUN PRODOTTO === --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                
                @if(request()->hasAny(['search', 'categoria', 'solo_critici']))
                    {{-- Nessun risultato per i filtri correnti --}}
                    <h3 class="text-muted">Nessun prodotto trovato</h3>
                    <p class="text-muted mb-3">
                        Non ci sono prodotti che corrispondono ai filtri selezionati.
                    </p>
                    <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Rimuovi Filtri
                    </a>
                @else
                    {{-- Nessun prodotto assegnato --}}
                    <h3 class="text-muted">Nessun Prodotto Assegnato</h3>
                    <p class="text-muted mb-3">
                        Al momento non hai prodotti assegnati sotto la tua responsabilità.
                        Contatta l'amministratore per richiedere l'assegnazione di prodotti specifici.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>Torna alla Dashboard
                        </a>
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-warning">
                            <i class="bi bi-collection me-1"></i>Esplora Catalogo
                        </a>
                    </div>
                    
                    {{-- Informazioni funzionalità opzionale --}}
                    <div class="alert alert-info mt-4 d-inline-block">
                        <h6 class="mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                            Funzionalità Opzionale Attiva
                        </h6>
                        <p class="mb-0 small">
                            La ripartizione dei prodotti tra membri dello staff è attualmente abilitata. 
                            Puoi comunque accedere al catalogo completo per consultare tutti i prodotti, 
                            ma potrai gestire malfunzionamenti solo sui prodotti a te assegnati.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- === AZIONI RAPIDE === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide Staff
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-outline-warning">
                            <i class="bi bi-list-check me-2"></i>
                            Dashboard Malfunzionamenti
                        </a>
                        <a href="{{ route('staff.statistiche') }}" class="btn btn-outline-info">
                            <i class="bi bi-graph-up me-2"></i>
                            Le Mie Statistiche
                        </a>
                        <a href="{{ route('staff.report.attivita') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-file-text me-2"></i>
                            Report Attività
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Aiuto e Supporto
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Prodotti Assegnati:</strong> Solo tu puoi gestire i malfunzionamenti
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>CRUD Completo:</strong> Crea, modifica ed elimina problemi/soluzioni
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Priorità:</strong> Gestisci prima i problemi critici
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Supporto:</strong> <a href="mailto:supporto@sistemaassistenza.it">supporto@sistemaassistenza.it</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- === JavaScript per gestione dinamica === --}}
@push('scripts')
<script>
$(document).ready(function() {
    // === CONFIGURAZIONE ===
    console.log('Prodotti Assegnati Staff caricati');
    
    // === AUTO-SUBMIT FILTRI ===
    $('#categoria, #sort, select[name="direction"]').on('change', function() {
        $('#filterForm').submit();
    });
    
    // === RICERCA SOLO MANUALE ===
    
    // Ricerca con tasto Invio
    $('#search').on('keypress', function(e) {
        if (e.which === 13) { // Tasto Invio
            e.preventDefault();
            const query = $(this).val().trim();
            console.log('Ricerca con Invio attivata per:', query);
            $('#filterForm').submit();
        }
    });
    
    // Ricerca con pulsante
    $('#searchButton').on('click', function() {
        const query = $('#search').val().trim();
        console.log('Ricerca con pulsante attivata per:', query);
        $('#filterForm').submit();
    });
    
    // === DEBUG FORM SUBMIT ===
    $('#filterForm').on('submit', function() {
        console.log('Form inviato con parametri:', $(this).serialize());
    });
    
    // === CHECKBOX CRITICI ===
    $('#solo_critici').on('change', function() {
        $('#filterForm').submit();
    });
    
    // === TOOLTIP ===
    $('[data-bs-toggle="tooltip"], [title]').tooltip();
    
    // === ANIMAZIONI HOVER CARDS ===
    $('.card').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // === HIGHLIGHT RISULTATI RICERCA ===
    const searchTerm = $('#search').val();
    if (searchTerm && searchTerm.length > 2) {
        highlightSearchResults(searchTerm);
    }
    
    function highlightSearchResults(term) {
        const regex = new RegExp(`(${term})`, 'gi');
        $('table tbody td').each(function() {
            const $td = $(this);
            if (!$td.find('a, button, .btn').length) { // Non toccare celle con link/bottoni
                const text = $td.text();
                const highlightedText = text.replace(regex, '<mark class="bg-warning">$1</mark>');
                if (text !== highlightedText) {
                    $td.html(highlightedText);
                }
            }
        });
    }
    
    // === CONFERMA AZIONI PERICOLOSE ===
    $(document).on('click', 'a[href*="delete"], button[data-action="delete"]', function(e) {
        if (!confirm('Sei sicuro di voler procedere con questa azione?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // === CONTATORI ANIMATI ===
    $('.card h3').each(function() {
        const $this = $(this);
        const finalValue = parseInt($this.text());
        
        if (!isNaN(finalValue) && finalValue > 0) {
            $this.text('0');
            $({ counter: 0 }).animate({ counter: finalValue }, {
                duration: 1000,
                step: function() {
                    $this.text(Math.ceil(this.counter));
                }
            });
        }
    });
    
    // === SHORTCUTS TASTIERA ===
    $(document).on('keydown', function(e) {
        // Solo se non stiamo scrivendo in un input
        if (!$('input, textarea, select').is(':focus')) {
            switch(e.key) {
                case 'f':
                case 'F':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        $('#search').focus();
                    }
                    break;
                case 'r':
                case 'R':
                    if (e.ctrlKey && e.shiftKey) {
                        e.preventDefault();
                        window.location.href = '{{ route("staff.prodotti.assegnati") }}';
                    }
                    break;
            }
        }
    });
    
    // === INFO SHORTCUTS ===
    console.log('🔧 Shortcuts disponibili:');
    console.log('  Ctrl+F: Focus ricerca');
    console.log('  Ctrl+Shift+R: Reset filtri');
    
    // === LOADING STATES ===
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').addClass('loading').prop('disabled', true);
    });
});
</script>
@endpush

{{-- === CSS Personalizzato === --}}
@push('styles')
<style>
/* === STILI SPECIFICI PRODOTTI ASSEGNATI === */

/* Card hover effects */
.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

/* Badge personalizzati */
.badge.bg-light {
    color: #495057 !important;
    border: 1px solid #dee2e6;
}

/* Tabella responsiva migliorata */
.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.025em;
}

.table td {
    vertical-align: middle;
}

/* Hover righe tabella */
.table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.1);
    transition: background-color 0.2s ease;
}

/* Immagini prodotto */
.table img {
    transition: transform 0.2s ease;
}

.table img:hover {
    transform: scale(1.1);
}

/* Badge status con animazioni */
.badge.bg-danger {
    animation: pulse-danger 2s infinite;
}

@keyframes pulse-danger {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

/* Bottoni gruppo verticale */
.btn-group-vertical .btn {
    border-radius: 0.25rem !important;
    margin-bottom: 2px;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

/* Loading states */
.loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid #0d6efd;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Evidenziazione risultati ricerca */
mark.bg-warning {
    background-color: #fff3cd !important;
    color: #856404 !important;
    padding: 0.1rem 0.2rem;
    border-radius: 0.25rem;
}

/* Filtri form */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Card statistiche con gradienti */
.card.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
}

.card.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%) !important;
}

.card.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
}

.card.bg-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
}

/* Miglioramenti tipografici */
.table small {
    font-size: 0.8rem;
    line-height: 1.2;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Alert personalizzati */
.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border-color: rgba(13, 202, 240, 0.2);
    color: #055160;
}

.alert-info .bi {
    color: #0dcaf0;
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
}

.breadcrumb-item.active {
    color: #495057;
    font-weight: 600;
}

/* Paginazione personalizzata */
.pagination .page-link {
    color: #0d6efd;
    border-color: #dee2e6;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Responsive miglioramenti */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .btn-group-vertical {
        width: 100%;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
    }
    
    .table th, .table td {
        font-size: 0.875rem;
        padding: 0.5rem;
    }
    
    .display-1 {
        font-size: 3rem;
    }
    
    .card-body.py-5 {
        padding: 2rem 1rem !important;
    }
}

@media (max-width: 576px) {
    .btn-group-vertical .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
    }
    
    .card .card-header h5 {
        font-size: 1rem;
    }
    
    .card .card-body h6 {
        font-size: 0.95rem;
    }
}

/* Focus ring personalizzato */
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Transizioni smooth */
.card,
.btn,
.form-control,
.form-select,
.badge,
.alert {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Miglioramenti accessibilità */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Print styles */
@media print {
    .btn,
    .card-footer,
    .alert,
    nav {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    .table th,
    .table td {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: white !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: white !important;
    }
}

/* Utilità extra */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.shadow-hover:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.border-start-thick {
    border-left: 4px solid !important;
}

.fs-7 {
    font-size: 0.875rem !important;
}

.fs-8 {
    font-size: 0.75rem !important;
}
</style>
@endpush