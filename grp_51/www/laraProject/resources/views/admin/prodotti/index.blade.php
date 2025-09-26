{{-- 
    VISTA GESTIONE PRODOTTI ADMIN CON STILE CATALOGO TECNICO
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    SCOPO: Vista per amministratori che permette la gestione completa dei prodotti
           con design identico al catalogo ma con funzionalità amministrative
    
    FUNZIONALITÀ PRINCIPALI:
    - Visualizzazione catalogo prodotti con stile tecnico professionale
    - Controlli amministrativi (modifica, elimina, attiva/disattiva)
    - Ricerca avanzata e filtri multipli 
    - Selezione multipla per azioni bulk
    - Assegnazione staff per ogni prodotto
    - Statistiche in tempo reale
    - Gestione malfunzionamenti per prodotto
--}}

{{-- EXTENDS: Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- SECTION TITLE: Imposta il titolo della pagina nel browser --}}
@section('title', 'Gestione Prodotti')

{{-- SECTION CONTENT: Inizio del contenuto principale della vista --}}
@section('content')

{{-- CONTAINER: Contenitore fluido responsive con padding laterale --}}
<div class="container-fluid px-3 px-lg-4">
    
    {{-- ========== HEADER PRINCIPALE CON STATISTICHE ========== --}}
    {{-- Header con design identico al catalogo per mantenere coerenza visiva --}}
    <div class="row mb-3">
        <div class="col-12">
            {{-- Card header con gradiente blu e testo bianco --}}
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        
                        {{-- COLONNA SINISTRA: Titolo e descrizione --}}
                        <div class="col-lg-8 col-md-7">
                            {{-- Titolo principale con icona gear per indicare funzioni admin --}}
                            <h2 class="mb-1 fw-bold">
                                <i class="bi bi-gear-fill me-2"></i>
                                Gestione Prodotti Admin
                            </h2>
                            
                            {{-- Sottotitolo con badge rosso per evidenziare livello amministrativo --}}
                            <p class="mb-0 opacity-90">
                                <span class="badge bg-danger text-white me-2">Amministrazione Completa</span>
                                Controllo totale del catalogo prodotti
                            </p>
                        </div>
                        
                        {{-- COLONNA DESTRA: Statistiche amministrative nell'header --}}
                        <div class="col-lg-4 col-md-5 mt-2 mt-md-0">
                            {{-- 
                                CONDIZIONE: Mostra statistiche solo se sono state passate dal controller
                                Le statistiche includono: totale prodotti, prodotti con malfunzionamenti, ecc.
                            --}}
                            @if(isset($stats))
                                <div class="row g-2">
                                    {{-- Statistica: Numero totale prodotti --}}
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            {{-- 
                                                OPERATORE NULL COALESCING (??): Se $stats['total_prodotti'] 
                                                è null o non esiste, mostra 0 
                                            --}}
                                            <div class="h5 fw-bold mb-0">{{ $stats['total_prodotti'] ?? 0 }}</div>
                                            <small class="opacity-90">Totali</small>
                                        </div>
                                    </div>
                                    
                                    {{-- Statistica: Prodotti con malfunzionamenti --}}
                                    <div class="col-6">
                                        <div class="text-center bg-white bg-opacity-10 rounded p-2">
                                            <div class="h5 fw-bold mb-0">{{ $stats['con_malfunzionamenti'] ?? 0 }}</div>
                                            <small class="opacity-90">Problemi</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== PULSANTI AZIONE FLOTTANTI ========== --}}
    {{-- 
        Pulsanti posizionati fissi in basso a destra della finestra
        z-index: 1050 per stare sopra tutti gli altri elementi
    --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div class="d-flex flex-column gap-2">
            
            {{-- PULSANTE: Aggiungi nuovo prodotto --}}
            {{-- 
                ROUTE: Usa named route per generare URL verso admin.prodotti.create
                STILE: Pulsante circolare verde con icona plus
                TOOLTIP: Bootstrap tooltip per mostrare descrizione al hover
            --}}
            <a href="{{ route('admin.prodotti.create') }}" 
               class="btn btn-success rounded-circle shadow" 
               style="width: 50px; height: 50px;"
               data-bs-toggle="tooltip" 
               title="Aggiungi Nuovo Prodotto">
                <i class="bi bi-plus" style="font-size: 1.25rem;"></i>
            </a>
            
            {{-- PULSANTE: Azioni multiple (inizialmente nascosto) --}}
            {{-- 
                JAVASCRIPT: Questo pulsante viene mostrato/nascosto da JavaScript
                quando l'utente seleziona uno o più prodotti tramite checkbox
            --}}
            <button class="btn btn-warning rounded-circle shadow d-none" 
                    id="bulkActionsBtn"
                    style="width: 50px; height: 50px;"
                    data-bs-toggle="dropdown"
                    title="Azioni Multiple">
                <i class="bi bi-gear" style="font-size: 1.25rem;"></i>
            </button>
            
            {{-- MENU DROPDOWN: Azioni per selezione multipla --}}
            {{-- 
                BOOTSTRAP DROPDOWN: Menu a tendina collegato al pulsante precedente
                Contiene azioni che si applicano a tutti i prodotti selezionati
            --}}
            <ul class="dropdown-menu" aria-labelledby="bulkActionsBtn">
                {{-- Azione: Seleziona tutti i prodotti --}}
                <li>
                    {{-- 
                        JAVASCRIPT FUNCTION: onclick chiama selectAllProducts() 
                        definita nello script in fondo al file
                    --}}
                    <button class="dropdown-item" type="button" onclick="selectAllProducts()">
                        <i class="bi bi-check-all me-2"></i>Seleziona Tutti
                    </button>
                </li>
                
                {{-- Azione: Deseleziona tutti i prodotti --}}
                <li>
                    <button class="dropdown-item" type="button" onclick="deselectAllProducts()">
                        <i class="bi bi-x-square me-2"></i>Deseleziona Tutti
                    </button>
                </li>
                
                {{-- Separatore visuale --}}
                <li><hr class="dropdown-divider"></li>
                
                {{-- Azione: Attiva prodotti selezionati --}}
                <li>
                    {{-- CLASSE BOOTSTRAP: text-success per colore verde --}}
                    <button class="dropdown-item text-success" type="button" onclick="bulkActivateProducts()">
                        <i class="bi bi-check-circle me-2"></i>Attiva Selezionati
                    </button>
                </li>
                
                {{-- Azione: Disattiva prodotti selezionati --}}
                <li>
                    <button class="dropdown-item text-warning" type="button" onclick="bulkDeactivateProducts()">
                        <i class="bi bi-x-circle me-2"></i>Disattiva Selezionati
                    </button>
                </li>
                
                {{-- Azione: Elimina prodotti selezionati --}}
                <li>
                    {{-- CLASSE BOOTSTRAP: text-danger per colore rosso --}}
                    <button class="dropdown-item text-danger" type="button" onclick="bulkDeleteProducts()">
                        <i class="bi bi-trash me-2"></i>Elimina Selezionati
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- ========== FORM DI RICERCA E FILTRI ========== --}}
    {{-- Form con design identico al catalogo per coerenza visiva --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    {{-- 
                        FORM HTTP GET: Usa metodo GET per permettere bookmark dei filtri
                        ACTION: Invia a admin.prodotti.index (stessa pagina)
                        ID: Per riferimento JavaScript
                    --}}
                    <form method="GET" action="{{ route('admin.prodotti.index') }}" id="filterForm" class="row g-3">

                        {{-- CAMPO RICERCA: Ricerca avanzata identica al catalogo --}}
                        <div class="col-lg-4 col-md-6">
                            {{-- Label con stile primario e icona --}}
                            <label for="search" class="form-label fw-semibold text-primary">
                                <i class="bi bi-search me-1"></i>Ricerca Prodotti
                            </label>
                            
                            {{-- Input group con campo testo e pulsante clear --}}
                            <div class="input-group">
                                {{-- 
                                    CAMPO TESTO: 
                                    - name="search" per parametro GET
                                    - value="{{ request('search') }}" mantiene valore dopo submit
                                    - autocomplete="off" disabilita autocompletamento browser
                                --}}
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Nome, modello, descrizione..."
                                       autocomplete="off">
                                       
                                {{-- Pulsante per cancellare la ricerca --}}
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Pulisci ricerca">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            
                            {{-- Testo di aiuto per spiegare la sintassi wildcard --}}
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Supporta ricerche parziali con <code>*</code>
                            </div>
                        </div>

                        {{-- FILTRO STATO: Dropdown per filtrare per stato attivo/inattivo --}}
                        <div class="col-lg-2 col-md-3">
                            <label for="status" class="form-label fw-semibold text-primary">
                                <i class="bi bi-funnel me-1"></i>Stato
                            </label>
                            
                            {{-- 
                                SELECT DROPDOWN:
                                - name="status" per parametro GET
                                - Opzioni con icone emoji per migliore UX
                            --}}
                            <select name="status" id="status" class="form-select">
                                <option value="">Tutti</option>
                                {{-- 
                                    CONDIZIONE TERNARIA: 
                                    {{ request('status') == 'attivi' ? 'selected' : '' }}
                                    Se il parametro GET status è uguale a 'attivi', imposta selected
                                --}}
                                <option value="attivi" {{ request('status') == 'attivi' ? 'selected' : '' }}>
                                    ✅ Attivi
                                </option>
                                <option value="inattivi" {{ request('status') == 'inattivi' ? 'selected' : '' }}>
                                    ❌ Disattivati
                                </option>
                            </select>
                        </div>
                        {{-- FILTRO STAFF: Dropdown per filtrare per staff assegnato --}}
                        <div class="col-lg-3 col-md-3">
                            <label for="staff_id" class="form-label fw-semibold text-primary">
                                <i class="bi bi-person-gear me-1"></i>Staff Assegnato
                            </label>
                            
                            {{-- 
                                SELECT STAFF:
                                - Permette di filtrare prodotti per membro staff assegnato
                                - Include opzione speciale "Non Assegnati" con valore "0"
                            --}}
                            <select name="staff_id" id="staff_id" class="form-select">
                                <option value="">Tutti</option>
                                
                                {{-- 
                                    OPZIONE SPECIALE: Prodotti non assegnati a nessuno staff
                                    CONFRONTO STRICT: request('staff_id') === '0' usa === per confronto esatto
                                    perché i parametri GET sono sempre stringhe
                                --}}
                                <option value="0" {{ request('staff_id') === '0' ? 'selected' : '' }}>
                                    🚫 Non Assegnati
                                </option>
                                
                                {{-- 
                                    LOOP FOREACH: Itera attraverso tutti i membri dello staff
                                    $staffMembers è una collection passata dal controller
                                --}}
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        👤 {{ $staff->nome }} {{ $staff->cognome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PULSANTI AZIONE: Submit e Reset --}}
                        <div class="col-lg-3 col-md-12">
                            {{-- Label nascosta su schermi grandi per allineamento --}}
                            <label class="form-label d-none d-lg-block">&nbsp;</label>
                            
                            <div class="d-flex gap-2">
                                {{-- Pulsante per eseguire la ricerca --}}
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search me-1"></i>Cerca
                                </button>
                                
                                {{-- 
                                    LINK RESET: Ricarica la pagina senza parametri per azzerare filtri
                                    ROUTE: Usa named route senza parametri
                                --}}
                                <a href="{{ route('admin.prodotti.index') }}" 
                                   class="btn btn-outline-secondary" 
                                   title="Reset filtri">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FILTRI RAPIDI AMMINISTRATIVI ========== --}}
    {{-- Riga di badge/link per filtri predefiniti --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                {{-- Etichetta per identificare la sezione --}}
                <span class="badge bg-secondary py-2 px-3">
                    <i class="bi bi-funnel me-1"></i>Filtri Admin:
                </span>
                
                {{-- 
                    FILTRO: Tutti i prodotti (nessun filtro)
                    CONDIZIONE: Controlla se NON ci sono parametri di ricerca attivi
                    hasAny(['search', 'status', 'staff_id']): true se almeno uno dei parametri esiste
                    OPERATORE !: Nega il risultato
                --}}
                <a href="{{ route('admin.prodotti.index') }}" 
                   class="badge {{ !request()->hasAny(['search', 'status', 'staff_id']) ? 'bg-primary' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Tutti i Prodotti
                </a>
                
                {{-- FILTRO RAPIDO: Solo prodotti attivi --}}
                <a href="{{ route('admin.prodotti.index') }}?status=attivi" 
                   class="badge {{ request('status') === 'attivi' ? 'bg-success' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Solo Attivi
                </a>
                
                {{-- FILTRO RAPIDO: Prodotti non assegnati --}}
                <a href="{{ route('admin.prodotti.index') }}?staff_id=0" 
                   class="badge {{ request('staff_id') === '0' ? 'bg-warning' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Non Assegnati
                </a>
                
                {{-- FILTRO RAPIDO: Prodotti disattivati --}}
                <a href="{{ route('admin.prodotti.index') }}?status=inattivi" 
                   class="badge {{ request('status') === 'inattivi' ? 'bg-danger' : 'bg-light text-dark border' }} py-2 px-3 text-decoration-none">
                    Disattivati
                </a>
            </div>
        </div>
    </div>

    {{-- ========== STATISTICHE DETTAGLIATE ========== --}}
    {{-- Mostra statistiche solo se passate dal controller --}}
    @if(isset($stats))
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    {{-- Statistica: Totale prodotti --}}
                    <span class="badge bg-primary">{{ $stats['total_prodotti'] }} prodotti totali</span>
                    
                    {{-- Statistica: Prodotti attivi --}}
                    <span class="badge bg-success">{{ $stats['attivi'] ?? 0 }} attivi</span>
                    
                    {{-- Statistica: Prodotti inattivi --}}
                    <span class="badge bg-warning">{{ $stats['inattivi'] ?? 0 }} disattivati</span>
                    
                    {{-- Statistica: Prodotti con malfunzionamenti --}}
                    <span class="badge bg-danger">{{ $stats['con_malfunzionamenti'] ?? 0 }} con problemi</span>
                    
                    {{-- ========== BADGE PER FILTRI ATTIVI ========== --}}
                    {{-- Badge che mostrano quali filtri sono attualmente applicati --}}
                    
                    {{-- FILTRO ATTIVO: Ricerca testuale --}}
                    @if(request('search'))
                        <span class="badge bg-info">Ricerca: "{{ request('search') }}"</span>
                    @endif
                    
                    {{-- FILTRO ATTIVO: Stato prodotto --}}
                    @if(request('status'))
                        {{-- FUNZIONE ucfirst(): Rende maiuscola la prima lettera --}}
                        <span class="badge bg-secondary">Stato: {{ ucfirst(request('status')) }}</span>
                    @endif
                    
                    {{-- FILTRO ATTIVO: Staff assegnato --}}
                    @if(request('staff_id') === '0')
                        {{-- Caso speciale: Non assegnati --}}
                        <span class="badge bg-warning">Solo Non Assegnati</span>
                    @elseif(request('staff_id'))
                        {{-- 
                            METODO ELOQUENT find(): Trova il record per ID
                            PROPRIETÀ ACCESSOR: ->nome_completo è probabilmente un accessor nel model
                            OPERATORE ??: Se find() restituisce null, mostra 'N/A'
                        --}}
                        <span class="badge bg-info">Staff: {{ $staffMembers->find(request('staff_id'))->nome_completo ?? 'N/A' }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ========== MESSAGGIO RISULTATI RICERCA ========== --}}
    {{-- Identico al catalogo per coerenza UX --}}
    {{-- 
        CONDIZIONE: Mostra solo se ci sono filtri attivi
        hasAny(): Controlla se almeno uno dei parametri specificati esiste
    --}}
    @if(request()->hasAny(['search', 'status', 'staff_id']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm py-2">
                    <div class="row align-items-center">
                        
                        {{-- COLONNA SINISTRA: Messaggio risultati --}}
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Risultati filtrati:</strong>
                                    {{-- 
                                        METODO PAGINATION: ->total() restituisce il numero totale 
                                        di record (non solo quelli nella pagina corrente)
                                    --}}
                                    Trovati <span class="badge bg-primary">{{ $prodotti->total() }}</span> prodotti
                                    
                                    {{-- Mostra dettagli del filtro applicato --}}
                                    @if(request('search'))
                                        per "<em class="text-primary">{{ request('search') }}</em>"
                                    @endif
                                    @if(request('status'))
                                        con stato "<em class="text-primary">{{ request('status') }}</em>"
                                    @endif
                                    @if(request('staff_id'))
                                        {{-- OPERATORE TERNARIO per testo diverso basato sul valore --}}
                                        {{ request('staff_id') === '0' ? 'non assegnati' : 'per staff selezionato' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- COLONNA DESTRA: Pulsante reset filtri --}}
                        <div class="col-lg-4 text-end mt-2 mt-lg-0">
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Rimuovi filtri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- ========== GRIGLIA PRODOTTI CON FUNZIONALITÀ ADMIN ========== --}}
    {{-- 
        LAYOUT RESPONSIVE: 
        - xl: 4 colonne (3 prodotti per riga)
        - lg: 3 colonne (4 prodotti per riga) 
        - md: 2 colonne (6 prodotti per riga)
        - sm: 2 colonne (6 prodotti per riga)
        - xs: 1 colonna (12 prodotti per riga)
    --}}
    <div class="row g-3 mb-4" id="prodotti-admin-grid">
        
        {{-- 
            LOOP FORELSE: Itera sui prodotti o mostra contenuto alternativo se vuoto
            $prodotti è una LengthAwarePaginator passata dal controller
        --}}
        @forelse($prodotti as $prodotto)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                
                {{-- ========== CARD PRODOTTO CON DESIGN CATALOGO + CONTROLLI ADMIN ========== --}}
                {{-- 
                    CLASSI MULTIPLE:
                    - product-card: Classe CSS custom per animazioni
                    - admin-card: Classe CSS per stili specifici admin
                    - h-100: Altezza 100% per card uniformi
                    - shadow-sm: Ombra sottile Bootstrap
                    - border-0: Rimuove bordo default Bootstrap
                --}}
                <div class="card h-100 shadow-sm border-0 product-card admin-card
                    {{-- 
                        LOGICA BORDI COLORATI BASATA SU STATO PRODOTTO:
                        Utilizza metodi definiti nel Model Prodotto
                    --}}
                    @if($prodotto->hasMalfunzionamentiCritici())
                        border-danger-subtle
                    @elseif($prodotto->malfunzionamenti_count > 0)
                        border-warning-subtle
                    @elseif($prodotto->attivo)
                        border-success-subtle
                    @else
                        border-secondary-subtle
                    @endif
                ">
                    
                    {{-- ========== CHECKBOX SELEZIONE MULTIPLA (SOLO ADMIN) ========== --}}
                    {{-- 
                        POSIZIONAMENTO ASSOLUTO: top-0 start-0 per angolo superiore sinistro
                        z-index: 10 per stare sopra l'immagine
                    --}}
                    <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                        {{-- 
                            CHECKBOX:
                            - product-checkbox: Classe CSS per stile personalizzato
                            - value="{{ $prodotto->id }}": ID prodotto per JavaScript
                            - transform: scale(1.2): Ingrandisce del 20% per migliore UX
                        --}}
                        <input type="checkbox" 
                               class="form-check-input product-checkbox shadow" 
                               value="{{ $prodotto->id }}"
                               style="transform: scale(1.2);">
                    </div>
                    
                    {{-- ========== AREA IMMAGINE CON BADGE E INDICATORI ========== --}}
                    <div class="position-relative overflow-hidden">
                        
                        {{-- IMMAGINE PRODOTTO O PLACEHOLDER --}}
                        @if($prodotto->foto)
                            {{-- 
                                IMMAGINE REALE:
                                - asset('storage/' . $prodotto->foto): Genera URL per file nel storage
                                - object-fit: contain: Mantiene proporzioni senza tagliare
                                - object-position: center: Centra l'immagine
                                - background-color: #f8f9fa: Sfondo grigio chiaro
                            --}}
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $prodotto->nome }}"
                                 style="height: 160px;
    object-fit: contain !important; /* ← Mostra immagine completa */
    object-position: center center;
    background-color: #f8f9fa;">
                        @else
                            {{-- 
                                PLACEHOLDER: Quando non c'è immagine
                                - d-flex align-items-center justify-content-center: Centra icona
                                - bg-light: Sfondo grigio chiaro Bootstrap
                            --}}
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 160px;">
                                <i class="bi bi-box text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif
                        
                        {{-- BADGE CATEGORIA (angolo superiore destro) --}}
                        <div class="position-absolute top-0 end-0 m-2">
                            {{-- 
                                BADGE CATEGORIA:
                                - bg-opacity-90: Trasparenza per non coprire completamente l'immagine
                                - categoria_label: Accessor nel model per nome user-friendly
                                - ucfirst(): Capitalizza prima lettera se non c'è label
                            --}}
                            <span class="badge bg-secondary bg-opacity-90 px-2 py-1 mb-1 d-block">
                                <i class="bi bi-tag me-1"></i>{{ $prodotto->categoria_label ?? ucfirst($prodotto->categoria) }}
                            </span>
                        </div>

                        {{-- ========== INDICATORI STATO ADMIN (overlay in basso) ========== --}}
                        {{-- 
                            OVERLAY: Barra scura in basso all'immagine con informazioni admin
                            bg-dark bg-opacity-75: Sfondo scuro semi-trasparente
                            --}}
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75">
                            <div class="d-flex justify-content-between align-items-center p-2">
                                
                                {{-- INDICATORE STATO ATTIVO/INATTIVO --}}
                                <div>
                                    {{-- 
                                        CONDIZIONE: Mostra badge verde se attivo, rosso se inattivo
                                        PROPRIETÀ BOOLEAN: $prodotto->attivo è un campo boolean nel database
                                    --}}
                                    @if($prodotto->attivo)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Attivo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Inattivo
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- CONTATORI PROBLEMI/MALFUNZIONAMENTI --}}
                                <div class="d-flex gap-1">
                                    {{-- 
                                        CONTATORE TOTALE MALFUNZIONAMENTI:
                                        malfunzionamenti_count è aggiunto dal controller con withCount()
                                    --}}
                                    @if($prodotto->malfunzionamenti_count > 0)
                                        <span class="badge bg-warning" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $prodotto->malfunzionamenti_count }} problemi totali">
                                            {{ $prodotto->malfunzionamenti_count }}
                                        </span>
                                    @endif
                                    
                                    {{-- 
                                        CONTATORE MALFUNZIONAMENTI CRITICI:
                                        critici_count potrebbe essere aggiunto con query aggiuntiva
                                        isset() controlla se la proprietà esiste
                                    --}}
                                    @if(isset($prodotto->critici_count) && $prodotto->critici_count > 0)
                                        <span class="badge bg-danger" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $prodotto->critici_count }} critici">
                                            {{ $prodotto->critici_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========== CORPO DELLA CARD CON INFORMAZIONI PRODOTTO ========== --}}
                    <div class="card-body d-flex flex-column p-3">
                        
                        {{-- TITOLO PRODOTTO CON COLORE BASATO SU STATO --}}
                        {{-- 
                            LOGICA COLORI:
                            - Grigio se inattivo
                            - Rosso se ha malfunzionamenti critici  
                            - Arancione se ha malfunzionamenti normali
                            - Blu se tutto ok
                        --}}
                        <h6 class="card-title mb-2 fw-bold
                            @if(!$prodotto->attivo)
                                text-muted
                            @elseif($prodotto->hasMalfunzionamentiCritici())
                                text-danger
                            @elseif($prodotto->malfunzionamenti_count > 0)
                                text-warning
                            @else
                                text-primary
                            @endif
                        ">
                            {{ $prodotto->nome }}
                        </h6>
                        
                        {{-- INFORMAZIONI MODELLO E PREZZO --}}
                        <div class="row g-1 mb-2 small">
                            {{-- MODELLO (se presente) --}}
                            @if($prodotto->modello)
                                <div class="col-12">
                                    <span class="text-muted">
                                        <i class="bi bi-gear me-1"></i>{{ $prodotto->modello }}
                                    </span>
                                </div>
                            @endif
                            
                            {{-- PREZZO (se presente) --}}
                            @if($prodotto->prezzo)
                                <div class="col-12">
                                    <span class="text-success fw-bold">
                                        {{-- 
                                            FUNZIONE number_format():
                                            - 2 decimali
                                            - ',' come separatore decimali (stile europeo)
                                            - '.' come separatore migliaia
                                        --}}
                                        <i class="bi bi-tag me-1"></i>€ {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- DESCRIZIONE PRODOTTO TRONCATA --}}
                        {{-- 
                            HELPER Str::limit():
                            - Tronca la stringa a 80 caratteri
                            - Aggiunge '...' alla fine se troncata
                            - flex-grow-1: Occupa spazio disponibile per allineamento uniforme
                        --}}
                        <p class="card-text flex-grow-1 text-muted small">
                            {{ Str::limit($prodotto->descrizione, 80, '...') }}
                        </p>

                        {{-- ========== INFORMAZIONI AMMINISTRATIVE ========== --}}
                        {{-- Statistiche in formato 2 colonne --}}
                        <div class="row g-1 mb-2 small">
                            
                            {{-- COLONNA 1: Numero problemi --}}
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    {{-- 
                                        LOGICA COLORE:
                                        - Arancione se ci sono problemi
                                        - Verde se nessun problema
                                        - OPERATORE ??: Se malfunzionamenti_count è null, mostra 0
                                    --}}
                                    <strong class="text-{{ $prodotto->malfunzionamenti_count > 0 ? 'warning' : 'success' }}">
                                        {{ $prodotto->malfunzionamenti_count ?? 0 }}
                                    </strong>
                                    <br><small class="text-muted">Problemi</small>
                                </div>
                            </div>
                            
                            {{-- COLONNA 2: Data creazione --}}
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    {{-- 
                                        CARBON DATE FORMATTING:
                                        created_at è un'istanza Carbon (estensione DateTime di PHP)
                                        format('d/m/Y'): Formato data italiano
                                    --}}
                                    <strong class="text-muted">
                                        {{ $prodotto->created_at->format('d/m/Y') }}
                                    </strong>
                                    <br><small class="text-muted">Creato</small>
                                </div>
                            </div>
                        </div>

                        {{-- ========== STAFF ASSEGNATO ========== --}}
                        {{-- 
                            RELAZIONE ELOQUENT:
                            staffAssegnato è probabilmente una relazione belongsTo nel Model Prodotto
                        --}}
                        @if($prodotto->staffAssegnato)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person-badge me-1"></i>
                                {{-- 
                                    ACCESSOR: nome_completo è probabilmente un accessor nel Model User
                                    che concatena nome e cognome
                                --}}
                                Staff: {{ $prodotto->staffAssegnato->nome_completo }}
                            </p>
                        @else
                            {{-- Messaggio di warning se nessun staff assegnato --}}
                            <p class="text-warning small mb-2">
                                <i class="bi bi-person-x me-1"></i>
                                Nessun staff assegnato
                            </p>
                        @endif

                        {{-- ========== PULSANTI AZIONE AMMINISTRATIVA ========== --}}
                        {{-- d-grid gap-1: Layout a griglia con gap di 1 unità --}}
                        <div class="d-grid gap-1">
                            
                            {{-- PULSANTE: Visualizza dettagli prodotto --}}
                            {{-- 
                                NAMED ROUTE: admin.prodotti.show con parametro $prodotto
                                Laravel usa automaticamente la proprietà 'id' del model
                            --}}
                            <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Visualizza
                            </a>
                            
                            {{-- ========== DROPDOWN AZIONI AVANZATE ========== --}}
                            <div class="dropdown">
                                {{-- 
                                    PULSANTE DROPDOWN:
                                    - w-100: Larghezza 100% per allineamento con pulsante sopra
                                    - data-bs-toggle="dropdown": Attributo Bootstrap per dropdown
                                --}}
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-1"></i>Azioni
                                </button>
                                
                                {{-- MENU DROPDOWN --}}
                                <ul class="dropdown-menu w-100">
                                    
                                    {{-- AZIONE: Modifica prodotto --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.prodotti.edit', $prodotto) }}">
                                            <i class="bi bi-pencil me-2"></i>Modifica
                                        </a>
                                    </li>
                                    
                                    {{-- AZIONE CONDIZIONALE: Gestisci malfunzionamenti (solo se presenti) --}}
                                    @if($prodotto->malfunzionamenti_count > 0)
                                        <li>
                                            {{-- 
                                                ROUTE CON PARAMETRO: malfunzionamenti.index accetta $prodotto
                                                Mostra il numero di malfunzionamenti nel testo
                                            --}}
                                            <a class="dropdown-item" href="{{ route('malfunzionamenti.index', $prodotto) }}">
                                                <i class="bi bi-tools me-2"></i>Malfunzionamenti ({{ $prodotto->malfunzionamenti_count }})
                                            </a>
                                        </li>
                                    @endif
                                    
                                    {{-- Separatore visuale --}}
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    {{-- ========== TOGGLE STATO ATTIVO/INATTIVO ========== --}}
                                    {{-- 
                                        CONDIZIONE ROUTE: Controlla se la route esiste prima di usarla
                                        Route::has(): Metodo Laravel per verificare esistenza route
                                    --}}
                                    @if(Route::has('admin.prodotti.toggle-status'))
                                        <li>
                                            {{-- 
                                                FORM POST: Usa metodo POST per cambiare stato
                                                onsubmit: Chiama JavaScript per conferma prima dell'invio
                                            --}}
                                            <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirmToggleStatus({{ $prodotto->attivo ? 'true' : 'false' }})">
                                                @csrf
                                                {{-- 
                                                    PULSANTE DINAMICO:
                                                    - Colore e testo cambiano in base allo stato attuale
                                                    - Se attivo: rosso + "Disattiva"
                                                    - Se inattivo: verde + "Attiva"
                                                --}}
                                                <button type="submit" 
                                                        class="dropdown-item {{ $prodotto->attivo ? 'text-danger' : 'text-success' }}">
                                                    <i class="bi bi-{{ $prodotto->attivo ? 'pause' : 'play' }} me-2"></i>
                                                    {{ $prodotto->attivo ? 'Disattiva' : 'Attiva' }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    
                                    {{-- ========== ELIMINAZIONE PRODOTTO ========== --}}
                                    <li>
                                        {{-- 
                                            FORM DELETE: Usa method spoofing di Laravel
                                            onsubmit: JavaScript per conferma eliminazione
                                            d-inline: Display inline per stare nel dropdown
                                        --}}
                                        <form action="{{ route('admin.prodotti.destroy', $prodotto) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Sei sicuro di voler eliminare il prodotto \"{{ $prodotto->nome }}\"?\n\nQuesta azione non può essere annullata.')">
                                            @csrf
                                            {{-- 
                                                METHOD SPOOFING: Laravel usa @method('DELETE') per simulare 
                                                richiesta HTTP DELETE attraverso POST
                                            --}}
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Elimina
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        {{-- ========== STATO VUOTO (EMPTY CASE) ========== --}}
        {{-- 
            FORELSE @empty: Eseguito quando $prodotti è vuoto
            Mostra messaggio user-friendly con azioni suggerite
        --}}
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    {{-- Icona grande per attirare attenzione --}}
                    <i class="bi bi-search fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun prodotto trovato</h4>
                    
                    {{-- MESSAGGIO DINAMICO basato su presenza filtri --}}
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'staff_id']))
                            Prova a modificare i filtri di ricerca
                        @else
                            Non ci sono ancora prodotti nel catalogo
                        @endif
                    </p>
                    
                    {{-- AZIONI SUGGERITE --}}
                    <div class="mt-3">
                        {{-- Se ci sono filtri attivi, mostra pulsante reset --}}
                        @if(request()->hasAny(['search', 'status', 'staff_id']))
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filtri
                            </a>
                        @endif
                        
                        {{-- Pulsante per aggiungere primo prodotto --}}
                        <a href="{{ route('admin.prodotti.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi Primo Prodotto
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    {{-- ========== PAGINAZIONE IDENTICA AL CATALOGO ========== --}}
    {{-- 
        CONDIZIONE: Mostra paginazione solo se ci sono multiple pagine
        hasPages(): Metodo Laravel per verificare se servono controlli di paginazione
    --}}
    @if($prodotti->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                {{-- INFORMAZIONI PAGINAZIONE: Mostra "X-Y di Z risultati" --}}
                <div class="text-center mb-2">
                    <small class="text-muted">
                        {{-- 
                            METODI LARAVEL PAGINATION:
                            - firstItem(): Primo elemento della pagina corrente
                            - lastItem(): Ultimo elemento della pagina corrente  
                            - total(): Totale elementi in tutte le pagine
                        --}}
                        Visualizzati {{ $prodotti->firstItem() }}-{{ $prodotti->lastItem() }} 
                        di {{ $prodotti->total() }} prodotti
                    </small>
                </div>
                
                {{-- CONTROLLI PAGINAZIONE --}}
                <div class="d-flex justify-content-center">
                    <nav aria-label="Paginazione prodotti">
                        <ul class="pagination pagination-sm mb-0">
                            
                            {{-- PULSANTE PAGINA PRECEDENTE --}}
                            {{-- 
                                CONDIZIONE: Se siamo sulla prima pagina, disabilita il pulsante
                                onFirstPage(): Metodo Laravel per verificare se è la prima pagina
                            --}}
                            @if ($prodotti->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    {{-- 
                                        URL PRECEDENTE: 
                                        - appends(request()->query()): Mantiene parametri GET esistenti
                                        - previousPageUrl(): URL della pagina precedente
                                    --}}
                                    <a class="page-link" href="{{ $prodotti->appends(request()->query())->previousPageUrl() }}">‹</a>
                                </li>
                            @endif

                            {{-- ========== NUMERI DELLE PAGINE ========== --}}
                            {{-- 
                                LOOP FOREACH: Genera link per ogni pagina
                                getUrlRange(1, $prodotti->lastPage()): Array con tutti i numeri pagina
                            --}}
                            @foreach ($prodotti->getUrlRange(1, $prodotti->lastPage()) as $page => $url)
                                {{-- PAGINA CORRENTE: Evidenziata con classe 'active' --}}
                                @if ($page == $prodotti->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    {{-- ALTRE PAGINE: Link normale --}}
                                    <li class="page-item">
                                        {{-- 
                                            URL PAGINA: 
                                            - appends(request()->query()): Mantiene filtri di ricerca
                                            - url($page): URL per la pagina specifica
                                        --}}
                                        <a class="page-link" href="{{ $prodotti->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- PULSANTE PAGINA SUCCESSIVA --}}
                            {{-- 
                                CONDIZIONE: Se ci sono più pagine, mostra pulsante successivo
                                hasMorePages(): Metodo Laravel per verificare se esistono pagine successive
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

    {{-- ========== SEZIONE INFORMAZIONI AMMINISTRATIVE ========== --}}
    {{-- Pannello finale con link di navigazione e informazioni aggiuntive --}}
    <div class="row">
        <div class="col-12">
            {{-- 
                CARD INFORMATIVA:
                - bg-gradient-light: Gradiente grigio chiaro definito in CSS
                - border-0: Rimuove bordo default
                - shadow-sm: Ombra sottile
            --}}
            <div class="card bg-gradient-light border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            {{-- TITOLO SEZIONE --}}
                            <h5 class="text-primary mb-2">
                                <i class="bi bi-shield-check me-2"></i>
                                Pannello Amministrazione Prodotti
                            </h5>
                            
                            {{-- DESCRIZIONE FUNZIONALITÀ --}}
                            <p class="mb-3 text-muted">
                                Controllo completo sui prodotti: creazione, modifica, assegnazione staff e gestione malfunzionamenti.
                            </p>
                            
                            {{-- ========== LINK DI NAVIGAZIONE RAPIDA ========== --}}
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                
                                {{-- LINK: Dashboard amministrativa principale --}}
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Dashboard Admin
                                </a>
                                
                                {{-- LINK: Creazione nuovo prodotto --}}
                                <a href="{{ route('admin.prodotti.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Nuovo Prodotto
                                </a>
                                
                                {{-- LINK: Gestione utenti del sistema --}}
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-people me-1"></i>
                                    Gestione Utenti
                                </a>
                                
                                {{-- LINK: Vista tecnica per staff --}}
                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i>
                                    Vista Tecnica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Fine container principale --}}
@endsection

{{-- ========== SEZIONE JAVASCRIPT ========== --}}
{{-- 
    PUSH SCRIPTS: Aggiunge script alla sezione 'scripts' del layout
    Questi script saranno inclusi nel footer della pagina
--}}
@push('scripts')
<script>
{{-- 
    INIZIALIZZAZIONE DATI PAGINA:
    Crea oggetto globale JavaScript con dati PHP per uso client-side
    LINGUAGGIO: JavaScript embedded in Blade
--}}

// Inizializza l'oggetto PageData se non esiste già (pattern Singleton)
window.PageData = window.PageData || {};

{{-- 
    CONDIZIONALE BLADE + JSON ENCODING:
    Passa dati PHP al JavaScript solo se esistono
    @json(): Helper Blade che converte array/oggetti PHP in JSON valido JavaScript
--}}

// Dati singolo prodotto (se presente nella vista)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Collezione prodotti con paginazione (se presente)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Dati singolo malfunzionamento (se presente)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Collezione malfunzionamenti (se presente)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati singolo centro assistenza (se presente)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Collezione centri assistenza (se presente)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Array categorie prodotti (se presente)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Collezione membri staff (se presente)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche aggregate (se presenti)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente corrente (se presente)
@if(isset($user))
window.PageData.user = @json($user);
@endif

{{-- 
    COMMENTO: Placeholder per aggiungere altri dati se necessario
    Questo pattern permette di espandere facilmente i dati condivisi
--}}
// Aggiungi altri dati che potrebbero servire...
</script>
@endpush

{{-- ========== SEZIONE CSS PERSONALIZZATI ========== --}}
{{-- 
    PUSH STYLES: Aggiunge CSS alla sezione 'styles' del layout
    Questi stili saranno inclusi nell'head della pagina
    LINGUAGGIO: CSS con commenti personalizzati
--}}
@push('styles')
<style>
{{-- 
    === COMMENTO CSS ===
    I seguenti stili sono identici al catalogo per mantenere coerenza visiva
    ma includono aggiunte specifiche per le funzionalità amministrative
    
    ORGANIZZAZIONE:
    1. Stili base card prodotto (identici al catalogo)
    2. Aggiunte specifiche per admin (checkbox, azioni bulk)
    3. Responsive design
    4. Animazioni e transizioni
    5. Personalizzazioni finali
--}}

/* === STILI CARD PRODOTTO IDENTICI AL CATALOGO === */

/* 
    CARD PRODOTTO BASE:
    Definisce l'aspetto base delle card con bordi eleganti e transizioni
*/
.product-card {
    transition: all 0.2s ease; /* Transizione fluida per hover */
    border-radius: 0.5rem; /* Angoli arrotondati */
    overflow: hidden; /* Nasconde contenuto che fuoriesce */
    /* Bordo sottile per tutte le card */
    border: 1px solid #e9ecef !important;
}

/* 
    HOVER EFFECT:
    Effetto di sollevamento al passaggio del mouse
*/
.product-card:hover {
    transform: translateY(-4px); /* Solleva la card di 4px */
    box-shadow: 0 0.75rem 2rem rgba(0,0,0,0.15) !important; /* Ombra più pronunciata */
    /* Bordo blu al hover */
    border-color: #007bff !important;
}

/* 
    CARD CON PROBLEMI CRITICI:
    Bordo rosso per prodotti con malfunzionamenti critici
*/
.product-card.border-danger-subtle {
    border-left: 4px solid #dc3545 !important; /* Bordo sinistro rosso spesso */
    border-top: 1px solid #fecaca !important; /* Altri bordi rosso chiaro */
    border-right: 1px solid #fecaca !important;
    border-bottom: 1px solid #fecaca !important;
    background-color: #fef7f7; /* Sfondo rosso molto chiaro */
}

.product-card.border-danger-subtle:hover {
    border-color: #dc3545 !important; /* Mantiene bordo rosso al hover */
    box-shadow: 0 0.75rem 2rem rgba(220, 53, 69, 0.2) !important; /* Ombra rossa */
}

/* 
    CARD CON PROBLEMI NON CRITICI:
    Bordo arancione per prodotti con malfunzionamenti normali
*/
.product-card.border-warning-subtle {
    border-left: 4px solid #ffc107 !important; /* Bordo sinistro arancione */
    border-top: 1px solid #fff3cd !important; /* Altri bordi arancione chiaro */
    border-right: 1px solid #fff3cd !important;
    border-bottom: 1px solid #fff3cd !important;
    background-color: #fffbf0; /* Sfondo arancione molto chiaro */
}

.product-card.border-warning-subtle:hover {
    border-color: #ffc107 !important;
    box-shadow: 0 0.75rem 2rem rgba(255, 193, 7, 0.2) !important; /* Ombra arancione */
}

/* 
    CARD SENZA PROBLEMI ATTIVE:
    Bordo verde per prodotti attivi senza problemi
*/
.product-card.border-success-subtle {
    border-left: 3px solid #28a745 !important; /* Bordo sinistro verde */
    border-top: 1px solid #d4edda !important; /* Altri bordi verde chiaro */
    border-right: 1px solid #d4edda !important;
    border-bottom: 1px solid #d4edda !important;
}

.product-card.border-success-subtle:hover {
    border-color: #28a745 !important;
    box
    -shadow: 0 0.75rem 2rem rgba(40, 167, 69, 0.15) !important; /* Ombra verde */
}

/* 
    CARD INATTIVE:
    Bordo grigio per prodotti disattivati
*/
.product-card.border-secondary-subtle {
    border-left: 3px solid #6c757d !important; /* Bordo sinistro grigio */
    border-top: 1px solid #e9ecef !important; /* Altri bordi grigio chiaro */
    border-right: 1px solid #e9ecef !important;
    border-bottom: 1px solid #e9ecef !important;
    background-color: #f8f9fa; /* Sfondo grigio molto chiaro */
}

.product-card.border-secondary-subtle:hover {
    border-color: #6c757d !important;
    box-shadow: 0 0.75rem 2rem rgba(108, 117, 125, 0.15) !important; /* Ombra grigia */
}

/* === AGGIUNTE SPECIFICHE PER ADMIN === */

/* 
    STILE ADMIN CARD:
    Posizionamento relativo per elementi assoluti interni
*/
.admin-card {
    position: relative;
}

/* 
    CHECKBOX SELEZIONE PRODOTTO:
    Stile personalizzato per checkbox di selezione multipla
*/
.product-checkbox {
    background-color: rgba(255, 255, 255, 0.9); /* Sfondo bianco semi-trasparente */
    border: 2px solid #007bff; /* Bordo blu */
    border-radius: 0.25rem; /* Angoli leggermente arrotondati */
    backdrop-filter: blur(2px); /* Effetto sfocatura dietro */
}

.product-checkbox:checked {
    background-color: #007bff; /* Sfondo blu quando selezionato */
    border-color: #007bff;
}

.product-checkbox:hover {
    border-color: #0056b3; /* Bordo blu scuro al hover */
    background-color: rgba(255, 255, 255, 1); /* Sfondo completamente opaco */
    transform: scale(1.05); /* Leggero ingrandimento */
}

/* 
    CARD SELEZIONATE:
    Stile per card con checkbox selezionato
*/
.product-card.selected {
    border: 2px solid #007bff !important; /* Bordo blu spesso */
    background-color: #f8f9ff; /* Sfondo blu molto chiaro */
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important; /* Alone blu */
    transform: translateY(-2px); /* Leggero sollevamento */
}

/* 
    PULSANTI AZIONE FLOTTANTI:
    Stile per pulsanti circolari fissi in basso a destra
*/
.btn.rounded-circle {
    display: flex; /* Layout flex per centrare contenuto */
    align-items: center; /* Centra verticalmente */
    justify-content: center; /* Centra orizzontalmente */
    border: none; /* Rimuove bordo default */
    transition: all 0.2s ease; /* Transizione fluida */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Ombra pronunciata */
}

.btn.rounded-circle:hover {
    transform: scale(1.1) translateY(-2px); /* Ingrandisce e solleva */
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); /* Ombra più grande */
}

.btn.rounded-circle:active {
    transform: scale(0.95) translateY(0px); /* Rimpicciolisce quando cliccato */
}

/* 
    ANIMAZIONE FLOAT:
    Animazione di fluttuazione per pulsanti flottanti
*/
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-3px); }
}

