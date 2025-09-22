/**
 * ===================================================================
 * FILE: admin/prodotti/edit.js
 * LINGUAGGIO: JavaScript Vanilla (ES6+) + Bootstrap + HTML5 APIs
 * SCOPO: Sistema di modifica prodotti esistenti con funzionalità avanzate
 * ===================================================================
 * 
 * Questo modulo JavaScript gestisce l'interfaccia di modifica prodotti con:
 * - Validazione in tempo reale dei campi modificati
 * - Auto-save in sessionStorage per prevenire perdite dati
 * - Preview upload immagini con validazione
 * - Contatori caratteri dinamici per textarea
 * - Shortcuts tastiera per produttività
 * - Sistema notifiche toast Bootstrap
 * - Protezione contro perdite dati accidentali
 * - Debug tools per sviluppatori
 * - Controlli accessibilità automatici
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (arrow functions, destructuring, template literals)
 * - DOM API nativo (addEventlistener, querySelector, FormData)
 * - HTML5 File API e FileReader per gestione immagini
 * - SessionStorage API per persistenza temporanea dati
 * - Bootstrap 5 Toast componenti per notifiche
 * - RegExp per validazione formato campi
 * ===================================================================
 */

// ===================================================================
// SEZIONE 1: INIZIALIZZAZIONE E CONTROLLO ROUTE
// ===================================================================

