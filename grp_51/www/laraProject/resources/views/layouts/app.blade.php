<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    // Imposta CSRF token globalmente per AJAX
    window.Laravel = {
        csrfToken: "{{ csrf_token() }}"
    };


</script>
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'Assistenza Tecnica Online') - TechSupport Pro</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom CSS per il progetto -->
    <style>
        /* === STILI GLOBALI === */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #16a34a;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            line-height: 1.6;
        }

        /* === NAVBAR PERSONALIZZATA === */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            font-weight: bold;
        }

        /* === BADGE LIVELLI UTENTE === */
        .badge-livello {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .badge-livello-1 { background-color: var(--secondary-color); }
        .badge-livello-2 { background-color: var(--info-color); }
        .badge-livello-3 { background-color: var(--warning-color); }
        .badge-livello-4 { background-color: var(--danger-color); }

        /* === CARD PERSONALIZZATE === */
        .card-custom {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        /* === GRAVITÀ MALFUNZIONAMENTI === */
        .gravita-bassa { color: var(--success-color); }
        .gravita-media { color: var(--warning-color); }
        .gravita-alta { color: var(--danger-color); }
        .gravita-critica { 
            color: var(--danger-color); 
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* === FOOTER === */
        .footer-custom {
            background-color: var(--dark-color);
            color: white;
            margin-top: auto;
        }

        /* === UTILITÀ === */
        .min-vh-100 {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .search-highlight {
            background-color: #fef3c7;
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            
            .card-custom {
                margin-bottom: 1rem;
            }
        }

        /* === LOADING SPINNER === */
        .spinner-custom {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* === DROPDOWN ANIMATIONS === */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 0.5rem;
        }

        .dropdown-item:hover {
            background-color: rgba(var(--primary-color), 0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-vh-100">
    
    <!-- === NAVBAR DINAMICA === -->
    <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <!-- Logo e nome dell'applicazione -->
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-wrench-adjustable me-2"></i>
            TechSupport Pro
        </a>

        <!-- Pulsante hamburger per mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu di navigazione -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                
                {{-- === LINK PUBBLICI (Livello 1 - Sempre visibili) === --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                </li>
                
                {{-- CATALOGO PRODOTTI PUBBLICO (senza malfunzionamenti) --}}
                <li class="nav-item">
                    {{-- 
                        IMPORTANTE: Usa la route PUBBLICA definita in web.php
                        Route::get('/catalogo', [ProdottoController::class, 'indexPubblico'])->name('prodotti.pubblico.index');
                    --}}
                    <a class="nav-link" href="{{ route('prodotti.pubblico.index') }}">
                        <i class="bi bi-box me-1"></i>Catalogo Pubblico
                    </a>
                </li>
                
                {{-- CENTRI ASSISTENZA PUBBLICI - QUESTA È LA CORREZIONE PRINCIPALE --}}
                <li class="nav-item">
                    {{-- 
                        CORREZIONE: Usa esattamente la route pubblica definita in web.php:
                        Route::get('/centri-assistenza', [CentroAssistenzaController::class, 'index'])->name('centri.index');
                        
                        Questa route punta al metodo index() del CentroAssistenzaController
                        che mostra la vista PUBBLICA dei centri (senza funzionalità admin)
                    --}}
                    <a class="nav-link" href="{{ route('centri.index') }}">
                        <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                    </a>
                </li>

                {{-- === MENU DINAMICO PER UTENTI AUTENTICATI === --}}
                @auth
                    @php
                        $user = Auth::user();
                        $livello = $user->livello_accesso;
                    @endphp
                    
                    {{-- DASHBOARD PERSONALIZZATA PER LIVELLO --}}
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

                    {{-- MENU AMMINISTRATIVO PER TECNICI, STAFF E ADMIN - VERSIONE AGGIORNATA --}}
@if($livello >= 2)
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-tools me-1"></i>
            @if($livello == 4)
                Amministrazione
            @elseif($livello == 3)
                Gestione Staff
            @else
                {{-- TECNICO (livello 2) - NUOVO MENU GESTIONE --}}
                Gestione Tecnico
            @endif
        </a>
        
        <ul class="dropdown-menu">
            @if($livello == 4)
                {{-- ADMIN: Gestione completa sistema --}}
                <li><h6 class="dropdown-header">Gestione Utenti</h6></li>
                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people me-1"></i>Tutti gli Utenti
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                    <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Gestione Prodotti</h6></li>
                <li><a class="dropdown-item" href="{{ route('admin.prodotti.index') }}">
                    <i class="bi bi-box me-1"></i>Gestisci Prodotti
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.prodotti.create') }}">
                    <i class="bi bi-plus-square me-1"></i>Nuovo Prodotto
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Centri Assistenza</h6></li>
                <li><a class="dropdown-item" href="{{ route('admin.centri.index') }}">
                    <i class="bi bi-building me-1"></i>Gestisci Centri
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.centri.create') }}">
                    <i class="bi bi-plus-circle me-1"></i>Nuovo Centro
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Sistema</h6></li>
                <li><a class="dropdown-item" href="{{ route('admin.statistiche.index') }}">
                    <i class="bi bi-graph-up me-1"></i>Statistiche Sistema
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.manutenzione.index') }}">
                    <i class="bi bi-gear me-1"></i>Manutenzione
                </a></li>
                
            @elseif($livello == 3)
                {{-- STAFF: Gestione completa --}}
                <li><h6 class="dropdown-header">Catalogo e Prodotti</h6></li>
                <li><a class="dropdown-item" href="{{ route('prodotti.completo.index') }}">
                    <i class="bi bi-box-seam me-1"></i>Catalogo Completo
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Malfunzionamenti</h6></li>
                <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}">
                    <i class="bi bi-search me-1"></i>Ricerca Soluzioni
                </a></li>
                <li><a class="dropdown-item" href="{{ route('staff.create.nuova.soluzione') }}">
                    <i class="bi bi-plus-circle me-1"></i>Crea Soluzione
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Statistiche</h6></li>
                <li><a class="dropdown-item" href="{{ route('staff.statistiche') }}">
                    <i class="bi bi-graph-up me-1"></i>Mie Statistiche
                </a></li>
                
            @else
                {{-- TECNICO (livello 2) - NUOVO MENU COMPLETO --}}
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
                
                {{-- Prodotti con priorità critica --}}
                <li><a class="dropdown-item" href="{{ route('prodotti.completo.index') }}?filter=critici">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Priorità Critica
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
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
                <li><h6 class="dropdown-header">
                    <i class="bi bi-geo-alt me-1"></i>Assistenza
                </h6></li>
                
                {{-- Centro assistenza del tecnico (se assegnato) --}}
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
                <li><h6 class="dropdown-header">
                    <i class="bi bi-tools me-1"></i>Azioni Rapide
                </h6></li>
                
                {{-- Ricerca rapida prodotti --}}
                <li><a class="dropdown-item" href="{{ route('prodotti.completo.ricerca') }}?search=lav*">
                    <i class="bi bi-search me-2"></i>Cerca "lav*"
                </a></li>
                
                {{-- Malfunzionamenti più comuni --}}
                <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}?q=non+si+accende">
                    <i class="bi bi-lightning me-2"></i>Non si accende
                </a></li>
                
                <li><a class="dropdown-item" href="{{ route('malfunzionamenti.ricerca') }}?q=perdita">
                    <i class="bi bi-droplet me-2"></i>Perdite
                </a></li>
                
                <li><hr class="dropdown-divider"></li>
                
                
            @endif
        </ul>
    </li>
@endif
                @endauth
            </ul>

            {{-- === MENU UTENTE (Lato destro) === --}}
            <ul class="navbar-nav">
                @guest
                    {{-- UTENTE NON LOGGATO --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Accedi
                        </a>
                    </li>
                @else
                    {{-- UTENTE LOGGATO --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->nome }} {{ Auth::user()->cognome }}
                            {{-- Badge livello utente --}}
                            <span class="badge badge-livello badge-livello-{{ Auth::user()->livello_accesso }} ms-1">
                                @switch(Auth::user()->livello_accesso)
                                    @case(4) Admin @break
                                    @case(3) Staff @break  
                                    @case(2) Tecnico @break
                                    @default Utente
                                @endswitch
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            {{-- Link alla dashboard principale dell'utente --}}
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
                            
                            {{-- Link specifici per livello --}}
                            @if(Auth::user()->livello_accesso >= 3)
                                <li><hr class="dropdown-divider"></li>
                                @if(Auth::user()->livello_accesso == 3)
                                    <li><a class="dropdown-item" href="{{ route('staff.statistiche') }}">
                                        <i class="bi bi-graph-up me-1"></i>Mie Statistiche
                                    </a></li>
                                @elseif(Auth::user()->livello_accesso == 4)
                                    <li><a class="dropdown-item" href="{{ route('admin.statistiche.index') }}">
                                        <i class="bi bi-bar-chart me-1"></i>Statistiche Sistema
                                    </a></li>
                                @endif
                            @endif
                            
                            {{-- Informazioni profilo --}}
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <span class="dropdown-item-text">
                                    <small class="text-muted">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ Auth::user()->username }}
                                        @if(Auth::user()->centroAssistenza)
                                            <br><i class="bi bi-geo-alt me-1"></i>{{ Auth::user()->centroAssistenza->nome }}
                                        @endif
                                    </small>
                                </span>
                            </li>
                            
                            {{-- Logout --}}
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i>Esci
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

    <!-- === BREADCRUMB (se presente) === -->
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <div class="container mt-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    @endif

    <!-- === MESSAGGI DI FEEDBACK === -->
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="container mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif

    <!-- === CONTENUTO PRINCIPALE === -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- === FOOTER === -->
    <footer class="footer-custom py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>TechSupport Pro</h5>
                    <p class="mb-0">Sistema di assistenza tecnica online per elettrodomestici</p>
                    <small class="text-muted">Gruppo 51 - Tecnologie Web 2024/2025</small>
                </div>
                <div class="col-md-3">
                    <h6>Link Utili</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('azienda') }}" class="text-light text-decoration-none">Chi Siamo</a></li>
                        <li><a href="{{ route('centri.index') }}" class="text-light text-decoration-none">Centri Assistenza</a></li>
                        <li><a href="{{ route('contatti') }}" class="text-light text-decoration-none">Contatti</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Documentazione</h6>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ route('documentazione') }}" class="text-light text-decoration-none" target="_blank">
                                <i class="bi bi-file-pdf me-1"></i>Documentazione Progetto
                            </a>
                        </li>
                        @auth
                            @if(Auth::user()->livello_accesso >= 4)
                                <li><a href="{{ route('test.db') }}" class="text-light text-decoration-none">Test DB</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-12 text-center">
                    <small>&copy; {{ date('Y') }} TechSupport Pro. Università Politecnica delle Marche.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- === JAVASCRIPT === -->
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery 3.7 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    
    <!-- JavaScript Personalizzato del Progetto -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    
    <!-- Script personalizzati -->

    <script>
$(document).ready(function() {
    // Configurazione globale
    window.LaravelApp = {
        csrfToken: '{{ csrf_token() }}',
        baseUrl: '{{ url('/') }}',
        route: @json(request()->route()->getName() ?? ''),
        user: @json(auth()->user() ?? null),
        locale: '{{ app()->getLocale() }}'
    };
    
    const routeName = window.LaravelApp.route;
    const userRole = window.LaravelApp.user?.ruolo ?? 'guest';
    
    console.log('Route attuale:', routeName);
    console.log('Ruolo utente:', userRole);
    
    // Mappa route → file JavaScript specifici
    const scriptMap = {
        // === ADMIN ===
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

        // === STAFF ===
        'staff.dashboard': 'staff/dashboard.js',
        'staff.statistiche': 'staff/statistiche.js',
        
        // === TECNICO ===
        'tecnico.dashboard': 'tecnico/dashboard.js',
        'tecnico.statistiche.view': 'tecnico/statistiche.js',
        
        // === MALFUNZIONAMENTI ===
    'malfunzionamenti.index': "{{ asset('js/malfunzionamenti/index.js') . '?v=' . filemtime(public_path('js/malfunzionamenti/index.js')) }}",
        'staff.create.nuova.soluzione': 'malfunzionamenti/create.js',
        'staff.malfunzionamenti.edit': 'malfunzionamenti/edit.js',
        'malfunzionamenti.show': 'malfunzionamenti/show.js',
        'malfunzionamenti.ricerca': 'malfunzionamenti/ricerca.js',
        
        // === PRODOTTI PUBBLICI ===
        'prodotti.pubblico.index': 'prodotti/pubblico/index.js',
        'prodotti.pubblico.show': 'prodotti/pubblico/show.js',

        // === PRODOTTI COMPLETI (TECNICI) ===
        'prodotti.completo.index': 'prodotti/completo/index.js',
        'prodotti.completo.show': 'prodotti/completo/show.js',
        
        // === CENTRI ===
        'centri.index': 'centri/index.js',
        'centri.show': 'centri/show.js',
        
        // === AUTENTICAZIONE ===
        
        'tecnico.interventi': 'auth/storico-interventi.js',
        
        // === PAGINE ===
        'azienda': 'pages/azienda.js',
        'contatti': 'pages/contatti.js',
        
        // === ERRORI ===
        '404': 'errors/404.js',
        '404.authenticated': 'errors/404-authenticated.js',
        '404.public': 'errors/404-public.js',
        
        // === HOME ===
        'home': 'prodotti/pubblico/index.js'
    };
    
    // Carica lo script specifico per la route
    if (scriptMap[routeName]) {
        const scriptUrl = `{{ asset('js/') }}/${scriptMap[routeName]}`;
        console.log('Caricamento script:', scriptUrl);
        
        const script = document.createElement('script');
        script.src = scriptUrl;
        script.onerror = function() {
            console.warn('Script non trovato (normale se non necessario):', scriptUrl);
        };
        script.onload = function() {
            console.log('Script caricato:', scriptUrl);
        };
        document.head.appendChild(script);
    } else {
        console.log('Nessuno script specifico per questa route');
    }
});
</script>

    <script>
        // === CONFIGURAZIONE GLOBALE ===
        
        // Setup CSRF token per richieste AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // === FUNZIONI UTILITY ===
        
        // Mostra spinner di caricamento
        function showSpinner(element) {
            const spinner = '<span class="spinner-custom me-2"></span>';
            $(element).prepend(spinner).prop('disabled', true);
        }
        
        // Nasconde spinner di caricamento
        function hideSpinner(element) {
            $(element).find('.spinner-custom').remove().prop('disabled', false);
        }
        
        // Formatta numeri italiani
        function formatNumber(num) {
            return new Intl.NumberFormat('it-IT').format(num);
        }
        
        // Mostra toast notification
        function showToast(message, type = 'info') {
            const toast = `
                <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            // Aggiunge toast container se non esiste
            if (!$('#toast-container').length) {
                $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
            }
            
            const $toast = $(toast);
            $('#toast-container').append($toast);
            
            const toastInstance = new bootstrap.Toast($toast[0]);
            toastInstance.show();
            
            // Rimuove il toast dopo che si nasconde
            $toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        // === NAVBAR DINAMICA JAVASCRIPT ===
        document.addEventListener('DOMContentLoaded', function() {
            // === HIGHLIGHT PAGINA CORRENTE ===
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .dropdown-item');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    if (link.classList.contains('nav-link')) {
                        link.classList.add('active');
                    } else if (link.classList.contains('dropdown-item')) {
                        link.classList.add('active');
                        link.style.backgroundColor = 'rgba(37, 99, 235, 0.1)';
                    }
                }
            });

            // === ANIMAZIONI DROPDOWN ===
            const dropdowns = document.querySelectorAll('.nav-item.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (toggle && menu) {
                    toggle.addEventListener('mouseenter', function() {
                        if (window.innerWidth > 991) {
                            dropdown.classList.add('show');
                            menu.classList.add('show');
                        }
                    });
                    
                    dropdown.addEventListener('mouseleave', function() {
                        if (window.innerWidth > 991) {
                            dropdown.classList.remove('show');
                            menu.classList.remove('show');
                        }
                    });
                }
            });

            // === RESPONSIVE NAVBAR BEHAVIOR ===
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const allNavLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            allNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991 && navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                });
            });

            // === BADGE LIVELLO TOOLTIP ===
            const badgeLivello = document.querySelectorAll('.badge-livello');
            badgeLivello.forEach(badge => {
                const livello = badge.classList.toString().match(/badge-livello-(\d)/)?.[1];
                if (livello) {
                    const descriptions = {
                        '1': 'Utente pubblico - Accesso base al catalogo',
                        '2': 'Tecnico - Accesso a malfunzionamenti e soluzioni',
                        '3': 'Staff aziendale - Gestione soluzioni e statistiche',
                        '4': 'Amministratore - Controllo completo del sistema'
                    };
                    
                    badge.setAttribute('title', descriptions[livello]);
                    badge.style.cursor = 'help';
                }
            });

            console.log('Navbar dinamica inizializzata correttamente');
        });

        // === RICERCA DINAMICA ===
        function setupSearchWithDebounce(inputSelector, apiUrl, resultsCallback) {
            let searchTimeout;
            
            $(inputSelector).on('input', function() {
                const query = $(this).val().trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        $.get(apiUrl, { q: query })
                            .done(resultsCallback)
                            .fail(() => showToast('Errore durante la ricerca', 'danger'));
                    }, 300);
                } else {
                    resultsCallback({ data: [] });
                }
            });
        }

        // === CONFERME DI ELIMINAZIONE ===
        $(document).on('click', '[data-confirm-delete]', function(e) {
            e.preventDefault();
            
            const message = $(this).data('confirm-delete') || 'Sei sicuro di voler eliminare questo elemento?';
            const form = $(this).closest('form');
            
            if (confirm(message)) {
                form.submit();
            }
        });

        // === AUTO-DISMISS ALERTS ===
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);

        // === TOOLTIPS E POPOVERS ===
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
            
            $('[data-bs-toggle="popover"]').each(function() {
                new bootstrap.Popover(this);
            });
        });

        // === SMOOTH SCROLLING ===
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });

        console.log('TechSupport Pro - Sistema inizializzato');
    </script>
    
    <!-- JavaScript specifico per pagina -->
    @stack('scripts')
</body>
</html>