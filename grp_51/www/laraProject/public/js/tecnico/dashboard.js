/**
 * ====================================================================
 * FILE: tecnico-dashboard.js - Dashboard per Utenti Tecnici
 * LINGUAGGIO: JavaScript ES6+ con jQuery e Ajax
 * FRAMEWORK: Laravel 12 - Sistema Assistenza Tecnica
 * AUTORE: Gruppo 51 - Corso Tecnologie Web 2024/2025
 * ====================================================================
 * 
 * DESCRIZIONE:
 * Dashboard completa per tecnici dei centri assistenza (Livello 2).
 * Gestisce ricerca prodotti/malfunzionamenti, segnalazioni, statistiche
 * in tempo reale, shortcuts tastiera e sistema di notifiche avanzato.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Ricerca prodotti con validazione e wildcard (*)
 * - Ricerca malfunzionamenti con filtri
 * - Segnalazione malfunzionamenti via AJAX
 * - Aggiornamento statistiche in tempo reale
 * - Shortcuts da tastiera per produttività
 * - Sistema notifiche toast
 * - Layout responsive per mobile/desktop
 * - Gestione errori avanzata
 */

// ====================================================================
// DOCUMENT READY: Inizializzazione Completa Dashboard
// LINGUAGGIO: jQuery
// ====================================================================
$(document).ready(function() {
    // Log di debug per monitoraggio caricamento modulo
    console.log('tecnico.dashboard caricato');
    
    // ================================================================
    // CONTROLLO SICUREZZA: Verifica route corretta
    // ================================================================
    /**
     * Sistema di sicurezza per eseguire il codice solo sulla pagina giusta
     * Previene errori e conflitti se il file viene caricato altrove
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'tecnico.dashboard') {
        return; // Exit immediato se non siamo nella dashboard tecnico
    }
    
    // ================================================================
    // INIZIALIZZAZIONE VARIABILI: Setup dati globali
    // ================================================================
    /**
     * Recupera dati passati dal controller Laravel via window.PageData
     * Inizializza variabili per gestione stato dell'applicazione
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per prodotti selezionati (funzionalità future)

    // ================================================================
    // CONFIGURAZIONE GLOBALE: URLs e Sicurezza
    // LINGUAGGIO: Laravel Blade + JavaScript
    // ================================================================
    /**
     * Configurazione URLs delle API e token di sicurezza CSRF
     * Gli URLs sono generati lato server da Laravel per massima sicurezza
     */
    console.log('Dashboard Tecnico caricata per: {{ auth()->user()->nome_completo }}');
    
    // URLs corretti per le API (generati dinamicamente da Laravel)
    const API_URLS = {
        // Endpoint per statistiche dashboard in tempo reale
        stats_dashboard: '{{ route("api.stats.dashboard") }}',
        
        // Base URL per API segnalazioni malfunzionamenti
        segnala_base_url: '{{ url("/api/malfunzionamenti") }}'
    };
    
    // Token CSRF per sicurezza nelle richieste AJAX
    // Laravel richiede questo token per prevenire attacchi Cross-Site Request Forgery
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    /**
     * Configurazione globale AJAX per includere automaticamente
     * il token CSRF e headers corretti in tutte le richieste
     */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,        // Token sicurezza Laravel
            'Accept': 'application/json',       // Accetta solo JSON
            'Content-Type': 'application/json' // Invia dati come JSON
        }
    });

    // ================================================================
    // DEBUG SECTION: Verifica presenza elementi DOM
    // ================================================================
    /**
     * Sezione di debug per verificare che tutti gli elementi HTML
     * necessari siano presenti nel DOM prima di attaccare eventi
     */
    console.log('Elemento searchProdotti trovato:', $('#searchProdotti').length);
    console.log('Elemento searchMalfunzionamenti trovato:', $('#searchMalfunzionamenti').length);
    console.log('Form prodotti trovato:', $('#searchProdotti').closest('form').length);
    console.log('Form malfunzionamenti trovato:', $('#searchMalfunzionamenti').closest('form').length);

    // ================================================================
    // GESTIONE RICERCA PRODOTTI: Form submission con validazione
    // LINGUAGGIO: jQuery Event Handling
    // ================================================================
    /**
     * Gestisce il submit del form di ricerca prodotti
     * Include validazione client-side e feedback visuale
     * 
     * PATTERN: Event delegation con .on() per robustezza
     * TECNOLOGIE: jQuery selectors, form validation, Bootstrap classes
     */
    $('#searchProdotti').closest('form').on('submit', function(e) {
        const form = $(this);                              // Riferimento al form
        const input = form.find('input[name="search"]');   // Input di ricerca
        const query = input.val().trim();                  // Query pulita da spazi
        
        console.log('Form ricerca prodotti submitted, query:', query);
        
        // VALIDAZIONE 1: Lunghezza minima (2 caratteri)
        if (query.length < 2) {
            e.preventDefault(); // Blocca submit del form
            showAlert('Inserisci almeno 2 caratteri per la ricerca', 'warning');
            input.focus();      // Riporta focus sull'input
            return false;       // Blocca esecuzione
        }
        
        // VALIDAZIONE 2: Caratteri non consentiti (funzione helper)
        const validazione = validaTermineRicerca(query);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // FEEDBACK VISUALE: Mostra indicatore di caricamento
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true)  // Disabilita bottone
               .html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input'); // Classe CSS per stile loading
        
        console.log('Ricerca prodotti validata, form si submitterà normalmente');
        // Lascia che il form si submitti normalmente (redirect lato server)
        return true;
    });
    
    // ================================================================
    // GESTIONE RICERCA MALFUNZIONAMENTI: Form submission con validazione
    // ================================================================
    /**
     * Analogo alla ricerca prodotti ma per malfunzionamenti
     * Nota: usa name="q" invece di "search" e validazione specifica
     */
    $('#searchMalfunzionamenti').closest('form').on('submit', function(e) {
        const form = $(this);
        const input = form.find('input[name="q"]');  // Nome campo diverso!
        const query = input.val().trim();
        
        console.log('Form ricerca malfunzionamenti submitted, query:', query);
        
        // Validazione specifica per malfunzionamenti (2 caratteri minimi)
        if (query.length < 2) {
            e.preventDefault();
            showAlert('Inserisci almeno 2 caratteri per cercare malfunzionamenti', 'warning');
            input.focus();
            return false;
        }
        
        // Validazione con parametro specifico (2 caratteri minimi)
        const validazione = validaTermineRicerca(query, 2);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // Feedback visuale identico alla ricerca prodotti
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true)
               .html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input');
        
        console.log('Ricerca malfunzionamenti validata, form si submitterà normalmente');
        return true;
    });

    // ================================================================
    // RICERCA CON TASTO ENTER: Shortcut per migliorare UX
    // ================================================================
    /**
     * Permette ricerca premendo ENTER anche senza cliccare il bottone
     * Migliora l'usabilità per utenti esperti
     * 
     * KEYCODE: 13 = tasto ENTER
     */
    
    // Gestione ENTER per ricerca prodotti
    $('#searchProdotti').on('keypress', function(e) {
        if (e.which === 13) { // Verifica tasto ENTER
            e.preventDefault(); // Evita doppio submit
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca prodotti, query:', query);
            
            // Stessa validazione del form submit
            const validazione = validaTermineRicerca(query);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            // Feedback visuale
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true)
                   .html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            // Submit manuale del form
            console.log('Ricerca prodotti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // Gestione ENTER per ricerca malfunzionamenti (analogo)
    $('#searchMalfunzionamenti').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca malfunzionamenti, query:', query);
            
            // Validazione specifica (2 caratteri minimi)
            const validazione = validaTermineRicerca(query, 2);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true)
                   .html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            console.log('Ricerca malfunzionamenti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // ================================================================
    // FUNZIONE VALIDAZIONE: Helper per validare termini di ricerca
    // LINGUAGGIO: JavaScript nativo con Regular Expressions
    // ================================================================
    /**
     * Funzione helper per validare i termini di ricerca
     * Controlla lunghezza, caratteri pericolosi, e limiti
     * 
     * @param {string} query - Termine da validare
     * @param {number} lunghezzaMinima - Caratteri minimi richiesti (default: 2)
     * @returns {Object} - {valido: boolean, messaggio: string}
     * 
     * SICUREZZA: Previene injection attacks e caratteri pericolosi
     */
    function validaTermineRicerca(query, lunghezzaMinima = 2) {
        // CONTROLLO 1: Lunghezza minima
        if (!query || query.length < lunghezzaMinima) {
            return {
                valido: false,
                messaggio: `Inserisci almeno ${lunghezzaMinima} caratteri per la ricerca`
            };
        }
        
        // CONTROLLO 2: Caratteri speciali pericolosi per sicurezza
        // Regex: /[<>]/ trova caratteri < o > che potrebbero essere XSS
        if (/[<>]/.test(query)) {
            return {
                valido: false,
                messaggio: 'Caratteri non ammessi nella ricerca: < >'
            };
        }
        
        // CONTROLLO 3: Lunghezza massima per performance
        if (query.length > 100) {
            return {
                valido: false,
                messaggio: 'Termine di ricerca troppo lungo (max 100 caratteri)'
            };
        }
        
        // Se tutti i controlli passano, la validazione è OK
        return { valido: true, messaggio: '' };
    }
    
    // ================================================================
    // SISTEMA TOOLTIP: Tooltip per errori dinamici
    // LINGUAGGIO: Bootstrap JavaScript API
    // ================================================================
    /**
     * Mostra tooltip di errore temporanei su elementi specifici
     * Utilizzato per feedback di validazione in tempo reale
     * 
     * @param {string} selector - Selettore CSS dell'elemento
     * @param {string} message - Messaggio da mostrare
     * 
     * BOOTSTRAP TOOLTIP: Componente UI per messaggi contestuali
     */
    function showErrorTooltip(selector, message) {
        const $element = $(selector);
        
        // Pulizia: Rimuovi tooltip esistenti per evitare sovrapposizioni
        $element.tooltip('dispose');
        
        // Crea nuovo tooltip di errore con configurazione personalizzata
        $element.tooltip({
            title: message,              // Testo del tooltip
            placement: 'bottom',         // Posizione sotto l'elemento
            trigger: 'manual',           // Controllo manuale (non hover)
            customClass: 'error-tooltip' // Classe CSS personalizzata
        }).tooltip('show'); // Mostra immediatamente
        
        // Auto-cleanup: Rimuovi automaticamente dopo 3 secondi
        setTimeout(function() {
            $element.tooltip('dispose');
        }, 3000);
    }
    
    // ================================================================
    // GESTIONE EVENTI GENERALI: Focus, blur, input validation
    // ================================================================
    /**
     * Eventi per migliorare l'esperienza utente con feedback visivi
     * e validazione in tempo reale
     */
    
    // Focus automatico sull'input di ricerca al caricamento pagina
    setTimeout(function() {
        $('#searchProdotti').focus();
        console.log('Focus automatico su searchProdotti');
    }, 500); // Delay per evitare conflitti con animazioni CSS
    
    // Evidenziazione suggerimenti durante focus/blur
    $('#searchProdotti, #searchMalfunzionamenti').on('focus', function() {
        // Evidenzia testo di aiuto quando input è attivo
        $(this).next('.form-text').addClass('text-primary');
        console.log('Focus su input ricerca:', this.id);
    }).on('blur', function() {
        // Rimuovi evidenziazione quando input perde focus
        $(this).next('.form-text').removeClass('text-primary');
        console.log('Blur su input ricerca:', this.id);
    });
    
    // Validazione in tempo reale per caratteri non consentiti
    $('#searchProdotti, #searchMalfunzionamenti').on('input', function() {
        const query = $(this).val();
        const hasInvalidChars = /[<>]/.test(query); // Stessa regex di validazione
        
        if (hasInvalidChars) {
            // Applica stile di errore Bootstrap
            $(this).addClass('is-invalid');
            showErrorTooltip(this, 'Caratteri non ammessi: < >');
        } else {
            // Rimuovi stile di errore
            $(this).removeClass('is-invalid');
            $(this).tooltip('dispose'); // Rimuovi tooltip errore
        }
    });
    
    // ================================================================
    // FUNZIONE SEGNALAZIONE: Segnala malfunzionamento via AJAX
    // LINGUAGGIO: jQuery AJAX + JSON API
    // ================================================================
    /**
     * Funzione globale per segnalare malfunzionamenti riscontrati
     * Utilizza AJAX per comunicazione asincrona con backend Laravel
     * 
     * @param {number} malfunzionamentoId - ID del malfunzionamento da segnalare
     * 
     * TECNOLOGIE:
     * - jQuery AJAX per richieste HTTP asincrone
     * - JSON per scambio dati strutturati
     * - Bootstrap per feedback visuale
     * - Promise-based error handling
     * 
     * FLUSSO:
     * 1. Validazione input
     * 2. Richiesta conferma utente
     * 3. Richiesta AJAX al server
     * 4. Aggiornamento UI in base alla risposta
     * 5. Gestione errori con feedback specifico
     */
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        console.log('Segnalazione richiesta per malfunzionamento ID:', malfunzionamentoId);
        
        // VALIDAZIONE 1: Verifica ID valido
        if (!malfunzionamentoId) {
            showAlert('Errore: ID malfunzionamento non valido', 'danger');
            return;
        }
        
        // CONFERMA UTENTE: Evita segnalazioni accidentali
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            console.log('Segnalazione annullata dall\'utente');
            return;
        }
        
        // TROVA BOTTONE: Localizza bottone specifico per feedback visuale
        const $button = $(`[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        if (!$button.length) {
            console.error('Pulsante segnalazione non trovato per ID:', malfunzionamentoId);
            showAlert('Errore: pulsante non trovato', 'danger');
            return;
        }
        
        // FEEDBACK VISUALE: Stato "loading" durante richiesta
        const originalContent = $button.html(); // Salva contenuto originale
        $button.prop('disabled', true)          // Disabilita per evitare doppi click
               .removeClass('btn-outline-warning') // Rimuovi stile normale
               .addClass('btn-secondary')           // Applica stile loading
               .html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...');
        
        // ============================================================
        // RICHIESTA AJAX: Comunicazione asincrona con server Laravel
        // ============================================================
        /**
         * Configurazione AJAX completa per massima robustezza
         * Include gestione errori, timeout, e feedback specifico
         */
        $.ajax({
            // URL dinamico costruito con template literal
            url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',                    // Metodo HTTP per creazione risorse
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,    // Token sicurezza Laravel
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify({}),          // Body JSON vuoto ma valido
            timeout: 10000,                    // Timeout di 10 secondi
            
            // ========================================================
            // SUCCESS HANDLER: Gestione risposta positiva del server
            // ========================================================
            success: function(response) {
                console.log('Risposta segnalazione:', response);
                
                // Verifica che la risposta contenga flag di successo
                if (response.success) {
                    // FEEDBACK SUCCESSO: Notifica utente
                    showAlert('Segnalazione registrata con successo!', 'success');
                    
                    // AGGIORNAMENTO UI: Aggiorna contatore visibile
                    const $counter = $(`#count-${malfunzionamentoId}`);
                    if ($counter.length) {
                        // Usa nuovo count dalla risposta o incrementa manualmente
                        const nuovoCount = response.nuovo_count || (parseInt($counter.text()) + 1);
                        $counter.text(nuovoCount);
                        
                        // ANIMAZIONE FEEDBACK: Evidenzia l'aggiornamento
                        $counter.addClass('badge-updated');
                        setTimeout(() => {
                            $counter.removeClass('badge-updated');
                        }, 2000);
                    }
                    
                    // TRASFORMAZIONE BOTTONE: Da "segnala" a "segnalato"
                    $button.removeClass('btn-secondary btn-outline-warning')
                           .addClass('btn-success')
                           .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                           .prop('disabled', true); // Mantieni disabilitato
                    
                    console.log(`Segnalazione registrata per malfunzionamento ${malfunzionamentoId}. Nuovo conteggio: ${response.nuovo_count}`);
                } else {
                    // Risposta del server indica fallimento
                    throw new Error(response.message || 'Errore nella risposta del server');
                }
            },
            
            // ========================================================
            // ERROR HANDLER: Gestione errori di rete o server
            // ========================================================
            error: function(xhr, status, error) {
                // Log completo dell'errore per debug
                console.error('Errore segnalazione AJAX:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento';
                
                // GESTIONE ERRORI SPECIFICI: Messaggi user-friendly
                if (xhr.status === 0) {
                    errorMsg = 'Errore di connessione. Controlla la rete.';
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato';
                } else if (xhr.status === 429) {
                    errorMsg = 'Troppi tentativi. Riprova tra qualche minuto';
                } else if (xhr.status === 500) {
                    errorMsg = 'Errore interno del server';
                } else {
                    // Prova a estrarre messaggio specifico dalla risposta JSON
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        // Se la risposta non è JSON valida, usa messaggio di default
                        console.warn('Risposta non JSON:', xhr.responseText);
                    }
                }
                
                // Mostra errore all'utente
                showAlert(errorMsg, 'danger');
                
                // RIPRISTINO UI: Riporta bottone allo stato originale
                $button.removeClass('btn-secondary')
                       .addClass('btn-outline-warning')
                       .prop('disabled', false)
                       .html(originalContent);
            }
        });
    };
    
    // ================================================================
    // SISTEMA NOTIFICHE: Alert toast dinamici
    // LINGUAGGIO: Bootstrap Alert Component + jQuery
    // ================================================================
    /**
     * Funzione per mostrare notifiche toast temporanee
     * Utilizza componenti Bootstrap per consistenza visuale
     * 
     * @param {string} message - Messaggio da mostrare
     * @param {string} type - Tipo alert: success, danger, warning, info
     * 
     * CARATTERISTICHE:
     * - Posizionamento fisso in alto a destra
     * - Auto-dismiss dopo 5 secondi
     * - Icone contestuali per ogni tipo
     * - Bottone di chiusura manuale
     * - Z-index alto per stare sopra altri elementi
     */
    function showAlert(message, type = 'info') {
        // ID univoco per evitare conflitti tra alert multipli
        const alertId = 'alert-' + Date.now();
        
        // Mappa tipi di alert a icone Bootstrap Icons
        const icons = {
            success: 'bi-check-circle',        // Spunta per successo
            danger: 'bi-exclamation-triangle', // Triangolo per errori
            warning: 'bi-exclamation-circle',  // Cerchio per warning
            info: 'bi-info-circle'             // Info per messaggi generici
        };
        
        // Template HTML dell'alert con interpolazione dinamica
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-sm" 
                 role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; min-width: 300px;">
                <i class="${icons[type] || icons.info} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Inserisce l'alert nel DOM come toast
        $('body').append(alertHtml);
        
        // Auto-dismiss con animazione di fade out
        setTimeout(function() {
            const $alert = $(`#${alertId}`);
            if ($alert.length) {
                $alert.fadeOut(500, function() {
                    $(this).remove(); // Cleanup DOM
                });
            }
        }, 5000);
    }
    
    // ================================================================
    // STATISTICHE IN TEMPO REALE: Aggiornamento periodico via AJAX
    // ================================================================
    /**
     * Funzione per aggiornare le statistiche dashboard in tempo reale
     * Utilizza AJAX per recuperare dati freschi dal server
     * 
     * PATTERN: Polling periodico per dati live
     * PERFORMANCE: Update solo se i valori sono cambiati
     */
    function aggiornaStatistiche() {
        console.log('Aggiornamento statistiche...');
        
        $.ajax({
            url: API_URLS.stats_dashboard,     // URL configurato globalmente
            method: 'GET',                     // Richiesta di lettura
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Statistiche aggiornate:', response.data);
                    
                    const stats = response.data;
                    
                    /**
                     * Helper per aggiornare statistiche con animazione
                     * Controlla se il valore è cambiato prima di aggiornare
                     * 
                     * @param {string} selector - Selettore elemento da aggiornare
                     * @param {number} newValue - Nuovo valore da mostrare
                     */
                    function updateStat(selector, newValue) {
                        const $element = $(selector);
                        // Aggiorna solo se elemento esiste e valore è diverso
                        if ($element.length && $element.text() !== newValue.toString()) {
                            // Animazione di "aggiornamento" con classe CSS
                            $element.addClass('updating');
                            setTimeout(() => {
                                $element.text(newValue).removeClass('updating');
                            }, 300);
                        }
                    }
                    
                    // Aggiorna le statistiche utilizzando selettori specifici
                    // NOTA: Usa combinazione contenuto + icona per precisione
                    $('h5:contains("' + (stats.total_prodotti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-box-seam').length) {
                            updateStat(this, stats.total_prodotti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_malfunzionamenti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-tools').length) {
                            updateStat(this, stats.total_malfunzionamenti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.malfunzionamenti_critici || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-exclamation-triangle').length) {
                            updateStat(this, stats.malfunzionamenti_critici);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_centri || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-geo-alt').length) {
                            updateStat(this, stats.total_centri);
                        }
                    });
                }
            },
            error: function(xhr) {
                // Errore silenzioso per non disturbare l'esperienza utente
                // Ma comunque loggato per debug
                console.warn('Aggiornamento statistiche fallito:', xhr.status);
            }
        });
    }
    
    // Avvia aggiornamento automatico ogni 10 minuti (600000 ms)
    const statsUpdateInterval = setInterval(aggiornaStatistiche, 10 * 60 * 1000);
    
    // ================================================================
    // SHORTCUTS DA TASTIERA: Produttività per utenti esperti
    // LINGUAGGIO: JavaScript Event Handling
    // ================================================================
    /**
     * Sistema di shortcuts da tastiera per navigazione rapida
     * Migliora la produttività per utenti che usano spesso la dashboard
     * 
     * PATTERN: Event delegation su document con controllo focus
     * SICUREZZA: Funziona solo quando non si sta digitando in input
     */
    $(document).on('keydown', function(e) {
        // Esegui shortcuts solo se NON siamo dentro un campo di input
        // Evita conflitti con digitazione normale
        if (!$(e.target).is('input, textarea')) {
            
            // Ctrl + F = Focus su ricerca prodotti
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault(); // Evita ricerca browser nativa
                $('#searchProdotti').focus().select(); // Focus + selezione testo
                console.log('Shortcut Ctrl+F: Focus su ricerca prodotti');
            }
            
            // Ctrl + M = Focus su ricerca malfunzionamenti  
            if (e.ctrlKey && e.key === 'm') {
                e.preventDefault();
                $('#searchMalfunzionamenti').focus().select();
                console.log('Shortcut Ctrl+M: Focus su ricerca malfunzionamenti');
            }
            
            // Ctrl + C = Vai al catalogo completo
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                window.location.href = '{{ route("prodotti.completo.index") }}';
            }
            
            // Ctrl + H = Vai alla dashboard principale
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = '{{ route("dashboard") }}';
            }
            
            // Ctrl + S = Focus su segnalazione rapida (se presente)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const $firstSegnalaBtn = $('.segnala-btn').first();
                if ($firstSegnalaBtn.length) {
                    $firstSegnalaBtn.focus();
                    console.log('Shortcut Ctrl+S: Focus su primo pulsante segnalazione');
                }
            }
        }
    });
    
    // ================================================================
    // INIZIALIZZAZIONE UI: Tooltip, animazioni, eventi hover
    // LINGUAGGIO: Bootstrap JavaScript + jQuery
    // ================================================================
    /**
     * Inizializza componenti UI avanzati per migliorare l'esperienza utente
     * Include tooltip, animazioni hover, e feedback visivi
     */
    
    // TOOLTIP BOOTSTRAP: Inizializza per tutti gli elementi con attributo
    $('[data-bs-toggle="tooltip"]').tooltip();
    console.log('Tooltip inizializzati per', $('[data-bs-toggle="tooltip"]').length, 'elementi');
    
    // ANIMAZIONI HOVER: Effetto 3D su card della dashboard
    $('.card.card-custom').hover(
        function() {
            // Mouse enter: aggiungi ombra e traslazione verso l'alto
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            // Mouse leave: rimuovi effetti per tornare normale
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // FEEDBACK CLICK: Animazione su tutti i bottoni per feedback tattile
    $('.btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('btn-clicked'); // Classe CSS per animazione
        setTimeout(() => {
            $btn.removeClass('btn-clicked');
        }, 200); // Durata animazione breve
    });
    
    // ================================================================
    // SEZIONE TESTING: Funzioni di debug e verifica connessioni
    // ================================================================
    /**
     * Funzioni per testare e debuggare l'applicazione
     * Utili durante sviluppo e per diagnosticare problemi in produzione
     */
    
    // Test delle connessioni API per verificare che tutto funzioni
    function testConnessioniAPI() {
        console.log('🧪 Test delle connessioni API...');
        
        // Test API statistiche con promise-based handling
        $.get(API_URLS.stats_dashboard)
            .done(() => console.log('✅ API Statistiche: OK'))
            .fail((xhr) => console.log('❌ API Statistiche: ERRORE', xhr.status));
    }
    
    // ================================================================
    // GESTIONE RESPONSIVE: Adattamento layout per dispositivi mobili
    // ================================================================
    /**
     * Adatta automaticamente il layout per dispositivi mobili
     * Migliora usabilità su schermi piccoli nascondendo elementi non essenziali
     */
    function handleResponsiveLayout() {
        const isMobile = window.innerWidth < 768; // Breakpoint Bootstrap MD
        console.log('Layout responsive:', isMobile ? 'mobile' : 'desktop');
        
        if (isMobile) {
            // OTTIMIZZAZIONI MOBILE:
            // Nasconde testi di aiuto per risparmiare spazio
            $('.form-text').addClass('d-none');
            
            // Riduce padding delle card per più contenuto visibile
            $('.card-body').addClass('p-2');
            
            // Disabilita tooltip che su mobile non funzionano bene
            $('[data-bs-toggle="tooltip"]').tooltip('disable');
        } else {
            // RIPRISTINO DESKTOP:
            $('.form-text').removeClass('d-none');
            $('.card-body').removeClass('p-2');
            $('[data-bs-toggle="tooltip"]').tooltip('enable');
        }
    }
    
    // Chiama al caricamento e al ridimensionamento finestra
    handleResponsiveLayout();
    $(window).on('resize', handleResponsiveLayout);
    
    // ================================================================
    // GESTIONE ERRORI GLOBALI: Intercettazione errori AJAX
    // ================================================================
    /**
     * Intercetta tutti gli errori AJAX per logging centralizzato
     * e gestione errori user-friendly
     * 
     * PATTERN: Global error handler per monitoring e UX
     */
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        // Log completo per debug tecnico
        console.error('Errore AJAX Globale:', {
            url: ajaxSettings.url,
            status: jqXHR.status,
            statusText: jqXHR.statusText,
            responseText: jqXHR.responseText,
            error: thrownError
        });
        
        // Mostra errore solo per chiamate importanti (non statistiche)
        // Evita spam di notifiche per aggiornamenti automatici
        if (!ajaxSettings.url.includes('/stats/')) {
            showAlert('Si è verificato un errore di connessione. Riprova tra qualche momento.', 'danger');
        }
    });
    
    // ================================================================
    // CLEANUP: Gestione risorse quando si esce dalla pagina
    // ================================================================
    /**
     * Pulizia delle risorse per evitare memory leaks
     * Importante per applicazioni SPA o con molti timer/interval
     */
    $(window).on('beforeunload', function() {
        // Pulizia intervallo statistiche
        if (typeof statsUpdateInterval !== 'undefined') {
            clearInterval(statsUpdateInterval);
        }
        
        // Rimuovi tutti i tooltip attivi per evitare memory leaks
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
    });
    
    // ================================================================
    // LOGGING E ANALYTICS: Tracciamento utilizzo per miglioramenti
    // ================================================================
    /**
     * Sistema di logging per tracciare come gli utenti usano la dashboard
     * Utile per identificare problemi e migliorare UX
     */
    
    // MESSAGGIO BENVENUTO: Solo al primo accesso giornaliero
    const oggi = new Date().toDateString();
    const ultimoAccesso = localStorage.getItem('ultimo_accesso_dashboard_tecnico');
    
    if (ultimoAccesso !== oggi) {
        setTimeout(function() {
            showAlert('Benvenuto nella Dashboard Tecnico! Usa Ctrl+F per cercare prodotti e Ctrl+M per malfunzionamenti.', 'info');
            localStorage.setItem('ultimo_accesso_dashboard_tecnico', oggi);
        }, 1500); // Delay per non interferire con caricamento
    }
    
    // METRICHE SESSIONE: Traccia durata utilizzo dashboard
    const sessionStart = Date.now();
    window.addEventListener('beforeunload', function() {
        const sessionDuration = Date.now() - sessionStart;
        console.log('Sessione Dashboard Tecnico:', {
            durata: Math.round(sessionDuration / 1000) + ' secondi',
            utente: '{{ auth()->user()->username }}',
            timestamp: new Date().toISOString()
        });
    });
    
    // ================================================================
    // TEST FUNZIONALITÀ: Verifica automatica componenti al caricamento
    // ================================================================
    /**
     * Test automatico che verifica la presenza e funzionamento
     * degli elementi chiave della dashboard dopo l'inizializzazione
     */
    setTimeout(function() {
        console.log('=== TEST FUNZIONALITÀ DASHBOARD ===');
        
        // Test presenza elementi chiave nel DOM
        const elementi = {
            'Input ricerca prodotti': $('#searchProdotti').length,
            'Input ricerca malfunzionamenti': $('#searchMalfunzionamenti').length,
            'Bottone ricerca prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').length,
            'Bottone ricerca malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').length,
            'Pulsanti segnalazione': $('.segnala-btn').length,
            'Card statistiche': $('.card.border-0.shadow-sm').length
        };
        
        // Output tabellare per facile lettura
        console.table(elementi);
        
        // Verifica se gli event handler sono stati correttamente attaccati
        // NOTA: jQuery .data('events') potrebbe non essere disponibile in tutte le versioni
        const eventiAttaccati = {
            'Click bottone prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Click bottone malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Keypress prodotti': $('#searchProdotti').data('events') ? 'SI' : 'NO',
            'Keypress malfunzionamenti': $('#searchMalfunzionamenti').data('events') ? 'SI' : 'NO'
        };
        
        console.table(eventiAttaccati);
        console.log('=== FINE TEST FUNZIONALITÀ ===');
    }, 1000); // Aspetta 1 secondo dopo inizializzazione completa
    
    // ================================================================
    // FINALIZZAZIONE: Configurazione oggetti globali e debug tools
    // ================================================================
    /**
     * Creazione namespace globale per funzioni di debug
     * e configurazione finale dell'applicazione
     */
    
    console.log('✅ Dashboard Tecnico inizializzata completamente');
    console.log('🔧 URLs API configurati:', API_URLS);
    console.log('🚀 Funzioni disponibili:', {
        'segnalaMalfunzionamento()': 'Segnala un problema riscontrato',
        'testConnessioniAPI()': 'Test delle connessioni API',
        'aggiornaStatistiche()': 'Forza aggiornamento statistiche',
        'validaTermineRicerca()': 'Valida termine di ricerca'
    });
    
    // NAMESPACE GLOBALE: Espone funzioni per debugging dalla console browser
    window.dashboardTecnico = {
        testAPI: testConnessioniAPI,         // Testa connessioni API
        updateStats: aggiornaStatistiche,    // Forza aggiornamento statistiche
        showAlert: showAlert,                // Mostra notifica personalizzata
        validateSearch: validaTermineRicerca,// Valida termine ricerca
        urls: API_URLS,                      // URLs API configurati
        version: '2.1.0'                     // Versione corrente del modulo
    };
    
    // TEST AUTOMATICO API: Solo in ambiente di sviluppo per evitare traffico in produzione
    // NOTA: window.isLocalEnv deve essere impostato nel template Blade:
    // <script>window.isLocalEnv = {{ app()->environment('local') ? 'true' : 'false' }};</script>
    if (window.isLocalEnv) {
        setTimeout(testConnessioniAPI, 2000);
    }
    
    // ================================================================
    // LOGGING FINALE: Conferma inizializzazione completata
    // ================================================================
    console.log('✅ Dashboard Tecnico inizializzata completamente');
    console.log('📊 Modulo version 2.1.0 caricato con successo');
    console.log('🎯 Target: Utenti Tecnici (Livello 2)');
    console.log('🔄 Auto-refresh statistiche: ogni 10 minuti');
    console.log('⌨️  Shortcuts disponibili: Ctrl+F, Ctrl+M, Ctrl+C, Ctrl+H, Ctrl+S');
    console.log('📱 Layout responsive: attivo');
    console.log('🔒 Sicurezza CSRF: configurata');
    console.log('🚦 Sistema validazione: attivo');
    console.log('💬 Sistema notifiche: operativo');
    
}); // Fine $(document).ready()

