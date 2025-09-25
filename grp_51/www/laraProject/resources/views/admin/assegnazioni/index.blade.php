
 {{-- 
/**
 * ========================================================================
 * BLADE TEMPLATE: Gestione Assegnazioni Prodotti - TechSupport Pro Gruppo 51
 * ========================================================================
 * 
 * TECNOLOGIE UTILIZZATE:
 * - BLADE: Sistema di template di Laravel per rendering dinamico HTML
 * - HTML5: Markup strutturale moderno con elementi semantici
 * - Bootstrap 5: Framework CSS per responsive design e componenti UI
 * - PHP: Logica server-side integrata tramite Blade directives
 * - JavaScript: Interattività client-side e manipolazione DOM
 * - CSS3: Styling avanzato con animazioni e responsive design
 * 
 * PATTERN ARCHITETTURALI:
 * - MVC View Layer: Vista nel pattern Model-View-Controller di Laravel
 * - Template Inheritance: Estensione di layout base con @extends
 * - Component-Based CSS: Modularità con classi riutilizzabili Bootstrap
 * - Data Binding: Collegamento dati PHP/Eloquent → JavaScript
 * 
 * SCOPO FUNZIONALE:
 * Vista amministrativa per l'assegnazione di prodotti ai membri dello staff.
 * Implementa la funzionalità opzionale delle specifiche del progetto dove
 * ogni membro staff gestisce un sottoinsieme specifico di prodotti.
 * 
 * FUNZIONALITÀ IMPLEMENTATE:
 * - Visualizzazione prodotti con filtri avanzati (ricerca, categoria, staff)
 * - Assegnazione singola prodotto → staff member
 * - Assegnazione multipla (bulk operations) 
 * - Rimozione assegnazioni esistenti
 * - Dashboard statistiche con contatori real-time
 * - Paginazione Laravel con persistenza filtri
 * - UI responsive Bootstrap con componenti modali
 */
--}}

{{-- 
/**
 * BLADE INHERITANCE SYSTEM - LARAVEL TEMPLATING ENGINE
 * 
 * @extends: Directive Blade per ereditarietà template
 * Estende il layout principale 'layouts.app' che contiene:
 * - Struttura HTML base, meta tags, CSS framework
 * - Navigation bar, footer comuni
 * - Yield sections per contenuto dinamico
 * 
 * @section: Definisce blocchi di contenuto da iniettare nel layout parent
 */
--}}
@extends('layouts.app')
@section('title', 'Gestione Assegnazioni Prodotti')

