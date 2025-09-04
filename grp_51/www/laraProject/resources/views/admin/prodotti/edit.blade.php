{{-- 
    Form per modifica prodotto esistente
    Percorso: resources/views/admin/prodotti/edit.blade.php
    Accesso: Solo livello 4 (Amministratori)
--}}

@extends('layouts.app')

@section('title', 'Modifica Prodotto - ' . $prodotto->nome)

@section('content')
<div class="container">
    
    {{-- === HEADER  === --}}
    <div class="row mb-4">
        <div class="col-12">
            

            {{-- Header principale --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-pencil text-warning me-2"></i>
                        Modifica Prodotto
                    </h1>
                    <p class="text-muted mb-0">
                        Modifica le informazioni del prodotto: <strong>{{ $prodotto->nome }}</strong>
                    </p>
                </div>

                {{-- Pulsanti di azione (solo Torna al Dettaglio per admin) --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Torna al Dettaglio
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- === FORM MODIFICA PRODOTTO === --}}
    <div class="row">
        <div class="col-lg-8">
            
            {{-- Card principale del form --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    
                    {{-- Form di modifica --}}
                    <form action="{{ route('admin.prodotti.update', $prodotto) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="editProdottoForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            
                            {{-- === INFORMAZIONI BASE === --}}
                            <div class="col-md-6">
                                
                                {{-- Nome prodotto --}}
                                <div class="mb-3">
                                    <label for="nome" class="form-label">
                                        <i class="bi bi-tag me-1"></i>Nome Prodotto <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" 
                                           name="nome" 
                                           value="{{ old('nome', $prodotto->nome) }}"
                                           placeholder="Es. Lavatrice EcoWash 2000"
                                           required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Modello --}}
                                <div class="mb-3">
                                    <label for="modello" class="form-label">
                                        <i class="bi bi-cpu me-1"></i>Modello <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('modello') is-invalid @enderror" 
                                           id="modello" 
                                           name="modello" 
                                           value="{{ old('modello', $prodotto->modello) }}"
                                           placeholder="Es. EW-2000-ECO"
                                           required>
                                    @error('modello')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Il modello deve essere univoco nel sistema
                                    </div>
                                </div>

                                {{-- Categoria --}}
                                {{-- CORREZIONE: Sezione Categoria con Sistema Unificato nel Form Edit --}}
<div class="mb-3">
    <label for="categoria" class="form-label">
        <i class="bi bi-grid me-1"></i>Categoria <span class="text-danger">*</span>
    </label>
    <select class="form-select @error('categoria') is-invalid @enderror" 
            id="categoria" 
            name="categoria" 
            required>
        <option value="">-- Seleziona categoria --</option>
        {{-- 
            CORREZIONE PRINCIPALE: Utilizza le categorie dal controller (sistema unificato)
            invece delle categorie hardcoded 
        --}}
        @if(isset($categorie) && count($categorie) > 0)
            {{-- Usa le categorie passate dal controller (sistema unificato) --}}
            @foreach($categorie as $key => $label)
                <option value="{{ $key }}" {{ old('categoria', $prodotto->categoria) == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        @else
            {{-- 
                FALLBACK: Se per qualche motivo $categorie non è disponibile,
                usa il metodo statico del modello direttamente nella vista 
            --}}
            @php
                $categorieUnificate = \App\Models\Prodotto::getCategorieUnifico();
            @endphp
            @foreach($categorieUnificate as $key => $label)
                <option value="{{ $key }}" {{ old('categoria', $prodotto->categoria) == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        @endif
    </select>
    @error('categoria')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    {{-- Debug info solo in sviluppo --}}
    @if(config('app.debug'))
        <div class="form-text">
            <small class="text-muted">
                DEBUG: Categoria attuale = "{{ $prodotto->categoria }}" | 
                Categorie disponibili: {{ isset($categorie) ? count($categorie) : 'Non passate dal controller' }}
            </small>
        </div>
    @endif
</div>


                                {{-- Prezzo --}}
                                <div class="mb-3">
                                    <label for="prezzo" class="form-label">
                                        <i class="bi bi-currency-euro me-1"></i>Prezzo
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" 
                                               class="form-control @error('prezzo') is-invalid @enderror" 
                                               id="prezzo" 
                                               name="prezzo" 
                                               value="{{ old('prezzo', $prodotto->prezzo) }}"
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                        @error('prezzo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Lascia vuoto se il prezzo non è pubblico</div>
                                </div>

                            </div>

                            {{-- === GESTIONE IMMAGINE E STATO === --}}
                            <div class="col-md-6">
                                
                                {{-- Foto prodotto --}}
                                <div class="mb-3">
                                    <label for="foto" class="form-label">
                                        <i class="bi bi-camera me-1"></i>Foto Prodotto
                                    </label>
                                    
                                    {{-- Anteprima foto attuale --}}
                                    @if($prodotto->foto)
                                    <div class="mb-3">
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                                 alt="Foto {{ $prodotto->nome }}"
                                                 class="img-thumbnail"
                                                 style="max-height: 200px; max-width: 100%;"
                                                 id="currentImage">
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>Foto attuale
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    {{-- Input per nuova foto --}}
                                    <input type="file" 
                                           class="form-control @error('foto') is-invalid @enderror" 
                                           id="foto" 
                                           name="foto" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Formati supportati: JPEG, PNG, JPG, GIF. Max 2MB.
                                        <br>Lascia vuoto per mantenere la foto attuale.
                                    </div>
                                </div>

                                {{-- Staff assegnato --}}
                                <div class="mb-3">
                                    <label for="staff_assegnato_id" class="form-label">
                                        <i class="bi bi-person-badge me-1"></i>Staff Assegnato
                                    </label>
                                    <select class="form-select @error('staff_assegnato_id') is-invalid @enderror" 
                                            id="staff_assegnato_id" 
                                            name="staff_assegnato_id">
                                        <option value="">-- Nessuno staff assegnato --</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}" 
                                                    {{ old('staff_assegnato_id', $prodotto->staff_assegnato_id) == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->nome }} {{ $staff->cognome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('staff_assegnato_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-lightbulb me-1"></i>
                                        Lo staff può gestire i malfunzionamenti di questo prodotto
                                    </div>
                                </div>

                                {{-- Stato prodotto --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-toggle-on me-1"></i>Stato Prodotto
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('attivo') is-invalid @enderror" 
                                               type="checkbox" 
                                               id="attivo" 
                                               name="attivo" 
                                               value="1"
                                               {{ old('attivo', $prodotto->attivo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attivo">
                                            Prodotto attivo nel catalogo
                                        </label>
                                    </div>
                                    @error('attivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Se disattivato, il prodotto non sarà visibile nel catalogo pubblico
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- === DESCRIZIONI E NOTE TECNICHE === --}}
                        <hr class="my-4">
                        
                        {{-- Descrizione generale --}}
                        <div class="mb-3">
                            <label for="descrizione" class="form-label">
                                <i class="bi bi-text-left me-1"></i>Descrizione Prodotto <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4" 
                                      placeholder="Descrizione dettagliata del prodotto per il catalogo pubblico..."
                                      required>{{ old('descrizione', $prodotto->descrizione) }}</textarea>
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Questa descrizione sarà visibile nel catalogo pubblico
                            </div>
                        </div>

                        {{-- Note tecniche --}}
                        <div class="mb-3">
                            <label for="note_tecniche" class="form-label">
                                <i class="bi bi-gear me-1"></i>Note Tecniche <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('note_tecniche') is-invalid @enderror" 
                                      id="note_tecniche" 
                                      name="note_tecniche" 
                                      rows="4" 
                                      placeholder="Specifiche tecniche, caratteristiche tecniche, requisiti..."
                                      required>{{ old('note_tecniche', $prodotto->note_tecniche) }}</textarea>
                            @error('note_tecniche')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Modalità di installazione --}}
                        <div class="mb-3">
                            <label for="modalita_installazione" class="form-label">
                                <i class="bi bi-tools me-1"></i>Modalità di Installazione <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('modalita_installazione') is-invalid @enderror" 
                                      id="modalita_installazione" 
                                      name="modalita_installazione" 
                                      rows="4" 
                                      placeholder="Istruzioni passo-passo per l'installazione del prodotto..."
                                      required>{{ old('modalita_installazione', $prodotto->modalita_installazione) }}</textarea>
                            @error('modalita_installazione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Modalità d'uso --}}
                        <div class="mb-3">
                            <label for="modalita_uso" class="form-label">
                                <i class="bi bi-book me-1"></i>Modalità d'Uso
                            </label>
                            <textarea class="form-control @error('modalita_uso') is-invalid @enderror" 
                                      id="modalita_uso" 
                                      name="modalita_uso" 
                                      rows="4" 
                                      placeholder="Istruzioni per l'uso corretto del prodotto...">{{ old('modalita_uso', $prodotto->modalita_uso) }}</textarea>
                            @error('modalita_uso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Campo opzionale</div>
                        </div>

                        {{-- Pulsanti di azione --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Prodotto creato: {{ $prodotto->created_at->format('d/m/Y H:i') }}
                                    @if($prodotto->created_at != $prodotto->updated_at)
                                        <br>Ultimo aggiornamento: {{ $prodotto->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </div>
                            
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                   class="btn btn-secondary">
                                    <i class="bi bi-x me-1"></i>Annulla
                                </a>
                                <button type="submit" 
                                        class="btn btn-primary" 
                                        id="submitBtn">
                                    <i class="bi bi-check me-1"></i>Salva Modifiche
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- === SIDEBAR CON INFORMAZIONI AGGIUNTIVE === --}}
        <div class="col-lg-4">
            
            {{-- Card informazioni prodotto --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%" class="text-muted">ID:</th>
                            <td><code>{{ $prodotto->id }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Modello attuale:</th>
                            <td><strong>{{ $prodotto->modello }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Categoria:</th>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Stato:</th>
                            <td>
                                <span class="badge bg-{{ $prodotto->attivo ? 'success' : 'danger' }}">
                                    {{ $prodotto->attivo ? 'ATTIVO' : 'INATTIVO' }}
                                </span>
                            </td>
                        </tr>
                        @if($prodotto->malfunzionamenti)
                        <tr>
                            <th class="text-muted">Malfunzionamenti:</th>
                            <td>{{ $prodotto->malfunzionamenti->count() }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Card azioni rapide (senza Vista Pubblica né Gestione Malfunzionamenti per Admin) --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Admin non ha bisogno di vista pubblica secondo le specifiche --}}
                        {{-- Il catalogo pubblico è accessibile a tutti (Livello 1) --}}
                        
                        @if($prodotto->attivo)
                        <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Disattivare questo prodotto?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-pause me-1"></i>Disattiva Prodotto
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                <i class="bi bi-play me-1"></i>Attiva Prodotto
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card aiuto --}}
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="card-title mb-0 text-info">
                        <i class="bi bi-question-circle me-2"></i>
                        Aiuto
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-1"></i>
                            I campi contrassegnati con <span class="text-danger">*</span> sono obbligatori
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-camera text-info me-1"></i>
                            L'immagine deve essere massimo 2MB
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person text-info me-1"></i>
                            Lo staff assegnato può gestire i malfunzionamenti
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-eye text-info me-1"></i>
                            Solo i prodotti attivi sono visibili nel catalogo pubblico
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- === STILI CSS PERSONALIZZATI === --}}
@push('styles')
<style>
/* Form styling */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Card hover effects */
.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Image preview */
#currentImage {
    transition: transform 0.2s ease-in-out;
}

#currentImage:hover {
    transform: scale(1.05);
}

/* Button loading state */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive design */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        width: 100%;
    }
}
</style>
@endpush

{{-- === JAVASCRIPT FUNZIONALITÀ === --}}
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