
$(document).ready(function() {
    console.log('centri.index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'centri.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    // Log di debug per il caricamento della pagina
    console.log('Pagina centri di assistenza caricata');
    console.log('Centri trovati: {{ isset($centri) ? $centri->count() : 0 }}');
    
    // === GESTIONE FORM DI RICERCA ===
    // Previene il submit accidentale e aggiunge validazioni
    $('#searchForm').on('submit', function(e) {
        const searchTerm = $('#search').val().trim();
        const provincia = $('#provincia').val();
        const citta = $('#citta').val().trim();
        
        // Verifica se almeno un campo è compilato
        if (!searchTerm && !provincia && !citta) {
            e.preventDefault();
            alert('Inserisci almeno un criterio di ricerca');
            return false;
        }
        
        // Log per debug
        console.log('Form sottomesso con parametri:', {
            search: searchTerm,
            provincia: provincia,
            citta: citta
        });
    });
    // === DEBUGGING E VALIDAZIONE ===
    // Verifica che tutti gli elementi necessari siano presenti
    console.log('Elementi form presenti:', {
        form: $('#searchForm').length,
        searchInput: $('#search').length,
        provinciaSelect: $('#provincia').length,
        cittaInput: $('#citta').length
    });
    
    // === RICERCA IN TEMPO REALE (OPZIONALE) ===
    // Implementa una ricerca con delay per evitare troppe chiamate
    let searchTimer;
    $('#search').on('input', function() {
        // Cancella il timer precedente se l'utente continua a digitare
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();
        
        // Avvia ricerca solo per termini di almeno 3 caratteri
        if (searchTerm.length >= 3) {
            searchTimer = setTimeout(function() {
                console.log('Ricerca per:', searchTerm);
                // Esempio di ricerca automatica (commentato per ora):
                // $('#searchForm').submit();
            }, 1000); // Aspetta 1 secondo prima di eseguire la ricerca
        }
    });
    
    // === ANALYTICS E TRACKING ===
    // Traccia quando un utente visualizza un centro
    $('.card').on('click', function() {
        const centerName = $(this).find('.card-title').text().trim();
        console.log('Centro visualizzato:', centerName);
        // Qui potresti inviare dati analytics
    });
    
    // === GESTIONE EVENTI COMUNICAZIONE ===
    // Traccia utilizzo pulsanti telefono
    $('a[href^="tel:"]').on('click', function() {
        console.log('Chiamata avviata:', $(this).attr('href'));
    });
    
    // Traccia utilizzo pulsanti email
    $('a[href^="mailto:"]').on('click', function() {
        console.log('Email aperta:', $(this).attr('href'));
    });

    // === TOOLTIP PER BADGE STATO TECNICI ===
    // Inizializza i tooltip per gli indicatori di stato
    $('[title]').tooltip();

    // === GESTIONE RESPONSIVE ===
    // Adatta il layout per dispositivi mobili
    function handleResponsive() {
        if ($(window).width() < 768) {
            // Su mobile, rendi i badge più compatti
            $('.badge.fs-6').removeClass('fs-6').addClass('small');
        } else {
            // Su desktop, ripristina dimensioni normali
            $('.badge.small').removeClass('small').addClass('fs-6');
        }
    }
    
    // Esegui al caricamento e al ridimensionamento
    handleResponsive();
    $(window).resize(handleResponsive);
});
