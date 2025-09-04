

$(document).ready(function() {
    console.log('prodotti.index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'prodotti.pubblico.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    $(document).ready(function() {
    console.log('📦 Catalogo Prodotti Pubblico - FILTRI CATEGORIA CORRETTI');
    console.log('📊 Categorie disponibili:', @json($categorie ?? []));
    console.log('📊 Stats per categoria:', @json($stats['per_categoria'] ?? []));
    
    // === GESTIONE FORM ===
    $('#clearSearch').on('click', function() {
        $('#search').val('').focus();
    });
    
    // CORREZIONE: Submit automatico quando cambia categoria nel dropdown
    $('#categoria').on('change', function() {
        const categoriaSelezionata = $(this).val();
        console.log('📂 Categoria selezionata dal dropdown:', categoriaSelezionata);
        
        // Submit del form per applicare il filtro
        $('#search-form').submit();
    });
    
    // CORREZIONE: Gestione click sui badge categoria
    $('.category-badge').on('click', function(e) {
        e.preventDefault();
        const categoria = $(this).data('categoria');
        console.log('🏷️ Badge categoria cliccato:', categoria);
        
        // Costruisci URL con filtro categoria
        if (categoria && categoria !== '') {
            window.location.href = `{{ route('prodotti.pubblico.index') }}?categoria=${encodeURIComponent(categoria)}`;
        } else {
            // Badge "Tutte" cliccato - rimuovi filtro
            window.location.href = '{{ route('prodotti.pubblico.index') }}';
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
                    <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                    <div class="small text-muted mt-1">${productName.substring(0, 20)}</div>
                </div>
            </div>
        `);
    });
    
    // === EVIDENZIAZIONE RICERCA ===
    const searchTerm = '{{ request("search") }}';
    if (searchTerm && searchTerm.length > 2 && !searchTerm.includes('*')) {
        $('.card-title, .card-text').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            const highlighted = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlighted);
        });
    }
    
    // === LOADING FORM ===
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
    
    // === DEBUG INFO ===
    @if(request('search') || request('categoria'))
        console.log('🔍 Ricerca pubblica:', {
            termine: '{{ request("search") }}',
            categoria: '{{ request("categoria") }}',
            risultati: {{ $prodotti->total() }},
            timestamp: new Date().toISOString()
        });
    @endif
    
    console.log('✅ Filtri categoria funzionanti correttamente');
});

// === FUNZIONI GLOBALI ===
function resetSearch() {
    window.location.href = '{{ route("prodotti.pubblico.index") }}';
}

function filterByCategory(categoria) {
    console.log('🏷️ Filtro categoria programmatico:', categoria);
    if (categoria && categoria !== '') {
        window.location.href = `{{ route('prodotti.pubblico.index') }}?categoria=${categoria}`;
    } else {
        resetSearch();
    }
}
});