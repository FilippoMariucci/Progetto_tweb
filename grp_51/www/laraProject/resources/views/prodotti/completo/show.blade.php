{{--
    Vista completa per prodotto con malfunzionamenti (per tecnici)
    LAYOUT ORIZZONTALE identico alla vista pubblica
    Percorso: resources/views/prodotti/completo/show.blade.php
    Accessibile solo a utenti con livello_accesso >= 2
--}}

@extends('layouts.app')

@section('title', $prodotto->nome . ' - Dettagli Completi')

@section('content')
<div class="container-fluid px-4 py-3">
    
    

    {{-- === ALERT PROBLEMI CRITICI === --}}
    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
        <div class="alert alert-danger d-flex align-items-center mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div class="flex-grow-1">
                <strong>ATTENZIONE: Problemi Critici</strong> - 
                Questo prodotto ha <span class="badge bg-white text-danger">{{ $statistiche['malfunzionamenti_critici'] }}</span> 
                problema/i critico/i che richiedono intervento immediato.
            </div>
            <a href="#malfunzionamenti-section" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-down me-1"></i>Vai ai Problemi
            </a>
        </div>
    @endif

    {{-- === LAYOUT ORIZZONTALE PRINCIPALE === --}}
    <div class="row g-4">
        
        {{-- === COLONNA IMMAGINE (come pubblico) === --}}
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

                    {{-- Indicatore problemi critici --}}
                    @if(isset($statistiche) && $statistiche['malfunzionamenti_critici'] > 0)
                        <div class="position-absolute bottom-0 start-0 end-0 bg-danger bg-opacity-75 text-white text-center py-1">
                            <small><i class="bi bi-exclamation-triangle me-1"></i><strong>PRIORITÀ ALTA</strong></small>
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
            
            {{-- Info box tecnico --}}
            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-tools me-1"></i>
                        Info Tecniche
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 text-center">
                        @if($prodotto->created_at)
                            <div class="col-6">
                                <small class="text-muted d-block">Catalogato</small>
                                <strong>{{ $prodotto->created_at->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        <div class="col-6">
                            <small class="text-muted d-block">Categoria</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                        </div>
                        @if($prodotto->modello)
                            <div class="col-12">
                                <small class="text-muted d-block">Modello</small>
                                <code>{{ $prodotto->modello }}</code>
                            </div>
                        @endif
                        
                        {{-- Staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <div class="col-12">
                                <small class="text-muted d-block">Staff Assegnato</small>
                                <span class="badge bg-info">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $prodotto->staffAssegnato->nome_completo }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistiche malfunzionamenti compatte --}}
            @if(isset($statistiche) && ($showMalfunzionamenti ?? false))
                <div class="card mt-3">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up me-1"></i>
                            Statistiche Problemi
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="p-2 bg-primary bg-opacity-10 rounded">
                                    <strong class="text-primary d-block">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</strong>
                                    <small class="text-muted">Totali</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-danger bg-opacity-10 rounded">
                                    <strong class="text-danger d-block">{{ $statistiche['malfunzionamenti_critici'] ?? 0 }}</strong>
                                    <small class="text-muted">Critici</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-warning bg-opacity-10 rounded">
                                    <strong class="text-warning d-block">{{ $statistiche['malfunzionamenti_alti'] ?? 0 }}</strong>
                                    <small class="text-muted">Alta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <strong class="text-info d-block">{{ $statistiche['totale_segnalazioni'] ?? 0 }}</strong>
                                    <small class="text-muted">Segnalaz.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- === COLONNA INFORMAZIONI PRINCIPALE === --}}
        <div class="col-md-8">
            
            {{-- Header prodotto in linea --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h2 mb-1">{{ $prodotto->nome }}</h1>
                    <div class="d-flex flex-wrap gap-2">
                        @if($prodotto->modello)
                            <span class="badge bg-secondary">{{ $prodotto->modello }}</span>
                        @endif
                        
                        {{-- Badge staff assegnato --}}
                        @if($prodotto->staffAssegnato)
                            <span class="badge bg-info">
                                <i class="bi bi-person-badge me-1"></i>
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </span>
                        @endif
                        
                        {{-- Badge stato problemi --}}
                        @if(isset($statistiche))
                            @if($statistiche['malfunzionamenti_critici'] > 0)
                                <span class="badge bg-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ $statistiche['malfunzionamenti_critici'] }} Critici
                                </span>
                            @elseif($statistiche['totale_malfunzionamenti'] > 0)
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $statistiche['totale_malfunzionamenti'] }} Problemi
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Nessun Problema
                                </span>
                            @endif
                        @endif
                    </div>
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
                        Scheda Tecnica Completa
                    </h5>
                </div>
                <div class="card-body">
                    
                    {{-- Layout a colonne per scheda tecnica --}}
                    <div class="row g-4">
                        
                        {{-- Note tecniche --}}
                        @if($prodotto->note_tecniche)
                            <div class="col-lg-4">
                                <h6 class="text-primary">
                                    <i class="bi bi-gear me-1"></i>Specifiche Tecniche
                                </h6>
                                <div class="bg-light p-3 rounded border-start border-primary border-3">
                                    {!! nl2br(e($prodotto->note_tecniche)) !!}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Installazione --}}
                        @if($prodotto->modalita_installazione)
                            <div class="col-lg-4">
                                <h6 class="text-success">
                                    <i class="bi bi-tools me-1"></i>Modalità Installazione
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
                                    <i class="bi bi-book me-1"></i>Modalità d'Uso
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
                                    Scheda tecnica in aggiornamento.
                                    @if(auth()->user()->isAdmin())
                                        <br><a href="{{ route('admin.prodotti.edit', $prodotto) }}">Completa le informazioni</a>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- === SEZIONE MALFUNZIONAMENTI ORIZZONTALE === --}}
            @if(($showMalfunzionamenti ?? false))
                <div class="card bg-warning-subtle mt-3" id="malfunzionamenti-section">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Malfunzionamenti e Soluzioni Tecniche
                                @if(isset($statistiche))
                                    <span class="badge bg-dark ms-2">{{ $statistiche['totale_malfunzionamenti'] ?? 0 }}</span>
                                @endif
                            </h5>
                            
                            {{-- Azioni staff --}}
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                    <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                       class="btn btn-dark btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo
                                    </a>
                                @endif
                                
                                @if(($prodotto->malfunzionamenti ?? collect())->count() > 0)
                                    <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                       class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-list me-1"></i>Vista Completa
                                    </a>
                                @endif
                                
                                {{-- Filtri rapidi --}}
                                <div class="btn-group btn-group-sm" id="malfunzionamentoFilter">
                                    <button type="button" class="btn btn-outline-dark active" data-filter="all">Tutti</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="critica">Critici</button>
                                    <button type="button" class="btn btn-outline-dark" data-filter="recent">Recenti</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        {{-- Griglia malfunzionamenti orizzontale --}}
                        <div class="row g-3" id="malfunzionamentiList">
                            @forelse($prodotto->malfunzionamenti ?? [] as $malfunzionamento)
                                <div class="col-md-6 malfunzionamento-item" 
                                     data-gravita="{{ $malfunzionamento->gravita }}" 
                                     data-created="{{ $malfunzionamento->created_at->format('Y-m-d') }}">
                                    
                                    @php
                                        $borderColor = match($malfunzionamento->gravita) {
                                            'critica' => 'danger',
                                            'alta' => 'warning', 
                                            'media' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $badgeColor = $borderColor;
                                        
                                        $diffColors = [
                                            'facile' => 'success',
                                            'media' => 'info',
                                            'difficile' => 'warning',
                                            'esperto' => 'danger'
                                        ];
                                    @endphp
                                    
                                    <div class="card border-start border-{{ $borderColor }} border-3 h-100">
                                        <div class="card-body py-3">
                                            
                                            {{-- Header malfunzionamento --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">{{ $malfunzionamento->titolo }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $badgeColor }}">
                                                        {{ ucfirst($malfunzionamento->gravita) }}
                                                    </span>
                                                    <span class="badge bg-{{ $diffColors[$malfunzionamento->difficolta] ?? 'secondary' }} ms-1">
                                                        {{ ucfirst($malfunzionamento->difficolta) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            {{-- Descrizione --}}
                                            <p class="text-muted small mb-2">
                                                {{ Str::limit($malfunzionamento->descrizione, 100) }}
                                            </p>
                                            
                                            {{-- Statistiche --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="text-muted small">
                                                    @if($malfunzionamento->numero_segnalazioni)
                                                        <span class="badge bg-primary me-1" id="badge-{{ $malfunzionamento->id }}">
                                                            <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }} segnalazioni
                                                        </span>
                                                    @endif
                                                    
                                                    @if($malfunzionamento->tempo_stimato)
                                                        <span class="badge bg-info me-1">
                                                            <i class="bi bi-clock me-1"></i>{{ $malfunzionamento->tempo_stimato }} min
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Data ultima segnalazione --}}
                                                @if($malfunzionamento->ultima_segnalazione)
                                                    <small class="text-muted">
                                                        Ultima: {{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                            
                                            {{-- Azioni compatte --}}
                                            <div class="d-grid gap-1">
                                                {{-- Visualizza soluzione --}}
                                                <a href="{{ route('malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" 
                                                   class="btn btn-{{ $borderColor }} btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Vedi Soluzione
                                                </a>
                                                
                                                {{-- Azioni secondarie --}}
                                                <div class="btn-group btn-group-sm w-100">
                                                    {{-- Segnala problema --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning segnala-btn flex-fill"
                                                            onclick="segnalaMalfunzionamento({{ $malfunzionamento->id }})"
                                                            title="Segnala di aver riscontrato questo problema">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Segnala
                                                    </button>
                                                    
                                                    {{-- Solo per staff: modifica/elimina --}}
                                                    @if(auth()->user()->canManageMalfunzionamenti())
                                                        <a href="{{ route('staff.malfunzionamenti.edit', $malfunzionamento) }}" 
                                                           class="btn btn-outline-secondary"
                                                           title="Modifica malfunzionamento">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <form method="POST" 
                                                              action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Confermi l\'eliminazione di questo malfunzionamento?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger"
                                                                    title="Elimina malfunzionamento">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Info creatore (per staff) --}}
                                            @if($malfunzionamento->creatoBy && auth()->user()->isStaff())
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        Creato da: {{ $malfunzionamento->creatoBy->nome_completo ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                            @empty
                                {{-- Nessun malfunzionamento --}}
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <i class="bi bi-check-circle-fill text-success display-4"></i>
                                        <h4 class="text-success mt-3">Ottima notizia!</h4>
                                        <p class="text-muted">
                                            Non ci sono malfunzionamenti noti per questo prodotto.
                                        </p>
                                        
                                        {{-- Solo per staff: aggiungi primo malfunzionamento --}}
                                        @if(auth()->user()->isStaff() && $prodotto->staff_assegnato_id === auth()->id())
                                            <div class="mt-3">
                                                <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" 
                                                   class="btn btn-outline-warning">
                                                    <i class="bi bi-plus-circle me-1"></i>
                                                    Aggiungi Primo Malfunzionamento
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Link per vedere tutti --}}
                        @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 6)
                            <div class="text-center mt-4">
                                <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                                   class="btn btn-warning">
                                    <i class="bi bi-list me-1"></i>
                                    Visualizza Tutti i Malfunzionamenti ({{ $prodotto->malfunzionamenti->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            

        </div>
    </div>

    {{-- === PRODOTTI CORRELATI === --}}
    @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-collection text-info me-2"></i>
                            Prodotti Correlati nella Stessa Categoria
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($prodottiCorrelati as $correlato)
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @if($correlato->foto)
                                                    <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('prodotti.completo.show', $correlato) }}" 
                                                           class="text-decoration-none">
                                                            {{ $correlato->nome }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted d-block mb-1">
                                                        {{ $correlato->modello ?? 'N/A' }}
                                                    </small>
                                                    <div class="d-flex gap-1">
                                                        @if($correlato->malfunzionamenti_count > 0)
                                                            <span class="badge bg-warning text-dark">
                                                                {{ $correlato->malfunzionamenti_count }} problemi
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">OK</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal per immagine (identico al pubblico) --}}
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

{{-- CSS identico al pubblico con aggiunte tecniche --}}
@push('styles')
<style>
/* === LAYOUT ORIZZONTALE COMPATTO (come pubblico) === */
.card-img-top {
    transition: transform 0.2s ease;
    border-radius: 0.375rem;
}

.card-img-top:hover {
    transform: scale(1.02);
}

.card {
    border-radius: 0.375rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.badge {
    font-size: 0.75rem;
}

/* === COLORI BOOTSTRAP ORIGINALI === */
.bg-primary { background-color: #0d6efd !important; }
.bg-success { background-color: #198754 !important; }
.bg-info { background-color: #0dcaf0 !important; }
.bg-secondary { background-color: #6c757d !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-danger { background-color: #dc3545 !important; }

.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-info { color: #0dcaf0 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

.border-primary { border-color: #0d6efd !important; }
.border-success { border-color: #198754 !important; }
.border-info { border-color: #0dcaf0 !important; }
.border-warning { border-color: #ffc107 !important; }
.border-danger { border-color: #dc3545 !important; }

/* === STILI SPECIFICI TECNICI === */
.malfunzionamento-item {
    transition: all 0.3s ease;
}

.malfunzionamento-item:hover {
    transform: translateY(-2px);
}

.border-3 {
    border-width: 3px !important;
}

/* Card con bordo colorato in base alla gravità */
.card.border-start.border-danger {
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.15);
}

.card.border-start.border-warning {
    box-shadow: 0 0.125rem 0.25rem rgba(255, 193, 7, 0.15);
}

.card.border-start.border-info {
    box-shadow: 0 0.125rem 0.25rem rgba(13, 202, 240, 0.15);
}

.card.border-start.border-secondary {
    box-shadow: 0 0.125rem 0.25rem rgba(108, 117, 125, 0.15);
}

/* Statistiche con background opacity */
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

/* Spinner per loading */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Alert personalizzati */
.custom-alert {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
}

/* Hover effects identici al pubblico */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Card background special per malfunzionamenti */
.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

/* Breadcrumb personalizzato */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

/* === RESPONSIVE DESIGN === */
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
    
    /* Malfunzionamenti responsive */
    .malfunzionamento-item .btn-group {
        flex-direction: column;
    }
    
    .malfunzionamento-item .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    .custom-alert {
        position: fixed !important;
        top: 10px !important;
        left: 10px !important;
        right: 10px !important;
        min-width: auto !important;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
    }
    
    /* Filtri malfunzionamenti su mobile */
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

@media (max-width: 576px) {
    /* Full mobile layout */
    .col-md-4, .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .display-4 {
        font-size: 2rem !important;
    }
}

/* === ANIMAZIONI === */
@keyframes pulse-danger {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.border-danger.card {
    animation: pulse-danger 2s infinite;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.malfunzionamento-item {
    animation: fadeInUp 0.4s ease forwards;
}

/* === MIGLIORAMENTI ACCESSIBILITÀ === */
.btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* === STILI PER IMMAGINE PRODOTTO === */
.img-fluid.rounded.shadow-sm {
    transition: transform 0.3s ease;
}

.img-fluid.rounded.shadow-sm:hover {
    transform: scale(1.05);
}

/* === HIGHLIGHTS PER RICERCA === */
mark {
    background-color: #fff3cd;
    padding: 0 2px;
    border-radius: 2px;
}

/* === TOOLTIP STYLING === */
.tooltip .tooltip-inner {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* === PRODOTTI CORRELATI === */
.card h-100 {
    height: 100% !important;
}
</style>
@endpush

{{-- JavaScript identico al pubblico con funzionalità tecniche --}}
@push('scripts')
<script>
$(document).ready(function() {
    
    // === INIZIALIZZAZIONE ===
    console.log('Vista prodotto tecnico completo inizializzata');
    console.log('Prodotto ID:', {{ $prodotto->id }});
    console.log('Malfunzionamenti:', {{ ($prodotto->malfunzionamenti ?? collect())->count() }});
    @if(isset($statistiche))
        console.log('Critici:', {{ $statistiche['malfunzionamenti_critici'] ?? 0 }});
    @endif
    
    // === MODAL IMMAGINE (identico al pubblico) ===
    window.openImageModal = function(imageSrc, imageTitle) {
        $('#imageModalImg').attr('src', imageSrc);
        $('#imageModalTitle').text(imageTitle);
        $('#imageModal').modal('show');
    };
    
    // === FILTRI MALFUNZIONAMENTI ===
    $('#malfunzionamentoFilter button').on('click', function() {
        const filter = $(this).data('filter');
        
        // Aggiorna stato attivo pulsanti
        $('#malfunzionamentoFilter button').removeClass('active');
        $(this).addClass('active');
        
        // Applica filtro
        filterMalfunzionamenti(filter);
    });
    
    function filterMalfunzionamenti(filter) {
        const items = $('.malfunzionamento-item');
        console.log(`Applicando filtro: ${filter}, Elementi totali: ${items.length}`);
        
        if (filter === 'all') {
            items.removeClass('d-none').show();
        } else if (filter === 'critica') {
            // Filtro per gravità critica
            items.each(function() {
                const gravita = $(this).data('gravita');
                console.log(`Elemento gravita: ${gravita}`);
                if (gravita === 'critica') {
                    $(this).removeClass('d-none').show();
                } else {
                    $(this).addClass('d-none').hide();
                }
            });
        } else if (filter === 'recent') {
            // Filtro per elementi recenti (ultimi 30 giorni)
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            console.log(`Data limite per recenti: ${thirtyDaysAgo.toISOString()}`);
            
            items.each(function() {
                const createdDateStr = $(this).data('created');
                const createdDate = new Date(createdDateStr);
                console.log(`Elemento data: ${createdDateStr}, Parsed: ${createdDate.toISOString()}`);
                
                if (createdDate >= thirtyDaysAgo) {
                    $(this).removeClass('d-none').show();
                } else {
                    $(this).addClass('d-none').hide();
                }
            });
        }
        
        // Aggiorna contatori visibili
        const visibleCount = items.filter(':not(.d-none)').length;
        console.log(`Filtro applicato: ${filter}, Elementi visibili: ${visibleCount}`);
        
        // Mostra messaggio se nessun elemento visibile
        if (visibleCount === 0) {
            const noResultsMsg = `
                <div class="col-12 text-center py-4" id="no-results-message">
                    <i class="bi bi-search text-muted mb-2" style="font-size: 2rem;"></i>
                    <h5 class="text-muted">Nessun risultato per "${filter}"</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                    </button>
                </div>
            `;
            $('#no-results-message').remove();
            $('#malfunzionamentiList').append(noResultsMsg);
        } else {
            $('#no-results-message').remove();
        }
    }
    
    // Funzione per resettare i filtri
    window.resetFilters = function() {
        $('#malfunzionamentoFilter button[data-filter="all"]').click();
    };
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            return;
        }
        
        // Mostra loading sul pulsante
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...').prop('disabled', true);
        
        $.ajax({
            url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                    updateSegnalazioniCount(malfunzionamentoId, response.nuovo_count);
                    button.html(originalText).prop('disabled', false);
                } else {
                    throw new Error(response.message || 'Errore sconosciuto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore segnalazione:', {xhr, status, error});
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione.';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato.';
                } else if (xhr.status === 500) {
                    errorMsg = 'Errore interno del server. Riprova più tardi.';
                } else if (status === 'timeout') {
                    errorMsg = 'Timeout della richiesta. Controlla la connessione.';
                }
                
                showAlert(errorMsg, 'danger');
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    // === FUNZIONE AGGIORNA CONTATORE ===
    function updateSegnalazioniCount(malfunzionamentoId, newCount) {
        const badge = $(`#badge-${malfunzionamentoId}`);
        if (badge.length > 0) {
            badge.html(`<i class="bi bi-flag me-1"></i>${newCount} segnalazioni`);
        }
    }
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type = 'info', duration = 5000) {
        $('.custom-alert').remove();
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'x-circle' : 'info-circle'} me-2"></i>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.custom-alert').fadeOut(function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // === TOOLTIPS ===
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // === GESTIONE ERRORI IMMAGINI ===
    $('.card-img-top, img').on('error', function() {
        $(this).replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 300px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0">Immagine non disponibile</p>
                </div>
            </div>
        `);
    });
    
    // === ANALYTICS TECNICO ===
    console.log('Vista tecnica completa visualizzata:', {
        prodotto_id: {{ $prodotto->id }},
        nome: '{{ $prodotto->nome }}',
        categoria: '{{ $prodotto->categoria }}',
        malfunzionamenti_count: {{ ($prodotto->malfunzionamenti ?? collect())->count() }},
        user_level: {{ auth()->user()->livello_accesso }},
        timestamp: new Date().toISOString()
    });
    
    // Traccia tempo di permanenza
    let startTime = Date.now();
    
    $(window).on('beforeunload', function() {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        console.log(`Tempo trascorso sulla pagina tecnica: ${timeSpent} secondi`);
    });
    
    console.log('Vista prodotto tecnico completo pronta');
});
</script>
@endpush