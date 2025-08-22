{{-- 
    Vista dettaglio prodotto per amministratori - CORRETTA
    Percorso: resources/views/admin/prodotti/show.blade.php
    Accesso: Solo livello 4 (Amministratori)
--}}

@extends('layouts.app')

@section('title', 'Dettaglio Prodotto - ' . $prodotto->nome)

@section('content')
<div class="container-fluid">
    
    {{-- === HEADER CON BREADCRUMB === --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.prodotti.index') }}" class="text-decoration-none">
                            <i class="bi bi-box me-1"></i>Gestione Prodotti
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $prodotto->nome }}</li>
                </ol>
            </nav>

            {{-- Header con azioni --}}
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-box text-primary me-2"></i>
                        {{ $prodotto->nome }}
                        @if(!$prodotto->attivo)
                            <span class="badge bg-danger ms-2">INATTIVO</span>
                        @else
                            <span class="badge bg-success ms-2">ATTIVO</span>
                        @endif
                    </h1>
                    <p class="text-muted mb-0">
                        Modello: <strong>{{ $prodotto->modello }}</strong> • 
                        Categoria: <strong>{{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}</strong>
                        @if($prodotto->prezzo)
                            • Prezzo: <strong>€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}</strong>
                        @endif
                    </p>
                </div>

                {{-- Pulsanti di azione --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.prodotti.edit', $prodotto) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    
                    {{-- Toggle status --}}
                    @if(Route::has('admin.prodotti.toggle-status'))
                    <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="btn {{ $prodotto->attivo ? 'btn-danger' : 'btn-success' }}"
                                onclick="return confirm('Sei sicuro di voler {{ $prodotto->attivo ? 'disattivare' : 'attivare' }} questo prodotto?')">
                            <i class="bi bi-{{ $prodotto->attivo ? 'pause' : 'play' }} me-1"></i>
                            {{ $prodotto->attivo ? 'Disattiva' : 'Attiva' }}
                        </button>
                    </form>
                    @endif

                    {{-- Vista pubblica --}}
                    <a href="{{ route('prodotti.show', $prodotto) }}" 
                       class="btn btn-outline-primary" 
                       target="_blank">
                        <i class="bi bi-eye me-1"></i>Vista Pubblica
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- === STATISTICHE RAPIDE === --}}
    @if(isset($statistiche))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $statistiche['malfunzionamenti_totali'] }}</h4>
                            <p class="card-text">Malfunzionamenti Totali</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $statistiche['malfunzionamenti_critici'] }}</h4>
                            <p class="card-text">Problemi Critici</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-octagon display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $statistiche['segnalazioni_totali'] }}</h4>
                            <p class="card-text">Segnalazioni Totali</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-flag display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-{{ isset($metriche['livello_criticita']['colore']) ? $metriche['livello_criticita']['colore'] : 'secondary' }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ isset($metriche['livello_criticita']['livello']) ? ucfirst($metriche['livello_criticita']['livello']) : 'N/A' }}</h4>
                            <p class="card-text">Livello Criticità</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-speedometer2 display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- === CONTENUTO PRINCIPALE === --}}
    <div class="row">
        
        {{-- === INFORMAZIONI PRODOTTO === --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informazioni Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Foto prodotto --}}
                        @if($prodotto->foto)
                        <div class="col-md-4 mb-3">
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 alt="Foto {{ $prodotto->nome }}"
                                 class="img-fluid rounded shadow-sm"
                                 onerror="this.src='{{ asset('images/placeholder-product.png') }}'; this.onerror=null;">
                        </div>
                        @endif
                        
                        {{-- Dettagli --}}
                        <div class="col-md-{{ $prodotto->foto ? '8' : '12' }}">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nome:</th>
                                    <td>{{ $prodotto->nome }}</td>
                                </tr>
                                <tr>
                                    <th>Modello:</th>
                                    <td><code>{{ $prodotto->modello }}</code></td>
                                </tr>
                                <tr>
                                    <th>Categoria:</th>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($prodotto->prezzo)
                                <tr>
                                    <th>Prezzo:</th>
                                    <td class="fw-bold text-success">€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $prodotto->attivo ? 'success' : 'danger' }}">
                                            {{ $prodotto->attivo ? 'ATTIVO' : 'INATTIVO' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Creato:</th>
                                    <td>{{ $prodotto->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Ultimo aggiornamento:</th>
                                    <td>{{ $prodotto->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Descrizione --}}
                    @if($prodotto->descrizione)
                    <div class="mt-3">
                        <h6>Descrizione:</h6>
                        <p class="text-muted">{{ $prodotto->descrizione }}</p>
                    </div>
                    @endif

                    {{-- Note tecniche --}}
                    @if($prodotto->note_tecniche)
                    <div class="mt-3">
                        <h6>Note Tecniche:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($prodotto->note_tecniche)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Modalità installazione --}}
                    @if($prodotto->modalita_installazione)
                    <div class="mt-3">
                        <h6>Modalità di Installazione:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_installazione)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Modalità d'uso --}}
                    @if($prodotto->modalita_uso)
                    <div class="mt-3">
                        <h6>Modalità d'Uso:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_uso)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- === MALFUNZIONAMENTI === --}}
            @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bug me-2"></i>Malfunzionamenti e Soluzioni
                    </h5>
                    <span class="badge bg-warning">{{ $prodotto->malfunzionamenti->count() }} problemi</span>
                </div>
                <div class="card-body">
                    @foreach($prodotto->malfunzionamenti as $malfunzionamento)
                    <div class="border rounded p-3 mb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-1">
                                {{-- Badge gravità --}}
                                <span class="badge bg-{{ 
                                    $malfunzionamento->gravita === 'critica' ? 'danger' : 
                                    ($malfunzionamento->gravita === 'alta' ? 'warning' : 
                                    ($malfunzionamento->gravita === 'media' ? 'info' : 'secondary')) 
                                }} me-2">
                                    {{ ucfirst($malfunzionamento->gravita) }}
                                </span>
                                {{ $malfunzionamento->descrizione }}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-flag me-1"></i>{{ $malfunzionamento->numero_segnalazioni }} segnalazioni
                            </small>
                        </div>
                        
                        {{-- Soluzione tecnica --}}
                        @if($malfunzionamento->soluzione_tecnica)
                        <div class="mt-2">
                            <strong class="text-success">
                                <i class="bi bi-tools me-1"></i>Soluzione:
                            </strong>
                            <p class="mb-1 text-muted">{{ $malfunzionamento->soluzione_tecnica }}</p>
                        </div>
                        @endif

                        {{-- Info creazione/modifica --}}
                        <div class="mt-2">
                            <small class="text-muted">
                                Creato: {{ $malfunzionamento->created_at->format('d/m/Y') }}
                                @if($malfunzionamento->creatoBy)
                                    da {{ $malfunzionamento->creatoBy->nome_completo }}
                                @endif
                                @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                                    • Aggiornato: {{ $malfunzionamento->updated_at->format('d/m/Y') }}
                                    @if($malfunzionamento->modificatoBy)
                                        da {{ $malfunzionamento->modificatoBy->nome_completo }}
                                    @endif
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- === SIDEBAR === --}}
        <div class="col-lg-4">
            
            {{-- Staff assegnato --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>Staff Assegnato
                    </h6>
                </div>
                <div class="card-body">
                    @if($prodotto->staffAssegnato)
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $prodotto->staffAssegnato->nome_completo }}</h6>
                                <small class="text-muted">Staff Aziendale</small>
                            </div>
                        </div>
                        
                        {{-- Pulsante per cambiare staff --}}
                        <div class="mt-3">
                            <button class="btn btn-outline-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#changeStaffModal">
                                <i class="bi bi-arrow-repeat me-1"></i>Riassegna
                            </button>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-person-x display-6 text-muted"></i>
                            <p class="text-muted mt-2 mb-3">Nessuno staff assegnato</p>
                            
                            <button class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#assignStaffModal">
                                <i class="bi bi-person-plus me-1"></i>Assegna Staff
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Metriche performance --}}
            @if(isset($metriche))
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Metriche Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Giorni dal lancio:</small>
                            <small class="fw-bold">{{ $metriche['giorni_dal_lancio'] }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Media segnalazioni:</small>
                            <small class="fw-bold">{{ $metriche['media_segnalazioni_per_malfunzionamento'] }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Frequenza problemi:</small>
                            <small class="fw-bold">{{ $metriche['frequenza_problemi'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Prodotti correlati --}}
            @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-link-45deg me-2"></i>Prodotti Correlati
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($prodottiCorrelati as $correlato)
                    <div class="d-flex align-items-center mb-2 {{ $loop->last ? '' : 'border-bottom pb-2' }}">
                        <div class="me-3">
                            @if($correlato->foto)
                                <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                     alt="{{ $correlato->nome }}"
                                     class="rounded"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     onerror="this.src='{{ asset('images/placeholder-product.png') }}'; this.onerror=null;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ route('admin.prodotti.show', $correlato) }}" 
                               class="text-decoration-none">
                                <small class="fw-bold">{{ $correlato->nome }}</small>
                            </a>
                            <br>
                            <small class="text-muted">
                                {{ $correlato->malfunzionamenti_count ?? 0 }} problemi
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- === MODALS CORRETTI === --}}

{{-- Modal assegnazione staff --}}
<div class="modal fade" id="assignStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assegna Staff al Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- CORREZIONE: Verifica che la route esista --}}
            @if(Route::has('admin.assegna.prodotto'))
                <form action="{{ route('admin.assegna.prodotto') }}" method="POST">
            @else
                {{-- Fallback alla route di update --}}
                <form action="{{ route('admin.prodotti.update', $prodotto) }}" method="POST">
                @method('PUT')
            @endif
                @csrf
                <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted mb-3">
                            <strong>Prodotto:</strong> {{ $prodotto->nome }} - {{ $prodotto->modello }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Seleziona Staff:</label>
                        <select name="staff_assegnato_id" id="staff_id" class="form-select" required>
                            <option value="">Seleziona...</option>
                            {{-- Genera lista staff dal controller o query diretta --}}
                            @php
                                $staffDisponibili = $staffDisponibili ?? \App\Models\User::where('livello_accesso', '3')->select('id', 'nome', 'cognome')->orderBy('nome')->get();
                            @endphp
                            @foreach($staffDisponibili as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->nome_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Assegna Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal cambio staff --}}
<div class="modal fade" id="changeStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riassegna Staff al Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- CORREZIONE: Verifica che la route esista --}}
            @if(Route::has('admin.assegna.prodotto'))
                <form action="{{ route('admin.assegna.prodotto') }}" method="POST">
            @else
                {{-- Fallback alla route di update --}}
                <form action="{{ route('admin.prodotti.update', $prodotto) }}" method="POST">
                @method('PUT')
            @endif
                @csrf
                <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted">
                            <strong>Prodotto:</strong> {{ $prodotto->nome }} - {{ $prodotto->modello }}
                        </p>
                        <p class="text-muted">
                            Staff attualmente assegnato: 
                            <strong>{{ $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : 'Nessuno' }}</strong>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="new_staff_id" class="form-label">Nuovo Staff:</label>
                        <select name="staff_assegnato_id" id="new_staff_id" class="form-select">
                            <option value="">Rimuovi assegnazione</option>
                            @php
                                $staffDisponibili = $staffDisponibili ?? \App\Models\User::where('livello_accesso', '3')->select('id', 'nome', 'cognome')->orderBy('nome')->get();
                            @endphp
                            @foreach($staffDisponibili as $staff)
                                <option value="{{ $staff->id }}"
                                        {{ $prodotto->staff_assegnato_id == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Lascia vuoto per rimuovere l'assegnazione corrente
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning">Riassegna</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- === STILI PERSONALIZZATI === --}}
@push('styles')
<style>
/* Card personalizzate */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Badge personalizzati */
.badge {
    font-size: 0.75em;
}

/* Tabelle borderless personalizzate */
.table-borderless th {
    font-weight: 600;
    color: #6c757d;
}

.table-borderless td {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}

/* Debug route */
.route-debug {
    position: fixed;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px;
    border-radius: 5px;
    font-size: 12px;
    z-index: 9999;
}
</style>
@endpush

{{-- === JAVASCRIPT PERSONALIZZATO === --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // === DEBUG ROUTE CHECKER ===
    const routes = {
        'admin.assegna.prodotto': '{{ Route::has("admin.assegna.prodotto") ? route("admin.assegna.prodotto") : "NON_TROVATA" }}',
        'admin.prodotti.update': '{{ route("admin.prodotti.update", $prodotto) }}',
        'admin.prodotti.toggle-status': '{{ Route::has("admin.prodotti.toggle-status") ? "ESISTE" : "NON_TROVATA" }}'
    };
    
    console.log('🔍 Route disponibili:', routes);
    
    // Mostra debug se necessario (rimuovi in produzione)
    if (routes['admin.assegna.prodotto'] === 'NON_TROVATA') {
        console.warn('⚠️ Route admin.assegna.prodotto non trovata! Verrà usato fallback.');
    }
    
    // === GESTIONE FORM SUBMISSION ===
    const forms = document.querySelectorAll('form[action*="assegna"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const formData = new FormData(form);
            
            console.log('📤 Form submission:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            // Validazione
            const prodottoId = formData.get('prodotto_id');
            if (!prodottoId) {
                alert('ERRORE: ID prodotto mancante!');
                e.preventDefault();
                return false;
            }
            
            // Loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
                
                // Reset dopo 5 secondi (fallback)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.innerHTML.replace(/Salvando\.\.\./, 'Riprova');
                }, 5000);
            }
        });
    });
    
    // === GESTIONE TOGGLE STATUS ===
    const toggleForms = document.querySelectorAll('form[action*="toggle-status"]');
    
    toggleForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            const isActive = button.textContent.trim().includes('Disattiva');
            
            if (!confirm(`Sei sicuro di voler ${isActive ? 'disattivare' : 'attivare'} questo prodotto?`)) {
                e.preventDefault();
                return false;
            }
            
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Attendere...';
        });
    });
    
    // === AUTO-FOCUS SUI MODALS ===
    const assignModal = document.getElementById('assignStaffModal');
    const changeModal = document.getElementById('changeStaffModal');
    
    if (assignModal) {
        assignModal.addEventListener('shown.bs.modal', function () {
            document.getElementById('staff_id')?.focus();
        });
    }
    
    if (changeModal) {
        changeModal.addEventListener('shown.bs.modal', function () {
            document.getElementById('new_staff_id')?.focus();
        });
    }
    
    // === GESTIONE ERRORI IMMAGINI ===
    const images = document.querySelectorAll('img[src*="storage"]');
    images.forEach(img => {
        img.addEventListener('error', function() {
            console.warn('🖼️ Immagine non caricata:', this.src);
            this.src = '{{ asset("images/placeholder-product.png") }}';
            this.onerror = null; // Previeni loop infinito
        });
    });
    
    // === TOOLTIP BOOTSTRAP (se presente) ===
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // === NOTIFICAZIONI SUCCESS/ERROR ===
    @if(session('success'))
        showNotification('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showNotification('error', '{{ session('error') }}');
    @endif
    
    @if($errors->any())
        @foreach($errors->all() as $error)
            showNotification('error', '{{ $error }}');
        @endforeach
    @endif
});

