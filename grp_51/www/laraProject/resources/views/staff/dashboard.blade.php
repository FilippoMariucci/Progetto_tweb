{{-- 
    Dashboard completa per lo staff aziendale (Livello 3)
    File: resources/views/staff/dashboard.blade.php
    
    Questa vista mostra il pannello di controllo per i membri dello staff aziendale
    Include statistiche, gestione malfunzionamenti, prodotti assegnati e strumenti di lavoro
--}}
@extends('layouts.app')

@section('title', 'Dashboard Staff Aziendale')

@section('content')
<div class="container-fluid mt-4" id="staff-dashboard-container">
    <div class="row">
        <div class="col-12">
            {{-- Header personalizzato per lo staff --}}
            <h1 class="h2 mb-4">
                <i class="bi bi-person-badge text-warning me-2"></i>
                Dashboard Staff Aziendale
            </h1>
            
            {{-- Benvenuto personalizzato per staff --}}
            <div class="alert alert-warning border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-badge display-6 text-warning me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">
                            Benvenuto, {{ auth()->user()->nome_completo ?? auth()->user()->nome . ' ' . auth()->user()->cognome }}!
                        </h4>
                        <p class="mb-0">
                            <span class="badge bg-warning text-dark">Staff Tecnico Aziendale</span>
                            <span class="badge bg-secondary ms-1">Livello 3</span>
                        </p>
                        <small class="text-muted">
                            Gestisci malfunzionamenti e soluzioni tecniche per i prodotti del sistema
                        </small>
                    </div>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard Staff</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- === STATISTICHE RAPIDE === --}}
        <div class="col-12">
            <div class="row g-3" id="statistiche-rapide">
                {{-- Statistiche caricate dinamicamente via AJAX --}}
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom h-100 border-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-wrench-adjustable display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Malfunzionamenti Gestiti</h5>
                            <h2 class="text-warning mb-0" id="stat-malfunzionamenti">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h2>
                            <small class="text-muted">Questo mese</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom h-100 border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                            <h5 class="card-title">Soluzioni Aggiunte</h5>
                            <h2 class="text-success mb-0" id="stat-soluzioni">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h2>
                            <small class="text-muted">Ultimi 30 giorni</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom h-100 border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-box-seam display-4 text-info mb-3"></i>
                            <h5 class="card-title">Prodotti Seguiti</h5>
                            <h2 class="text-info mb-0" id="stat-prodotti">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h2>
                            <small class="text-muted">Attualmente</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom h-100 border-danger">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle display-4 text-danger mb-3"></i>
                            <h5 class="card-title">Richieste Urgenti</h5>
                            <h2 class="text-danger mb-0" id="stat-urgenti">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </h2>
                            <small class="text-muted">Da risolvere</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === AZIONI PRINCIPALI === --}}
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-tools me-2"></i>
                        Gestione Malfunzionamenti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        {{-- Gestione malfunzionamenti --}}
                        <div class="col-md-6">
                            <a href="{{ route('staff.malfunzionamenti.index') }}" class="btn btn-warning btn-lg w-100 h-100">
                                <i class="bi bi-list-check display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Gestisci Malfunzionamenti</span>
                                <br><small>Visualizza e modifica</small>
                            </a>
                        </div>
                        
                        {{-- Ricerca malfunzionamenti --}}
                        <div class="col-md-6">
                            <a href="{{ route('malfunzionamenti.ricerca') }}" class="btn btn-outline-warning btn-lg w-100 h-100">
                                <i class="bi bi-search display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Ricerca Problemi</span>
                                <br><small>Trova soluzioni esistenti</small>
                            </a>
                        </div>
                        
                        {{-- Prodotti completi --}}
                        <div class="col-md-6">
                            <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-secondary btn-lg w-100 h-100">
                                <i class="bi bi-box-seam display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Catalogo Completo</span>
                                <br><small>Prodotti con malfunzionamenti</small>
                            </a>
                        </div>
                        
                        {{-- Statistiche staff --}}
                        <div class="col-md-6">
                            <a href="{{ route('staff.statistiche') }}" class="btn btn-outline-info btn-lg w-100 h-100">
                                <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                                <span class="fw-semibold">Le Mie Statistiche</span>
                                <br><small>Attività e performance</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === SIDEBAR INFORMAZIONI === --}}
        <div class="col-lg-4">
            {{-- Attività recenti --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Attività Recenti
                    </h6>
                </div>
                <div class="card-body" id="attivita-recenti">
                    <div class="text-center py-3">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Caricamento...</span>
                        </div>
                        <p class="mt-2 text-muted">Caricamento attività...</p>
                    </div>
                </div>
            </div>

            {{-- Prodotti più problematici --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        Prodotti Più Problematici
                    </h6>
                </div>
                <div class="card-body" id="prodotti-problematici">
                    <div class="text-center py-3">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Caricamento...</span>
                        </div>
                        <p class="mt-2 text-muted">Caricamento dati...</p>
                    </div>
                </div>
            </div>

            {{-- Azioni rapide --}}
            <div class="card card-custom">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-aggiungi-malfunzionamento">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nuovo Malfunzionamento
                        </button>
                        
                        <button type="button" class="btn btn-sm btn-outline-success" id="btn-ricerca-rapida">
                            <i class="bi bi-search me-1"></i>
                            Ricerca Rapida
                        </button>
                        
                        <button type="button" class="btn btn-sm btn-outline-info" id="btn-esporta-report">
                            <i class="bi bi-download me-1"></i>
                            Esporta Report
                        </button>
                        
                        <hr>
                        
                        <a href="{{ route('profilo') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-person me-1"></i>
                            Il Mio Profilo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- === SEZIONE MALFUNZIONAMENTI RECENTI === --}}
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-warning me-2"></i>
                        Malfunzionamenti Recenti
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-refresh-malfunzionamenti">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Aggiorna
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tabella-malfunzionamenti-recenti">
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Caricamento...</span>
                            </div>
                            <p class="mt-2 text-muted">Caricamento malfunzionamenti recenti...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === RICERCA RAPIDA === --}}
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-search text-primary me-2"></i>
                        Ricerca Rapida Malfunzionamenti
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-ricerca-rapida" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label for="ricerca-prodotto" class="form-label">Prodotto</label>
                                <input type="text" class="form-control" id="ricerca-prodotto" 
                                       placeholder="Nome prodotto...">
                            </div>
                            <div class="col-md-4">
                                <label for="ricerca-problema" class="form-label">Problema</label>
                                <input type="text" class="form-control" id="ricerca-problema" 
                                       placeholder="Descrizione problema...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-1"></i>
                                    Cerca
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div id="risultati-ricerca-rapida">
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-search display-4"></i>
                            <p class="mt-2">Utilizza la ricerca per trovare malfunzionamenti e soluzioni</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal per aggiungere nuovo malfunzionamento --}}
