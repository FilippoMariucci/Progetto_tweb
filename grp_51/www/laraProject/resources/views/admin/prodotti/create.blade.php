{{-- 
    Vista Admin Creazione Nuovo Prodotto - PARTE 1
    File: resources/views/admin/prodotti/create.blade.php
    Linguaggio: Blade Template (Laravel) + HTML + CSS
    
    FUNZIONALITÀ IMPLEMENTATE:
    - Form completo per creazione prodotto
    - Upload immagine con anteprima
    - Validazione client-side e server-side
    - Sistema categorie unificato
    - Assegnazione staff (funzionalità opzionale)
    - Layout responsive con sidebar informativa
    
    LIVELLO ACCESSO: Solo Amministratore (Livello 4)
    OPERAZIONE CRUD: CREATE - Creazione nuovo record
--}}

{{-- 
    EXTENDS: Estende il layout principale dell'applicazione Laravel 
    Il layout app.blade.php fornisce struttura base (header, navigation, footer)
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo statico della pagina
    Utilizzato dal layout per impostare il tag <title> del browser
--}}
@section('title', 'Nuovo Prodotto')

{{-- 
    SECTION CONTENT: Contenuto principale della vista
    Layout responsive con form principale + sidebar informativa
--}}
@section('content')
<div class="container-fluid mt-4">
    
    {{-- 
        SEZIONE HEADER PAGINA
        Header informativo con titolo, sottotitolo e alert di supporto
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Layout flex per icona + testi --}}
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-plus-circle text-primary me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Aggiungi Nuovo Prodotto</h1>
                    <p class="text-muted mb-0">
                        Inserisci un nuovo prodotto nel catalogo aziendale
                    </p>
                </div>
            </div>
            
            {{-- 
                Alert informativo con suggerimento
                border-start con Bootstrap per barra colorata laterale
            --}}
            <div class="alert alert-info border-start border-primary border-4">
                <i class="bi bi-lightbulb me-2"></i>
                <strong>Suggerimento:</strong> Compila tutte le informazioni tecniche per fornire il massimo supporto ai tecnici.
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 
            COLONNA FORM PRINCIPALE (8/12 = 66% larghezza)
            Contiene tutto il form di creazione prodotto
        --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear text-primary me-2"></i>
                        Dettagli Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        FORM PRINCIPALE CREAZIONE PRODOTTO
                        - action: route Laravel per store (POST)
                        - enctype: multipart/form-data per upload file
                        - id: per riferimenti JavaScript
                    --}}
                    <form action="{{ route('admin.prodotti.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                        {{-- Token CSRF Laravel per sicurezza --}}
                        @csrf
                        
                        {{-- 
                            SEZIONE INFORMAZIONI BASE
                            Header separatore per organizzare il form
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Informazioni Base
                                </h6>
                            </div>
                        </div>
                        
                        {{-- 
                            RIGA NOME E MODELLO PRODOTTO
                            Layout responsive: 8+4 colonne su desktop, stack su mobile
                        --}}
                        <div class="row mb-3">
                            {{-- Campo Nome Prodotto (obbligatorio) --}}
                            <div class="col-md-8">
                                <label for="nome" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Nome Prodotto *
                                </label>
                                {{-- 
                                    Input con validazione Bootstrap e Laravel
                                    - @error: direttiva Blade per classe CSS errore
                                    - old(): helper Laravel per mantenere valore dopo errore
                                    - required: validazione HTML5
                                    - maxlength: limite caratteri
                                --}}
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="es: Lavatrice EcoWash Pro">
                                {{-- Testo di aiuto --}}
                                <div class="form-text">Nome commerciale del prodotto</div>
                                {{-- 
                                    Messaggio errore Laravel
                                    @error: direttiva per mostrare errori di validazione
                                --}}
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Campo Modello Prodotto (obbligatorio) --}}
                            <div class="col-md-4">
                                <label for="modello" class="form-label fw-semibold">
                                    <i class="bi bi-hash me-1"></i>Modello *
                                </label>
                                <input type="text" 
                                       class="form-control @error('modello') is-invalid @enderror" 
                                       id="modello" 
                                       name="modello" 
                                       value="{{ old('modello') }}"
                                       required 
                                       maxlength="100"
                                       placeholder="es: EW-7000X">
                                @error('modello')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- 
                            SEZIONE CATEGORIA PRODOTTO (CORRETTA)
                            Select dinamico che utilizza il sistema di categorie unificato
                        --}}
                        <div class="col-md-6">
                            <label for="categoria" class="form-label fw-semibold">
                                <i class="bi bi-collection me-1"></i>Categoria *
                            </label>
                            <select class="form-select @error('categoria') is-invalid @enderror" 
                                    id="categoria" 
                                    name="categoria" 
                                    required>
                                <option value="">Seleziona categoria</option>
                                {{-- 
                                    CORREZIONE PRINCIPALE: Sistema categorie dinamico
                                    Utilizza le categorie unificate dal controller/modello
                                    invece di valori hardcoded
                                --}}
                                @if(isset($categorie) && count($categorie) > 0)
                                    {{-- 
                                        CASO PREFERITO: Usa categorie passate dal controller
                                        Assicura coerenza con resto dell'applicazione
                                    --}}
                                    @foreach($categorie as $key => $label)
                                        <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- 
                                        FALLBACK: Se $categorie non disponibile
                                        Chiamata diretta al metodo statico del modello
                                        @php: blocco PHP inline (da usare con parsimonia)
                                    --}}
                                    @php
                                        $categorieUnificate = \App\Models\Prodotto::getCategorieUnifico();
                                    @endphp
                                    @foreach($categorieUnificate as $key => $label)
                                        <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- 
                                Testo di aiuto dinamico
                                Mostra preview delle categorie disponibili
                            --}}
                            <div class="form-text">
                                Seleziona la categoria appropriata per il prodotto
                                <br><small class="text-muted">
                                    Categorie disponibili: 
                                    @if(isset($categorie))
                                        {{-- 
                                            array_slice() + implode() per mostrare prime 3 categorie
                                            Evita overflow di testo con "..."
                                        --}}
                                        {{ implode(', ', array_slice(array_values($categorie), 0, 3)) }}{{ count($categorie) > 3 ? '...' : '' }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        
                        {{-- 
                            CAMPO DESCRIZIONE PRODOTTO
                            Textarea con conteggio caratteri dinamico
                        --}}
                        <div class="mb-4">
                            <label for="descrizione" class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i>Descrizione *
                            </label>
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4" 
                                      required
                                      maxlength="1000"
                                      placeholder="Descrizione dettagliata del prodotto, caratteristiche principali, vantaggi...">{{ old('descrizione') }}</textarea>
                            {{-- 
                                Conteggio caratteri dinamico
                                Aggiornato via JavaScript durante digitazione
                            --}}
                            <div class="form-text">
                                <span id="descrizione-counter">0</span>/1000 caratteri
                            </div>
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- 
                            SEZIONE SPECIFICHE TECNICHE
                            Separatore visuale per organizzare form
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-tools me-2"></i>Specifiche Tecniche
                                </h6>
                            </div>
                        </div>
                        
                        {{-- Campo Note Tecniche (opzionale) --}}
                        <div class="mb-3">
                            <label for="note_tecniche" class="form-label fw-semibold">
                                <i class="bi bi-clipboard-data me-1"></i>Note Tecniche
                            </label>
                            <textarea class="form-control @error('note_tecniche') is-invalid @enderror" 
                                      id="note_tecniche" 
                                      name="note_tecniche" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Specifiche tecniche dettagliate, dimensioni, potenza, caratteristiche speciali...">{{ old('note_tecniche') }}</textarea>
                            {{-- Conteggio caratteri per note tecniche --}}
                            <div class="form-text">
                                <span id="note-counter">0</span>/2000 caratteri
                            </div>
                            @error('note_tecniche')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Campo Modalità Installazione (opzionale) --}}
                        <div class="mb-3">
                            <label for="modalita_installazione" class="form-label fw-semibold">
                                <i class="bi bi-wrench me-1"></i>Modalità di Installazione
                            </label>
                            <textarea class="form-control @error('modalita_installazione') is-invalid @enderror" 
                                      id="modalita_installazione" 
                                      name="modalita_installazione" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Istruzioni dettagliate per l'installazione, requisiti, procedure da seguire...">{{ old('modalita_installazione') }}</textarea>
                            <div class="form-text">
                                <span id="installazione-counter">0</span>/2000 caratteri
                            </div>
                            @error('modalita_installazione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Campo Modalità Uso (opzionale) --}}
                        <div class="mb-4">
                            <label for="modalita_uso" class="form-label fw-semibold">
                                <i class="bi bi-book me-1"></i>Modalità d'Uso
                            </label>
                            <textarea class="form-control @error('modalita_uso') is-invalid @enderror" 
                                      id="modalita_uso" 
                                      name="modalita_uso" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Istruzioni per l'uso corretto, funzioni principali, consigli per l'utilizzatore...">{{ old('modalita_uso') }}</textarea>
                            <div class="form-text">
                                <span id="uso-counter">0</span>/2000 caratteri
                            </div>
                            @error('modalita_uso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- 
                            SEZIONE IMMAGINE PRODOTTO
                            Upload file con anteprima dinamica
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning mb-3">
                                    <i class="bi bi-image me-2"></i>Immagine Prodotto
                                </h6>
                            </div>
                        </div>
                        
                        {{-- Upload Foto Prodotto --}}
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-semibold">
                                <i class="bi bi-camera me-1"></i>Foto Prodotto
                            </label>
                            {{-- 
                                Input file con validazione e anteprima
                                - accept: limita tipi file HTML5
                                - onchange: callback JavaScript per anteprima
                            --}}
                            <input type="file" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   id="foto" 
                                   name="foto"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   onchange="previewImage(this)">
                            <div class="form-text">
                                Formati supportati: JPG, PNG, GIF, WebP. Dimensione massima: 5MB
                            </div>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- 
                                ANTEPRIMA IMMAGINE DINAMICA
                                Div nascosto mostrato via JavaScript dopo selezione file
                            --}}
                            <div id="image-preview" class="mt-3" style="display: none;">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="mb-2">Anteprima:</h6>
                                    {{-- Immagine anteprima con sizing responsivo --}}
                                    <img id="preview-img" src="" class="img-fluid rounded" style="max-height: 200px;">
                                    {{-- Pulsante rimozione immagine --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                        <i class="bi bi-trash me-1"></i>Rimuovi
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 
                            SEZIONE CONFIGURAZIONI
                            Stati e assegnazioni del prodotto
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-gear me-2"></i>Configurazioni
                                </h6>
                            </div>
                        </div>
                        
                        {{-- Riga Stato e Assegnazione Staff --}}
                        <div class="row mb-4">
                            {{-- Select Stato Prodotto --}}
                            <div class="col-md-6">
                                <label for="attivo" class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-1"></i>Stato Prodotto
                                </label>
                                <select class="form-select @error('attivo') is-invalid @enderror" 
                                        id="attivo" 
                                        name="attivo">
                                    {{-- 
                                        Opzioni con emoji e old() per mantenere selezione
                                        Default attivo = '1'
                                    --}}
                                    <option value="1" {{ old('attivo', '1') == '1' ? 'selected' : '' }}>
                                        Attivo (visibile nel catalogo)
                                    </option>
                                    <option value="0" {{ old('attivo') == '0' ? 'selected' : '' }}>
                                        Disattivo (nascosto dal catalogo)
                                    </option>
                                </select>
                                @error('attivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- 
                                Select Assegnazione Staff (Funzionalità Opzionale)
                                Permette assegnare prodotto a membro staff specifico
                            --}}
                            <div class="col-md-6">
                                <label for="staff_assegnato_id" class="form-label fw-semibold">
                                    <i class="bi bi-person-check me-1"></i>Assegna a Staff
                                </label>
                                <select class="form-select @error('staff_assegnato_id') is-invalid @enderror" 
                                        id="staff_assegnato_id" 
                                        name="staff_assegnato_id">
                                    <option value="">Nessuna assegnazione</option>
                                    {{-- 
                                        QUERY ELOQUENT INLINE per ottenere staff
                                        @php: blocco PHP per query complessa
                                        Filtra utenti con livello_accesso = '3' (Staff)
                                    --}}
                                    @php
                                        $staffMembers = \App\Models\User::where('livello_accesso', '3')
                                            ->orderBy('nome')->orderBy('cognome')->get();
                                    @endphp
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}" {{ old('staff_assegnato_id') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->nome }} {{ $staff->cognome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Staff responsabile della gestione (opzionale)</div>
                                @error('staff_assegnato_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- 
                            RIEPILOGO PRODOTTO DINAMICO
                            Area nascosta popolata via JavaScript per preview
                        --}}
                        <div id="riepilogo-prodotto" class="alert alert-light border" style="display: none;">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle text-success me-2"></i>Riepilogo Prodotto
                            </h6>
                            <div id="riepilogo-content"></div>
                        </div>
                        
                        {{-- 
                            PULSANTI AZIONE FORM
                            Layout flex per allineamento e spaziatura
                        --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                {{-- Pulsante Annulla (torna alla lista) --}}
                                <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            <div>
                                {{-- Pulsante Anteprima (apre modal) --}}
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                {{-- Pulsante Submit Form --}}
                                <button type="submit" class="btn btn-success" id="createBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Crea Prodotto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- 
            SIDEBAR INFORMATIVA (4/12 = 33% larghezza)
            Pannelli di supporto con guide, statistiche e azioni rapide
        --}}
        <div class="col-lg-4">
            
            {{-- 
                CARD GUIDA CATEGORIE (CORRETTA)
                Genera dinamicamente la guida dalle categorie unificate
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-collection text-primary me-2"></i>Categorie Prodotti
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        {{-- 
                            CORREZIONE: Sistema dinamico invece di descrizioni hardcoded
                            Array associativo per mappare categorie con descrizioni
                        --}}
                        @php
                            // Mapping categorie -> descrizioni personalizzate
                            $categorieGuida = [
                                'lavatrice' => ['label' => 'Lavatrici', 'desc' => 'Lavatrici a carica frontale e dall\'alto'],
                                'lavastoviglie' => ['label' => 'Lavastoviglie', 'desc' => 'Lavastoviglie da incasso e libere'],
                                'frigorifero' => ['label' => 'Frigoriferi', 'desc' => 'Frigoriferi, congelatori e combinati'],
                                'forno' => ['label' => 'Forni', 'desc' => 'Forni elettrici, gas e combinati'],
                                'asciugatrice' => ['label' => 'Asciugatrici', 'desc' => 'Asciugatrici a condensazione e pompa di calore'],
                                'piano_cottura' => ['label' => 'Piani Cottura', 'desc' => 'Piani a induzione, gas e elettrici'],
                                'cappa' => ['label' => 'Cappe Aspiranti', 'desc' => 'Cappe a parete, isola e da incasso'],
                                'microonde' => ['label' => 'Microonde', 'desc' => 'Microonde semplici e combinati'],
                                'condizionatore' => ['label' => 'Condizionatori', 'desc' => 'Climatizzatori e split'],
                                'aspirapolvere' => ['label' => 'Aspirapolvere', 'desc' => 'Robot e aspirapolvere tradizionali'],
                                'ferro_stiro' => ['label' => 'Ferri da Stiro', 'desc' => 'Ferri con caldaia e tradizionali'],
                                'macchina_caffe' => ['label' => 'Macchine Caffè', 'desc' => 'Espresso automatiche e manuali'],
                                'scaldabagno' => ['label' => 'Scaldabagni', 'desc' => 'Boiler elettrici e a gas'],
                                'caldaia' => ['label' => 'Caldaie', 'desc' => 'Caldaie murali e a basamento'],
                                'altro' => ['label' => 'Altri Elettrodomestici', 'desc' => 'Tutti gli altri elettrodomestici']
                            ];
                            
                            // Ottieni categorie disponibili nel sistema
                            $categorieDisponibili = isset($categorie) && count($categorie) > 0 
                                ? $categorie 
                                : \App\Models\Prodotto::getCategorieUnifico();
                        @endphp
                        
                        {{-- 
                            Loop attraverso categorie disponibili nel sistema
                            Mostra solo quelle effettivamente configurate
                        --}}
                        @foreach($categorieDisponibili as $key => $label)
                            @if(isset($categorieGuida[$key]))
                                {{-- Categoria con descrizione personalizzata --}}
                                <div class="mb-2">
                                    <strong>{{ $label }}:</strong> 
                                    <span class="text-muted">{{ $categorieGuida[$key]['desc'] }}</span>
                                </div>
                            @else
                                {{-- Categoria senza mapping -> descrizione generica --}}
                                <div class="mb-2">
                                    <strong>{{ $label }}:</strong> 
                                    <span class="text-muted">Prodotti della categoria {{ strtolower($label) }}</span>
                                </div>
                            @endif
                        @endforeach
                        
                        {{-- Nota informativa sistema --}}
                        <hr class="my-2">
                        <div class="text-center">
                            <small class="text-muted fst-italic">
                                Sistema di categorie unificato
                                <br>{{ count($categorieDisponibili) }} categorie disponibili
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD STATISTICHE CATALOGO
                Query in tempo reale per statistiche aggiornate
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>Catalogo Attuale
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Grid statistiche principali --}}
                    <div class="row text-center">
                        <div class="col-6">
                            {{-- 
                                Count totale prodotti
                                Query Eloquent diretta nel template (uso limitato)
                            --}}
                            <h5 class="mb-1 text-primary">{{ \App\Models\Prodotto::count() }}</h5>
                            <small class="text-muted">Prodotti Totali</small>
                        </div>
                        <div class="col-6">
                            {{-- Count prodotti attivi --}}
                            <h5 class="mb-1 text-success">{{ \App\Models\Prodotto::where('attivo', true)->count() }}</h5>
                            <small class="text-muted">Attivi</small>
                        </div>
                    </div>
                    <hr>
                    <div class="small">
                        <strong>Distribuzione per categoria:</strong>
                        {{-- 
                            Query aggregata per top categorie
                            selectRaw: query SQL grezza per funzioni aggregate
                            groupBy + orderBy + take: top 3 categorie
                        --}}
                        @php
                            $categorie_stats = \App\Models\Prodotto::selectRaw('categoria, COUNT(*) as count')
                                ->whereNotNull('categoria')
                                ->groupBy('categoria')
                                ->orderBy('count', 'desc')
                                ->take(3)
                                ->get();
                        @endphp
                        @foreach($categorie_stats as $cat_stat)
                            <div class="d-flex justify-content-between mt-1">
                                <span>{{ ucfirst($cat_stat->categoria) }}</span>
                                <span class="badge bg-secondary">{{ $cat_stat->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD CONSIGLI UTENTE
                Tips statici per migliorare qualità input
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Consigli
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        {{-- Consigli per nome prodotto --}}
                        <div class="mb-3">
                            <strong>Nome prodotto:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Usa un nome chiaro e descrittivo</li>
                                <li>Includi marca e serie se importante</li>
                                <li>Evita caratteri speciali</li>
                            </ul>
                        </div>
                        
                        {{-- Consigli per descrizione --}}
                        <div class="mb-3">
                            <strong>Descrizione efficace:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Evidenzia caratteristiche principali</li>
                                <li>Usa paragrafi brevi</li>
                                <li>Includi vantaggi per l'utente</li>
                            </ul>
                        </div>
                        
                        {{-- Consigli per specifiche tecniche --}}
                        <div class="mb-0">
                            <strong>Specifiche tecniche:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Sii preciso e completo</li>
                                <li>Includi dimensioni e potenza</li>
                                <li>Aggiungi requisiti di installazione</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD AZIONI RAPIDE
                Pulsanti utility per sviluppo e testing
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-danger me-2"></i>Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Link visualizza catalogo --}}
                        <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list me-1"></i>Visualizza Catalogo
                        </a>
                        {{-- 
                            Pulsante dati esempio (JavaScript)
                            Riempie form con dati di test per sviluppo rapido
                        --}}
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="fillSampleData()">
                            <i class="bi bi-magic me-1"></i>Dati di Esempio
                        </button>
                        {{-- Pulsante reset form (JavaScript) --}}
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearForm()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Svuota Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    MODAL ANTEPRIMA PRODOTTO
    Modal Bootstrap 5 per preview completa prima del salvataggio
    Mostra come apparirà il prodotto nel catalogo
--}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- 
                    Contenitore per anteprima
                    Popolato dinamicamente via JavaScript
                --}}
                <div id="previewContent">
                    <!-- Contenuto popolato via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Modifica</button>
                {{-- 
                    Pulsante conferma creazione da preview
                    Triggera submit del form principale
                --}}
                <button type="button" class="btn btn-success" id="createFromPreview">
                    <i class="bi bi-plus-circle me-1"></i>Conferma Creazione
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- 
    PUSH STYLES: CSS personalizzato per questa vista
    Stili specifici per migliorare UX del form di creazione
