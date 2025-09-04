{{-- 
    File: resources/views/errors/404-public.blade.php
    Descrizione: Pagina 404 personalizzata per utenti non autenticati
--}}

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina non trovata - TechSupport Pro</title>
    
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- Custom Styles --}}
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: none;
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-outline-custom {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }
        
        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }
        
        .icon-large {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ffc107;
        }
        
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
            pointer-events: none;
        }
        
        @media (max-width: 768px) {
            .error-number {
                font-size: 5rem;
            }
            
            .error-card {
                margin: 10px;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="tech-pattern"></div>
    
    <div class="error-container">
        <div class="error-card">
            <div class="card-body p-5 text-center">
                
                {{-- Icona di errore --}}
                <div class="icon-large">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                
                {{-- Numero errore grande --}}
                <div class="error-number mb-3">404</div>
                
                {{-- Messaggio principale --}}
                <h2 class="h3 mb-3 text-dark">Pagina non trovata</h2>
                <p class="lead text-muted mb-4">
                    Oops! La pagina che stai cercando non esiste o è stata spostata.
                </p>
                
                {{-- Messaggio informativo --}}
                <div class="alert alert-info border-0" style="border-radius: 15px;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>TechSupport Pro</strong> - Sistema di assistenza tecnica per prodotti aziendali
                </div>
                
                {{-- Pulsanti di navigazione --}}
                <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                    <a href="{{ route('home') }}" class="btn btn-custom btn-primary-custom btn-lg">
                        <i class="bi bi-house-fill me-2"></i>
                        Vai alla Homepage
                    </a>
                    <button onclick="history.back()" class="btn btn-custom btn-outline-custom btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>
                        Torna Indietro
                    </button>
                </div>
                
                {{-- Sezione link utili --}}
                <div class="mt-5">
                    <h5 class="text-muted mb-3">Link utili</h5>
                    <div class="row">
                        @if(isset($suggested_routes))
                            @foreach($suggested_routes as $name => $url)
                                <div class="col-md-6 mb-2">
                                    <a href="{{ $url }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-{{ $loop->first ? 'house' : ($name === 'Login' ? 'box-arrow-in-right' : 'box') }} me-1"></i>
                                        {{ $name }}
                                    </a>
                                </div>
                            @endforeach
                        @else
                            {{-- Link di default se non forniti --}}
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-house me-1"></i>
                                    Homepage
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('prodotti.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-box me-1"></i>
                                    Prodotti
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-building me-1"></i>
                                    Centri Assistenza
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Accedi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Footer informativo --}}
                <div class="mt-5 pt-4 border-top">
                    <p class="small text-muted mb-0">
                        <i class="bi bi-question-circle me-1"></i>
                        Hai bisogno di aiuto? 
                        <a href="{{ route('contatti') }}" class="text-decoration-none">Contattaci</a>
                        o consulta la 
                        <a href="{{ route('documentazione') }}" class="text-decoration-none">documentazione</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
   
    
</body>
</html>