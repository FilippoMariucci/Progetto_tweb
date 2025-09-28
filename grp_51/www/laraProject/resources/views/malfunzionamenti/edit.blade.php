{{-- 
    SEZIONE JAVASCRIPT INIZIALE
    JavaScript: Definisce una variabile globale per i dati del malfunzionamento
    Blade: @json() converte oggetto PHP in formato JSON sicuro per JavaScript
    POSIZIONAMENTO: Questo script è in cima per essere disponibile immediatamente
--}}
@push('scripts')
<script>
// JavaScript: Variabile globale contenente i dati del malfunzionamento da modificare
// Sarà accessibile in tutti gli altri script della pagina come window.malfunzionamento
window.malfunzionamento = @json($malfunzionamento);
</script>
@endpush

{{-- 
    Vista Blade per modificare malfunzionamento esistente (Staff)
    LINGUAGGIO: Blade Template (Laravel) - permette di modificare soluzioni tecniche esistenti
    SCOPO: Form di modifica completo con validazione per aggiornare malfunzionamenti
    UTENTI: Solo membri dello staff con privilegi di modifica
--}}
@extends('layouts.app')

{{-- 
    Titolo dinamico della pagina
    Blade: Concatena stringhe con il titolo del malfunzionamento specifico
    Questo appare nel tag <title> del browser
--}}
@section('title', 'Modifica Soluzione - ' . $malfunzionamento->titolo)