--}}
@push('styles')
<style>
/**
 * Card personalizzate con ombreggiatura - CSS
 * Effetto moderno per distinguere sezioni
 */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/**
 * Label form con font weight - CSS
 * Migliora leggibilità e gerarchia visiva
 */
.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
}

/**
 * Bordo colorato per alert - CSS
 * Bootstrap border utilities con spessore personalizzato
 */
.border-start.border-4 {
    border-width: 4px !important;
}

/**
 * Badge dimensioni consistenti - CSS
 * Font-size uniforme per tutti i badge
 */
.badge {
    font-size: 0.75rem;
}

/**
 * Stili anteprima prodotto - CSS
 * Layout per preview nel modal
 */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #0d6efd;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #0d6efd;
    margin-bottom: 0.5rem;
}

/**
 * Effetto focus per campi form - CSS
 * Micro-animazione per feedback visivo
 */
.focused { 
    transform: scale(1.01); 
    transition: transform 0.2s ease; 
}

/**
 * Animazioni alert - CSS
 * Fade in per messaggi e notifiche
 */
.alert { 
    animation: fadeIn 0.5s ease; 
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}

/**
 * Stili anteprima immagine - CSS
 * Container e sizing per preview upload
 */
#image-preview {
    max-width: 100%;
}

#preview-img {
    border: 2px solid #dee2e6;
    object-fit: cover;
}

