{{-- Vista per creare nuovo prodotto (Admin) --}}
@extends('layouts.app')

@section('title', 'Nuovo Prodotto')

@section('content')
<div class="container-fluid mt-4">
    
    <!-- === BREADCRUMB === -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.prodotti.index') }}">Gestione Prodotti</a></li>
            <li class="breadcrumb-item active">Nuovo Prodotto</li>
        </ol>
    </nav>

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
$(document).ready(function() {
    
    // === CONTATORI CARATTERI ===
    
    /**
     * Aggiorna i contatori dei caratteri per le textarea
     */
    function setupCharacterCounters() {
        // Contatore descrizione
        $('#descrizione').on('input', function() {
            const length = $(this).val().length;
            const counter = $('#descrizione-counter');
            counter.text(length);
            
            if (length > 800) {
                counter.addClass('text-danger').removeClass('text-warning');
            } else if (length > 600) {
                counter.addClass('text-warning').removeClass('text-danger');
            } else {
                counter.removeClass('text-warning text-danger');
            }
        });
        
        // Contatore note tecniche
        $('#note_tecniche').on('input', function() {
            const length = $(this).val().length;
            $('#note-counter').text(length);
        });
        
        // Contatore installazione
        $('#modalita_installazione').on('input', function() {
            const length = $(this).val().length;
            $('#installazione-counter').text(length);
        });
        
        // Contatore uso
        $('#modalita_uso').on('input', function() {
            const length = $(this).val().length;
            $('#uso-counter').text(length);
        });
    }
    
    // === GESTIONE ANTEPRIMA ===
    
    /**
     * Mostra l'anteprima del prodotto in un modal
     */
    $('#previewBtn').on('click', function() {
        updatePreview();
        $('#previewModal').modal('show');
    });
    
    /**
     * Aggiorna il contenuto dell'anteprima
     */
    function updatePreview() {
        const formData = {
            nome: $('#nome').val(),
            modello: $('#modello').val(),
            categoria: $('#categoria option:selected').text(),
            prezzo: $('#prezzo').val(),
            descrizione: $('#descrizione').val(),
            note_tecniche: $('#note_tecniche').val(),
            modalita_installazione: $('#modalita_installazione').val(),
            modalita_uso: $('#modalita_uso').val(),
            attivo: $('#attivo option:selected').text(),
            staff_assegnato: $('#staff_assegnato_id option:selected').text()
        };
        
        let previewHtml = `
            <div class="preview-section">
                <div class="preview-title">📦 Informazioni Base</div>
                <div><strong>Nome:</strong> ${formData.nome || 'Non specificato'}</div>
                <div><strong>Modello:</strong> ${formData.modello || 'Non specificato'}</div>
                <div><strong>Categoria:</strong> ${formData.categoria !== 'Seleziona categoria' ? formData.categoria : 'Non selezionata'}</div>
                <div><strong>Prezzo:</strong> ${formData.prezzo ? '€ ' + formData.prezzo : 'Non specificato'}</div>
                <div><strong>Stato:</strong> ${formData.attivo}</div>
            </div>
        `;
        
        // Descrizione se presente
        if (formData.descrizione) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">📝 Descrizione</div>
                    <div>${formData.descrizione}</div>
                </div>
            `;
        }
        
        // Specifiche tecniche
        if (formData.note_tecniche || formData.modalita_installazione || formData.modalita_uso) {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">🔧 Specifiche Tecniche</div>
            `;
            
            if (formData.note_tecniche) {
                previewHtml += `<div><strong>Note Tecniche:</strong><br>${formData.note_tecniche}</div><br>`;
            }
            
            if (formData.modalita_installazione) {
                previewHtml += `<div><strong>Installazione:</strong><br>${formData.modalita_installazione}</div><br>`;
            }
            
            if (formData.modalita_uso) {
                previewHtml += `<div><strong>Modalità d'Uso:</strong><br>${formData.modalita_uso}</div>`;
            }
            
            previewHtml += `</div>`;
        }
        
        // Assegnazione staff se presente
        if (formData.staff_assegnato !== 'Nessuna assegnazione') {
            previewHtml += `
                <div class="preview-section">
                    <div class="preview-title">👥 Gestione</div>
                    <div><strong>Staff Assegnato:</strong> ${formData.staff_assegnato}</div>
                </div>
            `;
        }
        
        $('#previewContent').html(previewHtml);
        
        // Mostra anche il riepilogo inline
        $('#riepilogo-content').html(previewHtml);
        $('#riepilogo-prodotto').slideDown();
    }
    
    /**
     * Conferma creazione dal modal di anteprima
     */
    $('#createFromPreview').on('click', function() {
        $('#previewModal').modal('hide');
        $('#createProductForm').submit();
    });
    
    // === VALIDAZIONE FORM ===
    
    /**
     * Validazione in tempo reale del form
     */
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('blur change', function() {
        validateField($(this));
    });
    
    /**
     * Valida un singolo campo
     */
    function validateField($field) {
        const value = $field.val().trim();
        const fieldName = $field.attr('name');
        let isValid = true;
        let errorMessage = '';
        
        // Validazioni specifiche per campo
        switch(fieldName) {
            case 'nome':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'Nome deve essere almeno 3 caratteri';
                }
                break;
                
            case 'modello':
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Modello deve essere almeno 2 caratteri';
                }
                break;
                
            case 'prezzo':
                if (value && (isNaN(value) || parseFloat(value) < 0)) {
                    isValid = false;
                    errorMessage = 'Prezzo deve essere un numero positivo';
                }
                break;
                
            case 'descrizione':
                if (value.length < 10) {
                    isValid = false;
                    errorMessage = 'Descrizione deve essere almeno 10 caratteri';
                }
                break;
        }
        
        // Applica la validazione visiva
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            $field.siblings('.invalid-feedback.custom-validation').remove();
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            
            // Aggiunge messaggio di errore se non esiste
            if (!$field.siblings('.invalid-feedback.custom-validation').length) {
                $field.after(`<div class="invalid-feedback custom-validation">${errorMessage}</div>`);
            } else {
                $field.siblings('.invalid-feedback.custom-validation').text(errorMessage);
            }
        }
        
        return isValid;
    }
    
    /**
     * Validazione completa del form prima dell'invio
     */
    $('#createProductForm').on('submit', function(e) {
        let isFormValid = true;
        
        // Valida tutti i campi obbligatori
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        // Previene l'invio se ci sono errori
        if (!isFormValid) {
            e.preventDefault();
            
            // Mostra alert di errore
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Errori nel form:</strong> Correggi i campi evidenziati prima di continuare.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#createProductForm').prepend(alertHtml);
            
            // Scroll al primo errore
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });
    
    // === MIGLIORAMENTI UX ===
    
    /**
     * Genera automaticamente il modello basato sul nome
     */
    $('#nome').on('input', function() {
        const nome = $(this).val();
        
        // Solo se il campo modello è vuoto
        if ($('#modello').val().trim() === '' && nome.length > 3) {
            // Estrae le prime lettere delle parole principali
            const words = nome.split(' ');
            let modello = '';
            
            words.forEach(word => {
                if (word.length > 2) {
                    modello += word.substring(0, 2).toUpperCase();
                }
            });
            
            // Aggiunge numeri casuali
            modello += '-' + Math.floor(Math.random() * 9000 + 1000);
            
            $('#modello').val(modello);
        }
    });
    
    /**
     * Effetti visivi per i campi in focus
     */
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.mb-3, .mb-4').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.mb-3, .mb-4').removeClass('focused');
    });
    
    /**
     * Conferma prima di abbandonare la pagina se ci sono modifiche
     */
    let formChanged = false;
    $('#createProductForm input, #createProductForm select, #createProductForm textarea').on('input change', function() {
        formChanged = true;
    });
    
    $(window).on('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Hai modifiche non salvate. Vuoi davvero uscire?';
            return e.returnValue;
        }
    });
    
    // Rimuove l'avviso quando il form viene inviato
    $('#createProductForm').on('submit', function() {
        formChanged = false;
    });
    
    // Inizializza componenti
    initializeComponents();
    
    /**
     * Inizializza componenti e impostazioni iniziali
     */
    function initializeComponents() {
        // Setup contatori caratteri
        setupCharacterCounters();
        
        // Nasconde il riepilogo inizialmente
        $('#riepilogo-prodotto').hide();
        
        // Focus sul primo campo
        $('#nome').focus();
        
        console.log('✅ Sistema creazione prodotto inizializzato correttamente');
    }
});

