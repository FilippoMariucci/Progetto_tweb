{{-- 
    VISTA BLADE PHP: Dettagli Utente per Amministratori
    File: resources/views/admin/users/show.blade.php
    
    Descrizione:
    Questa è una vista Blade (template engine di Laravel) che mostra i dettagli completi
    di un utente specifico. È accessibile solo agli amministratori (livello 4).
    
    Funzionalità principali:
    - Visualizzazione informazioni personali complete
    - Gestione ruoli e permessi utente
    - Statistiche di utilizzo
    - Azioni amministrative (modifica, reset password, eliminazione)
    - Informazioni specifiche per ruolo (tecnico/staff)
    
    Variabili ricevute dal Controller:
    - $user: Oggetto User con tutti i dati dell'utente da visualizzare
    - $stats: Array con statistiche specifiche per ruolo utente
    - $attivitaRecente: Array con attività recenti dell'utente (opzionale)
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- 
    Definisce il titolo dinamico della pagina che apparirà nel tag <title> dell'HTML
    Concatena "Dettagli Utente - " con il nome completo dell'utente
--}}
@section('title', 'Dettagli Utente - ' . $user->nome_completo)

{{-- Inizia la sezione content che sarà inserita nel layout principale --}}
@section('content')
<div class="container mt-4">
    
    {{-- 
        === SEZIONE BREADCRUMB ===
        Navigazione breadcrumb per mostrare il percorso di navigazione all'utente
        Utilizza Bootstrap per lo styling e icone Bootstrap Icons
    --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            {{-- Link alla homepage --}}
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="bi bi-house-door me-1"></i>Home
                </a>
            </li>
            {{-- Link alla dashboard amministratore --}}
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                </a>
            </li>
            {{-- Link alla lista utenti --}}
            <li class="breadcrumb-item">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                    <i class="bi bi-people me-1"></i>Gestione Utenti
                </a>
            </li>
            {{-- Elemento corrente (non cliccabile) --}}
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-eye me-1"></i>{{ $user->nome_completo }}
            </li>
        </ol>
    </nav>

    {{-- 
        === SEZIONE HEADER ===
        Header principale della pagina con informazioni utente e azioni principali
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                {{-- Informazioni utente con avatar --}}
                <div class="d-flex align-items-center">
                    {{-- 
                        Avatar circolare con iniziali utente
                        Colore di sfondo dinamico basato sul livello di accesso:
                        - Livello 4 (Admin): rosso (danger)
                        - Livello 3 (Staff): giallo (warning) 
                        - Livello 2 (Tecnico): azzurro (info)
                        - Altri: grigio (secondary)
                    --}}
                    <div class="avatar-circle bg-{{ $user->livello_accesso == '4' ? 'danger' : ($user->livello_accesso == '3' ? 'warning' : ($user->livello_accesso == '2' ? 'info' : 'secondary')) }} text-white me-3">
                        {{-- 
                            Genera iniziali prendendo prima lettera di nome e cognome
                            strtoupper() converte in maiuscolo
                            substr() estrae il primo carattere
                        --}}
                        {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                    </div>
                    <div>
                        {{-- Nome completo utente come titolo principale --}}
                        <h1 class="h2 mb-1">{{ $user->nome_completo }}</h1>
                        <p class="text-muted mb-0">
                            {{-- 
                                Badge per il livello di accesso
                                Classe CSS dinamica: badge-livello-{numero_livello}
                            --}}
                            <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }} me-2">
                                {{-- 
                                    $user->livello_descrizione è un accessor nel Model User
                                    che restituisce una descrizione leggibile del livello
                                --}}
                                {{ $user->livello_descrizione }}
                            </span>
                            {{-- Mostra username se presente --}}
                            @if($user->username)
                                <code>{{ $user->username }}</code>
                            @endif
                        </p>
                    </div>
                </div>
                <h4 class="mt-2 mb-1">{{ $user->livello_descrizione }}</h4>
                                    <small class="text-muted">Tecnico Specializzato</small>
                                </div>
                            </div>
                            {{-- Informazioni del centro di assistenza se assegnato --}}
                            @if($user->centroAssistenza)
                                <div class="col-12">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded">
                                        <i class="bi bi-building text-secondary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $user->centroAssistenza->nome }}</h4>
                                        <small class="text-muted">Centro Assegnato</small>
                                    </div>
                                </div>
                            @endif
                        
                        {{-- 
                            === STATISTICHE PER ALTRI RUOLI ===
                            Fallback per amministratori o utenti senza ruolo specifico
                        --}}
                        @else
                            <div class="col-12">
                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                    <i class="bi bi-person text-info fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $user->livello_descrizione }}</h4>
                                    <small class="text-muted">Ruolo Sistema</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 
                === CARD INFORMAZIONI ACCOUNT ===
                Dettagli tecnici sull'account utente
            --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Informazioni Account
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Livello di accesso numerico --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Livello Accesso:</span>
                        <span class="badge bg-info">Livello {{ $user->livello_accesso }}</span>
                    </div>
                    
                    {{-- Data di registrazione --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Account creato:</span>
                        <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    {{-- 
                        Ultimo login se disponibile
                        Campo tracciato automaticamente dal sistema di autenticazione
                    --}}
                    @if($user->last_login_at)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Ultimo Login:</span>
                            <span title="{{ $user->last_login_at->format('d/m/Y H:i') }}">
                                {{-- Tooltip con data completa, testo con tempo relativo --}}
                                {{ $user->last_login_at->diffForHumans() }}
                            </span>
                        </div>
                    @else
                        {{-- Avviso se l'utente non ha mai fatto login --}}
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small>Non ha mai effettuato l'accesso</small>
                        </div>
                    @endif

                    {{-- 
                        Stato account sempre attivo
                        Nota: non c'è più la funzione di sospensione utenti
                    --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Stato Account:</span>
                        <span class="badge bg-success">Attivo</span>
                    </div>
                </div>
            </div>

            {{-- 
                === CARD AZIONI RAPIDE ===
                Raccolta di link e azioni frequenti per l'amministratore
            --}}
            <div class="card card-custom">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        d-grid gap-2 crea una griglia con spaziatura uniforme
                        Tutti i pulsanti avranno larghezza completa
                    --}}
                    <div class="d-grid gap-2">
                        {{-- Link per tornare alla lista utenti --}}
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Lista
                        </a>
                        
                        {{-- Azioni disponibili solo se non è l'utente corrente --}}
                        @if($user->id !== auth()->id())
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil me-1"></i>Modifica Utente
                            </a>
                            
                            {{-- 
                                Form per reset password
                                Stile inline per mantenere il pulsante nella griglia
                            --}}
                            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-sm w-100" onclick="return confirm('Resettare la password per {{ $user->nome_completo }}?\n\nVerrà generata una password temporanea.')">
                                    <i class="bi bi-key me-1"></i>Reset Password
                                </button>
                            </form>
                        @endif

                        {{-- Link gestione prodotti solo per staff --}}
                        @if($user->isStaff())
                            <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $user->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-seam me-1"></i>Gestisci Prodotti
                            </a>
                        @endif

                        {{-- Separatore visivo --}}
                        <hr class="my-2">
                        
                        {{-- Link per creare nuovo utente --}}
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    === SEZIONE JAVASCRIPT ===
    Script inline che definisce variabili globali per JavaScript esterno
    Queste variabili saranno accessibili da file JS inclusi successivamente
--}}
<script>
    /*
     * Variabile booleana che indica se l'utente corrente può modificare questo utente
     * Convertita in string perché JavaScript riceverà una stringa
     */
    window.canEditUser = {{ $user->id !== auth()->id() ? 'true' : 'false' }};
    
    /*
     * URL per la pagina di modifica di questo utente specifico
     * Generato dinamicamente usando l'helper route() di Laravel
     */
    window.editUserUrl = "{{ route('admin.users.edit', $user) }}";
    
    /*
     * URL per tornare alla lista completa degli utenti
     */
    window.usersIndexUrl = "{{ route('admin.users.index') }}";

    /*
     * Dati completi dell'utente in formato JSON
     * @json è una direttiva Blade che converte oggetti PHP in JSON sicuro
     * Utilizzabile da JavaScript per operazioni client-side
     */
    window.userData = @json($user);
</script>
@endsection

{{-- 
    === SEZIONE STILI CSS PERSONALIZZATI ===
    Utilizza @push per aggiungere stili al layout principale
    Questi stili sono specifici per questa vista
--}}
@push('styles')
<style>
/* 
 * Stile personalizzato per le card
 * Effetto hover per migliorare l'interattività
 */
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease; /* Transizione fluida per hover */
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15); /* Ombra più pronunciata al hover */
}

/* 
 * Stile per l'avatar circolare nell'header
 */
.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%; /* Rende il div perfettamente circolare */
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
    border: 3px solid rgba(255, 255, 255, 0.2); /* Bordo semi-trasparente */
}

/* 
 * Stili per i badge dei livelli di accesso
 * Ogni livello ha un colore specifico per identificazione rapida
 */
.badge-livello {
    font-size: 0.75rem;
    padding: 0.5em 0.8em;
    border-radius: 0.375rem;
}

.badge-livello-4 { 
    background-color: #dc3545; /* Rosso per amministratori */
    color: white;
}
.badge-livello-3 { 
    background-color: #ffc107; /* Giallo per staff */
    color: #000; 
}
.badge-livello-2 { 
    background-color: #0dcaf0; /* Azzurro per tecnici */
    color: #000; 
}
.badge-livello-1 { 
    background-color: #6c757d; /* Grigio per altri */
    color: white;
}

/* 
 * Stili per tabelle senza bordi nelle card informazioni
 */
.table-borderless td {
    border: none;
    padding: 0.5rem 0;
    vertical-align: middle;
}

.table-borderless .fw-semibold {
    min-width: 140px; /* Larghezza minima per allineamento etichette */
    white-space: nowrap; /* Previene il wrap del testo */
}

/* 
 * Bordi per immagini prodotti nelle card staff
 */
.card .card-body .d-flex img,
.card .card-body .d-flex .bg-light {
    border: 1px solid #e9ecef;
}

/* 
 * === RESPONSIVE DESIGN ===
 * Adattamenti per dispositivi mobili
 */
@media (max-width: 768px) {
    .avatar-circle {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .table-borderless .fw-semibold {
        min-width: auto;
        font-size: 0.9rem;
    }
}

/* 
 * Effetti hover per pulsanti di azione
 * Micro-animazione per feedback visivo
 */
.btn-outline-secondary:hover,
.btn-outline-info:hover,
.btn-outline-primary:hover,
.btn-outline-success:hover {
    transform: translateY(-1px); /* Leggero movimento verso l'alto */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* 
 * Classi utility per le statistiche
 * bg-opacity-10 crea sfondi semi-trasparenti
 */
.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.fs-1 {
    font-size: 2.5rem !important; /* Icone grandi per le statistiche */
}
</style>
@endpush

{{-- 
    === SEZIONE SCRIPT AGGIUNTIVI ===
    Script JavaScript specifici per questa vista
--}}
@push('scripts')
<script>
    /*
     * Ridefinizione delle variabili globali per compatibilità
     * Alcune parti del codice potrebbero aspettarsi questi nomi
     */
    window.canEditUser = {{ $user->id !== auth()->id() ? 'true' : 'false' }};
    window.editUserUrl = "{{ route('admin.users.edit', $user) }}";
    window.usersIndexUrl = "{{ route('admin.users.index') }}";
</script>
<script>
    /*
     * === SISTEMA DI GESTIONE DATI GLOBALI ===
     * Inizializza o estende l'oggetto PageData per condividere dati tra script
     * Questo pattern evita conflitti tra diverse viste che potrebbero
     * definire le stesse variabili globali
     */
    
    // Inizializza l'oggetto se non esiste già
    window.PageData = window.PageData || {};

    /*
     * Aggiungi dati specifici solo se sono presenti nella vista
     * Utilizza isset() PHP per controllare l'esistenza prima della conversione JSON
     * Questo approccio condizionale evita errori e mantiene il codice flessibile
     */

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

    /*
     * Questo pattern può essere esteso per aggiungere altri dati
     * che potrebbero essere necessari per funzionalità JavaScript avanzate
     * come filtri dinamici, validazione client-side, etc.
     */
</script>
@endpush