{{-- Inizio sezione contenuto principale della pagina --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">

    {{-- 
        SEZIONE HEADER DELLA PAGINA
        Bootstrap: row e col-12 per layout a griglia responsive
        CSS: mb-4 = margin-bottom di 4 unità Bootstrap
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- Header con icona e informazioni del malfunzionamento --}}
            <div class="d-flex align-items-center mb-3">
                {{-- 
                    Icona Bootstrap per "modifica"
                    CSS: text-warning = colore arancione, me-3 = margin-end, fs-2 = font-size grande
                --}}
                <i class="bi bi-pencil-square text-warning me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Modifica Soluzione</h1>
                    <p class="text-muted mb-0">
                        {{-- 
                            Blade: Accesso alle proprietà degli oggetti Eloquent (Laravel ORM)
                            $prodotto e $malfunzionamento sono variabili passate dal controller
                        --}}
                        Prodotto: <strong>{{ $prodotto->nome }}</strong> - {{ $prodotto->modello }}
                    </p>
                </div>
            </div>
            
            {{-- 
                Alert Bootstrap di avvertimento per le modifiche
                CSS: border-start border-warning border-4 = bordo sinistro arancione spesso
            --}}
            <div class="alert alert-warning border-start border-warning border-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Attenzione:</strong> Le modifiche saranno visibili immediatamente ai tecnici. 
                Assicurati che tutte le informazioni siano accurate prima di salvare.
            </div>
        </div>
    </div>

    {{-- Layout principale diviso in due colonne responsive --}}
    <div class="row">
        {{-- 
            COLONNA PRINCIPALE: FORM DI MODIFICA
            Bootstrap: col-lg-8 = 8 colonne su 12 per schermi large e superiori
            Su schermi piccoli occupa tutta la larghezza (col-12 implicito)
        --}}
        <div class="col-lg-8">
            {{-- Card Bootstrap per contenere il form --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-form-text text-warning me-2"></i>
                        Modifica Dettagli Soluzione
                    </h5>
                </div>
                <div class="card-body">
                   {{-- 
                        FORM HTML per aggiornamento del malfunzionamento
                        Laravel: route() helper con parametri array per URL specifico
                        HTML: method="POST" con @method('PUT') per update RESTful
                        Laravel Routes: staff.malfunzionamenti.update richiede prodotto_id e malfunzionamento_id
                    --}}
                   <form action="{{ route('staff.malfunzionamenti.update', [$prodotto->id, $malfunzionamento->id]) }}" method="POST">
                        {{-- 
                            Token CSRF per sicurezza Laravel
                            Blade: @csrf genera automaticamente un campo hidden con token anti-CSRF
                        --}}
                        @csrf
                        {{-- 
                            Blade: @method('PUT') override del metodo HTTP
                            HTML supporta solo GET e POST, questo simula PUT per RESTful routing
                        --}}
                        @method('PUT')
                        
                        {{-- 
                            SEZIONE: INFORMAZIONI PROBLEMA
                            Organizzazione visiva del form con intestazioni colorate
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Identificazione Problema
                                </h6>
                            </div>
                        </div>
                        
                        {{-- 
                            CAMPO: TITOLO DEL PROBLEMA
                            HTML: Input text con validazione required e maxlength per limitare caratteri
                        --}}
                        <div class="mb-3">
                            <label for="titolo" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>Titolo del Problema *
                            </label>
                            {{-- 
                                Input con valore pre-popolato per modifica
                                Blade: old() con fallback per recuperare valore precedente (se errore) o esistente
                                Laravel: @error() controlla errori di validazione specifici per questo campo
                                CSS: is-invalid classe Bootstrap per stili di errore
                            --}}
                            <input type="text" 
                                   class="form-control @error('titolo') is-invalid @enderror" 
                                   id="titolo" 
                                   name="titolo" 
                                   value="{{ old('titolo', $malfunzionamento->titolo) }}"
                                   placeholder="es: Lavatrice non centrifuga correttamente"
                                   required 
                                   maxlength="255">
                            {{-- Testo di aiuto per l'utente --}}
                            <div class="form-text">Sii conciso ma specifico nel descrivere il problema</div>
                            {{-- 
                                Blade: Mostra errori di validazione Laravel se presenti
                                Solo visibile se la validazione del campo 'titolo' fallisce
                            --}}
                            @error('titolo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- 
                            CAMPO: DESCRIZIONE DETTAGLIATA
                            HTML: Textarea per testo lungo con validazione required
                        --}}
                        <div class="mb-3">
                            <label for="descrizione" class="form-label fw-semibold">
                                <i class="bi bi-file-text me-1"></i>Descrizione Dettagliata *
                            </label>
                            {{-- 
                                Textarea con valore esistente per modifica
                                Il contenuto del textarea va tra i tag di apertura e chiusura
                                rows="4" imposta altezza iniziale
                            --}}
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4" 
                                      required
                                      placeholder="Descrivi in dettaglio il problema, quando si verifica, sintomi osservati...">{{ old('descrizione', $malfunzionamento->descrizione) }}</textarea>
                            <div class="form-text">Include tutti i dettagli che possono essere utili per la diagnosi</div>
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- 
                            SEZIONE: GRAVITÀ E DIFFICOLTÀ
                            Bootstrap: row e col-md-6 per layout a due colonne responsive
                        --}}
                        <div class="row mb-3">
                            {{-- Colonna 1: Selezione gravità --}}
                            <div class="col-md-6">
                                <label for="gravita" class="form-label fw-semibold">
                                    <i class="bi bi-speedometer2 me-1"></i>Gravità del Problema *
                                </label>
                                {{-- 
                                    Select HTML con opzioni predefinite per gravità
                                    Blade: Controllo del valore selezionato con old() e fallback al valore esistente
                                --}}
                                <select class="form-select @error('gravita') is-invalid @enderror" 
                                        id="gravita" 
                                        name="gravita" 
                                        required>
                                    <option value="">Seleziona gravità</option>
                                    {{-- 
                                        Opzioni con controllo selected dinamico
                                        PHP: Operatore == per confronto valori, operatore ternario per selected
                                        Emoji per migliorare UX e identificazione rapida
                                    --}}
                                    <option value="bassa" {{ old('gravita', $malfunzionamento->gravita) == 'bassa' ? 'selected' : '' }}>
                                        🟢 Bassa - Problema minore
                                    </option>
                                    <option value="media" {{ old('gravita', $malfunzionamento->gravita) == 'media' ? 'selected' : '' }}>
                                        🟡 Media - Funzionalità compromessa
                                    </option>
                                    <option value="alta" {{ old('gravita', $malfunzionamento->gravita) == 'alta' ? 'selected' : '' }}>
                                        🟠 Alta - Problema significativo
                                    </option>
                                    <option value="critica" {{ old('gravita', $malfunzionamento->gravita) == 'critica' ? 'selected' : '' }}>
                                        🔴 Critica - Prodotto inutilizzabile
                                    </option>
                                </select>
                                @error('gravita')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Colonna 2: Selezione difficoltà --}}
                            <div class="col-md-6">
                                <label for="difficolta" class="form-label fw-semibold">
                                    <i class="bi bi-star me-1"></i>Difficoltà Riparazione *
                                </label>
                                {{-- Select per livello di difficoltà con stelle per indicare complessità --}}
                                <select class="form-select @error('difficolta') is-invalid @enderror" 
                                        id="difficolta" 
                                        name="difficolta" 
                                        required>
                                    <option value="">Seleziona difficoltà</option>
                                    <option value="facile" {{ old('difficolta', $malfunzionamento->difficolta) == 'facile' ? 'selected' : '' }}>
                                        ⭐ Facile - Tecnico junior
                                    </option>
                                    <option value="media" {{ old('difficolta', $malfunzionamento->difficolta) == 'media' ? 'selected' : '' }}>
                                        ⭐⭐ Media - Tecnico esperto
                                    </option>
                                    <option value="difficile" {{ old('difficolta', $malfunzionamento->difficolta) == 'difficile' ? 'selected' : '' }}>
                                        ⭐⭐⭐ Difficile - Tecnico specializzato
                                    </option>
                                    <option value="esperto" {{ old('difficolta', $malfunzionamento->difficolta) == 'esperto' ? 'selected' : '' }}>
                                        ⭐⭐⭐⭐ Esperto - Solo tecnici certificati
                                    </option>
                                </select>
                                @error('difficolta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- 
                            SEZIONE: PROCEDURA DI RISOLUZIONE
                            Intestazione per separare logicamente le sezioni del form
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-tools me-2"></i>Procedura di Risoluzione
                                </h6>
                            </div>
                        </div>
                        
                        {{-- 
                            CAMPO: SOLUZIONE DETTAGLIATA
                            Textarea ampia per procedure step-by-step
                        --}}
                        <div class="mb-3">
                            <label for="soluzione" class="form-label fw-semibold">
                                <i class="bi bi-list-ol me-1"></i>Procedura Risolutiva *
                            </label>
                            {{-- 
                                Textarea con placeholder che include caratteri speciali HTML
                                &#10; = carattere newline HTML per mostrare esempio multi-riga nel placeholder
                                rows="6" per altezza maggiore dato che le soluzioni sono lunghe
                            --}}
                            <textarea class="form-control @error('soluzione') is-invalid @enderror" 
                                      id="soluzione" 
                                      name="soluzione" 
                                      rows="6" 
                                      required
                                      placeholder="1. Spegnere la lavatrice e scollegarla dalla rete elettrica&#10;2. Controllare il filtro della pompa di scarico...&#10;3. Verificare la cinghia di trasmissione...&#10;4. Testare il funzionamento...">{{ old('soluzione', $malfunzionamento->soluzione) }}</textarea>
                            <div class="form-text">
                                <strong>Suggerimento:</strong> Numera i passaggi per renderli più chiari (1. 2. 3...)
                            </div>
                            @error('soluzione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- 
                            SEZIONE: STRUMENTI E TEMPO
                            Layout responsive a due colonne per ottimizzare spazio
                        --}}
                        <div class="row mb-3">
                            {{-- Colonna 1: Strumenti necessari (più ampia per testo lungo) --}}
                            <div class="col-md-8">
                                <label for="strumenti_necessari" class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Strumenti Necessari
                                </label>
                                {{-- Input opzionale per elencare strumenti separati da virgole --}}
                                <input type="text" 
                                       class="form-control @error('strumenti_necessari') is-invalid @enderror" 
                                       id="strumenti_necessari" 
                                       name="strumenti_necessari" 
                                       value="{{ old('strumenti_necessari', $malfunzionamento->strumenti_necessari) }}"
                                       placeholder="es: Cacciavite a croce, Multimetro, Chiave inglese 10mm">
                                <div class="form-text">Elenca gli strumenti separati da virgole</div>
                                @error('strumenti_necessari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Colonna 2: Tempo stimato in minuti --}}
                            <div class="col-md-4">
                                <label for="tempo_stimato" class="form-label fw-semibold">
                                    <i class="bi bi-clock me-1"></i>Tempo Stimato (minuti)
                                </label>
                                {{-- 
                                    Input numerico con validazione HTML5
                                    min/max per limitare valori ragionevoli (1-999 minuti)
                                --}}
                                <input type="number" 
                                       class="form-control @error('tempo_stimato') is-invalid @enderror" 
                                       id="tempo_stimato" 
                                       name="tempo_stimato" 
                                       value="{{ old('tempo_stimato', $malfunzionamento->tempo_stimato) }}"
                                       min="1" 
                                       max="999" 
                                       placeholder="30">
                                @error('tempo_stimato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- 
                            SEZIONE: INFORMAZIONI STATISTICHE
                            Dati storici sul malfunzionamento per tracking e analisi trend
                        --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-graph-up me-2"></i>Informazioni Statistiche
                                </h6>
                            </div>
                        </div>
                        
                        {{-- Layout a tre colonne per dati statistici --}}
                        <div class="row mb-4">
                            {{-- Colonna 1: Conteggio segnalazioni --}}
                            <div class="col-md-4">
                                <label for="numero_segnalazioni" class="form-label">
                                    <i class="bi bi-flag me-1"></i>Numero Segnalazioni
                                </label>
                                {{-- 
                                    Input numerico per conteggio segnalazioni
                                    Campo opzionale per statistiche (non required)
                                --}}
                                <input type="number" 
                                       class="form-control" 
                                       id="numero_segnalazioni" 
                                       name="numero_segnalazioni" 
                                       value="{{ old('numero_segnalazioni', $malfunzionamento->numero_segnalazioni) }}"
                                       min="1" 
                                       max="999">
                                <div class="form-text">Quante volte è stato segnalato</div>
                            </div>
                            
                            {{-- Colonna 2: Data prima segnalazione --}}
                            <div class="col-md-4">
                                <label for="prima_segnalazione" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Prima Segnalazione
                                </label>
                                {{-- 
                                    Input data HTML5 con validazione max (non date future)
                                    PHP: ?-> operatore null-safe per evitare errori se data è null
                                    Laravel: format() metodo Carbon per formattare date in formato Y-m-d (ISO)
                                    PHP: date() funzione nativa per data odierna come limite massimo
                                --}}
                                <input type="date" 
                                       class="form-control" 
                                       id="prima_segnalazione" 
                                       name="prima_segnalazione" 
                                       value="{{ old('prima_segnalazione', $malfunzionamento->prima_segnalazione?->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                            
                            {{-- Colonna 3: Data ultima segnalazione --}}
                            <div class="col-md-4">
                                <label for="ultima_segnalazione" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>Ultima Segnalazione
                                </label>
                                {{-- Input data per tracking ultima occorrenza del problema --}}
                                <input type="date" 
                                       class="form-control" 
                                       id="ultima_segnalazione" 
                                       name="ultima_segnalazione" 
                                       value="{{ old('ultima_segnalazione', $malfunzionamento->ultima_segnalazione?->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        
                        {{-- 
                            SEZIONE: PULSANTI DI AZIONE
                            Layout flex con separazione tra azioni secondarie e principali
                            CSS: justify-content-between distribuisce spazio, align-items-center allinea verticalmente
                        --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            {{-- Pulsanti secondari allineati a sinistra --}}
                            <div>
                                {{-- 
                                    Link di annullamento che torna alla vista dettaglio
                                    Laravel: route() con array di parametri multipli [prodotto, malfunzionamento]
                                --}}
                                <a href="{{ route('staff.malfunzionamenti.show', [$prodotto, $malfunzionamento]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            {{-- Pulsanti principali allineati a destra --}}
                            <div>
                                {{-- 
                                    Pulsante Anteprima (gestito via JavaScript)
                                    HTML: type="button" per evitare submit del form
                                    JavaScript userà l'id "previewBtn" per attach event listeners
                                --}}
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                {{-- 
                                    Pulsante Elimina con modal Bootstrap
                                    data-bs-* attributi Bootstrap 5 per controllo modal
                                    data-bs-toggle="modal" apre il modal
                                    data-bs-target="#deleteModal" specifica quale modal aprire
                                --}}
                                <button type="button" class="btn btn-outline-danger me-2" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-1"></i>Elimina
                                </button>
                                {{-- 
                                    Pulsante submit principale del form
                                    type="submit" invia il form al server
                                    CSS: btn-warning = colore arancione per "modifica"
                                --}}
                                <button type="submit" class="btn btn-warning" id="updateBtn">
                                    <i class="bi bi-check-circle me-1"></i>Salva Modifiche
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- 
            COLONNA SIDEBAR: INFORMAZIONI AGGIUNTIVE
            Bootstrap: col-lg-4 = 4 colonne su 12 per informazioni laterali di supporto
            Su schermi piccoli va sotto il form principale
        --}}
        <div class="col-lg-4">
            
            {{-- 
                CARD: STATO ATTUALE DELLA SOLUZIONE
                Mostra statistiche e metriche chiave del malfunzionamento
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>Stato Attuale
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Layout a tre colonne per metriche chiave --}}
                    <div class="row text-center">
                        {{-- Colonna 1: Numero segnalazioni --}}
                        <div class="col-4">
                            <div class="border-end">
                                {{-- 
                                    PHP: ?? operatore null coalescing (se null usa 0)
                                    Mostra numero segnalazioni o 0 di default
                                --}}
                                <h5 class="mb-1">{{ $malfunzionamento->numero_segnalazioni ?? 0 }}</h5>
                                <small class="text-muted">Segnalazioni</small>
                            </div>
                        </div>
                        {{-- Colonna 2: Badge gravità con colore dinamico --}}
                        <div class="col-4">
                            <div class="border-end">
                                {{-- 
                                    PHP: Operatori ternari annidati per colori badge dinamici
                                    Logica: critica=rosso(danger), alta=arancione(warning), altro=azzurro(info)
                                --}}
                                <span class="badge bg-{{ $malfunzionamento->gravita == 'critica' ? 'danger' : ($malfunzionamento->gravita == 'alta' ? 'warning' : 'info') }}">
                                    {{-- PHP: ucfirst() capitalizza la prima lettera --}}
                                    {{ ucfirst($malfunzionamento->gravita) }}
                                </span>
                                <div><small class="text-muted">Gravità</small></div>
                            </div>
                        </div>
                        {{-- Colonna 3: Tempo formattato --}}
                        <div class="col-4">
                            {{-- 
                                Laravel: Accessor personalizzato tempo_formattato
                                Probabilmente converte minuti in formato leggibile (es. "1h 30m")
                                Definito nel Model Malfunzionamento come getAttribute
                            --}}
                            <h6 class="mb-1">{{ $malfunzionamento->tempo_formattato }}</h6>
                            <small class="text-muted">Tempo</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    {{-- 
                        Informazioni meta sul malfunzionamento
                        Utilizza relazioni Eloquent e metodi Carbon per gestione date
                    --}}
                    <div class="small">
                        <p class="mb-2">
                            <strong>Creato da:</strong> 
                            {{-- 
                                Laravel: Relazione Eloquent creatoBy
                                ?-> operatore null-safe per evitare errori se relazione è null
                                ?? fallback se relazione o campo nome_completo sono nulli
                            --}}
                            {{ $malfunzionamento->creatoBy?->nome_completo ?? 'Sistema' }}
                        </p>
                        <p class="mb-2">
                            <strong>Creato il:</strong> 
                            {{-- Laravel: Carbon format() per data formato italiano --}}
                            {{ $malfunzionamento->created_at->format('d/m/Y H:i') }}
                        </p>
                        {{-- Mostra ultima modifica solo se diversa da creazione --}}
                        @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                            <p class="mb-0">
                                <strong>Ultima modifica:</strong> 
                                {{-- Laravel: Carbon diffForHumans() per tempo relativo (es. "2 ore fa") --}}
                                {{ $malfunzionamento->updated_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD: INFORMAZIONI PRODOTTO ASSOCIATO
                Mostra dettagli del prodotto a cui appartiene questo malfunzionamento
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-box text-primary me-2"></i>Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Layout con immagine e informazioni prodotto --}}
                    <div class="d-flex align-items-center mb-3">
                        {{-- 
                            Immagine prodotto con dimensioni fisse
                            CSS: object-fit: cover mantiene proporzioni ritagliando se necessario
                            Laravel: foto_url probabilmente un accessor che gestisce URL immagini
                        --}}
                        <img src="{{ $prodotto->foto_url }}" 
                             class="rounded me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;"
                             alt="{{ $prodotto->nome }}">
                        <div>
                            <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                            <small class="text-muted">{{ $prodotto->modello }}</small>
                            <br>
                            {{-- 
                                Laravel: Accessor categoria_label
                                Probabilmente formatta la categoria per display user-friendly
                            --}}
                            <span class="badge bg-primary">{{ $prodotto->categoria_label }}</span>
                        </div>
                    </div>
                    
                    {{-- Statistiche correlate al prodotto --}}
                    <div class="small">
                        <p class="mb-2">
                            <strong>Problemi totali:</strong> 
                            {{-- 
                                Laravel: Relazione malfunzionamenti con count()
                                Conta tutti i problemi associati a questo prodotto
                            --}}
                            <span class="badge bg-warning">{{ $prodotto->malfunzionamenti->count() }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Critici:</strong>
                            {{-- 
                                Laravel: where() su Collection per filtrare elementi
                                Conta solo malfunzionamenti con gravità critica per questo prodotto
                            --}}
                            <span class="badge bg-danger">
                                {{ $prodotto->malfunzionamenti->where('gravita', 'critica')->count() }}
                            </span>
                        </p>
                    </div>
                    
                    {{-- Link alla vista completa del prodotto --}}
                    <div class="mt-3">
                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Visualizza Prodotto
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD: CRONOLOGIA MODIFICHE
                Timeline visuale delle modifiche al malfunzionamento
                Mostra storia delle modifiche per audit trail
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history text-secondary me-2"></i>Cronologia
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Timeline personalizzata con CSS custom --}}
                    <div class="timeline">
                        {{-- Evento: Creazione iniziale --}}
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Creazione</h6>
                                <p class="timeline-text">{{ $malfunzionamento->created_at->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">
                                    da {{ $malfunzionamento->creatoBy?->nome_completo ?? 'Sistema' }}
                                </small>
                            </div>
                        </div>
                        
                        {{-- Evento: Ultima modifica (solo se diversa da creazione) --}}
                        @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ultima Modifica</h6>
                                    <p class="timeline-text">{{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}</p>
                                    <small class="text-muted">
                                        {{-- 
                                            Laravel: Relazione modificatoBy per tracking chi ha modificato
                                            Probabilmente definita nel Model con belongsTo User
                                        --}}
                                        da {{ $malfunzionamento->modificatoBy?->nome_completo ?? 'Sistema' }}
                                    </small>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Evento: Ultima segnalazione (se presente) --}}
                        @if($malfunzionamento->ultima_segnalazione)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ultima Segnalazione</h6>
                                    <p class="timeline-text">{{ $malfunzionamento->ultima_segnalazione->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD: LINK UTILI
                Collegamenti rapidi per navigazione correlata e workflow efficiente
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-info me-2"></i>Link Utili
                    </h6>
                </div>
                <div class="card-body">
                    {{-- 
                        Bootstrap: d-grid gap-2 per bottoni verticali con spaziatura uniforme
                        Tutti i bottoni avranno la stessa larghezza
                    --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-list me-1"></i>Tutte le Soluzioni
                        </a>
                        <a href="{{ route('staff.malfunzionamenti.create', $prodotto) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus me-1"></i>Nuova Soluzione
                        </a>
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Dashboard Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    MODAL BOOTSTRAP: ANTEPRIMA MODIFICHE
    Permette di visualizzare come appariranno le modifiche prima di salvarle
    JavaScript popola dinamicamente il contenuto
--}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Modifiche
                </h5>
                {{-- Bootstrap: btn-close per chiudere modal --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- 
                    Container per contenuto generato dinamicamente via JavaScript
                    Il contenuto sarà popolato dal click su "Anteprima"
                --}}
                <div id="previewContent">
                    {{-- Il contenuto verrà popolato via JavaScript --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                {{-- 
                    Pulsante per confermare direttamente dal modal
                    JavaScript gestirà il submit del form principale quando cliccato
                --}}
                <button type="button" class="btn btn-warning" id="updateFromPreview">
                    <i class="bi bi-check-circle me-1"></i>Conferma Modifiche
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 
    MODAL BOOTSTRAP: CONFERMA ELIMINAZIONE
    Modal di sicurezza per prevenire eliminazioni accidentali
    Implementa pattern di double-confirmation per azioni distruttive
--}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Header rosso per indicare azione pericolosa --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Conferma Eliminazione
                </h5>
                {{-- btn-close-white per contrasto su sfondo scuro --}}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Alert Bootstrap di pericolo per enfatizzare gravità dell'azione --}}
                <div class="alert alert-danger">
                    <strong>Attenzione!</strong> Stai per eliminare definitivamente questa soluzione.
                </div>
                
                <p>Sei sicuro di voler eliminare la soluzione:</p>
                {{-- 
                    Blockquote HTML semantico per citare il titolo da eliminare
                    Rende visivamente chiaro cosa si sta per eliminare
                --}}
                <blockquote class="blockquote">
                    <p class="mb-2"><strong>"{{ $malfunzionamento->titolo }}"</strong></p>
                    <footer class="blockquote-footer">
                        {{ $prodotto->nome }} - {{ $prodotto->modello }}
                    </footer>
                </blockquote>
                
                <p class="text-danger">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <strong>Questa azione non può essere annullata.</strong>
                </p>
                
                {{-- 
                    Checkbox di conferma per doppia sicurezza (UX pattern)
                    JavaScript abiliterà il pulsante elimina solo quando spuntato
                --}}
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDelete">
                    <label class="form-check-label" for="confirmDelete">
                        Confermo di voler eliminare questa soluzione
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                {{-- 
                    Form separato per eliminazione con metodo DELETE
                    Laravel: @method('DELETE') per RESTful routing
                    style="display: inline;" per mantenerlo sulla stessa riga
                --}}
                <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    {{-- 
                        Pulsante inizialmente disabilitato (disabled)
                        JavaScript lo abiliterà quando checkbox di conferma è spuntato
                    --}}
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="bi bi-trash me-1"></i>Elimina Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade: @push('styles') aggiunge CSS al layout principale
    Questi stili sovrascrivono/estendono Bootstrap per UX personalizzata
--}}
@push('styles')
<style>
/* 
    CSS: Stili personalizzati per le card
    Rimuove bordi default Bootstrap e aggiunge ombra moderna
*/
.card-custom {
    border: none;                                      /* Rimuove bordo default Bootstrap */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);           /* Ombra sottile per effetto elevazione Material Design */
    transition: all 0.3s ease;                        /* Transizione smooth per eventuali animazioni hover */
}

/* CSS: Stili per label con peso font personalizzato */
.form-label.fw-semibold {
    color: #495057;          /* Grigio scuro Bootstrap per buon contrasto */
    font-weight: 600;        /* Semi-bold per enfasi senza essere eccessivo */
}

/* CSS: Override per bordi Bootstrap più spessi e visibili */
.border-start.border-4 {
    border-width: 4px !important;    /* !important per sovrascrivere specificity Bootstrap */
}

/* CSS: Dimensione consistente per badge in tutta l'interfaccia */
.badge {
    font-size: 0.75rem;     /* Leggermente più piccolo del testo normale per gerarchia visiva */
}

/* 
    CSS: Stili per timeline personalizzata
    Crea una linea verticale visiva che connette gli eventi cronologici
*/
.timeline {
    position: relative;      /* Necessario per posizionamento assoluto dei figli */
    padding-left: 20px;      /* Spazio a sinistra per markers e linea connettrice */
}

.timeline-item {
    position: relative;      /* Per posizionamento assoluto del marker */
    margin-bottom: 15px;     /* Spaziatura verticale tra eventi */
}

/* Marker circolare per ogni evento della timeline */
.timeline-marker {
    position: absolute;                              /* Posizionamento assoluto rispetto a timeline-item */
    left: -25px;                                     /* Allineamento con padding della timeline */
    top: 5px;                                        /* Allineamento verticale con testo dell'evento */
    width: 12px;
    height: 12px;
    border-radius: 50%;                              /* Cerchio perfetto */
    border: 2px solid white;                         /* Bordo bianco per contrasto */
    box-shadow: 0 0 0 2px #dee2e6;                  /* Ombra grigia per definire il bordo esterno */
}

/* Contenuto testuale dell'evento timeline */
.timeline-content {
    padding-left: 5px;       /* Piccolo indent dal marker per allineamento */
}

/* Titolo di ogni evento timeline */
.timeline-title {
    font-size: 0.9rem;       /* Leggermente più piccolo del testo normale */
    font-weight: 600;        /* Semi-bold per evidenziare l'importanza */
    margin-bottom: 2px;      /* Spazio ridotto sotto il titolo */
}

/* Testo descrittivo dell'evento */
.timeline-text {
    font-size: 0.85rem;      /* Testo più piccolo per informazioni secondarie */
    margin-bottom: 2px;      /* Spazio minimo sotto per compattezza */
}

/* 
    CSS: Stili per anteprima nel modal
    Evidenzia visivamente le sezioni nell'anteprima delle modifiche
*/
#previewContent .preview-section {
    margin-bottom: 1.5rem;                          /* Spaziatura tra sezioni dell'anteprima */
    padding: 1rem;                                  /* Padding interno per leggibilità */
    border-left: 3px solid #ffc107;                /* Bordo giallo a sinistra per evidenziare */
    background-color: #f8f9fa;                     /* Sfondo grigio chiaro per contrasto */
}

#previewContent .preview-title {
    font-weight: bold;       /* Titolo sezione in grassetto */
    color: #ffc107;         /* Colore giallo coordinato con bordo sinistro */
    margin-bottom: 0.5rem;  /* Spazio sotto il titolo della sezione */
}

/* 
    CSS: Evidenziazione delle modifiche
    Stile per evidenziare campi che sono stati modificati nell'anteprima
*/
.highlight-change {
    background-color: #fff3cd;     /* Sfondo giallo chiaro per evidenziare cambiamenti */
    padding: 2px 4px;              /* Padding minimo per leggibilità del testo */
    border-radius: 3px;            /* Bordi leggermente arrotondati */
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT
    Blade: @push('scripts') aggiunge script alla fine della pagina per performance ottimali
    Script caricati dopo DOM per evitare errori di elementi non trovati
--}}
@push('scripts')
<script>
/*
    JavaScript: Inizializzazione dati globali della pagina
    Pattern per condividere dati PHP con JavaScript lato client
    Evita chiamate AJAX per dati già disponibili dal server
*/

// Inizializza l'oggetto PageData se non esiste già (pattern safe)
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    @json() converte oggetti/array PHP in JSON sicuro (escape automatico)
    isset() verifica esistenza variabili per evitare errori undefined
    Questo pattern permette JavaScript di accedere ai dati del backend
*/

// Dati del prodotto corrente a cui appartiene il malfunzionamento
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Lista di tutti i prodotti (se disponibile nel controller)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Dati del malfunzionamento specifico che si sta modificando (duplicato per compatibilità)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista di tutti i malfunzionamenti (se disponibile)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati del centro di assistenza corrente (se applicabile)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista completa dei centri di assistenza (se disponibile)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti per filtri e raggruppamenti
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Membri dello staff per gestione assegnazioni
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche generali dell'applicazione per dashboard
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati dell'utente attualmente autenticato
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    PATTERN DI UTILIZZO DEI DATI:
    
    JavaScript può ora accedere ai dati tramite:
    - window.PageData.prodotto.nome
    - window.PageData.malfunzionamento.titolo
    - window.PageData.user.id
    
    VANTAGGI:
    1. Evita chiamate AJAX per dati già disponibili al caricamento
    2. Mantiene sincronizzazione perfetta backend-frontend
    3. Facilita validazione client-side e popolamento dinamico
    4. Supporta funzionalità avanzate (anteprima, auto-save, etc.)
    
    USI TIPICI:
    - Popolare il modal di anteprima con dati form
    - Validazione JavaScript prima del submit
    - Auto-completamento e suggerimenti
    - Gestione interazioni dinamiche UI
    - Tracking modifiche per conferma uscita
*/

</script>
@endpush