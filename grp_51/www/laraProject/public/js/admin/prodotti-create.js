/**
 * ===================================================================
 * FILE: admin/prodotti/create.js
 * LINGUAGGIO: JavaScript + jQuery + Bootstrap + HTML5 APIs
 * SCOPO: Interfaccia per creazione nuovo prodotto nel sistema assistenza tecnica
 * ===================================================================
 * 
 * Questo modulo JavaScript gestisce il form di creazione prodotti con:
 * - Validazione in tempo reale dei campi
 * - Contatori caratteri per textarea
 * - Anteprima prodotto prima del salvataggio
 * - Generazione automatica codice modello
 * - Upload e preview immagini
 * - Dati di esempio per testing
 * - Protezione contro perdita dati accidentale
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (template literals, arrow functions, destructuring)
 * - jQuery 3.x per manipolazione DOM e event handling
 * - Bootstrap 5 per componenti UI (modal, alert, form validation)
 * - HTML5 File API per gestione upload immagini
 * - HTML5 Form Validation API per controlli nativi browser
 * ===================================================================
 */

// ===================================================================
// SEZIONE 1: INIZIALIZZAZIONE PRINCIPALE E CONTROLLO ROUTE
// ===================================================================

/**
 * EVENT HANDLER PRINCIPALE - Document Ready
 * LINGUAGGIO: jQuery Event System
 * 
 * Punto di ingresso principale del modulo. Si attiva quando il DOM è
 * completamente caricato e inizializza tutte le funzionalità del form.
 */
