{{-- 
    File: resources/views/admin/centri/create.blade.php
    Descrizione: Form per la creazione di un nuovo centro di assistenza
    Livello accesso: Solo Amministratori (Livello 4)
    
    Funzionalità:
    - Form completo per inserimento dati centro
    - Validazione lato client con JavaScript
    - Select dinamiche per provincia/città
    - Gestione errori di validazione
--}}

@extends('admin.dashboard')

{{-- Titolo della pagina nell'head HTML --}}
@section('title', 'Nuovo Centro Assistenza')

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
        <li class="breadcrumb-item active" aria-current="page">
            <i class="bi bi-plus-circle"></i> Nuovo Centro
        </li>
    </ol>
</nav>
@endsection

{{-- Contenuto principale della pagina --}}
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header della pagina con titolo e pulsante torna indietro --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="bi bi-building me-2"></i>
                        Nuovo Centro di Assistenza
                    </h1>
                    <p class="text-muted mb-0">
                        Aggiungi un nuovo centro di assistenza al sistema
                    </p>
                </div>
                
                {{-- Pulsante per tornare alla lista --}}
                <a href="{{ route('admin.centri.index') }}" 
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Torna alla Lista
                </a>
            </div>

            {{-- Card contenente il form --}}
            <div class="row">
                <div class="col-lg-8 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-form-text me-2"></i>
                                Dati del Centro
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            {{-- Form per la creazione del centro --}}
                            <form action="{{ route('admin.centri.store') }}" 
                                  method="POST" 
                                  id="formCentro"
                                  novalidate>
                                @csrf {{-- Token di sicurezza Laravel --}}
                                
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
                                           value="{{ old('nome') }}"
                                           placeholder="Es: Centro Assistenza Roma Nord"
                                           maxlength="255"
                                           required>
                                    
                                    {{-- Mostra errore di validazione se presente --}}
                                    @error('nome')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    
                                    {{-- Contatore caratteri --}}
                                    <small class="form-text text-muted">
                                        <span id="nomeCount">0</span>/255 caratteri
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
                                           value="{{ old('indirizzo') }}"
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
                                               value="{{ old('citta') }}"
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
                                            {{-- Elenco delle province italiane più comuni --}}
                                            <option value="AG" {{ old('provincia') == 'AG' ? 'selected' : '' }}>AG - Agrigento</option>
                                            <option value="AL" {{ old('provincia') == 'AL' ? 'selected' : '' }}>AL - Alessandria</option>
                                            <option value="AN" {{ old('provincia') == 'AN' ? 'selected' : '' }}>AN - Ancona</option>
                                            <option value="AO" {{ old('provincia') == 'AO' ? 'selected' : '' }}>AO - Aosta</option>
                                            <option value="AR" {{ old('provincia') == 'AR' ? 'selected' : '' }}>AR - Arezzo</option>
                                            <option value="AP" {{ old('provincia') == 'AP' ? 'selected' : '' }}>AP - Ascoli Piceno</option>
                                            <option value="AT" {{ old('provincia') == 'AT' ? 'selected' : '' }}>AT - Asti</option>
                                            <option value="AV" {{ old('provincia') == 'AV' ? 'selected' : '' }}>AV - Avellino</option>
                                            <option value="BA" {{ old('provincia') == 'BA' ? 'selected' : '' }}>BA - Bari</option>
                                            <option value="BT" {{ old('provincia') == 'BT' ? 'selected' : '' }}>BT - Barletta-Andria-Trani</option>
                                            <option value="BL" {{ old('provincia') == 'BL' ? 'selected' : '' }}>BL - Belluno</option>
                                            <option value="BN" {{ old('provincia') == 'BN' ? 'selected' : '' }}>BN - Benevento</option>
                                            <option value="BG" {{ old('provincia') == 'BG' ? 'selected' : '' }}>BG - Bergamo</option>
                                            <option value="BI" {{ old('provincia') == 'BI' ? 'selected' : '' }}>BI - Biella</option>
                                            <option value="BO" {{ old('provincia') == 'BO' ? 'selected' : '' }}>BO - Bologna</option>
                                            <option value="BZ" {{ old('provincia') == 'BZ' ? 'selected' : '' }}>BZ - Bolzano</option>
                                            <option value="BS" {{ old('provincia') == 'BS' ? 'selected' : '' }}>BS - Brescia</option>
                                            <option value="BR" {{ old('provincia') == 'BR' ? 'selected' : '' }}>BR - Brindisi</option>
                                            <option value="CA" {{ old('provincia') == 'CA' ? 'selected' : '' }}>CA - Cagliari</option>
                                            <option value="CL" {{ old('provincia') == 'CL' ? 'selected' : '' }}>CL - Caltanissetta</option>
                                            <option value="CB" {{ old('provincia') == 'CB' ? 'selected' : '' }}>CB - Campobasso</option>
                                            <option value="CE" {{ old('provincia') == 'CE' ? 'selected' : '' }}>CE - Caserta</option>
                                            <option value="CT" {{ old('provincia') == 'CT' ? 'selected' : '' }}>CT - Catania</option>
                                            <option value="CZ" {{ old('provincia') == 'CZ' ? 'selected' : '' }}>CZ - Catanzaro</option>
                                            <option value="CH" {{ old('provincia') == 'CH' ? 'selected' : '' }}>CH - Chieti</option>
                                            <option value="CO" {{ old('provincia') == 'CO' ? 'selected' : '' }}>CO - Como</option>
                                            <option value="CS" {{ old('provincia') == 'CS' ? 'selected' : '' }}>CS - Cosenza</option>
                                            <option value="CR" {{ old('provincia') == 'CR' ? 'selected' : '' }}>CR - Cremona</option>
                                            <option value="KR" {{ old('provincia') == 'KR' ? 'selected' : '' }}>KR - Crotone</option>
                                            <option value="CN" {{ old('provincia') == 'CN' ? 'selected' : '' }}>CN - Cuneo</option>
                                            <option value="EN" {{ old('provincia') == 'EN' ? 'selected' : '' }}>EN - Enna</option>
                                            <option value="FM" {{ old('provincia') == 'FM' ? 'selected' : '' }}>FM - Fermo</option>
                                            <option value="FE" {{ old('provincia') == 'FE' ? 'selected' : '' }}>FE - Ferrara</option>
                                            <option value="FI" {{ old('provincia') == 'FI' ? 'selected' : '' }}>FI - Firenze</option>
                                            <option value="FG" {{ old('provincia') == 'FG' ? 'selected' : '' }}>FG - Foggia</option>
                                            <option value="FC" {{ old('provincia') == 'FC' ? 'selected' : '' }}>FC - Forlì-Cesena</option>
                                            <option value="FR" {{ old('provincia') == 'FR' ? 'selected' : '' }}>FR - Frosinone</option>
                                            <option value="GE" {{ old('provincia') == 'GE' ? 'selected' : '' }}>GE - Genova</option>
                                            <option value="GO" {{ old('provincia') == 'GO' ? 'selected' : '' }}>GO - Gorizia</option>
                                            <option value="GR" {{ old('provincia') == 'GR' ? 'selected' : '' }}>GR - Grosseto</option>
                                            <option value="IM" {{ old('provincia') == 'IM' ? 'selected' : '' }}>IM - Imperia</option>
                                            <option value="IS" {{ old('provincia') == 'IS' ? 'selected' : '' }}>IS - Isernia</option>
                                            <option value="AQ" {{ old('provincia') == 'AQ' ? 'selected' : '' }}>AQ - L'Aquila</option>
                                            <option value="SP" {{ old('provincia') == 'SP' ? 'selected' : '' }}>SP - La Spezia</option>
                                            <option value="LT" {{ old('provincia') == 'LT' ? 'selected' : '' }}>LT - Latina</option>
                                            <option value="LE" {{ old('provincia') == 'LE' ? 'selected' : '' }}>LE - Lecce</option>
                                            <option value="LC" {{ old('provincia') == 'LC' ? 'selected' : '' }}>LC - Lecco</option>
                                            <option value="LI" {{ old('provincia') == 'LI' ? 'selected' : '' }}>LI - Livorno</option>
                                            <option value="LO" {{ old('provincia') == 'LO' ? 'selected' : '' }}>LO - Lodi</option>
                                            <option value="LU" {{ old('provincia') == 'LU' ? 'selected' : '' }}>LU - Lucca</option>
                                            <option value="MC" {{ old('provincia') == 'MC' ? 'selected' : '' }}>MC - Macerata</option>
                                            <option value="MN" {{ old('provincia') == 'MN' ? 'selected' : '' }}>MN - Mantova</option>
                                            <option value="MS" {{ old('provincia') == 'MS' ? 'selected' : '' }}>MS - Massa-Carrara</option>
                                            <option value="MT" {{ old('provincia') == 'MT' ? 'selected' : '' }}>MT - Matera</option>
                                            <option value="ME" {{ old('provincia') == 'ME' ? 'selected' : '' }}>ME - Messina</option>
                                            <option value="MI" {{ old('provincia') == 'MI' ? 'selected' : '' }}>MI - Milano</option>
                                            <option value="MO" {{ old('provincia') == 'MO' ? 'selected' : '' }}>MO - Modena</option>
                                            <option value="MB" {{ old('provincia') == 'MB' ? 'selected' : '' }}>MB - Monza e Brianza</option>
                                            <option value="NA" {{ old('provincia') == 'NA' ? 'selected' : '' }}>NA - Napoli</option>
                                            <option value="NO" {{ old('provincia') == 'NO' ? 'selected' : '' }}>NO - Novara</option>
                                            <option value="NU" {{ old('provincia') == 'NU' ? 'selected' : '' }}>NU - Nuoro</option>
                                            <option value="OR" {{ old('provincia') == 'OR' ? 'selected' : '' }}>OR - Oristano</option>
                                            <option value="PD" {{ old('provincia') == 'PD' ? 'selected' : '' }}>PD - Padova</option>
                                            <option value="PA" {{ old('provincia') == 'PA' ? 'selected' : '' }}>PA - Palermo</option>
                                            <option value="PR" {{ old('provincia') == 'PR' ? 'selected' : '' }}>PR - Parma</option>
                                            <option value="PV" {{ old('provincia') == 'PV' ? 'selected' : '' }}>PV - Pavia</option>
                                            <option value="PG" {{ old('provincia') == 'PG' ? 'selected' : '' }}>PG - Perugia</option>
                                            <option value="PU" {{ old('provincia') == 'PU' ? 'selected' : '' }}>PU - Pesaro e Urbino</option>
                                            <option value="PE" {{ old('provincia') == 'PE' ? 'selected' : '' }}>PE - Pescara</option>
                                            <option value="PC" {{ old('provincia') == 'PC' ? 'selected' : '' }}>PC - Piacenza</option>
                                            <option value="PI" {{ old('provincia') == 'PI' ? 'selected' : '' }}>PI - Pisa</option>
                                            <option value="PT" {{ old('provincia') == 'PT' ? 'selected' : '' }}>PT - Pistoia</option>
                                            <option value="PN" {{ old('provincia') == 'PN' ? 'selected' : '' }}>PN - Pordenone</option>
                                            <option value="PZ" {{ old('provincia') == 'PZ' ? 'selected' : '' }}>PZ - Potenza</option>
                                            <option value="PO" {{ old('provincia') == 'PO' ? 'selected' : '' }}>PO - Prato</option>
                                            <option value="RG" {{ old('provincia') == 'RG' ? 'selected' : '' }}>RG - Ragusa</option>
                                            <option value="RA" {{ old('provincia') == 'RA' ? 'selected' : '' }}>RA - Ravenna</option>
                                            <option value="RC" {{ old('provincia') == 'RC' ? 'selected' : '' }}>RC - Reggio Calabria</option>
                                            <option value="RE" {{ old('provincia') == 'RE' ? 'selected' : '' }}>RE - Reggio Emilia</option>
                                            <option value="RI" {{ old('provincia') == 'RI' ? 'selected' : '' }}>RI - Rieti</option>
                                            <option value="RN" {{ old('provincia') == 'RN' ? 'selected' : '' }}>RN - Rimini</option>
                                            <option value="RM" {{ old('provincia') == 'RM' ? 'selected' : '' }}>RM - Roma</option>
                                            <option value="RO" {{ old('provincia') == 'RO' ? 'selected' : '' }}>RO - Rovigo</option>
                                            <option value="SA" {{ old('provincia') == 'SA' ? 'selected' : '' }}>SA - Salerno</option>
                                            <option value="SS" {{ old('provincia') == 'SS' ? 'selected' : '' }}>SS - Sassari</option>
                                            <option value="SV" {{ old('provincia') == 'SV' ? 'selected' : '' }}>SV - Savona</option>
                                            <option value="SI" {{ old('provincia') == 'SI' ? 'selected' : '' }}>SI - Siena</option>
                                            <option value="SR" {{ old('provincia') == 'SR' ? 'selected' : '' }}>SR - Siracusa</option>
                                            <option value="SO" {{ old('provincia') == 'SO' ? 'selected' : '' }}>SO - Sondrio</option>
                                            <option value="TA" {{ old('provincia') == 'TA' ? 'selected' : '' }}>TA - Taranto</option>
                                            <option value="TE" {{ old('provincia') == 'TE' ? 'selected' : '' }}>TE - Teramo</option>
                                            <option value="TR" {{ old('provincia') == 'TR' ? 'selected' : '' }}>TR - Terni</option>
                                            <option value="TO" {{ old('provincia') == 'TO' ? 'selected' : '' }}>TO - Torino</option>
                                            <option value="TP" {{ old('provincia') == 'TP' ? 'selected' : '' }}>TP - Trapani</option>
                                            <option value="TN" {{ old('provincia') == 'TN' ? 'selected' : '' }}>TN - Trento</option>
                                            <option value="TV" {{ old('provincia') == 'TV' ? 'selected' : '' }}>TV - Treviso</option>
                                            <option value="TS" {{ old('provincia') == 'TS' ? 'selected' : '' }}>TS - Trieste</option>
                                            <option value="UD" {{ old('provincia') == 'UD' ? 'selected' : '' }}>UD - Udine</option>
                                            <option value="VA" {{ old('provincia') == 'VA' ? 'selected' : '' }}>VA - Varese</option>
                                            <option value="VE" {{ old('provincia') == 'VE' ? 'selected' : '' }}>VE - Venezia</option>
                                            <option value="VB" {{ old('provincia') == 'VB' ? 'selected' : '' }}>VB - Verbano-Cusio-Ossola</option>
                                            <option value="VC" {{ old('provincia') == 'VC' ? 'selected' : '' }}>VC - Vercelli</option>
                                            <option value="VR" {{ old('provincia') == 'VR' ? 'selected' : '' }}>VR - Verona</option>
                                            <option value="VV" {{ old('provincia') == 'VV' ? 'selected' : '' }}>VV - Vibo Valentia</option>
                                            <option value="VI" {{ old('provincia') == 'VI' ? 'selected' : '' }}>VI - Vicenza</option>
                                            <option value="VT" {{ old('provincia') == 'VT' ? 'selected' : '' }}>VT - Viterbo</option>
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
                                               value="{{ old('cap') }}"
                                               placeholder="Es: 00100"
                                               pattern="[0-9]{5}"
                                               maxlength="5">
                                        
                                        @error('cap')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            5 cifre numeriche
                                        </small>
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
                                               value="{{ old('telefono') }}"
                                               placeholder="Es: 06 1234567 oppure 347 1234567"
                                               maxlength="20">
                                        
                                        @error('telefono')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            Formato: fisso o mobile
                                        </small>
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
                                               value="{{ old('email') }}"
                                               placeholder="Es: centro@assistenza.it"
                                               maxlength="255">
                                        
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            Indirizzo email valido
                                        </small>
                                    </div>
                                </div>

                                {{-- Note per l'amministratore --}}
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Informazioni:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>I campi contrassegnati con * sono obbligatori</li>
                                        <li>Il centro sarà subito disponibile per l'assegnazione ai tecnici</li>
                                        <li>Puoi modificare questi dati in qualsiasi momento</li>
                                    </ul>
                                </div>

                                {{-- Pulsanti del form --}}
                                <div class="d-flex gap-2 justify-content-end">
                                    {{-- Pulsante annulla --}}
                                    <a href="{{ route('admin.centri.index') }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Annulla
                                    </a>
                                    
                                    {{-- Pulsante salva --}}
                                    <button type="submit" 
                                            class="btn btn-primary" 
                                            id="btnSalva">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Crea Centro
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sidebar con informazioni aggiuntive --}}
                <div class="col-lg-4 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightbulb me-2"></i>
                                Suggerimenti
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary">Nome Centro</h6>
                            <p class="text-muted small">
                                Scegli un nome descrittivo che identifichi chiaramente la posizione 
                                geografica o la specializzazione del centro.
                            </p>

                            <h6 class="text-primary">Indirizzo Completo</h6>
                            <p class="text-muted small">
                                Inserisci l'indirizzo completo per facilitare la localizzazione 
                                da parte dei tecnici e clienti.
                            </p>

                            <h6 class="text-primary">Contatti</h6>
                            <p class="text-muted small">
                                Telefono ed email saranno visibili ai tecnici e utilizzati 
                                per le comunicazioni ufficiali.
                            </p>

                            <h6 class="text-primary">Dopo la Creazione</h6>
                            <p class="text-muted small">
                                Una volta creato, il centro sarà disponibile per:
                            </p>
                            <ul class="text-muted small">
                                <li>Assegnazione ai nuovi tecnici</li>
                                <li>Visualizzazione pubblica nell'elenco centri</li>
                                <li>Statistiche e reportistica</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Script JavaScript personalizzati per questa pagina --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === CONTATORE CARATTERI PER IL NOME ===
    const nomeInput = document.getElementById('nome');
    const nomeCounter = document.getElementById('nomeCount');
    
    if (nomeInput && nomeCounter) {
        // Funzione per aggiornare il contatore
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
        
        // Aggiorna contatore all'input
        nomeInput.addEventListener('input', updateNomeCounter);
        
        // Aggiorna contatore al caricamento se c'è del testo (old input)
        updateNomeCounter();
    }
    
    // === VALIDAZIONE CAP ===
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
        });
    }
    
    // === VALIDAZIONE TELEFONO ===
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            // Rimuove caratteri non validi per il telefono (mantiene numeri, spazi, +, -, ())
            let value = e.target.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            e.target.value = value;
        });
    }
    
    // === VALIDAZIONE FORM COMPLETA ===
    const form = document.getElementById('formCentro');
    const btnSalva = document.getElementById('btnSalva');
    
    if (form && btnSalva) {
        form.addEventListener('submit', function(e) {
            // Mostra spinner sul pulsante durante l'invio
            btnSalva.disabled = true;
            btnSalva.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creazione in corso...';
            
            // Validazione JavaScript aggiuntiva
            const nome = document.getElementById('nome').value.trim();
            const indirizzo = document.getElementById('indirizzo').value.trim();
            const citta = document.getElementById('citta').value.trim();
            const provincia = document.getElementById('provincia').value;
            
            // Controlla campi obbligatori
            if (!nome || !indirizzo || !citta || !provincia) {
                e.preventDefault();
                
                // Ripristina pulsante
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Crea Centro';
                
                // Mostra messaggio di errore
                showAlert('Errore', 'Compila tutti i campi obbligatori', 'danger');
                return;
            }
            
            // Validazione CAP se presente
            const cap = document.getElementById('cap').value.trim();
            if (cap && (cap.length !== 5 || !/^\d{5}$/.test(cap))) {
                e.preventDefault();
                
                // Ripristina pulsante
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Crea Centro';
                
                showAlert('Errore', 'Il CAP deve essere composto da esattamente 5 cifre', 'danger');
                return;
            }
            
            // Validazione email se presente
            const email = document.getElementById('email').value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                
                // Ripristina pulsante
                btnSalva.disabled = false;
                btnSalva.innerHTML = '<i class="bi bi-check-circle me-1"></i>Crea Centro';
                
                showAlert('Errore', 'Inserisci un indirizzo email valido', 'danger');
                return;
            }
        });
    }
    
    // === FUNZIONE PER MOSTRARE ALERT ===
    function showAlert(title, message, type) {
        // Crea e mostra un alert Bootstrap dinamico
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
        alertContainer.innerHTML = `
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Inserisce l'alert all'inizio del contenuto della card
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertBefore(alertContainer, cardBody.firstChild);
            
            // Rimuove automaticamente dopo 5 secondi
            setTimeout(() => {
                if (alertContainer.parentNode) {
                    alertContainer.remove();
                }
            }, 5000);
        }
    }
    
    // === AUTO-FOCUS SUL PRIMO CAMPO ===
    const primoInput = document.getElementById('nome');
    if (primoInput) {
        primoInput.focus();
    }
});
</script>
@endpush

{{-- CSS personalizzato per questa pagina --}}
@push('styles')
<style>
/* Stili per campi obbligatori */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Stile per select provincia più compatta */
#provincia option {
    padding: 2px 5px;
}

/* Hover effect per i pulsanti */
.btn-outline-secondary:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Animazione per il contatore caratteri */
#nomeCount {
    transition: color 0.3s ease;
}

/* Migliore spaziatura per gli alert */
.alert {
    margin-bottom: 1.5rem;
}

/* Stile per i suggerimenti nella sidebar */
.card-body h6.text-primary {
    border-left: 3px solid #0d6efd;
    padding-left: 8px;
    margin-bottom: 8px;
}

/* Focus migliorato per i campi */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

/* Responsive design per mobile */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush