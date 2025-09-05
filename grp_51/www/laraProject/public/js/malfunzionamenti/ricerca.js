
$(document).ready(function() {
    console.log('Dettaglio malfunzionamento ricerca caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'malfunzionamenti.ricerca') {
        return;
    }
    
    const pageData = window.PageData || {};
    const malfunzionamento = pageData.malfunzionamento;
    
    if (!malfunzionamento) {
        console.warn('Dati malfunzionamento non disponibili');
        return;
    }
    
    // Il tuo codice JavaScript qui...
    console.log('🔍 Ricerca Malfunzionamenti con Immagini caricata');
    
    // === FUNZIONE SEGNALAZIONE MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!malfunzionamentoId) {
            showAlert('Errore: ID malfunzionamento non valido', 'danger');
            return;
        }
        
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return;
        }
        
        // Trova il bottone e mostra loading
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...')
              .prop('disabled', true)
              .addClass('btn-loading');
        
        // Chiamata AJAX
        $.ajax({
            url: `${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    // Aggiorna il contatore
                    $(`[data-segnalazioni-count="${malfunzionamentoId}"]`)
                        .html(`<i class="bi bi-flag me-1"></i>${response.nuovo_count} segnalazioni`);
                    
                    // Cambia il pulsante per mostrare successo
                    button.removeClass('btn-outline-warning btn-loading')
                          .addClass('btn-success')
                          .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                          .prop('disabled', true);
                    
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                } else {
                    throw new Error(response.message || 'Errore nella risposta');
                }
            },
            error: function(xhr) {
                console.error('Errore AJAX:', xhr);
                let msg = 'Errore nella segnalazione del malfunzionamento';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    msg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    msg = 'Malfunzionamento non trovato';
                } else if (xhr.status === 429) {
                    msg = 'Troppi tentativi. Riprova tra qualche minuto';
                }
                
                showAlert(msg, 'danger');
                button.html(originalText)
                      .prop('disabled', false)
                      .removeClass('btn-loading');
            }
        });
    };
    
    // === FUNZIONE ALERT ===
    function showAlert(message, type = 'info', duration = 5000) {
        $('.custom-alert').remove();
        
        const icons = {
            success: 'check-circle-fill',
            danger: 'exclamation-triangle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;" 
                 role="alert">
                <i class="bi bi-${icons[type] || 'info-circle-fill'} me-2"></i>
                ${message}
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
    
    // === GESTIONE IMMAGINI ===
    $('.product-thumb').on('error', function() {
        const $this = $(this);
        const productName = $this.attr('alt') || 'Prodotto';
        
        $this.replaceWith(`
            <div class="product-thumb-placeholder rounded shadow-sm d-flex align-items-center justify-content-center bg-light" 
                 style="width: 100%; height: 120px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                    <div class="small text-muted mt-1">${productName.substring(0, 15)}</div>
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
        }, {
            rootMargin: '50px'
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // === EVIDENZIAZIONE RICERCA ===
    const searchTerm = '{{ request("q") }}';
    if (searchTerm && searchTerm.length > 2) {
        $('.fw-bold a, p.text-muted').each(function() {
            const text = $(this).html();
            const regex = new RegExp(
    `(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`,
    'gi'
);
const highlighted = text.replace(regex, '<mark>$1</mark>');
$(this).html(highlighted);
        });
                }
    
    // === HOVER EFFECTS ===
    $('.product-thumb, .product-thumb-placeholder').hover(
        function() {
            $(this).addClass('shadow');
        },
        function() {
            $(this).removeClass('shadow');
        }
    );
    
    // === FORM SUBMIT LOADING ===
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
    
    // === SHORTCUT TASTIERA ===
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#q').focus();
        }
        if (e.key === 'Escape') {
            $('#q').blur();
        }
    });
    
    // === TOOLTIP ===
    $('[title]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
    
    // === ANALYTICS RICERCA ===
    if (
        window.PageData &&
        (
            window.PageData.q ||
            window.PageData.gravita ||
            window.PageData.difficolta ||
            window.PageData.categoria_prodotto
        )
    ) {
        console.log('🔍 Ricerca malfunzionamenti effettuata:', {
            termine: window.PageData.q || '',
            gravita: window.PageData.gravita || '',
            difficolta: window.PageData.difficolta || '',
            categoria: window.PageData.categoria_prodotto || '',
            risultati: window.PageData.malfunzionamentiTotal || 0,
            timestamp: new Date().toISOString()
        });
    }
    
    // === AUTO-REFRESH OPZIONALE ===
    if (window.PageData && window.PageData.gravita === 'critica') {
        setInterval(() => {
            console.log('🔄 Auto-refresh per problemi critici');
            // location.reload(); // Decommentare se necessario
        }, 300000);
    }
    
    // === PERFORMANCE MONITORING ===
    const performanceData = {
        loadTime: Date.now(),
        totalResults: window.PageData.malfunzionamentiTotal,
        displayedResults: window.PageData.malfunzionamentiCount,
        imagesLoaded: $('.product-thumb').length,
        searchActive: window.PageData.searchActive
    };
    
    console.log('📊 Performance ricerca:', performanceData);
    
    // === CLEANUP ===
    $(window).on('beforeunload', function() {
        $('[title]').tooltip('dispose');
        console.log('🧹 Cleanup ricerca completato');
    });
    
    console.log('✅ Ricerca Malfunzionamenti con Immagini completamente caricata');
});

// === FUNZIONI GLOBALI UTILITY ===

/**
 * Filtra risultati per gravità
 */
function filterByGravity(gravita) {
    const currentUrl = new URL(window.location.href);
    
    if (gravita) {
        currentUrl.searchParams.set('gravita', gravita);
    } else {
        currentUrl.searchParams.delete('gravita');
    }
    
    window.location.href = currentUrl.toString();
}

/**
 * Reset completo filtri
 */
function resetAllFilters() {
    window.location.href = `{{ route('malfunzionamenti.ricerca') }}`;
}

/**
 * Condividi risultato di ricerca
 */
function shareSearchResults() {
    const url = window.location.href;
    const title = 'Ricerca Malfunzionamenti - Sistema Assistenza Tecnica';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        });
    } else {
        // Fallback: copia URL negli appunti
        navigator.clipboard.writeText(url).then(() => {
            showAlert('Link copiato negli appunti!', 'success', 2000);
        });
    }
}