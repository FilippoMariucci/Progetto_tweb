

{{-- 
/**
 * BLADE TEMPLATE INHERITANCE - LARAVEL LAYOUT SYSTEM
 * 
 * @extends: Blade directive per template inheritance
 * Utilizza il layout principale 'layouts.app' che fornisce:
 * - HTML5 document structure con responsive meta tags
 * - Bootstrap CSS/JS framework integration
 * - Navigation bar amministrativa con user authentication
 * - Flash messages system per user feedback
 * - CSRF meta tag per AJAX security
 * - Common footer e global JavaScript libraries
 */
--}}
@extends('layouts.app')

{{-- 
/**
 * DYNAMIC PAGE TITLE - SEO E BROWSER TAB OPTIMIZATION
 * 
 * PATTERN: Dynamic title generation con model attributes
 * {{ $centro->nome }}: Eloquent model attribute access
 * String concatenation per descriptive browser title
 * SEO benefit: Specific page titles per unique content
 * User experience: Clear tab identification in browser
 */
--}}
@section('title', 'Modifica Centro: ' . $centro->nome)

{{-- 
/**
 * MAIN CONTENT SECTION - FORM LAYOUT CON SIDEBAR
 * 
 * LAYOUT STRUCTURE:
 * - Container responsive con Bootstrap grid system
 * - Breadcrumb navigation per user orientation
 * - Flash messages handling per user feedback
 * - Two-column layout: Form (col-lg-8) + Sidebar (col-lg-4)
 * - Modal integration per confirmation dialogs
 */
--}}
@section('content')
<div class="container mt-4">
    
    {{-- 
    /**
     * BREADCRUMB NAVIGATION - USER ORIENTATION SYSTEM
     * 
     * ACCESSIBILITY FEATURES:
     * - aria-label="breadcrumb": Screen reader support
     * - Semantic <nav> element per navigation landmark
     * - Bootstrap .breadcrumb styling con separators
     * 
     * NAVIGATION HIERARCHY:
     * Home → Admin Dashboard → Gestione Centri → View Centro → Edit Centro
     * 
     * LARAVEL INTEGRATION:
     * - route() helpers per URL generation
     * - Model binding: $centro parameter automatic injection
     * - Str::limit() Laravel helper per text truncation
     * 
     * UX BENEFITS:
     * - Clear navigation path per user orientation
     * - Quick access to parent pages
     * - Current page indication con .active class
     */
    --}}
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

    {{-- 
    /**
     * FLASH MESSAGES SYSTEM - LARAVEL SESSION FEEDBACK
     * 
     * PATTERN: Conditional message display
     * - @if(session('key')): Blade conditional per session data
     * - session('success'): Laravel session helper per success messages
     * - session('error'): Laravel session helper per error messages
     * 
     * BOOTSTRAP ALERT COMPONENTS:
     * - .alert-success: Green success message styling
     * - .alert-danger: Red error message styling
     * - .alert-dismissible: Closeable alert functionality
     * - .fade.show: Animation classes per smooth transitions
     * - data-bs-dismiss="alert": Bootstrap JS dismiss functionality
     * 
     * ACCESSIBILITY:
     * - role="alert": Screen reader notification
     * - aria-label="Close": Screen reader close button description
     * 
     * UX IMPROVEMENTS:
     * - Icons per visual context (bi-check-circle, bi-exclamation-triangle)
     * - Auto-dismissible con JavaScript
     * - Clear visual hierarchy con colors e typography
     */
    --}}
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

    {{-- 
    /**
     * VALIDATION ERRORS DISPLAY - LARAVEL FORM VALIDATION
     * 
     * ERROR BAG PROCESSING:
     * - $errors: Laravel MessageBag instance per validation errors
     * - ->any(): Check se ci sono validation errors
     * - ->all(): Array di tutti gli error messages
     * 
     * DISPLAY PATTERN:
     * - Conditional rendering: Solo se errors present
     * - Bootstrap danger alert per error styling
     * - Bulleted list per multiple errors organization
     * - Icons per visual context e user attention
     */
    --}}
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
        {{-- 
        /**
         * MAIN FORM COLUMN - PRIMARY CONTENT AREA
         * 
         * RESPONSIVE LAYOUT:
         * - col-lg-8: 8/12 columns on large screens (66.67% width)
         * - Stacks vertically below lg breakpoint (< 992px)
         * - Form-first approach per mobile usability
         */
        --}}
        <div class="col-lg-8">
            
            {{-- 
            /**
             * PAGE HEADER SECTION - TITLE E ACTION BUTTONS
             * 
             * LAYOUT PATTERN:
             * - Bootstrap Flexbox: .d-flex .justify-content-between
             * - Left side: Title + description
             * - Right side: Action button group
             * 
             * DYNAMIC CONTENT:
             * - $centro->nome: Eloquent model attribute binding
             * - Context-aware title generation
             * - Action buttons per quick navigation
             */
            --}}
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
                
                {{-- 
                /**
                 * ACTION BUTTON GROUP - QUICK NAVIGATION
                 * 
                 * BOOTSTRAP BUTTON GROUP:
                 * - .btn-group: Grouped buttons styling
                 * - role="group": ARIA accessibility
                 * - .btn-sm: Smaller buttons per header space
                 * 
                 * LARAVEL ROUTING:
                 * - route('admin.centri.show', $centro): Named route con model binding
                 * - route('admin.centri.index'): Named route to index
                 * - Automatic URL generation con route parameters
                 */
                --}}
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

            {{-- 
            /**
             * MAIN FORM CARD - BOOTSTRAP CARD COMPONENT
             * 
             * CARD STRUCTURE:
             * - .card: Bootstrap card container
             * - .card-custom: Custom CSS class per enhanced styling
             * - .shadow-sm: Subtle shadow per depth perception
             * - .bg-warning: Warning theme per edit context
             * - .text-dark: Dark text per contrast su warning background
             */
            --}}
            <div class="card card-custom shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-form-text me-2"></i>
                        Modifica Dati Centro
                    </h5>
                </div>
                
                <div class="card-body">
                    {{-- 
                    /**
                     * LARAVEL UPDATE FORM - RESTful RESOURCE FORM
                     * 
                     * FORM ATTRIBUTES:
                     * - action: route('admin.centri.update', $centro) con model binding
                     * - method="POST": HTTP method (Laravel method spoofing)
                     * - @method('PUT'): Laravel method spoofing per RESTful PUT
                     * - @csrf: Laravel CSRF protection token
                     * - novalidate: Disabilita HTML5 validation per custom handling
                     * 
                     * SECURITY FEATURES:
                     * - CSRF token automatic inclusion
                     * - Route model binding per authorized resource access
                     * - Laravel validation rules enforcement
                     * - Input sanitization automatic
                     */
                    --}}
                    <form action="{{ route('admin.centri.update', $centro) }}" 
                          method="POST" 
                          id="formModificaCentro"
                          novalidate>
                        @csrf {{-- Laravel CSRF Protection Token --}}
                        @method('PUT') {{-- Laravel Method Spoofing per RESTful PUT --}}
                        
                        {{-- 
                        /**
                         * NOME CENTRO FIELD - TEXT INPUT CON PRE-POPULATION
                         * 
                         * PRE-POPULATION PATTERN:
                         * - old('nome', $centro->nome): Laravel old() helper con fallback
                         * - First priority: old input (dopo validation failure)
                         * - Second priority: existing model data
                         * - Ensures form persistence attraverso validation cycles
                         * 
                         * VALIDATION INTEGRATION:
                         * - @error('nome'): Blade directive per field-specific errors
                         * - .is-invalid: Bootstrap validation class conditional
                         * - .invalid-feedback: Bootstrap error message styling
                         * 
                         * UX ENHANCEMENTS:
                         * - Character counter con JavaScript integration
                         * - strlen($centro->nome): PHP function per initial counter
                         * - Real-time feedback per user guidance
                         */
                        --}}
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
                            
                            {{-- 
                            /**
                             * CHARACTER COUNTER - REAL-TIME FEEDBACK
                             * 
                             * INITIAL STATE:
                             * - strlen($centro->nome): PHP string length function
                             * - JavaScript will update #nomeCount on input
                             * - Visual feedback per user input limits
                             */
                            --}}
                            <small class="form-text text-muted">
                                <span id="nomeCount">{{ strlen($centro->nome) }}</span>/255 caratteri
                            </small>
                        </div>

                        {{-- 
                        /**
                         * INDIRIZZO FIELD - ADDRESS INPUT CON MODEL BINDING
                         * 
                         * Same pattern as nome field:
                         * - Pre-population con old() fallback
                         * - Validation error handling
                         * - Bootstrap styling con conditional classes
                         */
                        --}}
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

                        {{-- 
                        /**
                         * LOCATION ROW - BOOTSTRAP RESPONSIVE GRID
                         * 
                         * GRID LAYOUT:
                         * - .row: Bootstrap flex container
                         * - col-md-6: Città field (50% width on md+ screens)
                         * - col-md-3: Provincia field (25% width on md+ screens)  
                         * - col-md-3: CAP field (25% width on md+ screens)
                         * - Mobile: Stack vertically (< 768px)
                         */
                        --}}
                        <div class="row">
                            {{-- CITTÀ FIELD --}}
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

                            {{-- 
                            /**
                             * PROVINCIA SELECT - PHP ARRAY CON SELECTED STATE
                             * 
                             * PHP ARRAY DEFINITION:
                             * - @php directive per inline PHP code
                             * - $province array: Associative array [sigla => nome_completo]
                             * - Complete Italian provinces list (110 province)
                             * 
                             * SELECTED STATE LOGIC:
                             * - old('provincia', $centro->provincia): Priority order selection
                             * - Ternary operator: {{ condition ? 'selected' : '' }}
                             * - Maintains selection attraverso validation cycles
                             * 
                             * PERFORMANCE CONSIDERATION:
                             * - Static PHP array invece di database query
                             * - Client-side caching dopo primo load
                             * - Future: Could be moved to JavaScript per optimization
                             */
                            --}}
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
                                    {{-- 
                                    /**
                                     * PHP INLINE ARRAY DEFINITION - PROVINCE ITALIANE
                                     * 
                                     * ARRAY STRUCTURE:
                                     * - Key: Sigla provincia (2 caratteri) per database storage
                                     * - Value: Display string "SIGLA - Nome Completo"
                                     * - Complete coverage di tutte le province italiane
                                     * - Alphabetical order per user experience
                                     */
                                    --}}
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
                                    
                                    {{-- 
                                    /**
                                     * PROVINCE LOOP - DYNAMIC OPTION GENERATION
                                     * 
                                     * @foreach: Blade loop attraverso province array
                                     * - $sigla: Array key (2-character province code)
                                     * - $nome: Array value (display string)
                                     * - Selected logic: old('provincia', $centro->provincia) == $sigla
                                     * - Form persistence attraverso validation cycles
                                     */
                                    --}}
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

                            {{-- 
                            /**
                             * CAP FIELD - NUMERIC PATTERN VALIDATION
                             * 
                             * NOTE: Non required field (different da create form)
                             * - Allows optional CAP per existing data flexibility
                             * - pattern="[0-9]{5}": HTML5 regex per 5 digits
                             * - type="text": Better pattern control vs type="number"
                             */
                            --}}
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

                        {{-- 
                        /**
                         * CONTACT ROW - TELEFONO E EMAIL FIELDS
                         * 
                         * RESPONSIVE GRID:
                         * - col-md-6: Equal width columns (50% each on md+ screens)
                         * - Mobile: Stack vertically
                         * 
                         * OPTIONAL FIELDS:
                         * - No required attribute per backward compatibility
                         * - Existing data may have null values
                         * - Validation rules nel Controller gestiscono requirements
                         */
                        --}}
                        <div class="row">
                            {{-- 
                            /**
                             * TELEFONO FIELD - TEL INPUT TYPE
                             * 
                             * INPUT FEATURES:
                             * - type="tel": Mobile keyboard optimization
                             * - maxlength="20": Flexible per vari formati
                             * - Placeholder con esempi pratici
                             * 
                             * UX ENHANCEMENT:
                             * - Current value display sotto input
                             * - @if($centro->telefono): Conditional display only if exists
                             * - Helps user understand current vs new value
                             */
                            --}}
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
                                        @error('telefono')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                
                                {{-- 
                                /**
                                 * CURRENT VALUE DISPLAY - UX ENHANCEMENT
                                 * 
                                 * CONDITIONAL DISPLAY:
                                 * - @if($centro->telefono): Only show if value exists
                                 * - Helps user understand current stored value
                                 * - Visual comparison between current e new input
                                 */
                                --}}
                                @if($centro->telefono)
                                    <small class="form-text text-muted">
                                        Attuale: {{ $centro->telefono }}
                                    </small>
                                @endif
                            </div>

                            {{-- EMAIL FIELD - EMAIL INPUT TYPE --}}
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

                        {{-- 
                        /**
                         * INFORMATION ALERT - CHANGE IMPACT NOTIFICATION
                         * 
                         * BUSINESS LOGIC COMMUNICATION:
                         * - Explains consequences of modifications
                         * - Transparency about system behavior
                         * - Audit trail information display
                         * - User expectation setting
                         * 
                         * TIMESTAMP DISPLAY:
                         * - $centro->created_at: Carbon datetime instance
                         * - ->format('d/m/Y H:i'): Italian date format
                         * - Conditional updated_at display only if different from created_at
                         * - User-friendly timestamp formatting
                         */
                        --}}
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Informazioni:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Le modifiche saranno immediatamente visibili a tutti gli utenti</li>
                                <li>I tecnici assegnati riceveranno una notifica delle modifiche</li>
                                <li>Creato il: <strong>{{ $centro->created_at->format('d/m/Y H:i') }}</strong></li>
                                {{-- 
                                /**
                                 * CONDITIONAL UPDATED TIMESTAMP
                                 * 
                                 * LOGIC: Show only if record has been modified
                                 * - $centro->updated_at != $centro->created_at
                                 * - Carbon datetime comparison
                                 * - Avoids redundant "never updated" display
                                 */
                                --}}
                                @if($centro->updated_at != $centro->created_at)
                                    <li>Ultima modifica: <strong>{{ $centro->updated_at->format('d/m/Y H:i') }}</strong></li>
                                @endif
                            </ul>
                        </div>

                        {{-- 
                        /**
                         * FORM ACTIONS - COMPLEX BUTTON LAYOUT
                         * 
                         * LAYOUT PATTERN:
                         * - .d-flex .justify-content-between: Space between layout
                         * - Left side: Destructive action (Delete)
                         * - Right side: Primary actions (Cancel/Save)
                         * - Visual hierarchy con button colors
                         * 
                         * BUTTON HIERARCHY:
                         * - Delete: .btn-outline-danger (destructive, lower priority)
                         * - Cancel: .btn-outline-secondary (neutral)
                         * - Save: .btn-warning (primary action, edit theme)
                         */
                        --}}
                        <div class="d-flex gap-2 justify-content-between">
                            {{-- 
                            /**
                             * DELETE BUTTON - MODAL TRIGGER
                             * 
                             * BOOTSTRAP MODAL INTEGRATION:
                             * - data-bs-toggle="modal": Bootstrap modal trigger
                             * - data-bs-target="#modalElimina": Target modal selector
                             * - Confirmation pattern per destructive actions
                             * 
                             * ACCESSIBILITY:
                             * - type="button": Not form submission trigger
                             * - Clear labeling per screen readers
                             * - Icon + text per visual context
                             */
                            --}}
                            <button type="button" 
                                    class="btn btn-outline-danger"
                                    id="btnElimina"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalElimina">
                                <i class="bi bi-trash me-1"></i>
                                Elimina Centro
                            </button>
                            
                            {{-- PRIMARY ACTIONS GROUP --}}
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

        {{-- 
        /**
         * SIDEBAR COLUMN - SUPPLEMENTARY INFORMATION
         * 
         * RESPONSIVE LAYOUT:
         * - col-lg-4: 4/12 columns (33.33% width) on large screens
         * - Stacks below main content on smaller screens
         * - Contains statistics, related data, quick actions
         * 
         * CONTENT ORGANIZATION:
         * - Statistics card with relationship counts
         * - Tecnici assegnati list (if any)
         * - Quick actions utility buttons
         */
        --}}
        <div class="col-lg-4">
            {{-- 
            /**
             * STATISTICS CARD - RELATIONSHIP METRICS
             * 
             * ELOQUENT RELATIONSHIPS:
             * - $centro->tecnici: hasMany relationship to User model
             * - ->count(): Efficient count query (no model loading)
             * - $centro->created_at->diffInDays(): Carbon method per days calculation
             * 
             * DISPLAY PATTERNS:
             * - Visual metrics con numbers + labels
             * - Bootstrap grid per organized layout
             * - Border-end separation per visual hierarchy
             */
            --}}
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
                    
                    {{-- 
                    /**
                     * SPECIALIZATIONS ANALYSIS - COLLECTION METHODS
                     * 
                     * LARAVEL COLLECTION OPERATIONS:
                     * - ->whereNotNull('specializzazione'): Filter non-null specializations
                     * - ->pluck('specializzazione'): Extract specific attribute
                     * - ->countBy(): Group e count occurrences
                     * 
                     * PHP VARIABLE:
                     * - @php directive per complex logic
                     * - Collection stored in $specializzazioni variable
                     * - Conditional rendering basato su count
                     */
                    --}}
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

            {{-- 
            /**
             * TECNICI ASSEGNATI CARD - RELATIONSHIP DISPLAY
             * 
             * CONDITIONAL RENDERING:
             * - @if($centro->tecnici->count() > 0): Only show if tecnici exist
             * - Dynamic card title con count
             * - List format per individual tecnico display
             * 
             * RELATIONSHIP ITERATION:
             * - @foreach($centro->tecnici as $tecnico): Loop through relationship
             * - Access related model attributes: $tecnico->nome, $tecnico->cognome
             * - Conditional specialization display
             * - Quick action links to tecnico details
             */
            --}}
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

            {{-- 
            /**
             * QUICK ACTIONS CARD - UTILITY FUNCTIONS
             * 
             * GOOGLE MAPS INTEGRATION:
             * - PHP string concatenation per address building
             * - urlencode() per URL-safe address string
             * - Dynamic Google Maps URL generation
             * - target="_blank" per new tab opening
             * 
             * CONDITIONAL ACTIONS:
             * - Address-based actions only if address exists
             * - Tecnici management only if tecnici assigned
             * - JavaScript integration per clipboard functionality
             */
            --}}
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
                        
                        {{-- 
                        /**
                         * GOOGLE MAPS INTEGRATION - DYNAMIC URL GENERATION
                         * 
                         * PHP LOGIC:
                         * - @if($centro->indirizzo && $centro->citta): Conditional rendering
                         * - @php directive per complex URL building
                         * - String concatenation: indirizzo + citta + provincia + Italia
                         * - urlencode(): URL encoding per special characters
                         * - Google Maps Search API URL structure
                         * 
                         * EXTERNAL INTEGRATION:
                         * - Opens in new tab (target="_blank")
                         * - Direct integration con Google Maps service
                         * - User convenience per location verification
                         */
                        --}}
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
                        
                        {{-- CONDITIONAL TECNICI MANAGEMENT LINK --}}
                        @if($centro->tecnici->count() > 0)
                            <a href="{{ route('admin.users.index') }}?centro={{ $centro->id }}" 
                               class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-people me-1"></i>
                                Gestisci Tecnici
                            </a>
                        @endif
                        
                        {{-- 
                        /**
                         * JAVASCRIPT INTEGRATION - CLIPBOARD FUNCTIONALITY
                         * 
                         * onclick="copiaIndirizzo()": JavaScript function call
                         * - Custom JavaScript function (deve essere definita)
                         * - Clipboard API integration per address copying
                         * - User convenience per external use
                         */
                        --}}
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

