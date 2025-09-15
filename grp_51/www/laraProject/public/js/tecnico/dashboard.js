
$(document).ready(function() {
    console.log('tecnico.dashboard caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'tecnico.dashboard') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...

     // === CONFIGURAZIONE GLOBALE ===
    console.log('Dashboard Tecnico caricata per: {{ auth()->user()->nome_completo }}');
    
    // URLs corretti per le API (basati sulle route del progetto)
    const API_URLS = {
        // Statistiche dashboard in tempo reale
        stats_dashboard: '{{ route("api.stats.dashboard") }}',
        
        // Endpoint per segnalazione malfunzionamenti
        segnala_base_url: '{{ url("/api/malfunzionamenti") }}'
    };
    
    // Token CSRF per sicurezza nelle richieste AJAX
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    // Configurazione AJAX globale per includere sempre il CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    // === DEBUG: Verifica presenza elementi ===
    console.log('Elemento searchProdotti trovato:', $('#searchProdotti').length);
    console.log('Elemento searchMalfunzionamenti trovato:', $('#searchMalfunzionamenti').length);
    console.log('Form prodotti trovato:', $('#searchProdotti').closest('form').length);
    console.log('Form malfunzionamenti trovato:', $('#searchMalfunzionamenti').closest('form').length);

    // === GESTIONE RICERCA PRODOTTI ===
    // Gestione form ricerca prodotti - intercetta SOLO per validazione
    $('#searchProdotti').closest('form').on('submit', function(e) {
        const form = $(this);
        const input = form.find('input[name="search"]');
        const query = input.val().trim();
        
        console.log('Form ricerca prodotti submitted, query:', query);
        
        // Validazione lunghezza minima
        if (query.length < 2) {
            e.preventDefault();
            showAlert('Inserisci almeno 2 caratteri per la ricerca', 'warning');
            input.focus();
            return false;
        }
        
        // Validazione caratteri non consentiti
        const validazione = validaTermineRicerca(query);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // Mostra indicatore di caricamento sul bottone
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input');
        
        console.log('Ricerca prodotti validata, form si submitterà normalmente');
        // Lascio che il form si submitti normalmente
        return true;
    });
    
    // === GESTIONE RICERCA MALFUNZIONAMENTI ===
    // Gestione form ricerca malfunzionamenti - intercetta SOLO per validazione
    $('#searchMalfunzionamenti').closest('form').on('submit', function(e) {
        const form = $(this);
        const input = form.find('input[name="q"]');
        const query = input.val().trim();
        
        console.log('Form ricerca malfunzionamenti submitted, query:', query);
        
        // Validazione lunghezza minima
        if (query.length < 2) {
            e.preventDefault();
            showAlert('Inserisci almeno 2 caratteri per cercare malfunzionamenti', 'warning');
            input.focus();
            return false;
        }
        
        // Validazione caratteri non consentiti
        const validazione = validaTermineRicerca(query, 2);
        if (!validazione.valido) {
            e.preventDefault();
            showAlert(validazione.messaggio, 'warning');
            input.focus();
            return false;
        }
        
        // Mostra indicatore di caricamento sul bottone
        const $button = form.find('button[type="submit"]');
        $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
        input.addClass('loading-input');
        
        console.log('Ricerca malfunzionamenti validata, form si submitterà normalmente');
        // Lascio che il form si submitti normalmente
        return true;
    });

    // === RICERCA CON ENTER (manteniamo per usabilità) ===
    
    // Gestione ricerca prodotti con ENTER
    $('#searchProdotti').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca prodotti, query:', query);
            
            // Validazione prima del submit
            const validazione = validaTermineRicerca(query);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            // Mostra indicatore di caricamento
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            // Submit manuale del form
            console.log('Ricerca prodotti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // Gestione ricerca malfunzionamenti con ENTER
    $('#searchMalfunzionamenti').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const query = $(this).val().trim();
            
            console.log('Pressione ENTER su ricerca malfunzionamenti, query:', query);
            
            // Validazione prima del submit (2 caratteri minimi per malfunzionamenti)
            const validazione = validaTermineRicerca(query, 2);
            if (!validazione.valido) {
                showAlert(validazione.messaggio, 'warning');
                $(this).focus();
                return false;
            }
            
            // Mostra indicatore di caricamento
            const $button = $(this).closest('form').find('button[type="submit"]');
            $button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Cercando...');
            $(this).addClass('loading-input');
            
            // Submit manuale del form
            console.log('Ricerca malfunzionamenti con ENTER:', query);
            $(this).closest('form').submit();
        }
    });
    
    // === VALIDAZIONE RICERCA ===
    // Funzione helper per validare i termini di ricerca
    function validaTermineRicerca(query, lunghezzaMinima = 2) {
        // Controllo lunghezza minima
        if (!query || query.length < lunghezzaMinima) {
            return {
                valido: false,
                messaggio: `Inserisci almeno ${lunghezzaMinima} caratteri per la ricerca`
            };
        }
        
        // Controlli aggiuntivi per caratteri speciali pericolosi
        if (/[<>]/.test(query)) {
            return {
                valido: false,
                messaggio: 'Caratteri non ammessi nella ricerca: < >'
            };
        }
        
        // Controllo lunghezza massima per evitare query troppo lunghe
        if (query.length > 100) {
            return {
                valido: false,
                messaggio: 'Termine di ricerca troppo lungo (max 100 caratteri)'
            };
        }
        
        return { valido: true, messaggio: '' };
    }
    
    // === TOOLTIP PER ERRORI ===
    function showErrorTooltip(selector, message) {
        const $element = $(selector);
        
        // Rimuovi tooltip esistenti per evitare sovrapposizioni
        $element.tooltip('dispose');
        
        // Aggiungi nuovo tooltip di errore
        $element.tooltip({
            title: message,
            placement: 'bottom',
            trigger: 'manual',
            customClass: 'error-tooltip'
        }).tooltip('show');
        
        // Rimuovi automaticamente dopo 3 secondi
        setTimeout(function() {
            $element.tooltip('dispose');
        }, 3000);
    }
    
    // === GESTIONE EVENTI GENERALI ===
    
    // Focus automatico sull'input di ricerca quando si arriva alla pagina
    setTimeout(function() {
        $('#searchProdotti').focus();
        console.log('Focus automatico su searchProdotti');
    }, 500);
    
    // Suggerimenti visivi per ricerca con wildcard
    $('#searchProdotti, #searchMalfunzionamenti').on('focus', function() {
        $(this).next('.form-text').addClass('text-primary');
        console.log('Focus su input ricerca:', this.id);
    }).on('blur', function() {
        $(this).next('.form-text').removeClass('text-primary');
        console.log('Blur su input ricerca:', this.id);
    });
    
    // Validazione in tempo reale per caratteri non consentiti
    $('#searchProdotti, #searchMalfunzionamenti').on('input', function() {
        const query = $(this).val();
        const hasInvalidChars = /[<>]/.test(query);
        
        if (hasInvalidChars) {
            $(this).addClass('is-invalid');
            showErrorTooltip(this, 'Caratteri non ammessi: < >');
        } else {
            $(this).removeClass('is-invalid');
            $(this).tooltip('dispose');
        }
    });
    
    // === FUNZIONE SEGNALA MALFUNZIONAMENTO ===
    window.segnalaMalfunzionamento = function(malfunzionamentoId) {
        console.log('Segnalazione richiesta per malfunzionamento ID:', malfunzionamentoId);
        
        if (!malfunzionamentoId) {
            showAlert('Errore: ID malfunzionamento non valido', 'danger');
            return;
        }
        
        // Richiedi conferma dall'utente prima di procedere
        if (!confirm('Confermi di aver riscontrato questo problema? Incrementerà il contatore delle segnalazioni.')) {
            console.log('Segnalazione annullata dall\'utente');
            return;
        }
        
        // Trova il pulsante e disabilitalo temporaneamente per evitare doppi click
        const $button = $(`[onclick="segnalaMalfunzionamento(${malfunzionamentoId})"]`);
        if (!$button.length) {
            console.error('Pulsante segnalazione non trovato per ID:', malfunzionamentoId);
            showAlert('Errore: pulsante non trovato', 'danger');
            return;
        }
        
        const originalContent = $button.html();
        $button.prop('disabled', true)
               .removeClass('btn-outline-warning')
               .addClass('btn-secondary')
               .html('<span class="spinner-border spinner-border-sm me-1"></span>Segnalando...');
        
        // Esegui la chiamata AJAX per registrare la segnalazione
        $.ajax({
            url: `{{ url('/api/malfunzionamenti') }}/${malfunzionamentoId}/segnala`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            data: JSON.stringify({}), // Body vuoto ma JSON valido per Laravel
            timeout: 10000, // Timeout di 10 secondi
            success: function(response) {
                console.log('Risposta segnalazione:', response);
                
                if (response.success) {
                    // Mostra messaggio di successo
                    showAlert('Segnalazione registrata con successo!', 'success');
                    
                    // Aggiorna il contatore visibile nella tabella
                    const $counter = $(`#count-${malfunzionamentoId}`);
                    if ($counter.length) {
                        const nuovoCount = response.nuovo_count || (parseInt($counter.text()) + 1);
                        $counter.text(nuovoCount);
                        
                        // Aggiungi animazione di feedback per mostrare l'aggiornamento
                        $counter.addClass('badge-updated');
                        setTimeout(() => {
                            $counter.removeClass('badge-updated');
                        }, 2000);
                    }
                    
                    // Cambia il pulsante per mostrare successo
                    $button.removeClass('btn-secondary btn-outline-warning')
                           .addClass('btn-success')
                           .html('<i class="bi bi-check-circle me-1"></i>Segnalato')
                           .prop('disabled', true);
                    
                    console.log(`Segnalazione registrata per malfunzionamento ${malfunzionamentoId}. Nuovo conteggio: ${response.nuovo_count}`);
                } else {
                    throw new Error(response.message || 'Errore nella risposta del server');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore segnalazione AJAX:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMsg = 'Errore nella segnalazione del malfunzionamento';
                
                // Gestione errori specifici
                if (xhr.status === 0) {
                    errorMsg = 'Errore di connessione. Controlla la rete.';
                } else if (xhr.status === 403) {
                    errorMsg = 'Non hai i permessi per questa azione';
                } else if (xhr.status === 404) {
                    errorMsg = 'Malfunzionamento non trovato';
                } else if (xhr.status === 429) {
                    errorMsg = 'Troppi tentativi. Riprova tra qualche minuto';
                } else if (xhr.status === 500) {
                    errorMsg = 'Errore interno del server';
                } else {
                    // Prova a estrarre messaggio di errore dalla risposta
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        // Se la risposta non è JSON valida, usa il messaggio di default
                        console.warn('Risposta non JSON:', xhr.responseText);
                    }
                }
                
                showAlert(errorMsg, 'danger');
                
                // Ripristina il pulsante originale
                $button.removeClass('btn-secondary')
                       .addClass('btn-outline-warning')
                       .prop('disabled', false)
                       .html(originalContent);
            }
        });
    };
    
    // === FUNZIONE MOSTRA ALERT ===
    function showAlert(message, type = 'info') {
        const alertId = 'alert-' + Date.now();
        const icons = {
            success: 'bi-check-circle',
            danger: 'bi-exclamation-triangle',
            warning: 'bi-exclamation-circle',
            info: 'bi-info-circle'
        };
        
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-sm" 
                 role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; min-width: 300px;">
                <i class="${icons[type] || icons.info} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Inserisci l'alert nel body per mostrarlo come toast
        $('body').append(alertHtml);
        
        // Rimuovi automaticamente dopo 5 secondi
        setTimeout(function() {
            const $alert = $(`#${alertId}`);
            if ($alert.length) {
                $alert.fadeOut(500, function() {
                    $(this).remove();
                });
            }
        }, 5000);
    }
    
    // === AGGIORNAMENTO STATISTICHE PERIODICO ===
    function aggiornaStatistiche() {
        console.log('Aggiornamento statistiche...');
        
        $.ajax({
            url: API_URLS.stats_dashboard,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Statistiche aggiornate:', response.data);
                    
                    const stats = response.data;
                    
                    // Funzione helper per aggiornare statistiche con animazione
                    function updateStat(selector, newValue) {
                        const $element = $(selector);
                        if ($element.length && $element.text() !== newValue.toString()) {
                            // Aggiungi classe per animazione di aggiornamento
                            $element.addClass('updating');
                            setTimeout(() => {
                                $element.text(newValue).removeClass('updating');
                            }, 300);
                        }
                    }
                    
                    // Aggiorna le statistiche se gli elementi esistono nel DOM
                    // Usa selettori più specifici basati sul contenuto delle card
                    $('h5:contains("' + (stats.total_prodotti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-box-seam').length) {
                            updateStat(this, stats.total_prodotti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_malfunzionamenti || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-tools').length) {
                            updateStat(this, stats.total_malfunzionamenti);
                        }
                    });
                    
                    $('h5:contains("' + (stats.malfunzionamenti_critici || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-exclamation-triangle').length) {
                            updateStat(this, stats.malfunzionamenti_critici);
                        }
                    });
                    
                    $('h5:contains("' + (stats.total_centri || 0) + '")').each(function() {
                        if ($(this).closest('.card-body').find('i.bi-geo-alt').length) {
                            updateStat(this, stats.total_centri);
                        }
                    });
                }
            },
            error: function(xhr) {
                // Errore silenzioso per non disturbare l'esperienza utente
                console.warn('Aggiornamento statistiche fallito:', xhr.status);
            }
        });
    }
    
    // Avvia aggiornamento automatico statistiche ogni 10 minuti
    const statsUpdateInterval = setInterval(aggiornaStatistiche, 10 * 60 * 1000);
    
    // === SHORTCUTS DA TASTIERA ===
    $(document).on('keydown', function(e) {
        // Solo se non siamo già dentro un input per evitare conflitti
        if (!$(e.target).is('input, textarea')) {
            
            // Ctrl + F = Focus su ricerca prodotti
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                $('#searchProdotti').focus().select();
                console.log('Shortcut Ctrl+F: Focus su ricerca prodotti');
            }
            
            // Ctrl + M = Focus su ricerca malfunzionamenti  
            if (e.ctrlKey && e.key === 'm') {
                e.preventDefault();
                $('#searchMalfunzionamenti').focus().select();
                console.log('Shortcut Ctrl+M: Focus su ricerca malfunzionamenti');
            }
            
            // Ctrl + C = Vai al catalogo completo
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                window.location.href = '{{ route("prodotti.completo.index") }}';
            }
            
            // Ctrl + H = Vai alla dashboard principale
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = '{{ route("dashboard") }}';
            }
            
            // Ctrl + S = Focus su segnalazione rapida (se presente)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const $firstSegnalaBtn = $('.segnala-btn').first();
                if ($firstSegnalaBtn.length) {
                    $firstSegnalaBtn.focus();
                    console.log('Shortcut Ctrl+S: Focus su primo pulsante segnalazione');
                }
            }
        }
    });
    
    // === INIZIALIZZAZIONE TOOLTIP E ANIMAZIONI ===
    
    // Inizializza tooltip Bootstrap per tutti gli elementi con data-bs-toggle="tooltip"
    $('[data-bs-toggle="tooltip"]').tooltip();
    console.log('Tooltip inizializzati per', $('[data-bs-toggle="tooltip"]').length, 'elementi');
    
    // Effetto hover migliorato per le card con animazione smooth
    $('.card.card-custom').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // Animazione click per pulsanti
    $('.btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('btn-clicked');
        setTimeout(() => {
            $btn.removeClass('btn-clicked');
        }, 200);
    });
    
    // === FUNZIONI DI DEBUG E TESTING ===
    
    // Test delle connessioni API per verificare che tutto funzioni
    function testConnessioniAPI() {
        console.log('🧪 Test delle connessioni API...');
        
        // Test API statistiche
        $.get(API_URLS.stats_dashboard)
            .done(() => console.log('✅ API Statistiche: OK'))
            .fail((xhr) => console.log('❌ API Statistiche: ERRORE', xhr.status));
    }
    
    // === GESTIONE RESPONSIVE ===
    
    // Adatta layout per dispositivi mobili
    function handleResponsiveLayout() {
        const isMobile = window.innerWidth < 768;
        console.log('Layout responsive:', isMobile ? 'mobile' : 'desktop');
        
        if (isMobile) {
            // Nasconde alcuni elementi non essenziali su mobile
            $('.form-text').addClass('d-none');
            
            // Riduce il padding delle card
            $('.card-body').addClass('p-2');
            
            // Semplifica i tooltip
            $('[data-bs-toggle="tooltip"]').tooltip('disable');
        } else {
            // Ripristina layout desktop
            $('.form-text').removeClass('d-none');
            $('.card-body').removeClass('p-2');
            $('[data-bs-toggle="tooltip"]').tooltip('enable');
        }
    }
    
    // Chiama al caricamento e al resize
    handleResponsiveLayout();
    $(window).on('resize', handleResponsiveLayout);
    
    // === GESTIONE ERRORI GLOBALI ===
    
    // Intercetta errori AJAX globali per logging
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        console.error('Errore AJAX Globale:', {
            url: ajaxSettings.url,
            status: jqXHR.status,
            statusText: jqXHR.statusText,
            responseText: jqXHR.responseText,
            error: thrownError
        });
        
        // Mostra errore solo per chiamate importanti (non statistiche)
        if (!ajaxSettings.url.includes('/stats/')) {
            showAlert('Si è verificato un errore di connessione. Riprova tra qualche momento.', 'danger');
        }
    });
    
    // === CLEANUP E FINALIZZAZIONE ===
    
    // Pulizia quando si lascia la pagina per evitare memory leaks
    $(window).on('beforeunload', function() {
        if (typeof statsUpdateInterval !== 'undefined') {
            clearInterval(statsUpdateInterval);
        }
        
        // Rimuovi tutti i tooltip attivi
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
    });
    
    // === INIZIALIZZAZIONE COMPLETATA ===
    console.log('✅ Dashboard Tecnico inizializzata completamente');
    console.log('🔧 URLs API configurati:', API_URLS);
    console.log('🚀 Funzioni disponibili:', {
        'segnalaMalfunzionamento()': 'Segnala un problema riscontrato',
        'testConnessioniAPI()': 'Test delle connessioni API',
        'aggiornaStatistiche()': 'Forza aggiornamento statistiche',
        'validaTermineRicerca()': 'Valida termine di ricerca'
    });
    
    // Esponi funzioni per debugging nella console del browser
    window.dashboardTecnico = {
        testAPI: testConnessioniAPI,
        updateStats: aggiornaStatistiche,
        showAlert: showAlert,
        validateSearch: validaTermineRicerca,
        urls: API_URLS,
        version: '2.1.0'
    };
    
    // Test automatico delle API all'avvio (solo in ambiente di sviluppo)
    // Esegui test delle API solo se in ambiente di sviluppo (modifica secondo necessità)
    // Ad esempio, puoi impostare una variabile globale in un tag <script> nel template Blade:
    // window.isLocalEnv = {{ app()->environment('local') ? 'true' : 'false' }};
    if (window.isLocalEnv) {
        setTimeout(testConnessioniAPI, 2000);
    }
    
    // Messaggio di benvenuto personalizzato (solo al primo accesso giornaliero)
    const oggi = new Date().toDateString();
    const ultimoAccesso = localStorage.getItem('ultimo_accesso_dashboard_tecnico');
    
    if (ultimoAccesso !== oggi) {
        setTimeout(function() {
            showAlert('Benvenuto nella Dashboard Tecnico! Usa Ctrl+F per cercare prodotti e Ctrl+M per malfunzionamenti.', 'info');
            localStorage.setItem('ultimo_accesso_dashboard_tecnico', oggi);
        }, 1500);
    }
    
    // Logging delle metriche di utilizzo per analytics
    const sessionStart = Date.now();
    window.addEventListener('beforeunload', function() {
        const sessionDuration = Date.now() - sessionStart;
        console.log('Sessione Dashboard Tecnico:', {
            durata: Math.round(sessionDuration / 1000) + ' secondi',
            utente: '{{ auth()->user()->username }}',
            timestamp: new Date().toISOString()
        });
    });
    
    // === TEST FINALE FUNZIONALITÀ ===
    // Dopo 1 secondo dall'inizializzazione, testa le funzionalità principali
    setTimeout(function() {
        console.log('=== TEST FUNZIONALITÀ DASHBOARD ===');
        
        // Test presenza elementi chiave
        const elementi = {
            'Input ricerca prodotti': $('#searchProdotti').length,
            'Input ricerca malfunzionamenti': $('#searchMalfunzionamenti').length,
            'Bottone ricerca prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').length,
            'Bottone ricerca malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').length,
            'Pulsanti segnalazione': $('.segnala-btn').length,
            'Card statistiche': $('.card.border-0.shadow-sm').length
        };
        
        console.table(elementi);
        
        // Verifica se gli event handler sono stati attaccati
        const eventiAttaccati = {
            'Click bottone prodotti': $('#searchProdotti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Click bottone malfunzionamenti': $('#searchMalfunzionamenti').closest('form').find('button[type="submit"]').data('events') ? 'SI' : 'NO',
            'Keypress prodotti': $('#searchProdotti').data('events') ? 'SI' : 'NO',
            'Keypress malfunzionamenti': $('#searchMalfunzionamenti').data('events') ? 'SI' : 'NO'
        };
        
        console.table(eventiAttaccati);
        
        console.log('=== FINE TEST FUNZIONALITÀ ===');
    }, 1000);
});