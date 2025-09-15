{{-- Vista per creare nuovo prodotto (Admin) --}}
@extends('layouts.app')

@section('title', 'Nuovo Prodotto')

@section('content')
<div class="container-fluid mt-4">
    
    

    <!-- === HEADER === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-plus-circle text-primary me-3 fs-2"></i>
                <div>
                    <h1 class="h2 mb-1">Aggiungi Nuovo Prodotto</h1>
                    <p class="text-muted mb-0">
                        Inserisci un nuovo prodotto nel catalogo aziendale
                    </p>
                </div>
            </div>
            
            <div class="alert alert-info border-start border-primary border-4">
                <i class="bi bi-lightbulb me-2"></i>
                <strong>Suggerimento:</strong> Compila tutte le informazioni tecniche per fornire il massimo supporto ai tecnici.
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === FORM PRINCIPALE === -->
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear text-primary me-2"></i>
                        Dettagli Prodotto
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.prodotti.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                        @csrf
                        
                        <!-- === INFORMAZIONI BASE === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Informazioni Base
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Nome e Modello -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="nome" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Nome Prodotto *
                                </label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="es: Lavatrice EcoWash Pro">
                                <div class="form-text">Nome commerciale del prodotto</div>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="modello" class="form-label fw-semibold">
                                    <i class="bi bi-hash me-1"></i>Modello *
                                </label>
                                <input type="text" 
                                       class="form-control @error('modello') is-invalid @enderror" 
                                       id="modello" 
                                       name="modello" 
                                       value="{{ old('modello') }}"
                                       required 
                                       maxlength="100"
                                       placeholder="es: EW-7000X">
                                @error('modello')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Categoria e Prezzo -->
                        
<div class="col-md-6">
    <label for="categoria" class="form-label fw-semibold">
        <i class="bi bi-collection me-1"></i>Categoria *
    </label>
    <select class="form-select @error('categoria') is-invalid @enderror" 
            id="categoria" 
            name="categoria" 
            required>
        <option value="">Seleziona categoria</option>
        {{-- 
            CORREZIONE PRINCIPALE: Utilizza le categorie dal sistema unificato
            invece delle categorie hardcoded 
        --}}
        @if(isset($categorie) && count($categorie) > 0)
            {{-- Usa le categorie passate dal controller (sistema unificato) --}}
            @foreach($categorie as $key => $label)
                <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        @else
            {{-- 
                FALLBACK: Se per qualche motivo $categorie non è disponibile,
                usa il metodo statico del modello direttamente nella vista 
            --}}
            @php
                $categorieUnificate = \App\Models\Prodotto::getCategorieUnifico();
            @endphp
            @foreach($categorieUnificate as $key => $label)
                <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        @endif
    </select>
    @error('categoria')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    {{-- Aggiungi testo di aiuto con le categorie disponibili --}}
    <div class="form-text">
        Seleziona la categoria appropriata per il prodotto
        <br><small class="text-muted">
            Categorie disponibili: 
            @if(isset($categorie))
                {{ implode(', ', array_slice(array_values($categorie), 0, 3)) }}{{ count($categorie) > 3 ? '...' : '' }}
            @endif
        </small>
    </div>
