{{-- 
    VISTA DETTAGLIO PRODOTTO PER AMMINISTRATORI - VERSIONE SEMPLIFICATA
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    PERCORSO: resources/views/admin/prodotti/show.blade.php
    ACCESSO: Solo livello 4 (Amministratori)
    
    SCOPO: Visualizza tutti i dettagli di un singolo prodotto con controlli amministrativi
    
    FUNZIONALITÀ PRINCIPALI:
    - Visualizzazione completa informazioni prodotto
    - Gestione stato attivo/inattivo 
    - Visualizzazione malfunzionamenti e soluzioni
    - Informazioni staff assegnato (solo visualizzazione)
    - Metriche di performance
    - Prodotti correlati
    - Debug info in modalità sviluppo
    
    MODIFICHE VERSIONE SEMPLIFICATA:
    - Rimossi pulsanti "Riassegna Staff" e "Rimuovi Assegnazione"
    - Rimosse tutte le modal per gestione staff
    - Mantenute tutte le altre funzionalità
--}}

{{-- EXTENDS: Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Imposta titolo dinamico nel browser
    Concatena stringa fissa con nome del prodotto
--}}
@section('title', 'Dettaglio Prodotto - ' . $prodotto->nome)

{{-- SECTION CONTENT: Inizio del contenuto principale della vista --}}
@section('content')

