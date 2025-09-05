

$(document).ready(function() {
    console.log('centri.show caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'centri.show') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('Vista centro assistenza caricata:', '{{ $centro->nome }}');
    
    // Analytics per tracking delle interazioni
    $('a[href^="tel:"]').on('click', function() {
        const numero = $(this).attr('href').replace('tel:', '');
        console.log('Chiamata avviata verso:', numero);
        
        // Potresti inviare un evento analytics qui
        // gtag('event', 'phone_call', {
        //     centro_id: {{ $centro->id }},
        //     centro_nome: '{{ $centro->nome }}',
        //     numero: numero
        // });
    });
    
    $('a[href^="mailto:"]').on('click', function() {
        const email = $(this).attr('href').replace('mailto:', '');
        console.log('Email aperta verso:', email);
        
        // Analytics per email
        // gtag('event', 'email_click', {
        //     centro_id: {{ $centro->id }},
        //     email: email
        // });
    });
    
    // Tracking apertura Google Maps
    $('a[href*="google.com/maps"]').on('click', function() {
        console.log('Mappa aperta per centro:', '{{ $centro->nome }}');
        
        // Analytics per mappe
        // gtag('event', 'map_view', {
        //     centro_id: {{ $centro->id }},
        //     centro_nome: '{{ $centro->nome }}'
        // });
    });
    
    // Smooth scroll per link interni (se presenti)
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Auto-dismiss alert dopo 5 secondi
    $('.alert-dismissible').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut('slow', function() {
                alert.remove();
            });
        }, 5000);
    });
});