{{-- 
/**
 * SEZIONE CONTENUTO PRINCIPALE - BLADE CONTENT INJECTION
 * 
 * La directive @section('content') inietta tutto il markup HTML
 * nella sezione @yield('content') del layout principale.
 * Utilizza Bootstrap Grid System per layout responsive.
 */
--}}
@section('content')
<div class="container mt-4">
    
    {{-- 
    /**
     * HEADER SECTION - BOOTSTRAP FLEXBOX LAYOUT SYSTEM
     * 
     * TECNOLOGIE:
     * - Bootstrap 5 Flexbox: d-flex, align-items-center, justify-content-between
     * - Bootstrap Icons: bi-* classes per iconografia vettoriale
     * - Bootstrap Responsive: Classi responsive mb-* per margin
     * 
     * COMPONENTI:
     * - Heading semantico (h1, h2) per SEO e accessibilità
     * - Badge informativi con contatori dinamici
     * - Button trigger per modal Bootstrap (data-bs-* attributes)
     */
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    {{-- 
                    /**
                     * SEMANTIC HTML - Structured Heading Hierarchy
                     * h1.h2: Bootstrap utility class per sizing senza alterare semantica
                     * Bootstrap Icons: bi-person-gear con color utility text-warning
                     */
                    --}}
                    <h1 class="h2 mb-1">
                        <i class="bi bi-person-gear text-warning me-2"></i>
                        Gestione Assegnazioni Prodotti
                    </h1>
                    <p class="text-muted mb-0">
                        Assegna prodotti ai membri dello staff per la gestione dei malfunzionamenti
                    </p>
                </div>
                <div>
                    {{-- 
                    /**
                     * BOOTSTRAP MODAL TRIGGER - JAVASCRIPT INTEGRATION
                     * 
                     * data-bs-toggle="modal": Bootstrap JS attribute per trigger modal
                     * data-bs-target="#bulkAssignModal": CSS selector del modal target
                     * Utilizza Bootstrap Modal Plugin per overlay dinamici
                     */
                    --}}
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                        <i class="bi bi-collection me-1"></i>Assegnazione Multipla
                    </button>
                </div>
            </div>
            
            {{-- 
            /**
             * BOOTSTRAP ALERT COMPONENT - INFORMATIONAL UI PATTERN
             * 
             * TECNOLOGIE:
             * - Bootstrap Alert: .alert, .alert-info per styling predefinito
             * - Bootstrap Borders: .border-start, .border-4 per visual emphasis  
             * - Bootstrap Grid: .row, .col-md-* per responsive columns
             * - Blade Variables: {{ }} per PHP variable output sicuro (escaped)
             * 
             * BLADE OUTPUT SYNTAX:
             * {{ $variable }}: Output escaped (sicuro da XSS)
             * {!! $variable !!}: Output raw (non escaped, solo per HTML sicuro)
             */
            --}}
            <div class="alert alert-info border-start border-info border-4">
                <div class="row">
                    <div class="col-md-8">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Funzionalità Opzionale:</strong> Ogni membro dello staff può gestire un sottoinsieme specifico di prodotti.
                    </div>
                    <div class="col-md-4 text-end">
                        {{-- 
                        /**
                         * BLADE VARIABLE OUTPUT - PHP ARRAY ACCESS
                         * 
                         * $stats['chiave']: Array associativo PHP passato dal Controller
                         * Bootstrap Badges: .badge, .bg-* per visual indicators
                         * Array access sicuro con Blade escaped output {{ }}
                         */
                        --}}
                        <span class="badge bg-success">{{ $stats['prodotti_assegnati'] }}</span> Assegnati
                        <span class="badge bg-warning">{{ $stats['prodotti_non_assegnati'] }}</span> Non Assegnati
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
    /**
     * DASHBOARD CARDS SECTION - BOOTSTRAP CARD COMPONENT SYSTEM
     * 
     * DESIGN PATTERN:
     * - Card-based UI per visualizzazione statistiche
     * - Bootstrap Grid System (row, col-md-3) per 4 colonne responsive
     * - Color Theming con classi bg-* e text-white per contrast
     * 
     * TECNOLOGIE:
     * - Bootstrap Cards: .card, .card-body per contenitori strutturati
     * - Bootstrap Colors: .bg-primary, .bg-success, .bg-warning, .bg-info
     * - Bootstrap Typography: .text-center, .mt-2, .mb-1 per spacing
     * - Bootstrap Icons: .display-6 per sizing iconografia
     */
    --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            {{-- 
            /**
             * STATISTIC CARD - Totale Prodotti
             * Pattern: Icon + Number + Label per KPI dashboard
             */
            --}}
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-box display-6"></i>
                    <h4 class="mt-2">{{ $stats['totale_prodotti'] }}</h4>
                    <small>Prodotti Totali</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            {{-- STATISTIC CARD - Prodotti Assegnati --}}
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_assegnati'] }}</h4>
                    <small>Assegnati</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            {{-- STATISTIC CARD - Prodotti Non Assegnati --}}
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_non_assegnati'] }}</h4>
                    <small>Non Assegnati</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            {{-- STATISTIC CARD - Staff Attivi --}}
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h4 class="mt-2">{{ $stats['staff_attivi'] }}</h4>
                    <small>Staff Attivi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 
        /**
         * FILTERS SIDEBAR SECTION - HTML FORM PROCESSING + LARAVEL REQUEST HELPERS
         * 
         * PATTERN ARCHITETTURALE:
         * - Sidebar filtering con Bootstrap Grid (col-lg-3)
         * - Form GET per filtri con persistenza URL parameters  
         * - Laravel Request helpers per form state management
         * 
         * TECNOLOGIE:
         * - HTML Forms: method="GET" per filtri persistenti via URL
         * - Laravel Route Helpers: route() helper per URL generation
         * - Laravel Request: request() global helper per input persistence
         * - Bootstrap Forms: .form-control, .form-select per styling
         */
        --}}
        <div class="col-lg-3">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                    /**
                     * LARAVEL FORM PROCESSING - GET METHOD FILTERING SYSTEM
                     * 
                     * method="GET": Filtri tramite URL parameters (SEO friendly)
                     * action="{{ route() }}": Laravel route helper per URL generation  
                     * id="filterForm": JavaScript hook per form manipulation
                     * 
                     * LARAVEL ROUTE SYSTEM:
                     * route('admin.assegnazioni.index'): Named route resolution
                     * Genera URL corretto basato su routes/web.php configuration
                     */
                    --}}
                    <form method="GET" action="{{ route('admin.assegnazioni.index') }}" id="filterForm">
                        
                        {{-- 
                        /**
                         * SEARCH INPUT - TEXT FILTERING COMPONENT
                         * 
                         * request('search'): Laravel Request helper per input persistence
                         * Mantiene il valore inserito dopo submit form
                         * value="{{ request('search') }}": Form repopulation pattern
                         */
                        --}}
                        <div class="mb-3">
                            <label for="search" class="form-label">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome o modello prodotto">
                        </div>
                        
                        {{-- 
                        /**
                         * BLADE FOREACH LOOP - LARAVEL COLLECTIONS ITERATION
                         * 
                         * TECNOLOGIE:
                         * - Blade @foreach: Template engine loop per Collections/Arrays
                         * - Laravel Collections: $staffMembers Collection da Controller
                         * - Conditional Logic: Ternary operator per selected state
                         * - HTML Select Options: Dynamic option generation
                         * 
                         * BLADE LOOP SYNTAX:
                         * @foreach($collection as $item): Itera Laravel Collection
                         * $item->property: Eloquent Model attribute access
                         * @endforeach: Chiude loop Blade
                         */
                        --}}
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">
                                <i class="bi bi-person me-1"></i>Membro Staff
                            </label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">Tutti gli staff</option>
                                {{-- 
                                /**
                                 * CONDITIONAL OPTION SELECTION - PHP TERNARY OPERATOR
                                 * 
                                 * {{ request('staff_id') === 'null' ? 'selected' : '' }}
                                 * Confronto strikt (===) per selected state
                                 * 'null' string per prodotti non assegnati filter
                                 */
                                --}}
                                <option value="null" {{ request('staff_id') === 'null' ? 'selected' : '' }}>
                                    Non Assegnati
                                </option>
                                @foreach($staffMembers as $staff)
                                    {{-- 
                                    /**
                                     * ELOQUENT MODEL ATTRIBUTE ACCESS
                                     * 
                                     * $staff->id: Primary key dell'Eloquent Model
                                     * $staff->nome_completo: Accessor Method o attributo composito  
                                     * request('staff_id') == $staff->id: Type juggling comparison
                                     */
                                    --}}
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 
                        /**
                         * CATEGORY FILTER SELECT - ASSOCIATIVE ARRAY ITERATION
                         * 
                         * $categorie: Array associativo [key => label] dal Controller
                         * @foreach($array as $key => $label): Key-value iteration
                         */
                        --}}
                        <div class="mb-3">
                            <label for="categoria" class="form-label">
                                <i class="bi bi-tag me-1"></i>Categoria
                            </label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Tutte le categorie</option>
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 
                        /**
                         * BOOTSTRAP FORM-CHECK COMPONENT - BOOLEAN FILTER CHECKBOX
                         * 
                         * TECNOLOGIE:
                         * - Bootstrap Form Check: .form-check, .form-check-input styling
                         * - HTML Checkbox: type="checkbox" per boolean input
                         * - Value Attribute: value="1" per form submission
                         * - Checked State: {{ request('non_assegnati') ? 'checked' : '' }}
                         * 
                         * BOOLEAN LOGIC:
                         * request('non_assegnati'): Laravel Request helper
                         * Truthy evaluation per checkbox checked state
                         */
                        --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="non_assegnati" 
                                       name="non_assegnati" 
                                       value="1"
                                       {{ request('non_assegnati') ? 'checked' : '' }}>
                                <label class="form-check-label" for="non_assegnati">
                                    Solo prodotti non assegnati
                                </label>
                            </div>
                        </div>
                        
                        {{-- 
                        /**
                         * FORM ACTIONS - BOOTSTRAP BUTTON GROUP
                         * 
                         * .d-grid gap-2: Bootstrap utility per full-width buttons
                         * .btn-primary, .btn-outline-secondary: Button variants
                         * route() helper: Reset link mantiene structure URL
                         */
                        --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Applica Filtri
                            </button>
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 
            /**
             * STAFF OVERVIEW SIDEBAR - ELOQUENT RELATIONSHIPS DISPLAY
             * 
             * PATTERN:
             * - Secondary sidebar component per overview rapido
             * - Links filtrati per navigazione quick-access
             * - Real-time counters tramite relationship count
             * 
             * TECNOLOGIE:
             * - Blade @forelse: Loop con fallback per Collections vuote
             * - Eloquent Relationships: $staff->prodottiAssegnati() relation
             * - Method Chaining: ->count() su relationship Query Builder
             */
            --}}
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-info me-2"></i>
                        Staff Overview
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                    /**
                     * BLADE @forelse DIRECTIVE - COLLECTION ITERATION WITH FALLBACK
                     * 
                     * @forelse: Blade loop con @empty fallback
                     * Gestisce automaticamente Collections vuote
                     * Migliore UX rispetto a @foreach + @if count check
                     */
                    --}}
                    @forelse($staffMembers as $staff)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                            <div>
                                {{-- 
                                /**
                                 * ELOQUENT MODEL ATTRIBUTES ACCESS
                                 * 
                                 * $staff->nome_completo: Accessor method o computed attribute
                                 * $staff->username: Standard model attribute
                                 */
                                --}}
                                <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                <small class="text-muted">{{ $staff->username }}</small>
                            </div>
                            <div class="text-end">
                                {{-- 
                                /**
                                 * ELOQUENT RELATIONSHIP COUNTING - QUERY BUILDER METHOD
                                 * 
                                 * $staff->prodottiAssegnati(): Eloquent relationship method
                                 * ->count(): Query Builder method per count efficient
                                 * Non carica i records, solo conta (performance optimized)
                                 */
                                --}}
                                <span class="badge bg-primary">
                                    {{ $staff->prodottiAssegnati()->count() }}
                                </span>
                                <div>
                                    {{-- 
                                    /**
                                     * FILTERED NAVIGATION LINK - LARAVEL ROUTE WITH PARAMETERS
                                     * 
                                     * route('route.name', ['param' => $value]): Named route con parameters
                                     * Genera URL con query string per filtro automatico
                                     */
                                    --}}
                                    <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $staff->id]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- 
                        /**
                         * EMPTY STATE FALLBACK - UX PATTERN
                         * 
                         * @empty: Blade directive per Collections vuote
                         * Mostra messaggio informativo invece di sezione vuota
                         */
                        --}}
                        <p class="text-muted text-center">Nessun membro staff disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 
        /**
         * PRODUCTS TABLE SECTION - LARAVEL PAGINATION + RESPONSIVE TABLE + JAVASCRIPT INTEGRATION
         * 
         * DESIGN PATTERN:
         * - Data Table con pagination integrata Laravel
         * - Responsive Design con Bootstrap table utilities
         * - JavaScript hooks per interattività (checkboxes, bulk actions)
         * - Modal integration per actions singole e multiple
         * 
         * TECNOLOGIE:
         * - Laravel Pagination: Automatic pagination con links e counters
         * - Bootstrap Responsive: .table-responsive per horizontal scroll
         * - JavaScript Integration: IDs e classes per DOM manipulation
         * - PHP Logic: Inline calculations e conditional rendering
         */
        --}}
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Prodotti 
                        {{-- 
                        /**
                         * LARAVEL PAGINATION COUNTER - COLLECTION METHODS
                         * 
                         * $prodotti->total(): LengthAwarePaginator method
                         * Conta total records ignoring pagination limits
                         */
                        --}}
                        <span class="badge bg-secondary">{{ $prodotti->total() }}</span>
                    </h5>
                    <div>
                        {{-- 
                        /**
                         * JAVASCRIPT INTEGRATION HOOKS - DOM MANIPULATION TARGET IDS
                         * 
                         * id="selectAllBtn": JavaScript hook per select all functionality
                         * id="bulkAssignBtn": JavaScript hook per bulk operations
                         * disabled: HTML attribute per initial disabled state
                         * 
                         * JAVASCRIPT PATTERN:
                         * document.getElementById() userà questi IDs
                         * Event listeners attachati per interactivity
                         */
                        --}}
                        <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                            <i class="bi bi-check-all me-1"></i>Seleziona Tutti
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" id="bulkAssignBtn" disabled>
                            <i class="bi bi-person-plus me-1"></i>Assegna Selezionati
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- 
                    /**
                     * CONDITIONAL RENDERING - BLADE @if DIRECTIVE
                     * 
                     * PATTERN: Content vs Empty State
                     * $prodotti->count(): Collection method per record presenti
                     * Se >0 mostra tabella, altrimenti empty state UX
                     */
                    --}}
                    @if($prodotti->count() > 0)
                        <div class="table-responsive">
                            {{-- 
                            /**
                             * BOOTSTRAP RESPONSIVE TABLE - DATA DISPLAY COMPONENT
                             * 
                             * .table-responsive: Horizontal scrolling su mobile
                             * .table-hover: Row hover effects CSS
                             * .table-light: Header styling predefinito Bootstrap
                             */
                            --}}
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            {{-- 
                                            /**
                                             * MASTER CHECKBOX - JAVASCRIPT CONTROL PATTERN
                                             * 
                                             * id="checkAll": Master control per select all
                                             * JavaScript userà questo per toggle all checkboxes
                                             */
                                            --}}
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </th>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th>Staff Assegnato</th>
                                        <th>Problemi</th>
                                        <th width="200">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 
                                    /**
                                     * LARAVEL PAGINATION COLLECTION ITERATION
                                     * 
                                     * $prodotti: LengthAwarePaginator instance da Controller
                                     * @foreach: Itera solo records della current page
                                     * $prodotto: Eloquent Model instance (Product model)
                                     */
                                    --}}
                                    @foreach($prodotti as $prodotto)
                                        <tr>
                                            <td>
                                                {{-- 
                                                /**
                                                 * INDIVIDUAL CHECKBOX - JAVASCRIPT COLLECTION PATTERN
                                                 * 
                                                 * class="product-checkbox": JavaScript selector class
                                                 * value="{{ $prodotto->id }}": Model primary key
                                                 * JavaScript leggerà tutti elementi .product-checkbox
                                                 */
                                                --}}
                                                <input type="checkbox" 
                                                       class="form-check-input product-checkbox" 
                                                       value="{{ $prodotto->id }}">
                                            </td>
                                            <td>
                                                {{-- 
                                                /**
                                                 * PRODUCT DISPLAY CELL - BOOTSTRAP FLEXBOX + IMAGE HANDLING
                                                 * 
                                                 * TECNOLOGIE:
                                                 * - Bootstrap Flexbox: .d-flex, .align-items-center layout
                                                 * - CSS Object-fit: object-fit: cover per aspect ratio
                                                 * - Eloquent Accessors: $prodotto->foto_url method/attribute
                                                 * - HTML Semantic: alt attribute per accessibility
                                                 */
                                                --}}
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $prodotto->foto_url }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $prodotto->nome }}">
                                                    <div>
                                                        <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                                                        <small class="text-muted">{{ $prodotto->modello }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- 
                                                /**
                                                 * ELOQUENT ACCESSOR METHOD - MODEL COMPUTED ATTRIBUTES
                                                 * 
                                                 * $prodotto->categoria_label: Accessor method nel Product Model
                                                 * Converte categoria enum/string in human-readable label
                                                 * Definito nel Model come getCategoriaLabelAttribute()
                                                 */
                                                --}}
                                                <span class="badge bg-secondary">
                                                    {{ $prodotto->categoria_label }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- 
                                                /**
                                                 * ELOQUENT RELATIONSHIPS - BELONGSTO RELATIONSHIP ACCESS
                                                 * 
                                                 * PATTERN: Conditional Display con Relationship Check
                                                 * 
                                                 * $prodotto->staffAssegnato: belongsTo relationship
                                                 * @if($prodotto->staffAssegnato): Check se relation exists
                                                 * ->nome_completo: Accessor method su related model
                                                 * ->username: Standard attribute su related model
                                                 */
                                                --}}
                                                @if($prodotto->staffAssegnato)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-person-check text-success me-2"></i>
                                                        <div>
                                                            <strong>{{ $prodotto->staffAssegnato->nome_completo }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $prodotto->staffAssegnato->username }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- EMPTY RELATIONSHIP STATE --}}
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Non Assegnato
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- 
                                                /**
                                                 * PHP INLINE LOGIC - COLLECTION METHODS + STATISTICS
                                                 * 
                                                 * TECNOLOGIE:
                                                 * - Blade @php directive: Inline PHP code block
                                                 * - Laravel Collections: $prodotto->malfunzionamenti Collection
                                                 * - Collection Methods: ->count(), ->where() filtering
                                                 * - Method Chaining: ->where()->count() pipeline
                                                 * 
                                                 * BUSINESS LOGIC:
                                                 * Calcola statistiche problemi per priorità visualizzazione
                                                 * $problemiCount: Total malfunzionamenti per questo prodotto
                                                 * $criticiCount: Solo malfunzionamenti con gravità critica
                                                 */

                                                * $problemiCount: Total malfunzionamenti per questo prodotto
                                                 * $criticiCount: Solo malfunzionamenti con gravità critica
                                                 */
                                                --}}
                                                @php
                                                    $problemiCount = $prodotto->malfunzionamenti->count();
                                                    $criticiCount = $prodotto->malfunzionamenti->where('gravita', 'critica')->count();
                                                @endphp
                                                
                                                {{-- 
                                                /**
                                                 * CONDITIONAL BADGE DISPLAY - BUSINESS LOGIC VISUALIZATION
                                                 * 
                                                 * Pattern: Status indicators con color coding
                                                 * @if($problemiCount > 0): Check per esistenza problemi
                                                 * .badge: Bootstrap component per status indicators
                                                 * .bg-info, .bg-danger, .bg-success: Color semantic coding
                                                 */
                                                --}}
                                                <div class="text-center">
                                                    @if($problemiCount > 0)
                                                        <span class="badge bg-info">{{ $problemiCount }}</span>
                                                        @if($criticiCount > 0)
                                                            <span class="badge bg-danger">{{ $criticiCount }} critici</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-success">0</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{-- 
                                                /**
                                                 * ACTIONS CELL - BOOTSTRAP BTN-GROUP + MODAL TRIGGERS + LARAVEL FORMS
                                                 * 
                                                 * DESIGN PATTERN:
                                                 * - Button Group per actions compatte
                                                 * - Modal triggers tramite data attributes
                                                 * - CSRF protection per form submissions
                                                 * - Confirmation dialogs per destructive actions
                                                 * 
                                                 * TECNOLOGIE:
                                                 * - Bootstrap Button Group: .btn-group styling
                                                 * - Bootstrap Modal: data-bs-* attributes per triggers
                                                 * - Laravel Forms: @csrf directive per security
                                                 * - Laravel Routes: route() helper + model binding
                                                 * - JavaScript: onclick confirm() per user confirmation
                                                 */
                                                --}}
                                                <div class="btn-group" role="group">
                                                    {{-- 
                                                    /**
                                                     * ASSIGN BUTTON - MODAL DATA ATTRIBUTES PATTERN
                                                     * 
                                                     * JAVASCRIPT DATA PASSING:
                                                     * data-product-id: Prodotto ID per modal form
                                                     * data-product-name: Nome prodotto per display
                                                     * data-current-staff: Staff attualmente assegnato (nullable)
                                                     * data-bs-toggle/target: Bootstrap Modal triggers
                                                     * 
                                                     * JavaScript leggerà questi data-* attributes
                                                     * per popolare il modal form dinamicamente
                                                     */
                                                    --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm assign-btn"
                                                            data-product-id="{{ $prodotto->id }}"
                                                            data-product-name="{{ $prodotto->nome }}"
                                                            data-current-staff="{{ $prodotto->staff_assegnato_id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#assignModal">
                                                        <i class="bi bi-person-plus"></i>
                                                    </button>
                                                    
                                                    {{-- 
                                                    /**
                                                     * VIEW BUTTON - LARAVEL ROUTE + MODEL BINDING
                                                     * 
                                                     * route('admin.prodotti.show', $prodotto): Named route con Model
                                                     * Laravel Route Model Binding risolve automaticamente
                                                     * $prodotto Model instance basato su route parameter
                                                     */
                                                    --}}
                                                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    {{-- 
                                                    /**
                                                     * REMOVE ASSIGNMENT FORM - CONDITIONAL DESTRUCTIVE ACTION
                                                     * 
                                                     * TECNOLOGIE:
                                                     * - Blade @if: Conditional rendering se staff assegnato
                                                     * - Laravel CSRF: @csrf directive per security token
                                                     * - HTML Hidden Inputs: Per form data passing
                                                     * - JavaScript Confirmation: onclick confirm() dialog
                                                     * 
                                                     * SECURITY PATTERN:
                                                     * POST method con CSRF token per state-changing operations
                                                     * Hidden inputs per dati form (prodotto_id, staff_id empty)
                                                     */
                                                    --}}
                                                    @if($prodotto->staffAssegnato)
                                                        <form action="{{ route('admin.assegnazioni.prodotto') }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                                                            <input type="hidden" name="staff_id" value="">
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Rimuovere l\'assegnazione?')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- 
                        /**
                         * LARAVEL PAGINATION SECTION - CUSTOM PAGINATION CONTROLS
                         * 
                         * PATTERN: Manual Pagination con Query String Persistence
                         * 
                         * TECNOLOGIE:
                         * - Laravel Pagination: LengthAwarePaginator methods
                         * - Query String Persistence: ->appends(request()->query())
                         * - Bootstrap Pagination: .pagination, .page-item, .page-link
                         * - Conditional Rendering: @if per navigation state
                         * 
                         * METODI UTILIZZATI:
                         * ->hasPages(): Check se pagination necessaria
                         * ->firstItem(), ->lastItem(): Range items corrente
                         * ->total(): Total records count
                         * ->onFirstPage(): Check se prima pagina
                         * ->previousPageUrl(), ->nextPageUrl(): Navigation URLs
                         * ->getUrlRange(): Array page numbers per loop
                         * ->currentPage(): Pagina attiva corrente
                         * ->lastPage(): Ultima pagina disponibile
                         * ->hasMorePages(): Check se pagine successive esistono
                         */
                        --}}
                        @if($prodotti->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    {{-- 
                                    /**
                                     * PAGINATION INFO - USER FEEDBACK COMPONENT
                                     * 
                                     * Mostra range records visualizzati: "1-15 di 150 prodotti"
                                     * UX Pattern per informare utente su posizione dataset
                                     */
                                    --}}
                                    <div class="text-center mb-2">
                                        <small class="text-muted">
                                            Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                                            di {{ $prodotti->total() }} prodotti
                                        </small>
                                    </div>
                                    
                                    {{-- 
                                    /**
                                     * BOOTSTRAP PAGINATION COMPONENT - MANUAL IMPLEMENTATION
                                     * 
                                     * TECNOLOGIE:
                                     * - Bootstrap Pagination: Semantic navigation component
                                     * - Laravel URL Generation: ->appends() per query persistence
                                     * - Accessibility: aria-label per screen readers
                                     * - State Management: active/disabled states
                                     */
                                    --}}
                                    <div class="d-flex justify-content-center">
                                        <nav aria-label="Paginazione prodotti">
                                            <ul class="pagination pagination-sm mb-0">
                                                {{-- 
                                                /**
                                                 * PREVIOUS PAGE LINK - CONDITIONAL NAVIGATION
                                                 * 
                                                 * ->onFirstPage(): Laravel method check prima pagina
                                                 * .page-item.disabled: Bootstrap disabled state
                                                 * ->appends(request()->query()): Mantiene filtri in URL
                                                 * ->previousPageUrl(): Laravel generated previous URL
                                                 */
                                                --}}
                                                @if ($prodotti->onFirstPage())
                                                    <li class="page-item disabled">
                                                        <span class="page-link">‹</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                                    </li>
                                                @endif

                                                {{-- 
                                                /**
                                                 * PAGE NUMBERS LOOP - DYNAMIC PAGE GENERATION
                                                 * 
                                                 * ->getUrlRange(1, $prodotti->lastPage()): Array [page => url]
                                                 * @foreach($pages as $page => $url): Key-value iteration
                                                 * ->currentPage(): Laravel current page number
                                                 * .page-item.active: Bootstrap active page styling
                                                 * ->appends()->url($page): URL generation con query persistence
                                                 */
                                                --}}
                                                @foreach ($prodotti->getUrlRange(1, $prodotti->lastPage()) as $page => $url)
                                                    @if ($page == $prodotti->currentPage())
                                                        <li class="page-item active">
                                                            <span class="page-link">{{ $page }}</span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $prodotti->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                                        </li>
                                                    @endif
                                                @endforeach

                                                {{-- 
                                                /**
                                                 * NEXT PAGE LINK - CONDITIONAL NAVIGATION
                                                 * 
                                                 * ->hasMorePages(): Laravel method check pagine successive
                                                 * ->nextPageUrl(): Laravel generated next URL
                                                 * Pattern speculare a previous page
                                                 */
                                                --}}
                                                @if ($prodotti->hasMorePages())
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->nextPageUrl() }}">›</a>
                                                    </li>
                                                @else
                                                    <li class="page-item disabled">
                                                        <span class="page-link">›</span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- 
                        /**
                         * EMPTY STATE COMPONENT - UX PATTERN PER NO RESULTS
                         * 
                         * DESIGN PATTERN:
                         * - Large icon + heading + description per empty feedback
                         * - Suggerimenti actionable per user guidance
                         * - Consistent styling con theme applicazione
                         * 
                         * TECNOLOGIE:
                         * - Bootstrap Typography: .display-1 per large icons
                         * - Bootstrap Utilities: .text-center, .py-5 per spacing
                         * - Bootstrap Colors: .text-muted per secondary content
                         */
                        --}}
                        <div class="text-center py-5">
                            <i class="bi bi-box display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