.btn.rounded-circle:not(:hover) {
    animation: float 3s ease-in-out infinite; /* Animazione continua quando non in hover */
}

/* 
    RESPONSIVE DESIGN - MOBILE:
    Adattamenti per schermi piccoli
*/
@media (max-width: 768px) {
    .product-checkbox {
        transform: scale(1.4); /* Checkbox più grandi su mobile */
    }
    
    .product-card {
        margin-bottom: 1rem; /* Maggiore spazio tra card */
    }
    
    .btn.rounded-circle {
        width: 45px !important; /* Pulsanti leggermente più piccoli */
        height: 45px !important;
    }
    
    .btn.rounded-circle i {
        font-size: 1.1rem !important; /* Icone leggermente più piccole */
    }
}

/* 
    HOVER EFFECTS DROPDOWN:
    Effetti di transizione per elementi dropdown
*/
.dropdown-item {
    transition: all 0.15s ease; /* Transizione rapida */
}

.dropdown-item:hover {
    background-color: #f8f9fa; /* Sfondo grigio chiaro */
    padding-left: 1.25rem; /* Indentazione maggiore */
    transform: translateX(2px); /* Leggero spostamento a destra */
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da; /* Sfondo rosso chiaro per azioni pericolose */
    color: #721c24 !important; /* Testo rosso scuro */
}

