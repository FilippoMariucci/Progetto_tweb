{{-- 
    Vista Admin Centro Assistenza - Dettaglio Singolo Centro
    File: resources/views/admin/centri/show.blade.php
    Linguaggio: Blade Template (Laravel) + HTML + CSS + JavaScript
    
    FUNZIONALITÀ IMPLEMENTATE:
    - Visualizzazione dettagliata singolo centro assistenza
    - Gestione tecnici associati (assegnazione, rimozione)
    - Pannello amministrativo completo
    - Statistiche centro in tempo reale
    - Azioni CRUD su centro e tecnici
    - Modal per assegnazione tecnici via AJAX
    - Integrazione Google Maps
    - Sistema di notifiche temporanee
    
    LIVELLO ACCESSO: Solo Amministratore (Livello 4)
    FUNZIONALITÀ OPZIONALE: Gestione archivio centri assistenza
--}}

{{-- 
    EXTENDS: Estende il layout principale dell'applicazione Laravel 
    Il layout app.blade.php fornisce struttura base (header, navigation, footer)
--}}
@extends('layouts.app')

{{-- 
    SECTION TITLE: Titolo dinamico della pagina
    Combina prefisso "Admin" con nome del centro per identificazione univoca
--}}
@section('title', 'Admin - ' . $centro->nome)

{{-- 
    SECTION META DESCRIPTION: Meta tag SEO per amministratori
    Genera descrizione automatica per indicizzazione motori ricerca
--}}
@section('meta_description', 'Amministrazione centro di assistenza ' . $centro->nome . ' a ' . $centro->citta . '. Gestione tecnici, contatti e configurazioni.')

{{-- 
    PUSH BREADCRUMB OVERRIDE: CSS per nascondere breadcrumb
    Utilizza @push per iniettare CSS nel layout principale
    display: none !important forza nascondimento elemento
--}}
@push('breadcrumb-override')
<style>
.breadcrumb, nav[aria-label="breadcrumb"] {
    display: none !important;
}
</style>
@endpush

