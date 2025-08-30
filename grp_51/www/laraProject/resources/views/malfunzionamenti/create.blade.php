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
    
    <!-- === BREADCRUMB DINAMICO === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
            
            {{-- Se è una nuova soluzione dalla dashboard, non mostra il prodotto nel breadcrumb --}}
            @if(!isset($isNuovaSoluzione) || !$isNuovaSoluzione)
                <li class="breadcrumb-item"><a href="{{ route('prodotti.completo.show', $prodotto) }}">{{ $prodotto->nome }}</a></li>
            @endif
            
            <li class="breadcrumb-item active">
                {{ isset($isNuovaSoluzione) && $isNuovaSoluzione ? 'Nuova Soluzione' : 'Aggiungi Soluzione' }}
            </li>
        </ol>
    </nav>

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
                        
                        {{-- SELEZIONE PRODOTTO (solo per nuova soluzione dalla dashboard) --}}
                        @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                            <div class="mb-4">
                                <label for="prodotto_id" class="form-label fw-bold">
                                    <i class="bi bi-box-seam text-primary me-2"></i>
                                    Seleziona Prodotto <span class="text-danger">*</span>
                                </label>
                                
                                <select class="form-select @error('prodotto_id') is-invalid @enderror" 
                                        id="prodotto_id" 
                                        name="prodotto_id" 
                                        required>
                                    <option value="">-- Scegli un prodotto --</option>
                                    
                                    {{-- Raggruppa i prodotti per categoria per facilitare la ricerca --}}
                                    @php
                                        $prodottiGrouped = $prodotti->groupBy('categoria');
                                    @endphp
                                    
                                    @foreach($prodottiGrouped as $categoria => $prodottiCategoria)
                                        <optgroup label="{{ ucfirst($categoria) }}">
                                            @foreach($prodottiCategoria as $prod)
                                                <option value="{{ $prod->id }}" 
                                                        {{ old('prodotto_id') == $prod->id ? 'selected' : '' }}
                                                        data-categoria="{{ $prod->categoria }}"
                                                        data-modello="{{ $prod->modello }}">
                                                    {{ $prod->nome }} 
                                                    @if($prod->modello) - {{ $prod->modello }} @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                
                                @error('prodotto_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    I prodotti sono raggruppati per categoria per facilitare la ricerca
                                </div>
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
$(document).ready(function() {
    // === INIZIALIZZAZIONE ===
    console.log('Form Nuova Soluzione inizializzato');
    
    // Variabili per il controllo del form
    const form = $('#formNuovaSoluzione');
    const prodottoSelect = $('#prodotto_id');
    const titoloInput = $('#titolo');
    const gravitaSelect = $('#gravita');
    
    // === GESTIONE SELEZIONE PRODOTTO ===
    @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
    // Solo se è una nuova soluzione con selezione prodotto
    prodottoSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const prodottoNome = selectedOption.text();
        const categoria = selectedOption.data('categoria');
        
        if (selectedOption.val()) {
            // Aggiorna il titolo della pagina dinamicamente
            document.title = 'Nuova Soluzione - ' + prodottoNome;
            
            // Mostra info sul prodotto selezionato
            showProdottoInfo(selectedOption);
            
            // Focus automatico sul campo titolo
            setTimeout(() => {
                titoloInput.focus();
            }, 300);
            
            console.log('Prodotto selezionato:', {
                id: selectedOption.val(),
                nome: prodottoNome,
                categoria: categoria
            });
        } else {
            // Rimuovi info prodotto se deselezionato
            hideProdottoInfo();
        }
    });
    
    // Funzione per mostrare informazioni sul prodotto selezionato
    function showProdottoInfo(option) {
        // Rimuovi eventuali info precedenti
        $('.prodotto-info-card').remove();
        
        // Crea card informativa
        const prodottoNome = option.text();
        const categoria = option.data('categoria');
        const modello = option.data('modello');
        
        const infoHtml = `
            <div class="prodotto-info-card alert alert-light border-start border-primary border-3 mt-2 mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <div>
                        <strong>Prodotto selezionato:</strong> ${prodottoNome}<br>
                        <small class="text-muted">
                            Categoria: ${categoria}${modello ? ' - Modello: ' + modello : ''}
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        prodottoSelect.parent().after(infoHtml);
        
        // Animazione di entrata
        $('.prodotto-info-card').hide().slideDown(300);
    }
    
    // Funzione per nascondere info prodotto
    function hideProdottoInfo() {
        $('.prodotto-info-card').slideUp(200, function() {
            $(this).remove();
        });
    }
    @endif
    
    // === VALIDAZIONE IN TEMPO REALE ===
    
    // Validazione titolo
    titoloInput.on('input', function() {
        const value = $(this).val().trim();
        const length = value.length;
        
        // Aggiorna contatore caratteri
        updateCharCounter(this, length, 255);
        
        // Validazione lunghezza
        if (length > 0 && length <= 255) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
    
    // Validazione textarea (descrizione e soluzione)
    $('textarea[required]').on('input', function() {
        const value = $(this).val().trim();
        
        if (value.length > 10) { // Minimo 10 caratteri per desc/soluzione
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
    
    // Validazione select (gravità e eventuale prodotto)
    $('select[required]').on('change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
    
    // === FUNZIONI UTILITY ===
    
    // Aggiorna contatore caratteri
    function updateCharCounter(input, current, max) {
        const $input = $(input);
        let $counter = $input.siblings('.char-counter');
        
        // Crea contatore se non esiste
        if ($counter.length === 0) {
            $counter = $('<small class="char-counter text-muted float-end"></small>');
            $input.parent().append($counter);
        }
        
        // Aggiorna testo e colore
        $counter.text(`${current}/${max}`);
        
        if (current > max * 0.9) {
            $counter.removeClass('text-muted text-warning').addClass('text-danger');
        } else if (current > max * 0.8) {
            $counter.removeClass('text-muted text-danger').addClass('text-warning');
        } else {
            $counter.removeClass('text-warning text-danger').addClass('text-muted');
        }
    }
    
    // === GESTIONE INVIO FORM ===
    form.on('submit', function(e) {
        // Aggiungi classe per validazione Bootstrap
        form.addClass('was-validated');
        
        // Controlla se il form è valido
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Scorri al primo campo con errore
            const firstInvalid = form.find('.is-invalid, :invalid').first();
            if (firstInvalid.length) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
                firstInvalid.focus();
            }
            
            // Mostra messaggio di errore
            showFormMessage('Compila tutti i campi obbligatori correttamente.', 'danger');
            
            return false;
        }
        
        // Form valido - mostra loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
               .html('<i class="bi bi-hourglass-split me-2"></i>Salvataggio...');
        
        // Se tutto ok, il form viene inviato normalmente
        console.log('Form inviato con successo');
    });
    
    // === FUNZIONI MESSAGGIO ===
    function showFormMessage(message, type = 'info') {
        // Rimuovi messaggi precedenti
        $('.form-message').remove();
        
        // Crea nuovo messaggio
        const alertHtml = `
            <div class="form-message alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        form.prepend(alertHtml);
        
        // Scorri al messaggio
        $('html, body').animate({
            scrollTop: $('.form-message').offset().top - 50
        }, 300);
        
        // Auto-hide dopo 5 secondi per messaggi info
        if (type === 'info') {
            setTimeout(() => {
                $('.form-message').alert('close');
            }, 5000);
        }
    }
    
    // === AUTO-SAVE DRAFT (funzionalità avanzata) ===
    let autoSaveTimer;
    const AUTOSAVE_DELAY = 30000; // 30 secondi
    
    // Monitora modifiche nei campi principali per auto-save
    form.find('input, textarea, select').on('input change', function() {
        clearTimeout(autoSaveTimer);
        
        autoSaveTimer = setTimeout(() => {
            saveDraft();
        }, AUTOSAVE_DELAY);
    });
    
    // Funzione per salvare bozza (localStorage)
    function saveDraft() {
        const formData = {
            titolo: $('#titolo').val(),
            descrizione: $('#descrizione').val(),
            soluzione: $('#soluzione').val(),
            gravita: $('#gravita').val(),
            componente_difettoso: $('#componente_difettoso').val(),
            codice_errore: $('#codice_errore').val(),
            @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
            prodotto_id: $('#prodotto_id').val(),
            @endif
            timestamp: Date.now()
        };
        
        try {
            localStorage.setItem('draft_nuova_soluzione', JSON.stringify(formData));
            console.log('Bozza salvata automaticamente');
            
            // Mostra indicatore salvataggio
            showSaveIndicator();
        } catch (e) {
            console.warn('Impossibile salvare bozza:', e);
        }
    }
    
    // Funzione per caricare bozza salvata
    function loadDraft() {
        try {
            const savedDraft = localStorage.getItem('draft_nuova_soluzione');
            if (!savedDraft) return;
            
            const draftData = JSON.parse(savedDraft);
            const now = Date.now();
            const draftAge = now - draftData.timestamp;
            
            // Carica solo se la bozza è recente (meno di 24 ore)
            if (draftAge < 86400000) {
                // Mostra opzione per ripristinare bozza
                showDraftRestoreOption(draftData);
            } else {
                // Rimuovi bozza vecchia
                localStorage.removeItem('draft_nuova_soluzione');
            }
        } catch (e) {
            console.warn('Errore caricamento bozza:', e);
            localStorage.removeItem('draft_nuova_soluzione');
        }
    }
    
    // Mostra opzione per ripristinare bozza
    function showDraftRestoreOption(draftData) {
        const restoreHtml = `
            <div class="alert alert-warning alert-dismissible" id="draft-restore-alert">
                <i class="bi bi-file-text me-2"></i>
                <strong>Bozza trovata!</strong> È stata trovata una bozza salvata automaticamente.
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="restoreDraft()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Ripristina
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deleteDraft()">
                        <i class="bi bi-trash me-1"></i>Elimina bozza
                    </button>
                </div>
            </div>
        `;
        
        form.prepend(restoreHtml);
    }
    
    // Indica salvataggio automatico
    function showSaveIndicator() {
        // Rimuovi indicatori precedenti
        $('.save-indicator').remove();
        
        const indicator = $('<small class="save-indicator text-success position-fixed" style="top: 20px; right: 20px; z-index: 1050;"><i class="bi bi-check-circle me-1"></i>Bozza salvata</small>');
        $('body').append(indicator);
        
        setTimeout(() => {
            indicator.fadeOut(300, function() {
                $(this).remove();
            });
        }, 2000);
    }
    
    // === FUNZIONI GLOBALI PER GESTIONE BOZZE ===
    window.restoreDraft = function() {
        try {
            const savedDraft = localStorage.getItem('draft_nuova_soluzione');
            const draftData = JSON.parse(savedDraft);
            
            // Ripristina i valori nei campi
            $('#titolo').val(draftData.titolo || '');
            $('#descrizione').val(draftData.descrizione || '');
            $('#soluzione').val(draftData.soluzione || '');
            $('#gravita').val(draftData.gravita || '');
            $('#componente_difettoso').val(draftData.componente_difettoso || '');
            $('#codice_errore').val(draftData.codice_errore || '');
            
            @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
            if (draftData.prodotto_id) {
                $('#prodotto_id').val(draftData.prodotto_id).trigger('change');
            }
            @endif
            
            // Rimuovi alert
            $('#draft-restore-alert').alert('close');
            
            showFormMessage('Bozza ripristinata con successo!', 'success');
            
        } catch (e) {
            console.error('Errore ripristino bozza:', e);
            showFormMessage('Errore durante il ripristino della bozza.', 'danger');
        }
    };
    
    window.deleteDraft = function() {
        localStorage.removeItem('draft_nuova_soluzione');
        $('#draft-restore-alert').alert('close');
        console.log('Bozza eliminata');
    };
    
    // === INIZIALIZZAZIONE FINALE ===
    // Carica bozza salvata se disponibile
    loadDraft();
    
    // Rimuovi bozza quando il form viene inviato con successo
    form.on('submit', function() {
        if (this.checkValidity()) {
            setTimeout(() => {
                localStorage.removeItem('draft_nuova_soluzione');
            }, 1000);
        }
    });
    
    console.log('✅ Form Nuova Soluzione completamente inizializzato');
});
</script>
@endpush