.dropdown-item.text-success:hover {
    background-color: #d4edda; /* Sfondo verde chiaro per azioni positive */
    color: #155724 !important; /* Testo verde scuro */
}

/* 
    FORM ELIMINAZIONE INLINE:
    Stili per form incorporati nei dropdown
*/
.dropdown-item form {
    margin: 0; /* Rimuove margini default form */
}

.dropdown-item form button {
    border: none; /* Rimuove bordo */
    background: none; /* Rimuove sfondo */
    padding: 0.25rem 1rem; /* Padding come dropdown normale */
    text-align: left; /* Allineamento testo a sinistra */
    width: 100%; /* Larghezza completa */
    color: inherit; /* Eredita colore dal parent */
    transition: all 0.15s ease;
}

.dropdown-item form button:hover {
    background: none; /* Mantiene sfondo trasparente */
    color: inherit; /* Mantiene colore ereditato */
    padding-left: 1.25rem; /* Indentazione al hover */
}

/* 
    BADGE STATO PRODOTTO:
    Stili per badge di stato con bordi
*/
.badge {
    font-size: 0.7rem; /* Dimensione font piccola */
    font-weight: 500; /* Peso font medio */
    border-radius: 0.35rem; /* Angoli arrotondati */
}

.badge.bg-success {
    background-color: #28a745 !important; /* Verde */
    border: 1px solid #1e7e34; /* Bordo verde scuro */
}

