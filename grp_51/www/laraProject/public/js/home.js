/**
 * HOMEPAGE JAVASCRIPT - SISTEMA COMPLETO
 * TechSupport Pro - Gruppo 51
 * 
 * LINGUAGGIO: JavaScript ES6+ con jQuery, AJAX, Bootstrap, Intersection Observer API
 * SCOPO: Gestione completa della homepage del sistema TechSupport Pro
 * INTEGRAZIONE: Laravel backend, Bootstrap frontend, API REST per ricerca prodotti
 * 
 * FUNZIONALITÀ PRINCIPALI:
 * - Ricerca in tempo reale prodotti con debounce e AJAX
 * - Animazioni scroll-based con Intersection Observer
 * - Contatori animati per statistiche
 * - Lazy loading immagini per performance
 * - UX enhancements (hover effects, smooth scroll)
 * - Error handling e fallback per immagini
 * - Suggerimenti ricerca dinamici
 */

/**
 * DOCUMENT READY HANDLER
 * 
 * LINGUAGGIO: jQuery
 * SCOPO: Inizializzazione completa homepage quando DOM è caricato
 * 
 * CONDITIONAL EXECUTION: Verifica route corrente per evitare conflitti
 * Esegue codice solo se siamo effettivamente sulla homepage
 * 
 * GLOBAL VARIABLES:
 * - window.LaravelApp: oggetto globale con info routing Laravel
 * - window.PageData: dati pagina passati da backend
 * - selectedProducts: array per gestione selezioni multiple (future use)
 */
