/**
 * Gestione Autenticazione e Login
 * TechSupport Pro - Gruppo 51
 * 
 * LINGUAGGIO: JavaScript ES6+ con jQuery e Bootstrap
 * SCOPO: Gestione completa del sistema di autenticazione lato client
 * INTEGRAZIONE: Lavora con Laravel backend per autenticazione sicura
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Validazione form di login in tempo reale
 * - UX migliorata con spinner e feedback visivi
 * - Sicurezza: prevenzione doppio submit, rilevazione Caps Lock
 * - Accessibilità: scorciatoie tastiera, toggle password
 * - Helper per sviluppo: inserimento credenziali di test
 */

/**
 * CLASSE: AuthManager
 * 
 * Gestisce tutti gli aspetti dell'autenticazione lato client.
 * Si occupa dell'esperienza utente durante login e registrazione.
 * 
 * DESIGN PATTERN: Module Pattern con incapsulamento delle funzionalità
 * RESPONSABILITÀ:
 * - Validazione input utente
 * - Feedback visivo durante autenticazione  
 * - Prevenzione errori comuni (doppio submit, caps lock)
 * - Logging delle attività per analytics
 */
class AuthManager {
    /**
     * COSTRUTTORE AuthManager
     * 
     * LINGUAGGIO: JavaScript ES6 (Class Constructor)
     * SCOPO: Inizializza tutti i componenti del sistema di autenticazione
     * 
     * Viene eseguito automaticamente alla creazione dell'istanza.
     * Orchestrazione dell'inizializzazione di tutti i sottosistemi.
     */
    constructor() {
        this.initializeAuth();  // Avvia inizializzazione completa
    }

    /**
     * METODO: initializeAuth
     * 
     * LINGUAGGIO: JavaScript
     * SCOPO: Coordinatore principale dell'inizializzazione
     * 
     * Inizializza tutti i componenti nell'ordine corretto:
     * 1. Form di login e validazione
     * 2. Funzionalità UX (password toggle, shortcuts)
     * 3. Helper sviluppo e sicurezza
     * 
     * CONSOLE.LOG: Conferma inizializzazione per debugging
     */
    initializeAuth() {
        this.setupLoginForm();          // Gestione submit e validazione base
        this.setupPasswordToggle();     // Mostra/nascondi password
        this.setupCredentialsHelpers(); // Helper per credenziali test (sviluppo)
        this.setupValidation();         // Validazione in tempo reale
        this.setupKeyboardShortcuts();  // Scorciatoie tastiera per UX
        this.detectCapsLock();          // Rileva e avvisa Caps Lock attivo
        
        console.log('🔐 Auth Manager inizializzato');
    }

    /**
     * METODO: setupLoginForm
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Gestisce il comportamento del form di login
     * 
     * FUNZIONALITÀ IMPLEMENTATE:
     * - Validazione lato client prima invio
     * - Prevenzione doppio submit (problema comune web app)
     * - Feedback visivo con spinner durante elaborazione
     * - Pulizia input (trim() per rimuovere spazi)
     * 
     * SECURITY PATTERN: Client-side validation + server-side validation
     * Nota: validazione client è solo per UX, server deve sempre validare
     */
    setupLoginForm() {
        // jQuery: seleziona form con ID specifico
        const loginForm = $('#loginForm');
        
        // Verifica esistenza form prima di procedere
        if (loginForm.length) {
            // Flag per prevenire doppio submit (closure variable)
            let formSubmitted = false;
            
            // Event listener per evento submit del form
            loginForm.on('submit', (e) => {
                // Legge e pulisce valori input
                const username = $('#username').val().trim();  // trim() rimuove spazi
                const password = $('#password').val();         // password non trimmed (può contenere spazi)
                
                // === VALIDAZIONE LATO CLIENT ===
                // Controllo presenza valori obbligatori
                if (!username || !password) {
                    e.preventDefault();  // Blocca invio form
                    showToast('Inserisci username e password', 'warning');  // Notifica utente
                    return false;
                }
                
                // === PREVENZIONE DOPPIO SUBMIT ===
                // Controlla se form già inviato
                if (formSubmitted) {
                    e.preventDefault();  // Blocca secondo submit
                    return false;
                }
                
                // Imposta flag submit e mostra feedback visivo
                formSubmitted = true;
                this.showLoginSpinner();  // Mostra spinner su pulsante
                
                // === TIMER DI SICUREZZA ===
                // Reset flag dopo 5 secondi (caso errori che non causano redirect)
                setTimeout(() => {
                    formSubmitted = false;
                    this.hideLoginSpinner();  // Ripristina pulsante
                }, 5000);
                
                // Se arriva qui, form viene inviato normalmente
            });
        }
    }

