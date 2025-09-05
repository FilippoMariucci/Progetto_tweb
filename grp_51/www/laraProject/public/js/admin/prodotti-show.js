

    
    
    // Il tuo codice JavaScript qui...

    document.addEventListener('DOMContentLoaded', function() {
        console.log('admin.prodotti.show caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.show') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // ===== CONFIGURAZIONE GLOBALE =====
    const config = {
        prodotto: {
            id: window.PageData?.prodotto?.id || null,
            nome: window.PageData?.prodotto?.nome || '',
            attivo: window.PageData?.prodotto?.attivo || false,
            staffAssegnato: window.PageData?.prodotto?.staffAssegnato || null
        },
        routes: {
            toggleStatus: window.PageData?.routes?.toggleStatus || '',
            show: window.PageData?.routes?.show || ''
        },
        debug: window.LaravelApp?.debug || false
    };
    
    // ===== LOGGING E DEBUG =====
    function log(message, type = 'info', data = null) {
        if (config.debug) {
            const timestamp = new Date().toLocaleTimeString();
            const prefix = `[${timestamp}] TechSupport Admin:`;
            
            switch(type) {
                case 'error':
                    console.error(prefix, message, data);
                    break;
                case 'warn':
                    console.warn(prefix, message, data);
                    break;
                case 'success':
                    console.log(`%c${prefix}`, 'color: green; font-weight: bold;', message, data);
                    break;
                default:
                    console.log(prefix, message, data);
            }
        }
    }
    
    log('Inizializzazione pagina dettaglio prodotto (versione semplificata)', 'info', config.prodotto);
    
    // ===== GESTIONE TOGGLE STATUS =====
    const toggleForms = document.querySelectorAll('form[action*="toggle-status"]');
    
    toggleForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const isActive = config.prodotto.attivo;
            
            if (!confirmToggleStatus(isActive)) {
                e.preventDefault();
                return false;
            }
            
            log(`Toggle status prodotto: ${isActive ? 'disattivare' : 'attivare'}`, 'info', {
                prodottoId: config.prodotto.id,
                currentStatus: isActive
            });
            
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Attendere...';
            }
        });
    });
    
    // ===== GESTIONE IMMAGINI =====
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this);
        });
        
        img.addEventListener('click', function() {
            // Modalità fullscreen per immagine (opzionale)
            if (this.requestFullscreen) {
                this.requestFullscreen();
            }
        });
    });
    
    // ===== NOTIFICAZIONI =====
    // Notifiche lato client (iniettate dal template Blade)
    if (window.LaravelNotifications) {
        if (window.LaravelNotifications.success) {
            showNotification('success', window.LaravelNotifications.success);
            log('Notifica success mostrata', 'success', window.LaravelNotifications.success);
        }
        if (window.LaravelNotifications.error) {
            showNotification('error', window.LaravelNotifications.error);
            log('Notifica error mostrata', 'error', window.LaravelNotifications.error);
        }
        if (Array.isArray(window.LaravelNotifications.errors)) {
            window.LaravelNotifications.errors.forEach(function(error) {
                showNotification('error', error);
                log('Errore validazione', 'error', error);
            });
        }
    }
});

// ===== FUNZIONI GLOBALI =====

/**
 * Conferma toggle status prodotto
 */
function confirmToggleStatus(isActive) {
    const action = isActive ? 'disattivare' : 'attivare';
    const message = `Sei sicuro di voler ${action} questo prodotto?`;
    
    if (isActive) {
        return confirm(`${message}\n\nSe disattivato, il prodotto non sarà più visibile nel catalogo pubblico.`);
    } else {
        return confirm(`${message}\n\nSe attivato, il prodotto tornerà visibile nel catalogo pubblico.`);
    }
}

/**
 * Gestione errori immagini
 */
function handleImageError(img) {
    // Sostituisci con il percorso assoluto o relativo corretto dell'immagine placeholder
    const placeholderUrl = '/images/placeholder-product.png';
    
    if (img.src !== placeholderUrl) {
        console.warn('🖼️ Errore caricamento immagine:', img.src);
        img.src = placeholderUrl;
        img.onerror = null; // Previeni loop infinito
        
        // Aggiungi classe per styling
        img.classList.add('image-error');
    }
}

/**
 * Copia testo negli appunti
 */
function copyToClipboard(text, successMessage = 'Testo copiato negli appunti!') {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('success', successMessage);
        }).catch(err => {
            console.error('Errore copia clipboard:', err);
            fallbackCopyTextToClipboard(text, successMessage);
        });
    } else {
        fallbackCopyTextToClipboard(text, successMessage);
    }
}

function fallbackCopyTextToClipboard(text, successMessage) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
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
    
    document.body.removeChild(textArea);
}

/**
 * Sistema di notificazioni toast
 */
function showNotification(type, message, duration = 5000) {
    // Controlla se Bootstrap Toast è disponibile
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';
        
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
        
        // Container per toast
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });
        
        toast.show();
        
        // Rimuovi elemento dopo che è stato nascosto
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    } else {
        // Fallback ad alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Gestione keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S per salvare (previeni default)
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        showNotification('info', 'Usa i pulsanti di modifica per salvare le modifiche');
        return false;
    }
    
    // Alt + E per modificare prodotto
    if (e.altKey && e.key === 'e') {
        e.preventDefault();
        const editBtn = document.querySelector('a[href*="edit"]');
        if (editBtn) {
            editBtn.click();
        }
    }
});

/**
 * Tooltips per keyboard shortcuts
 */
function initKeyboardTooltips() {
    const tooltips = [
        { selector: 'a[href*="edit"]', title: 'Modifica prodotto (Alt+E)' }
    ];
    
    tooltips.forEach(item => {
        const element = document.querySelector(item.selector);
        if (element && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(element, {
                title: item.title,
                placement: 'top'
            });
        }
    });
}

// Inizializza tooltips
initKeyboardTooltips();

/**
 * Accessibilità migliorata
 */
function improveAccessibility() {
    // Aggiungi aria-labels mancanti
    document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])').forEach(btn => {
        const text = btn.textContent.trim();
        if (text) {
            btn.setAttribute('aria-label', text);
        }
    });
    
    // Miglioramento focus per keyboard navigation
    document.querySelectorAll('.btn, .form-control, .form-select').forEach(el => {
        el.addEventListener('focus', function() {
            this.style.outline = '2px solid #007bff';
            this.style.outlineOffset = '2px';
        });
        
        el.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });
}

improveAccessibility();

// ===== INIZIALIZZAZIONE FINALE =====
console.log('🎉 Pagina admin prodotto (versione semplificata) completamente inizializzata');

// Notifica ready per altri script
window.dispatchEvent(new CustomEvent('adminProductPageReady', {
    detail: {
        prodotto: config.prodotto,
        routes: config.routes,
        timestamp: new Date().toISOString(),
        version: 'simplified'
    }
}));

if (config.debug) {
    /**
     * Controllo performance pagina (solo debug)
     */
    window.addEventListener('load', function() {
        if (performance && performance.timing) {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`⏱️ Pagina admin prodotto caricata in: ${loadTime}ms`);
            
            if (loadTime > 3000) {
                console.warn('🐌 Caricamento lento rilevato per pagina admin prodotto');
            }
        }
    });

    // Debug panel per sviluppo (versione ridotta)
    if (new URLSearchParams(window.location.search).get('debug') === '1') {
        const panel = document.createElement('div');
        panel.className = 'debug-panel';
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
        panel.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 5px; color: #ffc107;">🔧 DEBUG PANEL (Simplified)</div>
            <div>Prodotto: <span style="color: #28a745;">${config.prodotto.id}</span></div>
            <div>Staff: <span style="color: ${config.prodotto.staffAssegnato ? '#28a745' : '#dc3545'};">${config.prodotto.staffAssegnato || 'Non assegnato'}</span></div>
            <div>Bootstrap: <span style="color: ${typeof bootstrap !== 'undefined' ? '#28a745' : '#dc3545'};">${typeof bootstrap !== 'undefined' ? 'Caricato' : 'Mancante'}</span></div>
        `;
        document.body.appendChild(panel);
    }
}

// Prevenzione XSS nelle notificazioni
function sanitizeMessage(message) {
    const div = document.createElement('div');
    div.textContent = message;
    return div.innerHTML;
}

// Override showNotification per sicurezza
const originalShowNotification = window.showNotification;
window.showNotification = function(type, message, duration = 5000) {
    return originalShowNotification(type, sanitizeMessage(message), duration);
};

// Controllo CSP (Content Security Policy)
document.addEventListener('securitypolicyviolation', function(e) {
    console.error('Violazione CSP rilevata:', e.violatedDirective, e.blockedURI);
});