// === FUNZIONI UTILITY ===

/**
 * Mostra notifiche toast
 */
function showNotification(type, message) {
    // Se Bootstrap Toast è disponibile
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Container per toast
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        
        toast.show();
        
        // Rimuovi elemento dopo che è stato nascosto
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    } else {
        // Fallback ad alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Conferma azione con sweet alert (se disponibile) o confirm normale
 */
function confirmAction(message, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Conferma azione',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sì, procedi',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    } else {
        if (confirm(message) && typeof callback === 'function') {
            callback();
        }
    }
}

/**
 * Copia testo negli appunti
 */
function copyToClipboard(text, successMessage = 'Copiato negli appunti!') {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('success', successMessage);
        }).catch(err => {
            console.error('Errore copia clipboard:', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('success', 'Copiato negli appunti!');
        } else {
            throw new Error('execCommand fallito');
        }
    } catch (err) {
        console.error('Fallback copy fallito:', err);
        showNotification('error', 'Impossibile copiare negli appunti');
    }
    
    document.body.removeChild(textArea);
}

/**
 * Refresh statistiche via AJAX (opzionale)
 */
function refreshStats() {
    const statsCards = document.querySelectorAll('.card[class*="bg-"]');
    
    statsCards.forEach(card => {
        card.style.opacity = '0.7';
    });
    
    // Simula refresh con delay
    setTimeout(() => {
        statsCards.forEach(card => {
            card.style.opacity = '1';
        });
        showNotification('success', 'Statistiche aggiornate');
    }, 1000);
}