</div>
                        
                        <!-- Descrizione -->
                        <div class="mb-4">
                            <label for="descrizione" class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i>Descrizione *
                            </label>
                            <textarea class="form-control @error('descrizione') is-invalid @enderror" 
                                      id="descrizione" 
                                      name="descrizione" 
                                      rows="4" 
                                      required
                                      maxlength="1000"
                                      placeholder="Descrizione dettagliata del prodotto, caratteristiche principali, vantaggi...">{{ old('descrizione') }}</textarea>
                            <div class="form-text">
                                <span id="descrizione-counter">0</span>/1000 caratteri
                            </div>
                            @error('descrizione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- === SPECIFICHE TECNICHE === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-tools me-2"></i>Specifiche Tecniche
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Note Tecniche -->
                        <div class="mb-3">
                            <label for="note_tecniche" class="form-label fw-semibold">
                                <i class="bi bi-clipboard-data me-1"></i>Note Tecniche
                            </label>
                            <textarea class="form-control @error('note_tecniche') is-invalid @enderror" 
                                      id="note_tecniche" 
                                      name="note_tecniche" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Specifiche tecniche dettagliate, dimensioni, potenza, caratteristiche speciali...">{{ old('note_tecniche') }}</textarea>
                            <div class="form-text">
                                <span id="note-counter">0</span>/2000 caratteri
                            </div>
                            @error('note_tecniche')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Modalità Installazione -->
                        <div class="mb-3">
                            <label for="modalita_installazione" class="form-label fw-semibold">
                                <i class="bi bi-wrench me-1"></i>Modalità di Installazione
                            </label>
                            <textarea class="form-control @error('modalita_installazione') is-invalid @enderror" 
                                      id="modalita_installazione" 
                                      name="modalita_installazione" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Istruzioni dettagliate per l'installazione, requisiti, procedure da seguire...">{{ old('modalita_installazione') }}</textarea>
                            <div class="form-text">
                                <span id="installazione-counter">0</span>/2000 caratteri
                            </div>
                            @error('modalita_installazione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Modalità Uso -->
                        <div class="mb-4">
                            <label for="modalita_uso" class="form-label fw-semibold">
                                <i class="bi bi-book me-1"></i>Modalità d'Uso
                            </label>
                            <textarea class="form-control @error('modalita_uso') is-invalid @enderror" 
                                      id="modalita_uso" 
                                      name="modalita_uso" 
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Istruzioni per l'uso corretto, funzioni principali, consigli per l'utilizzatore...">{{ old('modalita_uso') }}</textarea>
                            <div class="form-text">
                                <span id="uso-counter">0</span>/2000 caratteri
                            </div>
                            @error('modalita_uso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- === IMMAGINE === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning mb-3">
                                    <i class="bi bi-image me-2"></i>Immagine Prodotto
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-semibold">
                                <i class="bi bi-camera me-1"></i>Foto Prodotto
                            </label>
                            <input type="file" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   id="foto" 
                                   name="foto"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   onchange="previewImage(this)">
                            <div class="form-text">
                                Formati supportati: JPG, PNG, GIF, WebP. Dimensione massima: 5MB
                            </div>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Anteprima immagine -->
                            <div id="image-preview" class="mt-3" style="display: none;">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="mb-2">Anteprima:</h6>
                                    <img id="preview-img" src="" class="img-fluid rounded" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                        <i class="bi bi-trash me-1"></i>Rimuovi
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- === CONFIGURAZIONI === -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-gear me-2"></i>Configurazioni
                                </h6>
                            </div>
                        </div>
                        
                        <!-- Stato e Assegnazione -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="attivo" class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-1"></i>Stato Prodotto
                                </label>
                                <select class="form-select @error('attivo') is-invalid @enderror" 
                                        id="attivo" 
                                        name="attivo">
                                    <option value="1" {{ old('attivo', '1') == '1' ? 'selected' : '' }}>
                                        ✅ Attivo (visibile nel catalogo)
                                    </option>
                                    <option value="0" {{ old('attivo') == '0' ? 'selected' : '' }}>
                                        ❌ Disattivo (nascosto dal catalogo)
                                    </option>
                                </select>
                                @error('attivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="staff_assegnato_id" class="form-label fw-semibold">
                                    <i class="bi bi-person-check me-1"></i>Assegna a Staff
                                </label>
                                <select class="form-select @error('staff_assegnato_id') is-invalid @enderror" 
                                        id="staff_assegnato_id" 
                                        name="staff_assegnato_id">
                                    <option value="">Nessuna assegnazione</option>
                                    @php
                                        $staffMembers = \App\Models\User::where('livello_accesso', '3')
                                            ->orderBy('nome')->orderBy('cognome')->get();
                                    @endphp
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}" {{ old('staff_assegnato_id') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->nome }} {{ $staff->cognome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Staff responsabile della gestione (opzionale)</div>
                                @error('staff_assegnato_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- === RIEPILOGO === -->
                        <div id="riepilogo-prodotto" class="alert alert-light border" style="display: none;">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle text-success me-2"></i>Riepilogo Prodotto
                            </h6>
                            <div id="riepilogo-content"></div>
                        </div>
                        
                        <!-- === PULSANTI AZIONE === -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                <button type="submit" class="btn btn-success" id="createBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Crea Prodotto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- === SIDEBAR GUIDA === -->
        <div class="col-lg-4">
            
            <!-- Guida Categorie -->
           
<div class="card card-custom mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="bi bi-collection text-primary me-2"></i>Categorie Prodotti
        </h6>
    </div>
    <div class="card-body">
        <div class="small">
            {{-- 
                CORREZIONE: Genera dinamicamente la guida dalle categorie unificate
                invece di avere descrizioni hardcoded 
            --}}
            @php
                // Usa le categorie unificate con descrizioni personalizzate
                $categorieGuida = [
                    'lavatrice' => ['label' => 'Lavatrici', 'desc' => 'Lavatrici a carica frontale e dall\'alto'],
                    'lavastoviglie' => ['label' => 'Lavastoviglie', 'desc' => 'Lavastoviglie da incasso e libere'],
                    'frigorifero' => ['label' => 'Frigoriferi', 'desc' => 'Frigoriferi, congelatori e combinati'],
                    'forno' => ['label' => 'Forni', 'desc' => 'Forni elettrici, gas e combinati'],
                    'asciugatrice' => ['label' => 'Asciugatrici', 'desc' => 'Asciugatrici a condensazione e pompa di calore'],
                    'piano_cottura' => ['label' => 'Piani Cottura', 'desc' => 'Piani a induzione, gas e elettrici'],
                    'cappa' => ['label' => 'Cappe Aspiranti', 'desc' => 'Cappe a parete, isola e da incasso'],
                    'microonde' => ['label' => 'Microonde', 'desc' => 'Microonde semplici e combinati'],
                    'condizionatore' => ['label' => 'Condizionatori', 'desc' => 'Climatizzatori e split'],
                    'aspirapolvere' => ['label' => 'Aspirapolvere', 'desc' => 'Robot e aspirapolvere tradizionali'],
                    'ferro_stiro' => ['label' => 'Ferri da Stiro', 'desc' => 'Ferri con caldaia e tradizionali'],
                    'macchina_caffe' => ['label' => 'Macchine Caffè', 'desc' => 'Espresso automatiche e manuali'],
                    'scaldabagno' => ['label' => 'Scaldabagni', 'desc' => 'Boiler elettrici e a gas'],
                    'caldaia' => ['label' => 'Caldaie', 'desc' => 'Caldaie murali e a basamento'],
                    'altro' => ['label' => 'Altri Elettrodomestici', 'desc' => 'Tutti gli altri elettrodomestici']
                ];
                
                // Ottieni solo le categorie effettivamente disponibili nel sistema
                $categorieDisponibili = isset($categorie) && count($categorie) > 0 
                    ? $categorie 
                    : \App\Models\Prodotto::getCategorieUnifico();
            @endphp
            
            @foreach($categorieDisponibili as $key => $label)
                @if(isset($categorieGuida[$key]))
                    <div class="mb-2">
                        <strong>{{ $label }}:</strong> 
                        <span class="text-muted">{{ $categorieGuida[$key]['desc'] }}</span>
                    </div>
                @else
                    {{-- Per categorie non mappate, usa una descrizione generica --}}
                    <div class="mb-2">
                        <strong>{{ $label }}:</strong> 
                        <span class="text-muted">Prodotti della categoria {{ strtolower($label) }}</span>
                    </div>
                @endif
            @endforeach
            
            {{-- Nota informativa sul sistema --}}
            <hr class="my-2">
            <div class="text-center">
                <small class="text-muted fst-italic">
                    Sistema di categorie unificato
                    <br>{{ count($categorieDisponibili) }} categorie disponibili
                </small>
            </div>
        </div>
    </div>
</div>
            
            <!-- Statistiche Catalogo -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>Catalogo Attuale
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="mb-1 text-primary">{{ \App\Models\Prodotto::count() }}</h5>
                            <small class="text-muted">Prodotti Totali</small>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1 text-success">{{ \App\Models\Prodotto::where('attivo', true)->count() }}</h5>
                            <small class="text-muted">Attivi</small>
                        </div>
                    </div>
                    <hr>
                    <div class="small">
                        <strong>Distribuzione per categoria:</strong>
                        @php
                            $categorie_stats = \App\Models\Prodotto::selectRaw('categoria, COUNT(*) as count')
                                ->whereNotNull('categoria')
                                ->groupBy('categoria')
                                ->orderBy('count', 'desc')
                                ->take(3)
                                ->get();
                        @endphp
                        @foreach($categorie_stats as $cat_stat)
                            <div class="d-flex justify-content-between mt-1">
                                <span>{{ ucfirst($cat_stat->categoria) }}</span>
                                <span class="badge bg-secondary">{{ $cat_stat->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Consigli -->
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Consigli
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Nome prodotto:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Usa un nome chiaro e descrittivo</li>
                                <li>Includi marca e serie se importante</li>
                                <li>Evita caratteri speciali</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Descrizione efficace:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Evidenzia caratteristiche principali</li>
                                <li>Usa paragrafi brevi</li>
                                <li>Includi vantaggi per l'utente</li>
                            </ul>
                        </div>
                        
                        <div class="mb-0">
                            <strong>Specifiche tecniche:</strong>
                            <ul class="mt-1 mb-0">
                                <li>Sii preciso e completo</li>
                                <li>Includi dimensioni e potenza</li>
                                <li>Aggiungi requisiti di installazione</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Azioni Rapide -->
            <div class="card card-custom">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning text-danger me-2"></i>Azioni Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.prodotti.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list me-1"></i>Visualizza Catalogo
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="fillSampleData()">
                            <i class="bi bi-magic me-1"></i>Dati di Esempio
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearForm()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Svuota Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === MODAL ANTEPRIMA === -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Prodotto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Contenuto popolato via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Modifica</button>
                <button type="button" class="btn btn-success" id="createFromPreview">
                    <i class="bi bi-plus-circle me-1"></i>Conferma Creazione
                </button>
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

.form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
}

.border-start.border-4 {
    border-width: 4px !important;
}

.badge {
    font-size: 0.75rem;
}

/* Preview styling */
#previewContent .preview-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-left: 3px solid #0d6efd;
    background-color: #f8f9fa;
}

#previewContent .preview-title {
    font-weight: bold;
    color: #0d6efd;
    margin-bottom: 0.5rem;
}

/* Focused field styling */
.focused { 
    transform: scale(1.01); 
    transition: transform 0.2s ease; 
}

/* Animation styles */
.alert { 
    animation: fadeIn 0.5s ease; 
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}

/* Image preview styling */
#image-preview {
    max-width: 100%;
}

#preview-img {
    border: 2px solid #dee2e6;
    object-fit: cover;
}

/* Character counter styling */
.text-warning { color: #fd7e14 !important; }
.text-danger { color: #dc3545 !important; }
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