/**
 * ===================================================================
 * TECNICO STATISTICHE - JavaScript per Visualizzazione Dati Tecnico
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/tecnico/statistiche.js
 * 
 * FUNZIONALITÀ:
 * - Grafici personalizzati per tecnici
 * - Visualizzazione performance personali
 * - Monitoraggio trend e gravità
 * - Layout compatto e responsive
 * ===================================================================
 */

// === VARIABILI GLOBALI ===
window.graficoGravita = window.graficoGravita || null;
window.graficoTrend = window.graficoTrend || null;
window.graficoCategorie = window.graficoCategorie || null;
let isChartsInitialized = false;

// === CONFIGURAZIONI ===
const CONFIG = {
    AUTO_REFRESH_INTERVAL: 600000, // 10 minuti
    CHART_HEIGHT: 120, // Altezza grafici compatti
    ANIMATION_DURATION: 1500 // Durata animazioni contatori
};

// === CONFIGURAZIONE COMUNE GRAFICI ===
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false // Nasconde legenda per layout compatto
        },
        tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: '#ffffff',
            borderWidth: 1
        }
    },
    elements: {
        point: {
            radius: 3, // Punti più piccoli per grafici compatti
            hoverRadius: 5
        }
    },
    scales: {
        x: {
            display: false // Nasconde asse X per layout compatto
        },
        y: {
            display: false, // Nasconde asse Y per layout compatto
            beginAtZero: true,
            ticks: {
                stepSize: 1 // Incrementi di 1 unità
            }
        }
    }
};

// === INIZIALIZZAZIONE PRINCIPALE ===
$(document).ready(function() {
    console.log('tecnico.statistiche caricato');
    
    // Verifica route corretta
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'tecnico.statistiche.view' && !window.location.href.includes('statistiche')) {
        console.log('Route non corretta per statistiche tecnico');
        return;
    }
    
    // Inizializza sistema
    initializeTechnicianStats();
    setupEventListeners();
    
    console.log('Statistiche Tecnico - Layout Compatto inizializzato');
});

// === INIZIALIZZAZIONE SISTEMA ===
function initializeTechnicianStats() {
    console.log('Inizializzazione statistiche tecnico...');
    
    // Avvia animazioni contatori
    setTimeout(() => {
        animateCounters();
    }, 500);
    
    // Inizializza grafici dopo delay per assicurare DOM pronto
    setTimeout(() => {
        initializeAllCharts();
    }, 1000);
    
    // Setup auto-refresh
    setupAutoRefresh();
}

// === INIZIALIZZAZIONE GRAFICI ===
function initializeAllCharts() {
    console.log('Inizializzazione grafici tecnico...');
    
    try {
        initGravityChart();
        initTrendChart(); 
        initCategoryChart();
        
        isChartsInitialized = true;
        console.log('Tutti i grafici tecnico inizializzati');
    } catch (error) {
        console.error('Errore inizializzazione grafici tecnico:', error);
        showNotification('Errore caricamento grafici', 'error');
    }
}