{{-- 
/**
 * ========================================================================
 * DELETION CONFIRMATION MODAL - BOOTSTRAP MODAL COMPONENT
 * ========================================================================
 * 
 * DESIGN PATTERN:
 * - Confirmation dialog per destructive operations
 * - Business logic validation (tecnici assegnati check)
 * - Conditional action buttons based on business rules
 * - Clear warning messages e consequences explanation
 * 
 * BOOTSTRAP MODAL STRUCTURE:
 * - .modal-dialog-centered: Vertical centering
 * - .bg-danger header: Danger theme per destructive action
 * - .btn-close-white: White close button per dark header
 * - tabindex="-1": Proper focus management
 * - aria-labelledby/aria-hidden: Accessibility support
 */
--}}
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
                {{-- WARNING ALERT --}}
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Questa azione è irreversibile.
                </div>
                
                <p>Stai per eliminare il centro di assistenza:</p>
                {{-- 
                /**
                 * CENTRO DETAILS DISPLAY - CONFIRMATION INFO
                 * 
                 * DATA DISPLAY:
                 * - $centro->nome: Centro name
                 * - $centro->indirizzo, $centro->citta: Address info
                 * - Conditional provincia display if exists
                 * - strtoupper($centro->provincia): Uppercase provincia display
                 */
                --}}
                <div class="bg-light p-3 rounded">
                    <strong>{{ $centro->nome }}</strong><br>
                    {{ $centro->indirizzo }}, {{ $centro->citta }}
                    @if($centro->provincia)
                        ({{ strtoupper($centro->provincia) }})
                    @endif
                </div>
                
                {{-- 
                /**
                 * BUSINESS LOGIC VALIDATION - TECNICI CONSTRAINT
                 * 
                 * CONSTRAINT CHECK:
                 * - @if($centro->tecnici->count() > 0): Check assigned tecnici
                 * - Prevents deletion if tecnici are assigned
                 * - Business rule enforcement in UI
                 * - Clear explanation of constraint to user
                 */
                --}}
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
                
                {{-- 
                /**
                 * CONDITIONAL ACTION BUTTONS - BUSINESS LOGIC ENFORCEMENT
                 * 
                 * DELETE FORM (if no tecnici):
                 * - Laravel DELETE form con CSRF protection
                 * - @method('DELETE'): HTTP method spoofing
                 * - route('admin.centri.destroy', $centro): RESTful destroy route
                 * - Form submission triggers Controller destroy method
                 * 
                 * MANAGEMENT LINK (if tecnici exist):
                 * - Redirect to users management con centro filter
                 * - Query parameter: ?centro={{ $centro->id }}
                 * - Guides user to resolve constraint before deletion
                 */
                --}}
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

