@extends('layouts.app')

@section('title', 'Contatti')

@section('content')
<div class="container mt-4">
    
    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="bi bi-telephone text-primary me-2"></i>
                Contatti
            </h1>
            <p class="lead text-muted">
                Mettiti in contatto con noi per assistenza tecnica, informazioni sui prodotti o supporto generale
            </p>
        </div>
    </div>

    <!-- === INFORMAZIONI DI CONTATTO === -->
    @if(isset($contatti) && count($contatti) > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="bi bi-info-circle text-info me-2"></i>
                    Informazioni di Contatto
                </h3>
                <div class="row g-4">
                    @foreach($contatti as $contatto)
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-custom h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="me-3">
                                            @switch($contatto['tipo'])
                                                @case('sede_principale')
                                                    <i class="bi bi-building text-primary fs-2"></i>
                                                    @break
                                                @case('assistenza_tecnica')
                                                    <i class="bi bi-tools text-success fs-2"></i>
                                                    @break
                                                @case('commerciale')
                                                    <i class="bi bi-briefcase text-warning fs-2"></i>
                                                    @break
                                                @case('emergenze')
                                                    <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-telephone text-secondary fs-2"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-2">{{ $contatto['nome'] }}</h5>
                                            @if(isset($contatto['descrizione']))
                                                <p class="text-muted mb-3">{{ $contatto['descrizione'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="contact-details">
                                        @if(isset($contatto['telefono']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                <a href="tel:{{ $contatto['telefono'] }}" class="text-decoration-none">
                                                    {{ $contatto['telefono'] }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if(isset($contatto['email']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                <a href="mailto:{{ $contatto['email'] }}" class="text-decoration-none">
                                                    {{ $contatto['email'] }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if(isset($contatto['indirizzo']))
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                                <div>{{ $contatto['indirizzo'] }}</div>
                                            </div>
                                        @endif
                                        
                                        @if(isset($contatto['orari']))
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-clock text-muted me-2"></i>
                                                <div>{{ $contatto['orari'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(isset($contatto['telefono']) || isset($contatto['email']))
                                    <div class="card-footer bg-light">
                                        <div class="d-flex gap-2">
                                            @if(isset($contatto['telefono']))
                                                <a href="tel:{{ $contatto['telefono'] }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-telephone me-1"></i>Chiama
                                                </a>
                                            @endif
                                            @if(isset($contatto['email']))
                                                <a href="mailto:{{ $contatto['email'] }}" class="btn btn-outline-info btn-sm flex-fill">
                                                    <i class="bi bi-envelope me-1"></i>Email
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- === MODULO DI CONTATTO === -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>
                        Invia un Messaggio
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Compila il modulo sottostante per inviarci un messaggio. Ti risponderemo il prima possibile.
                    </p>
                    
                    <form action="{{ route('contatti.invia') }}" method="POST" id="contact-form">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Nome -->
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">
                                    Nome <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
                                       required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Cognome -->
                            <div class="col-md-6">
                                <label for="cognome" class="form-label fw-semibold">
                                    Cognome <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('cognome') is-invalid @enderror" 
                                       id="cognome" 
                                       name="cognome" 
                                       value="{{ old('cognome') }}" 
                                       required>
                                @error('cognome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Telefono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold">
                                    Telefono
                                </label>
                                <input type="tel" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tipo di Richiesta -->
                            <div class="col-md-6">
                                <label for="tipo_richiesta" class="form-label fw-semibold">
                                    Tipo di Richiesta <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_richiesta') is-invalid @enderror" 
                                        id="tipo_richiesta" 
                                        name="tipo_richiesta" 
                                        required>
                                    <option value="">Seleziona...</option>
                                    <option value="assistenza" {{ old('tipo_richiesta') == 'assistenza' ? 'selected' : '' }}>
                                        Assistenza Tecnica
                                    </option>
                                    <option value="informazioni" {{ old('tipo_richiesta') == 'informazioni' ? 'selected' : '' }}>
                                        Informazioni Prodotti
                                    </option>
                                    <option value="vendite" {{ old('tipo_richiesta') == 'vendite' ? 'selected' : '' }}>
                                        Vendite e Commerciale
                                    </option>
                                    <option value="reclamo" {{ old('tipo_richiesta') == 'reclamo' ? 'selected' : '' }}>
                                        Reclamo o Segnalazione
                                    </option>
                                </select>
                                @error('tipo_richiesta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Oggetto -->
                            <div class="col-md-6">
                                <label for="oggetto" class="form-label fw-semibold">
                                    Oggetto <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('oggetto') is-invalid @enderror" 
                                       id="oggetto" 
                                       name="oggetto" 
                                       value="{{ old('oggetto') }}" 
                                       required>
                                @error('oggetto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Messaggio -->
                            <div class="col-12">
                                <label for="messaggio" class="form-label fw-semibold">
                                    Messaggio <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('messaggio') is-invalid @enderror" 
                                          id="messaggio" 
                                          name="messaggio" 
                                          rows="6" 
                                          required 
                                          placeholder="Descrivi dettagliatamente la tua richiesta...">{{ old('messaggio') }}</textarea>
                                @error('messaggio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Privacy -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input @error('privacy') is-invalid @enderror" 
                                           type="checkbox" 
                                           id="privacy" 
                                           name="privacy" 
                                           value="1" 
                                           {{ old('privacy') ? 'checked' : '' }} 
                                           required>
                                    <label class="form-check-label" for="privacy">
                                        Accetto il trattamento dei dati personali secondo la <a href="#" class="text-decoration-none">Privacy Policy</a> <span class="text-danger">*</span>
                                    </label>
                                    @error('privacy')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Pulsanti -->
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>Invia Messaggio
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- === INFORMAZIONI AGGIUNTIVE === -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informazioni Utili
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Tempi di Risposta</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-clock text-success me-2"></i>Email: entro 24 ore lavorative</li>
                                <li><i class="bi bi-telephone text-success me-2"></i>Telefono: risposta immediata negli orari di ufficio</li>
                                <li><i class="bi bi-exclamation-triangle text-warning me-2"></i>Emergenze: disponibilità 24/7</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Prima di Contattarci</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-search text-info me-2"></i>Consulta la <a href="{{ route('prodotti.pubblico.index') }}" class="text-decoration-none">base di conoscenza prodotti</a></li>
                                <li><i class="bi bi-geo-alt text-info me-2"></i>Trova il <a href="{{ route('centri.index') }}" class="text-decoration-none">centro assistenza</a> più vicino</li>
                                <li><i class="bi bi-file-text text-info me-2"></i>Leggi la <a href="{{ route('documentazione') }}" class="text-decoration-none" target="_blank">documentazione</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

@push('styles')
<style>
.card-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: box-shadow 0.2s ease-in-out;
}

.card-custom:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.contact-details {
    font-size: 0.95rem;
}

.form-label.fw-semibold {
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
}

#messaggio {
    resize: vertical;
    min-height: 120px;
}

@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .card-footer .btn {
        flex: 1 !important;
    }
}

/* Stile per form validation */
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Stile per i link nei contatti */
a[href^="tel:"], a[href^="mailto:"] {
    color: inherit;
    text-decoration: none;
}

a[href^="tel:"]:hover, a[href^="mailto:"]:hover {
    color: var(--bs-primary);
    text-decoration: underline;
}
</style>
@endpush