// === FUNZIONI GLOBALI ===

/**
 * Anteprima immagine quando viene selezionata
 */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Controlla dimensione file (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('Il file è troppo grande. Dimensione massima: 5MB');
            input.value = '';
            return;
        }
        
        // Controlla tipo file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Formato file non supportato. Usa: JPG, PNG, GIF, WebP');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-img').attr('src', e.target.result);
            $('#image-preview').show();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Rimuove l'immagine selezionata
 */
function removeImage() {
    $('#foto').val('');
    $('#image-preview').hide();
    $('#preview-img').attr('src', '');
}

/**
 * Riempie il form con dati di esempio
 */
function fillSampleData() {
    if (confirm('Vuoi riempire il form con dati di esempio? I dati attuali verranno sostituiti.')) {
        // Ottieni la prima categoria disponibile dal dropdown
        const categoriaSelect = document.getElementById('categoria');
        const firstCategory = categoriaSelect.options[1]; // Prima opzione dopo "Seleziona categoria"
        
        // Dati di esempio adattati alla categoria selezionata
        let sampleData = getSampleDataForCategory(firstCategory ? firstCategory.value : 'altro');
        
        // Riempie i campi con i dati di esempio
        document.getElementById('nome').value = sampleData.nome;
        document.getElementById('modello').value = sampleData.modello;
        document.getElementById('prezzo').value = sampleData.prezzo;
        document.getElementById('descrizione').value = sampleData.descrizione;
        document.getElementById('note_tecniche').value = sampleData.note_tecniche;
        document.getElementById('modalita_installazione').value = sampleData.modalita_installazione;
        document.getElementById('modalita_uso').value = sampleData.modalita_uso;
        document.getElementById('attivo').value = '1';
        
        // Seleziona la categoria
        if (firstCategory) {
            categoriaSelect.value = firstCategory.value;
        }
        
        // Triggera gli eventi per aggiornare contatori e validazione
        ['#descrizione', '#note_tecniche', '#modalita_installazione', '#modalita_uso'].forEach(selector => {
            $(selector).trigger('input');
        });
        
        alert(`Dati di esempio inseriti per categoria: ${firstCategory ? firstCategory.text : 'Generico'}!`);
    }
}