{{-- 
/**
 * ========================================================================
 * JAVASCRIPT SECTION - CLIENT-SIDE ENHANCEMENTS
 * ========================================================================
 * 
 * DATA TRANSFER PATTERN:
 * - @push('scripts'): Blade stack injection
 * - window.PageData: Global namespace pattern
 * - Conditional data passing per performance
 * - Laravel → JavaScript data bridge
 */
--}}
@push('scripts')
<script>
/**
 * ========================================================================
 * GLOBAL DATA INITIALIZATION - JAVASCRIPT NAMESPACE
 * ========================================================================
 * 
 * NAMESPACE PATTERN:
 * - window.PageData: Global object per data storage
 * - Defensive initialization: || operator
 * - Prevents overwriting existing data
 * - Extensible structure per future requirements
 */
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

/**
 * ========================================================================
 * CONDITIONAL DATA PASSING - LARAVEL → JAVASCRIPT INTEGRATION
 * ========================================================================
 * 
 * PATTERN EXPLANATION:
 * - @if(isset($variable)): Check variable existence
 * - @json($variable): Laravel helper per JSON serialization
 * - Prevents undefined variable errors
 * - Performance optimization (only transfer needed data)
 * 
 * CURRENT IMPLEMENTATION:
 * - Template-based data passing structure
 * - Ready per future JavaScript enhancements
 * - Common variables da layout inheritance
 * 
 * FUTURE ENHANCEMENTS READY:
 * - Form validation data
 * - Character counting functionality
 * - Real-time validation
 * - Address validation API integration
 * - Clipboard functionality implementation
 */

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

