/**
 * Gestione Dashboard
 * TechSupport Pro - Gruppo 51
 * 
 * LINGUAGGIO: JavaScript ES6+ con jQuery, AJAX, Chart.js, Intersection Observer API
 * SCOPO: Gestione completa della dashboard del sistema di assistenza tecnica
 * INTEGRAZIONE: Comunica con backend Laravel per aggiornamenti real-time
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Aggiornamento automatico statistiche via AJAX
 * - Animazioni smooth per migliorare UX
 * - Scorciatoie tastiera per navigazione rapida
 * - Grafici statistici con Chart.js
 * - Osservazione viewport per animazioni on-scroll
 */

/**
 * CLASSE: DashboardManager
 * 
 * Gestisce tutti gli aspetti della dashboard amministrativa.
 * Coordina aggiornamenti dati, animazioni UI e interazioni utente.
 * 
 * DESIGN PATTERN: Module Pattern + Observer Pattern
 * RESPONSABILITÀ:
 * - Real-time data updates tramite polling AJAX
 * - Animazioni entry responsive con Intersection Observer
 * - Keyboard shortcuts per power users
 * - Integrazione Chart.js per visualizzazioni grafiche
 * - Performance optimization con lazy loading
 */
class DashboardManager {
    /**
     * COSTRUTTORE DashboardManager
     * 
     * LINGUAGGIO: JavaScript ES6 (Class Constructor)
     * SCOPO: Inizializza tutti i sottosistemi della dashboard
     * 
     * Avvia l'orchestrazione di tutte le funzionalità dashboard:
     * - Polling automatico dati
     * - Setup animazioni viewport-aware
     * - Configurazione shortcuts
     * - Inizializzazione grafici opzionali
     */
    constructor() {
        this.initializeDashboard();  // Coordinatore inizializzazione
    }

    /**
     * METODO: initializeDashboard
     * 
     * LINGUAGGIO: JavaScript
     * SCOPO: Coordinatore centrale dell'inizializzazione dashboard
     * 
     * Orchestrazione nell'ordine ottimale:
     * 1. Statistics refresh system (real-time data)
     * 2. UI animations (smooth user experience)
     * 3. Keyboard shortcuts (power user features)
     * 4. Charts initialization (data visualization)
     * 
     * CONSOLE.LOG: Conferma inizializzazione per debugging
     */
    initializeDashboard() {
        this.setupStatisticsRefresh();    // Sistema polling automatico
        this.setupAnimations();           // Animazioni scroll-based
        this.setupShortcuts();           // Scorciatoie tastiera
        this.initializeCharts();         // Grafici Chart.js (opzionale)
        
        console.log('📊 Dashboard Manager inizializzato');
    }

    /**
     * METODO: setupStatisticsRefresh
     * 
     * LINGUAGGIO: JavaScript + Browser Timer API
     * SCOPO: Configura aggiornamento automatico statistiche
     * 
     * POLLING PATTERN: Richiede dati aggiornati a intervalli regolari
     * Mantiene dashboard sincronizzata senza refresh manuale
     * 
     * TIMER CONFIGURATION:
     * - Intervallo: 5 minuti (5 * 60 * 1000 ms)
     * - Metodo: setInterval() per esecuzione ricorrente
     * 
     * PERFORMANCE CONSIDERATION:
     * - 5 minuti bilancia aggiornamento vs carico server
     * - In produzione potrebbe usare WebSockets per real-time
     * 
     * BROWSER API: setInterval()
     * Esegue funzione ripetutamente con intervallo fisso
     */
    setupStatisticsRefresh() {
        // setInterval: esegue refreshStats() ogni 5 minuti
        setInterval(() => {
            this.refreshStats();  // Chiama metodo refresh
        }, 5 * 60 * 1000);       // 300,000ms = 5 minuti
    }