    /**
     * METODO: setupPasswordToggle
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Icons
     * SCOPO: Implementa toggle visibilità password (show/hide)
     * 
     * UX PATTERN: Permette all'utente di vedere password digitata
     * Utile per password complesse o su dispositivi mobili
     * 
     * FUNZIONAMENTO:
     * 1. Click su icona occhio
     * 2. Cambia type="password" ↔ type="text"
     * 3. Cambia icona: bi-eye ↔ bi-eye-slash
     * 
     * ELEMENTI HTML COINVOLTI:
     * - #togglePassword: pulsante con icona
     * - #password: campo password  
     * - #togglePasswordIcon: icona Bootstrap Icons
     */
    setupPasswordToggle() {
        // Event listener per pulsante toggle
        $('#togglePassword').on('click', () => {
            const passwordField = $('#password');      // Campo password
            const icon = $('#togglePasswordIcon');     // Icona bootstrap
            
            // Controlla stato attuale del campo
            if (passwordField.attr('type') === 'password') {
                // === MOSTRA PASSWORD ===
                passwordField.attr('type', 'text');                    // Cambia a testo visibile
                icon.removeClass('bi-eye').addClass('bi-eye-slash');    // Cambia icona a "occhio barrato"
            } else {
                // === NASCONDI PASSWORD ===  
                passwordField.attr('type', 'password');                // Cambia a password nascosta
                icon.removeClass('bi-eye-slash').addClass('bi-eye');    // Cambia icona a "occhio"
            }
        });
    }

    /**
     * METODO: setupCredentialsHelpers
     * 
     * LINGUAGGIO: JavaScript + jQuery + Data Attributes
     * SCOPO: Helper per sviluppo - inserimento rapido credenziali test
     * 
     * DEVELOPMENT PATTERN: Facilita testing durante sviluppo
     * Permette di testare rapidamente diversi livelli utente
     * 
     * FUNZIONAMENTO:
     * 1. Pulsanti con data attributes contengono credenziali
     * 2. Click inserisce automaticamente username/password
     * 3. Feedback visivo (bordo verde) conferma inserimento
     * 4. Toast notification informa utente
     * 
     * ESEMPIO HTML:
     * <button class="fill-credentials" 
     *         data-username="adminadmin" 
     *         data-password="dNWRdNWR">Admin</button>
     * 
     * SICUREZZA: Da rimuovere in produzione!
     */
    setupCredentialsHelpers() {
        // Event listener per tutti i pulsanti helper
        $('.fill-credentials').on('click', function() {
            // Legge credenziali da data attributes
            const username = $(this).data('username');  // data-username
            const password = $(this).data('password');  // data-password
            
            // === INSERIMENTO AUTOMATICO ===
            $('#username').val(username);  // Compila campo username
            $('#password').val(password);  // Compila campo password
            
            // === FEEDBACK VISIVO ===
            // Aggiunge bordo verde per 2 secondi
            $('#username, #password').addClass('border-success');
            
            // Timer per rimuovere evidenziazione
            setTimeout(() => {
                $('#username, #password').removeClass('border-success');
            }, 2000);
            
            // === NOTIFICA UTENTE ===
            showToast(`Credenziali inserite: ${username}`, 'info');
        });
    }