/**
 * Stili contatore caratteri - CSS
 * Colori condizionali per feedback utente
 */
.text-warning { 
    color: #fd7e14 !important; 
}

.text-danger { 
    color: #dc3545 !important; 
}

/**
 * Responsive improvements - CSS Media Queries
 * Adattamenti per dispositivi mobili
 */
@media (max-width: 768px) {
    .card-custom {
        margin-bottom: 1rem;
    }
    
    .modal-dialog.modal-xl {
        margin: 0.5rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
        text-align: center;
    }
}

/**
 * Form validation styling - CSS
 * Miglioramenti visivi per stati validazione
 */
.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4M6 8.2V6.5'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/**
 * Character counter colors - CSS
 * Colori dinamici basati su utilizzo
 */
.char-counter-normal {
    color: #6c757d;
}

.char-counter-warning {
    color: #fd7e14;
    font-weight: 600;
}

.char-counter-danger {
    color: #dc3545;
    font-weight: 700;
}

/**
 * Loading states - CSS
 * Stili per stati di caricamento
 */
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
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
{{-- 
    PUSH SCRIPTS: JavaScript per funzionalità dinamiche
    Gestisce interazioni, validazione, anteprima, contatori caratteri
--}}
@push('scripts')
<script>
/**
 * INIZIALIZZAZIONE DATI PAGINA - JavaScript
 * Configura oggetto globale window.PageData per condividere dati PHP-JS
 * Pattern utilizzato in tutta l'applicazione per consistency
 */
window.PageData = window.PageData || {};

/**
 * CONDIZIONI PHP IN JAVASCRIPT
 * Trasferisce dati dal controller PHP al contesto JavaScript
 * Solo se le variabili sono definite (evita errori)
 */

// Prodotto singolo (se in edit mode)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Lista prodotti (per riferimenti)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento singolo (correlato)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti (per riferimenti)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro assistenza (se correlato)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista centri (per riferimenti)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie unificate (importante per questa vista)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members (per assegnazioni)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche (per dashboard elements)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente (per permessi)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/**
 * INIZIALIZZAZIONE DOM - JavaScript
 * Setup event listeners e funzionalità al caricamento pagina
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Vista creazione prodotto inizializzata');
    
    // Setup contatori caratteri per tutti i textarea
    setupCharacterCounters();
    
    // Setup event listeners per form validation
    setupFormValidation();
    
    // Setup anteprima modal
    setupPreviewModal();
    
    // Setup auto-save (opzionale)
    // setupAutoSave();
    
    console.log('✅ Tutti i componenti inizializzati');
});

/**
 * SETUP CONTATORI CARATTERI - JavaScript
 * Configura contatori dinamici per textarea con limiti
 */
function setupCharacterCounters() {
    console.log('📊 Setup contatori caratteri');
    
    const counters = [
        { field: 'descrizione', counter: 'descrizione-counter', limit: 1000 },
        { field: 'note_tecniche', counter: 'note-counter', limit: 2000 },
        { field: 'modalita_installazione', counter: 'installazione-counter', limit: 2000 },
        { field: 'modalita_uso', counter: 'uso-counter', limit: 2000 }
    ];
    
    counters.forEach(config => {
        const field = document.getElementById(config.field);
        const counter = document.getElementById(config.counter);
        
        if (field && counter) {
            // Aggiorna contatore iniziale
            updateCharCounter(field, counter, config.limit);
            
            // Event listener per aggiornamenti real-time
            field.addEventListener('input', function() {
                updateCharCounter(field, counter, config.limit);
            });
            
            // Event listener per paste
            field.addEventListener('paste', function() {
                setTimeout(() => {
                    updateCharCounter(field, counter, config.limit);
                }, 10);
            });
        }
    });
}

