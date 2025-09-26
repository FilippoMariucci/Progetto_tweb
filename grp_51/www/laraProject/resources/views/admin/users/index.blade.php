{{-- 
    VISTA GESTIONE UTENTI AMMINISTRATORI
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    PERCORSO: resources/views/admin/users/index.blade.php
    ACCESSO: Solo livello 4 (Amministratori)
    
    SCOPO: Vista principale per amministrazione completa di tutti gli utenti del sistema
    
    FUNZIONALITÀ PRINCIPALI:
    - Lista paginata di tutti gli utenti con filtri avanzati
    - Statistiche rapide per tipologia utente (Admin, Staff, Tecnici)
    - Sistema di ricerca e filtri (nome, livello, centro, data registrazione)
    - Azioni CRUD complete per ogni utente (visualizza, modifica, elimina)
    - Azioni pericolose condizionali (reset password, eliminazione)
    - Sistema di ordinamento dinamico
    - Export dati in formato CSV/JSON
    - Interfaccia responsive con layout a colonne
    - Avatar system con colori per livello accesso
    - Protezioni anti-auto-eliminazione
--}}

{{-- EXTENDS: Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- SECTION TITLE: Titolo statico per la gestione utenti --}}
@section('title', 'Gestione Utenti')

{{-- SECTION CONTENT: Inizio del contenuto principale della vista --}}
@section('content')

