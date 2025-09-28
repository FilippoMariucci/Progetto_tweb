{{-- 
    Vista per la pagina Contatti dell'applicazione
    LINGUAGGIO: Blade Template (Laravel) - pagina pubblica per contatti e richieste
    SCOPO: Interfaccia per contattare l'azienda con form e informazioni di contatto
    ACCESSO: Pagina pubblica, accessibile a tutti gli utenti senza autenticazione
    PERCORSO: resources/views/contatti.blade.php
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- Titolo statico della pagina per SEO e tab browser --}}
@section('title', 'Contatti')

{{-- Inizio sezione contenuto principale --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">
    
    {{-- 
        SEZIONE HEADER PRINCIPALE
        Presentazione della pagina con titolo e descrizione
    --}}
    <div class="row mb-4">
        <div class="col-12">
            {{-- 
                Titolo principale con classe h2
                Bootstrap: h2 per dimensioni ottimali, non h1 per gerarchia SEO
            --}}
            <h1 class="h2 mb-3">
                <i class="bi bi-telephone text-primary me-2"></i>
                Contatti
            </h1>
            {{-- Testo introduttivo con classe lead per evidenza --}}
            <p class="lead text-muted">
                Mettiti in contatto con noi per assistenza tecnica, informazioni sui prodotti o supporto generale
            </p>
        </div>
    </div>

    {{-- 
        SEZIONE INFORMAZIONI DI CONTATTO DINAMICHE
        Mostrata solo se il controller passa array $contatti popolato
        Laravel: isset() e count() per controllo esistenza e contenuto
    --}}
    @if(isset($contatti) && count($contatti) > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="bi bi-info-circle text-info me-2"></i>
                    Informazioni di Contatto
                </h3>
                {{-- Grid responsive per card contatti --}}
                <div class="row g-4">
                    {{-- 
                        Iterazione sui contatti forniti dal controller
                        Foreach Blade per ogni elemento dell'array $contatti
                    --}}
                    @foreach($contatti as $contatto)
                        {{-- Bootstrap: col-md-6 col-lg-4 per layout responsive (3 colonne desktop) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-custom h-100">
                                <div class="card-body">
                                    {{-- Header card con icona dinamica e informazioni --}}
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="me-3">
                                            {{-- 
                                                Switch Blade per icone dinamiche basate su tipo contatto
                                                PHP: Array associativo $contatto['tipo'] per determinare icona
                                            --}}
                                            @switch($contatto['tipo'])
                                                @case('sede_principale')
                                                    <i class="bi bi-building text-primary fs-2"></i>
                                                    @break
                                                @case('assistenza_tecnica')
                                                    <i class="bi bi-tools text-success fs-2"></i>
                                                    @break
                                                @case('commerciale')
                                                    <i class="bi bi-briefcase text-warning fs-2"></i>
                                                    @break
                                                @case('emergenze')
                                                    <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-telephone text-secondary fs-2"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1">
                                            {{-- Nome contatto (sempre presente) --}}
                                            <h5 class="card-title mb-2">{{ $contatto['nome'] }}</h5>
                                            {{-- Descrizione opzionale --}}
                                            @if(isset($contatto['descrizione']))
                                                <p class="text-muted mb-3">{{ $contatto['descrizione'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- 
                                        Dettagli di contatto con layout strutturato
                                        Ogni informazione è opzionale e mostrata solo se presente
                                    --}}
                                    <div class="contact-details">
                                        {{-- Telefono con link tel: per dispositivi mobili --}}
                                        @if(isset($contatto['telefono']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                {{-- 
                                                    Link tel: per chiamate dirette su mobile
                                                    HTML: tel: protocol per integrazione nativi dispositivi
                                                --}}
                                                <a href="tel:{{ $contatto['telefono'] }}" class="text-decoration-none">
                                                    {{ $contatto['telefono'] }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        {{-- Email con link mailto: --}}
                                        @if(isset($contatto['email']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                {{-- 
                                                    Link mailto: per aprire client email
                                                    HTML: mailto: protocol per composizione email automatica
                                                --}}
                                                <a href="mailto:{{ $contatto['email'] }}" class="text-decoration-none">
                                                    {{ $contatto['email'] }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        {{-- Indirizzo fisico --}}
                                        @if(isset($contatto['indirizzo']))
                                            <div class="d-flex align-items-start mb-2">
                                                {{-- mt-1 per allineamento icona con testo multi-riga --}}
                                                <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                                <div>{{ $contatto['indirizzo'] }}</div>
                                            </div>
                                        @endif
                                        
                                        {{-- Orari di apertura/disponibilità --}}
                                        @if(isset($contatto['orari']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-clock text-muted me-2"></i>
                                                <div>{{ $contatto['orari'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- 
                                    Footer card con pulsanti di azione
                                    Mostrato solo se ci sono telefono o email disponibili
                                --}}
                                @if(isset($contatto['telefono']) || isset($contatto['email']))
                                    <div class="card-footer bg-light">
                                        {{-- Layout flex per pulsanti responsive --}}
                                        <div class="d-flex gap-2">
                                            {{-- Pulsante chiamata (se presente telefono) --}}
                                            @if(isset($contatto['telefono']))
                                                <a href="tel:{{ $contatto['telefono'] }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-telephone me-1"></i>Chiama
                                                </a>
                                            @endif
                                            {{-- Pulsante email (se presente email) --}}
                                            @if(isset($contatto['email']))
                                                <a href="mailto:{{ $contatto['email'] }}" class="btn btn-outline-info btn-sm flex-fill">
                                                    <i class="bi bi-envelope me-1"></i>Email
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- 
        SEZIONE MODULO DI CONTATTO PRINCIPALE
        Form per inviare messaggi all'azienda
    --}}
    <div class="row mb-5">
        {{-- Bootstrap: col-lg-8 mx-auto per centrare e limitare larghezza su desktop --}}
        <div class="col-lg-8 mx-auto">
            <div class="card card-custom">
                {{-- Header colorato per evidenziare sezione importante --}}
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>
                        Invia un Messaggio
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Compila il modulo sottostante per inviarci un messaggio. Ti risponderemo il prima possibile.
                    </p>
                    
                    {{-- 
                        Form HTML per invio messaggi
                        Laravel: route() per URL, @csrf per sicurezza
                        HTML: id per targeting JavaScript
                    --}}
                    <form action="{{ route('contatti.invia') }}" method="POST" id="contact-form">
                        {{-- Token CSRF Laravel per sicurezza --}}
                        @csrf
                        
                        {{-- Grid responsive per campi form --}}
                        <div class="row g-3">
                            {{-- CAMPO: Nome (obbligatorio) --}}
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">
                                    Nome <span class="text-danger">*</span>
                                </label>
                                {{-- 
                                    Input con validazione Laravel
                                    Blade: @error() per controllo errori specifici campo
                                    Laravel: old() per recuperare valore dopo validazione fallita
                                --}}
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
                                       required>
                                {{-- Messaggio errore Laravel --}}
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Cognome (obbligatorio) --}}
                            <div class="col-md-6">
                                <label for="cognome" class="form-label fw-semibold">
                                    Cognome <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('cognome') is-invalid @enderror" 
                                       id="cognome" 
                                       name="cognome" 
                                       value="{{ old('cognome') }}" 
                                       required>
                                @error('cognome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Email (obbligatorio) --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                {{-- 
                                    Input email con validazione HTML5 automatica
                                    HTML: type="email" per controlli browser nativi
                                --}}
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Telefono (opzionale) --}}
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold">
                                    Telefono
                                </label>
                                {{-- 
                                    Input tel per ottimizzazione mobile
                                    HTML: type="tel" attiva tastiera numerica su mobile
                                --}}
                                <input type="tel" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Tipo di richiesta (obbligatorio) --}}
                            <div class="col-md-6">
                                <label for="tipo_richiesta" class="form-label fw-semibold">
                                    Tipo di Richiesta <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_richiesta') is-invalid @enderror" 
                                        id="tipo_richiesta" 
                                        name="tipo_richiesta" 
                                        required>
                                    <option value="">Seleziona...</option>
                                    {{-- 
                                        Opzioni con controllo selected dinamico
                                        Laravel: old() con confronto per mantenere selezione
                                    --}}
                                    <option value="assistenza" {{ old('tipo_richiesta') == 'assistenza' ? 'selected' : '' }}>
                                        Assistenza Tecnica
                                    </option>
                                    <option value="informazioni" {{ old('tipo_richiesta') == 'informazioni' ? 'selected' : '' }}>
                                        Informazioni Prodotti
                                    </option>
                                    <option value="vendite" {{ old('tipo_richiesta') == 'vendite' ? 'selected' : '' }}>
                                        Vendite e Commerciale
                                    </option>
                                    <option value="reclamo" {{ old('tipo_richiesta') == 'reclamo' ? 'selected' : '' }}>
                                        Reclamo o Segnalazione
                                    </option>
                                </select>
                                @error('tipo_richiesta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Oggetto (obbligatorio) --}}
                            <div class="col-md-6">
                                <label for="oggetto" class="form-label fw-semibold">
                                    Oggetto <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('oggetto') is-invalid @enderror" 
                                       id="oggetto" 
                                       name="oggetto" 
                                       value="{{ old('oggetto') }}" 
                                       required>
                                @error('oggetto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Messaggio (obbligatorio) --}}
                            <div class="col-12">
                                <label for="messaggio" class="form-label fw-semibold">
                                    Messaggio <span class="text-danger">*</span>
                                </label>
                                {{-- 
                                    Textarea per testo lungo
                                    HTML: rows="6" per altezza iniziale, placeholder per guida utente
                                --}}
                                <textarea class="form-control @error('messaggio') is-invalid @enderror" 
                                          id="messaggio" 
                                          name="messaggio" 
                                          rows="6" 
                                          required 
                                          placeholder="Descrivi dettagliatamente la tua richiesta...">{{ old('messaggio') }}</textarea>
                                @error('messaggio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CAMPO: Consenso Privacy (obbligatorio) --}}
                            <div class="col-12">
                                <div class="form-check">
                                    {{-- 
                                        Checkbox per consenso privacy GDPR
                                        HTML: required per validazione obbligatoria
                                        Laravel: old() per mantenere stato dopo errori
                                    --}}
                                    <input class="form-check-input @error('privacy') is-invalid @enderror" 
                                           type="checkbox" 
                                           id="privacy" 
                                           name="privacy" 
                                           value="1" 
                                           {{ old('privacy') ? 'checked' : '' }} 
                                           required>
                                    <label class="form-check-label" for="privacy">
                                        Accetto il trattamento dei dati personali secondo la <a href="#" class="text-decoration-none">Privacy Policy</a> <span class="text-danger">*</span>
                                    </label>
                                    {{-- 
                                        Error feedback per checkbox
                                        CSS: d-block per mostrare sotto checkbox (non inline)
                                    --}}
                                    @error('privacy')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- SEZIONE: Pulsanti azione form --}}
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    {{-- Pulsante reset per pulire form --}}
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                    </button>
                                    {{-- Pulsante submit principale --}}
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>Invia Messaggio
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        SEZIONE INFORMAZIONI AGGIUNTIVE
        Informazioni utili per gli utenti prima del contatto
    --}}
    <div class="row">
        <div class="col-12">
            {{-- Card con sfondo grigio per distinguere dalle altre sezioni --}}
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni Utili
                    </h5>
                    <div class="row">
                        {{-- COLONNA 1: Tempi di risposta --}}
                        <div class="col-md-6">
                            <h6>Tempi di Risposta</h6>
                            {{-- Lista non ordinata con icone per tipologia --}}
                            <ul class="list-unstyled">
                                <li><i class="bi bi-clock text-success me-2"></i>Email: entro 24 ore lavorative</li>
                                <li><i class="bi bi-telephone text-success me-2"></i>Telefono: risposta immediata negli orari di ufficio</li>
                                <li><i class="bi bi-exclamation-triangle text-warning me-2"></i>Emergenze: disponibilità 24/7</li>
                            </ul>
                        </div>
                        {{-- COLONNA 2: Risorse di self-service --}}
                        <div class="col-md-6">
                            <h6>Prima di Contattarci</h6>
                            {{-- 
                                Lista con link a risorse utili dell'applicazione
                                Laravel: route() per generare URL corretti
                            --}}
                            <ul class="list-unstyled">
                                <li><i class="bi bi-search text-info me-2"></i>Consulta la <a href="{{ route('prodotti.pubblico.index') }}" class="text-decoration-none">base di conoscenza prodotti</a></li>
                                <li><i class="bi bi-geo-alt text-info me-2"></i>Trova il <a href="{{ route('centri.index') }}" class="text-decoration-none">centro assistenza</a> più vicino</li>
                                {{-- 
                                    Link documentazione con target="_blank"
                                    HTML: target="_blank" per aprire in nuova tab
                                --}}
                                <li><i class="bi bi-file-text text-info me-2"></i>Leggi la <a href="{{ route('documentazione') }}" class="text-decoration-none" target="_blank">documentazione</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE JAVASCRIPT
    Blade: @push('scripts') per JavaScript specifico della pagina
    Pattern standard per trasferimento dati PHP→JS
--}}
@push('scripts')
<script>
/*
    JavaScript: Inizializzazione dati globali della pagina
    Pattern singleton per gestione stato applicazione
    Alcuni di questi dati potrebbero non essere rilevanti per la pagina contatti,
    ma il pattern è mantenuto per coerenza con altre viste
*/

// Inizializza oggetto globale se non esiste (pattern safe)
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    @json() garantisce encoding sicuro con escape automatico
    isset() previene errori per variabili non definite dal controller
*/

// Dati prodotto (per eventuale pre-compilazione form se si arriva da prodotto specifico)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Array prodotti (per suggerimenti o auto-completamento)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento (per pre-compilazione se si arriva da problema specifico)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti (per statistiche o suggerimenti)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro di assistenza (per pre-compilazione dati geografici)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Lista centri (per suggerimenti centro più vicino)
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti (per classificazione automatica richieste)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members (per routing automatico richieste)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche (per analisi comportamento utenti)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente (per pre-compilazione se autenticato)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    FUNZIONALITÀ JAVASCRIPT POTENZIALI PER QUESTA VISTA:
    
    1. Validazione form lato client
       - Controlli real-time su email, telefono
       - Conteggio caratteri per messaggio
       - Validazione checkbox privacy obbligatorio
    
    2. Pre-compilazione intelligente
       - Auto-fill da user data se autenticato
       - Selezione automatica tipo richiesta da contesto
       - Suggerimento oggetto basato su pagina provenienza
    
    3. Invio AJAX del form
       - Submit asincrono senza refresh pagina
       - Loading states e feedback progressivo
       - Gestione errori con highlight campi
    
    4. Geolocalizzazione per centro vicino
       - Rilevamento posizione utente
       - Suggerimento centro assistenza più vicino
       - Integrazione con mappa interattiva
    
    5. Chat widget integrato
       - Live chat per supporto immediato
       - Integrazione con sistema ticketing
       - Trasferimento contesto form a chat
*/

</script>
@endpush

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI
    Blade: @push('styles') per CSS specifico di questa pagina
--}}
@push('styles')
<style>
/* 
    CSS: STILI BASE PER CARD PERSONALIZZATE
    Design system coerente per elementi card
*/
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);  /* Ombra sottile */
    border: 1px solid rgba(0, 0, 0, 0.125);              /* Bordo leggero */
    transition: box-shadow 0.2s ease-in-out;              /* Transizione smooth */
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);       /* Ombra più pronunciata */
}