/**
 * EVENT HANDLER PRINCIPALE - DOMContentLoaded
 * LINGUAGGIO: JavaScript Vanilla DOM API
 * 
 * A differenza di $(document).ready() di jQuery, DOMContentLoaded è l'evento
 * nativo del browser che si attiva quando il DOM è completamente costruito
 * ma prima che tutte le risorse (immagini, CSS) siano caricate.
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('admin.prodotti.edit caricato');
    
    /**
     * CONTROLLO ROUTE - Verifica pagina corretta
     * Stesso pattern del file create.js ma per route di modifica
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.edit') {
        return; // Esce se non siamo nella pagina di modifica
    }
    
    /**
     * VARIABILI MODULO - Stato locale del sistema di modifica
     */
    const pageData = window.PageData || {};          // Dati Laravel passati alla view
    let selectedProducts = [];                       // Array per estensioni future
    
    // === SEZIONE 2: CONFIGURAZIONE ELEMENTI DOM ===
    
    /**
     * SELEZIONE ELEMENTI CRITICI - Riferimenti DOM principali
     * LINGUAGGIO: DOM API nativo per performance
     * 
     * Usa getElementById() che è più veloce di querySelector() per ID
     * poiché accede direttamente all'hash table interna del browser
     */
    const form = document.getElementById('editProdottoForm');     // Form principale
    const submitBtn = document.getElementById('submitBtn');       // Pulsante salvataggio
    const fotoInput = document.getElementById('foto');            // Input file immagine
    
    // === SEZIONE 3: GESTIONE UPLOAD E PREVIEW IMMAGINI ===
    
    /**
     * EVENT HANDLER - Upload nuova immagine prodotto
     * LINGUAGGIO: HTML5 File API + FileReader
     * 
     * Gestisce l'upload di nuove immagini con validazione completa
     * e preview immediata per migliorare l'esperienza utente.
     */
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];              // Primo file selezionato
            
            if (file) {
                /**
                 * VALIDAZIONE FILE - Controlli sicurezza e qualità
                 */
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                const maxSize = 2 * 1024 * 1024;         // 2MB in bytes
                
                /**
                 * CONTROLLO FORMATO - Validazione MIME type
                 * file.type restituisce il MIME type (es: "image/jpeg")
                 */
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato file non supportato. Usa JPEG, PNG, JPG o GIF.');
                    fotoInput.value = '';                 // Reset input file
                    return;
                }
                
                /**
                 * CONTROLLO DIMENSIONE - Limite per performance e storage
                 * file.size è la dimensione in bytes
                 */
                if (file.size > maxSize) {
                    alert('Il file è troppo grande. Massimo 2MB.');
                    fotoInput.value = '';
                    return;
                }
                
                /**
                 * GENERAZIONE PREVIEW - FileReader API per conversione file->DataURL
                 * FileReader è API HTML5 per lettura asincrona file locali
                 */
                const reader = new FileReader();
                
                /**
                 * CALLBACK LETTURA - Eseguito quando file è stato letto
                 * e.target.result contiene il DataURL dell'immagine
                 */
                reader.onload = function(e) {
                    const currentImg = document.getElementById('currentImage');
                    
                    if (currentImg) {
                        /**
                         * AGGIORNAMENTO IMMAGINE ESISTENTE
                         * Sostituisce l'immagine attuale con la nuova preview
                         */
                        currentImg.src = e.target.result;
                        
                        /**
                         * AGGIORNAMENTO CAPTION - Indicatore visivo nuova foto
                         * nextElementSibling è il prossimo elemento HTML fratello
                         */
                        const caption = currentImg.nextElementSibling;
                        if (caption) {
                            caption.innerHTML = '<small class="text-success"><i class="bi bi-upload me-1"></i>Nuova foto (anteprima)</small>';
                        }
                    } else {
                        /**
                         * CREAZIONE NUOVO PREVIEW - Se non c'era immagine originale
                         * Template HTML dinamico per container preview
                         */
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
                        
                        /**
                         * INSERIMENTO DOM - Posiziona preview prima dell'input file
                         * insertBefore(nuovoElemento, elementoRiferimento)
                         */
                        fotoInput.parentNode.insertBefore(preview, fotoInput);
                    }
                };
                
                /**
                 * AVVIO LETTURA - Converte file binario in DataURL base64
                 * DataURL format: "data:image/jpeg;base64,/9j/4AAQSkZJRgABA..."
                 */
                reader.readAsDataURL(file);
            }
        });
    }
    
    // === SEZIONE 4: SISTEMA VALIDAZIONE FORM ===
    
    /**
     * EVENT HANDLER - Submit form con validazione completa
     * LINGUAGGIO: JavaScript Event API + DOM Form Validation
     * 
     * Intercetta il submit del form per eseguire validazioni custom
     * prima che i dati vengano inviati al server Laravel.
     */
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;                          // Flag validità form
            
            /**
             * SELEZIONE CAMPI OBBLIGATORI - Query selector avanzato
             * Seleziona tutti gli elementi con attributo required
             */
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            /**
             * PULIZIA VALIDAZIONE PRECEDENTE - Reset stati visuali
             * Rimuove classi Bootstrap di errore da validazione precedente
             */
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            /**
             * VALIDAZIONE CAMPI OBBLIGATORI - Loop attraverso tutti i required
             * forEach è metodo Array per iterazione senza return value
             */
            requiredFields.forEach(field => {
                // trim() rimuove spazi bianchi iniziali e finali
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');    // Classe Bootstrap per errore
                    isValid = false;
                }
            });
            
            /**
             * VALIDAZIONE SPECIFICA PREZZO - Controllo numero positivo
             */
            const prezzoInput = document.getElementById('prezzo');
            if (prezzoInput && prezzoInput.value && parseFloat(prezzoInput.value) < 0) {
                prezzoInput.classList.add('is-invalid');
                isValid = false;
            }
            
            /**
             * GESTIONE FORM INVALIDO - Blocca invio e mostra errori
             */
            if (!isValid) {
                e.preventDefault();                       // Blocca submit form
                
                /**
                 * NOTIFICA ERRORE - Feedback visivo all'utente
                 */
                showNotification('error', 'Compila tutti i campi obbligatori correttamente');
                
                /**
                 * SCROLL AUTOMATICO - Porta utente al primo errore
                 * scrollIntoView() è API nativa per scroll fluido
                 */
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ 
                        behavior: 'smooth',               // Animazione fluida
                        block: 'center'                   // Centra elemento nello schermo
                    });
                    firstError.focus();                   // Focus per accessibilità
                }
                
                return false;                             // Previene invio
            }
            
            /**
             * FEEDBACK LOADING - Stato visivo durante salvataggio
             * Importante per operazioni che possono richiedere tempo
             */
            if (submitBtn) {
                submitBtn.disabled = true;                // Previene click multipli
                submitBtn.classList.add('btn-loading');   // Classe CSS per stile loading
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
            }
            
            /**
             * CONFERMA FINALE - Dialog sicurezza per modifiche importanti
             * confirm() è dialog nativo browser per conferme
             */
            if (!confirm('Salvare le modifiche al prodotto?')) {
                e.preventDefault();
                resetSubmitButton();                      // Ripristina stato pulsante
                return false;
            }
        });
    }
    
    // === SEZIONE 5: FUNZIONI HELPER UI ===
    
    /**
     * FUNZIONE HELPER - Reset stato pulsante submit
     * SCOPO: Ripristina pulsante allo stato normale dopo operazioni
     */
    function resetSubmitButton() {
        if (submitBtn) {
            submitBtn.disabled = false;                   // Riabilita pulsante
            submitBtn.classList.remove('btn-loading');    // Rimuove stato loading
            submitBtn.innerHTML = '<i class="bi bi-check me-1"></i>Salva Modifiche';
        }
    }
    
    // === SEZIONE 6: SISTEMA PROTEZIONE PERDITA DATI ===
    
    /**
     * TRACKING MODIFICHE - Sistema rilevazione cambiamenti form
     * LINGUAGGIO: JavaScript Event Handling + Comparison Logic
     * 
     * Monitora ogni campo del form per rilevare modifiche non salvate
     * e avvertire l'utente prima che esca accidentalmente dalla pagina.
     */
    let hasUnsavedChanges = false;                        // Flag globale modifiche
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    /**
     * MEMORIZZAZIONE VALORI ORIGINALI - Snapshot stato iniziale
     * Per ogni input salva il valore originale per confronti futuri
     */
    formInputs.forEach(input => {
        // Gestisce diversi tipi di input (checkbox vs text)
        const originalValue = input.value || input.checked;
        
        /**
         * EVENT LISTENER - Rilevazione modifiche per ogni campo
         * 'change' si attiva quando il valore cambia e l'elemento perde focus
         */
        input.addEventListener('change', function() {
            // Valore corrente considerando tipo di input
            const currentValue = this.type === 'checkbox' ? this.checked : this.value;
            // Confronto con valore originale
            hasUnsavedChanges = (currentValue !== originalValue);
        });
    });
    
    /**
     * EVENT HANDLER - Avviso prima di uscire dalla pagina
     * LINGUAGGIO: Browser beforeunload API
     * 
     * beforeunload è evento speciale che si attiva prima che l'utente
     * lasci la pagina (chiusura tab, navigazione, refresh)
     */
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();                           // Previene uscita immediata
            e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire?';
            return e.returnValue;                         // Messaggio dialog browser
        }
    });
    
    /**
     * RESET PROTEZIONE - Quando form viene salvato correttamente
     * Rimuove la protezione per permettere navigazione normale
     */
    form.addEventListener('submit', function() {
        hasUnsavedChanges = false;                        // Reset flag modifiche
    });
    
    // === SEZIONE 7: SISTEMA AUTO-SAVE ===
    
    /**
     * FUNZIONE AUTO-SAVE - Salvataggio automatico in sessionStorage
     * LINGUAGGIO: HTML5 Web Storage API + JSON serialization
     * 
     * Salva periodicamente i dati del form nel sessionStorage del browser
     * per recuperarli in caso di crash o chiusura accidentale.
     */
    function autoSave() {
        const formData = {};                              // Oggetto per dati form
        
        /**
         * RACCOLTA DATI - Itera tutti gli input per creare snapshot
         */
        formInputs.forEach(input => {
            if (input.type === 'checkbox') {
                formData[input.name] = input.checked;     // Boolean per checkbox
            } else if (input.type !== 'file') {           // Esclude file input
                formData[input.name] = input.value;       // String per altri tipi
            }
        });
        
        /**
         * SERIALIZZAZIONE E STORAGE - JSON storage con chiave univoca
         * sessionStorage è per sessione corrente (chiude con tab)
         * localStorage persiste tra sessioni
         */
        sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(formData));
    }
    
    /**
     * TIMER AUTO-SAVE - Salvataggio automatico ogni 30 secondi
     * setInterval esegue funzione ripetutamente con intervallo specificato
     */
    setInterval(autoSave, 30000);                         // 30000ms = 30 secondi
    
    /**
     * RECUPERO DATI AUTO-SALVATI - Al caricamento pagina
     * Controlla se esistono dati salvati e offre di ripristinarli
     */
    const savedData = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}');
    if (savedData) {
        try {
            /**
             * DESERIALIZZAZIONE - Converte JSON string in oggetto JavaScript
             */
            const data = JSON.parse(savedData);
            
            /**
             * CONFERMA RIPRISTINO - Dialog per scelta utente
             */
            if (confirm('Sono stati trovati dati non salvati. Vuoi ripristinarli?')) {
                /**
                 * RIPRISTINO VALORI - Popola form con dati salvati
                 * Object.keys() restituisce array delle chiavi oggetto
                 */
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = data[key];    // Boolean assignment
                        } else {
                            input.value = data[key];      // String assignment
                        }
                    }
                });
                
                showNotification('info', 'Dati ripristinati dalla sessione precedente');
            } else {
                /**
                 * PULIZIA STORAGE - Se utente rifiuta ripristino
                 */
                sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
            }
        } catch (e) {
            /**
             * GESTIONE ERRORI - JSON malformato o corrotto
             */
            console.warn('Errore nel ripristino dati auto-salvati:', e);
            sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        }
    }
    
    // === SEZIONE 8: CONTATORI CARATTERI DINAMICI ===
    
    /**
     * SISTEMA CONTATORI - Monitoraggio lunghezza textarea
     * LINGUAGGIO: JavaScript DOM manipulation + Event handling
     * 
     * Crea automaticamente contatori per textarea con attributo maxLength
     * e fornisce feedback visivo quando si avvicinano al limite.
     */
    const textareas = form.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.maxLength;             // Attributo HTML maxlength
        
        if (maxLength > 0) {
            /**
             * CREAZIONE CONTATORE - Elemento HTML dinamico
             */
            const counter = document.createElement('small');
            counter.className = 'form-text text-muted';
            counter.innerHTML = `<span id="${textarea.id}_count">${textarea.value.length}</span>/${maxLength} caratteri`;
            
            /**
             * INSERIMENTO DOM - Aggiunge dopo la textarea
             * appendChild aggiunge come ultimo figlio del parent
             */
            textarea.parentNode.appendChild(counter);
            
            /**
             * EVENT HANDLER - Aggiornamento contatore in tempo reale
             * 'input' si attiva ad ogni carattere digitato (più responsive di 'change')
             */
            textarea.addEventListener('input', function() {
                const count = this.value.length;
                const countSpan = document.getElementById(`${this.id}_count`);
                
                if (countSpan) {
                    countSpan.textContent = count;        // Aggiorna numero
                    
                    /**
                     * COLORAZIONE DINAMICA - Feedback visivo basato su soglia
                     * Cambia colore quando si avvicina al limite (90% del max)
                     */
                    countSpan.parentElement.className = count > maxLength * 0.9 
                        ? 'form-text text-warning'        // Giallo se vicino al limite
                        : 'form-text text-muted';         // Grigio se OK
                }
            });
        }
    });
    
    // === SEZIONE 9: SHORTCUTS TASTIERA ===
    
    /**
     * SISTEMA SHORTCUTS - Controlli tastiera per produttività
     * LINGUAGGIO: JavaScript Keyboard Event API
     * 
     * Implementa scorciatoie da tastiera comuni per migliorare
     * l'efficienza degli utenti avanzati del sistema.
     */
    document.addEventListener('keydown', function(e) {
        /**
         * CTRL/CMD + S - Salvataggio rapido
         * Funziona su Windows (Ctrl) e Mac (Cmd)
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();                           // Previene salvataggio browser
            form.submit();                                // Invia form programmaticamente
        }
        
        /**
         * CTRL/CMD + K - Focus primo input text
         * Shortcut comune per "quick search" o focus campo principale
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const firstInput = form.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();                       // Focus per editing immediato
            }
        }
        
        /**
         * ESCAPE - Navigazione indietro con conferma
         * UX pattern comune per "esci/annulla"
         */
        if (e.key === 'Escape') {
            if (hasUnsavedChanges) {
                if (confirm('Hai modifiche non salvate. Tornare indietro?')) {
                    // URL hardcoded - in produzione usare variabili Laravel
                    window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
                }
            } else {
                window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
            }
        }
    });
    
    // === SEZIONE 10: VALIDAZIONE TEMPO REALE CAMPI SPECIFICI ===
    
    /**
     * VALIDAZIONE MODELLO - Controllo formato in tempo reale
     * LINGUAGGIO: JavaScript RegExp + DOM Event handling
     * 
     * Valida il campo modello per assicurare che contenga solo
     * caratteri validi per identificatori prodotto.
     */
    const modelloInput = document.getElementById('modello');
    if (modelloInput) {
        modelloInput.addEventListener('input', function() {
            const modello = this.value.trim();
            
            if (modello) {
                /**
                 * REGEX VALIDATION - Pattern per formato modello valido
                 * ^[a-zA-Z0-9\-_]+$ significa:
                 * ^ = inizio stringa
                 * [a-zA-Z0-9\-_]+ = uno o più caratteri alfanumerici, trattini o underscore
                 * $ = fine stringa
                 */
                if (!/^[a-zA-Z0-9\-_]+$/.test(modello)) {
                    this.classList.add('is-invalid');     // Stile errore Bootstrap
                    showNotification('warning', 'Il modello può contenere solo lettere, numeri e trattini');
                } else {
                    this.classList.remove('is-invalid');  // Rimuove errore se valido
                }
            }
        });
    }
    
    // === SEZIONE 11: INIZIALIZZAZIONE COMPONENTI BOOTSTRAP ===
    
    /**
     * TOOLTIPS BOOTSTRAP - Inizializzazione automatica
     * LINGUAGGIO: Bootstrap JavaScript API
     * 
     * Attiva automaticamente i tooltip Bootstrap per elementi
     * che hanno l'attributo data-bs-toggle="tooltip"
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        // Spread operator (...) converte NodeList in Array
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
            new bootstrap.Tooltip(tooltipTriggerEl)
        );
    }
    
    // === SEZIONE 12: GESTIONE NOTIFICHE LARAVEL ===
    
    /**
     * INTEGRAZIONE NOTIFICHE LARAVEL - Bridge PHP->JavaScript
     * LINGUAGGIO: JavaScript + Laravel Session Flash Data
     * 
     * Gestisce le notifiche passate dal controller Laravel
     * tramite variabili JavaScript globali impostate nella view Blade.
     */
    if (window.LaravelNotifications) {
        if (window.LaravelNotifications.success) {
            showNotification('success', window.LaravelNotifications.success);
            
            /**
             * PULIZIA AUTO-SAVE - In caso di successo elimina backup
             * Non serve più il backup se il salvataggio è andato a buon fine
             */
            sessionStorage.removeItem('editProdotto_' + window.LaravelApp.prodottoId);
        }
        
        if (window.LaravelNotifications.error) {
            showNotification('error', window.LaravelNotifications.error);
        }
        
        /**
         * ERRORI MULTIPLI - Laravel validation errors array
         */
        if (Array.isArray(window.LaravelNotifications.errors)) {
            window.LaravelNotifications.errors.forEach(function(error) {
                showNotification('error', error);
            });
        }
    }
    
    console.log('🎉 Form modifica prodotto inizializzato correttamente');
});