    /**
     * METODO: refreshStats
     * 
     * LINGUAGGIO: JavaScript + jQuery AJAX
     * SCOPO: Esegue chiamata AJAX per aggiornare statistiche dashboard
     * 
     * AJAX PATTERN: Comunicazione asincrona con server Laravel
     * Non blocca UI durante caricamento dati
     * 
     * ENDPOINT: GET /api/stats/dashboard
     * Endpoint Laravel che ritorna JSON con statistiche aggiornate
     * 
     * ERROR HANDLING:
     * - .done(): Success callback per risposta positiva
     * - .fail(): Error callback per gestire fallimenti rete/server
     * 
     * EXPECTED RESPONSE FORMAT:
     * {
     *   "success": true,
     *   "data": {
     *     "prodotti": {"totali": 150},
     *     "malfunzionamenti": {"totali": 89}, 
     *     "utenti": {"totali": 45}
     *   }
     * }
     */
    refreshStats() {
        // jQuery GET request all'API Laravel
        $.get('/api/stats/dashboard')
            .done((response) => {
                // === SUCCESS CALLBACK ===
                // Controlla formato risposta server
                if (response.success) {
                    // Delega aggiornamento UI a metodo specifico
                    this.updateStatsDisplay(response.data);
                }
                // Se success=false, ignore silently (potrebbero esserci validazioni server)
            })
            .fail(() => {
                // === ERROR CALLBACK ===
                // Log errore per debugging (non disturba utente)
                console.log('Errore aggiornamento statistiche');
                
                // POSSIBILI MIGLIORAMENTI:
                // - Retry logic con exponential backoff
                // - Toast notification per errori persistenti
                // - Fallback a dati cached
            });
    }

    /**
     * METODO: updateStatsDisplay
     * 
     * LINGUAGGIO: JavaScript + jQuery + Intl API
     * SCOPO: Aggiorna elementi DOM con nuove statistiche
     * 
     * PARAMETRI:
     * @param {Object} data - Oggetto con statistiche dal server
     * 
     * DATA STRUCTURE EXPECTED:
     * {
     *   prodotti: {totali: number},
     *   malfunzionamenti: {totali: number},
     *   utenti: {totali: number}
     * }
     * 
     * DOM MANIPULATION:
     * - Usa selettori CSS class-based per flessibilità
     * - formatNumber(): funzione globale per localizzazione italiana
     * - Aggiorna timestamp ultimo refresh
     * 
     * DEFENSIVE PROGRAMMING: Controlla esistenza ogni dato prima uso
     */
    updateStatsDisplay(data) {
        // === AGGIORNAMENTO CONTATORI ===
        
        // Contatore prodotti
        if (data.prodotti && data.prodotti.totali) {
            // formatNumber(): 1234 → "1.234" (formato italiano)
            $('.stat-prodotti').text(formatNumber(data.prodotti.totali));
        }
        
        // Contatore malfunzionamenti  
        if (data.malfunzionamenti && data.malfunzionamenti.totali) {
            $('.stat-malfunzionamenti').text(formatNumber(data.malfunzionamenti.totali));
        }
        
        // Contatore utenti
        if (data.utenti && data.utenti.totali) {
            $('.stat-utenti').text(formatNumber(data.utenti.totali));
        }
        
        // === TIMESTAMP ULTIMO AGGIORNAMENTO ===
        // Date().toLocaleTimeString(): formato ora locale italiana (HH:MM:SS)
        $('.last-updated').text('Aggiornato: ' + new Date().toLocaleTimeString('it-IT'));
    }

    /**
     * METODO: setupAnimations
     * 
     * LINGUAGGIO: JavaScript + Intersection Observer API + CSS Transitions
     * SCOPO: Implementa animazioni smooth per elementi che entrano nel viewport
     * 
     * INTERSECTION OBSERVER PATTERN: Performance-optimized scroll animations
     * Sostituisce eventi scroll che possono causare performance issues
     * 
     * MODERN WEB API: IntersectionObserver()
     * API nativa browser per osservare quando elementi entrano/escono dal viewport
     * Molto più performante di scroll event listeners
     * 
     * ANIMATION APPROACH:
     * 1. Setup: elementi inizialmente opacity=0, translateY=20px
     * 2. Observer: rileva quando elemento è visibile
     * 3. Trigger: cambia CSS per fade-in + slide-up effect
     * 
     * CSS PROPERTIES ANIMATE:
     * - opacity: 0 → 1 (fade in)
     * - transform: translateY(20px) → translateY(0) (slide up)
     * - transition: smooth animation via CSS
     */
    setupAnimations() {
        // === CONFIGURAZIONE OBSERVER ===
        const observerOptions = {
            threshold: 0.1,                    // Trigger quando 10% elemento è visibile
            rootMargin: '0px 0px -50px 0px'   // Trigger 50px prima che elemento sia completamente visibile
        };
        
        // === CREAZIONE INTERSECTION OBSERVER ===
        const observer = new IntersectionObserver((entries) => {
            // Callback eseguito quando visibilità cambia
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // === ELEMENTO ENTRA NEL VIEWPORT ===
                    // Attiva animazione modificando CSS direttamente
                    entry.target.style.opacity = '1';           // Fade in
                    entry.target.style.transform = 'translateY(0)'; // Slide up to position
                }
                // Non gestisce uscita dal viewport (one-time animation)
            });
        }, observerOptions);
        
        // === SETUP ELEMENTI DA ANIMARE ===
        $('.card-custom').each(function() {
            // State iniziale (nascosto, spostato giù)
            this.style.opacity = '0';                                          // Invisibile
            this.style.transform = 'translateY(20px)';                        // 20px sotto posizione finale
            this.style.transition = 'opacity 0.6s ease, transform 0.6s ease'; // Smooth animation
            
            // Registra elemento per osservazione
            observer.observe(this);
        });
    }

    /**
     * METODO: setupShortcuts
     * 
     * LINGUAGGIO: JavaScript + jQuery + Keyboard Events
     * SCOPO: Implementa scorciatoie tastiera per navigazione rapida
     * 
     * POWER USER PATTERN: Shortcuts per utenti avanzati
     * Accelera workflow per amministratori che usano spesso il sistema
     * 
     * KEYBOARD EVENT: 'keydown' per intercettare combinazioni tasti
     * Più affidabile di 'keypress' per tasti modificatori (Ctrl, Alt, etc.)
     * 
     * SHORTCUTS IMPLEMENTATE:
     * - Ctrl+P: Vai a Prodotti
     * - Ctrl+U: Vai a Utenti (solo admin)
     * - Ctrl+H: Vai a Home
     * 
     * CONTEXT AWARENESS: Disabilita shortcuts durante digitazione
     * Previene conflitti quando utente scrive in campi input
     */
    setupShortcuts() {
        // Event listener globale per combinazioni tastiera
        $(document).on('keydown', (e) => {
            // === CONTROLLO CONTESTO ===
            // Ignora shortcuts se utente sta digitando
            if ($(e.target).is('input, textarea')) return;
            
            // === CTRL + P = PRODOTTI ===
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();                    // Previene print dialog browser
                window.location.href = '/prodotti';   // Navigate to products page
            }
            
            // === CTRL + U = UTENTI (SOLO ADMIN) ===
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();                    // Previene view source browser
                // Controlla se utente ha privilegi admin
                if ($('.admin-dashboard').length) {
                    window.location.href = '/admin/users';  // Navigate to users management
                }
                // Se non admin, shortcut viene ignorato
            }
            
            // === CTRL + H = HOME ===
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();                    // Previene history dialog browser
                window.location.href = '/';           // Navigate to homepage
            }
            
            // POSSIBILI ESTENSIONI:
            // - Ctrl+S: Quick save current form
            // - Ctrl+F: Focus search box
            // - Esc: Close modals/cancel actions
        });
    }

    /**
     * METODO: initializeCharts
     * 
     * LINGUAGGIO: JavaScript + Chart.js Detection
     * SCOPO: Inizializza grafici se libreria Chart.js è disponibile
     * 
     * PROGRESSIVE ENHANCEMENT PATTERN: Funzionalità opzionale
     * Dashboard funziona senza grafici, ma li aggiunge se disponibili
     * 
     * DEPENDENCY CHECK: typeof Chart !== 'undefined'
     * Verifica se Chart.js è stato caricato prima di usarlo
     * Evita errori ReferenceError se libreria mancante
     * 
     * MODULAR DESIGN: Delega creazione grafico a metodo specifico
     * Separa logica inizializzazione da implementazione chart
     */
    initializeCharts() {
        // Implementazione base per grafici futuri
        if (typeof Chart !== 'undefined') {
            // Chart.js è disponibile, procedi con inizializzazione
            this.createStatChart();
        }
        // Se Chart.js non disponibile, continua senza errori
    }

    /**
     * METODO: createStatChart
     * 
     * LINGUAGGIO: JavaScript + Chart.js Library
     * SCOPO: Crea grafico a ciambella per visualizzare statistiche
     * 
     * CHART.JS: Libreria JavaScript per grafici responsive e interattivi
     * Supporta vari tipi: line, bar, pie, doughnut, radar, etc.
     * 
     * CHART TYPE: 'doughnut' (ciambella)
     * Variante del pie chart con centro vuoto, più modern look
     * 
     * CONFIGURAZIONE:
     * - Data: Labels e datasets con colori Bootstrap-themed
     * - Options: Responsive design + legend positioning
     * 
     * DOM REQUIREMENT: Canvas element con ID 'statsChart'
     * Chart.js richiede elemento <canvas> per rendering
     * 
     * DEFENSIVE PROGRAMMING: Controlla esistenza canvas prima creazione
     */
    createStatChart() {
        // === CONTROLLO ELEMENTO TARGET ===
        const ctx = document.getElementById('statsChart');  // Canvas element
        if (!ctx) return;  // Exit gracefully se canvas non trovato
        
        // === CREAZIONE CHART.JS INSTANCE ===
        new Chart(ctx, {
            // === TIPO GRAFICO ===
            type: 'doughnut',  // Grafico a ciambella
            
            // === DATI GRAFICO ===
            data: {
                labels: ['Prodotti', 'Malfunzionamenti', 'Utenti'],  // Etichette settori
                datasets: [{
                    data: [12, 19, 3],  // Valori esempio (in produzione da API)
                    backgroundColor: [
                        '#198754',  // Verde Bootstrap (success)
                        '#ffc107',  // Giallo Bootstrap (warning) 
                        '#0d6efd'   // Blu Bootstrap (primary)
                    ]
                }]
            },
            
            // === OPZIONI CONFIGURAZIONE ===
            options: {
                responsive: true,  // Auto-resize su cambio dimensioni container
                plugins: {
                    legend: {
                        position: 'bottom'  // Legenda sotto grafico
                    }
                }
                
                // POSSIBILI ESTENSIONI:
                // - animation: {duration: 1000} per smooth loading
                // - onClick: handler per interazioni click
                // - tooltips: customizzazione hover info
                // - maintainAspectRatio: false per controllo dimensioni
            }
        });
    }
}

// === INIZIALIZZAZIONE CONDIZIONALE ===

/**
 * DOCUMENT READY HANDLER
 * 
 * LINGUAGGIO: jQuery
 * SCOPO: Inizializza DashboardManager solo su pagine dashboard
 * 
 * CONDITIONAL LOADING PATTERN: Performance optimization
 * Evita di eseguire codice dashboard su pagine che non lo necessitano
 * 
 * DETECTION STRATEGY:
 * - Cerca classe 'dashboard-page' (specifica)
 * - Oppure qualsiasi classe contenente 'dashboard' (flessibile)
 * - Selector: [class*="dashboard"] = attribute contains substring
 * 
 * ESEMPI CLASSI MATCHATE:
 * - 'dashboard-page'
 * - 'admin-dashboard'  
 * - 'user-dashboard'
 * - 'dashboard-main'
 * 
 * GLOBAL INSTANCE: window.dashboardManager
 * Accessibile da altri script per integrazione
 */
$(document).ready(function() {
    // Controlla presenza elementi dashboard
    if ($('.dashboard-page').length || $('[class*="dashboard"]').length) {
        // Crea istanza globale DashboardManager
        window.dashboardManager = new DashboardManager();
    }
    // Se condizioni non soddisfatte, DashboardManager non viene caricato
});