{{-- 
    SECTION CONTENT: Contenuto principale della vista
    Layout a due colonne: principale (dettagli) + sidebar (controlli admin)
--}}
@section('content')
<div class="container mt-4">
    
    <div class="row">
        {{-- 
            COLONNA PRINCIPALE (8/12 = 66% larghezza)
            Contiene informazioni dettagliate del centro e lista tecnici
        --}}
        <div class="col-lg-8">
            
            {{-- 
                HEADER DEL CENTRO - CARD PRINCIPALE
                Design moderno con header colorato e badge informativi
            --}}
            {{-- Favicon link (dovrebbe essere nel layout, qui per sicurezza) --}}
            <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
            <div class="card card-custom mb-4">
                {{-- Header rosso per identificare sezione admin --}}
                <div class="card-header bg-danger text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            {{-- Titolo principale con icona shield-lock (sicurezza admin) --}}
                            <h1 class="h3 mb-0">
                                <i class="bi bi-shield-lock me-2"></i>
                                {{ $centro->nome }}
                            </h1>
                            {{-- Sottotitolo identificativo pannello admin --}}
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-gear me-1"></i>
                                Pannello Amministrazione Centro
                            </p>
                        </div>
                        <div class="col-auto">
                            {{-- 
                                BADGE STATO CENTRO
                                Condizionale: mostra diversi badge in base a presenza tecnici
                                Plurale/singolare automatico per tecnici
                            --}}
                            @if($centro->tecnici->count() > 0)
                                {{-- Badge tecnici presenti --}}
                                <span class="badge bg-light text-danger fs-6 me-2">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $centro->tecnici->count() }} Tecnic{{ $centro->tecnici->count() > 1 ? 'i' : 'o' }}
                                </span>
                                {{-- Badge centro attivo --}}
                                <span class="badge bg-success fs-6">Centro Attivo</span>
                            @else
                                {{-- Badge centro senza tecnici --}}
                                <span class="badge bg-warning fs-6">Centro Inattivo</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- 
                            COLONNA INFORMAZIONI CONTATTO (50% larghezza su desktop)
                            Visualizza tutti i dati di contatto del centro
                        --}}
                        <div class="col-md-6">
                            <h5 class="text-danger mb-3">
                                <i class="bi bi-geo-alt me-2"></i>
                                Informazioni di Contatto
                            </h5>
                            
                            {{-- 
                                SEZIONE INDIRIZZO
                                Layout con label + pulsante modifica inline
                            --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small d-flex justify-content-between">
                                    INDIRIZZO
                                    {{-- Pulsante modifica rapida con stile compatto --}}
                                    <a href="{{ route('admin.centri.edit', $centro) }}" 
                                       class="btn btn-outline-warning btn-xs">
                                        <i class="bi bi-pencil"></i> Modifica
                                    </a>
                                </label>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{-- Operatore null coalescing ?? per default se vuoto --}}
                                    {{ $centro->indirizzo ?? 'Non specificato' }}
                                </p>
                                {{-- Riga secondaria con città, CAP, provincia --}}
                                <p class="text-muted small mb-0">
                                    {{ $centro->citta }}
                                    @if($centro->cap)
                                        {{ $centro->cap }}
                                    @endif
                                    @if($centro->provincia)
                                        ({{ strtoupper($centro->provincia) }})
                                    @endif
                                </p>
                            </div>
                            
                            {{-- 
                                SEZIONE TELEFONO
                                Link cliccabile + pulsante copia in clipboard
                                Condizionale: mostra solo se presente
                            --}}
                            @if($centro->telefono)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">TELEFONO</label>
                                    <p class="mb-0">
                                        <i class="bi bi-telephone text-success me-2"></i>
                                        {{-- Link tel: per apertura dialer su mobile --}}
                                        <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                            {{ $centro->telefono }}
                                        </a>
                                        {{-- Pulsante copia con funzione JavaScript --}}
                                        <button class="btn btn-outline-secondary btn-xs ms-2" 
                                                onclick="copiaInClipboard('{{ $centro->telefono }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- 
                                SEZIONE EMAIL
                                Link mailto + pulsante copia (stesso pattern telefono)
                            --}}
                            @if($centro->email)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">EMAIL</label>
                                    <p class="mb-0">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        {{-- Link mailto: per apertura client email --}}
                                        <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                            {{ $centro->email }}
                                        </a>
                                        {{-- Pulsante copia email --}}
                                        <button class="btn btn-outline-secondary btn-xs ms-2" 
                                                onclick="copiaInClipboard('{{ $centro->email }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- 
                                SEZIONE CAP (se disponibile)
                                Informazione semplice senza azioni
                            --}}
                            @if($centro->cap)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">CAP</label>
                                    <p class="mb-0">
                                        <i class="bi bi-mailbox text-info me-2"></i>
                                        {{ $centro->cap }}
                                    </p>
                                </div>
                            @endif
                            
                            {{-- 
                                STATO VUOTO - NESSUN CONTATTO
                                Mostrato solo se mancano sia telefono che email
                            --}}
                            @if(!$centro->telefono && !$centro->email)
                                <div class="text-center py-3">
                                    <i class="bi bi-exclamation-triangle text-warning display-6"></i>
                                    <p class="text-muted mb-0">Nessun contatto disponibile</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- 
                            COLONNA GESTIONE AMMINISTRATIVA (50% larghezza)
                            Azioni rapide e statistiche per amministratori
                        --}}
                        <div class="col-md-6">
                            <h5 class="text-danger mb-3">
                                <i class="bi bi-tools me-2"></i>
                                Gestione Amministrativa
                            </h5>
                            
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">AZIONI RAPIDE</label>
                                {{-- Grid layout per pulsanti stacked --}}
                                <div class="d-grid gap-2 mt-2">
                                    {{-- Pulsante modifica centro --}}
                                    <a href="{{ route('admin.centri.edit', $centro) }}" 
                                       class="btn btn-warning text-white">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Modifica Informazioni
                                    </a>
                                    
                                    {{-- 
                                        Pulsante aggiungi tecnico
                                        data-bs-toggle/target per modal Bootstrap 5
                                    --}}
                                    <button type="button" 
                                            class="btn btn-success"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAssegnaTecnico">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Aggiungi Tecnico
                                    </button>
                                    
                                    {{-- 
                                        Pulsante Google Maps
                                        onclick chiama funzione JavaScript per aprire maps
                                        Concatena indirizzo completo per geolocalizzazione
                                    --}}
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="GoogleMapsUtil.openMaps('{{ $centro->indirizzo }}, {{ $centro->citta }}, {{ $centro->provincia }}')">
                                        <i class="bi bi-map me-1"></i>
                                        Visualizza su Maps
                                    </button>
                                </div>
                            </div>
                            
                            {{-- 
                                SEZIONE STATISTICHE VISUALI
                                Grid 2x2 con metriche aggregate del centro
                            --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">STATISTICHE</label>
                                <div class="row mt-2">
                                    {{-- Statistica numero tecnici --}}
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <h5 class="text-danger mb-0">{{ $centro->tecnici->count() }}</h5>
                                            <small class="text-muted">Tecnici</small>
                                        </div>
                                    </div>
                                    {{-- 
                                        Statistica specializzazioni uniche
                                        whereNotNull + pluck + unique + count per calcolo
                                    --}}
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <h5 class="text-info mb-0">
                                                {{ $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count() }}
                                            </h5>
                                            <small class="text-muted">Specializzazioni</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 
                        PULSANTI NAVIGAZIONE ADMIN
                        Barra inferiore con azioni principali centrate
                    --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                {{-- Torna alla lista centri --}}
                                <a href="{{ route('admin.centri.index') }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Lista Centri
                                </a>
                                
                                {{-- Dashboard amministratore --}}
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="btn btn-outline-danger">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Dashboard Admin
                                </a>
                                
                                {{-- 
                                    Vista pubblica (nuova tab)
                                    target="_blank" per aprire in nuova finestra
                                --}}
                                <a href="{{ route('centri.show', $centro) }}" 
                                   class="btn btn-outline-info" 
                                   target="_blank">
                                    <i class="bi bi-eye me-1"></i>
                                    Vista Pubblica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                SEZIONE TECNICI DEL CENTRO
                Card principale per gestione tecnici associati
            --}}
            <div class="card card-custom mb-4">
                {{-- Header verde per identificare sezione tecnici --}}
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="bi bi-people me-2"></i>
                                {{-- Titolo dinamico con count tecnici --}}
                                Tecnici del Centro ({{ $centro->tecnici->count() }})
                            </h4>
                        </div>
                        <div class="col-auto">
                            {{-- Pulsante aggiungi tecnico duplicato per convenience --}}
                            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                                <i class="bi bi-plus-circle me-1"></i> Aggiungi Tecnico
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- 
                        CONDIZIONE: Lista tecnici vs stato vuoto
                        isNotEmpty() verifica se collection ha elementi
                    --}}
                    @if($centro->tecnici->isNotEmpty())
                        {{-- 
                            GRID RESPONSIVE TECNICI
                            col-md-6 = 2 colonne su desktop, 1 su mobile
                        --}}
                        <div class="row">
                            {{-- 
                                FOREACH TECNICI: Loop attraverso tecnici associati
                                $centro->tecnici relazione Eloquent many-to-many
                            --}}
                            @foreach($centro->tecnici as $tecnico)
                                <div class="col-md-6 mb-3">
                                    {{-- Card singolo tecnico con hover effects --}}
                                    <div class="border rounded p-3 h-100 tecnico-card">
                                        <div class="d-flex align-items-center mb-2">
                                            {{-- 
                                                AVATAR TECNICO STILIZZATO
                                                Circle con icona person-gear (tecnico)
                                            --}}
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                {{-- 
                                                    NOME TECNICO CLICCABILE
                                                    Link a pagina dettaglio utente admin
                                                --}}
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.users.show', $tecnico) }}" 
                                                       class="text-decoration-none">
                                                        {{-- nome_completo accessor o concatenazione --}}
                                                        {{ $tecnico->nome_completo }}
                                                    </a>
                                                </h6>
                                                {{-- 
                                                    BADGE SPECIALIZZAZIONE CONDIZIONALE
                                                    Badge diverso se specializzazione presente o meno
                                                --}}
                                                @if($tecnico->specializzazione)
                                                    <span class="badge bg-light text-dark small">
                                                        <i class="bi bi-wrench me-1"></i>
                                                        {{ $tecnico->specializzazione }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary small">
                                                        <i class="bi bi-question-circle me-1"></i>
                                                        Specializzazione non specificata
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- 
                                            INFORMAZIONI AGGIUNTIVE TECNICO
                                            Età calcolata se data_nascita presente
                                        --}}
                                        @if($tecnico->eta)
                                            <p class="small text-muted mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Età: {{ $tecnico->eta }} anni
                                            </p>
                                        @endif
                                        
                                        {{-- 
                                            AZIONI SUL TECNICO
                                            Gruppo pulsanti: visualizza, modifica, rimuovi
                                        --}}
                                        <div class="d-flex gap-1 justify-content-end">
                                            {{-- Visualizza dettagli tecnico --}}
                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Visualizza
                                            </a>
                                            {{-- Modifica dati tecnico --}}
                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Modifica
                                            </a>
                                            {{-- 
                                                FORM RIMOZIONE TECNICO
                                                - Form POST con @method('DELETE')
                                                - onsubmit con confirm JavaScript
                                                - addslashes() per escape quotes nel nome
                                                - Hidden input per ID tecnico
                                            --}}
                                            <form action="{{ route('admin.centri.rimuovi-tecnico', $centro) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Sei sicuro di voler rimuovere &quot;{{ addslashes($tecnico->nome_completo) }}&quot; da questo centro?\n\nIl tecnico rimarrà nel sistema ma non sarà più assegnato a questo centro.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tecnico_id" value="{{ $tecnico->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Rimuovi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- 
                            STATO VUOTO - NESSUN TECNICO
                            Centered layout con icona grande e call-to-action
                        --}}
                        <div class="text-center py-5">
                            <i class="bi bi-people display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">Nessun Tecnico Assegnato</h5>
                            <p class="text-muted mb-4">Questo centro non ha ancora tecnici assegnati.</p>
                            {{-- Pulsante CTA per primo tecnico --}}
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAssegnaTecnico">
                                <i class="bi bi-plus-circle me-1"></i> Aggiungi Primo Tecnico
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
        
        {{-- 
            SIDEBAR AMMINISTRATIVA (4/12 = 33% larghezza)
            Pannelli di controllo, statistiche, azioni rapide
        --}}
        <div class="col-lg-4">
            
            {{-- 
                CARD PANNELLO DI CONTROLLO
                Informazioni amministrative e stato centro
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Pannello di Controllo
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Sezione stato centro --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">STATO CENTRO</label>
                        <div class="mt-1">
                            {{-- Badge stato basato su presenza tecnici --}}
                            @if($centro->tecnici->count() > 0)
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Operativo
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Inattivo
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- 
                        SEZIONE INFORMAZIONI TEMPORALI
                        Date di creazione e modifica formattate
                    --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">INFORMAZIONI TEMPORALI</label>
                        {{-- Data creazione --}}
                        <p class="small mb-1">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Creato: {{ $centro->created_at->format('d/m/Y H:i') }}
                        </p>
                        {{-- 
                            Data modifica (solo se diversa da creazione)
                            Confronto timestamp per evitare ridondanza
                        --}}
                        @if($centro->updated_at != $centro->created_at)
                            <p class="small mb-0">
                                <i class="bi bi-calendar-check me-1"></i>
                                Modificato: {{ $centro->updated_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                    
                    {{-- 
                        SEZIONE ID CENTRO
                        ID tecnico con pulsante copia per riferimenti
                    --}}
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">ID CENTRO</label>
                        <div class="d-flex align-items-center">
                            {{-- Codice stilizzato con background --}}
                            <code class="bg-light p-2 rounded flex-grow-1">#{{ $centro->id }}</code>
                            {{-- Pulsante copia ID --}}
                            <button class="btn btn-outline-secondary btn-sm ms-2" 
                                    onclick="copiaInClipboard('{{ $centro->id }}')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD STATISTICHE DETTAGLIATE
                Metriche avanzate del centro in formato dashboard
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistiche Dettagliate
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Grid 2x2 + 1 per statistiche --}}
                    <div class="row g-3 text-center">
                        {{-- Statistica tecnici totali --}}
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ $centro->tecnici->count() }}</h4>
                                <small class="text-muted">Tecnici Totali</small>
                            </div>
                        </div>
                        {{-- 
                            Statistica specializzazioni uniche
                            @php block per calcolo complesso
                        --}}
                        <div class="col-6">
                            @php
                                $specializzazioni = $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count();
                            @endphp
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">{{ $specializzazioni }}</h4>
                                <small class="text-muted">Specializzazioni</small>
                            </div>
                        </div>
                        {{-- Stato operativo completo --}}
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h5 class="text-primary mb-1">
                                    @if($centro->tecnici->count() > 0)
                                        <i class="bi bi-check-circle me-1"></i>
                                        Centro Attivo
                                    @else
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Centro Inattivo
                                    @endif
                                </h5>
                                <small class="text-muted">
                                    Dal {{ $centro->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD ALTRI CENTRI NELLA STESSA PROVINCIA (condizionale)
                Mostrata solo se variabile $centriVicini è definita e non vuota
                Utile per comparazione geografica
            --}}
            @if(isset($centriVicini) && $centriVicini->isNotEmpty())
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo text-primary me-2"></i>
                            {{-- Titolo dinamico con provincia e count --}}
                            Altri Centri in {{ strtoupper($centro->provincia) }} ({{ $centriVicini->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Loop centri vicini --}}
                        @foreach($centriVicini as $centroVicino)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        {{-- Nome centro vicino linkato --}}
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.centri.show', $centroVicino) }}" 
                                               class="text-decoration-none">
                                                {{ $centroVicino->nome }}
                                            </a>
                                        </h6>
                                        {{-- Località centro vicino --}}
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $centroVicino->citta }}
                                        </small>
                                    </div>
                                    {{-- Badge count tecnici --}}
                                    <span class="badge bg-light text-dark">
                                        {{ $centroVicino->tecnici_count ?? 0 }} tecnici
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- 
                CARD LINK AMMINISTRATIVI
                Navigazione rapida verso altre sezioni admin
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-danger me-2"></i>
                        Link Amministrativi
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Grid di pulsanti navigazione --}}
                    <div class="d-grid gap-2">
                        {{-- Lista tutti i centri --}}
                        <a href="{{ route('admin.centri.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-list me-2"></i>
                            Tutti i Centri
                        </a>
                        
                        {{-- Gestione utenti --}}
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-people me-2"></i>
                            Gestione Utenti
                        </a>
                        
                        {{-- Catalogo prodotti --}}
                        <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box me-2"></i>
                            Catalogo Prodotti
                        </a>
                        
                        {{-- Divisore visuale --}}
                        <hr class="my-2">
                        
                        {{-- Vista pubblica (nuova finestra) --}}
                        <a href="{{ route('centri.show', $centro) }}" 
                           class="btn btn-outline-info btn-sm" 
                           target="_blank">
                            <i class="bi bi-eye me-2"></i>
                            Vista Pubblica
                        </a>
                        
                        {{-- Dashboard principale --}}
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
</div>

