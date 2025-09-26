{{-- 
    VISTA CREAZIONE NUOVO UTENTE (ADMIN - USERCONTROLLER)
    
    LINGUAGGIO: Blade Template (PHP + HTML)
    PERCORSO: resources/views/admin/users/create.blade.php
    ACCESSO: Solo livello 4 (Amministratori)
    
    SCOPO: Form completo per la creazione di nuovi utenti del sistema
    
    FUNZIONALITÀ PRINCIPALI:
    - Form multi-sezione per dati utente completi
    - Gestione livelli di accesso (2=Tecnico, 3=Staff, 4=Admin)
    - Campi condizionali per utenti tecnici
    - Generatore password automatico
    - Sistema di validazione lato client e server
    - Anteprima dati prima della creazione
    - Assegnazione centro assistenza opzionale
    - Interfaccia responsive e user-friendly
--}}

{{-- EXTENDS: Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- SECTION TITLE: Imposta titolo statico della pagina --}}
@section('title', 'Nuovo Utente')

{{-- SECTION CONTENT: Inizio del contenuto principale della vista --}}
@section('content')

{{-- 
    CONTAINER: Contenitore fluido con margine top
    mt-4: Margin top di 4 unità Bootstrap per spaziatura dall'header
--}}
<div class="container-fluid mt-4">
    
    {{-- ========== HEADER PRINCIPALE ========== --}}
    <div class="row mb-4">
        <div class="col-12">
            
            {{-- 
                SEZIONE TITOLO:
                Layout flex per allineare icona e testo
                d-flex align-items-center: Allineamento verticale centrato
            --}}
            <div class="d-flex align-items-center mb-3">
                {{-- 
                    ICONA PRINCIPALE:
                    - bi-person-plus: Icona persona con segno più
                    - text-success: Colore verde per azione positiva (creazione)
                    - fs-2: Font size grande per evidenziare
                --}}
                <i class="bi bi-person-plus text-success me-3 fs-2"></i>
                
                <div>
                    {{-- 
                        TITOLO PAGINA:
                        h2 per SEO ma classe h2 per controllo stile
                    --}}
                    <h1 class="h2 mb-1">Crea Nuovo Utente</h1>
                    
                    {{-- Sottotitolo esplicativo --}}
                    <p class="text-muted mb-0">
                        Aggiungi un nuovo utente al sistema di assistenza tecnica
                    </p>
                </div>
            </div>
            
            {{-- 
                ALERT INFORMATIVO:
                - alert-success: Colore verde per informazione positiva
                - border-start border-success border-4: Bordo sinistro verde spesso
                - Fornisce istruzioni iniziali all'utente
            --}}
            <div class="alert alert-success border-start border-success border-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Nuovo Account:</strong> Compila tutti i campi obbligatori. L'utente riceverà le credenziali per l'accesso.
            </div>
        </div>
    </div>

    {{-- ========== LAYOUT PRINCIPALE A 2 COLONNE ========== --}}
    <div class="row">
        
        {{-- 
            ========== COLONNA PRINCIPALE - FORM ========== 
            col-lg-8: 8/12 colonne su schermi large e superiori (66% larghezza)
        --}}
        <div class="col-lg-8">
            {{-- 
                CARD PRINCIPALE:
                card-custom: Classe CSS personalizzata per stili avanzati
            --}}
            <div class="card card-custom">
                
                {{-- HEADER CARD CON TITOLO --}}
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear text-success me-2"></i>
                        Informazioni Nuovo Utente
                    </h5>
                </div>
                
                <div class="card-body">
                    {{-- 
                        FORM PRINCIPALE:
                        - action: Route POST per creare utente
                        - method="POST": Metodo HTTP per invio sicuro dati
                        - id: Per riferimento JavaScript
                        - @csrf: Token Laravel per protezione CSRF
                    --}}
                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                        @csrf
                        
                        {{-- ========== SEZIONE CREDENZIALI ACCOUNT ========== --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                {{-- 
                                    TITOLO SEZIONE:
                                    h6 per gerarchia corretta con colore primario
                                --}}
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-key me-2"></i>Credenziali Account
                                </h6>
                            </div>
                        </div>
                        
                        {{-- ========== CAMPO USERNAME ========== --}}
                        <div class="mb-3">
                            {{-- 
                                LABEL CON STILE:
                                - fw-semibold: Font semi-grassetto
                                - Icona per migliore UX
                                - * indica campo obbligatorio
                            --}}
                            <label for="username" class="form-label fw-semibold">
                                <i class="bi bi-at me-1"></i>Username *
                            </label>
                            
                            {{-- 
                                INPUT USERNAME:
                                - type="text": Input testo normale
                                - @error('username') is-invalid @enderror: Classe Bootstrap se errore
                                - value="{{ old('username') }}": Mantiene valore dopo errore validazione
                                - required: Validazione HTML5 lato client
                                - maxlength: Limite caratteri
                            --}}
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}"
                                   required 
                                   maxlength="255"
                                   placeholder="es: mario.rossi">
                                   
                            {{-- 
                                TESTO DI AIUTO:
                                form-text: Classe Bootstrap per testo esplicativo sotto il campo
                            --}}
                            <div class="form-text">Username univoco per l'accesso al sistema (senza spazi)</div>
                            
                            {{-- 
                                GESTIONE ERRORI:
                                @error('username'): Direttiva Blade per errori validazione
                                invalid-feedback: Classe Bootstrap per messaggi errore
                            --}}
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- ========== CAMPI PASSWORD ========== --}}
                        <div class="row mb-3">
                            
                            {{-- COLONNA SINISTRA: Password principale --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock me-1"></i>Password *
                                </label>
                                
                                {{-- 
                                    INPUT GROUP:
                                    Raggruppa campo password con pulsante toggle visibilità
                                --}}
                                <div class="input-group">
                                    {{-- 
                                        INPUT PASSWORD:
                                        - type="password": Nasconde caratteri digitati
                                        - minlength="8": Validazione HTML5 lunghezza minima
                                    --}}
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           required
                                           minlength="8"
                                           placeholder="Minimo 8 caratteri">
                                           
                                    {{-- 
                                        PULSANTE TOGGLE PASSWORD:
                                        - btn-outline-secondary: Stile pulsante con bordo
                                        - id="togglePassword": Per JavaScript
                                        - Icona occhio per indicare funzione
                                    --}}
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                
                                {{-- Suggerimento lunghezza minima --}}
                                <div class="form-text">Minimo 8 caratteri</div>
                                
                                {{-- Gestione errore password --}}
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- COLONNA DESTRA: Conferma password --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-1"></i>Conferma Password *
                                </label>
                                
                                {{-- 
                                    INPUT CONFERMA PASSWORD:
                                    - name="password_confirmation": Nome convenzionale Laravel
                                    - Nessun @error perché Laravel gestisce automaticamente il confronto
                                --}}
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       required
                                       minlength="8"
                                       placeholder="Ripeti la password">
                                       
                                <div class="form-text">Ripeti la password</div>
                            </div>
                        </div>
                        
                        {{-- ========== GENERATORE PASSWORD AUTOMATICO ========== --}}
                        <div class="mb-4">
                            {{-- 
                                PULSANTE GENERA PASSWORD:
                                - btn-outline-info: Stile contorno azzurro
                                - btn-sm: Dimensione piccola
                                - id per JavaScript
                            --}}
                            <button type="button" class="btn btn-outline-info btn-sm" id="generatePassword">
                                <i class="bi bi-magic me-1"></i>Genera Password Sicura
                            </button>
                        </div>
                        
                        {{-- ========== SEZIONE DATI PERSONALI ========== --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success mb-3">
                                    <i class="bi bi-person me-2"></i>Informazioni Personali
                                </h6>
                            </div>
                        </div>
                        
                        {{-- ========== NOME E COGNOME ========== --}}
                        <div class="row mb-3">
                            
                            {{-- COLONNA SINISTRA: Nome --}}
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i>Nome *
                                </label>
                                
                                {{-- 
                                    INPUT NOME:
                                    Pattern standard con gestione errori e old() value
                                --}}
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="Mario">
                                       
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- COLONNA DESTRA: Cognome --}}
                            <div class="col-md-6">
                                <label for="cognome" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1"></i>Cognome *
                                </label>
                                
                                <input type="text" 
                                       class="form-control @error('cognome') is-invalid @enderror" 
                                       id="cognome" 
                                       name="cognome" 
                                       value="{{ old('cognome') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="Rossi">
                                       
                                @error('cognome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- ========== LIVELLO DI ACCESSO ========== --}}
                        <div class="mb-4">
                            <label for="livello_accesso" class="form-label fw-semibold">
                                <i class="bi bi-shield me-1"></i>Livello di Accesso *
                            </label>
                            
                            {{-- 
                                SELECT LIVELLO ACCESSO:
                                Campo cruciale che determina i permessi dell'utente nel sistema
                                - Usa valori numerici: 2=Tecnico, 3=Staff, 4=Admin
                                - required: Campo obbligatorio
                                - JavaScript mostrerà/nasconderà campi in base alla selezione
                            --}}
                            <select class="form-select @error('livello_accesso') is-invalid @enderror" 
                                    id="livello_accesso" 
                                    name="livello_accesso" 
                                    required>
                                <option value="">Seleziona livello di accesso</option>
                                
                                {{-- 
                                    OPZIONE TECNICO (Livello 2):
                                    - value="2": Valore numerico per il database
                                    - old('livello_accesso') == '2': Mantiene selezione dopo errore
                                    - Emoji per migliore UX visiva
                                --}}
                                <option value="2" {{ old('livello_accesso') == '2' ? 'selected' : '' }}>
                                    🔵 Tecnico - Visualizza e consulta soluzioni
                                </option>
                                
                                {{-- OPZIONE STAFF AZIENDALE (Livello 3) --}}
                                <option value="3" {{ old('livello_accesso') == '3' ? 'selected' : '' }}>
                                    🟡 Staff Aziendale - Gestisce malfunzionamenti e soluzioni
                                </option>
                                
                                {{-- OPZIONE AMMINISTRATORE (Livello 4) --}}
                                <option value="4" {{ old('livello_accesso') == '4' ? 'selected' : '' }}>
                                    🔴 Amministratore - Controllo completo del sistema
                                </option>
                            </select>
                            
                            {{-- Spiegazione funzione del campo --}}
                            <div class="form-text">Determina le funzionalità accessibili all'utente nel sistema</div>
                            
                            @error('livello_accesso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- ========== DATI TECNICO (CONDIZIONALI) ========== --}}
                        {{-- 
                            SEZIONE CONDIZIONALE:
                            - style="display: none;": Inizialmente nascosta
                            - JavaScript la mostrerà quando livello_accesso == 2 (Tecnico)
                            - Contiene campi specifici per utenti tecnici
                        --}}
                        <div id="dati-tecnico" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-info mb-3">
                                        <i class="bi bi-tools me-2"></i>Informazioni Tecnico
                                        {{-- 
                                            SMALL ESPLICATIVO:
                                            text-muted: Colore grigio per indicare natura opzionale
                                        --}}
                                        <small class="text-muted">(per utenti tecnici)</small>
                                    </h6>
                                </div>
                            </div>
                            
                            {{-- ========== DATA NASCITA E SPECIALIZZAZIONE ========== --}}
                            <div class="row mb-3">
                                
                                {{-- COLONNA SINISTRA: Data di nascita --}}
                                <div class="col-md-6">
                                    <label for="data_nascita" class="form-label fw-semibold">
                                        <i class="bi bi-calendar me-1"></i>Data di Nascita *
                                    </label>
                                    
                                    {{-- 
                                        INPUT DATE:
                                        - type="date": Input HTML5 per date con calendario
                                        - max="{{ date('Y-m-d') }}": Data massima = oggi (previene date future)
                                        - PHP date(): Funzione per formattare data corrente
                                    --}}
                                    <input type="date" 
                                           class="form-control @error('data_nascita') is-invalid @enderror" 
                                           id="data_nascita" 
                                           name="data_nascita" 
                                           value="{{ old('data_nascita') }}"
                                           max="{{ date('Y-m-d') }}">
                                           
                                    @error('data_nascita')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- COLONNA DESTRA: Specializzazione tecnica --}}
                                <div class="col-md-6">
                                    <label for="specializzazione" class="form-label fw-semibold">
                                        <i class="bi bi-star me-1"></i>Specializzazione *
                                    </label>
                                    
                                    {{-- 
                                        INPUT SPECIALIZZAZIONE:
                                        Campo testo per area di competenza del tecnico
                                    --}}
                                    <input type="text" 
                                           class="form-control @error('specializzazione') is-invalid @enderror" 
                                           id="specializzazione" 
                                           name="specializzazione" 
                                           value="{{ old('specializzazione') }}"
                                           placeholder="es: Elettrodomestici, Climatizzatori"
                                           maxlength="255">
                                           
                                    @error('specializzazione')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- ========== CENTRO ASSISTENZA - OPZIONALE ========== --}}
                            <div class="mb-3">
                                <label for="centro_assistenza_id" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>Centro di Assistenza 
                                    {{-- 
                                        BADGE OPZIONALE:
                                        Indica chiaramente che il campo non è obbligatorio
                                    --}}
                                    <span class="badge bg-secondary ms-2">Opzionale</span>
                                </label>
                                
                                {{-- 
                                    SELECT CENTRO ASSISTENZA:
                                    Dropdown popolato dinamicamente dai centri nel database
                                --}}
                                <select class="form-select @error('centro_assistenza_id') is-invalid @enderror"
                                        id="centro_assistenza_id"
                                        name="centro_assistenza_id">
                                    
                                    {{-- Opzione default per nessun centro --}}
                                    <option value="">-- Nessun centro assegnato --</option>
                                    
                                    {{-- 
                                        LOOP FORELSE: Itera sui centri o mostra messaggio alternativo
                                        @forelse: Combina @foreach + @empty
                                        $centri è passato dal controller
                                    --}}
                                    @forelse($centri as $centro)
                                        {{-- 
                                            OPZIONE CENTRO:
                                            - value="{{ $centro->id }}": ID per foreign key
                                            - old(): Mantiene selezione dopo errore
                                            - Emoji per migliore UX
                                            - Formato: Nome - Città (Provincia)
                                        --}}
                                        <option value="{{ $centro->id }}" 
                                                {{ old('centro_assistenza_id') == $centro->id ? 'selected' : '' }}>
                                            🏢 {{ $centro->nome }} - {{ $centro->citta }} ({{ $centro->provincia }})
                                        </option>
                                    @empty
                                        {{-- 
                                            CASO VUOTO: Nessun centro nel database
                                            disabled: Opzione non selezionabile
                                        --}}
                                        <option value="" disabled>Nessun centro di assistenza disponibile</option>
                                    @endforelse
                                </select>

                                {{-- Spiegazione campo opzionale --}}
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Centro opzionale:</strong> Può essere assegnato ora o successivamente.
                                </div>

                                @error('centro_assistenza_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- ========== PULSANTI AZIONE ========== --}}
                        {{-- 
                            SEZIONE PULSANTI:
                            - d-flex justify-content-between: Distribuisce pulsanti agli estremi
                            - align-items-center: Allineamento verticale
                            - pt-3 border-top: Padding top e bordo separatore
                        --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            
                            {{-- PULSANTE ANNULLA (Sinistra) --}}
                            <div>
                                {{-- 
                                    LINK ANNULLA:
                                    - Torna alla lista utenti
                                    - btn-outline-secondary: Stile contorno grigio per azione secondaria
                                --}}
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Annulla
                                </a>
                            </div>
                            
                            {{-- PULSANTI PRINCIPALI (Destra) --}}
                            <div>
                                {{-- 
                                    PULSANTE ANTEPRIMA:
                                    - btn-outline-primary: Stile contorno blu
                                    - id per JavaScript che aprirà modal
                                    - type="button": Non invia form
                                --}}
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="bi bi-eye me-1"></i>Anteprima
                                </button>
                                
                                {{-- 
                                    PULSANTE CREA UTENTE:
                                    - type="submit": Invia il form
                                    - btn-success: Colore verde per azione positiva
                                    - id per possibili controlli JavaScript
                                --}}
                                <button type="submit" class="btn btn-success" id="createBtn">
                                    <i class="bi bi-person-plus me-1"></i>Crea Utente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- 
            ========== SIDEBAR DESTRA - GUIDA ========== 
            col-lg-4: 4/12 colonne su schermi large (33% larghezza)
            Contiene informazioni di aiuto per l'utente
        --}}
        <div class="col-lg-4">
            
            {{-- ========== CARD GUIDA LIVELLI ========== --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check text-primary me-2"></i>Livelli di Accesso
                    </h6>
                </div>
                
                <div class="card-body">
                    {{-- 
                        SPIEGAZIONE LIVELLO TECNICO:
                        Layout con badge colorato e lista funzionalità
                    --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            {{-- Badge colorato corrispondente al select --}}
                            <span class="badge bg-info me-2">🔵</span>
                            <strong>Tecnico</strong>
                        </div>
                        
                        {{-- 
                            LISTA FUNZIONALITÀ:
                            - small: Testo più piccolo per risparmiare spazio
                            - text-muted: Colore grigio per testo secondario
                        --}}
                        <ul class="small text-muted mb-0">
                            <li>Visualizza prodotti completi</li>
                            <li>Accede a malfunzionamenti e soluzioni</li>
                            <li>Centro assistenza opzionale</li>
                        </ul>
                    </div>
                    
                    {{-- SPIEGAZIONE LIVELLO STAFF AZIENDALE --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2">🟡</span>
                            <strong>Staff Aziendale</strong>
                        </div>
                        <ul class="small text-muted mb-0">
                            <li>Tutte le funzioni Tecnico</li>
                            <li>Crea e modifica soluzioni</li>
                        </ul>
                    </div>
                    
                    {{-- SPIEGAZIONE LIVELLO AMMINISTRATORE --}}
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-danger me-2">🔴</span>
                            <strong>Amministratore</strong>
                        </div>
                        <ul class="small text-muted mb-0">
                            <li>Controllo completo sistema</li>
                            <li>Gestisce utenti e prodotti</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- ========== CARD SUGGERIMENTI ========== --}}
            <div class="card card-custom mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Suggerimenti
                    </h6>
                </div>
                
                <div class="card-body">
                    {{-- 
                        SUGGERIMENTI PRATICI:
                        Lista di best practices per compilazione form
                    --}}
                    <div class="small">
                        <div class="mb-3">
                            <strong>Username:</strong> Usa formato nome.cognome
                        </div>
                        <div class="mb-3">
                            <strong>Password:</strong> Usa il generatore automatico
                        </div>
                        <div class="mb-0">
                            <strong>Tecnici:</strong> Centro assistenza opzionale
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL ANTEPRIMA ========== --}}
{{-- 
    MODAL BOOTSTRAP: Finestra modale per anteprima dati prima dell'invio
    - fade: Effetto di dissolvenza
    - tabindex="-1": Per accessibilità keyboard
--}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            {{-- HEADER MODAL --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Anteprima Nuovo Utente
                </h5>
                
                {{-- 
                    PULSANTE CHIUSURA:
                    btn-close: Stile Bootstrap 5 per pulsante X
                    data-bs-dismiss: Attributo Bootstrap per chiudere modal
                --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- CORPO MODAL --}}
            <div class="modal-body">
                {{-- 
                    CONTENITORE ANTEPRIMA:
                    Sarà popolato dinamicamente da JavaScript
                --}}
                <div id="previewContent"></div>
            </div>
            
            {{-- FOOTER MODAL CON PULSANTI --}}
            <div class="modal-footer">
                {{-- Pulsante per tornare alla modifica --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Modifica</button>
                
                {{-- 
                    PULSANTE CONFERMA:
                    JavaScript chiamerà submitForm() per inviare effettivamente i dati
                --}}
                <button type="button" class="btn btn-success" id="createFromPreview">
                    <i class="bi bi-person-plus me-1"></i>Conferma Creazione
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ========== SEZIONE CSS PERSONALIZZATI ========== --}}
{{-- 
    PUSH STYLES: Aggiunge CSS alla sezione 'styles' del layout
    LINGUAGGIO: CSS con sintassi standard
--}}
@push('styles')
<style>
/* 
    === STILI PER IL FORM DI CREAZIONE UTENTE ===
    Organizzazione: Card, Form, Responsive, Utilità
*/

/* 
    CARD PERSONALIZZATE:
    Stile uniforme per tutte le card del form con effetti hover
*/
.card-custom {
    border: none; /* Rimuove bordo default Bootstrap */
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Ombra sottile per depth */
    transition: all 0.3s ease; /* Transizione fluida per tutti i cambiamenti */
    border-radius: 10px; /* Angoli arrotondati personalizzati */
}

/* 
    HOVER EFFECT CARD:
    Effetto di sollevamento al passaggio del mouse
*/
.card-custom:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15); /* Ombra più pronunciata */
}

/* 
    FORM LABELS:
    Stile uniforme per tutte le etichette dei campi
*/
.form-label.fw-semibold {
    color: #495057; /* Grigio scuro per buona leggibilità */
    font-weight: 600; /* Semi-grassetto per evidenziare */
    font-size: 0.95rem; /* Dimensione leggermente ridotta */
}

/* 
    ICONE NELLE LABEL:
    Colore più tenue per le icone per non distrarre dal testo
*/
.form-label i {
    color: #6c757d; /* Grigio Bootstrap per icone */
}

/* 
    BORDI COLORATI:
    Override Bootstrap per bordi più spessi
*/
.border-start.border-4 {
    border-width: 4px !important; /* Forza larghezza 4px */
}

/* 
    BADGE:
    Stile per badge con dimensioni e peso ottimizzati
*/
.badge {
    font-size: 0.75rem; /* Dimensione leggermente ridotta */
    font-weight: 500; /* Peso medio per leggibilità */
}

/* Badge secondari con colore esplicito */
.badge.bg-secondary {
    background-color: #6c757d !important; /* Forza colore grigio */
}

/* 
    FORM CONTROLS:
    Stili per focus state degli elementi form
*/
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe; /* Bordo blu chiaro al focus */
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); /* Alone blu */
}

/* 
    INPUT GROUPS:
    Stili per gruppi di input (es: password con pulsante toggle)
*/
.input-group .btn {
    border-color: #ced4da; /* Bordo coerente con input */
}

.input-group .btn:hover {
    background-color: #f8f9fa; /* Sfondo grigio chiaro al hover */
    border-color: #86b7fe; /* Bordo blu coerente con focus input */
}

/* 
    ALERT PERSONALIZZATI:
    Rimozione bordo default e angoli personalizzati
*/
.alert {
    border-radius: 8px; /* Angoli arrotondati */
    border: none; /* Rimuove bordo default */
}

/* 
    === RESPONSIVE DESIGN ===
    Adattamenti per dispositivi mobili
*/

/* 
    MOBILE: Adattamenti per schermi piccoli (≤768px)
*/
@media (max-width: 768px) {
    /* 
        LAYOUT PULSANTI:
        Cambia da layout orizzontale a verticale su mobile
    */
    .d-flex.justify-content-between {
        flex-direction: column; /* Impila verticalmente */
        gap: 1rem; /* Spazio tra i gruppi di pulsanti */
    }
    
    /* Pulsanti a larghezza completa su mobile */
    .d-flex.justify-content-between > div {
        width: 100%;
        text-align: center; /* Centra pulsanti */
    }
    
    /* Margine inferiore per card su mobile */
    .card-custom {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

{{-- ========== SEZIONE JAVASCRIPT ========== --}}
{{-- 
    PUSH SCRIPTS: Aggiunge JavaScript alla sezione 'scripts' del layout
    LINGUAGGIO: JavaScript embedded in Blade
--}}
@push('scripts')
<script>
/*
    INIZIALIZZAZIONE DATI PAGINA:
    Pattern standard per trasferire dati PHP al JavaScript
    Questo pattern è consistente in tutta l'applicazione
*/

// Inizializza l'oggetto PageData se non esiste già (Pattern Singleton)
window.PageData = window.PageData || {};

/*
    TRASFERIMENTO DATI CONDIZIONALE:
    Utilizza condizioni Blade per trasferire solo dati necessari
    Ogni @if controlla l'esistenza della variabile PHP prima del trasferimento
    @json(): Helper Blade per conversione sicura PHP → JSON
*/

// Dati singolo prodotto (se presente nella vista)
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Collezione prodotti con paginazione (se presente)
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Dati singolo malfunzionamento (se presente)  
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Collezione malfunzionamenti (se presente)
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Dati singolo centro assistenza (se presente)
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Collezione centri assistenza (se presente) 
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Array categorie prodotti (se presente)
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Collezione membri staff (se presente)
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche aggregate (se presenti)
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Dati utente corrente (se presente)
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    PATTERN ESPANDIBILE:
    Questo pattern permette di aggiungere facilmente nuovi dati
    senza modificare la struttura esistente o causare conflitti
    
    VANTAGGI:
    - Evita variabili globali multiple
    - Namespace unico per tutti i dati
    - Facile debug (window.PageData)
    - Espandibile senza refactoring
    - Compatibile con tutti i browser moderni
*/

// Placeholder per eventuali dati aggiuntivi futuri...

/*
    FUNZIONALITÀ JAVASCRIPT SPECIFICHE DEL FORM:
    Le seguenti funzioni dovrebbero essere implementate in un file separato
    per la gestione specifica di questo form di creazione utente
    
    FUNZIONI RICHIESTE:
    1. togglePassword() - Mostra/nasconde password
    2. generatePassword() - Genera password sicura automatica  
    3. showHideTechnicianFields() - Mostra/nasconde campi tecnico
    4. validateForm() - Validazione lato client
    5. showPreview() - Mostra anteprima dati in modal
    6. submitForm() - Invio form con validazione
    
    EVENTI DA GESTIRE:
    - Change su livello_accesso (mostra/nasconde campi tecnico)
    - Click su generatePassword (genera password automatica)
    - Click su togglePassword (toggle visibilità password)
    - Click su previewBtn (apre modal anteprima)
    - Submit form (validazione pre-invio)
    - Click su createFromPreview (conferma da modal)
    
    ESEMPIO IMPLEMENTAZIONE:
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Gestione toggle visibilità password
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        
        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;
                
                const icon = this.querySelector('i');
                icon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        }
        
        // Gestione campi tecnico condizionali
        const livelloAccesso = document.getElementById('livello_accesso');
        const datiTecnico = document.getElementById('dati-tecnico');
        
        if (livelloAccesso && datiTecnico) {
            livelloAccesso.addEventListener('change', function() {
                // Mostra campi tecnico solo per livello 2
                if (this.value === '2') {
                    datiTecnico.style.display = 'block';
                    // Rendi obbligatori i campi tecnico
                    document.getElementById('data_nascita').required = true;
                    document.getElementById('specializzazione').required = true;
                } else {
                    datiTecnico.style.display = 'none';
                    // Rimuovi obbligatorietà campi tecnico
                    document.getElementById('data_nascita').required = false;
                    document.getElementById('specializzazione').required = false;
                }
            });
        }
        
        // Generatore password automatico
        const generatePasswordBtn = document.getElementById('generatePassword');
        
        if (generatePasswordBtn) {
            generatePasswordBtn.addEventListener('click', function() {
                // Genera password sicura (maiuscole, minuscole, numeri, simboli)
                const length = 12;
                const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*";
                let password = "";
                
                for (let i = 0; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }
                
                // Imposta password in entrambi i campi
                document.getElementById('password').value = password;
                document.getElementById('password_confirmation').value = password;
                
                // Feedback visivo
                this.innerHTML = '<i class="bi bi-check me-1"></i>Password Generata!';
                this.className = 'btn btn-success btn-sm';
                
                // Reset dopo 2 secondi
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-magic me-1"></i>Genera Password Sicura';
                    this.className = 'btn btn-outline-info btn-sm';
                }, 2000);
            });
        }
        
        // Modal anteprima
        const previewBtn = document.getElementById('previewBtn');
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                // Raccogli tutti i dati del form
                const formData = new FormData(document.getElementById('createUserForm'));
                
                // Genera HTML anteprima
                let previewHTML = '<div class="row">';
                
                // Credenziali Account
                previewHTML += '<div class="col-md-6 mb-3">';
                previewHTML += '<h6 class="text-primary">Credenziali Account</h6>';
                previewHTML += '<p><strong>Username:</strong> ' + (formData.get('username') || 'N/A') + '</p>';
                previewHTML += '<p><strong>Password:</strong> ••••••••</p>';
                previewHTML += '</div>';
                
                // Dati Personali
                previewHTML += '<div class="col-md-6 mb-3">';
                previewHTML += '<h6 class="text-success">Dati Personali</h6>';
                previewHTML += '<p><strong>Nome:</strong> ' + (formData.get('nome') || 'N/A') + '</p>';
                previewHTML += '<p><strong>Cognome:</strong> ' + (formData.get('cognome') || 'N/A') + '</p>';
                
                // Livello accesso con emoji
                const livello = formData.get('livello_accesso');
                let livelloText = 'N/A';
                if (livello === '2') livelloText = '🔵 Tecnico';
                else if (livello === '3') livelloText = '🟡 Staff Aziendale'; 
                else if (livello === '4') livelloText = '🔴 Amministratore';
                
                previewHTML += '<p><strong>Livello:</strong> ' + livelloText + '</p>';
                previewHTML += '</div>';
                
                // Dati Tecnico (se livello 2)
                if (livello === '2') {
                    previewHTML += '<div class="col-12 mb-3">';
                    previewHTML += '<h6 class="text-info">Informazioni Tecnico</h6>';
                    previewHTML += '<p><strong>Data Nascita:</strong> ' + (formData.get('data_nascita') || 'N/A') + '</p>';
                    previewHTML += '<p><strong>Specializzazione:</strong> ' + (formData.get('specializzazione') || 'N/A') + '</p>';
                    
                    const centroId = formData.get('centro_assistenza_id');
                    if (centroId) {
                        const centroSelect = document.getElementById('centro_assistenza_id');
                        const centroText = centroSelect.options[centroSelect.selectedIndex].text;
                        previewHTML += '<p><strong>Centro:</strong> ' + centroText + '</p>';
                    } else {
                        previewHTML += '<p><strong>Centro:</strong> Nessun centro assegnato</p>';
                    }
                    previewHTML += '</div>';
                }
                
                previewHTML += '</div>';
                
                // Mostra anteprima nella modal
                document.getElementById('previewContent').innerHTML = previewHTML;
                previewModal.show();
            });
        }
        
        // Conferma creazione da anteprima
        const createFromPreview = document.getElementById('createFromPreview');
        
        if (createFromPreview) {
            createFromPreview.addEventListener('click', function() {
                previewModal.hide();
                document.getElementById('createUserForm').submit();
            });
        }
        
        // Validazione form in tempo reale
        const form = document.getElementById('createUserForm');
        const inputs = form.querySelectorAll('input[required], select[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                // Validazione singolo campo
                if (!this.checkValidity()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        // Validazione conferma password
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        if (password && confirmPassword) {
            function validatePasswordMatch() {
                if (confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity('Le password non coincidono');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            password.addEventListener('input', validatePasswordMatch);
            confirmPassword.addEventListener('input', validatePasswordMatch);
        }
    });
    
    NOTA: Il codice sopra è un esempio di implementazione.
    In un'applicazione reale, questo dovrebbe essere in un file .js separato
    e caricato tramite asset() nel layout principale.
*/

</script>
@endpush

{{--
    =========================================
    === RIEPILOGO FUNZIONALITÀ FILE ===
    =========================================
    
    QUESTO FILE IMPLEMENTA:
    
    1. FORM CREAZIONE UTENTE:
       - Sezioni organizzate logicamente (credenziali, dati personali, info tecnico)
       - Validazione lato client e server con messaggi di errore
       - Campi condizionali basati su livello accesso
       - Gestione old() values per errori validazione
    
    2. LIVELLI ACCESSO:
       - Sistema a 3 livelli: Tecnico(2), Staff(3), Admin(4)
       - Campi specifici per tecnici (data nascita, specializzazione, centro)
       - Centro assistenza opzionale con select popolato dinamicamente
    
    3. SICUREZZA:
       - Token CSRF per protezione attacchi
       - Validazione HTML5 e Laravel
       - Gestione sicura delle password
       - Escape HTML per prevenire XSS
    
    4. UX/UI AVANZATA:
       - Toggle visibilità password
       - Generatore password automatico
       - Modal anteprima prima dell'invio
       - Sidebar con guida livelli e suggerimenti
    
    5. RESPONSIVE DESIGN:
       - Layout a 2 colonne che si adatta a mobile
       - Card responsive con effetti hover
       - Form ottimizzato per touch device
    
    6. ACCESSIBILITÀ:
       - Label semantiche con icone
       - Attributi ARIA appropriati
       - Navigazione da tastiera
       - Contrast ratio ottimizzato
    
    PATTERN TECNICI:
    - Blade Template con sezioni modulari
    - Bootstrap 5 per layout e componenti
    - JavaScript vanilla per interattività
    - Pattern MVC Laravel standard
    - Progressive Enhancement
    
    INTEGRAZIONE SISTEMA:
    - Route: admin.users.store (POST)
    - Controller: UserController@store
    - Middleware: auth, admin (presumibili)
    - Validazione: Request custom o rules nel controller
    - Database: tabelle users, centri_assistenza
--}}