// === GRAFICO GRAVITÀ ===
function initGravityChart() {
    const canvas = document.getElementById('graficoGravita');
    if (!canvas) {
        console.warn('Canvas graficoGravita non trovato');
        return;
    }

    // Recupera dati da window object (passati dalla vista Blade)
    const graviताData = window.statsData?.malfunzionamenti?.per_gravita || {};
    
    // Prepara dati per grafico
    const labels = [];
    const values = [];
    const colors = ['#dc3545', '#ffc107', '#28a745', '#17a2b8']; // rosso, giallo, verde, azzurro
    
    // Ordine gravità
    const gravitaOrder = ['critica', 'alta', 'media', 'bassa'];
    
    gravitaOrder.forEach(gravita => {
        if (graviताData[gravita] !== undefined) {
            labels.push(gravita.charAt(0).toUpperCase() + gravita.slice(1));
            values.push(parseInt(graviताData[gravita]) || 0);
        }
    });
    
    // Se non ci sono dati, usa dati di fallback
    if (values.length === 0) {
        console.warn('Nessun dato gravità - usando fallback');
        labels.push('Critica', 'Alta', 'Media', 'Bassa');
        values.push(0, 0, 0, 0);
    }
    
    // Crea grafico doughnut
    window.graficoGravita = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            ...commonOptions,
            cutout: '50%', // Centro vuoto per doughnut
            plugins: {
                ...commonOptions.plugins,
                tooltip: {
                    ...commonOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('Grafico gravità creato:', labels, values);
}

// === GRAFICO TREND ===
function initTrendChart() {
    const canvas = document.getElementById('graficoTrend');
    if (!canvas) {
        console.warn('Canvas graficoTrend non trovato');
        return;
    }

    // Recupera dati trend settimanale
    const trendData = window.statsData?.trend_settimanale || {};
    
    let labels = trendData.giorni || [];
    let values = trendData.conteggi || [];
    
    // Se non ci sono dati, genera fallback per ultimi 7 giorni
    if (labels.length === 0 || values.length === 0) {
        console.warn('Nessun dato trend - usando fallback');
        labels = [];
        values = [];
        
        // Genera ultimi 7 giorni
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' }));
            values.push(Math.floor(Math.random() * 5)); // Dati casuali per demo
        }
    }
    
    // Crea grafico a linee
    window.graficoTrend = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Soluzioni',
                data: values,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            ...commonOptions,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                ...commonOptions.plugins,
                tooltip: {
                    ...commonOptions.plugins.tooltip,
                    callbacks: {
                        title: function(context) {
                            return `Giorno: ${context[0].label}`;
                        },
                        label: function(context) {
                            return `Soluzioni: ${context.parsed.y}`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('Grafico trend creato:', labels.length, 'giorni');
}

// === GRAFICO CATEGORIE ===
function initCategoryChart() {
    const canvas = document.getElementById('graficoCategorie');
    if (!canvas) {
        console.warn('Canvas graficoCategorie non trovato');
        return;
    }

    // Recupera dati categorie
    const categorieData = window.statsData?.per_categoria || {};
    
    const labels = Object.keys(categorieData).map(categoria => 
        categoria.charAt(0).toUpperCase() + categoria.slice(1)
    );
    const values = Object.values(categorieData).map(val => parseInt(val) || 0);
    const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'];
    
    // Se non ci sono dati, usa fallback
    if (values.length === 0) {
        console.warn('Nessun dato categorie - usando fallback');
        labels.push('Elettrodomestici', 'Informatica');
        values.push(0, 0);
    }
    
    // Crea grafico a barre
    window.graficoCategorie = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Prodotti per Categoria',
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: colors.slice(0, labels.length).map(color => color + 'CC'),
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                tooltip: {
                    ...commonOptions.plugins.tooltip,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `Prodotti: ${context.parsed.y}`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('Grafico categorie creato:', labels, values);
}

// === ANIMAZIONE CONTATORI ===
function animateCounters() {
    console.log('Avvio animazione contatori tecnico...');
    
    // Seleziona tutti i contatori numerici
    $('.h5.fw-bold').each(function() {
        const $counter = $(this);
        const text = $counter.text().trim();
        
        // Estrae numero dal testo
        const targetMatch = text.match(/\d+/);
        if (!targetMatch) return;
        
        const target = parseInt(targetMatch[0]);
        
        // Anima numeri ragionevoli
        if (!isNaN(target) && target > 0 && target < 1000) {
            $counter.text('0');
            
            // Animazione jQuery
            $({ counter: 0 }).animate({ counter: target }, {
                duration: CONFIG.ANIMATION_DURATION,
                easing: 'swing',
                step: function() {
                    $counter.text(Math.ceil(this.counter));
                },
                complete: function() {
                    $counter.text(target);
                    console.log(`Contatore animato: ${target}`);
                }
            });
        }
    });
}

// === EVENT LISTENERS ===
function setupEventListeners() {
    console.log('Setup event listeners tecnico...');
    
    // Gestione escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
            $('.dropdown-menu').removeClass('show');
        }
    });
}

// === AUTO-REFRESH ===
function setupAutoRefresh() {
    console.log('Setup auto-refresh statistiche tecnico');
    
    // Auto-refresh ogni 10 minuti
    setInterval(function() {
        console.log('Auto-refresh statistiche tecnico');
        // In produzione implementare chiamata AJAX
        updateTimestamp();
    }, CONFIG.AUTO_REFRESH_INTERVAL);
}

// === UTILITY FUNCTIONS ===

/**
 * Aggiorna statistiche (chiamata dal pulsante)
 */
function aggiornaStatistiche() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    setTimeout(function() {
        location.reload();
    }, 1000);
}

/**
 * Aggiorna timestamp corrente
 */
function updateTimestamp() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('it-IT', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    console.log('Timestamp aggiornato:', timeString);
}

/**
 * Mostra notifica toast
 */
function showNotification(message, type) {
    type = type || 'success';
    
    $('.toast-notification').remove();
    
    const alertClasses = {
        'success': 'alert-success',
        'error': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const icons = {
        'success': 'check-circle-fill',
        'error': 'exclamation-triangle-fill',
        'warning': 'exclamation-triangle-fill', 
        'info': 'info-circle-fill'
    };
    
    const alertClass = alertClasses[type] || 'alert-info';
    const icon = icons[type] || 'info-circle-fill';
    
    const toast = $(`
        <div class="toast-notification alert ${alertClass} alert-dismissible fade show position-fixed shadow-lg" 
             style="top: 20px; right: 20px; z-index: 10000; max-width: 350px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-${icon} me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>${type.charAt(0).toUpperCase() + type.slice(1)}</strong><br>
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    console.log(`Notifica mostrata (${type}):`, message);
    
    // Auto-dismiss
    setTimeout(function() {
        toast.fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

/**
 * Verifica se è dispositivo mobile
 */
function isMobileDevice() {
    return window.innerWidth <= 768;
}

/**
 * Formatta numeri per display
 */
function formatNumber(num) {
    if (isNaN(num)) return '0';
    
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    } else {
        return num.toString();
    }
}

// === CLEANUP ===
$(window).on('beforeunload', function() {
    // Distruggi grafici per liberare memoria
    if (isChartsInitialized) {
    [window.graficoGravita, window.graficoTrend, window.graficoCategorie].forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        console.log('Grafici tecnico distrutti');
    }
    
    // Rimuovi notifiche
    $('.toast-notification').remove();
});

// === ESPORTAZIONE FUNZIONI GLOBALI ===

// Espone funzioni globalmente per retrocompatibilità
window.aggiornaStatistiche = aggiornaStatistiche;
window.showNotification = showNotification;

// Oggetto debug per sviluppo
window.TechnicianStats = {
    charts: {
    window.graficoGravita,
    window.graficoTrend,
    window.graficoCategorie,
    },
    config: CONFIG,
    utils: {
        formatNumber,
        updateTimestamp,
        showNotification
    }
};

console.log('Statistiche Tecnico JavaScript caricato completamente');