{{-- 
    MODAL ASSEGNAZIONE TECNICO
    Modal Bootstrap 5 per assegnare tecnici al centro via AJAX
    Mantiene tutta la funzionalità JavaScript originale
--}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Header modal verde per coerenza con sezione tecnici --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Tecnico al Centro
                </h5>
                {{-- Pulsante chiusura con stile white per header colorato --}}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- 
                    INFO CENTRO NEL MODAL
                    Alert informativo con dettagli centro corrente
                --}}
                <div class="alert alert-info">
                    <strong>Centro:</strong> {{ $centro->nome }}<br>
                    <strong>Località:</strong> {{ $centro->indirizzo }}, {{ $centro->citta }}
                </div>
                
                {{-- 
                    FORM ASSEGNAZIONE TECNICO
                    Form che sarà gestito via AJAX dal JavaScript esterno
                    action: route per assegnazione tecnico al centro
                --}}
                <form id="formAssegnaTecnico" action="{{ route('admin.centri.assegna-tecnico', $centro) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        {{-- 
                            SELECT TECNICO CON CLASSE required
                            Il JavaScript caricherà dinamicamente le opzioni via API
                        --}}
                        <label for="tecnico_id" class="form-label required">Seleziona Tecnico</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select" required>
                            {{-- Opzione default durante caricamento --}}
                            <option value="">Caricamento tecnici disponibili...</option>
                        </select>
                        {{-- Testo di aiuto per l'utente --}}
                        <div class="form-text">
                            Vengono mostrati sia i tecnici non assegnati che quelli trasferibili da altri centri.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- Pulsante annulla --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                {{-- 
                    Pulsante submit collegato al form
                    form="formAssegnaTecnico" per submit del form anche se fuori
                    disabled inizialmente, abilitato dal JavaScript quando tecnico selezionato
                --}}
                <button type="submit" form="formAssegnaTecnico" id="btnAssegnaTecnico" class="btn btn-success" disabled>
                    <i class="bi bi-check-circle me-1"></i> Assegna Tecnico
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- 
===============================================================================
CONFIGURAZIONE JAVASCRIPT - MANTIENE TUTTO IL CODICE ORIGINALE
===============================================================================

Questa sezione carica e configura tutti gli script necessari per il funzionamento
della vista, mantenendo la compatibilità con il codice JavaScript esistente.
--}}

