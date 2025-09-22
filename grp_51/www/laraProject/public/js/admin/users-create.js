/**
 * JAVASCRIPT PER FORM CREAZIONE UTENTI - AMMINISTRAZIONE
 * 
 * Linguaggio: JavaScript + jQuery
 * Framework: jQuery 3.x (libreria JavaScript per manipolazione DOM e AJAX)
 * Scopo: Gestione completa del form di creazione nuovi utenti nel pannello admin
 * 
 * Funzionalità principali:
 * - Gestione campi condizionali in base al livello utente
 * - Validazione form lato client 
 * - Generazione password sicure
 * - Controllo visibilità password
 * - Auto-completamento username
 * - Anteprima dati prima dell'invio
 * - Prevenzione doppi submit
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('admin.users.create caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.create') {
        return; // Esce dalla funzione se non è la route corretta
    }
    
    /**
     * INIZIALIZZAZIONE DATI PAGINA
     * window.PageData è un oggetto globale popolato da Laravel con dati della pagina
     * Operatore || fornisce valore di default se PageData non esiste
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (uso futuro)
    
    // Log di conferma caricamento
    console.log('🚀 Form creazione utente caricato');
    
    /**
     * VARIABILE FLAG ANTI-DOPPIO SUBMIT
     * Previene l'invio multiplo accidentale del form
     * JavaScript: var dichiara variabile con scope funzione
     */
    var formSubmitted = false;
    
    // ===================================================================
    // SEZIONE: GESTIONE CAMPI CONDIZIONALI
    // ===================================================================
    
    /**
     * EVENT HANDLER: Cambiamento livello accesso utente
     * jQuery: .on('change') cattura l'evento change su select
     * Mostra/nasconde campi tecnico in base al livello selezionato
     */
    $('#livello_accesso').on('change', function() {
        /**
         * jQuery: $(this) riferisce all'elemento che ha scatenato l'evento
         * .val() ottiene il valore del campo select
         */
        var livello = $(this).val();
        var datiTecnico = $('#dati-tecnico'); // Selettore jQuery per div campi tecnico
        
        if (livello === '2') { // Livello 2 = Tecnico
            // jQuery: .slideDown() animazione di comparsa con slide
            datiTecnico.slideDown();
            
            /**
             * jQuery: .attr('required', true) aggiunge attributo HTML required
             * Rende obbligatori i campi per tecnici
             */
            $('#data_nascita, #specializzazione').attr('required', true);
            
            // Centro assistenza rimane SEMPRE opzionale
            $('#centro_assistenza_id').attr('required', false);
            
            console.log('✅ Campi tecnico mostrati');
            
        } else {
            // jQuery: .slideUp() animazione di scomparsa con slide
            datiTecnico.slideUp();
            
            /**
             * jQuery: .attr('required', false) rimuove obbligo campi
             * Per tutti gli altri livelli i campi tecnico sono opzionali
             */
            $('#data_nascita, #specializzazione, #centro_assistenza_id').attr('required', false);
            
            console.log('📴 Campi tecnico nascosti');
        }
    });
    
    // ===================================================================
    // SEZIONE: GESTIONE PASSWORD
    // ===================================================================
    
    /**
     * EVENT HANDLER: Toggle visibilità password
     * jQuery: .on('click') cattura click su pulsante occhio
     * Alterna tra password nascosta (*****) e visibile (testo)
     */
    $('#togglePassword').on('click', function() {
        var passwordField = $('#password'); // Campo password
        var icon = $(this).find('i'); // Icona Bootstrap dentro il pulsante
        
        /**
         * jQuery: .attr('type') ottiene/modifica l'attributo type dell'input
         * HTML: type="password" nasconde il testo, type="text" lo mostra
         */
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text'); // Mostra password
            // Bootstrap Icons: cambia icona da occhio a occhio barrato
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password'); // Nascondi password
            // Bootstrap Icons: cambia icona da occhio barrato a occhio
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    /**
     * EVENT HANDLER: Generatore password casuale
     * JavaScript: genera password sicura di 12 caratteri
     * Usa caratteri alfanumerici e simboli speciali
     */
    $('#generatePassword').on('click', function() {
        /**
         * JavaScript: stringa con tutti i caratteri possibili per la password
         * Include maiuscole, minuscole, numeri e simboli speciali
         */
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        var password = '';
        
        /**
         * JavaScript: ciclo for per generare 12 caratteri casuali
         * Math.random() genera numero 0-1, Math.floor() arrotonda per difetto
         * charAt() prende carattere alla posizione specificata
         */
        for (var i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        /**
         * jQuery: .val(valore) imposta il valore dei campi
         * Inserisce password generata sia in password che in conferma
         */
        $('#password, #password_confirmation').val(password);
        
        /**
         * Mostra temporaneamente la password generata
         * Utile per vedere cosa è stato generato
         */
        $('#password').attr('type', 'text');
        $('#togglePassword').find('i').removeClass('bi-eye').addClass('bi-eye-slash');
        
        /**
         * FEEDBACK VISIVO TEMPORANEO
         * jQuery: .removeClass() rimuove classe CSS, .addClass() aggiunge classe
         * Bootstrap: cambia colore pulsante per feedback
         */
        var btn = $(this);
        btn.removeClass('btn-outline-info').addClass('btn-success').text('Generata!');
        
        /**
         * JavaScript: setTimeout() esegue funzione dopo delay specificato
         * Ripristina aspetto originale del pulsante dopo 1,5 secondi
         */
        setTimeout(function() {
            btn.removeClass('btn-success').addClass('btn-outline-info');
            btn.html('<i class="bi bi-magic me-1"></i>Genera Password Sicura');
        }, 1500);
    });
    
    /**
     * EVENT HANDLER: Verifica corrispondenza password
     * jQuery: .on('input') si attiva ad ogni carattere digitato
     * Controlla che password e conferma coincidano in tempo reale
     */
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();        // Password principale
        var confirmation = $(this).val();           // Campo di conferma
        
        /**
         * JavaScript: confronto stringhe per verificare corrispondenza
         * Bootstrap: .addClass('is-invalid') aggiunge stile errore rosso
         */
        if (confirmation && password !== confirmation) {
            $(this).addClass('is-invalid');    // Bordo rosso di errore
        } else {
            $(this).removeClass('is-invalid'); // Rimuove stile errore
        }
    });
    
    // ===================================================================
    // SEZIONE: AUTO-COMPLETAMENTO USERNAME
    // ===================================================================
    
    /**
     * EVENT HANDLER: Auto-generazione username da nome e cognome
     * jQuery: .on('input') cattura ogni modifica nei campi nome/cognome
     * JavaScript: .toLowerCase() converte in minuscolo, .trim() rimuove spazi
     */
    $('#nome, #cognome').on('input', function() {
        var nome = $('#nome').val().toLowerCase().trim();
        var cognome = $('#cognome').val().toLowerCase().trim();
        
        /**
         * JavaScript: && operatore AND logico
         * !$('#username').val() controlla se username è vuoto
         * Genera username solo se entrambi i campi sono compilati e username vuoto
         */
        if (nome && cognome && !$('#username').val()) {
            $('#username').val(nome + '.' + cognome); // Formato: nome.cognome
        }
    });
    
    // ===================================================================
    // SEZIONE: VALIDAZIONE FORM PRINCIPALE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Submit del form
     * jQuery: .on('submit') cattura l'invio del form
     * Esegue validazione completa lato client prima dell'invio
     */
    $('#createUserForm').on('submit', function(e) {
        
        /**
         * PREVENZIONE DOPPIO SUBMIT
         * Controlla flag globale per evitare invii multipli
         */
        if (formSubmitted) {
            e.preventDefault(); // jQuery: blocca invio form
            return false;
        }
        
        console.log('📤 Invio form...');
        
        /**
         * PULIZIA MESSAGGI ERRORE PRECEDENTI
         * jQuery: .remove() elimina elementi dal DOM
         */
        $('.alert-danger').remove();
        
        /**
         * INIZIALIZZAZIONE VARIABILI VALIDAZIONE
         * JavaScript: array per raccogliere errori trovati
         */
        var hasErrors = false;
        var errors = [];
        
        /**
         * VALIDAZIONE CAMPO USERNAME
         * jQuery: .val().trim() ottiene valore senza spazi
         * JavaScript: ! operatore NOT logico per controllare se vuoto
         */
        if (!$('#username').val().trim()) {
            hasErrors = true;
            errors.push('Username obbligatorio');
        }
        
        /**
         * VALIDAZIONE PASSWORD COMPLESSA
         * Controlla esistenza, lunghezza minima e corrispondenza
         */
        var password = $('#password').val();
        var passwordConfirm = $('#password_confirmation').val();
        
        if (!password) {
            hasErrors = true;
            errors.push('Password obbligatoria');
        } else if (password.length < 8) {
            hasErrors = true;
            errors.push('Password troppo corta (minimo 8 caratteri)');
        } else if (password !== passwordConfirm) {
            hasErrors = true;
            errors.push('Le password non coincidono');
        }
        
        /**
         * VALIDAZIONE CAMPI ANAGRAFICI
         * Controlli base su nome e cognome obbligatori
         */
        if (!$('#nome').val().trim()) {
            hasErrors = true;
            errors.push('Nome obbligatorio');
        }
        
        if (!$('#cognome').val().trim()) {
            hasErrors = true;
            errors.push('Cognome obbligatorio');
        }
        
        /**
         * VALIDAZIONE LIVELLO ACCESSO
         * Assicura che sia selezionato un livello valido
         */
        if (!$('#livello_accesso').val()) {
            hasErrors = true;
            errors.push('Seleziona livello di accesso');
        }
        
        /**
         * VALIDAZIONE SPECIFICA PER TECNICI (LIVELLO 2)
         * Controlli aggiuntivi solo per utenti tecnici
         */
        if ($('#livello_accesso').val() === '2') {
            if (!$('#data_nascita').val()) {
                hasErrors = true;
                errors.push('Data nascita obbligatoria per tecnici');
            }
            if (!$('#specializzazione').val().trim()) {
                hasErrors = true;
                errors.push('Specializzazione obbligatoria per tecnici');
            }
            // NOTA: Centro assistenza NON è obbligatorio per design
        }
        
        /**
         * GESTIONE ERRORI DI VALIDAZIONE
         * Se trovati errori, blocca invio e mostra messaggi
         */
        if (hasErrors) {
            e.preventDefault(); // jQuery: impedisce invio form
            
            /**
             * COSTRUZIONE LISTA ERRORI HTML
             * JavaScript: ciclo for per creare lista HTML degli errori
             */
            var errorList = '';
            for (var i = 0; i < errors.length; i++) {
                errorList += '<li>' + errors[i] + '</li>';
            }
            
            /**
             * CREAZIONE ALERT BOOTSTRAP PER ERRORI
             * Bootstrap: alert-danger per stile errore rosso
             * Include pulsante chiusura e animazione fade
             */
            var alertHtml = '<div class="alert alert-danger alert-dismissible fade show">' +
                '<strong>Correggi questi errori:</strong>' +
                '<ul class="mb-0 mt-2">' + errorList + '</ul>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>';
            
            /**
             * jQuery: .prepend() aggiunge contenuto all'inizio del form
             * Mostra alert errori in cima al form
             */
            $('#createUserForm').prepend(alertHtml);
            
            /**
             * jQuery: .animate() crea animazione scroll fluida
             * Porta l'utente in cima alla pagina per vedere gli errori
             */
            $('html, body').animate({ scrollTop: 0 }, 300);
            
            console.log('❌ Errori trovati:', errors);
            return false; // Blocca completamente l'invio
        }
        
        /**
         * FORM VALIDO - PREPARAZIONE INVIO
         * Imposta flag, disabilita pulsante e mostra loading
         */
        formSubmitted = true;
        
        /**
         * DISABILITAZIONE PULSANTE SUBMIT
         * jQuery: .prop('disabled', true) disabilita elemento
         * Previene click multipli durante l'invio
         */
        var submitBtn = $('#createBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="spinner-border spinner-border-sm me-1"></i>Creazione...');
        
        /**
         * MESSAGGIO DI CARICAMENTO
         * Bootstrap: alert-info per informazione in blu
         * Informa l'utente che l'operazione è in corso
         */
        var loadingHtml = '<div class="alert alert-info">' +
            '<i class="bi bi-hourglass-split me-2"></i>' +
            '<strong>Creazione in corso...</strong> Non chiudere la pagina.' +
            '</div>';
        
        $('#createUserForm').prepend(loadingHtml);
        
        console.log('✅ Form inviato correttamente');
        
        /**
         * INVIO NORMALE DEL FORM
         * return true permette al browser di inviare il form a Laravel
         */
        return true;
    });
    
    // ===================================================================
    // SEZIONE: ANTEPRIMA DATI UTENTE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Pulsante anteprima dati
     * jQuery: .on('click') per aprire modale con riepilogo
     * Mostra anteprima dei dati inseriti prima della creazione finale
     */
    $('#previewBtn').on('click', function() {
        /**
         * RACCOLTA DATI DAL FORM
         * jQuery: .val() per input, :selected per option selezionate
         * Operatore || per valori di default se campi vuoti
         */
        var username = $('#username').val() || 'Non specificato';
        var nome = $('#nome').val() || '';
        var cognome = $('#cognome').val() || '';
        var livello = $('#livello_accesso option:selected').text() || 'Non selezionato';
        
        /**
         * COSTRUZIONE HTML ANTEPRIMA BASE
         * JavaScript: concatenazione stringhe per creare HTML
         */
        var previewHtml = '<div class="mb-3">' +
            '<strong>Username:</strong> ' + username + '<br>' +
            '<strong>Nome:</strong> ' + nome + ' ' + cognome + '<br>' +
            '<strong>Livello:</strong> ' + livello;
        
        /**
         * ANTEPRIMA DATI AGGIUNTIVI PER TECNICI
         * Controlla se livello selezionato è tecnico (valore '2')
         */
        if ($('#livello_accesso').val() === '2') {
            var dataNascita = $('#data_nascita').val() || 'Non specificata';
            var specializzazione = $('#specializzazione').val() || 'Non specificata';
            var centro = $('#centro_assistenza_id option:selected').text() || 'Nessun centro assegnato';
            
            /**
             * AGGIUNTA INFORMAZIONI TECNICHE
             * Concatena dati specifici per utenti tecnici
             */
            previewHtml += '<br><strong>Data Nascita:</strong> ' + dataNascita +
                '<br><strong>Specializzazione:</strong> ' + specializzazione +
                '<br><strong>Centro:</strong> ' + centro;
        }
        
        previewHtml += '</div>';
        
        /**
         * VISUALIZZAZIONE MODALE ANTEPRIMA
         * jQuery: .html() inserisce contenuto HTML
         * Bootstrap: .modal('show') apre la modale
         */
        $('#previewContent').html(previewHtml);
        $('#previewModal').modal('show');
    });
    
    /**
     * EVENT HANDLER: Conferma creazione da anteprima
     * Pulsante nella modale per procedere con la creazione
     */
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');  // Chiude modale
        $('#createUserForm').submit();     // Invia form
    });
    
    // ===================================================================
    // SEZIONE: INIZIALIZZAZIONE INTERFACCIA
    // ===================================================================
    
    /**
     * SETUP INIZIALE DELLA PAGINA
     * jQuery: .hide() nasconde elementi, .focus() mette focus
     */
    
    // Nasconde sezione dati tecnico all'avvio
    $('#dati-tecnico').hide();
    
    // Mette focus automatico sul primo campo
    $('#username').focus();
    
    // Log finale di conferma
    console.log('✅ Form inizializzato - Centro assistenza opzionale');
});