
$(document).ready(function() {
    console.log('prodotti.completo.show caricato');
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.completo.show') {
        return;
    }
    const pageData = window.PageData || {};
    let selectedProducts = [];
    // Il tuo codice JavaScript qui...
    console.log('🔧 Vista prodotto tecnico completo con immagini corrette caricata');
    
    // === MODAL IMMAGINE ===
    window.openImageModal = function(imageSrc, imageTitle) {
        $('#imageModalImg').attr('src', imageSrc).attr('alt', imageTitle);
        $('#imageModalTitle').text(imageTitle);
        $('#imageModal').modal('show');
    };
    
    // === FILTRI MALFUNZIONAMENTI ===
    $('#malfunzionamentoFilter button').on('click', function() {
        const filter = $(this).data('filter');
        $('#malfunzionamentoFilter button').removeClass('active');
        $(this).addClass('active');
        filterMalfunzionamenti(filter);
    });
    
    function filterMalfunzionamenti(filter) {
        const items = $('.malfunzionamento-item');
        
        if (filter === 'all') {
            items.removeClass('d-none').show();
        } else if (filter === 'critica') {
            items.each(function() {
                const gravita = $(this).data('gravita');
                if (gravita === 'critica') {
                    $(this).removeClass('d-none').show();
                } else {
                    $(this).addClass('d-none').hide();
                }
            });
        } else if (filter === 'recent') {
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            items.each(function() {
                const createdDateStr = $(this).data('created');
                const createdDate = new Date(createdDateStr);
                
                if (createdDate >= thirtyDaysAgo) {
                    $(this).removeClass('d-none').show();
                } else {
                    $(this).addClass('d-none').hide();
                }
            });
        }
        
        // Mostra messaggio se nessun risultato
        const visibleCount = items.filter(':not(.d-none)').length;
        if (visibleCount === 0) {
            $('#no-results-message').remove();
            $('#malfunzionamentiList').append(`
                <div class="col-12 text-center py-3" id="no-results-message">
                    <i class="bi bi-search text-muted mb-2" style="font-size: 1.5rem;"></i>
                    <h6 class="text-muted">Nessun risultato per "${filter}"</h6>
                    <button class="btn btn-outline-primary btn-sm" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Mostra Tutti
                    </button>
                </div>
            `);
        } else {
            $('#no-results-message').remove();
        }
    }
    
    window.resetFilters = function() {
        $('#malfunzionamentoFilter button[data-filter="all"]').click();
    };
    
    // === SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        if (!confirm('Confermi di aver riscontrato questo problema?')) {
            return;
        }
        
        const button = $(`button[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...')
              .prop('disabled', true);
        
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
                    showAlert(`Segnalazione registrata! Totale: ${response.nuovo_count}`, 'success');
                    updateSegnalazioniCount(malfunzionamentoId, response.nuovo_count);
                    button.html(originalText).prop('disabled', false);
                } else {
                    throw new Error(response.message || 'Errore sconosciuto');
                }
            },
            error: function(xhr) {
                console.error('Errore segnalazione:', xhr);
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento';
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato';
                }
                
                showAlert(errorMsg, 'danger');
                button.html(originalText).prop('disabled', false);
            }
        });
    };
    
    function updateSegnalazioniCount(malfunzionamentoId, newCount) {
        const badge = $(`#badge-${malfunzionamentoId}`);
        if (badge.length > 0) {
            badge.html(`<i class="bi bi-flag me-1"></i>${newCount}`);
        }
    }
    
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
    
    // === GESTIONE ERRORI IMMAGINI ===
    $('.product-image, img').on('error', function() {
        const $this = $(this);
        const productName = $this.attr('alt') || 'Prodotto';
        
        $this.replaceWith(`
            <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                 style="height: ${$this.height() || 280}px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted mb-2" style="font-size: 2rem;"></i>
                    <div class="small text-muted">${productName}</div>
                </div>
            </div>
        `);
    });
    
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
    
    // === ANALYTICS ===
    if (window.PageData.prodotto) {
        console.log('📊 Vista prodotto:', {
            prodotto_id: window.PageData.prodotto.id,
            nome: window.PageData.prodotto.nome,
            categoria: window.PageData.prodotto.categoria,
            malfunzionamenti: (window.PageData.prodotto.malfunzionamenti || []).length,
            timestamp: new Date().toISOString()
        });
    }
    
    console.log('✅ Vista prodotto tecnico completo completamente caricata');
});
