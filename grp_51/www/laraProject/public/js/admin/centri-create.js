
    $(document).ready(function() {
    console.log('admin.centri.create caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.centri.create') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
});
    
    
    // Il tuo codice JavaScript qui...
    document.addEventListener('DOMContentLoaded', function() {
    console.log('🏗️ Inizializzazione form creazione centro - TUTTI CAMPI OBBLIGATORI');
    
    // === CONTATORE CARATTERI PER IL NOME ===
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    if (nomeInput && nomeCounter) {
        function updateNomeCounter() {
            const currentLength = nomeInput.value.length;
            nomeCounter.textContent = currentLength;
            
            // Cambia colore in base alla lunghezza
            if (currentLength === 0) {
                nomeCounter.className = 'text-danger fw-bold';  // Rosso se vuoto
            } else if (currentLength > 240) {
                nomeCounter.className = 'text-danger fw-bold';  // Rosso se troppo lungo
            } else if (currentLength > 200) {
                nomeCounter.className = 'text-warning';        // Arancione se quasi pieno
            } else {
                nomeCounter.className = 'text-success';        // Verde se OK
            }
        }
        
        nomeInput.addEventListener('input', updateNomeCounter);
        updateNomeCounter(); // Inizializza
    }
    
    // === VALIDAZIONE CAP RIGOROSA ===
    const capInput = document.getElementById('cap');
    if (capInput) {
        capInput.addEventListener('input', function(e) {
            // Rimuove caratteri non numerici
            let value = e.target.value.replace(/\D/g, '');
            
            // Limita a 5 caratteri
            if (value.length > 5) {
                value = value.substr(0, 5);
            }
            
            e.target.value = value;
            
            // Validazione visiva in tempo reale
            if (value.length === 5) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else if (value.length > 0) {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // === VALIDAZIONE TELEFONO RIGOROSA ===
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            // Mantiene solo numeri, spazi, +, -, ()
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
            
            // Validazione visiva per telefono
            const phonePattern = /^[\+]?[\d\s\-\(\)]{8,20}$/;
            if (value.length > 0) {
                if (phonePattern.test(value) && value.length >= 8) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // === VALIDAZIONE EMAIL IN TEMPO REALE ===
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function(e) {
            const email = e.target.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length > 0) {
                if (emailPattern.test(email)) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            } else {
                e.target.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // === VALIDAZIONE COMPLETA FORM ===
    const form = document.getElementById('formCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        form.addEventListener('submit', function(e) {
            console.log('📤 Tentativo invio form...');
            
            // Raccoglie tutti i valori
            const formData = {
                nome: document.getElementById('nome').value.trim(),
                indirizzo: document.getElementById('indirizzo').value.trim(),
                citta: document.getElementById('citta').value.trim(),
                provincia: document.getElementById('provincia').value,
                cap: document.getElementById('cap').value.trim(),
                telefono: document.getElementById('telefono').value.trim(),
                email: document.getElementById('email').value.trim()
            };
            
            const errors = [];
            
            // VALIDAZIONE RIGOROSA DI TUTTI I CAMPI OBBLIGATORI
            if (!formData.nome) {
                errors.push('Nome centro è obbligatorio');
            } else if (formData.nome.length < 3) {
                errors.push('Nome centro deve avere almeno 3 caratteri');
            }
            
            if (!formData.indirizzo) {
                errors.push('Indirizzo è obbligatorio');
            } else if (formData.indirizzo.length < 5) {
                errors.push('Indirizzo deve essere completo (minimo 5 caratteri)');
            }
            
            if (!formData.citta) {
                errors.push('Città è obbligatoria');
            } else if (formData.citta.length < 2) {
                errors.push('Nome città troppo breve');
            }
            
            if (!formData.provincia) {
                errors.push('Provincia è obbligatoria');
            }
            
            if (!formData.cap) {
                errors.push('CAP è obbligatorio');
            } else if (!/^\d{5}$/.test(formData.cap)) {
                errors.push('CAP deve essere composto da esattamente 5 cifre');
            }
            
            if (!formData.telefono) {
                errors.push('Telefono è obbligatorio');
            } else if (formData.telefono.length < 8) {
                errors.push('Numero di telefono troppo breve');
            }
            
            if (!formData.email) {
                errors.push('Email è obbligatoria');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                errors.push('Formato email non valido');
            }
            
            // Se ci sono errori, blocca l'invio
            if (errors.length > 0) {
                e.preventDefault();
                console.log('❌ Validazione fallita:', errors);
                
                showValidationErrors(errors);
                return false;
            }
            
            // Mostra spinner durante l'invio
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creazione in corso...';
            
            console.log('✅ Validazione superata, invio form...');
        });
    }
    
    // === FUNZIONE PER MOSTRARE ERRORI DI VALIDAZIONE ===
    function showValidationErrors(errors) {
        // Rimuove eventuali alert precedenti
        const existingAlert = document.querySelector('.validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Crea nuovo alert con tutti gli errori
        const alertContainer = document.createElement('div');
        alertContainer.className = 'alert alert-danger alert-dismissible fade show validation-alert';
        
        let errorsHtml = '<ul class="mb-0">';
        errors.forEach(error => {
            errorsHtml += `<li>${error}</li>`;
        });
        errorsHtml += '</ul>';
        
        alertContainer.innerHTML = `
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Errori di Validazione</h6>
            <p class="mb-2">Correggi i seguenti errori prima di continuare:</p>
            ${errorsHtml}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Inserisce l'alert all'inizio del form
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertBefore(alertContainer, cardBody.firstChild);
            
            // Scrolla verso l'alert
            alertContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    // === CONTROLLO CAMPI IN TEMPO REALE ===
    function checkFormCompletion() {
        const allFields = [
            document.getElementById('nome'),
            document.getElementById('indirizzo'),
            document.getElementById('citta'),
            document.getElementById('provincia'),
            document.getElementById('cap'),
            document.getElementById('telefono'),
            document.getElementById('email')
        ];
        
        const filledFields = allFields.filter(field => 
            field && field.value.trim().length > 0
        ).length;
        
        const progress = (filledFields / allFields.length) * 100;
        
        // Aggiorna lo stato del pulsante
        if (btnSalva) {
            if (progress === 100) {
                btnSalva.classList.remove('btn-outline-primary');
                btnSalva.classList.add('btn-primary');
                btnSalva.disabled = false;
            } else {
                btnSalva.classList.remove('btn-primary');
                btnSalva.classList.add('btn-outline-primary');
            }
        }
        
        console.log(`📊 Completamento form: ${progress.toFixed(0)}% (${filledFields}/${allFields.length})`);
    }
    
    // Monitora cambiamenti in tutti i campi
    const allInputs = document.querySelectorAll('input, select');
    allInputs.forEach(input => {
        input.addEventListener('input', checkFormCompletion);
        input.addEventListener('change', checkFormCompletion);
    });
    
    // === AUTO-FOCUS E INIZIALIZZAZIONE ===
    const primoInput = document.getElementById('nome');
    if (primoInput) {
        primoInput.focus();
        
        // Mostra suggerimento se il campo è vuoto
        if (!primoInput.value) {
            setTimeout(() => {
                if (!primoInput.value) {
                    primoInput.placeholder = '✏️ Inizia digitando il nome del centro...';
                }
            }, 2000);
        }
    }
    
    // Controllo iniziale completamento
    checkFormCompletion();
    
    console.log('✅ Form creazione centro inizializzato - Validazione completa attiva');

}); // <-- chiude addEventListener
