
$(document).ready(function() {
    console.log('malfunzionamenti.edit caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'staff.malfunzionamenti.edit') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

    // === CONTROLLO CHECKBOX ELIMINAZIONE ===
    $('#confirmDelete').on('change', function() {
        $('#confirmDeleteBtn').prop('disabled', !this.checked);
    });
    
    // === ANTEPRIMA MODIFICHE ===
    $('#previewBtn').on('click', function() {
        generatePreview();
        $('#previewModal').modal('show');
    });
    
    function generatePreview() {
        // Dati originali per confronto
        const original = {
            titolo: window.malfunzionamento.titolo,
            descrizione: window.malfunzionamento.descrizione,
            gravita: window.malfunzionamento.gravita,
            difficolta: window.malfunzionamento.difficolta,
            soluzione: window.malfunzionamento.soluzione,
            strumenti_necessari: window.malfunzionamento.strumenti_necessari,
            tempo_stimato: window.malfunzionamento.tempo_stimato,
            numero_segnalazioni: window.malfunzionamento.numero_segnalazioni
        };
        
        // Dati correnti del form
        const current = {
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
        
        // Funzione per evidenziare le modifiche
        function highlightChange(originalValue, currentValue, fieldName) {
            if (originalValue != currentValue) {
                return `<span class="highlight-change" title="Modificato da: ${originalValue}">${currentValue}</span>`;
            }
            return currentValue || '<em class="text-muted">Non inserito</em>';
        }
        
        let html = `
            <div class="preview-section">
                <div class="preview-title">Titolo Problema</div>
                <h5>${highlightChange(original.titolo, current.titolo, 'titolo')}</h5>
            </div>
            
            <div class="preview-section">
                <div class="preview-title">Descrizione</div>
                <p>${highlightChange(original.descrizione, current.descrizione, 'descrizione')}</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="preview-section">
                        <div class="preview-title">Gravità</div>
                        <span class="badge bg-${getGravitaClass(current.gravita)}">${highlightChange(gravitaLabels[original.gravita], gravitaLabels[current.gravita], 'gravita')}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="preview-section">
                        <div class="preview-title">Difficoltà</div>
                        <span class="badge bg-info">${highlightChange(difficoltaLabels[original.difficolta], difficoltaLabels[current.difficolta], 'difficolta')}</span>
                    </div>
                </div>
            </div>
            
            <div class="preview-section">
                <div class="preview-title">Soluzione</div>
                <div style="white-space: pre-line;">${highlightChange(original.soluzione, current.soluzione, 'soluzione')}</div>
            </div>
        `;
        
        if (current.strumenti_necessari || current.tempo_stimato) {
            html += `
                <div class="row">
                    ${current.strumenti_necessari ? `
                        <div class="col-md-8">
                            <div class="preview-section">
                                <div class="preview-title">Strumenti</div>
                                ${highlightChange(original.strumenti_necessari, current.strumenti_necessari, 'strumenti')}
                            </div>
                        </div>
                    ` : ''}
                    ${current.tempo_stimato ? `
                        <div class="col-md-4">
                            <div class="preview-section">
                                <div class="preview-title">Tempo Stimato</div>
                                ${highlightChange(original.tempo_stimato, current.tempo_stimato, 'tempo')} minuti
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        // Mostra contatore modifiche
        let changesCount = 0;
        Object.keys(original).forEach(key => {
            if (original[key] != current[key]) {
                changesCount++;
            }
        });
        
        if (changesCount > 0) {
            html = `<div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>${changesCount} modifica${changesCount > 1 ? 'he' : ''} rilevata${changesCount > 1 ? 'e' : ''}:</strong> 
                I campi evidenziati sono stati modificati rispetto alla versione originale.
            </div>` + html;
        } else {
            html = `<div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Nessuna modifica rilevata. La soluzione rimane invariata.
            </div>` + html;
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
    $('#updateFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#editSoluzioneForm').submit();
    });
    
    // === VALIDAZIONE CLIENT-SIDE ===
    $('#editSoluzioneForm').on('submit', function(e) {
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
            showAlert('danger', 'Compila tutti i campi obbligatori prima di salvare.');
        } else {
            // Disabilita pulsante per evitare doppi submit
            $('#updateBtn').prop('disabled', true).html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Salvando...');
        }
    });
    
    // === AUTO-SAVE DRAFT (localStorage) ===
    const formFields = ['titolo', 'descrizione', 'gravita', 'difficolta', 'soluzione', 'strumenti_necessari', 'tempo_stimato'];
    const draftKey = 'soluzione_edit_draft_{{ $malfunzionamento->id }}';
    
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
            console.log('Draft modifiche salvato');
        }
    }
    
    function loadDraft() {
        const draft = localStorage.getItem(draftKey);
        if (draft) {
            try {
                const data = JSON.parse(draft);
                let hasChanges = false;
                
                Object.keys(data).forEach(field => {
                    const currentValue = $(`#${field}`).val();
                    if (data[field] !== currentValue) {
                        $(`#${field}`).val(data[field]);
                        hasChanges = true;
                    }
                });
                
                if (hasChanges) {
                    showAlert('info', 'Draft precedente caricato automaticamente. Le modifiche non salvate sono state ripristinate.');
                }
            } catch (e) {
                console.warn('Errore nel caricamento draft:', e);
            }
        }
    }
    
    // Pulisci draft al submit riuscito
    $('#editSoluzioneForm').on('submit', function() {
        if ($(this)[0].checkValidity()) {
            localStorage.removeItem(draftKey);
        }
    });
    
    // === CONTROLLI AGGIUNTIVI ===
    
    // Controlla coerenza date
    $('#prima_segnalazione, #ultima_segnalazione').on('change', function() {
        const prima = $('#prima_segnalazione').val();
        const ultima = $('#ultima_segnalazione').val();
        
        if (prima && ultima && prima > ultima) {
            showAlert('warning', 'La data della prima segnalazione non può essere successiva all\'ultima segnalazione.');
            $(this).focus();
        }
    });
    
    // Suggerimenti automatici in base alla gravità
    $('#gravita').on('change', function() {
        const gravita = $(this).val();
        if (gravita === 'critica' && !$('#numero_segnalazioni').val()) {
            $('#numero_segnalazioni').val('1');
            showAlert('info', 'Per problemi critici è consigliabile specificare il numero di segnalazioni.');
        }
    });
    
    // === FUNZIONI HELPER ===
    
    function showAlert(type, message) {
        const alertClass = type === 'danger' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        const icon = type === 'danger' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // === KEYBOARD SHORTCUTS ===
    $(document).on('keydown', function(e) {
        // Ctrl+S per salvare
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('#editSoluzioneForm').submit();
        }
        
        // Ctrl+P per anteprima
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            $('#previewBtn').click();
        }
        
        // Esc per annullare/chiudere modal
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
    });
    
    console.log('Form modifica soluzione inizializzato');
});