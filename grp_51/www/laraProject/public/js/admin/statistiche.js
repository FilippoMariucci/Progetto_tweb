/**
 * ===================================================================
 * ADMIN STATISTICHE - JavaScript per Gestione Statistiche
 * ===================================================================
 * Sistema Assistenza Tecnica - Gruppo 51
 * File: public/js/admin/statistiche.js
 * 
 * FUNZIONALITÀ:
 * - Inizializzazione grafici Chart.js
 * - Caricamento dati statistiche
 * - Animazioni contatori
 * - Aggiornamento dati in tempo reale
 * - Gestione errori e fallback
 * ===================================================================
 */


// === VARIABILI GLOBALI ===
let graficoUtenti, graficoProdotti, graficoGravita, graficoCrescita;
let isChartsInitialized = false;
let dataUpdateInterval = null;

// === CONFIGURAZIONI ===
const CONFIG = {
    UPDATE_INTERVAL: 600000, // 10 minuti in millisecondi
    ANIMATION_DURATION: 1500, // Durata animazioni contatori
    CHART_HEIGHT: 120, // Altezza grafici compatti
    ENABLE_AUTO_REFRESH: true // Abilita aggiornamento automatico
};

// === CONFIGURAZIONE COMUNE GRAFICI ===
const commonChartOptions = {
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
    console.log('📊 Statistiche Admin - Inizializzazione...');
    
    // Verifica se siamo nella pagina corretta
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.statistiche' && !window.location.href.includes('statistiche')) {
        console.log('🚫 Route non corretta per statistiche admin');
        return;
    }

    // Inizializza sistema
    try {
        initializeStatistics();
        setupEventListeners();
        startAutoRefresh();
        console.log('✅ Sistema statistiche admin inizializzato');
    } catch (error) {
        console.error('❌ Errore inizializzazione:', error);
        showNotification('Errore di inizializzazione sistema', 'error');
    }
});

// === INIZIALIZZAZIONE SISTEMA ===
function initializeStatistics() {
    console.log('🔄 Inizializzazione grafici e animazioni...');
    
    // Avvia animazioni contatori
    setTimeout(() => {
        animateCounters();
    }, 500);
    
    // Inizializza grafici dopo un breve delay per assicurare DOM pronto
    setTimeout(() => {
        initializeAllCharts();
    }, 1000);
}

// === INIZIALIZZAZIONE GRAFICI ===
function initializeAllCharts() {
    console.log('📈 Inizializzazione grafici Chart.js...');
    
    try {
        initUsersChart();
        initProductsChart(); 
        initGravityChart();
        initGrowthChart();
        
        isChartsInitialized = true;
        console.log('✅ Tutti i grafici inizializzati con successo');
    } catch (error) {
        console.error('❌ Errore inizializzazione grafici:', error);
        showNotification('Errore caricamento grafici', 'error');
    }
}

// === GRAFICO DISTRIBUZIONE UTENTI ===
function initUsersChart() {
    const canvas = document.getElementById('graficoUtenti');
    if (!canvas) {
        console.warn('⚠️ Canvas graficoUtenti non trovato');
        return;
    }

    // Recupera dati dal window object (passati dal controller PHP)
    const distribuzioneUtenti = window.distribuzioneUtenti || {};
    
    // Prepara dati per il grafico
    const labels = [];
    const values = [];
    const colors = ['#6c757d', '#0dcaf0', '#ffc107', '#dc3545'];
    
    // Converte i dati del controller in formato grafico
    Object.entries(distribuzioneUtenti).forEach(([livello, count]) => {
        switch(livello) {
            case '1': 
                labels.push('Pubblico'); 
                break;
            case '2': 
                labels.push('Tecnici'); 
                break;
            case '3': 
                labels.push('Staff'); 
                break;
            case '4': 
                labels.push('Admin'); 
                break;
            default: 
                labels.push('Livello ' + livello); 
                break;
        }
        values.push(parseInt(count) || 0);
    });
    
    // Se non ci sono dati, mostra grafico vuoto
    if (values.length === 0) {
        console.warn('⚠️ Nessun dato per grafico utenti');
        return;
    }
    
    // Crea grafico doughnut compatto
    graficoUtenti = new Chart(canvas, {
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
            ...commonChartOptions,
            cutout: '50%', // Percentuale del centro vuoto
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('✅ Grafico utenti creato:', labels, values);
}

// === GRAFICO PRODOTTI PER CATEGORIA ===
function initProductsChart() {
    const canvas = document.getElementById('graficoProdotti');
    if (!canvas) {
        console.warn('⚠️ Canvas graficoProdotti non trovato');
        return;
    }

    // Recupera dati prodotti
    const prodottiPerCategoria = window.prodottiPerCategoria || {};
    
    const labels = Object.keys(prodottiPerCategoria).map(categoria => 
        categoria.charAt(0).toUpperCase() + categoria.slice(1)
    );
    const values = Object.values(prodottiPerCategoria).map(val => parseInt(val) || 0);
    
    if (values.length === 0) {
        console.warn('⚠️ Nessun dato per grafico prodotti');
        return;
    }
    
    // Crea grafico a barre compatto
    graficoProdotti = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Prodotti',
                data: values,
                backgroundColor: '#0dcaf0',
                borderColor: '#0aa2c0',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            ...commonChartOptions,
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
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
    
    console.log('✅ Grafico prodotti creato:', labels, values);
}

// === GRAFICO GRAVITÀ MALFUNZIONAMENTI ===
function initGravityChart() {
    const canvas = document.getElementById('graficoGravita');
    if (!canvas) {
        console.warn('⚠️ Canvas graficoGravita non trovato');
        return;
    }

    // Recupera dati gravità
    const malfunzionamentiPerGravita = window.malfunzionamentiPerGravita || {};
    
    const labels = [];
    const values = [];
    const colors = [];
    
    // Ordine e colori per le gravità
    const gravitaOrder = ['critica', 'alta', 'media', 'bassa'];
    const gravitaColors = {
        'critica': '#dc3545',
        'alta': '#ffc107', 
        'media': '#0dcaf0',
        'bassa': '#198754'
    };
    
    // Costruisce dati in ordine di gravità
    gravitaOrder.forEach(gravita => {
        if (malfunzionamentiPerGravita[gravita]) {
            labels.push(gravita.charAt(0).toUpperCase() + gravita.slice(1));
            values.push(parseInt(malfunzionamentiPerGravita[gravita]) || 0);
            colors.push(gravitaColors[gravita]);
        }
    });
    
    if (values.length === 0) {
        console.warn('⚠️ Nessun dato per grafico gravità');
        return;
    }
    
    // Crea grafico a torta
    graficoGravita = new Chart(canvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 6
            }]
        },
        options: {
            ...commonChartOptions,
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} problemi (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('✅ Grafico gravità creato:', labels, values);
}

// === GRAFICO CRESCITA TEMPORALE ===
function initGrowthChart() {
    const canvas = document.getElementById('graficoCrescita');
    if (!canvas) {
        console.warn('⚠️ Canvas graficoCrescita non trovato');
        return;
    }

    // Recupera dati di crescita
    const crescitaUtenti = window.crescitaUtenti || [];
    const crescitaSoluzioni = window.crescitaSoluzioni || [];
    
    // Prepara etichette temporali
    const labels = crescitaUtenti.map(item => {
        const date = new Date(item.data);
        return date.toLocaleDateString('it-IT', { 
            day: '2-digit', 
            month: '2-digit' 
        });
    });
    
    // Prepara dati per le serie
    const utentiData = crescitaUtenti.map(item => parseInt(item.count) || 0);
    const soluzioniData = crescitaSoluzioni.map(item => parseInt(item.count) || 0);
    
    if (utentiData.length === 0 && soluzioniData.length === 0) {
        console.warn('⚠️ Nessun dato per grafico crescita');
        return;
    }
    
    // Crea grafico a linee
    graficoCrescita = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nuovi Utenti',
                    data: utentiData,
                    borderColor: '#0dcaf0',
                    backgroundColor: 'rgba(13, 202, 240, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#0dcaf0',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Nuove Soluzioni',
                    data: soluzioniData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#198754',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            ...commonChartOptions,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
                    callbacks: {
                        title: function(context) {
                            return `Data: ${context[0].label}`;
                        },
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y}`;
                        }
                    }
                }
            }
        }
    });
    
    console.log('✅ Grafico crescita creato:', labels.length, 'punti dati');
}

// === ANIMAZIONE CONTATORI ===
function animateCounters() {
    console.log('🎯 Avvio animazione contatori...');
    
    // Seleziona tutti i contatori numerici
    $('.h5.fw-bold, h3[id*="total"], .h4').each(function() {
        const $counter = $(this);
        const text = $counter.text().trim();
        
        // Estrae il numero dal testo (rimuove tutto tranne cifre)
        const targetMatch = text.match(/\d+/);
        if (!targetMatch) return;
        
        const target = parseInt(targetMatch[0]);
        
        // Anima solo numeri ragionevoli (evita animazioni troppo lunghe)
        if (!isNaN(target) && target > 0 && target < 1000) {
            $counter.text('0');
            
            // Animazione jQuery con easing
            $({ counter: 0 }).animate({ counter: target }, {
                duration: CONFIG.ANIMATION_DURATION,
                easing: 'easeOutCubic',
                step: function() {
                    $counter.text(Math.ceil(this.counter));
                },
                complete: function() {
                    $counter.text(target);
                    console.log(`✅ Contatore animato: ${target}`);
                }
            });
        }
    });
}

// === EVENT LISTENERS ===
function setupEventListeners() {
    console.log('🎧 Setup event listeners...');
    
    // Listener per i bottoni di periodo
    $(document).on('click', '.btn-outline-success', function(e) {
        const $btn = $(this);
        if ($btn.hasClass('active')) {
            e.preventDefault();
            return;
        }
        
        // Mostra loading
        $btn.html('<i class="bi bi-hourglass-split spinner-border spinner-border-sm"></i>');
        $btn.prop('disabled', true);
    });
    
    // Listener per il bottone aggiorna
    window.aggiornaStatistiche = function(event) {
        if (event) {
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            
            // Mostra spinner
            btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i>Aggiornando...';
            btn.disabled = true;
            
            // Simula caricamento e ricarica pagina
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // Chiamata diretta - ricarica immediata
            location.reload();
        }
    };
    
    // Listener per escape key (chiude modali/menu)
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
            $('.dropdown-menu').removeClass('show');
        }
    });
}

// === AUTO-REFRESH ===
function startAutoRefresh() {
    if (!CONFIG.ENABLE_AUTO_REFRESH) {
        console.log('🚫 Auto-refresh disabilitato');
        return;
    }
    
    console.log('⏰ Auto-refresh attivato (ogni 10 minuti)');
    
    // Aggiorna timestamp ogni minuto
    setInterval(updateTimestamp, 60000);
    
    // Aggiorna statistiche ogni 10 minuti
    dataUpdateInterval = setInterval(() => {
        console.log('🔄 Auto-refresh statistiche...');
        // In un ambiente reale, qui faremmo una chiamata AJAX
        // Per ora semplicemente aggiorniamo il timestamp
        updateTimestamp();
    }, CONFIG.UPDATE_INTERVAL);
}

// === AGGIORNA TIMESTAMP ===
function updateTimestamp() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('it-IT', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const $timestamp = $('#last-update');
    if ($timestamp.length) {
        $timestamp.text(timeString);
        console.log('🕐 Timestamp aggiornato:', timeString);
    }
}

// === NOTIFICHE ===
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    // Crea elemento notifica
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed shadow" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 350px; border-radius: 8px;">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    // Aggiunge al DOM e rimuove automaticamente
    $('body').append(alert);
    
    // Auto-dismiss dopo 4 secondi
    setTimeout(() => {
        alert.alert('close');
    }, 4000);
    
    console.log(`📢 Notifica mostrata: ${message} (${type})`);
}

// === UTILITY FUNCTIONS ===

// Formatta numeri per display
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

// Calcola percentuale
function calculatePercentage(value, total) {
    if (total === 0) return 0;
    return ((value / total) * 100).toFixed(1);
}

// Verifica se è mobile
function isMobileDevice() {
    return window.innerWidth <= 768;
}

// === CLEANUP ===
$(window).on('beforeunload', function() {
    // Cleanup interval timers
    if (dataUpdateInterval) {
        clearInterval(dataUpdateInterval);
        console.log('🧹 Auto-refresh interval cleared');
    }
    
    // Distruggi grafici per liberare memoria
    if (isChartsInitialized) {
        [graficoUtenti, graficoProdotti, graficoGravita, graficoCrescita].forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        console.log('🧹 Grafici distrutti');
    }
});

// === LOG FINALE ===
console.log('✅ Admin Statistiche JavaScript caricato completamente');

// === ESPORTAZIONE PER DEBUG ===
window.AdminStats = {
    charts: {
        graficoUtenti,
        graficoProdotti, 
        graficoGravita,
        graficoCrescita
    },
    config: CONFIG,
    utils: {
        formatNumber,
        calculatePercentage,
        showNotification,
        updateTimestamp
    }
};

console.log('🔧 AdminStats object disponibile per debug:', window.AdminStats);