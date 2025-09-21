/*
 * LINGUAGGIO: JavaScript (ES6+) con jQuery
 * TIPO FILE: Script lato client per gestione assegnazioni prodotti
 * DESCRIZIONE: Gestisce l'interfaccia dinamica per assegnare prodotti ai membri dello staff
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Modal per assegnazione singola prodotto
 * - Filtri dinamici con auto-submit
 * - Validazione form con conferme utente
 * - Gestione eventi keyboard shortcuts
 * - Alert temporanei per feedback
 * - Integrazione con Laravel e Bootstrap
 * 
 * DIPENDENZE:
 * - jQuery 3.x (manipolazione DOM e eventi)
 * - Bootstrap 5.x (modal, alert, componenti UI)
 * - Laravel (routing, variabili globali)
 */

/**
 * DOCUMENT READY: Punto di ingresso principale
 * 
 * LINGUAGGIO: jQuery
 * 
 * SPIEGAZIONE:
 * - $(document).ready() aspetta che il DOM sia completamente caricato
 * - È l'equivalente jQuery di window.addEventListener('DOMContentLoaded')
 * - Garantisce che tutti gli elementi HTML siano disponibili prima di manipolarli
 * - Diverso da window.onload che aspetta anche immagini e risorse esterne
 */
$(document).ready(function() {
    // === LOGGING E VERIFICA CONTESTO ===
    
    /**
     * LOG di inizializzazione per debugging
     * 
     * LINGUAGGIO: JavaScript
     * 
     * console.log() invia messaggi alla console del browser (F12)
     * Utile per debug e verifica che lo script si sia caricato correttamente
     */
    console.log('Admin assegnazioni index caricato');
    
    /**
     * VERIFICA ROUTE corrente per security
     * 
     * LINGUAGGIO: JavaScript + Laravel integration
     * 
     * SPIEGAZIONE:
     * - window.LaravelApp è una variabile globale impostata da Laravel
     * - ?. è optional chaining (ES2020): evita errori se LaravelApp è undefined
     * - || '' è nullish coalescing: usa stringa vuota se route è null/undefined
     * - Verifica che lo script stia girando nella pagina corretta
     * - Se route non corrisponde, esce subito (return) per evitare interferenze
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.assegnazioni.index') {
        return; // Esce se non siamo nella pagina corretta
    }
    
    /**
     * INIZIALIZZAZIONE variabili globali
     * 
     * LINGUAGGIO: JavaScript + Laravel
     * 
     * SPIEGAZIONE:
     * - window.PageData è impostata da Laravel nella vista Blade
     * - || {} è fallback: usa oggetto vuoto se PageData non esiste
     * - selectedProducts array per tenere traccia di selezioni multiple (futuro)
     * - const per dati immutabili, let per variabili che cambiano
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per gestire selezioni multiple
    
    // ================================================
    // SEZIONE GESTIONE MODAL ASSEGNAZIONE SINGOLA
    // ================================================
    
    /**
     * GESTORE CLICK: Pulsanti di assegnazione
     * 
     * LINGUAGGIO: jQuery Event Handling
     * 
     * SPIEGAZIONE:
     * - $('.assign-btn') seleziona tutti gli elementi con classe "assign-btn"
     * - .on('click', function()) registra un event listener per click
     * - $(this) si riferisce all'elemento cliccato specifico
     * - .data('attribute') legge attributi HTML5 data-* dall'elemento
     * 
     * ESEMPIO HTML ATTESO:
     * <button class="assign-btn" 
     *         data-product-id="123" 
     *         data-product-name="Lavatrice ABC"
     *         data-current-staff="5">
     *   Assegna
     * </button>
     */
    $('.assign-btn').on('click', function() {
        /**
         * ESTRAZIONE dati dal pulsante cliccato
         * 
         * LINGUAGGIO: jQuery + HTML5 Data Attributes
         * 
         * SPIEGAZIONE:
         * - $(this) è l'elemento button cliccato
         * - .data('product-id') legge l'attributo data-product-id
         * - HTML data-product-id diventa 'product-id' in jQuery
         * - I dati vengono passati dal server (Laravel) tramite attributi HTML
         */
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const currentStaff = $(this).data('current-staff');
        
        /**
         * POPOLAMENTO del modal con i dati estratti
         * 
         * LINGUAGGIO: jQuery DOM Manipulation
         * 
         * SPIEGAZIONE:
         * - $('#assign-product-id') seleziona elemento con ID "assign-product-id"
         * - .val() imposta il valore di input fields
         * - .text() imposta il contenuto testuale di elementi
         * - || '' fornisce stringa vuota se currentStaff è null/undefined
         * 
         * FLUSSO:
         * 1. L'utente clicca "Assegna" su un prodotto
         * 2. I dati del prodotto vengono estratti dal pulsante
         * 3. Il modal viene popolato con questi dati
         * 4. Il modal si apre (gestito da Bootstrap automaticamente)
         */
        $('#assign-product-id').val(productId);        // Hidden input con ID prodotto
        $('#assign-product-name').text(productName);   // Span che mostra nome prodotto
        $('#assign-staff-id').val(currentStaff || ''); // Select dello staff corrente
        
        // LOG per debugging
        console.log('Modal assegnazione aperto per prodotto:', productName);
    });
    
    // ================================================
    // SEZIONE FILTRI DINAMICI
    // ================================================
    
    /**
     * AUTO-SUBMIT filtri dropdown
     * 
     * LINGUAGGIO: jQuery Event Handling
     * 
     * SPIEGAZIONE:
     * - '#staff_id, #categoria' seleziona entrambi i dropdown (selettore multiplo)
     * - .on('change') si attiva quando l'utente cambia la selezione
     * - $(this).closest('form') trova il form parent più vicino
     * - .submit() invia il form automaticamente
     * 
     * UX MIGLIORATA:
     * - Senza auto-submit: utente deve cliccare "Applica Filtri"
     * - Con auto-submit: filtri si applicano immediatamente
     * - Risposta più reattiva e moderna dell'interfaccia
     */
    $('#staff_id, #categoria').on('change', function() {
        $(this).closest('form').submit();
    });
    
    /**
     * GESTIONE checkbox "Solo prodotti non assegnati"
     * 
     * LINGUAGGIO: jQuery + Logic Handling
     * 
     * SPIEGAZIONE:
     * - .on('change') si attiva quando checkbox cambia stato
     * - .is(':checked') verifica se checkbox è selezionato
     * - Logica: se checkbox attivo → filtra per non assegnati
     * - Se checkbox disattivo → resetta filtro
     * 
     * LOGICA DI BUSINESS:
     * - "null" nel select significa "prodotti non assegnati"
     * - "" (stringa vuota) nel select significa "tutti i prodotti"
     * - Il form viene inviato automaticamente per applicare filtri
     */
    $('#non_assegnati').on('change', function() {
        if ($(this).is(':checked')) {
            // Checkbox selezionato: mostra solo prodotti non assegnati
            $('#staff_id').val('null');
        } else {
            // Checkbox deselezionato: mostra tutti i prodotti
            $('#staff_id').val('');
        }
        // Applica automaticamente i filtri inviando il form
        $(this).closest('form').submit();
    });
    
    // ================================================
    // SEZIONE VALIDAZIONE FORM
    // ================================================
    
    /**
     * VALIDAZIONE form di assegnazione con conferma utente
     * 
     * LINGUAGGIO: jQuery Form Validation + JavaScript Confirm
     * 
     * SPIEGAZIONE:
     * - $('form[action*="assegna.prodotto"]') seleziona form con URL contenente "assegna.prodotto"
     * - .on('submit') intercetta l'invio del form PRIMA che venga inviato
     * - e.preventDefault() blocca l'invio se validazione fallisce
     * - confirm() mostra dialog browser nativo con OK/Annulla
     * 
     * FLUSSO VALIDAZIONE:
     * 1. Utente clicca "Salva" nel modal
     * 2. Event handler intercetta submit
     * 3. Estrae dati dal form
     * 4. Mostra messaggio di conferma appropriato
     * 5. Se utente conferma: form viene inviato
     * 6. Se utente annulla: form non viene inviato
     */
    $('form[action*="assegna.prodotto"]').on('submit', function(e) {
        /**
         * ESTRAZIONE dati dal form per validazione
         * 
         * LINGUAGGIO: jQuery Form Data Extraction
         * 
         * SPIEGAZIONE:
         * - $(this) è il form che sta per essere inviato
         * - .find() cerca elementi figli che corrispondono al selettore
         * - .val() ottiene il valore del campo select
         * - .text() ottiene il contenuto testuale dell'elemento
         */
        const staffId = $(this).find('select[name="staff_id"]').val();
        const productName = $('#assign-product-name').text();
        
        /**
         * LOGICA di conferma basata su azione
         * 
         * LINGUAGGIO: JavaScript Conditional Logic
         * 
         * SPIEGAZIONE:
         * - Se staffId ha valore: viene assegnato un prodotto
         * - Se staffId è vuoto: viene rimossa un'assegnazione
         * - :selected pseudo-selector jQuery per opzione selezionata
         * - Template literals (``) per costruire messaggi dinamici
         */
        if (staffId) {
            // ASSEGNAZIONE: un prodotto viene assegnato a qualcuno
            const staffName = $(this).find('select[name="staff_id"] option:selected').text();
            const message = `Confermi l'assegnazione del prodotto "${productName}" a ${staffName}?`;
            
            if (!confirm(message)) {
                e.preventDefault(); // Blocca l'invio del form
                return false;       // Esce dalla funzione
            }
        } else {
            // RIMOZIONE: un'assegnazione viene rimossa
            const message = `Confermi la rimozione dell'assegnazione per "${productName}"?`;
            
            if (!confirm(message)) {
                e.preventDefault(); // Blocca l'invio del form
                return false;       // Esce dalla funzione
            }
        }
        
        // Se arriviamo qui, l'utente ha confermato e il form viene inviato normalmente
    });
    
    // ================================================
    // SEZIONE FUNZIONI HELPER
    // ================================================
    
    /**
     * FUNZIONE HELPER: Mostra alert temporanei
     * 
     * LINGUAGGIO: JavaScript Function + jQuery DOM Creation
     * 
     * @param {string} type - Tipo di alert ('success', 'warning', 'error', 'info')
     * @param {string} message - Messaggio da mostrare all'utente
     * 
     * SPIEGAZIONE:
     * - Funzione riutilizzabile per feedback utente
     * - Crea dinamicamente elementi Bootstrap alert
     * - Position fixed per overlay sopra contenuto
     * - Auto-dismiss dopo timeout
     * - Integrazione con Bootstrap Icons
     */
    function showAlert(type, message) {
        /**
         * MAPPING tipo alert → classe CSS Bootstrap
         * 
         * LINGUAGGIO: JavaScript Ternary Operators (Conditional Chain)
         * 
         * SPIEGAZIONE:
         * - Ternary operator: condizione ? valore_se_vero : valore_se_falso
         * - Chain di ternary per gestire multiple condizioni
         * - Mappa tipi custom a classi CSS Bootstrap standard
         */
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        /**
         * MAPPING tipo alert → icona Bootstrap Icons
         * 
         * LINGUAGGIO: JavaScript Ternary Operators
         * 
         * SPIEGAZIONE:
         * - Stesso pattern per icone appropriate al tipo di messaggio
         * - Bootstrap Icons (bi-*) per feedback visivo
         */
        const icon = type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 
                    type === 'success' ? 'check-circle' : 'info-circle';
        
        /**
         * CREAZIONE dinamica elemento alert
         * 
         * LINGUAGGIO: jQuery DOM Creation + Template Literals
         * 
         * SPIEGAZIONE:
         * - $(`template`) crea elementi DOM da stringa HTML
         * - Template literals (``) permettono interpolazione variabili
         * - position-fixed con z-index alto per overlay
         * - Bootstrap classes per styling automatico
         * - data-bs-dismiss per auto-close integrato
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
         * INSERIMENTO e AUTO-REMOVAL dell'alert
         * 
         * LINGUAGGIO: jQuery DOM Manipulation + JavaScript setTimeout
         * 
         * SPIEGAZIONE:
         * - $('body').append() aggiunge alert alla fine del body
         * - setTimeout() programma esecuzione dopo delay
         * - .fadeOut() animazione jQuery di dissolvenza
         * - .remove() elimina elemento dal DOM dopo animazione
         * - Arrow function (=>) per callback conciso
         */
        $('body').append(alert);
        
        // Auto-rimozione dopo 5 secondi
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
    
    // ================================================
    // SEZIONE KEYBOARD SHORTCUTS
    // ================================================
    
    /**
     * GESTIONE scorciatoie da tastiera globali
     * 
     * LINGUAGGIO: jQuery Global Event Handling
     * 
     * SPIEGAZIONE:
     * - $(document).on('keydown') cattura eventi tastiera su tutto il documento
     * - e.key contiene il nome del tasto premuto (standard Web API)
     * - .modal('hide') è metodo Bootstrap per chiudere modal
     * - .modal.show seleziona solo modal attualmente aperti
     * 
     * SHORTCUTS IMPLEMENTATE:
     * - Escape: chiude modal aperti (UX standard)
     * - F5: ricarica pagina (comportamento browser standard)
     * - Ctrl+F: cerca (comportamento browser standard)
     */
    $(document).on('keydown', function(e) {
        /**
         * ESCAPE per chiudere modal
         * 
         * LINGUAGGIO: JavaScript Event Handling + Bootstrap Modal API
         * 
         * SPIEGAZIONE:
         * - 'Escape' è il valore standardizzato per il tasto ESC
         * - $('.modal.show') seleziona solo modal attualmente visibili
         * - .modal('hide') invoca il metodo Bootstrap per chiusura animata
         */
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
        
        // F5 e Ctrl+F hanno comportamento browser standard (non sovrascriviamo)
    });
    
    // ================================================
    // SEZIONE INIZIALIZZAZIONE E LOGGING
    // ================================================
    
    /**
     * LOGGING di stato iniziale per debugging
     * 
     * LINGUAGGIO: JavaScript + jQuery Length Property
     * 
     * SPIEGAZIONE:
     * - console.log() per debug dell'stato iniziale
     * - .length restituisce numero di elementi trovati da selettore
     * - -2 perché select include opzioni "Tutti" e "Non Assegnati"
     * - Template literals per interpolazione nel log
     * - Commentato {{ }} è sintassi Blade che sarebbe processata dal server
     */
    console.log('Gestione assegnazioni inizializzata');
    console.log(`Staff disponibili: ${$('#staff_id option').length - 2}`);
    console.log(`Prodotti totali: {{ $prodotti->total() ?? 0 }}`); // Blade syntax per totale
    
    /**
     * EVIDENZIAZIONE campo di ricerca se utilizzato
     * 
     * LINGUAGGIO: jQuery Conditional Styling
     * 
     * SPIEGAZIONE:
     * - $('#search').val() ottiene valore corrente del campo ricerca
     * - Se campo ha valore (ricerca attiva): aggiunge bordo blu
     * - .addClass() aggiunge classe CSS senza rimuovere esistenti
     * - Visual feedback che filtri sono attivi
     */
    if ($('#search').val()) {
        $('#search').addClass('border-primary');
    }
    
    // Fine del document.ready()
});

/*
 * RIEPILOGO FUNZIONALITÀ SCRIPT:
 * 
 * 1. GESTIONE MODAL:
 *    - Apertura con dati prodotto precompilati
 *    - Estrazione dati da attributi HTML5 data-*
 *    - Popolazione automatica campi form
 * 
 * 2. FILTRI DINAMICI:
 *    - Auto-submit su cambio dropdown
 *    - Checkbox specializzato per "non assegnati"
 *    - Miglioramento UX con aggiornamento immediato
 * 
 * 3. VALIDAZIONE E CONFERMA:
 *    - Intercepting form submit per validazione
 *    - Messaggi di conferma context-aware
 *    - Prevenzione invii accidentali
 * 
 * 4. FEEDBACK UTENTE:
 *    - Alert temporanei con auto-dismiss
 *    - Styling Bootstrap integrato
 *    - Icone appropriate per tipo messaggio
 * 
 * 5. KEYBOARD SHORTCUTS:
 *    - Escape per chiudere modal
 *    - Rispetto standard browser per altre shortcuts
 * 
 * 6. DEBUGGING E LOGGING:
 *    - Console logs per stato applicazione
 *    - Verifica route per sicurezza
 *    - Conteggi elementi per debug
 * 
 * INTEGRAZIONE TECNOLOGIE:
 * - jQuery: manipolazione DOM e eventi
 * - Bootstrap: componenti UI (modal, alert, form)
 * - Laravel: routing, dati server, integrazione Blade
 * - HTML5: data attributes per passaggio dati
 * - ES6+: arrow functions, template literals, optional chaining
 * 
 * PATTERN UTILIZZATI:
 * - Event delegation per performance
 * - Defensive programming (controlli null/undefined)
 * - Progressive enhancement (fallback se JS disabilitato)
 * - Separation of concerns (UI logic separata da business logic)
 */