<div class="modal fade" id="modalNuovoMalfunzionamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nuovo Malfunzionamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-nuovo-malfunzionamento">
                    <div class="mb-3">
                        <label for="prodotto-select" class="form-label required">Prodotto</label>
                        <select class="form-select" id="prodotto-select" required>
                            <option value="">Seleziona un prodotto...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="titolo-malfunzionamento" class="form-label required">Titolo del Problema</label>
                        <input type="text" class="form-control" id="titolo-malfunzionamento" 
                               placeholder="Es: Lavatrice non si accende" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descrizione-malfunzionamento" class="form-label required">Descrizione Dettagliata</label>
                        <textarea class="form-control" id="descrizione-malfunzionamento" rows="4" 
                                  placeholder="Descrivi il problema in dettaglio..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="soluzione-malfunzionamento" class="form-label required">Soluzione Tecnica</label>
                        <textarea class="form-control" id="soluzione-malfunzionamento" rows="4" 
                                  placeholder="Descrivi la soluzione step-by-step..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Annulla
                </button>
                <button type="submit" form="form-nuovo-malfunzionamento" class="btn btn-warning">
                    <i class="bi bi-check-circle me-1"></i>
                    Salva Malfunzionamento
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JavaScript personalizzato per la dashboard staff --}}
@push('scripts')
<script>
/**
 * JavaScript per la Dashboard Staff - Sistema Assistenza Tecnica
 * 
 * Questo script gestisce:
 * - Caricamento dinamico delle statistiche via AJAX
 * - Ricerca rapida malfunzionamenti 
 * - Gestione modal nuovo malfunzionamento
 * - Aggiornamento dati in tempo reale
 * - Gestione errori e feedback utente
 */