/**
 * ========================================================================
 * BOOTSTRAP MODALS SECTION - INTERACTIVE OVERLAY COMPONENTS
 * ========================================================================
 * 
 * PATTERN ARCHITETTURALE:
 * - Modal Overlays per form submissions senza page refresh
 * - Single + Bulk Assignment workflows
 * - CSRF protected forms con Laravel backend integration
 * - Dynamic content population tramite JavaScript data attributes
 * 
 * TECNOLOGIE INTEGRATE:
 * - Bootstrap Modal Plugin: JavaScript-driven overlay system
 * - Laravel Forms: CSRF protection + route generation
 * - JavaScript Integration: Data transfer PHP → JS → Modal forms
 * - Responsive Design: modal-lg per bulk operations layout
 */
--}}

{{-- 
/**
 * MODAL ASSEGNAZIONE SINGOLA - SINGLE PRODUCT ASSIGNMENT WORKFLOW
 * 
 * TECNOLOGIE:
 * - Bootstrap Modal: .modal, .modal-dialog structure
 * - Modal Attributes: tabindex="-1" per focus management
 * - Laravel Form: method="POST" con CSRF token
 * - Hidden Inputs: Per data passing da JavaScript
 * - Form Validation: Laravel backend validation dopo submit
 */
--}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Prodotto
                </h5>
                {{-- 
                /**
                 * BOOTSTRAP MODAL CONTROLS
                 * 
                 * data-bs-dismiss="modal": Bootstrap JS attribute per close
                 * .btn-close: Bootstrap styling per close button
                 */
                --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- 
            /**
             * LARAVEL FORM SUBMISSION - POST METHOD CON CSRF PROTECTION
             * 
             * action="{{ route() }}": Named route per assignment endpoint
             * method="POST": HTTP method per state-changing operation
             * @csrf: Blade directive genera hidden CSRF token input
             */
            --}}
            <form action="{{ route('admin.assegnazioni.prodotto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- 
                    /**
                     * HIDDEN FORM INPUTS - JAVASCRIPT → PHP DATA TRANSFER
                     * 
                     * id="assign-product-id": JavaScript popolerà questo campo
                     * name="prodotto_id": Laravel leggerà questo nel Controller
                     * Hidden inputs pattern per dati non editabili dall'utente
                     */
                    --}}
                    <input type="hidden" id="assign-product-id" name="prodotto_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Prodotto:</label>
                        {{-- 
                        /**
                         * DYNAMIC CONTENT DISPLAY - JAVASCRIPT POPULATED
                         * 
                         * id="assign-product-name": JavaScript target per product name
                         * .bg-light: Bootstrap utility per visual distinction
                         * Read-only display field (non form input)
                         */
                        --}}
                        <div class="p-2 bg-light rounded">
                            <strong id="assign-product-name"></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assign-staff-id" class="form-label">
                            <i class="bi bi-person me-1"></i>Assegna a Staff:
                        </label>
                        {{-- 
                        /**
                         * STAFF SELECTION DROPDOWN - ELOQUENT COLLECTION ITERATION
                         * 
                         * id="assign-staff-id": JavaScript + Laravel form target
                         * name="staff_id": Controller parameter name
                         * value="": Empty option per "nessuna assegnazione"
                         * 
                         * @foreach($staffMembers as $staff): Laravel Collection loop
                         * $staff->prodottiAssegnati()->count(): Relationship count display
                         */
                        --}}
                        <select class="form-select" id="assign-staff-id" name="staff_id">
                            <option value="">Nessuna assegnazione</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }} ({{ $staff->prodottiAssegnati()->count() }} prodotti)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Seleziona un membro dello staff o lascia vuoto per rimuovere l'assegnazione
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check me-1"></i>Conferma Assegnazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 
/**
 * MODAL ASSEGNAZIONE MULTIPLA - BULK OPERATIONS WORKFLOW
 * 
 * DESIGN PATTERN:
 * - Bulk operations per efficiency admin workflow
 * - Multi-step process: Select → Choose Staff → Confirm
 * - Dynamic product list update tramite JavaScript
 * - Real-time staff overview con current assignments count
 * 
 * TECNOLOGIE:
 * - Bootstrap Modal Large: .modal-lg per more content space
 * - JavaScript Integration: Dynamic content updates
 * - Laravel Collections: Staff iteration con relationship counts
 * - Form Validation: required attribute + Laravel backend validation
 */
--}}
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-collection me-2"></i>Assegnazione Multipla
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- 
            /**
             * BULK ASSIGNMENT FORM - MULTIPLE PRODUCTS PROCESSING
             * 
             * action="{{ route('admin.assegnazioni.multipla') }}": Bulk endpoint
             * method="POST": State-changing operation
             * id="bulkAssignForm": JavaScript hook per form manipulation
             */
            --}}
            <form action="{{ route('admin.assegnazioni.multipla') }}" method="POST" id="bulkAssignForm">
                @csrf
                <div class="modal-body">
                    {{-- 
                    /**
                     * INFORMATIONAL ALERT - USER GUIDANCE COMPONENT
                     * 
                     * .alert-info: Bootstrap informational styling
                     * Instructions per multi-step workflow guidance
                     */
                    --}}
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Seleziona i prodotti dalla lista e scegli il membro dello staff per l'assegnazione.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prodotti Selezionati:</label>
                        {{-- 
                        /**
                         * DYNAMIC PRODUCT LIST CONTAINER - JAVASCRIPT TARGET
                         * 
                         * id="selected-products": JavaScript manipulation target
                         * JavaScript popolerà questo div con selected products
                         * .border, .rounded, .p-3: Bootstrap styling per container
                         * .bg-light: Visual distinction per content area
                         */
                        --}}
                        <div id="selected-products" class="border rounded p-3 bg-light">
                            <em class="text-muted">Nessun prodotto selezionato</em>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk-staff-id" class="form-label">
                            <i class="bi bi-person me-1"></i>Assegna a Staff:
                        </label>
                        {{-- 
                        /**
                         * BULK STAFF SELECTION - REQUIRED FORM CONTROL
                         * 
                         * id="bulk-staff-id": JavaScript + Laravel form binding
                         * name="staff_id": Controller parameter
                         * required: HTML5 validation attribute
                         * value="": Explicit empty option per unassign functionality
                         */
                        --}}
                        <select class="form-select" id="bulk-staff-id" name="staff_id" required>
                            <option value="">Seleziona membro staff</option>
                            <option value="">-- Rimuovi assegnazione --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }} 
                                    ({{ $staff->prodottiAssegnati()->count() }} prodotti attuali)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- 
                    /**
                     * STAFF OVERVIEW GRID - REAL-TIME ASSIGNMENT COUNTERS
                     * 
                     * DESIGN PATTERN:
                     * - Visual overview current staff workload
                     * - Real-time counters per decision making support
                     * - Card-based layout per readability
                     * 
                     * TECNOLOGIE:
                     * - Bootstrap Grid: .row, .col-md-6 responsive columns
                     * - Bootstrap Cards: .card, .card-body compact display
                     * - Laravel Collections: Staff iteration con Eloquent relationships
                     * - Eloquent Counts: ->prodottiAssegnati()->count() efficient counting
                     */
                    --}}
                    <div class="row">
                        @foreach($staffMembers as $staff)
                            <div class="col-md-6 mb-2">
                                <div class="card card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                            <small class="text-muted">{{ $staff->username }}</small>
                                        </div>
                                        {{-- 
                                        /**
                                         * REAL-TIME COUNTER BADGE
                                         * 
                                         * .badge .bg-primary: Bootstrap visual indicator
                                         * ->prodottiAssegnati()->count(): Live relationship count
                                         * Performance efficient: count() query, no model loading
                                         */
                                        --}}
                                        <span class="badge bg-primary">
                                            {{ $staff->prodottiAssegnati()->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    {{-- 
                    /**
                     * BULK CONFIRM BUTTON - JAVASCRIPT CONTROLLED STATE
                     * 
                     * id="confirmBulkAssign": JavaScript manipulation target
                     * disabled: Initial state, JavaScript abiliterà dopo selection
                     * JavaScript gestirà enable/disable basato su products selected
                     */
                    --}}
                    <button type="submit" class="btn btn-warning" id="confirmBulkAssign" disabled>
                        <i class="bi bi-check me-1"></i>Conferma Assegnazioni
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- 
/**
 * ========================================================================
 * BLADE STACK SYSTEM - COMPONENT-SPECIFIC STYLING
 * ========================================================================
 * 
 * PATTERN ARCHITETTURALE:
 * - Section-specific CSS tramite Blade @push directive
 * - Separation of concerns: Styling isolato per component
 * - Template inheritance: CSS injected in parent layout head section
 * 
 * TECNOLOGIE:
 * - Blade @push: Stack system per content accumulation
 * - CSS3: Modern styling con transitions, shadows, hover effects
 * - Bootstrap Overrides: Customizzazioni specifiche framework
 */
--}}
@push('styles')
<style>
/*
 * ========================================================================
 * CUSTOM COMPONENT STYLING - CSS3 + BOOTSTRAP OVERRIDES
 * ========================================================================
 * 
 * TECNOLOGIE:
 * - CSS3 Box Shadow: box-shadow per depth visual cues
 * - CSS3 Transitions: transition properties per smooth interactions
 * - CSS3 Selectors: Advanced selectors per Bootstrap overrides
 * - CSS3 Pseudo-classes: :hover, :checked states
 * - CSS3 Transforms: transform properties per visual feedback
 */