.badge.bg-danger {
    background-color: #dc3545 !important; /* Rosso */
    border: 1px solid #bd2130; /* Bordo rosso scuro */
}

.badge.bg-warning {
    background-color: #ffc107 !important; /* Giallo/arancione */
    border: 1px solid #e0a800; /* Bordo arancione scuro */
    color: #212529 !important; /* Testo scuro per contrasto */
}

/* === PAGINAZIONE IDENTICA AL CATALOGO === */

/* 
    CONTENITORE PAGINAZIONE:
    Layout centrato con gap tra elementi
*/
.pagination {
    margin-bottom: 0 !important;
    justify-content: center !important;
    display: flex !important;
    gap: 4px !important; /* Spazio tra i link */
}

.pagination .page-item {
    margin: 0 !important; /* Rimuove margini default */
}

/* 
    LINK PAGINA:
    Stile uniforme per tutti i link di paginazione
*/
.pagination .page-link {
    border: 1px solid #dee2e6 !important; /* Bordo grigio chiaro */
    border-radius: 6px !important; /* Angoli arrotondati */
    color: #6c757d !important; /* Testo grigio */
    background-color: #fff !important; /* Sfondo bianco */
    padding: 6px 12px !important; /* Padding interno */
    font-size: 14px !important; /* Dimensione font */
    font-weight: 400 !important; /* Peso font normale */
    line-height: 1.2 !important; /* Altezza linea */
    text-decoration: none !important; /* Rimuove sottolineatura */
    margin: 0 !important;
    min-width: 32px !important; /* Larghezza minima */
    height: 32px !important; /* Altezza fissa */
    text-align: center !important; /* Centra testo */
    display: flex !important; /* Layout flex */
    align-items: center !important; /* Centra verticalmente */
    justify-content: center !important; /* Centra orizzontalmente */
    box-shadow: none !important; /* Rimuove ombra default */
    transition: all 0.15s ease; /* Transizione fluida */
}

