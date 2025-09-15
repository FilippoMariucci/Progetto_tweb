{{--
    File: resources/views/centri/show.blade.php
    Descrizione: Vista dettaglio di un centro di assistenza
    Livello accesso: Pubblico (Livello 1)
    
    Mostra:
    - Informazioni complete del centro
    - Lista tecnici specializzati
    - Centri vicini nella stessa provincia
    - Informazioni di contatto
--}}

@extends('layouts.app')

{{-- Titolo dinamico della pagina --}}
@section('title', $centro->nome . ' - Centro Assistenza')

{{-- Meta description per SEO --}}
@section('meta_description', 'Centro di assistenza ' . $centro->nome . ' a ' . $centro->citta . ' (' . $centro->provincia . '). Contatti, tecnici specializzati e servizi disponibili.')

@section('content')
<div class="container mt-4">
    
    

    <div class="row">
        {{-- Colonna principale con i dettagli del centro --}}
        <div class="col-lg-8">
            
            {{-- Header del centro --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="h3 mb-0">
                                <i class="bi bi-building me-2"></i>
                                {{ $centro->nome }}
                            </h1>
                            <p class="mb-0 opacity-75">
                                Centro di Assistenza Tecnica
                            </p>
                        </div>
                        <div class="col-auto">
                            {{-- Badge con numero tecnici --}}
                            @if($centro->tecnici_count > 0)
                                <span class="badge bg-light text-primary fs-6">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $centro->tecnici_count }} Tecnic{{ $centro->tecnici_count > 1 ? 'i' : 'o' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Informazioni di contatto --}}
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-geo-alt me-2"></i>
                                Informazioni di Contatto
                            </h5>
                            
                            {{-- Indirizzo --}}
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">INDIRIZZO</label>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{ $centro->indirizzo ?? 'Non specificato' }}
                                </p>
                                <p class="text-muted small mb-0">
                                    {{ $centro->citta }}
                                    @if($centro->cap)
                                        {{ $centro->cap }}
                                    @endif
                                    @if($centro->provincia)
                                        ({{ strtoupper($centro->provincia) }})
                                    @endif
                                </p>
                            </div>
                            
                            {{-- Telefono --}}
                            @if($centro->telefono)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">TELEFONO</label>
                                    <p class="mb-0">
                                        <i class="bi bi-telephone text-success me-2"></i>
                                        <a href="tel:{{ $centro->telefono }}" class="text-decoration-none">
                                            {{ $centro->telefono }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- Email --}}
                            @if($centro->email)
                                <div class="mb-3">
                                    <label class="fw-semibold text-muted small">EMAIL</label>
                                    <p class="mb-0">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        <a href="mailto:{{ $centro->email }}" class="text-decoration-none">
                                            {{ $centro->email }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Orari e servizi --}}
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>
                                Servizi e Orari
                            </h5>
                            
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">SERVIZI DISPONIBILI</label>
                                <ul class="list-unstyled mt-2">
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Diagnostica tecnica</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Riparazione componenti</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Supporto installazione</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Consulenza specialistica</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-semibold text-muted small">ORARI INDICATIVI</label>
                                <p class="mb-1">
                                    <i class="bi bi-calendar3 text-primary me-2"></i>
                                    Lunedì - Venerdì: 9:00 - 18:00
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Contattare il centro per confermare gli orari
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Pulsanti di azione --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                {{-- Chiama --}}
                                @if($centro->telefono)
                                    <a href="tel:{{ $centro->telefono }}" 
                                       class="btn btn-success text-white fw-semibold">
                                        <i class="bi bi-telephone-fill me-1"></i>
                                        Chiama Ora
                                    </a>
                                @endif
                                
                                {{-- Email --}}
                                @if($centro->email)
                                    <a href="mailto:{{ $centro->email }}" 
                                       class="btn btn-info text-white fw-semibold">
                                        <i class="bi bi-envelope-fill me-1"></i>
                                        Invia Email
                                    </a>
                                @endif
                                
                                {{-- Google Maps --}}
                                @if($centro->indirizzo && $centro->citta)
                                    @php
                                        $indirizzoCompleto = urlencode($centro->indirizzo . ', ' . $centro->citta . ($centro->provincia ? ', ' . $centro->provincia : '') . ', Italia');
                                        $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $indirizzoCompleto;
                                    @endphp
                                    <a href="{{ $mapsUrl }}" 
                                       target="_blank" 
                                       class="btn btn-primary text-white fw-semibold">
                                        <i class="bi bi-map-fill me-1"></i>
                                        Apri Mappa
                                    </a>
                                @endif
                                
                                {{-- Torna alla lista --}}
                                <a href="{{ route('centri.index') }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Torna alla Lista
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sezione Tecnici Specializzati --}}
            @if($centro->tecnici && $centro->tecnici->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-people text-primary me-2"></i>
                            Tecnici Specializzati
                            <span class="badge bg-primary ms-2">{{ $centro->tecnici->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($centro->tecnici as $tecnico)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $tecnico->nome }} {{ $tecnico->cognome }}</h6>
                                                @if($tecnico->specializzazione)
                                                    <span class="badge bg-light text-dark small">
                                                        {{ $tecnico->specializzazione }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Informazioni aggiuntive tecnico --}}
                                        @if($tecnico->data_nascita)
                                            <p class="small text-muted mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Età: {{ $tecnico->data_nascita->age }} anni
                                            </p>
                                        @endif
                                        
                                        @if($tecnico->specializzazione)
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-award me-1"></i>
                                                Specializzazione: {{ $tecnico->specializzazione }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
        
        {{-- Sidebar con informazioni aggiuntive --}}
        <div class="col-lg-4">
            
            {{-- Mappa e posizione --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        Posizione e Contatti
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Indirizzo completo --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt-fill text-primary me-2 mt-1"></i>
                            <div>
                                <strong>{{ $centro->indirizzo ?? 'Indirizzo non disponibile' }}</strong><br>
                                {{ $centro->citta }}
                                @if($centro->cap)
                                    {{ $centro->cap }}
                                @endif
                                <br>
                                @if($centro->provincia)
                                    Provincia di {{ strtoupper($centro->provincia) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Contatti diretti --}}
                    <div class="d-grid gap-2">
                        @if($centro->telefono)
                            <a href="tel:{{ $centro->telefono }}" 
                               class="btn btn-success text-white fw-semibold">
                                <i class="bi bi-telephone-fill me-2"></i>
                                {{ $centro->telefono }}
                            </a>
                        @endif
                        
                        @if($centro->email)
                            <a href="mailto:{{ $centro->email }}" 
                               class="btn btn-info text-white fw-semibold">
                                <i class="bi bi-envelope-fill me-2"></i>
                                {{ $centro->email }}
                            </a>
                        @endif
                        
                        {{-- Google Maps --}}
                        @if($centro->indirizzo && $centro->citta)
                            @php
                                $indirizzoCompleto = urlencode($centro->indirizzo . ', ' . $centro->citta . ($centro->provincia ? ', ' . $centro->provincia : '') . ', Italia');
                                $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $indirizzoCompleto;
                            @endphp
                            <a href="{{ $mapsUrl }}" 
                               target="_blank" 
                               class="btn btn-primary text-white fw-semibold">
                                <i class="bi bi-map-fill me-2"></i>
                                Visualizza su Google Maps
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Statistiche del centro --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistiche Centro
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ $centro->tecnici_count ?? 0 }}</h4>
                                <small class="text-muted">Tecnici</small>
                            </div>
                        </div>
                        <div class="col-6">
                            @php
                                $specializzazioni = $centro->tecnici ? $centro->tecnici->whereNotNull('specializzazione')->pluck('specializzazione')->unique()->count() : 0;
                            @endphp
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">{{ $specializzazioni }}</h4>
                                <small class="text-muted">Specializzazioni</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h5 class="text-success mb-1">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Attivo
                                </h5>
                                <small class="text-muted">
                                    Dal {{ $centro->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Centri vicini --}}
            @if(isset($centriVicini) && $centriVicini->count() > 0)
                <div class="card card-custom mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-geo text-primary me-2"></i>
                            Altri Centri in Provincia di {{ strtoupper($centro->provincia) }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($centriVicini as $centroVicino)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('centri.show', $centroVicino) }}" 
                                               class="text-decoration-none">
                                                {{ $centroVicino->nome }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $centroVicino->citta }}
                                        </small>
                                    </div>
                                    <span class="badge bg-light text-dark">
                                        {{ $centroVicino->tecnici_count ?? 0 }} tecnici
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Link utili --}}
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-link-45deg text-primary me-2"></i>
                        Link Utili
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('centri.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-list me-2"></i>
                            Tutti i Centri
                        </a>
                        
                        {{-- Link al catalogo: pubblico o tecnico in base all'autenticazione --}}
                        @auth
    {{-- Tecnico (livelli 2 o 3) --}}
    @if(Auth::user()->livello_accesso == 2 || Auth::user()->livello_accesso == 3)
        <a href="{{ route('prodotti.completo.index') }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-tools me-2"></i>
            Catalogo Tecnico
        </a>

    {{-- Admin (livello 4) --}}
    @elseif(Auth::user()->livello_accesso == 4)
        <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-shield-lock me-2"></i>
            Catalogo Admin
        </a>

    {{-- Utente autenticato ma con livello diverso --}}
    @else
        <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box me-2"></i>
            Catalogo Prodotti
        </a>
    @endif
@else
    {{-- Utente non autenticato --}}
    <a href="{{ route('prodotti.pubblico.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-box me-2"></i>
        Catalogo Prodotti
    </a>
@endauth
                        
                        <a href="{{ route('home') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-house me-2"></i>
                            Homepage
                        </a>
                        
                        @guest
                            <hr class="my-2">
                            <a href="{{ route('login') }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-person-lock me-2"></i>
                                Area Tecnici
                            </a>
                        @else
                            {{-- Utente autenticato - link alla dashboard appropriata --}}
                            <hr class="my-2">
                            @if(Auth::user()->livello_accesso >= 4)
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-gear me-2"></i>
                                    Dashboard Admin
                                </a>
                            @elseif(Auth::user()->livello_accesso >= 3)
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Dashboard Staff
                                </a>
                            @elseif(Auth::user()->livello_accesso >= 2)
                                <a href="{{ route('tecnico.dashboard') }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-person-gear me-2"></i>
                                    Dashboard Tecnico
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    {{-- Sezione informazioni aggiuntive --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni sui Servizi di Assistenza
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Come Richiedere Assistenza</h6>
                            <ol class="ps-3">
                                <li><strong>Contatta il centro</strong> telefonicamente o via email</li>
                                <li><strong>Descrivi il problema</strong> in modo dettagliato</li>
                                <li><strong>Fornisci il modello</strong> del prodotto interessato</li>
                                <li><strong>Concorda l'intervento</strong> con il tecnico specializzato</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Cosa Portare</h6>
                            <ul class="list-unstyled ps-3">
                                <li><i class="bi bi-check text-success me-2"></i><strong>Documento di identità</strong></li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Scontrino o fattura</strong> di acquisto</li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Documentazione tecnica</strong> del prodotto</li>
                                <li><i class="bi bi-check text-success me-2"></i><strong>Eventuali accessori</strong> correlati al problema</li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- Alert informativo --}}
                    <div class="alert alert-info mt-3 mb-0">
                        <div class="d-flex">
                            <i class="bi bi-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <strong>Importante:</strong> 
                                Prima di recarti presso il centro, ti consigliamo di contattarlo telefonicamente 
                                per verificare la disponibilità e concordare l'appuntamento più adatto alle tue esigenze.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

{{-- Script JavaScript per migliorare l'esperienza utente --}}
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

{{-- Stili CSS personalizzati per questa vista --}}
@push('styles')
<style>
/* Stili per la vista dettaglio centro */
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Badge personalizzati */
.badge.fs-6 {
    font-size: 0.875rem !important;
}

/* Bottoni di contatto con testo ben visibile */
.btn.btn-success {
    background: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
}

.btn.btn-info {
    background: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #ffffff !important;
}

.btn.btn-primary {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #ffffff !important;
}

/* Hover effects con testo sempre bianco */
.btn.btn-success:hover {
    background: #218838 !important;
    border-color: #1e7e34 !important;
    color: #ffffff !important;
}

.btn.btn-info:hover {
    background: #138496 !important;
    border-color: #117a8b !important;
    color: #ffffff !important;
}

.btn.btn-primary:hover {
    background: #0069d9 !important;
    border-color: #0062cc !important;
    color: #ffffff !important;
}

/* Responsive design per mobile */
@media (max-width: 768px) {
    .d-flex.flex-wrap.gap-2 {
        justify-content: stretch !important;
    }
    
    .d-flex.flex-wrap.gap-2 > * {
        flex: 1 !important;
        min-width: 120px;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.5rem;
    }
}

/* Animazioni hover per i tecnici */
.border.rounded.p-3:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Icone colorate per i contatti */
.bi-telephone {
    color: #28a745 !important;
}

.bi-envelope {
    color: #17a2b8 !important;
}

.bi-map {
    color: #007bff !important;
}

/* Alert personalizzato */
.alert-info {
    background: linear-gradient(135deg, #cce7ff, #e3f2fd);
    border: 1px solid #007bff;
    border-radius: 8px;
}
</style>
@endpush