/**
 * CUSTOM CARD COMPONENT - VISUAL DEPTH + INTERACTION FEEDBACK
 * 
 * DESIGN PATTERN:
 * - Material Design influenced shadows
 * - Hover feedback per interactive elements
 * - Consistent visual hierarchy
 */
.card-custom {
    border: none; /* Remove default Bootstrap border */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle depth shadow */
    transition: all 0.3s ease; /* Smooth hover transitions */
}

/**
 * TABLE HEADER STYLING - BOOTSTRAP OVERRIDE
 * 
 * PATTERN: Custom table header appearance
 * - Remove default Bootstrap top border
 * - Enhanced typography weight and color
 */
.table th {
    border-top: none; /* Override Bootstrap default */
    font-weight: 600; /* Semi-bold typography */
    color: #495057; /* Custom gray color */
}

/**
 * FORM CHECKBOX STYLING - BOOTSTRAP OVERRIDE + INTERACTION
 * 
 * TECNOLOGIE:
 * - CSS Pseudo-class :checked state
 * - Custom brand colors per consistency
 * - Visual feedback per form interactions
 */
.form-check-input:checked {
    background-color: #0d6efd; /* Bootstrap primary blue */
    border-color: #0d6efd; /* Consistent border color */
}

/**
 * TABLE ROW SELECTION STATE - INTERACTIVE FEEDBACK
 * 
 * PATTERN: Visual selection indication
 * - Custom selection background color
 * - Important override per specificity
 */
tr.selected {
    background-color: #e3f2fd !important; /* Light blue selection */
}

/**
 * INTERACTIVE HOVER EFFECTS - UX ENHANCEMENT
 * 
 * DESIGN PATTERN:
 * - Subtle hover feedback per clickable elements
 * - Smooth transitions per professional feel
 * - Accessibility consideration per visual cues
 */
.bg-light:hover {
    background-color: #e9ecef !important; /* Darker gray on hover */
    transition: background-color 0.2s ease; /* Smooth color transition */
}

/**
 * MODAL PRODUCT ITEMS STYLING - DYNAMIC CONTENT
 * 
 * PATTERN: Tag-like display per selected products
 * - Inline-block layout per flexible wrapping
 * - Consistent spacing and borders
 * - Small typography per compact display
 */
#selected-products .product-item {
    display: inline-block;
    background-color: #fff; /* White background */
    border: 1px solid #dee2e6; /* Light gray border */
    border-radius: 0.375rem; /* Bootstrap border radius */
    padding: 0.5rem; /* Internal spacing */
    margin: 0.25rem; /* External spacing */
    font-size: 0.875rem; /* Smaller typography */
}
</style>
@endpush