{{-- 
    PUSH SCRIPTS: Aggiunge JavaScript al layout principale
    Carica file esterno + configurazione inline
--}}
@push('scripts')
{{-- 
    FILE JAVASCRIPT PRINCIPALE ESTERNO
    Carica il file separato che contiene tutta la logica per:
    - Caricamento dinamico tecnici disponibili
    - Gestione modal assegnazione
    - Chiamate AJAX per operazioni
    - Utility varie (Google Maps, clipboard, etc.)
--}}
<script src="{{ asset('js/admin/centri-show.js') }}"></script>

{{-- 
    CONFIGURAZIONE GLOBALE PER JAVASCRIPT
    Oggetto window.AdminCentroShowConfig accessibile da script esterni
--}}
<script>
/**
 * Configurazione globale per modulo AdminCentroShow - JavaScript
 * Trasferisce dati PHP al contesto JavaScript per uso negli script esterni
 * 
 * @type {Object} AdminCentroShowConfig - Configurazione principale
 * @property {number} centroId - ID del centro corrente
 * @property {string} baseUrl - URL base applicazione per chiamate API
 * @property {string} csrfToken - Token CSRF Laravel per sicurezza
 * @property {boolean} debugMode - Abilita logging debug (solo ambiente locale)
 * @property {string} centroNome - Nome centro per logging/messaggi
 * @property {string} centroIndirizzo - Indirizzo completo per Google Maps
 */
