```blade
{{-- 
    === VISTA DASHBOARD AMMINISTRATORE ===
    Linguaggio: Blade Template Engine (HTML + PHP)
    File: resources/views/admin/dashboard.blade.php
    Accesso: Solo livello 4 (Amministratori)
    Funzione: Pannello di controllo completo per amministratori del sistema
    
    Questa vista fornisce:
    - Panoramica completa delle statistiche del sistema
    - Accesso rapido a tutte le funzionalità amministrative
    - Gestione utenti, prodotti, centri assistenza
    - Monitoraggio stato sistema e funzionalità avanzate
    - Design responsive e moderno per ottima UX
--}}

{{-- 
    Estende il layout principale dell'applicazione
    @extends: Direttiva Blade per ereditarietà del template
    layouts.app: Template base che contiene struttura HTML, navigation, footer
--}}
@extends('layouts.app')

{{-- 
    Definisce il titolo che apparirà nel tag <title> del documento
    @section: Direttiva Blade per definire contenuto di sezioni specifiche
--}}
@section('title', 'Dashboard Amministratore')

{{-- 
    Inizia la sezione del contenuto principale
    Tutto il contenuto qui viene inserito nella sezione 'content' del layout
--}}
@section('content')
<div class="container mt-4">
    {{-- 
        === HEADER COMPATTO PRINCIPALE ===
        Layout flexbox per posizionamento titolo e informazioni utente
        Bootstrap classes: d-flex, justify-content-between, align-items-center
    --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            {{-- 
                Titolo principale con icona Bootstrap Icons
                h2: Heading di secondo livello per gerarchia semantica
                bi-shield-check: Icona che rappresenta sicurezza/amministrazione
                text-danger: Colore rosso Bootstrap per livello admin
            --}}
            <h2 class="mb-1">
                <i class="bi bi-shield-check text-danger me-2"></i>
                Pannello Amministratore
            </h2>
            {{-- 
                Sottotitolo con informazioni utente corrente
                auth()->user(): Helper Laravel per utente autenticato
                nome_completo: Accessor del Model User che concatena nome e cognome
                ?? operator: Null coalescing per fallback se nome_completo non esiste
            --}}
            <p class="text-muted small mb-0">{{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Admin' }} - Controllo completo sistema</p>
        </div>
        <div class="text-end">
            {{-- 
                Badge di identificazione livello utente
                fs-6: Font size Bootstrap, px-3 py-2: padding personalizzato
                rounded-pill: Bordi completamente arrotondati
            --}}
            <div class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                <i class="bi bi-person-fill-gear me-1"></i>
                Amministratore
            </div>
            <div class="small text-muted mt-1">
                {{-- 
                    Timestamp ultimo accesso con helper Laravel
                    now(): Helper per data/ora corrente
                    format(): Metodo Carbon per formattazione data
                --}}
                Ultimo accesso: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- 
        === ALERT DI BENVENUTO ===
        Alert Bootstrap personalizzato con gradient CSS
        border-0: Rimuove bordo, shadow-sm: Ombra leggera
        style inline: Gradient CSS per sfondo dinamico
    --}}
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="row align-items-center">
            <div class="col-auto">
                {{-- 
                    Avatar circolare per icona utente
                    CSS personalizzato per dimensioni fisse e centratura contenuto
                --}}
                <div class="avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-fill-gear fs-4"></i>
                </div>
            </div>
            <div class="col">
                {{-- 
                    Messaggio di benvenuto personalizzato
                    alert-heading: Classe Bootstrap per titolo alert
                    Utilizza stesso sistema di fallback per nome utente
                --}}
                <h5 class="alert-heading mb-1">Benvenuto, {{ auth()->user()->nome_completo ?? auth()->user()->name ?? 'Admin' }}!</h5>
                <p class="mb-0">
                    <strong>Amministratore Sistema</strong> - Accesso completo a gestione utenti, prodotti e configurazioni
                </p>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONE STATISTICHE COMPATTE ===
        Grid responsive Bootstrap con gap ridotto per layout compatto
        g-2: Gap ridotto tra colonne per design più denso
        mb-3: Margin bottom ridotto per compattezza
    --}}
    <div class="row mb-3 g-2">
        {{-- 
            === CARD STATISTICA UTENTI TOTALI ===
            Layout responsive: xl-3 (1/4 su extra large), lg-6 (1/2 su large)
            Design uniforme per tutte le card statistiche
        --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    {{-- 
                        Icona grande per impatto visivo
                        fs-3: Font size grande Bootstrap
                        text-danger: Colore tema amministratore
                    --}}
                    <i class="bi bi-people text-danger fs-3 mb-1"></i>
                    {{-- 
                        Numero principale dalla statistica
                        $stats: Array di statistiche passato dal Controller
                        ?? 0: Fallback se statistica non disponibile
                    --}}
                    <h5 class="fw-bold mb-0 text-danger">{{ $stats['total_utenti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Utenti Totali</small>
                    {{-- 
                        Badge descrittivo con background opaco
                        bg-opacity-10: Utilità Bootstrap per trasparenza
                    --}}
                    <small class="badge bg-danger bg-opacity-10 text-danger mt-1">
                        Registrati
                    </small>
                </div>
            </div>
        </div>

        {{-- === CARD STATISTICA PRODOTTI === --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-box-seam text-primary fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-primary">{{ $stats['total_prodotti'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Prodotti</small>
                    <small class="badge bg-primary bg-opacity-10 text-primary mt-1">
                        Nel Catalogo
                    </small>
                </div>
            </div>
        </div>

        {{-- === CARD STATISTICA CENTRI ASSISTENZA === --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-geo-alt text-info fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-info">{{ $stats['total_centri'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Centri Assistenza</small>
                    <small class="badge bg-info bg-opacity-10 text-info mt-1">
                        Attivi
                    </small>
                </div>
            </div>
        </div>

        {{-- === CARD STATISTICA SOLUZIONI === --}}
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-2 px-3">
                    <i class="bi bi-tools text-success fs-3 mb-1"></i>
                    <h5 class="fw-bold mb-0 text-success">{{ $stats['total_soluzioni'] ?? 0 }}</h5>
                    <small class="text-muted d-block">Soluzioni</small>
                    <small class="badge bg-success bg-opacity-10 text-success mt-1">
                        Disponibili
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONE GESTIONE PRINCIPALE ===
        Card principale che contiene tutti i pulsanti di gestione amministrativa
        Organizzata in grid responsive per facile accesso alle funzionalità
    --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                {{-- 
                    Header con gradiente CSS personalizzato
                    bg-gradient: Classe personalizzata per animazione gradiente
                    style inline: Gradiente CSS da rosso a rosso scuro
                --}}
                <div class="card-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <h4 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Gestione Sistema
                    </h4>
                    <small class="opacity-75">Funzionalità amministrative principali</small>
                </div>
                <div class="card-body p-4">
                    {{-- 
                        Grid per pulsanti di gestione
                        g-3: Gap medio tra elementi
                        Layout responsive che si adatta a diverse dimensioni schermo
                    --}}
                    <div class="row g-3">
                        
                        {{-- 
                            === PULSANTE GESTIONE UTENTI ===
                            Link stilizzato come pulsante grande
                            route(): Helper Laravel per generare URL da nome route
                            Flexbox per layout centrato verticale e orizzontale
                        --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-people display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Utenti</span>
                            </a>
                        </div>
                        
                        {{-- === PULSANTE GESTIONE PRODOTTI === --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-box-seam display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- === PULSANTE CENTRI ASSISTENZA === --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.centri.index') }}" class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- 
                            === PULSANTE ASSEGNAZIONE PRODOTTI ===
                            Funzionalità opzionale per assegnare prodotti a staff
                            text-dark: Testo scuro per contrasto su background warning (giallo)
                        --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-warning btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none text-dark shadow-sm">
                                <i class="bi bi-person-gear display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Assegna Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- === PULSANTE STATISTICHE AVANZATE === --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.statistiche.index') }}" class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Statistiche</span>
                            </a>
                        </div>
                        
                        {{-- === PULSANTE MANUTENZIONE SISTEMA === --}}
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.manutenzione.index') }}" class="btn btn-secondary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center text-decoration-none shadow-sm">
                                <i class="bi bi-tools display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Manutenzione</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONE UTENTI E PRODOTTI ===
        Row con due colonne per informazioni dettagliate
        g-3: Gap standard tra colonne
    --}}
    <div class="row mb-4 g-3">
        {{-- 
            === CARD UTENTI RECENTI ===
            Mostra gli ultimi utenti registrati nel sistema
            col-lg-6: 50% larghezza su desktop
        --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-plus me-1"></i>
                        Utenti Recenti
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        === CONTROLLO PRESENZA UTENTI RECENTI ===
                        isset(): Verifica se variabile esiste
                        count(): Conta elementi nella collezione
                        Condizione doppia per sicurezza
                    --}}
                    @if(isset($stats['utenti_recenti']) && $stats['utenti_recenti']->count() > 0)
                        <div class="list-group list-group-flush">
                            {{-- 
                                === LOOP UTENTI RECENTI ===
                                take(4): Metodo Collection per limitare a 4 elementi
                                Mostra solo i primi 4 utenti per non appesantire UI
                            --}}
                            @foreach($stats['utenti_recenti']->take(4) as $utente)
                                <div class="list-group-item px-0 border-0 border-bottom py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{-- 
                                                Nome utente con fallback
                                                Priorità: nome_completo > username per migliore UX
                                            --}}
                                            <h6 class="mb-1 fw-semibold small">{{ $utente->nome_completo ?? $utente->username }}</h6>
                                            <small class="text-muted">{{ $utente->username }}</small>
                                        </div>
                                        <div class="text-end">
                                            {{-- 
                                                Badge livello utente con classe CSS personalizzata
                                                badge-livello-: Classe dinamica basata su livello
                                                CSS personalizzato definito negli stili
                                            --}}
                                            <span class="badge badge-livello badge-livello-{{ $utente->livello_accesso }}">
                                                Livello {{ $utente->livello_accesso }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            {{-- Link per visualizzare tutti gli utenti --}}
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-list me-1"></i>Tutti gli Utenti
                            </a>
                        </div>
                    @else
                        {{-- 
                            === STATO VUOTO ===
                            Messaggio quando non ci sono utenti recenti
                            UX friendly con icona e messaggio esplicativo
                        --}}
                        <div class="text-center py-3">
                            <i class="bi bi-person display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0 small">Nessun utente recente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
            === CARD PRODOTTI NON ASSEGNATI ===
            Evidenzia prodotti che necessitano assegnazione a staff
            Importante per workflow amministrativo
        --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Prodotti Non Assegnati
                        {{-- 
                            Badge con conteggio se ci sono prodotti non assegnati
                            Condizione per mostrare solo se necessario
                        --}}
                        @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                            <span class="badge bg-danger ms-2">{{ $stats['prodotti_non_assegnati_count'] }}</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body p-3" id="prodotti-non-assegnati-container">
                    {{-- 
                        === CONTROLLO PRODOTTI NON ASSEGNATI ===
                        Logica condizionale per gestire presenza/assenza prodotti
                    --}}
                    @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                        {{-- Alert di warning con conteggio --}}
                        <div class="alert alert-warning py-2 mb-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>{{ $stats['prodotti_non_assegnati_count'] }} prodotti</strong> senza staff
                        </div>

                        {{-- 
                            === LISTA PRODOTTI NON ASSEGNATI ===
                            Mostra esempi di prodotti che necessitano assegnazione
                        --}}
                        @if(isset($stats['prodotti_non_assegnati']) && $stats['prodotti_non_assegnati']->count() > 0)
                            <div class="list-group list-group-flush">
                                {{-- 
                                    Loop limitato a 3 prodotti per non appesantire UI
                                    take(3): Limita visualizzazione primi 3 elementi
                                --}}
                                @foreach($stats['prodotti_non_assegnati']->take(3) as $prodotto)
                                    <div class="list-group-item px-0 border-0 border-bottom py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold small">{{ $prodotto->nome }}</h6>
                                                <small class="text-muted">{{ $prodotto->categoria ?? 'N/A' }}</small>
                                            </div>
                                            <div class="text-end">
                                                {{-- 
                                                    Link diretto per assegnazione prodotto specifico
                                                    Query parameter per pre-filtrare nella vista assegnazioni
                                                --}}
                                                <a href="{{ route('admin.assegnazioni.index') }}?prodotto={{ $prodotto->id }}" 
                                                   class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-person-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="text-center mt-2">
                            {{-- Link generale per gestione assegnazioni --}}
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-gear me-1"></i>Gestisci Assegnazioni
                            </a>
                        </div>
                    @else
                        {{-- 
                            === STATO POSITIVO ===
                            Messaggio quando tutti i prodotti sono assegnati
                            Verde per indicare stato ottimale
                        --}}
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle display-4 text-success opacity-75"></i>
                            <p class="text-success mt-2 mb-0 small">Tutti i prodotti assegnati</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONI INFORMATIVE LATERALI ===
        Tre colonne con informazioni di sistema e azioni rapide
        Layout responsive per adattamento mobile
    --}}
    <div class="row mb-4 g-3">
        {{-- 
            === CARD DISTRIBUZIONE UTENTI ===
            Mostra breakdown utenti per livello di accesso
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-1"></i>
                        Distribuzione Utenti
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        === CONTROLLO DATI DISTRIBUZIONE ===
                        Verifica presenza e validità dati statistici
                    --}}
                    @if(isset($stats['distribuzione_utenti']) && count($stats['distribuzione_utenti']) > 0)
                        <div class="row g-2 text-center">
                            {{-- 
                                === LOOP DISTRIBUZIONE PER LIVELLO ===
                                $livello: Chiave numerica (2,3,4)
                                $count: Numero utenti per quel livello
                            --}}
                            @foreach($stats['distribuzione_utenti'] as $livello => $count)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light small">
                                        <span class="fw-semibold">
                                            {{-- 
                                                === SWITCH PER ETICHETTE LIVELLI ===
                                                @switch: Direttiva Blade per controllo condizionale multiplo
                                                Converte numeri livello in etichette user-friendly
                                            --}}
                                            @switch($livello)
                                                @case(2) Tecnici @break
                                                @case(3) Staff @break
                                                @case(4) Admin @break
                                                @default Utenti @break
                                            @endswitch
                                        </span>
                                        {{-- Badge con classe CSS dinamica per colori --}}
                                        <span class="badge badge-livello badge-livello-{{ $livello }}">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Stato vuoto per distribuzione utenti --}}
                        <div class="text-center py-3">
                            <i class="bi bi-pie-chart display-4 text-muted opacity-50"></i>
                            <p class="text-muted mt-2 mb-0 small">Dati non disponibili</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 
            === CARD STATO SISTEMA ===
            Informazioni tecniche su stato applicazione e infrastruttura
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-cpu me-1"></i>
                        Stato Sistema
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        {{-- Stato database (hardcoded come "Online" - in produzione potrebbe essere dinamico) --}}
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>Database</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        {{-- 
                            Versione Laravel
                            app()->version(): Helper Laravel per versione framework
                        --}}
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>Laravel</span>
                            <span class="badge bg-info">v{{ app()->version() }}</span>
                        </div>
                        {{-- 
                            Versione PHP
                            PHP_VERSION: Costante PHP per versione runtime
                        --}}
                        <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                            <span>PHP</span>
                            <span class="badge bg-info">v{{ PHP_VERSION }}</span>
                        </div>
                        {{-- 
                            Informazione backup opzionale
                            Mostrata solo se disponibile nelle statistiche
                        --}}
                        @if(isset($stats['ultimo_backup']))
                            <div class="list-group-item px-0 d-flex justify-content-between py-2 border-0 small">
                                <span>Ultimo Backup</span>
                                <span class="badge bg-warning text-dark">{{ $stats['ultimo_backup'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 
            === CARD AZIONI RAPIDE ===
            Shortcuts per operazioni amministrative comuni
        --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-lightning me-1"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body p-3">
                    {{-- 
                        d-grid gap-2: Layout Bootstrap per pulsanti impilati
                        Crea colonna verticale con spaziatura uniforme
                    --}}
                    <div class="d-grid gap-2">
                        {{-- Link rapido per creazione nuovo utente --}}
                        <a href="{{ route('admin.users.create') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                        {{-- Link rapido per creazione nuovo prodotto --}}
                        <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Nuovo Prodotto
                        </a>
                        {{-- Link rapido per creazione nuovo centro --}}
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-geo-alt-fill me-1"></i>Nuovo Centro
                        </a>
                        {{-- 
                            Separatore visivo con border-top
                            pt-2 mt-2: Padding e margin top per spaziatura
                        --}}
                        <div class="border-top pt-2 mt-2">
                            {{-- Link per funzionalità esportazione dati --}}
                            <a href="{{ route('admin.export.index') }}" class="btn btn-success btn-sm w-100 mb-1">
                                <i class="bi bi-download me-1"></i>Esporta Dati
                            </a>
                            {{-- Link per manutenzione sistema --}}
                            <a href="{{ route('admin.manutenzione.index') }}" class="btn btn-warning btn-sm w-100">
                                <i class="bi bi-gear me-1"></i>Manutenzione
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONE LINK DASHBOARD ALTERNATIVE ===
        Footer della dashboard con link di navigazione verso altre viste
        Utile per admin che necessitano di accedere rapidamente ad altre interfacce
    --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">
                        <i class="bi bi-grid text-secondary me-2"></i>
                        Visualizzazioni Alternative
                    </h6>
                    {{-- 
                        === FLEX CONTAINER PER LINK ===
                        d-flex flex-wrap: Layout flessibile che si adatta a contenuto
                        justify-content-center: Centratura orizzontale
                        gap-2: Spaziatura uniforme tra elementi
                    --}}
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        {{-- Link alla dashboard generale (per tutti i livelli utente) --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-house me-1"></i>Dashboard Generale
                        </a>
                        {{-- Link alla vista pubblica (livello 1 - senza autenticazione) --}}
                        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box me-1"></i>Vista Pubblica
                        </a>
                        {{-- 
                            Link alla vista tecnico con parametro GET
                            Query parameter ?view=tech per personalizzare interfaccia
                        --}}
                        <a href="{{ route('prodotti.completo.index') }}?view=tech" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-tools me-1"></i>Vista Tecnico
                        </a>
                        {{-- 
                            Link documentazione con target blank
                            target="_blank": Apre in nuova finestra per non perdere contesto dashboard
                        --}}
                        <a href="{{ route('documentazione') }}" class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Documentazione
                        </a>
                        {{-- 
                            Pulsante per refresh manuale della pagina
                            id specifico per gestione JavaScript
                        --}}
                        <button id="manual-refresh-btn" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
                        </button>
                    </div>
                    <div class="mt-2">
                        {{-- 
                            Timestamp ultimo aggiornamento
                            span con id per aggiornamento dinamico via JavaScript
                        --}}
                        <small class="text-muted">
                            Ultimo aggiornamento: <span id="last-update-time">{{ now()->format('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Chiude la sezione content --}}
@endsection

{{-- 
    === SEZIONE JAVASCRIPT ===
    Linguaggio: JavaScript ES6+ con integrazione Blade/Laravel
    @push: Direttiva Blade per aggiungere script allo stack 'scripts' del layout
    Funzione: Logica client-side per interattività dashboard e gestione dati dinamici
--}}
@push('scripts')
<script>
/*
 * === INIZIALIZZAZIONE NAMESPACE GLOBALE ===
 * Linguaggio: JavaScript Object Pattern
 * Funzione: Sistema centralizzato per condividere dati tra script
 * Pattern: Fail-safe initialization per prevenire conflitti
 */

// Inizializza oggetto globale se non esiste (pattern sicuro per multi-vista)
window.PageData = window.PageData || {};

/*
 * === INTEGRAZIONE DATI BACKEND-FRONTEND ===
 * Linguaggio: Blade Template + JavaScript + JSON
 * Funzione: Trasferisce dati dal Controller Laravel a JavaScript client-side
 * Utilizzo: @json() directive per serializzazione sicura, isset() per controlli esistenza
 * 
 * Questo pattern permette:
 * - Accesso JavaScript ai dati PHP senza chiamate AJAX aggiuntive
 * - Serializzazione JSON sicura (escape automatico)
 * - Controlli condizionali per evitare errori se variabili non esistono
 * - Organizzazione namespace per evitare conflitti globali
 */

// Dati prodotto singolo (per viste dettaglio)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Collezione prodotti (per liste e tabelle)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento singolo (per viste dettaglio problemi)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Collezione malfunzionamenti (per liste problemi)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro assistenza singolo (per viste dettaglio)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Collezione centri assistenza
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Array categorie prodotti (per filtri e form)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Collezione membri staff (per assegnazioni)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche dashboard (per aggiornamenti dinamici)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente corrente (per personalizzazione UI)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
 * === GESTIONE REFRESH MANUALE ===
 * Linguaggio: JavaScript con DOM API
 * Funzione: Permette aggiornamento dati dashboard senza ricaricare pagina completa
 * 
 * Event Flow:
 * 1. Click su pulsante refresh
 * 2. Aggiorna timestamp visibile
 * 3. Potenziale chiamata AJAX per nuovi dati (da implementare)
 * 4. Aggiornamento UI con nuovi dati
 */

// Event listener per caricamento DOM completo
document.addEventListener('DOMContentLoaded', function() {
    
    /*
     * === PULSANTE REFRESH MANUALE ===
     * getElementById: Seleziona elemento per ID specifico
     * addEventListener: Registra handler per evento click
     */
    const refreshBtn = document.getElementById('manual-refresh-btn');
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            
            /*
             * === FEEDBACK VISIVO AGGIORNAMENTO ===
             * Cambia temporaneamente il pulsante per indicare azione in corso
             * innerHTML: Modifica contenuto HTML dell'elemento
             */
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Aggiornando...';
            this.disabled = true; // Previene click multipli
            
            /*
             * === AGGIORNAMENTO TIMESTAMP ===
             * Aggiorna immediatamente il timestamp visibile
             * new Date(): Crea oggetto data corrente
             * toLocaleTimeString(): Formatta ora locale
             */
            const timeElement = document.getElementById('last-update-time');
            if (timeElement) {
                timeElement.textContent = new Date().toLocaleTimeString('it-IT');
            }
            
            /*
             * === SIMULAZIONE TEMPO CARICAMENTO ===
             * setTimeout: Simula tempo di elaborazione per UX realistica
             * In implementazione reale, questo sarebbe sostituito da chiamata AJAX
             */
            setTimeout(() => {
                // Ripristina pulsante allo stato originale
                this.innerHTML = originalContent;
                this.disabled = false;
                
                /*
                 * === PLACEHOLDER PER AGGIORNAMENTO DATI ===
                 * Qui andrebbero implementate chiamate AJAX per:
                 * - Aggiornamento statistiche
                 * - Refresh dati utenti recenti
                 * - Update conteggi prodotti non assegnati
                 * - Verifica stato sistema
                 */
                
                // Esempio di come potrebbe funzionare l'aggiornamento:
                // fetch('/admin/dashboard/stats')
                //     .then(response => response.json())
                //     .then(data => {
                //         // Aggiorna elementi DOM con nuovi dati
                //         updateStatistics(data);
                //     })
                //     .catch(error => console.error('Errore aggiornamento:', error));
                
            }, 1000); // 1 secondo di delay simulato
        });
    }
});

/*
 * === FUNZIONI UTILITY PER DASHBOARD ===
 * Funzioni riutilizzabili per operazioni comuni della dashboard
 * Linguaggio: JavaScript ES6+ Functions
 */

/*
 * Funzione per aggiornare statistiche nella UI
 * @param {Object} newStats - Nuovi dati statistici dal server
 */
function updateStatistics(newStats) {
    // Aggiorna contatori nelle card statistiche
    if (newStats.total_utenti !== undefined) {
        const utentiElement = document.querySelector('.text-danger h5');
        if (utentiElement) utentiElement.textContent = newStats.total_utenti;
    }
    
    if (newStats.total_prodotti !== undefined) {
        const prodottiElement = document.querySelector('.text-primary h5');
        if (prodottiElement) prodottiElement.textContent = newStats.total_prodotti;
    }
    
    // Aggiorna altri elementi statistici...
}

/*
 * Funzione per notifiche toast
 * @param {string} message - Messaggio da mostrare
 * @param {string} type - Tipo di notifica (success, warning, error)
 */
function showNotification(message, type = 'info') {
    // Implementazione toast Bootstrap o notifiche personalizzate
    console.log(`${type.toUpperCase()}: ${message}`);
}

/*
 * === CONFIGURAZIONE DASHBOARD ===
 * Object con impostazioni e stato corrente della dashboard
 * Linguaggio: JavaScript Object Literal
 */
window.AdminDashboard = {
    // Configurazione generale
    config: {
        autoRefresh: false,           // Auto-refresh abilitato
        refreshInterval: 30000,       // Intervallo refresh (30 secondi)
        enableNotifications: true,    // Notifiche abilitate
        theme: 'light'               // Tema UI
    },
    
    // Stato corrente
    state: {
        lastRefresh: new Date(),      // Ultimo aggiornamento
        isRefreshing: false,          // Flag refresh in corso
        activeModals: [],             // Modal attualmente aperti
        currentUser: window.PageData.user || null  // Utente corrente
    },
    
    // Metodi utili
    methods: {
        /*
         * Refresh completo dashboard
         */
        refresh: function() {
            if (this.state.isRefreshing) return;
            
            this.state.isRefreshing = true;
            // Implementa refresh logic
            
            setTimeout(() => {
                this.state.isRefreshing = false;
                this.state.lastRefresh = new Date();
            }, 1000);
        },
        
        /*
         * Toggle auto-refresh
         */
        toggleAutoRefresh: function() {
            this.config.autoRefresh = !this.config.autoRefresh;
            
            if (this.config.autoRefresh) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        },
        
        /*
         * Avvia auto-refresh
         */
        startAutoRefresh: function() {
            if (this.refreshTimer) clearInterval(this.refreshTimer);
            
            this.refreshTimer = setInterval(() => {
                this.refresh();
            }, this.config.refreshInterval);
        },
        
        /*
         * Ferma auto-refresh
         */
        stopAutoRefresh: function() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
                this.refreshTimer = null;
            }
        }
    }
};

/*
 * === DEBUG E MONITORAGGIO ===
 * Output informativo per sviluppo e troubleshooting
 * Linguaggio: JavaScript Console API
 */
console.log('🚀 Dashboard Amministratore caricata');
console.log('📊 Dati disponibili:', Object.keys(window.PageData));
console.log('⚙️ Configurazione:', window.AdminDashboard.config);

/*
 * === PERFORMANCE MONITORING ===
 * Traccia metriche di performance per ottimizzazioni
 */
if (window.performance && window.performance.mark) {
    performance.mark('dashboard-script-loaded');
    
    // Calcola tempo di caricamento script
    const loadTime = performance.now();
    console.log(`⏱️ Script caricato in ${loadTime.toFixed(2)}ms`);
}

/*
 * === ERROR HANDLING GLOBALE ===
 * Cattura errori JavaScript per debugging
 */
window.addEventListener('error', function(event) {
    console.error('❌ Errore Dashboard:', event.error);
    
    // In produzione, inviare errori a servizio di monitoring
    // errorReporting.log(event.error);
});

/*
 * === CLEANUP ON UNLOAD ===
 * Pulizia risorse quando si lascia la pagina
 */
window.addEventListener('beforeunload', function() {
    // Ferma auto-refresh se attivo
    if (window.AdminDashboard && window.AdminDashboard.methods) {
        window.AdminDashboard.methods.stopAutoRefresh();
    }
    
    console.log('🔄 Dashboard cleanup completato');
});
</script>
@endpush

{{-- 
    === SEZIONE STILI CSS PERSONALIZZATI ===
    Linguaggio: CSS3 con approccio component-based e responsive design
    Funzione: Stili specifici per dashboard amministratore
    
    Filosofia design:
    - Uniformità visiva con altre dashboard (tecnico/staff)
    - Responsive design mobile-first
    - Performance ottimizzata
    - Accessibilità WCAG compliant
--}}
<style>
/* 
 * === STILI UNIFORMI COMPONENTI ===
 * Design system coerente per tutti i livelli utente
 */

/* 
 * Card con design moderno e border-radius uniformi
 * border-radius: 12px per angoli arrotondati moderni
 * border: none per design flat/minimal
 * overflow: hidden per contenuto che sconfina
 */
.card {
    border-radius: 12px;
    border: none !important; /* !important per sovrascrivere Bootstrap */
    overflow: hidden;
}

/* 
 * Header card con border-radius che rispetti il parent
 * font-size ridotto per compattezza
 */
.card-header {
    border-radius: 12px 12px 0 0 !important;
    font-size: 0.9rem;
}

/* 
 * Body card con font-size compatto
 */
.card-body {
    font-size: 0.9rem;
}

/* 
 * === SISTEMA BADGE LIVELLI UTENTE ===
 * Badge colorati per identificazione rapida livelli accesso
 * font-size e font-weight per leggibilità ottimale
 */
.badge-livello {
    font-size: 0.7rem;
    font-weight: 600;
}

/* Colori specifici per ogni livello - sistema cromatico coerente */
.badge-livello-1 { background-color: #6c757d !important; } /* Grigio - Pubblico */
.badge-livello-2 { background-color: #0dcaf0 !important; } /* Azzurro - Tecnici */
.badge-livello-3 { background-color: #ffc107 !important; color: #000 !important; } /* Giallo - Staff */
.badge-livello-4 { background-color: #dc3545 !important; } /* Rosso - Admin */

/* 
 * === EFFETTI INTERATTIVI ===
 * Micro-animazioni per feedback utente e UX migliorata
 */

/* 
 * Hover effect per pulsanti grandi di gestione
 * transform: translateY per effetto "elevazione"
 * transition per animazione fluida
 */
.btn-lg:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* 
 * === SISTEMA COLORI BADGE STATO ===
 * Override Bootstrap per colori consistenti
 */
.badge-success { background-color: #198754 !important; }
.badge-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge-danger { background-color: #dc3545 !important; }
.badge-info { background-color: #0dcaf0 !important; }

/* 
 * === ANIMAZIONI CSS ===
 * Keyframes per animazioni riutilizzabili
 */

/* 
 * Animazione pulse per indicatori di caricamento
 * opacity che varia per effetto "respirazione"
 */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Classe utility per applicare animazione pulse */
.updating {
    animation: pulse 1s infinite;
}

/* 
 * Animazione gradiente per header dinamici
 * background-position che si muove per effetto fluido
 */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* 
 * === COMPONENTI UI SPECIFICI ===
 */

/* 
 * Avatar per rappresentazione utenti
 * Dimensioni fisse per consistenza
 */
.avatar {
    width: 50px;
    height: 50px;
}

/* 
 * Background gradiente animato
 * background-size: 200% per permettere movimento
 * animation applicata per movimento continuo
 */
.bg-gradient {
    background-size: 200% 200%;
    animation: gradient-shift 10s ease infinite;
}

/* 
 * === EFFETTI HOVER GLOBALI ===
 * Feedback visivo per elementi interattivi
 */

/* 
 * Hover effect per card
 * transform: translateY per effetto elevazione sottile
 * transition per animazione fluida
 */
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

/* 
 * Badge con dimensioni uniformi ridotte
 */
.badge {
    font-size: 0.7rem;
}

/* 
 * === RESPONSIVE DESIGN ===
 * Media queries per adattamento dispositivi diversi
 * Approccio mobile-first per performance ottimale
 */

/* 
 * Tablet e dispositivi medi (768px e inferiori)
 */
@media (max-width: 768px) {
    /* Margin bottom per spacing verticale su mobile */
    .col-lg-2, .col-lg-4, .col-lg-6 {
        margin-bottom: 1rem;
    }
    
    /* Pulsanti grandi ridotti per touch screen */
    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    /* Icone display ridotte per spazio limitato */
    .display-6 {
        font-size: 2rem !important;
    }
    
    /* Avatar più piccoli su mobile */
    .avatar {
        width: 40px;
        height: 40px;
    }
    
    /* Padding card ridotto */
    .card-body {
        padding: 0.75rem;
    }
    
    /* 
     * Layout flex modificato per mobile
     * Colonna invece che riga per migliore utilizzo spazio
     */
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: start !important;
    }
    
    .text-end {
        margin-top: 0.5rem;
    }
}

/* 
 * Smartphone e dispositivi piccoli (576px e inferiori)
 */
@media (max-width: 576px) {
    /* Container padding ridotto per massimizzare spazio */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    /* 
     * Grid modificata per mobile
     * Due pulsanti per riga su schermi molto piccoli
     */
    .col-lg-2 {
        flex: 0 0 auto;
        width: 50%;
    }
    
    /* Font size ridotto per elementi small */
    .small {
        font-size: 0.75rem !important;
    }
}

/* 
 * === ACCESSIBILITÀ ===
 * Miglioramenti per utenti con disabilità
 * Segue guidelines WCAG 2.1
 */

/* 
 * Focus outline personalizzato per elementi interattivi
 * box-shadow invece di outline per maggiore controllo
 */
.btn:focus,
.list-group-item:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* 
 * === COMPONENTI UTILITY ===
 */

/* 
 * Loading spinner CSS puro
 * Alternativa leggera a librerie esterne
 */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 0.125em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
}

/* Keyframe per rotazione spinner */
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* 
 * Toast personalizzati
 * min-width per consistenza dimensioni
 */
.toast {
    min-width: 300px;
}

/* 
 * === STILI LISTE ===
 * Rimozione bordi per design più pulito
 */
.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* 
 * === TIPOGRAFIA ===
 * Miglioramenti leggibilità e gerarchia visiva
 */

/* Font weight semi-bold per enfasi moderata */
.fw-semibold {
    font-weight: 600;
}

/* Utility per truncate text overflow */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* 
 * === TRANSIZIONI GLOBALI ===
 * Animazioni fluide per tutti gli elementi interattivi
 */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}

/* 
 * === PERSONALIZZAZIONE COLORI ===
 * Override Bootstrap per brand consistency
 */
.text-muted {
    color: #6c757d !important;
}

/* 
 * === SCROLLBAR PERSONALIZZATA ===
 * Webkit scrollbar per browser moderni
 */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* 
 * === DARK MODE SUPPORT ===
 * Media query per preferenza sistema dark mode
 * Preparazione per future implementazioni
 */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1e1e1e;
        color: #ffffff;
    }
    
    .bg-light {
        background-color: #2d2d2d !important;
        color: #ffffff;
    }
    
    .text-muted {
        color: #a0a0a0 !important;
    }
}

/* 
 * === PRINT STYLES ===
 * Ottimizzazioni per stampa dashboard
 */
@media print {
    /* Nascondi elementi interattivi non necessari in stampa */
    .btn, .alert, .modal {
        display: none !important;
    }
    
    /* Rimuovi ombre e effetti per stampa pulita */
    .card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    /* Assicura testo nero per leggibilità stampa */
    body, .text-muted {
        color: #000 !important;
    }
}
</style>