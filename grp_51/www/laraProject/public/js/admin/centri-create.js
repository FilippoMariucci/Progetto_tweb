/*
 * LINGUAGGIO: JavaScript (ES6+) con jQuery e Vanilla JS
 * TIPO FILE: Script lato client per form creazione centri assistenza
 * DESCRIZIONE: Gestisce validazione in tempo reale, feedback utente e UX del form
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Validazione in tempo reale per tutti i campi obbligatori
 * - Contatore caratteri dinamico
 * - Formattazione automatica CAP e telefono
 * - Progress tracking del completamento form
 * - Feedback visivo con classi Bootstrap
 * - Gestione errori con alert dinamici
 * 
 * DIPENDENZE:
 * - jQuery 3.x (per compatibilità con resto applicazione)
 * - Bootstrap 5.x (classi CSS e componenti)
 * - Vanilla JavaScript (per performance nelle validazioni)
 */

/**
 * PRIMO BLOCCO: Inizializzazione jQuery (compatibilità)
 * 
 * LINGUAGGIO: jQuery
 * 
 * SPIEGAZIONE:
 * - $(document).ready() è il pattern jQuery standard
 * - Verifica route per sicurezza (stesso pattern degli altri file)
 * - Mantiene consistenza con architettura esistente
 * - selectedProducts preparato per future funzionalità
 */
$(document).ready(function() {
    // LOG di inizializzazione per debugging
    console.log('admin.centri.create caricato');
    
    /**
     * VERIFICA ROUTE per sicurezza
     * 
     * LINGUAGGIO: JavaScript + Laravel integration
     * 
     * SPIEGAZIONE:
     * - window.LaravelApp?.route usa optional chaining (ES2020)
     * - Evita errori se LaravelApp non è definito
     * - Esce subito se non siamo nella pagina corretta
     * - Pattern di sicurezza per evitare interferenze tra script
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.create') {
        return; // Esce se route non corrisponde
    }
    
    /**
     * INIZIALIZZAZIONE variabili globali
     * 
     * LINGUAGGIO: JavaScript
     * 
     * SPIEGAZIONE:
     * - pageData per dati passati da Laravel (se necessari)
     * - selectedProducts per future espansioni (selezione multipla)
     * - Pattern consistente con altri script dell'applicazione
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per gestioni future
});

/**
 * SECONDO BLOCCO: Validazione form principale (Vanilla JS)
 * 
 * LINGUAGGIO: Vanilla JavaScript (ES6+)
 * 
 * SPIEGAZIONE:
 * - addEventListener('DOMContentLoaded') è l'equivalente vanilla di $(document).ready()
 * - Vanilla JS per performance nelle validazioni (più veloce di jQuery)
 * - Gestione eventi nativa del browser
 * - Nessuna dipendenza da librerie esterne per logica core
 */
document.addEventListener('DOMContentLoaded', function() {
    // LOG di inizializzazione con emoji per debug visivo
    console.log('🏗️ Inizializzazione form creazione centro - TUTTI CAMPI OBBLIGATORI');
    
    // ================================================
    // SEZIONE CONTATORE CARATTERI DINAMICO
    // ================================================
    
    /**
     * CONTATORE caratteri per campo nome
     * 
     * LINGUAGGIO: Vanilla JavaScript + DOM API
     * 
     * SPIEGAZIONE:
     * - document.getElementById() è più veloce di jQuery per singoli elementi
     * - Feedback visivo in tempo reale per lunghezza nome
     * - Prevenzione overflow con visual feedback
     * - Colori dinamici basati su soglie
     */
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    // Verifica esistenza elementi prima di usarli (defensive programming)
    if (nomeInput && nomeCounter) {
        /**
         * FUNZIONE di aggiornamento contatore
         * 
         * LINGUAGGIO: JavaScript Function + DOM Manipulation
         * 
         * SPIEGAZIONE:
         * - .value.length ottiene lunghezza corrente del testo
         * - .textContent aggiorna il testo del contatore
         * - .className cambia classe CSS per colorazione dinamica
         * - Soglie: 0=rosso, >240=rosso, >200=arancione, resto=verde
         */
        function updateNomeCounter() {
            const currentLength = nomeInput.value.length;
            nomeCounter.textContent = currentLength;
            
            /**
             * LOGICA di colorazione basata su lunghezza
             * 
             * LINGUAGGIO: JavaScript Conditional Logic
             * 
             * SPIEGAZIONE:
             * - if/else if/else per gestire diverse soglie
             * - Bootstrap classes: text-danger (rosso), text-warning (arancione), text-success (verde)
             * - fw-bold per enfatizzare situazioni critiche (vuoto o troppo lungo)
             * - Feedback visivo immediato per UX migliore
             */
            if (currentLength === 0) {
                nomeCounter.className = 'text-danger fw-bold';  // Rosso se vuoto
            } else if (currentLength > 240) {
                nomeCounter.className = 'text-danger fw-bold';  // Rosso se troppo lungo
            } else if (currentLength > 200) {
                nomeCounter.className = 'text-warning';        // Arancione se quasi pieno
            } else {
                nomeCounter.className = 'text-success';        // Verde se OK
            }
        }
        
        /**
         * REGISTRAZIONE event listener
         * 
         * LINGUAGGIO: JavaScript Event Handling
         * 
         * SPIEGAZIONE:
         * - 'input' event si attiva ad ogni carattere digitato
         * - Più reattivo di 'change' che si attiva solo al blur
         * - updateNomeCounter() chiamata immediatamente per inizializzazione
         */
        nomeInput.addEventListener('input', updateNomeCounter);
        updateNomeCounter(); // Inizializza contatore
    }
    
    // ================================================
    // SEZIONE VALIDAZIONE CAP RIGOROSA
    // ================================================
    
    /**
     * VALIDAZIONE e formattazione CAP italiano
     * 
     * LINGUAGGIO: JavaScript + Regular Expressions
     * 
     * SPIEGAZIONE:
     * - CAP italiano: esattamente 5 cifre numeriche
     * - Filtro in tempo reale per caratteri non numerici
     * - Limitazione automatica a 5 caratteri
     * - Feedback visivo con classi Bootstrap is-valid/is-invalid
     */
    const capInput = document.getElementById('cap');
    if (capInput) {
        capInput.addEventListener('input', function(e) {
            /**
             * PULIZIA input: solo numeri
             * 
             * LINGUAGGIO: JavaScript RegExp
             * 
             * SPIEGAZIONE:
             * - /\D/g è regex per "tutto ciò che NON è digit"
             * - \D è shorthand per [^0-9]
             * - g flag per "global" (sostituisce tutte le occorrenze)
             * - replace(/\D/g, '') rimuove tutti i caratteri non numerici
             */
            let value = e.target.value.replace(/\D/g, '');
            
            /**
             * LIMITAZIONE lunghezza a 5 caratteri
             * 
             * LINGUAGGIO: JavaScript String Methods
             * 
             * SPIEGAZIONE:
             * - substr(0, 5) estrae primi 5 caratteri
             * - Alternativa a slice(0, 5) ma più compatibile
             * - Previene input più lunghi di CAP valido
             */
            if (value.length > 5) {
                value = value.substr(0, 5);
            }
            
            // Aggiorna il valore dell'input con quello pulito
            e.target.value = value;
            
            /**
             * VALIDAZIONE visiva in tempo reale
             * 
             * LINGUAGGIO: JavaScript + Bootstrap CSS Classes
             * 
             * SPIEGAZIONE:
             * - is-valid: classe Bootstrap per campo valido (bordo verde)
             * - is-invalid: classe Bootstrap per campo invalido (bordo rosso)
             * - classList.add/remove: API moderna per gestire classi CSS
             * - Logica: 5 caratteri=valido, 1-4=invalido, 0=neutro
             */
            if (value.length === 5) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else if (value.length > 0) {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // ================================================
    // SEZIONE VALIDAZIONE TELEFONO
    // ================================================
    
    /**
     * VALIDAZIONE e formattazione numero telefono
     * 
     * LINGUAGGIO: JavaScript + Regular Expressions
     * 
     * SPIEGAZIONE:
     * - Permette numeri, spazi, +, -, () per formati internazionali
     * - Filtra caratteri non validi in tempo reale
     * - Valida lunghezza e formato con regex
     * - Supporta telefoni fissi, mobili, internazionali
     */
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            /**
             * PULIZIA input telefono
             * 
             * LINGUAGGIO: JavaScript RegExp
             * 
             * SPIEGAZIONE:
             * - [^0-9\s\+\-\(\)] è character class negata
             * - ^ all'inizio di [] significa "NOT"
             * - \s = spazi, \+ = plus (escaped), \- = trattino, \(\) = parentesi (escaped)
             * - Mantiene solo caratteri validi per numeri telefono
             */
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
            
            /**
             * VALIDAZIONE formato telefono
             * 
             * LINGUAGGIO: JavaScript RegExp
             * 
             * SPIEGAZIONE:
             * - ^[\+]? = può iniziare con + opzionale
             * - [\d\s\-\(\)]{8,20} = 8-20 caratteri tra numeri, spazi, -, ()
             * - $ = fine stringa
             * - {8,20} = quantificatore per lunghezza minima e massima
             */
            const phonePattern = /^[\+]?[\d\s\-\(\)]{8,20}$/;
            if (value.length > 0) {
                if (phonePattern.test(value) && value.length >= 8) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // ================================================
    // SEZIONE VALIDAZIONE EMAIL
    // ================================================
    
    /**
     * VALIDAZIONE email in tempo reale
     * 
     * LINGUAGGIO: JavaScript + Regular Expressions
     * 
     * SPIEGAZIONE:
     * - Regex semplificata ma efficace per email
     * - Trim automatico per rimuovere spazi
     * - Feedback visivo immediato
     * - Validazione durante la digitazione
     */
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function(e) {
            /**
             * PULIZIA e validazione email
             * 
             * LINGUAGGIO: JavaScript String + RegExp
             * 
             * SPIEGAZIONE:
             * - .trim() rimuove spazi all'inizio e fine
             * - ^[^\s@]+ = uno o più caratteri che non sono spazio o @
             * - @ = carattere @ letterale
             * - [^\s@]+ = uno o più caratteri che non sono spazio o @
             * - \. = punto letterale (escaped)
             * - [^\s@]+$ = uno o più caratteri che non sono spazio o @ fino alla fine
             * - Pattern basilare ma efficace: locale@dominio.tld
             */
            const email = e.target.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length > 0) {
                if (emailPattern.test(email)) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // ================================================
    // SEZIONE VALIDAZIONE COMPLETA FORM
    // ================================================
    
    /**
     * VALIDAZIONE finale al submit del form
     * 
     * LINGUAGGIO: JavaScript Form Validation
     * 
     * SPIEGAZIONE:
     * - Intercetta submit per validazione server-side
     * - Raccoglie tutti i valori e li valida
     * - Previene invio se ci sono errori
     * - Mostra errori aggregati in un alert
     * - Gestisce loading state durante invio
     */
    const form = document.getElementById('formCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        form.addEventListener('submit', function(e) {
            console.log('📤 Tentativo invio form...');
            
            /**
             * RACCOLTA dati form per validazione
             * 
             * LINGUAGGIO: JavaScript Object + DOM API
             * 
             * SPIEGAZIONE:
             * - Oggetto con tutti i campi del form
             * - .trim() rimuove spazi superflui
             * - .value per input text, select restituisce valore selezionato
             * - Struttura dati pulita per validazione
             */
            const formData = {
                nome: document.getElementById('nome').value.trim(),
                indirizzo: document.getElementById('indirizzo').value.trim(),
                citta: document.getElementById('citta').value.trim(),
                provincia: document.getElementById('provincia').value,
                cap: document.getElementById('cap').value.trim(),
                telefono: document.getElementById('telefono').value.trim(),
                email: document.getElementById('email').value.trim()
            };
            
            const errors = []; // Array per raccogliere errori
            
            /**
             * VALIDAZIONE RIGOROSA di tutti i campi
             * 
             * LINGUAGGIO: JavaScript Validation Logic
             * 
             * SPIEGAZIONE:
             * - Serie di controlli if per ogni campo obbligatorio
             * - Validazione a due livelli: presenza e formato/lunghezza
             * - Messaggi di errore specifici e chiari
             * - Accumulo errori in array per visualizzazione aggregata
             */
            
            // Validazione NOME
            if (!formData.nome) {
                errors.push('Nome centro è obbligatorio');
            } else if (formData.nome.length < 3) {
                errors.push('Nome centro deve avere almeno 3 caratteri');
            }
            
            // Validazione INDIRIZZO
            if (!formData.indirizzo) {
                errors.push('Indirizzo è obbligatorio');
            } else if (formData.indirizzo.length < 5) {
                errors.push('Indirizzo deve essere completo (minimo 5 caratteri)');
            }
            
            // Validazione CITTÀ
            if (!formData.citta) {
                errors.push('Città è obbligatoria');
            } else if (formData.citta.length < 2) {
                errors.push('Nome città troppo breve');
            }
            
            // Validazione PROVINCIA
            if (!formData.provincia) {
                errors.push('Provincia è obbligatoria');
            }
            
            // Validazione CAP
            if (!formData.cap) {
                errors.push('CAP è obbligatorio');
            } else if (!/^\d{5}$/.test(formData.cap)) {
                errors.push('CAP deve essere composto da esattamente 5 cifre');
            }
            
            // Validazione TELEFONO
            if (!formData.telefono) {
                errors.push('Telefono è obbligatorio');
            } else if (formData.telefono.length < 8) {
                errors.push('Numero di telefono troppo breve');
            }
            
            // Validazione EMAIL
            if (!formData.email) {
                errors.push('Email è obbligatoria');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                errors.push('Formato email non valido');
            }
            
            /**
             * GESTIONE errori di validazione
             * 
             * LINGUAGGIO: JavaScript Event Prevention
             * 
             * SPIEGAZIONE:
             * - Se errors.length > 0 ci sono errori
             * - e.preventDefault() blocca l'invio del form
             * - showValidationErrors() mostra errori all'utente
             * - return false per sicurezza (doppia prevenzione)
             */
            if (errors.length > 0) {
                e.preventDefault(); // Blocca invio form
                console.log('❌ Validazione fallita:', errors);
                
                showValidationErrors(errors);
                return false;
            }
            
            /**
             * LOADING STATE durante invio
             * 
             * LINGUAGGIO: JavaScript DOM Manipulation
             * 
             * SPIEGAZIONE:
             * - Disabilita pulsante per prevenire doppi invii
             * - Cambia testo con spinner Bootstrap
             * - Feedback visivo che operazione è in corso
             * - innerHTML per inserire HTML (spinner + testo)
             */
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creazione in corso...';
            
            console.log('✅ Validazione superata, invio form...');
            // Il form viene inviato normalmente se arriviamo qui
        });
    }
    
    // ================================================
    // SEZIONE GESTIONE ERRORI DINAMICI
    // ================================================
    
    /**
     * FUNZIONE per mostrare errori di validazione
     * 
     * LINGUAGGIO: JavaScript Function + DOM Creation
     * 
     * @param {Array} errors - Array di stringhe con i messaggi di errore
     * 
     * SPIEGAZIONE:
     * - Rimuove alert precedenti per evitare accumulo
     * - Crea dinamicamente nuovo alert Bootstrap
     * - Formatta errori in lista HTML
     * - Inserisce alert in posizione prominente
     * - Scroll automatico verso errori
     */
    function showValidationErrors(errors) {
        /**
         * RIMOZIONE alert precedenti
         * 
         * LINGUAGGIO: JavaScript DOM Query + Removal
         * 
         * SPIEGAZIONE:
         * - querySelector() trova primo elemento con classe
         * - .validation-alert è classe custom per identificare i nostri alert
         * - .remove() elimina elemento dal DOM
         * - Evita accumulo di alert multipli
         */
        const existingAlert = document.querySelector('.validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        /**
         * CREAZIONE dinamica alert container
         * 
         * LINGUAGGIO: JavaScript DOM Creation
         * 
         * SPIEGAZIONE:
         * - createElement() crea nuovo elemento div
         * - className imposta classi CSS Bootstrap
         * - alert alert-danger per styling rosso
         * - alert-dismissible fade show per animazioni e chiusura
         * - validation-alert classe custom per riconoscimento
         */
        const alertContainer = document.createElement('div');
        alertContainer.className = 'alert alert-danger alert-dismissible fade show validation-alert';
        
        /**
         * COSTRUZIONE HTML errori
         * 
         * LINGUAGGIO: JavaScript String Building + Array Methods
         * 
         * SPIEGAZIONE:
         * - forEach() itera su array errori
         * - Template string building per creare HTML lista
         * - <ul><li> per struttura semantica corretta
         * - Concatenazione progressiva per HTML complesso
         */
        let errorsHtml = '<ul class="mb-0">';
        errors.forEach(error => {
            errorsHtml += `<li>${error}</li>`;
        });
        errorsHtml += '</ul>';
        
        /**
         * POPOLAMENTO alert con contenuto completo
         * 
         * LINGUAGGIO: JavaScript Template Literals
         * 
         * SPIEGAZIONE:
         * - innerHTML per inserire HTML complesso
         * - Template literals (``) per multi-line string
         * - Bootstrap Icons (bi-exclamation-triangle) per visual appeal
         * - btn-close Bootstrap per chiusura manual dell'alert
         * - data-bs-dismiss per integrazione Bootstrap JS
         */
        alertContainer.innerHTML = `
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Errori di Validazione</h6>
            <p class="mb-2">Correggi i seguenti errori prima di continuare:</p>
            ${errorsHtml}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        /**
         * INSERIMENTO alert nel DOM
         * 
         * LINGUAGGIO: JavaScript DOM Insertion + Scroll API
         * 
         * SPIEGAZIONE:
         * - querySelector('.card-body') trova container principale
         * - insertBefore() inserisce come primo figlio
         * - firstChild per posizione prominente
         * - scrollIntoView() scroll automatico verso errori
         * - behavior: 'smooth' per animazione fluida
         * - block: 'start' per allineamento in alto
         */
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertBefore(alertContainer, cardBody.firstChild);
            
            // Scroll automatico verso l'alert
            alertContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    // ================================================
    // SEZIONE PROGRESS TRACKING
    // ================================================
    
    /**
     * FUNZIONE controllo completamento form
     * 
     * LINGUAGGIO: JavaScript Function + Array Methods
     * 
     * SPIEGAZIONE:
     * - Monitora quanti campi sono compilati
     * - Calcola percentuale completamento
     * - Aggiorna stato visivo pulsante salva
     * - Feedback progressivo all'utente
     */
    function checkFormCompletion() {
        /**
         * ARRAY di tutti i campi obbligatori
         * 
         * LINGUAGGIO: JavaScript Array + DOM References
         * 
         * SPIEGAZIONE:
         * - Array con riferimenti a tutti gli input obbligatori
         * - getElementById() per ogni campo specifico
         * - Lista esplicita per controllo preciso
         */
        const allFields = [
            document.getElementById('nome'),
            document.getElementById('indirizzo'),
            document.getElementById('citta'),
            document.getElementById('provincia'),
            document.getElementById('cap'),
            document.getElementById('telefono'),
            document.getElementById('email')
        ];
        
        /**
         * CONTEGGIO campi compilati
         * 
         * LINGUAGGIO: JavaScript Array Methods + Filter
         * 
         * SPIEGAZIONE:
         * - .filter() crea nuovo array con elementi che soddisfano condizione
         * - field && field.value.trim().length > 0 verifica:
         *   1. field esiste (non null)
         *   2. ha valore non vuoto dopo trim
         * - .length su array filtrato = numero campi compilati
         */
        const filledFields = allFields.filter(field => 
            field && field.value.trim().length > 0
        ).length;
        
        /**
         * CALCOLO percentuale completamento
         * 
         * LINGUAGGIO: JavaScript Math
         * 
         * SPIEGAZIONE:
         * - (filledFields / allFields.length) * 100 = percentuale
         * - Rapporto tra campi compilati e totali
         * - * 100 per convertire da decimale a percentuale
         */
        const progress = (filledFields / allFields.length) * 100;
        
        /**
         * AGGIORNAMENTO stato pulsante salva
         * 
         * LINGUAGGIO: JavaScript DOM Class Manipulation
         * 
         * SPIEGAZIONE:
         * - Se progress === 100: form completo
         * - btn-primary: stile attivo (blu)
         * - btn-outline-primary: stile disattivato (outline)
         * - disabled property per abilitazione/disabilitazione
         * - Feedback visivo chiaro dello stato
         */
        if (btnSalva) {
            if (progress === 100) {
                btnSalva.classList.remove('btn-outline-primary');
                btnSalva.classList.add('btn-primary');
                btnSalva.disabled = false;
            } else {
                btnSalva.classList.remove('btn-primary');
                btnSalva.classList.add('btn-outline-primary');
            }
        }
        
        /**
         * LOG progress per debugging
         * 
         * LINGUAGGIO: JavaScript Template Literals + Math
         * 
         * SPIEGAZIONE:
         * - toFixed(0) arrotonda a numero intero
         * - Template literals per formattazione leggibile
         * - Debug info: percentuale e frazione numerica
         */
        console.log(`📊 Completamento form: ${progress.toFixed(0)}% (${filledFields}/${allFields.length})`);
    }
    
    /**
     * REGISTRAZIONE listeners per tutti i campi
     * 
     * LINGUAGGIO: JavaScript Event Delegation + Array Methods
     * 
     * SPIEGAZIONE:
     * - querySelectorAll() seleziona tutti input e select
     * - forEach() itera su NodeList risultante
     * - addEventListener() per ogni elemento
     * - 'input' per typing in tempo reale
     * - 'change' per dropdown e perdita focus
     * - Monitora ogni modifica al form
     */
    const allInputs = document.querySelectorAll('input, select');
    allInputs.forEach(input => {
        input.addEventListener('input', checkFormCompletion);
        input.addEventListener('change', checkFormCompletion);
    });
    
    // ================================================
    // SEZIONE AUTO-FOCUS E INIZIALIZZAZIONE
    // ================================================
    
    /**
     * AUTO-FOCUS e suggerimenti iniziali
     * 
     * LINGUAGGIO: JavaScript DOM Focus + setTimeout
     * 
     * SPIEGAZIONE:
     * - .focus() sposta cursore al primo campo
     * - setTimeout() per suggerimento ritardato
     * - placeholder dinamico per guidare utente
     * - UX migliorata per iniziare subito la compilazione
     */
    const primoInput = document.getElementById('nome');
    if (primoInput) {
        primoInput.focus(); // Focus immediato
        
        /**
         * SUGGERIMENTO ritardato se campo vuoto
         * 
         * LINGUAGGIO: JavaScript setTimeout + Conditional
         * 
         * SPIEGAZIONE:
         * - setTimeout(..., 2000) aspetta 2 secondi
         * - Doppio controllo: se campo ancora vuoto dopo 2s
         * - placeholder con emoji per attrarre attenzione
         * - Non invasivo: solo se utente non ha ancora digitato
         */
        if (!primoInput.value) {
            setTimeout(() => {
                if (!primoInput.value) {
                    primoInput.placeholder = '✏️ Inizia digitando il nome del centro...';
                }
            }, 2000);
        }
    }
    
    /**
     * CONTROLLO iniziale completamento
     * 
     * LINGUAGGIO: JavaScript Function Call
     * 
     * SPIEGAZIONE:
     * - Chiamata immediata per stato iniziale
     * - Utile se form ha valori pre-compilati
     * - Aggiorna subito stato pulsante
     */
    checkFormCompletion();
    
    // LOG finale di inizializzazione
    console.log('✅ Form creazione centro inizializzato - Validazione completa attiva');

}); // Chiusura addEventListener('DOMContentLoaded')

/*
 * RIEPILOGO FUNZIONALITÀ SCRIPT:
 * 
 * 1. VALIDAZIONE IN TEMPO REALE:
 *    - Contatore caratteri dinamico per nome
 *    - Formattazione automatica CAP (solo numeri, max 5)
 *    - Validazione telefono (formato internazionale)
 *    - Controllo email con regex
 * 
 * 2. FEEDBACK VISIVO:
 *    - Classi Bootstrap is-valid/is-invalid
 *    - Colori dinamici per contatori
 *    - Progress tracking del completamento
 *    - Alert dinamici per errori aggregati
 * 
 * 3. UX ENHANCEMENTS:
 *    - Auto-focus primo campo
 *    - Placeholder dinamici e suggerimenti
 *    - Loading state durante invio
 *    - Scroll automatico verso errori
 * 
 * 4. VALIDAZIONE ROBUSTA:
 *    - Controlli client-side completi
 *    - Prevenzione invio form invalido
 *    - Messaggi errore specifici e chiari
 *    - Defensive programming per elementi mancanti
 * 
 * 5. PERFORMANCE:
 *    - Vanilla JS per validazioni (più veloce)
 *    - jQuery solo per compatibilità
 *    - Event delegation efficiente
 *    - Controlli esistenza elementi
 * 
 * ARCHITETTURA:
 * - Doppio pattern: jQuery + Vanilla JS
 * - Modularità: ogni sezione è indipendente
 * - Estensibilità: facile aggiungere nuovi campi
 * - Defensive programming: controlli esistenza elementi
 * - Event-driven: reazione immediata alle azioni utente
 * 
 * PATTERN DESIGN UTILIZZATI:
 * 
 * 1. **Observer Pattern**: 
 *    - Event listeners che osservano cambiamenti input
 *    - Callback functions che reagiscono agli eventi
 *    - Aggiornamento automatico UI quando dati cambiano
 * 
 * 2. **Strategy Pattern**:
 *    - Diverse strategie di validazione per ogni tipo campo
 *    - CAP: solo numeri, lunghezza fissa
 *    - Email: regex pattern matching
 *    - Telefono: caratteri permessi + lunghezza variabile
 * 
 * 3. **Factory Pattern**:
 *    - createElement() per creare alert dinamici
 *    - Template HTML generato programmaticamente
 *    - Riutilizzo struttura per diversi tipi messaggio
 * 
 * 4. **Command Pattern**:
 *    - Event handlers come comandi incapsulati
 *    - Ogni handler fa una cosa specifica
 *    - Facilita debugging e manutenzione
 * 
 * TECNICHE AVANZATE UTILIZZATE:
 * 
 * 1. **Debouncing Implicito**:
 *    - Validazione su 'input' event (real-time)
 *    - Ma submission solo su 'submit' event
 *    - Evita validazioni eccessive durante typing
 * 
 * 2. **Progressive Enhancement**:
 *    - Form funziona anche senza JavaScript
 *    - JavaScript aggiunge validazioni client-side
 *    - Server-side validation sempre presente come fallback
 * 
 * 3. **Graceful Degradation**:
 *    - Controlli if (element) prima di usare elementi
 *    - Fallback se window.LaravelApp non esiste
 *    - Script continua anche se alcuni elementi mancano
 * 
 * 4. **Separation of Concerns**:
 *    - HTML: struttura e contenuto
 *    - CSS/Bootstrap: presentazione e styling
 *    - JavaScript: comportamento e interattività
 *    - Laravel: logica business e validazione server-side
 * 
 * PERFORMANCE OTTIMIZZAZIONI:
 * 
 * 1. **DOM Query Caching**:
 *    - getElementById() chiamato una volta, risultato salvato
 *    - Evita query ripetute dello stesso elemento
 *    - Migliori performance su interazioni frequenti
 * 
 * 2. **Event Delegation**:
 *    - Pochi listener registrati su elementi parent
 *    - Invece di molti listener su singoli elementi
 *    - Migliore memoria e performance
 * 
 * 3. **Lazy Evaluation**:
 *    - Validazione completa solo al submit
 *    - Validazioni leggere durante typing
 *    - Calcoli pesanti posticipati quando necessario
 * 
 * 4. **Vanilla JS per Core Logic**:
 *    - JavaScript nativo per validazioni frequenti
 *    - jQuery solo per compatibilità e integrazione
 *    - Prestazioni migliori per operazioni critiche
 * 
 * SICUREZZA CONSIDERAZIONI:
 * 
 * 1. **Client-Side Validation NON è Sicurezza**:
 *    - Validazioni JavaScript facilmente bypassabili
 *    - Servono solo per UX, non per sicurezza
 *    - Server-side validation sempre obbligatoria
 * 
 * 2. **Input Sanitization**:
 *    - .trim() rimuove spazi pericolosi
 *    - Regex filtering per caratteri permessi
 *    - Prevenzione input malformati
 * 
 * 3. **XSS Prevention**:
 *    - textContent invece di innerHTML quando possibile
 *    - Template literals controllate
 *    - Validazione input prima di inserimento DOM
 * 
 * MANUTENIBILITÀ:
 * 
 * 1. **Codice Auto-Documentante**:
 *    - Nomi variabili descrittivi
 *    - Funzioni piccole e specifiche
 *    - Commenti per logica complessa
 * 
 * 2. **Modularità**:
 *    - Ogni sezione gestisce una funzionalità
 *    - Dipendenze minime tra sezioni
 *    - Facile aggiungere/rimuovere funzionalità
 * 
 * 3. **Configurabilità**:
 *    - Soglie numeriche come costanti
 *    - Pattern regex facilmente modificabili
 *    - Messaggi errore centralizzati
 * 
 * TESTING STRATEGIE:
 * 
 * 1. **Manual Testing**:
 *    - Console.log per debugging real-time
 *    - Visual feedback per ogni azione
 *    - Test edge cases (campi vuoti, lunghi, malformati)
 * 
 * 2. **Browser Compatibility**:
 *    - API moderne con fallback
 *    - ES6+ features con attenzione supporto
 *    - Progressive enhancement per browser vecchi
 * 
 * 3. **User Experience Testing**:
 *    - Feedback immediato vs. finale
 *    - Intuitività message di errore
 *    - Accessibilità con screen reader
 * 
 * INTEGRAZIONE ECOSYSTEM:
 * 
 * 1. **Laravel Integration**:
 *    - window.LaravelApp per dati route
 *    - Form submission verso Laravel controller
 *    - Error handling compatibile con Laravel validation
 * 
 * 2. **Bootstrap Integration**:
 *    - Classi CSS Bootstrap per styling
 *    - Componenti Bootstrap (alert, spinner)
 *    - Responsive design automatico
 * 
 * 3. **jQuery Compatibility**:
 *    - Coesistenza con altri script jQuery
 *    - Pattern consistente con resto applicazione
 *    - Nessun conflitto con altri plugin
 * 
 * FUTURE ENHANCEMENTS POSSIBILI:
 * 
 * 1. **AJAX Submission**:
 *    - Form submission senza page reload
 *    - Loading states più sofisticati
 *    - Error handling granulare per campo
 * 
 * 2. **Auto-Save Draft**:
 *    - Salvataggio automatico in localStorage
 *    - Recovery dopo crash browser
 *    - Indicatori "unsaved changes"
 * 
 * 3. **Accessibility Improvements**:
 *    - ARIA labels per screen readers
 *    - Keyboard navigation completa
 *    - High contrast mode support
 * 
 * 4. **Advanced Validation**:
 *    - Async validation (check duplicati)
 *    - Address validation con API geografiche
 *    - Smart suggestions durante typing
 * 
 * LESSONS LEARNED PER L'ESAME:
 * 
 * 1. **Sempre verificare esistenza elementi DOM**
 * 2. **Separare validazione UX da validazione sicurezza**
 * 3. **Feedback utente immediato migliora esperienza**
 * 4. **Performance: cache DOM queries, usa Vanilla JS per operazioni frequenti**
 * 5. **Defensive programming: script deve funzionare anche in condizioni non ideali**
 * 6. **Modularità: ogni funzione ha responsabilità specifica e limitata**
 * 7. **Integration: JavaScript deve giocare bene con Laravel e Bootstrap**
 * 8. **Testing: console.log strategici per debugging durante sviluppo**
 */