window.AdminCentroShowConfig = {
    centroId: {{ $centro->id }},
    baseUrl: '{{ url("/") }}',
    csrfToken: '{{ csrf_token() }}',
    debugMode: {{ app()->environment('local') ? 'true' : 'false' }}, // Debug solo in locale
    centroNome: @json($centro->nome),
    centroIndirizzo: @json($centro->indirizzo . ', ' . $centro->citta . ', ' . $centro->provincia)
};

// Log di configurazione per debug (solo se debugMode attivo)
console.log('🔧 Configurazione AdminCentroShow caricata:', window.AdminCentroShowConfig);

/**
 * Funzione copia in clipboard - JavaScript
 * Utilizza Clipboard API moderna per copiare testo
 * 
 * @param {string} testo - Testo da copiare in clipboard
 * @returns {Promise} - Promise della operazione di copia
 */
function copiaInClipboard(testo) {
    // Verifica supporto Clipboard API
    if (!navigator.clipboard) {
        console.warn('Clipboard API non supportata');
        mostraNotifica('Copia manuale necessaria', 'warning');
        return;
    }
    
    // Operazione di copia asincrona
    navigator.clipboard.writeText(testo).then(function() {
        // Successo: mostra notifica positiva
        mostraNotifica('Copiato in clipboard: ' + testo, 'success');
        console.log('✅ Testo copiato:', testo);
    }).catch(function(err) {
        // Errore: log e notifica errore
        console.error('❌ Errore nella copia:', err);
        mostraNotifica('Errore nella copia', 'error');
    });
}

/**
 * Sistema di notifiche temporanee - JavaScript
 * Crea toast notifications temporanee in alto a destra
 * 
 * @param {string} messaggio - Testo da mostrare
 * @param {string} tipo - Tipo notifica: 'success', 'error', 'info', 'warning'
 */
