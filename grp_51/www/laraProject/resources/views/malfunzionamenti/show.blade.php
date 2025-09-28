{{-- 
    SEZIONE JAVASCRIPT INIZIALE
    JavaScript: URL API per chiamate AJAX alla funzionalità segnalazioni
    Posizionato in cima per essere disponibile immediatamente
--}}
@push('scripts')
<script>
// JavaScript: URL dell'API per le operazioni sui malfunzionamenti
window.apiMalfunzionamentiUrl = "{{ url('/api/malfunzionamenti') }}";
</script>
@endpush

{{-- 
    Vista per visualizzare un singolo malfunzionamento con la sua soluzione completa
    LINGUAGGIO: Blade Template (Laravel) - vista dettaglio per singolo malfunzionamento
    SCOPO: Mostra informazioni complete di problema e soluzione con interazioni utente
    ACCESSO: Solo tecnici (livello 2+) e staff (livello 3+) autenticati
    PERCORSO: resources/views/malfunzionamenti/show.blade.php
    VERSIONE: Corretta - Fix duplicazioni e funzionalità segnalazione
--}}

{{-- Estende il layout principale dell'applicazione --}}
@extends('layouts.app')

{{-- 
    Titolo dinamico della pagina per SEO
    Blade: Concatenazione con proprietà oggetto Eloquent
--}}
@section('title', $malfunzionamento->titolo . ' - Soluzione')

{{-- Inizio sezione contenuto principale --}}
@section('content')

{{-- Container Bootstrap per layout responsive --}}
<div class="container mt-4">

    {{-- Layout principale a due colonne --}}
    <div class="row">
        
        {{-- 
            COLONNA PRINCIPALE: CONTENUTO MALFUNZIONAMENTO
            Bootstrap: col-lg-8 = 8 colonne su 12 per contenuto principale
        --}}
        <div class="col-lg-8">
            
            {{-- 
                Card principale del malfunzionamento con header colorato dinamico
                CSS: Colori header basati su gravità del problema
            --}}
            <div class="card mb-4 border-0 shadow-sm">
                {{-- 
                    Header card con colore dinamico basato su gravità
                    Blade: @switch per logica condizionale multipla
                    CSS: Classi Bootstrap per colori background
                --}}
                <div class="card-header 
                    @switch($malfunzionamento->gravita)
                        @case('critica') bg-danger text-white @break
                        @case('alta') bg-warning text-dark @break
                        @case('media') bg-info text-white @break
                        @default bg-light text-dark
                    @endswitch
                ">
                    {{-- Layout flex per distribuire elementi agli estremi --}}
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- 
                            Badge gravità con colori invertiti per contrasto
                            Switch annidato per determinare colori badge
                        --}}
                        <span class="badge 
                            @switch($malfunzionamento->gravita)
                                @case('critica') bg-light text-danger @break
                                @case('alta') bg-light text-warning @break
                                @case('media') bg-light text-info @break
                                @default bg-dark text-light
                            @endswitch
                            fs-6 px-3 py-2">
                            {{-- Testo badge basato su gravità --}}
                            @switch($malfunzionamento->gravita)
                                @case('critica') CRITICA @break
                                @case('alta') ALTA @break
                                @case('media') MEDIA @break
                                @default BASSA
                            @endswitch
                        </span>
                        
                        {{-- 
                            Contatore segnalazioni con ID per aggiornamenti JavaScript
                            HTML: id="segnalazioni-counter" per targeting JavaScript
                            PHP: ?? operatore null coalescing per valore di default
                        --}}
                        <div class="text-end">
                            <span class="badge bg-light text-dark" id="segnalazioni-counter">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $malfunzionamento->numero_segnalazioni ?? 0 }} segnalazioni
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Corpo principale della card --}}
                <div class="card-body">
                    {{-- 
                        Titolo principale del malfunzionamento
                        HTML: h1 con classe h3 per dimensione ottimale
                    --}}
                    <h1 class="h3 card-title mb-3">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        {{ $malfunzionamento->titolo }}
                    </h1>
                    
                    {{-- 
                        SEZIONE: DESCRIZIONE DEL PROBLEMA
                        Layout strutturato per leggibilità
                    --}}
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="bi bi-info-circle me-2"></i>Descrizione del Problema
                        </h5>
                        {{-- Container con sfondo grigio per evidenziare contenuto --}}
                        <div class="bg-light rounded p-3">
                            <p class="mb-0">{{ $malfunzionamento->descrizione }}</p>
                        </div>
                    </div>
                    
                    {{-- 
                        SEZIONE: SOLUZIONE TECNICA (PRINCIPALE)
                        Sezione più importante della vista con styling dedicato
                    --}}
                    <div class="mb-4">
                        <h5 class="text-success">
                            <i class="bi bi-tools me-2"></i>Soluzione Tecnica
                        </h5>
                        {{-- 
                            Container con bordo verde per evidenziare soluzione
                            CSS: border-start border-success border-3 per bordo sinistro colorato
                        --}}
                        <div class="border-start border-success border-3 ps-3">
                            {{-- Condizionale per presenza soluzione --}}
                            @if($malfunzionamento->soluzione)
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    {{-- 
                                        Elaborazione testo soluzione per paragrafi
                                        PHP: explode() per dividere su newline
                                        Blade: @php per logica complessa
                                    --}}
                                    @php
                                        $soluzione_paragrafi = explode("\n", $malfunzionamento->soluzione);
                                    @endphp
                                    
                                    {{-- 
                                        Iterazione sui paragrafi per formattazione migliore
                                        PHP: trim() per rimuovere spazi
                                    --}}
                                    @foreach($soluzione_paragrafi as $paragrafo)
                                        @if(trim($paragrafo) !== '')
                                            <p class="mb-2">{{ trim($paragrafo) }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                {{-- Alert per soluzione mancante --}}
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Soluzione non ancora disponibile per questo malfunzionamento.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- 
                        SEZIONE: STRUMENTI NECESSARI (OPZIONALE)
                        Mostrata solo se field popolato
                    --}}
                    @if($malfunzionamento->strumenti_necessari)
                        <div class="mb-4">
                            <h6 class="text-info">
                                <i class="bi bi-tools me-2"></i>Strumenti Necessari
                            </h6>
                            {{-- Container con sfondo azzurro trasparente --}}
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <p class="mb-0">{{ $malfunzionamento->strumenti_necessari }}</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- 
                        SEZIONE: INFORMAZIONI TECNICHE DETTAGLIATE
                        Layout a card per metriche chiave
                    --}}
                    <div class="row g-3 mb-4">
                        {{-- Card difficoltà --}}
                        <div class="col-sm-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-speedometer text-primary fs-4 d-block mb-1"></i>
                                    <strong>Difficoltà</strong>
                                    {{-- PHP: ucfirst() per capitalizzare prima lettera --}}
                                    <div class="text-muted">{{ ucfirst($malfunzionamento->difficolta) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Card tempo stimato (solo se disponibile) --}}
                        @if($malfunzionamento->tempo_stimato)
                            <div class="col-sm-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-clock text-success fs-4 d-block mb-1"></i>
                                        <strong>Tempo Stimato</strong>
                                        <div class="text-muted">{{ $malfunzionamento->tempo_stimato }} minuti</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- 
                        SEZIONE: PULSANTI DI AZIONE
                        Layout flex responsive per azioni utente
                    --}}
                    <div class="d-flex gap-2 flex-wrap">
                        
                        {{-- Pulsante torna all'elenco --}}
                        <a href="{{ route('malfunzionamenti.index', $prodotto) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-exclamation-circle me-1"></i>Malfunzionamenti di: {{ $prodotto->nome }}
                        </a>
                        
                        {{-- 
                            PULSANTE SEGNALAZIONE (per tecnici e staff)
                            Sistema di autorizzazioni Laravel
                        --}}
                        @auth
                            {{-- 
                                Laravel: Controllo autorizzazione con metodo custom nel model User
                                Solo utenti con permessi di visualizzazione malfunzionamenti
                            --}}
                            @if(auth()->user()->canViewMalfunzionamenti())
                                <button type="button" 
                                        class="btn btn-outline-warning btn-sm segnala-btn"
                                        onclick="segnalaMalfunzionamento('{{ $malfunzionamento->id }}')"
                                        title="Segnala di aver riscontrato questo problema">
                                    <i class="bi bi-plus-circle me-1"></i>Ho Questo Problema
                                </button>
                            @endif
                        @endauth
                        
                        {{-- 
                            PULSANTI GESTIONE (solo per staff)
                            Modifica ed eliminazione per utenti autorizzati
                        --}}
                        @auth
                            @if(auth()->user()->canManageMalfunzionamenti())
                                {{-- Pulsante modifica --}}
                                <a href="{{ route('staff.malfunzionamenti.edit', [$malfunzionamento]) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Modifica Soluzione
                                </a>
                                
                                {{-- 
                                    Form eliminazione con conferma JavaScript
                                    Laravel: @method('DELETE') per RESTful routing
                                --}}
                                <form action="{{ route('staff.malfunzionamenti.destroy', [$prodotto, $malfunzionamento]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i>Elimina
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            
        </div>
        
        {{-- 
            SIDEBAR DESTRA: INFORMAZIONI CORRELATE
            Bootstrap: col-lg-4 = 4 colonne per sidebar informativa
        --}}
        <div class="col-lg-4">
            
            {{-- 
                CARD: INFORMAZIONI PRODOTTO
                Dettagli del prodotto associato al malfunzionamento
            --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-box me-2"></i>Informazioni Prodotto
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Layout con immagine e dettagli prodotto --}}
                    <div class="d-flex align-items-center mb-3">
                        {{-- 
                            Immagine prodotto con fallback
                            Laravel: Condizionale per presenza foto
                        --}}
                        @if($prodotto->foto)
                            {{-- 
                                Immagine reale del prodotto
                                Laravel: asset() helper per URL storage
                                CSS: object-fit: cover per mantenere proporzioni
                            --}}
                            <img src="{{ asset('storage/' . $prodotto->foto) }}" 
                                 class="rounded me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;"
                                 alt="{{ $prodotto->nome }}">
                        @else
                            {{-- Placeholder per prodotti senza immagine --}}
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                        @endif
                        
                        {{-- Nome e modello prodotto --}}
                        <div>
                            <h6 class="mb-1">
                                {{-- 
                                    Link al prodotto con autorizzazioni differenziate
                                    Laravel: Sistema di routing basato su permessi utente
                                --}}
                                @auth
                                    @if(auth()->user()->canViewMalfunzionamenti())
                                        {{-- Link a vista completa per utenti autorizzati --}}
                                        <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="text-decoration-none">
                                            {{ $prodotto->nome }}
                                        </a>
                                    @else
                                        {{-- Link a vista base per utenti non autorizzati --}}
                                        <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                                            {{ $prodotto->nome }}
                                        </a>
                                    @endif
                                @else
                                    {{-- Link pubblico per utenti non autenticati --}}
                                    <a href="{{ route('prodotti.show', $prodotto) }}" class="text-decoration-none">
                                        {{ $prodotto->nome }}
                                    </a>
                                @endauth
                            </h6>
                            {{-- Modello prodotto (se disponibile) --}}
                            @if($prodotto->modello)
                                <small class="text-muted">Modello: {{ $prodotto->modello }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- 
                        Pulsante dettagli prodotto con badge informativi
                        Diversificato per tipo di accesso utente
                    --}}
                    <div class="text-center">
                        @auth
                            @if(auth()->user()->canViewMalfunzionamenti())
                                {{-- Pulsante per vista completa (tecnici/staff) --}}
                                <a href="{{ route('prodotti.completo.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>Vedi Dettagli Completi
                                    <span class="badge bg-warning text-dark ms-1">Con Malfunzionamenti</span>
                                </a>
                            @else
                                {{-- Pulsante per vista base (utenti base) --}}
                                <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>Vedi Dettagli Prodotto
                                    <span class="badge bg-info ms-1">Vista Base</span>
                                </a>
                            @endif
                        @else
                            {{-- Pulsante per vista pubblica (non autenticati) --}}
                            <a href="{{ route('prodotti.show', $prodotto) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye me-1"></i>Vedi Dettagli Prodotto
                                <span class="badge bg-secondary ms-1">Pubblico</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD: CRONOLOGIA E METADATA
                Informazioni storiche e di tracking del malfunzionamento
            --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Cronologia
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Layout small per informazioni compatte --}}
                    <div class="small">
                        {{-- Prima segnalazione (se disponibile) --}}
                        @if($malfunzionamento->prima_segnalazione)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Prima segnalazione:</span>
                                {{-- 
                                    Laravel: Carbon parse e format per date
                                    PHP: Parsing e formattazione data italiana
                                --}}
                                <strong>{{ \Carbon\Carbon::parse($malfunzionamento->prima_segnalazione)->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        
                        {{-- Ultima segnalazione (se disponibile) --}}
                        @if($malfunzionamento->ultima_segnalazione)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ultima segnalazione:</span>
                                <strong>{{ \Carbon\Carbon::parse($malfunzionamento->ultima_segnalazione)->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                        
                        {{-- 
                            Creatore record (se disponibile)
                            Laravel: Relazione Eloquent creatoBy
                        --}}
                        @if($malfunzionamento->creatoBy)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Creato da:</span>
                                <strong>{{ $malfunzionamento->creatoBy->nome_completo }}</strong>
                            </div>
                        @endif
                        
                        {{-- 
                            Ultimo modificatore (se disponibile)
                            Laravel: Relazione Eloquent modificatoBy
                        --}}
                        @if($malfunzionamento->modificatoBy)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Modificato da:</span>
                                <strong>{{ $malfunzionamento->modificatoBy->nome_completo }}</strong>
                            </div>
                        @endif
                        
                        {{-- 
                            Data ultimo aggiornamento (sempre presente)
                            Laravel: Carbon format per timestamp Eloquent
                        --}}
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Aggiornato:</span>
                            <strong>{{ $malfunzionamento->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 
                CARD: PROBLEMI CORRELATI (OPZIONALE)
                Mostrata solo se esistono malfunzionamenti correlati
                Laravel: isset() e count() per controllo esistenza e contenuto
            --}}
            @if(isset($correlati) && $correlati->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-link-45deg me-2"></i>Problemi Correlati
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- 
                            Iterazione sui malfunzionamenti correlati
                            Laravel: Collection foreach con $loop per controlli
                        --}}
                        @foreach($correlati as $correlato)
                            <div class="d-flex align-items-center mb-2 @if(!$loop->last) border-bottom pb-2 @endif">
                                {{-- Badge gravità correlato --}}
                                <span class="badge 
                                    @switch($correlato->gravita)
                                        @case('critica') bg-danger @break
                                        @case('alta') bg-warning text-dark @break
                                        @case('media') bg-info @break
                                        @default bg-secondary
                                    @endswitch
                                    me-2">
                                    {{ ucfirst($correlato->gravita) }}
                                </span>
                                {{-- Contenuto correlato --}}
                                <div class="flex-grow-1">
                                    {{-- 
                                        Link al malfunzionamento correlato
                                        Laravel: route() con parametri multipli
                                        Str::limit() per troncare titolo lungo
                                    --}}
                                    <a href="{{ route('malfunzionamenti.show', [$correlato->prodotto, $correlato]) }}" 
                                       class="text-decoration-none small">
                                        {{ Str::limit($correlato->titolo, 40) }}
                                    </a>
                                    {{-- Nome prodotto correlato --}}
                                    <div class="text-muted small">
                                        {{ $correlato->prodotto->nome }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</div>
@endsection

{{-- 
    SEZIONE STILI CSS PERSONALIZZATI AVANZATI
    Blade: @push('styles') per CSS specifico di questa vista
    CSS ottimizzato per vista dettaglio malfunzionamento
--}}
@push('styles')
<style>
/* 
    CSS: STILI BASE PER PAGINA DETTAGLIO MALFUNZIONAMENTO
    Fondamenta per layout e componenti specifici
*/

/* Transizioni fluide per tutte le card della pagina */
.card {
    transition: all 0.2s ease-in-out;  /* Transizione smooth per hover effects */
}

/* Dimensioni badge personalizzate */
.badge.fs-6 {
    font-size: 0.875rem !important;    /* Font size custom per badge grandi */
}

/* Miglioramenti tipografici per titolo principale */
h1.h3 {
    line-height: 1.3;                  /* Line height ottimizzato per leggibilità */
}

/* Hover effects per le card con elevazione */
.card:hover {
    transform: translateY(-2px);                         /* Solleva card al hover */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; /* Ombra più pronunciata */
}

/* 
    CSS: STILI MIGLIORATI PER PULSANTE SUCCESS
    Ottimizzazione visibilità e feedback utente
*/

/* Pulsante success con massima visibilità */
.btn-success {
    background-color: #198754 !important;  /* Verde Bootstrap scuro */
    border-color: #146c43 !important;      /* Bordo più scuro */
    color: #ffffff !important;             /* Testo bianco */
    font-weight: 600 !important;           /* Grassetto per evidenziare */
    border-width: 2px !important;          /* Bordo più spesso */
}

/* Stato hover per pulsante success */
.btn-success:hover:not(:disabled) {
    background-color: #157347 !important;               /* Verde più scuro al hover */
    border-color: #146c43 !important;                   /* Bordo invariato */
    color: #ffffff !important;                          /* Testo bianco */
    transform: translateY(-1px);                        /* Leggero sollevamento */
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4) !important; /* Ombra verde */
}

/* Stato focus per accessibilità */
.btn-success:focus:not(:disabled) {
    background-color: #198754 !important;               /* Colore originale */
    border-color: #146c43 !important;                   /* Bordo originale */
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important; /* Focus ring verde */
}

/* Stato disabled per feedback */
.btn-success:disabled {
    background-color: #198754 !important;  /* Mantiene colore */
    border-color: #146c43 !important;      /* Mantiene bordo */
    color: #ffffff !important;             /* Mantiene testo */
    opacity: 0.95 !important;              /* Leggera trasparenza */
    cursor: not-allowed;                   /* Cursore di divieto */
}

/* 
    CSS: ANIMAZIONE PULSO PER PULSANTE SUCCESS
    Attira attenzione su azioni importanti
*/
@keyframes pulse-success {
    0% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);    /* Ombra iniziale */
        transform: scale(1);                            /* Scala normale */
    }
    50% {
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0.2); /* Espansione ombra */
        transform: scale(1.05);                         /* Leggero ingrandimento */
    }
    100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);      /* Ombra finale */
        transform: scale(1);                            /* Ritorno normale */
    }
}