$(document).ready(function() {
    console.log('Home caricato');

    // === ROUTE VALIDATION ===
    // Controlla se siamo sulla route corretta per evitare esecuzione su altre pagine
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'home') {
        return;  // Exit early se non homepage
    }
    
    // === INIZIALIZZAZIONE VARIABILI GLOBALI ===
    const pageData = window.PageData || {};        // Dati pagina da Laravel backend
    let selectedProducts = [];                     // Array prodotti selezionati (future feature)
    
    // Il tuo codice JavaScript qui...
    console.log('🏠 Homepage inizializzata');
    
    // === RICERCA DINAMICA PRODOTTI ===
    
    /**
     * VARIABILI PER GESTIONE RICERCA
     * 
     * LINGUAGGIO: JavaScript
     * SCOPO: Controllo stato ricerca e debouncing
     * 
     * - searchTimeout: ID timeout per debounce ricerca
     * - isSearching: flag per prevenire ricerche multiple simultanee
     * 
     * DEBOUNCE PATTERN: Ritarda esecuzione ricerca per ottimizzare performance
     * Evita chiamate AJAX ad ogni carattere digitato
     */
    let searchTimeout;   // Timer ID per debounce
    let isSearching = false;  // Flag per prevenire ricerche sovrapposte
    
    /**
     * EVENT HANDLER: INPUT SEARCH
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Gestisce input utente per ricerca in tempo reale
     * 
     * EVENT: 'input' si scatena ad ogni modifica campo
     * DEBOUNCE: 300ms delay per ottimizzare chiamate API
     * VALIDATION: Minimo 2 caratteri per avviare ricerca
     * 
     * FLUSSO:
     * 1. Intercetta ogni carattere digitato
     * 2. Cancella timeout precedente (debounce)
     * 3. Se query >= 2 char, avvia timer per ricerca
     * 4. Altrimenti nasconde risultati
     */
    $('#search-input').on('input', function() {
        const query = $(this).val().trim();  // Rimuove spazi inizio/fine
        
        // === DEBOUNCE IMPLEMENTATION ===
        // Cancella il timeout precedente per evitare chiamate multiple
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            // Debounce di 300ms per evitare troppe chiamate API
            searchTimeout = setTimeout(() => {
                searchProdotti(query);  // Esegue ricerca dopo delay
            }, 300);
        } else {
            // Query troppo corta, nasconde risultati
            hideSearchResults();
        }
    });
    
    /**
     * FUNZIONE: searchProdotti
     * 
     * LINGUAGGIO: JavaScript + jQuery AJAX
     * SCOPO: Esegue chiamata API per ricerca prodotti
     * 
     * @param {string} query - Termine di ricerca inserito dall'utente
     * 
     * AJAX CONFIGURATION:
     * - URL: route Laravel per API ricerca prodotti
     * - Method: GET (safe, cacheable, idempotent)
     * - Data: query + parametri ricerca (type, limit)
     * - Timeout: 5 secondi per evitare attese infinite
     * 
     * STATE MANAGEMENT:
     * - isSearching flag previene ricerche sovrapposte
     * - Loading state con spinner durante elaborazione
     * - Complete handler garantisce reset flag
     * 
     * ERROR HANDLING: Callbacks separati per success/error
     */
    function searchProdotti(query) {
        // === GUARD CLAUSE ===
        // Previene ricerche multiple simultanee
        if (isSearching) return;
        
        // === STATE MANAGEMENT ===
        isSearching = true;  // Imposta flag ricerca in corso
        
        // === UI FEEDBACK ===
        // Mostra loading spinner per feedback utente
        showSearchLoading();
        
        // === AJAX REQUEST ===
        // Chiamata API Laravel per ricerca prodotti
        $.ajax({
            url: '{{ route("api.prodotti.search") }}',  // Endpoint Laravel API
            method: 'GET',                              // HTTP GET method
            data: { 
                q: query,           // Query di ricerca
                type: 'public',     // Tipo ricerca (pubblico, senza auth)
                limit: 8            // Massimo 8 risultati per performance
            },
            timeout: 5000,  // Timeout 5 secondi
            
            // === SUCCESS CALLBACK ===
            success: function(response) {
                console.log('✅ Ricerca completata:', response);
                displaySearchResults(response, query);  // Mostra risultati
            },
            
            // === ERROR CALLBACK ===
            error: function(xhr, status, error) {
                console.error('❌ Errore ricerca:', error);
                displaySearchError(query);  // Mostra messaggio errore
            },
            
            // === COMPLETE CALLBACK ===
            // Eseguito sempre, sia su success che error
            complete: function() {
                isSearching = false;  // Reset flag ricerca
            }
        });
    }
    
    /**
     * FUNZIONE: showSearchLoading
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Spinner
     * SCOPO: Mostra stato di loading durante ricerca AJAX
     * 
     * UX PATTERN: Loading state per operazioni asincrone
     * Informa utente che ricerca è in elaborazione
     * 
     * BOOTSTRAP COMPONENTS:
     * - spinner-border: animazione rotazione
     * - spinner-border-sm: versione piccola
     * - visually-hidden: testo per screen readers
     */
    function showSearchLoading() {
        $('#search-results').html(`
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
                <span class="text-muted">Ricerca in corso...</span>
            </div>
        `).show();  // Mostra container risultati
    }
    
    /**
     * FUNZIONE: displaySearchResults
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap Cards + Template Strings
     * SCOPO: Renderizza risultati ricerca nel DOM
     * 
     * @param {Object} response - Risposta API con array prodotti
     * @param {string} query - Query originale per link "vedi tutti"
     * 
     * RESPONSE FORMAT EXPECTED:
     * {
     *   success: boolean,
     *   data: [
     *     {nome: string, modello: string, categoria: string, 
     *      foto_url: string, url: string, prezzo: number}
     *   ]
     * }
     * 
     * FEATURES:
     * - Template strings per HTML dinamico
     * - Fallback immagini con onerror
     * - Link "vedi tutti" se risultati >= limit
     * - Animazioni CSS staggered per apparizione elementi
     * - Bootstrap cards responsive layout
     */
    function displaySearchResults(response, query) {
        if (response.success && response.data && response.data.length > 0) {
            // === COSTRUZIONE HTML RISULTATI ===
            let html = '<div class="row g-3">';  // Bootstrap grid con gap
            
            // === ITERAZIONE PRODOTTI ===
            response.data.forEach(function(prodotto) {
                // Fallback per immagini e URL mancanti
                const fotoUrl = prodotto.foto_url || '/images/no-image.png';
                const prodottoUrl = prodotto.url || `{{ route('prodotti.pubblico.index') }}`;
                
                // === TEMPLATE CARD PRODOTTO ===
                // Template string con interpolazione variabili
                html += `
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 search-result-item">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="${fotoUrl}" 
                                         class="me-3 rounded" 
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         alt="${prodotto.nome}"
                                         onerror="this.src='/images/no-image.png'">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">
                                            <a href="${prodottoUrl}" class="text-decoration-none text-dark">
                                                ${prodotto.nome}
                                            </a>
                                        </h6>
                                        <p class="card-text small text-muted mb-1">
                                            <i class="bi bi-tag me-1"></i>
                                            ${prodotto.modello || 'N/A'}
                                        </p>
                                        <span class="badge bg-secondary">${prodotto.categoria || 'Generale'}</span>
                                        ${prodotto.prezzo ? `<span class="badge bg-success ms-1">€${prodotto.prezzo}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            // === LINK "VEDI TUTTI" ===
            // Mostra link se risultati potrebbero essere troncati
            if (response.data.length >= 8) {
                html += `
                    <div class="text-center mt-3">
                        <a href="{{ route('prodotti.pubblico.index') }}?search=${encodeURIComponent(query)}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-arrow-right me-1"></i>
                            Vedi tutti i risultati per "${query}"
                        </a>
                    </div>
                `;
            }
            
            // === RENDERING E ANIMAZIONI ===
            $('#search-results').html(html).show();
            
            // === ANIMAZIONI STAGGERED ===
            // Ogni elemento appare con delay incrementale per effetto "cascata"
            $('.search-result-item').each(function(index) {
                $(this).css({
                    opacity: 0,                    // Inizialmente invisibile
                    transform: 'translateY(20px)'  // Spostato verso il basso
                }).delay(index * 100).animate({   // Delay crescente per effetto staggered
                    opacity: 1                     // Fade in
                }, 300).css('transform', 'translateY(0)');  // Slide up
            });
            
        } else {
            // === NESSUN RISULTATO TROVATO ===
            $('#search-results').html(`
                <div class="text-center py-4">
                    <i class="bi bi-search text-muted display-6 mb-3"></i>
                    <h6 class="text-muted">Nessun prodotto trovato</h6>
                    <p class="text-muted small mb-3">
                        Non abbiamo trovato prodotti corrispondenti a "<strong>${query}</strong>"
                    </p>
                    <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-grid me-1"></i>Sfoglia tutto il catalogo
                    </a>
                </div>
            `).show();
        }
    }
    
    /**
     * FUNZIONE: displaySearchError
     * 
     * LINGUAGGIO: JavaScript + jQuery + Bootstrap
     * SCOPO: Mostra interfaccia errore per fallimenti ricerca
     * 
     * @param {string} query - Query che ha causato l'errore
     * 
     * ERROR UX PATTERN: Errore friendly con azioni recovery
     * - Icona warning per attirare attenzione
     * - Messaggio comprensibile all'utente
     * - Azione "Riprova" per recovery
     * - Alternative per continuare navigazione
     */
    function displaySearchError(query) {
        $('#search-results').html(`
            <div class="text-center py-4">
                <i class="bi bi-exclamation-triangle text-warning display-6 mb-3"></i>
                <h6 class="text-warning">Errore durante la ricerca</h6>
                <p class="text-muted small mb-3">
                    Si è verificato un errore. Riprova o usa il pulsante di ricerca.
                </p>
                <button type="button" class="btn btn-outline-warning btn-sm" onclick="$('#search-form').submit();">
                    <i class="bi bi-arrow-repeat me-1"></i>Riprova
                </button>
            </div>
        `).show();
    }
    
    /**
     * FUNZIONE: hideSearchResults
     * 
     * LINGUAGGIO: JavaScript + jQuery
     * SCOPO: Nasconde container risultati ricerca con animazione
     * 
     * ANIMATION: fadeOut() jQuery per transizione smooth
     * CLEANUP: empty() rimuove contenuto per evitare memory leaks
     */
    function hideSearchResults() {
        $('#search-results').fadeOut(200).empty();  // Fade out + pulizia contenuto
    }
    
    // === GESTIONE CLICK FUORI DALLA RICERCA ===
    
    /**
     * GLOBAL CLICK HANDLER
     * 
     * LINGUAGGIO: JavaScript + jQuery Event Delegation
     * SCOPO: Nasconde ricerca quando utente clicca altrove
     * 
     * UX PATTERN: Close on outside click
     * Comportamento standard per dropdown/search suggestions
     * 
     * LOGIC: Nasconde solo se click NON è su elementi ricerca
     * - Controlla se target è dentro #search-input o #search-results
     * - .closest() trova antenato che matcha selettore
     * - Se .length è 0, click era fuori dall'area ricerca
     */
    $(document).on('click', function(e) {
        // Se click NON è su elementi ricerca, nascondi risultati
        if (!$(e.target).closest('#search-input, #search-results').length) {
            hideSearchResults();
        }
    });
    
    // === EFFETTI HOVER SULLE CARD CATEGORIE ===
    
    /**
     * HOVER EFFECTS: Category Cards
     * 
     * LINGUAGGIO: JavaScript + jQuery + CSS Transforms
     * SCOPO: Aggiunge interattività visiva alle card categoria
     * 
     * HOVER PATTERN: Lift effect con ombra aumentata
     * - mouseenter: solleva card e aumenta ombra
     * - mouseleave: ripristina stato originale
     * 
     * CSS PROPERTIES:
     * - transform: translateY() per movimento verticale
     * - box-shadow: shadow-lg Bootstrap class per ombra
     * - transition: animazione smooth tra stati
     */
    $('.category-card').hover(
        // === MOUSE ENTER ===
        function() {
            $(this).addClass('shadow-lg').css({
                'transform': 'translateY(-8px)',    // Solleva 8px
                'transition': 'all 0.3s ease'      // Transizione smooth
            });
        },
        // === MOUSE LEAVE ===
        function() {
            $(this).removeClass('shadow-lg').css({
                'transform': 'translateY(0)',       // Ripristina posizione
                'transition': 'all 0.3s ease'      // Transizione smooth
            });
        }
    );
    
    // === EFFETTI HOVER SULLE CERTIFICAZIONI ===
    
    /**
     * HOVER EFFECTS: Certification Items
     * 
     * LINGUAGGIO: JavaScript + jQuery + CSS Scale Transform
     * SCOPO: Effetto zoom su elementi certificazione
     * 
     * SCALE EFFECT: Ingrandimento leggero per attirare attenzione
     * - Hover: scale(1.05) = +5% dimensione
     * - Leave: scale(1) = dimensione normale
     * - Transition più rapida (0.2s) per responsività
     */
    $('.certification-item').hover(
        function() {
            $(this).css({
                'transform': 'scale(1.05)',         // Ingrandisce 5%
                'transition': 'all 0.2s ease'      // Transizione rapida
            });
        },
        function() {
            $(this).css({
                'transform': 'scale(1)',            // Ripristina dimensione
                'transition': 'all 0.2s ease'      // Transizione rapida
            });
        }
    );
    
    // === ANIMAZIONI AL SCROLL ===
    
    /**
     * SCROLL ANIMATIONS: Intersection Observer
     * 
     * LINGUAGGIO: JavaScript + Intersection Observer API + CSS Transitions
     * SCOPO: Anima elementi quando entrano nel viewport
     * 
     * INTERSECTION OBSERVER: API moderna per scroll-based animations
     * Performance superiore rispetto a scroll event listeners
     * 
     * CONFIGURATION:
     * - threshold: 0.1 = trigger quando 10% elemento è visibile
     * - rootMargin: trigger 50px prima che elemento sia completamente visibile
     * 
     * ANIMATION EFFECT:
     * - Iniziale: opacity=0, translateY=30px (invisibile, sotto)
     * - Final: opacity=1, translateY=0 (visibile, in posizione)
     * - Transition CSS per smooth animation
     */
    const observerOptions = {
        threshold: 0.1,                      // 10% visibilità per trigger
        rootMargin: '0px 0px -50px 0px'     // Trigger 50px prima
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // === ELEMENTO ENTRA NEL VIEWPORT ===
                entry.target.classList.add('fade-in-up');   // Classe CSS per styling
                entry.target.style.opacity = '1';           // Fade in
                entry.target.style.transform = 'translateY(0)'; // Slide to position
            }
        });
    }, observerOptions);
    
    // === SETUP ELEMENTI DA ANIMARE ===
    // Applica animazioni a tutte le sezioni della pagina
    $('section').each(function() {
        // === STATO INIZIALE ===
        this.style.opacity = '0';                                         // Invisibile
        this.style.transform = 'translateY(30px)';                       // Sotto posizione finale
        this.style.transition = 'opacity 0.8s ease, transform 0.8s ease'; // Animazione smooth
        
        // === REGISTRA PER OSSERVAZIONE ===
        observer.observe(this);  // IntersectionObserver monitora elemento
    });
    
    // === SMOOTH SCROLL PER I LINK INTERNI ===
    
    /**
     * SMOOTH SCROLL: Internal Anchor Links
     * 
     * LINGUAGGIO: JavaScript + jQuery Animation
     * SCOPO: Animazione fluida per navigazione interna pagina
     * 
     * TARGET SELECTOR: a[href^="#"] = link che iniziano con #
     * ANIMATION: jQuery animate() per smooth scroll
     * OFFSET: -100px per header fisso
     */
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();  // Previene jump immediato browser
        const target = $(this.getAttribute('href'));  // Elemento target
        
        if (target.length) {
            // === ANIMAZIONE SCROLL ===
            $('html, body').animate({
                scrollTop: target.offset().top - 100  // Posizione - offset header
            }, 600);  // Durata 600ms
        }
    });
    
    // === CONTATORE ANIMATO PER LE STATISTICHE ===
    
    /**
     * FUNZIONE: animateCounters
     * 
     * LINGUAGGIO: JavaScript + jQuery Animation
     * SCOPO: Anima contatori numerici da 0 al valore finale
     * 
     * COUNTER ANIMATION PATTERN: Incremento graduale per effetto visivo
     * Estrae numero dal testo, anima da 0 al valore, mantiene suffissi
     * 
     * REGEX: /[^\d]/g rimuove tutto tranne digit per estrarre numero
     * ANIMATION: jQuery animate() con custom property
     * EASING: 'swing' per accelerazione/decelerazione naturale
     */
    function animateCounters() {
        $('.stat-item h3').each(function() {
            const $this = $(this);
            // === ESTRAZIONE NUMERO ===
            const countTo = parseInt($this.text().replace(/[^\d]/g, '')) || 0;
            
            if (countTo > 0) {
                // === ANIMAZIONE CONTATORE ===
                $({ countNum: 0 }).animate({
                    countNum: countTo  // Anima da 0 a valore finale
                }, {
                    duration: 2000,     // 2 secondi durata
                    easing: 'swing',    // Easing naturale
                    
                    // === STEP CALLBACK ===
                    // Eseguito ad ogni frame animazione
                    step: function() {
                        const num = Math.floor(this.countNum);          // Numero intero corrente
                        const originalText = $this.text();             // Testo originale
                        const suffix = originalText.replace(/[\d]/g, ''); // Suffisso (es: +, k)
                        $this.text(num + suffix);                      // Aggiorna display
                    },
                    
                    // === COMPLETE CALLBACK ===
                    // Garantisce valore finale corretto
                    complete: function() {
                        const originalText = $this.text();
                        const suffix = originalText.replace(/[\d]/g, '');
                        $this.text(countTo + suffix);  // Valore finale preciso
                    }
                });
            }
        });
    }
    
    // === TRIGGER CONTATORI SU VISIBILITÀ SEZIONE ===
    
    /**
     * STATISTICS OBSERVER: Trigger Animation
     * 
     * LINGUAGGIO: JavaScript + Intersection Observer
     * SCOPO: Avvia contatori quando sezione statistiche è visibile
     * 
     * ONE-TIME TRIGGER: Classe 'animated' previene ri-esecuzione
     * THRESHOLD: 0.5 = 50% sezione visibile per trigger
     */
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');  // Marca come animato
                animateCounters();                        // Avvia animazioni contatori
            }
        });
    }, { threshold: 0.5 });  // 50% visibilità
    
    // === SETUP OSSERVAZIONE SEZIONE STATISTICHE ===
    const statsSection = document.querySelector('.bg-primary');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // === GESTIONE ERRORI IMMAGINI ===
    
    /**
     * IMAGE ERROR HANDLER: Global Fallback
     * 
     * LINGUAGGIO: JavaScript + jQuery Event Delegation
     * SCOPO: Fallback automatico per immagini non caricate
     * 
     * EVENT DELEGATION: $(document).on() per immagini caricate dinamicamente
     * EVENT: 'error' si scatena quando img.src fallisce
     * FALLBACK: /images/no-image.png come placeholder universale
     * LOOP PREVENTION: Controlla che fallback non sia già attivo
     */
    $(document).on('error', 'img', function() {
        if (this.src !== '/images/no-image.png') {
            console.log('🖼️ Caricamento immagine fallito, uso placeholder');
            this.src = '/images/no-image.png';  // Imposta immagine placeholder
        }
    });
    
    // === FEEDBACK INTERAZIONE UTENTE ===
    
    /**
     * BUTTON INTERACTION FEEDBACK
     * 
     * LINGUAGGIO: JavaScript + jQuery + CSS Transform
     * SCOPO: Feedback tattile per interazioni pulsanti
     * 
     * MICRO-INTERACTION: Scale-down effect per feedback click
     * Simula comportamento pulsante fisico che si "preme"
     * 
     * TIMING: 150ms per feedback rapido senza interferire con click
     */
    $('.btn').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();  // Salva HTML originale
        
        // === FEEDBACK VISIVO ===
        // Scale down per simulare pressione
        $btn.css('transform', 'scale(0.95)');
        setTimeout(() => {
            $btn.css('transform', 'scale(1)');  // Ripristina dopo 150ms
        }, 150);
    });
    
    // === SUGGERIMENTI RICERCA ===
    
    /**
     * SEARCH SUGGESTIONS: Dynamic Placeholders
     * 
     * LINGUAGGIO: JavaScript + Array Methods
     * SCOPO: Suggerimenti dinamici per campo ricerca
     * 
     * UX PATTERN: Placeholder dinamico per ispirare ricerche
     * Mostra esempi casuali quando campo è vuoto e riceve focus
     * 
     * ARRAY: searchSuggestions con termini comuni
     * RANDOM: Math.random() per selezione casuale suggerimento
     */
    const searchSuggestions = [
        'lavatrice', 'lavastoviglie', 'forno', 'frigorifero', 
        'asciugatrice', 'condizionatore', 'microonde'
    ];
    
    $('#search-input').on('focus', function() {
        if (!$(this).val().trim()) {  // Solo se campo vuoto
            // === SUGGERIMENTO CASUALE ===
            const randomSuggestion = searchSuggestions[Math.floor(Math.random() * searchSuggestions.length)];
            $(this).attr('placeholder', `Prova con: ${randomSuggestion}`);
        }
    }).on('blur', function() {
        // === RIPRISTINO PLACEHOLDER ORIGINALE ===
        $(this).attr('placeholder', 'Cerca prodotto (es: lavatrice, lav*)');
    });
    
    // === LAZY LOADING PER LE IMMAGINI ===
    
    /**
     * LAZY LOADING: Performance Optimization
     * 
     * LINGUAGGIO: JavaScript + Intersection Observer API
     * SCOPO: Carica immagini solo quando stanno per diventare visibili
     * 
     * PERFORMANCE BENEFITS:
     * - Riduce caricamento iniziale pagina
     * - Risparmia bandwidth per immagini non viste
     * - Migliora First Contentful Paint
     * 
     * HTML STRUCTURE REQUIRED:
     * <img data-src="real-image.jpg" src="placeholder.jpg" class="lazy">
     * 
     * MECHANISM:
     * 1. Observer rileva quando img sta per entrare nel viewport
     * 2. Copia data-src in src per avviare caricamento reale
     * 3. Rimuove classe lazy e smette di osservare
     * 
     * FEATURE DETECTION: Verifica supporto IntersectionObserver
     */
    if ('IntersectionObserver' in window) {
        // === LAZY LOADING OBSERVER ===
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // === CARICAMENTO IMMAGINE ===
                    const img = entry.target;
                    img.src = img.dataset.src;        // data-src → src
                    img.classList.remove('lazy');     // Rimuove classe marker
                    imageObserver.unobserve(img);     // Smette osservazione
                }
            });
        });
        
        // === SETUP LAZY IMAGES ===
        // Osserva tutte le immagini con data-src attribute
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('✅ Homepage completamente inizializzata con tutte le funzionalità');
});

