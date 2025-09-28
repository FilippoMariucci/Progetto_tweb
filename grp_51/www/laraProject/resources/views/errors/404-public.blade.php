{{-- 
    ===================================================================
    PAGINA 404 PUBBLICA - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/errors/404-public.blade.php
    
    DESCRIZIONE:
    Vista personalizzata per errore 404 (Pagina non trovata) specificamente
    progettata per utenti non autenticati (pubblico generale).
    A differenza della versione authenticated, questa è standalone e non estende layout.
    
    LINGUAGGIO: Blade PHP + HTML5 + CSS3 + JavaScript
    
    FUNZIONALITÀ PRINCIPALI:
    - Pagina 404 standalone per utenti non loggati
    - Design moderno con gradient background e glassmorphism
    - Layout completamente responsive
    - Links di navigazione per aree pubbliche
    - Integrazione Bootstrap CDN
    - Micro-animazioni e effetti visivi avanzati
    - Branding aziendale "TechSupport Pro"
    ===================================================================
--}}

{{-- 
    DICHIARAZIONE DOCTYPE HTML5
    HTML5: Dichiarazione doctype moderna per compatibilità browser
--}}
<!DOCTYPE html>
<html lang="it">
<head>
    {{-- 
        META TAGS FONDAMENTALI
        HTML5: Character encoding UTF-8 per supporto internazionale
        Viewport meta per responsive design su mobile
    --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- 
        TITOLO PAGINA CON BRANDING
        SEO: Titolo descrittivo che include brand aziendale
    --}}
    <title>Pagina non trovata - TechSupport Pro</title>
    
    {{-- 
        BOOTSTRAP CSS DA CDN
        Bootstrap 5.3.0: Framework CSS per layout e componenti responsive
        CDN: jsDelivr per performance globale ottimale
    --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- 
        BOOTSTRAP ICONS DA CDN
        Bootstrap Icons 1.10.0: Libreria icone ufficiale Bootstrap
        CDN: jsDelivr per caricamento veloce e caching
    --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- 
        STILI PERSONALIZZATI INLINE
        CSS3: Definizioni custom per design unico della pagina errore
    --}}
    <style>
        /*
            BODY CON GRADIENT BACKGROUND
            CSS3: Linear gradient per sfondo moderno e accattivante
            Flexbox: min-height 100vh per occupare sempre tutto lo schermo
        */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /*
            CONTAINER PRINCIPALE ERRORE
            CSS3: Flexbox per centraggio perfetto verticale e orizzontale
            Layout: Occupa tutta l'altezza viewport per centraggio ottimale
        */
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /*
            CARD ERRORE CON GLASSMORPHISM
            CSS3: Effetto vetro smerigliato con backdrop-filter
            Design moderno: Trasparenza, blur e ombra per depth
        */
        .error-card {
            background: rgba(255, 255, 255, 0.95); /* Bianco semi-trasparente */
            backdrop-filter: blur(10px); /* Effetto blur glassmorphism */
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); /* Ombra profonda */
            border: none;
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        /*
            NUMERO ERRORE 404 CON GRADIENT TEXT
            CSS3: Gradient come testo usando background-clip
            Typography: Font size grande per impatto visivo
        */
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text; /* Webkit prefix per compatibilità */
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        /*
            PULSANTI PERSONALIZZATI BASE
            CSS3: Design system coerente per tutti i pulsanti
            UX: Border-radius pronunciato e transizioni smooth
        */
        .btn-custom {
            border-radius: 25px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }
        
        /*
            PULSANTE PRIMARY CON GRADIENT
            CSS3: Background gradient coordinato con theme generale
        */
        .btn-primary-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
        
        /*
            HOVER EFFECT PULSANTE PRIMARY
            CSS3: Transform e box-shadow per feedback tattile
        */
        .btn-primary-custom:hover {
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white; /* Mantiene colore su hover */
        }
        
        /*
            PULSANTE OUTLINE PERSONALIZZATO
            CSS3: Bordo colorato con background trasparente
        */
        .btn-outline-custom {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }
        
        /*
            HOVER EFFECT PULSANTE OUTLINE
            CSS3: Fill animation con background e colore text
        */
        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }
        
        /*
            ICONA GRANDE PRINCIPALE
            CSS3: Dimensione custom per icona di avviso
        */
        .icon-large {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ffc107; /* Bootstrap warning color */
        }
        
        /*
            PATTERN TECNICO DECORATIVO
            CSS3: Background pattern con radial-gradients
            Design: Pattern sottile per texture senza distrarre
        */
        .tech-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: 
                radial-gradient(circle at 25% 25%, #ffffff 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, #ffffff 2px, transparent 2px);
            background-size: 50px 50px;
            pointer-events: none; /* Non interferisce con interazioni */
        }
        
        /* === RESPONSIVE DESIGN === 
           CSS3: Media queries per ottimizzazione mobile
        */
        @media (max-width: 768px) {
            /*
                RIDUZIONE NUMERO ERRORE SU MOBILE
                CSS3: Adatta dimensioni per schermi piccoli
            */
            .error-number {
                font-size: 5rem;
            }
            
            /*
                OTTIMIZZAZIONE CARD SU MOBILE
                CSS3: Riduce margini e border-radius per mobile
            */
            .error-card {
                margin: 10px;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    {{-- 
        PATTERN DECORATIVO DI BACKGROUND
        HTML: Div assoluto per pattern tecnico sottile
    --}}
    <div class="tech-pattern"></div>
    
    {{-- 
        CONTAINER PRINCIPALE CENTRATO
        HTML: Flexbox container per centraggio perfetto
    --}}
    <div class="error-container">
        <div class="error-card">
            <div class="card-body p-5 text-center">
                
                {{-- 
                    ICONA DI ERRORE PRINCIPALE
                    Bootstrap Icons: Triangolo esclamativo riempito per attenzione
                --}}
                <div class="icon-large">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                
                {{-- 
                    NUMERO ERRORE 404 PROMINENTE
                    CSS: Classe error-number con gradient text effect
                --}}
                <div class="error-number mb-3">404</div>
                
                {{-- 
                    MESSAGGI DESCRITTIVI
                    HTML: Gerarchia h2/p per SEO e accessibilità
                --}}
                <h2 class="h3 mb-3 text-dark">Pagina non trovata</h2>
                <p class="lead text-muted mb-4">
                    Oops! La pagina che stai cercando non esiste o è stata spostata.
                </p>
                
                {{-- 
                    ALERT INFORMATIVO CON BRANDING
                    Bootstrap: alert-info per messaggio non critico
                    Branding: Introduce nome sistema "TechSupport Pro"
                --}}
                <div class="alert alert-info border-0" style="border-radius: 15px;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>TechSupport Pro</strong> - Sistema di assistenza tecnica per prodotti aziendali
                </div>
                
                {{-- 
                    PULSANTI NAVIGAZIONE PRINCIPALI
                    Bootstrap: d-grid per layout responsive dei pulsanti
                --}}
                <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                    {{-- 
                        PULSANTE HOMEPAGE PRINCIPALE
                        Laravel: route('home') per URL homepage dinamico
                        CSS: btn-primary-custom con gradient styling
                    --}}
                    <a href="{{ route('home') }}" class="btn btn-custom btn-primary-custom btn-lg">
                        <i class="bi bi-house-fill me-2"></i>
                        Vai alla Homepage
                    </a>
                    {{-- 
                        PULSANTE TORNA INDIETRO
                        JavaScript: history.back() per navigazione browser nativa
                        CSS: btn-outline-custom per variazione visiva
                    --}}
                    <button onclick="history.back()" class="btn btn-custom btn-outline-custom btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>
                        Torna Indietro
                    </button>
                </div>
                
                {{-- 
                    SEZIONE LINK UTILI
                    HTML: Griglia di link per aree pubbliche principali
                --}}
                <div class="mt-5">
                    <h5 class="text-muted mb-3">Link utili</h5>
                    <div class="row">
                        {{-- 
                            ROUTE SUGGERITE DINAMICHE (OPZIONALI)
                            Laravel: Controllo esistenza $suggested_routes dal controller
                            Permette customizzazione link basata su contesto
                        --}}
                        @if(isset($suggested_routes))
                            {{-- 
                                ITERAZIONE ROUTE SUGGERITE
                                Blade PHP: @foreach con $loop per controllo posizione
                                Bootstrap Icons: Icone dinamiche basate su nome/posizione
                            --}}
                            @foreach($suggested_routes as $name => $url)
                                <div class="col-md-6 mb-2">
                                    <a href="{{ $url }}" class="btn btn-outline-secondary btn-sm w-100">
                                        {{-- 
                                            ICONE DINAMICHE PER TIPO LINK
                                            Laravel: $loop->first per primo elemento
                                            PHP: Confronto string per tipo specifico
                                        --}}
                                        <i class="bi bi-{{ $loop->first ? 'house' : ($name === 'Login' ? 'box-arrow-in-right' : 'box') }} me-1"></i>
                                        {{ $name }}
                                    </a>
                                </div>
                            @endforeach
                        @else
                            {{-- 
                                LINK DEFAULT SE NON FORNITI
                                Laravel: Route hardcoded per aree pubbliche standard
                                UX: Fallback per assicurare sempre navigazione utile
                            --}}
                            
                            {{-- LINK HOMEPAGE --}}
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-house me-1"></i>
                                    Homepage
                                </a>
                            </div>
                            {{-- 
                                LINK CATALOGO PRODOTTI PUBBLICO
                                Laravel: route('prodotti.index') per catalogo accessibile a tutti
                            --}}
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('prodotti.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-box me-1"></i>
                                    Prodotti
                                </a>
                            </div>
                            {{-- 
                                LINK CENTRI ASSISTENZA
                                Laravel: route('centri.index') per localizzazione supporto
                            --}}
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-building me-1"></i>
                                    Centri Assistenza
                                </a>
                            </div>
                            {{-- 
                                LINK LOGIN PER ACCESSO
                                Laravel: route('login') per autenticazione utenti
                            --}}
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- 
                    FOOTER INFORMATIVO
                    HTML: Sezione supporto con link contatti e documentazione
                --}}
                <div class="mt-5 pt-4 border-top">
                    <p class="small text-muted mb-0">
                        <i class="bi bi-question-circle me-1"></i>
                        Hai bisogno di aiuto? 
                        {{-- 
                            LINK CONTATTI
                            Laravel: route('contatti') per pagina supporto
                        --}}
                        <a href="{{ route('contatti') }}" class="text-decoration-none">Contattaci</a>
                        o consulta la 
                        {{-- 
                            LINK DOCUMENTAZIONE
                            Laravel: route('documentazione') per guide utente
                        --}}
                        <a href="{{ route('documentazione') }}" class="text-decoration-none">documentazione</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 
        BOOTSTRAP JAVASCRIPT DA CDN
        Bootstrap 5.3.0: Include Popper.js per componenti interattivi
        CDN: Bundle completo per tutte le funzionalità Bootstrap
    --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- 
        SPAZIO PER SCRIPT AGGIUNTIVI
        JavaScript: Area riservata per future implementazioni
        Possibili aggiunte: Analytics, animazioni, interazioni avanzate
    --}}
    
</body>
</html>