.pagination .page-link:hover {
    color: #495057 !important; /* Testo grigio scuro al hover */
    background-color: #f8f9fa !important; /* Sfondo grigio chiaro */
    border-color: #dee2e6 !important; /* Mantiene bordo */
    text-decoration: none !important;
    transform: translateY(-1px); /* Leggero sollevamento */
}

/* 
    PAGINA ATTIVA:
    Stile per la pagina corrente
*/
.pagination .page-item.active .page-link {
    color: #fff !important; /* Testo bianco */
    background-color: #007bff !important; /* Sfondo blu */
    border-color: #007bff !important; /* Bordo blu */
    font-weight: 500 !important; /* Testo leggermente più grassetto */
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3); /* Ombra blu */
}

/* 
    PAGINA DISABILITATA:
    Stile per pulsanti non cliccabili
*/
.pagination .page-item.disabled .page-link {
    color: #6c757d !important; /* Testo grigio */
    background-color: #fff !important; /* Sfondo bianco */
    border-color: #dee2e6 !important; /* Bordo grigio */
    opacity: 0.65 !important; /* Trasparenza per indicare disabilitazione */
    cursor: not-allowed !important; /* Cursore di divieto */
}

/* 
    FRECCE NAVIGAZIONE:
    Stile per frecce precedente/successivo
*/
.pagination .page-link:contains('‹'),
.pagination .page-link:contains('›') {
    font-weight: bold; /* Testo grassetto */
    font-size: 16px; /* Dimensione maggiore */
}