/**
 * Controllo connettività e stato server
 */
function checkServerStatus() {
    fetch('{{ route("home") }}', { 
        method: 'HEAD',
        cache: 'no-cache'
    })
    .then(response => {
        if (response.ok) {
            console.log('✅ Server raggiungibile');
        } else {
            console.warn('⚠️ Server risponde ma con errori');
        }
    })
    .catch(error => {
        console.error('❌ Server non raggiungibile:', error);
        showNotification('error', 'Problemi di connessione al server');
    });
}

// Controlla stato server ogni 5 minuti
setInterval(checkServerStatus, 5 * 60 * 1000);

/**
 * Debug info per sviluppo (rimuovi in produzione)
 */
function showDebugInfo() {
    if (console && console.table) {
        const debugData = {
            'Prodotto ID': '{{ $prodotto->id }}',
            'Prodotto Nome': '{{ $prodotto->nome }}',
            'Staff Assegnato': '{{ $prodotto->staffAssegnato ? $prodotto->staffAssegnato->nome_completo : "Nessuno" }}',
            'Malfunzionamenti': '{{ $prodotto->malfunzionamenti ? $prodotto->malfunzionamenti->count() : 0 }}',
            'Route Assegna': '{{ Route::has("admin.assegna.prodotto") ? "Disponibile" : "Non trovata" }}',
            'Laravel Version': '{{ app()->version() }}',
            'Environment': '{{ app()->environment() }}'
        };
        
        console.table(debugData);
    }
}

