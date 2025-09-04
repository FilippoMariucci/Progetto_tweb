{{-- 
    File: resources/views/errors/404.blade.php
    Descrizione: Pagina 404 di fallback semplice
--}}

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Pagina non trovata</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 20px;
        }
        
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        p {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 0.9rem;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 2rem;
                margin: 10px;
            }
            
            .error-code {
                font-size: 3rem;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1>Pagina non trovata</h1>
        <p>La pagina che stai cercando non esiste o è stata spostata.</p>
        
        <div>
            <a href="{{ route('home') }}" class="btn">🏠 Homepage</a>
            <a href="javascript:history.back()" class="btn btn-secondary">← Indietro</a>
        </div>
        
        <div class="links">
            <a href="{{ route('prodotti.pubblico.index') }}">Prodotti</a>
            <a href="{{ route('centri.index') }}">Centri Assistenza</a>
            <a href="{{ route('contatti') }}">Contatti</a>
            <a href="{{ route('login') }}">Accedi</a>
        </div>
        
        <div style="margin-top: 2rem; font-size: 0.8rem; color: #999;">
            TechSupport Pro - Sistema Assistenza Tecnica
        </div>
    </div>
    
  
</body>
</html>