/* Classe per applicare animazione pulse */
.pulse-success {
    animation: pulse-success 1.5s ease-in-out 2;       /* Ripete 2 volte */
}

/* 
    CSS: STILI PER PULSANTE SEGNALAZIONE
    Specializzazione per bottone "Ho Questo Problema"
*/
.segnala-btn {
    transition: all 0.3s ease;         /* Transizione più lunga per effetto drammatico */
    border-width: 2px;                 /* Bordo spesso per visibilità */
    font-weight: 500;                  /* Peso font medio */
}

.segnala-btn:hover:not(:disabled) {
    transform: translateY(-1px);                        /* Sollevamento al hover */
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);     /* Ombra gialla */
}

.segnala-btn:focus:not(:disabled) {
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); /* Focus ring giallo */
}

/* 
    CSS: MIGLIORAMENTI ALERT
    Stili personalizzati per messaggi di sistema
*/

/* Alert success più visibile */
.alert-success {
    background-color: #d1edda !important;  /* Verde chiaro */
    border: 1px solid #badbcc !important;  /* Bordo verde */
    color: #0f5132 !important;             /* Testo verde scuro */
    font-weight: 500;                      /* Font medium weight */
    border-radius: 0.5rem;                 /* Bordi arrotondati */
}

.alert-success .bi {
    color: #198754;                        /* Icone verdi */
}