{{-- CONTAINER: Contenitore normale con margine top --}}
<div class="container mt-4">
    
    {{-- ========== HEADER CON TITOLO E PULSANTE AZIONE ========== --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                LAYOUT HEADER:
                - d-flex con justify-content-between per separare titolo e pulsante
                - align-items-center per allineamento verticale
            --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                
                {{-- SEZIONE SINISTRA: Titolo e descrizione --}}
                <div>
                    <h1 class="h2 mb-1">
                        {{-- 
                            ICONA PEOPLE ROSSA:
                            text-danger per colore rosso che indica funzioni amministrative critiche
                        --}}
                        <i class="bi bi-people text-danger me-2"></i>
                        Gestione Utenti
                    </h1>
                    {{-- Sottotitolo esplicativo --}}
                    <p class="text-muted mb-0">
                        Amministra utenti, tecnici e staff del sistema
                    </p>
                </div>
                
                {{-- SEZIONE DESTRA: Pulsante azione principale --}}
                <div>
                    {{-- 
                        PULSANTE NUOVO UTENTE:
                        - btn-danger per evidenziare azione principale
                        - Link alla route di creazione utente
                    --}}
                    <a href="{{ route('admin.users.create') }}" class="btn btn-danger">
                        <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                    </a>
                </div>
            </div>
            
            {{-- 
                ALERT INFORMATIVO:
                Spiega le funzionalità disponibili agli amministratori
            --}}
            <div class="alert alert-info border-start border-info border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Gestione Completa:</strong> Crea, modifica ed elimina utenti. Gestisci livelli di accesso e assegnazioni.
            </div>
        </div>
    </div>

    {{-- ========== STATISTICHE RAPIDE ========== --}}
    {{-- 
        CARD STATISTICHE: 4 card responsive per mostrare conteggi per tipologia utente
        g-3: Gap di 3 unità tra le colonne
    --}}
    <div class="row g-3 mb-4">
        
        {{-- CARD: Amministratori --}}
        <div class="col-md-3">
            {{-- bg-danger: Sfondo rosso per amministratori --}}
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    {{-- Icona grande per impatto visivo --}}
                    <i class="bi bi-shield-check display-6"></i>
                    
                    {{-- 
                        CONTEGGIO DINAMICO:
                        $stats['admin']: Valore passato dal controller
                        ?? 0: Fallback a zero se null
                    --}}
                    <h4 class="mt-2">{{ $stats['admin'] ?? 0 }}</h4>
                    <small>Amministratori</small>
                </div>
            </div>
        </div>
        
        {{-- CARD: Staff Aziendale --}}
        <div class="col-md-3">
            {{-- bg-warning: Sfondo giallo/arancione per staff --}}
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear display-6"></i>
                    <h4 class="mt-2">{{ $stats['staff'] ?? 0 }}</h4>
                    <small>Staff</small>
                </div>
            </div>
        </div>
        
        {{-- CARD: Tecnici --}}
        <div class="col-md-3">
            {{-- bg-info: Sfondo azzurro per tecnici --}}
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-tools display-6"></i>
                    <h4 class="mt-2">{{ $stats['tecnici'] ?? 0 }}</h4>
                    <small>Tecnici</small>
                </div>
            </div>
        </div>
        
        {{-- CARD: Totale Utenti --}}
        <div class="col-md-3">
            {{-- bg-secondary: Sfondo grigio per totale --}}
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h4 class="mt-2">{{ $stats['totale'] ?? 0 }}</h4>
                    <small>Totale Utenti</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== LAYOUT PRINCIPALE A 2 COLONNE ========== --}}
    <div class="row">
        
        {{-- 
            ========== COLONNA SINISTRA - FILTRI ========== 
            col-lg-3: 3/12 colonne su large (25% larghezza)
        --}}
        <div class="col-lg-3">
            
            {{-- ========== CARD FILTRI ========== --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                
                <div class="card-body">
                    {{-- 
                        FORM FILTRI:
                        - method="GET": Usa GET per permettere bookmark/condivisione URL
                        - action: Stessa route (index) per applicare filtri
                        - Tutti i parametri diventano query string
                    --}}
                    <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                        
                        {{-- ========== CAMPO RICERCA TESTUALE ========== --}}
                        <div class="mb-3">
                            <label for="search" class="form-label">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            
                            {{-- 
                                INPUT RICERCA:
                                - value="{{ request('search') }}": Mantiene valore dopo submit
                                - placeholder: Indica tipi di ricerca supportati
                            --}}
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome, cognome o username">
                        </div>
                        
                        {{-- ========== FILTRO LIVELLO ACCESSO ========== --}}
                        <div class="mb-3">
                            <label for="livello_accesso" class="form-label">
                                <i class="bi bi-shield me-1"></i>Livello Accesso
                            </label>
                            
                            {{-- 
                                SELECT LIVELLI:
                                Ogni opzione usa emoji per migliore UX visiva
                            --}}
                            <select class="form-select" id="livello_accesso" name="livello_accesso">
                                <option value="">Tutti i livelli</option>
                                
                                {{-- 
                                    OPZIONI CON SELEZIONE CONDIZIONALE:
                                    request('livello_accesso') == '4': Controlla parametro GET corrente
                                --}}
                                <option value="4" {{ request('livello_accesso') == '4' ? 'selected' : '' }}>
                                    🔴 Amministratori
                                </option>
                                <option value="3" {{ request('livello_accesso') == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale
                                </option>
                                <option value="2" {{ request('livello_accesso') == '2' ? 'selected' : '' }}>
                                    🔵 Tecnici
                                </option>
                                <option value="1" {{ request('livello_accesso') == '1' ? 'selected' : '' }}>
                                    ⚪ Utenti Pubblici
                                </option>
                            </select>
                        </div>
                        
                        {{-- ========== FILTRO CENTRO ASSISTENZA (CONDIZIONALE) ========== --}}
                        {{-- 
                            CONDIZIONE: Mostra filtro solo se ci sono centri nel database
                            $centri->count() > 0: Verifica che collection non sia vuota
                        --}}
                        @if($centri->count() > 0)
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Centro Assistenza
                                </label>
                                
                                <select class="form-select" id="centro_assistenza_id" name="centro_assistenza_id">
                                    <option value="">Tutti i centri</option>
                                    
                                    {{-- 
                                        LOOP CENTRI: 
                                        Popola dropdown con centri dal database
                                    --}}
                                    @foreach($centri as $centro)
                                        <option value="{{ $centro->id }}" {{ request('centro_assistenza_id') == $centro->id ? 'selected' : '' }}>
                                            {{ $centro->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        {{-- ========== FILTRO DATA REGISTRAZIONE ========== --}}
                        <div class="mb-3">
                            <label for="data_registrazione" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Registrati da
                            </label>
                            
                            {{-- 
                                SELECT DATE PREDEFINITE:
                                Opzioni per filtri temporali comuni
                            --}}
                            <select class="form-select" id="data_registrazione" name="data_registrazione">
                                <option value="">Tutte le date</option>
                                <option value="oggi" {{ request('data_registrazione') == 'oggi' ? 'selected' : '' }}>
                                    Oggi
                                </option>
                                <option value="settimana" {{ request('data_registrazione') == 'settimana' ? 'selected' : '' }}>
                                    Ultima settimana
                                </option>
                                <option value="mese" {{ request('data_registrazione') == 'mese' ? 'selected' : '' }}>
                                    Ultimo mese
                                </option>
                            </select>
                        </div>
                        
                        {{-- ========== PULSANTI FORM ========== --}}
                        {{-- 
                            LAYOUT GRID:
                            d-grid gap-2: Layout verticale con spazio tra pulsanti
                        --}}
                        <div class="d-grid gap-2">
                            {{-- Pulsante submit per applicare filtri --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Applica Filtri
                            </button>
                            
                            {{-- 
                                PULSANTE RESET:
                                Link alla stessa route senza parametri per azzerare filtri
                            --}}
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========== CARD AZIONI RAPIDE ========== --}}
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        {{-- Pulsante principale per creare nuovo utente --}}
                        <a href="{{ route('admin.users.create') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                        
                        {{-- Separatore visuale --}}
                        <hr class="my-2">
                        
                        {{-- 
                            PULSANTE EXPORT:
                            - data-bs-toggle="modal": Apre modal Bootstrap
                            - data-bs-target="#exportModal": Target specifico modal
                        --}}
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i>Esporta Lista
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- 
            ========== COLONNA DESTRA - LISTA UTENTI ========== 
            col-lg-9: 9/12 colonne su large (75% larghezza)
        --}}
        <div class="col-lg-9">
            <div class="card card-custom">
                
                {{-- ========== HEADER LISTA CON CONTEGGIO E ORDINAMENTO ========== --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    
                    {{-- SEZIONE SINISTRA: Titolo con conteggio --}}
                    <h5 class="mb-0">
                        <i class="bi bi-people text-primary me-2"></i>
                        Utenti 
                        {{-- 
                            BADGE CONTATORE:
                            $users->total(): Metodo Laravel pagination per totale record
                        --}}
                        <span class="badge bg-secondary">{{ $users->total() }}</span>
                    </h5>
                    
                    {{-- SEZIONE DESTRA: Dropdown ordinamento --}}
                    <div class="btn-group" role="group">
                        {{-- 
                            PULSANTE DROPDOWN ORDINAMENTO:
                            data-bs-toggle="dropdown": Attributo Bootstrap per dropdown
                        --}}
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-down me-1"></i>Ordina
                        </button>
                        
                        {{-- 
                            MENU ORDINAMENTO:
                            Ogni link mantiene parametri GET esistenti e aggiunge 'sort'
                        --}}
                        <ul class="dropdown-menu">
                            {{-- 
                                FUNZIONE http_build_query():
                                - array_merge(request()->all(), ['sort' => 'nome']): 
                                  Unisce parametri esistenti con nuovo parametro sort
                                - Mantiene filtri applicati quando si cambia ordinamento
                            --}}
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'nome'])) }}">Nome A-Z</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => '-nome'])) }}">Nome Z-A</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'created_at'])) }}">Più Recenti</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => '-created_at'])) }}">Più Vecchi</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'livello_accesso'])) }}">Livello Accesso</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- 
                        CONDIZIONE: Mostra tabella solo se ci sono utenti
                        $users->count() > 0: Verifica che collection abbia elementi
                    --}}
                    @if($users->count() > 0)
                        
                        {{-- ========== TABELLA RESPONSIVE ========== --}}
                        <div class="table-responsive">
                            {{-- table-hover: Effetto hover sulle righe --}}
                            <table class="table table-hover">
                                
                                {{-- INTESTAZIONE TABELLA --}}
                                <thead class="table-light">
                                    <tr>
                                        <th>Utente</th>
                                        <th>Livello</th>
                                        <th>Centro/Specializzazione</th>
                                        <th>Ultimo Accesso</th>
                                        <th>Stato</th>
                                        <th width="150">Azioni</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    {{-- 
                                        LOOP UTENTI:
                                        @foreach itera attraverso collection utenti paginati
                                    --}}
                                    @foreach($users as $user)
                                        {{-- 
                                            RIGA UTENTE:
                                            - user-row: Classe CSS per styling
                                            - data-user-id: Attributo dati per JavaScript
                                        --}}
                                        <tr class="user-row" data-user-id="{{ $user->id }}">
                                            
                                            {{-- ========== CELLA UTENTE CON AVATAR ========== --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- 
                                                        AVATAR COLORATO PER LIVELLO:
                                                        Ternari nested per determinare colore background:
                                                        - Livello 4 (Admin): danger (rosso)
                                                        - Livello 3 (Staff): warning (giallo)
                                                        - Livello 2 (Tecnico): info (azzurro)
                                                        - Default: secondary (grigio)
                                                    --}}
                                                    <div class="avatar-circle bg-{{ $user->livello_accesso == '4' ? 'danger' : ($user->livello_accesso == '3' ? 'warning' : ($user->livello_accesso == '2' ? 'info' : 'secondary')) }} text-white me-3">
                                                        {{-- 
                                                            INIZIALI AVATAR:
                                                            - strtoupper(): Converte in maiuscolo
                                                            - substr(): Estrae primo carattere nome e cognome
                                                        --}}
                                                        {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                                                    </div>
                                                    
                                                    <div>
                                                        {{-- 
                                                            NOME COMPLETO:
                                                            nome_completo: Accessor nel model User
                                                        --}}
                                                        <h6 class="mb-1">{{ $user->nome_completo }}</h6>
                                                        <small class="text-muted">{{ $user->username }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- ========== CELLA LIVELLO ACCESSO ========== --}}
                                            <td>
                                                {{-- 
                                                    BADGE LIVELLO PERSONALIZZATO:
                                                    - badge-livello: Classe base CSS
                                                    - badge-livello-{{ $user->livello_accesso }}: Classe dinamica
                                                    - livello_descrizione: Accessor per testo user-friendly
                                                --}}
                                                <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }}">
                                                    {{ $user->livello_descrizione }}
                                                </span>
                                            </td>
                                            
                                            {{-- ========== CELLA INFO SPECIFICHE PER RUOLO ========== --}}
                                            <td>
                                                {{-- 
                                                    CONDIZIONI MULTIPLE PER VISUALIZZARE INFO SPECIFICHE:
                                                    Diverse informazioni basate sul tipo di utente
                                                --}}
                                                
                                                {{-- TECNICO CON CENTRO ASSEGNATO --}}
                                                @if($user->isTecnico() && $user->centroAssistenza)
                                                    <div>
                                                        {{-- Nome centro assistenza --}}
                                                        <strong>{{ $user->centroAssistenza->nome }}</strong>
                                                        <br>
                                                        {{-- 
                                                            SPECIALIZZAZIONE CON FALLBACK:
                                                            ?? 'N/A': Se specializzazione è null, mostra N/A
                                                        --}}
                                                        <small class="text-muted">{{ $user->specializzazione ?? 'N/A' }}</small>
                                                    </div>
                                                
                                                {{-- STAFF AZIENDALE --}}
                                                @elseif($user->isStaff())
                                                    <div>
                                                        <strong>Staff Aziendale</strong>
                                                        <br>
                                                        {{-- 
                                                            CONTEGGIO PRODOTTI ASSEGNATI:
                                                            prodottiAssegnati(): Relazione hasMany
                                                            ->count(): Conta record correlati
                                                        --}}
                                                        <small class="text-muted">{{ $user->prodottiAssegnati()->count() }} prodotti</small>
                                                    </div>
                                                
                                                {{-- ALTRI CASI (Admin, Pubblico) --}}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            
                                            {{-- ========== CELLA ULTIMO ACCESSO ========== --}}
                                            <td>
                                                {{-- 
                                                    ULTIMO LOGIN CONDIZIONALE:
                                                    Se campo last_login_at non è null
                                                --}}
                                                @if($user->last_login_at)
                                                    {{-- 
                                                        SPAN CON TOOLTIP:
                                                        - title: Tooltip HTML nativo con data completa
                                                        - diffForHumans(): Carbon method per formato relativo
                                                    --}}
                                                    <span title="{{ $user->last_login_at->format('d/m/Y H:i') }}">
                                                        {{ $user->last_login_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Mai</span>
                                                @endif
                                            </td>
                                            
                                            {{-- ========== CELLA STATO UTENTE ========== --}}
                                            <td>
                                                {{-- 
                                                    STATO ATTIVO/SOSPESO:
                                                    ?? true: Default a true se campo attivo non esiste
                                                --}}
                                                @if($user->attivo ?? true)
                                                    <span class="badge bg-success">Attivo</span>
                                                @else
                                                    <span class="badge bg-danger">Sospeso</span>
                                                @endif
                                            </td>
                                            
                                            {{-- ========== CELLA AZIONI ========== --}}
                                            <td>
                                                <div class="btn-group" role="group">
                                                    
                                                    {{-- PULSANTE VISUALIZZA --}}
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Visualizza">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    {{-- PULSANTE MODIFICA --}}
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="Modifica">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    {{-- ========== DROPDOWN AZIONI AGGIUNTIVE ========== --}}
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        
                                                        <ul class="dropdown-menu">
                                                            {{-- 
                                                                CONDIZIONE ANTI-AUTO-AZIONE:
                                                                Azioni pericolose solo se NON è l'utente corrente
                                                                $user->id !== auth()->id(): Previene auto-eliminazione
                                                            --}}
                                                            @if($user->id !== auth()->id())
                                                                
                                                                {{-- ========== RESET PASSWORD ========== --}}
                                                                <li>
                                                                    {{-- 
                                                                        FORM RESET PASSWORD:
                                                                        - style="display: inline;": Per stare nel dropdown
                                                                        - onclick: Conferma JavaScript
                                                                    --}}
                                                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Resettare la password?')">
                                                                            <i class="bi bi-key me-2"></i>Reset Password
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                
                                                                {{-- LINK VISUALIZZA DETTAGLI --}}
                                                                <li>
                                                                    <a href="{{ route('admin.users.show', $user) }}" class="dropdown-item">
                                                                        <i class="bi bi-info-circle me-2"></i>Visualizza Dettagli
                                                                    </a>
                                                                </li>
                                                                
                                                                {{-- Separatore prima dell'azione distruttiva --}}
                                                                <li><hr class="dropdown-divider"></li>
                                                                
                                                                {{-- ========== ELIMINAZIONE UTENTE ========== --}}
                                                                <li>
                                                                    {{-- 
                                                                        FORM ELIMINAZIONE:
                                                                        - @method('DELETE'): Method spoofing per HTTP DELETE
                                                                        - onclick: Doppia conferma per azione irreversibile
                                                                        - text-danger: Colore rosso per azione pericolosa
                                                                    --}}
                                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="dropdown-item text-danger" 
                                                                                onclick="return confirm('ATTENZIONE: Eliminare questo utente?\n\nQuesta azione non può essere annullata.')">
                                                                            <i class="bi bi-trash me-2"></i>Elimina Utente
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                {{-- CASO UTENTE CORRENTE: Messaggio speciale --}}
                                                                <li><span class="dropdown-item text-muted">
                                                                    <i class="bi bi-person-check me-2"></i>Sei tu!
                                                                </span></li>
                                                                <li>
                                                                    {{-- Elemento vuoto per spaziatura --}}
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ========== PAGINAZIONE ========== --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            
                            {{-- SEZIONE SINISTRA: Info paginazione --}}
                            <div>
                                <small class="text-muted">
                                    {{-- 
                                        INFORMAZIONI PAGINAZIONE:
                                        - firstItem(): Primo elemento pagina corrente
                                        - lastItem(): Ultimo elemento pagina corrente  
                                        - total(): Totale elementi in tutte le pagine
                                        - ?? 0: Fallback se valori sono null
                                    --}}
                                    Mostrando {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} 
                                    di {{ $users->total() }} utenti
                                </small>
                            </div>
                            
                            {{-- SEZIONE DESTRA: Link paginazione --}}
                            <div>
                                {{-- 
                                    LINK PAGINAZIONE AUTOMATICI:
                                    Laravel genera automaticamente i link prev/next/numeri
                                    Mantiene tutti i parametri GET esistenti (filtri)
                                --}}
                                {{ $users->links() }}
                            </div>
                        </div>
                    
                    {{-- ========== STATO VUOTO ========== --}}
                    @else
                        {{-- 
                            MESSAGGIO NESSUN UTENTE:
                            Stato vuoto user-friendly con call-to-action
                        --}}
                        <div class="text-center py-5">
                            {{-- Icona grande per impatto visivo --}}
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun utente trovato</h5>
                            <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                            
                            {{-- Call-to-action per creare primo utente --}}
                            <a href="{{ route('admin.users.create') }}" class="btn btn-danger">
                                <i class="bi bi-person-plus me-1"></i>Crea Primo Utente
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL EXPORT ========== --}}
{{-- 
    MODAL BOOTSTRAP: Finestra modale per esportazione dati
    - fade: Effetto dissolvenza
    - tabindex="-1": Per accessibilità keyboard navigation
--}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            
            {{-- HEADER MODAL --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-download me-2"></i>Esporta Lista Utenti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- CORPO MODAL --}}
            <div class="modal-body">
                {{-- 
                    FORM EXPORT:
                    - action: Route specifica per export completo
                    - method="POST": POST per sicurezza (evita GET con parametri sensibili)
                --}}
                <form action="{{ route('admin.export.all') }}" method="POST">
                    @csrf
                    
                    {{-- SCELTA FORMATO EXPORT --}}
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Formato Export:</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="csv">CSV (Excel)</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    {{-- ALERT INFORMATIVO --}}
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            L'export includerà tutti i dati del sistema (utenti, prodotti, malfunzionamenti e centri assistenza)
                        </small>
                    </div>
                    
                    {{-- PULSANTE EXPORT --}}
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Scarica Export Completo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ========== SEZIONE CSS PERSONALIZZATI ========== --}}
{{-- 
    PUSH STYLES: CSS specifici per questa vista di gestione utenti
    LINGUAGGIO: CSS con sintassi standard
--}}
@push('styles')
<style>
/* 
    === STILI CARD PERSONALIZZATE ===
    Sistema uniforme per tutte le card della vista
*/
.card-custom {
    border: none; /* Rimuove bordo default Bootstrap */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Ombra sottile per depth */
    transition: all 0.3s ease; /* Transizione fluida per hover */
}

/* 
    === AVATAR CIRCOLARE ===
    Avatar più piccolo per tabella compatta
*/
.avatar-circle {
    width: 40px; /* Dimensione ridotta per tabella */
    height: 40px;
    border-radius: 50%; /* Forma perfettamente circolare */
    display: flex; /* Layout flex per centrare contenuto */
    align-items: center; /* Centra verticalmente */
    justify-content: center; /* Centra orizzontalmente */
    font-weight: bold; /* Testo grassetto per iniziali */
    font-size: 0.9rem; /* Font size appropriato per dimensione ridotta */
}

/* 
    === STILI TABELLA ===
    Personalizzazioni per table responsive
*/
.table th {
    border-top: none; /* Rimuove bordo superiore dalle intestazioni */
    font-weight: 600; /* Semi-grassetto per intestazioni */
    color: #495057; /* Grigio scuro per leggibilità */
}

/* 
    === BADGE LIVELLI ===
    Sistema di colori per livelli accesso
*/
.badge-livello {
    font-size: 0.75rem; /* Dimensione standard per badge */
}

/* Colori specifici per ogni livello (stesso sistema del form edit) */
.badge-livello-4 { 
    background-color: #dc3545; /* Rosso per Admin */
}

.badge-livello-3 { 
    background-color: #ffc107; /* Giallo per Staff */
    color: #000; /* Testo nero per contrasto su giallo */
}

.badge-livello-2 { 
    background-color: #0dcaf0; /* Azzurro per Tecnico */
    color: #000; /* Testo nero per contrasto */
}

.badge-livello-1 { 
    background-color: #6c757d; /* Grigio per Pubblico */
}

/* 
    === HOVER EFFECTS ===
    Effetti interattivi per migliorare UX
*/

/* Hover su righe tabella */
.user-row:hover {
    background-color: #f8f9fa; /* Sfondo grigio chiaro al hover */
}

/* Dimensione minima dropdown per leggibilità */
.btn-group .dropdown-menu {
    min-width: 150px;
}

/* 
    === RESPONSIVE DESIGN ===
    Adattamenti per dispositivi mobili
*/
@media (max-width: 768px) {
    /* Avatar più piccolo su mobile */
    .avatar-circle {
        width: 35px;
        height: 35px;
        font-size: 0.8rem;
    }
    
    /* Tabella responsive migliorata */
    .table-responsive {
        font-size: 0.875rem; /* Font leggermente più piccolo */
    }
    
    /* Pulsanti più compatti su mobile */
    .btn-group .btn-sm {
        padding: 0.25rem 0.4rem;
    }
}

/* 
    === UTILITÀ AGGIUNTIVE ===
    Classi helper per layout specifici
*/

/* Separatori dropdown con stile personalizzato */
.dropdown-divider {
    margin: 0.5rem 0;
    opacity: 0.3;
}

/* Miglioramento accessibilità focus */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: none;
}

/* Stati loading per pulsanti */
.btn.loading {
    pointer-events: none;
    opacity: 0.6;
    position: relative;
}

/* 
    === DARK MODE SUPPORT ===
    Supporto base per tema scuro (se implementato)
*/
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .table {
        color: #e2e8f0;
    }
    
    .table th {
        color: #a0aec0;
        border-bottom-color: #4a5568;
    }
    
    .user-row:hover {
        background-color: #4a5568;
    }
}
</style>
@endpush

{{-- ========== SEZIONE JAVASCRIPT ========== --}}
{{-- 
    PUSH SCRIPTS: JavaScript per funzionalità interattive
    LINGUAGGIO: JavaScript embedded in Blade
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA:
    Pattern standard per trasferimento dati PHP → JavaScript
    Consistente con tutti gli altri file dell'applicazione
*/

// Inizializza oggetto globale se non esiste già
window.PageData = window.PageData || {};

/*
    TRASFERIMENTO DATI CONDIZIONALE:
    Solo dati effettivamente presenti vengono trasferiti
    Evita errori e ottimizza performance
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

// Dati singolo centro (se presente)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Collezione centri (se presente)  
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Array categorie (se presente)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Collezione staff (se presente)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche (se presente - molto rilevante per questa vista)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente corrente (se presente)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    FUNZIONALITÀ JAVASCRIPT SPECIFICHE PER GESTIONE UTENTI:
    Le seguenti funzioni dovrebbero essere implementate in file JS separato
    
    FUNZIONI RICHIESTE:
    1. initializeFilters() - Setup filtri automatici
    2. handleBulkActions() - Azioni multiple su utenti selezionati  
    3. confirmDangerousActions() - Conferme per azioni pericolose
    4. updateUserStatus() - Toggle stato utente AJAX
    5. exportUsers() - Gestione export con progress
    6. refreshUsersList() - Ricarica lista senza page reload
    
    EVENTI DA GESTIRE:
    - Change su filtri (auto-submit o live search)
    - Click su azioni pericolose (conferme multiple)
    - Hover su righe utenti (preview info aggiuntive)
    - Resize finestra (responsive table adjustments)
    - Submit form export (progress indicator)
    
    ESEMPIO IMPLEMENTAZIONE:
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Auto-submit filtri con debounce
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('search');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500); // Debounce di 500ms
            });
        }
        
        // Auto-submit per select filters
        const filterSelects = filterForm.querySelectorAll('select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
        
        // Conferme avanzate per azioni pericolose
        const dangerousForms = document.querySelectorAll('form[action*="destroy"], form[action*="reset-password"]');
        dangerousForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = form.action;
                const userName = this.closest('tr').querySelector('h6').textContent;
                
                let message = '';
                if (action.includes('destroy')) {
                    message = `ATTENZIONE: Eliminare definitivamente ${userName}?\n\nQuesta azione:\n- Rimuoverà tutti i dati dell'utente\n- Non può essere annullata\n- Potrebbe influenzare prodotti assegnati\n\nContinuare?`;
                } else if (action.includes('reset-password')) {
                    message = `Resettare la password per ${userName}?\n\nL'utente dovrà impostare una nuova password al prossimo accesso.`;
                }
                
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
        
        // Tooltip per date ultimo accesso
        const dateElements = document.querySelectorAll('[title]');
        dateElements.forEach(element => {
            // Tooltip nativo o libreria tooltip se disponibile
            if (typeof bootstrap !== 'undefined') {
                new bootstrap.Tooltip(element);
            }
        });
        
        // Progress indicator per export
        const exportForm = document.querySelector('#exportModal form');
        if (exportForm) {
            exportForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generando Export...';
                submitBtn.disabled = true;
                
                // Reset dopo 5 secondi in caso di problemi
                setTimeout(() => {
                    submitBtn.innerHTML = '<i class="bi bi-download me-1"></i>Scarica Export Completo';
                    submitBtn.disabled = false;
                }, 5000);
            });
        }
        
        // Refresh periodico statistiche (ogni 30 secondi)
        setInterval(() => {
            refreshStats();
        }, 30000);
        
        function refreshStats() {
            fetch(window.location.pathname + '?ajax=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.stats) {
                        updateStatsDisplay(data.stats);
                    }
                })
                .catch(error => {
                    console.log('Stats refresh failed:', error);
                });
        }
        
        function updateStatsDisplay(stats) {
            document.querySelector('.bg-danger h4').textContent = stats.admin || 0;
            document.querySelector('.bg-warning h4').textContent = stats.staff || 0;  
            document.querySelector('.bg-info h4').textContent = stats.tecnici || 0;
            document.querySelector('.bg-secondary h4').textContent = stats.totale || 0;
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+N = Nuovo utente
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route('admin.users.create') }}';
            }
            
            // Ctrl+F = Focus su ricerca
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search').focus();
            }
        });
    });
    
    NOTA: Implementazione esempio per riferimento.
    In produzione dovrebbe essere ottimizzato e in file separato.
*/

</script>
@endpush

{{--
    =========================================
    === RIEPILOGO FUNZIONALITÀ FILE ===
    =========================================
    
    QUESTO FILE IMPLEMENTA:
    
    1. LISTA UTENTI COMPLETA:
       - Tabella responsive con avatar colorati per livello
       - Paginazione Laravel con mantenimento filtri
       - Informazioni specifiche per ruolo utente
       - Stati e date ultimo accesso
    
    2. SISTEMA FILTRI AVANZATO:
       - Ricerca testuale multi-campo
       - Filtri per livello accesso
       - Filtro per centro assistenza (condizionale)
       - Filtri temporali predefiniti
       - Mantenimento stato filtri durante navigazione
    
    3. STATISTICHE DASHBOARD:
       - Conteggi per tipologia utente (Admin/Staff/Tecnici)
       - Card colorate per identificazione rapida
       - Aggiornamento dinamico possibile
    
    4. AZIONI AMMINISTRATIVE:
       - CRUD completo per ogni utente
       - Azioni pericolose con conferme multiple
       - Protezione anti-auto-eliminazione
       - Reset password con sicurezza
       - Export completo sistema
    
    5. UX/UI AVANZATA:
       - Sistema ordinamento dinamico con mantenimento filtri
       - Avatar con iniziali e colori per livello
       - Hover effects e transizioni fluide
       - Modal per export con opzioni formato
       - Responsive design per tutti i dispositivi
    
    PATTERN TECNICI:
    - Query string building per ordinamento + filtri
    - Laravel pagination con appends automatico
    - Method spoofing per azioni DELETE
    - Conditional rendering basato su ruoli
    - Collection methods (count, relationships)
    - Carbon date formatting con fallbacks
    
    INTEGRAZIONE SISTEMA:
    - Route: admin.users.index (GET) con parametri filtri
    - Route: admin.users.destroy (DELETE) per eliminazione
    - Route: admin.users.reset-password (POST) per reset
    - Route: admin.export.all (POST) per export completo
    - Controller: UserController con filtri, search, stats
    - Middleware: auth, admin per protezione accesso
    - Database: users table con relazioni centri_assistenza
    
    SICUREZZA:
    - Protezione anti-auto-eliminazione amministratore
    - CSRF token su tutte le form POST/DELETE
    - Conferme JavaScript per azioni irreversibili
    - Validazione parametri GET per filtri
    - Escape HTML automatico Blade per XSS prevention
--}}