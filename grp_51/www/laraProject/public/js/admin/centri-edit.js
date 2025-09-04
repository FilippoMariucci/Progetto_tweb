

$(document).ready(function() {
    console.log('admin.centri.edit caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.edit') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
});
    // Il tuo codice JavaScript qui...

    document.addEventListener('DOMContentLoaded', function() {
    console.log('📝 Script modifica centro caricato - VERSIONE CORRETTA');
    
    // === CONTATORE CARATTERI PER IL NOME ===
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    if (nomeInput && nomeCounter) {
        function updateNomeCounter() {
            const currentLength = nomeInput.value.length;
            nomeCounter.textContent = currentLength;
            
            // Cambia colore in base alla lunghezza
            if (currentLength > 240) {
                nomeCounter.className = 'text-danger fw-bold';
            } else if (currentLength > 200) {
                nomeCounter.className = 'text-warning';
            } else {
                nomeCounter.className = 'text-muted';
            }
        }
        
        // Event listener per aggiornamento contatore
        nomeInput.addEventListener('input', updateNomeCounter);
        updateNomeCounter(); // Inizializzazione
    }
    
    // === VALIDAZIONE CAP (solo numeri, 5 cifre) ===
    const capInput = document.getElementById('cap');
    if (capInput) {
        capInput.addEventListener('input', function(e) {
            // Rimuove tutti i caratteri non numerici
            let value = e.target.value.replace(/\D/g, '');
            
            // Limita a 5 cifre
            if (value.length > 5) {
                value = value.substr(0, 5);
            }
            e.target.value = value;
        });
    }
    
    // === VALIDAZIONE TELEFONO (permette numeri, spazi, +, -, (), ) ===
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            // Mantiene solo caratteri validi per numeri di telefono
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
        });
    }
    
    // === VALIDAZIONE FORM MODIFICA ===
    const form = document.getElementById('formModificaCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        form.addEventListener('submit', function(e) {
            console.log('🚀 Invio form modifica centro...');
            
            // Mostra spinner durante l'invio per feedback visivo
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            // === VALIDAZIONE CAMPI OBBLIGATORI ===
            const nome = document.getElementById('nome').value.trim();
            const indirizzo = document.getElementById('indirizzo').value.trim();
            const citta = document.getElementById('citta').value.trim();
            const provincia = document.getElementById('provincia').value;
            
            // Controlla che tutti i campi obbligatori siano compilati
            if (!nome || !indirizzo || !citta || !provincia) {
                e.preventDefault(); // Ferma l'invio del form
                
                // Ripristina pulsante
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                showAlert('Errore', 'Compila tutti i campi obbligatori (contrassegnati con *)', 'danger');
                return;
            }
            
            // === VALIDAZIONE CAP (se presente) ===
            const cap = document.getElementById('cap').value.trim();
            if (cap && (cap.length !== 5 || !/^\d{5}$/.test(cap))) {
                e.preventDefault();
                
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                showAlert('Errore', 'Il CAP deve essere di 5 cifre numeriche', 'danger');
                return;
            }
            
            // === VALIDAZIONE EMAIL (se presente) ===
            const email = document.getElementById('email').value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                
                showAlert('Errore', 'Inserisci un indirizzo email valido', 'danger');
                return;
            }
            
            console.log('✅ Validazione superata, invio in corso...');
        });
    }
    
    // === CONFERMA ELIMINAZIONE CENTRO ===
    const formElimina = document.getElementById('formElimina');
    if (formElimina) {
        formElimina.addEventListener('submit', function(e) {
            const conferma = confirm(
                'ATTENZIONE: ELIMINAZIONE DEFINITIVA\n\n' +
                'Sei sicuro di voler eliminare questo centro di assistenza?\n\n' +
                '⚠️ Questa azione è IRREVERSIBILE!\n' +
                '⚠️ Il centro sarà eliminato definitivamente dal sistema!\n\n' +
                'Digitare "ELIMINA" per confermare'
            );
            
            if (!conferma) {
                e.preventDefault(); // Annulla l'eliminazione
                console.log('❌ Eliminazione centro annullata dall\'utente');
            } else {
                console.log('🗑️ Eliminazione centro confermata');
            }
        });
    }
    
    // === FUNZIONE PER COPIARE L'INDIRIZZO NEGLI APPUNTI ===
    window.copiaIndirizzo = function() {
        // Costruisce l'indirizzo completo
        const indirizzo = '{{ $centro->indirizzo }}, {{ $centro->citta }}' +
                         '{{ $centro->cap ? " " . $centro->cap : "" }}' +
                         '{{ $centro->provincia ? " (" . strtoupper($centro->provincia) . ")" : "" }}';
        
        // Tentativo con API moderna
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(indirizzo).then(function() {
                showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
                console.log('📋 Indirizzo copiato:', indirizzo);
            }).catch(function(err) {
                console.error('❌ Errore clipboard API:', err);
                fallbackCopyText(indirizzo); // Fallback per browser vecchi
            });
        } else {
            fallbackCopyText(indirizzo); // Fallback per browser non supportati
        }
    };
    
    // === FALLBACK PER COPIA TESTO (browser più vecchi) ===
    function fallbackCopyText(text) {
        // Crea un elemento textarea temporaneo
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            // Usa il comando deprecato ma ancora supportato
            const successful = document.execCommand('copy');
            if (successful) {
                showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
                console.log('📋 Indirizzo copiato (fallback):', text);
            } else {
                showAlert('Errore', 'Impossibile copiare il testo', 'danger');
            }
        } catch (err) {
            console.error('❌ Errore fallback copy:', err);
            showAlert('Errore', 'Impossibile copiare il testo', 'danger');
        }
        
        document.body.removeChild(textArea);
    }
    
    // === FUNZIONE PER MOSTRARE ALERT TEMPORANEI ===
    function showAlert(title, message, type = 'info') {
        // Rimuove alert precedenti dello stesso tipo
        const existingAlerts = document.querySelectorAll('.alert-temp');
        existingAlerts.forEach(alert => alert.remove());
        
        // Crea nuovo elemento alert
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show alert-temp`;
        alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        alertContainer.innerHTML = `
            <i class="bi ${getIconForType(type)} me-2"></i>
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        `;
        
        // Aggiungi al DOM
        document.body.appendChild(alertContainer);
        
        console.log(`🔔 Alert ${type}: ${title} - ${message}`);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            if (alertContainer && alertContainer.parentNode) {
                alertContainer.remove();
            }
        }, 5000);
    }
    
    // === HELPER: ICONE PER TIPO DI ALERT ===
    function getIconForType(type) {
        const icons = {
            'success': 'bi-check-circle-fill',
            'danger': 'bi-exclamation-triangle-fill',
            'warning': 'bi-exclamation-circle-fill',
            'info': 'bi-info-circle-fill'
        };
        return icons[type] || 'bi-info-circle';
    }
    
    // === RILEVAMENTO MODIFICHE NON SALVATE ===
    let formModificato = false;
    
    // Monitora tutti i campi input del form
    const inputs = form ? form.querySelectorAll('input, select, textarea') : [];
    
    inputs.forEach(input => {
        // Salva il valore originale per confronto
        const originalValue = input.value;
        
        input.addEventListener('change', function() {
            // Controlla se il valore è diverso dall'originale
            if (input.value !== originalValue) {
                formModificato = true;
                console.log('📝 Form modificato - campo:', input.name);
            }
        });
        
        input.addEventListener('input', function() {
            if (input.value !== originalValue) {
                formModificato = true;
            }
        });
    });
    
    // === AVVISO PRIMA DI LASCIARE LA PAGINA ===
    window.addEventListener('beforeunload', function(e) {
        if (formModificato) {
            console.log('⚠️ Tentativo di lasciare pagina con modifiche non salvate');
            
            const message = 'Hai modifiche non salvate. Sei sicuro di voler lasciare la pagina?';
            e.preventDefault();
            e.returnValue = message;
            return message;
        }
    });
    
    // === RESET FLAG QUANDO IL FORM VIENE INVIATO ===
    if (form) {
        form.addEventListener('submit', function() {
            formModificato = false;
            console.log('📤 Form inviato, flag modifiche resettato');
        });
    }
    
    // === AUTO-FOCUS SUL PRIMO CAMPO ===
    const primoInput = document.getElementById('nome');
    if (primoInput) {
        // Posiziona il cursore alla fine del testo esistente
        primoInput.focus();
        primoInput.setSelectionRange(primoInput.value.length, primoInput.value.length);
    }
    
    console.log('✅ Script modifica centro completamente inizializzato');
});