/* === GRADIENTI E SFONDI === */

/* 
    GRADIENTE PRIMARIO:
    Header con gradiente blu identico al catalogo
*/
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white;
    border: none;
}

/* 
    GRADIENTE CHIARO:
    Sfondo con gradiente grigio per sezioni informative
*/
.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 1px solid #dee2e6;
}

/* 
    SFONDO STATISTICHE HEADER:
    Sfondo semi-trasparente con effetto blur
*/
.bg-white.bg-opacity-10 {
    background-color: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px); /* Effetto sfocatura */
    border: 1px solid rgba(255, 255, 255, 0.2); /* Bordo semi-trasparente */
}

/* === FORM ELEMENTI === */

/* 
    ETICHETTE FORM:
    Stile per label dei campi form
*/
.form-label.fw-semibold {
    font-weight: 600; /* Semi-grassetto */
    color: #495057; /* Grigio scuro */
    margin-bottom: 0.5rem; /* Margine inferiore */
}

/* 
    CAMPI INPUT E SELECT:
    Stile base per elementi form
*/
.form-control,
.form-select {
    border: 1px solid #ced4da; /* Bordo grigio */
    border-radius: 0.375rem; /* Angoli arrotondati */
    transition: all 0.15s ease; /* Transizione per focus */
}