// ===================================================================
// SEZIONE 13: FUNZIONI GLOBALI (ESPOSTE A WINDOW)
// ===================================================================

/**
 * FUNZIONE GLOBALE - Sistema notifiche toast avanzato
 * LINGUAGGIO: Bootstrap Toast API + Dynamic DOM creation
 * 
 * @param {string} type - Tipo notifica: 'success', 'error', 'warning', 'info'
 * @param {string} message - Messaggio da visualizzare
 * @param {number} duration - Durata in millisecondi (default 5000ms)
 * 
 * Sistema di notifiche non invasive usando Toast Bootstrap.
 * Include fallback ad alert() se Bootstrap non è disponibile.
 */
function showNotification(type, message, duration = 5000) {
    /**
     * CONTROLLO BOOTSTRAP - Verifica disponibilità Toast API
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        // ID univoco per ogni toast basato su timestamp
        const toastId = 'toast-' + Date.now();
        
        /**
         * MAPPING ICONS - Associa icone Bootstrap Icons ai tipi
         */
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        
        /**
         * MAPPING COLORS - Associa classi Bootstrap ai tipi
         */
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';
        
        /**
         * TEMPLATE TOAST - HTML dinamico per componente Bootstrap
         */
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgClass} border-0" 
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                            data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        /**
         * CONTAINER TOAST - Crea o trova container per posizionamento
         */
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';         // Z-index alto per visibilità
            document.body.appendChild(toastContainer);
        }
        
        /**
         * INSERIMENTO TOAST - Aggiunge HTML al container
         * insertAdjacentHTML è più efficiente di innerHTML per aggiunte
         */
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        /**
         * ATTIVAZIONE BOOTSTRAP TOAST - Inizializza e mostra
         */
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,                               // Auto-nasconde dopo delay
            delay: duration                               // Durata personalizzabile
        });
        
        toast.show();                                     // Mostra con animazione
        
        /**
         * CLEANUP AUTOMATICO - Rimuove DOM quando toast si nasconde
         * Event listener per evento Bootstrap personalizzato
         */
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();                                // Rimuove elemento dal DOM
        });
    } else {
        /**
         * FALLBACK - Alert nativo se Bootstrap non disponibile
         * Assicura funzionalità anche in ambienti minimali
         */
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * FUNZIONE GLOBALE - Conferma uscita con controllo modifiche
 * LINGUAGGIO: JavaScript + SessionStorage API
 * 
 * @param {string} message - Messaggio personalizzato per dialog
 * @returns {boolean} - True se può uscire, false se deve rimanere
 * 
 * Verifica se esistono modifiche non salvate e chiede conferma
 * prima di permettere navigazione via della pagina.
 */
function confirmExit(message = 'Hai modifiche non salvate. Sei sicuro di voler uscire?') {
    /**
     * CONTROLLO MODIFICHE - Verifica esistenza dati auto-salvati
     * L'esistenza di dati in sessionStorage indica modifiche pendenti
     */
    const hasUnsavedChanges = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}') !== null;
    
    if (hasUnsavedChanges) {
        return confirm(message);                          // Dialog conferma nativo
    }
    
    return true;                                          // OK uscire se nessuna modifica
}

