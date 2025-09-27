```blade
{{-- 
    === VISTA PRINCIPALE GESTIONE ASSEGNAZIONI PRODOTTI ===
    Linguaggio: Blade Template Engine (HTML + PHP)
    File: resources/views/admin/assegnazioni/index.blade.php
    Accesso: Solo livello 4 (Amministratori)
    Funzione: Interfaccia completa per gestire le assegnazioni dei prodotti ai membri dello staff
    Framework: Laravel con Bootstrap 5 per UI/UX
--}}

{{-- 
    Estende il layout principale dell'applicazione
    @extends: Direttiva Blade per ereditarietà template
    layouts.app: File base che contiene la struttura HTML comune
--}}
@extends('layouts.app')

{{-- 
    Definisce il titolo della pagina che apparirà nel tag <title>
    @section: Direttiva Blade per definire sezioni del layout
--}}
@section('title', 'Gestione Assegnazioni Prodotti')

{{-- 
    Inizia la sezione del contenuto principale della pagina
    Tutto il contenuto qui dentro verrà inserito nel layout principale
--}}
@section('content')
<div class="container mt-4">
    
    {{-- 
        === BREADCRUMB NAVIGATION ===
        Linguaggio: HTML con Bootstrap
        Funzione: Navigazione gerarchica per orientamento utente
        Accessibilità: aria-label per screen readers
    --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            {{-- 
                Ogni breadcrumb item utilizza route() helper Laravel
                route(): Helper per generare URL basati sui nomi delle route
            --}}
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item active">Assegnazioni Prodotti</li>
        </ol>
    </nav>

    {{-- 
        === HEADER PRINCIPALE ===
        Contiene titolo, descrizione e informazioni generali della pagina
    --}}
    <div class="row mb-4">
        {{-- 
            Link per favicon nella sezione head
            Dovrebbe essere spostato nel layout principale per migliori pratiche
        --}}
        <link rel="icon" type="image/png" href="favicon.png">
        
        <div class="col-12">
            {{-- 
                Flexbox per allineamento orizzontale tra titolo e eventuali azioni
                Bootstrap classes: d-flex, align-items-center, justify-content-between
            --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    {{-- 
                        Titolo principale con icona Bootstrap Icons
                        h2: Classe Bootstrap per dimensioni consistenti
                        bi-person-gear: Icona specifica per gestione utenti/staff
                    --}}
                    <h1 class="h2 mb-1">
                        <i class="bi bi-person-gear text-warning me-2"></i>
                        Gestione Assegnazioni Prodotti
                    </h1>
                    {{-- 
                        Sottotitolo descrittivo per spiegare lo scopo della pagina
                        text-muted: Classe Bootstrap per testo secondario
                    --}}
                    <p class="text-muted mb-0">
                        Assegna prodotti ai membri dello staff per la gestione dei malfunzionamenti
                    </p>
                </div>
            </div>
            
            {{-- 
                === ALERT INFORMATIVO ===
                Bootstrap Alert per comunicare informazioni importanti
                border-start: Bordo colorato a sinistra per enfasi visiva
            --}}
            <div class="alert alert-info border-start border-info border-4">
                <div class="row">
                    <div class="col-md-8">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Funzionalità Opzionale:</strong> Ogni membro dello staff può gestire un sottoinsieme specifico di prodotti.
                    </div>
                    <div class="col-md-4 text-end">
                        {{-- 
                            Badge con statistiche rapide
                            $stats: Array di statistiche passato dal Controller
                            Accesso alle chiavi dell'array per visualizzare conteggi
                        --}}
                        <span class="badge bg-success">{{ $stats['prodotti_assegnati'] }}</span> Assegnati
                        <span class="badge bg-warning">{{ $stats['prodotti_non_assegnati'] }}</span> Non Assegnati
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 
        === SEZIONE STATISTICHE RAPIDE ===
        Dashboard cards per overview rapido delle metriche principali
        g-3: Gap Bootstrap per spaziatura tra colonne
    --}}
    <div class="row g-3 mb-4">
        {{-- 
            === CARD PRODOTTI TOTALI ===
            Linguaggio: HTML + Bootstrap + Blade
            Funzione: Visualizza il numero totale di prodotti nel sistema
        --}}
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    {{-- 
                        Icona grande per impatto visivo
                        display-6: Classe Bootstrap per dimensioni icone
                    --}}
                    <i class="bi bi-box display-6"></i>
                    {{-- 
                        Numero dalla statistica del Controller
                        $stats['totale_prodotti']: Totale prodotti nel database
                    --}}
                    <h4 class="mt-2">{{ $stats['totale_prodotti'] }}</h4>
                    <small>Prodotti Totali</small>
                </div>
            </div>
        </div>
        
        {{-- === CARD PRODOTTI ASSEGNATI === --}}
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_assegnati'] }}</h4>
                    <small>Assegnati</small>
                </div>
            </div>
        </div>
        
        {{-- === CARD PRODOTTI NON ASSEGNATI === --}}
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle display-6"></i>
                    <h4 class="mt-2">{{ $stats['prodotti_non_assegnati'] }}</h4>
                    <small>Non Assegnati</small>
                </div>
            </div>
        </div>
        
        {{-- === CARD STAFF ATTIVI === --}}
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h4 class="mt-2">{{ $stats['staff_attivi'] }}</h4>
                    <small>Staff Attivi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 
            === COLONNA SIDEBAR FILTRI ===
            Layout responsive: lg-3 (3 colonne su desktop), full-width su mobile
        --}}
        <div class="col-lg-3">
            {{-- 
                === CARD FILTRI ===
                Card personalizzata per contenere tutti i controlli di filtro
                card-custom: Classe CSS personalizzata definita nel @push('styles')
            --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        === FORM FILTRI ===
                        Linguaggio: HTML Form con Method GET
                        Funzione: Invia parametri di filtro come query string
                        Action: Route che punta alla stessa pagina per mantenere i filtri
                    --}}
                    <form method="GET" action="{{ route('admin.assegnazioni.index') }}" id="filterForm">
                        
                        {{-- 
                            === CAMPO RICERCA TESTUALE ===
                            Input text per cercare prodotti per nome o modello
                            value="{{ request('search') }}": Mantiene il valore inserito dopo submit
                            request(): Helper Laravel per ottenere parametri dalla richiesta HTTP
                        --}}
                        <div class="mb-3">
                            <label for="search" class="form-label">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome o modello prodotto">
                        </div>
                        
                        {{-- 
                            === FILTRO SELEZIONE STAFF ===
                            Select dropdown per filtrare prodotti per staff assegnato
                            Include opzione speciale per prodotti non assegnati
                        --}}
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">
                                <i class="bi bi-person me-1"></i>Membro Staff
                            </label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">Tutti gli staff</option>
                                {{-- 
                                    Opzione speciale per prodotti non assegnati
                                    value="null": Valore stringa per identificare prodotti senza assegnazione
                                    Controllo selected per mantenere selezione dopo submit
                                --}}
                                <option value="null" {{ request('staff_id') === 'null' ? 'selected' : '' }}>
                                    Non Assegnati
                                </option>
                                {{-- 
                                    === LOOP MEMBRI STAFF ===
                                    @foreach: Itera la collezione $staffMembers dal Controller
                                    Ogni membro diventa un'opzione nel select
                                --}}
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 
                            === FILTRO CATEGORIA PRODOTTO ===
                            Select per filtrare prodotti per categoria
                            $categorie: Array associativo (chiave => etichetta) dal Controller
                        --}}
                        <div class="mb-3">
                            <label for="categoria" class="form-label">
                                <i class="bi bi-tag me-1"></i>Categoria
                            </label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Tutte le categorie</option>
                                {{-- 
                                    Loop attraverso array categorie
                                    $key: Valore da inviare al server
                                    $label: Testo da mostrare all'utente
                                --}}
                                @foreach($categorie as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 
                            === CHECKBOX PRODOTTI NON ASSEGNATI ===
                            Checkbox per shortcut rapido ai prodotti senza assegnazione
                            Quando selezionato, si comporta come filtro "Non Assegnati"
                        --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="non_assegnati" 
                                       name="non_assegnati" 
                                       value="1"
                                       {{ request('non_assegnati') ? 'checked' : '' }}>
                                <label class="form-check-label" for="non_assegnati">
                                    Solo prodotti non assegnati
                                </label>
                            </div>
                        </div>
                        
                        {{-- 
                            === PULSANTI AZIONE FILTRI ===
                            d-grid gap-2: Layout Bootstrap per pulsanti impilati con spaziatura
                        --}}
                        <div class="d-grid gap-2">
                            {{-- 
                                Pulsante submit per applicare i filtri
                                type="submit": Invia il form con i parametri selezionati
                            --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Applica Filtri
                            </button>
                            {{-- 
                                Link per reset completo dei filtri
                                Reindirizza alla stessa route ma senza parametri GET
                            --}}
                            <a href="{{ route('admin.assegnazioni.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 
                === STAFF OVERVIEW CARD ===
                Widget separato per panoramica rapida dei membri staff
                Mostra ogni staff con conteggio prodotti assegnati
            --}}
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-info me-2"></i>
                        Staff Overview
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        === LOOP STAFF OVERVIEW ===
                        @forelse: Direttiva Blade per loop con fallback
                        Gestisce automaticamente il caso di collezione vuota
                    --}}
                    @forelse($staffMembers as $staff)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                            <div>
                                {{-- 
                                    Informazioni base del membro staff
                                    nome_completo: Accessor del Model che concatena nome e cognome
                                    username: Campo diretto dal database
                                --}}
                                <h6 class="mb-0">{{ $staff->nome_completo }}</h6>
                                <small class="text-muted">{{ $staff->username }}</small>
                            </div>
                            <div class="text-end">
                                {{-- 
                                    === BADGE CONTEGGIO PRODOTTI ===
                                    prodottiAssegnati(): Relazione Eloquent definita nel Model Staff
                                    count(): Metodo per contare i record correlati senza caricarli
                                --}}
                                <span class="badge bg-primary">
                                    {{ $staff->prodottiAssegnati()->count() }}
                                </span>
                                <div>
                                    {{-- 
                                        Link per filtrare la vista sui prodotti di questo staff
                                        Utilizza lo stesso route ma con parametro staff_id preimpostato
                                    --}}
                                    <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $staff->id]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- 
                            Fallback quando non ci sono membri staff
                            @empty: Alternativa di @forelse quando la collezione è vuota
                        --}}
                        <p class="text-muted text-center">Nessun membro staff disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 
            === COLONNA PRINCIPALE LISTA PRODOTTI ===
            Layout responsive: lg-9 (9 colonne su desktop), occupa resto spazio
        --}}
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Prodotti 
                        {{-- 
                            Badge con conteggio totale prodotti nella vista corrente
                            $prodotti->total(): Metodo Paginator per totale record
                        --}}
                        <span class="badge bg-secondary">{{ $prodotti->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    {{-- 
                        === CONTROLLO PRESENZA PRODOTTI ===
                        Condizione per mostrare tabella o messaggio vuoto
                        count(): Conta i prodotti nella pagina corrente
                    --}}
                    @if($prodotti->count() > 0)
                        {{-- 
                            === TABELLA PRODOTTI ===
                            table-responsive: Wrapper Bootstrap per scroll orizzontale su mobile
                            table-hover: Effetto hover sulle righe per migliore UX
                        --}}
                        <div class="table-responsive">
                            <table class="table table-hover">
                                {{-- 
                                    Intestazione tabella con colonne fisse
                                    table-light: Background leggermente colorato per header
                                --}}
                                <thead class="table-light">
                                    <tr>
                                        <th>Prodotto</th>
                                        <th>Categoria</th>
                                        <th>Staff Assegnato</th>
                                        <th>Problemi</th>
                                        <th width="200">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 
                                        === LOOP PRODOTTI ===
                                        Itera attraverso i prodotti della pagina corrente
                                        $prodotti: Oggetto LengthAwarePaginator dal Controller
                                    --}}
                                    @foreach($prodotti as $prodotto)
                                        <tr>
                                            {{-- 
                                                === COLONNA INFORMAZIONI PRODOTTO ===
                                                Layout con immagine + dettagli testuali
                                            --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- 
                                                        Immagine prodotto con dimensioni fisse
                                                        foto_url: Accessor del Model per URL immagine
                                                        object-fit: cover: CSS per mantenere proporzioni
                                                    --}}
                                                    <img src="{{ $prodotto->foto_url }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="{{ $prodotto->nome }}">
                                                    <div>
                                                        {{-- 
                                                            Nome e modello del prodotto
                                                            Attributi diretti dal Model Prodotto
                                                        --}}
                                                        <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                                                        <small class="text-muted">{{ $prodotto->modello }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- 
                                                === COLONNA CATEGORIA ===
                                                Badge per categoria del prodotto
                                            --}}
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{-- 
                                                        categoria_label: Accessor del Model che converte
                                                        il valore del database in etichetta leggibile
                                                    --}}
                                                    {{ $prodotto->categoria_label }}
                                                </span>
                                            </td>
                                            
                                            {{-- 
                                                === COLONNA STAFF ASSEGNATO ===
                                                Mostra info staff o stato non assegnato
                                            --}}
                                            <td>
                                                {{-- 
                                                    Controllo condizionale per presenza staff
                                                    staffAssegnato: Relazione Eloquent caricata
                                                --}}
                                                @if($prodotto->staffAssegnato)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-person-check text-success me-2"></i>
                                                        <div>
                                                            {{-- 
                                                                Informazioni del membro staff assegnato
                                                                Accesso tramite relazione Eloquent
                                                            --}}
                                                            <strong>{{ $prodotto->staffAssegnato->nome_completo }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $prodotto->staffAssegnato->username }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- 
                                                        Badge di warning per prodotti non assegnati
                                                        Stato visivamente distinto per attenzione admin
                                                    --}}
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Non Assegnato
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            {{-- 
                                                === COLONNA PROBLEMI/MALFUNZIONAMENTI ===
                                                Conteggio con evidenziazione problemi critici
                                            --}}
                                            <td>
                                                {{-- 
                                                    === CALCOLO STATISTICHE PROBLEMI ===
                                                    @php: Direttiva Blade per codice PHP inline
                                                    Funzione: Calcola conteggi per visualizzazione
                                                --}}
                                                @php
                                                    // Conta tutti i malfunzionamenti del prodotto
                                                    $problemiCount = $prodotto->malfunzionamenti->count();
                                                    // Conta solo i malfunzionamenti critici
                                                    $criticiCount = $prodotto->malfunzionamenti->where('gravita', 'critica')->count();
                                                @endphp
                                                
                                                <div class="text-center">
                                                    {{-- 
                                                        Visualizzazione condizionale basata su presenza problemi
                                                        Badge colorati per impatto visivo immediato
                                                    --}}
                                                    @if($problemiCount > 0)
                                                        <span class="badge bg-info">{{ $problemiCount }}</span>
                                                        {{-- Badge aggiuntivo per problemi critici --}}
                                                        @if($criticiCount > 0)
                                                            <span class="badge bg-danger">{{ $criticiCount }} critici</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-success">0</span>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            {{-- 
                                                === COLONNA AZIONI ===
                                                Pulsanti per operazioni sui prodotti
                                                btn-group: Bootstrap per raggruppamento pulsanti
                                            --}}
                                            <td>
                                                <div class="btn-group" role="group">
                                                    {{-- 
                                                        === PULSANTE ASSEGNA/MODIFICA ===
                                                        Apre il modal per gestire l'assegnazione
                                                        Data attributes per passare dati al JavaScript
                                                    --}}
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm assign-btn"
                                                            data-product-id="{{ $prodotto->id }}"
                                                            data-product-name="{{ $prodotto->nome }}"
                                                            data-current-staff="{{ $prodotto->staff_assegnato_id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#assignModal"
                                                            title="Assegna/Modifica Staff">
                                                        <i class="bi bi-person-plus"></i>
                                                    </button>
                                                    
                                                    {{-- 
                                                        === PULSANTE VISUALIZZA DETTAGLI ===
                                                        Link alla pagina di dettaglio del prodotto
                                                        route(): Helper Laravel con model binding
                                                    --}}
                                                    <a href="{{ route('admin.prodotti.show', $prodotto) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Visualizza Prodotto">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    {{-- 
                                                        === PULSANTE RIMOZIONE ASSEGNAZIONE ===
                                                        Visibile solo se il prodotto è attualmente assegnato
                                                        Form inline per invio immediato
                                                    --}}
                                                    @if($prodotto->staffAssegnato)
                                                        <form action="{{ route('admin.assegnazioni.prodotto') }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            {{-- 
                                                                Campi nascosti per rimozione assegnazione
                                                                staff_id vuoto = rimozione assegnazione
                                                            --}}
                                                            <input type="hidden" name="prodotto_id" value="{{ $prodotto->id }}">
                                                            <input type="hidden" name="staff_id" value="">
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Rimuovere l\'assegnazione?')"
                                                                    title="Rimuovi Assegnazione">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- 
                            === SEZIONE PAGINAZIONE ===
                            Layout per informazioni pagina + controlli navigazione
                            Utilizza il sistema di paginazione integrato di Laravel
                        --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                {{-- 
                                    Informazioni sulla pagina corrente
                                    firstItem(), lastItem(), total(): Metodi Paginator
                                    ?? 0: Fallback per pagine vuote
                                --}}
                                <small class="text-muted">
                                    Mostrando {{ $prodotti->firstItem() ?? 0 }} - {{ $prodotti->lastItem() ?? 0 }} 
                                    di {{ $prodotti->total() }} prodotti
                                </small>
                            </div>
                            <div>
                                {{-- 
                                    Link di paginazione automatici
                                    links(): Metodo Paginator che genera i link prev/next
                                    Mantiene automaticamente i parametri GET (filtri)
                                --}}
                                {{ $prodotti->links() }}
                            </div>
                        </div>
                    @else
                        {{-- 
                            === MESSAGGIO STATO VUOTO ===
                            Visualizzato quando nessun prodotto soddisfa i filtri
                            UX friendly con icona e suggerimenti
                        --}}
                        <div class="text-center py-5">
                            <i class="bi bi-box display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun prodotto trovato</h5>
                            <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    === MODAL ASSEGNAZIONE SINGOLA ===
    Modal Bootstrap riutilizzabile per assegnazione prodotti
    Viene popolato dinamicamente via JavaScript
--}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Assegna Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- 
                === FORM ASSEGNAZIONE ===
                Form POST per inviare dati al Controller Laravel
                Utilizza la stessa route per assegnazione e modifica
            --}}
            <form action="{{ route('admin.assegnazioni.prodotto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Campo nascosto per ID prodotto (popolato da JavaScript) --}}
                    <input type="hidden" id="assign-product-id" name="prodotto_id">
                    
                    {{-- Visualizzazione nome prodotto selezionato --}}
                    <div class="mb-3">
                        <label class="form-label">Prodotto:</label>
                        <div class="p-2 bg-light rounded">
                            <strong id="assign-product-name"></strong>
                        </div>
                    </div>
                    
                    {{-- 
                        === SELECT STAFF PER ASSEGNAZIONE ===
                        Dropdown popolato con tutti i membri staff disponibili
                    --}}
                    <div class="mb-3">
                        <label for="assign-staff-id" class="form-label">
                            <i class="bi bi-person me-1"></i>Assegna a Staff:
                        </label>
                        <select class="form-select" id="assign-staff-id" name="staff_id">
                            {{-- Opzione per rimuovere assegnazione esistente --}}
                            <option value="">Nessuna assegnazione</option>
                            {{-- 
                                Loop attraverso membri staff con conteggio prodotti
                                Mostra carico di lavoro corrente per decisioni informate
                            --}}
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->nome_completo }} ({{ $staff->prodottiAssegnati()->count() }} prodotti)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Seleziona un membro dello staff o lascia vuoto per rimuovere l'assegnazione
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- Pulsante annulla (chiude modal senza salvare) --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    {{-- Pulsante conferma (invia form al Controller) --}}
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check me-1"></i>Conferma Assegnazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Chiude la sezione content del layout --}}
@endsection

{{-- 
    === SEZIONE STILI CSS PERSONALIZZATI ===
    Linguaggio: CSS3
    @push: Direttiva Blade per aggiungere contenuto allo stack 'styles' del layout
    Funzione: Stili specifici per questa vista senza interferire con altre pagine
--}}
@push('styles')
<style>
/* 
 * === STILI CSS PERSONALIZZATI PER GESTIONE ASSEGNAZIONI ===
 * Linguaggio: CSS3 con approccio component-based
 * Filosofia: Override selettivo di Bootstrap per UX migliorata
 */

/* 
 * === CLASSE CARD PERSONALIZZATA ===
 * Funzione: Stile unificato per tutte le card della pagina
 * Sostituisce il design di default Bootstrap con look più moderno
 */
.card-custom {
    border: none; /* Rimuove bordo di default per look clean */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Ombra sottile per profondità */
    transition: all 0.3s ease; /* Transizione fluida per interazioni */
}

/* 
 * === STILI HEADER TABELLA ===
 * Funzione: Personalizza intestazioni tabelle per migliore leggibilità
 * Target: Elementi <th> dentro tabelle con classe .table
 */
.table th {
    border-top: none; /* Rimuove bordo superiore per design pulito */
    font-weight: 600; /* Semi-bold per enfasi senza essere troppo pesante */
    color: #495057; /* Grigio scuro per contrasto ottimale */
}

/* 
 * === STANDARDIZZAZIONE BADGE ===
 * Funzione: Dimensioni consistenti per tutti i badge
 * Assicura uniformità visiva in tutta l'interfaccia
 */
.badge {
    font-size: 0.75rem; /* Dimensione leggermente ridotta per eleganza */
}

/* 
 * === EFFETTO HOVER STAFF OVERVIEW ===
 * Funzione: Feedback visivo per elementi interattivi
 * Target: Elementi con background Bootstrap .bg-light
 */
.bg-light:hover {
    background-color: #e9ecef !important; /* Colore più scuro al passaggio mouse */
    transition: background-color 0.2s ease; /* Transizione rapida e fluida */
}

/* 
 * === RESPONSIVE DESIGN ===
 * Linguaggio: CSS Media Queries
 * Funzione: Ottimizzazioni per dispositivi mobili (viewport < 768px)
 * Filosofia: Mobile-first approach per migliore usabilità touch
 */
@media (max-width: 768px) {
    /* 
     * Ottimizzazione pulsanti per touch screen
     * Riduce dimensioni per adattarsi meglio a schermi piccoli
     */
    .btn-group .btn {
        padding: 0.25rem 0.5rem; /* Padding ridotto per conservare spazio */
        font-size: 0.875rem; /* Testo più piccolo ma ancora leggibile */
    }
    
    /* 
     * Ottimizzazione tabelle per mobile
     * Migliora leggibilità su schermi stretti
     */
    .table-responsive {
        font-size: 0.875rem; /* Riduce dimensione font per contenere più contenuto */
    }
}
</style>
@endpush

{{-- 
    === SEZIONE JAVASCRIPT ===
    Linguaggio: JavaScript ES6+
    @push: Direttiva Blade per aggiungere script allo stack 'scripts' del layout
    Funzione: Logica client-side specifica per questa vista
--}}
@push('scripts')
<script>
/*
 * === INIZIALIZZAZIONE NAMESPACE GLOBALE ===
 * Linguaggio: JavaScript Object Pattern
 * Funzione: Previene conflitti di variabili globali tra diverse viste
 * Pattern: Fail-safe initialization per compatibilità multi-vista
 */

// Inizializza oggetto globale se non esiste (pattern sicuro)
window.PageData = window.PageData || {};

/*
 * === GESTIONE MODAL ASSEGNAZIONE ===
 * Linguaggio: JavaScript con DOM API e Event Handling
 * Funzione: Popola dinamicamente il modal quando viene aperto
 * 
 * Workflow completo:
 * 1. Attende caricamento completo del DOM
 * 2. Seleziona tutti i pulsanti di assegnazione
 * 3. Aggiunge event listener a ogni pulsante
 * 4. Estrae dati dal pulsante cliccato via data attributes
 * 5. Popola i campi del modal con i dati estratti
 * 6. Preseleziona staff corrente se presente
 */

// Event listener per DOMContentLoaded - garantisce che tutti gli elementi sono disponibili
document.addEventListener('DOMContentLoaded', function() {
    
    /*
     * === SELEZIONE ELEMENTI INTERATTIVI ===
     * querySelectorAll: Seleziona tutti gli elementi con classe 'assign-btn'
     * Questi pulsanti sono presenti in ogni riga della tabella prodotti
     */
    const assignButtons = document.querySelectorAll('.assign-btn');
    
    /*
     * === AGGIUNTA EVENT LISTENERS ===
     * forEach: Itera su ogni pulsante per aggiungere comportamento
     * addEventListener: Registra handler per evento 'click'
     */
    assignButtons.forEach(button => {
        button.addEventListener('click', function() {
            
            /*
             * === ESTRAZIONE DATI DAL PULSANTE ===
             * this.dataset: Accede a tutti i data-* attributes dell'elemento
             * I data attributes sono impostati nel template Blade per ogni prodotto
             * 
             * Data attributes utilizzati:
             * - data-product-id: ID numerico univoco del prodotto
             * - data-product-name: Nome del prodotto per visualizzazione
             * - data-current-staff: ID dello staff attualmente assegnato (null se non assegnato)
             */
            const productId = this.dataset.productId;        // ID numerico del prodotto
            const productName = this.dataset.productName;    // Nome leggibile del prodotto
            const currentStaff = this.dataset.currentStaff;  // ID staff corrente o null
            
            /*
             * === POPOLAMENTO CAMPI MODAL ===
             * getElementById: Seleziona elementi del modal per ID specifico
             * Questi ID sono definiti nel template del modal
             */
            
            // Popola campo nascosto con ID prodotto (necessario per form submission)
            document.getElementById('assign-product-id').value = productId;
            
            // Mostra nome prodotto per conferma visiva utente
            document.getElementById('assign-product-name').textContent = productName;
            
            /*
             * === PRESELEZIONE STAFF NEL DROPDOWN ===
             * Logica condizionale per gestire staff già assegnato vs non assegnato
             * Migliora UX mostrando stato corrente nel modal
             */
            const staffSelect = document.getElementById('assign-staff-id');
            
            if (currentStaff && currentStaff !== 'null') {
                // Se c'è uno staff assegnato, preselezionalo nel dropdown
                staffSelect.value = currentStaff;
            } else {
                // Se non assegnato, lascia su "Nessuna assegnazione"
                staffSelect.value = '';
            }
        });
    });
});

/*
 * === AREA DI SVILUPPO FUTURO ===
 * Placeholder per funzionalità avanzate che potrebbero essere implementate:
 * 
 * OTTIMIZZAZIONE FORM FILTRI:
 * - Auto-submit quando cambiano i filtri dropdown
 * - Salvataggio preferenze filtri in localStorage
 * - Validazione client-side prima dell'invio
 * - Indicatori di caricamento durante richieste
 * - Filtri in tempo reale con debouncing per ricerca testuale
 * 
 * GESTIONE STATO AVANZATA:
 * - Tracking modifiche per undo/redo
 * - Sincronizzazione real-time con WebSockets
 * - Cache client-side per performance
 * - Analytics per utilizzo interfaccia
 */

/*
 * === OBJECT STATE MANAGEMENT ===
 * Linguaggio: JavaScript Object Pattern con Blade Template Integration
 * Funzione: Centralizza stato e configurazione della pagina
 * Utilizzo: Debug, analytics, funzionalità avanzate future
 */
window.Assegnazioni = {
    /*
     * === CONFIGURAZIONE COMPONENTI ===
     * Object literal con impostazioni dell'interfaccia
     * Centralizza ID e opzioni per manutenibilità
     */
    config: {
        modalId: 'assignModal',        // ID del modal di assegnazione
        formId: 'filterForm',          // ID del form filtri
        autoSaveFilters: true          // Flag per salvataggio automatico preferenze
    },
    
    /*
     * === STATO CORRENTE FILTRI ===
     * Traccia i filtri attualmente applicati utilizzando helper Laravel
     * Blade template syntax integrata con JavaScript per sincronizzazione
     * 
     * request(): Helper Laravel per parametri GET della richiesta corrente
     * ?? "": Operatore null coalescing per valori di default
     */
    currentFilters: {
        search: '{{ request("search") ?? "" }}',                    // Termine ricerca testuale
        staff_id: '{{ request("staff_id") ?? "" }}',                // ID staff selezionato
        categoria: '{{ request("categoria") ?? "" }}',              // Categoria filtrata
        non_assegnati: {{ request("non_assegnati") ? 'true' : 'false' }}  // Flag prodotti non assegnati
    },
    
    /*
     * === STATISTICHE PAGINA ===
     * Metriche dal Controller per monitoring e debug
     * $stats: Array di statistiche passato dal Controller Laravel
     * Utilizzo: Dashboard analytics, ottimizzazione performance, debug
     */
    stats: {
        totalProducts: {{ $stats['totale_prodotti'] }},             // Conteggio totale prodotti in DB
        assignedProducts: {{ $stats['prodotti_assegnati'] }},       // Prodotti con staff assegnato
        unassignedProducts: {{ $stats['prodotti_non_assegnati'] }}, // Prodotti senza assegnazione
        activeStaff: {{ $stats['staff_attivi'] }}                   // Numero membri staff attivi
    }
};

/*
 * === DEBUG E MONITORING ===
 * Linguaggio: JavaScript Console API
 * Funzione: Output informativo per sviluppo e debug
 * Ambiente: Utile in sviluppo, può essere rimosso/disabilitato in produzione
 * 
 * Console logging per verificare:
 * - Caricamento corretto dello script
 * - Integrità dei dati passati dal Controller
 * - Stato dei filtri applicati
 */
console.log('📋 Gestione Assegnazioni caricata - Script inizializzato correttamente');
console.log('📊 Statistiche dal Controller:', window.Assegnazioni.stats);
console.log('🔍 Filtri attualmente attivi:', window.Assegnazioni.currentFilters);

/*
 * === VERIFICA INTEGRITÀ DATI ===
 * Controlli di sanità per assicurare che i dati dal backend siano validi
 * Utile per debugging di problemi di integrazione frontend-backend
 */
if (window.Assegnazioni.stats.totalProducts === 0) {
    console.warn('⚠️ Nessun prodotto trovato nel database');
}

if (window.Assegnazioni.stats.activeStaff === 0) {
    console.warn('⚠️ Nessun membro staff attivo trovato');
}

/*
 * === PERFORMANCE MONITORING ===
 * Traccia tempo di caricamento per ottimizzazioni future
 * Può essere esteso per analytics più dettagliati
 */
console.log('⏱️ Script caricato in:', performance.now().toFixed(2), 'ms');
</script>
@endpush