/**
 * FUNZIONE GLOBALE: refreshHomeStats
 * 
 * LINGUAGGIO: JavaScript + jQuery AJAX
 * SCOPO: Aggiorna statistiche homepage via API call
 * 
 * GLOBAL FUNCTION: Accessibile da altri script o console
 * Utile per debug o aggiornamenti manuali
 * 
 * API ENDPOINT: Laravel route per statistiche dashboard
 * ERROR HANDLING: Console logging per debugging
 * 
 * FUTURE ENHANCEMENT: Implementare aggiornamento UI automatico
 */
window.refreshHomeStats = function() {
    console.log('🔄 Refresh statistiche homepage...');
    
    $.ajax({
        url: '{{ route("api.stats.dashboard") }}',  // Endpoint Laravel API
        method: 'GET',                              // HTTP GET method
        
        // === SUCCESS CALLBACK ===
        success: function(response) {
            if (response.success && response.data) {
                // === AGGIORNAMENTO STATISTICHE ===
                // Itera attraverso elementi statistiche per aggiornare valori
                $('.stat-item h3').each(function() {
                    const $this = $(this);
                    // Logica per aggiornare le statistiche basata su response.data
                    // In implementazione completa, mapperebbe response.data ai DOM elements
                    
                    // ESEMPIO IMPLEMENTAZIONE:
                    // if (response.data.prodotti) {
                    //     $('.stat-prodotti').text(response.data.prodotti.totali);
                    // }
                    // if (response.data.utenti) {
                    //     $('.stat-utenti').text(response.data.utenti.totali);
                    // }
                    // if (response.data.malfunzionamenti) {
                    //     $('.stat-malfunzionamenti').text(response.data.malfunzionamenti.totali);
                    // }
                });
                
                // === AGGIORNAMENTO TIMESTAMP ===
                // Mostra quando sono state aggiornate le statistiche
                $('.last-updated').text('Aggiornato: ' + new Date().toLocaleTimeString('it-IT'));
                
                console.log('✅ Statistiche aggiornate');
            }
        },
        
        // === ERROR CALLBACK ===
        error: function(xhr, status, error) {
            console.error('❌ Errore aggiornamento statistiche:', error);
            
            // === USER FEEDBACK PER ERRORI ===
            // In implementazione completa, potrebbe mostrare toast di errore
            // showToast('Errore aggiornamento statistiche', 'warning');
        }
    });
};