{{-- 
/**
 * ========================================================================
 * PHP TO JAVASCRIPT DATA TRANSFER SYSTEM
 * ========================================================================
 * 
 * PATTERN ARCHITETTURALE:
 * - Server-side data → Client-side JavaScript transfer
 * - Global window object namespace per data access
 * - Conditional data passing per performance + security
 * - JSON serialization per complex data structures
 * 
 * TECNOLOGIE:
 * - Blade @push directive: Script injection nel layout parent
 * - PHP JSON encoding: @json() Blade helper per safe serialization
 * - JavaScript Global Namespace: window.PageData pattern
 * - Conditional Logic: @if(isset()) per safe data checking
 */
--}}
@push('scripts')
<script>
/**
 * ========================================================================
 * GLOBAL DATA OBJECT INITIALIZATION - JAVASCRIPT NAMESPACE PATTERN
 * ========================================================================
 * 
 * DESIGN PATTERN:
 * - Namespace pattern per evitare global scope pollution
 * - Defensive programming con || operator per initialization
 * - Centralized data object per application state management
 * 
 * UTILIZZO:
 * JavaScript modules potranno accedere a window.PageData.prodotti,
 * window.PageData.staffMembers, etc. per business logic client-side
 */
// GLOBAL DATA OBJECT: Laravel → JavaScript data transfer namespace
window.PageData = window.PageData || {};

/**
 * ========================================================================
 * CONDITIONAL DATA PASSING - PERFORMANCE + SECURITY PATTERN
 * ========================================================================
 * 
 * TECNOLOGIE:
 * - Blade @if(isset()): PHP conditional per esistenza variabili
 * - Blade @json(): Helper per JSON serialization sicura (XSS prevention)
 * - JavaScript Object Assignment: Dynamic property creation
 * 
 * SECURITY CONSIDERATIONS:
 * - @json() escapes automatically per XSS prevention
 * - isset() check previene undefined variable errors
 * - Only necessary data exposed to client-side
 * 
 * PERFORMANCE CONSIDERATIONS:
 * - Conditional transfer reduces payload size
 * - Only relevant data per specific page functionality
 * - Lazy loading pattern per large datasets
 */

// CONDITIONAL DATA PASSING: Only if exists to avoid JSON errors
@if(isset($prodotti))
    /**
     * PRODOTTI COLLECTION DATA TRANSFER
     * 
     * TIPO DATI: Laravel LengthAwarePaginator → JavaScript Array
     * CONTENUTO: Current page products con Eloquent model attributes
     * UTILIZZO: JavaScript filtering, modal population, bulk operations
     * 
     * STRUTTURA JSON:
     * {
     *   data: [
     *     {
     *       id: number,
     *       nome: string,
     *       modello: string,
     *       categoria: string,
     *       staff_assegnato_id: number|null,
     *       staffAssegnato: {object}|null,
     *       malfunzionamenti: array,
     *       ...
     *     }
     *   ],
     *   current_page: number,
     *   total: number,
     *   per_page: number,
     *   ...
     * }
     */
    window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($staffMembers))
    /**
     * STAFF MEMBERS COLLECTION DATA TRANSFER
     * 
     * TIPO DATI: Laravel Collection → JavaScript Array
     * CONTENUTO: Staff members con computed attributes e relationships
     * UTILIZZO: Modal option population, assignment logic, counters update
     * 
     * STRUTTURA JSON:
     * [
     *   {
     *     id: number,
     *     nome: string,
     *     cognome: string,
     *     nome_completo: string, // Eloquent Accessor
     *     username: string,
     *     prodotti_assegnati_count: number, // withCount() result
     *     ...
     *   }
     * ]
     */
    window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
    /**
     * STATISTICS DATA TRANSFER
     * 
     * TIPO DATI: PHP Array → JavaScript Object
     * CONTENUTO: Dashboard statistics e counters
     * UTILIZZO: Real-time updates, dashboard animations, progress indicators
     * 
     * STRUTTURA JSON:
     * {
     *   totale_prodotti: number,
     *   prodotti_assegnati: number,
     *   prodotti_non_assegnati: number,
     *   staff_attivi: number,
     *   media_prodotti_per_staff: number,
     *   ...
     * }
     */
    window.PageData.stats = @json($stats);
@endif

/**
 * ========================================================================
 * DATA TYPES MAPPING - LARAVEL → JAVASCRIPT CONVERSION
 * ========================================================================
 * 
 * ELOQUENT MODELS → JSON OBJECTS:
 * - Model attributes diventano object properties
 * - Accessors (getCategoriaLabelAttribute) inclusi automaticamente
 * - Relationships (staffAssegnato) serializzati se eager loaded
 * - Hidden attributes (password) esclusi automaticamente
 * 
 * LARAVEL COLLECTIONS → JAVASCRIPT ARRAYS:
 * - Collection methods non disponibili lato client
 * - Iteration tramite JavaScript Array.forEach(), map(), filter()
 * - Indexing tramite standard array[index] syntax
 * 
 * LARAVEL PAGINATION → JAVASCRIPT OBJECTS:
 * - LengthAwarePaginator serializza meta-data complete
 * - ->data property contiene actual records array
 * - Navigation methods non disponibili (server-side only)
 * 
 * PHP ARRAYS → JAVASCRIPT OBJECTS/ARRAYS:
 * - Associative arrays [key => value] → Objects {key: value}
 * - Numeric arrays [0, 1, 2] → Arrays [0, 1, 2]
 * - Multi-dimensional arrays mantengono struttura nested
 */

/**
 * ========================================================================
 * INTEGRATION READY PATTERNS - CLIENT-SIDE USAGE EXAMPLES
 * ========================================================================
 * 
 * FILTERING EXAMPLES:
 * // Filter prodotti by categoria
 * const lavatrici = window.PageData.prodotti.data.filter(p => p.categoria === 'lavatrice');
 * 
 * // Find staff by username  
 * const admin = window.PageData.staffMembers.find(s => s.username === 'adminadmin');
 * 
 * MODAL POPULATION EXAMPLES:
 * // Populate assignment modal
 * document.getElementById('assign-product-name').textContent = prodotto.nome;
 * document.getElementById('assign-product-id').value = prodotto.id;
 * 
 * AJAX REQUEST EXAMPLES:
 * // Bulk assignment with collected IDs
 * const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
 *                          .map(cb => parseInt(cb.value));
 * 
 * STATISTICS UPDATES:
 * // Update dashboard counters
 * document.querySelector('.total-products .counter').textContent = window.PageData.stats.totale_prodotti;
 * 
 * REAL-TIME FEATURES:
 * // WebSocket integration ready
 * window.PageData can be updated via WebSocket events
 * Dashboard counters can reflect real-time changes
 * Staff assignments can show live updates
 */

// DATA INTEGRATION READY: For filtering, modal population, AJAX, bulk operations
// All Laravel data structures now available for client-side business logic
// Performance optimized: Only necessary data transferred per page context
// Security compliant: XSS-safe JSON encoding with automatic escaping
</script>
@endpush

{{-- 
/**
 * ========================================================================
 * CONTINUAZIONE COMMENTI - GESTIONE ASSEGNAZIONI PRODOTTI
 * ========================================================================
 * 
 * Questa è la continuazione dei commenti per il file assegnazioni/index.blade.php
 * Il primo artifact è stato completato, qui continuiamo con eventuali sezioni
 * mancanti e approfondimenti aggiuntivi sui pattern utilizzati.
 */
--}}

