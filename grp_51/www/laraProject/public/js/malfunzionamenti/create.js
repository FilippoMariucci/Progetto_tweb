/**
 * FILE: create.js
 * LINGUAGGIO: JavaScript (ES6+) con jQuery
 * SCOPO: Gestione dell'interfaccia per la creazione di nuove soluzioni nella dashboard dello staff
 * FRAMEWORK UTILIZZATI: Laravel (backend), jQuery, Bootstrap 5
 * AUTORE: Sistema di assistenza tecnica
 */

// === LOG DI CARICAMENTO ===
/**
 * Console.log per debug - indica che il file è stato caricato correttamente
 * Utile durante lo sviluppo per verificare che il JavaScript sia stato incluso
 */
console.log('staff.create.nuova.soluzione caricato');

// === CONTROLLO ROUTE ATTIVA ===
/**
 * Verifica che il JavaScript si esegua solo sulla route corretta
 * window.LaravelApp?.route: variabile globale impostata dal backend Laravel
 * L'operatore ?. (optional chaining) evita errori se LaravelApp non esiste
 * Se la route non corrisponde, il JavaScript non viene eseguito (return)
 */
const currentRoute = window.LaravelApp?.route || '';
if (currentRoute !== 'staff.create.nuova.soluzione') {
    return; // Esce dalla funzione se non siamo nella route corretta
}

// === VARIABILI GLOBALI ===
/**
 * pageData: dati della pagina passati dal backend Laravel tramite @json() in Blade
 * selectedProducts: array per tenere traccia dei prodotti selezionati dall'utente
 */
const pageData = window.PageData || {};
let selectedProducts = [];

