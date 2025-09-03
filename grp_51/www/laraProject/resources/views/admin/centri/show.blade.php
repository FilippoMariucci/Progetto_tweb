{{-- 
    Vista Admin Gestione Centro di Assistenza con Stile Migliorato
    File: resources/views/admin/centri/show.blade.php
    
    Applica il design user-friendly della vista pubblica alla versione admin,
    mantenendo intatte tutte le funzionalità amministrative
--}}

@extends('layouts.app')

@section('title', 'Admin - ' . $centro->nome)

{{-- Meta description per SEO admin --}}
@section('meta_description', 'Amministrazione centro di assistenza ' . $centro->nome . ' a ' . $centro->citta . '. Gestione tecnici, contatti e configurazioni.')

@section('content')
<div class="container mt-4">
    
    {{-- Breadcrumb per navigazione admin --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">
                    <i class="bi bi-house"></i> Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard Admin
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.centri.index') }}">
                    <i class="bi bi-geo-alt"></i> Gestione Centri
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ Str::limit($centro->nome, 40) }}
            </li>
        </ol>
    </nav>

    <div class="row">
        {{-- Colonna principale con i dettagli del centro --}}
        <div class="col-lg-8">
            
            {{-- Header del centro con badge admin --}}
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
                        {{-- Informazioni di contatto admin --}}
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
                            
                            {{-- Telefono --}}
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
                            
                            {{-- Email --}}
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
                                    @if($centro->indirizzo && $centro->citta)
                                        <button type="button" 
                                                class="btn btn-primary" 
                                                onclick="apriGoogleMaps()">
                                            <i class="bi bi-map me-1"></i>
                                            Visualizza su Maps
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
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
                    
                    {{-- Pulsanti di navigazione admin --}}
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
                                
                                {{-- Elimina centro --}}
                                <button type="button" 
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEliminaCentro">
                                    <i class="bi bi-trash me-1"></i>
                                    Elimina Centro
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sezione Tecnici Assegnati --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2"></i>
                                Tecnici Assegnati
                                <span class="badge bg-light text-success ms-2">{{ $centro->tecnici->count() }}</span>
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button type="button" 
                                    class="btn btn-light btn-sm"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAssegnaTecnico">
                                <i class="bi bi-plus-circle me-1"></i>
                                Aggiungi
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($centro->tecnici && $centro->tecnici->count() > 0)
                        <div class="row g-3">
                            @foreach($centro->tecnici as $tecnico)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            {{-- Avatar tecnico --}}
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">
                                                    <a href="{{ route('admin.users.show', $tecnico) }}" 
                                                       class="text-decoration-none">
                                                        {{ $tecnico->nome }} {{ $tecnico->cognome }}
                                                    </a>
                                                </h6>
                                                @if($tecnico->specializzazione)
                                                    <span class="badge bg-light text-dark small">
                                                        {{ $tecnico->specializzazione }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Info dettagliate tecnico --}}
                                        @if($tecnico->data_nascita)
                                            <p class="small text-muted mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Età: {{ $tecnico->data_nascita->age }} anni
                                            </p>
                                        @endif
                                        
                                        <p class="small text-muted mb-2">
                                            <i class="bi bi-at me-1"></i>
                                            Username: <code>{{ $tecnico->username }}</code>
                                        </p>
                                        
                                        {{-- Azioni amministrative --}}
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.centri.rimuovi-tecnico', $centro) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Rimuovere {{ addslashes($tecnico->nome_completo) }} da questo centro?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tecnico_id" value="{{ $tecnico->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
                            <button type="button" 
                                    class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAssegnaTecnico">
                                <i class="bi bi-plus-circle me-1"></i>
                                Aggiungi Primo Tecnico
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
        
        {{-- Sidebar amministrativa --}}
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
                    
                    {{-- ID tecnico per riferimenti --}}
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
            
            {{-- Centri vicini (mantenuto dalla vista admin originale) --}}
            @if(isset($centriVicini) && $centriVicini->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo text-primary me-2"></i>
                            Altri Centri in {{ strtoupper($centro->provincia) }}
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

{{-- Modal per assegnazione tecnico (mantenuto identico per funzionalità) --}}
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

{{-- Modal conferma eliminazione centro --}}
<div class="modal fade" id="modalEliminaCentro" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Conferma Eliminazione Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Questa azione è irreversibile.
                </div>
                <p>Sei sicuro di voler eliminare il centro assistenza:</p>
                <p class="fw-bold text-danger">{{ $centro->nome }}</p>
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
                <form action="{{ route('admin.centri.destroy', $centro) }}" method="POST" class="d-inline">
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

@endsection

{{-- JavaScript completo per gestione centro assistenza - Versione Admin --}}
@push('scripts')
<script>
/**
 * JavaScript per gestione centro assistenza - Versione Admin con stile migliorato
 * Mantiene tutte le funzionalità amministrative originali con UX migliorata
 */

document.addEventListener('DOMContentLoaded', function() {
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
});

console.log('JavaScript admin centro assistenza caricato con stile migliorato - Versione completa');
</script>
@endpush

{{-- CSS personalizzato che combina lo stile pubblico con le funzionalità admin --}}
@push('styles')
<style>
/* === STILI BASE EREDISTATI DALLA VISTA PUBBLICA === */

/* Stili per le card principali */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Badge personalizzati con temi admin */
.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* Bottoni di contatto con testo ben visibile - tema admin */
.btn.btn-danger {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

.btn.btn-warning {
    background: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000000 !important;
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
}

/* Animazioni hover per i tecnici */
.border.rounded.p-3:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Icone colorate per i contatti e admin */
.bi-telephone {
    color: #28a745 !important;
}

.bi-envelope {
    color: #17a2b8 !important;
}

.bi-map {
    color: #007bff !important;
}

.bi-shield-lock {
    color: #dc3545 !important;
}

.bi-gear {
    color: #ffc107 !important;
}

/* Notifiche temporanee con animazione */
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

/* Modal personalizzati per admin */
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

/* Alert personalizzati */
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

/* Stile per gli avatar dei tecnici */
.bg-success.text-white.rounded-circle {
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

/* Miglioramenti per i badge */
.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
    border-radius: 0.5rem;
}

/* === RESPONSIVE DESIGN === */

/* Responsive design per mobile */
@media (max-width: 768px) {
    .d-flex.flex-wrap.gap-2 {
        justify-content: stretch !important;
    }
    
    .d-flex.flex-wrap.gap-2 > * {
        flex: 1 !important;
        min-width: 120px;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
    
    /* Notifiche mobile */
    .notifica-temp {
        left: 1rem;
        right: 1rem;
        min-width: auto;
    }
    
    /* Miglioramenti modal mobile */
    .modal-dialog {
        margin: 10px;
    }
    
    /* Pulsanti azioni tecnici mobile */
    .d-flex.gap-1 {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-1 .btn {
        flex: 1;
        min-width: calc(33.333% - 0.167rem);
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

/* === ANIMAZIONI E TRANSIZIONI === */

/* Transizioni smooth per tutti gli elementi */
.btn,
.card,
.badge,
.alert {
    transition: all 0.2s ease;
}

/* Riduci animazioni se richiesto dall'utente */
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
    
    .card-custom:hover {
        transform: none;
    }
}

/* === STILI UTILITÀ ADMIN === */

/* Bordi personalizzati */
.border-start-admin {
    border-left: 4px solid #dc3545 !important;
}

.border-start-success {
    border-left: 4px solid #28a745 !important;
}

.border-start-warning {
    border-left: 4px solid #ffc107 !important;
}

/* Spaziatura migliorata */
.gap-admin {
    gap: 0.75rem !important;
}

/* Scroll smooth per link interni */
html {
    scroll-behavior: smooth;
}

/* Miglioramenti tipografici */
h1, h2, h3, h4, h5, h6 {
    line-height: 1.3;
}

small, .small {
    line-height: 1.4;
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
</style>
@endpush