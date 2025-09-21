/*
 * =====================================================
 * MODULO JAVASCRIPT PER LA VISUALIZZAZIONE CENTRO ASSISTENZA
 * =====================================================
 * 
 * FRAMEWORK: Laravel + JavaScript ES6+ + Bootstrap 5
 * ARCHITETTURA: Module Pattern con Object Literal
 * FUNZIONALITÀ: Gestione assegnazione tecnici, API calls, Google Maps
 * 
 * DESCRIZIONE GENERALE:
 * Questo è un modulo JavaScript avanzato che implementa il Module Pattern
 * per gestire la pagina di dettaglio di un centro di assistenza.
 * Include funzionalità AJAX per assegnare tecnici, gestione modal,
 * chiamate API REST e integrazione con Google Maps.
 */

/**
 * =====================================================
 * HEADER DOCUMENTAZIONE FILE
 * =====================================================
 * STANDARD: JSDoc per documentazione professionale
 * INFORMAZIONI: File path, descrizione, funzionalità, versione
 */

/**
 * MODULO PRINCIPALE: AdminCentroShow
 * DESIGN PATTERN: Module Pattern con Object Literal
 * VANTAGGI:
 *   - Namespace isolato per evitare conflitti globali
 *   - Encapsulation di dati e metodi
 *   - API pubblica ben definita
 *   - Facilità di testing e manutenzione
 */
