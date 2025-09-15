

$(document).ready(function() {
    console.log('contatti caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'contatti') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    console.log('Pagina contatti caricata');
    
    // Validazione form lato client
    $('#contact-form').on('submit', function(e) {
        let isValid = true;
        const requiredFields = ['nome', 'cognome', 'email', 'tipo_richiesta', 'oggetto', 'messaggio'];
        
        // Controlla campi obbligatori
        requiredFields.forEach(function(field) {
            const input = $('#' + field);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        
        // Controlla privacy
        if (!$('#privacy').is(':checked')) {
            $('#privacy').addClass('is-invalid');
            isValid = false;
        } else {
            $('#privacy').removeClass('is-invalid');
        }
        
        // Se non valido, impedisce l'invio
        if (!isValid) {
            e.preventDefault();
            alert('Compila tutti i campi obbligatori e accetta la privacy policy.');
        }
    });
    
    // Rimuovi classe invalid quando l'utente inizia a scrivere
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Auto-resize textarea
    $('#messaggio').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Tracciamento interazioni
    $('a[href^="tel:"]').on('click', function() {
        console.log('Chiamata avviata:', $(this).attr('href'));
    });
    
    $('a[href^="mailto:"]').on('click', function() {
        console.log('Email aperta:', $(this).attr('href'));
    });
});