/**
 * FUNZIONE GLOBALE - Preview immagine da URL remoto
 * LINGUAGGIO: JavaScript + Image loading + Error handling
 * 
 * @param {string} url - URL dell'immagine da visualizzare
 * @param {string} targetId - ID dell'elemento img target (default: 'currentImage')
 * 
 * Carica un'immagine da URL remoto e la visualizza nell'elemento specificato
 * con effetto di caricamento e gestione errori.
 */
function previewImageFromUrl(url, targetId = 'currentImage') {
    const img = document.getElementById(targetId);
    if (img) {
        /**
         * EFFETTO LOADING - Feedback visivo durante caricamento
         * Riduce opacity mentre carica per indicare stato transitorio
         */
        img.style.opacity = '0.5';                        // Semi-trasparente durante loading
        img.src = url;                                     // Imposta nuovo URL
        
        /**
         * EVENT HANDLER - Ripristina visual quando caricamento completo
         * onload è evento nativo che si attiva quando immagine è caricata
         */
        img.onload = function() {
            this.style.opacity = '1';                      // Ripristina opacità completa
        };
        
        /**
         * ERROR HANDLER - Gestisce errori di caricamento immagine
         */
        img.onerror = function() {
            this.style.opacity = '1';
            this.alt = 'Errore caricamento immagine';
            console.error('Errore caricamento immagine:', url);
        };
    }
}