function mostraNotifica(messaggio, tipo = 'info') {
    console.log(`📢 Notifica ${tipo}:`, messaggio);
    
    // Rimuovi eventuali notifiche esistenti per evitare sovrapposizione
    document.querySelectorAll('.notifica-temp').forEach(el => el.remove());
    
    // Determina classe Bootstrap in base al tipo
    const bootstrapClass = {
        'success': 'alert-success',
        'error': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[tipo] || 'alert-info';
    
    // Determina icona Bootstrap in base al tipo
    const iconClass = {
        'success': 'bi-check-circle',
        'error': 'bi-exclamation-triangle',
        'warning': 'bi-exclamation-triangle', 
        'info': 'bi-info-circle'
    }[tipo] || 'bi-info-circle';
    
    // Crea elemento notifica
    const notifica = document.createElement('div');
    notifica.className = `alert ${bootstrapClass} notifica-temp`;
    
    // Stili CSS inline per posizionamento fisso
    notifica.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
    `;
    
    // Contenuto HTML con icona e messaggio
    notifica.innerHTML = `
        <i class="bi ${iconClass} me-2"></i>
        ${messaggio}
    `;
    
    // Aggiungi al DOM
    document.body.appendChild(notifica);
    
    // Rimozione automatica dopo 3 secondi
    setTimeout(() => {
        if (notifica.parentNode) {
            // Fade out prima della rimozione
            notifica.style.transition = 'opacity 0.3s ease';
            notifica.style.opacity = '0';
            
            setTimeout(() => {
                notifica.remove();
            }, 300);
        }
    }, 3000);
}

/**
 * Utility Google Maps - JavaScript globale
 * Oggetto per gestire interazioni con Google Maps
 */
window.GoogleMapsUtil = {
    /**
     * Apre Google Maps con indirizzo specificato
     * 
     * @param {string} indirizzo - Indirizzo da cercare su Maps
     */
    openMaps: function(indirizzo) {
        console.log('🗺️ Apertura Google Maps per:', indirizzo);
        
        try {
            // Codifica indirizzo per URL
            const indirizzoEncoded = encodeURIComponent(indirizzo);
            
            // URL Google Maps con query di ricerca
            const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${indirizzoEncoded}`;
            
            // Apri in nuova finestra
            window.open(mapsUrl, '_blank');
            
            // Notifica successo
            mostraNotifica('Google Maps aperto per: ' + indirizzo, 'info');
            
        } catch (error) {
            console.error('❌ Errore apertura Google Maps:', error);
            mostraNotifica('Errore apertura Google Maps', 'error');
        }
    }
};

/**
 * Inizializzazione al caricamento DOM - JavaScript
 * Event listener per setup iniziale della pagina
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Vista Admin Centro Show inizializzata');
    
    // Verifica presenza configurazione
    if (window.AdminCentroShowConfig) {
        console.log('✅ Configurazione trovata per centro ID:', window.AdminCentroShowConfig.centroId);
    } else {
        console.warn('⚠️ Configurazione AdminCentroShowConfig non trovata');
    }
    
    // Setup tooltip Bootstrap se presenti
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Log statistiche centro per debug
    if (window.AdminCentroShowConfig && window.AdminCentroShowConfig.debugMode) {
        console.log('📊 Debug info centro:', {
            nome: window.AdminCentroShowConfig.centroNome,
            indirizzo: window.AdminCentroShowConfig.centroIndirizzo
        });
    }
});
</script>
@endpush

{{-- 
    PUSH STYLES: CSS personalizzato per la vista
    Combina stili della vista moderna con funzionalità originale
    Organizzato per sezioni funzionali con commenti dettagliati
--}}
@push('styles')
<style>
/**
 * ===================================================================
 * STILI BASE DELLA VISTA MODERNA - CSS
 * ===================================================================
 * Stili principali per il layout e componenti base
 */

/**
 * Card personalizzate con effetti moderni - CSS
 * Ombreggiature e transizioni per migliorare UX
 */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.75rem;
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/**
 * Header card personalizzati - CSS
 * Border-radius coerente con card-custom
 */
.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

/**
 * Badge moderni con dimensioni adattive - CSS
 * Font-size e padding migliorati per leggibilità
 */
.badge.fs-6 {
    font-size: 0.875rem !important;
    padding: 0.5em 0.75em;
    border-radius: 0.5rem;
}

/**
 * ===================================================================
 * PULSANTI CON TESTO SEMPRE VISIBILE - CSS
 * ===================================================================
 * Override colori Bootstrap per garantire contrasto e visibilità
 */

/**
 * Pulsante rosso (danger) - CSS
 * Background e colore testo garantiti con !important
 */
