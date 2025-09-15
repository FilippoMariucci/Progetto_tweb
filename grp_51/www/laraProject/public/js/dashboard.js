/**
 * Gestione Dashboard
 * TechSupport Pro - Gruppo 51
 */

class DashboardManager {
    constructor() {
        this.initializeDashboard();
    }

    /**
     * Inizializza dashboard
     */
    initializeDashboard() {
        this.setupStatisticsRefresh();
        this.setupAnimations();
        this.setupShortcuts();
        this.initializeCharts();
        
        console.log('📊 Dashboard Manager inizializzato');
    }

    /**
     * Aggiornamento automatico statistiche
     */
    setupStatisticsRefresh() {
        // Aggiorna ogni 5 minuti
        setInterval(() => {
            this.refreshStats();
        }, 5 * 60 * 1000);
    }

    /**
     * Refresh delle statistiche via AJAX
     */
    refreshStats() {
        $.get('/api/stats/dashboard')
            .done((response) => {
                if (response.success) {
                    this.updateStatsDisplay(response.data);
                }
            })
            .fail(() => {
                console.log('Errore aggiornamento statistiche');
            });
    }

    /**
     * Aggiorna display delle statistiche
     */
    updateStatsDisplay(data) {
        // Aggiorna contatori
        if (data.prodotti && data.prodotti.totali) {
            $('.stat-prodotti').text(formatNumber(data.prodotti.totali));
        }
        
        if (data.malfunzionamenti && data.malfunzionamenti.totali) {
            $('.stat-malfunzionamenti').text(formatNumber(data.malfunzionamenti.totali));
        }
        
        if (data.utenti && data.utenti.totali) {
            $('.stat-utenti').text(formatNumber(data.utenti.totali));
        }
        
        // Timestamp ultimo aggiornamento
        $('.last-updated').text('Aggiornato: ' + new Date().toLocaleTimeString('it-IT'));
    }

    /**
     * Animazioni entry per le card
     */
    setupAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Applica alle card
        $('.card-custom').each(function() {
            this.style.opacity = '0';
            this.style.transform = 'translateY(20px)';
            this.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(this);
        });
    }

    /**
     * Scorciatoie da tastiera per dashboard
     */
    setupShortcuts() {
        $(document).on('keydown', (e) => {
            // Solo se non stiamo scrivendo in un input
            if ($(e.target).is('input, textarea')) return;
            
            // Ctrl + P = Prodotti
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.location.href = '/prodotti';
            }
            
            // Ctrl + U = Utenti (solo admin)
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
                if ($('.admin-dashboard').length) {
                    window.location.href = '/admin/users';
                }
            }
            
            // Ctrl + H = Home
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = '/';
            }
        });
    }

    /**
     * Inizializza grafici (se Chart.js è disponibile)
     */
    initializeCharts() {
        // Implementazione base per grafici futuri
        if (typeof Chart !== 'undefined') {
            this.createStatChart();
        }
    }

    /**
     * Crea grafico statistiche (esempio)
     */
    createStatChart() {
        const ctx = document.getElementById('statsChart');
        if (!ctx) return;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Prodotti', 'Malfunzionamenti', 'Utenti'],
                datasets: [{
                    data: [12, 19, 3],
                    backgroundColor: [
                        '#198754',
                        '#ffc107', 
                        '#0d6efd'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Inizializza su pagine dashboard
$(document).ready(function() {
    if ($('.dashboard-page').length || $('[class*="dashboard"]').length) {
        window.dashboardManager = new DashboardManager();
    }
});