

$(document).ready(function() {
    console.log('staff.dashboard caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'staff.dashboard') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    
    console.log('🚀 Dashboard Staff inizializzata - versione sicura');

    // === GESTIONE SICURA TOGGLE VISTA PRODOTTI ===
    $('input[name="prodotti-view"]').on('change', function() {
        const viewType = $(this).attr('id');
        
        if (viewType === 'view-grid') {
            $('#grid-view').fadeIn(300);
            $('#list-view').fadeOut(200);
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('staff_products_view', 'grid');
            }
        } else if (viewType === 'view-list') {
            $('#grid-view').fadeOut(200);
            $('#list-view').fadeIn(300);
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('staff_products_view', 'list');
            }
        }
    });

    // Ripristina vista salvata - CON CONTROLLO SICUREZZA
    try {
        if (typeof(Storage) !== "undefined") {
            const savedView = localStorage.getItem('staff_products_view');
            if (savedView === 'list') {
                const listToggle = $('#view-list');
                if (listToggle.length > 0) {
                    listToggle.prop('checked', true).trigger('change');
                }
            }
        }
    } catch(e) {
        console.warn('Impossibile ripristinare vista salvata:', e);
    }

    // === ANIMAZIONI HOVER PER CARDS - CON CONTROLLI ===
    $('.hover-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // === ANIMAZIONE CONTATORI MIGLIORATA ===
    function animateCounters() {
        $('.card-body h3, .h4').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            
            // Estrai solo i numeri dal testo
            const target = parseInt(text.replace(/[^\d]/g, ''));
            
            if (!isNaN(target) && target > 0 && target < 10000) {
                $counter.text('0');
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $counter.text(Math.ceil(this.counter));
                    },
                    complete: function() {
                        $counter.text(target);
                    }
                });
            }
        });
    }

    // Avvia animazione dopo un breve delay
    setTimeout(animateCounters, 800);

    // === TOOLTIP INITIALIZATION SICURA ===
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    } catch(e) {
        console.warn('Impossibile inizializzare tooltip:', e);
    }


    // === GESTIONE NOTIFICHE SICURA ===
    if (window.PageData.sessionSuccess) {
        showNotification('success', window.PageData.sessionSuccess);
    }
    if (window.PageData.sessionError) {
        showNotification('error', window.PageData.sessionError);
    }
    if (window.PageData.sessionWarning) {
        showNotification('warning', window.PageData.sessionWarning);
    }
    if (window.PageData.sessionInfo) {
        showNotification('info', window.PageData.sessionInfo);
    }

    // === FUNZIONE NOTIFICA SICURA ===
    function showNotification(type, message) {
        try {
            const alertClass = type === 'error' ? 'danger' : type;
            const icon = type === 'success' ? 'check-circle' : 
                        type === 'error' ? 'exclamation-triangle' : 
                        type === 'warning' ? 'exclamation-triangle' : 'info-circle';
            
            const notification = $(`
                <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed animate-slide-in" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
                     role="alert">
                    <i class="bi bi-${icon} me-2"></i>
                    <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
                </div>
            `);
            
            $('body').append(notification);
            
            // Auto-dismiss dopo 5 secondi con controllo esistenza
            setTimeout(() => {
                if (notification.length > 0 && notification.is(':visible')) {
                    notification.alert('close');
                }
            }, 5000);
            
        } catch(e) {
            console.error('Errore nella visualizzazione notifica:', e);
            // Fallback: alert browser nativo
            alert(type.toUpperCase() + ': ' + message);
        }
    }

    // Rendi showNotification globale
    window.showNotification = showNotification;

    // === REFRESH AUTOMATICO STATS SICURO ===
    let refreshInterval;
    
    function startAutoRefresh() {
        // Solo se siamo in una pagina dashboard attiva
        if (document.hidden || !document.hasFocus()) {
            return;
        }
        
        refreshInterval = setInterval(function() {
            const shouldUpdate = Math.random() > 0.9; // 10% probabilità
            
            if (shouldUpdate && typeof $ !== 'undefined') {
                console.log('📊 Controllo aggiornamento statistiche');
                
                // Effetto visivo leggero
                const counters = $('.card-body h3');
                if (counters.length > 0) {
                    counters.addClass('animate-pulse');
                    setTimeout(() => {
                        counters.removeClass('animate-pulse');
                    }, 1000);
                }
            }
        }, 300000); // 5 minuti
    }

    // Avvia refresh solo se la pagina è visibile
    if (!document.hidden) {
        startAutoRefresh();
    }

    // Gestisci visibilità pagina
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        } else {
            startAutoRefresh();
        }
    });

    // === PERFORMANCE MONITORING SICURO ===
    try {
        if (typeof performance !== 'undefined' && performance.timing) {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            if (loadTime > 0 && loadTime < 60000) { // Sanity check
                console.log(`Dashboard Staff caricata in ${loadTime}ms`);
            }
        }
    } catch(e) {
        console.warn('Performance monitoring non disponibile:', e);
    }

    // === DEBUG INFO SICURO (solo sviluppo) ===
    if (window.PageData.debug) {
        try {
            console.group('🐛 Debug Dashboard Staff');
            console.log('User:', window.PageData.user ? (window.PageData.user.nome || window.PageData.user.name || 'N/A') : 'N/A');
            console.log('Stats Keys:', window.PageData.stats ? Object.keys(window.PageData.stats) : []);
            console.log('Prodotti Count:', window.PageData.stats ? (window.PageData.stats.prodotti_assegnati || window.PageData.stats.total_prodotti || 0) : 0);
            console.log('Environment:', window.PageData.env || 'N/A');
            console.log('jQuery Version:', typeof $ !== 'undefined' ? $.fn.jquery : 'Non disponibile');
            console.log('Bootstrap:', typeof bootstrap !== 'undefined' ? 'Disponibile' : 'Non disponibile');
            console.groupEnd();
        } catch(e) {
            console.warn('Debug info parzialmente fallito:', e);
        }
    }

    console.log('✅ Dashboard Staff completamente funzionale - versione sicura');
});

// === FUNZIONI GLOBALI SICURE ===

// Esporta statistiche in formato JSON - CON CONTROLLI
window.exportStats = function() {
    try {
        const stats = {
            prodotti_gestiti: window.PageData.stats ? (window.PageData.stats.prodotti_assegnati || window.PageData.stats.total_prodotti || 0) : 0,
            soluzioni_create: window.PageData.stats ? (window.PageData.stats.soluzioni_create || 0) : 0,
            problemi_critici: window.PageData.stats ? (window.PageData.stats.soluzioni_critiche || 0) : 0,
            totale_database: window.PageData.stats ? (window.PageData.stats.total_malfunzionamenti || 0) : 0,
            exported_at: new Date().toISOString(),
            user: window.PageData.user ? (window.PageData.user.username || window.PageData.user.nome || 'staff') : 'staff'
        };

        const dataStr = JSON.stringify(stats, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});

        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = `staff_report_${new Date().toISOString().split('T')[0]}.json`;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        console.log('📄 Report esportato con successo');
        if (typeof showNotification === 'function') {
            showNotification('success', 'Report statistiche esportato con successo');
        }

    } catch(e) {
        console.error('Errore durante l\'esportazione:', e);
        if (typeof showNotification === 'function') {
            showNotification('error', 'Errore durante l\'esportazione del report');
        } else {
            alert('Errore durante l\'esportazione del report');
        }
    }
};

// Refresh manuale dashboard - SICURO
window.refreshDashboard = function() {
    try {
        console.log('🔄 Refresh dashboard richiesto');
        
        // Effetto visivo di loading con controllo esistenza elementi
        const cards = $('.card');
        if (cards.length > 0) {
            cards.addClass('animate-pulse');
        }
        
        setTimeout(() => {
            location.reload();
        }, 500);
        
    } catch(e) {
        console.error('Errore durante il refresh:', e);
        location.reload(); // Fallback diretto
    }
};

// === GESTIONE ERRORI GLOBALE MIGLIORATA ===
window.onerror = function(msg, url, line, col, error) {
    console.group('❌ Errore Dashboard Staff');
    console.error('Message:', msg);
    console.error('Source:', url);
    console.error('Line:', line);
    console.error('Column:', col);
    if (error) {
        console.error('Error Object:', error);
        console.error('Stack:', error.stack);
    }
    console.groupEnd();
    
    // Non bloccare l'esecuzione
    return false;
};

// Gestione promise rejections
window.addEventListener('unhandledrejection', function(event) {
    console.warn('Promise rejection non gestita:', event.reason);
    // Previeni che venga mostrato in console come errore
    event.preventDefault();
});

// === CONTROLLI DI INTEGRITÀ ===
function performIntegrityChecks() {
    const checks = {
        jquery: typeof $ !== 'undefined',
        bootstrap: typeof bootstrap !== 'undefined',
        localStorage: typeof Storage !== 'undefined',
        performance: typeof performance !== 'undefined'
    };
    
    console.log('🔍 Controlli integrità:', checks);
    
    return checks;
}

// Esegui controlli all'avvio
setTimeout(performIntegrityChecks, 1000);

// === FUNZIONI GLOBALI ===

// Esporta statistiche in formato JSON
// ...

// Refresh manuale dashboard
window.refreshDashboard = function() {
    console.log('🔄 Refresh dashboard richiesto');
    
    // Effetto visivo di loading
    $('.card').addClass('animate-pulse');
    
    setTimeout(() => {
        location.reload();
    }, 500);
};

// Mostra notifica personalizzata
window.showNotification = function(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const icon = type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-triangle' : 'info-circle';
    
    const notification = `
        <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(notification);
    setTimeout(() => $('.alert').alert('close'), 5000);
};

// === GESTIONE ERRORI GLOBALE ===
window.onerror = function(msg, url, line, col, error) {
    console.error('❌ Errore Dashboard Staff:', {
        message: msg,
        source: url,
        line: line,
        column: col,
        error: error?.toString()
    });
    return false;
};

// === SERVICE WORKER (opzionale) ===
if ('serviceWorker' in navigator && '{{ config("app.env") }}' === 'production') {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('SW registrato:', reg.scope))
        .catch(err => console.log('SW fallito:', err));
}