{{-- 
    Vista Admin Modifica Prodotto Esistente
    File: resources/views/admin/prodotti/edit.blade.php
    Linguaggio: Blade Template (Laravel) + HTML + CSS + JavaScript
    
    FUNZIONALITÀ IMPLEMENTATE:
    - Form precompilato per modifica prodotto esistente
    - Upload immagine con anteprima esistente
    - Validazione client-side e server-side
    - Sistema categorie unificato (correzione applicata)
    - Toggle stato attivo/inattivo
    - Gestione staff assegnato (funzionalità opzionale)
    - Sidebar informativa con dettagli prodotto
    
    LIVELLO ACCESSO: Solo Amministratore (Livello 4)
    OPERAZIONE CRUD: UPDATE - Modifica record esistente
--}}

{{-- 
    EXTENDS: Estende il layout principale dell'applicazione Laravel 
    Il layout app.blade.php fornisce struttura base con navigation e footer
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo dinamico della pagina
    Combina testo statico con nome prodotto per identificazione chiara
    Utilizzato dal layout per impostare <title> nel browser
--}}
@section('title', 'Modifica Prodotto - ' . $prodotto->nome)

{{-- 
    SECTION CONTENT: Contenuto principale della vista
    Layout responsive con form principale + sidebar informativa
--}}
@section('content')
<div class="container">
    
    {{-- 
        SEZIONE HEADER PAGINA
        Header con titolo dinamico e pulsanti navigazione
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                Layout flex responsive per header
                flex-md-row: row su desktop, column su mobile
            --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-pencil text-warning me-2"></i>
                        Modifica Prodotto
                    </h1>
                    {{-- 
                        Sottotitolo con nome prodotto dinamico
                        $prodotto->nome: proprietà Eloquent model
                    --}}
                    <p class="text-muted mb-0">
                        Modifica le informazioni del prodotto: <strong>{{ $prodotto->nome }}</strong>
                    </p>
                </div>

                {{-- 
                    Pulsanti azione header
                    btn-group per raggruppamento visivo
                --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Torna al Dettaglio
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        LAYOUT PRINCIPALE A DUE COLONNE
        8/12 per form, 4/12 per sidebar su desktop
        Stack verticale su mobile
    --}}
    <div class="row">
        {{-- 
            COLONNA FORM PRINCIPALE (66% larghezza desktop)
        --}}
        <div class="col-lg-8">
            
            {{-- Card contenitore form --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    
                    {{-- 
                        FORM MODIFICA PRODOTTO
                        - action: route update con parametro modello
                        - method: POST con @method('PUT') per RESTful update
                        - enctype: multipart/form-data per upload file
                    --}}
                    <form action="{{ route('admin.prodotti.update', $prodotto) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="editProdottoForm">
                        {{-- Token CSRF Laravel per sicurezza --}}
                        @csrf
                        {{-- Method spoofing per PUT request --}}
                        @method('PUT')

                        <div class="row">
                            
                            {{-- 
                                COLONNA INFORMAZIONI BASE (50% su desktop)
                            --}}
                            <div class="col-md-6">
                                
                                {{-- Campo Nome Prodotto (obbligatorio) --}}
                                <div class="mb-3">
                                    <label for="nome" class="form-label">
                                        <i class="bi bi-tag me-1"></i>Nome Prodotto <span class="text-danger">*</span>
                                    </label>
                                    {{-- 
                                        Input con valore precompilato
                                        old('nome', $prodotto->nome): usa old() per form errors, 
                                        altrimenti valore dal database
                                    --}}
                                    <input type="text" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" 
                                           name="nome" 
                                           value="{{ old('nome', $prodotto->nome) }}"
                                           placeholder="Es. Lavatrice EcoWash 2000"
                                           required>
                                    {{-- Messaggio errore validazione Laravel --}}
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Campo Modello (obbligatorio) --}}
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
                                    {{-- Testo di aiuto informativo --}}
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Il modello deve essere univoco nel sistema
                                    </div>
                                </div>

                                {{-- 
                                    SEZIONE CATEGORIA CON CORREZIONE
                                    Sistema categorie unificato applicato al form edit
                                --}}
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
                                            CORREZIONE PRINCIPALE: Utilizza sistema categorie unificato
                                            invece di valori hardcoded nel form edit
                                        --}}
                                        @if(isset($categorie) && count($categorie) > 0)
                                            {{-- 
                                                CASO PREFERITO: Usa categorie passate dal controller
                                                Mantiene coerenza con resto applicazione
                                            --}}
                                            @foreach($categorie as $key => $label)
                                                {{-- 
                                                    selected con old() e valore database
                                                    old('categoria', $prodotto->categoria): gestisce form errors
                                                --}}
                                                <option value="{{ $key }}" {{ old('categoria', $prodotto->categoria) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        @else
                                            {{-- 
                                                FALLBACK: Se $categorie non disponibile dal controller
                                                Chiamata diretta al metodo statico modello
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
                                    
                                    {{-- 
                                        DEBUG INFO (solo in sviluppo)
                                        Aiuta debugging durante sviluppo applicazione
                                    --}}
                                    @if(config('app.debug'))
                                        <div class="form-text">
                                            <small class="text-muted">
                                                DEBUG: Categoria attuale = "{{ $prodotto->categoria }}" | 
                                                Categorie disponibili: {{ isset($categorie) ? count($categorie) : 'Non passate dal controller' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                {{-- Campo Prezzo (opzionale) --}}
                                <div class="mb-3">
                                    <label for="prezzo" class="form-label">
                                        <i class="bi bi-currency-euro me-1"></i>Prezzo
                                    </label>
                                    {{-- Input group per simbolo euro --}}
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

                            {{-- 
                                COLONNA GESTIONE IMMAGINE E STATO (50% desktop)
                            --}}
                            <div class="col-md-6">
                                
                                {{-- 
                                    SEZIONE FOTO PRODOTTO
                                    Gestisce upload nuova immagine con preview esistente
                                --}}
                                <div class="mb-3">
                                    <label for="foto" class="form-label">
                                        <i class="bi bi-camera me-1"></i>Foto Prodotto
                                    </label>
                                    
                                    {{-- 
                                        ANTEPRIMA FOTO ATTUALE
                                        Mostrata solo se prodotto ha già un'immagine
                                    --}}
                                    @if($prodotto->foto)
                                    <div class="mb-3">
                                        <div class="position-relative d-inline-block">
                                            {{-- 
                                                Immagine esistente dal storage Laravel
                                                asset('storage/'): percorso public storage
                                            --}}
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
                                    
                                    {{-- Input per nuova foto (opzionale) --}}
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

                                {{-- 
                                    CAMPO STAFF ASSEGNATO (Funzionalità Opzionale)
                                    Select per assegnare prodotto a membro staff
                                --}}
                                <div class="mb-3">
                                    <label for="staff_assegnato_id" class="form-label">
                                        <i class="bi bi-person-badge me-1"></i>Staff Assegnato
                                    </label>
                                    <select class="form-select @error('staff_assegnato_id') is-invalid @enderror" 
                                            id="staff_assegnato_id" 
                                            name="staff_assegnato_id">
                                        <option value="">-- Nessuno staff assegnato --</option>
                                        {{-- 
                                            Loop attraverso staff members dal controller
                                            $staffMembers: collection passata dal controller
                                        --}}
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

                                {{-- 
                                    CAMPO STATO PRODOTTO
                                    Toggle switch per attivare/disattivare prodotto
                                --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-toggle-on me-1"></i>Stato Prodotto
                                    </label>
                                    <div class="form-check form-switch">
                                        {{-- 
                                            Checkbox switch con stato precompilato
                                            old('attivo', $prodotto->attivo): gestisce form errors
                                            checked se valore è truthy
                                        --}}
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

                        {{-- 
                            SEZIONE DESCRIZIONI E NOTE TECNICHE
                            Separatore visivo con HR per organizzare form
                        --}}
                        <hr class="my-4">
                        
                        {{-- Campo Descrizione Generale (obbligatorio) --}}
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

                        {{-- Campo Note Tecniche (obbligatorio) --}}
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

                        {{-- Campo Modalità Installazione (obbligatorio) --}}
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

                        {{-- Campo Modalità d'Uso (opzionale) --}}
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

                        {{-- 
                            FOOTER FORM CON AZIONI
                            Layout flex per info temporali + pulsanti azione
                        --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                {{-- 
                                    Informazioni temporali prodotto
                                    Utilizza Carbon per formattazione date
                                --}}
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Prodotto creato: {{ $prodotto->created_at->format('d/m/Y H:i') }}
                                    {{-- 
                                        Mostra data modifica solo se diversa da creazione
                                        Confronto timestamp Eloquent
                                    --}}
                                    @if($prodotto->created_at != $prodotto->updated_at)
                                        <br>Ultimo aggiornamento: {{ $prodotto->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </div>
                            
                            {{-- Gruppo pulsanti azione --}}
                            <div class="btn-group" role="group">
                                {{-- Pulsante Annulla (torna al dettaglio) --}}
                                <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                   class="btn btn-secondary">
                                    <i class="bi bi-x me-1"></i>Annulla
                                </a>
                                {{-- Pulsante Submit Form --}}
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

        {{-- 
            SIDEBAR INFORMATIVA (33% larghezza desktop)
            Pannelli informativi e azioni rapide
        --}}
        <div class="col-lg-4">
            
            {{-- 
                CARD INFORMAZIONI PRODOTTO
                Tabella read-only con dettagli correnti
            --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Tabella informazioni senza bordi --}}
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
                                {{-- 
                                    Badge categoria con formattazione
                                    str_replace('_', ' '): sostituisce underscore con spazi
                                    ucfirst(): prima lettera maiuscola
                                --}}
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Stato:</th>
                            <td>
                                {{-- 
                                    Badge colorato condizionale
                                    Colore verde per attivo, rosso per inattivo
                                --}}
                                <span class="badge bg-{{ $prodotto->attivo ? 'success' : 'danger' }}">
                                    {{ $prodotto->attivo ? 'ATTIVO' : 'INATTIVO' }}
                                </span>
                            </td>
                        </tr>
                        {{-- 
                            Count malfunzionamenti (se relazione presente)
                            Condizione per evitare errori se relazione non definita
                        --}}
                        @if($prodotto->malfunzionamenti)
                        <tr>
                            <th class="text-muted">Malfunzionamenti:</th>
                            <td>{{ $prodotto->malfunzionamenti->count() }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- 
                CARD AZIONI RAPIDE
                Pulsanti per operazioni veloci su prodotto
            --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- 
                            TOGGLE STATO PRODOTTO
                            Form condizionale per attivare/disattivare
                            Logica inversa: se attivo mostra disattiva, viceversa
                        --}}
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

            {{-- 
                CARD AIUTO
                Informazioni di supporto per l'utente
            --}}
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="card-title mb-0 text-info">
                        <i class="bi bi-question-circle me-2"></i>
                        Aiuto
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Lista help items senza bullet points --}}
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

{{-- 
    PUSH STYLES: CSS personalizzato per questa vista
    Miglioramenti visual e UX specifici per form edit
--}}
@push('styles')
<style>
/**
 * FORM STYLING - CSS
 * Miglioramenti focus states e validazione visiva
 */

/* Focus states personalizzati per form controls */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Checkbox switch personalizzato per stato attivo */
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/**
 * CARD HOVER EFFECTS - CSS
 * Animazioni subtle per migliorare interattività
 */
.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/**
 * IMAGE PREVIEW STYLING - CSS
 * Hover effect per anteprima immagine esistente
 */
#currentImage {
    transition: transform 0.2s ease-in-out;
}

#currentImage:hover {
    transform: scale(1.05);
}

/**
 * BUTTON LOADING STATE - CSS
 * Spinner loading per feedback submit form
 */
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

/**
 * RESPONSIVE DESIGN - CSS Media Queries
 * Adattamenti per dispositivi mobili
 */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        width: 100%;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
        text-align: center;
    }
}

/**
 * BADGE PERSONALIZZATI - CSS
 * Colori e sizing consistenti
 */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

/**
 * TABLE STYLING - CSS
 * Miglioramenti tabella sidebar
 */
.table-borderless th,
.table-borderless td {
    border: none;
    padding: 0.375rem 0;
}

/**
 * FORM VALIDATION STATES - CSS
 * Stili migliorati per errori validazione
 */
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    font-size: 0.875rem;
    color: #dc3545;
    margin-top: 0.25rem;
}

/**
 * ICON COLORS - CSS
 * Colori semantici per icone
 */
.text-warning {
    color: #ffc107 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-success {
    color: #198754 !important;
}

/**
 * ACCESSIBILITY - CSS
 * Miglioramenti per accessibilità
 */
.form-control:focus,
.form-select:focus,
.btn:focus {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/**
 * PRINT STYLES - CSS Media Print
 * Ottimizzazioni per stampa
 */
@media print {
    .btn,
    .card-header,
    .form-text {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

{{-- 
    PUSH SCRIPTS: JavaScript per funzionalità dinamiche
    Setup form validation, image preview, loading states
--}}
@push('scripts')
<script>
/**
 * INIZIALIZZAZIONE DATI PAGINA - JavaScript
 * Configura oggetto globale window.PageData per condividere dati PHP-JS
 * Pattern utilizzato in tutta l'applicazione per consistency
 */
window.PageData = window.PageData || {};

/**
 * CONDIZIONI PHP IN JAVASCRIPT
 * Trasferisce dati dal controller PHP al contesto JavaScript
 * Solo se le variabili sono definite per evitare errori
 */

// Prodotto corrente (sempre presente in edit)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Lista prodotti (per riferimenti)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento singolo (se correlato)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti (per riferimenti)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro assistenza (se correlato)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista centri (per riferimenti)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie unificate (importante per questa vista)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members (per assegnazioni)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche (per dashboard elements)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente (per permessi)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/**
 * INIZIALIZZAZIONE DOM - JavaScript
 * Setup event listeners e funzionalità al caricamento pagina
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Vista modifica prodotto inizializzata');
    
    // Setup form validation
    setupFormValidation();
    
    // Setup image preview per nuova foto
    setupImagePreview();
    
    // Setup loading states
    setupLoadingStates();
    
    // Setup auto-save per bozze (opzionale)
    // setupAutoSave();
    
    console.log('✅ Tutti i componenti inizializzati');
});

/**
 * SETUP VALIDAZIONE FORM - JavaScript
 * Configura validazione client-side per form edit
 */
function setupFormValidation() {
    console.log('✅ Setup validazione form edit');
    
    const form = document.getElementById('editProdottoForm');
    if (!form) return;
    
    // Campi obbligatori per validazione real-time
    const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    requiredFields.forEach(field => {
        // Event listener per validazione al blur
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        // Event listener per rimozione errore su input
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    // Validazione pre-submit
    form.addEventListener('submit', function(e) {
        console.log('📤 Validazione pre-submit form edit');
        
        let isValid = true;
        
        // Valida tutti i campi obbligatori
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            console.log('❌ Validazione fallita');
            showNotification('Completa tutti i campi obbligatori', 'error');
            
            // Scroll al primo errore
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        } else {
            console.log('✅ Form edit validato, invio in corso');
        }
    });
}

/**
 * VALIDA SINGOLO CAMPO - JavaScript
 * Validazione specifica per diversi tipi di campo
 * 
 * @param {HTMLElement} field - Campo da validare
 * @returns {boolean} - True se valido
 */
function validateField(field) {
    let isValid = true;
    let errorMessage = '';
    
    // Validazione required base
    if (field.hasAttribute('required') && !field.value.trim()) {
        isValid = false;
        errorMessage = 'Questo campo è obbligatorio';
    }
    
    // Validazioni specifiche per tipo campo
    switch (field.type) {
        case 'number':
            if (field.value && (isNaN(field.value) || field.value < 0)) {
                isValid = false;
                errorMessage = 'Inserisci un numero valido';
            }
            break;
            
        case 'file':
            if (field.files.length > 0) {
                const file = field.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    isValid = false;
                    errorMessage = 'File troppo grande (max 2MB)';
                }
                
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    errorMessage = 'Formato file non supportato';
                }
            }
            break;
    }
    
    // Applica stili validazione
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        // Rimuovi messaggio errore custom
        const existingError = field.parentNode.querySelector('.custom-error');
        if (existingError) {
            existingError.remove();
        }
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        
        // Aggiungi messaggio errore se non esiste
        if (!field.parentNode.querySelector('.invalid-feedback')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback custom-error';
            errorDiv.textContent = errorMessage;
            field.parentNode.appendChild(errorDiv);
        }
    }
    
    return isValid;
}

/**
 * SETUP ANTEPRIMA IMMAGINE - JavaScript
 * Configura preview per nuova immagine caricata
 */
function setupImagePreview() {
    console.log('🖼️ Setup anteprima immagine');
    
    const fileInput = document.getElementById('foto');
    const currentImage = document.getElementById('currentImage');
    
    if (!fileInput) return;
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (!file) return;
        
        // Validazione file
        if (!validateField(fileInput)) {
            return;
        }
        
        // FileReader per preview
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (currentImage) {
                // Sostituisce immagine esistente
                currentImage.src = e.target.result;
                currentImage.alt = 'Nuova foto ' + file.name;
                
                // Mostra notifica sostituzione
                showNotification('Nuova immagine selezionata. Salva per confermare.', 'info');
            } else {
                // Crea nuova preview se non esiste immagine
                createImagePreview(e.target.result, file.name);
            }
            
            console.log('✅ Preview immagine aggiornata');
        };
        
        reader.onerror = function() {
            console.error('❌ Errore lettura immagine');
            showNotification('Errore nel caricamento immagine', 'error');
        };
        
        reader.readAsDataURL(file);
    });
}

/**
 * CREA ANTEPRIMA IMMAGINE - JavaScript
 * Crea elemento preview se non esiste immagine corrente
 * 
 * @param {string} imageSrc - Data URL immagine
 * @param {string} fileName - Nome file
 */
function createImagePreview(imageSrc, fileName) {
    const fotoField = document.getElementById('foto');
    const container = fotoField.parentNode;
    
    // Crea container preview
    const previewDiv = document.createElement('div');
    previewDiv.className = 'mb-3';
    previewDiv.innerHTML = `
        <div class="position-relative d-inline-block">
            <img src="${imageSrc}" 
                 alt="Preview ${fileName}"
                 class="img-thumbnail"
                 style="max-height: 200px; max-width: 100%;"
                 id="currentImage">
            <div class="mt-2">
                <small class="text-success">
                    <i class="bi bi-check-circle me-1"></i>Nuova foto selezionata
                </small>
            </div>
        </div>
    `;
    
    // Inserisci prima dell'input file
    container.insertBefore(previewDiv, fotoField);
}

/**
 * SETUP STATI LOADING - JavaScript
 * Configura loading states per form submit
 */
function setupLoadingStates() {
    console.log('⏳ Setup loading states');
    
    const form = document.getElementById('editProdottoForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!form || !submitBtn) return;
    
    form.addEventListener('submit', function(e) {
        // Solo se form è valido
        if (!e.defaultPrevented) {
            // Aggiungi loading state al pulsante
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            
            // Salva testo originale
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
            
            // Timeout di sicurezza per riabilitare pulsante
            setTimeout(() => {
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000); // 10 secondi max
            
            console.log('⏳ Loading state attivato');
        }
    });
}

/**
 * SISTEMA NOTIFICHE - JavaScript
 * Mostra notifiche temporanee all'utente
 * 
 * @param {string} message - Messaggio da mostrare
 * @param {string} type - Tipo: success, error, warning, info
 */
function showNotification(message, type = 'info') {
    console.log(`📢 Notifica ${type}: ${message}`);
    
    // Rimuovi notifiche esistenti
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    // Crea elemento notifica
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 0.5rem;
    `;
    
    // Icona basata su tipo
    const icons = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        warning: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    };
    
    notification.innerHTML = `
        <i class="bi ${icons[type]} me-2"></i>
        ${message}
    `;
    
    // Aggiungi al DOM
    document.body.appendChild(notification);
    
    // Rimozione automatica
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transition = 'opacity 0.3s ease';
            notification.style.opacity = '0';
            
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 4000);
}

/**
 * GESTIONE ERRORI GLOBALI - JavaScript
 * Cattura errori JavaScript per debugging
 */
window.addEventListener('error', function(e) {
    console.error('❌ Errore JavaScript:', e.error);
    
    // Solo in sviluppo mostra errori all'utente
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        showNotification('Errore JavaScript rilevato (vedi console)', 'error');
    }
});

/**
 * LOG FINALE INIZIALIZZAZIONE - JavaScript
 */
console.log('🎉 Vista modifica prodotto completamente inizializzata');
console.log('📋 Funzionalità disponibili:', {
    'Validazione form': '✅',
    'Preview immagine': '✅', 
    'Loading states': '✅',
    'Notifiche': '✅',
    'Error handling': '✅'
});

/**
 * ESPORTA UTILITY - JavaScript
 * Rende disponibili funzioni per debugging
 */
window.ProductEditUtils = {
    validateField: validateField,
    showNotification: showNotification
};
</script>
@endpush