function getSampleDataForCategory(categoria) {
    const sampleDataMap = {
        'lavatrice': {
            nome: 'Lavatrice EcoWash Pro',
            modello: 'EW-7000X',
            prezzo: '699.99',
            descrizione: 'Lavatrice ad alta efficienza energetica con capacità di 7kg. Dotata di tecnologia inverter per un funzionamento silenzioso e programmi di lavaggio intelligenti. Ideale per famiglie di 3-4 persone.',
            note_tecniche: 'Capacità: 7kg\nVelocità centrifuga: 1400 giri/min\nClasse energetica: A+++\nDimensioni: 60x60x85 cm\nPotenza: 2100W\nCollegamento: 230V',
            modalita_installazione: '1. Rimuovere imballaggio e blocchi di trasporto\n2. Posizionare su superficie piana e livellare\n3. Collegare tubo di scarico e carico acqua\n4. Collegare alimentazione elettrica\n5. Eseguire primo lavaggio a vuoto',
            modalita_uso: 'Selezionare il programma adatto al tipo di tessuto. Dosare il detersivo secondo le indicazioni. Per capi delicati utilizzare il programma apposito. Pulire regolarmente il filtro e il cassetto detersivo.'
        },
        
        'lavastoviglie': {
            nome: 'Lavastoviglie SilentClean',
            modello: 'SC-6000',
            prezzo: '549.99',
            descrizione: 'Lavastoviglie da incasso ultra-silenziosa con 3° cestello per posate. 14 coperti, classe A+++, funzionamento silenzioso sotto i 42dB.',
            note_tecniche: 'Capacità: 14 coperti\nRumorosità: 42dB\nClasse energetica: A+++\nDimensioni: 60x60x82 cm\n8 programmi di lavaggio\n3° cestello per posate',
            modalita_installazione: '1. Preparare vano di incasso 60x60x82 cm\n2. Collegare scarico e carico acqua\n3. Collegamento elettrico 230V\n4. Fissare ai mobili laterali\n5. Test di funzionamento',
            modalita_uso: 'Caricare stoviglie senza sovrapporle. Utilizzare sale rigenerante e brillantante. Selezionare programma adeguato al carico. Pulire filtri settimanalmente.'
        },
        
        'frigorifero': {
            nome: 'Frigorifero CoolFresh XL',
            modello: 'CF-400L',
            prezzo: '1299.99',
            descrizione: 'Frigorifero combinato No Frost da 400L con dispenser acqua e ghiaccio. Controllo digitale della temperatura e sistema antibatterico.',
            note_tecniche: 'Capacità: 400L (280L frigo + 120L freezer)\nClasse energetica: A++\nSistema No Frost\nDispenser acqua/ghiaccio\nDimensioni: 70x60x185 cm',
            modalita_installazione: '1. Posizionare su superficie piana\n2. Lasciare 5cm di spazio sui lati\n3. Collegamento idrico per dispenser\n4. Collegamento elettrico\n5. Attesa 4 ore prima dell\'accensione',
            modalita_uso: 'Regolare temperature: frigo +4°C, freezer -18°C. Sostituire filtro acqua ogni 6 mesi. Pulire bobine posteriori ogni 6 mesi. Non sovraccaricare i ripiani.'
        },
        
        'forno': {
            nome: 'Forno Multifunzione Chef Pro',
            modello: 'CP-65L',
            prezzo: '799.99',
            descrizione: 'Forno elettrico multifunzione da 65L con pirolisi e 10 funzioni di cottura. Display touch e sonde temperatura.',
            note_tecniche: 'Capacità: 65L\n10 funzioni cottura\nPirolisi autopulente\nTemperatura: 50-275°C\nClasse energetica: A\nDimensioni: 60x60x60 cm',
            modalita_installazione: '1. Preparare vano incasso 60x60x60 cm\n2. Collegamento elettrico 380V\n3. Ventilazione retrostante\n4. Fissaggio con staffe\n5. Test funzionamento e calibrazione',
            modalita_uso: 'Preriscaldare sempre il forno. Utilizzare funzione pirolisi per pulizia mensile. Posizionare cibi nel ripiano centrale per cottura uniforme. Utilizzare sonde per arrosti.'
        },
        
        'piano_cottura': {
            nome: 'Piano Cottura Induzione FlexCook',
            modello: 'FC-4Z-IND',
            prezzo: '899.99',
            descrizione: 'Piano cottura a induzione da 60cm con 4 zone e area flessibile. Controlli touch e timer individuale per ogni zona.',
            note_tecniche: '4 zone induzione\nZona flessibile centrale\nPotenza totale: 7200W\nControlli touch\nTimer individuale\nDimensioni: 60x52 cm',
            modalita_installazione: '1. Taglio piano cucina 56x49 cm\n2. Collegamento elettrico 380V\n3. Ventilazione sottostante\n4. Sigillatura perimetrale\n5. Test funzionamento zone',
            modalita_uso: 'Utilizzare solo pentole compatibili induzione. Pulizia con prodotti specifici per vetroceramica. Non utilizzare come piano di appoggio. Controllo touch con mani asciutte.'
        }
    };
    
    // Restituisce dati specifici per categoria o generici se non trovata
    return sampleDataMap[categoria] || {
        nome: `Prodotto ${categoria.charAt(0).toUpperCase() + categoria.slice(1)}`,
        modello: 'MOD-2024',
        prezzo: '399.99',
        descrizione: `Prodotto di qualità per la categoria ${categoria}. Caratteristiche tecniche avanzate e design moderno per soddisfare ogni esigenza domestica.`,
        note_tecniche: `Specifiche tecniche complete per ${categoria}.\nDimensioni standard.\nClasse energetica ottimale.\nGaranzia 2 anni.`,
        modalita_installazione: `Installazione standard per prodotti ${categoria}.\nSeguire le istruzioni del manuale.\nRichiedere assistenza tecnica se necessario.`,
        modalita_uso: `Utilizzo intuitive e sicuro.\nSegui le indicazioni per ${categoria}.\nManutenzione regolare consigliata.`
    };
}