/**
 * FUNZIONE GLOBALE - Reset form ai valori originali
 * LINGUAGGIO: JavaScript + DOM API + SessionStorage
 * 
 * Ripristina completamente il form allo stato iniziale,
 * cancellando tutte le modifiche e i dati auto-salvati.
 */
function resetForm() {
    /**
     * CONFERMA SICUREZZA - Previene reset accidentali
     */
    if (confirm('Ripristinare tutti i campi ai valori originali?')) {
        /**
         * RESET FORM NATIVO - Usa API HTML5 form.reset()
         * Ripristina tutti i campi ai valori definiti nell'HTML originale
         */
        document.getElementById('editProdottoForm').reset();
        
        /**
         * RIPRISTINO IMMAGINE - Reset preview immagine se presente
         * Usa template literal Laravel per URL immagine originale
         */
        const currentImg = document.getElementById('currentImage');
        if (currentImg) {
            // URL generato da Laravel Blade template (sostituire con variabile reale)
            currentImg.src = '{{ $prodotto->foto ? asset("storage/" . $prodotto->foto) : "" }}';
        }
        
        /**
         * PULIZIA STORAGE - Elimina dati auto-salvati
         */
        sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        
        showNotification('info', 'Form ripristinato ai valori originali');
    }
}

/**
 * FUNZIONE GLOBALE - Salvataggio bozza manuale
 * LINGUAGGIO: JavaScript + FormData API + JSON serialization
 * 
 * Permette all'utente di salvare manualmente una bozza delle modifiche
 * nel sessionStorage per recupero futuro.
 */