/* 
    CSS: STILI PER DETTAGLI CONTATTO
    Ottimizzazione leggibilità informazioni contatto
*/
.contact-details {
    font-size: 0.95rem;        /* Font leggermente ridotto per compattezza */
}

/* Stili label form con peso font personalizzato */
.form-label.fw-semibold {
    font-weight: 600;          /* Semi-bold per evidenza senza essere eccessivo */
}

/* Colore rosso personalizzato per campi obbligatori */
.text-danger {
    color: #dc3545 !important; /* Rosso Bootstrap con important per override */
}

/* 
    CSS: PERSONALIZZAZIONE TEXTAREA
    Controlli dimensioni e comportamento textarea messaggio
*/
#messaggio {
    resize: vertical;          /* Consente ridimensionamento solo verticale */
    min-height: 120px;         /* Altezza minima per usabilità */
}

/* 
    CSS RESPONSIVE: ADATTAMENTI MOBILE
    Ottimizzazioni layout per dispositivi piccoli
*/
@media (max-width: 768px) {
    /* Footer card responsive: pulsanti in colonna su mobile */
    .card-footer .d-flex {
        flex-direction: column;         /* Stack verticale */
        gap: 0.5rem !important;        /* Gap ridotto */
    }
    
    /* Pulsanti full-width su mobile */
    .card-footer .btn {
        flex: 1 !important;            /* Occupa tutto lo spazio disponibile */
    }
}

/* 
    CSS: STILI VALIDAZIONE FORM
    Feedback visivo per errori di validazione Laravel
*/
.is-invalid {
    border-color: #dc3545;            /* Bordo rosso per campi invalidi */
}

.invalid-feedback {
    color: #dc3545;                   /* Testo rosso per messaggi errore */
    font-size: 0.875rem;              /* Font leggermente ridotto */
    margin-top: 0.25rem;              /* Margine top per spaziatura */
}

/* 
    CSS: STILI LINK NEI CONTATTI
    Ottimizzazione link telefono e email per UX
*/
a[href^="tel:"], a[href^="mailto:"] {
    color: inherit;                   /* Eredita colore dal parent */
    text-decoration: none;            /* Rimuove sottolineatura default */
}

a[href^="tel:"]:hover, a[href^="mailto:"]:hover {
    color: var(--bs-primary);         /* Colore primario Bootstrap al hover */
    text-decoration: underline;       /* Sottolineatura al hover per feedback */
}
</style>
@endpush