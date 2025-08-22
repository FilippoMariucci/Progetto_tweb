{{-- 
    Form per modifica prodotto esistente
    Percorso: resources/views/admin/prodotti/edit.blade.php
    Accesso: Solo livello 4 (Amministratori)
--}}

@extends('layouts.app')

@section('title', 'Modifica Prodotto - ' . $prodotto->nome)

@section('content')
<div class="container">
    
    {{-- === HEADER CON BREADCRUMB === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Breadcrumb di navigazione --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.prodotti.index') }}" class="text-decoration-none">
                            <i class="bi bi-box me-1"></i>Gestione Prodotti
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.prodotti.show', $prodotto) }}" class="text-decoration-none">
                            {{ $prodotto->nome }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Modifica</li>
                </ol>
            </nav>

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

                {{-- Pulsanti di azione --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Torna al Dettaglio
                    </a>
                    <a href="{{ route('prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-primary" 
                       target="_blank">
                        <i class="bi bi-eye me-1"></i>Vista Pubblica
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
                                <div class="mb-3">
                                    <label for="categoria" class="form-label">
                                        <i class="bi bi-grid me-1"></i>Categoria <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('categoria') is-invalid @enderror" 
                                            id="categoria" 
                                            name="categoria" 
                                            required>
                                        <option value="">-- Seleziona categoria --</option>
                                        <option value="elettrodomestici" {{ old('categoria', $prodotto->categoria) == 'elettrodomestici' ? 'selected' : '' }}>
                                            Elettrodomestici
                                        </option>
                                        <option value="informatica" {{ old('categoria', $prodotto->categoria) == 'informatica' ? 'selected' : '' }}>
                                            Informatica
                                        </option>
                                        <option value="climatizzatori" {{ old('categoria', $prodotto->categoria) == 'climatizzatori' ? 'selected' : '' }}>
                                            Climatizzatori
                                        </option>
                                        <option value="industriali" {{ old('categoria', $prodotto->categoria) == 'industriali' ? 'selected' : '' }}>
                                            Attrezzature Industriali
                                        </option>
                                        <option value="comunicazione" {{ old('categoria', $prodotto->categoria) == 'comunicazione' ? 'selected' : '' }}>
                                            Apparati Comunicazione
                                        </option>
                                        <option value="sanitarie" {{ old('categoria', $prodotto->categoria) == 'sanitarie' ? 'selected' : '' }}>
                                            Attrezzature Sanitarie
                                        </option>
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

            {{-- Card azioni rapide --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('prodotti.show', $prodotto) }}" 
                           class="btn btn-outline-primary btn-sm" 
                           target="_blank">
                            <i class="bi bi-eye me-1"></i>Vista Pubblica
                        </a>
                        
                        @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 0)
                        <a href="{{ route('malfunzionamenti.index', $prodotto) }}" 
                           class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-bug me-1"></i>
                            Gestisci Malfunzionamenti ({{ $prodotto->malfunzionamenti->count() }})
                        </a>
                        @endif
                        
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
document.addEventListener('DOMContentLoaded', function() {
    
    // === CONFIGURAZIONE ===
    const form = document.getElementById('editProdottoForm');
    const submitBtn = document.getElementById('submitBtn');
    const fotoInput = document.getElementById('foto');
    
    // === ANTEPRIMA NUOVA IMMAGINE ===
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validazione file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato file non supportato. Usa JPEG, PNG, JPG o GIF.');
                    fotoInput.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('Il file è troppo grande. Massimo 2MB.');
                    fotoInput.value = '';
                    return;
                }
                
                // Crea anteprima
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentImg = document.getElementById('currentImage');
                    if (currentImg) {
                        currentImg.src = e.target.result;
                        // Aggiorna il testo sotto l'immagine
                        const caption = currentImg.nextElementSibling;
                        if (caption) {
                            caption.innerHTML = '<small class="text-success"><i class="bi bi-upload me-1"></i>Nuova foto (anteprima)</small>';
                        }
                    } else {
                        // Crea nuova anteprima se non esiste immagine attuale
                        const preview = document.createElement('div');
                        preview.className = 'mb-3';
                        preview.innerHTML = `
                            <div class="position-relative d-inline-block">
                                <img src="${e.target.result}" 
                                     alt="Anteprima nuova foto"
                                     class="img-thumbnail"
                                     style="max-height: 200px; max-width: 100%;"
                                     id="newImagePreview">
                                <div class="mt-2">
                                    <small class="text-success">
                                        <i class="bi bi-upload me-1"></i>Nuova foto (anteprima)
                                    </small>
                                </div>
                            </div>
                        `;
                        fotoInput.parentNode.insertBefore(preview, fotoInput);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // === VALIDAZIONE FORM ===
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            // Rimuovi classi di errore precedenti
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Controlla campi obbligatori
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            // Validazione prezzo
            const prezzoInput = document.getElementById('prezzo');
            if (prezzoInput && prezzoInput.value && parseFloat(prezzoInput.value) < 0) {
                prezzoInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Mostra messaggio di errore
                showNotification('error', 'Compila tutti i campi obbligatori correttamente');
                
                // Scroll al primo campo con errore
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                
                return false;
            }
            
            // Mostra stato di caricamento
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
            }
            
            // Conferma salvataggio
            if (!confirm('Salvare le modifiche al prodotto?')) {
                e.preventDefault();
                resetSubmitButton();
                return false;
            }
        });
    }
    
    // === FUNZIONI HELPER ===
    
    function resetSubmitButton() {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-loading');
            submitBtn.innerHTML = '<i class="bi bi-check me-1"></i>Salva Modifiche';
        }
    }
    
    // === GESTIONE MODIFICHE NON SALVATE ===
    let hasUnsavedChanges = false;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        const originalValue = input.value || input.checked;
        
        input.addEventListener('change', function() {
            const currentValue = this.type === 'checkbox' ? this.checked : this.value;
            hasUnsavedChanges = (currentValue !== originalValue);
        });
    });
    
    // Avviso prima di uscire con modifiche non salvate
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire?';
            return e.returnValue;
        }
    });
    
    // Reset flag quando si salva
    form.addEventListener('submit', function() {
        hasUnsavedChanges = false;
    });
    
    // === AUTO-SAVE IN SESSION STORAGE ===
    function autoSave() {
        const formData = {};
        formInputs.forEach(input => {
            if (input.type === 'checkbox') {
                formData[input.name] = input.checked;
            } else if (input.type !== 'file') {
                formData[input.name] = input.value;
            }
        });
        
        sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(formData));
    }
    
    // Salva automaticamente ogni 30 secondi
    setInterval(autoSave, 30000);
    
    // Carica dati salvati se esistono
    const savedData = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}');
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            
            // Chiedi se ripristinare i dati
            if (confirm('Sono stati trovati dati non salvati. Vuoi ripristinarli?')) {
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = data[key];
                        } else {
                            input.value = data[key];
                        }
                    }
                });
                
                showNotification('info', 'Dati ripristinati dalla sessione precedente');
            } else {
                // Pulisci i dati salvati se l'utente non li vuole
                sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
            }
        } catch (e) {
            console.warn('Errore nel ripristino dati auto-salvati:', e);
            sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        }
    }
    
    // === CONTATORE CARATTERI PER TEXTAREA ===
    const textareas = form.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.maxLength;
        if (maxLength > 0) {
            // Crea contatore
            const counter = document.createElement('small');
            counter.className = 'form-text text-muted';
            counter.innerHTML = `<span id="${textarea.id}_count">${textarea.value.length}</span>/${maxLength} caratteri`;
            
            textarea.parentNode.appendChild(counter);
            
            // Aggiorna contatore
            textarea.addEventListener('input', function() {
                const count = this.value.length;
                const countSpan = document.getElementById(`${this.id}_count`);
                if (countSpan) {
                    countSpan.textContent = count;
                    countSpan.parentElement.className = count > maxLength * 0.9 
                        ? 'form-text text-warning' 
                        : 'form-text text-muted';
                }
            });
        }
    });
    
    // === SHORTCUTS TASTIERA ===
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S per salvare
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.submit();
        }
        
        // Ctrl/Cmd + K per focus sulla ricerca (se presente)
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const firstInput = form.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
        }
        
        // Escape per tornare indietro
        if (e.key === 'Escape') {
            if (hasUnsavedChanges) {
                if (confirm('Hai modifiche non salvate. Tornare indietro?')) {
                    window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
                }
            } else {
                window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
            }
        }
    });
    
    // === VALIDAZIONE IN TEMPO REALE ===
    
    // Modello univoco - validazione semplificata
    const modelloInput = document.getElementById('modello');
    if (modelloInput) {
        modelloInput.addEventListener('input', function() {
            const modello = this.value.trim();
            
            // Rimuovi caratteri non validi
            if (modello) {
                // Validazione formato modello (lettere, numeri, trattini)
                if (!/^[a-zA-Z0-9\-_]+$/.test(modello)) {
                    this.classList.add('is-invalid');
                    showNotification('warning', 'Il modello può contenere solo lettere, numeri e trattini');
                } else {
                    this.classList.remove('is-invalid');
                }
            }
        });
    }
    
    // === TOOLTIPS E POPOVERS ===
    
    // Inizializza tooltips Bootstrap se disponibile
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
    
    // === NOTIFICAZIONI ===
    @if(session('success'))
        showNotification('success', '{{ session("success") }}');
    @endif
    
    @if(session('error'))
        showNotification('error', '{{ session("error") }}');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showNotification('error', '{{ $error }}');
        @endforeach
    @endif
    
    // === PULIZIA AL SUBMIT RIUSCITO ===
    
    // Se il form è stato inviato con successo, pulisci auto-save
    @if(session('success'))
        sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
    @endif
    
    console.log('🎉 Form modifica prodotto inizializzato correttamente');
});

// === FUNZIONI GLOBALI ===

/**
 * Sistema di notificazioni
 */
function showNotification(type, message, duration = 5000) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });
        
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    } else {
        // Fallback ad alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Conferma prima di uscire dalla pagina
 */
function confirmExit(message = 'Hai modifiche non salvate. Sei sicuro di voler uscire?') {
    const hasUnsavedChanges = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}') !== null;
    
    if (hasUnsavedChanges) {
        return confirm(message);
    }
    
    return true;
}

/**
 * Anteprima immagine da URL
 */
function previewImageFromUrl(url, targetId = 'currentImage') {
    const img = document.getElementById(targetId);
    if (img) {
        img.src = url;
        
        // Aggiungi effetto di caricamento
        img.style.opacity = '0.5';
        img.onload = function() {
            this.style.opacity = '1';
        };
    }
}

/**
 * Reset form ai valori originali
 */
function resetForm() {
    if (confirm('Ripristinare tutti i campi ai valori originali?')) {
        document.getElementById('editProdottoForm').reset();
        
        // Ripristina immagine originale se presente
        const currentImg = document.getElementById('currentImage');
        if (currentImg) {
            currentImg.src = '{{ $prodotto->foto ? asset("storage/" . $prodotto->foto) : "" }}';
        }
        
        // Pulisci auto-save
        sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        
        showNotification('info', 'Form ripristinato ai valori originali');
    }
}

