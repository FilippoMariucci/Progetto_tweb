/**
 * ====================================================================
 * FILE: statistiche.js - Gestione Statistiche Staff
 * LINGUAGGIO: JavaScript (ES6+) con jQuery
 * FRAMEWORK: Laravel 12 - Progetto Assistenza Tecnica
 * AUTORE: Gruppo 51 - Corso Tecnologie Web 2024/2025
 * ====================================================================
 * 
 * DESCRIZIONE:
 * Questo file gestisce l'interfaccia utente della pagina delle statistiche
 * per gli utenti di livello Staff (livello 3). Include animazioni, tooltip,
 * gestione eventi e notifiche per migliorare l'esperienza utente.
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Aggiornamento statistiche con feedback visuale
 * - Animazioni contatori numerici
 * - Tooltip informativi
 * - Effetti hover sui grafici
 * - Sistema di notifiche
 * - Auto-refresh periodico
 */

// ====================================================================
// FUNZIONE GLOBALE: aggiornaStatistiche()
// LINGUAGGIO: JavaScript ES6
// ====================================================================
/**
 * Funzione per aggiornare le statistiche della pagina
 * Viene chiamata quando l'utente clicca sul pulsante "Aggiorna"
 * 
 * @function aggiornaStatistiche
 * @description Gestisce il processo di aggiornamento delle statistiche:
 *              1. Disabilita il pulsante per evitare click multipli
 *              2. Mostra uno spinner di caricamento
 *              3. Ricarica la pagina dopo 1 secondo
 * 
 * TECNOLOGIE UTILIZZATE:
 * - JavaScript nativo per manipolazione DOM
 * - Bootstrap Icons per lo spinner
 * - setTimeout per ritardo controllato
 */
function aggiornaStatistiche() {
    // Ottiene il bottone che ha scatenato l'evento click
    // 'event' è l'oggetto evento globale del browser
    const btn = event.target;
    
    // Salva il contenuto HTML originale del bottone
    // Questo serve per ripristinarlo in caso di errore
    const originalHtml = btn.innerHTML;
    
    // Sostituisce il contenuto del bottone con uno spinner animato
    // Utilizza le classi Bootstrap per l'icona e l'animazione
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    
    // Disabilita il bottone per evitare click multipli
    // Impedisce azioni accidentali durante il caricamento
    btn.disabled = true;
    
    // Ricarica la pagina dopo 1000 millisecondi (1 secondo)
    // Il delay permette all'utente di vedere il feedback visuale
    setTimeout(() => location.reload(), 1000);
}

// ====================================================================
// DOCUMENT READY: Inizializzazione jQuery
// LINGUAGGIO: jQuery (libreria JavaScript)
// ====================================================================
/**
 * Funzione di inizializzazione che si esegue quando il DOM è pronto
 * Utilizza jQuery per gestire l'evento document.ready
 * 
 * PATTERN: $(document).ready() è il pattern jQuery standard per
 *          assicurarsi che il codice venga eseguito solo dopo che
 *          il DOM HTML è stato completamente caricato
 */
$(document).ready(function() {
    // Log di debug per confermare il caricamento del modulo
    console.log('📊 Statistiche Staff Compatte caricato');

    // ================================================================
    // CONTROLLO ROUTE: Verifica della pagina corrente
    // ================================================================
    /**
     * Sistema di sicurezza per evitare esecuzione su pagine sbagliate
     * Utilizza una variabile globale impostata dal backend Laravel
     */
    const currentRoute = window.LaravelApp?.route || '';
    
    // Se non siamo nella pagina corretta, esce immediatamente
    // Questo evita errori e conflitti con altre pagine
    if (currentRoute !== 'staff.statistiche') {
        return; // Exit early se non siamo nella pagina giusta
    }

    // ================================================================
    // INIZIALIZZAZIONE VARIABILI: Setup dati di pagina
    // ================================================================
    /**
     * Recupera i dati passati dal controller Laravel tramite JavaScript
     * window.PageData è popolato dalla vista Blade con dati del backend
     */
    const pageData = window.PageData || {};
    
    // Array per tenere traccia dei prodotti selezionati
    // Utilizzato per funzionalità di selezione multipla (se implementata)
    let selectedProducts = [];

    // Log di conferma inizializzazione
    console.log('📊 Statistiche Staff Compatte inizializzate');

    // ================================================================
    // SEZIONE ANIMAZIONI: Animazione contatori numerici
    // LINGUAGGIO: jQuery con animazioni CSS
    // ================================================================
    /**
     * Funzione per animare i contatori numerici della dashboard
     * Crea un effetto visuale accattivante che conta da 0 al valore finale
     * 
     * @function animateCounters
     * @description Trova tutti gli elementi con classe 'h5.fw-bold',
     *              estrae i numeri dal testo e li anima con jQuery animate()
     * 
     * TECNOLOGIE:
     * - jQuery selectors per trovare elementi
     * - jQuery animate() per animazioni fluide
     * - Regular expressions per estrarre numeri
     * - Math.ceil() per arrotondare i numeri
     */
    function animateCounters() {
        // Seleziona tutti gli elementi h5 con classe fw-bold (Bootstrap)
        // Questi elementi contengono i numeri da animare
        $('.h5.fw-bold').each(function() {
            // 'this' si riferisce all'elemento DOM corrente nel loop
            const $counter = $(this); // Converte in oggetto jQuery
            
            // Estrae il testo dall'elemento e rimuove spazi
            const text = $counter.text().trim();
            
            // Usa regex per estrarre solo i numeri dal testo
            // replace(/[^\d]/g, '') rimuove tutto tranne i digit
            const target = parseInt(text.replace(/[^\d]/g, ''));
            
            // Verifica se il numero è valido e nel range appropriato
            // Evita animazioni su valori troppo grandi o non validi
            if (!isNaN(target) && target > 0 && target < 500) {
                // Inizializza il counter a 0
                $counter.text('0');
                
                // Crea un oggetto temporaneo per l'animazione
                // jQuery animate() può animare proprietà di oggetti JavaScript
                $({ counter: 0 }).animate({ counter: target }, {
                    // Durata animazione in millisecondi
                    duration: 1200,
                    
                    // Tipo di easing (curva di animazione)
                    // 'swing' crea un effetto naturale di accelerazione/decelerazione
                    easing: 'swing',
                    
                    // Funzione chiamata ad ogni frame dell'animazione
                    step: function() {
                        // Math.ceil() arrotonda per eccesso per numeri interi
                        // this.counter è il valore corrente dell'animazione
                        $counter.text(Math.ceil(this.counter));
                    },
                    
                    // Funzione chiamata quando l'animazione finisce
                    complete: function() {
                        // Assicura che il valore finale sia esatto
                        $counter.text(target);
                    }
                });
            }
        });
    }

    // Avvia le animazioni dopo 300ms per dare tempo al rendering
    // Il delay evita conflitti con altre animazioni CSS al caricamento
    setTimeout(animateCounters, 300);

    // ================================================================
    // SEZIONE TOOLTIP: Tooltip informativi compatti
    // LINGUAGGIO: Bootstrap JavaScript API
    // ================================================================
    /**
     * Inizializzazione tooltip per elementi con attributo 'title'
     * I tooltip forniscono informazioni aggiuntive al passaggio del mouse
     * 
     * BOOTSTRAP TOOLTIP API:
     * - trigger: 'hover' = mostra solo al passaggio del mouse
     * - placement: 'top' = posiziona sopra l'elemento
     * - delay: controlla tempi di show/hide
     */
    $('[title]').tooltip({
        trigger: 'hover',     // Attiva solo con hover del mouse
        placement: 'top',     // Posizione sopra l'elemento
        delay: { 
            show: 300,        // Attesa 300ms prima di mostrare
            hide: 100         // Attesa 100ms prima di nascondere
        }
    });

    // ================================================================
    // SEZIONE HOVER: Effetti hover sui grafici
    // LINGUAGGIO: jQuery event handling
    // ================================================================
    /**
     * Aggiunge effetti visivi ai grafici al passaggio del mouse
     * Migliora l'interattività e il feedback visuale per l'utente
     * 
     * PATTERN HOVER JQUERY:
     * - Primo parametro: funzione per mouseenter
     * - Secondo parametro: funzione per mouseleave
     */
    $('.chart-bar').hover(
        // Funzione eseguita quando il mouse entra nell'elemento
        function() {
            // Aggiunge la classe Bootstrap 'shadow-sm' per ombra leggera
            $(this).addClass('shadow-sm');
        },
        // Funzione eseguita quando il mouse esce dall'elemento
        function() {
            // Rimuove la classe per tornare allo stato originale
            $(this).removeClass('shadow-sm');
        }
    );

    // ================================================================
    // SEZIONE NOTIFICHE: Sistema di notifiche utente
    // LINGUAGGIO: JavaScript + jQuery + Bootstrap
    // ================================================================
    /**
     * Controlla se ci sono messaggi di sessione dal backend Laravel
     * e li mostra come notifiche Bootstrap all'utente
     * 
     * LARAVEL SESSION FLASH:
     * - sessionSuccess: messaggi di successo
     * - sessionError: messaggi di errore
     * I dati sono passati dalla vista Blade tramite window.PageData
     */
    
    // Controlla messaggio di successo
    if (window.PageData.sessionSuccess) {
        showNotification('success', window.PageData.sessionSuccess);
    }
    
    // Controlla messaggio di errore
    if (window.PageData.sessionError) {
        showNotification('error', window.PageData.sessionError);
    }

    /**
     * Funzione per mostrare notifiche dinamiche all'utente
     * Crea alert Bootstrap posizionati in alto a destra
     * 
     * @function showNotification
     * @param {string} type - Tipo di notifica ('success', 'error', 'warning', etc.)
     * @param {string} message - Messaggio da mostrare all'utente
     * 
     * BOOTSTRAP ALERT COMPONENT:
     * - Utilizza classi Bootstrap per styling
     * - alert-dismissible per bottone di chiusura
     * - position-fixed per posizionamento fisso
     * - z-index alto per stare sopra altri elementi
     * 
     * TECNOLOGIE:
     * - Template literals (``) per HTML dinamico
     * - Bootstrap Icons per icone
     * - jQuery per manipolazione DOM
     * - setTimeout per auto-dismiss
     */
    function showNotification(type, message) {
        // Converte tipo 'error' in classe Bootstrap 'danger'
        const alertClass = type === 'error' ? 'danger' : type;
        
        // Selezione icona in base al tipo di messaggio
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        // Crea l'elemento notifica con template literal
        // Template literal (``) permette HTML multi-linea con variabili
        const notification = $(`
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed"
                  style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;"
                  role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Aggiunge la notifica al body della pagina
        $('body').append(notification);
        
        // Rimuove automaticamente la notifica dopo 4 secondi
        // Migliora UX evitando accumulo di notifiche
        setTimeout(() => $('.alert').alert('close'), 4000);
    }

    // Rende la funzione disponibile globalmente
    // Permette di chiamarla da altre parti dell'applicazione
    window.showNotification = showNotification;

    // Log di completamento inizializzazione
    console.log('✅ Statistiche Staff Compatte caricate');
});

// ====================================================================
// AUTO-REFRESH: Aggiornamento periodico automatico
// LINGUAGGIO: JavaScript nativo (setInterval)
// ====================================================================
/**
 * Auto-refresh delle statistiche ogni 15 minuti (900000 ms)
 * Mantiene i dati aggiornati senza intervento dell'utente
 * 
 * SETINTERVAL API:
 * - Primo parametro: funzione da eseguire
 * - Secondo parametro: intervallo in millisecondi
 * 
 * NOTE TECNICHE:
 * - 900000 ms = 15 minuti
 * - Il console.log serve per debug e monitoraggio
 * - In produzione potrebbe essere implementato con AJAX
 *   invece di reload completo per migliori performance
 */
setInterval(() => {
    // Log per debug - aiuta a tracciare quando avviene il refresh
    console.log('🔄 Auto-refresh statistiche staff');
    
    // TODO: Implementare AJAX refresh invece di reload completo
    // location.reload(); // Commentato per evitare refresh in sviluppo
}, 900000); // 15 minuti in millisecondi

// ====================================================================
// NOTE TECNICHE AGGIUNTIVE:
// ====================================================================
/**
 * ARCHITETTURA DEL CODICE:
 * 1. Funzioni globali (aggiornaStatistiche)
 * 2. Inizializzazione jQuery (document.ready)
 * 3. Controlli di sicurezza (route checking)
 * 4. Gestione animazioni e UI
 * 5. Sistema di notifiche
 * 6. Auto-refresh
 * 
 * PATTERN UTILIZZATI:
 * - Module Pattern per organizzazione codice
 * - Event-driven programming per interazioni
 * - Progressive enhancement per accessibilità
 * - Defensive programming per robustezza
 * 
 * BEST PRACTICES:
 * - Namespace per evitare conflitti globali
 * - Error handling per robustezza
 * - Performance optimization con delay
 * - User feedback per migliore UX
 * 
 * INTEGRAZIONE LARAVEL:
 * - window.LaravelApp per dati route
 * - window.PageData per dati controller
 * - Blade directives per passaggio dati PHP->JS
 * - Laravel session flash per messaggi utente
 */