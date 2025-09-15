
function aggiornaStatistiche() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>';
    btn.disabled = true;
    
    setTimeout(() => location.reload(), 1000);
}


$(document).ready(function() {
    console.log('📊 Statistiche Staff Compatte caricato');

    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'staff.statistiche') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
     console.log('📊 Statistiche Staff Compatte inizializzate');

    // === ANIMAZIONI CONTATORI ===
    function animateCounters() {
        $('.h5.fw-bold').each(function() {
            const $counter = $(this);
            const text = $counter.text().trim();
            const target = parseInt(text.replace(/[^\d]/g, ''));
            
            if (!isNaN(target) && target > 0 && target < 500) {
                $counter.text('0');
                
                $({ counter: 0 }).animate({ counter: target }, {
                    duration: 1200,
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
    }

    setTimeout(animateCounters, 300);

    // === TOOLTIP COMPATTI ===
    $('[title]').tooltip({
        trigger: 'hover',
        placement: 'top',
        delay: { show: 300, hide: 100 }
    });

    // === HOVER GRAFICI ===
    $('.chart-bar').hover(
        function() {
            $(this).addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-sm');
        }
    );


    // === NOTIFICHE ===
    if (window.PageData.sessionSuccess) {
        showNotification('success', window.PageData.sessionSuccess);
    }
    if (window.PageData.sessionError) {
        showNotification('error', window.PageData.sessionError);
    }

    function showNotification(type, message) {
        const alertClass = type === 'error' ? 'danger' : type;
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        const notification = $(`
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;" 
                 role="alert">
                <i class="bi bi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        setTimeout(() => $('.alert').alert('close'), 4000);
    }

    window.showNotification = showNotification;

    console.log('✅ Statistiche Staff Compatte caricate');
});

// Auto-refresh ogni 15 minuti
setInterval(() => {
    console.log('🔄 Auto-refresh statistiche staff');
}, 900000);







   