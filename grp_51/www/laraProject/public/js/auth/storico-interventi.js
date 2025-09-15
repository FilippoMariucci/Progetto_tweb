
$(document).ready(function() {
    console.log('storico.interventi caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'tecnico.interventi') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    $(document).ready(function() {
    console.log('Storico Interventi inizializzato');
    
    // Tooltip per elementi troncati
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Auto-submit form quando cambi i filtri (opzionale)
    $('#periodo, #gravita, #categoria').change(function() {
        // Uncomment per auto-submit: $(this).closest('form').submit();
    });
    
    // Evidenziazione ricerca
    const searchTerm = '{{ request("search") }}';
    if (searchTerm) {
        $('.table tbody').highlight(searchTerm);
    }
    
    console.log('Storico interventi pronto');
});

// Plugin highlight semplice
$.fn.highlight = function(text) {
    return this.each(function() {
        $(this).html($(this).html().replace(
            new RegExp('(' + text + ')', 'gi'),
            '<mark>$1</mark>'
        ));
    });
};
});