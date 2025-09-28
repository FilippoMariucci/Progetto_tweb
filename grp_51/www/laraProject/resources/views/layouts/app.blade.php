{{-- 
=======================================================================
FILE: resources/views/layouts/app.blade.php - FILE COMPLETO
LINGUAGGIO: Blade (PHP + HTML)
DESCRIZIONE: Template principale dell'applicazione Laravel TechSupport Pro
            Layout base utilizzato da tutte le pagine del sito
=======================================================================
--}}

{{-- 
    DOCTYPE HTML5 standard
    Definisce che questo documento utilizza HTML5
--}}
<!DOCTYPE html>

{{-- 
    Elemento HTML root con lingua italiana
    L'attributo lang="it" specifica la lingua del contenuto per accessibilità
--}}
<html lang="it">

{{-- SEZIONE HEAD: Metadati e configurazione della pagina --}}
<head>
    {{-- 
        META TAG CSRF TOKEN per Laravel
        Imposta il token CSRF globalmente per la sicurezza delle form e richieste AJAX
        Laravel usa questo token per prevenire attacchi Cross-Site Request Forgery
    --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 
        SCRIPT JAVASCRIPT: Configurazione globale CSRF
        Imposta il CSRF token in un oggetto JavaScript globale 'window.Laravel'
        Questo permette alle chiamate AJAX di includere automaticamente il token
    --}}
    <script>
        // Oggetto globale JavaScript per Laravel
        // Contiene configurazioni accessibili da qualsiasi script nella pagina
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}" // Token CSRF dinamico generato da Laravel
        };
    </script>

    {{-- 
        FAVICON: Icona del sito
        asset() è una funzione helper di Laravel che genera l'URL completo
        per file statici nella cartella public/
    --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    {{-- META TAG STANDARD HTML --}}
    <meta charset="UTF-8"> {{-- Codifica caratteri UTF-8 per supporto internazionale --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> {{-- Responsive design --}}
    
    {{-- 
        TITOLO DINAMICO DELLA PAGINA
        @yield('title', 'default') è una direttiva Blade che:
        1. Cerca una sezione chiamata 'title' nelle view figlie
        2. Se non la trova, usa il valore di default 'Assistenza Tecnica Online'
        3. Concatena sempre "- TechSupport Pro" alla fine
    --}}
    <title>@yield('title', 'Assistenza Tecnica Online') - TechSupport Pro</title>
    
    {{-- 
        CSS FRAMEWORK BOOTSTRAP 5.3
        CDN (Content Delivery Network) per Bootstrap CSS
        Fornisce classi predefinite per layout responsive e componenti UI
    --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- 
        BOOTSTRAP ICONS
        Libreria di icone vettoriali compatibile con Bootstrap
        Permette l'uso di icone con classi CSS come 'bi bi-house'
    --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    {{-- 
        CSS PERSONALIZZATO INCORPORATO
        Stili CSS custom specifici per l'applicazione TechSupport Pro
        Definiti inline per semplicità e performance
    --}}
    <style>
        /* ============================================================
           VARIABILI CSS CUSTOM (CSS Custom Properties)
           Definiscono colori e valori riutilizzabili in tutto il CSS
           ============================================================ */
        :root {
            --primary-color: #2563eb;   /* Blu principale dell'applicazione */
            --secondary-color: #64748b; /* Grigio secondario */
            --success-color: #16a34a;   /* Verde per messaggi di successo */
            --warning-color: #d97706;   /* Arancione per avvisi */
            --danger-color: #dc2626;    /* Rosso per errori */
            --info-color: #0891b2;      /* Azzurro per informazioni */
            --dark-color: #1e293b;      /* Nero/grigio scuro */
            --light-color: #f8fafc;     /* Grigio chiaro per sfondi */
        }
        
        /* ============================================================
           STILI GLOBALI DEL BODY
           Applicati a tutto il documento
           ============================================================ */
        body {
            /* Font stack con fallback per compatibilità cross-browser */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            
            /* Colore di sfondo usando variabile CSS */
            background-color: var(--light-color);
            
            /* Interlinea per migliorare la leggibilità */
            line-height: 1.6;
        }

        /* ============================================================
           NAVBAR PERSONALIZZATA
           Stili per la barra di navigazione principale
           ============================================================ */
        .navbar-custom {
            /* Gradiente lineare per effetto visivo moderno */
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            
            /* Ombra sottile per separare la navbar dal contenuto */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Brand/logo nella navbar */
        .navbar-brand {
            font-weight: bold;           /* Testo in grassetto */
            color: white !important;     /* !important forza il colore bianco */
        }

        /* Link di navigazione nella navbar */
        .nav-link {
            color: rgba(255,255,255,0.9) !important; /* Bianco semi-trasparente */
            transition: color 0.3s ease;               /* Animazione smooth del colore */
        }

        /* Effetti hover e stato attivo per i link */
        .nav-link:hover, .nav-link.active {
            color: white !important;     /* Bianco pieno */
            font-weight: bold;           /* Grassetto per evidenziare */
        }

        /* ============================================================
           BADGE PER LIVELLI UTENTE
           Indicatori visivi del livello di accesso degli utenti
           ============================================================ */
        .badge-livello {
            font-size: 0.75rem;         /* Dimensione testo piccola */
            padding: 0.25rem 0.5rem;    /* Spaziatura interna */
            border-radius: 0.375rem;    /* Bordi arrotondati */
        }

        /* Colori specifici per ogni livello di accesso */
        .badge-livello-1 { background-color: var(--secondary-color); } /* Livello 1: Pubblico */
        .badge-livello-2 { background-color: var(--info-color); }      /* Livello 2: Tecnici */
        .badge-livello-3 { background-color: var(--warning-color); }   /* Livello 3: Staff */
        .badge-livello-4 { background-color: var(--danger-color); }    /* Livello 4: Admin */

        /* ============================================================
           CARD PERSONALIZZATE
           Stili per i componenti card utilizzati in tutto il sito
           ============================================================ */
        .card-custom {
            border: none;                /* Rimuove bordo predefinito */
            border-radius: 0.75rem;     /* Bordi molto arrotondati */
            
            /* Ombra per effetto "elevazione" */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            
            /* Transizioni smooth per animazioni hover */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Effetto hover per le card */
        .card-custom:hover {
            transform: translateY(-2px);           /* Movimento verso l'alto */
            box-shadow: 0 8px 15px rgba(0,0,0,0.15); /* Ombra più marcata */
        }

        /* ============================================================
           GRAVITÀ MALFUNZIONAMENTI
           Stili per indicare la severità dei problemi tecnici
           ============================================================ */
           
        /* Colore verde per malfunzionamenti di bassa gravità */
        .gravita-bassa { 
            color: var(--success-color); 
        }
        
        /* Colore arancione per malfunzionamenti di media gravità */
        .gravita-media { 
            color: var(--warning-color); 
        }
        
        /* Colore rosso per malfunzionamenti di alta gravità */
        .gravita-alta { 
            color: var(--danger-color); 
        }
        
        /* Stile speciale per malfunzionamenti critici */
        .gravita-critica { 
            color: var(--danger-color);  /* Rosso intenso */
            font-weight: bold;           /* Grassetto per enfasi */
            animation: pulse 2s infinite; /* Animazione di pulsazione continua */
        }

        /* 
           ANIMAZIONE PULSE per gravità critica
           Crea un effetto di "pulsazione" per attirare l'attenzione
        */
        @keyframes pulse {
            0% { opacity: 1; }    /* Inizio: completamente visibile */
            50% { opacity: 0.7; } /* Metà: parzialmente trasparente */
            100% { opacity: 1; }  /* Fine: ritorna completamente visibile */
        }

        /* ============================================================
           FOOTER PERSONALIZZATO
           Stili per il piè di pagina del sito
           ============================================================ */
        .footer-custom {
            background-color: var(--dark-color); /* Sfondo scuro */
            color: white;                         /* Testo bianco */
            margin-top: auto;                     /* Push automatico in basso */
        }

        /* ============================================================
           CLASSI UTILITY
           Classi helper riutilizzabili per layout e effetti
           ============================================================ */
           
        /* 
           Classe per layout full-height con flexbox
           Assicura che la pagina occupi almeno l'intera altezza dello schermo
        */
        .min-vh-100 {
            min-height: 100vh;        /* Altezza minima = altezza viewport */
            display: flex;            /* Layout flexbox */
            flex-direction: column;   /* Disposizione verticale degli elementi */
        }

        /* 
           Evidenziazione per risultati di ricerca
           Stile giallo per evidenziare testo trovato nelle ricerche
        */
        .search-highlight {
            background-color: #fef3c7;      /* Sfondo giallo chiaro */
            padding: 0.125rem 0.25rem;      /* Padding interno */
            border-radius: 0.25rem;         /* Bordi leggermente arrotondati */
        }

        /* ============================================================
           RESPONSIVE DESIGN
           Media query per adattamento a schermi piccoli (mobile)
           ============================================================ */
        @media (max-width: 768px) {
            /* Su mobile, centra il testo della navbar */
            .navbar-nav {
                text-align: center;
            }
            
            /* Su mobile, aggiunge margine inferiore alle card */
            .card-custom {
                margin-bottom: 1rem;
            }
        }

        /* ============================================================
           SPINNER DI CARICAMENTO
           Indicatore visivo per operazioni in corso
           ============================================================ */
        .spinner-custom {
            display: inline-block;           /* Visualizzazione inline */
            width: 1rem;                     /* Larghezza fissa */
            height: 1rem;                    /* Altezza fissa */
            border: 2px solid transparent;   /* Bordo trasparente di base */
            border-top: 2px solid var(--primary-color); /* Solo il bordo superiore colorato */
            border-radius: 50%;              /* Forma circolare */
            animation: spin 1s linear infinite; /* Animazione di rotazione continua */
        }

        /* 
           ANIMAZIONE SPIN per lo spinner
           Rotazione a 360 gradi per creare l'effetto di caricamento
        */
        @keyframes spin {
            0% { transform: rotate(0deg); }   /* Inizio: 0 gradi */
            100% { transform: rotate(360deg); } /* Fine: rotazione completa */
        }

        /* ============================================================
           ANIMAZIONI DROPDOWN
           Stili per menu a tendina migliorati
           ============================================================ */
           
        /* Menu dropdown con stili personalizzati */
        .dropdown-menu {
            border: none;                      /* Rimuove bordo predefinito */
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); /* Ombra più pronunciata */
            border-radius: 0.5rem;            /* Bordi arrotondati */
        }

        /* Effetto hover per gli elementi del dropdown */
        .dropdown-item:hover {
            /* Sfondo primario semi-trasparente */
            background-color: rgba(var(--primary-color), 0.1);
        }
    </style>
    
    {{-- 
        STACK PER STILI AGGIUNTIVI
        @stack('styles') permette alle view figlie di aggiungere CSS specifici
        Le view possono usare @push('styles') per inserire stili personalizzati
    --}}
    @stack('styles')
</head>

{{-- 
    BODY con classe per layout full-height
    La classe min-vh-100 assicura che il layout occupi sempre l'intera altezza
--}}
<body class="min-vh-100">
    
    {{-- ============================================================
         NAVBAR DINAMICA PRINCIPALE
         Barra di navigazione che si adatta al livello utente
         ============================================================ --}}
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            {{-- 
                LOGO E BRAND dell'applicazione
                route('home') genera dinamicamente l'URL della homepage
            --}}
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-wrench-adjustable me-2"></i> {{-- Icona chiave inglese --}}
                TechSupport Pro
            </a>

            {{-- 
                PULSANTE HAMBURGER per dispositivi mobile
                data-bs-toggle e data-bs-target sono attributi Bootstrap
                per controllare il comportamento del menu collassabile
            --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- 
                MENU DI NAVIGAZIONE COLLASSABILE
                Su desktop è sempre visibile, su mobile si nasconde/mostra
            --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto"> {{-- me-auto spinge il menu utente a destra --}}
                    
                    {{-- ============================================================
                         LINK PUBBLICI (Livello 1 - Sempre visibili)
                         Questi link sono accessibili a tutti gli utenti
                         ============================================================ --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    </li>
                    
                    {{-- 
                        CATALOGO PRODOTTI PUBBLICO (senza malfunzionamenti)
                        Usa la route PUBBLICA definita in web.php
                        Route::get('/catalogo', [ProdottoController::class, 'indexPubblico'])
                    --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('prodotti.pubblico.index') }}">
                            <i class="bi bi-box me-1"></i>Catalogo Pubblico
                        </a>
                    </li>
                    
                    {{-- 
                        CENTRI ASSISTENZA PUBBLICI
                        IMPORTANTE: Usa la route pubblica corretta
                        Route::get('/centri-assistenza', [CentroAssistenzaController::class, 'index'])
                        Questa route mostra la vista PUBBLICA dei centri senza funzionalità admin
                    --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('centri.index') }}">
                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                        </a>
                    </li>

                    {{-- ============================================================
                         MENU DINAMICO PER UTENTI AUTENTICATI
                         Questo blocco si mostra solo se l'utente è loggato
                         ============================================================ --}}
                    @auth {{-- Direttiva Blade: esegue solo se l'utente è autenticato --}}
                        @php
                            // Codice PHP inline per ottenere dati utente corrente
                            $user = Auth::user();              // Utente autenticato corrente
                            $livello = $user->livello_accesso; // Livello di accesso (1-4)
                        @endphp
                        
                        {{-- 
                            DASHBOARD PERSONALIZZATA PER LIVELLO
                            Ogni tipo di utente ha una dashboard specifica
                        --}}
                        <li class="nav-item">
                            @if($livello == 4)
                                {{-- ADMIN: Dashboard amministratore --}}
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                                </a>
                            @elseif($livello == 3)
                                {{-- STAFF: Dashboard staff aziendale --}}
                                <a class="nav-link" href="{{ route('staff.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Staff
                                </a>
                            @elseif($livello == 2)
                                {{-- TECNICO: Dashboard tecnico --}}
                                <a class="nav-link" href="{{ route('tecnico.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Tecnico
                                </a>
                            @else
                                {{-- UTENTE STANDARD: Dashboard generale --}}
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            @endif
                        </li>

                        {{-- ============================================================
                             MENU AMMINISTRATIVO CONDIZIONALE
                             Mostra opzioni diverse basate sul livello utente
                             Livello 2+ = Tecnici, Staff e Admin
                             ============================================================ --}}
                        @if($livello >= 2)
                            <li class="nav-item dropdown">
                                {{-- 
                                    TITOLO DROPDOWN basato sul livello
                                    data-bs-toggle="dropdown" attiva il menu a tendina Bootstrap
                                --}}
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-tools me-1"></i>
                                    @if($livello == 4)
                                        Amministrazione {{-- Admin: controllo completo --}}
                                    @elseif($livello == 3)
                                        Gestione Staff   {{-- Staff: gestione malfunzionamenti --}}
                                    @else
                                        Gestione Tecnico {{-- Tecnico: strumenti diagnostici --}}
                                    @endif
                                </a>
                                
                                {{-- 
                                    CONTENUTO DROPDOWN
                                    Lista di opzioni specifiche per ogni livello
                                --}}
                                <ul class="dropdown-menu">
                                    @if($livello == 4)
                                        {{-- ============================================================
                                             MENU AMMINISTRATORE (Livello 4)
                                             Accesso completo a tutte le funzionalità
                                             ============================================================ --}}
                                        
                                        {{-- Sezione Gestione Utenti --}}
                                        <li><h6 class="dropdown-header">Gestione Utenti</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="bi bi-people me-1"></i>Tutti gli Utenti
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                                        </a></li>
                                        
                                        {{-- Separatore visivo --}}
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Gestione Prodotti --}}
                                        <li><h6 class="dropdown-header">Gestione Prodotti</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.prodotti.index') }}">
                                            <i class="bi bi-box me-1"></i>Gestisci Prodotti
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.prodotti.create') }}">
                                            <i class="bi bi-plus-square me-1"></i>Nuovo Prodotto
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Centri Assistenza --}}
                                        <li><h6 class="dropdown-header">Centri Assistenza</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.centri.index') }}">
                                            <i class="bi bi-building me-1"></i>Gestisci Centri
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.centri.create') }}">
                                            <i class="bi bi-plus-circle me-1"></i>Nuovo Centro
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Sistema --}}
                                        <li><h6 class="dropdown-header">Sistema</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.statistiche.index') }}">
                                            <i class="bi bi-graph-up me-1"></i>Statistiche Sistema
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.manutenzione.index') }}">
                                            <i class="bi bi-gear me-1"></i>Manutenzione
                                        </a></li>
                                        
                                    @elseif($livello == 3)
                                        {{-- ============================================================
                                             MENU STAFF AZIENDALE (Livello 3)
                                             Gestione malfunzionamenti e statistiche
                                             ============================================================ --}}
                                        
                                        {{-- Sezione Catalogo e Prodotti --}}
                                        <li><h6 class="dropdown-header">Catalogo e Prodotti</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('prodotti.completo.index') }}">
                                            <i class="bi bi-box-seam me-1"></i>Catalogo Completo
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Malfunzionamenti --}}
                                        <li><h6 class="dropdown-header">Malfunzionamenti</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}">
                                            <i class="bi bi-search me-1"></i>Ricerca Soluzioni
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('staff.create.nuova.soluzione') }}">
                                            <i class="bi bi-plus-circle me-1"></i>Crea Soluzione
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Statistiche --}}
                                        <li><h6 class="dropdown-header">Statistiche</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('staff.statistiche') }}">
                                            <i class="bi bi-graph-up me-1"></i>Mie Statistiche
                                        </a></li>
                                        
                                    @else
                                        {{-- ============================================================
                                             MENU TECNICO (Livello 2)
                                             Strumenti diagnostici e di riparazione
                                             ============================================================ --}}
                                        
                                        {{-- Sezione Strumenti Tecnici --}}
                                        <li><h6 class="dropdown-header">
                                            <i class="bi bi-wrench-adjustable me-1"></i>Strumenti Tecnici
                                        </h6></li>
                                        
                                        {{-- Catalogo completo con malfunzionamenti --}}
                                        <li><a class="dropdown-item" href="{{ route('prodotti.completo.index') }}">
                                            <i class="bi bi-collection me-2"></i>Catalogo Completo
                                        </a></li>
                                        
                                        {{-- Ricerca malfunzionamenti e soluzioni --}}
                                        <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}">
                                            <i class="bi bi-search me-2"></i>Ricerca Soluzioni
                                        </a></li>
                                        
                                        {{-- Prodotti con priorità critica (filtro rapido) --}}
                                        <li><a class="dropdown-item" href="{{ route('prodotti.completo.index') }}?filter=critici">
                                            <i class="bi bi-exclamation-triangle text-danger me-2"></i>Priorità Critica
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Area Personale --}}
                                        <li><h6 class="dropdown-header">
                                            <i class="bi bi-person-gear me-1"></i>Area Personale
                                        </h6></li>
                                        
                                        {{-- Storico interventi del tecnico --}}
                                        <li><a class="dropdown-item" href="{{ route('tecnico.interventi') }}">
                                            <i class="bi bi-clock-history me-2"></i>Miei Interventi
                                        </a></li>
                                        
                                        {{-- Statistiche personali --}}
                                        <li><a class="dropdown-item" href="{{ route('tecnico.statistiche.view') }}">
                                            <i class="bi bi-graph-up me-2"></i>Le Mie Statistiche
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Assistenza --}}
                                        <li><h6 class="dropdown-header">
                                            <i class="bi bi-geo-alt me-1"></i>Assistenza
                                        </h6></li>
                                        
                                        {{-- 
                                            Centro assistenza del tecnico (se assegnato)
                                            Controlla se l'utente corrente ha un centro assegnato
                                        --}}
                                        @if(Auth::user()->centroAssistenza)
                                            <li><a class="dropdown-item" href="{{ route('centri.show', Auth::user()->centroAssistenza) }}">
                                                <i class="bi bi-building me-2"></i>Il Mio Centro
                                            </a></li>
                                        @endif
                                        
                                        {{-- Tutti i centri assistenza --}}
                                        <li><a class="dropdown-item" href="{{ route('centri.index') }}">
                                            <i class="bi bi-geo-alt-fill me-2"></i>Tutti i Centri
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        {{-- Sezione Azioni Rapide --}}
                                        <li><h6 class="dropdown-header">
                                            <i class="bi bi-tools me-1"></i>Azioni Rapide
                                        </h6></li>
                                        
                                        {{-- Ricerca rapida prodotti con wildcard --}}
                                        <li><a class="dropdown-item" href="{{ route('prodotti.completo.ricerca') }}?search=lav*">
                                            <i class="bi bi-search me-2"></i>Cerca "lav*"
                                        </a></li>
                                        
                                        {{-- Malfunzionamenti più comuni - Ricerca preimpostata --}}
                                        <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}?q=non+si+accende">
                                            <i class="bi bi-lightning me-2"></i>Non si accende
                                        </a></li>
                                        
                                        <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}?q=perdita">
                                            <i class="bi bi-droplet me-2"></i>Perdite
                                        </a></li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        
                                    @endif {{-- Fine condizione livello utente --}}
                                </ul>
                            </li>
                        @endif {{-- Fine condizione livello >= 2 --}}
                    @endauth {{-- Fine blocco utenti autenticati --}}
                </ul>

                {{-- ============================================================
                     MENU UTENTE (Lato destro della navbar)
                     Informazioni e azioni relative all'account utente
                     ============================================================ --}}
                <ul class="navbar-nav">
                    @guest {{-- Direttiva Blade: esegue solo se l'utente NON è autenticato --}}
                        {{-- UTENTE NON LOGGATO: Mostra link di accesso --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Accedi
                            </a>
                        </li>
                    @else {{-- Utente loggato --}}
                        {{-- DROPDOWN PROFILO UTENTE --}}
                        <li class="nav-item dropdown">
                            {{-- 
                                Trigger del dropdown con informazioni utente
                                Mostra nome, cognome e badge del livello
                            --}}
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                                
                                {{-- 
                                    BADGE LIVELLO UTENTE
                                    Indicatore visivo colorato del livello di accesso
                                --}}
                                <span class="badge badge-livello badge-livello-{{ Auth::user()->livello_accesso }} ms-1">
                                    @switch(Auth::user()->livello_accesso)
                                        @case(4) Admin @break      {{-- Livello 4: Amministratore --}}
                                        @case(3) Staff @break      {{-- Livello 3: Staff aziendale --}}
                                        @case(2) Tecnico @break    {{-- Livello 2: Tecnico --}}
                                        @default Utente           {{-- Livello 1: Utente standard --}}
                                    @endswitch
                                </span>
                            </a>
                            
                            {{-- 
                                CONTENUTO DROPDOWN PROFILO
                                dropdown-menu-end allinea il menu a destra
                            --}}
                            <ul class="dropdown-menu dropdown-menu-end">
                                {{-- 
                                    LINK DASHBOARD PRINCIPALE
                                    Varia in base al livello utente
                                --}}
                                <li>
                                    @if(Auth::user()->livello_accesso == 4)
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                                        </a>
                                    @elseif(Auth::user()->livello_accesso == 3)
                                        <a class="dropdown-item" href="{{ route('staff.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Staff
                                        </a>
                                    @elseif(Auth::user()->livello_accesso == 2)
                                        <a class="dropdown-item" href="{{ route('tecnico.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Tecnico
                                        </a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i>La Mia Dashboard
                                        </a>
                                    @endif
                                </li>
                                
                                {{-- 
                                    LINK SPECIFICI PER LIVELLO
                                    Solo per Staff (3) e Admin (4)
                                --}}
                                @if(Auth::user()->livello_accesso >= 3)
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Auth::user()->livello_accesso == 3)
                                        {{-- Staff: Link alle proprie statistiche --}}
                                        <li><a class="dropdown-item" href="{{ route('staff.statistiche') }}">
                                            <i class="bi bi-graph-up me-1"></i>Mie Statistiche
                                        </a></li>
                                    @elseif(Auth::user()->livello_accesso == 4)
                                        {{-- Admin: Link alle statistiche di sistema --}}
                                        <li><a class="dropdown-item" href="{{ route('admin.statistiche.index') }}">
                                            <i class="bi bi-bar-chart me-1"></i>Statistiche Sistema
                                        </a></li>
                                    @endif
                                @endif
                                
                                {{-- INFORMAZIONI PROFILO --}}
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    {{-- 
                                        Elemento non cliccabile con informazioni utente
                                        dropdown-item-text è una classe Bootstrap per testo informativo
                                    --}}
                                    <span class="dropdown-item-text">
                                        <small class="text-muted">
                                            <i class="bi bi-person-badge me-1"></i>
                                            {{ Auth::user()->username }}
                                            {{-- 
                                                Mostra centro assistenza se il tecnico ne ha uno assegnato
                                                centroAssistenza è una relazione Eloquent definita nel model User
                                            --}}
                                            @if(Auth::user()->centroAssistenza)
                                                <br><i class="bi bi-geo-alt me-1"></i>{{ Auth::user()->centroAssistenza->nome }}
                                            @endif
                                        </small>
                                    </span>
                                </li>
                                
                                {{-- LOGOUT --}}
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    {{-- 
                                        FORM LOGOUT con metodo POST (richiesto da Laravel per sicurezza)
                                        @csrf genera il token CSRF necessario per la form
                                    --}}
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-1"></i>Esci
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest {{-- Fine blocco utente non autenticato --}}
                </ul>
            </div>
        </div>
    </nav>

    {{-- ============================================================
         BREADCRUMB NAVIGATION (Briciole di pane)
         Sistema di navigazione che mostra il percorso corrente
         Mostra solo se la variabile $breadcrumbs è definita e non vuota
         ============================================================ --}}
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <div class="container mt-3">
            <nav aria-label="breadcrumb"> {{-- aria-label per accessibilità --}}
                <ol class="breadcrumb">
                    {{-- 
                        Loop attraverso array di breadcrumbs
                        $loop è una variabile magica di Blade per controllo cicli
                    --}}
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last) {{-- Se è l'ultimo elemento (pagina corrente) --}}
                            <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                        @else {{-- Se non è l'ultimo, crea link cliccabile --}}
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    @endif

    {{-- ============================================================
         MESSAGGI DI FEEDBACK
         Sistema per mostrare notifiche di successo, errore, avviso
         Utilizza la sessione Laravel per messaggi flash
         ============================================================ --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="container mt-3">
            {{-- MESSAGGIO DI SUCCESSO (verde) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{-- Icona check --}}
                    {{ session('success') }}                {{-- Contenuto messaggio --}}
                    {{-- Pulsante X per chiudere l'alert --}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MESSAGGIO DI ERRORE (rosso) --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i> {{-- Icona errore --}}
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MESSAGGIO DI AVVISO (giallo/arancione) --}}
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{-- Icona avviso --}}
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MESSAGGIO INFORMATIVO (azzurro) --}}
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i> {{-- Icona info --}}
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif

    {{-- ============================================================
         CONTENUTO PRINCIPALE
         Qui vengono renderizzate le singole pagine
         flex-grow-1 fa espandere il main per occupare lo spazio disponibile
         ============================================================ --}}
    <main class="flex-grow-1">
        @yield('content') {{-- Direttiva Blade: inserisce il contenuto delle view figlie --}}
    </main>

    {{-- ============================================================
         FOOTER DEL SITO
         Informazioni aziendali e link utili
         ============================================================ --}}
    <footer class="footer-custom py-4 mt-5">
        <div class="container">
            <div class="row">
                {{-- COLONNA SINISTRA: Informazioni principali --}}
                <div class="col-md-6">
                    <h5>TechSupport Pro</h5>
                    <p class="mb-0">Sistema di assistenza tecnica online per elettrodomestici</p>
                    <small class="text-muted">Gruppo 51 - Tecnologie Web 2024/2025</small>
                </div>
                
                {{-- COLONNA CENTRALE: Link utili --}}
                <div class="col-md-3">
                    <h6>Link Utili</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('azienda') }}" class="text-light text-decoration-none">Chi Siamo</a></li>
                        <li><a href="{{ route('centri.index') }}" class="text-light text-decoration-none">Centri Assistenza</a></li>
                        <li><a href="{{ route('contatti') }}" class="text-light text-decoration-none">Contatti</a></li>
                    </ul>
                </div>
                
                {{-- COLONNA DESTRA: Documentazione --}}
                <div class="col-md-3">
                    <h6>Documentazione</h6>
                    <ul class="list-unstyled">
                        {{-- Link alla documentazione PDF del progetto --}}
                        <li>
                            <a href="{{ route('documentazione') }}" class="text-light text-decoration-none" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i>Documentazione Progetto
                            </a>
                        </li>
                        {{-- 
                            Link di test database (solo per admin)
                            Controllo condizionale per mostrare solo agli amministratori
                        --}}
                        @auth
                            @if(Auth::user()->livello_accesso >= 4)
                                <li><a href="{{ route('test.db') }}" class="text-light text-decoration-none">Test DB</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
            </div>
            
            {{-- LINEA SEPARATRICE --}}
            <hr class="my-3">
            
            {{-- COPYRIGHT --}}
            <div class="row">
                <div class="col-12 text-center">
                    <small>&copy; {{ date('Y') }} TechSupport Pro. Università Politecnica delle Marche.</small>
                </div>
            </div>
        </div>
    </footer>

    {{-- ============================================================
         JAVASCRIPT E LIBRERIE
         Caricamento di librerie JavaScript e configurazione
         ============================================================ --}}
    
    {{-- 
        BOOTSTRAP 5.3 JavaScript Bundle
        Include Bootstrap JS e Popper.js per componenti interattivi
    --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    {{-- 
        JQUERY 3.7
        Libreria JavaScript per manipolazione DOM e AJAX
    --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    
    {{-- 
        JAVASCRIPT PERSONALIZZATO DEL PROGETTO
        File JS specifici dell'applicazione nella cartella public/js/
        asset() genera l'URL corretto per i file statici
    --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    
    {{-- ============================================================
         CONFIGURAZIONE JAVASCRIPT DINAMICA
         Script inline per inizializzazione e configurazione globale
         ============================================================ --}}
    <script>
        // ============================================================
        // INIZIALIZZAZIONE JQUERY
        // Eseguito quando il DOM è completamente caricato
        // ============================================================
        $(document).ready(function() {
            // === CONFIGURAZIONE GLOBALE JAVASCRIPT ===
            // Oggetto globale con dati Laravel accessibili da tutti gli script
            window.LaravelApp = {
                csrfToken: '{{ csrf_token() }}',                    // Token CSRF dinamico
                baseUrl: '{{ url('/') }}',                          // URL base dell'applicazione
                route: @json(request()->route()->getName() ?? ''),  // Nome della route corrente
                user: @json(auth()->user() ?? null),                // Dati utente autenticato (o null)
                locale: '{{ app()->getLocale() }}'                  // Lingua dell'applicazione
            };
            
            // === VARIABILI DI LAVORO ===
            const routeName = window.LaravelApp.route;              // Nome route corrente
            const userRole = window.LaravelApp.user?.ruolo ?? 'guest'; // Ruolo utente o 'guest'
            
            // === DEBUG CONSOLE ===
            console.log('Route attuale:', routeName);
            console.log('Ruolo utente:', userRole);
            
            // ============================================================
            // MAPPA DINAMICA SCRIPT SPECIFICI
            // Associa ogni route a un file JavaScript specifico
            // ============================================================
            const scriptMap = {
                // === AMMINISTRAZIONE ===
                'admin.dashboard': 'admin/dashboard.js',
                'admin.assegnazioni.index': 'admin/assegnazioni-index.js',
                'admin.centri.index': 'admin/centri-index.js',
                'admin.centri.create': 'admin/centri-create.js',
                'admin.centri.edit': 'admin/centri-edit.js',
                'admin.centri.show': 'admin/centri-show.js',
                'admin.prodotti.index': 'admin/prodotti-index.js',
                'admin.prodotti.create': 'admin/prodotti-create.js',
                'admin.prodotti.edit': 'admin/prodotti-edit.js',
                'admin.prodotti.show': 'admin/prodotti-show.js',
                'admin.users.index': 'admin/users-index.js',
                'admin.users.create': 'admin/users-create.js',
                'admin.users.edit': 'admin/users-edit.js',
                'admin.users.show': 'admin/users-show.js',
                'admin.statistiche.index': 'admin/statistiche.js',
                'admin.manutenzione.index': 'admin/manutenzione.js',

                // === STAFF AZIENDALE ===
                'staff.dashboard': 'staff/dashboard.js',
                'staff.statistiche': 'staff/statistiche.js',
                
                // === TECNICI ===
                'tecnico.dashboard': 'tecnico/dashboard.js',
                'tecnico.statistiche.view': 'tecnico/statistiche.js',
                
                // === MALFUNZIONAMENTI ===
                // Nota: versioning per forzare ricaricamento cache
                'malfunzionamenti.index': "{{ asset('js/malfunzionamenti/index.js') . '?v=' . filemtime(public_path('js/malfunzionamenti/index.js')) }}",
                'staff.create.nuova.soluzione': 'malfunzionamenti/create.js',
                'staff.malfunzionamenti.edit': 'malfunzionamenti/edit.js',
                'malfunzionamenti.show': 'malfunzionamenti/show.js',
                'malfunzionamenti.ricerca': 'malfunzionamenti/ricerca.js',
                
                // === PRODOTTI PUBBLICI ===
                'prodotti.pubblico.index': 'prodotti/pubblico/index.js',
                'prodotti.pubblico.show': 'prodotti/pubblico/show.js',

                // === PRODOTTI COMPLETI (per tecnici) ===
                'prodotti.completo.index': 'prodotti/completo/index.js',
                'prodotti.completo.show': 'prodotti/completo/show.js',
                
                // === CENTRI ASSISTENZA ===
                'centri.index': 'centri/index.js',
                'centri.show': 'centri/show.js',
                
                // === AUTENTICAZIONE ===
                'tecnico.interventi': 'auth/storico-interventi.js',
                
                // === PAGINE STATICHE ===
                'azienda': 'pages/azienda.js',
                'contatti': 'pages/contatti.js',
                
                // === GESTIONE ERRORI ===
                '404': 'errors/404.js',
                '404.authenticated': 'errors/404-authenticated.js',
                '404.public': 'errors/404-public.js',
                
                // === HOMEPAGE ===
                'home': 'prodotti/pubblico/index.js'
            };
            
            // ============================================================
            // CARICAMENTO DINAMICO SCRIPT SPECIFICO
            // Carica automaticamente lo script JavaScript per la route corrente
            // ============================================================
            if (scriptMap[routeName]) {
                const scriptUrl = `{{ asset('js/') }}/${scriptMap[routeName]}`;
                console.log('Caricamento script:', scriptUrl);
                
                // === CREAZIONE ELEMENTO SCRIPT ===
                const script = document.createElement('script');
                script.src = scriptUrl;
                
                // === GESTIONE ERRORI CARICAMENTO ===
                script.onerror = function() {
                    console.warn('Script non trovato (normale se non necessario):', scriptUrl);
                };
                
                // === CONFERMA CARICAMENTO ===
                script.onload = function() {
                    console.log('Script caricato:', scriptUrl);
                };
                
                // === AGGIUNTA AL DOM ===
                document.head.appendChild(script);
            } else {
                console.log('Nessuno script specifico per questa route');
            }
        });
    </script>

    {{-- ============================================================
         SCRIPT JAVASCRIPT PRINCIPALE PERSONALIZZATO
         Funzioni globali e configurazioni per tutta l'applicazione
         ============================================================ --}}
    <script>
        // ============================================================
        // CONFIGURAZIONE GLOBALE AJAX
        // Setup automatico per tutte le richieste AJAX jQuery
        // ============================================================
        $.ajaxSetup({
            headers: {
                // Imposta automaticamente l'header CSRF per ogni richiesta AJAX
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============================================================
        // FUNZIONI UTILITY GLOBALI
        // Funzioni riutilizzabili disponibili in tutta l'applicazione
        // ============================================================
        
        /**
         * FUNZIONE: showSpinner
         * SCOPO: Mostra un indicatore di caricamento su un elemento
         * PARAMETRI: element - Selettore jQuery dell'elemento target
         * LINGUAGGIO: JavaScript con jQuery
         */
        function showSpinner(element) {
            const spinner = '<span class="spinner-custom me-2"></span>';  // HTML dello spinner
            $(element).prepend(spinner).prop('disabled', true);           // Aggiunge spinner e disabilita
        }
        
        /**
         * FUNZIONE: hideSpinner  
         * SCOPO: Rimuove l'indicatore di caricamento da un elemento
         * PARAMETRI: element - Selettore jQuery dell'elemento target
         * LINGUAGGIO: JavaScript con jQuery
         */
        function hideSpinner(element) {
            $(element).find('.spinner-custom').remove().prop('disabled', false); // Rimuove spinner e riabilita
        }
        
        /**
         * FUNZIONE: formatNumber
         * SCOPO: Formatta numeri secondo lo standard italiano (migliaia con punti)
         * PARAMETRI: num - Numero da formattare
         * RITORNA: Stringa formattata (es: 1.234,56)
         * LINGUAGGIO: JavaScript con API Intl
         */
        function formatNumber(num) {
            return new Intl.NumberFormat('it-IT').format(num);
        }
        
        /**
         * FUNZIONE: showToast
         * SCOPO: Mostra notificazioni toast Bootstrap temporanee
         * PARAMETRI: message - Testo del messaggio, type - Tipo di toast (success, danger, etc.)
         * LINGUAGGIO: JavaScript con Bootstrap Toast API
         */
        function showToast(message, type = 'info') {
            // Template HTML del toast
            const toast = `
                <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            // Crea container per toast se non esiste
            if (!$('#toast-container').length) {
                $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
            }
            
            // Aggiunge il toast al container
            const $toast = $(toast);
            $('#toast-container').append($toast);
            
            // Inizializza e mostra il toast Bootstrap
            const toastInstance = new bootstrap.Toast($toast[0]);
            toastInstance.show();
            
            // Rimuove il toast dal DOM dopo che si nasconde
            $toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        // ============================================================
        // INIZIALIZZAZIONE NAVBAR DINAMICA
        // JavaScript puro per funzionalità navbar avanzate
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            // === EVIDENZIAZIONE PAGINA CORRENTE ===
            // Aggiunge classe 'active' al link della pagina corrente
            const currentPath = window.location.pathname;           // Percorso URL corrente
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .dropdown-item');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    if (link.classList.contains('nav-link')) {
                        link.classList.add('active');                // Classe Bootstrap per link attivo
                    } else if (link.classList.contains('dropdown-item')) {
                        link.classList.add('active');
                        link.style.backgroundColor = 'rgba(37, 99, 235, 0.1)'; // Sfondo colorato
                    }
                }
            });

            // === ANIMAZIONI DROPDOWN HOVER ===
            // Apre/chiude dropdown al passaggio del mouse (solo desktop)
            const dropdowns = document.querySelectorAll('.nav-item.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (toggle && menu) {
                    // Evento mouseenter: mostra dropdown
                    toggle.addEventListener('mouseenter', function() {
                        if (window.innerWidth > 991) { // Solo su schermi grandi
                            dropdown.classList.add('show');
                            menu.classList.add('show');
                        }
                    });
                    
                    // Evento mouseleave: nasconde dropdown
                    dropdown.addEventListener('mouseleave', function() {
                        if (window.innerWidth > 991) { // Solo su schermi grandi
                            dropdown.classList.remove('show');
                            menu.classList.remove('show');
                        }
                    });
                }
            });

            // === COMPORTAMENTO RESPONSIVE NAVBAR ===
            // Chiude automaticamente la navbar mobile quando si clicca un link
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const allNavLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            allNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Se siamo su mobile e la navbar è aperta, la chiude
                    if (window.innerWidth <= 991 && navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                });
            });

            // === TOOLTIP PER BADGE LIVELLO ===
            // Aggiunge descrizioni informative ai badge livello utente
            const badgeLivello = document.querySelectorAll('.badge-livello');
            badgeLivello.forEach(badge => {
                // Estrae il numero del livello dalla classe CSS
                const livello = badge.classList.toString().match(/badge-livello-(\d)/)?.[1];
                if (livello) {
                    // Mappa livelli → descrizioni
                    const descriptions = {
                        '1': 'Utente pubblico - Accesso base al catalogo',
                        '2': 'Tecnico - Accesso a malfunzionamenti e soluzioni',
                        '3': 'Staff aziendale - Gestione soluzioni e statistiche', 
                        '4': 'Amministratore - Controllo completo del sistema'
                    };
                    
                    // Imposta tooltip e cursore help
                    badge.setAttribute('title', descriptions[livello]);
                    badge.style.cursor = 'help';
                }
            });

            console.log('Navbar dinamica inizializzata correttamente');
        });

        // ============================================================
        // FUNZIONE RICERCA DINAMICA CON DEBOUNCE
        // Implementa ricerca in tempo reale con ritardo per performance
        // ============================================================
        
        /**
         * FUNZIONE: setupSearchWithDebounce
         * SCOPO: Configura ricerca dinamica con ritardo per evitare troppe richieste
         * PARAMETRI: 
         *   - inputSelector: selettore jQuery del campo input
         *   - apiUrl: URL dell'API di ricerca
         *   - resultsCallback: funzione da chiamare con i risultati
         * LINGUAGGIO: JavaScript con jQuery e AJAX
         */
        function setupSearchWithDebounce(inputSelector, apiUrl, resultsCallback) {
            let searchTimeout; // Variabile per memorizzare il timeout
            
            $(inputSelector).on('input', function() {
                const query = $(this).val().trim(); // Ottiene e pulisce il testo
                
                clearTimeout(searchTimeout); // Cancella timeout precedente
                
                if (query.length >= 2) { // Ricerca solo con almeno 2 caratteri
                    // Imposta nuovo timeout di 300ms (debounce)
                    searchTimeout = setTimeout(() => {
                        // Esegue richiesta AJAX GET
                        $.get(apiUrl, { q: query })
                            .done(resultsCallback)  // Successo: chiama callback
                            .fail(() => showToast('Errore durante la ricerca', 'danger')); // Errore: mostra toast
                    }, 300);
                } else {
                    // Query troppo corta: restituisce risultati vuoti
                    resultsCallback({ data: [] });
                }
            });
        }

        // ============================================================
        // GESTIONE CONFERME DI ELIMINAZIONE
        // Aggiunge conferma JavaScript per azioni di cancellazione
        // ============================================================
        $(document).on('click', '[data-confirm-delete]', function(e) {
            e.preventDefault(); // Blocca azione predefinita
            
            // Messaggio personalizzato o predefinito
            const message = $(this).data('confirm-delete') || 'Sei sicuro di voler eliminare questo elemento?';
            const form = $(this).closest('form'); // Trova form parent
            
            // Mostra conferma nativa del browser
            if (confirm(message)) {
                form.submit(); // Se confermato, invia form
            }
        });

        // ============================================================
        // AUTO-DISMISS ALERTS
        // Nasconde automaticamente gli alert dopo 5 secondi
        // ============================================================
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow'); // Fade out graduale
        }, 5000);

        // ============================================================
        // INIZIALIZZAZIONE TOOLTIP E POPOVER BOOTSTRAP
        // Attiva componenti interattivi Bootstrap su tutti gli elementi
        // ============================================================
        $(document).ready(function() {
            // Inizializza tutti i tooltip
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
            
            // Inizializza tutti i popover
            $('[data-bs-toggle="popover"]').each(function() {
                new bootstrap.Popover(this);
            });
        });

        // ============================================================
        // SMOOTH SCROLLING per link ancore
        // Scorrimento fluido per link interni (#section)
        // ============================================================
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href')); // Elemento target
            if (target.length) {
                e.preventDefault(); // Blocca scorrimento predefinito
                
                // Animazione jQuery per scorrimento fluido
                $('html, body').animate({
                    scrollTop: target.offset().top - 100 // -100px per header fisso
                }, 500);
            }
        });

        // ============================================================
        // MESSAGGIO DI INIZIALIZZAZIONE
        // Conferma che il sistema è stato caricato correttamente
        // ============================================================
        console.log('TechSupport Pro - Sistema inizializzato');
    </script>
    
    {{-- 
        STACK PER SCRIPT PERSONALIZZATI
        @stack('scripts') permette alle view figlie di aggiungere JavaScript specifico
        Le view possono usare @push('scripts') per inserire codice JS personalizzato
    --}}
    @stack('scripts')

{{-- ============================================================
     CHIUSURA TAG HTML
     Fine del documento HTML
     ============================================================ --}}
</body>
</html>

{{-- 
=======================================================================
FINE FILE: resources/views/layouts/app.blade.php
=======================================================================

RIEPILOGO FUNZIONALITÀ IMPLEMENTATE:

1. STRUTTURA HTML5 RESPONSIVE
   - DOCTYPE e meta tag standard
   - Viewport responsive per mobile
   - Bootstrap 5.3 e Bootstrap Icons

2. SISTEMA CSRF Laravel
   - Token CSRF in meta tag e JavaScript globale
   - Configurazione automatica AJAX

3. NAVBAR DINAMICA
   - Menu condizionale basato su livello utente (1-4)
   - Dropdown hover per desktop
   - Comportamento responsive per mobile
   - Badge colorati per livelli accesso

4. SISTEMA MESSAGGI
   - Alert Bootstrap per feedback utente
   - Breadcrumb dinamico per navigazione
   - Toast notifications JavaScript

5. CARICAMENTO SCRIPT DINAMICO
   - Mappa route → file JavaScript specifici
   - Caricamento automatico script per pagina
   - Gestione errori caricamento

6. FUNZIONI UTILITY
   - Spinner di caricamento
   - Formattazione numeri italiani
   - Ricerca dinamica con debounce
   - Conferme eliminazione
   - Smooth scrolling

7. FOOTER INFORMATIVO
   - Link utili e documentazione
   - Copyright e informazioni progetto
   - Link condizionali per admin

Il template è completamente commentato per facilitare la comprensione
durante l'esame orale del progetto universitario.
=======================================================================
--}}