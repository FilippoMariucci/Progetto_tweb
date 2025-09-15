
$(document).ready(function() {
    console.log('prodotti.completo.index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.completo.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('📦 Catalogo Tecnico Compatto caricato');
    console.log('📊 Prodotti visualizzati:', window.PageData.prodottiCount);
    
    // === GESTIONE FORM ===
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    $('#categoria, #filter').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // === SHORTCUT TASTIERA ===
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
        if (e.key === 'Escape') {
            $('#search').blur();
        }
    });
    
    // === GESTIONE ERRORI IMMAGINI ===
    $('.product-image').on('error', function() {
        const $this = $(this);
        const productName = $this.attr('alt') || 'Prodotto';
        
        $this.replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 140px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                    <div class="small text-muted mt-1">${productName}</div>
                </div>
            </div>
        `);
    });
    
    // === LAZY LOADING IMMAGINI ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
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
    
    // === EVIDENZIAZIONE RICERCA ===
    // Per evidenziare la ricerca, passare il termine da Blade a window.PageData.searchTerm
    const searchTerm = window.PageData.searchTerm || '';
    if (searchTerm && searchTerm.length > 2 && !searchTerm.includes('*')) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&')})`, 'gi');
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }
    
    // === TOOLTIP ===
    $('[data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
    
    // === LOADING FORM ===
    $('form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        if ($submitBtn.length) {
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Cercando...')
                      .prop('disabled', true);
            
            setTimeout(() => {
                $submitBtn.html(originalText).prop('disabled', false);
            }, 3000);
        }
    });
    
    // === ANALYTICS RICERCA ===
    if (window.PageData.searchTerm || window.PageData.categoria || window.PageData.filtro) {
        console.log('🔍 Ricerca tecnica effettuata:', {
            termine: window.PageData.searchTerm,
            categoria: window.PageData.categoria,
            filtro: window.PageData.filtro,
            risultati: window.PageData.prodottiTotal,
            staff_filter: window.PageData.staffFilter,
            timestamp: new Date().toISOString()
        });
    }
    
    // === AUTO-REFRESH OPZIONALE ===
    if (window.PageData.filtro === 'critici') {
        // Aggiorna ogni 5 minuti per problemi critici
        setInterval(() => {
            console.log('🔄 Auto-refresh per problemi critici');
            // location.reload(); // Decommentare se necessario
        }, 300000);
    }
    
    // === ANIMAZIONI CONTATORI ===
    setTimeout(() => {
        $('.fw-bold').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            const target = parseInt(text);
            
            if (!isNaN(target) && target > 0 && target < 100) {
                $counter.text('0');
                
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1000,
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
    }, 300);
    
    // === GESTIONE STATI HOVER ===
    $('.product-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
    
    // === CONFERME AZIONI ===
    $('[data-confirm]').on('click', function(e) {
        const message = $(this).data('confirm') || 'Sei sicuro di voler procedere?';
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
    
    // === NOTIFICHE SESSIONE ===
    // Per notifiche sessione, passare i messaggi da Blade a window.PageData (es: window.PageData.sessionSuccess)
    if (window.PageData.sessionSuccess) {
        showNotification(window.PageData.sessionSuccess, 'success');
    }
    if (window.PageData.sessionError) {
        showNotification(window.PageData.sessionError, 'error');
    }
    if (window.PageData.sessionWarning) {
        showNotification(window.PageData.sessionWarning, 'warning');
    }
    
    function showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        }[type] || 'info-circle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;" 
                 role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-rimuovi dopo 4 secondi
        setTimeout(() => {
            notification.alert('close');
        }, 4000);
    }
    
    // Rende la funzione disponibile globalmente
    window.showNotification = showNotification;
    
    // === PERFORMANCE MONITORING ===
    const performanceData = {
        loadTime: Date.now(),
        totalProducts: window.PageData.prodottiTotal,
        displayedProducts: window.PageData.prodottiCount,
        searchActive: window.PageData.searchActive,
        filtersActive: window.PageData.filtersActive
    };
    
    console.log('📊 Performance Data:', performanceData);
    
    // === CLEANUP ===
    $(window).on('beforeunload', function() {
        // Cleanup tooltip
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        console.log('🧹 Cleanup completato');
    });
    
    console.log('✅ Catalogo Tecnico Compatto completamente caricato');
});

// === FUNZIONI GLOBALI UTILITY ===

/**
 * Filtra prodotti per categoria via JavaScript (opzionale)
 */
function filterByCategory(categoria) {
    if (categoria) {
        window.location.href = `{{ route('prodotti.completo.index') }}?categoria=${categoria}`;
    } else {
        window.location.href = `{{ route('prodotti.completo.index') }}`;
    }
}

/**
 * Ricerca rapida senza submit
 */
function quickSearch(term) {
    if (term.length > 2) {
        $('#search').val(term);
        $('form').submit();
    }
}

/**
 * Toggle filtro staff
 */
function toggleStaffFilter(filter) {
    const currentUrl = new URL(window.location.href);
    
    if (filter === 'my_products') {
        currentUrl.searchParams.set('staff_filter', 'my_products');
    } else {
        currentUrl.searchParams.delete('staff_filter');
    }
    
    window.location.href = currentUrl.toString();
}

/**
 * Reset completo filtri
 */
function resetAllFilters() {
    window.location.href = `{{ route('prodotti.completo.index') }}`;
}

/**
 * Funzione per evidenziare un prodotto specifico
 */
function highlightProduct(productId) {
    const $card = $(`.product-card[data-product-id="${productId}"]`);
    if ($card.length) {
        $card.addClass('border-primary border-3 shadow-lg');
        $card[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        setTimeout(() => {
            $card.removeClass('border-primary border-3');
        }, 3000);
    }
}

/**
 * Statistiche di utilizzo (opzionale per analytics)
 */
function trackUsage(action, details = {}) {
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            ...details,
            page_title: 'Catalogo Tecnico Completo',
            page_location: window.location.href
        });
    }
    
    console.log('📈 Azione tracciata:', action, details);
}

// === EVENT LISTENERS GLOBALI ===

// Track click sui prodotti
$(document).on('click', '.product-card a', function() {
    const productName = $(this).closest('.product-card').find('.card-title').text();
    trackUsage('product_view', {
        product_name: productName,
        view_type: 'tecnico_completo'
    });
});

// Track ricerche
$(document).on('submit', 'form', function() {
    const searchTerm = $('#search').val();
    const categoria = $('#categoria').val();
    
    if (searchTerm || categoria) {
        trackUsage('search_performed', {
            search_term: searchTerm,
            category: categoria,
            results_count: window.PageData.prodottiTotal
        });
    }
});