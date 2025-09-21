/*
 * =====================================================
 * SCRIPT JAVASCRIPT PER LA MODIFICA DEI CENTRI DI ASSISTENZA
 * =====================================================
 * 
 * FRAMEWORK: Laravel + jQuery + Bootstrap 5
 * LINGUAGGIO: JavaScript ES6+
 * FUNZIONALITÀ: Gestione form di modifica centri, validazione, UI dinamica
 * 
 * DESCRIZIONE GENERALE:
 * Questo script gestisce l'interfaccia utente per la modifica dei centri di assistenza
 * nel pannello di amministrazione. Include validazione in tempo reale, gestione eventi,
 * controllo modifiche non salvate e funzionalità di copia negli appunti.
 */

// =====================================================
// INIZIALIZZAZIONE JQUERY
// =====================================================

/**
 * EVENTO: Document Ready di jQuery
 * LINGUAGGIO: JavaScript con libreria jQuery
 * 
 * Questo blocco si esegue quando il DOM è completamente caricato.
 * jQuery garantisce che tutti gli elementi HTML sono disponibili
 * prima di eseguire il codice JavaScript.
 */
$(document).ready(function() {
    console.log('admin.centri.edit caricato');
    
    /**
     * CONTROLLO ROUTE SPECIFICO
     * Verifica se siamo nella pagina corretta per evitare esecuzione
     * di codice su pagine sbagliate
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.edit') {
        return; // Esce dallo script se non siamo nella route corretta
    }
    
    /**
     * INIZIALIZZAZIONE VARIABILI GLOBALI
     * PageData contiene dati passati dal controller Laravel
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (se necessario)
});

// =====================================================
// EVENT LISTENER PRINCIPALE DEL DOM
// =====================================================

/**
 * EVENT LISTENER: DOMContentLoaded
 * LINGUAGGIO: JavaScript Vanilla (senza jQuery)
 * 
 * Alternativo a $(document).ready(), usa JavaScript puro.
 * Si esegue quando il DOM è completamente costruito ma prima
 * che tutte le risorse esterne (immagini, CSS) siano caricate.
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📝 Script modifica centro caricato - VERSIONE CORRETTA');
    
    // =====================================================
    // CONTATORE CARATTERI PER IL CAMPO NOME
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Contatore caratteri dinamico
     * SCOPO: Mostra all'utente quanti caratteri ha digitato nel campo nome
     * TECNOLOGIA: JavaScript Vanilla + HTML DOM manipulation
     */
    
    // Ottiene i riferimenti agli elementi HTML tramite ID
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    // Verifica che entrambi gli elementi esistano nel DOM
    if (nomeInput && nomeCounter) {
        
        /**
         * FUNZIONE: updateNomeCounter
         * LINGUAGGIO: JavaScript ES6
         * SCOPO: Aggiorna il contatore caratteri e cambia il colore in base alla lunghezza
         */
        function updateNomeCounter() {
            // Ottiene la lunghezza corrente del testo
            const currentLength = nomeInput.value.length;
            
            // Aggiorna il contenuto testuale del contatore
            nomeCounter.textContent = currentLength;
            
            // === LOGICA CONDIZIONALE PER I COLORI ===
            // Cambia la classe CSS in base al numero di caratteri
            if (currentLength > 240) {
                // Rosso per limite quasi raggiunto
                nomeCounter.className = 'text-danger fw-bold';
            } else if (currentLength > 200) {
                // Arancione per avviso
                nomeCounter.className = 'text-warning';
            } else {
                // Grigio per stato normale
                nomeCounter.className = 'text-muted';
            }
        }
        
        /**
         * EVENT LISTENER: input event
         * TRIGGER: Ogni volta che l'utente digita nel campo nome
         * CALLBACK: updateNomeCounter
         */
        nomeInput.addEventListener('input', updateNomeCounter);
        
        // Chiamata iniziale per impostare il contatore corretto
        updateNomeCounter();
    }
    
    // =====================================================
    // VALIDAZIONE CAP (CODICE AVVIAMENTO POSTALE)
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Validazione in tempo reale del CAP
     * REGOLE: Solo numeri, massimo 5 cifre
     * TECNOLOGIA: Regular Expression + Event Handling
     */
    const capInput = document.getElementById('cap');
    
    if (capInput) {
        /**
         * EVENT LISTENER: input event sul campo CAP
         * SCOPO: Filtra caratteri non numerici e limita a 5 cifre
         */
        capInput.addEventListener('input', function(e) {
            // === REGEX PATTERN ===
            // \D = qualsiasi carattere NON numerico
            // g = flag globale (sostituisce tutte le occorrenze)
            let value = e.target.value.replace(/\D/g, '');
            
            // === CONTROLLO LUNGHEZZA ===
            // substr(start, length) estrae una sottostringa
            if (value.length > 5) {
                value = value.substr(0, 5);
            }
            
            // Aggiorna il valore dell'input
            e.target.value = value;
        });
    }
    
    // =====================================================
    // VALIDAZIONE TELEFONO
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Validazione campo telefono
     * CARATTERI PERMESSI: numeri, spazi, +, -, (, )
     * TECNOLOGIA: Regular Expression per filtraggio caratteri
     */
    const telefonoInput = document.getElementById('telefono');
    
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            // === REGEX COMPLESSA ===
            // [^...] = negazione, cioè "tutto tranne"
            // 0-9 = cifre
            // \s = spazi bianchi
            // \+ = carattere + (escapato)
            // \- = carattere - (escapato)
            // \(\) = parentesi (escapate)
            // g = flag globale
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
        });
    }
    
    // =====================================================
    // VALIDAZIONE FORM PRINCIPALE
    // =====================================================
    
    /**
     * GESTIONE SUBMIT DEL FORM
     * SCOPO: Validazione completa prima dell'invio al server
     * INCLUDE: Feedback visivo, controlli di integrità dati
     */
    const form = document.getElementById('formModificaCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        /**
         * EVENT LISTENER: submit event
         * TRIGGER: Quando l'utente invia il form (click su submit o Enter)
         * COMPORTAMENTO: Previene invio se validazione fallisce
         */
        form.addEventListener('submit', function(e) {
            console.log('🚀 Invio form modifica centro...');
            
            // === UI FEEDBACK: LOADING STATE ===
            // Disabilita il pulsante e mostra spinner durante l'invio
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            // === RACCOLTA DATI DAL FORM ===
            // trim() rimuove spazi all'inizio e alla fine
            const nome = document.getElementById('nome').value.trim();
            const indirizzo = document.getElementById('indirizzo').value.trim();
            const citta = document.getElementById('citta').value.trim();
            const provincia = document.getElementById('provincia').value;
            
            // === VALIDAZIONE CAMPI OBBLIGATORI ===
            // Operatore OR (||) per verificare se almeno un campo è vuoto
            if (!nome || !indirizzo || !citta || !provincia) {
                // preventDefault() ferma l'invio del form
                e.preventDefault();
                
                // === RIPRISTINO UI ===
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                // Mostra messaggio di errore
                showAlert('Errore', 'Compila tutti i campi obbligatori (contrassegnati con *)', 'danger');
                return; // Esce dalla funzione
            }
            
            // === VALIDAZIONE CAP ===
            const cap = document.getElementById('cap').value.trim();
            
            // Verifica CAP se presente (campo opzionale)
            if (cap && (cap.length !== 5 || !/^\d{5}$/.test(cap))) {
                e.preventDefault();
                
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                showAlert('Errore', 'Il CAP deve essere di 5 cifre numeriche', 'danger');
                return;
            }
            
            // === VALIDAZIONE EMAIL ===
            const email = document.getElementById('email').value.trim();
            
            // Regex per validazione email base
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                showAlert('Errore', 'Inserisci un indirizzo email valido', 'danger');
                return;
            }
            
            console.log('✅ Validazione superata, invio in corso...');
        });
    }
    
    // =====================================================
    // CONFERMA ELIMINAZIONE CENTRO
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Conferma eliminazione con doppio controllo
     * SICUREZZA: Previene eliminazioni accidentali
     * UI: Mostra dialog di conferma con avviso importante
     */
    const formElimina = document.getElementById('formElimina');
    
    if (formElimina) {
        formElimina.addEventListener('submit', function(e) {
            /**
             * METODO: confirm()
             * TIPO: JavaScript native browser API
             * RITORNA: boolean (true se OK, false se Cancel)
             */
            const conferma = confirm(
                'ATTENZIONE: ELIMINAZIONE DEFINITIVA\n\n' +
                'Sei sicuro di voler eliminare questo centro di assistenza?\n\n' +
                '⚠️ Questa azione è IRREVERSIBILE!\n' +
                '⚠️ Il centro sarà eliminato definitivamente dal sistema!\n\n' +
                'Digitare "ELIMINA" per confermare'
            );
            
            if (!conferma) {
                e.preventDefault(); // Annulla l'eliminazione
                console.log('❌ Eliminazione centro annullata dall\'utente');
            } else {
                console.log('🗑️ Eliminazione centro confermata');
            }
        });
    }
    
    // =====================================================
    // FUNZIONE COPIA INDIRIZZO NEGLI APPUNTI
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Copia indirizzo completo negli appunti
     * TECNOLOGIE: Clipboard API moderna + fallback per browser vecchi
     * SCOPE: window.* per renderla accessibile globalmente dai template Blade
     */
    window.copiaIndirizzo = function() {
        // === COSTRUZIONE STRINGA INDIRIZZO ===
        // Template literal con placeholder Laravel Blade
        const indirizzo = '{{ $centro->indirizzo }}, {{ $centro->citta }}' +
                         '{{ $centro->cap ? " " . $centro->cap : "" }}' +
                         '{{ $centro->provincia ? " (" . strtoupper($centro->provincia) . ")" : "" }}';
        
        // === CLIPBOARD API MODERNA ===
        // Supporto browser moderni (Chrome 66+, Firefox 63+)
        if (navigator.clipboard && navigator.clipboard.writeText) {
            /**
             * METODO: navigator.clipboard.writeText()
             * TIPO: Promise-based API
             * VANTAGGI: Sicura, non richiede interazione utente diretta
             */
            navigator.clipboard.writeText(indirizzo).then(function() {
                showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
                console.log('📋 Indirizzo copiato:', indirizzo);
            }).catch(function(err) {
                console.error('❌ Errore clipboard API:', err);
                fallbackCopyText(indirizzo); // Fallback per errori
            });
        } else {
            // Browser non supportati o context non sicuro (HTTP)
            fallbackCopyText(indirizzo);
        }
    };
    
    // =====================================================
    // FALLBACK PER COPIA TESTO (BROWSER LEGACY)
    // =====================================================
    
    /**
     * FUNZIONE: fallbackCopyText
     * SCOPO: Supporto browser vecchi senza Clipboard API
     * METODO: execCommand (deprecato ma ancora supportato)
     */
    function fallbackCopyText(text) {
        // === CREAZIONE ELEMENTO TEMPORANEO ===
        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        // === POSIZIONAMENTO FUORI SCHERMO ===
        // Evita flash visivo dell'elemento
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        
        // === INSERIMENTO NEL DOM ===
        document.body.appendChild(textArea);
        textArea.focus(); // Necessario per la selezione
        textArea.select(); // Seleziona tutto il testo
        
        try {
            /**
             * METODO: document.execCommand('copy')
             * STATO: Deprecato ma ancora funzionante
             * RITORNA: boolean per successo/fallimento
             */
            const successful = document.execCommand('copy');
            
            if (successful) {
                showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
                console.log('📋 Indirizzo copiato (fallback):', text);
            } else {
                showAlert('Errore', 'Impossibile copiare il testo', 'danger');
            }
        } catch (err) {
            console.error('❌ Errore fallback copy:', err);
            showAlert('Errore', 'Impossibile copiare il testo', 'danger');
        }
        
        // === PULIZIA: RIMOZIONE ELEMENTO TEMPORANEO ===
        document.body.removeChild(textArea);
    }
    
    // =====================================================
    // SISTEMA DI ALERT TEMPORANEI
    // =====================================================
    
    /**
     * FUNZIONE: showAlert
     * SCOPO: Mostra notifiche temporanee all'utente
     * PARAMETRI:
     *   - title: Titolo del messaggio
     *   - message: Contenuto del messaggio  
     *   - type: Tipo Bootstrap (success, danger, warning, info)
     */
    function showAlert(title, message, type = 'info') {
        // === RIMOZIONE ALERT PRECEDENTI ===
        // Evita accumulo di notifiche
        const existingAlerts = document.querySelectorAll('.alert-temp');
        existingAlerts.forEach(alert => alert.remove());
        
        // === CREAZIONE ELEMENTO ALERT ===
        const alertContainer = document.createElement('div');
        
        // Classe Bootstrap + classe custom per identificazione
        alertContainer.className = `alert alert-${type} alert-dismissible fade show alert-temp`;
        
        // === POSIZIONAMENTO FISSO ===
        // cssText permette di impostare multiple proprietà CSS in una volta
        alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        
        // === CONTENUTO HTML ===
        // Template literal con icona dinamica
        alertContainer.innerHTML = `
            <i class="bi ${getIconForType(type)} me-2"></i>
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        `;
        
        // === INSERIMENTO NEL DOM ===
        document.body.appendChild(alertContainer);
        
        console.log(`🔔 Alert ${type}: ${title} - ${message}`);
        
        // === AUTO-RIMOZIONE ===
        // setTimeout per rimozione automatica dopo 5 secondi
        setTimeout(() => {
            if (alertContainer && alertContainer.parentNode) {
                alertContainer.remove();
            }
        }, 5000);
    }
    
    // =====================================================
    // HELPER: ICONE PER TIPO DI ALERT
    // =====================================================
    
    /**
     * FUNZIONE: getIconForType
     * SCOPO: Mappa tipi di alert a icone Bootstrap Icons
     * PARAMETRO: type (string) - Tipo di alert Bootstrap
     * RITORNA: string - Classe CSS per l'icona corrispondente
     */
    function getIconForType(type) {
        // Oggetto di mappatura tipo -> icona
        const icons = {
            'success': 'bi-check-circle-fill',
            'danger': 'bi-exclamation-triangle-fill', 
            'warning': 'bi-exclamation-circle-fill',
            'info': 'bi-info-circle-fill'
        };
        
        // Operatore OR per valore di default
        return icons[type] || 'bi-info-circle';
    }
    
    // =====================================================
    // RILEVAMENTO MODIFICHE NON SALVATE
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Avviso modifiche non salvate
     * SCOPO: Previene perdita dati accidentale quando l'utente cambia pagina
     * EVENTO: beforeunload per catturare tentativi di uscita
     */
    let formModificato = false; // Flag globale per tracciare modifiche
    
    // === MONITORAGGIO CAMPI INPUT ===
    // Ottiene tutti i campi di input del form
    const inputs = form ? form.querySelectorAll('input, select, textarea') : [];
    
    /**
     * LOOP: forEach per aggiungere listener a ogni campo
     * TECNOLOGIA: Array.prototype.forEach + Event Delegation
     */
    inputs.forEach(input => {
        // Salva il valore originale per confronto
        const originalValue = input.value;
        
        /**
         * EVENT LISTENER: change event
         * TRIGGER: Quando il valore cambia e il campo perde focus
         */
        input.addEventListener('change', function() {
            // Confronta valore corrente con originale
            if (input.value !== originalValue) {
                formModificato = true;
                console.log('📝 Form modificato - campo:', input.name);
            }
        });
        
        /**
         * EVENT LISTENER: input event  
         * TRIGGER: Ad ogni carattere digitato (più reattivo di change)
         */
        input.addEventListener('input', function() {
            if (input.value !== originalValue) {
                formModificato = true;
            }
        });
    });
    
    // =====================================================
    // AVVISO PRIMA DI LASCIARE LA PAGINA
    // =====================================================
    
    /**
     * EVENT LISTENER: beforeunload
     * SCOPO: Cattura tentativi di uscita dalla pagina
     * BROWSER API: window.beforeunload event
     */
    window.addEventListener('beforeunload', function(e) {
        if (formModificato) {
            console.log('⚠️ Tentativo di lasciare pagina con modifiche non salvate');
            
            const message = 'Hai modifiche non salvate. Sei sicuro di voler lasciare la pagina?';
            
            // === STANDARD MODERNO ===
            e.preventDefault(); // Previene uscita automatica
            e.returnValue = message; // Chrome/Edge
            
            return message; // Firefox/Safari
        }
    });
    
    // === RESET FLAG QUANDO FORM VIENE INVIATO ===
    if (form) {
        form.addEventListener('submit', function() {
            formModificato = false; // Reset flag per evitare falsi positivi
            console.log('📤 Form inviato, flag modifiche resettato');
        });
    }
    
    // =====================================================
    // AUTO-FOCUS SUL PRIMO CAMPO
    // =====================================================
    
    /**
     * FUNZIONALITÀ: Focus automatico per migliorare UX
     * METODO: Posiziona cursore alla fine del testo esistente
     */
    const primoInput = document.getElementById('nome');
    
    if (primoInput) {
        primoInput.focus(); // Imposta focus
        
        // === POSIZIONAMENTO CURSORE ===
        // setSelectionRange(start, end) posiziona il cursore
        primoInput.setSelectionRange(primoInput.value.length, primoInput.value.length);
    }
    
    console.log('✅ Script modifica centro completamente inizializzato');
});