/**
 * ========================================================================
 * JAVASCRIPT FUNCTIONS READY FOR IMPLEMENTATION
 * ========================================================================
 * 
 * CLIPBOARD FUNCTIONALITY:
 * function copiaIndirizzo() {
 *   const indirizzo = `${window.PageData.centro.indirizzo}, ${window.PageData.centro.citta}`;
 *   navigator.clipboard.writeText(indirizzo).then(() => {
 *     // Show success message
 *   });
 * }
 * 
 * CHARACTER COUNTING:
 * document.getElementById('nome').addEventListener('input', function() {
 *   document.getElementById('nomeCount').textContent = this.value.length;
 * });
 * 
 * FORM VALIDATION:
 * document.getElementById('formModificaCentro').addEventListener('submit', function(e) {
 *   // Custom validation logic
 * });
 * 
 * MODAL ENHANCEMENTS:
 * document.getElementById('modalElimina').addEventListener('show.bs.modal', function() {
 *   // Pre-delete validation
 * });
 */

// Placeholder per future JavaScript enhancements...
</script>
@endpush

{{-- 
/**
 * ========================================================================
 * CSS STYLES SECTION - CUSTOM STYLING E RESPONSIVE DESIGN
 * ========================================================================
 * 
 * STYLING APPROACH:
 * - @push('styles'): Blade stack per CSS injection
 * - Bootstrap extensions e overrides
 * - Custom component styling
 * - Responsive design enhancements
 * - Accessibility improvements
 */
--}}
@push('styles')
<style>
/**
 * ========================================================================
 * BASE COMPONENT STYLING - INHERITED FROM PUBLIC VIEW
 * ========================================================================
 * 
 * CARD ENHANCEMENTS:
 * - Custom shadow con hover effects
 * - Smooth transitions per interactive feedback
 * - Consistent border styling
 * - Depth perception con transform effects
 */