.btn.btn-danger {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

/**
 * Pulsante giallo (warning) - CSS  
 * Testo nero per contrasto su sfondo giallo
 */
.btn.btn-warning {
    background: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000000 !important;
    font-weight: 600;
}

/**
 * Pulsante verde (success) - CSS
 */
.btn.btn-success {
    background: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
}

/**
 * Pulsante azzurro (info) - CSS
 */
.btn.btn-info {
    background: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #ffffff !important;
}

/**
 * Pulsante blu (primary) - CSS
 */
.btn.btn-primary {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #ffffff !important;
}

/**
 * ===================================================================
 * HOVER EFFECTS CON TESTO VISIBILE - CSS
 * ===================================================================
 * Stati hover che mantengono leggibilità e aggiungono micro-animazioni
 */

.btn.btn-danger:hover {
    background: #c82333 !important;
    border-color: #bd2130 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-warning:hover {
    background: #e0a800 !important;
    border-color: #d39e00 !important;
    color: #000000 !important;
    transform: translateY(-1px);
}

.btn.btn-success:hover {
    background: #218838 !important;
    border-color: #1e7e34 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-info:hover {
    background: #138496 !important;
    border-color: #117a8b !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.btn.btn-primary:hover {
    background: #0069d9 !important;
    border-color: #0062cc !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/**
 * ===================================================================
 * STILI SPECIFICI AMMINISTRAZIONE - CSS
 * ===================================================================
 * Elementi specifici per interfaccia amministrativa
 */

/**
 * Pulsanti extra small per azioni compatte - CSS
 */
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.25rem;
}

/**
 * Label obbligatori con asterisco rosso - CSS
 * Pseudo-element ::after per aggiungere asterisco
 */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/**
 * Codici e ID tecnici stilizzati - CSS
 * Background e border per distinguere dal testo normale
 */
code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    border: 1px solid #dee2e6;
}

/**
 * Card tecnici con animazioni hover - CSS
 * Transizione smooth per effetto interattivo
 */
.tecnico-card {
    transition: all 0.2s ease;
}

.tecnico-card:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

/**
 * ===================================================================
 * ICONE COLORATE PER UX - CSS
 * ===================================================================
 * Colori semantici per icone migliorano usabilità
 */

.bi-telephone {
    color: #28a745 !important; /* Verde per telefono */
}

.bi-envelope {
    color: #17a2b8 !important; /* Azzurro per email */
}

.bi-map, .bi-geo-alt {
    color: #007bff !important; /* Blu per posizione */
}

.bi-shield-lock {
    color: #dc3545 !important; /* Rosso per admin/sicurezza */
}

.bi-gear, .bi-tools {
    color: #ffc107 !important; /* Giallo per strumenti */
}

.bi-people {
    color: #28a745 !important; /* Verde per persone */
}

.bi-calendar3, .bi-calendar-plus, .bi-calendar-check {
    color: #6f42c1 !important; /* Viola per date */
}

.bi-at {
    color: #fd7e14 !important; /* Arancione per @ */
}

.bi-clipboard {
    color: #6c757d !important; /* Grigio per clipboard */
}

/**
 * ===================================================================
 * NOTIFICHE TEMPORANEE - CSS
 * ===================================================================
 * Animazioni e stili per toast notifications
 */

/**
 * Notifica temporanea con animazione ingresso - CSS
 */
