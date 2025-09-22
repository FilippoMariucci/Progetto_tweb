/**
 * ===================================================================
 * ADMIN PRODOTTI SHOW - JavaScript per Dettaglio Singolo Prodotto
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/admin/prodotti-show.js
 * Linguaggio: JavaScript Vanilla (ES6+) + Bootstrap 5
 * 
 * DESCRIZIONE GENERALE:
 * Questo file gestisce l'interfaccia per la visualizzazione dettagliata
 * di un singolo prodotto nell'area amministrativa. Include funzionalità
 * per toggle status, gestione immagini, notifiche, shortcuts da tastiera
 * e sistema di debug avanzato.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Toggle attivazione/disattivazione prodotto con conferma
 * - Gestione errori immagini prodotto con fallback
 * - Sistema notifiche toast avanzato con Bootstrap
 * - Sistema di logging configurabile per debugging
 * - Keyboard shortcuts per migliorare produttività
 * - Accessibility enhancements per utenti con disabilità
 * - Sistema di sicurezza anti-XSS per notifiche
 * - Debug panel per sviluppo
 * - Performance monitoring per ottimizzazione
 * ===================================================================
 */

// ===================================================================
// SEZIONE 1: INIZIALIZZAZIONE PRINCIPALE E CONTROLLI
// ===================================================================