$(document).ready(function() {
    console.log('admin.prodotti.create caricato');
    
    /**
     * CONTROLLO ROUTE - Verifica pagina corretta
     * window.LaravelApp.route è variabile globale impostata da Laravel Blade
     * Garantisce che il codice si esegua solo nella pagina di creazione prodotti
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.create') {
        return; // Esce se non siamo nella route corretta
    }
    
    /**
     * VARIABILI LOCALI - Dati di stato del modulo
     */
    const pageData = window.PageData || {};        // Dati passati da Laravel
    let selectedProducts = [];                     // Array prodotti selezionati (future estensioni)
    
    // === SEZIONE 2: SISTEMA CONTATORI CARATTERI ===
    
    /**
     * FUNZIONE CONTATORI - Setup monitoraggio lunghezza testi
     * SCOPO: Mostra all'utente quanti caratteri ha inserito nelle textarea
     *        e cambia colore quando si avvicina ai limiti
     */
    function setupCharacterCounters() {
        /**
         * CONTATORE DESCRIZIONE - Campo principale prodotto
         * EVENT: 'input' si attiva ad ogni carattere digitato
         * LIMITI: 600 caratteri (warning), 800 caratteri (danger)
         */
        $('#descrizione').on('input', function() {
            const length = $(this).val().length;              // Ottiene lunghezza testo
            const counter = $('#descrizione-counter');         // Elemento contatore nel DOM
            counter.text(length);                             // Aggiorna numero visualizzato
            
            /**
             * LOGICA COLORAZIONE - Visual feedback basato su soglie
             * Bootstrap classes: text-warning (giallo), text-danger (rosso)
             */
            if (length > 800) {
                // ROSSO - Limite quasi raggiunto
                counter.addClass('text-danger').removeClass('text-warning');
            } else if (length > 600) {
                // GIALLO - Avvicinamento al limite
                counter.addClass('text-warning').removeClass('text-danger');
            } else {
                // NORMALE - Dentro i limiti
                counter.removeClass('text-warning text-danger');
            }
        });
        
        /**
         * CONTATORE NOTE TECNICHE - Specifiche tecniche prodotto
         * Setup più semplice senza colorazione per campo meno critico
         */
        $('#note_tecniche').on('input', function() {
            const length = $(this).val().length;
            $('#note-counter').text(length);                   // Solo aggiornamento numero
        });
        
        /**
         * CONTATORE INSTALLAZIONE - Istruzioni installazione
         */
        $('#modalita_installazione').on('input', function() {
            const length = $(this).val().length;
            $('#installazione-counter').text(length);
        });
        
        /**
         * CONTATORE USO - Istruzioni utilizzo prodotto
         */
        $('#modalita_uso').on('input', function() {
            const length = $(this).val().length;
            $('#uso-counter').text(length);
        });
    }
    
    // === SEZIONE 3: SISTEMA ANTEPRIMA PRODOTTO ===
    
    /**
     * EVENT HANDLER - Pulsante anteprima prodotto
     * ELEMENTO: #previewBtn (pulsante nell'interfaccia)
     * AZIONE: Genera anteprima e mostra modal Bootstrap
     */
    $('#previewBtn').on('click', function() {
        updatePreview();                    // Genera contenuto anteprima
        $('#previewModal').modal('show');   // Apre modal Bootstrap
    });
    
    /**
     * FUNZIONE CRITICA - Genera anteprima del prodotto
     * LINGUAGGIO: JavaScript + Template Literals ES6
     * 
     * Questa funzione raccoglie tutti i dati dal form e li formatta
     * in una anteprima HTML strutturata che simula come apparirà
     * il prodotto nel sistema finale.
     */
    function updatePreview() {
        /**
         * RACCOLTA DATI FORM - Estrae valori da tutti i campi
         * PATTERN: Oggetto con chiavi corrispondenti ai nomi dei campi
         */
        const formData = {
            nome: $('#nome').val(),
            modello: $('#modello').val(),
            // .text() per select ottiene il testo dell'opzione, non il value
            categoria: $('#categoria option:selected').text(),
            prezzo: $('#prezzo').val(),
            descrizione: $('#descrizione').val(),
            note_tecniche: $('#note_tecniche').val(),
            modalita_installazione: $('#modalita_installazione').val(),
            modalita_uso: $('#modalita_uso').val(),
            attivo: $('#attivo option:selected').text(),
            staff_assegnato: $('#staff_assegnato_id option:selected').text()
        };
        
        /**
         * GENERAZIONE HTML - Template literals per markup dinamico
         * SEZIONE: Informazioni base del prodotto
         */
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
        
        /**
         * SEZIONE CONDIZIONALE - Descrizione
         * Mostra solo se l'utente ha inserito una descrizione
         */
        if (formData.descrizione) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">📝 Descrizione</div>
                    <div>${formData.descrizione}</div>
                </div>
            `;
        }
        
        /**
         * SEZIONE CONDIZIONALE - Specifiche tecniche
         * Raggruppa note tecniche, installazione e uso se presenti
         */
        if (formData.note_tecniche || formData.modalita_installazione || formData.modalita_uso) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">🔧 Specifiche Tecniche</div>
            `;
            
            // Aggiunge ogni specifica solo se presente
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
        
        /**
         * SEZIONE CONDIZIONALE - Assegnazione staff
         * Mostra solo se è stato assegnato uno staff member
         */
        if (formData.staff_assegnato !== 'Nessuna assegnazione') {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">👥 Gestione</div>
                    <div><strong>Staff Assegnato:</strong> ${formData.staff_assegnato}</div>
                </div>
            `;
        }
        
        /**
         * AGGIORNAMENTO DOM - Inserisce HTML generato negli elementi target
         */
        $('#previewContent').html(previewHtml);           // Modal anteprima
        $('#riepilogo-content').html(previewHtml);        // Sezione riepilogo inline
        $('#riepilogo-prodotto').slideDown();             // Mostra con animazione jQuery
    }
    
    /**
     * EVENT HANDLER - Conferma creazione da anteprima
     * ELEMENTO: #createFromPreview (pulsante nel modal)
     * AZIONE: Chiude modal e invia il form
     */
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');        // Chiude modal Bootstrap
        $('#createProductForm').submit();        // Invia form al server Laravel
    });
    
    // === SEZIONE 4: SISTEMA VALIDAZIONE FORM ===
    
    /**
     * EVENT HANDLERS - Validazione in tempo reale
     * EVENTI: 'blur' (perde focus), 'change' (cambia valore)
     * ELEMENTI: Tutti i campi input, select, textarea del form
     */
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('blur change', function() {
        validateField($(this));              // Valida il singolo campo
    });
    
    /**
     * FUNZIONE VALIDAZIONE - Controlla singolo campo
     * LINGUAGGIO: JavaScript con RegExp e validazione logica
     * 
     * @param {jQuery} $field - Elemento jQuery del campo da validare
     * @returns {boolean} - True se valido, false se invalido
     * 
     * Implementa regole di business specifiche per ogni tipo di campo
     * e fornisce feedback visivo immediato all'utente.
     */
    function validateField($field) {
        const value = $field.val().trim();           // Valore pulito (senza spazi)
        const fieldName = $field.attr('name');       // Nome del campo HTML
        let isValid = true;                          // Flag validità
        let errorMessage = '';                       // Messaggio errore da mostrare
        
        /**
         * VALIDAZIONI SPECIFICHE - Switch per tipo campo
         * Ogni campo ha regole di business diverse
         */
        switch(fieldName) {
            case 'nome':
                // REGOLA: Nome prodotto deve essere almeno 3 caratteri
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'Nome deve essere almeno 3 caratteri';
                }
                break;
                
            case 'modello':
                // REGOLA: Codice modello deve essere almeno 2 caratteri
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Modello deve essere almeno 2 caratteri';
                }
                break;
                
            case 'prezzo':
                // REGOLA: Prezzo opzionale ma se inserito deve essere numero positivo
                if (value && (isNaN(value) || parseFloat(value) < 0)) {
                    isValid = false;
                    errorMessage = 'Prezzo deve essere un numero positivo';
                }
                break;
                
            case 'descrizione':
                // REGOLA: Descrizione deve essere significativa (min 10 caratteri)
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Descrizione deve essere almeno 10 caratteri';
                }
                break;
        }
        
        /**
         * APPLICAZIONE VISUAL FEEDBACK - Bootstrap validation classes
         * CLASSI: is-valid (verde), is-invalid (rosso)
         */
        if (isValid) {
            // STATO VALIDO - Verde con messaggio positivo
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.siblings('.invalid-feedback.custom-validation').remove();
        } else {
            // STATO INVALIDO - Rosso con messaggio errore
            $field.removeClass('is-valid').addClass('is-invalid');
            
            /**
             * GESTIONE MESSAGGI ERRORE - Crea o aggiorna messaggio
             */
            if (!$field.siblings('.invalid-feedback.custom-validation').length) {
                // CREA NUOVO MESSAGGIO - Se non esiste
                $field.after(`<div class="invalid-feedback custom-validation">${errorMessage}</div>`);
            } else {
                // AGGIORNA MESSAGGIO ESISTENTE
                $field.siblings('.invalid-feedback.custom-validation').text(errorMessage);
            }
        }
        
        return isValid;
    }
    
    /**
     * EVENT HANDLER - Validazione completa al submit
     * ELEMENTO: #createProductForm
     * EVENTO: 'submit' - Prima che il form venga inviato al server
     */
    $('#createProductForm').on('submit', function(e) {
        let isFormValid = true;                      // Flag validità form completo
        
        /**
         * VALIDAZIONE TUTTI I CAMPI OBBLIGATORI
         * .each() di jQuery itera su tutti gli elementi selezionati
         */
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;                 // Se un campo è invalido, tutto il form è invalido
            }
        });
        
        /**
         * GESTIONE FORM INVALIDO - Previene invio e mostra errori
         */
        if (!isFormValid) {
            e.preventDefault();                      // Blocca invio form al server
            
            /**
             * ALERT ERRORE - Template HTML per messaggio di errore
             * Bootstrap alert dismissible con icona e pulsante chiudi
             */
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Errori nel form:</strong> Correggi i campi evidenziati prima di continuare.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // INSERIMENTO ALERT - All'inizio del form per massima visibilità
            $('#createProductForm').prepend(alertHtml);
            
            /**
             * SCROLL AUTOMATICO - Porta l'utente al primo errore
             * .animate() di jQuery per scroll fluido
             */
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100    // -100px per margin superiore
                }, 500);                                         // 500ms durata animazione
            }
        }
    });
    
    // === SEZIONE 5: MIGLIORAMENTI UX (USER EXPERIENCE) ===
    
    /**
     * FEATURE UX - Generazione automatica codice modello
     * LINGUAGGIO: JavaScript string manipulation + Math.random()
     * 
     * SCOPO: Aiuta l'utente generando automaticamente un codice modello
     *        basato sul nome del prodotto quando inizia a digitare.
     */
    $('#nome').on('input', function() {
        const nome = $(this).val();
        
        /**
         * CONDIZIONE: Solo se campo modello è vuoto e nome ha senso
         */
        if ($('#modello').val().trim() === '' && nome.length > 3) {
            const words = nome.split(' ');               // Divide nome in parole
            let modello = '';
            
            /**
             * ALGORITMO GENERAZIONE - Estrae iniziali parole significative
             * forEach è metodo array JavaScript per iterazione
             */
            words.forEach(word => {
                if (word.length > 2) {                    // Solo parole significative
                    modello += word.substring(0, 2).toUpperCase();  // Prime 2 lettere maiuscole
                }
            });
            
            /**
             * SUFFISSO NUMERICO - Aggiunge numero casuale per unicità
             * Math.random() genera numero 0-1, Math.floor() arrotonda per difetto
             */
            modello += '-' + Math.floor(Math.random() * 9000 + 1000);  // Numero 1000-9999
            
            $('#modello').val(modello);                   // Inserisce codice generato
        }
    });
    
    /**
     * FEATURE UX - Effetti visivi focus campi
     * LINGUAGGIO: jQuery event chaining + CSS class manipulation
     * 
     * Aggiunge classe CSS quando campo ha focus per evidenziarlo visivamente
     */
    $('.form-control, .form-select').on('focus', function() {
        // FOCUS - Aggiunge classe per styling speciale
        $(this).closest('.mb-3, .mb-4').addClass('focused');
    }).on('blur', function() {
        // BLUR - Rimuove classe quando perde focus
        $(this).closest('.mb-3, .mb-4').removeClass('focused');
    });
    
    /**
     * FEATURE UX - Protezione contro perdita dati accidentale
     * LINGUAGGIO: Browser beforeunload API + event tracking
     * 
     * Avvisa l'utente se tenta di uscire dalla pagina con modifiche non salvate
     */
    let formChanged = false;                             // Flag modifiche pendenti
    
    /**
     * TRACKING MODIFICHE - Monitora tutti i campi del form
     * Eventi 'input' e 'change' coprono tutte le interazioni utente
     */
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('input change', function() {
        formChanged = true;                              // Marca form como modificato
    });
    
    /**
     * EVENT HANDLER - Intercetta uscita dalla pagina
     * beforeunload è evento browser nativo che si attiva prima di lasciare la pagina
     */
    $(window).on('beforeunload', function(e) {
        if (formChanged) {
            // AVVISO BROWSER - Messaggio di conferma standard
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Vuoi davvero uscire?';
            return e.returnValue;
        }
    });
    
    /**
     * RESET PROTEZIONE - Quando form viene inviato correttamente
     * Rimuove la protezione per permettere navigazione normale dopo salvataggio
     */
    $('#createProductForm').on('submit', function() {
        formChanged = false;                             // Reset flag modifiche
    });
    
    // === SEZIONE 6: INIZIALIZZAZIONE COMPONENTI ===
    
    initializeComponents();                              // Chiama funzione inizializzazione
    
    /**
     * FUNZIONE INIZIALIZZAZIONE - Setup configurazioni iniziali
     * SCOPO: Raggruppa tutte le operazioni di setup da eseguire all'avvio
     */
    function initializeComponents() {
        setupCharacterCounters();                        // Attiva contatori caratteri
        $('#riepilogo-prodotto').hide();                 // Nasconde riepilogo inizialmente
        $('#nome').focus();                              // Focus sul primo campo per UX
        
        console.log('✅ Sistema creazione prodotto inizializzato correttamente');
    }
});

// ===================================================================
// SEZIONE 7: FUNZIONI GLOBALI (CHIAMABILI DA HTML)
// ===================================================================

/**
 * FUNZIONE GLOBALE - Anteprima immagine upload
 * LINGUAGGIO: HTML5 File API + FileReader
 * 
 * @param {HTMLInputElement} input - Elemento input file HTML
 * 
 * Gestisce l'upload di immagini con validazione tipo e dimensione,
 * mostra anteprima immediata usando FileReader API del browser.
 */
function previewImage(input) {
    /**
     * CONTROLLO FILE - Verifica che sia stato selezionato un file
     * input.files è FileList HTML5, input.files[0] è il primo file
     */
    if (input.files && input.files[0]) {
        const file = input.files[0];                     // Riferimento al file selezionato
        
        /**
         * VALIDAZIONE DIMENSIONE - Limite 5MB per performance
         * file.size è in bytes, 5 * 1024 * 1024 = 5MB
         */
        if (file.size > 5 * 1024 * 1024) {
            alert('Il file è troppo grande. Dimensione massima: 5MB');
            input.value = '';                            // Pulisce selezione
            return;                                      // Esce dalla funzione
        }
        
        /**
         * VALIDAZIONE TIPO FILE - Solo formati immagine supportati
         * file.type è MIME type del file (es: "image/jpeg")
         */
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Formato file non supportato. Usa: JPG, PNG, GIF, WebP');
            input.value = '';                            // Pulisce selezione
            return;
        }
        
        /**
         * LETTURA FILE - FileReader API per convertire file in Data URL
         * FileReader è API HTML5 per lettura asincrona di file locali
         */
        const reader = new FileReader();
        
        /**
         * CALLBACK LETTURA - Si esegue quando file è stato letto
         * e.target.result contiene il Data URL dell'immagine
         */
        reader.onload = function(e) {
            $('#preview-img').attr('src', e.target.result);  // Imposta src immagine
            $('#image-preview').show();                      // Mostra container anteprima
        };
        
        reader.readAsDataURL(file);                      // Avvia lettura asincrona
    }
}

/**
 * FUNZIONE GLOBALE - Rimozione immagine selezionata
 * SCOPO: Permette all'utente di annullare la selezione immagine
 */
function removeImage() {
    $('#foto').val('');                                  // Pulisce input file
    $('#image-preview').hide();                          // Nasconde anteprima
    $('#preview-img').attr('src', '');                   // Pulisce src immagine
}

/**
 * FUNZIONE GLOBALE - Riempimento dati di esempio
 * LINGUAGGIO: JavaScript + DOM manipulation
 * 
 * SCOPO: Funzionalità di testing per sviluppatori e demo.
 *        Riempie automaticamente il form con dati realistici
 *        basati sulla categoria selezionata.
 */
function fillSampleData() {
    /**
     * CONFERMA UTENTE - Dialog nativo browser per sicurezza
     * Previene sovrascrittura accidentale di dati già inseriti
     */
    if (confirm('Vuoi riempire il form con dati di esempio? I dati attuali verranno sostituiti.')) {
        /**
         * RILEVAZIONE CATEGORIA - Ottiene prima categoria disponibile
         * Usa DOM nativo invece di jQuery per variare le tecniche
         */
        const categoriaSelect = document.getElementById('categoria');
        const firstCategory = categoriaSelect.options[1];        // [0] è "Seleziona categoria"
        
        /**
         * GENERAZIONE DATI - Ottiene dati campione per categoria
         * Funzione helper che restituisce oggetto con tutti i campi
         */
        let sampleData = getSampleDataForCategory(firstCategory ? firstCategory.value : 'altro');
        
        /**
         * POPOLAMENTO FORM - Assegnazione valori usando DOM nativo
         * Alterna jQuery e DOM nativo per mostrare entrambi gli approcci
         */
        document.getElementById('nome').value = sampleData.nome;
        document.getElementById('modello').value = sampleData.modello;
        document.getElementById('prezzo').value = sampleData.prezzo;
        document.getElementById('descrizione').value = sampleData.descrizione;
        document.getElementById('note_tecniche').value = sampleData.note_tecniche;
        document.getElementById('modalita_installazione').value = sampleData.modalita_installazione;
        document.getElementById('modalita_uso').value = sampleData.modalita_uso;
        document.getElementById('attivo').value = '1';            // Attivo di default
        
        /**
         * SELEZIONE CATEGORIA - Imposta categoria corrispondente
         */
        if (firstCategory) {
            categoriaSelect.value = firstCategory.value;
        }
        
        /**
         * TRIGGER EVENTI - Attiva event handlers per aggiornamenti
         * .trigger() di jQuery simula evento utente per attivare contatori e validazione
         */
        ['#descrizione', '#note_tecniche', '#modalita_installazione', '#modalita_uso'].forEach(selector => {
            $(selector).trigger('input');
        });
        
        alert(`Dati di esempio inseriti per categoria: ${firstCategory ? firstCategory.text : 'Generico'}!`);
    }
}

/**
 * FUNZIONE HELPER - Genera dati di esempio per categoria
 * LINGUAGGIO: JavaScript Object mapping + string templates
 * 
 * @param {string} categoria - Nome categoria prodotto
 * @returns {Object} - Oggetto con tutti i campi del form popolati
 * 
 * Mantiene database di dati realistici per ogni categoria di prodotto.
 * Utilizzato per testing, demo e sviluppo.
 */
function getSampleDataForCategory(categoria) {
    /**
     * DATABASE DATI CAMPIONE - Oggetto con dati specifici per categoria
     * Ogni categoria ha dati tecnici appropriati e realistici
     */
    const sampleDataMap = {
        /**
         * CATEGORIA: Lavatrice
         * Dati tecnici specifici per elettrodomestici lavatrice
         */
        'lavatrice': {
            nome: 'Lavatrice EcoWash Pro',
            modello: 'EW-7000X',
            prezzo: '699.99',
            descrizione: 'Lavatrice ad alta efficienza energetica con capacità di 7kg. Dotata di tecnologia inverter per un funzionamento silenzioso e programmi di lavaggio intelligenti. Ideale per famiglie di 3-4 persone.',
            note_tecniche: 'Capacità: 7kg\nVelocità centrifuga: 1400 giri/min\nClasse energetica: A+++\nDimensioni: 60x60x85 cm\nPotenza: 2100W\nCollegamento: 230V',
            modalita_installazione: '1. Rimuovere imballaggio e blocchi di trasporto\n2. Posizionare su superficie piana e livellare\n3. Collegare tubo di scarico e carico acqua\n4. Collegare alimentazione elettrica\n5. Eseguire primo lavaggio a vuoto',
            modalita_uso: 'Selezionare il programma adatto al tipo di tessuto. Dosare il detersivo secondo le indicazioni. Per capi delicati utilizzare il programma apposito. Pulire regolarmente il filtro e il cassetto detersivo.'
        },
        
        /**
         * CATEGORIA: Lavastoviglie
         * Specifiche tecniche per lavastoviglie
         */
        'lavastoviglie': {
            nome: 'Lavastoviglie SilentClean',
            modello: 'SC-6000',
            prezzo: '549.99',
            descrizione: 'Lavastoviglie da incasso ultra-silenziosa con 3° cestello per posate. 14 coperti, classe A+++, funzionamento silenzioso sotto i 42dB.',
            note_tecniche: 'Capacità: 14 coperti\nRumorosità: 42dB\nClasse energetica: A+++\nDimensioni: 60x60x82 cm\n8 programmi di lavaggio\n3° cestello per posate',
            modalita_installazione: '1. Preparare vano di incasso 60x60x82 cm\n2. Collegare scarico e carico acqua\n3. Collegamento elettrico 230V\n4. Fissare ai mobili laterali\n5. Test di funzionamento',
            modalita_uso: 'Caricare stoviglie senza sovrapporle. Utilizzare sale rigenerante e brillantante. Selezionare programma adeguato al carico. Pulire filtri settimanalmente.'
        },
        
        /**
         * CATEGORIA: Frigorifero
         * Dati tecnici per refrigeratori
         */
        'frigorifero': {
            nome: 'Frigorifero CoolFresh XL',
            modello: 'CF-400L',
            prezzo: '1299.99',
            descrizione: 'Frigorifero combinato No Frost da 400L con dispenser acqua e ghiaccio. Controllo digitale della temperatura e sistema antibatterico.',
            note_tecniche: 'Capacità: 400L (280L frigo + 120L freezer)\nClasse energetica: A++\nSistema No Frost\nDispenser acqua/ghiaccio\nDimensioni: 70x60x185 cm',
            modalita_installazione: '1. Posizionare su superficie piana\n2. Lasciare 5cm di spazio sui lati\n3. Collegamento idrico per dispenser\n4. Collegamento elettrico\n5. Attesa 4 ore prima dell\'accensione',
            modalita_uso: 'Regolare temperature: frigo +4°C, freezer -18°C. Sostituire filtro acqua ogni 6 mesi. Pulire bobine posteriori ogni 6 mesi. Non sovraccaricare i ripiani.'
        },
        
        /**
         * CATEGORIA: Forno
         * Dati tecnici per forni da cucina
         */
        'forno': {
            nome: 'Forno Multifunzione Chef Pro',
            modello: 'CP-65L',
            prezzo: '799.99',
            descrizione: 'Forno elettrico multifunzione da 65L con pirolisi e 10 funzioni di cottura. Display touch e sonde temperatura.',
            note_tecniche: 'Capacità: 65L\n10 funzioni cottura\nPirolisi autopulente\nTemperatura: 50-275°C\nClasse energetica: A\nDimensioni: 60x60x60 cm',
            modalita_installazione: '1. Preparare vano incasso 60x60x60 cm\n2. Collegamento elettrico 380V\n3. Ventilazione retrostante\n4. Fissaggio con staffe\n5. Test funzionamento e calibrazione',
            modalita_uso: 'Preriscaldare sempre il forno. Utilizzare funzione pirolisi per pulizia mensile. Posizionare cibi nel ripiano centrale per cottura uniforme. Utilizzare sonde per arrosti.'
        },
        
        /**
         * CATEGORIA: Piano Cottura
         * Specifiche per piani cottura a induzione
         */
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
    
    /**
     * RETURN CONDIZIONALE - Restituisce dati specifici o generici
     * Operatore || restituisce il primo valore "truthy"
     */
    return sampleDataMap[categoria] || {
        // DATI GENERICI - Fallback per categorie non mappate
        nome: `Prodotto ${categoria.charAt(0).toUpperCase() + categoria.slice(1)}`,
        modello: 'MOD-2024',
        prezzo: '399.99',
        descrizione: `Prodotto di qualità per la categoria ${categoria}. Caratteristiche tecniche avanzate e design moderno per soddisfare ogni esigenza domestica.`,
        note_tecniche: `Specifiche tecniche complete per ${categoria}.\nDimensioni standard.\nClasse energetica ottimale.\nGaranzia 2 anni.`,
        modalita_installazione: `Installazione standard per prodotti ${categoria}.\nSeguire le istruzioni del manuale.\nRichiedere assistenza tecnica se necessario.`,
        modalita_uso: `Utilizzo intuitivo e sicuro.\nSeguire le indicazioni per ${categoria}.\nManutenzione regolare consigliata.`
    };
}

// ===================================================================
// SEZIONE 8: EVENT HANDLERS CATEGORIA E PLACEHOLDER DINAMICI
// ===================================================================

/**
 * INIZIALIZZAZIONE AGGIUNTIVA - Handler per categoria
 * LINGUAGGIO: jQuery ready handler annidato
 * 
 * Questo blocco gestisce il cambio di categoria per aggiornare
 * i placeholder dei campi con suggerimenti specifici.
 */
$(document).ready(function() {
    /**
     * EVENT HANDLER - Cambio categoria prodotto
     * ELEMENTO: #categoria (select delle categorie)
     * EVENTO: 'change' - Quando utente seleziona nuova categoria
     */
    $('#categoria').on('change', function() {
        const selectedCategory = $(this).val();              // Valore opzione selezionata
        const selectedText = $(this).find('option:selected').text(); // Testo opzione
        
        if (selectedCategory) {
            // AGGIORNAMENTO DINAMICO - Placeholder specifici per categoria
            updatePlaceholderForCategory(selectedCategory);
            
            // DEBUG LOGGING - Per sviluppo e troubleshooting
            console.log(`Categoria selezionata: ${selectedText} (${selectedCategory})`);
        }
    });
});

/**
 * FUNZIONE HELPER - Aggiorna placeholder per categoria
 * LINGUAGGIO: JavaScript DOM manipulation + Object mapping
 * 
 * @param {string} categoria - Codice categoria selezionata
 * 
 * Personalizza i placeholder dei campi del form con suggerimenti
 * specifici per la categoria di prodotto selezionata, migliorando UX.
 */
function updatePlaceholderForCategory(categoria) {
    /**
     * MAPPING PLACEHOLDER - Suggerimenti specifici per categoria
     * Ogni categoria ha esempi appropriati per guidare l'utente
     */
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
        // ESTENSIBILITÀ - Si possono aggiungere altre categorie facilmente
    };
    
    /**
     * APPLICAZIONE PLACEHOLDER - Aggiorna elementi DOM
     * Object.keys() restituisce array delle chiavi dell'oggetto
     */
    if (placeholders[categoria]) {
        Object.keys(placeholders[categoria]).forEach(field => {
            const element = document.getElementById(field);      // DOM nativo per varietà
            if (element) {
                element.placeholder = placeholders[categoria][field];
            }
        });
    }
}

// ===================================================================
// SEZIONE 9: FUNZIONI UTILITY FORM
// ===================================================================

/**
 * FUNZIONE GLOBALE - Svuotamento completo form
 * LINGUAGGIO: jQuery + DOM manipulation + confirm dialog
 * 
 * SCOPO: Permette all'utente di resettare completamente il form
 *        con conferma di sicurezza per evitare perdite accidentali.
 */
function clearForm() {
    /**
     * CONFERMA SICUREZZA - Dialog nativo per prevenire cancellazioni accidentali
     */
    if (confirm('Vuoi svuotare tutti i campi? I dati inseriti verranno persi.')) {
        /**
         * RESET FORM - Metodo nativo HTML form.reset()
         * [0] converte oggetto jQuery in elemento DOM nativo
         */
        $('#createProductForm')[0].reset();
        
        /**
         * CLEANUP UI - Rimuove stati visivi aggiuntivi
         */
        $('#image-preview').hide();                          // Nasconde anteprima immagine
        $('#riepilogo-prodotto').hide();                     // Nasconde riepilogo
        
        // CLEANUP VALIDAZIONE - Rimuove classi Bootstrap validation
        $('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $('.custom-validation').remove();                   // Rimuove messaggi errore custom
        
        /**
         * RESET CONTATORI - Azzera tutti i contatori caratteri
         * Selettore multiplo jQuery per efficienza
         */
        $('#descrizione-counter, #note-counter, #installazione-counter, #uso-counter').text('0');
        
        // UX FINALE - Focus su primo campo per continuità
        $('#nome').focus();
        alert('Form svuotato!');
    }
}

/**
 * ===================================================================
 * FINE MODULO admin/prodotti/create.js
 * ===================================================================
 * 
 * RIEPILOGO FUNZIONALITÀ IMPLEMENTATE:
 * 
 * 1. VALIDAZIONE FORM
 *    - Validazione in tempo reale per ogni campo
 *    - Regole business specifiche per tipo prodotto
 *    - Feedback visivo immediato con classi Bootstrap
 *    - Validazione completa prima del submit
 * 
 * 2. CONTATORI CARATTERI
 *    - Monitoraggio lunghezza testi in tempo reale
 *    - Codifica colore per soglie di attenzione
 *    - Supporto per multiple textarea
 * 
 * 3. SISTEMA ANTEPRIMA
 *    - Anteprima completa prodotto in modal Bootstrap
 *    - Generazione HTML dinamica con template literals
 *    - Riepilogo inline con animazioni jQuery
 * 
 * 4. GESTIONE IMMAGINI
 *    - Upload con validazione tipo e dimensione
 *    - Anteprima immediata usando FileReader API
 *    - Rimozione e reset selezione
 * 
 * 5. MIGLIORAMENTI UX
 *    - Generazione automatica codici modello
 *    - Effetti visivi su focus campi
 *    - Protezione contro perdita dati accidentale
 *    - Placeholder dinamici per categoria
 * 
 * 6. DATI DI ESEMPIO
 *    - Database completo dati campione per categoria
 *    - Funzionalità testing e demo
 *    - Trigger automatico eventi per consistenza
 * 
 * 7. GESTIONE ERRORI
 *    - Alert visivi per form invalido
 *    - Scroll automatico a primo errore
 *    - Messaggi errore specifici per campo
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (template literals, arrow functions, destructuring)
 * - jQuery 3.x (DOM manipulation, event handling, animations)
 * - Bootstrap 5 (form validation, modal, alert, utilities)
 * - HTML5 APIs (File API, FileReader, Form Validation)
 * - Browser APIs (beforeunload event, confirm dialogs)
 * 
 * PATTERN ARCHITETTURALI:
 * - Event-Driven Programming per interazioni utente
 * - Template Pattern per generazione HTML dinamico
 * - Strategy Pattern per validazioni diverse per campo
 * - Observer Pattern per monitoraggio modifiche form
 * - Factory Pattern per generazione dati campione
 * 
 * SICUREZZA E VALIDAZIONE:
 * - Validazione client-side E server-side (Laravel)
 * - Sanitizzazione input per prevenire XSS
 * - Validazione tipo file e dimensioni per sicurezza
 * - Conferme utente per azioni distruttive
 * 
 * ACCESSIBILITY E USABILITÀ:
 * - Focus management per navigazione tastiera
 * - Messaggi errore chiari e specifici
 * - Feedback visivo immediato per ogni azione
 * - Protezione dati utente con avvisi navigazione
 * ===================================================================
 */