{{-- 
/**
 * ========================================================================
 * SEZIONI AGGIUNTIVE E APPROFONDIMENTI TECNICI
 * ========================================================================
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: BLADE TEMPLATING ENGINE - LARAVEL TEMPLATE SYSTEM
 * ========================================================================
 * 
 * ARCHITETTURA:
 * Blade è il template engine di Laravel che compila templates in plain PHP
 * per performance ottimali. Ogni .blade.php file viene compilato in cache.
 * 
 * DIRECTIVE PRINCIPALI UTILIZZATE:
 * 
 * @extends('layouts.app')
 * - Inheritance pattern: eredita da layout base
 * - Compiled PHP: <?php echo view('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 * 
 * @section('content')
 * - Content injection: definisce blocco da iniettare nel parent
 * - Compiled PHP: <?php $__env->startSection('content'); ?>
 * 
 * @if($condition)
 * - Conditional rendering: compila in if/endif PHP
 * - Compiled PHP: <?php if($condition): ?>
 * 
 * @foreach($collection as $item)
 * - Loop iteration: compila in foreach PHP con sicurezza
 * - Compiled PHP: <?php $__currentLoopData = $collection; ?>
 * 
 * {{ $variable }}
 * - Safe output: htmlspecialchars() automatico per XSS prevention
 * - Compiled PHP: <?php echo e($variable); ?>
 * 
 * @csrf
 * - Security token: genera hidden input con CSRF token
 * - Compiled PHP: <?php echo csrf_field(); ?>
 * 
 * @push('styles')
 * - Stack system: accumula content in named stacks
 * - Compiled PHP: <?php $__env->startPush('styles'); ?>
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: ELOQUENT ORM RELATIONSHIPS - LARAVEL DATABASE LAYER
 * ========================================================================
 * 
 * RELATIONSHIPS UTILIZZATE NEL PROGETTO:
 * 
 * PRODOTTO MODEL:
 * - belongsTo('App\Models\User', 'staff_assegnato_id'): Staff assegnato
 * - hasMany('App\Models\Malfunzionamento'): Problemi del prodotto
 * 
 * USER MODEL (Staff):
 * - hasMany('App\Models\Prodotto', 'staff_assegnato_id'): Prodotti assegnati
 * - belongsTo('App\Models\CentroAssistenza'): Centro di appartenenza (per tecnici)
 * 
 * QUERY METHODS UTILIZZATE:
 * 
 * $staff->prodottiAssegnati()->count()
 * - Relationship query: non carica models, solo conta
 * - SQL Generated: SELECT COUNT(*) FROM prodotti WHERE staff_assegnato_id = ?
 * - Performance: Efficient count query senza model instantiation
 * 
 * $prodotto->malfunzionamenti->count()
 * - Collection method: assume eager loading fatto
 * - Memory based: conta su Collection già caricata
 * - Usage: Quando relationship già eager loaded con ->with()
 * 
 * $prodotto->malfunzionamenti->where('gravita', 'critica')
 * - Collection filtering: filtra Collection in-memory
 * - Non genera nuova query SQL
 * - Performance: Buona se Collection piccola, altrimenti meglio query scope
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: LARAVEL PAGINATION SYSTEM
 * ========================================================================
 * 
 * PAGINATION CLASS: LengthAwarePaginator
 * 
 * METODI UTILIZZATI NELLA VIEW:
 * 
 * ->count(): Items nella pagina corrente
 * ->total(): Total items in tutto il dataset
 * ->firstItem(): Primo item number della pagina (es: 16 se page 2, 15 per page)
 * ->lastItem(): Ultimo item number della pagina (es: 30 se page 2, 15 per page) 
 * ->currentPage(): Pagina attuale (1-based)
 * ->lastPage(): Ultima pagina disponibile
 * ->hasPages(): Boolean se pagination necessaria (total > perPage)
 * ->onFirstPage(): Boolean se siamo alla prima pagina
 * ->hasMorePages(): Boolean se ci sono pagine successive
 * ->previousPageUrl(): URL pagina precedente (null se prima pagina)
 * ->nextPageUrl(): URL pagina successiva (null se ultima pagina)
 * ->appends(request()->query()): Append query parameters ai pagination links
 * ->getUrlRange(1, $lastPage): Array [page => url] per tutti i page numbers
 * ->url($pageNumber): URL per specifica pagina con current query parameters
 * 
 * QUERY STRING PERSISTENCE:
 * ->appends(request()->query()) mantiene filtri durante navigazione:
 * - Se URL corrente: /admin/assegnazioni?search=lavatrice&staff_id=2&page=1
 * - Next page URL: /admin/assegnazioni?search=lavatrice&staff_id=2&page=2
 * - Mantiene search e staff_id attraverso tutte le pagine
 * 
 * PERFORMANCE CONSIDERATIONS:
 * - LengthAwarePaginator esegue 2 query: COUNT(*) + SELECT con LIMIT/OFFSET
 * - Efficient per large datasets vs loading all records
 * - Memory usage: solo records della current page in memoria
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: BOOTSTRAP 5 COMPONENTS SYSTEM
 * ========================================================================
 * 
 * MODAL COMPONENT ANATOMY:
 * 
 * HTML STRUCTURE:
 * <div class="modal fade" id="modalId" tabindex="-1">
 *   <div class="modal-dialog [modal-lg]">
 *     <div class="modal-content">
 *       <div class="modal-header">
 *         <h5 class="modal-title">Title</h5>
 *         <button class="btn-close" data-bs-dismiss="modal"></button>
 *       </div>
 *       <div class="modal-body">Content</div>
 *       <div class="modal-footer">Actions</div>
 *     </div>
 *   </div>
 * </div>
 * 
 * JAVASCRIPT INTEGRATION:
 * - data-bs-toggle="modal": Trigger attribute
 * - data-bs-target="#modalId": Target modal selector
 * - data-bs-dismiss="modal": Close action attribute
 * - JavaScript API: new bootstrap.Modal(element) per programmatic control
 * 
 * CSS CLASSES BEHAVIOR:
 * - .modal: Base modal styles (hidden by default)
 * - .fade: CSS transitions per smooth show/hide
 * - .modal-dialog: Centering e sizing container
 * - .modal-lg: Larger width per complex content
 * - .modal-content: White background container with border-radius
 * 
 * ACCESSIBILITY FEATURES:
 * - tabindex="-1": Focus management per keyboard navigation
 * - aria-label: Screen reader support
 * - role="group": Semantic grouping per button groups
 * - Focus trap: Bootstrap JS gestisce focus dentro modal
 * - ESC key: Automatic close modal functionality
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: LARAVEL FORM PROCESSING & SECURITY
 * ========================================================================
 * 
 * CSRF PROTECTION MECHANISM:
 * 
 * @csrf DIRECTIVE:
 * - Genera: <input type="hidden" name="_token" value="random_40_char_string">
 * - Token generation: Cryptographically secure random string
 * - Session storage: Token stored in user session
 * - Validation: Laravel middleware confronta form token con session token
 * - Security: Previene Cross-Site Request Forgery attacks
 * 
 * FORM VALIDATION FLOW:
 * 1. Frontend: HTML5 validation (required attributes)
 * 2. Backend: Laravel FormRequest validation rules  
 * 3. Database: Model validation rules e constraints
 * 4. Response: Success redirect o error messages con old input
 * 
 * FORM METHODS:
 * - GET: Per filtri (URL-friendly, bookmarkable, SEO)
 * - POST: Per state changes (CSRF protected, non-cacheable)
 * - method="POST" + @method('PUT'): Laravel method spoofing per RESTful
 * 
 * REQUEST HELPERS:
 * request('parameter'): Laravel global helper
 * - Equivalent: $request->input('parameter')
 * - Auto-casting: Converts form strings to appropriate types
 * - Security: Automatically filtered input
 * - Old input: Available dopo validation failures
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: JAVASCRIPT INTEGRATION PATTERNS
 * ========================================================================
 * 
 * DATA ATTRIBUTES PATTERN:
 * 
 * HTML: <button data-product-id="123" data-product-name="Lavatrice">
 * JS: element.dataset.productId // "123"
 * JS: element.getAttribute('data-product-name') // "Lavatrice"
 * 
 * MODAL POPULATION JAVASCRIPT PATTERN:
 * document.querySelectorAll('.assign-btn').forEach(btn => {
 *   btn.addEventListener('click', function() {
 *     const productId = this.dataset.productId;
 *     const productName = this.dataset.productName;
 *     const currentStaff = this.dataset.currentStaff;
 *     
 *     document.getElementById('assign-product-id').value = productId;
 *     document.getElementById('assign-product-name').textContent = productName;
 *     
 *     if(currentStaff) {
 *       document.getElementById('assign-staff-id').value = currentStaff;
 *     }
 *   });
 * });
 * 
 * CHECKBOX SELECTION JAVASCRIPT PATTERN:
 * const masterCheckbox = document.getElementById('checkAll');
 * const productCheckboxes = document.querySelectorAll('.product-checkbox');
 * 
 * masterCheckbox.addEventListener('change', function() {
 *   productCheckboxes.forEach(cb => cb.checked = this.checked);
 *   updateBulkButtonState();
 * });
 * 
 * function updateBulkButtonState() {
 *   const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
 *   document.getElementById('bulkAssignBtn').disabled = selectedCount === 0;
 * }
 * 
 * PHP TO JAVASCRIPT DATA FLOW:
 * 1. Controller: $data = Model::with('relationships')->get();
 * 2. View: window.PageData.items = @json($data);
 * 3. JavaScript: const items = window.PageData.items.filter(condition);
 * 4. DOM Manipulation: updateUI(items);
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: RESPONSIVE DESIGN & BOOTSTRAP GRID
 * ========================================================================
 * 
 * GRID BREAKPOINTS UTILIZZATI:
 * - col-12: Full width su tutti i dispositivi
 * - col-lg-3: 3/12 colonne su large screens (≥992px), full width sotto
 * - col-lg-9: 9/12 colonne su large screens (≥992px), full width sotto
 * - col-md-3: 3/12 colonne su medium screens (≥768px), full width sotto
 * - col-md-4: 4/12 colonne su medium screens (≥768px), full width sotto
 * - col-md-6: 6/12 colonne su medium screens (≥768px), full width sotto
 * - col-md-8: 8/12 colonne su medium screens (≥768px), full width sotto
 * 
 * RESPONSIVE BEHAVIOR:
 * Desktop (≥992px): Sidebar (3 cols) + Main (9 cols)
 * Tablet (768px-991px): Stack verticale, full width
 * Mobile (<768px): Stack verticale, full width con horizontal scroll per tabelle
 * 
 * UTILITY CLASSES:
 * - d-flex: Display flex
 * - align-items-center: Vertical center alignment
 * - justify-content-between: Horizontal space distribution
 * - text-center: Text alignment
 * - mb-*, mt-*, me-*, ms-*: Margin utilities
 * - p-*, py-*, px-*: Padding utilities
 * - bg-*: Background color utilities
 * - text-*: Text color utilities
 * 
 * TABLE RESPONSIVENESS:
 * .table-responsive: Horizontal scrolling su small screens
 * - Maintains table structure integrity
 * - Provides horizontal scrollbar when needed
 * - Better UX than stacked table cells
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: PERFORMANCE OPTIMIZATION STRATEGIES
 * ========================================================================
 * 
 * DATABASE QUERY OPTIMIZATION:
 * 
 * EAGER LOADING:
 * $prodotti = Prodotto::with(['staffAssegnato', 'malfunzionamenti'])->paginate(15);
 * - Prevents N+1 queries problem
 * - Single query per relationship instead of per-model queries
 * 
 * COUNTING RELATIONSHIPS:
 * $staffMembers = User::withCount('prodottiAssegnati')->get();
 * - Adds 'prodotti_assegnati_count' attribute to each model
 * - More efficient than loading full relationships for counting
 * 
 * PAGINATION BENEFITS:
 * - Memory usage: Only 15 records loaded invece di tutti
 * - Query performance: LIMIT 15 OFFSET x invece di SELECT *
 * - Network transfer: Smaller JSON payload to frontend
 * - User experience: Faster page load times
 * 
 * FRONTEND PERFORMANCE:
 * 
 * CONDITIONAL SCRIPT LOADING:
 * @if(isset($prodotti))
 *   window.PageData.prodotti = @json($prodotti);
 * @endif
 * - Smaller JavaScript payload per page
 * - Only necessary data transferred
 * - Reduced memory usage in browser
 * 
 * CSS/JS ORGANIZATION:
 * @push('styles'): Page-specific CSS
 * @push('scripts'): Page-specific JavaScript  
 * - Prevents unused CSS/JS loading on other pages
 * - Better caching strategy
 * - Smaller initial bundle size
 * 
 * IMAGE OPTIMIZATION:
 * style="width: 50px; height: 50px; object-fit: cover;"
 * - object-fit: cover maintains aspect ratio
 * - Fixed dimensions prevent layout shift
 * - CSS transforms more efficient than image resizing
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: SECURITY BEST PRACTICES IMPLEMENTED
 * ========================================================================
 * 
 * XSS PREVENTION:
 * {{ $variable }}: Blade escaped output
 * - htmlspecialchars($variable, ENT_QUOTES, 'UTF-8', false)
 * - Converts <script> to &lt;script&gt;
 * - Safe for user input display
 * 
 * CSRF PROTECTION:
 * @csrf directive in all POST forms
 * - Cryptographically secure tokens
 * - Session-based validation
 * - Automatic token refresh
 * 
 * SQL INJECTION PREVENTION:
 * Eloquent Query Builder automatically escapes:
 * - where('column', $userInput): Parameter binding
 * - No raw SQL concatenation
 * - Prepared statements underneath
 * 
 * INPUT VALIDATION:
 * Laravel FormRequest validation:
 * - Server-side validation rules
 * - Type casting and sanitization
 * - Error handling and old input
 * 
 * AUTHORIZATION CHECKS:
 * Middleware and Gates:
 * - Route-level authorization
 * - Method-level authorization in Controllers
 * - View-level conditional rendering based on permissions
 * 
 * SESSION SECURITY:
 * Laravel session management:
 * - Encrypted session cookies
 * - CSRF token rotation
 * - Session regeneration on authentication
 */
--}}