/* Alert danger migliorato */
.alert-danger {
    background-color: #f8d7da !important;  /* Rosso chiaro */
    border: 1px solid #f1aeb5 !important;  /* Bordo rosso */
    color: #721c24 !important;             /* Testo rosso scuro */
    font-weight: 500;                      /* Font medium weight */
    border-radius: 0.5rem;                 /* Bordi arrotondati */
}

.alert-danger .bi {
    color: #dc3545;                        /* Icone rosse */
}

/* 
    CSS: STILE PER ALERT FLOTTANTI
    Notifiche temporanee per feedback operazioni
*/
.alert-floating {
    position: fixed;                       /* Posizione fissa viewport */
    top: 20px;                            /* Distanza dal top */
    right: 20px;                          /* Distanza da destra */
    z-index: 1055;                        /* Z-index sopra modal Bootstrap */
    min-width: 350px;                     /* Larghezza minima */
    max-width: 500px;                     /* Larghezza massima */
    border: none !important;              /* Rimuove bordi default */
    border-radius: 0.5rem !important;     /* Bordi arrotondati */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; /* Ombra elevazione */
}

/* 
    CSS: CONTRASTO MIGLIORATO PER BADGE
    Ottimizzazione leggibilità elementi interfaccia
*/

/* Badge contatore segnalazioni */
#segnalazioni-counter {
    background-color: #ffffff !important; /* Sfondo bianco */
    color: #495057 !important;            /* Testo grigio scuro */
    border: 2px solid #dee2e6 !important; /* Bordo grigio */
    font-weight: 600;                     /* Grassetto */
    font-size: 0.875rem;                  /* Font size medio */
    padding: 0.5rem 0.75rem;             /* Padding generoso */
    border-radius: 0.375rem;             /* Bordi arrotondati */
}