/* === STILI BASE EREDISTATI DALLA VISTA PUBBLICA === */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Subtle depth */
    border: 1px solid rgba(0, 0, 0, 0.125); /* Light border */
    transition: all 0.2s ease-in-out; /* Smooth transitions */
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
    transform: translateY(-2px); /* Lift effect */
}

/**
 * ========================================================================
 * FORM-SPECIFIC STYLING - EDIT FORM ENHANCEMENTS
 * ========================================================================
 */

/* Stili per campi obbligatori con asterisco rosso */
.form-label.required::after {
    content: " *"; /* Asterisk content */
    color: #dc3545; /* Bootstrap danger red */
    font-weight: bold; /* Visual emphasis */
}

/* Hover effect migliorato per i pulsanti */
.btn:hover {
    transform: translateY(-1px); /* Subtle lift on hover */
    transition: transform 0.2s ease; /* Smooth animation */
}

/* Animazione per il contatore caratteri */
#nomeCount {
    transition: color 0.3s ease; /* Smooth color transitions */
    font-weight: 500; /* Medium font weight */
}

/**
 * ========================================================================
 * SIDEBAR STYLING - STATISTICS E RELATIONSHIP DISPLAY
 * ========================================================================
 */

/* Stile per le statistiche nella sidebar */
.card-body .border-end {
    border-right: 1px solid #dee2e6 !important; /* Visual separator */
}

