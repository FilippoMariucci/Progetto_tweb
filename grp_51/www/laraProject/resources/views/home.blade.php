@extends('layouts.app')

@section('title', 'Home - Sistema Assistenza Tecnica')

@section('content')
<div class="container-fluid">
    
    <!-- === HERO SECTION === -->
    <section class="hero-section py-5 mb-5" style="background: linear-gradient(135deg, #2563eb, #0891b2); color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Assistenza Tecnica
                        <span class="text-warning">Online</span>
                    </h1>
                    <p class="lead mb-4">
                        Sistema completo per la gestione dell'assistenza tecnica sui nostri elettrodomestici. 
                        Accedi a soluzioni rapide per i malfunzionamenti più comuni e trova il centro assistenza più vicino.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('prodotti.index') }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-box me-2"></i>Esplora Catalogo
                        </a>
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-geo-alt me-2"></i>Trova Centro Assistenza
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="bi bi-tools display-1 text-warning mb-3"></i>
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="h1 fw-bold">{{ $stats['prodotti_totali'] ?? '150+' }}</h3>
                            <p class="mb-0">Prodotti</p>
                        </div>
                        <div class="col-4">
                            <h3 class="h1 fw-bold">{{ $stats['centri_totali'] ?? '25+' }}</h3>
                            <p class="mb-0">Centri</p>
                        </div>
                        <div class="col-4">
                            <h3 class="h1 fw-bold">{{ $stats['soluzioni_totali'] ?? '500+' }}</h3>
                            <p class="mb-0">Soluzioni</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        
        <!-- === RICERCA RAPIDA === -->
        <section class="mb-5">
            <div class="card card-custom">
                <div class="card-body p-4">
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida Prodotti
                    </h2>
                    <form action="{{ route('prodotti.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   name="search" 
                                   placeholder="Cerca prodotto (es: lavatrice, lav*)"
                                   value="{{ request('search') }}"
                                   id="search-input">
                            <div class="form-text">
                                Usa * alla fine per ricerche parziali (es: "lav*" per lavatrici, lavastoviglie, ecc.)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="categoria" class="form-select form-select-lg">
                                <option value="">Tutte le categorie</option>
                                @if(isset($categorie_stats) && count($categorie_stats) > 0)
                                    @foreach($categorie_stats as $key => $info)
                                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                            {{ $info['label'] ?? ucfirst($key) }} ({{ $info['count'] ?? 0 }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-1"></i>Cerca
                            </button>
                        </div>
                    </form>
                    
                    <!-- Risultati ricerca AJAX -->
                    <div id="search-results" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </section>

        <!-- === INFORMAZIONI AZIENDA === -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-custom h-100">
                        <div class="card-body">
                            <h2 class="h3 mb-4">
                                <i class="bi bi-building text-primary me-2"></i>
                                La Nostra Azienda
                            </h2>
                            <p class="lead">
                                <strong>TechSupport Pro</strong> è leader nel settore degli elettrodomestici da oltre {{ $stats['anni_esperienza'] ?? 30 }} anni, 
                                con una rete capillare di centri assistenza su tutto il territorio nazionale.
                            </p>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5><i class="bi bi-geo-alt text-primary me-2"></i>Sede Principale</h5>
                                    <p class="mb-1">Via dell'Industria, 123</p>
                                    <p class="mb-1">60121 Ancona (AN)</p>
                                    <p class="mb-3">Italia</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="bi bi-telephone text-primary me-2"></i>Contatti</h5>
                                    <p class="mb-1">
                                        <strong>Tel:</strong> +39 071 123 4567
                                    </p>
                                    <p class="mb-1">
                                        <strong>Email:</strong> info@techsupportpro.it
                                    </p>
                                    <p class="mb-3">
                                        <strong>Assistenza:</strong> assistenza@techsupportpro.it
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('azienda') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Scopri di più
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom h-100">
                        <div class="card-body text-center">
                            <h3 class="h4 mb-4">
                                <i class="bi bi-award text-warning me-2"></i>
                                Certificazioni
                            </h3>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-shield-check display-6 text-success"></i>
                                        <p class="mt-2 mb-0 small fw-bold">ISO 9001</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-leaf display-6 text-success"></i>
                                        <p class="mt-2 mb-0 small fw-bold">Eco-Friendly</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-star-fill display-6 text-warning"></i>
                                        <p class="mt-2 mb-0 small fw-bold">5 Stelle</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-headset display-6 text-info"></i>
                                        <p class="mt-2 mb-0 small fw-bold">24/7 Support</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === CATEGORIE PRODOTTI === -->
        @if(isset($categorie_stats) && count($categorie_stats) > 0)
        <section class="mb-5">
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-grid text-primary me-2"></i>
                Categorie Prodotti
            </h2>
            <div class="row g-4">
                @foreach($categorie_stats as $key => $info)
                    @php
                        $icons = [
                            'lavatrice' => 'bi-water',
                            'lavastoviglie' => 'bi-droplet',
                            'forno' => 'bi-fire',
                            'frigorifero' => 'bi-snow',
                            'asciugatrice' => 'bi-wind',
                            'condizionatore' => 'bi-thermometer',
                            'microonde' => 'bi-lightning',
                            'aspirapolvere' => 'bi-fan',
                            'ferro_stiro' => 'bi-iron',
                            'piccoli_elettrodomestici' => 'bi-gear'
                        ];
                        $icon = $icons[$key] ?? 'bi-gear';
                        $label = $info['label'] ?? ucfirst($key);
                        $count = $info['count'] ?? 0;
                    @endphp
                    @if($count > 0)
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('prodotti.categoria', $key) }}" class="text-decoration-none">
                                <div class="card card-custom h-100 text-center category-card">
                                    <div class="card-body">
                                        <i class="bi {{ $icon }} display-4 text-primary mb-3"></i>
                                        <h5 class="card-title">{{ $label }}</h5>
                                        <p class="text-muted mb-0">{{ $count }} prodotti disponibili</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        <!-- === ACCESSO PER LIVELLI === -->
        <section class="mb-5">
            <h2 class="h3 mb-4 text-center">
                <i class="bi bi-people text-primary me-2"></i>
                Accesso per Operatori
            </h2>
            <div class="row g-4">
                <!-- Tecnici -->
                <div class="col-md-4">
                    <div class="card card-custom h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-gear display-4 text-info mb-3"></i>
                            <h5 class="card-title">Tecnici</h5>
                            <p class="card-text">
                                Accesso completo a malfunzionamenti e soluzioni tecniche per tutti i prodotti.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="bi bi-check text-success me-2"></i>Visualizza malfunzionamenti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Accesso alle soluzioni</li>
                                <li><i class="bi bi-check text-success me-2"></i>Segnala problemi</li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-info">Accedi come Tecnico</a>
                            @else
                                @if(Auth::user()->isTecnico())
                                    <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info">Dashboard Tecnico</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-info">Accedi come Tecnico</a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                <!-- Staff -->
                <div class="col-md-4">
                    <div class="card card-custom h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-badge display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Staff Aziendale</h5>
                            <p class="card-text">
                                Gestione completa di malfunzionamenti e soluzioni per i prodotti assegnati.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="bi bi-check text-success me-2"></i>Tutto del livello Tecnico</li>
                                <li><i class="bi bi-check text-success me-2"></i>Crea nuovi malfunzionamenti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Modifica soluzioni</li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-warning">Accedi come Staff</a>
                            @else
                                @if(Auth::user()->isStaff())
                                    <a href="{{ route('staff.dashboard') }}" class="btn btn-warning">Dashboard Staff</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-warning">Accedi come Staff</a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>

                <!-- Amministratori -->
                <div class="col-md-4">
                    <div class="card card-custom h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-fill-gear display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Amministratori</h5>
                            <p class="card-text">
                                Controllo completo del sistema: utenti, prodotti e configurazioni.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="bi bi-check text-success me-2"></i>Tutto dei livelli precedenti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Gestione utenti</li>
                                <li><i class="bi bi-check text-success me-2"></i>Gestione prodotti</li>
                            </ul>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-danger">Accedi come Admin</a>
                            @else
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Dashboard Admin</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-danger">Accedi come Admin</a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === CENTRI ASSISTENZA === -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="h3 mb-4">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Rete Centri Assistenza
                    </h2>
                    <p class="lead">
                        I nostri centri di assistenza sono distribuiti su tutto il territorio nazionale 
                        per garantire un servizio rapido e professionale.
                    </p>
                    <div class="row g-3 mb-4">
                        @if(isset($centri_principali) && count($centri_principali) > 0)
                            @foreach($centri_principali as $centro)
                                <div class="col-12">
                                    <div class="card border-start border-primary border-3">
                                        <div class="card-body py-3">
                                            <h6 class="card-title mb-1">{{ $centro->nome }}</h6>
                                            <p class="card-text small text-muted mb-1">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $centro->indirizzo ?? 'Indirizzo non disponibile' }}, 
                                                {{ $centro->citta }} 
                                                @if($centro->provincia)
                                                    ({{ $centro->provincia }})
                                                @endif
                                            </p>
                                            @if($centro->telefono)
                                                <p class="card-text small text-muted mb-0">
                                                    <i class="bi bi-telephone me-1"></i>{{ $centro->telefono }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Informazioni sui centri assistenza in aggiornamento.
                                </div>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('centri.index') }}" class="btn btn-primary">
                        <i class="bi bi-geo-alt me-2"></i>Vedi Tutti i Centri
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="card card-custom">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-clock text-primary me-2"></i>
                                Orari di Servizio
                            </h4>
                            <div class="row">
                                <div class="col-6">
                                    <h6>Assistenza Telefonica</h6>
                                    <p class="mb-1">Lun-Ven: 8:00-18:00</p>
                                    <p class="mb-3">Sab: 8:00-13:00</p>
                                    
                                    <h6>Interventi Domicilio</h6>
                                    <p class="mb-1">Lun-Ven: 9:00-17:00</p>
                                    <p class="mb-0">Sab: Su appuntamento</p>
                                </div>
                                <div class="col-6">
                                    <h6>Centri Assistenza</h6>
                                    <p class="mb-1">Lun-Ven: 8:30-17:30</p>
                                    <p class="mb-3">Sab: 8:30-12:30</p>
                                    
                                    <h6>Supporto Online</h6>
                                    <p class="mb-1">24/7 attraverso</p>
                                    <p class="mb-0">questo portale</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === STATISTICHE === -->
        @if(isset($stats) && count($stats) > 0)
        <section class="mb-5">
            <div class="card card-custom bg-primary text-white">
                <div class="card-body">
                    <h2 class="h3 mb-4 text-center">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche del Sistema
                    </h2>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <i class="bi bi-box display-4 mb-2"></i>
                            <h3 class="h2 fw-bold">{{ $stats['prodotti_totali'] ?? 0 }}</h3>
                            <p class="mb-0">Prodotti Attivi</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-exclamation-triangle display-4 mb-2"></i>
                            <h3 class="h2 fw-bold">{{ $stats['soluzioni_totali'] ?? 0 }}</h3>
                            <p class="mb-0">Soluzioni Disponibili</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-geo-alt display-4 mb-2"></i>
                            <h3 class="h2 fw-bold">{{ $stats['centri_totali'] ?? 0 }}</h3>
                            <p class="mb-0">Centri Assistenza</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-people display-4 mb-2"></i>
                            <h3 class="h2 fw-bold">{{ $stats['tecnici_totali'] ?? 0 }}</h3>
                            <p class="mb-0">Tecnici Specializzati</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- === CALL TO ACTION === -->
        <section class="mb-5">
            <div class="card card-custom bg-light">
                <div class="card-body text-center py-5">
                    <h2 class="h3 mb-4">Hai bisogno di assistenza?</h2>
                    <p class="lead mb-4">
                        Il nostro team di esperti è sempre pronto ad aiutarti a risolvere 
                        qualsiasi problema con i tuoi elettrodomestici.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('prodotti.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-search me-2"></i>Cerca Soluzione
                        </a>
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-telephone me-2"></i>Contatta Centro
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-person me-2"></i>Accedi
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // === RICERCA DINAMICA PRODOTTI ===
    let searchTimeout;
    
    $('#search-input').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchProdotti(query);
            }, 300); // Debounce di 300ms
        } else {
            $('#search-results').hide().empty();
        }
    });
    
    function searchProdotti(query) {
        // Mostra loading
        $('#search-results').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Ricerca in corso...
            </div>
        `).show();
        
        // Usa la route corretta per la ricerca
        const searchUrl = '{{ route("api.prodotti.search") }}';
        
        $.get(searchUrl, { q: query, type: 'public' })
            .done(function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '<div class="row g-3">';
                    
                    response.data.forEach(function(prodotto) {
                        const fotoUrl = prodotto.foto_url || '/images/no-image.png';
                        
                        html += `
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <img src="${fotoUrl}" 
                                                 class="me-3 rounded" 
                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                 alt="${prodotto.nome}"
                                                 onerror="this.src='/images/no-image.png'">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">
                                                    <a href="${prodotto.url}" class="text-decoration-none">
                                                        ${prodotto.nome}
                                                    </a>
                                                </h6>
                                                <p class="card-text small text-muted mb-1">
                                                    Modello: ${prodotto.modello}
                                                </p>
                                                <span class="badge bg-secondary">${prodotto.categoria}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    
                    if (response.data.length === 10) {
                        html += `
                            <div class="text-center mt-3">
                                <a href="{{ route('prodotti.index') }}?search=${encodeURIComponent(query)}" 
                                   class="btn btn-outline-primary">
                                    Vedi tutti i risultati
                                </a>
                            </div>
                        `;
                    }
                    
                    $('#search-results').html(html);
                } else {
                    $('#search-results').html(`
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-search me-2"></i>
                            Nessun prodotto trovato per "${query}"
                        </div>
                    `);
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Errore ricerca:', error);
                $('#search-results').html(`
                    <div class="text-center py-3 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Errore durante la ricerca. Riprova.
                    </div>
                `);
            });
    }
    
    // Nasconde i risultati quando si clicca fuori
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-input, #search-results').length) {
            $('#search-results').hide();
        }
    });
    
    // === EFFETTI HOVER SULLE CARD CATEGORIE ===
    $('.category-card').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // === ANIMAZIONI AL SCROLL ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Applica animazioni alle card
    $('.card-custom').each(function() {
        this.style.opacity = '0';
        this.style.transform = 'translateY(20px)';
        this.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(this);
    });
    
    // === SMOOTH SCROLL PER I LINK INTERNI ===
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    console.log('Homepage inizializzata con tutte le funzionalità');
});

