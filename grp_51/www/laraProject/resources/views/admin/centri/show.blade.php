{{-- 
    Vista lineare per la gestione di un centro di assistenza (Admin)
    File: resources/views/admin/centri/show.blade.php
    
    Layout semplificato e lineare per migliorare l'usabilità
--}}

@extends('layouts.app')

@section('title', 'Centro: ' . $centro->nome)

{{-- Nasconde il breadcrumb per dare più spazio --}}
@push('breadcrumb-override')
<style>
.breadcrumb, nav[aria-label="breadcrumb"] {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    {{-- Header principale con info base --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">{{ $centro->nome }}</h1>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $centro->indirizzo }}, {{ $centro->citta }} ({{ $centro->provincia }})
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    {{-- Stato centro e conteggio tecnici --}}
                    @if($centro->tecnici->count() > 0)
                        <span class="badge bg-success fs-6">Centro Attivo</span>
                    @else
                        <span class="badge bg-warning fs-6">Centro Inattivo</span>
                    @endif
                    <div class="mt-1">
                        <small>{{ $centro->tecnici->count() }} Tecnici Assegnati</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezione contatti e azioni rapide in una riga --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Informazioni Contatto</h5>
                </div>
                <div class="card-body">
                    {{-- Mostra contatti se disponibili --}}
                    @if($centro->telefono)
                        <p class="mb-2">
                            <strong>Telefono:</strong>
                            <a href="tel:{{ $centro->telefono }}" class="ms-2">{{ $centro->telefono }}</a>
                        </p>
                    @endif
                    
                    @if($centro->email)
                        <p class="mb-2">
                            <strong>Email:</strong>
                            <a href="mailto:{{ $centro->email }}" class="ms-2">{{ $centro->email }}</a>
                        </p>
                    @endif
                    
                    @if($centro->cap)
                        <p class="mb-0">
                            <strong>CAP:</strong> <span class="ms-2">{{ $centro->cap }}</span>
                        </p>
                    @endif
                    
                    {{-- Se non ci sono contatti --}}
                    @if(!$centro->telefono && !$centro->email)
                        <p class="text-muted mb-0">Nessun contatto disponibile</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Azioni Rapide</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Modifica centro --}}
                        <a href="{{ route('admin.centri.edit', $centro) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Modifica Centro
                        </a>
                        
                        {{-- Visualizza su mappa --}}
                        <button type="button" class="btn btn-success" onclick="apriGoogleMaps()">
                            <i class="bi bi-map me-1"></i> Visualizza su Maps
                        </button>
                        
                        {{-- Torna alla lista --}}
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Torna alla Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezione tecnici - La più importante --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Tecnici del Centro ({{ $centro->tecnici->count() }})
                    </h4>
                </div>
                <div class="col-auto">
                    {{-- Pulsante per aggiungere tecnico --}}
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                        <i class="bi bi-plus-circle me-1"></i> Aggiungi Tecnico
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if($centro->tecnici->isNotEmpty())
                {{-- Lista tecnici in formato semplice --}}
                <div class="row">
                    @foreach($centro->tecnici as $tecnico)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        {{-- Nome e info tecnico --}}
                                        <h6 class="mb-1">{{ $tecnico->nome_completo }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="bi bi-wrench me-1"></i>
                                            {{ $tecnico->specializzazione ?? 'Specializzazione non specificata' }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-calendar me-1"></i>
                                            Età: {{ $tecnico->eta ?? 'N/A' }} anni
                                        </p>
                                    </div>
                                    <div class="col-auto">
                                        {{-- Azioni sul tecnico --}}
                                        <div class="btn-group-vertical" role="group">
                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Visualizza
                                            </a>
                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Modifica
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-rimuovi-tecnico"
                                                    data-tecnico-id="{{ $tecnico->id }}"
                                                    data-tecnico-nome="{{ $tecnico->nome_completo }}">
                                                <i class="bi bi-trash"></i> Rimuovi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Nessun tecnico assegnato --}}
                <div class="text-center py-5">
                    <i class="bi bi-people display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">Nessun Tecnico Assegnato</h5>
                    <p class="text-muted mb-4">Questo centro non ha ancora tecnici assegnati.</p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                        <i class="bi bi-plus-circle me-1"></i> Aggiungi Primo Tecnico
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Altri centri nella stessa provincia (se esistono) --}}
    @if(isset($centriVicini) && $centriVicini->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-geo me-2"></i>
                    Altri Centri in {{ $centro->provincia }} ({{ $centriVicini->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($centriVicini as $centroVicino)
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $centroVicino->nome }}</strong><br>
                                        <small class="text-muted">{{ $centroVicino->citta }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info">{{ $centroVicino->tecnici_count }} tecnici</span>
                                        <a href="{{ route('admin.centri.show', $centroVicino) }}" 
                                           class="btn btn-outline-secondary btn-sm d-block mt-1">
                                            Visualizza
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal per assegnazione tecnico --}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Tecnico al Centro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info centro nel modal --}}
                <div class="alert alert-info">
                    <strong>Centro:</strong> {{ $centro->nome }}<br>
                    <strong>Località:</strong> {{ $centro->indirizzo }}, {{ $centro->citta }}
                </div>
                
                {{-- Form assegnazione --}}
                <form id="formAssegnaTecnico" action="{{ route('admin.centri.assegna-tecnico', $centro) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label required">Seleziona Tecnico</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select" required>
                            <option value="">Caricamento tecnici disponibili...</option>
                        </select>
                        <div class="form-text">
                            Vengono mostrati sia i tecnici non assegnati che quelli trasferibili da altri centri.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="submit" form="formAssegnaTecnico" id="btnAssegnaTecnico" class="btn btn-success" disabled>
                    <i class="bi bi-check-circle me-1"></i> Assegna Tecnico
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal di conferma eliminazione centro --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Conferma Eliminazione Centro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Questa azione è irreversibile.
                </div>
                <p>Sei sicuro di voler eliminare il centro assistenza:</p>
                <p class="fw-bold text-danger" id="centro-name">Nome centro</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Nota:</strong> L'eliminazione rimuoverà anche i riferimenti 
                    ai tecnici associati a questo centro.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annulla
                </button>
                <form id="delete-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina Centro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Contenitore per notifiche temporanee --}}