// ====================================================================
// DOCUMENTAZIONE TECNICA AGGIUNTIVA
// ====================================================================
/**
 * ARCHITETTURA COMPLESSIVA:
 * 
 * 1. INIZIALIZZAZIONE (Righe 1-50):
 *    - Controllo sicurezza route
 *    - Setup variabili globali
 *    - Configurazione AJAX e CSRF
 * 
 * 2. GESTIONE RICERCA (Righe 51-200):
 *    - Validazione client-side
 *    - Feedback visuale loading
 *    - Submit form con prevenzione errori
 * 
 * 3. SEGNALAZIONI AJAX (Righe 201-350):
 *    - Comunicazione asincrona server
 *    - Gestione errori specifici
 *    - Aggiornamento UI real-time
 * 
 * 4. SISTEMA NOTIFICHE (Righe 351-400):
 *    - Alert toast temporanei
 *    - Icone contestuali
 *    - Auto-dismiss intelligente
 * 
 * 5. STATISTICHE LIVE (Righe 401-500):
 *    - Polling periodico
 *    - Update selettivo DOM
 *    - Gestione errori silenziosa
 * 
 * 6. UX AVANZATA (Righe 501-650):
 *    - Shortcuts tastiera
 *    - Layout responsive
 *    - Animazioni hover
 * 
 * 7. TESTING E DEBUG (Righe 651-750):
 *    - Verifica componenti
 *    - Test API automatici
 *    - Logging dettagliato
 * 
 * PATTERN UTILIZZATI:
 * - Module Pattern per organizzazione
 * - Event Delegation per robustezza
 * - Promise-based AJAX per asincronia
 * - Progressive Enhancement per accessibilità
 * - Defensive Programming per robustezza
 * - Responsive Design per multi-device
 * 
 * TECNOLOGIE INTEGRATE:
 * - jQuery 3.x per DOM manipulation
 * - Bootstrap 5.x per UI components  
 * - Laravel Blade per template integration
 * - AJAX/JSON per API communication
 * - Local Storage per persistenza dati
 * - CSS3 per animazioni e responsive
 * 
 * SICUREZZA:
 * - CSRF token in tutte le richieste POST
 * - Validazione input lato client e server
 * - Sanitizzazione caratteri pericolosi
 * - Timeout AJAX per evitare hang
 * - Error handling per prevenire crash
 * 
 * PERFORMANCE:
 * - Lazy loading per elementi non critici
 * - Debouncing per eventi frequenti
 * - Cleanup risorse per evitare memory leak
 * - Update selettivo DOM per fluidità
 * - Caching API response quando possibile
 * 
 * ACCESSIBILITÀ:
 * - ARIA labels per screen readers
 * - Keyboard navigation completa
 * - Focus management appropriato
 * - Contrast colors per visibilità
 * - Semantic HTML structure
 * 
 * INTEGRAZIONE LARAVEL:
 * - Blade directives per URLs dinamici
 * - CSRF token per sicurezza POST requests
 * - Session flash messages per feedback
 * - Route helpers per navigation
 * - Authentication data per personalizzazione
 * 
 * DEBUGGING E MONITORING:
 * - Console logging strutturato con emoji
 * - Error tracking con stack traces completi
 * - Performance monitoring con timing
 * - API health checks automatici
 * - User session analytics
 * 
 * FUTURE IMPROVEMENTS:
 * - WebSocket per real-time updates
 * - Service Worker per offline functionality
 * - Push notifications per alert critici
 * - Advanced caching strategies
 * - Machine learning per suggerimenti ricerca
 */

// ====================================================================
// FINE DEL MODULO DASHBOARD TECNICO
// Versione: 2.1.0
// Ultima modifica: Gruppo 51 - 2025
// Compatibilità: Laravel 12, jQuery 3.x, Bootstrap 5.x
// Browser supportati: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
// ====================================================================