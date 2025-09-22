/**
 * JAVASCRIPT PER LISTA UTENTI - AMMINISTRAZIONE
 * 
 * Linguaggio: JavaScript + jQuery + AJAX
 * Framework: jQuery 3.x per manipolazione DOM, Bootstrap per UI
 * Scopo: Gestione della lista utenti con filtri, ricerca e azioni in tempo reale
 * 
 * Funzionalità principali:
 * - Filtri dinamici per livello, centro, data registrazione
 * - Ricerca in tempo reale con debouncing
 * - Reset password via AJAX con visualizzazione password temporanea
 * - Eliminazione utenti con conferma
 * - Scorciatoie da tastiera per azioni rapide
 * - Sistema notifiche toast per feedback
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('admin.users.index caricato');

    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.index') {
        return; // Esce dalla funzione se non è la route corretta
    }
    
    /**
     * INIZIALIZZAZIONE DATI PAGINA
     * window.PageData è un oggetto globale popolato da Laravel con dati della pagina
     * Operatore || fornisce valore di default se PageData non esiste
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (uso futuro)
    
    // Log di conferma inizializzazione
    console.log('Gestione Utenti inizializzata - modalità singola');
    
    // ===================================================================
    // SEZIONE: FILTRI DINAMICI
    // ===================================================================
    
    /**
     * EVENT HANDLER: Filtri automatici
     * jQuery: selettore multiplo per più campi, .on('change') per modifiche
     * Invia automaticamente il form quando l'utente cambia un filtro
     * 
     * Campi gestiti:
     * - #livello_accesso: Filtra per ruolo utente (Tecnico, Staff, Admin)
     * - #centro_assistenza_id: Filtra per centro di appartenenza  
     * - #data_registrazione: Filtra per periodo di registrazione
     */
    $('#livello_accesso, #centro_assistenza_id, #data_registrazione').on('change', function() {
        /**
         * jQuery: $(this).attr('name') ottiene attributo name del campo
         * $(this).val() ottiene valore selezionato
         * Log per debugging delle azioni filtro
         */
        console.log('Applicazione filtro:', $(this).attr('name'), '=', $(this).val());
        
        /**
         * jQuery: .closest('form') risale al primo form antenato
         * .submit() invia il form al server per applicare il filtro
         * Laravel riceverà i parametri via GET
         */
        $(this).closest('form').submit();
    });
    
    // ===================================================================
    // SEZIONE: RICERCA DINAMICA CON DEBOUNCING
    // ===================================================================
    
    /**
     * VARIABILE TIMEOUT PER DEBOUNCING
     * JavaScript: let permette riassegnazione, usata per gestire timeout
     * Debouncing evita troppe richieste durante la digitazione
     */
    let searchTimeout;
    
    /**
     * EVENT HANDLER: Ricerca in tempo reale
     * jQuery: .on('input') si attiva ad ogni carattere digitato
     * Implementa ricerca con debouncing per performance ottimali
     */
    $('#search').on('input', function() {
        /**
         * JavaScript: clearTimeout() annulla timeout precedente
         * Evita invio multiplo di richieste durante digitazione rapida
         */
        clearTimeout(searchTimeout);
        
        /**
         * jQuery: $(this).val().trim() ottiene valore senza spazi
         * Pulisce automaticamente l'input dalla ricerca
         */
        const query = $(this).val().trim();
        
        /**
         * LOGICA RICERCA CONDIZIONALE
         * Cerca solo se: almeno 2 caratteri OR campo completamente vuoto
         * Evita ricerche con singoli caratteri (troppo generiche)
         */
        if (query.length >= 2 || query.length === 0) {
            /**
             * JavaScript: setTimeout() ritarda esecuzione di 800ms
             * Debouncing: attende pausa nella digitazione prima di cercare
             * Arrow function (=>) mantiene contesto this
             */
            searchTimeout = setTimeout(() => {
                console.log('Ricerca per:', query);
                $('#filterForm').submit(); // Invia form con termine di ricerca
            }, 800); // 800ms di delay per buona UX
        }
    });
    
    // ===================================================================
    // SEZIONE: AZIONI UTENTE CON AJAX
    // ===================================================================
    
    /**
     * EVENT HANDLER: Reset password via AJAX
     * jQuery: selettore attributo [action*="text"] per form contenenti "reset-password"
     * AJAX per evitare reload pagina e mostrare password temporanea
     */
    $('form[action*="reset-password"]').on('submit', function(e) {
        /**
         * jQuery: e.preventDefault() impedisce submit normale del form
         * Intercettiamo per gestire con AJAX invece del submit tradizionale
         */
        e.preventDefault();
        
        /**
         * JavaScript: confirm() mostra dialogo di conferma nativo browser
         * Return anticipato se utente annulla l'azione
         */
        if (!confirm('Resettare la password di questo utente?')) {
            return;
        }
        
        /**
         * RIFERIMENTI DOM PER MANIPOLAZIONE UI
         * jQuery: $(this) form corrente, .find() cerca elementi figli
         * Salva riferimenti per manipolare stati durante AJAX
         */
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.text(); // Salva testo originale pulsante
        
        /**
         * UI FEEDBACK: Disabilita pulsante e mostra loading
         * jQuery: .prop('disabled', true) disabilita elemento
         * .html() cambia contenuto con spinner Bootstrap
         */
        button.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Elaborazione...');
        
        /**
         * CHIAMATA AJAX PRINCIPALE
         * jQuery: $.ajax() per richiesta asincrona al server
         * Configurazione completa con gestione success/error
         */
        $.ajax({
            url: form.attr('action'),    // URL dal form action
            method: 'POST',              // Metodo HTTP
            data: form.serialize(),      // Serializza tutti i campi form
            
            /**
             * SUCCESS CALLBACK: Eseguito se server risponde 200 OK
             * @param {Object} response - Risposta JSON dal server Laravel
             */
            success: function(response) {
                if (response.success) {
                    /**
                     * MOSTRA PASSWORD TEMPORANEA IN ALERT
                     * Template literals con HTML complesso per notifica ricca
                     * Include password, pulsante copia, auto-dismiss
                     */
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999; min-width: 400px;">
                            <h6><i class="bi bi-check-circle me-2"></i>Password Resetata</h6>
                            <p>${response.message}</p>
                            <hr>
                            <p class="mb-0">
                                <strong>Password Temporanea:</strong> 
                                <code class="bg-light p-1 rounded">${response.temp_password}</code>
                                <button type="button" class="btn btn-outline-primary btn-sm ms-2" 
                                        onclick="navigator.clipboard.writeText('${response.temp_password}')">
                                    <i class="bi bi-clipboard"></i> Copia
                                </button>
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    
                    /**
                     * jQuery: .append() aggiunge alert al body
                     * Mostra notifica nell'angolo superiore destro
                     */
                    $('body').append(alertHtml);
                    
                    /**
                     * AUTO-DISMISS TEMPORIZZATO
                     * JavaScript: setTimeout() per rimozione automatica dopo 15 secondi
                     * Bootstrap: .alert('close') chiude e rimuove alert
                     */
                    setTimeout(() => $('.alert').alert('close'), 15000);
                } else {
                    // Usa helper function per errori dal server
                    showNotification(response.message, 'danger');
                }
            },
            
            /**
             * ERROR CALLBACK: Eseguito per errori HTTP/network
             * Gestisce fallimenti della richiesta AJAX
             */
            error: function() {
                showNotification('Errore nel reset della password', 'danger');
            },
            
            /**
             * COMPLETE CALLBACK: Eseguito sempre (success O error)
             * Ripristina stato originale del pulsante
             */
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    /**
     * EVENT HANDLER: Conferma eliminazione utente
     * jQuery: selettore specifico per pulsanti elimina dentro form destroy
     * Implementa conferma con feedback visivo durante eliminazione
     */
    $('form[action*="destroy"] button[type="submit"]').on('click', function(e) {
        /**
         * RACCOLTA INFORMAZIONI UTENTE DA ELIMINARE
         * jQuery: .closest() risale nella gerarchia DOM
         * .find() cerca elementi discendenti per recuperare nome utente
         */
        const form = $(this).closest('form');
        const userRow = $(this).closest('tr');           // Riga tabella
        const userName = userRow.find('h6').first().text().trim(); // Nome utente
        
        /**
         * DIALOGO CONFERMA CON NOME SPECIFICO
         * JavaScript: confirm() con messaggio personalizzato
         * Include nome utente per evitare eliminazioni sbagliate
         */
        const confirmed = confirm(`ATTENZIONE: Eliminare l'utente "${userName}"?\n\nQuesta azione non può essere annullata.`);
        
        if (confirmed) {
            /**
             * FEEDBACK VISIVO DURANTE ELIMINAZIONE
             * Bootstrap: .table-warning colora riga in giallo
             * Mostra spinner nell'ultima colonna per indicare elaborazione
             */
            userRow.addClass('table-warning');
            userRow.find('td').last().html('<i class="bi bi-hourglass-split"></i> Eliminazione...');
            return true; // Procede con submit normale
        } else {
            /**
             * ANNULLAMENTO AZIONE
             * jQuery: e.preventDefault() blocca submit del form
             * return false garantisce blocco completo
             */
            e.preventDefault();
            return false;
        }
    });
    
    // ===================================================================
    // SEZIONE: SCORCIATOIE DA TASTIERA
    // ===================================================================
    
    /**
     * EVENT HANDLER: Gestione tasti globali
     * jQuery: $(document) per catturare eventi su tutto il documento
     * Implementa scorciatoie per navigazione rapida
     */
    $(document).on('keydown', function(e) {
        /**
         * SCORCIATOIA: Ctrl+N per nuovo utente
         * JavaScript: e.ctrlKey verifica se Ctrl è premuto
         * e.key === 'n' verifica carattere specifico
         */
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault(); // Impedisce "nuova finestra" del browser
            /**
             * REDIRECT PROGRAMMATO
             * Blade Laravel: {{ route() }} genera URL corretto
             * window.location.href per navigazione JavaScript
             */
            window.location.href = "{{ route('admin.users.create') }}";
        }
        
        /**
         * SCORCIATOIA: Escape per pulire ricerca
         * Comportamento UX comune: Esc per "reset" stato
         */
        if (e.key === 'Escape') {
            /**
             * jQuery: .val('') svuota campo ricerca
             * .trigger('input') simula digitazione per attivare debouncing
             */
            $('#search').val('').trigger('input');
        }
    });
    
    // ===================================================================
    // SEZIONE: MIGLIORAMENTI UX
    // ===================================================================
    
    /**
     * INIZIALIZZAZIONE TOOLTIP BOOTSTRAP
     * jQuery: [title] selettore attributo per elementi con title
     * Bootstrap: .tooltip() inizializza tooltip su hover
     */
    $('[title]').tooltip();
    
    // ===================================================================
    // SEZIONE: FUNZIONI HELPER
    // ===================================================================
    
    /**
     * FUNZIONE: Sistema notifiche toast
     * Utility per mostrare messaggi temporanei all'utente
     * @param {string} message - Testo del messaggio
     * @param {string} type - Tipo notifica ('success' o 'danger')
     */
    function showNotification(message, type = 'success') {
        /**
         * MAPPING TIPO ALERT
         * JavaScript: operatore ternario per selezionare classe CSS
         * Bootstrap: alert-success (verde) o alert-danger (rosso)
         */
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        
        /**
         * CREAZIONE ELEMENTO NOTIFICA
         * Template literals per HTML complesso con variabili
         * CSS: position-fixed per overlay, z-index alto per visibilità
         */
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        /**
         * VISUALIZZAZIONE E AUTO-RIMOZIONE
         * jQuery: .append() aggiunge al DOM, setTimeout() per timing
         * Bootstrap: .alert('close') gestisce animazione e pulizia
         */
        $('body').append(alert);
        setTimeout(() => alert.alert('close'), 5000); // Auto-dismiss dopo 5 secondi
    }
    
    // ===================================================================
    // SEZIONE: LOG E STATISTICHE FINALI
    // ===================================================================
    
    // Log finale di conferma inizializzazione
    console.log('Gestione utenti inizializzata - versione senza selezione multipla');
    
    /**
     * LOG STATISTICHE CARICAMENTO
     * JavaScript: optional chaining (?.) per accesso sicuro
     * ?? operatore nullish coalescing per valore default
     * Laravel può popolare usersTotal tramite window.PageData
     */
    console.log('Utenti caricati:', window.PageData?.usersTotal ?? 0);
});