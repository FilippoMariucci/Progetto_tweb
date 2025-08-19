{{-- 
    Vista specifica per la dashboard amministratore
    File: resources/views/admin/dashboard.blade.php
    
    Questa vista mostra il pannello di controllo completo per gli amministratori (livello 4)
    Include statistiche, gestione utenti, prodotti, centri assistenza e funzionalità avanzate
--}}
@extends('layouts.app')

@section('title', 'Dashboard Amministratore')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per l'amministratore --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-shield-check text-danger me-2"></i>
                Pannello Amministratore
            </h1>
            
            {{-- Benvenuto personalizzato per admin --}}
            <div class="alert alert-danger border-start border-danger border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-fill-gear display-6 text-danger me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Benvenuto, {{ auth()->user()->nome_completo }}!</h4>
                        <p class="mb-0">
                            <span class="badge bg-danger">Amministratore Sistema</span>
                        </p>
                        <small class="text-muted">
                            Controllo completo su utenti, prodotti e configurazioni sistema
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- === GESTIONE PRINCIPALE === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Gestione Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Gestione utenti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-lg w-100 h-100">
                                <i class="bi bi-people display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Utenti</span>
                            </a>
                        </div>
                        
                        {{-- Gestione prodotti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.prodotti.index') }}" class="btn btn-primary btn-lg w-100 h-100">
                                <i class="bi bi-box-seam display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestione Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Gestione centri --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.centri.index') }}" class="btn btn-info btn-lg w-100 h-100">
                                <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Centri Assistenza</span>
                            </a>
                        </div>
                        
                        {{-- Assegnazione prodotti --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.assegnazioni') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-person-gear display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Assegna Prodotti</span>
                            </a>
                        </div>
                        
                        {{-- Statistiche avanzate --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.statistiche') }}" class="btn btn-success btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Statistiche</span>
                            </a>
                        </div>
                        
                        {{-- Backup/Manutenzione --}}
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('admin.manutenzione') }}" class="btn btn-secondary btn-lg w-100 h-100">
                                <i class="bi bi-tools display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Manutenzione</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === STATISTICHE GENERALI === --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche Sistema
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Controllo se le statistiche sono disponibili --}}
                    @if(isset($stats) && count($stats) > 0)
                        <div class="row g-3 text-center">
                            {{-- Totale utenti --}}
                            @if(isset($stats['total_utenti']))
                                <div class="col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded stat-utenti">
                                        <i class="bi bi-people text-danger fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_utenti'] }}</h4>
                                        <small class="text-muted">Utenti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Totale prodotti --}}
                            @if(isset($stats['total_prodotti']))
                                <div class="col-6">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded stat-prodotti">
                                        <i class="bi bi-box text-primary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_prodotti'] }}</h4>
                                        <small class="text-muted">Prodotti</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Totale centri --}}
                            @if(isset($stats['total_centri']))
                                <div class="col-6">
                                    <div class="p-3 bg-info bg-opacity-10 rounded stat-centri">
                                        <i class="bi bi-geo-alt text-info fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_centri'] }}</h4>
                                        <small class="text-muted">Centri</small>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Totale soluzioni --}}
                            @if(isset($stats['total_soluzioni']))
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded stat-soluzioni">
                                        <i class="bi bi-check-circle text-success fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['total_soluzioni'] }}</h4>
                                        <small class="text-muted">Soluzioni</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted text-center">Caricamento statistiche...</p>
                    @endif
                    
                    {{-- Pulsante aggiornamento manuale --}}
                    <div class="text-center mt-3">
                        <button id="manual-refresh-btn" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Aggiorna
                        </button>
                        <small class="d-block text-muted mt-1">
                            Ultimo aggiornamento: <span id="last-update-time">{{ now()->format('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === UTENTI RECENTI E ATTIVITÀ === --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        Utenti Recenti
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Lista utenti recenti se disponibili --}}
                    @if(isset($stats['utenti_recenti']) && $stats['utenti_recenti']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['utenti_recenti']->take(5) as $utente)
                                <div class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $utente->nome_completo }}</h6>
                                            <small class="text-muted">{{ $utente->username }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge badge-livello badge-livello-{{ $utente->livello_accesso }}">
                                                {{ $utente->livello_descrizione }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $utente->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                                Gestisci tutti gli utenti
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center">Nessun utente recente</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- === PRODOTTI NON ASSEGNATI === --}}
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Prodotti Non Assegnati
                        {{-- Badge dinamico per conteggio prodotti non assegnati --}}
                        @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                            <span class="badge bg-danger ms-2">{{ $stats['prodotti_non_assegnati_count'] }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body" id="prodotti-non-assegnati-container">
                    {{-- Controllo prodotti non assegnati --}}
                    @if(isset($stats['prodotti_non_assegnati_count']) && $stats['prodotti_non_assegnati_count'] > 0)
                        
                        {{-- Alert con conteggio --}}
                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <div>
                                <strong>{{ $stats['prodotti_non_assegnati_count'] }} prodotti</strong> 
                                non hanno uno staff assegnato
                            </div>
                        </div>

                        {{-- Lista dei prodotti non assegnati --}}
                        @if(isset($stats['prodotti_non_assegnati']) && $stats['prodotti_non_assegnati']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($stats['prodotti_non_assegnati']->take(5) as $prodotto)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 fw-semibold">{{ $prodotto->nome }}</h6>
                                                <small class="text-muted">
                                                    {{ $prodotto->modello }} 
                                                    @if($prodotto->categoria)
                                                        • {{ ucfirst($prodotto->categoria) }}
                                                    @endif
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    Creato {{ $prodotto->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                {{-- Badge stato prodotto --}}
                                                @if(!$prodotto->attivo)
                                                    <span class="badge bg-secondary mb-1">Disattivo</span><br>
                                                @endif
                                                <a href="{{ route('admin.assegnazioni') }}?prodotto={{ $prodotto->id }}" 
                                                   class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-person-plus"></i> Assegna
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Link per gestire tutti i prodotti non assegnati --}}
                            @if($stats['prodotti_non_assegnati_count'] > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.assegnazioni') }}?non_assegnati=1" 
                                       class="btn btn-outline-warning">
                                        <i class="bi bi-list me-1"></i>
                                        Vedi tutti i {{ $stats['prodotti_non_assegnati_count'] }} prodotti
                                    </a>
                                </div>
                            @else
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.assegnazioni') }}" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-gear me-1"></i>
                                        Gestisci Assegnazioni
                                    </a>
                                </div>
                            @endif
                        @endif

                    @else
                        {{-- Tutti i prodotti sono assegnati --}}
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h5 class="text-success mt-2 mb-1">Perfetto!</h5>
                            <p class="text-success mb-3">Tutti i prodotti sono assegnati</p>
                            <a href="{{ route('admin.assegnazioni') }}" class="btn btn-outline-success">
                                <i class="bi bi-eye me-1"></i>Visualizza Assegnazioni
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- === DISTRIBUZIONE UTENTI E SISTEMA === --}}
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribuzione Utenti
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Mostra distribuzione se disponibile --}}
                    @if(isset($stats['distribuzione_utenti']))
                        <div class="row g-2 text-center">
                            @foreach($stats['distribuzione_utenti'] as $livello => $count)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                        <span class="fw-semibold">
                                            {{-- Converte numero livello in etichetta --}}
                                            @switch($livello)
                                                @case(2) Tecnici @break
                                                @case(3) Staff @break
                                                @case(4) Admin @break
                                                @default Utenti @break
                                            @endswitch
                                        </span>
                                        <span class="badge badge-livello badge-livello-{{ $livello }}">
                                            {{ $count }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Dati non disponibili</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- === STATO SISTEMA === --}}
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cpu me-2"></i>
                        Stato Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        {{-- Indicatori stato sistema --}}
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span>Database</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span>Laravel</span>
                            <span class="badge bg-info">v{{ app()->version() }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span>PHP</span>
                            <span class="badge bg-info">v{{ PHP_VERSION }}</span>
                        </div>
                        {{-- Ultimo backup se disponibile --}}
                        @if(isset($stats['ultimo_backup']))
                            <div class="list-group-item px-0 d-flex justify-content-between">
                                <span>Ultimo Backup</span>
                                <span class="badge bg-warning text-dark">{{ $stats['ultimo_backup'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- === AZIONI RAPIDE === --}}
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Pulsanti per azioni rapide --}}
                        <a href="{{ route('admin.users.create') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                        <a href="{{ route('admin.prodotti.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Nuovo Prodotto
                        </a>
                        <a href="{{ route('admin.centri.create') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-geo-alt-fill me-1"></i>Nuovo Centro
                        </a>
                        <hr class="my-2">
                        <a href="{{ route('admin.export') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i>Esporta Dati
                        </a>
                        <a href="{{ route('admin.manutenzione') }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-gear me-1"></i>Manutenzione
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === LINK DASHBOARD ALTERNATIVE === --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="bi bi-grid text-secondary me-2"></i>
                        Visualizzazioni Alternative
                    </h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                        {{-- Link alle diverse viste --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>Dashboard Generale
                        </a>
                        <a href="{{ route('prodotti.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box me-1"></i>Vista Pubblica
                        </a>
                        <a href="{{ route('prodotti.index') }}?view=tech" class="btn btn-outline-info">
                            <i class="bi bi-tools me-1"></i>Vista Tecnico
                        </a>
                        <a href="{{ route('documentazione') }}" class="btn btn-outline-success" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Documentazione
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    Script JavaScript per gestione dinamica della dashboard
    Include aggiornamento automatico statistiche e controllo stato sistema
--}}
@push('scripts')
<script>
/**
 * JavaScript per Dashboard Admin - Gestione dinamica e aggiornamenti
 * Questo script gestisce l'aggiornamento automatico delle statistiche
 * e il controllo dello stato del sistema
 */

// Attende che il DOM sia completamente caricato
$(document).ready(function() {
    console.log('🔧 Dashboard Admin inizializzata');
    
    // Aggiornamento automatico delle statistiche ogni 2 minuti
    setInterval(function() {
        updateAdminStats();
    }, 120000); // 120000ms = 2 minuti
    
    // Controllo stato sistema ogni minuto
    setInterval(function() {
        checkSystemStatus();
    }, 60000); // 60000ms = 1 minuto
    
    /**
     * Funzione per aggiornare le statistiche della dashboard admin
     * Effettua chiamata AJAX al server per ottenere dati aggiornati
     */
    function updateAdminStats() {
        $.ajax({
            url: "/admin/stats-update", // Route nell'AdminController per statistiche
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Verifica che la risposta sia valida
                if (data.success && data.stats) {
                    console.log('📊 Statistiche aggiornate', data.stats);
                    
                    // Aggiorna i contatori nelle card statistiche
                    updateStatCards(data.stats);
                    
                    // Aggiorna la sezione prodotti non assegnati
                    updateProdottiNonAssegnati(data.stats);
                    
                    // Aggiorna il timestamp dell'ultimo aggiornamento
                    const now = new Date().toLocaleTimeString('it-IT');
                    $('#last-update-time').text(now);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Errore aggiornamento statistiche:', error);
                
                // Mostra indicatore di errore discreto
                showUpdateError();
            }
        });
    }
    
    /**
     * Aggiorna i contatori numerici nelle card delle statistiche
     * @param {Object} stats - Oggetto contenente le statistiche aggiornate
     */
    function updateStatCards(stats) {
        // Mappatura tra chiavi statistiche e selettori CSS
        const statMappings = {
            'total_utenti': '.stat-utenti h4',
            'total_prodotti': '.stat-prodotti h4', 
            'total_centri': '.stat-centri h4',
            'total_soluzioni': '.stat-soluzioni h4'
        };
        
        // Aggiorna ogni contatore se il dato è disponibile
        $.each(statMappings, function(statKey, selector) {
            if (stats[statKey] !== undefined) {
                $(selector).text(formatNumber(stats[statKey]));
            }
        });
    }
    
    /**
     * FUNZIONE PRINCIPALE - Aggiorna la sezione prodotti non assegnati
     * Questa è la funzione più importante per il controllo dei prodotti
     * @param {Object} stats - Statistiche dal server
     */
    function updateProdottiNonAssegnati(stats) {
        // Trova il container della sezione prodotti non assegnati
        const container = $('#prodotti-non-assegnati-container');
        
        if (!container.length) {
            console.warn('⚠️ Container prodotti non assegnati non trovato');
            return;
        }
        
        // Estrae i dati sui prodotti non assegnati
        const count = stats.prodotti_non_assegnati_count || 0;
        const prodotti = stats.prodotti_non_assegnati || [];
        
        console.log(`📦 Prodotti non assegnati: ${count}`, prodotti);
        
        if (count > 0) {
            // CI SONO PRODOTTI NON ASSEGNATI
            
            // Aggiorna il badge nel header se esiste
            const headerBadge = $('.card-header:has(.bi-exclamation-triangle) .badge');
            if (headerBadge.length) {
                headerBadge.text(count).removeClass('d-none');
            } else {
                // Crea il badge se non esiste
                $('.card-header:has(.bi-exclamation-triangle) h5').append(`
                    <span class="badge bg-danger ms-2">${count}</span>
                `);
            }
            
            // Costruisce l'HTML per mostrare la lista dei prodotti
            let html = `
                <div class="alert alert-warning d-flex align-items-center mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>
                        <strong>${count} prodotti</strong> 
                        non hanno uno staff assegnato
                    </div>
                </div>
            `;
            
            // Se ci sono prodotti specifici da mostrare
            if (prodotti.length > 0) {
                html += '<div class="list-group list-group-flush">';
                
                // Mostra massimo 5 prodotti
                prodotti.slice(0, 5).forEach(function(prodotto) {
                    const createdAt = new Date(prodotto.created_at).toLocaleDateString('it-IT');
                    const badgeInattivo = !prodotto.attivo ? 
                        '<span class="badge bg-secondary mb-1">Disattivo</span><br>' : '';
                    
                    html += `
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">${prodotto.nome}</h6>
                                    <small class="text-muted">
                                        ${prodotto.modello}
                                        ${prodotto.categoria ? ' • ' + prodotto.categoria : ''}
                                    </small>
                                    <br>
                                    <small class="text-muted">Creato il ${createdAt}</small>
                                </div>
                                <div class="text-end">
                                    ${badgeInattivo}
                                    <a href="/admin/assegnazioni?prodotto=${prodotto.id}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-person-plus"></i> Assegna
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                
                // Link per visualizzare tutti i prodotti se ce ne sono di più
                if (count > 5) {
                    html += `
                        <div class="text-center mt-3">
                            <a href="/admin/assegnazioni?non_assegnati=1" 
                               class="btn btn-outline-warning">
                                <i class="bi bi-list me-1"></i>
                                Vedi tutti i ${count} prodotti
                            </a>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="text-center mt-3">
                            <a href="/admin/assegnazioni" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-gear me-1"></i>
                                Gestisci Assegnazioni
                            </a>
                        </div>
                    `;
                }
            }
            
            // Sostituisce il contenuto del container
            container.html(html);
            
        } else {
            // NESSUN PRODOTTO NON ASSEGNATO - Tutti i prodotti sono assegnati
            
            // Rimuove il badge dal header se presente
            $('.card-header:has(.bi-exclamation-triangle) .badge').remove();
            
            // Mostra messaggio di successo
            const successHtml = `
                <div class="text-center py-4">
                    <i class="bi bi-check-circle display-1 text-success"></i>
                    <h5 class="text-success mt-2 mb-1">Perfetto!</h5>
                    <p class="text-success mb-3">Tutti i prodotti sono assegnati</p>
                    <a href="/admin/assegnazioni" class="btn btn-outline-success">
                        <i class="bi bi-eye me-1"></i>Visualizza Assegnazioni
                    </a>
                </div>
            `;
            
            container.html(successHtml);
        }
    }
    
    /**
     * Controlla lo stato dei servizi di sistema
     * Verifica database, storage e altri componenti critici
     */
    function checkSystemStatus() {
        $.ajax({
            url: "/admin/system-status", // Route nell'AdminController
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    updateSystemStatus(data.services);
                }
            },
            error: function() {
                console.warn('⚠️ Controllo stato sistema fallito');
            }
        });
    }
    
    /**
     * Aggiorna gli indicatori dello stato sistema nella dashboard
     * @param {Object} status - Stato dei vari servizi (database, storage, etc)
     */
    function updateSystemStatus(status) {
        // Aggiorna ogni servizio con il suo stato attuale
        $.each(status, function(component, state) {
            const badge = $(`.list-group-item:contains("${component}") .badge`);
            if (badge.length) {
                // Rimuove le classi di stato precedenti
                badge.removeClass('bg-success bg-warning bg-danger bg-info');
                
                // Applica la classe CSS appropriata in base allo stato
                switch(state) {
                    case 'online':
                    case 'writable':
                    case 'active':
                        badge.addClass('bg-success').text('Online');
                        break;
                    case 'read-only':
                        badge.addClass('bg-warning').text('Read-Only');
                        break;
                    case 'error':
                        badge.addClass('bg-danger').text('Errore');
                        break;
                    default:
                        badge.addClass('bg-info').text(state);
                }
            }
        });
    }
    
    /**
     * Mostra un indicatore di errore temporaneo quando l'aggiornamento fallisce
     */
    function showUpdateError() {
        // Crea un alert temporaneo di errore
        const indicator = $(`
            <div class="position-fixed top-0 end-0 m-3 alert alert-danger alert-dismissible" style="z-index: 9999;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Errore aggiornamento dati
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(indicator);
        
        // Rimuove automaticamente l'indicatore dopo 3 secondi
        setTimeout(function() {
            indicator.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Gestione del pulsante di aggiornamento manuale
     * Consente all'admin di aggiornare le statistiche on-demand
     */
    if ($('#manual-refresh-btn').length) {
        $('#manual-refresh-btn').on('click', function() {
            // Cambia il testo del pulsante durante l'aggiornamento
            $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiornamento...');
            $(this).prop('disabled', true);
            
            // Esegue l'aggiornamento
            updateAdminStats();
            
            // Ripristina il pulsante dopo 2 secondi
            setTimeout(() => {
                $(this).html('<i class="bi bi-arrow-clockwise me-1"></i>Aggiorna');
                $(this).prop('disabled', false);
            }, 2000);
        });
    }
    
    console.log('✅ Sistema di aggiornamento dashboard admin attivato');
});

/**
 * FUNZIONI HELPER GLOBALI
 */

/**
 * Formatta i numeri con separatori di migliaia in formato italiano
 * @param {number} num - Numero da formattare
 * @returns {string} - Numero formattato (es: 1.234)
 */
function formatNumber(num) {
    return new Intl.NumberFormat('it-IT').format(num);
}

/**
 * Mostra notificazioni toast Bootstrap personalizzate
 * @param {string} message - Messaggio da mostrare
 * @param {string} type - Tipo di notifica (success, warning, danger, info)
 */
function showNotification(message, type = 'success') {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" 
             role="alert" style="z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    
    // Inizializza e mostra il toast con Bootstrap
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Rimuove automaticamente il toast dopo 5 secondi
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

/**
 * Gestisce gli errori AJAX in modo centralizzato
 * @param {Object} xhr - Oggetto XMLHttpRequest dell'errore
 * @param {string} context - Contesto dell'errore per il logging
 */
function handleAjaxError(xhr, context) {
    console.error(`❌ Errore AJAX in ${context}:`, xhr.responseText);
    
    let message = 'Si è verificato un errore durante il caricamento dei dati.';
    
    // Personalizza il messaggio in base al codice di stato HTTP
    switch(xhr.status) {
        case 403:
            message = 'Non hai i permessi per accedere a questi dati.';
            break;
        case 404:
            message = 'Risorsa non trovata.';
            break;
        case 500:
            message = 'Errore interno del server.';
            break;
        case 0:
            message = 'Problemi di connessione. Controlla la tua connessione internet.';
            break;
    }
    
    showNotification(message, 'danger');
}

/**
 * Utility per il debugging - mostra le statistiche in console
 * @param {Object} stats - Oggetto statistiche da analizzare
 */
function debugStats(stats) {
    if (console && console.table) {
        console.group('📊 Debug Statistiche Dashboard');
        console.table(stats);
        console.groupEnd();
    }
}

/**
 * Verifica la connettività e lo stato dell'applicazione
 * Utile per diagnosticare problemi di rete o server
 */
function checkConnectivity() {
    return $.ajax({
        url: '/ping', // Endpoint semplice per test di connettività
        method: 'GET',
        timeout: 5000
    }).done(function() {
        console.log('✅ Connettività OK');
        return true;
    }).fail(function() {
        console.warn('⚠️ Problemi di connettività rilevati');
        showNotification('Problemi di connessione rilevati', 'warning');
        return false;
    });
}

// Event listener per gestire disconnessioni di rete
$(window).on('online', function() {
    showNotification('Connessione ripristinata', 'success');
    updateAdminStats(); // Aggiorna i dati quando la connessione torna
});

$(window).on('offline', function() {
    showNotification('Connessione persa. I dati potrebbero non essere aggiornati.', 'warning');
});

/**
 * Funzione di inizializzazione da chiamare quando la pagina è pronta
 * Configura tutti i listener e le impostazioni iniziali
 */
function initAdminDashboard() {
    // Verifica che siamo effettivamente nella dashboard admin
    if (!$('body').hasClass('admin-dashboard') && !$('#admin-dashboard-container').length) {
        console.warn('⚠️ Script caricato in pagina non-admin');
        return;
    }
    
    // Configurazioni globali per AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr, status, error) {
            handleAjaxError(xhr, 'Request generico');
        }
    });
    
    // Avvia controllo connettività iniziale
    checkConnectivity();
    
    console.log('🚀 Dashboard Admin completamente inizializzata');
}

// Inizializza quando il documento è pronto
$(document).ready(initAdminDashboard);
</script>

{{-- 
    Stili CSS personalizzati per la dashboard admin
    Questi stili migliorano l'aspetto visivo e l'usabilità
--}}
<style>
/* Stili per i badge dei livelli utente */
.badge-livello {
    font-size: 0.75em;
    font-weight: 600;
}

.badge-livello-1 { background-color: #6c757d !important; }
.badge-livello-2 { background-color: #0dcaf0 !important; }
.badge-livello-3 { background-color: #ffc107 !important; color: #000 !important; }
.badge-livello-4 { background-color: #dc3545 !important; }

/* Stili per le card personalizzate */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Effetti hover per i pulsanti di gestione */
.btn-lg:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Stili per gli indicatori di stato */
.badge-success { background-color: #198754 !important; }
.badge-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge-danger { background-color: #dc3545 !important; }
.badge-info { background-color: #0dcaf0 !important; }

/* Animazioni per gli aggiornamenti */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.updating {
    animation: pulse 1s infinite;
}

/* Responsive design per schermi piccoli */
@media (max-width: 768px) {
    .col-lg-4, .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .display-6 {
        font-size: 2rem !important;
    }
}

/* Stili per il contenitore principale */
.container {
    max-width: 1400px;
}

/* Miglioramenti per l'accessibilità */
.btn:focus,
.list-group-item:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Indicatori di caricamento */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 0.125em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Toast personalizzati */
.toast {
    min-width: 300px;
}

/* Stili per le liste di elementi */
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

/* Miglioramenti tipografici */
.fw-semibold {
    font-weight: 600;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Effetti di transizione fluidi */
.card, .btn, .badge {
    transition: all 0.2s ease-in-out;
}
</style>
@endpush