/**
 * AGGIORNA CONTATORE CARATTERI - JavaScript
 * Funzione helper per aggiornare display contatori con colori condizionali
 * 
 * @param {HTMLElement} field - Campo textarea
 * @param {HTMLElement} counter - Elemento contatore
 * @param {number} limit - Limite massimo caratteri
 */
function updateCharCounter(field, counter, limit) {
    const current = field.value.length;
    const percentage = (current / limit) * 100;
    
    // Aggiorna testo contatore
    counter.textContent = current;
    
    // Rimuovi classi precedenti
    counter.classList.remove('char-counter-normal', 'char-counter-warning', 'char-counter-danger');
    
    // Applica classe basata su percentuale utilizzo
    if (percentage >= 90) {
        counter.classList.add('char-counter-danger');
    } else if (percentage >= 75) {
        counter.classList.add('char-counter-warning');
    } else {
        counter.classList.add('char-counter-normal');
    }
    
    // Log per debug (solo se necessario)
    if (window.PageData && window.PageData.debug) {
        console.log(`📝 ${field.id}: ${current}/${limit} caratteri (${percentage.toFixed(1)}%)`);
    }
}

/**
 * SETUP VALIDAZIONE FORM - JavaScript
 * Configura validazione client-side e feedback visivi
 */