/* Badge nella card header */
.card-header .badge.bg-light {
    background-color: #ffffff !important; /* Sfondo bianco */
    color: #495057 !important;            /* Testo grigio scuro */
    border: 2px solid #dee2e6 !important; /* Bordo grigio */
    font-weight: 600;                     /* Grassetto */
    font-size: 0.875rem;                  /* Font size medio */
    padding: 0.5rem 0.75rem;             /* Padding generoso */
}

/* 
    CSS RESPONSIVE: MIGLIORAMENTI MOBILE
    Adattamenti per dispositivi mobili e tablet
*/

/* Tablet e mobile - max-width: 768px */
@media (max-width: 768px) {
    /* Pulsanti in colonna su mobile per usabilità */
    .d-flex.gap-2 {
        flex-direction: column;            /* Stack verticale */
    }
    
    .d-flex.gap-2 > * {
        width: 100% !important;            /* Larghezza piena */
        margin-bottom: 0.5rem;            /* Margine bottom tra elementi */
    }
    
    .d-flex.gap-2 > *:last-child {
        margin-bottom: 0;                 /* Rimuove margine ultimo elemento */
    }
    
    /* Pulsante success più grande su mobile per touch */
    .btn-success {
        font-size: 1.1rem;                /* Font più grande */
        padding: 0.75rem 1.25rem;         /* Padding aumentato */
        min-height: 50px;                 /* Altezza minima per touch */
    }
    
    .btn-success .bi {
        font-size: 1.2rem;                /* Icone più grandi */
    }
    
    /* Alert flottanti responsive */
    .alert-floating {
        right: 10px;                      /* Margine ridotto */
        left: 10px;                       /* Margine sinistro */
        min-width: auto;                  /* Larghezza automatica */
        max-width: none;                  /* Nessun limite larghezza */
        width: calc(100% - 20px);         /* Larghezza calcolata */
    }
}

