/**
 * FILE: azienda.js
 * LINGUAGGIO: JavaScript (ES6+) con jQuery e Intersection Observer API
 * SCOPO: Gestione della pagina informazioni azienda con animazioni e interazioni avanzate
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Intersection Observer API nativa
 * FUNZIONALITÀ PRINCIPALI: Animazioni contatori, smooth scrolling, tracking interazioni
 * TECNOLOGIE AVANZATE: IntersectionObserver per trigger animations, RegExp matching, setInterval timing
 * AUTORE: Sistema di assistenza tecnica
 * NOTA: Il codice contiene document.ready nidificato (da correggere in produzione)
 */

/**
 * PRIMO DOCUMENT READY - WRAPPER ESTERNO
 * Outer wrapper per controllo route e inizializzazione base
 * Entry point principale del file JavaScript
 */
$(document).ready(function() {
    
    /**
     * LOG IDENTIFICAZIONE FILE
     * console.log: identifica caricamento file nella console browser
     */
    console.log('pages.azienda caricato');
    
    // === CONTROLLO ROUTE SPECIFICA ===
    /**
     * VALIDAZIONE ROUTE CORRENTE PER SICUREZZA
     * window.LaravelApp?.route: variabile globale iniettata da Laravel via Blade
     * Optional chaining (?.) per prevenire TypeError se oggetto non definito
     * Early return pattern per performance e sicurezza
     */
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'azienda') {
        return; // Termina esecuzione se non siamo nella pagina azienda
    }
    
    // === INIZIALIZZAZIONE VARIABILI GLOBALI ===
    /**
     * SETUP DATI CONDIVISI TRA CLIENT E SERVER
     * window.PageData: oggetto popolato da Laravel tramite @json() in Blade template
     * selectedProducts: array per tracking (non utilizzato in questa pagina specifica)
     */
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    /**
     * SECONDO DOCUMENT READY - NIDIFICATO (PROBLEMA ARCHITETTURALE)
     * NOTA IMPORTANTE: Questo è un anti-pattern - document.ready nidificato non è necessario
     * Il DOM è già pronto nel primo wrapper. In produzione andrebbe rimosso il nesting.
     */
    $(document).ready(function() {
        
        /**
         * LOG CONFERMA CARICAMENTO PAGINA
         * Secondo log per tracciare progression caricamento interno
         */
        console.log('Pagina azienda caricata');
        
        // === ANIMAZIONE CONTATORI CON EFFETTO INCREMENTO ===
        /**
         * FUNZIONE PER ANIMARE NUMERI CON EFFETTO COUNTING UP
         * Crea effetto visivo di conteggio progressivo per statistiche/numeri
         * Utilizza setInterval per aggiornamenti frequenti e fluidi
         */
        function animateCounters() {
            
            /**
             * ITERAZIONE SU ELEMENTI CON NUMERI
             * $('.display-4, h3'): selettore jQuery per elementi tipografia Bootstrap
             * .each(): itera su ogni elemento trovato con callback function
             */
            $('.display-4, h3').each(function() {
                const $this = $(this); // Cache riferimento jQuery per performance
                const text = $this.text(); // Ottiene testo corrente dell'elemento
                
                /**
                 * ESTRAZIONE NUMERI CON REGULAR EXPRESSION
                 * RegExp pattern: (\d+)\+? 
                 * - (\d+): capture group per una o più cifre
                 * - \+?: zero o uno carattere '+' (opzionale)
                 * .match(): metodo String per trovare corrispondenze regex
                 */
                const match = text.match(/(\d+)\+?/);
                
                /**
                 * PROCESSING SE NUMERO TROVATO
                 * Verifica esistenza match prima di procedere con animazione
                 */
                if (match) {
                    
                    /**
                     * CONFIGURAZIONE PARAMETRI ANIMAZIONE
                     * parseInt(): converte stringa in numero intero
                     * match[1]: primo capture group della regex (il numero)
                     */
                    const finalNumber = parseInt(match[1]); // Numero finale target
                    const duration = 2000; // Durata totale animazione: 2 secondi
                    const increment = finalNumber / (duration / 50); // Incremento per frame (50ms intervals)
                    let current = 0; // Valore corrente del contatore
                    
                    /**
                     * TIMER PER ANIMAZIONE PROGRESSIVA
                     * setInterval(): esegue funzione ripetutamente ogni 50ms
                     * Arrow function per mantenere contesto lessicale
                     */
                    const timer = setInterval(() => {
                        current += increment; // Incrementa valore corrente
                        
                        /**
                         * CONTROLLO TERMINE ANIMAZIONE
                         * Ferma timer quando raggiunge o supera valore finale
                         */
                        if (current >= finalNumber) {
                            current = finalNumber; // Forza valore esatto finale
                            clearInterval(timer); // Stop timer per cleanup memoria
                        }
                        
                        /**
                         * AGGIORNAMENTO VISUALE ELEMENTO
                         * Math.floor(): arrotonda per difetto per numeri interi
                         * Conditional operator per mantenere '+' se presente nel testo originale
                         */
                        $this.text(Math.floor(current) + (text.includes('+') ? '+' : ''));
                    }, 50); // Frame rate: 20 FPS (1000ms / 50ms = 20 frames/sec)
                }
            });
        }
        
        // === TRIGGER ANIMAZIONI CON INTERSECTION OBSERVER ===
        /**
         * IMPLEMENTAZIONE LAZY ANIMATION CON INTERSECTION OBSERVER API
         * IntersectionObserver: API nativa per detectare visibilità elementi nel viewport
         * Permette di avviare animazioni solo quando elementi diventano visibili
         */
        
        /**
         * CONFIGURAZIONE INTERSECTION OBSERVER
         * Callback function eseguita quando elementi cambiano stato di visibilità
         * @param {Array} entries - Lista elementi che hanno cambiato stato intersect
         */
        const observer = new IntersectionObserver((entries) => {
            
            /**
             * ITERAZIONE SU ELEMENTI INTERSECANTI
             * .forEach(): processa ogni elemento che ha cambiato visibilità
             */
            entries.forEach(entry => {
                
                /**
                 * CONTROLLO SE ELEMENTO È VISIBILE NEL VIEWPORT
                 * entry.isIntersecting: boolean che indica visibilità elemento
                 */
                if (entry.isIntersecting) {
                    animateCounters(); // Avvia animazione contatori
                    
                    /**
                     * DISCONNESSIONE OBSERVER DOPO PRIMO TRIGGER
                     * .disconnect(): ferma observation per performance
                     * Pattern one-shot: animazione eseguita solo una volta
                     */
                    observer.disconnect();
                }
            });
        });
        
        /**
         * SELEZIONE TARGET PER OBSERVATION
         * document.querySelector(): API DOM nativa per selezione singolo elemento
         * Cerca sezione con background primario che probabilmente contiene le statistiche
         */
        const numbersSection = document.querySelector('.bg-primary');
        
        /**
         * ATTIVAZIONE OBSERVATION SE ELEMENTO ESISTE
         * Controllo esistenza prima di observe per prevenire errori
         */
        if (numbersSection) {
            observer.observe(numbersSection); // Inizia observation su sezione numeri
        }
        
        // === SMOOTH SCROLLING PER LINK INTERNI ===
        /**
         * IMPLEMENTAZIONE NAVIGAZIONE FLUIDA PER ANCORE
         * Event delegation per tutti i link che iniziano con # (link interni)
         * Sostituisce il jump brusco nativo del browser con animazione fluida
         */
        $('a[href^="#"]').on('click', function(e) {
            
            /**
             * PREVENZIONE COMPORTAMENTO DEFAULT BROWSER
             * e.preventDefault(): blocca il salto nativo alle ancore
             */
            e.preventDefault();
            
            /**
             * SELEZIONE TARGET DELL'ANCORA
             * this.getAttribute('href'): metodo DOM nativo per ottenere attributo href
             * $(target): conversione in oggetto jQuery per manipolazione
             */
            const target = $(this.getAttribute('href'));
            
            /**
             * ANIMAZIONE SCROLL SE TARGET ESISTE
             * .length: verifica esistenza elemento nel DOM prima di animare
             */
            if (target.length) {
                
                /**
                 * ANIMAZIONE SCROLL FLUIDA
                 * .animate(): metodo jQuery per animazioni CSS smooth
                 * scrollTop: proprietà CSS per posizione scroll verticale
                 * target.offset().top: posizione assoluta elemento target
                 * -80: offset negativo per compensare header fisso (80px)
                 * 600: durata animazione in millisecondi (0.6 secondi)
                 */
                $('html, body').animate({
                    scrollTop: target.offset().top - 80 // Offset per header sticky/fixed
                }, 600);
            }
        });
        
        // === TRACCIAMENTO INTERAZIONI UTENTE ===
        /**
         * ANALYTICS SEMPLIFICATO PER MONITORAGGIO COMPORTAMENTO
         * Event delegation su tutti i pulsanti per tracciare interazioni
         * Utile per analytics e debugging comportamento utenti
         */
        $('.btn').on('click', function() {
            
            /**
             * ESTRAZIONE TESTO PULSANTE PER TRACKING
             * $(this).text(): ottiene contenuto testuale del pulsante cliccato
             * .trim(): rimuove spazi bianchi iniziali e finali
             */
            const btnText = $(this).text().trim();
            
            /**
             * LOG INTERAZIONE PER ANALYTICS
             * console.log: registra click per debugging e monitoraggio
             * In produzione potrebbe essere sostituito con Google Analytics o simili
             */
            console.log('Pulsante cliccato:', btnText);
        });
        
    }); // Fine secondo document.ready (da rimuovere in produzione)
    
}); // Fine primo document.ready

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, arrow functions, template literals)
 * - jQuery (DOM manipulation, event handling, animations)
 * - Intersection Observer API (detection visibilità elementi per lazy animations)
 * - Regular Expressions (pattern matching per estrazione numeri)
 * - SetInterval/ClearInterval (timing functions per animazioni frame-based)
 * - DOM APIs native (querySelector, getAttribute)
 * - CSS Animation via JavaScript (scrollTop property manipulation)
 * - Event Delegation (gestione eventi efficiente)
 * - Laravel Integration (route checking, data injection)
 * 
 * PATTERN E PRINCIPI UTILIZZATI:
 * - Lazy Animation (animazioni triggerate dalla visibilità)
 * - Progressive Enhancement (JavaScript migliora esperienza HTML base)
 * - Performance Optimization (observer disconnect, element caching)
 * - Smooth User Experience (animazioni fluide, scrolling graduale)
 * - Analytics Integration (tracking interazioni utente)
 * - Error Prevention (controlli esistenza elementi prima manipolazione)
 * - Memory Management (clearInterval per cleanup timers)
 * - Accessibility Friendly (mantiene funzionalità base se JS disabilitato)
 * 
 * PROBLEMI ARCHITETTURALI DA CORREGGERE:
 * 1. **Document.ready nidificato**: Il secondo $(document).ready() all'interno del primo è ridondante
 *    e rappresenta un anti-pattern. Il DOM è già pronto nel primo wrapper.
 * 
 * 2. **Possibile memory leak**: Se la pagina viene abbandonata durante le animazioni,
 *    i setInterval potrebbero continuare a girare. Sarebbe meglio aggiungere cleanup.
 * 
 * 3. **Selector performance**: $('.display-4, h3') potrebbe essere troppo generico
 *    se la pagina ha molti elementi h3. Meglio usare classi specifiche.
 * 
 * FUNZIONALITÀ IMPLEMENTATE:
 * 1. **Counter Animation**: Effetto conteggio progressivo per numeri/statistiche
 * 2. **Lazy Animation Trigger**: Animazioni che si avviano solo quando visibili
 * 3. **Smooth Scrolling**: Navigazione fluida per link interni alla pagina
 * 4. **User Interaction Tracking**: Logging click sui pulsanti per analytics
 * 5. **Route Protection**: Esecuzione condizionale basata sulla route Laravel
 * 6. **Performance Optimization**: Observer disconnect e cleanup timers
 * 
 * MIGLIORAMENTI SUGGERITI PER PRODUZIONE:
 * 1. Rimuovere document.ready nidificato
 * 2. Aggiungere cleanup dei timer su page unload
 * 3. Utilizzare selettori CSS più specifici
 * 4. Implementare debouncing per scroll events se necessario
 * 5. Aggiungere fallback per browser senza IntersectionObserver support
 * 6. Sostituire console.log con sistema analytics reale
 */