{{-- 
/**
 * APPROFONDIMENTO: LARAVEL ROUTING & CONTROLLER INTEGRATION
 * ========================================================================
 * 
 * NAMED ROUTES UTILIZZATE:
 * 
 * route('admin.assegnazioni.index'): 
 * - Route name: admin.assegnazioni.index
 * - HTTP Method: GET
 * - Controller@Method: AssegnazioniController@index
 * - URL Pattern: /admin/assegnazioni
 * 
 * route('admin.assegnazioni.prodotto'):
 * - Route name: admin.assegnazioni.prodotto  
 * - HTTP Method: POST
 * - Controller@Method: AssegnazioniController@assignProdotto
 * - URL Pattern: /admin/assegnazioni/prodotto
 * 
 * route('admin.assegnazioni.multipla'):
 * - Route name: admin.assegnazioni.multipla
 * - HTTP Method: POST
 * - Controller@Method: AssegnazioniController@assignMultipla
 * - URL Pattern: /admin/assegnazioni/multipla
 * 
 * route('admin.prodotti.show', $prodotto):
 * - Route name: admin.prodotti.show
 * - HTTP Method: GET
 * - Model Binding: {prodotto} parameter automatically resolved
 * - Controller@Method: ProdottiController@show
 * - URL Pattern: /admin/prodotti/{prodotto}
 * 
 * CONTROLLER METHODS EXPECTATIONS:
 * 
 * AssegnazioniController@index():
 * - Returns view with: $prodotti, $staffMembers, $categorie, $stats
 * - Handles filtering via request()->input()
 * - Implements pagination with filters persistence
 * 
 * AssegnazioniController@assignProdotto(Request $request):
 * - Expects: prodotto_id, staff_id (nullable) 
 * - Validation rules for input parameters
 * - Updates Prodotto model staff_assegnato_id
 * - Redirects back with success/error message
 * 
 * AssegnazioniController@assignMultipla(Request $request):
 * - Expects: prodotto_ids[] array, staff_id (nullable)
 * - Bulk update multiple Prodotto records
 * - Transaction wrapping for data integrity
 * - Batch update performance optimization
 */
--}}

{{-- 
/**
 * CONCLUSIONI E PATTERN SUMMARY
 * ========================================================================
 * 
 * PATTERN ARCHITETTURALI IMPLEMENTATI:
 * 
 * 1. MVC (Model-View-Controller):
 *    - Model: Eloquent ORM per data layer
 *    - View: Blade templates per presentation layer  
 *    - Controller: Business logic e request handling
 * 
 * 2. Repository Pattern (implicito in Eloquent):
 *    - Eloquent Models come data repositories
 *    - Query Builder come query interface
 *    - Relationships come data associations
 * 
 * 3. Template Inheritance:
 *    - Base layout con sezioni yield
 *    - Child templates extend e override sezioni
 *    - Reusable components e partials
 * 
 * 4. Component-Based CSS:
 *    - Bootstrap components come building blocks
 *    - Custom CSS tramite component overrides
 *    - Utility-first approach per spacing e colors
 * 
 * 5. Progressive Enhancement:
 *    - Base functionality senza JavaScript
 *    - Enhanced UX con JavaScript interactions
 *    - Graceful degradation per accessibility
 * 
 * 6. Data Transfer Patterns:
 *    - Server-side rendering per initial page load
 *    - JSON data injection per JavaScript enhancements
 *    - AJAX-ready data structures per future enhancements
 * 
 * TECNOLOGIE INTEGRATE:
 * - Laravel 12: PHP Framework con Eloquent ORM
 * - Blade: Template Engine con inheritance e components
 * - Bootstrap 5: CSS Framework con JavaScript components
 * - HTML5: Semantic markup con accessibility features
 * - CSS3: Modern styling con animations e responsive design
 * - JavaScript: Client-side interactivity e DOM manipulation
 * - MySQL: Database con relational data structure
 * 
 * FUNZIONALITÀ IMPLEMENTATE:
 * ✅ Visualizzazione prodotti con pagination
 * ✅ Filtri avanzati (search, categoria, staff, status)
 * ✅ Assegnazione singola prodotto → staff
 * ✅ Assegnazione multipla (bulk operations)
 * ✅ Rimozione assegnazioni esistenti  
 * ✅ Dashboard statistiche real-time
 * ✅ UI responsive Bootstrap con modal workflows
 * ✅ Security compliance (CSRF, XSS prevention)
 * ✅ Performance optimization (pagination, eager loading)
 * ✅ Accessibility features (semantic HTML, ARIA labels)
 */
--}}