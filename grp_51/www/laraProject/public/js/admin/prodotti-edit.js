
document.addEventListener('DOMContentLoaded', function() {
     console.log('admin.prodotti.edit caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.prodotti.edit') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    
    // === CONFIGURAZIONE ===
    const form = document.getElementById('editProdottoForm');
    const submitBtn = document.getElementById('submitBtn');
    const fotoInput = document.getElementById('foto');
    
    // === ANTEPRIMA NUOVA IMMAGINE ===
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validazione file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato file non supportato. Usa JPEG, PNG, JPG o GIF.');
                    fotoInput.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('Il file è troppo grande. Massimo 2MB.');
                    fotoInput.value = '';
                    return;
                }
                
                // Crea anteprima
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentImg = document.getElementById('currentImage');
                    if (currentImg) {
                        currentImg.src = e.target.result;
                        // Aggiorna il testo sotto l'immagine
                        const caption = currentImg.nextElementSibling;
                        if (caption) {
                            caption.innerHTML = '<small class="text-success"><i class="bi bi-upload me-1"></i>Nuova foto (anteprima)</small>';
                        }
                    } else {
                        // Crea nuova anteprima se non esiste immagine attuale
                        const preview = document.createElement('div');
                        preview.className = 'mb-3';
                        preview.innerHTML = `
                            <div class="position-relative d-inline-block">
                                <img src="${e.target.result}" 
                                     alt="Anteprima nuova foto"
                                     class="img-thumbnail"
                                     style="max-height: 200px; max-width: 100%;"
                                     id="newImagePreview">
                                <div class="mt-2">
                                    <small class="text-success">
                                        <i class="bi bi-upload me-1"></i>Nuova foto (anteprima)
                                    </small>
                                </div>
                            </div>
                        `;
                        fotoInput.parentNode.insertBefore(preview, fotoInput);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // === VALIDAZIONE FORM ===
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            // Rimuovi classi di errore precedenti
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Controlla campi obbligatori
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            // Validazione prezzo
            const prezzoInput = document.getElementById('prezzo');
            if (prezzoInput && prezzoInput.value && parseFloat(prezzoInput.value) < 0) {
                prezzoInput.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Mostra messaggio di errore
                showNotification('error', 'Compila tutti i campi obbligatori correttamente');
                
                // Scroll al primo campo con errore
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                
                return false;
            }
            
            // Mostra stato di caricamento
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
            }
            
            // Conferma salvataggio
            if (!confirm('Salvare le modifiche al prodotto?')) {
                e.preventDefault();
                resetSubmitButton();
                return false;
            }
        });
    }
    
    // === FUNZIONI HELPER ===
    
    function resetSubmitButton() {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-loading');
            submitBtn.innerHTML = '<i class="bi bi-check me-1"></i>Salva Modifiche';
        }
    }
    
    // === GESTIONE MODIFICHE NON SALVATE ===
    let hasUnsavedChanges = false;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        const originalValue = input.value || input.checked;
        
        input.addEventListener('change', function() {
            const currentValue = this.type === 'checkbox' ? this.checked : this.value;
            hasUnsavedChanges = (currentValue !== originalValue);
        });
    });
    
    // Avviso prima di uscire con modifiche non salvate
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler uscire?';
            return e.returnValue;
        }
    });
    
    // Reset flag quando si salva
    form.addEventListener('submit', function() {
        hasUnsavedChanges = false;
    });
    
    // === AUTO-SAVE IN SESSION STORAGE ===
    function autoSave() {
        const formData = {};
        formInputs.forEach(input => {
            if (input.type === 'checkbox') {
                formData[input.name] = input.checked;
            } else if (input.type !== 'file') {
                formData[input.name] = input.value;
            }
        });
        
        sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(formData));
    }
    
    // Salva automaticamente ogni 30 secondi
    setInterval(autoSave, 30000);
    
    // Carica dati salvati se esistono
    const savedData = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}');
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            
            // Chiedi se ripristinare i dati
            if (confirm('Sono stati trovati dati non salvati. Vuoi ripristinarli?')) {
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = data[key];
                        } else {
                            input.value = data[key];
                        }
                    }
                });
                
                showNotification('info', 'Dati ripristinati dalla sessione precedente');
            } else {
                // Pulisci i dati salvati se l'utente non li vuole
                sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
            }
        } catch (e) {
            console.warn('Errore nel ripristino dati auto-salvati:', e);
            sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        }
    }
    
    // === CONTATORE CARATTERI PER TEXTAREA ===
    const textareas = form.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.maxLength;
        if (maxLength > 0) {
            // Crea contatore
            const counter = document.createElement('small');
            counter.className = 'form-text text-muted';
            counter.innerHTML = `<span id="${textarea.id}_count">${textarea.value.length}</span>/${maxLength} caratteri`;
            
            textarea.parentNode.appendChild(counter);
            
            // Aggiorna contatore
            textarea.addEventListener('input', function() {
                const count = this.value.length;
                const countSpan = document.getElementById(`${this.id}_count`);
                if (countSpan) {
                    countSpan.textContent = count;
                    countSpan.parentElement.className = count > maxLength * 0.9 
                        ? 'form-text text-warning' 
                        : 'form-text text-muted';
                }
            });
        }
    });
    
    // === SHORTCUTS TASTIERA ===
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S per salvare
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.submit();
        }
        
        // Ctrl/Cmd + K per focus sulla ricerca (se presente)
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const firstInput = form.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
        }
        
        // Escape per tornare indietro
        if (e.key === 'Escape') {
            if (hasUnsavedChanges) {
                if (confirm('Hai modifiche non salvate. Tornare indietro?')) {
                    window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
                }
            } else {
                window.location.href = '{{ route("admin.prodotti.show", $prodotto) }}';
            }
        }
    });
    
    // === VALIDAZIONE IN TEMPO REALE ===
    
    // Modello univoco - validazione semplificata
    const modelloInput = document.getElementById('modello');
    if (modelloInput) {
        modelloInput.addEventListener('input', function() {
            const modello = this.value.trim();
            
            // Rimuovi caratteri non validi
            if (modello) {
                // Validazione formato modello (lettere, numeri, trattini)
                if (!/^[a-zA-Z0-9\-_]+$/.test(modello)) {
                    this.classList.add('is-invalid');
                    showNotification('warning', 'Il modello può contenere solo lettere, numeri e trattini');
                } else {
                    this.classList.remove('is-invalid');
                }
            }
        });
    }
    
    // === TOOLTIPS E POPOVERS ===
    
    // Inizializza tooltips Bootstrap se disponibile
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
    
    // === NOTIFICAZIONI ===
    // Gestisci le notifiche lato Blade e passa i dati a JavaScript tramite variabili globali, ad esempio:
    // window.LaravelNotifications = { success: "...", error: "...", errors: ["..."] };

    if (window.LaravelNotifications) {
        if (window.LaravelNotifications.success) {
            showNotification('success', window.LaravelNotifications.success);
            // Pulisci auto-save se successo
            sessionStorage.removeItem('editProdotto_' + window.LaravelApp.prodottoId);
        }
        if (window.LaravelNotifications.error) {
            showNotification('error', window.LaravelNotifications.error);
        }
        if (Array.isArray(window.LaravelNotifications.errors)) {
            window.LaravelNotifications.errors.forEach(function(error) {
                showNotification('error', error);
            });
        }
    }
    
    console.log('🎉 Form modifica prodotto inizializzato correttamente');
});

