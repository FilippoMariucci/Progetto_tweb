<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .nav-link:hover {
            color: white !important;
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
    </style>
    
    @stack('styles')
</head>
<body class="min-vh-100">
    
    <!-- === NAVBAR === -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-tools me-2"></i>
                TechSupport Pro
            </a>

            <!-- Toggle button per mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu di navigazione -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- === LINK PUBBLICI (Livello 1) === -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('prodotti.index') }}">
                            <i class="bi bi-box me-1"></i>Catalogo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('centri.index') }}">
                            <i class="bi bi-geo-alt me-1"></i>Centri Assistenza
                        </a>
                    </li>

                    @auth
                        <!-- === LINK PER TECNICI (Livello 2+) === -->
                        @can('viewMalfunzionamenti')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Malfunzionamenti
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('prodotti.index') }}?view=tech">Cerca per Prodotto</a></li>
                                    @can('manageMalfunzionamenti')
                                        <li><a class="dropdown-item" href="{{ route('staff.malfunzionamenti.dashboard') }}">Dashboard Staff</a></li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan

                        <!-- === LINK PER ADMIN (Livello 4) === -->
                        @can('manageUsers')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-1"></i>Amministrazione
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Gestione Utenti</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.prodotti.index') }}">Gestione Prodotti</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.centri.index') }}">Centri Assistenza</a></li>
                                </ul>
                            </li>
                        @endcan
                    @endauth
                </ul>

                <!-- === MENU UTENTE === -->
                <ul class="navbar-nav">
                    @guest
                        <!-- Utente non autenticato -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Accedi
                            </a>
                        </li>
                    @else
                        <!-- Utente autenticato -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->nome_completo }}
                                <span class="badge badge-livello badge-livello-{{ Auth::user()->livello_accesso }} ms-1">
                                    {{ Auth::user()->livello_descrizione }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Form nascosto per logout -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
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
                        @can('accessDebug')
                            <li><a href="{{ route('test.db') }}" class="text-light text-decoration-none">Test DB</a></li>
                        @endcan
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
    
    <!-- JavaScript Personalizzato del Progetto (nella cartella /public/js/ come richiesto) -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    
    <!-- JavaScript specifico per pagina -->
    @stack('scripts')
</body>
</html>
    
    
    <!-- Script personalizzati -->
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

        // === RICERCA DINAMICA ===
        
        // Gestisce la ricerca con debounce
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
                    }, 300); // Debounce di 300ms
                } else {
                    resultsCallback({ data: [] }); // Pulisce i risultati
                }
            });
        }

        // === CONFERME DI ELIMINAZIONE ===
        
        // Setup conferme per azioni di eliminazione
        $(document).on('click', '[data-confirm-delete]', function(e) {
            e.preventDefault();
            
            const message = $(this).data('confirm-delete') || 'Sei sicuro di voler eliminare questo elemento?';
            const form = $(this).closest('form');
            
            if (confirm(message)) {
                form.submit();
            }
        });

        // === AUTO-DISMISS ALERTS ===
        
        // Auto-nasconde gli alert dopo 5 secondi
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);

        // === TOOLTIPS E POPOVERS ===
        
        // Inizializza tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
            
            $('[data-bs-toggle="popover"]').each(function() {
                new bootstrap.Popover(this);
            });
        });

        // === SMOOTH SCROLLING ===
        
        // Smooth scroll per anchor links
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
    
    @stack('scripts')
</body>
</html>