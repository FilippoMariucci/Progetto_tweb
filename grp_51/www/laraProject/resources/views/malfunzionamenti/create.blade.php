{{-- 
    Vista Blade per la creazione di nuovi malfunzionamenti/soluzioni (Staff)
    LINGUAGGIO: Blade Template (Laravel) - estende la sintassi HTML con direttive PHP
    SCOPO: Form per permettere al personale staff di aggiungere nuove soluzioni tecniche
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- 
    Definisce il titolo dinamico della pagina utilizzando la direttiva @section
    PHP: Utilizza operatori ternari per condizioni complesse
    Se $isNuovaSoluzione è true, mostra "Nuova Soluzione - Seleziona Prodotto"
    Altrimenti mostra "Aggiungi Soluzione - " concatenato con il nome del prodotto
--}}
@section('title', 
    isset($isNuovaSoluzione) && $isNuovaSoluzione 
        ? 'Nuova Soluzione - Seleziona Prodotto' 
        : 'Aggiungi Soluzione - ' . $prodotto->nome
)

{{-- Inizia la sezione del contenuto principale --}}
@section('content')

{{-- Container Bootstrap per il layout responsive --}}
<div class="container mt-4">

    {{-- 
        SEZIONE HEADER DINAMICO
        HTML/CSS: Utilizza le classi Bootstrap per layout responsive (row, col-12, d-flex)
        CSS: mb-4 = margin-bottom, text-success = colore verde, fs-2 = font-size
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                {{-- Icona Bootstrap con classi per colore e dimensione --}}
                <i class="bi bi-plus-circle text-success me-3 fs-2"></i>
                <div>
                    {{-- 
                        Blade: Condizionale @if per mostrare header diversi
                        PHP: Controlla se la variabile $isNuovaSoluzione esiste ed è true
                    --}}
                    @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                        {{-- Header per nuova soluzione dalla dashboard --}}
                        <h1 class="h2 mb-1">Aggiungi Nuova Soluzione</h1>
                        <p class="text-muted mb-0">
                            Seleziona un prodotto e descrivi il problema con la relativa soluzione
                        </p>
                    @else
                        {{-- Header per soluzione specifica di un prodotto --}}
                        <h1 class="h2 mb-1">Aggiungi Nuova Soluzione</h1>
                        <p class="text-muted mb-0">
                            {{-- 
                                Blade: Sintassi {{ }} per output PHP escapato
                                Accede alle proprietà dell'oggetto $prodotto
                            --}}
                            Prodotto: <strong>{{ $prodotto->nome }}</strong> - {{ $prodotto->modello }}
                        </p>
                    @endif
                </div>
            </div>
            
            {{-- 
                Alert Bootstrap informativo
                CSS: border-start = bordo sinistro, border-4 = spessore 4px
            --}}
            <div class="alert alert-info border-start border-info border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Istruzioni:</strong> 
                {{-- Altro condizionale per testo dinamico --}}
                @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                    Seleziona il prodotto interessato e descrivi accuratamente il problema con una soluzione dettagliata 
                    per aiutare altri tecnici a risolverlo rapidamente.
                @else
                    Descrivi accuratamente il problema e fornisci una soluzione dettagliata 
                    per aiutare altri tecnici a risolverlo rapidamente.
                @endif
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE FORM PRINCIPALE
        Bootstrap: justify-content-center centra orizzontalmente il contenuto
    --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Card Bootstrap per contenere il form --}}
            <div class="card shadow-sm">
                {{-- Header della card con sfondo verde --}}
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{-- Titolo dinamico della card --}}
                        {{ isset($isNuovaSoluzione) && $isNuovaSoluzione ? 'Nuova Soluzione' : 'Soluzione per ' . $prodotto->nome }}
                    </h5>
                </div>
                
                <div class="card-body">
                    {{-- 
                        FORM HTML con action dinamica
                        HTML: method="POST" per invio dati sicuro
                        Blade: route() helper per generare URL delle rotte
                        PHP: Operatore ternario per scegliere la rotta corretta
                    --}}
                    <form method="POST" 
                          action="{{ isset($isNuovaSoluzione) && $isNuovaSoluzione 
                                      ? route('staff.store.nuova.soluzione') 
                                      : route('staff.malfunzionamenti.store', $prodotto) }}" 
                          id="formNuovaSoluzione">
                        
                        {{-- 
                            Token CSRF per sicurezza Laravel
                            Blade: @csrf genera automaticamente il campo hidden per protezione
                        --}}
                        @csrf
                        
                        {{-- 
                            SEZIONE SELEZIONE PRODOTTO COMPLETA
                            Visibile solo quando si crea una nuova soluzione generica
                        --}}
                        @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
    
                            {{-- Alert informativo per sistema di assegnazione --}}
                            <div class="alert alert-success border-start border-success border-4 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check me-3 fs-4"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Sistema di Assegnazione Attivo</h6>
                                        <p class="mb-0">
                                            Come membro dello staff, puoi creare soluzioni <strong>solo per i prodotti che ti sono stati assegnati</strong> dall'amministratore. 
                                            Questo garantisce una gestione organizzata e responsabile del catalogo prodotti.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- 
                                Statistiche prodotti assegnati
                                PHP: Controlla se esistono statistiche e se il totale è maggiore di 0
                            --}}
                            @if(isset($statsAssegnati) && $statsAssegnati['totale'] > 0)
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body py-3">
                                        <div class="row align-items-center">
                                            <div class="col-lg-8">
                                                <h6 class="mb-2">
                                                    <i class="bi bi-graph-up text-success me-2"></i>
                                                    Riepilogo dei Tuoi Prodotti Assegnati
                                                </h6>
                                                {{-- Flex container per badge con gap --}}
                                                <div class="d-flex flex-wrap gap-3">
                                                    {{-- 
                                                        Badge Bootstrap per statistiche
                                                        Accesso agli array PHP tramite notazione ['chiave']
                                                    --}}
                                                    <span class="badge bg-primary px-3 py-2">
                                                        <i class="bi bi-box me-1"></i>{{ $statsAssegnati['totale'] }} Totali
                                                    </span>
                                                    {{-- Condizionale per mostrare badge solo se ci sono problemi --}}
                                                    @if($statsAssegnati['con_problemi'] > 0)
                                                        <span class="badge bg-warning px-3 py-2">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $statsAssegnati['con_problemi'] }} Con Problemi
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="bi bi-check-circle me-1"></i>{{ $statsAssegnati['senza_problemi'] }} Senza Problemi
                                                    </span>
                                                    {{-- 
                                                        Metodo count() su Collection Laravel
                                                        Conta il numero di categorie diverse
                                                    --}}
                                                    <span class="badge bg-info px-3 py-2">
                                                        <i class="bi bi-collection me-1"></i>{{ $statsAssegnati['per_categoria']->count() }} Categorie
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 text-end mt-2 mt-lg-0">
                                                {{-- Link alla gestione prodotti --}}
                                                <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-list me-1"></i>Gestisci i Miei Prodotti
                                                </a>
                                            </div>
                                        </div>
                                        
                                        {{-- Distribuzione per categoria se ci sono più categorie --}}
                                        @if($statsAssegnati['per_categoria']->count() > 1)
                                            <hr class="my-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <small class="text-muted fw-semibold d-block mb-2">Distribuzione per categoria:</small>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        {{-- 
                                                            Foreach Blade per iterare sulla Collection
                                                            PHP: ucfirst() capitalizza la prima lettera
                                                            PHP: str_replace() sostituisce underscore con spazi
                                                        --}}
                                                        @foreach($statsAssegnati['per_categoria'] as $categoria => $count)
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst(str_replace('_', ' ', $categoria)) }}: {{ $count }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Campo di selezione prodotto --}}
                            <div class="mb-4">
                                {{-- 
                                    Label HTML associata al select tramite attributo "for"
                                    CSS: fw-bold = font-weight bold
                                --}}
                                <label for="prodotto_id" class="form-label fw-bold">
                                    <i class="bi bi-box-seam text-primary me-2"></i>
                                    Seleziona Prodotto Assegnato <span class="text-danger">*</span>
                                </label>
                                
                                {{-- Verifica che ci siano prodotti assegnati disponibili --}}
                                @if(isset($prodotti) && $prodotti->count() > 0)
                                    {{-- 
                                        Select HTML con validazione Laravel
                                        Blade: @error() controlla se ci sono errori di validazione per questo campo
                                        CSS: is-invalid aggiunge stili di errore Bootstrap
                                    --}}
                                    <select class="form-select @error('prodotto_id') is-invalid @enderror" 
                                            id="prodotto_id" 
                                            name="prodotto_id" 
                                            required>
                                        <option value="">-- Scegli tra i tuoi {{ $prodotti->count() }} prodotti assegnati --</option>
                                        
                                        {{-- 
                                            Raggruppa i prodotti per categoria
                                            PHP: Variabile @php per logica complessa in Blade
                                            Laravel: groupBy() metodo delle Collection per raggruppare
                                        --}}
                                        @php
                                            $prodottiGrouped = $prodotti->groupBy('categoria');
                                        @endphp
                                        
                                        {{-- Itera sui gruppi di prodotti --}}
                                        @foreach($prodottiGrouped as $categoria => $prodottiCategoria)
                                            {{-- 
                                                Optgroup HTML per raggruppare le opzioni
                                                PHP: ucfirst() e str_replace() per formattare il nome categoria
                                            --}}
                                            <optgroup label="🏷️ {{ ucfirst(str_replace('_', ' ', $categoria)) }} ({{ $prodottiCategoria->count() }} prodotti)">
                                                {{-- Itera sui prodotti di questa categoria --}}
                                                @foreach($prodottiCategoria as $prod)
                                                    {{-- 
                                                        Calcola statistiche per ogni prodotto
                                                        PHP: ?? operatore null coalescing (se null, usa 0)
                                                        Laravel: where() per filtrare Collection
                                                    --}}
                                                    @php
                                                        $problemiCount = $prod->malfunzionamenti->count() ?? 0;
                                                        $criticiCount = $prod->malfunzionamenti->where('gravita', 'critica')->count() ?? 0;
                                                    @endphp
                                                    
                                                    {{-- 
                                                        Option HTML con dati personalizzati (data-*)
                                                        Blade: old() recupera il valore precedente in caso di errore
                                                    --}}
                                                    <option value="{{ $prod->id }}" 
                                                            {{ old('prodotto_id') == $prod->id ? 'selected' : '' }}
                                                            data-categoria="{{ $prod->categoria }}"
                                                            data-modello="{{ $prod->modello }}"
                                                            data-problemi="{{ $problemiCount }}"
                                                            data-critici="{{ $criticiCount }}"
                                                            data-codice="{{ $prod->codice ?? '' }}">
                                                        
                                                        {{-- Testo dell'opzione con informazioni prodotto --}}
                                                        {{ $prod->nome }}
                                                        @if($prod->modello) - {{ $prod->modello }} @endif
                                                        @if($prod->codice) [{{ $prod->codice }}] @endif
                                                        
                                                        {{-- Indicatori stato problemi con pluralizzazione dinamica --}}
                                                        @if($problemiCount > 0)
                                                            ({{ $problemiCount }} 
                                                            @if($problemiCount == 1)
                                                                problema
                                                            @else
                                                                problemi
                                                            @endif
                                                            @if($criticiCount > 0)
                                                                - {{ $criticiCount }} 
                                                                @if($criticiCount == 1)
                                                                    critico
                                                                @else
                                                                    critici
                                                                @endif
                                                            @endif
                                                            )
                                                        @else
                                                            (nessun problema noto)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    
                                    {{-- 
                                        Mostra errori di validazione Laravel
                                        Blade: @error direttiva per gestire errori specifici del campo
                                    --}}
                                    @error('prodotto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    {{-- Testo di aiuto per l'utente --}}
                                    <div class="form-text">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-lightbulb text-warning me-2 mt-1"></i>
                                            <div>
                                                <strong>Suggerimenti per la selezione:</strong>
                                                <ul class="mb-0 mt-1 small">
                                                    <li>I prodotti sono raggruppati per categoria per facilitare la ricerca</li>
                                                    <li>I numeri tra parentesi indicano i problemi già noti per quel prodotto</li>
                                                    <li>Considera di prioritizzare prodotti con molti problemi o critici</li>
                                                    <li>Se non vedi il prodotto che cerchi, contatta l'amministratore</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                @else
                                    {{-- Caso: nessun prodotto assegnato --}}
                                    <div class="alert alert-warning border-start border-warning border-4">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-exclamation-triangle me-3 fs-4 text-warning"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="alert-heading mb-2">Nessun Prodotto Assegnato</h6>
                                                <p class="mb-3">
                                                    {{-- 
                                                        Accesso all'utente autenticato Laravel
                                                        PHP: ?? operatore per valore di fallback
                                                    --}}
                                                    <strong>{{ auth()->user()->nome_completo ?? auth()->user()->username }}</strong>, 
                                                    non hai prodotti assegnati per la gestione delle soluzioni tecniche.
                                                </p>
                                                <p class="mb-3 small">
                                                    Per creare nuove soluzioni, è necessario che l'amministratore ti assegni almeno un prodotto. 
                                                    Questo sistema garantisce una gestione organizzata dove ogni membro dello staff 
                                                    è responsabile di specifici prodotti del catalogo.
                                                </p>
                                                
                                                {{-- Azioni per risolvere con controlli su rotte esistenti --}}
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-warning btn-sm">
                                                        <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                                                    </a>
                                                    {{-- 
                                                        Laravel: Route::has() controlla se una rotta esiste
                                                        Evita errori se la rotta non è definita
                                                    --}}
                                                    @if(Route::has('contatti'))
                                                        <a href="{{ route('contatti') }}" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-envelope me-1"></i>Contatta l'Amministratore
                                                        </a>
                                                    @endif
                                                    @if(Route::has('staff.prodotti.assegnati'))
                                                        <a href="{{ route('staff.prodotti.assegnati') }}" class="btn btn-outline-info btn-sm">
                                                            <i class="bi bi-list me-1"></i>Verifica Assegnazioni
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                @endif
                            </div>
                            
                            {{-- Container per informazioni prodotto (popolato via JavaScript) --}}
                            <div id="prodotto-info-container">
                                {{-- Qui verrà inserita dinamicamente l'info card del prodotto selezionato --}}
                            </div>
                            
                        @endif

                        {{-- 
                            CAMPO TITOLO DEL MALFUNZIONAMENTO
                            HTML: Input text con validazione required
                        --}}
                        <div class="mb-3">
                            <label for="titolo" class="form-label fw-bold">
                                <i class="bi bi-type text-primary me-2"></i>
                                Titolo del Problema <span class="text-danger">*</span>
                            </label>
                            {{-- 
                                Input con attributi HTML5 per validazione
                                maxlength: limita caratteri a 255
                                placeholder: testo di esempio
                                Blade: old() recupera il valore precedente
                            --}}
                            <input type="text" 
                                   class="form-control @error('titolo') is-invalid @enderror" 
                                   id="titolo" 
                                   name="titolo" 
                                   value="{{ old('titolo') }}"
                                   placeholder="es. Perdita di pressione nel circuito principale"
                                   maxlength="255"
                                   required>
                            
                            @error('titolo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Inserisci un titolo chiaro e descrittivo del problema (max 255 caratteri)
                            </div>
                        </div>

                        {{-- CAMPO GRAVITÀ con select predefinito --}}
                        <div class="mb-3">
                            <label for="gravita" class="form-label fw-bold">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Livello di Gravità <span class="text-danger">*</span>
                            </label>
                            {{-- Select con opzioni predefinite e emoji per UX --}}
                            <select class="form-select @error('gravita') is-invalid @enderror" 
                                    id="gravita" 
                                    name="gravita" 
                                    required>
                                <option value="">-- Seleziona gravità --</option>
                                {{-- Opzioni con controllo del valore precedente --}}
                                <option value="bassa" {{ old('gravita') == 'bassa' ? 'selected' : '' }}>
                                    🟢 Bassa - Problema minore, non compromette il funzionamento
                                </option>
                                <option value="media" {{ old('gravita') == 'media' ? 'selected' : '' }}>
                                    🟡 Media - Riduce l'efficienza, richiede intervento programmato
                                </option>
                                <option value="alta" {{ old('gravita') == 'alta' ? 'selected' : '' }}>
                                    🔴 Alta - Compromette il funzionamento, intervento urgente
                                </option>
                            </select>
                            
                            @error('gravita')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 
                            CAMPO DESCRIZIONE PROBLEMA
                            HTML: Textarea per testo lungo
                        --}}
                        <div class="mb-3">
                            <label for="descrizione" class="form-label fw-bold">
                                <i class="bi bi-file-text text-primary me-2"></i>
                                Descrizione del Problema <span class="text-danger">*</span>
                            </label>
                            {{-- 
                                Textarea con rows per altezza iniziale
                                Il contenuto va tra i tag di apertura e chiusura
                            --}}
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4"
                                      placeholder="Descrivi dettagliatamente il malfunzionamento: sintomi, quando si verifica, condizioni specifiche..."
                                      required>{{ old('descrizione') }}</textarea>
                            
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Fornisci tutti i dettagli utili per identificare il problema: sintomi, frequenza, condizioni di utilizzo
                            </div>
                        </div>

                        {{-- CAMPO COMPONENTE DIFETTOSO (opzionale) --}}
                        <div class="mb-3">
                            <label for="componente_difettoso" class="form-label fw-bold">
                                <i class="bi bi-gear text-secondary me-2"></i>
                                Componente Coinvolto <span class="text-muted">(opzionale)</span>
                            </label>
                            {{-- Input opzionale senza attributo required --}}
                            <input type="text" 
                                   class="form-control @error('componente_difettoso') is-invalid @enderror" 
                                   id="componente_difettoso" 
                                   name="componente_difettoso" 
                                   value="{{ old('componente_difettoso') }}"
                                   placeholder="es. Scheda elettronica, Sensore temperatura, Motore principale..."
                                   maxlength="255">
                            
                            @error('componente_difettoso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Specifica quale componente è coinvolto nel malfunzionamento (se noto)
                            </div>
                        </div>

                        {{-- CAMPO CODICE ERRORE (opzionale) --}}
                        <div class="mb-3">
                            <label for="codice_errore" class="form-label fw-bold">
                                <i class="bi bi-hash text-secondary me-2"></i>
                                Codice di Errore <span class="text-muted">(opzionale)</span>
                            </label>
                            {{-- Input con maxlength ridotto per codici brevi --}}
                            <input type="text" 
                                   class="form-control @error('codice_errore') is-invalid @enderror" 
                                   id="codice_errore" 
                                   name="codice_errore" 
                                   value="{{ old('codice_errore') }}"
                                   placeholder="es. E01, ERR_235, F12..."
                                   maxlength="50">
                            
                            @error('codice_errore')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                Inserisci l'eventuale codice di errore mostrato dall'apparecchio
                            </div>
                        </div>

                        {{-- CAMPO SOLUZIONE TECNICA (obbligatorio) --}}
                        <div class="mb-4">
                            <label for="soluzione" class="form-label fw-bold">
                                <i class="bi bi-tools text-success me-2"></i>
                                Soluzione Tecnica <span class="text-danger">*</span>
                            </label>
                            {{-- 
                                Textarea più grande per soluzione dettagliata
                                Placeholder con caratteri speciali HTML (&#10; = newline)
                            --}}
                            <textarea class="form-control @error('soluzione') is-invalid @enderror" 
                                      id="soluzione" 
                                      name="soluzione" 
                                      rows="6"
                                      placeholder="Descrivi step-by-step la procedura per risolvere il problema:&#10;1. Primo passaggio...&#10;2. Secondo passaggio...&#10;&#10;Include materiali necessari, attrezzi, precauzioni di sicurezza..."
                                      required>{{ old('soluzione') }}</textarea>
                            
                            @error('soluzione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- Istruzioni dettagliate per compilazione --}}
                            <div class="form-text">
                                <strong>Importante:</strong> Fornisci una procedura dettagliata e completa. Include:
                                <ul class="mb-0 mt-1">
                                    <li>Passaggi numerati in sequenza logica</li>
                                    <li>Strumenti e materiali necessari</li>
                                    <li>Precauzioni di sicurezza</li>
                                    <li>Controlli da effettuare dopo l'intervento</li>
                                </ul>
                            </div>
                        </div>

                        {{-- 
                            PULSANTI DI AZIONE
                            CSS: d-flex per layout flessibile, gap-2 per spaziatura
                        --}}
                        <div class="d-flex gap-2 flex-wrap">
                            {{-- Pulsante submit del form --}}
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Salva Soluzione
                            </button>
                            
                            {{-- 
                                Pulsante Annulla con URL dinamico
                                Link diverso basato sul contesto (nuova soluzione vs prodotto specifico)
                            --}}
                            @if(isset($isNuovaSoluzione) && $isNuovaSoluzione)
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Annulla
                                </a>
                            @else
                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Annulla
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE STILI CSS SPECIFICI
    Blade: @push aggiunge contenuto a uno stack definito nel layout
--}}
@push('styles')
<style>
/* 
    CSS: Stili personalizzati per migliorare la visualizzazione delle select con optgroup
    Questi stili sovrascrivono quelli di Bootstrap per una UX migliore
*/

/* Stili per i gruppi di opzioni (optgroup) */
optgroup {
    font-weight: bold;           /* Testo in grassetto per distinguere i gruppi */
    color: #6c757d;             /* Colore grigio per i titoli dei gruppi */
    background-color: #f8f9fa;  /* Sfondo grigio chiaro */
}

/* Stili per le opzioni all'interno dei gruppi */
optgroup option {
    font-weight: normal;    /* Testo normale per le opzioni */
    color: #212529;        /* Colore nero per il testo delle opzioni */
    padding-left: 1rem;    /* Indentazione per distinguere dalle etichette dei gruppi */
}

/* 
    CSS: Personalizzazione degli stati di focus per form controls
    Sovrascrive i colori di default di Bootstrap con il tema verde dell'app
*/
.form-control:focus, .form-select:focus {
    border-color: #198754;                                    /* Bordo verde quando il campo è attivo */
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);      /* Ombra verde trasparente */
}

/* CSS: Stili per il testo di aiuto sotto i campi */
.form-text {
    font-size: 0.875rem;   /* Dimensione font leggermente ridotta */
    margin-top: 0.25rem;   /* Margine superiore ridotto */
}

/* 
    CSS: Personalizzazione alert informativi
    Crea un effetto più moderno con sfondo semi-trasparente
*/
.alert-info {
    background-color: rgba(13, 202, 240, 0.1);   /* Sfondo azzurro trasparente al 10% */
    border-color: rgba(13, 202, 240, 0.2);       /* Bordo azzurro trasparente al 20% */
}

/* 
    CSS: Stili per validazione form dinamica
    Bootstrap aggiunge automaticamente la classe .was-validated
*/
.was-validated .form-control:valid,
.was-validated .form-select:valid {
    border-color: #198754;  /* Bordo verde per campi validi */
}

.was-validated .form-control:invalid,
.was-validated .form-select:invalid {
    border-color: #dc3545;  /* Bordo rosso per campi non validi */
}

/* CSS: Effetto hover per le opzioni del select (non sempre supportato) */
.form-select option:hover {
    background-color: #e9ecef;  /* Sfondo grigio chiaro al passaggio del mouse */
}

/* 
    CSS RESPONSIVE: Media query per dispositivi mobili
    Adatta il layout per schermi con larghezza massima di 768px (tablet e smartphone)
*/
@media (max-width: 768px) {
    /* Riduce il padding interno delle card su dispositivi piccoli */
    .card-body {
        padding: 1rem;
    }
    
    /* Cambia il layout dei bottoni da orizzontale a verticale */
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    /* I bottoni occupano tutta la larghezza disponibile */
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT
    Blade: @push('scripts') aggiunge script alla fine della pagina per performance
--}}
@push('scripts')
<script>
/*
    JAVASCRIPT: Inizializzazione dati globali della pagina
    Crea un oggetto globale PageData per condividere dati PHP con JavaScript
    Evita conflitti inizializzando solo se non esiste già
*/

// Inizializza l'oggetto globale se non esiste
window.PageData = window.PageData || {};

/*
    PHP in JavaScript: Trasferimento dati dal backend al frontend
    Blade: @json() converte array/oggetti PHP in formato JSON sicuro
    isset() controlla l'esistenza delle variabili per evitare errori
*/

// Aggiunge i dati del prodotto se disponibili
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Aggiunge l'elenco dei prodotti se disponibile
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Aggiunge i dati del malfunzionamento specifico se disponibili
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Aggiunge l'elenco dei malfunzionamenti se disponibile
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Aggiunge i dati del centro di assistenza se disponibili
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Aggiunge l'elenco dei centri se disponibile
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Aggiunge le categorie prodotti se disponibili
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Aggiunge i membri dello staff se disponibili
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Aggiunge statistiche generali se disponibili
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Aggiunge i dati dell'utente corrente se disponibili
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    NOTA: Questo pattern di trasferimento dati permette di:
    1. Avere dati PHP disponibili in JavaScript senza chiamate AJAX aggiuntive
    2. Mantenere la sincronizzazione tra backend e frontend
    3. Evitare problemi di encoding e caratteri speciali grazie a @json()
    4. Permettere a script esterni di accedere ai dati tramite window.PageData
    
    Utilizzo tipico in altri script:
    - if(window.PageData.prodotto) { ... }
    - console.log(window.PageData.prodotti);
    - let prodottoSelezionato = window.PageData.prodotti.find(p => p.id === selectedId);
*/

// Qui potrebbero essere aggiunti altri script JavaScript specifici per questa pagina
// Ad esempio: validazione form, popolamento dinamico campi, chiamate AJAX, etc.

</script>
@endpush