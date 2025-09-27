{{--
    ===================================================================
    PAGINA LOGIN - Vista Blade Corretta - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/auth/login.blade.php
    
    DESCRIZIONE:
    Vista per l'autenticazione degli utenti nel sistema di assistenza tecnica.
    Fornisce interfaccia di login con validazione, credenziali di test e informazioni sui livelli.
    
    LINGUAGGIO: Blade PHP (Template Engine di Laravel)
    
    FUNZIONALITÀ PRINCIPALI:
    - Form di login con validazione client e server-side
    - Credenziali di test per ambiente di sviluppo
    - Informazioni sui livelli di accesso (Tecnici, Staff, Admin)
    - Interfaccia responsive e accessibile
    - Toggle password visibility
    - Auto-fill credenziali per testing
    ===================================================================
--}}

{{-- 
    ESTENSIONE DEL LAYOUT BASE
    Blade PHP: @extends eredita il layout 'layouts.app'
    La vista login utilizza lo stesso layout dell'applicazione principale
--}}
@extends('layouts.app')

{{-- 
    DEFINIZIONE TITOLO PAGINA
    Blade PHP: @section('title') imposta il titolo nel tag <title> HTML
    Importante per SEO e identificazione scheda browser
--}}
@section('title', 'Accedi')

{{-- 
    INIZIO SEZIONE CONTENUTO PRINCIPALE
    Blade PHP: @section('content') definisce il contenuto che sarà inserito nel layout
--}}
@section('content')
<div class="container">
    <div class="row justify-content-center">
        {{-- 
            COLONNA RESPONSIVE PER FORM LOGIN
            Bootstrap: col-md-6 col-lg-5 per dimensioni ottimizzate
            Su mobile: 100% larghezza, su tablet: 50%, su desktop: 41.67%
        --}}
        <div class="col-md-6 col-lg-5">
            
            {{-- === CARD LOGIN === 
                HTML: Card Bootstrap per contenitore principale form di autenticazione
            --}}
            <div class="card card-custom shadow-lg">
                {{-- 
                    HEADER CARD CON BRANDING
                    Bootstrap: bg-primary per colore aziendale, text-center per allineamento
                --}}
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="h3 mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Accesso al Sistema
                    </h2>
                    <p class="mb-0 mt-2 text-white-50">
                        Inserisci le tue credenziali per accedere
                    </p>
                </div>
                
                <div class="card-body p-4">
                    
                    {{-- 
                        FORM DI LOGIN PRINCIPALE
                        HTML: Form con method POST per sicurezza credenziali
                        Laravel: action="{{ route('login') }}" utilizza route named per flessibilità
                        HTML: id="loginForm" per targeting JavaScript
                    --}}
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        {{-- 
                            TOKEN CSRF PER SICUREZZA
                            Laravel: @csrf genera hidden input con token anti-CSRF
                            Protegge da attacchi Cross-Site Request Forgery
                            Il token viene validato automaticamente dal middleware VerifyCsrfToken
                        --}}
                        @csrf
                        
                        {{-- 
                            CAMPO USERNAME
                            HTML: Input text per nome utente (non email per requisiti progetto)
                        --}}
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Username
                            </label>
                            {{-- 
                                INPUT USERNAME CON VALIDAZIONE
                                HTML: Attributi per accessibilità e UX
                                Laravel: @error aggiunge classe is-invalid se ci sono errori di validazione
                                Laravel: old('username') ripopola campo in caso di errore (flash data)
                            --}}
                            <input type="text" 
                                   class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autocomplete="username" 
                                   autofocus
                                   placeholder="Inserisci il tuo username">
                            
                            {{-- 
                                MESSAGGIO ERRORE USERNAME
                                Blade PHP: @error('username') verifica se esistono errori per questo campo
                                Laravel: $message contiene il messaggio di errore di validazione
                                Bootstrap: invalid-feedback per styling errori
                            --}}
                            @error('username')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- CAMPO PASSWORD --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            {{-- 
                                INPUT GROUP PER PASSWORD CON TOGGLE VISIBILITY
                                Bootstrap: input-group per combinare input e pulsante
                                UX: Permette all'utente di vedere/nascondere password digitata
                            --}}
                            <div class="input-group">
                                {{-- 
                                    INPUT PASSWORD
                                    HTML: type="password" nasconde caratteri per sicurezza
                                    HTML: autocomplete="current-password" aiuta password manager
                                --}}
                                <input type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Inserisci la tua password">
                                {{-- 
                                    PULSANTE TOGGLE VISIBILITÀ PASSWORD
                                    JavaScript: id="togglePassword" per event binding
                                    Bootstrap: data-bs-toggle="tooltip" per tooltip informativo
                                    UX: Migliora usabilità permettendo verifica password
                                --}}
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                        data-bs-toggle="tooltip"
                                        title="Mostra/Nascondi password">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            
                            {{-- MESSAGGIO ERRORE PASSWORD --}}
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 
                            CHECKBOX RICORDAMI
                            Laravel: Implementa funzionalità "Remember Me" del sistema auth
                            Cookie di sessione estesa per non dover rifare login frequentemente
                        --}}
                        <div class="mb-4">
                            <div class="form-check">
                                {{-- 
                                    CHECKBOX REMEMBER
                                    Laravel: old('remember') ? 'checked' : '' mantiene stato in caso di errore
                                    HTML: name="remember" viene gestito dal sistema di autenticazione Laravel
                                --}}
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    <i class="bi bi-bookmark me-1"></i>
                                    Ricordami su questo dispositivo
                                </label>
                            </div>
                        </div>

                        {{-- 
                            PULSANTE SUBMIT LOGIN
                            Bootstrap: d-grid gap-2 per pulsante full-width
                            HTML: type="submit" per invio form
                        --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Accedi
                            </button>
                        </div>
                    </form>
                    
                </div>
                
                {{-- 
                    FOOTER CARD INFORMATIVO
                    HTML: Messaggio rassicurante sulla sicurezza
                --}}
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Accesso sicuro protetto da SSL
                    </small>
                </div>
            </div>
            
            {{-- === INFORMAZIONI LIVELLI DI ACCESSO === 
                HTML: Card informativa per spiegare i diversi livelli utente del sistema
                UX: Aiuta utenti a comprendere le proprie autorizzazioni
            --}}
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Livelli di Accesso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- 
                            LIVELLO 2 - TECNICI
                            Sistema di autorizzazione: Tecnici dei centri di assistenza
                            Possono visualizzare soluzioni ma non modificare prodotti
                        --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-info me-2">Livello 2</span>
                                <strong>Tecnici</strong>
                            </div>
                            <small class="text-muted">
                                Accesso a malfunzionamenti e soluzioni tecniche
                            </small>
                        </div>
                        
                        {{-- 
                            LIVELLO 3 - STAFF AZIENDALE
                            Sistema di autorizzazione: Staff tecnico dell'azienda
                            Possono gestire malfunzionamenti e creare soluzioni
                        --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-warning me-2">Livello 3</span>
                                <strong>Staff Aziendale</strong>
                            </div>
                            <small class="text-muted">
                                Gestione completa di malfunzionamenti e soluzioni
                            </small>
                        </div>
                        
                        {{-- 
                            LIVELLO 4 - AMMINISTRATORI
                            Sistema di autorizzazione: Amministratori sistema
                            Controllo completo su tutti gli aspetti dell'applicazione
                        --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2">Livello 4</span>
                                <strong>Amministratori</strong>
                            </div>
                            <small class="text-muted">
                                Controllo completo: utenti, prodotti, sistema
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === CREDENZIALI DI TEST === 
                Laravel: Sezione visibile solo in ambiente di sviluppo
                PHP: app()->environment('local') verifica se siamo in ambiente locale
                Sicurezza: Credenziali di test non appaiono in produzione
            --}}
            @if(app()->environment('local'))
                <div class="card card-custom mt-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-tools me-2"></i>
                            Credenziali di Test (Solo Sviluppo)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 text-sm">
                            {{-- 
                                PULSANTE CREDENZIALI ADMIN
                                HTML: data-* attributes per JavaScript auto-fill
                                JavaScript: Classe 'fill-credentials' per event binding
                                UX: Un click riempie automaticamente il form
                            --}}
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm w-100 fill-credentials" 
                                        data-username="adminadmin" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-gear me-1"></i>
                                    Admin: adminadmin / dNWRdNWR
                                </button>
                            </div>
                            
                            {{-- PULSANTE CREDENZIALI STAFF --}}
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-warning btn-sm w-100 fill-credentials" 
                                        data-username="staffstaff" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Staff: staffstaff / dNWRdNWR
                                </button>
                            </div>
                            
                            {{-- PULSANTE CREDENZIALI TECNICO --}}
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-outline-info btn-sm w-100 fill-credentials" 
                                        data-username="tecntecn" 
                                        data-password="dNWRdNWR">
                                    <i class="bi bi-person-wrench me-1"></i>
                                    Tecnico: tecntecn / dNWRdNWR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- === LINK PUBBLICI === 
                HTML: Navigazione verso sezioni pubbliche del sito
                UX: Permette accesso senza autenticazione a contenuti pubblici
            --}}
            <div class="text-center mt-4">
                <div class="row g-2">
                    <div class="col-6">
                        {{-- 
                            LINK HOME PUBBLICA
                            Laravel: route('home') genera URL per homepage
                        --}}
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-house me-1"></i>
                            Torna alla Home
                        </a>
                    </div>
                    <div class="col-6">
                        {{-- 
                            LINK CATALOGO PUBBLICO
                            Laravel: route('prodotti.pubblico.index') per visualizzazione prodotti senza login
                            Funzionalità Livello 1: Accesso pubblico al catalogo
                        --}}
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-box me-1"></i>
                            Catalogo Pubblico
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
{{-- FINE SEZIONE CONTENUTO --}}
@endsection

{{-- 
    SEZIONE SCRIPT JAVASCRIPT
    Blade PHP: @push('scripts') aggiunge JavaScript al layout
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA
    JavaScript: Namespace globale per condivisione dati tra PHP e JavaScript
    Pattern: Inizializza solo se non esiste già (evita sovrascritture)
*/
window.PageData = window.PageData || {};

/*
    SEZIONE DATI CONDIZIONALI PER COMPATIBILITÀ
    Blade PHP: Controlli @if(isset()) per passaggio sicuro dati
    NOTA: Questa sezione è standardizzata in tutte le viste per compatibilità,
    anche se la pagina login potrebbe non utilizzare tutti questi dati
*/

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
/*
    DATI PRODOTTO SINGOLO
    Laravel: Conversione sicura oggetto PHP -> JSON JavaScript
*/
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
/*
    COLLEZIONE PRODOTTI
    Laravel: Array/Collection -> JSON per uso JavaScript
*/
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
/*
    DATI MALFUNZIONAMENTO SINGOLO
    Laravel: Model Eloquent -> JSON Object
*/
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
/*
    COLLEZIONE MALFUNZIONAMENTI
    Laravel: Collection -> JSON Array
*/
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
/*
    DATI CENTRO ASSISTENZA SINGOLO
    Laravel: Model -> JSON per JavaScript
*/
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
/*
    COLLEZIONE CENTRI ASSISTENZA
    Laravel: Collection -> JSON Array
*/
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
/*
    CATEGORIE PRODOTTI
    Laravel: Array associativo -> JSON Object
*/
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
/*
    MEMBRI STAFF
    Laravel: Collection utenti staff -> JSON
*/
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
/*
    STATISTICHE SISTEMA
    Laravel: Array statistiche -> JSON per dashboard
*/
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
/*
    DATI UTENTE CORRENTE
    Laravel: User model -> JSON (se presente)
*/
window.PageData.user = @json($user);
@endif

/*
    PATTERN ESTENSIBILE
    JavaScript: Questa struttura permette aggiunta facile di nuovi dati
    senza modificare l'architettura JavaScript esistente
*/
</script>
@endpush

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade PHP: @push('styles') aggiunge CSS al layout
--}}
@push('styles')
<style>
/* === STILI PERSONALIZZATI LOGIN === 
   CSS: Definizioni stile specifiche per pagina di autenticazione
   Design system coerente con l'applicazione principale
*/

/*
    CARD PERSONALIZZATA CON BORDI ARROTONDATI
    CSS: Override stili Bootstrap per design moderno
*/
.card-custom {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

/*
    HEADER CARD CON GRADIENTE
    CSS: Background lineare per effetto visivo premium
    Da blu chiaro (#0d6efd) a blu scuro (#0056b3)
*/
.card-header {
    border-bottom: none;
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}

/*
    INPUT FORM OTTIMIZZATI
    CSS: Styling personalizzato per campi di input
    Transizioni smooth per feedback visivo
*/
.form-control-lg {
    border-radius: 10px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

/*
    STATO FOCUS INPUT
    CSS: Effetti visivi quando utente interagisce con input
    Transform per feedback tattile, box-shadow per evidenziare focus
*/
.form-control-lg:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    transform: translateY(-1px);
}

/*
    PULSANTI GRANDI CON EFFETTI
    CSS: Styling per pulsante login principale
*/
.btn-lg {
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

/*
    EFFETTO HOVER PULSANTI
    CSS: Feedback visivo su hover per tutti i pulsanti
    Transform e box-shadow per effetto "lift"
*/
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/*
    BADGE LIVELLI UTENTE
    CSS: Styling per badge informativi livelli di accesso
*/
.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
    border-radius: 8px;
}

/*
    PULSANTI CREDENZIALI TEST
    CSS: Effetti per pulsanti auto-fill credenziali
*/
.fill-credentials {
    transition: all 0.2s ease;
}

.fill-credentials:hover {
    transform: scale(1.02);
}

/*
    ANIMAZIONE SUCCESS STATE
    CSS: Classe applicata dinamicamente per feedback positivo
    Keyframe animation per "pulse" effect verde
*/
.border-success {
    border-color: #198754 !important;
    animation: pulse-success 1s ease-in-out;
}

/*
    KEYFRAMES PULSE EFFECT
    CSS: Animazione per feedback successo login
    Da box-shadow iniziale a espansione e fade out
*/
@keyframes pulse-success {
    0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
    100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
}

/*
    SPINNER PER STATI DI CARICAMENTO
    CSS: Dimensioni ottimizzate per loading states
*/
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/*
    TESTO SEMITRASPARENTE
    CSS: Utility per testo header card
*/
.text-white-50 {
    color: rgba(255, 255, 255, 0.75) !important;
}

/* === RESPONSIVE DESIGN === 
   CSS: Media queries per dispositivi mobili
*/
@media (max-width: 576px) {
    /*
        PADDING RIDOTTO SU MOBILE
        CSS: Ottimizzazione spazio su schermi piccoli
    */
    .card-body {
        padding: 2rem 1.5rem;
    }
    
    .col-md-6 {
        margin-bottom: 2rem;
    }
    
    /*
        PULSANTI PICCOLI PIÙ COMPATTI
        CSS: Riduzione dimensioni per mobile
    */
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
}

/* === DARK MODE SUPPORT === 
   CSS: Supporto automatico per tema scuro del sistema
*/
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #2d3748;
        color: #fff;
    }
    
    .card-footer {
        background-color: #1a202c !important;
    }
}

/* === ACCESSIBILITY === 
   CSS: Supporto accessibilità per navigazione da tastiera
   Outline per focus visibility migliorata
*/
.form-control:focus,
.btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* === ANIMATION ON LOAD === 
   CSS: Animazione entrata per migliorare perceived performance
*/
.card-custom {
    animation: slideIn 0.5s ease-out;
}

/*
    KEYFRAMES SLIDE IN EFFECT
    CSS: Animazione smooth dall'alto per caricamento pagina
*/
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush