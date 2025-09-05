

$(document).ready(function() {
    console.log('prodotti.pubblico.show caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.pubblico.show') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    $(document).ready(function() {
    console.log('📄 Vista prodotto pubblica con stile unificato caricata');
    
    // === MODAL IMMAGINE IDENTICO ===
    window.openImageModal = function(imageSrc, imageTitle) {
        $('#imageModalImg').attr('src', imageSrc).attr('alt', imageTitle);
        $('#imageModalTitle').text(imageTitle);
        $('#imageModal').modal('show');
    };
    
    // === SCROLL TO SECTION ===
    window.scrollToSection = function(section) {
        let targetSelector = '';
        
        if (section === 'installazione') {
            targetSelector = 'h6:contains("Modalità Installazione")';
        } else if (section === 'uso') {
            targetSelector = 'h6:contains("Modalità d\'Uso")';
        }
        
        if (targetSelector) {
            const $target = $(targetSelector).closest('.col-lg-4');
            if ($target.length > 0) {
                $target.addClass('section-highlight');
                $('html, body').animate({
                    scrollTop: $target.offset().top - 100
                }, 500);
                
                setTimeout(() => {
                    $target.removeClass('section-highlight');
                }, 2000);
            }
        }
    };
    
    // === GESTIONE ERRORI IMMAGINI ===
    $('.product-image, img').on('error', function() {
        const $this = $(this);
        const productName = $this.attr('alt') || 'Prodotto';
        const height = $this.height() || 280;
        
        $this.replaceWith(`
            <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                 style="height: ${height}px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted mb-2" style="font-size: 2rem;"></i>
                    <div class="small text-muted">${productName.substring(0, 30)}</div>
                </div>
            </div>
        `);
    });
    
    // === ALERT SYSTEM ===
    function showAlert(message, type = 'info', duration = 4000) {
        $('.custom-alert').remove();
        
        const icons = {
            success: 'check-circle-fill',
            danger: 'exclamation-triangle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; max-width: 350px;" 
                 role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-${icons[type] || 'info-circle-fill'} me-2"></i>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.custom-alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // === TOOLTIP ===
    $('[title]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
    
    // === NOTIFICHE SESSIONE ===
    if (window.PageData.sessionSuccess) {
        showAlert(window.PageData.sessionSuccess, 'success');
    }
    if (window.PageData.sessionError) {
        showAlert(window.PageData.sessionError, 'danger');
    }
    if (window.PageData.sessionInfo) {
        showAlert(window.PageData.sessionInfo, 'info');
    }

    // === ANALYTICS E DEBUG ===
    const prodottoData = Object.assign({}, window.PageData.prodotto || {}, {
        vista_tipo: 'pubblica',
        user_authenticated: window.PageData.user ? true : false,
        user_can_view_malfunctions: window.PageData.user_can_view_malfunctions || false,
        timestamp: new Date().toISOString()
    });
    console.log('📊 Vista prodotto pubblico:', prodottoData);
    
    // === PERFORMANCE MONITORING ===
    const performanceData = {
        loadTime: Date.now(),
        imagesLoaded: $('img').length,
        cardsCount: $('.card').length,
        buttonsCount: $('.btn').length
    };
    
    console.log('⚡ Performance vista pubblica:', performanceData);
    
    // === LAZY LOADING IMMAGINI (se supportato) ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src && !img.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // === SHORTCUT TASTIERA ===
    $(document).on('keydown', function(e) {
        // ESC chiude modal
        if (e.key === 'Escape' && $('#imageModal').hasClass('show')) {
            $('#imageModal').modal('hide');
        }
        
        // Ctrl+K per focus ricerca (se presente)
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const $searchInput = $('#search');
            if ($searchInput.length > 0) {
                $searchInput.focus();
            }
        }
    });
    
    // === SMOOTH SCROLL PER LINK INTERNI ===
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // === TRACKING CLICKS SUI BOTTONI ===
    $('.btn').on('click', function() {
        const btnText = $(this).text().trim();
        const btnClass = $(this).attr('class');
        
        console.log('🔘 Click bottone:', {
            text: btnText,
            classes: btnClass,
            timestamp: new Date().toISOString()
        });
    });
    
    // === STATO CARICAMENTO COMPLETATO ===
    setTimeout(() => {
        console.log('✅ Vista prodotto pubblica con stile unificato completamente caricata');
        
        // Rimuovi eventuali indicatori di loading
        $('.loading-indicator').fadeOut();
        
        // Attiva animazioni se necessario
        $('.card').addClass('loaded');
        
    }, 100);
    
    console.log('📱 Responsive breakpoints attivi');
});

// === FUNZIONI GLOBALI ===

/**
 * Funzione per condividere prodotto (futura implementazione)
 */
window.shareProdotto = function() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $prodotto->nome }}',
            text: '{{ Str::limit($prodotto->descrizione, 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: copia URL
        navigator.clipboard.writeText(window.location.href).then(() => {
            showAlert('Link copiato negli appunti!', 'success');
        });
    }
};

/**
 * Funzione per stampare scheda prodotto
 */
window.printProdotto = function() {
    window.print();
};

/**
 * Funzione per favoriti (futura implementazione)
 */
window.toggleFavorite = function() {
    // Implementazione futura con localStorage o backend
    console.log('Toggle favoriti per prodotto ID: {{ $prodotto->id }}');
};
});