// Mostra debug info in sviluppo
@if(app()->environment('local'))
    console.log('🐛 Debug mode attivo');
    showDebugInfo();
@endif
</script>

{{-- Script aggiuntivi per funzionalità avanzate (opzionali) --}}
@if(config('app.debug'))
<script>
// === PERFORMANCE MONITORING ===
window.addEventListener('load', function() {
    if (performance && performance.timing) {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log(`⏱️ Pagina caricata in: ${loadTime}ms`);
        
        if (loadTime > 3000) {
            console.warn('🐌 Caricamento lento rilevato');
        }
    }
});

// === MEMORY USAGE ===
if (performance && performance.memory) {
    setInterval(() => {
        const memory = performance.memory;
        const used = Math.round(memory.usedJSHeapSize / 1048576);
        const total = Math.round(memory.totalJSHeapSize / 1048576);
        
        console.log(`💾 Memoria JS: ${used}MB / ${total}MB`);
        
        if (used > 100) {
            console.warn('⚠️ Elevato uso di memoria JavaScript');
        }
    }, 30000); // Ogni 30 secondi
}
</script>
@endif

{{-- Inclusione librerie esterne se necessarie --}}
@if(config('app.env') === 'production')
{{-- Script per analytics, monitoraggio errori, etc. --}}
@endif
@endpush

{{-- === SEZIONE MODALI AGGIUNTIVI (se necessari) === --}}

{{-- Modal conferma eliminazione (se implementato) --}}
@if(false) {{-- Abilitare se serve --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questo prodotto?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Attenzione:</strong> Questa azione non può essere annullata.
                    Tutti i malfunzionamenti associati verranno mantenuti ma il prodotto
                    non sarà più visibile nel catalogo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Debug panel (solo sviluppo) --}}
@if(app()->environment('local') && request()->get('debug'))
<div class="route-debug">
    <strong>🔧 Debug Panel</strong><br>
    <small>
        Route Check: {{ Route::has('admin.assegna.prodotto') ? '✅' : '❌' }}<br>
        Prodotto ID: {{ $prodotto->id }}<br>
        Staff: {{ $prodotto->staff_assegnato_id ?? 'NULL' }}<br>
        Env: {{ app()->environment() }}
    </small>
</div>
@endif