$(document).ready(function() {
    // Gestione cambio categoria per aggiornare suggerimenti
    $('#categoria').on('change', function() {
        const selectedCategory = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        
        if (selectedCategory) {
            // Aggiorna placeholder con suggerimenti specifici per categoria
            updatePlaceholderForCategory(selectedCategory);
            
            // Log per debugging
            console.log(`Categoria selezionata: ${selectedText} (${selectedCategory})`);
        }
    });
});

/**
 * Aggiorna i placeholder dei campi in base alla categoria selezionata
 */
function updatePlaceholderForCategory(categoria) {
    const placeholders = {
        'lavatrice': {
            nome: 'es: Lavatrice EcoWash Pro',
            modello: 'es: EW-7000X',
            descrizione: 'Caratteristiche tecniche, capacità di carico, efficienza energetica, programmi speciali...',
            note_tecniche: 'Capacità di carico, giri centrifuga, classe energetica, dimensioni, potenza...'
        },
        'lavastoviglie': {
            nome: 'es: Lavastoviglie SilentClean',
            modello: 'es: SC-6000',
            descrizione: 'Numero coperti, silenziosità, efficienza energetica, programmi di lavaggio...',
            note_tecniche: 'Coperti, rumorosità in dB, classe energetica, dimensioni, programmi...'
        },
        'frigorifero': {
            nome: 'es: Frigorifero CoolFresh XL',
            modello: 'es: CF-400L',
            descrizione: 'Capacità, sistema No Frost, dispenser, controllo temperatura digitale...',
            note_tecniche: 'Capacità totale e per vano, classe energetica, sistema di raffreddamento...'
        }
        // Aggiungi altre categorie se necessario
    };
    
    if (placeholders[categoria]) {
        Object.keys(placeholders[categoria]).forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.placeholder = placeholders[categoria][field];
            }
        });
    }
}

/**
 * Svuota tutti i campi del form
 */
function clearForm() {
    if (confirm('Vuoi svuotare tutti i campi? I dati inseriti verranno persi.')) {
        $('#createProductForm')[0].reset();
        $('#image-preview').hide();
        $('#riepilogo-prodotto').hide();
        $('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $('.custom-validation').remove();
        
        // Reset contatori
        $('#descrizione-counter, #note-counter, #installazione-counter, #uso-counter').text('0');
        
        $('#nome').focus();
        alert('Form svuotato!');
    }
}
</script>
@endpush