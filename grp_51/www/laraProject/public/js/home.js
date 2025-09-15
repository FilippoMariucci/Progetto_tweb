

$(document).ready(function() {
    console.log('Home caricato');

    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'home') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('🏠 Homepage inizializzata');
    
    // === RICERCA DINAMICA PRODOTTI ===
    let searchTimeout;
    let isSearching = false;
    
    $('#search-input').on('input', function() {
        const query = $(this).val().trim();
        
        // Cancella il timeout precedente
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            // Debounce di 300ms per evitare troppe chiamate
            searchTimeout = setTimeout(() => {
                searchProdotti(query);
            }, 300);
        } else {
            hideSearchResults();
        }
    });
    
    /**
     * Effettua la ricerca prodotti via AJAX
     * @param {string} query - Termine di ricerca
     */
    function searchProdotti(query) {
        if (isSearching) return;
        
        isSearching = true;
        
        // Mostra loading
        showSearchLoading();
        
        // Effettua la chiamata AJAX
        $.ajax({
            url: '{{ route("api.prodotti.search") }}',
            method: 'GET',
            data: { 
                q: query, 
                type: 'public',
                limit: 8 
            },
            timeout: 5000,
            success: function(response) {
                console.log('✅ Ricerca completata:', response);
                displaySearchResults(response, query);
            },
            error: function(xhr, status, error) {
                console.error('❌ Errore ricerca:', error);
                displaySearchError(query);
            },
            complete: function() {
                isSearching = false;
            }
        });
    }
    
    /**
     * Mostra lo stato di loading durante la ricerca
     */
    function showSearchLoading() {
        $('#search-results').html(`
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
                <span class="text-muted">Ricerca in corso...</span>
            </div>
        `).show();
    }
    
    /**
     * Visualizza i risultati della ricerca
     * @param {Object} response - Risposta dell'API
     * @param {string} query - Query di ricerca
     */
    function displaySearchResults(response, query) {
        if (response.success && response.data && response.data.length > 0) {
            let html = '<div class="row g-3">';
            
            response.data.forEach(function(prodotto) {
                const fotoUrl = prodotto.foto_url || '/images/no-image.png';
                const prodottoUrl = prodotto.url || `{{ route('prodotti.pubblico.index') }}`;
                
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
            
            // Aggiungi link per vedere tutti i risultati se necessario
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
            
            $('#search-results').html(html).show();
            
            // Applica animazioni agli elementi
            $('.search-result-item').each(function(index) {
                $(this).css({
                    opacity: 0,
                    transform: 'translateY(20px)'
                }).delay(index * 100).animate({
                    opacity: 1
                }, 300).css('transform', 'translateY(0)');
            });
            
        } else {
            // Nessun risultato trovato
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
     * Visualizza errore durante la ricerca
     * @param {string} query - Query che ha causato l'errore
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
     * Nasconde i risultati della ricerca
     */
    function hideSearchResults() {
        $('#search-results').fadeOut(200).empty();
    }
    
    // === GESTIONE CLICK FUORI DALLA RICERCA ===
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-input, #search-results').length) {
            hideSearchResults();
        }
    });
    
    // === EFFETTI HOVER SULLE CARD CATEGORIE ===
    $('.category-card').hover(
        function() {
            $(this).addClass('shadow-lg').css({
                'transform': 'translateY(-8px)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).removeClass('shadow-lg').css({
                'transform': 'translateY(0)',
                'transition': 'all 0.3s ease'
            });
        }
    );
    
    // === EFFETTI HOVER SULLE CERTIFICAZIONI ===
    $('.certification-item').hover(
        function() {
            $(this).css({
                'transform': 'scale(1.05)',
                'transition': 'all 0.2s ease'
            });
        },
        function() {
            $(this).css({
                'transform': 'scale(1)',
                'transition': 'all 0.2s ease'
            });
        }
    );
    
    // === ANIMAZIONI AL SCROLL ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Applica animazioni alle sezioni
    $('section').each(function() {
        this.style.opacity = '0';
        this.style.transform = 'translateY(30px)';
        this.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(this);
    });
    
    // === SMOOTH SCROLL PER I LINK INTERNI ===
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });
    
    // === CONTATORE ANIMATO PER LE STATISTICHE ===
    function animateCounters() {
        $('.stat-item h3').each(function() {
            const $this = $(this);
            const countTo = parseInt($this.text().replace(/[^\d]/g, '')) || 0;
            
            if (countTo > 0) {
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        const num = Math.floor(this.countNum);
                        const originalText = $this.text();
                        const suffix = originalText.replace(/[\d]/g, '');
                        $this.text(num + suffix);
                    },
                    complete: function() {
                        const originalText = $this.text();
                        const suffix = originalText.replace(/[\d]/g, '');
                        $this.text(countTo + suffix);
                    }
                });
            }
        });
    }
    
    // Avvia contatori quando la sezione statistiche diventa visibile
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                animateCounters();
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.bg-primary');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // === GESTIONE ERRORI IMMAGINI ===
    $(document).on('error', 'img', function() {
        if (this.src !== '/images/no-image.png') {
            console.log('🖼️ Caricamento immagine fallito, uso placeholder');
            this.src = '/images/no-image.png';
        }
    });
    
    // === FEEDBACK INTERAZIONE UTENTE ===
    $('.btn').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Feedback visivo sui pulsanti
        $btn.css('transform', 'scale(0.95)');
        setTimeout(() => {
            $btn.css('transform', 'scale(1)');
        }, 150);
    });
    
    // === SUGGERIMENTI RICERCA ===
    const searchSuggestions = [
        'lavatrice', 'lavastoviglie', 'forno', 'frigorifero', 
        'asciugatrice', 'condizionatore', 'microonde'
    ];
    
    $('#search-input').on('focus', function() {
        if (!$(this).val().trim()) {
            const randomSuggestion = searchSuggestions[Math.floor(Math.random() * searchSuggestions.length)];
            $(this).attr('placeholder', `Prova con: ${randomSuggestion}`);
        }
    }).on('blur', function() {
        $(this).attr('placeholder', 'Cerca prodotto (es: lavatrice, lav*)');
    });
    
    // === LAZY LOADING PER LE IMMAGINI ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('✅ Homepage completamente inizializzata con tutte le funzionalità');
});

/**
 * Funzione globale per refresh statistiche (se necessario)
 */
window.refreshHomeStats = function() {
    console.log('🔄 Refresh statistiche homepage...');
    
    $.ajax({
        url: '{{ route("api.stats.dashboard") }}',
        method: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                // Aggiorna i valori statistici
                $('.stat-item h3').each(function() {
                    const $this = $(this);
                    // Logica per aggiornare le statistiche
                });
                console.log('✅ Statistiche aggiornate');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Errore aggiornamento statistiche:', error);
        }
});
    };