{{-- CONTAINER: Contenitore fluido per layout responsive --}}
<div class="container-fluid">
    
    {{-- ========== HEADER PRINCIPALE ========== --}}
    <div class="row mb-4">
        <div class="col-12">
            
            {{-- 
                HEADER CON TITOLO E AZIONI:
                Layout flex responsive che si adatta da colonna su mobile a riga su desktop
            --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                
                {{-- SEZIONE SINISTRA: Titolo e informazioni base --}}
                <div class="mb-3 mb-md-0">
                    {{-- 
                        TITOLO PRINCIPALE:
                        - h2 per dimensione ma classe h2 per controllo preciso
                        - Icona Bootstrap Icons
                        - Nome prodotto dinamico
                    --}}
                    <h1 class="h2 mb-2">
                        <i class="bi bi-box text-primary me-2"></i>
                        {{ $prodotto->nome }}
                        
                        {{-- 
                            BADGE STATO DINAMICO:
                            Condizione per mostrare badge rosso o verde basato su campo boolean 'attivo'
                        --}}
                        @if(!$prodotto->attivo)
                            <span class="badge bg-danger ms-2">INATTIVO</span>
                        @else
                            <span class="badge bg-success ms-2">ATTIVO</span>
                        @endif
                    </h1>
                    
                    {{-- 
                        INFORMAZIONI BASE:
                        Linea con dettagli separati da bullet points
                    --}}
                    <p class="text-muted mb-0">
                        <strong>Modello:</strong> {{ $prodotto->modello }} • 
                        <strong>Categoria:</strong> {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                        
                        {{-- 
                            PREZZO CONDIZIONALE:
                            Mostra prezzo solo se presente nel database
                            number_format(): Formattazione numero con separatori europei
                        --}}
                        @if($prodotto->prezzo)
                            • <strong>Prezzo:</strong> € {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                        @endif
                    </p>
                </div>

                {{-- 
                    SEZIONE DESTRA: Pulsanti di azione principale
                    btn-group: Raggruppa pulsanti con stile Bootstrap
                    flex-wrap: Permette il wrapping su schermi piccoli
                --}}
                <div class="btn-group flex-wrap" role="group" aria-label="Azioni prodotto">
                    
                    {{-- PULSANTE: Modifica prodotto --}}
                    {{-- 
                        ROUTE CON PARAMETRO: 
                        Laravel automaticamente usa l'ID del model $prodotto
                        title: Attributo per tooltip
                    --}}
                    <a href="{{ route('admin.prodotti.edit', $prodotto) }}" 
                       class="btn btn-warning"
                       title="Modifica informazioni prodotto">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </a>
                    
                    {{-- 
                        PULSANTE: Toggle stato attivo/inattivo
                        CONDIZIONE ROUTE: Verifica che la route esista prima di creare il form
                    --}}
                    @if(Route::has('admin.prodotti.toggle-status'))
                    {{-- 
                        FORM POST: 
                        - method="POST" per sicurezza (non GET)
                        - d-inline per stare nel btn-group
                        - onsubmit: Chiama funzione JavaScript per conferma
                    --}}
                    <form action="{{ route('admin.prodotti.toggle-status', $prodotto) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirmToggleStatus({{ $prodotto->attivo ? 'true' : 'false' }})">
                        {{-- CSRF TOKEN: Protezione Laravel contro attacchi CSRF --}}
                        @csrf
                        
                        {{-- 
                            PULSANTE DINAMICO:
                            - Colore e testo cambiano basandosi sullo stato attuale
                            - Operatore ternario per logica condizionale
                        --}}
                        <button type="submit" 
                                class="btn {{ $prodotto->attivo ? 'btn-danger' : 'btn-success' }}"
                                title="{{ $prodotto->attivo ? 'Disattiva prodotto' : 'Attiva prodotto' }}">
                            <i class="bi bi-{{ $prodotto->attivo ? 'pause' : 'play' }} me-1"></i>
                            {{ $prodotto->attivo ? 'Disattiva' : 'Attiva' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========== STATISTICHE RAPIDE ========== --}}
    {{-- 
        CONDIZIONE: Mostra statistiche solo se passate dal controller
        isset(): Controlla se la variabile esiste e non è null
    --}}
    @if(isset($statistiche))
    <div class="row mb-4">
        
        {{-- CARD STATISTICA: Malfunzionamenti totali --}}
        <div class="col-md-3 mb-3">
            {{-- 
                CARD CON SFONDO COLORATO:
                - bg-primary text-white: Sfondo blu con testo bianco
                - h-100: Altezza uniforme tra le card
            --}}
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    {{-- 
                        LAYOUT FLEX: 
                        justify-content-between: Separa numero e icona
                        align-items-center: Allinea verticalmente
                    --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            {{-- 
                                NUMERO PRINCIPALE: 
                                Valore dalla variabile $statistiche passata dal controller
                            --}}
                            <h4 class="card-title mb-1">{{ $statistiche['malfunzionamenti_totali'] }}</h4>
                            <p class="card-text mb-0">Malfunzionamenti Totali</p>
                        </div>
                        <div>
                            {{-- 
                                ICONA DECORATIVA:
                                - display-6: Dimensione grande
                                - opacity-75: Trasparenza al 75%
                            --}}
                            <i class="bi bi-exclamation-triangle display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- CARD STATISTICA: Problemi critici --}}
        <div class="col-md-3 mb-3">
            {{-- bg-danger: Sfondo rosso per problemi critici --}}
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $statistiche['malfunzionamenti_critici'] }}</h4>
                            <p class="card-text mb-0">Problemi Critici</p>
                        </div>
                        <div>
                            {{-- bi-exclamation-octagon: Icona ottagonale per criticità --}}
                            <i class="bi bi-exclamation-octagon display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- CARD STATISTICA: Segnalazioni totali --}}
        <div class="col-md-3 mb-3">
            {{-- bg-info: Sfondo azzurro per informazioni --}}
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $statistiche['segnalazioni_totali'] }}</h4>
                            <p class="card-text mb-0">Segnalazioni Totali</p>
                        </div>
                        <div>
                            {{-- bi-flag: Icona bandierina per segnalazioni --}}
                            <i class="bi bi-flag display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- CARD STATISTICA: Livello criticità --}}
        <div class="col-md-3 mb-3">
            {{-- 
                LOGICA PHP EMBEDDED:
                Determina il livello di criticità e il colore associato
                OPERATORE NULL COALESCING: ?? restituisce valore default se null
            --}}
            @php
                $criticita = $metriche['livello_criticita'] ?? ['livello' => 'N/A', 'colore' => 'secondary'];
            @endphp
            
            {{-- Colore dinamico basato sui dati --}}
            <div class="card bg-{{ $criticita['colore'] }} text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            {{-- 
                                FUNZIONE ucfirst(): 
                                Capitalizza la prima lettera della stringa
                            --}}
                            <h4 class="card-title mb-1">{{ ucfirst($criticita['livello']) }}</h4>
                            <p class="card-text mb-0">Livello Criticità</p>
                        </div>
                        <div>
                            {{-- bi-speedometer2: Icona tachimetro per livello --}}
                            <i class="bi bi-speedometer2 display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========== CONTENUTO PRINCIPALE CON LAYOUT A 2 COLONNE ========== --}}
    <div class="row">
        
        {{-- 
            ========== COLONNA PRINCIPALE (SINISTRA) ========== 
            col-lg-8: 8/12 colonne su schermi large e superiori
        --}}
        <div class="col-lg-8">
            
            {{-- ========== CARD INFORMAZIONI GENERALI ========== --}}
            <div class="card mb-4 shadow-sm">
                {{-- 
                    HEADER CARD:
                    bg-light: Sfondo grigio chiaro per differenziare
                --}}
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Informazioni Prodotto
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        
                        {{-- ========== SEZIONE FOTO PRODOTTO ========== --}}
                        {{-- 
                            CONDIZIONE: Mostra foto solo se presente
                            Campo 'foto' contiene il path relativo nel storage
                        --}}
                        @if($prodotto->foto)
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                {{-- 
                                    IMMAGINE PRODOTTO:
                                    - asset('storage/' . $prodotto->foto): URL completo del file
                                    - object-fit: cover: Taglia immagine per riempire contenitore
                                    - onerror: JavaScript fallback se immagine non carica
                                    - loading="lazy": Caricamento lazy per performance
                                --}}
                                <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                     alt="Foto {{ $prodotto->nome }}"
                                     class="img-fluid rounded shadow-sm product-image"
                                     style="max-height: 250px; object-fit: cover;"
                                     onerror="handleImageError(this)"
                                     loading="lazy">
                                     
                                {{-- Didascalia sotto l'immagine --}}
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-camera me-1"></i>Immagine prodotto
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        {{-- 
                            ========== TABELLA DETTAGLI PRODOTTO ========== 
                            Colonna che si adatta: 8/12 se c'è foto, 12/12 se non c'è
                            OPERATORE TERNARIO: {{ $prodotto->foto ? '8' : '12' }}
                        --}}
                        <div class="col-md-{{ $prodotto->foto ? '8' : '12' }}">
                            {{-- 
                                TABELLA SENZA BORDI:
                                table-borderless: Rimuove tutti i bordi
                                table-sm: Riduce padding per design compatto
                            --}}
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    {{-- RIGA: Nome prodotto --}}
                                    <tr>
                                        {{-- 
                                            CELLE CON LARGHEZZA FISSA:
                                            width="30%": TH occupa 30% della larghezza
                                            fw-semibold: Font semi-grassetto per valore
                                        --}}
                                        <th width="30%" class="text-muted">Nome:</th>
                                        <td class="fw-semibold">{{ $prodotto->nome }}</td>
                                    </tr>
                                    
                                    {{-- RIGA: Modello con funzione copia --}}
                                    <tr>
                                        <th class="text-muted">Modello:</th>
                                        <td>
                                            {{-- 
                                                ELEMENTO CODE:
                                                Stile monospazio per codici/modelli
                                                bg-light px-2 py-1 rounded: Sfondo e padding
                                            --}}
                                            <code class="bg-light px-2 py-1 rounded">{{ $prodotto->modello }}</code>
                                            
                                            {{-- 
                                                PULSANTE COPIA:
                                                onclick: Chiama funzione JavaScript
                                                btn-link: Stile link senza sfondo
                                                p-0: Rimuove padding
                                            --}}
                                            <button type="button" 
                                                    class="btn btn-link btn-sm p-0 ms-2" 
                                                    onclick="copyToClipboard('{{ $prodotto->modello }}')"
                                                    title="Copia modello">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    {{-- RIGA: Categoria con badge --}}
                                    <tr>
                                        <th class="text-muted">Categoria:</th>
                                        <td>
                                            {{-- 
                                                BADGE CATEGORIA:
                                                str_replace('_', ' ', $prodotto->categoria): Sostituisce underscore con spazi
                                                ucfirst(): Capitalizza prima lettera
                                                fs-6: Font size 6 (leggermente più grande del default badge)
                                            --}}
                                            <span class="badge bg-secondary fs-6">
                                                {{ ucfirst(str_replace('_', ' ', $prodotto->categoria)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- RIGA CONDIZIONALE: Prezzo (solo se presente) --}}
                                    @if($prodotto->prezzo)
                                    <tr>
                                        <th class="text-muted">Prezzo:</th>
                                        <td class="fw-bold text-success fs-5">
                                            {{-- 
                                                FORMATTAZIONE PREZZO:
                                                - number_format(): Formatta numero con separatori
                                                - 2 decimali, virgola per decimali, punto per migliaia
                                                - fw-bold: Font grassetto
                                                - text-success: Verde per indicare valore monetario
                                                - fs-5: Font size 5 (più grande)
                                            --}}
                                            € {{ number_format($prodotto->prezzo, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endif
                                    
                                    {{-- RIGA: Stato prodotto con badge dinamico --}}
                                    <tr>
                                        <th class="text-muted">Stato:</th>
                                        <td>
                                            {{-- 
                                                BADGE STATO DINAMICO:
                                                - Colore: verde se attivo, rosso se inattivo
                                                - Icona: check-circle o x-circle
                                                - Testo: ATTIVO o INATTIVO
                                            --}}
                                            <span class="badge bg-{{ $prodotto->attivo ? 'success' : 'danger' }} fs-6">
                                                <i class="bi bi-{{ $prodotto->attivo ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ $prodotto->attivo ? 'ATTIVO' : 'INATTIVO' }}
                                            </span>
                                        </td>
                                    </tr>
                                    
                                    {{-- RIGA: Data creazione --}}
                                    <tr>
                                        <th class="text-muted">Creato:</th>
                                        <td>
                                            {{-- 
                                                CARBON DATE FORMATTING:
                                                - format('d/m/Y H:i'): Formato italiano con ora
                                                - diffForHumans(): Formato "relativo" (es: "2 giorni fa")
                                            --}}
                                            {{ $prodotto->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">
                                                ({{ $prodotto->created_at->diffForHumans() }})
                                            </small>
                                        </td>
                                    </tr>
                                    
                                    {{-- RIGA: Data ultimo aggiornamento --}}
                                    <tr>
                                        <th class="text-muted">Aggiornato:</th>
                                        <td>
                                            {{ $prodotto->updated_at->format('d/m/Y H:i') }}
                                            {{-- 
                                                CONDIZIONE: Mostra diff solo se diverso da created_at
                                                Evita di mostrare "aggiornato" se il record non è mai stato modificato
                                            --}}
                                            @if($prodotto->updated_at != $prodotto->created_at)
                                                <small class="text-muted">
                                                    ({{ $prodotto->updated_at->diffForHumans() }})
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ========== SEZIONI TESTO DESCRITTIVO ========== --}}
                    
                    {{-- SEZIONE: Descrizione prodotto --}}
                    @if($prodotto->descrizione)
                    <div class="mt-4">
                        {{-- 
                            TITOLO SEZIONE:
                            h6: Dimensione appropriata per sottosezione
                            text-primary: Colore blu per evidenziare
                        --}}
                        <h6 class="text-primary">
                            <i class="bi bi-text-left me-1"></i>Descrizione:
                        </h6>
                        
                        {{-- 
                            CONTENITORE TESTO:
                            bg-light: Sfondo grigio chiaro
                            p-3: Padding di 3 unità
                            rounded: Angoli arrotondati
                        --}}
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $prodotto->descrizione }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- SEZIONE: Note tecniche --}}
                    @if($prodotto->note_tecniche)
                    <div class="mt-4">
                        <h6 class="text-warning">
                            <i class="bi bi-gear me-1"></i>Note Tecniche:
                        </h6>
                        
                        {{-- 
                            CONTENITORE CON BORDO COLORATO:
                            - bg-warning bg-opacity-10: Sfondo giallo molto trasparente
                            - border border-warning border-opacity-25: Bordo giallo semi-trasparente
                            - Colori warning per indicare informazioni tecniche importanti
                        --}}
                        <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 p-3 rounded">
                            {{-- 
                                FUNZIONE nl2br(e()):
                                - e(): Escaping HTML per sicurezza (equivalente a htmlspecialchars)
                                - nl2br(): Converte newline (\n) in tag <br>
                                - {!! !!}: Output non escaped per permettere tag <br>
                            --}}
                            {!! nl2br(e($prodotto->note_tecniche)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- SEZIONE: Modalità di installazione --}}
                    @if($prodotto->modalita_installazione)
                    <div class="mt-4">
                        <h6 class="text-info">
                            <i class="bi bi-tools me-1"></i>Modalità di Installazione:
                        </h6>
                        
                        {{-- Contenitore azzurro per info installazione --}}
                        <div class="bg-info bg-opacity-10 border border-info border-opacity-25 p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_installazione)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- SEZIONE: Modalità d'uso --}}
                    @if($prodotto->modalita_uso)
                    <div class="mt-4">
                        <h6 class="text-success">
                            <i class="bi bi-book me-1"></i>Modalità d'Uso:
                        </h6>
                        
                        {{-- Contenitore verde per istruzioni d'uso --}}
                        <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-3 rounded">
                            {!! nl2br(e($prodotto->modalita_uso)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ========== SEZIONE MALFUNZIONAMENTI ========== --}}
            {{-- 
                CONDIZIONE COMPLESSA:
                - Verifica che relazione malfunzionamenti esista
                - Verifica che la collection abbia elementi (count > 0)
                - OPERATORE && per AND logico
            --}}
            @if($prodotto->malfunzionamenti && $prodotto->malfunzionamenti->count() > 0)
            <div class="card shadow-sm">
                {{-- 
                    HEADER CON CONTATORE:
                    Layout flex per separare titolo e badge contatore
                --}}
                <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bug text-warning me-2"></i>Malfunzionamenti e Soluzioni
                    </h5>
                    
                    {{-- 
                        BADGE CONTATORE DINAMICO:
                        Mostra numero totale con plurale/singolare appropriato
                    --}}
                    <span class="badge bg-warning">
                        {{ $prodotto->malfunzionamenti->count() }} 
                        {{ $prodotto->malfunzionamenti->count() === 1 ? 'problema' : 'problemi' }}
                    </span>
                </div>
                
                <div class="card-body">
                    {{-- 
                        LOOP MALFUNZIONAMENTI:
                        @foreach itera attraverso collection Eloquent
                        $index e $malfunzionamento sono variabili automatiche
                    --}}
                    @foreach($prodotto->malfunzionamenti as $index => $malfunzionamento)
                    {{-- 
                        CONTENITORE SINGOLO MALFUNZIONAMENTO:
                        border-bottom condizionale: rimuove dal last element
                        $loop->last: Variabile Laravel che indica ultimo elemento
                    --}}
                    <div class="border rounded p-3 mb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        
                        {{-- ========== HEADER MALFUNZIONAMENTO ========== --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    {{-- 
                                        LOGICA COLORI GRAVITÀ:
                                        Array PHP embedded per mappare gravità a colori Bootstrap
                                    --}}
                                    @php
                                        $gravetaColors = [
                                            'critica' => 'danger',    // Rosso per critica
                                            'alta' => 'warning',      // Giallo per alta
                                            'media' => 'info',        // Azzurro per media
                                            'bassa' => 'secondary'    // Grigio per bassa
                                        ];
                                        // OPERATORE NULL COALESCING: Default 'secondary' se gravità non mappata
                                        $color = $gravetaColors[$malfunzionamento->gravita] ?? 'secondary';
                                    @endphp
                                    
                                    {{-- 
                                        BADGE GRAVITÀ CON ICONA DINAMICA:
                                        Icona cambia in base alla gravità
                                    --}}
                                    <span class="badge bg-{{ $color }} me-2">
                                        <i class="bi bi-{{ $malfunzionamento->gravita === 'critica' ? 'exclamation-triangle' : 'info-circle' }} me-1"></i>
                                        {{ ucfirst($malfunzionamento->gravita) }}
                                    </span>
                                    
                                    {{-- Descrizione del malfunzionamento --}}
                                    <span class="text-dark">{{ $malfunzionamento->descrizione }}</span>
                                </h6>
                            </div>
                            
                            {{-- COLONNA DESTRA: Metadati --}}
                            <div class="text-end">
                                {{-- Numero segnalazioni con plurale dinamico --}}
                                <small class="text-muted d-block">
                                    <i class="bi bi-flag me-1"></i>
                                    {{ $malfunzionamento->numero_segnalazioni }} 
                                    {{ $malfunzionamento->numero_segnalazioni === 1 ? 'segnalazione' : 'segnalazioni' }}
                                </small>
                                
                                {{-- 
                                    NUMERO PROGRESSIVO:
                                    $index parte da 0, quindi +1 per numerazione umana
                                --}}
                                <small class="text-muted">
                                    #{{ $index + 1 }}
                                </small>
                            </div>
                            </div>
                        
                        {{-- ========== SOLUZIONE TECNICA ========== --}}
                        {{-- CONDIZIONE: Mostra soluzione solo se presente --}}
                        @if($malfunzionamento->soluzione_tecnica)
                        <div class="mb-3">
                            {{-- HEADER SOLUZIONE con icona e colore verde --}}
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-tools text-success me-2"></i>
                                <strong class="text-success">Soluzione Tecnica:</strong>
                            </div>
                            
                            {{-- 
                                CONTENITORE SOLUZIONE:
                                Sfondo verde chiaro per evidenziare la soluzione
                            --}}
                            <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-3 rounded">
                                {{ $malfunzionamento->soluzione_tecnica }}
                            </div>
                        </div>
                        @endif

                        {{-- ========== INFORMAZIONI AGGIUNTIVE ========== --}}
                        {{-- 
                            LAYOUT GRID RESPONSIVE:
                            g-3: Gap di 3 unità tra le colonne
                            text-sm: Classe custom per testo piccolo
                        --}}
                        <div class="row g-3 text-sm">
                            
                            {{-- COLONNA: Tempo stimato riparazione --}}
                            @if($malfunzionamento->tempo_stimato)
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <strong>Tempo stimato:</strong> {{ $malfunzionamento->tempo_stimato }} minuti
                                </small>
                            </div>
                            @endif
                            
                            {{-- COLONNA: Livello difficoltà riparazione --}}
                            @if($malfunzionamento->difficolta)
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-bar-chart me-1"></i>
                                    <strong>Difficoltà:</strong> {{ ucfirst($malfunzionamento->difficolta) }}
                                </small>
                            </div>
                            @endif
                        </div>

                        {{-- ========== METADATI CREAZIONE/MODIFICA ========== --}}
                        {{-- 
                            SEZIONE SEPARATA:
                            mt-3 pt-2: Margine top e padding top
                            border-top: Linea separatrice sopra
                        --}}
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <strong>Creato:</strong> {{ $malfunzionamento->created_at->format('d/m/Y H:i') }}
                                
                                {{-- 
                                    RELAZIONE ELOQUENT CONDIZIONALE:
                                    creatoBy è probabilmente una relazione belongsTo
                                    al model User che ha creato il malfunzionamento
                                --}}
                                @if($malfunzionamento->creatoBy)
                                    da <strong>{{ $malfunzionamento->creatoBy->nome_completo }}</strong>
                                @endif
                                
                                {{-- 
                                    INFORMAZIONI AGGIORNAMENTO:
                                    Mostra solo se il record è stato effettivamente modificato
                                --}}
                                @if($malfunzionamento->updated_at != $malfunzionamento->created_at)
                                    <br>
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    <strong>Aggiornato:</strong> {{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}
                                    
                                    {{-- Utente che ha fatto l'ultima modifica --}}
                                    @if($malfunzionamento->modificatoBy)
                                        da <strong>{{ $malfunzionamento->modificatoBy->nome_completo }}</strong>
                                    @endif
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- ========== STATO ALTERNATIVO: NESSUN MALFUNZIONAMENTO ========== --}}
            @else
            {{-- 
                CARD STATO VUOTO:
                Mostra quando non ci sono malfunzionamenti registrati
            --}}
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    {{-- 
                        ICONA GRANDE POSITIVA:
                        display-1: Dimensione molto grande
                        text-success: Verde per stato positivo
                    --}}
                    <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                    <h5 class="text-success">Nessun malfunzionamento segnalato</h5>
                    <p class="text-muted">Questo prodotto non ha problemi noti al momento.</p>
                </div>
            </div>
            @endif
        </div>

        {{-- 
            ========== SIDEBAR DESTRA ========== 
            col-lg-4: 4/12 colonne su schermi large e superiori
        --}}
        <div class="col-lg-4">
            
            {{-- ========== CARD STAFF ASSEGNATO (SOLO VISUALIZZAZIONE) ========== --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>Staff Assegnato
                    </h6>
                </div>
                
                <div class="card-body">
                    {{-- 
                        CONDIZIONE: Verifica se c'è staff assegnato
                        staffAssegnato è una relazione Eloquent belongsTo
                    --}}
                    @if($prodotto->staffAssegnato)
                        {{-- ========== STAFF PRESENTE - SOLO VISUALIZZAZIONE ========== --}}
                        <div class="d-flex align-items-center">
                            {{-- 
                                AVATAR PLACEHOLDER:
                                Cerchio colorato con icona persona
                                style inline per dimensioni precise
                            --}}
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white fs-4"></i>
                            </div>
                            
                            {{-- INFORMAZIONI STAFF --}}
                            <div class="flex-grow-1">
                                {{-- 
                                    ACCESSOR NOME COMPLETO:
                                    nome_completo è probabilmente un accessor nel Model User
                                    che concatena nome e cognome
                                --}}
                                <h6 class="mb-1">{{ $prodotto->staffAssegnato->nome_completo }}</h6>
                                
                                {{-- Ruolo/livello staff --}}
                                <small class="text-muted">
                                    <i class="bi bi-briefcase me-1"></i>Staff Aziendale (Livello 3)
                                </small>
                                
                                {{-- 
                                    DATA ASSEGNAZIONE:
                                    Usa created_at del prodotto come proxy per data assegnazione
                                --}}
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Assegnato il: {{ $prodotto->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ========== NESSUNO STAFF ASSEGNATO ========== --}}
                        <div class="text-center py-4">
                            {{-- Icona grande per stato vuoto --}}
                            <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Nessuno staff assegnato</h6>
                            <p class="text-muted small mb-0">
                                Questo prodotto non ha un responsabile tecnico assegnato.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ========== CARD METRICHE PERFORMANCE ========== --}}
            {{-- CONDIZIONE: Mostra solo se metriche sono state calcolate --}}
            @if(isset($metriche))
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up text-info me-2"></i>Metriche Performance
                    </h6>
                </div>
                
                <div class="card-body">
                    {{-- 
                        LAYOUT GRID PER METRICHE:
                        Una riga per ogni metrica con layout justify-content-between
                    --}}
                    <div class="row g-3">
                        
                        {{-- METRICA: Giorni dal lancio prodotto --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-date me-1"></i>Giorni dal lancio:
                                </small>
                                <span class="badge bg-info">{{ $metriche['giorni_dal_lancio'] }}</span>
                            </div>
                        </div>
                        
                        {{-- METRICA: Media segnalazioni per malfunzionamento --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-bar-chart me-1"></i>Media segnalazioni:
                                </small>
                                <span class="badge bg-warning">{{ $metriche['media_segnalazioni_per_malfunzionamento'] }}</span>
                            </div>
                        </div>
                        
                        {{-- METRICA: Frequenza problemi con colore dinamico --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-speedometer me-1"></i>Frequenza problemi:
                                </small>
                                
                                {{-- 
                                    MATCH EXPRESSION (PHP 8):
                                    Sintassi moderna per mappare valori a colori
                                    Più pulita di if/else o array per mappature semplici
                                --}}
                                @php
                                    $frequenza = $metriche['frequenza_problemi'];
                                    $colorFreq = match($frequenza) {
                                        'Molto Alta' => 'danger',
                                        'Alta' => 'warning', 
                                        'Media' => 'info',
                                        'Bassa' => 'success',
                                        default => 'secondary'  // Fallback per valori non mappati
                                    };
                                @endphp
                                
                                <span class="badge bg-{{ $colorFreq }}">{{ $frequenza }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ========== CARD PRODOTTI CORRELATI ========== --}}
            {{-- 
                CONDIZIONE MULTIPLA:
                - Verifica che variabile esista
                - Verifica che collection abbia elementi
            --}}
            @if(isset($prodottiCorrelati) && $prodottiCorrelati->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-link-45deg text-secondary me-2"></i>Prodotti Correlati
                        {{-- Badge con conteggio --}}
                        <span class="badge bg-secondary ms-2">{{ $prodottiCorrelati->count() }}</span>
                    </h6>
                </div>
                
                {{-- 
                    BODY SENZA PADDING:
                    p-0 per permettere agli elementi interni di gestire il proprio padding
                --}}
                <div class="card-body p-0">
                    {{-- LOOP PRODOTTI CORRELATI --}}
                    @foreach($prodottiCorrelati as $correlato)
                    {{-- 
                        RIGA PRODOTTO CORRELATO:
                        border-bottom condizionale per separatori
                    --}}
                    <div class="d-flex align-items-center p-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        
                        {{-- ========== MINIATURA PRODOTTO ========== --}}
                        <div class="me-3">
                            @if($correlato->foto)
                                {{-- 
                                    IMMAGINE MINIATURA:
                                    - object-fit: cover per riempire contenitore
                                    - onerror: Fallback JavaScript se immagine non carica
                                    - Dimensioni fisse per layout uniforme
                                --}}
                                <img src="{{ asset('storage/' . $correlato->foto) }}" 
                                     alt="{{ $correlato->nome }}"
                                     class="rounded border"
                                     style="width: 40px; height: 40px; object-fit: cover;"
                                     onerror="this.src='{{ asset('images/placeholder-product.png') }}'; this.onerror=null;">
                            @else
                                {{-- 
                                    PLACEHOLDER ICONA:
                                    Quando non c'è immagine
                                --}}
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        {{-- ========== INFORMAZIONI PRODOTTO CORRELATO ========== --}}
                        <div class="flex-grow-1">
                            {{-- 
                                LINK AL PRODOTTO:
                                text-decoration-none: Rimuove sottolineatura
                            --}}
                            <a href="{{ route('admin.prodotti.show', $correlato) }}" 
                               class="text-decoration-none">
                                <div class="fw-semibold text-dark small">{{ $correlato->nome }}</div>
                            </a>
                            
                            {{-- 
                                CONTATORE PROBLEMI:
                                Mostra numero malfunzionamenti con plurale appropriato
                                OPERATORE NULL COALESCING: Default 0 se count non disponibile
                            --}}
                            <div class="text-muted small">
                                <i class="bi bi-bug me-1"></i>
                                {{ $correlato->malfunzionamenti_count ?? 0 }} 
                                {{ ($correlato->malfunzionamenti_count ?? 0) === 1 ? 'problema' : 'problemi' }}
                            </div>
                        </div>
                        
                        {{-- ========== LINK RAPIDO ========== --}}
                        <div>
                            {{-- Pulsante piccolo per navigazione rapida --}}
                            <a href="{{ route('admin.prodotti.show', $correlato) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- FINE CONTENUTO PRINCIPALE --}}
@endsection
{{-- ========================================= --}}
{{-- === STILI CSS PERSONALIZZATI === --}}
{{-- ========================================= --}}

{{-- 
    PUSH STYLES: Aggiunge CSS alla sezione 'styles' del layout principale
    Questi stili saranno inclusi nell'<head> della pagina
    LINGUAGGIO: CSS con sintassi standard
--}}
@push('styles')
<style>
/* 
    === STILI GENERALI CARD ===
    Definisce l'aspetto base delle card con transizioni fluide
*/

/* 
    CARD BASE:
    Rimuove bordo default Bootstrap e aggiunge ombra personalizzata
*/
.card {
    border: none; /* Rimuove bordo default Bootstrap */
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Ombra sottile */
    transition: all 0.2s ease-in-out; /* Transizione fluida per tutti i cambiamenti */
}

/* 
    HOVER EFFECT CARD:
    Effetto di sollevamento al passaggio del mouse
*/
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Ombra più pronunciata */
    transform: translateY(-2px); /* Solleva la card di 2px */
}

/* 
    HEADER CARD:
    Stile per intestazioni delle card
*/
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125); /* Bordo sottile sotto header */
    font-weight: 600; /* Testo semi-grassetto */
}

/* 
    === BADGE PERSONALIZZATI ===
    Stili per badge con dimensioni customizzate
*/

/* Badge standard con dimensioni e peso font ottimizzati */
.badge {
    font-size: 0.75em; /* Dimensione leggermente ridotta */
    font-weight: 500; /* Peso font medio */
}

/* Badge più grandi per maggiore visibilità */
.badge.fs-6 {
    font-size: 0.875rem !important; /* Override dimensione Bootstrap */
}

/* 
    === TABELLE SENZA BORDI ===
    Stili per tabelle informative pulite
*/

/* 
    INTESTAZIONI TABELLA:
    Colore e peso font per le celle di intestazione
*/
.table-borderless th {
    font-weight: 600; /* Grassetto per evidenziare */
    color: #6c757d; /* Grigio Bootstrap per testo secondario */
    font-size: 0.875rem; /* Dimensione leggermente ridotta */
}

/* 
    CELLE DATI TABELLA:
    Stile per celle contenenti i valori
*/
.table-borderless td {
    font-weight: 500; /* Semi-grassetto per leggibilità */
    color: #212529; /* Colore testo principale Bootstrap */
}

/* 
    === IMMAGINI PRODOTTO ===
    Effetti interattivi per immagini
*/

/* 
    IMMAGINE CON TRANSIZIONE:
    Effetto zoom al hover con cursore pointer
*/
.product-image {
    transition: transform 0.2s ease-in-out; /* Transizione fluida per trasformazione */
    cursor: pointer; /* Indica che l'elemento è cliccabile */
}

.product-image:hover {
    transform: scale(1.05); /* Ingrandisce del 5% al hover */
}

/* 
    === GRUPPI BOTTONI ===
    Spacing per pulsanti raggruppati
*/

/* Margini uniformi per pulsanti nel gruppo */
.btn-group .btn {
    margin: 0.125rem; /* Piccolo margine per separazione visiva */
}

/* 
    === ANIMAZIONI CARICAMENTO ===
    Stati di loading per operazioni asincrone
*/

/* 
    CLASSE LOADING:
    Stato disabilitato durante operazioni
*/
.loading {
    opacity: 0.6; /* Riduce opacità per indicare stato inattivo */
    pointer-events: none; /* Disabilita tutti i click */
}

/* 
    SPINNER LOADING:
    Animazione rotante sovrapposta all'elemento
*/
.loading::after {
    content: ""; /* Contenuto vuoto per pseudo-elemento */
    position: absolute; /* Posizionamento assoluto sopra l'elemento */
    top: 50%; /* Centro verticale */
    left: 50%; /* Centro orizzontale */
    width: 20px; /* Dimensioni spinner */
    height: 20px;
    margin: -10px 0 0 -10px; /* Centramento preciso (metà dimensioni) */
    border: 2px solid #f3f3f3; /* Bordo grigio chiaro */
    border-top: 2px solid #007bff; /* Bordo superiore blu per contrasto */
    border-radius: 50%; /* Forma circolare */
    animation: spin 1s linear infinite; /* Animazione rotazione continua */
}

/* 
    KEYFRAME ANIMAZIONE ROTAZIONE:
    Definisce l'animazione di rotazione completa
*/
@keyframes spin {
    0% { transform: rotate(0deg); } /* Inizio: nessuna rotazione */
    100% { transform: rotate(360deg); } /* Fine: rotazione completa */
}

/* 
    === RESPONSIVE DESIGN ===
    Adattamenti per dispositivi mobili e tablet
*/

/* 
    TABLET E MOBILE:
    Adattamenti per schermi medi (≤768px)
*/
@media (max-width: 768px) {
    /* Pulsanti in colonna su schermi piccoli */
    .btn-group {
        flex-direction: column; /* Cambia da riga a colonna */
        width: 100%; /* Larghezza completa */
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem; /* Margine inferiore tra pulsanti */
        width: 100%; /* Pulsanti a larghezza completa */
    }
    
    /* Margini per colonne su mobile */
    .card-body .row .col-md-8,
    .card-body .row .col-md-4 {
        margin-bottom: 1rem; /* Spazio tra colonne impilate */
    }
}

/* 
    SMARTPHONE:
    Adattamenti per schermi molto piccoli (≤576px)
*/
@media (max-width: 576px) {
    /* Riduce dimensione icone grandi */
    .display-6 {
        font-size: 2rem; /* Più piccole su mobile */
    }
    
    /* Riduce dimensione titoli */
    .h2 {
        font-size: 1.5rem; /* Titoli più compatti */
    }
}

/* 
    === STATI INTERATTIVI ===
    Feedback visivo per interazioni utente
*/

/* 
    FOCUS PULSANTI:
    Outline personalizzato per navigazione da tastiera
*/
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Alone blu per focus */
}

/* 
    FOCUS FORM ELEMENTS:
    Stile uniforme per elementi form in focus
*/
.form-select:focus,
.form-control:focus {
    border-color: #86b7fe; /* Bordo blu chiaro */
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); /* Alone blu */
}

/* 
    === UTILITÀ CUSTOM ===
    Classi helper personalizzate
*/

/* 
    TESTO TRONCATO SU DUE RIGHE:
    Utilizza webkit line clamp per limitare righe
*/
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Massimo 2 righe */
    -webkit-box-orient: vertical;
    overflow: hidden; /* Nasconde testo in eccesso */
}

/* 
    UTILITÀ OPACITÀ:
    Classi personalizzate per trasparenze Bootstrap
*/
.border-opacity-25 {
    --bs-border-opacity: 0.25; /* Bordo con 25% opacità */
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1; /* Sfondo con 10% opacità */
}

/* 
    === NOTIFICHE TOAST ===
    Stili per notifiche temporanee
*/

/* 
    CONTENITORE TOAST:
    Z-index alto per stare sopra altri elementi
*/
.toast-container {
    z-index: 9999; /* Priorità massima */
}

/* Dimensione minima per toast leggibili */
.toast {
    min-width: 300px;
}

/* 
    === ACCESSIBILITÀ ===
    Rispetto per preferenze utente
*/

/* 
    MOTION REDUCE:
    Disabilita animazioni per utenti che preferiscono meno movimento
*/
@media (prefers-reduced-motion: reduce) {
    .card,
    .product-image,
    .btn {
        transition: none; /* Rimuove tutte le transizioni */
    }
    
    .loading::after {
        animation: none; /* Ferma animazione spinner */
    }
}

/* 
    === MODALITÀ SCURA ===
    Supporto per tema scuro (se implementato)
*/

/* 
    DARK MODE:
    Adattamenti colore per preferenza sistema scuro
*/
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #2d3748; /* Sfondo card scuro */
        color: #e2e8f0; /* Testo chiaro */
    }
    
    .card-header {
        background-color: #4a5568; /* Header più scuro */
        border-color: #2d3748; /* Bordo coerente */
    }
    
    .table-borderless th {
        color: #a0aec0; /* Intestazioni tabella in grigio chiaro */
    }
    
    .text-muted {
        color: #718096 !important; /* Testo secondario più chiaro */
    }
}
</style>
@endpush

{{-- ========================================= --}}
{{-- === JAVASCRIPT FUNZIONALITÀ === --}}
{{-- ========================================= --}}

{{-- 
    PUSH SCRIPTS: Aggiunge JavaScript alla sezione 'scripts' del layout
    Questi script saranno inclusi prima della chiusura del tag </body>
    LINGUAGGIO: JavaScript embedded in Blade
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA:
    Crea oggetto globale JavaScript con dati PHP per uso client-side
    Pattern Singleton per evitare sovrascritture
*/

// Inizializza l'oggetto PageData se non esiste già
window.PageData = window.PageData || {};

/*
    TRASFERIMENTO DATI PHP → JAVASCRIPT:
    Utilizza il sistema di condizioni Blade per passare solo dati necessari
    @json(): Helper Blade che converte array/oggetti PHP in JSON sicuro
*/

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

/*
    PATTERN ESPANDIBILE:
    Questo pattern permette di aggiungere facilmente nuovi dati
    senza modificare la struttura esistente
*/
// Aggiungi altri dati che potrebbero servire...
</script>
@endpush
{{-- ========================================= --}}
{{-- === SEZIONE DEBUG INFORMAZIONI === --}}
{{-- ========================================= --}}

{{-- 
    SEZIONE DEBUG CONDIZIONALE:
    Mostra informazioni di debug solo in modalità sviluppo
    
    CONDIZIONI MULTIPLE:
    1. config('app.debug'): Verifica che app sia in debug mode
    2. request()->get('debug'): Verifica parametro GET ?debug=1
    3. OPERATORE &&: Entrambe devono essere true
    
    SCOPO: Aiutare sviluppatori a debuggare problemi senza esporre info in produzione
--}}
@if(config('app.debug') && request()->get('debug'))
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            {{-- 
                CARD DEBUG CON BORDO AVVERTIMENTO:
                border-warning: Bordo giallo Bootstrap per indicare attenzione
            --}}
            <div class="card border-warning">
                {{-- 
                    HEADER DEBUG:
                    bg-warning bg-opacity-25: Sfondo giallo semi-trasparente
                --}}
                <div class="card-header bg-warning bg-opacity-25">
                    <h6 class="mb-0">
                        <i class="bi bi-bug text-warning me-2"></i>
                        Debug Information - Versione Semplificata
                    </h6>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        
                        {{-- ========== COLONNA SINISTRA: INFORMAZIONI PRODOTTO ========== --}}
                        <div class="col-md-6">
                            <h6>Informazioni Prodotto:</h6>
                            
                            {{-- 
                                TABELLA DEBUG:
                                table-sm: Tabella compatta
                                table-borderless: Senza bordi per semplicità
                            --}}
                            <table class="table table-sm table-borderless">
                                {{-- Informazioni principali del prodotto --}}
                                <tr><th>ID:</th><td>{{ $prodotto->id }}</td></tr>
                                <tr><th>Nome:</th><td>{{ $prodotto->nome }}</td></tr>
                                <tr><th>Modello:</th><td>{{ $prodotto->modello }}</td></tr>
                                <tr><th>Attivo:</th><td>{{ $prodotto->attivo ? 'Sì' : 'No' }}</td></tr>
                                
                                {{-- 
                                    FOREIGN KEY: staff_assegnato_id
                                    OPERATORE NULL COALESCING: ?? 'NULL' se campo è null
                                --}}
                                <tr><th>Staff ID:</th><td>{{ $prodotto->staff_assegnato_id ?? 'NULL' }}</td></tr>
                                
                                {{-- 
                                    RELAZIONE STAFF:
                                    Tenta di accedere a relazione e accessor
                                    Se fallisce, mostra 'N/A'
                                --}}
                                <tr><th>Staff Nome:</th><td>{{ $prodotto->staffAssegnato->nome_completo ?? 'N/A' }}</td></tr>
                                
                                {{-- 
                                    CONTEGGIO MALFUNZIONAMENTI:
                                    Operatore ternario con verifica esistenza relazione
                                --}}
                                <tr><th>Malfunzionamenti:</th><td>{{ $prodotto->malfunzionamenti ? $prodotto->malfunzionamenti->count() : 0 }}</td></tr>
                            </table>
                        </div>
                        
                        {{-- ========== COLONNA DESTRA: STATO FUNZIONALITÀ ========== --}}
                        <div class="col-md-6">
                            <h6>Funzionalità Attive:</h6>
                            
                            {{-- 
                                LISTA FUNZIONALITÀ:
                                list-unstyled: Rimuove bullets
                                Icone colorate per indicare stato on/off
                            --}}
                            <ul class="list-unstyled small">
                                <li><i class="bi bi-check-circle text-success"></i> Visualizzazione prodotto</li>
                                <li><i class="bi bi-check-circle text-success"></i> Toggle stato attivo/inattivo</li>
                                <li><i class="bi bi-check-circle text-success"></i> Vista pubblica</li>
                                <li><i class="bi bi-check-circle text-success"></i> Modifica prodotto</li>
                                
                                {{-- Funzionalità rimosse nella versione semplificata --}}
                                <li><i class="bi bi-x-circle text-danger"></i> Riassegnazione staff (rimossa)</li>
                                <li><i class="bi bi-x-circle text-danger"></i> Rimozione assegnazione (rimossa)</li>
                            </ul>
                            
                            <h6 class="mt-3">Route Disponibili:</h6>
                            
                            {{-- 
                                VERIFICA ROUTE ESISTENTI:
                                Route::has(): Laravel helper per verificare esistenza route
                                Icone dinamiche basate su esistenza route
                            --}}
                            <ul class="list-unstyled small">
                                <li>
                                    <i class="bi bi-{{ Route::has('admin.prodotti.show') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> 
                                    admin.prodotti.show
                                </li>
                                <li>
                                    <i class="bi bi-{{ Route::has('admin.prodotti.edit') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> 
                                    admin.prodotti.edit
                                </li>
                                <li>
                                    <i class="bi bi-{{ Route::has('admin.prodotti.toggle-status') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> 
                                    admin.prodotti.toggle-status
                                </li>
                                <li>
                                    <i class="bi bi-{{ Route::has('prodotti.show') ? 'check-circle text-success' : 'x-circle text-danger' }}"></i> 
                                    prodotti.show
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- ========== DEBUG STATISTICHE ========== --}}
                    {{-- Mostra dump JSON delle statistiche se disponibili --}}
                    @if(isset($statistiche))
                    <h6 class="mt-3">Statistiche Debug:</h6>
                    {{-- 
                        PRE FORMATTED JSON:
                        json_encode(): Converte array PHP in JSON
                        JSON_PRETTY_PRINT: Flag per formattazione leggibile
                        bg-light: Sfondo grigio per distinguere codice
                    --}}
                    <pre class="bg-light p-3 rounded small">{{ json_encode($statistiche, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    
                    {{-- ========== DEBUG METRICHE ========== --}}
                    {{-- Mostra dump JSON delle metriche se disponibili --}}
                    @if(isset($metriche))
                    <h6 class="mt-3">Metriche Debug:</h6>
                    <pre class="bg-light p-3 rounded small">{{ json_encode($metriche, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 
    =========================================
    === RIEPILOGO FUNZIONALITÀ FILE ===
    =========================================
    
    QUESTO FILE IMPLEMENTA:
    
    1. VISUALIZZAZIONE PRODOTTO:
       - Header dinamico con titolo e stato
       - Informazioni complete in formato tabellare
       - Foto prodotto con fallback
       - Sezioni testo con formattazione sicura
    
    2. GESTIONE AMMINISTRATIVA:
       - Pulsanti per modifica prodotto
       - Toggle stato attivo/inattivo con conferma
       - Controlli di sicurezza CSRF
    
    3. MALFUNZIONAMENTI:
       - Lista completa con dettagli
       - Codifica colori per gravità
       - Soluzioni tecniche formattate
       - Metadati creazione/modifica
    
    4. SIDEBAR INFORMATIVA:
       - Staff assegnato (solo visualizzazione)
       - Metriche performance calcolate
       - Prodotti correlati con link
    
    5. DEBUG AVANZATO:
       - Informazioni tecniche dettagliate
       - Verifica stato route
       - Dump dati JSON per troubleshooting
    
    6. SICUREZZA:
       - Protezione CSRF per form POST
       - Escape HTML per output sicuro
       - Verifiche condizionali per dati opzionali
    
    7. UX/UI:
       - Design responsive Bootstrap 5
       - Animazioni e transizioni fluide
       - Feedback visivo per interazioni
       - Supporto accessibilità e dark mode
    
    8. PERFORMANCE:
       - Lazy loading immagini
       - Riduzione motion per utenti sensibili
       - Ottimizzazioni JavaScript minime
    
    PATTERN TECNICI UTILIZZATI:
    - Blade Template Engine (@extends, @section, @if, @foreach)
    - Eloquent ORM Relations (belongsTo, hasMany)
    - Laravel Route Model Binding
    - Carbon Date Manipulation
    - Bootstrap 5 CSS Framework
    - Progressive Enhancement JavaScript
    - Mobile-First Responsive Design
--}}