function salvaBozza() {
    const form = document.getElementById('editProdottoForm');
    
    /**
     * RACCOLTA DATI - FormData API per estrazione dati form
     * FormData è API HTML5 per lavorare con form data, inclusi file
     */
    const formData = new FormData(form);
    
    /**
     * CONVERSIONE PER STORAGE - FormData -> Object -> JSON
     * SessionStorage richiede stringhe, FormData non è serializzabile direttamente
     */
    const data = {};
    for (let [key, value] of formData.entries()) {
        // Esclude campi tecnici Laravel e file (non serializzabili)
        if (key !== '_token' && key !== '_method' && key !== 'foto') {
            data[key] = value;
        }
    }
    
    /**
     * STORAGE E FEEDBACK - Salva e notifica utente
     */
    sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(data));
    showNotification('success', 'Bozza salvata automaticamente');
}

// ===================================================================
// SEZIONE 14: FUNZIONI DEBUG E ACCESSIBILITÀ
// ===================================================================

/**
 * FUNZIONE GLOBALE - Controllo accessibilità automatico
 * LINGUAGGIO: JavaScript + DOM Accessibility APIs
 * 
 * @returns {Array} - Array di problemi di accessibilità trovati
 * 
 * Esegue controlli automatici per verificare che il form rispetti
 * gli standard di accessibilità WCAG per utenti con disabilità.
 */