/* Mobile piccoli - max-width: 576px */
@media (max-width: 576px) {
    .card-body {
        padding: 1rem 0.75rem;            /* Padding ridotto per spazio */
    }
    
    .btn {
        font-size: 0.9rem;                /* Font leggermente ridotto */
        padding: 0.5rem 1rem;             /* Padding standard ridotto */
    }
    
    .btn-success {
        font-size: 1rem;                  /* Font ottimizzato mobile */
        padding: 0.75rem 1rem;            /* Padding bilanciato */
        min-height: 48px;                 /* Altezza minima touch ottimale */
    }
}

/* 
    CSS: STILI PER SPINNER DI CARICAMENTO
    Feedback visivo per operazioni asincrone
*/
.spinner-border-sm {
    width: 1rem;                         /* Larghezza spinner piccolo */
    height: 1rem;                        /* Altezza spinner piccolo */
    border-width: 0.125em;               /* Spessore bordo spinner */
}

/* Spinner integrato nel pulsante */
.btn .spinner-border-sm {
    margin-right: 0.5rem;                /* Spazio tra spinner e testo */
}

/* 
    CSS: MIGLIORAMENTI ACCESSIBILITÀ
    Conformità WCAG e usabilità keyboard
*/

/* Focus visibile per elementi interattivi */
.btn:focus,
.btn-close:focus {
    outline: 2px solid #007bff;          /* Outline blu per focus */
    outline-offset: 2px;                 /* Offset outline */
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); /* Shadow focus */
}

