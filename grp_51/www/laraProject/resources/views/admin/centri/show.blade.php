{{-- 
    File: resources/views/admin/centri/show.blade.php
    Descrizione: Pagina di visualizzazione dettagli centro con modal per assegnazione tecnici
    VERSIONE CORRETTA con meta CSRF e data attributes
--}}

@extends('admin.dashboard')

{{-- Titolo dinamico della pagina --}}
@section('title', 'Centro: ' . $centro->nome)

{{-- Meta tags aggiuntivi --}}
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Breadcrumb per la navigazione --}}
@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.centri.index') }}">
                <i class="bi bi-building"></i> Centri Assistenza
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="bi bi-eye"></i> {{ Str::limit($centro->nome, 40) }}
        </li>
    </ol>
</nav>
@endsection

{{-- Contenuto principale della pagina --}}
@section('content')
{{-- Data attributes nascosti per JavaScript --}}
<div class="d-none" 
     data-centro-id="{{ $centro->id }}"
     data-indirizzo="{{ $centro->indirizzo_completo }}"
     data-telefono="{{ $centro->telefono }}"
     data-email="{{ $centro->email }}">
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header della pagina --}}
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="h3 mb-2 text-primary">
                        <i class="bi bi-building-fill me-2"></i>
                        {{ $centro->nome }}
                    </h1>
                    
                    <div class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $centro->indirizzo_completo }}
                    </div>
                    
                    <div class="mt-2">
                        @if($centro->hasTecnici())
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Attivo con {{ $centro->numero_tecnici }} tecnici
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Nessun tecnico assegnato
                            </span>
                        @endif
                        
                        @if($centro->isAperto())
                            <span class="badge bg-success ms-1">
                                <i class="bi bi-clock me-1"></i>
                                Aperto ora
                            </span>
                        @else
                            <span class="badge bg-secondary ms-1">
                                <i class="bi bi-clock me-1"></i>
                                Chiuso
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="btn-group-vertical" role="group">
                    <a href="{{ route('admin.centri.edit', $centro) }}" 
                       class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>
                        Modifica
                    </a>
                    <a href="{{ route('admin.centri.index') }}" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>
                        Lista Centri
                    </a>
                    <a href="{{ $centro->google_maps_link }}" 
                       target="_blank" 
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-geo-alt me-1"></i>
                        Google Maps
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Colonna principale --}}
                <div class="col-lg-8">
                    {{-- Card informazioni centro --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Informazioni Centro
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        Localizzazione
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <strong>Indirizzo:</strong><br>
                                        {{ $centro->indirizzo }}
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-8">
                                            <strong>Città:</strong><br>
                                            {{ $centro->citta }}
                                        </div>
                                        <div class="col-4">
                                            <strong>Provincia:</strong><br>
                                            {{ strtoupper($centro->provincia) }}
                                        </div>
                                    </div>
                                    
                                    @if($centro->cap)
                                        <div class="mb-3">
                                            <strong>CAP:</strong><br>
                                            {{ $centro->cap }}
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm"
                                                onclick="copiaIndirizzo()"
                                                data-indirizzo="{{ $centro->indirizzo_completo }}">
                                            <i class="bi bi-clipboard me-1"></i>
                                            Copia Indirizzo
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="bi bi-telephone-fill me-1"></i>
                                        Contatti
                                    </h6>
                                    
                                    @if($centro->telefono)
                                        <div class="mb-3">
                                            <strong>Telefono:</strong><br>
                                            <a href="tel:{{ $centro->telefono }}" 
                                               class="text-decoration-none">
                                                {{ $centro->telefono_formattato }}
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-secondary btn-sm ms-2"
                                                    onclick="copiaTelefono()"
                                                    data-telefono="{{ $centro->telefono }}">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="mb-3 text-muted">
                                            <strong>Telefono:</strong><br>
                                            Non specificato
                                        </div>
                                    @endif
                                    
                                    @if($centro->email)
                                        <div class="mb-3">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:{{ $centro->email }}" 
                                               class="text-decoration-none">
                                                {{ $centro->email }}
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-secondary btn-sm ms-2"
                                                    onclick="copiaEmail()"
                                                    data-email="{{ $centro->email }}">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="mb-3 text-muted">
                                            <strong>Email:</strong><br>
                                            Non specificata
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <strong>Orari di Apertura:</strong><br>
                                        <small class="text-muted">
                                            Lun-Ven: {{ $centro->orario_apertura['lunedi_venerdi'] }}<br>
                                            Sabato: {{ $centro->orario_apertura['sabato'] }}<br>
                                            Domenica: {{ $centro->orario_apertura['domenica'] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Card tecnici assegnati --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>
                                Tecnici Assegnati ({{ $centro->numero_tecnici }})
                            </h5>
                            @if(auth()->user()->isAdmin())
                                <button type="button" 
                                        class="btn btn-light btn-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAssegnaTecnico">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Assegna Tecnico
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($centro->hasTecnici())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nome Completo</th>
                                                <th>Specializzazione</th>
                                                <th>Data Nascita</th>
                                                <th class="text-center">Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($centro->tecnici_con_specializzazioni as $tecnico)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                {{ strtoupper(substr($tecnico->nome, 0, 1) . substr($tecnico->cognome, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <strong>{{ $tecnico->nome_completo }}</strong><br>
                                                                <small class="text-muted">{{ $tecnico->username }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($tecnico->specializzazione)
                                                            <span class="badge bg-info">
                                                                {{ ucfirst($tecnico->specializzazione) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Non specificata</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($tecnico->data_nascita)
                                                            {{ $tecnico->data_nascita->format('d/m/Y') }}
                                                            <br><small class="text-muted">
                                                                {{ $tecnico->data_nascita->age }} anni
                                                            </small>
                                                        @else
                                                            <span class="text-muted">Non specificata</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.users.show', $tecnico) }}" 
                                                               class="btn btn-outline-primary"
                                                               title="Visualizza dettagli">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.users.edit', $tecnico) }}" 
                                                               class="btn btn-outline-warning"
                                                               title="Modifica tecnico">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-outline-danger"
                                                                    title="Rimuovi dal centro"
                                                                    onclick="rimuoviTecnico({{ $tecnico->id }}, '{{ addslashes($tecnico->nome_completo) }}')">
                                                                <i class="bi bi-person-dash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if(isset($centro->statistiche['specializzazioni']) && $centro->statistiche['specializzazioni'])
                                    <div class="mt-3">
                                        <h6 class="text-muted mb-2">Distribuzione Specializzazioni:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($centro->statistiche['specializzazioni'] as $spec => $count)
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($spec) }}: {{ $count }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nessun Tecnico Assegnato</h5>
                                    <p class="text-muted">
                                        Questo centro non ha ancora tecnici assegnati.
                                    </p>
                                    <button type="button" 
                                            class="btn btn-primary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAssegnaTecnico">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Assegna Primo Tecnico
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Card cronologia --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Cronologia e Metadati
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Creazione</h6>
                                    <p class="mb-2">
                                        <strong>Data:</strong> {{ $centro->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Tempo trascorso:</strong> {{ $centro->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">Ultima Modifica</h6>
                                    <p class="mb-2">
                                        @if($centro->updated_at != $centro->created_at)
                                            <strong>Data:</strong> {{ $centro->updated_at->format('d/m/Y H:i') }}<br>
                                            <strong>Tempo trascorso:</strong> {{ $centro->updated_at->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Mai modificato</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-primary mb-1">{{ $centro->created_at->diffInDays() }}</h4>
                                    <small class="text-muted">Giorni di Attività</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success mb-1">{{ $centro->numero_tecnici }}</h4>
                                    <small class="text-muted">Tecnici Attuali</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-info mb-1">
                                        @if(isset($centro->statistiche['eta_media_tecnici']) && $centro->statistiche['eta_media_tecnici'])
                                            {{ round($centro->statistiche['eta_media_tecnici']) }}
                                        @else
                                            0
                                        @endif
                                    </h4>
                                    <small class="text-muted">Età Media Tecnici</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Card azioni rapide --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>
                                Azioni Rapide
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.centri.edit', $centro) }}" 
                                   class="btn btn-warning">
                                    <i class="bi bi-pencil me-2"></i>
                                    Modifica Centro
                                </a>
                                
                                <hr class="my-2">
                                
                                <button type="button" 
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAssegnaTecnico">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Assegna Tecnico
                                </button>
                                
                                @if($centro->hasTecnici())
                                    <a href="{{ route('admin.users.index', ['centro_id' => $centro->id]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-people me-2"></i>
                                        Gestisci Tecnici
                                    </a>
                                @endif
                                
                                <hr class="my-2">
                                
                                <a href="{{ $centro->google_maps_link }}" 
                                   target="_blank" 
                                   class="btn btn-outline-success">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Apri in Google Maps
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-outline-info"
                                        onclick="copiaIndirizzo()">
                                    <i class="bi bi-clipboard me-2"></i>
                                    Copia Indirizzo
                                </button>
                                
                                @if($centro->telefono)
                                    <button type="button" 
                                            class="btn btn-outline-primary"
                                            onclick="chiamaCentro()"
                                            data-telefono="{{ $centro->telefono }}">
                                        <i class="bi bi-telephone me-2"></i>
                                        Chiama Centro
                                    </button>
                                @endif
                                
                                @if($centro->email)
                                    <a href="mailto:{{ $centro->email }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="bi bi-envelope me-2"></i>
                                        Invia Email
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card mappa --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-map me-2"></i>
                                Localizzazione
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="ratio ratio-4x3">
                                <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyC4R6AN7SmujjPUIGKdyao2Kqitzr1kiRg&q={{ urlencode($centro->indirizzo_completo) }}"
                                        style="border:0;" 
                                        allowfullscreen="" 
                                        loading="lazy" 
                                        referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                            <div class="card-body">
                                <p class="card-text small text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $centro->indirizzo_completo }}
                                </p>
                                <a href="{{ $centro->google_maps_link }}" 
                                   target="_blank" 
                                   class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-arrow-up-right-square me-1"></i>
                                    Apri a Schermo Intero
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card statistiche territoriali --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Statistiche Territoriali
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $altriCentriProvincia = \App\Models\CentroAssistenza::where('provincia', $centro->provincia)
                                    ->where('id', '!=', $centro->id)
                                    ->count();
                                
                                $tecniciProvincia = \App\Models\User::whereHas('centroAssistenza', function($q) use ($centro) {
                                    $q->where('provincia', $centro->provincia);
                                })->where('livello_accesso', '2')->count();
                            @endphp
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h5 class="text-primary mb-1">{{ $altriCentriProvincia + 1 }}</h5>
                                    <small class="text-muted">Centri in {{ strtoupper($centro->provincia) }}</small>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-success mb-1">{{ $tecniciProvincia }}</h5>
                                    <small class="text-muted">Tecnici Totali</small>
                                </div>
                            </div>
                            
                            @if($altriCentriProvincia > 0)
                                <hr>
                                <p class="small text-muted mb-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Altri centri nella provincia:
                                </p>
                                <a href="{{ route('admin.centri.index', ['provincia' => $centro->provincia]) }}" 
                                   class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>
                                    Visualizza Altri Centri
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Assegnazione tecnico --}}
<div class="modal fade" id="modalAssegnaTecnico" tabindex="-1" aria-labelledby="modalAssegnaTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAssegnaTecnicoLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Assegna Tecnico al Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Centro:</strong> {{ $centro->nome }}<br>
                    <strong>Indirizzo:</strong> {{ $centro->indirizzo_completo }}
                </div>
                
                <form id="formAssegnaTecnico">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="tecnico_id" class="form-label required">
                            <i class="bi bi-person me-1"></i>
                            Seleziona Tecnico da Assegnare
                        </label>
                        <select class="form-select" id="tecnico_id" name="tecnico_id" required>
                            <option value="">-- Caricamento tecnici disponibili... --</option>
                        </select>
                        <small class="form-text text-muted">
                            Vengono mostrati solo i tecnici non ancora assegnati ad altri centri
                        </small>
                    </div>
                    
                    <div id="dettagliTecnico" class="card d-none mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-person-badge me-1"></i>
                                Dettagli Tecnico Selezionato
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Nome:</strong> <span id="dettaglioNome">-</span></p>
                                    <p class="mb-2"><strong>Username:</strong> <span id="dettaglioUsername">-</span></p>
                                    <p class="mb-2"><strong>Data Nascita:</strong> <span id="dettaglioDataNascita">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Specializzazione:</strong> <span id="dettaglioSpecializzazione">-</span></p>
                                    <p class="mb-2"><strong>Centro Attuale:</strong> <span id="dettaglioCentroAttuale">-</span></p>
                                    <p class="mb-2"><strong>Registrato:</strong> <span id="dettaglioRegistrato">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notificaTecnico" name="notifica_tecnico" checked>
                            <label class="form-check-label" for="notificaTecnico">
                                <i class="bi bi-envelope me-1"></i>
                                Invia notifica email al tecnico dell'assegnazione
                            </label>
                        </div>
                    </div>
                </form>
                
                <div class="row text-center mt-4">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-primary mb-1" id="statTecniciDisponibili">-</h4>
                            <small class="text-muted">Tecnici Disponibili</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success mb-1">{{ $centro->numero_tecnici }}</h4>
                            <small class="text-muted">Tecnici Attuali</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info mb-1" id="statTecniciProvincia">-</h4>
                        <small class="text-muted">Tecnici in {{ strtoupper($centro->provincia) }}</small>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Annulla
                </button>
                
                <button type="submit" form="formAssegnaTecnico" class="btn btn-success" id="btnConfermaAssegnazione" disabled>
                    <i class="bi bi-check-circle me-1"></i>
                    Assegna Tecnico
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Rimozione tecnico --}}
<div class="modal fade" id="modalRimuoviTecnico" tabindex="-1" aria-labelledby="modalRimuoviTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalRimuoviTecnicoLabel">
                    <i class="bi bi-person-dash me-2"></i>
                    Rimuovi Tecnico dal Centro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong> Stai per rimuovere un tecnico da questo centro.
                </div>
                
                <p>Stai per rimuovere il tecnico:</p>
                <div class="bg-light p-3 rounded">
                    <strong id="nomeTecnicoDaRimuovere">-</strong><br>
                    <span class="text-muted">dal centro {{ $centro->nome }}</span>
                </div>
                
                <p class="mt-3 text-muted">
                    Il tecnico non sarà più assegnato a questo centro, ma rimarrà nel sistema e potrà essere assegnato ad altri centri.
                </p>
                
                <form id="formRimuoviTecnico">
                    @csrf
                    <input type="hidden" id="tecnicoIdDaRimuovere" name="tecnico_id">
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notificaRimozione" name="notifica_rimozione" checked>
                        <label class="form-check-label" for="notificaRimozione">
                            <i class="bi bi-envelope me-1"></i>
                            Invia notifica email al tecnico della rimozione
                        </label>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Annulla
                </button>
                
                <button type="submit" form="formRimuoviTecnico" class="btn btn-warning">
                    <i class="bi bi-person-dash me-1"></i>
                    Rimuovi Tecnico
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JavaScript personalizzato --}}
@push('scripts')
<script>
/**
 * CORREZIONE URL JAVASCRIPT PER CHIAMATE AJAX
 * Sostituisci le parti del tuo JavaScript con queste versioni corrette
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Script centri show caricato - VERSIONE CORRETTA');
    
    // === CORREZIONE 1: URL PER TECNICI DISPONIBILI ===
    function caricaTecniciDisponibili() {
        console.log('⏳ Caricamento tecnici disponibili...');
        
        selectTecnico.innerHTML = '<option value="">-- Caricamento... --</option>';
        
        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            console.error('❌ Token CSRF non trovato');
            selectTecnico.innerHTML = '<option value="">-- Errore configurazione --</option>';
            mostraNotifica('Token CSRF non trovato', 'danger');
            return;
        }
        
        // === URL CORRETTO ===
        const apiUrl = `${window.location.origin}/api/admin/tecnici-disponibili`;
        console.log('🔗 Chiamata API corretta:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('📡 Risposta status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('✅ Dati ricevuti:', data);
            
            if (data.success) {
                popolaSelectTecnici(data.tecnici || []);
                aggiornaStatistica('statTecniciDisponibili', data.tecnici?.length || 0);
            } else {
                throw new Error(data.message || 'Errore nel caricamento tecnici');
            }
        })
        .catch(error => {
            console.error('❌ Errore caricamento tecnici:', error);
            selectTecnico.innerHTML = '<option value="">-- Errore caricamento --</option>';
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        });
    }
    
    // === CORREZIONE 2: URL PER DETTAGLI TECNICO ===
    function caricaDettagliTecnico(tecnicoId) {
        console.log('👤 Caricamento dettagli tecnico:', tecnicoId);
        
        const csrfToken = getCSRFToken();
        if (!csrfToken) return;
        
        // === URL CORRETTO ===
        const apiUrl = `${window.location.origin}/api/admin/tecnici/${tecnicoId}`;
        console.log('🔗 Chiamata dettagli corretta:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Dettagli tecnico:', data);
            
            if (data.success && data.tecnico) {
                mostraDettagliTecnico(data.tecnico);
            } else {
                throw new Error(data.message || 'Errore caricamento dettagli');
            }
        })
        .catch(error => {
            console.error('❌ Errore dettagli tecnico:', error);
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        });
    }
    
    // === CORREZIONE 3: URL PER STATISTICHE CENTRO ===
    function caricaStatistiche() {
        console.log('📊 Caricamento statistiche...');
        
        const csrfToken = getCSRFToken();
        const centroId = getCentroId();
        
        if (!csrfToken || !centroId) {
            console.warn('⚠️ Dati mancanti per statistiche');
            return;
        }
        
        // === URL CORRETTO ===
        const apiUrl = `${window.location.origin}/api/admin/centri/${centroId}/statistiche`;
        console.log('🔗 Chiamata statistiche corretta:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 Statistiche ricevute:', data);
            
            if (data.success && data.stats) {
                aggiornaStatistica('statTecniciProvincia', data.stats.tecnici_provincia || 0);
            }
        })
        .catch(error => {
            console.warn('⚠️ Errore statistiche (non critico):', error);
        });
    }
    
    // === CORREZIONE 4: URL PER ASSEGNAZIONE TECNICO ===
    function inviaAssegnazione(tecnicoId) {
        console.log('📤 Invio assegnazione tecnico:', tecnicoId);
        
        // Disabilita pulsante e mostra loading
        if (btnConferma) {
            btnConferma.disabled = true;
            btnConferma.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assegnando...';
        }
        
        const notificaTecnico = document.getElementById('notificaTecnico')?.checked || false;
        const csrfToken = getCSRFToken();
        const centroId = getCentroId();
        
        if (!csrfToken || !centroId) {
            console.error('❌ Dati mancanti per assegnazione');
            ripristinaPulsanteAssegnazione();
            return;
        }
        
        // === URL CORRETTO - IMPORTANTE: NON /api/ ===
        // Usa la route WEB, non API, per l'assegnazione
        const apiUrl = `${window.location.origin}/admin/centri/${centroId}/assegna-tecnico`;
        console.log('🔗 Chiamata assegnazione corretta:', apiUrl);
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                tecnico_id: tecnicoId,
                notifica_tecnico: notificaTecnico
            })
        })
        .then(response => {
            console.log('📡 Risposta assegnazione:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Assegnazione completata:', data);
            
            if (data.success) {
                chiudiModal('modalAssegnaTecnico');
                mostraNotifica('Tecnico assegnato con successo!', 'success');
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Errore assegnazione');
            }
        })
        .catch(error => {
            console.error('❌ Errore assegnazione:', error);
            ripristinaPulsanteAssegnazione();
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        });
    }
    
    // === CORREZIONE 5: URL PER RIMOZIONE TECNICO ===
    function inviaRimozione(tecnicoId) {
        console.log('🗑️ Invio rimozione tecnico:', tecnicoId);
        
        const notificaRimozione = document.getElementById('notificaRimozione')?.checked || false;
        const csrfToken = getCSRFToken();
        const centroId = getCentroId();
        
        if (!csrfToken || !centroId) {
            console.error('❌ Dati mancanti per rimozione');
            return;
        }
        
        // === URL CORRETTO - IMPORTANTE: NON /api/ ===
        // Usa la route WEB, non API, per la rimozione
        const apiUrl = `${window.location.origin}/admin/centri/${centroId}/rimuovi-tecnico`;
        console.log('🔗 Chiamata rimozione corretta:', apiUrl);
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                tecnico_id: tecnicoId,
                notifica_rimozione: notificaRimozione
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Rimozione completata:', data);
            
            if (data.success) {
                chiudiModal('modalRimuoviTecnico');
                mostraNotifica('Tecnico rimosso dal centro con successo!', 'success');
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Errore rimozione');
            }
        })
        .catch(error => {
            console.error('❌ Errore rimozione:', error);
            mostraNotifica(`Errore: ${error.message}`, 'danger');
        });
    }
    
    // === FUNZIONE HELPER MIGLIORATA ===
    function getCentroId() {
        // Metodo 1: Cerca data attribute
        const dataElement = document.querySelector('[data-centro-id]');
        if (dataElement) {
            return dataElement.dataset.centroId;
        }
        
        // Metodo 2: Estrai dalla URL
        const matches = window.location.pathname.match(/centri\/(\d+)/);
        if (matches) {
            return matches[1];
        }
        
        // Metodo 3: Fallback, cerca nell'URL completa
        const urlParams = new URLSearchParams(window.location.search);
        const centroIdParam = urlParams.get('centro_id');
        if (centroIdParam) {
            return centroIdParam;
        }
        
        console.error('❌ Impossibile determinare centro ID dalla pagina');
        return null;
    }
    
    // === DEBUG HELPER ===
    function debugRoutes() {
        console.log('🔍 DEBUG Route URL:');
        console.log('- Base URL:', window.location.origin);
        console.log('- Current path:', window.location.pathname);
        console.log('- Centro ID:', getCentroId());
        console.log('- CSRF Token presente:', !!getCSRFToken());
        
        // Test delle URL
        const centroId = getCentroId();
        if (centroId) {
            console.log('📍 URL che saranno chiamate:');
            console.log('  - Tecnici disponibili:', `${window.location.origin}/api/admin/tecnici-disponibili`);
            console.log('  - Statistiche centro:', `${window.location.origin}/api/admin/centri/${centroId}/statistiche`);
            console.log('  - Assegna tecnico:', `${window.location.origin}/admin/centri/${centroId}/assegna-tecnico`);
            console.log('  - Rimuovi tecnico:', `${window.location.origin}/admin/centri/${centroId}/rimuovi-tecnico`);
        }
    }
    
    // Esegui debug all'avvio
    debugRoutes();
    
    // Resto del codice rimane uguale...
    // === Il resto delle funzioni rimane identico ===
});
</script>
@endpush

{{-- CSS personalizzato --}}
@push('styles')
<style>
/* Miglioramenti visual */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.notifica-temp {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.avatar {
    font-size: 0.875rem;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

.modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group-vertical {
        flex-direction: row !important;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .btn-group-vertical .btn {
        flex: 1;
        min-width: calc(50% - 0.125rem);
    }
    
    .notifica-temp {
        right: 10px !important;
        left: 10px !important;
        min-width: auto !important;
        max-width: calc(100% - 20px) !important;
    }
    
    .modal-dialog {
        margin: 10px;
    }
}

/* Accessibilità */
@media (prefers-reduced-motion: reduce) {
    .card,
    .btn,
    .notifica-temp {
        transition: none !important;
        animation: none !important;
    }
}
</style>
@endpush