<div id="notificheContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

@endsection

{{-- JavaScript semplificato --}}
@push('scripts')
<script>
/**
 * JavaScript per gestione centro assistenza - Versione semplificata
 * Gestisce assegnazione/rimozione tecnici e azioni base
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('📍 Inizializzazione pagina centro assistenza');
    
    // === VARIABILI GLOBALI ===
    const CENTRO_ID = {{ $centro->id }};
    const BASE_URL = '{{ url("/") }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Elementi del DOM
    const modalAssegnazione = document.getElementById('modalAssegnaTecnico');
    const selectTecnico = document.getElementById('tecnico_id');
    const btnAssegnaTecnico = document.getElementById('btnAssegnaTecnico');
    const formAssegnazione = document.getElementById('formAssegnaTecnico');
    
    // === INIZIALIZZAZIONE ===
    inizializzaEventHandlers();
    
    /**
     * Configura tutti gli event handler della pagina
     */
    function inizializzaEventHandlers() {
        // Carica tecnici quando si apre il modal
        if (modalAssegnazione) {
            modalAssegnazione.addEventListener('shown.bs.modal', caricaTecniciDisponibili);
        }
        
        // Gestione form assegnazione
        if (formAssegnazione) {
            formAssegnazione.addEventListener('submit', gestisciAssegnazioneTecnico);
        }
        
        // Pulsanti rimozione tecnici
        document.querySelectorAll('.btn-rimuovi-tecnico').forEach(btn => {
            btn.addEventListener('click', gestisciRimozioneTecnico);
        });
    }
    
    /**
     * Carica la lista dei tecnici disponibili per l'assegnazione
     */
    function caricaTecniciDisponibili() {
        console.log('🔄 Caricamento tecnici disponibili...');
        
        // Reset select
        selectTecnico.innerHTML = '<option value="">Caricamento...</option>';
        selectTecnico.disabled = true;
        btnAssegnaTecnico.disabled = true;
        
        // URL API per tecnici disponibili
        const apiUrl = `${BASE_URL}/api/admin/centri/${CENTRO_ID}/tecnici-disponibili`;
        
        // Chiamata API
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Errore HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                popolaSelectTecnici(data.tecnici || []);
                console.log('✅ Tecnici caricati:', data.tecnici.length);
            } else {
                throw new Error(data.message || 'Errore nel caricamento');
            }
        })
        .catch(error => {
            console.error('❌ Errore caricamento tecnici:', error);
            selectTecnico.innerHTML = '<option value="">Errore nel caricamento</option>';
            mostraNotifica('Errore nel caricamento dei tecnici: ' + error.message, 'danger');
        })
        .finally(() => {
            selectTecnico.disabled = false;
        });
    }
    
    /**
     * Popola la select con i tecnici disponibili
     */
    function popolaSelectTecnici(tecnici) {
        selectTecnico.innerHTML = '<option value="">-- Seleziona un tecnico --</option>';
        
        if (tecnici.length === 0) {
            selectTecnico.innerHTML += '<option value="">Nessun tecnico disponibile</option>';
            return;
        }
        
        // Separa tecnici liberi da quelli già assegnati
        const tecniciLiberi = tecnici.filter(t => t.centro_attuale?.status === 'unassigned');
        const tecniciAssegnati = tecnici.filter(t => t.centro_attuale?.status === 'assigned');
        
        // Aggiungi tecnici liberi
        if (tecniciLiberi.length > 0) {
            const gruppo = document.createElement('optgroup');
            gruppo.label = 'Tecnici Disponibili';
            tecniciLiberi.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} - ${tecnico.specializzazione || 'Specializzazione N/A'}`, 
                    tecnico.id
                );
                gruppo.appendChild(option);
            });
            selectTecnico.appendChild(gruppo);
        }
        
        // Aggiungi tecnici da trasferire
        if (tecniciAssegnati.length > 0) {
            const gruppo = document.createElement('optgroup');
            gruppo.label = 'Trasferimento da Altri Centri';
            tecniciAssegnati.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} (attualmente: ${tecnico.centro_attuale.nome})`, 
                    tecnico.id
                );
                option.setAttribute('data-centro-attuale', tecnico.centro_attuale.nome);
                gruppo.appendChild(option);
            });
            selectTecnico.appendChild(gruppo);
        }
        
        // Abilita il pulsante
        btnAssegnaTecnico.disabled = false;
    }
    
    /**
     * Gestisce l'invio del form di assegnazione tecnico
     */
    function gestisciAssegnazioneTecnico(e) {
        e.preventDefault();
        
        const tecnicoId = selectTecnico.value;
        if (!tecnicoId) {
            mostraNotifica('Seleziona un tecnico da assegnare', 'warning');
            return;
        }
        
        // Controlla se è un trasferimento
        const opzioneSelezionata = selectTecnico.options[selectTecnico.selectedIndex];
        const centroAttuale = opzioneSelezionata.getAttribute('data-centro-attuale');
        
        if (centroAttuale) {
            const conferma = confirm(
                `Il tecnico verrà trasferito dal centro "${centroAttuale}" a questo centro. Continuare?`
            );
            if (!conferma) return;
        }
        
        // Procedi con l'assegnazione
        inviaAssegnazioneTecnico(tecnicoId);
    }
    
    /**
     * Invia la richiesta di assegnazione al server
     */
    function inviaAssegnazioneTecnico(tecnicoId) {
        console.log('📤 Invio assegnazione tecnico:', tecnicoId);
        
        // Disabilita pulsante durante invio
        btnAssegnaTecnico.disabled = true;
        btnAssegnaTecnico.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Assegnazione...';
        
        // Prepara dati form
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', CSRF_TOKEN);
        
        // Invia richiesta
        fetch(formAssegnazione.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostraNotifica(data.message, 'success');
                console.log('✅ Tecnico assegnato con successo');
                
                // Chiudi modal e ricarica pagina
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modalAssegnazione).hide();
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Errore nell\'assegnazione');
            }
        })
        .catch(error => {
            console.error('❌ Errore assegnazione:', error);
            mostraNotifica('Errore nell\'assegnazione: ' + error.message, 'danger');
        })
        .finally(() => {
            // Ripristina pulsante
            btnAssegnaTecnico.disabled = false;
            btnAssegnaTecnico.innerHTML = '<i class="bi bi-check-circle me-1"></i> Assegna Tecnico';
        });
    }
    
    /**
     * Gestisce la rimozione di un tecnico dal centro
     */
    function gestisciRimozioneTecnico(e) {
        const tecnicoId = this.getAttribute('data-tecnico-id');
        const tecnicoNome = this.getAttribute('data-tecnico-nome');
        
        const conferma = confirm(
            `Sei sicuro di voler rimuovere "${tecnicoNome}" da questo centro?`
        );
        
        if (conferma) {
            rimuoviTecnicoDalCentro(tecnicoId, tecnicoNome);
        }
    }
    
    /**
     * Rimuove un tecnico dal centro
     */
    function rimuoviTecnicoDalCentro(tecnicoId, tecnicoNome) {
        console.log('🗑️ Rimozione tecnico:', tecnicoNome);
        
        // Prepara dati per richiesta
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', CSRF_TOKEN);
        formData.append('_method', 'DELETE');
        
        const urlRimozione = `${BASE_URL}/admin/centri/${CENTRO_ID}/rimuovi-tecnico`;
        
        fetch(urlRimozione, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostraNotifica(data.message, 'success');
                console.log('✅ Tecnico rimosso con successo');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Errore nella rimozione');
            }
        })
        .catch(error => {
            console.error('❌ Errore rimozione:', error);
            mostraNotifica('Errore nella rimozione: ' + error.message, 'danger');
        });
    }
    
    /**
     * Mostra una notifica temporanea
     */
    function mostraNotifica(messaggio, tipo = 'info') {
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
        notifica.className = `alert ${tipiAlert[tipo]} alert-dismissible fade show notifica-temp`;
        notifica.style.minWidth = '300px';
        notifica.innerHTML = `
            <i class="bi ${icone[tipo]} me-2"></i>
            ${messaggio}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.getElementById('notificheContainer').appendChild(notifica);
        
        // Auto-rimuovi dopo 5 secondi
        setTimeout(() => {
            if (notifica.parentNode) {
                notifica.remove();
            }
        }, 5000);
    }
});

/**
 * Apre Google Maps con l'indirizzo del centro
 */
function apriGoogleMaps() {
    const indirizzo = encodeURIComponent('{{ $centro->indirizzo }}, {{ $centro->citta }}, {{ $centro->provincia }}');
    const url = `https://www.google.com/maps/search/?api=1&query=${indirizzo}`;
    window.open(url, '_blank');
    console.log('🗺️ Apertura Google Maps per:', '{{ $centro->nome }}');
}
</script>
@endpush

{{-- CSS personalizzato --}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI PER VISTA LINEARE === */

/* Miglioramento cards */
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Badge migliorati */
.badge {
    font-size: 0.875em;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}

/* Pulsanti gruppo verticale più compatti */
.btn-group-vertical .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Notifiche temporanee */
.notifica-temp {
    animation: slideInRight 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Miglioramenti per mobile */
@media (max-width: 768px) {
    .btn-group-vertical {
        display: flex;
        flex-direction: row;
        gap: 0.25rem;
    }
    
    .btn-group-vertical .btn {
        flex: 1;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .notifica-temp {
        left: 1rem;
        right: 1rem;
        min-width: auto;
    }
}

/* Label required con asterisco rosso */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Modal migliorato */
.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Stati select */
select:disabled {
    background-color: #f8f9fa;
    opacity: 0.7;
}
</style>
@endpush