// === FUNZIONI GLOBALI ===

/**
 * Sistema di notificazioni
 */
function showNotification(type, message, duration = 5000) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'check-circle' : 
                         type === 'error' ? 'exclamation-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const bgClass = type === 'success' ? 'success' : 
                       type === 'error' ? 'danger' : 
                       type === 'warning' ? 'warning' : 'info';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });
        
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    } else {
        // Fallback ad alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Conferma prima di uscire dalla pagina
 */
function confirmExit(message = 'Hai modifiche non salvate. Sei sicuro di voler uscire?') {
    const hasUnsavedChanges = sessionStorage.getItem('editProdotto_{{ $prodotto->id }}') !== null;
    
    if (hasUnsavedChanges) {
        return confirm(message);
    }
    
    return true;
}

/**
 * Anteprima immagine da URL
 */
function previewImageFromUrl(url, targetId = 'currentImage') {
    const img = document.getElementById(targetId);
    if (img) {
        img.src = url;
        
        // Aggiungi effetto di caricamento
        img.style.opacity = '0.5';
        img.onload = function() {
            this.style.opacity = '1';
        };
    }
}

/**
 * Reset form ai valori originali
 */
function resetForm() {
    if (confirm('Ripristinare tutti i campi ai valori originali?')) {
        document.getElementById('editProdottoForm').reset();
        
        // Ripristina immagine originale se presente
        const currentImg = document.getElementById('currentImage');
        if (currentImg) {
            currentImg.src = '{{ $prodotto->foto ? asset("storage/" . $prodotto->foto) : "" }}';
        }
        
        // Pulisci auto-save
        sessionStorage.removeItem('editProdotto_{{ $prodotto->id }}');
        
        showNotification('info', 'Form ripristinato ai valori originali');
    }
}

/**
 * Salva bozza
 */
function salvaBozza() {
    const form = document.getElementById('editProdottoForm');
    const formData = new FormData(form);
    
    // Converti in oggetto per storage
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (key !== '_token' && key !== '_method' && key !== 'foto') {
            data[key] = value;
        }
    }
    
    sessionStorage.setItem('editProdotto_{{ $prodotto->id }}', JSON.stringify(data));
    showNotification('success', 'Bozza salvata automaticamente');
}