/**
 * Salva bozza
 */
function salvaBozza() {
    const form = document.getElementById('editProdottoForm');
    const formData = new FormData(form);
    
    // Converti in oggetto per storage
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (key !== '_token' && key !== '_method' && key !== 'foto') {
            data[key] = value;
        }
    }
    
    sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(data));
    showNotification('success', 'Bozza salvata automaticamente');
}

/**
 * Controllo accessibilità
 */
function checkAccessibility() {
    const form = document.getElementById('editProdottoForm');
    const issues = [];
    
    // Controlla labels
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        const label = form.querySelector(`label[for="${input.id}"]`);
        if (!label && !input.getAttribute('aria-label')) {
            issues.push(`Input ${input.name} senza label`);
        }
    });
    
    // Controlla required fields
    const required = form.querySelectorAll('[required]');
    required.forEach(field => {
        const label = form.querySelector(`label[for="${field.id}"]`);
        if (label && !label.textContent.includes('*')) {
            issues.push(`Campo obbligatorio ${field.name} non indicato visivamente`);
        }
    });
    
    if (issues.length > 0) {
        console.warn('Problemi di accessibilità trovati:', issues);
    } else {
        console.log('✅ Nessun problema di accessibilità rilevato');
    }
    
    return issues;
}

// Esegui controllo accessibilità in ambiente di sviluppo
@if(config('app.debug'))
    setTimeout(checkAccessibility, 1000);
@endif

/**
 * Debug informazioni form
 */
@if(config('app.debug'))
function debugFormInfo() {
    const form = document.getElementById('editProdottoForm');
    
    console.group('🔧 Debug Form Modifica Prodotto');
    console.log('Form element:', form);
    console.log('Prodotto ID:', {{ $prodotto->id }});
    console.log('Prodotto Nome:', '{{ $prodotto->nome }}');
    console.log('Action URL:', form.action);
    console.log('Method:', form.method);
    
    // Campi form
    const formData = new FormData(form);
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    // Validazione
    const required = form.querySelectorAll('[required]');
    console.log('Campi obbligatori:', required.length);
    
    // Auto-save status
    const autoSaveData = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}');
    console.log('Auto-save data presente:', !!autoSaveData);
    
    console.groupEnd();
}

// Attiva debug con Ctrl+Shift+D
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        debugFormInfo();
    }
});
@endif
</script>
@endpush