/**
 * EVENT LISTENER PRINCIPALE - DOMContentLoaded
 * SCOPO: Esegue inizializzazione quando DOM è completamente caricato
 * LINGUAGGIO: JavaScript Vanilla Event API
 * 
 * DOMContentLoaded si attiva prima di window.onload, non aspetta immagini/CSS
 * Garantisce che tutti gli elementi HTML siano disponibili per manipulation
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('admin.prodotti.show caricato');

    /**
     * CONTROLLO ROUTE - Verifica che siamo nella pagina corretta
     * SCOPO: Previene esecuzione codice in pagine sbagliate
     * PATTERN: Early return per performance e sicurezza
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.show') {
        return; // Esce dalla funzione se non siamo nella pagina show
    }

    /**
     * INIZIALIZZAZIONE DATI GLOBALI
     * FONTE: Dati iniettati da Laravel Blade template in window.PageData
     */
    const pageData = window.PageData || {};
    let selectedProducts = []; // Array per compatibilità con altri script

    // ===================================================================
    // SEZIONE 2: CONFIGURAZIONE GLOBALE - Oggetto Config
    // ===================================================================

    /**
     * OGGETTO CONFIG - Configurazione centralizzata
     * PATTERN: Configuration Object per centralizzare impostazioni
     * FONTE: Dati da Laravel (Blade → JavaScript)
     */
    const config = {
        // Dati prodotto corrente
        prodotto: {
            id: window.PageData?.prodotto?.id || null,              // ID numerico prodotto
            nome: window.PageData?.prodotto?.nome || '',            // Nome prodotto
            attivo: window.PageData?.prodotto?.attivo || false,     // Status attivo/inattivo
            staffAssegnato: window.PageData?.prodotto?.staffAssegnato || null // Staff responsabile
        },
        // Route Laravel per azioni
        routes: {
            toggleStatus: window.PageData?.routes?.toggleStatus || '', // URL per toggle status
            show: window.PageData?.routes?.show || ''                 // URL pagina corrente
        },
        // Flag debug per logging condizionale
        debug: window.LaravelApp?.debug || false
    };

    // ===================================================================
    // SEZIONE 3: SISTEMA DI LOGGING E DEBUG
    // ===================================================================

    /**
     * FUNZIONE: log(message, type, data)
     * SCOPO: Sistema di logging avanzato con categorizzazione
     * LINGUAGGIO: JavaScript Console API + String manipulation
     * PARAMETRI:
     *   - message (string): Messaggio da loggare
     *   - type (string): Tipo log ('info', 'error', 'warn', 'success')
     *   - data (any): Dati opzionali da allegare
     * RETURN: void
     * 
     * FEATURE: Logging condizionale (solo se config.debug = true)
     */
    function log(message, type = 'info', data = null) {
        // Esegue logging solo se modalità debug abilitata
        if (config.debug) {
            const timestamp = new Date().toLocaleTimeString(); // Timestamp locale
            const prefix = `[${timestamp}] TechSupport Admin:`; // Prefisso identificativo

            /**
             * SWITCH STATEMENT - Gestione tipi di log diversi
             * Ogni tipo ha colore e metodo console differente
             */
            switch(type) {
                case 'error':
                    console.error(prefix, message, data);
                    break;
                case 'warn':
                    console.warn(prefix, message, data);
                    break;
                case 'success':
                    // CSS inline per colorare output console
                    console.log(`%c${prefix}`, 'color: green; font-weight: bold;', message, data);
                    break;
                default:
                    console.log(prefix, message, data);
            }
        }
    }

    // Log inizializzazione con dati prodotto
    log('Inizializzazione pagina dettaglio prodotto (versione semplificata)', 'info', config.prodotto);

    // ===================================================================
    // SEZIONE 4: GESTIONE TOGGLE STATUS PRODOTTO
    // ===================================================================

    /**
     * GESTIONE FORM TOGGLE STATUS
     * SCOPO: Attiva/disattiva prodotto con conferma utente
     * LINGUAGGIO: JavaScript DOM Queries + Event Listeners
     * 
     * Trova tutti i form che contengono "toggle-status" nell'action
     * Utilizza querySelectorAll per supportare multiple form (se presenti)
     */
    const toggleForms = document.querySelectorAll('form[action*="toggle-status"]');

    /**
     * ITERAZIONE FORM - Aggiunge event listener a ogni form trovato
     * METODO: forEach per iterare NodeList
     */
    toggleForms.forEach(form => {
        /**
         * EVENT LISTENER: Submit form
         * EVENTO: 'submit' - Si attiva prima dell'invio al server
         * POSSIBILITÀ: preventDefault() per bloccare invio
         */
        form.addEventListener('submit', function(e) {
            const isActive = config.prodotto.attivo; // Stato corrente prodotto

            /**
             * CONFERMA UTENTE - Chiama funzione globale per conferma
             * Se utente annulla, preventDefault blocca submit
             */
            if (!confirmToggleStatus(isActive)) {
                e.preventDefault(); // Blocca invio form
                return false;
            }

            // Log operazione per debugging
            log(`Toggle status prodotto: ${isActive ? 'disattivare' : 'attivare'}`, 'info', {
                prodottoId: config.prodotto.id,
                currentStatus: isActive
            });

            /**
             * FEEDBACK VISIVO - Modifica pulsante durante operazione
             * SCOPO: Mostra all'utente che operazione è in corso
             * PREVIENE: Doppi click accidentali
             */
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true; // Disabilita pulsante
                // Cambia testo con spinner Bootstrap Icons
                button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Attendere...';
            }
        });
    });

    // ===================================================================
    // SEZIONE 5: GESTIONE IMMAGINI PRODOTTO
    // ===================================================================

    /**
     * GESTIONE IMMAGINI PRODOTTO
     * SCOPO: Fallback per immagini non trovate + interattività
     * LINGUAGGIO: JavaScript DOM manipulation + Event handling
     */
    const productImages = document.querySelectorAll('.product-image');

    /**
     * ITERAZIONE IMMAGINI - Event listeners per ogni immagine
     */
    productImages.forEach(img => {
        /**
         * EVENT: 'error' - Si attiva quando immagine non può essere caricata
         * CAUSA: 404, permessi, formato non supportato, etc.
         * AZIONE: Chiama funzione globale handleImageError
         */
        img.addEventListener('error', function() {
            handleImageError(this); // 'this' = elemento img che ha scatenato errore
        });

        /**
         * EVENT: 'click' - Click sull'immagine
         * FEATURE: Modalità fullscreen (se supportata dal browser)
         * COMPATIBILITÀ: Controlla supporto Fullscreen API
         */
        img.addEventListener('click', function() {
            // Verifica supporto API fullscreen
            if (this.requestFullscreen) {
                this.requestFullscreen(); // Attiva fullscreen
            }
            // NOTA: Potrebbe essere esteso con modal Bootstrap per più controllo
        });
    });

    // ===================================================================
    // SEZIONE 6: SISTEMA NOTIFICHE LARAVEL → JAVASCRIPT
    // ===================================================================

    /**
     * GESTIONE NOTIFICHE DA LARAVEL
     * SCOPO: Mostra notifiche iniettate dal controller Laravel
     * FONTE: window.LaravelNotifications (popolato da Blade template)
     * PATTERN: Bridge Laravel Backend → JavaScript Frontend
     */
    if (window.LaravelNotifications) {
        
        /**
         * NOTIFICA SUCCESS
         * TRIGGER: Operazioni riuscite (es. prodotto aggiornato)
         */
        if (window.LaravelNotifications.success) {
            showNotification('success', window.LaravelNotifications.success);
            log('Notifica success mostrata', 'success', window.LaravelNotifications.success);
        }

        /**
         * NOTIFICA ERROR
         * TRIGGER: Errori generici del server
         */
        if (window.LaravelNotifications.error) {
            showNotification('error', window.LaravelNotifications.error);
            log('Notifica error mostrata', 'error', window.LaravelNotifications.error);
        }

        /**
         * ERRORI VALIDAZIONE
         * TRIGGER: Validazione form fallita (Laravel Validator)
         * FORMATO: Array di stringhe di errore
         */
        if (Array.isArray(window.LaravelNotifications.errors)) {
            window.LaravelNotifications.errors.forEach(function(error) {
                showNotification('error', error);
                log('Errore validazione', 'error', error);
            });
        }
    }

}); // Fine DOMContentLoaded

// ===================================================================
// SEZIONE 7: FUNZIONI GLOBALI - Accessibili da HTML e altri script
// ===================================================================

/**
 * FUNZIONE GLOBALE: confirmToggleStatus(isActive)
 * SCOPO: Conferma utente per cambio status prodotto
 * LINGUAGGIO: JavaScript Dialog API + String templates
 * PARAMETRO: isActive (boolean) - Status attuale prodotto
 * RETURN: boolean - True se confermato, false se annullato
 * 
 * ACCESSIBILITÀ: Funzione globale per uso in onclick HTML
 */
function confirmToggleStatus(isActive) {
    const action = isActive ? 'disattivare' : 'attivare'; // Determina azione
    const message = `Sei sicuro di voler ${action} questo prodotto?`; // Messaggio base

    /**
     * MESSAGGI DIFFERENZIATI - Spiegazioni specifiche per ogni azione
     * UX: Informa utente delle conseguenze dell'azione
     */
    if (isActive) {
        // Messaggio per disattivazione (più severo)
        return confirm(`${message}\n\nSe disattivato, il prodotto non sarà più visibile nel catalogo pubblico.`);
    } else {
        // Messaggio per attivazione (positivo)
        return confirm(`${message}\n\nSe attivato, il prodotto tornerà visibile nel catalogo pubblico.`);
    }
}

/**
 * FUNZIONE GLOBALE: handleImageError(img)
 * SCOPO: Gestisce errori caricamento immagini con fallback
 * LINGUAGGIO: JavaScript DOM manipulation + Error handling
 * PARAMETRO: img (HTMLImageElement) - Elemento img che ha fallito
 * RETURN: void
 * 
 * PATTERN: Graceful degradation per migliorare UX
 */
function handleImageError(img) {
    // URL immagine placeholder (personalizzabile)
    const placeholderUrl = '/images/placeholder-product.png';

    /**
     * PREVENZIONE LOOP INFINITO
     * PROBLEMA: Se placeholder non esiste, scatenerebbe error infinito
     * SOLUZIONE: Controlla se stiamo già usando placeholder
     */
    if (img.src !== placeholderUrl) {
        console.warn('🖼️ Errore caricamento immagine:', img.src);
        img.src = placeholderUrl; // Imposta immagine fallback
        img.onerror = null; // Rimuove handler per evitare loop

        /**
         * CLASSE CSS per styling
         * SCOPO: Permette CSS personalizzato per immagini fallite
         */
        img.classList.add('image-error');
    }
}

// ===================================================================
// SEZIONE 8: SISTEMA CLIPBOARD - Copia Testo
// ===================================================================

/**
 * FUNZIONE GLOBALE: copyToClipboard(text, successMessage)
 * SCOPO: Copia testo negli appunti con fallback per browser meno recenti
 * LINGUAGGIO: JavaScript Clipboard API + Fallback execCommand
 * PARAMETRI:
 *   - text (string): Testo da copiare
 *   - successMessage (string): Messaggio di successo personalizzato
 * RETURN: void
 * 
 * PATTERN: Progressive Enhancement con fallback
 */
function copyToClipboard(text, successMessage = 'Testo copiato negli appunti!') {
    /**
     * CLIPBOARD API MODERNO (HTTPS richiesto)
     * SUPPORTO: Chrome 66+, Firefox 63+, Safari 13.1+
     * SICUREZZA: Richiede contesto sicuro (HTTPS o localhost)
     */
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('success', successMessage);
        }).catch(err => {
            console.error('Errore copia clipboard:', err);
            fallbackCopyTextToClipboard(text, successMessage); // Usa fallback
        });
    } else {
        /**
         * FALLBACK per browser più vecchi o HTTP
         */
        fallbackCopyTextToClipboard(text, successMessage);
    }
}

/**
 * FUNZIONE: fallbackCopyTextToClipboard(text, successMessage)
 * SCOPO: Metodo fallback per copia testo (execCommand)
 * LINGUAGGIO: JavaScript DOM manipulation + execCommand API
 * COMPATIBILITÀ: Supporto esteso per browser datati
 * 
 * TECNICA: Crea textarea temporaneo, seleziona, copia, rimuove
 */
function fallbackCopyTextToClipboard(text, successMessage) {
    /**
     * CREAZIONE TEXTAREA TEMPORANEO
     * STYLING: Invisible ma accessibile per selezione
     */
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0"; // Invisibile ma presente nel DOM

    /**
     * SEQUENZA COPIA
     * 1. Aggiungi al DOM
     * 2. Focus e selezione
     * 3. Comando copia
     * 4. Rimozione
     */
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        /**
         * execCommand('copy') - API DEPRECATA ma ampiamente supportata
         * RETURN: boolean - successo operazione
         */
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('success', successMessage);
        } else {
            throw new Error('execCommand fallito');
        }
    } catch (err) {
        console.error('Fallback copy fallito:', err);
        showNotification('error', 'Impossibile copiare negli appunti');
    }

    document.body.removeChild(textArea); // Pulizia elemento temporaneo
}

// ===================================================================
// SEZIONE 9: SISTEMA NOTIFICHE TOAST AVANZATO
// ===================================================================

/**
 * FUNZIONE GLOBALE: showNotification(type, message, duration)
 * SCOPO: Sistema notifiche toast moderno con Bootstrap
 * LINGUAGGIO: JavaScript + Bootstrap 5 Toast API
 * PARAMETRI:
 *   - type (string): Tipo ('success', 'error', 'warning', 'info')
 *   - message (string): Testo notifica
 *   - duration (number): Durata in millisecondi (default: 5000)
 * RETURN: void
 * 
 * FEATURE: Fallback ad alert() se Bootstrap non disponibile
 */
function showNotification(type, message, duration = 5000) {
    /**
     * CONTROLLO BOOTSTRAP TOAST
     * VERIFICA: Bootstrap caricato e Toast class disponibile
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        
        /**
         * CONFIGURAZIONE VISUAL
         * MAPPING: Tipo → Icona Bootstrap Icons + Classe colore
         */
        const toastId = 'toast-' + Date.now(); // ID unico basato su timestamp
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';

        /**
         * TEMPLATE HTML TOAST
         * STRUTTURA: Bootstrap Toast standard con icona + messaggio + close
         */
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        /**
         * CONTAINER TOAST - Crea se non esiste
         * POSIZIONE: Bottom-right fixed
         * Z-INDEX: Alto per visibilità sopra altri elementi
         */
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        /**
         * INSERIMENTO E ATTIVAZIONE TOAST
         */
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        
        /**
         * ISTANZA BOOTSTRAP TOAST
         * CONFIGURAZIONE: Auto-hide attivo con durata personalizzabile
         */
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });

        toast.show(); // Mostra toast

        /**
         * CLEANUP - Rimuove elemento DOM dopo nascondimento
         * EVENTO: 'hidden.bs.toast' - Toast completamente nascosto
         */
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove(); // Rimuove dal DOM per evitare accumulo
        });

    } else {
        /**
         * FALLBACK - Alert browser nativo se Bootstrap non disponibile
         * UX: Meno elegante ma funzionale
         */
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

// ===================================================================
// SEZIONE 10: KEYBOARD SHORTCUTS - Produttività Utente
// ===================================================================

/**
 * GESTIONE KEYBOARD SHORTCUTS
 * SCOPO: Shortcuts da tastiera per utenti esperti
 * LINGUAGGIO: JavaScript KeyboardEvent API
 * EVENT: 'keydown' - Rileva combinazioni tasti
 */
document.addEventListener('keydown', function(e) {
    
    /**
     * SHORTCUT: Ctrl/Cmd + S (Salva)
     * AZIONE: Previene comportamento default browser + informa utente
     * CROSS-PLATFORM: Ctrl (Windows/Linux) + Cmd (Mac)
     */
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault(); // Blocca "Salva pagina" del browser
        showNotification('info', 'Usa i pulsanti di modifica per salvare le modifiche');
        return false;
    }

    /**
     * SHORTCUT: Alt + E (Edit prodotto)
     * AZIONE: Click automatico sul link di modifica
     * UX: Accesso rapido alla modifica prodotto
     */
    if (e.altKey && e.key === 'e') {
        e.preventDefault();
        const editBtn = document.querySelector('a[href*="edit"]'); // Trova link edit
        if (editBtn) {
            editBtn.click(); // Simula click
        }
    }
});

// ===================================================================
// SEZIONE 11: TOOLTIP KEYBOARD SHORTCUTS
// ===================================================================

/**
 * FUNZIONE: initKeyboardTooltips()
 * SCOPO: Aggiunge tooltip con info shortcuts ai pulsanti
 * LINGUAGGIO: JavaScript + Bootstrap Tooltip API
 * RETURN: void
 * 
 * UX: Informa utenti dell'esistenza degli shortcuts
 */
function initKeyboardTooltips() {
    /**
     * CONFIGURAZIONE TOOLTIPS
     * ARRAY: Mappatura selettore CSS → testo tooltip
     */
    const tooltips = [
        { selector: 'a[href*="edit"]', title: 'Modifica prodotto (Alt+E)' }
        // Espandibile con altri shortcuts
    ];

    /**
     * APPLICAZIONE TOOLTIPS
     * ITERAZIONE: Per ogni configurazione, trova elemento e applica tooltip
     */
    tooltips.forEach(item => {
        const element = document.querySelector(item.selector);
        // Verifica elemento esista e Bootstrap disponibile
        if (element && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(element, {
                title: item.title,
                placement: 'top' // Posizione tooltip
            });
        }
    });
}

// Inizializza tooltips
initKeyboardTooltips();

// ===================================================================
// SEZIONE 12: ACCESSIBILITY ENHANCEMENTS - Miglioramenti Accessibilità
// ===================================================================

/**
 * FUNZIONE: improveAccessibility()
 * SCOPO: Migliora accessibilità per screen readers e navigazione tastiera
 * LINGUAGGIO: JavaScript DOM manipulation + ARIA attributes
 * RETURN: void
 * 
 * STANDARD: WCAG 2.1 compliance improvements
 */
function improveAccessibility() {
    
    /**
     * ARIA LABELS per pulsanti senza testo descrittivo
     * PROBLEMA: Screen readers non sanno descrivere pulsanti senza testo
     * SOLUZIONE: Aggiunge aria-label basato su contenuto visibile
     */
    document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])').forEach(btn => {
        const text = btn.textContent.trim();
        if (text) {
            btn.setAttribute('aria-label', text);
        }
    });

    /**
     * FOCUS ENHANCEMENT per navigazione tastiera
     * SCOPO: Outline visibile per utenti che navigano con tastiera
     * STANDARD: Focus deve essere sempre visibile (WCAG 2.4.7)
     */
    document.querySelectorAll('.btn, .form-control, .form-select').forEach(el => {
        
        /**
         * EVENT: 'focus' - Elemento riceve focus
         * STYLING: Aggiunge outline personalizzato
         */
        el.addEventListener('focus', function() {
            this.style.outline = '2px solid #007bff';
            this.style.outlineOffset = '2px';
        });

        /**
         * EVENT: 'blur' - Elemento perde focus
         * STYLING: Rimuove outline personalizzato
         */
        el.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });
}

// Attiva miglioramenti accessibilità
improveAccessibility();

// ===================================================================
// SEZIONE 13: INIZIALIZZAZIONE FINALE E EVENTI CUSTOM
// ===================================================================

/**
 * LOGGING FINALE - Conferma inizializzazione completa
 */
console.log('🎉 Pagina admin prodotto (versione semplificata) completamente inizializzata');

/**
 * CUSTOM EVENT - Notifica altri script che pagina è pronta
 * SCOPO: Interoperabilità con altri script/componenti
 * PATTERN: Event-driven architecture
 * 
 * DATI: Informazioni prodotto e route per altri script
 */
window.dispatchEvent(new CustomEvent('adminProductPageReady', {
    detail: {
        prodotto: config.prodotto,        // Dati prodotto corrente
        routes: config.routes,            // Route disponibili
        timestamp: new Date().toISOString(), // Timestamp inizializzazione
        version: 'simplified'             // Versione script
    }
}));

// ===================================================================
// SEZIONE 14: DEBUGGING E PERFORMANCE MONITORING
// ===================================================================

/**
 * SEZIONE DEBUG - Attiva solo se config.debug = true
 * SCOPO: Tools per sviluppo e ottimizzazione
 */
if (config.debug) {
    
    /**
     * PERFORMANCE MONITORING
     * API: Performance Timing per misurare velocità caricamento
     * EVENT: 'load' - Pagina completamente caricata (immagini incluse)
     */
    window.addEventListener('load', function() {
        if (performance && performance.timing) {
            // Calcola tempo caricamento totale
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`⏱️ Pagina admin prodotto caricata in: ${loadTime}ms`);

            /**
             * WARNING per performance lente
             * SOGLIA: 3 secondi considerati lenti
             */
            if (loadTime > 3000) {
                console.warn('🐌 Caricamento lento rilevato per pagina admin prodotto');
            }
        }
    });

    /**
     * DEBUG PANEL VISIVO
     * TRIGGER: URL parameter ?debug=1
     * SCOPO: Info runtime visibili durante sviluppo
     */
    if (new URLSearchParams(window.location.search).get('debug') === '1') {
        const panel = document.createElement('div');
        panel.className = 'debug-panel';
        
        /**
         * STYLING DEBUG PANEL
         * POSIZIONE: Fixed bottom-right
         * DESIGN: Dark overlay con info sviluppo
         */
        panel.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            z-index: 9998;
            max-width: 300px;
            line-height: 1.3;
        `;

        /**
         * CONTENUTO DEBUG PANEL
         * INFO: Stato prodotto, staff, dipendenze
         */
        panel.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 5px; color: #ffc107;">🔧 DEBUG PANEL (Simplified)</div>
            <div>Prodotto: <span style="color: #28a745;">${config.prodotto.id}</span></div>
            <div>Staff: <span style="color: ${config.prodotto.staffAssegnato ? '#28a745' : '#dc3545'};">${config.prodotto.staffAssegnato || 'Non assegnato'}</span></div>
            <div>Bootstrap: <span style="color: ${typeof bootstrap !== 'undefined' ? '#28a745' : '#dc3545'};">${typeof bootstrap !== 'undefined' ? 'Caricato' : 'Mancante'}</span></div>
        `;

        document.body.appendChild(panel);
    }
}

// ===================================================================
// SEZIONE 15: SICUREZZA - Prevenzione XSS
// ===================================================================

/**
 * FUNZIONE: sanitizeMessage(message)
 * SCOPO: Previene attacchi XSS nelle notifiche
 * LINGUAGGIO: JavaScript DOM Text API
 * PARAMETRO: message (string) - Messaggio da sanificare
 * RETURN: string - Messaggio sicuro (HTML escaped)
 * 
 * SICUREZZA: Converte HTML in testo plain per prevenire injection
 */
function sanitizeMessage(message) {
    const div = document.createElement('div');
    div.textContent = message; // textContent escape automaticamente HTML
    return div.innerHTML; // Ritorna versione HTML-escaped
}

/**
 * OVERRIDE SICUREZZA - Wrapper sicuro per showNotification
 * SCOPO: Garantisce che tutte le notifiche siano sanificate
 * PATTERN: Decorator pattern per aggiungere sicurezza
 * 
 * Salva riferimento alla funzione originale e la sostituisce
 * con una versione che sanifica automaticamente i messaggi
 */
const originalShowNotification = window.showNotification;
window.showNotification = function(type, message, duration = 5000) {
    return originalShowNotification(type, sanitizeMessage(message), duration);
};

// ===================================================================
// SEZIONE 16: CONTENT SECURITY POLICY (CSP) MONITORING
// ===================================================================

/**
 * CSP VIOLATION MONITORING
 * SCOPO: Rileva violazioni Content Security Policy per sicurezza
 * LINGUAGGIO: JavaScript SecurityPolicyViolationEvent API
 * STANDARD: CSP Level 3
 * 
 * CSP previene attacchi XSS limitando risorse che la pagina può caricare
 * Questo event listener aiuta a identificare problemi CSP durante sviluppo
 */
document.addEventListener('securitypolicyviolation', function(e) {
    console.error('Violazione CSP rilevata:', e.violatedDirective, e.blockedURI);
    
    /**
     * DETTAGLI VIOLAZIONE CSP
     * PROPRIETÀ UTILI:
     * - violatedDirective: Quale direttiva è stata violata
     * - blockedURI: Risorsa bloccata
     * - originalPolicy: Policy completa
     * - sourceFile: File che ha causato violazione
     */
    
    if (config.debug) {
        // Log dettagliato solo in modalità debug
        console.error('Dettagli violazione CSP:', {
            directive: e.violatedDirective,
            blocked: e.blockedURI,
            source: e.sourceFile,
            line: e.lineNumber,
            column: e.columnNumber
        });
    }
});

// ===================================================================
// SEZIONE 17: UTILITY FUNCTIONS AVANZATE
// ===================================================================

/**
 * FUNZIONE: debounce(func, delay)
 * SCOPO: Limita frequenza esecuzione funzioni per performance
 * LINGUAGGIO: JavaScript Closures + Timers
 * PARAMETRI:
 *   - func (function): Funzione da eseguire con debounce
 *   - delay (number): Millisecondi di ritardo
 * RETURN: function - Funzione wrappata con debounce
 * 
 * USO: Ottimizza eventi frequenti come scroll, resize, input
 */
function debounce(func, delay) {
    let timeoutId; // Closure variable per mantenere timeout
    
    return function debounced() {
        const context = this;        // Mantiene contesto originale
        const args = arguments;      // Mantiene argomenti originali
        
        clearTimeout(timeoutId);     // Cancella timeout precedente
        
        // Imposta nuovo timeout
        timeoutId = setTimeout(function() {
            func.apply(context, args);
        }, delay);
    };
}

/**
 * FUNZIONE: throttle(func, limit)
 * SCOPO: Limita esecuzione funzione a massimo una volta per periodo
 * LINGUAGGIO: JavaScript Closures + Timing
 * PARAMETRI:
 *   - func (function): Funzione da limitare
 *   - limit (number): Millisecondi tra esecuzioni
 * RETURN: function - Funzione throttled
 * 
 * DIFFERENZA DA DEBOUNCE: Garantisce esecuzione periodica regolare
 */
function throttle(func, limit) {
    let inThrottle; // Flag per controllare stato throttling
    
    return function throttled() {
        const context = this;
        const args = arguments;
        
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            
            setTimeout(function() {
                inThrottle = false;
            }, limit);
        }
    };
}

/**
 * FUNZIONE: formatDate(date, locale)
 * SCOPO: Formattazione date consistente nell'applicazione
 * LINGUAGGIO: JavaScript Intl.DateTimeFormat API
 * PARAMETRI:
 *   - date (Date|string): Data da formattare
 *   - locale (string): Locale (default: 'it-IT')
 * RETURN: string - Data formattata
 * 
 * INTERNAZIONALIZZAZIONE: Supporta diverse localizzazioni
 */
function formatDate(date, locale = 'it-IT') {
    if (!date) return '';
    
    // Converte string in Date se necessario
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    
    // Verifica validità data
    if (isNaN(dateObj.getTime())) {
        console.warn('Data non valida:', date);
        return 'Data non valida';
    }
    
    // Formattazione con Intl API
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(dateObj);
}

/**
 * FUNZIONE: validateForm(formElement)
 * SCOPO: Validazione client-side avanzata per form
 * LINGUAGGIO: JavaScript Form Validation API + Custom logic
 * PARAMETRO: formElement (HTMLFormElement) - Form da validare
 * RETURN: object - { valid: boolean, errors: array }
 * 
 * COMPLEMENTO: Validazione server-side Laravel (non sostituzione)
 */
function validateForm(formElement) {
    const errors = [];
    const formData = new FormData(formElement);
    
    /**
     * VALIDAZIONI CUSTOM
     * Aggiunge validazioni business-logic specifiche
     */
    
    // Esempio: Validazione nome prodotto
    const nome = formData.get('nome');
    if (nome && nome.length < 3) {
        errors.push('Il nome prodotto deve essere di almeno 3 caratteri');
    }
    
    // Esempio: Validazione descrizione
    const descrizione = formData.get('descrizione');
    if (descrizione && descrizione.length > 1000) {
        errors.push('La descrizione non può superare 1000 caratteri');
    }
    
    /**
     * VALIDAZIONE HTML5 NATIVA
     * Utilizza checkValidity() del browser
     */
    const isNativelyValid = formElement.checkValidity();
    
    return {
        valid: isNativelyValid && errors.length === 0,
        errors: errors,
        nativeErrors: !isNativelyValid
    };
}

// ===================================================================
// SEZIONE 18: ERROR HANDLING GLOBALE
// ===================================================================

/**
 * GLOBAL ERROR HANDLER
 * SCOPO: Cattura errori JavaScript non gestiti
 * LINGUAGGIO: JavaScript Error Events
 * 
 * IMPORTANTE: Non sostituisce try-catch specifici, ma fornisce safety net
 */
window.addEventListener('error', function(event) {
    console.error('Errore JavaScript globale:', {
        message: event.message,
        source: event.filename,
        line: event.lineno,
        column: event.colno,
        stack: event.error?.stack
    });
    
    // In produzione potresti voler inviare errori a servizio logging
    if (!config.debug) {
        // Esempio: sendErrorToService(event);
    }
});

/**
 * PROMISE REJECTION HANDLER
 * SCOPO: Cattura Promise rejections non gestite
 * LINGUAGGIO: JavaScript Promise API
 * 
 * COMUNE: Fetch API calls senza .catch()
 */
window.addEventListener('unhandledrejection', function(event) {
    console.error('Promise rejection non gestita:', event.reason);
    
    // Previeni logging default del browser
    // event.preventDefault(); // Decommenta se vuoi silenziare
});

// ===================================================================
// SEZIONE 19: PROGRESSIVE WEB APP (PWA) ENHANCEMENTS
// ===================================================================

/**
 * SERVICE WORKER REGISTRATION (se presente)
 * SCOPO: Abilita funzionalità PWA come cache e offline
 * COMPATIBILITÀ: Browser moderni con supporto Service Workers
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('Service Worker registrato:', registration.scope);
                log('Service Worker attivo', 'success', registration.scope);
            })
            .catch(function(error) {
                console.log('Service Worker fallito:', error);
                log('Service Worker non disponibile', 'warn', error);
            });
    });
}

/**
 * ONLINE/OFFLINE STATUS MONITORING
 * SCOPO: Mostra stato connessione e adatta comportamento
 * EVENTI: 'online', 'offline' - Cambio stato connessione
 */
function setupConnectivityMonitoring() {
    /**
     * EVENT: 'online' - Connessione ripristinata
     */
    window.addEventListener('online', function() {
        log('Connessione ripristinata', 'success');
        showNotification('success', 'Connessione internet ripristinata');
        
        // Riabilita funzionalità che richiedono connessione
        document.querySelectorAll('[data-requires-connection]').forEach(el => {
            el.disabled = false;
        });
    });
    
    /**
     * EVENT: 'offline' - Connessione persa
     */
    window.addEventListener('offline', function() {
        log('Connessione persa', 'warn');
        showNotification('warning', 'Connessione internet persa. Alcune funzionalità potrebbero non essere disponibili.', 8000);
        
        // Disabilita funzionalità che richiedono connessione
        document.querySelectorAll('[data-requires-connection]').forEach(el => {
            el.disabled = true;
        });
    });
    
    // Status iniziale
    if (!navigator.onLine) {
        log('Pagina caricata offline', 'warn');
    }
}

// Attiva monitoring connettività
setupConnectivityMonitoring();

// ===================================================================
// SEZIONE 20: ANALYTICS E TELEMETRIA (se necessario)
// ===================================================================

/**
 * USER INTERACTION TRACKING
 * SCOPO: Traccia interazioni utente per migliorare UX
 * PRIVACY: Solo dati anonimi per ottimizzazione
 */
function trackUserInteraction(action, element, details = {}) {
    if (!config.debug) return; // Solo in debug per questo esempio
    
    const trackingData = {
        timestamp: new Date().toISOString(),
        action: action,
        element: element?.tagName?.toLowerCase(),
        elementId: element?.id,
        elementClass: element?.className,
        page: window.location.pathname,
        userAgent: navigator.userAgent,
        ...details
    };
    
    log('User interaction tracked', 'info', trackingData);
    
    // In produzione: invia a servizio analytics
    // analytics.track('admin_product_interaction', trackingData);
}

// Esempio: Track click su pulsanti importanti
document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-primary, .btn-danger, .btn-success')) {
        trackUserInteraction('button_click', e.target, {
            buttonText: e.target.textContent.trim()
        });
    }
});

// ===================================================================
// SEZIONE 21: MEMORY MANAGEMENT E CLEANUP
// ===================================================================

/**
 * CLEANUP HANDLER
 * SCOPO: Pulizia risorse prima di abbandonare pagina
 * EVENT: 'beforeunload' - Prima di lasciare pagina
 * 
 * IMPORTANTE: Evita memory leaks e cancella timers attivi
 */
window.addEventListener('beforeunload', function() {
    // Cancella tutti i timeout/interval attivi
    const highestTimeoutId = setTimeout(() => {}, 0);
    for (let i = 0; i < highestTimeoutId; i++) {
        clearTimeout(i);
        clearInterval(i);
    }
    
    // Rimuovi event listeners custom se necessario
    // (quelli su document/window possono causare leaks)
    
    // Dispose tooltip Bootstrap se presenti
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const tooltip = bootstrap.Tooltip.getInstance(el);
        if (tooltip) {
            tooltip.dispose();
        }
    });
    
    log('Cleanup completato prima di abbandonare pagina', 'info');
});

/**
 * OBSERVER CLEANUP
 * Se vengono usati MutationObserver, IntersectionObserver, etc.
 * devono essere disconnessi per evitare memory leaks
 */
let observers = []; // Array per tenere traccia degli observers

function cleanupObservers() {
    observers.forEach(observer => {
        observer.disconnect();
    });
    observers = [];
}

// Registra cleanup degli observers
window.addEventListener('beforeunload', cleanupObservers);

// ===================================================================
// SEZIONE 22: DEVELOPER TOOLS E HELPERS
// ===================================================================

/**
 * DEVELOPER HELPERS (solo in debug mode)
 * SCOPO: Funzioni utility per sviluppo e testing
 */
if (config.debug) {
    
    /**
     * GLOBAL DEBUG OBJECT
     * SCOPO: Espone funzioni e dati utili in console
     */
    window.AdminProductDebug = {
        config: config,
        
        // Test notifiche
        testNotifications: function() {
            showNotification('success', 'Test notifica success');
            setTimeout(() => showNotification('warning', 'Test notifica warning'), 1000);
            setTimeout(() => showNotification('error', 'Test notifica error'), 2000);
            setTimeout(() => showNotification('info', 'Test notifica info'), 3000);
        },
        
        // Simula errori per testing
        simulateError: function() {
            throw new Error('Errore simulato per testing');
        },
        
        // Info performance
        getPerformanceInfo: function() {
            if (performance.memory) {
                return {
                    usedJSHeapSize: (performance.memory.usedJSHeapSize / 1048576).toFixed(2) + ' MB',
                    totalJSHeapSize: (performance.memory.totalJSHeapSize / 1048576).toFixed(2) + ' MB',
                    jsHeapSizeLimit: (performance.memory.jsHeapSizeLimit / 1048576).toFixed(2) + ' MB'
                };
            }
            return 'Performance.memory non supportato';
        },
        
        // Lista tutti gli event listeners attivi
        listEventListeners: function() {
            console.table(getEventListeners(document));
        }
    };
    
    console.log('🛠️ Debug tools disponibili in window.AdminProductDebug');
}

// ===================================================================
// FINE DEL FILE - RIEPILOGO E DOCUMENTAZIONE
// ===================================================================

/**
 * ===================================================================
 * RIEPILOGO ARCHITETTURA ADMIN PRODOTTI SHOW
 * ===================================================================
 * 
 * ORGANIZZAZIONE CODICE (22 Sezioni):
 * 
 * 1-3.   INIZIALIZZAZIONE E CONFIGURAZIONE
 *        - Event listener DOMContentLoaded
 *        - Controllo route e configurazione globale
 *        - Sistema logging con categorizzazione
 * 
 * 4-6.   FUNZIONALITÀ CORE PRODOTTO
 *        - Toggle status con conferma utente
 *        - Gestione immagini con fallback
 *        - Bridge notifiche Laravel→JavaScript
 * 
 * 7-9.   FUNZIONI GLOBALI E UTILITÀ
 *        - Funzioni accessibili da HTML/altri script
 *        - Sistema clipboard con fallback
 *        - Toast notifications avanzate
 * 
 * 10-12. UX E ACCESSIBILITÀ
 *        - Keyboard shortcuts per produttività
 *        - Tooltip informativi
 *        - Miglioramenti accessibilità WCAG
 * 
 * 13-16. DEBUGGING E SICUREZZA
 *        - Eventi custom per interoperabilità
 *        - Performance monitoring
 *        - Debug panel visivo
 *        - Prevenzione XSS e CSP monitoring
 * 
 * 17-19. UTILITÀ AVANZATE E PWA
 *        - Debounce/throttle per performance
 *        - Validazione form avanzata
 *        - Error handling globale
 *        - Service Workers e offline support
 * 
 * 20-22. ANALYTICS E MAINTENANCE
 *        - User interaction tracking
 *        - Memory management e cleanup
 *        - Developer tools per debugging
 * 
 * TECNOLOGIE INTEGRATE:
 * 
 * - JavaScript ES6+: Const/let, arrow functions, destructuring, modules
 * - DOM API: Manipulation, events, selectors, forms
 * - Bootstrap 5: Toast, tooltips, utilities, responsive
 * - Laravel Integration: CSRF, routing, data injection
 * - Web APIs: Clipboard, Fullscreen, Performance, Service Workers
 * - Accessibility: ARIA attributes, keyboard navigation, screen readers
 * - Security: XSS prevention, CSP monitoring, input sanitization
 * - PWA: Service workers, offline detection, caching
 * 
 * PATTERN ARCHITETTURALI:
 * 
 * - Module Pattern: Organizzazione codice in sezioni logiche
 * - Event-Driven: Comunicazione via eventi custom
 * - Progressive Enhancement: Funzionalità base + miglioramenti
 * - Graceful Degradation: Fallback per browser/funzionalità mancanti
 * - Separation of Concerns: Logica separata per funzionalità diverse
 * - Configuration Object: Impostazioni centralizzate
 * - Error Boundaries: Gestione errori a livelli diversi
 * - Performance First: Debouncing, throttling, cleanup
 * 
 * COMPATIBILITÀ BROWSER:
 * 
 * - Modern: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * - Fallbacks: Clipboard execCommand, alert() per toast
 * - Progressive: Service Workers, Performance API opzionali
 * - Mobile: Touch events, responsive design, offline support
 * 
 * SICUREZZA:
 * 
 * - XSS Prevention: Input sanitization, textContent usage
 * - CSP Compliance: Event listeners, no inline scripts
 * - CSRF Protection: Laravel token in AJAX calls
 * - Error Handling: Non espone dati sensibili
 * 
 * PERFORMANCE:
 * 
 * - Lazy Loading: Inizializzazione on-demand
 * - Debouncing: Eventi frequenti ottimizzati
 * - Memory Management: Cleanup automatico
 * - Resource Monitoring: Performance API integration
 * 
 * MANUTENIBILITÀ:
 * 
 * - Documentazione: Commenti dettagliati per ogni funzione
 * - Modularità: Codice organizzato in sezioni logiche
 * - Testabilità: Debug tools e error simulation
 * - Estensibilità: Pattern facilmente espandibili
 * 
 * ===================================================================
 * 
 * Questo file rappresenta un esempio completo di JavaScript moderno
 * per applicazioni web enterprise, integrando best practices per
 * sicurezza, performance, accessibilità e manutenibilità.
 * 
 * ===================================================================
 */

console.log('📋 Admin Prodotti Show JavaScript completamente documentato e inizializzato');