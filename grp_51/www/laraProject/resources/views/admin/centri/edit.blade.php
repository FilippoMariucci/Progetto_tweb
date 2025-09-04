{{-- 
    File: resources/views/admin/centri/edit.blade.php
    Descrizione: Form per la modifica di un centro di assistenza esistente - VERSIONE CORRETTA
    Fix per il problema dei falsi messaggi di errore
--}}

@extends('layouts.app')

{{-- Titolo dinamico della pagina --}}
@section('title', 'Modifica Centro: ' . $centro->nome)

@section('content')
<div class="container mt-4">
    
    {{-- Breadcrumb per navigazione --}}
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

    {{-- MESSAGGI FLASH CORRETTI - Mostra solo se sono presenti --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Successo!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Errore!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ERRORI DI VALIDAZIONE --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Errori di validazione:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Colonna principale con form --}}
        <div class="col-lg-8">
            
            {{-- Header della pagina con titolo e azioni --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-warning">
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

            {{-- Card form --}}
            <div class="card card-custom shadow-sm">
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
                                        Attuale: {{ $centro->telefono }}
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
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Informazioni:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Le modifiche saranno immediatamente visibili a tutti gli utenti</li>
                                <li>I tecnici assegnati riceveranno una notifica delle modifiche</li>
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
            <div class="card card-custom shadow-sm mb-3">
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
                                <h4 class="text-primary mb-1">{{ $centro->tecnici->count() }}</h4>
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
                    
                    @if($centro->tecnici->count() > 0)
                        <hr>
                        <h6 class="text-muted mb-2">Specializzazioni Disponibili:</h6>
                        @php
                            $specializzazioni = $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->countBy();
                        @endphp
                        @if($specializzazioni->count() > 0)
                            @foreach($specializzazioni as $spec => $count)
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
            @if($centro->tecnici->count() > 0)
                <div class="card card-custom shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-people me-2"></i>
                            Tecnici Assegnati ({{ $centro->tecnici->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($centro->tecnici as $tecnico)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $tecnico->nome }} {{ $tecnico->cognome }}</strong>
                                    @if($tecnico->specializzazione)
                                        <br><small class="text-muted">{{ $tecnico->specializzazione }}</small>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-primary">Tecnico</span>
                                    <a href="{{ route('admin.users.show', $tecnico) }}" 
                                       class="btn btn-outline-info btn-xs">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Azioni rapide --}}
            <div class="card card-custom shadow-sm">
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
                        
                        @if($centro->indirizzo && $centro->citta)
                            @php
                                $indirizzoMaps = urlencode($centro->indirizzo . ', ' . $centro->citta . ($centro->provincia ? ', ' . $centro->provincia : '') . ', Italia');
                                $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $indirizzoMaps;
                            @endphp
                            <a href="{{ $mapsUrl }}" 
                               target="_blank" 
                               class="btn btn-outline-success btn-sm">
                                <i class="bi bi-geo-alt me-1"></i>
                                Apri in Google Maps
                            </a>
                        @endif
                        
                        @if($centro->tecnici->count() > 0)
                            <a href="{{ route('admin.users.index') }}?centro={{ $centro->id }}" 
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
                    {{ $centro->indirizzo }}, {{ $centro->citta }}
                    @if($centro->provincia)
                        ({{ strtoupper($centro->provincia) }})
                    @endif
                </div>
                
                @if($centro->tecnici->count() > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-people me-2"></i>
                        <strong>Il centro ha {{ $centro->tecnici->count() }} tecnici assegnati.</strong><br>
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
                
                @if($centro->tecnici->count() == 0)
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
                    <a href="{{ route('admin.users.index') }}?centro={{ $centro->id }}" 
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
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush

{{-- CSS personalizzato --}}
@push('styles')
<style>
/* === STILI BASE EREDISTATI DALLA VISTA PUBBLICA === */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* === STILI PER FORM MODIFICA === */

/* Stili per campi obbligatori con asterisco rosso */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Hover effect migliorato per i pulsanti */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Animazione per il contatore caratteri */
#nomeCount {
    transition: color 0.3s ease;
    font-weight: 500;
}

/* Stile per le statistiche nella sidebar */
.card-body .border-end {
    border-right: 1px solid #dee2e6 !important;
}

/* Pulsanti extra small per azioni rapide */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.25rem;
}

/* Migliore spaziatura per i badge */
.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
    border-radius: 0.5rem;
}

/* Focus migliorato per tutti i campi del form */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    outline: none;
}

/* Stati invalid con feedback chiaro */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block;
    font-size: 0.875em;
    color: #dc3545;
    margin-top: 0.25rem;
}

/* Stile per il modal di conferma eliminazione */
.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header.bg-danger {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Alert temporanei con animazione */
.alert-temp {
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

/* === DESIGN RESPONSIVE === */

/* Responsive design per tablet */
@media (max-width: 992px) {
    .card-custom {
        margin-bottom: 2rem;
    }
    
    .col-lg-4 .card-custom {
        margin-bottom: 1rem;
    }
}

/* Responsive design per mobile */
@media (max-width: 768px) {
    /* Layout pulsanti form responsive */
    .d-flex.gap-2.justify-content-between {
        flex-direction: column;
        gap: 1rem !important;
    }
    
    .d-flex.gap-2.justify-content-between > * {
        width: 100%;
    }
    
    /* Gruppo pulsanti verticale su mobile */
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    /* Alert temporanei responsive */
    .alert-temp {
        left: 1rem !important;
        right: 1rem !important;
        min-width: auto !important;
        max-width: calc(100% - 2rem) !important;
    }
    
    /* Form responsive */
    .row > .col-md-6,
    .row > .col-md-3 {
        margin-bottom: 1rem;
    }
}

/* === ACCESSIBILITÀ === */

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

/* Transizioni smooth per tutti gli elementi interattivi */
.btn,
.card,
.badge,
.alert,
.form-control,
.form-select {
    transition: all 0.2s ease;
}

/* Spinner personalizzato per stati di caricamento */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Stati di caricamento per pulsanti */
.btn-loading {
    position: relative;
    pointer-events: none;
}

/* === MIGLIORAMENTI TIPOGRAFICI === */

/* Spaziatura migliorata per titoli */
h1, h2, h3, h4, h5, h6 {
    line-height: 1.3;
    margin-bottom: 0.75rem;
}

/* Spaziatura per testi piccoli */
small, .small {
    line-height: 1.4;
    color: #6c757d;
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
    
    .alert-temp {
        animation: none !important;
    }
    
    .card-custom:hover {
        transform: none !important;
    }
    
    .btn:hover {
        transform: none !important;
    }
}

/* === STILI UTILITÀ === */

/* Bordi colorati per evidenziare sezioni importanti */
.border-start-warning {
    border-left: 4px solid #ffc107 !important;
}

.border-start-info {
    border-left: 4px solid #0dcaf0 !important;
}

/* Scroll smooth per navigazione interna */
html {
    scroll-behavior: smooth;
}

/* Miglioramenti per campi di input */
.form-control::placeholder,
.form-select::placeholder {
    color: #adb5bd;
    opacity: 1;
}

/* Stili per contenuto copiabile */
.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
    padding: 0.75rem;
    border-radius: 0.375rem;
}
</style>
@endpush