function setupFormValidation() {
    console.log('✅ Setup validazione form');
    
    const form = document.getElementById('createProductForm');
    if (!form) return;
    
    // Validazione in tempo reale per campi obbligatori
    const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    requiredFields.forEach(field => {
        // Event listener per focus
        field.addEventListener('focus', function() {
            this.classList.add('focused');
        });
        
        // Event listener per blur (perdita focus)
        field.addEventListener('blur', function() {
            this.classList.remove('focused');
            validateField(this);
        });
        
        // Event listener per input (digitazione)
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    // Validazione pre-submit
    form.addEventListener('submit', function(e) {
        console.log('📤 Validazione pre-submit');
        
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            console.log('❌ Validazione fallita');
            showNotification('Completa tutti i campi obbligatori', 'error');
            
            // Scroll al primo campo con errore
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        } else {
            console.log('✅ Validazione superata');
            // Mostra loading state
            const submitBtn = document.getElementById('createBtn');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
            }
        }
    });
}

/**
 * VALIDA SINGOLO CAMPO - JavaScript
 * Validazione specifica per diversi tipi di campo
 * 
 * @param {HTMLElement} field - Campo da validare
 * @returns {boolean} - True se valido
 */
function validateField(field) {
    let isValid = true;
    let errorMessage = '';
    
    // Validazione base required
    if (field.hasAttribute('required') && !field.value.trim()) {
        isValid = false;
        errorMessage = 'Questo campo è obbligatorio';
    }
    
    // Validazioni specifiche per tipo
    switch (field.type) {
        case 'email':
            if (field.value && !isValidEmail(field.value)) {
                isValid = false;
                errorMessage = 'Inserisci un email valida';
            }
            break;
            
        case 'file':
            if (field.files.length > 0) {
                const file = field.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    isValid = false;
                    errorMessage = 'File troppo grande (max 5MB)';
                }
                
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    errorMessage = 'Formato file non supportato';
                }
            }
            break;
    }
    
    // Applica stili validazione
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        // Rimuovi messaggio errore custom se presente
        const existingError = field.parentNode.querySelector('.custom-error');
        if (existingError) {
            existingError.remove();
        }
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        
        // Aggiungi messaggio errore custom se necessario
        if (errorMessage && !field.parentNode.querySelector('.invalid-feedback')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback custom-error';
            errorDiv.textContent = errorMessage;
            field.parentNode.appendChild(errorDiv);
        }
    }
    
    return isValid;
}

/**
 * VALIDA EMAIL - JavaScript
 * Regex validation per formato email
 * 
 * @param {string} email - Email da validare
 * @returns {boolean} - True se valida
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * PREVIEW IMMAGINE - JavaScript globale
 * Funzione chiamata da onchange input file
 * Mostra anteprima immagine selezionata
 * 
 * @param {HTMLInputElement} input - Input file
 */
window.previewImage = function(input) {
    console.log('🖼️ Preview immagine');
    
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (!preview || !previewImg) return;
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validazione file
        if (!validateField(input)) {
            return;
        }
        
        // FileReader per anteprima
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            
            console.log('✅ Anteprima immagine caricata');
            showNotification('Immagine caricata con successo', 'success');
        };
        
        reader.onerror = function() {
            console.error('❌ Errore caricamento immagine');
            showNotification('Errore nel caricamento immagine', 'error');
        };
        
        reader.readAsDataURL(file);
    } else {
        // Nasconde anteprima se nessun file
        preview.style.display = 'none';
    }
};

/**
 * RIMUOVI IMMAGINE - JavaScript globale
 * Funzione per rimuovere immagine selezionata
 */
window.removeImage = function() {
    console.log('🗑️ Rimozione immagine');
    
    const fileInput = document.getElementById('foto');
    const preview = document.getElementById('image-preview');
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
    
    console.log('✅ Immagine rimossa');
    showNotification('Immagine rimossa', 'info');
};

/**
 * SETUP MODAL ANTEPRIMA - JavaScript
 * Configura modal per preview prodotto completo
 */
function setupPreviewModal() {
    console.log('👁️ Setup modal anteprima');
    
    const previewBtn = document.getElementById('previewBtn');
    const createFromPreviewBtn = document.getElementById('createFromPreview');
    
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            generatePreview();
        });
    }
    
    if (createFromPreviewBtn) {
        createFromPreviewBtn.addEventListener('click', function() {
            // Submit form principale
            document.getElementById('createProductForm').submit();
        });
    }
}

/**
 * GENERA ANTEPRIMA - JavaScript
 * Crea preview HTML del prodotto nel modal
 */