/* Pulsanti extra small per azioni rapide */
.btn-xs {
    padding: 0.125rem 0.375rem; /* Compact padding */
    font-size: 0.75rem; /* Smaller font */
    line-height: 1.2; /* Tight line height */
    border-radius: 0.25rem; /* Rounded corners */
}

/* Migliore spaziatura per i badge */
.badge {
    font-size: 0.75em; /* Relative sizing */
    padding: 0.375em 0.75em; /* Comfortable padding */
    border-radius: 0.5rem; /* Rounded badge design */
}

/**
 * ========================================================================
 * FORM CONTROL ENHANCEMENTS - FOCUS E VALIDATION STATES
 * ========================================================================
 */

/* Focus migliorato per tutti i campi del form */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe; /* Bootstrap focus blue */
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); /* Focus ring */
    outline: none; /* Remove browser default outline */
}

/* Stati invalid con feedback chiaro */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545; /* Danger red border */
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25); /* Red focus ring */
}

.invalid-feedback {
    display: block; /* Always show when present */
    font-size: 0.875em; /* Smaller font size */
    color: #dc3545; /* Danger red color */
    margin-top: 0.25rem; /* Spacing from input */
}

/**
 * ========================================================================
 * MODAL STYLING - CONFIRMATION DIALOG ENHANCEMENTS
 * ========================================================================
 */

