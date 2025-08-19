{{-- 
    File: resources/views/admin/centri/edit.blade.php
    Descrizione: Form per la modifica di un centro di assistenza esistente
    Livello accesso: Solo Amministratori (Livello 4)
    
    Funzionalità:
    - Form precompilato con i dati esistenti del centro
    - Validazione lato client e server
    - Conferma prima della cancellazione
    - Statistiche del centro nella sidebar
--}}

@extends('admin.dashboard')

{{-- Titolo dinamico della pagina --}}
@section('title', 'Modifica Centro: ' . $centro->nome)

{{-- Breadcrumb per la navigazione --}}
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.centri.index') }}">
                <i class="bi bi-building"></i> Centri Assistenza
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.centri.show', $centro) }}">
                <i class="bi bi-eye"></i> {{ Str::limit($centro->nome, 30) }}
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="bi bi-pencil"></i> Modifica
        </li>
    </ol>
</nav>
@endsection

{{-- Contenuto principale della pagina --}}
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header della pagina con titolo e azioni --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="bi bi-pencil-square me-2"></i>
                        Modifica Centro di Assistenza
                    </h1>
                    <p class="text-muted mb-0">
                        Aggiorna le informazioni per: <strong>{{ $centro->nome }}</strong>
                    </p>
                </div>
                
                {{-- Gruppo pulsanti azioni --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.centri.show', $centro) }}" 
                       class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye me-1"></i>
                        Visualizza
                    </a>
                    <a href="{{ route('admin.centri.index') }}" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>
                        Lista Centri
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Form di modifica --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-form-text me-2"></i>
                                Modifica Dati Centro
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            {{-- Form per la modifica del centro --}}
                            <form action="{{ route('admin.centri.update', $centro) }}" 
                                  method="POST" 
                                  id="formModificaCentro"
                                  novalidate>
                                @csrf {{-- Token di sicurezza Laravel --}}
                                @method('PUT') {{-- Override del metodo HTTP per UPDATE --}}
                                
                                {{-- Nome del centro --}}
                                <div class="mb-3">
                                    <label for="nome" class="form-label required">
                                        <i class="bi bi-building-fill me-1"></i>
                                        Nome Centro
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" 
                                           name="nome" 
                                           value="{{ old('nome', $centro->nome) }}"
                                           placeholder="Es: Centro Assistenza Roma Nord"
                                           maxlength="255"
                                           required>
                                    
                                    @error('nome')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    
                                    <small class="form-text text-muted">
                                        <span id="nomeCount">{{ strlen($centro->nome) }}</span>/255 caratteri
                                    </small>
                                </div>

                                {{-- Indirizzo --}}
                                <div class="mb-3">
                                    <label for="indirizzo" class="form-label required">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        Indirizzo
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('indirizzo') is-invalid @enderror" 
                                           id="indirizzo" 
                                           name="indirizzo" 
                                           value="{{ old('indirizzo', $centro->indirizzo) }}"
                                           placeholder="Es: Via Roma, 123"
                                           maxlength="255"
                                           required>
                                    
                                    @error('indirizzo')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Riga per Città, Provincia e CAP --}}
                                <div class="row">
                                    {{-- Città --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="citta" class="form-label required">
                                            <i class="bi bi-pin-map-fill me-1"></i>
                                            Città
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('citta') is-invalid @enderror" 
                                               id="citta" 
                                               name="citta" 
                                               value="{{ old('citta', $centro->citta) }}"
                                               placeholder="Es: Roma"
                                               maxlength="100"
                                               required>
                                        
                                        @error('citta')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Provincia --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="provincia" class="form-label required">
                                            <i class="bi bi-map me-1"></i>
                                            Provincia
                                        </label>
                                        <select class="form-select @error('provincia') is-invalid @enderror" 
                                                id="provincia" 
                                                name="provincia" 
                                                required>
                                            <option value="">Seleziona...</option>
                                            {{-- Lista province con selezione del valore attuale --}}
                                            @php
                                                $province = [
                                                    'AG' => 'AG - Agrigento', 'AL' => 'AL - Alessandria', 'AN' => 'AN - Ancona', 
                                                    'AO' => 'AO - Aosta', 'AR' => 'AR - Arezzo', 'AP' => 'AP - Ascoli Piceno',
                                                    'AT' => 'AT - Asti', 'AV' => 'AV - Avellino', 'BA' => 'BA - Bari',
                                                    'BT' => 'BT - Barletta-Andria-Trani', 'BL' => 'BL - Belluno', 'BN' => 'BN - Benevento',
                                                    'BG' => 'BG - Bergamo', 'BI' => 'BI - Biella', 'BO' => 'BO - Bologna',
                                                    'BZ' => 'BZ - Bolzano', 'BS' => 'BS - Brescia', 'BR' => 'BR - Brindisi',
                                                    'CA' => 'CA - Cagliari', 'CL' => 'CL - Caltanissetta', 'CB' => 'CB - Campobasso',
                                                    'CE' => 'CE - Caserta', 'CT' => 'CT - Catania', 'CZ' => 'CZ - Catanzaro',
                                                    'CH' => 'CH - Chieti', 'CO' => 'CO - Como', 'CS' => 'CS - Cosenza',
                                                    'CR' => 'CR - Cremona', 'KR' => 'KR - Crotone', 'CN' => 'CN - Cuneo',
                                                    'EN' => 'EN - Enna', 'FM' => 'FM - Fermo', 'FE' => 'FE - Ferrara',
                                                    'FI' => 'FI - Firenze', 'FG' => 'FG - Foggia', 'FC' => 'FC - Forlì-Cesena',
                                                    'FR' => 'FR - Frosinone', 'GE' => 'GE - Genova', 'GO' => 'GO - Gorizia',
                                                    'GR' => 'GR - Grosseto', 'IM' => 'IM - Imperia', 'IS' => 'IS - Isernia',
                                                    'AQ' => 'AQ - L\'Aquila', 'SP' => 'SP - La Spezia', 'LT' => 'LT - Latina',
                                                    'LE' => 'LE - Lecce', 'LC' => 'LC - Lecco', 'LI' => 'LI - Livorno',
                                                    'LO' => 'LO - Lodi', 'LU' => 'LU - Lucca', 'MC' => 'MC - Macerata',
                                                    'MN' => 'MN - Mantova', 'MS' => 'MS - Massa-Carrara', 'MT' => 'MT - Matera',
                                                    'ME' => 'ME - Messina', 'MI' => 'MI - Milano', 'MO' => 'MO - Modena',
                                                    'MB' => 'MB - Monza e Brianza', 'NA' => 'NA - Napoli', 'NO' => 'NO - Novara',
                                                    'NU' => 'NU - Nuoro', 'OR' => 'OR - Oristano', 'PD' => 'PD - Padova',
                                                    'PA' => 'PA - Palermo', 'PR' => 'PR - Parma', 'PV' => 'PV - Pavia',
                                                    'PG' => 'PG - Perugia', 'PU' => 'PU - Pesaro e Urbino', 'PE' => 'PE - Pescara',
                                                    'PC' => 'PC - Piacenza', 'PI' => 'PI - Pisa', 'PT' => 'PT - Pistoia',
                                                    'PN' => 'PN - Pordenone', 'PZ' => 'PZ - Potenza', 'PO' => 'PO - Prato',
                                                    'RG' => 'RG - Ragusa', 'RA' => 'RA - Ravenna', 'RC' => 'RC - Reggio Calabria',
                                                    'RE' => 'RE - Reggio Emilia', 'RI' => 'RI - Rieti', 'RN' => 'RN - Rimini',
                                                    'RM' => 'RM - Roma', 'RO' => 'RO - Rovigo', 'SA' => 'SA - Salerno',
                                                    'SS' => 'SS - Sassari', 'SV' => 'SV - Savona', 'SI' => 'SI - Siena',
                                                    'SR' => 'SR - Siracusa', 'SO' => 'SO - Sondrio', 'TA' => 'TA - Taranto',
                                                    'TE' => 'TE - Teramo', 'TR' => 'TR - Terni', 'TO' => 'TO - Torino',
                                                    'TP' => 'TP - Trapani', 'TN' => 'TN - Trento', 'TV' => 'TV - Treviso',
                                                    'TS' => 'TS - Trieste', 'UD' => 'UD - Udine', 'VA' => 'VA - Varese',
                                                    'VE' => 'VE - Venezia', 'VB' => 'VB - Verbano-Cusio-Ossola', 'VC' => 'VC - Vercelli',
                                                    'VR' => 'VR - Verona', 'VV' => 'VV - Vibo Valentia', 'VI' => 'VI - Vicenza',
                                                    'VT' => 'VT - Viterbo'
                                                ];
                                            @endphp
                                            
                                            @foreach($province as $sigla => $nome)
                                                <option value="{{ $sigla }}" 
                                                        {{ old('provincia', $centro->provincia) == $sigla ? 'selected' : '' }}>
                                                    {{ $nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        @error('provincia')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- CAP --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="cap" class="form-label">
                                            <i class="bi bi-mailbox me-1"></i>
                                            CAP
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('cap') is-invalid @enderror" 
                                               id="cap" 
                                               name="cap" 
                                               value="{{ old('cap', $centro->cap) }}"
                                               placeholder="Es: 00100"
                                               pattern="[0-9]{5}"
                                               maxlength="5">
                                        
                                        @error('cap')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Riga per Telefono ed Email --}}
                                <div class="row">
                                    {{-- Telefono --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">
                                            <i class="bi bi-telephone-fill me-1"></i>
                                            Telefono
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" 
                                               name="telefono" 
                                               value="{{ old('telefono', $centro->telefono) }}"
                                               placeholder="Es: 06 1234567 oppure 347 1234567"
                                               maxlength="20">
                                        
                                        @error('telefono')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        @if($centro->telefono)
                                            <small class="form-text text-muted">
                                                Attuale: {{ $centro->telefono_formattato }}
                                            </small>
                                        @endif
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            <i class="bi bi-envelope-fill me-1"></i>
                                            Email
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $centro->email) }}"
                                               placeholder="Es: centro@assistenza.it"
                                               maxlength="255">
                                        
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Informazioni sui cambiamenti --}}
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Attenzione:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Le modifiche saranno immediatamente visibili a tutti gli utenti</li>
                                        <li>I tecnici assegnati a questo centro verranno notificati delle modifiche ai contatti</li>
                                        <li>Creato il: <strong>{{ $centro->created_at->format('d/m/Y H:i') }}</strong></li>
                                        @if($centro->updated_at != $centro->created_at)
                                            <li>Ultima modifica: <strong>{{ $centro->updated_at->format('d/m/Y H:i') }}</strong></li>
                                        @endif
                                    </ul>
                                </div>

                                {{-- Pulsanti del form --}}
                                <div class="d-flex gap-2 justify-content-between">
                                    {{-- Pulsante eliminazione (a sinistra) --}}
                                    <button type="button" 
                                            class="btn btn-outline-danger"
                                            id="btnElimina"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalElimina">
                                        <i class="bi bi-trash me-1"></i>
                                        Elimina Centro
                                    </button>
                                    
                                    {{-- Pulsanti salva/annulla (a destra) --}}
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.centri.show', $centro) }}" 
                                           class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Annulla
                                        </a>
                                        
                                        <button type="submit" 
                                                class="btn btn-warning text-dark" 
                                                id="btnSalva">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Salva Modifiche
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sidebar con statistiche e informazioni --}}
                <div class="col-lg-4">
                    {{-- Statistiche del centro --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-graph-up me-2"></i>
                                Statistiche Centro
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ $centro->numero_tecnici }}</h4>
                                        <small class="text-muted">Tecnici Assegnati</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-1">
                                        {{ $centro->created_at->diffInDays() }}
                                    </h4>
                                    <small class="text-muted">Giorni di Attività</small>
                                </div>
                            </div>
                            
                            @if($centro->hasTecnici())
                                <hr>
                                <h6 class="text-muted mb-2">Specializzazioni Disponibili:</h6>
                                @if($centro->statistiche['specializzazioni'])
                                    @foreach($centro->statistiche['specializzazioni'] as $spec => $count)
                                        <span class="badge bg-secondary me-1 mb-1">
                                            {{ ucfirst($spec) }} ({{ $count }})
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-muted small">Nessuna specializzazione specificata</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Tecnici assegnati --}}
                    @if($centro->hasTecnici())
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-people me-2"></i>
                                    Tecnici Assegnati
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($centro->tecnici_con_specializzazioni as $tecnico)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $tecnico->nome_completo }}</strong>
                                            @if($tecnico->specializzazione)
                                                <br><small class="text-muted">{{ $tecnico->specializzazione }}</small>
                                            @endif
                                        </div>
                                        <span class="badge bg-primary">Tecnico</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Azioni rapide --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>
                                Azioni Rapide
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.centri.show', $centro) }}" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye me-1"></i>
                                    Visualizza Dettagli
                                </a>
                                
                                <a href="{{ $centro->google_maps_link }}" 
                                   target="_blank" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Apri in Google Maps
                                </a>
                                
                                @if($centro->hasTecnici())
                                    <a href="{{ route('admin.users.index', ['centro_id' => $centro->id]) }}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-people me-1"></i>
                                        Gestisci Tecnici
                                    </a>
                                @endif
                                
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm"
                                        onclick="copiaIndirizzo()">
                                    <i class="bi bi-clipboard me-1"></i>
                                    Copia Indirizzo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal per conferma eliminazione --}}