function generatePreview() {
    console.log('🔍 Generazione anteprima prodotto');
    
    const form = document.getElementById('createProductForm');
    const previewContent = document.getElementById('previewContent');
    
    if (!form || !previewContent) return;
    
    // Raccogli dati form
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Genera HTML anteprima
    let html = '';
    
    // Sezione informazioni base
    html += `<div class="preview-section">`;
    html += `<div class="preview-title">📦 Informazioni Base</div>`;
    html += `<p><strong>Nome:</strong> ${data.nome || 'Non specificato'}</p>`;
    html += `<p><strong>Modello:</strong> ${data.modello || 'Non specificato'}</p>`;
    html += `<p><strong>Categoria:</strong> ${getCategoryLabel(data.categoria) || 'Non specificata'}</p>`;
    html += `<p><strong>Descrizione:</strong></p>`;
    html += `<div class="border rounded p-2 bg-white">${data.descrizione || 'Nessuna descrizione'}</div>`;
    html += `</div>`;
    
    // Sezione specifiche tecniche (se presenti)
    if (data.note_tecniche || data.modalita_installazione || data.modalita_uso) {
        html += `<div class="preview-section">`;
        html += `<div class="preview-title">🔧 Specifiche Tecniche</div>`;
        
        if (data.note_tecniche) {
            html += `<p><strong>Note Tecniche:</strong></p>`;
            html += `<div class="border rounded p-2 bg-white">${data.note_tecniche}</div>`;
        }
        
        if (data.modalita_installazione) {
            html += `<p><strong>Modalità Installazione:</strong></p>`;
            html += `<div class="border rounded p-2 bg-white">${data.modalita_installazione}</div>`;
        }
        
        if (data.modalita_uso) {
            html += `<p><strong>Modalità d'Uso:</strong></p>`;
            html += `<div class="border rounded p-2 bg-white">${data.modalita_uso}</div>`;
        }
        
        html += `</div>`;
    }
    
    // Sezione configurazioni
    html += `<div class="preview-section">`;
    html += `<div class="preview-title">⚙️ Configurazioni</div>`;
    html += `<p><strong>Stato:</strong> <span class="badge ${data.attivo == '1' ? 'bg-success' : 'bg-warning'}">${data.attivo == '1' ? '✅ Attivo' : '❌ Disattivo'}</span></p>`;
    
    if (data.staff_assegnato_id) {
        const staffName = getStaffName(data.staff_assegnato_id);
        html += `<p><strong>Assegnato a:</strong> ${staffName}</p>`;
    }
    
    html += `</div>`;
    
    // Anteprima immagine se presente
    const previewImg = document.getElementById('preview-img');
    if (previewImg && previewImg.src && previewImg.src !== '') {
        html += `<div class="preview-section">`;
        html += `<div class="preview-title">🖼️ Immagine Prodotto</div>`;
        html += `<img src="${previewImg.src}" class="img-fluid rounded border" style="max-height: 300px;">`;
        html += `</div>`;
    }
    
    previewContent.innerHTML = html;
    
    // Mostra modal
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    console.log('✅ Anteprima generata');
}

/**
 * UTILITY: OTTIENI LABEL CATEGORIA - JavaScript
 * Converte key categoria in label leggibile
 * 
 * @param {string} categoryKey - Chiave categoria
 * @returns {string} - Label categoria
 */
function getCategoryLabel(categoryKey) {
    const categories = window.PageData?.categorie || {};
    return categories[categoryKey] || categoryKey;
}

/**
 * UTILITY: OTTIENI NOME STAFF - JavaScript
 * Converte ID staff in nome completo
 * 
 * @param {string} staffId - ID staff
 * @returns {string} - Nome staff
 */
function getStaffName(staffId) {
    // In una implementazione completa, questo dovrebbe
    // fare lookup in window.PageData.staffMembers
    const select = document.getElementById('staff_assegnato_id');
    const option = select?.querySelector(`option[value="${staffId}"]`);
    return option?.textContent || `Staff ID: ${staffId}`;
}

/**
 * DATI DI ESEMPIO - JavaScript globale
 * Riempie form con dati di test per sviluppo rapido
 */
window.fillSampleData = function() {
    console.log('🎲 Riempimento dati di esempio');
    
    // Dati di esempio predefiniti
    const sampleData = {
        nome: 'Lavatrice EcoWash Pro',
        modello: 'EW-7000X',
        categoria: 'lavatrice',
        descrizione: 'Lavatrice a carica frontale ad alta efficienza energetica con tecnologia inverter e programmi specializzati per ogni tipo di tessuto.',
        note_tecniche: 'Capacità: 9 kg, Velocità centrifuga: 1400 giri/min, Efficienza energetica: A+++, Dimensioni: 60x55x85 cm',
        modalita_installazione: '1. Rimuovere tutti i materiali di imballaggio\n2. Collegare il tubo di scarico\n3. Allacciare l\'alimentazione idrica\n4. Collegare alla rete elettrica\n5. Verificare stabilità e livellamento',
        modalita_uso: 'Selezionare il programma appropriato in base al tipo di tessuto. Utilizzare detersivo specifico per lavatrice a carica frontale. Non superare mai il carico massimo indicato.',
        attivo: '1'
    };
    
    // Compila i campi
    Object.entries(sampleData).forEach(([key, value]) => {
        const field = document.getElementById(key);
        if (field) {
            field.value = value;
            
            // Trigger eventi per aggiornare contatori e validazione
            field.dispatchEvent(new Event('input'));
            field.dispatchEvent(new Event('change'));
        }
    });
    
    console.log('✅ Dati di esempio caricati');
    showNotification('Dati di esempio caricati nel form', 'info');
};

/**
 * SVUOTA FORM - JavaScript globale
 * Reset completo del form
 */
window.clearForm = function() {
    console.log('🧹 Svuotamento form');
    
    const form = document.getElementById('createProductForm');
    if (!form) return;
    
    // Reset form HTML5
    form.reset();
    
    // Reset classi validazione
    form.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
        field.classList.remove('is-valid', 'is-invalid');
    });
    
    // Reset contatori caratteri
    document.querySelectorAll('[id$="-counter"]').forEach(counter => {
        counter.textContent = '0';
        counter.classList.remove('char-counter-warning', 'char-counter-danger');
        counter.classList.add('char-counter-normal');
    });
    
    // Reset anteprima immagine
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview) {
        imagePreview.style.display = 'none';
    }
    
    // Reset riepilogo prodotto
    const riepilogo = document.getElementById('riepilogo-prodotto');
    if (riepilogo) {
        riepilogo.style.display = 'none';
    }
    
    console.log('✅ Form svuotato completamente');
    showNotification('Form svuotato', 'info');
};