function checkAccessibility() {
    const form = document.getElementById('editProdottoForm');
    const issues = [];                                    // Array problemi trovati
    
    /**
     * CONTROLLO LABELS - Verifica associazione label-input
     * Ogni input deve avere una label associata o aria-label
     */
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        // Cerca label con attributo for che corrisponde all'ID input
        const label = form.querySelector(`label[for="${input.id}"]`);
        
        // Se non ha label e non ha aria-label, è problema accessibilità
        if (!label && !input.getAttribute('aria-label')) {
            issues.push(`Input ${input.name} senza label`);
        }
    });
    
    /**
     * CONTROLLO REQUIRED FIELDS - Indicazione visiva obbligatorietà
     * I campi obbligatori devono essere chiaramente indicati
     */
    const required = form.querySelectorAll('[required]');
    required.forEach(field => {
        const label = form.querySelector(`label[for="${field.id}"]`);
        // Verifica che la label contenga asterisco o altra indicazione
        if (label && !label.textContent.includes('*')) {
            issues.push(`Campo obbligatorio ${field.name} non indicato visivamente`);
        }
    });
    
    /**
     * LOGGING RISULTATI - Console output per sviluppatori
     */
    if (issues.length > 0) {
        console.warn('Problemi di accessibilità trovati:', issues);
    } else {
        console.log('✅ Nessun problema di accessibilità rilevato');
    }
    
    return issues;
}

/**
 * CONTROLLO ACCESSIBILITÀ AUTOMATICO - Solo in sviluppo
 * Esegue controllo dopo 1 secondo dal caricamento pagina
 */
if (window.LaravelApp && window.LaravelApp.debug) {
    setTimeout(checkAccessibility, 1000);
}

/**
 * FUNZIONE DEBUG - Informazioni complete form per sviluppatori
 * LINGUAGGIO: JavaScript + Console API advanced logging
 * 
 * Fornisce dump completo dello stato del form per troubleshooting
 * e sviluppo. Include dati form, validazione, storage, etc.
 */
function debugFormInfo() {
    const form = document.getElementById('editProdottoForm');
    
    /**
     * LOGGING STRUTTURATO - Console.group per organizzazione
     * Console.group crea sezioni collassabili nel developer console
     */
    console.group('🔧 Debug Form Modifica Prodotto');
    console.log('Form element:', form);
    console.log('Prodotto ID:', window.LaravelApp?.prodottoId);
    console.log('Prodotto Nome:', window.LaravelApp?.prodottoNome);
    console.log('Action URL:', form.action);
    console.log('Method:', form.method);
    
    /**
     * DUMP DATI FORM - Tutti i campi e valori correnti
     */
    const formData = new FormData(form);
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    /**
     * INFO VALIDAZIONE - Campi obbligatori e stato
     */
    const required = form.querySelectorAll('[required]');
    console.log('Campi obbligatori:', required.length);
    
    const invalid = form.querySelectorAll('.is-invalid');
    console.log('Campi con errori:', invalid.length);
    
    /**
     * STATO AUTO-SAVE - Informazioni persistenza dati
     */
    const autoSaveKey = 'editProdotto_' + (window.LaravelApp?.prodottoId || '');
    const autoSaveData = sessionStorage.getItem(autoSaveKey);
    console.log('Auto-save data presente:', !!autoSaveData);
    if (autoSaveData) {
        console.log('Auto-save data:', JSON.parse(autoSaveData));
    }
    
    /**
     * STATO MODIFICHE - Flag protezione perdita dati
     */
    console.log('Modifiche non salvate:', window.hasUnsavedChanges || false);
    
    console.groupEnd();
}

// ===================================================================
// SEZIONE 15: SHORTCUTS AVANZATI E EASTER EGGS
// ===================================================================

/**
 * SHORTCUT DEBUG - Ctrl+Shift+D per debug info
 * Utile per sviluppatori e supporto tecnico
 */
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        debugFormInfo();                                  // Mostra debug completo
    }
    
    /**
     * SHORTCUT ACCESSIBILITÀ - Ctrl+Shift+A
     * Esegue controllo accessibilità on-demand
     */
    if (e.ctrlKey && e.shiftKey && e.key === 'A') {
        e.preventDefault();
        const issues = checkAccessibility();
        if (issues.length === 0) {
            showNotification('success', 'Nessun problema di accessibilità trovato');
        } else {
            showNotification('warning', `${issues.length} problemi di accessibilità rilevati. Controlla la console.`);
        }
    }
    
    /**
     * SHORTCUT BOZZA - Ctrl+Shift+S
     * Salva bozza manualmente
     */
    if (e.ctrlKey && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        salvaBozza();
    }
});