// === GESTIONE FORM QUANDO NON CI SONO PRODOTTI ASSEGNATI ===
/**
 * Event listener che si attiva quando il DOM è completamente caricato
 * DOMContentLoaded: evento del browser che si scatena quando l'HTML è stato completamente caricato
 */
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * SCOPO: Disabilita tutti i campi del form se lo staff non ha prodotti assegnati
     * Questo previene errori e confusione per l'utente
     */
    
    // Trova il form principale tramite ID HTML
    const form = document.getElementById('formNuovaSoluzione');
    
    if (form) { // Verifica che il form esista nel DOM
        
        /**
         * querySelectorAll: metodo DOM che seleziona tutti gli elementi che corrispondono al selettore CSS
         * Selettore: input:not([type="button"]), textarea, select, button[type="submit"]
         * - input:not([type="button"]): tutti gli input TRANNE quelli di tipo button
         * - textarea: aree di testo
         * - select: menu a tendina
         * - button[type="submit"]: bottoni di invio form
         */
        const inputs = form.querySelectorAll('input:not([type="button"]), textarea, select, button[type="submit"]');
        
        /**
         * forEach: metodo JavaScript per iterare su ogni elemento dell'array
         * Per ogni input trovato, esegue la funzione di callback
         */
        inputs.forEach(input => {
            
            // Disabilita l'input (lo rende non interattivo)
            input.disabled = true;
            
            /**
             * tagName.toLowerCase(): proprietà DOM che restituisce il nome del tag HTML in minuscolo
             * Verifica il tipo di elemento per personalizzare il comportamento
             */
            if (input.tagName.toLowerCase() === 'select') {
                // Per i menu a tendina, svuota le opzioni e mostra messaggio informativo
                input.innerHTML = '<option value="">Nessun prodotto assegnato</option>';
            } else if (input.tagName.toLowerCase() !== 'button') {
                // Per tutti gli altri campi (tranne i button), imposta un placeholder informativo
                input.placeholder = 'Richiedere assegnazione prodotti all\'amministratore';
            }
        });
        
        /**
         * CREAZIONE DINAMICA DI UN ALERT INFORMATIVO
         * createElement: metodo DOM per creare un nuovo elemento HTML
         * className: proprietà per impostare le classi CSS
         * innerHTML: proprietà per impostare il contenuto HTML interno
         */
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info mt-3'; // Classi Bootstrap per styling
        alertDiv.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            <strong>Modulo disabilitato:</strong> 
            Senza prodotti assegnati non è possibile creare nuove soluzioni.
        `;
        
        /**
         * appendChild: metodo DOM per aggiungere un elemento figlio
         * Aggiunge l'alert alla fine del form
         */
        form.appendChild(alertDiv);
    }
});

// === GESTIONE SELEZIONE PRODOTTO CON JQUERY ===
/**
 * CONDIZIONE BLADE: @if(isset($isNuovaSoluzione) && $isNuovaSoluzione && isset($prodotti) && $prodotti->count() > 0)
 * Questo blocco si esegue solo se:
 * - $isNuovaSoluzione è definita e true
 * - $prodotti è definita e contiene almeno 1 prodotto
 * È una condizione Blade (template engine di Laravel) che genera JavaScript condizionalmente
 */

/**
 * $(document).ready(): metodo jQuery equivalente a DOMContentLoaded
 * Si esegue quando il DOM è pronto per la manipolazione JavaScript
 */
$(document).ready(function() {
    
    /**
     * EVENT HANDLER PER LA SELEZIONE DEL PRODOTTO
     * $('#prodotto_id'): selettore jQuery per l'elemento con ID "prodotto_id"
     * .on('change', function): attacca un event listener per l'evento "change"
     * L'evento "change" si scatena quando l'utente cambia selezione in un select
     */
    $('#prodotto_id').on('change', function() {
        
        /**
         * $(this): riferimento jQuery all'elemento che ha scatenato l'evento (il select)
         * .find('option:selected'): trova l'opzione attualmente selezionata
         * .val(): metodo jQuery per ottenere il valore dell'elemento selezionato
         */
        const selectedOption = $(this).find('option:selected');
        const prodottoId = selectedOption.val();
        
        // Verifica se è stato selezionato un prodotto valido (non vuoto)
        if (prodottoId) {
            
            /**
             * ESTRAZIONE DATI DAL PRODOTTO SELEZIONATO
             * .text(): metodo jQuery per ottenere il testo dell'elemento
             * .split(' (')[0]: divide la stringa al primo " (" e prende la prima parte
             * .data(): metodo jQuery per leggere attributi data-* dell'HTML
             */
            const prodottoData = {
                id: prodottoId,
                nome: selectedOption.text().split(' (')[0], // Rimuove info problemi tra parentesi
                categoria: selectedOption.data('categoria'), // Legge data-categoria
                modello: selectedOption.data('modello'),     // Legge data-modello
                codice: selectedOption.data('codice'),       // Legge data-codice
                problemi: selectedOption.data('problemi') || 0,  // Legge data-problemi, default 0
                critici: selectedOption.data('critici') || 0     // Legge data-critici, default 0
            };
            
            // Chiama funzione per mostrare informazioni dettagliate
            showDetailedProdottoInfo(prodottoData);
            
            /**
             * ANALYTICS/DEBUG
             * console.log: stampa informazioni nella console del browser per debug
             */
            console.log('Prodotto assegnato selezionato:', prodottoData);
            
        } else {
            // Se nessun prodotto è selezionato, nascondi le informazioni
            hideDetailedProdottoInfo();
        }
    });
    
    /**
     * FUNZIONE PER MOSTRARE INFORMAZIONI DETTAGLIATE DEL PRODOTTO
     * @param {Object} data - Oggetto contenente i dati del prodotto
     * Questa funzione crea dinamicamente una card HTML con le informazioni del prodotto
     */
    function showDetailedProdottoInfo(data) {
        
        /**
         * .empty(): metodo jQuery per rimuovere tutto il contenuto di un elemento
         * Rimuove eventuali informazioni di prodotti precedentemente selezionati
         */
        $('#prodotto-info-container').empty();
        
        /**
         * DETERMINAZIONE STATO DEL PRODOTTO
         * Logica condizionale per determinare l'aspetto visuale basato sui problemi
         */
        let statoClass = 'success'; // CSS class Bootstrap per colore verde
        let statoIcon = 'check-circle'; // Icona Bootstrap Icons
        let statoText = 'Nessun problema noto'; // Testo da mostrare
        
        /**
         * LOGICA CONDIZIONALE PER LO STATO
         * Controlla prima i problemi critici, poi quelli normali
         */
        if (data.critici > 0) {
            statoClass = 'danger';  // Rosso per problemi critici
            statoIcon = 'exclamation-triangle';
            statoText = `${data.critici} problema/i critico/i`;
        } else if (data.problemi > 0) {
            statoClass = 'warning'; // Giallo per problemi normali
            statoIcon = 'exclamation-circle';
            statoText = `${data.problemi} problema/i noto/i`;
        }
        
        /**
         * CREAZIONE HTML DINAMICO CON TEMPLATE LITERALS
         * Utilizza backticks (`) per creare stringhe multi-riga con interpolazione
         * ${variabile}: sintassi per inserire variabili JavaScript nell'HTML
         * Operatore ternario: condizione ? valore_se_vero : valore_se_falso
         */
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
        
        /**
         * INSERIMENTO E ANIMAZIONE DELL'HTML
         * .html(): metodo jQuery per impostare il contenuto HTML
         * .hide(): nasconde l'elemento
         * .slideDown(): animazione jQuery per mostrare l'elemento scivolando dall'alto
         * 400: durata animazione in millisecondi
         */
        $('#prodotto-info-container').html(infoHtml);
        $('#selected-product-info').hide().slideDown(400);
        
        /**
         * FOCUS AUTOMATICO SUL CAMPO TITOLO
         * setTimeout(): funzione JavaScript per eseguire codice dopo un delay
         * $('#titolo').focus(): metodo jQuery per dare focus al campo titolo
         * 500ms di delay per aspettare che l'animazione finisca
         */
        setTimeout(() => {
            $('#titolo').focus();
        }, 500);
    }
    
    /**
     * FUNZIONE PER NASCONDERE INFORMAZIONI DEL PRODOTTO
     * Viene chiamata quando nessun prodotto è selezionato
     */
    function hideDetailedProdottoInfo() {
        /**
         * .slideUp(): animazione jQuery per nascondere l'elemento scivolando verso l'alto
         * 300: durata animazione in millisecondi
         * function(): callback che viene eseguita quando l'animazione finisce
         * $(this).remove(): rimuove completamente l'elemento dal DOM
         */
        $('#selected-product-info').slideUp(300, function() {
            $(this).remove();
        });
    }
    
    /**
     * LOG DI INIZIALIZZAZIONE COMPLETATA
     * Conferma che tutti gli event handler sono stati configurati
     */
    console.log('✅ Gestione prodotti assegnati inizializzata');
});

/**
 * FINE DEL CODICE JAVASCRIPT
 * 
 * RIASSUNTO DELLE TECNOLOGIE UTILIZZATE:
 * - JavaScript ES6+ (const, let, arrow functions, template literals)
 * - jQuery (selettori, event handlers, animazioni, manipolazione DOM)
 * - Bootstrap 5 (classi CSS per styling e layout responsive)
 * - Bootstrap Icons (icone grafiche)
 * - Laravel Blade (template engine per generare JavaScript condizionalmente)
 * - DOM API (getElementById, querySelectorAll, createElement, appendChild)
 * 
 * PATTERN UTILIZZATI:
 * - Event-driven programming (gestione eventi utente)
 * - Conditional rendering (mostra/nascondi elementi basato sui dati)
 * - Progressive enhancement (disabilita campi se non ci sono dati)
 * - User feedback (animazioni e messaggi informativi)
 * - Data attributes (storage di metadati negli elementi HTML)
 */