/**
 * SISTEMA NOTIFICHE - JavaScript
 * Mostra notifiche temporanee all'utente
 * 
 * @param {string} message - Messaggio da mostrare
 * @param {string} type - Tipo: success, error, warning, info
 */
function showNotification(message, type = 'info') {
    console.log(`📢 Notifica ${type}: ${message}`);
    
    // Rimuovi notifiche esistenti
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    // Crea elemento notifica
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    // Icona basata su tipo
    const icons = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        warning: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    };
    
    notification.innerHTML = `
        <i class="bi ${icons[type]} me-2"></i>
        ${message}
    `;
    
    // Aggiungi al DOM
    document.body.appendChild(notification);
    
    // Rimozione automatica
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transition = 'opacity 0.3s ease';
            notification.style.opacity = '0';
            
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 3000);
}

/**
 * AUTO-SAVE BOZZA - JavaScript (opzionale)
 * Salva automaticamente i dati del form in localStorage
 * Utile per non perdere lavoro in caso di chiusura accidentale
 */
function setupAutoSave() {
    console.log('💾 Setup auto-save attivato');
    
    const form = document.getElementById('createProductForm');
    const STORAGE_KEY = 'prodotto_bozza_' + Date.now();
    
    if (!form) return;
    
    // Carica bozza esistente se presente
    loadDraft();
    
    // Auto-save ogni 30 secondi
    setInterval(() => {
        saveDraft();
    }, 30000);
    
    // Save su change di campi importanti
    const importantFields = form.querySelectorAll('input[name="nome"], textarea, select');
    importantFields.forEach(field => {
        field.addEventListener('change', saveDraft);
    });
    
    // Cancella bozza al submit
    form.addEventListener('submit', () => {
        localStorage.removeItem(STORAGE_KEY);
        console.log('🗑️ Bozza cancellata dopo submit');
    });
    
    /**
     * Salva bozza in localStorage - JavaScript
     */
    function saveDraft() {
        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Escludi file dal salvataggio (troppo grandi per localStorage)
            delete data.foto;
            
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                timestamp: Date.now(),
                data: data
            }));
            
            console.log('💾 Bozza salvata automaticamente');
        } catch (error) {
            console.warn('⚠️ Errore salvataggio bozza:', error);
        }
    }
    
    /**
     * Carica bozza da localStorage - JavaScript
     */
    function loadDraft() {
        try {
            const savedDraft = localStorage.getItem(STORAGE_KEY);
            if (!savedDraft) return;
            
            const { timestamp, data } = JSON.parse(savedDraft);
            
            // Carica solo se bozza recente (max 24h)
            const maxAge = 24 * 60 * 60 * 1000; // 24 ore
            if (Date.now() - timestamp > maxAge) {
                localStorage.removeItem(STORAGE_KEY);
                return;
            }
            
            // Conferma caricamento
            if (confirm('È stata trovata una bozza non salvata. Vuoi caricarla?')) {
                Object.entries(data).forEach(([key, value]) => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && value) {
                        field.value = value;
                        field.dispatchEvent(new Event('input'));
                        field.dispatchEvent(new Event('change'));
                    }
                });
                
                showNotification('Bozza caricata con successo', 'info');
                console.log('📄 Bozza caricata da localStorage');
            }
        } catch (error) {
            console.warn('⚠️ Errore caricamento bozza:', error);
            localStorage.removeItem(STORAGE_KEY);
        }
    }
}

/**
 * UTILITY: DEBOUNCE - JavaScript
 * Limita frequenza chiamate funzione per performance
 * 
 * @param {Function} func - Funzione da chiamare
 * @param {number} wait - Millisecondi di attesa
 * @returns {Function} - Funzione debounced
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * GESTIONE ERRORI GLOBALI - JavaScript
 * Cattura errori JavaScript non gestiti
 */
window.addEventListener('error', function(e) {
    console.error('❌ Errore JavaScript:', e.error);
    
    // In produzione, invia errori a servizio logging
    if (window.location.hostname !== 'localhost') {
        // logErrorToService(e.error);
    }
    
    // Mostra notifica utente solo per errori critici
    if (e.error && e.error.message.includes('network')) {
        showNotification('Errore di connessione. Riprova.', 'error');
    }
});

/**
 * GESTIONE PROMISES RIFIUTATE - JavaScript
 * Cattura promise rejection non gestite
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('❌ Promise rejection non gestita:', e.reason);
    
    // Previeni log di errore default del browser
    e.preventDefault();
    
    // Gestione specifica per errori comuni
    if (e.reason && e.reason.name === 'NetworkError') {
        showNotification('Problemi di connessione rilevati', 'warning');
    }
});

/**
 * LOG FINALE INIZIALIZZAZIONE - JavaScript
 * Conferma che tutti gli script sono stati caricati
 */
console.log('🎉 Vista creazione prodotto completamente inizializzata');
console.log('📋 Funzionalità disponibili:', {
    'Contatori caratteri': '✅',
    'Validazione form': '✅', 
    'Anteprima immagine': '✅',
    'Modal preview': '✅',
    'Notifiche': '✅',
    'Dati esempio': '✅',
    'Auto-save': '⚠️ Opzionale'
});

/**
 * ESPORTA FUNZIONI GLOBALI - JavaScript
 * Rende disponibili funzioni per uso esterno o debugging
 */
window.ProductCreateUtils = {
    fillSampleData: window.fillSampleData,
    clearForm: window.clearForm,
    previewImage: window.previewImage,
    removeImage: window.removeImage,
    generatePreview: generatePreview,
    showNotification: showNotification,
    validateField: validateField
};

console.log('🔧 Utility prodotto esportate in window.ProductCreateUtils');
</script>
@endpush