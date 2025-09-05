


    console.log('staff.create.nuova.soluzione caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'staff.create.nuova.soluzione') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    document.addEventListener('DOMContentLoaded', function() {
                    // Disabilita tutti i campi del form se non ci sono prodotti assegnati
                    const form = document.getElementById('formNuovaSoluzione');
                    if (form) {
                        const inputs = form.querySelectorAll('input:not([type="button"]), textarea, select, button[type="submit"]');
                        inputs.forEach(input => {
                            input.disabled = true;
                            if (input.tagName.toLowerCase() === 'select') {
                                input.innerHTML = '<option value="">Nessun prodotto assegnato</option>';
                            } else if (input.tagName.toLowerCase() !== 'button') {
                                input.placeholder = 'Richiedere assegnazione prodotti all\'amministratore';
                            }
                        });
                        
                        // Aggiungi messaggio informativo al form
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-info mt-3';
                        alertDiv.innerHTML = `
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Modulo disabilitato:</strong> 
                            Senza prodotti assegnati non è possibile creare nuove soluzioni.
                        `;
                        form.appendChild(alertDiv);
                    }
                });

                // Gestione selezione prodotto con informazioni dettagliate
@if(isset($isNuovaSoluzione) && $isNuovaSoluzione && isset($prodotti) && $prodotti->count() > 0)
$(document).ready(function() {
    
    // Gestione selezione prodotto migliorata
    $('#prodotto_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const prodottoId = selectedOption.val();
        
        if (prodottoId) {
            // Estrai dati dal prodotto selezionato
            const prodottoData = {
                id: prodottoId,
                nome: selectedOption.text().split(' (')[0], // Rimuove info problemi
                categoria: selectedOption.data('categoria'),
                modello: selectedOption.data('modello'),
                codice: selectedOption.data('codice'),
                problemi: selectedOption.data('problemi') || 0,
                critici: selectedOption.data('critici') || 0
            };
            
            // Mostra info card dettagliata
            showDetailedProdottoInfo(prodottoData);
            
            // Analytics
            console.log('Prodotto assegnato selezionato:', prodottoData);
            
        } else {
            // Nascondi info se deselezionato
            hideDetailedProdottoInfo();
        }
    });
    
    // Funzione per mostrare info dettagliate del prodotto
    function showDetailedProdottoInfo(data) {
        // Rimuovi info precedenti
        $('#prodotto-info-container').empty();
        
        // Determina lo stato del prodotto
        let statoClass = 'success';
        let statoIcon = 'check-circle';
        let statoText = 'Nessun problema noto';
        
        if (data.critici > 0) {
            statoClass = 'danger';
            statoIcon = 'exclamation-triangle';
            statoText = `${data.critici} problema/i critico/i`;
        } else if (data.problemi > 0) {
            statoClass = 'warning';
            statoIcon = 'exclamation-circle';
            statoText = `${data.problemi} problema/i noto/i`;
        }
        
        // Crea card informativa dettagliata
        const infoHtml = `
            <div class="card border-start border-primary border-3 mb-4" id="selected-product-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h6 class="card-title text-primary mb-2">
                                <i class="bi bi-box-seam me-2"></i>
                                Prodotto Selezionato
                            </h6>
                            <h5 class="mb-2">${data.nome}</h5>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-secondary">${data.categoria}</span>
                                ${data.modello ? `<span class="badge bg-light text-dark">Modello: ${data.modello}</span>` : ''}
                                ${data.codice ? `<span class="badge bg-light text-dark">Codice: ${data.codice}</span>` : ''}
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <div class="alert alert-${statoClass} py-2 mb-0">
                                <i class="bi bi-${statoIcon} me-1"></i>
                                <small><strong>${statoText}</strong></small>
                            </div>
                        </div>
                    </div>
                    
                    ${data.problemi > 0 ? `
                        <hr class="my-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            <small class="text-muted">
                                <strong>Suggerimento:</strong> Questo prodotto ha già problemi noti. 
                                La tua nuova soluzione può aiutare a risolvere un problema non ancora coperto 
                                o migliorare soluzioni esistenti.
                            </small>
                        </div>
                    ` : ''}
                    
                    <div class="mt-3">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ url('prodotti-completi') }}/${data.id}" class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-eye me-1"></i>Vedi Dettagli
        </a>
                            ${data.problemi > 0 ? `
                                <a href="{{ url('prodotti') }}/${data.id}/malfunzionamenti" class="btn btn-outline-warning" target="_blank">
                <i class="bi bi-list me-1"></i>Problemi Esistenti
            </a>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Inserisci e anima
        $('#prodotto-info-container').html(infoHtml);
        $('#selected-product-info').hide().slideDown(400);
        
        // Focus automatico sul campo titolo dopo la selezione
        setTimeout(() => {
            $('#titolo').focus();
        }, 500);
    }
    
    // Funzione per nascondere info prodotto
    function hideDetailedProdottoInfo() {
        $('#selected-product-info').slideUp(300, function() {
            $(this).remove();
        });
    }
    
    console.log('✅ Gestione prodotti assegnati inizializzata');
});