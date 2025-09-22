/**
 * JAVASCRIPT PER VISTA DETTAGLI UTENTE - AMMINISTRAZIONE
 * 
 * Linguaggio: JavaScript + jQuery + AJAX
 * Framework: jQuery 3.x per manipolazione DOM, Bootstrap per UI
 * Scopo: Gestione della pagina di dettagli utente con azioni avanzate
 * 
 * Funzionalità principali:
 * - Reset password AJAX con visualizzazione sicura password temporanea
 * - Eliminazione utente con doppia conferma di sicurezza
 * - Gestione errori immagini con fallback
 * - Scorciatoie da tastiera per navigazione rapida
 * - Sistema notifiche avanzato con auto-dismiss
 * - Tooltip informativi per tutti gli elementi
 */

/**
 * EVENTO PRINCIPALE JQUERY - DOCUMENT READY
 * Si attiva quando il DOM è completamente caricato
 * jQuery: $(document).ready() esegue il codice quando la pagina è pronta
 */
$(document).ready(function() {
    // Log di debug in console browser
    console.log('admin.users.show caricato');
    
    /**
     * CONTROLLO ROUTE CORRENTE
     * Verifica se siamo nella pagina giusta tramite variabile globale Laravel
     * Laravel espone dati in window.LaravelApp tramite helper JavaScript
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.users.show') {
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
    console.log('Vista dettagli utente inizializzata - senza funzione sospendi');
    
    // ===================================================================
    // SEZIONE: RESET PASSWORD CON AJAX AVANZATO
    // ===================================================================
    
    /**
     * EVENT HANDLER: Reset password via AJAX
     * jQuery: selettore attributo [action*="text"] per form contenenti "reset-password"
     * Implementazione AJAX completa con UI ricca per gestione password temporanea
     */
    $('form[action*="reset-password"]').on('submit', function(e) {
        /**
         * jQuery: e.preventDefault() impedisce submit normale del form
         * Intercettiamo per gestire con AJAX invece del submit tradizionale
         */
        e.preventDefault();
        
        /**
         * RIFERIMENTI DOM PER MANIPOLAZIONE UI
         * jQuery: $(this) form corrente, .find() cerca elementi figli
         * Blade Laravel: {{ $user->nome_completo }} interpola nome utente
         */
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.text(); // Salva testo originale pulsante
        const userName = '{{ $user->nome_completo }}'; // Nome utente da Laravel
        
        /**
         * DIALOGO CONFERMA DETTAGLIATO
         * JavaScript: confirm() con messaggio informativo completo
         * Include istruzioni per comunicare password all'utente
         */
        if (!confirm(`Resettare la password per ${userName}?\n\nVerrà generata una password temporanea che dovrà essere comunicata all'utente.`)) {
            return; // Esce se utente annulla
        }
        
        /**
         * UI FEEDBACK: Mostra stato di caricamento
         * jQuery: .prop('disabled', true) disabilita pulsante
         * .html() cambia contenuto con spinner Bootstrap e testo
         */
        button.prop('disabled', true)
              .html('<i class="bi bi-hourglass-split me-1"></i>Elaborazione...');
        
        /**
         * CHIAMATA AJAX PRINCIPALE
         * jQuery: $.ajax() per richiesta asincrona completa
         * Gestione success/error/complete per UX professionale
         */
        $.ajax({
            url: form.attr('action'),    // URL dal form action
            method: 'POST',              // Metodo HTTP POST
            data: form.serialize(),      // Serializza form incluso CSRF token
            
            /**
             * SUCCESS CALLBACK: Eseguito per risposta HTTP 200
             * @param {Object} response - Risposta JSON dal server Laravel
             */
            success: function(response) {
                if (response.success) {
                    /**
                     * ALERT RICCO PER PASSWORD TEMPORANEA
                     * Template literals con HTML complesso per UI professionale
                     * Include: icone, styling, pulsante copia con feedback, istruzioni
                     */
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999; min-width: 450px; max-width: 500px;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill me-2 fs-5 flex-shrink-0 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">Password Resetata con Successo</h6>
                                    <p class="mb-2">${response.message}</p>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong>Password Temporanea:</strong><br>
                                            <code class="bg-light p-2 rounded d-inline-block mt-1" style="font-size: 1.1em; letter-spacing: 1px;">${response.temp_password}</code>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" 
                                                onclick="navigator.clipboard.writeText('${response.temp_password}').then(() => { this.innerHTML='<i class=\\'bi bi-check\\' ></i> Copiato!'; setTimeout(() => { this.innerHTML='<i class=\\'bi bi-clipboard\\'></i> Copia'; }, 2000); })">
                                            <i class="bi bi-clipboard"></i> Copia
                                        </button>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Comunica questa password all'utente. Scadrà al primo login.
                                    </small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    `;
                    
                    /**
                     * jQuery: .append() aggiunge alert al body
                     * Mostra notifica ricca nell'angolo superiore destro
                     */
                    $('body').append(alertHtml);
                    
                    /**
                     * AUTO-DISMISS ESTESO PER PASSWORD SENSIBILI
                     * JavaScript: setTimeout() con 30 secondi (vs 5 per notifiche normali)
                     * jQuery: .fadeOut() animazione scomparsa, .remove() pulizia DOM
                     * Tempo esteso perché password deve essere copiata/annotata
                     */
                    setTimeout(() => {
                        $('.alert').fadeOut(500, function() { $(this).remove(); });
                    }, 30000); // 30 secondi per password sensibili
                } else {
                    // Usa helper function per errori dal server
                    showNotification(response.message, 'danger');
                }
            },
            
            /**
             * ERROR CALLBACK: Gestione errori HTTP/network
             * @param {Object} xhr - XMLHttpRequest object
             * @param {string} status - Stato errore
             * @param {string} error - Messaggio errore
             */
            error: function(xhr, status, error) {
                console.error('Errore reset password:', error); // Log per debug
                showNotification('Errore durante il reset della password. Riprova.', 'danger');
            },
            
            /**
             * COMPLETE CALLBACK: Eseguito sempre (success O error)
             * Ripristina stato originale dell'interfaccia
             */
            complete: function() {
                // Ripristina pulsante allo stato originale
                button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ===================================================================
    // SEZIONE: ELIMINAZIONE UTENTE CON DOPPIA SICUREZZA
    // ===================================================================
    
    /**
     * EVENT HANDLER: Eliminazione utente con conferme multiple
     * jQuery: selettore specifico per pulsanti elimina in form destroy
     * Implementa protocollo sicurezza per azione irreversibile
     */
    $('form[action*="destroy"] button[type="submit"]').on('click', function(e) {
        /**
         * RIFERIMENTI CONTEXT
         * jQuery: .closest() risale nella gerarchia DOM per trovare form
         * Blade Laravel: {{ }} per interpolare nome utente nel JavaScript
         */
        const form = $(this).closest('form');
        const userName = '{{ $user->nome_completo }}'; // Nome da Laravel
        
        /**
         * PRIMA CONFERMA: Informativa dettagliata
         * JavaScript: confirm() con elenco conseguenze eliminazione
         * Informa su tutti i dati che verranno persi
         */
        const firstConfirm = confirm(`ATTENZIONE: Stai per eliminare l'utente "${userName}".\n\nQuesta azione rimuoverà:\n- L'account utente\n- Tutti i dati associati\n- Le assegnazioni prodotti (se staff)\n- I collegamenti al centro assistenza (se tecnico)\n\nVuoi continuare?`);
        
        /**
         * BLOCCO SE PRIMA CONFERMA NEGATA
         * jQuery: e.preventDefault() impedisce submit del form
         * return false garantisce stop completo dell'esecuzione
         */
        if (!firstConfirm) {
            e.preventDefault();
            return false;
        }
        
        /**
         * SECONDA CONFERMA: Conferma finale di sicurezza
         * Doppio controllo per azioni irreversibili critiche
         * Pattern UX per prevenire eliminazioni accidentali
         */
        const finalConfirm = confirm(`CONFERMA FINALE: Eliminare definitivamente "${userName}"?\n\nQuesta azione NON PUÒ essere annullata.`);
        
        /**
         * BLOCCO SE SECONDA CONFERMA NEGATA
         * Stesso pattern della prima conferma
         */
        if (!finalConfirm) {
            e.preventDefault();
            return false;
        }
        
        /**
         * FEEDBACK VISIVO DURANTE ELIMINAZIONE
         * Solo se entrambe le conferme sono positive
         * jQuery: .html() cambia testo pulsante, .prop() disabilita
         */
        const button = $(this);
        button.html('<i class="bi bi-hourglass-split me-1"></i>Eliminazione...')
              .prop('disabled', true);
              
        return true; // Procede con submit normale del form
    });
    
    // ===================================================================
    // SEZIONE: MIGLIORAMENTI UX
    // ===================================================================
    
    /**
     * INIZIALIZZAZIONE TOOLTIP BOOTSTRAP
     * jQuery: [title] selettore attributo per elementi con attributo title
     * Bootstrap: .tooltip() inizializza tooltip su hover per tutti gli elementi
     */
    $('[title]').tooltip();
    
    /**
     * GESTIONE FALLBACK IMMAGINI
     * jQuery: .on('error') cattura errore caricamento immagine
     * Mostra placeholder quando immagine prodotto non può essere caricata
     */
    $('img[alt]').on('error', function() {
        /**
         * jQuery: $(this) immagine che ha fallito il caricamento
         * .hide() nasconde immagine rotta
         * .next() seleziona elemento fratello successivo (placeholder)
         * Bootstrap: .d-none/.d-flex per controllo visibilità
         */
        $(this).hide().next('.bg-light').removeClass('d-none').addClass('d-flex');
    });
    
    // ===================================================================
    // SEZIONE: FUNZIONI HELPER
    // ===================================================================
    
    /**
     * FUNZIONE: Sistema notifiche toast avanzato
     * Utility per mostrare messaggi con icone e stili differenziati
     * @param {string} message - Testo del messaggio
     * @param {string} type - Tipo notifica ('success' o 'danger')
     */
    function showNotification(message, type = 'success') {
        /**
         * MAPPING CLASSI E ICONE PER TIPO
         * JavaScript: operatore ternario per selezione condizionale
         * Bootstrap: classi alert e Bootstrap Icons per stili
         */
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        
        /**
         * CREAZIONE ELEMENTO NOTIFICA AVANZATA
         * Template literals per HTML complesso con layout flex
         * CSS: position-fixed per overlay, dimensioni responsive
         */
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
                <div class="d-flex align-items-center">
                    <i class="bi ${iconClass} me-2 fs-5"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `);
        
        /**
         * VISUALIZZAZIONE E AUTO-RIMOZIONE
         * jQuery: .append() aggiunge al DOM
         * setTimeout() + fadeOut() per rimozione graduale automatica
         */
        $('body').append(alert);
        
        // Auto-rimuovi dopo 5 secondi con animazione
        setTimeout(() => alert.fadeOut(300, () => alert.remove()), 5000);
    }
    
    // ===================================================================
    // SEZIONE: SCORCIATOIE DA TASTIERA
    // ===================================================================
    
    /**
     * EVENT HANDLER: Scorciatoie globali da tastiera
     * jQuery: $(document) per catturare eventi su tutto il documento
     * Supporta sia Ctrl (Windows/Linux) che Cmd (Mac) con metaKey
     */
    $(document).on('keydown', function(e) {
        /**
         * SCORCIATOIA: Ctrl+E / Cmd+E per modificare utente
         * JavaScript: || operatore OR per Ctrl o Cmd
         * window.canEditUser variabile globale da Laravel per permessi
         * window.editUserUrl URL generato da Laravel per link modifica
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 'e' && window.canEditUser) {
            e.preventDefault(); // Impedisce comportamento default browser
            window.location.href = window.editUserUrl; // Redirect alla modifica
        }
        
        /**
         * SCORCIATOIA: Ctrl+Backspace / Cmd+Backspace per tornare alla lista
         * Navigazione rapida verso lista utenti
         * window.usersIndexUrl URL generato da Laravel
         */
        if ((e.ctrlKey || e.metaKey) && e.key === 'Backspace') {
            e.preventDefault(); // Impedisce "indietro" del browser
            window.location.href = window.usersIndexUrl; // Redirect alla lista
        }
    });
    
    // ===================================================================
    // SEZIONE: DEBUG E LOGGING
    // ===================================================================
    
    /**
     * COMMENTO DOCUMENTAZIONE: Logging dati utente
     * 
     * Per loggare dati utente, devono essere passati dalla view Blade 
     * tramite una variabile JavaScript globale, esempio in Blade:
     * 
     * <script>
     *   window.userData = @json($user);
     * </script>
     * 
     * Poi qui si può accedere con:
     * console.log('Dettagli utente caricati:', window.userData);
     * 
     * Questo pattern separa logica PHP (Blade) da JavaScript mantenendo
     * i dati sincronizzati e accessibili lato client.
     */
    
    // Log finale di conferma inizializzazione
    console.log('Vista dettagli utente inizializzata - funzionalità sospendi account rimossa');
});