/* Classe per screen reader */
.visually-hidden {
    position: absolute !important;        /* Posizionamento assoluto */
    width: 1px !important;               /* Larghezza minima */
    height: 1px !important;              /* Altezza minima */
    padding: 0 !important;               /* Nessun padding */
    margin: -1px !important;             /* Margine negativo */
    overflow: hidden !important;          /* Nasconde overflow */
    clip: rect(0, 0, 0, 0) !important;   /* Clipping per nascondere */
    white-space: nowrap !important;       /* Evita wrapping */
    border: 0 !important;                /* Nessun bordo */
}

/* 
    CSS: STILI PER STATI DISABLED
    Feedback per elementi non interattivi
*/
.btn:disabled {
    cursor: not-allowed;                 /* Cursore divieto */
    opacity: 0.8;                       /* Opacità ridotta */
}

/* 
    CSS: MIGLIORAMENTI PERFORMANCE
    Ottimizzazioni rendering e animazioni
*/

/* Preload per transizioni fluide */
* {
    box-sizing: border-box;              /* Border box per tutti gli elementi */
}

/* 
    CSS: RISPETTO PREFERENZE UTENTE
    Accessibilità per utenti con preferenze motion ridotte
*/
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;      /* Durata minima animazioni */
        animation-iteration-count: 1 !important;    /* Una sola iterazione */
        transition-duration: 0.01ms !important;     /* Transizioni istantanee */
    }
    
    .pulse-success {
        animation: none;                 /* Disabilita animazione pulse */
    }
}

