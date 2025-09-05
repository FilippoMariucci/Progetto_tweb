@push('scripts')
<script>
window.malfunzionamento = @json($malfunzionamento);
</script>
@endpush
{{-- Vista per modificare malfunzionamento esistente (Staff) --}}
@extends('layouts.app')

@section('title', 'Modifica Soluzione - ' . $malfunzionamento->titolo)

@section('content')
<div class="container mt-4">
    
    

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-pencil-square text-warning me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Modifica Soluzione</h1>
                    <p class="text-muted mb-0">
                        Prodotto: <strong>{{ $prodotto->nome }}</strong> - {{ $prodotto->modello }}
                    </p>
                </div>
            </div>
            
            <!-- Alert Informativo -->
            <div class="alert alert-warning border-start border-warning border-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Attenzione:</strong> Le modifiche saranno visibili immediatamente ai tecnici. 
                Assicurati che tutte le informazioni siano accurate prima di salvare.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-form-text text-warning me-2"></i>
                        Modifica Dettagli Soluzione
                    </h5>
                </div>
                <div class="card-body">
                   <form action="{{ route('staff.malfunzionamenti.update', [$prodotto->id, $malfunzionamento->id]) }}" method="POST">
    @csrf
    @method('PUT')
                        
                        <!-- === INFORMAZIONI PROBLEMA === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Identificazione Problema
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Titolo -->
                        <div class="mb-3">
                            <label for="titolo" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>Titolo del Problema *
                            </label>
                            <input type="text" 
                                   class="form-control @error('titolo') is-invalid @enderror" 
                                   id="titolo" 
                                   name="titolo" 
                                   value="{{ old('titolo', $malfunzionamento->titolo) }}"
                                   placeholder="es: Lavatrice non centrifuga correttamente"
                                   required 
                                   maxlength="255">
                            <div class="form-text">Sii conciso ma specifico nel descrivere il problema</div>
                            @error('titolo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Descrizione Problema -->
                        <div class="mb-3">
                            <label for="descrizione" class="form-label fw-semibold">
                                <i class="bi bi-file-text me-1"></i>Descrizione Dettagliata *
                            </label>
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4" 
                                      required
                                      placeholder="Descrivi in dettaglio il problema, quando si verifica, sintomi osservati...">{{ old('descrizione', $malfunzionamento->descrizione) }}</textarea>
                            <div class="form-text">Include tutti i dettagli che possono essere utili per la diagnosi</div>
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Gravità e Difficoltà -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gravita" class="form-label fw-semibold">
                                    <i class="bi bi-speedometer2 me-1"></i>Gravità del Problema *
                                </label>
                                <select class="form-select @error('gravita') is-invalid @enderror" 
                                        id="gravita" 
                                        name="gravita" 
                                        required>
                                    <option value="">Seleziona gravità</option>
                                    <option value="bassa" {{ old('gravita', $malfunzionamento->gravita) == 'bassa' ? 'selected' : '' }}>
                                        🟢 Bassa - Problema minore
                                    </option>
                                    <option value="media" {{ old('gravita', $malfunzionamento->gravita) == 'media' ? 'selected' : '' }}>
                                        🟡 Media - Funzionalità compromessa
                                    </option>
                                    <option value="alta" {{ old('gravita', $malfunzionamento->gravita) == 'alta' ? 'selected' : '' }}>
                                        🟠 Alta - Problema significativo
                                    </option>
                                    <option value="critica" {{ old('gravita', $malfunzionamento->gravita) == 'critica' ? 'selected' : '' }}>
                                        🔴 Critica - Prodotto inutilizzabile
                                    </option>
                                </select>
                                @error('gravita')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="difficolta" class="form-label fw-semibold">
                                    <i class="bi bi-star me-1"></i>Difficoltà Riparazione *
                                </label>
                                <select class="form-select @error('difficolta') is-invalid @enderror" 
                                        id="difficolta" 
                                        name="difficolta" 
                                        required>
                                    <option value="">Seleziona difficoltà</option>
                                    <option value="facile" {{ old('difficolta', $malfunzionamento->difficolta) == 'facile' ? 'selected' : '' }}>
                                        ⭐ Facile - Tecnico junior
                                    </option>
                                    <option value="media" {{ old('difficolta', $malfunzionamento->difficolta) == 'media' ? 'selected' : '' }}>
                                        ⭐⭐ Media - Tecnico esperto
                                    </option>
                                    <option value="difficile" {{ old('difficolta', $malfunzionamento->difficolta) == 'difficile' ? 'selected' : '' }}>
                                        ⭐⭐⭐ Difficile - Tecnico specializzato
                                    </option>
                                    <option value="esperto" {{ old('difficolta', $malfunzionamento->difficolta) == 'esperto' ? 'selected' : '' }}>
                                        ⭐⭐⭐⭐ Esperto - Solo tecnici certificati
                                    </option>
                                </select>
                                @error('difficolta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === SOLUZIONE === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-tools me-2"></i>Procedura di Risoluzione
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Soluzione Dettagliata -->
                        <div class="mb-3">
                            <label for="soluzione" class="form-label fw-semibold">
                                <i class="bi bi-list-ol me-1"></i>Procedura Risolutiva *
                            </label>
                            <textarea class="form-control @error('soluzione') is-invalid @enderror" 
                                      id="soluzione" 
                                      name="soluzione" 
                                      rows="6" 
                                      required
                                      placeholder="1. Spegnere la lavatrice e scollegarla dalla rete elettrica&#10;2. Controllare il filtro della pompa di scarico...&#10;3. Verificare la cinghia di trasmissione...&#10;4. Testare il funzionamento...">{{ old('soluzione', $malfunzionamento->soluzione) }}</textarea>
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Numera i passaggi per renderli più chiari (1. 2. 3...)
                            </div>
                            @error('soluzione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Strumenti e Tempo -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="strumenti_necessari" class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Strumenti Necessari
                                </label>
                                <input type="text" 
                                       class="form-control @error('strumenti_necessari') is-invalid @enderror" 
                                       id="strumenti_necessari" 
                                       name="strumenti_necessari" 
                                       value="{{ old('strumenti_necessari', $malfunzionamento->strumenti_necessari) }}"
                                       placeholder="es: Cacciavite a croce, Multimetro, Chiave inglese 10mm">
                                <div class="form-text">Elenca gli strumenti separati da virgole</div>
                                @error('strumenti_necessari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="tempo_stimato" class="form-label fw-semibold">
                                    <i class="bi bi-clock me-1"></i>Tempo Stimato (minuti)
                                </label>
                                <input type="number" 
                                       class="form-control @error('tempo_stimato') is-invalid @enderror" 
                                       id="tempo_stimato" 
                                       name="tempo_stimato" 
                                       value="{{ old('tempo_stimato', $malfunzionamento->tempo_stimato) }}"
                                       min="1" 
                                       max="999" 
                                       placeholder="30">
                                @error('tempo_stimato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === STATISTICHE === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-graph-up me-2"></i>Informazioni Statistiche
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="numero_segnalazioni" class="form-label">
                                    <i class="bi bi-flag me-1"></i>Numero Segnalazioni
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="numero_segnalazioni" 
                                       name="numero_segnalazioni" 
                                       value="{{ old('numero_segnalazioni', $malfunzionamento->numero_segnalazioni) }}"
                                       min="1" 
                                       max="999">
                                <div class="form-text">Quante volte è stato segnalato</div>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="prima_segnalazione" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Prima Segnalazione
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="prima_segnalazione" 
                                       name="prima_segnalazione" 
                                       value="{{ old('prima_segnalazione', $malfunzionamento->prima_segnalazione?->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="ultima_segnalazione" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>Ultima Segnalazione
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="ultima_segnalazione" 
                                       name="ultima_segnalazione" 
                                       value="{{ old('ultima_segnalazione', $malfunzionamento->ultima_segnalazione?->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="{{ route('staff.malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                <button type="button" class="btn btn-outline-danger me-2" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i>Elimina
                                </button>
                                <button type="submit" class="btn btn-warning" id="updateBtn">
                                    <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR INFORMATIVA === -->
        <div class="col-lg-4">
            
            <!-- Info Soluzione Corrente -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>Stato Attuale
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-1">{{ $malfunzionamento->numero_segnalazioni ?? 0 }}</h5>
                                <small class="text-muted">Segnalazioni</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <span class="badge bg-{{ $malfunzionamento->gravita == 'critica' ? 'danger' : ($malfunzionamento->gravita == 'alta' ? 'warning' : 'info') }}">
                                    {{ ucfirst($malfunzionamento->gravita) }}
                                </span>
                                <div><small class="text-muted">Gravità</small></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-1">{{ $malfunzionamento->tempo_formattato }}</h6>
                            <small class="text-muted">Tempo</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small">
                        <p class="mb-2">
                            <strong>Creato da:</strong> 
                            {{ $malfunzionamento->creatoBy?->nome_completo ?? 'Sistema' }}
                        </p>
                        <p class="mb-2">
                            <strong>Creato il:</strong> 
                            {{ $malfunzionamento->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                            <p class="mb-0">
                                <strong>Ultima modifica:</strong> 
                                {{ $malfunzionamento->updated_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Info Prodotto -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-box text-primary me-2"></i>Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $prodotto->foto_url }}" 
                             class="rounded me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;"
                             alt="{{ $prodotto->nome }}">
                        <div>
                            <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                            <small class="text-muted">{{ $prodotto->modello }}</small>
                            <br>
                            <span class="badge bg-primary">{{ $prodotto->categoria_label }}</span>
                        </div>
                    </div>
                    
                    <div class="small">
                        <p class="mb-2">
                            <strong>Problemi totali:</strong> 
                            <span class="badge bg-warning">{{ $prodotto->malfunzionamenti->count() }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Critici:</strong>
                            <span class="badge bg-danger">
                                {{ $prodotto->malfunzionamenti->where('gravita', 'critica')->count() }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Visualizza Prodotto
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Cronologia Modifiche -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history text-secondary me-2"></i>Cronologia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Creazione</h6>
                                <p class="timeline-text">{{ $malfunzionamento->created_at->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">
                                    da {{ $malfunzionamento->creatoBy?->nome_completo ?? 'Sistema' }}
                                </small>
                            </div>
                        </div>
                        
                        @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ultima Modifica</h6>
                                    <p class="timeline-text">{{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}</p>
                                    <small class="text-muted">
                                        da {{ $malfunzionamento->modificatoBy?->nome_completo ?? 'Sistema' }}
                                    </small>
                                </div>
                            </div>
                        @endif
                        
                        @if($malfunzionamento->ultima_segnalazione)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ultima Segnalazione</h6>
                                    <p class="timeline-text">{{ $malfunzionamento->ultima_segnalazione->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Link Utili -->
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-info me-2"></i>Link Utili
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-list me-1"></i>Tutte le Soluzioni
                        </a>
                        <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus me-1"></i>Nuova Soluzione
                        </a>
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Dashboard Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL ANTEPRIMA === -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Modifiche
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Il contenuto verrà popolato via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-warning" id="updateFromPreview">
                    <i class="bi bi-check-circle me-1"></i>Conferma Modifiche
                </button>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL CONFERMA ELIMINAZIONE === -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Attenzione!</strong> Stai per eliminare definitivamente questa soluzione.
                </div>
                
                <p>Sei sicuro di voler eliminare la soluzione:</p>
                <blockquote class="blockquote">
                    <p class="mb-2"><strong>"{{ $malfunzionamento->titolo }}"</strong></p>
                    <footer class="blockquote-footer">
                        {{ $prodotto->nome }} - {{ $prodotto->modello }}
                    </footer>
                </blockquote>
                
                <p class="text-danger">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <strong>Questa azione non può essere annullata.</strong>
                </p>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDelete">
                    <label class="form-check-label" for="confirmDelete">
                        Confermo di voler eliminare questa soluzione
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="bi bi-trash me-1"></i>Elimina Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
}

.border-start.border-4 {
    border-width: 4px !important;
}

.badge {
    font-size: 0.75rem;
}

/* Timeline styling */
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 5px;
}

.timeline-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 2px;
}

.timeline-text {
    font-size: 0.85rem;
    margin-bottom: 2px;
}

/* Anteprima styling */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #ffc107;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #ffc107;
    margin-bottom: 0.5rem;
}

/* Modifiche evidenziate */
.highlight-change {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
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

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush