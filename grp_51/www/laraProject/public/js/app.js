/**
 * TechSupport Pro - JavaScript Core
 * Sistema di Assistenza Tecnica Online
 * Gruppo 51 - Tecnologie Web 2024/2025
 */



class TechSupportApp {
    constructor() {
        this.initializeCore();
        this.setupCSRF();
        this.initializeGlobalComponents();
        this.setupGlobalEventListeners();
    }

    /**
     * Inizializzazione core del sistema
     */
    initializeCore() {
        console.log('🚀 TechSupport Pro - Sistema inizializzato');
        
        // Configurazioni globali
        this.config = {
            toastDuration: 5000,
            ajaxTimeout: 10000,
            searchDebounce: 300
        };
    }

    /**
     * Setup CSRF token per tutte le richieste AJAX
     * Obbligatorio per la sicurezza Laravel
     */
    setupCSRF() {
        
        const token = $('meta[name="csrf-token"]').attr('content');
        
        if (token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                timeout: this.config.ajaxTimeout
            });
            console.log('✅ CSRF Token configurato');
        } else {
            console.warn('⚠️ CSRF Token non trovato');
        }
    }

    /**
     * Inizializza componenti Bootstrap e globali
     */
    initializeGlobalComponents() {
        // Tooltip Bootstrap
        this.initTooltips();
        
        // Popover Bootstrap
        this.initPopovers();
        
        // Auto-dismiss alerts
        this.setupAlertsDismiss();
        
        // Smooth scrolling
        this.setupSmoothScrolling();
    }

    /**
     * Inizializza tutti i tooltip
     */
    initTooltips() {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
    }

    /**
     * Inizializza tutti i popover
     */
    initPopovers() {
        $('[data-bs-toggle="popover"]').each(function() {
            new bootstrap.Popover(this);
        });
    }

    /**
     * Auto-dismissal degli alert dopo 5 secondi
     */
    setupAlertsDismiss() {
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, this.config.toastDuration);
    }

    /**
     * Smooth scrolling per anchor links
     */
    setupSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }

    /**
     * Event listeners globali
     */
    setupGlobalEventListeners() {
        // Conferme eliminazione
        $(document).on('click', '[data-confirm-delete]', this.handleDeleteConfirmation);
        
        // Prevenzione doppio submit
        $(document).on('submit', 'form', this.preventDoubleSubmit);
        
        // Gestione errori AJAX globali
        $(document).ajaxError(this.handleAjaxError);
    }

    /**
     * Gestisce le conferme di eliminazione
     */
    handleDeleteConfirmation(e) {
        e.preventDefault();
        
        const message = $(this).data('confirm-delete') || 'Sei sicuro di voler eliminare questo elemento?';
        const form = $(this).closest('form');
        
        if (confirm(message)) {
            form.submit();
        }
    }

    /**
     * Previene il doppio submit dei form
     */
    preventDoubleSubmit(e) {
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        if (form.data('submitted')) {
            e.preventDefault();
            return false;
        }
        
        form.data('submitted', true);
        submitBtn.prop('disabled', true);
        
        // Re-abilita dopo 3 secondi in caso di errori
        setTimeout(() => {
            form.data('submitted', false);
            submitBtn.prop('disabled', false);
        }, 3000);
    }

    /**
     * Gestione errori AJAX globali
     */
    handleAjaxError(event, jqXHR, ajaxSettings, thrownError) {
        console.error('Errore AJAX:', thrownError);
        
        let message = 'Errore di connessione';
        
        if (jqXHR.status === 403) {
            message = 'Non autorizzato';
        } else if (jqXHR.status === 404) {
            message = 'Risorsa non trovata';
        } else if (jqXHR.status === 500) {
            message = 'Errore del server';
        }
        
        TechSupportApp.showToast(message, 'danger');
    }

    // === UTILITY STATICHE ===

    /**
     * Mostra toast notification
     */
    static showToast(message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const toast = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Crea container se non esiste
        if (!$('#toast-container').length) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        
        const $toast = $(toast);
        $('#toast-container').append($toast);
        
        const toastInstance = new bootstrap.Toast($toast[0]);
        toastInstance.show();
        
        // Rimuovi quando nascosto
        $toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    /**
     * Mostra spinner di caricamento su elemento
     */
    static showSpinner(element) {
        const $el = $(element);
        const spinner = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
        
        $el.prop('disabled', true)
           .data('original-text', $el.html())
           .html(spinner + 'Caricamento...');
    }

    /**
     * Nasconde spinner di caricamento
     */
    static hideSpinner(element) {
        const $el = $(element);
        const originalText = $el.data('original-text');
        
        if (originalText) {
            $el.prop('disabled', false).html(originalText);
        }
    }

    /**
     * Formatta numeri in formato italiano
     */
    static formatNumber(num) {
        return new Intl.NumberFormat('it-IT').format(num);
    }

    /**
     * Debounce function per ottimizzare le ricerche
     */
    static debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }
}

// === INIZIALIZZAZIONE GLOBALE ===

// Inizializza quando DOM è pronto
$(document).ready(function() {
    // Crea istanza globale
    window.techSupportApp = new TechSupportApp();
    
    // Funzioni globali per compatibilità
    window.showToast = TechSupportApp.showToast;
    window.showSpinner = TechSupportApp.showSpinner;
    window.hideSpinner = TechSupportApp.hideSpinner;
    window.formatNumber = TechSupportApp.formatNumber;
    window.debounce = TechSupportApp.debounce;
    
    console.log('📱 TechSupport Pro pronto per l\'uso');
});

// === EXPORT PER MODULI ===
window.TechSupportApp = TechSupportApp;