/* 
    CSS: STILI AGGIUNTIVI PER MIGLIORARE ESPERIENZA
    Dettagli UI per componenti specifici
*/

/* Card prodotto nella sidebar */
.card-body img {
    object-fit: contain;                 /* Mantiene proporzioni immagine */
    background-color: #f8f9fa;          /* Sfondo grigio chiaro */
    border: 1px solid #dee2e6;          /* Bordo sottile */
}

/* 
    CSS: BADGE GRAVITÀ CON COLORI MIGLIORATI
    Palette colori accessibile e distintiva
*/
.badge.bg-danger {
    background-color: #dc3545 !important; /* Rosso Bootstrap */
    color: #ffffff !important;            /* Testo bianco */
}

.badge.bg-warning {
    background-color: #ffc107 !important; /* Giallo Bootstrap */
    color: #000000 !important;            /* Testo nero per contrasto */
}

.badge.bg-info {
    background-color: #0dcaf0 !important; /* Azzurro Bootstrap */
    color: #000000 !important;            /* Testo nero per contrasto */
}

.badge.bg-secondary {
    background-color: #6c757d !important; /* Grigio Bootstrap */
    color: #ffffff !important;            /* Testo bianco */
}

/* 
    CSS: MEDIA QUERY STAMPA
    Ottimizzazione per stampa pagina
*/
@media print {
    .btn,
    .alert-floating {
        display: none !important;         /* Nasconde elementi interattivi */
    }
    
    .card {
        box-shadow: none !important;      /* Rimuove ombre per stampa */
        border: 1px solid #dee2e6 !important; /* Bordo semplice */
    }
}
</style>
@endpush

{{-- 
    SEZIONE JAVASCRIPT AVANZATA
    Blade: @push('scripts') per JavaScript specifico della pagina
    Script per gestione interazioni e trasferimento dati PHP→JS
--}}
@push('scripts')
<script>
/*
    JavaScript: Inizializzazione dati globali della pagina
    Pattern singleton per gestione stato applicazione
    Evita conflitti con altri script e garantisce disponibilità dati
*/

// Inizializza oggetto globale se non esiste (pattern safe)
window.PageData = window.PageData || {};

/*
    Trasferimento dati PHP → JavaScript tramite Blade
    @json() garantisce encoding sicuro con escape automatico
    isset() previene errori per variabili non definite dal controller
    
    VANTAGGI:
    - Sincronizzazione automatica backend-frontend
    - Evita chiamate AJAX per dati già disponibili
    - Supporta validazioni client-side informate
    - Facilita operazioni JavaScript complesse
*/

// Dati prodotto corrente per operazioni correlate
@if(isset($prodotto))
window.PageData.prodotto = @json($prodotto);
@endif

// Array prodotti per suggerimenti e correlazioni
@if(isset($prodotti))
window.PageData.prodotti = @json($prodotti);
@endif

// Malfunzionamento corrente (principale della pagina)
@if(isset($malfunzionamento))
window.PageData.malfunzionamento = @json($malfunzionamento);
@endif

// Lista malfunzionamenti per correlazioni e confronti
@if(isset($malfunzionamenti))
window.PageData.malfunzionamenti = @json($malfunzionamenti);
@endif

// Centro di assistenza per geolocalizzazione
@if(isset($centro))
window.PageData.centro = @json($centro);
@endif

// Elenco centri per lookup e mappatura
@if(isset($centri))
window.PageData.centri = @json($centri);
@endif

// Categorie prodotti per filtri dinamici
@if(isset($categorie))
window.PageData.categorie = @json($categorie);
@endif

// Staff members per autorizzazioni e contatti
@if(isset($staffMembers))
window.PageData.staffMembers = @json($staffMembers);
@endif

// Statistiche per analytics e dashboard
@if(isset($stats))
window.PageData.stats = @json($stats);
@endif

// Utente corrente per autorizzazioni client-side
@if(isset($user))
window.PageData.user = @json($user);
@endif

