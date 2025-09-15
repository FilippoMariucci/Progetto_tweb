{{-- Vista per gestione utenti amministratori --}}
@extends('layouts.app')

@section('title', 'Gestione Utenti')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bi bi-people text-danger me-2"></i>
                        Gestione Utenti
                    </h1>
                    <p class="text-muted mb-0">
                        Amministra utenti, tecnici e staff del sistema
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-danger">
                        <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                    </a>
                </div>
            </div>
            
            <!-- Alert Informativo -->
            <div class="alert alert-info border-start border-info border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Gestione Completa:</strong> Crea, modifica ed elimina utenti. Gestisci livelli di accesso e assegnazioni.
            </div>
        </div>
    </div>

    <!-- === STATISTICHE RAPIDE === -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check display-6"></i>
                    <h4 class="mt-2">{{ $stats['admin'] ?? 0 }}</h4>
                    <small>Amministratori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear display-6"></i>
                    <h4 class="mt-2">{{ $stats['staff'] ?? 0 }}</h4>
                    <small>Staff</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-tools display-6"></i>
                    <h4 class="mt-2">{{ $stats['tecnici'] ?? 0 }}</h4>
                    <small>Tecnici</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h4 class="mt-2">{{ $stats['totale'] ?? 0 }}</h4>
                    <small>Totale Utenti</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FILTRI === -->
        <div class="col-lg-3">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel text-primary me-2"></i>
                        Filtri
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                        
                        <!-- Ricerca -->
                        <div class="mb-3">
                            <label for="search" class="form-label">
                                <i class="bi bi-search me-1"></i>Ricerca
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome, cognome o username">
                        </div>
                        
                        <!-- Livello Accesso -->
                        <div class="mb-3">
                            <label for="livello_accesso" class="form-label">
                                <i class="bi bi-shield me-1"></i>Livello Accesso
                            </label>
                            <select class="form-select" id="livello_accesso" name="livello_accesso">
                                <option value="">Tutti i livelli</option>
                                <option value="4" {{ request('livello_accesso') == '4' ? 'selected' : '' }}>
                                    🔴 Amministratori
                                </option>
                                <option value="3" {{ request('livello_accesso') == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale
                                </option>
                                <option value="2" {{ request('livello_accesso') == '2' ? 'selected' : '' }}>
                                    🔵 Tecnici
                                </option>
                                <option value="1" {{ request('livello_accesso') == '1' ? 'selected' : '' }}>
                                    ⚪ Utenti Pubblici
                                </option>
                            </select>
                        </div>
                        
                        <!-- Centro Assistenza -->
                        @if($centri->count() > 0)
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Centro Assistenza
                                </label>
                                <select class="form-select" id="centro_assistenza_id" name="centro_assistenza_id">
                                    <option value="">Tutti i centri</option>
                                    @foreach($centri as $centro)
                                        <option value="{{ $centro->id }}" {{ request('centro_assistenza_id') == $centro->id ? 'selected' : '' }}>
                                            {{ $centro->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        <!-- Data Registrazione -->
                        <div class="mb-3">
                            <label for="data_registrazione" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Registrati da
                            </label>
                            <select class="form-select" id="data_registrazione" name="data_registrazione">
                                <option value="">Tutte le date</option>
                                <option value="oggi" {{ request('data_registrazione') == 'oggi' ? 'selected' : '' }}>
                                    Oggi
                                </option>
                                <option value="settimana" {{ request('data_registrazione') == 'settimana' ? 'selected' : '' }}>
                                    Ultima settimana
                                </option>
                                <option value="mese" {{ request('data_registrazione') == 'mese' ? 'selected' : '' }}>
                                    Ultimo mese
                                </option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Applica Filtri
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- === AZIONI RAPIDE === -->
            <div class="card card-custom mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>
                        Azioni Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Pulsante principale per creare nuovo utente -->
                        <a href="{{ route('admin.users.create') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-plus me-1"></i>Nuovo Utente
                        </a>
                        
                        <!-- Separatore -->
                        <hr class="my-2">
                        
                        <!-- Pulsante per esportare la lista utenti -->
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i>Esporta Lista
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- === LISTA UTENTI === -->
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-primary me-2"></i>
                        Utenti 
                        <span class="badge bg-secondary">{{ $users->total() }}</span>
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-down me-1"></i>Ordina
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'nome'])) }}">Nome A-Z</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => '-nome'])) }}">Nome Z-A</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'created_at'])) }}">Più Recenti</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => '-created_at'])) }}">Più Vecchi</a></li>
                            <li><a class="dropdown-item" href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'livello_accesso'])) }}">Livello Accesso</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utente</th>
                                        <th>Livello</th>
                                        <th>Centro/Specializzazione</th>
                                        <th>Ultimo Accesso</th>
                                        <th>Stato</th>
                                        <th width="150">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="user-row" data-user-id="{{ $user->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-{{ $user->livello_accesso == '4' ? 'danger' : ($user->livello_accesso == '3' ? 'warning' : ($user->livello_accesso == '2' ? 'info' : 'secondary')) }} text-white me-3">
                                                        {{ strtoupper(substr($user->nome, 0, 1) . substr($user->cognome, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $user->nome_completo }}</h6>
                                                        <small class="text-muted">{{ $user->username }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-livello badge-livello-{{ $user->livello_accesso }}">
                                                    {{ $user->livello_descrizione }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->isTecnico() && $user->centroAssistenza)
                                                    <div>
                                                        <strong>{{ $user->centroAssistenza->nome }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->specializzazione ?? 'N/A' }}</small>
                                                    </div>
                                                @elseif($user->isStaff())
                                                    <div>
                                                        <strong>Staff Aziendale</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->prodottiAssegnati()->count() }} prodotti</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->last_login_at)
                                                    <span title="{{ $user->last_login_at->format('d/m/Y H:i') }}">
                                                        {{ $user->last_login_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Mai</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->attivo ?? true)
                                                    <span class="badge bg-success">Attivo</span>
                                                @else
                                                    <span class="badge bg-danger">Sospeso</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Visualizza -->
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Visualizza">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Modifica -->
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="Modifica">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <!-- Azioni Dropdown -->
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if($user->id !== auth()->id())
                                                                {{-- Reset Password --}}
                                                                <li>
                                                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Resettare la password?')">
                                                                            <i class="bi bi-key me-2"></i>Reset Password
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                
                                                                {{-- Visualizza Dettagli --}}
                                                                <li>
                                                                    <a href="{{ route('admin.users.show', $user) }}" class="dropdown-item">
                                                                        <i class="bi bi-info-circle me-2"></i>Visualizza Dettagli
                                                                    </a>
                                                                </li>
                                                                
                                                                {{-- Separatore prima dell'eliminazione --}}
                                                                <li><hr class="dropdown-divider"></li>
                                                                
                                                                {{-- Eliminazione Utente --}}
                                                                <li>
                                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="dropdown-item text-danger" 
                                                                                onclick="return confirm('ATTENZIONE: Eliminare questo utente?\n\nQuesta azione non può essere annullata.')">
                                                                            <i class="bi bi-trash me-2"></i>Elimina Utente
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                {{-- Se è l'utente corrente --}}
                                                                <li><span class="dropdown-item text-muted">
                                                                    <i class="bi bi-person-check me-2"></i>Sei tu!
                                                                </span></li>
                                                                <li>
                                                                    
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginazione -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Mostrando {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} 
                                    di {{ $users->total() }} utenti
                                </small>
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Nessun utente trovato</h5>
                            <p class="text-muted">Prova a modificare i filtri di ricerca</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-danger">
                                <i class="bi bi-person-plus me-1"></i>Crea Primo Utente
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL EXPORT === -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-download me-2"></i>Esporta Lista Utenti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Form corretto che punta al metodo exportAll esistente -->
                <form action="{{ route('admin.export.all') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Formato Export:</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="csv">CSV (Excel)</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <!-- Alert informativo -->
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            L'export includerà tutti i dati del sistema (utenti, prodotti, malfunzionamenti e centri assistenza)
                        </small>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Scarica Export Completo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card-custom {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge-livello {
    font-size: 0.75rem;
}

.badge-livello-4 { background-color: #dc3545; } /* Admin */
.badge-livello-3 { background-color: #ffc107; color: #000; } /* Staff */
.badge-livello-2 { background-color: #0dcaf0; color: #000; } /* Tecnico */
.badge-livello-1 { background-color: #6c757d; } /* Pubblico */

/* Hover effects */
.user-row:hover {
    background-color: #f8f9fa;
}

.btn-group .dropdown-menu {
    min-width: 150px;
}
</style>
@endpush

@push('scripts')
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