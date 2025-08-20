{{-- 
    Vista completa per la gestione di un centro di assistenza (Admin)
    File: resources/views/admin/centri/show.blade.php
    
    Funzionalità:
    - Visualizzazione dettagli centro
    - Gestione tecnici assegnati
    - Assegnazione nuovi tecnici
    - Rimozione tecnici
    - Statistiche centro
    - Azioni amministrative
--}}

@extends('layouts.app')

@section('title', 'Gestione Centro: ' . $centro->nome)

@section('content')
<div class="container-fluid mt-4">
    
    {{-- === BREADCRUMB === --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.centri.index') }}">Gestione Centri</a></li>
            <li class="breadcrumb-item active">{{ $centro->nome }}</li>
        </ol>
    </nav>

    {{-- === HEADER CENTRO === --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">
                                <i class="bi bi-building me-2"></i>
                                {{ $centro->nome }}
                            </h3>
                            <small class="opacity-75">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $centro->indirizzo_completo }}
                            </small>
                        </div>
                        
                        {{-- Badge stato centro --}}
                        <div class="text-end">
                            @if($centro->tecnici->count() > 0)
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Centro Attivo
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Nessun Tecnico
                                </span>
                            @endif
                            
                            <div class="mt-2">
                                <span class="badge bg-light text-dark badge-tecnici-count">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $centro->tecnici->count() }} Tecnici
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        {{-- === COLONNA PRINCIPALE (TECNICI E AZIONI) === --}}
        <div class="col-lg-8">
            
            {{-- === SEZIONE TECNICI ASSEGNATI === --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>
                            Tecnici Assegnati ({{ $centro->tecnici->count() }})
                        </h5>
                        
                        {{-- Pulsante assegna tecnico --}}
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                            <i class="bi bi-person-plus me-1"></i>
                            Assegna Tecnico
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($centro->tecnici->isNotEmpty())
                        {{-- Lista tecnici con azioni --}}
                        <div class="row">
                            @foreach($centro->tecnici as $tecnico)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title mb-1">
                                                        <i class="bi bi-person me-1 text-success"></i>
                                                        {{ $tecnico->nome_completo }}
                                                    </h6>
                                                    
                                                    @if($tecnico->specializzazione)
                                                        <p class="card-text mb-1">
                                                            <small class="text-muted">
                                                                <i class="bi bi-tools me-1"></i>
                                                                {{ $tecnico->specializzazione }}
                                                            </small>
                                                        </p>
                                                    @endif
                                                    
                                                    @if($tecnico->data_nascita)
                                                        <p class="card-text mb-1">
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar me-1"></i>
                                                                {{ $tecnico->eta }} anni
                                                            </small>
                                                        </p>
                                                    @endif
                                                    
                                                    <p class="card-text mb-0">
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Assegnato {{ $tecnico->created_at->diffForHumans() }}
                                                        </small>
                                                    </p>
                                                </div>
                                                
                                                {{-- Azioni tecnico --}}
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.users.show', $tecnico) }}">
                                                                <i class="bi bi-eye me-1"></i>
                                                                Visualizza Dettagli
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.users.edit', $tecnico) }}">
                                                                <i class="bi bi-pencil me-1"></i>
                                                                Modifica Tecnico
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" 
                                                                    class="dropdown-item text-warning btn-rimuovi-tecnico"
                                                                    data-tecnico-id="{{ $tecnico->id }}"
                                                                    data-tecnico-nome="{{ $tecnico->nome_completo }}">
                                                                <i class="bi bi-person-dash me-1"></i>
                                                                Rimuovi da Centro
                                                            </button>
                                                        </li>
                                                    </ul>
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
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun Tecnico Assegnato</h5>
                            <p class="text-muted">Questo centro non ha ancora tecnici assegnati.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                                <i class="bi bi-person-plus me-1"></i>
                                Assegna Primo Tecnico
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- === AZIONI RAPIDE === --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Modifica centro --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.centri.edit', $centro) }}" class="btn btn-warning w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="bi bi-pencil display-6 mb-2"></i>
                                <span class="fw-semibold">Modifica Centro</span>
                            </a>
                        </div>
                        
                        {{-- Lista centri --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.centri.index') }}" class="btn btn-info w-100 h-100 d-flex flex-column justify-content-center">
                                <i class="bi bi-list display-6 mb-2"></i>
                                <span class="fw-semibold">Lista Centri</span>
                            </a>
                        </div>
                        
                        {{-- Google Maps --}}
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100 h-100 d-flex flex-column justify-content-center" onclick="apriGoogleMaps()">
                                <i class="bi bi-geo-alt display-6 mb-2"></i>
                                <span class="fw-semibold">Apri in Google Maps</span>
                            </button>
                        </div>
                        
                        {{-- Copia indirizzo --}}
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center" onclick="copiaIndirizzo()">
                                <i class="bi bi-clipboard display-6 mb-2"></i>
                                <span class="fw-semibold">Copia Indirizzo</span>
                            </button>
                        </div>
                        
                        {{-- Chiama centro --}}
                        @if($centro->telefono)
                            <div class="col-md-6">
                                <a href="tel:{{ $centro->telefono }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                                    <i class="bi bi-telephone display-6 mb-2"></i>
                                    <span class="fw-semibold">Chiama Centro</span>
                                </a>
                            </div>
                        @endif
                        
                        {{-- Invia email --}}
                        @if($centro->email)
                            <div class="col-md-6">
                                <a href="mailto:{{ $centro->email }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                                    <i class="bi bi-envelope display-6 mb-2"></i>
                                    <span class="fw-semibold">Invia Email</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- === SIDEBAR INFORMAZIONI === --}}
        <div class="col-lg-4">
            
            {{-- === INFORMAZIONI CENTRO === --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informazioni Centro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Indirizzo --}}
                        <div class="col-12">
                            <div class="border-start border-info border-4 ps-3">
                                <h6 class="text-info mb-1">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Localizzazione
                                </h6>
                                <p class="mb-1"><strong>Indirizzo:</strong><br>{{ $centro->indirizzo }}</p>
                                <p class="mb-1"><strong>Città:</strong> {{ $centro->citta }}</p>
                                <p class="mb-1"><strong>Provincia:</strong> {{ $centro->provincia }}</p>
                                @if($centro->cap)
                                    <p class="mb-0"><strong>CAP:</strong> {{ $centro->cap }}</p>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Contatti --}}
                        @if($centro->telefono || $centro->email)
                            <div class="col-12">
                                <div class="border-start border-success border-4 ps-3">
                                    <h6 class="text-success mb-1">
                                        <i class="bi bi-telephone me-1"></i>
                                        Contatti
                                    </h6>
                                    @if($centro->telefono)
                                        <p class="mb-1">
                                            <strong>Telefono:</strong><br>
                                            <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                {{ $centro->telefono }}
                                            </a>
                                        </p>
                                    @endif
                                    @if($centro->email)
                                        <p class="mb-0">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                {{ $centro->email }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        {{-- Informazioni sistema --}}
                        <div class="col-12">
                            <div class="border-start border-warning border-4 ps-3">
                                <h6 class="text-warning mb-1">
                                    <i class="bi bi-gear me-1"></i>
                                    Info Sistema
                                </h6>
                                <p class="mb-1"><strong>ID Centro:</strong> {{ $centro->id }}</p>
                                <p class="mb-1"><strong>Creato:</strong> {{ $centro->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-0"><strong>Aggiornato:</strong> {{ $centro->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- === STATISTICHE CENTRO === --}}
            @if(isset($statisticheCentro))
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Statistiche
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            
                            {{-- Numero tecnici --}}
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-success mb-1">{{ $statisticheCentro['totale_tecnici'] }}</h3>
                                    <small class="text-muted">Tecnici Totali</small>
                                </div>
                            </div>
                            
                            {{-- Specializzazioni --}}
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-info mb-1">{{ $statisticheCentro['specializzazioni']->count() }}</h3>
                                    <small class="text-muted">Specializzazioni</small>
                                </div>
                            </div>
                            
                            {{-- Età media --}}
                            @if($statisticheCentro['eta_media'])
                                <div class="col-12">
                                    <div class="text-center">
                                        <h4 class="text-warning mb-1">{{ round($statisticheCentro['eta_media']) }} anni</h4>
                                        <small class="text-muted">Età Media Tecnici</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Specializzazioni dettagliate --}}
                            @if($statisticheCentro['specializzazioni']->isNotEmpty())
                                <div class="col-12">
                                    <hr>
                                    <h6 class="text-muted mb-2">Distribuzione Specializzazioni:</h6>
                                    @foreach($statisticheCentro['specializzazioni'] as $specializzazione => $count)
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>{{ $specializzazione ?: 'Non specificata' }}</small>
                                            <span class="badge bg-secondary">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- === CENTRI NELLE VICINANZE === --}}
            @if($centriVicini->isNotEmpty())
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-geo me-2"></i>
                            Centri Vicini ({{ $centro->provincia }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($centriVicini as $centroVicino)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $centroVicino->nome }}</strong><br>
                                    <small class="text-muted">{{ $centroVicino->citta }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ $centroVicino->tecnici_count }} tecnici</span><br>
                                    <a href="{{ route('admin.centri.show', $centroVicino) }}" class="btn btn-outline-secondary btn-sm mt-1">
                                        Visualizza
                                    </a>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- === MODAL ASSEGNAZIONE TECNICO === --}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1" aria-labelledby="modalAssegnaTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            {{-- Header Modal --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAssegnaTecnicoLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Assegna Tecnico al Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Body Modal --}}
            <div class="modal-body">
                
                {{-- Info Centro --}}
                <div class="alert alert-info border-start border-info border-4 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building display-6 text-info me-3"></i>
                        <div>
                            <h6 class="mb-1">{{ $centro->nome }}</h6>
                            <small class="text-muted">{{ $centro->indirizzo_completo }}</small>
                        </div>
                    </div>
                </div>
                
                {{-- Statistiche Rapide --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-people text-success display-6"></i>
                                <h4 class="mt-2 mb-1" id="statTecniciDisponibili">0</h4>
                                <small class="text-muted">Tecnici Disponibili</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-arrow-repeat text-warning display-6"></i>
                                <h4 class="mt-2 mb-1" id="statTecniciAssegnati">0</h4>
                                <small class="text-muted">Da Altri Centri</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-calculator text-info display-6"></i>
                                <h4 class="mt-2 mb-1" id="statTecniciTotali">0</h4>
                                <small class="text-muted">Totale Sistema</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Form Assegnazione --}}
                <form id="formAssegnaTecnico" action="{{ route('admin.centri.assegna-tecnico', $centro) }}" method="POST">
                    @csrf
                    
                    {{-- Select Tecnico --}}
                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i>
                            Seleziona Tecnico da Assegnare
                            <span class="text-danger">*</span>
                        </label>
                        
                        <select name="tecnico_id" id="tecnico_id" class="form-select form-select-lg" required>
                            <option value="">-- Caricamento tecnici... --</option>
                        </select>
                        
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Disponibili:</strong> Tecnici non assegnati a nessun centro<br>
                            <strong>Riassegnazione:</strong> Tecnici già assegnati ad altri centri possono essere trasferiti
                        </div>
                    </div>
                    
                    {{-- Opzioni Aggiuntive --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notificaAssegnazione" name="notifica_assegnazione" checked>
                            <label class="form-check-label" for="notificaAssegnazione">
                                <i class="bi bi-envelope me-1"></i>
                                Invia notifica email al tecnico dell'assegnazione
                            </label>
                        </div>
                    </div>
                    
                    {{-- Sezione Info Tecnico Selezionato (nascosta inizialmente) --}}
                    <div id="infoTecnicoSelezionato" class="d-none">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Informazioni Tecnico Selezionato
                                </h6>
                            </div>
                            <div class="card-body" id="dettagliTecnico">
                                <!-- Popolato via JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            
            {{-- Footer Modal --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Annulla
                </button>
                
                <button type="submit" form="formAssegnaTecnico" id="btnAssegnaTecnico" class="btn btn-success" disabled>
                    <i class="bi bi-person-plus me-1"></i>
                    Assegna Tecnico
                </button>
            </div>
        </div>
    </div>
</div>

{{-- === AREA MESSAGGI (per notifiche JavaScript) === --}}
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- I messaggi JavaScript verranno inseriti qui -->
</div>

@endsection

{{-- === JAVASCRIPT PERSONALIZZATO === --}}
@push('scripts')
<script>
/**
 * JavaScript per la gestione completa del centro di assistenza
 * Gestisce: assegnazione tecnici, rimozione, statistiche, azioni rapide
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Admin Centro Show - Script inizializzato');
    
    // === CONFIGURAZIONE ===
    const CENTRO_ID = {{ $centro->id }};
    const BASE_URL = '{{ url("/") }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // === ELEMENTI DOM ===
    const selectTecnico = document.getElementById('tecnico_id');
    const btnAssegnaTecnico = document.getElementById('btnAssegnaTecnico');
    const formAssegnazione = document.getElementById('formAssegnaTecnico');
    const modalAssegnazione = document.getElementById('modalAssegnaTecnico');
    
    // Verifica elementi critici
    if (!selectTecnico || !btnAssegnaTecnico || !formAssegnazione || !modalAssegnazione) {
        console.error('❌ Elementi DOM critici non trovati');
        return;
    }
    
    if (!CSRF_TOKEN) {
        console.error('❌ Token CSRF non trovato');
        mostraNotifica('Errore di configurazione - Token CSRF', 'danger');
        return;
    }
    
    // === CARICAMENTO TECNICI DISPONIBILI ===
    function caricaTecniciDisponibili() {
        console.log('⏳ Caricamento tecnici disponibili per centro:', CENTRO_ID);
        
        // Mostra stato di caricamento
        selectTecnico.innerHTML = '<option value="">-- Caricamento tecnici... --</option>';
        selectTecnico.disabled = true;
        btnAssegnaTecnico.disabled = true;
        
        // URL API per tecnici disponibili per questo centro specifico
        const apiUrl = `${BASE_URL}/api/admin/centri/${CENTRO_ID}/tecnici-disponibili`;
        console.log('🔗 Chiamata API:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('📡 Risposta HTTP status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('✅ Dati tecnici ricevuti:', data);
            
            if (data.success) {
                popolaSelectTecnici(data.tecnici || []);
                aggiornaStatistiche(data.tecnici || []);
            } else {
                throw new Error(data.message || 'Errore nel caricamento tecnici');
            }
        })
        .catch(error => {
            console.error('❌ Errore caricamento tecnici:', error);
            selectTecnico.innerHTML = '<option value="">-- Errore caricamento --</option>';
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        })
        .finally(() => {
            selectTecnico.disabled = false;
        });
    }
    
    // === POPOLAMENTO SELECT TECNICI ===
    function popolaSelectTecnici(tecnici) {
        console.log('📋 Popolamento select con', tecnici.length, 'tecnici');
        
        // Svuota select
        selectTecnico.innerHTML = '';
        
        // Opzione di default
        selectTecnico.appendChild(new Option('-- Seleziona un tecnico --', ''));
        
        if (tecnici.length === 0) {
            selectTecnico.appendChild(new Option('Nessun tecnico disponibile', ''));
            btnAssegnaTecnico.disabled = true;
            mostraNotifica('Non ci sono tecnici disponibili per l\'assegnazione', 'info');
            return;
        }
        
        // === RAGGRUPPAMENTO TECNICI ===
        // Separa tecnici non assegnati da quelli già assegnati ad altri centri
        const tecniciNonAssegnati = tecnici.filter(t => t.centro_attuale.status === 'unassigned');
        const tecniciAssegnati = tecnici.filter(t => t.centro_attuale.status === 'assigned');
        
        // === GRUPPO TECNICI NON ASSEGNATI (PRIORITÀ) ===
        if (tecniciNonAssegnati.length > 0) {
            const groupNonAssegnati = document.createElement('optgroup');
            groupNonAssegnati.label = '✅ Tecnici Disponibili';
            
            tecniciNonAssegnati.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} (${tecnico.specializzazione})`,
                    tecnico.id
                );
                option.style.fontWeight = 'bold';
                option.style.color = '#28a745';
                option.setAttribute('data-specializzazione', tecnico.specializzazione);
                option.setAttribute('data-stato', 'disponibile');
                groupNonAssegnati.appendChild(option);
            });
            
            selectTecnico.appendChild(groupNonAssegnati);
        }
        
        // === GRUPPO TECNICI ASSEGNATI (RIASSEGNAZIONE) ===
        if (tecniciAssegnati.length > 0) {
            const groupAssegnati = document.createElement('optgroup');
            groupAssegnati.label = '🔄 Riassegnazione (da altri centri)';
            
            tecniciAssegnati.forEach(tecnico => {
                const option = new Option(
                    `${tecnico.nome_completo} (da: ${tecnico.centro_attuale.nome})`,
                    tecnico.id
                );
                option.style.fontStyle = 'italic';
                option.style.color = '#6c757d';
                // Aggiungi attributi data per info aggiuntive
                option.setAttribute('data-centro-attuale', tecnico.centro_attuale.nome);
                option.setAttribute('data-specializzazione', tecnico.specializzazione);
                option.setAttribute('data-stato', 'assegnato');
                groupAssegnati.appendChild(option);
            });
            
            selectTecnico.appendChild(groupAssegnati);
        }
        
        // Abilita il pulsante se ci sono tecnici
        btnAssegnaTecnico.disabled = false;
        
        console.log('✅ Select popolato con successo');
    }
    
    // === AGGIORNAMENTO STATISTICHE ===
    function aggiornaStatistiche(tecnici) {
        const stats = {
            disponibili: tecnici.filter(t => t.centro_attuale.status === 'unassigned').length,
            assegnati: tecnici.filter(t => t.centro_attuale.status === 'assigned').length,
            totale: tecnici.length
        };
        
        // Aggiorna i contatori nel modal
        document.getElementById('statTecniciDisponibili').textContent = stats.disponibili;
        document.getElementById('statTecniciAssegnati').textContent = stats.assegnati;
        document.getElementById('statTecniciTotali').textContent = stats.totale;
        
        console.log('📊 Statistiche aggiornate:', stats);
    }
    
    // === GESTIONE SUBMIT FORM ASSEGNAZIONE ===
    formAssegnazione.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tecnicoId = selectTecnico.value;
        if (!tecnicoId) {
            mostraNotifica('Seleziona un tecnico da assegnare', 'warning');
            return;
        }
        
        // === VERIFICA RIASSEGNAZIONE ===
        const selectedOption = selectTecnico.options[selectTecnico.selectedIndex];
        const statoTecnico = selectedOption.getAttribute('data-stato');
        const centroAttuale = selectedOption.getAttribute('data-centro-attuale');
        
        if (statoTecnico === 'assegnato' && centroAttuale) {
            const conferma = confirm(
                `ATTENZIONE: Il tecnico "${selectedOption.text.split(' (da:')[0]}" è attualmente assegnato al centro "${centroAttuale}".\n\n` +
                `Procedendo con l'assegnazione, verrà automaticamente trasferito a questo centro.\n\n` +
                `Vuoi continuare con il trasferimento?`
            );
            
            if (!conferma) {
                return; // Utente ha annullato
            }
        }
        
        // === INVIO RICHIESTA ASSEGNAZIONE ===
        inviaAssegnazione(tecnicoId);
    });
    
    // === INVIO ASSEGNAZIONE ===
    function inviaAssegnazione(tecnicoId) {
        console.log('📤 Invio assegnazione tecnico:', tecnicoId, 'al centro:', CENTRO_ID);
        
        // Disabilita form durante l'invio
        btnAssegnaTecnico.disabled = true;
        btnAssegnaTecnico.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Assegnazione...';
        selectTecnico.disabled = true;
        
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', CSRF_TOKEN);
        
        // Opzioni aggiuntive
        const notificaAssegnazione = document.getElementById('notificaAssegnazione');
        if (notificaAssegnazione && notificaAssegnazione.checked) {
            formData.append('notifica_assegnazione', '1');
        }
        
        // URL form action
        const actionUrl = formAssegnazione.getAttribute('action');
        console.log('🔗 URL assegnazione:', actionUrl);
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostraNotifica(data.message, 'success');
                
                // Chiudi modal e ricarica pagina dopo 2 secondi
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modalAssegnazione).hide();
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(data.message || 'Errore durante l\'assegnazione');
            }
        })
        .catch(error => {
            console.error('❌ Errore assegnazione:', error);
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        })
        .finally(() => {
            // Riabilita form
            btnAssegnaTecnico.disabled = false;
            btnAssegnaTecnico.innerHTML = '<i class="bi bi-person-plus me-1"></i>Assegna Tecnico';
            selectTecnico.disabled = false;
        });
    }
    
    // === GESTIONE RIMOZIONE TECNICI ===
    document.querySelectorAll('.btn-rimuovi-tecnico').forEach(button => {
        button.addEventListener('click', function() {
            const tecnicoId = this.getAttribute('data-tecnico-id');
            const tecnicoNome = this.getAttribute('data-tecnico-nome');
            
            console.log('🗑️ Richiesta rimozione tecnico:', tecnicoId, tecnicoNome);
            
            const conferma = confirm(
                `Sei sicuro di voler rimuovere il tecnico "${tecnicoNome}" da questo centro?\n\n` +
                `Il tecnico non sarà più assegnato a questo centro, ma rimarrà nel sistema e potrà essere assegnato ad altri centri.`
            );
            
            if (conferma) {
                rimuoviTecnico(tecnicoId, tecnicoNome);
            }
        });
    });
    
    // === FUNZIONE RIMOZIONE TECNICO ===
    function rimuoviTecnico(tecnicoId, tecnicoNome) {
        console.log('📤 Invio rimozione tecnico:', tecnicoId);
        
        const formData = new FormData();
        formData.append('tecnico_id', tecnicoId);
        formData.append('_token', CSRF_TOKEN);
        formData.append('_method', 'DELETE'); // Simula DELETE method
        
        // URL per rimozione
        const removeUrl = `${BASE_URL}/admin/centri/${CENTRO_ID}/rimuovi-tecnico`;
        console.log('🔗 URL rimozione:', removeUrl);
        
        fetch(removeUrl, {
            method: 'POST', // Laravel usa POST con _method=DELETE
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostraNotifica(data.message, 'success');
                
                // Ricarica pagina dopo 1.5 secondi
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Errore durante la rimozione');
            }
        })
        .catch(error => {
            console.error('❌ Errore rimozione:', error);
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        });
    }
    
    // === EVENT LISTENERS MODAL ===
    
    // Carica tecnici quando si apre il modal
    modalAssegnazione.addEventListener('shown.bs.modal', function() {
        console.log('📂 Modal assegnazione aperto - caricamento tecnici');
        caricaTecniciDisponibili();
    });
    
    // Reset form quando si chiude il modal
    modalAssegnazione.addEventListener('hidden.bs.modal', function() {
        console.log('❌ Modal chiuso - reset form');
        
        // Reset select
        selectTecnico.innerHTML = '<option value="">-- Seleziona un tecnico --</option>';
        selectTecnico.disabled = false;
        
        // Reset button
        btnAssegnaTecnico.disabled = true;
        btnAssegnaTecnico.innerHTML = '<i class="bi bi-person-plus me-1"></i>Assegna Tecnico';
        
        // Reset statistiche
        document.getElementById('statTecniciDisponibili').textContent = '0';
        document.getElementById('statTecniciAssegnati').textContent = '0';
        document.getElementById('statTecniciTotali').textContent = '0';
        
        // Nascondi info tecnico
        document.getElementById('infoTecnicoSelezionato').classList.add('d-none');
        
        // Rimuovi alert
        document.querySelectorAll('.alert-custom').forEach(alert => alert.remove());
    });
    
    // === FUNZIONI UTILITY ===
    
    // Funzione notifiche
    function mostraNotifica(messaggio, tipo = 'info') {
        const alertTypes = {
            'success': 'alert-success',
            'danger': 'alert-danger', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const icons = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        };
        
        const alertClass = alertTypes[tipo] || 'alert-info';
        const iconClass = icons[tipo] || 'bi-info-circle';
        
        // Crea alert
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show alert-custom`;
        alert.innerHTML = `
            <i class="${iconClass} me-2"></i>
            ${messaggio}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Inserisci nel container
        const container = document.getElementById('alertContainer');
        container.appendChild(alert);
        
        // Auto-rimozione dopo 5 secondi
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
    
    console.log('✅ Script Admin Centro Show inizializzato correttamente');
});

// === FUNZIONI GLOBALI (callable da HTML) ===

/**
 * Apre Google Maps con l'indirizzo del centro
 */
function apriGoogleMaps() {
    const indirizzo = '{{ urlencode($centro->indirizzo_completo) }}';
    const url = `https://www.google.com/maps/search/?api=1&query=${indirizzo}`;
    window.open(url, '_blank');
}

/**
 * Copia l'indirizzo del centro negli appunti
 */
function copiaIndirizzo() {
    const indirizzo = '{{ $centro->indirizzo_completo }}';
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(indirizzo).then(() => {
            alert('Indirizzo copiato negli appunti!');
        });
    } else {
        // Fallback per browser più vecchi
        const textArea = document.createElement('textarea');
        textArea.value = indirizzo;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Indirizzo copiato negli appunti!');
    }
}
</script>
@endpush