.notifica-temp {
    animation: slideInRight 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

/**
 * Keyframe animazione slide da destra - CSS
 */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/**
 * ===================================================================
 * MODAL PERSONALIZZATI - CSS
 * ===================================================================
 * Styling per modal Bootstrap con bordi arrotondati
 */

.modal-content {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header.bg-danger {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

.modal-header.bg-success {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

/**
 * ===================================================================
 * ALERT PERSONALIZZATI - CSS
 * ===================================================================
 * Gradienti per alert più accattivanti
 */

.alert-info {
    background: linear-gradient(135deg, #cce7ff, #e3f2fd);
    border: 1px solid #007bff;
    border-radius: 8px;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    border: 1px solid #dc3545;
    border-radius: 8px;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    border: 1px solid #28a745;
    border-radius: 8px;
}

/**
 * ===================================================================
 * AVATAR E ELEMENTI GRAFICI - CSS
 * ===================================================================
 * Styling per avatar tecnici con effetti ombra
 */

.bg-success.text-white.rounded-circle {
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    transition: all 0.2s ease;
}

.bg-success.text-white.rounded-circle:hover {
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    transform: scale(1.05);
}

/**
 * ===================================================================
 * BADGE MIGLIORATI - CSS
 * ===================================================================
 * Sizing e colori consistenti per tutti i badge
 */

.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
    border-radius: 0.5rem;
    font-weight: 500;
}

.badge.bg-light {
    color: #212529 !important;
    border: 1px solid #dee2e6;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/**
 * ===================================================================
 * RESPONSIVE DESIGN - CSS Media Queries
 * ===================================================================
 * Adattamenti per dispositivi mobili e tablet
 */

/**
 * Mobile (fino a 768px) - CSS
 */
@media (max-width: 768px) {
    /* Layout responsive per pulsanti */
    .d-flex.flex-wrap.gap-2 {
        justify-content: stretch !important;
    }
    
    .d-flex.flex-wrap.gap-2 > * {
        flex: 1 !important;
        min-width: 120px;
    }
    
    /* Azioni tecnici responsive */
    .d-flex.gap-1 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-1 .btn {
        width: 100%;
    }
    
    /* Notifiche mobile */
    .notifica-temp {
        left: 1rem;
        right: 1rem;
        min-width: auto;
        top: 10px;
    }
    
    /* Modal responsive */
    .modal-dialog {
        margin: 10px;
    }
    
    /* Card responsive */
    .card-custom {
        margin-bottom: 1.5rem;
    }
    
    /* Statistiche responsive */
    .row.g-3.text-center .col-6 {
        margin-bottom: 1rem;
    }
}

/**
 * Tablet (fino a 992px) - CSS
 */
@media (max-width: 992px) {
    .card-custom {
        margin-bottom: 2rem;
    }
    
    .col-lg-4 .card-custom {
        margin-bottom: 1rem;
    }
    
    /* Layout tecnici su tablet */
    .col-md-6 {
        margin-bottom: 1rem;
    }
}

/**
 * ===================================================================
 * ACCESSIBILITÀ - CSS
 * ===================================================================
 * Miglioramenti per navigazione tastiera e screen reader
 */

/**
 * Focus migliorato per elementi interattivi - CSS
 */
.form-control:focus,
.form-select:focus,
.btn:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: none;
}

/**
 * Stati disabled migliorati - CSS
 */
.btn:disabled,
.form-control:disabled,
.form-select:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/**
 * Contrasto migliorato per testo muted - CSS
 */
.text-muted {
    color: #6c757d !important;
}

/**
 * Link con hover migliorato - CSS
 */
a:hover {
    text-decoration: underline;
}

/**
 * ===================================================================
 * ANIMAZIONI E TRANSIZIONI - CSS
 * ===================================================================
 * Transizioni smooth per tutti gli elementi interattivi
 */

.btn,
.card,
.badge,
.alert,
.border.rounded {
    transition: all 0.2s ease;
}

/**
 * ===================================================================
 * REDUCED MOTION - CSS Accessibility
 * ===================================================================
 * Disabilita animazioni per utenti con sensibilità al movimento
 */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    
    .notifica-temp {
        animation: none;
    }
    
    .card-custom:hover,
    .tecnico-card:hover {
        transform: none;
    }
    
    .btn:hover {
        transform: none;
    }
}

/**
 * ===================================================================
 * UTILITÀ AGGIUNTIVE - CSS
 * ===================================================================
 * Classi helper e miglioramenti generali
 */

/**
 * Scroll smooth per navigazione interna - CSS
 */
html {
    scroll-behavior: smooth;
}

/**
 * Tipografia migliorata - CSS
 */
h1, h2, h3, h4, h5, h6 {
    line-height: 1.3;
    font-weight: 600;
}

small, .small {
    line-height: 1.4;
}

/**
 * ===================================================================
 * STATI DI CARICAMENTO - CSS
 * ===================================================================
 * Spinner e stati loading per operazioni asincrone
 */

/**
 * Spinner personalizzato - CSS
 */
.spinner-admin {
    width: 1rem;
    height: 1rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #dc3545;
    border-radius: 50%;
    animation: spin-admin 1s linear infinite;
}

@keyframes spin-admin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/**
 * ===================================================================
 * STAMPA - CSS Media Print
 * ===================================================================
 * Ottimizzazioni per stampa della pagina
 */
@media print {
    /* Nascondi elementi interattivi */
    .btn,
    .modal,
    .notifica-temp {
        display: none !important;
    }
    
    /* Forza bordi per stampa */
    .card-custom {
        border: 1px solid #000 !important;
        box-shadow: none;
    }
    
    /* Colori per stampa */
    .text-primary,
    .text-success,
    .text-danger,
    .text-warning,
    .text-info {
        color: #000 !important;
    }
}

/**
 * ===================================================================
 * MIGLIORAMENTI SPECIFICI VISTA CENTRO - CSS
 * ===================================================================
 * Stili specifici per elementi di questa vista
 */

/**
 * Sezioni informative con spaziatura migliorata - CSS
 */
.mb-3 label.fw-semibold {
    letter-spacing: 0.05em;
    font-size: 0.875rem;
}

/**
 * Link di contatto con transizioni colorate - CSS
 */
a[href^="tel:"],
a[href^="mailto:"] {
    text-decoration: none;
    color: inherit;
    transition: color 0.2s ease;
}

a[href^="tel:"]:hover {
    color: #28a745 !important;
}

a[href^="mailto:"]:hover {
    color: #17a2b8 !important;
}

/**
 * Statistiche con gradiente hover - CSS
 */
.border.rounded.p-3 {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    transition: all 0.2s ease;
}

.border.rounded.p-3:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

/**
 * ===================================================================
 * DARK MODE PREPARAZIONE - CSS
 * ===================================================================
 * Struttura per futuro supporto tema scuro
 */
@media (prefers-color-scheme: dark) {
    /* 
     * Al momento manteniamo tema chiaro per coerenza,
     * ma prepariamo la struttura per futuro dark mode 
     */
    
    /* Esempio variabili CSS per dark mode futuro:
     * :root {
     *   --bg-primary: #1a1a1a;
     *   --text-primary: #ffffff;
     *   --border-color: #404040;
     * }
     */
}

/**
 * ===================================================================
 * FINE STILI PERSONALIZZATI
 * ===================================================================
 */
</style>
@endpush