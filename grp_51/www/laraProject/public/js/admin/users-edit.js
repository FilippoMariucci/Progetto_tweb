/**
 * JAVASCRIPT PER FORM MODIFICA UTENTI - AMMINISTRAZIONE
 * 
 * Linguaggio: JavaScript + jQuery
 * Framework: jQuery 3.x (libreria JavaScript per manipolazione DOM e AJAX)
 * Scopo: Gestione completa del form di modifica utenti esistenti nel pannello admin
 * 
 * Funzionalità principali:
 * - Gestione campi condizionali in base al livello utente
 * - Anteprima modifiche con evidenziazione cambiamenti
 * - Validazione form lato client in tempo reale
 * - Controllo disponibilità username
 * - Scorciatoie da tastiera per azioni rapide
 * - Conferme per azioni pericolose (reset password, eliminazione)
 * - Suggerimenti automatici per compilazione campi
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('admin.users.edit caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.edit') {
        return; // Esce dalla funzione se non è la route corretta
    }
    
    /**
     * INIZIALIZZAZIONE DATI PAGINA
     * window.PageData è un oggetto globale popolato da Laravel con dati della pagina
     * Operatore || fornisce valore di default se PageData non esiste
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (uso futuro)
    
    // ===================================================================
    // SEZIONE: GESTIONE CAMPI CONDIZIONALI
    // ===================================================================
    
    /**
     * EVENT HANDLER: Cambiamento livello accesso utente
     * jQuery: .on('change') cattura l'evento change su select
     * Mostra/nasconde campi tecnico in base al livello selezionato
     * Differenza con create: qui i campi centro sono OBBLIGATORI per tecnici
     */
    $('#livello_accesso').on('change', function() {
        /**
         * JavaScript: const dichiara costante (ES6)
         * jQuery: $(this) riferisce all'elemento che ha scatenato l'evento
         * .val() ottiene il valore del campo select
         */
        const livello = $(this).val();
        const datiTecnico = $('#dati-tecnico'); // Selettore jQuery per div campi tecnico
        
        if (livello === '2') { // Livello 2 = Tecnico
            // jQuery: .slideDown() animazione di comparsa con slide
            datiTecnico.slideDown();
            
            /**
             * jQuery: .prop('required', true) imposta proprietà HTML required
             * IMPORTANTE: In edit TUTTI i campi tecnico sono obbligatori, incluso centro
             * Differenza chiave rispetto al form di creazione
             */
            $('#data_nascita, #specializzazione, #centro_assistenza_id').prop('required', true);
        } else {
            // jQuery: .slideUp() animazione di scomparsa con slide
            datiTecnico.slideUp();
            
            /**
             * jQuery: .prop('required', false) rimuove obbligo campi
             * Per tutti gli altri livelli i campi tecnico sono opzionali
             */
            $('#data_nascita, #specializzazione, #centro_assistenza_id').prop('required', false);
        }
    });
    
    // ===================================================================
    // SEZIONE: ANTEPRIMA MODIFICHE CON EVIDENZIAZIONE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Pulsante anteprima modifiche
     * jQuery: .on('click') per aprire modale con confronto dati
     * Funzionalità avanzata: mostra solo i campi modificati evidenziati
     */
    $('#previewBtn').on('click', function() {
        generatePreview(); // Chiamata a funzione personalizzata
        $('#previewModal').modal('show'); // Bootstrap: apre modale
    });
    
    /**
     * FUNZIONE: Generazione anteprima modifiche
     * JavaScript: function per organizzare logica complessa
     * Confronta dati originali (da Laravel) con dati correnti (dal form)
     */
    function generatePreview() {
        /**
         * OGGETTO DATI ORIGINALI
         * Recupera dati utente originali da window.PageData popolato da Laravel
         * Operatore || fornisce stringa vuota come fallback
         */
        const original = {
            nome: window.PageData?.user?.nome || '',
            cognome: window.PageData?.user?.cognome || '',
            username: window.PageData?.user?.username || '',
            livello_accesso: window.PageData?.user?.livello_accesso || '',
            specializzazione: window.PageData?.user?.specializzazione || '',
            data_nascita: window.PageData?.user?.data_nascita || '',
            centro_assistenza_id: window.PageData?.user?.centro_assistenza_id || ''
        };
        
        /**
         * OGGETTO DATI CORRENTI
         * Legge valori attuali dai campi del form con jQuery .val()
         */
        const current = {
            nome: $('#nome').val(),
            cognome: $('#cognome').val(),
            username: $('#username').val(),
            livello_accesso: $('#livello_accesso').val(),
            specializzazione: $('#specializzazione').val(),
            data_nascita: $('#data_nascita').val(),
            centro_assistenza_id: $('#centro_assistenza_id').val(),
            password: $('#password').val()
        };
        
        /**
         * MAPPING LIVELLI UTENTE
         * JavaScript: oggetto per convertire codici in etichette leggibili
         * Emoji per identificazione visiva rapida
         */
        const livelloLabels = {
            '2': '🔵 Tecnico',
            '3': '🟡 Staff',
            '4': '🔴 Amministratore'
        };
        
        /**
         * FUNZIONE: Evidenziazione modifiche
         * Confronta valore originale con attuale e evidenzia differenze
         * HTML: <span> con classe CSS e attributo title per tooltip
         */
        function highlightChange(originalValue, currentValue) {
            /**
             * JavaScript: != confronto non stretto (permette conversioni tipo)
             * Utile per confrontare stringhe e numeri
             */
            if (originalValue != currentValue) {
                return `<span class="highlight-change" title="Originale: ${originalValue}">${currentValue}</span>`;
            }
            /**
             * JavaScript: operatore || per valore di default
             * HTML: <em> per stile corsivo, classe Bootstrap per testo muto
             */
            return currentValue || '<em class="text-muted">Non inserito</em>';
        }
        
        /**
         * COSTRUZIONE HTML ANTEPRIMA
         * JavaScript: template literals (${}) per interpolazione variabili
         * HTML: struttura con classi Bootstrap per styling
         */
        let html = `
            <div class="preview-section">
                <div class="preview-title">Informazioni Account</div>
                <p><strong>Nome:</strong> ${highlightChange(original.nome, current.nome)}</p>
                <p><strong>Cognome:</strong> ${highlightChange(original.cognome, current.cognome)}</p>
                <p><strong>Username:</strong> ${highlightChange(original.username, current.username)}</p>
                <p><strong>Livello:</strong> ${highlightChange(livelloLabels[original.livello_accesso], livelloLabels[current.livello_accesso])}</p>
                ${current.password ? '<p><strong>Password:</strong> <span class="text-success">Nuova password impostata</span></p>' : ''}
            </div>
        `;
        
        /**
         * SEZIONE CONDIZIONALE PER TECNICI
         * Mostra campi aggiuntivi solo se il livello attuale è tecnico
         */
        if (current.livello_accesso === '2') {
            /**
             * OTTENIMENTO NOME CENTRO ASSISTENZA
             * jQuery: :selected per opzione selezionata, .text() per testo visibile
             * Gestisce caso in cui nessun centro sia selezionato
             */
            const centroNome = current.centro_assistenza_id ? 
                $('#centro_assistenza_id option:selected').text() : 'Nessuno';
            const centroOriginale = original.centro_assistenza_id ? 
                $(`#centro_assistenza_id option[value="${original.centro_assistenza_id}"]`).text() : 'Nessuno';
            
            /**
             * JavaScript: += operatore di concatenazione e assegnazione
             * Aggiunge sezione tecnico all'HTML esistente
             */
            html += `
                <div class="preview-section">
                    <div class="preview-title">Informazioni Tecnico</div>
                    <p><strong>Data Nascita:</strong> ${highlightChange(original.data_nascita, current.data_nascita)}</p>
                    <p><strong>Specializzazione:</strong> ${highlightChange(original.specializzazione, current.specializzazione)}</p>
                    <p><strong>Centro:</strong> ${highlightChange(centroOriginale, centroNome)}</p>
                </div>
            `;
        }
        
        /**
         * CONTEGGIO MODIFICHE AUTOMATICO
         * JavaScript: Object.keys() ottiene array delle chiavi dell'oggetto
         * .forEach() itera su ogni chiave per confrontare valori
         */
        let changesCount = 0;
        Object.keys(original).forEach(key => {
            /**
             * JavaScript: && operatore AND logico per doppia condizione
             * Conta solo se valore è cambiato E il nuovo valore non è vuoto
             */
            if (original[key] != current[key] && current[key] !== '') {
                changesCount++;
            }
        });
        
        // Conta anche cambio password come modifica
        if (current.password) changesCount++;
        
        /**
         * ALERT INFORMATIVO IN BASE AL NUMERO DI MODIFICHE
         * Bootstrap: alert-warning per cambiamenti, alert-info per nessuna modifica
         * JavaScript: operatore ternario per plurale/singolare dinamico
         */
        if (changesCount > 0) {
            html = `<div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>${changesCount} modifica${changesCount > 1 ? 'he' : ''} rilevata${changesCount > 1 ? 'e' : ''}.</strong>
            </div>` + html;
        } else {
            html = `<div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Nessuna modifica rilevata.
            </div>` + html;
        }
        
        /**
         * jQuery: .html() inserisce contenuto HTML nel elemento
         * Popola il contenuto della modale con l'anteprima generata
         */
        $('#previewContent').html(html);
    }
    
    /**
     * EVENT HANDLER: Conferma modifiche da anteprima
     * Pulsante nella modale per procedere con il salvataggio
     */
    $('#updateFromPreview').on('click', function() {
        $('#previewModal').modal('hide');  // Bootstrap: chiude modale
        $('#editUserForm').submit();       // jQuery: invia form
    });
    
    // ===================================================================
    // SEZIONE: VALIDAZIONE FORM LATO CLIENT
    // ===================================================================
    
    /**
     * EVENT HANDLER: Submit del form di modifica
     * jQuery: .on('submit') cattura l'invio del form
     * Validazione completa prima dell'invio al server
     */
    $('#editUserForm').on('submit', function(e) {
        /**
         * JavaScript: let dichiara variabile modificabile
         * Flag per tracciare se tutti i campi sono validi
         */
        let isValid = true;
        
        /**
         * ARRAY CAMPI OBBLIGATORI BASE
         * JavaScript: array con nomi dei campi che devono essere sempre compilati
         */
        const requiredFields = ['nome', 'cognome', 'username', 'livello_accesso'];
        
        /**
         * VALIDAZIONE CAMPI OBBLIGATORI
         * JavaScript: .forEach() itera su ogni campo dell'array
         * jQuery: .trim() rimuove spazi vuoti, .addClass() aggiunge classe CSS
         */
        requiredFields.forEach(function(field) {
            const element = $(`#${field}`); // Template literal per selettore dinamico
            if (!element.val().trim()) {    // Verifica se campo è vuoto
                element.addClass('is-invalid');    // Bootstrap: bordo rosso errore
                isValid = false;
            } else {
                element.removeClass('is-invalid'); // Rimuove stile errore se valido
            }
        });
        
        /**
         * VALIDAZIONE PASSWORD SPECIALE
         * Controlla corrispondenza solo se password è stata inserita
         * In modifica la password è opzionale
         */
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirmation').val();
        
        /**
         * JavaScript: && operatore AND logico
         * Valida solo se entrambe le password sono inserite
         */
        if (password && password !== passwordConfirm) {
            $('#password, #password_confirmation').addClass('is-invalid');
            isValid = false;
            showAlert('error', 'Le password non coincidono'); // Funzione personalizzata
        } else {
            $('#password, #password_confirmation').removeClass('is-invalid');
        }
        
        /**
         * VALIDAZIONE CONDIZIONALE PER TECNICI
         * Controlli aggiuntivi solo per livello 2 (tecnici)
         */
        if ($('#livello_accesso').val() === '2') {
            const requiredTecnico = ['data_nascita', 'specializzazione', 'centro_assistenza_id'];
            requiredTecnico.forEach(function(field) {
                const element = $(`#${field}`);
                if (!element.val()) {
                    element.addClass('is-invalid');
                    isValid = false;
                } else {
                    element.removeClass('is-invalid');
                }
            });
        }
        
        /**
         * GESTIONE ERRORI DI VALIDAZIONE
         * Se ci sono errori, impedisce invio e mostra il primo errore
         */
        if (!isValid) {
            e.preventDefault(); // jQuery: impedisce invio form
            
            /**
             * SCROLL AUTOMATICO AL PRIMO ERRORE
             * jQuery: .first() prende primo elemento, .offset().top ottiene posizione Y
             * .animate() crea scroll fluido con offset per header fisso
             */
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        } else {
            /**
             * FORM VALIDO - DISABILITA PULSANTE
             * jQuery: .prop('disabled', true) disabilita elemento
             * .html() cambia testo con spinner di caricamento
             */
            $('#updateBtn').prop('disabled', true).html('<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i>Salvando...');
        }
    });
    
    // ===================================================================
    // SEZIONE: VALIDAZIONE IN TEMPO REALE
    // ===================================================================
    
    /**
     * CONTROLLO DISPONIBILITÀ USERNAME
     * JavaScript: let per variabile timeout, usata per debouncing
     * Debouncing evita troppe chiamate durante la digitazione
     */
    let usernameTimeout;
    $('#username').on('input', function() {
        /**
         * JavaScript: clearTimeout() annulla timeout precedente
         * Tecnica debouncing: aspetta pausa nella digitazione prima di validare
         */
        clearTimeout(usernameTimeout);
        const username = $(this).val();
        const originalUsername = window.PageData?.user?.username || '';
        
        /**
         * CONTROLLO CONDIZIONALE
         * Valida solo se username è nuovo, diverso dall'originale e abbastanza lungo
         */
        if (username && username !== originalUsername && username.length >= 3) {
            /**
             * JavaScript: setTimeout() ritarda esecuzione di 500ms
             * Arrow function (=>) per mantenere contesto
             */
            usernameTimeout = setTimeout(() => {
                checkUsernameAvailability(username);
            }, 500);
        }
    });
    
    /**
     * FUNZIONE: Controllo disponibilità username
     * Placeholder per futura implementazione con API
     * @param {string} username - Username da verificare
     */
    function checkUsernameAvailability(username) {
        /**
         * VALIDAZIONE BASE LUNGHEZZA
         * Per ora solo controllo lato client, in futuro AJAX al server
         */
        if (username.length < 3) {
            $('#username').addClass('is-invalid');
            showAlert('warning', 'Username deve essere di almeno 3 caratteri');
        }
    }
    
    /**
     * EVENT HANDLER: Conferma password in tempo reale
     * jQuery: .on('input') si attiva ad ogni carattere digitato
     * Feedback immediato sulla corrispondenza password
     */
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirm = $(this).val();
        
        /**
         * VALIDAZIONE VISIVA IMMEDIATA
         * Bootstrap: .is-valid (verde) e .is-invalid (rosso)
         * Feedback visivo in tempo reale durante digitazione
         */
        if (password && confirm) {
            if (password === confirm) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('#password').removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $('#password').removeClass('is-valid').addClass('is-invalid');
            }
        }
    });
    
    // ===================================================================
    // SEZIONE: FUNZIONI HELPER
    // ===================================================================
    
    /**
     * FUNZIONE: Sistema notifiche toast
     * Crea notifiche temporanee in stile toast per feedback utente
     * @param {string} type - Tipo di alert (success, error, warning, info)
     * @param {string} message - Messaggio da mostrare
     */
    function showAlert(type, message) {
        /**
         * MAPPING TIPI ALERT
         * JavaScript: operatore ternario annidato per mapping tipo -> classe CSS
         * Bootstrap: classi alert per diversi stili di notifica
         */
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        /**
         * MAPPING ICONE BOOTSTRAP
         * Bootstrap Icons: icone diverse per ogni tipo di messaggio
         */
        const icon = type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        /**
         * CREAZIONE ELEMENTO ALERT
         * jQuery: $() crea elemento DOM, template literals per HTML complesso
         * CSS: position-fixed per posizionamento fisso, z-index alto per sovrapposizione
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
         * jQuery: .append() aggiunge elemento al body
         * Mostra la notifica nell'angolo in alto a destra
         */
        $('body').append(alert);
        
        /**
         * AUTO-RIMOZIONE TEMPORIZZATA
         * JavaScript: setTimeout() per rimozione automatica dopo 5 secondi
         * jQuery: .fadeOut() animazione di scomparsa, .remove() elimina da DOM
         */
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // ===================================================================
    // SEZIONE: SCORCIATOIE DA TASTIERA
    // ===================================================================
    
    /**
     * EVENT HANDLER: Gestione tasti speciali
     * jQuery: $(document) per catturare eventi su tutto il documento
     * Implementa scorciatoie da tastiera per azioni rapide
     */
    $(document).on('keydown', function(e) {
        /**
         * SCORCIATOIA: Ctrl+S per salvare
         * JavaScript: e.ctrlKey verifica se Ctrl è premuto
         * e.key contiene il carattere del tasto premuto
         */
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault(); // Impedisce salvataggio browser
            $('#editUserForm').submit();
        }
        
        /**
         * SCORCIATOIA: Ctrl+P per anteprima
         * Override del comportamento predefinito di stampa browser
         */
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault(); // Impedisce dialogo stampa browser
            $('#previewBtn').click();
        }
        
        /**
         * SCORCIATOIA: Escape per chiudere modali
         * Comportamento standard per chiusura con Esc
         */
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide'); // Bootstrap: chiude modali aperte
        }
    });
    
    // ===================================================================
    // SEZIONE: CONFERME PER AZIONI PERICOLOSE
    // ===================================================================
    
    /**
     * EVENT HANDLER: Conferma reset password
     * jQuery: [action*="reset-password"] selettore per attributo contenente testo
     * Richiede conferma prima di resettare password utente
     */
    $('form[action*="reset-password"] button').on('click', function(e) {
        /**
         * JavaScript: confirm() mostra dialogo di conferma browser
         * Template literal con placeholder Laravel per nome utente
         */
        if (!confirm('Resettare la password per {{ $user->nome_completo }}?\n\nVerrà generata una password temporanea.')) {
            e.preventDefault(); // Blocca invio form se non confermato
        }
    });
    
    /**
     * EVENT HANDLER: Conferma eliminazione utente
     * Doppia sicurezza: richiede digitazione testo specifico
     * Prevenzione eliminazioni accidentali
     */
    $('form[action*="destroy"] button').on('click', function(e) {
        /**
         * TESTO DI CONFERMA SPECIFICO
         * JavaScript: stringa che l'utente deve digitare esattamente
         * Blade Laravel: {{ }} per interpolazione variabili PHP
         */
        const confirmText = 'ELIMINA {{ strtoupper($user->username) }}';
        
        /**
         * JavaScript: prompt() richiede input utente
         * Confronto esatto per confermare intenzione
         */
        const userInput = prompt(`ATTENZIONE: Stai per eliminare definitivamente l'account di {{ $user->nome_completo }}.\n\nQuesta azione NON può essere annullata!\n\nPer confermare, scrivi esattamente: ${confirmText}`);
        
        /**
         * VERIFICA TESTO INSERITO
         * JavaScript: !== confronto stretto, null se utente ha annullato
         */
        if (userInput !== confirmText) {
            e.preventDefault(); // Blocca eliminazione
            if (userInput !== null) { // Se non ha premuto Annulla
                alert('Testo di conferma non corretto. Eliminazione annullata.');
            }
        }
    });
    
    // ===================================================================
    // SEZIONE: SUGGERIMENTI AUTOMATICI
    // ===================================================================
    
    /**
     * ARRAY SPECIALIZZAZIONI PREDEFINITE
     * JavaScript: array con suggerimenti per campo specializzazione
     * Aiuta l'utente con opzioni comuni
     */
    const specializzazioni = [
        'Elettrodomestici',
        'Climatizzatori',
        'Lavatrici e Lavastoviglie',
        'Frigoriferi e Freezer',
        'Forni e Microonde',
        'Aspirapolvere',
        'Piccoli Elettrodomestici',
        'Caldaie e Scaldabagni',
        'Impianti Elettrici'
    ];
    
    /**
     * EVENT HANDLER: Suggerimento automatico specializzazione
     * jQuery: .on('focus') quando utente clicca nel campo
     * Mostra esempio casuale se campo è vuoto
     */
    $('#specializzazione').on('focus', function() {
        if (!$(this).val()) {
            /**
             * JavaScript: Math.random() numero 0-1, Math.floor() arrotonda per difetto
             * Seleziona elemento casuale dall'array specializzazioni
             */
            $(this).attr('placeholder', 'es: ' + specializzazioni[Math.floor(Math.random() * specializzazioni.length)]);
        }
    });
    
    // ===================================================================
    // SEZIONE: INIZIALIZZAZIONE FINALE
    // ===================================================================
    
    /**
     * TRIGGER INIZIALE CAMPI CONDIZIONALI
     * jQuery: .trigger('change') simula evento change al caricamento
     * Assicura che campi tecnico siano mostrati/nascosti correttamente
     */
    $('#livello_accesso').trigger('change');
    
    /**
     * EVIDENZIAZIONE CAMPI MODIFICATI
     * jQuery: 'input, select, textarea' selettore multiplo per tutti i form controls
     * .on('change') per ogni modifica, .addClass() per bordo di avviso
     */
    $('input, select, textarea').on('change', function() {
        $(this).addClass('border-warning'); // Bootstrap: bordo arancione
    });
    
    // Log finali di conferma
    console.log('Form modifica utente inizializzato');
    console.log('Utente in modifica: {{ $user->nome_completo }} ({{ $user->username }})');
});