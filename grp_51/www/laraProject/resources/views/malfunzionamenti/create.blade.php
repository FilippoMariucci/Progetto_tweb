{{-- Vista per creare nuovo malfunzionamento/soluzione (Staff) --}}
@extends('layouts.app')

{{-- Titolo dinamico: se $prodotto è null, è una "Nuova Soluzione", altrimenti specifica il prodotto --}}
@section('title', 
    isset($isNuovaSoluzione) && $isNuovaSoluzione 
        ? 'Nuova Soluzione - Seleziona Prodotto' 
        : 'Aggiungi Soluzione - ' . $prodotto->nome
)

@section('content')
<div class="container mt-4">
    
   

    <!-- === HEADER DINAMICO === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-plus-circle text-success me-3 fs-2"></i>
                <div>
                    @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                        {{-- Header per nuova soluzione dalla dashboard --}}
                        <h1 class="h2 mb-1">Aggiungi Nuova Soluzione</h1>
                        <p class="text-muted mb-0">
                            Seleziona un prodotto e descrivi il problema con la relativa soluzione
                        </p>
                    @else
                        {{-- Header per soluzione specifica di un prodotto --}}
                        <h1 class="h2 mb-1">Aggiungi Nuova Soluzione</h1>
                        <p class="text-muted mb-0">
                            Prodotto: <strong>{{ $prodotto->nome }}</strong> - {{ $prodotto->modello }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Alert Informativo -->
            <div class="alert alert-info border-start border-info border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Istruzioni:</strong> 
                @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                    Seleziona il prodotto interessato e descrivi accuratamente il problema con una soluzione dettagliata 
                    per aiutare altri tecnici a risolverlo rapidamente.
                @else
                    Descrivi accuratamente il problema e fornisci una soluzione dettagliata 
                    per aiutare altri tecnici a risolverlo rapidamente.
                @endif
            </div>
        </div>
    </div>

    <!-- === FORM PRINCIPALE === -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ isset($isNuovaSoluzione) && $isNuovaSoluzione ? 'Nuova Soluzione' : 'Soluzione per ' . $prodotto->nome }}
                    </h5>
                </div>
                
                <div class="card-body">
                    {{-- FORM con action dinamica --}}
                    <form method="POST" 
                          action="{{ isset($isNuovaSoluzione) && $isNuovaSoluzione 
                                      ? route('staff.store.nuova.soluzione') 
                                      : route('staff.malfunzionamenti.store', $prodotto) }}" 
                          id="formNuovaSoluzione">
                        @csrf
                        
                        {{-- 
    SEZIONE SELEZIONE PRODOTTO COMPLETA - SOLO PRODOTTI ASSEGNATI ALLO STAFF
    Sostituisce la sezione originale nel template malfunzionamenti/create.blade.php
--}}
@if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
    
    {{-- Alert informativo per sistema di assegnazione --}}
    <div class="alert alert-success border-start border-success border-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-check me-3 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-1">Sistema di Assegnazione Attivo</h6>
                <p class="mb-0">
                    Come membro dello staff, puoi creare soluzioni <strong>solo per i prodotti che ti sono stati assegnati</strong> dall'amministratore. 
                    Questo garantisce una gestione organizzata e responsabile del catalogo prodotti.
                </p>
            </div>
        </div>
    </div>

    {{-- Statistiche prodotti assegnati --}}
    @if(isset($statsAssegnati) && $statsAssegnati['totale'] > 0)
        <div class="card bg-light border-0 mb-4">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h6 class="mb-2">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            Riepilogo dei Tuoi Prodotti Assegnati
                        </h6>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="bi bi-box me-1"></i>{{ $statsAssegnati['totale'] }} Totali
                            </span>
                            @if($statsAssegnati['con_problemi'] > 0)
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $statsAssegnati['con_problemi'] }} Con Problemi
                                </span>
                            @endif
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>{{ $statsAssegnati['senza_problemi'] }} Senza Problemi
                            </span>
                            <span class="badge bg-info px-3 py-2">
                                <i class="bi bi-collection me-1"></i>{{ $statsAssegnati['per_categoria']->count() }} Categorie
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end mt-2 mt-lg-0">
                        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list me-1"></i>Gestisci i Miei Prodotti
                        </a>
                    </div>
                </div>
                
                {{-- Distribuzione per categoria se ci sono più categorie --}}
                @if($statsAssegnati['per_categoria']->count() > 1)
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted fw-semibold d-block mb-2">Distribuzione per categoria:</small>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($statsAssegnati['per_categoria'] as $categoria => $count)
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $categoria)) }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Campo di selezione prodotto --}}
    <div class="mb-4">
        <label for="prodotto_id" class="form-label fw-bold">
            <i class="bi bi-box-seam text-primary me-2"></i>
            Seleziona Prodotto Assegnato <span class="text-danger">*</span>
        </label>
        
        {{-- Verifica che ci siano prodotti assegnati disponibili --}}
        @if(isset($prodotti) && $prodotti->count() > 0)
            <select class="form-select @error('prodotto_id') is-invalid @enderror" 
                    id="prodotto_id" 
                    name="prodotto_id" 
                    required>
                <option value="">-- Scegli tra i tuoi {{ $prodotti->count() }} prodotti assegnati --</option>
                
                {{-- Raggruppa i prodotti per categoria per facilitare la ricerca --}}
                @php
                    $prodottiGrouped = $prodotti->groupBy('categoria');
                @endphp
                
                @foreach($prodottiGrouped as $categoria => $prodottiCategoria)
                    <optgroup label="🏷️ {{ ucfirst(str_replace('_', ' ', $categoria)) }} ({{ $prodottiCategoria->count() }} prodotti)">
                        @foreach($prodottiCategoria as $prod)
                            @php
                                $problemiCount = $prod->malfunzionamenti->count() ?? 0;
                                $criticiCount = $prod->malfunzionamenti->where('gravita', 'critica')->count() ?? 0;
                            @endphp
                            
                            <option value="{{ $prod->id }}" 
                                    {{ old('prodotto_id') == $prod->id ? 'selected' : '' }}
                                    data-categoria="{{ $prod->categoria }}"
                                    data-modello="{{ $prod->modello }}"
                                    data-problemi="{{ $problemiCount }}"
                                    data-critici="{{ $criticiCount }}"
                                    data-codice="{{ $prod->codice ?? '' }}">
                                
                                {{ $prod->nome }}
                                @if($prod->modello) - {{ $prod->modello }} @endif
                                @if($prod->codice) [{{ $prod->codice }}] @endif
                                
                                {{-- Indicatori stato problemi --}}
                                @if($problemiCount > 0)
                                    ({{ $problemiCount }} 
@if($problemiCount == 1)
    problema
@else
    problemi
@endif
@if($criticiCount > 0)
    - {{ $criticiCount }} 
    @if($criticiCount == 1)
        critico
    @else
        critici
    @endif
@endif
)
                                    (nessun problema noto)
                                @endif
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            
            @error('prodotto_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            <div class="form-text">
                <div class="d-flex align-items-start">
                    <i class="bi bi-lightbulb text-warning me-2 mt-1"></i>
                    <div>
                        <strong>Suggerimenti per la selezione:</strong>
                        <ul class="mb-0 mt-1 small">
                            <li>I prodotti sono raggruppati per categoria per facilitare la ricerca</li>
                            <li>I numeri tra parentesi indicano i problemi già noti per quel prodotto</li>
                            <li>Considera di prioritizzare prodotti con molti problemi o critici</li>
                            <li>Se non vedi il prodotto che cerchi, contatta l'amministratore</li>
                        </ul>
                    </div>
                </div>
            </div>
            
        @else
            {{-- Caso: nessun prodotto assegnato --}}
            <div class="alert alert-warning border-start border-warning border-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle me-3 fs-4 text-warning"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2">Nessun Prodotto Assegnato</h6>
                        <p class="mb-3">
                            <strong>{{ auth()->user()->nome_completo ?? auth()->user()->username }}</strong>, 
                            non hai prodotti assegnati per la gestione delle soluzioni tecniche.
                        </p>
                        <p class="mb-3 small">
                            Per creare nuove soluzioni, è necessario che l'amministratore ti assegni almeno un prodotto. 
                            Questo sistema garantisce una gestione organizzata dove ogni membro dello staff 
                            è responsabile di specifici prodotti del catalogo.
                        </p>
                        
                        {{-- Azioni per risolvere --}}
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                            </a>
                            @if(Route::has('contatti'))
                                <a href="{{ route('contatti') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Contatta l'Amministratore
                                </a>
                            @endif
                            @if(Route::has('staff.prodotti.assegnati'))
                                <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-list me-1"></i>Verifica Assegnazioni
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
        @endif
    </div>
    
    {{-- Prodotto info card (popolata dinamicamente via JavaScript) --}}
    <div id="prodotto-info-container">
        {{-- Qui verrà inserita dinamicamente l'info card del prodotto selezionato --}}
    </div>
    
@endif

                        {{-- TITOLO DEL MALFUNZIONAMENTO --}}
                        <div class="mb-3">
                            <label for="titolo" class="form-label fw-bold">
                                <i class="bi bi-type text-primary me-2"></i>
                                Titolo del Problema <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('titolo') is-invalid @enderror" 
                                   id="titolo" 
                                   name="titolo" 
                                   value="{{ old('titolo') }}"
                                   placeholder="es. Perdita di pressione nel circuito principale"
                                   maxlength="255"
                                   required>
                            
                            @error('titolo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Inserisci un titolo chiaro e descrittivo del problema (max 255 caratteri)
                            </div>
                        </div>

                        {{-- GRAVITÀ --}}
                        <div class="mb-3">
                            <label for="gravita" class="form-label fw-bold">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Livello di Gravità <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('gravita') is-invalid @enderror" 
                                    id="gravita" 
                                    name="gravita" 
                                    required>
                                <option value="">-- Seleziona gravità --</option>
                                <option value="bassa" {{ old('gravita') == 'bassa' ? 'selected' : '' }}>
                                    🟢 Bassa - Problema minore, non compromette il funzionamento
                                </option>
                                <option value="media" {{ old('gravita') == 'media' ? 'selected' : '' }}>
                                    🟡 Media - Riduce l'efficienza, richiede intervento programmato
                                </option>
                                <option value="alta" {{ old('gravita') == 'alta' ? 'selected' : '' }}>
                                    🔴 Alta - Compromette il funzionamento, intervento urgente
                                </option>
                            </select>
                            
                            @error('gravita')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESCRIZIONE DEL PROBLEMA --}}
                        <div class="mb-3">
                            <label for="descrizione" class="form-label fw-bold">
                                <i class="bi bi-file-text text-primary me-2"></i>
                                Descrizione del Problema <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4"
                                      placeholder="Descrivi dettagliatamente il malfunzionamento: sintomi, quando si verifica, condizioni specifiche..."
                                      required>{{ old('descrizione') }}</textarea>
                            
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Fornisci tutti i dettagli utili per identificare il problema: sintomi, frequenza, condizioni di utilizzo
                            </div>
                        </div>

                        {{-- COMPONENTE DIFETTOSO (opzionale) --}}
                        <div class="mb-3">
                            <label for="componente_difettoso" class="form-label fw-bold">
                                <i class="bi bi-gear text-secondary me-2"></i>
                                Componente Coinvolto <span class="text-muted">(opzionale)</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('componente_difettoso') is-invalid @enderror" 
                                   id="componente_difettoso" 
                                   name="componente_difettoso" 
                                   value="{{ old('componente_difettoso') }}"
                                   placeholder="es. Scheda elettronica, Sensore temperatura, Motore principale..."
                                   maxlength="255">
                            
                            @error('componente_difettoso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Specifica quale componente è coinvolto nel malfunzionamento (se noto)
                            </div>
                        </div>

                        {{-- CODICE ERRORE (opzionale) --}}
                        <div class="mb-3">
                            <label for="codice_errore" class="form-label fw-bold">
                                <i class="bi bi-hash text-secondary me-2"></i>
                                Codice di Errore <span class="text-muted">(opzionale)</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('codice_errore') is-invalid @enderror" 
                                   id="codice_errore" 
                                   name="codice_errore" 
                                   value="{{ old('codice_errore') }}"
                                   placeholder="es. E01, ERR_235, F12..."
                                   maxlength="50">
                            
                            @error('codice_errore')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Inserisci l'eventuale codice di errore mostrato dall'apparecchio
                            </div>
                        </div>

                        {{-- SOLUZIONE TECNICA --}}
                        <div class="mb-4">
                            <label for="soluzione" class="form-label fw-bold">
                                <i class="bi bi-tools text-success me-2"></i>
                                Soluzione Tecnica <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('soluzione') is-invalid @enderror" 
                                      id="soluzione" 
                                      name="soluzione" 
                                      rows="6"
                                      placeholder="Descrivi step-by-step la procedura per risolvere il problema:&#10;1. Primo passaggio...&#10;2. Secondo passaggio...&#10;&#10;Include materiali necessari, attrezzi, precauzioni di sicurezza..."
                                      required>{{ old('soluzione') }}</textarea>
                            
                            @error('soluzione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                <strong>Importante:</strong> Fornisci una procedura dettagliata e completa. Include:
                                <ul class="mb-0 mt-1">
                                    <li>Passaggi numerati in sequenza logica</li>
                                    <li>Strumenti e materiali necessari</li>
                                    <li>Precauzioni di sicurezza</li>
                                    <li>Controlli da effettuare dopo l'intervento</li>
                                </ul>
                            </div>
                        </div>

                        {{-- PULSANTI DI AZIONE --}}
                        <div class="d-flex gap-2 flex-wrap">
                            {{-- Pulsante Salva --}}
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Salva Soluzione
                            </button>
                            
                            {{-- Pulsante Annulla con URL dinamico --}}
                            @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Annulla
                                </a>
                            @else
                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Annulla
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Stili CSS specifici per questa pagina --}}
@push('styles')
<style>
/* Migliora la visualizzazione delle select con optgroup */
optgroup {
    font-weight: bold;
    color: #6c757d;
    background-color: #f8f9fa;
}

optgroup option {
    font-weight: normal;
    color: #212529;
    padding-left: 1rem;
}

/* Stili per i form controls */
.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

/* Badge per le icone nei placeholder */
.form-text {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Stili per gli alert */
.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border-color: rgba(13, 202, 240, 0.2);
}

/* Validazione visiva dinamica */
.was-validated .form-control:valid,
.was-validated .form-select:valid {
    border-color: #198754;
}

.was-validated .form-control:invalid,
.was-validated .form-select:invalid {
    border-color: #dc3545;
}

/* Hover effect per le opzioni del select */
.form-select option:hover {
    background-color: #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

{{-- JavaScript per validazione e UX migliorata --}}
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