.form-control:focus,
.form-select:focus {
    border-color: #86b7fe; /* Bordo blu chiaro al focus */
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); /* Alone blu */
    outline: none; /* Rimuove outline default */
}

/* 
    INPUT GROUP:
    Stile per gruppi di input con pulsanti
*/
.input-group .btn {
    border-left: none; /* Rimuove bordo sinistro del pulsante */
}

.input-group .form-control:focus + .btn {
    border-color: #86b7fe; /* Sincronizza colore bordo */
}

/* === BADGE FILTRI === */

/* 
    BADGE FILTRI RAPIDI:
    Stile per badge cliccabili dei filtri
*/
.badge.py-2.px-3 {
    font-size: 0.8rem; /* Dimensione font */
    font-weight: 500; /* Peso font medio */
    border-radius: 1rem; /* Angoli molto arrotondati */
    transition: all 0.15s ease; /* Transizione per hover */
}

.badge.py-2.px-3:hover {
    transform: translateY(-1px); /* Leggero sollevamento */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ombra sottile */
}

/* 
    BADGE INATTIVI:
    Stile per filtri non selezionati
*/
.badge.bg-light.text-dark.border {
    background-color: #f8f9fa !important; /* Sfondo grigio chiaro */
    border: 1px solid #dee2e6 !important; /* Bordo grigio */
    color: #495057 !important; /* Testo grigio scuro */
}