/*
    FUNZIONALITÀ JAVASCRIPT ATTESE PER QUESTA VISTA:
    
    1. segnalaMalfunzionamento(id)
       - Chiamata POST AJAX all'endpoint /api/malfunzionamenti/{id}/segnala
       - Aggiornamento real-time del badge contatore segnalazioni
       - Gestione stati loading con spinner e disabilitazione UI
       - Feedback utente con notifiche toast o alert
       - Validazione autorizzazioni lato client
    
    2. Gestione conferme eliminazione
       - Modal di conferma prima di submit form DELETE
       - Doppia conferma per azioni distruttive
       - Prevenzione eliminazioni accidentali
    
    3. Interazioni UI avanzate
       - Tooltip informativi su badge e icone
       - Zoom immagini prodotto al click
       - Copy-to-clipboard per codici errore
       - Condivisione link soluzione
    
    4. Analytics e tracking
       - Tracking visualizzazioni soluzioni
       - Misura tempo lettura soluzione
       - Eventi per segnalazioni e interazioni
    
    PATTERN DI UTILIZZO:
    
    // Accesso dati prodotto
    if (window.PageData.prodotto) {
        console.log('Prodotto:', window.PageData.prodotto.nome);
        console.log('Categoria:', window.PageData.prodotto.categoria);
    }
    
    // Accesso dati malfunzionamento corrente
    if (window.PageData.malfunzionamento) {
        console.log('Problema:', window.PageData.malfunzionamento.titolo);
        console.log('Gravità:', window.PageData.malfunzionamento.gravita);
        console.log('ID:', window.PageData.malfunzionamento.id);
    }
    
    // Verifica autorizzazioni utente
    if (window.PageData.user && window.PageData.user.can_manage_malfunzionamenti) {
        // Mostra controlli staff
    }
    
    INTEGRAZIONE LARAVEL:
    - window.apiMalfunzionamentiUrl contiene base URL API
    - Token CSRF disponibile in meta tag per chiamate AJAX
    - Route names disponibili per generazione URL client-side
    - Autorizzazioni sincronizzate tra backend e frontend
*/

/*
    ESEMPIO IMPLEMENTAZIONE SEGNALAZIONE:
    
    function segnalaMalfunzionamento(malfunzionamentoId) {
        // Validazione prerequisiti
        if (!window.PageData.user) {
            showAlert('Effettua il login per segnalare problemi', 'warning');
            return;
        }
        
        if (!window.PageData.user.can_view_malfunzionamenti) {
            showAlert('Non autorizzato a segnalare malfunzionamenti', 'error');
            return;
        }
        
        // Riferimenti DOM
        const btn = document.querySelector('.segnala-btn');
        const counter = document.getElementById('segnalazioni-counter');
        
        // Stato loading
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Segnalando...';
        
        // Chiamata AJAX
        fetch(`${window.apiMalfunzionamentiUrl}/${malfunzionamentoId}/segnala`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                timestamp: new Date().toISOString(),
                source: 'dettaglio_malfunzionamento',
                user_agent: navigator.userAgent
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Aggiornamento UI successo
            if (counter && data.nuovo_conteggio !== undefined) {
                counter.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${data.nuovo_conteggio} segnalazioni`;
                counter.classList.add('pulse-success');
                
                // Rimuove animazione dopo completamento
                setTimeout(() => {
                    counter.classList.remove('pulse-success');
                }, 3000);
            }
            
            // Aggiorna dati locali
            if (window.PageData.malfunzionamento) {
                window.PageData.malfunzionamento.numero_segnalazioni = data.nuovo_conteggio;
            }
            
            // Feedback positivo
            showAlert('Segnalazione registrata con successo!', 'success');
            
            // Analytics tracking
            if (window.gtag) {
                gtag('event', 'segnala_malfunzionamento', {
                    'event_category': 'user_interaction',
                    'event_label': 'dettaglio_vista',
                    'malfunzionamento_id': malfunzionamentoId,
                    'gravita': window.PageData.malfunzionamento?.gravita
                });
            }
        })
        .catch(error => {
            console.error('Errore segnalazione:', error);
            showAlert('Errore durante la segnalazione. Riprova più tardi.', 'error');
        })
        .finally(() => {
            // Ripristino stato bottone
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Ho Questo Problema';
        });
    }
    
    UTILITÀ SUPPORTO:
    
    function showAlert(message, type = 'info') {
        // Implementazione notifiche toast
        // Gestione alert flottanti
        // Auto-dismiss dopo timeout
    }
*/

</script>
@endpush