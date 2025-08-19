<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Malfunzionamenti - Sistema Assistenza Tecnica</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* === STILI PERSONALIZZATI === */
        
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            z-index: 10;
        }
        
        .search-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filter-btn {
            margin: 3px;
            border-radius: 25px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            transform: scale(1.05);
        }
        
        .gravita-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .difficolta-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        
        .btn-refresh {
            position: absolute;
            top: 15px;
            right: 15px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .refresh-icon {
            transition: transform 0.5s ease;
        }
        
        .refresh-icon.spinning {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .top-item {
            padding: 10px 15px;
            margin: 5px 0;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .top-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('staff.dashboard') }}">
                <i class="bi bi-tools me-2"></i> Dashboard Staff - Malfunzionamenti
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle me-1"></i>
                    Benvenuto, {{ Auth::user()->nome_completo ?? Auth::user()->nome . ' ' . Auth::user()->cognome }}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('staff.dashboard') }}"><i class="bi bi-house"></i> Dashboard Staff</a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="bi bi-bug"></i> Gestione Malfunzionamenti
                </li>
            </ol>
        </nav>

        <!-- Statistiche Generali -->
        <div class="row mb-4" id="stats-container">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card stat-card h-100 position-relative">
                    <button class="btn btn-sm btn-outline-light btn-refresh" onclick="refreshAllStats()" title="Aggiorna statistiche">
                        <i class="bi bi-arrow-clockwise refresh-icon"></i>
                    </button>
                    <div class="card-body text-center">
                        <h2 class="card-title mb-2" id="stat-totale">{{ $stats['totale_malfunzionamenti'] ?? 0 }}</h2>
                        <p class="card-text mb-1">Totale Malfunzionamenti</p>
                        <small class="opacity-75">Nel sistema</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card stat-card danger h-100">
                    <div class="card-body text-center">
                        <h2 class="card-title mb-2" id="stat-critici">{{ $stats['critici'] ?? 0 }}</h2>
                        <p class="card-text mb-1">Critici</p>
                        <small class="opacity-75">Richiedono attenzione immediata</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card stat-card warning h-100">
                    <div class="card-body text-center">
                        <h2 class="card-title mb-2" id="stat-alta-priorita">{{ $stats['alta_priorita'] ?? 0 }}</h2>
                        <p class="card-text mb-1">Alta Priorità</p>
                        <small class="opacity-75">Da risolvere presto</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card stat-card success h-100">
                    <div class="card-body text-center">
                        <h2 class="card-title mb-2" id="stat-questo-mese">{{ $stats['creati_questo_mese'] ?? 0 }}</h2>
                        <p class="card-text mb-1">Questo Mese</p>
                        <small class="opacity-75">Nuovi malfunzionamenti</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container Ricerca Avanzata -->
        <div class="search-container">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-3 fw-bold">
                        <i class="bi bi-search"></i> Ricerca Avanzata Malfunzionamenti
                    </h5>
                    
                    <!-- Form di Ricerca -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Termine di ricerca</label>
                            <input type="text" class="form-control" id="search-term" 
                                   placeholder="Cerca in titolo e descrizione...">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gravità</label>
                            <select class="form-select" id="filter-gravita">
                                <option value="">Tutte</option>
                                <option value="critica">Critica</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="bassa">Bassa</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Difficoltà</label>
                            <select class="form-select" id="filter-difficolta">
                                <option value="">Tutte</option>
                                <option value="esperto">Esperto</option>
                                <option value="difficile">Difficile</option>
                                <option value="media">Media</option>
                                <option value="facile">Facile</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Bottoni di Azione -->
                    <div class="mt-3">
                        <button class="btn btn-primary" onclick="eseguiRicerca()">
                            <i class="bi bi-search"></i> Cerca
                        </button>
                        <button class="btn btn-outline-secondary ms-2" onclick="resetRicerca()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                        <button class="btn btn-outline-info ms-2" onclick="esportaRisultati()">
                            <i class="bi bi-download"></i> Esporta
                        </button>
                    </div>
                </div>
                
                <!-- Filtri Rapidi -->
                <div class="col-lg-4">
                    <h6 class="mb-3 fw-semibold">Filtri Rapidi</h6>
                    <div class="d-flex flex-wrap">
                        <button class="btn btn-outline-danger filter-btn" onclick="applicaFiltroRapido('critica')">
                            <i class="bi bi-exclamation-triangle-fill"></i> Solo Critici
                        </button>
                        <button class="btn btn-outline-warning filter-btn" onclick="applicaFiltroRapido('frequenti')">
                            <i class="bi bi-arrow-repeat"></i> Più Frequenti
                        </button>
                        <button class="btn btn-outline-info filter-btn" onclick="applicaFiltroRapido('recenti')">
                            <i class="bi bi-clock"></i> Recenti
                        </button>
                        <button class="btn btn-outline-success filter-btn" onclick="applicaFiltroRapido('facili')">
                            <i class="bi bi-check-circle"></i> Facili
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row Principale -->
        <div class="row">
            <!-- Tabella Risultati -->
            <div class="col-lg-8">
                <div class="card dashboard-card position-relative">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-ul"></i> 
                            Risultati Ricerca 
                            <span class="badge bg-primary" id="risultati-count">0</span>
                        </h5>
                        <div>
                            <select class="form-select form-select-sm" id="ordinamento" onchange="eseguiRicerca()">
                                <option value="gravita">Ordina per Gravità</option>
                                <option value="frequenza">Ordina per Frequenza</option>
                                <option value="recente">Ordina per Data</option>
                                <option value="difficolta">Ordina per Difficoltà</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Loading Overlay -->
                    <div class="loading-overlay d-none" id="loading-overlay">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-2" role="status">
                                <span class="visually-hidden">Caricamento...</span>
                            </div>
                            <p class="mb-0">Caricamento malfunzionamenti...</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- Tabella Risultati -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabella-risultati">
                                <thead>
                                    <tr>
                                        <th>Titolo</th>
                                        <th>Prodotto</th>
                                        <th>Gravità</th>
                                        <th>Difficoltà</th>
                                        <th>Segnalazioni</th>
                                        <th>Tempo</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody id="risultati-tbody">
                                    <!-- Caricato via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Messaggio Nessun Risultato -->
                        <div class="p-5 text-center d-none" id="no-results">
                            <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Nessun risultato trovato</h5>
                            <p class="text-muted">Prova a modificare i criteri di ricerca</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar con Grafici -->
            <div class="col-lg-4">
                <!-- Grafico Distribuzione Gravità -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-pie-chart"></i> Distribuzione per Gravità
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="grafico-gravita"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Top Malfunzionamenti Frequenti -->
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-arrow-up-circle"></i> Più Frequenti
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="top-frequenti">
                            @if(isset($piu_frequenti) && $piu_frequenti->count() > 0)
                                @foreach($piu_frequenti->take(5) as $malfunction)
                                <div class="top-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="fw-semibold">{{ $malfunction->titolo }}</small>
                                            <br>
                                            <span class="badge gravita-badge 
                                                @if($malfunction->gravita == 'critica') bg-danger
                                                @elseif($malfunction->gravita == 'alta') bg-warning text-dark
                                                @elseif($malfunction->gravita == 'media') bg-info
                                                @else bg-success @endif">
                                                {{ ucfirst($malfunction->gravita) }}
                                            </span>
                                        </div>
                                        <span class="badge bg-primary">{{ $malfunction->numero_segnalazioni ?? 0 }}</span>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">Nessun dato disponibile</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ================================================
        // CONFIGURAZIONE GLOBALE
        // ================================================
        
        // Token CSRF per le richieste AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // URLs API corretti basati sulle tue route
        const API_URLS = {
            search: '/~grp_51/laraProject/public/api/malfunzionamenti/search',
            stats: '/~grp_51/laraProject/public/api/staff/stats',
            export: '/~grp_51/laraProject/public/api/export/malfunzionamenti'
        };
        
        // Abilita fallback alla simulazione se le API non funzionano
        const ENABLE_FALLBACK = true;
        
        // Variabili globali
        let chartGravita = null;
        let ultimaRicerca = {
            q: '',
            gravita: '',
            difficolta: '',
            order: 'gravita'
        };

        // ================================================
        // INIZIALIZZAZIONE
        // ================================================
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard malfunzionamenti inizializzata');
            
            // Setup event listeners
            setupEventListeners();
            
            // Carica dati iniziali
            caricaStatistiche();
            eseguiRicercaIniziale();
            inizializzaGraficoGravita();
            
            // Auto refresh ogni 5 minuti
            setInterval(refreshAllStats, 300000);
        });

        // ================================================
        // EVENT LISTENERS
        // ================================================
        
        function setupEventListeners() {
            // Ricerca in tempo reale con debounce
            let searchTimeout;
            document.getElementById('search-term').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(eseguiRicerca, 800);
            });
            
            // Filtri che scatenano ricerca immediata
            document.getElementById('filter-gravita').addEventListener('change', eseguiRicerca);
            document.getElementById('filter-difficolta').addEventListener('change', eseguiRicerca);
            
            // Enter key per ricerca
            document.getElementById('search-term').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    eseguiRicerca();
                }
            });
        }

        // ================================================
        // FUNZIONI DI RICERCA
        // ================================================
        
        function eseguiRicercaIniziale() {
            // Carica i primi 20 malfunzionamenti ordinati per gravità
            eseguiRicerca(true);
        }
        
        function eseguiRicerca(isInitial = false) {
            console.log('Eseguendo ricerca malfunzionamenti...');
            
            // Mostra loading
            mostraLoading(true);
            
            // Prepara parametri di ricerca
            const params = {
                q: document.getElementById('search-term').value.trim(),
                gravita: document.getElementById('filter-gravita').value,
                difficolta: document.getElementById('filter-difficolta').value,
                order: document.getElementById('ordinamento').value,
                limit: 20
            };
            
            // Salva ultima ricerca
            ultimaRicerca = { ...params };
            
            // Costruisci URL con parametri
            const url = new URL(API_URLS.search, window.location.origin);
            Object.keys(params).forEach(key => {
                if (params[key]) {
                    url.searchParams.append(key, params[key]);
                }
            });
            
            // Effettua la richiesta
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Ricerca completata:', data);
                
                if (data.success) {
                    aggiornaTabella(data.data);
                    aggiornaBadgeRisultati(data.total);
                    
                    if (!isInitial) {
                        mostraMessaggio('Ricerca completata con successo', 'success');
                    }
                } else {
                    throw new Error(data.message || 'Errore nella ricerca');
                }
            })
            .catch(error => {
                console.error('Errore nella ricerca:', error);
                mostraErrore('Errore durante la ricerca: ' + error.message);
                aggiornaTabella([]);
                aggiornaBadgeRisultati(0);
            })
            .finally(() => {
                mostraLoading(false);
            });
        }
        
        function resetRicerca() {
            // Reset form
            document.getElementById('search-term').value = '';
            document.getElementById('filter-gravita').value = '';
            document.getElementById('filter-difficolta').value = '';
            document.getElementById('ordinamento').value = 'gravita';
            
            // Esegui ricerca vuota
            eseguiRicerca();
            
            mostraMessaggio('Filtri reimpostati', 'info');
        }

        // ================================================
        // FILTRI RAPIDI
        // ================================================
        
        function applicaFiltroRapido(tipo) {
            console.log('Applicando filtro rapido:', tipo);
            
            // Reset form
            document.getElementById('search-term').value = '';
            document.getElementById('filter-gravita').value = '';
            document.getElementById('filter-difficolta').value = '';
            
            // Applica filtro specifico
            switch(tipo) {
                case 'critica':
                    document.getElementById('filter-gravita').value = 'critica';
                    document.getElementById('ordinamento').value = 'frequenza';
                    break;
                    
                case 'frequenti':
                    document.getElementById('ordinamento').value = 'frequenza';
                    break;
                    
                case 'recenti':
                    document.getElementById('ordinamento').value = 'recente';
                    break;
                    
                case 'facili':
                    document.getElementById('filter-difficolta').value = 'facile';
                    break;
            }
            
            // Esegui ricerca con nuovo filtro
            eseguiRicerca();
            
            mostraMessaggio(`Filtro "${tipo}" applicato`, 'info');
        }

        // ================================================
        // AGGIORNAMENTO INTERFACCIA
        // ================================================
        
        function aggiornaTabella(malfunzionamenti) {
            const tbody = document.getElementById('risultati-tbody');
            const noResults = document.getElementById('no-results');
            
            if (!malfunzionamenti || malfunzionamenti.length === 0) {
                tbody.innerHTML = '';
                noResults.classList.remove('d-none');
                return;
            }
            
            noResults.classList.add('d-none');
            
            tbody.innerHTML = malfunzionamenti.map(malfunction => `
                <tr>
                    <td>
                        <div>
                            <strong>${escapeHtml(malfunction.titolo)}</strong>
                            <br>
                            <small class="text-muted">${escapeHtml(malfunction.descrizione)}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="fw-medium">${escapeHtml(malfunction.prodotto_nome || 'N/A')}</span>
                            <br>
                            <small class="text-muted">${escapeHtml(malfunction.prodotto_modello || '')}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge gravita-badge ${getGravitaClass(malfunction.gravita)}">
                            ${capitalizeFirst(malfunction.gravita)}
                        </span>
                    </td>
                    <td>
                        <span class="badge difficolta-badge ${getDifficoltaClass(malfunction.difficolta)}">
                            ${capitalizeFirst(malfunction.difficolta)}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info">${malfunction.segnalazioni || 0}</span>
                    </td>
                    <td>
                        <small>${malfunction.tempo_stimato ? malfunction.tempo_stimato + ' min' : 'N/A'}</small>
                    </td>
                    <td>
                        <a href="${malfunction.url || '#'}" class="btn btn-sm btn-outline-primary" title="Visualizza dettagli">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            `).join('');
        }
        
        function aggiornaBadgeRisultati(count) {
            document.getElementById('risultati-count').textContent = count;
        }
        
        function mostraLoading(show) {
            const overlay = document.getElementById('loading-overlay');
            
            if (show) {
                overlay.classList.remove('d-none');
            } else {
                overlay.classList.add('d-none');
            }
        }

        // ================================================
        // CARICAMENTO STATISTICHE
        // ================================================
        
        function caricaStatistiche() {
            fetch(API_URLS.stats, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.stats) {
                    aggiornaStatistiche(data.stats);
                    aggiornaGraficoGravita(data.stats);
                    console.log('✅ Statistiche caricate con successo');
                } else {
                    throw new Error(data.message || 'Errore nei dati statistiche');
                }
            })
            .catch(error => {
                console.error('Errore caricamento statistiche:', error);
                
                if (ENABLE_FALLBACK) {
                    console.log('🔄 Attivando fallback simulazione...');
                    // Usa i dati dal server come fallback
                    const fallbackStats = {
                        total_malfunzionamenti: {{ $stats['totale_malfunzionamenti'] ?? 0 }},
                        critici: {{ $stats['critici'] ?? 0 }},
                        alta_priorita: {{ $stats['alta_priorita'] ?? 0 }},
                        creati_questo_mese: {{ $stats['creati_questo_mese'] ?? 0 }}
                    };
                    aggiornaStatistiche(fallbackStats);
                    aggiornaGraficoGravita(fallbackStats);
                    mostraMessaggio('Statistiche caricate (modalità offline)', 'warning');
                } else {
                    mostraErrore('Errore nel caricamento delle statistiche');
                }
            });
        }
        
        function aggiornaStatistiche(stats) {
            // Aggiorna contatori nelle card con animazione
            const updates = [
                { id: 'stat-totale', value: stats.total_malfunzionamenti || 0 },
                { id: 'stat-critici', value: stats.critici || 0 },
                { id: 'stat-alta-priorita', value: stats.alta_priorita || 0 },
                { id: 'stat-questo-mese', value: stats.creati_questo_mese || 0 }
            ];
            
            updates.forEach(update => {
                const element = document.getElementById(update.id);
                if (element) {
                    animateNumber(element, parseInt(element.textContent) || 0, update.value);
                }
            });
        }
        
        function animateNumber(element, from, to) {
            const duration = 1000;
            const increment = (to - from) / (duration / 16);
            let current = from;
            
            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= to) || (increment < 0 && current <= to)) {
                    current = to;
                    clearInterval(timer);
                }
                element.textContent = Math.round(current);
            }, 16);
        }
        
        function refreshAllStats() {
            const refreshIcon = document.querySelector('.refresh-icon');
            refreshIcon.classList.add('spinning');
            
            caricaStatistiche();
            
            setTimeout(() => {
                refreshIcon.classList.remove('spinning');
                mostraMessaggio('Statistiche aggiornate', 'success');
            }, 1000);
        }

        // ================================================
        // GRAFICO DISTRIBUZIONE GRAVITÀ
        // ================================================
        
        function inizializzaGraficoGravita() {
            // Dati iniziali del server
            const datiIniziali = {
                critica: {{ $stats['critici'] ?? 0 }},
                alta: {{ $stats['alta_priorita'] ?? 0 }},
                media: 0,
                bassa: 0
            };
            
            // Calcola media e bassa come differenza
            const totale = {{ $stats['totale_malfunzionamenti'] ?? 0 }};
            const assigned = datiIniziali.critica + datiIniziali.alta;
            const remaining = Math.max(0, totale - assigned);
            
            datiIniziali.media = Math.floor(remaining * 0.6);
            datiIniziali.bassa = remaining - datiIniziali.media;
            
            creaGraficoGravita(datiIniziali);
        }
        
        function aggiornaGraficoGravita(stats) {
            const datiGravita = {
                critica: stats.critici || 0,
                alta: stats.alta_priorita || 0,
                media: 0,
                bassa: 0
            };
            
            // Calcola approssimativamente media e bassa
            const totale = stats.total_malfunzionamenti || 0;
            const assigned = datiGravita.critica + datiGravita.alta;
            const remaining = Math.max(0, totale - assigned);
            
            datiGravita.media = Math.floor(remaining * 0.6);
            datiGravita.bassa = remaining - datiGravita.media;
            
            creaGraficoGravita(datiGravita);
        }
        
        function creaGraficoGravita(datiGravita) {
            const canvas = document.getElementById('grafico-gravita');
            const ctx = canvas.getContext('2d');
            
            // Distruggi grafico esistente
            if (chartGravita) {
                chartGravita.destroy();
            }
            
            const totale = Object.values(datiGravita).reduce((a, b) => a + b, 0);
            
            if (totale === 0) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '16px Arial';
                ctx.fillStyle = '#6c757d';
                ctx.textAlign = 'center';
                ctx.fillText('Nessun dato disponibile', canvas.width / 2, canvas.height / 2);
                return;
            }
            
            // Crea nuovo grafico
            chartGravita = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Critica', 'Alta', 'Media', 'Bassa'],
                    datasets: [{
                        data: [datiGravita.critica, datiGravita.alta, datiGravita.media, datiGravita.bassa],
                        backgroundColor: [
                            '#dc3545', // Rosso per critica
                            '#fd7e14', // Arancione per alta
                            '#ffc107', // Giallo per media
                            '#198754'  // Verde per bassa
                        ],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverBorderWidth: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
        }

        // ================================================
        // ESPORTAZIONE DATI
        // ================================================
        
        function esportaRisultati() {
            console.log('Esportando risultati...');
            
            const params = new URLSearchParams(ultimaRicerca);
            const url = API_URLS.export + '?' + params.toString();
            
            fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore durante l\'esportazione');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Crea e scarica file JSON
                    const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = data.filename || 'malfunzionamenti_export.json';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    mostraMessaggio('Export completato con successo', 'success');
                } else {
                    throw new Error(data.message || 'Errore durante l\'esportazione');
                }
            })
            .catch(error => {
                console.error('Errore export:', error);
                mostraErrore('Errore durante l\'esportazione: ' + error.message);
            });
        }

        // ================================================
        // FUNZIONI UTILITY
        // ================================================
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function capitalizeFirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
        function getGravitaClass(gravita) {
            const classes = {
                'critica': 'bg-danger',
                'alta': 'bg-warning text-dark',
                'media': 'bg-info',
                'bassa': 'bg-success'
            };
            return classes[gravita] || 'bg-secondary';
        }
        
        function getDifficoltaClass(difficolta) {
            const classes = {
                'esperto': 'bg-danger',
                'difficile': 'bg-warning text-dark',
                'media': 'bg-info',
                'facile': 'bg-success'
            };
            return classes[difficolta] || 'bg-secondary';
        }
        
        function mostraMessaggio(messaggio, tipo = 'info') {
            const alertContainer = document.getElementById('alert-container');
            const alertId = 'alert-' + Date.now();
            
            const alertHtml = `
                <div class="alert alert-${tipo} alert-dismissible fade show alert-custom" role="alert" id="${alertId}">
                    <i class="bi bi-${getIconForAlertType(tipo)} me-2"></i>
                    ${escapeHtml(messaggio)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            alertContainer.insertAdjacentHTML('beforeend', alertHtml);
            
            // Auto-remove dopo 5 secondi
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
        
        function mostraErrore(messaggio) {
            mostraMessaggio(messaggio, 'danger');
        }
        
        function getIconForAlertType(tipo) {
            const icons = {
                'success': 'check-circle-fill',
                'danger': 'exclamation-triangle-fill',
                'warning': 'exclamation-triangle-fill',
                'info': 'info-circle-fill'
            };
            return icons[tipo] || 'info-circle-fill';
        }

        // ================================================
        // GESTIONE ERRORI GLOBALI
        // ================================================
        
        window.addEventListener('error', function(e) {
            console.error('Errore JavaScript:', e.error);
            mostraErrore('Si è verificato un errore imprevisto. Ricarica la pagina se il problema persiste.');
        });
        
        // Gestione errori per fetch non catturati
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promise rejection non gestita:', e.reason);
            mostraErrore('Errore di comunicazione con il server. Riprova più tardi.');
        });

        // ================================================
        // FUNZIONI DI DEBUG
        // ================================================
        
        function testConnessioneAPI() {
            console.log('Testing API connections...');
            
            // Test API di ricerca
            fetch(API_URLS.search + '?limit=1', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('API Ricerca:', response.ok ? 'OK' : 'ERRORE');
                return response.json();
            })
            .then(data => {
                console.log('Risposta API Ricerca:', data);
            })
            .catch(error => {
                console.error('Errore API Ricerca:', error);
            });
            
            // Test API statistiche
            fetch(API_URLS.stats, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('API Stats:', response.ok ? 'OK' : 'ERRORE');
                return response.json();
            })
            .then(data => {
                console.log('Risposta API Stats:', data);
            })
            .catch(error => {
                console.error('Errore API Stats:', error);
            });
        }

        // ================================================
        // SHORTCUTS DA TASTIERA
        // ================================================
        
        document.addEventListener('keydown', function(e) {
            // Ctrl+F per focus su ricerca
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search-term').focus();
            }
            
            // Ctrl+R per refresh stats
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                refreshAllStats();
            }
            
            // Escape per reset ricerca
            if (e.key === 'Escape') {
                resetRicerca();
            }
        });

        // ================================================
        // ESPOSIZIONE FUNZIONI PER DEBUG
        // ================================================
        
        // Esponi funzioni per debugging in console
        window.debugDashboard = {
            testAPI: testConnessioneAPI,
            ricerca: eseguiRicerca,
            reset: resetRicerca,
            stats: caricaStatistiche,
            refresh: refreshAllStats,
            ultimaRicerca: () => ultimaRicerca,
            mostraErrore: mostraErrore,
            mostraMessaggio: mostraMessaggio
        };
        
        console.log('🚀 Dashboard caricata completamente!');
        console.log('📊 Comandi debug disponibili in window.debugDashboard');
        console.log('⌨️  Shortcuts: Ctrl+F (ricerca), Ctrl+R (refresh), Esc (reset)');
    </script>
</body>
</html>