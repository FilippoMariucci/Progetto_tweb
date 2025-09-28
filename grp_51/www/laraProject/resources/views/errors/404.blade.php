{{-- 
    ===================================================================
    PAGINA 404 FALLBACK SEMPLICE - CON COMMENTI DETTAGLIATI
    ===================================================================
    Sistema Assistenza Tecnica - Gruppo 51
    File: resources/views/errors/404.blade.php
    
    DESCRIZIONE:
    Vista di fallback per errore 404 (Pagina non trovata) completamente standalone.
    Versione minimalista che funziona anche se Bootstrap/CDN non sono disponibili.
    Progettata per essere il fallback finale quando altre pagine 404 falliscono.
    
    LINGUAGGIO: HTML5 + CSS3 Vanilla + Blade PHP
    
    FUNZIONALITÀ PRINCIPALI:
    - Pagina 404 completamente autonoma (no dipendenze esterne)
    - CSS inline per funzionamento garantito
    - Design minimalista ma moderno
    - Layout responsive con CSS Grid/Flexbox
    - Emoji integrate per ridurre dipendenze da icon fonts
    - Links essenziali hardcoded per aree pubbliche
    - Fallback JavaScript vanilla
    ===================================================================
--}}

{{-- 
    DICHIARAZIONE DOCTYPE HTML5
    HTML5: Doctype semplificato per compatibilità universale
--}}
<!DOCTYPE html>
<html lang="it">
<head>
    {{-- 
        META TAGS ESSENZIALI
        HTML5: Minimum viable meta tags per funzionamento base
    --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- 
        TITOLO SEMPLICE E DIRETTO
        SEO: Titolo breve e descrittivo senza branding complesso
    --}}
    <title>404 - Pagina non trovata</title>
    {{-- 
        CSS INLINE COMPLETO
        CSS3: Tutti gli stili inline per garantire rendering anche con CDN falliti
        Approccio: Self-contained per maximum reliability
    --}}
    <style>
        /*
            RESET CSS UNIVERSALE
            CSS: Reset minimale ma completo per browser consistency
        */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* Include padding/border in width calculations */
        }
        
        /*
            BODY CON FLEXBOX CENTERING
            CSS3: Flexbox per centraggio perfetto senza dipendenze
            Background: Stesso gradient delle altre pagine per consistency
        */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh; /* Occupa sempre almeno l'altezza viewport */
            display: flex;
            align-items: center; /* Centraggio verticale */
            justify-content: center; /* Centraggio orizzontale */
        }
        
        /*
            CONTAINER PRINCIPALE MINIMALISTA
            CSS3: Design pulito con shadow e border-radius moderni
            Responsive: max-width e margin per adattabilità
        */
        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); /* Ombra soft per depth */
            max-width: 500px;
            margin: 20px; /* Margine per mobile */
        }
        
        /*
            CODICE ERRORE 404 PROMINENTE
            CSS: Typography large per immediate recognition
            Color: Coordinato con tema generale
        */
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #667eea; /* Primary color del sistema */
            margin-bottom: 1rem;
        }
        
        /*
            HEADING PRINCIPALE
            CSS: Dimensioni moderate per gerarchia visiva
        */
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        /*
            PARAGRAFO DESCRITTIVO
            CSS: Styling per readability ottimale
        */
        p {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.5; /* Line-height per leggibilità */
        }
        
        /*
            PULSANTI BASE
            CSS3: Design system semplice ma efficace
            Transition: Smooth animations senza dipendenze JS
        */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease; /* Transizione smooth per hover */
            margin: 0 10px;
        }
        
        /*
            HOVER EFFECT PULSANTI
            CSS3: Transform e box-shadow per feedback visivo
        */
        .btn:hover {
            background: #764ba2; /* Secondary color */
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        /*
            PULSANTE SECONDARIO
            CSS: Variant styling per gerarchia visiva
        */
        .btn-secondary {
            background: #6c757d; /* Gray Bootstrap-like */
        }
        
        .btn-secondary:hover {
            background: #545b62; /* Darker gray on hover */
        }
        
        /*
            SEZIONE LINK AGGIUNTIVI
            CSS: Separazione visiva con border-top
        */
        .links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee; /* Subtle separator */
        }
        
        /*
            STYLING LINK TESTUALI
            CSS: Minimal styling per link di supporto
        */
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 0.9rem;
        }
        
        .links a:hover {
            text-decoration: underline; /* Simple hover feedback */
        }
        
        /* === RESPONSIVE DESIGN === 
           CSS3: Media query per mobile optimization
        */
        @media (max-width: 600px) {
            /*
                CONTAINER MOBILE
                CSS: Riduzione padding e margin per small screens
            */
            .container {
                padding: 2rem;
                margin: 10px;
            }
            
            /*
                ERROR CODE PIÙ PICCOLO
                CSS: Riduce dimensione per mobile readability
            */
            .error-code {
                font-size: 3rem;
            }
            
            /*
                PULSANTI STACK VERTICALE
                CSS: Block display per touch-friendly mobile layout
            */
            .btn {
                display: block;
                margin: 10px 0; /* Margin verticale invece che orizzontale */
            }
        }
    </style>