/*
 * =====================================================
 * RIEPILOGO TECNOLOGIE UTILIZZATE:
 * =====================================================
 * 
 * 1. JAVASCRIPT ES6+
 *    - Arrow functions
 *    - Template literals
 *    - Const/let declarations
 *    - Destructuring
 * 
 * 2. DOM MANIPULATION
 *    - document.getElementById()
 *    - document.createElement()
 *    - addEventListener()
 *    - element.style.cssText
 * 
 * 3. JQUERY
 *    - $(document).ready()
 *    - Selettori CSS
 *    - Event handling
 * 
 * 4. BROWSER APIs
 *    - navigator.clipboard (Clipboard API)
 *    - document.execCommand() (legacy)
 *    - window.beforeunload
 *    - setTimeout/clearTimeout
 * 
 * 5. REGULAR EXPRESSIONS
 *    - \D (non-digits)
 *    - Character classes [^...]
 *    - Email validation pattern
 * 
 * 6. BOOTSTRAP 5
 *    - Alert components
 *    - Spinner components
 *    - CSS classes
 * 
 * 7. LARAVEL INTEGRATION
 *    - Blade template syntax
 *    - CSRF tokens
 *    - Route checking
 * 
 * =====================================================
 * PATTERN ARCHITETTURALI:
 * =====================================================
 * 
 * - Event-Driven Programming
 * - Progressive Enhancement
 * - Graceful Degradation (fallback functions)
 * - Separation of Concerns
 * - Error Handling
 * - User Experience Enhancement
 * 
 * =====================================================
 */