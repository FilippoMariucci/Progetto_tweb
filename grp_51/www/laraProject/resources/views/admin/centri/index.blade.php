{{-- 
    Vista Gestione Centri Assistenza - Admin - ORDINAMENTO CORRETTO
    File: resources/views/admin/centri/index.blade.php
    Linguaggio: Blade Template (Laravel) + HTML + CSS + JavaScript
    
    FUNZIONALITÀ IMPLEMENTATE:
    - Lista completa centri assistenza con paginazione
    - Ordinamento per nome, provincia, numero tecnici
    - Filtri di ricerca per nome, provincia, città
    - Statistiche aggregate (totali, tecnici, province)
    - Gestione CRUD completa (visualizza, modifica, elimina)
    - Distribuzione geografica dei centri
    
    LIVELLO ACCESSO: Solo Amministratore (Livello 4)
--}}

{{-- 
    EXTENDS: Estende il layout principale dell'applicazione Laravel 
    Il layout app.blade.php contiene la struttura base (header, nav, footer)
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Definisce il titolo della pagina mostrato nel browser
    Utilizzato dal layout principale per impostare <title>
--}}
@section('title', 'Gestione Centri Assistenza - Admin')

{{-- 
    SECTION CONTENT: Contenuto principale della pagina
    Tutto il codice HTML della vista viene inserito qui
--}}
@section('content')
<div class="container-fluid mt-4">
    {{-- 
        SEZIONE HEADER PAGINA
        Contiene titolo, descrizione e pulsanti azioni principali
        - Titolo con icona Bootstrap Icons
        - Badge per indicare funzionalità opzionale
        - Pulsanti navigazione verso creazione e dashboard
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{-- Titolo principale con icona geo-alt (posizione) --}}
                    <h1 class="h2 mb-1">
                        <i class="bi bi-geo-alt text-info me-2"></i>
                        Gestione Centri Assistenza
                    </h1>
                    {{-- Sottotitolo esplicativo con badge funzionalità opzionale --}}
                    <p class="text-muted mb-0">
                        Amministra i centri di assistenza tecnica sul territorio
                        <span class="badge bg-info ms-2">Funzionalità Opzionale</span>
                    </p>
                </div>
                <div>
                    {{-- Pulsante creazione nuovo centro (route admin.centri.create) --}}
                    <a href="{{ route('admin.centri.create') }}" class="btn btn-info me-2">
                        <i class="bi bi-plus-circle me-1"></i>Nuovo Centro
                    </a>
                    {{-- Pulsante ritorno alla dashboard admin --}}
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE MESSAGGI FLASH
        Visualizza messaggi di successo o errore dalle sessioni Laravel
        - session('success'): messaggio verde di operazione completata
        - session('error'): messaggio rosso di errore
        - alert-dismissible: permette chiusura manuale
    --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 
        SEZIONE FILTRI E RICERCA
        Form GET per filtrare la lista dei centri
        - Ricerca per nome centro (campo text)
        - Filtro per provincia (select con opzioni)
        - Filtro per città (campo text)
        - Pulsanti Filtra e Reset
    --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Filtri e Ricerca
            </h5>
        </div>
        <div class="card-body">
            {{-- Form GET mantiene i filtri nell'URL per condivisione link --}}
            <form method="GET" action="{{ route('admin.centri.index') }}" class="row g-3">
                {{-- Campo ricerca nome centro --}}
                <div class="col-md-4">
                    <label for="search" class="form-label">Ricerca Centro</label>
                    {{-- request('search') mantiene valore dopo submit --}}
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome centro...">
                </div>
                
                {{-- Filtro dropdown province --}}
                <div class="col-md-3">
                    <label for="provincia" class="form-label">Provincia</label>
                    <select class="form-select" id="provincia" name="provincia">
                        <option value="">Tutte le province</option>
                        {{-- 
                            @if verifica se variabile $province esiste e non è vuota
                            isset() e count() per evitare errori se non definita
                        --}}
                        @if(isset($province) && count($province) > 0)
                            @foreach($province as $prov)
                                {{-- selected mantiene selezione dopo filtro --}}
                                <option value="{{ $prov }}" {{ request('provincia') == $prov ? 'selected' : '' }}>
                                    {{ strtoupper($prov) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                {{-- Campo ricerca città --}}
                <div class="col-md-3">
                    <label for="citta" class="form-label">Città</label>
                    <input type="text" class="form-control" id="citta" name="citta" 
                           value="{{ request('citta') }}" placeholder="Nome città...">
                </div>
                
                {{-- Pulsanti azione filtro --}}
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        {{-- Submit form per applicare filtri --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filtra
                        </button>
                        {{-- Link reset ricarica pagina senza parametri --}}
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 
        SEZIONE STATISTICHE CENTRI
        4 card con metriche aggregate calcolate dal controller
        - $centri->total(): totale centri con paginazione
        - $centri->sum(): somma colonna tecnici_count
        - Collection methods per conteggi specifici
    --}}
    <div class="row g-3 mb-4">
        {{-- Card Centri Totali --}}
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-building display-4 text-info mb-2"></i>
                    {{-- ?? 0 operatore null coalescing per default 0 --}}
                    <h3 class="mb-1">{{ $centri->total() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Centri Totali</p>
                </div>
            </div>
        </div>
        
        {{-- Card Tecnici Totali --}}
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people display-4 text-success mb-2"></i>
                    {{-- sum() calcola totale tecnici da relazione --}}
                    <h3 class="mb-1">
                        {{ $centri->sum('tecnici_count') ?? 0 }}
                    </h3>
                    <p class="text-muted mb-0">Tecnici Totali</p>
                </div>
            </div>
        </div>
        
        {{-- Card Centri con Tecnici --}}
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-check-circle display-4 text-primary mb-2"></i>
                    {{-- where() filtra centri con tecnici > 0 --}}
                    <h3 class="mb-1">
                        {{ $centri->where('tecnici_count', '>', 0)->count() }}
                    </h3>
                    <p class="text-muted mb-0">Con Tecnici</p>
                </div>
            </div>
        </div>
        
        {{-- Card Province Coperte --}}
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-map display-4 text-warning mb-2"></i>
                    {{-- pluck()->unique()->count() per province distinte --}}
                    <h3 class="mb-1">
                        {{ $centri->pluck('provincia')->unique()->count() }}
                    </h3>
                    <p class="text-muted mb-0">Province Coperte</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE LISTA CENTRI CON ORDINAMENTO
        Tabella principale con ordinamento funzionante
        - Header con link di ordinamento
        - Pulsanti ordinamento che mantengono filtri
        - request()->fullUrlWithQuery() per mantenere parametri esistenti
    --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list me-2"></i>
                Centri Assistenza
                {{-- Badge con count totale se presente --}}
                @if($centri->total() > 0)
                    <span class="badge bg-info">{{ $centri->total() }}</span>
                @endif
            </h5>
            
            {{-- 
                PULSANTI ORDINAMENTO CORRETTI
                fullUrlWithQuery() mantiene filtri esistenti e modifica sort/order
                Logica: se già ordinato ASC su campo, cambia a DESC e viceversa
            --}}
            <div class="btn-group" role="group">
                {{-- Ordinamento per Nome --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'nome', 
                    'order' => (request('sort') == 'nome' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'nome' ? 'active' : '' }}">
                    <i class="bi bi-sort-alpha-{{ (request('sort') == 'nome' && request('order') == 'desc') ? 'up' : 'down' }} me-1"></i>
                    Nome
                    {{-- Freccia direzione ordinamento se attivo --}}
                    @if(request('sort') == 'nome')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
                
                {{-- Ordinamento per Provincia --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'provincia', 
                    'order' => (request('sort') == 'provincia' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'provincia' ? 'active' : '' }}">
                    <i class="bi bi-geo me-1"></i>
                    Provincia
                    @if(request('sort') == 'provincia')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
                
                {{-- Ordinamento per Tecnici --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'sort' => 'tecnici', 
                    'order' => (request('sort') == 'tecnici' && request('order') == 'asc') ? 'desc' : 'asc'
                ]) }}" 
                   class="btn btn-outline-secondary btn-sm {{ request('sort') == 'tecnici' ? 'active' : '' }}">
                    <i class="bi bi-people me-1"></i>
                    Tecnici
                    @if(request('sort') == 'tecnici')
                        <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                    @endif
                </a>
            </div>
        </div>
        
        <div class="card-body">
            {{-- 
                CONDIZIONE: Mostra tabella solo se ci sono centri
                $centri->count() > 0 verifica presenza dati
            --}}
            @if($centri->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        {{-- Header tabella cliccabile per ordinamento --}}
                        <thead class="table-light">
                            <tr>
                                {{-- 
                                    HEADER CLICCABILI PER ORDINAMENTO
                                    Stessa logica pulsanti: mantiene filtri, cambia ordinamento
                                --}}
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'nome', 
                                        'order' => (request('sort') == 'nome' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Centro
                                        @if(request('sort') == 'nome')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'provincia', 
                                        'order' => (request('sort') == 'provincia' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Località
                                        @if(request('sort') == 'provincia')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Contatti</th>
                                <th class="text-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'tecnici', 
                                        'order' => (request('sort') == 'tecnici' && request('order') == 'asc') ? 'desc' : 'asc'
                                    ]) }}" class="text-decoration-none text-dark fw-bold">
                                        Tecnici
                                        @if(request('sort') == 'tecnici')
                                            <i class="bi bi-arrow-{{ request('order') == 'desc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center">Stato</th>
                                <th width="150">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 
                                FOREACH CENTRI: Loop attraverso tutti i centri della pagina corrente
                                $centro rappresenta ogni singolo centro nell'iterazione
                            --}}
                            @foreach($centri as $centro)
                                <tr>
                                    {{-- 
                                        COLONNA NOME CENTRO
                                        - Nome principale con styling
                                        - Badge attivo se ha tecnici
                                        - Indirizzo come sottotesto
                                    --}}
                                    <td>
                                        <div>
                                            <strong class="text-primary">{{ $centro->nome }}</strong>
                                            {{-- Badge verde se centro ha tecnici --}}
                                            @if($centro->tecnici_count > 0)
                                                <span class="badge bg-success badge-sm ms-2">Attivo</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $centro->indirizzo }}
                                            </small>
                                        </div>
                                    </td>
                                    
                                    {{-- 
                                        COLONNA LOCALITÀ
                                        - Città in grassetto
                                        - Provincia come badge
                                        - CAP come info aggiuntiva
                                    --}}
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $centro->citta }}</span>
                                            {{-- Mostra provincia solo se presente --}}
                                            @if($centro->provincia)
                                                <br>
                                                <span class="badge bg-info text-white">
                                                    {{ strtoupper($centro->provincia) }}
                                                </span>
                                            @endif
                                            {{-- Mostra CAP solo se presente --}}
                                            @if($centro->cap)
                                                <br>
                                                <small class="text-muted">CAP: {{ $centro->cap }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- 
                                        COLONNA CONTATTI
                                        - Telefono cliccabile (tel: link)
                                        - Email cliccabile (mailto: link)
                                        - Testo limitato con Str::limit()
                                    --}}
                                    <td>
                                        {{-- Link telefono se presente --}}
                                        @if($centro->telefono)
                                            <div class="mb-1">
                                                <i class="bi bi-telephone me-1 text-primary"></i>
                                                <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                                    {{ $centro->telefono }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        {{-- Link email se presente --}}
                                        @if($centro->email)
                                            <div>
                                                <i class="bi bi-envelope me-1 text-info"></i>
                                                <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                                    {{-- Str::limit() tronca testo lungo --}}
                                                    {{ Str::limit($centro->email, 25) }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        {{-- Messaggio se nessun contatto --}}
                                        @if(!$centro->telefono && !$centro->email)
                                            <span class="text-muted">Non disponibili</span>
                                        @endif
                                    </td>
                                    
                                    {{-- 
                                        COLONNA TECNICI MIGLIORATA
                                        - Badge con numero tecnici
                                        - Stato operativo/in attesa
                                        - Lista collapse con dettagli tecnici
                                    --}}
                                    <td class="text-center">
                                        @if($centro->tecnici_count > 0)
                                            <div>
                                                {{-- Badge verde con count tecnici --}}
                                                <span class="badge bg-success fs-5 px-3 py-2">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $centro->tecnici_count }}
                                                </span>
                                                <br>
                                                <small class="text-success fw-bold">Centro Operativo</small>
                                            </div>
                                        @else
                                            <div>
                                                {{-- Badge giallo se nessun tecnico --}}
                                                <span class="badge bg-warning text-dark fs-5 px-3 py-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    0
                                                </span>
                                                <br>
                                                <small class="text-warning fw-bold">Senza Tecnici</small>
                                            </div>
                                        @endif
                                        
                                        {{-- 
                                            LISTA TECNICI COLLAPSE
                                            Se centro ha tecnici, mostra pulsante dettagli
                                            Bootstrap collapse per lista tecnici espandibile
                                        --}}
                                        @if($centro->tecnici_count > 0 && $centro->tecnici->count() > 0)
                                            <div class="mt-2">
                                                {{-- Pulsante toggle collapse Bootstrap --}}
                                                <button class="btn btn-outline-info btn-xs" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#tecnici-{{ $centro->id }}">
                                                    <i class="bi bi-list me-1"></i>Dettagli
                                                </button>
                                                {{-- Area collapse nascosta di default --}}
                                                <div class="collapse mt-2" id="tecnici-{{ $centro->id }}">
                                                    <div class="card card-body p-2">
                                                        {{-- Loop tecnici del centro --}}
                                                        @foreach($centro->tecnici as $tecnico)
                                                            <div class="small">
                                                                <i class="bi bi-person me-1"></i>
                                                                {{ $tecnico->nome }} {{ $tecnico->cognome }}
                                                                {{-- Specializzazione se presente --}}
                                                                @if($tecnico->specializzazione)
                                                                    <br><span class="text-muted">{{ $tecnico->specializzazione }}</span>
                                                                @endif
                                                            </div>
                                                            {{-- HR divisore se non ultimo --}}
                                                            @if(!$loop->last)<hr class="my-1">@endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    {{-- 
                                        COLONNA STATO CENTRO
                                        Badge colorato basato su presenza tecnici
                                    --}}
                                    <td class="text-center">
                                        @if($centro->tecnici_count > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Operativo
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle me-1"></i>In Attesa
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- 
                                        COLONNA AZIONI CRUD
                                        - Visualizza: apre in nuova tab
                                        - Modifica: route edit
                                        - Elimina: form POST con method DELETE
                                    --}}
                                    <td>
                                        <div class="btn-group" role="group">
                                            {{-- Pulsante Visualizza (target _blank = nuova tab) --}}
                                            <a href="{{ route('admin.centri.show', $centro->id) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Visualizza dettagli centro" 
                                               target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            {{-- Pulsante Modifica --}}
                                            <a href="{{ route('admin.centri.edit', $centro->id) }}" 
                                               class="btn btn-outline-warning btn-sm" 
                                               title="Modifica centro">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            {{-- 
                                                FORM ELIMINAZIONE DIRETTA
                                                - Form POST con @method('DELETE') per route destroy
                                                - onsubmit con confirm JavaScript nativo
                                                - @csrf per protezione Laravel
                                            --}}
                                            <form action="{{ route('admin.centri.destroy', $centro) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Sei sicuro di voler eliminare il centro \"{{ $centro->nome }}\"?\n\nQuesta azione eliminerà anche i riferimenti ai tecnici associati e non può essere annullata.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        title="Elimina centro">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- 
                    PAGINAZIONE LARAVEL
                    Mostra controlli paginazione solo se necessario
                    appends() mantiene parametri query (filtri, ordinamento)
                --}}
                @if($centri->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            {{-- Info risultati correnti --}}
                            <small class="text-muted">
                                Visualizzati {{ $centri->firstItem() }}-{{ $centri->lastItem() }} 
                                di {{ $centri->total() }} centri
                            </small>
                        </div>
                        <div>
                            {{-- Links paginazione con query params --}}
                            {{ $centri->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
                
            @else
                {{-- 
                    STATO VUOTO - NESSUN CENTRO TROVATO
                    Due casi: filtri attivi o database vuoto
                --}}
                <div class="text-center py-5">
                    <i class="bi bi-building display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Nessun Centro Trovato</h4>
                    {{-- 
                        request()->hasAny() verifica se ci sono filtri attivi
                        Mostra messaggio e azioni diverse per caso con/senza filtri
                    --}}
                    @if(request()->hasAny(['search', 'provincia', 'citta']))
                        <p class="text-muted mb-3">
                            Nessun centro corrisponde ai filtri selezionati.
                        </p>
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Rimuovi Filtri
                        </a>
                    @else
                        <p class="text-muted mb-3">
                            Non ci sono centri di assistenza nel database.
                        </p>
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-info">
                            <i class="bi bi-plus-circle me-1"></i>Crea il Primo Centro
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- 
        SEZIONE DISTRIBUZIONE GEOGRAFICA (opzionale)
        Mostrata solo se ci sono centri nel database
        - Tabella raggruppamento per provincia
        - Pulsanti azioni rapide
    --}}
    @if($centri->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-map me-2"></i>
                            Distribuzione Geografica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Colonna distribuzione per provincia --}}
                            <div class="col-md-6">
                                <h6>Per Provincia</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Provincia</th>
                                                <th class="text-center">Centri</th>
                                                <th class="text-center">Tecnici</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- 
                                                RAGGRUPPAMENTO PER PROVINCIA
                                                groupBy() crea collection raggruppata per campo provincia
                                                Ogni gruppo contiene tutti i centri della stessa provincia
                                            --}}
                                            @php
                                                $byProvincia = $centri->groupBy('provincia');
                                            @endphp
                                            @foreach($byProvincia as $provincia => $centri_prov)
                                                <tr>
                                                    <td>
                                                        <strong>{{ strtoupper($provincia) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        {{-- count() dei centri nel gruppo --}}
                                                        <span class="badge bg-info">{{ $centri_prov->count() }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        {{-- sum() dei tecnici nel gruppo --}}
                                                        <span class="badge bg-success">{{ $centri_prov->sum('tecnici_count') }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            {{-- Colonna azioni rapide --}}
                            <div class="col-md-6">
                                <h6>Azioni Rapide</h6>
                                <div class="d-grid gap-2">
                                    {{-- Link vista pubblica centri (nuova tab) --}}
                                    <a href="{{ route('centri.index') }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye me-1"></i>Visualizza Lista Pubblica
                                    </a>
                                    
                                    {{-- Pulsante export (JavaScript function) --}}
                                    <button class="btn btn-outline-info" onclick="exportCentri()">
                                        <i class="bi bi-download me-1"></i>Esporta Lista Centri
                                    </button>
                                    
                                    {{-- Link creazione nuovo centro --}}
                                    <a href="{{ route('admin.centri.create') }}" class="btn btn-info">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi Nuovo Centro
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- 
    MODAL ELIMINAZIONE (non utilizzato in questa versione)
    Mantenuto per compatibilità futura se si vuole cambiare da confirm() a modal Bootstrap
--}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Conferma Eliminazione
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare il centro assistenza:</p>
                <p class="fw-bold text-danger" id="centro-name">Nome centro</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione:</strong> Questa azione eliminerà anche i riferimenti 
                    ai tecnici associati a questo centro e non può essere annullata.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annulla
                </button>
                <form id="delete-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina Centro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    PUSH SCRIPTS: Aggiunge JavaScript alla fine del layout
    window.PageData per passare dati PHP a JavaScript
    Inizializza variabili globali utilizzabili da script esterni
--}}
@push('scripts')
<script>
/**
 * INIZIALIZZAZIONE DATI PAGINA - JavaScript
 * Crea oggetto globale window.PageData per condividere dati tra PHP e JS
 * Utilizzato da script esterni per accedere ai dati della vista
 */
window.PageData = window.PageData || {};

/**
 * CONDIZIONI PHP IN JAVASCRIPT
 * isset() verifica esistenza variabile PHP prima di passarla a JS
 * @json converte oggetti PHP in formato JavaScript
 * Ogni variabile viene aggiunta solo se definita nel controller
 */

// Dati prodotto singolo (se presente)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Lista prodotti (se presente)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Dati malfunzionamento singolo (se presente)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti (se presente)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati centro singolo (se presente)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista centri (sempre presente in questa vista)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti (se presente)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members (se presente)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche (se presente)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente corrente (se presente)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/**
 * FUNZIONE EXPORT CENTRI - JavaScript globale
 * Funzione chiamata dal pulsante "Esporta Lista Centri"
 * Crea e scarica file CSV con dati dei centri
 */
function exportCentri() {
    console.log('🔽 Avvio export centri assistenza');
    
    // Verifica disponibilità dati
    if (!window.PageData || !window.PageData.centri) {
        alert('Nessun dato disponibile per l\'export');
        return;
    }
    
    try {
        // Header CSV
        const csvHeader = 'Nome,Indirizzo,Citta,Provincia,CAP,Telefono,Email,Tecnici\n';
        
        // Converti dati centri in righe CSV
        const csvRows = window.PageData.centri.data.map(centro => {
            return [
                `"${centro.nome || ''}"`,
                `"${centro.indirizzo || ''}"`,
                `"${centro.citta || ''}"`,
                `"${centro.provincia || ''}"`,
                `"${centro.cap || ''}"`,
                `"${centro.telefono || ''}"`,
                `"${centro.email || ''}"`,
                `"${centro.tecnici_count || 0}"`
            ].join(',');
        }).join('\n');
        
        // Crea contenuto CSV completo
        const csvContent = csvHeader + csvRows;
        
        // Crea blob per download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        
        // Crea link download temporaneo
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `centri_assistenza_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        
        // Aggiungi al DOM, clicca e rimuovi
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('✅ Export completato con successo');
        
    } catch (error) {
        console.error('❌ Errore durante export:', error);
        alert('Errore durante l\'esportazione dei dati');
    }
}

/**
 * FUNZIONE CONFIRM DELETE - JavaScript globale
 * Funzione per conferma eliminazione centro (modalità alternativa)
 * Non utilizzata nella versione corrente (si usa onsubmit inline)
 */
window.confirmDelete = function(centroId, centroName) {
    console.log('🗑️ Richiesta eliminazione centro:', centroName, 'ID:', centroId);
    
    // Implementazione modal Bootstrap (se necessaria in futuro)
    if (document.getElementById('deleteModal')) {
        // Aggiorna contenuti modal
        document.getElementById('centro-name').textContent = centroName;
        document.getElementById('delete-form').action = `/admin/centri/${centroId}`;
        
        // Mostra modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } else {
        // Fallback a confirm nativo
        return confirm(`Sei sicuro di voler eliminare il centro "${centroName}"?\n\nQuesta azione non può essere annullata.`);
    }
}
</script>
@endpush

{{-- 
    PUSH STYLES: Aggiunge CSS personalizzati al layout
    Tutti gli stili sono CSS puro, non utilizzano preprocessori
    Organizzati per sezioni funzionali
--}}
@push('styles')
<style>
/**
 * STILI TABELLA - CSS
 * Miglioramenti visivi per la tabella principale
 */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    background-color: #f8f9fa;
    position: sticky;  /* Header fisso durante scroll */
    top: 0;
    z-index: 10;
}

.table th a {
    color: #495057 !important;
    text-decoration: none !important;
    display: block;
    padding: 0.5rem 0;
}

.table th a:hover {
    color: #007bff !important;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 202, 240, 0.05);
}

/**
 * STILI CARD - CSS
 * Effetti hover e transizioni per le card
 */
.card {
    transition: all 0.2s ease-in-out;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/**
 * STILI BADGE - CSS
 * Badge personalizzati con dimensioni variabili
 */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.badge.fs-5 {
    font-size: 1.1rem !important;
    padding: 0.5rem 0.75rem !important;
}

.badge-sm {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

/**
 * STILI PULSANTI ORDINAMENTO - CSS
 * Stati attivo/hover per pulsanti ordinamento
 */
.btn-group .btn.active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
    font-weight: 600;
}

.btn-group .btn:not(.active):hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/**
 * STILI RESPONSIVE - CSS Media Queries
 * Adattamenti per dispositivi mobili
 */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
}

/**
 * STILI STATI CENTRO - CSS
 * Indicatori visivi per stato dei centri
 */
.centro-active {
    border-left: 4px solid #198754;
}

.centro-inactive {
    border-left: 4px solid #ffc107;
}

/**
 * STILI COLLAPSE DETTAGLI - CSS
 * Area espandibile per dettagli tecnici
 */
.collapse .card-body {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

/**
 * STILI LINK CONTATTI - CSS
 * Styling per link telefono e email
 */
a[href^="tel:"], 
a[href^="mailto:"] {
    color: inherit;
    text-decoration: none;
}

a[href^="tel:"]:hover, 
a[href^="mailto:"]:hover {
    color: #0d6efd;
    text-decoration: underline;
}

/**
 * STILI COLORI TEMA - CSS
 * Colori consistenti per icone e testo
 */
.text-primary {
    color: #0d6efd !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

/**
 * STILI MODAL - CSS
 * Miglioramenti visivi per modal Bootstrap
 */
.modal-content {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

/**
 * EFFETTI HOVER - CSS
 * Animazioni micro-interazione
 */
.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/**
 * ANIMAZIONI - CSS Keyframes
 * Animazione ingresso per alert
 */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-in-out;
}

/**
 * STILI ACCESSIBILITÀ - CSS
 * Focus states per navigazione tastiera
 */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
}

/**
 * STILI STAMPA - CSS Media Print
 * Ottimizzazioni per stampa pagina
 */
@media print {
    .btn, .alert, .modal, .collapse {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .table {
        font-size: 0.8rem;
    }
}

/**
 * SUPPORTO DARK MODE - CSS Media Prefers
 * Stili per tema scuro sistema
 */
@media (prefers-color-scheme: dark) {
    .table-light {
        background-color: #495057 !important;
        color: #fff !important;
    }
    
    .bg-light {
        background-color: #343a40 !important;
        color: #fff !important;
    }
}

/**
 * REDUCED MOTION - CSS Media Prefers
 * Disabilita animazioni per utenti sensibili
 */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .alert {
        transition: none;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
}

/**
 * CLASSI UTILITY PERSONALIZZATE - CSS
 * Classi helper aggiuntive
 */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}

.fs-7 {
    font-size: 0.875rem !important;
}

/**
 * SPACING MIGLIORATO - CSS
 * Margini e padding consistenti
 */
.g-3 > * {
    margin-bottom: 1rem;
}

/**
 * BADGE COLORATI - CSS
 * Override colori badge Bootstrap
 */
.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/**
 * STICKY HEADER TABELLA - CSS
 * Header tabella sempre visibile
 */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

/**
 * STATI LOADING - CSS
 * Disabilita pulsanti durante operazioni
 */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/**
 * FOCUS MIGLIORATI - CSS
 * Focus states più evidenti per accessibilità
 */
.table th a:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/**
 * TIPOGRAFIA - CSS
 * Pesi font consistenti
 */
.fw-bold {
    font-weight: 700;
}

.small, small {
    font-size: 0.875rem;
}

/**
 * HOVER RIGHE TABELLA - CSS
 * Effetti interattivi su righe tabella
 */
.table tbody tr {
    cursor: pointer;
}

.table tbody tr:hover .btn {
    opacity: 1;
}

.table tbody tr .btn {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}
</style>
@endpush