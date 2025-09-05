{{-- Vista dettagli utente per amministratori --}}
@extends('layouts.app')

@section('title', 'Dettagli Utente - ' . $user->nome_completo)

@section('content')
<div class="container mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="bi bi-house-door me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                    <i class="bi bi-people me-1"></i>Gestione Utenti
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-eye me-1"></i>{{ $user->nome_completo }}
            </li>
        </ol>
    </nav>

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-{{ $user->livello_accesso == '4' ? 'danger' : ($user->livello_accesso == '3' ? 'warning' : ($user->livello_accesso == '2' ? 'info' : 'secondary')) }} text-white me-3">
                        {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="h2 mb-1">{{ $user->nome_completo }}</h1>
                        <p class="text-muted mb-0">
                            <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }} me-2">
                                {{ $user->livello_descrizione }}
                            </span>
                            @if($user->username)
                                <code>{{ $user->username }}</code>
                            @endif
                        </p>
                    </div>
                </div>
                <div>
                    @if($user->id !== auth()->id())
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Modifica
                        </a>
                        <div class="btn-group ms-2" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {{-- Reset Password --}}
                                <li>
                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item" onclick="return confirm('Resettare la password per {{ $user->nome_completo }}?')">
                                            <i class="bi bi-key me-2"></i>Reset Password
                                        </button>
                                    </form>
                                </li>
                                
                                {{-- Visualizza Dettagli Aggiuntivi --}}
                                @if($user->isStaff())
                                    <li>
                                        <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $user->id]) }}" class="dropdown-item">
                                            <i class="bi bi-box-seam me-2"></i>Gestisci Prodotti
                                        </a>
                                    </li>
                                @endif
                                
                                {{-- Separatore prima dell'eliminazione --}}
                                <li><hr class="dropdown-divider"></li>
                                
                                {{-- Eliminazione Account --}}
                                <li>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="dropdown-item text-danger" 
                                                onclick="return confirm('ATTENZIONE: Eliminare definitivamente {{ $user->nome_completo }}?\n\nQuesta azione non può essere annullata e rimuoverà anche tutti i dati associati.')">
                                            <i class="bi bi-trash me-2"></i>Elimina Account
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info me-2">Il tuo account</span>
                            <a href="{{ route('profilo') }}" class="btn btn-outline-primary">
                                <i class="bi bi-gear me-1"></i>Modifica Profilo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === INFORMAZIONI PRINCIPALI === -->
        <div class="col-lg-8">
            
            <!-- Informazioni Personali -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person text-primary me-2"></i>
                        Informazioni Personali
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold text-muted">Nome:</td>
                                    <td>{{ $user->nome }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Cognome:</td>
                                    <td>{{ $user->cognome }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Username:</td>
                                    <td><code>{{ $user->username }}</code></td>
                                </tr>
                                @if($user->data_nascita)
                                    <tr>
                                        <td class="fw-semibold text-muted">Data Nascita:</td>
                                        <td>{{ $user->data_nascita->format('d/m/Y') }} ({{ $user->data_nascita->age }} anni)</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold text-muted">Livello Accesso:</td>
                                    <td>
                                        <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }}">
                                            {{ $user->livello_descrizione }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Registrato il:</td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Ultimo Aggiornamento:</td>
                                    <td>{{ $user->updated_at->diffForHumans() }}</td>
                                </tr>
                                @if($user->last_login_at)
                                    <tr>
                                        <td class="fw-semibold text-muted">Ultimo Accesso:</td>
                                        <td>{{ $user->last_login_at->diffForHumans() }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informazioni Specifiche per Ruolo -->
            @if($user->isTecnico())
                <div class="card card-custom mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-tools me-2"></i>
                            Informazioni Tecnico
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info mb-3">Centro di Assistenza</h6>
                                @if($user->centroAssistenza)
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="mb-1">{{ $user->centroAssistenza->nome }}</h6>
                                        <p class="text-muted mb-1">{{ $user->centroAssistenza->indirizzo_completo }}</p>
                                        @if($user->centroAssistenza->telefono)
                                            <small><i class="bi bi-telephone me-1"></i>{{ $user->centroAssistenza->telefono }}</small>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Nessun centro di assistenza assegnato
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info mb-3">Specializzazione</h6>
                                @if($user->specializzazione)
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="mb-0">{{ $user->specializzazione }}</h6>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Specializzazione non specificata
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isStaff())
                <div class="card card-custom mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>
                            Prodotti Assegnati
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($user->prodottiAssegnati && $user->prodottiAssegnati->count() > 0)
                            <div class="row">
                                @foreach($user->prodottiAssegnati as $prodotto)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $prodotto->foto_url }}" 
                                                         class="rounded me-3" 
                                                         style="width: 50px; height: 50px; object-fit: contain; background-color: #f8f9fa;"
                                                         alt="{{ $prodotto->nome }}"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="rounded me-3 d-none align-items-center justify-content-center bg-light" 
                                                         style="width: 50px; height: 50px; min-width: 50px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $prodotto->nome }}</h6>
                                                        <small class="text-muted">{{ $prodotto->modello }}</small>
                                                        <div>
                                                            <span class="badge bg-secondary">{{ $prodotto->categoria_label }}</span>
                                                            @if($prodotto->malfunzionamenti_count > 0)
                                                                <span class="badge bg-warning">{{ $prodotto->malfunzionamenti_count }} problemi</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Nessun prodotto assegnato a questo membro dello staff
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Attività Recente -->
            @if(isset($attivitaRecente))
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-activity text-success me-2"></i>
                            Attività Recente
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($user->isStaff() && isset($attivitaRecente['ultime_soluzioni']) && $attivitaRecente['ultime_soluzioni']->count() > 0)
                            <h6 class="text-success mb-3">Ultime Soluzioni Create</h6>
                            <div class="list-group list-group-flush">
                                @foreach($attivitaRecente['ultime_soluzioni'] as $soluzione)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $soluzione->titolo }}</h6>
                                                <p class="mb-1 text-muted">{{ Str::limit($soluzione->descrizione, 100) }}</p>
                                                <small>Prodotto: {{ $soluzione->prodotto->nome ?? 'N/A' }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-{{ $soluzione->gravita == 'critica' ? 'danger' : ($soluzione->gravita == 'alta' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($soluzione->gravita) }}
                                                </span>
                                                <div><small class="text-muted">{{ $soluzione->created_at->diffForHumans() }}</small></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Nessuna attività recente da visualizzare</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- === SIDEBAR STATISTICHE === -->
        <div class="col-lg-4">
            
            <!-- Statistiche Generali -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistiche
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @if($user->isStaff())
                            <div class="col-6">
                                <div class="p-3 bg-warning bg-opacity-10 rounded mb-3">
                                    <i class="bi bi-box text-warning fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $stats['prodotti_assegnati'] ?? 0 }}</h4>
                                    <small class="text-muted">Prodotti Assegnati</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 rounded mb-3">
                                    <i class="bi bi-check-circle text-success fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $stats['malfunzionamenti_creati'] ?? 0 }}</h4>
                                    <small class="text-muted">Soluzioni Create</small>
                                </div>
                            </div>
                            @if(isset($stats['soluzioni_critiche']))
                                <div class="col-12">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $stats['soluzioni_critiche'] }}</h4>
                                        <small class="text-muted">Problemi Critici Risolti</small>
                                    </div>
                                </div>
                            @endif
                        @elseif($user->isTecnico())
                            <div class="col-12">
                                <div class="p-3 bg-info bg-opacity-10 rounded mb-3">
                                    <i class="bi bi-tools text-info fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $user->livello_descrizione }}</h4>
                                    <small class="text-muted">Tecnico Specializzato</small>
                                </div>
                            </div>
                            @if($user->centroAssistenza)
                                <div class="col-12">
                                    <div class="p-3 bg-secondary bg-opacity-10 rounded">
                                        <i class="bi bi-building text-secondary fs-1"></i>
                                        <h4 class="mt-2 mb-1">{{ $user->centroAssistenza->nome }}</h4>
                                        <small class="text-muted">Centro Assegnato</small>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12">
                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                    <i class="bi bi-person text-info fs-1"></i>
                                    <h4 class="mt-2 mb-1">{{ $user->livello_descrizione }}</h4>
                                    <small class="text-muted">Ruolo Sistema</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informazioni Account -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Informazioni Account
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Livello Accesso:</span>
                        <span class="badge bg-info">Livello {{ $user->livello_accesso }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Account creato:</span>
                        <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    @if($user->last_login_at)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Ultimo Login:</span>
                            <span title="{{ $user->last_login_at->format('d/m/Y H:i') }}">
                                {{ $user->last_login_at->diffForHumans() }}
                            </span>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small>Non ha mai effettuato l'accesso</small>
                        </div>
                    @endif

                    {{-- Mostra sempre come attivo dato che non c'è più la funzione sospendi --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Stato Account:</span>
                        <span class="badge bg-success">Attivo</span>
                    </div>
                </div>
            </div>

            <!-- Azioni Rapide -->
            <div class="card card-custom">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Lista
                        </a>
                        
                        @if($user->id !== auth()->id())
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil me-1"></i>Modifica Utente
                            </a>
                            
                            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-sm w-100" onclick="return confirm('Resettare la password per {{ $user->nome_completo }}?\n\nVerrà generata una password temporanea.')">
                                    <i class="bi bi-key me-1"></i>Reset Password
                                </button>
                            </form>
                        @endif

                        @if($user->isStaff())
                            <a href="{{ route('admin.assegnazioni.index', ['staff_id' => $user->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-seam me-1"></i>Gestisci Prodotti
                            </a>
                        @endif

                        {{-- Link diretto per creare nuovo utente --}}
                        <hr class="my-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.canEditUser = {{ $user->id !== auth()->id() ? 'true' : 'false' }};
    window.editUserUrl = "{{ route('admin.users.edit', $user) }}";
    window.usersIndexUrl = "{{ route('admin.users.index') }}";

  window.userData = @json($user);

</script>
@endsection

@push('styles')
<style>
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-custom:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
    border: 3px solid rgba(255, 255, 255, 0.2);
}

.badge-livello {
    font-size: 0.75rem;
    padding: 0.5em 0.8em;
    border-radius: 0.375rem;
}

.badge-livello-4 { 
    background-color: #dc3545; 
    color: white;
}
.badge-livello-3 { 
    background-color: #ffc107; 
    color: #000; 
}
.badge-livello-2 { 
    background-color: #0dcaf0; 
    color: #000; 
}
.badge-livello-1 { 
    background-color: #6c757d; 
    color: white;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
    vertical-align: middle;
}

.table-borderless .fw-semibold {
    min-width: 140px;
    white-space: nowrap;
}

/* Miglioramenti per le card prodotti */
.card .card-body .d-flex img,
.card .card-body .d-flex .bg-light {
    border: 1px solid #e9ecef;
}

/* Miglioramenti responsive */
@media (max-width: 768px) {
    .avatar-circle {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .table-borderless .fw-semibold {
        min-width: auto;
        font-size: 0.9rem;
    }
}

/* Effetti hover per i pulsanti azioni */
.btn-outline-secondary:hover,
.btn-outline-info:hover,
.btn-outline-primary:hover,
.btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Stili per le statistiche */
.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.fs-1 {
    font-size: 2.5rem !important;
}
</style>
@endpush

@push('scripts')
<script>
  window.canEditUser = {{ $user->id !== auth()->id() ? 'true' : 'false' }};
  window.editUserUrl = "{{ route('admin.users.edit', $user) }}";
  window.usersIndexUrl = "{{ route('admin.users.index') }}";
</script>
<script>
// Inizializza i dati della pagina se non esistono già
window.PageData = window.PageData || {};

// Aggiungi dati specifici solo se necessari per questa view
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

@if(isset($user))
window.PageData.user = @json($user);
@endif

// Aggiungi altri dati che potrebbero servire...
</script>
@endpush