</head>
<body>
    {{-- 
        CONTAINER PRINCIPALE CON CONTENUTO
        HTML: Struttura semplice e lineare per maximum compatibility
    --}}
    <div class="container">
        {{-- 
            CODICE ERRORE PROMINENTE
            HTML: Plain text, no icon fonts per reliability
        --}}
        <div class="error-code">404</div>
        
        {{-- 
            HEADING E DESCRIZIONE
            HTML: Semantic markup per accessibility
        --}}
        <h1>Pagina non trovata</h1>
        <p>La pagina che stai cercando non esiste o è stata spostata.</p>
        
        {{-- 
            PULSANTI NAVIGAZIONE PRINCIPALI
            HTML: Links con emoji integrate per visual appeal senza dipendenze
        --}}
        <div>
            {{-- 
                LINK HOMEPAGE CON EMOJI
                Laravel: route('home') per URL dinamico homepage
                Emoji: 🏠 integrata per ridurre dipendenza da icon fonts
            --}}
            <a href="{{ route('home') }}" class="btn">🏠 Homepage</a>
            {{-- 
                PULSANTE TORNA INDIETRO
                JavaScript: history.back() vanilla JS (no jQuery/framework)
                href="javascript:" per compatibility se JS disabilitato
            --}}
            <a href="javascript:history.back()" class="btn btn-secondary">← Indietro</a>
        </div>
        
        {{-- 
            SEZIONE LINK AGGIUNTIVI
            HTML: Links essenziali per aree pubbliche principali
        --}}
        <div class="links">
            {{-- 
                LINK CATALOGO PRODOTTI PUBBLICO
                Laravel: route('prodotti.pubblico.index') per accesso senza login
            --}}
            <a href="{{ route('prodotti.pubblico.index') }}">Prodotti</a>
            {{-- 
                LINK CENTRI ASSISTENZA
                Laravel: route('centri.index') per localizzazione supporto
            --}}
            <a href="{{ route('centri.index') }}">Centri Assistenza</a>
            {{-- 
                LINK CONTATTI
                Laravel: route('contatti') per pagina supporto
            --}}
            <a href="{{ route('contatti') }}">Contatti</a>
            {{-- 
                LINK LOGIN
                Laravel: route('login') per accesso area riservata
            --}}
            <a href="{{ route('login') }}">Accedi</a>
        </div>
        
        {{-- 
            FOOTER CON BRANDING MINIMALE
            HTML: Inline styles per final branding touch
            Design: Subtle e non invasivo
        --}}
        <div style="margin-top: 2rem; font-size: 0.8rem; color: #999;">
            TechSupport Pro - Sistema Assistenza Tecnica
        </div>
    </div>
    
    {{-- 
        AREA RISERVATA PER SCRIPT AGGIUNTIVI
        JavaScript: Spazio per future implementazioni se necessario
        Attualmente vuoto per mantenere leggerezza pagina fallback
    --}}
    
</body>
</html>