/**
 * Controllo accessibilità
 */
function checkAccessibility() {
    const form = document.getElementById('editProdottoForm');
    const issues = [];
    
    // Controlla labels
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        const label = form.querySelector(`label[for="${input.id}"]`);
        if (!label && !input.getAttribute('aria-label')) {
            issues.push(`Input ${input.name} senza label`);
        }
    });
    
    // Controlla required fields
    const required = form.querySelectorAll('[required]');
    required.forEach(field => {
        const label = form.querySelector(`label[for="${field.id}"]`);
        if (label && !label.textContent.includes('*')) {
            issues.push(`Campo obbligatorio ${field.name} non indicato visivamente`);
        }
    });
    
    if (issues.length > 0) {
        console.warn('Problemi di accessibilità trovati:', issues);
    } else {
        console.log('✅ Nessun problema di accessibilità rilevato');
    }
    
    return issues;
}

// Esegui controllo accessibilità in ambiente di sviluppo
if (window.LaravelApp && window.LaravelApp.debug) {
    setTimeout(checkAccessibility, 1000);
}

/**
 * Debug informazioni form
 */
function debugFormInfo() {
    const form = document.getElementById('editProdottoForm');
    
    console.group('🔧 Debug Form Modifica Prodotto');
    console.log('Form element:', form);
    console.log('Prodotto ID:', window.LaravelApp?.prodottoId);
    console.log('Prodotto Nome:', window.LaravelApp?.prodottoNome);
    console.log('Action URL:', form.action);
    console.log('Method:', form.method);
    
    // Campi form
    const formData = new FormData(form);
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    // Validazione
    const required = form.querySelectorAll('[required]');
    console.log('Campi obbligatori:', required.length);
    
    // Auto-save status
    const autoSaveData = sessionStorage.getItem('editProdotto_' + (window.LaravelApp?.prodottoId || ''));
    console.log('Auto-save data presente:', !!autoSaveData);
    
    console.groupEnd();
}

// Attiva debug con Ctrl+Shift+D
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        e.preventDefault();
        debugFormInfo();
    }
});
   