$(document).ready(function() {
    console.log('🟡 Dashboard Staff - Inizializzazione...');
    
    // === CONFIGURAZIONE GLOBALE AJAX ===
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // === CARICAMENTO INIZIALE DATI ===
    caricaStatisticheStaff();
    caricaAttivitaRecenti();
    caricaProdottiProblematici();
    caricaMalfunzionamentiRecenti();
    caricaProdottiPerSelect();
    
    // === EVENT LISTENERS ===
    
    // Ricerca rapida
    $('#form-ricerca-rapida').on('submit', function(e) {
        e.preventDefault();
        eseguiRicercaRapida();
    });
    
    // Pulsante nuovo malfunzionamento
    $('#btn-aggiungi-malfunzionamento').on('click', function() {
        $('#modalNuovoMalfunzionamento').modal('show');
    });
    
    // Pulsante ricerca rapida
    $('#btn-ricerca-rapida').on('click', function() {
        $('#ricerca-prodotto').focus();
    });
    
    // Pulsante esporta report
    $('#btn-esporta-report').on('click', function() {
        esportaReport();
    });
    
    // Form nuovo malfunzionamento
    $('#form-nuovo-malfunzionamento').on('submit', function(e) {
        e.preventDefault();
        salvaNuovoMalfunzionamento();
    });
    
    // Refresh malfunzionamenti
    $('#btn-refresh-malfunzionamenti').on('click', function() {
        caricaMalfunzionamentiRecenti();
    });
    
    // Auto-refresh ogni 5 minuti
    setInterval(function() {
        caricaStatisticheStaff();
        caricaAttivitaRecenti();
    }, 300000);
});

/**
 * Carica le statistiche generali dello staff via AJAX
 */
function caricaStatisticheStaff() {
    $.ajax({
        url: '/api/staff/stats',
        method: 'GET',
        success: function(data) {
            // Aggiorna i contatori nelle card statistiche
            $('#stat-malfunzionamenti').html(data.malfunzionamenti_gestiti || '0');
            $('#stat-soluzioni').html(data.soluzioni_aggiunte || '0');
            $('#stat-prodotti').html(data.prodotti_seguiti || '0');
            $('#stat-urgenti').html(data.richieste_urgenti || '0');
            
            console.log('✅ Statistiche staff caricate');
        },
        error: function() {
            // In caso di errore, mostra valori di fallback
            $('#stat-malfunzionamenti, #stat-soluzioni, #stat-prodotti, #stat-urgenti')
                .html('<i class="bi bi-exclamation-triangle text-danger" title="Errore caricamento"></i>');
            
            showAlert('Attenzione', 'Impossibile caricare le statistiche.', 'warning');
        }
    });
}

/**
 * Carica le attività recenti dello staff
 */
function caricaAttivitaRecenti() {
    $.ajax({
        url: '/api/staff/ultime-soluzioni',
        method: 'GET',
        success: function(data) {
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(attivita) {
                    const dataFormatted = new Date(attivita.created_at).toLocaleDateString('it-IT');
                    html += `
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-warning rounded-circle p-2 me-3">
                                <i class="bi bi-wrench text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${attivita.title}</h6>
                                <p class="text-muted mb-0 small">
                                    ${attivita.prodotto_nome} - ${dataFormatted}
                                </p>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-clock-history display-4"></i>
                        <p class="mt-2">Nessuna attività recente</p>
                    </div>
                `;
            }
            
            $('#attivita-recenti').html(html);
        },
        error: function() {
            $('#attivita-recenti').html(`
                <div class="text-center text-danger py-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p class="mt-2">Errore nel caricamento</p>
                </div>
            `);
        }
    });
}

/**
 * Carica i prodotti più problematici
 */
