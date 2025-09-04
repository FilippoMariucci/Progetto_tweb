
    document.addEventListener('DOMContentLoaded', function() {
    console.log('admin.centri.show caricato');

    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.show') {
        return;
    }

    const pageData = window.PageData || {};
    let selectedProducts = [];

    console.log('🔧 Inizializzazione pagina admin centro assistenza');
    
    // === VARIABILI GLOBALI ===
    // Queste variabili vengono utilizzate in tutto lo script per gestire le operazioni
    const CENTRO_ID = {{ $centro->id }};  // ID del centro corrente
    const BASE_URL = '{{ url("/") }}';    // URL base dell'applicazione
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'); // Token CSRF per sicurezza
    
    // Elementi del DOM per la gestione dell'assegnazione tecnici
    const modalAssegnazione = document.getElementById('modalAssegnaTecnico');
    const selectTecnico = document.getElementById('tecnico_id');
    const btnAssegnaTecnico = document.getElementById('btnAssegnaTecnico');
    const formAssegnazione = document.getElementById('formAssegnaTecnico');
    
    // === INIZIALIZZAZIONE EVENT LISTENERS ===
    // Configura gli eventi per i vari elementi della pagina
    
    // Event listener per apertura modal assegnazione
    if (modalAssegnazione) {
        modalAssegnazione.addEventListener('shown.bs.modal', caricaTecniciDisponibili);
    }
    
    // Event listener per invio form assegnazione
    if (formAssegnazione) {
        formAssegnazione.addEventListener('submit', gestisciAssegnazioneTecnico);
    }
    
    // === GESTIONE RIMOZIONE TECNICI ===
    // Event listener per i form di rimozione tecnici
    document.querySelectorAll('.rimuovi-tecnico-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Ferma l'invio del form
            
            const tecnicoNome = this.getAttribute('data-tecnico-nome');
            const confermaMsg = `Sei sicuro di voler rimuovere "${tecnicoNome}" da questo centro?\n\n` +
                               `Il tecnico rimarrà nel sistema ma non sarà più assegnato a questo centro.`;
            
            // Mostra conferma personalizzata
            if (confirm(confermaMsg)) {
                console.log('Rimozione confermata per tecnico:', tecnicoNome);
                
                // Disabilita il pulsante per evitare doppi click
                const btn = this.querySelector('button');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                
                // Invia il form
                this.submit();
            } else {
                console.log('Rimozione annullata per tecnico:', tecnicoNome);
            }
        });
    });
    
    // === FUNZIONE COPIA IN CLIPBOARD ===
    // Questa funzione permette di copiare testo (ID centro, telefono, email) negli appunti
    window.copiaInClipboard = function(testo) {
        // Usa l'API moderna del browser per copiare negli appunti
        navigator.clipboard.writeText(testo).then(function() {
            // Mostra conferma di successo
            mostraNotifica('Copiato: ' + testo, 'success');
        }).catch(function(err) {
            // Gestisce errori di copia (es. browser non supportato)
            console.error('Errore copia clipboard:', err);
            mostraNotifica('Errore nella copia', 'danger');
        });
    };
    
    // === FUNZIONE APERTURA GOOGLE MAPS ===
    // Funzione globale per aprire Google Maps con l'indirizzo del centro
    window.apriGoogleMaps = function() {
        // Costruisce l'indirizzo completo del centro per la ricerca
        const indirizzo = encodeURIComponent('{{ $centro->indirizzo }}, {{ $centro->citta }}, {{ $centro->provincia }}');
        const url = `https://www.google.com/maps/search/?api=1&query=${indirizzo}`;
        
        // Apre Google Maps in una nuova finestra
        window.open(url, '_blank');
        console.log('Aperta mappa per:', '{{ $centro->nome }}');
    };
    
    /**
     * Carica tecnici disponibili quando si apre il modal di assegnazione
     * Questa funzione viene chiamata ogni volta che si apre il modal per assegnare un tecnico
     */
    function caricaTecniciDisponibili() {
        console.log('Caricamento tecnici disponibili per centro ID:', CENTRO_ID);
        
        // Reset della select e disabilitazione durante caricamento
        selectTecnico.innerHTML = '<option value="">Caricamento tecnici...</option>';
        selectTecnico.disabled = true;
        btnAssegnaTecnico.disabled = true;
        
        // Costruisce l'URL dell'API per ottenere i tecnici disponibili
        const apiUrl = `${BASE_URL}/api/admin/centri/${CENTRO_ID}/tecnici-disponibili`;
        
        // Chiamata AJAX all'API per ottenere la lista tecnici
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => {
            // Controlla se la risposta è valida
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dati tecnici ricevuti:', data);
            
            if (data.success) {
                // Popola la select con i tecnici disponibili
                popolaSelectTecnici(data.tecnici || []);
            } else {
                throw new Error(data.message || 'Errore nel caricamento tecnici');
            }
        })
        .catch(error => {
            console.error('Errore caricamento tecnici:', error);
            selectTecnico.innerHTML = '<option value="">Errore nel caricamento</option>';
            mostraNotifica('Errore caricamento tecnici: ' + error.message, 'danger');
        })
        .finally(() => {
            // Riabilita la select alla fine dell'operazione
            selectTecnico.disabled = false;
        });
    }
    
    /**
     * Popola la select con i tecnici disponibili, separando liberi da trasferibili
     * @param {Array} tecnici - Array dei tecnici disponibili dall'API
     */
    function popolaSelectTecnici(tecnici) {
        // Reset della select
        selectTecnico.innerHTML = '<option value="">-- Seleziona un tecnico --</option>';
        
        if (tecnici.length === 0) {
            selectTecnico.innerHTML += '<option value="">Nessun tecnico disponibile</option>';
            return;
        }
        
        console.log(`Processando ${tecnici.length} tecnici disponibili`);
        
        // Separa tecnici liberi da quelli già assegnati ad altri centri
        const tecniciLiberi = tecnici.filter(t => t.centro_attuale?.status === 'unassigned');
        const tecniciAssegnati = tecnici.filter(t => t.centro_attuale?.status === 'assigned');
        
        // Aggiungi gruppo per tecnici liberi (non assegnati)
        if (tecniciLiberi.length > 0) {
            const gruppo = document.createElement('optgroup');
            gruppo.label = `Tecnici Disponibili (${tecniciLiberi.length})`;
            
            tecniciLiberi.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} - ${tecnico.specializzazione || 'N/A'}`, 
                    tecnico.id
                );
                gruppo.appendChild(option);
            });
            selectTecnico.appendChild(gruppo);
        }
        
        // Aggiungi gruppo per tecnici da trasferire (assegnati ad altri centri)
        if (tecniciAssegnati.length > 0) {
            const gruppo = document.createElement('optgroup');
            gruppo.label = `Trasferimento da Altri Centri (${tecniciAssegnati.length})`;
            
            tecniciAssegnati.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} (da: ${tecnico.centro_attuale.nome})`, 
                    tecnico.id
                );
                // Salva informazioni del centro attuale come attributo
                option.setAttribute('data-centro-attuale', tecnico.centro_attuale.nome);
                gruppo.appendChild(option);
            });
            selectTecnico.appendChild(gruppo);
        }
        
        // Abilita il pulsante di assegnazione
        btnAssegnaTecnico.disabled = false;
        
        // Aggiungi event listener per mostrare info sui trasferimenti
        selectTecnico.addEventListener('change', mostraInfoTrasferimento);
    }
    
    /**
     * Mostra informazioni sui trasferimenti quando si seleziona un tecnico già assegnato
     */
    function mostraInfoTrasferimento() {
        const opzioneSelezionata = selectTecnico.options[selectTecnico.selectedIndex];
        const centroAttuale = opzioneSelezionata?.getAttribute('data-centro-attuale');
        
        // Rimuovi eventuali info precedenti
        const infoEsistente = document.getElementById('infoTrasferimento');
        if (infoEsistente) {
            infoEsistente.remove();
        }
        
        // Se è un trasferimento, mostra avviso informativo
        if (centroAttuale && selectTecnico.value) {
            const infoDiv = document.createElement('div');
            infoDiv.id = 'infoTrasferimento';
            infoDiv.className = 'alert alert-warning mt-2';
            infoDiv.innerHTML = `
                <i class="bi bi-arrow-right-circle me-2"></i>
                <strong>Trasferimento:</strong> Il tecnico sarà automaticamente rimosso da "${centroAttuale}"
            `;
            
            // Inserisce l'avviso dopo la select
            selectTecnico.parentNode.appendChild(infoDiv);
        }
    }
    
    /**
     * Gestisce l'invio del form di assegnazione tecnico
     * Controlla validazioni, conferme e invia la richiesta al server
     * @param {Event} e - Evento submit del form
     */
    function gestisciAssegnazioneTecnico(e) {
        e.preventDefault(); // Previene invio form tradizionale
        
        const tecnicoId = selectTecnico.value;
        if (!tecnicoId) {
            mostraNotifica('Seleziona un tecnico da assegnare', 'warning');
            return;
        }
        
        // Ottiene informazioni sul tecnico selezionato
        const opzioneSelezionata = selectTecnico.options[selectTecnico.selectedIndex];
        const centroAttuale = opzioneSelezionata.getAttribute('data-centro-attuale');
        const nomeTecnico = opzioneSelezionata.text.split(' - ')[0].split(' (')[0];
        
        // Se è un trasferimento, chiede conferma esplicita
        if (centroAttuale) {
            const confermaMsg = `TRASFERIMENTO TECNICO\n\n` +
                               `Tecnico: ${nomeTecnico}\n` +
                               `Da: ${centroAttuale}\n` +
                               `A: {{ $centro->nome }}\n\n` +
                               `Il tecnico sarà automaticamente rimosso dal centro precedente.\n\n` +
                               `Confermi il trasferimento?`;
                               
            if (!confirm(confermaMsg)) {
                return; // Annulla operazione se non confermata
            }
        }
        
        // Disabilita pulsante e mostra stato di caricamento
        btnAssegnaTecnico.disabled = true;
        const originalText = btnAssegnaTecnico.innerHTML;
        
        // Cambia testo del pulsante in base al tipo di operazione
        if (centroAttuale) {
            btnAssegnaTecnico.innerHTML = '<i class="bi bi-arrow-right me-1"></i> Trasferimento...';
        } else {
            btnAssegnaTecnico.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Assegnazione...';
        }
        
        // Prepara i dati per l'invio
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', CSRF_TOKEN);
        
        console.log('Invio richiesta assegnazione tecnico:', {
            tecnico_id: tecnicoId,
            centro_id: CENTRO_ID,
            is_transfer: !!centroAttuale
        });
        
        // Invia richiesta AJAX al server
        fetch(formAssegnazione.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Risposta server:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dati ricevuti:', data);
            
            if (data.success) {
                // Determina il tipo di operazione completata
                const tipologiaOperazione = data.is_transfer ? 'trasferito' : 'assegnato';
                let messaggioSuccesso = data.message || `Tecnico ${tipologiaOperazione} con successo`;
                
                // Mostra notifica di successo
                mostraNotifica(messaggioSuccesso, 'success');
                
                // Se è un trasferimento, mostra info aggiuntive
                if (data.is_transfer && data.previous_center) {
                    setTimeout(() => {
                        mostraNotifica(
                            `Il tecnico è stato automaticamente rimosso da "${data.previous_center}"`, 
                            'info'
                        );
                    }, 1000);
                }
                
                console.log(`Tecnico ${tipologiaOperazione} con successo`);
                
                // Chiudi modal e ricarica pagina per aggiornare i dati
                setTimeout(() => {
                    const modalInstance = bootstrap.Modal.getInstance(modalAssegnazione);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    location.reload(); // Ricarica per mostrare il tecnico assegnato
                }, 2000);
                
            } else {
                throw new Error(data.message || 'Errore nell\'operazione di assegnazione');
            }
        })
        .catch(error => {
            console.error('Errore operazione:', error);
            
            // Messaggi di errore specifici basati sul tipo di errore
            let messaggioErrore = 'Errore nell\'operazione';
            
            if (error.message.includes('già assegnato a questo centro')) {
                messaggioErrore = 'Il tecnico è già assegnato a questo centro';
            } else if (error.message.includes('403') || error.message.includes('Forbidden')) {
                messaggioErrore = 'Non hai i permessi per questa operazione';
            } else if (error.message.includes('422') || error.message.includes('Unprocessable')) {
                messaggioErrore = 'Dati non validi o tecnico non disponibile';
            } else if (error.message.includes('500') || error.message.includes('Internal Server Error')) {
                messaggioErrore = 'Errore del server. Riprova tra qualche momento';
            } else if (error.message.includes('404') || error.message.includes('Not Found')) {
                messaggioErrore = 'Tecnico o centro non trovato';
            }
            
            mostraNotifica(messaggioErrore + ': ' + error.message, 'danger');
        })
        .finally(() => {
            // Ripristina sempre il pulsante alla fine dell'operazione
            btnAssegnaTecnico.disabled = false;
            btnAssegnaTecnico.innerHTML = originalText;
        });
    }
    
    /**
     * Mostra notifica temporanea con animazione
     * @param {string} messaggio - Testo da mostrare
     * @param {string} tipo - Tipo di alert (success, danger, warning, info)
     */
    function mostraNotifica(messaggio, tipo = 'info') {
        // Mappa tipi di alert ai CSS Bootstrap
        const tipiAlert = {
            'success': 'alert-success',
            'danger': 'alert-danger', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        // Mappa icone per ogni tipo
        const icone = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-circle', 
            'info': 'bi-info-circle'
        };
        
        // Crea elemento notifica
        const notifica = document.createElement('div');
        notifica.className = `alert ${tipiAlert[tipo]} alert-dismissible fade show notifica-temp`;
        notifica.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        notifica.innerHTML = `
            <i class="bi ${icone[tipo]} me-2"></i>
            ${messaggio}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        `;
        
        // Aggiungi al DOM
        document.body.appendChild(notifica);
        
        // Log per debugging
        console.log(`Notifica ${tipo}:`, messaggio);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            if (notifica && notifica.parentNode) {
                notifica.remove();
            }
        }, 5000);
    }


console.log('JavaScript admin centro assistenza caricato con stile migliorato - Versione completa');
    }); 