    /**
     * METODO: setupValidation
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Validation Classes
     * SCOPO: Validazione in tempo reale durante digitazione
     * 
     * REALTIME VALIDATION PATTERN: Feedback immediato all'utente
     * Migliora UX evitando errori solo al submit
     * 
     * BOOTSTRAP CLASSES USATE:
     * - 'is-valid': bordo verde, campo valido
     * - 'is-invalid': bordo rosso, campo non valido
     * 
     * VALIDAZIONI IMPLEMENTATE:
     * - Username: minimo 3 caratteri
     * - Password: minimo 6 caratteri
     * 
     * EVENT: 'input' si scatena ad ogni carattere digitato
     */
    setupValidation() {
        // === VALIDAZIONE USERNAME ===
        $('#username').on('input', function() {
            const value = $(this).val().trim();  // Valore pulito
            const field = $(this);               // Campo corrente
            
            // Controlla lunghezza minima
            if (value.length < 3) {
                // Campo non valido
                field.removeClass('is-valid').addClass('is-invalid');
            } else {
                // Campo valido
                field.removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // === VALIDAZIONE PASSWORD ===
        $('#password').on('input', function() {
            const value = $(this).val();  // Password non trimmed (può avere spazi)
            const field = $(this);        // Campo corrente
            
            // Controlla lunghezza minima  
            if (value.length < 6) {
                // Password troppo corta
                field.removeClass('is-valid').addClass('is-invalid');
            } else {
                // Password sufficientemente lunga
                field.removeClass('is-invalid').addClass('is-valid');
            }
        });
    }

    /**
     * METODO: setupKeyboardShortcuts
     * 
     * LINGUAGGIO: JavaScript + jQuery + KeyCode Events
     * SCOPO: Migliora UX con scorciatoie tastiera
     * 
     * KEYBOARD UX PATTERN: Navigazione rapida tra campi
     * Riduce necessità di usare mouse
     * 
     * SCORCIATOIE IMPLEMENTATE:
     * - Username + Enter → Focus su Password
     * - Password + Enter → Submit form
     * 
     * EVENT: 'keypress' si scatena quando tasto viene premuto
     * e.which: codice tasto premuto (13 = Enter)
     */
    setupKeyboardShortcuts() {
        // === SHORTCUT DA USERNAME ===
        $('#username').on('keypress', function(e) {
            if (e.which === 13) {  // Enter key code
                $('#password').focus();  // Sposta focus su password
            }
        });
        
        // === SHORTCUT DA PASSWORD ===
        $('#password').on('keypress', function(e) {
            if (e.which === 13) {  // Enter key code
                $('#loginForm').submit();  // Invia form
            }
        });
    }

    /**
     * METODO: detectCapsLock
     * 
     * LINGUAGGIO: JavaScript + jQuery + Modern Browser APIs
     * SCOPO: Rileva e avvisa quando Caps Lock è attivo
     * 
     * SECURITY UX: Previene errori comuni di login
     * Molti utenti non si accorgono di avere Caps Lock attivo
     * 
     * BROWSER API: getModifierState() 
     * API moderna per rilevare stato tasti modificatori
     * (Caps Lock, Shift, Ctrl, Alt, etc.)
     * 
     * FUNZIONAMENTO:
     * 1. Intercetta keypress su campo password
     * 2. Controlla stato Caps Lock con API browser
     * 3. Mostra/nasconde warning dinamicamente
     * 
     * COMPATIBILITÀ: Funziona solo su browser moderni
     * Fallback gracefully su browser vecchi
     */
    detectCapsLock() {
        // Event listener su campo password
        $('#password').on('keypress', function(e) {
            // === RILEVAZIONE CAPS LOCK ===
            // Controlla supporto API e stato Caps Lock
            const capsLock = e.originalEvent.getModifierState && 
                           e.originalEvent.getModifierState('CapsLock');
            
            if (capsLock) {
                // === CAPS LOCK ATTIVO ===
                // Verifica se warning già presente (evita duplicati)
                if (!$('#capsLockWarning').length) {
                    // Inserisce warning dopo campo password
                    $(this).after(`
                        <small id="capsLockWarning" class="text-warning mt-1">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Caps Lock è attivo
                        </small>
                    `);
                }
            } else {
                // === CAPS LOCK DISATTIVO ===
                // Rimuove warning se presente
                $('#capsLockWarning').remove();
            }
        });
    }

    /**
     * METODO: showLoginSpinner
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Spinner
     * SCOPO: Mostra feedback visivo durante processo di login
     * 
     * UX PATTERN: Loading state per azioni asincrone
     * Informa l'utente che l'azione è in elaborazione
     * 
     * CAMBIAMENTI APPLICATI:
     * 1. Disabilita pulsante (previene click multipli)
     * 2. Sostituisce contenuto con spinner + testo
     * 3. Mantiene styling Bootstrap
     * 
     * BOOTSTRAP SPINNER: Componente animato per loading states
     * 'spinner-border-sm': versione piccola per pulsanti
     */
    showLoginSpinner() {
        const loginBtn = $('#loginBtn');  // Pulsante login
        
        // Disabilita e cambia contenuto
        loginBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Accesso in corso...
        `);
    }

    /**
     * METODO: hideLoginSpinner  
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Icons
     * SCOPO: Ripristina stato normale pulsante login
     * 
     * Metodo complementare a showLoginSpinner()
     * Ripristina pulsante allo stato originale
     * 
     * RIPRISTINO:
     * 1. Riabilita pulsante
     * 2. Ripristina icona e testo originali
     * 3. Mantiene classes Bootstrap
     */
    hideLoginSpinner() {
        const loginBtn = $('#loginBtn');  // Pulsante login
        
        // Riabilita e ripristina contenuto originale
        loginBtn.prop('disabled', false).html(`
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Accedi
        `);
    }

    /**
     * METODO: logLoginAttempt
     * 
     * LINGUAGGIO: JavaScript + Browser APIs + Date/Navigator Objects  
     * SCOPO: Registra tentativo di login per analytics e sicurezza
     * 
     * ANALYTICS PATTERN: Raccolta dati per:
     * - Statistiche utilizzo
     * - Rilevamento attività sospette  
     * - Debugging problemi login
     * - Analisi dispositivi/browser utenti
     * 
     * PARAMETRI:
     * @param {String} username - Username del tentativo login
     * 
     * DATI RACCOLTI:
     * - Username (per identificazione)
     * - Timestamp ISO (momento esatto)
     * - User Agent (browser/OS info)  
     * - Risoluzione schermo (tipo dispositivo)
     * 
     * BROWSER APIs UTILIZZATE:
     * - Date().toISOString(): timestamp formato standard
     * - navigator.userAgent: info browser/OS
     * - screen.width/height: risoluzione display
     * 
     * PRIVACY: In produzione considerare GDPR compliance
     */
    logLoginAttempt(username) {
        // === RACCOLTA DATI ===
        const logData = {
            username: username,                           // Identifica utente
            timestamp: new Date().toISOString(),         // Momento esatto (formato ISO)
            userAgent: navigator.userAgent,              // Info browser/OS
            screen: `${screen.width}x${screen.height}`   // Risoluzione schermo
        };
        
        // === LOGGING LOCALE ===
        console.log('Login attempt:', logData);  // Per debugging sviluppo
        
        // === INVIO AL SERVER (COMMENTATO) ===
        // In una versione più avanzata, invia dati al server per analytics
        // $.post('/api/log-login-attempt', logData);
        
        // POSSIBILI ESTENSIONI:
        // - Rilevamento IP lato server
        // - Geolocalizzazione (con permesso utente)
        // - Rilevamento tentativi multipli falliti
        // - Alert per accessi da nuovi dispositivi
    }
}

// === INIZIALIZZAZIONE CONDIZIONALE ===

/**
 * DOCUMENT READY HANDLER
 * 
 * LINGUAGGIO: jQuery  
 * SCOPO: Inizializza AuthManager solo quando necessario
 * 
 * CONDITIONAL LOADING PATTERN: Evita di caricare codice non necessario
 * AuthManager viene istanziato solo su pagine di autenticazione
 * 
 * CONDIZIONI PER INIZIALIZZAZIONE:
 * - Presenza form con ID 'loginForm' 
 * - Oppure elemento con classe 'auth-page'
 * 
 * VANTAGGI:
 * - Performance: meno codice eseguito su pagine non-auth
 * - Memoria: nessuna istanza inutile
 * - Errori: evita errori su pagine senza elementi auth
 * 
 * WINDOW.authManager: Istanza globale per accesso da altri script
 */
$(document).ready(function() {
    // Controlla presenza elementi autenticazione
    if ($('#loginForm').length || $('.auth-page').length) {
        // Crea istanza globale AuthManager
        window.authManager = new AuthManager();
    }
    // Se condizioni non soddisfatte, AuthManager non viene caricato
});