/**
 * FILE: edit.js
 * LINGUAGGIO: JavaScript (ES6+) con jQuery
 * SCOPO: Gestione dell'interfaccia per la modifica di soluzioni esistenti nella dashboard dello staff
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Bootstrap 5, LocalStorage API
 * FUNZIONALITÀ PRINCIPALI: Anteprima modifiche, validazione form, auto-save, scorciatoie tastiera
 * AUTORE: Sistema di assistenza tecnica
 */

/**
 * DOCUMENT READY EVENT - jQuery
 * Si attiva quando il DOM è completamente caricato e pronto per la manipolazione
 * Equivalente a document.addEventListener('DOMContentLoaded', function(){})
 */
$(document).ready(function() {
    
    /**
     * LOG DI CARICAMENTO PER DEBUG
     * console.log: stampa messaggio nella console del browser per verificare il caricamento
     */
    console.log('malfunzionamenti.edit caricato');
    
    // === CONTROLLO ROUTE ATTIVA ===
    /**
     * VERIFICA DELLA ROUTE CORRENTE
     * window.LaravelApp?.route: variabile globale impostata dal backend Laravel
     * Operatore ?. (optional chaining): evita errori se LaravelApp non esiste
     * Se la route non corrisponde, il JavaScript termina l'esecuzione
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'staff.malfunzionamenti.edit') {
        return; // Termina l'esecuzione se non siamo nella pagina di modifica
    }
    
    // === VARIABILI GLOBALI DI CONFIGURAZIONE ===
    /**
     * INIZIALIZZAZIONE VARIABILI
     * pageData: dati della pagina passati dal backend Laravel
     * selectedProducts: array per tracking prodotti (non utilizzato in questa pagina)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // === GESTIONE CHECKBOX ELIMINAZIONE ===
    /**
     * EVENT HANDLER PER CHECKBOX DI CONFERMA ELIMINAZIONE
     * $('#confirmDelete'): selettore jQuery per elemento con ID "confirmDelete"
     * .on('change', function): attacca event listener per evento "change"
     * L'evento change si scatena quando lo stato della checkbox cambia
     */
    $('#confirmDelete').on('change', function() {
        /**
         * CONTROLLO STATO CHECKBOX E ABILITAZIONE PULSANTE
         * this.checked: proprietà booleana che indica se la checkbox è selezionata
         * .prop('disabled', boolean): metodo jQuery per abilitare/disabilitare elemento
         * !this.checked: inverte il valore (se checked=true, disabled=false)
         */
        $('#confirmDeleteBtn').prop('disabled', !this.checked);
    });
    
    // === SISTEMA DI ANTEPRIMA MODIFICHE ===
    /**
     * EVENT HANDLER PER PULSANTE ANTEPRIMA
     * Quando l'utente clicca "Anteprima", genera preview e mostra modal
     */
    $('#previewBtn').on('click', function() {
        generatePreview(); // Chiama funzione per generare anteprima
        $('#previewModal').modal('show'); // Mostra modal Bootstrap
    });
    
    /**
     * FUNZIONE PER GENERARE ANTEPRIMA DELLE MODIFICHE
     * Confronta i dati originali con quelli attuali del form
     * Evidenzia le differenze per review dell'utente
     */
    function generatePreview() {
        
        /**
         * DATI ORIGINALI DEL MALFUNZIONAMENTO
         * window.malfunzionamento: oggetto JavaScript globale creato dal backend Laravel
         * Contiene i valori originali prima delle modifiche
         */
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
        
        /**
         * DATI CORRENTI DAL FORM
         * .val(): metodo jQuery per ottenere il valore corrente dei campi input
         * Rappresenta quello che l'utente ha modificato
         */
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
        
        /**
         * MAPPING VALORI GRAVITÀ CON EMOJI E ETICHETTE
         * Oggetto JavaScript per convertire codici in etichette user-friendly
         * Le emoji forniscono feedback visivo immediato
         */
        const gravitaLabels = {
            'bassa': '🟢 Bassa',
            'media': '🟡 Media', 
            'alta': '🟠 Alta',
            'critica': '🔴 Critica'
        };
        
        /**
         * MAPPING VALORI DIFFICOLTÀ CON STELLE
         * Sistema di rating con stelle per indicare il livello di difficoltà
         */
        const difficoltaLabels = {
            'facile': '⭐ Facile',
            'media': '⭐⭐ Media',
            'difficile': '⭐⭐⭐ Difficile', 
            'esperto': '⭐⭐⭐⭐ Esperto'
        };
        
        /**
         * FUNZIONE HELPER PER EVIDENZIARE MODIFICHE
         * @param {string} originalValue - Valore originale del campo
         * @param {string} currentValue - Valore attuale del campo  
         * @param {string} fieldName - Nome del campo (per debug)
         * @returns {string} HTML con evidenziazione se modificato
         */
        function highlightChange(originalValue, currentValue, fieldName) {
            /**
             * CONFRONTO VALORI E GENERAZIONE HTML CONDIZIONALE
             * != (non uguale): confronta valori convertendoli automaticamente
             * Operatore ternario: condizione ? valore_se_vero : valore_se_falso
             */
            if (originalValue != currentValue) {
                // Se modificato, avvolge in span con classe CSS per evidenziazione
                return `<span class="highlight-change" title="Modificato da: ${originalValue}">${currentValue}</span>`;
            }
            // Se non modificato, restituisce valore corrente o placeholder
            return currentValue || '<em class="text-muted">Non inserito</em>';
        }
        
        /**
         * GENERAZIONE HTML DELL'ANTEPRIMA
         * Utilizza template literals (backticks) per creare HTML multi-riga
         * ${variabile}: interpolazione di variabili JavaScript nell'HTML
         */
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
        
        /**
         * SEZIONI CONDIZIONALI PER CAMPI OPZIONALI
         * Mostra strumenti e tempo solo se compilati dall'utente
         * Operatore ternario per HTML condizionale
         */
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
        
        /**
         * CONTEGGIO E VISUALIZZAZIONE MODIFICHE
         * Itera attraverso tutti i campi per contare le modifiche
         */
        let changesCount = 0;
        /**
         * Object.keys(): metodo JavaScript che restituisce array delle chiavi di un oggetto
         * .forEach(): metodo array che esegue una funzione per ogni elemento
         */
        Object.keys(original).forEach(key => {
            if (original[key] != current[key]) {
                changesCount++; // Incrementa contatore se valore diverso
            }
        });
        
        /**
         * ALERT CONDIZIONALE BASATO SUL NUMERO DI MODIFICHE
         * Mostra messaggio diverso se ci sono modifiche o no
         */
        if (changesCount > 0) {
            // Se ci sono modifiche, mostra alert arancione con conteggio
            html = `<div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>${changesCount} modifica${changesCount > 1 ? 'he' : ''} rilevata${changesCount > 1 ? 'e' : ''}:</strong> 
                I campi evidenziati sono stati modificati rispetto alla versione originale.
            </div>` + html;
        } else {
            // Se non ci sono modifiche, mostra alert blu informativo
            html = `<div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Nessuna modifica rilevata. La soluzione rimane invariata.
            </div>` + html;
        }
        
        /**
         * INSERIMENTO HTML NELL'ELEMENTO MODAL
         * .html(): metodo jQuery per sostituire il contenuto HTML di un elemento
         */
        $('#previewContent').html(html);
    }
    
    /**
     * FUNZIONE HELPER PER MAPPARE GRAVITÀ A CLASSI CSS BOOTSTRAP
     * @param {string} gravita - Livello di gravità del problema
     * @returns {string} Nome classe CSS Bootstrap per il colore
     */
    function getGravitaClass(gravita) {
        /**
         * MAPPING GRAVITÀ -> CLASSI BOOTSTRAP
         * Oggetto JavaScript per associare valori di gravità a classi CSS
         */
        const classes = {
            'bassa': 'success',    // Verde
            'media': 'info',       // Azzurro  
            'alta': 'warning',     // Giallo
            'critica': 'danger'    // Rosso
        };
        // Operatore || per valore di default se gravità non riconosciuta
        return classes[gravita] || 'secondary';
    }
    
    /**
     * SUBMIT DAL MODAL DI ANTEPRIMA
     * Permette di salvare direttamente dall'anteprima senza tornare al form
     */
    $('#updateFromPreview').on('click', function() {
        $('#previewModal').modal('hide'); // Nasconde modal anteprima
        $('#editSoluzioneForm').submit(); // Invia form principale
    });
    
    // === VALIDAZIONE CLIENT-SIDE ===
    /**
     * EVENT HANDLER PER SUBMIT DEL FORM
     * Esegue validazione lato client prima dell'invio al server
     */
    $('#editSoluzioneForm').on('submit', function(e) {
        let isValid = true; // Flag per tracciare validità complessiva
        
        /**
         * DEFINIZIONE CAMPI OBBLIGATORI
         * Array JavaScript contenente ID dei campi che devono essere compilati
         */
        const requiredFields = ['titolo', 'descrizione', 'gravita', 'difficolta', 'soluzione'];
        
        /**
         * ITERAZIONE E VALIDAZIONE CAMPI OBBLIGATORI
         * .forEach(): esegue funzione per ogni elemento dell'array
         */
        requiredFields.forEach(function(field) {
            const element = $(`#${field}`); // Selettore jQuery dinamico
            
            /**
             * CONTROLLO VALORE CAMPO
             * .val().trim(): ottiene valore e rimuove spazi bianchi iniziali/finali
             * !valore: verifica se stringa è vuota (falsy)
             */
            if (!element.val().trim()) {
                element.addClass('is-invalid'); // Aggiunge classe Bootstrap per errore
                isValid = false; // Marca form come non valido
            } else {
                element.removeClass('is-invalid'); // Rimuove classe errore se valido
            }
        });
        
        /**
         * GESTIONE FORM NON VALIDO
         * Se ci sono errori, previene l'invio e mostra feedback
         */
        if (!isValid) {
            /**
             * .preventDefault(): metodo JavaScript per bloccare comportamento default
             * In questo caso impedisce l'invio del form
             */
            e.preventDefault();
            
            /**
             * SCROLL AUTOMATICO AL PRIMO ERRORE
             * Migliora UX portando l'utente al primo campo con errore
             */
            const firstError = $('.is-invalid').first(); // Primo elemento con errore
            if (firstError.length) {
                /**
                 * .animate(): metodo jQuery per animazioni fluide
                 * scrollTop: proprietà CSS per posizione scroll verticale
                 * .offset().top: posizione assoluta dell'elemento nella pagina
                 * -100: offset per non nascondere elemento sotto header fisso
                 */
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500); // Durata animazione 500ms
            }
            
            // Mostra messaggio di errore all'utente
            showAlert('danger', 'Compila tutti i campi obbligatori prima di salvare.');
        } else {
            /**
             * FORM VALIDO - PREVENZIONE DOPPI SUBMIT
             * Disabilita pulsante e mostra feedback visivo durante invio
             */
            $('#updateBtn').prop('disabled', true) // Disabilita pulsante
                          .html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Salvando...'); // Cambia testo con spinner
        }
    });
    
    // === SISTEMA AUTO-SAVE DRAFT (localStorage) ===
    /**
     * CONFIGURAZIONE AUTO-SAVE
     * Sistema per salvare automaticamente le modifiche in locale
     */
    const formFields = ['titolo', 'descrizione', 'gravita', 'difficolta', 'soluzione', 'strumenti_necessari', 'tempo_stimato'];
    /**
     * CHIAVE UNICA PER LOCALSTORAGE
     * Template literal con ID malfunzionamento per chiave univoca
     * {{ $malfunzionamento->id }}: sintassi Blade per inserire valore PHP
     */
    const draftKey = 'soluzione_edit_draft_{{ $malfunzionamento->id }}';
    
    // Carica eventuali draft salvati all'avvio
    loadDraft();
    
    /**
     * AUTO-SAVE PERIODICO
     * setInterval(): funzione JavaScript per eseguire codice ripetutamente
     * 30000ms = 30 secondi tra un salvataggio e l'altro
     */
    setInterval(saveDraft, 30000);
    
    /**
     * AUTO-SAVE SU MODIFICA CAMPI
     * Attacca event listener a tutti i campi per salvare quando cambiano
     */
    formFields.forEach(field => {
        $(`#${field}`).on('change', saveDraft);
    });
    
    /**
     * FUNZIONE PER SALVARE DRAFT IN LOCALSTORAGE
     * Crea snapshot dei dati correnti del form
     */
    function saveDraft() {
        const draft = {}; // Oggetto per contenere i dati
        
        /**
         * RACCOLTA DATI DAI CAMPI
         * Itera sui campi e salva solo quelli con valore
         */
        formFields.forEach(field => {
            const value = $(`#${field}`).val();
            if (value) draft[field] = value; // Salva solo se non vuoto
        });
        
        /**
         * SALVATAGGIO IN LOCALSTORAGE
         * Object.keys().length: conta proprietà dell'oggetto
         * JSON.stringify(): converte oggetto JavaScript in stringa JSON
         * localStorage.setItem(): salva dati nel browser
         */
        if (Object.keys(draft).length > 0) {
            localStorage.setItem(draftKey, JSON.stringify(draft));
            console.log('Draft modifiche salvato'); // Log per debug
        }
    }
    
    /**
     * FUNZIONE PER CARICARE DRAFT SALVATO
     * Ripristina dati dall'ultima sessione interrotta
     */
    function loadDraft() {
        /**
         * RECUPERO DATI DA LOCALSTORAGE
         * localStorage.getItem(): legge dati salvati
         */
        const draft = localStorage.getItem(draftKey);
        if (draft) {
            try {
                /**
                 * PARSING E RIPRISTINO DATI
                 * JSON.parse(): converte stringa JSON in oggetto JavaScript
                 * Try-catch per gestire errori di parsing
                 */
                const data = JSON.parse(draft);
                let hasChanges = false;
                
                /**
                 * APPLICAZIONE DRAFT AI CAMPI
                 * Confronta draft con valori attuali e ripristina se diversi
                 */
                Object.keys(data).forEach(field => {
                    const currentValue = $(`#${field}`).val();
                    if (data[field] !== currentValue) {
                        $(`#${field}`).val(data[field]); // Imposta valore dal draft
                        hasChanges = true;
                    }
                });
                
                // Notifica utente se draft è stato applicato
                if (hasChanges) {
                    showAlert('info', 'Draft precedente caricato automaticamente. Le modifiche non salvate sono state ripristinate.');
                }
            } catch (e) {
                // Gestione errori nel parsing del draft
                console.warn('Errore nel caricamento draft:', e);
            }
        }
    }
    
    /**
     * PULIZIA DRAFT AL SUBMIT RIUSCITO
     * Rimuove draft quando form viene inviato correttamente
     */
    $('#editSoluzioneForm').on('submit', function() {
        /**
         * CONTROLLO VALIDITÀ NATIVA HTML5
         * checkValidity(): metodo nativo per validazione HTML5
         * [0]: accede al primo elemento del set jQuery (elemento DOM nativo)
         */
        if ($(this)[0].checkValidity()) {
            localStorage.removeItem(draftKey); // Rimuove draft dal localStorage
        }
    });
    
    // === CONTROLLI AGGIUNTIVI DI VALIDAZIONE ===
    
    /**
     * VALIDAZIONE COERENZA DATE
     * Controlla che prima segnalazione non sia successiva all'ultima
     */
    $('#prima_segnalazione, #ultima_segnalazione').on('change', function() {
        const prima = $('#prima_segnalazione').val();
        const ultima = $('#ultima_segnalazione').val();
        
        /**
         * CONFRONTO DATE
         * In JavaScript, stringhe di date in formato ISO si possono confrontare direttamente
         */
        if (prima && ultima && prima > ultima) {
            showAlert('warning', 'La data della prima segnalazione non può essere successiva all\'ultima segnalazione.');
            $(this).focus(); // Riporta focus sul campo con errore
        }
    });
    
    /**
     * SUGGERIMENTI AUTOMATICI BASATI SULLA GRAVITÀ
     * Fornisce assistenza contestuale all'utente
     */
    $('#gravita').on('change', function() {
        const gravita = $(this).val();
        /**
         * LOGICA DI BUSINESS PER PROBLEMI CRITICI
         * Se gravità critica e numero segnalazioni vuoto, imposta default
         */
        if (gravita === 'critica' && !$('#numero_segnalazioni').val()) {
            $('#numero_segnalazioni').val('1');
            showAlert('info', 'Per problemi critici è consigliabile specificare il numero di segnalazioni.');
        }
    });
    
    // === FUNZIONI HELPER ===
    
    /**
     * FUNZIONE PER MOSTRARE ALERT TEMPORANEI
     * Sistema di notifiche toast per feedback utente
     * @param {string} type - Tipo di alert (danger, warning, success, info)
     * @param {string} message - Messaggio da mostrare
     */
    function showAlert(type, message) {
        /**
         * MAPPING TIPI -> CLASSI BOOTSTRAP
         * Operatore ternario nidificato per determinare classe CSS
         */
        const alertClass = type === 'danger' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        /**
         * MAPPING TIPI -> ICONE BOOTSTRAP
         * Determina icona appropriata per ogni tipo di messaggio
         */
        const icon = type === 'danger' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        /**
         * CREAZIONE ELEMENTO ALERT DINAMICO
         * Template literal per creare HTML complesso con stili inline
         */
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        /**
         * INSERIMENTO E GESTIONE ALERT
         * .append(): aggiunge elemento alla fine del body
         */
        $('body').append(alert);
        
        /**
         * AUTO-RIMOZIONE ALERT
         * setTimeout(): esegue codice dopo delay specificato
         * .fadeOut(): animazione jQuery per nascondere elemento
         * Callback function per rimuovere elemento dal DOM
         */
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000); // 5 secondi di visualizzazione
    }
    
    // === SCORCIATOIE DA TASTIERA ===
    /**
     * EVENT HANDLER GLOBALE PER TASTI
     * $(document).on(): attacca event listener a livello documento
     * Gestisce combinazioni di tasti per azioni rapide
     */
    $(document).on('keydown', function(e) {
        
        /**
         * CTRL+S PER SALVARE
         * e.ctrlKey: proprietà booleana per tasto Ctrl premuto
         * e.key: proprietà stringa con il tasto premuto
         */
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault(); // Previene comportamento default del browser (save page)
            $('#editSoluzioneForm').submit(); // Invia form
        }
        
        /**
         * CTRL+P PER ANTEPRIMA
         * Sovrascrive stampa del browser per mostrare anteprima modifiche
         */
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault(); // Previene dialog di stampa del browser
            $('#previewBtn').click(); // Simula click su pulsante anteprima
        }
        
        /**
         * ESC PER CHIUDERE MODAL
         * Fornisce modo rapido per chiudere finestre modali
         */
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide'); // Nasconde tutti i modal aperti
        }
    });
    
    /**
     * LOG FINALE DI INIZIALIZZAZIONE
     * Conferma che tutti gli event handler sono stati configurati
     */
    console.log('Form modifica soluzione inizializzato');
});

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, arrow functions, template literals, destructuring)
 * - jQuery (event handling, DOM manipulation, animations, AJAX)
 * - Bootstrap 5 (modal, alerts, form validation classes, responsive grid)
 * - Bootstrap Icons (icone per UI feedback)  
 * - LocalStorage API (persistenza dati lato client)
 * - HTML5 Form Validation API (checkValidity())
 * - Laravel Blade (template engine per generare JavaScript)
 * 
 * PATTERN E PRINCIPI UTILIZZATI:
 * - Event-driven programming (gestione eventi utente)
 * - Progressive enhancement (funzionalità aggiuntive senza rompere base)
 * - Client-side validation (feedback immediato prima invio server)
 * - Auto-save/Draft system (prevenzione perdita dati)
 * - Accessibility (keyboard shortcuts, focus management)
 * - User experience (preview, animations, loading states)
 * - Error handling (try-catch, graceful degradation)
 * - Separation of concerns (funzioni specifiche per compiti specifici)
 */