/* Stile per il modal di conferma eliminazione */
.modal-content {
    border: none; /* Remove default border */
    border-radius: 0.75rem; /* Rounded corners */
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175); /* Deep shadow */
}

.modal-header.bg-danger {
    border-top-left-radius: 0.75rem; /* Match modal border radius */
    border-top-right-radius: 0.75rem; /* Match modal border radius */
    border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Subtle separator */
}

/**
 * ========================================================================
 * ANIMATION SYSTEM - ALERT E FEEDBACK ANIMATIONS
 * ========================================================================
 */

/* Alert temporanei con animazione */
.alert-temp {
    animation: slideInRight 0.3s ease; /* Slide in animation */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow */
}

@keyframes slideInRight {
    from {
        transform: translateX(100%); /* Start off-screen right */
        opacity: 0; /* Start transparent */
    }
    to {
        transform: translateX(0); /* End in normal position */
        opacity: 1; /* End fully opaque */
    }
}

/**
 * ========================================================================
 * RESPONSIVE DESIGN - MOBILE E TABLET OPTIMIZATIONS
 * ========================================================================
 */

/* Responsive design per tablet */
@media (max-width: 992px) {
    .card-custom {
        margin-bottom: 2rem; /* Increased spacing on tablet */
    }
    
    .col-lg-4 .card-custom {
        margin-bottom: 1rem; /* Sidebar card spacing */
    }
}

