/**
 * ===================================================================
 * File: public/js/admin/centri-show.js
 * Descrizione: Gestione interfaccia amministrativa centro assistenza
 * Funzionalità: Assegnazione tecnici, notifiche, Google Maps
 * Versione: 1.0
 * ===================================================================
 */

/**
 * Modulo principale per la gestione del centro assistenza
 */
const AdminCentroShow = {
    
    /**
     * Configurazione del modulo
     */
    config: {
        centroId: null,
        baseUrl: null,
        csrfToken: null,
        debugMode: false
    },
    
    /**
     * Elementi DOM cachati
     */
    elements: {
        modal: null,
        select: null,
        button: null,
        form: null
    },
    
    /**
     * Inizializzazione del modulo
     * @param {Object} options - Opzioni di configurazione
     */
    init(options = {}) {
        console.log('📍 Inizializzazione AdminCentroShow');
        
        // Imposta configurazione
        this.config = {
            ...this.config,
            ...options
        };
        
        // Carica elementi DOM
        this.loadElements();
        
        // Verifica prerequisiti
        if (!this.validatePrerequisites()) {
            console.error('❌ Prerequisiti mancanti per AdminCentroShow');
            return false;
        }
        
        // Configura event listeners
        this.setupEventListeners();
        
        // Modalità debug
        if (this.config.debugMode) {
            this.enableDebugMode();
        }
        
        console.log('✅ AdminCentroShow inizializzato correttamente');
        return true;
    },
    
    /**
     * Carica e cача gli elementi DOM
     */
    loadElements() {
        this.elements = {
            modal: document.getElementById('modalAssegnaTecnico'),
            select: document.getElementById('tecnico_id'),
            button: document.getElementById('btnAssegnaTecnico'),
            form: document.getElementById('formAssegnaTecnico')
        };
    },
    
    /**
     * Valida che tutti i prerequisiti siano presenti
     * @returns {boolean}
     */
    validatePrerequisites() {
        // Verifica configurazione
        if (!this.config.centroId || !this.config.baseUrl || !this.config.csrfToken) {
            console.error('❌ Configurazione mancante:', this.config);
            return false;
        }
        
        // Verifica elementi DOM
        const missingElements = Object.entries(this.elements)
            .filter(([key, element]) => !element)
            .map(([key]) => key);
            
        if (missingElements.length > 0) {
            console.error('❌ Elementi DOM mancanti:', missingElements);
            return false;
        }
        
        return true;
    },
    
    /**
     * Configura gli event listeners
     */
    setupEventListeners() {
        // Event listener per apertura modal
        this.elements.modal.addEventListener('shown.bs.modal', () => {
            this.handleModalOpen();
        });
        
        // Event listener per submit form
        this.elements.form.addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });
        
        // Event listener per cambio selezione tecnico
        this.elements.select.addEventListener('change', () => {
            this.handleTecnicoChange();
        });
    },
    
    /**
     * Gestisce l'apertura del modal
     */
    handleModalOpen() {
        console.log('📂 Modal aperto - Caricamento tecnici');
        this.loadTecniciDisponibili();
    },
    
    /**
     * Gestisce il submit del form
     * @param {Event} e - Evento submit
     */
    handleFormSubmit(e) {
        e.preventDefault();
        console.log('📤 Submit form - Avvio assegnazione');
        this.processAssegnazioneTecnico();
    },
    
    /**
     * Gestisce il cambio di selezione del tecnico
     */
    handleTecnicoChange() {
        this.showTransferInfo();
    },
    
    /**
     * Carica la lista dei tecnici disponibili
     */
    async loadTecniciDisponibili() {
        console.log('🔄 Caricamento tecnici disponibili...');
        
        // Reset UI
        this.resetSelectUI();
        this.disableButton();
        
        try {
            // Costruisci URL API
            const apiUrl = `${this.config.baseUrl}/api/admin/centri/${this.config.centroId}/tecnici-disponibili`;
            
            // Esegui chiamata API
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && Array.isArray(data.tecnici)) {
                this.populateSelectTecnici(data.tecnici);
            } else {
                throw new Error(data.message || 'Formato dati non valido');
            }
            
        } catch (error) {
            console.error('❌ Errore caricamento tecnici:', error);
            this.showSelectError('Errore: ' + error.message);
            this.showNotification('Errore nel caricamento tecnici: ' + error.message, 'danger');
        } finally {
            this.enableSelect();
        }
    },
    
    /**
     * Popola la select con i tecnici disponibili
     * @param {Array} tecnici - Lista tecnici
     */
    populateSelectTecnici(tecnici) {
        console.log(`📋 Popolamento select con ${tecnici.length} tecnici`);
        
        // Reset select
        this.elements.select.innerHTML = '<option value="">-- Seleziona un tecnico --</option>';
        
        if (tecnici.length === 0) {
            this.elements.select.innerHTML += '<option value="">Nessun tecnico disponibile</option>';
            return;
        }
        
        // Separa tecnici per categoria
        const tecniciLiberi = tecnici.filter(t => 
            t.centro_attuale && t.centro_attuale.status === 'unassigned'
        );
        const tecniciAssegnati = tecnici.filter(t => 
            t.centro_attuale && t.centro_attuale.status === 'assigned'
        );
        
        // Aggiungi tecnici liberi
        if (tecniciLiberi.length > 0) {
            this.addOptGroup('Tecnici Disponibili', tecniciLiberi, 'libero');
        }
        
        // Aggiungi tecnici trasferibili
        if (tecniciAssegnati.length > 0) {
            this.addOptGroup('Trasferimento da Altri Centri', tecniciAssegnati, 'trasferimento');
        }
        
        // Abilita pulsante
        this.enableButton();
        
        console.log('✅ Select popolata con successo');
    },
    
    /**
     * Aggiunge un gruppo di opzioni alla select
     * @param {string} label - Etichetta del gruppo
     * @param {Array} tecnici - Lista tecnici
     * @param {string} tipo - Tipo di tecnico (libero/trasferimento)
     */
    addOptGroup(label, tecnici, tipo) {
        const gruppo = document.createElement('optgroup');
        gruppo.label = label;
        
        tecnici.forEach(tecnico => {
            const option = document.createElement('option');
            option.value = tecnico.id;
            option.setAttribute('data-tipo', tipo);
            
            if (tipo === 'libero') {
                option.textContent = `${tecnico.nome_completo} - ${tecnico.specializzazione || 'N/A'}`;
            } else {
                option.textContent = `${tecnico.nome_completo} (da: ${tecnico.centro_attuale.nome})`;
                option.setAttribute('data-centro-attuale', tecnico.centro_attuale.nome);
            }
            
            gruppo.appendChild(option);
        });
        
        this.elements.select.appendChild(gruppo);
    },
    
    /**
     * Mostra informazioni sui trasferimenti
     */
    showTransferInfo() {
        const opzioneSelezionata = this.elements.select.options[this.elements.select.selectedIndex];
        const centroAttuale = opzioneSelezionata?.getAttribute('data-centro-attuale');
        
        // Rimuovi info precedenti
        this.removeTransferInfo();
        
        // Mostra info trasferimento se necessario
        if (centroAttuale && this.elements.select.value) {
            const infoDiv = document.createElement('div');
            infoDiv.id = 'infoTrasferimento';
            infoDiv.className = 'alert alert-warning mt-2';
            infoDiv.innerHTML = `
                <i class="bi bi-arrow-right-circle me-2"></i>
                <strong>Trasferimento:</strong> Il tecnico sarà automaticamente rimosso da "${centroAttuale}"
            `;
            
            this.elements.select.parentNode.appendChild(infoDiv);
        }
    },
    
    /**
     * Rimuove le informazioni sui trasferimenti
     */
    removeTransferInfo() {
        const infoEsistente = document.getElementById('infoTrasferimento');
        if (infoEsistente) {
            infoEsistente.remove();
        }
    },
    
    /**
     * Processa l'assegnazione del tecnico
     */
    async processAssegnazioneTecnico() {
        console.log('🎯 Inizio processo assegnazione tecnico');
        
        const tecnicoId = this.elements.select.value;
        if (!tecnicoId) {
            this.showNotification('Seleziona un tecnico da assegnare', 'warning');
            return;
        }
        
        // Ottieni informazioni tecnico selezionato
        const tecnicoInfo = this.getSelectedTecnicoInfo();
        
        // Chiedi conferma per trasferimenti
        if (tecnicoInfo.isTransfer && !await this.confirmTransfer(tecnicoInfo)) {
            return;
        }
        
        // Disabilita UI durante operazione
        this.setLoadingState(tecnicoInfo.isTransfer);
        
        try {
            // Prepara e invia richiesta
            const response = await this.sendAssignmentRequest(tecnicoId);
            
            if (response.success) {
                await this.handleAssignmentSuccess(response);
            } else {
                throw new Error(response.message || 'Errore nell\'operazione');
            }
            
        } catch (error) {
            console.error('❌ Errore assegnazione:', error);
            this.handleAssignmentError(error);
        } finally {
            this.resetLoadingState();
        }
    },
    
    /**
     * Ottiene informazioni sul tecnico selezionato
     * @returns {Object}
     */
    getSelectedTecnicoInfo() {
        const opzioneSelezionata = this.elements.select.options[this.elements.select.selectedIndex];
        const tipo = opzioneSelezionata.getAttribute('data-tipo');
        const nomeTecnico = opzioneSelezionata.text.split(' - ')[0].split(' (')[0];
        const centroAttuale = opzioneSelezionata.getAttribute('data-centro-attuale');
        
        return {
            id: this.elements.select.value,
            nome: nomeTecnico,
            tipo: tipo,
            centroAttuale: centroAttuale,
            isTransfer: tipo === 'trasferimento' && centroAttuale
        };
    },
    
    /**
     * Chiede conferma per i trasferimenti
     * @param {Object} tecnicoInfo - Informazioni tecnico
     * @returns {Promise<boolean>}
     */
    async confirmTransfer(tecnicoInfo) {
        const confermaMsg = `TRASFERIMENTO TECNICO\n\n` +
                           `Tecnico: ${tecnicoInfo.nome}\n` +
                           `Da: ${tecnicoInfo.centroAttuale}\n` +
                           `Al centro corrente\n\n` +
                           `Il tecnico sarà automaticamente rimosso dal centro precedente.\n\n` +
                           `Confermi il trasferimento?`;
        
        return confirm(confermaMsg);
    },
    
    /**
     * Invia la richiesta di assegnazione
     * @param {string} tecnicoId - ID del tecnico
     * @returns {Promise<Object>}
     */
    async sendAssignmentRequest(tecnicoId) {
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', this.config.csrfToken);
        
        const response = await fetch(this.elements.form.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            throw new Error(errorData.message || `HTTP ${response.status}`);
        }
        
        return await response.json();
    },
    
    /**
     * Gestisce il successo dell'assegnazione
     * @param {Object} response - Risposta del server
     */
    async handleAssignmentSuccess(response) {
        console.log('✅ Assegnazione completata:', response);
        
        // Mostra messaggio principale
        this.showNotification(response.message || 'Operazione completata con successo', 'success');
        
        // Info aggiuntive per trasferimenti
        if (response.is_transfer && response.previous_center) {
            setTimeout(() => {
                this.showNotification(
                    `Tecnico rimosso automaticamente da "${response.previous_center}"`, 
                    'info'
                );
            }, 1500);
        }
        
        // Chiudi modal e ricarica pagina
        setTimeout(() => {
            this.closeModal();
            window.location.reload();
        }, 3000);
    },
    
    /**
     * Gestisce gli errori di assegnazione
     * @param {Error} error - Errore
     */
    handleAssignmentError(error) {
        let messaggioErrore = 'Errore nell\'operazione';
        const errorMsg = error.message.toLowerCase();
        
        if (errorMsg.includes('già assegnato')) {
            messaggioErrore = 'Tecnico già assegnato a questo centro';
        } else if (errorMsg.includes('403') || errorMsg.includes('non autorizzato')) {
            messaggioErrore = 'Non hai i permessi per questa operazione';
        } else if (errorMsg.includes('422') || errorMsg.includes('non validi')) {
            messaggioErrore = 'Dati non validi';
        } else if (errorMsg.includes('500')) {
            messaggioErrore = 'Errore del server';
        }
        
        this.showNotification(messaggioErrore + ': ' + error.message, 'danger');
    },
    
    /**
     * Imposta lo stato di caricamento
     * @param {boolean} isTransfer - Se è un trasferimento
     */
    setLoadingState(isTransfer) {
        this.elements.select.disabled = true;
        this.elements.button.disabled = true;
        this.elements.button.originalText = this.elements.button.innerHTML;
        
        if (isTransfer) {
            this.elements.button.innerHTML = '<i class="bi bi-arrow-right me-1"></i> Trasferimento...';
        } else {
            this.elements.button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Assegnazione...';
        }
    },
    
    /**
     * Ripristina lo stato dopo caricamento
     */
    resetLoadingState() {
        this.elements.select.disabled = false;
        this.elements.button.disabled = false;
        if (this.elements.button.originalText) {
            this.elements.button.innerHTML = this.elements.button.originalText;
        }
    },
    
    /**
     * Reset dell'interfaccia select
     */
    resetSelectUI() {
        this.elements.select.innerHTML = '<option value="">⏳ Caricamento...</option>';
        this.elements.select.disabled = true;
    },
    
    /**
     * Mostra errore nella select
     * @param {string} messaggio - Messaggio di errore
     */
    showSelectError(messaggio) {
        this.elements.select.innerHTML = `<option value="">❌ ${messaggio}</option>`;
    },
    
    /**
     * Abilita la select
     */
    enableSelect() {
        this.elements.select.disabled = false;
    },
    
    /**
     * Disabilita il pulsante
     */
    disableButton() {
        this.elements.button.disabled = true;
    },
    
    /**
     * Abilita il pulsante
     */
    enableButton() {
        this.elements.button.disabled = false;
    },
    
    /**
     * Chiude il modal
     */
    closeModal() {
        const modalInstance = bootstrap.Modal.getInstance(this.elements.modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    },
    
    /**
     * Mostra notifica temporanea
     * @param {string} messaggio - Messaggio da mostrare
     * @param {string} tipo - Tipo di notifica (success, danger, warning, info)
     */
    showNotification(messaggio, tipo = 'info') {
        console.log(`📢 Notifica ${tipo.toUpperCase()}: ${messaggio}`);
        
        const tipiAlert = {
            'success': 'alert-success',
            'danger': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const icone = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-circle',
            'info': 'bi-info-circle'
        };
        
        const notifica = document.createElement('div');
        notifica.className = `alert ${tipiAlert[tipo]} alert-dismissible fade show`;
        notifica.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        notifica.innerHTML = `
            <i class="bi ${icone[tipo]} me-2"></i>
            ${messaggio}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notifica);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            if (notifica && notifica.parentNode) {
                notifica.remove();
            }
        }, 5000);
    },
    
    /**
     * Abilita modalità debug
     */
    enableDebugMode() {
        console.log('🔧 Modalità debug abilitata');
        
        // Aggiungi funzioni globali per debug
        window.AdminCentroShow = this;
        
        window.debugCentroShow = () => {
            console.log('🔧 DEBUG AdminCentroShow:');
            console.log('Config:', this.config);
            console.log('Elements:', this.elements);
            console.log('API URL:', `${this.config.baseUrl}/api/admin/centri/${this.config.centroId}/tecnici-disponibili`);
        };
        
        window.testModalOpen = () => {
            const modalInstance = new bootstrap.Modal(this.elements.modal);
            modalInstance.show();
        };
        
        console.log('💡 Funzioni debug disponibili: debugCentroShow(), testModalOpen()');
    }
};

/**
 * Utility per Google Maps
 */
const GoogleMapsUtil = {
    /**
     * Apre Google Maps con un indirizzo
     * @param {string} indirizzo - Indirizzo da cercare
     */
    openMaps(indirizzo) {
        const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(indirizzo)}`;
        window.open(url, '_blank');
        console.log('🗺️ Apertura Google Maps per:', indirizzo);
    }
};

/**
 * Inizializzazione automatica quando il DOM è pronto
 */
document.addEventListener('DOMContentLoaded', function() {
    // Verifica se siamo nella pagina corretta
    if (!window.AdminCentroShowConfig) {
        console.log('⏭️ AdminCentroShowConfig non trovato, skip inizializzazione');
        return;
    }
    
    // Inizializza il modulo
    const success = AdminCentroShow.init(window.AdminCentroShowConfig);
    
    if (success) {
        console.log('🎉 AdminCentroShow caricato con successo');
    }
});

// Esporta per uso globale
window.AdminCentroShow = AdminCentroShow;
window.GoogleMapsUtil = GoogleMapsUtil;