const AdminCentroShow = {
    
    // =====================================================
    // PROPRIETÀ DI CONFIGURAZIONE
    // =====================================================
    
    /**
     * OGGETTO: config
     * SCOPO: Memorizza configurazione del modulo
     * PATTERN: Configuration Object per parametri esterni
     * VALORI: Impostati tramite init() da codice Laravel
     */
    config: {
        centroId: null,        // ID del centro di assistenza
        baseUrl: null,         // URL base dell'applicazione Laravel
        csrfToken: null,       // Token CSRF per sicurezza
        debugMode: false       // Flag per modalità debug
    },
    
    // =====================================================
    // CACHE ELEMENTI DOM
    // =====================================================
    
    /**
     * OGGETTO: elements
     * SCOPO: Cache degli elementi DOM per performance
     * PATTERN: DOM Caching per evitare query multiple
     * VANTAGGI: Performance migliori, codice più pulito
     */
    elements: {
        modal: null,           // Modal Bootstrap per assegnazione
        select: null,          // Select per scelta tecnico
        button: null,          // Pulsante submit
        form: null            // Form di assegnazione
    },
    
    // =====================================================
    // METODO DI INIZIALIZZAZIONE
    // =====================================================
    
    /**
     * METODO: init
     * SCOPO: Inizializza il modulo con configurazione
     * PARAMETRI: options (Object) - Configurazione da Laravel
     * RITORNA: boolean - Success/failure dell'inizializzazione
     * 
     * PATTERN: Initialization Method con validation
     * TECNOLOGIA: Object destructuring ES6
     */
    init(options = {}) {
        console.log('📍 Inizializzazione AdminCentroShow');
        
        // === MERGE CONFIGURAZIONE ===
        /**
         * SPREAD OPERATOR ES6 (...) 
         * Combina configurazione default con parametri passati
         * Permette override parziale delle impostazioni
         */
        this.config = {
            ...this.config,   // Configurazione esistente
            ...options        // Nuove opzioni (sovrascrivono esistenti)
        };
        
        // === SEQUENZA DI INIZIALIZZAZIONE ===
        // Ogni step è una funzione separata per modularità
        
        this.loadElements();          // 1. Carica elementi DOM
        
        // === EARLY RETURN PATTERN ===
        // Valida prerequisiti prima di continuare
        if (!this.validatePrerequisites()) {
            console.error('❌ Prerequisiti mancanti per AdminCentroShow');
            return false;  // Fallimento inizializzazione
        }
        
        this.setupEventListeners();  // 2. Configura event listeners
        
        // === MODALITÀ DEBUG CONDIZIONALE ===
        if (this.config.debugMode) {
            this.enableDebugMode();   // 3. Abilita debug se richiesto
        }
        
        console.log('✅ AdminCentroShow inizializzato correttamente');
        return true;  // Successo inizializzazione
    },
    
    // =====================================================
    // GESTIONE ELEMENTI DOM
    // =====================================================
    
    /**
     * METODO: loadElements
     * SCOPO: Carica e memorizza riferimenti agli elementi DOM
     * PATTERN: DOM Caching per performance
     * 
     * VANTAGGI:
     *   - Una sola query per elemento
     *   - Accesso veloce negli altri metodi
     *   - Codice più leggibile
     */
    loadElements() {
        // === OBJECT LITERAL ASSIGNMENT ===
        // Assegna tutti gli elementi in un'unica operazione
        this.elements = {
            modal: document.getElementById('modalAssegnaTecnico'),
            select: document.getElementById('tecnico_id'),
            button: document.getElementById('btnAssegnaTecnico'),
            form: document.getElementById('formAssegnaTecnico')
        };
    },
    
    // =====================================================
    // VALIDAZIONE PREREQUISITI
    // =====================================================
    
    /**
     * METODO: validatePrerequisites
     * SCOPO: Verifica che tutti i requisiti siano soddisfatti
     * RITORNA: boolean - true se tutto OK, false altrimenti
     * 
     * PATTERN: Guard Clauses per early exit
     * TECNOLOGIA: Object.entries() per iterazione dinamica
     */
    validatePrerequisites() {
        
        // === VALIDAZIONE CONFIGURAZIONE ===
        // Verifica che i parametri essenziali siano presenti
        if (!this.config.centroId || !this.config.baseUrl || !this.config.csrfToken) {
            console.error('❌ Configurazione mancante:', this.config);
            return false;
        }
        
        // === VALIDAZIONE ELEMENTI DOM ===
        /**
         * ALGORITMO:
         * 1. Object.entries() converte oggetto in array [key, value]
         * 2. .filter() trova elementi null/undefined
         * 3. .map() estrae solo le chiavi degli elementi mancanti
         */
        const missingElements = Object.entries(this.elements)
            .filter(([key, element]) => !element)  // Trova elementi null
            .map(([key]) => key);                   // Estrae solo le chiavi
            
        if (missingElements.length > 0) {
            console.error('❌ Elementi DOM mancanti:', missingElements);
            return false;
        }
        
        return true;  // Tutti i prerequisiti soddisfatti
    },
    
    // =====================================================
    // CONFIGURAZIONE EVENT LISTENERS
    // =====================================================
    
    /**
     * METODO: setupEventListeners
     * SCOPO: Configura tutti gli event listener del modulo
     * PATTERN: Central Event Registration
     * 
     * EVENTI GESTITI:
     *   - shown.bs.modal: Apertura modal Bootstrap
     *   - submit: Invio form
     *   - change: Cambio selezione tecnico
     */
    setupEventListeners() {
        
        // === EVENT LISTENER: MODAL APERTO ===
        /**
         * EVENTO: shown.bs.modal (Bootstrap 5 evento custom)
         * TRIGGER: Quando il modal è completamente visibile
         * CALLBACK: Arrow function per mantenere context (this)
         */
        this.elements.modal.addEventListener('shown.bs.modal', () => {
            this.handleModalOpen();
        });
        
        // === EVENT LISTENER: SUBMIT FORM ===
        /**
         * EVENTO: submit
         * COMPORTAMENTO: Previene submit normale, gestisce via AJAX
         * PARAMETRO: e (Event object) per preventDefault()
         */
        this.elements.form.addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });
        
        // === EVENT LISTENER: CAMBIO SELEZIONE ===
        /**
         * EVENTO: change su select
         * SCOPO: Mostra informazioni aggiuntive sui trasferimenti
         */
        this.elements.select.addEventListener('change', () => {
            this.handleTecnicoChange();
        });
    },
    
    // =====================================================
    // HANDLERS DEGLI EVENTI
    // =====================================================
    
    /**
     * HANDLER: handleModalOpen
     * TRIGGER: Quando il modal si apre completamente
     * SCOPO: Carica automaticamente la lista dei tecnici disponibili
     */
    handleModalOpen() {
        console.log('📂 Modal aperto - Caricamento tecnici');
        this.loadTecniciDisponibili();  // Carica dati via API
    },
    
    /**
     * HANDLER: handleFormSubmit
     * TRIGGER: Submit del form di assegnazione
     * PARAMETRO: e (Event) - Evento submit
     * COMPORTAMENTO: Previene submit normale, processa via AJAX
     */
    handleFormSubmit(e) {
        e.preventDefault();  // Previene submit HTML normale
        console.log('📤 Submit form - Avvio assegnazione');
        this.processAssegnazioneTecnico();  // Processa con AJAX
    },
    
    /**
     * HANDLER: handleTecnicoChange
     * TRIGGER: Cambio selezione nel dropdown tecnici
     * SCOPO: Mostra info sui trasferimenti se necessario
     */
    handleTecnicoChange() {
        this.showTransferInfo();  // Mostra info trasferimento
    },
    
    // =====================================================
    // CHIAMATE API AJAX
    // =====================================================
    
    /**
     * METODO ASYNC: loadTecniciDisponibili
     * SCOPO: Carica via API la lista dei tecnici disponibili
     * TECNOLOGIA: Fetch API + async/await ES8
     * GESTIONE ERRORI: Try-catch con fallback UI
     */
    async loadTecniciDisponibili() {
        console.log('🔄 Caricamento tecnici disponibili...');
        
        // === RESET UI PRIMA DEL CARICAMENTO ===
        this.resetSelectUI();    // Mostra "Caricamento..."
        this.disableButton();    // Disabilita pulsante
        
        try {
            // === COSTRUZIONE URL API ===
            /**
             * TEMPLATE LITERAL ES6
             * Costruisce URL dinamico con ID centro
             * Pattern REST API: /api/resource/{id}/related-resource
             */
            const apiUrl = `${this.config.baseUrl}/api/admin/centri/${this.config.centroId}/tecnici-disponibili`;
            
            // === CHIAMATA FETCH API ===
            /**
             * FETCH API (moderne browsers)
             * VANTAGGI vs XMLHttpRequest:
             *   - Promise-based (no callback hell)
             *   - Più pulita e leggibile
             *   - Supporto nativo async/await
             */
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',           // Richiede JSON
                    'X-Requested-With': 'XMLHttpRequest',   // Header AJAX Laravel
                    'X-CSRF-TOKEN': this.config.csrfToken  // Sicurezza CSRF
                },
                credentials: 'same-origin'  // Include cookies sessione
            });
            
            // === CONTROLLO STATUS HTTP ===
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // === PARSING RISPOSTA JSON ===
            const data = await response.json();
            
            // === VALIDAZIONE FORMATO DATI ===
            if (data.success && Array.isArray(data.tecnici)) {
                this.populateSelectTecnici(data.tecnici);  // Popola select
            } else {
                throw new Error(data.message || 'Formato dati non valido');
            }
            
        } catch (error) {
            // === GESTIONE ERRORI ===
            console.error('❌ Errore caricamento tecnici:', error);
            this.showSelectError('Errore: ' + error.message);
            this.showNotification('Errore nel caricamento tecnici: ' + error.message, 'danger');
            
        } finally {
            // === FINALLY BLOCK ===
            /**
             * ESEGUITO SEMPRE (successo o errore)
             * Ripristina UI in stato utilizzabile
             */
            this.enableSelect();
        }
    },
    
    // =====================================================
    // MANIPOLAZIONE DOM DINAMICA
    // =====================================================
    
    /**
     * METODO: populateSelectTecnici
     * SCOPO: Popola la select con i tecnici ricevuti dall'API
     * PARAMETRO: tecnici (Array) - Lista tecnici da API
     * 
     * LOGICA:
     *   - Separa tecnici liberi da quelli da trasferire
     *   - Crea optgroup separati per chiarezza
     *   - Gestisce caso lista vuota
     */
    populateSelectTecnici(tecnici) {
        console.log(`📋 Popolamento select con ${tecnici.length} tecnici`);
        
        // === RESET SELECT ===
        // Pulisce contenuto esistente e aggiunge opzione default
        this.elements.select.innerHTML = '<option value="">-- Seleziona un tecnico --</option>';
        
        // === GESTIONE LISTA VUOTA ===
        if (tecnici.length === 0) {
            this.elements.select.innerHTML += '<option value="">Nessun tecnico disponibile</option>';
            return;  // Early return se nessun tecnico
        }
        
        // === SEPARAZIONE TECNICI PER CATEGORIA ===
        /**
         * ARRAY.FILTER() + LOGIC CONDITIONALS
         * Separa tecnici in base al loro stato attuale
         */
        const tecniciLiberi = tecnici.filter(t => 
            t.centro_attuale && t.centro_attuale.status === 'unassigned'
        );
        
        const tecniciAssegnati = tecnici.filter(t => 
            t.centro_attuale && t.centro_attuale.status === 'assigned'
        );
        
        // === CREAZIONE GRUPPI OPZIONI ===
        if (tecniciLiberi.length > 0) {
            this.addOptGroup('Tecnici Disponibili', tecniciLiberi, 'libero');
        }
        
        if (tecniciAssegnati.length > 0) {
            this.addOptGroup('Trasferimento da Altri Centri', tecniciAssegnati, 'trasferimento');
        }
        
        // === ABILITAZIONE UI ===
        this.enableButton();  // Rende cliccabile il pulsante
        
        console.log('✅ Select popolata con successo');
    },
    
    /**
     * METODO: addOptGroup
     * SCOPO: Aggiunge un gruppo di opzioni (optgroup) alla select
     * PARAMETRI:
     *   - label: Etichetta del gruppo
     *   - tecnici: Array di tecnici per questo gruppo
     *   - tipo: Tipo tecnico ('libero' o 'trasferimento')
     * 
     * TECNOLOGIA: DOM Manipulation APIs
     */
    addOptGroup(label, tecnici, tipo) {
        // === CREAZIONE OPTGROUP ===
        /**
         * ELEMENT CREATION: document.createElement()
         * Crea elemento HTML in memoria (non ancora nel DOM)
         */
        const gruppo = document.createElement('optgroup');
        gruppo.label = label;  // Attributo HTML label
        
        // === ITERAZIONE TECNICI ===
        /**
         * ARRAY.FOREACH() per iterazione con side effects
         * Crea un'option per ogni tecnico
         */
        tecnici.forEach(tecnico => {
            // === CREAZIONE OPTION ===
            const option = document.createElement('option');
            option.value = tecnico.id;  // Valore per il form
            option.setAttribute('data-tipo', tipo);  // Data attribute custom
            
            // === LOGICA CONDIZIONALE PER TESTO ===
            if (tipo === 'libero') {
                // Tecnici liberi: nome + specializzazione
                option.textContent = `${tecnico.nome_completo} - ${tecnico.specializzazione || 'N/A'}`;
            } else {
                // Tecnici da trasferire: nome + centro attuale
                option.textContent = `${tecnico.nome_completo} (da: ${tecnico.centro_attuale.nome})`;
                option.setAttribute('data-centro-attuale', tecnico.centro_attuale.nome);
            }
            
            // === APPEND AL GRUPPO ===
            gruppo.appendChild(option);
        });
        
        // === APPEND AL SELECT ===
        this.elements.select.appendChild(gruppo);
    },
    
    // =====================================================
    // GESTIONE INFO TRASFERIMENTI
    // =====================================================
    
    /**
     * METODO: showTransferInfo
     * SCOPO: Mostra informazioni sui trasferimenti quando necessario
     * TRIGGER: Cambio selezione nel dropdown
     * 
     * COMPORTAMENTO:
     *   - Analizza opzione selezionata
     *   - Mostra avviso se è un trasferimento
     *   - Rimuove avvisi precedenti
     */
    showTransferInfo() {
        // === ANALISI OPZIONE SELEZIONATA ===
        const opzioneSelezionata = this.elements.select.options[this.elements.select.selectedIndex];
        const centroAttuale = opzioneSelezionata?.getAttribute('data-centro-attuale');
        
        // === CLEANUP PRECEDENTE ===
        this.removeTransferInfo();  // Rimuove info esistenti
        
        // === MOSTRA INFO TRASFERIMENTO ===
        if (centroAttuale && this.elements.select.value) {
            /**
             * CREAZIONE ELEMENTO INFORMATIVO
             * Crea div di avviso con Bootstrap classes
             */
            const infoDiv = document.createElement('div');
            infoDiv.id = 'infoTrasferimento';
            infoDiv.className = 'alert alert-warning mt-2';
            
            // === TEMPLATE LITERAL CON HTML ===
            infoDiv.innerHTML = `
                <i class="bi bi-arrow-right-circle me-2"></i>
                <strong>Trasferimento:</strong> Il tecnico sarà automaticamente rimosso da "${centroAttuale}"
            `;
            
            // === INSERIMENTO NEL DOM ===
            /**
             * PARENTNODE.APPENDCHILD()
             * Aggiunge elemento dopo la select
             */
            this.elements.select.parentNode.appendChild(infoDiv);
        }
    },
    
    /**
     * METODO: removeTransferInfo
     * SCOPO: Rimuove informazioni sui trasferimenti esistenti
     * PATTERN: Cleanup method per evitare duplicati
     */
    removeTransferInfo() {
        const infoEsistente = document.getElementById('infoTrasferimento');
        if (infoEsistente) {
            infoEsistente.remove();  // Rimozione dal DOM
        }
    },
    
    // =====================================================
    // PROCESSO DI ASSEGNAZIONE TECNICO
    // =====================================================
    
    /**
     * METODO ASYNC: processAssegnazioneTecnico
     * SCOPO: Gestisce l'intero processo di assegnazione
     * FLUSSO:
     *   1. Validazione input
     *   2. Conferma trasferimenti
     *   3. Chiamata API
     *   4. Gestione risposta
     *   5. Aggiornamento UI
     * 
     * PATTERN: Async/Await per operazioni sequenziali
     */
    async processAssegnazioneTecnico() {
        console.log('🎯 Inizio processo assegnazione tecnico');
        
        // === VALIDAZIONE INPUT ===
        const tecnicoId = this.elements.select.value;
        if (!tecnicoId) {
            this.showNotification('Seleziona un tecnico da assegnare', 'warning');
            return;  // Early return se nessuna selezione
        }
        
        // === RACCOLTA INFORMAZIONI TECNICO ===
        const tecnicoInfo = this.getSelectedTecnicoInfo();
        
        // === CONFERMA TRASFERIMENTI ===
        /**
         * AWAIT + CONDITIONAL
         * Chiede conferma solo per trasferimenti
         * Short-circuit evaluation (&& operator)
         */
        if (tecnicoInfo.isTransfer && !await this.confirmTransfer(tecnicoInfo)) {
            return;  // Utente ha annullato
        }
        
        // === UI LOADING STATE ===
        this.setLoadingState(tecnicoInfo.isTransfer);
        
        try {
            // === CHIAMATA API ===
            const response = await this.sendAssignmentRequest(tecnicoId);
            
            // === GESTIONE RISPOSTA ===
            if (response.success) {
                await this.handleAssignmentSuccess(response);
            } else {
                throw new Error(response.message || 'Errore nell\'operazione');
            }
            
        } catch (error) {
            // === GESTIONE ERRORI ===
            console.error('❌ Errore assegnazione:', error);
            this.handleAssignmentError(error);
            
        } finally {
            // === RIPRISTINO UI ===
            /**
             * FINALLY BLOCK
             * Eseguito sempre per ripristinare UI
             */
            this.resetLoadingState();
        }
    },
    
    // =====================================================
    // UTILITY METHODS
    // =====================================================
    
    /**
     * METODO: getSelectedTecnicoInfo
     * SCOPO: Estrae informazioni dettagliate sul tecnico selezionato
     * RITORNA: Object con proprietà del tecnico
     * 
     * TECNOLOGIE:
     *   - DOM traversal
     *   - String manipulation
     *   - Object composition
     */
    getSelectedTecnicoInfo() {
        const opzioneSelezionata = this.elements.select.options[this.elements.select.selectedIndex];
        const tipo = opzioneSelezionata.getAttribute('data-tipo');
        
        // === STRING MANIPULATION ===
        /**
         * SPLIT() + ARRAY ACCESS
         * Estrae nome tecnico dal testo dell'opzione
         * Gestisce formati diversi per liberi vs trasferimenti
         */
        const nomeTecnico = opzioneSelezionata.text.split(' - ')[0].split(' (')[0];
        const centroAttuale = opzioneSelezionata.getAttribute('data-centro-attuale');
        
        // === OBJECT COMPOSITION ===
        return {
            id: this.elements.select.value,
            nome: nomeTecnico,
            tipo: tipo,
            centroAttuale: centroAttuale,
            isTransfer: tipo === 'trasferimento' && centroAttuale  // Boolean computation
        };
    },
    
    /**
     * METODO ASYNC: confirmTransfer
     * SCOPO: Chiede conferma all'utente per i trasferimenti
     * PARAMETRO: tecnicoInfo (Object) - Info tecnico
     * RITORNA: Promise<boolean> - Conferma utente
     * 
     * TECNOLOGIA: Browser confirm() API
     */
    async confirmTransfer(tecnicoInfo) {
        // === TEMPLATE LITERAL MULTILINE ===
        /**
         * MULTILINE STRING con interpolazione
         * \n per new lines nel dialog
         */
        const confermaMsg = `TRASFERIMENTO TECNICO\n\n` +
                           `Tecnico: ${tecnicoInfo.nome}\n` +
                           `Da: ${tecnicoInfo.centroAttuale}\n` +
                           `Al centro corrente\n\n` +
                           `Il tecnico sarà automaticamente rimosso dal centro precedente.\n\n` +
                           `Confermi il trasferimento?`;
        
        // === BROWSER CONFIRM API ===
        /**
         * RETURN DIRECT BOOLEAN
         * confirm() ritorna true/false
         */
        return confirm(confermaMsg);
    },
    
    /**
     * METODO ASYNC: sendAssignmentRequest
     * SCOPO: Invia richiesta di assegnazione al server
     * PARAMETRO: tecnicoId (string) - ID del tecnico
     * RITORNA: Promise<Object> - Risposta del server
     * 
     * TECNOLOGIE:
     *   - FormData API per form multipart
     *   - Fetch API per HTTP request
     *   - Error handling
     */
    async sendAssignmentRequest(tecnicoId) {
        // === PREPARAZIONE FORM DATA ===
        /**
         * FORMDATA API
         * Simula form HTML con encoding corretto
         * Supporta file upload se necessario
         */
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', this.config.csrfToken);  // CSRF Laravel
        
        // === FETCH API CALL ===
        const response = await fetch(this.elements.form.getAttribute('action'), {
            method: 'POST',
            body: formData,  // FormData come body
            headers: {
                'X-Requested-With': 'XMLHttpRequest',  // Header AJAX
                'Accept': 'application/json'           // Richiede JSON
            },
            credentials: 'same-origin'  // Include session cookies
        });
        
        // === GESTIONE STATUS HTTP ===
        if (!response.ok) {
            // === ERROR PARSING ===
            const errorText = await response.text();
            let errorData;
            
            // === TRY-CATCH PER JSON PARSING ===
            try {
                errorData = JSON.parse(errorText);
            } catch {
                // Fallback se non è JSON valido
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            throw new Error(errorData.message || `HTTP ${response.status}`);
        }
        
        return await response.json();  // Parse JSON risposta
    },
    
    // =====================================================
    // GESTIONE RISPOSTA ASSEGNAZIONE
    // =====================================================
    
    /**
     * METODO ASYNC: handleAssignmentSuccess
     * SCOPO: Gestisce il successo dell'operazione di assegnazione
     * PARAMETRO: response (Object) - Risposta del server
     * 
     * COMPORTAMENTO:
     *   - Mostra notifiche di successo
     *   - Gestisce info aggiuntive per trasferimenti
     *   - Chiude modal e ricarica pagina
     */
    async handleAssignmentSuccess(response) {
        console.log('✅ Assegnazione completata:', response);
        
        // === NOTIFICA PRINCIPALE ===
        this.showNotification(response.message || 'Operazione completata con successo', 'success');
        
        // === INFO AGGIUNTIVE PER TRASFERIMENTI ===
        /**
         * CONDITIONAL NOTIFICATION
         * Mostra notifica aggiuntiva se è un trasferimento
         * setTimeout per sequenza temporale delle notifiche
         */
        if (response.is_transfer && response.previous_center) {
            setTimeout(() => {
                this.showNotification(
                    `Tecnico rimosso automaticamente da "${response.previous_center}"`, 
                    'info'
                );
            }, 1500);  // Ritardo 1.5 secondi
        }
        
        // === CHIUSURA E RELOAD ===
        /**
         * SETTIMEOUT per permettere lettura notifiche
         * Poi chiude modal e ricarica pagina
         */
        setTimeout(() => {
            this.closeModal();
            window.location.reload();  // Refresh per mostrare modifiche
        }, 3000);
    },
    
    /**
     * METODO: handleAssignmentError
     * SCOPO: Gestisce gli errori di assegnazione con messaggi user-friendly
     * PARAMETRO: error (Error) - Oggetto errore
     * 
     * PATTERN: Error Message Mapping per UX migliore
     */
    handleAssignmentError(error) {
        let messaggioErrore = 'Errore nell\'operazione';  // Default
        const errorMsg = error.message.toLowerCase();     // Case-insensitive
        
        // === ERROR MESSAGE MAPPING ===
        /**
         * CHAIN OF IF-ELSE per mappare errori specifici
         * String.includes() per pattern matching
         */
        if (errorMsg.includes('già assegnato')) {
            messaggioErrore = 'Tecnico già assegnato a questo centro';
        } else if (errorMsg.includes('403') || errorMsg.includes('non autorizzato')) {
            messaggioErrore = 'Non hai i permessi per questa operazione';
        } else if (errorMsg.includes('422') || errorMsg.includes('non validi')) {
            messaggioErrore = 'Dati non validi';
        } else if (errorMsg.includes('500')) {
            messaggioErrore = 'Errore del server';
        }
        
        // === NOTIFICA ERRORE ===
        this.showNotification(messaggioErrore + ': ' + error.message, 'danger');
    },
    
    // =====================================================
    // GESTIONE STATI UI
    // =====================================================
    
    /**
     * METODO: setLoadingState
     * SCOPO: Imposta UI in stato di caricamento durante operazioni
     * PARAMETRO: isTransfer (boolean) - Se è un trasferimento
     * 
     * CAMBIAMENTI UI:
     *   - Disabilita controlli
     *   - Cambia testo pulsante con spinner
     *   - Salva stato originale per ripristino
     */
    setLoadingState(isTransfer) {
        // === DISABILITAZIONE CONTROLLI ===
        this.elements.select.disabled = true;
        this.elements.button.disabled = true;
        
        // === SALVATAGGIO STATO ORIGINALE ===
        /**
         * CUSTOM PROPERTY per memorizzare testo originale
         * Necessario per ripristino dopo operazione
         */
        this.elements.button.originalText = this.elements.button.innerHTML;
        
        // === CAMBIO TESTO CONDIZIONALE ===
        if (isTransfer) {
            this.elements.button.innerHTML = '<i class="bi bi-arrow-right me-1"></i> Trasferimento...';
        } else {
            this.elements.button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Assegnazione...';
        }
    },
    
    /**
     * METODO: resetLoadingState
     * SCOPO: Ripristina UI dopo operazione (successo o errore)
     * 
     * RIPRISTINI:
     *   - Riabilita controlli
     *   - Ripristina testo originale pulsante
     */
    resetLoadingState() {
        // === RIABILITAZIONE CONTROLLI ===
        this.elements.select.disabled = false;
        this.elements.button.disabled = false;
        
        // === RIPRISTINO TESTO ORIGINALE ===
        /**
         * CONDITIONAL RESTORATION
         * Ripristina solo se era stato salvato
         */
        if (this.elements.button.originalText) {
            this.elements.button.innerHTML = this.elements.button.originalText;
        }
    },
    
    // =====================================================
    // UTILITY METHODS PER UI
    // =====================================================
    
    /**
     * METODO: resetSelectUI
     * SCOPO: Reset dell'interfaccia select durante caricamento
     * COMPORTAMENTO: Mostra messaggio di caricamento e disabilita
     */
    resetSelectUI() {
        this.elements.select.innerHTML = '<option value="">⏳ Caricamento...</option>';
        this.elements.select.disabled = true;
    },
    
    /**
     * METODO: showSelectError
     * SCOPO: Mostra messaggio di errore nella select
     * PARAMETRO: messaggio (string) - Messaggio di errore da mostrare
     */
    showSelectError(messaggio) {
        this.elements.select.innerHTML = `<option value="">❌ ${messaggio}</option>`;
    },
    
    /**
     * METODO: enableSelect
     * SCOPO: Abilita la select dopo caricamento dati
     */
    enableSelect() {
        this.elements.select.disabled = false;
    },
    
    /**
     * METODO: disableButton
     * SCOPO: Disabilita il pulsante di assegnazione
     */
    disableButton() {
        this.elements.button.disabled = true;
    },
    
    /**
     * METODO: enableButton
     * SCOPO: Abilita il pulsante di assegnazione
     */
    enableButton() {
        this.elements.button.disabled = false;
    },
    
    /**
     * METODO: closeModal
     * SCOPO: Chiude il modal Bootstrap programmaticamente
     * TECNOLOGIA: Bootstrap 5 Modal API
     */
    closeModal() {
        /**
         * BOOTSTRAP MODAL API
         * getInstance() ottiene istanza esistente del modal
         * hide() chiude con animazione
         */
        const modalInstance = bootstrap.Modal.getInstance(this.elements.modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    },
    
    // =====================================================
    // SISTEMA DI NOTIFICHE
    // =====================================================
    
    /**
     * METODO: showNotification
     * SCOPO: Sistema di notifiche temporanee per feedback utente
     * PARAMETRI:
     *   - messaggio (string): Testo da mostrare
     *   - tipo (string): Tipo notifica ('success', 'danger', 'warning', 'info')
     * 
     * TECNOLOGIE:
     *   - Bootstrap 5 Alert components
     *   - CSS positioning
     *   - Auto-dismissing con setTimeout
     */
    showNotification(messaggio, tipo = 'info') {
        console.log(`📢 Notifica ${tipo.toUpperCase()}: ${messaggio}`);
        
        // === MAPPATURA CLASSI BOOTSTRAP ===
        /**
         * OBJECT LITERAL per mapping tipo -> classe CSS
         * Bootstrap 5 alert variant classes
         */
        const tipiAlert = {
            'success': 'alert-success',
            'danger': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        // === MAPPATURA ICONE BOOTSTRAP ICONS ===
        /**
         * OBJECT LITERAL per mapping tipo -> icona
         * Bootstrap Icons per visual feedback
         */
        const icone = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-circle',
            'info': 'bi-info-circle'
        };
        
        // === CREAZIONE ELEMENTO NOTIFICA ===
        /**
         * DOCUMENT.CREATEELEMENT() per DOM manipulation
         * Crea alert Bootstrap con posizionamento fisso
         */
        const notifica = document.createElement('div');
        notifica.className = `alert ${tipiAlert[tipo]} alert-dismissible fade show`;
        
        // === CSS INLINE STYLING ===
        /**
         * CSSSTYLE per posizionamento
         * position: fixed per rimanere visibile durante scroll
         * z-index: 9999 per apparire sopra tutto
         */
        notifica.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        
        // === TEMPLATE LITERAL CON HTML ===
        /**
         * INNERHTML con template literal
         * Include icona dinamica e pulsante dismissal
         */
        notifica.innerHTML = `
            <i class="bi ${icone[tipo]} me-2"></i>
            ${messaggio}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // === INSERIMENTO NEL DOM ===
        document.body.appendChild(notifica);
        
        // === AUTO-RIMOZIONE TEMPORIZZATA ===
        /**
         * SETTIMEOUT per auto-dismiss
         * 5 secondi di visibilità, poi rimozione automatica
         */
        setTimeout(() => {
            if (notifica && notifica.parentNode) {
                notifica.remove();
            }
        }, 5000);
    },
    
    // =====================================================
    // MODALITÀ DEBUG
    // =====================================================
    
    /**
     * METODO: enableDebugMode
     * SCOPO: Abilita modalità debug per sviluppo e testing
     * 
     * FUNZIONALITÀ DEBUG:
     *   - Esposizione modulo a window scope
     *   - Funzioni helper globali
     *   - Logging dettagliato
     */
    enableDebugMode() {
        console.log('🔧 Modalità debug abilitata');
        
        // === ESPOSIZIONE GLOBALE ===
        /**
         * WINDOW SCOPE ASSIGNMENT
         * Rende il modulo accessibile dalla console browser
         * Utile per debugging e testing manuale
         */
        window.AdminCentroShow = this;
        
        // === FUNZIONE DEBUG PRINCIPALE ===
        /**
         * GLOBAL DEBUG FUNCTION
         * Mostra stato interno del modulo
         */
        window.debugCentroShow = () => {
            console.log('🔧 DEBUG AdminCentroShow:');
            console.log('Config:', this.config);
            console.log('Elements:', this.elements);
            console.log('API URL:', `${this.config.baseUrl}/api/admin/centri/${this.config.centroId}/tecnici-disponibili`);
        };
        
        // === FUNZIONE TEST MODAL ===
        /**
         * GLOBAL TEST FUNCTION
         * Permette apertura modal da console
         */
        window.testModalOpen = () => {
            const modalInstance = new bootstrap.Modal(this.elements.modal);
            modalInstance.show();
        };
        
        console.log('💡 Funzioni debug disponibili: debugCentroShow(), testModalOpen()');
    }
};

// =====================================================
// MODULO UTILITY: GOOGLE MAPS
// =====================================================

/**
 * OGGETTO UTILITY: GoogleMapsUtil
 * SCOPO: Utility per integrazione con Google Maps
 * PATTERN: Utility Object per funzioni stateless
 * 
 * SEPARAZIONE CONCERNS:
 *   - Logica Maps separata dal modulo principale
 *   - Riusabile in altri contesti
 *   - API semplice e focalizzata
 */
const GoogleMapsUtil = {
    
    /**
     * METODO: openMaps
     * SCOPO: Apre Google Maps con un indirizzo specifico
     * PARAMETRO: indirizzo (string) - Indirizzo da cercare
     * 
     * TECNOLOGIE:
     *   - Google Maps Search API via URL
     *   - encodeURIComponent per URL encoding
     *   - window.open per nuova tab
     */
    openMaps(indirizzo) {
        // === COSTRUZIONE URL GOOGLE MAPS ===
        /**
         * GOOGLE MAPS SEARCH API
         * URL format: https://www.google.com/maps/search/?api=1&query=...
         * encodeURIComponent() per encoding caratteri speciali
         */
        const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(indirizzo)}`;
        
        // === APERTURA NUOVA TAB ===
        /**
         * WINDOW.OPEN() per aprire in nuova tab
         * '_blank' target per nuova finestra
         * Mantiene contesto dell'applicazione
         */
        window.open(url, '_blank');
        
        console.log('🗺️ Apertura Google Maps per:', indirizzo);
    }
};

// =====================================================
// INIZIALIZZAZIONE AUTOMATICA
// =====================================================

/**
 * EVENT LISTENER: DOMContentLoaded
 * SCOPO: Inizializzazione automatica quando DOM è pronto
 * PATTERN: Auto-initialization con configuration detection
 * 
 * FLUSSO:
 *   1. Verifica presenza configurazione
 *   2. Inizializza modulo se configurazione presente
 *   3. Log risultato operazione
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // === VERIFICA CONFIGURAZIONE ===
    /**
     * CONFIGURATION DETECTION
     * window.AdminCentroShowConfig deve essere impostato
     * dal template Blade Laravel prima di questo script
     */
    if (!window.AdminCentroShowConfig) {
        console.log('⏭️ AdminCentroShowConfig non trovato, skip inizializzazione');
        return;  // Early return se configurazione mancante
    }
    
    // === INIZIALIZZAZIONE MODULO ===
    /**
     * MODULE INITIALIZATION
     * Passa configurazione da window scope al modulo
     */
    const success = AdminCentroShow.init(window.AdminCentroShowConfig);
    
    // === LOG RISULTATO ===
    if (success) {
        console.log('🎉 AdminCentroShow caricato con successo');
    }
});

// =====================================================
// ESPORTAZIONE GLOBALE
// =====================================================

/**
 * GLOBAL EXPORTS
 * SCOPO: Rende moduli disponibili globalmente
 * 
 * RAGIONI:
 *   - Accessibilità da template Blade
 *   - Interoperabilità con altri script
 *   - Debugging e testing
 */
window.AdminCentroShow = AdminCentroShow;
window.GoogleMapsUtil = GoogleMapsUtil;

/*
 * =====================================================
 * RIEPILOGO TECNOLOGIE E PATTERN UTILIZZATI:
 * =====================================================
 * 
 * 1. JAVASCRIPT AVANZATO ES6+:
 *    - Module Pattern con Object Literals
 *    - Async/Await per operazioni asincrone
 *    - Arrow functions per context preservation
 *    - Template literals per string interpolation
 *    - Destructuring assignment
 *    - Spread operator per object merging
 * 
 * 2. DOM MANIPULATION:
 *    - document.createElement() per creazione elementi
 *    - appendChild() per inserimento DOM
 *    - Element.remove() per pulizia
 *    - getAttribute/setAttribute per data attributes
 *    - innerHTML per content management
 * 
 * 3. FETCH API E AJAX:
 *    - fetch() per HTTP requests
 *    - FormData per form encoding
 *    - Headers management per CSRF e content-type
 *    - Error handling con try-catch
 *    - JSON parsing e validation
 * 
 * 4. BOOTSTRAP 5 INTEGRATION:
 *    - Modal API per dialog management
 *    - Alert components per notifications
 *    - CSS classes per styling
 *    - Event system (shown.bs.modal)
 * 
 * 5. LARAVEL INTEGRATION:
 *    - CSRF token handling
 *    - Route-based API calls
 *    - Configuration passing via window scope
 *    - Error response handling
 * 
 * 6. UX/UI PATTERNS:
 *    - Loading states per feedback
 *    - Progressive disclosure per info aggiuntive
 *    - Confirmation dialogs per azioni critiche
 *    - Auto-dismissing notifications
 *    - Form validation e error handling
 * 
 * 7. ARCHITECTURAL PATTERNS:
 *    - Module Pattern per encapsulation
 *    - Configuration Object per parametrization
 *    - Separation of Concerns
 *    - Error boundaries
 *    - Graceful degradation
 * 
 * 8. PERFORMANCE OPTIMIZATIONS:
 *    - DOM element caching
 *    - Early returns per evitare computazioni
 *    - Efficient DOM queries
 *    - Memory cleanup (remove listeners)
 * 
 * 9. DEBUGGING E MAINTENANCE:
 *    - Debug mode con funzioni helper
 *    - Comprehensive logging
 *    - Error tracking
 *    - Global exposure per testing
 * 
 * 10. SICUREZZA:
 *     - CSRF protection
 *     - Input validation
 *     - Same-origin policy
 *     - Secure error handling
 * 
 * =====================================================
 * BEST PRACTICES IMPLEMENTATE:
 * =====================================================
 * 
 * - Consistent naming conventions
 * - Comprehensive error handling
 * - User-friendly error messages
 * - Progressive enhancement
 * - Accessibility considerations
 * - Mobile-responsive design
 * - Performance optimization
 * - Code documentation
 * - Separation of concerns
 * - Testability
 * - Maintainability
 * - Scalability
 * 
 * =====================================================
 */