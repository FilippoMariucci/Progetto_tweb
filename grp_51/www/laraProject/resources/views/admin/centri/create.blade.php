{{-- 
    File: resources/views/admin/centri/create.blade.php
    Descrizione: Form per la creazione di un nuovo centro di assistenza - TUTTI I CAMPI OBBLIGATORI
    Livello accesso: Solo Amministratori (Livello 4)
    
    MIGLIORAMENTI:
    - Tutti i campi principali sono ora obbligatori
    - Validazione migliorata lato client
    - Messaggi di errore più chiari
--}}

@extends('layouts.app')

{{-- Titolo della pagina nell'head HTML --}}
@section('title', 'Nuovo Centro Assistenza')

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

            {{-- Alert per indicare che tutti i campi sono obbligatori --}}
            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Attenzione:</strong> Tutti i campi di questo form sono <strong>obbligatori</strong>. 
                Un centro deve avere informazioni complete per essere operativo.
            </div>

            {{-- Card contenente il form --}}
            <div class="row">
                <div class="col-lg-8 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-form-text me-2"></i>
                                Dati del Centro <span class="badge bg-light text-dark ms-2">Tutti Obbligatori</span>
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            {{-- Form per la creazione del centro --}}
                            <form action="{{ route('admin.centri.store') }}" 
                                  method="POST" 
                                  id="formCentro"
                                  novalidate>
                                @csrf {{-- Token di sicurezza Laravel --}}
                                
                                {{-- Nome del centro - OBBLIGATORIO --}}
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
                                        <span id="nomeCount">0</span>/255 caratteri - <strong>Campo obbligatorio</strong>
                                    </small>
                                </div>

                                {{-- Indirizzo - OBBLIGATORIO --}}
                                <div class="mb-3">
                                    <label for="indirizzo" class="form-label required">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        Indirizzo Completo
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('indirizzo') is-invalid @enderror" 
                                           id="indirizzo" 
                                           name="indirizzo" 
                                           value="{{ old('indirizzo') }}"
                                           placeholder="Es: Via Roma, 123"
                                           maxlength="500"
                                           required>
                                    
                                    @error('indirizzo')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    
                                    <small class="form-text text-muted">
                                        <strong>Campo obbligatorio</strong> - Indirizzo completo con numero civico
                                    </small>
                                </div>

                                {{-- Riga per Città, Provincia e CAP - TUTTI OBBLIGATORI --}}
                                <div class="row">
                                    {{-- Città - OBBLIGATORIO --}}
                                    <div class="col-md-5 mb-3">
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
                                        
                                        <small class="form-text text-muted">
                                            <strong>Obbligatorio</strong>
                                        </small>
                                    </div>

                                    {{-- Provincia - OBBLIGATORIO --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="provincia" class="form-label required">
                                            <i class="bi bi-map me-1"></i>
                                            Provincia
                                        </label>
                                        <select class="form-select @error('provincia') is-invalid @enderror" 
                                                id="provincia" 
                                                name="provincia" 
                                                required>
                                            <option value="">Seleziona provincia...</option>
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
                                        
                                        <small class="form-text text-muted">
                                            <strong>Obbligatorio</strong>
                                        </small>
                                    </div>

                                    {{-- CAP - ORA OBBLIGATORIO --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="cap" class="form-label required">
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
                                               maxlength="5"
                                               required>
                                        
                                        @error('cap')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            <strong>Obbligatorio</strong> - 5 cifre
                                        </small>
                                    </div>
                                </div>

                                {{-- Riga per Telefono ed Email - ORA ENTRAMBI OBBLIGATORI --}}
                                <div class="row">
                                    {{-- Telefono - ORA OBBLIGATORIO --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label required">
                                            <i class="bi bi-telephone-fill me-1"></i>
                                            Telefono
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" 
                                               name="telefono" 
                                               value="{{ old('telefono') }}"
                                               placeholder="Es: 06 1234567 oppure 347 1234567"
                                               maxlength="20"
                                               required>
                                        
                                        @error('telefono')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            <strong>Obbligatorio</strong> - Fisso o mobile
                                        </small>
                                    </div>

                                    {{-- Email - ORA OBBLIGATORIO --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label required">
                                            <i class="bi bi-envelope-fill me-1"></i>
                                            Email
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               placeholder="Es: centro@assistenza.it"
                                               maxlength="255"
                                               required>
                                        
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        
                                        <small class="form-text text-muted">
                                            <strong>Obbligatorio</strong> - Email valida
                                        </small>
                                    </div>
                                </div>

                                {{-- Note informative aggiornate --}}
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Informazioni importanti:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Tutti i campi sono obbligatori</strong> per garantire informazioni complete</li>
                                        <li>Il centro sarà <strong>immediatamente operativo</strong> dopo la creazione</li>
                                        <li>Potrai <strong>modificare</strong> questi dati in qualsiasi momento</li>
                                        <li>I contatti saranno <strong>visibili pubblicamente</strong> nell'elenco centri</li>
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
                                            class="btn btn-primary btn-lg" 
                                            id="btnSalva">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Crea Centro Completo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sidebar con informazioni aggiornate --}}
                <div class="col-lg-4 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightbulb me-2"></i>
                                Linee Guida - Tutti i Campi Obbligatori
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-danger">🔴 Perché Tutti i Campi sono Obbligatori</h6>
                            <p class="text-muted small">
                                Un centro di assistenza deve avere <strong>informazioni complete</strong> per poter:
                            </p>
                            <ul class="text-muted small">
                                <li>Essere contattato dai clienti</li>
                                <li>Ricevere assegnazioni di tecnici</li>
                                <li>Apparire negli elenchi pubblici</li>
                                <li>Gestire le emergenze tecniche</li>
                            </ul>

                            <h6 class="text-primary">📍 Localizzazione Precisa</h6>
                            <p class="text-muted small">
                                <strong>Indirizzo, città, provincia e CAP</strong> sono essenziali per permettere a tecnici e clienti di localizzare facilmente il centro.
                            </p>

                            <h6 class="text-primary">📞 Contatti Essenziali</h6>
                            <p class="text-muted small">
                                <strong>Telefono ed email</strong> sono indispensabili per le comunicazioni urgenti e la gestione delle emergenze tecniche.
                            </p>

                            <h6 class="text-success">✅ Dopo la Creazione</h6>
                            <p class="text-muted small">
                                Il centro sarà immediatamente disponibile per:
                            </p>
                            <ul class="text-muted small">
                                <li>Assegnazione ai tecnici</li>
                                <li>Visualizzazione pubblica</li>
                                <li>Gestione emergenze</li>
                                <li>Statistiche e reportistica</li>
                            </ul>

                            <div class="alert alert-warning mt-3">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Ricorda:</strong> Tutti i campi devono essere compilati per procedere con la creazione del centro.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Card con esempio di centro completo --}}
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Esempio Centro Completo
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-success">Centro Assistenza Roma Nord</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-geo-alt me-1"></i>Via Giuseppe Verdi, 45
                            </p>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-pin-map me-1"></i>Roma (RM) - 00198
                            </p>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-telephone me-1"></i>06 85301234
                            </p>
                            <p class="text-muted small">
                                <i class="bi bi-envelope me-1"></i>roma.nord@assistenza.it
                            </p>
                            
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Centro completo e operativo
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Script JavaScript personalizzati per validazione completa --}}
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

{{-- CSS personalizzato per form con campi obbligatori --}}
@push('styles')
<style>
/* Stili per campi obbligatori più evidenti */
.form-label.required {
    font-weight: 600;
    color: #495057;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
    font-size: 1.1em;
}

/* Badge "Tutti Obbligatori" nel header */
.card-header .badge {
    font-size: 0.75rem;
}

/* Validazione visiva migliorata */
.form-control.is-valid,
.form-select.is-valid {
    border-color: #198754;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.38 1.38'/%3e%3c/svg%3e");
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 5.8 2.4 2.4M8.2 5.8l-2.4 2.4'/%3e%3c/svg%3e");
}

/* Stile per select provincia più compatta */
#provincia {
    font-size: 0.9rem;
}

#provincia option {
    padding: 4px 8px;
    font-size: 0.85rem;
}

/* Alert di validazione personalizzato */
.validation-alert {
    border-left: 4px solid #dc3545;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.validation-alert h6 {
    color: #721c24;
    margin-bottom: 0.5rem;
}

.validation-alert ul li {
    margin-bottom: 0.25rem;
    color: #721c24;
}

/* Animazioni per feedback visivo */
.form-control,
.form-select {
    transition: all 0.15s ease-in-out;
}

.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Contatore caratteri migliorato */
#nomeCount {
    transition: color 0.3s ease;
    font-weight: 600;
}

/* Alert informativo più visibile */
.alert-warning {
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
}

.alert-success {
    border-left: 4px solid #198754;
    background-color: #d1e7dd;
}

/* Esempio centro nella sidebar */
.card-body h6.text-success {
    border-left: 3px solid #198754;
    padding-left: 8px;
    margin-bottom: 8px;
}

/* Focus migliorato */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    transform: scale(1.01);
}

/* Responsive per mobile */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .card-header .badge {
        display: block;
        margin-top: 0.5rem;
    }
}

/* Loading state per il pulsante */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Miglioramenti tipografici */
.small,
small {
    font-size: 0.875rem;
    line-height: 1.4;
}

strong {
    font-weight: 600;
}

/* Icone nei placeholder */
::placeholder {
    color: #6c757d;
    opacity: 0.8;
}

/* Stile per campi completati */
.form-control.completed {
    background-color: #f8f9fa;
    border-color: #198754;
}
</style>
@endpush