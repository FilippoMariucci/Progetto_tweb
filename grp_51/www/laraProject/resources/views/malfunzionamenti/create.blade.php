{{-- Vista per creare nuovo malfunzionamento/soluzione (Staff) --}}
@extends('layouts.app')

@section('title', 'Aggiungi Soluzione - ' . $prodotto->nome)

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard Staff</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prodotti.completo.show', $prodotto) }}">{{ $prodotto->nome }}</a></li>
            <li class="breadcrumb-item active">Aggiungi Soluzione</li>
        </ol>
    </nav>

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-plus-circle text-success me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Aggiungi Nuova Soluzione</h1>
                    <p class="text-muted mb-0">
                        Prodotto: <strong>{{ $prodotto->nome }}</strong> - {{ $prodotto->modello }}
                    </p>
                </div>
            </div>
            
            <!-- Alert Informativo -->
            <div class="alert alert-info border-start border-info border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Istruzioni:</strong> Descrivi accuratamente il problema e fornisci una soluzione dettagliata 
                per aiutare altri tecnici a risolverlo rapidamente.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-form-text text-primary me-2"></i>
                        Dettagli Soluzione
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.malfunzionamenti.store', $prodotto) }}" method="POST" id="soluzioneForm">
                        @csrf
                        
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
                                   value="{{ old('titolo') }}"
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
                                      placeholder="Descrivi in dettaglio il problema, quando si verifica, sintomi osservati...">{{ old('descrizione') }}</textarea>
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
                                    <option value="bassa" {{ old('gravita') == 'bassa' ? 'selected' : '' }}>
                                        🟢 Bassa - Problema minore
                                    </option>
                                    <option value="media" {{ old('gravita') == 'media' ? 'selected' : '' }}>
                                        🟡 Media - Funzionalità compromessa
                                    </option>
                                    <option value="alta" {{ old('gravita') == 'alta' ? 'selected' : '' }}>
                                        🟠 Alta - Problema significativo
                                    </option>
                                    <option value="critica" {{ old('gravita') == 'critica' ? 'selected' : '' }}>
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
                                    <option value="facile" {{ old('difficolta') == 'facile' ? 'selected' : '' }}>
                                        ⭐ Facile - Tecnico junior
                                    </option>
                                    <option value="media" {{ old('difficolta') == 'media' ? 'selected' : '' }}>
                                        ⭐⭐ Media - Tecnico esperto
                                    </option>
                                    <option value="difficile" {{ old('difficolta') == 'difficile' ? 'selected' : '' }}>
                                        ⭐⭐⭐ Difficile - Tecnico specializzato
                                    </option>
                                    <option value="esperto" {{ old('difficolta') == 'esperto' ? 'selected' : '' }}>
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
                                      placeholder="1. Spegnere la lavatrice e scollegarla dalla rete elettrica&#10;2. Controllare il filtro della pompa di scarico...&#10;3. Verificare la cinghia di trasmissione...&#10;4. Testare il funzionamento...">{{ old('soluzione') }}</textarea>
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
                                       value="{{ old('strumenti_necessari') }}"
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
                                       value="{{ old('tempo_stimato') }}"
                                       min="1" 
                                       max="999" 
                                       placeholder="30">
                                @error('tempo_stimato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === STATISTICHE INIZIALI === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-graph-up me-2"></i>Informazioni Aggiuntive (Opzionali)
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="numero_segnalazioni" class="form-label">
                                    <i class="bi bi-flag me-1"></i>Numero Segnalazioni Iniziali
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="numero_segnalazioni" 
                                       name="numero_segnalazioni" 
                                       value="{{ old('numero_segnalazioni', 1) }}"
                                       min="1" 
                                       max="999">
                                <div class="form-text">Quante volte è stato segnalato questo problema</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="prima_segnalazione" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Prima Segnalazione
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="prima_segnalazione" 
                                       name="prima_segnalazione" 
                                       value="{{ old('prima_segnalazione', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle me-1"></i>Salva Soluzione
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR INFORMATIVA === -->
        <div class="col-lg-4">
            
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
                            <strong>Problemi esistenti:</strong> 
                            <span class="badge bg-warning">{{ $prodotto->malfunzionamenti->count() }}</span>
                        </p>
                        @if($prodotto->malfunzionamenti->count() > 0)
                            <p class="mb-0">
                                <strong>Più critico:</strong>
                                <span class="badge bg-danger">
                                    {{ $prodotto->malfunzionamenti->where('gravita', 'critica')->count() }} critici
                                </span>
                            </p>
                        @endif
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Visualizza Prodotto
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Guida Rapida -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Suggerimenti
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Titolo efficace:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Usa termini tecnici specifici</li>
                                <li>Indica il sintomo principale</li>
                                <li>Evita termini generici</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Descrizione completa:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Quando si verifica</li>
                                <li>Condizioni di utilizzo</li>
                                <li>Sintomi correlati</li>
                            </ul>
                        </div>
                        
                        <div class="mb-0">
                            <strong>Soluzione dettagliata:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Passaggi numerati</li>
                                <li>Misure di sicurezza</li>
                                <li>Come verificare la riparazione</li>
                            </ul>
                        </div>
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
                        <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-list me-1"></i>Tutte le Soluzioni
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
                    <i class="bi bi-eye me-2"></i>Anteprima Soluzione
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
                <button type="button" class="btn btn-success" id="submitFromPreview">
                    <i class="bi bi-check-circle me-1"></i>Conferma e Salva
                </button>
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

/* Anteprima styling */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #007bff;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // === ANTEPRIMA SOLUZIONE ===
    $('#previewBtn').on('click', function() {
        generatePreview();
        $('#previewModal').modal('show');
    });
    
    function generatePreview() {
        const data = {
            titolo: $('#titolo').val(),
            descrizione: $('#descrizione').val(),
            gravita: $('#gravita').val(),
            difficolta: $('#difficolta').val(),
            soluzione: $('#soluzione').val(),
            strumenti_necessari: $('#strumenti_necessari').val(),
            tempo_stimato: $('#tempo_stimato').val(),
            numero_segnalazioni: $('#numero_segnalazioni').val()
        };
        
        const gravitaLabels = {
            'bassa': '🟢 Bassa',
            'media': '🟡 Media', 
            'alta': '🟠 Alta',
            'critica': '🔴 Critica'
        };
        
        const difficoltaLabels = {
            'facile': '⭐ Facile',
            'media': '⭐⭐ Media',
            'difficile': '⭐⭐⭐ Difficile', 
            'esperto': '⭐⭐⭐⭐ Esperto'
        };
        
        let html = `
            <div class="preview-section">
                <div class="preview-title">Titolo Problema</div>
                <h5>${data.titolo || '<em class="text-muted">Non inserito</em>'}</h5>
            </div>
            
            <div class="preview-section">
                <div class="preview-title">Descrizione</div>
                <p>${data.descrizione || '<em class="text-muted">Non inserita</em>'}</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="preview-section">
                        <div class="preview-title">Gravità</div>
                        <span class="badge bg-${getGravitaClass(data.gravita)}">${gravitaLabels[data.gravita] || 'Non selezionata'}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="preview-section">
                        <div class="preview-title">Difficoltà</div>
                        <span class="badge bg-info">${difficoltaLabels[data.difficolta] || 'Non selezionata'}</span>
                    </div>
                </div>
            </div>
            
            <div class="preview-section">
                <div class="preview-title">Soluzione</div>
                <div style="white-space: pre-line;">${data.soluzione || '<em class="text-muted">Non inserita</em>'}</div>
            </div>
        `;
        
        if (data.strumenti_necessari || data.tempo_stimato) {
            html += `
                <div class="row">
                    ${data.strumenti_necessari ? `
                        <div class="col-md-8">
                            <div class="preview-section">
                                <div class="preview-title">Strumenti</div>
                                ${data.strumenti_necessari}
                            </div>
                        </div>
                    ` : ''}
                    ${data.tempo_stimato ? `
                        <div class="col-md-4">
                            <div class="preview-section">
                                <div class="preview-title">Tempo Stimato</div>
                                ${data.tempo_stimato} minuti
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        $('#previewContent').html(html);
    }
    
    function getGravitaClass(gravita) {
        const classes = {
            'bassa': 'success',
            'media': 'info',
            'alta': 'warning',
            'critica': 'danger'
        };
        return classes[gravita] || 'secondary';
    }
    
    // Submit dal modal di anteprima
    $('#submitFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#soluzioneForm').submit();
    });
    
    // === VALIDAZIONE CLIENT-SIDE ===
    $('#soluzioneForm').on('submit', function(e) {
        let isValid = true;
        
        // Controlla campi obbligatori
        const requiredFields = ['titolo', 'descrizione', 'gravita', 'difficolta', 'soluzione'];
        
        requiredFields.forEach(function(field) {
            const element = $(`#${field}`);
            if (!element.val().trim()) {
                element.addClass('is-invalid');
                isValid = false;
            } else {
                element.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            
            // Scroll al primo errore
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
            
            // Mostra alert
            $('body').append(`
                <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Compila tutti i campi obbligatori prima di salvare.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            // Rimuovi alert dopo 5 secondi
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        } else {
            // Disabilita pulsante per evitare doppi submit
            $('#submitBtn').prop('disabled', true).html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Salvataggio...');
        }
    });
    
    // === AUTO-SAVE DRAFT (localStorage) ===
    const formFields = ['titolo', 'descrizione', 'gravita', 'difficolta', 'soluzione', 'strumenti_necessari', 'tempo_stimato'];
    const draftKey = 'soluzione_draft_{{ $prodotto->id }}';
    
    // Carica draft all'avvio
    loadDraft();
    
    // Salva draft ogni 30 secondi
    setInterval(saveDraft, 30000);
    
    // Salva anche quando si cambia campo
    formFields.forEach(field => {
        $(`#${field}`).on('change', saveDraft);
    });
    
    function saveDraft() {
        const draft = {};
        formFields.forEach(field => {
            const value = $(`#${field}`).val();
            if (value) draft[field] = value;
        });
        
        if (Object.keys(draft).length > 0) {
            localStorage.setItem(draftKey, JSON.stringify(draft));
            console.log('Draft salvato');
        }
    }
    
    function loadDraft() {
        const draft = localStorage.getItem(draftKey);
        if (draft) {
            try {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(field => {
                    $(`#${field}`).val(data[field]);
                });
                
                // Mostra notifica di draft caricato
                $('body').append(`
                    <div class="alert alert-info alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999;">
                        <i class="bi bi-info-circle me-2"></i>
                        Draft precedente caricato automaticamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
            } catch (e) {
                console.warn('Errore nel caricamento draft:', e);
            }
        }
    }
    
    // Pulisci draft al submit riuscito
    $('#soluzioneForm').on('submit', function() {
        if ($(this)[0].checkValidity()) {
            localStorage.removeItem(draftKey);
        }
    });
    
    console.log('Form creazione soluzione inizializzato');
});
</script>
@endpush