<div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminaLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Questa azione è irreversibile.
                </div>
                
                <p>Stai per eliminare il centro di assistenza:</p>
                <div class="bg-light p-3 rounded">
                    <strong>{{ $centro->nome }}</strong><br>
                    {{ $centro->indirizzo_completo }}
                </div>
                
                @if($centro->hasTecnici())
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-people me-2"></i>
                        <strong>Il centro ha {{ $centro->numero_tecnici }} tecnici assegnati.</strong><br>
                        Prima di eliminare il centro, devi riassegnare i tecnici ad altri centri.
                    </div>
                @else
                    <p class="mt-3 text-muted">
                        Il centro non ha tecnici assegnati, può essere eliminato in sicurezza.
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Annulla
                </button>
                
                @if(!$centro->hasTecnici())
                    <form action="{{ route('admin.centri.destroy', $centro) }}" 
                          method="POST" 
                          class="d-inline"
                          id="formElimina">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            Elimina Definitivamente
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.users.index', ['centro_id' => $centro->id]) }}" 
                       class="btn btn-warning">
                        <i class="bi bi-people me-1"></i>
                        Gestisci Tecnici Prima
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Script JavaScript personalizzati --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === CONTATORE CARATTERI PER IL NOME ===
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    if (nomeInput && nomeCounter) {
        function updateNomeCounter() {
            const currentLength = nomeInput.value.length;
            nomeCounter.textContent = currentLength;
            
            if (currentLength > 240) {
                nomeCounter.className = 'text-danger fw-bold';
            } else if (currentLength > 200) {
                nomeCounter.className = 'text-warning';
            } else {
                nomeCounter.className = 'text-muted';
            }
        }
        
        nomeInput.addEventListener('input', updateNomeCounter);
        updateNomeCounter();
    }
    
    // === VALIDAZIONE CAP (solo numeri, 5 cifre) ===
    const capInput = document.getElementById('cap');
    if (capInput) {
        capInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substr(0, 5);
            }
            e.target.value = value;
        });
    }
    
    // === VALIDAZIONE TELEFONO ===
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
        });
    }
    
    // === VALIDAZIONE FORM MODIFICA ===
    const form = document.getElementById('formModificaCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        form.addEventListener('submit', function(e) {
            // Mostra spinner durante l'invio
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            // Validazione campi obbligatori
            const nome = document.getElementById('nome').value.trim();
            const indirizzo = document.getElementById('indirizzo').value.trim();
            const citta = document.getElementById('citta').value.trim();
            const provincia = document.getElementById('provincia').value;
            
            if (!nome || !indirizzo || !citta || !provincia) {
                e.preventDefault();
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                showAlert('Errore', 'Compila tutti i campi obbligatori', 'danger');
                return;
            }
            
            // Validazione CAP se presente
            const cap = document.getElementById('cap').value.trim();
            if (cap && (cap.length !== 5 || !/^\d{5}$/.test(cap))) {
                e.preventDefault();
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                showAlert('Errore', 'Il CAP deve essere di 5 cifre', 'danger');
                return;
            }
            
            // Validazione email se presente
            const email = document.getElementById('email').value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salva Modifiche';
                showAlert('Errore', 'Inserisci un indirizzo email valido', 'danger');
                return;
            }
        });
    }
    
    // === CONFERMA ELIMINAZIONE ===
    const formElimina = document.getElementById('formElimina');
    if (formElimina) {
        formElimina.addEventListener('submit', function(e) {
            const conferma = confirm('Sei sicuro di voler eliminare questo centro?\n\nQuesta azione è irreversibile!');
            if (!conferma) {
                e.preventDefault();
            }
        });
    }
    
    // === FUNZIONE PER COPIARE L'INDIRIZZO ===
    window.copiaIndirizzo = function() {
        const indirizzo = '{{ $centro->indirizzo_completo }}';
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(indirizzo).then(function() {
                showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
            }).catch(function() {
                fallbackCopyText(indirizzo);
            });
        } else {
            fallbackCopyText(indirizzo);
        }
    };
    
    // === FALLBACK PER COPIA TESTO (browser più vecchi) ===
    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showAlert('Successo', 'Indirizzo copiato negli appunti!', 'success');
        } catch (err) {
            showAlert('Errore', 'Impossibile copiare il testo', 'danger');
        }
        
        document.body.removeChild(textArea);
    }
    
    // === FUNZIONE PER MOSTRARE ALERT ===
    function showAlert(title, message, type) {
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
        alertContainer.innerHTML = `
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertBefore(alertContainer, cardBody.firstChild);
            
            setTimeout(() => {
                if (alertContainer.parentNode) {
                    alertContainer.remove();
                }
            }, 5000);
        }
    }
    
    // === RILEVAMENTO MODIFICHE ===
    let formModificato = false;
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            formModificato = true;
        });
    });
    
    // Avvisa se l'utente cerca di lasciare la pagina con modifiche non salvate
    window.addEventListener('beforeunload', function(e) {
        if (formModificato) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler lasciare la pagina?';
            return e.returnValue;
        }
    });
    
    // Reset flag quando il form viene inviato
    if (form) {
        form.addEventListener('submit', function() {
            formModificato = false;
        });
    }
});
</script>
@endpush

{{-- CSS personalizzato --}}
@push('styles')
<style>
/* Stili per campi obbligatori */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Hover effect per i pulsanti */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Animazione per il contatore caratteri */
#nomeCount {
    transition: color 0.3s ease;
}

/* Stile per le statistiche nella sidebar */
.card-body .border-end {
    border-right: 1px solid #dee2e6 !important;
}

/* Migliore spaziatura per i badge */
.badge {
    font-size: 0.75em;
}

/* Focus migliorato per i campi */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

/* Stile per il modal di conferma */
.modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header.bg-danger {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Responsive design */
@media (max-width: 768px) {
    .d-flex.gap-2.justify-content-between {
        flex-direction: column;
    }
    
    .d-flex.gap-2.justify-content-between > * {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

/* Animazione per il caricamento */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Stile per il contenuto copiabile */
.bg-light {
    border-left: 4px solid #0d6efd;
}
</style>
@endpush