function caricaProdottiProblematici() {
    $.ajax({
        url: '/api/staff/malfunzionamenti-prioritari',
        method: 'GET',
        success: function(data) {
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(prodotto, index) {
                    const badge = index === 0 ? 'danger' : index === 1 ? 'warning' : 'secondary';
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0">${prodotto.nome}</h6>
                                <small class="text-muted">${prodotto.categoria}</small>
                            </div>
                            <span class="badge bg-${badge}">${prodotto.malfunzionamenti_count}</span>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-check-circle display-4 text-success"></i>
                        <p class="mt-2">Ottimo lavoro!<br>Nessun prodotto problematico</p>
                    </div>
                `;
            }
            
            $('#prodotti-problematici').html(html);
        },
        error: function() {
            $('#prodotti-problematici').html(`
                <div class="text-center text-danger py-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p class="mt-2">Errore nel caricamento</p>
                </div>
            `);
        }
    });
}

/**
 * Carica i malfunzionamenti recenti in una tabella
 */
function caricaMalfunzionamentiRecenti() {
    $('#tabella-malfunzionamenti-recenti').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Caricamento...</span>
            </div>
            <p class="mt-2 text-muted">Aggiornamento malfunzionamenti...</p>
        </div>
    `);
    
    $.ajax({
        url: '/api/staff/ultime-soluzioni?limit=10',
        method: 'GET',
        success: function(data) {
            let html = `
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Prodotto</th>
                            <th>Problema</th>
                            <th>Data</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            if (data.length > 0) {
                data.forEach(function(item) {
                    const dataFormatted = new Date(item.created_at).toLocaleDateString('it-IT');
                    html += `
                        <tr>
                            <td>
                                <strong>${item.prodotto_nome}</strong>
                                <br><small class="text-muted">${item.prodotto_categoria || 'N/A'}</small>
                            </td>
                            <td>
                                ${item.title}
                                <br><small class="text-muted">${item.description.substring(0, 50)}...</small>
                            </td>
                            <td>${dataFormatted}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="visualizzaMalfunzionamento(${item.id})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="modificaMalfunzionamento(${item.id})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Nessun malfunzionamento trovato</p>
                        </td>
                    </tr>
                `;
            }
            
            html += `
                    </tbody>
                </table>
            `;
            
            $('#tabella-malfunzionamenti-recenti').html(html);
        },
        error: function() {
            $('#tabella-malfunzionamenti-recenti').html(`
                <div class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle display-4"></i>
                    <p class="mt-2">Errore nel caricamento dei malfunzionamenti</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="caricaMalfunzionamentiRecenti()">
                        Riprova
                    </button>
                </div>
            `);
        }
    });
}

/**
 * Carica la lista prodotti per la select del modal
 */
function caricaProdottiPerSelect() {
    $.ajax({
        url: '/api/prodotti',
        method: 'GET',
        success: function(data) {
            let options = '<option value="">Seleziona un prodotto...</option>';
            
            data.forEach(function(prodotto) {
                options += `<option value="${prodotto.id}">${prodotto.nome} - ${prodotto.categoria}</option>`;
            });
            
            $('#prodotto-select').html(options);
        },
        error: function() {
            $('#prodotto-select').html('<option value="">Errore caricamento prodotti</option>');
        }
    });
}

/**
 * Esegue la ricerca rapida di malfunzionamenti
 */
function eseguiRicercaRapida() {
    const prodotto = $('#ricerca-prodotto').val().trim();
    const problema = $('#ricerca-problema').val().trim();
    
    if (!prodotto && !problema) {
        showAlert('Attenzione', 'Inserisci almeno un criterio di ricerca', 'warning');
        return;
    }
    
    $('#risultati-ricerca-rapida').html(`
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Ricerca in corso...</span>
            </div>
            <p class="mt-2 text-muted">Ricerca in corso...</p>
        </div>
    `);
    
    $.ajax({
        url: '/api/malfunzionamenti/search',
        method: 'GET',
        data: {
            prodotto: prodotto,
            problema: problema
        },
        success: function(data) {
            let html = '';
            
            if (data.length > 0) {
                html = `
                    <div class="row g-3">
                `;
                
                data.forEach(function(item) {
                    html += `
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">${item.prodotto_nome}</h6>
                                    <h6 class="card-subtitle mb-2 text-muted">${item.title}</h6>
                                    <p class="card-text small">${item.description.substring(0, 100)}...</p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary" onclick="visualizzaMalfunzionamento(${item.id})">
                                            <i class="bi bi-eye me-1"></i>Visualizza
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="modificaMalfunzionamento(${item.id})">
                                            <i class="bi bi-pencil me-1"></i>Modifica
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += `</div>`;
            } else {
                html = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-search display-4"></i>
                        <p class="mt-2">Nessun risultato trovato per la ricerca</p>
                        <small>Prova con altri termini di ricerca</small>
                    </div>
                `;
            }
            
            $('#risultati-ricerca-rapida').html(html);
        },
        error: function() {
            $('#risultati-ricerca-rapida').html(`
                <div class="text-center text-danger py-3">
                    <i class="bi bi-exclamation-triangle display-4"></i>
                    <p class="mt-2">Errore nella ricerca</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="eseguiRicercaRapida()">
                        Riprova
                    </button>
                </div>
            `);
        }
    });
}

/**
 * Salva un nuovo malfunzionamento tramite il modal
 */
function salvaNuovoMalfunzionamento() {
    const prodottoId = $('#prodotto-select').val();
    const titolo = $('#titolo-malfunzionamento').val().trim();
    const descrizione = $('#descrizione-malfunzionamento').val().trim();
    const soluzione = $('#soluzione-malfunzionamento').val().trim();
    
    // Validazione client-side
    if (!prodottoId || !titolo || !descrizione || !soluzione) {
        showAlert('Errore', 'Tutti i campi sono obbligatori', 'danger');
        return;
    }
    
    // Disabilita il pulsante di salvataggio durante la richiesta
    const btnSalva = $('button[form="form-nuovo-malfunzionamento"]');
    const originalText = btnSalva.html();
    btnSalva.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Salvataggio...');
    
    $.ajax({
        url: `/staff/prodotti/${prodottoId}/malfunzionamenti`,
        method: 'POST',
        data: {
            title: titolo,
            description: descrizione,
            solution: soluzione
        },
        success: function(response) {
            // Chiudi il modal
            $('#modalNuovoMalfunzionamento').modal('hide');
            
            // Reset del form
            $('#form-nuovo-malfunzionamento')[0].reset();
            
            // Mostra messaggio di successo
            showAlert('Successo', 'Malfunzionamento creato con successo!', 'success');
            
            // Ricarica i dati della dashboard
            caricaMalfunzionamentiRecenti();
            caricaStatisticheStaff();
        },
        error: function(xhr) {
            let message = 'Errore durante il salvataggio';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showAlert('Errore', message, 'danger');
        },
        complete: function() {
            // Ripristina il pulsante
            btnSalva.prop('disabled', false).html(originalText);
        }
    });
}

/**
 * Visualizza i dettagli di un malfunzionamento specifico
 */
function visualizzaMalfunzionamento(id) {
    // Redirect alla pagina di dettaglio del malfunzionamento
    window.open(`/staff/malfunzionamenti/${id}`, '_blank');
}

/**
 * Apre la pagina di modifica di un malfunzionamento
 */
function modificaMalfunzionamento(id) {
    // Redirect alla pagina di modifica
    window.location.href = `/staff/malfunzionamenti/${id}/edit`;
}

/**
 * Esporta un report delle attività dello staff
 */
function esportaReport() {
    showAlert('Info', 'Generazione report in corso...', 'info');
    
    $.ajax({
        url: '/staff/export-report',
        method: 'POST',
        success: function(response) {
            if (response.download_url) {
                // Crea un link temporaneo per il download
                const link = document.createElement('a');
                link.href = response.download_url;
                link.download = response.filename || 'report_staff.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showAlert('Successo', 'Report generato e scaricato!', 'success');
            } else {
                showAlert('Attenzione', 'Report generato ma link di download non disponibile', 'warning');
            }
        },
        error: function() {
            showAlert('Errore', 'Impossibile generare il report', 'danger');
        }
    });
}

/**
 * Mostra un alert Bootstrap personalizzato
 */
function showAlert(title, message, type) {
    // Rimuove eventuali alert precedenti
    $('.alert-custom').remove();
    
    // Crea l'alert HTML
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show alert-custom" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Aggiunge l'alert al body
    $('body').append(alertHtml);
    
    // Rimozione automatica dopo 5 secondi
    setTimeout(function() {
        $('.alert-custom').alert('close');
    }, 5000);
}

/**
 * Gestisce gli errori AJAX in modo centralizzato
 */
function handleAjaxError(xhr, operation) {
    console.error(`Errore ${operation}:`, xhr);
    
    let message = 'Si è verificato un errore imprevisto';
    
    if (xhr.status === 401) {
        message = 'Sessione scaduta. Effettua nuovamente il login';
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    } else if (xhr.status === 403) {
        message = 'Non hai i permessi necessari per questa operazione';
    } else if (xhr.status === 404) {
        message = 'Risorsa non trovata';
    } else if (xhr.status >= 500) {
        message = 'Errore del server. Riprova più tardi';
    }
    
    showAlert('Errore', message, 'danger');
}

/**
 * Controlla la connettività e mostra eventuali problemi
 */
function checkConnectivity() {
    $.ajax({
        url: '/api/staff/stats',
        method: 'GET',
        timeout: 5000,
        success: function() {
            console.log('Connettività OK');
        },
        error: function(xhr, status, error) {
            if (status === 'timeout') {
                showAlert('Attenzione', 'Connessione lenta. I dati potrebbero non essere aggiornati.', 'warning');
            } else {
                console.warn('Problema di connettività:', error);
            }
        }
    });
}

/**
 * Funzione di inizializzazione da chiamare quando la pagina è pronta
 */
function initStaffDashboard() {
    // Verifica che siamo effettivamente nella dashboard staff
    if (!$('#staff-dashboard-container').length) {
        console.warn('Script caricato in pagina non-staff');
        return;
    }
    
    // Avvia controllo connettività
    checkConnectivity();
    
    console.log('Dashboard Staff completamente inizializzata');
}

// Inizializza quando il documento è pronto
$(document).ready(initStaffDashboard);
</script>
@endpush

{{-- CSS personalizzato per la dashboard staff --}}
@push('styles')
<style>
/* === STILI GENERALI DASHBOARD STAFF === */
body.staff-dashboard {
    background-color: #fef9e7; /* Sfondo leggermente giallo per staff */
}

/* Stili per le card personalizzate */
.card-custom {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.15s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Stili specifici per le card di statistica */
.border-warning {
    border-color: #ffc107 !important;
    border-width: 2px !important;
}

.border-success {
    border-color: #198754 !important;
    border-width: 2px !important;
}

.border-info {
    border-color: #0dcaf0 !important;
    border-width: 2px !important;
}

.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

/* Effetti hover per i pulsanti di azione */
.btn-lg:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

/* Stili per i badge personalizzati */
.badge {
    font-size: 0.75em;
    font-weight: 600;
}

/* Stili per le statistiche numeriche */
.card-body h2 {
    font-weight: 700;
    font-size: 2.5rem;
}

/* Stili per la tabella responsive */
.table-hover tbody tr:hover {
    background-color: rgba(255, 193, 7, 0.1);
}

/* Stili per il modal */
.modal-header.bg-warning {
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Stili per gli alert personalizzati */
.alert-custom {
    border-radius: 0.5rem;
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
}

/* Animazioni per i caricamenti */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.updating {
    animation: pulse 1s infinite;
}

/* Stili per i risultati di ricerca */
.card.border-primary {
    border-color: #0d6efd !important;
    border-width: 1px !important;
}

.card.border-primary:hover {
    border-width: 2px !important;
    transform: translateY(-1px);
}

/* Responsive design */
@media (max-width: 768px) {
    .col-lg-4, .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
    
    .card-body h2 {
        font-size: 2rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    /* Tabella più compatta su mobile */
    .table-responsive table {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .alert-custom {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        width: 100% !important;
        margin-bottom: 1rem;
    }
}

/* Focus migliorato per accessibilità */
.form-control:focus,
.form-select:focus,
.btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    border-color: #ffc107;
}

/* Stili per stati di loading */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Miglioramenti per la leggibilità */
.text-muted {
    color: #6c757d !important;
}

.card-subtitle {
    font-weight: 500;
}

/* Stili per le icone di stato */
.text-warning { color: #ffc107 !important; }
.text-success { color: #198754 !important; }
.text-info { color: #0dcaf0 !important; }
.text-danger { color: #dc3545 !important; }

/* Animazioni di entrata per gli elementi dinamici */
.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Stile per elementi interattivi */
.clickable {
    cursor: pointer;
    transition: all 0.2s ease;
}

.clickable:hover {
    background-color: rgba(255, 193, 7, 0.1);
}
</style>
@endpush