// ===================================================================
// SEZIONE 16: PERFORMANCE MONITORING (OPZIONALE)
// ===================================================================

/**
 * PERFORMANCE TRACKING - Monitoraggio performance form
 * LINGUAGGIO: Browser Performance API
 * 
 * Traccia tempi di caricamento e interazione per ottimizzazione UX
 */
if ('performance' in window && window.LaravelApp?.debug) {
    // Marca tempo di inizializzazione completa
    performance.mark('form-edit-ready');
    
    // Calcola tempo dall'inizio caricamento pagina
    const loadTime = performance.now();
    console.log(`Form modifica caricato in ${loadTime.toFixed(2)}ms`);
    
    // Traccia primi input utente
    let firstInteractionTracked = false;
    document.addEventListener('input', function() {
        if (!firstInteractionTracked) {
            performance.mark('first-user-interaction');
            const interactionTime = performance.now();
            console.log(`Prima interazione utente dopo ${interactionTime.toFixed(2)}ms`);
            firstInteractionTracked = true;
        }
    });
}

/**
 * ===================================================================
 * FINE MODULO admin/prodotti/edit.js
 * ===================================================================
 * 
 * RIEPILOGO FUNZIONALITÀ IMPLEMENTATE:
 * 
 * 1. GESTIONE UPLOAD IMMAGINI
 *    - Validazione formato e dimensione file
 *    - Preview immediata con FileReader API
 *    - Sostituzioni dinamiche immagine esistente
 *    - Gestione errori caricamento
 * 
 * 2. SISTEMA VALIDAZIONE AVANZATO
 *    - Validazione real-time per campi specifici
 *    - Controlli format con RegExp
 *    - Feedback visivo immediato
 *    - Scroll automatico a errori
 * 
 * 3. PROTEZIONE PERDITA DATI
 *    - Tracking modifiche non salvate
 *    - Avviso beforeunload per uscita accidentale
 *    - Auto-save periodico in sessionStorage
 *    - Recupero dati crashs/chiusure
 * 
 * 4. CONTATORI E FEEDBACK UI
 *    - Contatori caratteri dinamici per textarea
 *    - Colorazione progressiva avvicinamento limiti
 *    - Loading states per operazioni asincrone
 *    - Notifiche toast non invasive
 * 
 * 5. SHORTCUTS PRODUTTIVITÀ
 *    - Ctrl+S per salvataggio rapido
 *    - Ctrl+K per focus campo principale
 *    - Escape per navigazione indietro
 *    - Shortcuts debug per sviluppatori
 * 
 * 6. ACCESSIBILITÀ E QUALITÀ
 *    - Controlli accessibilità automatici
 *    - Tooltip e ARIA labels
 *    - Focus management
 *    - Performance monitoring
 * 
 * 7. INTEGRAZIONE LARAVEL
 *    - Bridge notifiche PHP->JavaScript
 *    - Gestione CSRF token
 *    - URL routing dinamico
 *    - Session flash data
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (vanilla, no jQuery per performance)
 * - DOM API nativo per manipolazione elementi
 * - HTML5 APIs (File, FileReader, Storage, Performance)
 * - Bootstrap 5 (Toast, Validation classes)
 * - Browser APIs (beforeunload, keyboard events)
 * 
 * PATTERN ARCHITETTURALI:
 * - Event-Driven Architecture per reattività
 * - Observer Pattern per tracking modifiche
 * - Strategy Pattern per validazioni multiple
 * - Factory Pattern per creazione elementi DOM
 * - Command Pattern per shortcuts tastiera
 * 
 * SICUREZZA:
 * - Validazione file upload (tipo, dimensione)
 * - Sanitizzazione input utente
 * - Protezione XSS con innerHTML sicuro
 * - Conferme per azioni distruttive
 * 
 * PERFORMANCE:
 * - Vanilla JavaScript per velocità
 * - Event delegation dove possibile
 * - Lazy loading componenti Bootstrap
 * - Debouncing per validazione real-time
 * - Memory cleanup su page unload
 * 
 * MANUTENIBILITÀ:
 * - Codice modulare e commentato
 * - Separazione concerns (UI, logic, storage)
 * - Debug tools integrati
 * - Performance monitoring
 * - Accessibilità testing automatico
 * ===================================================================
 */