.badge.bg-light.text-dark.border:hover {
    background-color: #e9ecef !important; /* Sfondo grigio più scuro al hover */
    border-color: #adb5bd !important; /* Bordo grigio scuro */
}

/* === ALERT E MESSAGGI === */

/* 
    ALERT RISULTATI RICERCA:
    Stile per messaggi informativi
*/
.alert-info {
    background-color: #d1ecf1; /* Sfondo azzurro chiaro */
    border: 1px solid #b6d4dd; /* Bordo azzurro */
    border-left: 4px solid #0dcaf0; /* Bordo sinistro azzurro spesso */
    color: #0c5460; /* Testo azzurro scuro */
    border-radius: 0.375rem; /* Angoli arrotondati */
}

.alert .badge {
    font-size: 0.75rem; /* Badge più piccoli negli alert */
}

/* === TOOLTIP E ACCESSIBILITÀ === */

/* 
    CURSORE TOOLTIP:
    Indica elementi con informazioni aggiuntive
*/
[data-bs-toggle="tooltip"] {
    cursor: help; /* Cursore punto interrogativo */
}

/* 
    FOCUS VISIBILE:
    Migliora accessibilità con outline personalizzato
*/
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid #007bff; /* Outline blu */
    outline-offset: 2px; /* Spazio dall'elemento */
}

/* === ANIMAZIONI === */

/* 
    ANIMAZIONE ROTAZIONE:
    Per elementi di loading
*/
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

/* 
    ANIMAZIONE EVIDENZIAZIONE:
    Per risultati di ricerca
*/
@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

mark.bg-warning {
    animation: highlight 2s ease-out;
    padding: 0 0.2em;
    border-radius: 0.25rem;
}

/* 
    ANIMAZIONE TOAST:
    Per notifiche
*/
.toast-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* === STATI DI CARICAMENTO === */

/* 
    OVERLAY CARICAMENTO:
    Schermata di loading
*/
#loadingOverlay {
    backdrop-filter: blur(2px);
}

#loadingOverlay .card {
    border: none;
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    border-radius: 1rem;
}

/* 
    PULSANTI IN LOADING:
    Stato disabilitato durante operazioni
*/
.btn.loading {
    pointer-events: none; /* Disabilita click */
    opacity: 0.6; /* Riduce opacità */
    position: relative;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* === RESPONSIVE DESIGN === */

/* 
    TABLET:
    Adattamenti per schermi medi
*/
@media (max-width: 992px) {
    .form-label.d-none.d-lg-block {
        display: block !important;
        margin-top: 1rem;
    }
    
    .col-lg-4.col-md-6 .input-group {
        margin-bottom: 1rem;
    }
}

/* 
    MOBILE:
    Adattamenti per schermi piccoli
*/
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .card-body.py-3 {
        padding: 1rem !important;
    }
    
    .d-flex.gap-2.justify-content-center.flex-wrap {
        flex-direction: column;
        align-items: stretch;
    }
    
    .d-flex.gap-2.justify-content-center.flex-wrap .btn {
        margin-bottom: 0.5rem;
    }
    
    .badge.py-2.px-3 {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem !important;
    }
}

/* === MIGLIORAMENTI SPECIFICI === */

/* 
    SEPARATORI DROPDOWN:
    Linee di separazione nei menu
*/
.dropdown-divider {
    margin: 0.5rem 0;
    opacity: 0.3;
}

/* 
    ICONE DROPDOWN:
    Allineamento uniforme delle icone
*/
.dropdown-item i {
    width: 16px;
    text-align: center;
}

/* 
    STATO VUOTO:
    Effetti per messaggi quando non ci sono risultati
*/
.text-center.py-5 i {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.text-center.py-5:hover i {
    opacity: 0.5;
}

/* 
    MIGLIORAMENTI TIPOGRAFICI:
    Definizioni per classi tipografiche
*/
.fw-bold {
    font-weight: 600 !important;
}

.text-muted {
    color: #6c757d !important;
}

/* 
    OMBRE PERSONALIZZATE:
    Definizioni per diverse intensità di ombra
*/
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* === PERSONALIZZAZIONI FINALI === */

/* 
    SCROLLBAR PERSONALIZZATA:
    Stile per browser webkit (Chrome, Safari)
*/
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* 
    TRANSIZIONI GLOBALI:
    Reset e definizione transizioni
*/
* {
    transition: none; /* Reset transizioni globali */
}

/* 
    TRANSIZIONI SPECIFICHE:
    Solo per elementi che ne hanno bisogno
*/
.product-card,
.btn,
.form-control,
.form-select,
.badge,
.dropdown-item {
    transition: all 0.15s ease;
}

/* 
    ANTI-ALIASING:
    Font più nitidi su tutti i sistemi
*/
body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* 
    FOCUS RING PERSONALIZZATO:
    Migliore accessibilità per navigazione da tastiera
*/
:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    border-radius: 0.25rem;
}
</style>
@endpush