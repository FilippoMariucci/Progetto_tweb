{{-- 
    Vista Admin Centro Assistenza - Versione con Stile Migliorato
    File: resources/views/admin/centri/show.blade.php
    
    Mantiene tutto il JavaScript e le funzionalità della prima vista,
    ma applica lo stile visivo moderno della seconda vista
--}}

@extends('layouts.app')

@section('title', 'Admin - ' . $centro->nome)

{{-- Meta description per SEO admin --}}
@section('meta_description', 'Amministrazione centro di assistenza ' . $centro->nome . ' a ' . $centro->citta . '. Gestione tecnici, contatti e configurazioni.')

{{-- Nasconde il breadcrumb per dare più spazio se necessario --}}
@push('breadcrumb-override')
<style>
.breadcrumb, nav[aria-label="breadcrumb"] {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    <div class="row">
        {{-- Colonna principale con i dettagli del centro --}}
        <div class="col-lg-8">
            
            {{-- Header del centro con design moderno --}}
            <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
            <div class="card card-custom mb-4">
                <div class="card-header bg-danger text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="h3 mb-0">
                                <i class="bi bi-shield-lock me-2"></i>
                                {{ $centro->nome }}
                            </h1>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-gear me-1"></i>
                                Pannello Amministrazione Centro
                            </p>
                        </div>
                        <div class="col-auto">
                            {{-- Badge admin con stato centro --}}
                            @if($centro->tecnici->count() > 0)
                                <span class="badge bg-light text-danger fs-6 me-2">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $centro->tecnici->count() }} Tecnic{{ $centro->tecnici->count() > 1 ? 'i' : 'o' }}
                                </span>
                                <span class="badge bg-success fs-6">Centro Attivo</span>
                            @else
                                <span class="badge bg-warning fs-6">Centro Inattivo</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Informazioni di contatto stilizzate --}}
                        <div class="col-md-6">
                            <h5 class="text-danger mb-3">
                                <i class="bi bi-geo-alt me-2"></i>
                                Informazioni di Contatto
                            </h5>
                            
                            {{-- Indirizzo con pulsante modifica --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small d-flex justify-content-between">
                                    INDIRIZZO
                                    <a href="{{ route('admin.centri.edit', $centro) }}" 
                                       class="btn btn-outline-warning btn-xs">
                                        <i class="bi bi-pencil"></i> Modifica
                                    </a>
                                </label>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{ $centro->indirizzo ?? 'Non specificato' }}
                                </p>
                                <p class="text-muted small mb-0">
                                    {{ $centro->citta }}
                                    @if($centro->cap)
                                        {{ $centro->cap }}
                                    @endif
                                    @if($centro->provincia)
                                        ({{ strtoupper($centro->provincia) }})
                                    @endif
                                </p>
                            </div>
                            
                            {{-- Telefono con copia --}}
                            @if($centro->telefono)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">TELEFONO</label>
                                    <p class="mb-0">
                                        <i class="bi bi-telephone text-success me-2"></i>
                                        <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                            {{ $centro->telefono }}
                                        </a>
                                        <button class="btn btn-outline-secondary btn-xs ms-2" 
                                                onclick="copiaInClipboard('{{ $centro->telefono }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- Email con copia --}}
                            @if($centro->email)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">EMAIL</label>
                                    <p class="mb-0">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                            {{ $centro->email }}
                                        </a>
                                        <button class="btn btn-outline-secondary btn-xs ms-2" 
                                                onclick="copiaInClipboard('{{ $centro->email }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- CAP se disponibile --}}
                            @if($centro->cap)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">CAP</label>
                                    <p class="mb-0">
                                        <i class="bi bi-mailbox text-info me-2"></i>
                                        {{ $centro->cap }}
                                    </p>
                                </div>
                            @endif
                            
                            {{-- Se non ci sono contatti --}}
                            @if(!$centro->telefono && !$centro->email)
                                <div class="text-center py-3">
                                    <i class="bi bi-exclamation-triangle text-warning display-6"></i>
                                    <p class="text-muted mb-0">Nessun contatto disponibile</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Gestione amministrativa --}}
                        <div class="col-md-6">
                            <h5 class="text-danger mb-3">
                                <i class="bi bi-tools me-2"></i>
                                Gestione Amministrativa
                            </h5>
                            
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">AZIONI RAPIDE</label>
                                <div class="d-grid gap-2 mt-2">
                                    {{-- Modifica centro --}}
                                    <a href="{{ route('admin.centri.edit', $centro) }}" 
                                       class="btn btn-warning text-white">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Modifica Informazioni
                                    </a>
                                    
                                    {{-- Aggiungi tecnico --}}
                                    <button type="button" 
                                            class="btn btn-success"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAssegnaTecnico">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Aggiungi Tecnico
                                    </button>
                                    
                                    {{-- Visualizza su mappa --}}
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="GoogleMapsUtil.openMaps('{{ $centro->indirizzo }}, {{ $centro->citta }}, {{ $centro->provincia }}')">
                                        <i class="bi bi-map me-1"></i>
                                        Visualizza su Maps
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Statistiche visuali --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">STATISTICHE</label>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <h5 class="text-danger mb-0">{{ $centro->tecnici->count() }}</h5>
                                            <small class="text-muted">Tecnici</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <h5 class="text-info mb-0">
                                                {{ $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count() }}
                                            </h5>
                                            <small class="text-muted">Specializzazioni</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Pulsanti di navigazione admin stilizzati --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                {{-- Torna alla lista --}}
                                <a href="{{ route('admin.centri.index') }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Lista Centri
                                </a>
                                
                                {{-- Dashboard admin --}}
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="btn btn-outline-danger">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Dashboard Admin
                                </a>
                                
                                {{-- Vista pubblica --}}
                                <a href="{{ route('centri.show', $centro) }}" 
                                   class="btn btn-outline-info" 
                                   target="_blank">
                                    <i class="bi bi-eye me-1"></i>
                                    Vista Pubblica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sezione Tecnici con design moderno ma funzionalità originale --}}
            <div class="card card-custom mb-4">
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
                        {{-- Lista tecnici con design migliorato --}}
                        <div class="row">
                            @foreach($centro->tecnici as $tecnico)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 tecnico-card">
                                        <div class="d-flex align-items-center mb-2">
                                            {{-- Avatar tecnico stilizzato --}}
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                {{-- Nome e info tecnico --}}
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.users.show', $tecnico) }}" 
                                                       class="text-decoration-none">
                                                        {{ $tecnico->nome_completo }}
                                                    </a>
                                                </h6>
                                                @if($tecnico->specializzazione)
                                                    <span class="badge bg-light text-dark small">
                                                        <i class="bi bi-wrench me-1"></i>
                                                        {{ $tecnico->specializzazione }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary small">
                                                        <i class="bi bi-question-circle me-1"></i>
                                                        Specializzazione non specificata
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Info dettagliate tecnico --}}
                                        @if($tecnico->eta)
                                            <p class="small text-muted mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Età: {{ $tecnico->eta }} anni
                                            </p>
                                        @endif
                                        
                                        
                                        
                                        {{-- Azioni sul tecnico con design migliorato --}}
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Visualizza
                                            </a>
                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Modifica
                                            </a>
                                            <form action="{{ route('admin.centri.rimuovi-tecnico', $centro) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Sei sicuro di voler rimuovere &quot;{{ addslashes($tecnico->nome_completo) }}&quot; da questo centro?\n\nIl tecnico rimarrà nel sistema ma non sarà più assegnato a questo centro.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tecnico_id" value="{{ $tecnico->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Rimuovi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Nessun tecnico assegnato - design migliorato --}}
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
            
        </div>
        
        {{-- Sidebar amministrativa con design migliorato --}}
        <div class="col-lg-4">
            
            {{-- Card informazioni amministrative --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Pannello di Controllo
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Stato del centro --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">STATO CENTRO</label>
                        <div class="mt-1">
                            @if($centro->tecnici->count() > 0)
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Operativo
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Inattivo
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Date importanti --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">INFORMAZIONI TEMPORALI</label>
                        <p class="small mb-1">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Creato: {{ $centro->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($centro->updated_at != $centro->created_at)
                            <p class="small mb-0">
                                <i class="bi bi-calendar-check me-1"></i>
                                Modificato: {{ $centro->updated_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                    
                    {{-- ID centro per riferimenti --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">ID CENTRO</label>
                        <div class="d-flex align-items-center">
                            <code class="bg-light p-2 rounded flex-grow-1">#{{ $centro->id }}</code>
                            <button class="btn btn-outline-secondary btn-sm ms-2" 
                                    onclick="copiaInClipboard('{{ $centro->id }}')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Statistiche avanzate --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistiche Dettagliate
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ $centro->tecnici->count() }}</h4>
                                <small class="text-muted">Tecnici Totali</small>
                            </div>
                        </div>
                        <div class="col-6">
                            @php
                                $specializzazioni = $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count();
                            @endphp
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">{{ $specializzazioni }}</h4>
                                <small class="text-muted">Specializzazioni</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h5 class="text-primary mb-1">
                                    @if($centro->tecnici->count() > 0)
                                        <i class="bi bi-check-circle me-1"></i>
                                        Centro Attivo
                                    @else
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Centro Inattivo
                                    @endif
                                </h5>
                                <small class="text-muted">
                                    Dal {{ $centro->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Altri centri nella stessa provincia (se esistono) --}}
            @if(isset($centriVicini) && $centriVicini->isNotEmpty())
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo text-primary me-2"></i>
                            Altri Centri in {{ strtoupper($centro->provincia) }} ({{ $centriVicini->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($centriVicini as $centroVicino)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.centri.show', $centroVicino) }}" 
                                               class="text-decoration-none">
                                                {{ $centroVicino->nome }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $centroVicino->citta }}
                                        </small>
                                    </div>
                                    <span class="badge bg-light text-dark">
                                        {{ $centroVicino->tecnici_count ?? 0 }} tecnici
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Link utili admin --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-danger me-2"></i>
                        Link Amministrativi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-list me-2"></i>
                            Tutti i Centri
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-people me-2"></i>
                            Gestione Utenti
                        </a>
                        
                        <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box me-2"></i>
                            Catalogo Prodotti
                        </a>
                        
                        <hr class="my-2">
                        
                        <a href="{{ route('centri.show', $centro) }}" 
                           class="btn btn-outline-info btn-sm" 
                           target="_blank">
                            <i class="bi bi-eye me-2"></i>
                            Vista Pubblica
                        </a>
                        
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
</div>

{{-- Modal per assegnazione tecnico (MANTIENE TUTTA LA FUNZIONALITÀ ORIGINALE) --}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Tecnico al Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info centro nel modal --}}
                <div class="alert alert-info">
                    <strong>Centro:</strong> {{ $centro->nome }}<br>
                    <strong>Località:</strong> {{ $centro->indirizzo }}, {{ $centro->citta }}
                </div>
                
                {{-- Form assegnazione - MANTIENE TUTTO IL CODICE ORIGINALE --}}
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

@endsection

{{-- 
===================================================================
CONFIGURAZIONE JAVASCRIPT - MANTIENE TUTTO IL CODICE ORIGINALE
===================================================================
--}}

{{-- Carica il file JavaScript separato ORIGINALE --}}
@push('scripts')
{{-- File JavaScript principale - IDENTICO ALL'ORIGINALE --}}
<script src="{{ asset('js/admin/centri-show.js') }}"></script>

{{-- Configurazione per il modulo JavaScript - IDENTICA ALL'ORIGINALE --}}
<script>
// Configurazione per AdminCentroShow - MANTIENE TUTTO
window.AdminCentroShowConfig = {
    centroId: {{ $centro->id }},
    baseUrl: '{{ url("/") }}',
    csrfToken: '{{ csrf_token() }}',
    debugMode: {{ app()->environment('local') ? 'true' : 'false' }}, // Debug solo in locale
    centroNome: @json($centro->nome),
    centroIndirizzo: @json($centro->indirizzo . ', ' . $centro->citta . ', ' . $centro->provincia)
};

// Log di configurazione per debug
console.log('🔧 Configurazione AdminCentroShow caricata:', window.AdminCentroShowConfig);

// Funzione di copia in clipboard migliorata
function copiaInClipboard(testo) {
    navigator.clipboard.writeText(testo).then(function() {
        // Mostra notifica di successo
        mostraNotifica('Copiato in clipboard: ' + testo, 'success');
    }).catch(function(err) {
        console.error('Errore nella copia:', err);
        mostraNotifica('Errore nella copia', 'error');
    });
}

// Funzione per mostrare notifiche temporanee
function mostraNotifica(messaggio, tipo = 'info') {
    // Rimuovi notifiche esistenti
    document.querySelectorAll('.notifica-temp').forEach(el => el.remove());
    
    // Crea nuova notifica
    const notifica = document.createElement('div');
    notifica.className = `alert alert-${tipo === 'success' ? 'success' : 'info'} notifica-temp`;
    notifica.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
    `;
    notifica.innerHTML = `
        <i class="bi bi-${tipo === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
        ${messaggio}
    `;
    
    document.body.appendChild(notifica);
    
    // Rimuovi dopo 3 secondi
    setTimeout(() => {
        if (notifica.parentNode) {
            notifica.remove();
        }
    }, 3000);
}
</script>
@endpush

{{-- CSS personalizzato - COMBINA I MIGLIORI STILI DI ENTRAMBE LE VISTE --}}
@push('styles')
<style>
/* === STILI BASE DELLA VISTA MODERNA === */

/* Stili per le card principali con effetti moderni */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.75rem;
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Card header personalizzati */
.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

/* Badge personalizzati con dimensioni moderne */
.badge.fs-6 {
    font-size: 0.875rem !important;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}

/* === STILI PER PULSANTI CON TESTO SEMPRE VISIBILE === */

/* Pulsanti primari con colori garantiti */
.btn.btn-danger {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

.btn.btn-warning {
    background: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000000 !important;
    font-weight: 600;
}

.btn.btn-success {
    background: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
}

.btn.btn-info {
    background: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #ffffff !important;
}

.btn.btn-primary {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #ffffff !important;
}

/* Hover effects con testo sempre visibile */
.btn.btn-danger:hover {
    background: #c82333 !important;
    border-color: #bd2130 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-warning:hover {
    background: #e0a800 !important;
    border-color: #d39e00 !important;
    color: #000000 !important;
    transform: translateY(-1px);
}

.btn.btn-success:hover {
    background: #218838 !important;
    border-color: #1e7e34 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-info:hover {
    background: #138496 !important;
    border-color: #117a8b !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-primary:hover {
    background: #0069d9 !important;
    border-color: #0062cc !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* === STILI SPECIFICI ADMIN === */

/* Pulsanti extra small per azioni rapide */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.25rem;
}

/* Label obbligatori con asterisco rosso */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Stile per codici e ID tecnici */
code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    border: 1px solid #dee2e6;
}

/* Animazioni hover per le card dei tecnici */
.tecnico-card {
    transition: all 0.2s ease;
}

.tecnico-card:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

/* === ICONE COLORATE PER MIGLIORARE LA UX === */

/* Icone colorate per i contatti */
.bi-telephone {
    color: #28a745 !important;
}

.bi-envelope {
    color: #17a2b8 !important;
}

.bi-map, .bi-geo-alt {
    color: #007bff !important;
}

.bi-shield-lock {
    color: #dc3545 !important;
}

.bi-gear, .bi-tools {
    color: #ffc107 !important;
}

.bi-people {
    color: #28a745 !important;
}

.bi-calendar3, .bi-calendar-plus, .bi-calendar-check {
    color: #6f42c1 !important;
}

.bi-at {
    color: #fd7e14 !important;
}

.bi-clipboard {
    color: #6c757d !important;
}

/* === NOTIFICHE TEMPORANEE === */

.notifica-temp {
    animation: slideInRight 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
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

/* === MODAL PERSONALIZZATI === */

.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header.bg-danger {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

.modal-header.bg-success {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

/* === ALERT PERSONALIZZATI === */

.alert-info {
    background: linear-gradient(135deg, #cce7ff, #e3f2fd);
    border: 1px solid #007bff;
    border-radius: 8px;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    border: 1px solid #dc3545;
    border-radius: 8px;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    border: 1px solid #28a745;
    border-radius: 8px;
}

/* === AVATAR E ELEMENTI GRAFICI === */

/* Stile per gli avatar dei tecnici */
.bg-success.text-white.rounded-circle {
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    transition: all 0.2s ease;
}

.bg-success.text-white.rounded-circle:hover {
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    transform: scale(1.05);
}

/* === MIGLIORAMENTI PER I BADGE === */

.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
    border-radius: 0.5rem;
    font-weight: 500;
}

.badge.bg-light {
    color: #212529 !important;
    border: 1px solid #dee2e6;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* === SEZIONI CON BORDI COLORATI === */

.border-start-admin {
    border-left: 4px solid #dc3545 !important;
}

.border-start-success {
    border-left: 4px solid #28a745 !important;
}

.border-start-warning {
    border-left: 4px solid #ffc107 !important;
}

/* === RESPONSIVE DESIGN === */

/* Responsive design per mobile */
@media (max-width: 768px) {
    /* Layout responsive per i pulsanti */
    .d-flex.flex-wrap.gap-2 {
        justify-content: stretch !important;
    }
    
    .d-flex.flex-wrap.gap-2 > * {
        flex: 1 !important;
        min-width: 120px;
    }
    
    /* Azioni tecnici responsive */
    .d-flex.gap-1 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-1 .btn {
        width: 100%;
    }
    
    /* Notifiche mobile */
    .notifica-temp {
        left: 1rem;
        right: 1rem;
        min-width: auto;
        top: 10px;
    }
    
    /* Modal responsive */
    .modal-dialog {
        margin: 10px;
    }
    
    /* Card responsive */
    .card-custom {
        margin-bottom: 1.5rem;
    }
    
    /* Statistiche responsive */
    .row.g-3.text-center .col-6 {
        margin-bottom: 1rem;
    }
}

/* Tablet design */
@media (max-width: 992px) {
    .card-custom {
        margin-bottom: 2rem;
    }
    
    .col-lg-4 .card-custom {
        margin-bottom: 1rem;
    }
    
    /* Adatta layout tecnici su tablet */
    .col-md-6 {
        margin-bottom: 1rem;
    }
}

/* === ACCESSIBILITÀ === */

/* Focus migliorato per tutti gli elementi interattivi */
.form-control:focus,
.form-select:focus,
.btn:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: none;
}

/* Stati disabled migliorati */
.btn:disabled,
.form-control:disabled,
.form-select:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Contrasto migliorato per testo muted */
.text-muted {
    color: #6c757d !important;
}

/* Link con hover migliorato */
a:hover {
    text-decoration: underline;
}

/* === ANIMAZIONI E TRANSIZIONI === */

/* Transizioni smooth per tutti gli elementi */
.btn,
.card,
.badge,
.alert,
.border.rounded {
    transition: all 0.2s ease;
}

/* === RIDUZIONE ANIMAZIONI PER ACCESSIBILITÀ === */

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    
    .notifica-temp {
        animation: none;
    }
    
    .card-custom:hover,
    .tecnico-card:hover {
        transform: none;
    }
    
    .btn:hover {
        transform: none;
    }
}

/* === STILI UTILITÀ AGGIUNTIVI === */

/* Scroll smooth per link interni */
html {
    scroll-behavior: smooth;
}

/* Miglioramenti tipografici */
h1, h2, h3, h4, h5, h6 {
    line-height: 1.3;
    font-weight: 600;
}

small, .small {
    line-height: 1.4;
}

/* Spaziatura migliorata */
.gap-admin {
    gap: 0.75rem !important;
}

/* === STATI DI CARICAMENTO === */

/* Spinner personalizzato */
.spinner-admin {
    width: 1rem;
    height: 1rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #dc3545;
    border-radius: 50%;
    animation: spin-admin 1s linear infinite;
}

@keyframes spin-admin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Stati di caricamento per pulsanti */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 1rem;
    height: 1rem;
    margin: -0.5rem 0 0 -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin-admin 1s linear infinite;
}

/* === MIGLIORAMENTI SPECIFICI PER LA VISTA CENTRO === */

/* Stile per le sezioni informative */
.mb-3 label.fw-semibold {
    letter-spacing: 0.05em;
    font-size: 0.875rem;
}

/* Miglioramenti per i link di contatto */
a[href^="tel:"],
a[href^="mailto:"] {
    text-decoration: none;
    color: inherit;
    transition: color 0.2s ease;
}

a[href^="tel:"]:hover {
    color: #28a745 !important;
}

a[href^="mailto:"]:hover {
    color: #17a2b8 !important;
}

/* Stile per le statistiche */
.border.rounded.p-3 {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    transition: all 0.2s ease;
}

.border.rounded.p-3:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

/* === DARK MODE SUPPORT (preparazione futura) === */

@media (prefers-color-scheme: dark) {
    /* Al momento manteniamo il tema chiaro per coerenza,
       ma prepariamo la struttura per un futuro dark mode */
}

/* === STAMPA === */

@media print {
    /* Nascondi elementi non necessari in stampa */
    .btn,
    .modal,
    .notifica-temp {
        display: none !important;
    }
    
    /* Assicura buon contrasto in stampa */
    .card-custom {
        border: 1px solid #000;
        box-shadow: none;
    }
    
    /* Forza colori per stampa */
    .text-primary,
    .text-success,
    .text-danger,
    .text-warning,
    .text-info {
        color: #000 !important;
    }
}

/* === FINE STILI === */
</style>
@endpush