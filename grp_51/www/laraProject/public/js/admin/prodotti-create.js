

$(document).ready(function() {
    console.log('admin.prodotti.create caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.create') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

    // === CONTATORI CARATTERI ===
    
    /**
     * Aggiorna i contatori dei caratteri per le textarea
     */
    function setupCharacterCounters() {
        // Contatore descrizione
        $('#descrizione').on('input', function() {
            const length = $(this).val().length;
            const counter = $('#descrizione-counter');
            counter.text(length);
            
            if (length > 800) {
                counter.addClass('text-danger').removeClass('text-warning');
            } else if (length > 600) {
                counter.addClass('text-warning').removeClass('text-danger');
            } else {
                counter.removeClass('text-warning text-danger');
            }
        });
        
        // Contatore note tecniche
        $('#note_tecniche').on('input', function() {
            const length = $(this).val().length;
            $('#note-counter').text(length);
        });
        
        // Contatore installazione
        $('#modalita_installazione').on('input', function() {
            const length = $(this).val().length;
            $('#installazione-counter').text(length);
        });
        
        // Contatore uso
        $('#modalita_uso').on('input', function() {
            const length = $(this).val().length;
            $('#uso-counter').text(length);
        });
    }
    
    // === GESTIONE ANTEPRIMA ===
    
    /**
     * Mostra l'anteprima del prodotto in un modal
     */
    $('#previewBtn').on('click', function() {
        updatePreview();
        $('#previewModal').modal('show');
    });
    
    /**
     * Aggiorna il contenuto dell'anteprima
     */
    function updatePreview() {
        const formData = {
            nome: $('#nome').val(),
            modello: $('#modello').val(),
            categoria: $('#categoria option:selected').text(),
            prezzo: $('#prezzo').val(),
            descrizione: $('#descrizione').val(),
            note_tecniche: $('#note_tecniche').val(),
            modalita_installazione: $('#modalita_installazione').val(),
            modalita_uso: $('#modalita_uso').val(),
            attivo: $('#attivo option:selected').text(),
            staff_assegnato: $('#staff_assegnato_id option:selected').text()
        };
        
        let previewHtml = `
            <div class="preview-section">
                <div class="preview-title">📦 Informazioni Base</div>
                <div><strong>Nome:</strong> ${formData.nome || 'Non specificato'}</div>
                <div><strong>Modello:</strong> ${formData.modello || 'Non specificato'}</div>
                <div><strong>Categoria:</strong> ${formData.categoria !== 'Seleziona categoria' ? formData.categoria : 'Non selezionata'}</div>
                <div><strong>Prezzo:</strong> ${formData.prezzo ? '€ ' + formData.prezzo : 'Non specificato'}</div>
                <div><strong>Stato:</strong> ${formData.attivo}</div>
            </div>
        `;
        
        // Descrizione se presente
        if (formData.descrizione) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">📝 Descrizione</div>
                    <div>${formData.descrizione}</div>
                </div>
            `;
        }
        
        // Specifiche tecniche
        if (formData.note_tecniche || formData.modalita_installazione || formData.modalita_uso) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">🔧 Specifiche Tecniche</div>
            `;
            
            if (formData.note_tecniche) {
                previewHtml += `<div><strong>Note Tecniche:</strong><br>${formData.note_tecniche}</div><br>`;
            }
            
            if (formData.modalita_installazione) {
                previewHtml += `<div><strong>Installazione:</strong><br>${formData.modalita_installazione}</div><br>`;
            }
            
            if (formData.modalita_uso) {
                previewHtml += `<div><strong>Modalità d'Uso:</strong><br>${formData.modalita_uso}</div>`;
            }
            
            previewHtml += `</div>`;
        }
        
        // Assegnazione staff se presente
        if (formData.staff_assegnato !== 'Nessuna assegnazione') {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">👥 Gestione</div>
                    <div><strong>Staff Assegnato:</strong> ${formData.staff_assegnato}</div>
                </div>
            `;
        }
        
        $('#previewContent').html(previewHtml);
        
        // Mostra anche il riepilogo inline
        $('#riepilogo-content').html(previewHtml);
        $('#riepilogo-prodotto').slideDown();
    }
    
    /**
     * Conferma creazione dal modal di anteprima
     */
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#createProductForm').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    /**
     * Validazione in tempo reale del form
     */
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('blur change', function() {
        validateField($(this));
    });
    
    /**
     * Valida un singolo campo
     */
    function validateField($field) {
        const value = $field.val().trim();
        const fieldName = $field.attr('name');
        let isValid = true;
        let errorMessage = '';
        
        // Validazioni specifiche per campo
        switch(fieldName) {
            case 'nome':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'Nome deve essere almeno 3 caratteri';
                }
                break;
                
            case 'modello':
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Modello deve essere almeno 2 caratteri';
                }
                break;
                
            case 'prezzo':
                if (value && (isNaN(value) || parseFloat(value) < 0)) {
                    isValid = false;
                    errorMessage = 'Prezzo deve essere un numero positivo';
                }
                break;
                
            case 'descrizione':
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Descrizione deve essere almeno 10 caratteri';
                }
                break;
        }
        
        // Applica la validazione visiva
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.siblings('.invalid-feedback.custom-validation').remove();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            
            // Aggiunge messaggio di errore se non esiste
            if (!$field.siblings('.invalid-feedback.custom-validation').length) {
                $field.after(`<div class="invalid-feedback custom-validation">${errorMessage}</div>`);
            } else {
                $field.siblings('.invalid-feedback.custom-validation').text(errorMessage);
            }
        }
        
        return isValid;
    }
    
    /**
     * Validazione completa del form prima dell'invio
     */
    $('#createProductForm').on('submit', function(e) {
        let isFormValid = true;
        
        // Valida tutti i campi obbligatori
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        // Previene l'invio se ci sono errori
        if (!isFormValid) {
            e.preventDefault();
            
            // Mostra alert di errore
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Errori nel form:</strong> Correggi i campi evidenziati prima di continuare.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#createProductForm').prepend(alertHtml);
            
            // Scroll al primo errore
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });
    
    // === MIGLIORAMENTI UX ===
    
    /**
     * Genera automaticamente il modello basato sul nome
     */
    $('#nome').on('input', function() {
        const nome = $(this).val();
        
        // Solo se il campo modello è vuoto
        if ($('#modello').val().trim() === '' && nome.length > 3) {
            // Estrae le prime lettere delle parole principali
            const words = nome.split(' ');
            let modello = '';
            
            words.forEach(word => {
                if (word.length > 2) {
                    modello += word.substring(0, 2).toUpperCase();
                }
            });
            
            // Aggiunge numeri casuali
            modello += '-' + Math.floor(Math.random() * 9000 + 1000);
            
            $('#modello').val(modello);
        }
    });
    
    /**
     * Effetti visivi per i campi in focus
     */
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.mb-3, .mb-4').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.mb-3, .mb-4').removeClass('focused');
    });
    
    /**
     * Conferma prima di abbandonare la pagina se ci sono modifiche
     */
    let formChanged = false;
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('input change', function() {
        formChanged = true;
    });
    
    $(window).on('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Vuoi davvero uscire?';
            return e.returnValue;
        }
    });
    
    // Rimuove l'avviso quando il form viene inviato
    $('#createProductForm').on('submit', function() {
        formChanged = false;
    });
    
    // Inizializza componenti
    initializeComponents();
    
    /**
     * Inizializza componenti e impostazioni iniziali
     */
    function initializeComponents() {
        // Setup contatori caratteri
        setupCharacterCounters();
        
        // Nasconde il riepilogo inizialmente
        $('#riepilogo-prodotto').hide();
        
        // Focus sul primo campo
        $('#nome').focus();
        
        console.log('✅ Sistema creazione prodotto inizializzato correttamente');
    }
});

// === FUNZIONI GLOBALI ===

/**
 * Anteprima immagine quando viene selezionata
 */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Controlla dimensione file (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('Il file è troppo grande. Dimensione massima: 5MB');
            input.value = '';
            return;
        }
        
        // Controlla tipo file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Formato file non supportato. Usa: JPG, PNG, GIF, WebP');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-img').attr('src', e.target.result);
            $('#image-preview').show();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Rimuove l'immagine selezionata
 */
function removeImage() {
    $('#foto').val('');
    $('#image-preview').hide();
    $('#preview-img').attr('src', '');
}

/**
 * Riempie il form con dati di esempio
 */
function fillSampleData() {
    if (confirm('Vuoi riempire il form con dati di esempio? I dati attuali verranno sostituiti.')) {
        // Ottieni la prima categoria disponibile dal dropdown
        const categoriaSelect = document.getElementById('categoria');
        const firstCategory = categoriaSelect.options[1]; // Prima opzione dopo "Seleziona categoria"
        
        // Dati di esempio adattati alla categoria selezionata
        let sampleData = getSampleDataForCategory(firstCategory ? firstCategory.value : 'altro');
        
        // Riempie i campi con i dati di esempio
        document.getElementById('nome').value = sampleData.nome;
        document.getElementById('modello').value = sampleData.modello;
        document.getElementById('prezzo').value = sampleData.prezzo;
        document.getElementById('descrizione').value = sampleData.descrizione;
        document.getElementById('note_tecniche').value = sampleData.note_tecniche;
        document.getElementById('modalita_installazione').value = sampleData.modalita_installazione;
        document.getElementById('modalita_uso').value = sampleData.modalita_uso;
        document.getElementById('attivo').value = '1';
        
        // Seleziona la categoria
        if (firstCategory) {
            categoriaSelect.value = firstCategory.value;
        }
        
        // Triggera gli eventi per aggiornare contatori e validazione
        ['#descrizione', '#note_tecniche', '#modalita_installazione', '#modalita_uso'].forEach(selector => {
            $(selector).trigger('input');
        });
        
        alert(`Dati di esempio inseriti per categoria: ${firstCategory ? firstCategory.text : 'Generico'}!`);
    }
}

function getSampleDataForCategory(categoria) {
    const sampleDataMap = {
        'lavatrice': {
            nome: 'Lavatrice EcoWash Pro',
            modello: 'EW-7000X',
            prezzo: '699.99',
            descrizione: 'Lavatrice ad alta efficienza energetica con capacità di 7kg. Dotata di tecnologia inverter per un funzionamento silenzioso e programmi di lavaggio intelligenti. Ideale per famiglie di 3-4 persone.',
            note_tecniche: 'Capacità: 7kg\nVelocità centrifuga: 1400 giri/min\nClasse energetica: A+++\nDimensioni: 60x60x85 cm\nPotenza: 2100W\nCollegamento: 230V',
            modalita_installazione: '1. Rimuovere imballaggio e blocchi di trasporto\n2. Posizionare su superficie piana e livellare\n3. Collegare tubo di scarico e carico acqua\n4. Collegare alimentazione elettrica\n5. Eseguire primo lavaggio a vuoto',
            modalita_uso: 'Selezionare il programma adatto al tipo di tessuto. Dosare il detersivo secondo le indicazioni. Per capi delicati utilizzare il programma apposito. Pulire regolarmente il filtro e il cassetto detersivo.'
        },
        
        'lavastoviglie': {
            nome: 'Lavastoviglie SilentClean',
            modello: 'SC-6000',
            prezzo: '549.99',
            descrizione: 'Lavastoviglie da incasso ultra-silenziosa con 3° cestello per posate. 14 coperti, classe A+++, funzionamento silenzioso sotto i 42dB.',
            note_tecniche: 'Capacità: 14 coperti\nRumorosità: 42dB\nClasse energetica: A+++\nDimensioni: 60x60x82 cm\n8 programmi di lavaggio\n3° cestello per posate',
            modalita_installazione: '1. Preparare vano di incasso 60x60x82 cm\n2. Collegare scarico e carico acqua\n3. Collegamento elettrico 230V\n4. Fissare ai mobili laterali\n5. Test di funzionamento',
            modalita_uso: 'Caricare stoviglie senza sovrapporle. Utilizzare sale rigenerante e brillantante. Selezionare programma adeguato al carico. Pulire filtri settimanalmente.'
        },
        
        'frigorifero': {
            nome: 'Frigorifero CoolFresh XL',
            modello: 'CF-400L',
            prezzo: '1299.99',
            descrizione: 'Frigorifero combinato No Frost da 400L con dispenser acqua e ghiaccio. Controllo digitale della temperatura e sistema antibatterico.',
            note_tecniche: 'Capacità: 400L (280L frigo + 120L freezer)\nClasse energetica: A++\nSistema No Frost\nDispenser acqua/ghiaccio\nDimensioni: 70x60x185 cm',
            modalita_installazione: '1. Posizionare su superficie piana\n2. Lasciare 5cm di spazio sui lati\n3. Collegamento idrico per dispenser\n4. Collegamento elettrico\n5. Attesa 4 ore prima dell\'accensione',
            modalita_uso: 'Regolare temperature: frigo +4°C, freezer -18°C. Sostituire filtro acqua ogni 6 mesi. Pulire bobine posteriori ogni 6 mesi. Non sovraccaricare i ripiani.'
        },
        
        'forno': {
            nome: 'Forno Multifunzione Chef Pro',
            modello: 'CP-65L',
            prezzo: '799.99',
            descrizione: 'Forno elettrico multifunzione da 65L con pirolisi e 10 funzioni di cottura. Display touch e sonde temperatura.',
            note_tecniche: 'Capacità: 65L\n10 funzioni cottura\nPirolisi autopulente\nTemperatura: 50-275°C\nClasse energetica: A\nDimensioni: 60x60x60 cm',
            modalita_installazione: '1. Preparare vano incasso 60x60x60 cm\n2. Collegamento elettrico 380V\n3. Ventilazione retrostante\n4. Fissaggio con staffe\n5. Test funzionamento e calibrazione',
            modalita_uso: 'Preriscaldare sempre il forno. Utilizzare funzione pirolisi per pulizia mensile. Posizionare cibi nel ripiano centrale per cottura uniforme. Utilizzare sonde per arrosti.'
        },
        
        'piano_cottura': {
            nome: 'Piano Cottura Induzione FlexCook',
            modello: 'FC-4Z-IND',
            prezzo: '899.99',
            descrizione: 'Piano cottura a induzione da 60cm con 4 zone e area flessibile. Controlli touch e timer individuale per ogni zona.',
            note_tecniche: '4 zone induzione\nZona flessibile centrale\nPotenza totale: 7200W\nControlli touch\nTimer individuale\nDimensioni: 60x52 cm',
            modalita_installazione: '1. Taglio piano cucina 56x49 cm\n2. Collegamento elettrico 380V\n3. Ventilazione sottostante\n4. Sigillatura perimetrale\n5. Test funzionamento zone',
            modalita_uso: 'Utilizzare solo pentole compatibili induzione. Pulizia con prodotti specifici per vetroceramica. Non utilizzare come piano di appoggio. Controllo touch con mani asciutte.'
        }
    };
    
    // Restituisce dati specifici per categoria o generici se non trovata
    return sampleDataMap[categoria] || {
        nome: `Prodotto ${categoria.charAt(0).toUpperCase() + categoria.slice(1)}`,
        modello: 'MOD-2024',
        prezzo: '399.99',
        descrizione: `Prodotto di qualità per la categoria ${categoria}. Caratteristiche tecniche avanzate e design moderno per soddisfare ogni esigenza domestica.`,
        note_tecniche: `Specifiche tecniche complete per ${categoria}.\nDimensioni standard.\nClasse energetica ottimale.\nGaranzia 2 anni.`,
        modalita_installazione: `Installazione standard per prodotti ${categoria}.\nSeguire le istruzioni del manuale.\nRichiedere assistenza tecnica se necessario.`,
        modalita_uso: `Utilizzo intuitive e sicuro.\nSegui le indicazioni per ${categoria}.\nManutenzione regolare consigliata.`
    };
}

$(document).ready(function() {
    // Gestione cambio categoria per aggiornare suggerimenti
    $('#categoria').on('change', function() {
        const selectedCategory = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        
        if (selectedCategory) {
            // Aggiorna placeholder con suggerimenti specifici per categoria
            updatePlaceholderForCategory(selectedCategory);
            
            // Log per debugging
            console.log(`Categoria selezionata: ${selectedText} (${selectedCategory})`);
        }
    });
});

/**
 * Aggiorna i placeholder dei campi in base alla categoria selezionata
 */
function updatePlaceholderForCategory(categoria) {
    const placeholders = {
        'lavatrice': {
            nome: 'es: Lavatrice EcoWash Pro',
            modello: 'es: EW-7000X',
            descrizione: 'Caratteristiche tecniche, capacità di carico, efficienza energetica, programmi speciali...',
            note_tecniche: 'Capacità di carico, giri centrifuga, classe energetica, dimensioni, potenza...'
        },
        'lavastoviglie': {
            nome: 'es: Lavastoviglie SilentClean',
            modello: 'es: SC-6000',
            descrizione: 'Numero coperti, silenziosità, efficienza energetica, programmi di lavaggio...',
            note_tecniche: 'Coperti, rumorosità in dB, classe energetica, dimensioni, programmi...'
        },
        'frigorifero': {
            nome: 'es: Frigorifero CoolFresh XL',
            modello: 'es: CF-400L',
            descrizione: 'Capacità, sistema No Frost, dispenser, controllo temperatura digitale...',
            note_tecniche: 'Capacità totale e per vano, classe energetica, sistema di raffreddamento...'
        }
        // Aggiungi altre categorie se necessario
    };
    
    if (placeholders[categoria]) {
        Object.keys(placeholders[categoria]).forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.placeholder = placeholders[categoria][field];
            }
        });
    }
}

/**
 * Svuota tutti i campi del form
 */
function clearForm() {
    if (confirm('Vuoi svuotare tutti i campi? I dati inseriti verranno persi.')) {
        $('#createProductForm')[0].reset();
        $('#image-preview').hide();
        $('#riepilogo-prodotto').hide();
        $('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $('.custom-validation').remove();
        
        // Reset contatori
        $('#descrizione-counter, #note-counter, #installazione-counter, #uso-counter').text('0');
        
        $('#nome').focus();
        alert('Form svuotato!');
    }
}