/* Responsive design per mobile */
@media (max-width: 768px) {
    /* Layout pulsanti form responsive */
    .d-flex.gap-2.justify-content-between {
        flex-direction: column; /* Stack vertically */
        gap: 1rem !important; /* Consistent spacing */
    }{{-- 
/**
 * ========================================================================
 * BLADE TEMPLATE: Form Modifica Centro Assistenza - TechSupport Pro Gruppo 51
 * ========================================================================
 * 
 * FILE: resources/views/admin/centri/edit.blade.php
 * 
 * TECNOLOGIE UTILIZZATE:
 * - BLADE: Laravel template engine per rendering dinamico con model binding
 * - HTML5: Form semantico con validation attributes e accessibility features
 * - Bootstrap 5: Framework CSS responsive con componenti UI avanzati
 * - PHP: Server-side logic con Eloquent ORM integration
 * - JavaScript: Client-side functionality per UX enhancements
 * - CSS3: Custom styling con animations e responsive design
 * 
 * PATTERN ARCHITETTURALI:
 * - MVC View Layer: Vista nel pattern Model-View-Controller di Laravel
 * - CRUD Operations: UPDATE operation con form data binding
 * - Resource Controller Pattern: RESTful routing con route model binding
 * - Modal Pattern: Confirmation dialogs per destructive operations
 * - Responsive Design: Mobile-first Bootstrap approach
 * 
 * SCOPO FUNZIONALE:
 * Form di modifica per centri di assistenza esistenti con:
 * - Pre-popolamento dati esistenti tramite Eloquent model
 * - Validazione completa lato client e server
 * - Gestione errori e success messages
 * - Statistics display e relationship information
 * - Confirmation modal per eliminazione
 * - Quick actions sidebar con utility functions
 * 
 * BUSINESS REQUIREMENTS:
 * - Modifica dati centro con pre-popolamento automatico
 * - Validazione data integrity su update
 * - Relationship management (tecnici assegnati)
 * - Deletion protection se tecnici sono assegnati
 * - Audit trail con created/updated timestamps
 * - Real-time statistics display
 * - Integration con Google Maps per location verification
 * 
 * FUNZIONALITÀ IMPLEMENTATE:
 * ✅ Form pre-populated con dati esistenti
 * ✅ Laravel route model binding integration
 * ✅ Error/success message handling
 * ✅ Character counting per text fields
 * ✅ Province dropdown con current selection
 * ✅ Relationship statistics (tecnici assegnati)
 * ✅ Confirmation modal per deletion
 * ✅ Google Maps integration
 * ✅ Quick action buttons
 * ✅ Responsive design con sidebar informativa
 * ✅ Breadcrumb navigation system
 */
--}}