// === GESTIONE ERRORI IMMAGINI ===
$(document).on('error', 'img', function() {
    if (this.src !== '/images/no-image.png') {
        this.src = '/images/no-image.png';
    }
});
</script>

<style>
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
    background-size: cover;
    pointer-events: none;
}

.spinner-custom {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Miglioramenti responsive */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .hero-section .row > div {
        text-align: center !important;
        margin-bottom: 2rem;
    }
    
    .btn-lg {
        padding: 0.7rem 1.5rem;
        font-size: 1rem;
    }
    
    .col-lg-6:first-child {
        margin-bottom: 2rem;
    }
}

/* Animazioni personalizzate */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease;
}

/* Effetti sui pulsanti */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Stili personalizzati per le icone */
.display-4 i,
.display-1 i {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Miglioramenti per il form di ricerca */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Stili per i badge delle certificazioni */
.bg-light {
    transition: all 0.3s ease;
}

.bg-light:hover {
    background-color: #f8f9fa !important;
    transform: scale(1.05);
}

/* Personalizzazione delle card dei centri assistenza */
.border-start {
    border-left-width: 4px !important;
}

/* Effetti di loading personalizzati */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.spinner-border-sm {
    animation: pulse 1.5s ease-in-out infinite;
}

/* Stili per le statistiche nella sezione hero */
.hero-section .col-4 h3 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Personalizzazione per dispositivi molto piccoli */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .hero-section .display-4 {
        font-size: 1.8rem;
    }
    
    .hero-section .lead {
        font-size: 1rem;
    }
    
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.6rem 1.2rem;
    }
    
    .col-4 h3 {
        font-size: 1.5rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}

/* Dark mode support (opzionale) */
@media (prefers-color-scheme: dark) {
    .card-custom {
        background-color: #f8f9fa;
    }
    
    .bg-light {
        background-color: #e9ecef !important;
    }
}
</style>
@endpush