

$(document).ready(function() {
    console.log('prodotti.index caricato');

    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.pubblico.index') {
        return;
    }

    const pageData = window.PageData || {};
    let selectedProducts = [];

    // Log di debug con dati passati da Blade tramite window.PageData
    console.log('📦 Catalogo Prodotti Pubblico - FILTRI CATEGORIA CORRETTI');
    console.log('📊 Categorie disponibili:', pageData.categorie || []);
    console.log('📊 Stats per categoria:', (pageData.stats && pageData.stats.per_categoria) ? pageData.stats.per_categoria : []);

    // === GESTIONE FORM ===
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });

    // Submit automatico quando cambia categoria nel dropdown
    $('#categoria').on('change', function() {
        const categoriaSelezionata = $(this).val();
        console.log('📂 Categoria selezionata dal dropdown:', categoriaSelezionata);
        $('#search-form').submit();
    });

    // Gestione click sui badge categoria
    $('.category-badge').on('click', function(e) {
        e.preventDefault();
        const categoria = $(this).data('categoria');
        console.log('🏷️ Badge categoria cliccato:', categoria);
        if (categoria && categoria !== '') {
            window.location.href = window.location.pathname + '?categoria=' + encodeURIComponent(categoria);
        } else {
            window.location.href = window.location.pathname;
        }
    });

    // Gestione errori immagini
    $('.product-image').on('error', function() {
        const $this = $(this);
        const productName = $this.attr('alt') || 'Prodotto';
        $this.replaceWith(`
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                 style="height: 140px;">
                <div class="text-center">
                    <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                    <div class="small text-muted mt-1">${productName.substring(0, 20)}</div>
                </div>
            </div>
        `);
    });

    // Evidenziazione ricerca
    const searchTerm = (pageData.searchTerm || '').toString();
    if (searchTerm && searchTerm.length > 2 && !searchTerm.includes('*')) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&')})`, 'gi');
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }

    // Loading form
    $('#search-form').on('submit', function() {
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

    // Debug info ricerca
    if (pageData.searchTerm || pageData.categoria) {
        console.log('🔍 Ricerca pubblica:', {
            termine: pageData.searchTerm,
            categoria: pageData.categoria,
            risultati: pageData.risultati,
            timestamp: new Date().toISOString()
        });
    }

    console.log('✅ Filtri categoria funzionanti correttamente');
});

// === FUNZIONI GLOBALI ===
function resetSearch() {
    window.location.href = window.location.pathname;
}

function filterByCategory(categoria) {
    console.log('🏷️ Filtro categoria programmatico:', categoria);
    if (categoria && categoria !== '') {
        window.location.href = window.location.pathname + '?categoria=' + encodeURIComponent(categoria);
    } else {
        resetSearch();
    }
}